<?php

use App\Models\HealthcareProvider;
use App\Models\RegistrationApplication;
use App\Models\RegistrationDocument;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

uses(LazilyRefreshDatabase::class);

test('registration page explains authentication and upload errors clearly', function () {
    $this->get(route('registration.start'))
        ->assertOk()
        ->assertSee('Your sign-in session has expired. Reload the page, sign in again, and continue your registration.', false)
        ->assertSee('One or more uploaded files are too large for the server.', false);
});

test('facility identity creates a private draft and sends email verification', function () {
    Notification::fake();
    Storage::fake('local');

    $response = $this->postJson(route('registration.identity'), [
        'application_type' => 'facility',
        'first_name' => 'Amina',
        'middle_name' => 'N.',
        'last_name' => 'Kato',
        'date_of_birth' => '1990-01-01',
        'sex' => 'female',
        'nationality' => 'Ugandan',
        'profile_photo' => UploadedFile::fake()->image('amina.jpg'),
        'phone' => '+256700111222',
        'email' => 'amina@example.test',
        'nin' => 'CF123456789012',
        'password' => 'Strong!Pass1',
        'password_confirmation' => 'Strong!Pass1',
    ]);

    $response->assertOk()->assertJsonPath('next_step', 'email')->assertJsonPath('email_sent', true);

    $user = User::query()->where('email', 'amina@example.test')->firstOrFail();
    $application = RegistrationApplication::query()->whereBelongsTo($user)->firstOrFail();

    expect($user->email_verified_at)->toBeNull()
        ->and($application->status)->toBe('draft')
        ->and($application->profile_photo)->not->toBeNull();
    Storage::disk('local')->assertExists($application->profile_photo);
    Notification::assertSentTo($user, VerifyEmail::class);
    $this->assertAuthenticatedAs($user);
});

test('professionals can search verified workplaces', function () {
    HealthcareProvider::query()->create(['name' => 'Verified Medical Centre', 'provider_type' => 'clinic', 'district' => 'Kampala', 'is_verified' => true]);
    HealthcareProvider::query()->create(['name' => 'Unverified Medical Centre', 'provider_type' => 'clinic', 'district' => 'Kampala', 'is_verified' => false]);

    $this->getJson(route('registration.facilities', ['q' => 'Medical']))
        ->assertOk()->assertJsonCount(1, 'facilities')->assertJsonPath('facilities.0.name', 'Verified Medical Centre');
});

test('verified applicant can go back and save identity changes without creating another account', function () {
    Storage::fake('local');
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'applicant@example.test',
        'phone' => '+256700000001',
        'nin' => 'CM123456789012',
        'account_type' => 'facility',
        'registration_status' => 'draft',
    ]);
    $application = RegistrationApplication::query()->create([
        'user_id' => $user->id,
        'application_type' => 'facility',
        'status' => 'draft',
        'email_verified' => true,
        'profile_photo' => 'registration/profile-photos/original.jpg',
    ]);

    $response = $this->actingAs($user)->postJson(route('registration.identity.update'), [
        'application_type' => 'facility',
        'first_name' => 'Amina',
        'middle_name' => 'N.',
        'last_name' => 'Kato',
        'date_of_birth' => '1990-01-01',
        'sex' => 'female',
        'nationality' => 'Ugandan',
        'phone' => '+256700111222',
        'email' => 'applicant@example.test',
        'nin' => 'CF123456789012',
    ]);

    $response->assertOk()->assertJsonPath('next_step', 'regulatory');
    expect(User::query()->count())->toBe(1)
        ->and($user->fresh()->name)->toBe('Amina N. Kato')
        ->and($user->fresh()->hasVerifiedEmail())->toBeTrue()
        ->and($application->fresh()->first_name)->toBe('Amina')
        ->and($application->fresh()->profile_photo)->toBe('registration/profile-photos/original.jpg');
});

test('changing email while editing a draft requires verification again', function () {
    Notification::fake();
    $user = User::factory()->create([
        'email' => 'old@example.test',
        'phone' => '+256700000001',
        'nin' => 'CM123456789012',
        'account_type' => 'facility',
        'registration_status' => 'draft',
    ]);
    $application = RegistrationApplication::query()->create([
        'user_id' => $user->id,
        'application_type' => 'facility',
        'status' => 'draft',
        'email_verified' => true,
    ]);

    $response = $this->actingAs($user)->postJson(route('registration.identity.update'), [
        'application_type' => 'facility',
        'first_name' => 'Amina',
        'last_name' => 'Kato',
        'date_of_birth' => '1990-01-01',
        'sex' => 'female',
        'nationality' => 'Ugandan',
        'phone' => '+256700000001',
        'email' => 'new@example.test',
        'nin' => 'CM123456789012',
    ]);

    $response->assertOk()->assertJsonPath('next_step', 'email')->assertJsonPath('email_sent', true);
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse()
        ->and($application->fresh()->email_verified)->toBeFalse()
        ->and($application->fresh()->email)->toBe('new@example.test');
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('signed verification link verifies and signs in a user on another device', function () {
    $user = User::factory()->unverified()->create(['email' => 'verify@example.test']);
    RegistrationApplication::query()->create(['user_id' => $user->id, 'application_type' => 'facility', 'status' => 'draft']);

    $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
        'id' => $user->id,
        'hash' => sha1($user->getEmailForVerification()),
    ]);

    $this->get($url)->assertRedirect(route('registration.start'))->assertSessionHas('status', 'email-verified');

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue()
        ->and(RegistrationApplication::query()->whereBelongsTo($user)->value('email_verified'))->toBeTruthy();
    $this->assertAuthenticatedAs($user);
});

test('unverified applicants cannot submit an application', function () {
    $user = User::factory()->unverified()->create(['account_type' => 'facility', 'registration_status' => 'draft']);

    $this->actingAs($user)->postJson(route('registration.application.submit'), [])->assertForbidden();
});

test('verified applicant without a draft receives a recovery message', function () {
    $user = User::factory()->create([
        'account_type' => 'facility',
        'registration_status' => 'draft',
    ]);

    $this->actingAs($user)->postJson(route('registration.application.submit'), [
        'application_type' => 'facility',
        'regulatory' => [
            'name' => 'Recovery Clinic',
            'provider_type' => 'Clinic',
            'ownership' => 'Private',
            'district' => 'Kampala',
            'town' => 'Ntinda',
            'address' => 'Plot 1',
            'licence_number' => 'FAC-RECOVERY',
            'licence_expiry' => now()->addYear()->toDateString(),
            'phone' => '+256700123456',
        ],
        'documents' => [
            'operating_licence' => UploadedFile::fake()->create('licence.pdf', 10, 'application/pdf'),
            'registration_certificate' => UploadedFile::fake()->create('registration.pdf', 10, 'application/pdf'),
            'responsible_officer_id' => UploadedFile::fake()->image('officer.jpg'),
        ],
        'declaration' => true,
    ])->assertUnprocessable()
        ->assertJsonValidationErrorFor('application')
        ->assertJsonPath('errors.application.0', 'Your registration draft could not be found. Go back to Contact details, save your details, then return and submit again.');
});

test('verified facility applicant can submit details and private documents', function () {
    Storage::fake('local');
    $user = User::factory()->create(['account_type' => 'facility', 'registration_status' => 'draft']);
    $application = RegistrationApplication::query()->create(['user_id' => $user->id, 'application_type' => 'facility', 'status' => 'draft']);

    $response = $this->actingAs($user)->postJson(route('registration.application.submit'), [
        'application_type' => 'facility',
        'regulatory' => [
            'name' => 'Kampala Family Clinic', 'provider_type' => 'Clinic', 'ownership' => 'Private',
            'district' => 'Kampala', 'town' => 'Ntinda', 'address' => 'Plot 10 Health Road',
            'licence_number' => 'FAC-1234', 'licence_expiry' => now()->addYear()->toDateString(),
            'phone' => '+256700123456', 'email' => 'clinic@example.test', 'open_24_hours' => true,
            'services' => ['Outpatient care', 'Maternity'],
        ],
        'documents' => [
            'operating_licence' => UploadedFile::fake()->create('licence.pdf', 200, 'application/pdf'),
            'registration_certificate' => UploadedFile::fake()->create('registration.pdf', 200, 'application/pdf'),
            'responsible_officer_id' => UploadedFile::fake()->image('officer.jpg'),
            'facility_photo' => UploadedFile::fake()->image('facility.jpg'),
        ],
        'declaration' => true,
    ]);

    $response->assertOk()->assertJsonPath('message', 'Your application has been submitted for review.');
    $application->refresh();

    expect($application->status)->toBe('submitted')
        ->and($application->facility_name)->toBe('Kampala Family Clinic')
        ->and($application->email_verified)->toBeTrue()
        ->and($application->reference)->toStartWith('AFY-FAC-')
        ->and($user->fresh()->registration_status)->toBe('submitted')
        ->and($user->fresh()->registration_number)->toBe($application->reference)
        ->and($application->documents)->toHaveCount(4);

    $application->documents->each(fn (RegistrationDocument $document) => Storage::disk('local')->assertExists($document->path));
});

test('verified professional can submit against a verified workplace', function () {
    Storage::fake('local');
    $workplace = HealthcareProvider::query()->create(['name' => 'Mulago Test Hospital', 'provider_type' => 'hospital', 'district' => 'Kampala', 'is_verified' => true]);
    $user = User::factory()->create(['account_type' => 'professional', 'registration_status' => 'draft']);
    $application = RegistrationApplication::query()->create([
        'user_id' => $user->id, 'application_type' => 'professional', 'status' => 'draft', 'healthcare_provider_id' => $workplace->id,
    ]);

    $this->actingAs($user)->postJson(route('registration.application.submit'), [
        'application_type' => 'professional', 'healthcare_provider_id' => $workplace->id,
        'regulatory' => [
            'cadre' => 'Doctor', 'specialty' => 'Paediatrics',
            'regulatory_body' => 'Uganda Medical and Dental Practitioners Council',
            'registration_number' => 'UMDPC-1001', 'licence_number' => 'LIC-2002',
            'licence_expiry' => now()->addYear()->toDateString(), 'years_experience' => 5,
        ],
        'documents' => [
            'national_id' => UploadedFile::fake()->image('national-id.jpg'),
            'practising_licence' => UploadedFile::fake()->create('licence.pdf', 200, 'application/pdf'),
            'academic_certificate' => UploadedFile::fake()->create('certificate.pdf', 200, 'application/pdf'),
        ],
        'declaration' => true,
    ])->assertOk();

    $application->refresh();
    expect($application->status)->toBe('submitted')
        ->and($application->healthcare_provider_id)->toBe($workplace->id)
        ->and($application->registration_number)->toBe('UMDPC-1001')
        ->and($application->reference)->toStartWith('AFY-PRO-');
});
