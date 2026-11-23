<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $isSirkulasi = ($faktur->kategori === 'sirkulasi');
    @endphp
    <title>{{ $isSirkulasi ? 'Faktur Sirkulasi Koran' : 'Order Transaksi Iklan' }} - {{ $faktur->no_faktur }}</title>
    <link rel="icon" href="{{ asset('kabarpriangan.png') }}" type="image/png">
    @vite(['resources/css/app.css'])
    <style>
        @page {
            size: auto;
            margin: 10mm;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; color: black !important; }
            .dot-matrix-container {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            .tractor-feed { display: none !important; }
        }
        .font-dot-matrix {
            font-family: 'Courier New', Courier, monospace, 'Lucida Console', Monaco;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body class="bg-slate-200 text-slate-900 font-dot-matrix p-4 sm:p-8">

    <!-- Tombol Navigasi Cetak (Non-Printable) -->
    <div class="max-w-4xl mx-auto mb-4 flex items-center justify-between no-print">
        <a href="{{ $isSirkulasi ? route('keuangan.faktur.sirkulasi.index') : route('keuangan.faktur.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs font-sans">
            ← Kembali ke Daftar Faktur {{ $isSirkulasi ? 'Sirkulasi' : 'Iklan' }}
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition cursor-pointer font-sans">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Lembar {{ $isSirkulasi ? 'Faktur Sirkulasi' : 'Order Iklan' }} (Print / PDF)</span>
            </button>
        </div>
    </div>

    <!-- Lembar Dokumen Slip Dot Matrix Continuous Form -->
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-xl overflow-hidden border border-slate-300 flex dot-matrix-container">
        
        <!-- Lubang Kertas Continuous Form (Tractor Feed Kiri) -->
        <div class="w-8 bg-slate-50 border-r border-dashed border-slate-300 py-6 flex flex-col items-center justify-between gap-4 select-none shrink-0 tractor-feed">
            @for ($i = 0; $i < 16; $i++)
                <div class="w-2.5 h-2.5 rounded-full bg-slate-200 border border-slate-300"></div>
            @endfor
        </div>

        <!-- Isi Slip Faktur -->
        <div class="flex-1 p-6 sm:p-10 text-[12px] leading-relaxed text-black">
            
            <!-- Header Kop Sesuai Kategori (Sirkulasi vs Iklan) -->
            <div class="text-left mb-3">
                <h1 class="text-base sm:text-lg font-black tracking-wider uppercase font-dot-matrix">
                    {{ $isSirkulasi ? 'FAKTUR SIRKULASI KORAN HU. PRIANGAN' : 'ORDER TRANSAKSI IKLAN HU. PRIANGAN' }}
                </h1>
                <p class="text-[11px] font-bold text-neutral-700 tracking-tight leading-tight">
                    JL. DINDINGARI RAYA NO.12 TLP. (0265)-335300, FAX. 335677 PERUM BUMI RESIK PANGLAYUNGAN TASIKMALAYA 46134
                </p>
            </div>

            <!-- Garis Putus-putus Atas -->
            <div class="border-b-2 border-dashed border-black mb-3"></div>

            @php
                // Hitung variabel data dari relasi atau dari faktur
                $customer = $faktur->customer;
                $alamatCust = $txKoran->alamat_pemasangkoran ?? ($customer->alamat ?? '-');
                $kotaCust = $txKoran->kota ?? ($customer->kota ?? 'SUMEDANG');
                if (!$kotaCust || $kotaCust === '-') {
                    if (preg_match('/(sumedang|tasikmalaya|garut|ciamis|banjar|pangandaran)/i', $alamatCust, $matches)) {
                        $kotaCust = strtoupper($matches[1]);
                    } else {
                        $kotaCust = 'SUMEDANG';
                    }
                }

                $identitasCust = $txKoran->identitas ?? ($customer->identitas ?? ('1.00.04.01.01.01.' . str_pad($customer->id ?? 1, 5, '0', STR_PAD_LEFT)));
                $noOrder = $txKoran->no_order ?? ($faktur->no_order ?? ('2026' . str_pad($faktur->id, 6, '0', STR_PAD_LEFT)));
                $kopIklan = $txKoran->kop_iklan ?? '-';
                $merkIklan = $txKoran->merk ?? '-';
                $tglMuat = '-';
                $ukuranIklan = '-';
                $jenisIklan = 'DISPLAY UMUM';
                $hargaBrutto = (float) ($faktur->brutto > 0 ? $faktur->brutto : $faktur->total_tagihan);
                $diskonNominal = (float) ($faktur->diskon_nominal ?? 0);
                $diskonPersen = (float) ($faktur->diskon_persen ?? 0);
                $ppnNominal = (float) ($faktur->ppn_nominal ?? 0);
                $materaiNominal = (float) ($faktur->materai ?? 0);
                $materiIklan = $faktur->materi_iklan ?: '-';

                if ($txKoran) {
                    $tglMuat = $txKoran->tanggal_muatkoran ? \Carbon\Carbon::parse($txKoran->tanggal_muatkoran)->format('d-m-Y') : $faktur->tanggal_faktur->format('d-m-Y');
                    $ukuranIklan = $txKoran->ukuran_iklan ?: '-';
                    $jenisIklan = $txKoran->iklankoran->nama_jenis ?? ($txKoran->iklankoran->jenis_iklankoran ?? 'DISPLAY ADV DINAS 2009');
                    $hargaBrutto = (float) ($txKoran->brutto_transaksikoran ?: ($txKoran->harga_transaksikoran * ($txKoran->total_muatkoran ?: 1)));
                    $diskonNominal = (float) ($txKoran->diskon_transaksikoran ?? 0);
                    $diskonPersen = (float) ($txKoran->diskon_persen ?: ($hargaBrutto > 0 ? round(($diskonNominal / $hargaBrutto) * 100, 2) : 0));
                    $ppnNominal = (float) ($txKoran->ppn_transaksikoran ?? 0);
                    $materaiNominal = (float) ($txKoran->materai_transaksikoran ?? 0);
                    $materiIklan = $txKoran->materi_iklan ?: ('Iklan Koran Halaman ' . ($txKoran->halaman_iklan ?? 'Utama') . ' (' . ($txKoran->warna_iklan ?? 'FC') . ')');
                } elseif ($txOnline) {
                    $jenisIklan = $txOnline->iklanonline->nama_jenis ?? 'PORTAL ONLINE';
                    $tglMuat = $faktur->tanggal_faktur->format('d-m-Y');
                    $materiIklan = 'Iklan Web Banner / Advertorial (' . ($txOnline->portal_iklanonline ?: 'kabarpriangan.pikiran-rakyat.com') . ')';
                } elseif ($txPriangan) {
                    $jenisIklan = $txPriangan->iklanpriangan->nama_jenis ?? 'PRIANGAN TV';
                    $tglMuat = $faktur->tanggal_faktur->format('d-m-Y');
                    $materiIklan = 'Iklan Siaran Priangan TV Streaming';
                }

                $netto = max(0, $hargaBrutto - $diskonNominal);
                if ($ppnNominal == 0 && $faktur->total_tagihan > $netto) {
                    $ppnNominal = (float) ($faktur->total_tagihan - $netto);
                }
            @endphp

            <!-- Data Order & Customer (Dua Kolom) -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-6 gap-y-1 mb-3">
                <div class="sm:col-span-7 space-y-0.5">
                    <div class="flex">
                        <span class="w-24 shrink-0 font-bold">Identitas</span>
                        <span class="mr-2">:</span>
                        <span class="font-bold">{{ $identitasCust }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-24 shrink-0 font-bold">Nama</span>
                        <span class="mr-2">:</span>
                        <span class="font-bold uppercase">{{ $customer->nama_customer ?? 'UMUM' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-24 shrink-0 font-bold">Alamat</span>
                        <span class="mr-2">:</span>
                        <span class="uppercase">{{ $alamatCust }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-24 shrink-0 font-bold">Kota</span>
                        <span class="mr-2">:</span>
                        <span class="uppercase font-bold">{{ $kotaCust }}</span>
                    </div>
                </div>

                <div class="sm:col-span-5 space-y-0.5">
                    <div class="flex">
                        <span class="w-28 shrink-0 font-bold">{{ $isSirkulasi ? 'No. Distribusi' : 'No. Order' }}</span>
                        <span class="mr-2">:</span>
                        <span class="font-bold font-mono">{{ $noOrder }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-28 shrink-0 font-bold">No. Faktur</span>
                        <span class="mr-2">:</span>
                        <span class="font-bold font-mono text-blue-900">{{ $faktur->no_faktur }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-28 shrink-0 font-bold">{{ $isSirkulasi ? 'Jenis Transaksi' : 'Jenis Iklan' }}</span>
                        <span class="mr-2">:</span>
                        <span class="uppercase font-bold">{{ $isSirkulasi ? ('DISTRIBUSI SIRKULASI (' . strtoupper($faktur->tipe_faktur ?: 'REGULER') . ')') : $jenisIklan }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-28 shrink-0 font-bold">{{ $isSirkulasi ? 'Tanggal Terbit' : 'Tanggal Muat' }}</span>
                        <span class="mr-2">:</span>
                        <span class="font-bold">{{ $isSirkulasi ? $faktur->tanggal_faktur->format('d-m-Y') : $tglMuat }}</span>
                    </div>
                </div>
            </div>

            <!-- Garis Putus-putus Pemisah -->
            <div class="border-b-2 border-dashed border-black mb-3"></div>

            <!-- Detail Sesuai Kategori (Sirkulasi vs Iklan) -->
            @if($isSirkulasi)
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-1 mb-3">
                    <div class="sm:col-span-4 flex">
                        <span class="w-24 shrink-0 font-bold">Kategori</span>
                        <span class="mr-2">:</span>
                        <span class="font-bold uppercase">SIRKULASI KORAN</span>
                    </div>
                    <div class="sm:col-span-3 flex">
                        <span class="w-16 shrink-0 font-bold">Tipe</span>
                        <span class="mr-2">:</span>
                        <span class="font-bold uppercase">{{ $faktur->tipe_faktur ?: 'REGULER' }}</span>
                    </div>
                    <div class="sm:col-span-5 flex">
                        <span class="w-28 shrink-0 font-bold">Keterangan</span>
                        <span class="mr-2">:</span>
                        <span class="font-bold uppercase text-[11px] leading-tight">{{ $faktur->catatan ?: ($faktur->materi_iklan ?: 'Distribusi Eksemplar Sirkulasi HU Kabar Priangan') }}</span>
                    </div>
                </div>
            @else
                <!-- Detail Materi & Merk Iklan -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-1 mb-3">
                    <div class="sm:col-span-4 flex">
                        <span class="w-24 shrink-0 font-bold">Kop Iklan</span>
                        <span class="mr-2">:</span>
                        <span>{{ $kopIklan }}</span>
                    </div>
                    <div class="sm:col-span-3 flex">
                        <span class="w-16 shrink-0 font-bold">Merk</span>
                        <span class="mr-2">:</span>
                        <span>{{ $merkIklan }}</span>
                    </div>
                    <div class="sm:col-span-5 flex">
                        <span class="w-28 shrink-0 font-bold">Materi Iklan</span>
                        <span class="mr-2">:</span>
                        <span class="font-bold uppercase text-[11px] leading-tight">{{ $materiIklan }}</span>
                    </div>
                </div>
            @endif

            <!-- Garis Putus-putus Pemisah -->
            <div class="border-b-2 border-dashed border-black mb-3"></div>

            <!-- Rincian Biaya, Diskon, Netto, PPn -->
            <div class="space-y-1 max-w-lg mb-4">
                <div class="flex justify-between">
                    <span>{{ $isSirkulasi ? ('Oplah / Eksemplar : ' . ($faktur->eksemplar ? number_format($faktur->eksemplar, 0, ',', '.') . ' Eks' : '1 Paket')) : ('Ukuran : ' . $ukuranIklan) }}</span>
                    <span>Harga Rp. {{ number_format($hargaBrutto, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold">
                    <span>Brutto</span>
                    <span>Rp. {{ number_format($hargaBrutto, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Diskon {{ $diskonPersen > 0 ? number_format($diskonPersen, 2, ',', '.') . ' %' : '0,00 %' }}</span>
                    <span>Rp. {{ number_format($diskonNominal, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold">
                    <span>Netto</span>
                    <span>Rp. {{ number_format($netto, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>PPn 11 %</span>
                    <span>Rp. {{ number_format($ppnNominal, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Materai</span>
                    <span>Rp. {{ number_format($materaiNominal, 2, ',', '.') }}</span>
                </div>
                <div class="border-t border-dashed border-black pt-1 flex justify-between text-sm font-black">
                    <span>Jumlah Yang Dibayar</span>
                    <span>Rp. {{ number_format($faktur->total_tagihan, 2, ',', '.') }}</span>
                </div>
            </div>

            <!-- Garis Putus-putus Bawah -->
            <div class="border-b-2 border-dashed border-black mb-6"></div>

            <!-- Tanda Tangan & Stempel Sesuai Slip Fisik Asli -->
            <div class="grid grid-cols-2 gap-4 items-end pt-2">
                
                <!-- Stempel Piutang / Lunas (Lingkaran / Oval Biru) -->
                <div class="pl-4">
                    @if($faktur->status === 'lunas')
                        <div class="inline-block border-2 border-emerald-600 text-emerald-700 px-4 py-2 rounded-full font-black text-center rotate-[-4deg] opacity-90">
                            <div class="text-[9px] uppercase tracking-widest">KABAR PRIANGAN</div>
                            <div class="text-sm tracking-wider">L U N A S</div>
                            <div class="text-[8px] font-mono">{{ $faktur->tanggal_faktur->format('d/m/Y') }}</div>
                        </div>
                    @else
                        <!-- Stempel Lingkaran PIUTANG Sesuai Kategori -->
                        <div class="inline-block border-2 border-cyan-700 text-cyan-800 px-5 py-2.5 rounded-full font-black text-center rotate-[-6deg] opacity-90 shadow-2xs">
                            <div class="text-[9px] uppercase tracking-widest text-cyan-900">{{ $isSirkulasi ? 'PIUTANG SIRKULASI' : 'PIUTANG IKLAN' }}</div>
                            <div class="text-xs tracking-wider border-t border-b border-cyan-700 my-0.5 py-0.5">Kabar Priangan</div>
                            <div class="text-[8px] font-mono">DIVISI KEUANGAN</div>
                        </div>
                    @endif
                </div>

                <!-- Tanda Tangan Bagian Terkait & Pelanggan -->
                <div class="text-right space-y-6">
                    <div>
                        <p class="text-[11px] font-bold">Tasikmalaya, {{ $faktur->tanggal_faktur->format('d-m-Y') }}</p>
                        <p class="text-[11px] mt-0.5">{{ $isSirkulasi ? 'Bagian Sirkulasi,' : 'Front Office,' }}</p>
                        <div class="h-10"></div>
                        <p class="font-bold underline text-[11px]">{{ $isSirkulasi ? 'STAF SIRKULASI' : ('TIA / ' . ($faktur->sales_nama ?: 'ADMIN FO')) }}</p>
                    </div>

                    <div>
                        <p class="text-[11px]">{{ $isSirkulasi ? 'Agen / Pelanggan Sirkulasi,' : 'Pemasang,' }}</p>
                        <div class="h-10"></div>
                        <p class="font-bold underline text-[11px] uppercase">{{ $customer->nama_customer ?? '( .................... )' }}</p>
                    </div>
                </div>

            </div>

        </div>

        <!-- Lubang Kertas Continuous Form (Tractor Feed Kanan) -->
        <div class="w-8 bg-slate-50 border-l border-dashed border-slate-300 py-6 flex flex-col items-center justify-between gap-4 select-none shrink-0 tractor-feed">
            @for ($i = 0; $i < 16; $i++)
                <div class="w-2.5 h-2.5 rounded-full bg-slate-200 border border-slate-300"></div>
            @endfor
        </div>

    </div>

</body>
</html>
