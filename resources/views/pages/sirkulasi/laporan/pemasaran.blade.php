<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (60-30-10 Palette) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Rekap Pemasaran (Jatah Fisik Oplah)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Agregat distribusi oplah koran cetak, retur fisik, jatah gratis, dan serapan penjualan per rute.
                    </p>
                </div>
            </div>
        </div>

        <!-- Toolbar Filter Periode -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.laporan.pemasaran') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div class="w-full sm:w-64">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Periode Bulan</label>
                    <input type="month"
                           name="bulan"
                           value="{{ $bulanPilihan }}"
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>
                <button type="submit" class="h-11 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    Filter Periode
                </button>
            </form>
        </div>

        <!-- 4 KPI Serapan Oplah -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Produksi Cetak</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    {{ number_format($totalCetak, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Seluruh rute distribusi</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Koran Terjual Efektif</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalTerjual, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Serapan bersih komersial</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Retur Fisik</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalRetur, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Pengembalian BAPR agen</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jatah Cuma-cuma (Gratis)</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalGratis, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Bukti iklan &amp; legal redaksi</span>
            </div>
        </div>

        <!-- Tabel Rekap Per Rute -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Rincian Serapan Oplah per Rute Armada</h2>
                    <p class="text-xs text-slate-500">Periode {{ \Carbon\Carbon::parse($bulanPilihan . '-01')->translatedFormat('F Y') }}</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ count($rekapRute) }} Rute Distribusi
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5 w-28">Kode Rute</th>
                            <th class="py-3 px-3.5">Jalur Distribusi</th>
                            <th class="py-3 px-3.5 text-right">Oplah Dikirim</th>
                            <th class="py-3 px-3.5 text-right">Jatah Gratis</th>
                            <th class="py-3 px-3.5 text-right">Retur Fisik</th>
                            <th class="py-3 px-3.5 text-right">% Retur</th>
                            <th class="py-3 px-3.5 text-right">Terjual Efektif</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                        @forelse($rekapRute as $idx => $rr)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">{{ $rr->kode_rute }}</td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $rr->nama_jurusan }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-800 tabular-nums">{{ number_format($rr->cetak, 0, ',', '.') }} eks</td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums">{{ number_format($rr->gratis, 0, ',', '.') }} eks</td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums">{{ number_format($rr->retur, 0, ',', '.') }} eks</td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums">{{ $rr->retur_pct }}%</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($rr->terjual, 0, ',', '.') }} eks</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-500 font-normal">
                                    Tidak ada data distribusi pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($rekapRute) > 0)
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                            <tr>
                                <td colspan="3" class="py-3 px-3.5 text-right font-sans text-xs uppercase tracking-wider text-slate-600">Total Akumulasi:</td>
                                <td class="py-3 px-3.5 text-right tabular-nums">{{ number_format($totalCetak, 0, ',', '.') }} eks</td>
                                <td class="py-3 px-3.5 text-right text-slate-600 tabular-nums">{{ number_format($totalGratis, 0, ',', '.') }} eks</td>
                                <td class="py-3 px-3.5 text-right text-slate-600 tabular-nums">{{ number_format($totalRetur, 0, ',', '.') }} eks</td>
                                <td class="py-3 px-3.5 text-right text-slate-500">-</td>
                                <td class="py-3 px-3.5 text-right font-bold text-kp-blue-700 tabular-nums">{{ number_format($totalTerjual, 0, ',', '.') }} eks</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
