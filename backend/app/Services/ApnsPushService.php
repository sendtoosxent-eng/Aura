<?php

namespace App\Services;

use App\Models\PushDevice;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ApnsPushService
{
    /** @param array<string, string|int|bool> $data */
    public function send(User $user, string $title, string $body, array $data = []): void
    {
        if (! $this->isConfigured()) {
            return;
        }

        $user->pushDevices()
            ->whereNull('disabled_at')
            ->each(function (PushDevice $device) use ($title, $body, $data): void {
                $this->sendToDevice($device, $title, $body, $data);
            });
    }

    /** @param array<string, string|int|bool> $data */
    private function sendToDevice(PushDevice $device, string $title, string $body, array $data): void
    {
        $host = $device->environment === 'production'
            ? 'https://api.push.apple.com'
            : 'https://api.sandbox.push.apple.com';
        $response = Http::withToken($this->providerToken())
            ->withHeaders([
                'apns-topic' => $device->app_bundle_id,
                'apns-push-type' => 'alert',
                'apns-priority' => '10',
            ])
            ->withOptions(['version' => 2.0])
            ->post("{$host}/3/device/{$device->token}", [
                'aps' => [
                    'alert' => ['title' => $title, 'body' => $body],
                    'sound' => 'default',
                    'badge' => 1,
                ],
                'data' => $data,
            ]);

        if ($response->successful()) {
            return;
        }

        $reason = $response->json('reason');
        if ($response->status() === 410 || in_array($reason, ['BadDeviceToken', 'DeviceTokenNotForTopic', 'Unregistered'], true)) {
            $device->update(['disabled_at' => now()]);

            return;
        }

        Log::warning('APNs rejected an AfyaLink push notification.', [
            'push_device_id' => $device->id,
            'status' => $response->status(),
            'reason' => $reason,
        ]);

        throw new RuntimeException('APNs rejected the notification request.');
    }

    private function isConfigured(): bool
    {
        return filled(config('apns.key_id'))
            && filled(config('apns.team_id'))
            && filled(config('apns.private_key_path'));
    }

    private function providerToken(): string
    {
        return Cache::remember('apns.provider-token', now()->addMinutes(50), function (): string {
            $header = $this->base64UrlEncode(json_encode([
                'alg' => 'ES256',
                'kid' => config('apns.key_id'),
            ], JSON_THROW_ON_ERROR));
            $claims = $this->base64UrlEncode(json_encode([
                'iss' => config('apns.team_id'),
                'iat' => now()->timestamp,
            ], JSON_THROW_ON_ERROR));
            $unsignedToken = $header.'.'.$claims;
            $keyPath = (string) config('apns.private_key_path');
            $keyContents = is_file($keyPath) ? file_get_contents($keyPath) : false;
            if (! $keyContents) {
                throw new RuntimeException('The APNs private signing key could not be read.');
            }
            $privateKey = openssl_pkey_get_private($keyContents);
            if (! $privateKey || ! openssl_sign($unsignedToken, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
                throw new RuntimeException('The APNs provider token could not be signed.');
            }

            return $unsignedToken.'.'.$this->base64UrlEncode($this->derSignatureToJose($signature));
        });
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function derSignatureToJose(string $signature): string
    {
        $offset = 2;
        if ((ord($signature[1]) & 0x80) !== 0) {
            $offset += ord($signature[1]) & 0x7F;
        }
        if (ord($signature[$offset]) !== 0x02) {
            throw new RuntimeException('Invalid APNs signature format.');
        }
        $rLength = ord($signature[$offset + 1]);
        $r = substr($signature, $offset + 2, $rLength);
        $offset += 2 + $rLength;
        if (ord($signature[$offset]) !== 0x02) {
            throw new RuntimeException('Invalid APNs signature format.');
        }
        $sLength = ord($signature[$offset + 1]);
        $s = substr($signature, $offset + 2, $sLength);

        return str_pad(ltrim($r, "\0"), 32, "\0", STR_PAD_LEFT)
            .str_pad(ltrim($s, "\0"), 32, "\0", STR_PAD_LEFT);
    }
}
