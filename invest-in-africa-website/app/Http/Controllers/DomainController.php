<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\MediaDocument;
use App\Models\Project;
use App\Support\Locales;
use App\Support\Seo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DomainController
{
    public function __construct(private readonly Seo $seo) {}

    public function index(): View
    {
        $this->seo->page('what_we_do')->crumb(__('site.nav.what_we_do'));

        return view('pages.what-we-do', ['domains' => Domain::published()->get()]);
    }

    public function show(string $domain): View|RedirectResponse
    {
        $locale = Locales::current();
        $model = Domain::findBySlug($domain, $locale);

        abort_if(! $model, 404);

        // Canonical slug of the current language.
        if ($model->tr('slug', $locale) !== $domain) {
            return redirect()->to($model->url($locale), 301);
        }

        $this->seo->title($model->tr('title'))
            ->description($model->tr('tagline') ?: $model->tr('intro'))
            ->alternates(collect(Locales::codes())->mapWithKeys(fn ($l) => [$l => $model->url($l)])->all())
            ->crumb(__('site.nav.what_we_do'), lroute('what-we-do'))
            ->crumb($model->tr('title'));

        $all = Domain::published()->get();

        return view('pages.domain', [
            'domain' => $model,
            'others' => $all->where('id', '!=', $model->id),
            'previous' => $all->where('number', '<', $model->number)->last(),
            'next' => $all->where('number', '>', $model->number)->first(),
            'projects' => Project::published()->where('domain_id', $model->id)->with('sector')->latest('published_at')->take(3)->get(),
            'documents' => MediaDocument::published()->where('domain_id', $model->id)->get(),
        ]);
    }
}
