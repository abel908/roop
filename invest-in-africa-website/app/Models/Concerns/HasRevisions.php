<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use App\Models\Revision;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Keeps the previous version of the record on every change so that an
 * earlier version can be restored from the back-office (§8.1).
 */
trait HasRevisions
{
    /** Number of versions kept per record. */
    public static int $revisionsKept = 30;

    public static function bootHasRevisions(): void
    {
        static::updating(function (self $model) {
            $changed = array_diff(array_keys($model->getDirty()), ['updated_at', 'updated_by']);

            if (! $changed) {
                return;
            }

            $model->revisions()->create([
                'snapshot' => $model->revisionSnapshot($model->getRawOriginal()),
                'user_id' => auth()->id(),
            ]);

            $model->revisions()->orderByDesc('id')->skip(static::$revisionsKept)->take(PHP_INT_MAX)->get()->each->delete();
        });
    }

    public function revisions(): MorphMany
    {
        return $this->morphMany(Revision::class, 'revisionable')->latest('id');
    }

    /** @param  array<string, mixed>  $attributes  raw database attributes */
    protected function revisionSnapshot(array $attributes): array
    {
        return array_diff_key($attributes, array_flip(['id', 'created_at', 'updated_at']));
    }

    public function restoreRevision(Revision $revision): void
    {
        $attributes = array_intersect_key($revision->snapshot, array_flip($this->getFillable()));

        // Raw values: JSON columns are stored as strings in the snapshot.
        $this->setRawAttributes(array_merge($this->getAttributes(), $attributes));

        $this->save();

        ActivityLog::record(strtolower(class_basename($this)).'.restored', $this, ['revision' => $revision->id]);
    }
}
