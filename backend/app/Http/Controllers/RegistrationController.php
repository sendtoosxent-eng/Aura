<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationApplicationRequest;
use App\Http\Requests\UpdateRegistrationIdentityRequest;
use App\Models\HealthcareProvider;
use App\Models\RegistrationApplication;
use App\Models\RegistrationDocument;
use App\Models\User;
use App\Services\PhoneVerificationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Throwable;

class RegistrationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | REGISTRATION PAGE
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return view(
            'registration.onboarding'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | VERIFIED FACILITY SEARCH
    |--------------------------------------------------------------------------
    */

    public function facilities(
        Request $request
    ): JsonResponse {

        $query = trim(
            (string) $request->query(
                'q',
                ''
            )
        );

        $facilities = HealthcareProvider::query()

            ->where(
                'is_verified',
                true
            )

            ->when(
                $query !== '',
                function ($builder) use ($query) {

                    $builder->where(
                        function ($q) use ($query) {

                            $q
                                ->where(
                                    'name',
                                    'like',
                                    "%{$query}%"
                                )

                                ->orWhere(
                                    'district',
                                    'like',
                                    "%{$query}%"
                                )

                                ->orWhere(
                                    'town',
                                    'like',
                                    "%{$query}%"
                                );
                        }
                    );
                }
            )

            ->orderBy('name')

            ->limit(10)

            ->get([
                'id',
                'name',
                'provider_type',
                'district',
                'town',
                'image',
            ]);

        return response()->json([
            'facilities' => $facilities,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE ACCOUNT / IDENTITY
    |--------------------------------------------------------------------------
    */

    public function createIdentity(
        Request $request
    ): JsonResponse {

        $data = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | REGISTRATION TYPE
            |--------------------------------------------------------------------------
            */

            'application_type' => [
                'required',
                'in:facility,professional',
            ],

            'healthcare_provider_id' => [
                'nullable',
                'exists:healthcare_providers,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | PERSONAL INFORMATION
            |--------------------------------------------------------------------------
            */

            'first_name' => [
                'required',
                'string',
                'max:80',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:80',
            ],

            'last_name' => [
                'required',
                'string',
                'max:80',
            ],

            'date_of_birth' => [
                'required',
                'date',
                'before_or_equal:'.now()->subYears(18)->toDateString(),
            ],

            'sex' => [
                'required',
                'in:male,female',
            ],

            'nationality' => [
                'required',
                'string',
                'max:80',
            ],

            /*
            |--------------------------------------------------------------------------
            | PROFILE PHOTO
            |--------------------------------------------------------------------------
            */

            'profile_photo' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            /*
            |--------------------------------------------------------------------------
            | CONTACT DETAILS
            |--------------------------------------------------------------------------
            */

            'phone' => [
                'required',
                'string',
                'max:30',
                'unique:users,phone',
            ],

            'email' => [
                'required',
                'email',
                'max:150',
                'unique:users,email',
            ],

            /*
            |--------------------------------------------------------------------------
            | NATIONAL ID
            |--------------------------------------------------------------------------
            */

            'nin' => [
                'required',
                'string',
                'size:14',
                'regex:/^C[MF][A-Z0-9]{12}$/',
                'unique:users,nin',
            ],

            /*
            |--------------------------------------------------------------------------
            | PASSWORD
            |--------------------------------------------------------------------------
            */

            'password' => [
                'required',
                'confirmed',

                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],

        ]);

        /*
        |--------------------------------------------------------------------------
        | PROFESSIONAL MUST SELECT VERIFIED FACILITY
        |--------------------------------------------------------------------------
        */

        if (
            $data['application_type']
            ===
            'professional'
        ) {

            if (
                empty(
                    $data['healthcare_provider_id']
                )
            ) {

                throw ValidationException::withMessages([
                    'healthcare_provider_id' => 'Please select the verified AfyaLink facility where you work.',
                ]);
            }

            $facility = HealthcareProvider::query()

                ->whereKey(
                    $data['healthcare_provider_id']
                )

                ->where(
                    'is_verified',
                    true
                )

                ->first();

            if (! $facility) {

                throw ValidationException::withMessages([
                    'healthcare_provider_id' => 'This healthcare facility has not yet been verified by AfyaLink.',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | STORE PROFILE PHOTO
        |--------------------------------------------------------------------------
        |
        | Keep application photos private while registration is still under
        | review. Later, after AfyaLink approval, we can copy/publish the
        | approved photo to the public provider/professional profile.
        |
        */

        $profilePhotoPath = null;

        if (
            $request->hasFile(
                'profile_photo'
            )
        ) {

            $profilePhotoPath =
                $request
                    ->file('profile_photo')
                    ->store(
                        'registration/profile-photos',
                        'local'
                    );
        }

        /*
        |--------------------------------------------------------------------------
        | BUILD NAME
        |--------------------------------------------------------------------------
        */

        $name = collect([

            $data['first_name'],

            $data['middle_name']
                ?? null,

            $data['last_name'],

        ])
            ->filter()
            ->implode(' ');

        /*
        |--------------------------------------------------------------------------
        | CREATE USER
        |--------------------------------------------------------------------------
        */

        try {
            [$user, $application] = DB::transaction(function () use ($data, $name, $profilePhotoPath): array {
                $user = User::create([

                    'name' => $name,

                    'email' => strtolower(
                        $data['email']
                    ),

                    'phone' => $data['phone'],

                    'nin' => strtoupper(
                        $data['nin']
                    ),

                    'account_type' => $data['application_type']
                        ===
                        'facility'
                            ? 'facility'
                            : 'professional',

                    'registration_status' => 'draft',

                    'password' => Hash::make(
                        $data['password']
                    ),

                ]);

                /*
                |--------------------------------------------------------------------------
                | CREATE REGISTRATION APPLICATION
                |--------------------------------------------------------------------------
                */

                $application = RegistrationApplication::create([

                    'user_id' => $user->id,

                    'application_type' => $data['application_type'],

                    'healthcare_provider_id' => $data['healthcare_provider_id']
                        ?? null,

                    'first_name' => $data['first_name'],

                    'middle_name' => $data['middle_name']
                        ?? null,

                    'last_name' => $data['last_name'],

                    'date_of_birth' => $data['date_of_birth'],

                    'sex' => $data['sex'],

                    'nationality' => $data['nationality'],

                    /*
                |--------------------------------------------------------------------------
                | PROFILE PHOTO
                |--------------------------------------------------------------------------
                */

                    'profile_photo' => $profilePhotoPath,

                    /*
                |--------------------------------------------------------------------------
                | CONTACT
                |--------------------------------------------------------------------------
                */

                    'phone' => $data['phone'],

                    'email' => strtolower(
                        $data['email']
                    ),

                    /*
                |--------------------------------------------------------------------------
                | IDENTITY
                |--------------------------------------------------------------------------
                */

                    'nin' => strtoupper(
                        $data['nin']
                    ),

                    'status' => 'draft',

                ]);

                return [$user, $application];
            });
        } catch (Throwable $exception) {
            if ($profilePhotoPath) {
                Storage::disk('local')->delete($profilePhotoPath);
            }

            throw $exception;
        }

        /*
        |--------------------------------------------------------------------------
        | LOGIN USER
        |--------------------------------------------------------------------------
        */

        Auth::login(
            $user
        );

        $request
            ->session()
            ->regenerate();

        /*
        |--------------------------------------------------------------------------
        | SEND EMAIL VERIFICATION
        |--------------------------------------------------------------------------
        */

        $emailSent = true;

        try {
            event(new Registered($user));
        } catch (Throwable $exception) {
            $emailSent = false;
            report($exception);
        }

        return response()->json([

            'message' => $emailSent
                    ? 'Your account has been created. Check your email for the verification link.'
                    : 'Your account has been created, but the verification email could not be sent. Use Resend email to try again.',

            'application_id' => $application->id,

            'next_step' => 'email',

            'email_sent' => $emailSent,

        ]);
    }

    public function updateIdentity(UpdateRegistrationIdentityRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $application = RegistrationApplication::query()
            ->whereBelongsTo($user)
            ->where('status', 'draft')
            ->latest('id')
            ->firstOrFail();

        if ($application->application_type !== $data['application_type']) {
            throw ValidationException::withMessages([
                'application_type' => 'This registration type does not match your draft application.',
            ]);
        }

        $newProfilePhotoPath = $request->file('profile_photo')?->store('registration/profile-photos', 'local');
        $oldProfilePhotoPath = $application->profile_photo;
        $email = strtolower($data['email']);
        $emailChanged = $email !== strtolower($user->email);
        $name = collect([$data['first_name'], $data['middle_name'] ?? null, $data['last_name']])
            ->filter()
            ->implode(' ');

        try {
            DB::transaction(function () use ($application, $data, $email, $emailChanged, $name, $newProfilePhotoPath, $user): void {
                $user->forceFill([
                    'name' => $name,
                    'email' => $email,
                    'email_verified_at' => $emailChanged ? null : $user->email_verified_at,
                    'phone' => $data['phone'],
                    'nin' => strtoupper($data['nin']),
                ])->save();

                $application->update([
                    'healthcare_provider_id' => $data['healthcare_provider_id'] ?? null,
                    'first_name' => $data['first_name'],
                    'middle_name' => $data['middle_name'] ?? null,
                    'last_name' => $data['last_name'],
                    'date_of_birth' => $data['date_of_birth'],
                    'sex' => $data['sex'],
                    'nationality' => $data['nationality'],
                    'profile_photo' => $newProfilePhotoPath ?? $application->profile_photo,
                    'phone' => $data['phone'],
                    'email' => $email,
                    'nin' => strtoupper($data['nin']),
                    'email_verified' => $emailChanged ? false : $application->email_verified,
                ]);
            });
        } catch (Throwable $exception) {
            if ($newProfilePhotoPath) {
                Storage::disk('local')->delete($newProfilePhotoPath);
            }

            throw $exception;
        }

        if ($newProfilePhotoPath && $oldProfilePhotoPath) {
            Storage::disk('local')->delete($oldProfilePhotoPath);
        }

        $emailSent = true;
        if ($emailChanged) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (Throwable $exception) {
                $emailSent = false;
                report($exception);
            }
        }

        return response()->json([
            'message' => $emailChanged
                ? ($emailSent ? 'Your changes were saved. Check your new email address for the verification link.' : 'Your changes were saved, but the verification email could not be sent. Use Resend email to try again.')
                : 'Your changes were saved.',
            'next_step' => $emailChanged || ! $user->hasVerifiedEmail() ? 'email' : 'regulatory',
            'email_sent' => $emailSent,
        ]);
    }

    public function submitApplication(
        StoreRegistrationApplicationRequest $request
    ): JsonResponse {
        $user = $request->user();
        $data = $request->validated();

        if ($user->account_type !== $data['application_type']) {
            throw ValidationException::withMessages([
                'application_type' => 'This registration type does not match your account.',
            ]);
        }

        $application = RegistrationApplication::query()
            ->where('user_id', $user->id)
            ->where('status', 'draft')
            ->latest('id')
            ->first();

        if (! $application) {
            throw ValidationException::withMessages([
                'application' => 'Your registration draft could not be found. Go back to Contact details, save your details, then return and submit again.',
            ]);
        }

        if ($application->application_type !== $data['application_type']) {
            throw ValidationException::withMessages([
                'application_type' => 'This registration type does not match your draft application.',
            ]);
        }

        if ($data['application_type'] === 'professional') {
            $workplaceExists = HealthcareProvider::query()
                ->whereKey($data['healthcare_provider_id'])
                ->where('is_verified', true)
                ->exists();

            if (! $workplaceExists) {
                throw ValidationException::withMessages([
                    'healthcare_provider_id' => 'Please select a verified AfyaLink facility.',
                ]);
            }
        }

        $storedPaths = [];

        try {
            $reference = DB::transaction(function () use ($request, $data, $application, $user, &$storedPaths): string {
                $regulatory = $data['regulatory'];

                $application->update($data['application_type'] === 'professional'
                    ? $this->professionalApplicationData($regulatory, $data)
                    : $this->facilityApplicationData($regulatory));

                foreach ($request->file('documents', []) as $documentType => $file) {
                    $path = $file->store("registration/applications/{$application->id}", 'local');
                    $storedPaths[] = $path;

                    RegistrationDocument::create([
                        'registration_application_id' => $application->id,
                        'document_type' => $documentType,
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                        'size' => $file->getSize(),
                    ]);
                }

                $reference = sprintf(
                    'AFY-%s-%s-%06d',
                    $data['application_type'] === 'facility' ? 'FAC' : 'PRO',
                    now()->format('Y'),
                    $application->id,
                );

                $application->update([
                    'status' => 'submitted',
                    'email_verified' => true,
                    'reference' => $reference,
                    'submitted_at' => now(),
                ]);

                $user->update([
                    'registration_status' => 'submitted',
                    'registration_number' => $reference,
                ]);

                return $reference;
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($storedPaths);

            throw $exception;
        }

        return response()->json([
            'message' => 'Your application has been submitted for review.',
            'reference' => $reference,
            'redirect' => route('dashboard'),
        ]);
    }

    /** @param array<string, mixed> $regulatory @param array<string, mixed> $data */
    private function professionalApplicationData(array $regulatory, array $data): array
    {
        return [
            'healthcare_provider_id' => $data['healthcare_provider_id'],
            'profession' => $regulatory['cadre'],
            'cadre' => $regulatory['cadre'],
            'specialty' => $regulatory['specialty'] ?? null,
            'regulatory_body' => $regulatory['regulatory_body'],
            'registration_number' => $regulatory['registration_number'],
            'practising_license_number' => $regulatory['licence_number'],
            'license_expiry_date' => $regulatory['licence_expiry'],
            'license_status' => 'pending_verification',
            'years_experience' => $regulatory['years_experience'] ?? null,
        ];
    }

    /** @param array<string, mixed> $regulatory */
    private function facilityApplicationData(array $regulatory): array
    {
        $services = $regulatory['services'] ?? [];

        if (! empty($regulatory['services_other'])) {
            $services[] = $regulatory['services_other'];
        }

        return [
            'facility_name' => $regulatory['name'],
            'facility_type' => $regulatory['provider_type'],
            'ownership' => $regulatory['ownership'],
            'operating_license_number' => $regulatory['licence_number'],
            'operating_license_expiry' => $regulatory['licence_expiry'],
            'district' => $regulatory['district'],
            'town' => $regulatory['town'],
            'address' => $regulatory['address'],
            'latitude' => $regulatory['latitude'] ?? null,
            'longitude' => $regulatory['longitude'] ?? null,
            'phone' => $regulatory['phone'],
            'email' => $regulatory['email'] ?? null,
            'verification_notes' => json_encode([
                'in_charge' => $regulatory['in_charge'] ?? null,
                'open_24_hours' => (bool) ($regulatory['open_24_hours'] ?? false),
                'emergency_available' => (bool) ($regulatory['emergency_available'] ?? false),
                'services' => $services,
            ], JSON_THROW_ON_ERROR),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SEND PHONE OTP
    |--------------------------------------------------------------------------
    */

    public function sendPhoneOtp(
        Request $request,
        PhoneVerificationService $service
    ): JsonResponse {

        $user =
            $request->user();

        /*
        |--------------------------------------------------------------------------
        | GET PHONE SAFELY
        |--------------------------------------------------------------------------
        |
        | First try users.phone.
        | If it is empty, fall back to the registration application.
        |
        */

        $phone =
            $this->resolveRegistrationPhone(
                $user
            );

        if (! $phone) {

            return response()->json([

                'message' => 'No phone number was found for your registration. Please return to the identity step and enter your phone number.',

            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | KEEP USERS TABLE IN SYNC
        |--------------------------------------------------------------------------
        */

        if (! $user->phone) {

            $user->update([
                'phone' => $phone,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | SEND OTP
        |--------------------------------------------------------------------------
        */

        $service->sendOtp(
            $phone,
            $request->ip()
        );

        return response()->json([

            'message' => 'Verification code sent successfully.',

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | VERIFY PHONE OTP
    |--------------------------------------------------------------------------
    */

    public function verifyPhoneOtp(
        Request $request,
        PhoneVerificationService $service
    ): JsonResponse {

        $data =
            $request->validate([

                'otp' => [
                    'required',
                    'digits:6',
                ],

            ]);

        $user =
            $request->user();

        /*
        |--------------------------------------------------------------------------
        | GET PHONE SAFELY
        |--------------------------------------------------------------------------
        */

        $phone =
            $this->resolveRegistrationPhone(
                $user
            );

        if (! $phone) {

            return response()->json([

                'message' => 'No phone number was found for this account. Please return to the identity step and enter your phone number.',

            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | VERIFY OTP
        |--------------------------------------------------------------------------
        */

        $service->verify(
            $phone,
            $data['otp']
        );

        /*
        |--------------------------------------------------------------------------
        | UPDATE USER
        |--------------------------------------------------------------------------
        */

        $user->update([

            'phone' => $phone,

            'phone_verified_at' => now(),

        ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE APPLICATION
        |--------------------------------------------------------------------------
        */

        RegistrationApplication::query()

            ->where(
                'user_id',
                $user->id
            )

            ->where(
                'status',
                'draft'
            )

            ->latest()

            ->first()

            ?->update([

                'phone' => $phone,

                'phone_verified' => true,

            ]);

        return response()->json([

            'message' => 'Phone number verified successfully.',

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | EMAIL STATUS
    |--------------------------------------------------------------------------
    */

    public function emailStatus(
        Request $request
    ): JsonResponse {

        $verified =
            $request
                ->user()
                ->hasVerifiedEmail();

        if ($verified) {

            RegistrationApplication::query()

                ->where(
                    'user_id',
                    $request->user()->id
                )

                ->where(
                    'status',
                    'draft'
                )

                ->latest()

                ->first()

                ?->update([

                    'email_verified' => true,

                ]);
        }

        return response()->json([

            'verified' => $verified,

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | RESOLVE REGISTRATION PHONE
    |--------------------------------------------------------------------------
    |
    | This prevents the:
    |
    | Argument #1 ($phone) must be of type string, null given
    |
    | error.
    |
    */

    private function resolveRegistrationPhone(
        User $user
    ): ?string {

        /*
        |--------------------------------------------------------------------------
        | USERS TABLE
        |--------------------------------------------------------------------------
        */

        if (
            is_string(
                $user->phone
            )
            &&
            trim(
                $user->phone
            ) !== ''
        ) {

            return trim(
                $user->phone
            );
        }

        /*
        |--------------------------------------------------------------------------
        | REGISTRATION APPLICATION FALLBACK
        |--------------------------------------------------------------------------
        */

        $application =
            RegistrationApplication::query()

                ->where(
                    'user_id',
                    $user->id
                )

                ->latest()

                ->first();

        if (
            $application
            &&
            is_string(
                $application->phone
            )
            &&
            trim(
                $application->phone
            ) !== ''
        ) {

            return trim(
                $application->phone
            );
        }

        return null;
    }
}
