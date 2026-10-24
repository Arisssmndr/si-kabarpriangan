<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Proteksi Ketat Anti-Index Mesin Pencari (Internal Enterprise Security) -->
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noimageindex">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">

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

        /* Standar Collapsed Sidebar */
        .sidebar-is-collapsed nav span.truncate,
        .sidebar-is-collapsed nav span.rounded-full,
        .sidebar-is-collapsed nav span.rounded-md,
        .sidebar-is-collapsed nav .uppercase,
        .sidebar-is-collapsed nav .grid-cols-3,
        .sidebar-is-collapsed nav .text-\[9px\],
        .sidebar-is-collapsed nav .text-\[10px\] {
            display: none !important;
        }
        .sidebar-is-collapsed nav a,
        .sidebar-is-collapsed nav button {
            justify-content: center !important;
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }

        /* Sembunyikan scrollbar bawaan browser tapi tetap bisa di-scroll (Cursor / Scrollbar Non) */
        .no-scrollbar,
        .overflow-x-auto,
        .overflow-y-auto,
        .table-responsive,
        .sidebar-scroll,
        aside nav,
        aside {
            -ms-overflow-style: none !important;  /* IE and Edge */
            scrollbar-width: none !important;  /* Firefox */
        }

        .no-scrollbar::-webkit-scrollbar,
        .overflow-x-auto::-webkit-scrollbar,
        .overflow-y-auto::-webkit-scrollbar,
        .table-responsive::-webkit-scrollbar,
        .sidebar-scroll::-webkit-scrollbar,
        aside nav::-webkit-scrollbar,
        aside::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        /* Global Table Header Styling untuk Seluruh Halaman (Kecuali Dokumen Cetak) */
        table:not(.no-brand-table):not(.print-document-sheet table):not(.report-print-container table) > thead {
            background-color: #0A72AC !important;
            border-bottom: 3.5px solid #FFB900 !important;
        }

        table:not(.no-brand-table):not(.print-document-sheet table):not(.report-print-container table) > thead tr,
        table:not(.no-brand-table):not(.print-document-sheet table):not(.report-print-container table) > thead th {
            background-color: transparent !important;
        }

        table:not(.no-brand-table):not(.print-document-sheet table):not(.report-print-container table) > thead th,
        table:not(.no-brand-table):not(.print-document-sheet table):not(.report-print-container table) > thead td {
            color: #FFFFFF !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            font-size: 11px !important;
            letter-spacing: 0.05em !important;
        }

        /* Standar Cetak Dokumen & Kuitansi Resmi (Anti Tampilan Screenshot) */
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

            /* Sembunyikan SEMUA elemen web di luar dokumen cetak */
            aside, nav, header, footer, 
            .no-print, [class*="print:hidden"], 
            button, input[type="button"], input[type="submit"], 
            .select2-container, .swal2-container {
                display: none !important;
            }

            .min-h-screen, .flex, .min-w-0 {
                display: block !important;
                min-height: auto !important;
                height: auto !important;
                width: 100% !important;
                background: #ffffff !important;
            }

            main {
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

            /* Saat mencetak kuitansi modal, sembunyikan seluruh isi halaman belakang */
            .print-hide-when-modal-open {
                display: none !important;
            }

            /* Hilangkan backdrop gelap fixed pada modal cetak */
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

            /* Ubah pop-up menjadi lembar dokumen cetak asli */
            .print-document-sheet {
                position: static !important;
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
                border: 2px solid #000000 !important;
                border-radius: 0 !important;
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
                padding: 12px 16px !important;
            }

            .print-document-sheet * {
                color: #000000 !important;
            }

            .print-document-sheet .bg-amber-50,
            .print-document-sheet .bg-teal-50,
            .print-document-sheet .bg-sky-50,
            .print-document-sheet .bg-blue-50,
            .print-document-sheet .bg-emerald-50,
            .print-document-sheet .bg-slate-50 {
                background-color: #f8fafc !important;
                border: 1px solid #cbd5e1 !important;
            }

            /* Styling cetak laporan tabel resmi */
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
    </style>
</head>

<body class="font-sans antialiased bg-slate-100 text-slate-900 selection:bg-kp-blue-100 selection:text-kp-blue-900">
    <div x-data="{ 
            mobileMenuOpen: false, 
            sidebarCollapsed: localStorage.getItem('kp_sidebar_collapsed') === 'true',
            toggleSidebar() {
                this.sidebarCollapsed = !this.sidebarCollapsed;
                localStorage.setItem('kp_sidebar_collapsed', this.sidebarCollapsed ? 'true' : 'false');
            }
         }" 
         class="min-h-screen flex bg-slate-100">

        <!-- 1. Desktop Sidebar Navigation (Fixed on Left, Collapsible & Print Hidden) -->
        <div class="hidden md:flex md:shrink-0 sticky top-0 h-screen z-40 transition-[width] duration-200 ease-in-out print:hidden"
             :class="sidebarCollapsed ? 'md:w-[72px] sidebar-is-collapsed' : 'md:w-64'">
            @include('layouts.navigation')
        </div>

        <!-- 2. Mobile Drawer Navigation (Slide-over on Mobile) -->
        <div x-cloak x-show="mobileMenuOpen" class="relative z-50 md:hidden print:hidden" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-200"
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-200"
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0"
                 @click="mobileMenuOpen = false" 
                 class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs"></div>

            <div class="fixed inset-0 flex">
                <div x-show="mobileMenuOpen" 
                     x-transition:enter="transition ease-in-out duration-200 transform"
                     x-transition:enter-start="-translate-x-full" 
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in-out duration-200 transform"
                     x-transition:leave-start="translate-x-0" 
                     x-transition:leave-end="-translate-x-full"
                     class="relative mr-16 flex w-full max-w-xs flex-1">
                    @include('layouts.navigation')
                </div>
            </div>
        </div>

        <!-- 3. Right Column: Topbar + Main Content + Footer -->
        <div class="flex-1 flex flex-col min-w-0 min-h-screen">
            <!-- Topbar Bersih (Putih 30%) -->
            <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-3 flex items-center justify-between sticky top-0 z-30 shadow-xs print:hidden">
                <div class="flex items-center gap-3">
                    <!-- Tombol Buka Menu di HP -->
                    <button type="button" 
                            @click="mobileMenuOpen = true"
                            class="md:hidden p-2 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 transition cursor-pointer"
                            title="Buka Menu Navigasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <!-- Tombol Toggle Sidebar Desktop di Topbar (Standar Panel Dock) -->
                    <button type="button" 
                            @click="toggleSidebar()"
                            class="hidden md:flex items-center justify-center p-2 rounded-lg border border-slate-200 text-slate-600 hover:text-[#0E527B] hover:bg-slate-50 transition cursor-pointer shadow-2xs"
                            :title="sidebarCollapsed ? 'Perluas Sidebar' : 'Ciutkan Sidebar'">
                        <svg class="w-4 h-4 transition-transform duration-200" :class="sidebarCollapsed ? '' : 'text-[#0E527B]'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2.5" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v18" />
                        </svg>
                    </button>
                    <!-- Indikator Portal & Tanggal -->
                    <div class="flex items-center text-xs font-medium min-w-0">
                        <span class="font-extrabold text-[#0E527B] text-sm tracking-tight truncate">Kabar Priangan<span class="text-[#FFB900]">.com</span></span>
                        <span class="mx-2 text-slate-300 font-light">|</span>
                        <span class="font-semibold text-slate-700 text-xs truncate">{{ Auth::user()->role_label ?? 'Divisi Iklan' }}</span>
                        <span class="mx-2 text-slate-300 font-light hidden md:inline">•</span>
                        <span class="text-slate-500 text-[11px] hidden md:inline">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                    </div>
                </div>

                <!-- Profil Kasir / User di Kanan Atas Header (Standar Web Modern) -->
                <div x-data="{ userMenuOpen: false }" class="relative">
                    <button @click="userMenuOpen = !userMenuOpen" 
                            @click.outside="userMenuOpen = false"
                            type="button" 
                            class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg border border-slate-300 hover:bg-slate-50 transition text-left cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-kp-blue-600 text-white font-semibold text-xs flex items-center justify-center shrink-0 shadow-xs">
                            {{ strtoupper(substr(Auth::user()?->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="hidden sm:block leading-tight">
                            <div class="text-xs font-semibold text-black leading-tight">
                                {{ Auth::user()?->name ?? 'User' }}
                            </div>
                            <div class="text-[10px] text-gray-500 font-normal">
                                {{ Auth::user()?->role_label ?? 'Staf' }}
                            </div>
                        </div>
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-cloak
                         x-show="userMenuOpen" 
                         x-transition.duration.150ms
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-1.5 z-50 divide-y divide-slate-100">
                        
                        <div class="px-3.5 py-2">
                            <p class="text-xs font-semibold text-black">{{ Auth::user()?->name ?? 'User' }}</p>
                            <p class="text-[11px] text-gray-500 truncate">{{ Auth::user()?->email ?? '-' }}</p>
                            <span class="inline-block mt-1 px-1.5 py-0.5 text-[9px] font-medium uppercase rounded bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                {{ Auth::user()->role_label ?? 'Staf' }}
                            </span>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" 
                               class="flex items-center gap-2 px-3.5 py-2 text-xs text-gray-800 hover:bg-slate-50 hover:text-kp-blue-600 transition font-medium">
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
                                        class="w-full text-left px-3.5 py-2 text-xs text-rose-600 hover:bg-rose-50 transition font-medium cursor-pointer flex items-center gap-2">
                                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                    </svg>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Main Content Slot -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 print:p-0 print:m-0 print:max-w-none">
                <div class="max-w-7xl mx-auto print:max-w-none print:w-full print:m-0 print:p-0">
                    {{ $slot }}
                </div>
            </main>

            <!-- Footer Bersih -->
            <footer class="bg-white border-t border-slate-200 py-4 px-6 text-center text-xs text-slate-600 font-normal print:hidden">
                &copy; {{ date('Y') }} <span class="font-semibold text-slate-900">Harian Umum Kabar Priangan</span> — Sistem Informasi Kasir Iklan (SIKAPRI)
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
                message: '{{ session('success') }}',
                timer: 2400
            });
        @endif

        @if (session('error'))
            AppAlert.error({
                title: 'Terjadi Kesalahan',
                message: '{{ session('error') }}',
                buttonText: 'Tutup'
            });
        @endif
    </script>
</body>

</html>
