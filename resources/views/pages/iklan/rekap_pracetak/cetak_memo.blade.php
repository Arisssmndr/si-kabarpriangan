<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memo Iklan Susulan — {{ $memo->nomor_memo }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .memo-sheet {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 antialiased py-8 px-4">

    <!-- Toolbar Layar (60-30-10) -->
    <div class="max-w-2xl mx-auto mb-5 flex items-center justify-between gap-3 bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-xs no-print">
        <div class="flex items-center gap-2.5 min-w-0">
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">
                {{ $memo->nomor_memo }}
            </span>
            <span class="text-sm font-semibold text-slate-800 truncate">
                Memo Buka Cut-Off
            </span>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('iklan.rekap-pracetak.index', ['tanggal_muat' => $memo->tanggal_target?->toDateString()]) }}"
               class="h-9 px-3.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold inline-flex items-center transition">
                Kembali
            </a>
            <button type="button" onclick="window.print()"
                    class="h-9 px-4 rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold inline-flex items-center shadow-xs transition cursor-pointer">
                Cetak Memo
            </button>
        </div>
    </div>

    <!-- Lembar Dokumen Memo Internal -->
    <div class="memo-sheet max-w-2xl mx-auto bg-white border border-slate-200 rounded-xl shadow-sm p-8 space-y-6">

        <!-- 1. Kop Resmi -->
        <div class="flex items-start justify-between gap-4 pb-4 border-b-2 border-slate-900">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-500">PT. Berkah Pikiran Rakyat</div>
                <h1 class="text-base font-extrabold text-slate-900 tracking-tight uppercase">Harian Umum Kabar Priangan</h1>
            </div>
            <div class="text-right">
                <div class="text-xs font-bold uppercase tracking-wider text-kp-blue-700">Memo Internal</div>
                <div class="font-mono text-xs font-bold text-slate-900 mt-0.5">{{ $memo->nomor_memo }}</div>
            </div>
        </div>

        @php
            $pengajuNama = ($memo->tujuan_divisi && !str_contains($memo->tujuan_divisi, 'Divisi Akuntansi'))
                ? $memo->tujuan_divisi
                : ($memo->dibukaOleh->name ?? 'Front Office');
        @endphp

        <!-- 2. Metadata Memo (Kepada / Dari / Pemasang / Pengaju / Urgensi) -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs space-y-2">
            <div class="flex">
                <span class="w-36 text-slate-500 shrink-0">Kepada</span>
                <span class="font-semibold text-slate-900">: Divisi Akuntansi</span>
            </div>
            <div class="flex">
                <span class="w-36 text-slate-500 shrink-0">Dari</span>
                <span class="font-semibold text-slate-900">: Divisi Iklan</span>
            </div>
            <div class="flex">
                <span class="w-36 text-slate-500 shrink-0">Diajukan Oleh</span>
                <span class="font-semibold text-slate-900">: {{ $pengajuNama }}</span>
            </div>
            <div class="flex">
                <span class="w-36 text-slate-500 shrink-0">Nama Pemasang</span>
                <span class="font-bold text-slate-900">: {{ $memo->nama_pemasang ?: '-' }}</span>
            </div>
            <div class="flex">
                <span class="w-36 text-slate-500 shrink-0">Kategori Urgensi</span>
                <span class="font-semibold text-slate-900">: {{ $memo->kategori_urgensi ?: 'Iklan Susulan' }}</span>
            </div>
            <div class="flex">
                <span class="w-36 text-slate-500 shrink-0">Tanggal Terbit</span>
                <span class="font-semibold text-kp-blue-700">: {{ $memo->tanggal_target ? $memo->tanggal_target->locale('id')->translatedFormat('l, d F Y') : '-' }}</span>
            </div>
            <div class="flex">
                <span class="w-36 text-slate-500 shrink-0">Waktu Pengajuan</span>
                <span class="font-mono font-semibold text-slate-900">: {{ ($memo->dibuka_pada ?? $memo->created_at)->translatedFormat('d F Y, H:i') }} WIB</span>
            </div>
        </div>

        <!-- 3. Alasan Urgensi -->
        <div class="space-y-1.5">
            <div class="text-xs font-semibold text-slate-700">Alasan Urgensi:</div>
            <div class="p-4 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 leading-relaxed min-h-[72px]">
                {{ $memo->alasan }}
            </div>
        </div>

        <!-- 4. Otorisasi -->
        <div class="pt-4 border-t border-slate-200">
            <div class="grid grid-cols-3 gap-4 text-center text-xs">
                <div class="border border-slate-200 rounded-xl p-3 bg-slate-50/50">
                    <div class="text-slate-500">Diajukan Oleh</div>
                    <div class="h-10 flex items-center justify-center font-mono text-[11px] text-slate-400">
                        {{ ($memo->dibuka_pada ?? $memo->created_at)->format('d/m/Y H:i') }}
                    </div>
                    <div class="font-bold text-slate-900">{{ $pengajuNama }}</div>
                    <div class="text-[11px] text-slate-500">Divisi Iklan</div>
                </div>

                <div class="border border-slate-200 rounded-xl p-3 bg-slate-50/50">
                    <div class="text-slate-500">Penyetuju</div>
                    <div class="h-10 flex items-center justify-center font-mono text-[11px] text-kp-blue-700 font-semibold">
                        DISETUJUI
                    </div>
                    <div class="font-bold text-slate-900">{{ $memo->diketahui_oleh ?: 'Shinta Dewi, SH., M.H.' }}</div>
                    <div class="text-[11px] text-slate-500">Asmen Iklan</div>
                </div>

                <div class="border border-slate-200 rounded-xl p-3 bg-slate-50/50">
                    <div class="text-slate-500">Penerima</div>
                    <div class="h-10 flex items-center justify-center font-mono text-[11px] text-slate-500">
                        TERKIRIM
                    </div>
                    <div class="font-bold text-slate-900">Divisi Akuntansi</div>
                    <div class="text-[11px] text-slate-500">Verifikasi Faktur</div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>

