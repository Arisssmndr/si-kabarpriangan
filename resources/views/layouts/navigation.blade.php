<!-- Sidebar Navigasi Kabar Priangan (Modern Standard Collapsible - 60:30:10 Design) -->
<aside class="w-full bg-[#F8FAFC] text-slate-800 flex flex-col shrink-0 h-full border-r border-slate-200 select-none shadow-[1px_0_4px_rgba(0,0,0,0.02)] transition-all duration-200 overflow-x-hidden">

    <!-- 1. Header Brand & Identitas (Collapsible Ready) -->
    <div class="px-3.5 py-3 border-b border-slate-200/80 flex items-center justify-between bg-white/80 backdrop-blur-xs min-h-[58px]"
         :class="sidebarCollapsed ? 'px-2 justify-center' : 'px-3.5 justify-between'">
        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
            <img src="{{ asset('images/kabarpriangan.png') }}" 
                 alt="Logo Kabar Priangan" 
                 class="h-8 w-8 object-contain rounded-full bg-white p-0.5 shadow-xs border border-slate-200 shrink-0 cursor-pointer"
                 :title="sidebarCollapsed ? 'Perluas Sidebar' : ''"
                 @click="if (sidebarCollapsed) toggleSidebar()"
                 onerror="this.src='{{ asset('kabarpriangan.png') }}'">
            <div x-show="!sidebarCollapsed" class="leading-tight min-w-0">
                <h1 class="font-extrabold text-xs tracking-tight text-slate-900 truncate">
                    KABAR PRIANGAN
                </h1>
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider truncate mt-0.5">
                    {{ Auth::user()->role_label ?? 'Sistem Terpadu' }}
                </p>
            </div>
        </div>
    </div>

    <!-- 2. Navigasi Hirarkis Berbasis Role & Accordion -->
    <nav class="flex-1 py-3 space-y-1 overflow-y-auto sidebar-scroll" :class="sidebarCollapsed ? 'px-2' : 'px-3'">

        @php
            $user = Auth::user();
            $isAdmin = $user && $user->isAdmin();
            $isIklan = $user && ($isAdmin || $user->hasRole('iklan'));
            $isKeuangan = $user && ($isAdmin || $user->hasRole('keuangan') || $user->hasRole('admin_keuangan') || $user->hasRole('petugas_inkaso') || $user->hasRole('kepala_divisi'));
            $isAccounting = $user && ($isAdmin || $user->hasRole('accounting'));
            $isSirkulasi = $user && ($isAdmin || $user->hasRole('sirkulasi'));
            $isKasir = $user && ($isAdmin || $user->hasRole('kasir'));
        @endphp

        <!-- ============================================== -->
        <!-- JIKA ADMINISTRATOR: PUSAT KENDALI EKSEKUTIF    -->
        <!-- ============================================== -->
        @if($isAdmin)
            
            <!-- Dashboard Eksekutif Admin -->
            <div>
                @php $isAdminActive = request()->routeIs('admin.dashboard'); @endphp
                <a href="{{ route('admin.dashboard') }}" 
                   :title="sidebarCollapsed ? 'Dashboard Admin' : ''"
                   class="group flex items-center px-2.5 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAdminActive ? 'bg-kp-blue-600 text-white font-bold shadow-sm shadow-kp-blue-600/25' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-200/50 font-semibold' }}"
                   :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-2.5'">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 transition-colors {{ $isAdminActive ? 'bg-white/20 text-white' : 'text-slate-500 group-hover:text-slate-800' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="3.5" y="3.5" width="6.5" height="6.5" rx="2" />
                                <rect x="14" y="3.5" width="6.5" height="6.5" rx="2" />
                                <rect x="14" y="14" width="6.5" height="6.5" rx="2" />
                                <rect x="3.5" y="14" width="6.5" height="6.5" rx="2" />
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Dashboard Admin</span>
                    </div>
                </a>
            </div>

            <!-- Divider Label Modul -->
            <div x-show="!sidebarCollapsed" class="pt-3 pb-1 px-2.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Modul Divisi
                </span>
            </div>
            <div x-show="sidebarCollapsed" class="my-2 border-t border-slate-200 mx-1"></div>

            <!-- 1. Divisi Iklan -->
            @php
                $isIklanOpen = request()->routeIs('iklan.*') 
                            || request()->routeIs('transaksikoran.*') 
                            || request()->routeIs('transaksionline.*') 
                            || request()->routeIs('transaksipriangan.*') 
                            || request()->routeIs('laporan.*') 
                            || request()->routeIs('laporankoran.*') 
                            || request()->routeIs('laporanonline.*') 
                            || request()->routeIs('laporanpriangan.*') 
                            || request()->routeIs('kategori-media.*') 
                            || request()->routeIs('jenis-iklan.*')
                            || request()->routeIs('pelanggan.*')
                            || request()->routeIs('sales-ae.*')
                            || request()->routeIs('kop-kota.*')
                            || request()->routeIs('kop-iklan.*')
                            || request()->routeIs('pembayaran-iklan.*');
            @endphp
            <div x-data="{ open: {{ $isIklanOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebar(), open = true) : (open = !open)" 
                        :title="sidebarCollapsed ? 'Divisi Iklan' : ''"
                        class="group w-full flex items-center px-2.5 py-2 text-xs font-semibold rounded-xl transition-all duration-150 cursor-pointer {{ $isIklanOpen ? 'text-kp-blue-900 bg-kp-blue-50/80 border border-kp-blue-200/60 font-bold' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-200/40 border border-transparent' }}"
                        :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-2.5'">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 transition-colors {{ $isIklanOpen ? 'bg-kp-blue-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-500 group-hover:text-slate-800' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.388-.046-.771-.115-1.15-.205m6.75-7.397V3.375c0-.621-.504-1.125-1.125-1.125H4.875c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-6h7.5m-7.5-3h7.5m-7.5-3h7.5" />
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Divisi Iklan</span>
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex items-center gap-1.5 shrink-0">
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $isIklanOpen ? 'text-kp-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <!-- Submenu Flat & Clean -->
                <div x-cloak 
                     x-show="open && !sidebarCollapsed" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-3 pr-1 pt-1 pb-1 space-y-0.5">
                    
                    @php $isSub = request()->routeIs('iklan.dashboard'); @endphp
                    <a href="{{ route('iklan.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Overview Iklan</span>
                    </a>

                    <!-- Input Cepat Chips -->
                    <div class="pt-2 pb-1 px-1">
                        <div class="text-[9px] font-bold tracking-wider text-slate-400 uppercase mb-1">Input Cepat</div>
                        <div class="grid grid-cols-3 gap-1">
                            <a href="{{ route('transaksikoran.create') }}" class="px-1.5 py-1 text-center text-[10px] font-semibold rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-100 transition border border-sky-200" title="Input Transaksi Koran">+ Koran</a>
                            <a href="{{ route('transaksionline.create') }}" class="px-1.5 py-1 text-center text-[10px] font-semibold rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition border border-emerald-200" title="Input Media Online">+ Online</a>
                            <a href="{{ route('transaksipriangan.create') }}" class="px-1.5 py-1 text-center text-[10px] font-semibold rounded-lg bg-purple-50 text-purple-700 hover:bg-purple-100 transition border border-purple-200" title="Input Priangan TV">+ TV</a>
                        </div>
                    </div>

                    <!-- 1. FUNGSI: ORDER IKLAN -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">Order &amp; Pracetak</div>
                    
                    @php $isSub = request()->routeIs('transaksikoran.*') && !request()->routeIs('transaksikoran.create'); @endphp
                    <a href="{{ route('transaksikoran.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Daftar Pemuatan Koran</span>
                    </a>

                    @php $isSub = request()->routeIs('iklan.rekap-pracetak.*'); @endphp
                    <a href="{{ route('iklan.rekap-pracetak.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Rekap Dummy Pracetak</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' }}">Layout</span>
                    </a>

                    @php $isSub = request()->routeIs('transaksionline.*') && !request()->routeIs('transaksionline.create'); @endphp
                    <a href="{{ route('transaksionline.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Iklan Online</span>
                    </a>

                    @php $isSub = request()->routeIs('transaksipriangan.*') && !request()->routeIs('transaksipriangan.create'); @endphp
                    <a href="{{ route('transaksipriangan.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Iklan Priangan TV</span>
                    </a>

                    @php $isSub = request()->routeIs('pembayaran-iklan.*'); @endphp
                    <a href="{{ route('pembayaran-iklan.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Kwitansi Kasir</span>
                    </a>

                    <!-- 2. FUNGSI: MASTER DATA -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">Master Periklanan</div>

                    @php $isSub = request()->routeIs('pelanggan.*'); @endphp
                    <a href="{{ route('pelanggan.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Data Pemasang</span>
                    </a>

                    @php $isSub = request()->routeIs('sales-ae.*'); @endphp
                    <a href="{{ route('sales-ae.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Data Sales / AE</span>
                    </a>

                    @php $isSub = request()->routeIs('kop-kota.*'); @endphp
                    <a href="{{ route('kop-kota.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Halaman (Kop Kota)</span>
                    </a>

                    @php $isSub = request()->routeIs('kop-iklan.*'); @endphp
                    <a href="{{ route('kop-iklan.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Rubrik Baris (Kop Iklan)</span>
                    </a>

                    <!-- 3. FUNGSI: LAPORAN STANDAR PERS -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">Laporan &amp; Payroll Pers</div>

                    @php $isSub = request()->routeIs('laporan.index'); @endphp
                    <a href="{{ route('laporan.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Rekap Omzet 3 Media</span>
                    </a>

                    @php $isSub = request()->routeIs('laporan.rekap-sales'); @endphp
                    <a href="{{ route('laporan.rekap-sales') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Rekap Sales &amp; AE</span>
                    </a>

                    @php $isSub = request()->routeIs('laporan.insentif-ae'); @endphp
                    <a href="{{ route('laporan.insentif-ae') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Slip Insentif AE</span>
                    </a>

                    @php $isSub = request()->routeIs('laporan.aging-piutang'); @endphp
                    <a href="{{ route('laporan.aging-piutang') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Umur Piutang (Aging)</span>
                    </a>

                    @php $isSub = request()->routeIs('laporan.pembatalan'); @endphp
                    <a href="{{ route('laporan.pembatalan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Audit Pembatalan</span>
                    </a>

                    <!-- 4. FUNGSI: TARIF & PENGATURAN -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">Tarif &amp; Pengaturan</div>

                    @php $isSub = request()->routeIs('iklan.pengaturan.*'); @endphp
                    <a href="{{ route('iklan.pengaturan.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Tarif &amp; PPN 12%</span>
                    </a>

                    @php $isSub = request()->routeIs('jenis-iklan.*'); @endphp
                    <a href="{{ route('jenis-iklan.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Jenis &amp; Ukuran Iklan</span>
                    </a>

                    @php $isSub = request()->routeIs('kategori-media.*'); @endphp
                    <a href="{{ route('kategori-media.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Kategori Media</span>
                    </a>
                </div>
            </div>

            <!-- 2. Divisi Keuangan -->
            @php
                $isKeuanganOpen = request()->routeIs('keuangan.*');
            @endphp
            <div x-data="{ open: {{ $isKeuanganOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebar(), open = true) : (open = !open)" 
                        :title="sidebarCollapsed ? 'Divisi Keuangan' : ''"
                        class="group w-full flex items-center px-2.5 py-2 text-xs font-semibold rounded-xl transition-all duration-150 cursor-pointer {{ $isKeuanganOpen ? 'text-kp-blue-900 bg-kp-blue-50/80 border border-kp-blue-200/60 font-bold' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-200/40 border border-transparent' }}"
                        :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-2.5'">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 transition-colors {{ $isKeuanganOpen ? 'bg-kp-blue-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-500 group-hover:text-slate-800' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="2.5" y="5.5" width="19" height="13" rx="2.5" />
                                <circle cx="12" cy="12" r="2.5" />
                                <path stroke-linecap="round" d="M6 9.5h.01M18 14.5h.01" />
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Divisi Keuangan</span>
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex items-center gap-1.5 shrink-0">
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $isKeuanganOpen ? 'text-kp-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <!-- Submenu Flat & Clean -->
                <div x-cloak 
                     x-show="open && !sidebarCollapsed" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-3 pr-1 pt-1 pb-1 space-y-0.5">
                    
                    @php $isSub = request()->routeIs('keuangan.dashboard'); @endphp
                    <a href="{{ route('keuangan.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Overview Keuangan</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.alur'); @endphp
                    <a href="{{ route('keuangan.alur') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Panduan Alur & SOP</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">SOP</span>
                    </a>

                    <!-- Kategori: OPERASIONAL -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">Operasional Keuangan</div>

                    @php $isSub = request()->routeIs('keuangan.faktur.*'); @endphp
                    <a href="{{ route('keuangan.faktur.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Faktur Penjualan</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.inkaso.*'); @endphp
                    <a href="{{ route('keuangan.inkaso.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Penagihan (Inkaso)</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.anggaran.*'); @endphp
                    <a href="{{ route('keuangan.anggaran.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Pengajuan Anggaran</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.kas.*'); @endphp
                    <a href="{{ route('keuangan.kas.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Buku Kas (Treasury)</span>
                    </a>

                    <!-- Kategori: LAPORAN -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">Laporan Keuangan</div>

                    @php $isSub = request()->routeIs('keuangan.laporan'); @endphp
                    <a href="{{ route('keuangan.laporan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Laporan Keuangan Terpadu</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Rekap</span>
                    </a>
                </div>
            </div>

            <!-- 3. Divisi Accounting -->
            @php
                $isAccountingOpen = request()->routeIs('accounting.*');
            @endphp
            <div x-data="{ open: {{ $isAccountingOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebar(), open = true) : (open = !open)" 
                        :title="sidebarCollapsed ? 'Divisi Accounting' : ''"
                        class="group w-full flex items-center px-2.5 py-2 text-xs font-semibold rounded-xl transition-all duration-150 cursor-pointer {{ $isAccountingOpen ? 'text-kp-blue-900 bg-kp-blue-50/80 border border-kp-blue-200/60 font-bold' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-200/40 border border-transparent' }}"
                        :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-2.5'">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 transition-colors {{ $isAccountingOpen ? 'bg-kp-blue-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-500 group-hover:text-slate-800' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="4" y="3" width="16" height="18" rx="2.5" />
                                <line x1="8" y1="7" x2="16" y2="7" stroke-width="1.8" stroke-linecap="round" />
                                <circle cx="8" cy="11.5" r="0.9" fill="currentColor" />
                                <circle cx="12" cy="11.5" r="0.9" fill="currentColor" />
                                <circle cx="16" cy="11.5" r="0.9" fill="currentColor" />
                                <circle cx="8" cy="15.5" r="0.9" fill="currentColor" />
                                <circle cx="12" cy="15.5" r="0.9" fill="currentColor" />
                                <circle cx="16" cy="15.5" r="0.9" fill="currentColor" />
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Divisi Accounting</span>
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex items-center gap-1.5 shrink-0">
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $isAccountingOpen ? 'text-kp-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <!-- Submenu Flat & Clean -->
                <div x-cloak 
                     x-show="open && !sidebarCollapsed" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-3 pr-1 pt-1 pb-1 space-y-0.5">
                    
                    @php $isSub = request()->routeIs('accounting.dashboard'); @endphp
                    <a href="{{ route('accounting.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Overview Accounting</span>
                    </a>

                    <!-- Section: TARIK DATA -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">Penarikan Data</div>

                    @php $isSub = request()->routeIs('accounting.tarik.iklan*'); @endphp
                    <a href="{{ route('accounting.tarik.iklan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Tarik Data Iklan</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.tarik.sirkulasi*'); @endphp
                    <a href="{{ route('accounting.tarik.sirkulasi') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Tarik Data Sirkulasi</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.tarik.kasir*'); @endphp
                    <a href="{{ route('accounting.tarik.kasir') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Rekonsiliasi Kasir</span>
                    </a>

                    <!-- Section: KARTU PIUTANG -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">Kartu Piutang (Sub-Ledger)</div>

                    @php $isSub = request()->routeIs('accounting.kartu.iklan*'); @endphp
                    <a href="{{ route('accounting.kartu.iklan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Kartu Piutang Iklan</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.kartu.sirkulasi*'); @endphp
                    <a href="{{ route('accounting.kartu.sirkulasi') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Kartu Piutang Sirkulasi</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.kartu.retur*'); @endphp
                    <a href="{{ route('accounting.kartu.retur') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Penyesuaian Retur Koran</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Retur</span>
                    </a>

                    <!-- Section: BUKU BESAR & COA -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">Buku Besar & COA</div>

                    @php $isSub = request()->routeIs('accounting.gl.index*'); @endphp
                    <a href="{{ route('accounting.gl.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Jurnal Umum (GL)</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.gl.coa*'); @endphp
                    <a href="{{ route('accounting.gl.coa') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Daftar Akun (COA)</span>
                    </a>

                    <!-- Section: LAPORAN KEUANGAN -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">Laporan Keuangan</div>

                    @php $isSub = request()->routeIs('accounting.laporan.laba-rugi*'); @endphp
                    <a href="{{ route('accounting.laporan.laba-rugi') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Laporan Laba Rugi</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.laporan.neraca*'); @endphp
                    <a href="{{ route('accounting.laporan.neraca') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Laporan Neraca</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.laporan.buku-besar*'); @endphp
                    <a href="{{ route('accounting.laporan.buku-besar') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Buku Besar Pembantu</span>
                    </a>
                </div>
            </div>

            <!-- 4. Divisi Sirkulasi -->
            @php
                $isSirkulasiOpen = request()->routeIs('sirkulasi.*');
            @endphp
            <div x-data="{ open: {{ $isSirkulasiOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebar(), open = true) : (open = !open)" 
                        :title="sidebarCollapsed ? 'Divisi Sirkulasi' : ''"
                        class="group w-full flex items-center px-2.5 py-2 text-xs font-semibold rounded-xl transition-all duration-150 cursor-pointer {{ $isSirkulasiOpen ? 'text-kp-blue-900 bg-kp-blue-50/80 border border-kp-blue-200/60 font-bold' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-200/40 border border-transparent' }}"
                        :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-2.5'">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 transition-colors {{ $isSirkulasiOpen ? 'bg-kp-blue-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-500 group-hover:text-slate-800' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v9.375c0 .621.504 1.125 1.125 1.125h1.125" />
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Divisi Sirkulasi</span>
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex items-center gap-1.5 shrink-0">
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $isSirkulasiOpen ? 'text-kp-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <!-- Submenu Flat & Clean -->
                <div x-cloak 
                     x-show="open && !sidebarCollapsed" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-3 pr-1 pt-1 pb-1 space-y-0.5">
                    
                    @php $isSub = request()->routeIs('sirkulasi.dashboard'); @endphp
                    <a href="{{ route('sirkulasi.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Overview Sirkulasi</span>
                    </a>

                    <!-- Section: OPERASIONAL JATAH -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">Operasional Jatah</div>

                    @php $isSub = request()->routeIs('sirkulasi.jatah.harian*'); @endphp
                    <a href="{{ route('sirkulasi.jatah.harian') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Jatah Harian Koran</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.jatah.copy*'); @endphp
                    <a href="{{ route('sirkulasi.jatah.copy') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Salin Jatah Koran</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.jatah.quick-edit*'); @endphp
                    <a href="{{ route('sirkulasi.jatah.quick-edit') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Edit Cepat Jatah</span>
                    </a>

                    <!-- Section: LOGISTIK & PENGIRIMAN -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">Logistik & Pengiriman</div>

                    @php $isSub = request()->routeIs('sirkulasi.logistik.surat-jalan*'); @endphp
                    <a href="{{ route('sirkulasi.logistik.surat-jalan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Surat Jalan Pengiriman</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Armada</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.logistik.retur*'); @endphp
                    <a href="{{ route('sirkulasi.logistik.retur') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Retur Koran</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Retur</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.logistik.order-cetak*'); @endphp
                    <a href="{{ route('sirkulasi.logistik.order-cetak') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Order Cetak (SPK)</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">SPK</span>
                    </a>

                    <!-- Section: DATA MASTER -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">Data Master Sirkulasi</div>

                    @php $isSub = request()->routeIs('sirkulasi.master.agen*'); @endphp
                    <a href="{{ route('sirkulasi.master.agen') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Data Agen Koran</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.master.rute*'); @endphp
                    <a href="{{ route('sirkulasi.master.rute') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Rute & Armada</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.master.kalender*'); @endphp
                    <a href="{{ route('sirkulasi.master.kalender') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Kalender Terbit</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.master.tarif*'); @endphp
                    <a href="{{ route('sirkulasi.master.tarif') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Tarif Koran</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.master.pelanggan*'); @endphp
                    <a href="{{ route('sirkulasi.master.pelanggan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Pelanggan Langganan</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Dinas</span>
                    </a>

                    <!-- Section: LAPORAN & CLOSING -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">Laporan & Tutup Buku</div>

                    @php $isSub = request()->routeIs('sirkulasi.laporan.closing*'); @endphp
                    <a href="{{ route('sirkulasi.laporan.closing') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Tutup Buku Bulanan</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Closing</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.laporan.pemasaran*'); @endphp
                    <a href="{{ route('sirkulasi.laporan.pemasaran') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Laporan Pemasaran</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.laporan.penjualan*'); @endphp
                    <a href="{{ route('sirkulasi.laporan.penjualan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Laporan Penjualan</span>
                    </a>
                </div>
            </div>

            <!-- 5. Divisi Kasir -->
            @php
                $isKasirOpen = request()->routeIs('kasir.*');
            @endphp
            <div x-data="{ open: {{ $isKasirOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebar(), open = true) : (open = !open)" 
                        :title="sidebarCollapsed ? 'Divisi Kasir' : ''"
                        class="group w-full flex items-center px-2.5 py-2 text-xs font-semibold rounded-xl transition-all duration-150 cursor-pointer {{ $isKasirOpen ? 'text-kp-blue-900 bg-kp-blue-50/80 border border-kp-blue-200/60 font-bold' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-200/40 border border-transparent' }}"
                        :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-2.5'">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 transition-colors {{ $isKasirOpen ? 'bg-kp-blue-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-500 group-hover:text-slate-800' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="3" y="4.5" width="18" height="15" rx="3" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h2m4 0h4" />
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Divisi Kasir</span>
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex items-center gap-1.5 shrink-0">
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $isKasirOpen ? 'text-kp-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <!-- Submenu Flat & Clean -->
                <div x-cloak 
                     x-show="open && !sidebarCollapsed" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-3 pr-1 pt-1 pb-1 space-y-0.5">
                    
                    @php $isSub = request()->routeIs('kasir.dashboard'); @endphp
                    <a href="{{ route('kasir.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Overview Kasir</span>
                    </a>

                    <!-- Input Cepat Chips -->
                    <div class="pt-2 pb-1 px-1">
                        <div class="text-[9px] font-bold tracking-wider text-slate-400 uppercase mb-1">Input Cepat</div>
                        <div class="grid grid-cols-3 gap-1">
                            <a href="{{ route('transaksikoran.create') }}" class="px-1.5 py-1 text-center text-[10px] font-bold rounded-lg bg-teal-50 text-teal-700 hover:bg-teal-100 transition border border-teal-200/60" title="Input Koran Baru">+ Koran</a>
                            <a href="{{ route('transaksionline.create') }}" class="px-1.5 py-1 text-center text-[10px] font-bold rounded-lg bg-teal-50 text-teal-700 hover:bg-teal-100 transition border border-teal-200/60" title="Input Media Online">+ Online</a>
                            <a href="{{ route('transaksipriangan.create') }}" class="px-1.5 py-1 text-center text-[10px] font-bold rounded-lg bg-teal-50 text-teal-700 hover:bg-teal-100 transition border border-teal-200/60" title="Input Priangan TV">+ TV</a>
                        </div>
                    </div>

                    <!-- Section: PENERIMAAN KAS -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">Penerimaan Kas</div>

                    @php $isSub = request()->routeIs('kasir.penerimaan.iklan'); @endphp
                    <a href="{{ route('kasir.penerimaan.iklan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Setoran Iklan</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Faktur</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.penerimaan.sirkulasi'); @endphp
                    <a href="{{ route('kasir.penerimaan.sirkulasi') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Setoran Sirkulasi</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Koran</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.penerimaan.langganan'); @endphp
                    <a href="{{ route('kasir.penerimaan.langganan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Langganan Koran Dinas</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Dinas</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.penerimaan.tunai'); @endphp
                    <a href="{{ route('kasir.penerimaan.tunai') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Penjualan Tunai / Langsung</span>
                    </a>

                    <!-- Section: PENGELUARAN & BANK -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">Pengeluaran & Bank</div>

                    @php $isSub = request()->routeIs('kasir.pengeluaran.realisasi-bp'); @endphp
                    <a href="{{ route('kasir.pengeluaran.realisasi-bp') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Pengeluaran Kas (BP)</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">BKK</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.setoran-bank'); @endphp
                    <a href="{{ route('kasir.setoran-bank') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Setoran Kas ke Bank</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Bank</span>
                    </a>

                    <!-- Section: KASIR & REKAP -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase">Buku Kas & Rekap</div>

                    @php $isSub = request()->routeIs('kasir.buku-kas'); @endphp
                    <a href="{{ route('kasir.buku-kas') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Buku Kas & Shift</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Shift</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.riwayat-cetak'); @endphp
                    <a href="{{ route('kasir.riwayat-cetak') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Riwayat Transaksi & Cetak</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Kwitansi</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.laporan-harian'); @endphp
                    <a href="{{ route('kasir.laporan-harian') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/50 font-medium' }}">
                        <span class="truncate">Laporan Harian Kasir (LHK)</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Rekap</span>
                    </a>
                </div>
            </div>

            <!-- Divider Label Sistem & Manajemen Divisi -->
            <div x-show="!sidebarCollapsed" class="pt-3 pb-1 px-2.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Sistem & Manajemen
                </span>
            </div>
            <div x-show="sidebarCollapsed" class="my-2 border-t border-slate-200 mx-1"></div>

            <!-- 1. Menu Kelola Pengguna & Divisi (BARU SESUAI STANDAR INDUSTRI) -->
            <div>
                @php $isUsersActive = request()->routeIs('admin.users.*'); @endphp
                <a href="{{ route('admin.users.index') }}" 
                   :title="sidebarCollapsed ? 'Kelola Pengguna & Divisi' : ''"
                   class="group flex items-center px-2.5 py-2 text-xs rounded-xl transition-all duration-150 {{ $isUsersActive ? 'bg-kp-blue-600 text-white font-bold shadow-sm shadow-kp-blue-600/25' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-200/50 font-semibold' }}"
                   :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-2.5'">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 transition-colors {{ $isUsersActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:text-slate-800' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Kelola Tim & Divisi</span>
                    </div>
                </a>
            </div>

            <!-- Audit Log Keamanan (Area 2 CIA Triad) -->
            <div>
                @php $isLogsActive = request()->routeIs('admin.activity-logs.*'); @endphp
                <a href="{{ route('admin.activity-logs.index') }}" 
                   :title="sidebarCollapsed ? 'Audit Log Keamanan' : ''"
                   class="group flex items-center px-2.5 py-2 text-xs rounded-xl transition-all duration-150 {{ $isLogsActive ? 'bg-kp-blue-600 text-white font-bold shadow-sm shadow-kp-blue-600/25' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-200/50 font-semibold' }}"
                   :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-2.5'">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 transition-colors {{ $isLogsActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:text-slate-800' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Audit Log Keamanan</span>
                    </div>
                </a>
            </div>

            <!-- Cadangan Data / Backup (Area 1 CIA Triad) -->
            <div>
                @php $isBackupsActive = request()->routeIs('admin.backups.*'); @endphp
                <a href="{{ route('admin.backups.index') }}" 
                   :title="sidebarCollapsed ? 'Cadangan Data (Backup)' : ''"
                   class="group flex items-center px-2.5 py-2 text-xs rounded-xl transition-all duration-150 {{ $isBackupsActive ? 'bg-kp-blue-600 text-white font-bold shadow-sm shadow-kp-blue-600/25' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-200/50 font-semibold' }}"
                   :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-2.5'">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 transition-colors {{ $isBackupsActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:text-slate-800' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7zm0 5h16" />
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Cadangan Data (Backup)</span>
                    </div>
                </a>
            </div>

            <!-- 2. Pengaturan Sistem Global -->
            <div>
                @php $isConfigActive = request()->routeIs('iklan.pengaturan.*'); @endphp
                <a href="{{ route('iklan.pengaturan.index') }}" 
                   :title="sidebarCollapsed ? 'Pengaturan Tarif & PPN' : ''"
                   class="group flex items-center px-2.5 py-2 text-xs rounded-xl transition-all duration-150 {{ $isConfigActive ? 'bg-kp-blue-600 text-white font-bold shadow-sm shadow-kp-blue-600/25' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-200/50 font-semibold' }}"
                   :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-2.5'">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 transition-colors {{ $isConfigActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:text-slate-800' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Pengaturan Tarif & PPN</span>
                    </div>
                </a>
            </div>

            <!-- 3. Profil Administrator -->
            <div>
                @php $isProfileActive = request()->routeIs('profile.*'); @endphp
                <a href="{{ route('profile.edit') }}" 
                   :title="sidebarCollapsed ? 'Profil Pengguna' : ''"
                   class="group flex items-center px-2.5 py-2 text-xs rounded-xl transition-all duration-150 {{ $isProfileActive ? 'bg-kp-blue-600 text-white font-bold shadow-sm shadow-kp-blue-600/25' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-200/50 font-semibold' }}"
                   :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-2.5'">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 transition-colors {{ $isProfileActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:text-slate-800' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Profil Akun Admin</span>
                    </div>
                </a>
            </div>

        @else
            <!-- ============================================== -->
            <!-- JIKA BUKAN ADMIN: TAMPILAN TERFOKUS SESUAI ROLE-->
            <!-- ============================================== -->

            @if($isIklan)
                @php $isIklanDash = request()->routeIs('iklan.dashboard'); @endphp
                <div>
                    <a href="{{ route('iklan.dashboard') }}" 
                       :title="sidebarCollapsed ? 'Dashboard Iklan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isIklanDash ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-200/50 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isIklanDash ? 'text-white' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="3.5" y="3.5" width="6.5" height="6.5" rx="2" />
                                <rect x="14" y="3.5" width="6.5" height="6.5" rx="2" />
                                <rect x="14" y="14" width="6.5" height="6.5" rx="2" />
                                <rect x="3.5" y="14" width="6.5" height="6.5" rx="2" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Dashboard Iklan</span>
                        </div>
                    </a>
                </div>

                <!-- 1. FUNGSI: ORDER IKLAN -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Order Iklan
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-slate-200 mx-2"></div>

                    <!-- Input Cepat Chips -->
                    <div x-show="!sidebarCollapsed" class="px-2 pb-1.5 pt-0.5">
                        <div class="text-[9px] font-bold tracking-wider text-slate-400 uppercase mb-1">Input Cepat Order</div>
                        <div class="grid grid-cols-3 gap-1">
                            <a href="{{ route('transaksikoran.create') }}" class="px-1.5 py-1 text-center text-[10px] font-bold rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-100 transition border border-sky-200/60" title="Input Iklan Koran">+ Koran</a>
                            <a href="{{ route('transaksionline.create') }}" class="px-1.5 py-1 text-center text-[10px] font-bold rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition border border-emerald-200/60" title="Input Iklan Online">+ Online</a>
                            <a href="{{ route('transaksipriangan.create') }}" class="px-1.5 py-1 text-center text-[10px] font-bold rounded-lg bg-purple-50 text-purple-700 hover:bg-purple-100 transition border border-purple-200/60" title="Input Priangan TV">+ TV</a>
                        </div>
                    </div>

                    <!-- Iklan Koran -->
                    @php $isSub = request()->routeIs('transaksikoran.*') && !request()->routeIs('transaksikoran.create'); @endphp
                    <a href="{{ route('transaksikoran.index') }}" 
                       :title="sidebarCollapsed ? 'Iklan Koran' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-white' : 'text-sky-600 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Iklan Koran</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Koran</span>
                    </a>

                    <!-- Iklan Online -->
                    @php $isSub = request()->routeIs('transaksionline.*') && !request()->routeIs('transaksionline.create'); @endphp
                    <a href="{{ route('transaksionline.index') }}" 
                       :title="sidebarCollapsed ? 'Iklan Online' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-white' : 'text-emerald-600 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-.778.099-1.533.284-2.253" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Iklan Online</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Online</span>
                    </a>

                    <!-- Iklan Priangan TV -->
                    @php $isSub = request()->routeIs('transaksipriangan.*') && !request()->routeIs('transaksipriangan.create'); @endphp
                    <a href="{{ route('transaksipriangan.index') }}" 
                       :title="sidebarCollapsed ? 'Iklan Priangan TV' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-white' : 'text-purple-600 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="3" y="6" width="18" height="13" rx="2.5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 2.5l4 3.5 4-3.5" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Iklan Priangan TV</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">TV</span>
                    </a>

                    <!-- Kwitansi Pembayaran -->
                    @php $isSub = request()->routeIs('pembayaran-iklan.*'); @endphp
                    <a href="{{ route('pembayaran-iklan.index') }}" 
                       :title="sidebarCollapsed ? 'Kwitansi Pembayaran' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-white' : 'text-amber-600 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Kwitansi Pembayaran</span>
                        </div>
                    </a>
                </div>

                <!-- 2. FUNGSI: MASTER DATA -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Master Data
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-slate-200 mx-2"></div>

                    <!-- Data Pemasang -->
                    @php $isSub = request()->routeIs('pelanggan.*'); @endphp
                    <a href="{{ route('pelanggan.index') }}" 
                       :title="sidebarCollapsed ? 'Data Pemasang' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-white' : 'text-blue-600 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Data Pemasang</span>
                        </div>
                    </a>

                    <!-- Data Sales / AE -->
                    @php $isAe = request()->routeIs('sales-ae.*'); @endphp
                    <a href="{{ route('sales-ae.index') }}" 
                       :title="sidebarCollapsed ? 'Data Sales / AE' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAe ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isAe ? 'text-white' : 'text-indigo-600 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Data Sales / AE</span>
                        </div>
                    </a>
                </div>

                <!-- 3. FUNGSI: LAPORAN -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Laporan
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-slate-200 mx-2"></div>

                    @php $isLap = request()->routeIs('laporan.*') || request()->routeIs('laporankoran.*') || request()->routeIs('laporanonline.*') || request()->routeIs('laporanpriangan.*'); @endphp
                    <a href="{{ route('laporan.index') }}" 
                       :title="sidebarCollapsed ? 'Laporan Penjualan Iklan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isLap ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isLap ? 'text-white' : 'text-teal-600 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Laporan Penjualan Iklan</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isLap ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Rekap</span>
                    </a>
                </div>

                <!-- 4. FUNGSI: TARIF & PENGATURAN -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Tarif &amp; Pengaturan
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-slate-200 mx-2"></div>

                    <!-- Tarif Iklan -->
                    @php $isSub = request()->routeIs('iklan.pengaturan.*'); @endphp
                    <a href="{{ route('iklan.pengaturan.index') }}" 
                       :title="sidebarCollapsed ? 'Tarif & Pajak Iklan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-white' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Tarif &amp; Pajak Iklan</span>
                        </div>
                    </a>

                    <!-- Jenis & Ukuran Iklan -->
                    @php $isSub = request()->routeIs('jenis-iklan.*'); @endphp
                    <a href="{{ route('jenis-iklan.index') }}" 
                       :title="sidebarCollapsed ? 'Jenis & Ukuran Iklan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-white' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="3" y="3" width="18" height="18" rx="2" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9h18M9 21V9" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Jenis &amp; Ukuran Iklan</span>
                        </div>
                    </a>

                    <!-- Kategori Media -->
                    @php $isSub = request()->routeIs('kategori-media.*'); @endphp
                    <a href="{{ route('kategori-media.index') }}" 
                       :title="sidebarCollapsed ? 'Kategori Media' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-white' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Kategori Media</span>
                        </div>
                    </a>
                </div>
            @endif

            @if(!$isIklan && $isKeuangan)
                @php $isKeuDash = request()->routeIs('keuangan.dashboard'); @endphp
                <div>
                    <a href="{{ route('keuangan.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isKeuDash ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isKeuDash ? 'text-emerald-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="3" y="4.5" width="18" height="15" rx="3.5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 13.5l3.5-3.5 3 3 3.5-4" />
                            </svg>
                            <span class="truncate">Dashboard Keuangan</span>
                        </div>
                        @if($isKeuDash)
                            
                        @endif
                    </a>
                </div>

                <!-- 1. Faktur & Piutang Pelanggan -->
                <div class="space-y-0.5 pt-1">
                    <div class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Faktur &amp; Piutang
                        </span>
                    </div>

                    @php $isAlur = request()->routeIs('keuangan.alur'); @endphp
                    <a href="{{ route('keuangan.alur') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAlur ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" /></svg>
                            <span class="truncate">Alur Faktur &amp; Inkaso</span>
                        </div>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-cyan-100 text-cyan-800">SOP</span>
                    </a>

                    @php $isFaktur = request()->routeIs('keuangan.faktur.*'); @endphp
                    <a href="{{ route('keuangan.faktur.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isFaktur ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <span class="truncate">Faktur Penjualan</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isFaktur ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Faktur</span>
                    </a>

                    @php $isInkaso = request()->routeIs('keuangan.inkaso.*'); @endphp
                    <a href="{{ route('keuangan.inkaso.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isInkaso ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span class="truncate">Penugasan Inkaso</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isInkaso ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Inkaso</span>
                    </a>
                </div>

                <!-- 2. Anggaran Operasional -->
                <div class="space-y-0.5 pt-2">
                    <div class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Anggaran Operasional
                        </span>
                    </div>

                    @php $isAnggaran = request()->routeIs('keuangan.anggaran.*'); @endphp
                    <a href="{{ route('keuangan.anggaran.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAnggaran ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /></svg>
                            <span class="truncate">Pengajuan Anggaran</span>
                        </div>
                    </a>
                </div>

                <!-- 3. Kas & Perbendaharaan -->
                <div class="space-y-0.5 pt-2">
                    <div class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Kas &amp; Perbendaharaan
                        </span>
                    </div>

                    @php $isKas = request()->routeIs('keuangan.kas.*'); @endphp
                    <a href="{{ route('keuangan.kas.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isKas ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="truncate">Buku Kas (Treasury)</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isKas ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Kas</span>
                    </a>

                    @php $isLapKeu = request()->routeIs('keuangan.laporan'); @endphp
                    <a href="{{ route('keuangan.laporan') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isLapKeu ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isLapKeu ? 'text-amber-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="3" y="4.5" width="18" height="15" rx="3.5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 13.5l3.5-3.5 3 3 3.5-4" />
                            </svg>
                            <span class="truncate">Laporan Keuangan</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isLapKeu ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Rekap</span>
                    </a>
                </div>
            @endif

            @if(!$isIklan && $isAccounting)
                @php $isAccDash = request()->routeIs('accounting.dashboard'); @endphp
                <div>
                    <a href="{{ route('accounting.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAccDash ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isAccDash ? 'text-purple-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>
                            <span class="truncate">Dashboard Accounting</span>
                        </div>
                        @if($isAccDash)
                            
                        @endif
                    </a>
                </div>

                <!-- 1. Tarik & Validasi Data (Controlling) -->
                <div class="space-y-0.5 pt-1">
                    <div class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Tarik & Validasi Data
                        </span>
                    </div>

                    @php $isSub = request()->routeIs('accounting.tarik.iklan*'); @endphp
                    <a href="{{ route('accounting.tarik.iklan') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-purple-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span class="truncate">Penarikan Iklan Harian</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Harian</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.tarik.sirkulasi*'); @endphp
                    <a href="{{ route('accounting.tarik.sirkulasi') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-amber-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="truncate">Penarikan Sirkulasi Bulanan</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Bulanan</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.tarik.kasir*'); @endphp
                    <a href="{{ route('accounting.tarik.kasir') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-emerald-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="truncate">Rekonsiliasi Kasir (Buku Kas)</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Kas</span>
                    </a>
                </div>

                <!-- 2. Kartu Sub-Ledger (Kartu Piutang) -->
                <div class="space-y-0.5 pt-2">
                    <div class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Kartu Sub-Ledger Piutang
                        </span>
                    </div>

                    @php $isSub = request()->routeIs('accounting.kartu.iklan*'); @endphp
                    <a href="{{ route('accounting.kartu.iklan') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-purple-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="truncate">Kartu Piutang Iklan</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.kartu.sirkulasi*'); @endphp
                    <a href="{{ route('accounting.kartu.sirkulasi') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-purple-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="truncate">Kartu Piutang Agen Sirkulasi</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.kartu.retur*'); @endphp
                    <a href="{{ route('accounting.kartu.retur') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-rose-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 15v-1a4 4 0 00-4-4H4m0 0l3-3m-3 3l3 3m5 4v1a3 3 0 003 3h4" />
                            </svg>
                            <span class="truncate">Entri Retur Koran (BAPR)</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Retur</span>
                    </a>
                </div>

                <!-- 3. Buku Besar & Jurnal -->
                <div class="space-y-0.5 pt-2">
                    <div class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Buku Besar & Jurnal
                        </span>
                    </div>

                    @php $isSub = request()->routeIs('accounting.gl.index*'); @endphp
                    <a href="{{ route('accounting.gl.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-indigo-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span class="truncate">Jurnal Umum / GL</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">GL</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.gl.coa*'); @endphp
                    <a href="{{ route('accounting.gl.coa') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-purple-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            <span class="truncate">Bagan Akun (COA)</span>
                        </div>
                    </a>
                </div>

                <!-- 4. Laporan Keuangan -->
                <div class="space-y-0.5 pt-2">
                    <div class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Laporan Keuangan
                        </span>
                    </div>

                    @php $isSub = request()->routeIs('accounting.laporan.laba-rugi*'); @endphp
                    <a href="{{ route('accounting.laporan.laba-rugi') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-emerald-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                            <span class="truncate">Laporan Laba Rugi</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.laporan.neraca*'); @endphp
                    <a href="{{ route('accounting.laporan.neraca') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-sky-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                            </svg>
                            <span class="truncate">Neraca Keuangan (Balance Sheet)</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.laporan.buku-besar*'); @endphp
                    <a href="{{ route('accounting.laporan.buku-besar') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-purple-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span class="truncate">Buku Besar Pembantu</span>
                        </div>
                    </a>
                </div>
            @endif

            @if(!$isIklan && $isSirkulasi)
                @php $isSirDash = request()->routeIs('sirkulasi.dashboard'); @endphp
                <div>
                    <a href="{{ route('sirkulasi.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSirDash ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSirDash ? 'text-amber-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <circle cx="12" cy="7.5" r="3" />
                                <path stroke-linecap="round" d="M5.5 19.5c0-3.3 2.9-6 6.5-6s6.5 2.7 6.5 6" />
                            </svg>
                            <span class="truncate">Dashboard Sirkulasi</span>
                        </div>
                        @if($isSirDash)
                            
                        @endif
                    </a>
                </div>

                <!-- 1. Master Data Sirkulasi -->
                <div class="space-y-0.5 pt-1">
                    <div class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Master Data Sirkulasi
                        </span>
                    </div>

                    @php $isSub = request()->routeIs('sirkulasi.master.agen*'); @endphp
                    <a href="{{ route('sirkulasi.master.agen') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-amber-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="truncate">Agen & Sub-Agen</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Agen</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.master.rute*'); @endphp
                    <a href="{{ route('sirkulasi.master.rute') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-sky-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            <span class="truncate">Wilayah & Rute Armada</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Rute</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.master.kalender*'); @endphp
                    <a href="{{ route('sirkulasi.master.kalender') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-purple-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="truncate">Kalender Terbit Koran</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.master.tarif*'); @endphp
                    <a href="{{ route('sirkulasi.master.tarif') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-emerald-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="truncate">Tarif & Skema Gratis</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.master.pelanggan*'); @endphp
                    <a href="{{ route('sirkulasi.master.pelanggan') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-indigo-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="truncate">Pelanggan Dinas & Loper</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Dinas</span>
                    </a>
                </div>

                <!-- 2. Operasional Jatah (Siklus H-1) -->
                <div class="space-y-0.5 pt-2">
                    <div class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Operasional Jatah (H-1)
                        </span>
                    </div>

                    @php $isSub = request()->routeIs('sirkulasi.jatah.harian*'); @endphp
                    <a href="{{ route('sirkulasi.jatah.harian') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-amber-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <span class="truncate">Entri Jatah Harian H+1</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Input</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.jatah.copy*'); @endphp
                    <a href="{{ route('sirkulasi.jatah.copy') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-sky-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                            </svg>
                            <span class="truncate">Salin Pola Jatah</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.jatah.quick-edit*'); @endphp
                    <a href="{{ route('sirkulasi.jatah.quick-edit') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-rose-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span class="truncate">Perubahan Cepat (Quick)</span>
                        </div>
                    </a>
                </div>

                <!-- 3. Logistik & Distribusi Subuh -->
                <div class="space-y-0.5 pt-2">
                    <div class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Logistik & Pengiriman Subuh
                        </span>
                    </div>

                    @php $isSub = request()->routeIs('sirkulasi.logistik.surat-jalan*'); @endphp
                    <a href="{{ route('sirkulasi.logistik.surat-jalan') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-sky-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                            <span class="truncate">Surat Jalan & Packing</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Armada</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.logistik.retur*'); @endphp
                    <a href="{{ route('sirkulasi.logistik.retur') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-rose-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                            </svg>
                            <span class="truncate">Berita Acara Retur (BAPR)</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Retur</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.logistik.order-cetak*'); @endphp
                    <a href="{{ route('sirkulasi.logistik.order-cetak') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-orange-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            <span class="truncate">SPK Order Percetakan</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">SPK</span>
                    </a>
                </div>

                <!-- 4. Tutup Data & Pelaporan -->
                <div class="space-y-0.5 pt-2">
                    <div class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Tutup Data & Pelaporan
                        </span>
                    </div>

                    @php $isSub = request()->routeIs('sirkulasi.laporan.closing*'); @endphp
                    <a href="{{ route('sirkulasi.laporan.closing') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-purple-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span class="truncate">Tutup Data Bulanan</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Closing</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.laporan.pemasaran*'); @endphp
                    <a href="{{ route('sirkulasi.laporan.pemasaran') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-teal-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span class="truncate">Rekap Pemasaran (Fisik)</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.laporan.penjualan*'); @endphp
                    <a href="{{ route('sirkulasi.laporan.penjualan') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-emerald-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="truncate">Rekapitulasi Penjualan</span>
                        </div>
                    </a>
                </div>
            @endif

            @if(!$isIklan && $isKasir)
                @php $isKasDash = request()->routeIs('kasir.dashboard'); @endphp
                <div>
                    <a href="{{ route('kasir.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isKasDash ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isKasDash ? 'text-teal-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span class="truncate">Overview Kasir</span>
                        </div>
                        @if($isKasDash)
                            
                        @endif
                    </a>
                </div>

                <!-- Navigasi Menu Operasional Divisi Kasir -->
                <div class="space-y-0.5 pt-1">
                    <div class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Operasional Kasir
                        </span>
                    </div>

                    <!-- Input Transaksi Baru (Kasir Shortcut) -->
                    @php
                        $isInputKasirOpen2 = request()->routeIs('transaksikoran.create') 
                                          || request()->routeIs('transaksionline.create') 
                                          || request()->routeIs('transaksipriangan.create');
                    @endphp
                    <div x-data="{ openInputK2: {{ $isInputKasirOpen2 ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <button type="button" 
                                    @click="openInputK2 = !openInputK2" 
                                    class="w-full flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isInputKasirOpen2 ? 'bg-teal-50 text-teal-900 font-bold border border-teal-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <svg class="w-4 h-4 text-teal-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    <span class="truncate">Input Transaksi Baru</span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-teal-100 text-teal-800">+ Baru</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openInputK2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                            </button>
                        </div>

                        <!-- 3 Submenu Item: Koran, Online, Priangan TV -->
                        <div x-cloak 
                             x-show="openInputK2" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                            <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-teal-200"></div>

                            @php $isSub = request()->routeIs('transaksikoran.create'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-teal-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('transaksikoran.create') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Koran Cetak</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Koran</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('transaksionline.create'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-teal-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('transaksionline.create') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Media Online</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Online</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('transaksipriangan.create'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-teal-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('transaksipriangan.create') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Priangan TV</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">TV</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Setoran Iklan -->
                    @php $isSub = request()->routeIs('kasir.penerimaan.iklan'); @endphp
                    <a href="{{ route('kasir.penerimaan.iklan') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-teal-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span class="truncate">Setoran Iklan</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Faktur</span>
                    </a>

                    <!-- Setoran Sirkulasi -->
                    @php $isSub = request()->routeIs('kasir.penerimaan.sirkulasi'); @endphp
                    <a href="{{ route('kasir.penerimaan.sirkulasi') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-sky-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5" />
                            </svg>
                            <span class="truncate">Setoran Sirkulasi</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Koran</span>
                    </a>

                    <!-- Koran Langganan Instansi / Dinas -->
                    @php $isSub = request()->routeIs('kasir.penerimaan.langganan'); @endphp
                    <a href="{{ route('kasir.penerimaan.langganan') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-purple-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5m-15 10.5V10.5m18 0a48.667 48.667 0 00-10.5-.75A48.667 48.667 0 003 10.5" />
                            </svg>
                            <span class="truncate">Koran Langganan</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Dinas</span>
                    </a>

                    <!-- Penjualan Langsung -->
                    @php $isSub = request()->routeIs('kasir.penerimaan.tunai'); @endphp
                    <a href="{{ route('kasir.penerimaan.tunai') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-emerald-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5a1.5 1.5 0 011.5 1.5v9a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5v-9a1.5 1.5 0 011.5-1.5z" />
                            </svg>
                            <span class="truncate">Penjualan Langsung</span>
                        </div>
                    </a>

                    <!-- Realisasi BP -->
                    @php $isSub = request()->routeIs('kasir.pengeluaran.realisasi-bp'); @endphp
                    <a href="{{ route('kasir.pengeluaran.realisasi-bp') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-rose-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="truncate">Realisasi BP</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">BKK</span>
                    </a>

                    <!-- Setor Kas ke Bank -->
                    @php $isSub = request()->routeIs('kasir.setoran-bank'); @endphp
                    <a href="{{ route('kasir.setoran-bank') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-blue-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                            </svg>
                            <span class="truncate">Setor Kas ke Bank</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Bank</span>
                    </a>

                    <!-- Buku Kas & Tutup -->
                    @php $isSub = request()->routeIs('kasir.buku-kas'); @endphp
                    <a href="{{ route('kasir.buku-kas') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-emerald-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18c-2.305 0-4.408.867-6 2.292m0-14.25v14.25" />
                            </svg>
                            <span class="truncate">Buku Kas & Tutup</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Shift</span>
                    </a>

                    <!-- Riwayat & Cetak -->
                    @php $isSub = request()->routeIs('kasir.riwayat-cetak'); @endphp
                    <a href="{{ route('kasir.riwayat-cetak') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-amber-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span class="truncate">Riwayat & Cetak</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Kwitansi</span>
                    </a>

                    <!-- Laporan Harian (LHK) -->
                    @php $isSub = request()->routeIs('kasir.laporan-harian'); @endphp
                    <a href="{{ route('kasir.laporan-harian') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-teal-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                            <span class="truncate">Laporan Harian (LHK)</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600' }}">Rekap</span>
                    </a>
                </div>
            @endif

        @endif

    </nav>

    <!-- 3. Footer Sidebar: User Profile Ringkas & Logout -->
    <div class="p-2.5 border-t border-slate-200/80 bg-white/60">
        <div class="flex items-center rounded-xl bg-slate-100/80 border border-slate-200/70 transition-all"
             :class="sidebarCollapsed ? 'p-1.5 justify-center' : 'px-2.5 py-2 justify-between'">
            <div class="flex items-center gap-2.5 min-w-0" :title="sidebarCollapsed ? '{{ Auth::user()?->name ?? 'User' }} ({{ Auth::user()?->role_label ?? 'User' }})' : ''">
                <div class="w-7 h-7 rounded-lg bg-kp-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                    {{ strtoupper(substr(Auth::user()?->name ?? 'U', 0, 1)) }}
                </div>
                <div x-show="!sidebarCollapsed" class="min-w-0 leading-tight">
                    <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()?->name ?? 'User' }}</p>
                    <p class="text-[10px] text-slate-500 font-medium truncate">{{ Auth::user()?->email ?? '-' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="shrink-0" :class="sidebarCollapsed ? 'hidden' : 'block'">
                @csrf
                <button type="submit" 
                        title="Keluar dari Sistem" 
                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </button>
            </form>
        </div>
        <!-- Logout icon button when collapsed -->
        <div x-show="sidebarCollapsed" class="mt-1 flex justify-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        title="Keluar dari Sistem" 
                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

</aside>
