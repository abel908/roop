<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SubmissionStatus: string implements HasColor, HasLabel
{
    use HasTranslatedLabel;

    case Received = 'received';
    case UnderReview = 'under_review';
    case InfoRequested = 'info_requested';
    case Shortlisted = 'shortlisted';
    case Published = 'published';
    case Declined = 'declined';
    case Archived = 'archived';

    public static function translationGroup(): string
    {
        return 'submission_status';
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Received => 'info',
            self::UnderReview, self::InfoRequested => 'warning',
            self::Shortlisted, self::Published => 'success',
            self::Declined => 'danger',
            self::Archived => 'gray',
        };
    }
}
