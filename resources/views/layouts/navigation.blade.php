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
            $isKeuangan = $user && ($isAdmin || $user->hasRole('keuangan') || $user->hasRole('admin_keuangan') || $user->hasRole('petugas_inkaso') || $user->hasRole('kepala_divisi'));
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

                    <!-- 2. Input Transaksi Baru (Submenu) -->
                    @php
                        $isInputTxOpen = request()->routeIs('transaksikoran.create') 
                                      || request()->routeIs('transaksionline.create') 
                                      || request()->routeIs('transaksipriangan.create');
                    @endphp
                    <div x-data="{ openInput: {{ $isInputTxOpen ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <button type="button" 
                                    @click="openInput = !openInput" 
                                    class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isInputTxOpen ? 'bg-emerald-50/80 text-emerald-900 font-bold border border-emerald-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/70 font-semibold' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    <span class="truncate">Input Transaksi Baru</span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800">+ Baru</span>
                                    <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="openInput ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                            </button>
                        </div>

                        <!-- 3 Submenu Item: Koran, Online, Priangan TV -->
                        <div x-cloak 
                             x-show="openInput" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                            <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-emerald-200"></div>

                            @php $isSub = request()->routeIs('transaksikoran.create'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-emerald-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('transaksikoran.create') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-sky-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Koran Cetak</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-sky-800' : 'bg-slate-200/70 text-slate-600' }}">Koran</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('transaksionline.create'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-emerald-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('transaksionline.create') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-emerald-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Media Online</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200/70 text-slate-600' }}">Online</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('transaksipriangan.create'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-emerald-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('transaksipriangan.create') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-purple-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Priangan TV</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-purple-100 text-purple-800' : 'bg-slate-200/70 text-slate-600' }}">TV</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Daftar Koran Cetak -->
                    @php $isSub = request()->routeIs('transaksikoran.*') && !request()->routeIs('transaksikoran.create'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('transaksikoran.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Koran Cetak</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-sky-800' : 'bg-slate-200/70 text-slate-600' }}">Koran</span>
                        </a>
                    </div>

                    <!-- 4. Daftar Media Online -->
                    @php $isSub = request()->routeIs('transaksionline.*') && !request()->routeIs('transaksionline.create'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('transaksionline.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Media Online</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200/70 text-slate-600' }}">Online</span>
                        </a>
                    </div>

                    <!-- 5. Daftar Priangan TV -->
                    @php $isSub = request()->routeIs('transaksipriangan.*') && !request()->routeIs('transaksipriangan.create'); @endphp
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

                    <!-- 1. Dashboard Overview -->
                    @php $isSub = request()->routeIs('keuangan.dashboard'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('keuangan.dashboard') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Overview Keuangan</span>
                        </a>
                    </div>

                    <!-- 2. Alur Faktur & Inkaso -->
                    @php $isSub = request()->routeIs('keuangan.alur'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('keuangan.alur') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Alur Faktur & Inkaso</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-cyan-100 text-cyan-800' : 'bg-slate-200/70 text-slate-600' }}">SOP</span>
                        </a>
                    </div>

                    <!-- 3. Faktur Penjualan -->
                    @php $isSub = request()->routeIs('keuangan.faktur.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('keuangan.faktur.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Faktur Penjualan</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-blue-100 text-blue-800' : 'bg-slate-200/70 text-slate-600' }}">Faktur</span>
                        </a>
                    </div>

                    <!-- 4. Penugasan Inkaso -->
                    @php $isSub = request()->routeIs('keuangan.inkaso.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('keuangan.inkaso.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Penugasan Inkaso</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-amber-100 text-amber-800' : 'bg-slate-200/70 text-slate-600' }}">Inkaso</span>
                        </a>
                    </div>

                    <!-- 5. Pengajuan Anggaran -->
                    @php $isSub = request()->routeIs('keuangan.anggaran.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('keuangan.anggaran.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Pengajuan Anggaran</span>
                        </a>
                    </div>

                    <!-- 6. Buku Kas Treasury -->
                    @php $isSub = request()->routeIs('keuangan.kas.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('keuangan.kas.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Buku Kas (Treasury)</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200/70 text-slate-600' }}">Kas</span>
                        </a>
                    </div>

                    <!-- 7. Laporan Keuangan Terpadu -->
                    @php $isSub = request()->routeIs('keuangan.laporan'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('keuangan.laporan') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Laporan Terpadu</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-amber-100 text-amber-800' : 'bg-slate-200/70 text-slate-600' }}">Rekap</span>
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

                    <!-- 1. Overview Accounting -->
                    @php $isSub = request()->routeIs('accounting.dashboard'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('accounting.dashboard') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Dashboard Overview</span>
                        </a>
                    </div>

                    <!-- 2. Tarik & Validasi Data -->
                    @php $isTarikOpen = request()->routeIs('accounting.tarik.*'); @endphp
                    <div x-data="{ openTarik: {{ $isTarikOpen ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <button type="button" 
                                    @click="openTarik = !openTarik" 
                                    class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isTarikOpen ? 'bg-purple-50 text-purple-900 font-bold border border-purple-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/70 font-semibold' }}">
                                <span class="truncate">Tarik & Validasi Data</span>
                                <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="openTarik ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>
                        <div x-cloak x-show="openTarik" class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                            <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-purple-200"></div>

                            @php $isSub = request()->routeIs('accounting.tarik.iklan*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-purple-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('accounting.tarik.iklan') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-purple-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Penarikan Iklan Harian</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-purple-100 text-purple-800' : 'bg-slate-200/70 text-slate-600' }}">Harian</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('accounting.tarik.sirkulasi*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-purple-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('accounting.tarik.sirkulasi') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-purple-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Penarikan Sirkulasi Bulanan</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-amber-100 text-amber-800' : 'bg-slate-200/70 text-slate-600' }}">Bulanan</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('accounting.tarik.kasir*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-purple-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('accounting.tarik.kasir') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-purple-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Rekonsiliasi Kasir</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200/70 text-slate-600' }}">Kas</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Kartu Sub-Ledger (Kartu Piutang) -->
                    @php $isKartuOpen = request()->routeIs('accounting.kartu.*'); @endphp
                    <div x-data="{ openKartu: {{ $isKartuOpen ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <button type="button" 
                                    @click="openKartu = !openKartu" 
                                    class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isKartuOpen ? 'bg-purple-50 text-purple-900 font-bold border border-purple-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/70 font-semibold' }}">
                                <span class="truncate">Kartu Sub-Ledger</span>
                                <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="openKartu ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>
                        <div x-cloak x-show="openKartu" class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                            <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-purple-200"></div>

                            @php $isSub = request()->routeIs('accounting.kartu.iklan*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-purple-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('accounting.kartu.iklan') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-purple-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Kartu Piutang Iklan</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('accounting.kartu.sirkulasi*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-purple-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('accounting.kartu.sirkulasi') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-purple-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Piutang Agen Sirkulasi</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('accounting.kartu.retur*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-purple-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('accounting.kartu.retur') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-rose-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Penyesuaian Retur (BAPR)</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-rose-100 text-rose-800' : 'bg-slate-200/70 text-slate-600' }}">Retur</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Buku Besar & Jurnal -->
                    @php $isGlOpen = request()->routeIs('accounting.gl.*'); @endphp
                    <div x-data="{ openGL: {{ $isGlOpen ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <button type="button" 
                                    @click="openGL = !openGL" 
                                    class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isGlOpen ? 'bg-purple-50 text-purple-900 font-bold border border-purple-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/70 font-semibold' }}">
                                <span class="truncate">Buku Besar & Jurnal</span>
                                <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="openGL ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>
                        <div x-cloak x-show="openGL" class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                            <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-purple-200"></div>

                            @php $isSub = request()->routeIs('accounting.gl.index*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-purple-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('accounting.gl.index') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-indigo-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Jurnal Umum (GL)</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-200/70 text-slate-600' }}">GL</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('accounting.gl.coa*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-purple-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('accounting.gl.coa') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-purple-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Bagan Akun (COA)</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Laporan Keuangan -->
                    @php $isLaporanOpen = request()->routeIs('accounting.laporan.*'); @endphp
                    <div x-data="{ openLaporan: {{ $isLaporanOpen ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <button type="button" 
                                    @click="openLaporan = !openLaporan" 
                                    class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isLaporanOpen ? 'bg-purple-50 text-purple-900 font-bold border border-purple-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/70 font-semibold' }}">
                                <span class="truncate">Laporan Keuangan</span>
                                <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="openLaporan ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>
                        <div x-cloak x-show="openLaporan" class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                            <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-purple-200"></div>

                            @php $isSub = request()->routeIs('accounting.laporan.laba-rugi*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-purple-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('accounting.laporan.laba-rugi') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-purple-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Laporan Laba Rugi</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('accounting.laporan.neraca*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-purple-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('accounting.laporan.neraca') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-purple-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Neraca Keuangan</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('accounting.laporan.buku-besar*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-purple-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('accounting.laporan.buku-besar') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-purple-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Buku Besar Pembantu</span>
                                </a>
                            </div>
                        </div>
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

                    <!-- 1. Overview Dashboard Sirkulasi -->
                    @php $isSub = request()->routeIs('sirkulasi.dashboard'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('sirkulasi.dashboard') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Dashboard Sirkulasi</span>
                        </a>
                    </div>

                    <!-- 2. Master Data Sirkulasi -->
                    @php
                        $isMasterSirOpen = request()->routeIs('sirkulasi.master.*');
                    @endphp
                    <div x-data="{ openMasterSir: {{ $isMasterSirOpen ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <button type="button" 
                                    @click="openMasterSir = !openMasterSir" 
                                    class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isMasterSirOpen ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/70 font-semibold' }}">
                                <span class="truncate">Master Sirkulasi</span>
                                <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="openMasterSir ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>
                        <div x-cloak x-show="openMasterSir" class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                            <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-amber-200"></div>

                            @php $isSub = request()->routeIs('sirkulasi.master.agen*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('sirkulasi.master.agen') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-amber-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Agen & Sub-Agen</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-amber-100 text-amber-800' : 'bg-slate-200/70 text-slate-600' }}">Agen</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('sirkulasi.master.rute*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('sirkulasi.master.rute') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-sky-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Rute & Armada</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-sky-800' : 'bg-slate-200/70 text-slate-600' }}">Rute</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('sirkulasi.master.kalender*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('sirkulasi.master.kalender') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-purple-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Kalender Terbit</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('sirkulasi.master.tarif*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('sirkulasi.master.tarif') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-emerald-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Tarif & Ketentuan</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('sirkulasi.master.pelanggan*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('sirkulasi.master.pelanggan') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-indigo-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Pelanggan Dinas</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-200/70 text-slate-600' }}">Dinas</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Operasional Jatah (Siklus H-1) -->
                    @php
                        $isJatahSirOpen = request()->routeIs('sirkulasi.jatah.*');
                    @endphp
                    <div x-data="{ openJatahSir: {{ $isJatahSirOpen ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <button type="button" 
                                    @click="openJatahSir = !openJatahSir" 
                                    class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isJatahSirOpen ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/70 font-semibold' }}">
                                <span class="truncate">Operasional Jatah (H-1)</span>
                                <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="openJatahSir ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>
                        <div x-cloak x-show="openJatahSir" class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                            <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-amber-200"></div>

                            @php $isSub = request()->routeIs('sirkulasi.jatah.harian*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('sirkulasi.jatah.harian') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-amber-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Entri Jatah H+1</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-amber-100 text-amber-800' : 'bg-slate-200/70 text-slate-600' }}">Input</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('sirkulasi.jatah.copy*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('sirkulasi.jatah.copy') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-sky-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Salin Pola Jatah</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('sirkulasi.jatah.quick-edit*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('sirkulasi.jatah.quick-edit') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-rose-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Perubahan Cepat</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Logistik & Distribusi Subuh -->
                    @php
                        $isLogistikSirOpen = request()->routeIs('sirkulasi.logistik.*');
                    @endphp
                    <div x-data="{ openLogistikSir: {{ $isLogistikSirOpen ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <button type="button" 
                                    @click="openLogistikSir = !openLogistikSir" 
                                    class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isLogistikSirOpen ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/70 font-semibold' }}">
                                <span class="truncate">Logistik & Distribusi</span>
                                <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="openLogistikSir ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>
                        <div x-cloak x-show="openLogistikSir" class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                            <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-amber-200"></div>

                            @php $isSub = request()->routeIs('sirkulasi.logistik.surat-jalan*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('sirkulasi.logistik.surat-jalan') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-sky-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Surat Jalan Packing</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-sky-800' : 'bg-slate-200/70 text-slate-600' }}">Armada</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('sirkulasi.logistik.retur*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('sirkulasi.logistik.retur') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-rose-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">BAPR Koran Retur</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-rose-100 text-rose-800' : 'bg-slate-200/70 text-slate-600' }}">Retur</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('sirkulasi.logistik.order-cetak*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('sirkulasi.logistik.order-cetak') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-orange-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">SPK Percetakan</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-orange-100 text-orange-800' : 'bg-slate-200/70 text-slate-600' }}">SPK</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Tutup Data & Laporan -->
                    @php
                        $isLaporanSirOpen = request()->routeIs('sirkulasi.laporan.*');
                    @endphp
                    <div x-data="{ openLaporanSir: {{ $isLaporanSirOpen ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <button type="button" 
                                    @click="openLaporanSir = !openLaporanSir" 
                                    class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isLaporanSirOpen ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/70 font-semibold' }}">
                                <span class="truncate">Tutup Data & Laporan</span>
                                <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="openLaporanSir ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>
                        <div x-cloak x-show="openLaporanSir" class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                            <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-amber-200"></div>

                            @php $isSub = request()->routeIs('sirkulasi.laporan.closing*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('sirkulasi.laporan.closing') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-purple-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Tutup Data Bulanan</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-purple-100 text-purple-800' : 'bg-slate-200/70 text-slate-600' }}">Closing</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('sirkulasi.laporan.pemasaran*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('sirkulasi.laporan.pemasaran') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-teal-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Rekap Pemasaran</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('sirkulasi.laporan.penjualan*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('sirkulasi.laporan.penjualan') }}" class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-emerald-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Rekap Penjualan</span>
                                </a>
                            </div>
                        </div>
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

                    <!-- 1. Overview Kasir -->
                    @php $isSub = request()->routeIs('kasir.dashboard'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('kasir.dashboard') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Overview Kasir</span>
                        </a>
                    </div>

                    <!-- 2. Input Transaksi Baru (Kasir Shortcut) -->
                    @php
                        $isInputKasirOpen = request()->routeIs('transaksikoran.create') 
                                         || request()->routeIs('transaksionline.create') 
                                         || request()->routeIs('transaksipriangan.create');
                    @endphp
                    <div x-data="{ openInputK: {{ $isInputKasirOpen ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <button type="button" 
                                    @click="openInputK = !openInputK" 
                                    class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isInputKasirOpen ? 'bg-teal-50 text-teal-900 font-bold border border-teal-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/70 font-semibold' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    <svg class="w-3.5 h-3.5 text-teal-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    <span class="truncate">Input Transaksi Baru</span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-teal-100 text-teal-800">+ Baru</span>
                                    <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="openInputK ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                            </button>
                        </div>

                        <!-- 3 Submenu Item: Koran, Online, Priangan TV -->
                        <div x-cloak 
                             x-show="openInputK" 
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
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-sky-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Koran Cetak</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-sky-800' : 'bg-slate-200/70 text-slate-600' }}">Koran</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('transaksionline.create'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-teal-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('transaksionline.create') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-emerald-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Media Online</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200/70 text-slate-600' }}">Online</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('transaksipriangan.create'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-teal-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('transaksipriangan.create') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-purple-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Priangan TV</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-purple-100 text-purple-800' : 'bg-slate-200/70 text-slate-600' }}">TV</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Setoran Iklan [Badge: Faktur] -->
                    @php $isSub = request()->routeIs('kasir.penerimaan.iklan'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('kasir.penerimaan.iklan') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Setoran Iklan</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-teal-100 text-teal-800' : 'bg-slate-200/70 text-slate-600' }}">Faktur</span>
                        </a>
                    </div>

                    <!-- 3. Setoran Sirkulasi [Badge: Koran] -->
                    @php $isSub = request()->routeIs('kasir.penerimaan.sirkulasi'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('kasir.penerimaan.sirkulasi') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Setoran Sirkulasi</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-sky-800' : 'bg-slate-200/70 text-slate-600' }}">Koran</span>
                        </a>
                    </div>

                    <!-- 4. Koran Langganan [Badge: Dinas] -->
                    @php $isSub = request()->routeIs('kasir.penerimaan.langganan'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('kasir.penerimaan.langganan') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Koran Langganan</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-purple-100 text-purple-800' : 'bg-slate-200/70 text-slate-600' }}">Dinas</span>
                        </a>
                    </div>

                    <!-- 5. Penjualan Langsung -->
                    @php $isSub = request()->routeIs('kasir.penerimaan.tunai'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('kasir.penerimaan.tunai') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Penjualan Langsung</span>
                        </a>
                    </div>

                    <!-- 6. Realisasi BP [Badge: BKK] -->
                    @php $isSub = request()->routeIs('kasir.pengeluaran.realisasi-bp'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('kasir.pengeluaran.realisasi-bp') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Realisasi BP</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-rose-100 text-rose-800' : 'bg-slate-200/70 text-slate-600' }}">BKK</span>
                        </a>
                    </div>

                    <!-- 7. Setor Kas ke Bank [Badge: Bank] -->
                    @php $isSub = request()->routeIs('kasir.setoran-bank'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('kasir.setoran-bank') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Setor Kas ke Bank</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-blue-100 text-blue-800' : 'bg-slate-200/70 text-slate-600' }}">Bank</span>
                        </a>
                    </div>

                    <!-- 8. Buku Kas & Tutup [Badge: Shift] -->
                    @php $isSub = request()->routeIs('kasir.buku-kas'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('kasir.buku-kas') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Buku Kas & Tutup</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200/70 text-slate-600' }}">Shift</span>
                        </a>
                    </div>

                    <!-- 9. Riwayat & Cetak [Badge: Kwitansi] -->
                    @php $isSub = request()->routeIs('kasir.riwayat-cetak'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('kasir.riwayat-cetak') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Riwayat & Cetak</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-amber-100 text-amber-800' : 'bg-slate-200/70 text-slate-600' }}">Kwitansi</span>
                        </a>
                    </div>

                    <!-- 10. Laporan Harian Kasir [Badge: Rekap] -->
                    @php $isSub = request()->routeIs('kasir.laporan-harian'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('kasir.laporan-harian') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Laporan Harian (LHK)</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-teal-100 text-teal-800' : 'bg-slate-200/70 text-slate-600' }}">Rekap</span>
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

                        <!-- Input Transaksi Baru (Submenu) -->
                        @php
                            $isInputTxOpen = request()->routeIs('transaksikoran.create') 
                                          || request()->routeIs('transaksionline.create') 
                                          || request()->routeIs('transaksipriangan.create');
                        @endphp
                        <div x-data="{ openInput: {{ $isInputTxOpen ? 'true' : 'false' }} }" class="space-y-1">
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                                <button type="button" 
                                        @click="openInput = !openInput" 
                                        class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isInputTxOpen ? 'bg-emerald-50/80 text-emerald-900 font-bold border border-emerald-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/70 font-semibold' }}">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        <span class="truncate">Input Transaksi Baru</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800">+ Baru</span>
                                        <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="openInput ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </div>
                                </button>
                            </div>

                            <!-- 3 Submenu Item: Koran, Online, Priangan TV -->
                            <div x-cloak 
                                 x-show="openInput" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 -translate-y-1"
                                 class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                                <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-emerald-200"></div>

                                @php $isSub = request()->routeIs('transaksikoran.create'); @endphp
                                <div class="relative flex items-center">
                                    <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-emerald-300 rounded-bl-lg pointer-events-none"></span>
                                    <a href="{{ route('transaksikoran.create') }}" 
                                       class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-sky-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                        <span class="truncate">Koran Cetak</span>
                                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-sky-800' : 'bg-slate-200/70 text-slate-600' }}">Koran</span>
                                    </a>
                                </div>

                                @php $isSub = request()->routeIs('transaksionline.create'); @endphp
                                <div class="relative flex items-center">
                                    <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-emerald-300 rounded-bl-lg pointer-events-none"></span>
                                    <a href="{{ route('transaksionline.create') }}" 
                                       class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-emerald-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                        <span class="truncate">Media Online</span>
                                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200/70 text-slate-600' }}">Online</span>
                                    </a>
                                </div>

                                @php $isSub = request()->routeIs('transaksipriangan.create'); @endphp
                                <div class="relative flex items-center">
                                    <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-emerald-300 rounded-bl-lg pointer-events-none"></span>
                                    <a href="{{ route('transaksipriangan.create') }}" 
                                       class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-purple-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                        <span class="truncate">Priangan TV</span>
                                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-purple-100 text-purple-800' : 'bg-slate-200/70 text-slate-600' }}">TV</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Koran Cetak -->
                        @php $isSub = request()->routeIs('transaksikoran.*') && !request()->routeIs('transaksikoran.create'); @endphp
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <a href="{{ route('transaksikoran.index') }}" 
                               class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                                <span class="truncate">Koran Cetak</span>
                                <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-sky-800' : 'bg-slate-200/70 text-slate-600' }}">Koran</span>
                            </a>
                        </div>

                        <!-- Daftar Media Online -->
                        @php $isSub = request()->routeIs('transaksionline.*') && !request()->routeIs('transaksionline.create'); @endphp
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <a href="{{ route('transaksionline.index') }}" 
                               class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                                <span class="truncate">Media Online</span>
                                <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200/70 text-slate-600' }}">Online</span>
                            </a>
                        </div>

                        <!-- Daftar Priangan TV -->
                        @php $isSub = request()->routeIs('transaksipriangan.*') && !request()->routeIs('transaksipriangan.create'); @endphp
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
                        @if($isKeuDash)
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
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
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAlur ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" /></svg>
                            <span class="truncate">Alur Faktur &amp; Inkaso</span>
                        </div>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-cyan-100 text-cyan-800">SOP</span>
                    </a>

                    @php $isFaktur = request()->routeIs('keuangan.faktur.*'); @endphp
                    <a href="{{ route('keuangan.faktur.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isFaktur ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <span class="truncate">Faktur Penjualan</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isFaktur ? 'bg-blue-100 text-blue-800' : 'bg-slate-200/70 text-slate-600' }}">Faktur</span>
                    </a>

                    @php $isInkaso = request()->routeIs('keuangan.inkaso.*'); @endphp
                    <a href="{{ route('keuangan.inkaso.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isInkaso ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span class="truncate">Penugasan Inkaso</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isInkaso ? 'bg-amber-100 text-amber-800' : 'bg-slate-200/70 text-slate-600' }}">Inkaso</span>
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
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAnggaran ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
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
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isKas ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="truncate">Buku Kas (Treasury)</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isKas ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200/70 text-slate-600' }}">Kas</span>
                    </a>

                    @php $isLapKeu = request()->routeIs('keuangan.laporan'); @endphp
                    <a href="{{ route('keuangan.laporan') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isLapKeu ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isLapKeu ? 'text-amber-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <rect x="3" y="4.5" width="18" height="15" rx="3.5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 13.5l3.5-3.5 3 3 3.5-4" />
                            </svg>
                            <span class="truncate">Laporan Keuangan</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isLapKeu ? 'bg-amber-100 text-amber-800' : 'bg-slate-200/70 text-slate-600' }}">Rekap</span>
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
                        @if($isAccDash)
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600 shrink-0"></span>
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
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-purple-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span class="truncate">Penarikan Iklan Harian</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-purple-100 text-purple-800' : 'bg-slate-200/70 text-slate-600' }}">Harian</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.tarik.sirkulasi*'); @endphp
                    <a href="{{ route('accounting.tarik.sirkulasi') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-amber-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="truncate">Penarikan Sirkulasi Bulanan</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-amber-100 text-amber-800' : 'bg-slate-200/70 text-slate-600' }}">Bulanan</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.tarik.kasir*'); @endphp
                    <a href="{{ route('accounting.tarik.kasir') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-emerald-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="truncate">Rekonsiliasi Kasir (Buku Kas)</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200/70 text-slate-600' }}">Kas</span>
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
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-purple-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="truncate">Kartu Piutang Iklan</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.kartu.sirkulasi*'); @endphp
                    <a href="{{ route('accounting.kartu.sirkulasi') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-purple-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="truncate">Kartu Piutang Agen Sirkulasi</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.kartu.retur*'); @endphp
                    <a href="{{ route('accounting.kartu.retur') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-rose-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 15v-1a4 4 0 00-4-4H4m0 0l3-3m-3 3l3 3m5 4v1a3 3 0 003 3h4" />
                            </svg>
                            <span class="truncate">Entri Retur Koran (BAPR)</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-rose-100 text-rose-800' : 'bg-slate-200/70 text-slate-600' }}">Retur</span>
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
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-indigo-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span class="truncate">Jurnal Umum / GL</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-200/70 text-slate-600' }}">GL</span>
                    </a>

                    @php $isSub = request()->routeIs('accounting.gl.coa*'); @endphp
                    <a href="{{ route('accounting.gl.coa') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
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
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-emerald-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                            <span class="truncate">Laporan Laba Rugi</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.laporan.neraca*'); @endphp
                    <a href="{{ route('accounting.laporan.neraca') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-sky-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                            </svg>
                            <span class="truncate">Neraca Keuangan (Balance Sheet)</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('accounting.laporan.buku-besar*'); @endphp
                    <a href="{{ route('accounting.laporan.buku-besar') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
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
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSirDash ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSirDash ? 'text-amber-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <circle cx="12" cy="7.5" r="3" />
                                <path stroke-linecap="round" d="M5.5 19.5c0-3.3 2.9-6 6.5-6s6.5 2.7 6.5 6" />
                            </svg>
                            <span class="truncate">Dashboard Sirkulasi</span>
                        </div>
                        @if($isSirDash)
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600 shrink-0"></span>
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
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-amber-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="truncate">Agen & Sub-Agen</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-amber-100 text-amber-800' : 'bg-slate-200/70 text-slate-600' }}">Agen</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.master.rute*'); @endphp
                    <a href="{{ route('sirkulasi.master.rute') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-sky-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            <span class="truncate">Wilayah & Rute Armada</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-sky-800' : 'bg-slate-200/70 text-slate-600' }}">Rute</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.master.kalender*'); @endphp
                    <a href="{{ route('sirkulasi.master.kalender') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-purple-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="truncate">Kalender Terbit Koran</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.master.tarif*'); @endphp
                    <a href="{{ route('sirkulasi.master.tarif') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-emerald-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="truncate">Tarif & Skema Gratis</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.master.pelanggan*'); @endphp
                    <a href="{{ route('sirkulasi.master.pelanggan') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-indigo-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="truncate">Pelanggan Dinas & Loper</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-200/70 text-slate-600' }}">Dinas</span>
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
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-amber-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <span class="truncate">Entri Jatah Harian H+1</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-amber-100 text-amber-800' : 'bg-slate-200/70 text-slate-600' }}">Input</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.jatah.copy*'); @endphp
                    <a href="{{ route('sirkulasi.jatah.copy') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-sky-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                            </svg>
                            <span class="truncate">Salin Pola Jatah</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.jatah.quick-edit*'); @endphp
                    <a href="{{ route('sirkulasi.jatah.quick-edit') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
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
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-sky-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                            <span class="truncate">Surat Jalan & Packing</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-sky-800' : 'bg-slate-200/70 text-slate-600' }}">Armada</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.logistik.retur*'); @endphp
                    <a href="{{ route('sirkulasi.logistik.retur') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-rose-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                            </svg>
                            <span class="truncate">Berita Acara Retur (BAPR)</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-rose-100 text-rose-800' : 'bg-slate-200/70 text-slate-600' }}">Retur</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.logistik.order-cetak*'); @endphp
                    <a href="{{ route('sirkulasi.logistik.order-cetak') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-orange-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            <span class="truncate">SPK Order Percetakan</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-orange-100 text-orange-800' : 'bg-slate-200/70 text-slate-600' }}">SPK</span>
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
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-purple-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span class="truncate">Tutup Data Bulanan</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-purple-100 text-purple-800' : 'bg-slate-200/70 text-slate-600' }}">Closing</span>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.laporan.pemasaran*'); @endphp
                    <a href="{{ route('sirkulasi.laporan.pemasaran') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-teal-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span class="truncate">Rekap Pemasaran (Fisik)</span>
                        </div>
                    </a>

                    @php $isSub = request()->routeIs('sirkulasi.laporan.penjualan*'); @endphp
                    <a href="{{ route('sirkulasi.laporan.penjualan') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
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
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isKasDash ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isKasDash ? 'text-teal-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span class="truncate">Overview Kasir</span>
                        </div>
                        @if($isKasDash)
                            <span class="w-1.5 h-1.5 rounded-full bg-teal-600 shrink-0"></span>
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
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-sky-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Koran Cetak</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-sky-800' : 'bg-slate-200/70 text-slate-600' }}">Koran</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('transaksionline.create'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-teal-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('transaksionline.create') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-emerald-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Media Online</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200/70 text-slate-600' }}">Online</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('transaksipriangan.create'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-teal-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('transaksipriangan.create') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-purple-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Priangan TV</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-purple-100 text-purple-800' : 'bg-slate-200/70 text-slate-600' }}">TV</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Setoran Iklan -->
                    @php $isSub = request()->routeIs('kasir.penerimaan.iklan'); @endphp
                    <a href="{{ route('kasir.penerimaan.iklan') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-teal-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span class="truncate">Setoran Iklan</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-teal-100 text-teal-800' : 'bg-slate-200/70 text-slate-600' }}">Faktur</span>
                    </a>

                    <!-- Setoran Sirkulasi -->
                    @php $isSub = request()->routeIs('kasir.penerimaan.sirkulasi'); @endphp
                    <a href="{{ route('kasir.penerimaan.sirkulasi') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-sky-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5" />
                            </svg>
                            <span class="truncate">Setoran Sirkulasi</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-sky-100 text-sky-800' : 'bg-slate-200/70 text-slate-600' }}">Koran</span>
                    </a>

                    <!-- Koran Langganan Instansi / Dinas -->
                    @php $isSub = request()->routeIs('kasir.penerimaan.langganan'); @endphp
                    <a href="{{ route('kasir.penerimaan.langganan') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-purple-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5m-15 10.5V10.5m18 0a48.667 48.667 0 00-10.5-.75A48.667 48.667 0 003 10.5" />
                            </svg>
                            <span class="truncate">Koran Langganan</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-purple-100 text-purple-800' : 'bg-slate-200/70 text-slate-600' }}">Dinas</span>
                    </a>

                    <!-- Penjualan Langsung -->
                    @php $isSub = request()->routeIs('kasir.penerimaan.tunai'); @endphp
                    <a href="{{ route('kasir.penerimaan.tunai') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
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
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-rose-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="truncate">Realisasi BP</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-rose-100 text-rose-800' : 'bg-slate-200/70 text-slate-600' }}">BKK</span>
                    </a>

                    <!-- Setor Kas ke Bank -->
                    @php $isSub = request()->routeIs('kasir.setoran-bank'); @endphp
                    <a href="{{ route('kasir.setoran-bank') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-blue-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                            </svg>
                            <span class="truncate">Setor Kas ke Bank</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-blue-100 text-blue-800' : 'bg-slate-200/70 text-slate-600' }}">Bank</span>
                    </a>

                    <!-- Buku Kas & Tutup -->
                    @php $isSub = request()->routeIs('kasir.buku-kas'); @endphp
                    <a href="{{ route('kasir.buku-kas') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-emerald-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18c-2.305 0-4.408.867-6 2.292m0-14.25v14.25" />
                            </svg>
                            <span class="truncate">Buku Kas & Tutup</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200/70 text-slate-600' }}">Shift</span>
                    </a>

                    <!-- Riwayat & Cetak -->
                    @php $isSub = request()->routeIs('kasir.riwayat-cetak'); @endphp
                    <a href="{{ route('kasir.riwayat-cetak') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-amber-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span class="truncate">Riwayat & Cetak</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-amber-100 text-amber-800' : 'bg-slate-200/70 text-slate-600' }}">Kwitansi</span>
                    </a>

                    <!-- Laporan Harian (LHK) -->
                    @php $isSub = request()->routeIs('kasir.laporan-harian'); @endphp
                    <a href="{{ route('kasir.laporan-harian') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSub ? 'text-teal-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                            <span class="truncate">Laporan Harian (LHK)</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-teal-100 text-teal-800' : 'bg-slate-200/70 text-slate-600' }}">Rekap</span>
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
