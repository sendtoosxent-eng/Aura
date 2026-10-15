<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentBooking extends Model
{
    protected $fillable = [
        'user_id',
        'healthcare_provider_id',
        'doctor_id',
        'appointment_date',
        'appointment_time',
        'appointment_type',
        'service',
        'reason',
        'status',
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function healthcareProvider(): BelongsTo
    {
        return $this->belongsTo(HealthcareProvider::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
