<x-filament-panels::page>
    <div class="max-w-md mx-auto py-4 sm:py-8">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl sm:rounded-3xl p-5 sm:p-8 shadow-xl space-y-6">
            <div class="text-center space-y-2">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-500/10 dark:bg-amber-400/10 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20 shadow-inner">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold font-serif text-zinc-900 dark:text-white">
                    Verificación de Segundo Factor
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Ingresa el código dinámico de 6 dígitos generado por tu aplicación de autenticación para continuar.
                </p>
            </div>

            <form wire:submit.prevent="submitChallenge" class="space-y-4">
                @if(!$useRecoveryCode)
                    <div>
                        <label for="code" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                            Código de 6 Dígitos
                        </label>
                        <input 
                            type="text" 
                            id="code" 
                            wire:model.defer="code" 
                            maxlength="6" 
                            autocomplete="one-time-code"
                            placeholder="000000"
                            autofocus
                            class="w-full text-center text-2xl sm:text-3xl font-mono tracking-[0.25em] sm:tracking-[0.5em] font-bold py-3.5 px-4 bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-2xl text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition shadow-inner">
                        @error('code')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-medium text-center">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <div>
                        <label for="recovery_code" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                            Código de Emergencia
                        </label>
                        <input 
                            type="text" 
                            id="recovery_code" 
                            wire:model.defer="recovery_code" 
                            placeholder="XXXXX-XXXXX"
                            autofocus
                            class="w-full text-center text-lg font-mono font-bold py-3 px-4 bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-2xl text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition shadow-inner">
                        @error('recovery_code')
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-medium text-center">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <button 
                    type="submit" 
                    class="w-full py-3.5 px-4 bg-amber-500 hover:bg-amber-600 active:scale-[0.99] text-zinc-950 font-bold text-sm rounded-xl transition duration-200 shadow-md flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span>Verificar Acceso</span>
                </button>
            </form>

            <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 text-center">
                <button 
                    type="button" 
                    wire:click="$toggle('useRecoveryCode')" 
                    class="text-xs text-amber-600 dark:text-amber-400 hover:underline font-semibold transition">
                    {{ $useRecoveryCode ? '← Usar código dinámico de la app' : '¿No tienes acceso a tu teléfono? Usa un código de emergencia' }}
                </button>
            </div>
        </div>
    </div>
</x-filament-panels::page>
