<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Tugas Penagihan Inkaso - {{ $penugasan->no_tugas }}</title>
    <link rel="icon" href="{{ asset('kabarpriangan.png') }}" type="image/png">
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-shadow { box-shadow: none !important; border: 1px solid #cbd5e1 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans p-4 sm:p-8">

    <!-- Tombol Navigasi Cetak -->
    <div class="max-w-3xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('keuangan.inkaso.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs">
            ← Kembali ke Modul Inkaso
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-md transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Surat Tugas Penagihan</span>
        </button>
    </div>

    <!-- Lembar Surat Tugas -->
    <div class="max-w-3xl mx-auto bg-white p-8 sm:p-12 rounded-2xl border border-slate-200 shadow-lg print-shadow">
        
        <!-- Kop Surat -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-5 mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-12 w-auto object-contain shrink-0" onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <h1 class="text-lg font-black text-slate-900 uppercase tracking-tight">HARIAN UMUM KABAR PRIANGAN</h1>
                    <p class="text-xs text-slate-600">DIVISI KEUANGAN &bull; BAGIAN INKASO &amp; PENAGIHAN PIUTANG</p>
                    <p class="text-[11px] text-slate-500">Jl. Dr. Sukardjo No. 51 Kota Tasikmalaya | Telp: (0265) 331234</p>
                </div>
            </div>

            <div class="text-right">
                <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-amber-800 bg-amber-50 px-2.5 py-1 rounded border border-amber-200 block mb-1">
                    SURAT TUGAS PENAGIHAN
                </span>
                <span class="text-xs font-mono font-black text-slate-900">{{ $penugasan->no_tugas }}</span>
            </div>
        </div>

        <div class="text-center mb-6">
            <h2 class="text-base font-black text-slate-900 uppercase tracking-wide underline underline-offset-4">
                SURAT TUGAS PENAGIHAN LAPANGAN (INKASO)
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Nomor: {{ $penugasan->no_tugas }} / ST-INK / {{ date('m/Y', strtotime($penugasan->tanggal_tugas)) }}
            </p>
        </div>

        <div class="text-xs text-slate-700 leading-relaxed mb-6 space-y-3">
            <p>
                Yang bertanda tangan di bawah ini, Kepala Divisi Keuangan Harian Umum Kabar Priangan memberikan penugasan resmi kepada:
            </p>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-1.5 font-medium">
                <div class="flex">
                    <span class="w-36 text-slate-500">Nama Petugas:</span>
                    <strong class="text-slate-900 uppercase">{{ $penugasan->petugas->name ?? '-' }}</strong>
                </div>
                <div class="flex">
                    <span class="w-36 text-slate-500">Jabatan:</span>
                    <span>Petugas Penagihan Lapangan (Inkaso)</span>
                </div>
                <div class="flex">
                    <span class="w-36 text-slate-500">Tanggal Penugasan:</span>
                    <span class="font-mono">{{ $penugasan->tanggal_tugas->translatedFormat('d F Y') }}</span>
                </div>
            </div>

            <p>
                Untuk melakukan penagihan atas sisa kewajiban piutang transaksi penjualan penerbitan media sebagai berikut:
            </p>

            <!-- Tabel Rincian Piutang -->
            <table class="w-full text-xs text-left my-4 border border-slate-300">
                <thead class="bg-slate-100 uppercase text-[11px] font-bold text-slate-700 border-b border-slate-300">
                    <tr>
                        <th class="py-2 px-3">Nomor Faktur</th>
                        <th class="py-2 px-3">Nama Customer / Klien</th>
                        <th class="py-2 px-3">Alamat Pelanggan</th>
                        <th class="py-2 px-3 text-right">Sisa Piutang yang Harus Ditagih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <tr>
                        <td class="py-2.5 px-3 font-mono font-bold text-blue-700">{{ $penugasan->faktur->no_faktur }}</td>
                        <td class="py-2.5 px-3 font-bold text-slate-900">{{ $penugasan->faktur->customer->nama_customer ?? '-' }}</td>
                        <td class="py-2.5 px-3 text-slate-600">{{ $penugasan->faktur->customer->alamat ?? '-' }}</td>
                        <td class="py-2.5 px-3 text-right font-black font-mono text-rose-600 text-sm">
                            Rp {{ number_format($penugasan->faktur->sisa_piutang, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>

            @if($penugasan->catatan)
                <div class="p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-xs">
                    <strong>Catatan / Instruksi:</strong> {{ $penugasan->catatan }}
                </div>
            @endif

            <p class="pt-2">
                Demikian surat tugas ini diterbitkan agar dapat dilaksanakan dengan penuh tanggung jawab. Hasil setoran wajib segera diserahkan ke Bagian Kasir selambat-lambatnya 1 x 24 jam setelah penagihan dilakukan.
            </p>
        </div>

        <!-- Tanda Tangan -->
        <div class="grid grid-cols-2 gap-8 text-center text-xs pt-8 border-t border-slate-200">
            <div>
                <span class="text-slate-500 block mb-16">Petugas Penagih (Inkaso),</span>
                <div class="border-t border-slate-400 pt-1 font-bold text-slate-800">
                    ( {{ $penugasan->petugas->name ?? 'Petugas Inkaso' }} )
                </div>
            </div>
            <div>
                <span class="text-slate-500 block mb-16">Tasikmalaya, {{ $penugasan->tanggal_tugas->translatedFormat('d F Y') }}<br>Kepala Divisi Keuangan,</span>
                <div class="border-t border-slate-400 pt-1 font-bold text-slate-800">
                    ( H. Asep Ridwan, S.E. )
                </div>
            </div>
        </div>

    </div>

</body>
</html>
