<?php

namespace App\Models\Concerns;

use App\Support\Locales;

/**
 * Translatable attributes stored as JSON: {"en": "...", "fr": "...", "zh": "..."}.
 * Each language is independent (§7.2) — no automatic translation is ever published.
 *
 * @property array<int, string> $translatable
 */
trait HasTranslations
{
    public function initializeHasTranslations(): void
    {
        foreach ($this->translatable as $attribute) {
            $this->mergeCasts([$attribute => 'array']);
        }
    }

    /**
     * Value in the requested (or current) language, falling back to English
     * so a page never renders empty while a translation is pending.
     */
    public function tr(string $attribute, ?string $locale = null, bool $fallback = true): mixed
    {
        $locale ??= Locales::current();
        $values = $this->getAttribute($attribute) ?? [];

        $value = $values[$locale] ?? null;

        if ($this->isBlank($value) && $fallback) {
            $value = $values[config('site.default_locale')] ?? null;
        }

        return $value;
    }

    public function isTranslatedIn(string $locale, ?array $attributes = null): bool
    {
        foreach ($attributes ?? [$this->translatable[0]] as $attribute) {
            if ($this->isBlank(($this->getAttribute($attribute) ?? [])[$locale] ?? null)) {
                return false;
            }
        }

        return true;
    }

    /** @return array<string, bool> translation status per language, for the CMS */
    public function translationStatus(): array
    {
        $status = [];

        foreach (Locales::codes() as $locale) {
            $status[$locale] = $this->isTranslatedIn($locale);
        }

        return $status;
    }

    /** Store 中文 unescaped so keyword search works in every language. */
    protected function asJson($value, $flags = 0)
    {
        return parent::asJson($value, $flags | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function isBlank(mixed $value): bool
    {
        return $value === null || $value === '' || $value === [];
    }
}
