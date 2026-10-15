<?php

use App\Models\AppointmentBooking;
use App\Models\HealthcareProvider;
use App\Models\ProviderUnavailableDate;
use App\Models\RegistrationApplication;
use App\Models\User;
use Database\Seeders\MobileDemoSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(LazilyRefreshDatabase::class);

test('provider can close and reopen appointment dates', function () {
    $provider = HealthcareProvider::factory()->create();
    $providerUser = User::factory()->create(['account_type' => 'facility']);
    RegistrationApplication::query()->create([
        'user_id' => $providerUser->id,
        'application_type' => 'facility',
        'status' => 'verified',
        'healthcare_provider_id' => $provider->id,
    ]);
    Sanctum::actingAs($providerUser, ['mobile']);
    $date = now()->addDays(5)->toDateString();

    $response = $this->postJson('/api/v1/provider-availability', [
        'date' => $date,
        'reason' => 'Staff training',
    ])->assertCreated()
        ->assertJsonPath('data.date', $date)
        ->assertJsonPath('data.reason', 'Staff training');

    $unavailableDate = ProviderUnavailableDate::query()->findOrFail($response->json('data.id'));
    $this->assertDatabaseHas('provider_unavailable_dates', [
        'healthcare_provider_id' => $provider->id,
    ]);
    expect($unavailableDate->unavailable_date->toDateString())->toBe($date);

    $this->getJson('/api/v1/provider-availability')
        ->assertOk()
        ->assertJsonPath('data.0.date', $date);
    $this->deleteJson("/api/v1/provider-availability/{$unavailableDate->id}")
        ->assertOk();
    $this->assertModelMissing($unavailableDate);
});

test('patients see unavailable dates and cannot request one', function () {
    $provider = HealthcareProvider::factory()->create();
    $date = now()->addDays(7)->toDateString();
    ProviderUnavailableDate::factory()->for($provider)->create(['unavailable_date' => $date]);

    $this->getJson("/api/v1/providers/{$provider->id}/availability?month=".now()->addDays(7)->format('Y-m'))
        ->assertOk()
        ->assertJsonPath('data.unavailable_dates.0.date', $date);

    $patient = User::factory()->create(['account_type' => 'patient']);
    Sanctum::actingAs($patient, ['mobile']);

    $this->postJson('/api/v1/appointments', [
        'healthcare_provider_id' => $provider->id,
        'appointment_date' => $date,
        'appointment_time' => '09:00',
        'appointment_type' => 'in_person',
        'service' => 'General consultation',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['appointment_date']);
    expect(AppointmentBooking::query()->count())->toBe(0);
});

test('appointment request remains pending until the provider approves it', function () {
    $provider = HealthcareProvider::factory()->create();
    $patient = User::factory()->create(['account_type' => 'patient']);
    Sanctum::actingAs($patient, ['mobile']);

    $response = $this->postJson('/api/v1/appointments', [
        'healthcare_provider_id' => $provider->id,
        'appointment_date' => now()->addDays(3)->toDateString(),
        'appointment_time' => '10:30',
        'appointment_type' => 'in_person',
        'service' => 'Laboratory',
    ])->assertCreated()
        ->assertJsonPath('data.status', 'pending');

    $this->assertDatabaseHas('appointment_bookings', [
        'id' => $response->json('data.id'),
        'status' => 'pending',
        'service' => 'Laboratory',
    ]);
});

test('patient receives 403 when managing provider unavailable dates', function () {
    $patient = User::factory()->create(['account_type' => 'patient']);
    Sanctum::actingAs($patient, ['mobile']);

    $this->postJson('/api/v1/provider-availability', [
        'date' => now()->addDay()->toDateString(),
    ])->assertForbidden();
});

test('mobile demo seeder creates closed dates and a pending request', function () {
    $this->seed(MobileDemoSeeder::class);

    $facility = HealthcareProvider::query()
        ->where('name', 'Kampala Family Medical Centre')
        ->firstOrFail();

    expect($facility->unavailableDates()->count())->toBe(3)
        ->and(AppointmentBooking::query()
            ->whereBelongsTo($facility, 'healthcareProvider')
            ->where('status', 'pending')
            ->where('service', 'General Consultation')
            ->exists())->toBeTrue();
});
