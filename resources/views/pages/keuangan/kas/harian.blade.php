<x-app-layout>
    <div class="space-y-6">

        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('keuangan.kas.index') }}" class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition">
                    ←
                </a>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                        LAPORAN KAS HARIAN
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekapitulasi penerimaan dan pengeluaran kas per tanggal
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('keuangan.kas.cetak', ['mode' => 'harian', 'tanggal' => $tanggal]) }}" target="_blank"
                   class="h-9 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Laporan Harian (PDF)</span>
                </a>
            </div>
        </div>

        <!-- Filter Tanggal Harian -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('keuangan.kas.harian') }}" class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-700">Pilih Tanggal:</span>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="text-xs rounded-xl border border-slate-300 px-3 py-2 font-mono">
                <button type="submit" class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition">
                    Tampilkan Rekap
                </button>
            </form>
        </div>

        <!-- KPI Ringkas Harian -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 font-mono">Saldo Awal Hari</span>
                <div class="mt-2 text-xl font-black text-slate-900 font-mono">
                    Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                </div>
            </div>

            <div class="bg-white border border-emerald-200 rounded-2xl p-4 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 font-mono">Total Masuk Hari Ini</span>
                <div class="mt-2 text-xl font-black text-emerald-600 font-mono">
                    + Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                </div>
            </div>

            <div class="bg-white border border-rose-200 rounded-2xl p-4 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-700 font-mono">Total Keluar Hari Ini</span>
                <div class="mt-2 text-xl font-black text-rose-600 font-mono">
                    - Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                </div>
            </div>

            <div class="bg-white border border-blue-200 rounded-2xl p-4 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 font-mono">Saldo Akhir Hari Ini</span>
                <div class="mt-2 text-xl font-black text-blue-700 font-mono">
                    Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- Tabel Transaksi Hari Ini -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">
                    Rincian Arus Kas: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. Transaksi</th>
                            <th class="py-3 px-4">Kategori</th>
                            <th class="py-3 px-4">Keterangan</th>
                            <th class="py-3 px-4 text-right">Kas Masuk (Rp)</th>
                            <th class="py-3 px-4 text-right">Kas Keluar (Rp)</th>
                            <th class="py-3 px-4 text-right">Saldo Berjalan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($transaksis as $t)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 font-mono font-bold text-slate-800">{{ $t->no_transaksi }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-800">{{ $t->kategori_kas }}</td>
                                <td class="py-3 px-4 text-slate-700">{{ $t->keterangan }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-emerald-600">
                                    {{ $t->jenis === 'masuk' ? '+' . number_format($t->nominal, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-rose-600">
                                    {{ $t->jenis === 'keluar' ? '-' . number_format($t->nominal, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-black text-slate-900">
                                    Rp {{ number_format($t->saldo_akhir, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-400">
                                    Tidak ada mutasi kas pada tanggal {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
