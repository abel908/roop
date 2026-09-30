<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * "/" → the remembered language, otherwise the default language.
 * No forced redirect based on IP address (§7.3).
 */
class RootController
{
    public function __invoke(Request $request): RedirectResponse
    {
        $remembered = $request->cookie(SetLocale::COOKIE);
        $locale = Locales::isSupported($remembered) ? $remembered : config('site.default_locale');

        return redirect()->to(lroute('home', [], $locale), 302);
    }
}
