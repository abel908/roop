<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/** Back-office available in French and English, per user (§8.4). */
class SetAdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->user()?->locale;

        App::setLocale(in_array($locale, ['fr', 'en'], true) ? $locale : 'fr');

        return $next($request);
    }
}
