@php
    /**
     * @var string $siteName
     * @var string $siteTagline
     * @var ?string $logoUrl
     * @var string $heroBadge
     * @var string $heroTitle
     * @var string $heroSubtitle
     * @var ?string $heroBgUrl
     */
@endphp

<div x-data="{ previewMode: 'light' }" class="fpv-frame">
    <!-- Barra superior del simulador -->
    <div class="fpv-toolbar">
        <div class="fpv-toolbar-left">
            <span class="fpv-dots">
                <i style="background:#ef4444;"></i>
                <i style="background:#f59e0b;"></i>
                <i style="background:#10b981;"></i>
            </span>
            <span class="fpv-live-pill">
                <span class="fpv-live-dot"></span>
                Vista Previa en Vivo · Inicio
            </span>
        </div>

        <div class="fpv-mode-switch">
            <button type="button"
                    @click="previewMode = 'light'"
                    :class="previewMode === 'light' ? 'is-active' : ''">
                Claro
            </button>
            <button type="button"
                    @click="previewMode = 'dark'"
                    :class="previewMode === 'dark' ? 'is-active' : ''">
                Oscuro
            </button>
        </div>
    </div>

    <!-- Simulador Real del Header + Hero -->
    <div class="fpv-canvas" :class="previewMode === 'dark' ? 'fpv-is-dark' : 'fpv-is-light'">
        <!-- Header simulado -->
        <div class="fpv-site-header">
            <div class="fpv-site-brand">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Vista previa logo" class="fpv-logo-img">
                @else
                    <div class="fpv-vys-fallback">
                        <span class="fpv-vys-mono">V<span style="color:#d97706;">Y</span>S</span>
                        <span class="fpv-vys-sub">Logo vectorial oficial</span>
                    </div>
                @endif
            </div>

            <div class="fpv-site-nav">
                <span>Inicio</span>
                <span>Destacados</span>
                <span>Catálogo</span>
                <span>Montajes</span>
                <span>Contacto</span>
            </div>

            <div class="fpv-site-cta-pill">Cotización · 0</div>
        </div>

        <!-- Hero simulado con Imagen de Fondo Real -->
        <div class="fpv-hero-box">
            @if($heroBgUrl)
                <img src="{{ $heroBgUrl }}" alt="Vista previa fondo hero" class="fpv-hero-bg-img">
                <div class="fpv-hero-overlay"></div>
            @else
                <div class="fpv-hero-orb-1"></div>
                <div class="fpv-hero-orb-2"></div>
            @endif

            <div class="fpv-hero-content" :class="{{ $heroBgUrl ? 'true' : 'false' }} ? 'fpv-has-bg' : ''">
                <span class="fpv-hero-badge">
                    <span class="fpv-live-dot"></span>
                    {{ $heroBadge ?: 'Catálogo Oficial' }}
                </span>

                <h4 class="fpv-hero-title">
                    {{ $heroTitle ?: 'Equipamiento y ambientación para ocasiones extraordinarias' }}
                </h4>

                <p class="fpv-hero-subtitle">
                    {{ $heroSubtitle ?: 'Mobiliario de etiqueta, vajilla fina, cristalería y mantelería para bodas, galas y celebraciones.' }}
                </p>

                <div class="fpv-hero-actions">
                    <span class="fpv-hero-btn-primary">Explorar Catálogo &rarr;</span>
                    <span class="fpv-hero-btn-ghost">Ver Montajes</span>
                </div>
            </div>
        </div>

        <!-- Pie informativo del estado de assets -->
        <div class="fpv-status-bar">
            <span>
                Logo: <strong>{{ $logoUrl ? 'Imagen personalizada cargada' : 'Logo vectorial VYS por defecto' }}</strong>
            </span>
            <span>
                Fondo Hero: <strong>{{ $heroBgUrl ? 'Fotografía personalizada cargada' : 'Fondo minimalista con luz aurora' }}</strong>
            </span>
        </div>
    </div>
</div>
