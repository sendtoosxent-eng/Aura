<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AppointmentBooking;
use App\Models\Doctor;
use App\Models\ProviderUnavailableDate;
use App\Models\User;
use App\Services\ProviderAccountResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AppointmentController extends Controller
{
    public function __construct(private ProviderAccountResolver $providerAccounts) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = AppointmentBooking::query()->with(['user:id,name,phone', 'healthcareProvider:id,name', 'doctor:id,name,specialty']);

        if ($this->isProvider($user)) {
            $scope = $this->providerScope($user);
            $query->where(function ($builder) use ($scope) {
                if ($scope['provider_id']) {
                    $builder->orWhere('healthcare_provider_id', $scope['provider_id']);
                }
                if ($scope['doctor_id']) {
                    $builder->orWhere('doctor_id', $scope['doctor_id']);
                }
                if (! $scope['provider_id'] && ! $scope['doctor_id']) {
                    $builder->whereRaw('1 = 0');
                }
            });
        } else {
            $query->where('user_id', $user->id);
        }

        $appointments = $query->orderByDesc('appointment_date')->orderByDesc('appointment_time')->paginate(25);
        $appointments->through(fn (AppointmentBooking $booking) => $this->payload($booking, $this->isProvider($user)));

        return response()->json($appointments);
    }

    public function store(Request $request): JsonResponse
    {
        abort_if($this->isProvider($request->user()), 403, 'Only patient accounts can request appointments.');

        $data = $request->validate([
            'healthcare_provider_id' => ['nullable', 'integer', 'exists:healthcare_providers,id'],
            'doctor_id' => ['nullable', 'integer', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'appointment_type' => ['required', Rule::in(['in_person'])],
            'service' => ['required', 'string', 'max:160'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        if (empty($data['healthcare_provider_id']) && empty($data['doctor_id'])) {
            throw ValidationException::withMessages(['booking' => 'Please choose a healthcare provider or doctor.']);
        }

        if (! empty($data['doctor_id']) && ! empty($data['healthcare_provider_id'])) {
            $belongs = Doctor::query()->whereKey($data['doctor_id'])
                ->where('healthcare_provider_id', $data['healthcare_provider_id'])->exists();
            if (! $belongs) {
                throw ValidationException::withMessages(['doctor_id' => 'This doctor does not work at the selected facility.']);
            }
        }

        $providerId = $data['healthcare_provider_id']
            ?? Doctor::query()->whereKey($data['doctor_id'])->value('healthcare_provider_id');
        $isUnavailable = ProviderUnavailableDate::query()
            ->where('healthcare_provider_id', $providerId)
            ->whereDate('unavailable_date', $data['appointment_date'])
            ->exists();
        if ($isUnavailable) {
            throw ValidationException::withMessages([
                'appointment_date' => 'This provider is unavailable on the selected date. Please choose another day.',
            ]);
        }

        $booking = AppointmentBooking::query()->create($data + [
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ])->load(['healthcareProvider:id,name', 'doctor:id,name,specialty']);
        $this->notifyProviderOfRequest($booking, $request->user());

        return response()->json([
            'message' => 'Your visit request has been submitted.',
            'data' => $this->payload($booking, false),
        ], 201);
    }

    public function update(Request $request, AppointmentBooking $appointment): JsonResponse
    {
        abort_unless($this->isProvider($request->user()) && $this->providerCanManage($request->user(), $appointment), 403);
        $data = $request->validate(['status' => ['required', Rule::in(['confirmed', 'cancelled', 'completed'])]]);
        $appointment->update(['status' => $data['status']]);
        $appointment->user->userNotifications()->create([
            'title' => 'Appointment '.match ($data['status']) {
                'confirmed' => 'confirmed',
                'completed' => 'completed',
                default => 'declined',
            },
            'body' => sprintf(
                '%s updated your %s appointment request.',
                $appointment->healthcareProvider?->name ?? $appointment->doctor?->name ?? 'Your provider',
                $appointment->appointment_date->format('j M Y'),
            ),
            'category' => 'updates',
            'icon' => 'calendar.badge.clock',
        ]);

        return response()->json([
            'message' => 'Appointment status updated.',
            'data' => $this->payload($appointment->fresh(['user:id,name,phone', 'healthcareProvider:id,name', 'doctor:id,name,specialty']), true),
        ]);
    }

    public function cancel(Request $request, AppointmentBooking $appointment): JsonResponse
    {
        abort_unless($appointment->user_id === $request->user()->id, 403);
        abort_unless(in_array($appointment->status, ['pending', 'confirmed'], true), 422, 'This appointment can no longer be cancelled.');
        $appointment->update(['status' => 'cancelled']);
        $providerUser = $this->providerUserFor($appointment);
        $providerUser?->userNotifications()->create([
            'title' => 'Appointment cancelled',
            'body' => $request->user()->name.' cancelled their appointment request.',
            'category' => 'updates',
            'icon' => 'calendar.badge.exclamationmark',
        ]);

        return response()->json(['message' => 'Appointment cancelled.']);
    }

    private function isProvider(User $user): bool
    {
        return in_array($user->account_type, ['facility', 'professional'], true);
    }

    /** @return array{provider_id: ?int, doctor_id: ?int} */
    private function providerScope(User $user): array
    {
        $providerId = $user->account_type === 'professional'
            ? null
            : $this->providerAccounts->healthcareProvider($user)?->id;
        $doctorId = $user->account_type === 'professional'
            ? $this->providerAccounts->doctor($user)?->id
            : null;

        return ['provider_id' => $providerId, 'doctor_id' => $doctorId];
    }

    private function providerCanManage(User $user, AppointmentBooking $appointment): bool
    {
        $scope = $this->providerScope($user);

        return ($scope['provider_id'] && $appointment->healthcare_provider_id === $scope['provider_id'])
            || ($scope['doctor_id'] && $appointment->doctor_id === $scope['doctor_id']);
    }

    private function notifyProviderOfRequest(AppointmentBooking $booking, User $patient): void
    {
        $providerUser = $this->providerUserFor($booking);
        $providerUser?->userNotifications()->create([
            'title' => 'New appointment request',
            'body' => sprintf(
                '%s requested %s on %s at %s.',
                $patient->name,
                $booking->service,
                $booking->appointment_date->format('j M Y'),
                substr((string) $booking->appointment_time, 0, 5),
            ),
            'category' => 'alerts',
            'icon' => 'calendar.badge.plus',
        ]);
    }

    private function providerUserFor(AppointmentBooking $booking): ?User
    {
        $booking->loadMissing(['healthcareProvider:id,email,phone', 'doctor:id,email,phone']);
        $emails = array_filter([$booking->doctor?->email, $booking->healthcareProvider?->email]);
        $phones = array_filter([$booking->doctor?->phone, $booking->healthcareProvider?->phone]);
        if (! $emails && ! $phones) {
            return null;
        }

        return User::query()
            ->whereIn('account_type', ['facility', 'professional'])
            ->where(function ($query) use ($emails, $phones): void {
                if ($emails) {
                    $query->orWhereIn('email', $emails);
                }
                if ($phones) {
                    $query->orWhereIn('phone', $phones);
                }
            })
            ->first();
    }

    /** @return array<string, mixed> */
    private function payload(AppointmentBooking $booking, bool $includePatient): array
    {
        return [
            'id' => $booking->id,
            'appointment_date' => $booking->appointment_date->toDateString(),
            'appointment_time' => substr((string) $booking->appointment_time, 0, 5),
            'appointment_type' => $booking->appointment_type,
            'service' => $booking->service,
            'reason' => $booking->reason,
            'status' => $booking->status,
            'facility' => $booking->healthcareProvider ? [
                'id' => $booking->healthcareProvider->id,
                'name' => $booking->healthcareProvider->name,
            ] : null,
            'doctor' => $booking->doctor ? [
                'id' => $booking->doctor->id,
                'name' => $booking->doctor->name,
                'specialty' => $booking->doctor->specialty,
            ] : null,
            'patient' => $includePatient && $booking->user ? [
                'id' => $booking->user->id,
                'name' => $booking->user->name,
                'phone' => $booking->user->phone,
            ] : null,
        ];
    }
}
