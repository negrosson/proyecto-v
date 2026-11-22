<x-filament-widgets::widget class="executive-hero-widget-wrapper col-span-full w-full">
    <div x-data="{
            mx: 50,
            my: 35,
            active: false,
            move(e) {
                const r = e.currentTarget.getBoundingClientRect();
                this.mx = ((e.clientX - r.left) / r.width) * 100;
                this.my = ((e.clientY - r.top) / r.height) * 100;
                this.active = true;
            }
         }"
         @mousemove="move($event)"
         @mouseleave="active = false"
         class="executive-hero-card w-full">

        <!-- Haz de luz dorado viajero en el borde superior -->
        <div class="exec-top-rail">
            <div class="exec-top-beam"></div>
        </div>

        <!-- Luz Spotlight interactiva que sigue el cursor -->
        <div class="exec-spotlight"
             :style="`opacity: ${active ? 1 : 0.45}; background: radial-gradient(460px circle at ${mx}% ${my}%, rgba(245, 158, 11, 0.14), rgba(16, 185, 129, 0.05) 45%, transparent 72%);`">
        </div>

        <div class="exec-hero-grid">
            <!-- COLUMNA IZQUIERDA: Identidad VYS, Saludo y 4 Acciones Rápidas Jerarquizadas -->
            <div class="exec-left-col">
                <div class="executive-badge-pill">
                    <span class="exec-live-dot"></span>
                    <span>Catálogo Online &bull; {{ $siteName }}</span>
                </div>

                <div>
                    <h2 class="executive-hero-title">
                        Hola, {{ $userName }}
                    </h2>
                    <p class="executive-hero-desc">
                        Gestiona tu vitrina de eventos, organiza la galería fotográfica, actualiza tus montajes reales y controla las cotizaciones en tiempo real.
                    </p>
                </div>

                <div class="exec-actions-row">
                    <!-- Acción Principal Dorada -->
                    <a href="{{ $createProductUrl }}" wire:navigate class="executive-pill-btn-gold">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Nuevo Artículo</span>
                    </a>

                    <!-- 3 Acciones Secundarias Limpias -->
                    <a href="{{ $catalogUrl }}" target="_blank" rel="noopener noreferrer" class="executive-pill-btn">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        <span>Ver Sitio Web</span>
                    </a>

                    <a href="{{ $galleryUrl }}" wire:navigate class="executive-pill-btn">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Galería de Fotos</span>
                    </a>

                    <a href="{{ $siteSettingsUrl }}" wire:navigate class="executive-pill-btn">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Ajustes del Sitio</span>
                    </a>
                </div>
            </div>

            <!-- COLUMNA DERECHA: Grilla Simétrica 2×2 (Cero datos duplicados con los KPIs de abajo) -->
            <div class="exec-right-grid">
                <!-- 1. Galería Fotográfica -->
                <a href="{{ $galleryUrl }}" wire:navigate class="executive-stat-card">
                    <div class="executive-stat-icon-amber">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="exec-stat-body">
                        <div class="executive-stat-title">{{ $totalGalleryPhotos }} Fotos</div>
                        <div class="executive-stat-subtitle">Galería organizada</div>
                    </div>
                    <span class="exec-stat-arrow">&rarr;</span>
                </a>

                <!-- 2. Montajes Publicados -->
                <a href="{{ $montajesUrl }}" wire:navigate class="executive-stat-card">
                    <div class="executive-stat-icon-blue">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="exec-stat-body">
                        <div class="executive-stat-title">{{ $totalMontajes }} Montajes</div>
                        <div class="executive-stat-subtitle">Publicados en vitrina</div>
                    </div>
                    <span class="exec-stat-arrow">&rarr;</span>
                </a>

                <!-- 3. Familias / Categorías -->
                <a href="{{ $categoriesUrl }}" wire:navigate class="executive-stat-card">
                    <div class="executive-stat-icon-green">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div class="exec-stat-body">
                        <div class="executive-stat-title">{{ $totalCategories }} Categorías</div>
                        <div class="executive-stat-subtitle">Familias de catálogo</div>
                    </div>
                    <span class="exec-stat-arrow">&rarr;</span>
                </a>

                <!-- 4. Seguridad 2FA en Vivo -->
                <a href="{{ $twoFactorUrl }}" wire:navigate class="executive-stat-card {{ $hasTwoFactor ? 'exec-card-secure' : 'exec-card-warn' }}">
                    <div class="{{ $hasTwoFactor ? 'executive-stat-icon-green' : 'executive-stat-icon-amber' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div class="exec-stat-body">
                        <div class="executive-stat-title">
                            {{ $hasTwoFactor ? '2FA Protegido' : '2FA Pendiente' }}
                        </div>
                        <div class="executive-stat-subtitle">
                            {{ $hasTwoFactor ? 'Cuenta blindada' : 'Activar verificación' }}
                        </div>
                    </div>
                    <span class="exec-stat-arrow">&rarr;</span>
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
