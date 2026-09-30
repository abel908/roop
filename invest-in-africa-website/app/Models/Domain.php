<?php

namespace App\Models;

use App\Models\Concerns\HasRevisions;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\OptimizesImages;
use App\Support\Locales;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * The six official areas of intervention (What We Do, §5.4).
 * List fields (services, benefits, steps…) are stored per language as arrays.
 */
class Domain extends Model
{
    use HasRevisions, HasTranslations, OptimizesImages;

    protected array $translatable = ['slug', 'title', 'tagline', 'audience', 'intro', 'challenges', 'services', 'benefits', 'steps'];

    protected array $optimizedImages = ['cover_image'];

    protected $fillable = [
        'number', 'icon', 'slug', 'title', 'tagline', 'audience', 'intro', 'challenges',
        'services', 'benefits', 'steps', 'cover_image', 'is_published',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('number');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(MediaDocument::class);
    }

    public function url(?string $locale = null): string
    {
        $locale ??= Locales::current();

        return lroute('domain', ['domain' => $this->tr('slug', $locale)], $locale);
    }

    public static function findBySlug(string $slug, string $locale): ?self
    {
        return static::published()->get()->first(
            fn (self $domain) => $domain->tr('slug', $locale) === $slug || $domain->tr('slug', 'en') === $slug
        );
    }

    public function numberLabel(): string
    {
        return str_pad((string) $this->number, 2, '0', STR_PAD_LEFT);
    }
}
