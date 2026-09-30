<?php

namespace App\Models;

use App\Enums\ContactSubject;
use App\Enums\RequestStatus;
use App\Models\Concerns\HasReference;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasReference;

    protected $fillable = [
        'reference', 'locale', 'status', 'subject', 'full_name', 'organization', 'email', 'phone',
        'country', 'message', 'consent_at', 'internal_notes', 'ip_address',
    ];

    public static function referencePrefix(): string
    {
        return 'C';
    }

    protected function casts(): array
    {
        return [
            'status' => RequestStatus::class,
            'subject' => ContactSubject::class,
            'consent_at' => 'datetime',
        ];
    }
}
