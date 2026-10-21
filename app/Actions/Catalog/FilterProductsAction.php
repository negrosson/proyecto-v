<?php

declare(strict_types=1);

namespace App\Actions\Catalog;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class FilterProductsAction
{
    /**
     * Filtra y pagina los productos activos del catálogo por categoría y/o término de búsqueda.
     */
    public function execute(?string $search = null, ?string $categorySlug = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = Product::query()
            ->with('category')
            ->where('is_active', true);

        if ($categorySlug !== null && trim($categorySlug) !== '') {
            $query->whereHas('category', function (Builder $q) use ($categorySlug): void {
                $q->where('slug', trim($categorySlug));
            });
        }

        if ($search !== null && trim($search) !== '') {
            $sanitizedSearch = trim($search);
            $escapedSearch = addcslashes($sanitizedSearch, '%_\\');

            $query->where(function (Builder $q) use ($escapedSearch): void {
                $q->where('name', 'like', '%'.$escapedSearch.'%')
                    ->orWhere('code', 'like', '%'.$escapedSearch.'%');
            });
        }

        return $query->orderBy('name')->paginate($perPage)->withQueryString();
    }
}
