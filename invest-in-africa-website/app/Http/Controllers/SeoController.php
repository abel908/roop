<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Page;
use App\Models\Project;
use App\Services\IndexNow;
use App\Support\Locales;
use Illuminate\Http\Response;

/** International sitemaps with hreflang annotations, robots.txt (§10.1). */
class SeoController
{
    private const STATIC_PAGES = [
        'home' => '1.0', 'about' => '0.8', 'mission' => '0.8', 'what-we-do' => '0.9',
        'get-involved' => '0.9', 'invest' => '0.9', 'submit' => '0.8', 'partners' => '0.7',
        'contact' => '0.7', 'legal' => '0.2', 'privacy' => '0.2', 'cookies' => '0.2', 'terms' => '0.2',
    ];

    public function sitemapIndex(): Response
    {
        $sitemaps = collect(Locales::codes())->map(fn ($locale) => route('sitemap.locale', $locale));

        return response()
            ->view('seo.sitemap-index', ['sitemaps' => $sitemaps, 'lastmod' => $this->lastModified()])
            ->header('Content-Type', 'application/xml');
    }

    public function sitemap(string $locale): Response
    {
        $entries = [];

        foreach (self::STATIC_PAGES as $page => $priority) {
            $entries[] = [
                'loc' => lroute($page, [], $locale),
                'alternates' => $this->alternates(fn ($l) => lroute($page, [], $l)),
                'priority' => $priority,
            ];
        }

        foreach (Domain::published()->get() as $domain) {
            $entries[] = [
                'loc' => $domain->url($locale),
                'alternates' => $this->alternates(fn ($l) => $domain->url($l)),
                'priority' => '0.8',
                'lastmod' => $domain->updated_at,
            ];
        }

        foreach (Page::query()->live()->get() as $page) {
            $entries[] = [
                'loc' => $page->url($locale),
                'alternates' => $this->alternates(fn ($l) => $page->url($l)),
                'priority' => '0.6',
                'lastmod' => $page->updated_at,
            ];
        }

        foreach (Project::published()->get() as $project) {
            if (! $project->isTranslatedIn($locale, ['title', 'summary'])) {
                continue;
            }

            $entries[] = [
                'loc' => $project->url($locale),
                'alternates' => $this->alternates(fn ($l) => $project->url($l), fn ($l) => $project->isTranslatedIn($l, ['title', 'summary'])),
                'priority' => '0.7',
                'lastmod' => $project->updated_at,
            ];
        }

        return response()
            ->view('seo.sitemap', ['entries' => $entries])
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $lines = app()->isProduction()
            ? ['User-agent: *', 'Disallow: /admin', 'Disallow: /livewire', 'Disallow: /*?*sort=', '', 'Sitemap: '.route('sitemap')]
            : ['User-agent: *', 'Disallow: /'];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain']);
    }

    /** IndexNow key file proving ownership of the host. */
    public function indexNowKey(string $key): Response
    {
        abort_unless(hash_equals(IndexNow::key(), $key), 404);

        return response($key, 200, ['Content-Type' => 'text/plain']);
    }

    /** @return array<string, string> */
    private function alternates(callable $url, ?callable $available = null): array
    {
        $alternates = [];

        foreach (Locales::codes() as $locale) {
            if ($available === null || $available($locale)) {
                $alternates[Locales::hreflang($locale)] = $url($locale);
            }
        }

        $alternates['x-default'] = $url(config('site.default_locale'));

        return $alternates;
    }

    private function lastModified(): ?string
    {
        return Project::published()->max('updated_at');
    }
}
