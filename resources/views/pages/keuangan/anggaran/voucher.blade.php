<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Kas Keluar (BKK / KO) - {{ $pengajuan->buktiPengeluaran?->kasKeluar?->no_bkk ?? $pengajuan->no_pengajuan }}</title>
    <link rel="icon" href="{{ asset('kabarpriangan.png') }}" type="image/png">
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-border { border: 2px solid #0f172a !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans p-4 sm:p-8 antialiased">

    <!-- Tombol Navigasi & Cetak (Non-Printable) -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('keuangan.anggaran.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs">
            ← Kembali ke Modul Anggaran
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-black shadow-md transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            <span>Cetak Bukti Kas Keluar (KO)</span>
        </button> 
    </div>

    @php
        $bp = $pengajuan->buktiPengeluaran;
        $kk = $bp?->kasKeluar;
        $noBkk = $kk?->no_bkk ?: ('KO-' . date('Ym') . str_pad($pengajuan->id, 4, '0', STR_PAD_LEFT));
        $nominalDisetujui = (float) ($pengajuan->nominal_disetujui ?? $pengajuan->nominal_diajukan);
        $nominalDiajukan = (float) $pengajuan->nominal_diajukan;
        $tanggalCair = $pengajuan->tanggal_cair ? $pengajuan->tanggal_cair->translatedFormat('d F Y') : date('d F Y');
    @endphp

    <!-- Lembar Voucher Resmi Standar Akuntansi Perusahaan -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-10 rounded-2xl border-2 border-slate-900 shadow-xl print-border">
        
        <!-- Header / Kop Surat Resmi -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-4 mb-5">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-12 w-auto object-contain shrink-0" onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <h1 class="text-base font-black text-slate-900 uppercase tracking-tight">HARIAN UMUM KABAR PRIANGAN</h1>
                    <p class="text-[11px] font-bold text-slate-700">DIVISI KEUANGAN &bull; BAGIAN TREASURY &amp; KASIR</p>
                    <p class="text-[10px] text-slate-500">Jl. Dr. Sukardjo No. 51 Kota Tasikmalaya | Telp: (0265) 331234</p>
                </div>
            </div>

            <!-- Box Identitas Dokumen Kanan Atas -->
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded text-xs font-black uppercase tracking-wider bg-amber-400 text-amber-950 border border-amber-500/40 mb-1.5 shadow-2xs">
                    BUKTI KAS KELUAR (KO)
                </span>
                <table class="text-[11px] font-mono text-right ml-auto border-separate border-spacing-y-0.5">
                    <tr>
                        <td class="text-slate-500 font-sans pr-2">No. BKK (KO) :</td>
                        <td class="font-black text-slate-900 text-xs">{{ $noBkk }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 font-sans pr-2">No. Register :</td>
                        <td class="font-bold text-blue-800">{{ $bp?->no_register_anggaran ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 font-sans pr-2">No. Bukti BP :</td>
                        <td class="font-bold text-slate-700">{{ $bp?->no_bp ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 font-sans pr-2">Tanggal Cair :</td>
                        <td class="font-medium text-slate-700">{{ $tanggalCair }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Judul Dokumen -->
        <div class="text-center mb-5">
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider underline underline-offset-4">
                BUKTI PENGELUARAN KAS KELUAR (CASH OUT)
            </h2>
            <p class="text-[11px] text-slate-500 mt-0.5">
                Surat Perintah &amp; Realisasi Pembayaran Kas Operasional Kantor
            </p>
        </div>

        <!-- Tabel Rincian Pembayaran (Grid Bersih & Rapi) -->
        <table class="w-full text-xs border border-slate-300 mb-5">
            <tbody class="divide-y divide-slate-200">
                <tr class="bg-slate-50/50">
                    <td class="w-48 py-2.5 px-3.5 font-bold text-slate-600 border-r border-slate-200">
                        Dibayarkan Kepada
                    </td>
                    <td class="py-2.5 px-3.5">
                        <strong class="text-slate-900 text-sm uppercase">{{ $bp?->pemohon ?: ($pengajuan->creator->name ?? 'Pemohon') }}</strong>
                        <span class="text-slate-500 ml-1.5 font-semibold">({{ $pengajuan->nama_divisi }})</span>
                    </td>
                </tr>
                <tr>
                    <td class="py-2.5 px-3.5 font-bold text-slate-600 border-r border-slate-200">
                        Kode Perkiraan (COA)
                    </td>
                    <td class="py-2.5 px-3.5 font-mono">
                        <span class="font-bold text-blue-900">{{ $bp?->kode_perkiraan ?: '6.00.01.00.01.04' }}</span>
                        <span class="text-slate-700 font-sans ml-1.5">&mdash; {{ $bp?->nama_perkiraan ?: ('Beban Operasional ' . $pengajuan->nama_divisi) }}</span>
                    </td>
                </tr>
                <tr class="bg-slate-50/50">
                    <td class="py-2.5 px-3.5 font-bold text-slate-600 border-r border-slate-200">
                        Untuk Keperluan
                    </td>
                    <td class="py-2.5 px-3.5 text-slate-800 leading-relaxed font-medium">
                        {{ $pengajuan->keterangan }}
                    </td>
                </tr>
                <tr>
                    <td class="py-2.5 px-3.5 font-bold text-slate-600 border-r border-slate-200">
                        Metode Pembayaran
                    </td>
                    <td class="py-2.5 px-3.5 font-medium text-slate-800">
                        {{ $kk?->metode_bayar === 'transfer' ? 'Transfer Bank Operasional' : 'Kas Fisik / Kas Tunai Kasir' }}
                    </td>
                </tr>
                <tr class="bg-amber-50/60 border-t-2 border-slate-300">
                    <td class="py-3 px-3.5 font-black text-amber-950 border-r border-amber-200/80 uppercase">
                        Jumlah Uang Keluar
                    </td>
                    <td class="py-3 px-3.5">
                        <div class="flex items-baseline gap-2">
                            <span class="font-mono text-xl font-black text-slate-900">
                                Rp {{ number_format($nominalDisetujui, 0, ',', '.') }}
                            </span>
                            @if($nominalDisetujui != $nominalDiajukan)
                                <span class="text-[11px] text-slate-500">
                                    (Semula diajukan: Rp {{ number_format($nominalDiajukan, 0, ',', '.') }})
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Status Pembukuan Terintegrasi -->
        <div class="p-2.5 rounded-lg bg-blue-50/60 border border-blue-200 flex items-center justify-between text-[11px] text-blue-950 mb-6">
            <span class="font-semibold flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                Status: <strong>TELAH DICAIRKAN &amp; TERCATAT PADA BUKU KAS (TREASURY)</strong>
            </span>
            <span class="font-mono text-slate-600 text-[10px]">Doc Ref: {{ $pengajuan->no_pengajuan }}</span>
        </div>

        <!-- Kolom Tanda Tangan 4 Pihak Resmi (Rapi, Seimbang & Standar Kantor) -->
        <div class="border-t-2 border-slate-900 pt-4">
            <div class="text-right text-[11px] text-slate-600 mb-3">
                Tasikmalaya, {{ $tanggalCair }}
            </div>
            <table class="w-full text-center text-xs">
                <thead>
                    <tr class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="w-1/4 pb-16 font-semibold">Pemohon / Pengusul,</th>
                        <th class="w-1/4 pb-16 font-semibold">Disetujui Oleh,</th>
                        <th class="w-1/4 pb-16 font-semibold">Dibayarkan Kasir,</th>
                        <th class="w-1/4 pb-16 font-semibold">Penerima Uang,</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-2">
                            <div class="border-t border-slate-400 pt-1 font-bold text-slate-900 text-[11px]">
                                ( {{ $pengajuan->creator->name ?? 'Pemohon' }} )
                            </div>
                        </td>
                        <td class="px-2">
                            <div class="border-t border-slate-400 pt-1 font-bold text-slate-900 text-[11px]">
                                ( {{ $pengajuan->approver->name ?? 'H. Asep Ridwan, S.E.' }} )
                            </div>
                        </td>
                        <td class="px-2">
                            <div class="border-t border-slate-400 pt-1 font-bold text-slate-900 text-[11px]">
                                ( {{ $pengajuan->disburser->name ?? ($kk->kasir->name ?? 'Kasir Keuangan') }} )
                            </div>
                        </td>
                        <td class="px-2">
                            <div class="border-t border-slate-400 pt-1 font-bold text-slate-900 text-[11px]">
                                ( {{ $bp?->pemohon ?: '................................' }} )
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>
