<x-app-layout>
    <div class="space-y-5">

        <!-- 1. Header Halaman (Palet 60-30-10 & Action Cetak) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Rincian Beban Operasional
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Analisis Biaya
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Daftar rincian realisasi seluruh beban kas kecil dan operasional per pos akun.
                    </p>
                </div>
            </div>

            <!-- Tombol Cetak Dokumen Resmi -->
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('accounting.laporan.rincian-beban.cetak', ['periode' => $periode]) }}" target="_blank" 
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Dokumen Resmi</span>
                </a>
            </div>
        </div>

        <!-- 2. KPI Summary Cards (Report Info) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jumlah Bukti Pengeluaran</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">{{ number_format($daftarBeban->count()) }} Bukti</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Realisasi BKK bulan {{ $periode }}</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Realisasi Beban</span>
                <div class="text-xl font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($totalBeban, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Total beban kasir terverifikasi</span>
            </div>
        </div>

        <!-- 3. Filter Periode Mandiri -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <form method="GET" action="{{ route('accounting.laporan.rincian-beban') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Periode Laporan</label>
                    <input type="month" 
                           name="periode" 
                           value="{{ $periode }}" 
                           class="h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Terapkan
                    </button>
                    <a href="{{ route('accounting.laporan.rincian-beban') }}" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- 4. Pure Tabel Transaksi Beban -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[350px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. BKK</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Diberikan Kepada</th>
                            <th class="py-3 px-3.5">Pos Akun</th>
                            <th class="py-3 px-3.5">Keterangan</th>
                            <th class="py-3 px-3.5 text-right w-44">Nominal Realisasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-sm whitespace-nowrap">
                        @forelse($daftarBeban as $key => $b)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-slate-400 font-mono text-xs">{{ $key + 1 }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $b->no_bkk ?: ('BKK-' . $b->id) }}</td>
                                <td class="py-3 px-3.5 text-slate-600">{{ \Carbon\Carbon::parse($b->tanggal_keluar)->format('d/m/Y') }}</td>
                                <td class="py-3 px-3.5">{{ $b->diberikan_kepada ?: ($b->nama_penerima ?: '-') }}</td>
                                <td class="py-3 px-3.5 font-mono text-xs">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-semibold">
                                        {{ $b->kode_perkiraan ?: '6.00.01' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 max-w-xs truncate text-slate-600">{{ $b->keterangan }}</td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($b->nominal_keluar ?: $b->jumlah_keluar ?: 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                    Tidak ada data beban operasional pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-kp-blue-50/70 border-t-2 border-slate-300 font-bold text-sm">
                            <td colspan="6" class="py-3 px-3.5 text-right uppercase text-xs">
                                TOTAL REALISASI BEBAN
                            </td>
                            <td class="py-3 px-3.5 text-right font-mono text-slate-900 font-bold">
                                Rp {{ number_format($totalBeban, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>