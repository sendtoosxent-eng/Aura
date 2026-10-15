<?php

use Database\Factories\DoctorFactory;
use Database\Factories\HealthcareProviderFactory;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('welcome displays saved hospital clinic and doctor details and images', function () {
    $hospital = HealthcareProviderFactory::new()->create([
        'name' => 'Test Referral Hospital',
        'provider_type' => 'hospital',
        'image' => 'images/test-hospital.jpg',
    ]);
    HealthcareProviderFactory::new()->create([
        'name' => 'Test Family Clinic',
        'image' => 'facilities/test-clinic.jpg',
    ]);
    DoctorFactory::new()->create([
        'healthcare_provider_id' => $hospital->id,
        'name' => 'Dr. Test Specialist',
        'specialty' => 'Cardiology',
        'image' => 'https://example.com/doctor.jpg',
    ]);
    HealthcareProviderFactory::new()->create(['name' => 'Unpublished Facility', 'is_verified' => false]);
    DoctorFactory::new()->create(['name' => 'Unpublished Doctor', 'is_verified' => false]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Test Referral Hospital')
        ->assertSee('Test Family Clinic')
        ->assertSee('Dr. Test Specialist')
        ->assertSee('Cardiology')
        ->assertSee('/images/test-hospital.jpg')
        ->assertSee('/storage/facilities/test-clinic.jpg')
        ->assertSee('https://example.com/doctor.jpg')
        ->assertDontSee('Unpublished Facility')
        ->assertDontSee('Unpublished Doctor');
});

test('welcome uses a neutral placeholder when a record has no photo', function () {
    HealthcareProviderFactory::new()->create(['image' => null]);
    DoctorFactory::new()->create(['image' => null]);

    $this->get('/')
        ->assertOk()
        ->assertSee('src="/images/profile-placeholder.svg"', false);
});

test('directory lists doctors without requiring doctor in their name', function () {
    DoctorFactory::new()->count(7)->create(['name' => 'Jane Namuli']);
    DoctorFactory::new()->create(['is_verified' => false]);
    HealthcareProviderFactory::new()->create();

    $this->getJson('/health-search?category=doctor')
        ->assertOk()
        ->assertJsonCount(7, 'doctors')
        ->assertJsonCount(0, 'providers')
        ->assertJsonPath('doctors.0.name', 'Jane Namuli');
});

test('directory filters facilities by category and returns saved service details', function () {
    HealthcareProviderFactory::new()->create([
        'name' => 'Family Care',
        'description' => 'Care for all ages',
        'services' => ['Ultrasound'],
        'image' => 'facilities/family.jpg',
    ]);
    HealthcareProviderFactory::new()->create(['provider_type' => 'hospital', 'services' => ['Ultrasound']]);
    HealthcareProviderFactory::new()->create(['is_verified' => false, 'services' => ['Ultrasound']]);

    $this->getJson('/health-search?category=clinic&q=Ultrasound')
        ->assertOk()
        ->assertJsonCount(1, 'providers')
        ->assertJsonCount(0, 'doctors')
        ->assertJsonPath('providers.0.name', 'Family Care')
        ->assertJsonPath('providers.0.description', 'Care for all ages')
        ->assertJsonPath('providers.0.services.0', 'Ultrasound')
        ->assertJsonPath('providers.0.image', 'facilities/family.jpg');
});

test('profile endpoints return the saved details and associated doctor photos', function () {
    $provider = HealthcareProviderFactory::new()->create([
        'description' => 'Family health services',
        'services' => ['Maternity'],
        'image' => 'facilities/profile.jpg',
    ]);
    $doctor = DoctorFactory::new()->create([
        'healthcare_provider_id' => $provider->id,
        'bio' => 'Family medicine specialist',
        'image' => 'doctors/profile.jpg',
    ]);

    $this->getJson('/providers/'.$provider->id.'/profile')
        ->assertOk()
        ->assertJsonPath('profile.description', 'Family health services')
        ->assertJsonPath('profile.services.0', 'Maternity')
        ->assertJsonPath('profile.image', 'facilities/profile.jpg')
        ->assertJsonPath('profile.doctors.0.image', 'doctors/profile.jpg');

    $this->getJson('/doctors/'.$doctor->id.'/profile')
        ->assertOk()
        ->assertJsonPath('profile.bio', 'Family medicine specialist')
        ->assertJsonPath('profile.image', 'doctors/profile.jpg')
        ->assertJsonPath('profile.facility.id', $provider->id);
});

test('directory returns only emergency facilities when requested', function () {
    HealthcareProviderFactory::new()->create(['emergency_available' => true]);
    HealthcareProviderFactory::new()->create(['emergency_available' => false]);

    $this->getJson('/health-search?category=emergency')
        ->assertOk()
        ->assertJsonCount(1, 'providers')
        ->assertJsonPath('providers.0.emergency_available', true);
});

test('search rejects invalid input with 422', function (array $query, string $field) {
    $this->getJson('/health-search?'.http_build_query($query))
        ->assertUnprocessable()
        ->assertJsonValidationErrors($field);
})->with([
    'unknown category' => [['category' => 'invalid'], 'category'],
    'non-string search' => [['q' => ['clinic']], 'q'],
    'long search' => [['q' => str_repeat('a', 201)], 'q'],
]);
