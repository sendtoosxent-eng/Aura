<?php

namespace Database\Seeders;

use App\Models\HealthcareProvider;
use Illuminate\Database\Seeder;

class FacilityPhotoDemoSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'hospital' => '/images/facility-demo-hospital.jpg',
            'clinic' => '/images/facility-demo-clinic.jpg',
            'pharmacy' => '/images/facility-demo-pharmacy.jpg',
        ] as $providerType => $image) {
            HealthcareProvider::query()
                ->whereRaw('LOWER(provider_type) = ?', [$providerType])
                ->update(['image' => $image]);
        }

        $this->command?->info('Demo facility photos assigned to hospitals, clinics, and pharmacies.');
    }
}
