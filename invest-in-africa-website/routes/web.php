<?php

use App\Http\Controllers\Admin\DocumentDownloadController;
use App\Http\Controllers\CmsPageController;
use App\Http\Controllers\ConfirmationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\InterestController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RootController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SubmissionController;
use App\Support\Locales;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Technical routes
|--------------------------------------------------------------------------
*/
Route::get('/', RootController::class)->name('root');
Route::get('sitemap.xml', [SeoController::class, 'sitemapIndex'])->name('sitemap');
Route::get('sitemap-{locale}.xml', [SeoController::class, 'sitemap'])->whereIn('locale', Locales::codes())->name('sitemap.locale');
Route::get('robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('{key}.txt', [SeoController::class, 'indexNowKey'])->where('key', '[a-zA-Z0-9-]{8,128}')->name('indexnow.key');

// Preview of a draft page from the back-office (§8.1).
Route::get('preview/pages/{page}/{locale}', [CmsPageController::class, 'preview'])
    ->whereIn('locale', Locales::codes())
    ->middleware(['auth', 'signed'])
    ->name('pages.preview');

Route::get('admin/documents/{document}', DocumentDownloadController::class)
    ->middleware(['auth', 'signed'])
    ->name('admin.documents.download');

/*
|--------------------------------------------------------------------------
| Public site — one route set per language (/en/, /fr/, /zh/)
|--------------------------------------------------------------------------
| Structure from the table of contents transmitted by the institution
| (cahier des charges §3.1). Slugs are defined in lang/{locale}/routes.php:
| translated in French, Latin characters in Chinese (§7.4).
*/
foreach (Locales::codes() as $locale) {
    $slug = fn (string $key) => trans("routes.$key", [], $locale);

    Route::prefix($locale)
        ->name("$locale.")
        ->middleware("locale:$locale")
        ->group(function () use ($slug) {
            Route::get('/', [PageController::class, 'home'])->name('home');
            Route::get($slug('about'), [PageController::class, 'about'])->name('about');
            Route::get($slug('mission'), [PageController::class, 'mission'])->name('mission');

            Route::get($slug('what_we_do'), [DomainController::class, 'index'])->name('what-we-do');
            Route::get($slug('what_we_do').'/{domain}', [DomainController::class, 'show'])->name('domain');

            Route::get($slug('get_involved'), [PageController::class, 'getInvolved'])->name('get-involved');

            Route::get($slug('invest'), [ProjectController::class, 'index'])->name('invest');
            Route::get($slug('invest').'/{project}', [ProjectController::class, 'show'])->name('project');
            Route::post($slug('invest').'/{project}', [InterestController::class, 'store'])
                ->middleware(['throttle:forms', 'antispam'])->name('interest.store');

            Route::get($slug('submit'), [SubmissionController::class, 'create'])->name('submit');
            Route::post($slug('submit'), [SubmissionController::class, 'store'])
                ->middleware(['throttle:forms', 'antispam'])->name('submit.store');

            Route::get($slug('partners'), [PageController::class, 'partners'])->name('partners');

            Route::get($slug('contact'), [ContactController::class, 'create'])->name('contact');
            Route::post($slug('contact'), [ContactController::class, 'store'])
                ->middleware(['throttle:forms', 'antispam'])->name('contact.store');

            Route::get($slug('confirmation'), ConfirmationController::class)->name('confirmation');

            Route::get($slug('legal'), [PageController::class, 'legal'])->name('legal')->defaults('page', 'legal');
            Route::get($slug('privacy'), [PageController::class, 'legal'])->name('privacy')->defaults('page', 'privacy');
            Route::get($slug('cookies'), [PageController::class, 'legal'])->name('cookies')->defaults('page', 'cookies');
            Route::get($slug('terms'), [PageController::class, 'legal'])->name('terms')->defaults('page', 'terms');

            // Pages created in the back-office — registered last so fixed sections take precedence.
            Route::get('{page}', [CmsPageController::class, 'show'])->where('page', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('page');
        });
}
