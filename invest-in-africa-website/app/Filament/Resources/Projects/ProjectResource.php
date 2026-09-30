<?php

namespace App\Filament\Resources\Projects;

use App\Enums\FundingType;
use App\Enums\ProjectStage;
use App\Enums\ProjectStatus;
use App\Filament\Concerns\HasModulePermission;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Support\Translatable;
use App\Models\Domain;
use App\Models\Project;
use App\Models\Sector;
use App\Support\Countries;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

/** Projects module (§8.2): sheets, statuses, highlight, categories, public and controlled documents. */
class ProjectResource extends Resource
{
    use HasModulePermission;

    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $recordTitleAttribute = 'reference';

    protected static ?int $navigationSort = 1;

    public static function module(): string
    {
        return 'projects';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('admin.nav.opportunities');
    }

    public static function getModelLabel(): string
    {
        return __('admin.project');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.projects');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make(__('admin.tabs.content'))->icon(Heroicon::OutlinedLanguage)->schema([
                    Translatable::text('title', __('admin.fields.title'), required: true),
                    Translatable::textarea('summary', __('admin.fields.summary'), required: true, rows: 3),
                    Translatable::rich('description', __('admin.fields.description')),
                    Translatable::list('use_of_funds', __('admin.fields.use_of_funds')),
                    Translatable::textarea('impact', __('admin.fields.impact'), rows: 2),
                    Translatable::textarea('timeline', __('admin.fields.timeline'), rows: 2),
                ]),
                Tab::make(__('admin.tabs.investment'))->icon(Heroicon::OutlinedBanknotes)->schema([
                    Grid::make(['default' => 1, 'md' => 3])->schema([
                        TextInput::make('reference')->label(__('admin.fields.reference'))
                            ->placeholder(fn () => Project::nextReference())
                            ->helperText(__('admin.help.reference'))
                            ->unique(ignoreRecord: true)->maxLength(20),
                        Select::make('country')->label(__('admin.fields.country'))
                            ->options(fn () => Countries::african())->searchable()->required(),
                        Select::make('sector_id')->label(__('admin.fields.sector'))
                            ->options(fn () => Sector::options())->searchable()->required(),
                        Select::make('domain_id')->label(__('admin.fields.domain'))
                            ->options(fn () => Domain::published()->get()->mapWithKeys(fn ($d) => [$d->id => $d->numberLabel().' — '.$d->tr('title')])),
                        Select::make('stage')->label(__('admin.fields.stage'))->options(ProjectStage::class)->required(),
                        Select::make('funding_type')->label(__('admin.fields.funding_type'))->options(FundingType::class)->required(),
                        TextInput::make('investment_amount')->label(__('admin.fields.amount'))->numeric()->minValue(0)->required(),
                        Select::make('currency')->label(__('admin.fields.currency'))
                            ->options(array_combine(config('site.currencies'), config('site.currencies')))->default('USD')->required(),
                        TextInput::make('jobs_expected')->label(__('admin.fields.jobs'))->numeric()->minValue(0),
                    ]),
                ]),
                Tab::make(__('admin.tabs.media'))->icon(Heroicon::OutlinedPhoto)->schema([
                    FileUpload::make('cover_image')->label(__('admin.fields.cover'))
                        ->image()->imageEditor()->imageAspectRatio('16:10')->automaticallyResizeImagesToWidth('1600')
                        ->disk('public')->directory('projects')->maxSize(4096),
                    FileUpload::make('gallery')->label(__('admin.fields.gallery'))
                        ->image()->multiple()->reorderable()->maxFiles(8)
                        ->disk('public')->directory('projects/gallery')->maxSize(4096),
                    Grid::make(2)->schema([
                        FileUpload::make('public_document')->label(__('admin.fields.public_document'))
                            ->helperText(__('admin.help.public_document'))
                            ->acceptedFileTypes(['application/pdf'])->disk('public')->directory('projects/documents')->maxSize(10240),
                        FileUpload::make('confidential_document')->label(__('admin.fields.confidential_document'))
                            ->helperText(__('admin.help.confidential_document'))
                            ->disk('local')->directory('projects/confidential')->visibility('private')->maxSize(20480),
                    ]),
                ]),
                Tab::make(__('admin.tabs.publication'))->icon(Heroicon::OutlinedGlobeAlt)->schema([
                    Grid::make(['default' => 1, 'md' => 2])->schema([
                        Select::make('status')->label(__('admin.fields.status'))->options(ProjectStatus::class)->default('open')->required(),
                        DateTimePicker::make('published_at')->label(__('admin.fields.published_at'))->helperText(__('admin.help.published_at')),
                        Toggle::make('is_published')->label(__('admin.fields.is_published')),
                        Toggle::make('is_featured')->label(__('admin.fields.is_featured'))->helperText(__('admin.help.featured')),
                        Toggle::make('description_on_request')->label(__('admin.fields.description_on_request')),
                    ]),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')->label(__('admin.fields.reference'))->searchable()->sortable()->fontFamily('mono'),
                TextColumn::make('title')->label(__('admin.fields.title'))
                    ->state(fn (Project $record) => $record->tr('title', 'en'))
                    ->searchable(query: fn ($query, string $search) => $query->where('title', 'like', "%$search%"))
                    ->limit(50)->wrap(),
                TextColumn::make('country')->label(__('admin.fields.country'))
                    ->formatStateUsing(fn (string $state) => Countries::name($state))->sortable(),
                TextColumn::make('investment_amount')->label(__('admin.fields.amount'))
                    ->state(fn (Project $record) => $record->amountCompact('en'))->sortable(),
                TextColumn::make('status')->label(__('admin.fields.status'))->badge(),
                ViewColumn::make('translations')->label(__('admin.fields.translations'))->view('filament.columns.translation-status'),
                ToggleColumn::make('is_featured')->label(__('admin.fields.is_featured')),
                ToggleColumn::make('is_published')->label(__('admin.fields.is_published')),
                TextColumn::make('interests_count')->counts('interests')->label(__('admin.interests'))->badge()->color('success'),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('admin.fields.status'))->options(ProjectStatus::class),
                SelectFilter::make('sector_id')->label(__('admin.fields.sector'))->options(fn () => Sector::options()),
                SelectFilter::make('country')->label(__('admin.fields.country'))->options(fn () => Countries::african())->searchable(),
                TernaryFilter::make('is_published')->label(__('admin.fields.is_published')),
                TernaryFilter::make('is_featured')->label(__('admin.fields.is_featured')),
            ])
            ->recordActions([
                Action::make('preview')->label(__('admin.preview'))->icon(Heroicon::OutlinedEye)->color('gray')
                    ->url(fn (Project $record) => $record->is_published ? $record->url('en') : null, shouldOpenInNewTab: true)
                    ->visible(fn (Project $record) => $record->is_published),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
