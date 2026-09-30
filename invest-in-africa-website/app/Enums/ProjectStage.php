<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProjectStage: string implements HasLabel
{
    use HasTranslatedLabel;

    case Idea = 'idea';
    case Prototype = 'prototype';
    case EarlyRevenue = 'early_revenue';
    case Growth = 'growth';
    case Expansion = 'expansion';

    public static function translationGroup(): string
    {
        return 'stage';
    }
}
