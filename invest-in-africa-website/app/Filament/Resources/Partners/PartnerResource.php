<?php

namespace App\Filament\Resources\Partners;

use App\Enums\PartnerCategory;
use App\Filament\Concerns\HasModulePermission;
use App\Filament\Resources\Partners\Pages\CreatePartner;
use App\Filament\Resources\Partners\Pages\EditPartner;
use App\Filament\Resources\Partners\Pages\ListPartners;
use App\Filament\Support\Translatable;
use App\Models\Partner;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

/** Partners module (§5.5, §8.2): logos, descriptions, links, categories, display order. */
class PartnerResource extends Resource
{
    use HasModulePermission;

    protected static ?string $model = Partner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    public static function module(): string
    {
        return 'partners';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.nav.content');
    }

    public static function getModelLabel(): string
    {
        return __('admin.partner');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.partners');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->schema([
                Grid::make(['default' => 1, 'md' => 3])->schema([
                    TextInput::make('name')->label(__('admin.fields.name'))->required()->maxLength(160),
                    Select::make('category')->label(__('admin.fields.category'))->options(PartnerCategory::class)->required()->default('strategic'),
                    TextInput::make('website_url')->label(__('admin.fields.website'))->url()->maxLength(255),
                ]),
                FileUpload::make('logo')->label(__('admin.fields.logo'))
                    ->helperText(__('admin.help.logo'))
                    ->image()->acceptedFileTypes(['image/svg+xml', 'image/png', 'image/webp', 'image/jpeg'])
                    ->disk('public')->directory('partners')->maxSize(2048),
                Translatable::textarea('description', __('admin.fields.description'), rows: 3),
                Grid::make(['default' => 1, 'md' => 3])->schema([
                    Toggle::make('is_published')->label(__('admin.fields.is_published'))->default(true),
                    Toggle::make('is_featured')->label(__('admin.fields.featured_home')),
                    TextInput::make('sort')->label(__('admin.fields.sort'))->numeric()->default(0),
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
                ImageColumn::make('logo')->label(__('admin.fields.logo'))->disk('public')->imageHeight(40),
                TextColumn::make('name')->label(__('admin.fields.name'))->searchable()->weight('bold'),
                TextColumn::make('category')->label(__('admin.fields.category'))->badge()->color('gray'),
                ToggleColumn::make('is_featured')->label(__('admin.fields.featured_home')),
                ToggleColumn::make('is_published')->label(__('admin.fields.is_published')),
            ])
            ->filters([SelectFilter::make('category')->label(__('admin.fields.category'))->options(PartnerCategory::class)])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPartners::route('/'),
            'create' => CreatePartner::route('/create'),
            'edit' => EditPartner::route('/{record}/edit'),
        ];
    }
}
