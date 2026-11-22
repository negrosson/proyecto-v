<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class MoveProductsToCategoryAction
{
    /**
     * Mueve productos hacia una categoría destino, ya sea todos los de una categoría origen
     * o una lista seleccionada manualmente de IDs de productos.
     *
     * @param  array<int|string>|null  $productIds
     */
    public function execute(
        int $targetCategoryId,
        ?int $sourceCategoryId = null,
        ?array $productIds = null,
        string $mode = 'all',
    ): int {
        if (! Category::query()->whereKey($targetCategoryId)->exists()) {
            throw new InvalidArgumentException('La categoría de destino seleccionada no existe.');
        }

        if ($sourceCategoryId !== null && $sourceCategoryId === $targetCategoryId) {
            throw new InvalidArgumentException('La categoría de destino debe ser distinta a la categoría de origen.');
        }

        $query = Product::query();

        if ($sourceCategoryId !== null) {
            $query->where('category_id', $sourceCategoryId);
        }

        if ($mode === 'manual' || $sourceCategoryId === null) {
            $cleanIds = array_values(array_unique(array_filter(
                array_map(static fn (mixed $id): int => (int) $id, $productIds ?? []),
                static fn (int $id): bool => $id > 0
            )));

            if ($cleanIds === []) {
                return 0;
            }

            $query->whereIn('id', $cleanIds);
        }

        $movedCount = DB::transaction(static function () use ($query, $targetCategoryId): int {
            return $query->update([
                'category_id' => $targetCategoryId,
                'updated_at' => now(),
            ]);
        });

        Cache::forget('filament_badge_products_count');
        Cache::forget('filament_badge_categories_count');

        return $movedCount;
    }
}
