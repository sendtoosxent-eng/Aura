<?php

namespace App\Models;

use Database\Factories\MessageThreadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageThread extends Model
{
    /** @use HasFactory<MessageThreadFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_user_id', 'provider_user_id', 'healthcare_provider_id',
        'doctor_id', 'title', 'subtitle', 'is_automated', 'last_activity_at',
    ];

    protected function casts(): array
    {
        return ['is_automated' => 'boolean', 'last_activity_at' => 'datetime'];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_user_id');
    }

    public function providerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_user_id');
    }

    public function healthcareProvider(): BelongsTo
    {
        return $this->belongsTo(HealthcareProvider::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }
}
