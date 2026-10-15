<?php

namespace App\Models;

use Database\Factories\ProviderUnavailableDateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderUnavailableDate extends Model
{
    /** @use HasFactory<ProviderUnavailableDateFactory> */
    use HasFactory;

    protected $fillable = [
        'healthcare_provider_id',
        'created_by_user_id',
        'unavailable_date',
        'reason',
    ];

    protected $casts = [
        'unavailable_date' => 'date',
    ];

    public function healthcareProvider(): BelongsTo
    {
        return $this->belongsTo(HealthcareProvider::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
