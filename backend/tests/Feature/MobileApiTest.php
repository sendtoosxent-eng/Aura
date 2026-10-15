<?php

use App\Models\AppointmentBooking;
use App\Models\Doctor;
use App\Models\HealthcareProvider;
use App\Models\LoginVerification;
use App\Models\PhoneVerification;
use App\Models\RegistrationApplication;
use App\Models\RegistrationDocument;
use App\Models\TrustedLogin;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

uses(LazilyRefreshDatabase::class);

test('mobile login returns a safe profile then verifies an untrusted device', function () {
    $user = User::factory()->create([
        'name' => 'Musa Balondemu',
        'phone' => '+256700111222',
        'account_type' => 'patient',
        'password' => Hash::make('Strong!Pass1'),
    ]);

    $this->postJson('/api/v1/auth/lookup', [
        'phone' => $user->phone,
        'role' => 'patient',
        'device_id' => 'ios-device-1',
    ])->assertOk()
        ->assertJsonPath('data.name', 'Musa Balondemu')
        ->assertJsonPath('data.requires_security_otp', true);

    $login = $this->postJson('/api/v1/auth/login', [
        'phone' => $user->phone,
        'password' => 'Strong!Pass1',
        'role' => 'patient',
        'device_id' => 'ios-device-1',
        'device_name' => 'Musa iPhone',
    ])->assertStatus(202)->assertJsonPath('requires_security_otp', true);

    $challenge = LoginVerification::query()->findOrFail($login->json('challenge_id'));
    $challenge->update(['token_hash' => Hash::make('123456')]);

    $this->postJson('/api/v1/auth/login/verify', [
        'challenge_id' => $challenge->id,
        'otp' => '123456',
        'device_id' => 'ios-device-1',
        'device_name' => 'Musa iPhone',
    ])->assertOk()->assertJsonStructure(['access_token', 'data' => ['id', 'name', 'profile_id']]);

    expect(TrustedLogin::query()->whereBelongsTo($user)->exists())->toBeTrue();

    $this->postJson('/api/v1/auth/login', [
        'phone' => $user->phone,
        'password' => 'Strong!Pass1',
        'role' => 'patient',
        'device_id' => 'ios-device-1',
        'device_name' => 'Musa iPhone',
    ])->assertOk()->assertJsonPath('requires_security_otp', false);
});

test('mobile login can resend a code after the cooldown', function () {
    config()->set('africastalking.driver', 'log');
    $user = User::factory()->create([
        'phone' => '+256700111223',
        'account_type' => 'patient',
        'password' => Hash::make('Strong!Pass1'),
    ]);

    $login = $this->postJson('/api/v1/auth/login', [
        'phone' => $user->phone,
        'password' => 'Strong!Pass1',
        'role' => 'patient',
        'device_id' => 'resend-device',
        'device_name' => 'Musa iPhone',
    ])->assertStatus(202);

    $this->travel(61)->seconds();
    $resend = $this->postJson('/api/v1/auth/login/resend', [
        'challenge_id' => $login->json('challenge_id'),
        'device_id' => 'resend-device',
    ])->assertOk()->assertJsonStructure(['challenge_id', 'expires_at']);

    expect($resend->json('challenge_id'))->not->toBe($login->json('challenge_id'));
});

test('patient registration persists the profile and restores every saved detail after login', function () {
    Storage::fake('local');
    config()->set('africastalking.driver', 'log');

    $registration = $this->post('/api/v1/patient-registration', [
        'name' => 'Musa Balondemu',
        'phone' => '+256770123456',
        'email' => 'musa.mobile@example.test',
        'nin' => 'CM12345678ABCD',
        'password' => 'Strong!Pass1',
        'password_confirmation' => 'Strong!Pass1',
        'profile_photo' => UploadedFile::fake()->image('profile.jpg'),
        'cover_photo' => UploadedFile::fake()->image('cover.jpg'),
        'blood_group' => 'O+',
        'systolic' => 120,
        'diastolic' => 80,
        'heart_rate' => 72,
        'height_cm' => 175,
        'weight_kg' => 70,
        'allergies' => 'Penicillin',
        'chronic_conditions' => 'Asthma',
        'emergency_contact_name' => 'Sarah Namono',
        'emergency_contact_relationship' => 'Spouse',
        'emergency_contact_phone' => '+256780123456',
        'health_data_consent' => '1',
        'device_name' => 'Musa iPhone',
    ], ['Accept' => 'application/json']);

    $registration->assertCreated()
        ->assertJsonPath('data.name', 'Musa Balondemu')
        ->assertJsonPath('data.patient_profile.blood_group', 'O+')
        ->assertJsonPath('data.patient_profile.emergency_contact_name', 'Sarah Namono')
        ->assertJsonStructure(['access_token', 'data' => ['profile_id', 'patient_profile']]);
    $user = User::query()->where('phone', '+256770123456')->firstOrFail();
    $profile = $user->patientProfile()->firstOrFail();
    expect(Hash::check('Strong!Pass1', $user->password))->toBeTrue();
    $this->assertDatabaseHas('patient_profiles', [
        'user_id' => $user->id,
        'blood_group' => 'O+',
        'systolic' => 120,
        'emergency_contact_relationship' => 'Spouse',
    ]);
    Storage::disk('local')->assertExists($profile->profile_photo);
    Storage::disk('local')->assertExists($profile->cover_photo);

    $registrationToken = $registration->json('access_token');
    $this->withToken($registrationToken)
        ->postJson('/api/v1/patient-registration/phone/send')
        ->assertOk();
    PhoneVerification::query()->where('phone', $user->phone)->latest()->firstOrFail()
        ->update(['otp_hash' => Hash::make('654321')]);
    $this->withToken($registrationToken)
        ->postJson('/api/v1/patient-registration/phone/verify', ['otp' => '654321'])
        ->assertOk()
        ->assertJsonPath('verified', true)
        ->assertJsonPath('data.phone_verified', true);
    expect($user->fresh()->phone_verified_at)->not->toBeNull();

    $this->postJson('/api/v1/auth/lookup', [
        'phone' => $user->phone,
        'role' => 'patient',
        'device_id' => 'returning-patient-device',
    ])->assertOk()
        ->assertJsonMissingPath('data.patient_profile');

    $login = $this->postJson('/api/v1/auth/login', [
        'phone' => $user->phone,
        'password' => 'Strong!Pass1',
        'role' => 'patient',
        'device_id' => 'returning-patient-device',
        'device_name' => 'Musa new iPhone',
    ])->assertStatus(202);
    $challenge = LoginVerification::query()->findOrFail($login->json('challenge_id'));
    $challenge->update(['token_hash' => Hash::make('123456')]);

    $this->postJson('/api/v1/auth/login/verify', [
        'challenge_id' => $challenge->id,
        'otp' => '123456',
        'device_id' => 'returning-patient-device',
        'device_name' => 'Musa new iPhone',
    ])->assertOk()
        ->assertJsonPath('data.patient_profile.blood_group', 'O+')
        ->assertJsonPath('data.patient_profile.systolic', 120)
        ->assertJsonPath('data.patient_profile.diastolic', 80)
        ->assertJsonPath('data.patient_profile.allergies', 'Penicillin')
        ->assertJsonPath('data.patient_profile.chronic_conditions', 'Asthma')
        ->assertJsonPath('data.patient_profile.emergency_contact_relationship', 'Spouse')
        ->assertJsonPath('data.patient_profile.emergency_contact_phone', '+256780123456');
});

test('login reports missing real sms credentials without leaving a challenge', function () {
    config()->set('africastalking.driver', 'africastalking');
    config()->set('africastalking.api_key', '');
    $user = User::factory()->create([
        'phone' => '+256700111224',
        'account_type' => 'patient',
        'password' => Hash::make('Strong!Pass1'),
    ]);

    $this->postJson('/api/v1/auth/login', [
        'phone' => $user->phone,
        'password' => 'Strong!Pass1',
        'role' => 'patient',
        'device_id' => 'unconfigured-sms-device',
        'device_name' => 'Musa iPhone',
    ])->assertStatus(503)
        ->assertJsonPath('message', 'SMS delivery is not configured. Add the Africa’s Talking username and API key on the server.');

    expect(LoginVerification::query()->whereBelongsTo($user)->exists())->toBeFalse();
});

test('provider identity API creates a private draft and mobile token', function () {
    Notification::fake();
    Storage::fake('local');

    $response = $this->post('/api/v1/provider-registration/identity', [
        'application_type' => 'facility',
        'first_name' => 'Amina',
        'middle_name' => 'N.',
        'last_name' => 'Kato',
        'date_of_birth' => '1990-01-01',
        'sex' => 'female',
        'nationality' => 'Ugandan',
        'profile_photo' => UploadedFile::fake()->image('amina.jpg'),
        'phone' => '+256700333444',
        'email' => 'mobile-provider@example.test',
        'nin' => 'CF123456789012',
        'password' => 'Strong!Pass1',
        'password_confirmation' => 'Strong!Pass1',
        'device_name' => 'Amina iPhone',
    ], ['Accept' => 'application/json']);

    $response->assertCreated()
        ->assertJsonPath('next_step', 'email_verification')
        ->assertJsonStructure(['access_token', 'application_id', 'data']);

    $user = User::query()->where('email', 'mobile-provider@example.test')->firstOrFail();
    $application = RegistrationApplication::query()->whereBelongsTo($user)->firstOrFail();

    expect($application->status)->toBe('draft')
        ->and($user->tokens()->count())->toBe(1);
    Storage::disk('local')->assertExists($application->profile_photo);
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('provider can complete the mobile verification and application flow', function () {
    Notification::fake();
    Storage::fake('local');
    config()->set('africastalking.driver', 'log');

    $identity = $this->post('/api/v1/provider-registration/identity', [
        'application_type' => 'facility',
        'first_name' => 'Amina',
        'last_name' => 'Kato',
        'date_of_birth' => '1990-01-01',
        'sex' => 'female',
        'nationality' => 'Ugandan',
        'profile_photo' => UploadedFile::fake()->image('amina.jpg'),
        'phone' => '+256700333445',
        'email' => 'complete-provider@example.test',
        'nin' => 'CF123456789013',
        'password' => 'Strong!Pass1',
        'password_confirmation' => 'Strong!Pass1',
        'device_name' => 'Amina iPhone',
    ], ['Accept' => 'application/json'])->assertCreated();
    $token = $identity->json('access_token');
    $user = User::query()->where('email', 'complete-provider@example.test')->firstOrFail();
    $user->markEmailAsVerified();

    $this->withToken($token)->postJson('/api/v1/provider-registration/phone/send')
        ->assertOk();
    PhoneVerification::query()->where('phone', $user->phone)->latest()->firstOrFail()
        ->update(['otp_hash' => Hash::make('123456')]);
    $this->withToken($token)->postJson('/api/v1/provider-registration/phone/verify', ['otp' => '123456'])
        ->assertOk()
        ->assertJsonPath('verified', true);

    $response = $this->withToken($token)->post('/api/v1/provider-registration/application', [
        'application_type' => 'facility',
        'regulatory' => [
            'name' => 'Amina Family Clinic',
            'provider_type' => 'Clinic',
            'ownership' => 'Private',
            'district' => 'Kampala',
            'town' => 'Ntinda',
            'address' => 'Plot 1, Ntinda Road',
            'phone' => '+256700333445',
            'licence_number' => 'CLINIC-100',
            'licence_expiry' => now()->addYear()->toDateString(),
            'services' => ['Outpatient care'],
        ],
        'documents' => [
            'operating_licence' => UploadedFile::fake()->create('operating.pdf', 100, 'application/pdf'),
            'registration_certificate' => UploadedFile::fake()->create('registration.pdf', 100, 'application/pdf'),
            'responsible_officer_id' => UploadedFile::fake()->image('officer.png'),
        ],
        'declaration' => '1',
    ], ['Accept' => 'application/json']);

    $response->assertOk()
        ->assertJsonPath('message', 'Your application has been submitted for review.')
        ->assertJsonStructure(['reference']);
    expect($user->fresh()->registration_status)->toBe('submitted');
    expect(RegistrationDocument::query()->count())->toBe(3);

    $this->withToken($token)->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.account_type', 'facility')
        ->assertJsonPath('data.name', 'Amina Family Clinic')
        ->assertJsonPath('data.provider_profile.application_type', 'facility')
        ->assertJsonPath('data.provider_profile.display_name', 'Amina Family Clinic')
        ->assertJsonPath('data.provider_profile.facility_type', 'Clinic')
        ->assertJsonPath('data.provider_profile.district', 'Kampala')
        ->assertJsonPath('data.provider_profile.services.0', 'Outpatient care');
});

test('professional login profile includes the saved workplace and licence details', function () {
    $facility = HealthcareProvider::factory()->create(['name' => 'Kampala Medical Centre']);
    $professional = User::factory()->create([
        'name' => 'Dr Jane Atim',
        'phone' => '+256700987654',
        'account_type' => 'professional',
        'registration_status' => 'submitted',
        'registration_number' => 'AFY-PRO-2026-000123',
    ]);
    RegistrationApplication::query()->create([
        'user_id' => $professional->id,
        'application_type' => 'professional',
        'status' => 'submitted',
        'reference' => 'AFY-PRO-2026-000123',
        'healthcare_provider_id' => $facility->id,
        'first_name' => 'Jane',
        'last_name' => 'Atim',
        'cadre' => 'Doctor',
        'specialty' => 'Paediatrics',
        'practising_license_number' => 'UMD-7788',
        'license_expiry_date' => '2027-12-31',
    ]);
    Sanctum::actingAs($professional, ['mobile']);

    $this->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.provider_profile.application_type', 'professional')
        ->assertJsonPath('data.provider_profile.display_name', 'Dr Jane Atim')
        ->assertJsonPath('data.provider_profile.role_title', 'Paediatrics')
        ->assertJsonPath('data.provider_profile.workplace', 'Kampala Medical Centre')
        ->assertJsonPath('data.provider_profile.licence_number', 'UMD-7788')
        ->assertJsonPath('data.provider_profile.licence_expiry', '2027-12-31');
});

test('discovery API only returns verified facilities and doctors with map coordinates', function () {
    $facility = HealthcareProvider::factory()->create([
        'name' => 'Verified Family Clinic',
        'latitude' => 0.3476,
        'longitude' => 32.5825,
        'image' => '/images/facility-demo-clinic.jpg',
        'services' => ['Family planning', 'Antenatal care'],
    ]);
    HealthcareProvider::factory()->create(['name' => 'Hidden Clinic', 'is_verified' => false]);
    Doctor::query()->create([
        'healthcare_provider_id' => $facility->id,
        'name' => 'Dr. Sarah Namubiru',
        'specialty' => 'Paediatrics',
        'district' => 'Kampala',
        'is_verified' => true,
    ]);

    $this->getJson('/api/v1/providers?service=Family%20planning&map_only=1')
        ->assertOk()->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Verified Family Clinic')
        ->assertJsonPath('data.0.image', '/images/facility-demo-clinic.jpg');

    $this->getJson('/api/v1/doctors')
        ->assertOk()->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Dr. Sarah Namubiru')
        ->assertJsonPath('data.0.latitude', 0.3476);
});

test('patients can book and the assigned professional can manage an appointment', function () {
    $facility = HealthcareProvider::factory()->create();
    $doctor = Doctor::query()->create([
        'healthcare_provider_id' => $facility->id,
        'name' => 'Dr. James Okello',
        'specialty' => 'General Medicine',
        'district' => 'Kampala',
        'email' => 'doctor@example.test',
        'is_verified' => true,
    ]);
    $patient = User::factory()->create(['account_type' => 'patient', 'phone' => '+256700000101']);
    $professional = User::factory()->create([
        'account_type' => 'professional',
        'email' => 'doctor@example.test',
        'phone' => '+256700000202',
    ]);
    RegistrationApplication::query()->create([
        'user_id' => $professional->id,
        'application_type' => 'professional',
        'status' => 'verified',
        'healthcare_provider_id' => $facility->id,
    ]);

    Sanctum::actingAs($patient, ['mobile']);
    $response = $this->postJson('/api/v1/appointments', [
        'healthcare_provider_id' => $facility->id,
        'doctor_id' => $doctor->id,
        'appointment_date' => now()->addDay()->toDateString(),
        'appointment_time' => '10:30',
        'appointment_type' => 'in_person',
        'service' => 'General Medicine',
        'reason' => 'Follow-up consultation',
    ])->assertCreated()
        ->assertJsonPath('data.status', 'pending')
        ->assertJsonPath('data.service', 'General Medicine');

    $booking = AppointmentBooking::query()->findOrFail($response->json('data.id'));
    Sanctum::actingAs($professional, ['mobile']);

    $this->patchJson("/api/v1/appointments/{$booking->id}", ['status' => 'confirmed'])
        ->assertOk()->assertJsonPath('data.status', 'confirmed')
        ->assertJsonPath('data.patient.name', $patient->name);
});
