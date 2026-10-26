<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        $response = $next($request);

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

        // En rutas públicas, aplicar la directiva Content-Security-Policy (CSP) estricta
        if (! $request->is('admin*') && ! $request->is('livewire*')) {
            $csp = "default-src 'self'; "
                ."script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net; "
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
