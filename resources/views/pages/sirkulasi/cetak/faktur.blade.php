<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Penjualan Sirkulasi - {{ $fakturData->no_faktur }}</title>
    <link rel="icon" href="{{ asset('kabarpriangan.png') }}" type="image/png">
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; color: #0f172a !important; }
            .print-shadow { box-shadow: none !important; border: 1px solid #cbd5e1 !important; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans p-4 sm:p-8 min-h-screen">

    <!-- Top Action Bar (Non-Printable) -->
    <div class="max-w-4xl mx-auto mb-6 no-print space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center gap-2">
                <a href="{{ route('sirkulasi.cetak.penjualan', ['bulan' => $bulanPilihan]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    &larr; Laporan Penjualan
                </a>
                <span class="text-slate-300">|</span>
                <span class="text-xs font-bold text-emerald-800 uppercase tracking-wide">Faktur Sirkulasi Koran</span>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Dropdown Pilih Agen -->
                <form method="GET" action="{{ route('sirkulasi.cetak.faktur') }}" class="flex items-center gap-2">
                    <input type="hidden" name="mode" value="single">
                    <input type="month" name="bulan" value="{{ $bulanPilihan }}" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 py-1.5 focus:border-emerald-500 focus:ring-emerald-500">
                    <select name="agen" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 py-1.5 pr-8 focus:border-emerald-500 focus:ring-emerald-500 font-semibold text-slate-800">
                        @foreach($daftarAgen as $ag)
                            <option value="{{ $ag->kode_agen }}" {{ $selectedAgen->kode_agen === $ag->kode_agen ? 'selected' : '' }}>
                                {{ $ag->kode_agen }} - {{ $ag->nama_agen }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <!-- Tombol Print -->
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Faktur (Print / PDF)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Lembar Dokumen Faktur Resmi Sirkulasi -->
    @php
        $itemsToRender = $mode === 'all' ? $allFakturList : collect([$fakturData]);
    @endphp

    @foreach($itemsToRender as $item)
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-2xl border border-slate-200 shadow-md print-shadow mb-8 {{ !$loop->last ? 'page-break' : '' }}">
        
        <!-- Kop Surat Resmi -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-6 mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Logo Kabar Priangan" class="h-14 w-auto object-contain shrink-0" onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight uppercase leading-tight">HARIAN UMUM KABAR PRIANGAN</h1>
                    <p class="text-xs font-bold text-slate-700 mt-0.5">PT BERKAH KABAR PRIANGAN &bull; GRUP PIKIRAN RAKYAT</p>
                    <p class="text-[11px] text-slate-500 mt-0.5 leading-tight">
                        Jl. Dr. Sukardjo No. 51 Kota Tasikmalaya 46112 | Telp: (0265) 331234 | Email: sirkulasi@kabarpriangan.co.id
                    </p>
                </div>
            </div>

            <div class="text-right shrink-0">
                <span class="inline-block text-xs font-mono font-bold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-3 py-1 rounded-lg border border-emerald-200 mb-1">
                    FAKTUR PENJUALAN SIRKULASI
                </span>
                <span class="text-sm font-black font-mono text-slate-900 block whitespace-nowrap">{{ $item->no_faktur }}</span>
                <span class="text-[10px] text-slate-500 block uppercase font-mono mt-0.5">DIVISI SIRKULASI &amp; DISTRIBUSI</span>
            </div>
        </div>

        <!-- Detail Agen & Faktur (2 Kolom) -->
        <div class="grid grid-cols-2 gap-6 text-xs mb-6 pb-6 border-b border-slate-200">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1 font-mono">TAGIHAN KEPADA (AGEN KORAN):</span>
                <h2 class="text-sm font-black text-slate-900 leading-snug">{{ $item->nama_agen }}</h2>
                <div class="mt-1 space-y-0.5 text-slate-600">
                    <p><strong class="text-slate-800 font-mono">{{ $item->kode_agen }}</strong> &bull; {{ $item->kategori_label }}</p>
                    <p class="text-slate-700">Wilayah: <strong>{{ $item->wilayah }}</strong> ({{ $item->jurusan }})</p>
                    <p class="text-slate-600 leading-relaxed">{{ $item->alamat }}</p>
                    <p class="text-slate-600 font-mono">Telp: {{ $item->telepon }}</p>
                </div>
            </div>

            <div class="text-right space-y-1 text-slate-700">
                <div>
                    <span class="text-slate-500">Tanggal Faktur:</span>
                    <strong class="text-slate-900 font-mono">{{ $item->tanggal_faktur->translatedFormat('d F Y') }}</strong>
                </div>
                <div>
                    <span class="text-slate-500">Jatuh Tempo:</span>
                    <strong class="text-rose-600 font-mono">{{ $item->jatuh_tempo->translatedFormat('d F Y') }}</strong>
                </div>
                <div>
                    <span class="text-slate-500">Periode Distribusi:</span>
                    <strong class="text-slate-800 font-mono">{{ \Carbon\Carbon::parse($bulanPilihan . '-01')->translatedFormat('F Y') }}</strong>
                </div>
                <div>
                    <span class="text-slate-500">Status Pembayaran:</span>
                    <span class="px-2 py-0.5 rounded font-bold uppercase font-mono text-[10px] {{ $item->status_bayar === 'Lancar' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $item->status_bayar }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Tabel Rincian Distribusi Koran -->
        <table class="w-full text-xs text-left mb-6">
            <thead class="bg-slate-100 uppercase text-[10px] font-bold text-slate-700 border-y border-slate-300">
                <tr>
                    <th class="py-2.5 px-3 w-8 text-center">No</th>
                    <th class="py-2.5 px-3">Rincian Komoditas Koran</th>
                    <th class="py-2.5 px-3 text-right">Kirim (Eks)</th>
                    <th class="py-2.5 px-3 text-right text-rose-600">Retur BAPR</th>
                    <th class="py-2.5 px-3 text-right font-extrabold text-slate-900">Nett Sales</th>
                    <th class="py-2.5 px-3 text-right">Tarif Agen</th>
                    <th class="py-2.5 px-3 text-right font-extrabold text-emerald-900">Total Tagihan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <tr>
                    <td class="py-3 px-3 text-center font-mono">1</td>
                    <td class="py-3 px-3">
                        <div class="font-bold text-slate-900 text-xs">Koran Harian Umum Kabar Priangan</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            Distribusi Harian Periode {{ \Carbon\Carbon::parse($bulanPilihan . '-01')->translatedFormat('F Y') }} &bull; Standar Pengepakan Bendel Rafia Ganda
                        </div>
                    </td>
                    <td class="py-3 px-3 text-right font-semibold text-slate-800 tabular-nums">
                        {{ number_format($item->kirim, 0, ',', '.') }} eks
                    </td>
                    <td class="py-3 px-3 text-right text-rose-600 font-semibold tabular-nums">
                        {{ number_format($item->retur, 0, ',', '.') }} eks
                    </td>
                    <td class="py-3 px-3 text-right font-extrabold text-slate-900 tabular-nums">
                        {{ number_format($item->nett_sales, 0, ',', '.') }} eks
                    </td>
                    <td class="py-3 px-3 text-right font-mono text-slate-700 tabular-nums">
                        Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                    </td>
                    <td class="py-3 px-3 text-right font-mono font-bold text-emerald-800 tabular-nums">
                        Rp {{ number_format($item->nilai_tagihan, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
            <tfoot class="border-t-2 border-slate-300 bg-slate-50 font-bold text-xs text-slate-800">
                <tr>
                    <td colspan="6" class="py-2 px-3 text-right uppercase text-[11px]">Nilai Tagihan Koran Bulan Ini:</td>
                    <td class="py-2 px-3 text-right font-mono text-slate-900">
                        Rp {{ number_format($item->nilai_tagihan, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="6" class="py-2 px-3 text-right uppercase text-[11px] text-slate-500">Saldo Piutang Berjalan:</td>
                    <td class="py-2 px-3 text-right font-mono {{ $item->saldo_piutang > 0 ? 'text-rose-600' : 'text-slate-600' }}">
                        Rp {{ number_format($item->saldo_piutang, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="bg-emerald-50/80 border-t border-emerald-200">
                    <td colspan="6" class="py-2.5 px-3 text-right uppercase text-xs font-black text-emerald-900">
                        TOTAL HARGA BAYAR / SETORAN KE KASIR:
                    </td>
                    <td class="py-2.5 px-3 text-right font-mono text-base font-black text-emerald-800">
                        Rp {{ number_format($item->harga_bayar, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- BOX KODE QR KASIR (Fitur Utama Integrasi Scan Kasir) -->
        <div class="mb-8 p-4 bg-slate-50 rounded-2xl border-2 border-dashed border-emerald-300 flex flex-col sm:flex-row items-center gap-5">
            <!-- Render QR Code Asli -->
            <div class="p-2 bg-white rounded-xl border border-slate-300 shadow-2xs shrink-0 flex flex-col items-center justify-center">
                <div class="w-[96px] h-[96px] flex items-center justify-center">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(96)->margin(0)->generate($item->qr_payload) !!}
                </div>
                <span class="block text-[8px] font-mono font-bold text-slate-600 mt-1 uppercase tracking-wider">
                    SCAN KASIR
                </span>
            </div>

            <!-- Rincian Data QR Code untuk Kasir -->
            <div class="flex-1 space-y-1.5 text-xs">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 font-mono flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <span>KODE QR VALIDASI SCAN KASIR</span>
                    </span>
                    <span class="px-2 py-0.5 rounded text-[9px] font-mono font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        SI-KABARPRIANGAN &bull; SIRKULASI
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 py-1 text-slate-700 bg-white/70 p-2.5 rounded-lg border border-slate-200">
                    <div>
                        <span class="text-[10px] text-slate-400 block font-mono">NOMOR FAKTUR:</span>
                        <strong class="font-mono text-slate-900 text-xs">{{ $item->no_faktur }}</strong>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block font-mono">NAMA AGEN:</span>
                        <strong class="text-slate-900 text-xs">{{ $item->nama_agen }}</strong>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block font-mono">PENJUALAN MASUK (HARGA BAYAR):</span>
                        <strong class="font-mono text-emerald-800 text-xs">Rp {{ number_format($item->harga_bayar, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <p class="text-[11px] text-slate-500 leading-relaxed">
                    Petunjuk Kasir: Pindai (scan) kode QR ini pada bagian kasir untuk langsung menarik rincian faktur sirkulasi, memvalidasi identitas agen, dan memproses tanda terima setoran koran secara instan.
                </p>
            </div>
        </div>

        <!-- Tanda Tangan 3 Pihak -->
        <div class="grid grid-cols-3 gap-6 text-center text-xs text-slate-700 pt-4 border-t border-slate-200">
            <div>
                <p class="text-slate-500">Diterbitkan Oleh,</p>
                <p class="font-bold text-slate-900">Bagian Sirkulasi &amp; Distribusi</p>
                <div class="h-16"></div>
                <p class="font-bold border-t border-slate-400 pt-1 inline-block min-w-[140px] text-slate-800">
                    Koordinator Ekspedisi
                </p>
            </div>
            <div>
                <p class="text-slate-500">Diterima / Disetor Oleh,</p>
                <p class="font-bold text-slate-900">Agen Koran</p>
                <div class="h-16"></div>
                <p class="font-bold border-t border-slate-400 pt-1 inline-block min-w-[140px] text-slate-800">
                    {{ $item->nama_agen }}
                </p>
            </div>
            <div>
                <p class="text-slate-500">Diverifikasi &amp; Diterima,</p>
                <p class="font-bold text-slate-900">Bagian Kasir Keuangan</p>
                <div class="h-16"></div>
                <p class="font-bold border-t border-slate-400 pt-1 inline-block min-w-[140px] text-slate-800">
                    Petugas Kasir
                </p>
            </div>
        </div>

        <!-- Footer Catatan -->
        <div class="mt-8 pt-4 border-t border-slate-200 text-[10px] text-slate-400 flex items-center justify-between font-mono">
            <span>SI-KABARPRIANGAN &bull; Dokumen Faktur Resmi Divisi Sirkulasi</span>
            <span>Harian Umum Kabar Priangan &bull; Grup Pikiran Rakyat</span>
        </div>
    </div>
    @endforeach

</body>
</html>
