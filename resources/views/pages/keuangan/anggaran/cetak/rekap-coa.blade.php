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
                            Rekap Realisasi Per Akun (Bagan Akun COA)
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            CETAK #5 &bull; ANGGARAN.EXE
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Klasifikasi penyerapan beban operasional berdasarkan Kode Perkiraan Akuntansi periode {{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('keuangan.anggaran.cetak.hub') }}" class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition">
                    &larr; Menu Cetak
                </a>
                <a href="{{ route('keuangan.anggaran.cetak.rekap-bidang-coa', ['periode' => $periode, 'cetak' => 1]) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Rekap COA (Dot-Matrix / PDF)</span>
                </a>
            </div>
        </div>

        <!-- Tier 2: 4 KPI Summary Cards Standar Divisi Iklan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Realisasi Akun</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono tabular-nums mt-1">
                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Periode {{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jumlah Akun COA</span>
                <div class="text-2xl font-bold text-kp-blue-700 font-mono mt-1">
                    {{ count($rekapCoa) }} <span class="text-sm font-normal text-slate-500">Akun</span>
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Bagan Perkiraan Biaya
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Transaksi BP</span>
                <div class="text-2xl font-bold text-slate-900 font-mono mt-1">
                    {{ collect($rekapCoa)->sum('count_bp') }} <span class="text-sm font-normal text-slate-500">Dokumen</span>
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Tercatat Realisasi
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Status Buku Besar</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 mt-1">
                    Sinkron
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Terhubung Kasir &amp; Akuntansi
                </div>
            </div>
        </div>

        <!-- Tier 3: Toolbar Filter Periode Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <form method="GET" action="{{ route('keuangan.anggaran.cetak.rekap-bidang-coa') }}" class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-700 whitespace-nowrap">Pilih Periode:</label>
                <input type="month" name="periode" value="{{ $periode }}" 
                       class="h-11 text-xs font-mono font-semibold rounded-xl border border-slate-300 px-3 bg-white text-slate-800 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                <button type="submit" class="h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl transition shadow-xs cursor-pointer">
                    Filter
                </button>
            </form>

            <div class="text-xs text-slate-500 font-medium">
                Periode: <strong class="text-slate-900">{{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}</strong>
            </div>
        </div>

        <!-- Tier 4: Pure Data Table Standar Divisi Iklan (Kolom Terpisah) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3.5 px-3.5 text-center w-12">No</th>
                            <th class="py-3.5 px-3.5 min-w-[150px]">Kode Perkiraan</th>
                            <th class="py-3.5 px-3.5 min-w-[240px]">Nama Perkiraan (COA)</th>
                            <th class="py-3.5 px-3.5 min-w-[160px]">Bidang Operasional</th>
                            <th class="py-3.5 px-3.5 text-center w-24">Jml BP</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[140px]">Realisasi (Rp)</th>
                            <th class="py-3.5 px-3.5 text-center w-32">Porsi Biaya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @foreach($rekapCoa as $key => $item)
                            @php
                                $porsi = $grandTotal > 0 ? round(($item['total'] / $grandTotal) * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $key + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700">
                                    {{ $item['kode'] }}
                                </td>
                                <td class="py-3.5 px-3.5 font-bold text-slate-900">
                                    {{ $item['nama'] }}
                                </td>
                                <td class="py-3.5 px-3.5 font-medium text-slate-800">
                                    {{ $item['bidang'] }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center font-mono text-xs font-bold text-slate-900">
                                    {{ $item['count_bp'] }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($item['total'], 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <div class="w-14 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-kp-blue-600 h-1.5 rounded-full" style="width: {{ $porsi }}%"></div>
                                        </div>
                                        <span class="font-mono text-xs font-bold text-slate-700">{{ $porsi }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t border-slate-200 font-semibold text-xs whitespace-nowrap text-slate-900">
                        <tr>
                            <td colspan="4" class="py-3.5 px-3.5 text-center uppercase font-bold text-slate-900">
                                TOTAL REALISASI BEBAN KESELURUHAN
                            </td>
                            <td class="py-3.5 px-3.5 text-center font-mono font-bold text-slate-900">
                                {{ collect($rekapCoa)->sum('count_bp') }} BP
                            </td>
                            <td class="py-3.5 px-3.5 text-right">
                                <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                    <span class="font-bold text-kp-blue-700 tabular-nums">{{ number_format($grandTotal, 0, ',', '.') }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-3.5 text-center font-mono font-bold text-kp-blue-700">
                                100%
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
