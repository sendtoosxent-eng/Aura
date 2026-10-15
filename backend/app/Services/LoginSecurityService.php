<?php

namespace App\Services;

use App\Models\LoginVerification;
use App\Models\TrustedLogin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginSecurityService
{
    public function __construct(private AfricasTalkingSmsService $sms) {}

    public function deviceHash(string $deviceId): string
    {
        return hash('sha256', trim($deviceId));
    }

    public function isTrusted(User $user, string $ipAddress, string $deviceId): bool
    {
        return $user->trustedLogins()
            ->where('ip_address', $ipAddress)
            ->where('device_hash', $this->deviceHash($deviceId))
            ->exists();
    }

    public function touchTrustedLogin(User $user, string $ipAddress, string $deviceId, ?string $userAgent): void
    {
        $user->trustedLogins()
            ->where('ip_address', $ipAddress)
            ->where('device_hash', $this->deviceHash($deviceId))
            ->update(['last_seen_at' => now(), 'user_agent' => $userAgent]);
    }

    /** @return array{challenge: LoginVerification, debug_code: ?string} */
    public function createChallenge(User $user, string $ipAddress, string $deviceId, ?string $userAgent): array
    {
        if (! $user->phone) {
            throw ValidationException::withMessages([
                'phone' => 'This account has no phone number for security verification.',
            ]);
        }

        LoginVerification::query()
            ->where('user_id', $user->id)
            ->whereNull('verified_at')
            ->delete();

        $otp = (string) random_int(100000, 999999);
        $challenge = LoginVerification::query()->create([
            'user_id' => $user->id,
            'token_hash' => Hash::make($otp),
            'ip_address' => $ipAddress,
            'device_hash' => $this->deviceHash($deviceId),
            'user_agent' => $userAgent,
            'expires_at' => now()->addMinutes(5),
        ]);

        try {
            $this->sms->send(
                $user->phone,
                "Your AfyaLink login code is {$otp}. It expires in 5 minutes. Do not share this code.",
            );
        } catch (\Throwable $exception) {
            $challenge->delete();
            throw $exception;
        }

        return [
            'challenge' => $challenge,
            'debug_code' => app()->environment('local', 'testing') && config('app.debug') ? $otp : null,
        ];
    }

    /** @return array{challenge: LoginVerification, debug_code: ?string} */
    public function resendChallenge(
        LoginVerification $challenge,
        string $ipAddress,
        string $deviceId,
        ?string $userAgent,
    ): array {
        if ($challenge->verified_at) {
            throw ValidationException::withMessages(['otp' => 'This login has already been verified.']);
        }

        if ($challenge->ip_address !== $ipAddress
            || ! hash_equals($challenge->device_hash, $this->deviceHash($deviceId))) {
            throw ValidationException::withMessages(['device_id' => 'This verification request belongs to another device.']);
        }

        if ($challenge->created_at->gt(now()->subMinute())) {
            throw ValidationException::withMessages(['otp' => 'Please wait one minute before requesting another code.']);
        }

        return $this->createChallenge($challenge->user, $ipAddress, $deviceId, $userAgent);
    }

    public function verifyChallenge(
        LoginVerification $challenge,
        string $otp,
        string $ipAddress,
        string $deviceId,
        ?string $userAgent,
    ): User {
        if ($challenge->verified_at) {
            throw ValidationException::withMessages(['otp' => 'This login code has already been used.']);
        }

        if ($challenge->expires_at->isPast()) {
            throw ValidationException::withMessages(['otp' => 'This login code has expired.']);
        }

        if ($challenge->attempts >= 5) {
            throw ValidationException::withMessages(['otp' => 'Too many incorrect attempts. Sign in again.']);
        }

        if ($challenge->ip_address !== $ipAddress
            || ! hash_equals($challenge->device_hash, $this->deviceHash($deviceId))) {
            throw ValidationException::withMessages(['device_id' => 'This verification request belongs to another device.']);
        }

        $challenge->increment('attempts');

        if (! Hash::check($otp, $challenge->token_hash)) {
            throw ValidationException::withMessages(['otp' => 'The verification code is incorrect.']);
        }

        $challenge->update(['verified_at' => now()]);
        $user = $challenge->user;

        TrustedLogin::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'ip_address' => $ipAddress,
                'device_hash' => $this->deviceHash($deviceId),
            ],
            [
                'user_agent' => $userAgent,
                'first_seen_at' => now(),
                'last_seen_at' => now(),
                'verified_at' => now(),
            ],
        );

        return $user;
    }
}
