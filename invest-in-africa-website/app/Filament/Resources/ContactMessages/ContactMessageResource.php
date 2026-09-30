<?php

namespace App\Filament\Resources\ContactMessages;

use App\Enums\ContactSubject;
use App\Enums\RequestStatus;
use App\Filament\Concerns\HasModulePermission;
use App\Filament\Resources\ContactMessages\Pages\EditContactMessage;
use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Filament\Support\CsvExport;
use App\Models\ContactMessage;
use App\Support\Countries;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

/** Contact Messages module (§8.2): subject, processing status, export. */
class ContactMessageResource extends Resource
{
    use HasModulePermission;

    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $recordTitleAttribute = 'reference';

    protected static ?int $navigationSort = 4;

    public static function module(): string
    {
        return 'contact_messages';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.nav.requests');
    }

    public static function getModelLabel(): string
    {
        return __('admin.message');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.messages');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = ContactMessage::query()->where('status', RequestStatus::New)->count();

        return $count ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('admin.processing'))->columnSpanFull()->schema([
                Select::make('status')->label(__('admin.fields.status'))->options(RequestStatus::class)->required(),
                Textarea::make('internal_notes')->label(__('admin.fields.internal_notes'))->rows(6),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->columns(4)->schema([
                TextEntry::make('reference')->label(__('admin.fields.reference'))->fontFamily('mono')->weight('bold')->copyable(),
                TextEntry::make('subject')->label(__('forms.labels.subject'))->badge(),
                TextEntry::make('status')->label(__('admin.fields.status'))->badge(),
                TextEntry::make('created_at')->label(__('admin.fields.received_at'))->dateTime('d/m/Y H:i'),
                TextEntry::make('full_name')->label(__('forms.labels.full_name')),
                TextEntry::make('organization')->label(__('forms.labels.organization'))->placeholder('—'),
                TextEntry::make('email')->label(__('forms.labels.email'))->copyable()->url(fn ($state) => "mailto:$state"),
                TextEntry::make('phone')->label(__('forms.labels.phone'))->placeholder('—'),
                TextEntry::make('country')->label(__('forms.labels.country'))->formatStateUsing(fn ($state) => Countries::name($state))->placeholder('—'),
                TextEntry::make('locale')->label(__('admin.fields.language'))->formatStateUsing(fn ($state) => config("site.locales.$state.label")),
                TextEntry::make('message')->label(__('forms.labels.message'))->columnSpanFull(),
                TextEntry::make('internal_notes')->label(__('admin.fields.internal_notes'))->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $export = [
            'Reference' => 'reference', 'Received' => 'created_at', 'Status' => 'status', 'Subject' => 'subject',
            'Full name' => 'full_name', 'Organization' => 'organization', 'Email' => 'email', 'Phone' => 'phone',
            'Country' => 'country', 'Message' => 'message', 'Language' => 'locale',
        ];

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')->label(__('admin.fields.reference'))->fontFamily('mono')->searchable()->sortable(),
                TextColumn::make('subject')->label(__('forms.labels.subject'))->badge()->color('gray'),
                TextColumn::make('full_name')->label(__('forms.labels.full_name'))->searchable()->weight('bold')
                    ->description(fn (ContactMessage $record) => $record->email),
                TextColumn::make('message')->label(__('forms.labels.message'))->limit(60)->wrap()->toggleable(),
                TextColumn::make('status')->label(__('admin.fields.status'))->badge(),
                TextColumn::make('created_at')->label(__('admin.fields.received_at'))->dateTime('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('admin.fields.status'))->options(RequestStatus::class),
                SelectFilter::make('subject')->label(__('forms.labels.subject'))->options(ContactSubject::class),
            ])
            ->headerActions([CsvExport::headerAction('messages', fn () => ContactMessage::query(), $export)])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([CsvExport::bulkAction('messages', $export), DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'view' => ViewContactMessage::route('/{record}'),
            'edit' => EditContactMessage::route('/{record}/edit'),
        ];
    }
}
