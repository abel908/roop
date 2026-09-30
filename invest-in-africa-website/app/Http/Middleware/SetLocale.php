<?php

namespace App\Http\Middleware;

use App\Support\Locales;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the language of the /en/, /fr/ or /zh/ directory and remembers
 * the visitor's choice for the next visits (§7.3).
 */
class SetLocale
{
    public const COOKIE = 'site_locale';

    public function handle(Request $request, Closure $next, string $locale): Response
    {
        App::setLocale($locale);
        Carbon::setLocale(Locales::icu($locale));

        if ($request->cookie(self::COOKIE) !== $locale) {
            Cookie::queue(self::COOKIE, $locale, 60 * 24 * 365, null, null, null, true, false, 'Lax');
        }

        return $next($request);
    }
}
