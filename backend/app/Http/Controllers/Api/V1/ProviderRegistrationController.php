<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\HealthcareProvider;
use App\Models\RegistrationApplication;
use App\Models\User;
use App\Services\PhoneVerificationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Throwable;

class ProviderRegistrationController extends Controller
{
    public function facilities(Request $request): JsonResponse
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:150']]);
        $query = trim($data['q'] ?? '');

        $facilities = HealthcareProvider::query()
            ->where('is_verified', true)
            ->when($query !== '', fn ($builder) => $builder->where(function ($nested) use ($query) {
                $nested->where('name', 'like', "%{$query}%")
                    ->orWhere('district', 'like', "%{$query}%")
                    ->orWhere('town', 'like', "%{$query}%");
            }))
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'provider_type', 'district', 'town', 'address', 'image']);

        return response()->json(['data' => $facilities]);
    }

    public function createIdentity(Request $request): JsonResponse
    {
        $data = $request->validate([
            'application_type' => ['required', Rule::in(['facility', 'professional'])],
            'healthcare_provider_id' => [
                Rule::requiredIf($request->input('application_type') === 'professional'),
                'nullable',
                Rule::exists('healthcare_providers', 'id')->where('is_verified', true),
            ],
            'first_name' => ['required', 'string', 'max:80'],
            'middle_name' => ['nullable', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:'.now()->subYears(18)->toDateString()],
            'sex' => ['required', Rule::in(['male', 'female'])],
            'nationality' => ['required', 'string', 'max:80'],
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'nin' => ['required', 'string', 'size:14', 'regex:/^C[MF][A-Z0-9]{12}$/', 'unique:users,nin'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'device_name' => ['required', 'string', 'max:100'],
        ]);

        $profilePhotoPath = $request->file('profile_photo')->store('registration/profile-photos', 'local');
        $name = collect([$data['first_name'], $data['middle_name'] ?? null, $data['last_name']])
            ->filter()->implode(' ');

        try {
            [$user, $application] = DB::transaction(function () use ($data, $name, $profilePhotoPath) {
                $user = User::query()->create([
                    'name' => $name,
                    'email' => strtolower($data['email']),
                    'phone' => $data['phone'],
                    'nin' => strtoupper($data['nin']),
                    'account_type' => $data['application_type'],
                    'registration_status' => 'draft',
                    'password' => Hash::make($data['password']),
                ]);

                $application = RegistrationApplication::query()->create([
                    'user_id' => $user->id,
                    'application_type' => $data['application_type'],
                    'healthcare_provider_id' => $data['healthcare_provider_id'] ?? null,
                    'first_name' => $data['first_name'],
                    'middle_name' => $data['middle_name'] ?? null,
                    'last_name' => $data['last_name'],
                    'date_of_birth' => $data['date_of_birth'],
                    'sex' => $data['sex'],
                    'nationality' => $data['nationality'],
                    'profile_photo' => $profilePhotoPath,
                    'phone' => $data['phone'],
                    'email' => strtolower($data['email']),
                    'nin' => strtoupper($data['nin']),
                    'status' => 'draft',
                ]);

                return [$user, $application];
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($profilePhotoPath);
            throw $exception;
        }

        $emailSent = true;
        try {
            event(new Registered($user));
        } catch (Throwable $exception) {
            $emailSent = false;
            report($exception);
        }

        return response()->json([
            'message' => 'Your provider account draft has been created.',
            'access_token' => $user->createToken($data['device_name'], ['mobile'])->plainTextToken,
            'token_type' => 'Bearer',
            'application_id' => $application->id,
            'next_step' => 'email_verification',
            'email_sent' => $emailSent,
            'data' => $this->applicationPayload($application),
        ], 201);
    }

    public function status(Request $request): JsonResponse
    {
        $application = $request->user()->registrationApplications()
            ->with('documents:id,registration_application_id,document_type,original_name,mime_type,size')
            ->latest('id')->firstOrFail();

        return response()->json([
            'data' => $this->applicationPayload($application) + [
                'documents' => $application->documents,
                'email_verified' => $request->user()->hasVerifiedEmail(),
                'phone_verified' => $request->user()->phone_verified_at !== null,
            ],
        ]);
    }

    public function resendEmail(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Your email address is already verified.', 'verified' => true]);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'A new verification email has been sent.', 'verified' => false]);
    }

    public function sendPhoneOtp(Request $request, PhoneVerificationService $service): JsonResponse
    {
        $user = $request->user();
        $debugCode = $service->sendOtp($user->phone, $request->ip());

        return response()->json(array_filter([
            'message' => 'A verification code has been sent to your phone.',
            'debug_code' => $debugCode,
        ], fn (mixed $value): bool => $value !== null));
    }

    public function verifyPhoneOtp(Request $request, PhoneVerificationService $service): JsonResponse
    {
        $data = $request->validate(['otp' => ['required', 'digits:6']]);
        $user = $request->user();
        $service->verify($user->phone, $data['otp']);
        $user->forceFill(['phone_verified_at' => now()])->save();
        $user->registrationApplications()->where('status', 'draft')->update(['phone_verified' => true]);

        return response()->json(['message' => 'Your phone number has been verified.', 'verified' => true]);
    }

    /** @return array<string, mixed> */
    private function applicationPayload(RegistrationApplication $application): array
    {
        return [
            'id' => $application->id,
            'application_type' => $application->application_type,
            'status' => $application->status,
            'reference' => $application->reference,
            'healthcare_provider_id' => $application->healthcare_provider_id,
            'person_name' => collect([$application->first_name, $application->middle_name, $application->last_name])
                ->filter()->implode(' '),
            'facility_name' => $application->facility_name,
            'submitted_at' => $application->submitted_at?->toIso8601String(),
        ];
    }
}
