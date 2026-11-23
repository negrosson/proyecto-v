<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Montaje;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Genera el mapa del sitio XML dinámico (/sitemap.xml) para Google y Bing.
     */
    public function index(): Response
    {
        $xml = Cache::remember('seo.sitemap_xml', 3600, function (): string {
            $urls = [];

            $settingsUpdatedAt = SiteSetting::query()->max('updated_at');
            $productsUpdatedAt = Product::query()->where('is_active', true)->max('updated_at');
            $montajesUpdatedAt = Montaje::query()->where('is_active', true)->max('updated_at');

            $homeLastMod = collect([$settingsUpdatedAt, $productsUpdatedAt, $montajesUpdatedAt])
                ->filter()
                ->map(static fn (mixed $date): Carbon => Carbon::parse((string) $date))
                ->sortDesc()
                ->first() ?? now();

            $urls[] = [
                'loc' => route('catalog.index'),
                'lastmod' => $homeLastMod->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ];

            $urls[] = [
                'loc' => route('catalog.montajes'),
                'lastmod' => $montajesUpdatedAt ? Carbon::parse((string) $montajesUpdatedAt)->toAtomString() : $homeLastMod->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ];

            $urls[] = [
                'loc' => route('catalog.como-cotizar'),
                'lastmod' => $settingsUpdatedAt ? Carbon::parse((string) $settingsUpdatedAt)->toAtomString() : $homeLastMod->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ];

            // Rutas SEO limpias por categoría activa (solo categorías con al menos 1 producto activo)
            $categories = Category::query()
                ->whereHas('products', static fn ($q) => $q->where('is_active', true))
                ->withMax(['products as latest_product_updated_at' => static fn ($q) => $q->where('is_active', true)], 'updated_at')
                ->orderBy('name')
                ->get();

            foreach ($categories as $category) {
                $catLastMod = $category->latest_product_updated_at ?? $category->updated_at ?? now();

                $urls[] = [
                    'loc' => route('catalog.category', $category->slug),
                    'lastmod' => Carbon::parse((string) $catLastMod)->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.9',
                ];
            }

            $lines = [
                '<?xml version="1.0" encoding="UTF-8"?>',
                '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
            ];

            foreach ($urls as $entry) {
                $loc = htmlspecialchars($entry['loc'], ENT_XML1 | ENT_COMPAT, 'UTF-8');
                $lastmod = htmlspecialchars($entry['lastmod'], ENT_XML1 | ENT_COMPAT, 'UTF-8');

                $lines[] = '  <url>';
                $lines[] = "    <loc>{$loc}</loc>";
                $lines[] = "    <lastmod>{$lastmod}</lastmod>";
                $lines[] = "    <changefreq>{$entry['changefreq']}</changefreq>";
                $lines[] = "    <priority>{$entry['priority']}</priority>";
                $lines[] = '  </url>';
            }

            $lines[] = '</urlset>';

            return implode("\n", $lines)."\n";
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
