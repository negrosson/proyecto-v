<x-filament-widgets::widget class="executive-hero-widget-wrapper col-span-full w-full">
    <div class="executive-hero-card w-full">
        <!-- Resplandor decorativo sutil en modo oscuro -->
        <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full bg-cyan-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full bg-amber-500/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col xl:flex-row xl:items-center justify-between gap-6 lg:gap-8">
            <!-- COLUMNA IZQUIERDA: Saludo, descripción y Acciones Rápidas -->
            <div class="w-full xl:w-[50%] flex flex-col justify-between space-y-4">
                <!-- Badge superior tipo píldora -->
                <div class="executive-badge-pill">
                    <span>Vitrina & Logística de Arriendos • Menaje & Mobiliario</span>
                </div>

                <!-- Saludo principal y bajada elegante -->
                <div class="space-y-1.5">
                    <h2 class="executive-hero-title">
                        Hola, {{ $userName }} 👋
                    </h2>
                    <p class="executive-hero-desc">
                        Supervisión en tiempo real de vajilla, cristalería, mantelería y mobiliario para eventos. Monitorea la disponibilidad de piezas en vitrina, el interés de los clientes y las cotizaciones activas enviadas a WhatsApp.
                    </p>
                </div>

                <!-- Acciones Rápidas con botones píldora -->
                <div class="pt-1">
                    <div class="executive-section-label mb-2">
                        Acciones Rápidas
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- 1. Ver Catálogo Web -->
                        <a href="{{ $catalogUrl }}" target="_blank" rel="noopener noreferrer" class="executive-pill-btn">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            <span>Ver Catálogo Web</span>
                        </a>

                        <!-- 2. Nuevo Artículo (Píldora destacada en Teal / Cyan) -->
                        <a href="{{ $createProductUrl }}" class="executive-pill-btn-teal">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Nuevo Artículo</span>
                        </a>

                        <!-- 3. Ajustes de Vitrina -->
                        <a href="{{ $siteSettingsUrl }}" class="executive-pill-btn">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Ajustes de Vitrina</span>
                        </a>

                        <!-- 4. Bandeja de Cotizaciones -->
                        <a href="{{ $quotesUrl }}" class="executive-pill-btn">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span>Bandeja de Cotizaciones</span>
                        </a>

                        <!-- 5. Accesos Rápidos -->
                        <a href="{{ $usersUrl }}" class="executive-pill-btn">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span>Accesos Rápidos</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: Grilla de Métricas Obsidian -->
            <div class="w-full xl:w-[50%] flex flex-col gap-3">
                <!-- Fila 1: 2 Tarjetas (1 ancha con detalle stock + 1 familias) -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <!-- Tarjeta 1: Artículos en Vitrina con Piezas de Menaje -->
                    <a href="{{ $productsUrl }}" class="executive-stat-card sm:col-span-7">
                        <div class="executive-stat-icon-blue">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="executive-stat-title truncate">
                                {{ $totalProducts }} Artículos en Vitrina
                            </div>
                            <div class="executive-stat-subtitle truncate">
                                {{ number_format($totalStock, 0, ',', '.') }} piezas de menaje
                            </div>
                        </div>
                    </a>

                    <!-- Tarjeta 2: Familias Activas de Menaje -->
                    <a href="{{ $categoriesUrl }}" class="executive-stat-card sm:col-span-5">
                        <div class="executive-stat-icon-green">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="executive-stat-title truncate">
                                {{ $totalCategories }} Familias
                            </div>
                            <div class="executive-stat-subtitle truncate">
                                Activas de Menaje
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Fila 2: 3 Tarjetas (Artículos, Cotizaciones WhatsApp, Consultas Contacto) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Tarjeta 3: Artículos en Vitrina -->
                    <a href="{{ $productsUrl }}" class="executive-stat-card">
                        <div class="executive-stat-icon-blue">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="executive-stat-title truncate">
                                {{ $totalProducts }} Artículos
                            </div>
                            <div class="executive-stat-subtitle truncate">
                                en Vitrina
                            </div>
                        </div>
                    </a>

                    <!-- Tarjeta 4: Cotizaciones WhatsApp -->
                    <a href="{{ $quotesUrl }}" class="executive-stat-card">
                        <div class="executive-stat-icon-whatsapp">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 15 3.8 13.47 3.8 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.33C9.35 7.33 9.06 7.4 8.81 7.67C8.56 7.94 7.85 8.61 7.85 9.97C7.85 11.33 8.84 12.64 8.98 12.83C9.12 13.02 10.93 15.81 13.7 17.01C14.36 17.3 14.88 17.47 15.28 17.6C15.94 17.81 16.55 17.78 17.02 17.71C17.55 17.63 18.65 17.04 18.88 16.39C19.11 15.74 19.11 15.18 19.04 15.07C18.97 14.96 18.78 14.89 18.5 14.75C18.22 14.61 16.85 13.93 16.6 13.84C16.34 13.75 16.15 13.7 15.97 13.98C15.78 14.25 15.25 14.89 15.09 15.07C14.92 15.25 14.76 15.28 14.48 15.14C14.2 15 13.3 14.7 12.23 13.75C11.4 13.01 10.84 12.1 10.7 11.85C10.56 11.61 10.68 11.47 10.82 11.34C10.95 11.21 11.11 11 11.25 10.84C11.39 10.68 11.44 10.56 11.53 10.38C11.62 10.2 11.58 10.03 11.51 9.89C11.44 9.75 10.89 8.39 10.66 7.84C10.43 7.31 10.2 7.39 10.03 7.38C9.88 7.38 9.71 7.33 9.53 7.33Z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="executive-stat-title truncate">
                                {{ $pendingQuotes }} Cotizaciones
                            </div>
                            <div class="executive-stat-subtitle truncate">
                                WhatsApp Pendientes
                            </div>
                        </div>
                    </a>

                    <!-- Tarjeta 5: Consultas de Contacto -->
                    <a href="{{ $messagesUrl }}" class="executive-stat-card">
                        <div class="executive-stat-icon-amber">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="executive-stat-title truncate">
                                @if($unreadMessages > 0)
                                    {{ $unreadMessages }} Consultas
                                @else
                                    Consultas de
                                @endif
                            </div>
                            <div class="executive-stat-subtitle truncate">
                                @if($unreadMessages > 0)
                                    por Responder
                                @else
                                    Contacto al Día
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
