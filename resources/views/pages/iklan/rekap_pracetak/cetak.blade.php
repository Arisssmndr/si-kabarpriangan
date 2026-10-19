<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Dummy Pracetak - Edisi {{ $tanggalFormatted }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; font-size: 10pt; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body class="bg-white text-black p-6 font-sans">

    <!-- Action Bar Cetak -->
    <div class="no-print mb-6 flex justify-between items-center bg-slate-100 p-4 rounded-xl border border-slate-300">
        <div>
            <span class="font-bold text-sm text-black">Lembar Rekap Dummy Pracetak Resmi (HU Kabar Priangan)</span>
            <span class="block text-xs font-semibold text-black">Edisi Terbit: {{ $tanggalFormatted }} • Total Pemuatan: {{ $totalIklan }} Iklan</span>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-black text-white font-bold text-xs rounded-lg hover:bg-slate-800 transition">
                🖨️ Cetak Lembar Dummy
            </button>
            <button onclick="window.close()" class="px-3 py-2 bg-slate-200 text-black font-bold text-xs rounded-lg hover:bg-slate-300 transition">
                Tutup
            </button>
        </div>
    </div>

    <!-- Kop Dokumen -->
    <div class="border-b-2 border-black pb-2 mb-4 flex justify-between items-end">
        <div>
            <h1 class="text-xl font-bold uppercase tracking-wider text-black">HARIAN UMUM KABAR PRIANGAN</h1>
            <p class="text-xs font-bold text-black">DIVISI IKLAN • REKAP DUMMY TATA LETAK PRACETAK PER TANGGAL MUAT</p>
        </div>
        <div class="text-right text-xs">
            <p class="font-bold text-black">EDISI TERBIT: <span class="font-mono text-sm underline">{{ $tanggalFormatted }}</span></p>
            <p class="font-semibold text-black text-[11px]">Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
        </div>
    </div>

    <!-- KPI Ringkasan Edisi Ini -->
    <div class="grid grid-cols-4 gap-2 border border-black p-2 mb-4 text-xs text-center font-bold">
        <div class="border-r border-black">TOTAL IKLAN: {{ $totalIklan }}</div>
        <div class="border-r border-black">VOLUME MMK: {{ number_format($totalMmk, 0, ',', '.') }} mmk</div>
        <div class="border-r border-black">TOTAL BARIS: {{ $totalBaris }} Baris</div>
        <div>TOTAL OMSET: Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
    </div>

    <!-- Pengelompokan Data per Halaman -->
    @foreach($groupedByHalaman as $halId => $grup)
        @if(count($grup['items']) > 0)
            <div class="mb-6">
                <!-- Header Halaman -->
                <div class="bg-slate-200 border border-black px-3 py-1.5 flex justify-between items-center text-xs font-bold text-black">
                    <span>
                        HALAMAN {{ $grup['nomor_halaman'] < 99 ? $grup['nomor_halaman'] : 'DALAM' }} — {{ strtoupper($grup['nama']) }} (Wilayah: {{ $grup['wilayah'] }})
                    </span>
                    <span>
                        {{ count($grup['items']) }} Iklan | {{ $grup['total_mmk'] > 0 ? number_format($grup['total_mmk'], 0, ',', '.') . ' mmk' : $grup['total_baris'] . ' baris' }}
                    </span>
                </div>

                <!-- Tabel Iklan -->
                <table class="w-full text-left border-collapse border border-black text-xs text-black mt-1">
                    <thead>
                        <tr class="bg-slate-100 text-black">
                            <th class="border border-black p-1.5 font-bold text-center w-8">No</th>
                            <th class="border border-black p-1.5 font-bold w-24">No Faktur</th>
                            <th class="border border-black p-1.5 font-bold w-48">Nama Pengiklan / Instansi</th>
                            <th class="border border-black p-1.5 font-bold w-32">Kategori &amp; Ukuran</th>
                            <th class="border border-black p-1.5 font-bold w-16">Warna</th>
                            <th class="border border-black p-1.5 font-bold">Materi / Teks Iklan Baris</th>
                            <th class="border border-black p-1.5 font-bold w-24">Sales AE</th>
                            <th class="border border-black p-1.5 font-bold text-center w-14">Check</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($grup['items'] as $i => $item)
                            <tr class="{{ $item['is_cbp_blocked'] ? 'bg-slate-50' : '' }}">
                                <td class="border border-black p-1.5 text-center font-bold font-mono">{{ $i + 1 }}</td>
                                <td class="border border-black p-1.5 font-bold font-mono">
                                    {{ $item['nofaktur'] }}
                                    @if($item['is_cbp_blocked'])
                                        <span class="block text-[9px] text-red-600 font-bold uppercase">[HOLD: CBP]</span>
                                    @endif
                                </td>
                                <td class="border border-black p-1.5 font-bold">
                                    {{ $item['pengiklan'] }}
                                </td>
                                <td class="border border-black p-1.5 font-bold">
                                    {{ $item['kategori_form'] }}<br>
                                    <span class="font-mono text-[11px]">{{ $item['ukuran'] }}</span>
                                </td>
                                <td class="border border-black p-1.5 font-bold text-center">
                                    {{ $item['warna'] }}
                                </td>
                                <td class="border border-black p-1.5 text-[11px]">
                                    @if($item['kategori_form'] === 'BARIS')
                                        <div class="font-mono p-1 bg-slate-50 border border-slate-300 leading-tight">
                                            {{ $item['naskah_baris'] }}
                                        </div>
                                    @else
                                        <span class="font-semibold block">{{ $item['keterangan'] ?: 'Materi visual display' }}</span>
                                        <span class="font-mono text-[10px] block">File: {{ $item['file_materi'] ? basename($item['file_materi']) : 'Setter Redaksi' }} ({{ $item['status_materi'] }})</span>
                                    @endif
                                </td>
                                <td class="border border-black p-1.5 font-bold text-[11px]">
                                    {{ $item['sales'] }}
                                </td>
                                <td class="border border-black p-1.5 text-center font-bold">
                                    [ &nbsp; ]
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach

    <!-- Kolom Verifikasi & Tanda Tangan -->
    <div class="grid grid-cols-3 gap-6 text-center text-xs text-black pt-6 border-t-2 border-black mt-8">
        <div>
            <p class="font-bold mb-16">Disusun oleh Admin Iklan</p>
            <p class="font-bold underline">( ............................................ )</p>
            <p class="text-[10px] font-bold text-black mt-1">Divisi Pemasaran &amp; Iklan</p>
        </div>
        <div>
            <p class="font-bold mb-16">Diterima oleh Setter Redaksi</p>
            <p class="font-bold underline">( ............................................ )</p>
            <p class="text-[10px] font-bold text-black mt-1">Bagian Tata Letak Halaman</p>
        </div>
        <div>
            <p class="font-bold mb-16">Mengetahui Manajer Produksi Cetak</p>
            <p class="font-bold underline">( ............................................ )</p>
            <p class="text-[10px] font-bold text-black mt-1">Percetakan HU Kabar Priangan</p>
        </div>
    </div>

</body>
</html>
