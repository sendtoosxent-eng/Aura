<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientProfile extends Model
{
    protected $fillable = [
        'user_id',
        'date_of_birth',
        'gender',
        'profile_photo',
        'cover_photo',
        'blood_group',
        'systolic',
        'diastolic',
        'heart_rate',
        'height_cm',
        'weight_kg',
        'allergies',
        'chronic_conditions',
        'current_medications',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'health_data_consent',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'health_data_consent' => 'boolean',
        'allergies' => 'encrypted',
        'chronic_conditions' => 'encrypted',
        'current_medications' => 'encrypted',
        'height_cm' => 'float',
        'weight_kg' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
