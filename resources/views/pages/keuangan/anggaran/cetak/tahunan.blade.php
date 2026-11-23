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
                            Rekap Realisasi Anggaran Per Tahun (12 Bulan Komparasi)
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            CETAK #7 &bull; ANGGARAN.EXE
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Matriks realisasi penyerapan per bidang dari Januari s.d. Desember Tahun {{ $tahun }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('keuangan.anggaran.cetak.hub') }}" class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition">
                    &larr; Menu Cetak
                </a>
                <a href="{{ route('keuangan.anggaran.cetak.tahunan', ['tahun' => $tahun, 'cetak' => 1]) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Matriks Tahunan (Dot-Matrix / PDF)</span>
                </a>
            </div>
        </div>

        <!-- Tier 2: 4 KPI Summary Cards Standar Divisi Iklan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Grand Total Tahun {{ $tahun }}</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono tabular-nums mt-1">
                    Rp {{ number_format($grandTotalTahun, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Akumulasi 12 Bulan Penuh
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Rata-Rata / Bulan</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums mt-1">
                    Rp {{ number_format($grandTotalTahun / 12, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Rata-Rata Penyerapan
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Bidang Terdaftar</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">
                    {{ count($matriks) }} <span class="text-sm font-normal text-slate-500">Bidang</span>
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Unit Operasional Priangan
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Periode Komparasi</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 mt-1">
                    12 Bulan
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Januari &ndash; Desember {{ $tahun }}
                </div>
            </div>
        </div>

        <!-- Tier 3: Toolbar Filter Tahun Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <form method="GET" action="{{ route('keuangan.anggaran.cetak.tahunan') }}" class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-700 whitespace-nowrap">Pilih Tahun Anggaran:</label>
                <select name="tahun" onchange="this.form.submit()" class="h-11 text-xs font-semibold rounded-xl border border-slate-300 px-3 bg-white text-slate-900 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                    @for($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ (int)$tahun === $y ? 'selected' : '' }}>
                            Tahun {{ $y }}
                        </option>
                    @endfor
                </select>
                <button type="submit" class="h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl transition shadow-xs cursor-pointer">
                    Filter
                </button>
            </form>

            <div class="text-xs text-slate-500 font-medium">
                Tahun Anggaran Aktif: <strong class="text-slate-900">{{ $tahun }}</strong>
            </div>
        </div>

        <!-- Tier 4: Pure Data Table Matriks 12 Bulan Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3.5 px-3 text-center w-12 sticky left-0 bg-slate-50 z-10">No</th>
                            <th class="py-3.5 px-3 text-center w-14 sticky left-12 bg-slate-50 z-10">Kode</th>
                            <th class="py-3.5 px-3 min-w-[160px] sticky left-24 bg-slate-50 z-10">Nama Bidang</th>
                            @for($b = 1; $b <= 12; $b++)
                                <th class="py-3.5 px-2 text-right min-w-[90px]">
                                    {{ \Carbon\Carbon::create(2026, $b, 1)->translatedFormat('M') }}
                                </th>
                            @endfor
                            <th class="py-3.5 px-3 text-right min-w-[130px] font-bold text-kp-blue-700 bg-kp-blue-50/50">
                                Total Tahun
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700 font-mono text-xs">
                        @foreach($matriks as $key => $row)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-3 text-center text-slate-500 font-normal sticky left-0 bg-white">
                                    {{ $key + 1 }}
                                </td>
                                <td class="py-3.5 px-3 text-center font-bold text-kp-blue-700 sticky left-12 bg-white">
                                    {{ $row['kode'] }}
                                </td>
                                <td class="py-3.5 px-3 font-sans font-bold text-slate-900 sticky left-24 bg-white truncate">
                                    {{ $row['nama'] }}
                                </td>
                                @for($b = 1; $b <= 12; $b++)
                                    <td class="py-3.5 px-2 text-right {{ $row['bulan'][$b] > 0 ? 'text-slate-900 font-semibold' : 'text-slate-300' }}">
                                        {{ $row['bulan'][$b] > 0 ? number_format($row['bulan'][$b], 0, ',', '.') : '-' }}
                                    </td>
                                @endfor
                                <td class="py-3.5 px-3 text-right font-bold text-kp-blue-700 bg-kp-blue-50/30">
                                    {{ number_format($row['total'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t border-slate-200 font-semibold text-xs whitespace-nowrap text-slate-900">
                        <tr>
                            <td colspan="3" class="py-3.5 px-3 uppercase font-bold text-slate-900 text-center sticky left-0 bg-slate-50 z-10">
                                TOTAL BULANAN
                            </td>
                            @for($b = 1; $b <= 12; $b++)
                                <td class="py-3.5 px-2 text-right font-mono {{ $totalPerBulan[$b] > 0 ? 'text-kp-blue-700 font-bold' : 'text-slate-400' }}">
                                    {{ $totalPerBulan[$b] > 0 ? number_format($totalPerBulan[$b], 0, ',', '.') : '-' }}
                                </td>
                            @endfor
                            <td class="py-3.5 px-3 text-right font-mono font-bold text-kp-blue-700 bg-kp-blue-50/50">
                                Rp {{ number_format($grandTotalTahun, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
