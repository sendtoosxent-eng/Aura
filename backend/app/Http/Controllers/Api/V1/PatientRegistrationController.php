<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRegistrationRequest;
use App\Models\PatientProfile;
use App\Models\User;
use App\Services\MobileProfileService;
use App\Services\PhoneVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class PatientRegistrationController extends Controller
{
    public function store(
        StorePatientRegistrationRequest $request,
        MobileProfileService $profiles,
    ): JsonResponse {
        $data = $request->validated();
        $profilePhotoPath = $request->file('profile_photo')->store('patients/profile-photos', 'local');
        $coverPhotoPath = $request->file('cover_photo')->store('patients/cover-photos', 'local');

        try {
            $user = DB::transaction(function () use ($data, $profilePhotoPath, $coverPhotoPath): User {
                $user = User::query()->create([
                    'name' => trim($data['name']),
                    'email' => strtolower($data['email']),
                    'phone' => $data['phone'],
                    'nin' => strtoupper($data['nin']),
                    'account_type' => 'patient',
                    'registration_status' => 'active',
                    'password' => Hash::make($data['password']),
                ]);

                $user->update(['registration_number' => sprintf('PAT-%06d', $user->id)]);

                PatientProfile::query()->create([
                    'user_id' => $user->id,
                    'profile_photo' => $profilePhotoPath,
                    'cover_photo' => $coverPhotoPath,
                    'blood_group' => $data['blood_group'] ?? null,
                    'systolic' => $data['systolic'] ?? null,
                    'diastolic' => $data['diastolic'] ?? null,
                    'heart_rate' => $data['heart_rate'] ?? null,
                    'height_cm' => $data['height_cm'] ?? null,
                    'weight_kg' => $data['weight_kg'] ?? null,
                    'allergies' => $data['allergies'] ?? null,
                    'chronic_conditions' => $data['chronic_conditions'] ?? null,
                    'emergency_contact_name' => $data['emergency_contact_name'],
                    'emergency_contact_relationship' => $data['emergency_contact_relationship'],
                    'emergency_contact_phone' => $data['emergency_contact_phone'],
                    'health_data_consent' => true,
                ]);

                return $user->fresh('patientProfile');
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete([$profilePhotoPath, $coverPhotoPath]);

            throw $exception;
        }

        return response()->json([
            'message' => 'Your patient account has been created. Verify your phone number to continue.',
            'token_type' => 'Bearer',
            'access_token' => $user->createToken($data['device_name'], ['mobile'])->plainTextToken,
            'data' => $profiles->authenticated($user),
        ], 201);
    }

    public function sendPhoneOtp(Request $request, PhoneVerificationService $service): JsonResponse
    {
        $this->ensurePatient($request->user());
        $debugCode = $service->sendOtp($request->user()->phone, $request->ip());

        return response()->json(array_filter([
            'message' => 'A verification code has been sent to your phone.',
            'debug_code' => $debugCode,
        ], fn (mixed $value): bool => $value !== null));
    }

    public function verifyPhoneOtp(
        Request $request,
        PhoneVerificationService $service,
        MobileProfileService $profiles,
    ): JsonResponse {
        $data = $request->validate(['otp' => ['required', 'digits:6']]);
        $user = $request->user();
        $this->ensurePatient($user);
        $service->verify($user->phone, $data['otp']);
        $user->forceFill(['phone_verified_at' => now()])->save();

        return response()->json([
            'message' => 'Your phone number has been verified.',
            'verified' => true,
            'data' => $profiles->authenticated($user->fresh()),
        ]);
    }

    private function ensurePatient(User $user): void
    {
        if ($user->account_type !== 'patient') {
            throw ValidationException::withMessages([
                'account' => 'This action is only available to patient accounts.',
            ]);
        }
    }
}
