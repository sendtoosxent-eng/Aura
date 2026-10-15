<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\HealthcareProvider;
use App\Models\MessageThread;
use App\Models\User;
use App\Services\MobileDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MessageController extends Controller
{
    public function start(Request $request, MobileDataService $mobileData): JsonResponse
    {
        abort_if(in_array($request->user()->account_type, ['facility', 'professional'], true), 403, 'Only patients can start a provider conversation.');
        $data = $request->validate([
            'healthcare_provider_id' => ['nullable', 'integer', 'exists:healthcare_providers,id'],
            'doctor_id' => ['nullable', 'integer', 'exists:doctors,id'],
            'text' => ['required', 'string', 'max:5000'],
        ]);
        if (empty($data['healthcare_provider_id']) && empty($data['doctor_id'])) {
            throw ValidationException::withMessages(['recipient' => 'Please choose a healthcare provider or doctor.']);
        }

        $doctor = isset($data['doctor_id']) ? Doctor::query()->find($data['doctor_id']) : null;
        $provider = $doctor?->healthcareProvider
            ?? (isset($data['healthcare_provider_id']) ? HealthcareProvider::query()->find($data['healthcare_provider_id']) : null);
        $providerUser = $this->providerUser($doctor?->email, $doctor?->phone)
            ?? $this->providerUser($provider?->email, $provider?->phone);
        if (! $providerUser) {
            throw ValidationException::withMessages(['recipient' => 'This provider does not have a messaging account yet.']);
        }

        $thread = DB::transaction(function () use ($request, $providerUser, $provider, $doctor, $data): MessageThread {
            $thread = MessageThread::query()->firstOrCreate(
                ['patient_user_id' => $request->user()->id, 'provider_user_id' => $providerUser->id],
                [
                    'healthcare_provider_id' => $provider?->id,
                    'doctor_id' => $doctor?->id,
                    'subtitle' => $doctor?->specialty ?? 'Patient support desk',
                    'last_activity_at' => now(),
                ],
            );
            $thread->messages()->create([
                'sender_user_id' => $request->user()->id,
                'sender_type' => 'user',
                'body' => $data['text'],
                'sent_at' => now(),
            ]);
            $thread->update(['last_activity_at' => now()]);
            $this->notifyRecipient($providerUser, $request->user()->name, $data['text']);

            return $thread;
        });

        return response()->json([
            'data' => $mobileData->thread($this->loadThread($thread), $request->user()),
        ], 201);
    }

    public function store(Request $request, MessageThread $thread, MobileDataService $mobileData): JsonResponse
    {
        abort_unless($mobileData->canAccessThread($request->user(), $thread) && ! $thread->is_automated, 404);
        $data = $request->validate(['text' => ['required', 'string', 'max:5000']]);
        $senderType = $thread->patient_user_id === $request->user()->id ? 'user' : 'provider';

        $message = $thread->messages()->create([
            'sender_user_id' => $request->user()->id,
            'sender_type' => $senderType,
            'body' => $data['text'],
            'sent_at' => now(),
        ]);
        $thread->update(['last_activity_at' => now()]);
        $recipient = $thread->patient_user_id === $request->user()->id ? $thread->providerUser : $thread->patient;
        if ($recipient) {
            $this->notifyRecipient($recipient, $request->user()->name, $data['text']);
        }

        return response()->json(['data' => $mobileData->message($message)], 201);
    }

    public function markRead(Request $request, MessageThread $thread, MobileDataService $mobileData): JsonResponse
    {
        abort_unless($mobileData->canAccessThread($request->user(), $thread), 404);

        $thread->messages()
            ->where(function ($builder) use ($request): void {
                $builder->whereNull('sender_user_id')
                    ->orWhere('sender_user_id', '!=', $request->user()->id);
            })
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'Conversation marked as read.']);
    }

    private function providerUser(?string $email, ?string $phone): ?User
    {
        if (! $email && ! $phone) {
            return null;
        }

        return User::query()
            ->whereIn('account_type', ['facility', 'professional'])
            ->where(function ($builder) use ($email, $phone): void {
                if ($email) {
                    $builder->orWhere('email', $email);
                }
                if ($phone) {
                    $builder->orWhere('phone', $phone);
                }
            })
            ->first();
    }

    private function notifyRecipient(User $recipient, string $senderName, string $text): void
    {
        $recipient->userNotifications()->create([
            'title' => "New message from {$senderName}",
            'body' => str($text)->limit(160)->toString(),
            'category' => 'updates',
            'icon' => 'message.fill',
        ]);
    }

    private function loadThread(MessageThread $thread): MessageThread
    {
        return $thread->load([
            'patient:id,name', 'providerUser:id,name,registration_status', 'healthcareProvider:id,name,is_verified',
            'doctor:id,name', 'messages' => fn ($builder) => $builder->oldest('sent_at'),
        ]);
    }
}
