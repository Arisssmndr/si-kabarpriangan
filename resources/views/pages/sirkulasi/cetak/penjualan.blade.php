<x-app-layout>
    <div class="space-y-6" x-data="{ 
        showQrModal: false, 
        activeQr: { id: '', no_faktur: '', nama_agen: '', kode_agen: '', harga_bayar: '0', wilayah: '', kirim: '0', retur: '0', nett: '0', url_cetak: '#' } 
    }">

        <!-- Header Halaman -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Menu Cetak</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Laporan Penjualan &amp; Faktur Sirkulasi</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekapitulasi penjualan bulanan agen sirkulasi dengan integrasi Kode QR Faktur untuk validasi scan kasir
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" action="{{ route('sirkulasi.cetak.penjualan') }}" class="flex items-center gap-2">
                    <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="text-xs rounded-lg border-slate-200 py-1.5 focus:border-emerald-500 focus:ring-emerald-500">
                    <button type="submit" class="py-1.5 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition-colors cursor-pointer">
                        Filter
                    </button>
                </form>

                <!-- Tombol Lihat Faktur Agen Resmi & QR -->
                <a href="{{ route('sirkulasi.cetak.faktur', ['bulan' => $bulanPilihan]) }}" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 transition-colors inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    <span>Faktur Agen &amp; QR Kasir</span>
                </a>

                <!-- Cetak Rekapitulasi -->
                <button type="button" onclick="window.print()" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak Laporan</span>
                </button>
            </div>
        </div>

        <!-- KPI Ringkasan Keuangan Sirkulasi -->
        <div class="print:hidden grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Koran Dikirim</span>
                <div class="text-xl font-extrabold text-slate-900 mt-1">{{ number_format($totalKirim, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">eks</span></div>
                <div class="text-[11px] text-slate-400 mt-0.5">Seluruh Agen Terdaftar</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-rose-600 uppercase tracking-wider">Total Koran Diretur</span>
                <div class="text-xl font-extrabold text-rose-700 mt-1">{{ number_format($totalRetur, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">eks</span></div>
                <div class="text-[11px] text-slate-400 mt-0.5">Rasio Retur: 2.2% (Toleransi &le; 10%)</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider">Nett Sales Terjual</span>
                <div class="text-xl font-extrabold text-emerald-700 mt-1">{{ number_format($totalNett, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">eks</span></div>
                <div class="text-[11px] text-slate-400 mt-0.5">Kirim Dikurangi Retur</div>
            </div>

            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-emerald-800 uppercase tracking-wider">Total Nilai Tagihan</span>
                <div class="text-xl font-extrabold text-emerald-900 mt-1">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
                <div class="text-[11px] text-emerald-700 mt-0.5">Siap di-scan kasir via Kode QR</div>
            </div>
        </div>

    <!-- Styling Khusus Cetak Printer (Anti-Terpotong Horizontal & Vertikal, Presisi 1 Halaman Landscape) -->
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
            .space-y-6 > * {
                margin-top: 0 !important;
                margin-bottom: 0 !important;
            }
            .overflow-x-auto {
                overflow: visible !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            /* Table 100% Width Fixed Layout agar pas persis pada kertas A4 Landscape */
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
            table.table-penjualan-print thead th {
                background-color: #f1f5f9 !important;
                color: #000000 !important;
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

            /* Pembagian Lebar Kolom Paten (Total 100% Pas Sesuai Area A4) */
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

            /* Kotak Barcode / QR Khusus Print (Tajam, Presisi, Anti-Pecah, Pas 1 Kolom) */
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

            /* Header Lembar Cetak */
            .print-header {
                display: flex !important;
                justify-content: space-between !important;
                align-items: flex-end !important;
                margin-bottom: 5px !important;
                padding-bottom: 3px !important;
                border-bottom: 1.5px solid #000000 !important;
            }

            /* Lembar Tanda Tangan Kompak & Rapi Pas di Bawah Tabel */
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
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(140)->margin(0)->generate($rp->qr_payload) !!}
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
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 print:hidden">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                    <span>Rekapitulasi Tagihan &amp; Faktur Agen Bulanan</span>
                    <span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded bg-emerald-100 text-emerald-800 border border-emerald-300">
                        Barcode Scan Kasir Terpasang
                    </span>
                </h3>
                <p class="text-[11px] text-slate-500">Periode: {{ \Carbon\Carbon::parse($bulanPilihan . '-01')->translatedFormat('F Y') }} &bull; Dilengkapi Nomor Faktur &amp; Barcode Penjualan Masuk untuk Kasir</p>
            </div>
            <div class="text-right text-xs text-slate-500">
                Tarif Agen Besar: <strong class="text-slate-800">Rp2.800</strong> &bull; Sub Agen: <strong class="text-slate-800">Rp3.000</strong>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 min-w-[1050px] print:min-w-0 print:w-full table-penjualan-print">
                <thead class="bg-slate-100/75 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200 print:bg-slate-100 print:text-[7px]">
                    <tr>
                        <th class="col-kode py-2.5 px-3 whitespace-nowrap text-center w-24 print:w-[50px] print:px-1 print:py-1">Kode Agen</th>
                        <th class="col-faktur py-2.5 px-3 whitespace-nowrap w-32 print:w-[85px] print:px-1 print:py-1">No. Faktur</th>
                        <th class="col-agen py-2.5 px-3 min-w-[160px] print:min-w-0 print:px-1.5 print:py-1">Nama Agen &amp; Kios</th>
                        <th class="col-wilayah py-2.5 px-2.5 whitespace-nowrap w-20 print:w-[60px] print:px-1 print:py-1">Wilayah</th>
                        <th class="col-kirim py-2.5 px-2 text-right whitespace-nowrap w-16 print:w-[42px] print:px-1 print:py-1">Kirim</th>
                        <th class="col-retur py-2.5 px-2 text-right text-rose-600 whitespace-nowrap w-14 print:w-[35px] print:px-1 print:py-1 print:text-black">Retur</th>
                        <th class="col-nett py-2.5 px-2 text-right font-extrabold text-slate-900 whitespace-nowrap w-16 print:w-[45px] print:px-1 print:py-1">Nett Sales</th>
                        <th class="col-harga py-2.5 px-2 text-right whitespace-nowrap w-14 print:w-[35px] print:px-1 print:py-1">Harga</th>
                        <th class="col-bayar py-2.5 px-3 text-right font-bold text-emerald-800 whitespace-nowrap w-28 print:w-[70px] print:px-1 print:py-1 print:text-black">Harga Bayar</th>
                        <th class="col-piutang py-2.5 px-2.5 text-right whitespace-nowrap w-24 print:w-[65px] print:px-1 print:py-1 print:text-black">Saldo Piutang</th>
                        <th class="col-status py-2.5 px-2 text-center whitespace-nowrap w-16 print:w-[40px] print:px-1 print:py-1">Status</th>
                        <th class="col-barcode py-2.5 px-3 text-center whitespace-nowrap min-w-[190px] print:min-w-0 print:w-[42px] print:px-0.5 print:py-1">Barcode Kasir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($rekapPenjualan as $rp)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="col-kode py-2 px-3 whitespace-nowrap print:py-1 print:px-0.5 print:text-center">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded font-mono font-bold text-slate-700 bg-slate-100 text-[11px] border border-slate-200 print:border-none print:p-0 print:bg-transparent print:text-[7.2px] print:text-black">
                                    {{ $rp->kode_agen }}
                                </span>
                            </td>
                            <td class="col-faktur py-2 px-3 whitespace-nowrap print:py-1 print:px-0.5">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded font-mono font-bold text-emerald-800 bg-emerald-50 text-[10.5px] border border-emerald-200 print:border-none print:p-0 print:bg-transparent print:text-[7px] print:text-black">
                                    {{ $rp->no_faktur }}
                                </span>
                            </td>
                            <td class="col-agen py-2 px-3 font-bold text-slate-900 text-xs print:py-1 print:px-1 print:text-[7.2px] print:font-semibold">
                                {{ $rp->nama_agen }}
                            </td>
                            <td class="col-wilayah py-2 px-2.5 text-xs text-slate-600 whitespace-nowrap print:py-1 print:px-0.5 print:text-[7px] print:text-black">
                                {{ $rp->wilayah }}
                            </td>
                            <td class="col-kirim py-2 px-2 text-right whitespace-nowrap font-semibold text-slate-800 tabular-nums print:py-1 print:px-0.5 print:text-[7px] print:text-black">
                                {{ number_format($rp->kirim, 0, ',', '.') }}
                            </td>
                            <td class="col-retur py-2 px-2 text-right text-rose-600 font-semibold whitespace-nowrap tabular-nums print:py-1 print:px-0.5 print:text-[7px] print:text-black">
                                {{ number_format($rp->retur, 0, ',', '.') }}
                            </td>
                            <td class="col-nett py-2 px-2 text-right font-bold text-slate-900 whitespace-nowrap tabular-nums print:py-1 print:px-0.5 print:text-[7px] print:text-black">
                                {{ number_format($rp->nett_sales, 0, ',', '.') }}
                            </td>
                            <td class="col-harga py-2 px-2 text-right font-mono text-xs text-slate-700 whitespace-nowrap tabular-nums print:py-1 print:px-0.5 print:text-[7px] print:text-black">
                                {{ number_format($rp->harga_satuan, 0, ',', '.') }}
                            </td>
                            <td class="col-bayar py-2 px-3 text-right font-bold text-emerald-800 whitespace-nowrap tabular-nums font-mono print:text-black print:py-1 print:px-0.5 print:text-[7.2px]">
                                <span class="text-emerald-600/70 text-[10px] mr-0.5 print:text-black print:text-[6.5px]">Rp</span>{{ number_format($rp->harga_bayar, 0, ',', '.') }}
                            </td>
                            <td class="col-piutang py-2 px-2.5 text-right font-extrabold whitespace-nowrap tabular-nums font-mono text-xs {{ $rp->saldo_piutang > 0 ? 'text-rose-600' : 'text-slate-600' }} print:text-black print:py-1 print:px-0.5 print:text-[7.2px]">
                                <span class="{{ $rp->saldo_piutang > 0 ? 'text-rose-400' : 'text-slate-400' }} text-[10px] mr-0.5 print:text-black print:text-[6.5px]">Rp</span>{{ number_format($rp->saldo_piutang, 0, ',', '.') }}
                            </td>
                            <td class="col-status py-2 px-2 text-center whitespace-nowrap print:py-1 print:px-0.5">
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[9.5px] font-bold border whitespace-nowrap shadow-2xs {{ $rp->status_bayar === 'Lancar' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' }} print:border-none print:shadow-none print:p-0 print:text-black print:text-[6.5px]">
                                    <span class="w-1 h-1 rounded-full {{ $rp->status_bayar === 'Lancar' ? 'bg-emerald-500' : 'bg-slate-400' }} print:hidden"></span>
                                    <span>{{ $rp->status_bayar }}</span>
                                </span>
                            </td>
                            <!-- Kolom Barcode / QR Scan Kasir Terintegrasi Langsung (Kompak, Rapi, & Pas Saat Print) -->
                            <td class="col-barcode py-2 px-3 whitespace-nowrap align-middle print:py-1 print:px-0.5 print:text-center">
                                <div class="flex items-center gap-2 print:justify-center">
                                    <!-- Kotak Barcode / QR Code Scannable Langsung -->
                                    <div class="p-1 bg-white border border-slate-300 rounded-lg shadow-2xs shrink-0 flex flex-col items-center justify-center cursor-pointer hover:border-emerald-500 hover:shadow-xs transition print:border-none print:p-0 print:shadow-none print-qr-box"
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
                                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(44)->margin(0)->generate($rp->qr_payload) !!}
                                        </div>
                                        <span class="text-[7px] font-mono font-black text-slate-600 uppercase tracking-tight mt-0.5 print-qr-label">
                                            SCAN
                                        </span>
                                    </div>

                                    <!-- Keterangan Penjualan Masuk (Hanya Tampil di Layar Desktop, Disembunyikan saat Print agar pas 1 halaman dan tidak terpotong) -->
                                    <div class="text-left leading-tight space-y-0.5 min-w-[110px] print:hidden">
                                        <span class="text-[8.5px] font-mono font-bold text-slate-400 uppercase tracking-wider block">
                                            Penjualan Masuk:
                                        </span>
                                        <div class="text-xs font-black font-mono text-emerald-800">
                                            Rp {{ number_format($rp->harga_bayar, 0, ',', '.') }}
                                        </div>
                                        <div class="text-[9.5px] text-slate-500 font-semibold">
                                            {{ number_format($rp->nett_sales, 0, ',', '.') }} eks terjual
                                        </div>
                                        <div class="pt-0.5 flex items-center gap-1.5">
                                            <a href="{{ route('sirkulasi.cetak.faktur', $rp->kode_agen) }}" 
                                               target="_blank" 
                                               class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[9.5px] font-bold rounded bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition"
                                               title="Buka Lembar Faktur Satuan">
                                                <svg class="w-2.5 h-2.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                <span>Faktur</span>
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
                                                    class="px-1.5 py-0.5 text-[9.5px] font-bold rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 transition cursor-pointer"
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
                <tfoot class="bg-slate-100 font-bold text-slate-800 border-t-2 border-slate-200 print:bg-slate-100 print:text-[7.2px]">
                    <tr>
                        <td colspan="4" class="py-2.5 px-3 text-right uppercase text-[10px] whitespace-nowrap print:py-1 print:px-1.5 print:text-[7.2px]">Akumulasi Total:</td>
                        <td class="col-kirim py-2.5 px-2 text-right whitespace-nowrap tabular-nums print:py-1 print:px-0.5 print:text-[7px] print:text-black">{{ number_format($totalKirim, 0, ',', '.') }} eks</td>
                        <td class="col-retur py-2.5 px-2 text-right text-rose-600 whitespace-nowrap tabular-nums print:py-1 print:px-0.5 print:text-[7px] print:text-black">{{ number_format($totalRetur, 0, ',', '.') }} eks</td>
                        <td class="col-nett py-2.5 px-2 text-right text-slate-900 whitespace-nowrap tabular-nums print:py-1 print:px-0.5 print:text-[7px] print:text-black">{{ number_format($totalNett, 0, ',', '.') }} eks</td>
                        <td class="col-harga py-2.5 px-2 text-right text-slate-400 whitespace-nowrap print:py-1 print:px-0.5">-</td>
                        <td class="col-bayar py-2.5 px-3 text-right text-emerald-900 text-xs font-extrabold whitespace-nowrap tabular-nums font-mono print:text-black print:py-1 print:px-0.5 print:text-[7.2px]">
                            <span class="text-emerald-700 text-[10px] mr-0.5 print:text-black print:text-[6.5px]">Rp</span>{{ number_format($totalTagihan, 0, ',', '.') }}
                        </td>
                        <td class="col-piutang py-2.5 px-2.5 text-right text-rose-600 text-xs font-extrabold whitespace-nowrap tabular-nums font-mono print:text-black print:py-1 print:px-0.5 print:text-[7.2px]">
                            <span class="text-rose-400 text-[10px] mr-0.5 print:text-black print:text-[6.5px]">Rp</span>{{ number_format($totalSaldoPiutang, 0, ',', '.') }}
                        </td>
                        <td class="col-status py-2.5 px-2 text-center text-slate-400 whitespace-nowrap print:py-1 print:px-0.5">-</td>
                        <td class="col-barcode py-2.5 px-3 text-center whitespace-nowrap print:py-1 print:px-0.5">
                            <span class="print:hidden inline-flex items-center justify-center px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-900 text-[9px] font-mono font-bold border border-emerald-200">
                                Total: Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                            </span>
                            <span class="hidden print:inline text-[7px] text-slate-400 font-mono">-</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="p-4 bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2 print:hidden">
            <span>Data penjualan otomatis tersinkronisasi dengan Surat Jalan Pengepakan, Faktur Akuntansi, &amp; Kasir Scan.</span>
            <span>Harian Umum Kabar Priangan &bull; Divisi Sirkulasi</span>
        </div>
    </div>

    <!-- Lembar Tanda Tangan Cetak 3 Pihak (Hanya Muncul Saat Cetak, Kompak & Rapi) -->
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

        <!-- Interactive Modal: Preview Kode QR Kasir Faktur Agen -->
        <div x-cloak 
             x-show="showQrModal" 
             class="qr-modal-container print:hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="bg-white w-full max-w-lg rounded-2xl border border-slate-200 shadow-2xl overflow-hidden p-6 space-y-5"
                 @click.outside="showQrModal = false">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                SCAN KASIR
                            </span>
                            <span class="text-xs font-mono font-bold text-slate-500" x-text="activeQr.kode_agen"></span>
                        </div>
                        <h2 class="text-base font-black text-slate-900 mt-1" x-text="activeQr.nama_agen"></h2>
                        <p class="text-xs text-slate-500">Wilayah: <span x-text="activeQr.wilayah"></span></p>
                    </div>
                    <button type="button" @click="showQrModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1">
                        &times;
                    </button>
                </div>

                <!-- Box Detail Kode QR Kasir -->
                <div class="p-4 bg-slate-50 rounded-xl border border-emerald-200 flex flex-col sm:flex-row items-center gap-5">
                    <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-xs shrink-0 flex flex-col items-center justify-center">
                        <div id="modal-qr-container" class="w-[140px] h-[140px] flex items-center justify-center">
                            <!-- SVG QR dimasukkan via Javascript -->
                        </div>
                        <span class="block text-[9px] font-mono font-bold text-slate-500 mt-1 uppercase tracking-wider">
                            KODE QR KASIR
                        </span>
                    </div>

                    <div class="space-y-2 text-xs flex-1">
                        <div>
                            <span class="text-[10px] font-mono text-slate-400 block uppercase">Nomor Faktur:</span>
                            <strong class="font-mono text-emerald-900 text-sm" x-text="activeQr.no_faktur"></strong>
                        </div>
                        <div>
                            <span class="text-[10px] font-mono text-slate-400 block uppercase">Nama Agen (Penyetor):</span>
                            <strong class="text-slate-900 text-xs" x-text="activeQr.nama_agen"></strong>
                        </div>
                        <div class="pt-1 border-t border-slate-200">
                            <span class="text-[10px] font-mono text-slate-400 block uppercase">Total Penjualan Masuk:</span>
                            <span class="text-lg font-black font-mono text-emerald-700">Rp <span x-text="activeQr.harga_bayar"></span></span>
                        </div>
                        <div class="text-[11px] text-slate-500 pt-0.5">
                            Oplah Kirim: <strong x-text="activeQr.kirim"></strong> eks &bull; Retur: <strong x-text="activeQr.retur"></strong> eks &bull; Nett Terjual: <strong x-text="activeQr.nett"></strong> eks
                        </div>
                    </div>
                </div>

                <!-- Petunjuk Scan Kasir -->
                <div class="p-3 rounded-lg bg-emerald-50/70 border border-emerald-200 text-xs text-emerald-900 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="leading-relaxed">
                        Kode QR di atas memuat identitas <strong>Nomor Faktur</strong>, <strong>Nama Agen</strong>, dan nominal <strong>Harga Bayar</strong> yang dapat langsung dipindai oleh kasir saat pembayaran koran.
                    </p>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button type="button" 
                            @click="showQrModal = false" 
                            class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition cursor-pointer">
                        Tutup
                    </button>
                    <a :href="activeQr.url_cetak" 
                       target="_blank" 
                       class="px-5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Buka &amp; Cetak Faktur Ini</span>
                    </a>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>
