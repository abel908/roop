<?php

namespace App\Http\Controllers;

use App\Enums\PartnerCategory;
use App\Models\Domain;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Setting;
use App\Services\DataRetention;
use App\Support\Seo;
use Illuminate\Contracts\View\View;

class PageController
{
    public function __construct(private readonly Seo $seo) {}

    public function home(): View
    {
        $this->seo->page('home');
        $this->seo->schema([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => config('site.name'),
            'url' => lroute('home'),
            'inLanguage' => ['en', 'fr', 'zh-Hans'],
        ]);

        return view('pages.home', [
            'domains' => Domain::published()->get(),
            'featuredProjects' => Project::published()->where('is_featured', true)->with('sector')->latest('published_at')->take(6)->get(),
            'projectCount' => Project::published()->count(),
            'partners' => Partner::published()->where('is_featured', true)->get(),
            'figures' => Setting::get('impact.figures', []),
            'mapCountries' => Project::published()->selectRaw('country, count(*) as total')->groupBy('country')->pluck('total', 'country')->all(),
        ]);
    }

    public function about(): View
    {
        $this->seo->page('about')->crumb(__('site.nav.about'));

        return view('pages.about', [
            'timeline' => Setting::get('about.timeline', []),
            'leaders' => Setting::get('about.leaders', []),
            'figures' => Setting::get('impact.figures', []),
        ]);
    }

    public function mission(): View
    {
        $this->seo->page('mission')->crumb(__('site.nav.mission'));

        return view('pages.mission', [
            'figures' => Setting::get('impact.figures', []),
            'domains' => Domain::published()->get(),
        ]);
    }

    public function getInvolved(): View
    {
        $this->seo->page('get_involved')->crumb(__('site.nav.get_involved'));

        return view('pages.get-involved', [
            'projectCount' => Project::published()->count(),
        ]);
    }

    public function partners(): View
    {
        $this->seo->page('partners')->crumb(__('site.nav.partners'));

        $partners = Partner::published()->get();

        return view('pages.partners', [
            'partners' => $partners,
            'categories' => collect(PartnerCategory::cases())
                ->filter(fn ($category) => $partners->contains('category', $category))
                ->values(),
        ]);
    }

    public function legal(string $page): View
    {
        $this->seo->page($page)->crumb(__("legal.$page.title"));

        return view('pages.legal', ['page' => $page, 'replace' => $this->legalReplacements()]);
    }

    /**
     * Values of the legal pages, taken from the site settings so the texts
     * are always complete and up to date (§3.4).
     *
     * @return array<string, string>
     */
    private function legalReplacements(): array
    {
        $contact = Setting::get('contact', []);
        $legal = Setting::get('legal', []);
        $name = e(config('site.name'));

        $publisher = collect([$name, e($legal['registration'] ?? ''), e(str_replace("\n", ', ', trim($contact['address'] ?? '')))])
            ->filter()->implode(', ');

        $channels = collect([
            ! empty($contact['email']) ? '<a href="mailto:'.e($contact['email']).'">'.e($contact['email']).'</a>' : null,
            ! empty($contact['phone']) ? '<a href="tel:'.e(preg_replace('/[^0-9+]/', '', $contact['phone'])).'">'.e($contact['phone']).'</a>' : null,
            '<a href="'.e(lroute('contact')).'">'.e(__('site.nav.contact')).'</a>',
        ])->filter()->implode(' · ');

        $retention = collect(DataRetention::DEFAULTS)->except('activity_logs')
            ->map(fn ($default, $type) => __("legal_retention.$type", ['months' => DataRetention::months($type)]))
            ->implode(' ; ');

        return [
            'publisher' => $publisher,
            'director' => e($legal['director'] ?? '') ?: __('legal_retention.director_default', ['name' => $name]),
            'contact' => $channels,
            'host' => nl2br(e($legal['host'] ?? '')) ?: __('legal_retention.host_default', ['name' => $name]),
            'retention' => $retention,
        ];
    }
}
