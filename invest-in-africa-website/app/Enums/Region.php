<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Region: string implements HasLabel
{
    use HasTranslatedLabel;

    case North = 'north';
    case West = 'west';
    case Central = 'central';
    case East = 'east';
    case Southern = 'southern';

    public static function translationGroup(): string
    {
        return 'region';
    }
}
