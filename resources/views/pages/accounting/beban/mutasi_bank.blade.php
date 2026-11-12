<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Mutasi Rekening Bank
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Bank & Kas
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Monitoring arus kas bank dan verifikasi status rekonsiliasi kasir.
                    </p>
                </div>
            </div>

            <!-- Filter Bulan -->
            <form method="GET" action="{{ route('accounting.beban.mutasi-bank') }}" class="flex items-center gap-2">
                <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="h-9 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 transition" />
                <button type="submit" class="h-9 px-3.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                    Filter
                </button>
            </form>
        </div>

        <!-- Info Card: Rekonsiliasi Kasir -->
        <div class="bg-amber-50/70 border border-amber-200 rounded-xl p-4 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </span>
                <div>
                    <span class="text-xs font-bold text-amber-900">Catatan Rekonsiliasi Bank</span>
                    <p class="text-xs text-amber-800 mt-0.5">Proses rekonsiliasi mutasi rekening koran fisik dikelola oleh Divisi Kasir. Divisi Akuntansi memvalidasi status rekonsiliasi sebagai prasyarat Tutup Buku.</p>
                </div>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-amber-300 text-amber-800 shrink-0">
                Tervalidasi Kasir
            </span>
        </div>

        <!-- 3 KPI Cards Mutasi Bank -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Mutasi Masuk (Kredit Bank)</span>
                <div class="text-xl font-bold text-kp-blue-700 mt-1">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Mutasi Keluar (Debet Bank)</span>
                <div class="text-xl font-bold text-slate-900 mt-1">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Net Arus Kas Bank</span>
                <div class="text-xl font-bold text-slate-900 mt-1">Rp {{ number_format($saldoMutasi, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Mutasi Masuk -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="p-3.5 border-b border-slate-200 bg-kp-blue-50/50">
                    <h2 class="text-xs font-bold text-kp-blue-800 uppercase">Penerimaan Transfer Bank Masuk</h2>
                </div>
                <div class="overflow-x-auto max-h-[350px]">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] font-bold text-[11px] uppercase tracking-wider whitespace-nowrap sticky top-0">
                            <tr>
                                <th class="py-2.5 px-3">Tanggal</th>
                                <th class="py-2.5 px-3">No. Bukti</th>
                                <th class="py-2.5 px-3">Keterangan</th>
                                <th class="py-2.5 px-3 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($mutasiMasuk as $in)
                                <tr>
                                    <td class="py-2 px-3">{{ \Carbon\Carbon::parse($in->tanggal)->format('d/m/Y') }}</td>
                                    <td class="py-2 px-3 font-bold text-slate-900 font-mono">{{ $in->no_transaksi ?: ('BKM-' . $in->id) }}</td>
                                    <td class="py-2 px-3 max-w-[180px] truncate">{{ $in->keterangan }}</td>
                                    <td class="py-2 px-3 text-right font-mono font-bold text-slate-900">
                                        Rp {{ number_format($in->nominal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-slate-400">Tidak ada mutasi masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mutasi Keluar -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="p-3.5 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-xs font-bold text-slate-800 uppercase">Pengeluaran Transfer Bank Keluar</h2>
                </div>
                <div class="overflow-x-auto max-h-[350px]">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] font-bold text-[11px] uppercase tracking-wider whitespace-nowrap sticky top-0">
                            <tr>
                                <th class="py-2.5 px-3">Tanggal</th>
                                <th class="py-2.5 px-3">No. Bukti</th>
                                <th class="py-2.5 px-3">Keterangan</th>
                                <th class="py-2.5 px-3 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($mutasiKeluar as $out)
                                <tr>
                                    <td class="py-2 px-3">{{ \Carbon\Carbon::parse($out->tanggal_keluar)->format('d/m/Y') }}</td>
                                    <td class="py-2 px-3 font-bold text-slate-900 font-mono">{{ $out->no_bkk ?: ('BKK-' . $out->id) }}</td>
                                    <td class="py-2 px-3 max-w-[180px] truncate">{{ $out->keterangan }}</td>
                                    <td class="py-2 px-3 text-right font-mono font-bold text-slate-900">
                                        Rp {{ number_format($out->nominal_keluar ?: $out->jumlah_keluar ?: 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-slate-400">Tidak ada mutasi keluar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>

