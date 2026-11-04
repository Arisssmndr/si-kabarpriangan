@props(['activeTab' => 'keseluruhan', 'activeCategory' => 'iklan'])

<div class="space-y-4">
    <!-- 1. Tab Level 1: Pemisah 2 Tabel Utama (Iklan vs Sirkulasi) -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
        <div class="flex items-center gap-2">
            <!-- Tab Iklan (Biru 30% saat aktif) -->
            <a href="{{ route('keuangan.faktur.index', ['tab' => 'keseluruhan']) }}" 
               class="px-4 py-2.5 rounded-xl text-xs font-black flex items-center gap-2 transition-all duration-150 {{ $activeCategory === 'iklan' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700' }}">
                <span>📰</span>
                <span>TABEL FAKTUR IKLAN</span>
                @php
                    $countIklan = \App\Models\Faktur::where('kategori', 'iklan')->count();
                @endphp
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $activeCategory === 'iklan' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">
                    {{ $countIklan }}
                </span>
            </a>

            <!-- Tab Sirkulasi -->
            <a href="{{ route('keuangan.faktur.sirkulasi.index') }}" 
               class="px-4 py-2.5 rounded-xl text-xs font-black flex items-center gap-2 transition-all duration-150 {{ $activeCategory === 'sirkulasi' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700' }}">
                <span>🗞️</span>
                <span>TABEL FAKTUR SIRKULASI</span>
                @php
                    $countSirkulasi = \App\Models\Faktur::where('kategori', 'sirkulasi')->count();
                @endphp
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $activeCategory === 'sirkulasi' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">
                    {{ $countSirkulasi }}
                </span>
            </a>
        </div>

        <div class="hidden sm:flex items-center gap-2.5">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-blue-50/70 border border-blue-200 text-blue-900 text-[11px] font-semibold">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span>Sinkronisasi Otomatis Divisi</span>
            </span>
            <!-- Tombol Aksen Kuning 10% (Action penting penagihan piutang) -->
            <a href="{{ route('keuangan.faktur.index', ['tab' => 'piutang']) }}" 
               class="h-8.5 px-3.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-amber-950 border border-amber-500/30 text-[11px] font-extrabold flex items-center gap-1.5 transition shadow-xs cursor-pointer">
                <svg class="w-3.5 h-3.5 text-amber-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <span>Faktur Piutang &amp; Surat Tugas</span>
            </a>
        </div>
    </div>

    <!-- 2. Tab Level 2: Sub-Menu Khusus Tabel Iklan -->
    @if($activeCategory === 'iklan')
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <!-- 1. Keseluruhan -->
            <a href="{{ route('keuangan.faktur.index', ['tab' => 'keseluruhan']) }}" 
               class="px-3 py-1.5 rounded-xl font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'keseluruhan' ? 'bg-blue-50 text-blue-700 border-2 border-blue-600 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-blue-700 hover:bg-blue-50/50' }}">
                <span>📋</span>
                <span>Faktur Iklan Keseluruhan</span>
            </a>

            <!-- 2. Rekap Tanggal & Bulan -->
            <a href="{{ route('keuangan.faktur.index', ['tab' => 'rekap_tanggal']) }}" 
               class="px-3 py-1.5 rounded-xl font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'rekap' || $activeTab === 'rekap_tanggal' ? 'bg-blue-50 text-blue-700 border-2 border-blue-600 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-blue-700 hover:bg-blue-50/50' }}">
                <span>📊</span>
                <span>Rekap Faktur Iklan (Tgl/Bulan)</span>
            </a>

            <!-- 3. Faktur Susulan -->
            <a href="{{ route('keuangan.faktur.index', ['tab' => 'susulan']) }}" 
               class="px-3 py-1.5 rounded-xl font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'susulan' ? 'bg-blue-50 text-blue-700 border-2 border-blue-600 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-blue-700 hover:bg-blue-50/50' }}">
                <span>⚡</span>
                <span>Faktur Susulan</span>
                @php
                    $susulanIklanCount = \App\Models\Faktur::where('kategori', 'iklan')->where('tipe_faktur', 'susulan')->count();
                @endphp
                @if($susulanIklanCount > 0)
                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-blue-100 text-blue-800">
                        {{ $susulanIklanCount }}
                    </span>
                @endif
            </a>

            <!-- 4. Rekap Tunai -->
            <a href="{{ route('keuangan.faktur.index', ['tab' => 'tunai']) }}" 
               class="px-3 py-1.5 rounded-xl font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'tunai' ? 'bg-blue-50 text-blue-700 border-2 border-blue-600 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-blue-700 hover:bg-blue-50/50' }}">
                <span>💵</span>
                <span>Rekap Faktur Iklan Tunai</span>
            </a>

            <!-- 5. Rekap Piutang (Aksen Kuning 10% untuk highlight piutang) -->
            <a href="{{ route('keuangan.faktur.index', ['tab' => 'piutang']) }}" 
               class="px-3 py-1.5 rounded-xl font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'piutang' ? 'bg-amber-50 text-amber-900 border-2 border-amber-400 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-amber-800 hover:bg-amber-50/60' }}">
                <span>⏳</span>
                <span>Rekap Faktur Iklan Piutang</span>
                @php
                    $piutangBadge = \App\Models\Faktur::where('kategori', 'iklan')->where('sisa_piutang', '>', 0)->count();
                @endphp
                @if($piutangBadge > 0)
                    <span class="text-[9px] font-black px-1.5 py-0.2 rounded-full bg-amber-400 text-amber-950 shadow-2xs">
                        {{ $piutangBadge }}
                    </span>
                @endif
            </a>

            <!-- 6. Rekap Stopper & Bonus -->
            <a href="{{ route('keuangan.faktur.index', ['tab' => 'stopper_bonus']) }}" 
               class="px-3 py-1.5 rounded-xl font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'stopper_bonus' ? 'bg-blue-50 text-blue-700 border-2 border-blue-600 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-blue-700 hover:bg-blue-50/50' }}">
                <span>🎁</span>
                <span>Stopper &amp; Bonus</span>
                @php
                    $stopperBonusCount = \App\Models\Faktur::where('kategori', 'iklan')->whereIn('tipe_faktur', ['stopper', 'bonus'])->count();
                @endphp
                @if($stopperBonusCount > 0)
                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-blue-100 text-blue-800">
                        {{ $stopperBonusCount }}
                    </span>
                @endif
            </a>

            <!-- 7. Stok Opname -->
            <a href="{{ route('keuangan.faktur.stok-opname') }}" 
               class="px-3 py-1.5 rounded-xl font-bold flex items-center gap-1.5 shrink-0 transition {{ request()->routeIs('keuangan.faktur.stok-opname*') || request()->routeIs('keuangan.faktur.stock-opname*') || in_array($activeTab, ['stock_opname', 'stok_opname']) ? 'bg-blue-50 text-blue-700 border-2 border-blue-600 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-blue-700 hover:bg-blue-50/50' }}">
                <span>📦</span>
                <span>Stok Opname Faktur</span>
            </a>
        </div>
    @elseif($activeCategory === 'sirkulasi')
        <!-- Tab Level 2: Sub-Menu Khusus Tabel Sirkulasi -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs border-b border-slate-100 no-scrollbar">
            <!-- 1. Keseluruhan -->
            <a href="{{ route('keuangan.faktur.sirkulasi.index') }}" 
               class="px-3 py-1.5 rounded-xl font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'keseluruhan' || $activeTab === 'sirkulasi' ? 'bg-blue-50 text-blue-700 border-2 border-blue-600 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-blue-700 hover:bg-blue-50/50' }}">
                <span>🗞️</span>
                <span>Faktur Sirkulasi Keseluruhan</span>
            </a>

            <!-- 2. Susulan -->
            <a href="{{ route('keuangan.faktur.sirkulasi.susulan') }}" 
               class="px-3 py-1.5 rounded-xl font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'susulan' ? 'bg-blue-50 text-blue-700 border-2 border-blue-600 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-blue-700 hover:bg-blue-50/50' }}">
                <span>⚡</span>
                <span>Faktur Sirkulasi Susulan</span>
                @php
                    $susulanSrkBadge = \App\Models\Faktur::where('kategori', 'sirkulasi')->where('tipe_faktur', 'susulan')->count();
                @endphp
                @if($susulanSrkBadge > 0)
                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-blue-100 text-blue-800">
                        {{ $susulanSrkBadge }}
                    </span>
                @endif
            </a>

            <!-- 3. Rekap Sirkulasi -->
            <a href="{{ route('keuangan.faktur.sirkulasi.rekap') }}" 
               class="px-3 py-1.5 rounded-xl font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'rekap' ? 'bg-blue-50 text-blue-700 border-2 border-blue-600 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-blue-700 hover:bg-blue-50/50' }}">
                <span>📊</span>
                <span>Rekap Faktur Sirkulasi</span>
            </a>

            <!-- 4. Faktur Insidentil -->
            <a href="{{ route('keuangan.faktur.sirkulasi.insidentil') }}" 
               class="px-3 py-1.5 rounded-xl font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'insidentil' ? 'bg-blue-50 text-blue-700 border-2 border-blue-600 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-blue-700 hover:bg-blue-50/50' }}">
                <span>🏷️</span>
                <span>Faktur Insidentil</span>
                @php
                    $insidentilSrkBadge = \App\Models\Faktur::where('kategori', 'sirkulasi')->where('tipe_faktur', 'insidentil')->count();
                @endphp
                @if($insidentilSrkBadge > 0)
                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-blue-100 text-blue-800">
                        {{ $insidentilSrkBadge }}
                    </span>
                @endif
            </a>
        </div>
    @endif
</div>
