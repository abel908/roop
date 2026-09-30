<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ContactSubject: string implements HasLabel
{
    use HasTranslatedLabel;

    case Investment = 'investment';
    case Project = 'project';
    case Partnership = 'partnership';
    case Press = 'press';
    case Other = 'other';

    public static function translationGroup(): string
    {
        return 'contact_subject';
    }
}
