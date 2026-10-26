@php
    $categoryPlaceholders = [
        'vajilla' => 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=800&q=80',
        'manteleria' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80',
        'mobiliario' => 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&w=800&q=80',
        'equipamiento-cocina' => 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&w=800&q=80',
        'bar-decoracion' => 'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?auto=format&fit=crop&w=800&q=80',
    ];
    $defaultPlaceholder = 'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&fit=crop&w=800&q=80';

    $getProductImage = function ($item) use ($categoryPlaceholders, $defaultPlaceholder) {
        if (!empty($item->image_url)) {
            return $item->image_url;
        }
        $slug = $item->category?->slug ?? '';
        return $categoryPlaceholders[$slug] ?? $defaultPlaceholder;
    };
@endphp
<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings->site_name ?? 'Arriendos & Eventos' }} | {{ $settings->site_tagline ?? 'Banquetería, Mobiliario & Vajilla Premium' }}</title>

    <!-- Google Fonts: Cormorant Garamond (Editorial Luxury) & Plus Jakarta Sans (UI Clean) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS y Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Cormorant Garamond"', 'serif'],
                    },
                    colors: {
                        amber: {
                            50: '#FFFDF9',
                            100: '#FEF8EE',
                            200: '#FDF1DD',
                            300: '#FBE3BC',
                            400: '#F6D090',
                            500: '#EAB759',
                            600: '#C89333',
                            700: '#9E6D1C',
                            800: '#785018',
                            900: '#543612',
                        }
                    }
                }
            }
        }
    </script>
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Paginación Editorial Luxury */
        .custom-pagination nav svg { width: 1.15rem; height: 1.15rem; }
        .custom-pagination nav span, 
        .custom-pagination nav a {
            background-color: #FFFFFF !important;
            color: #27272A !important;
            border-color: #E4E4E7 !important;
            border-radius: 0.75rem !important;
            margin: 0 0.2rem !important;
            font-size: 0.8125rem !important;
            padding: 0.5rem 0.85rem !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;
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
    </style>
</head>
<body class="bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans min-h-screen flex flex-col antialiased selection:bg-amber-500 selection:text-white relative overflow-x-hidden transition-colors duration-200" 
      x-data="quoteCart()" 
      x-cloak>

    <!-- Barra de Navegación Pegajosa (Sticky Top) -->
    <header class="sticky top-0 z-40 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md border-b border-zinc-200/80 dark:border-zinc-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between gap-4">
            
            <!-- Brand Monograma Boutique -->
            <a href="{{ route('catalog.index') }}" class="group flex items-center gap-3">
                @if(!empty($settings?->logo_path))
                    <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="{{ $settings->site_name ?? 'Logo' }}" class="w-10 h-10 rounded-xl object-cover shadow-md border border-zinc-200 dark:border-zinc-700">
                @else
                    <div class="w-10 h-10 rounded-xl bg-zinc-900 dark:bg-zinc-800 text-zinc-100 flex items-center justify-center font-serif text-2xl font-bold shadow-md shadow-zinc-900/10 group-hover:scale-105 transition-transform duration-300 border border-zinc-800 dark:border-zinc-700">
                        {{ mb_substr($settings->site_name ?? 'A', 0, 1) }}
                    </div>
                @endif
                <div>
                    <span class="text-base sm:text-lg font-serif font-bold tracking-wider text-zinc-900 dark:text-zinc-100 uppercase block leading-tight">
                        {{ $settings->site_name ?? 'Arriendos & Eventos' }}
                    </span>
                    <span class="text-[9px] sm:text-[10px] tracking-[0.22em] text-zinc-500 dark:text-zinc-400 uppercase font-mono block">
                        {{ $settings->site_tagline ?? 'Boutique de Banquetería & Menaje' }}
                    </span>
                </div>
            </a>

            <!-- Enlaces de Navegación Desktop (Menú limpio: Inicio, Destacados, Catálogo, Envíos, Contacto, WhatsApp) -->
            <nav class="hidden md:flex items-center gap-6 text-xs uppercase tracking-wider font-semibold text-zinc-600 dark:text-zinc-400">
                <a href="{{ route('catalog.index') }}" class="hover:text-zinc-900 dark:hover:text-white transition-colors py-1 relative {{ !request('categoria') && !request('buscar') ? 'text-zinc-950 dark:text-white font-bold border-b-2 border-zinc-900 dark:border-amber-400' : '' }}">
                    Inicio
                </a>
                <a href="#destacados" class="hover:text-zinc-900 dark:hover:text-white transition-colors py-1">
                    Destacados
                </a>
                <a href="#catalogo" class="hover:text-zinc-900 dark:hover:text-white transition-colors py-1">
                    Catálogo
                </a>
                <a href="#envios" class="hover:text-zinc-900 dark:hover:text-white transition-colors py-1">
                    Envíos
                </a>
                <a href="#contacto" class="hover:text-zinc-900 dark:hover:text-white transition-colors py-1">
                    Contacto
                </a>
                <a href="https://wa.me/{{ $settings->cleanWhatsappPhone() }}?text={{ rawurlencode('Hola, me comunico desde el catálogo de ' . ($settings->site_name ?? 'Arriendos & Eventos') . '.') }}" 
                   target="_blank" 
                   class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors py-1 flex items-center gap-1.5 font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    WhatsApp
                </a>
            </nav>

            <!-- Acciones: Botón de Cotización, Botón Toggle Sol/Luna, Icono de Usuario y Menú Móvil -->
            <div class="flex items-center gap-2 sm:gap-2.5">
                <div x-data="{ bump: false }" 
                     x-init="$watch('countItems()', () => { bump = true; setTimeout(() => bump = false, 350) })">
                    <button @click="cartOpen = true" 
                            id="open-quote-drawer-btn"
                            class="relative inline-flex items-center gap-2 bg-zinc-900 hover:bg-zinc-800 dark:bg-amber-500 dark:hover:bg-amber-600 text-white dark:text-zinc-950 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-full shadow-md hover:shadow-lg transition-all duration-200 active:scale-95 group">
                        <svg class="w-4 h-4 text-amber-300 dark:text-zinc-950 group-hover:rotate-6 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span class="text-xs font-semibold tracking-wide">Cotización</span>
                        <span class="transition-transform duration-300 font-mono font-bold text-xs px-2 py-0.5 rounded-full" 
                              :class="{ 'scale-125 bg-amber-400 dark:bg-zinc-900 text-zinc-950 dark:text-amber-400 shadow-md': bump, 'scale-100 bg-white/20 dark:bg-black/20 text-white dark:text-zinc-950': !bump }" 
                              x-text="countItems()"></span>
                    </button>
                </div>

                <!-- Botón Toggle Sol / Luna Interactiva (Alpine.js) -->
                <button @click="toggleTheme()" 
                        type="button" 
                        id="theme-toggle-btn"
                        title="Alternar Modo Claro / Modo Oscuro"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-amber-400 transition-all duration-200 flex items-center justify-center border border-zinc-200/80 dark:border-zinc-700 shadow-sm hover:scale-105 active:scale-95"
                        aria-label="Cambiar tema claro/oscuro">
                    <!-- Icono Sol (visible en modo oscuro) -->
                    <svg x-show="isDark" class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <!-- Icono Luna (visible en modo claro) -->
                    <svg x-show="!isDark" class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                <!-- Botón de Acceso / Login de Administrador -->
                <a href="{{ route('filament.admin.auth.login') }}" 
                   title="Acceso Administración / Iniciar Sesión"
                   class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-900 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 hover:text-white dark:hover:text-white transition-all duration-200 flex items-center justify-center border border-zinc-200/80 dark:border-zinc-700 shadow-sm hover:scale-105 active:scale-95 group">
                    <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </a>

                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="md:hidden p-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 transition">
                    <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menú Móvil Desplegable (Limpio: sin FAQ) -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             @click.outside="mobileMenuOpen = false"
             class="md:hidden bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl border-b border-zinc-200 dark:border-zinc-800 px-5 py-4 space-y-3"
             style="display: none;">
            <nav class="flex flex-col space-y-2 text-sm font-medium text-zinc-800 dark:text-zinc-200">
                <a @click="mobileMenuOpen = false" href="{{ route('catalog.index') }}" class="px-3 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex items-center justify-between">
                    <span>Inicio</span>
                    <span class="text-xs text-zinc-400 font-mono">01</span>
                </a>
                <a @click="mobileMenuOpen = false" href="#destacados" class="px-3 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex items-center justify-between">
                    <span>Productos Destacados</span>
                    <span class="text-xs text-zinc-400 font-mono">02</span>
                </a>
                <a @click="mobileMenuOpen = false" href="#catalogo" class="px-3 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex items-center justify-between">
                    <span>Catálogo Completo</span>
                    <span class="text-xs text-zinc-400 font-mono">03</span>
                </a>
                <a @click="mobileMenuOpen = false" href="#envios" class="px-3 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex items-center justify-between">
                    <span>Envíos & Logística</span>
                    <span class="text-xs text-zinc-400 font-mono">04</span>
                </a>
                <a @click="mobileMenuOpen = false" href="#contacto" class="px-3 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex items-center justify-between">
                    <span>Contacto & Ubicación</span>
                    <span class="text-xs text-zinc-400 font-mono">05</span>
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
    </header>

    @if(!request('categoria') && !request('buscar'))
        <!-- Hero Banner Editorial (Blanco Puro en Claro + Modo Oscuro Obsidian) -->
        <section class="relative bg-white dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 py-16 sm:py-24 overflow-hidden"
                 @if(!empty($settings?->hero_bg_image)) style="background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.92), rgba(255, 255, 255, 0.98)), url('{{ asset('storage/' . $settings->hero_bg_image) }}'); background-size: cover; background-position: center;" @endif>
            <!-- Patrón de fondo geométrico sutil -->
            <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none" style="background-image: radial-gradient(#18181B 1px, transparent 1px); background-size: 24px 24px;"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <div class="inline-flex items-center gap-2 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 px-4 py-1.5 rounded-full mb-6 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse"></span>
                    <span class="text-[10px] sm:text-xs font-mono font-medium tracking-widest uppercase text-zinc-700 dark:text-zinc-300">
                        {{ $settings->hero_badge ?? 'Catálogo Oficial • Temporada 2026' }}
                    </span>
                </div>

                <h1 class="font-serif text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-light tracking-tight text-zinc-900 dark:text-white max-w-4xl mx-auto leading-[1.12]">
                    {{ $settings->hero_title ?? 'Equipamiento y ambientación para ocasiones extraordinarias' }}
                </h1>

                <p class="text-xs sm:text-sm md:text-base text-zinc-600 dark:text-zinc-400 mt-5 max-w-2xl mx-auto font-light leading-relaxed">
                    {{ $settings->hero_subtitle ?? 'Mobiliario de etiqueta, vajilla fina, cristalería y mantelería para bodas, galas y celebraciones corporativas. Celebra con distinción y logística puntual garantizada.' }}
                </p>

                <!-- Mini-Stats de Confianza Boutique -->
                <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-2xl mx-auto">
                    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-zinc-200 dark:border-zinc-800 p-4 rounded-2xl shadow-sm text-center">
                        <span class="block font-serif text-2xl font-bold text-zinc-900 dark:text-white">+190</span>
                        <span class="text-xs text-zinc-600 dark:text-zinc-400 font-medium">Ítems de colección</span>
                    </div>
                    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-zinc-200 dark:border-zinc-800 p-4 rounded-2xl shadow-sm text-center">
                        <span class="block font-serif text-2xl font-bold text-emerald-600 dark:text-emerald-400">100%</span>
                        <span class="text-xs text-zinc-600 dark:text-zinc-400 font-medium">Sanitización certificada</span>
                    </div>
                    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border border-zinc-200 dark:border-zinc-800 p-4 rounded-2xl shadow-sm text-center">
                        <span class="block font-serif text-2xl font-bold text-amber-600 dark:text-amber-400">Puntual</span>
                        <span class="text-xs text-zinc-600 dark:text-zinc-400 font-medium">Entrega y retiro coordinado</span>
                    </div>
                </div>

                <div class="mt-8">
                    <a href="#catalogo" class="inline-flex items-center gap-2 bg-zinc-900 hover:bg-zinc-800 dark:bg-amber-500 dark:hover:bg-amber-600 text-white dark:text-zinc-950 font-medium text-xs sm:text-sm px-6 py-3.5 rounded-full shadow-lg hover:shadow-xl transition-all duration-300">
                        <span>Explorar Colección Completa</span>
                        <svg class="w-4 h-4 text-amber-400 dark:text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- Barra de 3 Pasos de Reserva Visual -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 sm:-mt-8 relative z-20">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/90 dark:border-zinc-800 p-5 sm:p-7 shadow-xl shadow-zinc-900/5">
                <div class="text-center mb-6">
                    <span class="text-[10px] font-mono tracking-widest uppercase text-zinc-500 dark:text-zinc-400 block mb-1">Paso a Paso</span>
                    <h3 class="font-serif text-xl sm:text-2xl text-zinc-900 dark:text-white font-semibold">Proceso de Reserva Simple y Transparente</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 relative">
                    <!-- Paso 1 -->
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-zinc-50/70 dark:bg-zinc-800/50 border border-zinc-200/60 dark:border-zinc-700/60">
                        <div class="w-10 h-10 rounded-full bg-zinc-900 dark:bg-zinc-700 text-amber-300 font-serif text-lg font-bold flex items-center justify-center shrink-0 shadow-sm">
                            1
                        </div>
                        <div>
                            <h4 class="font-semibold text-zinc-900 dark:text-white text-sm">Selecciona tu mobiliario</h4>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-1 leading-relaxed">
                                Agrega las piezas y cantidades que necesitas. Cada artículo muestra su stock en tiempo real.
                            </p>
                        </div>
                    </div>

                    <!-- Paso 2 -->
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-zinc-50/70 dark:bg-zinc-800/50 border border-zinc-200/60 dark:border-zinc-700/60">
                        <div class="w-10 h-10 rounded-full bg-zinc-900 dark:bg-zinc-700 text-amber-300 font-serif text-lg font-bold flex items-center justify-center shrink-0 shadow-sm">
                            2
                        </div>
                        <div>
                            <h4 class="font-semibold text-zinc-900 dark:text-white text-sm">Envía tu cotización</h4>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-1 leading-relaxed">
                                Ingresa tu nombre, fecha y comuna en el drawer lateral para consolidar tu solicitud.
                            </p>
                        </div>
                    </div>

                    <!-- Paso 3 -->
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-zinc-50/70 dark:bg-zinc-800/50 border border-zinc-200/60 dark:border-zinc-700/60">
                        <div class="w-10 h-10 rounded-full bg-emerald-700 text-white font-serif text-lg font-bold flex items-center justify-center shrink-0 shadow-sm">
                            3
                        </div>
                        <div>
                            <h4 class="font-semibold text-zinc-900 dark:text-white text-sm">Coordinación y flete vía WhatsApp</h4>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-1 leading-relaxed">
                                Te confirmamos disponibilidad de fecha, coordinamos el flete y aseguramos la reserva.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECCIÓN: SHOWROOM "DISFRUTA DE NUESTRO MOBILIARIO" -->
        <section id="mobiliario-rotacion" class="py-14 sm:py-20 border-b border-zinc-200/80 dark:border-zinc-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-12">
                    <span class="text-[10px] font-mono uppercase tracking-[0.25em] text-amber-800 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 px-3.5 py-1 rounded-full inline-block mb-3 font-semibold">
                        Showroom &bull; Colección Exclusiva
                    </span>
                    <h2 class="font-serif text-2xl sm:text-4xl md:text-5xl font-light text-zinc-900 dark:text-white tracking-wide uppercase">
                        Disfruta de Nuestro <span class="italic font-normal text-amber-700 dark:text-amber-500">Mobiliario</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 mt-3 font-light max-w-xl mx-auto">
                        Piezas seleccionadas con acabados de alta gama, maderas nobles y metales finamente trabajados para montajes distinguidos.
                    </p>
                </div>

                <!-- Grid de Mobiliario Showcase -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($showcaseProducts->take(6) as $item)
                        @php
                            $imgSrc = $getProductImage($item);
                        @endphp
                        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/90 dark:border-zinc-800 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                            <div>
                                <!-- Imagen Fotográfica de Alta Resolución -->
                                <div @click="openDetail({
                                        id: {{ $item->id }},
                                        name: '{{ addslashes($item->name) }}',
                                        code: {{ $item->code }},
                                        price: {{ $item->rental_price }},
                                        stock: {{ $item->stock }},
                                        category: '{{ addslashes($item->category->name ?? 'Mobiliario') }}',
                                        description: '{{ addslashes($item->description ?? 'Pieza de alta calidad para ambientación de bodas y banquetes. Se entrega completamente sanitizado e inspeccionado.') }}',
                                        is_sale: {{ $item->is_sale ? 'true' : 'false' }},
                                        image: '{{ $imgSrc }}'
                                     })"
                                     class="cursor-pointer relative h-60 sm:h-64 overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                                    <img src="{{ $imgSrc }}" 
                                         alt="{{ $item->name }}" 
                                         loading="lazy"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                                    
                                    <!-- Badges Flotantes de Cristal -->
                                    <span class="absolute top-3 left-3 backdrop-blur-md bg-white/85 dark:bg-zinc-900/85 text-zinc-900 dark:text-zinc-100 text-[10px] font-mono font-medium px-2.5 py-1 rounded-md shadow-sm border border-white/50 dark:border-zinc-700">
                                        Cód: {{ $item->code }}
                                    </span>

                                    <span class="absolute top-3 right-3 backdrop-blur-md bg-white/85 dark:bg-zinc-900/85 text-emerald-800 dark:text-emerald-400 text-[10px] font-semibold px-2.5 py-1 rounded-md shadow-sm border border-white/50 dark:border-zinc-700">
                                        {{ $item->stock }} un. disponibles
                                    </span>
                                </div>

                                <!-- Detalles del Producto -->
                                <div class="p-5">
                                    <span class="text-[10px] font-mono tracking-widest uppercase text-amber-800 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded border border-amber-200/60 dark:border-amber-800/60 inline-block font-semibold">
                                        {{ $item->category->name ?? 'Mobiliario' }}
                                    </span>

                                    <h3 @click="openDetail({
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
                                        class="cursor-pointer font-serif font-bold text-zinc-900 dark:text-white text-lg sm:text-xl mt-2 line-clamp-1 group-hover:text-amber-800 dark:group-hover:text-amber-400 transition-colors" title="{{ $item->name }}">
                                        {{ $item->name }}
                                    </h3>

                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 line-clamp-2 leading-relaxed">
                                        {{ $item->description ?? 'Pieza de alta calidad para ambientación de bodas y banquetes.' }}
                                    </p>

                                    <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-baseline justify-between">
                                        <div>
                                            <span class="text-[10px] text-zinc-500 dark:text-zinc-400 block uppercase tracking-wider">Valor arriendo:</span>
                                            <p class="text-xl font-bold font-mono text-zinc-900 dark:text-white mt-0.5">
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
                                                        category: '{{ addslashes($item->category->name ?? 'Mobiliario') }}',
                                                        description: '{{ addslashes($item->description ?? 'Pieza de alta calidad para ambientación de bodas y banquetes.') }}',
                                                        is_sale: {{ $item->is_sale ? 'true' : 'false' }},
                                                        image: '{{ $imgSrc }}'
                                                     })" 
                                                    class="text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white underline">
                                                Ficha completa
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón de Agregar con Feedback Táctil de 1 segundo -->
                            <div class="p-5 pt-0" x-data="{ added: false }">
                                @if($item->stock > 0)
                                    <button @click="addItem({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->rental_price }}, {{ $item->stock }}, {{ $item->code }}); added = true; setTimeout(() => added = false, 1000)"
                                            :class="added ? 'bg-emerald-600 text-white shadow-md' : 'bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white shadow-sm'"
                                            class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold transition-all duration-200 flex items-center justify-center gap-2 active:scale-98">
                                        <template x-if="!added">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                <span>+ Agregar al Arriendo</span>
                                            </span>
                                        </template>
                                        <template x-if="added">
                                            <span class="flex items-center gap-1.5 font-bold">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                <span>✓ Agregado</span>
                                            </span>
                                        </template>
                                    </button>
                                @else
                                    <button disabled class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 border border-zinc-200 dark:border-zinc-700 cursor-not-allowed">
                                        Sin stock disponible
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- SECCIÓN: PRODUCTOS DESTACADOS -->
        <section id="destacados" class="py-14 sm:py-20 border-b border-zinc-200/80 dark:border-zinc-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
                    <div>
                        <span class="text-[10px] font-mono uppercase tracking-[0.25em] text-amber-800 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 px-3.5 py-1 rounded-full inline-block mb-3 font-semibold">
                            Selección de Gala
                        </span>
                        <h2 class="font-serif text-2xl sm:text-4xl font-light text-zinc-900 dark:text-white tracking-wide">
                            Productos <span class="italic font-normal text-amber-700 dark:text-amber-500">Destacados</span>
                        </h2>
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 mt-2 font-light max-w-xl">
                            Artículos icónicos de alta demanda para recepciones nupciales, cócteles de gala y banquetes corporativos.
                        </p>
                    </div>

                    <a href="#catalogo" class="self-start sm:self-auto inline-flex items-center gap-2 text-xs font-semibold text-zinc-900 dark:text-zinc-200 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 px-4 py-2.5 rounded-full transition shadow-sm">
                        <span>Explorar todo el catálogo</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach ($featuredProducts as $feat)
                        @php
                            $featImg = $getProductImage($feat);
                        @endphp
                        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/90 dark:border-zinc-800 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                            <div>
                                <div @click="openDetail({
                                        id: {{ $feat->id }},
                                        name: '{{ addslashes($feat->name) }}',
                                        code: {{ $feat->code }},
                                        price: {{ $feat->rental_price }},
                                        stock: {{ $feat->stock }},
                                        category: '{{ addslashes($feat->category->name ?? 'Catálogo') }}',
                                        description: '{{ addslashes($feat->description ?? 'Artículo de alta gama para ambientación de eventos.') }}',
                                        is_sale: {{ $feat->is_sale ? 'true' : 'false' }},
                                        image: '{{ $featImg }}'
                                     })"
                                     class="cursor-pointer relative h-48 sm:h-52 overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                                    <img src="{{ $featImg }}" 
                                         alt="{{ $feat->name }}" 
                                         loading="lazy"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                                    
                                    <span class="absolute top-2.5 left-2.5 backdrop-blur-md bg-white/85 dark:bg-zinc-900/85 text-zinc-900 dark:text-zinc-100 text-[10px] font-mono font-medium px-2 py-0.5 rounded shadow-sm border border-white/50 dark:border-zinc-700">
                                        Cód: {{ $feat->code }}
                                    </span>

                                    <span class="absolute top-2.5 right-2.5 bg-amber-400 text-zinc-950 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded shadow-sm">
                                        ★ Destacado
                                    </span>
                                </div>

                                <div class="p-4 sm:p-5">
                                    <span class="text-[10px] font-mono tracking-widest uppercase text-amber-800 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded border border-amber-200/60 dark:border-amber-800/60 inline-block font-semibold">
                                        {{ $feat->category->name ?? 'Catálogo' }}
                                    </span>

                                    <h3 @click="openDetail({
                                            id: {{ $feat->id }},
                                            name: '{{ addslashes($feat->name) }}',
                                            code: {{ $feat->code }},
                                            price: {{ $feat->rental_price }},
                                            stock: {{ $feat->stock }},
                                            category: '{{ addslashes($feat->category->name ?? 'Catálogo') }}',
                                            description: '{{ addslashes($feat->description ?? 'Artículo de alta gama para ambientación de eventos.') }}',
                                            is_sale: {{ $feat->is_sale ? 'true' : 'false' }},
                                            image: '{{ $featImg }}'
                                         })"
                                        class="cursor-pointer font-serif font-bold text-zinc-900 dark:text-white text-base sm:text-lg mt-2 leading-snug line-clamp-1 group-hover:text-amber-800 dark:group-hover:text-amber-400 transition-colors" title="{{ $feat->name }}">
                                        {{ $feat->name }}
                                    </h3>

                                    <div class="mt-3 pt-2.5 border-t border-zinc-100 dark:border-zinc-800 flex items-baseline justify-between">
                                        <div>
                                            <span class="text-[10px] text-zinc-500 dark:text-zinc-400 block uppercase tracking-wider">Arriendo:</span>
                                            <p class="text-base sm:text-lg font-bold font-mono text-zinc-900 dark:text-white mt-0.5">
                                                ${{ number_format($feat->rental_price, 0, ',', '.') }}
                                                <span class="text-xs font-normal text-zinc-500 dark:text-zinc-400">/ un.</span>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[10px] font-mono font-semibold {{ $feat->stock > 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-zinc-400 dark:text-zinc-500' }}">
                                                {{ $feat->stock > 0 ? $feat->stock . ' un.' : 'Agotado' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 sm:p-5 pt-0" x-data="{ added: false }">
                                @if($feat->stock > 0)
                                    <button @click="addItem({{ $feat->id }}, '{{ addslashes($feat->name) }}', {{ $feat->rental_price }}, {{ $feat->stock }}, {{ $feat->code }}); added = true; setTimeout(() => added = false, 1000)"
                                            :class="added ? 'bg-emerald-600 text-white shadow-md' : 'bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white shadow-sm'"
                                            class="w-full py-2 px-3 rounded-xl text-xs font-semibold transition-all duration-200 flex items-center justify-center gap-1.5 active:scale-98">
                                        <template x-if="!added">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                <span>+ Agregar</span>
                                            </span>
                                        </template>
                                        <template x-if="added">
                                            <span class="flex items-center gap-1 font-bold">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                <span>✓ Agregado</span>
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
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- CONTENIDO PRINCIPAL: CATÁLOGO DE ARRIENDOS Y FILTROS -->
    <main id="catalogo" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-1 w-full">
        
        <div class="mb-8">
            <span class="text-[10px] font-mono uppercase tracking-[0.25em] text-amber-800 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 px-3.5 py-1 rounded-full inline-block mb-2 font-semibold">
                Inventario Oficial
            </span>
            <h2 class="font-serif text-2xl sm:text-4xl font-light text-zinc-900 dark:text-white tracking-wide">
                Catálogo de <span class="italic font-normal text-amber-700 dark:text-amber-500">Arriendos</span>
            </h2>
            <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 mt-1 font-light">
                Filtra por categoría o busca por código o nombre para armar tu cotización con disponibilidad asegurada.
            </p>
        </div>

        <!-- Buscador Integrado con Bordes Redondeados Completos y Búsqueda en Vivo Reactiva -->
        <div class="mb-8 bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 p-3 sm:p-4 rounded-2xl shadow-sm flex flex-col sm:flex-row justify-between items-center gap-3 relative">
            <form method="GET" action="{{ route('catalog.index') }}#catalogo" class="flex gap-2 w-full sm:max-w-xl relative">
                @if(request('categoria'))
                    <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                @endif
                <div class="relative flex-1">
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
                <button type="submit" class="bg-zinc-900 hover:bg-zinc-800 dark:bg-amber-500 dark:hover:bg-amber-600 text-white dark:text-zinc-950 font-semibold px-6 py-2.5 rounded-full text-xs sm:text-sm transition duration-200 active:scale-95 shadow-sm shrink-0">
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
                                         class="w-12 h-12 rounded-lg object-cover bg-zinc-100 dark:bg-zinc-800 shrink-0 border border-zinc-200 dark:border-zinc-700">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-zinc-900 dark:text-white text-xs sm:text-sm truncate" x-text="item.name"></p>
                                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">
                                            <span x-text="'Cód: ' + item.code"></span> &bull; 
                                            <span x-text="item.category_name"></span> &bull; 
                                            <span class="font-semibold text-zinc-800 dark:text-zinc-200" x-text="'$' + Number(item.rental_price).toLocaleString('es-CL')"></span>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <template x-if="item.stock > 0">
                                        <button type="button" 
                                                @click="addItem(item.id, item.name, item.rental_price, item.stock, item.code)"
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

            @if(request('buscar') || request('categoria'))
                <div class="flex items-center gap-2 self-start sm:self-center">
                    <a href="{{ route('catalog.index') }}#catalogo" class="group inline-flex items-center gap-1.5 text-xs font-semibold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/40 border border-rose-200 dark:border-rose-800 px-3.5 py-2 rounded-full transition-all duration-200">
                        <span>Limpiar filtros</span>
                        <svg class="w-3.5 h-3.5 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                </div>
            @endif
        </div>

        <!-- Carrusel Horizontal de Categorías con Conteo Activo -->
        <nav class="flex gap-2 overflow-x-auto pb-4 mb-8 no-scrollbar items-center">
            <a href="{{ route('catalog.index', array_filter(['buscar' => request('buscar')])) }}#catalogo" 
               class="px-5 py-2.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all duration-200 {{ !request('categoria') ? 'bg-zinc-900 dark:bg-amber-500 text-white dark:text-zinc-950 shadow-md' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 border border-zinc-200/90 dark:border-zinc-700' }}">
                Todos los productos
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('catalog.index', array_filter(['categoria' => $cat->slug, 'buscar' => request('buscar')])) }}#catalogo" 
                   class="px-5 py-2.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all duration-200 {{ request('categoria') === $cat->slug ? 'bg-zinc-900 dark:bg-amber-500 text-white dark:text-zinc-950 shadow-md' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 border border-zinc-200/90 dark:border-zinc-700' }}">
                    {{ $cat->name }}
                    <span class="ml-1 opacity-75 font-normal">({{ $cat->products_count }})</span>
                </a>
            @endforeach
        </nav>

        <!-- Grid de Cards de Producto -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @forelse ($products as $prod)
                @php
                    $cardImg = $getProductImage($prod);
                @endphp
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/90 dark:border-zinc-800 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <!-- Contenedor Fotográfico con Cero Cajas Grises -->
                        <div @click="openDetail({
                                id: {{ $prod->id }},
                                name: '{{ addslashes($prod->name) }}',
                                code: {{ $prod->code }},
                                price: {{ $prod->rental_price }},
                                stock: {{ $prod->stock }},
                                category: '{{ addslashes($prod->category->name ?? 'Catálogo') }}',
                                description: '{{ addslashes($prod->description ?? 'Artículo de alta gama para ambientación de bodas y eventos.') }}',
                                is_sale: {{ $prod->is_sale ? 'true' : 'false' }},
                                image: '{{ $cardImg }}'
                             })"
                             class="cursor-pointer relative h-48 sm:h-56 overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                            <img src="{{ $cardImg }}" 
                                 alt="{{ $prod->name }}" 
                                 loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                            
                            <!-- Badges Flotantes con Efecto Cristal -->
                            <span class="absolute top-2.5 left-2.5 backdrop-blur-md bg-white/85 dark:bg-zinc-900/85 text-zinc-900 dark:text-zinc-100 text-[10px] font-mono font-medium px-2.5 py-0.5 rounded shadow-sm border border-white/50 dark:border-zinc-700">
                                Cód: {{ $prod->code }}
                            </span>

                            @if($prod->is_sale)
                                <span class="absolute top-2.5 right-2.5 bg-amber-400 text-zinc-950 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded shadow-sm">
                                    Venta
                                </span>
                            @endif
                        </div>

                        <!-- Detalles del Producto -->
                        <div class="p-4 sm:p-5">
                            <span class="text-[10px] font-mono tracking-widest uppercase text-amber-800 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded border border-amber-200/60 dark:border-amber-800/60 inline-block font-semibold">
                                {{ $prod->category->name ?? 'Catálogo' }}
                            </span>

                            <h3 @click="openDetail({
                                    id: {{ $prod->id }},
                                    name: '{{ addslashes($prod->name) }}',
                                    code: {{ $prod->code }},
                                    price: {{ $prod->rental_price }},
                                    stock: {{ $prod->stock }},
                                    category: '{{ addslashes($prod->category->name ?? 'Catálogo') }}',
                                    description: '{{ addslashes($prod->description ?? 'Artículo de alta gama para ambientación de bodas y banquetes.') }}',
                                    is_sale: {{ $prod->is_sale ? 'true' : 'false' }},
                                    image: '{{ $cardImg }}'
                                 })"
                                class="cursor-pointer font-serif font-bold text-zinc-900 dark:text-white text-base sm:text-lg mt-2 leading-snug line-clamp-1 group-hover:text-amber-800 dark:group-hover:text-amber-400 transition-colors" title="{{ $prod->name }}">
                                {{ $prod->name }}
                            </h3>

                            <div class="mt-3 pt-2.5 border-t border-zinc-100 dark:border-zinc-800 flex items-baseline justify-between">
                                <div>
                                    <span class="text-[10px] text-zinc-500 dark:text-zinc-400 block uppercase tracking-wider">Valor arriendo:</span>
                                    <p class="text-base sm:text-lg font-bold font-mono text-zinc-900 dark:text-white mt-0.5">
                                        ${{ number_format($prod->rental_price, 0, ',', '.') }}
                                        <span class="text-xs font-normal text-zinc-500 dark:text-zinc-400">/ un.</span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-mono font-semibold {{ $prod->stock > 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-zinc-400 dark:text-zinc-500' }}">
                                        {{ $prod->stock > 0 ? $prod->stock . ' un.' : 'Agotado' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botón "+ Agregar al Arriendo" con Feedback Táctil de 1 Segundo -->
                    <div class="p-4 sm:p-5 pt-0" x-data="{ added: false }">
                        @if($prod->stock > 0)
                            <button @click="addItem({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $prod->rental_price }}, {{ $prod->stock }}, {{ $prod->code }}); added = true; setTimeout(() => added = false, 1000)"
                                    :class="added ? 'bg-emerald-600 text-white shadow-md' : 'bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white shadow-sm'"
                                    class="w-full py-2.5 px-3 rounded-xl text-xs font-semibold transition-all duration-200 flex items-center justify-center gap-1.5 active:scale-98">
                                <template x-if="!added">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                        <span>+ Agregar al Arriendo</span>
                                    </span>
                                </template>
                                <template x-if="added">
                                    <span class="flex items-center gap-1.5 font-bold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        <span>✓ Agregado</span>
                                    </span>
                                </template>
                            </button>
                        @else
                            <button disabled class="w-full py-2.5 px-3 rounded-xl text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 border border-zinc-200 dark:border-zinc-700 cursor-not-allowed">
                                Sin stock disponible
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200/90 dark:border-zinc-800 p-8 shadow-sm">
                    <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto text-zinc-400 dark:text-zinc-500 mb-4">
                        <svg class="w-8 h-8 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-serif text-2xl font-bold text-zinc-900 dark:text-white">No encontramos productos coincidentes</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-2 max-w-sm mx-auto">Intenta con términos más generales o limpia los filtros activos.</p>
                    <a href="{{ route('catalog.index') }}#catalogo" class="inline-block mt-5 bg-zinc-900 hover:bg-zinc-800 dark:bg-amber-500 dark:hover:bg-amber-600 text-white dark:text-zinc-950 text-xs font-semibold px-6 py-2.5 rounded-full transition shadow-sm">
                        Ver todo el catálogo
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        <div class="mt-    <!-- SECCIÓN DE ENVÍOS Y LOGÍSTICA EDITORIAL -->
    <section id="envios" class="py-20 sm:py-24 bg-zinc-100/70 dark:bg-zinc-900/60 border-t border-b border-zinc-200/80 dark:border-zinc-800 scroll-mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-7 border border-zinc-200/80 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-800/60 text-amber-800 dark:text-amber-400 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        </div>
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 block mb-1">01. Tarifa & Volumen</span>
                        <h3 class="font-serif text-lg sm:text-xl font-bold text-zinc-900 dark:text-white mb-2.5">Flete por Zona y Capacidad</h3>
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
                <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-7 border border-zinc-200/80 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-800/60 text-amber-800 dark:text-amber-400 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 block mb-1">02. Descarga</span>
                        <h3 class="font-serif text-lg sm:text-xl font-bold text-zinc-900 dark:text-white mb-2.5">Acceso y Primer Piso</h3>
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
                <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-7 border border-zinc-200/80 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-800/60 text-amber-800 dark:text-amber-400 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 block mb-1">03. Montaje</span>
                        <h3 class="font-serif text-lg sm:text-xl font-bold text-zinc-900 dark:text-white mb-2.5">Instalación Certificada</h3>
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
                <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-7 border border-zinc-200/80 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-800/60 text-amber-800 dark:text-amber-400 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 block mb-1">04. Coordinación</span>
                        <h3 class="font-serif text-lg sm:text-xl font-bold text-zinc-900 dark:text-white mb-2.5">Conteo en Terreno</h3>
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
                                class="w-full text-left px-5 sm:px-6 py-4 sm:py-5 flex items-center justify-between gap-4 hover:bg-zinc-50/70 dark:hover:bg-zinc-800/70 transition">
                            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                                <span class="text-[9px] sm:text-[10px] uppercase font-mono font-semibold tracking-wider px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-400 border border-amber-200/70 dark:border-amber-800/70 shrink-0">
                                    {{ $faq->category }}
                                </span>
                                <span class="font-serif text-sm sm:text-base md:text-lg font-bold text-zinc-900 dark:text-white leading-snug">
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

    <!-- ZONA DE CONTACTO COMPLETA -->
    <section id="contacto" class="py-20 sm:py-24 bg-white dark:bg-zinc-950 border-t border-zinc-200/80 dark:border-zinc-800 scroll-mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
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
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                
                <!-- Izquierda: Información Oficial de la Empresa -->
                <div class="lg:col-span-5 bg-zinc-900 dark:bg-zinc-900/90 text-zinc-100 rounded-3xl p-8 sm:p-10 shadow-xl border border-zinc-800 flex flex-col justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 bg-zinc-800/80 border border-zinc-700 px-3 py-1 rounded-full text-[10px] font-mono tracking-wider uppercase text-amber-300 mb-6">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Atención Central & Bodega
                        </div>
                        <h3 class="font-serif text-2xl sm:text-3xl font-light tracking-wide text-white mb-4">
                            Visítanos o comunícate directamente
                        </h3>
                        <p class="text-zinc-400 text-xs sm:text-sm font-light leading-relaxed mb-8">
                            Disponemos de showroom de vajilla, mantelería y mobiliario previa coordinación, para degustaciones y pruebas de ambientación de bodas y galas.
                        </p>

                        <!-- Items de Contacto con Números Dinámicos -->
                        <div class="space-y-6 text-xs sm:text-sm">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-center shrink-0 text-amber-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <span class="text-[10px] font-mono text-zinc-400 uppercase tracking-widest block">Dirección Matriz</span>
                                    <span class="text-zinc-200 font-medium">{{ $settings->company_address ?? 'Av. El Parque 4120, Ciudad Empresarial, Huechuraba' }}</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-center shrink-0 text-emerald-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div>
                                    <span class="text-[10px] font-mono text-zinc-400 uppercase tracking-widest block">Teléfonos & WhatsApp</span>
                                    <a href="tel:{{ $settings->cleanTelPhone() }}" class="text-zinc-200 hover:text-white font-medium block">
                                        {{ $settings->company_phone ?? '+56 2 2987 6543' }}
                                    </a>
                                    <a href="https://wa.me/{{ $settings->cleanWhatsappPhone() }}" target="_blank" class="text-emerald-400 hover:text-emerald-300 font-mono text-xs flex items-center gap-1.5 mt-0.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        WhatsApp: {{ $settings->whatsapp_phone ?? '+56 9 1234 5678' }}
                                    </a>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-center shrink-0 text-amber-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <span class="text-[10px] font-mono text-zinc-400 uppercase tracking-widest block">Correo Electrónico</span>
                                    <a href="mailto:{{ $settings->company_email ?? 'contacto@arriendoseventos.cl' }}" class="text-zinc-200 hover:text-white font-medium">
                                        {{ $settings->company_email ?? 'contacto@arriendoseventos.cl' }}
                                    </a>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-center shrink-0 text-amber-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
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
                <div class="lg:col-span-7 bg-zinc-50 dark:bg-zinc-900 rounded-3xl p-8 sm:p-10 border border-zinc-200/90 dark:border-zinc-800 shadow-sm" x-data="contactForm()">
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
                                <div class="flex items-center justify-between mb-1.5">
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
                <div class="rounded-3xl overflow-hidden shadow-lg border border-zinc-200 dark:border-zinc-800 h-96 relative bg-zinc-100 dark:bg-zinc-900">
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

    <!-- FOOTER BOUTIQUE LUXURY (Blanco Puro + Modo Oscuro Obsidian) -->
    <footer class="bg-white dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800 mt-20 py-14 text-zinc-600 dark:text-zinc-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Columna 1: Marca & Lema -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        @if(!empty($settings?->logo_path))
                            <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="{{ $settings->site_name ?? 'Logo' }}" class="w-10 h-10 rounded-xl object-cover shadow border border-zinc-200 dark:border-zinc-800">
                        @else
                            <div class="w-10 h-10 rounded-xl bg-zinc-900 dark:bg-zinc-800 text-zinc-100 flex items-center justify-center font-serif text-xl font-bold shadow border border-zinc-800 dark:border-zinc-700">
                                {{ mb_substr($settings->site_name ?? 'A', 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <span class="font-serif text-lg font-bold tracking-wider text-zinc-900 dark:text-white uppercase block">
                                {{ $settings->site_name ?? 'Arriendos & Eventos' }}
                            </span>
                            <span class="text-xs text-zinc-500 dark:text-zinc-400 font-mono">
                                {{ $settings->company_legal_info ?? 'Menaje & Banquetería de Alta Gala' }}
                            </span>
                        </div>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-md">
                        {{ $settings->site_tagline ?? 'Boutique especializada en arriendo de vajilla, copas de cristal, mantelería fina y mobiliario premium para eventos inolvidables.' }}
                    </p>
                    
                    <!-- Teléfonos directos dinámicos en Footer -->
                    <div class="flex flex-wrap items-center gap-4 text-xs font-mono pt-1">
                        @if(!empty($settings?->company_phone))
                            <a href="tel:{{ $settings->cleanTelPhone() }}" class="inline-flex items-center gap-1.5 text-zinc-700 dark:text-zinc-300 hover:text-amber-600 dark:hover:text-amber-400 transition">
                                <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>{{ $settings->company_phone }}</span>
                            </a>
                        @endif
                        @if(!empty($settings?->whatsapp_phone))
                            <a href="https://wa.me/{{ $settings->cleanWhatsappPhone() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 font-semibold transition">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.529 1.831.812 2.791.812 3.179 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.768-5.768-5.768zm9.969 5.768c0 5.518-4.482 10-10 10-1.748 0-3.385-.45-4.819-1.242l-5.181 1.356 1.381-5.048c-.902-1.492-1.381-3.21-1.381-5.066 0-5.518 4.482-10 10-10s10 4.482 10 10z"/></svg>
                                <span>WhatsApp: {{ $settings->whatsapp_phone }}</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Columna 2: Navegación Rápida Destacando FAQ -->
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white mb-4">
                        Navegación
                    </h5>
                    <ul class="space-y-2.5 text-xs">
                        <li>
                            <a href="#catalogo" class="hover:text-zinc-900 dark:hover:text-white transition">
                                Catálogo de Productos
                            </a>
                        </li>
                        <li>
                            <a href="#envios" class="hover:text-zinc-900 dark:hover:text-white transition">
                                Políticas de Despacho & Envíos
                            </a>
                        </li>
                        <li>
                            <a href="#contacto" class="hover:text-zinc-900 dark:hover:text-white transition">
                                Contacto & Bodega
                            </a>
                        </li>
                        <li class="pt-1.5">
                            <a href="#preguntas-frecuentes" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 dark:hover:bg-amber-900/60 text-amber-900 dark:text-amber-400 font-bold border border-amber-200/80 dark:border-amber-800/60 transition-all shadow-sm">
                                <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Preguntas Frecuentes (FAQ)
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Columna 3: Redes & Atención -->
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white mb-4">
                        Síguenos
                    </h5>
                    <div class="flex items-center gap-2.5 mb-4">
                        @if(!empty($settings?->instagram_url))
                            <a href="{{ $settings->instagram_url }}" target="_blank" rel="noopener noreferrer" 
                               title="Instagram"
                               class="w-9 h-9 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-900 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 hover:text-white transition-all flex items-center justify-center border border-zinc-200 dark:border-zinc-700">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                        @endif
                        @if(!empty($settings?->facebook_url))
                            <a href="{{ $settings->facebook_url }}" target="_blank" rel="noopener noreferrer" 
                               title="Facebook"
                               class="w-9 h-9 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-900 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 hover:text-white transition-all flex items-center justify-center border border-zinc-200 dark:border-zinc-700">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M9 8H6v4h3v12h5V12h3.642L18 8h-4V6.333C14 5.374 14.5 5 15.5 5H18V0h-3.808C10.595 0 9 1.583 9 4.615V8z"/></svg>
                            </a>
                        @endif
                        @if(!empty($settings?->tiktok_url))
                            <a href="{{ $settings->tiktok_url }}" target="_blank" rel="noopener noreferrer" 
                               title="TikTok"
                               class="w-9 h-9 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-900 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 hover:text-white transition-all flex items-center justify-center border border-zinc-200 dark:border-zinc-700">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64c.298-.002.595.042.88.13V9.4a6.33 6.33 0 00-1-.08A6.34 6.34 0 003 15.66a6.34 6.34 0 0010.86 4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-.04-4.52z"/></svg>
                            </a>
                        @endif
                        <a href="https://wa.me/{{ $settings->cleanWhatsappPhone() }}" target="_blank" rel="noopener noreferrer" 
                           title="WhatsApp"
                           class="w-9 h-9 rounded-full bg-emerald-100 dark:bg-emerald-950/60 hover:bg-emerald-600 text-emerald-800 dark:text-emerald-300 hover:text-white transition-all flex items-center justify-center border border-emerald-200 dark:border-emerald-800">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.529 1.831.812 2.791.812 3.179 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.768-5.768-5.768zm9.969 5.768c0 5.518-4.482 10-10 10-1.748 0-3.385-.45-4.819-1.242l-5.181 1.356 1.381-5.048c-.902-1.492-1.381-3.21-1.381-5.066 0-5.518 4.482-10 10-10s10 4.482 10 10z"/></svg>
                        </a>
                    </div>
                    <p class="text-[11px] text-zinc-400 dark:text-zinc-500">
                        Atención personalizada para cotizaciones corporativas y bodas.
                    </p>
                </div>
            </div>

            <!-- Copyright -->
            <div class="pt-6 border-t border-zinc-100 dark:border-zinc-800 flex flex-col sm:flex-row items-center justify-between text-[11px] text-zinc-400 dark:text-zinc-500 font-mono gap-2">
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
                 class="inline-block w-full max-w-3xl bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-left align-middle transition-all transform rounded-3xl shadow-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden relative z-10">
                
                <!-- Encabezado del Modal -->
                <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-zinc-50/70 dark:bg-zinc-900/70">
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

                <div class="p-6 sm:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                        <!-- Fotografía del Producto -->
                        <div class="rounded-2xl bg-zinc-100 dark:bg-zinc-800/60 p-2 border border-zinc-200/80 dark:border-zinc-700/60 overflow-hidden relative group">
                            <img :src="detailModal.product?.image" 
                                 :alt="detailModal.product?.name" 
                                 class="w-full h-72 object-cover rounded-xl shadow-inner">
                            <span class="absolute top-4 left-4 backdrop-blur-md bg-white/90 dark:bg-zinc-900/90 text-zinc-900 dark:text-zinc-100 text-xs font-mono font-medium px-3 py-1 rounded shadow-sm border border-zinc-200 dark:border-zinc-700" 
                                  x-text="'Cód: ' + detailModal.product?.code"></span>
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
                                        <span class="text-[11px] text-zinc-500 dark:text-zinc-400 block mt-0.5">IVA incluido &bull; Arriendo por evento</span>
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
                                    <button @click="addDetailToCart(); modalAdded = true; setTimeout(() => modalAdded = false, 1200)" 
                                            :disabled="!detailModal.product || detailModal.product.stock <= 0"
                                            :class="modalAdded ? 'bg-emerald-600 text-white' : 'bg-zinc-900 dark:bg-amber-600 hover:bg-zinc-800 dark:hover:bg-amber-500 text-white'"
                                            class="w-full uppercase tracking-wider py-3.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2 shadow-md active:scale-98 disabled:bg-zinc-200 dark:disabled:bg-zinc-800 disabled:text-zinc-400 dark:disabled:text-zinc-600 disabled:cursor-not-allowed">
                                        <template x-if="!modalAdded">
                                            <span class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                <span>+ Agregar a la Cotización</span>
                                            </span>
                                        </template>
                                        <template x-if="modalAdded">
                                            <span class="flex items-center gap-2 font-bold">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                <span>✓ ¡Agregado a tu Lista!</span>
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
             class="fixed inset-y-0 right-0 max-w-md w-full bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 shadow-2xl flex flex-col justify-between z-50">
            
            <!-- Header del Drawer -->
            <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-zinc-50/70 dark:bg-zinc-900/80">
                <div>
                    <h2 class="font-serif font-bold text-2xl text-zinc-900 dark:text-white tracking-wide">Tu Cotización</h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Revisa tus artículos antes de consultar disponibilidad</p>
                </div>
                <button @click="cartOpen = false" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-zinc-100 dark:hover:bg-zinc-800 transition text-2xl font-light">
                    &times;
                </button>
            </div>

            <!-- Alerta de Error si aplica -->
            <template x-if="errorMessage">
                <div class="mx-5 mt-4 p-3.5 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 text-rose-700 dark:text-rose-300 text-xs rounded-xl flex items-start gap-2 shadow-sm">
                    <svg class="w-4 h-4 shrink-0 mt-0.5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span x-text="errorMessage"></span>
                </div>
            </template>

            <!-- Listado de Artículos -->
            <div class="p-5 overflow-y-auto flex-1 space-y-3.5 divide-y divide-zinc-100 dark:divide-zinc-800">
                <template x-if="cart.length === 0">
                    <div class="text-center py-20 text-zinc-400 dark:text-zinc-500">
                        <svg class="w-12 h-12 mx-auto stroke-zinc-300 dark:stroke-zinc-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <p class="font-serif text-xl font-medium text-zinc-700 dark:text-zinc-300">Tu lista está vacía</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 max-w-xs mx-auto">Explora nuestro catálogo y agrega mobiliario o vajilla para comenzar.</p>
                    </div>
                </template>

                <template x-for="item in cart" :key="item.id">
                    <div class="pt-3.5 first:pt-0 flex items-start justify-between text-sm gap-3">
                        <div class="flex-1">
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

                        <div class="text-right flex flex-col justify-between items-end">
                            <p class="font-bold text-zinc-900 dark:text-white text-sm font-mono" x-text="'$' + (item.price * item.qty).toLocaleString('es-CL')"></p>
                            <button @click="removeItem(item.id)" class="text-[11px] text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-300 hover:underline mt-2.5 transition">
                                Quitar
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Resumen y Formulario para Solicitud por WhatsApp -->
            <div class="p-5 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-900/80 space-y-4">
                <div class="bg-white dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 p-4 rounded-xl space-y-2 shadow-sm">
                    <div class="flex justify-between items-center text-xs text-zinc-500 dark:text-zinc-400">
                        <span>Total de artículos:</span>
                        <span class="font-semibold text-zinc-800 dark:text-zinc-200 font-mono" x-text="countItems() + ' unidades'"></span>
                    </div>
                    <div class="flex justify-between items-center text-sm font-bold text-zinc-900 dark:text-white pt-2.5 border-t border-zinc-100 dark:border-zinc-700">
                        <span>Total Estimado:</span>
                        <span class="text-2xl font-bold font-mono text-zinc-900 dark:text-white" x-text="'$' + getTotal().toLocaleString('es-CL')"></span>
                    </div>
                </div>

                <div class="space-y-3 pt-1 text-xs">
                    <label class="block font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider text-[10px]">Datos para la reserva:</label>
                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 text-[11px] mb-1">Nombre y Apellido *</label>
                        <input type="text" x-model="form.name" placeholder="Tu Nombre y Apellido *" 
                               class="w-full p-3 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition shadow-sm">
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
                               class="w-full p-3 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition shadow-sm">
                    </div>
                    <div>
                        <label class="block font-medium text-zinc-700 dark:text-zinc-300 text-[11px] mb-1">Comuna o Lugar del Evento *</label>
                        <input type="text" x-model="form.location" placeholder="Comuna / Lugar del Evento *" 
                               class="w-full p-3 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition shadow-sm">
                    </div>
                </div>

                <!-- Alerta de Error visible en el pie del carrito -->
                <template x-if="errorMessage">
                    <div class="p-3 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 text-rose-700 dark:text-rose-300 text-xs rounded-xl flex items-start gap-2 shadow-sm animate-pulse">
                        <svg class="w-4 h-4 shrink-0 mt-0.5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span x-text="errorMessage"></span>
                    </div>
                </template>

                <!-- Botón de Envío POST a route('quote.store') y Apertura de WhatsApp -->
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
    <div class="fixed bottom-6 right-6 z-30">
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
                form: JSON.parse(localStorage.getItem('cotizacion_form') || '{"name":"","date":"","location":""}'),

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

                addDetailToCart() {
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
                    }

                    this.save();
                },

                addItem(id, name, price, stock, code) {
                    this.trackProductClick(id);
                    let item = this.cart.find(i => i.id === id);
                    if (item) {
                        if (item.qty < stock) {
                            item.qty++;
                            this.showToast(`Agregaste otra unidad de "${name}"`);
                        } else {
                            this.showToast(`Alcanzaste el stock disponible de "${name}" (${stock} un.)`);
                        }
                    } else {
                        this.cart.push({ id, name, price, stock, code, qty: 1 });
                        this.showToast(`"${name}" añadido a tu lista`);
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
    </script>
</body>
</html>