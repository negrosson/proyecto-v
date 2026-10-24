<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Rate Limiters contra DoS y Spam (OWASP A04)
        RateLimiter::for('catalog-search', static function (Request $request): Limit {
            return Limit::perMinute(45)->by($request->ip() ?: '127.0.0.1');
        });

        RateLimiter::for('quote-submission', static function (Request $request): Limit {
            return Limit::perMinute(4)->by($request->ip() ?: '127.0.0.1');
        });

        RateLimiter::for('contact-submission', static function (Request $request): Limit {
            return Limit::perMinute(3)->by($request->ip() ?: '127.0.0.1');
        });

        // 2. Reglas Globales de Contraseña Estricta (OWASP A07)
        Password::defaults(static function (): Password {
            return Password::min(10)
                ->mixedCase()
                ->numbers()
                ->symbols();
        });
    }
}
