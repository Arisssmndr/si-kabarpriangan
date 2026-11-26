<x-app-layout>
    <div class="space-y-5">

        <!-- 1. Header Halaman (Palet 60-30-10) -->
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

            <div class="flex items-center gap-2 shrink-0">
                <span class="text-xs font-semibold px-3 py-2 rounded-xl bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>Tervalidasi Kasir</span>
                </span>
            </div>
        </div>

        <!-- 2. KPI Cards Mutasi Bank (Report Info) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Mutasi Masuk</span>
                <div class="text-xl font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
                <p class="text-xs text-slate-400 mt-0.5">Penerimaan transfer & kasir</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Mutasi Keluar</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</div>
                <p class="text-xs text-slate-400 mt-0.5">Pengeluaran & beban bank</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Net Arus Kas Bank</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($saldoMutasi, 0, ',', '.') }}</div>
                <p class="text-xs text-slate-400 mt-0.5">Surplus / defisit kas bank</p>
            </div>
        </div>

        <!-- 3. Toolbar Pencarian & Filter Mandiri (Format Standar Divisi Iklan) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.beban.mutasi-bank') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Mutasi</label>
                    <input type="text" 
                           name="q" 
                           value="{{ $q ?? '' }}" 
                           placeholder="Ketik no bukti, keterangan, penerima..." 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pos Rekening / Kas</label>
                    <select name="rekening" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all" {{ ($rekening ?? 'all') === 'all' ? 'selected' : '' }}>Semua Rekening Bank &amp; Kas</option>
                        <option value="bca" {{ ($rekening ?? '') === 'bca' ? 'selected' : '' }}>BCA Giro (054-067-6270)</option>
                        <option value="bjb" {{ ($rekening ?? '') === 'bjb' ? 'selected' : '' }}>BJB Giro (001-002-3456)</option>
                        <option value="kasir" {{ ($rekening ?? '') === 'kasir' ? 'selected' : '' }}>Kas Operasional / Kasir</option>
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bulan Mutasi</label>
                    <input type="month" 
                           name="bulan" 
                           value="{{ $bulanPilihan }}" 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'rekening']) || request('bulan') != date('Y-m'))
                        <a href="{{ route('accounting.beban.mutasi-bank') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 4. Pure Tabel Data Mutasi Masuk & Keluar -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Mutasi Masuk -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5">Tanggal</th>
                                <th class="py-3 px-3.5">No. Bukti</th>
                                <th class="py-3 px-3.5">Keterangan</th>
                                <th class="py-3 px-3.5 text-right">Nominal Masuk</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 text-sm whitespace-nowrap">
                            @forelse($mutasiMasuk as $in)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-3.5 text-slate-600">{{ \Carbon\Carbon::parse($in->tanggal)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $in->no_transaksi ?: ('BKM-' . $in->id) }}</td>
                                    <td class="py-3 px-3.5 max-w-[180px] truncate">{{ $in->keterangan }}</td>
                                    <td class="py-3 px-3.5 text-right font-mono font-bold text-kp-blue-700">
                                        Rp {{ number_format($in->nominal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400 text-sm">Tidak ada mutasi penerimaan transfer.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mutasi Keluar -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5">Tanggal</th>
                                <th class="py-3 px-3.5">No. Bukti</th>
                                <th class="py-3 px-3.5">Keterangan</th>
                                <th class="py-3 px-3.5 text-right">Nominal Keluar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 text-sm whitespace-nowrap">
                            @forelse($mutasiKeluar as $out)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-3.5 text-slate-600">{{ \Carbon\Carbon::parse($out->tanggal_keluar)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $out->no_bkk ?: ('BKK-' . $out->id) }}</td>
                                    <td class="py-3 px-3.5 max-w-[180px] truncate">{{ $out->keterangan }}</td>
                                    <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">
                                        Rp {{ number_format($out->nominal_keluar ?: $out->jumlah_keluar ?: 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400 text-sm">Tidak ada mutasi pengeluaran bank.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>