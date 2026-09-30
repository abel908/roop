<?php

namespace App\Support;

use Collator;
use ResourceBundle;

/**
 * Country names in EN / FR / 中文 from the ICU data shipped with PHP intl,
 * so no list has to be translated or maintained by hand.
 */
class Countries
{
    /** Codes in the ICU region bundle that are not countries. */
    private const EXCLUDED = ['EU', 'EZ', 'UN', 'ZZ', 'QO', 'XA', 'XB', 'AC', 'CP', 'DG', 'EA', 'IC', 'TA', 'CQ'];

    /** @var array<string, array<string, string>> */
    private static array $cache = [];

    /** @return array<string, string> code => name, sorted for the locale */
    public static function all(?string $locale = null): array
    {
        $icu = Locales::icu($locale);

        if (! isset(self::$cache[$icu])) {
            $names = [];
            $bundle = ResourceBundle::create($icu, 'ICUDATA-region')['Countries'];

            foreach ($bundle as $code => $name) {
                if (preg_match('/^[A-Z]{2}$/', $code) && ! in_array($code, self::EXCLUDED, true)) {
                    $names[$code] = $name;
                }
            }

            (new Collator($icu))->asort($names);
            self::$cache[$icu] = $names;
        }

        return self::$cache[$icu];
    }

    /** @return array<string, string> */
    public static function african(?string $locale = null): array
    {
        return array_intersect_key(self::all($locale), array_flip(self::africanCodes()));
    }

    /** @return array<int, string> */
    public static function africanCodes(): array
    {
        return array_merge(...array_values(config('site.africa')));
    }

    public static function name(?string $code, ?string $locale = null): string
    {
        if (! $code) {
            return '';
        }

        return self::all($locale)[strtoupper($code)] ?? strtoupper($code);
    }

    public static function regionOf(string $code): ?string
    {
        foreach (config('site.africa') as $region => $codes) {
            if (in_array(strtoupper($code), $codes, true)) {
                return $region;
            }
        }

        return null;
    }
}
