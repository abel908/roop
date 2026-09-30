<?php

use App\Support\Locales;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

if (! function_exists('lroute')) {
    /**
     * URL of a named page in a given language: lroute('about') → /fr/a-propos.
     */
    function lroute(string $name, mixed $parameters = [], ?string $locale = null, bool $absolute = true): string
    {
        $locale ??= Locales::current();
        $routeName = $locale.'.'.$name;

        if (! Route::has($routeName)) {
            return route($locale.'.home', [], $absolute);
        }

        return route($routeName, $parameters, $absolute);
    }
}

if (! function_exists('current_page')) {
    /** Route name without its language prefix: "fr.about" → "about". */
    function current_page(): ?string
    {
        $name = Route::currentRouteName();

        if (! $name) {
            return null;
        }

        $parts = explode('.', $name, 2);

        return Locales::isSupported($parts[0]) && isset($parts[1]) ? $parts[1] : $name;
    }
}

if (! function_exists('is_page')) {
    function is_page(string ...$patterns): bool
    {
        $page = current_page();

        foreach ($patterns as $pattern) {
            if ($page && Str::is($pattern, $page)) {
                return true;
            }
        }

        return false;
    }
}
