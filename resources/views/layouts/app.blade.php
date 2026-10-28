<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Dark Mode Initial State (Anti-Flicker) -->
    <script>
        if (localStorage.getItem('kp_theme') === 'dark' || (!('kp_theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <title>{{ config('app.name', 'Kabar Priangan - Kasir Iklan Kabar Priangan') }}</title>
    <link rel="icon" href="{{ asset('kabarpriangan.png') }}" type="image/png">

    <!-- Fonts: Plus Jakarta Sans (Standar Media Cetak Modern) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Select2 Stylesheet (Minimal) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- jQuery & Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/html5-qrcode.min.js') }}"></script>

    <style>
        [x-cloak] { display: none !important; }
        .select2-container .select2-selection--single {
            width: 100% !important;
            background-color: #ffffff;
            border: 1px solid #cbd5e1 !important;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            height: 42px;
            border-radius: 0.75rem;
            color: #111827;
        }

        .select2-container .select2-selection--single .select2-selection__arrow {
            top: 20% !important;
            right: 10px;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            font-size: 14px !important;
            top: -2px;
            left: -4px;
            position: relative;
            color: #111827;
        }

        .select2-search__field {
            font-size: 14px !important;
            border-radius: 0.5rem;
            border: 1px solid #cbd5e1;
        }

        .select2-results {
            font-size: 14px !important;
        }

        /* Standar Cetak Dokumen & Kuitansi Resmi (Anti Tampilan Screenshot & Bebas Elemen Web) */
        @media print {
            @page {
                size: auto;
                margin: 8mm 12mm;
            }

            html, body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 11pt !important;
                width: 100% !important;
                height: auto !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* 1. Sembunyikan SEMUA elemen web di luar dokumen cetak */
            aside, nav, header, footer, #sidebar,
            .no-print, [class*="print:hidden"], .print\:hidden,
            button, input[type="button"], input[type="submit"], 
            .select2-container, .swal2-container, .modal-backdrop {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                width: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                overflow: hidden !important;
            }

            /* 2. Reset wrapper layout utama aplikasi tanpa merusak layout flex di dalam dokumen cetak */
            body > div,
            body > div > div.flex-1 {
                display: block !important;
                min-height: 0 !important;
                height: auto !important;
                width: 100% !important;
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            main {
                display: block !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            main > div {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* 3. Utilitas print display Tailwind */
            .print\:block, [class*="print:block"] {
                display: block !important;
            }

            .print\:flex, [class*="print:flex"] {
                display: flex !important;
            }

            .print\:grid, [class*="print:grid"] {
                display: grid !important;
            }

            /* 4. Saat mencetak kuitansi modal, sembunyikan seluruh isi halaman belakang */
            body:has(.print-document-sheet) main > div > div > .space-y-6:first-child,
            body:has(.print-document-sheet) [class*="space-y-6"]:not(:has(.print-document-sheet)),
            .print-hide-when-modal-open {
                display: none !important;
            }

            /* 5. Hilangkan backdrop gelap fixed pada modal cetak */
            .modal-print-overlay {
                position: static !important;
                inset: auto !important;
                width: 100% !important;
                height: auto !important;
                background: transparent !important;
                backdrop-filter: none !important;
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
                z-index: auto !important;
                overflow: visible !important;
            }

            /* 6. Ubah pop-up menjadi lembar dokumen cetak asli */
            .print-document-sheet {
                position: static !important;
                width: 100% !important;
                max-width: 180mm !important;
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
                border: 2px solid #000000 !important;
                border-radius: 4px !important;
                box-shadow: none !important;
                margin: 0 auto !important;
                padding: 0 !important;
                background: #ffffff !important;
                color: #000000 !important;
                page-break-inside: avoid !important;
            }

            .print-document-body {
                overflow: visible !important;
                max-height: none !important;
                padding: 16px 20px !important;
            }

            /* Sembunyikan footer modal (Tombol Tutup & Tombol Cetak) di dalam dokumen */
            .print-document-sheet .print\:hidden,
            .print-document-sheet [class*="print:hidden"],
            .print-document-sheet button,
            .print-document-sheet .border-t.bg-slate-50\/80,
            .print-document-sheet [class*="border-t"][class*="bg-slate-50"] {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
            }

            .print-document-sheet * {
                color: #000000 !important;
            }

            .print-document-sheet .bg-amber-50,
            .print-document-sheet .bg-teal-50,
            .print-document-sheet .bg-sky-50,
            .print-document-sheet .bg-blue-50,
            .print-document-sheet .bg-emerald-50,
            .print-document-sheet .bg-rose-50,
            .print-document-sheet .bg-slate-50 {
                background-color: #f8fafc !important;
                border: 1px solid #cbd5e1 !important;
            }

            /* 7. Styling cetak laporan tabel resmi (.report-print-container) */
            .report-print-container {
                border: none !important;
                box-shadow: none !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
            }

            .report-print-container table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            .report-print-container th, 
            .report-print-container td {
                border: 1px solid #334155 !important;
                color: #000000 !important;
                padding: 6px 8px !important;
            }

            .report-print-container th {
                background-color: #f1f5f9 !important;
                font-weight: bold !important;
            }
        }

        /* ========================================================================== */
        /* SIDEBAR DOCKED TERPADU (SEAMLESS INTEGRATED DASHBOARD)                     */
        /* ========================================================================== */
        
        /* 1. Transisi Lebar Sidebar Ultra-Smooth & Snappy */
        #desktop-sidebar-container {
            transition: width 0.22s cubic-bezier(0.16, 1, 0.3, 1) !important;
            will-change: width;
        }

        #desktop-sidebar-container aside {
            width: 100% !important;
            height: 100% !important;
            overflow: hidden !important;
            white-space: nowrap !important;
        }

        /* 2. Menu Utama Aktif (Parent Nav): HIJAU EMERALD SOLID (#059669 - #10b981) */
        aside nav .nav-item-btn.active-nav,
        aside nav a.nav-item-btn.active-nav,
        aside nav button.nav-item-btn.active-nav {
            background-color: #059669 !important;
            background-image: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border: 1px solid rgba(255, 255, 255, 0.25) !important;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25) !important;
            border-radius: 0.75rem !important; /* rounded-xl */
        }

        /* Icon & teks di menu utama aktif menjadi putih murni */
        aside nav .nav-item-btn.active-nav svg,
        aside nav .nav-item-btn.active-nav span {
            color: #ffffff !important;
            stroke: #ffffff !important;
        }

        /* 3. SUB MENU AKTIF: MODE TERANG (MINT SAMAR) VS MODE GELAP (TRANSLUCENT EMERALD GLOW) */
        html:not(.dark) aside nav .submenu-container a.sub-nav-active {
            background-color: #e6f9f0 !important;
            background-image: none !important;
            color: #047857 !important;
            font-weight: 700 !important;
            border: 1px solid #86efac !important;
            box-shadow: 0 1px 3px rgba(16, 185, 129, 0.08) !important;
            border-radius: 0.625rem !important;
        }

        html:not(.dark) aside nav .submenu-container a.sub-nav-active span:first-child {
            color: #047857 !important;
            font-weight: 700 !important;
            stroke: none !important;
        }

        html:not(.dark) aside nav .submenu-container a.sub-nav-active .sub-badge-active,
        html:not(.dark) aside nav .submenu-container a.sub-nav-active span:last-child {
            background-color: #bbf7d0 !important;
            color: #065f46 !important;
            border: 1px solid #86efac !important;
            font-weight: 700 !important;
        }

        /* Mode Gelap - Sub Menu Aktif (Sleek Translucent Emerald Pill - Elegan & Lembut) */
        html.dark aside nav .submenu-container a.sub-nav-active {
            background-color: rgba(16, 185, 129, 0.16) !important;
            background-image: none !important;
            color: #34d399 !important;
            font-weight: 700 !important;
            border: 1px solid rgba(52, 211, 153, 0.35) !important;
            box-shadow: 0 0 14px rgba(16, 185, 129, 0.12) !important;
            border-radius: 0.625rem !important;
        }

        html.dark aside nav .submenu-container a.sub-nav-active span:first-child {
            color: #34d399 !important;
            font-weight: 700 !important;
            stroke: none !important;
        }

        html.dark aside nav .submenu-container a.sub-nav-active .sub-badge-active,
        html.dark aside nav .submenu-container a.sub-nav-active span:last-child {
            background-color: rgba(16, 185, 129, 0.25) !important;
            color: #6ee7b7 !important;
            border: 1px solid rgba(52, 211, 153, 0.45) !important;
            font-weight: 700 !important;
        }

        /* Sub-menu non-aktif: Mode Terang */
        html:not(.dark) aside nav .submenu-container a.sub-nav-idle {
            background: transparent !important;
            background-image: none !important;
            border: 1px solid transparent !important;
            box-shadow: none !important;
            color: #475569 !important;
            font-weight: 500 !important;
        }
        html:not(.dark) aside nav .submenu-container a.sub-nav-idle span:first-child {
            color: #475569 !important;
            font-weight: 500 !important;
        }
        html:not(.dark) aside nav .submenu-container a.sub-nav-idle .sub-badge-idle,
        html:not(.dark) aside nav .submenu-container a.sub-nav-idle span:last-child {
            background-color: #f1f5f9 !important;
            color: #64748b !important;
            border: 1px solid #e2e8f0 !important;
            font-weight: 600 !important;
        }

        html:not(.dark) aside nav .submenu-container a.sub-nav-idle:hover {
            background-color: #f0fdf4 !important;
            color: #047857 !important;
            border-color: #d1fae5 !important;
        }
        html:not(.dark) aside nav .submenu-container a.sub-nav-idle:hover span:first-child {
            color: #047857 !important;
        }

        /* Sub-menu non-aktif: Mode Gelap */
        html.dark aside nav .submenu-container a.sub-nav-idle {
            background: transparent !important;
            background-image: none !important;
            border: 1px solid transparent !important;
            box-shadow: none !important;
            color: #94a3b8 !important;
            font-weight: 500 !important;
        }
        html.dark aside nav .submenu-container a.sub-nav-idle span:first-child {
            color: #94a3b8 !important;
            font-weight: 500 !important;
        }
        html.dark aside nav .submenu-container a.sub-nav-idle .sub-badge-idle,
        html.dark aside nav .submenu-container a.sub-nav-idle span:last-child {
            background-color: rgba(255, 255, 255, 0.05) !important;
            color: #94a3b8 !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            font-weight: 600 !important;
        }
        html.dark aside nav .submenu-container a.sub-nav-idle:hover {
            background-color: rgba(255, 255, 255, 0.04) !important;
            color: #34d399 !important;
            border-color: rgba(52, 211, 153, 0.2) !important;
        }
        html.dark aside nav .submenu-container a.sub-nav-idle:hover span:first-child {
            color: #34d399 !important;
        }

        /* Tombol Navigasi */
        aside nav .nav-item-btn {
            border-radius: 0.75rem !important;
            transition: background-color 0.15s ease, color 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease !important;
        }
        aside nav .nav-item-btn:hover {
            transform: translateY(-1px);
        }
        aside nav .nav-item-btn:active {
            transform: scale(0.98);
        }

        /* Hover pada menu utama non-aktif (Mode Terang vs Mode Gelap) */
        html:not(.dark) aside nav a.nav-item-btn:not(.active-nav):hover,
        html:not(.dark) aside nav button.nav-item-btn:not(.active-nav):hover {
            background-color: rgba(209, 250, 229, 0.75) !important;
            color: #047857 !important;
        }
        html:not(.dark) aside nav a.nav-item-btn:not(.active-nav):hover svg.nav-main-icon,
        html:not(.dark) aside nav button.nav-item-btn:not(.active-nav):hover svg.nav-main-icon {
            color: #047857 !important;
            stroke: #047857 !important;
            transform: scale(1.05);
        }

        html.dark aside nav a.nav-item-btn:not(.active-nav):hover,
        html.dark aside nav button.nav-item-btn:not(.active-nav):hover {
            background-color: rgba(16, 185, 129, 0.12) !important;
            color: #34d399 !important;
        }
        html.dark aside nav a.nav-item-btn:not(.active-nav):hover svg.nav-main-icon,
        html.dark aside nav button.nav-item-btn:not(.active-nav):hover svg.nav-main-icon {
            color: #34d399 !important;
            stroke: #34d399 !important;
            transform: scale(1.05);
        }

        /* Connector Tree Lines Mode Gelap */
        html.dark aside nav .bg-emerald-200 {
            background-color: rgba(52, 211, 153, 0.3) !important;
        }
        html.dark aside nav .border-emerald-300 {
            border-color: rgba(52, 211, 153, 0.4) !important;
        }
        html.dark aside nav .bg-slate-300\/80 {
            background-color: rgba(148, 163, 184, 0.2) !important;
        }
        html.dark aside nav .border-slate-300\/80 {
            border-color: rgba(148, 163, 184, 0.25) !important;
        }
        html.dark aside nav .text-emerald-700\/90 {
            color: #34d399 !important;
        }

        /* 4. TAMPILAN TEXT SAAT EXPANDED */
        #desktop-sidebar-container.sidebar-expanded aside .brand-info,
        #desktop-sidebar-container.sidebar-expanded aside .footer-user-info,
        #desktop-sidebar-container.sidebar-expanded aside span.truncate,
        #desktop-sidebar-container.sidebar-expanded aside .sidebar-badge,
        #desktop-sidebar-container.sidebar-expanded aside .chevron-arrow,
        #desktop-sidebar-container.sidebar-expanded aside span[class*="uppercase"] {
            opacity: 1 !important;
            visibility: visible !important;
            transition: opacity 0.15s ease-out !important;
        }

        /* 5. MODE COLLAPSED */
        #desktop-sidebar-container.sidebar-collapsed aside .brand-info,
        #desktop-sidebar-container.sidebar-collapsed aside .footer-user-info,
        #desktop-sidebar-container.sidebar-collapsed aside span.truncate,
        #desktop-sidebar-container.sidebar-collapsed aside span[class*="rounded-full"]:not(.nav-dot),
        #desktop-sidebar-container.sidebar-collapsed aside .sidebar-badge,
        #desktop-sidebar-container.sidebar-collapsed aside .chevron-arrow,
        #desktop-sidebar-container.sidebar-collapsed aside svg:not(.nav-main-icon):not(.toggle-icon),
        #desktop-sidebar-container.sidebar-collapsed aside [x-show*="open"],
        #desktop-sidebar-container.sidebar-collapsed aside div[x-cloak][x-show],
        #desktop-sidebar-container.sidebar-collapsed aside .submenu-container,
        #desktop-sidebar-container.sidebar-collapsed aside .pl-6,
        #desktop-sidebar-container.sidebar-collapsed aside span[class*="uppercase"] {
            opacity: 0 !important;
            visibility: hidden !important;
            display: none !important;
            pointer-events: none !important;
        }

        /* Tombol ikon saat collapsed */
        #desktop-sidebar-container.sidebar-collapsed aside nav a.nav-item-btn,
        #desktop-sidebar-container.sidebar-collapsed aside nav button.nav-item-btn {
            width: 2.75rem !important;
            height: 2.75rem !important;
            padding: 0 !important;
            margin-left: auto !important;
            margin-right: auto !important;
            justify-content: center !important;
            align-items: center !important;
            border-radius: 0.75rem !important;
        }
        html:not(.dark) #desktop-sidebar-container.sidebar-collapsed aside nav a.nav-item-btn:not(.active-nav),
        html:not(.dark) #desktop-sidebar-container.sidebar-collapsed aside nav button.nav-item-btn:not(.active-nav) {
            background-color: #f1f5f9 !important;
            color: #64748b !important;
        }
        html:not(.dark) #desktop-sidebar-container.sidebar-collapsed aside nav a.nav-item-btn:not(.active-nav):hover,
        html:not(.dark) #desktop-sidebar-container.sidebar-collapsed aside nav button.nav-item-btn:not(.active-nav):hover {
            background-color: #d1fae5 !important;
            color: #059669 !important;
        }
        html.dark #desktop-sidebar-container.sidebar-collapsed aside nav a.nav-item-btn:not(.active-nav),
        html.dark #desktop-sidebar-container.sidebar-collapsed aside nav button.nav-item-btn:not(.active-nav) {
            background-color: rgba(255, 255, 255, 0.05) !important;
            color: #94a3b8 !important;
        }
        html.dark #desktop-sidebar-container.sidebar-collapsed aside nav a.nav-item-btn:not(.active-nav):hover,
        html.dark #desktop-sidebar-container.sidebar-collapsed aside nav button.nav-item-btn:not(.active-nav):hover {
            background-color: rgba(16, 185, 129, 0.2) !important;
            color: #34d399 !important;
        }
        #desktop-sidebar-container.sidebar-collapsed aside nav a.nav-item-btn > div,
        #desktop-sidebar-container.sidebar-collapsed aside nav button.nav-item-btn > div {
            gap: 0 !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Header Brand Logo & Toggle saat collapsed */
        #desktop-sidebar-container.sidebar-collapsed aside .brand-header {
            padding: 0 0.5rem !important;
            justify-content: center !important;
            flex-direction: row !important;
            height: 4rem !important;
        }
        #desktop-sidebar-container.sidebar-collapsed aside .brand-logo-container {
            margin: 0 auto !important;
            width: 2.5rem !important;
            height: 2.5rem !important;
        }
        #desktop-sidebar-container.sidebar-collapsed aside .sidebar-toggle-btn {
            display: none !important;
        }

        /* Footer User Profile saat collapsed */
        #desktop-sidebar-container.sidebar-collapsed aside .footer-card {
            padding: 0 !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            justify-content: center !important;
        }
        #desktop-sidebar-container.sidebar-collapsed aside .footer-card .user-avatar {
            width: 2.5rem !important;
            height: 2.5rem !important;
            margin: 0 auto !important;
            border-radius: 0.75rem !important;
        }

        /* Custom scrollbar sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        html:not(.dark) .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #a7f3d0;
            border-radius: 4px;
        }
        html:not(.dark) .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #6ee7b7;
        }
        html.dark .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 4px;
        }
        html.dark .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #334155;
        }
    </style>
</head>

<body class="font-sans antialiased bg-slate-100/80 dark:bg-slate-950 text-slate-900 dark:text-slate-100 selection:bg-emerald-100 selection:text-emerald-900 transition-colors duration-200">
    <div x-data="{ 
            mobileMenuOpen: false,
            sidebarExpanded: (localStorage.getItem('kp_sidebar_expanded') === 'true'),
            isHovered: false,
            isDark: document.documentElement.classList.contains('dark'),
            toggleSidebar() {
                this.sidebarExpanded = !this.sidebarExpanded;
                localStorage.setItem('kp_sidebar_expanded', this.sidebarExpanded);
            },
            toggleTheme() {
                this.isDark = !this.isDark;
                if (this.isDark) {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('kp_theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('kp_theme', 'light');
                }
            }
          }" 
         class="min-h-screen flex bg-slate-100/80 dark:bg-slate-950 transition-colors duration-200">

        <!-- 1. Desktop Sidebar Navigation (Docked Full Height) -->
        <div id="desktop-sidebar-container" 
             class="hidden md:flex flex-col h-screen sticky top-0 shrink-0 z-40 print:hidden no-print transition-[width] duration-200 ease-out border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900"
             :class="(sidebarExpanded || isHovered) ? 'w-72 sidebar-expanded' : 'w-20 sidebar-collapsed'"
             @mouseenter="if (!sidebarExpanded) isHovered = true"
             @mouseleave="isHovered = false">
            @include('layouts.navigation')
        </div>

        <!-- 2. Mobile Drawer Navigation (Slide-over on Mobile) -->
        <div x-cloak x-show="mobileMenuOpen" class="relative z-50 md:hidden print:hidden no-print" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-200"
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-200"
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0"
                 @click="mobileMenuOpen = false" 
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

            <div class="fixed inset-0 flex">
                <div x-show="mobileMenuOpen" 
                     x-transition:enter="transition ease-in-out duration-200 transform"
                     x-transition:enter-start="-translate-x-full" 
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in-out duration-200 transform"
                     x-transition:leave-start="translate-x-0" 
                     x-transition:leave-end="-translate-x-full"
                     class="relative mr-16 flex w-full max-w-xs flex-1 bg-white dark:bg-slate-900 shadow-2xl">
                    @include('layouts.navigation')
                </div>
            </div>
        </div>

        <!-- 3. Right Column: Topbar + Main Content + Footer -->
        <div class="flex-1 flex flex-col min-w-0 min-h-screen transition-all duration-300 ease-in-out">
            <!-- Topbar Bersih -->
            <header class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-xs border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 h-16 flex items-center justify-between sticky top-0 z-30 shadow-2xs print:hidden no-print transition-colors duration-200">
                <div class="flex items-center gap-3">
                    <!-- Tombol Buka Menu di HP -->
                    <button type="button" 
                            @click="mobileMenuOpen = true"
                            class="md:hidden px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold text-xs transition">
                        Menu
                    </button>

                    <!-- Tombol Perkecil / Perbesar Sidebar di Desktop Topbar -->
                    <button type="button" 
                            @click="toggleSidebar()" 
                            class="hidden md:inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-slate-700 hover:border-emerald-300 dark:hover:border-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-400 text-slate-600 dark:text-slate-300 text-xs font-bold transition-all duration-200 shadow-2xs cursor-pointer"
                            :title="sidebarExpanded ? 'Perkecil Sidebar' : 'Perbesar Sidebar'">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 transition-transform duration-300" :class="sidebarExpanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                        </svg>
                        <span x-text="sidebarExpanded ? 'Perkecil' : 'Perbesar'"></span>
                    </button>

                    <!-- Indikator Portal & Tanggal -->
                    <div class="hidden sm:flex items-center text-xs text-slate-500 dark:text-slate-400 font-medium">
                        <span class="font-bold text-emerald-700 dark:text-emerald-400">Harian Umum Kabar Priangan</span>
                        <span class="mx-2 text-slate-300 dark:text-slate-700">/</span>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <!-- Tombol Toggle Mode Terang / Gelap (Sun & Moon Switcher) -->
                    <button type="button" 
                            @click="toggleTheme()" 
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all duration-200 shadow-2xs cursor-pointer"
                            :title="isDark ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'">
                        <template x-if="isDark">
                            <div class="flex items-center gap-1.5 text-amber-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                </svg>
                                <span class="hidden sm:inline font-semibold">Mode Terang</span>
                            </div>
                        </template>
                        <template x-if="!isDark">
                            <div class="flex items-center gap-1.5 text-slate-700">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                                </svg>
                                <span class="hidden sm:inline font-semibold">Mode Gelap</span>
                            </div>
                        </template>
                    </button>

                    <!-- Profil Kasir / User di Kanan Atas Header (Standar Web Modern) -->
                    <div x-data="{ userMenuOpen: false }" class="relative">
                        <button @click="userMenuOpen = !userMenuOpen" 
                                @click.outside="userMenuOpen = false"
                                type="button" 
                                class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition text-left cursor-pointer">
                            <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-xs">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden sm:block leading-tight">
                                <div class="text-xs font-bold text-slate-900 dark:text-slate-100 leading-tight">
                                    {{ Auth::user()->name }}
                                </div>
                                <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">
                                    {{ Auth::user()->role_label ?? 'Staf' }}
                                </div>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-cloak
                             x-show="userMenuOpen" 
                             x-transition.duration.150ms
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 py-1.5 z-50 divide-y divide-slate-100 dark:divide-slate-700">
                            
                            <div class="px-3.5 py-2">
                                <p class="text-xs font-semibold text-slate-900 dark:text-white">{{ Auth::user()->name }}</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()->email }}</p>
                                <span class="inline-block mt-1 px-1.5 py-0.5 text-[9px] font-semibold uppercase rounded bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                    {{ Auth::user()->role_label ?? 'Staf' }}
                                </span>
                            </div>

                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}" 
                                   class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-emerald-600 dark:hover:text-emerald-400 transition font-medium">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                    <span>Profil Saya</span>
                                </a>
                            </div>

                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                                    @csrf
                                    <button type="button" 
                                            onclick="confirmLogout()"
                                            class="w-full text-left px-3.5 py-2 text-xs text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition font-medium cursor-pointer flex items-center gap-2">
                                        <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                        </svg>
                                        <span>Keluar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Main Content Slot -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 print:p-0 print:m-0 print:max-w-none transition-colors duration-200">
                <div class="max-w-7xl mx-auto print:max-w-none print:w-full print:m-0 print:p-0">
                    {{ $slot }}
                </div>
            </main>

            <!-- Footer Bersih -->
            <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-4 px-6 text-center text-xs text-slate-600 dark:text-slate-400 font-normal print:hidden no-print transition-colors duration-200">
                &copy; {{ date('Y') }} <span class="font-semibold text-slate-900 dark:text-slate-200">Harian Umum Kabar Priangan</span> — Sistem Informasi Terpadu
            </footer>
        </div>

    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            if ($(".js-example-placeholder-single").length > 0) {
                $(".js-example-placeholder-single").select2({
                    placeholder: "Pilih...",
                    allowClear: true,
                    width: '100%'
                });
            }
        });
    </script>
    @include('partials.alert-scripts')
    @stack('scripts')

    <!-- SweetAlert Notifikasi Flash Sesi -->
    <script>
        @if (session('success'))
            AppAlert.success({
                title: 'Berhasil',
                subtitle: 'Data telah diperbarui',
                message: '{{ session('success') }}',
                timer: 2200
            });
        @endif

        @if (session('error'))
            AppAlert.error({
                title: 'Terjadi Kesalahan',
                subtitle: 'Operasi gagal diproses',
                message: '{{ session('error') }}',
                buttonText: 'Tutup'
            });
        @endif
    </script>
</body>

</html>
