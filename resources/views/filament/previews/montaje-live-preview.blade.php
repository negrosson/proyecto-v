@php
    /**
     * @var string $title
     * @var string $type
     * @var string $year
     * @var string $location
     * @var string $capacity
     * @var string $summary
     * @var string $description
     * @var ?string $coverUrl
     * @var array<int, string> $galleryUrls
     */

    $effectiveCover = $coverUrl ?? ($galleryUrls[0] ?? asset('images/montajes/teno-mesa-principal.jpg'));
    $subPhotos = array_values(array_slice(
        array_filter($galleryUrls, static fn (string $url): bool => $url !== $effectiveCover),
        0,
        4
    ));
@endphp

<div x-data="{ tab: 'spotlight' }" class="fpv-frame">
    <div class="fpv-toolbar">
        <div class="fpv-toolbar-left">
            <span class="fpv-dots">
                <i style="background:#ef4444;"></i>
                <i style="background:#f59e0b;"></i>
                <i style="background:#10b981;"></i>
            </span>
            <span class="fpv-live-pill">
                <span class="fpv-live-dot"></span>
                Vista Previa en Vivo · Montaje
            </span>
        </div>

        <div class="fpv-mode-switch">
            <button type="button"
                    @click="tab = 'spotlight'"
                    :class="tab === 'spotlight' ? 'is-active' : ''">
                Spotlight Inicio
            </button>
            <button type="button"
                    @click="tab = 'card'"
                    :class="tab === 'card' ? 'is-active' : ''">
                Tarjeta Portafolio
            </button>
        </div>
    </div>

    <div class="fpv-canvas fpv-is-light" style="padding: 1.25rem;">
        <!-- MODO 1: Spotlight en Página de Inicio -->
        <div x-show="tab === 'spotlight'">
            <div class="fpv-montaje-head">
                <div>
                    <h4 class="fpv-montaje-title">{{ $title ?: 'Título del Montaje de Evento' }}</h4>
                    <div class="fpv-montaje-pills">
                        <span>Tipo: <strong>{{ $type ?: 'Evento Empresa' }}</strong></span>
                        <span>Año: <strong>{{ $year ?: date('Y') }}</strong></span>
                        @if($location)
                            <span>Lugar: <strong>{{ $location }}</strong></span>
                        @endif
                    </div>
                </div>
                <p class="fpv-montaje-summary">
                    {{ $summary ?: 'Resumen corto de la puesta en escena que aparecerá junto al título principal.' }}
                </p>
            </div>

            <!-- Portada Principal -->
            <div class="fpv-montaje-cover">
                <img src="{{ $effectiveCover }}" alt="Vista previa portada montaje">
                <div class="fpv-montaje-cover-caption">
                    <span class="fpv-cap-badge">{{ $capacity ?: 'Montaje Premium' }}</span>
                    <p>{{ $summary ?: ($description ? \Illuminate\Support\Str::limit($description, 100) : 'Vista principal del montaje') }}</p>
                </div>
            </div>

            <!-- Fotos Secundarias Únicas (adaptable igual que el inicio real) -->
            @if(count($subPhotos) > 0)
                <div class="fpv-montaje-subgrid" style="grid-template-columns: repeat({{ min(count($subPhotos), 4) }}, minmax(0, 1fr));">
                    @foreach($subPhotos as $idx => $url)
                        <div class="fpv-montaje-subitem">
                            <img src="{{ $url }}" alt="Vista previa galería {{ $idx + 1 }}">
                            <span class="fpv-sub-tag">Foto {{ $idx + 2 }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="fpv-empty-hint">
                    Solo tienes foto de portada por ahora (sin duplicados debajo). Sube 1 a 4 fotos en <strong>Galería Fotográfica</strong> para ver cómo se acomodan en la grilla inferior.
                </div>
            @endif
        </div>

        <!-- MODO 2: Tarjeta de Portafolio -->
        <div x-show="tab === 'card'" style="display:none; max-width: 22rem; margin: 0 auto;">
            <div class="fpv-portfolio-card">
                <div class="fpv-portfolio-media">
                    <img src="{{ $effectiveCover }}" alt="Vista previa tarjeta montaje">
                    <span class="fpv-year-badge">{{ $year ?: date('Y') }}</span>
                    <span class="fpv-gold-badge">{{ $capacity ?: 'Montaje Premium' }}</span>
                </div>
                <div class="fpv-portfolio-body">
                    <span class="fpv-portfolio-meta">{{ $type ?: 'Evento' }} &bull; {{ $location ?: 'Santiago' }}</span>
                    <h5>{{ $title ?: 'Título del Montaje' }}</h5>
                    <p>{{ \Illuminate\Support\Str::limit($description ?: ($summary ?: 'Descripción del montaje que verán los clientes en la galería.'), 110) }}</p>
                    <div class="fpv-portfolio-btn">Ver Detalle del Montaje &rarr;</div>
                </div>
            </div>
        </div>
    </div>
</div>
