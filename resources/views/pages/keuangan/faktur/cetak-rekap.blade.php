<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REKAP FAKTUR PEMUATAN IKLAN PENERBITAN HU. KABAR PRIANGAN</title>
    <link rel="icon" href="{{ asset('kabarpriangan.png') }}" type="image/png">
    
    <!-- Fonts & Vite Assets -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .font-mono-report {
            font-family: 'Consolas', 'Courier New', Courier, Monaco, monospace;
        }

        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        @media print {
            .no-print { display: none !important; }
            body {
                background: white !important;
                padding: 0 !important;
                color: #000 !important;
            }
            .report-card {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
                margin: 0 !important;
            }
            table th, table td {
                color: #000 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-3 sm:p-6 min-h-screen">

    <!-- Top Action Bar (Non-Printable) -->
    <div class="max-w-[1440px] mx-auto mb-4 flex flex-wrap items-center justify-between gap-3 no-print">
        <div class="flex items-center gap-3">
            <a href="{{ route('keuangan.faktur.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 shadow-xs transition">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                <span>Kembali ke Pemantauan Faktur</span>
            </a>
            <span class="text-xs text-slate-500 font-medium hidden sm:inline">
                Rekapitulasi Faktur Iklan &bull; <strong>15 Kolom Lengkap</strong>
            </span>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm hover:shadow transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Rekap (Print / PDF)</span>
            </button>
        </div>
    </div>

    <!-- Lembar Laporan Rekapitulasi (Tema Putih Bersih & Profesional) -->
    <div class="max-w-[1440px] mx-auto bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8 report-card">
        
        <!-- Header Dokumen Sesuai Format Resmi PT. Berkah Pikiran Rakyat -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between pb-4 border-b border-slate-200 mb-4 gap-3">
            <div>
                <p class="text-xs font-black text-slate-500 tracking-wider uppercase">
                    PT. BERKAH PIKIRAN RAKYAT
                </p>
                <h1 class="text-base sm:text-lg font-black text-slate-900 tracking-tight uppercase mt-0.5">
                    REKAP FAKTUR PEMUATAN IKLAN PENERBITAN HU. KABAR PRIANGAN
                </h1>
                <div class="mt-1 flex items-center gap-2 text-xs text-slate-600">
                    <span class="font-bold text-slate-800">HARI / TANGGAL :</span>
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 font-semibold font-mono-report text-slate-700">
                        {{ strtoupper($periodeJudul) }}
                    </span>
                    <span class="text-slate-400">&bull;</span>
                    <span class="text-slate-500">Kategori Status: <strong>{{ strtoupper($statusJudul) }}</strong></span>
                </div>
            </div>
            <div class="text-right sm:self-start">
                <span class="inline-block px-3 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold font-mono-report border border-slate-200">
                    Hal : 1
                </span>
                <p class="text-[11px] text-slate-400 mt-1">
                    Dicetak: {{ date('d/m/Y H:i') }}
                </p>
            </div>
        </div>

        <!-- Tabel 15 Kolom Lengkap -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[11px] border-collapse">
                <!-- THEAD TABLE -->
                <thead>
                    <tr class="bg-slate-900 text-white text-[10.5px] uppercase tracking-wider font-bold">
                        <th class="py-2.5 px-2 text-center w-8 rounded-l-lg">No.</th>
                        <th class="py-2.5 px-2 w-20">Tgl Muat</th>
                        <th class="py-2.5 px-2 w-24">Faktur</th>
                        <th class="py-2.5 px-2 w-24">Kwitansi</th>
                        <th class="py-2.5 px-2 w-32">No.Perkiraan</th>
                        <th class="py-2.5 px-2 min-w-[150px]">Nama</th>
                        <th class="py-2.5 px-2 w-24 text-right">Brutto</th>
                        <th class="py-2.5 px-2 w-20 text-right">Reduksi</th>
                        <th class="py-2.5 px-2 w-24 text-right">Netto</th>
                        <th class="py-2.5 px-2 w-20 text-right">PPn</th>
                        <th class="py-2.5 px-2 w-16 text-right">Materai</th>
                        <th class="py-2.5 px-2 w-20 text-center">TglKoreksi</th>
                        <th class="py-2.5 px-2 w-24 text-right">Bayar</th>
                        <th class="py-2.5 px-2 w-16 text-right">Koreksi</th>
                        <th class="py-2.5 px-2 w-24 text-right rounded-r-lg">Sisa</th>
                    </tr>
                </thead>

                <!-- TBODY ROWS -->
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                    @forelse($fakturs as $index => $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2 px-2 text-center font-bold text-slate-500 font-mono-report">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-2 px-2 font-mono-report text-slate-600">
                                {{ $item->calc_tgl_muat }}
                            </td>
                            <td class="py-2 px-2 font-mono-report font-bold text-blue-700">
                                {{ $item->no_faktur }}
                            </td>
                            <td class="py-2 px-2 font-mono-report text-slate-500">
                                {{ $item->calc_kwitansi }}
                            </td>
                            <td class="py-2 px-2 font-mono-report text-[10px] text-slate-600 truncate max-w-[130px]" title="{{ $item->calc_no_perkiraan }}">
                                {{ $item->calc_no_perkiraan }}
                            </td>
                            <td class="py-2 px-2 uppercase font-bold text-slate-900 truncate max-w-xs" title="{{ $item->customer->nama_customer ?? 'UMUM' }}">
                                {{ $item->customer->nama_customer ?? 'UMUM' }}
                            </td>
                            <td class="py-2 px-2 text-right font-mono-report text-slate-800">
                                {{ number_format($item->calc_brutto, 2, ',', '.') }}
                            </td>
                            <td class="py-2 px-2 text-right font-mono-report text-slate-500">
                                {{ number_format($item->calc_reduksi, 2, ',', '.') }}
                            </td>
                            <td class="py-2 px-2 text-right font-mono-report font-bold text-slate-900">
                                {{ number_format($item->calc_netto, 2, ',', '.') }}
                            </td>
                            <td class="py-2 px-2 text-right font-mono-report text-slate-600">
                                {{ number_format($item->calc_ppn, 2, ',', '.') }}
                            </td>
                            <td class="py-2 px-2 text-right font-mono-report text-slate-600">
                                {{ number_format($item->calc_materai, 2, ',', '.') }}
                            </td>
                            <td class="py-2 px-2 text-center font-mono-report text-slate-500">
                                {{ $item->status === 'lunas' ? $item->updated_at->format('d-m-Y') : '-' }}
                            </td>
                            <td class="py-2 px-2 text-right font-mono-report font-semibold text-emerald-700">
                                {{ number_format($item->total_terbayar, 2, ',', '.') }}
                            </td>
                            <td class="py-2 px-2 text-right font-mono-report text-slate-400">
                                0,00
                            </td>
                            <td class="py-2 px-2 text-right font-mono-report font-bold {{ $item->sisa_piutang > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                {{ number_format($item->sisa_piutang, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="py-8 text-center text-slate-400 font-medium">
                                Tidak ada data faktur pemuatan iklan pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <!-- TFOOT TOTAL -->
                <tfoot>
                    <tr class="bg-slate-100 border-t-2 border-b-2 border-slate-900 text-[11px] font-black uppercase text-slate-900">
                        <td class="py-2.5 px-2 text-center"></td>
                        <td class="py-2.5 px-2"></td>
                        <td class="py-2.5 px-2"></td>
                        <td class="py-2.5 px-2"></td>
                        <td class="py-2.5 px-2"></td>
                        <td class="py-2.5 px-2 tracking-wider font-extrabold text-slate-900">
                            TOTAL ...
                        </td>
                        <td class="py-2.5 px-2 text-right font-mono-report font-black">
                            {{ number_format($totalBrutto, 2, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-2 text-right font-mono-report font-black text-slate-600">
                            {{ number_format($totalReduksi, 2, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-2 text-right font-mono-report font-black text-blue-900">
                            {{ number_format($totalNetto, 2, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-2 text-right font-mono-report font-black text-slate-800">
                            {{ number_format($totalPpn, 2, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-2 text-right font-mono-report font-black text-slate-800">
                            {{ number_format($totalMaterai, 2, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-2 text-center font-mono-report"></td>
                        <td class="py-2.5 px-2 text-right font-mono-report font-black text-emerald-800">
                            {{ number_format($totalBayar, 2, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-2 text-right font-mono-report font-black text-slate-500">
                            0,00
                        </td>
                        <td class="py-2.5 px-2 text-right font-mono-report font-black text-rose-700">
                            {{ number_format($totalSisa, 2, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Kolom Tanda Tangan Resmi Sesuai Format -->
        <div class="mt-10 grid grid-cols-2 gap-8 text-xs text-slate-800">
            <!-- Tanda Tangan Kiri: Ass. Keuangan -->
            <div>
                <p class="font-medium text-slate-600">Tasikmalaya, {{ date('d-m-Y') }}</p>
                <p class="font-bold text-slate-800 mt-0.5">Ass. Keuangan,</p>
                <div class="h-16"></div>
                <p class="font-black tracking-wider uppercase underline underline-offset-4 text-slate-900 text-sm">
                    ELI YULIANTI
                </p>
            </div>

            <!-- Tanda Tangan Kanan: Manager Keuangan & Akuntansi -->
            <div class="text-right sm:text-left sm:pl-32">
                <p class="font-bold text-slate-800">Mengetahui,</p>
                <p class="font-bold text-slate-800 mt-0.5">Manager Keuangan &amp; Akuntansi</p>
                <div class="h-16"></div>
                <p class="font-black tracking-wider uppercase underline underline-offset-4 text-slate-900 text-sm">
                    NITA YULIANTI
                </p>
            </div>
        </div>

    </div>

</body>
</html>
