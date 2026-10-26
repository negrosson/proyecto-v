<div class="space-y-4">
    <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-800">
        <table class="w-full text-left text-xs">
            <thead class="bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-[11px] font-bold">
                <tr>
                    <th class="px-3 py-2.5">Cód.</th>
                    <th class="px-3 py-2.5">Producto</th>
                    <th class="px-3 py-2.5 text-right">Precio Unit.</th>
                    <th class="px-3 py-2.5 text-center">Cantidad</th>
                    <th class="px-3 py-2.5 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse($record->items_payload ?? [] as $item)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="px-3 py-2 font-mono font-bold text-amber-600 dark:text-amber-400">{{ $item['code'] ?? '—' }}</td>
                        <td class="px-3 py-2 font-semibold text-zinc-900 dark:text-white">{{ $item['name'] ?? 'Artículo' }}</td>
                        <td class="px-3 py-2 text-right text-zinc-600 dark:text-zinc-400 font-mono">$ {{ number_format((float) ($item['unit_price'] ?? 0), 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-center font-bold text-zinc-900 dark:text-white">{{ $item['quantity'] ?? 1 }}</td>
                        <td class="px-3 py-2 text-right font-bold text-zinc-900 dark:text-white font-mono">$ {{ number_format((float) ($item['subtotal'] ?? 0), 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-4 text-center text-zinc-400">Sin desglose de ítems registrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-between p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 text-amber-900 dark:text-amber-200">
        <span class="text-xs font-bold uppercase tracking-wider">Total Estimado en Arriendo</span>
        <span class="text-base font-extrabold font-mono text-amber-950 dark:text-amber-100">$ {{ number_format((float) $record->total_amount, 0, ',', '.') }} CLP</span>
    </div>
</div>
