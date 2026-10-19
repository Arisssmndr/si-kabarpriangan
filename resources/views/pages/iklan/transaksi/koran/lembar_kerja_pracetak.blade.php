<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Kerja Pracetak - {{ $transaksi->nofakturkoran }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; font-size: 11pt; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-white text-black p-8 max-w-4xl mx-auto font-sans">

    <!-- Tombol Cetak -->
    <div class="no-print mb-6 flex justify-between items-center bg-slate-100 p-4 rounded-xl border border-slate-300">
        <div>
            <span class="font-bold text-sm text-black">Lembar Instruksi Kerja Pracetak (Job Ticket)</span>
            <span class="block text-xs font-semibold text-black">Gunakan sebagai panduan tata letak halaman koran oleh setter redaksi.</span>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-black text-white font-bold text-xs rounded-lg hover:bg-slate-800 transition">
                🖨️ Cetak Lembar Kerja
            </button>
            <button onclick="window.close()" class="px-3 py-2 bg-slate-200 text-black font-bold text-xs rounded-lg hover:bg-slate-300 transition">
                Tutup
            </button>
        </div>
    </div>

    <!-- Kop Dokumen Resmi -->
    <div class="border-b-2 border-black pb-3 mb-4 flex justify-between items-start">
        <div>
            <h1 class="text-xl font-bold uppercase tracking-wider text-black">HARIAN UMUM KABAR PRIANGAN</h1>
            <p class="text-xs font-bold text-black">DIVISI IKLAN &amp; PRODUKSI CETAK • LEMBAR KERJA TATA LETAK PRACETAK</p>
        </div>
        <div class="text-right">
            <div class="border border-black px-3 py-1 text-center">
                <span class="text-[10px] font-bold block uppercase text-black">JOB TICKET #</span>
                <span class="font-mono text-sm font-bold text-black">{{ $transaksi->nofakturkoran }}</span>
            </div>
        </div>
    </div>

    <!-- Parameter Penempatan & Spesifikasi Cetak -->
    <div class="grid grid-cols-2 gap-4 border border-black p-3 mb-4 text-xs">
        <div>
            <table class="w-full text-black">
                <tr>
                    <td class="font-bold py-1 w-36">Pengiklan / Klien</td>
                    <td class="font-bold py-1">: {{ $transaksi->nama_pemasangkoran }}</td>
                </tr>
                <tr>
                    <td class="font-bold py-1">Kategori Iklan</td>
                    <td class="font-bold py-1">: {{ $transaksi->kategori_form }}</td>
                </tr>
                <tr>
                    <td class="font-bold py-1">Warna Pemuatan</td>
                    <td class="font-bold py-1">: {{ $transaksi->warna_iklan }}</td>
                </tr>
                <tr>
                    <td class="font-bold py-1">Sales / AE</td>
                    <td class="font-bold py-1">: {{ $transaksi->sales_iklankoran }}</td>
                </tr>
            </table>
        </div>
        <div>
            <table class="w-full text-black">
                <tr>
                    <td class="font-bold py-1 w-36">Penempatan Halaman</td>
                    <td class="font-bold py-1">: {{ $transaksi->kopKota->nama_penempatan ?? $transaksi->halaman_iklan }}</td>
                </tr>
                <tr>
                    <td class="font-bold py-1">Spesifikasi Ukuran</td>
                    <td class="font-bold py-1">: 
                        @if($transaksi->kategori_form === 'BARIS')
                            {{ $transaksi->jumlah_baris }} Baris (Rubrik: {{ $transaksi->kopIklan->nama_kop ?? 'Aneka' }})
                        @else
                            {{ $transaksi->lebar_kolom }} Kolom × {{ $transaksi->tinggi_mm }} mm ({{ $transaksi->lebar_kolom * $transaksi->tinggi_mm }} mmk)
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="font-bold py-1">Status Kesiapan</td>
                    <td class="font-bold py-1">: {{ $transaksi->status_materi }}</td>
                </tr>
                <tr>
                    <td class="font-bold py-1">Tgl Order Masuk</td>
                    <td class="font-bold py-1">: {{ $transaksi->tanggal_transaksikoran->format('d/m/Y') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Naskah Teks Kalimat jika Iklan Baris -->
    @if($transaksi->kategori_form === 'BARIS' || !empty($transaksi->naskah_iklan_baris))
        <div class="border border-black p-4 mb-4">
            <span class="block font-bold text-xs uppercase mb-2 text-black bg-slate-100 p-1">Naskah Iklan Baris (Teks Cetak Huruf):</span>
            <div class="font-mono text-sm leading-relaxed whitespace-pre-wrap p-2 border border-dashed border-black">
                {{ $transaksi->naskah_iklan_baris }}
            </div>
        </div>
    @endif

    <!-- Lampiran Desain & Instruksi Teknis Layout -->
    <div class="border border-black p-4 mb-4 text-xs">
        <span class="block font-bold uppercase mb-2 text-black bg-slate-100 p-1">Instruksi Teknis &amp; Berkas Materi Desain:</span>
        <div class="space-y-2">
            <p class="font-semibold text-black">Catatan Operasional: <span class="font-bold">{{ $transaksi->keterangan_materi ?: 'Sesuai standar grid redaksi.' }}</span></p>
            <p class="font-semibold text-black">File Materi: <span class="font-bold font-mono">{{ $transaksi->file_materi ? basename($transaksi->file_materi) : 'Tidak ada berkas (Setter input teks manual)' }}</span></p>
        </div>
    </div>

    <!-- Jadwal Muat Koran -->
    <div class="border border-black p-3 mb-6 text-xs">
        <span class="block font-bold uppercase mb-2 text-black bg-slate-100 p-1">Jadwal Tanggal Pemuatan Koran (Wajib Terbit):</span>
        <table class="w-full text-left border-collapse border border-black text-black">
            <thead>
                <tr class="bg-slate-200">
                    <th class="border border-black p-1.5 font-bold">Edisi</th>
                    <th class="border border-black p-1.5 font-bold">Hari &amp; Tanggal Terbit</th>
                    <th class="border border-black p-1.5 font-bold">Status</th>
                    <th class="border border-black p-1.5 font-bold">Checklist Setter</th>
                    <th class="border border-black p-1.5 font-bold">Checklist Pracetak</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi->jadwalMuat as $j)
                    <tr>
                        <td class="border border-black p-1.5 font-bold">Edisi #{{ $j->edisi_ke }}</td>
                        <td class="border border-black p-1.5 font-bold font-mono">
                            {{ \Carbon\Carbon::parse($j->tanggal_muat)->translatedFormat('l, d F Y') }}
                        </td>
                        <td class="border border-black p-1.5 font-bold">{{ $j->status_tayang }}</td>
                        <td class="border border-black p-1.5 text-center font-bold">[ &nbsp; &nbsp; ]</td>
                        <td class="border border-black p-1.5 text-center font-bold">[ &nbsp; &nbsp; ]</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Tanda Tangan Verifikasi Operasional -->
    <div class="grid grid-cols-3 gap-4 text-center text-xs text-black pt-4">
        <div>
            <p class="font-bold mb-14">Front Office / Admin Iklan</p>
            <p class="font-bold underline">{{ $transaksi->inputOleh->name ?? 'Admin Iklan' }}</p>
        </div>
        <div>
            <p class="font-bold mb-14">Setter / Layout Redaksi</p>
            <p class="font-bold">( ............................................ )</p>
        </div>
        <div>
            <p class="font-bold mb-14">Koordinator Pracetak</p>
            <p class="font-bold">( ............................................ )</p>
        </div>
    </div>

</body>
</html>
