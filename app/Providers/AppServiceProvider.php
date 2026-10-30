<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
        // 0. Detección Dinámica de Host y Esquema para Proxies Reversos / Túneles (Ngrok / HTTPS)
        if (! $this->app->runningInConsole()) {
            if (request()->header('x-forwarded-proto') === 'https' || request()->isSecure()) {
                URL::forceScheme('https');
            }

            $currentHost = request()->getSchemeAndHttpHost();
            if ($currentHost && ! str_contains($currentHost, '127.0.0.1:0')) {
                URL::forceRootUrl($currentHost);
                config(['filesystems.disks.public.url' => $currentHost.'/storage']);
            }
        }
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

        RateLimiter::for('product-telemetry', static function (Request $request): Limit {
            return Limit::perMinute(60)->by($request->ip() ?: '127.0.0.1');
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
