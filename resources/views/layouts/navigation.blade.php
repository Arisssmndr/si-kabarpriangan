<!-- Sidebar Navigasi Kabar Priangan (Modern Standard Collapsible - 60:30:10 Design) -->
<aside class="w-full bg-[#0E527B] text-white flex flex-col shrink-0 h-full border-r border-[#0B4365] select-none shadow-[1px_0_4px_rgba(0,0,0,0.05)] transition-all duration-200 overflow-x-hidden sidebar-kp">

    <!-- 1. Header Brand & Identitas (Collapsible Ready) -->
    <div class="px-3.5 py-3 border-b border-white/10 flex items-center justify-between bg-[#0E527B] min-h-[58px]"
         :class="sidebarCollapsed ? 'px-2 justify-center' : 'px-3.5 justify-between'">
        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
            <div class="w-8 h-8 rounded-full bg-white p-0.5 shadow-sm border border-white/20 shrink-0 flex items-center justify-center cursor-pointer transition-transform hover:scale-105 overflow-hidden"
                 :title="sidebarCollapsed ? 'Klik untuk Perluas Sidebar' : ''"
                 @click="if (sidebarCollapsed) toggleSidebar()">
                <img src="{{ asset('images/kabarpriangan.png') }}" 
                     alt="Logo Kabar Priangan" 
                     class="w-full h-full object-cover rounded-full"
                     onerror="this.src='{{ asset('kabarpriangan.png') }}'">
            </div>
            <div x-show="!sidebarCollapsed" class="leading-tight min-w-0">
                <h1 class="font-extrabold text-xs tracking-tight text-white truncate">
                    KABAR PRIANGAN
                </h1>
                <p class="text-[10px] font-semibold text-sky-200/80 uppercase tracking-wider truncate mt-0.5">
                    {{ Auth::user()->role_label ?? 'Sistem Terpadu' }}
                </p>
            </div>
        </div>
        <!-- Tombol Ciutkan Sidebar Langsung di Sidemenu (Standar Industri Modern) -->
        <button x-show="!sidebarCollapsed" 
                @click="toggleSidebar()" 
                type="button" 
                title="Ciutkan Sidebar"
                class="hidden md:flex p-1.5 rounded-lg text-sky-200/70 hover:text-white hover:bg-white/10 transition cursor-pointer shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
            </svg>
        </button>
    </div>

    <!-- 2. Navigasi Hirarkis Berbasis Role & Accordion -->
    <nav class="flex-1 py-3 space-y-1 overflow-y-auto sidebar-scroll" :class="sidebarCollapsed ? 'px-2' : 'px-3'">

        @php
            $user = Auth::user();
            $isAdmin = $user && $user->isAdmin();
            $isIklan = $user && ($isAdmin || $user->hasRole('iklan'));
            $isKeuangan = $user && ($isAdmin || $user->hasRole('keuangan') || $user->hasRole('admin_keuangan') || $user->hasRole('petugas_inkaso') || $user->hasRole('kepala_divisi'));
            $isFaktur = $user && ($isAdmin || $user->hasRole('faktur'));
            $isAnggaran = $user && ($isAdmin || $user->hasRole('anggaran'));
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
                   class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAdminActive ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                   :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                    <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isAdminActive ? 'text-[#0E527B]' : 'text-sky-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <rect x="3.5" y="3.5" width="6.5" height="6.5" rx="2" />
                            <rect x="14" y="3.5" width="6.5" height="6.5" rx="2" />
                            <rect x="14" y="14" width="6.5" height="6.5" rx="2" />
                            <rect x="3.5" y="14" width="6.5" height="6.5" rx="2" />
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Dashboard Admin</span>
                    </div>
                </a>
            </div>

            <!-- Laporan Eksekutif Konsolidasi Lintas Divisi (Standar Industri) -->
            <div>
                @php $isLaporanActive = request()->routeIs('admin.laporan.*'); @endphp
                <a href="{{ route('admin.laporan.index') }}" 
                   :title="sidebarCollapsed ? 'Laporan Eksekutif' : ''"
                   class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isLaporanActive ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                   :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                    <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isLaporanActive ? 'text-[#0E527B]' : 'text-indigo-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Laporan Eksekutif</span>
                    </div>
                </a>
            </div>

            <!-- Divider Label Modul -->
            <div x-show="!sidebarCollapsed" class="pt-3 pb-1 px-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-sky-200/90">
                    Modul Divisi
                </span>
            </div>
            <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

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
                            || request()->routeIs('kop-iklan.*');
            @endphp
            <div x-data="{ open: {{ $isIklanOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebar(), open = true) : (open = !open)" 
                        :title="sidebarCollapsed ? 'Divisi Iklan' : ''"
                        class="group w-full flex items-center px-3 py-2 text-sm font-semibold rounded-xl transition-all duration-150 cursor-pointer {{ $isIklanOpen ? 'bg-white/15 text-white font-bold border border-white/20 shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 border border-transparent' }}"
                        :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isIklanOpen ? 'text-white' : 'text-sky-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.388-.046-.771-.115-1.15-.205m6.75-7.397V3.375c0-.621-.504-1.125-1.125-1.125H4.875c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-6h7.5m-7.5-3h7.5m-7.5-3h7.5" />
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-sm">Divisi Iklan</span>
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex items-center gap-1.5 shrink-0">
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $isIklanOpen ? 'text-white' : 'text-sky-200/70 group-hover:text-white' }}" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
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
                     class="pl-3 pr-1 pt-1 pb-1 space-y-0.5 border-l border-white/10 ml-3.5 my-1">
                    
                    <!-- 1. DASHBOARD -->
                    <div class="pt-1 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Dashboard</div>

                    @php $isSub = request()->routeIs('iklan.dashboard'); @endphp
                    <a href="{{ route('iklan.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Dashboard Iklan</span>
                    </a>

                    <!-- 2. TRANSAKSI IKLAN -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Transaksi Iklan</div>
                    
                    @php $isSub = request()->routeIs('transaksikoran.*'); @endphp
                    <a href="{{ route('transaksikoran.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Iklan Koran</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Koran</span>
                    </a>

                    @php $isSub = request()->routeIs('transaksionline.*'); @endphp
                    <a href="{{ route('transaksionline.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Iklan Online</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Online</span>
                    </a>

                    @php $isSub = request()->routeIs('transaksipriangan.*'); @endphp
                    <a href="{{ route('transaksipriangan.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Iklan TV</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">TV</span>
                    </a>

                    <!-- 3. OPERASIONAL -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Operasional</div>

                    @php $isSub = request()->routeIs('iklan.rekap-pracetak.*'); @endphp
                    <a href="{{ route('iklan.rekap-pracetak.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Dummy Pracetak</span>
                    </a>

                    <!-- 4. LAPORAN IKLAN -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Laporan Iklan</div>

                    @php $isSub = request()->routeIs('laporan.index'); @endphp
                    <a href="{{ route('laporan.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Laporan Pemuatan</span>
                    </a>

                    @php $isSub = request()->routeIs('laporan.rekap-sales'); @endphp
                    <a href="{{ route('laporan.rekap-sales') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Laporan Penjualan</span>
                    </a>

                    @php $isSub = request()->routeIs('laporan.klasifikasi'); @endphp
                    <a href="{{ route('laporan.klasifikasi') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Klasifikasi Iklan</span>
                    </a>

                    @php $isSub = request()->routeIs('laporan.insentif-ae'); @endphp
                    <a href="{{ route('laporan.insentif-ae') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Insentif Sales</span>
                    </a>

                    @php $isSub = request()->routeIs('laporan.pembatalan'); @endphp
                    <a href="{{ route('laporan.pembatalan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Laporan Batal</span>
                    </a>

                    <!-- 5. MASTER DATA -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Master Data</div>

                    @php $isSub = request()->routeIs('pelanggan.*'); @endphp
                    <a href="{{ route('pelanggan.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Data Pemasang</span>
                    </a>

                    @php $isSub = request()->routeIs('sales-ae.*'); @endphp
                    <a href="{{ route('sales-ae.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Data Sales</span>
                    </a>

                    @php $isSub = request()->routeIs('jenis-iklan.*'); @endphp
                    <a href="{{ route('jenis-iklan.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Jenis Iklan</span>
                    </a>

                    @php $isSub = request()->routeIs('kategori-media.*'); @endphp
                    <a href="{{ route('kategori-media.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Kategori Media</span>
                    </a>

                    @php $isSub = request()->routeIs('kop-kota.*'); @endphp
                    <a href="{{ route('kop-kota.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Kop Kota</span>
                    </a>

                    @php $isSub = request()->routeIs('kop-iklan.*'); @endphp
                    <a href="{{ route('kop-iklan.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Kop Iklan</span>
                    </a>

                    <!-- 6. PENGATURAN -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Pengaturan</div>

                    @php $isSub = request()->routeIs('iklan.pengaturan.*'); @endphp
                    <a href="{{ route('iklan.pengaturan.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Pengaturan Tarif</span>
                    </a>
                </div>
            </div>

            <!-- 2. Divisi Faktur -->
            @php
                $isFakturOpen = request()->routeIs('keuangan.faktur.*') 
                             || request()->routeIs('keuangan.inkaso.*')
                             || request()->routeIs('laporan.aging-piutang*')
                             || (request()->routeIs('keuangan.dashboard') && !request()->routeIs('keuangan.anggaran.*') && !request()->routeIs('keuangan.kas.*'))
                             || (request()->routeIs('keuangan.alur') && !request()->routeIs('keuangan.anggaran.*'));
            @endphp
            <div x-data="{ open: {{ $isFakturOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebar(), open = true) : (open = !open)" 
                        :title="sidebarCollapsed ? 'Divisi Faktur' : ''"
                        class="group w-full flex items-center px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-150 cursor-pointer {{ $isFakturOpen ? 'bg-white/15 text-white font-bold border border-white/20 shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 border border-transparent' }}"
                        :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isFakturOpen ? 'text-white' : 'text-cyan-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Divisi Faktur</span>
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex items-center gap-1.5 shrink-0">
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $isFakturOpen ? 'text-white' : 'text-sky-200/70 group-hover:text-white' }}" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <!-- Submenu Divisi Faktur -->
                <div x-cloak 
                     x-show="open && !sidebarCollapsed" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-3 pr-1 pt-1 pb-1 space-y-0.5 border-l border-white/10 ml-3.5 my-1">
                    
                    @php $isSub = request()->routeIs('keuangan.dashboard'); @endphp
                    <a href="{{ route('keuangan.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Overview Faktur</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.alur'); @endphp
                    <a href="{{ route('keuangan.alur') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Panduan SOP Faktur</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">SOP</span>
                    </a>

                    <!-- 1. TABEL FAKTUR IKLAN -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-sky-200/70 uppercase">1. Tabel Faktur Iklan</div>

                    @php $isSub = request()->routeIs('keuangan.faktur.iklan.index') || (request()->routeIs('keuangan.faktur.index') && request('kategori') !== 'sirkulasi'); @endphp
                    <a href="{{ route('keuangan.faktur.iklan.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Faktur Keseluruhan</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-md {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Iklan</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.iklan.rekap*'); @endphp
                    <a href="{{ route('keuangan.faktur.iklan.rekap') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Rekap Tgl / Bulan</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.iklan.susulan'); @endphp
                    <a href="{{ route('keuangan.faktur.iklan.susulan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Faktur Iklan Susulan</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-md {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Susulan</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.iklan.tunai'); @endphp
                    <a href="{{ route('keuangan.faktur.iklan.tunai') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Rekap Iklan Tunai</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.iklan.piutang*'); @endphp
                    <a href="{{ route('keuangan.faktur.iklan.piutang') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Rekap Iklan Piutang</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-md {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Piutang</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.iklan.stopper-bonus'); @endphp
                    <a href="{{ route('keuangan.faktur.iklan.stopper-bonus') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Rekap Stopper / Bonus</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.iklan.stok-opname*'); @endphp
                    <a href="{{ route('keuangan.faktur.iklan.stok-opname') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Stok Opname Faktur</span>
                    </a>

                    @php $isSub = request()->routeIs('laporan.aging-piutang*'); @endphp
                    <a href="{{ route('laporan.aging-piutang') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Umur Piutang (Aging)</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-md {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Aging</span>
                    </a>

                    <!-- 2. TABEL FAKTUR SIRKULASI -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-sky-200/70 uppercase">2. Tabel Sirkulasi</div>

                    @php $isSub = request()->routeIs('keuangan.faktur.sirkulasi.index'); @endphp
                    <a href="{{ route('keuangan.faktur.sirkulasi.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Sirkulasi Keseluruhan</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-md {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Sirkulasi</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.sirkulasi.susulan'); @endphp
                    <a href="{{ route('keuangan.faktur.sirkulasi.susulan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Sirkulasi Susulan</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.sirkulasi.rekap*'); @endphp
                    <a href="{{ route('keuangan.faktur.sirkulasi.rekap') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Rekap Sirkulasi</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.sirkulasi.insidentil'); @endphp
                    <a href="{{ route('keuangan.faktur.sirkulasi.insidentil') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Faktur Insidentil</span>
                    </a>

                    <!-- 3. PENAGIHAN & OPERASIONAL -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-sky-200/70 uppercase">3. Penagihan & Terbit</div>

                    @php $isSub = request()->routeIs('keuangan.inkaso.*'); @endphp
                    <a href="{{ route('keuangan.inkaso.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Penugasan Inkaso</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-md {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Inkaso</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.create'); @endphp
                    <a href="{{ route('keuangan.faktur.create') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Terbitkan Faktur Baru</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-md {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-emerald-400/30 text-emerald-200' }}">+ Baru</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.fungsi.*'); @endphp
                    <a href="{{ route('keuangan.faktur.fungsi.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Fungsi &amp; Sinkronisasi</span>
                    </a>
                </div>
            </div>

            <!-- 3. Divisi Anggaran -->
            @php
                $isAnggaranOpen = request()->routeIs('keuangan.anggaran.*') 
                               || request()->routeIs('keuangan.kas.*') 
                               || request()->routeIs('keuangan.laporan');
            @endphp
            <div x-data="{ open: {{ $isAnggaranOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebar(), open = true) : (open = !open)" 
                        :title="sidebarCollapsed ? 'Divisi Anggaran' : ''"
                        class="group w-full flex items-center px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-150 cursor-pointer {{ $isAnggaranOpen ? 'bg-white/15 text-white font-bold border border-white/20 shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 border border-transparent' }}"
                        :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isAnggaranOpen ? 'text-white' : 'text-amber-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Divisi Anggaran</span>
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex items-center gap-1.5 shrink-0">
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $isAnggaranOpen ? 'text-white' : 'text-sky-200/70 group-hover:text-white' }}" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <!-- Submenu Divisi Anggaran -->
                <div x-cloak 
                     x-show="open && !sidebarCollapsed" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-3 pr-1 pt-1 pb-1 space-y-0.5 border-l border-white/10 ml-3.5 my-1">
                    
                    @php $isSub = request()->routeIs('keuangan.dashboard'); @endphp
                    <a href="{{ route('keuangan.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Overview Anggaran</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.alur'); @endphp
                    <a href="{{ route('keuangan.alur') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Panduan Alur &amp; SOP</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">SOP</span>
                    </a>

                    <!-- PENGELUARAN & ANGGARAN -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-sky-200/70 uppercase">Pengajuan Anggaran</div>

                    @php $isSub = request()->routeIs('keuangan.anggaran.index'); @endphp
                    <a href="{{ route('keuangan.anggaran.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Pengajuan Anggaran</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-md {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Daftar</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.anggaran.create'); @endphp
                    <a href="{{ route('keuangan.anggaran.create') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Buat Pengajuan Baru</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-md {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-emerald-400/30 text-emerald-200' }}">+ Baru</span>
                    </a>

                    <!-- PERBENDAHARAAN & KAS -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-sky-200/70 uppercase">Kas &amp; Perbendaharaan</div>

                    @php $isSub = request()->routeIs('keuangan.kas.index'); @endphp
                    <a href="{{ route('keuangan.kas.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Buku Kas (Treasury)</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.kas.harian'); @endphp
                    <a href="{{ route('keuangan.kas.harian') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Kas Harian</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.kas.bulanan'); @endphp
                    <a href="{{ route('keuangan.kas.bulanan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Kas Bulanan</span>
                    </a>

                    <!-- LAPORAN -->
                    <div class="pt-2 pb-0.5 px-2 text-[9px] font-bold tracking-wider text-sky-200/70 uppercase">Laporan Keuangan</div>

                    @php $isSub = request()->routeIs('keuangan.laporan'); @endphp
                    <a href="{{ route('keuangan.laporan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-medium' }}">
                        <span class="truncate">Laporan Keuangan Terpadu</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Rekap</span>
                    </a>
                </div>
            </div>

            <!-- 4. Divisi Accounting -->
            @php
                $isAccountingOpen = request()->routeIs('accounting.*');
            @endphp
            <div x-data="{ open: {{ $isAccountingOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebar(), open = true) : (open = !open)" 
                        :title="sidebarCollapsed ? 'Divisi Accounting' : ''"
                        class="group w-full flex items-center px-3 py-2 text-sm font-semibold rounded-xl transition-all duration-150 cursor-pointer {{ $isAccountingOpen ? 'bg-white/15 text-white font-bold border border-white/20 shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 border border-transparent' }}"
                        :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isAccountingOpen ? 'text-white' : 'text-sky-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <rect x="4" y="3" width="16" height="18" rx="2.5" />
                            <line x1="8" y1="7" x2="16" y2="7" stroke-width="1.8" stroke-linecap="round" />
                            <circle cx="8" cy="11.5" r="0.9" fill="currentColor" />
                            <circle cx="12" cy="11.5" r="0.9" fill="currentColor" />
                            <circle cx="16" cy="11.5" r="0.9" fill="currentColor" />
                            <circle cx="8" cy="15.5" r="0.9" fill="currentColor" />
                            <circle cx="12" cy="15.5" r="0.9" fill="currentColor" />
                            <circle cx="16" cy="15.5" r="0.9" fill="currentColor" />
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-sm">Divisi Accounting</span>
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex items-center gap-1.5 shrink-0">
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $isAccountingOpen ? 'text-white' : 'text-sky-200/70 group-hover:text-white' }}" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <!-- Submenu 8 Kelompok Terpadu Sesuai Standar Divisi Iklan -->
                <div x-cloak 
                     x-show="open && !sidebarCollapsed" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-3 pr-1 pt-1 pb-1 space-y-0.5 border-l border-white/10 ml-3.5 my-1">
                    
                    @php $isSub = request()->routeIs('accounting.dashboard'); @endphp
                    <a href="{{ route('accounting.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Dashboard</span>
                    </a>

                    <!-- 1. KARTU IKLAN -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Kartu Iklan</div>

                    @php $isSub = request()->routeIs('accounting.iklan.hitung-penjualan*'); @endphp
                    <a href="{{ route('accounting.iklan.hitung-penjualan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Hitung Penjualan</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.iklan.hitung-pembayaran*'); @endphp
                    <a href="{{ route('accounting.iklan.hitung-pembayaran') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Hitung Pembayaran</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.iklan.koreksi*'); @endphp
                    <a href="{{ route('accounting.iklan.koreksi') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Koreksi Iklan</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">CN</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.iklan.buka-data*'); @endphp
                    <a href="{{ route('accounting.iklan.buka-data') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Buka Data</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.iklan.belanja*'); @endphp
                    <a href="{{ route('accounting.iklan.belanja') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Belanja Pemasang</span>
                    </a>

                    <!-- 2. KARTU SIRKULASI -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Kartu Sirkulasi</div>

                    @php $isSub = request()->routeIs('accounting.sirkulasi.saldo-awal*'); @endphp
                    <a href="{{ route('accounting.sirkulasi.saldo-awal') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Saldo Awal</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.sirkulasi.hitung-penjualan*'); @endphp
                    <a href="{{ route('accounting.sirkulasi.hitung-penjualan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Hitung Penjualan</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.sirkulasi.hitung-pembayaran*'); @endphp
                    <a href="{{ route('accounting.sirkulasi.hitung-pembayaran') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Hitung Pembayaran</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.sirkulasi.koreksi*'); @endphp
                    <a href="{{ route('accounting.sirkulasi.koreksi') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Koreksi Sirkulasi</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Retur</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.sirkulasi.buka-data*'); @endphp
                    <a href="{{ route('accounting.sirkulasi.buka-data') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Buka Data</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.tabel.agen*'); @endphp
                    <a href="{{ route('sirkulasi.tabel.agen') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Master Data Agen</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-amber-400/20 text-amber-200' }}">Kelola</span>
                    </a>

                    <!-- 3. CETAK KARTU -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Cetak Kartu</div>

                    @php $isSub = request()->routeIs('accounting.cetak.iklan*'); @endphp
                    <a href="{{ route('accounting.cetak.iklan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Laporan Iklan</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.cetak.sirkulasi*'); @endphp
                    <a href="{{ route('accounting.cetak.sirkulasi') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Laporan Sirkulasi</span>
                    </a>

                    <!-- 4. KAS, BANK & BEBAN -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Beban & Kas</div>

                    @php $isSub = request()->routeIs('accounting.beban.verifikasi*'); @endphp
                    <a href="{{ route('accounting.beban.verifikasi') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Verifikasi Beban</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.beban.mutasi-bank*'); @endphp
                    <a href="{{ route('accounting.beban.mutasi-bank') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Mutasi Bank</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.beban.akrual*'); @endphp
                    <a href="{{ route('accounting.beban.akrual') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Beban Akrual</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.beban.aset-tetap*'); @endphp
                    <a href="{{ route('accounting.beban.aset-tetap') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Aset Tetap</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.beban.anggaran*'); @endphp
                    <a href="{{ route('accounting.beban.anggaran') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Anggaran</span>
                    </a>

                    <!-- 5. JURNAL & BUKU BESAR -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Jurnal & Buku Besar</div>

                    @php $isSub = request()->routeIs('accounting.gl.jurnal*'); @endphp
                    <a href="{{ route('accounting.gl.jurnal') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Jurnal Umum</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.gl.memorial*'); @endphp
                    <a href="{{ route('accounting.gl.memorial') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Jurnal Memorial</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.gl.buku-besar*'); @endphp
                    <a href="{{ route('accounting.gl.buku-besar') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Buku Besar</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.gl.neraca-lajur*'); @endphp
                    <a href="{{ route('accounting.gl.neraca-lajur') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Neraca Lajur</span>
                    </a>

                    <!-- 6. LAPORAN KEUANGAN -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Laporan Keuangan</div>

                    @php $isSub = request()->routeIs('accounting.laporan.laba-rugi*'); @endphp
                    <a href="{{ route('accounting.laporan.laba-rugi') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Laba Rugi</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.laporan.neraca*'); @endphp
                    <a href="{{ route('accounting.laporan.neraca') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Neraca</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.laporan.arus-kas*'); @endphp
                    <a href="{{ route('accounting.laporan.arus-kas') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Arus Kas</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.laporan.ekuitas*'); @endphp
                    <a href="{{ route('accounting.laporan.ekuitas') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Perubahan Ekuitas</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.laporan.rincian-beban*'); @endphp
                    <a href="{{ route('accounting.laporan.rincian-beban') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Rincian Beban</span>
                    </a>

                    <!-- 7. PENGATURAN & MASTER -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Pengaturan</div>

                    @php $isSub = request()->routeIs('accounting.pengaturan.coa*'); @endphp
                    <a href="{{ route('accounting.pengaturan.coa') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">No. Perkiraan</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.pengaturan.tutup-buku*'); @endphp
                    <a href="{{ route('accounting.pengaturan.tutup-buku') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Tutup Buku</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.pengaturan.audit-log*'); @endphp
                    <a href="{{ route('accounting.pengaturan.audit-log') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Log Aktivitas</span>
                    </a>
                </div>
            </div>

            <!-- 5. Divisi Sirkulasi -->
            @php
                $isSirkulasiOpen = request()->routeIs('sirkulasi.*');
            @endphp
            <div x-data="{ open: {{ $isSirkulasiOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebar(), open = true) : (open = !open)" 
                        :title="sidebarCollapsed ? 'Divisi Sirkulasi' : ''"
                        class="group w-full flex items-center px-3 py-2 text-sm font-semibold rounded-xl transition-all duration-150 cursor-pointer {{ $isSirkulasiOpen ? 'bg-white/15 text-white font-bold border border-white/20 shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 border border-transparent' }}"
                        :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSirkulasiOpen ? 'text-white' : 'text-sky-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5" />
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-sm">Divisi Sirkulasi</span>
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex items-center gap-1.5 shrink-0">
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $isSirkulasiOpen ? 'text-white' : 'text-sky-200/70 group-hover:text-white' }}" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <!-- Submenu Flat & Clean (Standar Divisi Iklan) -->
                <div x-cloak 
                     x-show="open && !sidebarCollapsed" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-3 pr-1 pt-1 pb-1 space-y-0.5 border-l border-white/10 ml-3.5 my-1">
                    
                    <!-- 1. DASHBOARD -->
                    <div class="pt-1 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Dashboard</div>

                    @php $isSub = request()->routeIs('sirkulasi.dashboard'); @endphp
                    <a href="{{ route('sirkulasi.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Ringkasan Sirkulasi</span>
                    </a>

                    <!-- 2. OPLAH & DISTRIBUSI -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Oplah &amp; Distribusi</div>

                    @php $isSub = request()->routeIs('sirkulasi.jatah.edit*'); @endphp
                    <a href="{{ route('sirkulasi.jatah.edit') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Input Oplah Harian</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.jatah.copy*'); @endphp
                    <a href="{{ route('sirkulasi.jatah.copy') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Salin Oplah Harian</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.jatah.tutup*'); @endphp
                    <a href="{{ route('sirkulasi.jatah.tutup') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Tutup Buku Harian</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.jatah.hitung*'); @endphp
                    <a href="{{ route('sirkulasi.jatah.hitung') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Kalkulasi Penjualan</span>
                    </a>

                    <!-- 3. LAPORAN & CETAK -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Laporan &amp; Cetak</div>

                    @php $isSub = request()->routeIs('sirkulasi.cetak.pemasaran*'); @endphp
                    <a href="{{ route('sirkulasi.cetak.pemasaran') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Laporan Pemasaran</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.cetak.penjualan*'); @endphp
                    <a href="{{ route('sirkulasi.cetak.penjualan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Laporan Penjualan</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.cetak.nota-distribusi*'); @endphp
                    <a href="{{ route('sirkulasi.cetak.nota-distribusi') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Nota Kirim Percetakan</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.cetak.alamat-agen*'); @endphp
                    <a href="{{ route('sirkulasi.cetak.alamat-agen') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Direktori Alamat Agen</span>
                    </a>

                    <!-- 4. MASTER & PENGATURAN -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Master &amp; Pengaturan</div>

                    @php $isSub = request()->routeIs('sirkulasi.tabel.agen*'); @endphp
                    <a href="{{ route('sirkulasi.tabel.agen') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Data Agen</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.tabel.jenis-agen'); @endphp
                    <a href="{{ route('sirkulasi.tabel.jenis-agen') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Klasifikasi Agen</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.tabel.wilayah'); @endphp
                    <a href="{{ route('sirkulasi.tabel.wilayah') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Wilayah Pemasaran</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.tabel.pengepakan'); @endphp
                    <a href="{{ route('sirkulasi.tabel.pengepakan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Standar Pengepakan</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.pengaturan.*'); @endphp
                    <a href="{{ route('sirkulasi.pengaturan.index') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Tarif &amp; Pengaturan</span>
                    </a>
                </div>
            </div>

            <!-- 6. Divisi Kasir -->
            @php
                $isKasirOpen = request()->routeIs('kasir.*');
            @endphp
            <div x-data="{ open: {{ $isKasirOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                <button type="button" 
                        @click="sidebarCollapsed ? (toggleSidebar(), open = true) : (open = !open)" 
                        :title="sidebarCollapsed ? 'Divisi Kasir' : ''"
                        class="group w-full flex items-center px-3 py-2 text-sm font-semibold rounded-xl transition-all duration-150 cursor-pointer {{ $isKasirOpen ? 'bg-white/15 text-white font-bold border border-white/20 shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 border border-transparent' }}"
                        :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isKasirOpen ? 'text-white' : 'text-sky-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <rect x="3" y="4.5" width="18" height="15" rx="3" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h2m4 0h4" />
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-sm">Divisi Kasir</span>
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex items-center gap-1.5 shrink-0">
                        <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $isKasirOpen ? 'text-white' : 'text-sky-200/70 group-hover:text-white' }}" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <!-- Submenu Flat & Clean (Standar Divisi Iklan) -->
                <div x-cloak 
                     x-show="open && !sidebarCollapsed" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-3 pr-1 pt-1 pb-1 space-y-0.5 border-l border-white/10 ml-3.5 my-1">

                    <!-- 1. DASHBOARD -->
                    <div class="pt-1 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Dashboard</div>

                    @php $isSub = request()->routeIs('kasir.dashboard'); @endphp
                    <a href="{{ route('kasir.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Overview Kasir</span>
                    </a>

                    <!-- 2. KAS MASUK -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Kas Masuk</div>

                    @php $isSub = request()->routeIs('kasir.penerimaan.iklan'); @endphp
                    <a href="{{ route('kasir.penerimaan.iklan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Setoran Iklan</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.penerimaan.sirkulasi'); @endphp
                    <a href="{{ route('kasir.penerimaan.sirkulasi') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Setoran Sirkulasi</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.penerimaan.koran-sisa') || request()->routeIs('kasir.penerimaan.tunai'); @endphp
                    <a href="{{ route('kasir.penerimaan.koran-sisa') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Koran Sisa &amp; Eceran</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.penerimaan.bank'); @endphp
                    <a href="{{ route('kasir.penerimaan.bank') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Bank &amp; Jasa Giro</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.penerimaan.langganan'); @endphp
                    <a href="{{ route('kasir.penerimaan.langganan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Piutang Langganan</span>
                    </a>

                    <!-- 3. KAS KELUAR -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Kas Keluar</div>

                    @php $isSub = request()->routeIs('kasir.pengeluaran.realisasi-bp*'); @endphp
                    <a href="{{ route('kasir.pengeluaran.realisasi-bp') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Realisasi BP</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.pengeluaran.komisi*'); @endphp
                    <a href="{{ route('kasir.pengeluaran.komisi') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Komisi &amp; Insentif</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.setoran-bank*'); @endphp
                    <a href="{{ route('kasir.setoran-bank') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Setoran ke Bank</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.pengeluaran.kasbon*') || request()->routeIs('kasir.kasbon*'); @endphp
                    <a href="{{ route('kasir.pengeluaran.kasbon') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Kasbon Operasional</span>
                    </a>

                    <!-- 4. LAPORAN KAS -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Laporan Kas</div>

                    @php $isSub = request()->routeIs('kasir.laporan.rekap*'); @endphp
                    <a href="{{ route('kasir.laporan.rekap') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Rekap Kas</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.laporan.mutasi-bank*'); @endphp
                    <a href="{{ route('kasir.laporan.mutasi-bank') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Mutasi Bank</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.laporan.perkiraan*'); @endphp
                    <a href="{{ route('kasir.laporan.perkiraan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Mutasi Perkiraan</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.laporan.posisi-kas*'); @endphp
                    <a href="{{ route('kasir.laporan.posisi-kas') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Posisi Saldo Kas</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.laporan.kasbon*'); @endphp
                    <a href="{{ route('kasir.laporan.kasbon') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Monitoring Kasbon</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.laporan-harian*'); @endphp
                    <a href="{{ route('kasir.laporan-harian') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Kas Harian</span>
                    </a>

                    <!-- 5. UTILITAS KAS -->
                    <div class="pt-2.5 pb-1 px-2 text-[10px] font-bold tracking-wider text-sky-200/80 uppercase">Utilitas Kas</div>

                    @php $isSub = request()->routeIs('kasir.utilitas.closing*'); @endphp
                    <a href="{{ route('kasir.utilitas.closing') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Tutup Kas Harian</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.buku-kas*'); @endphp
                    <a href="{{ route('kasir.buku-kas') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Buku Kas</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.utilitas.koreksi*'); @endphp
                    <a href="{{ route('kasir.utilitas.koreksi') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Koreksi Transaksi</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.utilitas.cetak-ulang*'); @endphp
                    <a href="{{ route('kasir.utilitas.cetak-ulang') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Cetak Ulang</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.riwayat-cetak*'); @endphp
                    <a href="{{ route('kasir.riwayat-cetak') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Riwayat Cetak</span>
                    </a>

                    @php $isSub = request()->routeIs('kasir.utilitas.buka-kas*'); @endphp
                    <a href="{{ route('kasir.utilitas.buka-kas') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-xs' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}">
                        <span class="truncate">Buka Kembali Kas</span>
                    </a>
                </div>
            </div>

            <!-- Divider Label Tata Kelola Sistem -->
            <div x-show="!sidebarCollapsed" class="pt-4 pb-1 px-3">
                <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                    Tata Kelola Sistem
                </span>
            </div>
            <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

            <!-- 1. Pengaturan Sistem Terpusat -->
            <div>
                @php $isConfigActive = request()->routeIs('admin.pengaturan.*'); @endphp
                <a href="{{ route('admin.pengaturan.index') }}" 
                   :title="sidebarCollapsed ? 'Pengaturan Sistem' : ''"
                   class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isConfigActive ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                   :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                    <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isConfigActive ? 'text-[#0E527B]' : 'text-violet-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Pengaturan Sistem</span>
                    </div>
                </a>
            </div>

            <!-- 2. Pemantauan Kesehatan Sistem -->
            <div>
                @php $isMonitorActive = request()->routeIs('admin.sistem.*'); @endphp
                <a href="{{ route('admin.sistem.index') }}" 
                   :title="sidebarCollapsed ? 'Kesehatan Sistem & Log' : ''"
                   class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isMonitorActive ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                   :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                    <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isMonitorActive ? 'text-[#0E527B]' : 'text-emerald-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Kesehatan Sistem &amp; Log</span>
                    </div>
                </a>
            </div>

            <!-- 3. Cadangan Data (Backup) -->
            <div>
                @php $isBackupsActive = request()->routeIs('admin.backups.*'); @endphp
                <a href="{{ route('admin.backups.index') }}" 
                   :title="sidebarCollapsed ? 'Cadangan Data (Backup)' : ''"
                   class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isBackupsActive ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                   :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                    <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isBackupsActive ? 'text-[#0E527B]' : 'text-cyan-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7zm0 5h16" />
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Cadangan Data (Backup)</span>
                    </div>
                </a>
            </div>

            <!-- Divider Label Keamanan & Akses -->
            <div x-show="!sidebarCollapsed" class="pt-4 pb-1 px-3">
                <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                    Keamanan &amp; Akses
                </span>
            </div>
            <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

            <!-- 4. Menu Kelola Pengguna & Divisi -->
            <div>
                @php $isUsersActive = request()->routeIs('admin.users.*'); @endphp
                <a href="{{ route('admin.users.index') }}" 
                   :title="sidebarCollapsed ? 'Kelola Tim & Divisi' : ''"
                   class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isUsersActive ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                   :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                    <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isUsersActive ? 'text-[#0E527B]' : 'text-sky-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Kelola Tim &amp; Divisi</span>
                    </div>
                </a>
            </div>

            <!-- 5. Audit Log Keamanan -->
            <div>
                @php $isLogsActive = request()->routeIs('admin.activity-logs.*'); @endphp
                <a href="{{ route('admin.activity-logs.index') }}" 
                   :title="sidebarCollapsed ? 'Audit Log Keamanan' : ''"
                   class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isLogsActive ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                   :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                    <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isLogsActive ? 'text-[#0E527B]' : 'text-amber-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Audit Log Keamanan</span>
                    </div>
                </a>
            </div>

            <!-- 6. Profil Administrator -->
            <div>
                @php $isProfileActive = request()->routeIs('profile.*'); @endphp
                <a href="{{ route('profile.edit') }}" 
                   :title="sidebarCollapsed ? 'Profil Pengguna' : ''"
                   class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isProfileActive ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                   :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                    <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isProfileActive ? 'text-[#0E527B]' : 'text-emerald-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
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
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isIklanDash ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isIklanDash ? 'text-[#0E527B]' : 'text-sky-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="3.5" y="3.5" width="6.5" height="6.5" rx="2" />
                                <rect x="14" y="3.5" width="6.5" height="6.5" rx="2" />
                                <rect x="14" y="14" width="6.5" height="6.5" rx="2" />
                                <rect x="3.5" y="14" width="6.5" height="6.5" rx="2" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Dashboard Iklan</span>
                        </div>
                    </a>
                </div>

                <!-- 1. TRANSAKSI IKLAN -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Transaksi Iklan
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    <!-- Iklan Koran -->
                    @php $isSub = request()->routeIs('transaksikoran.*'); @endphp
                    <a href="{{ route('transaksikoran.index') }}" 
                       :title="sidebarCollapsed ? 'Iklan Koran' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Iklan Koran</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Koran</span>
                    </a>

                    <!-- Iklan Online -->
                    @php $isSub = request()->routeIs('transaksionline.*'); @endphp
                    <a href="{{ route('transaksionline.index') }}" 
                       :title="sidebarCollapsed ? 'Iklan Online' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-.778.099-1.533.284-2.253" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Iklan Online</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Online</span>
                    </a>

                    <!-- Iklan TV -->
                    @php $isSub = request()->routeIs('transaksipriangan.*'); @endphp
                    <a href="{{ route('transaksipriangan.index') }}" 
                       :title="sidebarCollapsed ? 'Iklan TV' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="3" y="6" width="18" height="13" rx="2.5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 2.5l4 3.5 4-3.5" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Iklan TV</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">TV</span>
                    </a>
                </div>

                <!-- 2. OPERASIONAL & KASIR -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Operasional
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    <!-- Dummy Pracetak -->
                    @php $isSub = request()->routeIs('iklan.rekap-pracetak.*'); @endphp
                    <a href="{{ route('iklan.rekap-pracetak.index') }}" 
                       :title="sidebarCollapsed ? 'Dummy Pracetak' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Dummy Pracetak</span>
                        </div>
                    </a>
                </div>

                <!-- 3. LAPORAN IKLAN -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Laporan Iklan
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    <!-- Laporan Pemuatan -->
                    @php $isLap = request()->routeIs('laporan.index'); @endphp
                    <a href="{{ route('laporan.index') }}" 
                       :title="sidebarCollapsed ? 'Laporan Pemuatan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isLap ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isLap ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Laporan Pemuatan</span>
                        </div>
                    </a>

                    <!-- Laporan Penjualan -->
                    @php $isSub = request()->routeIs('laporan.rekap-sales'); @endphp
                    <a href="{{ route('laporan.rekap-sales') }}" 
                       :title="sidebarCollapsed ? 'Laporan Penjualan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Laporan Penjualan</span>
                        </div>
                    </a>

                    <!-- Klasifikasi Iklan -->
                    @php $isSub = request()->routeIs('laporan.klasifikasi'); @endphp
                    <a href="{{ route('laporan.klasifikasi') }}" 
                       :title="sidebarCollapsed ? 'Klasifikasi Iklan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Klasifikasi Iklan</span>
                        </div>
                    </a>

                    <!-- Insentif Sales -->
                    @php $isSub = request()->routeIs('laporan.insentif-ae'); @endphp
                    <a href="{{ route('laporan.insentif-ae') }}" 
                       :title="sidebarCollapsed ? 'Insentif Sales' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v10.5m0-10.5h19.5m0 0v10.5m0-10.5A.75.75 0 0021 5.25V4.5m0 10.5a.75.75 0 00.75-.75V6m-.75 9h-19.5" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Insentif Sales</span>
                        </div>
                    </a>

                    <!-- Laporan Batal -->
                    @php $isSub = request()->routeIs('laporan.pembatalan'); @endphp
                    <a href="{{ route('laporan.pembatalan') }}" 
                       :title="sidebarCollapsed ? 'Laporan Batal' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Laporan Batal</span>
                        </div>
                    </a>
                </div>

                <!-- 4. MASTER DATA -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Master Data
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    <!-- Data Pemasang -->
                    @php $isSub = request()->routeIs('pelanggan.*'); @endphp
                    <a href="{{ route('pelanggan.index') }}" 
                       :title="sidebarCollapsed ? 'Data Pemasang' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Data Pemasang</span>
                        </div>
                    </a>

                    <!-- Data Sales -->
                    @php $isAe = request()->routeIs('sales-ae.*'); @endphp
                    <a href="{{ route('sales-ae.index') }}" 
                       :title="sidebarCollapsed ? 'Data Sales' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAe ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isAe ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 014.5 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Data Sales</span>
                        </div>
                    </a>

                    <!-- Jenis Iklan -->
                    @php $isSub = request()->routeIs('jenis-iklan.*'); @endphp
                    <a href="{{ route('jenis-iklan.index') }}" 
                       :title="sidebarCollapsed ? 'Jenis Iklan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="3" y="3" width="18" height="18" rx="2" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9h18M9 21V9" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Jenis Iklan</span>
                        </div>
                    </a>

                    <!-- Kategori Media -->
                    @php $isSub = request()->routeIs('kategori-media.*'); @endphp
                    <a href="{{ route('kategori-media.index') }}" 
                       :title="sidebarCollapsed ? 'Kategori Media' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Kategori Media</span>
                        </div>
                    </a>

                    <!-- Kop Kota -->
                    @php $isSub = request()->routeIs('kop-kota.*'); @endphp
                    <a href="{{ route('kop-kota.index') }}" 
                       :title="sidebarCollapsed ? 'Kop Kota' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Kop Kota</span>
                        </div>
                    </a>

                    <!-- Kop Iklan -->
                    @php $isSub = request()->routeIs('kop-iklan.*'); @endphp
                    <a href="{{ route('kop-iklan.index') }}" 
                       :title="sidebarCollapsed ? 'Kop Iklan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Kop Iklan</span>
                        </div>
                    </a>
                </div>

                <!-- 5. PENGATURAN -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Pengaturan
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    <!-- Pengaturan Tarif -->
                    @php $isSub = request()->routeIs('iklan.pengaturan.*'); @endphp
                    <a href="{{ route('iklan.pengaturan.index') }}" 
                       :title="sidebarCollapsed ? 'Pengaturan Tarif' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Pengaturan Tarif</span>
                        </div>
                    </a>
                </div>
            @endif

            <!-- ============================================== -->
            <!-- DIVISI FAKTUR (Standalone Role Menu)           -->
            <!-- ============================================== -->
            @if(!$isIklan && ($isFaktur || ($isKeuangan && !$isAnggaran)))
                @php $isFakturDash = request()->routeIs('keuangan.dashboard') && !request()->routeIs('keuangan.anggaran.*') && !request()->routeIs('keuangan.kas.*'); @endphp
                <div>
                    <a href="{{ route('keuangan.dashboard') }}" 
                       :title="sidebarCollapsed ? 'Dashboard Faktur' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isFakturDash ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isFakturDash ? 'text-[#0E527B]' : 'text-cyan-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Dashboard Faktur</span>
                        </div>
                    </a>
                </div>

                <!-- Tombol Cepat Terbitkan Faktur -->
                <div x-show="!sidebarCollapsed" class="px-2 pb-2 pt-2">
                    <a href="{{ route('keuangan.faktur.create') }}" 
                       class="w-full flex items-center justify-center gap-1.5 py-1.5 px-3 text-center text-xs font-bold rounded-xl bg-white/15 hover:bg-white hover:text-[#0E527B] text-white transition-all duration-150 border border-white/20 shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Terbitkan Faktur Baru</span>
                    </a>
                </div>

                <!-- Panduan SOP -->
                @php $isAlur = request()->routeIs('keuangan.alur'); @endphp
                <div class="pt-0.5">
                    <a href="{{ route('keuangan.alur') }}" 
                       :title="sidebarCollapsed ? 'Panduan SOP Faktur' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAlur ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isAlur ? 'text-[#0E527B]' : 'text-sky-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Panduan SOP Faktur</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isAlur ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">SOP</span>
                    </a>
                </div>

                <!-- 1. TABEL FAKTUR IKLAN -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            1. Tabel Faktur Iklan
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('keuangan.faktur.iklan.index') || (request()->routeIs('keuangan.faktur.index') && request('kategori') !== 'sirkulasi'); @endphp
                    <a href="{{ route('keuangan.faktur.iklan.index') }}" 
                       :title="sidebarCollapsed ? 'Faktur Keseluruhan' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Faktur Keseluruhan</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Iklan</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.iklan.rekap*'); @endphp
                    <a href="{{ route('keuangan.faktur.iklan.rekap') }}" 
                       :title="sidebarCollapsed ? 'Rekap Tgl / Bulan' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Rekap Tgl / Bulan</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.iklan.susulan'); @endphp
                    <a href="{{ route('keuangan.faktur.iklan.susulan') }}" 
                       :title="sidebarCollapsed ? 'Faktur Susulan' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-amber-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Faktur Susulan</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Susulan</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.iklan.tunai'); @endphp
                    <a href="{{ route('keuangan.faktur.iklan.tunai') }}" 
                       :title="sidebarCollapsed ? 'Rekap Iklan Tunai' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-emerald-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v8.25m0 0a60.063 60.063 0 0015.797 2.101c.727.198 1.453-.342 1.453-1.096V14.25m-17.25 0h17.25" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Rekap Iklan Tunai</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.iklan.piutang*'); @endphp
                    <a href="{{ route('keuangan.faktur.iklan.piutang') }}" 
                       :title="sidebarCollapsed ? 'Rekap Iklan Piutang' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-rose-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Rekap Iklan Piutang</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Piutang</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.iklan.stopper-bonus'); @endphp
                    <a href="{{ route('keuangan.faktur.iklan.stopper-bonus') }}" 
                       :title="sidebarCollapsed ? 'Rekap Stopper & Bonus' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-teal-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H4.5a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Rekap Stopper / Bonus</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.iklan.stok-opname*'); @endphp
                    <a href="{{ route('keuangan.faktur.iklan.stok-opname') }}" 
                       :title="sidebarCollapsed ? 'Stok Opname Faktur' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-purple-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Stok Opname Faktur</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('laporan.aging-piutang*'); @endphp
                    <a href="{{ route('laporan.aging-piutang') }}" 
                       :title="sidebarCollapsed ? 'Umur Piutang (Aging)' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-rose-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Umur Piutang (Aging)</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Aging</span>
                    </a>
                </div>

                <!-- 2. TABEL FAKTUR SIRKULASI -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            2. Tabel Sirkulasi
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('keuangan.faktur.sirkulasi.index'); @endphp
                    <a href="{{ route('keuangan.faktur.sirkulasi.index') }}" 
                       :title="sidebarCollapsed ? 'Sirkulasi Keseluruhan' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-indigo-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Sirkulasi Keseluruhan</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Sirkulasi</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.sirkulasi.susulan'); @endphp
                    <a href="{{ route('keuangan.faktur.sirkulasi.susulan') }}" 
                       :title="sidebarCollapsed ? 'Sirkulasi Susulan' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-amber-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Sirkulasi Susulan</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.sirkulasi.rekap*'); @endphp
                    <a href="{{ route('keuangan.faktur.sirkulasi.rekap') }}" 
                       :title="sidebarCollapsed ? 'Rekap Sirkulasi' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Rekap Sirkulasi</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.sirkulasi.insidentil'); @endphp
                    <a href="{{ route('keuangan.faktur.sirkulasi.insidentil') }}" 
                       :title="sidebarCollapsed ? 'Faktur Insidentil' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-teal-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Faktur Insidentil</span>
                        </div>
                    </a>
                </div>

                <!-- 3. PENAGIHAN & UTILITAS -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            3. Penagihan &amp; Utilitas
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('keuangan.inkaso.*'); @endphp
                    <a href="{{ route('keuangan.inkaso.index') }}" 
                       :title="sidebarCollapsed ? 'Penugasan Inkaso' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-amber-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Penugasan Inkaso</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Inkaso</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.faktur.fungsi.*'); @endphp
                    <a href="{{ route('keuangan.faktur.fungsi.index') }}" 
                       :title="sidebarCollapsed ? 'Fungsi & Sinkronisasi' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-teal-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Fungsi &amp; Sinkronisasi</span>
                        </div>
                    </a>
                </div>
            @endif

            <!-- ============================================== -->
            <!-- DIVISI ANGGARAN (Standalone Role Menu)         -->
            <!-- ============================================== -->
            @if(!$isIklan && $isAnggaran)
                @php $isAnggaranDash = request()->routeIs('keuangan.dashboard') && !request()->routeIs('keuangan.faktur.*') && !request()->routeIs('keuangan.inkaso.*'); @endphp
                <div>
                    <a href="{{ route('keuangan.dashboard') }}" 
                       :title="sidebarCollapsed ? 'Dashboard Anggaran' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAnggaranDash ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isAnggaranDash ? 'text-[#0E527B]' : 'text-amber-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate font-semibold text-xs">Dashboard Anggaran</span>
                        </div>
                    </a>
                </div>

                <!-- Tombol Cepat Buat Pengajuan Anggaran -->
                <div x-show="!sidebarCollapsed" class="px-2 pb-2 pt-2">
                    <a href="{{ route('keuangan.anggaran.create') }}" 
                       class="w-full flex items-center justify-center gap-1.5 py-1.5 px-3 text-center text-xs font-bold rounded-xl bg-white/15 hover:bg-white hover:text-[#0E527B] text-white transition-all duration-150 border border-white/20 shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Buat Pengajuan Baru</span>
                    </a>
                </div>

                <!-- Panduan SOP Anggaran -->
                @php $isAlur = request()->routeIs('keuangan.alur'); @endphp
                <div class="pt-0.5">
                    <a href="{{ route('keuangan.alur') }}" 
                       :title="sidebarCollapsed ? 'Panduan SOP Anggaran' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAlur ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isAlur ? 'text-[#0E527B]' : 'text-sky-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Panduan SOP Anggaran</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isAlur ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">SOP</span>
                    </a>
                </div>

                <!-- 1. MODUL PENGAJUAN ANGGARAN -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Pengajuan Anggaran
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('keuangan.anggaran.index'); @endphp
                    <a href="{{ route('keuangan.anggaran.index') }}" 
                       :title="sidebarCollapsed ? 'Daftar Pengajuan' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-amber-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Daftar Pengajuan</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Daftar</span>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.anggaran.create'); @endphp
                    <a href="{{ route('keuangan.anggaran.create') }}" 
                       :title="sidebarCollapsed ? 'Buat Pengajuan Baru' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-emerald-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Buat Pengajuan Baru</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-emerald-400/30 text-emerald-200' }}">+ Baru</span>
                    </a>
                </div>

                <!-- 2. KAS & PERBENDAHARAAN -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Kas &amp; Perbendaharaan
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('keuangan.kas.index'); @endphp
                    <a href="{{ route('keuangan.kas.index') }}" 
                       :title="sidebarCollapsed ? 'Buku Kas Treasury' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-emerald-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Buku Kas (Treasury)</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.kas.harian'); @endphp
                    <a href="{{ route('keuangan.kas.harian') }}" 
                       :title="sidebarCollapsed ? 'Kas Harian' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Kas Harian</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('keuangan.kas.bulanan'); @endphp
                    <a href="{{ route('keuangan.kas.bulanan') }}" 
                       :title="sidebarCollapsed ? 'Kas Bulanan' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-indigo-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Kas Bulanan</span>
                        </div>
                    </a>
                </div>

                <!-- 3. LAPORAN KEUANGAN -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Laporan Keuangan
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('keuangan.laporan'); @endphp
                    <a href="{{ route('keuangan.laporan') }}" 
                       :title="sidebarCollapsed ? 'Laporan Keuangan Terpadu' : ''"
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-amber-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="3" y="4.5" width="18" height="15" rx="3.5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 13.5l3.5-3.5 3 3 3.5-4" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Laporan Keuangan Terpadu</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Rekap</span>
                    </a>
                </div>
            @endif

            @if(!$isIklan && $isAccounting)
                @php $isAccDash = request()->routeIs('accounting.dashboard'); @endphp
                <div>
                    <a href="{{ route('accounting.dashboard') }}" 
                       :title="sidebarCollapsed ? 'Dashboard Accounting' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAccDash ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isAccDash ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Dashboard</span>
                        </div>
                    </a>
                </div>

                <!-- 1. KARTU IKLAN -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Kartu Iklan
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('accounting.iklan.hitung-penjualan*'); @endphp
                    <a href="{{ route('accounting.iklan.hitung-penjualan') }}" 
                       :title="sidebarCollapsed ? 'Hitung Penjualan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Hitung Penjualan</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.iklan.hitung-pembayaran*'); @endphp
                    <a href="{{ route('accounting.iklan.hitung-pembayaran') }}" 
                       :title="sidebarCollapsed ? 'Hitung Pembayaran' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Hitung Pembayaran</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.iklan.koreksi*'); @endphp
                    <a href="{{ route('accounting.iklan.koreksi') }}" 
                       :title="sidebarCollapsed ? 'Koreksi Iklan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Koreksi Iklan</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">CN</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.iklan.buka-data*'); @endphp
                    <a href="{{ route('accounting.iklan.buka-data') }}" 
                       :title="sidebarCollapsed ? 'Buka Data' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Buka Data</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.iklan.belanja*'); @endphp
                    <a href="{{ route('accounting.iklan.belanja') }}" 
                       :title="sidebarCollapsed ? 'Belanja Pemasang' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Belanja Pemasang</span>
                        </div>
                    </a>
                </div>

                <!-- 2. KARTU SIRKULASI -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Kartu Sirkulasi
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('accounting.sirkulasi.saldo-awal*'); @endphp
                    <a href="{{ route('accounting.sirkulasi.saldo-awal') }}" 
                       :title="sidebarCollapsed ? 'Saldo Awal' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Saldo Awal</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.sirkulasi.hitung-penjualan*'); @endphp
                    <a href="{{ route('accounting.sirkulasi.hitung-penjualan') }}" 
                       :title="sidebarCollapsed ? 'Hitung Penjualan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Hitung Penjualan</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.sirkulasi.hitung-pembayaran*'); @endphp
                    <a href="{{ route('accounting.sirkulasi.hitung-pembayaran') }}" 
                       :title="sidebarCollapsed ? 'Hitung Pembayaran' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Hitung Pembayaran</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.sirkulasi.koreksi*'); @endphp
                    <a href="{{ route('accounting.sirkulasi.koreksi') }}" 
                       :title="sidebarCollapsed ? 'Koreksi Sirkulasi' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 15v-1a4 4 0 00-4-4H4m0 0l3-3m-3 3l3 3m5 4v1a3 3 0 003 3h4" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Koreksi Sirkulasi</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Retur</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.sirkulasi.buka-data*'); @endphp
                    <a href="{{ route('accounting.sirkulasi.buka-data') }}" 
                       :title="sidebarCollapsed ? 'Buka Data' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Buka Data</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.tabel.agen*'); @endphp
                    <a href="{{ route('sirkulasi.tabel.agen') }}" 
                       :title="sidebarCollapsed ? 'Master Data Agen' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-amber-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Master Data Agen</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-amber-400/20 text-amber-200' }}">Kelola</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.jatah.tutup*'); @endphp
                    <a href="{{ route('sirkulasi.jatah.tutup') }}" 
                       :title="sidebarCollapsed ? 'Otorisasi Cutoff Sirkulasi' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-amber-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Cutoff Sirkulasi</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-[#0E527B]' : 'bg-white/15 text-white' }}">Memo</span>
                    </a>
                </div>

                <!-- 3. CETAK KARTU -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Cetak Kartu
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('accounting.cetak.iklan*'); @endphp
                    <a href="{{ route('accounting.cetak.iklan') }}" 
                       :title="sidebarCollapsed ? 'Laporan Iklan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Laporan Iklan</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.cetak.sirkulasi*'); @endphp
                    <a href="{{ route('accounting.cetak.sirkulasi') }}" 
                       :title="sidebarCollapsed ? 'Laporan Sirkulasi' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Laporan Sirkulasi</span>
                        </div>
                    </a>
                </div>

                <!-- 4. KAS, BANK & BEBAN -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Beban & Kas
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('accounting.beban.verifikasi*'); @endphp
                    <a href="{{ route('accounting.beban.verifikasi') }}" 
                       :title="sidebarCollapsed ? 'Verifikasi Beban' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Verifikasi Beban</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.beban.mutasi-bank*'); @endphp
                    <a href="{{ route('accounting.beban.mutasi-bank') }}" 
                       :title="sidebarCollapsed ? 'Mutasi Bank' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Mutasi Bank</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.beban.akrual*'); @endphp
                    <a href="{{ route('accounting.beban.akrual') }}" 
                       :title="sidebarCollapsed ? 'Beban Akrual' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Beban Akrual</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.beban.aset-tetap*'); @endphp
                    <a href="{{ route('accounting.beban.aset-tetap') }}" 
                       :title="sidebarCollapsed ? 'Aset Tetap' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Aset Tetap</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.beban.anggaran*'); @endphp
                    <a href="{{ route('accounting.beban.anggaran') }}" 
                       :title="sidebarCollapsed ? 'Anggaran' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Anggaran</span>
                        </div>
                    </a>
                </div>

                <!-- 5. JURNAL & BUKU BESAR -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Jurnal & Buku Besar
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('accounting.gl.jurnal*'); @endphp
                    <a href="{{ route('accounting.gl.jurnal') }}" 
                       :title="sidebarCollapsed ? 'Jurnal Umum' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Jurnal Umum</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.gl.memorial*'); @endphp
                    <a href="{{ route('accounting.gl.memorial') }}" 
                       :title="sidebarCollapsed ? 'Jurnal Memorial' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Jurnal Memorial</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.gl.buku-besar*'); @endphp
                    <a href="{{ route('accounting.gl.buku-besar') }}" 
                       :title="sidebarCollapsed ? 'Buku Besar' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Buku Besar</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.gl.neraca-lajur*'); @endphp
                    <a href="{{ route('accounting.gl.neraca-lajur') }}" 
                       :title="sidebarCollapsed ? 'Neraca Lajur' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Neraca Lajur</span>
                        </div>
                    </a>
                </div>

                <!-- 6. LAPORAN KEUANGAN -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Laporan Keuangan
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('accounting.laporan.laba-rugi*'); @endphp
                    <a href="{{ route('accounting.laporan.laba-rugi') }}" 
                       :title="sidebarCollapsed ? 'Laba Rugi' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Laba Rugi</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.laporan.neraca*'); @endphp
                    <a href="{{ route('accounting.laporan.neraca') }}" 
                       :title="sidebarCollapsed ? 'Neraca' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Neraca</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.laporan.arus-kas*'); @endphp
                    <a href="{{ route('accounting.laporan.arus-kas') }}" 
                       :title="sidebarCollapsed ? 'Arus Kas' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Arus Kas</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.laporan.ekuitas*'); @endphp
                    <a href="{{ route('accounting.laporan.ekuitas') }}" 
                       :title="sidebarCollapsed ? 'Perubahan Ekuitas' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Perubahan Ekuitas</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.laporan.rincian-beban*'); @endphp
                    <a href="{{ route('accounting.laporan.rincian-beban') }}" 
                       :title="sidebarCollapsed ? 'Rincian Beban' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Rincian Beban</span>
                        </div>
                    </a>
                </div>

                <!-- 7. PENGATURAN -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Pengaturan
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('accounting.pengaturan.coa*'); @endphp
                    <a href="{{ route('accounting.pengaturan.coa') }}" 
                       :title="sidebarCollapsed ? 'No. Perkiraan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">No. Perkiraan</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.pengaturan.tutup-buku*'); @endphp
                    <a href="{{ route('accounting.pengaturan.tutup-buku') }}" 
                       :title="sidebarCollapsed ? 'Tutup Buku' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Tutup Buku</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.pengaturan.audit-log*'); @endphp
                    <a href="{{ route('accounting.pengaturan.audit-log') }}" 
                       :title="sidebarCollapsed ? 'Log Aktivitas' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Log Aktivitas</span>
                        </div>
                    </a>
                </div>
            @endif

            @if(!$isIklan && $isSirkulasi)
                @php $isSirDash = request()->routeIs('sirkulasi.dashboard'); @endphp
                <div>
                    <a href="{{ route('sirkulasi.dashboard') }}" 
                       :title="sidebarCollapsed ? 'Ringkasan Sirkulasi' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSirDash ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSirDash ? 'text-[#0E527B]' : 'text-sky-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="3.5" y="3.5" width="6.5" height="6.5" rx="2" />
                                <rect x="14" y="3.5" width="6.5" height="6.5" rx="2" />
                                <rect x="14" y="14" width="6.5" height="6.5" rx="2" />
                                <rect x="3.5" y="14" width="6.5" height="6.5" rx="2" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Ringkasan Sirkulasi</span>
                        </div>
                    </a>
                </div>

                <!-- 1. OPLAH & DISTRIBUSI -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Oplah &amp; Distribusi
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('sirkulasi.jatah.edit*'); @endphp
                    <a href="{{ route('sirkulasi.jatah.edit') }}" 
                       :title="sidebarCollapsed ? 'Input Oplah Harian' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Input Oplah Harian</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.jatah.copy*'); @endphp
                    <a href="{{ route('sirkulasi.jatah.copy') }}" 
                       :title="sidebarCollapsed ? 'Salin Oplah Harian' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Salin Oplah Harian</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.jatah.tutup*'); @endphp
                    <a href="{{ route('sirkulasi.jatah.tutup') }}" 
                       :title="sidebarCollapsed ? 'Tutup Buku Harian' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Tutup Buku Harian</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.jatah.hitung*'); @endphp
                    <a href="{{ route('sirkulasi.jatah.hitung') }}" 
                       :title="sidebarCollapsed ? 'Kalkulasi Penjualan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Kalkulasi Penjualan</span>
                        </div>
                    </a>
                </div>

                <!-- 2. LAPORAN & CETAK -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Laporan &amp; Cetak
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('sirkulasi.cetak.pemasaran*'); @endphp
                    <a href="{{ route('sirkulasi.cetak.pemasaran') }}" 
                       :title="sidebarCollapsed ? 'Laporan Pemasaran' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Laporan Pemasaran</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.cetak.penjualan*'); @endphp
                    <a href="{{ route('sirkulasi.cetak.penjualan') }}" 
                       :title="sidebarCollapsed ? 'Laporan Penjualan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v10.5m0-10.5h19.5m0 0v10.5m0-10.5A.75.75 0 0021 5.25V4.5m0 10.5a.75.75 0 00.75-.75V6m-.75 9h-19.5" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Laporan Penjualan</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.cetak.nota-distribusi*'); @endphp
                    <a href="{{ route('sirkulasi.cetak.nota-distribusi') }}" 
                       :title="sidebarCollapsed ? 'Nota Kirim Percetakan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Nota Kirim Percetakan</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.cetak.alamat-agen*'); @endphp
                    <a href="{{ route('sirkulasi.cetak.alamat-agen') }}" 
                       :title="sidebarCollapsed ? 'Direktori Alamat Agen' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Direktori Alamat Agen</span>
                        </div>
                    </a>
                </div>

                <!-- 3. MASTER & PENGATURAN -->
                <div class="space-y-0.5 pt-2 pb-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Master &amp; Pengaturan
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('sirkulasi.tabel.agen*'); @endphp
                    <a href="{{ route('sirkulasi.tabel.agen') }}" 
                       :title="sidebarCollapsed ? 'Data Agen' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Data Agen</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.tabel.jenis-agen'); @endphp
                    <a href="{{ route('sirkulasi.tabel.jenis-agen') }}" 
                       :title="sidebarCollapsed ? 'Klasifikasi Agen' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="3" y="3" width="18" height="18" rx="2" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9h18M9 21V9" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Klasifikasi Agen</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.tabel.wilayah'); @endphp
                    <a href="{{ route('sirkulasi.tabel.wilayah') }}" 
                       :title="sidebarCollapsed ? 'Wilayah Pemasaran' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Wilayah Pemasaran</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.tabel.pengepakan'); @endphp
                    <a href="{{ route('sirkulasi.tabel.pengepakan') }}" 
                       :title="sidebarCollapsed ? 'Standar Pengepakan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Standar Pengepakan</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.pengaturan.*'); @endphp
                    <a href="{{ route('sirkulasi.pengaturan.index') }}" 
                       :title="sidebarCollapsed ? 'Tarif & Pengaturan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Tarif &amp; Pengaturan</span>
                        </div>
                    </a>
                </div>
            @endif

            @if(!$isIklan && $isKasir)
                @php $isKasDash = request()->routeIs('kasir.dashboard'); @endphp
                <div>
                    <a href="{{ route('kasir.dashboard') }}" 
                       :title="sidebarCollapsed ? 'Dashboard Kasir' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isKasDash ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isKasDash ? 'text-[#0E527B]' : 'text-sky-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="3.5" y="3.5" width="6.5" height="6.5" rx="2" />
                                <rect x="14" y="3.5" width="6.5" height="6.5" rx="2" />
                                <rect x="14" y="14" width="6.5" height="6.5" rx="2" />
                                <rect x="3.5" y="14" width="6.5" height="6.5" rx="2" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Dashboard Kasir</span>
                        </div>
                    </a>
                </div>

                <!-- 1. KAS MASUK -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Kas Masuk
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('kasir.penerimaan.iklan'); @endphp
                    <a href="{{ route('kasir.penerimaan.iklan') }}" 
                       :title="sidebarCollapsed ? 'Setoran Iklan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Setoran Iklan</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('kasir.penerimaan.sirkulasi'); @endphp
                    <a href="{{ route('kasir.penerimaan.sirkulasi') }}" 
                       :title="sidebarCollapsed ? 'Setoran Sirkulasi' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Setoran Sirkulasi</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('kasir.penerimaan.koran-sisa') || request()->routeIs('kasir.penerimaan.tunai'); @endphp
                    <a href="{{ route('kasir.penerimaan.koran-sisa') }}" 
                       :title="sidebarCollapsed ? 'Koran Sisa & Eceran' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v8.25m0-8.25a60.074 60.074 0 0115.797-2.101c.727-.198 1.453.342 1.453 1.096V6m0 0h-.75a.75.75 0 01-.75-.75V4.5m0 0a60.074 60.074 0 00-15.797 2.101" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Koran Sisa &amp; Eceran</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('kasir.penerimaan.bank'); @endphp
                    <a href="{{ route('kasir.penerimaan.bank') }}" 
                       :title="sidebarCollapsed ? 'Bank & Jasa Giro' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Bank &amp; Jasa Giro</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('kasir.penerimaan.langganan'); @endphp
                    <a href="{{ route('kasir.penerimaan.langganan') }}" 
                       :title="sidebarCollapsed ? 'Piutang Langganan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Piutang Langganan</span>
                        </div>
                    </a>
                </div>

                <!-- 2. KAS KELUAR -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Kas Keluar
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('kasir.pengeluaran.realisasi-bp*'); @endphp
                    <a href="{{ route('kasir.pengeluaran.realisasi-bp') }}" 
                       :title="sidebarCollapsed ? 'Realisasi BP' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Realisasi BP</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('kasir.pengeluaran.komisi*'); @endphp
                    <a href="{{ route('kasir.pengeluaran.komisi') }}" 
                       :title="sidebarCollapsed ? 'Komisi & Insentif' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Komisi &amp; Insentif</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('kasir.setoran-bank*'); @endphp
                    <a href="{{ route('kasir.setoran-bank') }}" 
                       :title="sidebarCollapsed ? 'Setoran ke Bank' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Setoran ke Bank</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('kasir.pengeluaran.kasbon*') || request()->routeIs('kasir.kasbon*'); @endphp
                    <a href="{{ route('kasir.pengeluaran.kasbon') }}" 
                       :title="sidebarCollapsed ? 'Kasbon Operasional' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Kasbon Operasional</span>
                        </div>
                    </a>
                </div>

                <!-- 3. LAPORAN KAS -->
                <div class="space-y-0.5 pt-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Laporan Kas
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('kasir.laporan.rekap*'); @endphp
                    <a href="{{ route('kasir.laporan.rekap') }}" 
                       :title="sidebarCollapsed ? 'Rekap Kas' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Rekap Kas</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('kasir.laporan.mutasi-bank*'); @endphp
                    <a href="{{ route('kasir.laporan.mutasi-bank') }}" 
                       :title="sidebarCollapsed ? 'Mutasi Bank' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Mutasi Bank</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('kasir.laporan.perkiraan*'); @endphp
                    <a href="{{ route('kasir.laporan.perkiraan') }}" 
                       :title="sidebarCollapsed ? 'Mutasi Perkiraan' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Mutasi Perkiraan</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('kasir.laporan.posisi-kas*'); @endphp
                    <a href="{{ route('kasir.laporan.posisi-kas') }}" 
                       :title="sidebarCollapsed ? 'Posisi Saldo Kas' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v10.5m0-10.5h19.5m0 0v10.5m0-10.5A.75.75 0 0021 5.25V4.5m0 10.5a.75.75 0 00.75-.75V6m-.75 9h-19.5" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Posisi Saldo Kas</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('kasir.laporan.kasbon*'); @endphp
                    <a href="{{ route('kasir.laporan.kasbon') }}" 
                       :title="sidebarCollapsed ? 'Monitoring Kasbon' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Monitoring Kasbon</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('kasir.laporan-harian*'); @endphp
                    <a href="{{ route('kasir.laporan-harian') }}" 
                       :title="sidebarCollapsed ? 'Kas Harian' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Kas Harian</span>
                        </div>
                    </a>
                </div>

                <!-- 4. UTILITAS KAS -->
                <div class="space-y-0.5 pt-2 pb-2">
                    <div x-show="!sidebarCollapsed" class="pt-2 pb-1 px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-200/70">
                            Utilitas Kas
                        </span>
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 border-t border-white/10 mx-2"></div>

                    @php $isSub = request()->routeIs('kasir.utilitas.closing*'); @endphp
                    <a href="{{ route('kasir.utilitas.closing') }}" 
                       :title="sidebarCollapsed ? 'Tutup Kas Harian' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Tutup Kas Harian</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('kasir.buku-kas*'); @endphp
                    <a href="{{ route('kasir.buku-kas') }}" 
                       :title="sidebarCollapsed ? 'Buku Kas' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Buku Kas</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('kasir.utilitas.koreksi*'); @endphp
                    <a href="{{ route('kasir.utilitas.koreksi') }}" 
                       :title="sidebarCollapsed ? 'Koreksi Transaksi' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Koreksi Transaksi</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('kasir.utilitas.cetak-ulang*'); @endphp
                    <a href="{{ route('kasir.utilitas.cetak-ulang') }}" 
                       :title="sidebarCollapsed ? 'Cetak Ulang' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Cetak Ulang</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('kasir.riwayat-cetak*'); @endphp
                    <a href="{{ route('kasir.riwayat-cetak') }}" 
                       :title="sidebarCollapsed ? 'Riwayat Cetak' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Riwayat Cetak</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('kasir.utilitas.buka-kas*'); @endphp
                    <a href="{{ route('kasir.utilitas.buka-kas') }}" 
                       :title="sidebarCollapsed ? 'Buka Kembali Kas' : ''"
                       class="group flex items-center px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-[#0E527B] font-bold shadow-md' : 'text-sky-100 hover:text-white hover:bg-white/10 font-semibold' }}"
                       :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-3'">
                        <div class="flex items-center gap-2.5 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-[#0E527B]' : 'text-sky-200 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="truncate">Buka Kembali Kas</span>
                        </div>
                    </a>
                </div>
            @endif

        @endif

    </nav>
</aside>
