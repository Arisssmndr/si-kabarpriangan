<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - {{ $pembayaran->no_kwitansi }}</title>
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
    <div class="max-w-2xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('keuangan.inkaso.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs">
            ← Kembali ke Modul Inkaso
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Kwitansi (Print / PDF)</span>
        </button>
    </div>

    <!-- Lembar Kwitansi -->
    <div class="max-w-2xl mx-auto bg-white p-8 sm:p-10 rounded-2xl border border-slate-200 shadow-lg print-shadow">
        
        <!-- Kop Surat -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-4 mb-5">
            <div class="flex items-center gap-3">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-10 w-auto object-contain shrink-0" onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <h1 class="text-sm font-black text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</h1>
                    <p class="text-[10px] text-slate-500">Kwitansi Tanda Terima Pembayaran Resmi</p>
                </div>
            </div>

            <div class="text-right">
                <span class="text-xs font-mono font-black text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded border border-emerald-200">
                    KWITANSI
                </span>
                <span class="text-xs font-mono font-bold text-slate-900 block mt-1">{{ $pembayaran->no_kwitansi }}</span>
            </div>
        </div>

        <div class="text-xs text-slate-800 space-y-3 font-medium">
            <div class="flex pb-2 border-b border-slate-100">
                <span class="w-36 text-slate-500">Telah Terima Dari:</span>
                <strong class="text-slate-900 uppercase">{{ $pembayaran->faktur->customer->nama_customer ?? '-' }}</strong>
            </div>

            <div class="flex pb-2 border-b border-slate-100">
                <span class="w-36 text-slate-500">Uang Sejumlah:</span>
                <div class="flex-1 bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                    <span class="text-base font-black text-emerald-700 font-mono">
                        Rp {{ number_format($pembayaran->nominal_bayar, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-slate-500 block italic mt-0.5">
                        (Metode Pembayaran: {{ strtoupper($pembayaran->metode_bayar) }})
                    </span>
                </div>
            </div>

            <div class="flex pb-2 border-b border-slate-100">
                <span class="w-36 text-slate-500">Untuk Pembayaran:</span>
                <div class="flex-1 text-slate-700">
                    Setoran pelunasan Faktur No. <strong class="text-blue-700 font-mono">{{ $pembayaran->faktur->no_faktur }}</strong> (Divisi {{ ucfirst($pembayaran->faktur->kategori) }}).
                    @if($pembayaran->catatan)
                        <span class="block text-[11px] text-slate-500 mt-0.5">{{ $pembayaran->catatan }}</span>
                    @endif
                </div>
            </div>

            <div class="flex pb-2 border-b border-slate-100">
                <span class="w-36 text-slate-500">Sisa Piutang Faktur:</span>
                <strong class="{{ $pembayaran->faktur->sisa_piutang > 0 ? 'text-rose-600' : 'text-emerald-700' }} font-mono">
                    Rp {{ number_format($pembayaran->faktur->sisa_piutang, 0, ',', '.') }}
                    @if($pembayaran->faktur->sisa_piutang <= 0)
                        (LUNAS 100%)
                    @endif
                </strong>
            </div>
        </div>

        <!-- Tanda Tangan -->
        <div class="grid grid-cols-2 gap-8 text-center text-xs pt-8 mt-4 border-t border-slate-200">
            <div>
                <span class="text-slate-500 block mb-14">Petugas Penyetor / Inkaso,</span>
                <div class="border-t border-slate-400 pt-1 font-bold text-slate-800">
                    ( {{ $pembayaran->penugasanInkaso->petugas->name ?? 'Petugas Inkaso' }} )
                </div>
            </div>
            <div>
                <span class="text-slate-500 block mb-14">Tasikmalaya, {{ $pembayaran->tanggal_bayar->translatedFormat('d F Y') }}<br>Kasir Penerima,</span>
                <div class="border-t border-slate-400 pt-1 font-bold text-slate-800">
                    ( {{ $pembayaran->penerima->name ?? 'Kasir Keuangan' }} )
                </div>
            </div>
        </div>

    </div>

</body>
</html>
