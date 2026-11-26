<x-app-layout>
    <div class="space-y-6">

        <!-- Tier 1: Header Halaman Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-9 sm:h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Buku Kas &amp; Treasury Real-Time
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            HU. Kabar Priangan
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Mutasi kas terpadu (pelunasan langsung &amp; setoran inkaso masuk, pencairan anggaran keluar) dengan saldo berjalan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('keuangan.kas.harian') }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Laporan Kas Harian</span>
                </a>

                <a href="{{ route('keuangan.kas.bulanan') }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Laporan Kas Bulanan</span>
                </a>

                <a href="{{ route('keuangan.kas.cetak', ['mode' => 'harian', 'tanggal' => date('Y-m-d')]) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Ledger</span>
                </a>
            </div>
        </div>

        <!-- Tier 2: 4 KPI Summary Cards Standar Divisi Iklan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Kas Berjalan</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums mt-1">
                    Rp {{ number_format($saldoSaatIni, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Likuiditas Kas Aktif
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kas Masuk (Inflow)</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Pelunasan &amp; Setoran Inkaso
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kas Keluar (Outflow)</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Pencairan Anggaran Operasional
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Volume Mutasi</span>
                <div class="text-2xl font-bold text-slate-900 font-mono mt-1">
                    {{ $transaksis->total() }} <span class="text-sm font-normal text-slate-500">Transaksi</span>
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Buku Besar Kas
                </div>
            </div>
        </div>

        <!-- Tier 3: Toolbar Filter Mutasi Kas Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('keuangan.kas.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="dari" value="{{ request('dari') }}" class="w-full h-11 text-xs rounded-xl border border-slate-300 px-3 bg-white text-slate-900 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="sampai" value="{{ request('sampai') }}" class="w-full h-11 text-xs rounded-xl border border-slate-300 px-3 bg-white text-slate-900 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Mutasi</label>
                    <select name="jenis" class="w-full h-11 text-xs rounded-xl border border-slate-300 px-3 bg-white text-slate-900 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                        <option value="">Semua (Masuk &amp; Keluar)</option>
                        <option value="masuk" {{ request('jenis') === 'masuk' ? 'selected' : '' }}>Kas Masuk (+)</option>
                        <option value="keluar" {{ request('jenis') === 'keluar' ? 'selected' : '' }}>Kas Keluar (-)</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Kas</label>
                    <select name="kategori" class="w-full h-11 text-xs rounded-xl border border-slate-300 px-3 bg-white text-slate-900 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 h-11 bg-kp-blue-600 hover:bg-kp-blue-700 text-white rounded-xl text-xs font-semibold transition shadow-xs cursor-pointer">
                        Filter
                    </button>
                    @if(request()->hasAny(['dari', 'sampai', 'jenis', 'kategori']))
                        <a href="{{ route('keuangan.kas.index') }}" class="h-11 px-4 inline-flex items-center justify-center border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tier 4: Pure Data Table Standar Divisi Iklan (Tanpa Banner Sub-Header) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3.5 px-3.5 text-center w-12">No</th>
                            <th class="py-3.5 px-3.5 min-w-[140px]">No. Transaksi</th>
                            <th class="py-3.5 px-3.5 min-w-[110px]">Tanggal</th>
                            <th class="py-3.5 px-3.5 min-w-[140px]">Kategori</th>
                            <th class="py-3.5 px-3.5 min-w-[260px]">Keterangan Arus Kas</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[140px]">Kas Masuk</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[140px]">Kas Keluar</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[140px]">Saldo Berjalan</th>
                            <th class="py-3.5 px-3.5 text-center w-28">Kasir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($transaksis as $key => $kas)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                    {{ $transaksis->firstItem() ? ($transaksis->firstItem() + $key) : ($key + 1) }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700">
                                    {{ $kas->no_transaksi }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-700">
                                    {{ $kas->tanggal ? $kas->tanggal->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 uppercase">
                                        {{ $kas->kategori_kas }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 text-xs text-slate-600 whitespace-normal min-w-[260px] max-w-sm leading-relaxed">
                                    {{ $kas->keterangan }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    @if($kas->jenis === 'masuk')
                                        <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($kas->nominal, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400 font-mono">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    @if($kas->jenis === 'keluar')
                                        <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($kas->nominal, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400 font-mono">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($kas->saldo_akhir, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-center text-xs text-slate-600">
                                    {{ $kas->creator->name ?? 'Kasir' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400 italic">
                                    Belum ada mutasi kas untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transaksis->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $transaksis->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
