<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * China-compatible anti-spam, without reCAPTCHA (§6.3, §7.6, §11.1):
 * invisible honeypot field + minimum filling time.
 */
class ProtectAgainstSpam
{
    public function handle(Request $request, Closure $next): Response
    {
        $honeypot = config('site.antispam.honeypot_field');

        if (filled($request->input($honeypot)) || $this->filledTooFast($request->input('_started'))) {
            Log::channel('stack')->warning('Spam attempt blocked', ['ip' => $request->ip(), 'path' => $request->path()]);

            throw ValidationException::withMessages(['form' => __('forms.spam_detected')]);
        }

        return $next($request);
    }

    private function filledTooFast(?string $token): bool
    {
        if (! $token) {
            return true;
        }

        try {
            $startedAt = (int) Crypt::decryptString($token);
        } catch (DecryptException) {
            return true;
        }

        return (time() - $startedAt) < config('site.antispam.min_seconds');
    }

    public static function token(): string
    {
        return Crypt::encryptString((string) time());
    }
}
