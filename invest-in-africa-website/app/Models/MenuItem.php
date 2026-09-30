<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Support\Locales;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Navigation administrable per language (§7.2 "Menus administrables par
 * langue", §8.1 "Menus"). A label left empty uses the default translation
 * of the section.
 */
class MenuItem extends Model
{
    use HasTranslations;

    public const LOCATIONS = ['header', 'footer'];

    /** Fixed sections that can be linked (route name => translation key). */
    public const SECTIONS = [
        'home' => 'site.nav.home',
        'about' => 'site.nav.about',
        'mission' => 'site.nav.mission',
        'what-we-do' => 'site.nav.what_we_do',
        'get-involved' => 'site.nav.get_involved',
        'invest' => 'site.nav.invest',
        'submit' => 'site.nav.submit',
        'partners' => 'site.nav.partners',
        'contact' => 'site.nav.contact',
    ];

    /** Mega-menus available in the header (§3.2). */
    public const MEGA = ['domains' => 'what-we-do', 'involved' => 'get-involved'];

    protected array $translatable = ['label', 'url'];

    protected $fillable = ['location', 'label', 'type', 'target', 'page_id', 'url', 'new_tab', 'is_active', 'sort'];

    protected function casts(): array
    {
        return ['new_tab' => 'boolean', 'is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('menus'));
        static::deleted(fn () => Cache::forget('menus'));
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort');
    }

    /** @return Collection<int, self> */
    public static function for(string $location): Collection
    {
        // Plain arrays in the cache (Eloquent objects are never unserialised).
        $rows = Cache::rememberForever('menus', function () {
            try {
                return static::query()->active()->get()->map->getAttributes()->all();
            } catch (\Throwable) {
                return [];
            }
        });

        $items = static::hydrate(array_values(array_filter($rows, fn ($row) => $row['location'] === $location)));

        return $items->contains('type', 'page') ? $items->load('page') : $items;
    }

    public function label(?string $locale = null): string
    {
        $label = $this->tr('label', $locale, false);

        if (filled($label)) {
            return $label;
        }

        return match ($this->type) {
            'route' => __(self::SECTIONS[$this->target] ?? 'site.nav.home', [], $locale),
            'mega' => __(self::SECTIONS[self::MEGA[$this->target] ?? 'home'], [], $locale),
            'page' => (string) $this->page?->tr('title', $locale),
            default => (string) $this->tr('label', $locale),
        };
    }

    public function href(?string $locale = null): string
    {
        $locale ??= Locales::current();

        return match ($this->type) {
            'route' => lroute($this->target, [], $locale),
            'mega' => lroute(self::MEGA[$this->target] ?? 'home', [], $locale),
            'page' => $this->page?->url($locale) ?? lroute('home', [], $locale),
            default => (string) $this->tr('url', $locale),
        };
    }

    /** Route names considered "current" for aria-current. */
    public function activePatterns(): array
    {
        return match ($this->type) {
            'route' => [$this->target],
            'mega' => $this->target === 'domains' ? ['what-we-do', 'domain'] : ['get-involved', 'invest', 'project', 'submit'],
            default => [],
        };
    }

    /** Array shape consumed by the header and footer views. */
    public function toNav(): array
    {
        return [
            'url' => $this->href(),
            'label' => $this->label(),
            'mega' => $this->type === 'mega' ? $this->target : null,
            'active' => $this->activePatterns(),
            'new_tab' => $this->new_tab,
            'current' => $this->type === 'page' && $this->page && request()->url() === $this->page->url(),
        ];
    }
}
