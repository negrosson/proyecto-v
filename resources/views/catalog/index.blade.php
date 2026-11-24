@php
    $getProductImage = static fn ($item): string => $item->display_image_url;
    $activeCategory = $activeCategory ?? null;
    $siteBrand = $settings->site_name ?? 'VYS · Productora de Eventos';
    $seoTitle = $activeCategory
        ? "Arriendo de {$activeCategory->name} para Eventos en Santiago | {$siteBrand}"
        : "{$siteBrand} | ".($settings->site_tagline ?? 'Arriendo de Mobiliario, Vajilla y Menaje para Eventos en Santiago');
    $seoDescription = $activeCategory
        ? "Arriendo de {$activeCategory->name} para bodas, matrimonios, galas y eventos corporativos en Santiago y regiones. Cotiza online directo por WhatsApp con {$siteBrand}."
        : ($settings->hero_subtitle ?? 'Arriendo de mobiliario de etiqueta, vajilla fina, cristalería y mantelería para bodas, galas y eventos corporativos en Santiago.');
    $canonicalUrl = $activeCategory
        ? route('catalog.category', $activeCategory->slug)
        : route('catalog.index');
    $seoBreadcrumbs = $activeCategory
        ? [
            ['name' => 'Inicio', 'url' => route('catalog.index')],
            ['name' => "Arriendo de {$activeCategory->name}", 'url' => $canonicalUrl],
        ]
        : [];
@endphp
<!DOCTYPE html>
<html lang="es-CL" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- OpenGraph & WhatsApp Preview -->
    <meta property="og:type" content="website">
    <meta property="og:locale" content="es_CL">
    <meta property="og:site_name" content="{{ $siteBrand }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:image" content="{{ $activeCategory?->slug === 'vajilla' ? asset('images/products/portada-vajilla-cristaleria.webp') : (!empty($settings?->hero_bg_image) ? asset('storage/' . $settings->hero_bg_image) : asset('images/montajes/teno-mesa-principal.jpg')) }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">

    <!-- Datos Estructurados Schema.org (JSON-LD para Google y Bing) -->
    <x-seo-json-ld
        :settings="$settings"
        :page-type="$activeCategory ? 'category' : 'home'"
        :faqs="$faqs"
        :categories="$categories"
        :active-category="$activeCategory"
        :breadcrumbs="$seoBreadcrumbs"
    />

    <!-- Google Fonts: Cormorant Garamond (Editorial Luxury) & Plus Jakarta Sans (UI Clean) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS Compilado con Vite (Producción) y Alpine.js -->
    @vite(['resources/css/app.css'])
    <script>
        (function() {
            var theme = localStorage.getItem('theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    <script defer src="{{ asset('vendor/alpinejs/alpine.min.js') }}"></script>

    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Paginación Editorial Luxury (Responsiva sin desborde en móvil) */
        .custom-pagination nav { width: 100%; max-width: 100%; }
        .custom-pagination nav > div { flex-wrap: wrap; justify-content: center; gap: 0.35rem; }
        .custom-pagination nav svg { width: 1.05rem; height: 1.05rem; }
        .custom-pagination nav span, 
        .custom-pagination nav a {
            background-color: #FFFFFF !important;
            color: #27272A !important;
            border-color: #E4E4E7 !important;
            border-radius: 0.75rem !important;
            margin: 0.1rem !important;
            font-size: 0.75rem !important;
            padding: 0.4rem 0.7rem !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;
        }
        @media (min-width: 640px) {
            .custom-pagination nav span,
            .custom-pagination nav a {
                margin: 0 0.2rem !important;
                font-size: 0.8125rem !important;
                padding: 0.5rem 0.85rem !important;
            }
        }
        .dark .custom-pagination nav span,
        .dark .custom-pagination nav a {
            background-color: #18181B !important;
            color: #F4F4F5 !important;
            border-color: #27272A !important;
        }
        .custom-pagination nav a:hover {
            background-color: #18181B !important;
            color: #FFFFFF !important;
            border-color: #18181B !important;
        }
        .dark .custom-pagination nav a:hover {
            background-color: #27272A !important;
            color: #FFFFFF !important;
            border-color: #3F3F46 !important;
        }
        .custom-pagination nav span[aria-current="page"] span {
            background: #18181B !important;
            color: #FFFFFF !important;
            font-weight: 700 !important;
            border-color: #18181B !important;
        }
        .dark .custom-pagination nav span[aria-current="page"] span {
            background: #F4F4F5 !important;
            color: #09090B !important;
            font-weight: 700 !important;
            border-color: #F4F4F5 !important;
        }

        /* === EFECTOS MODERNOS SECCIÓN INICIO (HERO INTERACTIVO) === */
        @keyframes heroFloatSlow {
            0%, 100% { transform: translate3d(0, 0, 0) scale(1); }
            33% { transform: translate3d(28px, -22px, 0) scale(1.06); }
            66% { transform: translate3d(-20px, 18px, 0) scale(0.96); }
        }
        @keyframes heroRevealUp {
            0% { opacity: 0; transform: translate3d(0, 22px, 0) scale(0.985); filter: blur(4px); }
            100% { opacity: 1; transform: translate3d(0, 0, 0) scale(1); filter: blur(0); }
        }
        @keyframes heroShimmerSweep {
            0% { transform: translateX(-120%) skewX(-18deg); }
            100% { transform: translateX(220%) skewX(-18deg); }
        }
        @keyframes heroPulseLine {
            0% { left: -15%; opacity: 0; }
            20% { opacity: 1; }
            80% { opacity: 1; }
            100% { left: 100%; opacity: 0; }
        }
        @keyframes heroTextGradient {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .hero-orb-1 { animation: heroFloatSlow 11s ease-in-out infinite; }
        .hero-orb-2 { animation: heroFloatSlow 14s ease-in-out infinite reverse; }
        .hero-reveal-1 { animation: heroRevealUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.05s both; }
        .hero-reveal-2 { animation: heroRevealUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.15s both; }
        .hero-reveal-3 { animation: heroRevealUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.25s both; }
        .hero-reveal-4 { animation: heroRevealUp 0.85s cubic-bezier(0.16, 1, 0.3, 1) 0.35s both; }
        .hero-reveal-5 { animation: heroRevealUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.45s both; }

        .hero-grid-mask {
            background-image:
                linear-gradient(to right, rgba(200, 147, 51, 0.08) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(200, 147, 51, 0.08) 1px, transparent 1px);
            background-size: 44px 44px;
            -webkit-mask-image: radial-gradient(ellipse 75% 65% at 50% 45%, #000 35%, transparent 85%);
            mask-image: radial-gradient(ellipse 75% 65% at 50% 45%, #000 35%, transparent 85%);
        }
        .dark .hero-grid-mask {
            background-image:
                linear-gradient(to right, rgba(234, 183, 89, 0.09) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(234, 183, 59, 0.09) 1px, transparent 1px);
        }

        .hero-btn-shimmer {
            position: relative;
            overflow: hidden;
        }
        .hero-btn-shimmer::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 45%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.28), transparent);
            transform: translateX(-120%) skewX(-18deg);
            animation: heroShimmerSweep 4.2s ease-in-out infinite;
            pointer-events: none;
        }

        .hero-step-beam {
            position: absolute;
            top: 0;
            width: 18%;
            height: 100%;
            background: linear-gradient(90deg, transparent, #EAB759, #10B981, transparent);
            animation: heroPulseLine 3.6s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }

        /* === EFECTOS GLOBALES DEL RESTO DEL INICIO (SCROLL REVEAL, MARQUEE & CARD SHINE) === */
        @keyframes marqueeScroll {
            0% { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(-50%, 0, 0); }
        }
        .luxury-marquee-track {
            display: flex;
            width: max-content;
            animation: marqueeScroll 34s linear infinite;
        }
        .luxury-marquee-track:hover {
            animation-play-state: paused;
        }

        /* Efecto de reflejo diagonal (Cristal) al pasar el mouse sobre fotos de productos/montajes */
        .card-gloss-shine {
            position: relative;
            overflow: hidden;
        }
        .card-gloss-shine::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -60%;
            width: 40%;
            height: 200%;
            background: linear-gradient(
                to right,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.32) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            transform: rotate(25deg);
            transition: transform 0.75s cubic-bezier(0.19, 1, 0.22, 1);
            pointer-events: none;
        }
        .group:hover .card-gloss-shine::after {
            transform: translateX(380%) rotate(25deg);
        }

        /* Scroll Reveal con IntersectionObserver */
        [data-reveal] {
            opacity: 0;
            transform: translate3d(0, 28px, 0) scale(0.985);
            transition:
                opacity 0.75s cubic-bezier(0.16, 1, 0.3, 1),
                transform 0.75s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }
        [data-reveal].is-revealed {
            opacity: 1;
            transform: translate3d(0, 0, 0) scale(1);
        }

        /* === OPCIÓN C: VUELO AL CARRITO (FLY-TO-CART) & SHOCKWAVE === */
        @keyframes cartShockwave {
            0% { box-shadow: 0 0 0 0 rgba(234, 183, 89, 0.75); transform: scale(1); }
            50% { box-shadow: 0 0 0 14px rgba(234, 183, 89, 0); transform: scale(1.09); }
            100% { box-shadow: 0 0 0 0 rgba(234, 183, 89, 0); transform: scale(1); }
        }
        .cart-hit-shockwave {
            animation: cartShockwave 0.55s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* === FOOTER EDITORIAL: SUBRAYADO MAGNÉTICO & MARCA DE AGUA === */
        .footer-link-magnetic {
            position: relative;
            display: inline-block;
        }
        .footer-link-magnetic::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 100%;
            height: 1.5px;
            background: linear-gradient(90deg, #EAB759, #10B981);
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.35s cubic-bezier(0.19, 1, 0.22, 1);
        }
        .footer-link-magnetic:hover::after {
            transform: scaleX(1);
            transform-origin: left;
        }

        @media (prefers-reduced-motion: reduce) {
            .hero-orb-1, .hero-orb-2, .hero-reveal-1, .hero-reveal-2, .hero-reveal-3, .hero-reveal-4, .hero-reveal-5, .hero-btn-shimmer::after, .hero-step-beam, .luxury-marquee-track {
                animation: none !important;
            }
            [data-reveal] {
                opacity: 1 !important;
                transform: none !important;
            }
        }
    </style>
</head>
<body class="bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans min-h-screen flex flex-col antialiased selection:bg-amber-500 selection:text-white relative overflow-x-clip transition-colors duration-200" 
      x-data="quoteCart()" 
      x-cloak>

    @php
        $hasHeroBanner = !request('categoria') && !request('buscar');
    @endphp

    <!-- Franja Superior Boutique (solo desde tablet/desktop para no saturar pantalla móvil) -->
    <div class="hidden sm:block bg-zinc-950 text-zinc-200 border-b border-amber-500/20 text-[11px] font-medium relative z-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-1.5 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0 truncate">
                <span class="inline-flex items-center gap-1.5 text-amber-300 font-semibold shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>Santiago &amp; Regiones</span>
                </span>
                <span class="text-zinc-600">&bull;</span>
                <span class="truncate text-zinc-300">Lavado de vajilla y mantelería 100% incluido</span>
                <span class="text-zinc-600 hidden md:inline">&bull;</span>
                <span class="hidden md:inline text-zinc-400">Despacho y retiro coordinado</span>
            </div>
            <a href="https://wa.me/{{ $settings->cleanWhatsappPhone() }}?text={{ rawurlencode('Hola, me comunico desde el catálogo de ' . ($settings->site_name ?? 'VYS Productora de Eventos') . '.') }}"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center gap-1.5 text-emerald-400 hover:text-emerald-300 font-semibold shrink-0 transition">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                <span>WhatsApp Directo</span>
            </a>
        </div>
    </div>

    <!-- Barra de Navegación Adaptativa (Cristal sobre Hero cinematográfico → Sólida elegante al hacer scroll) -->
    <header x-data="{ scrolled: false, hasHero: {{ $hasHeroBanner ? 'true' : 'false' }} }"
            x-init="scrolled = window.scrollY > 36"
            @scroll.window="scrolled = window.scrollY > 36"
            :class="(hasHero && !scrolled && !mobileMenuOpen)
                ? 'navbar-over-hero bg-zinc-950/40 backdrop-blur-md border-white/10 text-white'
                : 'bg-white/95 dark:bg-zinc-950/95 backdrop-blur-xl border-zinc-200/80 dark:border-zinc-800 shadow-sm text-zinc-900 dark:text-white'"
            class="sticky top-0 z-40 border-b transition-all duration-300 {{ $hasHeroBanner ? '-mb-[58px] sm:-mb-[76px]' : '' }}">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 h-14 sm:h-[74px] flex items-center justify-between gap-2 sm:gap-5 min-w-0">

            <!-- Brand Logo Oficial VYS (shrink-0 en desktop para jamás chocar con la navegación) -->
            <a href="{{ route('catalog.index') }}" class="group flex items-center shrink-0 focus:outline-none">
                <x-brand-logo variant="header" :settings="$settings" />
            </a>

            <!-- Enlaces de Navegación Desktop (Cápsula elegante de 6 enlaces equilibrados, cero saturación) -->
            <nav :class="(hasHero && !scrolled && !mobileMenuOpen)
                    ? 'bg-white/10 border-white/15 text-zinc-100'
                    : 'bg-zinc-100/80 dark:bg-zinc-900/90 border-zinc-200/80 dark:border-zinc-800 text-zinc-600 dark:text-zinc-300'"
                 class="hidden lg:flex items-center gap-1 xl:gap-1.5 px-2 py-1.5 rounded-full border backdrop-blur-md text-[11px] xl:text-xs uppercase tracking-wider font-semibold whitespace-nowrap transition-all duration-300">
                <a href="{{ route('catalog.index') }}"
                   :class="(hasHero && !scrolled)
                       ? '{{ !request('categoria') && !request('buscar') ? 'bg-white/18 text-white shadow-xs' : 'hover:bg-white/10 hover:text-white' }}'
                       : '{{ !request('categoria') && !request('buscar') ? 'bg-white dark:bg-zinc-800 text-zinc-950 dark:text-white shadow-xs' : 'hover:text-zinc-950 dark:hover:text-white hover:bg-white/60 dark:hover:bg-zinc-800/60' }}'"
                   class="px-3 xl:px-3.5 py-1.5 rounded-full transition-all duration-200">
                    Inicio
                </a>
                <x-catalog-nav-dropdown :categories="$categories" :interactive="true" :adaptive="true" />
                <a href="{{ route('catalog.montajes') }}"
                   :class="(hasHero && !scrolled) ? 'hover:bg-white/10 hover:text-white' : 'hover:text-zinc-950 dark:hover:text-white hover:bg-white/60 dark:hover:bg-zinc-800/60'"
                   class="px-3 xl:px-3.5 py-1.5 rounded-full transition-all duration-200 flex items-center gap-1.5">
                    <span>Montajes</span>
                    <span class="text-[9px] font-mono uppercase bg-amber-400/20 text-amber-500 dark:text-amber-300 px-1.5 py-0.5 rounded-full border border-amber-400/40 leading-none">Nuevo</span>
                </a>
                <a href="#como-cotizar"
                   :class="(hasHero && !scrolled) ? 'hover:bg-white/10 hover:text-white' : 'hover:text-zinc-950 dark:hover:text-white hover:bg-white/60 dark:hover:bg-zinc-800/60'"
                   class="px-3 xl:px-3.5 py-1.5 rounded-full transition-all duration-200">
                    Cómo Cotizar
                </a>
                <a href="#envios"
                   :class="(hasHero && !scrolled) ? 'hover:bg-white/10 hover:text-white' : 'hover:text-zinc-950 dark:hover:text-white hover:bg-white/60 dark:hover:bg-zinc-800/60'"
                   class="px-3 xl:px-3.5 py-1.5 rounded-full transition-all duration-200">
                    Envíos
                </a>
                <a href="#contacto"
                   :class="(hasHero && !scrolled) ? 'hover:bg-white/10 hover:text-white' : 'hover:text-zinc-950 dark:hover:text-white hover:bg-white/60 dark:hover:bg-zinc-800/60'"
                   class="px-3 xl:px-3.5 py-1.5 rounded-full transition-all duration-200">
                    Contacto
                </a>
            </nav>

            <!-- Acciones: Botón de Cotización Dorado, Toggle Sol/Luna, Admin y Menú Móvil -->
            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                <div x-data="{ bump: false }" 
                     x-init="$watch('countItems()', () => { bump = true; setTimeout(() => bump = false, 350) })">
                    <button @click="cartOpen = true" 
                            id="open-quote-drawer-btn"
                            :class="(hasHero && !scrolled && !mobileMenuOpen)
                                ? 'bg-gradient-to-r from-amber-400 via-amber-500 to-amber-400 hover:from-amber-300 hover:to-amber-400 text-zinc-950 shadow-lg shadow-amber-500/25'
                                : 'bg-zinc-900 hover:bg-zinc-800 dark:bg-amber-500 dark:hover:bg-amber-400 text-white dark:text-zinc-950 shadow-md'"
                            class="relative inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 sm:py-2.5 rounded-full hover:shadow-lg transition-all duration-200 active:scale-95 group">
                        <svg :class="(hasHero && !scrolled && !mobileMenuOpen) ? 'text-zinc-950' : 'text-amber-300 dark:text-zinc-950'"
                             class="w-4 h-4 group-hover:rotate-6 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span class="hidden sm:inline text-xs font-bold tracking-wide">Cotización</span>
                        <span class="transition-transform duration-300 font-mono font-bold text-xs px-1.5 sm:px-2 py-0.5 rounded-full" 
                              :class="bump
                                  ? 'scale-125 bg-zinc-950 text-amber-300 shadow-md'
                                  : ((hasHero && !scrolled && !mobileMenuOpen) ? 'scale-100 bg-zinc-950/15 text-zinc-950' : 'scale-100 bg-white/20 dark:bg-black/20 text-white dark:text-zinc-950')" 
                              x-text="countItems()"></span>
                    </button>
                </div>

                <!-- Botón Toggle Sol / Luna -->
                <button @click="toggleTheme()" 
                        type="button" 
                        id="theme-toggle-btn"
                        title="Alternar Modo Claro / Modo Oscuro"
                        :class="(hasHero && !scrolled && !mobileMenuOpen)
                            ? 'bg-white/12 hover:bg-white/20 text-amber-300 border-white/20'
                            : 'bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-amber-400 border-zinc-200/80 dark:border-zinc-700'"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-full transition-all duration-200 flex items-center justify-center border shadow-sm hover:scale-105 active:scale-95"
                        aria-label="Cambiar tema claro/oscuro">
                    <svg x-show="isDark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg x-show="!isDark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                <!-- Botón Login Panel Admin (en móvil disponible dentro del menú desplegable) -->
                <a href="{{ route('filament.admin.auth.login') }}" 
                   title="Acceso Administración / Iniciar Sesión"
                   :class="(hasHero && !scrolled && !mobileMenuOpen)
                       ? 'bg-white/12 hover:bg-white/20 text-white border-white/20'
                       : 'bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-900 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 hover:text-white border-zinc-200/80 dark:border-zinc-700'"
                   class="hidden sm:flex w-9 h-9 sm:w-10 sm:h-10 rounded-full transition-all duration-200 items-center justify-center border shadow-sm hover:scale-105 active:scale-95 group">
                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </a>

                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        type="button"
                        aria-label="Abrir menú de navegación"
                        :aria-expanded="mobileMenuOpen"
                        :class="(hasHero && !scrolled && !mobileMenuOpen)
                            ? 'bg-white/12 hover:bg-white/20 text-white border border-white/20'
                            : 'bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200'"
                        class="lg:hidden p-2 rounded-xl transition">
                    <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menú Móvil / Tablet Desplegable -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             @click.outside="mobileMenuOpen = false"
             class="lg:hidden bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl border-b border-zinc-200 dark:border-zinc-800 px-5 py-4 space-y-3"
             style="display: none;">
            <nav class="flex flex-col space-y-2 text-sm font-medium text-zinc-800 dark:text-zinc-200">
                <a @click="mobileMenuOpen = false" href="{{ route('catalog.index') }}" class="px-3 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex items-center justify-between">
                    <span>Inicio</span>
                    <span class="text-xs text-zinc-400 font-mono">01</span>
                </a>
                <a @click="mobileMenuOpen = false" href="{{ (request('categoria') || request('buscar')) ? route('catalog.index') . '#destacados' : '#destacados' }}" class="px-3 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex items-center justify-between">
                    <span>Colección en Carrusel</span>
                    <span class="text-xs text-zinc-400 font-mono">02</span>
                </a>
                <x-catalog-mobile-categories :categories="$categories" :interactive="true" step-number="03" />
                <a @click="mobileMenuOpen = false" href="{{ route('catalog.montajes') }}" class="px-3 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex items-center justify-between font-bold text-amber-700 dark:text-amber-400">
                    <span class="flex items-center gap-2">
                        <span>Montajes & Puesta en Escena</span>
                        <span class="text-[9px] font-mono uppercase bg-amber-500/10 text-amber-700 dark:text-amber-400 px-1.5 py-0.5 rounded">Nuevo</span>
                    </span>
                    <span class="text-xs text-zinc-400 font-mono">04</span>
                </a>
                <a @click="mobileMenuOpen = false" href="#como-cotizar" class="px-3 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex items-center justify-between">
                    <span>Cómo Cotizar Mobiliario</span>
                    <span class="text-xs text-zinc-400 font-mono">05</span>
                </a>
                <a @click="mobileMenuOpen = false" href="#envios" class="px-3 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex items-center justify-between">
                    <span>Envíos & Logística</span>
                    <span class="text-xs text-zinc-400 font-mono">06</span>
                </a>
                <a @click="mobileMenuOpen = false" href="#contacto" class="px-3 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex items-center justify-between">
                    <span>Contacto & Ubicación</span>
                    <span class="text-xs text-zinc-400 font-mono">07</span>
                </a>
                <a href="https://wa.me/{{ $settings->cleanWhatsappPhone() }}?text={{ rawurlencode('Hola, me comunico desde el catálogo de ' . ($settings->site_name ?? 'Arriendos & Eventos') . '.') }}" 
                   target="_blank" 
                   class="px-3 py-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 font-semibold transition flex items-center justify-between">
                    <span>Atención por WhatsApp</span>
                    <svg class="w-4 h-4 fill-current text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.529 1.831.812 2.791.812 3.179 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.768-5.768-5.768zm9.969 5.768c0 5.518-4.482 10-10 10-1.748 0-3.385-.45-4.819-1.242l-5.181 1.356 1.381-5.048c-.902-1.492-1.381-3.21-1.381-5.066 0-5.518 4.482-10 10-10s10 4.482 10 10z"/></svg>
                </a>
                <a href="{{ route('filament.admin.auth.login') }}" 
                   class="px-3 py-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-medium transition flex items-center justify-between border border-zinc-200 dark:border-zinc-700">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Acceso Administración</span>
                    </span>
                    <span class="text-xs text-zinc-500 font-mono">Panel &rarr;</span>
                </a>
            </nav>
        </div>

        <!-- Barra Dinámica de Progreso de Lectura (Scroll Progress) -->
        <div class="h-[2px] w-full bg-transparent overflow-hidden pointer-events-none">
            <div id="scroll-progress-bar" class="h-full bg-gradient-to-r from-amber-500 via-amber-400 to-emerald-500 transition-[width] duration-75 ease-out" style="width: 0%;"></div>
        </div>
    </header>

    @if($hasHeroBanner)
        @php
            $heroSlides = [];
            if (!empty($settings?->hero_bg_image)) {
                $heroSlides[] = [
                    'url' => asset('storage/' . $settings->hero_bg_image),
                    'caption' => 'Montaje Destacado • Colección Oficial',
                ];
            }
            $heroSlides[] = ['url' => asset('images/hero/hero-1.webp'), 'caption' => 'Pabellón de Cristal • Banquete de Gala'];
            $heroSlides[] = ['url' => asset('images/hero/hero-2.webp'), 'caption' => 'Salón Principal • Iluminación & Pista'];
            $heroSlides[] = ['url' => asset('images/hero/hero-3.webp'), 'caption' => 'Ceremonia Exterior • Arco de Novios'];
            $heroSlides[] = ['url' => asset('images/hero/hero-4.webp'), 'caption' => 'Recepción de Etiqueta • Vajilla & Flores'];
            $heroSlides = array_slice($heroSlides, 0, 4);
        @endphp

        <!-- Hero Banner Cinematográfico Editorial (Carrusel Real de Montajes + Scrim Cálido + Glassmorphism) -->
        <section id="inicio"
                 x-data="{
                     mouseX: 50,
                     mouseY: 40,
                     spotlightActive: false,
                     activeSlide: 0,
                     slides: {{ Js::from($heroSlides) }},
                     activeTagIndex: 0,
                     eventTags: [
                         'Matrimonios & Bodas de Gala',
                         'Eventos Corporativos & Lanzamientos',
                         'Banquetes & Cenas de Fin de Año',
                         'Cócteles & Celebraciones Privadas'
                     ],
                     itemsCounter: 0,
                     sanitizedCounter: 0,
                     slideTimer: null,
                     initHero() {
                         this.slideTimer = setInterval(() => {
                             this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                         }, 6000);

                         setInterval(() => {
                             this.activeTagIndex = (this.activeTagIndex + 1) % this.eventTags.length;
                         }, 3200);

                         const duration = 1400;
                         const start = performance.now();
                         const step = (now) => {
                             const progress = Math.min((now - start) / duration, 1);
                             const eased = 1 - Math.pow(1 - progress, 3);
                             this.itemsCounter = Math.round(eased * 190);
                             this.sanitizedCounter = Math.round(eased * 100);
                             if (progress < 1) requestAnimationFrame(step);
                         };
                         requestAnimationFrame(step);
                     },
                     goToSlide(idx) {
                         this.activeSlide = idx;
                         if (this.slideTimer) clearInterval(this.slideTimer);
                         this.slideTimer = setInterval(() => {
                             this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                         }, 6000);
                     },
                     handleMouseMove(e) {
                         const rect = e.currentTarget.getBoundingClientRect();
                         this.mouseX = ((e.clientX - rect.left) / rect.width) * 100;
                         this.mouseY = ((e.clientY - rect.top) / rect.height) * 100;
                         this.spotlightActive = true;
                     }
                 }"
                 x-init="initHero()"
                 @mousemove="handleMouseMove($event)"
                 @mouseleave="spotlightActive = false"
                 class="relative bg-zinc-950 border-b border-zinc-800 pt-20 pb-12 sm:pt-32 sm:pb-28 lg:pt-36 lg:pb-32 overflow-hidden select-none min-h-[450px] sm:min-h-[620px] lg:min-h-[670px] flex items-center">

            <!-- 0. Carrusel Fotográfico Real de Montajes con Crossfade & Zoom Suave (Ken Burns) -->
            <div class="absolute inset-0 z-0 overflow-hidden bg-zinc-950">
                <template x-for="(slide, idx) in slides" :key="idx">
                    <div x-show="activeSlide === idx"
                         x-transition:enter="transition ease-out duration-1000"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-1000"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute inset-0">
                        <img :src="slide.url"
                             :alt="slide.caption"
                             :fetchpriority="idx === 0 ? 'high' : 'low'"
                             class="w-full h-full object-cover object-center hero-ken-burns">
                    </div>
                </template>
                <!-- Fallback estático instantáneo para primer pintado (LCP) antes de Alpine -->
                <img src="{{ $heroSlides[0]['url'] }}"
                     alt="{{ $heroSlides[0]['caption'] }}"
                     fetchpriority="high"
                     class="w-full h-full object-cover object-center opacity-100"
                     :class="'opacity-0 pointer-events-none'">
            </div>

            <!-- 1. Velo Cinematográfico Cálido (contraste AAA en texto blanco/dorado + profundidad) -->
            <div class="pointer-events-none absolute inset-0 z-[1] bg-gradient-to-b from-zinc-950/75 via-zinc-950/55 to-zinc-950/88"></div>
            <div class="pointer-events-none absolute inset-0 z-[1] bg-[radial-gradient(ellipse_at_center,rgba(234,183,89,0.14)_0%,transparent_65%)]"></div>

            <!-- 2. Luz Spotlight Interactiva Dorada que sigue el cursor -->
            <div class="pointer-events-none absolute inset-0 z-[2] transition-opacity duration-500"
                 :class="spotlightActive ? 'opacity-100' : 'opacity-40'"
                 :style="`background: radial-gradient(560px circle at ${mouseX}% ${mouseY}%, rgba(234, 183, 89, 0.18), rgba(16, 185, 129, 0.05) 45%, transparent 75%);`">
            </div>

            <!-- Contenido Editorial Principal -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 w-full">
                <!-- Badges Superiores: en móvil 1 sola píldora limpia; en tablet/desktop ambas -->
                <div class="hero-reveal-1 flex flex-col sm:flex-row items-center justify-center gap-2 sm:gap-2.5 mb-3.5 sm:mb-7">
                    <div class="hidden sm:inline-flex items-center gap-2 bg-zinc-950/55 backdrop-blur-md border border-white/20 px-4 py-1.5 rounded-full shadow-lg">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-400"></span>
                        </span>
                        <span class="text-xs font-mono font-semibold tracking-widest uppercase text-zinc-100">
                            {{ $settings->hero_badge ?? 'Catálogo Oficial • Temporada 2026' }}
                        </span>
                    </div>

                    <!-- Píldora Dinámica Cambiante en Champaña Dorado -->
                    <div class="inline-flex items-center gap-1.5 sm:gap-2 bg-zinc-950/55 sm:bg-amber-400/15 backdrop-blur-md border border-amber-300/35 px-3 sm:px-3.5 py-1 sm:py-1.5 rounded-full text-[11px] sm:text-xs font-medium text-amber-200 shadow-lg overflow-hidden max-w-full h-7 sm:h-8">
                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-amber-300 shrink-0 animate-spin" style="animation-duration: 9s;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        <div class="relative flex items-center justify-center min-w-0 w-[195px] max-w-[70vw] sm:w-[240px] sm:max-w-none">
                            <template x-for="(tag, idx) in eventTags" :key="idx">
                                <span x-show="activeTagIndex === idx"
                                      x-transition:enter="transition ease-out duration-500"
                                      x-transition:enter-start="opacity-0 translate-y-3"
                                      x-transition:enter-end="opacity-100 translate-y-0"
                                      x-transition:leave="transition ease-in duration-300 absolute"
                                      x-transition:leave-start="opacity-100 translate-y-0"
                                      x-transition:leave-end="opacity-0 -translate-y-3"
                                      class="font-semibold tracking-wide truncate max-w-full"
                                      x-text="tag"></span>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Título Principal Editorial Cinematográfico (respirable en móvil, monumental en desktop) -->
                <h1 class="hero-reveal-2 font-serif text-[1.75rem] leading-[1.13] sm:text-5xl md:text-6xl lg:text-[4.25rem] sm:leading-[1.09] font-light tracking-tight text-white max-w-md sm:max-w-4xl mx-auto drop-shadow-[0_4px_24px_rgba(0,0,0,0.45)] break-words">
                    @if(!empty($settings?->hero_title) && $settings->hero_title !== 'Equipamiento y ambientación para ocasiones extraordinarias')
                        {{ $settings->hero_title }}
                    @else
                        Equipamiento y ambientación para <span class="italic font-normal bg-gradient-to-r from-amber-200 via-amber-300 to-amber-200 bg-clip-text text-transparent">ocasiones extraordinarias</span>
                    @endif
                </h1>

                <!-- Subtítulo Editorial -->
                <p class="hero-reveal-3 text-[13px] sm:text-sm md:text-base text-zinc-200/95 mt-3 sm:mt-6 max-w-sm sm:max-w-2xl mx-auto font-light leading-relaxed drop-shadow-sm line-clamp-3 sm:line-clamp-none">
                    {{ $settings->hero_subtitle ?? 'Mobiliario de etiqueta, vajilla fina, cristalería y mantelería para bodas, galas y celebraciones corporativas. Celebra con distinción y logística puntual garantizada.' }}
                </p>

                <!-- Botones CTA en una fila limpia desde móvil (cero saturación vertical) -->
                <div class="hero-reveal-4 mt-5 sm:mt-9 flex flex-wrap items-center justify-center gap-2 sm:gap-3.5">
                    <a href="#catalogo" class="hero-btn-shimmer group inline-flex items-center justify-center gap-2 bg-gradient-to-r from-amber-400 via-amber-500 to-amber-400 hover:from-amber-300 hover:to-amber-400 text-zinc-950 font-bold text-xs sm:text-sm px-5 sm:px-7 py-2.5 sm:py-3.5 rounded-full shadow-xl shadow-amber-500/25 hover:shadow-2xl hover:shadow-amber-400/35 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300">
                        <span>Explorar Catálogo</span>
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-zinc-950 group-hover:translate-y-0.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                    <a href="{{ route('catalog.montajes') }}" class="group inline-flex items-center justify-center gap-2 bg-white/12 hover:bg-white/22 backdrop-blur-md text-white border border-white/25 hover:border-amber-300/60 font-medium text-xs sm:text-sm px-4 sm:px-6 py-2.5 sm:py-3.5 rounded-full shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                        <span class="sm:hidden">Ver Montajes</span>
                        <span class="hidden sm:inline">Montajes Realizados</span>
                        <span class="hidden sm:inline-block text-[10px] font-mono bg-amber-400/20 text-amber-200 px-2 py-0.5 rounded-full font-bold border border-amber-300/30 group-hover:bg-amber-400 group-hover:text-zinc-950 transition-colors duration-300">2023-2026</span>
                    </a>
                </div>

                <!-- Mini-Stats en Cristal Oscuro Frosted (compactas en móvil para dejar respirar la fotografía) -->
                <div class="hero-reveal-5 mt-6 sm:mt-11 grid grid-cols-3 gap-1.5 sm:gap-4 max-w-md sm:max-w-3xl mx-auto">
                    <a href="#catalogo" class="group relative min-w-0 bg-zinc-950/45 hover:bg-zinc-950/65 backdrop-blur-md border border-white/15 hover:border-amber-400/50 py-2 px-1.5 sm:p-4 rounded-xl sm:rounded-2xl shadow-lg hover:-translate-y-1 transition-all duration-300 text-center overflow-hidden">
                        <div class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-transparent via-amber-400/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <span class="block font-serif text-base sm:text-3xl font-bold text-white group-hover:scale-105 transition-transform duration-300 leading-tight"
                              x-text="'+' + (itemsCounter || 190)">+190</span>
                        <span class="sm:hidden text-[9.5px] text-zinc-300 font-medium mt-0.5 block leading-tight">Ítems</span>
                        <span class="hidden sm:block text-xs text-zinc-300 font-medium mt-0.5 leading-tight">Ítems de colección</span>
                    </a>

                    <a href="#como-cotizar" class="group relative min-w-0 bg-zinc-950/45 hover:bg-zinc-950/65 backdrop-blur-md border border-white/15 hover:border-emerald-400/50 py-2 px-1.5 sm:p-4 rounded-xl sm:rounded-2xl shadow-lg hover:-translate-y-1 transition-all duration-300 text-center overflow-hidden">
                        <div class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-transparent via-emerald-400/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <span class="block font-serif text-base sm:text-3xl font-bold text-emerald-400 group-hover:scale-105 transition-transform duration-300 leading-tight"
                              x-text="(sanitizedCounter || 100) + '%'">100%</span>
                        <span class="sm:hidden text-[9.5px] text-zinc-300 font-medium mt-0.5 block leading-tight">Lavado incl.</span>
                        <span class="hidden sm:block text-xs text-zinc-300 font-medium mt-0.5 leading-tight">Lavado incluido</span>
                    </a>

                    <a href="#envios" class="group relative min-w-0 bg-zinc-950/45 hover:bg-zinc-950/65 backdrop-blur-md border border-white/15 hover:border-amber-400/50 py-2 px-1.5 sm:p-4 rounded-xl sm:rounded-2xl shadow-lg hover:-translate-y-1 transition-all duration-300 text-center overflow-hidden">
                        <div class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r from-transparent via-amber-400/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <span class="block font-serif text-base sm:text-3xl font-bold text-amber-300 group-hover:scale-105 transition-transform duration-300 leading-tight">Puntual</span>
                        <span class="sm:hidden text-[9.5px] text-zinc-300 font-medium mt-0.5 block leading-tight">Despacho</span>
                        <span class="hidden sm:block text-xs text-zinc-300 font-medium mt-0.5 leading-tight">Entrega coordinada</span>
                    </a>
                </div>

                <!-- Control Discreto de Escenas Reales (en móvil solo puntos sutiles) -->
                <div class="mt-4 sm:mt-8 flex items-center justify-center gap-3 sm:gap-4 text-[11px] font-mono text-zinc-300/90">
                    <span class="hidden sm:inline-flex items-center gap-1.5 bg-zinc-950/45 backdrop-blur-md border border-white/10 px-3 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                        <span x-text="slides[activeSlide]?.caption"></span>
                    </span>
                    <div class="flex items-center gap-1.5">
                        <template x-for="(slide, idx) in slides" :key="'dot-' + idx">
                            <button type="button"
                                    @click="goToSlide(idx)"
                                    :aria-label="'Ver escena ' + (idx + 1)"
                                    :class="activeSlide === idx ? 'w-7 bg-amber-400' : 'w-2 bg-white/35 hover:bg-white/60'"
                                    class="h-1.5 rounded-full transition-all duration-300"></button>
                        </template>
                    </div>
                </div>
            </div>
        </section>

        <!-- Barra de 3 Pasos de Reserva Visual con Pulso Luminoso Conector -->
        <section class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 -mt-5 sm:-mt-10 relative z-20">
            <div class="relative bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl rounded-2xl border border-zinc-200/90 dark:border-zinc-800 p-3 sm:p-6 shadow-xl shadow-zinc-900/5 overflow-hidden">
                <!-- Haz de luz animado recorriendo el borde superior de los 3 pasos -->
                <div class="absolute inset-x-0 top-0 h-[2px] bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                    <div class="hero-step-beam"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5 sm:gap-6 relative">
                    <!-- Paso 1 -->
                    <a href="#catalogo" class="group flex items-start gap-3.5 p-3.5 sm:p-4 rounded-xl bg-zinc-50/70 dark:bg-zinc-800/50 hover:bg-white dark:hover:bg-zinc-800 border border-zinc-200/60 dark:border-zinc-700/60 hover:border-amber-500/40 dark:hover:border-amber-400/40 hover:shadow-md transition-all duration-300">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-zinc-900 dark:bg-zinc-700 group-hover:bg-amber-500 text-amber-300 group-hover:text-zinc-950 font-serif text-base font-bold flex items-center justify-center shrink-0 shadow-sm group-hover:scale-110 transition-all duration-300">
                            1
                        </div>
                        <div>
                            <h4 class="font-semibold text-zinc-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors text-xs sm:text-sm">Selecciona tu mobiliario</h4>
                            <p class="text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-400 mt-0.5 leading-relaxed">
                                Agrega piezas y cantidades requeridas con stock en tiempo real.
                            </p>
                        </div>
                    </a>

                    <!-- Paso 2 -->
                    <button type="button" @click="cartOpen = true" class="group text-left flex items-start gap-3.5 p-3.5 sm:p-4 rounded-xl bg-zinc-50/70 dark:bg-zinc-800/50 hover:bg-white dark:hover:bg-zinc-800 border border-zinc-200/60 dark:border-zinc-700/60 hover:border-amber-500/40 dark:hover:border-amber-400/40 hover:shadow-md transition-all duration-300">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-zinc-900 dark:bg-zinc-700 group-hover:bg-amber-500 text-amber-300 group-hover:text-zinc-950 font-serif text-base font-bold flex items-center justify-center shrink-0 shadow-sm group-hover:scale-110 transition-all duration-300">
                            2
                        </div>
                        <div>
                            <h4 class="font-semibold text-zinc-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors text-xs sm:text-sm">Revisa tu cotización</h4>
                            <p class="text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-400 mt-0.5 leading-relaxed">
                                Ingresa tu nombre, fecha y comuna en el drawer lateral consolidado.
                            </p>
                        </div>
                    </button>

                    <!-- Paso 3 -->
                    <a href="#como-cotizar" class="group flex items-start gap-3.5 p-3.5 sm:p-4 rounded-xl bg-zinc-50/70 dark:bg-zinc-800/50 hover:bg-white dark:hover:bg-zinc-800 border border-zinc-200/60 dark:border-zinc-700/60 hover:border-emerald-500/40 dark:hover:border-emerald-400/40 hover:shadow-md transition-all duration-300">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-emerald-700 group-hover:bg-emerald-500 text-white font-serif text-base font-bold flex items-center justify-center shrink-0 shadow-sm group-hover:scale-110 transition-all duration-300">
                            3
                        </div>
                        <div>
                            <h4 class="font-semibold text-zinc-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors text-xs sm:text-sm">Confirmación por WhatsApp</h4>
                            <p class="text-[11px] sm:text-xs text-zinc-600 dark:text-zinc-400 mt-0.5 leading-relaxed">
                                Confirmamos disponibilidad de fecha, flete y reserva inmediata.
                            </p>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <!-- SECCIÓN: CARRUSEL INTERACTIVO DE INICIO (Compacto & Elegante) -->
        <section id="destacados" 
                 class="py-12 sm:py-16 border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/30 overflow-hidden"
                 x-data="{
                     currentIndex: 0,
                     width: window.innerWidth,
                     touchStartX: 0,
                     total: {{ $carouselProducts->count() }},
                     get perPage() {
                         if (this.width >= 1024) return 4;
                         if (this.width >= 640) return 2;
                         return 1;
                     },
                     get maxIndex() {
                         return Math.max(0, this.total - this.perPage);
                     },
                     next() {
                         this.currentIndex = (this.currentIndex >= this.maxIndex) ? 0 : this.currentIndex + 1;
                     },
                     prev() {
                         this.currentIndex = (this.currentIndex <= 0) ? this.maxIndex : this.currentIndex - 1;
                     },
                     autoplayTimer: null,
                     startAutoplay() {
                         this.stopAutoplay();
                         this.autoplayTimer = setInterval(() => { this.next(); }, 4500);
                     },
                     stopAutoplay() {
                         if (this.autoplayTimer) clearInterval(this.autoplayTimer);
                     }
                 }"
                 x-init="startAutoplay()"
                 @resize.window.debounce.100ms="width = window.innerWidth; if (currentIndex > maxIndex) currentIndex = maxIndex"
                 @mouseenter="stopAutoplay()"
                 @mouseleave="startAutoplay()">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header con Título Editorial y Botones Deslizadores -->
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                    <div>
                        <div class="inline-flex flex-wrap items-center gap-2 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 px-3 py-1 rounded-full mb-2 max-w-full">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse shrink-0"></span>
                            <span class="text-[10px] font-mono uppercase tracking-[0.12em] sm:tracking-[0.22em] text-amber-800 dark:text-amber-400 font-semibold">
                                Vitrina en Carrusel • Colección en Rotación
                            </span>
                        </div>
                        <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl font-light text-zinc-900 dark:text-white tracking-wide">
                            Disfruta de Nuestro <span class="italic font-normal text-amber-700 dark:text-amber-500">Mobiliario & Destacados</span>
                        </h2>
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 mt-1 font-light max-w-xl">
                            Desliza para explorar nuestras piezas de mayor distinción para bodas, aniversarios y recepciones corporativas.
                        </p>
                    </div>

                    <!-- Controles Flechas Prev / Next con Contador -->
                    <div class="flex items-center gap-3 self-end sm:self-auto">
                        <div class="text-xs font-mono text-zinc-500 dark:text-zinc-400 hidden sm:block">
                            <span x-text="currentIndex + 1" class="font-bold text-zinc-900 dark:text-white"></span>
                            <span> / </span>
                            <span x-text="maxIndex + 1"></span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button @click="prev()" 
                                    aria-label="Diapositiva anterior"
                                    class="w-10 h-10 rounded-full bg-white dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 shadow-sm flex items-center justify-center text-zinc-700 dark:text-zinc-200 hover:scale-105 active:scale-95 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button @click="next()" 
                                    aria-label="Siguiente diapositiva"
                                    class="w-10 h-10 rounded-full bg-white dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 shadow-sm flex items-center justify-center text-zinc-700 dark:text-zinc-200 hover:scale-105 active:scale-95 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Slider Track (Touch Swipeable en Móvil) -->
                <div class="relative overflow-hidden rounded-2xl"
                     @touchstart.passive="touchStartX = $event.touches[0].clientX"
                     @touchend.passive="if ($event.changedTouches[0].clientX - touchStartX > 45) prev(); if (touchStartX - $event.changedTouches[0].clientX > 45) next();">
                    <div class="flex transition-transform duration-500 ease-out"
                         :style="'transform: translateX(-' + (currentIndex * (100 / perPage)) + '%)'">
                        @foreach ($carouselProducts as $item)
                            @php
                                $imgSrc = $getProductImage($item);
                                $isStudioPackshot = $item->usesWhiteStudioBackground();
                            @endphp
                            <div class="w-full sm:w-1/2 lg:w-1/4 shrink-0 p-2 sm:p-2.5">
                                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/90 dark:border-zinc-800 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between h-full group">
                                    <div>
                                        <!-- Imagen con Zoom, Brillo Diagonal & Click para Ficha Completa -->
                                        <div @click="openDetail({
                                                id: {{ $item->id }},
                                                name: '{{ addslashes($item->name) }}',
                                                code: {{ $item->code }},
                                                price: {{ $item->rental_price }},
                                                stock: {{ $item->stock }},
                                                category: '{{ addslashes($item->category->name ?? 'Mobiliario') }}',
                                                description: '{{ addslashes($item->description ?? 'Pieza de alta calidad para ambientación de bodas y banquetes.') }}',
                                                is_sale: {{ $item->is_sale ? 'true' : 'false' }},
                                                image: '{{ $imgSrc }}'
                                             })"
                                             class="card-gloss-shine cursor-pointer relative h-48 sm:h-52 overflow-hidden {{ $isStudioPackshot ? 'bg-white' : 'bg-zinc-100 dark:bg-zinc-800' }}">
                                            <img src="{{ $imgSrc }}" 
                                                 alt="{{ $item->name }}" 
                                                 loading="lazy"
                                                 class="w-full h-full {{ $isStudioPackshot ? 'object-contain p-3' : 'object-cover' }} group-hover:scale-105 transition-transform duration-500 ease-out">

                                            <span class="absolute top-2.5 left-2.5 backdrop-blur-md bg-white/85 dark:bg-zinc-900/85 text-zinc-900 dark:text-zinc-100 text-[10px] font-mono font-medium px-2 py-0.5 rounded shadow-sm border border-white/50 dark:border-zinc-700">
                                                Cód: {{ $item->code }}
                                            </span>

                                            @if($item->is_featured)
                                                <span class="absolute top-2.5 right-2.5 bg-amber-400 text-zinc-950 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded shadow-sm">
                                                    ★ Destacado
                                                </span>
                                            @elseif($item->stock > 0)
                                                <span class="absolute top-2.5 right-2.5 backdrop-blur-md bg-white/85 dark:bg-zinc-900/85 text-emerald-700 dark:text-emerald-400 text-[10px] font-semibold px-2 py-0.5 rounded shadow-sm border border-white/50 dark:border-zinc-700">
                                                    {{ $item->stock }} un.
                                                </span>
                                            @else
                                                <span class="absolute top-2.5 right-2.5 bg-zinc-700 text-white text-[10px] font-medium px-2 py-0.5 rounded shadow-sm">
                                                    Agotado
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Detalles del Producto -->
                                        <div class="p-4 sm:p-5">
                                            <span class="text-[10px] font-mono tracking-widest uppercase text-amber-800 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded border border-amber-200/60 dark:border-amber-800/60 inline-block font-semibold">
                                                {{ $item->category->name ?? 'Catálogo' }}
                                            </span>

                                            <h3 @click="openDetail({
                                                    id: {{ $item->id }},
                                                    name: '{{ addslashes($item->name) }}',
                                                    code: {{ $item->code }},
                                                    price: {{ $item->rental_price }},
                                                    stock: {{ $item->stock }},
                                                    category: '{{ addslashes($item->category->name ?? 'Catálogo') }}',
                                                    description: '{{ addslashes($item->description ?? 'Pieza de alta calidad para ambientación.') }}',
                                                    is_sale: {{ $item->is_sale ? 'true' : 'false' }},
                                                    image: '{{ $imgSrc }}'
                                                 })"
                                                class="cursor-pointer font-serif font-bold text-zinc-900 dark:text-white text-base sm:text-lg mt-1.5 leading-snug line-clamp-1 group-hover:text-amber-800 dark:group-hover:text-amber-400 transition-colors" title="{{ $item->name }}">
                                                {{ $item->name }}
                                            </h3>

                                            <div class="mt-3 pt-2.5 border-t border-zinc-100 dark:border-zinc-800 flex items-baseline justify-between">
                                                <div>
                                                    <span class="text-[10px] text-zinc-500 dark:text-zinc-400 block uppercase tracking-wider">Arriendo:</span>
                                                    <p class="text-base sm:text-lg font-bold font-mono text-zinc-900 dark:text-white mt-0.5">
                                                        ${{ number_format($item->rental_price, 0, ',', '.') }}
                                                        <span class="text-xs font-normal text-zinc-500 dark:text-zinc-400">/ un.</span>
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    <button @click="openDetail({
                                                                id: {{ $item->id }},
                                                                name: '{{ addslashes($item->name) }}',
                                                                code: {{ $item->code }},
                                                                price: {{ $item->rental_price }},
                                                                stock: {{ $item->stock }},
                                                                category: '{{ addslashes($item->category->name ?? 'Catálogo') }}',
                                                                description: '{{ addslashes($item->description ?? 'Pieza de alta calidad para ambientación.') }}',
                                                                is_sale: {{ $item->is_sale ? 'true' : 'false' }},
                                                                image: '{{ $imgSrc }}'
                                                             })" 
                                                            class="text-xs font-medium text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white underline">
                                                        Ficha
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Botón Agregar al Arriendo con Vuelo al Carrito -->
                                    <div class="p-4 sm:p-5 pt-0" x-data="{ added: false }">
                                        @if($item->stock > 0)
                                            <button @click="addItem({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->rental_price }}, {{ $item->stock }}, {{ $item->code }}, $event, '{{ $imgSrc }}'); added = true; setTimeout(() => added = false, 1000)"
                                                    :class="added ? 'bg-emerald-600 text-white shadow-md' : 'bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white shadow-sm'"
                                                    class="w-full py-2 px-3 rounded-xl text-xs font-semibold transition-all duration-200 flex items-center justify-center gap-1.5 active:scale-98">
                                                <template x-if="!added">
                                                    <span class="flex items-center gap-1.5">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                        <span>Agregar</span>
                                                    </span>
                                                </template>
                                                <template x-if="added">
                                                    <span class="flex items-center gap-1.5 font-bold">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                        <span>Agregado</span>
                                                    </span>
                                                </template>
                                            </button>
                                        @else
                                            <button disabled class="w-full py-2 px-3 rounded-xl text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 border border-zinc-200 dark:border-zinc-700 cursor-not-allowed">
                                                Sin stock
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Indicadores de Posición en Carrusel -->
                <div class="mt-6 flex items-center justify-center gap-2">
                    <template x-for="i in (maxIndex + 1)" :key="i">
                        <button @click="currentIndex = (i - 1)" 
                                :class="currentIndex === (i - 1) ? 'w-8 bg-zinc-900 dark:bg-amber-400' : 'w-2 bg-zinc-300 dark:bg-zinc-700 hover:bg-zinc-400'"
                                class="h-2 rounded-full transition-all duration-300"
                                :aria-label="'Ir a diapositiva ' + i"></button>
                    </template>
                </div>
            </div>
        </section>

        <!-- PUENTE EDITORIAL & VISUAL: CONEXIÓN DE PIEZAS DESTACADAS → MONTAJES EN ESCENA -->
        <div class="relative bg-gradient-to-b from-zinc-50 via-amber-50/40 to-white dark:from-zinc-900/50 dark:via-amber-950/15 dark:to-zinc-950 py-8 sm:py-10 overflow-hidden">
            <!-- Línea vertical dorada sutil que une ambas secciones -->
            <div class="pointer-events-none absolute inset-y-0 left-1/2 -translate-x-1/2 w-px bg-gradient-to-b from-amber-500/0 via-amber-500/45 to-amber-500/0"></div>

            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md rounded-2xl sm:rounded-full border border-amber-200/80 dark:border-amber-800/40 shadow-sm px-5 py-4 sm:px-7 sm:py-3.5 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3 text-center sm:text-left">
                        <span class="hidden sm:inline-flex items-center justify-center w-9 h-9 rounded-full bg-amber-50 dark:bg-amber-950/60 border border-amber-200/80 dark:border-amber-800/60 text-amber-700 dark:text-amber-400 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                        </span>
                        <div>
                            <span class="text-[10px] font-mono uppercase tracking-[0.22em] text-amber-700 dark:text-amber-400 font-semibold block">
                                De la pieza individual al montaje real
                            </span>
                            <p class="text-xs sm:text-sm text-zinc-700 dark:text-zinc-300 font-medium">
                                Mira cómo lucen estas piezas vestidas en escena y cotízalas directo desde la foto.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 shrink-0">
                        <a href="#montajes"
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-zinc-900 hover:bg-zinc-800 dark:bg-amber-500 dark:hover:bg-amber-400 text-white dark:text-zinc-950 text-xs font-semibold transition shadow-sm">
                            <span>Ver en escena</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/></svg>
                        </a>
                        <a href="#catalogo"
                           class="inline-flex items-center gap-1 px-3.5 py-2 rounded-full bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 text-xs font-semibold transition border border-zinc-200/80 dark:border-zinc-700">
                            <span>Catálogo</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN: MONTAJES & PUESTA EN ESCENA (Conectada con la Vitrina Destacada) -->
        <section id="montajes" class="scroll-mt-20 pt-8 pb-16 sm:pt-10 sm:pb-20 border-b border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-950 overflow-x-clip">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Encabezado Editorial Conciso y Elegante -->
                <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-12">
                    <span class="text-[10px] font-mono uppercase tracking-[0.18em] sm:tracking-[0.28em] text-amber-800 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 px-3.5 sm:px-4 py-1.5 rounded-full inline-block mb-3.5 font-semibold max-w-full">
                        Puesta en Escena &bull; Experiencia Real
                    </span>
                    <h2 class="font-serif text-2xl sm:text-4xl md:text-5xl font-light text-zinc-900 dark:text-white tracking-wide break-words">
                        Montaje de Eventos en Santiago &ndash; <span class="italic font-normal text-amber-700 dark:text-amber-500">Tu Solución Completa</span>
                    </h2>
                    <p class="text-xs sm:text-sm md:text-base text-zinc-600 dark:text-zinc-400 mt-3.5 font-light leading-relaxed max-w-2xl mx-auto">
                        En <span class="font-medium text-zinc-900 dark:text-white">{{ $settings->site_name ?? 'VYS · Productora de Eventos' }}</span> transformamos cada pieza de nuestro catálogo en celebraciones memorables: mesas, sillas, vajilla, mantelería y carpas montadas con precisión para matrimonios y eventos corporativos.
                    </p>
                </div>

                @php
                    $teno = collect($montajes)->firstWhere('is_featured', true) ?? ($montajes[0] ?? null);
                    $tenoCover = $teno['cover_image'] ?? asset('images/montajes/teno-mesa-principal.jpg');
                    $tenoSubPhotos = collect($teno['gallery'] ?? [])
                        ->filter(static fn ($url): bool => is_string($url) && $url !== '' && $url !== $tenoCover)
                        ->unique()
                        ->values()
                        ->take(4)
                        ->all();
                @endphp
                @if($teno)
                    <!-- Montaje Destacado Spotlight (Gestionable 100% desde el Panel de Administración) -->
                    <div class="bg-zinc-50/70 dark:bg-zinc-900/60 rounded-3xl border border-zinc-200/90 dark:border-zinc-800 p-4 sm:p-8 lg:p-10 mb-10 sm:mb-12 shadow-sm">
                        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-5 sm:gap-6 pb-6 border-b border-zinc-200 dark:border-zinc-800 mb-6 sm:mb-8">
                            <div class="space-y-3 sm:space-y-4 min-w-0">
                                <h3 class="font-serif text-xl sm:text-3xl lg:text-4xl font-light tracking-[0.06em] sm:tracking-[0.2em] text-zinc-900 dark:text-white uppercase leading-snug break-words">
                                    {{ $teno['title'] }}
                                </h3>
                                <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200">
                                        <span class="text-zinc-400 font-mono text-[10px] uppercase">Tipo:</span>
                                        <strong class="font-semibold">{{ $teno['type'] }}</strong>
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200">
                                        <span class="text-zinc-400 font-mono text-[10px] uppercase">Año:</span>
                                        <strong class="font-semibold">{{ $teno['year'] }}</strong>
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200">
                                        <span class="text-zinc-400 font-mono text-[10px] uppercase">Lugar:</span>
                                        <strong class="font-semibold">{{ $teno['location'] }}</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="max-w-md text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 lg:text-right font-light leading-relaxed">
                                <p class="font-medium text-zinc-900 dark:text-white mb-1">{{ $teno['summary'] }}</p>
                                <p>{{ Str::limit($teno['description'], 150) }}</p>
                                <div class="mt-3 flex lg:justify-end gap-2">
                                    <a href="{{ route('catalog.montajes') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 dark:text-amber-400 hover:underline">
                                        <span>Ver especificaciones de este montaje</span>
                                        <span>&rarr;</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        @php
                            $hotspotPool = $carouselProducts->values();
                            $hotspotCoords = [
                                ['x' => 26, 'y' => 64, 'tag' => 'Sillas & Asientos de Gala'],
                                ['x' => 48, 'y' => 52, 'tag' => 'Mesa & Mantelería Imperial'],
                                ['x' => 38, 'y' => 38, 'tag' => 'Cristalería Fina de Brindis'],
                                ['x' => 68, 'y' => 46, 'tag' => 'Vajilla & Servicio de Mesa'],
                            ];
                            $montajeHotspots = [];
                            foreach ($hotspotCoords as $idx => $coord) {
                                $hpProd = $hotspotPool->get($idx);
                                if ($hpProd) {
                                    $montajeHotspots[] = [
                                        'x' => $coord['x'],
                                        'y' => $coord['y'],
                                        'tag' => $coord['tag'],
                                        'id' => $hpProd->id,
                                        'name' => $hpProd->name,
                                        'code' => $hpProd->code,
                                        'price' => (int) $hpProd->rental_price,
                                        'stock' => (int) $hpProd->stock,
                                        'category' => $hpProd->category->name ?? 'Colección Gala',
                                        'description' => $hpProd->description ?? 'Pieza de alta gama utilizada en nuestros montajes oficiales.',
                                        'is_sale' => (bool) $hpProd->is_sale,
                                        'image' => $getProductImage($hpProd),
                                    ];
                                }
                            }
                        @endphp

                        <!-- Composición Fotográfica Dinámica e Interactiva (Lookbook Hotspots + Quick Photo Switcher) -->
                        <div class="space-y-4 sm:space-y-6"
                             x-data="{
                                 mainPhoto: {{ Js::from($tenoCover) }},
                                 subPhotos: {{ Js::from($tenoSubPhotos) }},
                                 activeHotspot: null,
                                 showHotspots: true,
                                 hotspots: {{ Js::from($montajeHotspots) }},
                                 swapPhoto(index) {
                                     if (index < 0 || index >= this.subPhotos.length) return;
                                     this.activeHotspot = null;
                                     const currentMain = this.mainPhoto;
                                     this.mainPhoto = this.subPhotos[index];
                                     this.subPhotos[index] = currentMain;
                                 }
                             }">
                            <!-- Foto 1: Mesa Principal Gran Angular con Hotspots Interactivos -->
                            <div class="relative rounded-2xl overflow-hidden shadow-lg border border-zinc-200/80 dark:border-zinc-700/80 group"
                                 @click.self="activeHotspot = null">
                                <img :src="mainPhoto"
                                     src="{{ $tenoCover }}" 
                                     alt="{{ $teno['title'] }}" 
                                     class="w-full h-64 sm:h-96 md:h-[500px] object-cover group-hover:scale-[1.015] transition-all duration-700 ease-out">

                                <!-- Badge Superior Lookbook Interactivo -->
                                <template x-if="hotspots.length > 0">
                                    <div class="absolute top-3 left-3 right-3 sm:top-3.5 sm:left-3.5 sm:right-3.5 flex items-center justify-between gap-2 z-20 pointer-events-none">
                                        <div class="inline-flex items-center gap-2 bg-zinc-950/75 backdrop-blur-md text-white text-[10px] sm:text-xs font-mono px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-full border border-amber-400/40 shadow-lg pointer-events-auto max-w-full">
                                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping shrink-0"></span>
                                            <span class="truncate sm:whitespace-normal">Toca los puntos <strong>(+)</strong> para cotizar</span>
                                        </div>
                                        <button type="button"
                                                @click="showHotspots = !showHotspots; activeHotspot = null"
                                                class="pointer-events-auto hidden sm:inline-flex items-center gap-1.5 bg-zinc-950/75 hover:bg-zinc-900 backdrop-blur-md text-zinc-200 text-[10px] font-mono px-3 py-1.5 rounded-full border border-white/20 transition shrink-0">
                                            <span x-text="showHotspots ? 'Ocultar Puntos' : 'Mostrar Puntos'"></span>
                                        </button>
                                    </div>
                                </template>

                                <!-- Puntos Pulsantes (Hotspots) sobre la Fotografía -->
                                <template x-if="showHotspots && hotspots.length > 0">
                                    <div class="absolute inset-0 z-20 pointer-events-none">
                                        <template x-for="(spot, index) in hotspots" :key="index">
                                            <div class="absolute pointer-events-auto"
                                                 :style="`left: ${spot.x}%; top: ${spot.y}%; transform: translate(-50%, -50%);`">
                                                <!-- Botón Pin Dorado Pulsante -->
                                                <button type="button"
                                                        @mouseenter="activeHotspot = index"
                                                        @click.stop="activeHotspot = (activeHotspot === index ? null : index)"
                                                        :aria-label="'Ver pieza: ' + spot.name"
                                                        class="relative w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center transition-transform duration-300 focus:outline-none"
                                                        :class="activeHotspot === index ? 'scale-125 bg-amber-400 text-zinc-950 ring-4 ring-white/80 shadow-2xl' : 'bg-zinc-950/85 hover:bg-amber-400 text-amber-400 hover:text-zinc-950 border-2 border-amber-400 shadow-xl hover:scale-110'">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-50"></span>
                                                    <svg class="w-4 h-4 relative z-10 transition-transform duration-300"
                                                         :class="activeHotspot === index ? 'rotate-45' : ''"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                </button>

                                                <!-- Tarjeta Flotante Desktop/Tablet del Hotspot (en móvil se ancla centrada abajo sin recorte) -->
                                                <div x-show="activeHotspot === index"
                                                     x-transition:enter="transition ease-out duration-200"
                                                     x-transition:enter-start="opacity-0 scale-90 translate-y-2"
                                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                     x-transition:leave="transition ease-in duration-150"
                                                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                                     x-transition:leave-end="opacity-0 scale-90 translate-y-2"
                                                     @click.outside="if (activeHotspot === index) activeHotspot = null"
                                                     :class="[spot.x > 46 ? 'right-0' : 'left-0', spot.y < 44 ? 'top-full mt-3' : 'bottom-full mb-3']"
                                                     class="hidden sm:block absolute w-72 bg-zinc-950/95 backdrop-blur-xl text-white rounded-2xl p-3.5 border border-amber-400/50 shadow-2xl z-30"
                                                     style="display: none;">
                                                    <div class="flex items-start gap-3">
                                                        <img :src="spot.image"
                                                             :alt="spot.name"
                                                             :class="(spot.image || '').includes('default-') ? 'bg-white object-contain p-1' : 'object-cover'"
                                                             class="w-14 h-14 rounded-xl border border-white/15 shrink-0">
                                                        <div class="min-w-0 flex-1">
                                                            <span class="text-[9px] font-mono uppercase tracking-wider text-amber-400 block truncate" x-text="spot.tag"></span>
                                                            <h5 class="font-serif font-bold text-sm text-white truncate mt-0.5" x-text="spot.name"></h5>
                                                            <div class="flex items-baseline justify-between mt-1">
                                                                <span class="font-mono font-bold text-xs text-amber-300" x-text="'$' + Number(spot.price).toLocaleString('es-CL') + ' / un.'"></span>
                                                                <span class="text-[10px] font-mono text-zinc-400" x-text="'Cód: ' + spot.code"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3 pt-2.5 border-t border-white/10 flex items-center gap-2">
                                                        <button type="button"
                                                                @click.stop="addItem(spot.id, spot.name, spot.price, spot.stock, spot.code, $event, spot.image)"
                                                                class="flex-1 py-1.5 px-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-zinc-950 font-bold text-[11px] flex items-center justify-center gap-1 transition shadow">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                            <span>Cotizar Pieza</span>
                                                        </button>
                                                        <button type="button"
                                                                @click.stop="openDetail(spot); activeHotspot = null"
                                                                class="py-1.5 px-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-[11px] font-medium transition">
                                                            Ficha
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- Tarjeta Móvil Segura del Hotspot (anclada al ancho de la foto, cero recorte ni desborde) -->
                                <template x-if="activeHotspot !== null && hotspots[activeHotspot]">
                                    <div @click.stop
                                         class="sm:hidden absolute inset-x-2.5 bottom-2.5 z-30 bg-zinc-950/95 backdrop-blur-xl text-white rounded-2xl p-3 border border-amber-400/50 shadow-2xl">
                                        <div class="flex items-center gap-2.5">
                                            <img :src="hotspots[activeHotspot].image"
                                                 :alt="hotspots[activeHotspot].name"
                                                 :class="(hotspots[activeHotspot].image || '').includes('default-') ? 'bg-white object-contain p-1' : 'object-cover'"
                                                 class="w-11 h-11 rounded-xl border border-white/15 shrink-0">
                                            <div class="min-w-0 flex-1">
                                                <h5 class="font-serif font-bold text-xs text-white truncate" x-text="hotspots[activeHotspot].name"></h5>
                                                <span class="font-mono font-bold text-[11px] text-amber-300" x-text="'$' + Number(hotspots[activeHotspot].price).toLocaleString('es-CL') + ' / un.'"></span>
                                            </div>
                                            <button type="button"
                                                    @click.stop="addItem(hotspots[activeHotspot].id, hotspots[activeHotspot].name, hotspots[activeHotspot].price, hotspots[activeHotspot].stock, hotspots[activeHotspot].code, $event, hotspots[activeHotspot].image)"
                                                    class="py-1.5 px-2.5 rounded-xl bg-amber-400 text-zinc-950 font-bold text-[11px] shrink-0">
                                                + Cotizar
                                            </button>
                                            <button type="button"
                                                    @click.stop="activeHotspot = null"
                                                    aria-label="Cerrar detalle"
                                                    class="w-7 h-7 rounded-lg bg-white/10 text-zinc-300 flex items-center justify-center shrink-0 text-xs">
                                                &times;
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent flex items-end justify-between p-3.5 sm:p-6 pointer-events-none">
                                    <div class="text-white min-w-0">
                                        <span class="text-[10px] font-mono tracking-widest uppercase bg-black/40 backdrop-blur-sm px-2.5 py-1 rounded border border-white/20 inline-block max-w-full truncate">{{ $teno['capacity'] ?: 'Montaje Premium' }}</span>
                                        <p class="text-xs sm:text-sm mt-1 text-zinc-200 line-clamp-2">{{ $teno['summary'] }}</p>
                                    </div>
                                    @if(count($tenoSubPhotos) > 0)
                                        <span class="hidden sm:inline-flex items-center gap-1.5 text-[11px] font-mono text-amber-300 bg-black/50 backdrop-blur-md px-3 py-1 rounded-full border border-amber-400/30 shrink-0">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                            {{ count($tenoSubPhotos) === 1 ? 'Haz clic en la foto inferior para alternar vista' : 'Haz clic en las fotos inferiores para alternar vista' }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Barra Sincronizada: Piezas del Catálogo presentes en el Lookbook (1 col en móvil, 2 en tablet, 4 en desktop) -->
                            <template x-if="hotspots.length > 0">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                                    <template x-for="(spot, idx) in hotspots" :key="'chip-' + idx">
                                        <div @mouseenter="showHotspots = true; activeHotspot = idx"
                                             @click="showHotspots = true; activeHotspot = (activeHotspot === idx ? null : idx)"
                                             :class="activeHotspot === idx ? 'border-amber-500 ring-2 ring-amber-500/20 bg-amber-50/50 dark:bg-amber-950/30' : 'border-zinc-200/90 dark:border-zinc-800 bg-white dark:bg-zinc-900/90 hover:border-amber-400/60'"
                                             class="cursor-pointer rounded-xl border p-2.5 flex items-center gap-2.5 transition-all duration-200 shadow-xs">
                                            <img :src="spot.image"
                                                 :alt="spot.name"
                                                 :class="(spot.image || '').includes('default-') ? 'bg-white object-contain p-1' : 'object-cover'"
                                                 class="w-10 h-10 rounded-lg border border-zinc-200/80 dark:border-zinc-700 shrink-0">
                                            <div class="min-w-0 flex-1">
                                                <span class="text-[9px] font-mono uppercase tracking-wider text-amber-700 dark:text-amber-400 block truncate" x-text="'Punto ' + (idx + 1)"></span>
                                                <p class="text-xs font-semibold text-zinc-900 dark:text-white truncate" x-text="spot.name"></p>
                                                <span class="text-[11px] font-mono font-bold text-zinc-600 dark:text-zinc-300" x-text="'$' + Number(spot.price).toLocaleString('es-CL')"></span>
                                            </div>
                                            <button type="button"
                                                    @click.stop="addItem(spot.id, spot.name, spot.price, spot.stock, spot.code, $event, spot.image)"
                                                    title="Agregar a cotización"
                                                    class="w-7 h-7 rounded-lg bg-zinc-900 hover:bg-amber-500 dark:bg-zinc-800 dark:hover:bg-amber-500 text-white hover:text-zinc-950 flex items-center justify-center shrink-0 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- Fotos Adicionales de la Galería (sin duplicados, adaptables a la cantidad real cargada) -->
                            @if(count($tenoSubPhotos) > 0)
                                @php
                                    $subGridCols = match (count($tenoSubPhotos)) {
                                        1 => 'grid-cols-1',
                                        2 => 'grid-cols-1 sm:grid-cols-2',
                                        3 => 'grid-cols-1 sm:grid-cols-3',
                                        default => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
                                    };
                                    $subCaptions = [
                                        trim(($teno['location'] ?? '') . ' • ' . ($teno['year'] ?? ''), ' •'),
                                        $teno['type'] ?? 'Detalle del Montaje',
                                        $teno['capacity'] ?: 'Perspectiva Adicional',
                                        $teno['client'] ?: 'Galería del Evento',
                                    ];
                                @endphp
                                <div class="grid {{ $subGridCols }} gap-4 sm:gap-6">
                                    @foreach($tenoSubPhotos as $subIndex => $subUrl)
                                        <div @click="swapPhoto({{ $subIndex }})"
                                             class="card-gloss-shine cursor-pointer relative rounded-2xl overflow-hidden shadow-md hover:shadow-xl border border-zinc-200/80 dark:border-zinc-700/80 hover:border-amber-500/60 transition-all duration-300 group">
                                            <img :src="subPhotos[{{ $subIndex }}]"
                                                 src="{{ $subUrl }}"
                                                 alt="{{ $teno['title'] }} perspectiva {{ $subIndex + 1 }}"
                                                 class="w-full {{ count($tenoSubPhotos) === 1 ? 'h-64 sm:h-80' : 'h-60 sm:h-72' }} object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent flex items-end justify-between p-4">
                                                <span class="text-white text-xs font-medium">{{ $subCaptions[$subIndex] ?? ($teno['type'] ?? 'Detalle') }}</span>
                                                <span class="text-[10px] font-mono text-amber-300 bg-black/50 backdrop-blur-sm px-2 py-0.5 rounded-full border border-white/15 opacity-80 group-hover:opacity-100 transition">Ampliar &uarr;</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Grilla de Otros Montajes Realizados -->
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                        <div>
                            <span class="text-[10px] font-mono uppercase tracking-[0.25em] text-amber-800 dark:text-amber-400 font-semibold block mb-1">
                                Portafolio Fotográfico
                            </span>
                            <h3 class="font-serif text-xl sm:text-3xl font-light text-zinc-900 dark:text-white">
                                Galería de <span class="italic font-normal text-amber-700 dark:text-amber-500">Puestas en Escena</span>
                            </h3>
                        </div>
                        <a href="{{ route('catalog.montajes') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-zinc-900 dark:text-white bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 px-4 py-2.5 rounded-full transition border border-zinc-300 dark:border-zinc-700 shadow-sm">
                            <span>Ver Todos los Montajes & Fichas</span>
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>

                    @php
                        $montajesGridCols = match (count($montajes)) {
                            1 => 'grid-cols-1 max-w-xl mx-auto',
                            2 => 'grid-cols-1 sm:grid-cols-2',
                            3 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
                            default => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
                        };
                    @endphp
                    <div class="grid {{ $montajesGridCols }} gap-6">
                        @foreach ($montajes as $montaje)
                            <div data-reveal class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/90 dark:border-zinc-800 hover:border-amber-500/50 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                                <div>
                                    <div class="card-gloss-shine relative aspect-[4/3] sm:aspect-[16/10] overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                                        <img src="{{ $montaje['image'] }}" 
                                             alt="{{ $montaje['title'] }}" 
                                             class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 ease-out">
                                        <span class="absolute top-2.5 left-2.5 bg-black/75 backdrop-blur-md text-white text-[10px] font-mono uppercase px-2.5 py-1 rounded-full border border-white/20 shadow-sm">
                                            {{ $montaje['year'] }}
                                        </span>
                                        <span class="absolute top-2.5 right-2.5 max-w-[65%] truncate bg-amber-400 text-zinc-950 text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md">
                                            {{ $montaje['capacity'] }}
                                        </span>
                                    </div>
                                    <div class="p-4 sm:p-5">
                                        <span class="text-[10px] font-mono uppercase tracking-wider text-amber-800 dark:text-amber-400 font-semibold block mb-1">
                                            {{ $montaje['type'] }} &bull; {{ $montaje['location'] }}
                                        </span>
                                        <h4 class="font-serif font-bold text-base sm:text-lg text-zinc-900 dark:text-white line-clamp-1 group-hover:text-amber-700 dark:group-hover:text-amber-400 transition-colors">
                                            {{ $montaje['title'] }}
                                        </h4>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 line-clamp-2 leading-relaxed">
                                            {{ $montaje['description'] }}
                                        </p>
                                    </div>
                                </div>
                                <div class="p-4 sm:p-5 pt-0">
                                    <a href="{{ route('catalog.montajes') }}" class="w-full py-2 px-3 rounded-xl text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 group-hover:bg-zinc-900 dark:group-hover:bg-amber-500 group-hover:text-white dark:group-hover:text-zinc-950 text-zinc-800 dark:text-zinc-200 flex items-center justify-center gap-1.5 transition-colors duration-300">
                                        <span>Ver Detalle del Montaje</span>
                                        <span>&rarr;</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- PUENTE EDITORIAL & CINTA LUMINOSA: CONEXIÓN MONTAJES → CATÁLOGO OFICIAL -->
        <div class="relative bg-gradient-to-b from-white via-amber-50/40 to-zinc-50 dark:from-zinc-950 dark:via-zinc-900/50 dark:to-zinc-950 pt-6 pb-8 sm:pt-8 sm:pb-10 overflow-hidden">
            <!-- Cinta Editorial Sutil en Tonos Champagne / Neutro con Desvanecimiento Lateral -->
            <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm text-zinc-700 dark:text-zinc-300 border-y border-amber-200/70 dark:border-amber-800/30 py-3 overflow-hidden select-none"
                 style="-webkit-mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent); mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);">
                <div class="luxury-marquee-track items-center gap-8 text-[11px] sm:text-xs font-mono uppercase tracking-[0.2em]">
                    @for ($m = 0; $m < 2; $m++)
                        <div class="flex items-center gap-8 shrink-0 pr-8">
                            <span class="flex items-center gap-2.5"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Vajilla de Porcelana &amp; Gala</span>
                            <span class="flex items-center gap-2.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Cristalería Fina</span>
                            <span class="flex items-center gap-2.5"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Sillas Chiavari &amp; Mesas</span>
                            <span class="flex items-center gap-2.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Mantelería &amp; Servilletas</span>
                            <span class="flex items-center gap-2.5"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Carpas, Toldos &amp; Catering</span>
                            <span class="flex items-center gap-2.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> ¡Nosotros Lavamos por Ti!</span>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    @endif

    <!-- CONTENIDO PRINCIPAL: CATÁLOGO DE ARRIENDOS Y FILTROS -->
    <main id="catalogo" class="scroll-mt-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-14 sm:pt-8 sm:pb-16 flex-1 w-full">
        
        <div class="mb-6 sm:mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <span class="text-[10px] font-mono uppercase tracking-[0.25em] text-amber-800 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 px-3.5 py-1 rounded-full inline-block mb-2 font-semibold">
                    Inventario Oficial
                </span>
                <h2 class="font-serif text-2xl sm:text-4xl font-light text-zinc-900 dark:text-white tracking-wide">
                    Catálogo de <span class="italic font-normal text-amber-700 dark:text-amber-500">Arriendos</span>
                </h2>
                <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 mt-1 font-light">
                    Elige las piezas exactas de nuestros montajes o filtra por categoría para armar tu cotización.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 text-xs text-zinc-600 dark:text-zinc-300 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span><strong class="font-mono text-zinc-900 dark:text-white">{{ $categories->sum('products_count') }}</strong> artículos activos</span>
                </span>
                <button type="button"
                        @click="cartOpen = true"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-zinc-900 hover:bg-zinc-800 dark:bg-amber-500 dark:hover:bg-amber-400 text-white dark:text-zinc-950 text-xs font-semibold transition shadow-xs">
                    <span>Mi Cotización</span>
                    <span class="font-mono font-bold px-1.5 py-0.5 rounded-full bg-white/20 dark:bg-black/15" x-text="countItems()"></span>
                </button>
            </div>
        </div>

        <!-- Buscador Integrado + Atajos Populares Balanceados -->
        <div class="mb-7 bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 p-3 sm:p-4 rounded-2xl shadow-sm flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-3 relative">
            <form method="GET" action="{{ route('catalog.index') }}#catalogo" class="flex gap-2 w-full lg:flex-1 lg:max-w-2xl relative min-w-0">
                @if(request('categoria'))
                    <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                @endif
                <div class="relative flex-1 min-w-0">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 dark:text-zinc-500">
                        <template x-if="!isSearching">
                            <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </template>
                        <template x-if="isSearching">
                            <svg class="w-4 h-4 text-amber-600 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                        </template>
                    </span>
                    <input type="text" 
                           name="buscar" 
                           x-model="searchQuery" 
                           @input.debounce.300ms="liveSearch()"
                           @keydown.escape="clearLiveSearch()"
                           maxlength="60"
                           value="{{ request('buscar') }}" 
                           placeholder="Buscar por nombre o código (ej: 1030, copa, mesa, lino)..." 
                           class="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-full pl-10 pr-10 py-2.5 text-xs sm:text-sm text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 focus:bg-white dark:focus:bg-zinc-800 transition duration-200" />
                    
                    <button type="button" 
                            x-show="searchQuery && searchQuery.trim().length > 0" 
                            @click="searchQuery = ''; clearLiveSearch()"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition"
                            title="Limpiar búsqueda">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <button type="submit" class="bg-zinc-900 hover:bg-zinc-800 dark:bg-amber-500 dark:hover:bg-amber-600 text-white dark:text-zinc-950 font-semibold px-4 sm:px-6 py-2.5 rounded-full text-xs sm:text-sm transition duration-200 active:scale-95 shadow-sm shrink-0">
                    Buscar
                </button>

                <!-- Dropdown Flotante de Resultados en Tiempo Real -->
                <div x-show="searchResults !== null" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     @click.outside="clearLiveSearch()"
                     class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 z-50 overflow-hidden max-h-96 flex flex-col"
                     style="display: none;">
                    
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-100 dark:border-zinc-700 flex justify-between items-center text-xs">
                        <span class="font-semibold text-zinc-700 dark:text-zinc-300 font-mono" x-text="searchResults ? searchResults.length + ' resultado(s) en vivo' : ''"></span>
                        <button type="button" @click="clearLiveSearch()" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 font-medium text-xs">Cerrar &times;</button>
                    </div>

                    <div class="overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800 p-2">
                        <template x-if="searchResults && searchResults.length === 0">
                            <div class="p-6 text-center text-zinc-500 dark:text-zinc-400 text-xs">
                                No se encontraron productos coincidentes.
                            </div>
                        </template>

                        <template x-for="item in (searchResults || [])" :key="item.id">
                            <div class="p-2.5 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-xl transition flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0 flex-1 cursor-pointer"
                                     @click="openDetail({
                                         id: item.id,
                                         name: item.name,
                                         code: item.code,
                                         price: item.rental_price,
                                         stock: item.stock,
                                         category: item.category_name,
                                         description: 'Pieza de alta calidad para ambientación de eventos.',
                                         is_sale: false,
                                         image: getLiveImage(item)
                                     })">
                                    <img :src="getLiveImage(item)" 
                                         :alt="item.name" 
                                         :class="getLiveImage(item).includes('default-') ? 'bg-white object-contain p-1' : 'object-cover bg-zinc-100 dark:bg-zinc-800'"
                                         class="w-12 h-12 rounded-lg shrink-0 border border-zinc-200 dark:border-zinc-700">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-zinc-900 dark:text-white text-xs sm:text-sm truncate" x-text="item.name"></p>
                                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-mono mt-0.5 truncate">
                                            <span x-text="'Cód: ' + item.code"></span> &bull; 
                                            <span x-text="item.category_name"></span> &bull; 
                                            <span class="font-semibold text-zinc-800 dark:text-zinc-200" x-text="'$' + Number(item.rental_price).toLocaleString('es-CL')"></span>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <template x-if="item.stock > 0">
                                        <button type="button" 
                                                @click="addItem(item.id, item.name, item.rental_price, item.stock, item.code, $event, getLiveImage(item))"
                                                class="px-3 py-1.5 rounded-lg bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white text-xs font-semibold shadow-sm active:scale-95 transition">
                                            + Agregar
                                        </button>
                                    </template>
                                    <template x-if="item.stock <= 0">
                                        <span class="text-[10px] text-zinc-400 dark:text-zinc-500 font-mono">Sin stock</span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </form>

            <div class="flex flex-wrap items-center justify-between lg:justify-end gap-2">
                <!-- Atajos rápidos de búsqueda popular -->
                <div class="flex flex-wrap items-center gap-1.5 text-[11px]">
                    <span class="text-zinc-400 dark:text-zinc-500 font-mono uppercase tracking-wider text-[10px] mr-0.5 hidden sm:inline">Populares:</span>
                    @foreach(['Copa', 'Plato', 'Silla', 'Mantel'] as $quickTerm)
                        <button type="button"
                                @click="searchQuery = '{{ $quickTerm }}'; liveSearch()"
                                class="px-2.5 py-1 rounded-full bg-zinc-100 hover:bg-amber-50 dark:bg-zinc-800 dark:hover:bg-amber-950/40 text-zinc-700 dark:text-zinc-300 hover:text-amber-800 dark:hover:text-amber-300 border border-zinc-200/80 dark:border-zinc-700 hover:border-amber-300 dark:hover:border-amber-700 transition font-medium">
                            {{ $quickTerm }}
                        </button>
                    @endforeach
                </div>

                <div x-show="activeCategory || (searchQuery && searchQuery.trim().length > 0)" 
                     class="flex items-center gap-2" 
                     style="{{ (!request('buscar') && !request('categoria')) ? 'display: none;' : '' }}">
                    <button type="button" 
                            @click.prevent="searchQuery = ''; clearLiveSearch(); filterCategory('', '{{ route('catalog.index') }}#catalogo')" 
                            class="group inline-flex items-center gap-1.5 text-xs font-semibold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/40 border border-rose-200 dark:border-rose-800 px-3.5 py-1.5 rounded-full transition-all duration-200 cursor-pointer shadow-sm">
                        <span>Limpiar</span>
                        <svg class="w-3.5 h-3.5 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Carrusel Horizontal de Categorías con Conteo Activo (Navegación Instantánea con 0ms Latencia) -->
        <nav class="flex gap-2 overflow-x-auto max-w-full pb-3 sm:pb-4 mb-6 sm:mb-8 no-scrollbar items-center">
            <a href="{{ route('catalog.index', array_filter(['buscar' => request('buscar')])) }}#catalogo" 
               @click.prevent="filterCategory('', '{{ route('catalog.index', array_filter(['buscar' => request('buscar')])) }}#catalogo')"
               :class="activeCategory === '' ? 'bg-zinc-900 dark:bg-amber-500 text-white dark:text-zinc-950 shadow-md ring-2 ring-amber-500/20' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 border border-zinc-200/90 dark:border-zinc-700'"
               class="px-3.5 sm:px-5 py-2 sm:py-2.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all duration-200 cursor-pointer shadow-sm shrink-0">
                Todos los productos
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('catalog.index', array_filter(['categoria' => $cat->slug, 'buscar' => request('buscar')])) }}#catalogo" 
                   @click.prevent="filterCategory('{{ $cat->slug }}', '{{ route('catalog.index', array_filter(['categoria' => $cat->slug, 'buscar' => request('buscar')])) }}#catalogo')"
                   :class="activeCategory === '{{ $cat->slug }}' ? 'bg-zinc-900 dark:bg-amber-500 text-white dark:text-zinc-950 shadow-md ring-2 ring-amber-500/20' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 border border-zinc-200/90 dark:border-zinc-700'"
                   class="px-3.5 sm:px-5 py-2 sm:py-2.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all duration-200 cursor-pointer shadow-sm shrink-0">
                    {{ $cat->name }}
                    <span class="ml-1 opacity-75 font-normal">({{ $cat->products_count }})</span>
                </a>
            @endforeach
        </nav>

        <!-- Contenedor Reactivo de Grilla de Productos -->
        <div id="products-grid-container" class="relative min-h-[360px]">
            <!-- Skeleton/Cargador sutil de transición de categorías -->
            <div x-show="isFiltering" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 bg-white/70 dark:bg-zinc-950/70 backdrop-blur-[2px] z-20 flex items-center justify-center rounded-3xl"
                 style="display: none;">
                <div class="flex items-center gap-3 bg-zinc-900 dark:bg-zinc-800 text-white px-5 py-2.5 rounded-full shadow-2xl border border-zinc-700">
                    <svg class="animate-spin h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-xs font-semibold tracking-wide">Cargando categoría...</span>
                </div>
            </div>

            <div id="products-grid-inner">
                @include('catalog.partials.product_grid', ['products' => $products, 'getProductImage' => $getProductImage])
            </div>
        </div>
    </main>

    <!-- SECCIÓN CÓMO COTIZAR (GUÍA COMPLETA, CAJONES ELEGANTES Y MODO OSCURO CON LUZ SPOTLIGHT INTERACTIVA) -->
    <section id="como-cotizar"
             x-data="{
                 mouseX: 50,
                 mouseY: 45,
                 spotlightActive: false,
                 handleMouseMove(e) {
                     const rect = e.currentTarget.getBoundingClientRect();
                     this.mouseX = ((e.clientX - rect.left) / rect.width) * 100;
                     this.mouseY = ((e.clientY - rect.top) / rect.height) * 100;
                     this.spotlightActive = true;
                 }
             }"
             @mousemove="handleMouseMove($event)"
             @mouseleave="spotlightActive = false"
             class="py-20 sm:py-24 bg-white dark:bg-zinc-950 border-t border-b border-zinc-200/80 dark:border-zinc-800 scroll-mt-16 relative overflow-hidden">
        <!-- 1. Luz Spotlight Interactiva que sigue el cursor -->
        <div class="pointer-events-none absolute inset-0 transition-opacity duration-500"
             :class="spotlightActive ? 'opacity-100' : 'opacity-50'"
             :style="`background: radial-gradient(620px circle at ${mouseX}% ${mouseY}%, rgba(234, 183, 89, 0.16), rgba(16, 185, 129, 0.05) 45%, transparent 75%);`">
        </div>

        <!-- 2. Orbes Flotantes de Luz Ambiental -->
        <div class="hero-orb-1 pointer-events-none absolute -top-24 right-1/4 w-72 h-72 sm:w-96 sm:h-96 rounded-full bg-amber-400/12 dark:bg-amber-500/10 blur-3xl"></div>
        <div class="hero-orb-2 pointer-events-none absolute -bottom-24 left-1/4 w-72 h-72 sm:w-96 sm:h-96 rounded-full bg-emerald-400/10 dark:bg-teal-500/10 blur-3xl"></div>

        <!-- 3. Retícula Arquitectónica con Máscara Radial -->
        <div class="hero-grid-mask absolute inset-0 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Encabezado Editorial Fiel al Contenido del Cliente -->
            <div class="text-center max-w-3xl mx-auto mb-16 sm:mb-20">
                <div class="inline-flex flex-wrap items-center justify-center gap-2 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 px-3.5 sm:px-4 py-1.5 rounded-full mb-4 shadow-sm max-w-full">
                    <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse shrink-0"></span>
                    <span class="text-[10px] sm:text-xs font-mono font-medium tracking-wider sm:tracking-widest uppercase text-amber-800 dark:text-amber-400">
                        CELEBRA CON ESTILO &bull; TU EVENTO, NUESTRO MOBILIARIO
                    </span>
                </div>

                <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-light text-zinc-900 dark:text-white tracking-tight leading-tight">
                    Guía Completa: <span class="italic font-normal text-amber-700 dark:text-amber-500">Cómo Cotizar Mobiliario</span> para tu Evento
                </h2>

                <p class="text-xs sm:text-sm md:text-base text-zinc-600 dark:text-zinc-400 mt-4 font-light leading-relaxed max-w-2xl mx-auto">
                    ¿Buscas la manera de cómo cotizar mobiliario para tu próximo evento? En <span class="font-medium text-zinc-900 dark:text-white">{{ $settings->site_name ?? 'Arriendos & Eventos' }}</span> te ayudamos a hacerlo fácil y rápido. Nuestro servicio incluye una amplia variedad de mobiliario para eventos, perfecto para cualquier ocasión. Ya sea que necesites sillas, mesas o decoración, nuestro equipo está listo para asistirte. Consulta cómo cotizar mobiliario con nosotros y haz de tu celebración una experiencia inolvidable.
                </p>
            </div>

            <!-- Cajones de Pasos del Proceso (5 Cajones Bonitos & Ordenados con Revelado y Borde Luminoso) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7">
                <!-- Cajón 1: COTIZA -->
                <div data-reveal class="relative overflow-hidden bg-zinc-50/80 dark:bg-zinc-900/90 rounded-3xl p-6 sm:p-8 border border-zinc-200/90 dark:border-zinc-800 hover:border-amber-500/60 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-amber-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div>
                        <div class="flex items-center justify-between mb-5">
                            <span class="text-xs font-mono font-bold tracking-widest text-amber-700 dark:text-amber-400 bg-amber-100/70 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/80 px-3 py-1 rounded-full uppercase">
                                Paso 01
                            </span>
                            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            </div>
                        </div>

                        <h3 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white mb-4 tracking-wide group-hover:text-amber-700 dark:group-hover:text-amber-400 transition-colors">
                            COTIZA
                        </h3>

                        <ul class="space-y-3 text-xs sm:text-sm text-zinc-600 dark:text-zinc-300 font-light leading-relaxed">
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mt-2 shrink-0"></span>
                                <span>Primero debes elegir los productos y cantidades que requiera tu evento.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mt-2 shrink-0"></span>
                                <span>Indica Dirección, Fecha y si existe alguna restricción en la entrega y retiro.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mt-2 shrink-0"></span>
                                <span>Haz llegar tu solicitud a nuestro equipo agregando al carrito y completando el formulario o también vía WhatsApp o correo.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-6 pt-4 border-t border-zinc-200/70 dark:border-zinc-800 text-xs font-semibold text-amber-700 dark:text-amber-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>¡Te daremos respuesta lo más pronto posible!</span>
                    </div>
                </div>

                <!-- Cajón 2: ABONA -->
                <div data-reveal class="relative overflow-hidden bg-zinc-50/80 dark:bg-zinc-900/90 rounded-3xl p-6 sm:p-8 border border-zinc-200/90 dark:border-zinc-800 hover:border-emerald-500/60 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-emerald-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div>
                        <div class="flex items-center justify-between mb-5">
                            <span class="text-xs font-mono font-bold tracking-widest text-emerald-700 dark:text-emerald-400 bg-emerald-100/70 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 px-3 py-1 rounded-full uppercase">
                                Paso 02
                            </span>
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                        </div>

                        <h3 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white mb-4 tracking-wide group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">
                            ABONA
                        </h3>

                        <ul class="space-y-3 text-xs sm:text-sm text-zinc-600 dark:text-zinc-300 font-light leading-relaxed">
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2 shrink-0"></span>
                                <span>Una vez que estés de acuerdo con la cotización solo faltará que formalicemos la reserva.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2 shrink-0"></span>
                                <span>Se solicitará un abono previo para reservar el mobiliario el cual servirá como valor de garantía.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2 shrink-0"></span>
                                <span>El valor total del arriendo deberá cancelarse como último plazo contra entrega.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-6 pt-4 border-t border-zinc-200/70 dark:border-zinc-800 text-xs font-semibold text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Reserva formal asegurada</span>
                    </div>
                </div>

                <!-- Cajón 3: RECIBE -->
                <div data-reveal class="relative overflow-hidden bg-zinc-50/80 dark:bg-zinc-900/90 rounded-3xl p-6 sm:p-8 border border-zinc-200/90 dark:border-zinc-800 hover:border-sky-500/60 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-sky-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div>
                        <div class="flex items-center justify-between mb-5">
                            <span class="text-xs font-mono font-bold tracking-widest text-sky-700 dark:text-sky-400 bg-sky-100/70 dark:bg-sky-950/60 border border-sky-200 dark:border-sky-800/80 px-3 py-1 rounded-full uppercase">
                                Paso 03
                            </span>
                            <div class="w-12 h-12 rounded-2xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-500/20 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                            </div>
                        </div>

                        <h3 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white mb-4 tracking-wide group-hover:text-sky-700 dark:group-hover:text-sky-400 transition-colors">
                            RECIBE
                        </h3>

                        <ul class="space-y-3 text-xs sm:text-sm text-zinc-600 dark:text-zinc-300 font-light leading-relaxed">
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-500 mt-2 shrink-0"></span>
                                <span>Previo al evento nos pondremos en contacto para coordinar la entrega.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-500 mt-2 shrink-0"></span>
                                <span>Es importante que alguien nos reciba para contabilizar el arriendo.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-500 mt-2 shrink-0"></span>
                                <span>Te entregaremos todo el mobiliario ordenado y apilado.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-500 mt-2 shrink-0"></span>
                                <span>Recuerda informar si existen dificultades en la descarga.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-6 pt-4 border-t border-zinc-200/70 dark:border-zinc-800 text-xs font-semibold text-sky-700 dark:text-sky-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                        <span>Entrega ordenada y coordinada</span>
                    </div>
                </div>

                <!-- Cajón 4: DEVUELVE -->
                <div data-reveal class="relative overflow-hidden bg-zinc-50/80 dark:bg-zinc-900/90 rounded-3xl p-6 sm:p-8 border border-zinc-200/90 dark:border-zinc-800 hover:border-violet-500/60 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-violet-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div>
                        <div class="flex items-center justify-between mb-5">
                            <span class="text-xs font-mono font-bold tracking-widest text-violet-700 dark:text-violet-400 bg-violet-100/70 dark:bg-violet-950/60 border border-violet-200 dark:border-violet-800/80 px-3 py-1 rounded-full uppercase">
                                Paso 04
                            </span>
                            <div class="w-12 h-12 rounded-2xl bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center border border-violet-500/20 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </div>
                        </div>

                        <h3 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white mb-4 tracking-wide group-hover:text-violet-700 dark:group-hover:text-violet-400 transition-colors">
                            DEVUELVE
                        </h3>

                        <ul class="space-y-3 text-xs sm:text-sm text-zinc-600 dark:text-zinc-300 font-light leading-relaxed">
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-violet-500 mt-2 shrink-0"></span>
                                <span>Nuestro equipo se contactará previamente para coordinar el retiro.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-violet-500 mt-2 shrink-0"></span>
                                <span>Es importante que haya alguien encargado de la devolución para contabilizar los productos.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-violet-500 mt-2 shrink-0"></span>
                                <span>Recuerda que el mobiliario debe estar ordenado, apilado y en el mismo lugar que fue la entrega.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-6 pt-4 border-t border-zinc-200/70 dark:border-zinc-800 text-xs font-semibold text-violet-700 dark:text-violet-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-violet-500"></span>
                        <span>Retiro puntual en el lugar</span>
                    </div>
                </div>

                <!-- Cajón 5: GARANTÍA -->
                <div data-reveal class="relative overflow-hidden bg-zinc-50/80 dark:bg-zinc-900/90 rounded-3xl p-6 sm:p-8 border border-zinc-200/90 dark:border-zinc-800 hover:border-teal-500/60 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-teal-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div>
                        <div class="flex items-center justify-between mb-5">
                            <span class="text-xs font-mono font-bold tracking-widest text-teal-700 dark:text-teal-400 bg-teal-100/70 dark:bg-teal-950/60 border border-teal-200 dark:border-teal-800/80 px-3 py-1 rounded-full uppercase">
                                Paso 05
                            </span>
                            <div class="w-12 h-12 rounded-2xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center border border-teal-500/20 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>

                        <h3 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white mb-4 tracking-wide group-hover:text-teal-700 dark:group-hover:text-teal-400 transition-colors">
                            GARANTÍA
                        </h3>

                        <ul class="space-y-3 text-xs sm:text-sm text-zinc-600 dark:text-zinc-300 font-light leading-relaxed">
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-teal-500 mt-2 shrink-0"></span>
                                <span>Para finalizar se le informarán las mermas del evento junto con su valor de reposición.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-teal-500 mt-2 shrink-0"></span>
                                <span>Se descontará de la garantía las mermas existentes.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-teal-500 mt-2 shrink-0"></span>
                                <span>El saldo restante se devolverá mediante transferencia electrónica directa.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-6 pt-4 border-t border-zinc-200/70 dark:border-zinc-800 text-xs font-semibold text-teal-700 dark:text-teal-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                        <span>Devolución rápida por transferencia</span>
                    </div>
                </div>

                <!-- Tarjeta Destacada Extra: Soporte Directo -->
                <div data-reveal class="bg-gradient-to-br from-amber-500/10 via-amber-600/5 to-transparent dark:from-amber-950/30 dark:via-zinc-900 dark:to-zinc-900 rounded-3xl p-6 sm:p-8 border border-amber-300/80 dark:border-amber-700/60 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-500 text-zinc-950 flex items-center justify-center font-bold text-xl shadow-md mb-5">
                            ?
                        </div>
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-amber-800 dark:text-amber-400 block mb-1">Asesoría Inmediata</span>
                        <h3 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white mb-2.5">¿Dudas con tu cubicación?</h3>
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-300 font-light leading-relaxed">
                            Si no estás seguro de cuántas mesas, copas o vajilla necesitas para tus invitados, escríbenos directamente y un asesor te armará la propuesta a la medida.
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-amber-200/60 dark:border-zinc-800">
                        <a href="https://wa.me/{{ $settings->cleanWhatsappPhone() }}?text={{ rawurlencode('Hola, quisiera asesoría para cotizar mobiliario para mi evento.') }}" 
                           target="_blank" 
                           class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 transition">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Hablar con un asesor en WhatsApp &rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Gran Banner de Valor: ¡NOSOTROS LAVAMOS POR TI! -->
            <div class="mt-10 sm:mt-16 bg-gradient-to-r from-zinc-900 via-zinc-950 to-zinc-900 text-white rounded-3xl p-5 sm:p-12 border border-zinc-800 shadow-2xl relative overflow-hidden group">
                <!-- Efecto de resplandor dorado -->
                <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-amber-500/10 blur-3xl pointer-events-none group-hover:bg-amber-500/20 transition-all duration-700"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6 sm:gap-8">
                    <div class="max-w-2xl min-w-0">
                        <div class="inline-flex flex-wrap items-center gap-1.5 sm:gap-2 bg-amber-500/10 border border-amber-500/30 text-amber-300 px-3 py-1 rounded-full text-[10px] sm:text-xs font-mono uppercase tracking-wider mb-4 max-w-full">
                            <span>✨ Cero Preocupaciones</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                            <span>Higiene & Desinfección Certificada</span>
                        </div>

                        <h3 class="font-serif text-2xl sm:text-4xl md:text-5xl font-light text-white tracking-wide break-words">
                            ¡NOSOTROS <span class="font-bold text-amber-400">LAVAMOS POR TI!</span>
                        </h3>

                        <p class="text-sm sm:text-base text-zinc-300 font-light leading-relaxed mt-4">
                            En nuestro servicio de alquiler de mobiliario, nos encargamos del lavado de todo el mobiliario para que tú solo disfrutes de la fiesta. Relájate y nosotros nos ocupamos de los detalles.
                        </p>

                        <p class="text-xs sm:text-sm text-amber-200/90 font-medium mt-2">
                            ¡Haz de tu evento una experiencia inolvidable!
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row lg:flex-col gap-3 shrink-0">
                        <a href="#catalogo" 
                           class="hero-btn-shimmer inline-flex items-center justify-center gap-2.5 bg-amber-500 hover:bg-amber-400 text-zinc-950 font-bold px-7 py-3.5 rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 text-xs sm:text-sm active:scale-98">
                            <svg class="w-4 h-4 text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span>Armar Cotización Ahora</span>
                        </a>
                        <a href="https://wa.me/{{ $settings->cleanWhatsappPhone() }}?text={{ rawurlencode('Hola, leí la guía de cómo cotizar y quisiera consultar por mobiliario.') }}" 
                           target="_blank"
                           class="inline-flex items-center justify-center gap-2.5 bg-zinc-800 hover:bg-zinc-700 text-white font-medium px-6 py-3.5 rounded-2xl border border-zinc-700 transition text-xs sm:text-sm">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Consultar por WhatsApp</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN DE ENVÍOS Y LOGÍSTICA EDITORIAL CON LUZ SPOTLIGHT INTERACTIVA -->
    <section id="envios"
             x-data="{
                 mouseX: 50,
                 mouseY: 50,
                 spotlightActive: false,
                 handleMouseMove(e) {
                     const rect = e.currentTarget.getBoundingClientRect();
                     this.mouseX = ((e.clientX - rect.left) / rect.width) * 100;
                     this.mouseY = ((e.clientY - rect.top) / rect.height) * 100;
                     this.spotlightActive = true;
                 }
             }"
             @mousemove="handleMouseMove($event)"
             @mouseleave="spotlightActive = false"
             class="relative overflow-hidden py-20 sm:py-24 bg-zinc-100/70 dark:bg-zinc-900/60 border-t border-b border-zinc-200/80 dark:border-zinc-800 scroll-mt-16">
        <!-- 1. Luz Spotlight Interactiva que sigue el cursor -->
        <div class="pointer-events-none absolute inset-0 transition-opacity duration-500"
             :class="spotlightActive ? 'opacity-100' : 'opacity-50'"
             :style="`background: radial-gradient(600px circle at ${mouseX}% ${mouseY}%, rgba(234, 183, 89, 0.17), rgba(200, 147, 51, 0.06) 45%, transparent 75%);`">
        </div>

        <!-- 2. Orbes Flotantes de Luz Ambiental y Retícula -->
        <div class="hero-orb-2 pointer-events-none absolute -top-24 left-1/3 w-80 h-80 rounded-full bg-amber-400/12 dark:bg-amber-500/10 blur-3xl"></div>
        <div class="hero-grid-mask absolute inset-0 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-[10px] sm:text-xs font-mono font-semibold uppercase tracking-widest text-amber-800 dark:text-amber-400 bg-amber-100/70 dark:bg-amber-950/40 px-3 py-1 rounded-full border border-amber-200/80 dark:border-amber-800/60 inline-block mb-3">
                    Logística & Cobertura
                </span>
                <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-light text-zinc-900 dark:text-white tracking-tight">
                    {{ $settings->shipping_title ?? 'Políticas de Envíos y Logística' }}
                </h2>
                <p class="text-zinc-600 dark:text-zinc-400 text-xs sm:text-sm font-light mt-3 max-w-2xl mx-auto leading-relaxed">
                    {{ $settings->shipping_subtitle ?? 'Coordinación profesional de transporte y retiro en terreno para que tu evento transcurra con total serenidad y puntualidad.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Tarjeta 1: Tarifa por Comuna y Volumen -->
                <div data-reveal class="group relative overflow-hidden bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-7 border border-zinc-200/80 dark:border-zinc-800 hover:border-amber-500/60 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-amber-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-800/60 text-amber-800 dark:text-amber-400 flex items-center justify-center mb-5 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        </div>
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 block mb-1">01. Tarifa & Volumen</span>
                        <h3 class="font-serif text-lg sm:text-xl font-bold text-zinc-900 dark:text-white group-hover:text-amber-700 dark:group-hover:text-amber-400 transition-colors mb-2.5">Flete por Zona y Capacidad</h3>
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-light leading-relaxed">
                            {{ $settings->shipping_zones_info ?? 'Despachamos a todas las comunas del Gran Santiago y alrededores. La tarifa se cotiza según volumen del pedido y distancia desde nuestra bodega.' }}
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center gap-1.5 text-[11px] text-amber-800 dark:text-amber-400 font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Furgones & Camiones Cerrados
                    </div>
                </div>

                <!-- Tarjeta 2: Condiciones de Descarga -->
                <div data-reveal class="group relative overflow-hidden bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-7 border border-zinc-200/80 dark:border-zinc-800 hover:border-amber-500/60 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-amber-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-800/60 text-amber-800 dark:text-amber-400 flex items-center justify-center mb-5 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 block mb-1">02. Descarga</span>
                        <h3 class="font-serif text-lg sm:text-xl font-bold text-zinc-900 dark:text-white group-hover:text-amber-700 dark:group-hover:text-amber-400 transition-colors mb-2.5">Acceso y Primer Piso</h3>
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-light leading-relaxed">
                            {{ $settings->shipping_unload_rules ?? 'Entrega y retiro a nivel de calle o primer piso (máximo 20 metros desde el camión). Escaleras o montacargas deben informarse previamente.' }}
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center gap-1.5 text-[11px] text-amber-800 dark:text-amber-400 font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Máx. 20m desde vehículo
                    </div>
                </div>

                <!-- Tarjeta 3: Instalación Técnica Obligatoria -->
                <div data-reveal class="group relative overflow-hidden bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-7 border border-zinc-200/80 dark:border-zinc-800 hover:border-amber-500/60 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-amber-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-800/60 text-amber-800 dark:text-amber-400 flex items-center justify-center mb-5 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 block mb-1">03. Montaje</span>
                        <h3 class="font-serif text-lg sm:text-xl font-bold text-zinc-900 dark:text-white group-hover:text-amber-700 dark:group-hover:text-amber-400 transition-colors mb-2.5">Instalación Certificada</h3>
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-light leading-relaxed">
                            {{ $settings->shipping_setup_rules ?? 'Carpas estructurales y equipamiento gastronómico a gas son montados exclusivamente por nuestro personal técnico especializado.' }}
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center gap-1.5 text-[11px] text-amber-800 dark:text-amber-400 font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Protocolo de Seguridad a Gas
                    </div>
                </div>

                <!-- Tarjeta 4: Recepción y Retiro con Guía -->
                <div data-reveal class="group relative overflow-hidden bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-7 border border-zinc-200/80 dark:border-zinc-800 hover:border-amber-500/60 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-amber-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-800/60 text-amber-800 dark:text-amber-400 flex items-center justify-center mb-5 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 block mb-1">04. Coordinación</span>
                        <h3 class="font-serif text-lg sm:text-xl font-bold text-zinc-900 dark:text-white group-hover:text-amber-700 dark:group-hover:text-amber-400 transition-colors mb-2.5">Conteo en Terreno</h3>
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-light leading-relaxed">
                            {{ $settings->shipping_schedule_rules ?? 'Conteo conjunto al entregar y retirar el día hábil posterior al evento mediante guía foliada y firma responsable.' }}
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center gap-1.5 text-[11px] text-amber-800 dark:text-amber-400 font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Guía de Despacho Foliada
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN DE PREGUNTAS FRECUENTES (FAQ) -->
    <section id="preguntas-frecuentes" class="py-20 sm:py-24 bg-zinc-50 dark:bg-zinc-950 scroll-mt-16" x-data="{ activeFaq: null }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-[10px] sm:text-xs font-mono font-semibold uppercase tracking-widest text-zinc-500 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-900 px-3 py-1 rounded-full border border-zinc-200 dark:border-zinc-800 inline-block mb-3">
                    Transparencia & Servicio
                </span>
                <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-light text-zinc-900 dark:text-white tracking-tight">
                    Preguntas <span class="italic font-normal text-amber-700 dark:text-amber-500">Frecuentes</span>
                </h2>
                <p class="text-zinc-600 dark:text-zinc-400 text-xs sm:text-sm font-light mt-3 max-w-xl mx-auto leading-relaxed">
                    Todo lo que necesitas saber sobre garantías, lavado de vajilla, condiciones de entrega y arriendo por evento completo.
                </p>
            </div>

            <div class="space-y-3.5">
                @forelse($faqs ?? [] as $faq)
                    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/90 dark:border-zinc-800 shadow-sm transition-all duration-200 overflow-hidden"
                         :class="{ 'ring-2 ring-zinc-900/10 dark:ring-amber-400/20 border-zinc-300 dark:border-zinc-700': activeFaq === {{ $faq->id }} }">
                        <button type="button" 
                                @click="activeFaq = (activeFaq === {{ $faq->id }} ? null : {{ $faq->id }})"
                                class="w-full text-left px-4 sm:px-6 py-4 sm:py-5 flex items-center justify-between gap-3 sm:gap-4 hover:bg-zinc-50/70 dark:hover:bg-zinc-800/70 transition">
                            <div class="flex flex-col items-start sm:flex-row sm:items-center gap-1.5 sm:gap-4 min-w-0">
                                <span class="text-[9px] sm:text-[10px] uppercase font-mono font-semibold tracking-wider px-2.5 py-0.5 sm:py-1 rounded-full bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-400 border border-amber-200/70 dark:border-amber-800/70 shrink-0">
                                    {{ $faq->category }}
                                </span>
                                <span class="font-serif text-sm sm:text-base md:text-lg font-bold text-zinc-900 dark:text-white leading-snug break-words">
                                    {{ $faq->question }}
                                </span>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0 text-zinc-600 dark:text-zinc-400 transition-transform duration-300"
                                 :class="{ 'rotate-180 bg-zinc-900 dark:bg-zinc-700 text-white': activeFaq === {{ $faq->id }} }">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </button>
                        <div x-show="activeFaq === {{ $faq->id }}" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="px-5 sm:px-6 pb-6 pt-1 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-light leading-relaxed border-t border-zinc-100 dark:border-zinc-800"
                             style="display: none;">
                            {{ $faq->answer }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 text-zinc-400 dark:text-zinc-500 text-xs">
                        No hay preguntas frecuentes registradas por el momento.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- ZONA DE CONTACTO COMPLETA CON LUZ SPOTLIGHT INTERACTIVA -->
    <section id="contacto"
             x-data="{
                 mouseX: 50,
                 mouseY: 45,
                 spotlightActive: false,
                 handleMouseMove(e) {
                     const rect = e.currentTarget.getBoundingClientRect();
                     this.mouseX = ((e.clientX - rect.left) / rect.width) * 100;
                     this.mouseY = ((e.clientY - rect.top) / rect.height) * 100;
                     this.spotlightActive = true;
                 }
             }"
             @mousemove="handleMouseMove($event)"
             @mouseleave="spotlightActive = false"
             class="relative overflow-hidden py-20 sm:py-24 bg-white dark:bg-zinc-950 border-t border-zinc-200/80 dark:border-zinc-800 scroll-mt-16">
        <!-- 1. Luz Spotlight Interactiva que sigue el cursor -->
        <div class="pointer-events-none absolute inset-0 transition-opacity duration-500"
             :class="spotlightActive ? 'opacity-100' : 'opacity-50'"
             :style="`background: radial-gradient(640px circle at ${mouseX}% ${mouseY}%, rgba(234, 183, 89, 0.16), rgba(16, 185, 129, 0.06) 45%, transparent 75%);`">
        </div>

        <!-- 2. Orbes Flotantes de Luz Ambiental y Retícula -->
        <div class="hero-orb-1 pointer-events-none absolute -top-24 left-1/4 w-80 h-80 sm:w-96 sm:h-96 rounded-full bg-amber-400/12 dark:bg-amber-500/10 blur-3xl"></div>
        <div class="hero-orb-2 pointer-events-none absolute -bottom-24 right-1/4 w-80 h-80 sm:w-96 sm:h-96 rounded-full bg-emerald-400/10 dark:bg-emerald-500/10 blur-3xl"></div>
        <div class="hero-grid-mask absolute inset-0 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-[10px] sm:text-xs font-mono font-semibold uppercase tracking-widest text-amber-800 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-3 py-1 rounded-full border border-amber-200 dark:border-amber-800/60 inline-block mb-3">
                    Canales de Comunicación
                </span>
                <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-light text-zinc-900 dark:text-white tracking-tight">
                    Contacto & <span class="italic font-normal text-amber-700 dark:text-amber-500">Cotizaciones</span>
                </h2>
                <p class="text-zinc-600 dark:text-zinc-400 text-xs sm:text-sm font-light mt-3 leading-relaxed">
                    Cuéntanos la fecha y ubicación de tu evento. Te responderemos a la brevedad con la asesoría y disponibilidad técnica necesaria.
                </p>
            </div>

            <!-- Bloque Superior: 2 Columnas (Info Oficial + Formulario Reactivo) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 lg:gap-12 items-start">
                
                <!-- Izquierda: Información Oficial de la Empresa -->
                <div class="min-w-0 lg:col-span-5 bg-zinc-900 dark:bg-zinc-900/90 text-zinc-100 rounded-3xl p-5 sm:p-8 lg:p-10 shadow-xl border border-zinc-800 flex flex-col justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 bg-zinc-800/80 border border-zinc-700 px-3 py-1 rounded-full text-[10px] font-mono tracking-wider uppercase text-amber-300 mb-5 sm:mb-6">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Atención Central & Bodega
                        </div>
                        <h3 class="font-serif text-2xl sm:text-3xl font-light tracking-wide text-white mb-4">
                            Visítanos o comunícate directamente
                        </h3>
                        <p class="text-zinc-400 text-xs sm:text-sm font-light leading-relaxed mb-7 sm:mb-8">
                            Disponemos de showroom de vajilla, mantelería y mobiliario previa coordinación, para degustaciones y pruebas de ambientación de bodas y galas.
                        </p>

                        <!-- Items de Contacto con Números Dinámicos -->
                        <div class="space-y-5 sm:space-y-6 text-xs sm:text-sm">
                            <div class="flex items-start gap-3.5 sm:gap-4 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-center shrink-0 text-amber-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1 break-words">
                                    <span class="text-[10px] font-mono text-zinc-400 uppercase tracking-widest block">Dirección Matriz</span>
                                    <span class="text-zinc-200 font-medium">{{ $settings->company_address ?? 'Av. El Parque 4120, Ciudad Empresarial, Huechuraba' }}</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-3.5 sm:gap-4 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-center shrink-0 text-emerald-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1 break-words">
                                    <span class="text-[10px] font-mono text-zinc-400 uppercase tracking-widest block">Teléfonos & WhatsApp</span>
                                    <a href="tel:{{ $settings->cleanTelPhone() }}" class="text-zinc-200 hover:text-white font-medium block">
                                        {{ $settings->company_phone ?? '+56 2 2987 6543' }}
                                    </a>
                                    <a href="https://wa.me/{{ $settings->cleanWhatsappPhone() }}" target="_blank" class="text-emerald-400 hover:text-emerald-300 font-mono text-xs inline-flex flex-wrap items-center gap-1.5 mt-0.5 break-all">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                                        <span>WhatsApp: {{ $settings->whatsapp_phone ?? '+56 9 1234 5678' }}</span>
                                    </a>
                                </div>
                            </div>

                            <div class="flex items-start gap-3.5 sm:gap-4 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-center shrink-0 text-amber-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1 break-words">
                                    <span class="text-[10px] font-mono text-zinc-400 uppercase tracking-widest block">Correo Electrónico</span>
                                    <a href="mailto:{{ $settings->company_email ?? 'contacto@arriendoseventos.cl' }}" class="text-zinc-200 hover:text-white font-medium break-all">
                                        {{ $settings->company_email ?? 'contacto@arriendoseventos.cl' }}
                                    </a>
                                </div>
                            </div>

                            <div class="flex items-start gap-3.5 sm:gap-4 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-center shrink-0 text-amber-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1 break-words">
                                    <span class="text-[10px] font-mono text-zinc-400 uppercase tracking-widest block">Horario de Bodega & Oficina</span>
                                    <span class="text-zinc-300 font-light text-xs leading-relaxed block">
                                        {{ $settings->business_hours ?? 'Lunes a Viernes de 09:00 a 19:00 hrs. Sábados de 09:00 a 14:00 hrs.' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Derecha: Formulario Reactivo Alpine.js con Honeypot -->
                <div class="min-w-0 lg:col-span-7 bg-zinc-50 dark:bg-zinc-900 rounded-3xl p-5 sm:p-8 lg:p-10 border border-zinc-200/90 dark:border-zinc-800 shadow-sm" x-data="contactForm()">
                    <h3 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white mb-2">Envíanos un Mensaje</h3>
                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 font-light mb-6">Completa tus datos y la dirección de tu evento para calcular cubicación y despacho.</p>

                    <!-- Mensaje de éxito -->
                    <template x-if="successMessage">
                        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs sm:text-sm flex items-start gap-3 shadow-sm">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <div>
                                <strong class="block font-semibold">¡Mensaje Enviado con Éxito!</strong>
                                <span x-text="successMessage"></span>
                            </div>
                        </div>
                    </template>

                    <!-- Error general del servidor -->
                    <template x-if="generalError">
                        <div class="mb-6 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs sm:text-sm flex items-start gap-3 shadow-sm">
                            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="generalError"></span>
                        </div>
                    </template>

                    <form @submit.prevent="submit()" class="space-y-4">
                        <!-- Campo Honeypot Oculto anti-bots -->
                        <div class="hidden" aria-hidden="true" style="display: none !important;">
                            <label for="website_hp">Website</label>
                            <input type="text" id="website_hp" name="website_hp" x-model="form.website_hp" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">Nombre Completo *</label>
                                <input type="text" 
                                       x-model="form.name" 
                                       placeholder="Ej: Macarena Valdés"
                                       class="w-full px-4 py-3 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition shadow-sm"
                                       :class="{ 'border-rose-400 ring-2 ring-rose-400/20': errors.name }">
                                <template x-if="errors.name">
                                    <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1" x-text="errors.name[0]"></p>
                                </template>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">Correo Electrónico *</label>
                                <input type="email" 
                                       x-model="form.email" 
                                       placeholder="contacto@ejemplo.cl"
                                       class="w-full px-4 py-3 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition shadow-sm"
                                       :class="{ 'border-rose-400 ring-2 ring-rose-400/20': errors.email }">
                                <template x-if="errors.email">
                                    <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1" x-text="errors.email[0]"></p>
                                </template>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">Teléfono Directo</label>
                                <input type="tel" 
                                       x-model="form.phone" 
                                       placeholder="+56 9 8765 4321"
                                       class="w-full px-4 py-3 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition shadow-sm"
                                       :class="{ 'border-rose-400 ring-2 ring-rose-400/20': errors.phone }">
                                <template x-if="errors.phone">
                                    <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1" x-text="errors.phone[0]"></p>
                                </template>
                            </div>

                            <div>
                                <div class="flex flex-wrap items-center justify-between gap-1 mb-1.5">
                                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Fecha del Evento *</label>
                                    <span class="text-[10px] text-zinc-400 dark:text-zinc-500 font-medium">Desde hoy en adelante</span>
                                </div>
                                <input type="date" 
                                       x-model="form.event_date" 
                                       min="{{ now()->format('Y-m-d') }}"
                                       max="{{ now()->addYears(2)->format('Y-m-d') }}"
                                       :min="minDate"
                                       :max="maxDate"
                                       class="w-full px-4 py-3 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition shadow-sm"
                                       :class="{ 'border-rose-400 ring-2 ring-rose-400/20': errors.event_date }">
                                <template x-if="errors.event_date">
                                    <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1" x-text="errors.event_date[0]"></p>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">Dirección o Comuna del Evento *</label>
                            <input type="text" 
                                   x-model="form.event_address" 
                                   placeholder="Ej: Av. Las Condes 12450, Centro de Eventos Los Nogales"
                                   class="w-full px-4 py-3 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition shadow-sm"
                                   :class="{ 'border-rose-400 ring-2 ring-rose-400/20': errors.event_address }">
                            <template x-if="errors.event_address">
                                <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1" x-text="errors.event_address[0]"></p>
                            </template>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">Detalle o Consulta *</label>
                            <textarea x-model="form.message" 
                                      rows="4" 
                                      placeholder="Cuéntanos cantidad aproximada de invitados, requerimientos de vajilla, manteles, carpas o mobiliario..."
                                      class="w-full px-4 py-3 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition shadow-sm"
                                      :class="{ 'border-rose-400 ring-2 ring-rose-400/20': errors.message }"></textarea>
                            <template x-if="errors.message">
                                <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1" x-text="errors.message[0]"></p>
                            </template>
                        </div>

                        <button type="submit" 
                                :disabled="isSubmitting"
                                class="w-full bg-zinc-900 hover:bg-zinc-800 dark:bg-amber-500 dark:hover:bg-amber-600 disabled:bg-zinc-300 dark:disabled:bg-zinc-800 text-white dark:text-zinc-950 font-semibold py-3.5 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-2 transition shadow-md active:scale-98">
                            <template x-if="isSubmitting">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin w-4 h-4 text-white dark:text-zinc-950" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                    <span>Enviando mensaje...</span>
                                </span>
                            </template>
                            <template x-if="!isSubmitting">
                                <span>Enviar Consulta Oficial</span>
                            </template>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Bloque Medio: Mapa Interactivo Seguro -->
            <div class="mt-16">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <span class="text-[10px] font-mono uppercase tracking-widest text-zinc-400 dark:text-zinc-500 block">Ubicación Estratégica</span>
                        <h3 class="font-serif text-xl sm:text-2xl font-bold text-zinc-900 dark:text-white">Nuestra Bodega y Showroom</h3>
                    </div>
                    <span class="text-xs text-zinc-500 dark:text-zinc-400 font-light hidden sm:inline-block">Huechuraba &bull; Conectividad Costanera Norte y Vespucio</span>
                </div>
                <div class="rounded-3xl overflow-hidden shadow-lg border border-zinc-200 dark:border-zinc-800 h-72 sm:h-96 relative bg-zinc-100 dark:bg-zinc-900">
                    <iframe 
                        src="{{ $settings->map_embed_url ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3330.822830386548!2d-70.66986682343828!3d-33.37435887342083!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9662c6f6634c44f3%3A0xbc4e5e4125b060d4!2sCiudad%20Empresarial!5e0!3m2!1ses!2scl!4v1700000000000!5m2!1ses!2scl' }}" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Mapa interactivo de ubicación de bodega">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER BOUTIQUE LUXURY (Spotlight Reactivo + Subrayado Magnético + Marca de Agua Editorial Gigante) -->
    <footer x-data="{
                mouseX: 50,
                mouseY: 50,
                spotlightActive: false,
                handleMouseMove(e) {
                    const rect = e.currentTarget.getBoundingClientRect();
                    this.mouseX = ((e.clientX - rect.left) / rect.width) * 100;
                    this.mouseY = ((e.clientY - rect.top) / rect.height) * 100;
                    this.spotlightActive = true;
                }
            }"
            @mousemove="handleMouseMove($event)"
            @mouseleave="spotlightActive = false"
            class="relative overflow-hidden bg-white dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800 mt-20 pt-16 pb-10 text-zinc-600 dark:text-zinc-400">
        <!-- Haz de luz viajero en el borde superior del Footer -->
        <div class="absolute inset-x-0 top-0 h-[2px] bg-zinc-100 dark:bg-zinc-900 overflow-hidden">
            <div class="hero-step-beam"></div>
        </div>

        <!-- Luz Spotlight Interactiva que sigue el cursor en el Footer -->
        <div class="pointer-events-none absolute inset-0 transition-opacity duration-500"
             :class="spotlightActive ? 'opacity-100' : 'opacity-40'"
             :style="`background: radial-gradient(560px circle at ${mouseX}% ${mouseY}%, rgba(234, 183, 89, 0.14), rgba(16, 185, 129, 0.05) 45%, transparent 75%);`">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Columna 1: Marca & Lema -->
                <div class="md:col-span-2 space-y-4">
                    <a href="{{ route('catalog.index') }}" class="inline-block group focus:outline-none">
                        <x-brand-logo variant="footer" :settings="$settings" />
                    </a>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-md">
                        {{ $settings->site_tagline ?? 'Productora de eventos con servicios integrales: catering, mobiliario, mesas, sillas, carpas y toldos para celebraciones inolvidables.' }}
                    </p>
                    
                    <!-- Teléfonos directos dinámicos en Footer -->
                    <div class="flex flex-wrap items-center gap-4 text-xs font-mono pt-1">
                        @if(!empty($settings?->company_phone))
                            <a href="tel:{{ $settings->cleanTelPhone() }}" class="footer-link-magnetic inline-flex items-center gap-1.5 text-zinc-700 dark:text-zinc-300 hover:text-amber-600 dark:hover:text-amber-400 transition">
                                <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>{{ $settings->company_phone }}</span>
                            </a>
                        @endif
                        @if(!empty($settings?->whatsapp_phone))
                            <a href="https://wa.me/{{ $settings->cleanWhatsappPhone() }}" target="_blank" rel="noopener noreferrer" class="footer-link-magnetic inline-flex items-center gap-1.5 text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 font-semibold transition">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.529 1.831.812 2.791.812 3.179 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.768-5.768-5.768zm9.969 5.768c0 5.518-4.482 10-10 10-1.748 0-3.385-.45-4.819-1.242l-5.181 1.356 1.381-5.048c-.902-1.492-1.381-3.21-1.381-5.066 0-5.518 4.482-10 10-10s10 4.482 10 10z"/></svg>
                                <span>WhatsApp: {{ $settings->whatsapp_phone }}</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Columna 2: Navegación Rápida con Subrayado Magnético Destacando FAQ -->
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white mb-4">
                        Navegación
                    </h5>
                    <ul class="space-y-2.5 text-xs">
                        <li>
                            <a href="#catalogo" class="footer-link-magnetic hover:text-zinc-900 dark:hover:text-white transition">
                                Catálogo de Productos
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('catalog.montajes') }}" class="footer-link-magnetic hover:text-zinc-900 dark:hover:text-white transition font-semibold text-amber-700 dark:text-amber-400 inline-flex items-center gap-1.5">
                                <span>Montajes & Puesta en Escena</span>
                                <span class="text-[9px] font-mono uppercase bg-amber-500/10 px-1 py-0.2 rounded border border-amber-500/30">Nuevo</span>
                            </a>
                        </li>
                        <li>
                            <a href="#como-cotizar" class="footer-link-magnetic hover:text-zinc-900 dark:hover:text-white transition">
                                Cómo Cotizar Mobiliario
                            </a>
                        </li>
                        <li>
                            <a href="#envios" class="footer-link-magnetic hover:text-zinc-900 dark:hover:text-white transition">
                                Políticas de Despacho & Envíos
                            </a>
                        </li>
                        <li>
                            <a href="#contacto" class="footer-link-magnetic hover:text-zinc-900 dark:hover:text-white transition">
                                Contacto & Bodega
                            </a>
                        </li>
                        <li class="pt-1.5">
                            <a href="#preguntas-frecuentes" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 dark:hover:bg-amber-900/60 text-amber-900 dark:text-amber-400 font-bold border border-amber-200/80 dark:border-amber-800/60 hover:-translate-y-0.5 transition-all shadow-sm">
                                <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Preguntas Frecuentes (FAQ)
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Columna 3: Redes (solo las configuradas), Atención & Botón Magnético Volver Arriba -->
                @php
                    $socialLinks = $settings?->socialLinks() ?? [];
                @endphp
                <div class="flex flex-col justify-between">
                    <div>
                        @if(count($socialLinks) > 0)
                            <h5 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white mb-4">
                                Síguenos
                            </h5>
                            <div class="flex items-center gap-2.5 mb-4">
                                @if(isset($socialLinks['instagram']))
                                    <a href="{{ $socialLinks['instagram'] }}" target="_blank" rel="noopener noreferrer" 
                                       title="Instagram"
                                       class="w-9 h-9 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-900 dark:hover:bg-amber-500 text-zinc-700 dark:text-zinc-300 hover:text-white dark:hover:text-zinc-950 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center border border-zinc-200 dark:border-zinc-700 shadow-sm">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    </a>
                                @endif
                                @if(isset($socialLinks['facebook']))
                                    <a href="{{ $socialLinks['facebook'] }}" target="_blank" rel="noopener noreferrer" 
                                       title="Facebook"
                                       class="w-9 h-9 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-900 dark:hover:bg-amber-500 text-zinc-700 dark:text-zinc-300 hover:text-white dark:hover:text-zinc-950 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center border border-zinc-200 dark:border-zinc-700 shadow-sm">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M9 8H6v4h3v12h5V12h3.642L18 8h-4V6.333C14 5.374 14.5 5 15.5 5H18V0h-3.808C10.595 0 9 1.583 9 4.615V8z"/></svg>
                                    </a>
                                @endif
                                @if(isset($socialLinks['tiktok']))
                                    <a href="{{ $socialLinks['tiktok'] }}" target="_blank" rel="noopener noreferrer" 
                                       title="TikTok"
                                       class="w-9 h-9 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-900 dark:hover:bg-amber-500 text-zinc-700 dark:text-zinc-300 hover:text-white dark:hover:text-zinc-950 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center border border-zinc-200 dark:border-zinc-700 shadow-sm">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64c.298-.002.595.042.88.13V9.4a6.33 6.33 0 00-1-.08A6.34 6.34 0 003 15.66a6.34 6.34 0 0010.86 4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-.04-4.52z"/></svg>
                                    </a>
                                @endif
                                <a href="https://wa.me/{{ $settings->cleanWhatsappPhone() }}" target="_blank" rel="noopener noreferrer" 
                                   title="WhatsApp"
                                   class="w-9 h-9 rounded-full bg-emerald-100 dark:bg-emerald-950/60 hover:bg-emerald-600 text-emerald-800 dark:text-emerald-300 hover:text-white hover:-translate-y-1 transition-all duration-300 flex items-center justify-center border border-emerald-200 dark:border-emerald-800 shadow-sm">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.529 1.831.812 2.791.812 3.179 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.768-5.768-5.768zm9.969 5.768c0 5.518-4.482 10-10 10-1.748 0-3.385-.45-4.819-1.242l-5.181 1.356 1.381-5.048c-.902-1.492-1.381-3.21-1.381-5.066 0-5.518 4.482-10 10-10s10 4.482 10 10z"/></svg>
                                </a>
                            </div>
                        @else
                            <h5 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white mb-4">
                                Atención Directa
                            </h5>
                            <div class="mb-4">
                                <a href="https://wa.me/{{ $settings->cleanWhatsappPhone() }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-emerald-100/90 dark:bg-emerald-950/60 hover:bg-emerald-600 text-emerald-800 dark:text-emerald-300 hover:text-white transition-all duration-300 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold shadow-sm">
                                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.529 1.831.812 2.791.812 3.179 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.768-5.768-5.768zm9.969 5.768c0 5.518-4.482 10-10 10-1.748 0-3.385-.45-4.819-1.242l-5.181 1.356 1.381-5.048c-.902-1.492-1.381-3.21-1.381-5.066 0-5.518 4.482-10 10-10s10 4.482 10 10z"/></svg>
                                    <span>Cotizar por WhatsApp</span>
                                </a>
                            </div>
                        @endif
                        <p class="text-[11px] text-zinc-400 dark:text-zinc-500">
                            Atención personalizada para cotizaciones corporativas y bodas.
                        </p>
                    </div>

                    <div class="pt-4">
                        <button type="button"
                                @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                                class="group inline-flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-zinc-600 dark:text-zinc-300 hover:text-amber-600 dark:hover:text-amber-400 bg-zinc-100/80 dark:bg-zinc-900 hover:bg-white dark:hover:bg-zinc-800 px-3.5 py-2 rounded-full border border-zinc-200 dark:border-zinc-800 hover:border-amber-500/50 shadow-sm hover:-translate-y-0.5 transition-all duration-300">
                            <span>Volver arriba</span>
                            <svg class="w-3.5 h-3.5 group-hover:-translate-y-0.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Marca de Agua Tipográfica Monumental (Luxury Editorial Watermark) -->
            <div class="pt-4 select-none pointer-events-none overflow-hidden text-center">
                <div class="font-serif font-bold uppercase tracking-[0.06em] sm:tracking-[0.16em] text-2xl sm:text-5xl md:text-7xl lg:text-8xl leading-tight sm:leading-none break-words bg-gradient-to-r from-zinc-200/70 via-amber-500/25 to-zinc-200/70 dark:from-zinc-800/70 dark:via-amber-400/25 dark:to-zinc-800/70 bg-clip-text text-transparent transition-all duration-500">
                    {{ $settings->site_name ?? 'Arriendos & Eventos' }}
                </div>
            </div>

            <!-- Copyright -->
            <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800/80 flex flex-col sm:flex-row items-center justify-between text-center sm:text-left text-[11px] text-zinc-400 dark:text-zinc-500 font-mono gap-2">
                <p>&copy; {{ date('Y') }} {{ $settings->site_name ?? 'Arriendos & Eventos' }}. Todos los derechos reservados.</p>
                <p>Menaje & Banquetería de Alta Gala &bull; Santiago, Chile</p>
            </div>
        </div>
    </footer>

    <!-- MODAL DE DETALLE DE PRODUCTO: "CELEBRA CON ESTILO" -->
    <div x-show="detailModal.open" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         @keydown.escape.window="detailModal.open = false"
         style="display: none;">
        
        <div class="fixed inset-0 bg-zinc-950/70 backdrop-blur-sm transition-opacity" 
             @click="detailModal.open = false"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div class="min-h-screen px-4 text-center flex items-center justify-center py-8">
            <div x-show="detailModal.open" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="inline-block w-full max-w-3xl max-h-[90dvh] overflow-y-auto bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-left align-middle transition-all transform rounded-3xl shadow-2xl border border-zinc-200 dark:border-zinc-800 relative z-10">
                
                <!-- Encabezado del Modal -->
                <div class="p-5 sm:p-6 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-zinc-50/70 dark:bg-zinc-900/70 sticky top-0 z-10 backdrop-blur-md">
                    <div>
                        <span class="text-[10px] font-mono uppercase tracking-[0.2em] text-amber-800 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2.5 py-0.5 rounded border border-amber-200 dark:border-amber-800/60 font-semibold">
                            Ficha de Colección
                        </span>
                        <h2 class="font-serif text-xl sm:text-2xl font-bold text-zinc-900 dark:text-white mt-1">
                            Celebra con <span class="italic font-normal text-amber-600 dark:text-amber-400">Estilo</span>
                        </h2>
                    </div>
                    <button @click="detailModal.open = false" 
                            class="w-9 h-9 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white flex items-center justify-center transition text-xl font-light">
                        &times;
                    </button>
                </div>

                <div class="p-5 sm:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 items-start">
                        <!-- Fotografía del Producto -->
                        <div :class="(detailModal.product?.image || '').includes('default-') ? 'bg-white' : 'bg-zinc-100 dark:bg-zinc-800/60'"
                             class="rounded-2xl p-2 border border-zinc-200/80 dark:border-zinc-700/60 overflow-hidden relative group">
                            <img :src="detailModal.product?.image" 
                                 :alt="detailModal.product?.name" 
                                 :class="(detailModal.product?.image || '').includes('default-') ? 'object-contain p-3 bg-white' : 'object-cover shadow-inner'"
                                 class="w-full h-64 sm:h-72 rounded-xl">
                            <span class="absolute top-4 left-4 backdrop-blur-md bg-white/90 dark:bg-zinc-900/90 text-zinc-900 dark:text-zinc-100 text-xs font-mono font-medium px-3 py-1 rounded shadow-sm border border-zinc-200 dark:border-zinc-700" 
                                  x-text="'Cód: ' + detailModal.product?.code"></span>
                            <template x-if="detailModal.product?.is_sale">
                                <span class="absolute top-4 right-4 bg-amber-400 text-zinc-950 text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded shadow-sm">
                                    Venta Directa
                                </span>
                            </template>
                        </div>

                        <!-- Detalles & Controles -->
                        <div class="flex flex-col justify-between space-y-5">
                            <div>
                                <span class="text-[10px] font-mono tracking-widest uppercase text-amber-800 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2.5 py-0.5 rounded border border-amber-200 dark:border-amber-800/60 font-semibold inline-block" 
                                      x-text="detailModal.product?.category"></span>

                                <h3 class="font-serif text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white mt-2 leading-tight" 
                                    x-text="detailModal.product?.name"></h3>

                                <div class="mt-4 pb-4 border-b border-zinc-100 dark:border-zinc-800 flex items-baseline justify-between">
                                    <div>
                                        <p class="text-3xl font-bold font-mono text-zinc-900 dark:text-white" 
                                           x-text="'$' + (detailModal.product?.price || 0).toLocaleString('es-CL')"></p>
                                        <span class="text-[11px] text-zinc-500 dark:text-zinc-400 block mt-0.5" 
                                              x-text="detailModal.product?.is_sale ? 'IVA incluido • Venta directa' : 'IVA incluido • Arriendo por evento'"></span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-mono font-bold" 
                                              :class="detailModal.product?.stock > 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                                              x-text="detailModal.product?.stock > 0 ? detailModal.product.stock + ' unidades en stock' : 'Sin stock'"></span>
                                    </div>
                                </div>

                                <!-- Selector de Cantidad -->
                                <div class="mt-5 space-y-2">
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                        Cantidad a cotizar:
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <div class="inline-flex items-center bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-1">
                                            <button @click="if (detailModal.qty > 1) detailModal.qty--" 
                                                    class="w-9 h-9 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 active:scale-95 text-zinc-700 dark:text-zinc-300 font-bold flex items-center justify-center transition">
                                                -
                                            </button>
                                            <input type="number" 
                                                   x-model.number="detailModal.qty" 
                                                   min="1" 
                                                   :max="detailModal.product?.stock || 1"
                                                   class="w-14 bg-transparent text-center font-mono font-bold text-sm text-zinc-900 dark:text-white focus:outline-none">
                                            <button @click="if (detailModal.qty < (detailModal.product?.stock || 1)) detailModal.qty++" 
                                                    class="w-9 h-9 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 active:scale-95 text-zinc-700 dark:text-zinc-300 font-bold flex items-center justify-center transition">
                                                +
                                            </button>
                                        </div>
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400 font-mono" x-text="'(Máximo disponible: ' + (detailModal.product?.stock || 0) + ')'"></span>
                                    </div>
                                </div>

                                <!-- Botón de Agregar -->
                                <div class="mt-6" x-data="{ modalAdded: false }">
                                    <button @click="addDetailToCart($event); modalAdded = true; setTimeout(() => modalAdded = false, 1200)" 
                                            :disabled="!detailModal.product || detailModal.product.stock <= 0"
                                            :class="modalAdded ? 'bg-emerald-600 text-white' : 'bg-zinc-900 dark:bg-amber-600 hover:bg-zinc-800 dark:hover:bg-amber-500 text-white'"
                                            class="w-full uppercase tracking-wider py-3.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2 shadow-md active:scale-98 disabled:bg-zinc-200 dark:disabled:bg-zinc-800 disabled:text-zinc-400 dark:disabled:text-zinc-600 disabled:cursor-not-allowed">
                                        <template x-if="!modalAdded">
                                            <span class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                <span>Agregar a la Cotización</span>
                                            </span>
                                        </template>
                                        <template x-if="modalAdded">
                                            <span class="flex items-center gap-2 font-bold">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                <span>¡Agregado a tu Lista!</span>
                                            </span>
                                        </template>
                                    </button>
                                </div>

                                <!-- Descripción -->
                                <div class="mt-5 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                                    <h4 class="text-xs font-semibold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1">Descripción:</h4>
                                    <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-300 font-light leading-relaxed" 
                                       x-text="detailModal.product?.description"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Toast de Confirmación -->
    <div x-show="toast.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed bottom-6 inset-x-0 mx-auto max-w-sm z-50 pointer-events-none px-4"
         style="display: none;">
        <div class="bg-zinc-900 dark:bg-zinc-800 text-white px-4 py-3 rounded-2xl shadow-2xl flex items-center justify-between gap-3 border border-zinc-800 dark:border-zinc-700 pointer-events-auto">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/30">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </span>
                <p class="text-xs font-medium truncate text-zinc-100" x-text="toast.message"></p>
            </div>
            <button @click="cartOpen = true; toast.show = false" class="text-xs text-amber-400 hover:text-amber-300 font-semibold underline shrink-0 transition">
                Ver cotización
            </button>
        </div>
    </div>

    <!-- DRAWER LATERAL DEL CARRITO CON TRANSICIÓN SUAVE -->
    <div x-show="cartOpen" 
         class="fixed inset-0 z-50 overflow-hidden" 
         style="display: none;">
        <!-- Fondo Oscurecido -->
        <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-sm transition-opacity" 
             @click="cartOpen = false"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <!-- Panel Lateral Deslizable -->
        <div x-show="cartOpen" 
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed inset-y-0 right-0 max-w-md w-full h-[100dvh] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 shadow-2xl flex flex-col justify-between z-50">
            
            <!-- Header del Drawer -->
            <div class="p-4 sm:p-5 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-zinc-50/70 dark:bg-zinc-900/80 shrink-0">
                <div>
                    <h2 class="font-serif font-bold text-xl sm:text-2xl text-zinc-900 dark:text-white tracking-wide">Tu Cotización</h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Revisa tus artículos antes de consultar disponibilidad</p>
                </div>
                <button @click="cartOpen = false" aria-label="Cerrar cotización" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-zinc-100 dark:hover:bg-zinc-800 transition text-2xl font-light">
                    &times;
                </button>
            </div>

            <!-- Cuerpo Scrolleable Unificado (Artículos + Formulario de Reserva cómodos en móvil) -->
            <div class="flex-1 overflow-y-auto flex flex-col justify-between">
                <!-- Alerta de Error si aplica -->
                <template x-if="errorMessage">
                    <div class="mx-4 sm:mx-5 mt-4 p-3.5 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 text-rose-700 dark:text-rose-300 text-xs rounded-xl flex items-start gap-2 shadow-sm">
                        <svg class="w-4 h-4 shrink-0 mt-0.5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span x-text="errorMessage"></span>
                    </div>
                </template>

                <!-- Listado de Artículos -->
                <div class="p-4 sm:p-5 space-y-3.5 divide-y divide-zinc-100 dark:divide-zinc-800">
                    <template x-if="cart.length === 0">
                        <div class="text-center py-12 sm:py-16 text-zinc-400 dark:text-zinc-500">
                            <svg class="w-12 h-12 mx-auto stroke-zinc-300 dark:stroke-zinc-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <p class="font-serif text-xl font-medium text-zinc-700 dark:text-zinc-300">Tu lista está vacía</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 max-w-xs mx-auto">Explora nuestro catálogo y agrega mobiliario o vajilla para comenzar.</p>
                        </div>
                    </template>

                    <template x-for="item in cart" :key="item.id">
                        <div class="pt-3.5 first:pt-0 flex items-start justify-between text-sm gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-zinc-900 dark:text-white text-xs sm:text-sm leading-snug" x-text="item.name"></p>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-mono mt-0.5" x-text="'Cód: ' + item.code + ' • $' + item.price.toLocaleString('es-CL') + ' c/u'"></p>
                                
                                <div class="flex items-center gap-2 mt-2.5">
                                    <button @click="updateQty(item.id, -1)" 
                                            class="w-7 h-7 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 active:scale-95 text-zinc-800 dark:text-zinc-200 font-bold flex items-center justify-center transition border border-zinc-200 dark:border-zinc-700 text-xs">
                                        -
                                    </button>
                                    <span class="text-xs font-mono font-bold px-1 min-w-[1.5rem] text-center text-zinc-900 dark:text-white" x-text="item.qty"></span>
                                    <button @click="updateQty(item.id, 1)" 
                                            class="w-7 h-7 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 active:scale-95 text-zinc-800 dark:text-zinc-200 font-bold flex items-center justify-center transition border border-zinc-200 dark:border-zinc-700 text-xs">
                                        +
                                    </button>
                                    <span class="text-[10px] text-zinc-400 dark:text-zinc-500 font-mono" x-text="'(Máx: ' + item.stock + ')'"></span>
                                </div>
                            </div>

                            <div class="text-right flex flex-col justify-between items-end shrink-0">
                                <p class="font-bold text-zinc-900 dark:text-white text-sm font-mono" x-text="'$' + (item.price * item.qty).toLocaleString('es-CL')"></p>
                                <button @click="removeItem(item.id)" class="text-[11px] text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-300 hover:underline mt-2.5 transition">
                                    Quitar
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Resumen y Datos del Evento -->
                <div class="p-4 sm:p-5 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-900/80 space-y-4">
                    <div class="bg-white dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 p-3.5 sm:p-4 rounded-xl space-y-2 shadow-sm">
                        <div class="flex justify-between items-center text-xs text-zinc-500 dark:text-zinc-400">
                            <span>Total de artículos:</span>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-200 font-mono" x-text="countItems() + ' unidades'"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-bold text-zinc-900 dark:text-white pt-2.5 border-t border-zinc-100 dark:border-zinc-700">
                            <span>Total Estimado:</span>
                            <span class="text-xl sm:text-2xl font-bold font-mono text-zinc-900 dark:text-white" x-text="'$' + getTotal().toLocaleString('es-CL')"></span>
                        </div>
                    </div>

                    <div class="space-y-3 pt-1 text-xs">
                        <!-- Campo Honeypot Oculto anti-bots -->
                        <div class="hidden" aria-hidden="true" style="display: none !important;">
                            <label for="quote_website_hp">Website</label>
                            <input type="text" id="quote_website_hp" name="website_hp" x-model="form.website_hp" tabindex="-1" autocomplete="off">
                        </div>

                        <label class="block font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-[10px]">Datos para la reserva:</label>
                        <div>
                            <label class="block font-medium text-zinc-700 dark:text-zinc-300 text-[11px] mb-1">Nombre y Apellido *</label>
                            <input type="text" x-model="form.name" placeholder="Tu Nombre y Apellido *" 
                                   class="w-full p-2.5 sm:p-3 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition shadow-sm">
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-medium text-zinc-700 dark:text-zinc-300 text-[11px]">Fecha del Evento <span class="text-zinc-400 font-normal">(opcional)</span></label>
                                <span class="text-[10px] text-zinc-400 dark:text-zinc-500">Mínimo hoy</span>
                            </div>
                            <input type="date" 
                                   x-model="form.date" 
                                   min="{{ now()->format('Y-m-d') }}"
                                   max="{{ now()->addYears(2)->format('Y-m-d') }}"
                                   :min="minDate"
                                   :max="maxDate"
                                   class="w-full p-2.5 sm:p-3 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition shadow-sm">
                        </div>
                        <div>
                            <label class="block font-medium text-zinc-700 dark:text-zinc-300 text-[11px] mb-1">Comuna o Lugar del Evento *</label>
                            <input type="text" x-model="form.location" placeholder="Comuna / Lugar del Evento *" 
                                   class="w-full p-2.5 sm:p-3 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition shadow-sm">
                        </div>
                    </div>

                    <!-- Alerta de Error visible en el pie del carrito -->
                    <template x-if="errorMessage">
                        <div class="p-3 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 text-rose-700 dark:text-rose-300 text-xs rounded-xl flex items-start gap-2 shadow-sm animate-pulse">
                            <svg class="w-4 h-4 shrink-0 mt-0.5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="errorMessage"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Pie Fijo CTA WhatsApp siempre visible en móvil -->
            <div class="p-4 sm:p-5 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0">
                <button @click="submitQuote()" 
                        :disabled="isSubmitting || cart.length === 0"
                        class="w-full bg-emerald-700 hover:bg-emerald-800 disabled:bg-zinc-200 dark:disabled:bg-zinc-800 disabled:text-zinc-400 dark:disabled:text-zinc-600 disabled:cursor-not-allowed text-white font-bold py-3.5 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-2.5 transition-all duration-200 shadow-md active:scale-98">
                    <template x-if="!isSubmitting">
                        <svg class="w-5 h-5 fill-current shrink-0" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.529 1.831.812 2.791.812 3.179 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.768-5.768-5.768zm9.969 5.768c0 5.518-4.482 10-10 10-1.748 0-3.385-.45-4.819-1.242l-5.181 1.356 1.381-5.048c-.902-1.492-1.381-3.21-1.381-5.066 0-5.518 4.482-10 10-10s10 4.482 10 10z"/>
                        </svg>
                    </template>
                    <span x-text="isSubmitting ? 'Procesando cotización...' : 'Solicitar Disponibilidad por WhatsApp'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Botón Flotante de WhatsApp -->
    <div class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-30">
        <a href="https://wa.me/{{ $settings->cleanWhatsappPhone() }}?text={{ rawurlencode('Hola, quisiera consultar por disponibilidad de arriendos para un evento.') }}" 
           target="_blank" 
           title="Conversar por WhatsApp"
           class="w-13 h-13 sm:w-14 sm:h-14 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full flex items-center justify-center shadow-xl shadow-emerald-900/20 hover:scale-105 active:scale-95 transition-all duration-300">
            <svg class="w-7 h-7 sm:w-8 sm:h-8 fill-current text-white" viewBox="0 0 24 24">
                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.529 1.831.812 2.791.812 3.179 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.768-5.768-5.768zm9.969 5.768c0 5.518-4.482 10-10 10-1.748 0-3.385-.45-4.819-1.242l-5.181 1.356 1.381-5.048c-.902-1.492-1.381-3.21-1.381-5.066 0-5.518 4.482-10 10-10s10 4.482 10 10z"/>
            </svg>
        </a>
    </div>

    <!-- Lógica Reactiva con Alpine.js -->
    <script>
        function contactForm() {
            return {
                form: {
                    name: '',
                    email: '',
                    phone: '',
                    event_date: '',
                    event_address: '',
                    message: '',
                    website_hp: ''
                },
                minDate: '{{ now()->format('Y-m-d') }}',
                maxDate: '{{ now()->addYears(2)->format('Y-m-d') }}',
                errors: {},
                isSubmitting: false,
                successMessage: '',
                generalError: '',

                async submit() {
                    this.errors = {};
                    this.generalError = '';
                    this.successMessage = '';

                    // Validación de coherencia de fecha en cliente
                    if (!this.form.event_date) {
                        this.errors = { event_date: ['La fecha del evento es obligatoria para verificar disponibilidad de arriendo.'] };
                        return;
                    }

                    if (this.form.event_date < this.minDate) {
                        this.errors = { event_date: ['La fecha del evento no puede ser en el pasado. Debe ser desde hoy en adelante.'] };
                        return;
                    }

                    if (this.form.event_date > this.maxDate) {
                        this.errors = { event_date: ['La fecha del evento no puede superar los 2 años en el futuro.'] };
                        return;
                    }

                    this.isSubmitting = true;

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const response = await fetch('{{ route('contact.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token || ''
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.successMessage = data.message || '¡Tu mensaje ha sido recibido con éxito! Nos contactaremos a la brevedad.';
                            this.form = {
                                name: '',
                                email: '',
                                phone: '',
                                event_date: '',
                                event_address: '',
                                message: '',
                                website_hp: ''
                            };
                        } else if (response.status === 422 && data.errors) {
                            this.errors = data.errors;
                        } else {
                            this.generalError = data.message || 'No fue posible enviar el mensaje. Por favor intenta más tarde.';
                        }
                    } catch (err) {
                        this.generalError = 'Error de conexión con el servidor. Por favor verifica tu red e intenta nuevamente.';
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            };
        }
        function quoteCart() {
            return {
                cartOpen: false,
                mobileMenuOpen: false,
                isSubmitting: false,
                errorMessage: '',
                minDate: '{{ now()->format('Y-m-d') }}',
                maxDate: '{{ now()->addYears(2)->format('Y-m-d') }}',
                toast: { show: false, message: '', timeout: null },
                isDark: document.documentElement.classList.contains('dark'),

                // Filtro Reactivo de Categorías y Carga Asíncrona (0ms de latencia con caché)
                activeCategory: '{{ addslashes(request('categoria', '')) }}',
                isFiltering: false,
                categoryCache: {},

                init() {
                    // Abrir el cajón de cotización si el cliente llega desde otra vista (ej: /como-cotizar)
                    const params = new URLSearchParams(window.location.search);
                    if (params.get('abrir_cotizacion') === '1') {
                        this.cartOpen = true;
                        params.delete('abrir_cotizacion');
                        const cleanQuery = params.toString();
                        const cleanUrl = window.location.pathname + (cleanQuery ? '?' + cleanQuery : '') + window.location.hash;
                        window.history.replaceState({}, '', cleanUrl);
                    }

                    // Interceptar clics en la paginación dentro del contenedor del catálogo
                    document.addEventListener('click', (e) => {
                        const pageLink = e.target.closest('#products-grid-container .custom-pagination a, #products-grid-container nav a');
                        if (pageLink && pageLink.href) {
                            e.preventDefault();
                            this.fetchGrid(pageLink.href);
                        }
                    });

                    // Sincronizar con botones de navegación del explorador (Atrás / Adelante)
                    window.addEventListener('popstate', () => {
                        const params = new URLSearchParams(window.location.search);
                        this.activeCategory = params.get('categoria') || '';
                        this.fetchGrid(window.location.href, false);
                    });
                },

                async filterCategory(slug, targetUrl) {
                    if (this.activeCategory === slug && !targetUrl.includes('buscar=')) {
                        return;
                    }
                    this.activeCategory = slug;
                    await this.fetchGrid(targetUrl, true);
                },

                async fetchGrid(url, pushState = true) {
                    const cacheKey = url;
                    if (this.categoryCache[cacheKey]) {
                        const container = document.getElementById('products-grid-inner');
                        if (container) {
                            container.innerHTML = this.categoryCache[cacheKey];
                        }
                        if (pushState) {
                            window.history.pushState({ url }, '', url);
                        }
                        return;
                    }

                    this.isFiltering = true;
                    try {
                        const fetchUrl = new URL(url, window.location.origin);
                        fetchUrl.searchParams.set('ajax', '1');

                        const res = await fetch(fetchUrl.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-Catalog-Filter': '1',
                                'Accept': 'application/json'
                            }
                        });

                        if (res.ok) {
                            const data = await res.json();
                            if (data && data.html) {
                                this.categoryCache[cacheKey] = data.html;
                                const container = document.getElementById('products-grid-inner');
                                if (container) {
                                    container.innerHTML = data.html;
                                }
                                if (pushState) {
                                    window.history.pushState({ url }, '', url);
                                }
                            }
                        } else {
                            window.location.href = url;
                        }
                    } catch (err) {
                        console.error('Error al filtrar catálogo:', err);
                        window.location.href = url;
                    } finally {
                        this.isFiltering = false;
                    }
                },

                toggleTheme() {
                    this.isDark = !this.isDark;
                    if (this.isDark) {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    }
                },
                
                // Modal de detalle interactivo: "Celebra con estilo"
                detailModal: {
                    open: false,
                    product: null,
                    qty: 1
                },

                // Búsqueda en tiempo real (Live Search con AbortController contra Race Conditions)
                searchQuery: '{{ addslashes(request('buscar', '')) }}',
                isSearching: false,
                searchResults: null,
                searchAbortController: null,

                clearLiveSearch() {
                    this.searchResults = null;
                    this.isSearching = false;
                    if (this.searchAbortController) {
                        this.searchAbortController.abort();
                        this.searchAbortController = null;
                    }
                },

                getLiveImage(item) {
                    if (item && item.image) return item.image;
                    const catPlaceholders = {
                        'vajilla': 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=800&q=80',
                        'manteleria': 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80',
                        'mobiliario': 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&w=800&q=80',
                        'equipamiento-cocina': 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&w=800&q=80',
                        'bar-decoracion': 'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?auto=format&fit=crop&w=800&q=80',
                    };
                    const slug = item && item.category_slug ? item.category_slug : '';
                    return catPlaceholders[slug] || 'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&fit=crop&w=800&q=80';
                },

                async liveSearch() {
                    const query = (this.searchQuery || '').trim().slice(0, 60);
                    if (query.length === 0) {
                        this.clearLiveSearch();
                        return;
                    }

                    if (this.searchAbortController) {
                        this.searchAbortController.abort();
                    }
                    this.searchAbortController = new AbortController();
                    this.isSearching = true;

                    try {
                        const url = new URL('{{ route('catalog.search') }}', window.location.origin);
                        url.searchParams.set('q', query);
                        @if(request('categoria'))
                            url.searchParams.set('categoria', '{{ request('categoria') }}');
                        @endif

                        const response = await fetch(url.toString(), {
                            signal: this.searchAbortController.signal,
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            const payload = await response.json();
                            this.searchResults = payload.data || [];
                        }
                    } catch (err) {
                        if (err.name !== 'AbortError') {
                            console.error('Error en búsqueda reactiva:', err);
                        }
                    } finally {
                        this.isSearching = false;
                    }
                },

                cart: JSON.parse(localStorage.getItem('cotizacion_arriendo') || '[]'),
                form: Object.assign({ name: '', date: '', location: '', website_hp: '' }, JSON.parse(localStorage.getItem('cotizacion_form') || '{}')),

                save() {
                    localStorage.setItem('cotizacion_arriendo', JSON.stringify(this.cart));
                    localStorage.setItem('cotizacion_form', JSON.stringify(this.form));
                },

                showToast(message) {
                    this.toast.message = message;
                    this.toast.show = true;
                    clearTimeout(this.toast.timeout);
                    this.toast.timeout = setTimeout(() => {
                        this.toast.show = false;
                    }, 3000);
                },

                trackedClicks: new Set(),

                trackProductClick(productId) {
                    if (!productId || this.trackedClicks.has(productId)) {
                        return;
                    }
                    this.trackedClicks.add(productId);

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        fetch(`/catalogo/productos/${productId}/click`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token || ''
                            },
                            keepalive: true
                        }).catch(() => {});
                    } catch (e) {}
                },

                openDetail(product) {
                    this.detailModal.product = product;
                    this.detailModal.qty = 1;
                    this.detailModal.open = true;
                    if (product && product.id) {
                        this.trackProductClick(product.id);
                    }
                },

                flyToCart(evt, imageUrl) {
                    try {
                        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                        const cartBtn = document.getElementById('open-quote-drawer-btn');
                        if (!cartBtn) return;

                        const targetRect = cartBtn.getBoundingClientRect();
                        let startX = window.innerWidth / 2;
                        let startY = window.innerHeight / 2;

                        if (evt && evt.currentTarget) {
                            const srcRect = evt.currentTarget.getBoundingClientRect();
                            startX = srcRect.left + srcRect.width / 2;
                            startY = srcRect.top + srcRect.height / 2;
                        } else if (evt && evt.clientX) {
                            startX = evt.clientX;
                            startY = evt.clientY;
                        }

                        const flyer = document.createElement('div');
                        flyer.style.position = 'fixed';
                        flyer.style.left = (startX - 26) + 'px';
                        flyer.style.top = (startY - 26) + 'px';
                        flyer.style.width = '52px';
                        flyer.style.height = '52px';
                        flyer.style.borderRadius = '9999px';
                        flyer.style.zIndex = '9999';
                        flyer.style.pointerEvents = 'none';
                        flyer.style.border = '2px solid #EAB759';
                        flyer.style.boxShadow = '0 14px 30px rgba(234, 183, 89, 0.45)';
                        flyer.style.backgroundColor = '#18181B';
                        flyer.style.backgroundSize = 'cover';
                        flyer.style.backgroundPosition = 'center';
                        if (imageUrl) {
                            flyer.style.backgroundImage = `url("${imageUrl}")`;
                        }
                        flyer.style.transition = 'transform 0.65s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.65s ease-in';

                        document.body.appendChild(flyer);

                        const deltaX = (targetRect.left + targetRect.width / 2) - startX;
                        const deltaY = (targetRect.top + targetRect.height / 2) - startY;

                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
                                flyer.style.transform = `translate3d(${deltaX}px, ${deltaY}px, 0) scale(0.22) rotate(12deg)`;
                                flyer.style.opacity = '0.25';
                            });
                        });

                        setTimeout(() => {
                            flyer.remove();
                            cartBtn.classList.remove('cart-hit-shockwave');
                            void cartBtn.offsetWidth;
                            cartBtn.classList.add('cart-hit-shockwave');
                        }, 640);
                    } catch (e) {}
                },

                addDetailToCart(evt) {
                    if (!this.detailModal.product) return;
                    const p = this.detailModal.product;
                    this.trackProductClick(p.id);
                    const qtyToAdd = Math.max(1, parseInt(this.detailModal.qty) || 1);

                    let item = this.cart.find(i => i.id === p.id);
                    if (item) {
                        const newQty = item.qty + qtyToAdd;
                        if (newQty <= p.stock) {
                            item.qty = newQty;
                            this.showToast(`Agregaste ${qtyToAdd} un. más de "${p.name}"`);
                            this.flyToCart(evt, p.image);
                        } else {
                            item.qty = p.stock;
                            this.showToast(`Ajustado al stock máximo de "${p.name}" (${p.stock} un.)`);
                        }
                    } else {
                        const initialQty = Math.min(qtyToAdd, p.stock);
                        this.cart.push({
                            id: p.id,
                            name: p.name,
                            price: p.price,
                            stock: p.stock,
                            code: p.code,
                            qty: initialQty
                        });
                        this.showToast(`"${p.name}" (${initialQty} un.) añadido a tu lista`);
                        this.flyToCart(evt, p.image);
                    }

                    this.save();
                },

                addItem(id, name, price, stock, code, evt = null, imageUrl = null) {
                    this.trackProductClick(id);
                    let item = this.cart.find(i => i.id === id);
                    if (item) {
                        if (item.qty < stock) {
                            item.qty++;
                            this.showToast(`Agregaste otra unidad de "${name}"`);
                            this.flyToCart(evt, imageUrl);
                        } else {
                            this.showToast(`Alcanzaste el stock disponible de "${name}" (${stock} un.)`);
                        }
                    } else {
                        this.cart.push({ id, name, price, stock, code, qty: 1 });
                        this.showToast(`"${name}" añadido a tu lista`);
                        this.flyToCart(evt, imageUrl);
                    }
                    this.save();
                    this.errorMessage = '';
                },

                updateQty(id, delta) {
                    let item = this.cart.find(i => i.id === id);
                    if (!item) return;

                    item.qty += delta;
                    if (item.qty > item.stock) {
                        item.qty = item.stock;
                    }
                    if (item.qty <= 0) {
                        this.removeItem(id);
                        return;
                    }
                    this.save();
                },

                removeItem(id) {
                    this.cart = this.cart.filter(i => i.id !== id);
                    this.save();
                },

                countItems() {
                    return this.cart.reduce((acc, i) => acc + i.qty, 0);
                },

                getTotal() {
                    return this.cart.reduce((acc, i) => acc + (i.price * i.qty), 0);
                },

                async submitQuote() {
                    this.errorMessage = '';

                    if (this.cart.length === 0) {
                        this.errorMessage = 'Debes agregar al menos un artículo para cotizar.';
                        return;
                    }

                    if (!this.form.name || !this.form.name.trim()) {
                        this.errorMessage = 'Por favor ingresa tu Nombre y Apellido.';
                        return;
                    }

                    if (!this.form.location || !this.form.location.trim()) {
                        this.errorMessage = 'Por favor ingresa la Comuna o Lugar del Evento.';
                        return;
                    }

                    if (this.form.date) {
                        if (this.form.date < this.minDate) {
                            this.errorMessage = 'La fecha del evento no puede ser en el pasado. Debe ser desde hoy en adelante.';
                            return;
                        }

                        if (this.form.date > this.maxDate) {
                            this.errorMessage = 'La fecha del evento no puede superar los 2 años en el futuro.';
                            return;
                        }
                    }

                    this.isSubmitting = true;
                    this.save();

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                        const response = await fetch('{{ route('quote.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token || ''
                            },
                            body: JSON.stringify({
                                website_hp: this.form.website_hp || '',
                                customer_name: this.form.name.trim(),
                                event_date: this.form.date || null,
                                location: this.form.location.trim(),
                                items: this.cart.map(item => ({
                                    product_id: item.id,
                                    quantity: item.qty
                                }))
                            })
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            if (response.status === 422 && data.errors) {
                                const firstErrorKey = Object.keys(data.errors)[0];
                                this.errorMessage = data.errors[firstErrorKey][0];
                            } else {
                                this.errorMessage = data.message || 'Ocurrió un error al procesar la cotización. Intenta nuevamente.';
                            }
                            return;
                        }

                        if (data.whatsapp_url) {
                            window.open(data.whatsapp_url, '_blank');
                        }
                    } catch (error) {
                        this.errorMessage = 'Error de conexión con el servidor. Revisa tu conexión a internet.';
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }
        }

        // === SISTEMA GLOBAL DE SCROLL REVEAL & BARRA DE PROGRESO DE LECTURA ===
        document.addEventListener('DOMContentLoaded', () => {
            const progressBar = document.getElementById('scroll-progress-bar');
            const updateScrollProgress = () => {
                if (!progressBar) return;
                const scrollTop = window.scrollY || document.documentElement.scrollTop;
                const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                const pct = docHeight > 0 ? Math.min(100, Math.max(0, (scrollTop / docHeight) * 100)) : 0;
                progressBar.style.width = pct + '%';
            };
            window.addEventListener('scroll', updateScrollProgress, { passive: true });
            updateScrollProgress();

            const revealNodes = document.querySelectorAll('[data-reveal]');
            if ('IntersectionObserver' in window && revealNodes.length > 0) {
                const observer = new IntersectionObserver((entries, obs) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-revealed');
                            obs.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

                revealNodes.forEach((node, index) => {
                    node.style.transitionDelay = ((index % 4) * 75) + 'ms';
                    observer.observe(node);
                });
            } else {
                revealNodes.forEach((node) => node.classList.add('is-revealed'));
            }
        });
    </script>
</body>
</html>