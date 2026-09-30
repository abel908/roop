<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Filament\Concerns\HasModulePermission;
use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Filament\Resources\ActivityLogs\Pages\ViewActivityLog;
use App\Models\ActivityLog;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Activity log — read only (§8.4, §11.1). */
class ActivityLogResource extends Resource
{
    use HasModulePermission;

    protected static ?string $model = ActivityLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?int $navigationSort = 3;

    public static function module(): string
    {
        return 'users';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.nav.administration');
    }

    public static function getModelLabel(): string
    {
        return __('admin.activity');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.activities');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->columns(3)->schema([
                TextEntry::make('created_at')->label(__('admin.fields.date'))->dateTime('d/m/Y H:i:s'),
                TextEntry::make('user.name')->label(__('admin.user'))->placeholder('—'),
                TextEntry::make('action')->label(__('admin.fields.action'))->badge(),
                TextEntry::make('subject_type')->label(__('admin.fields.subject'))->formatStateUsing(fn ($state, ActivityLog $r) => class_basename((string) $state).' #'.$r->subject_id)->placeholder('—'),
                TextEntry::make('ip_address')->label('IP'),
                KeyValueEntry::make('properties')->label(__('admin.fields.details'))->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label(__('admin.fields.date'))->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('user.name')->label(__('admin.user'))->placeholder('—')->searchable(),
                TextColumn::make('action')->label(__('admin.fields.action'))->badge()->searchable(),
                TextColumn::make('subject_type')->label(__('admin.fields.subject'))->formatStateUsing(fn ($state, ActivityLog $r) => class_basename((string) $state).' #'.$r->subject_id)->placeholder('—'),
                TextColumn::make('ip_address')->label('IP')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_id')->label(__('admin.user'))->relationship('user', 'name'),
                SelectFilter::make('action')->label(__('admin.fields.action'))
                    ->options(fn () => ActivityLog::query()->distinct()->pluck('action', 'action')->all()),
            ])
            ->recordActions([ViewAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityLogs::route('/'),
            'view' => ViewActivityLog::route('/{record}'),
        ];
    }
}
