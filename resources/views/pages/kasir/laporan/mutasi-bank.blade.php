<x-app-layout>
    <div class="space-y-6">

        <!-- Header Mutasi Kas Bank -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Laporan Mutasi Kas Bank (BJB)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-blue-100 text-blue-800 border border-blue-300">
                            Bank BJB Tasikmalaya
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Monitoring berkala mutasi penerimaan bank, jasa giro bunga, dan setoran kliring harian/bulanan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 print:hidden">
                <button type="button" onclick="window.print()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan Bank</span>
                </button>
            </div>
        </div>

        <!-- Filter Bulan & Tahun -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:hidden">
            <form method="GET" action="{{ route('kasir.laporan.mutasi-bank') }}" class="flex items-center gap-3">
                <div class="w-44">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Bulan</label>
                    <select name="bulan" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 text-slate-800">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="w-32">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tahun</label>
                    <select name="tahun" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 text-slate-800">
                        @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="pt-5">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition shadow-2xs">
                        Tampilkan Mutasi
                    </button>
                </div>
            </form>
        </div>

        <!-- Metric Card -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Mutasi Bank Diterima</span>
                <div class="mt-2 text-xl font-black text-blue-700">
                    Rp {{ number_format($totalPenerimaanBank, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Periode: {{ \Carbon\Carbon::create(null, (int)$bulan, 1)->translatedFormat('F') }} {{ $tahun }}</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Jumlah Transaksi Masuk</span>
                <div class="mt-2 text-xl font-black text-slate-800">
                    {{ $mutasiBank->count() }} Kali Mutasi
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Rekening Koran Bank BJB Tasikmalaya</div>
            </div>
        </div>

        <!-- Tabel Mutasi Bank -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-blue-50/40">
                <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    Rincian Mutasi Rekening Bank BJB
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">No. Kuitansi (KI)</th>
                            <th class="px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Uraian / Terima Dari</th>
                            <th class="px-4 py-3 font-semibold">No. Blanko / Ref Bank</th>
                            <th class="px-4 py-3 font-semibold">COA Perkiraan</th>
                            <th class="px-4 py-3 font-semibold text-right">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($mutasiBank as $m)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-bold text-blue-900">{{ $m->no_kuitansi ?? $m->no_bkm }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $m->tanggal_terima ? \Carbon\Carbon::parse($m->tanggal_terima)->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-800">{{ $m->terima_dari ?? $m->nama_pembayar }}</div>
                                <div class="text-[10px] text-slate-500">{{ $m->keterangan }}</div>
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600">
                                {{ $m->no_referensi_transfer ?? ($m->no_blanko ? 'Blanko: ' . $m->no_blanko : '-') }}
                            </td>
                            <td class="px-4 py-3 font-mono text-[11px] text-slate-700">
                                {{ $m->kode_perkiraan }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-black text-blue-800">
                                Rp {{ number_format($m->jumlah_diterima ?? $m->jumlah_dibayar, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-6 text-center text-slate-400">Tidak ada mutasi kas bank pada periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
