<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum InvestorType: string implements HasLabel
{
    use HasTranslatedLabel;

    case Individual = 'individual';
    case Fund = 'fund';
    case Institution = 'institution';
    case Company = 'company';
    case Diaspora = 'diaspora';

    public static function translationGroup(): string
    {
        return 'investor_type';
    }
}
