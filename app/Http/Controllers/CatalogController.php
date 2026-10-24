<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Catalog\FilterProductsAction;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Muestra la vista principal del catálogo con filtros, configuración CMS y FAQs.
     */
    public function index(Request $request, FilterProductsAction $filterProducts): View
    {
        $settings = SiteSetting::current();

        $faqs = Faq::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

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

        return view('catalog.index', compact(
            'categories',
            'products',
            'featuredProducts',
            'showcaseProducts',
            'settings',
            'faqs'
        ));
    }

    /**
     * Búsqueda en tiempo real (Live Search) protegida contra DoS y payloads maliciosos.
     */
    public function search(Request $request, FilterProductsAction $filterProducts): JsonResponse
    {
        $rawQ = $request->string('q')->trim()->toString();
        $q = mb_substr($rawQ, 0, 60);

        $categoria = $request->query('categoria');
        $categorySlug = is_string($categoria) && trim($categoria) !== '' ? trim($categoria) : null;

        $products = $filterProducts->execute(
            search: $q !== '' ? $q : null,
            categorySlug: $categorySlug,
            perPage: 20,
        );

        $data = $products->getCollection()->map(static fn (Product $product): array => [
            'id' => $product->id,
            'code' => $product->code,
            'name' => $product->name,
            'rental_price' => $product->rental_price,
            'stock' => $product->stock,
            'category_name' => $product->category?->name ?? 'Catálogo',
            'category_slug' => $product->category?->slug ?? '',
            'image' => $product->image_url,
        ])->values();

        return response()->json([
            'data' => $data,
            'total' => $products->total(),
        ]);
    }
}
