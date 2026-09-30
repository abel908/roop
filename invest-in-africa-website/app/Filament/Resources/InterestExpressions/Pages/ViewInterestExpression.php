<?php

namespace App\Filament\Resources\InterestExpressions\Pages;

use App\Filament\Resources\InterestExpressions\InterestExpressionResource;
use App\Filament\Support\AnonymiseAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInterestExpression extends ViewRecord
{
    protected static string $resource = InterestExpressionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            AnonymiseAction::make(),
            EditAction::make(),
        ];
    }
}
