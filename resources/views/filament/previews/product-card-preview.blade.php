@php
    /**
     * @var string $name
     * @var string|int|null $code
     * @var int $rentalPrice
     * @var int $stock
     * @var bool $isSale
     * @var string $categoryName
     * @var ?string $imageUrl
     * @var bool $isFallback
     */
@endphp

<div class="fpv-frame">
    <div class="fpv-toolbar">
        <div class="fpv-toolbar-left">
            <span class="fpv-dots">
                <i style="background:#ef4444;"></i>
                <i style="background:#f59e0b;"></i>
                <i style="background:#10b981;"></i>
            </span>
            <span class="fpv-live-pill">
                <span class="fpv-live-dot"></span>
                Vista Previa · Tarjeta en Catálogo
            </span>
        </div>
        <span style="font-size:0.7rem; color:#71717a; font-family:ui-monospace, monospace;">
            {{ $isFallback ? 'Miniatura automática fondo blanco' : 'Fotografía propia cargada' }}
        </span>
    </div>

    <div class="fpv-canvas fpv-is-light" style="padding: 1.25rem; display:flex; justify-content:center;">
        <div class="fpv-prod-card">
            <div class="fpv-prod-media {{ $isFallback ? 'is-packshot' : '' }}">
                <img src="{{ $imageUrl }}" alt="{{ $name ?: 'Producto' }}">
                <span class="fpv-prod-code">Cód: {{ $code ?: '—' }}</span>
                @if($isSale)
                    <span class="fpv-prod-sale">Venta</span>
                @endif
            </div>
            <div class="fpv-prod-body">
                <span class="fpv-prod-cat">{{ $categoryName ?: 'Catálogo' }}</span>
                <h5>{{ $name ?: 'Nombre del Producto' }}</h5>
                <div class="fpv-prod-price-row">
                    <div>
                        <span class="fpv-prod-price-label">VALOR ARRIENDO:</span>
                        <div class="fpv-prod-price">${{ number_format($rentalPrice, 0, ',', '.') }} <span>/ un.</span></div>
                    </div>
                    <span class="fpv-prod-stock {{ $stock > 0 ? 'in-stock' : 'out-stock' }}">
                        {{ $stock > 0 ? $stock . ' un.' : 'Agotado' }}
                    </span>
                </div>
                <div class="fpv-prod-btn">
                    {{ $stock > 0 ? '+ Agregar al Arriendo' : 'Sin stock disponible' }}
                </div>
            </div>
        </div>
    </div>
</div>
