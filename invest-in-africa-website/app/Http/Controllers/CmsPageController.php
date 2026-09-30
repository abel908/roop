<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\Locales;
use App\Support\Seo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;

/** Pages composed of blocks in the back-office (§8.1). */
class CmsPageController
{
    public function __construct(private readonly Seo $seo) {}

    public function show(string $page): View|RedirectResponse
    {
        $locale = Locales::current();
        $model = Page::findBySlug($page, $locale);

        abort_if(! $model, 404);

        if ($model->tr('slug', $locale) !== $page) {
            return redirect()->to($model->url($locale), 301);
        }

        return $this->render($model, $locale);
    }

    /** Draft preview through a temporary signed link. */
    public function preview(Page $page, string $locale): View
    {
        abort_unless(auth()->user()?->canManage('pages'), 403);

        App::setLocale($locale);
        $this->seo->noindex = true;

        return $this->render($page, $locale, preview: true);
    }

    private function render(Page $page, string $locale, bool $preview = false): View
    {
        $this->seo->title($page->tr('seo_title', $locale, false) ?: $page->tr('title', $locale))
            ->description($page->tr('seo_description', $locale) ?: null)
            ->image($page->og_image ? asset('storage/'.$page->og_image) : null)
            ->alternates(collect(Locales::codes())->mapWithKeys(fn ($l) => [$l => $page->url($l)])->all())
            ->crumb($page->tr('title', $locale));

        return view('pages.cms', [
            'page' => $page,
            'blocks' => collect($page->blocks ?? []),
            'preview' => $preview,
        ]);
    }
}
