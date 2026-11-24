<x-mobile-inkaso-layout>
    <div class="space-y-4">

        <!-- Navigasi Balik -->
        <div class="flex items-center justify-between">
            <a href="{{ route('keuangan.inkaso.mobile') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#0E527B] hover:text-[#0A3D5C] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Kembali ke Tagihan Lapangan</span>
            </a>
            <span class="text-[11px] font-mono text-slate-500 font-bold bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">{{ $setoran->no_bsk }}</span>
        </div>

        <!-- Kartu Tanda Terima Digital BSK (Standar 60-30-10 Kp-Blue) -->
        <div class="bg-white border-2 border-[#0E527B]/30 rounded-3xl p-5 sm:p-6 shadow-sm space-y-5 text-center relative overflow-hidden">
            
            <!-- Watermark / Banner Status -->
            @if($setoran->status_approval === 'approved')
                <div class="absolute -right-12 top-6 bg-[#0E527B] text-white font-black text-[10px] tracking-widest uppercase py-1 px-12 rotate-45 shadow-xs">
                    APPROVED
                </div>
            @elseif($setoran->status_approval === 'rejected')
                <div class="absolute -right-12 top-6 bg-rose-600 text-white font-black text-[10px] tracking-widest uppercase py-1 px-12 rotate-45 shadow-xs">
                    DITOLAK
                </div>
            @else
                <div class="absolute -right-12 top-6 bg-[#FFB900] text-[#0A3D5C] font-black text-[10px] tracking-widest uppercase py-1 px-12 rotate-45 shadow-xs">
                    PENDING
                </div>
            @endif

            <!-- Logo & Identitas Perusahaan -->
            <div class="space-y-1">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-9 w-auto object-contain mx-auto"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                    PT Berkah Pikiran Rakyat &bull; Divisi Keuangan &amp; Inkaso
                </p>
                <h2 class="text-base font-black text-[#0E527B] tracking-tight">
                    BUKTI SETORAN KOLEKSI (BSK)
                </h2>
            </div>

            <!-- AREA UTAMA BARCODE & QR CODE UNTUK KASIR -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-3">
                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider block">
                    Arahkan Barcode Scanner Kasir ke Sini:
                </span>

                <!-- 1. Barcode Code 128 Bersih SVG -->
                <div class="overflow-x-auto py-1">
                    {!! \App\Services\Barcode128Service::getBarcodeSvg($setoran->no_bsk, 60, 2) !!}
                </div>

                <!-- 2. QR Code Cadangan -->
                <div class="pt-2 border-t border-slate-200/60 flex flex-col items-center justify-center">
                    <div class="bg-white p-2 rounded-xl border border-slate-200 shadow-2xs">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(140)->margin(1)->generate($setoran->barcode_token) !!}
                    </div>
                    <span class="text-[10px] font-mono font-bold text-slate-600 mt-1">
                        {{ $setoran->no_bsk }}
                    </span>
                </div>
            </div>

            <!-- Rincian Setoran Finansial -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-left space-y-2 text-xs">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                    <span class="text-slate-500 font-semibold">Nominal Disetor:</span>
                    <span class="text-lg font-black text-[#0E527B]">
                        Rp {{ number_format($setoran->nominal_titipan, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Nama Pelanggan/Agen:</span>
                    <span class="font-bold text-slate-900 text-right">{{ $setoran->nama_pembayar }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-slate-500">No. Faktur Tagihan:</span>
                    <span class="font-mono font-bold text-[#0E527B]">{{ $setoran->faktur->no_faktur ?? '-' }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Keterangan:</span>
                    <span class="font-medium text-slate-800 text-right">{{ $setoran->keterangan_angsuran }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Metode Bayar:</span>
                    <span class="font-bold uppercase text-slate-800">{{ $setoran->metode_bayar }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Petugas Inkaso:</span>
                    <span class="font-semibold text-slate-800">{{ $setoran->petugas->name ?? 'Petugas Inkaso' }}</span>
                </div>

                <div class="flex items-center justify-between pt-1 border-t border-slate-200">
                    <span class="text-slate-500">Waktu Koleksi:</span>
                    <span class="text-slate-700 font-mono">{{ $setoran->waktu_terima_lapangan ? $setoran->waktu_terima_lapangan->format('d/m/Y H:i') : '-' }} WIB</span>
                </div>

                @if($setoran->status_approval === 'approved' && $setoran->no_kuitansi_kasir)
                    <div class="flex items-center justify-between pt-2 border-t border-[#0E527B]/30 text-[#0E527B] font-bold">
                        <span>No. BKM Kasir:</span>
                        <span class="font-mono">{{ $setoran->no_kuitansi_kasir }}</span>
                    </div>
                @endif
            </div>

            <!-- Petunjuk SOP Kasir -->
            @if($setoran->status_approval === 'pending')
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-3 text-left space-y-1">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-amber-900">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Instruksi Serah Terima Kasir:</span>
                    </div>
                    <p class="text-[11px] text-amber-800 leading-relaxed">
                        Tunjukkan layar HP ini kepada Kasir di Loket Redaksi sore hari beserta fisik uang tunai. Setelah Kasir men-scan barcode dan menyetujui, tanda terima ini sah menjadi Kuitansi BKM resmi.
                    </p>
                </div>
            @elseif($setoran->status_approval === 'approved')
                <div class="bg-slate-50 border border-[#0E527B]/30 rounded-2xl p-3 text-left space-y-1">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-[#0E527B]">
                        <svg class="w-4 h-4 text-[#0E527B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Setoran Telah Disetujui:</span>
                    </div>
                    <p class="text-[11px] text-slate-700 leading-relaxed">
                        Uang fisik telah diterima kasir pada {{ $setoran->approved_at ? $setoran->approved_at->format('d/m/Y H:i') : '' }}. Sisa piutang faktur telah dipotong secara real-time.
                    </p>
                </div>
            @else
                <div class="bg-rose-50 border border-rose-200 rounded-2xl p-3 text-left space-y-1">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-rose-900">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Setoran Ditolak Kasir:</span>
                    </div>
                    <p class="text-[11px] text-rose-800 leading-relaxed">
                        Alasan: {{ $setoran->catatan_kasir ?? 'Fisik uang tidak sesuai' }}. Silakan lakukan rekonsiliasi ulang bersama kasir.
                    </p>
                </div>
            @endif

            <!-- Tombol Navigasi Aksi Bawah -->
            <div class="pt-2">
                <a href="{{ route('keuangan.inkaso.mobile') }}" 
                   class="w-full py-3 px-4 rounded-xl bg-[#0E527B] hover:bg-[#0A3D5C] text-white text-xs font-bold flex items-center justify-center gap-2 shadow-sm transition cursor-pointer">
                    <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Tagihan</span>
                </a>
            </div>

        </div>

    </div>
</x-mobile-inkaso-layout>
