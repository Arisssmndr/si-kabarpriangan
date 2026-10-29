<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REKAP BUKTI PENGELUARAN (BP) PENERBITAN HU. KABAR PRIANGAN</title>
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
            <a href="{{ route('keuangan.anggaran.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 shadow-xs transition">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                <span>Kembali ke Anggaran</span>
            </a>
            <span class="text-xs text-slate-500 font-medium hidden sm:inline">
                Sistem DOS ANGGARAN.EXE &bull; <strong>Rekap BP Priangan Realisasi Kas</strong>
            </span>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-sm hover:shadow transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Rekap (Print / PDF)</span>
            </button>
        </div>
    </div>

    <!-- Lembar Rekapitulasi BP Priangan -->
    <div class="max-w-[1440px] mx-auto bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8 report-card">
        
        <!-- Header Dokumen Sesuai Format Resmi PT. Berkah Pikiran Rakyat -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between pb-4 border-b border-slate-200 mb-4 gap-3">
            <div>
                <p class="text-xs font-black text-slate-500 tracking-wider uppercase">
                    PT. BERKAH PIKIRAN RAKYAT
                </p>
                <h1 class="text-base sm:text-lg font-black text-slate-900 tracking-tight uppercase mt-0.5">
                    REKAP BUKTI PENGELUARAN (BP) PENERBITAN HU. KABAR PRIANGAN
                </h1>
                <div class="mt-1 flex items-center gap-2 text-xs text-slate-600">
                    <span class="font-bold text-slate-800">PERIODE :</span>
                    <span class="px-2 py-0.5 rounded-md bg-purple-50 font-semibold font-mono-report text-purple-900 border border-purple-200">
                        {{ strtoupper($periodeJudul) }}
                    </span>
                    <span class="text-slate-400">&bull;</span>
                    <span class="text-slate-500">Total Transaksi: <strong>{{ $bpList->count() }} Dokumen</strong></span>
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

        <!-- Tabel Rekap BP Priangan Terkoneksi Kas -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[11px] border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white text-[10px] uppercase tracking-wider font-bold">
                        <th class="py-2.5 px-2 text-center w-8 rounded-l-lg">No.</th>
                        <th class="py-2.5 px-2 w-20">Tanggal</th>
                        <th class="py-2.5 px-2 w-28">No. BP</th>
                        <th class="py-2.5 px-2 w-32">No. Register (PR)</th>
                        <th class="py-2.5 px-2 w-28">Bidang</th>
                        <th class="py-2.5 px-2 w-28">Kode Perkiraan</th>
                        <th class="py-2.5 px-2 min-w-[130px]">Pemohon</th>
                        <th class="py-2.5 px-2 min-w-[180px]">Keperluan</th>
                        <th class="py-2.5 px-2 w-24 text-right">Diajukan</th>
                        <th class="py-2.5 px-2 w-24 text-right">Disetujui</th>
                        <th class="py-2.5 px-2 w-28 text-center">BKK Kas Keluar</th>
                        <th class="py-2.5 px-2 w-24 text-center rounded-r-lg">Status Kas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                    @forelse($bpList as $index => $bp)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2 px-2 text-center font-bold text-slate-500 font-mono-report">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-2 px-2 font-mono-report text-slate-600">
                                {{ $bp->tanggal_pengajuan ? $bp->tanggal_pengajuan->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-2 px-2 font-mono-report font-bold text-purple-700">
                                {{ $bp->no_bp }}
                            </td>
                            <td class="py-2 px-2 font-mono-report text-slate-600 font-semibold">
                                {{ $bp->no_register_anggaran ?: '-' }}
                            </td>
                            <td class="py-2 px-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono-report bg-slate-100 text-slate-800">
                                    {{ $bp->kode_bagian ? $bp->kode_bagian . ' - ' : '' }}{{ $bp->divisi }}
                                </span>
                            </td>
                            <td class="py-2 px-2 font-mono-report text-[10px] text-slate-600">
                                {{ $bp->kode_perkiraan ?: '6.00.01.00.01.04' }}
                            </td>
                            <td class="py-2 px-2 font-bold text-slate-900 truncate max-w-[130px]">
                                {{ $bp->pemohon }}
                            </td>
                            <td class="py-2 px-2 text-slate-700 text-xs leading-relaxed max-w-xs">
                                {{ $bp->keperluan }}
                            </td>
                            <td class="py-2 px-2 text-right font-mono-report text-slate-700">
                                {{ number_format($bp->nominal_pengajuan, 2, ',', '.') }}
                            </td>
                            <td class="py-2 px-2 text-right font-mono-report font-bold text-slate-900">
                                {{ number_format($bp->nominal_acc, 2, ',', '.') }}
                            </td>
                            <td class="py-2 px-2 text-center font-mono-report font-bold">
                                @if($bp->kasKeluar)
                                    <span class="text-blue-700">{{ $bp->kasKeluar->no_bkk }}</span>
                                @elseif($bp->status === 'dicairkan_kasir')
                                    <span class="text-emerald-700">TERBAYAR</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-2 px-2 text-center">
                                @if($bp->status === 'dicairkan_kasir')
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        ✓ CAIR KAS
                                    </span>
                                @elseif($bp->status === 'approved')
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                        SIAP CAIR
                                    </span>
                                @elseif($bp->status === 'rejected')
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                                        DITOLAK
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                        PENDING
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="py-8 text-center text-slate-400 font-medium">
                                Tidak ada data bukti pengeluaran (BP) pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 border-t-2 border-b-2 border-slate-900 text-[11px] font-black uppercase text-slate-900">
                        <td class="py-2.5 px-2 text-center"></td>
                        <td class="py-2.5 px-2"></td>
                        <td class="py-2.5 px-2"></td>
                        <td class="py-2.5 px-2"></td>
                        <td class="py-2.5 px-2"></td>
                        <td class="py-2.5 px-2"></td>
                        <td class="py-2.5 px-2"></td>
                        <td class="py-2.5 px-2 tracking-wider font-extrabold text-slate-900">
                            TOTAL ...
                        </td>
                        <td class="py-2.5 px-2 text-right font-mono-report font-black text-slate-700">
                            {{ number_format($totalPengajuan, 2, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-2 text-right font-mono-report font-black text-purple-900">
                            {{ number_format($totalAcc, 2, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-2 text-center font-mono-report font-black text-emerald-800">
                            Cair: {{ number_format($totalCair, 0, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-2 text-center font-mono-report font-black text-amber-800">
                            Pending: {{ number_format($totalPending, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Kolom Tanda Tangan Resmi Sesuai Format Rekap Priangan -->
        <div class="mt-10 grid grid-cols-2 gap-8 text-xs text-slate-800">
            <div>
                <p class="font-medium text-slate-600">Tasikmalaya, {{ date('d-m-Y') }}</p>
                <p class="font-bold text-slate-800 mt-0.5">Ass. Anggaran &amp; Keuangan,</p>
                <div class="h-16"></div>
                <p class="font-black tracking-wider uppercase underline underline-offset-4 text-slate-900 text-sm">
                    ELI YULIANTI
                </p>
            </div>

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
