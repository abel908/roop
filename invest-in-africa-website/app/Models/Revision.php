<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/** Snapshot of a record before a change — history and restoration (§8.1). */
class Revision extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['revisionable_type', 'revisionable_id', 'snapshot', 'user_id'];

    protected function casts(): array
    {
        return ['snapshot' => 'array'];
    }

    public function revisionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
