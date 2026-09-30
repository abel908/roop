<?php

namespace App\Filament\Resources\MediaDocuments\Pages;

use App\Filament\Resources\MediaDocuments\MediaDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMediaDocuments extends ListRecords
{
    protected static string $resource = MediaDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
