<x-app-layout>
    <div class="space-y-5" x-data="{
        showQrModal: false,
        activeQr: { id: '', no_faktur: '', nama_agen: '', kode_agen: '', harga_bayar: '0', wilayah: '', kirim: '0', retur: '0', nett: '0', url_cetak: '#' }
    }">

        <!-- Header Halaman (60-30-10 Palette) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Laporan Penjualan &amp; Faktur Sirkulasi
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Rekapitulasi penjualan bulanan agen sirkulasi dan validasi Kode QR faktur kasir.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('sirkulasi.cetak.faktur', ['bulan' => $bulanPilihan]) }}"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    <span>Faktur Agen &amp; QR Kasir</span>
                </a>

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

        <!-- Toolbar Filter Periode -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.cetak.penjualan') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div class="w-full sm:w-64">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Periode Bulan</label>
                    <input type="month"
                           name="bulan"
                           value="{{ $bulanPilihan }}"
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>
                <button type="submit" class="h-11 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    Filter Periode
                </button>
            </form>
        </div>

        <!-- 4 KPI Ringkasan Keuangan Sirkulasi (60-30-10 Palette) -->
        <div class="print:hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Nilai Tagihan</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Terintegrasi Kode QR Kasir</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Koran Dikirim</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalKirim, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Seluruh agen terdaftar</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Nett Sales Terjual</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalNett, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Pengiriman dikurangi retur</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Koran Diretur</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalRetur, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Rasio retur dalam batas toleransi</span>
            </div>
        </div>

        <!-- Styling Khusus Cetak Printer (Presisi 1 Halaman Landscape) -->
        <style>
            @media print {
                header,
                footer,
                nav,
                aside,
                .print\:hidden,
                .no-print,
                [x-cloak],
                .qr-modal-container {
                    display: none !important;
                }

                @page {
                    size: landscape;
                    margin: 5mm 6mm 5mm 6mm;
                }
                html, body {
                    width: 100% !important;
                    height: auto !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    background: #ffffff !important;
                    color: #000000 !important;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
                body, .min-h-screen, main, .max-w-7xl, .printable-area {
                    display: block !important;
                    width: 100% !important;
                    max-width: 100% !important;
                    min-width: 0 !important;
                    min-height: 0 !important;
                    height: auto !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    border: none !important;
                    box-shadow: none !important;
                    overflow: visible !important;
                }
                .space-y-5 > *, .space-y-6 > * {
                    margin-top: 0 !important;
                    margin-bottom: 0 !important;
                }
                .overflow-x-auto {
                    overflow: visible !important;
                    width: 100% !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
                table.table-penjualan-print {
                    width: 100% !important;
                    min-width: 100% !important;
                    max-width: 100% !important;
                    table-layout: fixed !important;
                    border-collapse: collapse !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    font-size: 7.2px !important;
                    line-height: 1.15 !important;
                }
                table.table-penjualan-print th,
                table.table-penjualan-print td {
                    border: 0.5px solid #94a3b8 !important;
                    padding: 2px 2.5px !important;
                    vertical-align: middle !important;
                    color: #000000 !important;
                    box-sizing: border-box !important;
                    word-break: break-word !important;
                }
                table.table-penjualan-print > thead,
                table.table-penjualan-print > thead tr,
                table.table-penjualan-print > thead th {
                    background-color: #f1f5f9 !important;
                    color: #000000 !important;
                    border-bottom: 1px solid #64748b !important;
                    font-weight: 800 !important;
                    font-size: 6.8px !important;
                    text-transform: uppercase !important;
                    padding: 3px 1.5px !important;
                    white-space: normal !important;
                    text-align: center !important;
                }
                table.table-penjualan-print tfoot {
                    display: table-row-group !important;
                    page-break-inside: avoid !important;
                    break-inside: avoid !important;
                }
                table.table-penjualan-print tfoot td {
                    background-color: #f8fafc !important;
                    font-weight: 800 !important;
                    border-top: 1.5px solid #000000 !important;
                    padding: 2.5px 2px !important;
                    font-size: 7.2px !important;
                }
                table.table-penjualan-print tr {
                    page-break-inside: avoid !important;
                    break-inside: avoid !important;
                }

                .col-kode    { width: 6.5% !important; }
                .col-faktur  { width: 12% !important; }
                .col-agen    { width: 19% !important; }
                .col-wilayah { width: 9% !important; }
                .col-kirim   { width: 5.5% !important; }
                .col-retur   { width: 5% !important; }
                .col-nett    { width: 6% !important; }
                .col-harga   { width: 5.5% !important; }
                .col-bayar   { width: 11% !important; }
                .col-piutang { width: 10% !important; }
                .col-status  { width: 5% !important; }
                .col-barcode { width: 5.5% !important; }

                .print-qr-box {
                    width: 30px !important;
                    height: 30px !important;
                    margin: 0 auto !important;
                    padding: 0 !important;
                    border: none !important;
                    box-shadow: none !important;
                    display: flex !important;
                    flex-direction: column !important;
                    align-items: center !important;
                    justify-content: center !important;
                }
                .print-qr-box svg {
                    width: 26px !important;
                    height: 26px !important;
                    max-width: 26px !important;
                    max-height: 26px !important;
                    shape-rendering: crispEdges !important;
                }
                .print-qr-label {
                    font-size: 5px !important;
                    font-weight: 900 !important;
                    line-height: 1 !important;
                    letter-spacing: -0.2px !important;
                    margin-top: 1px !important;
                    color: #000000 !important;
                    display: block !important;
                    text-align: center !important;
                }

                .print-header {
                    display: flex !important;
                    justify-content: space-between !important;
                    align-items: flex-end !important;
                    margin-bottom: 5px !important;
                    padding-bottom: 3px !important;
                    border-bottom: 1.5px solid #000000 !important;
                }

                .print-signature-grid {
                    display: grid !important;
                    grid-template-columns: repeat(3, 1fr) !important;
                    gap: 12px !important;
                    margin-top: 8px !important;
                    padding-top: 6px !important;
                    font-size: 7.8px !important;
                    line-height: 1.2 !important;
                    page-break-inside: avoid !important;
                    break-inside: avoid !important;
                }
                .print-signature-gap {
                    height: 24px !important;
                }
            }
        </style>

        <!-- Hidden SVG Containers untuk Modal Alpine.js -->
        <div class="hidden print:hidden" style="display: none !important;">
            @foreach($rekapPenjualan as $rp)
                <div id="qr-svg-{{ $rp->id }}">
                    @if(class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class))
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(140)->margin(0)->generate($rp->qr_payload) !!}
                    @else
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ urlencode($rp->qr_payload) }}" alt="QR {{ $rp->no_faktur }}" class="w-[140px] h-[140px] object-contain">
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Header Khusus Cetak Dokumen Resmi Sirkulasi -->
        <div class="hidden print:block print-header">
            <div>
                <h1 class="text-xs font-black tracking-tight uppercase text-slate-900 leading-tight">
                    Harian Umum Kabar Priangan &bull; Divisi Sirkulasi
                </h1>
                <h2 class="text-[10px] font-bold text-slate-800 leading-tight mt-0.5">
                    Rekapitulasi Penjualan Koran &amp; Validasi Barcode Scan Kasir
                </h2>
                <p class="text-[7.5px] text-slate-600 leading-none mt-0.5">
                    Periode: {{ \Carbon\Carbon::parse($bulanPilihan . '-01')->translatedFormat('F Y') }} &bull; Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB
                </p>
            </div>
            <div class="text-right text-[8px] font-mono text-slate-700">
                <div class="font-bold border border-slate-900 px-1.5 py-0.5 rounded inline-block bg-slate-50 leading-none">
                    DOKUMEN VALIDASI KASIR
                </div>
                <div class="mt-0.5 text-[6.5px] text-slate-500">Barcode Terintegrasi Penjualan Masuk</div>
            </div>
        </div>

        <!-- Tabel Rekapitulasi Penjualan Agen Terintegrasi Barcode -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area print:border-none print:shadow-none print:p-0 print:rounded-none">
            <div class="px-4 sm:px-5 py-3.5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 print:hidden">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Rekapitulasi Tagihan &amp; Faktur Agen Bulanan</h2>
                    <p class="text-xs text-slate-500">
                        Periode {{ \Carbon\Carbon::parse($bulanPilihan . '-01')->translatedFormat('F Y') }} &bull; Tarif Agen Besar Rp 2.800 &bull; Sub Agen Rp 3.000
                    </p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ count($rekapPenjualan) }} Faktur Agen
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse min-w-[1050px] print:min-w-0 print:w-full table-penjualan-print">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap print:bg-slate-100 print:text-[7px]">
                        <tr>
                            <th class="col-kode py-3 px-3 text-center w-24 print:w-[50px] print:px-1 print:py-1">Kode Agen</th>
                            <th class="col-faktur py-3 px-3 w-32 print:w-[85px] print:px-1 print:py-1">No. Faktur</th>
                            <th class="col-agen py-3 px-3 min-w-[160px] print:min-w-0 print:px-1.5 print:py-1">Nama Agen &amp; Kios</th>
                            <th class="col-wilayah py-3 px-2.5 w-20 print:w-[60px] print:px-1 print:py-1">Wilayah</th>
                            <th class="col-kirim py-3 px-2.5 text-right w-16 print:w-[42px] print:px-1 print:py-1">Kirim</th>
                            <th class="col-retur py-3 px-2.5 text-right w-14 print:w-[35px] print:px-1 print:py-1">Retur</th>
                            <th class="col-nett py-3 px-2.5 text-right w-16 print:w-[45px] print:px-1 print:py-1">Nett Sales</th>
                            <th class="col-harga py-3 px-2.5 text-right w-14 print:w-[35px] print:px-1 print:py-1">Harga</th>
                            <th class="col-bayar py-3 px-3 text-right w-28 print:w-[70px] print:px-1 print:py-1">Harga Bayar</th>
                            <th class="col-piutang py-3 px-2.5 text-right w-24 print:w-[65px] print:px-1 print:py-1">Saldo Piutang</th>
                            <th class="col-status py-3 px-2 text-center w-16 print:w-[40px] print:px-1 print:py-1">Status</th>
                            <th class="col-barcode py-3 px-3 text-center min-w-[180px] print:min-w-0 print:w-[42px] print:px-0.5 print:py-1">QR Kasir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($rekapPenjualan as $rp)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="col-kode py-3 px-3 text-center whitespace-nowrap font-mono text-slate-600 print:py-1 print:px-0.5 print:text-[7.2px] print:text-black">
                                    {{ $rp->kode_agen }}
                                </td>
                                <td class="col-faktur py-3 px-3 whitespace-nowrap font-mono font-bold text-kp-blue-700 print:py-1 print:px-0.5 print:text-[7px] print:text-black">
                                    {{ $rp->no_faktur }}
                                </td>
                                <td class="col-agen py-3 px-3 font-semibold text-slate-900 print:py-1 print:px-1 print:text-[7.2px]">
                                    {{ $rp->nama_agen }}
                                </td>
                                <td class="col-wilayah py-3 px-2.5 text-slate-600 whitespace-nowrap print:py-1 print:px-0.5 print:text-[7px] print:text-black">
                                    {{ $rp->wilayah }}
                                </td>
                                <td class="col-kirim py-3 px-2.5 text-right whitespace-nowrap font-mono text-slate-700 tabular-nums print:py-1 print:px-0.5 print:text-[7px] print:text-black">
                                    {{ number_format($rp->kirim, 0, ',', '.') }}
                                </td>
                                <td class="col-retur py-3 px-2.5 text-right whitespace-nowrap font-mono text-slate-600 tabular-nums print:py-1 print:px-0.5 print:text-[7px] print:text-black">
                                    {{ number_format($rp->retur, 0, ',', '.') }}
                                </td>
                                <td class="col-nett py-3 px-2.5 text-right font-mono font-semibold text-slate-900 whitespace-nowrap tabular-nums print:py-1 print:px-0.5 print:text-[7px] print:text-black">
                                    {{ number_format($rp->nett_sales, 0, ',', '.') }}
                                </td>
                                <td class="col-harga py-3 px-2.5 text-right font-mono text-slate-600 whitespace-nowrap tabular-nums print:py-1 print:px-0.5 print:text-[7px] print:text-black">
                                    {{ number_format($rp->harga_satuan, 0, ',', '.') }}
                                </td>
                                <td class="col-bayar py-3 px-3 text-right font-mono font-semibold text-slate-900 whitespace-nowrap tabular-nums print:text-black print:py-1 print:px-0.5 print:text-[7.2px]">
                                    <span class="text-slate-400 text-xs mr-0.5 print:text-black print:text-[6.5px]">Rp</span>{{ number_format($rp->harga_bayar, 0, ',', '.') }}
                                </td>
                                <td class="col-piutang py-3 px-2.5 text-right font-mono text-slate-700 whitespace-nowrap tabular-nums print:text-black print:py-1 print:px-0.5 print:text-[7.2px]">
                                    <span class="text-slate-400 text-xs mr-0.5 print:text-black print:text-[6.5px]">Rp</span>{{ number_format($rp->saldo_piutang, 0, ',', '.') }}
                                </td>
                                <td class="col-status py-3 px-2 text-center whitespace-nowrap print:py-1 print:px-0.5">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border {{ $rp->status_bayar === 'Lancar' ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-slate-100 text-slate-700 border-slate-200' }} print:border-none print:p-0 print:text-black print:text-[6.5px]">
                                        {{ $rp->status_bayar }}
                                    </span>
                                </td>
                                <td class="col-barcode py-3 px-3 whitespace-nowrap align-middle print:py-1 print:px-0.5 print:text-center">
                                    <div class="flex items-center gap-2 print:justify-center">
                                        <div class="p-1 bg-white border border-slate-300 rounded-lg shadow-2xs shrink-0 flex flex-col items-center justify-center cursor-pointer hover:border-kp-blue-600 transition print:border-none print:p-0 print:shadow-none print-qr-box"
                                             title="Klik untuk perbesar Kode QR"
                                             @click="
                                                 activeQr = {
                                                     id: {{ $rp->id }},
                                                     no_faktur: '{{ $rp->no_faktur }}',
                                                     nama_agen: '{{ addslashes($rp->nama_agen) }}',
                                                     kode_agen: '{{ $rp->kode_agen }}',
                                                     harga_bayar: '{{ number_format($rp->harga_bayar, 0, ',', '.') }}',
                                                     wilayah: '{{ $rp->wilayah }}',
                                                     kirim: '{{ number_format($rp->kirim, 0, ',', '.') }}',
                                                     retur: '{{ number_format($rp->retur, 0, ',', '.') }}',
                                                     nett: '{{ number_format($rp->nett_sales, 0, ',', '.') }}',
                                                     url_cetak: '{{ route('sirkulasi.cetak.faktur', $rp->kode_agen) }}'
                                                 };
                                                 $nextTick(() => {
                                                     document.getElementById('modal-qr-container').innerHTML = document.getElementById('qr-svg-{{ $rp->id }}').innerHTML;
                                                 });
                                                 showQrModal = true;
                                             ">
                                            <div class="w-[44px] h-[44px] print:w-[26px] print:h-[26px] flex items-center justify-center bg-white print:p-0">
                                                @if(class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class))
                                                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(44)->margin(0)->generate($rp->qr_payload) !!}
                                                @else
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=44x44&data={{ urlencode($rp->qr_payload) }}" alt="QR {{ $rp->no_faktur }}" class="w-[44px] h-[44px] print:w-[26px] print:h-[26px] object-contain">
                                                @endif
                                            </div>
                                            <span class="text-[7px] font-mono font-black text-slate-600 uppercase tracking-tight mt-0.5 print-qr-label">
                                                SCAN
                                            </span>
                                        </div>

                                        <div class="text-left leading-tight space-y-1 min-w-[100px] print:hidden">
                                            <div class="text-xs font-bold font-mono text-kp-blue-700 tabular-nums">
                                                Rp {{ number_format($rp->harga_bayar, 0, ',', '.') }}
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <a href="{{ route('sirkulasi.cetak.faktur', $rp->kode_agen) }}"
                                                   target="_blank"
                                                   class="inline-flex items-center px-2 py-0.5 text-[10px] font-semibold rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition"
                                                   title="Buka Lembar Faktur Satuan">
                                                    Faktur
                                                </a>
                                                <button type="button"
                                                        @click="
                                                            activeQr = {
                                                                id: {{ $rp->id }},
                                                                no_faktur: '{{ $rp->no_faktur }}',
                                                                nama_agen: '{{ addslashes($rp->nama_agen) }}',
                                                                kode_agen: '{{ $rp->kode_agen }}',
                                                                harga_bayar: '{{ number_format($rp->harga_bayar, 0, ',', '.') }}',
                                                                wilayah: '{{ $rp->wilayah }}',
                                                                kirim: '{{ number_format($rp->kirim, 0, ',', '.') }}',
                                                                retur: '{{ number_format($rp->retur, 0, ',', '.') }}',
                                                                nett: '{{ number_format($rp->nett_sales, 0, ',', '.') }}',
                                                                url_cetak: '{{ route('sirkulasi.cetak.faktur', $rp->kode_agen) }}'
                                                            };
                                                            $nextTick(() => {
                                                                document.getElementById('modal-qr-container').innerHTML = document.getElementById('qr-svg-{{ $rp->id }}').innerHTML;
                                                            });
                                                            showQrModal = true;
                                                        "
                                                        class="px-2 py-0.5 text-[10px] font-semibold rounded-md bg-kp-blue-50 hover:bg-kp-blue-100 text-kp-blue-700 border border-kp-blue-200 transition cursor-pointer"
                                                        title="Perbesar Kode QR">
                                                    Zoom
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 print:bg-slate-100 print:text-[7.2px]">
                        <tr>
                            <td colspan="4" class="py-3 px-3 text-right font-sans text-xs uppercase tracking-wider text-slate-600 whitespace-nowrap print:py-1 print:px-1.5 print:text-[7.2px]">Akumulasi Total:</td>
                            <td class="col-kirim py-3 px-2.5 text-right whitespace-nowrap tabular-nums print:py-1 print:px-0.5 print:text-[7px] print:text-black">{{ number_format($totalKirim, 0, ',', '.') }}</td>
                            <td class="col-retur py-3 px-2.5 text-right text-slate-600 whitespace-nowrap tabular-nums print:py-1 print:px-0.5 print:text-[7px] print:text-black">{{ number_format($totalRetur, 0, ',', '.') }}</td>
                            <td class="col-nett py-3 px-2.5 text-right text-slate-900 whitespace-nowrap tabular-nums print:py-1 print:px-0.5 print:text-[7px] print:text-black">{{ number_format($totalNett, 0, ',', '.') }}</td>
                            <td class="col-harga py-3 px-2.5 text-right text-slate-400 whitespace-nowrap print:py-1 print:px-0.5">-</td>
                            <td class="col-bayar py-3 px-3 text-right text-kp-blue-700 font-bold whitespace-nowrap tabular-nums print:text-black print:py-1 print:px-0.5 print:text-[7.2px]">
                                Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                            </td>
                            <td class="col-piutang py-3 px-2.5 text-right text-slate-800 whitespace-nowrap tabular-nums print:text-black print:py-1 print:px-0.5 print:text-[7.2px]">
                                Rp {{ number_format($totalSaldoPiutang, 0, ',', '.') }}
                            </td>
                            <td class="col-status py-3 px-2 text-center text-slate-400 whitespace-nowrap print:py-1 print:px-0.5">-</td>
                            <td class="col-barcode py-3 px-3 text-center whitespace-nowrap print:py-1 print:px-0.5">
                                <span class="print:hidden inline-block px-2 py-0.5 rounded-md bg-kp-blue-50 text-kp-blue-700 text-xs font-mono font-semibold border border-kp-blue-200">
                                    {{ count($rekapPenjualan) }} QR
                                </span>
                                <span class="hidden print:inline text-[7px] text-slate-400 font-mono">-</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Lembar Tanda Tangan Cetak 3 Pihak -->
        <div class="hidden print:grid grid-cols-3 gap-4 text-center text-[7.8px] text-slate-900 break-inside-avoid print-signature-grid">
            <div>
                <div>Mengetahui,</div>
                <div class="font-bold">Manajer Sirkulasi</div>
                <div class="print-signature-gap mt-4"></div>
                <div class="font-bold underline">( ........................................ )</div>
            </div>
            <div>
                <div>Diverifikasi Oleh,</div>
                <div class="font-bold">Bagian Kasir / Penerimaan</div>
                <div class="print-signature-gap mt-4"></div>
                <div class="font-bold underline">( ........................................ )</div>
            </div>
            <div>
                <div>Tasikmalaya, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                <div class="font-bold">Staf Penjualan Sirkulasi</div>
                <div class="print-signature-gap mt-4"></div>
                <div class="font-bold underline">{{ Auth::user()->name ?? '( ........................................ )' }}</div>
            </div>
        </div>

        <!-- Modal Preview Kode QR Kasir Faktur Agen -->
        <div x-cloak
             x-show="showQrModal"
             class="qr-modal-container print:hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="bg-white w-full max-w-lg rounded-2xl border border-slate-200 shadow-2xl overflow-hidden p-6 space-y-5"
                 @click.outside="showQrModal = false">

                <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-md text-xs font-mono font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                SCAN KASIR
                            </span>
                            <span class="text-xs font-mono font-bold text-slate-500" x-text="activeQr.kode_agen"></span>
                        </div>
                        <h2 class="text-base font-bold text-slate-900 mt-1.5" x-text="activeQr.nama_agen"></h2>
                        <p class="text-xs text-slate-500">Wilayah: <span x-text="activeQr.wilayah"></span></p>
                    </div>
                    <button type="button" @click="showQrModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1 cursor-pointer">
                        &times;
                    </button>
                </div>

                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex flex-col sm:flex-row items-center gap-5">
                    <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-xs shrink-0 flex flex-col items-center justify-center">
                        <div id="modal-qr-container" class="w-[140px] h-[140px] flex items-center justify-center"></div>
                        <span class="block text-[9px] font-mono font-bold text-slate-500 mt-1.5 uppercase tracking-wider">
                            KODE QR KASIR
                        </span>
                    </div>

                    <div class="space-y-2 text-xs flex-1 w-full">
                        <div>
                            <span class="text-[10px] font-mono text-slate-400 block uppercase">Nomor Faktur</span>
                            <strong class="font-mono text-kp-blue-700 text-sm" x-text="activeQr.no_faktur"></strong>
                        </div>
                        <div>
                            <span class="text-[10px] font-mono text-slate-400 block uppercase">Nama Agen</span>
                            <strong class="text-slate-900 text-xs" x-text="activeQr.nama_agen"></strong>
                        </div>
                        <div class="pt-2 border-t border-slate-200">
                            <span class="text-[10px] font-mono text-slate-400 block uppercase">Total Harga Bayar</span>
                            <span class="text-lg font-bold font-mono text-kp-blue-700 tabular-nums">Rp <span x-text="activeQr.harga_bayar"></span></span>
                        </div>
                        <div class="text-[11px] text-slate-500 pt-0.5 font-mono">
                            Kirim: <strong class="text-slate-800" x-text="activeQr.kirim"></strong> &bull; Retur: <strong class="text-slate-800" x-text="activeQr.retur"></strong> &bull; Nett: <strong class="text-slate-800" x-text="activeQr.nett"></strong> eks
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-1">
                    <button type="button"
                            @click="showQrModal = false"
                            class="h-10 px-4 rounded-xl border border-slate-300 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold transition cursor-pointer">
                        Tutup
                    </button>
                    <a :href="activeQr.url_cetak"
                       target="_blank"
                       class="h-10 px-5 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold shadow-xs transition inline-flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Buka &amp; Cetak Faktur</span>
                    </a>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>
