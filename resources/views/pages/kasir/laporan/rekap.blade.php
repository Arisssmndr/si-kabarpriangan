<x-app-layout>
    <div class="space-y-6" x-data="{ activeTab: 'semua' }">

        <!-- Header Laporan Rekapitulasi Kas -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Rekapitulasi Kas Masuk & Keluar (KAS.EXE)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-indigo-100 text-indigo-800 border border-indigo-300">
                            Menu Cetak DOS
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekap mutasi harian dan bulanan seluruh bagian: Iklan (01), Sirkulasi (02), Umum (03), Redaksi (06), BPR (14)
                    </p>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="flex items-center gap-2 print:hidden">
                <a href="{{ route('kasir.laporan.rekap.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                   class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Export Excel / CSV</span>
                </a>
                <button type="button" onclick="window.print()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Dokumen</span>
                </button>
            </div>
        </div>

        <!-- Filter Form Periode & Bagian -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:hidden">
            <form method="GET" action="{{ route('kasir.laporan.rekap') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" 
                           class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-indigo-500 text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Sampai</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" 
                           class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-indigo-500 text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Filter Kode Bagian</label>
                    <select name="bagian" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-indigo-500 text-slate-800">
                        <option value="all" {{ $bagianFilter === 'all' ? 'selected' : '' }}>Semua Bagian (Konsolidasi)</option>
                        <option value="01" {{ $bagianFilter === '01' ? 'selected' : '' }}>01 - Iklan & Komisi</option>
                        <option value="02" {{ $bagianFilter === '02' ? 'selected' : '' }}>02 - Sirkulasi & Ekspedisi</option>
                        <option value="03" {{ $bagianFilter === '03' ? 'selected' : '' }}>03 - Umum & SDM</option>
                        <option value="06" {{ $bagianFilter === '06' ? 'selected' : '' }}>06 - Redaksi & Wartawan</option>
                        <option value="14" {{ $bagianFilter === '14' ? 'selected' : '' }}>14 - Kas Bank BJB / BPR</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg transition shadow-2xs">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Metric Cards Saldo Rekap -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Kas Masuk (KI)</span>
                <div class="mt-2 text-xl font-black text-teal-700">
                    Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">{{ $kasMasuks->count() }} transaksi penerimaan</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Kas Keluar (KO)</span>
                <div class="mt-2 text-xl font-black text-rose-700">
                    Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">{{ $kasKeluars->count() }} transaksi pengeluaran</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Saldo Surplus / (Defisit)</span>
                <div class="mt-2 text-xl font-black {{ $saldoSurplusDefisit >= 0 ? 'text-emerald-700' : 'text-amber-700' }}">
                    Rp {{ number_format($saldoSurplusDefisit, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">{{ $saldoSurplusDefisit >= 0 ? 'Surplus Kas Periode Ini' : 'Defisit Pengeluaran Lebih Besar' }}</div>
            </div>
        </div>

        <!-- Tab Selector: Kas Masuk / Kas Keluar -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2 print:hidden">
            <button type="button" @click="activeTab = 'semua'" :class="activeTab === 'semua' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition">
                Semua Mutasi
            </button>
            <button type="button" @click="activeTab = 'masuk'" :class="activeTab === 'masuk' ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition">
                Kas Masuk (KI - {{ $kasMasuks->count() }})
            </button>
            <button type="button" @click="activeTab = 'keluar'" :class="activeTab === 'keluar' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition">
                Kas Keluar (KO - {{ $kasKeluars->count() }})
            </button>
        </div>

        <!-- Tabel Kas Masuk (KI) -->
        <div x-show="activeTab === 'semua' || activeTab === 'masuk'" class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-teal-50/40">
                <h3 class="text-xs font-bold text-teal-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-600"></span>
                    Rincian Kas Masuk (KI - Penerimaan)
                </h3>
                <span class="text-xs font-black text-teal-800">Total: Rp {{ number_format($totalMasuk, 0, ',', '.') }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">No. Kuitansi</th>
                            <th class="px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Bagian & COA</th>
                            <th class="px-4 py-3 font-semibold">Terima Dari / Pemasang</th>
                            <th class="px-4 py-3 font-semibold text-right">Jumlah (Rp)</th>
                            <th class="px-4 py-3 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($kasMasuks as $ki)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-bold text-teal-900">{{ $ki->no_kuitansi ?? $ki->no_bkm }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $ki->tanggal_terima ? \Carbon\Carbon::parse($ki->tanggal_terima)->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 font-bold text-[10px] text-slate-700">Bagian {{ $ki->kode_bagian }}</span>
                                <div class="font-mono text-[10px] text-slate-500">{{ $ki->kode_perkiraan }}</div>
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-800">{{ $ki->terima_dari ?? $ki->nama_pembayar }}</td>
                            <td class="px-4 py-3 text-right font-mono font-extrabold text-teal-800">
                                Rp {{ number_format($ki->jumlah_diterima ?? $ki->jumlah_dibayar, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ strtoupper($ki->status_bayar ?? 'Lunas') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-6 text-center text-slate-400">Tidak ada transaksi kas masuk pada periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Kas Keluar (KO) -->
        <div x-show="activeTab === 'semua' || activeTab === 'keluar'" class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-rose-50/40">
                <h3 class="text-xs font-bold text-rose-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                    Rincian Kas Keluar (KO - Realisasi BP / Beban)
                </h3>
                <span class="text-xs font-black text-rose-800">Total: Rp {{ number_format($totalKeluar, 0, ',', '.') }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">No. BKK (KO)</th>
                            <th class="px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">No. Register BP</th>
                            <th class="px-4 py-3 font-semibold">Bagian & COA</th>
                            <th class="px-4 py-3 font-semibold">Diberikan Kepada</th>
                            <th class="px-4 py-3 font-semibold text-right">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($kasKeluars as $ko)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-bold text-rose-900">{{ $ko->no_bkk }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $ko->tanggal_keluar ? \Carbon\Carbon::parse($ko->tanggal_keluar)->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3 font-mono text-[10px] text-amber-800 font-bold">
                                {{ $ko->no_register_anggaran ?? $ko->no_bp_ref }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 font-bold text-[10px] text-slate-700">Bagian {{ $ko->kode_bagian }}</span>
                                <div class="font-mono text-[10px] text-slate-500">{{ $ko->kode_perkiraan }}</div>
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-800">{{ $ko->diberikan_kepada ?? $ko->nama_penerima }}</td>
                            <td class="px-4 py-3 text-right font-mono font-extrabold text-rose-800">
                                Rp {{ number_format($ko->jumlah_keluar, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-6 text-center text-slate-400">Tidak ada transaksi kas keluar pada periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
