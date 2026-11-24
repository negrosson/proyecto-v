@props([
    'categories',
    'interactive' => false,
    'adaptive' => false,
])

@php
    $totalProducts = (int) $categories->sum('products_count');
@endphp

<div x-data="{
        open: false,
        closeTimer: null,
        openMenu() {
            clearTimeout(this.closeTimer);
            this.open = true;
        },
        scheduleClose() {
            clearTimeout(this.closeTimer);
            this.closeTimer = setTimeout(() => { this.open = false; }, 140);
        }
     }"
     @mouseenter="openMenu()"
     @mouseleave="scheduleClose()"
     @keydown.escape.window="open = false"
     @click.outside="open = false"
     class="relative flex items-center">

    <button type="button"
            @click="open = !open"
            :aria-expanded="open"
            aria-haspopup="true"
            @if($adaptive)
                :class="(hasHero && !scrolled)
                    ? (open ? 'bg-white/18 text-white' : 'hover:bg-white/10 hover:text-white')
                    : (open ? 'bg-white dark:bg-zinc-800 text-zinc-950 dark:text-white shadow-xs' : 'hover:text-zinc-950 dark:hover:text-white hover:bg-white/60 dark:hover:bg-zinc-800/60')"
                class="inline-flex items-center gap-1 px-3 xl:px-3.5 py-1.5 rounded-full transition-all duration-200 uppercase cursor-pointer"
            @else
                :class="open ? 'text-zinc-950 dark:text-white' : ''"
                class="inline-flex items-center gap-1 hover:text-zinc-900 dark:hover:text-white transition-colors py-1 uppercase cursor-pointer"
            @endif>
        <span>Catálogo</span>
        <svg class="w-3.5 h-3.5 transition-transform duration-200 opacity-75"
             :class="open ? 'rotate-180' : ''"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <!-- Menú desplegable de categorías -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-180"
         x-transition:enter-start="opacity-0 translate-y-1.5 scale-98"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-130"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-1 scale-98"
         @mouseenter="openMenu()"
         @mouseleave="scheduleClose()"
         class="absolute left-1/2 -translate-x-1/2 top-full pt-3 z-50 w-72 normal-case tracking-normal"
         style="display: none;">
        <div class="rounded-2xl bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl border border-zinc-200/90 dark:border-zinc-800 shadow-2xl shadow-zinc-950/15 dark:shadow-black/50 overflow-hidden p-2">
            <!-- Encabezado -->
            <div class="px-3 pt-1.5 pb-2 flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/80 mb-1.5">
                <span class="text-[10px] font-mono uppercase tracking-[0.18em] text-zinc-400 dark:text-zinc-500 font-semibold">
                    Categorías
                </span>
                @if($totalProducts > 0)
                    <span class="text-[10px] font-mono text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/50 border border-amber-200/70 dark:border-amber-800/60 px-2 py-0.5 rounded-full font-semibold">
                        {{ $totalProducts }} artículos
                    </span>
                @endif
            </div>

            <!-- Ver todo -->
            <a href="{{ route('catalog.index') }}#catalogo"
               @if($interactive)
                   @click.prevent="open = false; filterCategory('', '{{ route('catalog.index') }}#catalogo'); document.getElementById('catalogo')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
                   :class="activeCategory === '' ? 'bg-amber-50/90 dark:bg-amber-950/40 text-amber-900 dark:text-amber-300 font-bold' : 'text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100/80 dark:hover:bg-zinc-800/80'"
               @else
                   @click="open = false"
                   class="text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100/80 dark:hover:bg-zinc-800/80"
               @endif
               class="flex items-center justify-between gap-2 px-3 py-2 rounded-xl text-xs font-semibold transition-colors mb-1">
                <span class="flex items-center gap-2 min-w-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                    <span class="truncate">Ver Catálogo Completo</span>
                </span>
                <span class="text-[10px] font-mono text-zinc-400 dark:text-zinc-500 shrink-0">Todo &rarr;</span>
            </a>

            <!-- Lista dinámica de categorías -->
            <div class="space-y-0.5 max-h-80 overflow-y-auto no-scrollbar">
                @foreach($categories as $cat)
                    <a href="{{ route('catalog.category', $cat->slug) }}#catalogo"
                       @if($interactive)
                           @click.prevent="open = false; filterCategory('{{ $cat->slug }}', '{{ route('catalog.category', $cat->slug) }}#catalogo'); document.getElementById('catalogo')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
                           :class="activeCategory === '{{ $cat->slug }}' ? 'bg-zinc-900 dark:bg-amber-500 text-white dark:text-zinc-950 font-bold' : 'text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100/90 dark:hover:bg-zinc-800/80 hover:text-zinc-950 dark:hover:text-white'"
                       @else
                           @click="open = false"
                           class="text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100/90 dark:hover:bg-zinc-800/80 hover:text-zinc-950 dark:hover:text-white"
                       @endif
                       class="group flex items-center justify-between gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all">
                        <span class="truncate">{{ $cat->name }}</span>
                        @if(isset($cat->products_count))
                            <span @if($interactive)
                                      :class="activeCategory === '{{ $cat->slug }}' ? 'bg-white/20 dark:bg-black/20 text-white dark:text-zinc-950' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 group-hover:bg-amber-100 dark:group-hover:bg-amber-950/60 group-hover:text-amber-800 dark:group-hover:text-amber-300'"
                                  @endif
                                  class="text-[10px] font-mono px-2 py-0.5 rounded-full shrink-0 transition-colors {{ $interactive ? '' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 group-hover:bg-amber-100 dark:group-hover:bg-amber-950/60 group-hover:text-amber-800 dark:group-hover:text-amber-300' }}">
                                {{ $cat->products_count }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
