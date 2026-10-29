@php
    $categoryPlaceholders = [
        'vajilla' => 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=800&q=80',
        'manteleria' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80',
        'mobiliario' => 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&w=800&q=80',
        'equipamiento-cocina' => 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&w=800&q=80',
        'bar-decoracion' => 'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?auto=format&fit=crop&w=800&q=80',
    ];
    $defaultPlaceholder = 'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&fit=crop&w=800&q=80';

    $getProductImage = $getProductImage ?? function ($item) use ($categoryPlaceholders, $defaultPlaceholder) {
        if (!empty($item->image_url)) {
            return $item->image_url;
        }
        $slug = $item->category?->slug ?? '';
        return $categoryPlaceholders[$slug] ?? $defaultPlaceholder;
    };
@endphp

<!-- Grid de Cards de Producto -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
    @forelse ($products as $prod)
        @php
            $cardImg = $getProductImage($prod);
        @endphp
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/90 dark:border-zinc-800 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
            <div>
                <!-- Contenedor Fotográfico con Cero Cajas Grises -->
                <div @click="openDetail({
                        id: {{ $prod->id }},
                        name: '{{ addslashes($prod->name) }}',
                        code: {{ $prod->code }},
                        price: {{ $prod->rental_price }},
                        stock: {{ $prod->stock }},
                        category: '{{ addslashes($prod->category->name ?? 'Catálogo') }}',
                        description: '{{ addslashes($prod->description ?? 'Artículo de alta gama para ambientación de bodas y eventos.') }}',
                        is_sale: {{ $prod->is_sale ? 'true' : 'false' }},
                        image: '{{ $cardImg }}'
                     })"
                     class="cursor-pointer relative h-48 sm:h-56 overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                    <img src="{{ $cardImg }}" 
                         alt="{{ $prod->name }}" 
                         loading="lazy"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                    
                    <!-- Badges Flotantes con Efecto Cristal -->
                    <span class="absolute top-2.5 left-2.5 backdrop-blur-md bg-white/85 dark:bg-zinc-900/85 text-zinc-900 dark:text-zinc-100 text-[10px] font-mono font-medium px-2.5 py-0.5 rounded shadow-sm border border-white/50 dark:border-zinc-700">
                        Cód: {{ $prod->code }}
                    </span>

                    @if($prod->is_sale)
                        <span class="absolute top-2.5 right-2.5 bg-amber-400 text-zinc-950 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded shadow-sm">
                            Venta
                        </span>
                    @endif
                </div>

                <!-- Detalles del Producto -->
                <div class="p-4 sm:p-5">
                    <span class="text-[10px] font-mono tracking-widest uppercase text-amber-800 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded border border-amber-200/60 dark:border-amber-800/60 inline-block font-semibold">
                        {{ $prod->category->name ?? 'Catálogo' }}
                    </span>

                    <h3 @click="openDetail({
                            id: {{ $prod->id }},
                            name: '{{ addslashes($prod->name) }}',
                            code: {{ $prod->code }},
                            price: {{ $prod->rental_price }},
                            stock: {{ $prod->stock }},
                            category: '{{ addslashes($prod->category->name ?? 'Catálogo') }}',
                            description: '{{ addslashes($prod->description ?? 'Artículo de alta gama para ambientación de bodas y banquetes.') }}',
                            is_sale: {{ $prod->is_sale ? 'true' : 'false' }},
                            image: '{{ $cardImg }}'
                         })"
                        class="cursor-pointer font-serif font-bold text-zinc-900 dark:text-white text-base sm:text-lg mt-2 leading-snug line-clamp-1 group-hover:text-amber-800 dark:group-hover:text-amber-400 transition-colors" title="{{ $prod->name }}">
                        {{ $prod->name }}
                    </h3>

                    <div class="mt-3 pt-2.5 border-t border-zinc-100 dark:border-zinc-800 flex items-baseline justify-between">
                        <div>
                            <span class="text-[10px] text-zinc-500 dark:text-zinc-400 block uppercase tracking-wider">Valor arriendo:</span>
                            <p class="text-base sm:text-lg font-bold font-mono text-zinc-900 dark:text-white mt-0.5">
                                ${{ number_format($prod->rental_price, 0, ',', '.') }}
                                <span class="text-xs font-normal text-zinc-500 dark:text-zinc-400">/ un.</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-mono font-semibold {{ $prod->stock > 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-zinc-400 dark:text-zinc-500' }}">
                                {{ $prod->stock > 0 ? $prod->stock . ' un.' : 'Agotado' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón "+ Agregar al Arriendo" con Feedback Táctil de 1 Segundo -->
            <div class="p-4 sm:p-5 pt-0" x-data="{ added: false }">
                @if($prod->stock > 0)
                    <button @click="addItem({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $prod->rental_price }}, {{ $prod->stock }}, {{ $prod->code }}); added = true; setTimeout(() => added = false, 1000)"
                            :class="added ? 'bg-emerald-600 text-white shadow-md' : 'bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white shadow-sm'"
                            class="w-full py-2.5 px-3 rounded-xl text-xs font-semibold transition-all duration-200 flex items-center justify-center gap-1.5 active:scale-98">
                        <template x-if="!added">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>+ Agregar al Arriendo</span>
                            </span>
                        </template>
                        <template x-if="added">
                            <span class="flex items-center gap-1.5 font-bold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                <span>✓ Agregado</span>
                            </span>
                        </template>
                    </button>
                @else
                    <button disabled class="w-full py-2.5 px-3 rounded-xl text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 border border-zinc-200 dark:border-zinc-700 cursor-not-allowed">
                        Sin stock disponible
                    </button>
                @endif
            </div>
        </div>
    @empty
        <div class="col-span-full py-20 text-center bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200/90 dark:border-zinc-800 p-8 shadow-sm">
            <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto text-zinc-400 dark:text-zinc-500 mb-4">
                <svg class="w-8 h-8 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">No encontramos productos coincidentes</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-2 max-w-sm mx-auto">Intenta con términos más generales o limpia los filtros activos.</p>
            <button type="button" @click.prevent="filterCategory('', '{{ route('catalog.index') }}#catalogo')" class="inline-block mt-5 bg-zinc-900 hover:bg-zinc-800 dark:bg-amber-500 dark:hover:bg-amber-600 text-white dark:text-zinc-950 text-xs font-semibold px-6 py-2.5 rounded-full transition shadow-sm">
                Ver todo el catálogo
            </button>
        </div>
    @endforelse
</div>

<!-- Paginación -->
@if($products->hasPages())
    <div class="mt-10 flex justify-center custom-pagination">
        {{ $products->links() }}
    </div>
@endif
