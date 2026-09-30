<?php

namespace App\Filament\Support;

use App\Services\DataRetention;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

/** Exercise of the right to erasure (§11.2): anonymises the personal data of a request. */
class AnonymiseAction
{
    public static function make(): Action
    {
        return Action::make('anonymise')
            ->label(__('admin.actions.anonymise'))
            ->icon(Heroicon::OutlinedEyeSlash)
            ->color('danger')
            ->requiresConfirmation()
            ->modalDescription(__('admin.actions.anonymise_help'))
            ->visible(fn (Model $record) => $record->full_name !== DataRetention::ANONYMISED)
            ->action(function (Model $record) {
                app(DataRetention::class)->anonymise($record);
                Notification::make()->success()->title(__('admin.actions.anonymised'))->send();
            });
    }
}
