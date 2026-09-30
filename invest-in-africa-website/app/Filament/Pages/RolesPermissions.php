<?php

namespace App\Filament\Pages;

use App\Enums\Role;
use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Roles and permissions (§8.3): the matrix remains configurable after launch.
 * Reserved to the Super Admin, whose own access can never be removed.
 */
class RolesPermissions extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?int $navigationSort = 2;

    protected function module(): string
    {
        return 'settings';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    protected function settingKeys(): array
    {
        return ['permissions'];
    }

    public function mount(): void
    {
        $values = [];

        foreach (Role::cases() as $role) {
            if ($role !== Role::SuperAdmin) {
                $values[$role->value] = $role->modules();
            }
        }

        $this->form->fill(['permissions' => $values]);
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.nav.administration');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.permissions.title');
    }

    public function getTitle(): string
    {
        return __('admin.permissions.title');
    }

    public function form(Schema $schema): Schema
    {
        $modules = collect(Role::MODULES)->mapWithKeys(fn ($m) => [$m => __("admin.permissions.modules.$m")])->all();

        return $schema->components([
            Section::make()->description(__('admin.permissions.help'))->columns(2)->schema(
                collect(Role::cases())
                    ->reject(fn (Role $role) => $role === Role::SuperAdmin)
                    ->map(fn (Role $role) => CheckboxList::make('permissions.'.$role->value)
                        ->label($role->getLabel())->options($modules)->columns(2)->bulkToggleable())
                    ->values()->all()
            ),
        ]);
    }
}
