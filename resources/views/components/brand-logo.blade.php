@props([
    'variant' => 'header', // 'header' | 'footer' | 'mark'
    'settings' => null,
])

@php
    $customLogoPath = $settings?->logo_path ?? null;
    $siteName = $settings?->site_name ?? 'VYS Productora de Eventos';
@endphp

@if(!empty($customLogoPath))
    <img src="{{ asset('storage/' . $customLogoPath) }}"
         alt="{{ $siteName }}"
         {{ $attributes->merge([
             'class' => match ($variant) {
                 'footer' => 'h-14 sm:h-16 w-auto object-contain transition-transform duration-300 group-hover:scale-105',
                 'mark' => 'h-12 w-auto object-contain mx-auto',
                 default => 'h-10 sm:h-12 w-auto max-w-[190px] sm:max-w-[230px] object-contain transition-transform duration-300 group-hover:scale-[1.03]',
             }
         ]) }}>
@elseif($variant === 'mark')
    {{-- Emblema compacto VYS (Login, Error Pages, Icono) --}}
    <div {{ $attributes->merge(['class' => 'inline-flex flex-col items-center justify-center select-none']) }}>
        <svg viewBox="0 0 180 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-16 w-auto text-zinc-900 dark:text-zinc-50" role="img" aria-label="{{ $siteName }}">
            <defs>
                <linearGradient id="vysGoldMark" x1="15" y1="8" x2="165" y2="112" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#B68228"/>
                    <stop offset="50%" stop-color="#E5BC61"/>
                    <stop offset="100%" stop-color="#B07B22"/>
                </linearGradient>
            </defs>
            {{-- Carpa / Pabellón Dorado --}}
            <path d="M90 10 C82 32 58 46 28 53 C54 50 76 38 90 20 C104 38 126 50 152 53 C122 46 98 32 90 10 Z" fill="url(#vysGoldMark)"/>
            <path d="M90 22 C79 38 59 52 34 59 C60 55 80 43 90 28 C100 43 120 55 146 59 C121 52 101 38 90 22 Z" fill="url(#vysGoldMark)" opacity="0.88"/>
            {{-- Postes / Cortinas Laterales --}}
            <path d="M35 58 L35 95 M41 58 L41 93 M145 58 L145 95 M139 58 L139 93" stroke="url(#vysGoldMark)" stroke-width="2.4" stroke-linecap="round"/>
            {{-- Arco Inferior Dorado --}}
            <path d="M18 106 C56 89 124 89 162 106 C124 94 56 94 18 106 Z" fill="url(#vysGoldMark)"/>
            {{-- Mesa con Mantel --}}
            <path d="M68 72 L112 72 L110 92 L98 92 L95 82 L85 82 L82 92 L70 92 Z" fill="currentColor"/>
            <rect x="65" y="69" width="50" height="4" rx="1.5" fill="currentColor"/>
            {{-- Centro Floral y Copas --}}
            <circle cx="90" cy="61" r="4.5" fill="currentColor"/>
            <circle cx="85" cy="63" r="3" fill="currentColor"/>
            <circle cx="95" cy="63" r="3" fill="currentColor"/>
            <path d="M88 65 L92 65 L91 69 L89 69 Z" fill="currentColor"/>
            <path d="M74 63 C74 65.5 76 66.5 76 69 M74.5 69 L77.5 69" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            <path d="M106 63 C106 65.5 104 66.5 104 69 M102.5 69 L105.5 69" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            {{-- Silla Izquierda --}}
            <path d="M49 62 L53 62 L56 81 L65 81 L65 92 L62 92 L62 84 L55 84 L54 92 L51 92 Z" fill="currentColor"/>
            {{-- Silla Derecha --}}
            <path d="M131 62 L127 62 L124 81 L115 81 L115 92 L118 92 L118 84 L125 84 L126 92 L129 92 Z" fill="currentColor"/>
        </svg>
        <div class="mt-1 text-center leading-none">
            <span class="block text-[9px] font-sans font-semibold tracking-[0.22em] text-zinc-600 dark:text-zinc-300 uppercase">Productora de Eventos</span>
            <span class="block font-serif text-3xl font-bold tracking-[0.12em] mt-0.5">
                <span class="text-zinc-900 dark:text-white">V</span><span class="text-amber-600 dark:text-amber-400">Y</span><span class="text-zinc-900 dark:text-white">S</span>
            </span>
        </div>
    </div>
@elseif($variant === 'footer')
    {{-- Versión Completa Oficial VYS (Footer Editorial) --}}
    <div {{ $attributes->merge(['class' => 'inline-flex flex-col items-start max-w-full select-none']) }}>
        <div class="flex items-center gap-3 sm:gap-4 max-w-full">
            <svg viewBox="0 0 180 116" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-12 sm:h-16 w-auto shrink-0 text-zinc-900 dark:text-zinc-50 transition-transform duration-300 group-hover:scale-105" role="img" aria-label="{{ $siteName }}">
                <defs>
                    <linearGradient id="vysGoldFooter" x1="15" y1="8" x2="165" y2="108" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#B68228"/>
                        <stop offset="50%" stop-color="#E5BC61"/>
                        <stop offset="100%" stop-color="#B07B22"/>
                    </linearGradient>
                </defs>
                <path d="M90 8 C82 30 58 44 26 51 C53 48 76 36 90 18 C104 36 127 48 154 51 C122 44 98 30 90 8 Z" fill="url(#vysGoldFooter)"/>
                <path d="M90 20 C79 36 59 50 32 57 C59 53 80 41 90 26 C100 41 121 53 148 57 C121 50 101 36 90 20 Z" fill="url(#vysGoldFooter)" opacity="0.88"/>
                <path d="M33 56 L33 93 M39 56 L39 91 M147 56 L147 93 M141 56 L141 91" stroke="url(#vysGoldFooter)" stroke-width="2.5" stroke-linecap="round"/>
                <path d="M16 105 C55 88 125 88 164 105 C125 93 55 93 16 105 Z" fill="url(#vysGoldFooter)"/>
                <path d="M68 71 L112 71 L110 91 L98 91 L95 81 L85 81 L82 91 L70 91 Z" fill="currentColor"/>
                <rect x="65" y="68" width="50" height="4" rx="1.5" fill="currentColor"/>
                <circle cx="90" cy="60" r="4.5" fill="currentColor"/>
                <circle cx="85" cy="62" r="3" fill="currentColor"/>
                <circle cx="95" cy="62" r="3" fill="currentColor"/>
                <path d="M88 64 L92 64 L91 68 L89 68 Z" fill="currentColor"/>
                <path d="M74 62 C74 64.5 76 65.5 76 68 M74.5 68 L77.5 68" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                <path d="M106 62 C106 64.5 104 65.5 104 68 M102.5 68 L105.5 68" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                <path d="M48 60 L52 60 L55 80 L65 80 L65 91 L62 91 L62 83 L54 83 L53 91 L50 91 Z" fill="currentColor"/>
                <path d="M132 60 L128 60 L125 80 L115 80 L115 91 L118 91 L118 83 L126 83 L127 91 L130 91 Z" fill="currentColor"/>
            </svg>
            <div class="leading-none">
                <span class="block text-[10px] sm:text-[11px] font-sans font-bold tracking-[0.18em] text-zinc-800 dark:text-zinc-200 uppercase">
                    Productora de Eventos
                </span>
                <span class="block text-[9px] sm:text-[10px] font-sans font-medium tracking-[0.22em] text-zinc-500 dark:text-zinc-400 uppercase mt-0.5">
                    Servicios Integrales
                </span>
                <span class="block font-serif text-3xl sm:text-4xl font-bold tracking-[0.06em] leading-[0.9] mt-1">
                    <span class="text-zinc-950 dark:text-white">V</span><span class="bg-gradient-to-b from-amber-500 via-amber-600 to-amber-700 dark:from-amber-300 dark:via-amber-400 dark:to-amber-500 bg-clip-text text-transparent">Y</span><span class="text-zinc-950 dark:text-white">S</span>
                </span>
            </div>
        </div>
        <div class="mt-2.5 pt-2 border-t border-amber-500/30 w-full">
            <span class="block text-[8.5px] sm:text-[10px] font-mono tracking-[0.08em] sm:tracking-[0.16em] text-zinc-600 dark:text-zinc-400 uppercase leading-relaxed">
                <span class="text-amber-600 dark:text-amber-400">&bull;</span> Catering
                <span class="text-amber-600 dark:text-amber-400">&bull;</span> Mobiliario
                <span class="text-amber-600 dark:text-amber-400">&bull;</span> Mesas
                <span class="text-amber-600 dark:text-amber-400">&bull;</span> Sillas
                <span class="text-amber-600 dark:text-amber-400">&bull;</span> Carpas
                <span class="text-amber-600 dark:text-amber-400">&bull;</span> Toldos
                <span class="text-amber-600 dark:text-amber-400">&bull;</span>
            </span>
        </div>
    </div>
@else
    {{-- Versión Header Responsiva (Nítida y proporcional desde 320px hasta Desktop 4K, compatible con header transparente sobre Hero oscuro) --}}
    <div {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 sm:gap-2.5 min-w-0 max-w-full shrink-0 select-none']) }}>
        <svg viewBox="0 0 180 116" fill="none" xmlns="http://www.w3.org/2000/svg" class="brand-logo-svg h-8 sm:h-10 lg:h-11 w-auto shrink-0 text-zinc-900 dark:text-zinc-50 transition-all duration-300 group-hover:scale-105" role="img" aria-label="{{ $siteName }}">
            <defs>
                <linearGradient id="vysGoldHeader" x1="15" y1="8" x2="165" y2="108" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#B68228"/>
                    <stop offset="50%" stop-color="#E5BC61"/>
                    <stop offset="100%" stop-color="#B07B22"/>
                </linearGradient>
            </defs>
            <path d="M90 8 C82 30 58 44 26 51 C53 48 76 36 90 18 C104 36 127 48 154 51 C122 44 98 30 90 8 Z" fill="url(#vysGoldHeader)"/>
            <path d="M90 20 C79 36 59 50 32 57 C59 53 80 41 90 26 C100 41 121 53 148 57 C121 50 101 36 90 20 Z" fill="url(#vysGoldHeader)" opacity="0.88"/>
            <path d="M33 56 L33 93 M39 56 L39 91 M147 56 L147 93 M141 56 L141 91" stroke="url(#vysGoldHeader)" stroke-width="2.6" stroke-linecap="round"/>
            <path d="M16 105 C55 88 125 88 164 105 C125 93 55 93 16 105 Z" fill="url(#vysGoldHeader)"/>
            <path d="M68 71 L112 71 L110 91 L98 91 L95 81 L85 81 L82 91 L70 91 Z" fill="currentColor"/>
            <rect x="65" y="68" width="50" height="4" rx="1.5" fill="currentColor"/>
            <circle cx="90" cy="60" r="4.5" fill="currentColor"/>
            <circle cx="85" cy="62" r="3" fill="currentColor"/>
            <circle cx="95" cy="62" r="3" fill="currentColor"/>
            <path d="M88 64 L92 64 L91 68 L89 68 Z" fill="currentColor"/>
            <path d="M74 62 C74 64.5 76 65.5 76 68 M74.5 68 L77.5 68" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            <path d="M106 62 C106 64.5 104 65.5 104 68 M102.5 68 L105.5 68" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            <path d="M48 60 L52 60 L55 80 L65 80 L65 91 L62 91 L62 83 L54 83 L53 91 L50 91 Z" fill="currentColor"/>
            <path d="M132 60 L128 60 L125 80 L115 80 L115 91 L118 91 L118 83 L126 83 L127 91 L130 91 Z" fill="currentColor"/>
        </svg>
        <div class="leading-none min-w-0">
            <span class="brand-logo-kicker hidden sm:block text-[8px] font-sans font-bold tracking-[0.17em] text-zinc-700 dark:text-zinc-300 uppercase whitespace-nowrap transition-colors duration-300">
                Productora de Eventos
            </span>
            <div class="flex items-baseline gap-1.5">
                <span class="font-serif text-xl sm:text-[26px] font-bold tracking-[0.06em] leading-none">
                    <span class="brand-logo-letter text-zinc-950 dark:text-white transition-colors duration-300">V</span><span class="bg-gradient-to-b from-amber-400 via-amber-500 to-amber-600 dark:from-amber-300 dark:via-amber-400 dark:to-amber-500 bg-clip-text text-transparent">Y</span><span class="brand-logo-letter text-zinc-950 dark:text-white transition-colors duration-300">S</span>
                </span>
                <span class="brand-logo-sub hidden 2xl:inline text-[8px] font-mono tracking-[0.14em] text-zinc-500 dark:text-zinc-400 uppercase whitespace-nowrap transition-colors duration-300">
                    Servicios Integrales
                </span>
            </div>
            <span class="brand-logo-sub block sm:hidden text-[6.5px] font-mono tracking-[0.08em] text-zinc-500 dark:text-zinc-400 uppercase truncate mt-0.5 transition-colors duration-300">
                Productora de Eventos
            </span>
        </div>
    </div>
@endif
