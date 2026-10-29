<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Catalog\FilterProductsAction;
use App\Actions\Catalog\TrackProductClickAction;
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

        $carouselProducts = Product::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('is_featured', true)
                    ->orWhereNotNull('image');
            })
            ->with('category')
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->take(12)
            ->get();

        if ($carouselProducts->isEmpty()) {
            $carouselProducts = Product::query()
                ->where('is_active', true)
                ->with('category')
                ->orderBy('name')
                ->take(8)
                ->get();
        }

        $montajes = $this->getMontajesData();

        return view('catalog.index', compact(
            'categories',
            'products',
            'featuredProducts',
            'showcaseProducts',
            'carouselProducts',
            'montajes',
            'settings',
            'faqs'
        ));
    }

    /**
     * Muestra la vista dedicada de montajes y galerías de proyectos reales.
     */
    public function montajes(): View
    {
        $settings = SiteSetting::current();
        $montajes = $this->getMontajesData();

        return view('catalog.montajes', compact('settings', 'montajes'));
    }

    /**
     * Muestra la guía paso a paso sobre cómo cotizar mobiliario y el proceso de reserva.
     */
    public function comoCotizar(): View
    {
        $settings = SiteSetting::current();
        $categories = Category::query()->where('is_active', true)->orderBy('sort_order')->get();

        return view('catalog.como-cotizar', compact('settings', 'categories'));
    }

    /**
     * Retorna la colección de proyectos reales de montaje documentados con galería.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMontajesData(): array
    {
        return [
            [
                'id' => 'teno-2023',
                'slug' => 'teno-2023',
                'title' => 'T E N O   2 0 2 3',
                'client' => 'Agrozzi',
                'type' => 'Evento Empresa',
                'year' => '2023',
                'location' => 'Teno, Curicó',
                'capacity' => 'Montaje completo para 250 personas',
                'summary' => 'Evento para Agrozzi en Teno, realizado el 2023. Montaje completo para 250 personas.',
                'description' => 'Montaje integral de banquetería corporativa a gran escala. Se implementaron mesas redondas con mantelería base color crudo, caminos de yute natural, centros de mesa florales silvestres y sillas Tiffany blancas. El servicio incluyó vajilla completa de cerámica, cubiertos de acero inoxidable y cristalería fina de vino y agua.',
                'cover_image' => asset('images/montajes/teno-mesa-principal.jpg'),
                'image' => asset('images/montajes/teno-mesa-principal.jpg'),
                'gallery' => [
                    asset('images/montajes/teno-mesa-principal.jpg'),
                    asset('images/montajes/teno-salon-250p.jpg'),
                    asset('images/montajes/montaje-teno.png'),
                ],
                'items_used' => [
                    '250x Sillas Tiffany Blancas',
                    '25x Mesas Redondas de Banquete (10 personas)',
                    '25x Manteles Redondos Base Cruda',
                    '25x Caminos de Mesa en Yute Rústico',
                    '250x Copas de Vino & Copas de Agua',
                    'Vajilla Completa de 3 Tiempos',
                ],
            ],
            [
                'id' => 'gala-nocturna',
                'slug' => 'gala-nocturna',
                'title' => 'Gala Nocturna & Ambientación Dorada',
                'client' => 'Corporativo Santiago',
                'type' => 'Gala & Cena de Fin de Año',
                'year' => '2024',
                'location' => 'Las Condes, Santiago',
                'capacity' => 'Cena de Gala para 180 invitados',
                'summary' => 'Ambientación de gala con iluminación cálida, centros de rosas rojas y bajoplatos dorados.',
                'description' => 'Producción de alta etiqueta diseñada para una celebración de fin de año corporativa. Mesas redondas con bajoplatos de oro viejo, copas talladas de cristal, servilletas de lino dobladas a mano y rosas rojas importadas como punto focal. Zona perimetral equipada con barras iluminadas y lounges de descanso.',
                'cover_image' => asset('images/montajes/gala-nocturna-dorada.jpg'),
                'image' => asset('images/montajes/gala-nocturna-dorada.jpg'),
                'gallery' => [
                    asset('images/montajes/gala-nocturna-dorada.jpg'),
                    asset('images/montajes/montajes-galeria.png'),
                ],
                'items_used' => [
                    '180x Sillas Tiffany Doradas',
                    '18x Mesas Circulares Amplias',
                    '180x Bajoplatos Dorados de Gala',
                    'Cristalería Bar & Coctelería de Lujo',
                    'Barras Modulares Retroiluminadas',
                ],
            ],
            [
                'id' => 'black-and-white',
                'slug' => 'black-and-white',
                'title' => 'Banquete Moderno Black & White',
                'client' => 'Institucional Metropolitano',
                'type' => 'Evento Corporativo',
                'year' => '2024',
                'location' => 'Huechuraba, Región Metropolitana',
                'capacity' => '320 comensales simultáneos',
                'summary' => 'Contraste vanguardista con manteles negros profundos y sillas blancas Tiffany.',
                'description' => 'Un concepto visual audaz y distinguido montado en un centro de eventos empresarial. La combinación de manteles negros azabache con la pureza de las sillas Tiffany blancas y los arreglos florales de temporada brindaron un impacto estético memorable a los más de 300 invitados.',
                'cover_image' => asset('images/montajes/banquete-black-and-white.jpg'),
                'image' => asset('images/montajes/banquete-black-and-white.jpg'),
                'gallery' => [
                    asset('images/montajes/banquete-black-and-white.jpg'),
                    asset('images/montajes/montajes-galeria.png'),
                ],
                'items_used' => [
                    '320x Sillas Blancas para Eventos',
                    '32x Manteles Circulares Negros',
                    '32x Caminos de Mesa Estructurados',
                    'Vajilla Cuadrada Moderna & Platería',
                    'Calefactores de Ambiente a Gas',
                ],
            ],
            [
                'id' => 'pabellon-campestre',
                'slug' => 'pabellon-campestre',
                'title' => 'Boda Chic en Pabellón Acristalado',
                'client' => 'Matrimonio Privado',
                'type' => 'Matrimonio & Boda',
                'year' => '2025',
                'location' => 'Chicureo, Colina',
                'capacity' => '200 invitados en salón campestre',
                'summary' => 'Celebración campestre elegante con lámparas de mimbre colgantes y vajilla clásica.',
                'description' => 'Montaje soñado para un matrimonio de tarde en salón con ventanales de piso a cielo. Las mesas redondas vistieron manteles blancos inmaculados con detalles rústicos elegantes, copas de cristal labrado y una cálida integración de luminarias de mimbre que aportaron calidez al atardecer.',
                'cover_image' => asset('images/montajes/matrimonio-campestre-pabellon.jpg'),
                'image' => asset('images/montajes/matrimonio-campestre-pabellon.jpg'),
                'gallery' => [
                    asset('images/montajes/matrimonio-campestre-pabellon.jpg'),
                    asset('images/montajes/montajes-galeria.png'),
                ],
                'items_used' => [
                    '200x Sillas de Madera & Tiffany Blancas',
                    'Lámparas Colgantes de Mimbre',
                    'Vajilla de Porcelana con Borde Plateado',
                    'Copas de Espumante & Vino Reserva',
                    'Mesas Auxiliares de Degustación',
                ],
            ],
        ];
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

    /**
     * Registra la interacción o clic de interés sobre un producto del catálogo.
     */
    public function trackClick(Request $request, Product $product, TrackProductClickAction $action): JsonResponse
    {
        $action->execute($product->id);

        return response()->json([
            'success' => true,
            'clicks_count' => (int) $product->fresh()->clicks_count,
        ]);
    }
}
