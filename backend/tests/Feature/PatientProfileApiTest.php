<?php

use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

uses(LazilyRefreshDatabase::class);

test('patient can update every profile field and replace saved photos', function () {
    Storage::fake('local');
    Storage::disk('local')->put('patients/profile-photos/old.jpg', 'old profile');
    Storage::disk('local')->put('patients/cover-photos/old.jpg', 'old cover');

    $user = User::factory()->create([
        'name' => 'Old Name',
        'phone' => '+256700111222',
        'email' => 'old@example.test',
        'nin' => 'CM12345678ABCD',
        'account_type' => 'patient',
        'phone_verified_at' => now(),
    ]);
    PatientProfile::query()->create([
        'user_id' => $user->id,
        'profile_photo' => 'patients/profile-photos/old.jpg',
        'cover_photo' => 'patients/cover-photos/old.jpg',
    ]);
    Sanctum::actingAs($user, ['mobile']);

    $response = $this->post('/api/v1/profile', [
        'name' => 'Musa Updated',
        'phone' => '+256770123456',
        'email' => 'musa.updated@example.test',
        'nin' => 'CM87654321WXYZ',
        'date_of_birth' => '1993-04-18',
        'gender' => 'Male',
        'profile_photo' => UploadedFile::fake()->image('profile-new.jpg'),
        'cover_photo' => UploadedFile::fake()->image('cover-new.jpg'),
        'blood_group' => 'O+',
        'systolic' => 118,
        'diastolic' => 76,
        'heart_rate' => 69,
        'height_cm' => 176.5,
        'weight_kg' => 72.4,
        'allergies' => 'Penicillin',
        'chronic_conditions' => 'Asthma',
        'current_medications' => 'Salbutamol inhaler',
        'emergency_contact_name' => 'Sarah Namono',
        'emergency_contact_relationship' => 'Spouse',
        'emergency_contact_phone' => '+256780123456',
        'health_data_consent' => '1',
    ], ['Accept' => 'application/json']);

    $response->assertOk()
        ->assertJsonPath('message', 'Your profile has been updated.')
        ->assertJsonPath('data.name', 'Musa Updated')
        ->assertJsonPath('data.nin', 'CM87654321WXYZ')
        ->assertJsonPath('data.phone_verified', false)
        ->assertJsonPath('data.patient_profile.date_of_birth', '1993-04-18')
        ->assertJsonPath('data.patient_profile.gender', 'Male')
        ->assertJsonPath('data.patient_profile.has_profile_photo', true)
        ->assertJsonPath('data.patient_profile.current_medications', 'Salbutamol inhaler')
        ->assertJsonPath('data.patient_profile.emergency_contact_relationship', 'Spouse');

    $profile = $user->fresh()->patientProfile;
    expect($user->fresh())
        ->name->toBe('Musa Updated')
        ->email_verified_at->toBeNull()
        ->phone_verified_at->toBeNull()
        ->and($profile->allergies)->toBe('Penicillin')
        ->and($profile->chronic_conditions)->toBe('Asthma')
        ->and($profile->current_medications)->toBe('Salbutamol inhaler');
    Storage::disk('local')->assertMissing('patients/profile-photos/old.jpg');
    Storage::disk('local')->assertMissing('patients/cover-photos/old.jpg');
    Storage::disk('local')->assertExists($profile->profile_photo);
    Storage::disk('local')->assertExists($profile->cover_photo);

    $this->get('/api/v1/profile/photos/profile')->assertOk();
    $this->get('/api/v1/profile/photos/cover')->assertOk();
});

test('profile update requires authentication and a patient account', function () {
    $this->postJson('/api/v1/profile', [])->assertUnauthorized();

    $provider = User::factory()->create(['account_type' => 'professional']);
    Sanctum::actingAs($provider, ['mobile']);

    $this->postJson('/api/v1/profile', [])->assertForbidden();
});

test('profile update validates identity and health details', function () {
    $existing = User::factory()->create([
        'phone' => '+256770000001',
        'email' => 'taken@example.test',
        'nin' => 'CF12345678ABCD',
    ]);
    $patient = User::factory()->create([
        'phone' => '+256770000002',
        'email' => 'patient@example.test',
        'nin' => 'CM12345678ABCD',
        'account_type' => 'patient',
    ]);
    Sanctum::actingAs($patient, ['mobile']);

    $this->postJson('/api/v1/profile', [
        'name' => 'Patient',
        'phone' => $existing->phone,
        'email' => $existing->email,
        'nin' => $existing->nin,
        'systolic' => 100,
        'diastolic' => 120,
        'emergency_contact_name' => 'Next of Kin',
        'emergency_contact_relationship' => 'Sibling',
        'emergency_contact_phone' => '+256780000003',
        'health_data_consent' => true,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['phone', 'email', 'nin', 'diastolic']);
});
