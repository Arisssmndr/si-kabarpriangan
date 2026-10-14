<!-- Sidebar Navigasi Kabar Priangan (Clean Minimalist Industry Standard) -->
<aside class="w-64 bg-[#F8FAFC] text-slate-800 flex flex-col shrink-0 h-full border-r border-slate-200 select-none shadow-[1px_0_4px_rgba(0,0,0,0.02)]">

    <!-- 1. Header Brand & Identitas (Sesuai Referensi Modern Standard) -->
    <div class="px-5 py-4 border-b border-slate-200/80 flex items-center gap-3 bg-white/80 backdrop-blur-xs">
        <img src="{{ asset('images/kabarpriangan.png') }}" 
             alt="Logo Kabar Priangan" 
             class="h-9 w-9 object-contain rounded-full bg-white p-0.5 shadow-xs border border-slate-200 shrink-0"
             onerror="this.src='{{ asset('kabarpriangan.png') }}'">
        <div class="leading-tight min-w-0">
            <h1 class="font-extrabold text-xs tracking-tight text-slate-900 truncate">
                KABAR PRIANGAN
            </h1>
            <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider truncate mt-0.5">
                {{ Auth::user()->role_label ?? 'Sistem Terpadu' }}
            </p>
        </div>
    </div>

    <!-- 2. Navigasi Hirarkis Berbasis Role & Dropdown Accordion -->
    <nav class="flex-1 px-3 py-3.5 space-y-1.5 overflow-y-auto sidebar-scroll">

        @php
            $user = Auth::user();
            $isAdmin = $user && $user->isAdmin();
            $isIklan = $user && ($isAdmin || $user->hasRole('iklan'));
            $isKeuangan = $user && ($isAdmin || $user->hasRole('keuangan'));
            $isAccounting = $user && ($isAdmin || $user->hasRole('accounting'));
            $isSirkulasi = $user && ($isAdmin || $user->hasRole('sirkulasi'));
            $isKasir = $user && ($isAdmin || $user->hasRole('kasir'));
        @endphp

        <!-- ============================================== -->
        <!-- JIKA ADMINISTRATOR: PUSAT KENDALI & DROPDOWN   -->
        <!-- ============================================== -->
        @if($isAdmin)
            
            <!-- Dashboard Eksekutif Admin (Direct Link Sesuai Referensi) -->
            <div>
                @php $isAdminActive = request()->routeIs('admin.dashboard'); @endphp
                <a href="{{ route('admin.dashboard') }}" 
                   class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAdminActive ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <!-- Modern 2x2 Grid Icon (Dashboard) -->
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isAdminActive ? 'text-kp-blue-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <rect x="3.5" y="3.5" width="6.5" height="6.5" rx="2" />
                            <rect x="14" y="3.5" width="6.5" height="6.5" rx="2" />
                            <rect x="14" y="14" width="6.5" height="6.5" rx="2" />
                            <rect x="3.5" y="14" width="6.5" height="6.5" rx="2" />
                        </svg>
                        <span class="truncate">Dashboard Admin</span>
                    </div>
                    @if($isAdminActive)
                        <span class="w-1.5 h-1.5 rounded-full bg-kp-blue-600 shrink-0"></span>
                    @endif
                </a>
            </div>

            <!-- Divider Label Modul -->
            <div class="pt-2.5 pb-1 px-3">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Modul Divisi
                </span>
            </div>

            <!-- 1. Dropdown Divisi Iklan -->
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
                            || request()->routeIs('pembayaran-iklan.*');
            @endphp
            <div x-data="{ open: {{ $isIklanOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                <button type="button" 
                        @click="open = !open" 
                        class="group w-full flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-150 cursor-pointer {{ $isIklanOpen ? 'text-slate-900 bg-slate-200/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <!-- Product/Folder Outline Icon Sesuai Referensi -->
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isIklanOpen ? 'text-kp-blue-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.75a3 3 0 013-3h2.186a3 3 0 012.343 1.125l.828 1.125a1.5 1.5 0 001.171.562H18a3 3 0 013 3v6.75a3 3 0 01-3 3H6.75a3 3 0 01-3-3V9.75z" />
                        </svg>
                        <span class="truncate">Divisi Iklan</span>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <!-- Submenu Tree Branch Connectors -->
                <div x-cloak 
                     x-show="open" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="relative pl-6 pt-1 pb-1 space-y-1">
                    
                    <!-- Garis Utama Pohon (Tree Guide Line) -->
                    <div class="absolute left-3 top-0 bottom-3.5 w-[1.5px] bg-slate-300/80"></div>

                    <!-- 1. Dashboard Iklan -->
                    @php $isSub = request()->routeIs('iklan.dashboard'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('iklan.dashboard') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Overview Iklan</span>
                        </a>
                    </div>

                    <!-- 2. Koran Cetak -->
                    @php $isSub = request()->routeIs('transaksikoran.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('transaksikoran.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Koran Cetak</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-sky-800' : 'bg-slate-200/70 text-slate-600' }}">Koran</span>
                        </a>
                    </div>

                    <!-- 3. Media Online -->
                    @php $isSub = request()->routeIs('transaksionline.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('transaksionline.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Media Online</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200/70 text-slate-600' }}">Online</span>
                        </a>
                    </div>

                    <!-- 4. Priangan TV -->
                    @php $isSub = request()->routeIs('transaksipriangan.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('transaksipriangan.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Priangan TV</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-purple-100 text-purple-800' : 'bg-slate-200/70 text-slate-600' }}">TV</span>
                        </a>
                    </div>

                    <!-- 5. Kwitansi Kasir Iklan -->
                    @php $isSub = request()->routeIs('pembayaran-iklan.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('pembayaran-iklan.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Kwitansi Kasir</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-teal-100 text-teal-800' : 'bg-slate-200/70 text-slate-600' }}">Kasir</span>
                        </a>
                    </div>

                    <!-- 6. Laporan Iklan -->
                    @php $isSub = request()->routeIs('laporan.*') || request()->routeIs('laporankoran.*') || request()->routeIs('laporanonline.*') || request()->routeIs('laporanpriangan.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('laporan.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Laporan Iklan</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-amber-100 text-amber-800' : 'bg-slate-200/70 text-slate-600' }}">Rekap</span>
                        </a>
                    </div>

                    <!-- 7. Master Pelanggan (CRM) -->
                    @php $isSub = request()->routeIs('pelanggan.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('pelanggan.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Pelanggan (CRM)</span>
                        </a>
                    </div>

                    <!-- 8. Kategori Media -->
                    @php $isSub = request()->routeIs('kategori-media.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('kategori-media.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Kategori Media</span>
                        </a>
                    </div>

                    <!-- 9. Tipe & Jenis Iklan -->
                    @php $isSub = request()->routeIs('jenis-iklan.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('jenis-iklan.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Tipe & Jenis Iklan</span>
                        </a>
                    </div>

                    <!-- 10. Pengaturan Tarif & PPN -->
                    @php $isSub = request()->routeIs('iklan.pengaturan.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('iklan.pengaturan.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Pengaturan Tarif</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. Dropdown Divisi Keuangan -->
            @php
                $isKeuanganOpen = request()->routeIs('keuangan.*');
            @endphp
            <div x-data="{ open: {{ $isKeuanganOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                <button type="button" 
                        @click="open = !open" 
                        class="group w-full flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-150 cursor-pointer {{ $isKeuanganOpen ? 'text-slate-900 bg-slate-200/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <!-- Income / Trending Chart Icon Sesuai Referensi -->
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isKeuanganOpen ? 'text-emerald-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <rect x="3" y="4.5" width="18" height="15" rx="3.5" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 13.5l3.5-3.5 3 3 3.5-4" />
                        </svg>
                        <span class="truncate">Divisi Keuangan</span>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <div x-cloak 
                     x-show="open" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="relative pl-6 pt-1 pb-1 space-y-1">
                    <div class="absolute left-3 top-0 bottom-3.5 w-[1.5px] bg-slate-300/80"></div>

                    @php $isSub = request()->routeIs('keuangan.dashboard'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('keuangan.dashboard') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Dashboard Keuangan</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. Dropdown Divisi Accounting -->
            @php
                $isAccountingOpen = request()->routeIs('accounting.*');
            @endphp
            <div x-data="{ open: {{ $isAccountingOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                <button type="button" 
                        @click="open = !open" 
                        class="group w-full flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-150 cursor-pointer {{ $isAccountingOpen ? 'text-slate-900 bg-slate-200/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <!-- Rosette/Star Badge Outline Icon Sesuai Referensi -->
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isAccountingOpen ? 'text-purple-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                        </svg>
                        <span class="truncate">Divisi Accounting</span>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <div x-cloak 
                     x-show="open" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="relative pl-6 pt-1 pb-1 space-y-1">
                    <div class="absolute left-3 top-0 bottom-3.5 w-[1.5px] bg-slate-300/80"></div>

                    @php $isSub = request()->routeIs('accounting.dashboard'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('accounting.dashboard') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Dashboard Accounting</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 4. Dropdown Divisi Sirkulasi -->
            @php
                $isSirkulasiOpen = request()->routeIs('sirkulasi.*');
            @endphp
            <div x-data="{ open: {{ $isSirkulasiOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                <button type="button" 
                        @click="open = !open" 
                        class="group w-full flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-150 cursor-pointer {{ $isSirkulasiOpen ? 'text-slate-900 bg-slate-200/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <!-- Customers/User Outline Icon Sesuai Referensi -->
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSirkulasiOpen ? 'text-amber-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <circle cx="12" cy="7.5" r="3" />
                            <path stroke-linecap="round" d="M5.5 19.5c0-3.3 2.9-6 6.5-6s6.5 2.7 6.5 6" />
                        </svg>
                        <span class="truncate">Divisi Sirkulasi</span>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <div x-cloak 
                     x-show="open" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="relative pl-6 pt-1 pb-1 space-y-1">
                    <div class="absolute left-3 top-0 bottom-3.5 w-[1.5px] bg-slate-300/80"></div>

                    @php $isSub = request()->routeIs('sirkulasi.dashboard'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('sirkulasi.dashboard') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Dashboard Sirkulasi</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 5. Dropdown Divisi Kasir -->
            @php
                $isKasirOpen = request()->routeIs('kasir.*');
            @endphp
            <div x-data="{ open: {{ $isKasirOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                <button type="button" 
                        @click="open = !open" 
                        class="group w-full flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-150 cursor-pointer {{ $isKasirOpen ? 'text-slate-900 bg-slate-200/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <!-- Shop/Register Outline Icon Sesuai Referensi -->
                        <svg class="w-4 h-4 shrink-0 transition-colors {{ $isKasirOpen ? 'text-teal-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span class="truncate">Divisi Kasir</span>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <div x-cloak 
                     x-show="open" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="relative pl-6 pt-1 pb-1 space-y-1">
                    <div class="absolute left-3 top-0 bottom-3.5 w-[1.5px] bg-slate-300/80"></div>

                    @php $isSub = request()->routeIs('kasir.dashboard'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('kasir.dashboard') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Dashboard Kasir</span>
                        </a>
                    </div>
                </div>
            </div>

        @else
            <!-- ============================================== -->
            <!-- JIKA BUKAN ADMIN: TAMPILAN TERFOKUS SESUAI ROLE-->
            <!-- ============================================== -->

            @if($isIklan)
                @php $isIklanDash = request()->routeIs('iklan.dashboard'); @endphp
                <div>
                    <a href="{{ route('iklan.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isIklanDash ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isIklanDash ? 'text-kp-blue-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="3.5" y="3.5" width="6.5" height="6.5" rx="2" />
                                <rect x="14" y="3.5" width="6.5" height="6.5" rx="2" />
                                <rect x="14" y="14" width="6.5" height="6.5" rx="2" />
                                <rect x="3.5" y="14" width="6.5" height="6.5" rx="2" />
                            </svg>
                            <span class="truncate">Dashboard Iklan</span>
                        </div>
                        @if($isIklanDash)
                            <span class="w-1.5 h-1.5 rounded-full bg-kp-blue-600 shrink-0"></span>
                        @endif
                    </a>
                </div>

                <!-- Dropdown Transaksi Media -->
                @php
                    $isTxOpen = request()->routeIs('transaksikoran.*') || request()->routeIs('transaksionline.*') || request()->routeIs('transaksipriangan.*');
                @endphp
                <div x-data="{ open: {{ $isTxOpen ? 'true' : 'true' }} }" class="space-y-0.5 pt-1">
                    <button type="button" 
                            @click="open = !open" 
                            class="group w-full flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-150 cursor-pointer {{ $isTxOpen ? 'text-slate-900 bg-slate-200/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isTxOpen ? 'text-kp-blue-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.75a3 3 0 013-3h2.186a3 3 0 012.343 1.125l.828 1.125a1.5 1.5 0 001.171.562H18a3 3 0 013 3v6.75a3 3 0 01-3 3H6.75a3 3 0 01-3-3V9.75z" />
                            </svg>
                            <span class="truncate">Transaksi Media</span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </button>

                    <div x-cloak 
                         x-show="open" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="relative pl-6 pt-1 pb-1 space-y-1">
                        <div class="absolute left-3 top-0 bottom-3.5 w-[1.5px] bg-slate-300/80"></div>

                        @php $isSub = request()->routeIs('transaksikoran.*'); @endphp
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <a href="{{ route('transaksikoran.index') }}" 
                               class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                                <span class="truncate">Koran Cetak</span>
                                <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-sky-800' : 'bg-slate-200/70 text-slate-600' }}">Koran</span>
                            </a>
                        </div>

                        @php $isSub = request()->routeIs('transaksionline.*'); @endphp
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <a href="{{ route('transaksionline.index') }}" 
                               class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                                <span class="truncate">Media Online</span>
                                <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200/70 text-slate-600' }}">Online</span>
                            </a>
                        </div>

                        @php $isSub = request()->routeIs('transaksipriangan.*'); @endphp
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <a href="{{ route('transaksipriangan.index') }}" 
                               class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                                <span class="truncate">Priangan TV</span>
                                <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-purple-100 text-purple-800' : 'bg-slate-200/70 text-slate-600' }}">TV</span>
                            </a>
                        </div>
                        <!-- Kwitansi Kasir Iklan -->
                        @php $isSub = request()->routeIs('pembayaran-iklan.*'); @endphp
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <a href="{{ route('pembayaran-iklan.index') }}" 
                               class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                                <span class="truncate">Kwitansi Kasir</span>
                                <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-teal-100 text-teal-800' : 'bg-slate-200/70 text-slate-600' }}">Kasir</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Laporan Iklan -->
                @php $isLap = request()->routeIs('laporan.*') || request()->routeIs('laporankoran.*') || request()->routeIs('laporanonline.*') || request()->routeIs('laporanpriangan.*'); @endphp
                <div class="pt-1">
                    <a href="{{ route('laporan.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isLap ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isLap ? 'text-amber-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="3" y="4.5" width="18" height="15" rx="3.5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 13.5l3.5-3.5 3 3 3.5-4" />
                            </svg>
                            <span class="truncate">Laporan Iklan</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isLap ? 'bg-amber-100 text-amber-800' : 'bg-slate-200/70 text-slate-600' }}">Rekap</span>
                    </a>
                </div>

                <!-- Dropdown Master Data -->
                @php
                    $isMasterOpen = request()->routeIs('kategori-media.*') || request()->routeIs('jenis-iklan.*') || request()->routeIs('pelanggan.*') || request()->routeIs('iklan.pengaturan.*');
                @endphp
                <div x-data="{ open: {{ $isMasterOpen ? 'true' : 'false' }} }" class="space-y-0.5 pt-1">
                    <button type="button" 
                            @click="open = !open" 
                            class="group w-full flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition-all duration-150 cursor-pointer {{ $isMasterOpen ? 'text-slate-900 bg-slate-200/50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isMasterOpen ? 'text-kp-blue-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>
                            <span class="truncate">Master Data</span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </button>

                    <div x-cloak 
                         x-show="open" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="relative pl-6 pt-1 pb-1 space-y-1">
                        <div class="absolute left-3 top-0 bottom-3.5 w-[1.5px] bg-slate-300/80"></div>

                        <!-- Master Pelanggan (CRM) -->
                        @php $isSub = request()->routeIs('pelanggan.*'); @endphp
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <a href="{{ route('pelanggan.index') }}" 
                               class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                                <span class="truncate">Pelanggan (CRM)</span>
                            </a>
                        </div>

                        <!-- Kategori Media -->
                        @php $isSub = request()->routeIs('kategori-media.*'); @endphp
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <a href="{{ route('kategori-media.index') }}" 
                               class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                                <span class="truncate">Kategori Media</span>
                            </a>
                        </div>

                        <!-- Tipe & Jenis Iklan -->
                        @php $isSub = request()->routeIs('jenis-iklan.*'); @endphp
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <a href="{{ route('jenis-iklan.index') }}" 
                               class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                                <span class="truncate">Tipe & Jenis Iklan</span>
                            </a>
                        </div>

                        <!-- Pengaturan Tarif -->
                        @php $isSub = request()->routeIs('iklan.pengaturan.*'); @endphp
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <a href="{{ route('iklan.pengaturan.index') }}" 
                               class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                                <span class="truncate">Pengaturan Tarif</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            @if(!$isIklan && $isKeuangan)
                @php $isKeuDash = request()->routeIs('keuangan.dashboard'); @endphp
                <div>
                    <a href="{{ route('keuangan.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isKeuDash ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isKeuDash ? 'text-emerald-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="3" y="4.5" width="18" height="15" rx="3.5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 13.5l3.5-3.5 3 3 3.5-4" />
                            </svg>
                            <span class="truncate">Dashboard Keuangan</span>
                        </div>
                    </a>
                </div>
            @endif

            @if(!$isIklan && $isAccounting)
                @php $isAccDash = request()->routeIs('accounting.dashboard'); @endphp
                <div>
                    <a href="{{ route('accounting.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAccDash ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isAccDash ? 'text-purple-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>
                            <span class="truncate">Dashboard Accounting</span>
                        </div>
                    </a>
                </div>
            @endif

            @if(!$isIklan && $isSirkulasi)
                @php $isSirDash = request()->routeIs('sirkulasi.dashboard'); @endphp
                <div>
                    <a href="{{ route('sirkulasi.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSirDash ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSirDash ? 'text-amber-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <circle cx="12" cy="7.5" r="3" />
                                <path stroke-linecap="round" d="M5.5 19.5c0-3.3 2.9-6 6.5-6s6.5 2.7 6.5 6" />
                            </svg>
                            <span class="truncate">Dashboard Sirkulasi</span>
                        </div>
                    </a>
                </div>
            @endif

            @if(!$isIklan && $isKasir)
                @php $isKasDash = request()->routeIs('kasir.dashboard'); @endphp
                <div>
                    <a href="{{ route('kasir.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isKasDash ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isKasDash ? 'text-teal-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span class="truncate">Dashboard Kasir</span>
                        </div>
                    </a>
                </div>
            @endif

        @endif

    </nav>

    <!-- 3. Footer Sidebar: User Profile Ringkas & Logout -->
    <div class="p-3 border-t border-slate-200/80 bg-white/60">
        <div class="flex items-center justify-between px-2.5 py-2 rounded-xl bg-slate-100/80 border border-slate-200/70">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-7 h-7 rounded-lg bg-kp-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="min-w-0 leading-tight">
                    <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-slate-500 font-medium truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
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
