<x-app-layout>
    <div class="space-y-5" x-data="{
        activeTab: 'rincian',
        periodeTipe: '{{ $periodeTipe ?? 'bulan' }}'
    }">

        <!-- Header Halaman (Hidden on Print) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 print:hidden no-print">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Rekapitulasi Kas (KI & KO)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Rekapitulasi kas masuk (KI) dan kas keluar (KO) per klasifikasi Bagan Akun Standar ({{ $labelPeriode }}).
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap print:hidden no-print">
                <a href="{{ route('kasir.laporan.rekap.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Export CSV</span>
                </a>
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Lembar Rekap</span>
                </button>
            </div>
        </div>

        <!-- Filter Form Periode -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:hidden no-print">
            <form method="GET" action="{{ route('kasir.laporan.rekap') }}">
                <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tipe Periode</label>
                        <select name="periode_tipe" x-model="periodeTipe" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            <option value="bulan">Per Bulan</option>
                            <option value="hari">Per Hari (Tanggal Tertentu)</option>
                            <option value="tahun">Per Tahun</option>
                            <option value="rentang">Rentang Tanggal Bebas</option>
                        </select>
                    </div>

                    <template x-if="periodeTipe === 'hari'">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pilih Tanggal</label>
                            <input type="date" name="tanggal" value="{{ $tanggalPilihan }}" 
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </template>

                    <template x-if="periodeTipe === 'bulan'">
                        <div class="grid grid-cols-2 gap-2.5 sm:col-span-2">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bulan</label>
                                <select name="bulan" class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ ($bulanPilihan ?? Carbon\Carbon::now()->month) == $m ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tahun</label>
                                <input type="number" name="tahun" value="{{ $tahunPilihan ?? Carbon\Carbon::now()->year }}" 
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            </div>
                        </div>
                    </template>

                    <template x-if="periodeTipe === 'tahun'">
                        <div class="grid grid-cols-3 gap-2 sm:col-span-2">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tahun</label>
                                <input type="number" name="tahun" value="{{ $tahunPilihan ?? Carbon\Carbon::now()->year }}" 
                                       class="w-full h-11 px-2.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Dari</label>
                                <select name="bulan_awal" class="w-full h-11 px-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ ($bulanAwal ?? 1) == $m ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::create(2026, $m, 1)->translatedFormat('M') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sampai</label>
                                <select name="bulan_akhir" class="w-full h-11 px-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600">
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
                        <div class="grid grid-cols-2 gap-2.5 sm:col-span-2">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Mulai</label>
                                <input type="date" name="start_date" value="{{ $startDate }}" 
                                       class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sampai</label>
                                <input type="date" name="end_date" value="{{ $endDate }}" 
                                       class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600">
                            </div>
                        </div>
                    </template>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Divisi</label>
                        <select name="kode_bagian" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            <option value="all" {{ ($bagianFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Divisi</option>
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
                        <button type="submit" class="w-full h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer">
                            Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- 4 Kartu KPI -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:hidden no-print">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kas Masuk (KI)</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">{{ $kasMasuks->count() }} transaksi penerimaan</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kas Keluar (KO)</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">{{ $kasKeluars->count() }} transaksi pengeluaran</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Aplikasi (KI - KO)</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($saldoAkhirRekap, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Saldo pembukuan aplikasi</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Posisi Kas Tunai Fisik</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format(is_array($posisiKasFisikHarian ?? null) ? ($posisiKasFisikHarian['saldo_fisik_laci'] ?? $saldoAkhirRekap) : ($posisiKasFisikHarian ?? $saldoAkhirRekap), 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block font-mono">
                    Kasbon BS: Rp {{ number_format(is_array($posisiKasFisikHarian ?? null) ? ($posisiKasFisikHarian['kasbon_bs_gantung'] ?? 0) : ($kasbonGantungAktif ?? 0), 0, ',', '.') }}
                </span>
            </div>
        </div>

        @if(!empty($komparasiTahunan))
        <!-- Panel Komparasi Antar-Tahun -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden print:hidden no-print">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Komparasi Penerimaan Antar-Tahun</h2>
                    <p class="text-xs text-slate-500">Realisasi KI-01 & KI-02 Tahun {{ $komparasiTahunan['tahun_berjalan'] ?? $komparasiTahunan['tahun_ini'] ?? '' }} vs {{ $komparasiTahunan['tahun_lalu'] }}</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    YoY
                </span>
            </div>
            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 flex items-center justify-between gap-3">
                    <div>
                        <span class="text-slate-500 font-semibold block">Divisi Iklan (KI-01)</span>
                        <span class="text-sm font-bold font-mono text-slate-900 block mt-0.5">Tahun {{ $komparasiTahunan['tahun_berjalan'] ?? $komparasiTahunan['tahun_ini'] ?? '' }}: Rp {{ number_format($komparasiTahunan['iklan_tahun_ini'], 0, ',', '.') }}</span>
                        <span class="text-xs text-slate-500 font-mono block mt-0.5">Tahun {{ $komparasiTahunan['tahun_lalu'] }}: Rp {{ number_format($komparasiTahunan['iklan_tahun_lalu'], 0, ',', '.') }}</span>
                    </div>
                    <span class="px-2.5 py-1 rounded-md font-mono font-medium text-xs border {{ $komparasiTahunan['iklan_tahun_ini'] >= $komparasiTahunan['iklan_tahun_lalu'] ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                        {{ $komparasiTahunan['iklan_tahun_ini'] >= $komparasiTahunan['iklan_tahun_lalu'] ? 'Naik / Stabil' : 'Turun' }}
                    </span>
                </div>
                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 flex items-center justify-between gap-3">
                    <div>
                        <span class="text-slate-500 font-semibold block">Divisi Sirkulasi (KI-02)</span>
                        <span class="text-sm font-bold font-mono text-slate-900 block mt-0.5">Tahun {{ $komparasiTahunan['tahun_berjalan'] ?? $komparasiTahunan['tahun_ini'] ?? '' }}: Rp {{ number_format($komparasiTahunan['sirkulasi_tahun_ini'], 0, ',', '.') }}</span>
                        <span class="text-xs text-slate-500 font-mono block mt-0.5">Tahun {{ $komparasiTahunan['tahun_lalu'] }}: Rp {{ number_format($komparasiTahunan['sirkulasi_tahun_lalu'], 0, ',', '.') }}</span>
                    </div>
                    <span class="px-2.5 py-1 rounded-md font-mono font-medium text-xs border {{ $komparasiTahunan['sirkulasi_tahun_ini'] >= $komparasiTahunan['sirkulasi_tahun_lalu'] ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                        {{ $komparasiTahunan['sirkulasi_tahun_ini'] >= $komparasiTahunan['sirkulasi_tahun_lalu'] ? 'Naik / Stabil' : 'Turun' }}
                    </span>
                </div>
            </div>
        </div>
        @endif

        <!-- Tab Selector -->
        <div class="bg-white border border-slate-200 rounded-xl p-1.5 shadow-xs inline-flex flex-wrap items-center gap-1.5 print:hidden no-print">
            <button type="button" @click="activeTab = 'rincian'"
                    :class="activeTab === 'rincian' ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-semibold'"
                    class="px-4 py-2 rounded-lg text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer">
                <span>Tabel Rincian Transaksi (KI & KO)</span>
            </button>
            <button type="button" @click="activeTab = 'rekap_dos'"
                    :class="activeTab === 'rekap_dos' ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-semibold'"
                    class="px-4 py-2 rounded-lg text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer">
                <span>Format Rekap Kas (HU. Priangan)</span>
            </button>
        </div>

        <!-- TAB 1: FORMAT REKAP KAS -->
        <div x-show="activeTab === 'rekap_dos'" class="report-print-container bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-xs font-mono text-xs">
            <div class="max-w-4xl mx-auto space-y-4">
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

                <div class="border-b border-slate-900 py-2">
                    <div class="grid grid-cols-12 font-bold text-slate-600 text-[11px] pb-1 uppercase">
                        <span class="col-span-3">KODE</span>
                        <span class="col-span-6">NAMA PERKIRAAN</span>
                        <span class="col-span-3 text-right">DEBET (RP)</span>
                    </div>
                    <div class="grid grid-cols-12 py-1 font-bold text-slate-900">
                        <span class="col-span-3 text-kp-blue-700">1.00.00.01.01</span>
                        <span class="col-span-6">Kas BK. Priangan</span>
                        <span class="col-span-3 text-right font-mono tabular-nums">{{ number_format($totalMasuk, 2, ',', '.') }}</span>
                    </div>
                </div>

                <div class="space-y-1 pt-1">
                    <div class="font-bold text-slate-800 tracking-wider text-[11px] uppercase pb-1">
                        PENERIMAAN - PENERIMAAN
                    </div>
                    @foreach($coaPenerimaan as $kode => $coa)
                    <div class="grid grid-cols-12 py-0.5 text-slate-700 hover:bg-slate-50">
                        <span class="col-span-3 font-semibold text-kp-blue-700">{{ $kode }}</span>
                        <span class="col-span-6">{{ $coa['nama'] }}</span>
                        <span class="col-span-3 text-right font-mono tabular-nums">{{ number_format($coa['nominal'], 2, ',', '.') }}</span>
                    </div>
                    @endforeach
                    <div class="border-t border-slate-400 my-1"></div>
                    <div class="grid grid-cols-12 py-1 font-bold text-slate-900">
                        <span class="col-span-9 uppercase">Jumlah Penerimaan</span>
                        <span class="col-span-3 text-right font-mono tabular-nums">{{ number_format($totalMasuk, 2, ',', '.') }}</span>
                    </div>
                    <div class="grid grid-cols-12 py-1 font-bold text-kp-blue-700 bg-slate-50 px-2 rounded">
                        <span class="col-span-9 uppercase">Jumlah Kas Tersedia</span>
                        <span class="col-span-3 text-right font-mono tabular-nums">{{ number_format($saldoKasTersedia, 2, ',', '.') }}</span>
                    </div>
                </div>

                <div class="space-y-1 pt-4 border-t border-dashed border-slate-300">
                    <div class="font-bold text-slate-800 tracking-wider text-[11px] uppercase pb-1">
                        PENGELUARAN - PENGELUARAN
                    </div>
                    @foreach($coaPengeluaran as $kode => $coa)
                    <div class="grid grid-cols-12 py-0.5 text-slate-700 hover:bg-slate-50">
                        <span class="col-span-3 font-semibold text-kp-blue-700">{{ $kode }}</span>
                        <span class="col-span-6">{{ $coa['nama'] }}</span>
                        <span class="col-span-3 text-right font-mono tabular-nums">{{ number_format($coa['nominal'], 2, ',', '.') }}</span>
                    </div>
                    @endforeach
                    <div class="border-t border-slate-400 my-1"></div>
                    <div class="grid grid-cols-12 py-1 font-bold text-slate-900">
                        <span class="col-span-9 uppercase">Jumlah Pengeluaran</span>
                        <span class="col-span-3 text-right font-mono tabular-nums">{{ number_format($totalKeluar, 2, ',', '.') }}</span>
                    </div>
                    <div class="grid grid-cols-12 py-1.5 font-bold text-sm text-slate-900 border-t-2 border-b-2 border-slate-900 bg-slate-50 px-2">
                        <span class="col-span-9 uppercase">Saldo Akhir</span>
                        <span class="col-span-3 text-right font-mono text-kp-blue-700 tabular-nums">{{ number_format($saldoAkhirRekap, 2, ',', '.') }}</span>
                    </div>
                </div>

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

        <!-- TAB 2: TABEL RINCIAN TRANSAKSI KAS (KI & KO) -->
        <div x-show="activeTab === 'rincian'" class="space-y-5">
            <!-- Tabel Kas Masuk (KI) -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Rincian Kas Masuk (KI)</h2>
                        <p class="text-xs text-slate-500">{{ $kasMasuks->count() }} transaksi penerimaan</p>
                    </div>
                    <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                        Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-3.5">No. Kuitansi</th>
                                <th class="py-3 px-3.5">Tanggal</th>
                                <th class="py-3 px-3.5">Divisi & COA</th>
                                <th class="py-3 px-3.5">Terima Dari / Pemasang</th>
                                <th class="py-3 px-3.5 text-right">Jumlah (Rp)</th>
                                <th class="py-3 px-3.5 text-center">Metode</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($kasMasuks as $idx => $ki)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm whitespace-nowrap">
                                    {{ $ki->no_kuitansi ?? $ki->no_bkm }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-slate-600 whitespace-nowrap">{{ $ki->tanggal_terima ? \Carbon\Carbon::parse($ki->tanggal_terima)->format('d/m/Y') : '-' }}</td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <div class="text-xs font-medium text-slate-800">{{ $ki->nama_divisi }}</div>
                                    <div class="font-mono text-xs text-slate-500">{{ $ki->kode_perkiraan }}</div>
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $ki->terima_dari ?? $ki->nama_pembayar }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-kp-blue-700 tabular-nums whitespace-nowrap">
                                    Rp {{ number_format($ki->jumlah_diterima ?? $ki->jumlah_dibayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $ki->metode_bayar ?? 'tunai' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500">Tidak ada transaksi kas masuk pada periode ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel Kas Keluar (KO) -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Rincian Kas Keluar (KO)</h2>
                        <p class="text-xs text-slate-500">{{ $kasKeluars->count() }} transaksi pengeluaran</p>
                    </div>
                    <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                        Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-3.5">No. BKK (KO)</th>
                                <th class="py-3 px-3.5">Tanggal</th>
                                <th class="py-3 px-3.5">No. Register BP</th>
                                <th class="py-3 px-3.5">Divisi & COA</th>
                                <th class="py-3 px-3.5">Diberikan Kepada / Uraian</th>
                                <th class="py-3 px-3.5 text-right">Jumlah (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($kasKeluars as $idx => $ko)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm whitespace-nowrap">
                                    {{ $ko->no_bkk }}
                                    @if($ko->is_kasbon)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] bg-slate-100 text-slate-700 border border-slate-200 font-sans font-medium block w-fit mt-0.5">Kasbon</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-slate-600 whitespace-nowrap">{{ $ko->tanggal_keluar ? \Carbon\Carbon::parse($ko->tanggal_keluar)->format('d/m/Y') : '-' }}</td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-700 whitespace-nowrap">
                                    {{ $ko->no_register_anggaran ?? $ko->no_bp_ref ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <div class="text-xs font-medium text-slate-800">{{ $ko->nama_divisi }}</div>
                                    <div class="font-mono text-xs text-slate-500">{{ $ko->kode_perkiraan }}</div>
                                </td>
                                <td class="py-3.5 px-3.5">
                                    <div class="font-semibold text-slate-900">{{ $ko->diberikan_kepada ?? $ko->nama_penerima }}</div>
                                    <div class="text-xs text-slate-500 truncate max-w-xs">{{ $ko->keperluan ?? $ko->keterangan }}</div>
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums whitespace-nowrap">
                                    Rp {{ number_format($ko->nominal_keluar ?? $ko->jumlah_keluar, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500">Tidak ada transaksi kas keluar pada periode ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel Rekapitulasi Asal Bulan/Tahun Faktur -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Rekapitulasi Asal Bulan/Tahun Faktur yang Dilunasi</h2>
                        <p class="text-xs text-slate-500">Pengelompokan kuitansi penerimaan berdasarkan periode faktur asal ({{ $labelPeriode }})</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-3.5">Bulan & Tahun Faktur Asal</th>
                                <th class="py-3 px-3.5 text-center">Jumlah Kuitansi</th>
                                <th class="py-3 px-3.5 text-right">Penerimaan Iklan (KI-01)</th>
                                <th class="py-3 px-3.5 text-right">Penerimaan Sirkulasi (KI-02)</th>
                                <th class="py-3 px-3.5 text-right">Total Piutang Tertagih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($rekapAsalFaktur ?? [] as $idx => $aging)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    {{ $aging['label'] ?? $aging['label_periode_asal'] ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center font-mono text-slate-700">
                                    {{ $aging['jumlah_kuitansi'] }} Kuitansi
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums whitespace-nowrap">
                                    Rp {{ number_format($aging['nominal_iklan'], 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums whitespace-nowrap">
                                    Rp {{ number_format($aging['nominal_sirkulasi'], 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-kp-blue-700 tabular-nums whitespace-nowrap">
                                    Rp {{ number_format($aging['total_nominal'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">Belum ada data kuitansi pelunasan faktur pada periode ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
