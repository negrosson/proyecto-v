<x-filament-panels::page>
    <div class="max-w-md mx-auto py-4 sm:py-10">
        <!-- Tarjeta Principal con Estilo Obsidian y Relieve Sutil -->
        <div class="two-factor-card space-y-7 shadow-2xl backdrop-blur-xl">
            
            <!-- Encabezado con Icono de Seguridad e Indicador Zero-Trust -->
            <div class="text-center space-y-3">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center border border-amber-500/30 shadow-inner">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>

                <div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-amber-500/10 text-amber-500 border border-amber-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        Desafío 2FA • RFC 6238
                    </span>
                    <h2 class="text-xl sm:text-2xl font-bold font-serif mt-2">
                        Verificación en Dos Pasos
                    </h2>
                    <p class="text-xs mt-1 max-w-xs mx-auto leading-relaxed">
                        {{ $useRecoveryCode ? 'Ingresa uno de tus códigos de emergencia de 10 caracteres.' : 'Abre tu aplicación de autenticación e introduce el código dinámico de 6 dígitos.' }}
                    </p>
                </div>
            </div>

            <!-- Formulario de Desafío 2FA -->
            <form wire:submit.prevent="submitChallenge" class="space-y-5">
                @if(!$useRecoveryCode)
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <label for="code" class="font-bold uppercase tracking-wider text-[11px]">
                                Código de Autenticación
                            </label>
                            <span class="text-[10px] font-mono text-zinc-400">6 Dígitos</span>
                        </div>
                        <input 
                            type="text" 
                            id="code" 
                            wire:model.defer="code" 
                            maxlength="6" 
                            inputmode="numeric"
                            pattern="[0-9]*"
                            autocomplete="one-time-code"
                            placeholder="000 000"
                            autofocus
                            class="two-factor-input-text text-center text-3xl font-mono tracking-[0.35em] sm:tracking-[0.5em] font-extrabold py-4 px-4 shadow-inner">
                        @error('code')
                            <div class="p-2.5 bg-rose-500/10 border border-rose-500/30 rounded-xl">
                                <p class="text-xs text-rose-500 font-medium text-center flex items-center justify-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    <span>{{ $message }}</span>
                                </p>
                            </div>
                        @enderror
                    </div>
                @else
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <label for="recovery_code" class="font-bold uppercase tracking-wider text-[11px]">
                                Código de Recuperación
                            </label>
                            <span class="text-[10px] font-mono text-zinc-400">1 Solo Uso</span>
                        </div>
                        <input 
                            type="text" 
                            id="recovery_code" 
                            wire:model.defer="recovery_code" 
                            placeholder="XXXXX-XXXXX"
                            autofocus
                            class="two-factor-input-text text-center text-xl font-mono font-bold py-3.5 px-4 shadow-inner uppercase tracking-wider">
                        @error('recovery_code')
                            <div class="p-2.5 bg-rose-500/10 border border-rose-500/30 rounded-xl">
                                <p class="text-xs text-rose-500 font-medium text-center flex items-center justify-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    <span>{{ $message }}</span>
                                </p>
                            </div>
                        @enderror
                    </div>
                @endif

                <!-- Botón de Envío -->
                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    class="two-factor-btn-primary w-full py-4 text-xs font-extrabold shadow-md">
                    <span wire:loading.remove wire:target="submitChallenge" class="flex items-center gap-2">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span>Verificar e Ingresar al Panel</span>
                    </span>
                    <span wire:loading wire:target="submitChallenge" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Validando Criptografía...</span>
                    </span>
                </button>
            </form>

            <!-- Alternar Entre TOTP Dinámico y Código de Respaldo -->
            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 text-center">
                <button 
                    type="button" 
                    wire:click="$toggle('useRecoveryCode')" 
                    class="text-xs text-zinc-400 hover:text-amber-500 font-semibold transition inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <span>{{ $useRecoveryCode ? 'Volver a código dinámico de la app' : '¿Problemas con tu teléfono? Usar código de emergencia' }}</span>
                </button>
            </div>
        </div>
    </div>
</x-filament-panels::page>
