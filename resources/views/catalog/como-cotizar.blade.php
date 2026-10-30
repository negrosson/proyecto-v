<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Guía Completa: Cómo Cotizar Mobiliario para tu Evento | {{ $settings->site_name ?? 'Fiesta Rent' }}</title>
    <meta name="description" content="Aprende cómo cotizar mobiliario para tu evento en Fiesta Rent. Proceso simple en 5 pasos: cotiza, abona, recibe, devuelve y garantía. Nosotros lavamos por ti.">

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
<body x-data="quoteCart()" 
      class="bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen flex flex-col selection:bg-amber-500 selection:text-white transition-colors duration-200">

    <!-- HEADER / NAVEGACIÓN PRINCIPAL -->
    <header class="sticky top-0 z-40 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md border-b border-zinc-200 dark:border-zinc-800 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <a href="{{ route('catalog.index') }}" class="flex items-center gap-3 group focus:outline-none">
                @if(!empty($settings?->logo_path))
                    <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="{{ $settings->site_name ?? 'Logo' }}" class="w-10 h-10 rounded-xl object-cover shadow-md border border-zinc-200 dark:border-zinc-700">
                @else
                    <div class="w-10 h-10 rounded-xl bg-zinc-900 dark:bg-zinc-800 text-zinc-100 flex items-center justify-center font-serif text-2xl font-bold shadow-md shadow-zinc-900/10 group-hover:scale-105 transition-transform duration-300 border border-zinc-800 dark:border-zinc-700">
                        {{ mb_substr($settings->site_name ?? 'F', 0, 1) }}
                    </div>
                @endif
                <div class="min-w-0 flex-1 sm:flex-initial">
                    <span class="text-sm sm:text-base md:text-lg font-serif font-bold tracking-wider text-zinc-900 dark:text-zinc-100 uppercase block leading-tight truncate max-w-[135px] sm:max-w-none">
                        {{ $settings->site_name ?? 'Fiesta Rent' }}
                    </span>
                    <span class="hidden sm:block text-[9px] sm:text-[10px] tracking-[0.22em] text-zinc-500 dark:text-zinc-400 uppercase font-mono truncate">
                        {{ $settings->site_tagline ?? 'Tu Evento, Nuestro Mobiliario' }}
                    </span>
                </div>
            </a>

            <!-- Navegación Desktop -->
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
                <a href="{{ route('catalog.montajes') }}" class="hover:text-zinc-900 dark:hover:text-white transition-colors py-1">
                    Montajes
                </a>
                <a href="{{ route('catalog.como-cotizar') }}" class="text-zinc-950 dark:text-white font-bold border-b-2 border-zinc-900 dark:border-amber-400 py-1">
                    Cómo Cotizar
                </a>
                <a href="{{ route('catalog.index') }}#envios" class="hover:text-zinc-900 dark:hover:text-white transition-colors py-1">
                    Envíos
                </a>
                <a href="{{ route('catalog.index') }}#contacto" class="hover:text-zinc-900 dark:hover:text-white transition-colors py-1">
                    Contacto
                </a>
                <a href="https://wa.me/{{ $settings->cleanWhatsappPhone() }}?text={{ rawurlencode('Hola, quisiera consultar cómo cotizar mobiliario.') }}" 
                   target="_blank" 
                   class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors py-1 flex items-center gap-1.5 font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    WhatsApp
                </a>
            </nav>

            <!-- Acciones Header -->
            <div class="flex items-center gap-2 sm:gap-2.5">
                <!-- Botón Cotización -->
                <button @click="cartOpen = true" 
                        class="relative inline-flex items-center gap-1.5 sm:gap-2 bg-zinc-900 hover:bg-zinc-800 dark:bg-amber-500 dark:hover:bg-amber-600 text-white dark:text-zinc-950 px-2.5 sm:px-4 py-2 sm:py-2.5 rounded-full shadow-md transition-all active:scale-95 group">
                    <svg class="w-4 h-4 text-amber-300 dark:text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span class="hidden sm:inline text-xs font-semibold tracking-wide">Cotización</span>
                    <span class="font-mono font-bold text-xs px-1.5 sm:px-2 py-0.5 rounded-full bg-amber-400 dark:bg-zinc-900 text-zinc-950 dark:text-amber-400" 
                          x-text="countItems()"></span>
                </button>

                <!-- Botón Modo Oscuro / Claro -->
                <button @click="toggleTheme()" 
                        type="button" 
                        title="Alternar Modo Claro / Oscuro"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-amber-400 transition flex items-center justify-center border border-zinc-200 dark:border-zinc-700 shadow-sm">
                    <svg x-show="isDark" class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg x-show="!isDark" class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                <!-- Toggle Móvil -->
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

        <!-- Menú Móvil Desplegable -->
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
                <a @click="mobileMenuOpen = false" href="{{ route('catalog.index') }}#destacados" class="px-3 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex items-center justify-between">
                    <span>Colección en Carrusel</span>
                    <span class="text-xs text-zinc-400 font-mono">02</span>
                </a>
                <a @click="mobileMenuOpen = false" href="{{ route('catalog.index') }}#catalogo" class="px-3 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex items-center justify-between">
                    <span>Catálogo Completo</span>
                    <span class="text-xs text-zinc-400 font-mono">03</span>
                </a>
                <a @click="mobileMenuOpen = false" href="{{ route('catalog.montajes') }}" class="px-3 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex items-center justify-between">
                    <span>Montajes Realizados</span>
                    <span class="text-xs text-zinc-400 font-mono">04</span>
                </a>
                <a @click="mobileMenuOpen = false" href="{{ route('catalog.como-cotizar') }}" class="px-3 py-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 transition flex items-center justify-between font-bold text-amber-700 dark:text-amber-400">
                    <span>Cómo Cotizar Mobiliario</span>
                    <span class="text-xs text-zinc-400 font-mono">05</span>
                </a>
                <a @click="mobileMenuOpen = false" href="{{ route('catalog.index') }}#envios" class="px-3 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex items-center justify-between">
                    <span>Envíos & Logística</span>
                    <span class="text-xs text-zinc-400 font-mono">06</span>
                </a>
                <a @click="mobileMenuOpen = false" href="{{ route('catalog.index') }}#contacto" class="px-3 py-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition flex items-center justify-between">
                    <span>Contacto & Ubicación</span>
                    <span class="text-xs text-zinc-400 font-mono">07</span>
                </a>
                <a href="https://wa.me/{{ $settings->cleanWhatsappPhone() }}" 
                   target="_blank" 
                   class="px-3 py-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 font-semibold transition flex items-center justify-between">
                    <span>Atención por WhatsApp</span>
                    <svg class="w-4 h-4 fill-current text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.529 1.831.812 2.791.812 3.179 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.768-5.768-5.768zm9.969 5.768c0 5.518-4.482 10-10 10-1.748 0-3.385-.45-4.819-1.242l-5.181 1.356 1.381-5.048c-.902-1.492-1.381-3.21-1.381-5.066 0-5.518 4.482-10 10-10s10 4.482 10 10z"/></svg>
                </a>
            </nav>
        </div>
    </header>

    <!-- CONTENIDO PRINCIPAL: CÓMO COTIZAR -->
    <main class="flex-1">
        <!-- Hero Editorial: Celebra con Estilo -->
        <section class="relative bg-zinc-50/60 dark:bg-zinc-950 border-b border-zinc-200/80 dark:border-zinc-800 py-16 sm:py-24 overflow-hidden">
            <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none" style="background-image: radial-gradient(#18181B 1px, transparent 1px); background-size: 24px 24px;"></div>

            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <div class="inline-flex items-center gap-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 px-4 py-1.5 rounded-full mb-6 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse"></span>
                    <span class="text-[10px] sm:text-xs font-mono font-medium tracking-widest uppercase text-zinc-700 dark:text-zinc-300">
                        CELEBRA CON ESTILO &bull; TU EVENTO, NUESTRO MOBILIARIO
                    </span>
                </div>

                <h1 class="font-serif text-3xl sm:text-5xl md:text-6xl font-light tracking-tight text-zinc-900 dark:text-white leading-[1.15]">
                    Guía completa: <span class="italic font-normal text-amber-700 dark:text-amber-500">cómo cotizar mobiliario</span> para tu evento
                </h1>

                <p class="text-sm sm:text-base md:text-lg text-zinc-600 dark:text-zinc-300 mt-6 max-w-3xl mx-auto font-light leading-relaxed">
                    ¿Buscas la manera de cómo cotizar mobiliario para tu próximo evento? En <span class="font-semibold text-zinc-900 dark:text-white">{{ $settings->site_name ?? 'Fiesta Rent' }}</span> te ayudamos a hacerlo fácil y rápido. Nuestro servicio incluye una amplia variedad de mobiliario para eventos, perfecto para cualquier ocasión. Ya sea que necesites sillas, mesas, o decoración, nuestro equipo está listo para asistirte. Consulta cómo cotizar mobiliario con nosotros y haz de tu celebración una experiencia inolvidable.
                </p>

                <!-- Acciones Rápidas -->
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('catalog.index') }}#catalogo" class="inline-flex items-center gap-2 bg-zinc-900 hover:bg-zinc-800 dark:bg-amber-500 dark:hover:bg-amber-600 text-white dark:text-zinc-950 font-semibold text-xs sm:text-sm px-6 py-3.5 rounded-full shadow-lg hover:shadow-xl transition active:scale-95">
                        <svg class="w-4 h-4 text-amber-300 dark:text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span>Explorar Catálogo & Cotizar</span>
                    </a>
                    <a href="https://wa.me/{{ $settings->cleanWhatsappPhone() }}?text={{ rawurlencode('Hola, quisiera asesoría sobre cómo cotizar mobiliario para mi evento.') }}" 
                       target="_blank" 
                       class="inline-flex items-center gap-2 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 font-medium text-xs sm:text-sm px-6 py-3.5 rounded-full shadow-sm transition">
                        <svg class="w-4 h-4 fill-current text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.529 1.831.812 2.791.812 3.179 0 5.767-2.587 5.768-5.766 0-3.18-2.588-5.768-5.768-5.768zm9.969 5.768c0 5.518-4.482 10-10 10-1.748 0-3.385-.45-4.819-1.242l-5.181 1.356 1.381-5.048c-.902-1.492-1.381-3.21-1.381-5.066 0-5.518 4.482-10 10-10s10 4.482 10 10z"/></svg>
                        <span>Consultar por WhatsApp</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- SECCIÓN DE LOS 5 PASOS: CAJONES BONITOS & ORDENADOS -->
        <section class="py-16 sm:py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-[10px] font-mono uppercase tracking-[0.25em] text-amber-800 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 px-3.5 py-1 rounded-full inline-block mb-3 font-semibold">
                    Paso a Paso Transparente
                </span>
                <h2 class="font-serif text-2xl sm:text-4xl font-light text-zinc-900 dark:text-white">
                    El Proceso de <span class="italic font-normal text-amber-700 dark:text-amber-500">Reserva y Arriendo</span>
                </h2>
                <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-2 font-light">
                    Sigue estos 5 sencillos pasos para asegurar el mobiliario perfecto para tu fecha.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7">
                <!-- Cajón 1: COTIZA -->
                <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-8 border border-zinc-200/90 dark:border-zinc-800 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between mb-5">
                            <span class="text-xs font-mono font-bold tracking-widest text-amber-700 dark:text-amber-400 bg-amber-100/70 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/80 px-3 py-1 rounded-full uppercase">
                                Paso 01
                            </span>
                            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
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

                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 text-xs font-semibold text-amber-700 dark:text-amber-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>¡Te daremos respuesta lo más pronto posible!</span>
                    </div>
                </div>

                <!-- Cajón 2: ABONA -->
                <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-8 border border-zinc-200/90 dark:border-zinc-800 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between mb-5">
                            <span class="text-xs font-mono font-bold tracking-widest text-emerald-700 dark:text-emerald-400 bg-emerald-100/70 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 px-3 py-1 rounded-full uppercase">
                                Paso 02
                            </span>
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 group-hover:scale-110 transition-transform duration-300">
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

                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 text-xs font-semibold text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Reserva formal asegurada</span>
                    </div>
                </div>

                <!-- Cajón 3: RECIBE -->
                <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-8 border border-zinc-200/90 dark:border-zinc-800 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between mb-5">
                            <span class="text-xs font-mono font-bold tracking-widest text-sky-700 dark:text-sky-400 bg-sky-100/70 dark:bg-sky-950/60 border border-sky-200 dark:border-sky-800/80 px-3 py-1 rounded-full uppercase">
                                Paso 03
                            </span>
                            <div class="w-12 h-12 rounded-2xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-500/20 group-hover:scale-110 transition-transform duration-300">
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

                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 text-xs font-semibold text-sky-700 dark:text-sky-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                        <span>Entrega ordenada y coordinada</span>
                    </div>
                </div>

                <!-- Cajón 4: DEVUELVE -->
                <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-8 border border-zinc-200/90 dark:border-zinc-800 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between mb-5">
                            <span class="text-xs font-mono font-bold tracking-widest text-violet-700 dark:text-violet-400 bg-violet-100/70 dark:bg-violet-950/60 border border-violet-200 dark:border-violet-800/80 px-3 py-1 rounded-full uppercase">
                                Paso 04
                            </span>
                            <div class="w-12 h-12 rounded-2xl bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center border border-violet-500/20 group-hover:scale-110 transition-transform duration-300">
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

                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 text-xs font-semibold text-violet-700 dark:text-violet-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-violet-500"></span>
                        <span>Retiro puntual en el lugar</span>
                    </div>
                </div>

                <!-- Cajón 5: GARANTÍA -->
                <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-8 border border-zinc-200/90 dark:border-zinc-800 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between mb-5">
                            <span class="text-xs font-mono font-bold tracking-widest text-teal-700 dark:text-teal-400 bg-teal-100/70 dark:bg-teal-950/60 border border-teal-200 dark:border-teal-800/80 px-3 py-1 rounded-full uppercase">
                                Paso 05
                            </span>
                            <div class="w-12 h-12 rounded-2xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center border border-teal-500/20 group-hover:scale-110 transition-transform duration-300">
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

                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 text-xs font-semibold text-teal-700 dark:text-teal-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                        <span>Devolución rápida por transferencia</span>
                    </div>
                </div>

                <!-- Tarjeta 6: Soporte Directo y Cubicación -->
                <div class="bg-gradient-to-br from-amber-500/10 via-amber-600/5 to-transparent dark:from-amber-950/30 dark:via-zinc-900 dark:to-zinc-900 rounded-3xl p-6 sm:p-8 border border-amber-300/80 dark:border-amber-700/60 shadow-sm flex flex-col justify-between">
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
            <div class="mt-14 sm:mt-20 bg-gradient-to-r from-zinc-900 via-zinc-950 to-zinc-900 text-white rounded-3xl p-8 sm:p-12 border border-zinc-800 shadow-2xl relative overflow-hidden group">
                <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-amber-500/10 blur-3xl pointer-events-none group-hover:bg-amber-500/20 transition-all duration-700"></div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 bg-amber-500/10 border border-amber-500/30 text-amber-300 px-3.5 py-1 rounded-full text-xs font-mono uppercase tracking-wider mb-4">
                            <span>✨ Cero Preocupaciones</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                            <span>Higiene & Desinfección Certificada</span>
                        </div>

                        <h3 class="font-serif text-3xl sm:text-4xl md:text-5xl font-light text-white tracking-wide">
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
                        <a href="{{ route('catalog.index') }}#catalogo" 
                           class="inline-flex items-center justify-center gap-2.5 bg-amber-500 hover:bg-amber-400 text-zinc-950 font-bold px-7 py-3.5 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 text-xs sm:text-sm active:scale-98">
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
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-200/80 dark:border-zinc-800 py-12 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white mb-2">
                        {{ $settings->site_name ?? 'Fiesta Rent' }}
                    </h5>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-light leading-relaxed">
                        Arriendo de mobiliario, vajilla y mantelería fina para bodas, galas y eventos en Santiago y regiones.
                    </p>
                </div>
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white mb-2">
                        Enlaces de Interés
                    </h5>
                    <ul class="space-y-1.5 text-xs text-zinc-600 dark:text-zinc-400">
                        <li><a href="{{ route('catalog.index') }}#catalogo" class="hover:underline">Catálogo Completo</a></li>
                        <li><a href="{{ route('catalog.montajes') }}" class="hover:underline">Montajes Realizados</a></li>
                        <li><a href="{{ route('catalog.como-cotizar') }}" class="hover:underline font-semibold text-amber-700 dark:text-amber-400">Cómo Cotizar Mobiliario</a></li>
                        <li><a href="{{ route('catalog.index') }}#envios" class="hover:underline">Políticas de Envíos</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white mb-2">
                        Contacto Rápido
                    </h5>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $settings->address ?? 'Huechuraba, Región Metropolitana' }}
                    </p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 font-mono">
                        {{ $settings->whatsapp_phone ?? '+56 9 7788 9988' }}
                    </p>
                </div>
            </div>
            <div class="pt-6 border-t border-zinc-200 dark:border-zinc-800 text-center text-xs text-zinc-400 font-mono">
                &copy; {{ date('Y') }} {{ $settings->site_name ?? 'Fiesta Rent' }}. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    <!-- Lógica de Carrito de Cotización en LocalStorage -->
    <script>
        function quoteCart() {
            return {
                cartOpen: false,
                mobileMenuOpen: false,
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
                cart: JSON.parse(localStorage.getItem('cotizacion_arriendo') || '[]'),
                countItems() {
                    return this.cart.reduce((acc, i) => acc + i.qty, 0);
                }
            };
        }
    </script>
</body>
</html>
