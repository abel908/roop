<?php

namespace App\Services;

use App\Mail\BrandedMail;
use App\Models\ContactMessage;
use App\Models\InterestExpression;
use App\Models\Setting;
use App\Models\Submission;
use App\Models\User;
use App\Support\Countries;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Transactional emails (§6.5) + internal back-office notifications (§8.4).
 * Visitor emails are sent in the visitor's language; team emails in English
 * (the back-office language can be changed per user).
 */
class Notifier
{
    public function submissionReceived(Submission $submission): void
    {
        $replace = ['reference' => $submission->reference, 'name' => $submission->full_name, 'project' => $submission->project_name];

        $this->send($submission->email, $submission->locale, new BrandedMail('submission_received', $replace, [
            __('forms.attributes.project_name', [], $submission->locale) => $submission->project_name,
            __('forms.reference', [], $submission->locale) => $submission->reference,
        ]));

        $this->send($this->recipients('submissions'), config('site.default_locale'), new BrandedMail('submission_team', $replace, [
            'Reference' => $submission->reference,
            'Project' => $submission->project_name,
            'Country' => Countries::name($submission->project_country, 'en'),
            'Amount' => $submission->amountLabel('en'),
            'Holder' => "{$submission->full_name} — {$submission->organization}",
            'Email' => $submission->email,
            'Documents' => (string) $submission->documents()->count(),
        ], url('/admin/submissions/'.$submission->id), $submission->email));

        $this->notifyTeam('submissions', "New project submitted — {$submission->reference}", $submission->project_name, '/admin/submissions/'.$submission->id);
    }

    public function submissionStatusChanged(Submission $submission): void
    {
        $this->send($submission->email, $submission->locale, new BrandedMail('submission_status', [
            'reference' => $submission->reference,
            'name' => $submission->full_name,
            'project' => $submission->project_name,
            'status' => $submission->status->getLabel(),
        ]));
    }

    public function interestReceived(InterestExpression $interest): void
    {
        $project = $interest->project;
        $replace = ['reference' => $interest->reference, 'name' => $interest->full_name, 'project' => $project->tr('title', $interest->locale), 'project_reference' => $project->reference];

        $this->send($interest->email, $interest->locale, new BrandedMail('interest_received', $replace, [
            __('forms.project', [], $interest->locale) => "{$project->reference} — {$project->tr('title', $interest->locale)}",
            __('forms.reference', [], $interest->locale) => $interest->reference,
        ], $project->url($interest->locale)));

        $this->send($this->recipients('interests'), config('site.default_locale'), new BrandedMail('interest_team', $replace, [
            'Reference' => $interest->reference,
            'Project' => "{$project->reference} — {$project->tr('title', 'en')}",
            'Investor' => "{$interest->full_name} ({$interest->investor_type->value})",
            'Organization' => $interest->organization ?: '—',
            'Email' => $interest->email,
            'Phone' => $interest->phone,
            'Country' => Countries::name($interest->country, 'en'),
            'Amount considered' => $interest->amountLabel('en'),
        ], url('/admin/interest-expressions/'.$interest->id), $interest->email));

        $this->notifyTeam('projects', "New expression of interest — {$project->reference}", $interest->full_name, '/admin/interest-expressions/'.$interest->id);
    }

    public function contactReceived(ContactMessage $message): void
    {
        $replace = ['reference' => $message->reference, 'name' => $message->full_name];

        $this->send($message->email, $message->locale, new BrandedMail('contact_received', $replace, [
            __('forms.attributes.subject', [], $message->locale) => $message->subject->getLabel(),
            __('forms.reference', [], $message->locale) => $message->reference,
        ]));

        // Routing to the relevant team according to the subject (§5.6).
        $this->send($this->recipients('contact_'.$message->subject->value), config('site.default_locale'), new BrandedMail('contact_team', $replace + ['subject' => $message->subject->value], [
            'Reference' => $message->reference,
            'Subject' => $message->subject->value,
            'Name' => $message->full_name,
            'Organization' => $message->organization ?: '—',
            'Email' => $message->email,
            'Phone' => $message->phone ?: '—',
            'Message' => $message->message,
        ], url('/admin/contact-messages/'.$message->id), $message->email));

        $this->notifyTeam('contact_messages', "New message — {$message->reference}", $message->full_name, '/admin/contact-messages/'.$message->id);
    }

    /** @return array<int, string> */
    public function recipients(string $channel): array
    {
        $configured = Setting::get("notifications.$channel");

        if ($channel !== 'submissions' && $channel !== 'interests' && empty($configured)) {
            $configured = Setting::get('notifications.contact_other');
        }

        $emails = collect(is_array($configured) ? $configured : preg_split('/[\s,;]+/', (string) $configured))
            ->filter(fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->values()
            ->all();

        return $emails ?: [config('mail.team_address', config('mail.from.address'))];
    }

    /** @param string|array<int, string> $to */
    private function send(string|array $to, string $locale, BrandedMail $mail): void
    {
        try {
            Mail::to($to)->locale($locale)->queue($mail);
        } catch (\Throwable $e) {
            Log::error('Transactional email failed', ['template' => $mail->template, 'error' => $e->getMessage()]);
        }
    }

    private function notifyTeam(string $module, string $title, string $body, string $url): void
    {
        try {
            $users = User::query()->where('is_active', true)->get()->filter(fn (User $u) => $u->canManage($module));

            Notification::make()
                ->title($title)
                ->body($body)
                ->icon('heroicon-o-inbox-arrow-down')
                ->actions([Action::make('open')->label('Open')->url(url($url))])
                ->sendToDatabase($users);
        } catch (\Throwable $e) {
            Log::warning('Back-office notification failed', ['error' => $e->getMessage()]);
        }
    }
}
