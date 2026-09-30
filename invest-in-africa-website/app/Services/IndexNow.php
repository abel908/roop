<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Notifies search engines (Bing, Yandex, Seznam, Naver…) of new or updated
 * URLs through the IndexNow protocol — no manual submission (§10.1).
 */
class IndexNow
{
    public static function key(): string
    {
        return config('site.indexnow.key') ?: substr(hash_hmac('sha256', 'indexnow', (string) config('app.key')), 0, 32);
    }

    public static function enabled(): bool
    {
        return (bool) config('site.indexnow.enabled') && filled(config('app.key'));
    }

    /** @param array<int, string> $urls */
    public function submit(array $urls): bool
    {
        $urls = array_values(array_unique(array_filter($urls)));

        if (! $urls || ! self::enabled()) {
            return false;
        }

        $host = parse_url(config('app.url'), PHP_URL_HOST);

        try {
            $response = Http::timeout(15)->post(config('site.indexnow.endpoint'), [
                'host' => $host,
                'key' => self::key(),
                'keyLocation' => rtrim(config('app.url'), '/').'/'.self::key().'.txt',
                'urlList' => array_slice($urls, 0, 10000),
            ]);
        } catch (\Throwable $e) {
            Log::info('IndexNow submission failed', ['error' => $e->getMessage()]);

            return false;
        }

        return $response->successful();
    }
}
