<x-app-layout>
    @php
        $unitPelaksana = Auth::user()?->kasirReportUnitLabel() ?? 'Divisi Kasir';
        $isKasirUser = Auth::user()?->hasRole('kasir') ?? false;
    @endphp
    <div class="space-y-6" x-data="{
        activeTab: 'rincian',
        periodeTipe: '{{ $periodeTipe ?? 'bulan' }}'
    }">

        <!-- 1. Header Standar Divisi Iklan (Hidden on Print) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 print:hidden no-print">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Logo Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                            Rekapitulasi Kas (Sistem KAS)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-sky-50 text-[#0E527B] border border-sky-200">
                            Rekap Kas
                        </span>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $unitPelaksana }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Rekapitulasi mutasi kas masuk (KI) dan kas keluar (KO) berdasarkan klasifikasi Bagan Akun Standar (COA) operasional kasir.
                    </p>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="flex items-center gap-2 print:hidden no-print shrink-0">
                <a href="{{ route('kasir.laporan.rekap.export', [
                    'periode_tipe' => $periodeTipe ?? 'bulan',
                    'tanggal' => $tanggalPilihan ?? date('Y-m-d'),
                    'bulan' => $bulanPilihan ?? date('m'),
                    'tahun' => $tahunPilihan ?? date('Y'),
                    'bulan_awal' => $bulanAwal ?? 1,
                    'bulan_akhir' => $bulanAkhir ?? 12,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'kode_bagian' => $bagianFilter ?? 'all',
                ]) }}"
                   class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs rounded-xl transition shadow-xs flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Export Excel (.xls)</span>
                </a>
                <button type="button" onclick="window.print()" class="h-10 px-4 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl border border-slate-300 transition shadow-xs flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Lembar Rekap</span>
                </button>
            </div>
        </div>

        <!-- 2. Filter Bar Terpisah (Periode: Hari, Bulan, Tahun, Rentang) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:hidden no-print">
            <form method="GET" action="{{ route('kasir.laporan.rekap') }}" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Tipe Periode</label>
                        <select name="periode_tipe" x-model="periodeTipe" class="h-11 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600">
                            <option value="bulan">Per Bulan</option>
                            <option value="hari">Per Hari (Tanggal Tertentu)</option>
                            <option value="tahun">Per Tahun</option>
                            <option value="rentang">Rentang Tanggal Bebas</option>
                        </select>
                    </div>

                    <!-- Input Dinamis Berdasarkan Tipe Periode -->
                    <template x-if="periodeTipe === 'hari'">
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Pilih Tanggal</label>
                            <input type="date" name="tanggal" value="{{ $tanggalPilihan }}" 
                                   class="h-11 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600">
                        </div>
                    </template>

                    <template x-if="periodeTipe === 'bulan'">
                        <div class="grid grid-cols-2 gap-2 sm:col-span-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Bulan</label>
                                <select name="bulan" class="h-11 w-full px-3 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ ($bulanPilihan ?? Carbon\Carbon::now()->month) == $m ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Tahun</label>
                                <input type="number" name="tahun" value="{{ $tahunPilihan ?? Carbon\Carbon::now()->year }}" 
                                       class="h-11 w-full px-3.5 bg-white border border-slate-300 rounded-xl font-mono text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600">
                            </div>
                        </div>
                    </template>

                    <template x-if="periodeTipe === 'tahun'">
                        <div class="grid grid-cols-3 gap-2 sm:col-span-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Tahun</label>
                                <input type="number" name="tahun" value="{{ $tahunPilihan ?? Carbon\Carbon::now()->year }}" 
                                       class="h-11 w-full px-2.5 bg-white border border-slate-300 rounded-xl font-mono text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Dari Bulan</label>
                                <select name="bulan_awal" class="h-11 w-full px-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ ($bulanAwal ?? 1) == $m ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::create(2026, $m, 1)->translatedFormat('M') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Sampai</label>
                                <select name="bulan_akhir" class="h-11 w-full px-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ ($bulanAkhir ?? 12) == $m ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::create(2026, $m, 1)->translatedFormat('M') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </template>

                    <template x-if="periodeTipe === 'rentang'">
                        <div class="grid grid-cols-2 gap-2 sm:col-span-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Tanggal Mulai</label>
                                <input type="date" name="start_date" value="{{ $startDate }}" 
                                       class="h-11 w-full px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Tanggal Sampai</label>
                                <input type="date" name="end_date" value="{{ $endDate }}" 
                                       class="h-11 w-full px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600">
                            </div>
                        </div>
                    </template>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Divisi / Bidang</label>
                        <select name="kode_bagian" class="h-11 w-full px-3 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600">
                            <option value="all" {{ ($bagianFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Divisi</option>
                            <option value="01" {{ ($bagianFilter ?? '') === '01' ? 'selected' : '' }}>Divisi Iklan</option>
                            <option value="02" {{ ($bagianFilter ?? '') === '02' ? 'selected' : '' }}>Divisi Sirkulasi</option>
                            <option value="03" {{ ($bagianFilter ?? '') === '03' ? 'selected' : '' }}>Divisi Umum & SDM</option>
                            <option value="05" {{ ($bagianFilter ?? '') === '05' ? 'selected' : '' }}>Bidang Keuangan</option>
                            <option value="06" {{ ($bagianFilter ?? '') === '06' ? 'selected' : '' }}>Divisi Redaksi</option>
                            <option value="09" {{ ($bagianFilter ?? '') === '09' ? 'selected' : '' }}>Bidang Keuangan (Bank KI-09/KO-09)</option>
                            <option value="14" {{ ($bagianFilter ?? '') === '14' ? 'selected' : '' }}>Penerimaan Tunai (KI-14)</option>
                        </select>
                    </div>

                    <div>
                        <button type="submit" class="h-11 w-full bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-xs cursor-pointer">
                            Tampilkan Laporan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- 3. KPI Cards Standar Divisi Iklan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:hidden no-print">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kas Masuk (KI)</span>
                <p class="mt-2 text-2xl font-bold font-mono text-slate-900">
                    Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                </p>
                <span class="mt-1 text-[11px] text-slate-400 block">{{ $kasMasuks->count() }} transaksi penerimaan ({{ $labelPeriode }})</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kas Keluar (KO)</span>
                <p class="mt-2 text-2xl font-bold font-mono text-slate-900">
                    Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                </p>
                <span class="mt-1 text-[11px] text-slate-400 block">{{ $kasKeluars->count() }} transaksi pengeluaran</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Aplikasi (KI - KO)</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-sky-50 text-[#0E527B] font-bold border border-sky-200">Buku Kas</span>
                </div>
                <p class="mt-2 text-2xl font-bold font-mono text-kp-blue-700">
                    Rp {{ number_format($saldoAkhirRekap, 0, ',', '.') }}
                </p>
                <span class="mt-1 text-[11px] text-slate-400 block">Saldo pembukuan aplikasi</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Posisi Kas Tunai Fisik</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold border border-slate-200">Laci Fisik</span>
                </div>
                <p class="mt-2 text-2xl font-bold font-mono text-slate-900">
                    Rp {{ number_format(is_array($posisiKasFisikHarian ?? null) ? ($posisiKasFisikHarian['saldo_fisik_laci'] ?? $saldoAkhirRekap) : ($posisiKasFisikHarian ?? $saldoAkhirRekap), 0, ',', '.') }}
                </p>
                <span class="mt-1 text-[11px] text-slate-400 block">
                    Estimasi kas tunai fisik di laci
                </span>
            </div>
        </div>

        @if(!empty($komparasiTahunan))
        <!-- Panel Komparasi Antar-Tahun -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs print:hidden no-print">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                    Komparasi Penerimaan Antar-Tahun (Rentang Bulan Terpilih)
                </h3>
                <span class="px-2.5 py-0.5 rounded-md bg-sky-50 text-[#0E527B] font-mono font-bold text-xs border border-sky-200">
                    YoY
                </span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3 text-xs">
                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 flex items-center justify-between">
                    <div>
                        <span class="text-slate-500 font-semibold block">Divisi Iklan (KI-01)</span>
                        <span class="text-sm font-bold font-mono text-slate-900">Tahun {{ $komparasiTahunan['tahun_berjalan'] ?? $komparasiTahunan['tahun_ini'] ?? '' }}: Rp {{ number_format($komparasiTahunan['iklan_tahun_ini'], 0, ',', '.') }}</span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">Tahun {{ $komparasiTahunan['tahun_lalu'] }}: Rp {{ number_format($komparasiTahunan['iklan_tahun_lalu'], 0, ',', '.') }}</span>
                    </div>
                    <span class="px-2.5 py-1 rounded-md font-mono font-semibold text-xs {{ $komparasiTahunan['iklan_tahun_ini'] >= $komparasiTahunan['iklan_tahun_lalu'] ? 'bg-sky-50 text-[#0E527B] border border-sky-200' : 'bg-slate-100 text-slate-700 border border-slate-300' }}">
                        {{ $komparasiTahunan['iklan_tahun_ini'] >= $komparasiTahunan['iklan_tahun_lalu'] ? '▲ Naik/Stabil' : '▼ Turun' }}
                    </span>
                </div>
                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 flex items-center justify-between">
                    <div>
                        <span class="text-slate-500 font-semibold block">Divisi Sirkulasi (KI-02)</span>
                        <span class="text-sm font-bold font-mono text-slate-900">Tahun {{ $komparasiTahunan['tahun_berjalan'] ?? $komparasiTahunan['tahun_ini'] ?? '' }}: Rp {{ number_format($komparasiTahunan['sirkulasi_tahun_ini'], 0, ',', '.') }}</span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">Tahun {{ $komparasiTahunan['tahun_lalu'] }}: Rp {{ number_format($komparasiTahunan['sirkulasi_tahun_lalu'], 0, ',', '.') }}</span>
                    </div>
                    <span class="px-2.5 py-1 rounded-md font-mono font-semibold text-xs {{ $komparasiTahunan['sirkulasi_tahun_ini'] >= $komparasiTahunan['sirkulasi_tahun_lalu'] ? 'bg-sky-50 text-[#0E527B] border border-sky-200' : 'bg-slate-100 text-slate-700 border border-slate-300' }}">
                        {{ $komparasiTahunan['sirkulasi_tahun_ini'] >= $komparasiTahunan['sirkulasi_tahun_lalu'] ? '▲ Naik/Stabil' : '▼ Turun' }}
                    </span>
                </div>
            </div>
        </div>
        @endif

        <!-- 4. Tab Selector: Tabel Rincian vs DOS -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2 print:hidden no-print">
            <button type="button" @click="activeTab = 'rincian'" 
                    :class="activeTab === 'rincian' ? 'bg-kp-blue-600 text-white font-semibold' : 'bg-white text-slate-700 hover:bg-slate-100 font-medium border border-slate-200'" 
                    class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2 cursor-pointer shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                <span>Tabel Rincian Transaksi (KI & KO)</span>
            </button>
            <button type="button" @click="activeTab = 'rekap_dos'" 
                    :class="activeTab === 'rekap_dos' ? 'bg-kp-blue-600 text-white font-semibold' : 'bg-white text-slate-700 hover:bg-slate-100 font-medium border border-slate-200'" 
                    class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2 cursor-pointer shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Format Rekap Kas DOS (KAS.EXE)</span>
            </button>
        </div>

        <!-- TAB 1: FORMAT ASLI REKAP KAS DOS -->
        <div x-show="activeTab === 'rekap_dos'" class="bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-xs font-mono text-xs">
            <div class="max-w-4xl mx-auto space-y-4">
                <div class="flex justify-between items-start border-b border-dashed border-slate-400 pb-3">
                    <div>
                        <div class="font-bold text-sm text-slate-900 tracking-wider">HU KABAR PRIANGAN</div>
                        <div class="font-bold text-xs text-slate-800">REKAP KAS</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-xs text-slate-900 uppercase">
                            {{ strtoupper($labelPeriode) }}
                        </div>
                        <div class="text-[10px] text-slate-500">SIKAPRI - {{ strtoupper($unitPelaksana) }}</div>
                    </div>
                </div>

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

                <div class="space-y-1 pt-1">
                    <div class="font-bold text-slate-800 tracking-wider text-[11px] uppercase pb-1">
                        PENERIMAAN - PENERIMAAN
                    </div>
                    @foreach($coaPenerimaan as $kode => $coa)
                    <div class="grid grid-cols-12 py-0.5 text-slate-700 hover:bg-slate-50">
                        <span class="col-span-3 font-bold">{{ $kode }}</span>
                        <span class="col-span-6">{{ $coa['nama'] }}</span>
                        <span class="col-span-3 text-right font-mono">{{ number_format($coa['nominal'], 2, ',', '.') }}</span>
                    </div>
                    @endforeach
                    <div class="border-t border-slate-400 my-1"></div>
                    <div class="grid grid-cols-12 py-1 font-bold text-slate-900">
                        <span class="col-span-9 uppercase">Jumlah Penerimaan</span>
                        <span class="col-span-3 text-right font-mono">{{ number_format($totalMasuk, 2, ',', '.') }}</span>
                    </div>
                    <div class="grid grid-cols-12 py-1 font-bold text-slate-900 bg-slate-50 px-2 rounded">
                        <span class="col-span-9 uppercase">Jumlah Kas Tersedia</span>
                        <span class="col-span-3 text-right font-mono">{{ number_format($saldoKasTersedia, 2, ',', '.') }}</span>
                    </div>
                </div>

                <div class="space-y-1 pt-4 border-t border-dashed border-slate-300">
                    <div class="font-bold text-slate-800 tracking-wider text-[11px] uppercase pb-1">
                        PENGELUARAN - PENGELUARAN
                    </div>
                    @foreach($coaPengeluaran as $kode => $coa)
                    <div class="grid grid-cols-12 py-0.5 text-slate-700 hover:bg-slate-50">
                        <span class="col-span-3 font-bold">{{ $kode }}</span>
                        <span class="col-span-6">{{ $coa['nama'] }}</span>
                        <span class="col-span-3 text-right font-mono">{{ number_format($coa['nominal'], 2, ',', '.') }}</span>
                    </div>
                    @endforeach
                    <div class="border-t border-slate-400 my-1"></div>
                    <div class="grid grid-cols-12 py-1 font-bold text-slate-900">
                        <span class="col-span-9 uppercase">Jumlah Pengeluaran</span>
                        <span class="col-span-3 text-right font-mono">{{ number_format($totalKeluar, 2, ',', '.') }}</span>
                    </div>
                    <div class="grid grid-cols-12 py-1.5 font-bold text-sm text-slate-900 border-t-2 border-b-2 border-slate-900 bg-slate-50 px-2">
                        <span class="col-span-9 uppercase">Saldo Akhir</span>
                        <span class="col-span-3 text-right font-mono">{{ number_format($saldoAkhirRekap, 2, ',', '.') }}</span>
                    </div>
                </div>

                <div class="pt-8 grid grid-cols-3 gap-4 text-center text-slate-800">
                    <div>
                        <div class="text-[11px] text-slate-500">{{ $isKasirUser ? 'Kasir Penerima,' : 'Manager Keuangan,' }}</div>
                        <div class="h-14"></div>
                        <div class="font-bold underline text-slate-900">{{ Auth::user()->name ?? $unitPelaksana }}</div>
                        <div class="text-[10px] text-slate-500">{{ $unitPelaksana }}</div>
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
            </div>
        </div>

        <!-- TAB 2: TABEL RINCIAN TRANSAKSI KAS (KI & KO) -->
        <div x-show="activeTab === 'rincian'" class="space-y-6">
            <!-- Kop Cetak -->
            <div class="hidden print:block pb-3 mb-3 border-b-2 border-slate-900">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('kabarpriangan.png') }}" alt="Logo Kabar Priangan" class="h-10 w-auto object-contain">
                        <div>
                            <h1 class="text-sm font-black uppercase text-slate-900">HU KABAR PRIANGAN</h1>
                            <h2 class="text-xs font-bold text-slate-800 uppercase">{{ strtoupper($unitPelaksana) }} &mdash; LAPORAN RINCIAN TRANSAKSI KAS</h2>
                            <p class="text-[10px] text-slate-500">{{ $labelPeriode }}</p>
                        </div>
                    </div>
                    <div class="text-right text-[10px] text-slate-600 font-mono">
                        <div>Dicetak: {{ Carbon\Carbon::now()->translatedFormat('d-m-Y H:i:s') }}</div>
                        <div>{{ $unitPelaksana }}: {{ Auth::user()->name ?? $unitPelaksana }}</div>
                    </div>
                </div>
            </div>

            <!-- Pure Data Table: Kas Masuk (KI) -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-4">No. Kuitansi</th>
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">Divisi & COA</th>
                                <th class="py-3 px-4">Terima Dari / Pemasang</th>
                                <th class="py-3 px-4 text-right">Jumlah (Rp)</th>
                                <th class="py-3 px-4 text-center">Metode</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800">
                            @forelse($kasMasuks as $ki)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                                    {{ $ki->no_kuitansi ?? $ki->no_bkm }}
                                </td>
                                <td class="py-3 px-4 text-slate-600 font-mono text-xs whitespace-nowrap">
                                    {{ $ki->tanggal_terima ? \Carbon\Carbon::parse($ki->tanggal_terima)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 font-semibold text-xs text-slate-700">{{ $ki->nama_divisi }}</span>
                                    <div class="font-mono text-xs text-slate-400">{{ $ki->kode_perkiraan }}</div>
                                </td>
                                <td class="py-3 px-4 font-semibold text-slate-900">{{ $ki->terima_dari ?? $ki->nama_pembayar }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                    Rp {{ number_format($ki->jumlah_diterima ?? $ki->jumlah_dibayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $ki->metode_bayar ?? 'tunai' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-slate-400">Tidak ada transaksi kas masuk pada periode ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pure Data Table: Kas Keluar (KO) -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-4">No. BKK (KO)</th>
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">No. Register BP</th>
                                <th class="py-3 px-4">Divisi & COA</th>
                                <th class="py-3 px-4">Diberikan Kepada / Uraian</th>
                                <th class="py-3 px-4 text-right">Jumlah (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800">
                            @forelse($kasKeluars as $ko)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                                    {{ $ko->no_bkk }}
                                </td>
                                <td class="py-3 px-4 text-slate-600 font-mono text-xs whitespace-nowrap">
                                    {{ $ko->tanggal_keluar ? \Carbon\Carbon::parse($ko->tanggal_keluar)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-3 px-4 font-mono text-xs text-slate-700 font-semibold">
                                    {{ $ko->no_register_anggaran ?? $ko->no_bp_ref ?? '-' }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 font-semibold text-xs text-slate-700">{{ $ko->nama_divisi }}</span>
                                    <div class="font-mono text-xs text-slate-400">{{ $ko->kode_perkiraan }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-slate-900">{{ $ko->diberikan_kepada ?? $ko->nama_penerima }}</div>
                                    <div class="text-xs text-slate-500 truncate max-w-xs">{{ $ko->keperluan ?? $ko->keterangan }}</div>
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                    Rp {{ number_format($ko->nominal_keluar ?? $ko->jumlah_keluar, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-slate-400">Tidak ada transaksi kas keluar pada periode ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Aging Piutang Tertagih -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto print:overflow-visible">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-4">Bulan & Tahun Faktur</th>
                                <th class="py-3 px-4 text-center">Jml KI</th>
                                <th class="py-3 px-4 text-right">Iklan (KI-01)</th>
                                <th class="py-3 px-4 text-right">Sirkulasi (KI-02)</th>
                                <th class="py-3 px-4 text-right">Total Tertagih (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800">
                            @forelse($rekapAsalFaktur ?? [] as $aging)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $aging['label'] ?? $aging['label_periode_asal'] ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-center font-mono text-slate-700 whitespace-nowrap">
                                    {{ $aging['jumlah_kuitansi'] }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-slate-900 whitespace-nowrap">
                                    Rp {{ number_format($aging['nominal_iklan'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-slate-900 whitespace-nowrap">
                                    Rp {{ number_format($aging['nominal_sirkulasi'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-kp-blue-700 whitespace-nowrap">
                                    Rp {{ number_format($aging['total_nominal'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-400">Belum ada data kuitansi pelunasan faktur pada periode ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Rangkuman Subtotal & Balance Kas / Bank -->
            @php
                $totalSetorBankKo = (float) $kasKeluars->where('kategori_beban', 'setor_bank')->sum(fn($x) => $x->nominal_keluar ?? $x->jumlah_keluar ?? 0);
                $saldoFisikBersih = $totalMasuk - $totalKeluar;
                $saldoKasDanBankBalance = $saldoFisikBersih + $totalSetorBankKo;
            @endphp
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-slate-200 text-sm">
                    <!-- Subtotal Penerimaan (KI) -->
                    <div class="p-5 space-y-2">
                        <span class="font-bold text-slate-900 uppercase tracking-wider text-xs pb-2 border-b border-slate-100 block">
                            A. Subtotal Penerimaan per Divisi (KI)
                        </span>
                        @foreach(($rekapKiPerDivisi ?? []) as $labelDiv => $nomKi)
                        <div class="flex items-center justify-between py-1 border-b border-slate-50 text-xs">
                            <span class="text-slate-600 font-medium">{{ $labelDiv }}</span>
                            <span class="font-mono font-bold text-slate-900">Rp {{ number_format($nomKi, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                        <div class="flex items-center justify-between pt-3 font-bold text-slate-900 bg-slate-50 px-3 py-2 rounded-xl text-xs">
                            <span>TOTAL PENERIMAAN (KI)</span>
                            <span class="font-mono text-kp-blue-700 font-bold">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Subtotal Pengeluaran (KO) -->
                    <div class="p-5 space-y-2">
                        <span class="font-bold text-slate-900 uppercase tracking-wider text-xs pb-2 border-b border-slate-100 block">
                            B. Subtotal Pengeluaran per Divisi (KO)
                        </span>
                        @foreach(($rekapKoPerDivisi ?? []) as $labelDiv => $nomKo)
                        <div class="flex items-center justify-between py-1 border-b border-slate-50 text-xs">
                            <span class="text-slate-600 font-medium">{{ $labelDiv }}</span>
                            <span class="font-mono font-bold text-slate-900">Rp {{ number_format($nomKo, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                        <div class="flex items-center justify-between pt-3 font-bold text-slate-900 bg-slate-50 px-3 py-2 rounded-xl text-xs">
                            <span>TOTAL PENGELUARAN (KO)</span>
                            <span class="font-mono text-slate-900 font-bold">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Baris Rekonsiliasi Saldo Kas & Bank Seimbang -->
                <div class="bg-slate-50 px-5 py-4 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div class="p-3 bg-white rounded-xl border border-slate-200 flex items-center justify-between">
                        <span class="text-slate-600 font-semibold">Saldo Kas Fisik:</span>
                        <span class="font-mono font-bold text-slate-900">Rp {{ number_format($saldoFisikBersih, 0, ',', '.') }}</span>
                    </div>
                    <div class="p-3 bg-white rounded-xl border border-slate-200 flex items-center justify-between">
                        <span class="text-slate-600 font-semibold">Mutasi Setor ke Bank:</span>
                        <span class="font-mono font-bold text-slate-900">Rp {{ number_format($totalSetorBankKo, 0, ',', '.') }}</span>
                    </div>
                    <div class="p-3 bg-sky-50 rounded-xl border border-sky-200 flex items-center justify-between">
                        <span class="text-[#0E527B] font-bold">SALDO KAS & BANK (BALANCE):</span>
                        <span class="font-mono font-bold text-kp-blue-700">Rp {{ number_format($saldoKasDanBankBalance, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Tanda Tangan Cetak -->
            <div class="hidden print:grid grid-cols-3 pt-6 text-center text-xs text-slate-800">
                <div>
                    <div class="text-[11px] text-slate-500">{{ $isKasirUser ? 'Kasir Pelaksana,' : 'Manager Keuangan,' }}</div>
                    <div class="h-14"></div>
                    <div class="font-bold underline text-slate-900">{{ Auth::user()->name ?? $unitPelaksana }}</div>
                    <div class="text-[10px] text-slate-500">{{ $unitPelaksana }}</div>
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
        </div>

    </div>
</x-app-layout>