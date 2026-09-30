<?php

namespace App\Filament\Auth;

use App\Models\ActivityLog;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Back-office login with account lockout after repeated failures (§11.1
 * "verrouillage après échecs"), in addition to Filament's per-IP limit.
 */
class Login extends BaseLogin
{
    public const MAX_ATTEMPTS = 5;

    public const LOCK_SECONDS = 900;

    public function authenticate(): ?LoginResponse
    {
        $key = self::lockKey((string) ($this->data['email'] ?? ''));

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            throw ValidationException::withMessages([
                'data.email' => __('admin.auth.locked', ['minutes' => ceil(RateLimiter::availableIn($key) / 60)]),
            ]);
        }

        try {
            $response = parent::authenticate();
        } catch (ValidationException $exception) {
            RateLimiter::hit($key, self::LOCK_SECONDS);

            if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
                ActivityLog::record('auth.locked', null, ['email' => $this->data['email'] ?? null]);
            }

            throw $exception;
        }

        if ($response) {
            RateLimiter::clear($key);
        }

        return $response;
    }

    public static function lockKey(string $email): string
    {
        return 'login-lock:'.sha1(mb_strtolower(trim($email)));
    }
}
