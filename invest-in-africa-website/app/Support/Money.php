<?php

namespace App\Support;

use NumberFormatter;

class Money
{
    /** Full amount formatted according to the language (§7.2 "Formats"). */
    public static function format(float|int|string|null $amount, string $currency = 'USD', ?string $locale = null): string
    {
        if ($amount === null || $amount === '') {
            return '—';
        }

        $formatter = new NumberFormatter(Locales::icu($locale), NumberFormatter::CURRENCY);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        return $formatter->formatCurrency((float) $amount, $currency);
    }

    /**
     * Short form for cards: "USD 2.5M" (en), "2,5 M USD" (fr), "250万 USD" (zh).
     * PHP intl does not expose ICU compact notation, hence the manual scale.
     */
    public static function compact(float|int|string|null $amount, string $currency = 'USD', ?string $locale = null): string
    {
        if ($amount === null || $amount === '') {
            return '—';
        }

        $locale ??= Locales::current();
        $amount = (float) $amount;

        $scales = match ($locale) {
            'zh' => [[1e8, '亿'], [1e4, '万']],
            'fr' => [[1e9, ' Md'], [1e6, ' M'], [1e3, ' k']],
            default => [[1e9, 'B'], [1e6, 'M'], [1e3, 'K']],
        };

        $value = $amount;
        $suffix = '';

        foreach ($scales as [$divisor, $unit]) {
            if ($amount >= $divisor) {
                $value = $amount / $divisor;
                $suffix = $unit;
                break;
            }
        }

        $number = new NumberFormatter(Locales::icu($locale), NumberFormatter::DECIMAL);
        $number->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $value < 10 && $suffix !== '' ? 1 : 0);
        $formatted = $number->format($value).$suffix;

        return $locale === 'en' ? "$currency $formatted" : "$formatted $currency";
    }

    public static function number(float|int|null $value, ?string $locale = null): string
    {
        return (new NumberFormatter(Locales::icu($locale), NumberFormatter::DECIMAL))->format($value ?? 0);
    }
}
