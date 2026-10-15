<?php

namespace App\Models;

use Database\Factories\RegistrationDocumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationDocument extends Model
{
    /** @use HasFactory<RegistrationDocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'registration_application_id',
        'document_type',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    protected $hidden = [
        'path',
    ];

    public function registrationApplication(): BelongsTo
    {
        return $this->belongsTo(RegistrationApplication::class);
    }
}
