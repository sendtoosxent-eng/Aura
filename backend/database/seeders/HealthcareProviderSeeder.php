<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\HealthcareProvider;
use Illuminate\Database\Seeder;

class HealthcareProviderSeeder extends Seeder
{
    public function run(): void
    {
        Doctor::query()->delete();
        HealthcareProvider::query()->delete();

        /*
        |--------------------------------------------------------------------------
        | KAMPALA
        |--------------------------------------------------------------------------
        */

        $mulago = HealthcareProvider::create([
            'name' => 'Mulago National Referral Hospital',
            'provider_type' => 'hospital',
            'ownership' => 'government',
            'district' => 'Kampala',
            'town' => 'Mulago',
            'address' => 'Mulago Hill, Kampala',
            'phone' => '+256 000 000 001',
            'description' => 'Sample AfyaLink listing for a government referral hospital.',
            'latitude' => 0.3377,
            'longitude' => 32.5764,
            'rating' => 4.8,
            'review_count' => 245,
            'is_verified' => true,
            'is_featured' => true,
            'open_24_hours' => true,
            'emergency_available' => true,
            'is_demo' => true,
            'image' => '/images/facility-demo-hospital.jpg',
            'services' => [
                'Emergency Care',
                'Maternity',
                'Pediatrics',
                'Surgery',
                'Laboratory',
                'Specialist Care',
            ],
        ]);

        $mengo = HealthcareProvider::create([
            'name' => 'Mengo Hospital',
            'provider_type' => 'hospital',
            'ownership' => 'private',
            'district' => 'Kampala',
            'town' => 'Mengo',
            'address' => 'Mengo, Kampala',
            'phone' => '+256 000 000 002',
            'description' => 'Sample private healthcare provider listing.',
            'latitude' => 0.3045,
            'longitude' => 32.5576,
            'rating' => 4.7,
            'review_count' => 186,
            'is_verified' => true,
            'is_featured' => true,
            'open_24_hours' => true,
            'emergency_available' => true,
            'is_demo' => true,
            'image' => '/images/facility-demo-hospital.jpg',
            'services' => [
                'General Medicine',
                'Maternity',
                'Surgery',
                'Pediatrics',
                'Laboratory',
            ],
        ]);

        $kampalaClinic = HealthcareProvider::create([
            'name' => 'Afya Kampala Medical Clinic',
            'provider_type' => 'clinic',
            'ownership' => 'private',
            'district' => 'Kampala',
            'town' => 'Ntinda',
            'address' => 'Ntinda, Kampala',
            'phone' => '+256 000 000 003',
            'description' => 'Demo private clinic for AfyaLink development.',
            'latitude' => 0.3543,
            'longitude' => 32.6123,
            'rating' => 4.6,
            'review_count' => 92,
            'is_verified' => true,
            'is_featured' => true,
            'open_24_hours' => false,
            'emergency_available' => false,
            'is_demo' => true,
            'image' => '/images/facility-demo-clinic.jpg',
            'services' => [
                'General Consultation',
                'Pediatrics',
                'Family Medicine',
                'Laboratory',
            ],
        ]);

        HealthcareProvider::create([
            'name' => 'Afya Central Pharmacy',
            'provider_type' => 'pharmacy',
            'ownership' => 'private',
            'district' => 'Kampala',
            'town' => 'Central Kampala',
            'address' => 'Kampala Road, Kampala',
            'phone' => '+256 000 000 004',
            'description' => 'Demo pharmacy listing.',
            'latitude' => 0.3150,
            'longitude' => 32.5810,
            'rating' => 4.5,
            'review_count' => 78,
            'is_verified' => true,
            'is_featured' => false,
            'open_24_hours' => true,
            'emergency_available' => false,
            'is_demo' => true,
            'image' => '/images/facility-demo-pharmacy.jpg',
            'services' => [
                'Prescription Medicines',
                'OTC Medicines',
                'Health Supplies',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | WAKISO
        |--------------------------------------------------------------------------
        */

        $wakisoHospital = HealthcareProvider::create([
            'name' => 'Wakiso Government Health Centre',
            'provider_type' => 'clinic',
            'ownership' => 'government',
            'district' => 'Wakiso',
            'town' => 'Wakiso',
            'address' => 'Wakiso Town',
            'phone' => '+256 000 000 005',
            'description' => 'Demo government healthcare listing.',
            'latitude' => 0.3980,
            'longitude' => 32.4780,
            'rating' => 4.3,
            'review_count' => 63,
            'is_verified' => true,
            'is_featured' => true,
            'open_24_hours' => false,
            'emergency_available' => true,
            'is_demo' => true,
            'image' => '/images/facility-demo-clinic.jpg',
            'services' => [
                'General Medicine',
                'Maternity',
                'Immunisation',
                'Laboratory',
            ],
        ]);

        HealthcareProvider::create([
            'name' => 'Afya Wakiso Pharmacy',
            'provider_type' => 'pharmacy',
            'ownership' => 'private',
            'district' => 'Wakiso',
            'town' => 'Nansana',
            'address' => 'Nansana, Wakiso',
            'phone' => '+256 000 000 006',
            'description' => 'Demo private pharmacy.',
            'latitude' => 0.3631,
            'longitude' => 32.5280,
            'rating' => 4.4,
            'review_count' => 57,
            'is_verified' => true,
            'is_featured' => false,
            'open_24_hours' => true,
            'emergency_available' => false,
            'is_demo' => true,
            'image' => '/images/facility-demo-pharmacy.jpg',
            'services' => [
                'Medicines',
                'Health Products',
                'Prescription Services',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | MBARARA
        |--------------------------------------------------------------------------
        */

        $mbararaHospital = HealthcareProvider::create([
            'name' => 'Mbarara Regional Referral Hospital',
            'provider_type' => 'hospital',
            'ownership' => 'government',
            'district' => 'Mbarara',
            'town' => 'Mbarara City',
            'address' => 'Mbarara City',
            'phone' => '+256 000 000 007',
            'description' => 'Sample government referral hospital listing.',
            'latitude' => -0.6160,
            'longitude' => 30.6570,
            'rating' => 4.7,
            'review_count' => 193,
            'is_verified' => true,
            'is_featured' => true,
            'open_24_hours' => true,
            'emergency_available' => true,
            'is_demo' => true,
            'image' => '/images/facility-demo-hospital.jpg',
            'services' => [
                'Emergency Care',
                'Surgery',
                'Maternity',
                'Pediatrics',
                'Specialist Care',
                'Laboratory',
            ],
        ]);

        HealthcareProvider::create([
            'name' => 'Afya Mbarara Medical Centre',
            'provider_type' => 'clinic',
            'ownership' => 'private',
            'district' => 'Mbarara',
            'town' => 'Mbarara City',
            'address' => 'Mbarara City',
            'phone' => '+256 000 000 008',
            'description' => 'Demo private medical centre.',
            'latitude' => -0.6070,
            'longitude' => 30.6540,
            'rating' => 4.5,
            'review_count' => 86,
            'is_verified' => true,
            'is_featured' => true,
            'open_24_hours' => false,
            'emergency_available' => false,
            'is_demo' => true,
            'image' => '/images/facility-demo-clinic.jpg',
            'services' => [
                'General Consultation',
                'Family Medicine',
                'Pediatrics',
                'Laboratory',
            ],
        ]);

        HealthcareProvider::create([
            'name' => 'Mbarara Community Pharmacy',
            'provider_type' => 'pharmacy',
            'ownership' => 'private',
            'district' => 'Mbarara',
            'town' => 'Mbarara City',
            'address' => 'Mbarara City',
            'phone' => '+256 000 000 009',
            'description' => 'Demo pharmacy listing.',
            'latitude' => -0.6100,
            'longitude' => 30.6560,
            'rating' => 4.2,
            'review_count' => 42,
            'is_verified' => true,
            'is_demo' => true,
            'image' => '/images/facility-demo-pharmacy.jpg',
            'services' => [
                'Medicines',
                'Prescription Services',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | MPIGI
        |--------------------------------------------------------------------------
        */

        $mpigiHospital = HealthcareProvider::create([
            'name' => 'Mpigi Government Health Centre',
            'provider_type' => 'clinic',
            'ownership' => 'government',
            'district' => 'Mpigi',
            'town' => 'Mpigi',
            'address' => 'Mpigi Town',
            'phone' => '+256 000 000 010',
            'description' => 'Demo government facility listing.',
            'latitude' => 0.2250,
            'longitude' => 32.3130,
            'rating' => 4.2,
            'review_count' => 48,
            'is_verified' => true,
            'is_featured' => true,
            'open_24_hours' => false,
            'emergency_available' => true,
            'is_demo' => true,
            'image' => '/images/facility-demo-clinic.jpg',
            'services' => [
                'General Medicine',
                'Maternity',
                'Immunisation',
                'Laboratory',
            ],
        ]);

        HealthcareProvider::create([
            'name' => 'Afya Mpigi Family Clinic',
            'provider_type' => 'clinic',
            'ownership' => 'private',
            'district' => 'Mpigi',
            'town' => 'Mpigi',
            'address' => 'Mpigi Town',
            'phone' => '+256 000 000 011',
            'description' => 'Demo private clinic.',
            'latitude' => 0.2280,
            'longitude' => 32.3160,
            'rating' => 4.4,
            'review_count' => 66,
            'is_verified' => true,
            'is_featured' => true,
            'open_24_hours' => false,
            'emergency_available' => false,
            'is_demo' => true,
            'image' => '/images/facility-demo-clinic.jpg',
            'services' => [
                'General Consultation',
                'Family Medicine',
                'Child Health',
            ],
        ]);

        HealthcareProvider::create([
            'name' => 'Mpigi Care Pharmacy',
            'provider_type' => 'pharmacy',
            'ownership' => 'private',
            'district' => 'Mpigi',
            'town' => 'Mpigi',
            'address' => 'Mpigi Town',
            'phone' => '+256 000 000 012',
            'description' => 'Demo pharmacy listing.',
            'latitude' => 0.2260,
            'longitude' => 32.3140,
            'rating' => 4.1,
            'review_count' => 31,
            'is_verified' => true,
            'is_demo' => true,
            'image' => '/images/facility-demo-pharmacy.jpg',
            'services' => [
                'Medicines',
                'Health Supplies',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | DOCTORS
        |--------------------------------------------------------------------------
        */

        Doctor::create([
            'healthcare_provider_id' => $mulago->id,
            'name' => 'Dr. Sarah N.',
            'specialty' => 'Pediatrics',
            'district' => 'Kampala',
            'town' => 'Mulago',
            'bio' => 'Demo pediatric specialist profile.',
            'rating' => 4.9,
            'review_count' => 118,
            'is_verified' => true,
            'is_featured' => true,
            'is_demo' => true,
            'image' => '/images/doctor-1.jpg',
        ]);

        Doctor::create([
            'healthcare_provider_id' => $mengo->id,
            'name' => 'Dr. Daniel O.',
            'specialty' => 'General Medicine',
            'district' => 'Kampala',
            'town' => 'Mengo',
            'bio' => 'Demo general medicine doctor profile.',
            'rating' => 4.8,
            'review_count' => 102,
            'is_verified' => true,
            'is_featured' => true,
            'is_demo' => true,
            'image' => '/images/doctor-2.jpg',
        ]);

        Doctor::create([
            'healthcare_provider_id' => $wakisoHospital->id,
            'name' => 'Dr. Ruth A.',
            'specialty' => 'Family Medicine',
            'district' => 'Wakiso',
            'town' => 'Wakiso',
            'bio' => 'Demo family medicine profile.',
            'rating' => 4.7,
            'review_count' => 91,
            'is_verified' => true,
            'is_featured' => true,
            'is_demo' => true,
            'image' => '/images/doctor-3.jpg',
        ]);

        Doctor::create([
            'healthcare_provider_id' => $mbararaHospital->id,
            'name' => 'Dr. Michael K.',
            'specialty' => 'Internal Medicine',
            'district' => 'Mbarara',
            'town' => 'Mbarara City',
            'bio' => 'Demo internal medicine specialist.',
            'rating' => 4.7,
            'review_count' => 84,
            'is_verified' => true,
            'is_featured' => true,
            'is_demo' => true,
            'image' => '/images/doctor-4.jpg',
        ]);

        Doctor::create([
            'healthcare_provider_id' => $mpigiHospital->id,
            'name' => 'Dr. Grace M.',
            'specialty' => 'General Practice',
            'district' => 'Mpigi',
            'town' => 'Mpigi',
            'bio' => 'Demo general practitioner profile.',
            'rating' => 4.5,
            'review_count' => 55,
            'is_verified' => true,
            'is_featured' => true,
            'is_demo' => true,
            'image' => '/images/doctor-1.jpg',
        ]);
    }
}
