<?php

namespace App\Filament\Resources\Sectors;

use App\Filament\Concerns\HasModulePermission;
use App\Filament\Resources\Sectors\Pages\CreateSector;
use App\Filament\Resources\Sectors\Pages\EditSector;
use App\Filament\Resources\Sectors\Pages\ListSectors;
use App\Filament\Support\Translatable;
use App\Models\Sector;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

/** Administrable sector list, used by projects and the Submit a Project form. */
class SectorResource extends Resource
{
    use HasModulePermission;

    protected static ?string $model = Sector::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?int $navigationSort = 5;

    public static function module(): string
    {
        return 'projects';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.nav.opportunities');
    }

    public static function getModelLabel(): string
    {
        return __('admin.sector');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.sectors');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->schema([
                Translatable::text('name', __('admin.fields.name'), required: true),
                Grid::make(3)->schema([
                    TextInput::make('code')->label(__('admin.fields.code'))->required()->alphaDash()->unique(ignoreRecord: true)->helperText(__('admin.help.code')),
                    TextInput::make('sort')->label(__('admin.fields.sort'))->numeric()->default(0),
                    Toggle::make('is_active')->label(__('admin.fields.is_active'))->default(true)->inline(false),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('name')->label(__('admin.fields.name'))->state(fn (Sector $record) => $record->tr('name')),
                TextColumn::make('code')->label(__('admin.fields.code'))->fontFamily('mono')->color('gray'),
                TextColumn::make('projects_count')->counts('projects')->label(__('admin.projects'))->badge(),
                ToggleColumn::make('is_active')->label(__('admin.fields.is_active')),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSectors::route('/'),
            'create' => CreateSector::route('/create'),
            'edit' => EditSector::route('/{record}/edit'),
        ];
    }
}
