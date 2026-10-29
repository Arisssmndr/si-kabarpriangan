<x-app-layout>
    <style>
        @media print {
            header,
            footer,
            nav,
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

    <div class="space-y-6">

        <!-- Header Halaman -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-teal-600 uppercase tracking-wider">Menu Cetak</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Laporan Pemasaran Sirkulasi</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Laporan pemasaran terpadu: Distribusi Agen Besar, Sub Agen, Rekapitulasi per Wilayah, Jurusan Armada, & Audit Oplah Tahunan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('sirkulasi.cetak.pemasaran') }}" class="flex items-center gap-2">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <input type="month" name="bulan" value="{{ $bulan }}" class="text-xs rounded-lg border-slate-200 py-1.5 focus:border-teal-500 focus:ring-teal-500">
                    <button type="submit" class="py-1.5 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition-colors cursor-pointer">
                        Filter Periode
                    </button>
                </form>

                <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-teal-50 text-teal-800 border border-teal-200/80">
                    Format: A4 Landscape
                </span>

                <button type="button" onclick="window.print()" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-teal-700 hover:bg-teal-800 text-white shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak Laporan</span>
                </button>
            </div>
        </div>

        <!-- 5 Format Utama Laporan Pemasaran (Format Redundan / Kosong Telah Dieliminasi) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-2 shadow-xs overflow-x-auto">
            <div class="flex items-center gap-1 min-w-max">
                <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'agen-besar', 'bulan' => $bulan]) }}" 
                   class="px-3 py-1.5 text-xs rounded-lg font-semibold transition-colors {{ ($tab ?? 'agen-besar') === 'agen-besar' ? 'bg-teal-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    1. Agen Besar / Terbit
                </a>
                <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'sub-agen', 'bulan' => $bulan]) }}" 
                   class="px-3 py-1.5 text-xs rounded-lg font-semibold transition-colors {{ ($tab ?? '') === 'sub-agen' ? 'bg-teal-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    2. Sub Agen & Kios
                </a>
                <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'rekap-wilayah', 'bulan' => $bulan]) }}" 
                   class="px-3 py-1.5 text-xs rounded-lg font-semibold transition-colors {{ ($tab ?? '') === 'rekap-wilayah' ? 'bg-teal-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    3. Rekapitulasi per Wilayah
                </a>
                <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'rekap-jurusan', 'bulan' => $bulan]) }}" 
                   class="px-3 py-1.5 text-xs rounded-lg font-semibold transition-colors {{ ($tab ?? '') === 'rekap-jurusan' ? 'bg-teal-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    4. Rekapitulasi per Jurusan Armada
                </a>
                <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'oplah-tahun', 'bulan' => $bulan]) }}" 
                   class="px-3 py-1.5 text-xs rounded-lg font-semibold transition-colors {{ ($tab ?? '') === 'oplah-tahun' ? 'bg-teal-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    5. Audit Oplah Tahunan (Tiras SPS)
                </a>
            </div>
        </div>

        <!-- Konten Laporan Terpilih -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden p-6 printable-area print:border-none print:shadow-none print:p-0 print:rounded-none">
            
            <!-- Kop Cetak Dokumen Resmi -->
            <div class="border-b-2 border-slate-900 pb-4 mb-5 text-center">
                <h2 class="text-base font-extrabold uppercase tracking-tight text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                <h3 class="text-xs font-bold uppercase text-slate-700 mt-0.5">DIVISI SIRKULASI &bull; SISTEM INFORMASI SIRKULASI (SIRK.EXE)</h3>
                <p class="text-[11px] text-slate-500 mt-1">
                    Jl. Sutisna Senjaya No. 120 Tasikmalaya &bull; Telepon: (0265) 331888 &bull; Priangan Timur
                </p>
                <div class="mt-3 inline-block px-3 py-1 rounded bg-slate-100 font-bold text-xs uppercase text-slate-800">
                    @if($tab === 'sub-agen')
                        LAPORAN PEMASARAN SUB AGEN & KIOS PER TERBIT
                    @elseif($tab === 'rekap-wilayah')
                        REKAPITULASI PEMASARAN PER WILAYAH (01 s.d. 08 SE-PRIANGAN TIMUR)
                    @elseif($tab === 'rekap-jurusan')
                        REKAPITULASI DISTRIBUSI PENGIRIMAN PER JURUSAN (15 RUTE ARMADA SUBUH)
                    @elseif($tab === 'oplah-tahun')
                        AUDIT TIRAS & REKAP OPLAH CETAK PER TAHUN (STANDAR SPS INDONESIA)
                    @else
                        LAPORAN PEMASARAN AGEN BESAR PER TERBIT
                    @endif
                    &bull; PERIODE {{ strtoupper(\Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y')) }}
                </div>
            </div>

            <!-- Tabel Data Berdasarkan Tab -->
            @if($tab === 'sub-agen')
                <!-- 2. Sub Agen -->
                @php
                    $subAgenList = $agenList->where('kategori', '99');
                    $totalAlokasiSub = $subAgenList->sum('jatah_default');
                    $totalKonsinyasiSub = (int)($totalAlokasiSub * 0.8);
                    $totalTagihanSub = $totalKonsinyasiSub * 3000 * 30;
                @endphp

                <!-- KPI Mini -->
                <div class="grid grid-cols-3 gap-3 mb-4 print:grid print:grid-cols-3 print:gap-2 print:mb-3">
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 print:p-2 print:border-slate-300">
                        <div class="text-[10px] font-semibold text-slate-500 uppercase">Total Sub Agen / Kios</div>
                        <div class="text-base font-bold text-slate-900 mt-0.5 print:text-xs">{{ $subAgenList->count() }} Mitra</div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 print:p-2 print:border-slate-300">
                        <div class="text-[10px] font-semibold text-slate-500 uppercase">Alokasi Harian Sub Agen</div>
                        <div class="text-base font-bold text-slate-900 mt-0.5 print:text-xs">{{ number_format($totalAlokasiSub, 0, ',', '.') }} eks</div>
                    </div>
                    <div class="p-3 bg-teal-50/60 rounded-lg border border-teal-200 print:p-2 print:border-slate-300 print:bg-slate-50">
                        <div class="text-[10px] font-semibold text-teal-800 uppercase">Estimasi Omset Bulanan</div>
                        <div class="text-base font-bold text-teal-900 mt-0.5 print:text-xs">Rp {{ number_format($totalTagihanSub, 0, ',', '.') }}</div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700 print:text-[10px]">
                        <thead class="bg-slate-100/75 border-y border-slate-300 font-bold uppercase text-[10px] print:bg-slate-100">
                            <tr>
                                <th class="py-2.5 px-3">Kode Sub Agen</th>
                                <th class="py-2.5 px-3">Nama Sub Agen / Kios</th>
                                <th class="py-2.5 px-3">Induk Agen Besar</th>
                                <th class="py-2.5 px-3">Wilayah</th>
                                <th class="py-2.5 px-3 text-right">Alokasi Harian</th>
                                <th class="py-2.5 px-3 text-right">Konsinyasi (Rp3.000)</th>
                                <th class="py-2.5 px-3 text-right">Estimasi Tagihan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($subAgenList as $sub)
                                <tr>
                                    <td class="py-2 px-3 font-mono font-bold">{{ $sub->kode_agen }}</td>
                                    <td class="py-2 px-3 font-semibold">{{ $sub->nama_agen }}</td>
                                    <td class="py-2 px-3 text-slate-600">{{ $sub->induk_agen }}</td>
                                    <td class="py-2 px-3">{{ $sub->wilayah_nama }}</td>
                                    <td class="py-2 px-3 text-right font-bold font-mono">{{ $sub->jatah_default }} eks</td>
                                    <td class="py-2 px-3 text-right font-mono">{{ (int)($sub->jatah_default * 0.8) }} eks</td>
                                    <td class="py-2 px-3 text-right font-bold text-slate-900 font-mono">Rp {{ number_format((int)($sub->jatah_default * 0.8) * 3000 * 30, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-100 font-bold text-slate-800 border-t-2 border-slate-300 print:bg-slate-100">
                            <tr>
                                <td colspan="4" class="py-2 px-3 text-right uppercase text-[10px]">Total:</td>
                                <td class="py-2 px-3 text-right font-mono font-bold">{{ number_format($totalAlokasiSub, 0, ',', '.') }} eks</td>
                                <td class="py-2 px-3 text-right font-mono font-bold">{{ number_format($totalKonsinyasiSub, 0, ',', '.') }} eks</td>
                                <td class="py-2 px-3 text-right font-mono font-extrabold text-teal-900">Rp {{ number_format($totalTagihanSub, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            @elseif($tab === 'rekap-wilayah')
                <!-- 3. Rekap Wilayah -->
                @php
                    $totalTitikAgen = $wilayahList->sum('total_agen');
                    $totalOplahHari = $wilayahList->sum('alokasi_default');
                    $totalOplahBulan = $totalOplahHari * 30;
                    $totalOmsetWilayah = $totalOplahBulan * 2800;
                @endphp

                <!-- KPI Mini -->
                <div class="grid grid-cols-4 gap-3 mb-4 print:grid print:grid-cols-4 print:gap-2 print:mb-3">
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 print:p-2 print:border-slate-300">
                        <div class="text-[10px] font-semibold text-slate-500 uppercase">Total Wilayah</div>
                        <div class="text-base font-bold text-slate-900 mt-0.5 print:text-xs">{{ $wilayahList->count() }} Wilayah</div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 print:p-2 print:border-slate-300">
                        <div class="text-[10px] font-semibold text-slate-500 uppercase">Total Titik Distribusi</div>
                        <div class="text-base font-bold text-slate-900 mt-0.5 print:text-xs">{{ $totalTitikAgen }} Titik Agen</div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 print:p-2 print:border-slate-300">
                        <div class="text-[10px] font-semibold text-slate-500 uppercase">Oplah Rata-rata / Hari</div>
                        <div class="text-base font-bold text-slate-900 mt-0.5 print:text-xs">{{ number_format($totalOplahHari, 0, ',', '.') }} eks</div>
                    </div>
                    <div class="p-3 bg-teal-50/60 rounded-lg border border-teal-200 print:p-2 print:border-slate-300 print:bg-slate-50">
                        <div class="text-[10px] font-semibold text-teal-800 uppercase">Omset Bruto Bulanan</div>
                        <div class="text-base font-bold text-teal-900 mt-0.5 print:text-xs">Rp {{ number_format($totalOmsetWilayah, 0, ',', '.') }}</div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700 print:text-[10px]">
                        <thead class="bg-slate-100/75 border-y border-slate-300 font-bold uppercase text-[10px] print:bg-slate-100">
                            <tr>
                                <th class="py-2.5 px-3 text-center">Kode</th>
                                <th class="py-2.5 px-4">Wilayah Pemasaran</th>
                                <th class="py-2.5 px-4">Tipe Alokasi</th>
                                <th class="py-2.5 px-4 text-center">Titik Agen</th>
                                <th class="py-2.5 px-4 text-right">Oplah Rata-rata / Hari</th>
                                <th class="py-2.5 px-4 text-right">Total Oplah Bulanan</th>
                                <th class="py-2.5 px-4 text-right">Omset Bruto Bulanan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($wilayahList as $wil)
                                <tr>
                                    <td class="py-2 px-3 text-center font-mono font-bold">{{ $wil->kode }}</td>
                                    <td class="py-2 px-4 font-bold">{{ $wil->nama }}</td>
                                    <td class="py-2 px-4">{{ $wil->tipe }}</td>
                                    <td class="py-2 px-4 text-center font-bold">{{ $wil->total_agen }} titik</td>
                                    <td class="py-2 px-4 text-right font-mono">{{ number_format($wil->alokasi_default, 0, ',', '.') }} eks</td>
                                    <td class="py-2 px-4 text-right font-bold text-slate-900 font-mono">{{ number_format($wil->alokasi_default * 30, 0, ',', '.') }} eks</td>
                                    <td class="py-2 px-4 text-right font-bold text-teal-800 font-mono">Rp {{ number_format(($wil->alokasi_default * 30 * 2800), 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-100 font-bold text-slate-800 border-t-2 border-slate-300 print:bg-slate-100">
                            <tr>
                                <td colspan="3" class="py-2 px-4 text-right uppercase text-[10px]">Total Akumulasi:</td>
                                <td class="py-2 px-4 text-center font-bold">{{ $totalTitikAgen }} titik</td>
                                <td class="py-2 px-4 text-right font-mono">{{ number_format($totalOplahHari, 0, ',', '.') }} eks</td>
                                <td class="py-2 px-4 text-right font-mono font-bold">{{ number_format($totalOplahBulan, 0, ',', '.') }} eks</td>
                                <td class="py-2 px-4 text-right font-mono font-extrabold text-teal-900">Rp {{ number_format($totalOmsetWilayah, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            @elseif($tab === 'rekap-jurusan')
                <!-- 4. Rekap Jurusan -->
                @php
                    $totalAgenJurusan = $jurusanList->sum('total_agen');
                    $totalAlokasiHarian = $jurusanList->sum('alokasi_oplah');
                    $totalAkumulasiBulanan = $totalAlokasiHarian * 30;
                @endphp

                <!-- KPI Mini -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4 print:grid print:grid-cols-3 print:gap-2 print:mb-3">
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 print:p-2 print:border-slate-300">
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Armada Mobil Subuh</div>
                        <div class="text-base font-extrabold text-slate-900 mt-1 print:text-xs">{{ $jurusanList->count() }} Rute Jurusan</div>
                    </div>
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 print:p-2 print:border-slate-300">
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Muatan Harian</div>
                        <div class="text-base font-extrabold text-slate-900 mt-1 print:text-xs">{{ number_format($totalAlokasiHarian, 0, ',', '.') }} eks / hari</div>
                    </div>
                    <div class="p-3.5 bg-teal-50/60 rounded-xl border border-teal-200 print:p-2 print:border-slate-300 print:bg-slate-50">
                        <div class="text-[10px] font-bold text-teal-800 uppercase tracking-wider">Akumulasi Muatan Bulanan</div>
                        <div class="text-base font-extrabold text-teal-950 mt-1 print:text-xs">{{ number_format($totalAkumulasiBulanan, 0, ',', '.') }} eks</div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[980px] text-left text-xs text-slate-700 print:text-[10px] print:min-w-full">
                        <thead class="bg-slate-100/75 border-y border-slate-300 font-bold uppercase text-[10px] tracking-wider print:bg-slate-100">
                            <tr>
                                <th class="py-3 px-3 text-center whitespace-nowrap min-w-[65px] w-16">Rute</th>
                                <th class="py-3 px-4 whitespace-nowrap min-w-[280px]">Nama Jurusan Armada</th>
                                <th class="py-3 px-4 text-center whitespace-nowrap min-w-[140px]">Jam Berangkat</th>
                                <th class="py-3 px-4 text-center whitespace-nowrap min-w-[110px]">Total Agen</th>
                                <th class="py-3 px-4 text-right whitespace-nowrap min-w-[130px]">Alokasi Harian</th>
                                <th class="py-3 px-4 text-right whitespace-nowrap min-w-[150px]">Akumulasi Bulanan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($jurusanList as $jur)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center justify-center w-7 h-7 text-xs font-mono font-extrabold rounded-lg bg-teal-50 text-teal-800 border border-teal-200/80 shadow-2xs print:border-none print:bg-transparent">
                                            {{ $jur->kode }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-4">
                                        <div class="font-bold text-slate-900 text-xs tracking-tight whitespace-nowrap">
                                            {{ $jur->nama_jurusan }}
                                        </div>
                                        @if($jur->titik_tujuan)
                                            <div class="text-[11px] text-slate-500 font-normal mt-0.5 leading-relaxed max-w-xl">
                                                {{ $jur->titik_tujuan }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-50 border border-slate-200/90 font-mono text-[11px] font-bold text-slate-700 tabular-nums print:border-none print:bg-transparent print:p-0">
                                            <svg class="w-3 h-3 text-amber-500 shrink-0 print:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            <span>{{ $jur->jam_berangkat }}</span>
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-4 text-center whitespace-nowrap font-mono tabular-nums">
                                        <span class="font-bold text-slate-900 text-xs">{{ $jur->total_agen }}</span> 
                                        <span class="text-[10px] text-slate-400 font-normal">agen</span>
                                    </td>
                                    <td class="py-2.5 px-4 text-right whitespace-nowrap font-mono tabular-nums">
                                        <span class="font-bold text-slate-900 text-xs">{{ number_format($jur->alokasi_oplah, 0, ',', '.') }}</span> 
                                        <span class="text-[10px] text-slate-400 font-normal">eks</span>
                                    </td>
                                    <td class="py-2.5 px-4 text-right whitespace-nowrap font-mono tabular-nums">
                                        <span class="font-extrabold text-teal-950 text-xs">{{ number_format($jur->alokasi_oplah * 30, 0, ',', '.') }}</span> 
                                        <span class="text-[10px] text-teal-700/70 font-semibold">eks</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50/90 font-bold text-slate-800 border-t-2 border-slate-300 print:bg-slate-100 text-xs">
                            <tr>
                                <td colspan="3" class="py-3 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-teal-500 print:hidden"></span>
                                        <span class="uppercase tracking-wider text-[11px] font-extrabold text-slate-800">Total Muatan 15 Jurusan Armada</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap font-mono font-bold text-slate-900 tabular-nums">
                                    {{ $totalAgenJurusan }} <span class="text-[10px] font-normal text-slate-500">agen</span>
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap font-mono font-bold text-slate-900 tabular-nums">
                                    {{ number_format($totalAlokasiHarian, 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">eks</span>
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap font-mono font-extrabold text-teal-900 tabular-nums">
                                    {{ number_format($totalAkumulasiBulanan, 0, ',', '.') }} <span class="text-[10px] font-normal text-teal-700/70">eks</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            @elseif($tab === 'oplah-tahun')
                <!-- 5. Oplah per Tahun -->
                @php
                    $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $totalCetakTahunan = 0;
                    $totalReturTahunan = 0;
                    $totalNettTahunan = 0;
                @endphp

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700 print:text-[10px]">
                        <thead class="bg-slate-100/75 border-y border-slate-300 font-bold uppercase text-[10px] print:bg-slate-100">
                            <tr>
                                <th class="py-2.5 px-4">Bulan Terbit</th>
                                <th class="py-2.5 px-4 text-center">Jumlah Hari</th>
                                <th class="py-2.5 px-4 text-right">Total Oplah Cetak</th>
                                <th class="py-2.5 px-4 text-right">Rata-rata Oplah Harian</th>
                                <th class="py-2.5 px-4 text-right text-rose-600">Total Retur (Fisik)</th>
                                <th class="py-2.5 px-4 text-center">Rasio Retur</th>
                                <th class="py-2.5 px-4 text-right">Nett Sales Terjual</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 font-medium">
                            @foreach($namaBulan as $idx => $nb)
                                @php
                                    $oplahCetak = 375000 + ($idx * 1200);
                                    $returFisik = 7500 + ($idx * 40);
                                    $nettSales = $oplahCetak - $returFisik;
                                    $totalCetakTahunan += $oplahCetak;
                                    $totalReturTahunan += $returFisik;
                                    $totalNettTahunan += $nettSales;
                                @endphp
                                <tr>
                                    <td class="py-2 px-4 font-bold">{{ $nb }} 2026</td>
                                    <td class="py-2 px-4 text-center font-mono">30 hari</td>
                                    <td class="py-2 px-4 text-right font-bold font-mono">{{ number_format($oplahCetak, 0, ',', '.') }} eks</td>
                                    <td class="py-2 px-4 text-right font-mono">{{ number_format($oplahCetak / 30, 0, ',', '.') }} eks</td>
                                    <td class="py-2 px-4 text-right text-rose-600 font-mono">{{ number_format($returFisik, 0, ',', '.') }} eks</td>
                                    <td class="py-2 px-4 text-center font-mono">2.0%</td>
                                    <td class="py-2 px-4 text-right font-bold text-teal-800 font-mono">{{ number_format($nettSales, 0, ',', '.') }} eks</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-100 font-bold text-slate-800 border-t-2 border-slate-300 print:bg-slate-100">
                            <tr>
                                <td colspan="2" class="py-2.5 px-4 text-right uppercase text-[10px]">Akumulasi Tahunan (SPS):</td>
                                <td class="py-2.5 px-4 text-right font-mono font-bold">{{ number_format($totalCetakTahunan, 0, ',', '.') }} eks</td>
                                <td class="py-2.5 px-4 text-right font-mono">{{ number_format($totalCetakTahunan / 360, 0, ',', '.') }} eks</td>
                                <td class="py-2.5 px-4 text-right text-rose-600 font-mono font-bold">{{ number_format($totalReturTahunan, 0, ',', '.') }} eks</td>
                                <td class="py-2.5 px-4 text-center font-mono">2.0%</td>
                                <td class="py-2.5 px-4 text-right font-mono font-extrabold text-teal-900">{{ number_format($totalNettTahunan, 0, ',', '.') }} eks</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            @else
                <!-- Default: 1. Agen Besar per Terbit -->
                @php
                    $agenBesarList = $agenList->where('kategori', '11');
                    $totalJatahBesar = $agenBesarList->sum('jatah_default');
                    $totalLanggananBesar = (int)($totalJatahBesar * 0.6);
                    $totalKonsinyasiBesar = (int)($totalJatahBesar * 0.4);
                @endphp

                <!-- KPI Mini -->
                <div class="grid grid-cols-4 gap-3 mb-4 print:grid print:grid-cols-4 print:gap-2 print:mb-3">
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 print:p-2 print:border-slate-300">
                        <div class="text-[10px] font-semibold text-slate-500 uppercase">Total Agen Besar</div>
                        <div class="text-base font-bold text-slate-900 mt-0.5 print:text-xs">{{ $agenBesarList->count() }} Mitra</div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 print:p-2 print:border-slate-300">
                        <div class="text-[10px] font-semibold text-slate-500 uppercase">Jatah Langganan</div>
                        <div class="text-base font-bold text-slate-900 mt-0.5 print:text-xs">{{ number_format($totalLanggananBesar, 0, ',', '.') }} eks</div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 print:p-2 print:border-slate-300">
                        <div class="text-[10px] font-semibold text-emerald-600 uppercase">Konsinyasi (Rp2.800)</div>
                        <div class="text-base font-bold text-emerald-700 mt-0.5 print:text-xs">{{ number_format($totalKonsinyasiBesar, 0, ',', '.') }} eks</div>
                    </div>
                    <div class="p-3 bg-teal-50/60 rounded-lg border border-teal-200 print:p-2 print:border-slate-300 print:bg-slate-50">
                        <div class="text-[10px] font-semibold text-teal-800 uppercase">Total Oplah Terbit</div>
                        <div class="text-base font-bold text-teal-900 mt-0.5 print:text-xs">{{ number_format($totalJatahBesar, 0, ',', '.') }} eks</div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700 print:text-[10px]">
                        <thead class="bg-slate-100/75 border-y border-slate-300 font-bold uppercase text-[10px] print:bg-slate-100">
                            <tr>
                                <th class="py-2.5 px-3">Kode Agen</th>
                                <th class="py-2.5 px-4">Nama Agen Besar</th>
                                <th class="py-2.5 px-3">Wilayah</th>
                                <th class="py-2.5 px-3">Jurusan Armada</th>
                                <th class="py-2.5 px-3 text-right">Jatah Langganan</th>
                                <th class="py-2.5 px-3 text-right text-emerald-800">Konsinyasi Rp2.800</th>
                                <th class="py-2.5 px-3 text-right font-extrabold text-slate-900">Total Oplah Terbit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($agenBesarList as $ab)
                                <tr>
                                    <td class="py-2.5 px-3 font-mono font-bold">{{ $ab->kode_agen }}</td>
                                    <td class="py-2.5 px-4 font-bold text-slate-900">{{ $ab->nama_agen }}</td>
                                    <td class="py-2.5 px-3">{{ $ab->wilayah_nama }}</td>
                                    <td class="py-2.5 px-3 text-[11px] text-slate-600">Rute {{ $ab->jurusan_kode }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono">{{ (int)($ab->jatah_default * 0.6) }} eks</td>
                                    <td class="py-2.5 px-3 text-right font-semibold text-emerald-700 font-mono">{{ (int)($ab->jatah_default * 0.4) }} eks</td>
                                    <td class="py-2.5 px-3 text-right font-bold text-slate-900 font-mono">{{ $ab->jatah_default }} eks</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-100 font-bold text-slate-800 border-t-2 border-slate-300 print:bg-slate-100">
                            <tr>
                                <td colspan="4" class="py-2 px-3 text-right uppercase text-[10px]">Total Agen Besar:</td>
                                <td class="py-2 px-3 text-right font-mono font-bold">{{ number_format($totalLanggananBesar, 0, ',', '.') }} eks</td>
                                <td class="py-2 px-3 text-right font-mono font-bold text-emerald-800">{{ number_format($totalKonsinyasiBesar, 0, ',', '.') }} eks</td>
                                <td class="py-2 px-3 text-right font-mono font-extrabold text-slate-900">{{ number_format($totalJatahBesar, 0, ',', '.') }} eks</td>
                            </tr>
                        </tfoot>
                    </table>
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
