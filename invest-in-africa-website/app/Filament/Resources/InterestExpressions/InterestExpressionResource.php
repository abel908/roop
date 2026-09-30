<?php

namespace App\Filament\Resources\InterestExpressions;

use App\Enums\InvestorType;
use App\Enums\RequestStatus;
use App\Filament\Concerns\HasModulePermission;
use App\Filament\Resources\InterestExpressions\Pages\EditInterestExpression;
use App\Filament\Resources\InterestExpressions\Pages\ListInterestExpressions;
use App\Filament\Resources\InterestExpressions\Pages\ViewInterestExpression;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Support\CsvExport;
use App\Models\InterestExpression;
use App\Models\User;
use App\Support\Countries;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
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

/** Investors' expressions of interest (§6.2, §8.2 Dashboard). */
class InterestExpressionResource extends Resource
{
    use HasModulePermission;

    protected static ?string $model = InterestExpression::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHandRaised;

    protected static ?string $recordTitleAttribute = 'reference';

    protected static ?int $navigationSort = 3;

    public static function module(): string
    {
        return 'projects';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.nav.requests');
    }

    public static function getModelLabel(): string
    {
        return __('admin.interest');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.interests');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = InterestExpression::query()->where('status', RequestStatus::New)->count();

        return $count ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('admin.processing'))->columnSpanFull()->columns(2)->schema([
                Select::make('status')->label(__('admin.fields.status'))->options(RequestStatus::class)->required(),
                Select::make('assigned_to')->label(__('admin.fields.assigned_to'))
                    ->options(fn () => User::query()->where('is_active', true)->get()->filter(fn (User $u) => $u->canManage('projects'))->pluck('name', 'id')),
                Textarea::make('internal_notes')->label(__('admin.fields.internal_notes'))->rows(6)->columnSpanFull(),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columnSpanFull()->columns(4)->schema([
                TextEntry::make('reference')->label(__('admin.fields.reference'))->fontFamily('mono')->weight('bold')->copyable(),
                TextEntry::make('project.reference')->label(__('admin.project'))
                    ->formatStateUsing(fn (InterestExpression $record) => $record->project->reference.' — '.$record->project->tr('title', 'en'))
                    ->url(fn (InterestExpression $record) => ProjectResource::getUrl('edit', ['record' => $record->project])),
                TextEntry::make('status')->label(__('admin.fields.status'))->badge(),
                TextEntry::make('created_at')->label(__('admin.fields.received_at'))->dateTime('d/m/Y H:i'),
            ]),
            Section::make(__('admin.sections.investor'))->columnSpanFull()->columns(3)->schema([
                TextEntry::make('full_name')->label(__('forms.labels.full_name')),
                TextEntry::make('organization')->label(__('forms.labels.organization'))->placeholder('—'),
                TextEntry::make('investor_type')->label(__('forms.labels.investor_type'))->badge(),
                TextEntry::make('email')->label(__('forms.labels.email'))->copyable()->url(fn ($state) => "mailto:$state"),
                TextEntry::make('phone')->label(__('forms.labels.phone')),
                TextEntry::make('country')->label(__('forms.labels.country'))->formatStateUsing(fn ($state) => Countries::name($state)),
                TextEntry::make('amount')->label(__('forms.labels.amount'))->state(fn (InterestExpression $record) => $record->amountLabel()),
                TextEntry::make('locale')->label(__('admin.fields.language'))->formatStateUsing(fn ($state) => config("site.locales.$state.label")),
                TextEntry::make('message')->label(__('forms.labels.message'))->columnSpanFull()->placeholder('—'),
                TextEntry::make('internal_notes')->label(__('admin.fields.internal_notes'))->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $export = [
            'Reference' => 'reference', 'Received' => 'created_at', 'Status' => 'status',
            'Project' => 'project.reference', 'Full name' => 'full_name', 'Organization' => 'organization',
            'Investor type' => 'investor_type', 'Email' => 'email', 'Phone' => 'phone', 'Country' => 'country',
            'Amount' => 'amount', 'Message' => 'message', 'Language' => 'locale',
        ];

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')->label(__('admin.fields.reference'))->fontFamily('mono')->searchable()->sortable(),
                TextColumn::make('project.reference')->label(__('admin.project'))->fontFamily('mono')->searchable(),
                TextColumn::make('full_name')->label(__('forms.labels.full_name'))->searchable()->weight('bold')
                    ->description(fn (InterestExpression $record) => $record->organization),
                TextColumn::make('investor_type')->label(__('forms.labels.investor_type'))->badge()->color('gray'),
                TextColumn::make('country')->label(__('forms.labels.country'))->formatStateUsing(fn ($state) => Countries::name($state)),
                TextColumn::make('status')->label(__('admin.fields.status'))->badge(),
                TextColumn::make('created_at')->label(__('admin.fields.received_at'))->dateTime('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('admin.fields.status'))->options(RequestStatus::class),
                SelectFilter::make('investor_type')->label(__('forms.labels.investor_type'))->options(InvestorType::class),
                SelectFilter::make('project_id')->label(__('admin.project'))->relationship('project', 'reference'),
            ])
            ->headerActions([CsvExport::headerAction('interests', fn () => InterestExpression::query(), $export)])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([CsvExport::bulkAction('interests', $export)])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInterestExpressions::route('/'),
            'view' => ViewInterestExpression::route('/{record}'),
            'edit' => EditInterestExpression::route('/{record}/edit'),
        ];
    }
}
