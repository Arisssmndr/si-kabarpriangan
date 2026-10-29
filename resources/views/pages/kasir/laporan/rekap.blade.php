<x-app-layout>
    <div class="space-y-6" x-data="{ activeTab: 'semua' }">

        <!-- Header Laporan Rekapitulasi Kas (Hidden on Print) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden no-print">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Rekapitulasi Kas Masuk & Keluar (Sistem KAS)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-indigo-100 text-indigo-800 border border-indigo-300">
                            Menu Cetak DOS
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekap mutasi harian dan bulanan seluruh divisi: Divisi Iklan, Sirkulasi, Umum, Redaksi, serta Kas Bank BJB / BPR
                    </p>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="flex items-center gap-2 print:hidden no-print">
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
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:hidden no-print">
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
                    <label class="block text-xs font-bold text-slate-700 mb-1">Filter Divisi / Bagian</label>
                    <select name="bagian" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-indigo-500 text-slate-800">
                        <option value="all" {{ $bagianFilter === 'all' ? 'selected' : '' }}>Semua Divisi (Konsolidasi)</option>
                        <option value="01" {{ $bagianFilter === '01' ? 'selected' : '' }}>Divisi Iklan & Komisi</option>
                        <option value="02" {{ $bagianFilter === '02' ? 'selected' : '' }}>Divisi Sirkulasi & Ekspedisi</option>
                        <option value="03" {{ $bagianFilter === '03' ? 'selected' : '' }}>Divisi Umum & SDM</option>
                        <option value="06" {{ $bagianFilter === '06' ? 'selected' : '' }}>Divisi Redaksi & Wartawan</option>
                        <option value="14" {{ $bagianFilter === '14' ? 'selected' : '' }}>Kas Bank BJB / BPR</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg transition shadow-2xs">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Kop Cetak Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print:block border-b-2 border-slate-900 pb-3 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black tracking-wider uppercase text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                    <p class="text-[10px] text-slate-600 font-semibold">DIVISI KASIR & KEUANGAN - SISTEM INFORMASI KAS (SIKAPRI)</p>
                    <p class="text-[9px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</p>
                </div>
                <div class="text-right">
                    <div class="text-xs font-black uppercase text-slate-900">LAPORAN REKAPITULASI KAS</div>
                    <div class="text-[10px] font-semibold text-slate-700">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</div>
                    <div class="text-[9px] text-slate-400 font-mono">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB</div>
                </div>
            </div>
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
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2 print:hidden no-print">
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
        <div x-show="activeTab === 'semua' || activeTab === 'masuk'" class="report-print-container bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden mb-6">
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
                            <th class="px-4 py-3 font-semibold">Divisi & COA</th>
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
                                @php
                                    $divisiKi = match($ki->kode_bagian) {
                                        '01' => 'Divisi Iklan',
                                        '02' => 'Divisi Sirkulasi',
                                        '03' => 'Divisi Umum',
                                        '06' => 'Divisi Redaksi',
                                        '14' => 'Kas Bank BJB',
                                        default => 'Divisi ' . ($ki->kode_bagian ?? 'Umum')
                                    };
                                @endphp
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 font-bold text-[10px] text-slate-700">{{ $divisiKi }}</span>
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
        <div x-show="activeTab === 'semua' || activeTab === 'keluar'" class="report-print-container bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
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
                            <th class="px-4 py-3 font-semibold">Divisi & COA</th>
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
                                @php
                                    $divisiKo = match($ko->kode_bagian) {
                                        '01' => 'Divisi Iklan',
                                        '02' => 'Divisi Sirkulasi',
                                        '03' => 'Divisi Umum',
                                        '06' => 'Divisi Redaksi',
                                        '14' => 'Kas Bank BJB',
                                        default => 'Divisi ' . ($ko->kode_bagian ?? 'Umum')
                                    };
                                @endphp
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 font-bold text-[10px] text-slate-700">{{ $divisiKo }}</span>
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

        <!-- Tanda Tangan Laporan Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print:grid grid-cols-3 gap-6 text-center text-xs pt-6 mt-6 border-t border-slate-300">
            <div>
                <div class="text-slate-500">Dibuat Oleh:</div>
                <div class="text-slate-700 font-semibold mt-1">Petugas Kasir</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">{{ Auth::user()->name }}</div>
                <div class="text-[10px] text-slate-500">Kasir Priangan</div>
            </div>
            <div>
                <div class="text-slate-500">Diperiksa Oleh:</div>
                <div class="text-slate-700 font-semibold mt-1">Bagian Akuntansi / Pajak</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                <div class="text-[10px] text-slate-500">Kabag Keuangan</div>
            </div>
            <div>
                <div class="text-slate-500">Disetujui Oleh:</div>
                <div class="text-slate-700 font-semibold mt-1">Pemimpin Perusahaan</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                <div class="text-[10px] text-slate-500">Direktur / GM</div>
            </div>
        </div>

    </div>
</x-app-layout>
