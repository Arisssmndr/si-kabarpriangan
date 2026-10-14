<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kas Bulanan - {{ $bulan }}/{{ $tahun }}</title>
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

    <!-- Tombol Cetak -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('keuangan.kas.bulanan', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs">
            ← Kembali
        </a>
        <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition cursor-pointer">
            🖨 Cetak Laporan Bulanan (Print / PDF)
        </button>
    </div>

    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-2xl border border-slate-200 shadow-lg print-shadow">
        <!-- Kop Surat -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-5 mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-12 w-auto object-contain shrink-0" onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <h1 class="text-base font-black text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</h1>
                    <p class="text-xs text-slate-600">DIVISI KEUANGAN &bull; LAPORAN ARUS KAS BULANAN (MONTHLY CASH REPORT)</p>
                    <p class="text-[11px] text-slate-500">Jl. Dr. Sukardjo No. 51 Kota Tasikmalaya | Telp: (0265) 331234</p>
                </div>
            </div>

            <div class="text-right">
                <span class="text-xs font-mono font-bold text-slate-500 block">Periode Pelaporan:</span>
                <strong class="text-sm font-black font-mono text-slate-900">
                    {{ \Carbon\Carbon::create(null, $bulan)->translatedFormat('F') }} {{ $tahun }}
                </strong>
            </div>
        </div>

        <!-- Ringkasan Periode -->
        <div class="grid grid-cols-3 gap-4 text-xs p-4 rounded-xl bg-slate-50 border border-slate-200 mb-6 font-mono">
            <div>
                <span class="text-slate-500 block">Total Pemasukan:</span>
                <strong class="text-emerald-700 text-sm">+ Rp {{ number_format($totalMasuk, 0, ',', '.') }}</strong>
            </div>
            <div>
                <span class="text-slate-500 block">Total Pengeluaran:</span>
                <strong class="text-rose-700 text-sm">- Rp {{ number_format($totalKeluar, 0, ',', '.') }}</strong>
            </div>
            <div>
                <span class="text-slate-500 block">Surplus / (Defisit):</span>
                <strong class="{{ ($totalMasuk - $totalKeluar) >= 0 ? 'text-blue-900' : 'text-rose-700' }} text-sm">
                    Rp {{ number_format($totalMasuk - $totalKeluar, 0, ',', '.') }}
                </strong>
            </div>
        </div>

        <!-- Tabel Mutasi Bulanan -->
        <table class="w-full text-xs text-left mb-8 border border-slate-300">
            <thead class="bg-slate-100 uppercase text-[11px] font-bold text-slate-700 border-b border-slate-300">
                <tr>
                    <th class="py-2.5 px-3">Tgl</th>
                    <th class="py-2.5 px-3">No. Transaksi</th>
                    <th class="py-2.5 px-3">Kategori</th>
                    <th class="py-2.5 px-3">Keterangan</th>
                    <th class="py-2.5 px-3 text-right">Masuk (Rp)</th>
                    <th class="py-2.5 px-3 text-right">Keluar (Rp)</th>
                    <th class="py-2.5 px-3 text-right">Saldo Akhir (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($transaksis as $t)
                    <tr>
                        <td class="py-2.5 px-3 font-mono text-slate-500">{{ $t->tanggal->format('d/m') }}</td>
                        <td class="py-2.5 px-3 font-mono font-bold">{{ $t->no_transaksi }}</td>
                        <td class="py-2.5 px-3 font-semibold">{{ $t->kategori_kas }}</td>
                        <td class="py-2.5 px-3 text-slate-700">{{ $t->keterangan }}</td>
                        <td class="py-2.5 px-3 text-right font-mono font-bold text-emerald-700">
                            {{ $t->jenis === 'masuk' ? number_format($t->nominal, 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono font-bold text-rose-700">
                            {{ $t->jenis === 'keluar' ? number_format($t->nominal, 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono font-black">
                            {{ number_format($t->saldo_akhir, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-slate-400">Tidak ada transaksi pada bulan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tanda Tangan -->
        <div class="grid grid-cols-2 gap-8 text-center text-xs pt-8 border-t border-slate-200">
            <div>
                <span class="text-slate-500 block mb-14">Dibuat Oleh Kasir,</span>
                <div class="border-t border-slate-400 pt-1 font-bold text-slate-800">
                    ( Staf Kasir Keuangan )
                </div>
            </div>
            <div>
                <span class="text-slate-500 block mb-14">Tasikmalaya, {{ date('d F Y') }}<br>Mengetahui Kepala Divisi Keuangan,</span>
                <div class="border-t border-slate-400 pt-1 font-bold text-slate-800">
                    ( H. Asep Ridwan, S.E. )
                </div>
            </div>
        </div>
    </div>

</body>
</html>
