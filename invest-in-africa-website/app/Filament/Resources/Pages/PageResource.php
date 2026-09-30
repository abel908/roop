<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Concerns\HasModulePermission;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Support\PageBlocks;
use App\Filament\Support\Translatable;
use App\Models\Page;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use UnitEnum;

/** Pages module (§8.1, §8.2): block editing, preview, drafts, scheduling, history. */
class PageResource extends Resource
{
    use HasModulePermission;

    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

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
        return __('admin.page');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.pages');
    }

    public static function getRecordTitle(?Model $record): ?string
    {
        return $record?->tr('title', 'en');
    }

    public static function previewUrl(Page $page, string $locale): string
    {
        return URL::temporarySignedRoute('pages.preview', now()->addHour(), ['page' => $page, 'locale' => $locale]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make(__('admin.tabs.content'))->icon(Heroicon::OutlinedLanguage)->schema([
                    Translatable::text('title', __('admin.fields.title'), required: true),
                    PageBlocks::make(),
                ]),
                Tab::make(__('admin.tabs.publication'))->icon(Heroicon::OutlinedGlobeAlt)->schema([
                    Grid::make(['default' => 1, 'md' => 2])->schema([
                        Select::make('status')->label(__('admin.fields.status'))->required()->default(Page::STATUS_DRAFT)
                            ->options([Page::STATUS_DRAFT => __('admin.status.draft'), Page::STATUS_PUBLISHED => __('admin.status.published')]),
                        DateTimePicker::make('published_at')->label(__('admin.fields.published_at'))->helperText(__('admin.help.published_at')),
                    ]),
                    Translatable::make('slug', __('admin.fields.slug'), fn (string $path) => TextInput::make($path)
                        ->maxLength(120)->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                        ->dehydrateStateUsing(fn ($state) => $state ? Str::slug($state) : null), required: true),
                ]),
                Tab::make(__('admin.nav.seo'))->icon(Heroicon::OutlinedMagnifyingGlass)->schema([
                    Translatable::text('seo_title', __('admin.fields.meta_title'), maxLength: 70),
                    Translatable::textarea('seo_description', __('admin.fields.meta_description'), rows: 3),
                    FileUpload::make('og_image')->label(__('admin.fields.og_image'))->image()->imageEditor()
                        ->imageAspectRatio('1.91:1')->disk('public')->directory('seo')->maxSize(2048),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('title')->label(__('admin.fields.title'))->state(fn (Page $record) => $record->tr('title', 'en'))->weight('bold')
                    ->description(fn (Page $record) => '/'.$record->tr('slug', 'en')),
                TextColumn::make('status')->label(__('admin.fields.status'))->badge()
                    ->state(fn (Page $record) => $record->isLive() ? __('admin.status.published') : ($record->status === Page::STATUS_PUBLISHED ? __('admin.status.scheduled') : __('admin.status.draft')))
                    ->color(fn (Page $record) => $record->isLive() ? 'success' : ($record->status === Page::STATUS_PUBLISHED ? 'info' : 'gray')),
                ViewColumn::make('translations')->label(__('admin.fields.translations'))->view('filament.columns.translation-status'),
                TextColumn::make('updated_at')->label(__('admin.fields.updated_at'))->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('admin.fields.status'))
                    ->options([Page::STATUS_DRAFT => __('admin.status.draft'), Page::STATUS_PUBLISHED => __('admin.status.published')]),
            ])
            ->recordActions([
                ActionGroup::make(collect(['en' => 'English', 'fr' => 'Français', 'zh' => '中文'])->map(
                    fn ($label, $locale) => Action::make("preview_$locale")->label($label)
                        ->url(fn (Page $record) => static::previewUrl($record, $locale), shouldOpenInNewTab: true)
                )->values()->all())->label(__('admin.preview'))->icon(Heroicon::OutlinedEye)->button()->color('gray'),
                EditAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
