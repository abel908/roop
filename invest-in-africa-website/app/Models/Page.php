<?php

namespace App\Models;

use App\Jobs\OptimizeImage;
use App\Models\Concerns\HasRevisions;
use App\Models\Concerns\HasTranslations;
use App\Services\ImageOptimizer;
use App\Support\Locales;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Page built from design-system blocks (§8.1): each block's texts are stored
 * per language; draft / published status and scheduled publication.
 */
class Page extends Model
{
    use HasRevisions, HasTranslations;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    protected array $translatable = ['title', 'slug', 'seo_title', 'seo_description'];

    protected $fillable = ['title', 'slug', 'blocks', 'seo_title', 'seo_description', 'og_image', 'status', 'published_at', 'updated_by'];

    protected function casts(): array
    {
        return [
            'blocks' => 'array',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $page) {
            if (auth()->check()) {
                $page->updated_by = auth()->id();
            }
        });

        // AVIF / WebP variants for the images placed in blocks (§10.4).
        static::saved(function (self $page) {
            foreach ($page->blocks ?? [] as $block) {
                $image = $block['data']['image'] ?? null;

                if (is_string($image) && ImageOptimizer::supports($image) && ! ImageOptimizer::manifest($image)) {
                    OptimizeImage::dispatch($image);
                }
            }
        });
    }

    /** Published and publication date reached. */
    public function scopeLive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function isLive(): bool
    {
        return $this->status === self::STATUS_PUBLISHED && (! $this->published_at || $this->published_at->isPast());
    }

    public function url(?string $locale = null): string
    {
        $locale ??= Locales::current();

        return lroute('page', ['page' => $this->tr('slug', $locale)], $locale);
    }

    public static function findBySlug(string $slug, string $locale): ?self
    {
        return static::query()->live()->get()->first(
            fn (self $page) => $page->tr('slug', $locale) === $slug || $page->tr('slug', 'en') === $slug
        );
    }
}
