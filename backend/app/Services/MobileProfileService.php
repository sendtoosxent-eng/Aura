<?php

namespace App\Services;

use App\Models\RegistrationApplication;
use App\Models\User;

class MobileProfileService
{
    /** @return array<string, mixed> */
    public function safe(User $user): array
    {
        $application = $user->registrationApplications()->latest('id')->first();
        $displayName = $user->account_type === 'facility'
            ? ($application?->facility_name ?: $user->name)
            : $user->name;

        return [
            'id' => $user->id,
            'name' => $displayName,
            'person_name' => $user->name,
            'initials' => collect(preg_split('/\s+/', trim($displayName)))
                ->filter()
                ->take(2)
                ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
                ->implode(''),
            'phone' => $user->phone,
            'email' => $user->email,
            'account_type' => $user->account_type ?: 'patient',
            'profile_id' => $user->registration_number ?: sprintf('PAT-%06d', $user->id),
            'registration_status' => $user->registration_status,
        ];
    }

    /** @return array<string, mixed> */
    public function authenticated(User $user): array
    {
        $user->loadMissing('patientProfile');
        $application = $user->registrationApplications()
            ->with('workplace:id,name,provider_type,district,town,address,phone,email')
            ->latest('id')
            ->first();

        return $this->safe($user) + [
            'nin' => $user->nin,
            'email_verified' => $user->hasVerifiedEmail(),
            'phone_verified' => $user->phone_verified_at !== null,
            'two_factor_enabled' => $user->two_factor_enabled,
            'patient_profile' => $this->patientProfile($user),
            'provider_profile' => $application ? $this->providerProfile($application) : null,
        ];
    }

    /** @return array<string, mixed>|null */
    private function patientProfile(User $user): ?array
    {
        $profile = $user->patientProfile;

        if (! $profile) {
            return null;
        }

        return [
            'date_of_birth' => $profile->date_of_birth?->toDateString(),
            'gender' => $profile->gender,
            'has_profile_photo' => filled($profile->profile_photo),
            'has_cover_photo' => filled($profile->cover_photo),
            'blood_group' => $profile->blood_group,
            'systolic' => $profile->systolic,
            'diastolic' => $profile->diastolic,
            'heart_rate' => $profile->heart_rate,
            'height_cm' => $profile->height_cm,
            'weight_kg' => $profile->weight_kg,
            'allergies' => $profile->allergies,
            'chronic_conditions' => $profile->chronic_conditions,
            'current_medications' => $profile->current_medications,
            'emergency_contact_name' => $profile->emergency_contact_name,
            'emergency_contact_relationship' => $profile->emergency_contact_relationship,
            'emergency_contact_phone' => $profile->emergency_contact_phone,
            'health_data_consent' => $profile->health_data_consent,
        ];
    }

    /** @return array<string, mixed> */
    private function providerProfile(RegistrationApplication $application): array
    {
        $metadata = json_decode($application->verification_notes ?? '{}', true);
        $metadata = is_array($metadata) ? $metadata : [];
        $isFacility = $application->application_type === 'facility';

        return [
            'application_type' => $application->application_type,
            'display_name' => $isFacility
                ? ($application->facility_name ?: $application->user?->name)
                : $application->user?->name,
            'role_title' => $isFacility
                ? $application->facility_type
                : ($application->specialty ?: $application->cadre),
            'registration_status' => $application->status,
            'registration_reference' => $application->reference,
            'workplace' => $isFacility
                ? $application->facility_name
                : $application->workplace?->name,
            'facility_type' => $application->facility_type,
            'ownership' => $application->ownership,
            'district' => $isFacility ? $application->district : $application->workplace?->district,
            'town' => $isFacility ? $application->town : $application->workplace?->town,
            'address' => $isFacility ? $application->address : $application->workplace?->address,
            'phone' => $isFacility ? $application->phone : $application->user?->phone,
            'email' => $isFacility ? $application->email : $application->user?->email,
            'licence_number' => $isFacility
                ? $application->operating_license_number
                : $application->practising_license_number,
            'licence_expiry' => ($isFacility
                ? $application->operating_license_expiry
                : $application->license_expiry_date)?->toDateString(),
            'specialty' => $application->specialty,
            'open_24_hours' => (bool) ($metadata['open_24_hours'] ?? false),
            'emergency_available' => (bool) ($metadata['emergency_available'] ?? false),
            'services' => array_values((array) ($metadata['services'] ?? [])),
        ];
    }
}
