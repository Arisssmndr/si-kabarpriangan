<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAPORAN REALISASI ANGGARAN PRIANGAN - BIDANG {{ $kodeBagian }} {{ strtoupper($namaBidang) }}</title>
    <link rel="icon" href="{{ asset('kabarpriangan.png') }}" type="image/png">
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, sans-serif;
        }
        .font-dotmatrix {
            font-family: 'Consolas', 'Courier New', Courier, Monaco, monospace;
            letter-spacing: -0.02em;
        }
        @page {
            size: A4 portrait;
            margin: 8mm;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; color: #000 !important; }
            .report-card { box-shadow: none !important; border: none !important; padding: 0 !important; max-width: 100% !important; margin: 0 !important; }
            table th, table td { color: #000 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-3 sm:p-6 min-h-screen">

    <!-- Top Action Bar (Non-Printable) -->
    <div class="max-w-[1020px] mx-auto mb-4 flex flex-wrap items-center justify-between gap-3 no-print">
        <div class="flex items-center gap-3">
            <a href="{{ route('keuangan.anggaran.cetak.per-bidang', ['kode_bagian' => $kodeBagian, 'periode' => $periode]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 shadow-xs transition">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                <span>Kembali ke Tampilan Web</span>
            </a>
            <span class="text-xs text-slate-500 font-medium hidden sm:inline">
                Sistem DOS ANGGARAN.EXE &bull; <strong>Laporan #2 Realisasi Anggaran Per Bidang (01 s/d 21)</strong>
            </span>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-sm hover:shadow transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Lembar Dokumen (Print / PDF)</span>
            </button>
        </div>
    </div>

    <!-- Lembar Cetak Laporan Resmi Sesuai Format Fisik Continuous Form Dot-Matrix -->
    <div class="max-w-[1020px] mx-auto bg-white border border-slate-300 rounded-2xl shadow-sm p-6 sm:p-8 report-card font-dotmatrix">
        
        <!-- Header Dokumen Persis Format Fisik Cetak DOS Kabar Priangan -->
        <div class="pb-2 border-b-2 border-slate-900 leading-tight">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-sm font-black tracking-wide uppercase text-slate-900">
                        PT. BERKAH PIKIRAN RAKYAT
                    </h2>
                    <h1 class="text-base sm:text-lg font-black tracking-wide uppercase text-slate-900 mt-0.5">
                        LAPORAN REALISASI ANGGARAN PRIANGAN
                    </h1>
                    <div class="text-xs font-bold uppercase text-slate-800 mt-0.5">
                        BULAN {{ strtoupper(\Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y')) }}
                    </div>
                    <div class="text-xs font-bold uppercase text-purple-900 mt-0.5">
                        BIDANG {{ $kodeBagian }} {{ strtoupper($namaBidang) }}
                    </div>
                </div>
                <div class="text-right text-[11px] text-slate-600 space-y-0.5">
                    <div class="font-bold border border-slate-400 px-2 py-0.5 rounded bg-slate-50 inline-block">KODE : CETAK-02</div>
                    <div>Dicetak: {{ date('d/m/Y H:i') }}</div>
                    <div>Operator: {{ Auth::user()->name ?? 'Staf Anggaran' }}</div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Anggaran Bidang (RAB vs Realisasi vs Sisa) -->
        <div class="my-3 py-2 px-3 border border-slate-300 bg-slate-50/70 rounded-lg flex flex-wrap justify-between items-center text-xs">
            <div>
                <span class="text-slate-500 text-[10px] uppercase block font-semibold">Plafon RAB Bulan Ini:</span>
                <strong class="text-slate-900 text-sm">Rp {{ number_format($plafon, 0, ',', '.') }}</strong>
            </div>
            <div>
                <span class="text-slate-500 text-[10px] uppercase block font-semibold">Total Realisasi BP:</span>
                <strong class="text-purple-800 text-sm">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</strong>
            </div>
            <div>
                <span class="text-slate-500 text-[10px] uppercase block font-semibold">Sudah Cair di Kasir:</span>
                <strong class="text-slate-900 text-sm">Rp {{ number_format($totalCair, 0, ',', '.') }}</strong>
            </div>
            <div>
                <span class="text-slate-500 text-[10px] uppercase block font-semibold">Sisa Plafon Anggaran:</span>
                <strong class="text-sm {{ $sisaPlafon >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                    Rp {{ number_format($sisaPlafon, 0, ',', '.') }}
                </strong>
            </div>
        </div>

        <!-- Garis Pembatas Header Tabel Dot Matrix (Double Line) -->
        <div class="text-slate-400 text-xs tracking-tighter overflow-hidden select-none whitespace-nowrap">
            ========================================================================================================================
        </div>

        <!-- TABEL 1: BAGAN AKUN PERKIRAAN (COA) BIDANG INI (PERSIS SEPERTI DI KERTAS CONTINUOUS FORM) -->
        <div class="overflow-x-auto my-1">
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr class="text-slate-900 uppercase text-[11px] font-black border-b border-slate-400">
                        <th class="py-1.5 px-2 w-48">KODE PERKIRAAN</th>
                        <th class="py-1.5 px-2">PERKIRAAN</th>
                        <th class="py-1.5 px-2 text-right w-36">ANGGARAN (RAB)</th>
                        <th class="py-1.5 px-2 text-right w-32">REALISASI BP</th>
                        <th class="py-1.5 px-2 text-right w-36">LEBIH / (KURANG)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-[11px]">
                    @php $sumCoaAlokasi = 0; @endphp
                    @foreach($coaList as $c)
                        @php $sumCoaAlokasi += $c['alokasi']; @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="py-1 px-2 font-bold text-slate-800 font-mono">{{ $c['kode'] }}</td>
                            <td class="py-1 px-2 text-slate-900 font-medium">{{ $c['nama'] }}</td>
                            <td class="py-1 px-2 text-right text-slate-900 font-bold">
                                {{ $c['alokasi'] > 0 ? number_format($c['alokasi'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-1 px-2 text-right text-purple-800 font-bold">
                                {{-- Jika ada transaksi BP yang mencocoki perkiraan ini --}}
                                @php
                                    $realisasiCoa = (float) $bpList->where('kode_perkiraan', $c['kode'])->sum('nominal_acc');
                                @endphp
                                {{ $realisasiCoa > 0 ? number_format($realisasiCoa, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-1 px-2 text-right text-emerald-700 font-bold">
                                {{ $c['alokasi'] > 0 ? number_format($c['alokasi'] - ($realisasiCoa ?? 0), 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t-2 border-slate-900 text-xs font-black">
                    <tr>
                        <td colspan="2" class="py-2 px-2 uppercase">TOTAL ALOKASI PERKIRAAN BIDANG {{ $kodeBagian }}</td>
                        <td class="py-2 px-2 text-right">Rp {{ number_format($sumCoaAlokasi, 0, ',', '.') }}</td>
                        <td class="py-2 px-2 text-right text-purple-800">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</td>
                        <td class="py-2 px-2 text-right text-emerald-700">Rp {{ number_format($sumCoaAlokasi - $totalRealisasi, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Garis Pemisah Antar Bagian -->
        <div class="text-slate-400 text-xs tracking-tighter overflow-hidden select-none whitespace-nowrap my-2">
            ------------------------------------------------------------------------------------------------------------------------
        </div>

        <!-- TABEL 2: RINCIAN DOKUMEN BUKTI PENGELUARAN (BP) YANG DITERBITKAN -->
        <div class="mt-4">
            <h3 class="text-xs font-black uppercase text-slate-900 mb-2 flex items-center justify-between">
                <span>DAFTAR BUKTI PENGELUARAN (BP) TEREGISTRASI BULAN INI:</span>
                <span class="text-[11px] font-normal text-slate-500">{{ $bpList->count() }} Dokumen Terdaftar</span>
            </h3>

            <table class="w-full text-xs text-left border-collapse border border-slate-300">
                <thead>
                    <tr class="bg-slate-100 text-[10px] font-black uppercase border-b border-slate-300 text-slate-800">
                        <th class="py-1.5 px-2 border-r border-slate-300 w-8 text-center">No</th>
                        <th class="py-1.5 px-2 border-r border-slate-300 w-24">Tanggal</th>
                        <th class="py-1.5 px-2 border-r border-slate-300 w-32">No. BP / Reg</th>
                        <th class="py-1.5 px-2 border-r border-slate-300">Pemohon &amp; Keperluan Belanja</th>
                        <th class="py-1.5 px-2 border-r border-slate-300 text-right w-28">Jumlah (Rp)</th>
                        <th class="py-1.5 px-2 text-center w-24">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-[11px]">
                    @forelse($bpList as $idx => $bp)
                        <tr>
                            <td class="py-1.5 px-2 border-r border-slate-300 text-center">{{ $idx + 1 }}</td>
                            <td class="py-1.5 px-2 border-r border-slate-300">{{ \Carbon\Carbon::parse($bp->tanggal_pengajuan)->format('d/m/Y') }}</td>
                            <td class="py-1.5 px-2 border-r border-slate-300 font-bold text-purple-900">
                                <div>{{ $bp->no_bp }}</div>
                                <div class="text-[9px] text-slate-400">PR: {{ $bp->no_register_anggaran ?? '-' }}</div>
                            </td>
                            <td class="py-1.5 px-2 border-r border-slate-300">
                                <strong>{{ $bp->pemohon }}</strong>: {{ $bp->keperluan }}
                            </td>
                            <td class="py-1.5 px-2 border-r border-slate-300 text-right font-bold text-slate-900">
                                {{ number_format($bp->nominal_acc ?? $bp->nominal_pengajuan, 0, ',', '.') }}
                            </td>
                            <td class="py-1.5 px-2 text-center text-[10px] font-bold uppercase">
                                {{ $bp->status === 'dicairkan_kasir' ? 'CAIR KASIR' : ($bp->status === 'approved' ? 'ACC MANAJER' : $bp->status) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-slate-400">
                                Belum ada dokumen Bukti Pengeluaran (BP) yang diterbitkan untuk bidang ini pada periode bersangkutan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-400 font-black text-xs">
                    <tr>
                        <td colspan="4" class="py-2 px-2 border-r border-slate-300 text-center uppercase">
                            TOTAL REALISASI PENGELUARAN BIDANG {{ $kodeBagian }} ({{ strtoupper($namaBidang) }})
                        </td>
                        <td class="py-2 px-2 border-r border-slate-300 text-right text-purple-900">
                            Rp {{ number_format($totalRealisasi, 0, ',', '.') }}
                        </td>
                        <td class="py-2 px-2 text-center text-[10px] text-slate-500">
                            {{ $bpList->count() }} Dokumen
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Tanda Tangan Resmi 3 Pihak (SOP HU Kabar Priangan) -->
        <div class="mt-8 pt-4 border-t border-slate-300 grid grid-cols-3 text-center text-xs text-slate-800 gap-4" style="page-break-inside: avoid;">
            <div>
                <p class="font-medium text-slate-500">Dibuat Oleh,</p>
                <p class="font-bold uppercase text-slate-900 mt-0.5">Petugas Bagian Anggaran</p>
                <div class="h-16 flex items-center justify-center">
                    <span class="text-[10px] text-slate-300 italic">[Tanda Tangan]</span>
                </div>
                <p class="font-bold underline text-slate-900">{{ Auth::user()->name ?? 'Staf Anggaran' }}</p>
                <p class="text-[10px] text-slate-500 mt-0.5">Staf Bagian Anggaran</p>
            </div>

            <div>
                <p class="font-medium text-slate-500">Diperiksa &amp; Disetujui,</p>
                <p class="font-bold uppercase text-slate-900 mt-0.5">Manajer Keuangan</p>
                <div class="h-16 flex items-center justify-center">
                    <span class="text-[10px] text-slate-300 italic">[Tanda Tangan]</span>
                </div>
                <p class="font-bold underline text-slate-900">RANI ANGGRAENI, SE</p>
                <p class="text-[10px] text-slate-500 mt-0.5">Ka. Divisi Keuangan</p>
            </div>

            <div>
                <p class="font-medium text-slate-500">Mengetahui,</p>
                <p class="font-bold uppercase text-slate-900 mt-0.5">Pimpinan Perusahaan</p>
                <div class="h-16 flex items-center justify-center">
                    <span class="text-[10px] text-slate-300 italic">[Tanda Tangan]</span>
                </div>
                <p class="font-bold underline text-slate-900">Drs. H. DUDI SUPRIYADI</p>
                <p class="text-[10px] text-slate-500 mt-0.5">Pemimpin Umum / Redaksi</p>
            </div>
        </div>

    </div>

</body>
</html>
