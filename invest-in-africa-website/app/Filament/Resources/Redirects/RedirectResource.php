<?php

namespace App\Filament\Resources\Redirects;

use App\Filament\Concerns\HasModulePermission;
use App\Filament\Resources\Redirects\Pages\CreateRedirect;
use App\Filament\Resources\Redirects\Pages\EditRedirect;
use App\Filament\Resources\Redirects\Pages\ListRedirects;
use App\Models\Redirect;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

/** 301 redirects managed from the back-office (§10.1). */
class RedirectResource extends Resource
{
    use HasModulePermission;

    protected static ?string $model = Redirect::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnRight;

    protected static ?int $navigationSort = 2;

    public static function module(): string
    {
        return 'seo';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.nav.seo');
    }

    public static function getModelLabel(): string
    {
        return __('admin.redirect');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.redirects');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->columns(2)->schema([
                TextInput::make('from_path')->label(__('admin.fields.from_path'))->required()->placeholder('/old-page')->unique(ignoreRecord: true),
                TextInput::make('to_path')->label(__('admin.fields.to_path'))->required()->placeholder('/en/about-us'),
                Select::make('status_code')->label('Code')->options([301 => '301 — permanent', 302 => '302 — temporary'])->default(301),
                Toggle::make('is_active')->label(__('admin.fields.is_active'))->default(true)->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('from_path')->label(__('admin.fields.from_path'))->fontFamily('mono')->searchable(),
                TextColumn::make('to_path')->label(__('admin.fields.to_path'))->fontFamily('mono')->searchable(),
                TextColumn::make('status_code')->label('Code')->badge(),
                TextColumn::make('hits')->label(__('admin.fields.hits'))->numeric(),
                ToggleColumn::make('is_active')->label(__('admin.fields.is_active')),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRedirects::route('/'),
            'create' => CreateRedirect::route('/create'),
            'edit' => EditRedirect::route('/{record}/edit'),
        ];
    }
}
