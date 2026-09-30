<?php

namespace App\Filament\Resources\ContentTranslations;

use App\Filament\Concerns\HasModulePermission;
use App\Filament\Resources\ContentTranslations\Pages\EditContentTranslation;
use App\Filament\Resources\ContentTranslations\Pages\ListContentTranslations;
use App\Models\ContentTranslation;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Contents & translations (§8.1, §8.2 Translations): every text of the pages,
 * interface, forms and emails, EN / FR / 中文 side by side. An empty field
 * keeps the default text; a filled field overrides it immediately.
 */
class ContentTranslationResource extends Resource
{
    use HasModulePermission;

    protected static ?string $model = ContentTranslation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    protected static ?int $navigationSort = 2;

    public const LOCALES = ['en' => 'English', 'fr' => 'Français', 'zh' => '中文'];

    public static function module(): string
    {
        return 'translations';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.nav.content');
    }

    public static function getModelLabel(): string
    {
        return __('admin.text');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.texts');
    }

    public static function getRecordTitle(?Model $record): ?string
    {
        return $record ? "{$record->group}.{$record->key}" : null;
    }

    public static function form(Schema $schema): Schema
    {
        $fields = [];

        foreach (self::LOCALES as $locale => $label) {
            $fields[] = Textarea::make($locale)->label($label)->rows(5)
                ->placeholder(fn (?ContentTranslation $record) => $record?->fileValue($locale))
                ->helperText(fn (?ContentTranslation $record) => $record && blank($record->fileValue($locale)) && blank($record->{$locale})
                    ? __('admin.translation_missing') : __('admin.help.default_text'));
        }

        return $schema->components([
            Section::make()->columnSpanFull()->schema([
                TextEntry::make('key')->label(__('admin.fields.key'))
                    ->state(fn (ContentTranslation $record) => "{$record->group}.{$record->key}")->fontFamily('mono'),
                Grid::make(['default' => 1, 'lg' => 3])->schema($fields),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $column = fn (string $locale, string $label) => TextColumn::make($locale)->label($label)
            ->state(fn (ContentTranslation $record) => $record->effective($locale))
            ->placeholder(__('admin.translation_missing'))
            ->color(fn (ContentTranslation $record) => filled($record->{$locale}) ? 'success' : null)
            ->limit(60)->wrap();

        return $table
            ->defaultSort('group')
            ->columns([
                TextColumn::make('group')->label(__('admin.fields.page'))->badge()->color('gray')->sortable(),
                TextColumn::make('key')->label(__('admin.fields.key'))->fontFamily('mono')->size('xs')->searchable(),
                $column('en', 'English'),
                $column('fr', 'Français'),
                $column('zh', '中文'),
            ])
            ->filters([
                SelectFilter::make('group')->label(__('admin.fields.page'))
                    ->options(fn () => ContentTranslation::query()->distinct()->orderBy('group')->pluck('group', 'group')->all()),
                Filter::make('overridden')->label(__('admin.filters.overridden'))
                    ->query(fn (Builder $q) => $q->where(fn ($q) => $q->whereNotNull('en')->orWhereNotNull('fr')->orWhereNotNull('zh'))),
            ])
            ->searchable()
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContentTranslations::route('/'),
            'edit' => EditContentTranslation::route('/{record}/edit'),
        ];
    }
}
