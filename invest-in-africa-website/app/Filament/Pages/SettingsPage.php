<?php

namespace App\Filament\Pages;

use App\Models\ActivityLog;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;

/**
 * Base for back-office pages editing groups of settings stored in the
 * `settings` table (one row per top-level key).
 */
abstract class SettingsPage extends Page
{
    /** @var array<string, mixed> */
    public ?array $data = [];

    /** @return array<int, string> top-level setting keys edited by the page */
    abstract protected function settingKeys(): array;

    abstract protected function module(): string;

    public static function canAccess(): bool
    {
        return auth()->user()?->canManage((new static)->module()) ?? false;
    }

    public function mount(): void
    {
        $values = [];

        foreach ($this->settingKeys() as $key) {
            $values[$key] = Setting::get($key, []);
        }

        $this->form->fill($values);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))->submit('save')->keyBindings(['mod+s']),
                    ])->sticky(),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($this->settingKeys() as $key) {
            Setting::put($key, $data[$key] ?? []);
        }

        ActivityLog::record('settings.updated', null, ['keys' => implode(', ', $this->settingKeys())]);

        Notification::make()->success()->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))->send();
    }
}
