<x-filament-panels::page>
    <div style="max-width: 28rem; margin: 1.5rem auto; width: 100%;">
        <div class="tfa-card tfa-card-accent-amber">
            <div style="text-align: center;">
                <div class="tfa-icon-box tfa-icon-amber" style="margin: 0 auto 1rem;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>

                <span class="tfa-badge tfa-badge-amber">
                    <span class="tfa-dot tfa-dot-amber"></span>
                    Desafío 2FA · RFC 6238
                </span>

                <h2 class="tfa-title" style="margin-top: 0.75rem;">
                    Verificación en Dos Pasos
                </h2>
                <p class="tfa-subtitle" style="font-size: 0.8125rem;">
                    {{ $useRecoveryCode ? 'Ingresa uno de tus códigos de emergencia de un solo uso.' : 'Abre tu aplicación autenticadora e introduce el código dinámico de 6 dígitos.' }}
                </p>
            </div>

            <div class="tfa-divider"></div>

            <form wire:submit.prevent="submitChallenge" style="display:flex; flex-direction:column; gap:1.15rem;">
                @if(!$useRecoveryCode)
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.45rem;">
                            <label for="code" class="tfa-label" style="margin:0;">Código de Autenticación</label>
                            <span style="font-size:0.7rem; font-family:ui-monospace, monospace; color:#71717a;">6 Dígitos</span>
                        </div>
                        <input type="text"
                               id="code"
                               wire:model.defer="code"
                               maxlength="6"
                               inputmode="numeric"
                               pattern="[0-9]*"
                               autocomplete="one-time-code"
                               placeholder="000000"
                               autofocus
                               class="tfa-input tfa-input-otp">
                        @error('code')
                            <p class="tfa-error" style="text-align:center;">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.45rem;">
                            <label for="recovery_code" class="tfa-label" style="margin:0;">Código de Emergencia</label>
                            <span style="font-size:0.7rem; font-family:ui-monospace, monospace; color:#71717a;">1 Solo Uso</span>
                        </div>
                        <input type="text"
                               id="recovery_code"
                               wire:model.defer="recovery_code"
                               placeholder="XXXXX-XXXXX"
                               autofocus
                               class="tfa-input"
                               style="text-align:center; font-family:ui-monospace, monospace; font-weight:700; font-size:1.1rem; letter-spacing:0.15em; text-transform:uppercase;">
                        @error('recovery_code')
                            <p class="tfa-error" style="text-align:center;">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <button type="submit" wire:loading.attr="disabled" class="tfa-btn tfa-btn-primary tfa-btn-full" style="padding-top:0.9rem; padding-bottom:0.9rem;">
                    <span wire:loading.remove wire:target="submitChallenge" style="display:inline-flex; align-items:center; gap:0.45rem;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Verificar e Ingresar al Panel
                    </span>
                    <span wire:loading wire:target="submitChallenge">
                        Validando código...
                    </span>
                </button>
            </form>

            <div class="tfa-footer-bar" style="flex-direction:column; align-items:center; gap:0.85rem; padding-top:1rem; margin-top:1.25rem;">
                <button type="button"
                        wire:click="$toggle('useRecoveryCode')"
                        style="background:none; border:none; cursor:pointer; font-size:0.78rem; font-weight:600; color:#71717a; text-decoration:underline;">
                    {{ $useRecoveryCode ? 'Volver al código de 6 dígitos de la app' : '¿Sin acceso al teléfono? Usar código de emergencia' }}
                </button>

                <div style="display:flex; align-items:center; justify-content:center; gap:1rem; flex-wrap:wrap;">
                    <form method="POST" action="{{ \Filament\Facades\Filament::getLogoutUrl() }}" style="margin:0;">
                        @csrf
                        <button type="submit"
                                style="background:none; border:none; cursor:pointer; font-size:0.78rem; font-weight:600; color:#ef4444; text-decoration:underline;">
                            Cerrar sesión y volver al login
                        </button>
                    </form>

                    <span style="color:#d4d4d8;">·</span>

                    <a href="{{ route('catalog.index') }}"
                       style="font-size:0.78rem; font-weight:600; color:#71717a; text-decoration:underline;">
                        Ir al catálogo público
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
