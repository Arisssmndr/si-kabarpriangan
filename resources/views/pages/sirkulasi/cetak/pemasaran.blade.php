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

            table > thead,
            table > thead tr,
            table > thead th {
                background-color: #f1f5f9 !important;
                color: #0f172a !important;
                border: 1px solid #cbd5e1 !important;
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

    <div class="space-y-5">

        <!-- Header Halaman (60-30-10 Palette) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Laporan Pemasaran Sirkulasi
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Distribusi agen besar, sub-agen, rekapitulasi wilayah, jurusan armada, dan audit oplah tahunan.
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
                    <span>Cetak Laporan</span>
                </button>
            </div>
        </div>

        <!-- Toolbar Filter Periode & Navigasi 5 Format Laporan -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 shadow-xs space-y-4">
            <form method="GET" action="{{ route('sirkulasi.cetak.pemasaran') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="w-full sm:w-64">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Periode Bulan</label>
                    <input type="month"
                           name="bulan"
                           value="{{ $bulan }}"
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>
                <button type="submit" class="h-11 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    Tampilkan Periode
                </button>
            </form>

            <div class="pt-3 border-t border-slate-100 overflow-x-auto">
                <div class="flex items-center gap-1.5 min-w-max">
                    <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'agen-besar', 'bulan' => $bulan]) }}"
                       class="px-3.5 py-2 text-xs rounded-xl font-semibold transition {{ ($tab ?? 'agen-besar') === 'agen-besar' ? 'bg-kp-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        1. Agen Besar / Terbit
                    </a>
                    <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'sub-agen', 'bulan' => $bulan]) }}"
                       class="px-3.5 py-2 text-xs rounded-xl font-semibold transition {{ ($tab ?? '') === 'sub-agen' ? 'bg-kp-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        2. Sub Agen &amp; Kios
                    </a>
                    <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'rekap-wilayah', 'bulan' => $bulan]) }}"
                       class="px-3.5 py-2 text-xs rounded-xl font-semibold transition {{ ($tab ?? '') === 'rekap-wilayah' ? 'bg-kp-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        3. Rekapitulasi Wilayah
                    </a>
                    <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'rekap-jurusan', 'bulan' => $bulan]) }}"
                       class="px-3.5 py-2 text-xs rounded-xl font-semibold transition {{ ($tab ?? '') === 'rekap-jurusan' ? 'bg-kp-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        4. Rekapitulasi Jurusan
                    </a>
                    <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'oplah-tahun', 'bulan' => $bulan]) }}"
                       class="px-3.5 py-2 text-xs rounded-xl font-semibold transition {{ ($tab ?? '') === 'oplah-tahun' ? 'bg-kp-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        5. Audit Oplah Tahunan (SPS)
                    </a>
                </div>
            </div>
        </div>

        @php
            $judulLaporan = match($tab) {
                'sub-agen'      => 'Laporan Pemasaran Sub Agen & Kios per Terbit',
                'rekap-wilayah' => 'Rekapitulasi Pemasaran per Wilayah (01 s.d. 08)',
                'rekap-jurusan' => 'Rekapitulasi Distribusi per Jurusan Armada Subuh',
                'oplah-tahun'   => 'Audit Tiras & Rekap Oplah Cetak Tahunan (Standar SPS)',
                default         => 'Laporan Pemasaran Agen Besar per Terbit',
            };
            $periodeFormatted = \Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y');
        @endphp

        <!-- Konten Laporan Terpilih -->
        <div class="printable-area space-y-5 print:space-y-3">

            <!-- Kop Cetak Dokumen Resmi (Tampil Saat Print) -->
            <div class="hidden print:block border-b-2 border-slate-900 pb-4 mb-4 text-center">
                <h2 class="text-base font-extrabold uppercase tracking-tight text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                <h3 class="text-xs font-bold uppercase text-slate-700 mt-0.5">DIVISI SIRKULASI &bull; SISTEM INFORMASI SIRKULASI</h3>
                <p class="text-[11px] text-slate-500 mt-1">
                    Jl. Sutisna Senjaya No. 120 Tasikmalaya &bull; Telepon: (0265) 331888 &bull; Priangan Timur
                </p>
                <div class="mt-2.5 inline-block px-3 py-1 rounded bg-slate-100 font-bold text-xs uppercase text-slate-800 border border-slate-300">
                    {{ strtoupper($judulLaporan) }} &bull; PERIODE {{ strtoupper($periodeFormatted) }}
                </div>
            </div>

            @if($tab === 'sub-agen')
                @php
                    $subAgenList = $agenList->where('kategori', '99');
                    $totalAlokasiSub = $subAgenList->sum('jatah_default');
                    $totalKonsinyasiSub = (int)($totalAlokasiSub * 0.8);
                    $totalTagihanSub = $totalKonsinyasiSub * 3000 * 30;
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 print:grid-cols-3 print:gap-2">
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Estimasi Omset Bulanan</span>
                        <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums print:text-xs print:text-slate-900">
                            Rp {{ number_format($totalTagihanSub, 0, ',', '.') }}
                        </p>
                        <span class="text-xs text-slate-400 mt-1 block print:hidden">Tarif konsinyasi Rp 3.000 / eks</span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Sub Agen / Kios</span>
                        <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-xs">
                            {{ $subAgenList->count() }} <span class="text-xs font-sans font-normal text-slate-400">Mitra</span>
                        </p>
                        <span class="text-xs text-slate-400 mt-1 block print:hidden">Kategori 99 aktif</span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Alokasi Harian Sub Agen</span>
                        <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-xs">
                            {{ number_format($totalAlokasiSub, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                        </p>
                        <span class="text-xs text-slate-400 mt-1 block print:hidden">Konsinyasi: {{ number_format($totalKonsinyasiSub, 0, ',', '.') }} eks</span>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden print:border-none print:shadow-none">
                    <div class="print:hidden px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">{{ $judulLaporan }}</h2>
                            <p class="text-xs text-slate-500">Periode {{ $periodeFormatted }}</p>
                        </div>
                        <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                            {{ $subAgenList->count() }} Sub Agen
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse print:text-[10px]">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap print:bg-slate-100">
                                <tr>
                                    <th class="py-3 px-3.5 text-center w-12">No</th>
                                    <th class="py-3 px-3.5">Kode Sub Agen</th>
                                    <th class="py-3 px-3.5">Nama Sub Agen / Kios</th>
                                    <th class="py-3 px-3.5">Induk Agen Besar</th>
                                    <th class="py-3 px-3.5">Wilayah</th>
                                    <th class="py-3 px-3.5 text-right">Alokasi Harian</th>
                                    <th class="py-3 px-3.5 text-right">Konsinyasi (Rp3.000)</th>
                                    <th class="py-3 px-3.5 text-right">Estimasi Tagihan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                                @foreach($subAgenList->values() as $idx => $sub)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                        <td class="py-3 px-3.5 font-mono font-bold text-kp-blue-700 print:text-slate-900">{{ $sub->kode_agen }}</td>
                                        <td class="py-3 px-3.5 font-semibold text-slate-900">{{ $sub->nama_agen }}</td>
                                        <td class="py-3 px-3.5 text-slate-600">{{ $sub->induk_agen }}</td>
                                        <td class="py-3 px-3.5 text-slate-700">{{ $sub->wilayah_nama }}</td>
                                        <td class="py-3 px-3.5 text-right font-mono tabular-nums text-slate-800">{{ $sub->jatah_default }} eks</td>
                                        <td class="py-3 px-3.5 text-right font-mono tabular-nums text-slate-700">{{ (int)($sub->jatah_default * 0.8) }} eks</td>
                                        <td class="py-3 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">
                                            Rp {{ number_format((int)($sub->jatah_default * 0.8) * 3000 * 30, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 print:bg-slate-100">
                                <tr>
                                    <td colspan="5" class="py-3 px-3.5 text-right font-sans text-xs uppercase tracking-wider text-slate-600">Total:</td>
                                    <td class="py-3 px-3.5 text-right tabular-nums">{{ number_format($totalAlokasiSub, 0, ',', '.') }} eks</td>
                                    <td class="py-3 px-3.5 text-right tabular-nums">{{ number_format($totalKonsinyasiSub, 0, ',', '.') }} eks</td>
                                    <td class="py-3 px-3.5 text-right font-bold text-kp-blue-700 tabular-nums print:text-slate-900">
                                        Rp {{ number_format($totalTagihanSub, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            @elseif($tab === 'rekap-wilayah')
                @php
                    $totalTitikAgen = $wilayahList->sum('total_agen');
                    $totalOplahHari = $wilayahList->sum('alokasi_default');
                    $totalOplahBulan = $totalOplahHari * 30;
                    $totalOmsetWilayah = $totalOplahBulan * 2800;
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:grid-cols-4 print:gap-2">
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Omset Bruto Bulanan</span>
                        <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums print:text-xs print:text-slate-900">
                            Rp {{ number_format($totalOmsetWilayah, 0, ',', '.') }}
                        </p>
                        <span class="text-xs text-slate-400 mt-1 block print:hidden">Estimasi seluruh wilayah</span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Wilayah</span>
                        <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-xs">
                            {{ $wilayahList->count() }} <span class="text-xs font-sans font-normal text-slate-400">Wilayah</span>
                        </p>
                        <span class="text-xs text-slate-400 mt-1 block print:hidden">Priangan Timur</span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Titik Distribusi</span>
                        <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-xs">
                            {{ $totalTitikAgen }} <span class="text-xs font-sans font-normal text-slate-400">Titik Agen</span>
                        </p>
                        <span class="text-xs text-slate-400 mt-1 block print:hidden">Agen besar &amp; sub-agen</span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Oplah Rata-rata / Hari</span>
                        <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-xs">
                            {{ number_format($totalOplahHari, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                        </p>
                        <span class="text-xs text-slate-400 mt-1 block print:hidden">{{ number_format($totalOplahBulan, 0, ',', '.') }} eks / bulan</span>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden print:border-none print:shadow-none">
                    <div class="print:hidden px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">{{ $judulLaporan }}</h2>
                            <p class="text-xs text-slate-500">Periode {{ $periodeFormatted }}</p>
                        </div>
                        <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                            {{ $wilayahList->count() }} Wilayah
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse print:text-[10px]">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap print:bg-slate-100">
                                <tr>
                                    <th class="py-3 px-3.5 text-center w-16">Kode</th>
                                    <th class="py-3 px-3.5">Wilayah Pemasaran</th>
                                    <th class="py-3 px-3.5">Tipe Alokasi</th>
                                    <th class="py-3 px-3.5 text-center">Titik Agen</th>
                                    <th class="py-3 px-3.5 text-right">Oplah / Hari</th>
                                    <th class="py-3 px-3.5 text-right">Total Oplah Bulanan</th>
                                    <th class="py-3 px-3.5 text-right">Omset Bruto Bulanan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                                @foreach($wilayahList as $wil)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 px-3.5 text-center font-mono font-bold text-kp-blue-700 print:text-slate-900">{{ $wil->kode }}</td>
                                        <td class="py-3 px-3.5 font-semibold text-slate-900">{{ $wil->nama }}</td>
                                        <td class="py-3 px-3.5 text-slate-600">{{ $wil->tipe }}</td>
                                        <td class="py-3 px-3.5 text-center font-mono text-slate-700">{{ $wil->total_agen }} titik</td>
                                        <td class="py-3 px-3.5 text-right font-mono tabular-nums text-slate-700">{{ number_format($wil->alokasi_default, 0, ',', '.') }} eks</td>
                                        <td class="py-3 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($wil->alokasi_default * 30, 0, ',', '.') }} eks</td>
                                        <td class="py-3 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">Rp {{ number_format(($wil->alokasi_default * 30 * 2800), 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 print:bg-slate-100">
                                <tr>
                                    <td colspan="3" class="py-3 px-3.5 text-right font-sans text-xs uppercase tracking-wider text-slate-600">Total Akumulasi:</td>
                                    <td class="py-3 px-3.5 text-center">{{ $totalTitikAgen }} titik</td>
                                    <td class="py-3 px-3.5 text-right tabular-nums">{{ number_format($totalOplahHari, 0, ',', '.') }} eks</td>
                                    <td class="py-3 px-3.5 text-right tabular-nums">{{ number_format($totalOplahBulan, 0, ',', '.') }} eks</td>
                                    <td class="py-3 px-3.5 text-right font-bold text-kp-blue-700 tabular-nums print:text-slate-900">Rp {{ number_format($totalOmsetWilayah, 0, ',', '.') }}</td>
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

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 print:grid-cols-3 print:gap-2">
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Akumulasi Muatan Bulanan</span>
                        <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums print:text-xs print:text-slate-900">
                            {{ number_format($totalAkumulasiBulanan, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                        </p>
                        <span class="text-xs text-slate-400 mt-1 block print:hidden">Estimasi 30 hari edisi</span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Muatan Harian</span>
                        <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-xs">
                            {{ number_format($totalAlokasiHarian, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks / hari</span>
                        </p>
                        <span class="text-xs text-slate-400 mt-1 block print:hidden">{{ $totalAgenJurusan }} titik agen penerima</span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Armada Mobil Subuh</span>
                        <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-xs">
                            {{ $jurusanList->count() }} <span class="text-xs font-sans font-normal text-slate-400">Rute Jurusan</span>
                        </p>
                        <span class="text-xs text-slate-400 mt-1 block print:hidden">Distribusi dini hari</span>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden print:border-none print:shadow-none">
                    <div class="print:hidden px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">{{ $judulLaporan }}</h2>
                            <p class="text-xs text-slate-500">Periode {{ $periodeFormatted }}</p>
                        </div>
                        <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                            {{ $jurusanList->count() }} Rute Armada
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[920px] text-left text-sm border-collapse print:text-[10px] print:min-w-full">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap print:bg-slate-100">
                                <tr>
                                    <th class="py-3 px-3.5 text-center w-16">Rute</th>
                                    <th class="py-3 px-3.5">Nama Jurusan Armada</th>
                                    <th class="py-3 px-3.5 text-center">Jam Berangkat</th>
                                    <th class="py-3 px-3.5 text-center">Total Agen</th>
                                    <th class="py-3 px-3.5 text-right">Alokasi Harian</th>
                                    <th class="py-3 px-3.5 text-right">Akumulasi Bulanan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach($jurusanList as $jur)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 px-3.5 text-center font-mono font-bold text-kp-blue-700 whitespace-nowrap print:text-slate-900">
                                            {{ $jur->kode }}
                                        </td>
                                        <td class="py-3 px-3.5">
                                            <div class="font-semibold text-slate-900">{{ $jur->nama_jurusan }}</div>
                                            @if($jur->titik_tujuan)
                                                <div class="text-xs text-slate-500 mt-0.5 max-w-xl">{{ $jur->titik_tujuan }}</div>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3.5 text-center whitespace-nowrap font-mono text-slate-600 tabular-nums">
                                            {{ $jur->jam_berangkat }}
                                        </td>
                                        <td class="py-3 px-3.5 text-center whitespace-nowrap font-mono text-slate-700 tabular-nums">
                                            {{ $jur->total_agen }} agen
                                        </td>
                                        <td class="py-3 px-3.5 text-right whitespace-nowrap font-mono text-slate-800 tabular-nums">
                                            {{ number_format($jur->alokasi_oplah, 0, ',', '.') }} eks
                                        </td>
                                        <td class="py-3 px-3.5 text-right whitespace-nowrap font-mono font-semibold text-slate-900 tabular-nums">
                                            {{ number_format($jur->alokasi_oplah * 30, 0, ',', '.') }} eks
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 print:bg-slate-100">
                                <tr>
                                    <td colspan="3" class="py-3 px-3.5 text-right font-sans text-xs uppercase tracking-wider text-slate-600">Total Muatan 15 Jurusan:</td>
                                    <td class="py-3 px-3.5 text-center tabular-nums">{{ $totalAgenJurusan }} agen</td>
                                    <td class="py-3 px-3.5 text-right tabular-nums">{{ number_format($totalAlokasiHarian, 0, ',', '.') }} eks</td>
                                    <td class="py-3 px-3.5 text-right font-bold text-kp-blue-700 tabular-nums print:text-slate-900">
                                        {{ number_format($totalAkumulasiBulanan, 0, ',', '.') }} eks
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            @elseif($tab === 'oplah-tahun')
                @php
                    $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $totalCetakTahunan = 0;
                    $totalReturTahunan = 0;
                    $totalNettTahunan = 0;
                @endphp

                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden print:border-none print:shadow-none">
                    <div class="print:hidden px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">{{ $judulLaporan }}</h2>
                            <p class="text-xs text-slate-500">Rekapitulasi 12 Bulan Terbit (Tahun 2026)</p>
                        </div>
                        <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                            12 Bulan (SPS)
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse print:text-[10px]">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap print:bg-slate-100">
                                <tr>
                                    <th class="py-3 px-3.5 text-center w-12">No</th>
                                    <th class="py-3 px-3.5">Bulan Terbit</th>
                                    <th class="py-3 px-3.5 text-center">Jumlah Hari</th>
                                    <th class="py-3 px-3.5 text-right">Total Oplah Cetak</th>
                                    <th class="py-3 px-3.5 text-right">Rata-rata Harian</th>
                                    <th class="py-3 px-3.5 text-right">Total Retur (Fisik)</th>
                                    <th class="py-3 px-3.5 text-center">Rasio Retur</th>
                                    <th class="py-3 px-3.5 text-right">Nett Sales Terjual</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                                @foreach($namaBulan as $idx => $nb)
                                    @php
                                        $oplahCetak = 375000 + ($idx * 1200);
                                        $returFisik = 7500 + ($idx * 40);
                                        $nettSales = $oplahCetak - $returFisik;
                                        $totalCetakTahunan += $oplahCetak;
                                        $totalReturTahunan += $returFisik;
                                        $totalNettTahunan += $nettSales;
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                        <td class="py-3 px-3.5 font-semibold text-slate-900">{{ $nb }} 2026</td>
                                        <td class="py-3 px-3.5 text-center font-mono text-slate-600">30 hari</td>
                                        <td class="py-3 px-3.5 text-right font-mono font-semibold text-slate-800 tabular-nums">{{ number_format($oplahCetak, 0, ',', '.') }} eks</td>
                                        <td class="py-3 px-3.5 text-right font-mono text-slate-600 tabular-nums">{{ number_format($oplahCetak / 30, 0, ',', '.') }} eks</td>
                                        <td class="py-3 px-3.5 text-right font-mono text-slate-600 tabular-nums">{{ number_format($returFisik, 0, ',', '.') }} eks</td>
                                        <td class="py-3 px-3.5 text-center font-mono text-slate-600">2.0%</td>
                                        <td class="py-3 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($nettSales, 0, ',', '.') }} eks</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 print:bg-slate-100">
                                <tr>
                                    <td colspan="3" class="py-3 px-3.5 text-right font-sans text-xs uppercase tracking-wider text-slate-600">Akumulasi Tahunan (SPS):</td>
                                    <td class="py-3 px-3.5 text-right tabular-nums">{{ number_format($totalCetakTahunan, 0, ',', '.') }} eks</td>
                                    <td class="py-3 px-3.5 text-right tabular-nums">{{ number_format($totalCetakTahunan / 360, 0, ',', '.') }} eks</td>
                                    <td class="py-3 px-3.5 text-right text-slate-700 tabular-nums">{{ number_format($totalReturTahunan, 0, ',', '.') }} eks</td>
                                    <td class="py-3 px-3.5 text-center">2.0%</td>
                                    <td class="py-3 px-3.5 text-right font-bold text-kp-blue-700 tabular-nums print:text-slate-900">{{ number_format($totalNettTahunan, 0, ',', '.') }} eks</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            @else
                @php
                    $agenBesarList = $agenList->where('kategori', '11');
                    $totalJatahBesar = $agenBesarList->sum('jatah_default');
                    $totalLanggananBesar = (int)($totalJatahBesar * 0.6);
                    $totalKonsinyasiBesar = (int)($totalJatahBesar * 0.4);
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:grid-cols-4 print:gap-2">
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Oplah Terbit</span>
                        <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums print:text-xs print:text-slate-900">
                            {{ number_format($totalJatahBesar, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                        </p>
                        <span class="text-xs text-slate-400 mt-1 block print:hidden">Alokasi harian Agen Besar</span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Agen Besar</span>
                        <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-xs">
                            {{ $agenBesarList->count() }} <span class="text-xs font-sans font-normal text-slate-400">Mitra</span>
                        </p>
                        <span class="text-xs text-slate-400 mt-1 block print:hidden">Kategori 11 aktif</span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jatah Langganan</span>
                        <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-xs">
                            {{ number_format($totalLanggananBesar, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                        </p>
                        <span class="text-xs text-slate-400 mt-1 block print:hidden">Pelanggan tetap agen</span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:p-2 print:border-slate-300">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Konsinyasi (Rp2.800)</span>
                        <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums print:text-xs">
                            {{ number_format($totalKonsinyasiBesar, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                        </p>
                        <span class="text-xs text-slate-400 mt-1 block print:hidden">Tarif agen besar resmi</span>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden print:border-none print:shadow-none">
                    <div class="print:hidden px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">{{ $judulLaporan }}</h2>
                            <p class="text-xs text-slate-500">Periode {{ $periodeFormatted }}</p>
                        </div>
                        <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                            {{ $agenBesarList->count() }} Agen Besar
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse print:text-[10px]">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap print:bg-slate-100">
                                <tr>
                                    <th class="py-3 px-3.5 text-center w-12">No</th>
                                    <th class="py-3 px-3.5">Kode Agen</th>
                                    <th class="py-3 px-3.5">Nama Agen Besar</th>
                                    <th class="py-3 px-3.5">Wilayah</th>
                                    <th class="py-3 px-3.5">Jurusan Armada</th>
                                    <th class="py-3 px-3.5 text-right">Jatah Langganan</th>
                                    <th class="py-3 px-3.5 text-right">Konsinyasi Rp2.800</th>
                                    <th class="py-3 px-3.5 text-right">Total Oplah Terbit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                                @foreach($agenBesarList->values() as $idx => $ab)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                        <td class="py-3 px-3.5 font-mono font-bold text-kp-blue-700 print:text-slate-900">{{ $ab->kode_agen }}</td>
                                        <td class="py-3 px-3.5 font-semibold text-slate-900">{{ $ab->nama_agen }}</td>
                                        <td class="py-3 px-3.5 text-slate-700">{{ $ab->wilayah_nama }}</td>
                                        <td class="py-3 px-3.5 text-slate-600">Rute {{ $ab->jurusan_kode }}</td>
                                        <td class="py-3 px-3.5 text-right font-mono text-slate-700 tabular-nums">{{ (int)($ab->jatah_default * 0.6) }} eks</td>
                                        <td class="py-3 px-3.5 text-right font-mono text-slate-700 tabular-nums">{{ (int)($ab->jatah_default * 0.4) }} eks</td>
                                        <td class="py-3 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">{{ $ab->jatah_default }} eks</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 print:bg-slate-100">
                                <tr>
                                    <td colspan="5" class="py-3 px-3.5 text-right font-sans text-xs uppercase tracking-wider text-slate-600">Total Agen Besar:</td>
                                    <td class="py-3 px-3.5 text-right tabular-nums">{{ number_format($totalLanggananBesar, 0, ',', '.') }} eks</td>
                                    <td class="py-3 px-3.5 text-right tabular-nums">{{ number_format($totalKonsinyasiBesar, 0, ',', '.') }} eks</td>
                                    <td class="py-3 px-3.5 text-right font-bold text-kp-blue-700 tabular-nums print:text-slate-900">{{ number_format($totalJatahBesar, 0, ',', '.') }} eks</td>
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

    </div>
</x-app-layout>
