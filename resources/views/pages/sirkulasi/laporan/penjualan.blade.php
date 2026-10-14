<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Rekapitulasi Penjualan & Piutang Sirkulasi</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-emerald-50 text-emerald-700 border border-emerald-200">Keuangan Sirkulasi</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Omset kotor pengiriman, potongan BAPR retur fisik, omset bersih, dan akumulasi saldo piutang koran per agen.
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('sirkulasi.laporan.penjualan') }}" class="flex items-center gap-2">
                <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="text-xs rounded-lg border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                <button type="submit" class="py-2 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition">
                    Filter Periode
                </button>
            </form>
        </div>

        <!-- 4 KPI Finansial -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Omset Kotor</span>
                <div class="text-lg font-extrabold text-slate-900 mt-1">Rp {{ number_format($grandKotor, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Potongan Retur Resmi</span>
                <div class="text-lg font-extrabold text-rose-600 mt-1">- Rp {{ number_format($grandRetur, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Omset Bersih Sirkulasi</span>
                <div class="text-lg font-extrabold text-emerald-600 mt-1">Rp {{ number_format($grandBersih, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Saldo Piutang Agen</span>
                <div class="text-lg font-extrabold text-amber-700 mt-1">Rp {{ number_format($grandPiutang, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Tabel Per Agen -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Nama Agen & Kategori</th>
                            <th class="px-4 py-3 font-semibold text-right">Kirim (Eks)</th>
                            <th class="px-4 py-3 font-semibold text-right">Retur (Eks)</th>
                            <th class="px-4 py-3 font-semibold text-right">Terjual (Eks)</th>
                            <th class="px-4 py-3 font-semibold text-right">Tagihan Kotor</th>
                            <th class="px-4 py-3 font-semibold text-right">Potongan Retur</th>
                            <th class="px-4 py-3 font-semibold text-right">Tagihan Bersih</th>
                            <th class="px-4 py-3 font-semibold text-right">Sisa Piutang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($rekapAgen as $ra)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $ra->nama_agen }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $ra->kode_agen }} &bull; {{ $ra->kategori_label }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-slate-800">{{ number_format($ra->total_kirim, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-rose-600 font-semibold">{{ number_format($ra->retur_eks, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-extrabold text-emerald-700">{{ number_format($ra->terjual_eks, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-slate-700">Rp {{ number_format($ra->tagihan_kotor, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-rose-600">- Rp {{ number_format($ra->potongan_retur, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-extrabold text-slate-900">Rp {{ number_format($ra->tagihan_bersih, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-bold text-amber-700">Rp {{ number_format($ra->saldo_piutang, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
