<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPPI — {{ $sppiData['nomor_sppi'] ?? $sppi->nomor_sppi }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm;
        }
        @media print {
            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                height: auto !important;
            }
            .no-print {
                display: none !important;
            }
            .sppi-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 antialiased py-6 px-4">

    <!-- Toolbar Layar (No-Print) -->
    <div class="no-print max-w-3xl mx-auto mb-4 flex items-center justify-between gap-3 bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-xs">
        <div class="flex items-center gap-2.5 min-w-0">
            <span class="px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                {{ $sppiData['nomor_sppi'] }}
            </span>
            <span class="text-sm font-semibold text-slate-800 truncate">
                Surat Perintah Pemuatan Iklan (SPPI)
            </span>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ $sppiData['back_url'] }}"
               class="h-9 px-3.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center transition">
                Kembali
            </a>
            <button type="button" onclick="window.print()"
                    class="h-9 px-4 rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold inline-flex items-center shadow-xs transition cursor-pointer">
                Cetak SPPI
            </button>
        </div>
    </div>

    <!-- Lembar Dokumen SPPI 1 Halaman A4 Standar Industri -->
    <div class="sppi-sheet max-w-3xl mx-auto bg-white border border-slate-200 rounded-xl shadow-sm p-6 sm:p-8 space-y-3.5">

        <!-- 1. Kop Resmi Singkat & Proporsional -->
        <div class="flex items-center justify-between gap-4 pb-3 border-b-2 border-slate-900">
            <div class="flex items-center gap-3">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-10 w-auto object-contain">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">PT. Berkah Pikiran Rakyat</div>
                    <h1 class="text-sm font-extrabold text-slate-900 tracking-tight uppercase">Harian Umum Kabar Priangan</h1>
                    <p class="text-[11px] text-slate-500">Jl. R.E. Martadinata No. 193, Kota Tasikmalaya</p>
                </div>
            </div>
            <div class="text-right text-xs leading-snug">
                <div class="text-[11px] font-bold uppercase tracking-wider text-kp-blue-700">Dokumen SPPI</div>
                <div class="font-mono font-bold text-slate-900">{{ $sppiData['nomor_sppi'] }}</div>
                <div class="font-mono text-[11px] text-slate-500">No. Faktur: {{ $sppiData['nofaktur'] }}</div>
            </div>
        </div>

        <!-- 2. Judul Dokumen -->
        <div class="text-center py-0.5">
            <h2 class="text-sm font-bold uppercase tracking-wide text-slate-900 underline underline-offset-4">
                SURAT PERINTAH PEMUATAN IKLAN (SPPI)
            </h2>
            <p class="text-[11px] text-slate-600 mt-0.5">
                Tanggal Order: <span class="font-mono font-semibold text-slate-900">{{ \Carbon\Carbon::parse($sppiData['tanggal_order'])->translatedFormat('d F Y') }}</span>
            </p>
        </div>

        <!-- 3. Bagian I: Data Pemasang -->
        <div class="space-y-1">
            <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">I. Data Pemasang</h3>
            <div class="border border-slate-300 rounded-lg overflow-hidden">
                <table class="w-full text-xs border-collapse no-brand-table">
                    <tbody class="divide-y divide-slate-200">
                        <tr>
                            <td class="py-1.5 px-3 bg-slate-50 font-semibold text-slate-600 w-32">Nama Pemasang</td>
                            <td class="py-1.5 px-3 font-bold text-slate-900">{{ $sppiData['nama_pemasang'] }}</td>
                            <td class="py-1.5 px-3 bg-slate-50 font-semibold text-slate-600 w-28">No. Telepon</td>
                            <td class="py-1.5 px-3 font-mono text-slate-800 w-36">{{ $sppiData['telepon'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5 px-3 bg-slate-50 font-semibold text-slate-600">Alamat</td>
                            <td class="py-1.5 px-3 text-slate-800">{{ $sppiData['alamat'] }}</td>
                            <td class="py-1.5 px-3 bg-slate-50 font-semibold text-slate-600">Sales / AE</td>
                            <td class="py-1.5 px-3 font-semibold text-slate-900">{{ $sppiData['sales'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. Bagian II: Spesifikasi & Jadwal Iklan -->
        <div class="space-y-1">
            <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">II. Spesifikasi &amp; Jadwal Tayang</h3>
            <div class="border border-slate-300 rounded-lg overflow-hidden">
                <table class="w-full text-xs border-collapse no-brand-table">
                    <tbody class="divide-y divide-slate-200">
                        <tr>
                            <td class="py-1.5 px-3 bg-slate-50 font-semibold text-slate-600 w-32">Media Publikasi</td>
                            <td class="py-1.5 px-3 font-semibold text-slate-900">{{ $sppiData['media_label'] }}</td>
                            <td class="py-1.5 px-3 bg-slate-50 font-semibold text-slate-600 w-28">Jenis Iklan</td>
                            <td class="py-1.5 px-3 font-semibold text-slate-900 w-44">{{ $sppiData['jenis_iklan'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5 px-3 bg-slate-50 font-semibold text-slate-600">Spesifikasi</td>
                            <td class="py-1.5 px-3 font-mono text-slate-800">{{ $sppiData['spesifikasi'] }}</td>
                            <td class="py-1.5 px-3 bg-slate-50 font-semibold text-slate-600">Format / Warna</td>
                            <td class="py-1.5 px-3 text-slate-800">{{ $sppiData['warna_media'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5 px-3 bg-slate-50 font-semibold text-slate-600">Penempatan</td>
                            <td class="py-1.5 px-3 text-slate-800">{{ $sppiData['penempatan'] }}</td>
                            <td class="py-1.5 px-3 bg-slate-50 font-semibold text-slate-600">Frekuensi</td>
                            <td class="py-1.5 px-3 font-semibold text-slate-900">{{ $sppiData['frekuensi'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5 px-3 bg-slate-50 font-semibold text-slate-600">Tanggal Tayang</td>
                            <td colspan="3" class="py-1.5 px-3 font-mono font-semibold text-kp-blue-700">{{ $sppiData['jadwal_tayang'] }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5 px-3 bg-slate-50 font-semibold text-slate-600">Materi / Naskah</td>
                            <td colspan="3" class="py-1.5 px-3 text-slate-800">{{ $sppiData['catatan_materi'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. Bagian III: Rincian Biaya & Pembayaran (2 Kolom Kompak) -->
        <div class="space-y-1">
            <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">III. Rincian Biaya &amp; Pembayaran</h3>
            <div class="border border-slate-300 rounded-lg overflow-hidden">
                <table class="w-full text-xs border-collapse no-brand-table">
                    <tbody class="divide-y divide-slate-200">
                        <tr>
                            <td class="py-1.5 px-3 bg-slate-50 font-semibold text-slate-600 w-32">Harga Brutto</td>
                            <td class="py-1.5 px-3 font-mono font-semibold text-slate-900">Rp {{ number_format($sppiData['bruto'], 0, ',', '.') }}</td>
                            <td class="py-1.5 px-3 bg-slate-50 font-semibold text-slate-600 w-36">Potongan / Diskon</td>
                            <td class="py-1.5 px-3 text-right font-mono text-slate-700 w-36">Rp {{ number_format($sppiData['diskon'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5 px-3 bg-slate-50 font-semibold text-slate-600">Netto (DPP)</td>
                            <td class="py-1.5 px-3 font-mono text-slate-800">Rp {{ number_format($sppiData['dpp'], 0, ',', '.') }}</td>
                            <td class="py-1.5 px-3 bg-slate-50 font-semibold text-slate-600">PPN ({{ $sppiData['ppn_persen'] }}%){{ $sppiData['materai'] > 0 ? ' + Meterai' : '' }}</td>
                            <td class="py-1.5 px-3 text-right font-mono text-slate-800">Rp {{ number_format($sppiData['ppn'] + $sppiData['materai'], 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-slate-50">
                            <td class="py-1.5 px-3 font-semibold text-slate-600">Sudah Dibayar ({{ $sppiData['cara_bayar'] }})</td>
                            <td class="py-1.5 px-3 font-mono font-semibold text-slate-900">Rp {{ number_format($sppiData['dibayar'], 0, ',', '.') }}</td>
                            <td class="py-1.5 px-3 font-bold uppercase text-slate-900">Total Tagihan</td>
                            <td class="py-1.5 px-3 text-right font-mono font-bold text-kp-blue-700">Rp {{ number_format($sppiData['total_tagihan'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5 px-3 bg-slate-50 font-semibold text-slate-600">Status Tagihan</td>
                            <td class="py-1.5 px-3 font-semibold {{ $sppiData['piutang'] <= 0 ? 'text-kp-blue-700' : 'text-slate-800' }}">
                                {{ $sppiData['piutang'] <= 0 ? 'Lunas' : 'Piutang / Belum Lunas' }}
                            </td>
                            <td class="py-1.5 px-3 bg-slate-50 font-semibold text-slate-700">Sisa Piutang</td>
                            <td class="py-1.5 px-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($sppiData['piutang'], 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 6. Ketentuan Ringkas Standar Industri -->
        <div class="bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2 text-[11px] text-slate-600 leading-relaxed">
            <span class="font-semibold text-slate-800">Ketentuan:</span>
            (1) Materi iklan menjadi tanggung jawab pemasang dan wajib diterima sebelum jam cut-off harian.
            (2) Bukti pemuatan dilampirkan bersama faktur resmi sebagai dasar penagihan.
        </div>

        <!-- 7. Blok Tanda Tangan 2 Kolom Bersih -->
        <div class="grid grid-cols-2 gap-8 pt-2 text-center text-xs">
            <div>
                <div class="text-slate-600 font-medium">Pemasang Iklan</div>
                <div class="h-14"></div>
                <div class="font-bold text-slate-900 underline">{{ $sppiData['nama_pemasang'] }}</div>
                <div class="text-[11px] text-slate-500">Tanda Tangan &amp; Nama Jelas</div>
            </div>
            <div>
                <div class="text-slate-600 font-medium">Divisi Iklan Kabar Priangan</div>
                <div class="h-14"></div>
                <div class="font-bold text-slate-900 underline">{{ $sppiData['sales'] }}</div>
                <div class="text-[11px] text-slate-500">Account Executive / Front Office</div>
            </div>
        </div>

    </div>

</body>
</html>
