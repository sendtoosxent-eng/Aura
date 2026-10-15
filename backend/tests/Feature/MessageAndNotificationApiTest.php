<?php

use App\Models\ChatMessage;
use App\Models\HealthcareProvider;
use App\Models\MessageThread;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(LazilyRefreshDatabase::class);

test('returns 401 for mobile messages and notifications without a token', function () {
    $this->getJson('/api/v1/mobile-data')->assertUnauthorized();
});

test('returns only the signed-in users messages and notifications', function () {
    $patient = User::factory()->create(['account_type' => 'patient']);
    $provider = User::factory()->create(['account_type' => 'professional']);
    $otherPatient = User::factory()->create(['account_type' => 'patient']);
    $thread = MessageThread::factory()->create([
        'patient_user_id' => $patient->id,
        'provider_user_id' => $provider->id,
    ]);
    ChatMessage::factory()->create([
        'message_thread_id' => $thread->id,
        'sender_user_id' => $provider->id,
        'sender_type' => 'provider',
        'body' => 'Your results are ready.',
    ]);
    MessageThread::factory()->create(['patient_user_id' => $otherPatient->id]);
    $ownNotification = UserNotification::factory()->create(['user_id' => $patient->id, 'title' => 'Own update']);
    UserNotification::factory()->create(['user_id' => $otherPatient->id, 'title' => 'Private update']);
    Sanctum::actingAs($patient, ['mobile']);

    $this->getJson('/api/v1/mobile-data')
        ->assertOk()
        ->assertJsonCount(1, 'data.message_threads')
        ->assertJsonCount(1, 'data.notifications')
        ->assertJsonPath('data.message_threads.0.messages.0.text', 'Your results are ready.')
        ->assertJsonPath('data.notifications.0.id', $ownNotification->id);
});

test('persists a message and creates a notification for the recipient', function () {
    $patient = User::factory()->create(['name' => 'Musa', 'account_type' => 'patient']);
    $provider = User::factory()->create(['account_type' => 'professional']);
    $thread = MessageThread::factory()->create([
        'patient_user_id' => $patient->id,
        'provider_user_id' => $provider->id,
    ]);
    Sanctum::actingAs($patient, ['mobile']);

    $this->postJson("/api/v1/message-threads/{$thread->id}/messages", ['text' => 'My symptoms have improved.'])
        ->assertCreated()
        ->assertJsonPath('data.author', 'user')
        ->assertJsonPath('data.text', 'My symptoms have improved.');

    $this->assertDatabaseHas('chat_messages', [
        'message_thread_id' => $thread->id,
        'sender_user_id' => $patient->id,
        'body' => 'My symptoms have improved.',
    ]);
    $this->assertDatabaseHas('user_notifications', [
        'user_id' => $provider->id,
        'title' => 'New message from Musa',
    ]);
});

test('starts and persists a patient conversation with a facility account', function () {
    $patient = User::factory()->create(['name' => 'Musa', 'account_type' => 'patient']);
    $facilityUser = User::factory()->create([
        'account_type' => 'facility',
        'phone' => '+256700111222',
    ]);
    $facility = HealthcareProvider::factory()->create([
        'phone' => $facilityUser->phone,
        'email' => $facilityUser->email,
    ]);
    Sanctum::actingAs($patient, ['mobile']);

    $this->postJson('/api/v1/message-threads', [
        'healthcare_provider_id' => $facility->id,
        'text' => 'Can I visit tomorrow?',
    ])->assertCreated()
        ->assertJsonPath('data.participant_name', $facilityUser->name)
        ->assertJsonPath('data.messages.0.text', 'Can I visit tomorrow?');

    $this->assertDatabaseHas('message_threads', [
        'patient_user_id' => $patient->id,
        'provider_user_id' => $facilityUser->id,
        'healthcare_provider_id' => $facility->id,
    ]);
});

test('returns 404 when a user accesses another conversation', function () {
    $patient = User::factory()->create(['account_type' => 'patient']);
    $provider = User::factory()->create(['account_type' => 'professional']);
    $outsider = User::factory()->create(['account_type' => 'patient']);
    $thread = MessageThread::factory()->create([
        'patient_user_id' => $patient->id,
        'provider_user_id' => $provider->id,
    ]);
    Sanctum::actingAs($outsider, ['mobile']);

    $this->postJson("/api/v1/message-threads/{$thread->id}/messages", ['text' => 'Not allowed'])
        ->assertNotFound();
    $this->postJson("/api/v1/message-threads/{$thread->id}/read")
        ->assertNotFound();

    $this->assertDatabaseMissing('chat_messages', ['body' => 'Not allowed']);
});

test('persists message and notification read status only for the owner', function () {
    $patient = User::factory()->create(['account_type' => 'patient']);
    $provider = User::factory()->create(['account_type' => 'professional']);
    $outsider = User::factory()->create(['account_type' => 'patient']);
    $thread = MessageThread::factory()->create([
        'patient_user_id' => $patient->id,
        'provider_user_id' => $provider->id,
    ]);
    $message = ChatMessage::factory()->create([
        'message_thread_id' => $thread->id,
        'sender_user_id' => $provider->id,
        'sender_type' => 'provider',
    ]);
    $notification = UserNotification::factory()->create(['user_id' => $patient->id]);
    $secondNotification = UserNotification::factory()->create(['user_id' => $patient->id]);
    $outsiderNotification = UserNotification::factory()->create(['user_id' => $outsider->id]);
    Sanctum::actingAs($outsider, ['mobile']);

    $this->patchJson("/api/v1/notifications/{$notification->id}/read")->assertNotFound();

    Sanctum::actingAs($patient, ['mobile']);
    $this->postJson("/api/v1/message-threads/{$thread->id}/read")->assertOk();
    $this->patchJson("/api/v1/notifications/{$notification->id}/read")->assertOk();
    $this->postJson('/api/v1/notifications/read-all')->assertOk();

    expect($message->fresh()->read_at)->not->toBeNull();
    expect($notification->fresh()->read_at)->not->toBeNull();
    expect($secondNotification->fresh()->read_at)->not->toBeNull();
    expect($outsiderNotification->fresh()->read_at)->toBeNull();
});
