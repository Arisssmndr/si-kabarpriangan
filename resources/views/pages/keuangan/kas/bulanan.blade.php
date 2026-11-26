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
                            Laporan Kas Bulanan
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Buku Kas
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Rekapitulasi arus kas masuk, pengeluaran, surplus/defisit per bulan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('keuangan.kas.index') }}" class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition">
                    &larr; Buku Kas Utama
                </a>
                <a href="{{ route('keuangan.kas.cetak', ['mode' => 'bulanan', 'bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan Bulanan (PDF)</span>
                </a>
            </div>
        </div>

        <!-- Tier 2: 4 KPI Summary Cards Standar Divisi Iklan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pemasukan Bulan Ini</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums mt-1">
                    + Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Inflow Arus Kas
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pengeluaran Bulan Ini</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono tabular-nums mt-1">
                    - Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Outflow Realisasi Anggaran
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Net Arus Kas (Surplus)</span>
                <div class="text-xl sm:text-2xl font-bold font-mono tabular-nums mt-1 {{ $surplusDefisit >= 0 ? 'text-kp-blue-700' : 'text-slate-900' }}">
                    Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    {{ $surplusDefisit >= 0 ? 'Surplus Bersih' : 'Defisit Kas' }}
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Periode Kasir</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 mt-1">
                    {{ \Carbon\Carbon::create(null, (int)$bulan)->translatedFormat('F') }} {{ $tahun }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Buku Rekapitulasi
                </div>
            </div>
        </div>

        <!-- Tier 3: Toolbar Filter Periode Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <form method="GET" action="{{ route('keuangan.kas.bulanan') }}" class="flex items-center gap-2 flex-wrap">
                <label class="text-xs font-semibold text-slate-700 whitespace-nowrap">Pilih Periode:</label>
                <select name="bulan" class="h-11 text-xs font-semibold rounded-xl border border-slate-300 px-3 bg-white text-slate-900 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <select name="tahun" class="h-11 text-xs font-mono font-semibold rounded-xl border border-slate-300 px-3 bg-white text-slate-900 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                    @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white rounded-xl text-xs font-semibold transition shadow-xs cursor-pointer">
                    Tampilkan Rekap
                </button>
            </form>

            <div class="text-xs text-slate-500 font-medium">
                Periode: <strong class="text-slate-900">{{ \Carbon\Carbon::create(null, (int)$bulan)->translatedFormat('F') }} {{ $tahun }}</strong>
            </div>
        </div>

        <!-- Tier 4: Pure Data Tables Rincian Kategori Pemasukan & Pengeluaran -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Rekap Pemasukan per Kategori -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">
                        Rincian Kategori Pemasukan
                    </h3>
                    <span class="text-xs font-mono font-bold text-kp-blue-700">+ Rp {{ number_format($totalMasuk, 0, ',', '.') }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50/50 border-b border-slate-200 text-slate-600 text-xs font-semibold">
                            <tr>
                                <th class="py-2.5 px-3.5">Kategori Pemasukan</th>
                                <th class="py-2.5 px-3.5 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @forelse($rekapKategoriMasuk as $kategori => $nominal)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3 px-3.5 font-medium text-slate-900">{{ $kategori }}</td>
                                    <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">
                                        <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($nominal, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-6 text-center text-slate-400 italic">Tidak ada pemasukan pada bulan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Rekap Pengeluaran per Kategori -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">
                        Rincian Kategori Pengeluaran
                    </h3>
                    <span class="text-xs font-mono font-bold text-slate-900">- Rp {{ number_format($totalKeluar, 0, ',', '.') }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50/50 border-b border-slate-200 text-slate-600 text-xs font-semibold">
                            <tr>
                                <th class="py-2.5 px-3.5">Kategori Pengeluaran</th>
                                <th class="py-2.5 px-3.5 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @forelse($rekapKategoriKeluar as $kategori => $nominal)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3 px-3.5 font-medium text-slate-900">{{ $kategori }}</td>
                                    <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">
                                        <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($nominal, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-6 text-center text-slate-400 italic">Tidak ada pengeluaran pada bulan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
