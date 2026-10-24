<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and apply OWASP A05 security headers.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // En rutas públicas, aplicar la directiva Content-Security-Policy (OWASP A05)
        if (! $request->is('admin*') && ! $request->is('livewire*')) {
            $csp = "default-src 'self'; "
                ."script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net; "
                ."style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
                ."font-src 'self' https://fonts.gstatic.com data:; "
                ."img-src 'self' data: https://images.unsplash.com; "
                ."connect-src 'self'; "
                ."frame-src 'self' https://www.google.com https://maps.google.com https://www.openstreetmap.org; "
                ."frame-ancestors 'self';";

            $response->headers->set('Content-Security-Policy', $csp);
        }

        return $response;
    }
}
