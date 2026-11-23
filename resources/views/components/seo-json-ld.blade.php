@props([
    'settings' => null,
    'pageType' => 'home',
    'faqs' => null,
    'categories' => null,
    'activeCategory' => null,
    'breadcrumbs' => [],
])

@php
    $siteUrl = rtrim((string) config('app.url', url('/')), '/');
    $siteName = $settings?->site_name ?? 'VYS · Productora de Eventos';
    $siteDescription = $settings?->hero_subtitle ?? 'Arriendo de mobiliario de etiqueta, vajilla fina, cristalería y mantelería para bodas, galas y eventos corporativos en Santiago y regiones.';
    $logoUrl = ! empty($settings?->logo_path)
        ? asset('storage/'.$settings->logo_path)
        : asset('images/brand/logo-vys.svg');

    $sameAs = array_values(array_filter([
        $settings?->instagram_url,
        $settings?->facebook_url,
        $settings?->tiktok_url,
    ], static fn (mixed $url): bool => is_string($url) && trim($url) !== ''));

    $localBusiness = [
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        '@id' => $siteUrl.'/#business',
        'name' => $siteName,
        'description' => $siteDescription,
        'url' => $siteUrl,
        'logo' => $logoUrl,
        'image' => ! empty($settings?->hero_bg_image)
            ? asset('storage/'.$settings->hero_bg_image)
            : asset('images/montajes/teno-mesa-principal.jpg'),
        'telephone' => $settings?->cleanTelPhone() ?? '+56229876543',
        'email' => $settings?->company_email ?? 'contacto@arriendomobiliario.cl',
        'priceRange' => '$$',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $settings?->company_address ?? 'Av. El Parque 4120, Huechuraba',
            'addressLocality' => 'Santiago',
            'addressRegion' => 'Región Metropolitana',
            'addressCountry' => 'CL',
        ],
        'areaServed' => [
            ['@type' => 'City', 'name' => 'Santiago'],
            ['@type' => 'AdministrativeArea', 'name' => 'Región Metropolitana de Santiago'],
            ['@type' => 'Country', 'name' => 'Chile'],
        ],
    ];

    if ($sameAs !== []) {
        $localBusiness['sameAs'] = $sameAs;
    }

    if ($categories !== null && count($categories) > 0) {
        $localBusiness['hasOfferCatalog'] = [
            '@type' => 'OfferCatalog',
            'name' => 'Catálogo de Arriendo de Mobiliario y Menaje para Eventos',
            'itemListElement' => collect($categories)->map(static fn ($cat): array => [
                '@type' => 'OfferCatalog',
                'name' => (string) $cat->name,
                'url' => route('catalog.category', $cat->slug),
            ])->values()->all(),
        ];
    }

    $schemas = [$localBusiness];

    if ($pageType === 'home') {
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => $siteUrl.'/#website',
            'url' => $siteUrl,
            'name' => $siteName,
            'inLanguage' => 'es-CL',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => $siteUrl.'/?buscar={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    if ($faqs !== null && count($faqs) > 0) {
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => collect($faqs)->map(static fn ($faq): array => [
                '@type' => 'Question',
                'name' => (string) $faq->question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => (string) $faq->answer,
                ],
            ])->values()->all(),
        ];
    }

    if (is_array($breadcrumbs) && count($breadcrumbs) > 0) {
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($breadcrumbs)->values()->map(static fn (array $crumb, int $index): array => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => (string) $crumb['name'],
                'item' => (string) $crumb['url'],
            ])->all(),
        ];
    }
@endphp

@foreach ($schemas as $schema)
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endforeach
