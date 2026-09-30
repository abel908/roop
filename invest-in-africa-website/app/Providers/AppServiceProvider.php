<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\ContentTranslation;
use App\Models\Domain;
use App\Models\InterestExpression;
use App\Models\MediaDocument;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Redirect;
use App\Models\Sector;
use App\Models\SeoMeta;
use App\Models\Setting;
use App\Models\Submission;
use App\Models\User;
use App\Support\DatabaseTranslationLoader;
use App\Support\Locales;
use App\Support\Seo;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Texts of lang/*.php can be overridden from the back-office.
        $this->app->extend('translation.loader', fn ($loader) => new DatabaseTranslationLoader($loader));

        $this->app->scoped(Seo::class);
    }

    public function boot(): void
    {
        // Form submissions and login attempts are rate limited (§11.1).
        RateLimiter::for('forms', fn (Request $request) => [
            Limit::perMinute(5)->by($request->ip()),
            Limit::perDay(40)->by($request->ip()),
        ]);

        // Public project URLs use the reference; only published projects are reachable.
        Route::bind('project', fn (string $value) => Project::published()->where('reference', strtoupper($value))->firstOrFail());

        $this->registerActivityLog();

        Password::defaults(fn () => Password::min(12)->mixedCase()->numbers()->symbols());

        View::composer(['layouts.*', 'pages.*', 'projects.*', 'submissions.*', 'partials.*', 'errors.*', 'components.*'], function ($view) {
            $view->with([
                'seo' => app(Seo::class),
                'currentLocale' => Locales::current(),
                'settings' => Setting::allCached(),
            ]);
        });

        View::composer(['partials.header', 'partials.footer'], function ($view) {
            $view->with('navDomains', once(fn () => Domain::published()->get()));
        });
    }

    /** Activity log (§8.4, §11.1): logins, failed attempts, back-office changes. */
    private function registerActivityLog(): void
    {
        Event::listen(Login::class, function (Login $event) {
            $event->user->forceFill(['last_login_at' => now()])->saveQuietly();
            ActivityLog::record('auth.login', $event->user);
        });

        Event::listen(Logout::class, fn (Logout $event) => $event->user && ActivityLog::record('auth.logout', $event->user));

        Event::listen(Failed::class, fn (Failed $event) => ActivityLog::record('auth.failed', null, [
            'email' => $event->credentials['email'] ?? null,
        ]));

        $audited = [
            Project::class, Submission::class, InterestExpression::class,
            ContactMessage::class, Partner::class, Domain::class,
            Sector::class, MediaDocument::class, ContentTranslation::class,
            SeoMeta::class, Redirect::class, User::class,
        ];

        foreach ($audited as $model) {
            foreach (['created', 'updated', 'deleted'] as $event) {
                $model::$event(function ($record) use ($event) {
                    if (! auth()->check()) {
                        return;
                    }

                    $changes = $event === 'updated'
                        ? array_diff_key($record->getChanges(), array_flip(['updated_at', 'password', 'remember_token', 'app_authentication_secret', 'app_authentication_recovery_codes', 'last_login_at']))
                        : [];

                    if ($event === 'updated' && ! $changes) {
                        return;
                    }

                    ActivityLog::record(strtolower(class_basename($record)).'.'.$event, $record, $changes ? ['fields' => implode(', ', array_keys($changes))] : []);
                });
            }
        }
    }
}
