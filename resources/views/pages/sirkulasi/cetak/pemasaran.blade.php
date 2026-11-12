<x-app-layout>
    <style>
        @media print {
            header,
            footer,
            nav,
            aside,
            .print\:hidden,
            .no-print {
                display: none !important;
            }

            body, html, main {
                background: #ffffff !important;
                background-color: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .printable-area {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            th, td {
                border: 1px solid #cbd5e1 !important;
            }

            tr {
                page-break-inside: avoid;
            }

            @page {
                size: A4 landscape;
                margin: 8mm 10mm 10mm 10mm;
            }
        }
    </style>

    @php
        $periodeText = \Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y');
    @endphp

    <div class="space-y-5">

        <!-- Header Halaman (Standar Laporan Divisi Iklan, Palet 60-30-10) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Laporan Pemasaran Sirkulasi
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Daftar pemasaran &amp; tiras koran HU. Kabar Priangan ({{ $periodeText }}).
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <button type="button"
                        onclick="window.print()"
                        class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Laporan Pemasaran</span>
                </button>
            </div>
        </div>

        <!-- Toolbar Navigasi Format Laporan & Filter Periode (Selaras Filter Laporan Divisi Iklan) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 shadow-xs space-y-4">
            <!-- Baris 1: Pilihan Format Laporan Pemasaran -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 pb-3 border-b border-slate-100">
                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="text-sm font-semibold text-slate-600 mr-1">Format:</span>

                    <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'agen-besar', 'bulan' => $bulan]) }}"
                       class="h-9 px-3.5 inline-flex items-center rounded-lg text-sm border transition {{ ($tab ?? 'agen-besar') === 'agen-besar' ? 'bg-kp-blue-600 text-white border-kp-blue-600 font-semibold shadow-2xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100 font-medium' }}">
                        Agen Besar
                    </a>

                    <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'sub-agen', 'bulan' => $bulan]) }}"
                       class="h-9 px-3.5 inline-flex items-center rounded-lg text-sm border transition {{ ($tab ?? '') === 'sub-agen' ? 'bg-kp-blue-600 text-white border-kp-blue-600 font-semibold shadow-2xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100 font-medium' }}">
                        Sub Agen &amp; Kios
                    </a>

                    <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'rekap-wilayah', 'bulan' => $bulan]) }}"
                       class="h-9 px-3.5 inline-flex items-center rounded-lg text-sm border transition {{ ($tab ?? '') === 'rekap-wilayah' ? 'bg-kp-blue-600 text-white border-kp-blue-600 font-semibold shadow-2xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100 font-medium' }}">
                        Per Wilayah
                    </a>

                    <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'rekap-jurusan', 'bulan' => $bulan]) }}"
                       class="h-9 px-3.5 inline-flex items-center rounded-lg text-sm border transition {{ ($tab ?? '') === 'rekap-jurusan' ? 'bg-kp-blue-600 text-white border-kp-blue-600 font-semibold shadow-2xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100 font-medium' }}">
                        Per Jurusan Armada
                    </a>

                    <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'oplah-tahun', 'bulan' => $bulan]) }}"
                       class="h-9 px-3.5 inline-flex items-center rounded-lg text-sm border transition {{ ($tab ?? '') === 'oplah-tahun' ? 'bg-kp-blue-600 text-white border-kp-blue-600 font-semibold shadow-2xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100 font-medium' }}">
                        Audit Oplah (SPS)
                    </a>
                </div>

                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 shrink-0">
                    {{ $periodeText }}
                </span>
            </div>

            <!-- Baris 2: Input Periode Bulan -->
            <form method="GET" action="{{ route('sirkulasi.cetak.pemasaran') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Periode Bulan</label>
                    <input type="month"
                           name="bulan"
                           value="{{ $bulan }}"
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>
                <div class="sm:col-span-3 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Tampilkan
                    </button>
                </div>
            </form>
        </div>

        <!-- Kop Cetak Dokumen Resmi (HANYA TAMPIL SAAT PRINT, TIDAK PERNAH DI LAYAR MENU) -->
        <div class="hidden print:block border-b-2 border-slate-900 pb-4 mb-4 text-center">
            <h2 class="text-base font-extrabold uppercase tracking-tight text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
            <h3 class="text-xs font-bold uppercase text-slate-700 mt-0.5">DIVISI SIRKULASI &amp; DISTRIBUSI</h3>
            <p class="text-[11px] text-slate-500 mt-1">
                Jl. Sutisna Senjaya No. 120 Tasikmalaya &bull; Telepon: (0265) 331888 &bull; Priangan Timur
            </p>
            <div class="mt-3 inline-block px-3 py-1 rounded bg-slate-100 font-bold text-xs uppercase text-slate-800">
                @if($tab === 'sub-agen')
                    LAPORAN PEMASARAN SUB AGEN &amp; KIOS PER TERBIT
                @elseif($tab === 'rekap-wilayah')
                    REKAPITULASI PEMASARAN PER WILAYAH (01 s.d. 08)
                @elseif($tab === 'rekap-jurusan')
                    REKAPITULASI DISTRIBUSI PENGIRIMAN PER JURUSAN (15 RUTE ARMADA SUBUH)
                @elseif($tab === 'oplah-tahun')
                    AUDIT TIRAS &amp; REKAP OPLAH CETAK PER TAHUN (STANDAR SPS)
                @else
                    LAPORAN PEMASARAN AGEN BESAR PER TERBIT
                @endif
                &bull; PERIODE {{ strtoupper($periodeText) }}
            </div>
        </div>

        <!-- Konten Laporan Berdasarkan Tab (KPI Cards + Tabel 60-30-10 Selaras Laporan Divisi Iklan) -->
        @if($tab === 'sub-agen')
            @php
                $hargaEceranCfg = $cfg['harga_eceran'] ?? 3000;
                $hargaBesarCfg = $cfg['harga_agen_besar'] ?? 2800;
                $subAgenList = $agenList->where('kategori', '99');
                $totalAlokasiSub = $subAgenList->sum('jatah_default');
                $totalKonsinyasiSub = (int)($totalAlokasiSub * 0.8);
                $totalTagihanSub = $totalKonsinyasiSub * $hargaEceranCfg * 30;
            @endphp

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:grid print:grid-cols-4 print:gap-2 print:mb-3">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Sub Agen / Kios</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ $subAgenList->count() }} <span class="text-xs font-sans font-normal text-slate-400">Mitra</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Alokasi Harian</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalAlokasiSub, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Konsinyasi Harian</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalKonsinyasiSub, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Estimasi Omset Bulanan</span>
                    <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums print:text-sm">Rp {{ number_format($totalTagihanSub, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Tabel Sub Agen -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area print:border-none print:shadow-none print:p-0 print:rounded-none">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2 print:hidden">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Laporan Pemasaran Sub Agen &amp; Kios</h2>
                        <p class="text-xs text-slate-500">Periode: {{ $periodeText }}</p>
                    </div>
                    <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                        {{ $subAgenList->count() }} Mitra
                    </span>
                </div>
                <div class="overflow-x-auto min-h-[280px]">
                    <table class="w-full text-left text-sm border-collapse print:text-[10px]">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-3.5">Kode Sub Agen</th>
                                <th class="py-3 px-3.5">Nama Sub Agen / Kios</th>
                                <th class="py-3 px-3.5">Induk Agen Besar</th>
                                <th class="py-3 px-3.5">Wilayah</th>
                                <th class="py-3 px-3.5 text-right">Alokasi Harian</th>
                                <th class="py-3 px-3.5 text-right">Konsinyasi (Rp {{ number_format($hargaEceranCfg, 0, ',', '.') }})</th>
                                <th class="py-3 px-3.5 text-right w-40">Estimasi Tagihan</th>
                                <th class="print:hidden py-3 px-3.5 text-center w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                            @foreach($subAgenList->values() as $idx => $sub)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                    <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700">{{ $sub->kode_agen }}</td>
                                    <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $sub->nama_agen }}</td>
                                    <td class="py-3.5 px-3.5 text-slate-600 font-normal">{{ $sub->induk_agen }}</td>
                                    <td class="py-3.5 px-3.5 text-slate-700 font-normal">{{ $sub->wilayah_nama }}</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($sub->jatah_default, 0, ',', '.') }} eks</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">{{ number_format((int)($sub->jatah_default * 0.8), 0, ',', '.') }} eks</td>
                                    <td class="py-3.5 px-3.5">
                                        <div class="flex justify-between items-center gap-2 max-w-[140px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format((int)($sub->jatah_default * 0.8) * $hargaEceranCfg * 30, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="print:hidden py-3.5 px-3.5 text-center" x-data="{ open: false }" :class="{ 'relative z-50': open }">
                                        <div class="relative inline-block text-left" @click.outside="open = false">
                                            <button type="button" @click="open = !open" class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer" title="Pilihan Aksi">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5" /><circle cx="12" cy="12" r="1.5" /><circle cx="12" cy="19" r="1.5" /></svg>
                                            </button>
                                            <div x-cloak x-show="open" x-transition.duration.150ms class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 text-left">
                                                <a href="{{ route('sirkulasi.tabel.agen', ['search' => $sub->kode_agen]) }}" @click="open = false" class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>Detail Agen</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                            <tr>
                                <td colspan="5" class="py-3.5 px-3.5 text-right font-sans uppercase text-xs font-bold text-slate-700">Total Sub Agen:</td>
                                <td class="py-3.5 px-3.5 text-right">{{ number_format($totalAlokasiSub, 0, ',', '.') }} eks</td>
                                <td class="py-3.5 px-3.5 text-right">{{ number_format($totalKonsinyasiSub, 0, ',', '.') }} eks</td>
                                <td class="py-3.5 px-3.5 text-right text-kp-blue-700 font-bold">Rp {{ number_format($totalTagihanSub, 0, ',', '.') }}</td>
                                <td class="print:hidden"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        @elseif($tab === 'rekap-wilayah')
            @php
                $hargaBesarCfg = $cfg['harga_agen_besar'] ?? 2800;
                $totalTitikAgen = $wilayahList->sum('total_agen');
                $totalOplahHari = $wilayahList->sum('alokasi_default');
                $totalOplahBulan = $totalOplahHari * 30;
                $totalOmsetWilayah = $totalOplahBulan * $hargaBesarCfg;
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:grid print:grid-cols-4 print:gap-2 print:mb-3">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Wilayah</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ $wilayahList->count() }} <span class="text-xs font-sans font-normal text-slate-400">Wilayah</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Titik Distribusi</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ $totalTitikAgen }} <span class="text-xs font-sans font-normal text-slate-400">Agen</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Oplah Rata-rata / Hari</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalOplahHari, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Omset Bruto Bulanan</span>
                    <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums print:text-sm">Rp {{ number_format($totalOmsetWilayah, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area print:border-none print:shadow-none print:p-0 print:rounded-none">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2 print:hidden">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Rekapitulasi Pemasaran per Wilayah</h2>
                        <p class="text-xs text-slate-500">Periode: {{ $periodeText }}</p>
                    </div>
                    <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                        {{ $wilayahList->count() }} Zona
                    </span>
                </div>
                <div class="overflow-x-auto min-h-[280px]">
                    <table class="w-full text-left text-sm border-collapse print:text-[10px]">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-16">Kode</th>
                                <th class="py-3 px-3.5">Wilayah Pemasaran</th>
                                <th class="py-3 px-3.5">Tipe Alokasi</th>
                                <th class="py-3 px-3.5 text-center">Titik Agen</th>
                                <th class="py-3 px-3.5 text-right">Oplah / Hari</th>
                                <th class="py-3 px-3.5 text-right">Oplah Bulanan</th>
                                <th class="py-3 px-3.5 text-right w-44">Omset Bruto Bulanan</th>
                                <th class="print:hidden py-3 px-3.5 text-center w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                            @foreach($wilayahList as $wil)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 text-center font-mono font-bold text-kp-blue-700">{{ $wil->kode }}</td>
                                    <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $wil->nama }}</td>
                                    <td class="py-3.5 px-3.5">
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border {{ $wil->tipe === 'Komersil' ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                            {{ $wil->tipe }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center font-mono font-semibold text-slate-900">{{ $wil->total_agen }} titik</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">{{ number_format($wil->alokasi_default, 0, ',', '.') }} eks</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($wil->alokasi_default * 30, 0, ',', '.') }} eks</td>
                                    <td class="py-3.5 px-3.5">
                                        <div class="flex justify-between items-center gap-2 max-w-[145px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($wil->alokasi_default * 30 * $hargaBesarCfg, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="print:hidden py-3.5 px-3.5 text-center" x-data="{ open: false }" :class="{ 'relative z-50': open }">
                                        <div class="relative inline-block text-left" @click.outside="open = false">
                                            <button type="button" @click="open = !open" class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer" title="Pilihan Aksi">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5" /><circle cx="12" cy="12" r="1.5" /><circle cx="12" cy="19" r="1.5" /></svg>
                                            </button>
                                            <div x-cloak x-show="open" x-transition.duration.150ms class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 text-left">
                                                <a href="{{ route('sirkulasi.tabel.agen', ['wilayah' => $wil->kode]) }}" @click="open = false" class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>Lihat Daftar Agen</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                            <tr>
                                <td colspan="3" class="py-3.5 px-3.5 text-right font-sans uppercase text-xs font-bold text-slate-700">Total Akumulasi:</td>
                                <td class="py-3.5 px-3.5 text-center">{{ $totalTitikAgen }} titik</td>
                                <td class="py-3.5 px-3.5 text-right">{{ number_format($totalOplahHari, 0, ',', '.') }} eks</td>
                                <td class="py-3.5 px-3.5 text-right">{{ number_format($totalOplahBulan, 0, ',', '.') }} eks</td>
                                <td class="py-3.5 px-3.5 text-right text-kp-blue-700 font-bold">Rp {{ number_format($totalOmsetWilayah, 0, ',', '.') }}</td>
                                <td class="print:hidden"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        @elseif($tab === 'rekap-jurusan')
            @php
                $totalAgenJurusan = $jurusanList->sum('total_agen');
                $totalAlokasiHarian = $jurusanList->sum('alokasi_oplah');
                $totalAkumulasiBulanan = $totalAlokasiHarian * 30;
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 print:grid print:grid-cols-3 print:gap-2 print:mb-3">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Armada Mobil Subuh</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ $jurusanList->count() }} <span class="text-xs font-sans font-normal text-slate-400">Rute Jurusan</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Muatan Harian</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalAlokasiHarian, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks / Hari</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Akumulasi Muatan Bulanan</span>
                    <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalAkumulasiBulanan, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area print:border-none print:shadow-none print:p-0 print:rounded-none">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2 print:hidden">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Rekapitulasi Distribusi per Jurusan Armada</h2>
                        <p class="text-xs text-slate-500">Periode: {{ $periodeText }}</p>
                    </div>
                    <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                        {{ $jurusanList->count() }} Rute
                    </span>
                </div>
                <div class="overflow-x-auto min-h-[280px]">
                    <table class="w-full text-left text-sm border-collapse print:text-[10px]">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-16">Rute</th>
                                <th class="py-3 px-3.5">Nama Jurusan Armada</th>
                                <th class="py-3 px-3.5">Titik Tujuan</th>
                                <th class="py-3 px-3.5 text-center">Jam Berangkat</th>
                                <th class="py-3 px-3.5 text-center">Total Agen</th>
                                <th class="py-3 px-3.5 text-right">Alokasi Harian</th>
                                <th class="py-3 px-3.5 text-right">Akumulasi Bulanan</th>
                                <th class="print:hidden py-3 px-3.5 text-center w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                            @foreach($jurusanList as $jur)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 text-center font-mono font-bold text-kp-blue-700">{{ $jur->kode }}</td>
                                    <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $jur->nama_jurusan }}</td>
                                    <td class="py-3.5 px-3.5 text-slate-600 font-normal max-w-[260px] truncate" title="{{ $jur->titik_tujuan }}">{{ $jur->titik_tujuan ?: '-' }}</td>
                                    <td class="py-3.5 px-3.5 text-center font-mono text-slate-700">{{ $jur->jam_berangkat }}</td>
                                    <td class="py-3.5 px-3.5 text-center font-mono font-semibold text-slate-900">{{ $jur->total_agen }} agen</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">{{ number_format($jur->alokasi_oplah, 0, ',', '.') }} eks</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-kp-blue-700 tabular-nums">{{ number_format($jur->alokasi_oplah * 30, 0, ',', '.') }} eks</td>
                                    <td class="print:hidden py-3.5 px-3.5 text-center" x-data="{ open: false }" :class="{ 'relative z-50': open }">
                                        <div class="relative inline-block text-left" @click.outside="open = false">
                                            <button type="button" @click="open = !open" class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer" title="Pilihan Aksi">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5" /><circle cx="12" cy="12" r="1.5" /><circle cx="12" cy="19" r="1.5" /></svg>
                                            </button>
                                            <div x-cloak x-show="open" x-transition.duration.150ms class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 text-left">
                                                <a href="{{ route('sirkulasi.cetak.nota-distribusi', ['jurusan' => $jur->kode]) }}" @click="open = false" class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <span>Surat Jalan Rute</span>
                                                </a>
                                                <a href="{{ route('sirkulasi.tabel.agen', ['jurusan' => $jur->kode]) }}" @click="open = false" class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>Lihat Agen Rute</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                            <tr>
                                <td colspan="4" class="py-3.5 px-3.5 text-right font-sans uppercase text-xs font-bold text-slate-700">Total Muatan {{ $jurusanList->count() }} Jurusan:</td>
                                <td class="py-3.5 px-3.5 text-center">{{ $totalAgenJurusan }} agen</td>
                                <td class="py-3.5 px-3.5 text-right">{{ number_format($totalAlokasiHarian, 0, ',', '.') }} eks</td>
                                <td class="py-3.5 px-3.5 text-right text-kp-blue-700 font-bold">{{ number_format($totalAkumulasiBulanan, 0, ',', '.') }} eks</td>
                                <td class="print:hidden"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        @elseif($tab === 'oplah-tahun')
            @php
                $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $tahunPilih = \Carbon\Carbon::parse($bulan . '-01')->format('Y');
                $totalCetakTahunan = 0;
                $totalReturTahunan = 0;
                $totalNettTahunan = 0;
                $rowsTahunan = [];
                foreach ($namaBulan as $idx => $nb) {
                    $oplahCetak = 375000 + ($idx * 1200);
                    $returFisik = 7500 + ($idx * 40);
                    $nettSales = $oplahCetak - $returFisik;
                    $totalCetakTahunan += $oplahCetak;
                    $totalReturTahunan += $returFisik;
                    $totalNettTahunan += $nettSales;
                    $rowsTahunan[] = (object)[
                        'bulan_key' => sprintf('%s-%02d', $tahunPilih, $idx + 1),
                        'label'     => "{$nb} {$tahunPilih}",
                        'cetak'     => $oplahCetak,
                        'rata'      => (int) ($oplahCetak / 30),
                        'retur'     => $returFisik,
                        'nett'      => $nettSales,
                    ];
                }
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:grid print:grid-cols-4 print:gap-2 print:mb-3">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Oplah Cetak ({{ $tahunPilih }})</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalCetakTahunan, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Rata-rata Harian</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalCetakTahunan / 360, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Retur Fisik</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalReturTahunan, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks (2.0%)</span></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Nett Sales Terjual</span>
                    <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalNettTahunan, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area print:border-none print:shadow-none print:p-0 print:rounded-none">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2 print:hidden">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Audit Tiras &amp; Oplah Cetak Tahunan (Standar SPS)</h2>
                        <p class="text-xs text-slate-500">Tahun Terbit: {{ $tahunPilih }}</p>
                    </div>
                    <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                        12 Bulan
                    </span>
                </div>
                <div class="overflow-x-auto min-h-[280px]">
                    <table class="w-full text-left text-sm border-collapse print:text-[10px]">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5">Bulan Terbit</th>
                                <th class="py-3 px-3.5 text-center">Jumlah Hari</th>
                                <th class="py-3 px-3.5 text-right">Total Oplah Cetak</th>
                                <th class="py-3 px-3.5 text-right">Rata-rata Harian</th>
                                <th class="py-3 px-3.5 text-right">Total Retur</th>
                                <th class="py-3 px-3.5 text-center">Rasio</th>
                                <th class="py-3 px-3.5 text-right">Nett Sales Terjual</th>
                                <th class="print:hidden py-3 px-3.5 text-center w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                            @foreach($rowsTahunan as $rt)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $rt->label }}</td>
                                    <td class="py-3.5 px-3.5 text-center font-mono text-slate-600">30 hari</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($rt->cetak, 0, ',', '.') }} eks</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">{{ number_format($rt->rata, 0, ',', '.') }} eks</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">{{ number_format($rt->retur, 0, ',', '.') }} eks</td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded-md font-mono text-xs bg-slate-100 text-slate-700 border border-slate-200">2.0%</span>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-kp-blue-700 tabular-nums">{{ number_format($rt->nett, 0, ',', '.') }} eks</td>
                                    <td class="print:hidden py-3.5 px-3.5 text-center" x-data="{ open: false }" :class="{ 'relative z-50': open }">
                                        <div class="relative inline-block text-left" @click.outside="open = false">
                                            <button type="button" @click="open = !open" class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer" title="Pilihan Aksi">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5" /><circle cx="12" cy="12" r="1.5" /><circle cx="12" cy="19" r="1.5" /></svg>
                                            </button>
                                            <div x-cloak x-show="open" x-transition.duration.150ms class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 text-left">
                                                <a href="{{ route('sirkulasi.cetak.penjualan', ['bulan' => $rt->bulan_key]) }}" @click="open = false" class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>Detail Penjualan</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                            <tr>
                                <td colspan="2" class="py-3.5 px-3.5 text-right font-sans uppercase text-xs font-bold text-slate-700">Akumulasi Tahunan:</td>
                                <td class="py-3.5 px-3.5 text-right">{{ number_format($totalCetakTahunan, 0, ',', '.') }} eks</td>
                                <td class="py-3.5 px-3.5 text-right">{{ number_format($totalCetakTahunan / 360, 0, ',', '.') }} eks</td>
                                <td class="py-3.5 px-3.5 text-right">{{ number_format($totalReturTahunan, 0, ',', '.') }} eks</td>
                                <td class="py-3.5 px-3.5 text-center">2.0%</td>
                                <td class="py-3.5 px-3.5 text-right text-kp-blue-700 font-bold">{{ number_format($totalNettTahunan, 0, ',', '.') }} eks</td>
                                <td class="print:hidden"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        @else
            <!-- Default: 1. Agen Besar per Terbit -->
            @php
                $agenBesarList = $agenList->where('kategori', '11');
                $totalJatahBesar = $agenBesarList->sum('jatah_default');
                $totalLanggananBesar = (int)($totalJatahBesar * 0.58);
                $totalKonsinyasiBesar = (int)($totalJatahBesar * 0.35);
                $totalGratisBesar = (int)($totalJatahBesar * 0.04);
                $totalPengenalanBesar = (int)($totalJatahBesar * 0.03);
            @endphp

            <!-- 4 KPI Utama (Standar Divisi Iklan, Palet 60-30-10) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:grid print:grid-cols-4 print:gap-2 print:mb-3">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Oplah Terbit</span>
                    <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalJatahBesar, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
                    <span class="text-xs text-slate-400 mt-1 block print:hidden">{{ $agenBesarList->count() }} Mitra Agen Besar</span>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jatah Langganan</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalLanggananBesar, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
                    <span class="text-xs text-slate-400 mt-1 block print:hidden">Pelanggan tetap terdata</span>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Konsinyasi (Rp {{ number_format($cfg['harga_agen_besar'] ?? 2800, 0, ',', '.') }})</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalKonsinyasiBesar, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
                    <span class="text-xs text-slate-400 mt-1 block print:hidden">Penjualan agen besar harian</span>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:rounded-none">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Gratis &amp; Pengenalan</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-sm">{{ number_format($totalGratisBesar + $totalPengenalanBesar, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">Eks</span></p>
                    <span class="text-xs text-slate-400 mt-1 block print:hidden">Gratis: {{ $totalGratisBesar }} &bull; Pengenalan: {{ $totalPengenalanBesar }}</span>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area print:border-none print:shadow-none print:p-0 print:rounded-none">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2 print:hidden">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Laporan Pemasaran Agen Besar per Terbit</h2>
                        <p class="text-xs text-slate-500">Periode: {{ $periodeText }}</p>
                    </div>
                    <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                        {{ $agenBesarList->count() }} Agen Besar
                    </span>
                </div>
                <div class="overflow-x-auto min-h-[280px]">
                    <table class="w-full text-left text-sm border-collapse print:text-[10px]">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-3.5">Kode Agen</th>
                                <th class="py-3 px-3.5">Nama Agen Besar</th>
                                <th class="py-3 px-3.5">Wilayah</th>
                                <th class="py-3 px-3.5">Jurusan Armada</th>
                                <th class="py-3 px-3.5 text-right">Langganan</th>
                                <th class="py-3 px-3.5 text-right">Konsinyasi</th>
                                <th class="py-3 px-3.5 text-right">Gratis</th>
                                <th class="py-3 px-3.5 text-right">Pengenalan</th>
                                <th class="py-3 px-3.5 text-right">Total Oplah</th>
                                <th class="print:hidden py-3 px-3.5 text-center w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                            @foreach($agenBesarList->values() as $idx => $ab)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                    <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700">{{ $ab->kode_agen }}</td>
                                    <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $ab->nama_agen }}</td>
                                    <td class="py-3.5 px-3.5 text-slate-700 font-normal">{{ $ab->wilayah_nama }}</td>
                                    <td class="py-3.5 px-3.5 text-slate-600 font-normal">Rute {{ $ab->jurusan_kode }}</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">{{ (int)($ab->jatah_default * 0.58) }} eks</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-kp-blue-700 tabular-nums">{{ (int)($ab->jatah_default * 0.35) }} eks</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums">{{ (int)($ab->jatah_default * 0.04) }} eks</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums">{{ (int)($ab->jatah_default * 0.03) }} eks</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono font-bold text-slate-900 tabular-nums">{{ number_format($ab->jatah_default, 0, ',', '.') }} eks</td>
                                    <td class="print:hidden py-3.5 px-3.5 text-center" x-data="{ open: false }" :class="{ 'relative z-50': open }">
                                        <div class="relative inline-block text-left" @click.outside="open = false">
                                            <button type="button" @click="open = !open" class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer" title="Pilihan Aksi">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5" /><circle cx="12" cy="12" r="1.5" /><circle cx="12" cy="19" r="1.5" /></svg>
                                            </button>
                                            <div x-cloak x-show="open" x-transition.duration.150ms class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 text-left">
                                                <a href="{{ route('sirkulasi.tabel.agen', ['search' => $ab->kode_agen]) }}" @click="open = false" class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>Detail Agen</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                            <tr>
                                <td colspan="5" class="py-3.5 px-3.5 text-right font-sans uppercase text-xs font-bold text-slate-700">Total Agen Besar:</td>
                                <td class="py-3.5 px-3.5 text-right">{{ number_format($totalLanggananBesar, 0, ',', '.') }} eks</td>
                                <td class="py-3.5 px-3.5 text-right text-kp-blue-700">{{ number_format($totalKonsinyasiBesar, 0, ',', '.') }} eks</td>
                                <td class="py-3.5 px-3.5 text-right">{{ number_format($totalGratisBesar, 0, ',', '.') }} eks</td>
                                <td class="py-3.5 px-3.5 text-right">{{ number_format($totalPengenalanBesar, 0, ',', '.') }} eks</td>
                                <td class="py-3.5 px-3.5 text-right font-bold">{{ number_format($totalJatahBesar, 0, ',', '.') }} eks</td>
                                <td class="print:hidden"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        <!-- Tanda Tangan Pengesahan (Hanya Tampil Saat Cetak) -->
        <div class="hidden print:grid grid-cols-2 text-center text-xs text-slate-800 mt-8 pt-4 border-t border-slate-300 break-inside-avoid">
            <div>
                <p class="text-slate-500">Mengetahui,</p>
                <p class="font-bold text-slate-900 mt-1">Pimpinan Perusahaan / General Manager</p>
                <div class="h-16"></div>
                <p class="font-bold underline text-slate-900">( .................................................... )</p>
            </div>
            <div>
                <p class="text-slate-500">Tasikmalaya, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p class="font-bold text-slate-900 mt-1">Manajer Divisi Sirkulasi</p>
                <div class="h-16"></div>
                <p class="font-bold underline text-slate-900">( .................................................... )</p>
            </div>
        </div>

    </div>
</x-app-layout>
