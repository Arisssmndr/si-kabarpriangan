<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow, noarchive">

    <title>{{ $title ?? 'Portal Inkaso Lapangan — Kabar Priangan' }}</title>
    <link rel="icon" href="{{ asset('kabarpriangan.png') }}" type="image/png">

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] { display: none !important; }
        body {
            background-color: #F8FAFC;
            color: #0F172A;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        .inkaso-header {
            background-color: #0E527B !important;
            background: linear-gradient(135deg, #0E527B 0%, #07334F 100%) !important;
            color: #FFFFFF !important;
            border-bottom: 2px solid #FFB900 !important;
        }
        .bg-\[\#0E527B\] {
            background-color: #0E527B !important;
        }
        .text-\[\#0E527B\] {
            color: #0E527B !important;
        }
        .border-\[\#0E527B\] {
            border-color: #0E527B !important;
        }
        .inkaso-card {
            background-color: #FFFFFF;
            border-radius: 1.25rem;
            box-shadow: 0 2px 8px rgba(14, 82, 123, 0.05);
            border: 1px solid #E2E8F0;
        }
    </style>
</head>
<body class="antialiased bg-slate-50 min-h-screen flex flex-col text-slate-800 overflow-y-auto" x-data="{ showLogoutModal: false }">

    <!-- Top Mobile Navigation Header (Standar 60-30-10 KP-Blue) -->
    <header class="inkaso-header text-white px-4 py-3.5 sticky top-0 z-30 shadow-md border-b-2 border-[#FFB900]" style="background-color: #0E527B !important; background: linear-gradient(135deg, #0E527B 0%, #07334F 100%) !important; color: #FFFFFF !important; border-bottom: 2px solid #FFB900 !important;">
        <div class="max-w-lg mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-8 w-auto object-contain shrink-0 filter drop-shadow brightness-0 invert"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <h1 class="text-sm font-extrabold tracking-tight text-white leading-tight uppercase">PORTAL INKASO</h1>
                    <p class="text-[10px] text-amber-300 font-semibold uppercase tracking-wider">Kolektor Lapangan &bull; Kabar Priangan</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" 
                        @click="showLogoutModal = true" 
                        title="Keluar Akun" 
                        class="p-2 rounded-xl bg-white/10 hover:bg-white/20 active:bg-white/30 text-white transition text-xs font-semibold flex items-center gap-1.5 cursor-pointer border border-white/10">
                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="text-xs font-bold text-white">Keluar</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content Mobile Container (Max Width LG, Tanpa Nav Side, Padding Bawah Ekstra agar tidak terpotong navbar) -->
    <main class="flex-1 max-w-lg w-full mx-auto p-3 sm:p-4" style="padding-bottom: 200px !important;">
        {{ $slot }}
    </main>

    <!-- Modal Konfirmasi Logout Petugas Inkaso -->
    <div x-cloak x-show="showLogoutModal" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl p-5 space-y-4 border border-slate-200" 
             @click.away="showLogoutModal = false">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-[#0E527B] border border-slate-200 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-[#0E527B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Keluar dari Portal?</h3>
                    <p class="text-xs text-slate-500">Selesaikan penagihan sebelum keluar aplikasi.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" 
                        @click="showLogoutModal = false"
                        class="h-9 px-4 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition cursor-pointer">
                    Batal
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="h-9 px-4 rounded-xl bg-[#0E527B] hover:bg-[#0A3D5C] text-white text-xs font-bold shadow-xs transition cursor-pointer">
                        Ya, Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
