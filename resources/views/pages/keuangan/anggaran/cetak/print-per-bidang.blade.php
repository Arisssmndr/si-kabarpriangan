<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REALISASI ANGGARAN {{ strtoupper($namaBidang) }} - HU. KABAR PRIANGAN</title>
    <link rel="icon" href="{{ asset('kabarpriangan.png') }}" type="image/png">
    
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
            size: A4 portrait;
            margin: 10mm;
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
                Sistem DOS ANGGARAN.EXE &bull; <strong>Laporan #2 Realisasi Per Bidang</strong>
            </span>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-sm hover:shadow transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Lembar Cetak Laporan Resmi -->
    <div class="max-w-[1020px] mx-auto bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-10 report-card">
        
        <!-- Header Dokumen Sesuai Format Resmi PT. Berkah Pikiran Rakyat / Kabar Priangan -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between pb-4 border-b-2 border-slate-900 mb-6 gap-3">
            <div>
                <p class="text-xs font-black text-slate-500 tracking-wider uppercase">
                    HU. KABAR PRIANGAN &bull; PT. BERKAH PIKIRAN RAKYAT
                </p>
                <h1 class="text-base sm:text-xl font-black text-slate-900 tracking-tight uppercase mt-0.5">
                    REALISASI ANGGARAN PER BIDANG PRIANGAN
                </h1>
                <div class="mt-1 flex items-center gap-2 text-xs text-slate-600">
                    <span class="font-bold text-slate-800">BIDANG :</span>
                    <span class="px-2 py-0.5 rounded-md bg-purple-50 font-semibold font-mono-report text-purple-900 border border-purple-200 uppercase">
                        {{ $namaBidang }}
                    </span>
                    <span class="text-slate-400">&bull;</span>
                    <span class="font-bold text-slate-800">PERIODE :</span>
                    <span class="font-mono-report uppercase font-bold text-slate-700">
                        {{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}
                    </span>
                </div>
            </div>
            <div class="text-right sm:self-start">
                <span class="inline-block px-3 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold font-mono-report border border-slate-200">
                    KODE : CETAK-02
                </span>
                <p class="text-[11px] text-slate-400 mt-1">
                    Dicetak: {{ date('d/m/Y H:i') }}
                </p>
            </div>
        </div>

        <!-- Ringkasan Anggaran Bidang -->
        <div class="mb-4 grid grid-cols-4 gap-3 text-xs font-mono-report bg-slate-50 p-3 rounded-xl border border-slate-200">
            <div>
                <span class="text-slate-400 text-[10px] block uppercase">Plafon RAB</span>
                <span class="font-bold text-slate-900">Rp {{ number_format($plafon, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] block uppercase">Realisasi BP</span>
                <span class="font-bold text-purple-700">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] block uppercase">Sisa Plafon</span>
                <span class="font-bold {{ $sisaPlafon >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">Rp {{ number_format($sisaPlafon, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] block uppercase">Cair Kasir</span>
                <span class="font-bold text-slate-900">Rp {{ number_format($totalCair, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Tabel Rincian BP -->
        <table class="w-full text-xs text-left border-collapse border border-slate-300">
            <thead>
                <tr class="bg-slate-100 border-b border-slate-300 text-slate-800 uppercase text-[10px] font-black font-mono-report">
                    <th class="py-2 px-2.5 border-r border-slate-300 w-8 text-center">No</th>
                    <th class="py-2 px-2.5 border-r border-slate-300 w-24">Tanggal</th>
                    <th class="py-2 px-2.5 border-r border-slate-300 w-28">No. BP</th>
                    <th class="py-2 px-2.5 border-r border-slate-300">Pemohon / Keterangan Keperluan</th>
                    <th class="py-2 px-2.5 border-r border-slate-300 text-right w-28">Jumlah (Rp)</th>
                    <th class="py-2 px-2.5 text-center w-24">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 font-mono-report">
                @forelse($bpList as $idx => $bp)
                    <tr>
                        <td class="py-2 px-2.5 border-r border-slate-300 text-center">
                            {{ $idx + 1 }}
                        </td>
                        <td class="py-2 px-2.5 border-r border-slate-300">
                            {{ \Carbon\Carbon::parse($bp->tanggal_pengajuan)->format('d/m/Y') }}
                        </td>
                        <td class="py-2 px-2.5 border-r border-slate-300 font-bold text-purple-900">
                            {{ $bp->no_bp }}
                        </td>
                        <td class="py-2 px-2.5 border-r border-slate-300 font-sans">
                            <span class="font-bold text-slate-900">{{ $bp->pemohon }}</span>: 
                            <span class="text-slate-600">{{ $bp->keperluan }}</span>
                        </td>
                        <td class="py-2 px-2.5 border-r border-slate-300 text-right font-bold text-slate-900">
                            {{ number_format($bp->nominal_acc ?? $bp->nominal_pengajuan, 0, ',', '.') }}
                        </td>
                        <td class="py-2 px-2.5 text-center uppercase text-[10px] font-bold">
                            {{ $bp->status === 'dicairkan_kasir' ? 'CAIR KASIR' : ($bp->status === 'approved' ? 'ACC' : $bp->status) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-400">
                            Tidak ada transaksi Bukti Pengeluaran (BP) pada bidang dan periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-slate-50 border-t-2 border-slate-400 font-mono-report font-black text-xs">
                <tr>
                    <td colspan="4" class="py-2.5 px-3 border-r border-slate-300 text-center uppercase">
                        TOTAL REALISASI BIDANG {{ strtoupper($namaBidang) }}
                    </td>
                    <td class="py-2.5 px-3 border-r border-slate-300 text-right">
                        Rp {{ number_format($totalRealisasi, 0, ',', '.') }}
                    </td>
                    <td class="py-2.5 px-3 text-center">
                        {{ $bpList->count() }} Dokumen
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Tanda Tangan Resmi 3 Pihak -->
        <div class="mt-12 pt-6 grid grid-cols-3 text-center text-xs text-slate-800 gap-4" style="page-break-inside: avoid;">
            <div>
                <p class="font-medium text-slate-500">Dibuat Oleh,</p>
                <p class="font-bold uppercase text-slate-900 mt-0.5">Petugas Anggaran</p>
                <div class="h-20 flex items-center justify-center">
                    <span class="text-[10px] text-slate-300 italic">[Tanda Tangan]</span>
                </div>
                <p class="font-bold underline text-slate-900 font-mono-report">{{ Auth::user()->name ?? 'Staf Anggaran' }}</p>
                <p class="text-[10px] text-slate-500 mt-0.5">Staf Bagian Anggaran</p>
            </div>

            <div>
                <p class="font-medium text-slate-500">Diperiksa Oleh,</p>
                <p class="font-bold uppercase text-slate-900 mt-0.5">Manajer Keuangan</p>
                <div class="h-20 flex items-center justify-center">
                    <span class="text-[10px] text-slate-300 italic">[Tanda Tangan]</span>
                </div>
                <p class="font-bold underline text-slate-900 font-mono-report">RANI ANGGRAENI, SE</p>
                <p class="text-[10px] text-slate-500 mt-0.5">Ka. Divisi Keuangan</p>
            </div>

            <div>
                <p class="font-medium text-slate-500">Mengetahui &amp; Menyetujui,</p>
                <p class="font-bold uppercase text-slate-900 mt-0.5">Pimpinan Perusahaan</p>
                <div class="h-20 flex items-center justify-center">
                    <span class="text-[10px] text-slate-300 italic">[Tanda Tangan]</span>
                </div>
                <p class="font-bold underline text-slate-900 font-mono-report">Drs. H. DUDI SUPRIYADI</p>
                <p class="text-[10px] text-slate-500 mt-0.5">Pemimpin Umum / Redaksi</p>
            </div>
        </div>

    </div>

</body>
</html>
