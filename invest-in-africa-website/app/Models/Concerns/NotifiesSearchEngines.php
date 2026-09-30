<?php

namespace App\Models\Concerns;

use App\Jobs\NotifySearchEngines;
use App\Services\IndexNow;
use App\Support\Locales;

/**
 * Published pages are notified to search engines in the three languages as
 * soon as they are created or updated (IndexNow).
 */
trait NotifiesSearchEngines
{
    public static function bootNotifiesSearchEngines(): void
    {
        static::saved(function (self $model) {
            if (IndexNow::enabled() && $model->isPubliclyVisible()) {
                NotifySearchEngines::dispatch(
                    collect(Locales::codes())->map(fn ($locale) => $model->url($locale))->all()
                )->afterCommit();
            }
        });
    }

    abstract public function isPubliclyVisible(): bool;
}
