<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePatientProfileRequest;
use App\Services\MobileProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class PatientProfileController extends Controller
{
    public function update(
        UpdatePatientProfileRequest $request,
        MobileProfileService $profiles,
    ): JsonResponse {
        $user = $request->user();
        $data = $request->validated();
        $newPhotoPaths = [];

        foreach (['profile_photo' => 'patients/profile-photos', 'cover_photo' => 'patients/cover-photos'] as $field => $directory) {
            if ($request->hasFile($field)) {
                $newPhotoPaths[$field] = $request->file($field)->store($directory, 'local');
            }
        }

        $oldPhotoPaths = [];

        try {
            DB::transaction(function () use ($user, $data, $newPhotoPaths, &$oldPhotoPaths): void {
                $emailChanged = strtolower($data['email']) !== strtolower((string) $user->email);
                $phoneChanged = $data['phone'] !== $user->phone;

                $user->fill([
                    'name' => trim($data['name']),
                    'phone' => $data['phone'],
                    'email' => strtolower($data['email']),
                    'nin' => strtoupper($data['nin']),
                ]);
                if ($emailChanged) {
                    $user->email_verified_at = null;
                }
                if ($phoneChanged) {
                    $user->phone_verified_at = null;
                }
                $user->save();

                $profile = $user->patientProfile()->firstOrNew();
                foreach (array_keys($newPhotoPaths) as $field) {
                    if ($profile->{$field}) {
                        $oldPhotoPaths[] = $profile->{$field};
                    }
                }

                $profile->fill(Arr::only($data, [
                    'date_of_birth',
                    'gender',
                    'blood_group',
                    'systolic',
                    'diastolic',
                    'heart_rate',
                    'height_cm',
                    'weight_kg',
                    'allergies',
                    'chronic_conditions',
                    'current_medications',
                    'emergency_contact_name',
                    'emergency_contact_relationship',
                    'emergency_contact_phone',
                    'health_data_consent',
                ]) + $newPhotoPaths);
                $profile->save();
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete(array_values($newPhotoPaths));

            throw $exception;
        }

        Storage::disk('local')->delete($oldPhotoPaths);

        return response()->json([
            'message' => 'Your profile has been updated.',
            'data' => $profiles->authenticated($user->fresh()),
        ]);
    }

    public function photo(Request $request, string $type): StreamedResponse
    {
        $column = match ($type) {
            'profile' => 'profile_photo',
            'cover' => 'cover_photo',
            default => abort(404),
        };
        $path = $request->user()->patientProfile?->{$column};

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }
}
