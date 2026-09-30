<?php

use App\Http\Middleware\NoIndexOnStaging;
use App\Http\Middleware\ProtectAgainstSpam;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use App\Models\Redirect;
use App\Support\Locales;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'locale' => SetLocale::class,
            'antispam' => ProtectAgainstSpam::class,
        ]);

        $middleware->web(append: [
            SecurityHeaders::class,
            NoIndexOnStaging::class,
        ]);

        $middleware->encryptCookies(except: [SetLocale::COOKIE, 'cookie_consent']);
        $middleware->trustProxies(at: '*');
        $middleware->redirectGuestsTo(fn () => '/admin/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // 301 redirects managed from the back-office, then a trilingual 404 (§3.4).
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('admin/*', 'livewire/*')) {
                return null;
            }

            try {
                $redirect = Redirect::query()
                    ->where('is_active', true)
                    ->where('from_path', Redirect::normalize($request->getPathInfo()))
                    ->first();

                if ($redirect) {
                    $redirect->increment('hits');

                    return redirect($redirect->to_path, $redirect->status_code);
                }
            } catch (Throwable) {
                // Database unavailable: fall through to the 404 page.
            }

            $segment = $request->segment(1);
            $locale = Locales::isSupported($segment) ? $segment : (Locales::fromBrowser($request->header('Accept-Language')) ?? config('site.default_locale'));
            App::setLocale($locale);

            return response()->view('errors.404', ['locale' => $locale], 404);
        });
    })->create();
