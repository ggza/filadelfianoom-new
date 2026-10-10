<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

// Cloudflare Turnstile token ellenőrzése a siteverify végponton
class Turnstile implements ValidationRule
{
    // hiányzó mezőnél is lefut, így a token nélküli beküldés is elbukik
    public bool $implicit = true;

    public static function enabled(): bool
    {
        return filled(config('services.turnstile.site_key')) && filled(config('services.turnstile.secret_key'));
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! self::enabled()) {
            return;
        }

        if (! is_string($value) || $value === '' || strlen($value) > 2048) {
            $fail(__('site.captcha_failed'));
            return;
        }

        try {
            $res = Http::asForm()->timeout(10)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('services.turnstile.secret_key'),
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);
            $ok = $res->successful() && $res->json('success') === true;
        } catch (Throwable $e) {
            Log::warning('Turnstile ellenőrzés sikertelen: ' . $e->getMessage());
            $ok = false;
        }

        if (! $ok) {
            $fail(__('site.captcha_failed'));
        }
    }
}
