<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RequestStatus: string implements HasLabel
{
    use HasTranslatedLabel;

    case New = 'new';
    case InProgress = 'in_progress';
    case Closed = 'closed';

    public static function translationGroup(): string
    {
        return 'request_status';
    }
}
