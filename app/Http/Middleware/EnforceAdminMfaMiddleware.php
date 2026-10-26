<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Filament\Pages\TwoFactorChallengePage;
use App\Models\User;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware Zero-Trust que impone la verificación obligatoria del 2do Factor (MFA)
 * para todas las rutas administrativas de Filament (OWASP A07).
 */
class EnforceAdminMfaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = auth()->user();

        if ($user === null) {
            return $next($request);
        }

        $panel = Filament::getCurrentPanel();
        if ($panel !== null && ! $user->canAccessPanel($panel)) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            abort(403, 'Acceso no autorizado al panel administrativo.');
        }

        // Si el usuario tiene 2FA configurado y confirmado
        if ($user->hasEnabledTwoFactor()) {
            // Permitir navegación libre hacia la página del desafío 2FA y hacia el logout
            if (
                $request->routeIs('filament.admin.pages.two-factor-challenge') ||
                $request->routeIs('filament.admin.auth.logout') ||
                $request->is('admin/two-factor-challenge*') ||
                $request->is('admin/logout*') ||
                str_contains((string) $request->header('referer'), 'admin/two-factor-challenge')
            ) {
                return $next($request);
            }

            $verifiedUserId = session('mfa_verified_for_user_id');
            $verifiedIp = session('mfa_verified_ip');

            // Exigir validación idéntica de usuario e IP de la sesión
            if ($verifiedUserId !== $user->id || $verifiedIp !== $request->ip()) {
                return redirect()->to(TwoFactorChallengePage::getUrl());
            }
        }

        return $next($request);
    }
}
