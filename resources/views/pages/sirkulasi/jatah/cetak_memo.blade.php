<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memo Buka Kunci Sirkulasi — {{ $memo->nomor_memo }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                color: #0f172a !important;
            }
            .no-print {
                display: none !important;
            }
            .memo-sheet {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                margin: 0 !important;
                max-width: 100% !important;
                padding: 24px !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 antialiased py-8 px-4">

    <!-- Toolbar Cetak di Layar (60-30-10) -->
    <div class="max-w-3xl mx-auto mb-5 flex items-center justify-between gap-3 bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-xs no-print">
        <div class="flex items-center gap-2.5 min-w-0">
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">
                {{ $memo->nomor_memo }}
            </span>
            <span class="text-sm font-semibold text-slate-800 truncate">
                Memo Otorisasi Pembukaan Kunci Oplah Sirkulasi
            </span>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $memo->tanggal_target?->toDateString()]) }}"
               class="h-9 px-3.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center transition">
                Kembali
            </a>
            <button type="button" onclick="window.print()"
                    class="h-9 px-4 rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold inline-flex items-center shadow-xs transition cursor-pointer">
                Cetak Memo
            </button>
        </div>
    </div>

    @php
        $st = strtoupper((string) ($memo->status_akuntansi ?? ''));
        $isApproved = in_array($st, ['APPROVED', 'OPEN', 'DISETUJUI'], true);
        $isRejected = in_array($st, ['REJECTED', 'DITOLAK'], true);
        $statusTitle = $isApproved ? 'DISETUJUI RESMI' : ($isRejected ? 'DITOLAK' : 'MENUNGGU OTORISASI');
        $statusBorder = $isApproved ? 'border-kp-blue-300 bg-kp-blue-50/50 text-kp-blue-800' : ($isRejected ? 'border-rose-300 bg-rose-50/50 text-rose-800' : 'border-amber-300 bg-amber-50/50 text-amber-800');
        $pengajuNama = $memo->diketahui_oleh ?: ($memo->dibukaOleh->name ?? 'Staf Sirkulasi');
    @endphp

    <!-- Lembar Dokumen Resmi Memo Internal -->
    <div class="memo-sheet max-w-3xl mx-auto bg-white border border-slate-200 rounded-xl shadow-sm p-8 space-y-6">

        <!-- 1. Kop Resmi Perusahaan -->
        <div class="flex items-start justify-between gap-4 pb-4 border-b-2 border-slate-900">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-11 w-auto object-contain shrink-0">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">PT. Berkah Pikiran Rakyat</div>
                    <h1 class="text-lg font-black text-slate-900 tracking-tight uppercase">Harian Umum Kabar Priangan</h1>
                    <p class="text-[11px] text-slate-500">Jl. Sutisna Senjaya No. 120 Tasikmalaya &bull; Telp (0265) 331688</p>
                </div>
            </div>
            <div class="text-right shrink-0">
                <div class="text-xs font-bold uppercase tracking-wider text-kp-blue-700">Memo Internal</div>
                <div class="font-mono text-xs font-bold text-slate-900 mt-0.5">{{ $memo->nomor_memo }}</div>
                <div class="inline-block mt-1 px-2.5 py-0.5 rounded text-[10px] font-bold border {{ $statusBorder }}">
                    {{ $statusTitle }}
                </div>
            </div>
        </div>

        <!-- 2. Metadata Memo -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs space-y-2">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div class="flex">
                    <span class="w-32 text-slate-500 shrink-0">Kepada</span>
                    <span class="font-bold text-slate-900">: Divisi Akuntansi &amp; Keuangan</span>
                </div>
                <div class="flex">
                    <span class="w-32 text-slate-500 shrink-0">Dari</span>
                    <span class="font-bold text-slate-900">: Divisi Sirkulasi &amp; Distribusi</span>
                </div>
                <div class="flex">
                    <span class="w-32 text-slate-500 shrink-0">Pemohon</span>
                    <span class="font-semibold text-slate-900">: {{ $pengajuNama }}</span>
                </div>
                <div class="flex">
                    <span class="w-32 text-slate-500 shrink-0">Tanggal Edisi Target</span>
                    <span class="font-mono font-bold text-kp-blue-700">: {{ $memo->tanggal_target ? $memo->tanggal_target->locale('id')->translatedFormat('l, d F Y') : '-' }}</span>
                </div>
                <div class="flex">
                    <span class="w-32 text-slate-500 shrink-0">Kategori Alasan</span>
                    <span class="font-semibold text-slate-900">: {{ $memo->kategori_urgensi ?: 'Koreksi Oplah Harian' }}</span>
                </div>
                <div class="flex">
                    <span class="w-32 text-slate-500 shrink-0">Waktu Pengajuan</span>
                    <span class="font-mono text-slate-900">: {{ ($memo->created_at ?? now())->translatedFormat('d F Y, H:i') }} WIB</span>
                </div>
                @if($memo->estimasi_nominal > 0)
                <div class="flex">
                    <span class="w-32 text-slate-500 shrink-0">Estimasi Nilai</span>
                    <span class="font-mono font-bold text-slate-900">: Rp {{ number_format($memo->estimasi_nominal, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($memo->spesifikasi_iklan)
                <div class="flex">
                    <span class="w-32 text-slate-500 shrink-0">Keterangan Oplah</span>
                    <span class="font-mono text-slate-900">: {{ $memo->spesifikasi_iklan }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- 3. Alasan Permohonan Pembukaan Kunci -->
        <div class="space-y-1.5">
            <div class="text-xs font-bold text-slate-700 uppercase tracking-wider">Alasan Permohonan Pembukaan Kunci:</div>
            <div class="p-4 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 leading-relaxed min-h-[64px]">
                {{ $memo->alasan }}
            </div>
        </div>

        <!-- 4. Keputusan / Tanggapan Divisi Akuntansi -->
        <div class="space-y-1.5">
            <div class="text-xs font-bold text-slate-700 uppercase tracking-wider">Keputusan &amp; Verifikasi Divisi Akuntansi:</div>
            <div class="p-4 rounded-xl border {{ $statusBorder }} text-xs leading-relaxed space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="font-bold">STATUS: {{ $statusTitle }}</span>
                    @if($memo->diterima_akuntansi_pada || $memo->ditolak_pada)
                        <span class="font-mono text-[11px]">{{ ($memo->diterima_akuntansi_pada ?? $memo->ditolak_pada)->translatedFormat('d F Y, H:i') }} WIB</span>
                    @endif
                </div>
                <p class="text-slate-800">
                    <strong>Catatan Akuntansi:</strong>
                    {{ $memo->catatan_akuntansi ?: ($isApproved ? 'Permohonan disetujui. Kunci data edisi dibuka sementara.' : ($isRejected ? 'Permohonan ditolak oleh Akuntansi.' : 'Menunggu verifikasi otorisasi dari Bagian Akuntansi.')) }}
                </p>
            </div>
        </div>

        <!-- 5. Kolom Tanda Tangan Pengesahan (3 Kolom) -->
        <div class="pt-4 border-t border-slate-200">
            <div class="grid grid-cols-3 gap-4 text-center text-xs">
                <div class="border border-slate-200 rounded-xl p-3 bg-slate-50/50">
                    <div class="text-slate-500">Diajukan Oleh,</div>
                    <div class="h-12 flex items-center justify-center font-mono text-[11px] text-slate-400">
                        {{ ($memo->created_at ?? now())->format('d/m/Y') }}
                    </div>
                    <div class="font-bold text-slate-900 underline">{{ $pengajuNama }}</div>
                    <div class="text-[11px] text-slate-500">Divisi Sirkulasi</div>
                </div>

                <div class="border border-slate-200 rounded-xl p-3 bg-slate-50/50">
                    <div class="text-slate-500">Mengetahui,</div>
                    <div class="h-12 flex items-center justify-center font-mono text-[11px] text-slate-400">
                        Koordinator Distribusi
                    </div>
                    <div class="font-bold text-slate-900 underline">( ......................................... )</div>
                    <div class="text-[11px] text-slate-500">Kabag Sirkulasi</div>
                </div>

                <div class="border border-slate-200 rounded-xl p-3 bg-slate-50/50">
                    <div class="text-slate-500">Otorisasi Resmi,</div>
                    <div class="h-12 flex items-center justify-center font-mono text-[11px] font-bold {{ $isApproved ? 'text-kp-blue-700' : ($isRejected ? 'text-rose-700' : 'text-slate-400') }}">
                        {{ $isApproved ? 'DISETUJUI' : ($isRejected ? 'DITOLAK' : 'PENDING') }}
                    </div>
                    <div class="font-bold text-slate-900 underline">{{ $memo->diresponOleh->name ?? 'Divisi Akuntansi' }}</div>
                    <div class="text-[11px] text-slate-500">Verifikator Akuntansi</div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
