<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneVerification extends Model
{
    protected $fillable = [
        'phone', 'otp_hash', 'attempts', 'expires_at', 'verified_at',
        'last_sent_at', 'ip_address',
    ];

    protected $hidden = ['otp_hash'];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'last_sent_at' => 'datetime',
    ];
}
