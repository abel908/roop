<?php

namespace App\Filament\Resources\Users;

use App\Enums\Role;
use App\Filament\Concerns\HasModulePermission;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Password;
use UnitEnum;

/** Users module (§8.2, §8.3): accounts, roles, two-factor authentication status. */
class UserResource extends Resource
{
    use HasModulePermission;

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function module(): string
    {
        return 'users';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.nav.administration');
    }

    public static function getModelLabel(): string
    {
        return __('admin.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.users');
    }

    /** Only a Super Admin can grant or modify the Super Admin role. */
    protected static function roleOptions(): array
    {
        $options = Role::options();

        if (! auth()->user()?->isSuperAdmin()) {
            unset($options[Role::SuperAdmin->value]);
        }

        return $options;
    }

    public static function canEdit(Model $record): bool
    {
        return parent::canEdit($record) && ($record->role !== Role::SuperAdmin || auth()->user()->isSuperAdmin());
    }

    public static function canDelete(Model $record): bool
    {
        return static::canEdit($record) && $record->id !== auth()->id();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->columns(2)->schema([
                TextInput::make('name')->label(__('admin.fields.name'))->required()->maxLength(120),
                TextInput::make('email')->label(__('forms.labels.email'))->email()->required()->unique(ignoreRecord: true),
                Select::make('role')->label(__('admin.fields.role'))->options(fn () => static::roleOptions())->required(),
                Select::make('locale')->label(__('admin.fields.admin_language'))->options(['fr' => 'Français', 'en' => 'English'])->default('fr')->required(),
                TextInput::make('password')->label(__('admin.fields.password'))->password()->revealable()
                    ->rule(Password::defaults())
                    ->required(fn (string $operation) => $operation === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->helperText(__('admin.help.password')),
                Toggle::make('is_active')->label(__('admin.fields.is_active'))->default(true)->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('admin.fields.name'))->searchable()->weight('bold')->description(fn (User $r) => $r->email),
                TextColumn::make('role')->label(__('admin.fields.role'))->badge(),
                IconColumn::make('app_authentication_secret')->label('2FA')->boolean()->state(fn (User $r) => filled($r->app_authentication_secret)),
                IconColumn::make('is_active')->label(__('admin.fields.is_active'))->boolean(),
                TextColumn::make('last_login_at')->label(__('admin.fields.last_login'))->dateTime('d/m/Y H:i')->placeholder('—'),
            ])
            ->filters([SelectFilter::make('role')->label(__('admin.fields.role'))->options(Role::class)])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
