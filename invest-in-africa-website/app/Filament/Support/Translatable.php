<?php

namespace App\Filament\Support;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;

/**
 * EN / FR / 中文 versions side by side for every translatable field (§8.1).
 * English is required; the other languages show their translation status.
 */
class Translatable
{
    public const LOCALES = ['en' => 'English', 'fr' => 'Français', 'zh' => '中文'];

    public static function text(string $name, string $label, bool $required = false, ?int $maxLength = 255): Fieldset
    {
        return self::make($name, $label, fn (string $path) => TextInput::make($path)->maxLength($maxLength), $required);
    }

    public static function textarea(string $name, string $label, bool $required = false, int $rows = 4): Fieldset
    {
        return self::make($name, $label, fn (string $path) => Textarea::make($path)->rows($rows), $required);
    }

    public static function rich(string $name, string $label, bool $required = false): Fieldset
    {
        return self::make($name, $label, fn (string $path) => RichEditor::make($path)
            ->toolbarButtons([['bold', 'italic', 'link'], ['h3', 'bulletList', 'orderedList'], ['undo', 'redo']]), $required);
    }

    public static function list(string $name, string $label): Fieldset
    {
        return self::make($name, $label, fn (string $path) => TagsInput::make($path)->reorderable()->splitKeys(['Enter']), false);
    }

    /** @param Closure(string): Field $factory */
    public static function make(string $name, string $label, Closure $factory, bool $required = false): Fieldset
    {
        $fields = [];

        foreach (self::LOCALES as $locale => $language) {
            $field = $factory("$name.$locale")->label($language);

            if ($locale === 'en' && $required) {
                $field->required();
            }

            if ($locale !== 'en') {
                $field->hint(fn ($state) => blank($state) ? __('admin.translation_missing') : null)->hintColor('warning');
            }

            $fields[] = $field;
        }

        return Fieldset::make($label)->schema($fields)->columns(['default' => 1, 'lg' => 3]);
    }
}
