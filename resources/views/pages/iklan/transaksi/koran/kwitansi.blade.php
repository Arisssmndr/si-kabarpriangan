<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran Iklan - {{ $pembayaran->nokwitansi }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; padding: 0 !important; font-size: 11px !important; }
            .print-card { border: none !important; box-shadow: none !important; padding: 0 !important; max-width: 100% !important; }
            @page { margin: 12mm 15mm; size: A5 landscape; }
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 text-slate-900">

    <!-- Action Bar (No-Print) -->
    <div class="no-print max-w-3xl mx-auto mb-6 px-4 flex items-center justify-between">
        <a href="{{ route('transaksikoran.index') }}" 
           class="px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-xs transition">
            &larr; Kembali ke Daftar Transaksi
        </a>
        <button onclick="window.print()" 
                class="px-4 py-2 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-lg shadow-xs transition flex items-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            <span>Cetak Kwitansi / PDF</span>
        </button>
    </div>

    @php
        function terbilang($angka) {
            $angka = abs($angka);
            $huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
            $hasil = "";
            if ($angka < 12) {
                $hasil = " " . $huruf[$angka];
            } else if ($angka < 20) {
                $hasil = terbilang($angka - 10) . " Belas";
            } else if ($angka < 100) {
                $hasil = terbilang(intval($angka / 10)) . " Puluh" . terbilang($angka % 10);
            } else if ($angka < 200) {
                $hasil = " Seratus" . terbilang($angka - 100);
            } else if ($angka < 1000) {
                $hasil = terbilang(intval($angka / 100)) . " Ratus" . terbilang($angka % 100);
            } else if ($angka < 2000) {
                $hasil = " Seribu" . terbilang($angka - 1000);
            } else if ($angka < 1000000) {
                $hasil = terbilang(intval($angka / 1000)) . " Ribu" . terbilang($angka % 1000);
            } else if ($angka < 1000000000) {
                $hasil = terbilang(intval($angka / 1000000)) . " Juta" . terbilang($angka % 1000000);
            } else if ($angka < 1000000000000) {
                $hasil = terbilang(intval($angka / 1000000000)) . " Milyar" . terbilang(fmod($angka, 1000000000));
            }
            return $hasil;
        }
    @endphp

    <!-- Kwitansi Card -->
    <div class="print-card max-w-3xl mx-auto bg-white border-2 border-slate-300 rounded-2xl shadow-sm p-8 space-y-6">

        <!-- Kop Surat -->
        <div class="border-b-2 border-kp-blue-600 pb-4 flex items-center justify-between">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-12 w-auto object-contain">
                <div>
                    <h1 class="text-base font-extrabold text-slate-900 tracking-tight uppercase">
                        HARIAN UMUM KABAR PRIANGAN
                    </h1>
                    <p class="text-[11px] text-slate-500 font-medium">
                        Divisi Keuangan & Kasir Periklanan • Pikiran Rakyat Group
                    </p>
                    <p class="text-[10px] text-slate-400">
                        Jl. KHZ. Mustofa No. 343, Kota Tasikmalaya • Telp. (0265) 334567
                    </p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-kp-blue-50 border border-kp-blue-300 rounded font-extrabold font-mono text-xs text-kp-blue-800 uppercase tracking-wider">
                    KWITANSI RESMI
                </span>
                <div class="text-xs font-mono font-bold text-slate-800 mt-1.5">
                    No: {{ $pembayaran->nokwitansi }}
                </div>
            </div>
        </div>

        <!-- Tabel Rincian Kwitansi -->
        <div class="text-xs space-y-3 font-medium text-slate-800">
            <div class="grid grid-cols-12 gap-2 border-b border-slate-200 pb-2">
                <span class="col-span-3 text-slate-500 uppercase font-semibold text-[11px]">Telah Diterima Dari</span>
                <span class="col-span-1 text-center">:</span>
                <span class="col-span-8 font-bold text-slate-900 text-sm">{{ $pembayaran->nama_penyetor }}</span>
            </div>

            <div class="grid grid-cols-12 gap-2 border-b border-slate-200 pb-2">
                <span class="col-span-3 text-slate-500 uppercase font-semibold text-[11px]">Uang Sejumlah</span>
                <span class="col-span-1 text-center">:</span>
                <span class="col-span-8 italic font-semibold text-kp-blue-900 bg-slate-50 p-2 rounded border border-slate-200">
                    "{{ trim(terbilang($pembayaran->nominal_bayar)) }} Rupiah"
                </span>
            </div>

            <div class="grid grid-cols-12 gap-2 border-b border-slate-200 pb-2">
                <span class="col-span-3 text-slate-500 uppercase font-semibold text-[11px]">Untuk Pembayaran</span>
                <span class="col-span-1 text-center">:</span>
                <div class="col-span-8 space-y-1">
                    <p class="text-slate-900">
                        Pembayaran Iklan Koran untuk Faktur No. <strong class="font-mono text-kp-blue-700">{{ $pembayaran->nofaktur }}</strong>
                    </p>
                    <p class="text-[11px] text-slate-500">
                        Metode: <strong>{{ str_replace('_', ' ', $pembayaran->metode_bayar) }}</strong>
                        @if($pembayaran->catatan)
                            • Catatan: {{ $pembayaran->catatan }}
                        @endif
                    </p>
                </div>
            </div>

            @if($pembayaran->potongan_pph23 > 0)
                <div class="grid grid-cols-12 gap-2 border-b border-slate-200 pb-2">
                    <span class="col-span-3 text-slate-500 uppercase font-semibold text-[11px]">Potongan PPh 23 (2%)</span>
                    <span class="col-span-1 text-center">:</span>
                    <span class="col-span-8 font-mono text-slate-700">
                        Rp {{ number_format($pembayaran->potongan_pph23, 0, ',', '.') }} (Dipungut Instansi Pemda/BUMN)
                    </span>
                </div>
            @endif
        </div>

        <!-- Nominal Kotak & Tanda Tangan -->
        <div class="pt-2 flex items-center justify-between">
            <div class="border-2 border-dashed border-slate-400 bg-slate-50 px-5 py-3 rounded-xl">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Jumlah Uang:</span>
                <span class="text-xl font-extrabold font-mono text-slate-900">
                    Rp {{ number_format($pembayaran->nominal_bayar, 0, ',', '.') }}
                </span>
            </div>

            <div class="text-center space-y-1 w-52">
                <span class="text-[11px] text-slate-500 block">
                    Tasikmalaya, {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->translatedFormat('d F Y') }}
                </span>
                <span class="text-[10px] font-bold text-slate-600 uppercase block">KASIR / PENERIMA</span>

                <div class="h-16 flex items-center justify-center">
                    <div class="border border-emerald-500 bg-emerald-50 text-emerald-800 text-[10px] font-bold px-3 py-1 rounded">
                        LUNAS KASIR KP
                    </div>
                </div>

                <p class="font-bold text-xs text-slate-900 uppercase">
                    ( {{ $pembayaran->kasir->name ?? 'Kasir Divisi Iklan' }} )
                </p>
                <span class="text-[10px] text-slate-400 block">Harian Umum Kabar Priangan</span>
            </div>
        </div>

    </div>

</body>
</html>
