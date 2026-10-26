<?php

declare(strict_types=1);

namespace App\Actions\Catalog;

use App\Models\Product;

final class TrackProductClickAction
{
    /**
     * Incrementa atómicamente el contador de clics / interés de un producto activo.
     */
    public function execute(int $productId): bool
    {
        return Product::query()
            ->where('id', $productId)
            ->where('is_active', true)
            ->increment('clicks_count') > 0;
    }
}
