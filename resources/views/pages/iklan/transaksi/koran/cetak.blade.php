<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Transaksi Iklan HU. Priangan - {{ $transaksi->nofakturkoran }}</title>
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
        <a href="{{ route('transaksikoran.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs font-sans">
            ← Kembali ke Daftar Transaksi
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition cursor-pointer font-sans">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Lembar Order (Print / PDF)</span>
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

        @php
            $brutto = (float) ($transaksi->brutto_transaksikoran ?: ($transaksi->harga_transaksikoran * ($transaksi->total_muatkoran ?: 1)));
            $diskonNominal = (float) ($transaksi->diskon_transaksikoran ?? 0);
            $diskonPersen = (float) ($transaksi->diskon_persen ?: ($brutto > 0 ? round(($diskonNominal / $brutto) * 100, 2) : 0));
            $netto = (float) ($transaksi->netto_transaksikoran ?: max(0, $brutto - $diskonNominal));
            $ppn = (float) ($transaksi->ppn_transaksikoran ?? 0);
            $materai = (float) ($transaksi->materai_transaksikoran ?? 0);
            $totalTagihan = (float) ($transaksi->totaltagihan_transaksikoran ?: ($netto + $ppn + $materai));
            $sisaPiutang = (float) ($transaksi->piutang_transaksikoran ?? max(0, $totalTagihan - ($transaksi->jumlahbayar_transaksikoran ?? 0)));

            $kota = $transaksi->kota ?: 'SUMEDANG';
            $identitas = $transaksi->identitas ?: '1.00.04.01.01.01.' . str_pad($transaksi->id, 5, '0', STR_PAD_LEFT);
            $noOrder = $transaksi->no_order ?: ('2026' . str_pad($transaksi->id, 6, '0', STR_PAD_LEFT));
            $tglTransaksi = $transaksi->tanggal_transaksikoran ? \Carbon\Carbon::parse($transaksi->tanggal_transaksikoran)->format('d-m-Y') : date('d-m-Y');
            $tglMuat = $transaksi->tanggal_muatkoran ? \Carbon\Carbon::parse($transaksi->tanggal_muatkoran)->format('d-m-Y') : '-';
            $jenisIklan = $transaksi->iklankoran->nama_jenis ?? ($transaksi->iklankoran->jenis_iklankoran ?? 'DISPLAY ADV DINAS 2009');
            $materi = $transaksi->materi_iklan ?: ($transaksi->halaman_iklan ? "IKLAN KORAN {$transaksi->halaman_iklan} ({$transaksi->warna_iklan})" : '-');
        @endphp

        <!-- Isi Slip Faktur -->
        <div class="flex-1 p-6 sm:p-10 text-[12px] leading-relaxed text-black">
            
            <!-- Header Kop Sesuai Slip Fisik Asli -->
            <div class="text-left mb-3">
                <h1 class="text-base sm:text-lg font-black tracking-wider uppercase font-dot-matrix">
                    ORDER TRANSAKSI IKLAN HU. PRIANGAN
                </h1>
                <p class="text-[11px] font-bold text-neutral-700 tracking-tight leading-tight">
                    JL.DINDINGARI RAYA NO.12 TLP. (0265)-335300, FAX 335677 PERUM BUMI RESIK PANGLAYUNGAN TASIKMALAYA 46134
                </p>
            </div>

            <!-- Garis Putus-putus Atas -->
            <div class="border-b-2 border-dashed border-black mb-3"></div>

            <!-- Data Order & Customer (Dua Kolom) -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-6 gap-y-1 mb-3">
                <div class="sm:col-span-7 space-y-0.5">
                    <div class="flex">
                        <span class="w-24 shrink-0 font-bold">Identitas</span>
                        <span class="mr-2">:</span>
                        <span class="font-bold">{{ $identitas }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-24 shrink-0 font-bold">Nama</span>
                        <span class="mr-2">:</span>
                        <span class="font-black uppercase">{{ $transaksi->nama_pemasangkoran }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-24 shrink-0 font-bold">Alamat</span>
                        <span class="mr-2">:</span>
                        <span class="uppercase">{{ $transaksi->alamat_pemasangkoran }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-24 shrink-0 font-bold">Kota</span>
                        <span class="mr-2">:</span>
                        <span class="uppercase font-bold">{{ $kota }}</span>
                    </div>
                </div>

                <div class="sm:col-span-5 space-y-0.5">
                    <div class="flex">
                        <span class="w-28 shrink-0 font-bold">No. Order</span>
                        <span class="mr-2">:</span>
                        <span class="font-bold font-mono">{{ $noOrder }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-28 shrink-0 font-bold">No. Faktur</span>
                        <span class="mr-2">:</span>
                        <span class="font-bold font-mono text-blue-900">{{ $transaksi->nofakturkoran }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-28 shrink-0 font-bold">Jenis Iklan</span>
                        <span class="mr-2">:</span>
                        <span class="uppercase font-bold">{{ $jenisIklan }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-28 shrink-0 font-bold">Tanggal Muat</span>
                        <span class="mr-2">:</span>
                        <span class="font-bold">{{ $tglMuat }}</span>
                    </div>
                </div>
            </div>

            <!-- Garis Putus-putus Pemisah -->
            <div class="border-b-2 border-dashed border-black mb-3"></div>

            <!-- Detail Materi & Merk Iklan -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-4 gap-y-1 mb-3">
                <div class="sm:col-span-4 flex">
                    <span class="w-24 shrink-0 font-bold">Kop Iklan</span>
                    <span class="mr-2">:</span>
                    <span>{{ $transaksi->kop_iklan ?: '-' }}</span>
                </div>
                <div class="sm:col-span-3 flex">
                    <span class="w-16 shrink-0 font-bold">Merk</span>
                    <span class="mr-2">:</span>
                    <span>{{ $transaksi->merk ?: '-' }}</span>
                </div>
                <div class="sm:col-span-5 flex">
                    <span class="w-28 shrink-0 font-bold">Materi Iklan</span>
                    <span class="mr-2">:</span>
                    <span class="font-bold uppercase text-[11px] leading-tight">{{ $materi }}</span>
                </div>
            </div>

            <!-- Garis Putus-putus Pemisah -->
            <div class="border-b-2 border-dashed border-black mb-3"></div>

            <!-- Rincian Biaya, Diskon, Netto, PPn -->
            <div class="space-y-1 max-w-lg mb-4">
                <div class="flex justify-between">
                    <span>Ukuran : {{ $transaksi->ukuran_iklan }}</span>
                    <span>Harga Rp. {{ number_format($transaksi->harga_transaksikoran, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold">
                    <span>Brutto</span>
                    <span>Rp. {{ number_format($brutto, 2, ',', '.') }}</span>
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
                    <span>Rp. {{ number_format($ppn, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Materai</span>
                    <span>Rp. {{ number_format($materai, 2, ',', '.') }}</span>
                </div>
                <div class="border-t border-dashed border-black pt-1 flex justify-between text-sm font-black">
                    <span>Jumlah Yang Dibayar</span>
                    <span>Rp. {{ number_format($totalTagihan, 2, ',', '.') }}</span>
                </div>
            </div>

            <!-- Garis Putus-putus Bawah -->
            <div class="border-b-2 border-dashed border-black mb-6"></div>

            <!-- Tanda Tangan & Stempel Sesuai Slip Fisik Asli -->
            <div class="grid grid-cols-2 gap-4 items-end pt-2">
                
                <!-- Stempel Piutang Iklan / Lunas (Lingkaran / Oval Biru) -->
                <div class="pl-4">
                    @if($sisaPiutang <= 0)
                        <div class="inline-block border-2 border-emerald-600 text-emerald-700 px-4 py-2 rounded-full font-black text-center rotate-[-4deg] opacity-90">
                            <div class="text-[9px] uppercase tracking-widest">KABAR PRIANGAN</div>
                            <div class="text-sm tracking-wider">L U N A S</div>
                            <div class="text-[8px] font-mono">{{ $tglTransaksi }}</div>
                        </div>
                    @else
                        <!-- Stempel Lingkaran PIUTANG IKLAN persis di Foto Dokumen -->
                        <div class="inline-block border-2 border-cyan-800 text-cyan-900 px-5 py-2.5 rounded-full font-black text-center rotate-[-6deg] opacity-90 shadow-2xs">
                            <div class="text-[9px] uppercase tracking-widest text-cyan-950 font-black">PIUTANG IKLAN</div>
                            <div class="text-xs tracking-wider border-t border-b border-cyan-800 my-0.5 py-0.5">Kabar Priangan</div>
                            <div class="text-[8px] font-mono">DIVISI KEUANGAN</div>
                        </div>
                    @endif
                </div>

                <!-- Tanda Tangan Front Office & Pemasang -->
                <div class="text-right space-y-6">
                    <div>
                        <p class="text-[11px] font-bold">Tasikmalaya, {{ $tglTransaksi }}</p>
                        <p class="text-[11px] mt-0.5">Front Office,</p>
                        <div class="h-10"></div>
                        <p class="font-bold underline text-[11px] uppercase">{{ $transaksi->sales_iklankoran ?: 'TIA' }}</p>
                    </div>

                    <div>
                        <p class="text-[11px]">Pemasang,</p>
                        <div class="h-10"></div>
                        <p class="font-bold underline text-[11px] uppercase">{{ $transaksi->nama_pemasangkoran ?: '( .................... )' }}</p>
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
