<?php

namespace App\Models;

use App\Enums\PartnerCategory;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Partner extends Model
{
    use HasTranslations;

    protected array $translatable = ['description'];

    protected $fillable = ['name', 'logo', 'website_url', 'category', 'description', 'is_featured', 'is_published', 'sort'];

    protected function casts(): array
    {
        return [
            'category' => PartnerCategory::class,
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('sort')->orderBy('name');
    }

    public function logoUrl(): ?string
    {
        return $this->logo ? Storage::disk('public')->url($this->logo) : null;
    }
}
