<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher Kas Keluar - {{ $pengajuan->no_pengajuan }}</title>
    <link rel="icon" href="{{ asset('kabarpriangan.png') }}" type="image/png">
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-shadow { box-shadow: none !important; border: 1px solid #cbd5e1 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans p-4 sm:p-8">

    <!-- Tombol Navigasi Cetak -->
    <div class="max-w-3xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('keuangan.anggaran.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs">
            ← Kembali ke Modul Anggaran
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-md transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Bukti Voucher Kas Keluar</span>
        </button>
    </div>

    <!-- Lembar Voucher Resmi -->
    <div class="max-w-3xl mx-auto bg-white p-8 sm:p-12 rounded-2xl border border-slate-200 shadow-lg print-shadow">
        
        <!-- Kop Surat -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-5 mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-12 w-auto object-contain shrink-0" onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <h1 class="text-base font-black text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</h1>
                    <p class="text-xs text-slate-600">DIVISI KEUANGAN &bull; BAGIAN TREASURY &amp; BUKU KAS</p>
                    <p class="text-[11px] text-slate-500">Jl. Dr. Sukardjo No. 51 Kota Tasikmalaya | Telp: (0265) 331234</p>
                </div>
            </div>

            <div class="text-right">
                <span class="text-xs font-mono font-black uppercase text-purple-700 bg-purple-50 px-2.5 py-1 rounded border border-purple-200 block mb-1">
                    VOUCHER KAS KELUAR
                </span>
                <span class="text-xs font-mono font-bold text-slate-900">{{ $pengajuan->no_pengajuan }}</span>
            </div>
        </div>

        <div class="text-center mb-6">
            <h2 class="text-base font-black text-slate-900 uppercase tracking-wide underline underline-offset-4">
                BUKTI PENGELUARAN KAS KELUAR (CASH OUT)
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Tanggal Pencairan: {{ $pengajuan->tanggal_cair ? $pengajuan->tanggal_cair->translatedFormat('d F Y') : date('d F Y') }}
            </p>
        </div>

        <div class="text-xs text-slate-800 space-y-3 font-medium mb-6">
            <div class="flex pb-2 border-b border-slate-100">
                <span class="w-40 text-slate-500">Dibayarkan Kepada Divisi:</span>
                <strong class="text-purple-900 uppercase text-sm">{{ $pengajuan->nama_divisi }}</strong>
            </div>

            <div class="flex pb-2 border-b border-slate-100">
                <span class="w-40 text-slate-500">Jumlah Dana Dicairkan:</span>
                <div class="flex-1 bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <span class="text-lg font-black text-slate-900 font-mono">
                        Rp {{ number_format($pengajuan->nominal_disetujui ?? $pengajuan->nominal_diajukan, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-slate-500 block mt-0.5">
                        (Nominal Diajukan: Rp {{ number_format($pengajuan->nominal_diajukan, 0, ',', '.') }})
                    </span>
                </div>
            </div>

            <div class="flex pb-2 border-b border-slate-100">
                <span class="w-40 text-slate-500">Keperluan Pengeluaran:</span>
                <p class="flex-1 text-slate-800 leading-relaxed bg-white p-2 rounded border border-slate-100">
                    {{ $pengajuan->keterangan }}
                </p>
            </div>

            <div class="flex pb-2 border-b border-slate-100">
                <span class="w-40 text-slate-500">Status Pencairan Kas:</span>
                <span class="font-bold text-emerald-700 uppercase">
                    ✓ TELAH DICAIRKAN &amp; TERCATAT PADA BUKU KAS
                </span>
            </div>
        </div>

        <!-- Kolom Tanda Tangan 4 Pihak -->
        <div class="grid grid-cols-4 gap-4 text-center text-xs pt-8 border-t border-slate-200">
            <div>
                <span class="text-slate-500 block mb-14">Pemohon / Pengusul,</span>
                <div class="border-t border-slate-400 pt-1 font-bold text-slate-800 text-[11px]">
                    ( {{ $pengajuan->creator->name ?? 'Pemohon' }} )
                </div>
            </div>
            <div>
                <span class="text-slate-500 block mb-14">Disetujui Oleh,</span>
                <div class="border-t border-slate-400 pt-1 font-bold text-slate-800 text-[11px]">
                    ( {{ $pengajuan->approver->name ?? 'Pimpinan' }} )
                </div>
            </div>
            <div>
                <span class="text-slate-500 block mb-14">Dibayarkan Kasir,</span>
                <div class="border-t border-slate-400 pt-1 font-bold text-slate-800 text-[11px]">
                    ( {{ $pengajuan->disburser->name ?? 'Kasir Keuangan' }} )
                </div>
            </div>
            <div>
                <span class="text-slate-500 block mb-14">Penerima Dana,</span>
                <div class="border-t border-slate-400 pt-1 font-bold text-slate-800 text-[11px]">
                    ( ........................................ )
                </div>
            </div>
        </div>

    </div>

</body>
</html>
