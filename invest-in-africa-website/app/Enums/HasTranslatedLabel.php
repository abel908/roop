<?php

namespace App\Enums;

/**
 * Labels are resolved from lang/{locale}/enums.php so every option is
 * translated in EN / FR / 中文 and editable from the back-office.
 */
trait HasTranslatedLabel
{
    abstract public static function translationGroup(): string;

    public function getLabel(): string
    {
        return __('enums.'.static::translationGroup().'.'.$this->value);
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getLabel();
        }

        return $options;
    }
}
