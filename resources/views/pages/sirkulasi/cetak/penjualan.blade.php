<x-app-layout>
    <div class="space-y-5" x-data="{
        showQrModal: false,
        activeQr: { id: '', no_faktur: '', nama_agen: '', kode_agen: '', harga_bayar: '0', wilayah: '', kirim: '0', retur: '0', nett: '0', url_cetak: '#' }
    }">

        <!-- Header Halaman (Standar Divisi Iklan, 60-30-10) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Laporan Penjualan Sirkulasi
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 leading-relaxed">
                        Rekapitulasi realisasi pengiriman, retur koran, dan nilai penjualan bersih per agen.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <form method="GET" action="{{ route('sirkulasi.cetak.penjualan') }}" class="flex items-center gap-2">
                    <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    <button type="submit" class="h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        Filter
                    </button>
                </form>

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

        <!-- KPI Ringkasan Keuangan Sirkulasi (60-30-10) -->
        <div class="print:hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Koran Dikirim</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($totalKirim, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                <span class="text-xs text-slate-400 mt-1 block">Seluruh Agen Terdaftar</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Koran Diretur</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($totalRetur, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                <span class="text-xs text-slate-400 mt-1 block">Rasio Retur: 2.2% (Toleransi &le; 10%)</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Nett Sales Terjual</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($totalNett, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span></p>
                <span class="text-xs text-slate-400 mt-1 block">Kirim dikurangi retur</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Nilai Tagihan</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
                <span class="text-xs text-slate-400 mt-1 block">Siap di-scan kasir via Kode QR</span>
            </div>
        </div>

        <!-- Styling Khusus Cetak Printer -->
        <style>
            @media print {
                header, footer, nav, aside, .print\:hidden, .no-print, [x-cloak], .qr-modal-container {
                    display: none !important;
                }
                @page {
                    size: landscape;
                    margin: 5mm 6mm 5mm 6mm;
                }
                html, body {
                    width: 100% !important;
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
                    margin: 0 !important;
                    padding: 0 !important;
                    border: none !important;
                    box-shadow: none !important;
                    overflow: visible !important;
                }
                .overflow-x-auto {
                    overflow: visible !important;
                    width: 100% !important;
                }
                table.table-penjualan-print {
                    width: 100% !important;
                    min-width: 100% !important;
                    max-width: 100% !important;
                    table-layout: fixed !important;
                    border-collapse: collapse !important;
                    font-size: 7.2px !important;
                    line-height: 1.15 !important;
                }
                table.table-penjualan-print th,
                table.table-penjualan-print td {
                    border: 0.5px solid #94a3b8 !important;
                    padding: 2px 2.5px !important;
                    vertical-align: middle !important;
                    color: #000000 !important;
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
                table.table-penjualan-print tfoot td {
                    background-color: #f8fafc !important;
                    font-weight: 800 !important;
                    border-top: 1.5px solid #000000 !important;
                    padding: 2.5px 2px !important;
                    font-size: 7.2px !important;
                }
                table.table-penjualan-print tr {
                    page-break-inside: avoid !important;
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
                    display: flex !important;
                    flex-direction: column !important;
                    align-items: center !important;
                    justify-content: center !important;
                }
                .print-qr-box svg {
                    width: 26px !important;
                    height: 26px !important;
                    shape-rendering: crispEdges !important;
                }
                .print-qr-label {
                    font-size: 5px !important;
                    font-weight: 900 !important;
                    line-height: 1 !important;
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
                    page-break-inside: avoid !important;
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

        <!-- Header Khusus Cetak Dokumen Resmi (Hanya Tampil Saat Print) -->
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
            </div>
        </div>

        <!-- Tabel Rekapitulasi Penjualan Agen (Standar Divisi Iklan, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden printable-area print:border-none print:shadow-none print:p-0 print:rounded-none">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse print:min-w-0 print:w-full table-penjualan-print">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap print:bg-slate-100 print:text-slate-900">
                        <tr>
                            <th class="col-kode py-3 px-3.5 text-center">Kode</th>
                            <th class="col-faktur py-3 px-3.5">No. Faktur</th>
                            <th class="col-agen py-3 px-3.5">Nama Agen</th>
                            <th class="col-wilayah py-3 px-3.5">Wilayah</th>
                            <th class="col-kirim py-3 px-3.5 text-right">Kirim</th>
                            <th class="col-retur py-3 px-3.5 text-right">Retur</th>
                            <th class="col-nett py-3 px-3.5 text-right">Nett Sales</th>
                            <th class="col-harga py-3 px-3.5 text-right">Harga</th>
                            <th class="col-bayar py-3 px-3.5 text-right">Harga Bayar</th>
                            <th class="col-piutang py-3 px-3.5 text-right">Saldo Piutang</th>
                            <th class="col-status py-3 px-3.5 text-center">Status</th>
                            <th class="col-barcode py-3 px-3.5 text-center">Barcode QR</th>
                            <th class="print:hidden py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @foreach($rekapPenjualan as $rp)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="col-kode py-3.5 px-3.5 text-center font-mono text-slate-700 font-medium print:py-1 print:px-0.5">
                                    {{ $rp->kode_agen }}
                                </td>
                                <td class="col-faktur py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm print:py-1 print:px-0.5 print:text-black">
                                    {{ $rp->no_faktur }}
                                </td>
                                <td class="col-agen py-3.5 px-3.5 font-semibold text-slate-900 print:py-1 print:px-1">
                                    {{ $rp->nama_agen }}
                                </td>
                                <td class="col-wilayah py-3.5 px-3.5 text-slate-600 font-normal print:py-1 print:px-0.5 print:text-black">
                                    {{ $rp->wilayah }}
                                </td>
                                <td class="col-kirim py-3.5 px-3.5 text-right font-mono text-slate-800 tabular-nums print:py-1 print:px-0.5">
                                    {{ number_format($rp->kirim, 0, ',', '.') }}
                                </td>
                                <td class="col-retur py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums print:py-1 print:px-0.5">
                                    {{ number_format($rp->retur, 0, ',', '.') }}
                                </td>
                                <td class="col-nett py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums print:py-1 print:px-0.5">
                                    {{ number_format($rp->nett_sales, 0, ',', '.') }}
                                </td>
                                <td class="col-harga py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums print:py-1 print:px-0.5">
                                    {{ number_format($rp->harga_satuan, 0, ',', '.') }}
                                </td>
                                <td class="col-bayar py-3.5 px-3.5 text-right font-mono font-semibold text-kp-blue-700 tabular-nums print:text-black print:py-1 print:px-0.5">
                                    Rp {{ number_format($rp->harga_bayar, 0, ',', '.') }}
                                </td>
                                <td class="col-piutang py-3.5 px-3.5 text-right font-mono tabular-nums {{ $rp->saldo_piutang > 0 ? 'font-semibold text-slate-900' : 'text-slate-400' }} print:text-black print:py-1 print:px-0.5">
                                    Rp {{ number_format($rp->saldo_piutang, 0, ',', '.') }}
                                </td>
                                <td class="col-status py-3.5 px-3.5 text-center print:py-1 print:px-0.5">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border {{ $rp->status_bayar === 'Lancar' ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-slate-100 text-slate-700 border-slate-200' }} print:border-none print:p-0 print:text-black">
                                        {{ $rp->status_bayar }}
                                    </span>
                                </td>
                                <td class="col-barcode py-2.5 px-3.5 text-center align-middle print:py-1 print:px-0.5">
                                    <div class="inline-flex flex-col items-center justify-center p-1 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-kp-blue-600 transition print:border-none print:p-0 print-qr-box"
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
                                                 nett: '{{ number_format($rp->nett_sales, 0, ',', '.') }}'
                                             };
                                             $nextTick(() => {
                                                 document.getElementById('modal-qr-container').innerHTML = document.getElementById('qr-svg-{{ $rp->id }}').innerHTML;
                                             });
                                             showQrModal = true;
                                         ">
                                        <div class="w-[38px] h-[38px] print:w-[26px] print:h-[26px] flex items-center justify-center bg-white">
                                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(38)->margin(0)->generate($rp->qr_payload) !!}
                                        </div>
                                        <span class="text-[7px] font-mono font-bold text-slate-500 uppercase tracking-tight mt-0.5 print-qr-label">
                                            SCAN
                                        </span>
                                    </div>
                                </td>
                                <td class="print:hidden py-3.5 px-3.5 text-center"
                                    x-data="{ open: false }"
                                    :class="{ 'relative z-50': open }">
                                    <div class="relative inline-block text-left" @click.outside="open = false">
                                        <button type="button"
                                                @click="open = !open"
                                                class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none cursor-pointer"
                                                :class="{ 'text-slate-900 bg-slate-100': open }"
                                                title="Pilihan Aksi">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="5" r="1.5" />
                                                <circle cx="12" cy="12" r="1.5" />
                                                <circle cx="12" cy="19" r="1.5" />
                                            </svg>
                                        </button>

                                        <div x-cloak
                                             x-show="open"
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">

                                            <button type="button"
                                                    @click="
                                                        open = false;
                                                        activeQr = {
                                                            id: {{ $rp->id }},
                                                            no_faktur: '{{ $rp->no_faktur }}',
                                                            nama_agen: '{{ addslashes($rp->nama_agen) }}',
                                                            kode_agen: '{{ $rp->kode_agen }}',
                                                            harga_bayar: '{{ number_format($rp->harga_bayar, 0, ',', '.') }}',
                                                            wilayah: '{{ $rp->wilayah }}',
                                                            kirim: '{{ number_format($rp->kirim, 0, ',', '.') }}',
                                                            retur: '{{ number_format($rp->retur, 0, ',', '.') }}',
                                                            nett: '{{ number_format($rp->nett_sales, 0, ',', '.') }}'
                                                        };
                                                        $nextTick(() => {
                                                            document.getElementById('modal-qr-container').innerHTML = document.getElementById('qr-svg-{{ $rp->id }}').innerHTML;
                                                        });
                                                        showQrModal = true;
                                                    "
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                                <span>Perbesar Kode QR</span>
                                            </button>

                                            <a href="{{ route('sirkulasi.tabel.agen', ['search' => $rp->kode_agen]) }}"
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail Agen</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 font-semibold text-slate-900 border-t-2 border-slate-200 print:bg-slate-100 print:text-[7.2px]">
                        <tr>
                            <td colspan="4" class="py-3.5 px-3.5 text-right uppercase text-xs font-bold text-slate-700 print:py-1 print:px-1.5">Akumulasi Total:</td>
                            <td class="col-kirim py-3.5 px-3.5 text-right font-mono tabular-nums print:py-1 print:px-0.5">{{ number_format($totalKirim, 0, ',', '.') }}</td>
                            <td class="col-retur py-3.5 px-3.5 text-right font-mono tabular-nums print:py-1 print:px-0.5">{{ number_format($totalRetur, 0, ',', '.') }}</td>
                            <td class="col-nett py-3.5 px-3.5 text-right font-mono font-bold tabular-nums print:py-1 print:px-0.5">{{ number_format($totalNett, 0, ',', '.') }}</td>
                            <td class="col-harga py-3.5 px-3.5 text-right text-slate-400 print:py-1 print:px-0.5">-</td>
                            <td class="col-bayar py-3.5 px-3.5 text-right font-mono font-bold text-kp-blue-700 tabular-nums print:text-black print:py-1 print:px-0.5">
                                Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                            </td>
                            <td class="col-piutang py-3.5 px-3.5 text-right font-mono font-bold text-slate-900 tabular-nums print:text-black print:py-1 print:px-0.5">
                                Rp {{ number_format($totalSaldoPiutang, 0, ',', '.') }}
                            </td>
                            <td class="col-status py-3.5 px-3.5 text-center text-slate-400 print:py-1 print:px-0.5">-</td>
                            <td class="col-barcode py-3.5 px-3.5 text-center text-slate-400 print:py-1 print:px-0.5">-</td>
                            <td class="print:hidden"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Lembar Tanda Tangan Cetak 3 Pihak (Hanya Tampil Saat Print) -->
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

        <!-- Interactive Modal: Preview Kode QR Kasir Faktur Agen (60-30-10) -->
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

                <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                SCAN KASIR
                            </span>
                            <span class="text-xs font-mono font-bold text-slate-500" x-text="activeQr.kode_agen"></span>
                        </div>
                        <h2 class="text-base font-bold text-slate-900 mt-1" x-text="activeQr.nama_agen"></h2>
                        <p class="text-xs text-slate-500">Wilayah: <span x-text="activeQr.wilayah"></span></p>
                    </div>
                    <button type="button" @click="showQrModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex flex-col sm:flex-row items-center gap-5">
                    <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-xs shrink-0 flex flex-col items-center justify-center">
                        <div id="modal-qr-container" class="w-[140px] h-[140px] flex items-center justify-center"></div>
                        <span class="block text-[10px] font-mono font-bold text-slate-500 mt-1 uppercase tracking-wider">KODE QR KASIR</span>
                    </div>

                    <div class="space-y-2 text-xs flex-1">
                        <div>
                            <span class="text-[11px] font-mono text-slate-400 block uppercase">Nomor Faktur</span>
                            <strong class="font-mono text-slate-900 text-sm" x-text="activeQr.no_faktur"></strong>
                        </div>
                        <div>
                            <span class="text-[11px] font-mono text-slate-400 block uppercase">Nama Agen</span>
                            <strong class="text-slate-900 text-sm" x-text="activeQr.nama_agen"></strong>
                        </div>
                        <div class="pt-1 border-t border-slate-200">
                            <span class="text-[11px] font-mono text-slate-400 block uppercase">Total Penjualan Masuk</span>
                            <span class="text-lg font-bold font-mono text-kp-blue-700">Rp <span x-text="activeQr.harga_bayar"></span></span>
                        </div>
                        <div class="text-xs text-slate-500 pt-0.5">
                            Kirim: <strong x-text="activeQr.kirim"></strong> eks &bull; Retur: <strong x-text="activeQr.retur"></strong> eks &bull; Terjual: <strong x-text="activeQr.nett"></strong> eks
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-kp-blue-50/70 border border-kp-blue-200 text-xs text-slate-700 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="leading-relaxed">
                        Kode QR ini memuat identitas faktur dan nominal tagihan yang dapat langsung dipindai oleh bagian kasir saat penyetoran.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <button type="button"
                            @click="showQrModal = false"
                            class="h-10 px-5 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-sm font-bold shadow-xs transition cursor-pointer">
                        Tutup
                    </button>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>
