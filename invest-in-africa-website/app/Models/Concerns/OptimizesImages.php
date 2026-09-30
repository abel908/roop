<?php

namespace App\Models\Concerns;

use App\Jobs\OptimizeImage;
use App\Services\ImageOptimizer;

/**
 * Generates AVIF / WebP variants for the image attributes listed in
 * $optimizedImages whenever they change (§10.4).
 *
 * @property array<int, string> $optimizedImages
 */
trait OptimizesImages
{
    public static function bootOptimizesImages(): void
    {
        static::saved(function (self $model) {
            foreach ($model->optimizedImages as $attribute) {
                if (! $model->wasChanged($attribute) && ! $model->wasRecentlyCreated) {
                    continue;
                }

                $old = (array) ($model->getOriginal($attribute) ?? []);
                $new = (array) ($model->getAttribute($attribute) ?? []);

                foreach (array_diff($old, $new) as $removed) {
                    is_string($removed) && app(ImageOptimizer::class)->forget($removed);
                }

                foreach (array_filter($new, 'is_string') as $path) {
                    if (ImageOptimizer::supports($path)) {
                        OptimizeImage::dispatch($path);
                    }
                }
            }
        });
    }
}
