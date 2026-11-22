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
            // Permitir navegación libre únicamente hacia la página del desafío 2FA y hacia el logout
            if (
                $request->routeIs('filament.admin.pages.two-factor-challenge') ||
                $request->routeIs('filament.admin.auth.logout') ||
                $request->is('admin/two-factor-challenge') ||
                $request->is('admin/logout')
            ) {
                return $next($request);
            }

            // Permitir actualizaciones AJAX de Livewire exclusivamente si TODOS los componentes del payload pertenecen al desafío 2FA
            if ($this->isTwoFactorChallengeLivewireUpdate($request)) {
                return $next($request);
            }

            $verifiedUserId = session('mfa_verified_for_user_id');
            $verifiedUserAgentHash = session('mfa_verified_ua');
            $currentUserAgentHash = hash('sha256', (string) $request->userAgent());

            // Exigir validación idéntica de usuario y huella de navegador (permite movilidad 4G/5G sin falsos bloqueos por cambio de IP)
            if ($verifiedUserId !== $user->id || ! is_string($verifiedUserAgentHash) || ! hash_equals($verifiedUserAgentHash, $currentUserAgentHash)) {
                return redirect()->to(TwoFactorChallengePage::getUrl());
            }
        }

        return $next($request);
    }

    /**
     * Comprueba de forma segura (sin confiar en la cabecera Referer) si una petición de Livewire
     * corresponde exclusivamente al componente TwoFactorChallengePage.
     */
    private function isTwoFactorChallengeLivewireUpdate(Request $request): bool
    {
        if (! $request->isMethod('POST') || ! $request->is('livewire/update')) {
            return false;
        }

        $components = $request->input('components');
        if (! is_array($components) || $components === []) {
            return false;
        }

        foreach ($components as $component) {
            $snapshotRaw = is_array($component) ? ($component['snapshot'] ?? null) : null;
            $snapshot = is_string($snapshotRaw) ? json_decode($snapshotRaw, true) : null;
            $memoName = is_array($snapshot) ? ($snapshot['memo']['name'] ?? null) : null;

            if ($memoName !== 'app.filament.pages.two-factor-challenge-page') {
                return false;
            }
        }

        return true;
    }
}
