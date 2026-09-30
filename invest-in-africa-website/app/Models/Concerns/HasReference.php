<?php

namespace App\Models\Concerns;

/**
 * Human-readable file numbers: P-2026-001, S-2026-0001, I-2026-0001, C-2026-0001.
 */
trait HasReference
{
    abstract public static function referencePrefix(): string;

    public static function referencePadding(): int
    {
        return 4;
    }

    public static function bootHasReference(): void
    {
        static::creating(function (self $model) {
            if (empty($model->reference)) {
                $model->reference = static::nextReference();
            }
        });
    }

    public static function nextReference(): string
    {
        $prefix = static::referencePrefix().'-'.now()->year.'-';

        $last = static::query()
            ->where('reference', 'like', $prefix.'%')
            ->orderByDesc('reference')
            ->value('reference');

        $sequence = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $sequence, static::referencePadding(), '0', STR_PAD_LEFT);
    }
}
