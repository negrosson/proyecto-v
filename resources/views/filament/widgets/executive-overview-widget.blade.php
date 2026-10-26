<x-filament-widgets::widget class="executive-hero-widget-wrapper col-span-full w-full">
    <div class="executive-hero-widget relative w-full overflow-hidden rounded-2xl p-5 sm:p-7 shadow-sm border bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white transition-all"
         style="width: 100% !important;">
        <!-- Resplandor decorativo sutil adaptativo -->
        <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-amber-500/10 dark:bg-amber-500/15 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full bg-zinc-200/50 dark:bg-zinc-800/40 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 w-full">
            <!-- Columna Izquierda: Información de Operaciones y Saludo -->
            <div class="space-y-3 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 dark:bg-amber-500/20 border border-amber-500/30 text-amber-700 dark:text-amber-400 text-[11px] font-bold tracking-wider uppercase">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Centro de Operaciones • Arriendos & Eventos
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-zinc-950 dark:text-white flex items-center gap-2 font-['Plus_Jakarta_Sans']">
                    Hola, {{ $userName }} 👋
                </h1>

                <p class="text-zinc-600 dark:text-zinc-300 text-sm leading-relaxed max-w-2xl">
                    Supervisa el inventario de menaje para eventos, la telemetría de interacción en tiempo real y el flujo de cotizaciones enviadas a WhatsApp.
                </p>

                <!-- Pills de Estado Ejecutivo adaptadas a pantalla móvil y escritorio -->
                <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 font-medium shadow-xs">
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ now()->translatedFormat('l, d \d\e F Y') }}
                    </span>

                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 font-medium shadow-xs">
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        {{ $totalProducts }} Artículos ({{ number_format($totalStock, 0, ',', '.') }} piezas de menaje)
                    </span>

                    @if($pendingQuotes > 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30 font-semibold shadow-xs">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $pendingQuotes }} cotizaciones WhatsApp pendientes
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 font-medium shadow-xs">
                            <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $totalQuotes }} cotizaciones registradas
                        </span>
                    @endif
                </div>
            </div>

            <!-- Columna Derecha: Botonera de Acción Rápida Responsive -->
            <div class="flex flex-col sm:flex-row lg:flex-col gap-2.5 sm:min-w-[210px] shrink-0 w-full lg:w-auto">
                <a href="{{ $catalogUrl }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-zinc-950 font-bold text-xs uppercase tracking-wider transition-all shadow-sm hover:shadow-md hover:shadow-amber-500/20 w-full text-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Ver Catálogo Web
                </a>

                <a href="{{ $createProductUrl }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-white font-medium text-xs transition-colors border border-zinc-200 dark:border-zinc-700 w-full text-center">
                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo Artículo
                </a>

                <a href="{{ $siteSettingsUrl }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/60 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-medium text-xs transition-colors border border-zinc-200 dark:border-zinc-700 w-full text-center">
                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Ajustes de Vitrina
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
