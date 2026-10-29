<x-filament-panels::page>
    <div class="max-w-4xl space-y-7">
        @if(!$isEnabled && !$isConfiguring)
            <!-- Estado: Desactivado -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-2xl sm:rounded-3xl p-6 sm:p-9 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row items-start gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-amber-500/10 dark:bg-amber-400/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-500/20 shadow-inner">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-lg sm:text-xl font-bold font-serif text-zinc-900 dark:text-white">
                                Doble Factor de Autenticación (2FA) Desactivado
                            </h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                RECOMENDADO
                            </span>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-2xl">
                            Añade una capa de defensa en profundidad de grado bancario a tu cuenta administrativa. Al activar 2FA bajo el estándar RFC 6238, necesitarás ingresar un código dinámico de 6 dígitos generado por tu teléfono inteligente en cada inicio de sesión, neutralizando intentos de robo de contraseñas.
                        </p>
                        
                        <!-- Píldoras de Apps Compatibles -->
                        <div class="flex flex-wrap items-center gap-2 pt-2 text-[11px] text-zinc-500 dark:text-zinc-400">
                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">Compatible con:</span>
                            <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded-md border border-zinc-200 dark:border-zinc-700 font-mono text-[10px]">Google Authenticator</span>
                            <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded-md border border-zinc-200 dark:border-zinc-700 font-mono text-[10px]">Microsoft Authenticator</span>
                            <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded-md border border-zinc-200 dark:border-zinc-700 font-mono text-[10px]">1Password</span>
                            <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded-md border border-zinc-200 dark:border-zinc-700 font-mono text-[10px]">Authy</span>
                        </div>
                    </div>
                </div>

                <div class="pt-5 border-t border-zinc-100 dark:border-zinc-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <span class="text-xs text-zinc-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Protección contra ataques de fuerza bruta y replay attacks.
                    </span>
                    <button 
                        type="button" 
                        wire:click="startConfiguration" 
                        class="w-full sm:w-auto px-6 py-3 bg-amber-500 hover:bg-amber-600 active:scale-95 text-zinc-950 font-bold text-xs uppercase tracking-wider rounded-xl transition duration-200 shadow-md shadow-amber-500/20 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                        <span>Comenzar Configuración de 2FA</span>
                    </button>
                </div>
            </div>
        @elseif($isConfiguring)
            <!-- Asistente de Configuración Paso a Paso -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-2xl sm:rounded-3xl p-6 sm:p-9 shadow-sm space-y-8">
                
                <!-- Encabezado del Asistente -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-zinc-100 dark:border-zinc-800 gap-3">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            Asistente de Vinculación
                        </span>
                        <h3 class="text-xl sm:text-2xl font-bold font-serif text-zinc-900 dark:text-white mt-1">
                            Vincular tu Teléfono Móvil
                        </h3>
                    </div>
                    <button 
                        type="button" 
                        wire:click="cancelConfiguration" 
                        class="self-start sm:self-auto text-xs text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition">
                        &times; Cancelar Proceso
                    </button>
                </div>

                <!-- PASO 1: Escaneo y Clave Secreta -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full bg-amber-500 text-zinc-950 font-mono font-bold text-xs flex items-center justify-center shrink-0">1</span>
                        <h4 class="font-bold text-sm text-zinc-900 dark:text-white uppercase tracking-wider">
                            Escanea el Código QR en tu Aplicación
                        </h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center bg-zinc-50 dark:bg-zinc-950/60 p-6 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80">
                        <!-- Código QR con Marco de Alto Contraste -->
                        <div class="md:col-span-5 flex flex-col items-center space-y-2">
                            <div class="p-3.5 bg-white rounded-2xl shadow-md border border-zinc-200 flex items-center justify-center">
                                <img src="{{ $setupQrUri }}" alt="Código QR de configuración 2FA" class="w-48 h-48 object-contain">
                            </div>
                            <span class="text-[11px] font-mono text-zinc-400 text-center">
                                Apunta la cámara de tu app aquí
                            </span>
                        </div>

                        <!-- Opción de Clave Manual con Copia en 1 Clic -->
                        <div class="md:col-span-7 space-y-4" x-data="{ copied: false }">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1">
                                    ¿No puedes escanear el código QR?
                                </label>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed mb-3">
                                    Selecciona <em>"Ingresar clave manualmente"</em> en tu app e introduce la siguiente clave secreta:
                                </p>

                                <div class="flex items-center gap-2">
                                    <code class="px-3.5 py-2.5 bg-white dark:bg-zinc-900 text-amber-600 dark:text-amber-400 font-mono text-xs sm:text-sm font-bold rounded-xl border border-zinc-200 dark:border-zinc-800 select-all tracking-wider shadow-inner flex-1 overflow-x-auto">
                                        {{ $setupSecret }}
                                    </code>
                                    <button 
                                        type="button" 
                                        @click="navigator.clipboard.writeText('{{ $setupSecret }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="px-3.5 py-2.5 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0 shadow-sm active:scale-95">
                                        <template x-if="!copied">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                                <span>Copiar</span>
                                            </span>
                                        </template>
                                        <template x-if="copied">
                                            <span class="flex items-center gap-1 text-emerald-400">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>¡Copiado!</span>
                                            </span>
                                        </template>
                                    </button>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-zinc-200/60 dark:border-zinc-800/60 text-[11px] text-zinc-400 space-y-1">
                                <p>&bull; Tipo: Basado en tiempo (TOTP - RFC 6238)</p>
                                <p>&bull; Ventana: 30 segundos &bull; Cifrado: AES-256 en base de datos</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 2: Confirmación con Código de 6 Dígitos y Modo Sudo -->
                <form wire:submit.prevent="confirmConfiguration" class="space-y-4 pt-2">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full bg-emerald-500 text-white font-mono font-bold text-xs flex items-center justify-center shrink-0">2</span>
                        <h4 class="font-bold text-sm text-zinc-900 dark:text-white uppercase tracking-wider">
                            Confirmar Identidad y Activar Protección
                        </h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 p-6 bg-zinc-50 dark:bg-zinc-950/60 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80">
                        <div>
                            <label for="confirmationCode" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                                Código de 6 Dígitos de la App *
                            </label>
                            <input 
                                type="text" 
                                id="confirmationCode" 
                                wire:model.defer="confirmationCode" 
                                maxlength="6" 
                                inputmode="numeric"
                                pattern="[0-9]*"
                                placeholder="123 456" 
                                class="w-full font-mono text-center text-2xl tracking-[0.3em] font-extrabold py-3 px-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 shadow-inner">
                            @error('confirmationCode')
                                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="currentPassword" class="block text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                                Contraseña Actual (Modo Sudo) *
                            </label>
                            <input 
                                type="password" 
                                id="currentPassword" 
                                wire:model.defer="currentPassword" 
                                placeholder="••••••••••••" 
                                class="w-full py-3 px-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 shadow-inner text-sm">
                            @error('currentPassword')
                                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button 
                            type="button" 
                            wire:click="cancelConfiguration" 
                            class="px-5 py-2.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold text-xs rounded-xl transition">
                            Cancelar
                        </button>
                        <button 
                            type="submit" 
                            class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition duration-200 shadow-md flex items-center gap-2 active:scale-95">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Verificar y Activar 2FA</span>
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- Estado: Activado y Protegido -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-2xl sm:rounded-3xl p-6 sm:p-9 shadow-sm space-y-7">
                
                <!-- Encabezado de Protección Activa -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 border-b border-zinc-100 dark:border-zinc-800 gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 dark:bg-emerald-400/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-inner">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg sm:text-xl font-bold font-serif text-zinc-900 dark:text-white">
                                    Doble Factor de Autenticación Activo
                                </h3>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold border border-emerald-200 dark:border-emerald-800 font-mono">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    PROTEGIDO
                                </span>
                            </div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                Tu cuenta administrativa exige el código dinámico TOTP en cada inicio de sesión.
                            </p>
                        </div>
                    </div>
                </div>

                @if(!empty($recoveryCodes))
                    <!-- Sección de Códigos de Emergencia con Botón de Copiado Masivo -->
                    <div class="p-6 bg-amber-50/70 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/50 rounded-2xl space-y-4" x-data="{ copiedAll: false }">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-amber-900 dark:text-amber-300">
                                    Códigos de Emergencia (Guárdalos en un Lugar Seguro)
                                </h4>
                            </div>

                            <button 
                                type="button" 
                                @click="navigator.clipboard.writeText('{{ implode("\n", $recoveryCodes) }}'); copiedAll = true; setTimeout(() => copiedAll = false, 2500)"
                                class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 active:scale-95 text-zinc-950 rounded-lg text-xs font-bold transition flex items-center gap-1.5 shadow-sm self-start sm:self-auto">
                                <template x-if="!copiedAll">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                        <span>Copiar Todos</span>
                                    </span>
                                </template>
                                <template x-if="copiedAll">
                                    <span class="flex items-center gap-1 font-extrabold text-zinc-950">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>¡Códigos Copiados!</span>
                                    </span>
                                </template>
                            </button>
                        </div>

                        <p class="text-xs text-amber-800 dark:text-amber-400">
                            Cada código puede ser utilizado exactamente 1 sola vez en caso de pérdida o avería de tu teléfono inteligente:
                        </p>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-1 font-mono text-xs">
                            @foreach($recoveryCodes as $code)
                                <div class="p-2.5 bg-white dark:bg-zinc-900 border border-amber-200/80 dark:border-amber-800/60 rounded-xl text-center font-bold text-zinc-900 dark:text-zinc-100 select-all shadow-sm tracking-wider">
                                    {{ $code }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Tarjetas de Gestión en Modo Sudo -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    
                    <!-- Tarjeta 1: Regenerar Códigos -->
                    <div class="p-6 bg-zinc-50 dark:bg-zinc-950/60 rounded-2xl border border-zinc-200 dark:border-zinc-800 space-y-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-zinc-600 dark:text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white">
                                Regenerar Códigos de Emergencia
                            </h4>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            Invalida de inmediato todos los códigos previos y genera un nuevo conjunto de 8 códigos de un solo uso.
                        </p>
                        <form wire:submit.prevent="regenerateRecoveryCodes" class="space-y-3 pt-1">
                            <div>
                                <input 
                                    type="password" 
                                    wire:model.defer="regeneratePassword" 
                                    placeholder="Contraseña actual *" 
                                    class="w-full text-xs py-2.5 px-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 shadow-inner">
                                @error('regeneratePassword')
                                    <p class="mt-1 text-[11px] text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            <button 
                                type="submit" 
                                class="w-full py-2.5 px-3 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white font-bold text-xs rounded-xl transition shadow-sm active:scale-98">
                                Regenerar Nuevos Códigos
                            </button>
                        </form>
                    </div>

                    <!-- Tarjeta 2: Desactivar 2FA -->
                    <div class="p-6 bg-rose-50/40 dark:bg-rose-950/20 rounded-2xl border border-rose-200/70 dark:border-rose-900/40 space-y-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-rose-900 dark:text-rose-300">
                                Desactivar Doble Factor
                            </h4>
                        </div>
                        <p class="text-xs text-rose-700/80 dark:text-rose-400/80 leading-relaxed">
                            Removerá la protección de tu cuenta. Solo se recomienda si vas a cambiar o desvincular tu dispositivo.
                        </p>
                        <form wire:submit.prevent="disableTwoFactor" class="space-y-3 pt-1">
                            <div>
                                <input 
                                    type="password" 
                                    wire:model.defer="disablePassword" 
                                    placeholder="Contraseña actual *" 
                                    class="w-full text-xs py-2.5 px-3 bg-white dark:bg-zinc-900 border border-rose-300/80 dark:border-rose-800/80 rounded-xl text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 shadow-inner">
                                @error('disablePassword')
                                    <p class="mt-1 text-[11px] text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            <button 
                                type="submit" 
                                class="w-full py-2.5 px-3 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition shadow-sm active:scale-98">
                                Confirmar y Desactivar 2FA
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
