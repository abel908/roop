<?php

namespace App\Filament\Resources\InterestExpressions\Pages;

use App\Filament\Resources\InterestExpressions\InterestExpressionResource;
use Filament\Resources\Pages\ListRecords;

class ListInterestExpressions extends ListRecords
{
    protected static string $resource = InterestExpressionResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
