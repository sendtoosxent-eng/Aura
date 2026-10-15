<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doctor extends Model
{
    protected $fillable = [
        'healthcare_provider_id',
        'name',
        'specialty',
        'district',
        'town',
        'phone',
        'email',
        'bio',
        'rating',
        'review_count',
        'is_verified',
        'is_featured',
        'is_demo',
        'image',
    ];

    protected $casts = [
        'rating' => 'float',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'is_demo' => 'boolean',
    ];

    public function healthcareProvider(): BelongsTo
    {
        return $this->belongsTo(HealthcareProvider::class);
    }
}