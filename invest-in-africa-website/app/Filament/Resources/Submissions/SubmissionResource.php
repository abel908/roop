<?php

namespace App\Filament\Resources\Submissions;

use App\Enums\ProjectStage;
use App\Enums\SubmissionStatus;
use App\Filament\Concerns\HasModulePermission;
use App\Filament\Resources\Submissions\Pages\EditSubmission;
use App\Filament\Resources\Submissions\Pages\ListSubmissions;
use App\Filament\Resources\Submissions\Pages\ViewSubmission;
use App\Filament\Support\CsvExport;
use App\Models\Sector;
use App\Models\Submission;
use App\Models\User;
use App\Support\Countries;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * Submissions module (§8.2): secure attachments, statuses of the §6.4
 * workflow, internal notes, assignment, export.
 */
class SubmissionResource extends Resource
{
    use HasModulePermission;

    protected static ?string $model = Submission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static ?string $recordTitleAttribute = 'reference';

    protected static ?int $navigationSort = 2;

    public static function module(): string
    {
        return 'submissions';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.nav.requests');
    }

    public static function getModelLabel(): string
    {
        return __('admin.submission');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.submissions');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Submission::query()->where('status', SubmissionStatus::Received)->count();

        return $count ? (string) $count : null;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'project_name', 'full_name', 'organization', 'email'];
    }

    /** Processing form: status, assignment, internal notes. */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('admin.processing'))->columnSpanFull()->schema([
                Grid::make(['default' => 1, 'md' => 2])->schema([
                    Select::make('status')->label(__('admin.fields.status'))->options(SubmissionStatus::class)->required(),
                    Select::make('assigned_to')->label(__('admin.fields.assigned_to'))
                        ->options(fn () => User::query()->where('is_active', true)->get()->filter(fn (User $u) => $u->canManage('submissions'))->pluck('name', 'id'))
                        ->searchable(),
                ]),
                Toggle::make('notify_holder')->label(__('admin.fields.notify_holder'))->helperText(__('admin.help.notify_holder'))->dehydrated(false),
                Textarea::make('internal_notes')->label(__('admin.fields.internal_notes'))->rows(6),
            ]),
        ]);
    }

    /** Complete file view. */
    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('admin.processing'))->columnSpanFull()->columns(4)->schema([
                TextEntry::make('reference')->label(__('admin.fields.reference'))->fontFamily('mono')->copyable()->weight('bold'),
                TextEntry::make('status')->label(__('admin.fields.status'))->badge(),
                TextEntry::make('assignee.name')->label(__('admin.fields.assigned_to'))->placeholder('—'),
                TextEntry::make('created_at')->label(__('admin.fields.received_at'))->dateTime('d/m/Y H:i'),
                TextEntry::make('internal_notes')->label(__('admin.fields.internal_notes'))->columnSpanFull()->placeholder('—'),
            ]),
            Section::make(__('admin.sections.holder'))->columns(3)->schema([
                TextEntry::make('full_name')->label(__('forms.labels.full_name')),
                TextEntry::make('organization')->label(__('forms.labels.organization')),
                TextEntry::make('position')->label(__('forms.labels.position')),
                TextEntry::make('email')->label(__('forms.labels.email'))->copyable()->url(fn ($state) => "mailto:$state"),
                TextEntry::make('phone')->label(__('forms.labels.phone'))->url(fn ($state) => 'tel:'.preg_replace('/[^0-9+]/', '', $state)),
                TextEntry::make('country')->label(__('forms.labels.country'))->formatStateUsing(fn ($state) => Countries::name($state)),
                TextEntry::make('locale')->label(__('admin.fields.language'))->formatStateUsing(fn ($state) => config("site.locales.$state.label")),
            ]),
            Section::make(__('admin.sections.project'))->columns(3)->schema([
                TextEntry::make('project_name')->label(__('submit.fields.project_name'))->weight('bold')->columnSpanFull(),
                TextEntry::make('project_country')->label(__('admin.fields.country'))->formatStateUsing(fn ($state) => Countries::name($state)),
                TextEntry::make('sector_id')->label(__('admin.fields.sector'))->formatStateUsing(fn (Submission $record) => $record->sector?->tr('name')),
                TextEntry::make('stage')->label(__('admin.fields.stage'))->badge(),
                TextEntry::make('investment_amount')->label(__('admin.fields.amount'))->state(fn (Submission $record) => $record->amountLabel()),
                TextEntry::make('funding_type')->label(__('admin.fields.funding_type')),
                TextEntry::make('timeline')->label(__('admin.fields.timeline')),
                TextEntry::make('description')->label(__('admin.fields.description'))->columnSpanFull()->prose(),
            ]),
            Section::make(__('admin.sections.documents'))->description(__('admin.help.documents'))->schema([
                RepeatableEntry::make('documents')->hiddenLabel()->placeholder(__('submit.summary.no_documents'))->columns(3)->schema([
                    TextEntry::make('original_name')->label(__('admin.fields.file'))
                        ->icon(Heroicon::OutlinedDocumentArrowDown)
                        ->url(fn ($record) => $record->temporaryUrl(), shouldOpenInNewTab: true)
                        ->color('primary'),
                    TextEntry::make('mime_type')->label(__('admin.fields.type')),
                    TextEntry::make('size')->label(__('admin.fields.size'))->state(fn ($record) => $record->humanSize()),
                ]),
            ]),
            Section::make(__('admin.sections.consent'))->columns(3)->collapsed()->schema([
                TextEntry::make('consent_at')->label(__('admin.fields.consent_at'))->dateTime('d/m/Y H:i'),
                TextEntry::make('certified_at')->label(__('admin.fields.certified_at'))->dateTime('d/m/Y H:i'),
                TextEntry::make('ip_address')->label('IP'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $export = [
            'Reference' => 'reference',
            'Received' => 'created_at',
            'Status' => 'status',
            'Project' => 'project_name',
            'Project country' => fn (Submission $s) => Countries::name($s->project_country, 'en'),
            'Sector' => fn (Submission $s) => $s->sector?->tr('name', 'en'),
            'Stage' => 'stage',
            'Amount' => 'investment_amount',
            'Currency' => 'currency',
            'Funding' => 'funding_type',
            'Timeline' => 'timeline',
            'Full name' => 'full_name',
            'Organization' => 'organization',
            'Position' => 'position',
            'Email' => 'email',
            'Phone' => 'phone',
            'Country' => 'country',
            'Language' => 'locale',
            'Assigned to' => 'assignee.name',
            'Documents' => fn (Submission $s) => $s->documents()->count(),
        ];

        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['sector', 'assignee'])->withCount('documents'))
            ->columns([
                TextColumn::make('reference')->label(__('admin.fields.reference'))->fontFamily('mono')->searchable()->sortable(),
                TextColumn::make('project_name')->label(__('submit.fields.project_name'))->searchable()->limit(40)->weight('bold')
                    ->description(fn (Submission $record) => $record->full_name.' — '.$record->organization),
                TextColumn::make('project_country')->label(__('admin.fields.country'))->formatStateUsing(fn ($state) => Countries::name($state)),
                TextColumn::make('investment_amount')->label(__('admin.fields.amount'))->state(fn (Submission $record) => $record->amountLabel())->sortable(),
                TextColumn::make('status')->label(__('admin.fields.status'))->badge()->sortable(),
                TextColumn::make('documents_count')->label(__('admin.sections.documents'))->badge()->color('gray'),
                TextColumn::make('assignee.name')->label(__('admin.fields.assigned_to'))->placeholder('—')->toggleable(),
                TextColumn::make('created_at')->label(__('admin.fields.received_at'))->dateTime('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('admin.fields.status'))->options(SubmissionStatus::class)->multiple(),
                SelectFilter::make('stage')->label(__('admin.fields.stage'))->options(ProjectStage::class),
                SelectFilter::make('sector_id')->label(__('admin.fields.sector'))->options(fn () => Sector::options()),
                SelectFilter::make('assigned_to')->label(__('admin.fields.assigned_to'))->relationship('assignee', 'name'),
            ])
            ->headerActions([
                CsvExport::headerAction('submissions', fn () => Submission::query(), $export),
            ])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([CsvExport::bulkAction('submissions', $export)]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubmissions::route('/'),
            'view' => ViewSubmission::route('/{record}'),
            'edit' => EditSubmission::route('/{record}/edit'),
        ];
    }
}
