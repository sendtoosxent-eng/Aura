<?php

namespace Database\Seeders;

use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MobileDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            HealthcareProviderSeeder::class,
            ProviderLoginTestSeeder::class,
        ]);

        $patient = User::query()->updateOrCreate(
            ['email' => 'patient@afyalink.test'],
            [
                'name' => 'Musa Balondemu',
                'phone' => '+256700555666',
                'registration_number' => 'AFY-PAT-2026-000103',
                'account_type' => 'patient',
                'registration_status' => 'verified',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'password' => Hash::make('Password123!'),
            ]
        );

        PatientProfile::query()->updateOrCreate(
            ['user_id' => $patient->id],
            [
                'date_of_birth' => '1992-04-18',
                'gender' => 'male',
                'blood_group' => 'O+',
                'systolic' => 118,
                'diastolic' => 76,
                'heart_rate' => 72,
                'height_cm' => 176,
                'weight_kg' => 72.5,
                'allergies' => 'Penicillin',
                'chronic_conditions' => 'Asthma',
                'current_medications' => 'Salbutamol inhaler',
                'emergency_contact_name' => 'Sarah Namono',
                'emergency_contact_relationship' => 'Spouse',
                'emergency_contact_phone' => '+256780123456',
                'health_data_consent' => true,
            ]
        );

        $this->call(MobileAppDataSeeder::class);
        $this->call(AppointmentAvailabilityDemoSeeder::class);
        $this->call(FacilityPhotoDemoSeeder::class);

        $this->command?->info('Mobile demo patient: +256700555666 / Password123!');
    }
}
