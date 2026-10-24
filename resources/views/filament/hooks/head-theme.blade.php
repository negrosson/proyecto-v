<!-- Inyección de Tema Claro Ejecutivo con Alto Contraste y Tipografía Editorial Luxury -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<script>
    try {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    } catch (e) {}
</script>

<style>
    /* 1. PREVENCIÓN DE OVERLAY EN PANTALLA DE LOGIN */
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

    /* 2. BASE TIPOGRÁFICA Y COLOR GENERAL */
    :root, html, body {
        color-scheme: light !important;
        color: #1C1814 !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    /* 3. FONDO DE LOGIN (Patrón sutil exclusivo de login) */
    .fi-simple-layout {
        background-color: #FAF8F5 !important;
        background-image: 
            radial-gradient(circle at 50% 0%, rgba(234, 183, 89, 0.15) 0%, transparent 60%),
            radial-gradient(#1C1814 1px, transparent 1px) !important;
        background-size: 100% 100%, 28px 28px !important;
        min-height: 100vh !important;
    }

    /* 4. FONDO DEL PANEL INTERIOR (LIENZO DE ALTO CONTRASTE, DESCANSO VISUAL SIN BRILLO BLANCO) */
    .fi-layout,
    .fi-layout body,
    .fi-main-ctn,
    .fi-page {
        background-color: #ECE7DE !important;
        background-image: none !important;
    }

    /* 5. SIDEBAR / MENÚ LATERAL DE ALTA GAMA */
    .fi-sidebar,
    .fi-sidebar header,
    .fi-sidebar-header {
        background-color: #FFFFFF !important;
        border-right: 1px solid #D8D1C4 !important;
        background-image: none !important;
    }

    .fi-sidebar-nav {
        background-image: none !important;
    }

    .fi-sidebar-item-active button,
    .fi-sidebar-item-active a {
        background-color: #F8EEDD !important;
        color: #925C14 !important;
        font-weight: 700 !important;
        border-radius: 0.625rem !important;
    }

    .fi-sidebar-item-active svg {
        color: #925C14 !important;
    }

    .fi-sidebar-group-label span {
        color: #8C7E70 !important;
        font-size: 0.6875rem !important;
        font-weight: 700 !important;
        letter-spacing: 0.12em !important;
        text-transform: uppercase !important;
    }

    /* 6. TOPBAR / BARRA SUPERIOR */
    .fi-topbar {
        background-color: #FFFFFF !important;
        border-bottom: 1px solid #D8D1C4 !important;
        background-image: none !important;
    }

    /* 7. TARJETA DE LOGIN */
    .fi-simple-main-ctn {
        position: relative !important;
        z-index: 20 !important;
        pointer-events: auto !important;
    }

    .fi-simple-main {
        background-color: #FFFFFF !important;
        border: 1px solid #D8D1C4 !important;
        border-radius: 1.5rem !important;
        box-shadow: 0 25px 50px -12px rgba(28, 24, 20, 0.08), 0 0 0 1px rgba(216, 209, 196, 0.8) !important;
        padding: 2.25rem !important;
        position: relative !important;
        z-index: 30 !important;
        pointer-events: auto !important;
    }

    /* 8. TARJETAS, SECCIONES Y TABLAS (DEFINICIÓN NÍTIDA Y PROFUNDIDAD REAL) */
    .fi-wi-widget,
    .fi-section,
    .fi-ta-ctn {
        background-color: #FFFFFF !important;
        border: 1px solid #D8D1C4 !important;
        border-radius: 1rem !important;
        box-shadow: 0 4px 16px -2px rgba(28, 24, 20, 0.05), 0 2px 4px -2px rgba(28, 24, 20, 0.03) !important;
    }

    /* 9. INPUTS BLANCOS Y CLAROS */
    .fi-input-wrp {
        background-color: #FFFFFF !important;
        border: 1.5px solid #CEC5B6 !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
        color: #1C1814 !important;
    }

    .fi-input-wrp:focus-within {
        border-color: #C89333 !important;
        box-shadow: 0 0 0 3px rgba(200, 147, 51, 0.2) !important;
    }

    .fi-input-wrp input {
        color: #1C1814 !important;
    }

    /* 10. ENCABEZADOS Y TITULOS */
    .fi-simple-header-heading {
        color: #1C1814 !important;
        font-family: 'Cormorant Garamond', serif !important;
        font-weight: 700 !important;
        letter-spacing: -0.02em !important;
        font-size: 2.25rem !important;
    }

    .fi-header-heading {
        color: #1C1814 !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-weight: 800 !important;
        font-size: 1.75rem !important;
        letter-spacing: -0.03em !important;
    }

    /* 11. BOTONES PRINCIPALES */
    .fi-btn-primary {
        background-color: #1C1814 !important;
        color: #FFFFFF !important;
        border-radius: 0.75rem !important;
        font-weight: 700 !important;
        letter-spacing: 0.02em !important;
        padding-top: 0.625rem !important;
        padding-bottom: 0.625rem !important;
        box-shadow: 0 4px 14px rgba(28, 24, 20, 0.15) !important;
        transition: all 0.2s ease !important;
    }

    .fi-btn-primary:hover {
        background-color: #2F2922 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 18px rgba(28, 24, 20, 0.2) !important;
    }
</style>
