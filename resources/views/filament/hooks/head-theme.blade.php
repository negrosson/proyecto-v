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
</style>
