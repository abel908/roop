<?php

namespace App\Filament\Resources\Submissions\Pages;

use App\Enums\SubmissionStatus;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Submissions\SubmissionResource;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Submission;
use App\Support\Countries;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewSubmission extends ViewRecord
{
    protected static string $resource = SubmissionResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        ActivityLog::record('submission.viewed', $this->record);
    }

    protected function getHeaderActions(): array
    {
        return [
            // A file received through Submit a Project can become a project sheet after validation (§6.2).
            Action::make('convert')
                ->label(__('admin.actions.convert'))
                ->icon(Heroicon::OutlinedArrowRightCircle)
                ->color('success')
                ->requiresConfirmation()
                ->modalDescription(__('admin.actions.convert_help'))
                ->visible(fn (Submission $record) => ! $record->project()->exists() && auth()->user()->canManage('projects'))
                ->action(function (Submission $record) {
                    $project = Project::query()->create([
                        'title' => ['en' => $record->project_name, $record->locale => $record->project_name],
                        'summary' => [$record->locale => str($record->description)->limit(280)->toString()],
                        'description' => [$record->locale => '<p>'.e($record->description).'</p>'],
                        'timeline' => [$record->locale => $record->timeline],
                        'country' => $record->project_country,
                        'region' => Countries::regionOf($record->project_country) ?? 'west',
                        'sector_id' => $record->sector_id,
                        'stage' => $record->stage,
                        'investment_amount' => $record->investment_amount,
                        'currency' => $record->currency,
                        'funding_type' => $record->funding_type,
                        'status' => 'open',
                        'is_published' => false,
                        'submission_id' => $record->id,
                    ]);

                    $record->update(['status' => SubmissionStatus::Shortlisted]);
                    ActivityLog::record('submission.converted', $record, ['project' => $project->reference]);

                    Notification::make()->success()->title(__('admin.actions.converted', ['reference' => $project->reference]))->send();

                    $this->redirect(ProjectResource::getUrl('edit', ['record' => $project]));
                }),
            Action::make('project')
                ->label(__('admin.actions.open_project'))
                ->icon(Heroicon::OutlinedBriefcase)
                ->color('gray')
                ->visible(fn (Submission $record) => $record->project()->exists())
                ->url(fn (Submission $record) => ProjectResource::getUrl('edit', ['record' => $record->project])),
            EditAction::make()->label(__('admin.actions.process')),
        ];
    }
}
