<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPPI / Insertion Order - {{ $sppi->nomor_sppi }} - Kabar Priangan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; padding: 0 !important; font-size: 11px !important; }
            .print-card { border: none !important; box-shadow: none !important; padding: 0 !important; max-width: 100% !important; }
            @page { margin: 12mm 15mm; size: A4 portrait; }
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 text-slate-900">

    <!-- Top Action Bar (No-Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 px-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('transaksikoran.index') }}" 
               class="px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-xs transition">
                &larr; Kembali ke Daftar Transaksi
            </a>
            <span class="text-xs text-slate-500 font-mono">
                Status SPPI: 
                <strong class="{{ $sppi->status_sppi === 'Sudah_Ditandatangani' ? 'text-emerald-700' : 'text-amber-600' }}">
                    {{ str_replace('_', ' ', $sppi->status_sppi) }}
                </strong>
            </span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" 
                    class="px-4 py-2 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-lg shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Cetak SPPI / PDF</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="no-print max-w-4xl mx-auto mb-4 px-4">
            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-lg">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- SPPI Document Body -->
    <div class="print-card max-w-4xl mx-auto bg-white border border-slate-200 rounded-2xl shadow-sm p-8 sm:p-10 space-y-6">

        <!-- Kop Surat HU Kabar Priangan -->
        <div class="border-b-2 border-slate-900 pb-5 flex items-start justify-between">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-14 w-auto object-contain">
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight leading-none uppercase">
                        HARIAN UMUM KABAR PRIANGAN
                    </h1>
                    <p class="text-[11px] text-slate-500 font-medium mt-1">
                        Penerbit: PT Berkah Pikiran Rakyat Media • Kelompok Pikiran Rakyat (PR Group)
                    </p>
                    <p class="text-[10px] text-slate-400 mt-0.5">
                        Kantor Redaksi & Iklan: Jl. KHZ. Mustofa No. 343, Kota Tasikmalaya 46124 • Telp: (0265) 334567 • Email: iklan@kabarpriangan.co.id
                    </p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-2.5 py-1 bg-slate-100 border border-slate-300 rounded font-bold font-mono text-[11px] text-slate-900 uppercase">
                    SURAT KONTRAK IKLAN
                </span>
                <div class="text-[11px] font-mono text-slate-500 mt-1.5">
                    No: <strong class="text-slate-900">{{ $sppi->nomor_sppi }}</strong>
                </div>
                <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                    Faktur: {{ $transaksi->nofakturkoran }}
                </div>
            </div>
        </div>

        <!-- Judul Dokumen Legal -->
        <div class="text-center py-1">
            <h2 class="text-base font-extrabold text-slate-900 uppercase tracking-wide">
                SURAT PERJANJIAN PEMASANGAN IKLAN (SPPI)
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                (Advertising Insertion Order / Order Penempatan Iklan Cetak)
            </p>
        </div>

        <p class="text-xs text-slate-700 leading-relaxed text-justify">
            Pada hari ini, tanggal <strong>{{ \Carbon\Carbon::parse($sppi->tanggal_sppi)->translatedFormat('d F Y') }}</strong>, telah disepakati perjanjian pemuatan iklan antara <strong>Harian Umum Kabar Priangan</strong> (disebut sebagai PIHAK PERTAMA) dan Pemasang Iklan / Kuasa Usaha (disebut sebagai PIHAK KEDUA) dengan ketentuan sebagaimana diuraikan di bawah ini:
        </p>

        <!-- Identitas Pemasang / Klien (Pihak Kedua) -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs">
            <span class="font-bold text-slate-900 uppercase tracking-wide block mb-2 text-[11px] border-b border-slate-200 pb-1">
                A. DATA PEMASANG / INSTANSI / BIRO IKLAN (PIHAK KEDUA)
            </span>
            <div class="grid grid-cols-2 gap-y-2 gap-x-4">
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Nama Instansi / Perusahaan</span>
                    <strong class="text-slate-900 font-bold text-sm">{{ $transaksi->nama_pemasangkoran }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">NPWP / NIK Pemasang</span>
                    <span class="font-mono font-medium text-slate-800">{{ $transaksi->pelanggan->npwp_nik ?? '-' }}</span>
                </div>
                <div class="col-span-2">
                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Alamat Lengkap</span>
                    <span class="text-slate-800 leading-tight">{{ $transaksi->alamat_pemasangkoran }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Kontak PIC / Penanggung Jawab</span>
                    <span class="text-slate-800 font-medium">{{ $transaksi->pelanggan->kontak_pic ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">No. Telepon / HP</span>
                    <span class="text-slate-800 font-mono">{{ $transaksi->pelanggan->no_telp ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Spesifikasi Iklan & Jadwal Terbit -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs">
            <span class="font-bold text-slate-900 uppercase tracking-wide block mb-2 text-[11px] border-b border-slate-200 pb-1">
                B. SPESIFIKASI IKLAN & JADWAL PEMUATAN
            </span>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-y-3 gap-x-4">
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Kategori Iklan</span>
                    <span class="font-medium text-slate-800">{{ $transaksi->kategori_form ?? $transaksi->iklankoran->jenis_iklankoran ?? 'KORAN' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Ukuran Iklan</span>
                    <span class="font-normal text-slate-800">{{ $transaksi->ukuran_iklan ?: ($transaksi->jumlah_baris ? $transaksi->jumlah_baris . ' Baris' : '-') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Warna Cetak</span>
                    <span class="font-medium text-slate-800">{{ $transaksi->warna_iklan }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Halaman Penempatan</span>
                    <span class="font-medium text-slate-800">{{ $transaksi->halaman_iklan }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Jumlah Terbit</span>
                    <span class="font-medium text-slate-800">{{ $transaksi->total_muatkoran }} Edisi</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Kanal Order</span>
                    <span class="font-medium text-slate-800">{{ $transaksi->kanal_order ?? 'FO' }}</span>
                </div>
                <div class="col-span-2">
                    <span class="text-slate-400 block text-[10px] uppercase font-semibold">Jadwal Tanggal Pemuatan</span>
                    <span class="font-normal text-slate-800">
                        @if(!empty($transaksi->tanggal_muat_list))
                            {{ implode(', ', $transaksi->tanggal_muat_list) }}
                        @else
                            {{ \Carbon\Carbon::parse($transaksi->tanggal_muatkoran)->translatedFormat('d F Y') }}
                        @endif
                    </span>
                </div>
            </div>

            @if($transaksi->naskah_iklan_baris)
                <div class="mt-3 pt-2.5 border-t border-slate-200">
                    <span class="text-slate-400 block text-[10px] uppercase font-semibold mb-1">Naskah Iklan Baris:</span>
                    <div class="p-2.5 bg-white border border-slate-200 rounded font-mono text-[11px] leading-tight text-slate-800 whitespace-pre-wrap">
                        {{ $transaksi->naskah_iklan_baris }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Rincian Biaya & Perhitungan Pajak -->
        <div class="border border-slate-200 rounded-xl overflow-hidden text-xs">
            <table class="w-full text-left">
                <thead class="bg-slate-100 border-b border-slate-200 font-bold uppercase text-[10px] text-slate-600">
                    <tr>
                        <th class="py-2 px-3">Rincian Komponen Biaya</th>
                        <th class="py-2 px-3 text-right">Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    <tr>
                        <td class="py-2 px-3">Tarif Bruto ({{ $transaksi->total_muatkoran }} Edisi Pemuatan)</td>
                        <td class="py-2 px-3 text-right font-tabular">
                            Rp {{ number_format($transaksi->harga_transaksikoran * $transaksi->total_muatkoran, 0, ',', '.') }}
                        </td>
                    </tr>
                    @if($transaksi->diskon_transaksikoran > 0)
                        <tr class="text-rose-600">
                            <td class="py-2 px-3">Potongan Diskon (Agensi / Kuantitas / Khusus)</td>
                            <td class="py-2 px-3 text-right font-tabular">
                                - Rp {{ number_format($transaksi->diskon_transaksikoran, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                    <tr class="bg-slate-50/50">
                        <td class="py-2 px-3 text-slate-500 text-[11px]">Dasar Pengenaan Pajak (DPP)</td>
                        <td class="py-2 px-3 text-right font-mono text-[11px] text-slate-600 font-tabular">
                            Rp {{ number_format(round(($transaksi->totaltagihan_transaksikoran - $transaksi->materai) / (1 + (($transaksi->ppn_persen ?: \App\Models\SistemConfig::getPpnPersen()) / 100))), 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3">PPN {{ $transaksi->ppn_persen ?: \App\Models\SistemConfig::getPpnPersen() }}% (Pajak Pertambahan Nilai Resmi)</td>
                        <td class="py-2 px-3 text-right font-tabular">
                            Rp {{ number_format($transaksi->ppn_transaksikoran, 0, ',', '.') }}
                        </td>
                    </tr>
                    @if($transaksi->materai > 0)
                        <tr>
                            <td class="py-2 px-3">Bea Meterai Dokumen Transaksi (UU No. 10/2020)</td>
                            <td class="py-2 px-3 text-right font-tabular">
                                Rp {{ number_format($transaksi->materai, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                    <tr class="bg-slate-100/80 font-bold text-slate-900">
                        <td class="py-2.5 px-3 uppercase text-[11px]">TOTAL NILAI KONTRAK IKLAN</td>
                        <td class="py-2.5 px-3 text-right font-bold text-sm text-kp-blue-700 font-tabular">
                            Rp {{ number_format($transaksi->totaltagihan_transaksikoran, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3">Status Pembayaran: 
                            <strong class="{{ $transaksi->piutang_transaksikoran <= 0 ? 'text-emerald-700' : 'text-amber-700' }}">
                                {{ $transaksi->jenis_pembayaran }} {{ $transaksi->piutang_transaksikoran <= 0 ? '(LUNAS)' : '(Sisa Piutang: Rp ' . number_format($transaksi->piutang_transaksikoran, 0, ',', '.') . ')' }}
                            </strong>
                        </td>
                        <td class="py-2 px-3 text-right text-slate-500 font-tabular">
                            Uang Masuk: Rp {{ number_format($transaksi->jumlahbayar_transaksikoran, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Klausul Baku Perjanjian Pers & Legal -->
        <div class="border border-slate-200 rounded-xl p-3.5 bg-slate-50 text-[10px] text-slate-600 space-y-1.5 leading-relaxed text-justify">
            <span class="font-bold text-slate-900 uppercase block text-[10px]">KETENTUAN & KLAUSUL HUKUM PEMUATAN IKLAN:</span>
            <ol class="list-decimal list-inside space-y-1">
                <li><strong>Materi Iklan:</strong> Seluruh naskah, foto, gambar, dan konten iklan sepenuhnya menjadi tanggung jawab PIHAK KEDUA serta tidak bertentangan dengan UU Pers No. 40/1999, Kode Etik Jurnalistik, dan Kode Etik Pariwara Indonesia (EPI).</li>
                <li><strong>Hak Koreksi Redaksi:</strong> PIHAK PERTAMA berhak menolak atau meminta revisi materi iklan yang mengandung unsur SARA, ujaran kebencian, pornografi, penipuan, atau berpotensi melanggar hukum.</li>
                <li><strong>Deadline Materi Siap Cetak:</strong> Materi lengkap wajib diserahkan paling lambat pukul 14.00 WIB satu hari sebelum tanggal terbit yang disepakati. Keterlambatan penyerahan materi berakibat penundaan edisi terbit tanpa pembatalan kewajiban pembayaran.</li>
                <li><strong>Bukti Muat:</strong> PIHAK PERTAMA akan melampirkan bukti muat (tear sheet koran fisik atau kliping digital) bersama faktur penagihan resmi.</li>
                <li><strong>Sengketa:</strong> Apabila timbul perselisihan di kemudian hari, kedua belah pihak sepakat mengutamakan musyawarah untuk mufakat. Apabila tidak tercapai mufakat, perselisihan diselesaikan melalui domisili hukum Pengadilan Negeri Tasikmalaya.</li>
            </ol>
        </div>

        <!-- Kolom Tanda Tangan Dua Belah Pihak -->
        <div class="grid grid-cols-2 gap-8 pt-4 text-xs">
            <div class="text-center space-y-1">
                <span class="text-slate-500 uppercase text-[10px] font-semibold">PIHAK KEDUA (Pemasang / Instansi)</span>
                <p class="font-semibold text-slate-900">{{ $transaksi->nama_pemasangkoran }}</p>
                
                <div class="h-20 flex items-center justify-center">
                    @if($sppi->status_sppi === 'Sudah_Ditandatangani')
                        <div class="border-2 border-dashed border-emerald-500 bg-emerald-50 text-emerald-800 px-4 py-2 rounded-lg text-center">
                            <span class="font-bold block text-xs">SUDAH DITANDATANGANI</span>
                            <span class="text-[10px] font-mono text-emerald-700">Tgl: {{ \Carbon\Carbon::parse($sppi->tanggal_ditandatangani)->format('d/m/Y') }}</span>
                        </div>
                    @else
                        <div class="text-slate-300 border-b border-slate-300 w-48 mx-auto mt-16"></div>
                    @endif
                </div>

                <p class="font-bold text-slate-900 uppercase">
                    ( {{ $sppi->ditandatangani_oleh_nama ?: ($transaksi->pelanggan->kontak_pic ?: '..........................................') }} )
                </p>
                <span class="text-[10px] text-slate-400">Tanda Tangan & Stempel Instansi</span>
            </div>

            <div class="text-center space-y-1">
                <span class="text-slate-500 uppercase text-[10px] font-semibold">PIHAK PERTAMA</span>
                <p class="font-semibold text-slate-900">HU KABAR PRIANGAN</p>

                <div class="h-20 flex items-center justify-center">
                    <div class="border-2 border-dashed border-kp-blue-500 bg-kp-blue-50 text-kp-blue-800 px-4 py-2 rounded-lg text-center">
                        <span class="font-bold block text-xs">DIVISI IKLAN KP</span>
                        <span class="text-[10px] font-mono text-kp-blue-700">TERVERIFIKASI SISTEM</span>
                    </div>
                </div>

                <p class="font-bold text-slate-900 uppercase">
                    ( {{ $transaksi->sales_iklankoran ?: 'Bagian Pemasaran Iklan' }} )
                </p>
                <span class="text-[10px] text-slate-400">Account Executive / Manajer Iklan</span>
            </div>
        </div>

    </div>

    <!-- Form Konfirmasi Tanda Tangan (No-Print) -->
    @if($sppi->status_sppi !== 'Sudah_Ditandatangani')
        <div class="no-print max-w-4xl mx-auto mt-6 px-4">
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wide mb-1">
                    Konfirmasi Tanda Tangan Kontrak SPPI
                </h3>
                <p class="text-xs text-slate-500 mb-4">
                    Setelah klien membubuhkan tanda tangan fisik pada lembar cetak atau dokumen digital, verifikasi status di sini:
                </p>
                <form method="POST" action="{{ route('sppi.konfirmasi-ttd', $sppi->id) }}" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <input type="text" name="ditandatangani_oleh_nama" required placeholder="Nama lengkap penandatangan klien..."
                           value="{{ $transaksi->pelanggan->kontak_pic ?? $transaksi->nama_pemasangkoran }}"
                           class="flex-1 h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    <input type="date" name="tanggal_ditandatangani" value="{{ date('Y-m-d') }}" required
                           class="h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    <button type="submit" class="px-5 h-10 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-xs transition cursor-pointer shrink-0">
                        Tandai Sudah Ditandatangani
                    </button>
                </form>
            </div>
        </div>
    @endif

</body>
</html>
