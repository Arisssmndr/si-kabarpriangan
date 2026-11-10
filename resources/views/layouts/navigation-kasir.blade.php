<!-- Dedicated Sidebar Navigasi Khusus Admin Kasir (Dominan Hijau Emerald & Kontras Tajam) -->
<aside class="w-full h-full bg-emerald-800 dark:bg-emerald-950 text-white flex flex-col shrink-0 select-none overflow-hidden transition-colors duration-200">

    <!-- 1. Header Brand & Identitas Kasir Kabar Priangan + Tombol Perkecil / Perbesar -->
    <div class="brand-header px-4 h-16 border-b border-emerald-700/80 dark:border-emerald-900 flex items-center justify-between gap-2 shrink-0 bg-emerald-900/40">
        <div class="flex items-center gap-3 min-w-0">
            <div class="brand-logo-container w-9 h-9 rounded-xl bg-white p-1 shadow-xs shrink-0 flex items-center justify-center transition-all duration-200">
                <img src="{{ asset('images/kabarpriangan.png') }}" 
                     alt="Logo Kabar Priangan" 
                     class="h-full w-full object-contain rounded-lg"
                     onerror="this.src='{{ asset('kabarpriangan.png') }}'">
            </div>
            <div class="leading-tight min-w-0 brand-info transition-opacity duration-200">
                <h1 class="font-black text-xs tracking-tight text-white truncate">
                    KABAR PRIANGAN
                </h1>
                <p class="text-[10px] font-bold text-emerald-200 uppercase tracking-wider truncate mt-0.5 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                    Divisi Kasir
                </p>
            </div>
        </div>

        <!-- Tombol Perkecil / Perbesar Sidebar -->
        <button type="button" 
                @click="toggleSidebar()" 
                :title="sidebarExpanded ? 'Perkecil Sidebar' : 'Perbesar Sidebar'"
                class="sidebar-toggle-btn w-8 h-8 rounded-lg bg-emerald-700/60 hover:bg-emerald-600 active:scale-95 text-white border border-emerald-600/60 shadow-2xs flex items-center justify-center shrink-0 transition-all duration-200 cursor-pointer">
            <svg class="toggle-icon w-3.5 h-3.5 transition-transform duration-300" :class="sidebarExpanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5" />
            </svg>
        </button>
    </div>

    <!-- 2. Navigasi Hirarkis Khusus Kasir (Menu Utama Putih Bersih, Submenu Putih Lembut Pudar) -->
    <nav class="flex-1 px-3 py-3.5 space-y-1.5 overflow-y-auto sidebar-scroll">

        <!-- A. DASHBOARD KASIR -->
        @php $isDash = request()->routeIs('kasir.dashboard'); @endphp
        <a href="{{ route('kasir.dashboard') }}" 
           title="Dashboard Kasir"
           class="nav-item-btn group w-full flex items-center gap-3 px-3 py-2 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isDash ? 'bg-white text-emerald-950 font-black shadow-md active-nav' : 'text-white hover:text-white hover:bg-emerald-700/80 font-bold' }}">
            <div class="flex items-center gap-2.5 min-w-0">
                <svg class="nav-main-icon w-5 h-5 shrink-0 transition-transform duration-200 group-hover:scale-110 {{ $isDash ? 'text-emerald-950' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
                <span class="truncate">Dashboard Kasir</span>
            </div>
        </a>

        <!-- Pemisah Menu Operasional -->
        <div class="sidebar-section-header pt-3 pb-1 px-3">
            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-200">Menu Operasional Kasir</span>
        </div>

        <!-- B. KAS MASUK (KI - Cash In) -->
        @php
            $isKiOpen = request()->routeIs('kasir.penerimaan.*') || request()->routeIs('kasir.verifikasi-bilyet*');
        @endphp
        <div x-data="{ open: {{ $isKiOpen ? 'true' : 'false' }} }" class="space-y-0.5">
            <button type="button" 
                    @click="open = !open" 
                    title="Kas Masuk (KI)"
                    class="nav-item-btn group w-full flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isKiOpen ? 'bg-white text-emerald-950 font-black shadow-md active-nav' : 'text-white hover:text-white hover:bg-emerald-700/80 font-bold' }}">
                <div class="flex items-center gap-2.5 min-w-0">
                    <svg class="nav-main-icon w-5 h-5 shrink-0 transition-transform duration-200 group-hover:scale-110 {{ $isKiOpen ? 'text-emerald-950' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 4.5l-15 15m0 0h11.25m-11.25 0V8.25" />
                    </svg>
                    <span class="truncate">Kas Masuk (KI)</span>
                </div>
                <svg class="chevron-arrow w-3.5 h-3.5 transition-transform duration-200 shrink-0 {{ $isKiOpen ? 'text-emerald-950' : 'text-white/80' }}" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>

            <div x-show="open" x-collapse class="pl-4 pr-1 py-1 space-y-1">
                @php $isSub = request()->routeIs('kasir.penerimaan.iklan*'); @endphp
                <a href="{{ route('kasir.penerimaan.iklan') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Input Setoran Iklan</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">BKM</span>
                </a>

                @php $isSub = request()->routeIs('kasir.penerimaan.sirkulasi*'); @endphp
                <a href="{{ route('kasir.penerimaan.sirkulasi') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Input Setoran Koran Agen</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">Agen</span>
                </a>

                @php $isSub = request()->routeIs('kasir.penerimaan.koran-sisa*'); @endphp
                <a href="{{ route('kasir.penerimaan.koran-sisa') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Input Koran Sisa & Eceran</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">Sisa</span>
                </a>

                @php $isSub = request()->routeIs('kasir.penerimaan.bank*'); @endphp
                <a href="{{ route('kasir.penerimaan.bank') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Input Bank / Jasa Giro</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">Bank</span>
                </a>

                @php $isSub = request()->routeIs('kasir.penerimaan.langganan*'); @endphp
                <a href="{{ route('kasir.penerimaan.langganan') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Input Piutang Langganan</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">Dinas</span>
                </a>

                @php $isSub = request()->routeIs('kasir.penerimaan.tunai*'); @endphp
                <a href="{{ route('kasir.penerimaan.tunai') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Input Penerimaan Tunai</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">Tunai</span>
                </a>
            </div>
        </div>

        <!-- C. KAS KELUAR (KO - Cash Out) -->
        @php
            $isKoOpen = request()->routeIs('kasir.pengeluaran.realisasi-bp*') || request()->routeIs('kasir.voucher*') || request()->routeIs('kasir.pengeluaran.komisi*');
        @endphp
        <div x-data="{ open: {{ $isKoOpen ? 'true' : 'false' }} }" class="space-y-0.5">
            <button type="button" 
                    @click="open = !open" 
                    title="Kas Keluar (KO)"
                    class="nav-item-btn group w-full flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isKoOpen ? 'bg-white text-emerald-950 font-black shadow-md active-nav' : 'text-white hover:text-white hover:bg-emerald-700/80 font-bold' }}">
                <div class="flex items-center gap-2.5 min-w-0">
                    <svg class="nav-main-icon w-5 h-5 shrink-0 transition-transform duration-200 group-hover:scale-110 {{ $isKoOpen ? 'text-emerald-950' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                    </svg>
                    <span class="truncate">Kas Keluar (KO)</span>
                </div>
                <svg class="chevron-arrow w-3.5 h-3.5 transition-transform duration-200 shrink-0 {{ $isKoOpen ? 'text-emerald-950' : 'text-white/80' }}" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>

            <div x-show="open" x-collapse class="pl-4 pr-1 py-1 space-y-1">
                @php $isSub = request()->routeIs('kasir.pengeluaran.realisasi-bp*'); @endphp
                <a href="{{ route('kasir.pengeluaran.realisasi-bp') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Realisasi BP Anggaran</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">BKK</span>
                </a>

                @php $isSub = request()->routeIs('kasir.voucher*'); @endphp
                <a href="{{ route('kasir.voucher') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Voucher Kas Keluar</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">Voucher</span>
                </a>

                @php $isSub = request()->routeIs('kasir.pengeluaran.komisi*'); @endphp
                <a href="{{ route('kasir.pengeluaran.komisi') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Pengeluaran Komisi AE</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">Komisi</span>
                </a>
            </div>
        </div>

        <!-- D. KASBON & BANK -->
        @php
            $isKasbonOpen = request()->routeIs('kasir.kasbon*') || request()->routeIs('kasir.setoran-bank*');
        @endphp
        <div x-data="{ open: {{ $isKasbonOpen ? 'true' : 'false' }} }" class="space-y-0.5">
            <button type="button" 
                    @click="open = !open" 
                    title="Kasbon & Bank"
                    class="nav-item-btn group w-full flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isKasbonOpen ? 'bg-white text-emerald-950 font-black shadow-md active-nav' : 'text-white hover:text-white hover:bg-emerald-700/80 font-bold' }}">
                <div class="flex items-center gap-2.5 min-w-0">
                    <svg class="nav-main-icon w-5 h-5 shrink-0 transition-transform duration-200 group-hover:scale-110 {{ $isKasbonOpen ? 'text-emerald-950' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5m16.5 0h-18" />
                    </svg>
                    <span class="truncate">Kasbon & Bank</span>
                </div>
                <svg class="chevron-arrow w-3.5 h-3.5 transition-transform duration-200 shrink-0 {{ $isKasbonOpen ? 'text-emerald-950' : 'text-white/80' }}" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>

            <div x-show="open" x-collapse class="pl-4 pr-1 py-1 space-y-1">
                @php $isSub = request()->routeIs('kasir.kasbon*'); @endphp
                <a href="{{ route('kasir.kasbon') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Kasbon Operasional</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">ACC</span>
                </a>

                @php $isSub = request()->routeIs('kasir.setoran-bank*'); @endphp
                <a href="{{ route('kasir.setoran-bank') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Setor Kas ke Bank</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">Setor</span>
                </a>
            </div>
        </div>

        <!-- E. LAPORAN & MUTASI -->
        @php
            $isLapOpen = request()->routeIs('kasir.laporan*');
        @endphp
        <div x-data="{ open: {{ $isLapOpen ? 'true' : 'false' }} }" class="space-y-0.5">
            <button type="button" 
                    @click="open = !open" 
                    title="Laporan & Mutasi"
                    class="nav-item-btn group w-full flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isLapOpen ? 'bg-white text-emerald-950 font-black shadow-md active-nav' : 'text-white hover:text-white hover:bg-emerald-700/80 font-bold' }}">
                <div class="flex items-center gap-2.5 min-w-0">
                    <svg class="nav-main-icon w-5 h-5 shrink-0 transition-transform duration-200 group-hover:scale-110 {{ $isLapOpen ? 'text-emerald-950' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                    <span class="truncate">Laporan & Mutasi</span>
                </div>
                <svg class="chevron-arrow w-3.5 h-3.5 transition-transform duration-200 shrink-0 {{ $isLapOpen ? 'text-emerald-950' : 'text-white/80' }}" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>

            <div x-show="open" x-collapse class="pl-4 pr-1 py-1 space-y-1">
                @php $isSub = request()->routeIs('kasir.laporan.rekap*'); @endphp
                <a href="{{ route('kasir.laporan.rekap') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Rekapitulasi Kas (KI & KO)</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">Rekap</span>
                </a>

                @php $isSub = request()->routeIs('kasir.laporan.mutasi-bank*'); @endphp
                <a href="{{ route('kasir.laporan.mutasi-bank') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Mutasi Kas Bank</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">Bank</span>
                </a>

                @php $isSub = request()->routeIs('kasir.laporan.perkiraan*'); @endphp
                <a href="{{ route('kasir.laporan.perkiraan') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Mutasi per COA</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">COA</span>
                </a>

                @php $isSub = request()->routeIs('kasir.laporan.posisi-kas*'); @endphp
                <a href="{{ route('kasir.laporan.posisi-kas') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Posisi Saldo Kas Fisik</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">Fisik</span>
                </a>

                @php $isSub = request()->routeIs('kasir.laporan.kasbon*'); @endphp
                <a href="{{ route('kasir.laporan.kasbon') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Monitoring Uang Muka</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">Gantung</span>
                </a>

                @php $isSub = request()->routeIs('kasir.laporan-harian*'); @endphp
                <a href="{{ route('kasir.laporan-harian') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Laporan Kas Harian</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">Harian</span>
                </a>
            </div>
        </div>

        <!-- F. UTILITAS KONTROL SHIFT -->
        @php
            $isUtilOpen = request()->routeIs('kasir.buku-kas*') || request()->routeIs('kasir.utilitas.*') || request()->routeIs('kasir.riwayat-cetak*');
        @endphp
        <div x-data="{ open: {{ $isUtilOpen ? 'true' : 'false' }} }" class="space-y-0.5">
            <button type="button" 
                    @click="open = !open" 
                    title="Utilitas Kontrol"
                    class="nav-item-btn group w-full flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isUtilOpen ? 'bg-white text-emerald-950 font-black shadow-md active-nav' : 'text-white hover:text-white hover:bg-emerald-700/80 font-bold' }}">
                <div class="flex items-center gap-2.5 min-w-0">
                    <svg class="nav-main-icon w-5 h-5 shrink-0 transition-transform duration-200 group-hover:scale-110 {{ $isUtilOpen ? 'text-emerald-950' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                    </svg>
                    <span class="truncate">Utilitas Kontrol</span>
                </div>
                <svg class="chevron-arrow w-3.5 h-3.5 transition-transform duration-200 shrink-0 {{ $isUtilOpen ? 'text-emerald-950' : 'text-white/80' }}" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>

            <div x-show="open" x-collapse class="pl-4 pr-1 py-1 space-y-1">
                @php $isSub = request()->routeIs('kasir.buku-kas*'); @endphp
                <a href="{{ route('kasir.buku-kas') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Buku Kas Harian Real-Time</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">Live</span>
                </a>

                @php $isSub = request()->routeIs('kasir.utilitas.closing*'); @endphp
                <a href="{{ route('kasir.utilitas.closing') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Closing / Tutup Kas</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-rose-200 text-rose-950' : 'bg-rose-900/70 text-rose-100 border border-rose-600/40' }}">Tutup</span>
                </a>

                @php $isSub = request()->routeIs('kasir.utilitas.koreksi*'); @endphp
                <a href="{{ route('kasir.utilitas.koreksi') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Edit & Koreksi Inputan</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-amber-200 text-amber-950' : 'bg-amber-900/70 text-amber-100 border border-amber-600/40' }}">Void</span>
                </a>

                @php $isSub = request()->routeIs('kasir.utilitas.cetak-ulang*'); @endphp
                <a href="{{ route('kasir.utilitas.cetak-ulang') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Cetak Ulang Kuitansi</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">Cetak</span>
                </a>

                @php $isSub = request()->routeIs('kasir.riwayat-cetak*'); @endphp
                <a href="{{ route('kasir.riwayat-cetak') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Riwayat Log Cetak</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">Log</span>
                </a>

                @php $isSub = request()->routeIs('kasir.utilitas.buka-kas*'); @endphp
                <a href="{{ route('kasir.utilitas.buka-kas') }}" 
                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-emerald-50 text-emerald-950 font-bold shadow-xs border border-emerald-200/90' : 'text-white hover:text-white hover:bg-emerald-700/60 font-semibold' }}">
                    <span class="truncate">Buka Kembali Kas (Re-open)</span>
                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-900/60 text-emerald-100 border border-emerald-600/40' }}">Reopen</span>
                </a>
            </div>
        </div>

    </nav>

    <!-- 3. Profil Pengguna & Status Shift Kasir (Footer Docked) -->
    <div class="px-3.5 py-3 border-t border-emerald-700/80 bg-emerald-900/50 shrink-0">
        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-emerald-700 text-white font-bold text-xs flex items-center justify-center border border-emerald-500/50 shrink-0 shadow-2xs">
                    {{ strtoupper(substr(Auth::user()->name ?? 'K', 0, 1)) }}
                </div>
                <div class="min-w-0 leading-tight">
                    <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name ?? 'Kasir' }}</p>
                    <p class="text-[10px] text-emerald-200 truncate flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        Staf Divisi Kasir
                    </p>
                </div>
            </div>

            <!-- Tombol Keluar (Logout) -->
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" 
                        title="Keluar dari Sistem"
                        class="p-2 rounded-lg bg-emerald-700/60 hover:bg-rose-600 text-emerald-100 hover:text-white transition duration-150 cursor-pointer shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
