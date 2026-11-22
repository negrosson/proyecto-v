<div class="space-y-4">
    <div class="rounded-2xl overflow-hidden bg-zinc-950 border border-zinc-800 flex items-center justify-center p-2">
        <img src="{{ $record->image_url }}"
             alt="{{ $record->title }}"
             class="max-h-[70vh] w-auto object-contain rounded-xl">
    </div>
    <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-zinc-500 dark:text-zinc-400 px-1">
        <div>
            <span class="font-semibold text-zinc-900 dark:text-white">Categoría:</span> {{ $record->category }}
            @if($record->montaje)
                &bull; <span class="font-semibold text-amber-600 dark:text-amber-400">En Montaje:</span> {{ $record->montaje->title }}
            @endif
        </div>
        @if($record->original_name)
            <span class="font-mono text-[11px]">Archivo original: {{ $record->original_name }}</span>
        @endif
    </div>
</div>
