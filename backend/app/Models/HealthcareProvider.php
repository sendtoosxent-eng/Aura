<?php

namespace App\Models;

use Database\Factories\HealthcareProviderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HealthcareProvider extends Model
{
    /** @use HasFactory<HealthcareProviderFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'provider_type',
        'ownership',
        'district',
        'town',
        'address',
        'phone',
        'email',
        'description',
        'latitude',
        'longitude',
        'rating',
        'review_count',
        'is_verified',
        'is_featured',
        'open_24_hours',
        'emergency_available',
        'is_demo',
        'image',
        'services',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'rating' => 'float',
        'services' => 'array',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'open_24_hours' => 'boolean',
        'emergency_available' => 'boolean',
        'is_demo' => 'boolean',
    ];

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function unavailableDates(): HasMany
    {
        return $this->hasMany(ProviderUnavailableDate::class);
    }
}
