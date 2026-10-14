<!-- Sidebar Navigasi Kabar Priangan (Modern Enterprise Industry Standard 2026) -->
<aside class="w-64 bg-[#F8FAFC] text-slate-800 flex flex-col shrink-0 h-full border-r border-slate-200 select-none shadow-[1px_0_4px_rgba(0,0,0,0.02)]"
       x-data="{
           @php
               // Deteksi divisi aktif berdasarkan route saat ini
               $currentDiv = 'all';
               if (request()->routeIs('iklan.*') || request()->routeIs('transaksikoran.*') || request()->routeIs('transaksionline.*') || request()->routeIs('transaksipriangan.*') || request()->routeIs('laporan.*') || request()->routeIs('kategori-media.*') || request()->routeIs('jenis-iklan.*') || request()->routeIs('pelanggan.*') || request()->routeIs('pembayaran-iklan.*')) {
                   $currentDiv = 'iklan';
               } elseif (request()->routeIs('sirkulasi.*')) {
                   $currentDiv = 'sirkulasi';
               } elseif (request()->routeIs('kasir.*')) {
                   $currentDiv = 'kasir';
               } elseif (request()->routeIs('accounting.*')) {
                   $currentDiv = 'accounting';
               } elseif (request()->routeIs('keuangan.*')) {
                   $currentDiv = 'keuangan';
               }
           @endphp
           searchQuery: '',
           filterDivision: 'all',
           openDivision: '{{ $currentDiv === 'all' ? 'iklan' : $currentDiv }}',
           toggleDivision(div) {
               this.openDivision = this.openDivision === div ? '' : div;
           },
           matchSearch(text) {
               if (!this.searchQuery.trim()) return true;
               return text.toLowerCase().includes(this.searchQuery.toLowerCase().trim());
           }
       }">

    <!-- 1. Header Brand & Identitas Perusahaan -->
    <div class="px-4 py-3.5 border-b border-slate-200/80 flex items-center justify-between bg-white shrink-0">
        <div class="flex items-center gap-2.5 min-w-0">
            <img src="{{ asset('images/kabarpriangan.png') }}" 
                 alt="Logo Kabar Priangan" 
                 class="h-9 w-9 object-contain rounded-lg bg-white p-0.5 shadow-2xs border border-slate-200 shrink-0"
                 onerror="this.src='{{ asset('kabarpriangan.png') }}'">
            <div class="leading-tight min-w-0">
                <h1 class="font-black text-xs tracking-tight text-slate-900 truncate">
                    KABAR PRIANGAN
                </h1>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider truncate">
                        {{ Auth::user()->role_label ?? 'Portal Terpadu' }}
                    </span>
                </div>
            </div>
        </div>
        @if(Auth::user() && Auth::user()->isAdmin())
            <span class="px-1.5 py-0.5 text-[9px] font-extrabold uppercase rounded bg-rose-50 text-rose-700 border border-rose-200 shrink-0">
                Admin
            </span>
        @endif
    </div>

    @php
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $isIklan = $user && ($isAdmin || $user->hasRole('iklan'));
        $isKeuangan = $user && ($isAdmin || $user->hasRole('keuangan'));
        $isAccounting = $user && ($isAdmin || $user->hasRole('accounting'));
        $isSirkulasi = $user && ($isAdmin || $user->hasRole('sirkulasi'));
        $isKasir = $user && ($isAdmin || $user->hasRole('kasir'));
    @endphp

    <!-- ============================================================== -->
    <!-- JIKA ADMINISTRATOR: PUSAT KONTROL DENGAN PENCARIAN & FILTER   -->
    <!-- ============================================================== -->
    @if($isAdmin)
        <!-- Search Bar Cepat (Instant Filter Sesuai Standar Enterprise) -->
        <div class="px-3 pt-3 pb-1 shrink-0 bg-[#F8FAFC]">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                </div>
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Cari menu & fitur..." 
                       class="w-full pl-8 pr-7 py-1.5 text-xs bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-hidden focus:border-kp-blue-500 focus:ring-1 focus:ring-kp-blue-500 transition shadow-2xs font-medium">
                <button x-show="searchQuery.length > 0" 
                        @click="searchQuery = ''" 
                        type="button" 
                        class="absolute inset-y-0 right-0 pr-2 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Tab Segmented Filter Divisi (Mengurangi Kebingungan Navigasi) -->
            <div class="mt-2.5 flex items-center gap-1 overflow-x-auto pb-1 text-[11px] font-semibold sidebar-scroll no-scrollbar">
                <button type="button" 
                        @click="filterDivision = 'all'" 
                        :class="filterDivision === 'all' ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white text-slate-600 hover:text-slate-900 border border-slate-200/80'"
                        class="px-2 py-0.5 rounded-md transition cursor-pointer whitespace-nowrap shrink-0">
                    Semua
                </button>
                <button type="button" 
                        @click="filterDivision = 'iklan'; openDivision = 'iklan'" 
                        :class="filterDivision === 'iklan' ? 'bg-sky-600 text-white shadow-2xs' : 'bg-white text-slate-600 hover:text-slate-900 border border-slate-200/80'"
                        class="px-2 py-0.5 rounded-md transition cursor-pointer whitespace-nowrap shrink-0 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                    Iklan
                </button>
                <button type="button" 
                        @click="filterDivision = 'sirkulasi'; openDivision = 'sirkulasi'" 
                        :class="filterDivision === 'sirkulasi' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white text-slate-600 hover:text-slate-900 border border-slate-200/80'"
                        class="px-2 py-0.5 rounded-md transition cursor-pointer whitespace-nowrap shrink-0 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    Sirkulasi
                </button>
                <button type="button" 
                        @click="filterDivision = 'kasir'; openDivision = 'kasir'" 
                        :class="filterDivision === 'kasir' ? 'bg-amber-600 text-white shadow-2xs' : 'bg-white text-slate-600 hover:text-slate-900 border border-slate-200/80'"
                        class="px-2 py-0.5 rounded-md transition cursor-pointer whitespace-nowrap shrink-0 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    Kasir
                </button>
                <button type="button" 
                        @click="filterDivision = 'accounting'; openDivision = 'accounting'" 
                        :class="filterDivision === 'accounting' ? 'bg-purple-600 text-white shadow-2xs' : 'bg-white text-slate-600 hover:text-slate-900 border border-slate-200/80'"
                        class="px-2 py-0.5 rounded-md transition cursor-pointer whitespace-nowrap shrink-0 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                    Akuntansi
                </button>
                <button type="button" 
                        @click="filterDivision = 'keuangan'; openDivision = 'keuangan'" 
                        :class="filterDivision === 'keuangan' ? 'bg-indigo-600 text-white shadow-2xs' : 'bg-white text-slate-600 hover:text-slate-900 border border-slate-200/80'"
                        class="px-2 py-0.5 rounded-md transition cursor-pointer whitespace-nowrap shrink-0 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                    Keuangan
                </button>
            </div>
        </div>
    @endif

    <!-- 2. Navigasi Hirarkis Berbasis Role & Accordion Modern -->
    <nav class="flex-1 px-3 py-2 space-y-1.5 overflow-y-auto sidebar-scroll">

        <!-- ============================================== -->
        <!-- MENU UTAMA: DASHBOARD ADMINISTRATOR            -->
        <!-- ============================================== -->
        @if($isAdmin)
            <div x-show="filterDivision === 'all' && (!searchQuery || matchSearch('dashboard admin pusat kendali superadmin'))">
                @php $isAdminActive = request()->routeIs('admin.dashboard'); @endphp
                <a href="{{ route('admin.dashboard') }}" 
                   class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAdminActive ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/90 ring-1 ring-slate-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-6 h-6 rounded-lg {{ $isAdminActive ? 'bg-kp-blue-50 text-kp-blue-700' : 'bg-slate-100 text-slate-500 group-hover:text-slate-800' }} flex items-center justify-center transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <rect x="3.5" y="3.5" width="6.5" height="6.5" rx="2" />
                                <rect x="14" y="3.5" width="6.5" height="6.5" rx="2" />
                                <rect x="14" y="14" width="6.5" height="6.5" rx="2" />
                                <rect x="3.5" y="14" width="6.5" height="6.5" rx="2" />
                            </svg>
                        </span>
                        <span class="truncate">Pusat Kendali Eksekutif</span>
                    </div>
                    @if($isAdminActive)
                        <span class="w-1.5 h-1.5 rounded-full bg-kp-blue-600 shrink-0"></span>
                    @else
                        <span class="text-[9px] font-bold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">All</span>
                    @endif
                </a>
            </div>

            <!-- Header Pemisah Modul -->
            <div x-show="filterDivision === 'all' && !searchQuery" class="pt-2 pb-0.5 px-3 flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    5 Divisi Operasional
                </span>
                <span class="text-[9px] font-semibold text-slate-400">Pilih / Klik</span>
            </div>

            <!-- ============================================== -->
            <!-- 1. DIVISI IKLAN (Koran, Online, Priangan TV)   -->
            <!-- ============================================== -->
            @php
                $isIklanActive = request()->routeIs('iklan.*') 
                              || request()->routeIs('transaksikoran.*') 
                              || request()->routeIs('transaksionline.*') 
                              || request()->routeIs('transaksipriangan.*') 
                              || request()->routeIs('laporan.*') 
                              || request()->routeIs('kategori-media.*') 
                              || request()->routeIs('jenis-iklan.*')
                              || request()->routeIs('pelanggan.*')
                              || request()->routeIs('pembayaran-iklan.*');
            @endphp
            <div x-show="(filterDivision === 'all' || filterDivision === 'iklan') && (!searchQuery || matchSearch('iklan koran online priangan tv pelanggan sppi kwitansi'))"
                 class="rounded-xl border transition-all duration-200 {{ $isIklanActive ? 'border-sky-200/80 bg-white/70 shadow-2xs' : 'border-slate-200/60 bg-white/40' }}">
                
                <!-- Header Accordion Divisi Iklan -->
                <button type="button" 
                        @click="toggleDivision('iklan')" 
                        class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold rounded-xl transition cursor-pointer text-slate-800 hover:bg-slate-100/60">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-6 h-6 rounded-lg bg-sky-50 text-sky-700 border border-sky-200 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                            </svg>
                        </span>
                        <div class="text-left min-w-0 leading-tight">
                            <span class="truncate block">Divisi Iklan</span>
                            <span class="text-[10px] font-normal text-slate-500">Koran &bull; Online &bull; TV</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-sky-100 text-sky-800 border border-sky-200">
                            Lengkap
                        </span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" 
                             :class="(openDivision === 'iklan' || searchQuery.length > 0) ? 'rotate-180' : ''" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <!-- Isi Submenu Divisi Iklan -->
                <div x-cloak 
                     x-show="openDivision === 'iklan' || searchQuery.length > 0" 
                     x-collapse
                     class="px-2 pt-1 pb-2 space-y-1 border-t border-slate-100">
                    
                    <!-- Dashboard Iklan -->
                    <a href="{{ route('iklan.dashboard') }}" 
                       x-show="!searchQuery || matchSearch('dashboard overview ringkasan')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('iklan.dashboard') ? 'bg-sky-50 text-sky-800 font-bold border border-sky-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Overview Dashboard</span>
                        <span class="text-[9px] text-slate-400">KPI</span>
                    </a>

                    <!-- Subheader Group -->
                    <div class="pt-1.5 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">
                        Transaksi Media
                    </div>

                    <a href="{{ route('transaksikoran.index') }}" 
                       x-show="!searchQuery || matchSearch('koran cetak baris kolom display advertorial')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('transaksikoran.index') ? 'bg-sky-50 text-sky-800 font-bold border border-sky-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Iklan Koran Cetak</span>
                        <span class="text-[9px] font-bold text-sky-700 bg-sky-100/80 px-1 rounded">KRN</span>
                    </a>

                    <a href="{{ route('transaksionline.index') }}" 
                       x-show="!searchQuery || matchSearch('media online portal web berita banner')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('transaksionline.index') ? 'bg-sky-50 text-sky-800 font-bold border border-sky-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Media Online</span>
                        <span class="text-[9px] font-bold text-indigo-700 bg-indigo-100/80 px-1 rounded">ONL</span>
                    </a>

                    <a href="{{ route('transaksipriangan.index') }}" 
                       x-show="!searchQuery || matchSearch('priangan tv siaran televisi streaming video')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('transaksipriangan.index') ? 'bg-sky-50 text-sky-800 font-bold border border-sky-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Priangan TV</span>
                        <span class="text-[9px] font-bold text-purple-700 bg-purple-100/80 px-1 rounded">TV</span>
                    </a>

                    <!-- Subheader Group -->
                    <div class="pt-1.5 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">
                        Keuangan & CRM Iklan
                    </div>

                    <a href="{{ route('pembayaran-iklan.index') }}" 
                       x-show="!searchQuery || matchSearch('kwitansi kasir pembayaran pelunasan cbp')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('pembayaran-iklan.*') ? 'bg-sky-50 text-sky-800 font-bold border border-sky-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Kwitansi & Pembayaran</span>
                        <span class="text-[9px] font-bold text-teal-700 bg-teal-100/80 px-1 rounded">Kasir</span>
                    </a>

                    <a href="{{ route('laporan.index') }}" 
                       x-show="!searchQuery || matchSearch('laporan keuangan omzet piutang iklan')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('laporan.*') ? 'bg-sky-50 text-sky-800 font-bold border border-sky-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Laporan Keuangan Iklan</span>
                        <span class="text-[9px] font-bold text-amber-700 bg-amber-100/80 px-1 rounded">Rekap</span>
                    </a>

                    <a href="{{ route('pelanggan.index') }}" 
                       x-show="!searchQuery || matchSearch('pelanggan crm data klien kontak biro')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('pelanggan.*') ? 'bg-sky-50 text-sky-800 font-bold border border-sky-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Pelanggan & Agensi (CRM)</span>
                        <span class="text-[9px] font-bold text-blue-700 bg-blue-100/80 px-1 rounded">CRM</span>
                    </a>

                    <!-- Subheader Group -->
                    <div class="pt-1.5 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">
                        Master & Pengaturan
                    </div>

                    <a href="{{ route('kategori-media.index') }}" 
                       x-show="!searchQuery || matchSearch('kategori media saluran')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('kategori-media.*') ? 'bg-sky-50 text-sky-800 font-bold border border-sky-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Kategori Media</span>
                    </a>

                    <a href="{{ route('jenis-iklan.index') }}" 
                       x-show="!searchQuery || matchSearch('tipe jenis iklan paket tarif')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('jenis-iklan.*') ? 'bg-sky-50 text-sky-800 font-bold border border-sky-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Tipe & Jenis Iklan</span>
                    </a>

                    <a href="{{ route('iklan.pengaturan.index') }}" 
                       x-show="!searchQuery || matchSearch('pengaturan tarif ppn pajak cut-off deadline')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('iklan.pengaturan.*') ? 'bg-sky-50 text-sky-800 font-bold border border-sky-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Pengaturan Tarif & PPN</span>
                        <span class="text-[9px] text-slate-400">12%</span>
                    </a>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- 2. DIVISI SIRKULASI (Distribusi & Agen)        -->
            <!-- ============================================== -->
            @php $isSirkulasiActive = request()->routeIs('sirkulasi.*'); @endphp
            <div x-show="(filterDivision === 'all' || filterDivision === 'sirkulasi') && (!searchQuery || matchSearch('sirkulasi koran oplah agen jatah surat jalan retur bapr cetak rute'))"
                 class="rounded-xl border transition-all duration-200 {{ $isSirkulasiActive ? 'border-emerald-200/80 bg-white/70 shadow-2xs' : 'border-slate-200/60 bg-white/40' }}">
                
                <button type="button" 
                        @click="toggleDivision('sirkulasi')" 
                        class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold rounded-xl transition cursor-pointer text-slate-800 hover:bg-slate-100/60">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875v9.75c0 .621.504 1.125 1.125 1.125h1.125m9.75-8.25H9.75" />
                            </svg>
                        </span>
                        <div class="text-left min-w-0 leading-tight">
                            <span class="truncate block">Divisi Sirkulasi</span>
                            <span class="text-[10px] font-normal text-slate-500">Distribusi &bull; Agen &bull; Retur</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 border border-emerald-200">
                            16 Menu
                        </span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" 
                             :class="(openDivision === 'sirkulasi' || searchQuery.length > 0) ? 'rotate-180' : ''" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <div x-cloak 
                     x-show="openDivision === 'sirkulasi' || searchQuery.length > 0" 
                     x-collapse
                     class="px-2 pt-1 pb-2 space-y-1 border-t border-slate-100">
                    
                    <a href="{{ route('sirkulasi.dashboard') }}" 
                       x-show="!searchQuery || matchSearch('dashboard sirkulasi overview')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('sirkulasi.dashboard') ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Overview Dashboard</span>
                        <span class="text-[9px] text-slate-400">Oplah</span>
                    </a>

                    <div class="pt-1.5 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">
                        Logistik & Distribusi
                    </div>

                    <a href="{{ route('sirkulasi.logistik.surat-jalan') }}" 
                       x-show="!searchQuery || matchSearch('surat jalan logistik pengiriman sj')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('sirkulasi.logistik.surat-jalan') ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Surat Jalan Distribusi</span>
                        <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100/80 px-1 rounded">SJ</span>
                    </a>

                    <a href="{{ route('sirkulasi.logistik.retur') }}" 
                       x-show="!searchQuery || matchSearch('retur koran bapr sisa kemarin')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('sirkulasi.logistik.retur') ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Retur Koran (BAPR)</span>
                        <span class="text-[9px] font-bold text-rose-700 bg-rose-100/80 px-1 rounded">BAPR</span>
                    </a>

                    <a href="{{ route('sirkulasi.logistik.order-cetak') }}" 
                       x-show="!searchQuery || matchSearch('order cetak mesin percetakan tiras')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('sirkulasi.logistik.order-cetak') ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Order Cetak (Tiras)</span>
                    </a>

                    <div class="pt-1.5 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">
                        Operasional Jatah
                    </div>

                    <a href="{{ route('sirkulasi.jatah.harian') }}" 
                       x-show="!searchQuery || matchSearch('jatah harian alokasi koran per agen')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('sirkulasi.jatah.harian') ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Alokasi Jatah Harian</span>
                    </a>

                    <a href="{{ route('sirkulasi.jatah.copy') }}" 
                       x-show="!searchQuery || matchSearch('copy jatah duplikasi alokasi')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('sirkulasi.jatah.copy') ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Copy Jatah Cepat</span>
                    </a>

                    <a href="{{ route('sirkulasi.jatah.quick-edit') }}" 
                       x-show="!searchQuery || matchSearch('quick edit edit massal jatah')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('sirkulasi.jatah.quick-edit') ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Quick-Edit Jatah</span>
                    </a>

                    <div class="pt-1.5 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">
                        Master Data & Laporan
                    </div>

                    <a href="{{ route('sirkulasi.master.agen') }}" 
                       x-show="!searchQuery || matchSearch('master agen mitra drop point')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('sirkulasi.master.agen') ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Master Data Agen</span>
                    </a>

                    <a href="{{ route('sirkulasi.master.rute') }}" 
                       x-show="!searchQuery || matchSearch('rute armada pengiriman driver')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('sirkulasi.master.rute') ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Rute Distribusi</span>
                    </a>

                    <a href="{{ route('sirkulasi.master.kalender') }}" 
                       x-show="!searchQuery || matchSearch('kalender terbit libur edisi')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('sirkulasi.master.kalender') ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Kalender Terbit</span>
                    </a>

                    <a href="{{ route('sirkulasi.master.tarif') }}" 
                       x-show="!searchQuery || matchSearch('tarif koran harga agen')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('sirkulasi.master.tarif') ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Tarif Koran Agen</span>
                    </a>

                    <a href="{{ route('sirkulasi.master.pelanggan') }}" 
                       x-show="!searchQuery || matchSearch('langganan home delivery kantor perorangan')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('sirkulasi.master.pelanggan') ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Pelanggan Langganan</span>
                    </a>

                    <a href="{{ route('sirkulasi.laporan.closing') }}" 
                       x-show="!searchQuery || matchSearch('closing tutup data bulanan lock')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('sirkulasi.laporan.closing') ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Closing / Tutup Data</span>
                        <span class="text-[9px] font-bold text-amber-700 bg-amber-100/80 px-1 rounded">Lock</span>
                    </a>

                    <a href="{{ route('sirkulasi.laporan.penjualan') }}" 
                       x-show="!searchQuery || matchSearch('laporan penjualan nett sales rekap')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('sirkulasi.laporan.penjualan') ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Rekap Penjualan</span>
                    </a>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- 3. DIVISI KASIR (Penerimaan, Kas & LHK)        -->
            <!-- ============================================== -->
            @php $isKasirActive = request()->routeIs('kasir.*'); @endphp
            <div x-show="(filterDivision === 'all' || filterDivision === 'kasir') && (!searchQuery || matchSearch('kasir penerimaan setoran kas buku kas bank kwitansi lhk'))"
                 class="rounded-xl border transition-all duration-200 {{ $isKasirActive ? 'border-amber-200/80 bg-white/70 shadow-2xs' : 'border-slate-200/60 bg-white/40' }}">
                
                <button type="button" 
                        @click="toggleDivision('kasir')" 
                        class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold rounded-xl transition cursor-pointer text-slate-800 hover:bg-slate-100/60">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-6 h-6 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm6 3.75a3 3 0 11-6 0 3 3 0 016 0zM6 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        <div class="text-left min-w-0 leading-tight">
                            <span class="truncate block">Divisi Kasir</span>
                            <span class="text-[10px] font-normal text-slate-500">Penerimaan &bull; Buku Kas &bull; LHK</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 border border-amber-200">
                            10 Menu
                        </span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" 
                             :class="(openDivision === 'kasir' || searchQuery.length > 0) ? 'rotate-180' : ''" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <div x-cloak 
                     x-show="openDivision === 'kasir' || searchQuery.length > 0" 
                     x-collapse
                     class="px-2 pt-1 pb-2 space-y-1 border-t border-slate-100">
                    
                    <a href="{{ route('kasir.dashboard') }}" 
                       x-show="!searchQuery || matchSearch('dashboard kasir overview')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('kasir.dashboard') ? 'bg-amber-50 text-amber-800 font-bold border border-amber-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Overview Dashboard</span>
                        <span class="text-[9px] text-slate-400">Kas</span>
                    </a>

                    <div class="pt-1.5 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">
                        Penerimaan Kas (Inflow)
                    </div>

                    <a href="{{ route('kasir.penerimaan.iklan') }}" 
                       x-show="!searchQuery || matchSearch('setoran iklan faktur piutang')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('kasir.penerimaan.iklan') ? 'bg-amber-50 text-amber-800 font-bold border border-amber-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Setoran Iklan</span>
                        <span class="text-[9px] font-bold text-sky-700 bg-sky-100/80 px-1 rounded">Faktur</span>
                    </a>

                    <a href="{{ route('kasir.penerimaan.sirkulasi') }}" 
                       x-show="!searchQuery || matchSearch('setoran sirkulasi agen koran')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('kasir.penerimaan.sirkulasi') ? 'bg-amber-50 text-amber-800 font-bold border border-amber-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Setoran Sirkulasi Agen</span>
                        <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100/80 px-1 rounded">Koran</span>
                    </a>

                    <a href="{{ route('kasir.penerimaan.langganan') }}" 
                       x-show="!searchQuery || matchSearch('langganan instansi dinas pemda')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('kasir.penerimaan.langganan') ? 'bg-amber-50 text-amber-800 font-bold border border-amber-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Langganan Dinas/Instansi</span>
                        <span class="text-[9px] font-bold text-indigo-700 bg-indigo-100/80 px-1 rounded">Dinas</span>
                    </a>

                    <a href="{{ route('kasir.penerimaan.tunai') }}" 
                       x-show="!searchQuery || matchSearch('penjualan langsung eceran tunai')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('kasir.penerimaan.tunai') ? 'bg-amber-50 text-amber-800 font-bold border border-amber-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Penjualan Tunai Langsung</span>
                    </a>

                    <div class="pt-1.5 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">
                        Pengeluaran & Buku Kas
                    </div>

                    <a href="{{ route('kasir.pengeluaran.realisasi-bp') }}" 
                       x-show="!searchQuery || matchSearch('realisasi bp bkk kas keluar operasional')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('kasir.pengeluaran.realisasi-bp') ? 'bg-amber-50 text-amber-800 font-bold border border-amber-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Realisasi BP Kasir</span>
                        <span class="text-[9px] font-bold text-rose-700 bg-rose-100/80 px-1 rounded">BKK</span>
                    </a>

                    <a href="{{ route('kasir.setoran-bank') }}" 
                       x-show="!searchQuery || matchSearch('setoran bank kas ke rekening giro')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('kasir.setoran-bank') ? 'bg-amber-50 text-amber-800 font-bold border border-amber-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Setoran Kas ke Bank</span>
                        <span class="text-[9px] font-bold text-blue-700 bg-blue-100/80 px-1 rounded">Bank</span>
                    </a>

                    <a href="{{ route('kasir.buku-kas') }}" 
                       x-show="!searchQuery || matchSearch('buku kas tutup kasir shift harian')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('kasir.buku-kas') ? 'bg-amber-50 text-amber-800 font-bold border border-amber-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Buku Kas & Tutup Kasir</span>
                        <span class="text-[9px] font-bold text-amber-700 bg-amber-100/80 px-1 rounded">Shift</span>
                    </a>

                    <a href="{{ route('kasir.riwayat-cetak') }}" 
                       x-show="!searchQuery || matchSearch('riwayat cetak kwitansi tanda bukti')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('kasir.riwayat-cetak') ? 'bg-amber-50 text-amber-800 font-bold border border-amber-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Riwayat & Bukti Cetak</span>
                    </a>

                    <a href="{{ route('kasir.laporan-harian') }}" 
                       x-show="!searchQuery || matchSearch('laporan harian kasir lhk rekap kas')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('kasir.laporan-harian') ? 'bg-amber-50 text-amber-800 font-bold border border-amber-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Laporan Harian Kasir (LHK)</span>
                        <span class="text-[9px] font-bold text-teal-700 bg-teal-100/80 px-1 rounded">LHK</span>
                    </a>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- 4. DIVISI ACCOUNTING (Jurnal, Ledger & LapKeu) -->
            <!-- ============================================== -->
            @php $isAccountingActive = request()->routeIs('accounting.*'); @endphp
            <div x-show="(filterDivision === 'all' || filterDivision === 'accounting') && (!searchQuery || matchSearch('accounting akuntansi jurnal buku besar coa kartu piutang neraca laba rugi'))"
                 class="rounded-xl border transition-all duration-200 {{ $isAccountingActive ? 'border-purple-200/80 bg-white/70 shadow-2xs' : 'border-slate-200/60 bg-white/40' }}">
                
                <button type="button" 
                        @click="toggleDivision('accounting')" 
                        class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold rounded-xl transition cursor-pointer text-slate-800 hover:bg-slate-100/60">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-6 h-6 rounded-lg bg-purple-50 text-purple-700 border border-purple-200 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                        </span>
                        <div class="text-left min-w-0 leading-tight">
                            <span class="truncate block">Divisi Akuntansi</span>
                            <span class="text-[10px] font-normal text-slate-500">Jurnal &bull; Buku Besar &bull; LapKeu</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-purple-100 text-purple-800 border border-purple-200">
                            12 Menu
                        </span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" 
                             :class="(openDivision === 'accounting' || searchQuery.length > 0) ? 'rotate-180' : ''" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <div x-cloak 
                     x-show="openDivision === 'accounting' || searchQuery.length > 0" 
                     x-collapse
                     class="px-2 pt-1 pb-2 space-y-1 border-t border-slate-100">
                    
                    <a href="{{ route('accounting.dashboard') }}" 
                       x-show="!searchQuery || matchSearch('dashboard accounting akuntansi overview')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('accounting.dashboard') ? 'bg-purple-50 text-purple-800 font-bold border border-purple-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Overview Dashboard</span>
                        <span class="text-[9px] text-slate-400">GL</span>
                    </a>

                    <div class="pt-1.5 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">
                        Tarik & Validasi Data
                    </div>

                    <a href="{{ route('accounting.tarik.iklan') }}" 
                       x-show="!searchQuery || matchSearch('tarik iklan transaksi piutang')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('accounting.tarik.iklan*') ? 'bg-purple-50 text-purple-800 font-bold border border-purple-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Tarik Transaksi Iklan</span>
                        <span class="text-[9px] font-bold text-sky-700 bg-sky-100/80 px-1 rounded">Iklan</span>
                    </a>

                    <a href="{{ route('accounting.tarik.sirkulasi') }}" 
                       x-show="!searchQuery || matchSearch('tarik sirkulasi koran penjualan')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('accounting.tarik.sirkulasi*') ? 'bg-purple-50 text-purple-800 font-bold border border-purple-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Tarik Data Sirkulasi</span>
                        <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100/80 px-1 rounded">Koran</span>
                    </a>

                    <a href="{{ route('accounting.tarik.kasir') }}" 
                       x-show="!searchQuery || matchSearch('tarik kasir setoran mutasi lhk')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('accounting.tarik.kasir*') ? 'bg-purple-50 text-purple-800 font-bold border border-purple-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Tarik Mutasi Kasir</span>
                        <span class="text-[9px] font-bold text-amber-700 bg-amber-100/80 px-1 rounded">Kasir</span>
                    </a>

                    <div class="pt-1.5 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">
                        Buku Besar & COA
                    </div>

                    <a href="{{ route('accounting.gl.index') }}" 
                       x-show="!searchQuery || matchSearch('buku besar gl general ledger jurnal')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('accounting.gl.index*') ? 'bg-purple-50 text-purple-800 font-bold border border-purple-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Buku Besar (GL)</span>
                        <span class="text-[9px] font-bold text-purple-700 bg-purple-100/80 px-1 rounded">GL</span>
                    </a>

                    <a href="{{ route('accounting.gl.coa') }}" 
                       x-show="!searchQuery || matchSearch('coa bagan akun rekening chart of accounts')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('accounting.gl.coa*') ? 'bg-purple-50 text-purple-800 font-bold border border-purple-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Bagan Akun (COA)</span>
                        <span class="text-[9px] font-bold text-slate-700 bg-slate-200 px-1 rounded">COA</span>
                    </a>

                    <div class="pt-1.5 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">
                        Kartu Piutang Sub-Ledger
                    </div>

                    <a href="{{ route('accounting.kartu.iklan') }}" 
                       x-show="!searchQuery || matchSearch('kartu piutang iklan saldo pemasang')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('accounting.kartu.iklan*') ? 'bg-purple-50 text-purple-800 font-bold border border-purple-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Kartu Piutang Iklan</span>
                    </a>

                    <a href="{{ route('accounting.kartu.sirkulasi') }}" 
                       x-show="!searchQuery || matchSearch('kartu piutang agen sirkulasi')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('accounting.kartu.sirkulasi*') ? 'bg-purple-50 text-purple-800 font-bold border border-purple-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Kartu Piutang Sirkulasi</span>
                    </a>

                    <a href="{{ route('accounting.kartu.retur') }}" 
                       x-show="!searchQuery || matchSearch('klaim retur koran bapr akuntansi')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('accounting.kartu.retur*') ? 'bg-purple-50 text-purple-800 font-bold border border-purple-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Validasi Retur Koran</span>
                    </a>

                    <div class="pt-1.5 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">
                        Laporan Finansial
                    </div>

                    <a href="{{ route('accounting.laporan.laba-rugi') }}" 
                       x-show="!searchQuery || matchSearch('laba rugi income statement profit loss')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('accounting.laporan.laba-rugi*') ? 'bg-purple-50 text-purple-800 font-bold border border-purple-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Laporan Laba / Rugi</span>
                    </a>

                    <a href="{{ route('accounting.laporan.neraca') }}" 
                       x-show="!searchQuery || matchSearch('neraca balance sheet aktiva pasiva')"
                       class="flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('accounting.laporan.neraca*') ? 'bg-purple-50 text-purple-800 font-bold border border-purple-200' : 'text-slate-600 hover:text-slate-900 hover:bg-white font-medium' }}">
                        <span class="truncate">Laporan Neraca</span>
                    </a>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- 5. DIVISI KEUANGAN (Arus Kas & Piutang)        -->
            <!-- ============================================== -->
            @php $isKeuanganActive = request()->routeIs('keuangan.*'); @endphp
            <div x-show="(filterDivision === 'all' || filterDivision === 'keuangan') && (!searchQuery || matchSearch('keuangan kas piutang saldo arus kas'))"
                 class="rounded-xl border transition-all duration-200 {{ $isKeuanganActive ? 'border-indigo-200/80 bg-white/70 shadow-2xs' : 'border-slate-200/60 bg-white/40' }}">
                
                <a href="{{ route('keuangan.dashboard') }}" 
                   class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold rounded-xl transition cursor-pointer {{ $isKeuanganActive ? 'bg-indigo-50 text-indigo-900' : 'text-slate-800 hover:bg-slate-100/60' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <rect x="3" y="4.5" width="18" height="15" rx="3.5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 13.5l3.5-3.5 3 3 3.5-4" />
                            </svg>
                        </span>
                        <div class="text-left min-w-0 leading-tight">
                            <span class="truncate block">Divisi Keuangan</span>
                            <span class="text-[10px] font-normal text-slate-500">Arus Kas &bull; Likuiditas</span>
                        </div>
                    </div>
                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-indigo-100 text-indigo-800 border border-indigo-200 shrink-0">
                        KPI
                    </span>
                </a>
            </div>

        <!-- ============================================================== -->
        <!-- JIKA BUKAN ADMINISTRATOR: TAMPILAN TERFOKUS SESUAI ROLE MASING2-->
        <!-- ============================================================== -->
        @else

            <!-- ROLE: IKLAN -->
            @if($user->hasRole('iklan'))
                <a href="{{ route('iklan.dashboard') }}" 
                   class="flex items-center justify-between px-3 py-2 text-xs rounded-xl font-bold transition {{ request()->routeIs('iklan.dashboard') ? 'bg-white text-slate-900 shadow-xs border border-slate-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40' }}">
                    <span class="truncate">Dashboard Iklan</span>
                </a>

                <div class="pt-2 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Transaksi Media</div>
                <div class="space-y-1">
                    <a href="{{ route('transaksikoran.create') }}" class="flex items-center justify-between px-3 py-1.5 text-xs rounded-lg bg-emerald-50 text-emerald-900 border border-emerald-200 font-bold">
                        <span>+ Input Order Koran</span>
                        <span class="text-[9px] bg-emerald-200 px-1 rounded">Baru</span>
                    </a>
                    <a href="{{ route('transaksikoran.index') }}" class="flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('transaksikoran.*') && !request()->routeIs('transaksikoran.create') ? 'bg-white font-bold text-slate-900 shadow-xs border border-slate-200' : 'text-slate-600 hover:bg-white font-medium' }}">
                        <span>Koran Cetak</span>
                        <span class="text-[9px] font-bold text-sky-700 bg-sky-100 px-1 rounded">KRN</span>
                    </a>
                    <a href="{{ route('transaksionline.index') }}" class="flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('transaksionline.*') ? 'bg-white font-bold text-slate-900 shadow-xs border border-slate-200' : 'text-slate-600 hover:bg-white font-medium' }}">
                        <span>Media Online</span>
                        <span class="text-[9px] font-bold text-indigo-700 bg-indigo-100 px-1 rounded">ONL</span>
                    </a>
                    <a href="{{ route('transaksipriangan.index') }}" class="flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('transaksipriangan.*') ? 'bg-white font-bold text-slate-900 shadow-xs border border-slate-200' : 'text-slate-600 hover:bg-white font-medium' }}">
                        <span>Priangan TV</span>
                        <span class="text-[9px] font-bold text-purple-700 bg-purple-100 px-1 rounded">TV</span>
                    </a>
                </div>

                <div class="pt-2 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Keuangan & CRM</div>
                <div class="space-y-1">
                    <a href="{{ route('pembayaran-iklan.index') }}" class="flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('pembayaran-iklan.*') ? 'bg-white font-bold text-slate-900 shadow-xs border border-slate-200' : 'text-slate-600 hover:bg-white font-medium' }}">
                        <span>Kwitansi Kasir</span>
                        <span class="text-[9px] font-bold text-teal-700 bg-teal-100 px-1 rounded">Kasir</span>
                    </a>
                    <a href="{{ route('laporan.index') }}" class="flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('laporan.*') ? 'bg-white font-bold text-slate-900 shadow-xs border border-slate-200' : 'text-slate-600 hover:bg-white font-medium' }}">
                        <span>Laporan Iklan</span>
                        <span class="text-[9px] font-bold text-amber-700 bg-amber-100 px-1 rounded">Rekap</span>
                    </a>
                    <a href="{{ route('pelanggan.index') }}" class="flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('pelanggan.*') ? 'bg-white font-bold text-slate-900 shadow-xs border border-slate-200' : 'text-slate-600 hover:bg-white font-medium' }}">
                        <span>Pelanggan (CRM)</span>
                    </a>
                    <a href="{{ route('iklan.pengaturan.index') }}" class="flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition {{ request()->routeIs('iklan.pengaturan.*') ? 'bg-white font-bold text-slate-900 shadow-xs border border-slate-200' : 'text-slate-600 hover:bg-white font-medium' }}">
                        <span>Pengaturan Tarif</span>
                    </a>
                </div>
            @endif

            <!-- ROLE: SIRKULASI -->
            @if($user->hasRole('sirkulasi'))
                <a href="{{ route('sirkulasi.dashboard') }}" class="flex items-center justify-between px-3 py-2 text-xs rounded-xl font-bold transition {{ request()->routeIs('sirkulasi.dashboard') ? 'bg-white text-slate-900 shadow-xs border border-slate-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40' }}">
                    <span class="truncate">Dashboard Sirkulasi</span>
                </a>
                <div class="pt-2 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Logistik & Distribusi</div>
                <div class="space-y-1">
                    <a href="{{ route('sirkulasi.logistik.surat-jalan') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('sirkulasi.logistik.surat-jalan') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Surat Jalan Distribusi</a>
                    <a href="{{ route('sirkulasi.logistik.retur') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('sirkulasi.logistik.retur') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Retur Koran (BAPR)</a>
                    <a href="{{ route('sirkulasi.logistik.order-cetak') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('sirkulasi.logistik.order-cetak') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Order Cetak (Tiras)</a>
                </div>
                <div class="pt-2 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jatah & Master</div>
                <div class="space-y-1">
                    <a href="{{ route('sirkulasi.jatah.harian') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('sirkulasi.jatah.harian') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Jatah Harian</a>
                    <a href="{{ route('sirkulasi.master.agen') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('sirkulasi.master.agen') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Master Agen</a>
                    <a href="{{ route('sirkulasi.master.rute') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('sirkulasi.master.rute') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Rute Distribusi</a>
                    <a href="{{ route('sirkulasi.laporan.closing') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('sirkulasi.laporan.closing') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Closing Bulanan</a>
                </div>
            @endif

            <!-- ROLE: KASIR -->
            @if($user->hasRole('kasir'))
                <a href="{{ route('kasir.dashboard') }}" class="flex items-center justify-between px-3 py-2 text-xs rounded-xl font-bold transition {{ request()->routeIs('kasir.dashboard') ? 'bg-white text-slate-900 shadow-xs border border-slate-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40' }}">
                    <span class="truncate">Dashboard Kasir</span>
                </a>
                <div class="pt-2 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Penerimaan Kas</div>
                <div class="space-y-1">
                    <a href="{{ route('kasir.penerimaan.iklan') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('kasir.penerimaan.iklan') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Setoran Iklan</a>
                    <a href="{{ route('kasir.penerimaan.sirkulasi') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('kasir.penerimaan.sirkulasi') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Setoran Sirkulasi</a>
                    <a href="{{ route('kasir.penerimaan.langganan') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('kasir.penerimaan.langganan') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Langganan Dinas</a>
                    <a href="{{ route('kasir.penerimaan.tunai') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('kasir.penerimaan.tunai') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Penjualan Tunai</a>
                </div>
                <div class="pt-2 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Buku Kas & Rekap</div>
                <div class="space-y-1">
                    <a href="{{ route('kasir.pengeluaran.realisasi-bp') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('kasir.pengeluaran.realisasi-bp') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Realisasi BP (BKK)</a>
                    <a href="{{ route('kasir.setoran-bank') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('kasir.setoran-bank') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Setoran Bank</a>
                    <a href="{{ route('kasir.buku-kas') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('kasir.buku-kas') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Buku Kas & Tutup</a>
                    <a href="{{ route('kasir.laporan-harian') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('kasir.laporan-harian') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Laporan Harian (LHK)</a>
                </div>
            @endif

            <!-- ROLE: ACCOUNTING -->
            @if($user->hasRole('accounting'))
                <a href="{{ route('accounting.dashboard') }}" class="flex items-center justify-between px-3 py-2 text-xs rounded-xl font-bold transition {{ request()->routeIs('accounting.dashboard') ? 'bg-white text-slate-900 shadow-xs border border-slate-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40' }}">
                    <span class="truncate">Dashboard Akuntansi</span>
                </a>
                <div class="pt-2 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tarik Data & GL</div>
                <div class="space-y-1">
                    <a href="{{ route('accounting.tarik.iklan') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('accounting.tarik.iklan*') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Tarik Iklan</a>
                    <a href="{{ route('accounting.tarik.sirkulasi') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('accounting.tarik.sirkulasi*') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Tarik Sirkulasi</a>
                    <a href="{{ route('accounting.tarik.kasir') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('accounting.tarik.kasir*') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Tarik Kasir</a>
                    <a href="{{ route('accounting.gl.index') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('accounting.gl.index*') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Buku Besar (GL)</a>
                    <a href="{{ route('accounting.gl.coa') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('accounting.gl.coa*') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Bagan Akun (COA)</a>
                </div>
                <div class="pt-2 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Laporan Keuangan</div>
                <div class="space-y-1">
                    <a href="{{ route('accounting.laporan.laba-rugi') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('accounting.laporan.laba-rugi*') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Laba / Rugi</a>
                    <a href="{{ route('accounting.laporan.neraca') }}" class="block px-3 py-1.5 text-xs rounded-lg {{ request()->routeIs('accounting.laporan.neraca*') ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:bg-white' }}">Neraca</a>
                </div>
            @endif

            <!-- ROLE: KEUANGAN -->
            @if($user->hasRole('keuangan'))
                <a href="{{ route('keuangan.dashboard') }}" class="flex items-center justify-between px-3 py-2 text-xs rounded-xl font-bold transition {{ request()->routeIs('keuangan.dashboard') ? 'bg-white text-slate-900 shadow-xs border border-slate-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40' }}">
                    <span class="truncate">Dashboard Keuangan</span>
                </a>
            @endif

        @endif

    </nav>

    <!-- 3. Footer Sidebar: User Profile Ringkas & Logout Cepat -->
    <div class="p-3 border-t border-slate-200/80 bg-white/80 shrink-0">
        <div class="flex items-center justify-between px-2.5 py-2 rounded-xl bg-slate-50 border border-slate-200/70 shadow-2xs">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-7 h-7 rounded-lg bg-kp-blue-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-xs">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="min-w-0 leading-tight">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-slate-500 font-semibold truncate">{{ Auth::user()->role_label ?? 'Administrator' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" 
                        title="Keluar dari Sistem" 
                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

</aside>
