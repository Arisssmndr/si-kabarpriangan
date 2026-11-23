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
                            Realisasi Anggaran Seluruh Bidang Priangan
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            CETAK #1 &bull; ANGGARAN.EXE
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Rekapitulasi penyerapan plafon RAB per bidang operasional periode {{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('keuangan.anggaran.cetak.hub') }}" class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition">
                    &larr; Menu Cetak
                </a>
                <a href="{{ route('keuangan.anggaran.cetak.seluruh-bidang', ['periode' => $periode, 'cetak' => 1]) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan (Print / PDF)</span>
                </a>
            </div>
        </div>

        <!-- Tier 2: 4 KPI Summary Cards Standar Divisi Iklan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Plafon Anggaran</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalPlafon, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Akumulasi Seluruh Bidang
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Realisasi (ACC)</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalRealisasi, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    {{ $totalBpCount }} Dokumen BP Disetujui
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sisa Plafon Anggaran</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalSisa, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    {{ $totalSisa >= 0 ? 'Surplus Tersisa' : 'Defisit Anggaran' }}
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">% Penyerapan Anggaran</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums mt-1">
                    {{ $totalPersen }}%
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-kp-blue-600 h-1.5 rounded-full" style="width: {{ min(100, $totalPersen) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Tier 3: Toolbar Filter Periode Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <form method="GET" action="{{ route('keuangan.anggaran.cetak.seluruh-bidang') }}" class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-700 whitespace-nowrap">Pilih Periode:</label>
                <input type="month" name="periode" value="{{ $periode }}" 
                       class="h-11 text-xs font-mono font-semibold rounded-xl border border-slate-300 px-3 bg-white text-slate-800 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                <button type="submit" class="h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl transition shadow-xs cursor-pointer">
                    Filter
                </button>
            </form>

            <div class="flex items-center gap-3">
                <div class="text-xs text-slate-500 font-medium">
                    Periode: <strong class="text-slate-900">{{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}</strong>
                </div>
                <a href="{{ route('keuangan.anggaran.entry.rab', ['periode' => $periode]) }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
                    Edit Plafon RAB &rarr;
                </a>
            </div>
        </div>

        <!-- Tier 4: Pure Data Table Standar Divisi Iklan (Tanpa Banner Sub-Header) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3.5 px-3.5 text-center w-12">No</th>
                            <th class="py-3.5 px-3.5 text-center w-16">Kode</th>
                            <th class="py-3.5 px-3.5 min-w-[200px]">Nama Bidang / Divisi Operasional</th>
                            <th class="py-3.5 px-3.5 text-center w-24">Jml BP</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[140px]">Plafon Anggaran</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[140px]">Realisasi BP</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[140px]">Sisa Anggaran</th>
                            <th class="py-3.5 px-3.5 min-w-[180px]">Penyerapan</th>
                            <th class="py-3.5 px-3.5 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($laporanList as $key => $row)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $key + 1 }}</td>
                                <td class="py-3.5 px-3.5 text-center font-mono font-bold text-kp-blue-700">
                                    <span class="px-2 py-0.5 rounded bg-kp-blue-50 border border-kp-blue-200">{{ $row['kode'] }}</span>
                                </td>
                                <td class="py-3.5 px-3.5 font-bold text-slate-900">{{ $row['nama'] }}</td>
                                <td class="py-3.5 px-3.5 text-center font-mono text-xs text-slate-700">{{ $row['count_bp'] }}</td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($row['plafon'], 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($row['realisasi'], 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($row['sisa'], 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5">
                                    <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                        <span class="text-slate-700 font-mono">{{ $row['persen'] }}%</span>
                                        <span class="text-slate-500 text-[11px] font-normal">
                                            {{ $row['persen'] > 90 ? 'Hampir Habis' : ($row['persen'] > 75 ? 'Peringatan' : 'Aman') }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 rounded-full bg-kp-blue-600" style="width: {{ min(100, $row['persen']) }}%"></div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <a href="{{ route('keuangan.anggaran.cetak.per-bidang', ['kode_bagian' => $row['kode'], 'periode' => $periode]) }}" 
                                       class="px-2.5 py-1 rounded-lg border border-slate-300 hover:border-kp-blue-500 hover:bg-kp-blue-50 text-kp-blue-700 text-xs font-semibold transition shadow-2xs">
                                        Rincian &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400 italic">
                                    Tidak ada data untuk periode terpilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 border-t border-slate-200 font-semibold text-xs whitespace-nowrap text-slate-900">
                        <tr>
                            <td colspan="3" class="py-3.5 px-3.5 uppercase font-bold text-slate-900 text-center">
                                TOTAL SELURUH BIDANG
                            </td>
                            <td class="py-3.5 px-3.5 text-center font-mono font-bold text-slate-900">
                                {{ $totalBpCount }}
                            </td>
                            <td class="py-3.5 px-3.5 text-right">
                                <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                    <span class="font-bold text-slate-900 tabular-nums">{{ number_format($totalPlafon, 0, ',', '.') }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-3.5 text-right">
                                <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                    <span class="font-bold text-kp-blue-700 tabular-nums">{{ number_format($totalRealisasi, 0, ',', '.') }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-3.5 text-right">
                                <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                    <span class="font-bold text-slate-900 tabular-nums">{{ number_format($totalSisa, 0, ',', '.') }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-3.5 text-center font-mono font-bold text-kp-blue-700">
                                {{ $totalPersen }}%
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
