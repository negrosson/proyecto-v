<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Arriendos & Eventos | Mobiliario, Vajilla & Ambientación de Gala</title>

    <!-- Google Fonts: Plus Jakarta Sans (UI) & Cormorant Garamond (Editorial Luxury) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS y Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Cormorant Garamond"', 'serif'],
                    },
                    colors: {
                        obsidian: {
                            DEFAULT: '#0B0C0E',
                            card: '#121418',
                            hover: '#191C22',
                        },
                        gold: {
                            300: '#FDE68A',
                            400: '#FCD34D',
                            500: '#F59E0B',
                            600: '#D97706',
                        }
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Perspectiva y Aceleración 3D para Tarjetas */
        .perspective-1000 { perspective: 1000px; }
        .perspective-1500 { perspective: 1500px; }
        .preserve-3d { transform-style: preserve-3d; }
        .backface-hidden { backface-visibility: hidden; }

        /* Efecto Podio Escénico 3D con Iluminación Cenital */
        .podium-stage {
            position: relative;
            background: radial-gradient(ellipse at 50% 100%, rgba(245, 158, 11, 0.25) 0%, rgba(18, 20, 24, 0.95) 65%, #0B0C0E 100%);
            box-shadow: inset 0 -20px 30px rgba(0, 0, 0, 0.85);
        }

        .podium-ring {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%) rotateX(68deg);
            width: 78%;
            height: 48px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.35) 0%, transparent 70%);
            border: 1.5px solid rgba(245, 158, 11, 0.45);
            box-shadow: 0 0 25px rgba(245, 158, 11, 0.4);
            pointer-events: none;
        }

        .spotlight-beam {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 85%;
            height: 65%;
            background: radial-gradient(ellipse at 50% 0%, rgba(253, 230, 138, 0.2) 0%, transparent 75%);
            pointer-events: none;
        }

        /* Animación de Giro Suave 3D para el Visor */
        @keyframes turntable3d {
            0% { transform: rotateY(0deg) scale(1); }
            50% { transform: rotateY(180deg) scale(1.04); }
            100% { transform: rotateY(360deg) scale(1); }
        }
        .animate-turntable-3d {
            animation: turntable3d 12s infinite linear;
            transform-style: preserve-3d;
        }

        /* Paginación Dark Luxury */
        .custom-pagination nav svg { width: 1.25rem; height: 1.25rem; }
        .custom-pagination nav span, 
        .custom-pagination nav a {
            background-color: rgba(255, 255, 255, 0.05) !important;
            color: #d6d3d1 !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            border-radius: 0.75rem !important;
            margin: 0 0.2rem !important;
            transition: all 0.2s ease !important;
        }
        .custom-pagination nav a:hover {
            background-color: rgba(245, 158, 11, 0.2) !important;
            color: #fde68a !important;
            border-color: rgba(245, 158, 11, 0.4) !important;
        }
        .custom-pagination nav span[aria-current="page"] span {
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
            color: #0b0c0e !important;
            font-weight: 700 !important;
            border-color: #f59e0b !important;
        }
    </style>
</head>
<body class="bg-obsidian text-stone-100 font-sans min-h-screen flex flex-col antialiased selection:bg-gold-500 selection:text-stone-950 relative overflow-x-hidden pb-20 sm:pb-0" 
      x-data="quoteCart()" 
      x-cloak>

    <!-- Luces Ambientales de Fondo (Glow Auras) -->
    <div class="fixed top-0 left-1/4 -translate-x-1/2 w-[650px] h-[550px] bg-gold-500/10 rounded-full blur-[150px] pointer-events-none -z-10"></div>
    <div class="fixed top-36 right-0 w-[550px] h-[500px] bg-purple-600/10 rounded-full blur-[160px] pointer-events-none -z-10"></div>
    <div class="fixed bottom-0 left-1/3 w-[600px] h-[500px] bg-amber-600/8 rounded-full blur-[160px] pointer-events-none -z-10"></div>

    <!-- Topbar & Barra de Navegación Principal Responsiva -->
    <header class="bg-[#0B0C0E]/90 backdrop-blur-xl border-b border-white/10 sticky top-0 z-40 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between gap-4">
            
            <!-- Brand Monograma Luxury -->
            <a href="{{ route('catalog.index') }}" class="group flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-gold-400 via-gold-500 to-amber-700 p-0.5 shadow-lg shadow-gold-500/20 group-hover:scale-105 transition-transform duration-300">
                    <div class="w-full h-full bg-[#0B0C0E] rounded-[10px] flex items-center justify-center">
                        <span class="text-gold-400 font-serif font-bold text-xl leading-none">A</span>
                    </div>
                </div>
                <div>
                    <span class="text-lg sm:text-xl font-serif font-bold tracking-widest text-white uppercase block leading-none group-hover:text-gold-300 transition-colors">
                        Arriendos & Eventos
                    </span>
                    <span class="text-[9px] tracking-[0.22em] text-gold-400/80 uppercase font-mono mt-1 block">
                        Colección Exclusiva & Menaje
                    </span>
                </div>
            </a>

            <!-- Enlaces de Navegación de Escritorio -->
            <nav class="hidden md:flex items-center gap-6 text-xs uppercase tracking-wider font-medium text-stone-300">
                <a href="{{ route('catalog.index') }}" class="hover:text-gold-300 transition-colors py-1 relative {{ !request('categoria') && !request('buscar') ? 'text-gold-400 font-bold' : '' }}">
                    Inicio
                </a>
                <a href="#destacados" class="hover:text-gold-300 transition-colors py-1">
                    Destacados
                </a>
                <a href="#mobiliario-rotacion" class="hover:text-gold-300 transition-colors py-1">
                    Mobiliario 3D
                </a>
                <a href="#catalogo" class="hover:text-gold-300 transition-colors py-1">
                    Catálogo
                </a>
                <a href="https://wa.me/{{ config('services.whatsapp.quote_phone', '56912345678') }}?text={{ rawurlencode('Hola, me comunico desde el sitio web de Arriendos & Eventos.') }}" 
                   target="_blank" 
                   class="hover:text-emerald-400 transition-colors py-1 flex items-center gap-1.5 text-stone-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Contacto WhatsApp
                </a>
            </nav>

            <!-- Acciones Header: Botón Cotización & Hamburguesa Móvil -->
            <div class="flex items-center gap-2.5">
                <div x-data="{ bump: false }" 
                     x-init="$watch('countItems()', () => { bump = true; setTimeout(() => bump = false, 200) })">
                    <button @click="cartOpen = true" 
                            id="open-quote-drawer-btn"
                            class="relative inline-flex items-center gap-2 bg-white/[0.05] hover:bg-white/[0.1] border border-white/10 hover:border-gold-500/40 text-stone-200 hover:text-white px-3.5 sm:px-4 py-2 rounded-full shadow-sm hover:shadow-lg hover:shadow-gold-500/10 transition-all duration-300 active:scale-95 group backdrop-blur-md">
                        <svg class="w-4 h-4 text-gold-400 group-hover:rotate-6 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span class="text-xs font-semibold tracking-wide hidden xs:inline">Cotización</span>
                        <span class="transition-transform duration-200 font-bold text-xs px-2 py-0.5 rounded-full" 
                              :class="{ 'scale-125 bg-gold-400 text-stone-950 shadow-md shadow-gold-400/50': bump, 'scale-100 bg-gradient-to-r from-gold-400 to-amber-500 text-stone-950': !bump }" 
                              x-text="countItems()"></span>
                    </button>
                </div>

                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="md:hidden p-2 rounded-xl bg-white/[0.05] hover:bg-white/10 border border-white/10 text-stone-300 hover:text-white transition">
                    <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menú Desplegable Móvil -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             @click.outside="mobileMenuOpen = false"
             class="md:hidden bg-[#101216]/98 backdrop-blur-2xl border-b border-white/10 px-5 py-4 space-y-3"
             style="display: none;">
            <nav class="flex flex-col space-y-2.5 text-sm font-medium text-stone-200">
                <a @click="mobileMenuOpen = false" href="{{ route('catalog.index') }}" class="px-3 py-2 rounded-lg hover:bg-white/5 transition flex items-center justify-between">
                    <span>Inicio</span>
                    <span class="text-xs text-gold-400 font-mono">01</span>
                </a>
                <a @click="mobileMenuOpen = false" href="#destacados" class="px-3 py-2 rounded-lg hover:bg-white/5 transition flex items-center justify-between">
                    <span>Productos Destacados</span>
                    <span class="text-xs text-gold-400 font-mono">02</span>
                </a>
                <a @click="mobileMenuOpen = false" href="#mobiliario-rotacion" class="px-3 py-2 rounded-lg hover:bg-white/5 transition flex items-center justify-between">
                    <span>Mobiliario 3D</span>
                    <span class="text-xs text-gold-400 font-mono">03</span>
                </a>
                <a @click="mobileMenuOpen = false" href="#catalogo" class="px-3 py-2 rounded-lg hover:bg-white/5 transition flex items-center justify-between">
                    <span>Catálogo Completo</span>
                    <span class="text-xs text-gold-400 font-mono">04</span>
                </a>
                <a href="https://wa.me/{{ config('services.whatsapp.quote_phone', '56912345678') }}?text={{ rawurlencode('Hola, me comunico desde el sitio web de Arriendos & Eventos.') }}" 
                   target="_blank" 
                   class="px-3 py-2 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 transition flex items-center justify-between">
                    <span>Atención por WhatsApp</span>
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.529 1.831.812 2.791.812 3.179 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.768-5.768-5.768zm9.969 5.768c0 5.518-4.482 10-10 10-1.748 0-3.385-.45-4.819-1.242l-5.181 1.356 1.381-5.048c-.902-1.492-1.381-3.21-1.381-5.066 0-5.518 4.482-10 10-10s10 4.482 10 10z"/></svg>
                </a>
            </nav>
        </div>
    </header>

    @if(!request('categoria') && !request('buscar'))
        <!-- Hero Banner Editorial -->
        <section class="relative border-b border-white/10 py-12 sm:py-20 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <div class="inline-flex items-center gap-2 bg-gradient-to-r from-gold-500/15 via-gold-500/10 to-transparent border border-gold-500/30 px-4 py-1.5 rounded-full mb-5 backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-gold-400 animate-pulse"></span>
                    <span class="text-[10px] sm:text-xs font-mono font-medium tracking-widest uppercase text-gold-300">
                        Catálogo Oficial &bull; Temporada 2026
                    </span>
                </div>

                <h1 class="font-serif text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-light tracking-tight text-white max-w-4xl mx-auto leading-[1.15]">
                    Equipamiento y ambientación para 
                    <span class="italic font-normal bg-gradient-to-r from-gold-300 via-gold-400 to-amber-500 bg-clip-text text-transparent">ocasiones extraordinarias</span>
                </h1>

                <p class="text-xs sm:text-sm text-stone-400 mt-4 sm:mt-5 max-w-2xl mx-auto font-light leading-relaxed">
                    Mobiliario de etiqueta, vajilla de porcelana, cristalería fina y mantelería para bodas, celebraciones y banquetes corporativos con disponibilidad garantizada.
                </p>

                <!-- Badges de Confianza Glassmorphism -->
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3 sm:gap-4 text-xs text-stone-300">
                    <div class="inline-flex items-center gap-2 bg-white/[0.04] border border-white/10 backdrop-blur-md px-4 py-2 rounded-full shadow-lg shadow-black/20">
                        <span class="w-4 h-4 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-[10px] font-bold">✓</span>
                        <span class="font-medium text-stone-200">Despacho en fecha</span>
                    </div>
                    <div class="inline-flex items-center gap-2 bg-white/[0.04] border border-white/10 backdrop-blur-md px-4 py-2 rounded-full shadow-lg shadow-black/20">
                        <span class="w-4 h-4 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-[10px] font-bold">✓</span>
                        <span class="font-medium text-stone-200">Vajilla sanitizada</span>
                    </div>
                    <div class="inline-flex items-center gap-2 bg-white/[0.04] border border-white/10 backdrop-blur-md px-4 py-2 rounded-full shadow-lg shadow-black/20">
                        <span class="w-4 h-4 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-[10px] font-bold">✓</span>
                        <span class="font-medium text-stone-200">Atención directa</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECCIÓN 1: CARRUSEL ROTATORIO CON PODIOS 3D ("DISFRUTA DE NUESTRO MOBILIARIO") -->
        <section id="mobiliario-rotacion" 
                 class="py-14 sm:py-20 border-b border-white/10 relative overflow-hidden bg-gradient-to-b from-transparent via-white/[0.02] to-transparent"
                 x-data="{
                     currentIndex: 0,
                     totalSlides: {{ ceil(count($showcaseProducts) / 3) }},
                     autoplayTimer: null,
                     init() {
                         this.startAutoplay();
                     },
                     startAutoplay() {
                         this.autoplayTimer = setInterval(() => {
                             this.next();
                         }, 4500);
                     },
                     stopAutoplay() {
                         clearInterval(this.autoplayTimer);
                     },
                     next() {
                         this.currentIndex = (this.currentIndex + 1) % this.totalSlides;
                     },
                     prev() {
                         this.currentIndex = (this.currentIndex - 1 + this.totalSlides) % this.totalSlides;
                     }
                 }"
                 @mouseenter="stopAutoplay()" 
                 @mouseleave="startAutoplay()">
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Título Editorial con Badges 3D -->
                <div class="text-center max-w-3xl mx-auto mb-12">
                    <span class="text-[10px] font-mono uppercase tracking-[0.25em] text-gold-400 bg-gold-500/10 border border-gold-500/20 px-3.5 py-1 rounded-full inline-flex items-center gap-1.5 mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-gold-400 animate-ping"></span>
                        <span>Showroom &bull; Efecto 3D Interactivo</span>
                    </span>
                    <h2 class="font-serif text-2xl sm:text-4xl md:text-5xl font-light text-white tracking-wide uppercase">
                        Disfruta de Nuestro <span class="italic font-normal bg-gradient-to-r from-gold-300 via-gold-400 to-amber-500 bg-clip-text text-transparent">Mobiliario</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-stone-400 mt-3 font-light">
                        Mueve tu cursor sobre cada pieza para interactuar con su perspectiva tridimensional. Haz clic en cualquier modelo para ver su ficha completa.
                    </p>
                </div>

                <!-- Carrusel 3D de Tarjetas con Inclinación Háptica -->
                <div class="relative">
                    <div class="overflow-hidden py-6 px-1">
                        <div class="flex transition-transform duration-700 ease-out" 
                             :style="`transform: translateX(-${currentIndex * 100}%);`">
                            @foreach ($showcaseProducts->chunk(3) as $chunk)
                                <div class="w-full shrink-0 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-1">
                                    @foreach ($chunk as $item)
                                        <div x-data="card3d()" 
                                             @mousemove="handleMouseMove($event)" 
                                             @mouseleave="handleMouseLeave()"
                                             class="perspective-1000 group">
                                            
                                            <!-- Tarjeta con Transformación 3D Dinámica -->
                                            <div :style="`transform: rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(${isHovered ? 1.03 : 1}, ${isHovered ? 1.03 : 1}, 1);`"
                                                 class="preserve-3d relative bg-gradient-to-b from-white/[0.08] to-white/[0.02] border border-white/10 hover:border-gold-500/60 rounded-3xl p-5 text-center transition-all duration-150 ease-out shadow-xl hover:shadow-2xl hover:shadow-gold-500/20 backdrop-blur-xl flex flex-col justify-between overflow-hidden cursor-pointer"
                                                 @click="openDetail({
                                                     id: {{ $item->id }},
                                                     name: '{{ addslashes($item->name) }}',
                                                     code: {{ $item->code }},
                                                     price: {{ $item->rental_price }},
                                                     stock: {{ $item->stock }},
                                                     category: '{{ addslashes($item->category->name ?? 'Mobiliario') }}',
                                                     description: '{{ addslashes($item->description ?? 'Pieza de alta calidad para ambientación de bodas y banquetes. Se entrega completamente sanitizado e inspeccionado.') }}',
                                                     is_sale: {{ $item->is_sale ? 'true' : 'false' }},
                                                     image: '{{ $item->image_url ?? '' }}'
                                                 })">
                                                
                                                <!-- Reflejo Especular Holográfico (Glint Sheen) -->
                                                <div class="pointer-events-none absolute inset-0 z-30 transition-opacity duration-200 rounded-3xl"
                                                     :style="`opacity: ${glareOpacity}; background: radial-gradient(circle at ${glareX}% ${glareY}%, rgba(255, 255, 255, 0.35) 0%, rgba(245, 158, 11, 0.2) 30%, transparent 70%);`"></div>

                                                <!-- Header Superior de la Tarjeta -->
                                                <div class="flex justify-between items-center text-xs w-full mb-3 relative z-20">
                                                    <span class="text-[10px] font-mono bg-black/80 border border-white/15 text-gold-300 px-2.5 py-0.5 rounded-lg shadow-md">
                                                        Cód: {{ $item->code }}
                                                    </span>
                                                    <span class="text-[10px] font-semibold text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-full border border-emerald-500/20">
                                                        {{ $item->stock }} un. disponibles
                                                    </span>
                                                </div>

                                                <!-- Podio Escénico 3D con Iluminación y Sombra -->
                                                <div class="podium-stage relative h-52 sm:h-60 flex items-center justify-center rounded-2xl overflow-hidden border border-white/10 group-hover:border-gold-500/40 transition-colors preserve-3d">
                                                    <!-- Foco Cenital -->
                                                    <div class="spotlight-beam"></div>
                                                    <!-- Anillo de Luz del Podio -->
                                                    <div class="podium-ring"></div>
                                                    
                                                    @if($item->image_url)
                                                        <img src="{{ $item->image_url }}" 
                                                             alt="{{ $item->name }}" 
                                                             class="relative z-10 max-h-48 sm:max-h-52 w-auto object-contain transition-transform duration-300 ease-out group-hover:scale-110 drop-shadow-[0_20px_25px_rgba(0,0,0,0.95)]"
                                                             :style="`transform: translateZ(45px) scale(${isHovered ? 1.08 : 1});`">
                                                    @else
                                                        <!-- Emblema 3D Isométrico Iluminado de Mobiliario -->
                                                        <div class="relative z-10 flex flex-col items-center justify-center text-gold-400 group-hover:text-gold-300 transition-colors"
                                                             :style="`transform: translateZ(35px);`">
                                                            <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-gold-500/20 to-amber-600/30 border border-gold-500/40 flex items-center justify-center shadow-xl shadow-gold-500/15 animate-bounce">
                                                                <svg class="w-10 h-10 stroke-current" fill="none" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                                </svg>
                                                            </div>
                                                            <span class="text-[10px] font-mono mt-3 text-gold-300/80 tracking-wider uppercase font-semibold">Exhibición 3D</span>
                                                        </div>
                                                    @endif

                                                    <!-- Indicador Flotante 3D -->
                                                    <span class="absolute bottom-2.5 right-3 text-[9px] font-mono text-gold-400/90 bg-black/70 px-2 py-0.5 rounded-full border border-gold-500/30 flex items-center gap-1 z-20">
                                                        <span class="w-1 h-1 rounded-full bg-gold-400 animate-ping"></span>
                                                        <span>3D Tilt</span>
                                                    </span>
                                                </div>

                                                <!-- Detalles del Mueble -->
                                                <div class="mt-4 text-left relative z-20">
                                                    <h3 class="font-serif font-bold text-base sm:text-lg text-white group-hover:text-gold-300 transition-colors line-clamp-1">
                                                        {{ $item->name }}
                                                    </h3>
                                                    <p class="text-xs text-stone-400 mt-1 line-clamp-1 font-light">
                                                        {{ $item->description ?? 'Pieza de alta calidad para ambientación de bodas y banquetes.' }}
                                                    </p>
                                                    
                                                    <div class="mt-4 pt-3.5 border-t border-white/10 flex items-center justify-between">
                                                        <div>
                                                            <span class="text-[9px] uppercase tracking-wider text-stone-400 block font-normal">Arriendo:</span>
                                                            <span class="text-lg font-bold font-mono bg-gradient-to-r from-gold-300 via-gold-400 to-amber-500 bg-clip-text text-transparent">
                                                                ${{ number_format($item->rental_price, 0, ',', '.') }}
                                                            </span>
                                                        </div>
                                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-gold-400 group-hover:text-gold-300 group-hover:translate-x-1 transition-transform">
                                                            <span>Ver detalle</span>
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Controles de Flechas de Navegación -->
                    <button @click="prev()" 
                            class="absolute -left-3 sm:-left-5 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-stone-900/90 border border-white/15 text-white hover:text-gold-400 hover:border-gold-500/50 flex items-center justify-center transition shadow-2xl z-20 hover:scale-105 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button @click="next()" 
                            class="absolute -right-3 sm:-right-5 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-stone-900/90 border border-white/15 text-white hover:text-gold-400 hover:border-gold-500/50 flex items-center justify-center transition shadow-2xl z-20 hover:scale-105 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </button>

                    <!-- Indicadores de Puntos (Dots) -->
                    <div class="flex items-center justify-center gap-2 mt-6">
                        @for ($i = 0; $i < ceil(count($showcaseProducts) / 3); $i++)
                            <button @click="currentIndex = {{ $i }}" 
                                    class="h-1.5 rounded-full transition-all duration-300" 
                                    :class="currentIndex === {{ $i }} ? 'w-8 bg-gold-400 shadow-md shadow-gold-400/50' : 'w-2 bg-white/20 hover:bg-white/40'"></button>
                        @endfor
                    </div>
                </div>
            </div>
        </section>

        <!-- SECCIÓN 2: PRODUCTOS DESTACADOS CON EFECTO 3D SINCRONIZADOS CON LA BD -->
        <section id="destacados" class="py-14 sm:py-20 border-b border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
                    <div>
                        <span class="text-[10px] font-mono uppercase tracking-[0.25em] text-gold-400 bg-gold-500/10 border border-gold-500/20 px-3.5 py-1 rounded-full inline-block mb-3">
                            Selección de Gala
                        </span>
                        <h2 class="font-serif text-2xl sm:text-4xl font-light text-white tracking-wide">
                            Productos <span class="italic font-normal bg-gradient-to-r from-gold-300 via-gold-400 to-amber-500 bg-clip-text text-transparent">Destacados</span>
                        </h2>
                        <p class="text-xs sm:text-sm text-stone-400 mt-2 font-light max-w-xl">
                            Artículos icónicos y de alta demanda sincronizados directamente con nuestro inventario en tiempo real.
                        </p>
                    </div>

                    <a href="#catalogo" class="self-start sm:self-auto inline-flex items-center gap-2 text-xs font-semibold text-gold-400 hover:text-gold-300 bg-white/[0.04] border border-white/10 hover:border-gold-500/40 px-4 py-2.5 rounded-xl transition">
                        <span>Explorar todo el catálogo</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach ($featuredProducts as $feat)
                        <div x-data="card3d()" 
                             @mousemove="handleMouseMove($event)" 
                             @mouseleave="handleMouseLeave()" 
                             class="perspective-1000 group">
                            
                            <div :style="`transform: rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(${isHovered ? 1.02 : 1}, ${isHovered ? 1.02 : 1}, 1);`"
                                 class="preserve-3d relative bg-gradient-to-b from-white/[0.07] to-white/[0.02] border border-gold-500/30 hover:border-gold-500/70 rounded-2xl overflow-hidden transition-all duration-150 ease-out shadow-xl hover:shadow-2xl hover:shadow-gold-500/20 flex flex-col justify-between backdrop-blur-md">
                                
                                <!-- Brillo Especular -->
                                <div class="pointer-events-none absolute inset-0 z-30 transition-opacity duration-200"
                                     :style="`opacity: ${glareOpacity}; background: radial-gradient(circle at ${glareX}% ${glareY}%, rgba(255, 255, 255, 0.3) 0%, rgba(245, 158, 11, 0.15) 30%, transparent 70%);`"></div>

                                <div>
                                    <!-- Podio de Imagen 3D -->
                                    <div @click="openDetail({
                                            id: {{ $feat->id }},
                                            name: '{{ addslashes($feat->name) }}',
                                            code: {{ $feat->code }},
                                            price: {{ $feat->rental_price }},
                                            stock: {{ $feat->stock }},
                                            category: '{{ addslashes($feat->category->name ?? 'Catálogo') }}',
                                            description: '{{ addslashes($feat->description ?? 'Artículo de alta gama para ambientación de bodas y banquetes. Se entrega completamente sanitizado e inspeccionado.') }}',
                                            is_sale: {{ $feat->is_sale ? 'true' : 'false' }},
                                            image: '{{ $feat->image_url ?? '' }}'
                                         })" 
                                         class="cursor-pointer podium-stage h-44 sm:h-52 flex items-center justify-center overflow-hidden relative border-b border-white/5 preserve-3d">
                                        
                                        <div class="spotlight-beam"></div>
                                        <div class="podium-ring"></div>

                                        @if($feat->image_url)
                                            <img src="{{ $feat->image_url }}" 
                                                 alt="{{ $feat->name }}" 
                                                 class="relative z-10 max-h-40 sm:max-h-48 w-auto object-contain transition-transform duration-300 ease-out group-hover:scale-110 drop-shadow-[0_15px_20px_rgba(0,0,0,0.9)]"
                                                 :style="`transform: translateZ(40px) scale(${isHovered ? 1.06 : 1});`">
                                        @else
                                            <div class="text-stone-600 flex flex-col items-center select-none group-hover:scale-105 transition-transform duration-500 ease-out">
                                                <svg class="w-12 h-12 stroke-gold-400/60" fill="none" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                                </svg>
                                                <span class="text-[10px] font-mono mt-1 text-gold-400/70">Destacado</span>
                                            </div>
                                        @endif

                                        <span class="absolute top-3 left-3 bg-black/80 backdrop-blur-md border border-white/10 text-gold-300 text-[10px] font-mono font-medium px-2.5 py-0.5 rounded-md shadow-lg z-20">
                                            Cód: {{ $feat->code }}
                                        </span>

                                        <span class="absolute top-3 right-3 bg-gradient-to-r from-gold-400 to-amber-500 text-stone-950 text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-md shadow-lg z-20 flex items-center gap-1">
                                            ★ Destacado
                                        </span>
                                    </div>

                                    <!-- Detalles del Producto -->
                                    <div class="p-4 sm:p-5 relative z-20">
                                        <span class="text-[10px] font-mono tracking-widest uppercase text-gold-400 bg-gold-500/10 border border-gold-500/20 px-2.5 py-0.5 rounded-full inline-block">
                                            {{ $feat->category->name ?? 'Catálogo' }}
                                        </span>
                                        
                                        <h3 @click="openDetail({
                                                id: {{ $feat->id }},
                                                name: '{{ addslashes($feat->name) }}',
                                                code: {{ $feat->code }},
                                                price: {{ $feat->rental_price }},
                                                stock: {{ $feat->stock }},
                                                category: '{{ addslashes($feat->category->name ?? 'Catálogo') }}',
                                                description: '{{ addslashes($feat->description ?? 'Artículo de alta gama para ambientación de bodas y banquetes. Se entrega completamente sanitizado e inspeccionado.') }}',
                                                is_sale: {{ $feat->is_sale ? 'true' : 'false' }},
                                                image: '{{ $feat->image_url ?? '' }}'
                                             })"
                                            class="cursor-pointer font-serif font-bold text-white text-base sm:text-lg mt-2.5 leading-snug line-clamp-2 group-hover:text-gold-300 transition-colors" title="{{ $feat->name }}">
                                            {{ $feat->name }}
                                        </h3>
                                        
                                        <div class="mt-4 pt-3.5 border-t border-white/10 flex items-baseline justify-between">
                                            <div>
                                                <span class="text-[10px] text-stone-400 block font-normal uppercase tracking-wider">Valor arriendo:</span>
                                                <p class="text-lg sm:text-xl font-bold bg-gradient-to-r from-gold-300 via-gold-400 to-amber-500 bg-clip-text text-transparent tracking-tight mt-0.5 font-mono">
                                                    ${{ number_format($feat->rental_price, 0, ',', '.') }}
                                                    <span class="text-xs font-normal text-stone-400">/ un.</span>
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-[10px] text-stone-400 block font-normal uppercase tracking-wider">Stock:</span>
                                                <span class="text-xs font-mono font-semibold {{ $feat->stock > 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                                    {{ $feat->stock > 0 ? $feat->stock . ' un.' : 'Agotado' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones de Acción -->
                                <div class="p-4 sm:p-5 pt-0 flex gap-2 relative z-20">
                                    <button @click="openDetail({
                                                id: {{ $feat->id }},
                                                name: '{{ addslashes($feat->name) }}',
                                                code: {{ $feat->code }},
                                                price: {{ $feat->rental_price }},
                                                stock: {{ $feat->stock }},
                                                category: '{{ addslashes($feat->category->name ?? 'Catálogo') }}',
                                                description: '{{ addslashes($feat->description ?? 'Artículo de alta gama para ambientación de bodas y banquetes. Se entrega completamente sanitizado e inspeccionado.') }}',
                                                is_sale: {{ $feat->is_sale ? 'true' : 'false' }},
                                                image: '{{ $feat->image_url ?? '' }}'
                                             })"
                                            class="p-2.5 rounded-xl bg-white/[0.05] hover:bg-white/[0.12] text-stone-300 hover:text-white border border-white/10 transition flex items-center justify-center shrink-0" 
                                            title="Ver detalle 3D">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    
                                    @if($feat->stock > 0)
                                        <div x-data="{ added: false }" class="flex-1">
                                            <button @click="addItem({{ $feat->id }}, '{{ addslashes($feat->name) }}', {{ $feat->rental_price }}, {{ $feat->stock }}, {{ $feat->code }}); added = true; setTimeout(() => added = false, 1200)"
                                                    :class="added ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/30 border-emerald-500' : 'bg-white/[0.08] hover:bg-gold-500 hover:text-stone-950 text-white border-white/15 hover:border-gold-400'"
                                                    class="w-full py-2.5 px-3 rounded-xl text-xs font-semibold transition-all duration-300 flex items-center justify-center gap-1.5 active:scale-98 shadow-sm border">
                                                <template x-if="!added">
                                                    <span class="flex items-center gap-1.5">
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
                                        </div>
                                    @else
                                        <button disabled class="flex-1 bg-white/[0.03] text-stone-500 border border-white/5 text-xs font-semibold py-2.5 rounded-xl cursor-not-allowed">
                                            Sin stock
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Contenido Principal: Filtros y Catálogo Completo -->
    <main id="catalogo" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-1 w-full">
        
        <div class="mb-8">
            <span class="text-[10px] font-mono uppercase tracking-[0.25em] text-gold-400 bg-gold-500/10 border border-gold-500/20 px-3.5 py-1 rounded-full inline-block mb-2">
                Inventario Oficial
            </span>
            <h2 class="font-serif text-2xl sm:text-4xl font-light text-white tracking-wide">
                Catálogo de <span class="italic font-normal bg-gradient-to-r from-gold-300 via-gold-400 to-amber-500 bg-clip-text text-transparent">Arriendos</span>
            </h2>
            <p class="text-xs sm:text-sm text-stone-400 mt-1 font-light">
                Filtra por categoría o busca por código/nombre del artículo para armar tu lista de cotización instantánea.
            </p>
        </div>

        <!-- Barra de Categorías Horizontales -->
        <nav class="flex gap-2 overflow-x-auto pb-4 mb-6 no-scrollbar items-center">
            <a href="{{ route('catalog.index', array_filter(['buscar' => request('buscar')])) }}#catalogo" 
               class="px-5 py-2.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all duration-300 {{ !request('categoria') ? 'bg-gradient-to-r from-gold-400 to-amber-500 text-stone-950 font-bold shadow-lg shadow-gold-500/20 scale-102' : 'bg-white/[0.04] text-stone-400 hover:text-white hover:bg-white/[0.08] border border-white/5 backdrop-blur-md' }}">
                Todos los productos
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('catalog.index', array_filter(['categoria' => $cat->slug, 'buscar' => request('buscar')])) }}#catalogo" 
                   class="px-5 py-2.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all duration-300 {{ request('categoria') === $cat->slug ? 'bg-gradient-to-r from-gold-400 to-amber-500 text-stone-950 font-bold shadow-lg shadow-gold-500/20 scale-102' : 'bg-white/[0.04] text-stone-400 hover:text-white hover:bg-white/[0.08] border border-white/5 backdrop-blur-md' }}">
                    {{ $cat->name }}
                    <span class="ml-1 opacity-75 font-normal">({{ $cat->products_count }})</span>
                </a>
            @endforeach
        </nav>

        <!-- Barra de Búsqueda y Limpieza de Filtros -->
        <div class="mb-10 bg-white/[0.03] border border-white/10 p-3.5 sm:p-4 rounded-2xl shadow-xl backdrop-blur-xl flex flex-col sm:flex-row justify-between items-center gap-3">
            <form method="GET" action="{{ route('catalog.index') }}#catalogo" class="flex gap-2.5 w-full sm:max-w-lg">
                @if(request('categoria'))
                    <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                @endif
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-500">
                        <svg class="w-4 h-4 text-gold-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" 
                           placeholder="Buscar por nombre o código (ej: 1030, copa, mesa)..." 
                           class="w-full bg-white/[0.04] border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-xs sm:text-sm text-white placeholder-stone-500 focus:outline-none focus:ring-2 focus:ring-gold-500/20 focus:border-gold-500/60 focus:bg-white/[0.07] transition duration-200" />
                </div>
                <button type="submit" class="bg-gradient-to-r from-gold-400 to-amber-500 hover:from-gold-300 hover:to-amber-400 text-stone-950 font-bold px-6 py-2.5 rounded-xl text-xs sm:text-sm transition duration-200 active:scale-95 shadow-md shadow-gold-500/20">
                    Buscar
                </button>
            </form>

            @if(request('buscar') || request('categoria'))
                <div class="flex items-center gap-2 self-start sm:self-center">
                    <a href="{{ route('catalog.index') }}#catalogo" class="group inline-flex items-center gap-1.5 text-xs font-semibold text-rose-400 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 px-3.5 py-2 rounded-full transition-all duration-200">
                        <span>Limpiar filtros</span>
                        <svg class="w-3.5 h-3.5 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                </div>
            @endif
        </div>

        <!-- Grid de Cards de Producto con Efecto 3D Tilt -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @forelse ($products as $prod)
                <div x-data="card3d()" 
                     @mousemove="handleMouseMove($event)" 
                     @mouseleave="handleMouseLeave()" 
                     class="perspective-1000 group">
                    
                    <div :style="`transform: rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(${isHovered ? 1.02 : 1}, ${isHovered ? 1.02 : 1}, 1);`"
                         class="preserve-3d relative bg-gradient-to-b from-white/[0.06] to-white/[0.02] border border-white/10 hover:border-gold-500/50 rounded-2xl overflow-hidden transition-all duration-150 ease-out shadow-lg hover:shadow-2xl hover:shadow-gold-500/15 flex flex-col justify-between backdrop-blur-md">
                        
                        <!-- Brillo Holográfico -->
                        <div class="pointer-events-none absolute inset-0 z-30 transition-opacity duration-200"
                             :style="`opacity: ${glareOpacity}; background: radial-gradient(circle at ${glareX}% ${glareY}%, rgba(255, 255, 255, 0.3) 0%, rgba(245, 158, 11, 0.15) 30%, transparent 70%);`"></div>

                        <div>
                            <!-- Podio Escénico 3D -->
                            <div @click="openDetail({
                                    id: {{ $prod->id }},
                                    name: '{{ addslashes($prod->name) }}',
                                    code: {{ $prod->code }},
                                    price: {{ $prod->rental_price }},
                                    stock: {{ $prod->stock }},
                                    category: '{{ addslashes($prod->category->name ?? 'Catálogo') }}',
                                    description: '{{ addslashes($prod->description ?? 'Artículo de alta gama para ambientación de bodas y banquetes. Se entrega completamente sanitizado e inspeccionado.') }}',
                                    is_sale: {{ $prod->is_sale ? 'true' : 'false' }},
                                    image: '{{ $prod->image_url ?? '' }}'
                                 })"
                                 class="cursor-pointer podium-stage h-44 sm:h-52 flex items-center justify-center overflow-hidden relative border-b border-white/5 preserve-3d">
                                
                                <div class="spotlight-beam"></div>
                                <div class="podium-ring"></div>

                                @if($prod->image_url)
                                    <img src="{{ $prod->image_url }}" 
                                         alt="{{ $prod->name }}" 
                                         class="relative z-10 max-h-40 sm:max-h-48 w-auto object-contain transition-transform duration-300 ease-out group-hover:scale-110 drop-shadow-[0_15px_20px_rgba(0,0,0,0.9)]"
                                         :style="`transform: translateZ(35px) scale(${isHovered ? 1.06 : 1});`">
                                @else
                                    <div class="relative z-10 text-stone-600 flex flex-col items-center select-none group-hover:scale-105 transition-transform duration-500 ease-out">
                                        <svg class="w-12 h-12 stroke-stone-600 group-hover:stroke-gold-400/50 transition-colors" fill="none" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-[10px] font-mono mt-1 text-stone-500">Ficha técnica 3D</span>
                                    </div>
                                @endif

                                <span class="absolute top-3 left-3 bg-black/80 backdrop-blur-md border border-white/10 text-gold-300 text-[10px] font-mono font-medium px-2.5 py-0.5 rounded-md shadow-lg z-20">
                                    Cód: {{ $prod->code }}
                                </span>

                                @if($prod->is_sale)
                                    <span class="absolute top-3 right-3 bg-gradient-to-r from-amber-500 to-yellow-500 text-stone-950 text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-md shadow-lg z-20">
                                        Venta Directa
                                    </span>
                                @endif
                            </div>

                            <!-- Detalles del Producto -->
                            <div class="p-4 sm:p-5 relative z-20">
                                <span class="text-[10px] font-mono tracking-widest uppercase text-gold-400 bg-gold-500/10 border border-gold-500/20 px-2.5 py-0.5 rounded-full inline-block">
                                    {{ $prod->category->name ?? 'Catálogo' }}
                                </span>
                                
                                <h3 @click="openDetail({
                                        id: {{ $prod->id }},
                                        name: '{{ addslashes($prod->name) }}',
                                        code: {{ $prod->code }},
                                        price: {{ $prod->rental_price }},
                                        stock: {{ $prod->stock }},
                                        category: '{{ addslashes($prod->category->name ?? 'Catálogo') }}',
                                        description: '{{ addslashes($prod->description ?? 'Artículo de alta gama para ambientación de bodas y banquetes. Se entrega completamente sanitizado e inspeccionado.') }}',
                                        is_sale: {{ $prod->is_sale ? 'true' : 'false' }},
                                        image: '{{ $prod->image_url ?? '' }}'
                                     })"
                                    class="cursor-pointer font-serif font-bold text-white text-base sm:text-lg mt-2.5 leading-snug line-clamp-2 group-hover:text-gold-300 transition-colors" title="{{ $prod->name }}">
                                    {{ $prod->name }}
                                </h3>
                                
                                <div class="mt-4 pt-3.5 border-t border-white/10 flex items-baseline justify-between">
                                    <div>
                                        <span class="text-[10px] text-stone-400 block font-normal uppercase tracking-wider">Valor arriendo:</span>
                                        <p class="text-lg sm:text-xl font-bold bg-gradient-to-r from-gold-300 via-gold-400 to-amber-500 bg-clip-text text-transparent tracking-tight mt-0.5 font-mono">
                                            ${{ number_format($prod->rental_price, 0, ',', '.') }}
                                            <span class="text-xs font-normal text-stone-400">/ un.</span>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[10px] text-stone-400 block font-normal uppercase tracking-wider">Stock:</span>
                                        <span class="text-xs font-mono font-semibold {{ $prod->stock > 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                            {{ $prod->stock > 0 ? $prod->stock . ' un.' : 'Agotado' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Microinteracción de Botón de Compra por Tarjeta -->
                        <div class="p-4 sm:p-5 pt-0 flex gap-2 relative z-20">
                            <button @click="openDetail({
                                        id: {{ $prod->id }},
                                        name: '{{ addslashes($prod->name) }}',
                                        code: {{ $prod->code }},
                                        price: {{ $prod->rental_price }},
                                        stock: {{ $prod->stock }},
                                        category: '{{ addslashes($prod->category->name ?? 'Catálogo') }}',
                                        description: '{{ addslashes($prod->description ?? 'Artículo de alta gama para ambientación de bodas y banquetes. Se entrega completamente sanitizado e inspeccionado.') }}',
                                        is_sale: {{ $prod->is_sale ? 'true' : 'false' }},
                                        image: '{{ $prod->image_url ?? '' }}'
                                     })"
                                    class="p-2.5 rounded-xl bg-white/[0.05] hover:bg-white/[0.12] text-stone-300 hover:text-white border border-white/10 transition flex items-center justify-center shrink-0" 
                                    title="Ver ficha 3D">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>

                            @if($prod->stock > 0)
                                <div x-data="{ added: false }" class="flex-1">
                                    <button @click="addItem({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $prod->rental_price }}, {{ $prod->stock }}, {{ $prod->code }}); added = true; setTimeout(() => added = false, 1200)"
                                            :class="added ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/30 border-emerald-500' : 'bg-white/[0.08] hover:bg-gold-500 hover:text-stone-950 text-white border-white/15 hover:border-gold-400'"
                                            class="w-full py-2.5 px-3 rounded-xl text-xs font-semibold transition-all duration-300 flex items-center justify-center gap-1.5 active:scale-98 shadow-sm border">
                                        <template x-if="!added">
                                            <span class="flex items-center gap-1.5">
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
                                </div>
                            @else
                                <button disabled class="flex-1 bg-white/[0.03] text-stone-500 border border-white/5 text-xs font-semibold py-2.5 rounded-xl cursor-not-allowed">
                                    Sin stock
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-24 text-center bg-white/[0.02] rounded-3xl border border-white/10 p-8 shadow-xl backdrop-blur-md">
                    <div class="w-16 h-16 bg-white/[0.05] rounded-full flex items-center justify-center mx-auto text-stone-400 mb-4 border border-white/10">
                        <svg class="w-8 h-8 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-serif text-2xl font-bold text-white">No encontramos productos coincidentes</h3>
                    <p class="text-xs text-stone-400 mt-2 max-w-sm mx-auto">Intenta buscando con términos más generales o limpia los filtros activos.</p>
                    <a href="{{ route('catalog.index') }}#catalogo" class="inline-block mt-5 bg-gradient-to-r from-gold-400 to-amber-500 text-stone-950 text-xs font-bold px-6 py-2.5 rounded-xl hover:brightness-110 transition shadow-lg shadow-gold-500/20">
                        Ver todo el catálogo
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        <div class="mt-12 flex justify-center custom-pagination">
            {{ $products->links() }}
        </div>
    </main>

    <!-- MODAL DE DETALLE DE PRODUCTO CON VISOR 3D: "CELEBRA CON ESTILO" -->
    <div x-show="detailModal.open" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         @keydown.escape.window="detailModal.open = false"
         style="display: none;">
        
        <div class="fixed inset-0 bg-black/85 backdrop-blur-md transition-opacity" 
             @click="detailModal.open = false"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div class="min-h-full flex items-center justify-center p-4 sm:p-6 text-center">
            <div x-show="detailModal.open" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 @click.stop
                 class="w-full max-w-4xl bg-[#121418] border border-white/15 rounded-3xl text-left overflow-hidden shadow-2xl relative z-10 my-8">
                
                <!-- Encabezado Editorial -->
                <div class="px-6 sm:px-8 pt-6 pb-4 border-b border-white/10 flex items-center justify-between bg-white/[0.02]">
                    <div>
                        <span class="text-[10px] font-mono uppercase tracking-[0.3em] text-gold-400 block">Ficha Técnica &bull; Visor 3D</span>
                        <h2 class="font-serif text-xl sm:text-2xl font-light tracking-widest text-white uppercase mt-0.5">
                            Celebra con <span class="italic font-normal bg-gradient-to-r from-gold-300 to-amber-500 bg-clip-text text-transparent">Estilo</span>
                        </h2>
                    </div>
                    <button @click="detailModal.open = false" 
                            class="w-9 h-9 rounded-full bg-white/[0.06] hover:bg-white/15 text-stone-400 hover:text-white flex items-center justify-center transition text-xl font-light">
                        &times;
                    </button>
                </div>

                <div class="p-6 sm:p-8">
                    <!-- Migas de Pan -->
                    <nav class="flex items-center gap-2 text-xs text-stone-400 mb-6 font-medium">
                        <a href="{{ route('catalog.index') }}" class="hover:text-gold-400 transition">Inicio</a>
                        <span>&gt;</span>
                        <span class="text-stone-300" x-text="detailModal.product?.category"></span>
                        <span>&gt;</span>
                        <span class="text-gold-400 truncate max-w-xs" x-text="detailModal.product?.name"></span>
                    </nav>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                        <!-- Columna Izquierda: Visor 3D de Estudio con Pedestal Reflectivo y Controles -->
                        <div x-data="{ spinning: false, zoom: false }" 
                             class="rounded-3xl podium-stage p-6 border border-white/15 flex flex-col items-center justify-center min-h-[350px] relative overflow-hidden group preserve-3d">
                            
                            <div class="spotlight-beam"></div>
                            <div class="podium-ring"></div>

                            <template x-if="detailModal.product?.image">
                                <div class="relative z-10 flex items-center justify-center w-full h-72 preserve-3d">
                                    <img :src="detailModal.product.image" 
                                         :alt="detailModal.product.name" 
                                         :class="{ 'animate-turntable-3d': spinning, 'scale-125': zoom, 'scale-105': !zoom }"
                                         class="max-h-64 max-w-full object-contain transition-all duration-500 drop-shadow-[0_25px_35px_rgba(0,0,0,0.95)]">
                                </div>
                            </template>

                            <template x-if="!detailModal.product?.image">
                                <div class="relative z-10 flex flex-col items-center justify-center py-12 text-gold-400">
                                    <div class="w-24 h-24 rounded-2xl bg-gradient-to-tr from-gold-500/20 to-amber-600/30 border border-gold-500/40 flex items-center justify-center shadow-xl shadow-gold-500/15"
                                         :class="{ 'animate-turntable-3d': spinning }">
                                        <svg class="w-12 h-12 stroke-current" fill="none" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <span class="text-xs font-mono text-gold-300/80 mt-3 uppercase tracking-wider font-semibold">Exhibición Tridimensional</span>
                                </div>
                            </template>

                            <span class="absolute top-4 left-4 bg-black/80 border border-white/15 text-gold-300 text-xs font-mono px-3 py-1 rounded-md shadow-lg z-20" 
                                  x-text="'Cód: ' + detailModal.product?.code"></span>

                            <!-- Barra de Herramientas de Interacción 3D -->
                            <div class="absolute bottom-4 inset-x-4 flex items-center justify-center gap-2 z-20">
                                <button @click="spinning = !spinning" 
                                        :class="spinning ? 'bg-gold-500 text-stone-950 font-bold' : 'bg-black/70 text-stone-200 hover:text-white'"
                                        class="px-3 py-1.5 rounded-full text-[11px] border border-white/15 backdrop-blur-md transition flex items-center gap-1.5 shadow-lg">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    <span x-text="spinning ? 'Detener Rotación' : 'Girar 360°'"></span>
                                </button>

                                <button @click="zoom = !zoom" 
                                        :class="zoom ? 'bg-gold-500 text-stone-950 font-bold' : 'bg-black/70 text-stone-200 hover:text-white'"
                                        class="px-3 py-1.5 rounded-full text-[11px] border border-white/15 backdrop-blur-md transition flex items-center gap-1.5 shadow-lg">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                    <span x-text="zoom ? 'Vista Normal' : 'Zoom 3D'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Columna Derecha: Detalles, Cantidad & Cotización -->
                        <div class="flex flex-col justify-between space-y-5">
                            <div>
                                <span class="text-[10px] font-mono tracking-widest uppercase text-gold-400 bg-gold-500/10 border border-gold-500/20 px-3 py-0.5 rounded-full inline-block" 
                                      x-text="detailModal.product?.category"></span>

                                <h3 class="font-serif text-2xl sm:text-3xl font-bold text-white mt-2 leading-tight" 
                                    x-text="detailModal.product?.name"></h3>

                                <!-- Precios y disponibilidad -->
                                <div class="mt-4 pb-4 border-b border-white/10 flex items-baseline justify-between">
                                    <div>
                                        <p class="text-3xl font-bold font-mono bg-gradient-to-r from-gold-300 via-gold-400 to-amber-500 bg-clip-text text-transparent" 
                                           x-text="'$' + (detailModal.product?.price || 0).toLocaleString('es-CL')"></p>
                                        <span class="text-[11px] text-stone-400 block mt-0.5">IVA incluido &bull; Arriendo por evento</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[11px] text-stone-400 block uppercase font-mono">Disponibilidad:</span>
                                        <span class="text-xs font-mono font-bold" 
                                              :class="detailModal.product?.stock > 0 ? 'text-emerald-400' : 'text-rose-400'"
                                              x-text="detailModal.product?.stock > 0 ? detailModal.product.stock + ' unidades en stock' : 'Sin stock disponible'"></span>
                                    </div>
                                </div>

                                <!-- Selector de Cantidad -->
                                <div class="mt-5 space-y-2">
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-300">
                                        Cantidad requerida *
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <div class="inline-flex items-center bg-white/[0.05] border border-white/15 rounded-xl p-1 shadow-inner">
                                            <button @click="if (detailModal.qty > 1) detailModal.qty--" 
                                                    class="w-9 h-9 rounded-lg hover:bg-white/10 active:scale-95 text-stone-200 hover:text-white font-bold flex items-center justify-center transition text-base">
                                                -
                                            </button>
                                            <input type="number" 
                                                   x-model.number="detailModal.qty" 
                                                   min="1" 
                                                   :max="detailModal.product?.stock || 1"
                                                   class="w-14 bg-transparent text-center font-mono font-bold text-sm text-white focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                            <button @click="if (detailModal.qty < (detailModal.product?.stock || 1)) detailModal.qty++" 
                                                    class="w-9 h-9 rounded-lg hover:bg-white/10 active:scale-95 text-stone-200 hover:text-white font-bold flex items-center justify-center transition text-base">
                                                +
                                            </button>
                                        </div>
                                        <span class="text-xs text-stone-400 font-mono" x-text="'(Máximo: ' + (detailModal.product?.stock || 0) + ')'"></span>
                                    </div>
                                </div>

                                <!-- Botón de Agregar a la Cotización -->
                                <div class="mt-6" x-data="{ modalAdded: false }">
                                    <button @click="addDetailToCart(); modalAdded = true; setTimeout(() => modalAdded = false, 1500)" 
                                            :disabled="!detailModal.product || detailModal.product.stock <= 0"
                                            :class="modalAdded ? 'bg-emerald-600 text-white shadow-emerald-500/30' : 'bg-gradient-to-r from-gold-400 to-amber-500 hover:from-gold-300 hover:to-amber-400 text-stone-950 shadow-gold-500/20'"
                                            class="w-full uppercase tracking-wider py-4 rounded-xl text-xs sm:text-sm font-extrabold transition-all duration-300 flex items-center justify-center gap-2 shadow-xl active:scale-98 disabled:from-stone-800 disabled:to-stone-800 disabled:text-stone-600 disabled:cursor-not-allowed">
                                        <template x-if="!modalAdded">
                                            <span class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                <span>Agregar a la Cotización</span>
                                            </span>
                                        </template>
                                        <template x-if="modalAdded">
                                            <span class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                <span>¡Agregado Exitosamente a tu Lista!</span>
                                            </span>
                                        </template>
                                    </button>
                                </div>

                                <!-- Descripción y Especificaciones -->
                                <div class="mt-6 pt-5 border-t border-white/10">
                                    <h4 class="text-xs font-semibold uppercase tracking-wider text-stone-300 mb-2">Descripción del Artículo:</h4>
                                    <p class="text-xs sm:text-sm text-stone-300 font-light leading-relaxed" 
                                       x-text="detailModal.product?.description"></p>
                                </div>

                                <!-- Garantías de Servicio -->
                                <div class="mt-5 grid grid-cols-2 gap-2 text-[11px] text-stone-400">
                                    <div class="flex items-center gap-1.5 bg-white/[0.03] p-2 rounded-lg border border-white/5">
                                        <span class="text-emerald-400 font-bold">✓</span>
                                        <span>Sanitizado y embalado</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 bg-white/[0.03] p-2 rounded-lg border border-white/5">
                                        <span class="text-emerald-400 font-bold">✓</span>
                                        <span>Garantía de stock</span>
                                    </div>
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
         class="fixed bottom-20 sm:bottom-6 inset-x-0 mx-auto max-w-sm z-50 pointer-events-none px-4"
         style="display: none;">
        <div class="bg-stone-900/95 backdrop-blur-2xl text-white px-4 py-3 rounded-2xl shadow-2xl flex items-center justify-between gap-3 border border-gold-500/30 pointer-events-auto">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/30">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </span>
                <p class="text-xs font-medium truncate text-stone-200" x-text="toast.message"></p>
            </div>
            <button @click="cartOpen = true; toast.show = false" class="text-xs text-gold-400 hover:text-gold-300 font-semibold underline shrink-0 transition">
                Ver lista
            </button>
        </div>
    </div>

    <!-- Drawer Lateral del Carrito con Curvas Suaves -->
    <div x-show="cartOpen" 
         class="fixed inset-0 z-50 overflow-hidden" 
         style="display: none;">
        <div class="absolute inset-0 bg-black/75 backdrop-blur-md transition-opacity" 
             @click="cartOpen = false"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div x-show="cartOpen" 
             x-transition:enter="transform transition ease-in-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed inset-y-0 right-0 max-w-md w-full bg-[#101216]/98 backdrop-blur-2xl border-l border-white/10 text-white shadow-2xl flex flex-col justify-between z-50">
            
            <!-- Header del Drawer -->
            <div class="p-5 border-b border-white/10 flex justify-between items-center bg-white/[0.02]">
                <div>
                    <h2 class="font-serif font-bold text-2xl text-white tracking-wide">Tu Cotización</h2>
                    <p class="text-xs text-stone-400 mt-0.5">Revisa tus artículos antes de consultar disponibilidad</p>
                </div>
                <button @click="cartOpen = false" class="text-stone-400 hover:text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-white/10 transition text-2xl font-light">
                    &times;
                </button>
            </div>

            <template x-if="errorMessage">
                <div class="mx-5 mt-4 p-3.5 bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs rounded-xl flex items-start gap-2 shadow-lg">
                    <svg class="w-4 h-4 shrink-0 mt-0.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span x-text="errorMessage"></span>
                </div>
            </template>

            <!-- Listado de Artículos -->
            <div class="p-5 overflow-y-auto flex-1 space-y-3.5 divide-y divide-white/5">
                <template x-if="cart.length === 0">
                    <div class="text-center py-20 text-stone-500">
                        <svg class="w-12 h-12 mx-auto stroke-stone-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <p class="font-serif text-xl font-medium text-stone-300">Tu lista está vacía</p>
                        <p class="text-xs text-stone-500 mt-1">Explora el catálogo y agrega los productos que deseas cotizar.</p>
                    </div>
                </template>

                <template x-for="item in cart" :key="item.id">
                    <div class="pt-3.5 first:pt-0 flex items-start justify-between text-sm gap-3">
                        <div class="flex-1">
                            <p class="font-semibold text-white text-xs sm:text-sm leading-snug" x-text="item.name"></p>
                            <p class="text-[11px] text-gold-400/80 font-mono mt-0.5" x-text="'Cód: ' + item.code + ' • $' + item.price.toLocaleString('es-CL') + ' c/u'"></p>
                            
                            <div class="flex items-center gap-2 mt-2.5">
                                <button @click="updateQty(item.id, -1)" 
                                        class="w-7 h-7 rounded-full bg-white/[0.07] hover:bg-white/[0.15] active:scale-95 text-white font-bold flex items-center justify-center transition border border-white/10 shadow-sm">
                                    -
                                </button>
                                <span class="text-xs font-mono font-bold px-1 min-w-[1.5rem] text-center text-white" x-text="item.qty"></span>
                                <button @click="updateQty(item.id, 1)" 
                                        class="w-7 h-7 rounded-full bg-white/[0.07] hover:bg-white/[0.15] active:scale-95 text-white font-bold flex items-center justify-center transition border border-white/10 shadow-sm">
                                    +
                                </button>
                                <span class="text-[10px] text-stone-500 font-mono" x-text="'(Máx: ' + item.stock + ')'"></span>
                            </div>
                        </div>

                        <div class="text-right flex flex-col justify-between items-end">
                            <p class="font-bold text-gold-300 text-sm font-mono" x-text="'$' + (item.price * item.qty).toLocaleString('es-CL')"></p>
                            <button @click="removeItem(item.id)" class="text-[11px] text-rose-400 hover:text-rose-300 hover:underline mt-2.5 transition">
                                Quitar
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Resumen y Formulario de Cotización -->
            <div class="p-5 border-t border-white/10 bg-white/[0.02] space-y-4">
                <div class="bg-white/[0.03] border border-white/10 p-4 rounded-xl space-y-2">
                    <div class="flex justify-between items-center text-xs text-stone-400">
                        <span>Total de artículos:</span>
                        <span class="font-semibold text-stone-200 font-mono" x-text="countItems() + ' unidades'"></span>
                    </div>
                    <div class="flex justify-between items-center text-sm font-bold text-white pt-2.5 border-t border-white/10">
                        <span>Total Estimado:</span>
                        <span class="text-2xl font-bold bg-gradient-to-r from-gold-300 via-gold-400 to-amber-500 bg-clip-text text-transparent font-mono" x-text="'$' + getTotal().toLocaleString('es-CL')"></span>
                    </div>
                </div>

                <div class="space-y-2.5 pt-1 text-xs">
                    <label class="block font-semibold text-stone-300 uppercase tracking-wider text-[10px]">Datos para la reserva:</label>
                    <div>
                        <input type="text" x-model="form.name" placeholder="Tu Nombre y Apellido *" 
                               class="w-full p-3 bg-white/[0.04] border border-white/10 rounded-xl text-white placeholder-stone-500 focus:outline-none focus:ring-2 focus:ring-gold-500/20 focus:border-gold-500/60 transition shadow-inner">
                    </div>
                    <div>
                        <input type="date" x-model="form.date" 
                               class="w-full p-3 bg-white/[0.04] border border-white/10 rounded-xl text-white placeholder-stone-500 focus:outline-none focus:ring-2 focus:ring-gold-500/20 focus:border-gold-500/60 transition shadow-inner [color-scheme:dark]">
                    </div>
                    <div>
                        <input type="text" x-model="form.location" placeholder="Comuna / Lugar del Evento *" 
                               class="w-full p-3 bg-white/[0.04] border border-white/10 rounded-xl text-white placeholder-stone-500 focus:outline-none focus:ring-2 focus:ring-gold-500/20 focus:border-gold-500/60 transition shadow-inner">
                    </div>
                </div>

                <button @click="submitQuote()" 
                        :disabled="isSubmitting || cart.length === 0"
                        class="w-full bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 hover:from-emerald-400 hover:to-teal-500 disabled:from-stone-800 disabled:to-stone-800 disabled:text-stone-600 disabled:cursor-not-allowed text-stone-950 font-bold py-4 rounded-xl text-sm flex items-center justify-center gap-2.5 transition-all duration-300 shadow-xl shadow-emerald-500/25 active:scale-98">
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

    <!-- Barra de Navegación Móvil Inferior -->
    <div class="sm:hidden fixed bottom-0 inset-x-0 z-40 bg-[#0B0C0E]/95 backdrop-blur-xl border-t border-white/10 px-4 py-2 flex items-center justify-around shadow-2xl">
        <a href="{{ route('catalog.index') }}" class="flex flex-col items-center gap-1 text-[10px] text-stone-400 hover:text-gold-400 transition py-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Inicio</span>
        </a>
        <a href="#destacados" class="flex flex-col items-center gap-1 text-[10px] text-stone-400 hover:text-gold-400 transition py-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            <span>Destacados</span>
        </a>
        <a href="#mobiliario-rotacion" class="flex flex-col items-center gap-1 text-[10px] text-stone-400 hover:text-gold-400 transition py-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span>Mobiliario</span>
        </a>
        <a href="#catalogo" class="flex flex-col items-center gap-1 text-[10px] text-stone-400 hover:text-gold-400 transition py-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <span>Catálogo</span>
        </a>
        <button @click="cartOpen = true" class="flex flex-col items-center gap-1 text-[10px] text-gold-400 relative py-1">
            <div class="relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span class="absolute -top-1.5 -right-2 bg-gold-400 text-stone-950 text-[9px] font-bold px-1.5 py-0.2 rounded-full" x-text="countItems()"></span>
            </div>
            <span>Cotizar</span>
        </button>
    </div>

    <!-- Botón Flotante de WhatsApp con Burbuja de Ayuda -->
    <div class="fixed bottom-24 sm:bottom-6 right-4 sm:right-6 z-30 flex items-center gap-3">
        <div class="hidden md:flex bg-stone-900/95 border border-white/15 text-stone-200 text-xs px-3.5 py-2 rounded-2xl shadow-xl backdrop-blur-md items-center gap-2 animate-bounce">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span>¿Cómo podemos ayudarte?</span>
        </div>
        <a href="https://wa.me/{{ config('services.whatsapp.quote_phone', '56912345678') }}?text={{ rawurlencode('Hola, quisiera consultar por disponibilidad de arriendos para un evento.') }}" 
           target="_blank" 
           title="Conversar por WhatsApp"
           class="w-13 h-13 sm:w-14 sm:h-14 bg-gradient-to-br from-emerald-400 to-emerald-600 hover:from-emerald-300 hover:to-emerald-500 text-stone-950 rounded-full flex items-center justify-center shadow-2xl shadow-emerald-500/40 hover:scale-105 active:scale-95 transition-all duration-300 border-2 border-emerald-300/30">
            <svg class="w-7 h-7 sm:w-8 sm:h-8 fill-current text-white" viewBox="0 0 24 24">
                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.529 1.831.812 2.791.812 3.179 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.768-5.768-5.768zm9.969 5.768c0 5.518-4.482 10-10 10-1.748 0-3.385-.45-4.819-1.242l-5.181 1.356 1.381-5.048c-.902-1.492-1.381-3.21-1.381-5.066 0-5.518 4.482-10 10-10s10 4.482 10 10z"/>
            </svg>
        </a>
    </div>

    <!-- Footer Luxury -->
    <footer class="bg-black/60 border-t border-white/10 mt-20 py-12 text-center text-xs text-stone-500">
        <div class="max-w-7xl mx-auto px-4">
            <p class="font-serif text-xl font-bold text-stone-300 uppercase tracking-widest mb-2">Arriendos & Eventos</p>
            <p class="text-stone-400 font-light max-w-md mx-auto mb-4">Mobiliario de etiqueta, vajilla fina y ambientación para bodas, galas y recepciones memorables.</p>
            <div class="flex items-center justify-center gap-6 text-stone-400 text-xs mb-6">
                <a href="#destacados" class="hover:text-gold-300 transition">Destacados</a>
                <a href="#mobiliario-rotacion" class="hover:text-gold-300 transition">Mobiliario 3D</a>
                <a href="#catalogo" class="hover:text-gold-300 transition">Catálogo</a>
                <a href="{{ route('catalog.index') }}" class="hover:text-gold-300 transition">Términos & Despacho</a>
            </div>
            <p class="text-stone-600 font-mono text-[11px]">&copy; {{ date('Y') }} Todos los derechos reservados. Diseñado para ocasiones extraordinarias.</p>
        </div>
    </footer>

    <!-- Lógica Reactiva con Alpine.js -->
    <script>
        // Controlador de Inclinación 3D y Brillo Especular para Tarjetas
        function card3d() {
            return {
                rotateX: 0,
                rotateY: 0,
                glareX: 50,
                glareY: 50,
                glareOpacity: 0,
                isHovered: false,

                handleMouseMove(e) {
                    const rect = this.$el.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;

                    // Cálculo suave de inclinación 3D
                    this.rotateX = -((y - centerY) / centerY) * 10;
                    this.rotateY = ((x - centerX) / centerX) * 10;

                    this.glareX = (x / rect.width) * 100;
                    this.glareY = (y / rect.height) * 100;
                    this.glareOpacity = 0.35;
                    this.isHovered = true;
                },

                handleMouseLeave() {
                    this.rotateX = 0;
                    this.rotateY = 0;
                    this.glareOpacity = 0;
                    this.isHovered = false;
                }
            }
        }

        function quoteCart() {
            return {
                cartOpen: false,
                mobileMenuOpen: false,
                isSubmitting: false,
                errorMessage: '',
                toast: { show: false, message: '', timeout: null },
                
                // Modal de detalle interactivo 3D ("Celebra con estilo")
                detailModal: {
                    open: false,
                    product: null,
                    qty: 1
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

                openDetail(product) {
                    this.detailModal.product = product;
                    this.detailModal.qty = 1;
                    this.detailModal.open = true;
                },

                addDetailToCart() {
                    if (!this.detailModal.product) return;
                    const p = this.detailModal.product;
                    const qtyToAdd = Math.max(1, parseInt(this.detailModal.qty) || 1);

                    let item = this.cart.find(i => i.id === p.id);
                    if (item) {
                        const newQty = item.qty + qtyToAdd;
                        if (newQty <= p.stock) {
                            item.qty = newQty;
                            this.showToast(`Agregaste ${qtyToAdd} un. más de "${p.name}"`);
                        } else {
                            item.qty = p.stock;
                            this.showToast(`Ajustado al stock máximo disponible de "${p.name}" (${p.stock} un.)`);
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