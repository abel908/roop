<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Self-hosted, adaptive captcha (§6.3 "captcha compatible avec les visiteurs
 * de Chine", §7.6): no third-party service. A simple arithmetic question,
 * readable by screen readers, is only asked after a suspicious attempt or
 * when the same IP address sends several forms within an hour.
 */
class Captcha
{
    private const SESSION_ANSWER = 'captcha.answer';

    private const SESSION_REQUIRED = 'captcha.required';

    public const THRESHOLD = 2;

    public static function key(Request $request): string
    {
        return 'form-submissions:'.$request->ip();
    }

    public static function required(Request $request): bool
    {
        return (bool) $request->session()->get(self::SESSION_REQUIRED)
            || RateLimiter::attempts(self::key($request)) >= self::THRESHOLD;
    }

    public static function requireFor(Request $request): void
    {
        $request->session()->put(self::SESSION_REQUIRED, true);
    }

    /** Generates a new question and keeps the expected answer in the session. */
    public static function question(Request $request): string
    {
        $a = random_int(2, 9);
        $b = random_int(1, 9);
        $request->session()->put(self::SESSION_ANSWER, hash('sha256', (string) ($a + $b)));

        return __('forms.captcha.question', ['a' => $a, 'b' => $b]);
    }

    public static function verify(Request $request): bool
    {
        $expected = $request->session()->pull(self::SESSION_ANSWER);
        $answer = trim((string) $request->input('captcha'));

        return $expected !== null && $answer !== '' && hash_equals($expected, hash('sha256', $answer));
    }

    /** Called after every successful submission. */
    public static function recordSubmission(Request $request): void
    {
        RateLimiter::hit(self::key($request), 3600);
        $request->session()->forget(self::SESSION_REQUIRED);
    }
}
