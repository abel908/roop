<?php

namespace App\Support;

use Illuminate\Support\Facades\App;

class Locales
{
    /** @return array<int, string> */
    public static function codes(): array
    {
        return array_keys(config('site.locales'));
    }

    public static function isSupported(?string $locale): bool
    {
        return $locale !== null && array_key_exists($locale, config('site.locales'));
    }

    public static function current(): string
    {
        $locale = App::getLocale();

        return self::isSupported($locale) ? $locale : config('site.default_locale');
    }

    public static function label(string $locale): string
    {
        return config("site.locales.$locale.label", $locale);
    }

    public static function hreflang(?string $locale = null): string
    {
        return config('site.locales.'.($locale ?? self::current()).'.hreflang', 'en');
    }

    public static function icu(?string $locale = null): string
    {
        return config('site.locales.'.($locale ?? self::current()).'.icu', 'en');
    }

    public static function og(?string $locale = null): string
    {
        return config('site.locales.'.($locale ?? self::current()).'.og', 'en_US');
    }

    /**
     * Best supported language from the Accept-Language header — used only
     * for a discreet suggestion, never for a forced redirect (§7.3).
     */
    public static function fromBrowser(?string $header): ?string
    {
        if (! $header) {
            return null;
        }

        foreach (explode(',', $header) as $part) {
            $code = strtolower(substr(trim(explode(';', $part)[0]), 0, 2));

            if (self::isSupported($code)) {
                return $code;
            }
        }

        return null;
    }
}
