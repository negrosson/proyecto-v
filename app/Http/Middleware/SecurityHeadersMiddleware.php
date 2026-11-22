<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Aplica cabeceras de contención defensiva y blindaje HTTP (OWASP A05 / Zero-Trust).
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 0. Sincronización Segura de Host y Esquema (solo confía en esquema HTTPS real o proxies confiables configurados)
        $isHttps = $request->isSecure();

        if (app()->isProduction()) {
            $appUrl = rtrim((string) config('app.url', ''), '/');
            if ($appUrl !== '') {
                URL::forceRootUrl($appUrl);
                config(['filesystems.disks.public.url' => $appUrl.'/storage']);
            }
            if ($isHttps || str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
        } else {
            if ($isHttps) {
                URL::forceScheme('https');
            }

            $schemeAndHost = $request->getSchemeAndHttpHost();
            if ($schemeAndHost && ! str_contains($schemeAndHost, '127.0.0.1:0')) {
                URL::forceRootUrl($schemeAndHost);
                config(['filesystems.disks.public.url' => $schemeAndHost.'/storage']);
                // Reiniciar la instancia del disco public si ya fue resuelta previamente
                Storage::forgetDisk('public');
            }
        }

        $response = $next($request);

        // HSTS estricto bajo HTTPS en producción (OWASP A02 / A05)
        if (app()->isProduction() && $isHttps) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Anti-Clickjacking: DENY estricto en rutas administrativas y SAMEORIGIN en catálogo público
        if ($request->is('admin*')) {
            $response->headers->set('X-Frame-Options', 'DENY');
        } else {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        }

        // Mitigación de MIME-Sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Restricciones de dominios cruzados
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Content-Security-Policy diferenciada:
        // - Rutas públicas: CSP estricta 100% self-hosted para scripts (Alpine.js local sin CDN de terceros)
        // - Panel /admin: CSP base compatible con Filament/Livewire que refuerza anti-clickjacking y bloquea plugins/base hijack
        if ($request->is('admin*')) {
            $response->headers->set(
                'Content-Security-Policy',
                "frame-ancestors 'none'; object-src 'none'; base-uri 'self'; form-action 'self';"
            );
        } elseif (! $request->is('livewire*')) {
            $csp = "default-src 'self'; "
                ."script-src 'self' 'unsafe-inline' 'unsafe-eval'; "
                ."style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
                ."font-src 'self' https://fonts.gstatic.com data:; "
                ."img-src 'self' data: https://images.unsplash.com; "
                ."connect-src 'self'; "
                ."frame-src 'self' https://www.google.com https://maps.google.com https://www.openstreetmap.org; "
                ."object-src 'none'; "
                ."base-uri 'self'; "
                ."form-action 'self'; "
                ."frame-ancestors 'self';";

            $response->headers->set('Content-Security-Policy', $csp);
        }

        return $response;
    }
}
