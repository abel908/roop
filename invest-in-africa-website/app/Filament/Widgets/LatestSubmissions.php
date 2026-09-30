<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Submissions\SubmissionResource;
use App\Models\Submission;
use App\Support\Countries;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestSubmissions extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->canManage('submissions') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('admin.dashboard.latest_submissions'))
            ->query(Submission::query()->latest()->limit(8))
            ->paginated(false)
            ->recordUrl(fn (Submission $record) => SubmissionResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('reference')->label(__('admin.fields.reference'))->fontFamily('mono'),
                TextColumn::make('project_name')->label(__('submit.fields.project_name'))->weight('bold')->limit(40),
                TextColumn::make('project_country')->label(__('admin.fields.country'))->formatStateUsing(fn ($state) => Countries::name($state)),
                TextColumn::make('investment_amount')->label(__('admin.fields.amount'))->state(fn (Submission $r) => $r->amountLabel()),
                TextColumn::make('status')->label(__('admin.fields.status'))->badge(),
                TextColumn::make('created_at')->label(__('admin.fields.received_at'))->since(),
            ]);
    }
}
