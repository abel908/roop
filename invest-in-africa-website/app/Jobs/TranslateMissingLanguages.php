<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\ContentTranslation;
use App\Models\Setting;
use App\Services\Translator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

/**
 * Completes the missing languages of a record or of settings after they are
 * saved in the back-office, so every page exists in EN / FR / 中文 (§7).
 * Runs in the queue: saving is never slowed down.
 */
class TranslateMissingLanguages implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 2;

    public int $uniqueFor = 60;

    /**
     * @param  class-string<Model>|null  $model
     * @param  array<int, string>  $settingKeys
     */
    public function __construct(
        public ?string $model = null,
        public int|string|null $id = null,
        public array $settingKeys = [],
    ) {}

    public function uniqueId(): string
    {
        return $this->model ? $this->model.':'.$this->id : 'settings:'.implode(',', $this->settingKeys);
    }

    public function handle(Translator $translator): void
    {
        if (! Translator::enabled()) {
            return;
        }

        if ($this->model) {
            $record = $this->model::query()->find($this->id);
            $record && $this->translateRecord($record, $translator);

            return;
        }

        foreach ($this->settingKeys as $key) {
            [$value, $count] = $translator->fill((array) Setting::get($key, []));

            if ($count) {
                Setting::put($key, $value);
                ActivityLog::record('translation.auto', null, ['setting' => $key, 'texts' => $count]);
            }
        }
    }

    private function translateRecord(Model $record, Translator $translator): void
    {
        if ($record instanceof ContentTranslation) {
            [$values, $count] = $translator->fill(['text' => $record->only(['en', 'fr', 'zh'])]);
        } else {
            $attributes = $record->autoTranslatedAttributes();
            [$values, $count] = $translator->fill($record->only($attributes));
        }

        if (! $count && ! method_exists($record, 'completeSlugs')) {
            return;
        }

        $record instanceof ContentTranslation
            ? $record->forceFill($values['text'])
            : $record->forceFill($values);

        if (method_exists($record, 'completeSlugs')) {
            $record->completeSlugs();
        }

        if ($record->isDirty()) {
            // Quiet save: no new revision, no new translation job.
            $record->saveQuietly();

            if ($count) {
                ActivityLog::record('translation.auto', $record, ['texts' => $count]);
            }
        }
    }

    /** French slug from the French title, Chinese slug kept in Latin characters (§7.4). */
    public static function slugsFor(array $slug, array $title): array
    {
        $slug['en'] = $slug['en'] ?? null ?: Str::slug($title['en'] ?? '');
        $slug['fr'] = $slug['fr'] ?? null ?: Str::slug($title['fr'] ?? '') ?: $slug['en'];
        $slug['zh'] = $slug['zh'] ?? null ?: $slug['en'];

        return $slug;
    }
}
