<?php

namespace App\Services;

use App\Enums\RequestStatus;
use App\Enums\SubmissionStatus;
use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\InterestExpression;
use App\Models\Setting;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Personal data protection (§11.2): retention periods applied automatically
 * and anonymisation on request (right to erasure). Durations are set in the
 * back-office — "À valider par The Invest In Africa Initiative".
 */
class DataRetention
{
    public const ANONYMISED = '[anonymised]';

    /** Default retention in months, per type of request, once processed. */
    public const DEFAULTS = ['contact_messages' => 24, 'interests' => 36, 'submissions' => 60, 'activity_logs' => 12];

    public static function months(string $type): int
    {
        return (int) (Setting::get("retention.$type") ?: self::DEFAULTS[$type]);
    }

    /** @return array<string, int> number of records anonymised / deleted per type */
    public function purge(): array
    {
        $result = [];

        $result['contact_messages'] = $this->anonymiseAll(ContactMessage::query()
            ->where('status', RequestStatus::Closed)
            ->where('full_name', '!=', self::ANONYMISED)
            ->where('updated_at', '<', now()->subMonths(self::months('contact_messages'))));

        $result['interests'] = $this->anonymiseAll(InterestExpression::query()
            ->where('status', RequestStatus::Closed)
            ->where('full_name', '!=', self::ANONYMISED)
            ->where('updated_at', '<', now()->subMonths(self::months('interests'))));

        $result['submissions'] = $this->anonymiseAll(Submission::query()
            ->whereIn('status', [SubmissionStatus::Archived, SubmissionStatus::Declined])
            ->where('full_name', '!=', self::ANONYMISED)
            ->where('updated_at', '<', now()->subMonths(self::months('submissions'))));

        $result['activity_logs'] = ActivityLog::query()
            ->where('created_at', '<', now()->subMonths(self::months('activity_logs')))
            ->delete();

        return $result;
    }

    private function anonymiseAll($query): int
    {
        $count = 0;
        $query->each(function (Model $record) use (&$count) {
            $this->anonymise($record, automatic: true);
            $count++;
        });

        return $count;
    }

    /** Removes the personal data of a request while keeping anonymous statistics. */
    public function anonymise(Model $record, bool $automatic = false): void
    {
        $record->forceFill([
            'full_name' => self::ANONYMISED,
            'email' => 'anonymised+'.$record->getKey().'@invalid',
            'phone' => $record instanceof ContactMessage ? null : '—',
            'organization' => $record instanceof Submission ? self::ANONYMISED : null,
            'ip_address' => null,
            'internal_notes' => null,
        ]);

        if ($record instanceof Submission) {
            $record->forceFill(['position' => self::ANONYMISED, 'user_agent' => null]);

            foreach ($record->documents as $document) {
                Storage::disk('local')->delete($document->path);
                $document->delete();
            }
        }

        if ($record instanceof ContactMessage || $record instanceof InterestExpression) {
            $record->forceFill(['message' => $record instanceof ContactMessage ? self::ANONYMISED : null]);
        }

        $record->saveQuietly();

        ActivityLog::record($automatic ? 'data.retention_purge' : 'data.anonymised', $record);
    }
}
