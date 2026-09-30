<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use App\Filament\Support\RevisionHistoryAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Preview before publication (§8.1), in each language.
            ActionGroup::make(collect(['en' => 'English', 'fr' => 'Français', 'zh' => '中文'])->map(
                fn ($label, $locale) => Action::make("preview_$locale")->label($label)
                    ->url(fn () => PageResource::previewUrl($this->record, $locale), shouldOpenInNewTab: true)
            )->values()->all())->label(__('admin.preview'))->icon(Heroicon::OutlinedEye)->button()->color('gray'),
            RevisionHistoryAction::make(),
            DeleteAction::make(),
        ];
    }
}
