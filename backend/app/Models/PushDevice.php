<?php

namespace App\Models;

use Database\Factories\PushDeviceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushDevice extends Model
{
    /** @use HasFactory<PushDeviceFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token_hash',
        'token',
        'platform',
        'environment',
        'app_bundle_id',
        'last_seen_at',
        'disabled_at',
    ];

    protected function casts(): array
    {
        return [
            'token' => 'encrypted',
            'last_seen_at' => 'datetime',
            'disabled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
