<?php

namespace App\Filament\Resources\SeoMetas;

use App\Filament\Concerns\HasModulePermission;
use App\Filament\Resources\SeoMetas\Pages\CreateSeoMeta;
use App\Filament\Resources\SeoMetas\Pages\EditSeoMeta;
use App\Filament\Resources\SeoMetas\Pages\ListSeoMetas;
use App\Filament\Support\Translatable;
use App\Models\SeoMeta;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use UnitEnum;

/** SEO module: title, meta description and Open Graph image per page and per language (§10.1). */
class SeoMetaResource extends Resource
{
    use HasModulePermission;

    protected static ?string $model = SeoMeta::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static ?int $navigationSort = 1;

    public const PAGES = ['home', 'about', 'mission', 'what_we_do', 'get_involved', 'invest', 'submit', 'partners', 'contact', 'legal', 'privacy', 'cookies', 'terms'];

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
        return __('admin.seo_meta');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.seo_metas');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->schema([
                Select::make('page_key')->label(__('admin.fields.page'))->required()->unique(ignoreRecord: true)
                    ->options(array_combine(self::PAGES, self::PAGES)),
                Translatable::text('title', __('admin.fields.meta_title'), maxLength: 70),
                Translatable::textarea('description', __('admin.fields.meta_description'), rows: 3),
                FileUpload::make('og_image')->label(__('admin.fields.og_image'))->image()->imageEditor()
                    ->imageAspectRatio('1.91:1')->disk('public')->directory('seo')->maxSize(2048)
                    ->helperText('1200 × 630 px'),
                Toggle::make('noindex')->label('noindex'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('page_key')->label(__('admin.fields.page'))->fontFamily('mono'),
                TextColumn::make('title')->label(__('admin.fields.meta_title'))->state(fn (SeoMeta $r) => $r->tr('title', 'en'))->limit(60),
                ViewColumn::make('translations')->label(__('admin.fields.translations'))->view('filament.columns.translation-status'),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeoMetas::route('/'),
            'create' => CreateSeoMeta::route('/create'),
            'edit' => EditSeoMeta::route('/{record}/edit'),
        ];
    }
}
