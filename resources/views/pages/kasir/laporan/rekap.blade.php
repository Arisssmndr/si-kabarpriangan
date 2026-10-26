<x-app-layout>
    <div class="space-y-6" x-data="{
        activeTab: 'rekap_dos',
        periodeTipe: '{{ $periodeTipe ?? 'bulan' }}'
    }">

        <!-- Header Laporan Rekapitulasi Kas (OripioFin Breadcrumbs & Style) -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden no-print">
            <div>
                <nav class="flex items-center gap-1.5 text-xs text-slate-400 font-medium mb-1">
                    <a href="{{ route('kasir.dashboard') }}" class="hover:text-slate-600 transition">OripioFin</a>
                    <span class="text-slate-300">&rsaquo;</span>
                    <span class="text-slate-400 font-medium">Laporan</span>
                    <span class="text-slate-300">&rsaquo;</span>
                    <span class="text-slate-800 font-semibold">Rekapitulasi Kas</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    Rekapitulasi Arus Kas
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 mt-0.5">
                    Rekapitulasi mutasi kas masuk (KI) dan kas keluar (KO) berdasarkan Bagan Akun Standar (COA)
                </p>
            </div>

            <!-- Export Buttons -->
            <div class="flex items-center gap-2 print:hidden no-print">
                <a href="{{ route('kasir.laporan.rekap.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                   class="px-3.5 py-2 bg-[#0B6B44] hover:bg-[#095737] text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Export CSV</span>
                </a>
                <button type="button" onclick="window.print()" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200/80 transition shadow-2xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Lembar Rekap</span>
                </button>
            </div>
        </div>

        <!-- Filter Form Periode (Hari, Bulan, Tahun, Rentang) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:hidden no-print">
            <form method="GET" action="{{ route('kasir.laporan.rekap') }}" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tipe Periode</label>
                        <select name="periode_tipe" x-model="periodeTipe" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-indigo-500 font-semibold text-slate-800">
                            <option value="bulan">Per Bulan</option>
                            <option value="hari">Per Hari (Tanggal Tertentu)</option>
                            <option value="tahun">Per Tahun</option>
                            <option value="rentang">Rentang Tanggal Bebas</option>
                        </select>
                    </div>

                    <!-- Input Dinamis Berdasarkan Tipe Periode -->
                    <template x-if="periodeTipe === 'hari'">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Tanggal</label>
                            <input type="date" name="tanggal" value="{{ $tanggalPilihan }}" 
                                   class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-indigo-500 text-slate-800 font-semibold">
                        </div>
                    </template>

                    <template x-if="periodeTipe === 'bulan'">
                        <div class="grid grid-cols-2 gap-2 sm:col-span-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Bulan</label>
                                <select name="bulan" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-indigo-500 font-semibold text-slate-800">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ ($bulanPilihan ?? Carbon\Carbon::now()->month) == $m ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Tahun</label>
                                <input type="number" name="tahun" value="{{ $tahunPilihan ?? Carbon\Carbon::now()->year }}" 
                                       class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-indigo-500 font-mono font-semibold text-slate-800">
                            </div>
                        </div>
                    </template>

                    <template x-if="periodeTipe === 'tahun'">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tahun Anggaran</label>
                            <input type="number" name="tahun" value="{{ $tahunPilihan ?? Carbon\Carbon::now()->year }}" 
                                   class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-indigo-500 font-mono font-semibold text-slate-800">
                        </div>
                    </template>

                    <template x-if="periodeTipe === 'rentang'">
                        <div class="grid grid-cols-2 gap-2 sm:col-span-2">
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
                        </div>
                    </template>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Filter Divisi</label>
                        <select name="kode_bagian" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-indigo-500 text-slate-800 font-semibold">
                            <option value="all" {{ ($bagianFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Divisi (Konsolidasi)</option>
                            <option value="01" {{ ($bagianFilter ?? '') === '01' ? 'selected' : '' }}>Divisi Iklan</option>
                            <option value="02" {{ ($bagianFilter ?? '') === '02' ? 'selected' : '' }}>Divisi Sirkulasi</option>
                            <option value="03" {{ ($bagianFilter ?? '') === '03' ? 'selected' : '' }}>Divisi Umum & SDM</option>
                            <option value="05" {{ ($bagianFilter ?? '') === '05' ? 'selected' : '' }}>Divisi Keuangan</option>
                            <option value="06" {{ ($bagianFilter ?? '') === '06' ? 'selected' : '' }}>Divisi Redaksi</option>
                            <option value="09" {{ ($bagianFilter ?? '') === '09' ? 'selected' : '' }}>Divisi Kasir (Bank)</option>
                            <option value="14" {{ ($bagianFilter ?? '') === '14' ? 'selected' : '' }}>Divisi Percetakan & Koran Sisa</option>
                        </select>
                    </div>

                    <div>
                        <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg transition shadow-2xs">
                            Tampilkan Laporan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Metric Cards Saldo Rekap -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 print:hidden no-print">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Kas Masuk (KI)</span>
                <div class="mt-2 text-xl font-black text-teal-700 font-mono">
                    Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">{{ $kasMasuks->count() }} transaksi penerimaan ({{ $labelPeriode }})</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Kas Keluar (KO)</span>
                <div class="mt-2 text-xl font-black text-rose-700 font-mono">
                    Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">{{ $kasKeluars->count() }} transaksi pengeluaran</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Saldo Surplus / (Defisit)</span>
                <div class="mt-2 text-xl font-black font-mono {{ $saldoSurplusDefisit >= 0 ? 'text-emerald-700' : 'text-amber-700' }}">
                    Rp {{ number_format($saldoSurplusDefisit, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">{{ $saldoSurplusDefisit >= 0 ? 'Surplus Mutasi Kas' : 'Defisit Pengeluaran Lebih Besar' }}</div>
            </div>
        </div>

        <!-- Tab Selector: Tampilan Dot-Matrix DOS vs Tabel Rincian -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2 print:hidden no-print">
            <button type="button" @click="activeTab = 'rekap_dos'" :class="activeTab === 'rekap_dos' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Format Rekap Kas DOS (KAS.EXE)</span>
            </button>
            <button type="button" @click="activeTab = 'rincian'" :class="activeTab === 'rincian' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                <span>Tabel Rincian Transaksi (KI & KO)</span>
            </button>
        </div>

        <!-- ======================================================== -->
        <!-- TAB 1: FORMAT ASLI REKAP KAS DOS (HU. PRIANGAN REKAP KAS) -->
        <!-- ======================================================== -->
        <div x-show="activeTab === 'rekap_dos'" class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xs font-mono text-xs">
            <div class="max-w-4xl mx-auto space-y-4">
                <!-- Header Dot-Matrix DOS -->
                <div class="flex justify-between items-start border-b border-dashed border-slate-400 pb-3">
                    <div>
                        <div class="font-bold text-sm text-slate-900 tracking-wider">HU. PRIANGAN</div>
                        <div class="font-bold text-xs text-slate-800">REKAP KAS</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-xs text-slate-900 uppercase">
                            BULAN : {{ strtoupper($labelPeriode) }}
                        </div>
                        <div class="text-[10px] text-slate-500">SIKAPRI - DIVISI KASIR</div>
                    </div>
                </div>

                <!-- Baris Akun Kas Induk -->
                <div class="border-b border-slate-900 py-2">
                    <div class="grid grid-cols-12 font-bold text-slate-600 text-[11px] pb-1 uppercase">
                        <span class="col-span-3">KODE</span>
                        <span class="col-span-6">NAMA PERKIRAAN</span>
                        <span class="col-span-3 text-right">DEBET (RP)</span>
                    </div>
                    <div class="grid grid-cols-12 py-1 font-bold text-slate-900">
                        <span class="col-span-3">1.00.00.01.01</span>
                        <span class="col-span-6">Kas BK. Priangan</span>
                        <span class="col-span-3 text-right font-mono">{{ number_format($totalMasuk, 2, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Bagian 1: PENERIMAAN-PENERIMAAN -->
                <div class="space-y-1 pt-1">
                    <div class="font-bold text-slate-800 tracking-wider text-[11px] uppercase pb-1">
                        PENERIMAAN - PENERIMAAN
                    </div>
                    @foreach($coaPenerimaan as $kode => $coa)
                    <div class="grid grid-cols-12 py-0.5 text-slate-700 hover:bg-slate-50">
                        <span class="col-span-3 font-semibold">{{ $kode }}</span>
                        <span class="col-span-6">{{ $coa['nama'] }}</span>
                        <span class="col-span-3 text-right font-mono">{{ number_format($coa['nominal'], 2, ',', '.') }}</span>
                    </div>
                    @endforeach
                    <div class="border-t border-slate-400 my-1"></div>
                    <div class="grid grid-cols-12 py-1 font-bold text-slate-900">
                        <span class="col-span-9 uppercase">Jumlah Penerimaan</span>
                        <span class="col-span-3 text-right font-mono">{{ number_format($totalMasuk, 2, ',', '.') }}</span>
                    </div>
                    <div class="grid grid-cols-12 py-1 font-bold text-indigo-900 bg-indigo-50/50 px-2 rounded">
                        <span class="col-span-9 uppercase">Jumlah Kas Tersedia</span>
                        <span class="col-span-3 text-right font-mono">{{ number_format($saldoKasTersedia, 2, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Bagian 2: PENGELUARAN-PENGELUARAN -->
                <div class="space-y-1 pt-4 border-t border-dashed border-slate-300">
                    <div class="font-bold text-slate-800 tracking-wider text-[11px] uppercase pb-1">
                        PENGELUARAN - PENGELUARAN
                    </div>
                    @foreach($coaPengeluaran as $kode => $coa)
                    <div class="grid grid-cols-12 py-0.5 text-slate-700 hover:bg-slate-50">
                        <span class="col-span-3 font-semibold">{{ $kode }}</span>
                        <span class="col-span-6">{{ $coa['nama'] }}</span>
                        <span class="col-span-3 text-right font-mono">{{ number_format($coa['nominal'], 2, ',', '.') }}</span>
                    </div>
                    @endforeach
                    <div class="border-t border-slate-400 my-1"></div>
                    <div class="grid grid-cols-12 py-1 font-bold text-slate-900">
                        <span class="col-span-9 uppercase">Jumlah Pengeluaran</span>
                        <span class="col-span-3 text-right font-mono">{{ number_format($totalKeluar, 2, ',', '.') }}</span>
                    </div>
                    <div class="grid grid-cols-12 py-1.5 font-black text-sm text-slate-900 border-t-2 border-b-2 border-slate-900 bg-slate-50 px-2">
                        <span class="col-span-9 uppercase">Saldo Akhir</span>
                        <span class="col-span-3 text-right font-mono">{{ number_format($saldoAkhirRekap, 2, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Tanda Tangan Resmi Sesuai Lembar Fisik -->
                <div class="pt-8 grid grid-cols-3 gap-4 text-center text-slate-800">
                    <div>
                        <div class="text-[11px] text-slate-500">Kasir Penerima,</div>
                        <div class="h-14"></div>
                        <div class="font-bold underline text-slate-900">{{ Auth::user()->name ?? 'Ulfa Nirmala' }}</div>
                        <div class="text-[10px] text-slate-500">Kasir</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-500">Mengetahui,</div>
                        <div class="h-14"></div>
                        <div class="font-bold underline text-slate-900">Eti Yulianti</div>
                        <div class="text-[10px] text-slate-500">Asisten Manajer Keu</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-500">Menyetujui,</div>
                        <div class="h-14"></div>
                        <div class="font-bold underline text-slate-900">Moch. Al Nurdin</div>
                        <div class="text-[10px] text-slate-500">Pemimpin Perusahaan</div>
                    </div>
                </div>

                <div class="text-[10px] text-slate-400 text-right pt-2 border-t border-slate-200">
                    Tasikmalaya, {{ Carbon\Carbon::now()->translatedFormat('d-m-Y H:i:s') }}
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- TAB 2: TABEL RINCIAN TRANSAKSI KAS (KI & KO) -->
        <!-- ======================================================== -->
        <div x-show="activeTab === 'rincian'" class="space-y-6">
            <!-- Tabel Kas Masuk (KI) -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-teal-50/40">
                    <h3 class="text-xs font-bold text-teal-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-teal-600"></span>
                        Rincian Kas Masuk (KI - {{ $kasMasuks->count() }} Transaksi)
                    </h3>
                    <span class="text-xs font-black text-teal-800 font-mono">Total: Rp {{ number_format($totalMasuk, 0, ',', '.') }}</span>
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
                                <th class="px-4 py-3 font-semibold text-center">Metode</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($kasMasuks as $ki)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono font-bold text-teal-900">
                                    {{ $ki->no_kuitansi ?? $ki->no_bkm }}
                                </td>
                                <td class="px-4 py-3 text-slate-500">{{ $ki->tanggal_terima ? \Carbon\Carbon::parse($ki->tanggal_terima)->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 font-bold text-[10px] text-slate-700">{{ $ki->nama_divisi }}</span>
                                    <div class="font-mono text-[10px] text-slate-500">{{ $ki->kode_perkiraan }}</div>
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-800">{{ $ki->terima_dari ?? $ki->nama_pembayar }}</td>
                                <td class="px-4 py-3 text-right font-mono font-extrabold text-teal-800">
                                    Rp {{ number_format($ki->jumlah_diterima ?? $ki->jumlah_dibayar, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                        {{ $ki->metode_bayar ?? 'tunai' }}
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
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-rose-50/40">
                    <h3 class="text-xs font-bold text-rose-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        Rincian Kas Keluar (KO - {{ $kasKeluars->count() }} Transaksi)
                    </h3>
                    <span class="text-xs font-black text-rose-800 font-mono">Total: Rp {{ number_format($totalKeluar, 0, ',', '.') }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 font-semibold">No. BKK (KO)</th>
                                <th class="px-4 py-3 font-semibold">Tanggal</th>
                                <th class="px-4 py-3 font-semibold">No. Register BP</th>
                                <th class="px-4 py-3 font-semibold">Divisi & COA</th>
                                <th class="px-4 py-3 font-semibold">Diberikan Kepada / Uraian</th>
                                <th class="px-4 py-3 font-semibold text-right">Jumlah (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($kasKeluars as $ko)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono font-bold text-rose-900">
                                    {{ $ko->no_bkk }}
                                    @if($ko->is_kasbon)
                                        <span class="px-1 py-0.2 rounded text-[9px] bg-amber-100 text-amber-800 font-bold block w-fit">Kasbon Meja</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-500">{{ $ko->tanggal_keluar ? \Carbon\Carbon::parse($ko->tanggal_keluar)->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-3 font-mono text-[10px] text-amber-800 font-bold">
                                    {{ $ko->no_register_anggaran ?? $ko->no_bp_ref ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 font-bold text-[10px] text-slate-700">{{ $ko->nama_divisi }}</span>
                                    <div class="font-mono text-[10px] text-slate-500">{{ $ko->kode_perkiraan }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-800">{{ $ko->diberikan_kepada ?? $ko->nama_penerima }}</div>
                                    <div class="text-[10px] text-slate-500 truncate max-w-xs">{{ $ko->keperluan ?? $ko->keterangan }}</div>
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-extrabold text-rose-800">
                                    Rp {{ number_format($ko->nominal_keluar ?? $ko->jumlah_keluar, 0, ',', '.') }}
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

    </div>
</x-app-layout>
