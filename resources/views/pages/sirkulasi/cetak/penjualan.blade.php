<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Standar Divisi Iklan, 60-30-10) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Laporan Penjualan Sirkulasi
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl" title="Rekapitulasi realisasi pengiriman, retur koran, dan nilai penjualan bersih per agen.">
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
                <span class="text-xs text-slate-400 mt-1 block">Penjualan bersih sirkulasi</span>
            </div>
        </div>

        <!-- Header Khusus Cetak Dokumen Resmi (Hanya Tampil Saat Print) -->
        <div class="hidden print:block print-header">
            <div>
                <h1 class="text-xs font-black tracking-tight uppercase text-slate-900 leading-tight">
                    Harian Umum Kabar Priangan &bull; Divisi Sirkulasi
                </h1>
                <h2 class="text-[10px] font-bold text-slate-800 leading-tight mt-0.5">
                    Rekapitulasi Penjualan Koran &amp; Tagihan Piutang Agen
                </h2>
                <p class="text-[7.5px] text-slate-600 leading-none mt-0.5">
                    Periode: {{ \Carbon\Carbon::parse($bulanPilihan . '-01')->translatedFormat('F Y') }} &bull; Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB
                </p>
            </div>
            <div class="text-right text-[8px] font-mono text-slate-700">
                <div class="font-bold border border-slate-900 px-1.5 py-0.5 rounded inline-block bg-slate-50 leading-none">
                    DOKUMEN RESMI SIRKULASI
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
                            <th class="col-nett py-3.5 px-3.5 text-right">Nett Sales</th>
                            <th class="col-harga py-3 px-3.5 text-right">Harga</th>
                            <th class="col-bayar py-3 px-3.5 text-right">Harga Bayar</th>
                            <th class="col-piutang py-3 px-3.5 text-right">Saldo Piutang</th>
                            <th class="col-status py-3 px-3.5 text-center">Status</th>
                            <th class="print:hidden py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @foreach($rekapPenjualan as $rp)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="col-kode py-3.5 px-3.5 text-center font-mono text-slate-700 font-medium print:py-1 print:px-0.5">
                                    {{ $rp->kode_agen }}
                                </td>
                                <td class="col-faktur py-3.5 px-3.5 font-mono text-slate-600 text-xs print:py-1 print:px-0.5">
                                    {{ $rp->no_faktur }}
                                </td>
                                <td class="col-agen py-3.5 px-3.5 font-medium text-slate-900 print:py-1 print:px-0.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 flex items-center justify-center font-bold text-[10px] shrink-0 print:hidden">
                                            {{ substr($rp->nama_agen, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="block truncate max-w-[200px]">{{ $rp->nama_agen }}</span>
                                            <span class="text-[11px] text-slate-400 block print:hidden">{{ $rp->jurusan }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-wilayah py-3.5 px-3.5 text-slate-600 print:py-1 print:px-0.5">
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

    </div>

    @push('styles')
    <style>
        @media print {
            header, footer, nav, aside, .print\:hidden, .no-print, [x-cloak] {
                display: none !important;
            }
            @page {
                size: landscape;
                margin: 6mm 8mm 6mm 8mm;
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
                font-size: 7.5px !important;
                line-height: 1.2 !important;
            }
            table.table-penjualan-print th,
            table.table-penjualan-print td {
                border: 0.5px solid #94a3b8 !important;
                padding: 3px 3px !important;
                vertical-align: middle !important;
                color: #000000 !important;
                word-break: break-word !important;
            }
            table.table-penjualan-print thead th {
                background-color: #f1f5f9 !important;
                color: #000000 !important;
                font-weight: 800 !important;
                font-size: 7px !important;
                text-transform: uppercase !important;
                padding: 4px 2px !important;
                white-space: normal !important;
                text-align: center !important;
            }
            table.table-penjualan-print tfoot td {
                background-color: #f8fafc !important;
                font-weight: 800 !important;
                border-top: 1.5px solid #000000 !important;
                padding: 3px 3px !important;
                font-size: 7.5px !important;
            }
            table.table-penjualan-print tr {
                page-break-inside: avoid !important;
            }
            .col-kode    { width: 7% !important; }
            .col-faktur  { width: 14% !important; }
            .col-agen    { width: 22% !important; }
            .col-wilayah { width: 11% !important; }
            .col-kirim   { width: 6.5% !important; }
            .col-retur   { width: 5.5% !important; }
            .col-nett    { width: 7% !important; }
            .col-harga   { width: 6% !important; }
            .col-bayar   { width: 11% !important; }
            .col-piutang { width: 10% !important; }
            .col-status  { width: 0% !important; display: none !important; }
            .print-header {
                display: flex !important;
                justify-content: space-between !important;
                align-items: flex-end !important;
                margin-bottom: 6px !important;
                padding-bottom: 4px !important;
                border-bottom: 1.5px solid #000000 !important;
            }
            .print-signature-grid {
                display: grid !important;
                grid-template-columns: repeat(3, 1fr) !important;
                gap: 16px !important;
                margin-top: 10px !important;
                padding-top: 8px !important;
                font-size: 8px !important;
                page-break-inside: avoid !important;
            }
            .print-signature-gap {
                height: 28px !important;
            }
        }
    </style>
    @endpush
</x-app-layout>
