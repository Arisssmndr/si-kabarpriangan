<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Rekap Pemasaran (Jatah Fisik Oplah)</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-teal-50 text-teal-700 border border-teal-200">Koran Fisik</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Laporan agregat distribusi oplah koran cetak, retur, jatah gratis, dan serapan penjualan per rute se-Priangan Timur.
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('sirkulasi.laporan.pemasaran') }}" class="flex items-center gap-2">
                <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="text-xs rounded-lg border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                <button type="submit" class="py-2 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition">
                    Filter Periode
                </button>
            </form>
        </div>

        <!-- 4 KPI Serapan -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Produksi Cetak</span>
                <div class="text-lg font-extrabold text-slate-900 mt-1">{{ number_format($totalCetak, 0, ',', '.') }} eks</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Koran Terjual Efektif</span>
                <div class="text-lg font-extrabold text-emerald-600 mt-1">{{ number_format($totalTerjual, 0, ',', '.') }} eks</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Retur Fisik</span>
                <div class="text-lg font-extrabold text-rose-600 mt-1">{{ number_format($totalRetur, 0, ',', '.') }} eks</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Jatah Cuma-cuma (Gratis)</span>
                <div class="text-lg font-extrabold text-purple-700 mt-1">{{ number_format($totalGratis, 0, ',', '.') }} eks</div>
            </div>
        </div>

        <!-- Tabel Per Rute -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Kode & Jalur Rute</th>
                            <th class="px-4 py-3 font-semibold text-right">Oplah Dikirim</th>
                            <th class="px-4 py-3 font-semibold text-right">Jatah Gratis</th>
                            <th class="px-4 py-3 font-semibold text-right">Retur Fisik</th>
                            <th class="px-4 py-3 font-semibold text-right">% Retur</th>
                            <th class="px-5 py-3 font-semibold text-right">Terjual Efektif</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($rekapRute as $rr)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3">
                                <span class="font-mono font-bold text-slate-800 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200 mr-1.5">{{ $rr->kode_rute }}</span>
                                <span class="font-bold text-slate-900">{{ $rr->nama_jurusan }}</span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-slate-800">{{ number_format($rr->cetak, 0, ',', '.') }} eks</td>
                            <td class="px-4 py-3 text-right text-purple-700 font-semibold">{{ number_format($rr->gratis, 0, ',', '.') }} eks</td>
                            <td class="px-4 py-3 text-right text-rose-600 font-semibold">{{ number_format($rr->retur, 0, ',', '.') }} eks</td>
                            <td class="px-4 py-3 text-right font-bold text-rose-600">{{ $rr->retur_pct }}%</td>
                            <td class="px-5 py-3 text-right font-extrabold text-emerald-700 text-sm">{{ number_format($rr->terjual, 0, ',', '.') }} eks</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
