@props(['activeTab' => 'keseluruhan', 'activeCategory' => 'iklan'])

<div class="space-y-4">
    <!-- 1. Tab Level 1: Pemisah 2 Tabel Utama (Iklan vs Sirkulasi) -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-2">
        <div class="flex items-center gap-2">
            <a href="{{ route('keuangan.faktur.iklan.index') }}" 
               class="px-4 py-2 rounded-xl text-xs font-extrabold flex items-center gap-2 transition-all {{ $activeCategory === 'iklan' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                <span>📰</span>
                <span>TABEL FAKTUR IKLAN</span>
                @php
                    $countIklan = \App\Models\Faktur::where('kategori', 'iklan')->count();
                @endphp
                <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $activeCategory === 'iklan' ? 'bg-white/20 text-white' : 'bg-slate-300 text-slate-700' }}">
                    {{ $countIklan }}
                </span>
            </a>

            <a href="{{ route('keuangan.faktur.sirkulasi.index') }}" 
               class="px-4 py-2 rounded-xl text-xs font-extrabold flex items-center gap-2 transition-all {{ $activeCategory === 'sirkulasi' ? 'bg-indigo-900 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                <span>🗞️</span>
                <span>TABEL FAKTUR SIRKULASI</span>
                @php
                    $countSirkulasi = \App\Models\Faktur::where('kategori', 'sirkulasi')->count();
                @endphp
                <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full {{ $activeCategory === 'sirkulasi' ? 'bg-white/20 text-white' : 'bg-slate-300 text-slate-700' }}">
                    {{ $countSirkulasi }}
                </span>
            </a>
        </div>

        <div class="hidden sm:flex items-center gap-2">
            @if($activeCategory === 'iklan')
                <a href="{{ route('keuangan.faktur.create', ['kategori' => 'iklan', 'tipe_faktur' => 'reguler']) }}" 
                   class="h-8 px-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold flex items-center gap-1 transition shadow-2xs">
                    <span>+ Buat Faktur Iklan</span>
                </a>
            @else
                <a href="{{ route('keuangan.faktur.create', ['kategori' => 'sirkulasi', 'tipe_faktur' => 'susulan']) }}" 
                   class="h-8 px-3 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 text-[11px] font-bold flex items-center gap-1 transition shadow-2xs">
                    <span>⚡ + Susulan</span>
                </a>
                <a href="{{ route('keuangan.faktur.create', ['kategori' => 'sirkulasi', 'tipe_faktur' => 'insidentil']) }}" 
                   class="h-8 px-3 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-300 text-[11px] font-bold flex items-center gap-1 transition shadow-2xs">
                    <span>🏷️ + Insidentil</span>
                </a>
                <a href="{{ route('keuangan.faktur.create', ['kategori' => 'sirkulasi', 'tipe_faktur' => 'reguler']) }}" 
                   class="h-8 px-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-bold flex items-center gap-1 transition shadow-2xs">
                    <span>+ Buat Faktur Sirkulasi</span>
                </a>
            @endif
        </div>
    </div>

    <!-- 2. Tab Level 2: Sub-Menu Khusus Tabel Iklan -->
    @if($activeCategory === 'iklan')
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <!-- 1. Keseluruhan -->
            <a href="{{ route('keuangan.faktur.iklan.index') }}" 
               class="px-3 py-1.5 rounded-lg font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'keseluruhan' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <span>📋</span>
                <span>Faktur Iklan Keseluruhan</span>
            </a>

            <!-- 2. Rekap Tanggal & Bulan -->
            <a href="{{ route('keuangan.faktur.iklan.rekap') }}" 
               class="px-3 py-1.5 rounded-lg font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'rekap' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <span>📊</span>
                <span>Rekap Faktur Iklan (Tgl/Bulan)</span>
            </a>

            <!-- 3. Faktur Susulan -->
            <a href="{{ route('keuangan.faktur.iklan.susulan') }}" 
               class="px-3 py-1.5 rounded-lg font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'susulan' ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <span>⚡</span>
                <span>Faktur Susulan</span>
                @php
                    $susulanIklanCount = \App\Models\Faktur::where('kategori', 'iklan')->where('tipe_faktur', 'susulan')->count();
                @endphp
                @if($susulanIklanCount > 0)
                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-amber-200 text-amber-900">
                        {{ $susulanIklanCount }}
                    </span>
                @endif
            </a>

            <!-- 4. Rekap Tunai -->
            <a href="{{ route('keuangan.faktur.iklan.tunai') }}" 
               class="px-3 py-1.5 rounded-lg font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'tunai' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <span>💵</span>
                <span>Rekap Faktur Iklan Tunai</span>
            </a>

            <!-- 5. Rekap Piutang -->
            <a href="{{ route('keuangan.faktur.iklan.piutang') }}" 
               class="px-3 py-1.5 rounded-lg font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'piutang' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <span>⏳</span>
                <span>Rekap Faktur Iklan Piutang</span>
                @php
                    $piutangBadge = \App\Models\Faktur::where('kategori', 'iklan')->where('sisa_piutang', '>', 0)->count();
                @endphp
                @if($piutangBadge > 0)
                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-rose-200 text-rose-900">
                        {{ $piutangBadge }}
                    </span>
                @endif
            </a>

            <!-- 6. Rekap Stopper & Bonus -->
            <a href="{{ route('keuangan.faktur.iklan.stopper-bonus') }}" 
               class="px-3 py-1.5 rounded-lg font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'stopper_bonus' ? 'bg-teal-50 text-teal-700 border border-teal-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <span>🎁</span>
                <span>Stopper & Bonus</span>
                @php
                    $stopperBonusCount = \App\Models\Faktur::where('kategori', 'iklan')->whereIn('tipe_faktur', ['stopper', 'bonus'])->count();
                @endphp
                @if($stopperBonusCount > 0)
                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-teal-200 text-teal-900">
                        {{ $stopperBonusCount }}
                    </span>
                @endif
            </a>

            <!-- 7. Stok Opname -->
            <a href="{{ route('keuangan.faktur.iklan.stok-opname') }}" 
               class="px-3 py-1.5 rounded-lg font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'stok_opname' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <span>📦</span>
                <span>Stok Opname Faktur</span>
            </a>
        </div>
    @elseif($activeCategory === 'sirkulasi')
        <!-- Tab Level 2: Sub-Menu Khusus Tabel Sirkulasi -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs border-b border-slate-100 sidebar-scroll">
            <!-- 1. Keseluruhan -->
            <a href="{{ route('keuangan.faktur.sirkulasi.index') }}" 
               class="px-3 py-1.5 rounded-lg font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'keseluruhan' || $activeTab === 'sirkulasi' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <span>🗞️</span>
                <span>Faktur Sirkulasi Keseluruhan</span>
            </a>

            <!-- 2. Susulan -->
            <a href="{{ route('keuangan.faktur.sirkulasi.susulan') }}" 
               class="px-3 py-1.5 rounded-lg font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'susulan' ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <span>⚡</span>
                <span>Faktur Sirkulasi Susulan</span>
                @php
                    $susulanSrkBadge = \App\Models\Faktur::where('kategori', 'sirkulasi')->where('tipe_faktur', 'susulan')->count();
                @endphp
                @if($susulanSrkBadge > 0)
                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-amber-200 text-amber-900">
                        {{ $susulanSrkBadge }}
                    </span>
                @endif
            </a>

            <!-- 3. Rekap Sirkulasi -->
            <a href="{{ route('keuangan.faktur.sirkulasi.rekap') }}" 
               class="px-3 py-1.5 rounded-lg font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'rekap' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <span>📊</span>
                <span>Rekap Faktur Sirkulasi</span>
            </a>

            <!-- 4. Faktur Insidentil -->
            <a href="{{ route('keuangan.faktur.sirkulasi.insidentil') }}" 
               class="px-3 py-1.5 rounded-lg font-bold flex items-center gap-1.5 shrink-0 transition {{ $activeTab === 'insidentil' ? 'bg-teal-50 text-teal-800 border border-teal-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <span>🏷️</span>
                <span>Faktur Insidentil</span>
                @php
                    $insidentilSrkBadge = \App\Models\Faktur::where('kategori', 'sirkulasi')->where('tipe_faktur', 'insidentil')->count();
                @endphp
                @if($insidentilSrkBadge > 0)
                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-teal-200 text-teal-900">
                        {{ $insidentilSrkBadge }}
                    </span>
                @endif
            </a>
        </div>
    @endif
</div>
