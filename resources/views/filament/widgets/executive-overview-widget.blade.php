<x-filament-widgets::widget class="executive-hero-widget-wrapper col-span-full w-full">
    <div class="executive-hero-widget relative w-full overflow-hidden rounded-2xl p-6 sm:p-7 shadow-xs border bg-white dark:bg-zinc-900 border-zinc-200/90 dark:border-zinc-800 text-zinc-900 dark:text-white transition-all space-y-5"
         style="width: 100% !important;">
        <!-- Resplandor decorativo adaptativo -->
        <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full bg-amber-500/10 dark:bg-amber-500/15 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full bg-zinc-200/40 dark:bg-zinc-800/40 blur-3xl pointer-events-none"></div>

        <!-- 1. Encabezado Superior con Badge de Negocio y Fecha -->
        <div class="relative z-10 flex flex-wrap items-center justify-between gap-3">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-amber-500/10 dark:bg-amber-500/20 border border-amber-500/30 text-amber-800 dark:text-amber-300 text-xs font-bold tracking-wider uppercase">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                Vitrina & Logística de Arriendos • Menaje & Mobiliario
            </div>

            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 text-xs font-medium">
                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ now()->translatedFormat('l, d \d\e F Y') }}
            </div>
        </div>

        <!-- 2. Saludo Personalizado y Descripción Específica de Menaje -->
        <div class="relative z-10 space-y-1.5 max-w-4xl">
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-zinc-950 dark:text-white font-['Plus_Jakarta_Sans'] flex items-center gap-2">
                Hola, {{ $userName }} 👋
            </h1>
            <p class="text-zinc-600 dark:text-zinc-300 text-sm leading-relaxed">
                Supervisión en tiempo real de vajilla, cristalería, mantelería y mobiliario para eventos. Monitorea la disponibilidad de piezas en vitrina, el interés de los clientes y las cotizaciones activas enviadas a WhatsApp.
            </p>
        </div>

        <!-- 3. Métricas Operativas en Vitrina (Pastillas Informativas de Dominio) -->
        <div class="relative z-10 flex flex-wrap items-center gap-2.5 pt-1 text-xs">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/90 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 font-semibold shadow-xs">
                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                {{ $totalProducts }} Artículos en Vitrina ({{ number_format($totalStock, 0, ',', '.') }} piezas de menaje)
            </span>

            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/90 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 font-medium shadow-xs">
                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                {{ $totalCategories }} Familias Activas de Menaje
            </span>

            @if($pendingQuotes > 0)
                <a href="{{ $quotesUrl }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30 font-semibold shadow-xs hover:bg-amber-100 dark:hover:bg-amber-500/30 transition-colors">
                    <svg class="w-3.5 h-3.5 text-amber-600 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $pendingQuotes }} Cotizaciones WhatsApp Pendientes
                </a>
            @else
                <a href="{{ $quotesUrl }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 font-medium shadow-xs hover:bg-emerald-100 transition-colors">
                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $totalQuotes }} Cotizaciones Registradas (Bandeja al Día)
                </a>
            @endif

            @if($unreadMessages > 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30 font-semibold shadow-xs">
                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ $unreadMessages }} Consultas Web por Revisar
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/60 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 text-xs">
                    <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Consultas de Contacto al Día
                </span>
            @endif
        </div>

        <!-- 4. Barra Inferior de Acciones Rápidas (Flujo de Trabajo Operativo) -->
        <div class="relative z-10 pt-4 border-t border-zinc-100 dark:border-zinc-800/80 flex flex-wrap items-center gap-2.5">
            <a href="{{ $catalogUrl }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 active:scale-95 text-zinc-950 font-bold text-xs uppercase tracking-wider transition-all shadow-xs hover:shadow-md hover:shadow-amber-500/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                <span>Ver Catálogo Web</span>
            </a>

            <a href="{{ $createProductUrl }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 active:scale-95 text-zinc-900 dark:text-white font-semibold text-xs transition-colors border border-zinc-200 dark:border-zinc-700">
                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nuevo Artículo</span>
            </a>

            <a href="{{ $siteSettingsUrl }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/60 dark:hover:bg-zinc-700 active:scale-95 text-zinc-700 dark:text-zinc-300 font-semibold text-xs transition-colors border border-zinc-200 dark:border-zinc-700">
                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Ajustes de Vitrina</span>
            </a>

            <a href="{{ $quotesUrl }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/60 dark:hover:bg-zinc-700 active:scale-95 text-zinc-700 dark:text-zinc-300 font-semibold text-xs transition-colors border border-zinc-200 dark:border-zinc-700">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>Bandeja de Cotizaciones</span>
            </a>
        </div>
    </div>
</x-filament-widgets::widget>
