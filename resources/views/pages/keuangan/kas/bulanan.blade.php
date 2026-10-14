<x-app-layout>
    <div class="space-y-6">

        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('keuangan.kas.index') }}" class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition">
                    ←
                </a>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                        LAPORAN KAS BULANAN
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekapitulasi arus kas masuk, pengeluaran, surplus/defisit per bulan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('keuangan.kas.cetak', ['mode' => 'bulanan', 'bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank"
                   class="h-9 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Laporan Bulanan (PDF)</span>
                </a>
            </div>
        </div>

        <!-- Filter Bulan/Tahun -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('keuangan.kas.bulanan') }}" class="flex items-center gap-3 flex-wrap">
                <span class="text-xs font-bold text-slate-700">Pilih Periode:</span>
                <select name="bulan" class="text-xs rounded-xl border border-slate-300 px-3 py-2">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <select name="tahun" class="text-xs rounded-xl border border-slate-300 px-3 py-2 font-mono">
                    @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition">
                    Tampilkan Rekap
                </button>
            </form>
        </div>

        <!-- KPI Ringkas Bulanan -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-emerald-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 font-mono">Total Pemasukan Bulan Ini</span>
                <div class="mt-2 text-2xl font-black text-emerald-600 font-mono">
                    + Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                </div>
            </div>

            <div class="bg-white border border-rose-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-700 font-mono">Total Pengeluaran Bulan Ini</span>
                <div class="mt-2 text-2xl font-black text-rose-600 font-mono">
                    - Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                </div>
            </div>

            <div class="bg-white border border-blue-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 font-mono">Net Arus Kas (Surplus / Defisit)</span>
                <div class="mt-2 text-2xl font-black {{ $surplusDefisit >= 0 ? 'text-blue-700' : 'text-rose-600' }} font-mono">
                    Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- Rekap Per Kategori -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Rekap Pemasukan per Kategori -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-700 font-mono mb-3 pb-2 border-b border-slate-100">
                    Rincian Kategori Pemasukan
                </h3>
                <div class="space-y-2">
                    @forelse($rekapKategoriMasuk as $kategori => $nominal)
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-emerald-50/50 text-xs">
                            <span class="font-bold text-slate-800">{{ $kategori }}</span>
                            <span class="font-mono font-bold text-emerald-700">Rp {{ number_format($nominal, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center">Tidak ada pemasukan.</p>
                    @endforelse
                </div>
            </div>

            <!-- Rekap Pengeluaran per Kategori -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <h3 class="text-xs font-bold uppercase tracking-wider text-rose-700 font-mono mb-3 pb-2 border-b border-slate-100">
                    Rincian Kategori Pengeluaran
                </h3>
                <div class="space-y-2">
                    @forelse($rekapKategoriKeluar as $kategori => $nominal)
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-rose-50/50 text-xs">
                            <span class="font-bold text-slate-800">{{ $kategori }}</span>
                            <span class="font-mono font-bold text-rose-700">Rp {{ number_format($nominal, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center">Tidak ada pengeluaran.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
