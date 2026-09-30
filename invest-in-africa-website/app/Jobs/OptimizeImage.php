<?php

namespace App\Jobs;

use App\Models\Media;
use App\Services\ImageOptimizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/** Runs image conversion outside the request (queue worker in production). */
class OptimizeImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(public string $path, public ?int $mediaId = null) {}

    public function handle(ImageOptimizer $optimizer): void
    {
        $result = $optimizer->optimize($this->path);

        if ($result && $this->mediaId) {
            Media::query()->whereKey($this->mediaId)->update([
                'width' => $result['width'],
                'height' => $result['height'],
                'variants' => json_encode($result['variants']),
            ]);
        }
    }
}
