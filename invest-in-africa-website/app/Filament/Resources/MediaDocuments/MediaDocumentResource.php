<?php

namespace App\Filament\Resources\MediaDocuments;

use App\Filament\Concerns\HasModulePermission;
use App\Filament\Resources\MediaDocuments\Pages\CreateMediaDocument;
use App\Filament\Resources\MediaDocuments\Pages\EditMediaDocument;
use App\Filament\Resources\MediaDocuments\Pages\ListMediaDocuments;
use App\Filament\Support\Translatable;
use App\Models\Domain;
use App\Models\MediaDocument;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
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

/** Media module: public guides, brochures and attachments (§8.1 Documents). */
class MediaDocumentResource extends Resource
{
    use HasModulePermission;

    protected static ?string $model = MediaDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?int $navigationSort = 4;

    public static function module(): string
    {
        return 'media';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.nav.content');
    }

    public static function getModelLabel(): string
    {
        return __('admin.document');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.documents');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->schema([
                Translatable::text('title', __('admin.fields.title'), required: true),
                FileUpload::make('file')->label(__('admin.fields.file'))->required()
                    ->acceptedFileTypes(['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'])
                    ->disk('public')->directory('documents')->preserveFilenames()->maxSize(20480),
                Grid::make(3)->schema([
                    Select::make('category')->label(__('admin.fields.category'))->options(['guide' => 'Guide', 'brochure' => 'Brochure', 'report' => 'Report', 'other' => 'Other'])->default('guide'),
                    Select::make('domain_id')->label(__('admin.fields.domain'))
                        ->options(fn () => Domain::published()->get()->mapWithKeys(fn ($d) => [$d->id => $d->numberLabel().' — '.$d->tr('title')])),
                    Toggle::make('is_published')->label(__('admin.fields.is_published'))->default(true)->inline(false),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label(__('admin.fields.title'))->state(fn (MediaDocument $record) => $record->tr('title')),
                TextColumn::make('category')->label(__('admin.fields.category'))->badge()->color('gray'),
                TextColumn::make('domain.number')->label(__('admin.fields.domain'))->placeholder('—'),
                ToggleColumn::make('is_published')->label(__('admin.fields.is_published')),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMediaDocuments::route('/'),
            'create' => CreateMediaDocument::route('/create'),
            'edit' => EditMediaDocument::route('/{record}/edit'),
        ];
    }
}
