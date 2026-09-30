<?php

namespace App\Models\Concerns;

use App\Jobs\TranslateMissingLanguages;
use App\Services\Translator;

/**
 * After every save, missing languages are completed automatically in the
 * queue (EN / FR / 中文). Texts already written are never replaced.
 */
trait AutoTranslates
{
    public static function bootAutoTranslates(): void
    {
        static::saved(function (self $model) {
            if (! Translator::enabled() || ! $model->hasMissingTranslations()) {
                return;
            }

            TranslateMissingLanguages::dispatch($model::class, $model->getKey())->afterCommit();
        });
    }

    /** @return array<int, string> */
    public function autoTranslatedAttributes(): array
    {
        $attributes = property_exists($this, 'autoTranslated') ? $this->autoTranslated : ($this->translatable ?? []);

        return array_values(array_diff($attributes, ['slug']));
    }

    public function hasMissingTranslations(): bool
    {
        $found = false;
        $walk = function ($value) use (&$walk, &$found) {
            if ($found || ! is_array($value)) {
                return;
            }

            if (Translator::isLocaleMap($value)) {
                $filled = array_filter($value, fn ($v) => filled($v));
                $found = $filled && count($filled) < count(Translator::LOCALES);

                return;
            }

            array_walk($value, $walk);
        };

        foreach ($this->autoTranslatedAttributes() as $attribute) {
            $walk($this->getAttribute($attribute));
        }

        return $found || (method_exists($this, 'completeSlugs') && count(array_filter((array) $this->slug)) < 3);
    }
}
