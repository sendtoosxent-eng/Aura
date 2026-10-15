<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'nin',
        'account_type',
        'registration_status',
        'registration_number',
        'password',
        'two_factor_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'nin',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
        ];
    }

    public function patientProfile(): HasOne
    {
        return $this->hasOne(
            PatientProfile::class
        );
    }

    public function appointmentBookings(): HasMany
    {
        return $this->hasMany(
            AppointmentBooking::class
        );
    }

    public function trustedLogins(): HasMany
    {
        return $this->hasMany(
            TrustedLogin::class
        );
    }

    public function loginVerifications(): HasMany
    {
        return $this->hasMany(LoginVerification::class);
    }

    public function registrationApplications(): HasMany
    {
        return $this->hasMany(RegistrationApplication::class);
    }

    public function userNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    public function pushDevices(): HasMany
    {
        return $this->hasMany(PushDevice::class);
    }

    public function patientMessageThreads(): HasMany
    {
        return $this->hasMany(MessageThread::class, 'patient_user_id');
    }

    public function providerMessageThreads(): HasMany
    {
        return $this->hasMany(MessageThread::class, 'provider_user_id');
    }
}
