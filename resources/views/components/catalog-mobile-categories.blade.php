@props([
    'categories',
    'interactive' => false,
    'stepNumber' => null,
])

<div x-data="{ mobileCatalogOpen: false }" class="rounded-xl overflow-hidden">
    <button type="button"
            @click="mobileCatalogOpen = !mobileCatalogOpen"
            :aria-expanded="mobileCatalogOpen"
            class="w-full px-3 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex items-center justify-between text-left text-sm font-medium text-zinc-800 dark:text-zinc-200">
        <span class="flex items-center gap-2">
            <span>Catálogo</span>
        </span>
        <span class="flex items-center gap-2">
            @if($stepNumber)
                <span class="text-xs text-zinc-400 font-mono">{{ $stepNumber }}</span>
            @endif
            <svg class="w-4 h-4 text-zinc-400 transition-transform duration-200"
                 :class="mobileCatalogOpen ? 'rotate-180 text-amber-600 dark:text-amber-400' : ''"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </span>
    </button>

    <div x-show="mobileCatalogOpen"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         class="mt-1 ml-3 pl-3 border-l-2 border-amber-400/50 dark:border-amber-500/40 space-y-1 pb-1"
         style="display: none;">
        @foreach($categories as $cat)
            <a href="{{ route('catalog.category', $cat->slug) }}#catalogo"
               @if($interactive)
                   @click.prevent="mobileCatalogOpen = false; mobileMenuOpen = false; filterCategory('{{ $cat->slug }}', '{{ route('catalog.category', $cat->slug) }}#catalogo'); document.getElementById('catalogo')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
                   :class="activeCategory === '{{ $cat->slug }}' ? 'bg-zinc-900 dark:bg-amber-500 text-white dark:text-zinc-950 font-semibold' : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800'"
               @else
                   @click="mobileCatalogOpen = false; mobileMenuOpen = false"
                   class="text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800"
               @endif
               class="flex items-center justify-between gap-2 px-3 py-1.5 rounded-lg text-xs transition">
                <span class="truncate">{{ $cat->name }}</span>
                @if(isset($cat->products_count))
                    <span class="text-[10px] font-mono opacity-75 shrink-0">({{ $cat->products_count }})</span>
                @endif
            </a>
        @endforeach
    </div>
</div>
