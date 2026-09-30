<?php

namespace App\Support;

use App\Models\SeoMeta;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Per-request SEO state (§10.1): title, description, canonical, hreflang
 * alternates, Open Graph image, breadcrumbs and JSON-LD.
 */
class Seo
{
    public ?string $title = null;

    public ?string $description = null;

    public ?string $image = null;

    public bool $noindex = false;

    /** @var array<string, string> locale => absolute URL */
    public array $alternates = [];

    /** @var array<int, array{label: string, url: string}> */
    public array $breadcrumbs = [];

    /** @var array<int, array<string, mixed>> */
    public array $schemas = [];

    /** Load defaults for a page key, then back-office overrides (SEO module). */
    public function page(string $key, array $replace = []): static
    {
        $this->title = __("seo.$key.title", $replace);
        $this->description = __("seo.$key.description", $replace);

        try {
            $meta = SeoMeta::query()->where('page_key', $key)->first();
        } catch (\Throwable) {
            $meta = null;
        }

        if ($meta) {
            $this->title = $meta->tr('title', null, false) ?: $this->title;
            $this->description = $meta->tr('description', null, false) ?: $this->description;
            $this->image = $meta->og_image ? asset('storage/'.$meta->og_image) : $this->image;
            $this->noindex = $meta->noindex;
        }

        return $this;
    }

    public function title(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function description(?string $description): static
    {
        $this->description = $description ? Str::limit(strip_tags($description), 158) : null;

        return $this;
    }

    public function image(?string $url): static
    {
        $this->image = $url;

        return $this;
    }

    /** @param array<string, string> $alternates */
    public function alternates(array $alternates): static
    {
        $this->alternates = $alternates;

        return $this;
    }

    public function crumb(string $label, ?string $url = null): static
    {
        $this->breadcrumbs[] = ['label' => $label, 'url' => $url ?? url()->current()];

        return $this;
    }

    public function schema(array $schema): static
    {
        $this->schemas[] = $schema;

        return $this;
    }

    public function fullTitle(): string
    {
        $site = config('site.name');

        if (! $this->title || $this->title === $site) {
            return $site;
        }

        return $this->title.' | '.$site;
    }

    public function canonical(): string
    {
        return url()->current();
    }

    /**
     * Equivalent URLs in the other languages: controller-provided alternates
     * first, otherwise the same named route with the same parameters.
     *
     * @return array<string, string>
     */
    public function resolvedAlternates(): array
    {
        if ($this->alternates) {
            return $this->alternates;
        }

        $page = current_page();
        $route = Route::current();

        if (! $page || ! $route) {
            return collect(Locales::codes())->mapWithKeys(fn ($l) => [$l => lroute('home', [], $l)])->all();
        }

        $alternates = [];

        foreach (Locales::codes() as $locale) {
            $alternates[$locale] = Route::has("$locale.$page")
                ? route("$locale.$page", $route->parameters())
                : lroute('home', [], $locale);
        }

        return $alternates;
    }

    /** @return array<string, mixed> */
    public function breadcrumbSchema(): ?array
    {
        if (! $this->breadcrumbs) {
            return null;
        }

        $items = array_merge([['label' => __('site.nav.home'), 'url' => lroute('home')]], $this->breadcrumbs);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn ($item, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['label'],
                'item' => $item['url'],
            ])->all(),
        ];
    }
}
