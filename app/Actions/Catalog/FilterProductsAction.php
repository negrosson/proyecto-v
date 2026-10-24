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
            ->with('category:id,name,slug')
            ->where('is_active', true);

        if ($categorySlug !== null && trim($categorySlug) !== '') {
            $query->whereHas('category', function (Builder $q) use ($categorySlug): void {
                $q->where('slug', trim($categorySlug));
            });
        }

        if ($search !== null && trim($search) !== '') {
            $trimmedSearch = trim($search);

            if (ctype_digit($trimmedSearch)) {
                $code = (int) $trimmedSearch;
                $escapedSearch = addcslashes($trimmedSearch, '%_\\');

                $query->where(function (Builder $q) use ($code, $escapedSearch): void {
                    $q->where('code', $code);

                    if ($q->getConnection()->getDriverName() === 'sqlite') {
                        $q->orWhereRaw('name like ? escape \'\\\'', ['%'.$escapedSearch.'%']);
                    } else {
                        $q->orWhere('name', 'like', '%'.$escapedSearch.'%');
                    }
                });
            } else {
                $escapedSearch = addcslashes($trimmedSearch, '%_\\');

                $query->where(function (Builder $q) use ($escapedSearch): void {
                    if ($q->getConnection()->getDriverName() === 'sqlite') {
                        $q->whereRaw('name like ? escape \'\\\'', ['%'.$escapedSearch.'%'])
                            ->orWhereRaw('code like ? escape \'\\\'', ['%'.$escapedSearch.'%']);
                    } else {
                        $q->where('name', 'like', '%'.$escapedSearch.'%')
                            ->orWhere('code', 'like', '%'.$escapedSearch.'%');
                    }
                });
            }
        }

        return $query->orderBy('name')->paginate($perPage)->withQueryString();
    }
}
