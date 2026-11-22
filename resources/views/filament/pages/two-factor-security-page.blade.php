<x-filament-panels::page>
    <div class="tfa-shell">
        @if(!$isEnabled && !$isConfiguring)
            <!-- ESTADO: DESACTIVADO (Layout equilibrado 2 columnas) -->
            <div class="tfa-layout-grid">
                <!-- Columna Principal: Invitación + 3 Pasos + Acción -->
                <div class="tfa-card tfa-card-accent-amber">
                    <div class="tfa-header-row">
                        <div class="tfa-header-main">
                            <div class="tfa-icon-box tfa-icon-amber">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <div>
                                <div style="display:flex; flex-wrap:wrap; align-items:center; gap:0.5rem; margin-bottom:0.35rem;">
                                    <span class="tfa-badge tfa-badge-amber">
                                        <span class="tfa-dot tfa-dot-amber"></span>
                                        Acción Recomendada
                                    </span>
                                </div>
                                <h3 class="tfa-title">Protege tu panel administrativo con 2FA</h3>
                                <p class="tfa-subtitle">
                                    Añade una segunda capa de seguridad que solicita un código temporal de 6 dígitos desde tu teléfono cada vez que inicies sesión en producción.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="tfa-divider"></div>

                    <!-- Flujo visual en 3 pasos claros -->
                    <div style="margin-bottom: 1.25rem;">
                        <span class="tfa-label">Cómo se activa en menos de 1 minuto</span>
                        <div class="tfa-steps-grid">
                            <div class="tfa-step-box">
                                <span class="tfa-step-num">01</span>
                                <span class="tfa-step-title">Abre tu app autenticadora</span>
                                <span class="tfa-step-desc">Usa Google Authenticator, Microsoft Authenticator, Authy o 1Password.</span>
                            </div>
                            <div class="tfa-step-box">
                                <span class="tfa-step-num">02</span>
                                <span class="tfa-step-title">Escanea el código QR</span>
                                <span class="tfa-step-desc">Apunta la cámara o copia la clave secreta cifrada en 1 clic.</span>
                            </div>
                            <div class="tfa-step-box">
                                <span class="tfa-step-num">03</span>
                                <span class="tfa-step-title">Confirma y guarda respaldos</span>
                                <span class="tfa-step-desc">Ingresa el código de 6 dígitos y guarda tus 8 códigos de emergencia.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Apps compatibles -->
                    <div class="tfa-apps-row">
                        <span style="font-size:0.75rem; font-weight:600; color:#71717a; margin-right:0.25rem;">Compatible con:</span>
                        <span class="tfa-app-pill">Google Authenticator</span>
                        <span class="tfa-app-pill">Microsoft Authenticator</span>
                        <span class="tfa-app-pill">Authy</span>
                        <span class="tfa-app-pill">1Password</span>
                    </div>

                    <!-- Barra de Acción -->
                    <div class="tfa-footer-bar">
                        <span style="display:inline-flex; align-items:center; gap:0.45rem; font-size:0.78rem; color:#71717a;">
                            <svg style="width:1.05rem; height:1.05rem; color:#10b981; flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Bloqueo automático ante 5 intentos fallidos
                        </span>

                        <button type="button" wire:click="startConfiguration" wire:loading.attr="disabled" class="tfa-btn tfa-btn-primary">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span wire:loading.remove wire:target="startConfiguration">Activar Doble Factor (2FA)</span>
                            <span wire:loading wire:target="startConfiguration">Generando QR seguro...</span>
                        </button>
                    </div>
                </div>

                <!-- Columna Lateral: Estado Actual y Garantías -->
                <div class="tfa-card">
                    <span class="tfa-label">Estado de tu cuenta</span>
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:0.75rem; margin-top:0.25rem;">
                        <div style="min-width:0;">
                            <div style="font-size:0.9rem; font-weight:700; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                {{ auth()->user()?->email }}
                            </div>
                            <div style="font-size:0.75rem; color:#71717a; margin-top:0.15rem;">
                                Nivel actual: Solo contraseña
                            </div>
                        </div>
                        <span class="tfa-badge tfa-badge-amber">Inactivo</span>
                    </div>

                    <div class="tfa-side-list">
                        <div class="tfa-side-item">
                            <svg style="color:#f59e0b;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                            </svg>
                            <div>
                                <div class="tfa-step-title">Estándar bancario TOTP (RFC 6238)</div>
                                <div class="tfa-step-desc">Códigos de 6 dígitos que caducan cada 30 segundos y no se pueden reutilizar.</div>
                            </div>
                        </div>

                        <div class="tfa-side-item">
                            <svg style="color:#10b981;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <div>
                                <div class="tfa-step-title">Secretos cifrados AES-256</div>
                                <div class="tfa-step-desc">Tu llave privada y códigos de respaldo se guardan cifrados en la base de datos.</div>
                            </div>
                        </div>

                        <div class="tfa-side-item">
                            <svg style="color:#0284c7;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            <div>
                                <div class="tfa-step-title">Acceso de respaldo garantizado</div>
                                <div class="tfa-step-desc">Al finalizar recibirás 8 códigos de emergencia por si cambias o pierdes tu celular.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($isConfiguring)
            <!-- ESTADO: ASISTENTE DE CONFIGURACIÓN PASO A PASO -->
            <div class="tfa-card tfa-card-accent-amber">
                <div class="tfa-header-row">
                    <div class="tfa-header-main">
                        <div class="tfa-icon-box tfa-icon-amber">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="tfa-badge tfa-badge-amber" style="margin-bottom:0.35rem;">
                                <span class="tfa-dot tfa-dot-amber"></span>
                                Asistente de Vinculación
                            </span>
                            <h3 class="tfa-title">Vincular tu aplicación autenticadora</h3>
                            <p class="tfa-subtitle">
                                Escanea el código QR con tu teléfono y luego confirma con el código de 6 dígitos generado.
                            </p>
                        </div>
                    </div>

                    <button type="button" wire:click="cancelConfiguration" class="tfa-btn tfa-btn-secondary">
                        &times; Cancelar
                    </button>
                </div>

                <div class="tfa-divider"></div>

                <!-- PASO 1: QR y Clave Manual -->
                <div style="margin-bottom: 1.75rem;">
                    <div style="display:flex; align-items:center; gap:0.65rem; margin-bottom:0.9rem;">
                        <span class="tfa-step-num">01</span>
                        <span style="font-size:0.875rem; font-weight:700;">Escanea el código QR en tu app</span>
                    </div>

                    <div class="tfa-inner tfa-qr-grid">
                        <div style="text-align:center;">
                            <div class="tfa-qr-frame">
                                <img src="{{ $setupQrUri }}" alt="Código QR de configuración 2FA">
                            </div>
                            <span style="display:block; margin-top:0.6rem; font-size:0.72rem; color:#71717a; font-family:ui-monospace, monospace;">
                                Emisor: VYS Eventos
                            </span>
                        </div>

                        <div x-data="{ copied: false }">
                            <span class="tfa-label">¿Prefieres ingresarlo manualmente?</span>
                            <p class="tfa-step-desc" style="margin-top:0.2rem;">
                                En tu app elige <em>«Ingresar clave de configuración»</em> y pega esta llave secreta:
                            </p>

                            <div class="tfa-secret-box">
                                <code class="tfa-secret-code">{{ $setupSecret }}</code>
                                <button type="button"
                                        @click="navigator.clipboard.writeText('{{ $setupSecret }}'); copied = true; setTimeout(() => copied = false, 2200)"
                                        class="tfa-btn tfa-btn-secondary">
                                    <span x-show="!copied">Copiar</span>
                                    <span x-show="copied" style="color:#10b981; display:none;">¡Copiado!</span>
                                </button>
                            </div>

                            <div style="margin-top:0.9rem; display:flex; flex-wrap:wrap; gap:0.5rem;">
                                <span class="tfa-app-pill">Tipo: TOTP (30s)</span>
                                <span class="tfa-app-pill">Algoritmo: SHA-1 · 6 dígitos</span>
                                <span class="tfa-app-pill">Almacenamiento: AES-256</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 2: Verificación y Activación -->
                <form wire:submit.prevent="confirmConfiguration">
                    <div style="display:flex; align-items:center; gap:0.65rem; margin-bottom:0.9rem;">
                        <span class="tfa-step-num" style="background:#059669; color:#ffffff;">02</span>
                        <span style="font-size:0.875rem; font-weight:700;">Confirma tu identidad para activar la protección</span>
                    </div>

                    <div class="tfa-inner tfa-two-col">
                        <div>
                            <label for="confirmationCode" class="tfa-label">Código de 6 dígitos de la app *</label>
                            <input type="text"
                                   id="confirmationCode"
                                   wire:model.defer="confirmationCode"
                                   maxlength="6"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   autocomplete="one-time-code"
                                   placeholder="000000"
                                   class="tfa-input tfa-input-otp">
                            @error('confirmationCode')
                                <p class="tfa-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="currentPassword" class="tfa-label">Tu contraseña actual *</label>
                            <input type="password"
                                   id="currentPassword"
                                   wire:model.defer="currentPassword"
                                   placeholder="Ingresa tu contraseña de acceso"
                                   class="tfa-input"
                                   style="padding-top:1rem; padding-bottom:1rem;">
                            @error('currentPassword')
                                <p class="tfa-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="tfa-footer-bar">
                        <button type="button" wire:click="cancelConfiguration" class="tfa-btn tfa-btn-secondary">
                            Cancelar
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="tfa-btn tfa-btn-success">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span wire:loading.remove wire:target="confirmConfiguration">Verificar y Activar 2FA</span>
                            <span wire:loading wire:target="confirmConfiguration">Verificando código...</span>
                        </button>
                    </div>
                </form>
            </div>

        @else
            <!-- ESTADO: ACTIVADO Y PROTEGIDO -->
            <div class="tfa-card tfa-card-accent-emerald">
                <div class="tfa-header-row">
                    <div class="tfa-header-main">
                        <div class="tfa-icon-box tfa-icon-emerald">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <div style="display:flex; flex-wrap:wrap; align-items:center; gap:0.5rem; margin-bottom:0.35rem;">
                                <span class="tfa-badge tfa-badge-emerald">
                                    <span class="tfa-dot tfa-dot-emerald"></span>
                                    Protegido · 2FA Activo
                                </span>
                            </div>
                            <h3 class="tfa-title">Doble Factor de Autenticación Activo</h3>
                            <p class="tfa-subtitle">
                                Tu cuenta <strong>{{ auth()->user()?->email }}</strong> exige el código dinámico TOTP al iniciar sesión desde nuevos dispositivos.
                            </p>
                        </div>
                    </div>
                </div>

                @if(!empty($recoveryCodes))
                    <div class="tfa-divider"></div>

                    <!-- Códigos de Emergencia recién generados -->
                    <div class="tfa-inner" x-data="{ copiedAll: false }" style="border-color: #fcd34d;">
                        <div class="tfa-header-row" style="align-items:center;">
                            <div>
                                <span class="tfa-badge tfa-badge-amber" style="margin-bottom:0.35rem;">Guárdalos ahora</span>
                                <h4 style="margin:0; font-size:0.95rem; font-weight:800;">Códigos de Emergencia de Un Solo Uso</h4>
                                <p class="tfa-step-desc" style="margin-top:0.2rem;">
                                    Guárdalos en un lugar seguro fuera del servidor. Sirven para recuperar acceso si pierdes tu teléfono.
                                </p>
                            </div>
                            <button type="button"
                                    @click="navigator.clipboard.writeText('{{ implode("\n", $recoveryCodes) }}'); copiedAll = true; setTimeout(() => copiedAll = false, 2500)"
                                    class="tfa-btn tfa-btn-primary">
                                <span x-show="!copiedAll">Copiar Todos</span>
                                <span x-show="copiedAll" style="display:none;">¡Códigos Copiados!</span>
                            </button>
                        </div>

                        <div class="tfa-codes-grid">
                            @foreach($recoveryCodes as $code)
                                <div class="tfa-code-chip">{{ $code }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="tfa-divider"></div>

                <!-- Gestión de Códigos y Desactivación (2 Columnas) -->
                <div class="tfa-two-col">
                    <div class="tfa-inner">
                        <span class="tfa-label">Códigos de respaldo</span>
                        <p class="tfa-step-desc" style="margin-bottom:1rem;">
                            Revoca inmediatamente los códigos anteriores y genera un nuevo lote de 8 códigos de emergencia.
                        </p>
                        <form wire:submit.prevent="regenerateRecoveryCodes" style="display:flex; flex-direction:column; gap:0.75rem;">
                            <div>
                                <input type="password"
                                       wire:model.defer="regeneratePassword"
                                       placeholder="Confirma tu contraseña actual *"
                                       class="tfa-input">
                                @error('regeneratePassword')
                                    <p class="tfa-error">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" class="tfa-btn tfa-btn-secondary tfa-btn-full">
                                Regenerar Nuevos Códigos
                            </button>
                        </form>
                    </div>

                    <div class="tfa-inner tfa-danger-zone">
                        <span class="tfa-label" style="color:#e11d48;">Zona de peligro · Desactivar 2FA</span>
                        <p class="tfa-step-desc" style="margin-bottom:1rem;">
                            Quitará la protección de doble factor de tu cuenta. Úsalo solo si necesitas vincular otro teléfono.
                        </p>
                        <form wire:submit.prevent="disableTwoFactor" style="display:flex; flex-direction:column; gap:0.75rem;">
                            <div>
                                <input type="password"
                                       wire:model.defer="disablePassword"
                                       placeholder="Confirma tu contraseña actual *"
                                       class="tfa-input">
                                @error('disablePassword')
                                    <p class="tfa-error">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" class="tfa-btn tfa-btn-danger tfa-btn-full">
                                Confirmar y Desactivar 2FA
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
