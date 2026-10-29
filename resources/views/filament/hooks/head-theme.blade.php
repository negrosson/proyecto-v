<!-- Inyección de Tipografía Moderna y Reglas Estructurales Filament -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Cormorant+Garamond:wght@600;700&display=swap" rel="stylesheet">

<style>
    :root {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    /* 1. PREVENCIÓN DE OVERLAY BLOQUEANTE EN PANTALLA DE LOGIN */
    .fi-simple-layout [x-cloak] {
        display: none !important;
    }

    .fi-simple-layout .fi-modal:not(.fi-modal-open),
    .fi-simple-layout .fi-modal:not(.fi-modal-open) .fi-modal-close-overlay {
        display: none !important;
        pointer-events: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
    }

    /* 2. CONTENEDOR HERO DE ANCHO COMPLETO SIN FONDOS BLANCOS PARÁSITOS */
    .executive-hero-widget-wrapper,
    .fi-wi-widget:has(.executive-hero-widget) {
        grid-column: 1 / -1 !important;
        width: 100% !important;
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    .executive-hero-widget {
        background-color: #ffffff;
        border: 1px solid #e4e4e7;
        color: #09090b;
    }

    .dark .executive-hero-widget {
        background-color: #18181b !important;
        border-color: #27272a !important;
        color: #f4f4f5 !important;
    }

    /* 3. RESPONSIVIDAD GLOBAL PANEL ADMINISTRATIVO */
    .fi-main {
        max-width: 100% !important;
        overflow-x: hidden !important;
    }

    .fi-ta-content {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
    }

    /* Asegurar que modales y slide-overs no excedan el ancho de pantalla en móviles */
    @media (max-width: 640px) {
        .fi-modal-window,
        .fi-slide-over-window {
            max-width: 100vw !important;
            margin: 0 !important;
        }

        .fi-ta-header-toolbar {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.75rem !important;
        }

        .fi-ta-actions {
            flex-wrap: wrap !important;
            gap: 0.25rem !important;
        }
    }

    /* 4. BLINDAJE VISUAL COMPLETO PARA 2FA (LIGHT & DARK MODE OBSIDIAN) */
    .two-factor-wrapper {
        max-width: 56rem;
        margin: 0 auto;
    }

    .two-factor-card {
        background-color: #ffffff;
        border: 1px solid #e4e4e7;
        color: #09090b;
        border-radius: 1.25rem;
        padding: 1.75rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    }

    .dark .two-factor-card {
        background-color: #18181b !important;
        border-color: #27272a !important;
        color: #f4f4f5 !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.6) !important;
    }

    .two-factor-card h2,
    .two-factor-card h3,
    .two-factor-card h4 {
        color: #09090b;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .dark .two-factor-card h2,
    .dark .two-factor-card h3,
    .dark .two-factor-card h4 {
        color: #ffffff !important;
    }

    .two-factor-card p {
        color: #71717a;
    }

    .dark .two-factor-card p {
        color: #a1a1aa !important;
    }

    .two-factor-inner {
        background-color: #f4f4f5;
        border: 1px solid #e4e4e7;
        border-radius: 1rem;
        padding: 1.25rem;
    }

    .dark .two-factor-inner {
        background-color: #09090b !important;
        border-color: #27272a !important;
        color: #f4f4f5 !important;
    }

    .two-factor-badge-app {
        display: inline-block;
        padding: 0.25rem 0.625rem;
        background-color: #f4f4f5;
        border: 1px solid #e4e4e7;
        color: #3f3f46;
        border-radius: 0.5rem;
        font-size: 0.6875rem;
        font-weight: 600;
        font-family: monospace;
    }

    .dark .two-factor-badge-app {
        background-color: #27272a !important;
        border-color: #3f3f46 !important;
        color: #e4e4e7 !important;
    }

    .two-factor-input-text {
        width: 100%;
        background-color: #ffffff;
        border: 1px solid #d4d4d8;
        color: #09090b;
        border-radius: 0.75rem;
        padding: 0.625rem 0.875rem;
        font-size: 0.875rem;
    }

    .dark .two-factor-input-text {
        background-color: #09090b !important;
        border-color: #27272a !important;
        color: #ffffff !important;
    }

    .two-factor-input-text:focus {
        outline: none;
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2) !important;
    }

    .two-factor-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background-color: #f59e0b;
        color: #09090b;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-radius: 0.75rem;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .two-factor-btn-primary:hover {
        background-color: #d97706;
    }

    .two-factor-btn-success {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background-color: #059669;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-radius: 0.75rem;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .two-factor-btn-success:hover {
        background-color: #047857;
    }

    .two-factor-btn-danger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        background-color: #e11d48;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.75rem;
        border-radius: 0.75rem;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .two-factor-btn-danger:hover {
        background-color: #be123c;
    }

    .two-factor-btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        background-color: #f4f4f5;
        color: #3f3f46;
        font-weight: 600;
        font-size: 0.75rem;
        border-radius: 0.75rem;
        transition: all 0.2s;
        border: 1px solid #e4e4e7;
        cursor: pointer;
    }

    .dark .two-factor-btn-secondary {
        background-color: #27272a !important;
        border-color: #3f3f46 !important;
        color: #f4f4f5 !important;
    }

    .two-factor-btn-secondary:hover {
        background-color: #e4e4e7;
    }

    .dark .two-factor-btn-secondary:hover {
        background-color: #3f3f46 !important;
    }

    .two-factor-code-item {
        background-color: #ffffff;
        border: 1px solid #fcd34d;
        color: #09090b;
        font-family: monospace;
        font-weight: 700;
        font-size: 0.8125rem;
        padding: 0.5rem;
        border-radius: 0.5rem;
        text-align: center;
        letter-spacing: 0.05em;
    }

    .dark .two-factor-code-item {
        background-color: #09090b !important;
        border-color: rgba(245, 158, 11, 0.4) !important;
        color: #f4f4f5 !important;
    }
</style>
