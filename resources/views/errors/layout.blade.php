<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('code') — @yield('title') | {{ config('app.name', 'Arriendos & Eventos') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-zinc-950 text-zinc-100 font-sans min-h-screen flex items-center justify-center p-6 antialiased relative overflow-hidden">
    <div class="pointer-events-none absolute -top-32 left-1/4 w-96 h-96 rounded-full bg-amber-500/10 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-32 right-1/4 w-96 h-96 rounded-full bg-emerald-500/10 blur-3xl"></div>

    <div class="relative z-10 max-w-lg w-full bg-zinc-900/80 backdrop-blur-xl border border-zinc-800 rounded-3xl p-8 sm:p-10 text-center shadow-2xl">
        <div class="mb-5 flex justify-center">
            <x-brand-logo variant="mark" />
        </div>

        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-mono uppercase tracking-widest mb-4">
            <span>Error @yield('code')</span>
        </div>

        <h1 class="font-serif text-5xl sm:text-6xl font-bold text-white tracking-tight">
            @yield('code')
        </h1>

        <h2 class="font-serif text-2xl sm:text-3xl text-amber-400 mt-2">
            @yield('title')
        </h2>

        <p class="text-sm text-zinc-400 mt-4 leading-relaxed">
            @yield('message')
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-zinc-950 font-semibold text-xs sm:text-sm px-6 py-3 rounded-full shadow-lg transition">
                <span>&larr; Volver al Catálogo</span>
            </a>
        </div>
    </div>
</body>
</html>
