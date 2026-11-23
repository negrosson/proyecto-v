@php
    $getProductImage = $getProductImage ?? static fn ($item): string => $item->display_image_url;
@endphp

<!-- Grid de Cards de Producto -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-5 lg:gap-6">
    @forelse ($products as $prod)
        @php
            $cardImg = $getProductImage($prod);
            $isStudioPackshot = $prod->usesFallbackImage();
        @endphp
        <div class="min-w-0 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/90 dark:border-zinc-800 hover:border-amber-500/50 dark:hover:border-amber-400/50 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
            <div>
                <!-- Contenedor Fotográfico con Brillo Diagonal y Fondo Limpio -->
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
                     class="card-gloss-shine cursor-pointer relative h-36 sm:h-52 lg:h-56 overflow-hidden {{ $isStudioPackshot ? 'bg-white' : 'bg-zinc-100 dark:bg-zinc-800' }}">
                    <img src="{{ $cardImg }}" 
                         alt="{{ $prod->name }}" 
                         loading="lazy"
                         class="w-full h-full {{ $isStudioPackshot ? 'object-contain p-2 sm:p-3' : 'object-cover' }} group-hover:scale-105 transition-transform duration-500 ease-out">
                    
                    <!-- Badges Flotantes con Efecto Cristal -->
                    <span class="absolute top-2 left-2 sm:top-2.5 sm:left-2.5 backdrop-blur-md bg-white/85 dark:bg-zinc-900/85 text-zinc-900 dark:text-zinc-100 text-[9px] sm:text-[10px] font-mono font-medium px-1.5 sm:px-2.5 py-0.5 rounded shadow-sm border border-white/50 dark:border-zinc-700">
                        Cód: {{ $prod->code }}
                    </span>

                    @if($prod->is_sale)
                        <span class="absolute top-2 right-2 sm:top-2.5 sm:right-2.5 bg-amber-400 text-zinc-950 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider px-1.5 sm:px-2 py-0.5 rounded shadow-sm">
                            Venta
                        </span>
                    @endif
                </div>

                <!-- Detalles del Producto -->
                <div class="p-2.5 sm:p-5">
                    <span class="max-w-full truncate text-[9px] sm:text-[10px] font-mono tracking-wider sm:tracking-widest uppercase text-amber-800 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-1.5 sm:px-2 py-0.5 rounded border border-amber-200/60 dark:border-amber-800/60 inline-block font-semibold">
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
                        class="cursor-pointer font-serif font-bold text-zinc-900 dark:text-white text-sm sm:text-lg mt-1.5 sm:mt-2 leading-snug line-clamp-2 min-h-[2.45rem] sm:min-h-0 sm:line-clamp-1 group-hover:text-amber-800 dark:group-hover:text-amber-400 transition-colors" title="{{ $prod->name }}">
                        {{ $prod->name }}
                    </h3>

                    <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-2.5 border-t border-zinc-100 dark:border-zinc-800 flex flex-wrap items-baseline justify-between gap-x-1.5 gap-y-0.5">
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] text-zinc-500 dark:text-zinc-400 block uppercase tracking-wider">Arriendo:</span>
                            <p class="text-sm sm:text-lg font-bold font-mono text-zinc-900 dark:text-white mt-0.5 whitespace-nowrap">
                                ${{ number_format($prod->rental_price, 0, ',', '.') }}
                                <span class="text-[10px] sm:text-xs font-normal text-zinc-500 dark:text-zinc-400">/ un.</span>
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[9px] sm:text-[10px] font-mono font-semibold {{ $prod->stock > 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-zinc-400 dark:text-zinc-500' }}">
                                {{ $prod->stock > 0 ? $prod->stock . ' un.' : 'Agotado' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón "+ Agregar al Arriendo" con Vuelo al Carrito y Feedback Táctil -->
            <div class="p-2.5 sm:p-5 pt-0" x-data="{ added: false }">
                @if($prod->stock > 0)
                    <button @click="addItem({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $prod->rental_price }}, {{ $prod->stock }}, {{ $prod->code }}, $event, '{{ $cardImg }}'); added = true; setTimeout(() => added = false, 1000)"
                            :class="added ? 'bg-emerald-600 text-white shadow-md' : 'bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white shadow-sm'"
                            class="w-full py-2 sm:py-2.5 px-2 sm:px-3 rounded-xl text-[11px] sm:text-xs font-semibold transition-all duration-200 flex items-center justify-center gap-1 sm:gap-1.5 active:scale-98 whitespace-nowrap">
                        <template x-if="!added">
                            <span class="flex items-center gap-1 sm:gap-1.5">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span class="sm:hidden">Agregar</span>
                                <span class="hidden sm:inline">Agregar al Arriendo</span>
                            </span>
                        </template>
                        <template x-if="added">
                            <span class="flex items-center gap-1 sm:gap-1.5 font-bold">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                <span>Agregado</span>
                            </span>
                        </template>
                    </button>
                @else
                    <button disabled class="w-full py-2 sm:py-2.5 px-2 sm:px-3 rounded-xl text-[11px] sm:text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 border border-zinc-200 dark:border-zinc-700 cursor-not-allowed">
                        Sin stock
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
    <div class="mt-8 sm:mt-10 flex justify-center w-full max-w-full overflow-x-auto pb-1 custom-pagination">
        {{ $products->links() }}
    </div>
@endif
