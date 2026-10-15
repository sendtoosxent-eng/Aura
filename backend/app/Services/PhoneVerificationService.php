<?php

namespace App\Services;

use App\Models\PhoneVerification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PhoneVerificationService
{
    public function __construct(
        private AfricasTalkingSmsService $sms
    ) {}

    public function sendOtp(
        string $phone,
        ?string $ip = null
    ): ?string {

        $existing =
            PhoneVerification::query()
                ->where('phone', $phone)
                ->latest()
                ->first();

        if (
            $existing &&
            $existing->last_sent_at &&
            $existing->last_sent_at
                ->gt(now()->subSeconds(60))
        ) {

            throw ValidationException::withMessages([
                'phone' => 'Please wait before requesting another code.',
            ]);
        }

        $otp =
            (string) random_int(
                100000,
                999999
            );

        PhoneVerification::create([
            'phone' => $phone,

            'otp_hash' => Hash::make($otp),

            'attempts' => 0,

            'expires_at' => now()->addMinutes(5),

            'last_sent_at' => now(),

            'ip_address' => $ip,
        ]);

        try {
            $this->sms->send(
                $phone,
                "Your AfyaLink verification code is {$otp}. "
                .'It expires in 5 minutes. '
                .'Do not share this code.'
            );
        } catch (\Throwable $exception) {
            PhoneVerification::query()
                ->where('phone', $phone)
                ->whereNull('verified_at')
                ->latest()
                ->first()?->delete();
            throw $exception;
        }

        return app()->environment('local', 'testing') && config('app.debug') ? $otp : null;
    }

    public function verify(
        string $phone,
        string $otp
    ): void {

        $record =
            PhoneVerification::query()
                ->where('phone', $phone)
                ->whereNull('verified_at')
                ->latest()
                ->first();

        if (! $record) {

            throw ValidationException::withMessages([
                'otp' => 'No active verification code was found.',
            ]);
        }

        if (
            $record->expires_at->isPast()
        ) {

            throw ValidationException::withMessages([
                'otp' => 'This verification code has expired.',
            ]);
        }

        if ($record->attempts >= 5) {

            throw ValidationException::withMessages([
                'otp' => 'Too many incorrect attempts. Request a new code.',
            ]);
        }

        $record->increment('attempts');

        if (
            ! Hash::check(
                $otp,
                $record->otp_hash
            )
        ) {

            throw ValidationException::withMessages([
                'otp' => 'The verification code is incorrect.',
            ]);
        }

        $record->update([
            'verified_at' => now(),
        ]);
    }
}
