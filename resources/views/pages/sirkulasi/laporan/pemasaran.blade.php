<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-kp-blue-50 text-kp-blue-600 border border-kp-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-kp-blue-600 uppercase tracking-wider">Divisi Sirkulasi</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Rekap Pemasaran (Jatah Fisik Oplah)</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Laporan agregat distribusi oplah koran cetak, retur, jatah gratis, dan serapan penjualan per rute.
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('sirkulasi.laporan.pemasaran') }}" class="flex items-center gap-2">
                <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="text-xs rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                <button type="submit" class="py-2 px-3 text-xs font-semibold rounded-lg bg-kp-blue-600 text-white hover:bg-kp-blue-700 transition shadow-2xs">
                    Filter Periode
                </button>
            </form>
        </div>

        <!-- 4 KPI Serapan (60-30-10) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Produksi Cetak</span>
                <div class="text-lg font-bold text-slate-900 font-mono mt-1">{{ number_format($totalCetak, 0, ',', '.') }} eks</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Koran Terjual Efektif</span>
                <div class="text-lg font-bold text-slate-900 font-mono mt-1">{{ number_format($totalTerjual, 0, ',', '.') }} eks</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Retur Fisik</span>
                <div class="text-lg font-bold text-slate-900 font-mono mt-1">{{ number_format($totalRetur, 0, ',', '.') }} eks</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Jatah Gratis</span>
                <div class="text-lg font-bold text-slate-900 font-mono mt-1">{{ number_format($totalGratis, 0, ',', '.') }} eks</div>
            </div>
        </div>

        <!-- Tabel Per Rute (Kolom Terpisah) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left min-w-[950px]">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] uppercase text-[10px] tracking-wider font-bold">
                        <tr>
                            <th class="px-3.5 py-3 whitespace-nowrap w-24">Kode Rute</th>
                            <th class="px-4 py-3 min-w-[200px]">Nama Jalur Rute</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap w-32">Oplah Dikirim</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap w-28">Jatah Gratis</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap w-28">Retur Fisik</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap w-24">% Retur</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap w-32">Terjual Efektif</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($rekapRute as $rr)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Kode Rute (Kolom Terpisah) -->
                            <td class="px-3.5 py-3 whitespace-nowrap">
                                <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded text-xs border border-slate-200">{{ $rr->kode_rute }}</span>
                            </td>

                            <!-- Nama Jalur Rute (Kolom Terpisah) -->
                            <td class="px-4 py-3 font-bold text-slate-900 text-xs">
                                {{ $rr->nama_jurusan }}
                            </td>

                            <!-- Oplah Dikirim -->
                            <td class="px-4 py-3 text-right font-mono text-slate-800 text-xs whitespace-nowrap">
                                {{ number_format($rr->cetak, 0, ',', '.') }} eks
                            </td>

                            <!-- Jatah Gratis -->
                            <td class="px-4 py-3 text-right font-mono text-slate-600 text-xs whitespace-nowrap">
                                {{ number_format($rr->gratis, 0, ',', '.') }} eks
                            </td>

                            <!-- Retur Fisik -->
                            <td class="px-4 py-3 text-right font-mono text-slate-600 text-xs whitespace-nowrap">
                                {{ number_format($rr->retur, 0, ',', '.') }} eks
                            </td>

                            <!-- % Retur -->
                            <td class="px-4 py-3 text-right font-mono text-slate-700 text-xs whitespace-nowrap">
                                {{ $rr->retur_pct }}%
                            </td>

                            <!-- Terjual Efektif -->
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900 text-xs whitespace-nowrap">
                                {{ number_format($rr->terjual, 0, ',', '.') }} eks
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
