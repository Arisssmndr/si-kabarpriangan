<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Penjualan - {{ $faktur->no_faktur }}</title>
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

    <!-- Tombol Navigasi Cetak (Non-Printable) -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('keuangan.faktur.show', $faktur->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs">
            ← Kembali ke Faktur
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Faktur (Print / PDF)</span>
        </button>
    </div>

    <!-- Lembar Dokumen Faktur Resmi -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-2xl border border-slate-200 shadow-lg print-shadow">
        
        <!-- Kop Surat -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-6 mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-14 w-auto object-contain shrink-0" onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight uppercase">HARIAN UMUM KABAR PRIANGAN</h1>
                    <p class="text-xs text-slate-600 mt-0.5">PT BERKAH KABAR PRIANGAN &bull; GRUP PIKIRAN RAKYAT</p>
                    <p class="text-[11px] text-slate-500 mt-0.5 leading-tight">
                        Jl. Dr. Sukardjo No. 51 Kota Tasikmalaya 46112 | Telp: (0265) 331234 | Email: keuangan@kabarpriangan.co.id
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right">
                    <span class="text-xs font-mono font-bold uppercase tracking-widest text-blue-700 bg-blue-50 px-3 py-1 rounded-lg border border-blue-200 block mb-1">
                        FAKTUR PENJUALAN
                    </span>
                    <span class="text-sm font-black font-mono text-slate-900 block whitespace-nowrap">{{ $faktur->no_faktur }}</span>
                    <span class="text-[10px] text-slate-500 block uppercase font-mono mt-0.5">DIVISI {{ strtoupper($faktur->kategori) }}</span>
                </div>
                <div class="p-1.5 bg-white border border-slate-300 rounded-lg shadow-2xs text-center shrink-0">
                    <div class="w-[72px] h-[72px] flex items-center justify-center">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(72)->margin(0)->generate($faktur->qr_kasir_payload) !!}
                    </div>
                    <span class="block text-[8px] font-mono font-bold text-slate-600 mt-1 uppercase tracking-wider">SCAN KASIR</span>
                </div>
            </div>
        </div>

        <!-- Detail Tagihan & Customer -->
        <div class="grid grid-cols-2 gap-6 text-xs mb-6 pb-6 border-b border-slate-200">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1 font-mono">TAGIHAN KEPADA (CUSTOMER):</span>
                <h2 class="text-sm font-black text-slate-900">{{ $faktur->customer->nama_customer ?? '-' }}</h2>
                <p class="text-slate-600 mt-1 leading-relaxed">{{ $faktur->customer->alamat ?? '-' }}</p>
                <p class="text-slate-600 mt-0.5">Telp: {{ $faktur->customer->no_telepon ?? '-' }}</p>
            </div>

            <div class="text-right space-y-1 text-slate-700">
                <div>
                    <span class="text-slate-500">Tanggal Faktur:</span>
                    <strong class="text-slate-900 font-mono">{{ $faktur->tanggal_faktur->translatedFormat('d F Y') }}</strong>
                </div>
                <div>
                    <span class="text-slate-500">Jatuh Tempo:</span>
                    <strong class="text-rose-600 font-mono">{{ $faktur->jatuh_tempo->translatedFormat('d F Y') }}</strong>
                </div>
                <div>
                    <span class="text-slate-500">Status Pembayaran:</span>
                    @if($faktur->status === 'lunas')
                        <span class="px-2 py-0.5 rounded font-bold bg-emerald-100 text-emerald-800 uppercase font-mono">LUNAS</span>
                    @elseif($faktur->status === 'cicilan')
                        <span class="px-2 py-0.5 rounded font-bold bg-amber-100 text-amber-800 uppercase font-mono">CICILAN</span>
                    @else
                        <span class="px-2 py-0.5 rounded font-bold bg-rose-100 text-rose-800 uppercase font-mono">BELUM BAYAR</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tabel Rincian -->
        <table class="w-full text-xs text-left mb-6">
            <thead class="bg-slate-100 uppercase text-[11px] font-bold text-slate-700 border-y border-slate-300">
                <tr>
                    <th class="py-2.5 px-3 w-10 text-center">No</th>
                    <th class="py-2.5 px-3">Uraian / Deskripsi Item Transaksi</th>
                    <th class="py-2.5 px-3 w-20 text-center">Qty</th>
                    <th class="py-2.5 px-3 w-36 text-right">Harga Satuan</th>
                    <th class="py-2.5 px-3 w-40 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @if(is_array($faktur->rincian_biaya))
                    @foreach($faktur->rincian_biaya as $idx => $item)
                        <tr>
                            <td class="py-2.5 px-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                            <td class="py-2.5 px-3 font-semibold text-slate-800">{{ $item['item'] ?? '-' }}</td>
                            <td class="py-2.5 px-3 text-center font-mono">{{ $item['qty'] ?? 1 }}</td>
                            <td class="py-2.5 px-3 text-right font-mono">Rp {{ number_format($item['harga'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-right font-bold font-mono text-slate-900">Rp {{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot class="border-t-2 border-slate-900 text-xs">
                <tr>
                    <td colspan="4" class="py-2 px-3 text-right font-bold uppercase text-slate-700">Total Tagihan:</td>
                    <td class="py-2 px-3 text-right font-black font-mono text-slate-900 text-sm">
                        Rp {{ number_format($faktur->total_tagihan, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="py-1 px-3 text-right font-semibold text-emerald-700">Sudah Dibayar (Masuk Kas):</td>
                    <td class="py-1 px-3 text-right font-bold font-mono text-emerald-700">
                        Rp {{ number_format($faktur->total_terbayar, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="border-t border-slate-300">
                    <td colspan="4" class="py-2 px-3 text-right font-black uppercase text-rose-700">Sisa Tagihan / Piutang:</td>
                    <td class="py-2 px-3 text-right font-black font-mono text-rose-700 text-sm">
                        Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Catatan, Rekening & Validasi Kasir -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs p-4 rounded-xl bg-slate-50 border border-slate-200 mb-8 items-center">
            <div>
                <span class="font-bold text-slate-700 block mb-1">Catatan Transaksi:</span>
                <p class="text-slate-600 leading-relaxed italic text-[11px]">
                    {{ $faktur->catatan ?? 'Pembayaran dapat disetorkan langsung ke Kasir atau melalui Petugas Inkaso resmi yang membawa Surat Tugas.' }}
                </p>
            </div>
            <div>
                <span class="font-bold text-slate-700 block mb-1">Rekening Resmi Pembayaran:</span>
                <p class="text-slate-800 font-mono text-[11px] leading-tight">
                    Bank BJB Cabang Tasikmalaya<br>
                    No. Rek: <strong>0012-3456-78901</strong><br>
                    a/n: PT Berkah Kabar Priangan
                </p>
            </div>
            <div class="flex items-center gap-3 border-t md:border-t-0 md:border-l border-slate-200 pt-3 md:pt-0 md:pl-4">
                <div class="p-1.5 bg-white border border-slate-300 rounded-lg shadow-2xs shrink-0">
                    <div class="w-[64px] h-[64px] flex items-center justify-center">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(64)->margin(0)->generate($faktur->qr_kasir_payload) !!}
                    </div>
                </div>
                <div>
                    <span class="text-[10px] font-mono font-bold text-blue-700 uppercase block tracking-wider">KODE QR KASIR</span>
                    <p class="text-[10px] text-slate-500 leading-tight mt-0.5">
                        Scan untuk verifikasi & penerimaan pesanan di loket kasir.
                    </p>
                    <span class="text-[9px] font-mono font-bold text-slate-700 block mt-1">NO: {{ $faktur->no_faktur }}</span>
                </div>
            </div>
        </div>

        <!-- Kolom Tanda Tangan -->
        <div class="grid grid-cols-3 gap-6 text-center text-xs pt-4">
            <div>
                <span class="text-slate-500 block mb-16">Diterima Oleh Customer,</span>
                <div class="border-t border-slate-400 pt-1 font-bold text-slate-800">
                    ( ........................................ )
                </div>
            </div>
            <div>
                <span class="text-slate-500 block mb-16">Petugas Penagih / Inkaso,</span>
                <div class="border-t border-slate-400 pt-1 font-bold text-slate-800">
                    ( {{ $faktur->penugasanAktif->petugas->name ?? '........................................' }} )
                </div>
            </div>
            <div>
                <span class="text-slate-500 block mb-16">Kasir / Bagian Keuangan,</span>
                <div class="border-t border-slate-400 pt-1 font-bold text-slate-800">
                    ( {{ $faktur->creator->name ?? 'Staf Keuangan' }} )
                </div>
            </div>
        </div>

    </div>

</body>
</html>
