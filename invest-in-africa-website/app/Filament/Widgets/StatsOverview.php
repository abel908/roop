<?php

namespace App\Filament\Widgets;

use App\Enums\RequestStatus;
use App\Enums\SubmissionStatus;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\ContentTranslations\ContentTranslationResource;
use App\Filament\Resources\InterestExpressions\InterestExpressionResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Submissions\SubmissionResource;
use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\InterestExpression;
use App\Models\Project;
use App\Models\Submission;
use App\Services\Translator;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/** Dashboard overview (§8.2): new files, expressions of interest, messages, contents to translate. */
class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $user = auth()->user();
        $stats = [];

        if ($user->canManage('submissions')) {
            $stats[] = Stat::make(__('admin.dashboard.new_submissions'), Submission::query()->where('status', SubmissionStatus::Received)->count())
                ->description(__('admin.dashboard.total', ['count' => Submission::query()->count()]))
                ->icon('heroicon-o-inbox-arrow-down')->color('success')
                ->url(SubmissionResource::getUrl('index'));
        }

        if ($user->canManage('projects')) {
            $stats[] = Stat::make(__('admin.dashboard.new_interests'), InterestExpression::query()->where('status', RequestStatus::New)->count())
                ->description(__('admin.dashboard.total', ['count' => InterestExpression::query()->count()]))
                ->icon('heroicon-o-hand-raised')->color('success')
                ->url(InterestExpressionResource::getUrl('index'));
            $stats[] = Stat::make(__('admin.dashboard.published_projects'), Project::published()->count())
                ->description(__('admin.dashboard.drafts', ['count' => Project::query()->where('is_published', false)->count()]))
                ->icon('heroicon-o-briefcase')
                ->url(ProjectResource::getUrl('index'));
        }

        if ($user->canManage('contact_messages')) {
            $stats[] = Stat::make(__('admin.dashboard.new_messages'), ContactMessage::query()->where('status', RequestStatus::New)->count())
                ->description(__('admin.dashboard.total', ['count' => ContactMessage::query()->count()]))
                ->icon('heroicon-o-envelope')
                ->url(ContactMessageResource::getUrl('index'));
        }

        if ($user->canManage('translations') && Translator::enabled()) {
            $auto = ActivityLog::query()->where('action', 'translation.auto')->where('created_at', '>=', now()->subDays(30))->count();
            $stats[] = Stat::make(__('admin.dashboard.auto_translations'), $auto)
                ->description(__('admin.dashboard.auto_translations_help'))
                ->icon('heroicon-o-sparkles');
        }

        if ($user->canManage('translations')) {
            $toTranslate = Project::query()->get()->filter(fn (Project $p) => in_array(false, $p->translationStatus(), true))->count();
            $stats[] = Stat::make(__('admin.dashboard.to_translate'), $toTranslate)
                ->description(__('admin.dashboard.to_translate_help'))
                ->icon('heroicon-o-language')->color($toTranslate ? 'warning' : 'success')
                ->url(ContentTranslationResource::getUrl('index'));
        }

        return $stats;
    }
}
