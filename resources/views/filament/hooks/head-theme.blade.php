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

    /* 2. CONTENEDOR HERO DE ANCHO COMPLETO Y DISEÑO OBSIDIAN ELEGANTE */
    .executive-hero-widget-wrapper,
    .fi-wi-widget:has(.executive-hero-card),
    .fi-wi-widget:has(.executive-hero-widget) {
        grid-column: 1 / -1 !important;
        width: 100% !important;
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    .executive-hero-card {
        width: 100% !important;
        background-color: #ffffff;
        border: 1px solid #e4e4e7;
        color: #09090b;
        border-radius: 1.25rem;
        padding: 1.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
    }

    .dark .executive-hero-card {
        background: linear-gradient(145deg, #131316 0%, #0a0a0d 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        color: #ffffff !important;
        box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.7), inset 0 1px 0 rgba(255, 255, 255, 0.06) !important;
    }

    .executive-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.875rem;
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 500;
        letter-spacing: 0.02em;
        background: #f4f4f5;
        border: 1px solid #e4e4e7;
        color: #52525b;
        width: fit-content;
    }

    .dark .executive-badge-pill {
        background: rgba(255, 255, 255, 0.04) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        color: #d4d4d8 !important;
    }

    .executive-hero-title {
        font-size: 1.75rem;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -0.025em;
        color: #09090b;
    }

    .dark .executive-hero-title {
        color: #ffffff !important;
    }

    .executive-hero-desc {
        font-size: 0.8125rem;
        line-height: 1.55;
        color: #52525b;
        max-width: 38rem;
    }

    .dark .executive-hero-desc {
        color: #a1a1aa !important;
    }

    .executive-section-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #71717a;
    }

    .dark .executive-section-label {
        color: #a1a1aa !important;
    }

    .executive-pill-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        text-decoration: none !important;
        background: #f4f4f5;
        border: 1px solid #e4e4e7;
        color: #18181b;
        transition: all 0.2s ease;
    }

    .executive-pill-btn:hover {
        background: #e4e4e7;
        color: #000000;
    }

    .dark .executive-pill-btn {
        background: rgba(255, 255, 255, 0.04) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        color: #e4e4e7 !important;
    }

    .dark .executive-pill-btn:hover {
        background: rgba(255, 255, 255, 0.09) !important;
        border-color: rgba(255, 255, 255, 0.22) !important;
        color: #ffffff !important;
    }

    .executive-pill-btn-teal {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none !important;
        background: #f0fdfa;
        border: 1px solid #14b8a6;
        color: #0f766e;
        transition: all 0.2s ease;
    }

    .executive-pill-btn-teal:hover {
        background: #ccfbf1;
    }

    .dark .executive-pill-btn-teal {
        background: rgba(20, 184, 166, 0.1) !important;
        border: 1px solid rgba(20, 184, 166, 0.45) !important;
        color: #5eead4 !important;
        box-shadow: 0 0 12px rgba(20, 184, 166, 0.15) !important;
    }

    .dark .executive-pill-btn-teal:hover {
        background: rgba(20, 184, 166, 0.18) !important;
        border-color: rgba(20, 184, 166, 0.7) !important;
        box-shadow: 0 0 18px rgba(20, 184, 166, 0.25) !important;
    }

    .executive-stat-card {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        padding: 0.75rem 1rem;
        border-radius: 1rem;
        text-decoration: none !important;
        background: #ffffff;
        border: 1px solid #e4e4e7;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .executive-stat-card:hover {
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }

    .dark .executive-stat-card {
        background: rgba(255, 255, 255, 0.03) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        backdrop-filter: blur(12px) !important;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.4) !important;
    }

    .dark .executive-stat-card:hover {
        background: rgba(255, 255, 255, 0.05) !important;
        border-color: rgba(255, 255, 255, 0.18) !important;
        transform: translateY(-1px);
        box-shadow: 0 8px 24px -2px rgba(0, 0, 0, 0.6) !important;
    }

    .executive-stat-icon-blue {
        width: 2.75rem;
        height: 2.75rem;
        flex-shrink: 0;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e0f2fe;
        color: #0284c7;
        border: 1px solid #bae6fd;
    }

    .dark .executive-stat-icon-blue {
        background: rgba(14, 165, 233, 0.18) !important;
        border: 1px solid rgba(56, 189, 248, 0.35) !important;
        color: #38bdf8 !important;
        box-shadow: 0 0 16px rgba(56, 189, 248, 0.2) !important;
    }

    .executive-stat-icon-green {
        width: 2.75rem;
        height: 2.75rem;
        flex-shrink: 0;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #dcfce7;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }

    .dark .executive-stat-icon-green {
        background: rgba(16, 185, 129, 0.18) !important;
        border: 1px solid rgba(52, 211, 153, 0.35) !important;
        color: #34d399 !important;
        box-shadow: 0 0 16px rgba(52, 211, 153, 0.2) !important;
    }

    .executive-stat-icon-whatsapp {
        width: 2.75rem;
        height: 2.75rem;
        flex-shrink: 0;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }

    .dark .executive-stat-icon-whatsapp {
        background: rgba(34, 197, 94, 0.2) !important;
        border: 1px solid rgba(74, 222, 128, 0.4) !important;
        color: #4ade80 !important;
        box-shadow: 0 0 18px rgba(34, 197, 94, 0.25) !important;
    }

    .executive-stat-icon-amber {
        width: 2.75rem;
        height: 2.75rem;
        flex-shrink: 0;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fef3c7;
        color: #d97706;
        border: 1px solid #fde68a;
    }

    .dark .executive-stat-icon-amber {
        background: rgba(245, 158, 11, 0.18) !important;
        border: 1px solid rgba(251, 191, 36, 0.35) !important;
        color: #fbbf24 !important;
        box-shadow: 0 0 16px rgba(245, 158, 11, 0.2) !important;
    }

    .executive-stat-title {
        font-size: 0.875rem;
        font-weight: 700;
        line-height: 1.25;
        letter-spacing: -0.01em;
        color: #09090b;
    }

    .dark .executive-stat-title {
        color: #ffffff !important;
    }

    .executive-stat-subtitle {
        font-size: 0.6875rem;
        line-height: 1.2;
        color: #71717a;
    }

    .dark .executive-stat-subtitle {
        color: #a1a1aa !important;
    }

    /* Badge de fecha en el encabezado junto a Escritorio */
    .dashboard-header-date-badge,
    button.dashboard-header-date-badge {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        cursor: default !important;
        pointer-events: none !important;
        color: #71717a !important;
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
        text-transform: lowercase !important;
    }

    .dark .dashboard-header-date-badge,
    .dark button.dashboard-header-date-badge {
        color: #a1a1aa !important;
    }

    .dashboard-header-date-badge svg,
    .dashboard-header-date-badge .fi-btn-icon {
        width: 1rem !important;
        height: 1rem !important;
        color: #71717a !important;
    }

    .dark .dashboard-header-date-badge svg,
    .dark .dashboard-header-date-badge .fi-btn-icon {
        color: #a1a1aa !important;
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
