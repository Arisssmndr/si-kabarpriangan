<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Resmi - {{ $pembayaran->nokwitansi }} - Kabar Priangan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @page {
            size: A5 landscape;
            margin: 6mm 8mm;
        }

        @media print {
            .no-print { display: none !important; }
            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                font-size: 10.5px !important;
                color: #0f172a !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .kwitansi-sheet {
                border: 1.5px solid #0f172a !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 14px 16px !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 auto !important;
                page-break-inside: avoid !important;
            }
            .stub-section {
                border-right: 1.5px dashed #475569 !important;
            }
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        /* Security Guilloche Border Ornament */
        .security-border {
            background-image: radial-gradient(#0E527B 0.5px, transparent 0.5px);
            background-size: 6px 6px;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-6 px-3 text-slate-800 antialiased selection:bg-sky-100 selection:text-sky-900">

    @php
        $tx = $transaksi ?? $pembayaran->transaksi;
        $mediaType = strtolower($pembayaran->media_type ?? 'koran');
        $mediaLabel = match($mediaType) {
            'koran'    => 'Koran Cetak',
            'online'   => 'Media Online',
            'priangan' => 'Priangan TV',
            default    => 'Koran Cetak',
        };

        $backRoute = match($mediaType) {
            'online'   => route('transaksionline.index'),
            'priangan' => route('transaksipriangan.index'),
            default    => route('transaksikoran.index'),
        };

        // Rekonsiliasi Finansial
        $totalTagihan = 0;
        $totalBayar = 0;
        $sisaPiutang = 0;
        $namaJenis = '-';

        if ($tx) {
            if ($mediaType === 'koran') {
                $totalTagihan = (int) $tx->totaltagihan_transaksikoran;
                $totalBayar   = (int) $tx->jumlahbayar_transaksikoran;
                $sisaPiutang  = (int) $tx->piutang_transaksikoran;
                $namaJenis    = $tx->iklankoran->nama_jenis ?? $tx->kategori_form ?? 'Koran';
            } elseif ($mediaType === 'online') {
                $totalTagihan = (int) $tx->totaltagihan_transaksionline;
                $totalBayar   = (int) $tx->jumlahbayar_transaksionline;
                $sisaPiutang  = (int) $tx->piutang_transaksionline;
                $namaJenis    = $tx->iklanonline->nama_jenis ?? 'Online Banner / Advertorial';
            } elseif ($mediaType === 'priangan') {
                $totalTagihan = (int) $tx->totaltagihan_transaksipriangan;
                $totalBayar   = (int) $tx->jumlahbayar_transaksipriangan;
                $sisaPiutang  = (int) $tx->piutang_transaksipriangan;
                $namaJenis    = $tx->iklanpriangan->nama_jenis ?? 'Spot TV / Running Text';
            }
        }

        $isLunas = $sisaPiutang <= 0;
        $isMeterai = $pembayaran->nominal_bayar >= 5000000;

        // Fungsi Terbilang Rupiah
        if (!function_exists('terbilangRupiah')) {
            function terbilangRupiah($angka) {
                $angka = abs((float) $angka);
                $huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
                if ($angka < 12) {
                    return " " . $huruf[$angka];
                } elseif ($angka < 20) {
                    return terbilangRupiah($angka - 10) . " Belas";
                } elseif ($angka < 100) {
                    return terbilangRupiah(intval($angka / 10)) . " Puluh" . terbilangRupiah($angka % 10);
                } elseif ($angka < 200) {
                    return " Seratus" . terbilangRupiah($angka - 100);
                } elseif ($angka < 1000) {
                    return terbilangRupiah(intval($angka / 100)) . " Ratus" . terbilangRupiah($angka % 100);
                } elseif ($angka < 2000) {
                    return " Seribu" . terbilangRupiah($angka - 1000);
                } elseif ($angka < 1000000) {
                    return terbilangRupiah(intval($angka / 1000)) . " Ribu" . terbilangRupiah($angka % 1000);
                } elseif ($angka < 1000000000) {
                    return terbilangRupiah(intval($angka / 1000000)) . " Juta" . terbilangRupiah($angka % 1000000);
                } elseif ($angka < 1000000000000) {
                    return terbilangRupiah(intval($angka / 1000000000)) . " Milyar" . terbilangRupiah(fmod($angka, 1000000000));
                }
                return "";
            }
        }
    @endphp

    <!-- Bar Navigasi Aksi (Hanya di Layar, Tersembunyi Saat Dicetak) -->
    <div class="no-print max-w-[960px] mx-auto mb-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <a href="{{ $backRoute }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-2xs transition">
                &larr; <span>Kembali ke Transaksi</span>
            </a>
            <a href="{{ route('pembayaran-iklan.index') }}" 
               class="inline-flex items-center gap-1 px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-medium rounded-lg shadow-2xs transition">
                <span>Daftar Kwitansi Kasir</span>
            </a>
        </div>
        
        <div class="flex items-center gap-2">
            <span class="text-[11px] text-slate-500 font-medium hidden sm:inline">
                Format Cetak: <strong>A5 Landscape (Standar Continuous Form Pers)</strong>
            </span>
            <button type="button" 
                    onclick="window.print()" 
                    class="inline-flex items-center gap-2 px-4 py-1.5 bg-[#0E527B] hover:bg-[#0B4365] text-white text-xs font-bold rounded-lg shadow-sm hover:shadow transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Cetak Kwitansi / PDF</span>
            </button>
        </div>
    </div>

    <!-- Container Lembar Kwitansi Resmi (Desain Standar Industri Pers Modern) -->
    <div class="kwitansi-sheet max-w-[960px] mx-auto bg-white border border-slate-300 rounded-xl shadow-md p-5 relative overflow-hidden">

        <!-- Grid 2 Sisi: Sisi Kiri (Bonggol / Arsip Kasir) + Sisi Kanan (Kwitansi Asli Klien) -->
        <div class="grid grid-cols-12 gap-0 relative">

            <!-- ======================================================== -->
            <!-- 1. SISI KIRI: BONGGOL KASIR / ARSIP PERFORASI (25% LEBAR) -->
            <!-- ======================================================== -->
            <div class="col-span-3 pr-4 border-r-2 border-dashed border-slate-300 flex flex-col justify-between stub-section relative select-none">
                
                <!-- Ikon Perforasi Gunting Cetak -->
                <div class="absolute -right-2.5 top-1/2 -translate-y-1/2 bg-white text-slate-400 text-[10px] py-1 font-mono hidden sm:block">
                    ✂
                </div>

                <div>
                    <!-- Header Bonggol -->
                    <div class="border-b border-slate-200 pb-2 mb-2.5">
                        <span class="text-[9px] font-extrabold uppercase tracking-wider text-[#0E527B] block">
                            BONGGOL KASIR
                        </span>
                        <span class="text-[8.5px] font-semibold text-slate-400 block uppercase">
                            Arsip Kasir & Keuangan
                        </span>
                    </div>

                    <!-- Data Ringkas Bonggol -->
                    <div class="space-y-1.5 text-[10px]">
                        <div>
                            <span class="text-slate-400 text-[9px] uppercase font-semibold block">No. Kwitansi</span>
                            <span class="font-mono font-bold text-slate-900 break-all leading-tight">
                                {{ $pembayaran->nokwitansi }}
                            </span>
                        </div>

                        <div>
                            <span class="text-slate-400 text-[9px] uppercase font-semibold block">No. Faktur</span>
                            <span class="font-mono font-bold text-[#0E527B] break-all leading-tight">
                                {{ $pembayaran->nofaktur }}
                            </span>
                        </div>

                        <div>
                            <span class="text-slate-400 text-[9px] uppercase font-semibold block">Tgl. Setor</span>
                            <span class="font-mono font-semibold text-slate-800">
                                {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') }}
                            </span>
                        </div>

                        <div>
                            <span class="text-slate-400 text-[9px] uppercase font-semibold block">Penyetor / Klien</span>
                            <span class="font-semibold text-slate-800 line-clamp-2 leading-tight">
                                {{ $pembayaran->nama_penyetor }}
                            </span>
                        </div>

                        <div>
                            <span class="text-slate-400 text-[9px] uppercase font-semibold block">Saluran Media</span>
                            <span class="font-bold text-slate-700">
                                {{ $mediaLabel }}
                            </span>
                        </div>

                        <div>
                            <span class="text-slate-400 text-[9px] uppercase font-semibold block">Metode Pembayaran</span>
                            <span class="font-semibold text-slate-700">
                                {{ str_replace('_', ' ', $pembayaran->metode_bayar) }}
                            </span>
                        </div>

                        <div class="pt-1 border-t border-slate-100">
                            <span class="text-slate-400 text-[9px] uppercase font-semibold block">Nominal Diterima</span>
                            <span class="font-mono font-extrabold text-xs text-slate-900 block">
                                Rp {{ number_format($pembayaran->nominal_bayar, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Kolom Paraf Kasir di Bonggol -->
                <div class="pt-3 border-t border-slate-200 mt-3 text-center">
                    <span class="text-[8.5px] font-bold text-slate-500 uppercase tracking-wider block">
                        Paraf Kasir
                    </span>
                    <div class="h-10 my-1 border border-dashed border-slate-200 rounded flex items-center justify-center text-[9px] text-slate-400 italic">
                        ( Validasi )
                    </div>
                    <span class="text-[9px] font-semibold text-slate-700 truncate block">
                        {{ $pembayaran->kasir->name ?? 'Kasir FO' }}
                    </span>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- 2. SISI KANAN: KWITANSI ASLI RESMI PEMBAYAR (75% LEBAR)  -->
            <!-- ======================================================== -->
            <div class="col-span-9 pl-5 flex flex-col justify-between">

                <div>
                    <!-- KOP PERUSAHAAN PERS RESMI (STANDAR PIKIRAN RAKYAT GROUP) -->
                    <div class="border-b-2 border-[#0E527B] pb-3 flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('kabarpriangan.png') }}" 
                                 alt="Kabar Priangan" 
                                 class="h-11 w-auto object-contain shrink-0"
                                 onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                            <div class="leading-tight">
                                <h1 class="text-sm font-extrabold text-slate-900 tracking-tight uppercase">
                                    HARIAN UMUM KABAR PRIANGAN
                                </h1>
                                <p class="text-[10px] font-bold text-[#0E527B]">
                                    PT BERKAH PIKIRAN RAKYAT • PIKIRAN RAKYAT MEDIA NETWORK (PRMN)
                                </p>
                                <p class="text-[9px] text-slate-500 mt-0.5">
                                    Jl. KHZ. Mustofa No. 343, Kota Tasikmalaya 46124 • Telp: (0265) 334567 • Fax: (0265) 334568
                                </p>
                                <p class="text-[8.5px] text-slate-400">
                                    NPWP: 01.543.829.1-425.000 • Izin Penerbitan Pers: Menkumham RI
                                </p>
                            </div>
                        </div>

                        <!-- Badge Kwitansi Resmi & No. Seri -->
                        <div class="text-right shrink-0">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-sky-50 border border-sky-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#0E527B]"></span>
                                <span class="font-extrabold text-[10px] text-[#0E527B] uppercase tracking-wider">
                                    KWITANSI RESMI
                                </span>
                            </div>
                            <div class="text-[9px] text-slate-400 font-medium mt-0.5">
                                Lembar 1: Asli (Untuk Pembayar)
                            </div>
                            <div class="font-mono text-xs font-extrabold text-slate-900 mt-1">
                                No: {{ $pembayaran->nokwitansi }}
                            </div>
                        </div>
                    </div>

                    <!-- BADGE WATERMARK LATAR BELAKANG HALUS -->
                    <div class="mt-3 space-y-2 text-xs">

                        <!-- 1. Telah Diterima Dari -->
                        <div class="grid grid-cols-12 gap-2 items-baseline">
                            <div class="col-span-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                Telah Diterima Dari
                            </div>
                            <div class="col-span-9 font-bold text-slate-900 text-xs border-b border-slate-200 pb-1">
                                {{ $pembayaran->nama_penyetor }}
                                @if($tx && !empty($tx->alamat_pemasangkoran) && $tx->alamat_pemasangkoran !== '-')
                                    <span class="text-[10px] font-normal text-slate-500 block">
                                        {{ $tx->alamat_pemasangkoran }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- 2. Uang Sejumlah (Terbilang Resmi) -->
                        <div class="grid grid-cols-12 gap-2 items-center">
                            <div class="col-span-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                Uang Sejumlah
                            </div>
                            <div class="col-span-9">
                                <div class="px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 font-semibold italic text-[#0E527B] text-[11px] leading-snug">
                                    "{{ trim(terbilangRupiah($pembayaran->nominal_bayar)) }} Rupiah"
                                </div>
                            </div>
                        </div>

                        <!-- 3. Untuk Pembayaran -->
                        <div class="grid grid-cols-12 gap-2 items-baseline">
                            <div class="col-span-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                Untuk Pembayaran
                            </div>
                            <div class="col-span-9 space-y-0.5 border-b border-slate-200 pb-1.5">
                                <div class="font-medium text-slate-900 text-[11px]">
                                    Pembayaran Iklan <strong>{{ $mediaLabel }}</strong> 
                                    • No. Faktur: <span class="font-mono font-bold text-[#0E527B]">{{ $pembayaran->nofaktur }}</span>
                                </div>
                                <div class="text-[10px] text-slate-600 flex flex-wrap items-center gap-x-3 gap-y-0.5">
                                    <span>Paket / Jenis: <strong>{{ $namaJenis }}</strong></span>
                                    <span>&bull;</span>
                                    <span>Metode: <strong>{{ str_replace('_', ' ', $pembayaran->metode_bayar) }}</strong></span>
                                    @if($pembayaran->catatan)
                                        <span>&bull;</span>
                                        <span class="italic text-slate-500">{{ $pembayaran->catatan }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- 4. Rincian Rekonsiliasi Finansial & Saldo Sisa (Standar Akuntansi Pers) -->
                        <div class="grid grid-cols-12 gap-2 items-center pt-0.5">
                            <div class="col-span-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                Rekonsiliasi Faktur
                            </div>
                            <div class="col-span-9">
                                <div class="grid grid-cols-4 gap-2 text-[10px] p-2 rounded-lg bg-slate-50 border border-slate-200 font-mono">
                                    <div>
                                        <span class="text-slate-400 block text-[9px] uppercase">Nilai Faktur</span>
                                        <span class="font-semibold text-slate-800">
                                            Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block text-[9px] uppercase">Setoran Ini</span>
                                        <span class="font-bold text-[#0E527B]">
                                            Rp {{ number_format($pembayaran->nominal_bayar, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block text-[9px] uppercase">PPh 23 Dipotong</span>
                                        <span class="font-semibold text-slate-600">
                                            Rp {{ number_format($pembayaran->potongan_pph23 ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-slate-400 block text-[9px] uppercase">Sisa Piutang</span>
                                        <span class="font-bold {{ $isLunas ? 'text-emerald-700' : 'text-rose-600' }}">
                                            @if($isLunas)
                                                Rp 0 (LUNAS)
                                            @else
                                                Rp {{ number_format($sisaPiutang, 0, ',', '.') }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- BAGIAN BAWAH: NOMINAL KOTAK, METERAI, TANDA TANGAN, DAN STEMPEL -->
                <div class="pt-3 border-t border-slate-200 mt-3">
                    <div class="grid grid-cols-12 gap-3 items-end">
                        
                        <!-- 1. Kotak Nominal Angka Tebal & QR Audit -->
                        <div class="col-span-5 flex items-center gap-3">
                            <div class="px-3.5 py-2 bg-slate-900 text-white rounded-lg border border-slate-800 shadow-2xs font-mono">
                                <span class="text-[8.5px] uppercase font-bold text-slate-400 block tracking-wider">
                                    JUMLAH DITERIMA:
                                </span>
                                <span class="text-base font-extrabold tracking-tight">
                                    Rp {{ number_format($pembayaran->nominal_bayar, 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- Mini QR Code Generator Simulasi Validasi Digital -->
                            <div class="text-center shrink-0">
                                <div class="w-12 h-12 bg-white border border-slate-300 p-0.5 rounded flex items-center justify-center">
                                    <!-- Dynamic SVG QR Motif -->
                                    <svg viewBox="0 0 24 24" class="w-full h-full text-slate-800" fill="currentColor">
                                        <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm14-2h4v2h-4v-2zm-4 0h2v4h-2v-4zm2 4h4v4h-4v-4zm2-2h2v2h-2v-2zm-6 2h2v2h-2v-2zm0 2h4v2h-4v-2zM5 5h2v2H5V5zm12 0h2v2h-2V5zM5 17h2v2H5v-2z"/>
                                    </svg>
                                </div>
                                <span class="text-[7.5px] font-mono text-slate-400 block uppercase mt-0.5">
                                    E-Verify
                                </span>
                            </div>
                        </div>

                        <!-- 2. Ruang Bea Meterai Resmi -->
                        <div class="col-span-3 text-center">
                            @if($isMeterai)
                                <div class="border border-dashed border-amber-400 bg-amber-50/60 rounded px-2 py-1.5 text-[8px] text-amber-900 leading-tight">
                                    <span class="font-bold block uppercase text-[8.5px]">Bea Meterai Rp 10.000</span>
                                    <span>Lunas Sesuai UU No. 10/2020</span>
                                </div>
                            @else
                                <div class="text-[8.5px] text-slate-400 italic leading-tight">
                                    Bebas Bea Meterai<br>(Transaksi &lt; Rp 5 Juta)
                                </div>
                            @endif
                        </div>

                        <!-- 3. Tanda Tangan Kasir & Stempel Digital -->
                        <div class="col-span-4 text-center relative">
                            <span class="text-[9.5px] text-slate-500 font-medium block">
                                Tasikmalaya, {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->translatedFormat('d F Y') }}
                            </span>
                            <span class="text-[9px] font-bold text-slate-700 uppercase tracking-wider block">
                                Kasir / Bagian Keuangan
                            </span>

                            <!-- Stempel Digital Resmi Bulat -->
                            <div class="relative h-12 flex items-center justify-center my-0.5">
                                <div class="w-24 h-11 rounded-full border-2 border-emerald-600/80 text-emerald-700 font-extrabold text-[8.5px] uppercase tracking-wider flex flex-col items-center justify-center rotate-[-4deg] shadow-2xs select-none">
                                    <span class="text-[7px] text-emerald-800 leading-none">★ KABAR PRIANGAN ★</span>
                                    <span class="font-black text-[10px] text-emerald-600 tracking-tight leading-tight">LUNAS</span>
                                    <span class="text-[6.5px] font-mono text-emerald-800 leading-none">{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d.m.Y') }} - KASIR</span>
                                </div>
                            </div>

                            <div class="border-t border-slate-300 pt-0.5">
                                <span class="font-bold text-slate-900 text-[10px] block leading-tight">
                                    {{ $pembayaran->kasir->name ?? 'Staf Divisi Iklan' }}
                                </span>
                                <span class="text-[8px] text-slate-400 uppercase block">
                                    Harian Umum Kabar Priangan
                                </span>
                            </div>
                        </div>

                    </div>

                    <!-- KLAUSUL HUKUM RESMI PERS DI FOOTER -->
                    <div class="pt-2 mt-2 border-t border-slate-100 flex items-center justify-between text-[8px] text-slate-400">
                        <div>
                            <strong>Catatan:</strong> Kwitansi ini sah apabila pembayaran via Cek/Giro/Transfer telah efektif (kliring) di rekening penerbit.
                        </div>
                        <div class="font-mono">
                            SIKAPRI Enterprise &bull; Dicetak: {{ now()->format('d/m/Y H:i') }} WIB
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</body>
</html>
