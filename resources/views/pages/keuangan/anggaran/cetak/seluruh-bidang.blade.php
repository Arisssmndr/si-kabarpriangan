<x-app-layout>
    <div class="space-y-6">

        <!-- Top Header & Breadcrumb -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('keuangan.anggaran.cetak.hub') }}" class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition" title="Kembali ke Menu Cetak">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-md bg-purple-100 text-purple-800 text-[10px] font-black uppercase tracking-wider">
                            CETAK #1 &bull; ANGGARAN.EXE
                        </span>
                        <span class="text-xs text-slate-400 font-medium">HU. KABAR PRIANGAN</span>
                    </div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight mt-0.5">
                        REALISASI ANGGARAN SELURUH BIDANG PRIANGAN
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekapitulasi penyerapan plafon RAB per bidang operasional periode {{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}
                    </p>
                </div>
            </div>

            <!-- Filter & Print Action -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <form method="GET" action="{{ route('keuangan.anggaran.cetak.seluruh-bidang') }}" class="flex items-center gap-2">
                    <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()" 
                           class="h-9 text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 font-semibold focus:ring-2 focus:ring-purple-500">
                    <button type="submit" class="h-9 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Filter
                    </button>
                </form>

                <a href="{{ route('keuangan.anggaran.cetak.seluruh-bidang', ['periode' => $periode, 'cetak' => 1]) }}" 
                   target="_blank"
                   class="h-9 px-4 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold inline-flex items-center gap-2 shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan (Print / PDF)</span>
                </a>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Plafon Anggaran</div>
                <div class="text-xl sm:text-2xl font-black text-slate-900 mt-1 font-mono">
                    Rp {{ number_format($totalPlafon, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    <span>7 Bidang Terdaftar</span>
                </div>
            </div>

            <div class="bg-white border border-purple-200/80 rounded-2xl p-4 sm:p-5 shadow-xs bg-purple-50/20">
                <div class="text-[11px] font-bold text-purple-700 uppercase tracking-wider">Total Realisasi (ACC)</div>
                <div class="text-xl sm:text-2xl font-black text-purple-700 mt-1 font-mono">
                    Rp {{ number_format($totalRealisasi, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-purple-600 mt-1 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                    <span>{{ $totalBpCount }} Dokumen BP Disetujui</span>
                </div>
            </div>

            <div class="bg-white border border-emerald-200/80 rounded-2xl p-4 sm:p-5 shadow-xs bg-emerald-50/20">
                <div class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Sisa Plafon Anggaran</div>
                <div class="text-xl sm:text-2xl font-black {{ $totalSisa >= 0 ? 'text-emerald-700' : 'text-rose-600' }} mt-1 font-mono">
                    Rp {{ number_format($totalSisa, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-emerald-600 mt-1 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full {{ $totalSisa >= 0 ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                    <span>{{ $totalSisa >= 0 ? 'Surplus Tersisa' : 'Defisit Anggaran' }}</span>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">% Penyerapan Anggaran</div>
                <div class="text-xl sm:text-2xl font-black text-slate-900 mt-1 font-mono">
                    {{ $totalPersen }} %
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 mt-2">
                    <div class="bg-purple-600 h-2 rounded-full transition-all" style="width: {{ min(100, $totalPersen) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Tabel Laporan Realisasi Anggaran Seluruh Bidang -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-tight">
                        Matriks Realisasi 7 Bidang Kerja Priangan
                    </h3>
                    <p class="text-xs text-slate-500">
                        Periode: <strong>{{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}</strong>
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('keuangan.anggaran.entry.rab', ['periode' => $periode]) }}" class="text-xs font-bold text-purple-700 hover:text-purple-800 underline">
                        Edit Plafon RAB &rarr;
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100/80 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-black tracking-wider">
                            <th class="py-3 px-4 w-12 text-center">Kode</th>
                            <th class="py-3 px-4">Nama Bidang / Divisi Operasional</th>
                            <th class="py-3 px-4 text-center">Jml BP</th>
                            <th class="py-3 px-4 text-right">Plafon Anggaran (Rp)</th>
                            <th class="py-3 px-4 text-right">Realisasi BP (Rp)</th>
                            <th class="py-3 px-4 text-right">Sisa Anggaran (Rp)</th>
                            <th class="py-3 px-4 text-center w-28">Penyerapan</th>
                            <th class="py-3 px-4 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($laporanList as $row)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 text-center font-mono font-black text-slate-700">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200">
                                        {{ $row['kode'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $row['nama'] }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono text-slate-600">
                                    {{ $row['count_bp'] }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold text-slate-800">
                                    {{ number_format($row['plafon'], 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-purple-700">
                                    {{ number_format($row['realisasi'], 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold {{ $row['sisa'] >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                    {{ number_format($row['sisa'], 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <div class="w-16 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="h-1.5 rounded-full {{ $row['persen'] > 90 ? 'bg-rose-500' : ($row['persen'] > 75 ? 'bg-amber-500' : 'bg-purple-600') }}" 
                                                 style="width: {{ min(100, $row['persen']) }}%"></div>
                                        </div>
                                        <span class="font-mono text-[11px] font-bold text-slate-700">{{ $row['persen'] }}%</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('keuangan.anggaran.cetak.per-bidang', ['kode_bagian' => $row['kode'], 'periode' => $periode]) }}" 
                                       class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-purple-100 hover:text-purple-700 text-slate-700 font-bold text-[11px] transition inline-block">
                                        Rincian &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">
                                    Tidak ada data untuk periode terpilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-300 font-mono text-xs">
                        <tr class="font-black text-slate-900">
                            <td colspan="2" class="py-3 px-4 uppercase text-center tracking-wider">
                                TOTAL SELURUH BIDANG
                            </td>
                            <td class="py-3 px-4 text-center">
                                {{ $totalBpCount }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                Rp {{ number_format($totalPlafon, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right text-purple-700">
                                Rp {{ number_format($totalRealisasi, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right {{ $totalSisa >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                Rp {{ number_format($totalSisa, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
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
