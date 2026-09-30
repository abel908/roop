<?php

namespace App\Filament\Resources\ContentTranslations\Pages;

use App\Filament\Resources\ContentTranslations\ContentTranslationResource;
use App\Models\ContentTranslation;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListContentTranslations extends ListRecords
{
    protected static string $resource = ContentTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync')
                ->label(__('admin.actions.sync_texts'))
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function () {
                    $count = ContentTranslation::syncFromFiles();
                    Notification::make()->success()->title(__('admin.actions.synced', ['count' => $count]))->send();
                }),
        ];
    }
}
