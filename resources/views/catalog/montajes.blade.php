<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Montaje de Eventos en Santiago & Regiones | {{ $settings->site_name ?? 'Arriendos & Eventos' }}</title>
    <meta name="description" content="Montaje integral de eventos, mobiliario, vajilla, mantelería y ambientación para bodas, galas y eventos corporativos en Santiago y regiones.">

    <!-- Google Fonts: Cormorant Garamond & Plus Jakarta Sans -->
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
    </style>
</head>
<body x-data="{
        mobileMenuOpen: false,
        cartOpen: false,
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
        selectedProject: null,
        lightboxOpen: false,
        currentPhotoIndex: 0,
        activeTab: 'todos',
        openProject(project) {
            this.selectedProject = project;
            this.currentPhotoIndex = 0;
            this.lightboxOpen = true;
        },
        nextPhoto() {
            if (!this.selectedProject) return;
            this.currentPhotoIndex = (this.currentPhotoIndex + 1) % this.selectedProject.gallery.length;
        },
        prevPhoto() {
            if (!this.selectedProject) return;
            this.currentPhotoIndex = (this.currentPhotoIndex - 1 + this.selectedProject.gallery.length) % this.selectedProject.gallery.length;
        }
    }" 
    class="bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen flex flex-col selection:bg-amber-500 selection:text-white transition-colors duration-200">

    <!-- HEADER / NAVEGACIÓN PRINCIPAL -->
    <header class="sticky top-0 z-40 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md border-b border-zinc-200 dark:border-zinc-800 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <a href="{{ route('catalog.index') }}" class="flex items-center gap-3 group focus:outline-none">
                @if(!empty($settings?->logo_path))
                    <img src="{{ asset('storage/' . $settings->logo_path) }}" 
                         alt="{{ $settings->site_name ?? 'Arriendos & Eventos' }}" 
                         class="h-10 sm:h-12 w-auto object-contain transition-transform duration-300 group-hover:scale-105">
                @else
                    <div class="w-10 h-10 rounded-xl bg-zinc-900 dark:bg-amber-500 text-amber-400 dark:text-zinc-950 flex items-center justify-center font-serif font-black text-xl shadow-inner">
                        {{ mb_substr($settings->site_name ?? 'A', 0, 1) }}
                    </div>
                @endif
                <div class="flex flex-col">
                    <span class="font-serif text-lg sm:text-xl font-bold tracking-tight text-zinc-900 dark:text-white leading-tight">
                        {{ $settings->site_name ?? 'Arriendos & Eventos' }}
                    </span>
                    <span class="text-[10px] sm:text-xs text-zinc-500 dark:text-zinc-400 font-mono tracking-wider uppercase">
                        {{ $settings->site_tagline ?? 'Banquetería & Mobiliario' }}
                    </span>
                </div>
            </a>

            <!-- Menú Desktop -->
            <nav class="hidden md:flex items-center gap-6 text-xs uppercase tracking-wider font-semibold text-zinc-600 dark:text-zinc-400">
                <a href="{{ route('catalog.index') }}" class="hover:text-zinc-900 dark:hover:text-white transition-colors py-1">
                    Inicio
                </a>
                <a href="{{ route('catalog.index') }}#destacados" class="hover:text-zinc-900 dark:hover:text-white transition-colors py-1">
                    Destacados
                </a>
                <a href="{{ route('catalog.index') }}#catalogo" class="hover:text-zinc-900 dark:hover:text-white transition-colors py-1">
                    Catálogo
                </a>
                <a href="{{ route('catalog.montajes') }}" class="text-zinc-950 dark:text-white font-bold border-b-2 border-zinc-900 dark:border-amber-400 py-1 flex items-center gap-1">
                    <span>Montajes</span>
                    <span class="text-[9px] font-mono uppercase bg-amber-500/10 text-amber-700 dark:text-amber-400 px-1.5 py-0.2 rounded border border-amber-500/30">Nuevo</span>
                </a>
                <a href="{{ route('catalog.index') }}#envios" class="hover:text-zinc-900 dark:hover:text-white transition-colors py-1">
                    Envíos
                </a>
                <a href="{{ route('catalog.index') }}#contacto" class="hover:text-zinc-900 dark:hover:text-white transition-colors py-1">
                    Contacto
                </a>
                <a href="https://wa.me/{{ $settings->cleanWhatsappPhone() }}?text={{ rawurlencode('Hola, me comunico para consultar por un servicio de montaje.') }}" 
                   target="_blank" 
                   class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors py-1 flex items-center gap-1.5 font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    WhatsApp
                </a>
            </nav>

            <!-- Acciones -->
            <div class="flex items-center gap-2 sm:gap-2.5">
                <!-- Botón Toggle Sol / Luna -->
                <button @click="toggleTheme()" 
                        type="button" 
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-amber-400 flex items-center justify-center border border-zinc-200/80 dark:border-zinc-700 shadow-sm hover:scale-105 active:scale-95 transition">
                    <svg x-show="isDark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg x-show="!isDark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                <!-- Botón Login Panel -->
                <a href="{{ route('filament.admin.auth.login') }}" 
                   class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-900 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 hover:text-white transition flex items-center justify-center border border-zinc-200/80 dark:border-zinc-700 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </a>

                <!-- Botón Móvil -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200">
                    <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menú Móvil -->
        <div x-show="mobileMenuOpen" 
             x-transition 
             @click.outside="mobileMenuOpen = false" 
             class="md:hidden bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl border-b border-zinc-200 dark:border-zinc-800 px-5 py-4 space-y-2"
             style="display: none;">
            <a href="{{ route('catalog.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium">Inicio</a>
            <a href="{{ route('catalog.index') }}#destacados" class="block px-3 py-2 rounded-lg text-sm font-medium">Destacados</a>
            <a href="{{ route('catalog.index') }}#catalogo" class="block px-3 py-2 rounded-lg text-sm font-medium">Catálogo</a>
            <a href="{{ route('catalog.montajes') }}" class="block px-3 py-2 rounded-lg text-sm font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-950 dark:text-white">Montajes & Puesta en Escena</a>
            <a href="{{ route('catalog.index') }}#envios" class="block px-3 py-2 rounded-lg text-sm font-medium">Envíos</a>
            <a href="{{ route('catalog.index') }}#contacto" class="block px-3 py-2 rounded-lg text-sm font-medium">Contacto</a>
        </div>
    </header>

    <!-- HERO EDITORIAL DE MONTAJES (Inspirado en imagen 2) -->
    <section class="py-12 sm:py-16 bg-zinc-50/70 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center max-w-4xl">
            <span class="text-[10px] font-mono uppercase tracking-[0.25em] text-amber-800 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 px-4 py-1 rounded-full inline-block mb-4 font-semibold">
                Portafolio Exclusivo &bull; Trabajos Reales
            </span>
            <h1 class="font-serif text-3xl sm:text-5xl md:text-6xl font-light text-zinc-900 dark:text-white tracking-wide">
                Montaje de Eventos en Santiago & Regiones <br class="hidden sm:inline">
                <span class="italic font-normal text-amber-700 dark:text-amber-500">Tu Solución Completa</span>
            </h1>
            <p class="text-xs sm:text-sm md:text-base text-zinc-600 dark:text-zinc-300 mt-5 leading-relaxed font-light max-w-3xl mx-auto">
                En {{ $settings->site_name ?? 'Arriendos & Eventos' }}, nos especializamos en el montaje de eventos en Santiago y regiones, proporcionando un servicio integral para tus celebraciones. Desde mesas hasta sillas y otros mobiliarios, nuestras soluciones están diseñadas para que disfrutes de cada momento sin preocupaciones. Ya sea que planifiques un evento corporativo, una boda o una reunión especial, contamos con opciones ideales para cada necesidad. Nuestro enfoque está en asegurar que el montaje de eventos sea sencillo, confiable y con resultados excepcionales. Contáctanos y descubre cómo podemos hacer de tu evento un éxito inolvidable.
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="#proyecto-destacado" class="px-5 py-2.5 rounded-full bg-zinc-900 hover:bg-zinc-800 dark:bg-amber-500 dark:hover:bg-amber-600 text-white dark:text-zinc-950 font-semibold text-xs transition shadow-sm">
                    Explorar Producciones
                </a>
                <a href="https://wa.me/{{ $settings->cleanWhatsappPhone() }}?text={{ rawurlencode('Hola, me gustaría cotizar un montaje completo de evento.') }}" 
                   target="_blank"
                   class="px-5 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition shadow-sm flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.529 1.831.812 2.791.812 3.179 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.768-5.768-5.768zm9.969 5.768c0 5.518-4.482 10-10 10-1.748 0-3.385-.45-4.819-1.242l-5.181 1.356 1.381-5.048c-.902-1.492-1.381-3.21-1.381-5.066 0-5.518 4.482-10 10-10s10 4.482 10 10z"/></svg>
                    <span>Cotizar Montaje por WhatsApp</span>
                </a>
            </div>
        </div>
    </section>

    <!-- PROYECTO PRINCIPAL SPOTLIGHT: T E N O   2 0 2 3 (Inspirado en imagen 1) -->
    @php
        $teno = collect($montajes)->firstWhere('id', 'teno-2023') ?? $montajes[0];
    @endphp
    <section id="proyecto-destacado" class="py-14 sm:py-20 border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-zinc-50/70 dark:bg-zinc-900/60 rounded-3xl border border-zinc-200/90 dark:border-zinc-800 p-6 sm:p-10 shadow-sm">
                
                <!-- Encabezado Editorial Spaced -->
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6 pb-6 border-b border-zinc-200/80 dark:border-zinc-800 mb-8">
                    <div>
                        <h2 class="font-serif text-3xl sm:text-5xl font-light tracking-[0.3em] uppercase text-zinc-900 dark:text-white">
                            T E N O &nbsp; 2 0 2 3
                        </h2>
                        
                        <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-6 text-xs">
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400 block font-medium">Tipo de proyecto</span>
                                <span class="text-zinc-900 dark:text-zinc-100 font-semibold text-sm">{{ $teno['type'] }}</span>
                            </div>
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400 block font-medium">Fecha</span>
                                <span class="text-zinc-900 dark:text-zinc-100 font-semibold text-sm">{{ $teno['year'] }}</span>
                            </div>
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400 block font-medium">Ubicación</span>
                                <span class="text-zinc-900 dark:text-zinc-100 font-semibold text-sm">{{ $teno['location'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="lg:max-w-md lg:text-right space-y-3">
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-300 font-light leading-relaxed">
                            {{ $teno['summary'] }}
                        </p>
                        <button @click="openProject({{ json_encode($teno) }})" 
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-zinc-900 hover:bg-zinc-800 dark:bg-amber-500 dark:hover:bg-amber-600 text-white dark:text-zinc-950 text-xs font-semibold shadow-sm transition active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Ver Galería & Ficha Técnica</span>
                        </button>
                    </div>
                </div>

                <!-- Galería Teno 2023: Foto Principal + Fotos Panorámicas -->
                <div class="space-y-4">
                    <div @click="openProject({{ json_encode($teno) }})" 
                         class="cursor-pointer relative rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 group h-72 sm:h-96 md:h-[480px] shadow-sm">
                        <img src="{{ $teno['cover_image'] }}" 
                             alt="{{ $teno['title'] }}" 
                             class="w-full h-full object-cover group-hover:scale-103 transition-transform duration-700 ease-out">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                            <span class="text-white text-xs font-medium flex items-center gap-1.5 backdrop-blur-md bg-black/40 px-3.5 py-1.5 rounded-full border border-white/20">
                                <span>Mesa Principal de Gala • Click para ampliar</span>
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div @click="openProject({{ json_encode($teno) }})" 
                             class="cursor-pointer relative rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 group h-64 sm:h-80 shadow-sm">
                            <img src="{{ $teno['gallery'][1] ?? $teno['cover_image'] }}" 
                                 alt="{{ $teno['title'] }} Vista Amplia" 
                                 class="w-full h-full object-cover group-hover:scale-103 transition-transform duration-700 ease-out">
                            <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-md text-white text-[11px] px-3 py-1 rounded-full border border-white/20">
                                Vista Panorámica Salón (250 personas)
                            </div>
                        </div>

                        <div @click="openProject({{ json_encode($teno) }})" 
                             class="cursor-pointer relative rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 group h-64 sm:h-80 shadow-sm">
                            <img src="{{ $teno['gallery'][2] ?? $teno['cover_image'] }}" 
                                 alt="{{ $teno['title'] }} Composición Completa" 
                                 class="w-full h-full object-cover group-hover:scale-103 transition-transform duration-700 ease-out">
                            <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-md text-white text-[11px] px-3 py-1 rounded-full border border-white/20">
                                Disposición de Sillas Tiffany & Iluminación
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- GALERÍA GENERAL DE MONTAJES (Inspirado en imagen 2) -->
    <section class="py-14 sm:py-20 bg-zinc-50/50 dark:bg-zinc-950 flex-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-[10px] font-mono uppercase tracking-[0.25em] text-amber-800 dark:text-amber-400 font-bold block mb-2">
                    Colección de Producciones
                </span>
                <h3 class="font-serif text-2xl sm:text-4xl text-zinc-900 dark:text-white font-light">
                    Montajes Destacados de <span class="italic font-normal text-amber-700 dark:text-amber-500">Temporada</span>
                </h3>
                <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 mt-2 font-light">
                    Cada montaje se entrega inspeccionado, sanitizado y con puntualidad garantizada por nuestro equipo de logística.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach (collect($montajes)->where('id', '!=', 'teno-2023') as $m)
                    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200/90 dark:border-zinc-800 overflow-hidden shadow-xs hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                        <div>
                            <div @click="openProject({{ json_encode($m) }})"
                                 class="cursor-pointer relative h-64 sm:h-72 overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                                <img src="{{ $m['cover_image'] }}" 
                                     alt="{{ $m['title'] }}" 
                                     loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                                
                                <span class="absolute top-3 left-3 backdrop-blur-md bg-black/60 text-white text-[10px] font-mono font-medium px-2.5 py-1 rounded-full border border-white/20">
                                    {{ $m['type'] }}
                                </span>

                                <span class="absolute top-3 right-3 backdrop-blur-md bg-white/90 dark:bg-zinc-900/90 text-zinc-900 dark:text-white text-[10px] font-semibold px-2.5 py-1 rounded-full border border-white/50 dark:border-zinc-700">
                                    {{ $m['location'] }}
                                </span>
                            </div>

                            <div class="p-6">
                                <span class="text-[10px] font-mono tracking-widest uppercase text-amber-800 dark:text-amber-400 block mb-1 font-semibold">
                                    {{ $m['capacity'] }} &bull; {{ $m['year'] }}
                                </span>

                                <h4 @click="openProject({{ json_encode($m) }})" 
                                    class="cursor-pointer font-serif font-bold text-zinc-900 dark:text-white text-lg sm:text-xl group-hover:text-amber-800 dark:group-hover:text-amber-400 transition-colors">
                                    {{ $m['title'] }}
                                </h4>

                                <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-2 font-light leading-relaxed line-clamp-3">
                                    {{ $m['description'] }}
                                </p>

                                <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex flex-wrap gap-1.5">
                                    @foreach(array_slice($m['items_used'], 0, 3) as $itemTag)
                                        <span class="text-[10px] bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 px-2 py-0.5 rounded-md font-medium">
                                            {{ $itemTag }}
                                        </span>
                                    @endforeach
                                    @if(count($m['items_used']) > 3)
                                        <span class="text-[10px] text-zinc-400 px-1 py-0.5 font-mono">
                                            +{{ count($m['items_used']) - 3 }} más
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="p-6 pt-0">
                            <button @click="openProject({{ json_encode($m) }})"
                                    class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold bg-zinc-100 hover:bg-zinc-900 dark:bg-zinc-800 dark:hover:bg-amber-500 text-zinc-900 hover:text-white dark:text-zinc-200 dark:hover:text-zinc-950 transition-all duration-200 flex items-center justify-center gap-2">
                                <span>Ver Galería & Ficha Técnica</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- LIGHTBOX MODAL DE PROYECTO -->
    <div x-show="lightboxOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/80 backdrop-blur-md" 
         style="display: none;" 
         @keydown.escape.window="lightboxOpen = false">
        <div @click.outside="lightboxOpen = false" 
             class="bg-white dark:bg-zinc-900 rounded-3xl max-w-4xl w-full max-h-[90vh] overflow-y-auto border border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col">
            
            <div class="p-5 sm:p-6 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-mono uppercase tracking-widest text-amber-700 dark:text-amber-400 font-bold" x-text="selectedProject?.type"></span>
                    <h3 class="font-serif text-xl sm:text-2xl font-bold text-zinc-900 dark:text-white" x-text="selectedProject?.title"></h3>
                </div>
                <button @click="lightboxOpen = false" class="p-2 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-500 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Visor -->
            <div class="relative bg-zinc-950 h-72 sm:h-96 md:h-[420px] flex items-center justify-center overflow-hidden">
                <template x-if="selectedProject && selectedProject.gallery">
                    <img :src="selectedProject.gallery[currentPhotoIndex]" 
                         :alt="selectedProject.title" 
                         class="w-full h-full object-contain">
                </template>
                
                <template x-if="selectedProject && selectedProject.gallery.length > 1">
                    <div>
                        <button @click="prevPhoto()" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/60 hover:bg-black/90 text-white flex items-center justify-center transition border border-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button @click="nextPhoto()" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/60 hover:bg-black/90 text-white flex items-center justify-center transition border border-white/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/70 backdrop-blur-md px-3 py-1 rounded-full text-white text-xs font-mono">
                            <span x-text="(currentPhotoIndex + 1) + ' / ' + selectedProject.gallery.length"></span>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Ficha Técnica -->
            <div class="p-6 sm:p-8 space-y-6">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-700/80 text-xs">
                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400 block font-medium">Ubicación</span>
                        <span class="text-zinc-900 dark:text-white font-semibold" x-text="selectedProject?.location"></span>
                    </div>
                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400 block font-medium">Capacidad</span>
                        <span class="text-zinc-900 dark:text-white font-semibold" x-text="selectedProject?.capacity"></span>
                    </div>
                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400 block font-medium">Temporada</span>
                        <span class="text-zinc-900 dark:text-white font-semibold" x-text="selectedProject?.year"></span>
                    </div>
                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400 block font-medium">Cliente / Tipo</span>
                        <span class="text-zinc-900 dark:text-white font-semibold" x-text="selectedProject?.client"></span>
                    </div>
                </div>

                <div>
                    <h4 class="text-xs font-mono uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-2">Memoria Descriptiva</h4>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed font-light" x-text="selectedProject?.description"></p>
                </div>

                <div>
                    <h4 class="text-xs font-mono uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-2">Mobiliario & Menaje Implementado</h4>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="item in selectedProject?.items_used" :key="item">
                            <span class="text-xs px-3 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 font-medium" x-text="item"></span>
                        </template>
                    </div>
                </div>

                <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <a :href="'https://wa.me/{{ $settings->cleanWhatsappPhone() }}?text=' + encodeURIComponent('Hola, me gustaría cotizar un montaje similar a: ' + (selectedProject?.title ?? 'Montaje de Eventos'))"
                       target="_blank"
                       class="w-full sm:w-auto px-6 py-3 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.529 1.831.812 2.791.812 3.179 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.768-5.768-5.768zm9.969 5.768c0 5.518-4.482 10-10 10-1.748 0-3.385-.45-4.819-1.242l-5.181 1.356 1.381-5.048c-.902-1.492-1.381-3.21-1.381-5.066 0-5.518 4.482-10 10-10s10 4.482 10 10z"/></svg>
                        <span>Cotizar este Montaje por WhatsApp</span>
                    </a>

                    <a href="{{ route('catalog.index') }}#catalogo" 
                       class="w-full sm:w-auto px-6 py-3 rounded-full bg-zinc-900 hover:bg-zinc-800 dark:bg-amber-500 dark:hover:bg-amber-600 text-white dark:text-zinc-950 font-semibold text-xs transition shadow-sm flex items-center justify-center gap-2">
                        <span>Ver Piezas en el Catálogo &rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-zinc-900 text-white border-t border-zinc-800 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-6 text-xs text-zinc-400">
            <div>
                <p>&copy; {{ date('Y') }} {{ $settings->site_name ?? 'Arriendos & Eventos' }}. Todos los derechos reservados.</p>
                <p class="mt-1 font-mono text-[11px]">{{ $settings->site_tagline ?? 'Banquetería, Mobiliario & Vajilla Premium' }}</p>
            </div>
            <div class="flex items-center gap-6 font-medium">
                <a href="{{ route('catalog.index') }}" class="hover:text-white transition">Inicio</a>
                <a href="{{ route('catalog.index') }}#catalogo" class="hover:text-white transition">Catálogo</a>
                <a href="{{ route('catalog.montajes') }}" class="hover:text-white transition font-bold text-amber-400">Montajes</a>
                <a href="{{ route('catalog.index') }}#envios" class="hover:text-white transition">Envíos</a>
                <a href="{{ route('catalog.index') }}#contacto" class="hover:text-white transition">Contacto</a>
            </div>
        </div>
    </footer>

</body>
</html>
