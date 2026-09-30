<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FundingType: string implements HasLabel
{
    use HasTranslatedLabel;

    case Equity = 'equity';
    case Debt = 'debt';
    case Grant = 'grant';
    case Mixed = 'mixed';
    case Other = 'other';

    public static function translationGroup(): string
    {
        return 'funding_type';
    }
}
