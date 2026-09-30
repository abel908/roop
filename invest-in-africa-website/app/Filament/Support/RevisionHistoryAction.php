<?php

namespace App\Filament\Support;

use App\Models\Revision;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

/**
 * "History" header action: lists the previous versions of a record and
 * restores the selected one (§8.1 "Historique des versions et restauration").
 */
class RevisionHistoryAction
{
    public static function make(): Action
    {
        return Action::make('history')
            ->label(__('admin.revisions.history'))
            ->icon(Heroicon::OutlinedClock)
            ->color('gray')
            ->badge(fn (Model $record) => $record->revisions()->count() ?: null)
            ->modalHeading(__('admin.revisions.history'))
            ->modalDescription(__('admin.revisions.help'))
            ->modalSubmitActionLabel(__('admin.revisions.restore'))
            ->schema([
                Select::make('revision')
                    ->label(__('admin.revisions.version'))
                    ->required()
                    ->options(fn (Model $record) => $record->revisions()->with('user')->get()->mapWithKeys(fn (Revision $revision) => [
                        $revision->id => $revision->created_at->format('d/m/Y H:i').' — '.($revision->user?->name ?? '—'),
                    ])),
            ])
            ->action(function (array $data, Model $record, $livewire) {
                $revision = $record->revisions()->findOrFail($data['revision']);
                $record->restoreRevision($revision);

                Notification::make()->success()->title(__('admin.revisions.restored'))->send();

                $livewire->redirect($livewire->getResource()::getUrl('edit', ['record' => $record]));
            })
            ->visible(fn (Model $record) => $record->revisions()->exists());
    }
}
