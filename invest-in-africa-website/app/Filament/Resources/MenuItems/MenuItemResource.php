<?php

namespace App\Filament\Resources\MenuItems;

use App\Filament\Concerns\HasModulePermission;
use App\Filament\Resources\MenuItems\Pages\CreateMenuItem;
use App\Filament\Resources\MenuItems\Pages\EditMenuItem;
use App\Filament\Resources\MenuItems\Pages\ListMenuItems;
use App\Filament\Support\Translatable;
use App\Models\MenuItem;
use App\Models\Page;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

/** Menus administrable per language (§7.2, §8.1): header and footer navigation. */
class MenuItemResource extends Resource
{
    use HasModulePermission;

    protected static ?string $model = MenuItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    protected static ?int $navigationSort = 2;

    public static function module(): string
    {
        return 'pages';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.nav.content');
    }

    public static function getModelLabel(): string
    {
        return __('admin.menu_item');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.menus');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->schema([
                Grid::make(['default' => 1, 'md' => 3])->schema([
                    Select::make('location')->label(__('admin.fields.location'))->required()->default('header')
                        ->options(['header' => __('admin.menu.header'), 'footer' => __('admin.menu.footer')]),
                    Select::make('type')->label(__('admin.fields.type'))->required()->default('route')->live()
                        ->options([
                            'route' => __('admin.menu.route'),
                            'mega' => __('admin.menu.mega'),
                            'page' => __('admin.page'),
                            'url' => __('admin.menu.url'),
                        ]),
                    TextInput::make('sort')->label(__('admin.fields.sort'))->numeric()->default(100),
                ]),
                Select::make('target')->label(__('admin.menu.section'))
                    ->visible(fn (Get $get) => $get('type') === 'route')->required(fn (Get $get) => $get('type') === 'route')
                    ->options(fn () => collect(MenuItem::SECTIONS)->map(fn ($key) => __($key))->all()),
                Select::make('target')->label(__('admin.menu.mega'))
                    ->visible(fn (Get $get) => $get('type') === 'mega')->required(fn (Get $get) => $get('type') === 'mega')
                    ->options(['domains' => __('site.nav.what_we_do'), 'involved' => __('site.nav.get_involved')])
                    ->helperText(__('admin.help.mega')),
                Select::make('page_id')->label(__('admin.page'))
                    ->visible(fn (Get $get) => $get('type') === 'page')->required(fn (Get $get) => $get('type') === 'page')
                    ->options(fn () => Page::query()->get()->mapWithKeys(fn (Page $p) => [$p->id => $p->tr('title', 'en')])),
                Translatable::text('url', __('admin.menu.url'))->visible(fn (Get $get) => $get('type') === 'url'),
                Translatable::text('label', __('admin.help.menu_label')),
                Grid::make(2)->schema([
                    Toggle::make('is_active')->label(__('admin.fields.is_active'))->default(true),
                    Toggle::make('new_tab')->label(__('admin.menu.new_tab')),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->defaultGroup('location')
            ->columns([
                TextColumn::make('label')->label(__('admin.fields.label'))->state(fn (MenuItem $record) => $record->label('en'))->weight('bold')
                    ->description(fn (MenuItem $record) => $record->label('fr').' · '.$record->label('zh')),
                TextColumn::make('type')->label(__('admin.fields.type'))->badge()->color('gray'),
                TextColumn::make('href')->label('URL (EN)')->state(fn (MenuItem $record) => $record->href('en'))->limit(50)->color('gray'),
                ToggleColumn::make('is_active')->label(__('admin.fields.is_active')),
            ])
            ->filters([SelectFilter::make('location')->label(__('admin.fields.location'))->options(['header' => __('admin.menu.header'), 'footer' => __('admin.menu.footer')])])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenuItems::route('/'),
            'create' => CreateMenuItem::route('/create'),
            'edit' => EditMenuItem::route('/{record}/edit'),
        ];
    }
}
