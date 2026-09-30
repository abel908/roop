<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProjectStatus: string implements HasColor, HasLabel
{
    use HasTranslatedLabel;

    case Open = 'open';
    case Funding = 'funding';
    case Funded = 'funded';
    case Closed = 'closed';

    public static function translationGroup(): string
    {
        return 'project_status';
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Open => 'success',
            self::Funding => 'warning',
            self::Funded => 'info',
            self::Closed => 'gray',
        };
    }
}
