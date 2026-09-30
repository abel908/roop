<?php

namespace App\Filament\Resources\InterestExpressions\Pages;

use App\Filament\Resources\InterestExpressions\InterestExpressionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInterestExpression extends ViewRecord
{
    protected static string $resource = InterestExpressionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
