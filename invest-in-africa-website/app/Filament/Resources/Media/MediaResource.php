<?php

namespace App\Filament\Resources\Media;

use App\Filament\Concerns\HasModulePermission;
use App\Filament\Resources\Media\Pages\CreateMedia;
use App\Filament\Resources\Media\Pages\EditMedia;
use App\Filament\Resources\Media\Pages\ListMedia;
use App\Filament\Support\Translatable;
use App\Models\Media;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

/**
 * Media library (§8.1, §8.2): images, videos and documents; alternative
 * texts per language; images converted automatically to AVIF / WebP.
 */
class MediaResource extends Resource
{
    use HasModulePermission;

    protected static ?string $model = Media::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?int $navigationSort = 5;

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
        return __('admin.media');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.media_library');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->schema([
                FileUpload::make('file')->label(__('admin.fields.file'))->required()
                    ->disk('public')->directory('media')->maxSize(51200)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'video/mp4', 'application/pdf'])
                    ->imageEditor()
                    ->helperText(__('admin.help.media')),
                Translatable::text('alt', __('admin.blocks.alt'))->columnSpanFull(),
            ]),
        ]);
    }

    /** Type, size and MIME type are derived from the stored file. */
    public static function describe(array $data): array
    {
        $disk = Storage::disk('public');
        $path = $data['file'];
        $mime = $disk->mimeType($path) ?: 'application/octet-stream';

        return array_merge($data, [
            'mime_type' => $mime,
            'size' => $disk->size($path),
            'type' => match (true) {
                str_starts_with($mime, 'image/') => 'image',
                str_starts_with($mime, 'video/') => 'video',
                default => 'document',
            },
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('preview')->label('')->disk('public')->imageHeight(56)
                    ->state(fn (Media $record) => $record->isImage() ? $record->file : null),
                TextColumn::make('file')->label(__('admin.fields.file'))->formatStateUsing(fn (string $state) => basename($state))
                    ->description(fn (Media $record) => $record->altText('en') ?: __('admin.help.alt_missing'))->searchable()->wrap(),
                TextColumn::make('type')->label(__('admin.fields.type'))->badge()->color('gray'),
                TextColumn::make('variants')->label('AVIF / WebP')
                    ->state(fn (Media $record) => $record->variants ? count($record->variants['webp'] ?? []).' × 2' : '—'),
                TextColumn::make('size')->label(__('admin.fields.size'))
                    ->state(fn (Media $record) => $record->size ? round($record->size / 1024).' KB' : '—'),
            ])
            ->filters([
                SelectFilter::make('type')->label(__('admin.fields.type'))->options(['image' => 'Image', 'video' => 'Video', 'document' => 'Document']),
            ])
            ->recordActions([
                Action::make('copy')->label(__('admin.copy_url'))->icon(Heroicon::OutlinedLink)->color('gray')
                    ->url(fn (Media $record) => $record->url(), shouldOpenInNewTab: true),
                EditAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMedia::route('/'),
            'create' => CreateMedia::route('/create'),
            'edit' => EditMedia::route('/{record}/edit'),
        ];
    }
}
