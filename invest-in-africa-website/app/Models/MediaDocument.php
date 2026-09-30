<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Public guides and brochures (§5.4 "Documents téléchargeables"). */
class MediaDocument extends Model
{
    use HasTranslations;

    protected array $translatable = ['title'];

    protected $fillable = ['title', 'file', 'category', 'domain_id', 'is_published'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function extension(): string
    {
        return strtoupper(pathinfo($this->file, PATHINFO_EXTENSION));
    }
}
