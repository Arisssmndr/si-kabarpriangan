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
                            CETAK #5 &bull; ANGGARAN.EXE
                        </span>
                        <span class="text-xs text-slate-400 font-medium">HU. KABAR PRIANGAN</span>
                    </div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight mt-0.5">
                        REKAP REALISASI ANGGARAN PER BIDANG (BAGAN AKUN COA)
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Klasifikasi penyerapan beban operasional berdasarkan Kode Perkiraan Akuntansi periode {{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}
                    </p>
                </div>
            </div>

            <!-- Filter & Print Action -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <form method="GET" action="{{ route('keuangan.anggaran.cetak.rekap-bidang-coa') }}" class="flex items-center gap-2">
                    <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()" 
                           class="h-9 text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 font-semibold focus:ring-2 focus:ring-purple-500">
                    <button type="submit" class="h-9 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Filter
                    </button>
                </form>

                <a href="{{ route('keuangan.anggaran.cetak.rekap-bidang-coa', ['periode' => $periode, 'cetak' => 1]) }}" 
                   target="_blank"
                   class="h-9 px-4 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold inline-flex items-center gap-2 shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Rekap COA (Print / PDF)</span>
                </a>
            </div>
        </div>

        <!-- KPI Ringkasan Akuntansi -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Realisasi Seluruh Akun</div>
                <div class="text-xl sm:text-2xl font-black text-slate-900 mt-1 font-mono">
                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Periode: {{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}
                </div>
            </div>

            <div class="bg-white border border-purple-200/80 rounded-2xl p-4 sm:p-5 shadow-xs bg-purple-50/20">
                <div class="text-[11px] font-bold text-purple-700 uppercase tracking-wider">Jumlah Akun Biaya COA</div>
                <div class="text-xl sm:text-2xl font-black text-purple-700 mt-1 font-mono">
                    {{ count($rekapCoa) }} Bagan Perkiraan
                </div>
                <div class="text-[11px] text-purple-600 mt-1">
                    Standar Akuntansi HU. Kabar Priangan
                </div>
            </div>

            <div class="bg-white border border-indigo-200/80 rounded-2xl p-4 sm:p-5 shadow-xs bg-indigo-50/20">
                <div class="text-[11px] font-bold text-indigo-700 uppercase tracking-wider">Status Integrasi</div>
                <div class="text-xl sm:text-2xl font-black text-indigo-700 mt-1 font-mono">
                    Buku Besar Sinkron
                </div>
                <div class="text-[11px] text-indigo-600 mt-1">
                    Terhubung Kasir &amp; Akuntansi
                </div>
            </div>
        </div>

        <!-- Tabel Rekap Bagan Akun COA -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-tight">
                        Rekapitulasi Per Kode Perkiraan (Beban Operasional)
                    </h3>
                    <p class="text-xs text-slate-500">
                        Periode: <strong>{{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}</strong>
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100/80 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-black tracking-wider">
                            <th class="py-3 px-4 w-40">Kode Perkiraan</th>
                            <th class="py-3 px-4">Nama Akun Perkiraan</th>
                            <th class="py-3 px-4 w-48">Bidang Operasional</th>
                            <th class="py-3 px-4 text-center w-24">Jml BP</th>
                            <th class="py-3 px-4 text-right w-40">Total Realisasi (Rp)</th>
                            <th class="py-3 px-4 text-center w-28">Porsi Biaya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($rekapCoa as $item)
                            @php
                                $porsi = $grandTotal > 0 ? round(($item['total'] / $grandTotal) * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 font-mono font-black text-purple-700">
                                    <span class="px-2 py-0.5 rounded-md bg-purple-50 border border-purple-200">
                                        {{ $item['kode'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $item['nama'] }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 font-semibold">
                                    {{ $item['bidang'] }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-700">
                                    {{ $item['count_bp'] }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                    {{ number_format($item['total'], 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <div class="w-14 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-purple-600 h-1.5 rounded-full" style="width: {{ $porsi }}%"></div>
                                        </div>
                                        <span class="font-mono text-[11px] font-bold text-slate-600">{{ $porsi }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-300 font-mono text-xs">
                        <tr class="font-black text-slate-900">
                            <td colspan="3" class="py-3.5 px-4 text-center uppercase">
                                TOTAL REALISASI BEBAN KESELURUHAN
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                {{ collect($rekapCoa)->sum('count_bp') }} BP
                            </td>
                            <td class="py-3.5 px-4 text-right text-purple-700">
                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                100%
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
