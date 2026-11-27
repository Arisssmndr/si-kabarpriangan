<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $tabsPemasaran = [
            'agen-besar'         => 'LAPORAN AGEN BESAR',
            'sub-agen'           => 'LAPORAN SUB AGEN & KIOS',
            'rekap-terbit-bulan' => 'REKAP PENJUALAN HARIAN',
            'rekap-wilayah'      => 'LAPORAN PENJUALAN PER WILAYAH',
            'rekap-jurusan'      => 'LAPORAN PER RUTE JURUSAN',
            'penerimaan-koran'   => 'LAPORAN PENERIMAAN KORAN',
            'oplah-tahun'        => 'LAPORAN PENJUALAN KORAN TAHUNAN',
            'rekap-bln-wilayah'  => 'LAPORAN BULANAN PER WILAYAH',
        ];
        $judulCetak = $tabsPemasaran[$tab] ?? 'LAPORAN PEMASARAN SIRKULASI';
        $wilayahLabel = $wilayahFilter === 'all'
            ? 'Semua Wilayah'
            : ($wilayahList->firstWhere('kode', $wilayahFilter)->nama ?? $wilayahFilter);
        $jurusanLabel = $jurusanFilter === 'all'
            ? 'Semua Jurusan'
            : ($jurusanList->firstWhere('kode', $jurusanFilter)->nama_jurusan ?? ('Rute ' . $jurusanFilter));
    @endphp
    <title>{{ $judulCetak }} - {{ $periodeCetakHeader }}</title>
    <link rel="icon" href="{{ asset('kabarpriangan.png') }}" type="image/png">
    @vite(['resources/css/app.css'])
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 10mm 10mm 10mm;
        }
        @media print {
            .no-print { display: none !important; }
            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                color: #0f172a !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .sheet-box {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans antialiased">

    <!-- Toolbar Non-Cetak -->
    <div class="no-print sticky top-0 z-40 bg-white border-b border-slate-200 px-4 sm:px-6 py-3 flex flex-wrap items-center justify-between gap-3 shadow-xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('sirkulasi.cetak.pemasaran', request()->query()) }}"
               class="inline-flex items-center gap-1.5 h-9 px-3.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition">
                &larr; Kembali ke Filter Laporan
            </a>
            <span class="text-xs text-slate-500 hidden md:inline">
                Dicetak sesuai filter: <strong class="text-slate-800">{{ $periodeLabel }}</strong>
            </span>
        </div>
        <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 h-9 px-4 rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
            <span>Cetak Dokumen (A4 Landscape)</span>
        </button>
    </div>

    <!-- Lembar Dokumen Cetak -->
    <div class="max-w-7xl mx-auto p-4 sm:p-6">
        <div class="sheet-box bg-white border border-slate-200 rounded-xl shadow-sm p-6 sm:p-8 space-y-5">

            <!-- Kop Resmi Perusahaan -->
            <div class="border-b-2 border-slate-900 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-11 w-auto object-contain">
                    <div>
                        <h1 class="text-base font-extrabold uppercase tracking-wide text-slate-900">HARIAN UMUM KABAR PRIANGAN</h1>
                        <p class="text-xs font-bold uppercase text-slate-700">Divisi Sirkulasi &amp; Distribusi Koran</p>
                        <p class="text-[11px] text-slate-500">Jl. Dindingari Raya No. 12 Panglayungan, Tasikmalaya &bull; Telp. (0265) 335300</p>
                    </div>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-xs font-extrabold uppercase text-kp-blue-700">{{ $judulCetak }}</p>
                    <p class="text-[11px] font-semibold text-slate-700 mt-0.5">Periode: {{ $periodeCetakHeader }}</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">
                        Wilayah: {{ $wilayahLabel }} &bull; Jurusan: {{ $jurusanLabel }}
                        @if($searchQ !== '') &bull; Cari: "{{ $searchQ }}" @endif
                        @if($tab === 'penerimaan-koran' && $statusBayarFilter !== 'all') &bull; Status: {{ strtoupper($statusBayarFilter) }} @endif
                    </p>
                </div>
            </div>

            <!-- Konten Laporan Sesuai Filter Aktif -->
            @include('pages.sirkulasi.cetak.partials.pemasaran_content', ['isPrintMode' => true])

            <!-- Blok Pengesahan -->
            <div class="grid grid-cols-2 text-center text-xs text-slate-800 pt-6 mt-6 border-t border-slate-300 break-inside-avoid">
                <div>
                    <p class="text-slate-500">Mengetahui,</p>
                    <p class="font-bold text-slate-900 mt-1">Pimpinan Perusahaan / General Manager</p>
                    <div class="h-16"></div>
                    <p class="font-bold underline text-slate-900">( .................................................... )</p>
                </div>
                <div>
                    <p class="text-slate-500">Tasikmalaya, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p class="font-bold text-slate-900 mt-1">Manajer Divisi Sirkulasi</p>
                    <div class="h-16"></div>
                    <p class="font-bold underline text-slate-900">( .................................................... )</p>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
