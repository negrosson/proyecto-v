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
        background: linear-gradient(135deg, #ffffff 0%, #fffdf7 62%, #fef9ec 100%);
        border: 1px solid #ece7db;
        color: #09090b;
        border-radius: 1.35rem;
        padding: 1.85rem;
        box-shadow: 0 10px 30px -12px rgba(180, 130, 40, 0.14), 0 1px 3px rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
    }

    .dark .executive-hero-card {
        background: linear-gradient(145deg, #15151a 0%, #0b0b0f 100%) !important;
        border: 1px solid rgba(251, 191, 36, 0.16) !important;
        color: #ffffff !important;
        box-shadow: 0 24px 40px -14px rgba(0, 0, 0, 0.78), inset 0 1px 0 rgba(255, 255, 255, 0.06) !important;
    }

    @keyframes execBeamSlide {
        0% { transform: translateX(-120%); }
        100% { transform: translateX(620%); }
    }

    .exec-top-rail {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2.5px;
        background: linear-gradient(90deg, rgba(245, 158, 11, 0.12), rgba(245, 158, 11, 0.25), rgba(16, 185, 129, 0.15));
        overflow: hidden;
        pointer-events: none;
    }

    .exec-top-beam {
        width: 22%;
        height: 100%;
        background: linear-gradient(90deg, transparent, #f59e0b, #fbbf24, #10b981, transparent);
        animation: execBeamSlide 4.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }

    .exec-spotlight {
        position: absolute;
        inset: 0;
        pointer-events: none;
        transition: opacity 0.35s ease;
        z-index: 1;
    }

    .exec-hero-grid {
        position: relative;
        z-index: 2;
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.75rem;
        align-items: center;
    }

    @media (min-width: 1180px) {
        .exec-hero-grid {
            grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr);
        }
    }

    .exec-left-col {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .exec-live-dot {
        width: 0.48rem;
        height: 0.48rem;
        border-radius: 9999px;
        background: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        display: inline-block;
    }

    .executive-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.3rem 0.9rem;
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        background: #ffffff;
        border: 1px solid #e7e1d2;
        color: #52525b;
        width: fit-content;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }

    .dark .executive-badge-pill {
        background: rgba(255, 255, 255, 0.04) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        color: #d4d4d8 !important;
    }

    .executive-hero-title {
        font-family: 'Cormorant Garamond', Georgia, serif !important;
        font-size: 2.1rem;
        font-weight: 700;
        line-height: 1.12;
        letter-spacing: -0.01em;
        color: #09090b;
        margin: 0;
    }

    .dark .executive-hero-title {
        color: #ffffff !important;
    }

    .executive-hero-desc {
        font-size: 0.84rem;
        line-height: 1.55;
        color: #52525b;
        max-width: 34rem;
        margin: 0.4rem 0 0;
    }

    .dark .executive-hero-desc {
        color: #a1a1aa !important;
    }

    .exec-actions-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.55rem;
        padding-top: 0.25rem;
    }

    .executive-pill-btn-gold {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.52rem 1.1rem;
        border-radius: 9999px;
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none !important;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #09090b !important;
        box-shadow: 0 8px 18px -6px rgba(217, 119, 6, 0.55);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .executive-pill-btn-gold svg,
    .executive-pill-btn svg {
        width: 0.9rem;
        height: 0.9rem;
        flex-shrink: 0;
    }

    .executive-pill-btn-gold:hover {
        transform: translateY(-1.5px);
        filter: brightness(1.06);
        box-shadow: 0 12px 24px -6px rgba(217, 119, 6, 0.68);
    }

    .executive-pill-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.48rem 0.95rem;
        border-radius: 9999px;
        font-size: 0.76rem;
        font-weight: 600;
        text-decoration: none !important;
        background: #ffffff;
        border: 1px solid #e4e4e7;
        color: #27272a;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .executive-pill-btn:hover {
        background: #18181b;
        border-color: #18181b;
        color: #ffffff;
        transform: translateY(-1.5px);
    }

    .dark .executive-pill-btn {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        color: #e4e4e7 !important;
    }

    .dark .executive-pill-btn:hover {
        background: rgba(251, 191, 36, 0.14) !important;
        border-color: rgba(251, 191, 36, 0.45) !important;
        color: #fef3c7 !important;
    }

    .exec-right-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }

    @media (min-width: 560px) {
        .exec-right-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    .executive-stat-card {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.9rem 1rem;
        border-radius: 1rem;
        text-decoration: none !important;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid #e7e5e4;
        box-shadow: 0 2px 8px -4px rgba(0, 0, 0, 0.06);
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .executive-stat-card:hover {
        border-color: #f59e0b;
        transform: translateY(-2px);
        box-shadow: 0 12px 24px -10px rgba(217, 119, 6, 0.25);
    }

    .dark .executive-stat-card {
        background: rgba(255, 255, 255, 0.035) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        backdrop-filter: blur(12px) !important;
        box-shadow: 0 6px 20px -6px rgba(0, 0, 0, 0.5) !important;
    }

    .dark .executive-stat-card:hover {
        background: rgba(255, 255, 255, 0.065) !important;
        border-color: rgba(251, 191, 36, 0.42) !important;
        transform: translateY(-2px);
        box-shadow: 0 14px 28px -8px rgba(0, 0, 0, 0.7) !important;
    }

    .exec-card-warn {
        border-color: #fcd34d !important;
        background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%) !important;
    }

    .dark .exec-card-warn {
        border-color: rgba(245, 158, 11, 0.38) !important;
        background: rgba(245, 158, 11, 0.07) !important;
    }

    .exec-card-secure {
        border-color: #a7f3d0 !important;
    }

    .dark .exec-card-secure {
        border-color: rgba(16, 185, 129, 0.32) !important;
    }

    .exec-stat-body {
        min-width: 0;
        flex: 1;
    }

    .exec-stat-arrow {
        font-size: 0.85rem;
        color: #a1a1aa;
        transition: transform 0.2s ease, color 0.2s ease;
    }

    .executive-stat-card:hover .exec-stat-arrow {
        transform: translateX(3px);
        color: #d97706;
    }

    .executive-stat-icon-blue,
    .executive-stat-icon-green,
    .executive-stat-icon-amber {
        width: 2.65rem;
        height: 2.65rem;
        flex-shrink: 0;
        border-radius: 0.8rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .executive-stat-icon-blue svg,
    .executive-stat-icon-green svg,
    .executive-stat-icon-amber svg {
        width: 1.25rem;
        height: 1.25rem;
    }

    .executive-stat-icon-blue {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
    }

    .dark .executive-stat-icon-blue {
        background: rgba(59, 130, 246, 0.16) !important;
        border-color: rgba(96, 165, 250, 0.32) !important;
        color: #60a5fa !important;
    }

    .executive-stat-icon-green {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }

    .dark .executive-stat-icon-green {
        background: rgba(16, 185, 129, 0.16) !important;
        border-color: rgba(52, 211, 153, 0.32) !important;
        color: #34d399 !important;
    }

    .executive-stat-icon-amber {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
    }

    .dark .executive-stat-icon-amber {
        background: rgba(245, 158, 11, 0.16) !important;
        border-color: rgba(251, 191, 36, 0.35) !important;
        color: #fbbf24 !important;
    }

    .executive-stat-title {
        font-size: 0.92rem;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -0.015em;
        color: #09090b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dark .executive-stat-title {
        color: #ffffff !important;
    }

    .executive-stat-subtitle {
        font-size: 0.72rem;
        line-height: 1.25;
        color: #71717a;
        margin-top: 0.15rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dark .executive-stat-subtitle {
        color: #a1a1aa !important;
    }

    /* 2.5 PULIDO VISUAL DE TARJETAS KPI Y TABLA DE RANKING EN ESCRITORIO */
    .fi-wi-stats-overview-stat {
        border-radius: 1.15rem !important;
        border: 1px solid #e7e5e4 !important;
        box-shadow: 0 4px 16px -8px rgba(9, 9, 11, 0.08) !important;
        transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.22s ease, box-shadow 0.22s ease !important;
    }

    .fi-wi-stats-overview-stat:hover {
        transform: translateY(-2px);
        border-color: rgba(245, 158, 11, 0.55) !important;
        box-shadow: 0 14px 28px -12px rgba(217, 119, 6, 0.2) !important;
    }

    .dark .fi-wi-stats-overview-stat {
        background: linear-gradient(160deg, #16161b 0%, #0f0f13 100%) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 12px 24px -12px rgba(0, 0, 0, 0.65) !important;
    }

    .dark .fi-wi-stats-overview-stat:hover {
        border-color: rgba(251, 191, 36, 0.4) !important;
    }

    .fi-ta-ctn {
        border-radius: 1.2rem !important;
        overflow: hidden;
    }

    .fi-ta-image img {
        background-color: #ffffff;
        border: 1px solid #e4e4e7;
        object-fit: contain !important;
        padding: 2px;
    }

    .dark .fi-ta-image img {
        border-color: rgba(255, 255, 255, 0.14);
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

    /* 4. DISEÑO PREMIUM AUTOCONTENIDO PARA 2FA (INMUNE A PURGADO TAILWIND DE FILAMENT, LIGHT & DARK) */
    .tfa-shell {
        width: 100%;
        max-width: 72rem;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .tfa-layout-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        align-items: start;
    }

    @media (min-width: 1024px) {
        .tfa-layout-grid {
            grid-template-columns: minmax(0, 1.65fr) minmax(0, 1fr);
        }
    }

    .tfa-card {
        position: relative;
        overflow: hidden;
        background-color: #ffffff;
        border: 1px solid #e4e4e7;
        color: #09090b;
        border-radius: 1.25rem;
        padding: 1.75rem;
        box-shadow: 0 4px 20px -6px rgba(9, 9, 11, 0.06);
    }

    .dark .tfa-card {
        background: linear-gradient(160deg, #16161a 0%, #0e0e11 100%) !important;
        border-color: rgba(255, 255, 255, 0.09) !important;
        color: #f4f4f5 !important;
        box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.65), inset 0 1px 0 rgba(255, 255, 255, 0.05) !important;
    }

    .tfa-card-accent-amber::before,
    .tfa-card-accent-emerald::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
    }

    .tfa-card-accent-amber::before {
        background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 50%, #d97706 100%);
    }

    .tfa-card-accent-emerald::before {
        background: linear-gradient(90deg, #10b981 0%, #34d399 50%, #059669 100%);
    }

    .tfa-header-row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1.25rem;
    }

    .tfa-header-main {
        display: flex;
        align-items: flex-start;
        gap: 1.125rem;
        flex: 1;
        min-width: 240px;
    }

    .tfa-icon-box {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .tfa-icon-box svg {
        width: 1.75rem;
        height: 1.75rem;
    }

    .tfa-icon-amber {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border: 1px solid #fcd34d;
        color: #b45309;
        box-shadow: 0 6px 16px -4px rgba(245, 158, 11, 0.25);
    }

    .dark .tfa-icon-amber {
        background: rgba(245, 158, 11, 0.14) !important;
        border-color: rgba(251, 191, 36, 0.35) !important;
        color: #fbbf24 !important;
    }

    .tfa-icon-emerald {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border: 1px solid #6ee7b7;
        color: #047857;
        box-shadow: 0 6px 16px -4px rgba(16, 185, 129, 0.25);
    }

    .dark .tfa-icon-emerald {
        background: rgba(16, 185, 129, 0.14) !important;
        border-color: rgba(52, 211, 153, 0.35) !important;
        color: #34d399 !important;
    }

    .tfa-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        white-space: nowrap;
    }

    .tfa-badge-amber {
        background: #fffbeb;
        border: 1px solid #fcd34d;
        color: #92400e;
    }

    .dark .tfa-badge-amber {
        background: rgba(245, 158, 11, 0.14) !important;
        border-color: rgba(251, 191, 36, 0.35) !important;
        color: #fcd34d !important;
    }

    .tfa-badge-emerald {
        background: #ecfdf5;
        border: 1px solid #6ee7b7;
        color: #065f46;
    }

    .dark .tfa-badge-emerald {
        background: rgba(16, 185, 129, 0.14) !important;
        border-color: rgba(52, 211, 153, 0.35) !important;
        color: #6ee7b7 !important;
    }

    .tfa-dot {
        width: 0.45rem;
        height: 0.45rem;
        border-radius: 9999px;
        display: inline-block;
    }

    .tfa-dot-amber { background: #f59e0b; }
    .tfa-dot-emerald { background: #10b981; }

    .tfa-title {
        font-size: 1.25rem;
        font-weight: 800;
        line-height: 1.3;
        letter-spacing: -0.02em;
        color: #09090b;
        margin: 0;
    }

    .dark .tfa-title {
        color: #ffffff !important;
    }

    .tfa-subtitle {
        font-size: 0.875rem;
        line-height: 1.55;
        color: #52525b;
        margin: 0.375rem 0 0;
    }

    .dark .tfa-subtitle {
        color: #a1a1aa !important;
    }

    .tfa-divider {
        height: 1px;
        background: #e4e4e7;
        margin: 1.5rem 0;
    }

    .dark .tfa-divider {
        background: rgba(255, 255, 255, 0.08) !important;
    }

    .tfa-steps-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.875rem;
    }

    @media (min-width: 640px) {
        .tfa-steps-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    .tfa-step-box {
        background: #fafafa;
        border: 1px solid #e4e4e7;
        border-radius: 0.9rem;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    .dark .tfa-step-box {
        background: rgba(255, 255, 255, 0.03) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }

    .tfa-step-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.65rem;
        height: 1.65rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 800;
        font-family: ui-monospace, monospace;
        background: #18181b;
        color: #fbbf24;
    }

    .dark .tfa-step-num {
        background: rgba(245, 158, 11, 0.16) !important;
        color: #fbbf24 !important;
        border: 1px solid rgba(251, 191, 36, 0.35);
    }

    .tfa-step-title {
        font-size: 0.8125rem;
        font-weight: 700;
        color: #18181b;
    }

    .dark .tfa-step-title {
        color: #f4f4f5 !important;
    }

    .tfa-step-desc {
        font-size: 0.75rem;
        line-height: 1.45;
        color: #71717a;
    }

    .dark .tfa-step-desc {
        color: #a1a1aa !important;
    }

    .tfa-apps-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
    }

    .tfa-app-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.7rem;
        background: #f4f4f5;
        border: 1px solid #e4e4e7;
        color: #27272a;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 600;
    }

    .dark .tfa-app-pill {
        background: rgba(255, 255, 255, 0.05) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        color: #e4e4e7 !important;
    }

    .tfa-footer-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding-top: 1.25rem;
        margin-top: 1.5rem;
        border-top: 1px solid #e4e4e7;
    }

    .dark .tfa-footer-bar {
        border-top-color: rgba(255, 255, 255, 0.08) !important;
    }

    .tfa-side-list {
        display: flex;
        flex-direction: column;
        gap: 0.875rem;
        margin-top: 1rem;
    }

    .tfa-side-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 0.85rem;
        background: #fafafa;
        border: 1px solid #f4f4f5;
    }

    .dark .tfa-side-item {
        background: rgba(255, 255, 255, 0.03) !important;
        border-color: rgba(255, 255, 255, 0.06) !important;
    }

    .tfa-side-item svg {
        width: 1.15rem;
        height: 1.15rem;
        flex-shrink: 0;
        margin-top: 0.1rem;
    }

    .tfa-inner {
        background-color: #fafafa;
        border: 1px solid #e4e4e7;
        border-radius: 1rem;
        padding: 1.25rem;
    }

    .dark .tfa-inner {
        background-color: rgba(255, 255, 255, 0.03) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #f4f4f5 !important;
    }

    .tfa-qr-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        align-items: center;
    }

    @media (min-width: 768px) {
        .tfa-qr-grid {
            grid-template-columns: 220px minmax(0, 1fr);
        }
    }

    .tfa-qr-frame {
        background: #ffffff;
        padding: 0.875rem;
        border-radius: 1rem;
        border: 1px solid #e4e4e7;
        box-shadow: 0 10px 25px -8px rgba(0, 0, 0, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        width: 210px;
        height: 210px;
        margin: 0 auto;
    }

    .tfa-qr-frame img {
        width: 184px;
        height: 184px;
        object-fit: contain;
    }

    .tfa-two-col {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }

    @media (min-width: 768px) {
        .tfa-two-col {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    .tfa-codes-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.625rem;
        margin-top: 0.75rem;
    }

    @media (min-width: 640px) {
        .tfa-codes-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    .tfa-code-chip {
        background-color: #ffffff;
        border: 1px solid #fcd34d;
        color: #18181b;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-weight: 700;
        font-size: 0.8125rem;
        padding: 0.6rem 0.5rem;
        border-radius: 0.65rem;
        text-align: center;
        letter-spacing: 0.06em;
        user-select: all;
    }

    .dark .tfa-code-chip {
        background-color: #09090b !important;
        border-color: rgba(251, 191, 36, 0.35) !important;
        color: #fef3c7 !important;
    }

    .tfa-secret-box {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.65rem;
    }

    .tfa-secret-code {
        flex: 1;
        min-width: 0;
        overflow-x: auto;
        padding: 0.7rem 0.9rem;
        background: #ffffff;
        border: 1px solid #e4e4e7;
        border-radius: 0.75rem;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-weight: 700;
        font-size: 0.875rem;
        letter-spacing: 0.12em;
        color: #b45309;
        user-select: all;
    }

    .dark .tfa-secret-code {
        background: #09090b !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
        color: #fbbf24 !important;
    }

    .tfa-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #3f3f46;
        margin-bottom: 0.45rem;
    }

    .dark .tfa-label {
        color: #d4d4d8 !important;
    }

    .tfa-input {
        width: 100%;
        background-color: #ffffff;
        border: 1px solid #d4d4d8;
        color: #09090b;
        border-radius: 0.75rem;
        padding: 0.75rem 0.95rem;
        font-size: 0.875rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .dark .tfa-input {
        background-color: #09090b !important;
        border-color: #27272a !important;
        color: #ffffff !important;
    }

    .tfa-input:focus {
        outline: none;
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.18) !important;
    }

    .tfa-input-otp {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        text-align: center;
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: 0.35em;
        padding: 0.75rem 1rem;
    }

    .tfa-error {
        margin-top: 0.4rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #e11d48;
    }

    .tfa-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1.35rem;
        border-radius: 0.85rem;
        font-size: 0.8125rem;
        font-weight: 700;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.18s ease;
        text-decoration: none;
        white-space: nowrap;
    }

    .tfa-btn svg {
        width: 1rem;
        height: 1rem;
        flex-shrink: 0;
    }

    .tfa-btn-primary {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #09090b;
        box-shadow: 0 8px 20px -6px rgba(217, 119, 6, 0.55);
    }

    .tfa-btn-primary:hover {
        filter: brightness(1.06);
        transform: translateY(-1px);
        box-shadow: 0 12px 24px -6px rgba(217, 119, 6, 0.65);
    }

    .tfa-btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        box-shadow: 0 8px 20px -6px rgba(5, 150, 105, 0.5);
    }

    .tfa-btn-success:hover {
        filter: brightness(1.06);
        transform: translateY(-1px);
    }

    .tfa-btn-danger {
        background: #fff1f2;
        border-color: #fecdd3;
        color: #be123c;
    }

    .tfa-btn-danger:hover {
        background: #e11d48;
        border-color: #e11d48;
        color: #ffffff;
    }

    .dark .tfa-btn-danger {
        background: rgba(225, 29, 72, 0.14) !important;
        border-color: rgba(244, 63, 94, 0.35) !important;
        color: #fda4af !important;
    }

    .dark .tfa-btn-danger:hover {
        background: #e11d48 !important;
        color: #ffffff !important;
    }

    .tfa-btn-secondary {
        background: #f4f4f5;
        border-color: #e4e4e7;
        color: #27272a;
    }

    .tfa-btn-secondary:hover {
        background: #e4e4e7;
    }

    .dark .tfa-btn-secondary {
        background: rgba(255, 255, 255, 0.06) !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
        color: #f4f4f5 !important;
    }

    .dark .tfa-btn-secondary:hover {
        background: rgba(255, 255, 255, 0.12) !important;
    }

    .tfa-btn-full {
        width: 100%;
    }

    .tfa-danger-zone {
        border-color: #fecdd3 !important;
        background: linear-gradient(180deg, #fff1f2 0%, #ffffff 100%);
    }

    .dark .tfa-danger-zone {
        border-color: rgba(244, 63, 94, 0.28) !important;
        background: rgba(225, 29, 72, 0.05) !important;
    }

    /* 5. VISTAS PREVIAS EN VIVO (SITE SETTINGS HERO, MONTAJES Y PRODUCTOS) */
    .fpv-frame {
        border: 1px solid #e4e4e7;
        border-radius: 1rem;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 8px 24px -10px rgba(9, 9, 11, 0.09);
    }

    .dark .fpv-frame {
        border-color: rgba(255, 255, 255, 0.1) !important;
        background: #141418 !important;
    }

    .fpv-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.65rem 1rem;
        background: #f4f4f5;
        border-bottom: 1px solid #e4e4e7;
    }

    .dark .fpv-toolbar {
        background: #18181b !important;
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
    }

    .fpv-toolbar-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .fpv-dots {
        display: inline-flex;
        gap: 0.32rem;
    }

    .fpv-dots i {
        width: 0.6rem;
        height: 0.6rem;
        border-radius: 9999px;
        display: inline-block;
    }

    .fpv-live-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        color: #52525b;
    }

    .dark .fpv-live-pill {
        color: #d4d4d8 !important;
    }

    .fpv-live-dot {
        width: 0.45rem;
        height: 0.45rem;
        border-radius: 9999px;
        background: #10b981;
        display: inline-block;
    }

    .fpv-mode-switch {
        display: inline-flex;
        background: #e4e4e7;
        padding: 0.18rem;
        border-radius: 9999px;
        gap: 0.15rem;
    }

    .dark .fpv-mode-switch {
        background: #27272a !important;
    }

    .fpv-mode-switch button {
        border: none;
        background: transparent;
        color: #52525b;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.25rem 0.7rem;
        border-radius: 9999px;
        cursor: pointer;
        transition: all 0.18s ease;
    }

    .dark .fpv-mode-switch button {
        color: #a1a1aa !important;
    }

    .fpv-mode-switch button.is-active {
        background: #ffffff;
        color: #09090b;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
    }

    .dark .fpv-mode-switch button.is-active {
        background: #f59e0b !important;
        color: #09090b !important;
    }

    .fpv-canvas.fpv-is-light {
        background: #fafafa;
        color: #09090b;
    }

    .fpv-canvas.fpv-is-dark {
        background: #09090b;
        color: #fafafa;
    }

    .fpv-site-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.7rem 1.15rem;
        border-bottom: 1px solid rgba(113, 113, 122, 0.18);
    }

    .fpv-is-light .fpv-site-header { background: rgba(255, 255, 255, 0.94); }
    .fpv-is-dark .fpv-site-header { background: rgba(24, 24, 27, 0.94); }

    .fpv-logo-img {
        max-height: 2.35rem;
        max-width: 11rem;
        width: auto;
        object-fit: contain;
        display: block;
    }

    .fpv-vys-fallback {
        display: flex;
        align-items: baseline;
        gap: 0.5rem;
    }

    .fpv-vys-mono {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 1.45rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        line-height: 1;
    }

    .fpv-vys-sub {
        font-size: 0.62rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        opacity: 0.65;
        font-family: ui-monospace, monospace;
    }

    .fpv-site-nav {
        display: none;
        gap: 0.95rem;
        font-size: 0.64rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 600;
        opacity: 0.7;
    }

    @media (min-width: 640px) {
        .fpv-site-nav { display: flex; }
    }

    .fpv-site-cta-pill {
        font-size: 0.65rem;
        font-weight: 700;
        padding: 0.32rem 0.75rem;
        border-radius: 9999px;
        background: #18181b;
        color: #ffffff;
        white-space: nowrap;
    }

    .fpv-is-dark .fpv-site-cta-pill {
        background: #f59e0b;
        color: #09090b;
    }

    .fpv-hero-box {
        position: relative;
        min-height: 15.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.25rem 1.5rem;
        overflow: hidden;
        text-align: center;
    }

    .fpv-hero-bg-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 0;
    }

    .fpv-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(9, 9, 11, 0.56) 0%, rgba(9, 9, 11, 0.72) 100%);
        z-index: 1;
    }

    .fpv-hero-orb-1, .fpv-hero-orb-2 {
        position: absolute;
        border-radius: 9999px;
        filter: blur(48px);
        z-index: 0;
        pointer-events: none;
    }

    .fpv-hero-orb-1 {
        width: 15rem; height: 15rem; top: -4rem; left: 15%;
        background: rgba(245, 158, 11, 0.18);
    }

    .fpv-hero-orb-2 {
        width: 13rem; height: 13rem; bottom: -4rem; right: 15%;
        background: rgba(16, 185, 129, 0.14);
    }

    .fpv-hero-content {
        position: relative;
        z-index: 2;
        max-width: 34rem;
    }

    .fpv-hero-content.fpv-has-bg {
        color: #ffffff !important;
    }

    .fpv-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.25rem 0.8rem;
        border-radius: 9999px;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.16em;
        font-family: ui-monospace, monospace;
        background: rgba(245, 158, 11, 0.16);
        border: 1px solid rgba(245, 158, 11, 0.45);
        color: #b45309;
    }

    .fpv-is-dark .fpv-hero-badge,
    .fpv-has-bg .fpv-hero-badge {
        background: rgba(9, 9, 11, 0.55);
        color: #fcd34d;
    }

    .fpv-hero-title {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 1.75rem;
        font-weight: 600;
        line-height: 1.18;
        margin: 0.75rem 0 0;
    }

    .fpv-hero-subtitle {
        font-size: 0.8rem;
        line-height: 1.5;
        opacity: 0.82;
        margin: 0.6rem auto 0;
        max-width: 30rem;
    }

    .fpv-hero-actions {
        margin-top: 1.15rem;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.55rem;
    }

    .fpv-hero-btn-primary {
        padding: 0.5rem 1.1rem;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 700;
        background: #18181b;
        color: #ffffff;
    }

    .fpv-is-dark .fpv-hero-btn-primary,
    .fpv-has-bg .fpv-hero-btn-primary {
        background: #f59e0b;
        color: #09090b;
    }

    .fpv-hero-btn-ghost {
        padding: 0.5rem 1.05rem;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 600;
        border: 1px solid currentColor;
        opacity: 0.75;
    }

    .fpv-status-bar {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        font-size: 0.7rem;
        border-top: 1px solid rgba(113, 113, 122, 0.18);
        opacity: 0.85;
    }

    /* Preview Montaje */
    .fpv-montaje-head {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-end;
        gap: 0.75rem;
        margin-bottom: 0.9rem;
    }

    .fpv-montaje-title {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 1.35rem;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        margin: 0;
        color: #09090b;
    }

    .fpv-montaje-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        margin-top: 0.4rem;
    }

    .fpv-montaje-pills span {
        font-size: 0.68rem;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        background: #ffffff;
        border: 1px solid #e4e4e7;
        color: #3f3f46;
    }

    .fpv-montaje-summary {
        font-size: 0.75rem;
        color: #52525b;
        max-width: 22rem;
        margin: 0;
    }

    .fpv-montaje-cover {
        position: relative;
        height: 15.5rem;
        border-radius: 0.9rem;
        overflow: hidden;
        border: 1px solid #e4e4e7;
    }

    .fpv-montaje-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .fpv-montaje-cover-caption {
        position: absolute;
        inset: auto 0 0 0;
        padding: 1rem;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
        color: #ffffff;
    }

    .fpv-montaje-cover-caption p {
        font-size: 0.75rem;
        margin: 0.35rem 0 0;
        color: #e4e4e7;
    }

    .fpv-cap-badge {
        display: inline-block;
        font-size: 0.62rem;
        font-family: ui-monospace, monospace;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        background: rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.25);
        padding: 0.18rem 0.55rem;
        border-radius: 0.35rem;
    }

    .fpv-montaje-subgrid {
        display: grid;
        gap: 0.65rem;
        margin-top: 0.75rem;
    }

    .fpv-montaje-subitem {
        position: relative;
        height: 7.5rem;
        border-radius: 0.75rem;
        overflow: hidden;
        border: 1px solid #e4e4e7;
    }

    .fpv-montaje-subitem img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .fpv-sub-tag {
        position: absolute;
        bottom: 0.4rem;
        right: 0.4rem;
        font-size: 0.62rem;
        font-family: ui-monospace, monospace;
        background: rgba(0, 0, 0, 0.68);
        color: #fcd34d;
        padding: 0.12rem 0.45rem;
        border-radius: 9999px;
    }

    .fpv-empty-hint {
        margin-top: 0.75rem;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        border: 1px dashed #d4d4d8;
        background: #ffffff;
        font-size: 0.75rem;
        color: #52525b;
        text-align: center;
    }

    /* Tarjeta Portafolio Montaje */
    .fpv-portfolio-card {
        background: #ffffff;
        border: 1px solid #e4e4e7;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 6px 18px -8px rgba(0, 0, 0, 0.12);
    }

    .fpv-portfolio-media {
        position: relative;
        aspect-ratio: 16 / 10;
        overflow: hidden;
        background: #f4f4f5;
    }

    .fpv-portfolio-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .fpv-year-badge {
        position: absolute;
        top: 0.6rem;
        left: 0.6rem;
        background: rgba(0, 0, 0, 0.75);
        color: #ffffff;
        font-size: 0.62rem;
        font-family: ui-monospace, monospace;
        padding: 0.2rem 0.55rem;
        border-radius: 9999px;
    }

    .fpv-gold-badge {
        position: absolute;
        top: 0.6rem;
        right: 0.6rem;
        max-width: 62%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        background: #fbbf24;
        color: #09090b;
        font-size: 0.62rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 9999px;
    }

    .fpv-portfolio-body {
        padding: 1rem;
    }

    .fpv-portfolio-meta {
        font-size: 0.62rem;
        font-family: ui-monospace, monospace;
        text-transform: uppercase;
        color: #92400e;
        font-weight: 700;
        display: block;
    }

    .fpv-portfolio-body h5 {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 1.15rem;
        font-weight: 700;
        margin: 0.25rem 0 0.35rem;
        color: #09090b;
    }

    .fpv-portfolio-body p {
        font-size: 0.75rem;
        color: #71717a;
        line-height: 1.45;
        margin: 0 0 0.85rem;
    }

    .fpv-portfolio-btn {
        text-align: center;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.5rem;
        border-radius: 0.65rem;
        background: #f4f4f5;
        color: #18181b;
    }

    /* Preview Tarjeta Producto */
    .fpv-prod-card {
        width: 100%;
        max-width: 17.5rem;
        background: #ffffff;
        border: 1px solid #e4e4e7;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 6px 20px -8px rgba(0, 0, 0, 0.12);
    }

    .fpv-prod-media {
        position: relative;
        height: 12.5rem;
        background: #f4f4f5;
        overflow: hidden;
    }

    .fpv-prod-media.is-packshot {
        background: #ffffff;
        padding: 0.75rem;
    }

    .fpv-prod-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .fpv-prod-media.is-packshot img {
        object-fit: contain;
    }

    .fpv-prod-code {
        position: absolute;
        top: 0.6rem;
        left: 0.6rem;
        background: rgba(255, 255, 255, 0.9);
        color: #18181b;
        font-size: 0.62rem;
        font-family: ui-monospace, monospace;
        font-weight: 600;
        padding: 0.18rem 0.5rem;
        border-radius: 0.35rem;
        border: 1px solid #e4e4e7;
    }

    .fpv-prod-sale {
        position: absolute;
        top: 0.6rem;
        right: 0.6rem;
        background: #fbbf24;
        color: #09090b;
        font-size: 0.62rem;
        font-weight: 800;
        text-transform: uppercase;
        padding: 0.18rem 0.5rem;
        border-radius: 0.35rem;
    }

    .fpv-prod-body {
        padding: 0.95rem;
    }

    .fpv-prod-cat {
        display: inline-block;
        font-size: 0.6rem;
        font-family: ui-monospace, monospace;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        background: #fffbeb;
        color: #92400e;
        border: 1px solid #fde68a;
        padding: 0.15rem 0.45rem;
        border-radius: 0.3rem;
        font-weight: 700;
    }

    .fpv-prod-body h5 {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 1.08rem;
        font-weight: 700;
        color: #09090b;
        margin: 0.45rem 0 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .fpv-prod-price-row {
        margin-top: 0.65rem;
        padding-top: 0.6rem;
        border-top: 1px solid #f4f4f5;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
    }

    .fpv-prod-price-label {
        font-size: 0.58rem;
        color: #71717a;
        display: block;
    }

    .fpv-prod-price {
        font-family: ui-monospace, monospace;
        font-size: 1.05rem;
        font-weight: 800;
        color: #09090b;
    }

    .fpv-prod-price span {
        font-size: 0.7rem;
        font-weight: 400;
        color: #71717a;
    }

    .fpv-prod-stock {
        font-size: 0.65rem;
        font-family: ui-monospace, monospace;
        font-weight: 700;
    }

    .fpv-prod-stock.in-stock { color: #047857; }
    .fpv-prod-stock.out-stock { color: #a1a1aa; }

    .fpv-prod-btn {
        margin-top: 0.85rem;
        text-align: center;
        padding: 0.55rem;
        border-radius: 0.65rem;
        background: #18181b;
        color: #ffffff;
        font-size: 0.72rem;
        font-weight: 700;
    }
</style>
