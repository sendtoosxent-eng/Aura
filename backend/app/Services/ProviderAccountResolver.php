<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\HealthcareProvider;
use App\Models\User;

class ProviderAccountResolver
{
    public function healthcareProvider(User $user): ?HealthcareProvider
    {
        if (! in_array($user->account_type, ['facility', 'professional'], true)) {
            return null;
        }

        $application = $user->registrationApplications()->latest('id')->first();
        if ($application?->healthcare_provider_id) {
            return HealthcareProvider::query()->find($application->healthcare_provider_id);
        }

        if ($user->account_type === 'facility' && $application?->facility_name) {
            return HealthcareProvider::query()->where('name', $application->facility_name)->first();
        }

        return null;
    }

    public function doctor(User $user): ?Doctor
    {
        if ($user->account_type !== 'professional') {
            return null;
        }

        return Doctor::query()
            ->where(fn ($query) => $query->where('email', $user->email)->orWhere('phone', $user->phone))
            ->first();
    }
}
