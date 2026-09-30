<?php

namespace App\Jobs;

use App\Services\IndexNow;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class NotifySearchEngines implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;

    public array $backoff = [60, 600];

    /** @param array<int, string> $urls */
    public function __construct(public array $urls) {}

    public function handle(IndexNow $indexNow): void
    {
        $indexNow->submit($this->urls);
    }
}
