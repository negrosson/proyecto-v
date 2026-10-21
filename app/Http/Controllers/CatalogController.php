<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Catalog\FilterProductsAction;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Muestra la vista principal del catálogo con filtros y categorías activas.
     */
    public function index(Request $request, FilterProductsAction $filterProducts): View
    {
        $categories = Category::query()
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        $search = $request->query('buscar');
        $categoria = $request->query('categoria');

        $products = $filterProducts->execute(
            search: is_string($search) ? $search : null,
            categorySlug: is_string($categoria) ? $categoria : null,
        );

        $featuredProducts = Product::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with('category')
            ->orderBy('name')
            ->take(8)
            ->get();

        $showcaseProducts = Product::query()
            ->where('is_active', true)
            ->whereHas('category', fn ($q) => $q->where('slug', 'mobiliario'))
            ->with('category')
            ->orderByRaw('CASE WHEN image IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('is_featured', 'desc')
            ->orderBy('stock', 'desc')
            ->take(9)
            ->get();

        return view('catalog.index', compact('categories', 'products', 'featuredProducts', 'showcaseProducts'));
    }
}
