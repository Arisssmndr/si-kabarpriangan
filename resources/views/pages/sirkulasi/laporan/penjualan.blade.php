<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-kp-blue-50 text-kp-blue-600 border border-kp-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-kp-blue-600 uppercase tracking-wider">Divisi Sirkulasi</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Rekapitulasi Penjualan & Piutang</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Omset pengiriman, potongan BAPR retur fisik, omset bersih, dan saldo piutang koran per agen.
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('sirkulasi.laporan.penjualan') }}" class="flex items-center gap-2">
                <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="text-xs rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                <button type="submit" class="py-2 px-3 text-xs font-semibold rounded-lg bg-kp-blue-600 text-white hover:bg-kp-blue-700 transition shadow-2xs">
                    Filter Periode
                </button>
            </form>
        </div>

        <!-- 4 KPI Finansial (60-30-10) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Omset Kotor</span>
                <div class="text-lg font-bold text-slate-900 font-mono mt-1">Rp {{ number_format($grandKotor, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Potongan Retur</span>
                <div class="text-lg font-bold text-slate-900 font-mono mt-1">- Rp {{ number_format($grandRetur, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Omset Bersih Sirkulasi</span>
                <div class="text-lg font-bold text-slate-900 font-mono mt-1">Rp {{ number_format($grandBersih, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Saldo Piutang Agen</span>
                <div class="text-lg font-bold text-slate-900 font-mono mt-1">Rp {{ number_format($grandPiutang, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Tabel Per Agen (Kolom Terpisah) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left min-w-[1150px]">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] uppercase text-[10px] tracking-wider font-bold">
                        <tr>
                            <th class="px-3.5 py-3 whitespace-nowrap w-28">Kode Agen</th>
                            <th class="px-4 py-3 min-w-[170px]">Nama Agen</th>
                            <th class="px-3.5 py-3 whitespace-nowrap w-28 text-center">Kategori</th>
                            <th class="px-3.5 py-3 font-semibold text-right whitespace-nowrap w-24">Kirim (Eks)</th>
                            <th class="px-3.5 py-3 font-semibold text-right whitespace-nowrap w-24">Retur (Eks)</th>
                            <th class="px-3.5 py-3 font-semibold text-right whitespace-nowrap w-24">Terjual (Eks)</th>
                            <th class="px-4 py-3 font-semibold text-right whitespace-nowrap w-32">Tagihan Kotor</th>
                            <th class="px-4 py-3 font-semibold text-right whitespace-nowrap w-32">Potongan Retur</th>
                            <th class="px-4 py-3 font-semibold text-right whitespace-nowrap w-32">Tagihan Bersih</th>
                            <th class="px-4 py-3 font-semibold text-right whitespace-nowrap w-32">Sisa Piutang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($rekapAgen as $ra)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Kode Agen (Kolom Terpisah) -->
                            <td class="px-3.5 py-3 whitespace-nowrap">
                                <span class="inline-block px-2 py-0.5 rounded font-mono font-bold text-slate-900 bg-slate-100 text-xs border border-slate-200">
                                    {{ $ra->kode_agen }}
                                </span>
                            </td>

                            <!-- Nama Agen (Kolom Terpisah) -->
                            <td class="px-4 py-3 font-bold text-slate-900 text-xs">
                                {{ $ra->nama_agen }}
                            </td>

                            <!-- Kategori (Kolom Terpisah) -->
                            <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $ra->kategori_label }}
                                </span>
                            </td>

                            <!-- Kirim -->
                            <td class="px-3.5 py-3 text-right font-mono text-slate-800 text-xs whitespace-nowrap">
                                {{ number_format($ra->total_kirim, 0, ',', '.') }}
                            </td>

                            <!-- Retur -->
                            <td class="px-3.5 py-3 text-right font-mono text-slate-600 text-xs whitespace-nowrap">
                                {{ number_format($ra->retur_eks, 0, ',', '.') }}
                            </td>

                            <!-- Terjual -->
                            <td class="px-3.5 py-3 text-right font-mono font-bold text-slate-900 text-xs whitespace-nowrap">
                                {{ number_format($ra->terjual_eks, 0, ',', '.') }}
                            </td>

                            <!-- Tagihan Kotor -->
                            <td class="px-4 py-3 text-right font-mono text-slate-700 text-xs whitespace-nowrap">
                                Rp {{ number_format($ra->tagihan_kotor, 0, ',', '.') }}
                            </td>

                            <!-- Potongan Retur -->
                            <td class="px-4 py-3 text-right font-mono text-slate-600 text-xs whitespace-nowrap">
                                - Rp {{ number_format($ra->potongan_retur, 0, ',', '.') }}
                            </td>

                            <!-- Tagihan Bersih -->
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900 text-xs whitespace-nowrap">
                                Rp {{ number_format($ra->tagihan_bersih, 0, ',', '.') }}
                            </td>

                            <!-- Sisa Piutang -->
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900 text-xs whitespace-nowrap">
                                Rp {{ number_format($ra->saldo_piutang, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
