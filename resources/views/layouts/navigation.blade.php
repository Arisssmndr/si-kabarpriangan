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
            $isKeuangan = $user && ($isAdmin || $user->hasRole('keuangan', 'admin_keuangan'));
            $isFaktur = $user && ($isAdmin || $user->hasRole('faktur'));
            $isAnggaran = $user && ($isAdmin || $user->hasRole('anggaran'));
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
                            || request()->routeIs('jenis-iklan.*');
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

                    <!-- 5. Laporan Iklan -->
                    @php $isSub = request()->routeIs('laporan.*') || request()->routeIs('laporankoran.*') || request()->routeIs('laporanonline.*') || request()->routeIs('laporanpriangan.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('laporan.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Laporan Iklan</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $isSub ? 'bg-amber-100 text-amber-800' : 'bg-slate-200/70 text-slate-600' }}">Rekap</span>
                        </a>
                    </div>

                    <!-- 6. Kategori Media -->
                    @php $isSub = request()->routeIs('kategori-media.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('kategori-media.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Kategori Media</span>
                        </a>
                    </div>

                    <!-- 7. Tipe & Jenis Iklan -->
                    @php $isSub = request()->routeIs('jenis-iklan.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('jenis-iklan.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Tipe & Jenis Iklan</span>
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

                    <!-- Ikhtisar -->
                    <div class="pt-0.5 pb-0.5 px-3">
                        <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Ikhtisar</span>
                    </div>

                    @php $isSub = request()->routeIs('keuangan.dashboard'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('keuangan.dashboard') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Dashboard Keuangan</span>
                        </a>
                    </div>



                    <!-- Faktur & Piutang -->
                    <div class="pt-2 pb-0.5 px-3">
                        <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Faktur &amp; Piutang</span>
                    </div>

                    @php $isSub = request()->routeIs('keuangan.faktur.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('keuangan.faktur.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Faktur Penjualan</span>
                        </a>
                    </div>

                    <!-- Pengeluaran & Anggaran -->
                    <div class="pt-2 pb-0.5 px-3">
                        <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Pengeluaran &amp; Anggaran</span>
                    </div>

                    @php $isTabelMasterAnggaran = request()->routeIs('keuangan.anggaran.tabel.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('keuangan.anggaran.tabel.bidang') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isTabelMasterAnggaran ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Tabel Master (Bidang/COA/Bank)</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-purple-100 text-purple-800">DOS</span>
                        </a>
                    </div>

                    @php $isAnggaranIndex = request()->routeIs('keuangan.anggaran.index') && !request()->has('tab'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('keuangan.anggaran.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isAnggaranIndex ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Daftar BP &amp; Anggaran</span>
                        </a>
                    </div>

                    @php $isAnggaranCreate = request()->routeIs('keuangan.anggaran.create'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('keuangan.anggaran.create') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isAnggaranCreate ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">+ Entry BP Priangan</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800">Baru</span>
                        </a>
                    </div>

                    @php $isCekPencairan = request()->routeIs('keuangan.anggaran.index') && request('tab') === 'cek_pencairan'; @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('keuangan.anggaran.index', ['tab' => 'cek_pencairan']) }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isCekPencairan ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Cek Pencairan BP (Kas)</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-blue-100 text-blue-800">Kasir</span>
                        </a>
                    </div>

                    @php $isCetakAnggaran = request()->routeIs('keuangan.anggaran.cetak.*') || request()->routeIs('keuangan.anggaran.rekap-bp'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('keuangan.anggaran.cetak.hub') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isCetakAnggaran ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Pusat Cetak Anggaran (7 Laporan)</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-blue-100 text-blue-800">Cetak</span>
                        </a>
                    </div>

                    <!-- Kas & Perbendaharaan -->
                    <div class="pt-2 pb-0.5 px-3">
                        <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Kas &amp; Perbendaharaan</span>
                    </div>

                    @php $isSub = request()->routeIs('keuangan.kas.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('keuangan.kas.index') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Buku Kas (Treasury)</span>
                        </a>
                    </div>

                    @php $isSub = request()->routeIs('keuangan.laporan') || request()->routeIs('laporan.*'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('keuangan.laporan') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Laporan Keuangan</span>
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

            <!-- 5. Dropdown Divisi Kasir (Sistem DOS KAS Modern) -->
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
                        <span class="truncate">Divisi Kasir (KAS)</span>
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

                    <!-- 1. Dashboard Kasir -->
                    @php $isSub = request()->routeIs('kasir.dashboard'); @endphp
                    <div class="relative flex items-center">
                        <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                        <a href="{{ route('kasir.dashboard') }}" 
                           class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                            <span class="truncate">Dashboard Kasir</span>
                        </a>
                    </div>

                    <!-- 2. Transaksi Kas Masuk (KI - Cash In) [Dropdown] -->
                    @php
                        $isKiOpen = request()->routeIs('kasir.penerimaan.*');
                    @endphp
                    <div x-data="{ openKi: {{ $isKiOpen ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <button type="button" 
                                    @click="openKi = !openKi" 
                                    class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isKiOpen ? 'bg-emerald-50 text-emerald-900 font-bold border border-emerald-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/70 font-semibold' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="truncate">Kas Masuk</span>
                                </div>
                                <svg class="w-3 h-3 text-slate-400 transition-transform duration-200 shrink-0" :class="openKi ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>

                        <div x-cloak x-show="openKi" class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                            <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-emerald-200"></div>

                            @php $isSub = request()->routeIs('kasir.penerimaan.iklan'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-emerald-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.penerimaan.iklan') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-teal-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Input Setoran Iklan</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.penerimaan.sirkulasi'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-emerald-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.penerimaan.sirkulasi') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-sky-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Input Setoran Koran Agen</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.penerimaan.tunai'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-emerald-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.penerimaan.tunai') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-emerald-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Input Koran Sisa & Eceran</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.penerimaan.bank'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-emerald-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.penerimaan.bank') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-blue-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Input Bank / Jasa Giro BJB</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.penerimaan.langganan'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-emerald-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.penerimaan.langganan') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-indigo-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Input Piutang Langganan</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Transaksi Kas Keluar [Dropdown] -->
                    @php
                        $isKoOpen = request()->routeIs('kasir.pengeluaran.*');
                    @endphp
                    <div x-data="{ openKo: {{ $isKoOpen ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <button type="button" 
                                    @click="openKo = !openKo" 
                                    class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isKoOpen ? 'bg-rose-50 text-rose-900 font-bold border border-rose-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/70 font-semibold' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="truncate">Kas Keluar</span>
                                </div>
                                <svg class="w-3 h-3 text-slate-400 transition-transform duration-200 shrink-0" :class="openKo ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>

                        <div x-cloak x-show="openKo" class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                            <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-rose-200"></div>

                            @php $isSub = request()->routeIs('kasir.pengeluaran.realisasi-bp'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-rose-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.pengeluaran.realisasi-bp') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-rose-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Realisasi BP</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.pengeluaran.komisi'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-rose-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.pengeluaran.komisi') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-amber-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Komisi KH &amp; Insentif AE</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('keuangan.anggaran.index') && request('tab') === 'cek_pencairan'; @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-rose-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('keuangan.anggaran.index', ['tab' => 'cek_pencairan']) }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-purple-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Cek BP Anggaran</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Laporan & Mutasi Kas (Menu Cetak KAS) [Dropdown] -->
                    @php
                        $isLaporanKasirOpen = request()->routeIs('kasir.laporan.*');
                    @endphp
                    <div x-data="{ openLapK: {{ $isLaporanKasirOpen ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <button type="button" 
                                    @click="openLapK = !openLapK" 
                                    class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isLaporanKasirOpen ? 'bg-blue-50 text-blue-900 font-bold border border-blue-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/70 font-semibold' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="truncate">Laporan & Mutasi</span>
                                </div>
                                <svg class="w-3 h-3 text-slate-400 transition-transform duration-200 shrink-0" :class="openLapK ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>

                        <div x-cloak x-show="openLapK" class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                            <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-blue-200"></div>

                            @php $isSub = request()->routeIs('kasir.laporan.rekap*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-blue-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.laporan.rekap') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-blue-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Rekapitulasi Kas Masuk & Keluar</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.laporan.mutasi-bank*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-blue-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.laporan.mutasi-bank') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-blue-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Mutasi Kas Bank</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.laporan.perkiraan*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-blue-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.laporan.perkiraan') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-blue-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Mutasi per COA</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.laporan.posisi-kas*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-blue-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.laporan.posisi-kas') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-blue-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Posisi Saldo Fisik</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.laporan.kasbon*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-blue-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.laporan.kasbon') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-blue-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Monitoring Uang Muka</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Fungsi & Utilitas Kontrol [Dropdown] -->
                    @php
                        $isUtilitasOpen = request()->routeIs('kasir.utilitas.*') || request()->routeIs('kasir.buku-kas*');
                    @endphp
                    <div x-data="{ openUtilK: {{ $isUtilitasOpen ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <button type="button" 
                                    @click="openUtilK = !openUtilK" 
                                    class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isUtilitasOpen ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/70 font-semibold' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="truncate">Utilitas Kontrol</span>
                                </div>
                                <svg class="w-3 h-3 text-slate-400 transition-transform duration-200 shrink-0" :class="openUtilK ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>

                        <div x-cloak x-show="openUtilK" class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                            <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-amber-200"></div>

                            @php $isSub = request()->routeIs('kasir.utilitas.closing') || request()->routeIs('kasir.buku-kas*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.utilitas.closing') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-amber-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Tutup Kas Harian</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.utilitas.koreksi*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.utilitas.koreksi') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-amber-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Koreksi Transaksi</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.utilitas.cetak-ulang*') || request()->routeIs('kasir.riwayat-cetak*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.utilitas.cetak-ulang') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-[11px] rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-amber-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Cetak Ulang Kuitansi</span>
                                </a>
                            </div>
                        </div>
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
                    $isMasterOpen = request()->routeIs('kategori-media.*') || request()->routeIs('jenis-iklan.*');
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

                        @php $isSub = request()->routeIs('kategori-media.*'); @endphp
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <a href="{{ route('kategori-media.index') }}" 
                               class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                                <span class="truncate">Kategori Media</span>
                            </a>
                        </div>

                        @php $isSub = request()->routeIs('jenis-iklan.*'); @endphp
                        <div class="relative flex items-center">
                            <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3.5 border-b-[1.5px] border-l-[1.5px] border-slate-300/80 rounded-bl-lg pointer-events-none"></span>
                            <a href="{{ route('jenis-iklan.index') }}" 
                               class="w-full flex items-center justify-between px-3 py-1.5 text-xs rounded-xl transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-white/70 font-medium' }}">
                                <span class="truncate">Tipe & Jenis Iklan</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            @if(!$isIklan && $user->hasRole('keuangan', 'admin_keuangan'))
                <!-- ============================================== -->
                <!-- MENU KHUSUS MANAJER KEUANGAN                   -->
                <!-- ============================================== -->
                <!-- Kelompok 1: Ikhtisar & SOP -->
                <div class="pt-1 pb-1 px-3 flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Ikhtisar
                    </span>
                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800">Manajer</span>
                </div>

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



                <!-- Kelompok 2: Modul Faktur & Piutang -->
                <div class="pt-3.5 pb-1 px-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Faktur Iklan
                    </span>
                </div>

                @php 
                    $isFakturRoute = request()->routeIs('keuangan.faktur.*');
                    $activeTab = request('tab', 'keseluruhan');
                @endphp

                <div class="space-y-0.5">
                    <!-- Faktur Keseluruhan -->
                    @php $isActive = $isFakturRoute && $activeTab === 'keseluruhan'; @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.index', ['tab' => 'keseluruhan']) }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isActive ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isActive ? 'text-blue-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                <span class="truncate">Faktur Keseluruhan</span>
                            </div>
                        </a>
                    </div>

                    <!-- Faktur Susulan -->
                    @php $isActive = $isFakturRoute && $activeTab === 'susulan'; @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.index', ['tab' => 'susulan']) }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isActive ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isActive ? 'text-indigo-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="truncate">Faktur Susulan</span>
                            </div>
                        </a>
                    </div>

                    <!-- Rekap Iklan / Tanggal -->
                    @php $isActive = $isFakturRoute && $activeTab === 'rekap_tanggal'; @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.index', ['tab' => 'rekap_tanggal']) }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isActive ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isActive ? 'text-sky-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                                <span class="truncate">Rekap Iklan / Tanggal</span>
                            </div>
                        </a>
                    </div>

                    <!-- Rekap Iklan / Bulan -->
                    @php $isActive = $isFakturRoute && $activeTab === 'rekap_bulan'; @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.index', ['tab' => 'rekap_bulan']) }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isActive ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isActive ? 'text-violet-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                </svg>
                                <span class="truncate">Rekap Iklan / Bulan</span>
                            </div>
                        </a>
                    </div>

                    <!-- Rekap Tunai / Bln -->
                    @php $isActive = $isFakturRoute && $activeTab === 'tunai'; @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.index', ['tab' => 'tunai']) }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isActive ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isActive ? 'text-emerald-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v8.25m0-8.25a9.75 9.75 0 011.5-.118m0 0h16.5m0 0a9.75 9.75 0 011.5.118m0 0v8.25m0-8.25a.75.75 0 00-.75-.75h-.75M3.75 14.25v.75a.75.75 0 00.75.75h15a.75.75 0 00.75-.75v-.75m-16.5 0v3.75m16.5-3.75v3.75m-16.5 0a9.75 9.75 0 001.5.118m15-1.118a9.75 9.75 0 011.5-.118M9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75s.168-.75.375-.75.375.336.375.75zm6 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-3 4.5c.828 0 1.5-.895 1.5-2s-.672-2-1.5-2-1.5.895-1.5 2 .672 2 1.5 2z" />
                                </svg>
                                <span class="truncate">Rekap Tunai / Bln</span>
                            </div>
                        </a>
                    </div>

                    <!-- Rekap Piutang -->
                    @php $isActive = $isFakturRoute && $activeTab === 'piutang'; @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.index', ['tab' => 'piutang']) }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isActive ? 'bg-white text-amber-900 font-bold shadow-xs border border-amber-300' : 'text-amber-700 hover:text-amber-900 hover:bg-amber-50/70 font-semibold' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isActive ? 'text-amber-600' : 'text-amber-600/70 group-hover:text-amber-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>
                                <span class="truncate">Rekap Faktur Piutang</span>
                            </div>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-800 shrink-0">Piutang</span>
                        </a>
                    </div>

                    <!-- Stopper & Bonus -->
                    @php $isActive = $isFakturRoute && $activeTab === 'stopper_bonus'; @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.index', ['tab' => 'stopper_bonus']) }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isActive ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isActive ? 'text-rose-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.386l4.242-2.49c.827-.486 1.096-1.516.59-2.261L11.16 3.659A2.25 2.25 0 009.568 3z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                                </svg>
                                <span class="truncate">Stopper &amp; Bonus</span>
                            </div>
                        </a>
                    </div>

                    <!-- Stock Opname Faktur -->
                    @php $isActive = $isFakturRoute && $activeTab === 'stock_opname'; @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.index', ['tab' => 'stock_opname']) }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isActive ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isActive ? 'text-teal-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                                <span class="truncate">Stock Opname Faktur</span>
                            </div>
                        </a>
                    </div>

                    <!-- Submenu Faktur Sirkulasi (DOS faktur1.exe) -->
                    <div class="pt-3 pb-1 px-3 border-t border-slate-200/60 mt-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 font-mono flex items-center gap-1.5">
                            <span>🗞️</span>
                            <span>Faktur Sirkulasi</span>
                        </span>
                    </div>

                    @php $isSrkIndex = request()->routeIs('keuangan.faktur.sirkulasi.index'); @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.sirkulasi.index') }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSrkIndex ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSrkIndex ? 'text-indigo-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                                </svg>
                                <span class="truncate">Sirkulasi Keseluruhan</span>
                            </div>
                        </a>
                    </div>

                    @php $isSrkSusulan = request()->routeIs('keuangan.faktur.sirkulasi.susulan'); @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.sirkulasi.susulan') }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSrkSusulan ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSrkSusulan ? 'text-amber-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.948c0-.621-.504-1.125-1.125-1.125H4.125C3.504 5.5 3 6.004 3 6.625v7.625M14.25 7.5v6.75m0 0H3" />
                                </svg>
                                <span class="truncate">Sirkulasi Susulan</span>
                            </div>
                        </a>
                    </div>

                    @php $isSrkRekap = request()->routeIs('keuangan.faktur.sirkulasi.rekap*'); @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.sirkulasi.rekap') }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSrkRekap ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSrkRekap ? 'text-sky-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zM9 13.5h1.5m-1.5 3h6m-6-6h6" />
                                </svg>
                                <span class="truncate">Rekap Faktur Sirkulasi</span>
                            </div>
                        </a>
                    </div>

                    @php $isSrkInsidentil = request()->routeIs('keuangan.faktur.sirkulasi.insidentil'); @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.sirkulasi.insidentil') }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSrkInsidentil ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSrkInsidentil ? 'text-teal-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                <span class="truncate">Faktur Insidentil</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Kelompok 3: Modul Anggaran -->
                <div class="pt-3.5 pb-1 px-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Pengeluaran &amp; Anggaran
                    </span>
                </div>

                @php $isTabelMasterAnggaran = request()->routeIs('keuangan.anggaran.tabel.*'); @endphp
                <div>
                    <a href="{{ route('keuangan.anggaran.tabel.bidang') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isTabelMasterAnggaran ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            <span class="truncate">Tabel Master (Bidang/COA/Bank)</span>
                        </div>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-purple-100 text-purple-800">DOS</span>
                    </a>
                </div>

                @php $isAnggaranIndex = request()->routeIs('keuangan.anggaran.index') && !request()->has('tab'); @endphp
                <div>
                    <a href="{{ route('keuangan.anggaran.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAnggaranIndex ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /></svg>
                            <span class="truncate">Persetujuan &amp; BP Priangan</span>
                        </div>
                        @if($isAnggaranIndex)
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600 shrink-0"></span>
                        @endif
                    </a>
                </div>

                @php $isAnggaranCreate = request()->routeIs('keuangan.anggaran.create'); @endphp
                <div class="pt-1">
                    <a href="{{ route('keuangan.anggaran.create') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAnggaranCreate ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            <span class="truncate">+ Entry BP Priangan</span>
                        </div>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800">Baru</span>
                    </a>
                </div>

                @php $isCekPencairan = request()->routeIs('keuangan.anggaran.index') && request('tab') === 'cek_pencairan'; @endphp
                <div class="pt-1">
                    <a href="{{ route('keuangan.anggaran.index', ['tab' => 'cek_pencairan']) }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isCekPencairan ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="truncate">Cek Pencairan BP (Kas)</span>
                        </div>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-blue-100 text-blue-800">Kasir</span>
                    </a>
                </div>

                @php $isCetakAnggaran = request()->routeIs('keuangan.anggaran.cetak.*') || request()->routeIs('keuangan.anggaran.rekap-bp'); @endphp
                <div class="pt-1">
                    <a href="{{ route('keuangan.anggaran.cetak.hub') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isCetakAnggaran ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                            <span class="truncate">Pusat Cetak Anggaran</span>
                        </div>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-blue-100 text-blue-800">7 Laporan</span>
                    </a>
                </div>

                <!-- Kelompok 4: Modul Kas & Laporan -->
                <div class="pt-3.5 pb-1 px-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Kas &amp; Perbendaharaan
                    </span>
                </div>

                @php $isKas = request()->routeIs('keuangan.kas.*'); @endphp
                <div>
                    <a href="{{ route('keuangan.kas.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isKas ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="truncate">Buku Kas (Treasury)</span>
                        </div>
                    </a>
                </div>

                @php $isLapKeu = request()->routeIs('keuangan.laporan') || request()->routeIs('laporan.*'); @endphp
                <div class="pt-1">
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

            <!-- ============================================== -->
            <!-- MENU KHUSUS STAF FAKTUR                        -->
            <!-- ============================================== -->
            @if(!$isIklan && $user->hasRole('faktur'))
                <div class="pt-3.5 pb-1 px-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        Faktur Iklan
                    </span>
                </div>

                @php 
                    $isFakturRoute = request()->routeIs('keuangan.faktur.*');
                    $activeTab = request('tab', 'keseluruhan');
                @endphp

                <div class="space-y-0.5">
                    <!-- Faktur Keseluruhan -->
                    @php $isActive = $isFakturRoute && $activeTab === 'keseluruhan'; @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.index', ['tab' => 'keseluruhan']) }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isActive ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isActive ? 'text-blue-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                <span class="truncate">Faktur Keseluruhan</span>
                            </div>
                        </a>
                    </div>

                    <!-- Faktur Susulan -->
                    @php $isActive = $isFakturRoute && $activeTab === 'susulan'; @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.index', ['tab' => 'susulan']) }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isActive ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isActive ? 'text-indigo-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="truncate">Faktur Susulan</span>
                            </div>
                        </a>
                    </div>

                    <!-- Rekap Iklan / Tanggal -->
                    @php $isActive = $isFakturRoute && $activeTab === 'rekap_tanggal'; @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.index', ['tab' => 'rekap_tanggal']) }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isActive ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isActive ? 'text-sky-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                                <span class="truncate">Rekap Iklan / Tanggal</span>
                            </div>
                        </a>
                    </div>

                    <!-- Rekap Iklan / Bulan -->
                    @php $isActive = $isFakturRoute && $activeTab === 'rekap_bulan'; @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.index', ['tab' => 'rekap_bulan']) }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isActive ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isActive ? 'text-violet-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                </svg>
                                <span class="truncate">Rekap Iklan / Bulan</span>
                            </div>
                        </a>
                    </div>

                    <!-- Rekap Tunai / Bln -->
                    @php $isActive = $isFakturRoute && $activeTab === 'tunai'; @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.index', ['tab' => 'tunai']) }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isActive ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isActive ? 'text-emerald-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v8.25m0-8.25a9.75 9.75 0 011.5-.118m0 0h16.5m0 0a9.75 9.75 0 011.5.118m0 0v8.25m0-8.25a.75.75 0 00-.75-.75h-.75M3.75 14.25v.75a.75.75 0 00.75.75h15a.75.75 0 00.75-.75v-.75m-16.5 0v3.75m16.5-3.75v3.75m-16.5 0a9.75 9.75 0 001.5.118m15-1.118a9.75 9.75 0 011.5-.118M9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75s.168-.75.375-.75.375.336.375.75zm6 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-3 4.5c.828 0 1.5-.895 1.5-2s-.672-2-1.5-2-1.5.895-1.5 2 .672 2 1.5 2z" />
                                </svg>
                                <span class="truncate">Rekap Tunai / Bln</span>
                            </div>
                        </a>
                    </div>

                    <!-- Rekap Piutang -->
                    @php $isActive = $isFakturRoute && $activeTab === 'piutang'; @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.index', ['tab' => 'piutang']) }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isActive ? 'bg-white text-amber-900 font-bold shadow-xs border border-amber-300' : 'text-amber-700 hover:text-amber-900 hover:bg-amber-50/70 font-semibold' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isActive ? 'text-amber-600' : 'text-amber-600/70 group-hover:text-amber-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>
                                <span class="truncate">Rekap Faktur Piutang</span>
                            </div>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-800 shrink-0">Piutang</span>
                        </a>
                    </div>

                    <!-- Stopper & Bonus -->
                    @php $isActive = $isFakturRoute && $activeTab === 'stopper_bonus'; @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.index', ['tab' => 'stopper_bonus']) }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isActive ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isActive ? 'text-rose-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.386l4.242-2.49c.827-.486 1.096-1.516.59-2.261L11.16 3.659A2.25 2.25 0 009.568 3z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                                </svg>
                                <span class="truncate">Stopper &amp; Bonus</span>
                            </div>
                        </a>
                    </div>

                    <!-- Stock Opname Faktur -->
                    @php $isActive = $isFakturRoute && $activeTab === 'stock_opname'; @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.index', ['tab' => 'stock_opname']) }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isActive ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isActive ? 'text-teal-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                                <span class="truncate">Stock Opname Faktur</span>
                            </div>
                        </a>
                    </div>

                    <!-- Submenu Faktur Sirkulasi (DOS faktur1.exe) -->
                    <div class="pt-3 pb-1 px-3 border-t border-slate-200/60 mt-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 font-mono flex items-center gap-1.5">
                            <span>🗞️</span>
                            <span>Faktur Sirkulasi</span>
                        </span>
                    </div>

                    @php $isSrkIndexMobile = request()->routeIs('keuangan.faktur.sirkulasi.index'); @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.sirkulasi.index') }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSrkIndexMobile ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSrkIndexMobile ? 'text-indigo-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                                </svg>
                                <span class="truncate">Sirkulasi Keseluruhan</span>
                            </div>
                        </a>
                    </div>

                    @php $isSrkSusulanMobile = request()->routeIs('keuangan.faktur.sirkulasi.susulan'); @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.sirkulasi.susulan') }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSrkSusulanMobile ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSrkSusulanMobile ? 'text-amber-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.948c0-.621-.504-1.125-1.125-1.125H4.125C3.504 5.5 3 6.004 3 6.625v7.625M14.25 7.5v6.75m0 0H3" />
                                </svg>
                                <span class="truncate">Sirkulasi Susulan</span>
                            </div>
                        </a>
                    </div>

                    @php $isSrkRekapMobile = request()->routeIs('keuangan.faktur.sirkulasi.rekap*'); @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.sirkulasi.rekap') }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSrkRekapMobile ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSrkRekapMobile ? 'text-sky-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zM9 13.5h1.5m-1.5 3h6m-6-6h6" />
                                </svg>
                                <span class="truncate">Rekap Faktur Sirkulasi</span>
                            </div>
                        </a>
                    </div>

                    @php $isSrkInsidentilMobile = request()->routeIs('keuangan.faktur.sirkulasi.insidentil'); @endphp
                    <div>
                        <a href="{{ route('keuangan.faktur.sirkulasi.insidentil') }}" 
                           class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isSrkInsidentilMobile ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 shrink-0 transition-colors {{ $isSrkInsidentilMobile ? 'text-teal-600' : 'text-slate-500 group-hover:text-slate-800' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                <span class="truncate">Faktur Insidentil</span>
                            </div>
                        </a>
                    </div>
                </div>

            @endif

            <!-- ============================================== -->
            <!-- MENU KHUSUS STAF ANGGARAN                      -->
            <!-- ============================================== -->
            <!-- ============================================== -->
            <!-- MENU KHUSUS STAF ANGGARAN (DOS ANGGARAN.EXE)   -->
            <!-- ============================================== -->
            @if(!$isIklan && $user->hasRole('anggaran'))
                <!-- 1. KELOMPOK TABEL (Master Data ANGGARAN.EXE) -->
                <div class="pt-1 pb-1 px-3 flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                        1. Menu Tabel
                    </span>
                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-purple-100 text-purple-800">Master</span>
                </div>

                @php $isTabelBidang = request()->routeIs('keuangan.anggaran.tabel.bidang'); @endphp
                <div>
                    <a href="{{ route('keuangan.anggaran.tabel.bidang') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isTabelBidang ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            <span class="truncate">Bidang (Kode 01-07)</span>
                        </div>
                        @if($isTabelBidang)
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600 shrink-0"></span>
                        @endif
                    </a>
                </div>

                @php $isTabelPerkiraan = request()->routeIs('keuangan.anggaran.tabel.perkiraan'); @endphp
                <div class="pt-0.5">
                    <a href="{{ route('keuangan.anggaran.tabel.perkiraan') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isTabelPerkiraan ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            <span class="truncate">Perkiraan Priangan</span>
                        </div>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-slate-100 text-slate-700">COA</span>
                    </a>
                </div>

                @php $isTabelBank = request()->routeIs('keuangan.anggaran.tabel.rekening-bank'); @endphp
                <div class="pt-0.5">
                    <a href="{{ route('keuangan.anggaran.tabel.rekening-bank') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isTabelBank ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                            <span class="truncate">Rekening Bank Priangan</span>
                        </div>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-slate-100 text-slate-700">Bank</span>
                    </a>
                </div>

                <!-- 2. KELOMPOK ENTRY (DOS ANGGARAN.EXE) -->
                <div class="pt-3 pb-1 px-3 flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                        2. Menu Entry
                    </span>
                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800">Transaksi</span>
                </div>

                <!-- 1. RAB Priangan -->
                @php $isRab = request()->routeIs('keuangan.anggaran.entry.rab'); @endphp
                <div>
                    <a href="{{ route('keuangan.anggaran.entry.rab') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isRab ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                            <span class="truncate">RAB Priangan</span>
                        </div>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800">Plafon</span>
                    </a>
                </div>

                <!-- 2. BP Priangan (+ Entry & Daftar) -->
                @php $isAnggaranCreate = request()->routeIs('keuangan.anggaran.create'); @endphp
                <div class="pt-0.5">
                    <a href="{{ route('keuangan.anggaran.create') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAnggaranCreate ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            <span class="truncate">+ Entry BP Priangan</span>
                        </div>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-purple-100 text-purple-800">Baru</span>
                    </a>
                </div>

                @php $isAnggaranIndex = request()->routeIs('keuangan.anggaran.index') && !request()->has('tab'); @endphp
                <div class="pt-0.5">
                    <a href="{{ route('keuangan.anggaran.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isAnggaranIndex ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /></svg>
                            <span class="truncate">Daftar BP Priangan</span>
                        </div>
                        @if($isAnggaranIndex)
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600 shrink-0"></span>
                        @endif
                    </a>
                </div>

                <!-- 3. Tutup Anggaran -->
                @php $isTutup = request()->routeIs('keuangan.anggaran.entry.tutup-anggaran*'); @endphp
                <div class="pt-0.5">
                    <a href="{{ route('keuangan.anggaran.entry.tutup-anggaran') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isTutup ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            <span class="truncate">Tutup Anggaran</span>
                        </div>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-rose-100 text-rose-800">Closing</span>
                    </a>
                </div>

                <!-- 4. DP Kasbon -->
                @php $isKasbon = request()->routeIs('keuangan.anggaran.entry.dp-kasbon*'); @endphp
                <div class="pt-0.5">
                    <a href="{{ route('keuangan.anggaran.entry.dp-kasbon') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isKasbon ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            <span class="truncate">DP Kasbon</span>
                        </div>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-amber-100 text-amber-800">Panjar</span>
                    </a>
                </div>

                <!-- 3. KELOMPOK CETAK (DOS ANGGARAN.EXE) -->
                <div class="pt-3 pb-1 px-3 flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                        3. Menu Cetak
                    </span>
                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-blue-100 text-blue-800">7 Laporan</span>
                </div>

                <!-- Pusat Cetak Hub -->
                @php $isCetakHub = request()->routeIs('keuangan.anggaran.cetak.*'); @endphp
                <div>
                    <a href="{{ route('keuangan.anggaran.cetak.hub') }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isCetakHub ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                            <span class="truncate">Pusat Cetak Anggaran</span>
                        </div>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-blue-100 text-blue-800">Hub</span>
                    </a>
                </div>

                @php $isSeluruhBidang = request()->routeIs('keuangan.anggaran.cetak.seluruh-bidang'); @endphp
                <div class="pt-0.5">
                    <a href="{{ route('keuangan.anggaran.cetak.seluruh-bidang') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-[11px] rounded-xl transition-all duration-150 {{ $isSeluruhBidang ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                        <div class="flex items-center gap-2 min-w-0 pl-1">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isSeluruhBidang ? 'bg-purple-600' : 'bg-slate-300' }}"></span>
                            <span class="truncate">1. Seluruh Bidang</span>
                        </div>
                    </a>
                </div>

                @php $isPerBidang = request()->routeIs('keuangan.anggaran.cetak.per-bidang'); @endphp
                <div class="pt-0.5">
                    <a href="{{ route('keuangan.anggaran.cetak.per-bidang') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-[11px] rounded-xl transition-all duration-150 {{ $isPerBidang ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                        <div class="flex items-center gap-2 min-w-0 pl-1">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isPerBidang ? 'bg-purple-600' : 'bg-slate-300' }}"></span>
                            <span class="truncate">2. Per Bidang</span>
                        </div>
                    </a>
                </div>

                @php $isRekapBpTgl = request()->routeIs('keuangan.anggaran.cetak.bp-tanggal'); @endphp
                <div class="pt-0.5">
                    <a href="{{ route('keuangan.anggaran.cetak.bp-tanggal') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-[11px] rounded-xl transition-all duration-150 {{ $isRekapBpTgl ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                        <div class="flex items-center gap-2 min-w-0 pl-1">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isRekapBpTgl ? 'bg-purple-600' : 'bg-slate-300' }}"></span>
                            <span class="truncate">3. BP Per Tanggal</span>
                        </div>
                    </a>
                </div>

                @php $isBpBulan = request()->routeIs('keuangan.anggaran.cetak.bp-bulan'); @endphp
                <div class="pt-0.5">
                    <a href="{{ route('keuangan.anggaran.cetak.bp-bulan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-[11px] rounded-xl transition-all duration-150 {{ $isBpBulan ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                        <div class="flex items-center gap-2 min-w-0 pl-1">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isBpBulan ? 'bg-purple-600' : 'bg-slate-300' }}"></span>
                            <span class="truncate">4. BP Per Bulan</span>
                        </div>
                    </a>
                </div>

                @php $isRekapCoa = request()->routeIs('keuangan.anggaran.cetak.rekap-bidang-coa'); @endphp
                <div class="pt-0.5">
                    <a href="{{ route('keuangan.anggaran.cetak.rekap-bidang-coa') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-[11px] rounded-xl transition-all duration-150 {{ $isRekapCoa ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                        <div class="flex items-center gap-2 min-w-0 pl-1">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isRekapCoa ? 'bg-purple-600' : 'bg-slate-300' }}"></span>
                            <span class="truncate">5. Realisasi (COA)</span>
                        </div>
                    </a>
                </div>

                @php $isCetakUlangBp = request()->routeIs('keuangan.anggaran.cetak.cetak-ulang-bp'); @endphp
                <div class="pt-0.5">
                    <a href="{{ route('keuangan.anggaran.cetak.cetak-ulang-bp') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-[11px] rounded-xl transition-all duration-150 {{ $isCetakUlangBp ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                        <div class="flex items-center gap-2 min-w-0 pl-1">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isCetakUlangBp ? 'bg-purple-600' : 'bg-slate-300' }}"></span>
                            <span class="truncate">6. Cetak Ulang BP</span>
                        </div>
                    </a>
                </div>

                @php $isTahunan = request()->routeIs('keuangan.anggaran.cetak.tahunan'); @endphp
                <div class="pt-0.5">
                    <a href="{{ route('keuangan.anggaran.cetak.tahunan') }}" 
                       class="group flex items-center justify-between px-3 py-1.5 text-[11px] rounded-xl transition-all duration-150 {{ $isTahunan ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-200/40 font-medium' }}">
                        <div class="flex items-center gap-2 min-w-0 pl-1">
                            <span class="w-1.5 h-1.5 rounded-full {{ $isTahunan ? 'bg-purple-600' : 'bg-slate-300' }}"></span>
                            <span class="truncate">7. Realisasi Per Tahun</span>
                        </div>
                    </a>
                </div>

                <!-- 4. KELOMPOK RELASI KASIR -->
                <div class="pt-3 pb-1 px-3 flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                        4. Relasi Kasir
                    </span>
                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-blue-100 text-blue-800">Kasir</span>
                </div>

                @php $isCekPencairan = request()->routeIs('keuangan.anggaran.index') && request('tab') === 'cek_pencairan'; @endphp
                <div class="pt-0.5">
                    <a href="{{ route('keuangan.anggaran.index', ['tab' => 'cek_pencairan']) }}" 
                       class="group flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 {{ $isCekPencairan ? 'bg-white text-slate-900 font-bold shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="truncate">Cek Pencairan BP (Kasir)</span>
                        </div>
                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-blue-100 text-blue-800">Kasir</span>
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

                <!-- Navigasi Menu Operasional Divisi Kasir (Sistem KAS) -->
                <div class="space-y-1.5 pt-1">
                    <div class="pt-2 pb-0.5 px-3 flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Menu Operasional Kasir
                        </span>
                    </div>

                    <!-- 1. Transaksi Kas Masuk [Dropdown] -->
                    @php
                        $isKiOpen2 = request()->routeIs('kasir.penerimaan.*');
                    @endphp
                    <div x-data="{ openKi: {{ $isKiOpen2 ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <button type="button" 
                                    @click="openKi = !openKi" 
                                    class="w-full flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isKiOpen2 ? 'bg-emerald-50 text-emerald-900 font-bold border border-emerald-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="truncate">Kas Masuk</span>
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0" :class="openKi ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>

                        <div x-cloak x-show="openKi" class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                            <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-emerald-200"></div>

                            @php $isSub = request()->routeIs('kasir.penerimaan.iklan'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-emerald-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.penerimaan.iklan') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-teal-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Input Setoran Iklan</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.penerimaan.sirkulasi'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-emerald-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.penerimaan.sirkulasi') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-sky-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Input Setoran Koran Agen</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.penerimaan.tunai'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-emerald-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.penerimaan.tunai') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-emerald-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Input Koran Sisa & Eceran</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.penerimaan.bank'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-emerald-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.penerimaan.bank') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-blue-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Input Bank / Jasa Giro BJB</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.penerimaan.langganan'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-emerald-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.penerimaan.langganan') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-indigo-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Input Piutang Langganan</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Transaksi Kas Keluar [Dropdown] -->
                    @php
                        $isKoOpen2 = request()->routeIs('kasir.pengeluaran.*');
                    @endphp
                    <div x-data="{ openKo: {{ $isKoOpen2 ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <button type="button" 
                                    @click="openKo = !openKo" 
                                    class="w-full flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isKoOpen2 ? 'bg-rose-50 text-rose-900 font-bold border border-rose-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="truncate">Kas Keluar</span>
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0" :class="openKo ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>

                        <div x-cloak x-show="openKo" class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                            <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-rose-200"></div>

                            @php $isSub = request()->routeIs('kasir.pengeluaran.realisasi-bp'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-rose-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.pengeluaran.realisasi-bp') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-rose-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Realisasi BP</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.pengeluaran.komisi'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-rose-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.pengeluaran.komisi') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-amber-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Komisi KH &amp; Insentif AE</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('keuangan.anggaran.index') && request('tab') === 'cek_pencairan'; @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-rose-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('keuangan.anggaran.index', ['tab' => 'cek_pencairan']) }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-purple-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Cek BP Anggaran</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Laporan & Mutasi Kas (Menu Cetak KAS) [Dropdown] -->
                    @php
                        $isLaporanKasirOpen2 = request()->routeIs('kasir.laporan.*');
                    @endphp
                    <div x-data="{ openLapK: {{ $isLaporanKasirOpen2 ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <button type="button" 
                                    @click="openLapK = !openLapK" 
                                    class="w-full flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isLaporanKasirOpen2 ? 'bg-blue-50 text-blue-900 font-bold border border-blue-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="truncate">Laporan & Mutasi</span>
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0" :class="openLapK ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>

                        <div x-cloak x-show="openLapK" class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                            <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-blue-200"></div>

                            @php $isSub = request()->routeIs('kasir.laporan.rekap*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-blue-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.laporan.rekap') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-blue-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Rekapitulasi Kas Masuk & Keluar</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.laporan.mutasi-bank*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-blue-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.laporan.mutasi-bank') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-blue-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Mutasi Kas Bank</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.laporan.perkiraan*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-blue-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.laporan.perkiraan') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-blue-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Mutasi per COA</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.laporan.posisi-kas*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-blue-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.laporan.posisi-kas') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-blue-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Posisi Saldo Kas Fisik</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.laporan.kasbon*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-blue-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.laporan.kasbon') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-blue-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Monitoring Uang Muka / Kasbon</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Fungsi & Utilitas Kontrol [Dropdown] -->
                    @php
                        $isUtilitasOpen2 = request()->routeIs('kasir.utilitas.*') || request()->routeIs('kasir.buku-kas*');
                    @endphp
                    <div x-data="{ openUtilK: {{ $isUtilitasOpen2 ? 'true' : 'false' }} }" class="space-y-1">
                        <div class="relative flex items-center">
                            <button type="button" 
                                    @click="openUtilK = !openUtilK" 
                                    class="w-full flex items-center justify-between px-3 py-2 text-xs rounded-xl transition-all duration-150 cursor-pointer {{ $isUtilitasOpen2 ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/40 font-semibold' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="truncate">Utilitas Kontrol</span>
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0" :class="openUtilK ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>

                        <div x-cloak x-show="openUtilK" class="relative pl-6 pt-0.5 pb-0.5 space-y-1">
                            <div class="absolute left-3 top-0 bottom-3 w-[1.5px] bg-amber-200"></div>

                            @php $isSub = request()->routeIs('kasir.utilitas.closing') || request()->routeIs('kasir.buku-kas*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.utilitas.closing') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-amber-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Tutup Kas Harian (Closing)</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.utilitas.koreksi*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.utilitas.koreksi') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-amber-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Perbaikan & Koreksi</span>
                                </a>
                            </div>

                            @php $isSub = request()->routeIs('kasir.utilitas.cetak-ulang*') || request()->routeIs('kasir.riwayat-cetak*'); @endphp
                            <div class="relative flex items-center">
                                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-3 h-3 border-b-[1.5px] border-l-[1.5px] border-amber-300 rounded-bl-lg pointer-events-none"></span>
                                <a href="{{ route('kasir.utilitas.cetak-ulang') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1.5 text-xs rounded-lg transition-all duration-150 {{ $isSub ? 'bg-white text-slate-900 font-bold shadow-2xs border border-amber-300' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                                    <span class="truncate">Cetak Ulang Kwitansi</span>
                                </a>
                            </div>
                        </div>
                    </div>
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
