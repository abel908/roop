<?php

namespace App\Filament\Resources\MediaDocuments\Pages;

use App\Filament\Resources\MediaDocuments\MediaDocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMediaDocument extends EditRecord
{
    protected static string $resource = MediaDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
