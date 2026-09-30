<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PartnerCategory: string implements HasLabel
{
    use HasTranslatedLabel;

    case Strategic = 'strategic';
    case Institutional = 'institutional';
    case Financial = 'financial';
    case Technical = 'technical';
    case Media = 'media';

    public static function translationGroup(): string
    {
        return 'partner_category';
    }
}
