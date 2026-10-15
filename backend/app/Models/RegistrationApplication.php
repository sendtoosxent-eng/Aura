<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RegistrationApplication extends Model
{
    protected $hidden = [
        'nin',
        'national_id_front',
        'national_id_back',
        'profile_photo',
        'verification_notes',
    ];

    protected $fillable = [

        'user_id',
        'application_type',
        'status',
        'reference',

        'healthcare_provider_id',

        'first_name',
        'middle_name',
        'last_name',

        'date_of_birth',
        'sex',
        'nationality',

        'phone',
        'email',
        'nin',

        'national_id_front',
        'national_id_back',
        'profile_photo',

        'profession',
        'cadre',
        'specialty',

        'regulatory_body',

        'registration_number',

        'practising_license_number',

        'license_expiry_date',

        'license_status',

        'qualification',

        'years_experience',

        'facility_name',
        'facility_type',

        'ownership',

        'operating_license_number',

        'operating_license_expiry',

        'ursb_registration_number',

        'district',
        'town',
        'address',

        'latitude',
        'longitude',

        'phone_verified',
        'email_verified',

        'identity_verified',

        'regulator_verified',

        'submitted_at',

        'verified_at',

        'verified_by',

        'verification_notes',
    ];

    protected $casts = [

        'date_of_birth' => 'date',

        'license_expiry_date' => 'date',

        'operating_license_expiry' => 'date',

        'latitude' => 'float',

        'longitude' => 'float',

        'phone_verified' => 'boolean',

        'email_verified' => 'boolean',

        'identity_verified' => 'boolean',

        'regulator_verified' => 'boolean',

        'submitted_at' => 'datetime',

        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function workplace(): BelongsTo
    {
        return $this->belongsTo(
            HealthcareProvider::class,
            'healthcare_provider_id'
        );
    }

    public function healthcareProvider()
    {
        return $this->belongsTo(
            HealthcareProvider::class,
        );
    }

    public function documents(): HasMany
    {
        return $this->hasMany(RegistrationDocument::class);
    }
}
