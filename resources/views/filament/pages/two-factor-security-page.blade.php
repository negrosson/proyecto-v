<x-filament-panels::page>
    <div class="max-w-4xl space-y-6">
        @if(!$isEnabled && !$isConfiguring)
            <!-- Estado: Desactivado -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl sm:rounded-3xl p-5 sm:p-8 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row items-start gap-4 sm:gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 dark:bg-amber-400/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-500/20">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base sm:text-lg font-bold font-serif text-zinc-900 dark:text-white">
                            El Doble Factor de Autenticación (2FA) está desactivado
                        </h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-2xl">
                            Añade una capa de defensa en profundidad a tu cuenta administrativa. Al activar 2FA, deberás ingresar un código dinámico de 6 dígitos generado por tu teléfono (Google Authenticator, Microsoft Authenticator o 1Password) en cada inicio de sesión.
                        </p>
                    </div>
                </div>

                <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end">
                    <button 
                        type="button" 
                        wire:click="startConfiguration" 
                        class="w-full sm:w-auto px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:scale-95 text-zinc-950 font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                        <span>Comenzar Configuración de 2FA</span>
                    </button>
                </div>
            </div>
        @elseif($isConfiguring)
            <!-- Asistente de Configuración -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl sm:rounded-3xl p-5 sm:p-8 shadow-sm space-y-6 sm:space-y-8">
                <div>
                    <span class="text-[10px] font-mono uppercase tracking-[0.2em] text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2.5 py-0.5 rounded border border-amber-200 dark:border-amber-800/60 font-semibold">
                        Paso 1 de 2: Vincular Aplicación
                    </span>
                    <h3 class="text-xl font-bold font-serif text-zinc-900 dark:text-white mt-1">
                        Escanea el Código QR con tu App de Autenticación
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        Abre Google Authenticator o Microsoft Authenticator en tu teléfono y escanea la clave de configuración.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <!-- Código QR Vectorizado -->
                    <div class="flex flex-col items-center p-6 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl border border-zinc-200 dark:border-zinc-700 space-y-3">
                        <div class="bg-white p-3 rounded-xl shadow border border-zinc-200">
                            <img src="{{ $setupQrUri }}" alt="Código QR de configuración 2FA" class="w-44 h-44 object-contain">
                        </div>
                        <span class="text-[11px] font-mono text-zinc-500 dark:text-zinc-400 text-center">
                            Escanea con Google Authenticator
                        </span>
                    </div>

                    <!-- Clave Secreta en Texto Plano para Copia Rápida -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">
                                ¿No puedes escanear el código QR?
                            </label>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-2">
                                Puedes ingresar manualmente esta clave secreta en tu aplicación:
                            </p>
                            <div class="flex items-center gap-2">
                                <code class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white font-mono text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 select-all font-bold">
                                    {{ $setupSecret }}
                                </code>
                            </div>
                        </div>

                        <div class="pt-2 text-[11px] text-zinc-400 space-y-1">
                            <p>&bull; Tipo: Basado en tiempo (TOTP - RFC 6238)</p>
                            <p>&bull; Longitud: 6 dígitos &bull; Intervalo: 30 segundos</p>
                        </div>
                    </div>
                </div>

                <!-- Formulario de Confirmación y Modo Sudo -->
                <form wire:submit.prevent="confirmConfiguration" class="pt-6 border-t border-zinc-100 dark:border-zinc-800 space-y-5">
                    <div>
                        <span class="text-[10px] font-mono uppercase tracking-[0.2em] text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-0.5 rounded border border-emerald-200 dark:border-emerald-800/60 font-semibold">
                            Paso 2 de 2: Confirmar Identidad (Modo Sudo)
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="confirmationCode" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                                Código de 6 Dígitos de la App *
                            </label>
                            <input 
                                type="text" 
                                id="confirmationCode" 
                                wire:model.defer="confirmationCode" 
                                maxlength="6" 
                                placeholder="123456" 
                                class="w-full font-mono text-center text-xl tracking-widest font-bold py-2.5 px-3 bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 shadow-inner">
                            @error('confirmationCode')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="currentPassword" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                                Tu Contraseña Actual (Modo Sudo) *
                            </label>
                            <input 
                                type="password" 
                                id="currentPassword" 
                                wire:model.defer="currentPassword" 
                                placeholder="••••••••••••" 
                                class="w-full py-2.5 px-3 bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 shadow-inner text-sm">
                            @error('currentPassword')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button 
                            type="button" 
                            wire:click="cancelConfiguration" 
                            class="px-4 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-xs rounded-xl transition">
                            Cancelar
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow flex items-center gap-2">
                            <span>Verificar y Activar 2FA</span>
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- Estado: Activado -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-8 shadow-sm space-y-6">
                <div class="flex items-start gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 dark:bg-emerald-400/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-bold font-serif text-zinc-900 dark:text-white">
                                Doble Factor de Autenticación Activo
                            </h3>
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold border border-emerald-200 dark:border-emerald-800 font-mono">
                                PROTEGIDO
                            </span>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-2xl">
                            Tu cuenta exige un código de 6 dígitos en cada inicio de sesión. Si pierdes el acceso a tu aplicación de autenticación, podrás utilizar uno de tus códigos de recuperación de emergencia.
                        </p>
                    </div>
                </div>

                @if(!empty($recoveryCodes))
                    <!-- Mostrar Códigos de Emergencia Recién Generados -->
                    <div class="p-6 bg-amber-50/70 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/50 rounded-2xl space-y-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-700 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-amber-900 dark:text-amber-300">
                                Guarda tus Códigos de Emergencia en un Lugar Seguro
                            </h4>
                        </div>
                        <p class="text-xs text-amber-800 dark:text-amber-400">
                            Cada código puede ser utilizado exactamente 1 sola vez en caso de que pierdas tu teléfono:
                        </p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1 font-mono text-xs">
                            @foreach($recoveryCodes as $code)
                                <div class="p-2 bg-white dark:bg-zinc-900 border border-amber-200/80 dark:border-amber-800/60 rounded-lg text-center font-bold text-zinc-900 dark:text-zinc-100 select-all shadow-sm">
                                    {{ $code }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Acciones en Modo Sudo -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-zinc-100 dark:border-zinc-800">
                    <!-- Regenerar Códigos -->
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-800/40 rounded-2xl border border-zinc-200 dark:border-zinc-700/60 space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white">
                            Regenerar Códigos de Emergencia
                        </h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            Invalida los códigos anteriores y genera un nuevo conjunto de 8 códigos.
                        </p>
                        <form wire:submit.prevent="regenerateRecoveryCodes" class="space-y-3">
                            <div>
                                <input 
                                    type="password" 
                                    wire:model.defer="regeneratePassword" 
                                    placeholder="Contraseña actual *" 
                                    class="w-full text-xs py-2 px-3 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                                @error('regeneratePassword')
                                    <p class="mt-1 text-[11px] text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <button 
                                type="submit" 
                                class="w-full py-2 px-3 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-white font-semibold text-xs rounded-xl transition">
                                Regenerar Códigos
                            </button>
                        </form>
                    </div>

                    <!-- Desactivar 2FA -->
                    <div class="p-5 bg-rose-50/50 dark:bg-rose-950/20 rounded-2xl border border-rose-200/80 dark:border-rose-900/40 space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-rose-900 dark:text-rose-300">
                            Desactivar Doble Factor
                        </h4>
                        <p class="text-xs text-rose-700 dark:text-rose-400">
                            Removerá la protección de tu cuenta. Solo se recomienda si vas a cambiar de dispositivo.
                        </p>
                        <form wire:submit.prevent="disableTwoFactor" class="space-y-3">
                            <div>
                                <input 
                                    type="password" 
                                    wire:model.defer="disablePassword" 
                                    placeholder="Contraseña actual *" 
                                    class="w-full text-xs py-2 px-3 bg-white dark:bg-zinc-800 border border-rose-300 dark:border-rose-800/80 rounded-xl text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                @error('disablePassword')
                                    <p class="mt-1 text-[11px] text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <button 
                                type="submit" 
                                class="w-full py-2 px-3 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition">
                                Confirmar y Desactivar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
