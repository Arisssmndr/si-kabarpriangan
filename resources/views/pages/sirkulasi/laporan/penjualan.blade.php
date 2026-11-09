<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (60-30-10 Palette) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Rekapitulasi Penjualan &amp; Piutang Sirkulasi
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Omset kotor pengiriman, potongan BAPR retur fisik, omset bersih, dan saldo piutang per agen.
                    </p>
                </div>
            </div>
        </div>

        <!-- Toolbar Filter Periode -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.laporan.penjualan') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
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

        <!-- 4 KPI Finansial (60-30-10 Palette) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Omset Bersih Sirkulasi</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($grandBersih, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Tagihan bersih setelah retur</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Omset Kotor</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($grandKotor, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Nilai bruto pengiriman koran</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Potongan Retur Resmi</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($grandRetur, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Pengurang tagihan via BAPR</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Saldo Piutang</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($grandPiutang, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Sisa kewajiban agen berjalan</span>
            </div>
        </div>

        <!-- Tabel Rekap Penjualan per Agen -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Rincian Tagihan &amp; Piutang Koran per Agen</h2>
                    <p class="text-xs text-slate-500">Periode {{ \Carbon\Carbon::parse($bulanPilihan . '-01')->translatedFormat('F Y') }}</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ count($rekapAgen) }} Agen
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3 text-center w-12">No</th>
                            <th class="py-3 px-3">Kode Agen</th>
                            <th class="py-3 px-3">Nama Agen</th>
                            <th class="py-3 px-3">Kategori</th>
                            <th class="py-3 px-3 text-right">Kirim (Eks)</th>
                            <th class="py-3 px-3 text-right">Retur (Eks)</th>
                            <th class="py-3 px-3 text-right">Terjual (Eks)</th>
                            <th class="py-3 px-3 text-right">Tagihan Kotor</th>
                            <th class="py-3 px-3 text-right">Potongan Retur</th>
                            <th class="py-3 px-3 text-right">Tagihan Bersih</th>
                            <th class="py-3 px-3 text-right">Sisa Piutang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                        @forelse($rekapAgen as $idx => $ra)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-3 font-mono font-bold text-kp-blue-700 text-sm">{{ $ra->kode_agen }}</td>
                                <td class="py-3.5 px-3 font-semibold text-slate-900">{{ $ra->nama_agen }}</td>
                                <td class="py-3.5 px-3">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $ra->kategori_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-right font-mono text-slate-700 tabular-nums">{{ number_format($ra->total_kirim, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3 text-right font-mono text-slate-600 tabular-nums">{{ number_format($ra->retur_eks, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3 text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($ra->terjual_eks, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3 text-right font-mono text-slate-700 tabular-nums">Rp {{ number_format($ra->tagihan_kotor, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3 text-right font-mono text-slate-500 tabular-nums">- Rp {{ number_format($ra->potongan_retur, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3 text-right font-mono font-semibold text-slate-900 tabular-nums">Rp {{ number_format($ra->tagihan_bersih, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-3 text-right font-mono text-slate-700 tabular-nums">Rp {{ number_format($ra->saldo_piutang, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-12 text-center text-slate-500 font-normal">
                                    Tidak ada data penjualan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($rekapAgen) > 0)
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                            <tr>
                                <td colspan="7" class="py-3 px-3 text-right font-sans text-xs uppercase tracking-wider text-slate-600">Total Keseluruhan:</td>
                                <td class="py-3 px-3 text-right tabular-nums">Rp {{ number_format($grandKotor, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right text-slate-600 tabular-nums">- Rp {{ number_format($grandRetur, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-bold text-kp-blue-700 tabular-nums">Rp {{ number_format($grandBersih, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right tabular-nums">Rp {{ number_format($grandPiutang, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
