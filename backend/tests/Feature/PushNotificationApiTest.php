<?php

use App\Jobs\SendUserPushNotification;
use App\Models\PushDevice;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(LazilyRefreshDatabase::class);

test('returns 401 when registering a push device without authentication', function () {
    $this->putJson('/api/v1/push/devices', [
        'token' => str_repeat('a', 64),
        'environment' => 'development',
        'app_bundle_id' => 'ug.afyalink.ios',
    ])->assertUnauthorized();
});

test('registers and disables the signed in users push device', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['mobile']);
    $token = str_repeat('a1', 32);

    $this->putJson('/api/v1/push/devices', [
        'token' => strtoupper($token),
        'environment' => 'development',
        'app_bundle_id' => 'ug.afyalink.ios',
    ])->assertOk()
        ->assertJsonPath('message', 'This device is registered for push notifications.');

    $device = PushDevice::query()->firstOrFail();
    expect($device)
        ->user_id->toBe($user->id)
        ->token->toBe($token)
        ->disabled_at->toBeNull();
    expect((string) $device->getRawOriginal('token'))->not->toBe($token);

    $this->deleteJson('/api/v1/push/devices', ['token' => $token])
        ->assertOk();
    expect($device->fresh()->disabled_at)->not->toBeNull();
});

test('rejects malformed device tokens and environments', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['mobile']);

    $this->putJson('/api/v1/push/devices', [
        'token' => 'not-a-device-token',
        'environment' => 'staging',
        'app_bundle_id' => 'ug.afyalink.ios',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['token', 'environment']);
});

test('queues an APNs delivery whenever an in-app notification is created', function () {
    Queue::fake([SendUserPushNotification::class]);
    $user = User::factory()->create();

    $notification = UserNotification::factory()->create(['user_id' => $user->id]);

    Queue::assertPushed(
        SendUserPushNotification::class,
        fn (SendUserPushNotification $job): bool => $job->notificationId === $notification->id,
    );
});
