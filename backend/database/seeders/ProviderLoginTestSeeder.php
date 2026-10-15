<?php

namespace Database\Seeders;

use App\Models\HealthcareProvider;
use App\Models\RegistrationApplication;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProviderLoginTestSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | TEST FACILITY
        |--------------------------------------------------------------------------
        */

        $facilityUser = User::updateOrCreate(
            [
                'email' => 'facility@afyalink.test',
            ],
            [
                'name' => 'Kampala Family Medical Centre',

                'phone' => '+256700111222',

                'registration_number' => 'AFY-FAC-2026-000101',

                'account_type' => 'facility',

                'registration_status' => 'verified',

                'email_verified_at' => now(),

                'phone_verified_at' => now(),

                'password' => Hash::make('Password123!'),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | FACILITY PROVIDER RECORD
        |--------------------------------------------------------------------------
        */

        $facility = HealthcareProvider::updateOrCreate(
            [
                'name' => 'Kampala Family Medical Centre',
            ],
            [
                'provider_type' => 'Clinic',

                'ownership' => 'Private',

                'district' => 'Kampala',

                'town' => 'Ntinda',

                'address' => 'Ntinda, Kampala',

                'phone' => '+256700111222',

                'email' => 'facility@afyalink.test',

                'description' => 'AfyaLink demonstration healthcare facility.',

                'rating' => 4.6,

                'review_count' => 28,

                'is_verified' => true,

                'is_featured' => true,

                'open_24_hours' => false,

                'emergency_available' => true,

                'is_demo' => true,

                'services' => [
                    'General Consultation',
                    'Laboratory Services',
                    'Maternal Health',
                    'Child Health',
                ],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | FACILITY REGISTRATION APPLICATION
        |--------------------------------------------------------------------------
        */

        RegistrationApplication::updateOrCreate(
            [
                'user_id' => $facilityUser->id,
            ],
            [
                'application_type' => 'facility',

                'status' => 'verified',

                'reference' => 'AFY-FAC-2026-000101',

                'healthcare_provider_id' => $facility->id,

                'first_name' => 'Sarah',

                'middle_name' => null,

                'last_name' => 'Nabirye',

                'phone' => '+256700111222',

                'email' => 'facility@afyalink.test',

                'facility_name' => 'Kampala Family Medical Centre',

                'facility_type' => 'Clinic',

                'ownership' => 'Private',

                'district' => 'Kampala',

                'town' => 'Ntinda',

                'address' => 'Ntinda, Kampala',

                'operating_license_number' => 'KCCA-CLINIC-2026-101',

                'operating_license_expiry' => '2027-12-31',

                'verification_notes' => json_encode([
                    'in_charge' => 'Sarah Nabirye',
                    'open_24_hours' => false,
                    'emergency_available' => true,
                    'services' => [
                        'General Consultation',
                        'Laboratory Services',
                        'Maternal Health',
                        'Child Health',
                    ],
                ], JSON_THROW_ON_ERROR),

                'phone_verified' => true,

                'email_verified' => true,

                'identity_verified' => true,

                'regulator_verified' => true,

                'verified_at' => now(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | TEST HEALTH PROFESSIONAL
        |--------------------------------------------------------------------------
        */

        $professionalUser = User::updateOrCreate(
            [
                'email' => 'doctor@afyalink.test',
            ],
            [
                'name' => 'Dr. Daniel Kato',

                'phone' => '+256700333444',

                'registration_number' => 'AFY-PRO-2026-000102',

                'account_type' => 'professional',

                'registration_status' => 'verified',

                'email_verified_at' => now(),

                'phone_verified_at' => now(),

                'password' => Hash::make('Password123!'),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | PROFESSIONAL REGISTRATION APPLICATION
        |--------------------------------------------------------------------------
        */

        RegistrationApplication::updateOrCreate(
            [
                'user_id' => $professionalUser->id,
            ],
            [
                'application_type' => 'professional',

                'status' => 'verified',

                'reference' => 'AFY-PRO-2026-000102',

                'healthcare_provider_id' => $facility->id,

                'first_name' => 'Daniel',

                'middle_name' => null,

                'last_name' => 'Kato',

                'phone' => '+256700333444',

                'email' => 'doctor@afyalink.test',

                'profession' => 'Doctor',

                'cadre' => 'Doctor',

                'specialty' => 'General Medicine',

                'regulatory_body' => 'Uganda Medical and Dental Practitioners Council',

                'registration_number' => 'UMDPC-20482',

                'practising_license_number' => 'APL-2026-4412',

                'license_expiry_date' => '2027-12-31',

                'qualification' => 'Bachelor of Medicine and Bachelor of Surgery',

                'years_experience' => 6,

                'district' => 'Kampala',

                'town' => 'Ntinda',

                'phone_verified' => true,

                'email_verified' => true,

                'identity_verified' => true,

                'regulator_verified' => true,

                'verified_at' => now(),
            ]
        );

        $this->command->info(
            'AfyaLink provider login test accounts created successfully.'
        );
    }
}
