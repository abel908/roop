<?php

namespace App\Filament\Resources\InterestExpressions\Pages;

use App\Filament\Resources\InterestExpressions\InterestExpressionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditInterestExpression extends EditRecord
{
    protected static string $resource = InterestExpressionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
