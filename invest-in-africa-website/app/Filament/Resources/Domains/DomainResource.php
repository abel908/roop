<?php

namespace App\Filament\Resources\Domains;

use App\Filament\Concerns\HasModulePermission;
use App\Filament\Resources\Domains\Pages\CreateDomain;
use App\Filament\Resources\Domains\Pages\EditDomain;
use App\Filament\Resources\Domains\Pages\ListDomains;
use App\Filament\Support\Translatable;
use App\Models\Domain;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** The six areas of What We Do — each page administrable in the three languages (§5.4). */
class DomainResource extends Resource
{
    use HasModulePermission;

    protected static ?string $model = Domain::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?int $navigationSort = 1;

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
        return __('admin.domain');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.domains');
    }

    public static function getRecordTitle(?Model $record): ?string
    {
        return $record?->tr('title', 'en');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()->columnSpanFull()->tabs([
                Tab::make(__('admin.tabs.content'))->icon(Heroicon::OutlinedLanguage)->schema([
                    Translatable::text('title', __('admin.fields.title'), required: true),
                    Translatable::textarea('tagline', __('admin.fields.tagline'), rows: 2),
                    Translatable::text('audience', __('admin.fields.audience')),
                    Translatable::textarea('intro', __('admin.fields.intro'), rows: 4),
                    Translatable::textarea('challenges', __('admin.fields.challenges'), rows: 3),
                    Translatable::list('services', __('admin.fields.services')),
                    Translatable::list('benefits', __('admin.fields.benefits')),
                    Translatable::list('steps', __('admin.fields.steps')),
                ]),
                Tab::make(__('admin.tabs.settings'))->icon(Heroicon::OutlinedCog6Tooth)->schema([
                    Grid::make(['default' => 1, 'md' => 3])->schema([
                        TextInput::make('number')->label(__('admin.fields.number'))->numeric()->required()->unique(ignoreRecord: true),
                        Select::make('icon')->label(__('admin.fields.icon'))->required()->options([
                            'fdi' => 'FDI', 'financing' => 'Financing', 'talents' => 'Talents / SME',
                            'industry' => 'Industrialization', 'diaspora' => 'Diaspora', 'government' => 'Government',
                        ]),
                        Toggle::make('is_published')->label(__('admin.fields.is_published'))->inline(false),
                    ]),
                    Translatable::text('slug', __('admin.fields.slug'), required: true),
                    FileUpload::make('cover_image')->label(__('admin.fields.cover'))->image()->imageEditor()
                        ->disk('public')->directory('domains')->maxSize(4096),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('number')
            ->columns([
                TextColumn::make('number')->label('#')->sortable(),
                TextColumn::make('title')->label(__('admin.fields.title'))->state(fn (Domain $record) => $record->tr('title', 'en'))->weight('bold')->wrap(),
                ViewColumn::make('translations')->label(__('admin.fields.translations'))->view('filament.columns.translation-status'),
                ToggleColumn::make('is_published')->label(__('admin.fields.is_published')),
            ])
            ->recordActions([
                Action::make('preview')->label(__('admin.preview'))->icon(Heroicon::OutlinedEye)->color('gray')
                    ->url(fn (Domain $record) => $record->url('en'), shouldOpenInNewTab: true),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDomains::route('/'),
            'create' => CreateDomain::route('/create'),
            'edit' => EditDomain::route('/{record}/edit'),
        ];
    }
}
