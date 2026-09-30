<?php

namespace App\Filament\Resources\ContentTranslations\Pages;

use App\Filament\Resources\ContentTranslations\ContentTranslationResource;
use Filament\Resources\Pages\EditRecord;

class EditContentTranslation extends EditRecord
{
    protected static string $resource = ContentTranslationResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        foreach (array_keys(ContentTranslationResource::LOCALES) as $locale) {
            $data[$locale] = filled($data[$locale] ?? null) ? $data[$locale] : null;
        }

        $data['updated_by'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
