@php
    $layoutTag = ($isFakturView ?? false) ? 'app-layout' : 'mobile-inkaso-layout';
@endphp

<x-dynamic-component :component="$layoutTag">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-shadow { box-shadow: none !important; border: 1px solid #cbd5e1 !important; }
            .rincian-mode-view { display: none !important; }
            .surat-resmi-view { display: block !important; }
        }
    </style>

    <div x-data="{ viewMode: '{{ ($isFakturView ?? false) ? 'surat_resmi' : request('view', 'rincian') }}' }"
         class="{{ ($isFakturView ?? false) ? 'max-w-5xl mx-auto p-4 sm:p-6 space-y-5' : 'space-y-4 pb-16' }}">
        <!-- Bar Navigasi & Cetak Surat Resmi -->
        <div class="flex items-center justify-between gap-2 flex-wrap no-print">
            <a href="{{ $backUrl }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-white border border-slate-200 text-xs font-extrabold text-[#0E527B] hover:bg-slate-50 shadow-2xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>{{ $backLabel }}</span>
            </a>

            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $isSelesai ? 'bg-[#0E527B] text-white' : ($waitingTasks->count() > 0 ? 'bg-amber-100 text-[#07334F] border border-amber-300' : 'bg-[#FFB900] text-[#07334F]') }}">
                    {{ $isSelesai ? 'Selesai' : ($waitingTasks->count() > 0 ? 'Menunggu Persetujuan' : 'Aktif') }}
                </span>

                <button type="button"
                        onclick="window.print()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-[#0E527B] hover:bg-[#07334F] text-white text-xs font-bold shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak Surat</span>
                </button>
            </div>
        </div>

        <!-- Switcher Mode: Rincian Faktur vs Format Surat Resmi (KOP) -->
        <div class="flex items-center gap-1 p-1.5 rounded-full bg-slate-100/90 shadow-2xs border border-slate-200/90 no-print">
            <button type="button"
                    @click="viewMode = 'rincian'"
                    :class="viewMode === 'rincian' ? 'bg-[#0E527B] text-white font-black shadow-xs' : 'text-slate-600 hover:text-[#0E527B] font-bold hover:bg-slate-200/60'"
                    class="flex-1 px-3 py-2 rounded-full text-xs transition cursor-pointer flex items-center justify-center gap-1.5 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                <span>Rincian Faktur ({{ $tasks->count() }})</span>
            </button>
            <button type="button"
                    @click="viewMode = 'surat_resmi'"
                    :class="viewMode === 'surat_resmi' ? 'bg-[#0E527B] text-white font-black shadow-xs' : 'text-slate-600 hover:text-[#0E527B] font-bold hover:bg-slate-200/60'"
                    class="flex-1 px-3 py-2 rounded-full text-xs transition cursor-pointer flex items-center justify-center gap-1.5 whitespace-nowrap">
                <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Format Surat Resmi (KOP)</span>
            </button>
        </div>

        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="p-3.5 rounded-2xl bg-sky-50 border border-sky-200 text-[#0E527B] text-xs font-bold flex items-center gap-2 shadow-xs no-print">
                <svg class="w-4 h-4 text-[#0E527B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-bold flex items-center gap-2 shadow-xs no-print">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-300 text-[#07334F] text-xs font-bold flex items-center gap-2 shadow-xs no-print">
                <svg class="w-4 h-4 text-[#FFB900] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- VIEW 1: RINCIAN FAKTUR (MENAMPILKAN SEMUA FAKTUR: MANA YANG SUDAH BAYAR & MANA YANG BELUM) -->
        <div x-show="viewMode === 'rincian'" x-cloak class="space-y-3.5 no-print rincian-mode-view">
            @php
                $detailTotalTagihan = $tasks->sum(fn ($t) => (float) ($t->faktur->total_tagihan ?? 0));
                $detailTotalTerbayar = $tasks->sum(function($t) use ($setoranPending) {
                    $pBsk = isset($setoranPending) ? $setoranPending->get($t->faktur_id) : $t->setoranLapangan->where('status_approval', 'pending')->first();
                    $sah = (float) ($t->faktur->total_terbayar ?? 0);
                    $nom = $pBsk ? (float) $pBsk->nominal_titipan : $sah;
                    return max($sah, $nom, (float) $t->nominal_diterima);
                });
                $detailTotalSisa = max(0, $detailTotalTagihan - $detailTotalTerbayar);
                $sudahBayarCount = $tasks->filter(function($t) use ($setoranPending) {
                    $pBsk = isset($setoranPending) ? $setoranPending->get($t->faktur_id) : $t->setoranLapangan->where('status_approval', 'pending')->first();
                    $fSisa = (float) ($t->faktur->sisa_piutang ?? 0);
                    $fBayar = (float) ($t->faktur->total_terbayar ?? 0);
                    return $fSisa <= 0 || $pBsk || $fBayar > 0;
                })->count();
                $belumBayarCount = max(0, $tasks->count() - $sudahBayarCount);
            @endphp

            <!-- Hero Banner Ringkasan Surat Tugas -->
            <div class="rounded-2xl p-5 text-white shadow-md relative overflow-hidden"
                 style="background: linear-gradient(135deg, #0E527B 0%, #0A72AC 100%) !important;">
                <div class="flex items-start justify-between gap-3 relative z-10">
                    <div>
                        <span class="text-[10px] font-bold text-white/80 uppercase tracking-wider block">Rincian Surat Penugasan</span>
                        <div class="text-lg sm:text-xl font-black text-white font-mono mt-0.5">
                            {{ $noSuratLabel }}
                        </div>
                        <div class="text-[11px] text-white/85 mt-1 flex items-center gap-2 flex-wrap">
                            <span>Petugas: <strong class="text-white">{{ $namaPetugas }}</strong></span>
                            <span>&bull;</span>
                            <span>{{ $tasks->count() }} Faktur</span>
                        </div>
                    </div>

                    <button type="button"
                            @click="viewMode = 'surat_resmi'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/20 hover:bg-white/30 text-white text-[11px] font-bold border border-white/30 backdrop-blur-md transition cursor-pointer shrink-0">
                        <svg class="w-3.5 h-3.5 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Surat Resmi</span>
                    </button>
                </div>

                <!-- 3 Kolom Ringkasan Finansial -->
                <div class="mt-4 pt-3 border-t border-white/20 grid grid-cols-3 gap-2 text-center relative z-10">
                    <div>
                        <span class="text-[9.5px] font-medium text-white/75 uppercase tracking-wider block">Total Tagihan</span>
                        <span class="text-xs sm:text-sm font-black text-white font-mono block mt-0.5">Rp {{ number_format($detailTotalTagihan, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-[9.5px] font-medium text-white/75 uppercase tracking-wider block">Terkumpul</span>
                        <span class="text-xs sm:text-sm font-black text-[#FFB900] font-mono block mt-0.5">Rp {{ number_format($detailTotalTerbayar, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-[9.5px] font-medium text-white/75 uppercase tracking-wider block">Sisa Tagihan</span>
                        <span class="text-xs sm:text-sm font-black text-white font-mono block mt-0.5">Rp {{ number_format($detailTotalSisa, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Header List & Status Count -->
            <div class="flex items-center justify-between px-1 pt-1">
                <span class="text-[11px] font-extrabold text-[#0E527B] uppercase tracking-wider">
                    Daftar Faktur ({{ $tasks->count() }})
                </span>
                <div class="flex items-center gap-1.5 text-[10px] font-bold">
                    <span class="px-2 py-0.5 rounded-full bg-sky-50 text-[#0E527B] border border-sky-200">
                        {{ $sudahBayarCount }} Sudah Bayar
                    </span>
                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                        {{ $belumBayarCount }} Belum Bayar
                    </span>
                </div>
            </div>

            <!-- Loop Kartu Faktur Sesuai Surat Penugasan Ini -->
            <div class="space-y-3">
                @foreach($tasks as $tugas)
                    @php
                        $fTotal = (float) ($tugas->faktur->total_tagihan ?? 0);
                        $fBayarSah = (float) ($tugas->faktur->total_terbayar ?? 0);
                        $pendingBsk = isset($setoranPending) ? $setoranPending->get($tugas->faktur_id) : $tugas->setoranLapangan->where('status_approval', 'pending')->first();
                        $latestBsk = $pendingBsk ?: $tugas->setoranLapangan->sortByDesc('id')->first();
                        $nominalBayar = $pendingBsk ? (float) $pendingBsk->nominal_titipan : $fBayarSah;
                        $fSisaRiil = max(0, $fTotal - $nominalBayar);
                        $isLunas = $fSisaRiil <= 0 && $fTotal > 0;
                    @endphp
                    <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/90 hover:border-slate-300 transition-all space-y-3">
                        <!-- Baris 1: Header No Faktur & Status Badge -->
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-1.5 min-w-0">
                                <span class="px-2.5 py-1 rounded-lg bg-sky-50 text-[#0E527B] font-mono text-[10px] font-extrabold border border-sky-100">
                                    {{ $tugas->faktur->no_faktur ?? '-' }}
                                </span>
                                @if($tugas->no_tugas)
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-mono text-[9.5px] font-semibold">
                                        #{{ $tugas->no_tugas }}
                                    </span>
                                @endif
                            </div>
                            <div class="shrink-0">
                                @if($isLunas)
                                    <span class="px-2.5 py-1 rounded-full text-[9.5px] font-black bg-[#0E527B] text-white">Lunas</span>
                                @elseif($pendingBsk)
                                    <span class="px-2.5 py-1 rounded-full text-[9.5px] font-black bg-[#FFB900] text-[#07334F]">Menunggu Kasir</span>
                                @elseif($fBayarSah > 0)
                                    <span class="px-2.5 py-1 rounded-full text-[9.5px] font-black bg-sky-100 text-[#0E527B]">Cicilan</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[9.5px] font-bold bg-slate-100 text-slate-600 border border-slate-200">Belum Bayar</span>
                                @endif
                            </div>
                        </div>

                        <!-- Baris 2: Nama Pelanggan & Tombol Bukti -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 leading-snug">
                                    {{ $tugas->faktur->customer->nama_customer ?? '-' }}
                                </h4>
                                <p class="text-[10.5px] text-slate-500 mt-0.5 truncate">
                                    {{ $tugas->faktur->customer->alamat ?? ($tugas->faktur->customer->kota ?? 'Tasikmalaya') }}
                                    @if(!empty($tugas->faktur->customer->no_telepon))
                                        &bull; {{ $tugas->faktur->customer->no_telepon }}
                                    @endif
                                </p>
                            </div>
                            @if($latestBsk)
                                <a href="{{ route('keuangan.inkaso.bsk.show', $latestBsk->no_bsk) }}"
                                   class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-[#0E527B] text-[#0E527B] hover:text-white text-[10.5px] font-bold border border-slate-200 hover:border-[#0E527B] transition-all shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>Lihat Bukti</span>
                                </a>
                            @endif
                        </div>

                        <!-- Baris 3: Ringkasan Finansial Menggunakan Kata yang Umum & Rapi -->
                        <div class="bg-slate-50/90 rounded-xl p-3 border border-slate-100 flex items-center justify-between text-xs">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Tagihan</span>
                                <span class="font-extrabold text-xs text-slate-700 font-mono block mt-0.5">Rp {{ number_format($fTotal, 0, ',', '.') }}</span>
                                @if($nominalBayar > 0 && $fSisaRiil > 0)
                                    <span class="text-[10.5px] text-amber-800 font-bold block mt-1">
                                        Sisa Tagihan: <span class="font-mono">Rp {{ number_format($fSisaRiil, 0, ',', '.') }}</span>
                                    </span>
                                @endif
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-bold text-[#0E527B] uppercase tracking-wider block">
                                    {{ $isLunas ? 'Lunas Terbayar' : ($nominalBayar > 0 ? 'Jumlah Bayar' : 'Status') }}
                                </span>
                                <span class="font-black text-xs sm:text-sm text-[#0E527B] font-mono block mt-0.5">
                                    {{ $nominalBayar > 0 ? 'Rp ' . number_format($nominalBayar, 0, ',', '.') : 'Belum Ada Pembayaran' }}
                                </span>
                            </div>
                        </div>

                        <!-- Baris 4: Tombol Aksi Lapangan jika Surat Masih Aktif -->
                        @if(!$isSelesai && !$isLunas)
                            <div class="flex items-center gap-2 pt-1 border-t border-slate-100">
                                @if(!empty($tugas->faktur->customer->no_telepon))
                                    <a href="tel:{{ $tugas->faktur->customer->no_telepon }}"
                                       class="h-9 px-3 rounded-xl border border-slate-200 text-[#0E527B] hover:bg-slate-50 text-xs font-bold flex items-center justify-center gap-1 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span>Hubungi</span>
                                    </a>
                                @endif
                                <a href="{{ route('keuangan.inkaso.mobile', ['tab' => 'penagihan', 'sub' => 'semua_faktur', 'q' => $tugas->faktur->no_faktur]) }}"
                                   class="flex-1 h-9 px-3 rounded-xl bg-[#0E527B] hover:bg-[#07334F] text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition">
                                    <svg class="w-3.5 h-3.5 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span>{{ $pendingBsk ? 'Kelola Pembayaran' : 'Input Pembayaran' }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Tombol Cepat Beralih ke Format Surat Resmi -->
            <div class="pt-2">
                <button type="button"
                        @click="viewMode = 'surat_resmi'"
                        class="w-full h-11 rounded-2xl bg-white hover:bg-slate-50 border-2 border-[#0E527B] text-[#0E527B] text-xs font-black flex items-center justify-center gap-2 shadow-2xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Lihat Format Surat Penugasan Resmi (KOP &amp; TTD)</span>
                </button>
            </div>
        </div>

        <!-- VIEW 2: LEMBAR SURAT PENUGASAN RESMI (KOP, LOGO, NOMOR SURAT, TABEL FAKTUR, TTD) -->
        <div x-show="viewMode === 'surat_resmi'" x-cloak class="space-y-4 surat-resmi-view">
            @include('pages.keuangan.inkaso.partials.surat-resmi-paper')
        </div>

        <!-- PANEL AKSI PETUGAS INKASO (HANYA MUNCUL UNTUK PETUGAS LAPANGAN) -->
        @if(!($isFakturView ?? false))
            <div class="space-y-3 pt-1 no-print">
                @if($waitingTasks->count() > 0)
                    <!-- Aksi Persetujuan Surat (Approve -> Otomatis pindah ke Halaman Penagihan) -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs space-y-3">
                        <div>
                            <h3 class="text-xs font-extrabold text-[#0E527B] uppercase tracking-wider">Konfirmasi Penerimaan Surat</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Setujui surat penugasan untuk memindahkan faktur ke halaman Penagihan, atau tolak faktur spesifik bila tidak sesuai rute.
                            </p>
                        </div>

                        @if($isGrupSurat && $waitingTasks->count() > 1)
                            <form action="{{ route('keuangan.inkaso.penugasan.grup.konfirmasi', $noSuratLabel) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full h-11 rounded-2xl bg-[#0E527B] hover:bg-[#07334F] active:scale-98 text-white text-xs font-black flex items-center justify-center gap-2 shadow-sm transition cursor-pointer">
                                    <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <span>Terima Semua Surat Ini &amp; Masuk Penagihan</span>
                                </button>
                            </form>
                        @elseif($waitingTasks->count() === 1)
                            @php $singleWait = $waitingTasks->first(); @endphp
                            <form action="{{ route('keuangan.inkaso.penugasan.konfirmasi', $singleWait->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="aksi" value="terima">
                                <button type="submit"
                                        class="w-full h-11 rounded-2xl bg-[#0E527B] hover:bg-[#07334F] active:scale-98 text-white text-xs font-black flex items-center justify-center gap-2 shadow-sm transition cursor-pointer">
                                    <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <span>Setujui Surat &amp; Masuk Halaman Penagihan</span>
                                </button>
                            </form>
                        @endif

                        <!-- Daftar Faktur Menunggu (Jika Ingin Tolak Faktur Spesifik) -->
                        <div class="pt-2 border-t border-slate-100 space-y-2">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">
                                Tolak Faktur Spesifik (Opsional)
                            </span>
                            @foreach($waitingTasks as $wt)
                                <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                                    <div class="min-w-0">
                                        <span class="font-mono font-bold text-[#0E527B] block">{{ $wt->faktur->no_faktur ?? '-' }}</span>
                                        <span class="text-[11px] text-slate-700 truncate block">{{ $wt->faktur->customer->nama_customer ?? '-' }}</span>
                                    </div>
                                    <button type="button"
                                            onclick="bukaModalTolakDetail('{{ $wt->id }}', '{{ $wt->faktur->no_faktur ?? '-' }}', '{{ addslashes($wt->faktur->customer->nama_customer ?? '-') }}')"
                                            class="px-3 py-1.5 rounded-lg border border-amber-300 bg-amber-50 hover:bg-amber-100 text-[#07334F] text-[11px] font-bold shrink-0 cursor-pointer transition">
                                        Tolak Faktur Ini
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!$isSelesai && $activeTasks->count() > 0)
                    <a href="{{ route('keuangan.inkaso.mobile', ['tab' => 'penagihan', 'sub' => 'semua_faktur']) }}"
                       class="w-full h-11 rounded-2xl bg-[#0E527B] hover:bg-[#07334F] text-white text-xs font-extrabold flex items-center justify-center gap-2 shadow-sm transition">
                        <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>Kelola Penagihan Faktur</span>
                    </a>

                    @if($isGrupSurat)
                        @php
                            $belumBayarCountDetail = $tasks->filter(function($t) {
                                return !($t->faktur && $t->faktur->sisa_piutang <= 0);
                            })->count();
                        @endphp
                        <form action="{{ route('keuangan.inkaso.penugasan.grup.selesai', $noSuratLabel) }}" method="POST"
                              onsubmit="return konfirmasiSelesaiPenugasan(this, '{{ $noSuratLabel }}', {{ $tasks->count() }}, {{ $belumBayarCountDetail }})">
                            @csrf
                            <button type="submit"
                                    class="w-full h-11 rounded-2xl bg-[#FFB900] hover:bg-amber-400 active:scale-98 text-[#07334F] text-xs font-black flex items-center justify-center gap-2 shadow-sm transition cursor-pointer">
                                <svg class="w-4 h-4 text-[#0E527B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Selesai Penagihan Surat Ini</span>
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        @endif

        <a href="{{ $backUrl }}"
           class="w-full h-11 rounded-2xl border border-slate-300 bg-white hover:bg-slate-50 text-[#0E527B] text-xs font-extrabold flex items-center justify-center gap-1.5 shadow-2xs transition no-print">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            <span>{{ $backLabel }}</span>
        </a>
    </div>

    <!-- Modal Tolak Faktur Spesifik -->
    <div id="modalTolakDetail" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-[2px] p-4 hidden no-print">
        <div class="bg-white rounded-[1.75rem] max-w-md w-full p-5 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-extrabold text-[#0E527B] uppercase tracking-tight">Tolak Faktur Ini</h3>
                <button type="button" onclick="document.getElementById('modalTolakDetail').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="formTolakDetail" method="POST" class="space-y-3.5">
                @csrf
                <input type="hidden" name="aksi" value="tolak">
                <p class="text-xs text-slate-600">Tolak faktur <strong id="dtlTolakFakturNo" class="font-mono">-</strong> (<strong id="dtlTolakCustomer">-</strong>).</p>
                <textarea name="alasan_penolakan" required rows="3" placeholder="Tuliskan alasan penolakan faktur ini..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800"></textarea>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-[#FFB900] hover:bg-amber-400 text-[#07334F] text-xs font-black cursor-pointer">Konfirmasi Tolak Faktur</button>
            </form>
        </div>
    </div>

    <script>
        function bukaModalTolakDetail(tugasId, fakturNo, customer) {
            document.getElementById('dtlTolakFakturNo').innerText = fakturNo;
            document.getElementById('dtlTolakCustomer').innerText = customer;
            document.getElementById('formTolakDetail').action = "{{ url('keuangan/inkaso/penugasan') }}/" + tugasId + "/konfirmasi";
            document.getElementById('modalTolakDetail').classList.remove('hidden');
        }

        function konfirmasiSelesaiPenugasan(formEl, noSurat, totalFaktur, belumBayar) {
            let pesan = 'Apakah Anda yakin ingin menyelesaikan penagihan surat ini?';
            if (belumBayar > 0) {
                pesan = 'Ada ' + belumBayar + ' faktur yang belum terbayar. Tetap selesaikan penagihan surat ini?';
            }

            if (window.Swal) {
                Swal.fire({
                    title: 'Selesaikan Surat Penugasan?',
                    text: pesan,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0E527B',
                    cancelButtonColor: '#94A3B8',
                    confirmButtonText: 'Ya, Selesaikan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-3xl',
                        confirmButton: 'rounded-xl px-5 py-2.5 font-bold text-xs',
                        cancelButton: 'rounded-xl px-5 py-2.5 font-bold text-xs'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        formEl.submit();
                    }
                });
                return false;
            }
            return confirm(pesan);
        }
    </script>

    @if(!($isFakturView ?? false))
        <!-- FLOATING BOTTOM DOCK NAVIGATION (MOBILE COLLECTOR PERSIS PORTAL INKASO) -->
        <nav class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[60] inline-flex items-center gap-2 sm:gap-2.5 p-2 px-2.5 rounded-full bg-[#0E527B] shadow-[0_16px_40px_rgba(14,82,123,0.50)] border border-white/20 select-none backdrop-blur-xl transition-all duration-300 no-print">
            {{-- 1. Beranda --}}
            <a href="{{ route('keuangan.inkaso.mobile', ['tab' => 'beranda']) }}" 
               class="w-11 h-11 rounded-full bg-white/15 hover:bg-white/25 active:bg-white/30 flex items-center justify-center text-white cursor-pointer transition select-none shrink-0"
               title="Beranda">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
            </a>

            {{-- 2. Penagihan --}}
            <a href="{{ route('keuangan.inkaso.mobile', ['tab' => 'penagihan', 'sub' => 'surat_penugasan']) }}" 
               class="bg-white text-[#0E527B] px-5 py-2.5 rounded-full font-black text-sm shadow-md flex items-center gap-2.5 cursor-pointer transition select-none shrink-0">
                <svg class="w-5 h-5 text-[#0E527B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span class="whitespace-nowrap font-extrabold text-xs sm:text-sm tracking-tight">Penagihan</span>
            </a>

            {{-- 3. Riwayat --}}
            <a href="{{ route('keuangan.inkaso.mobile', ['tab' => 'riwayat']) }}" 
               class="w-11 h-11 rounded-full bg-white/15 hover:bg-white/25 active:bg-white/30 flex items-center justify-center text-white cursor-pointer transition select-none shrink-0"
               title="Riwayat">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </a>

            {{-- 4. Profil --}}
            <a href="{{ route('keuangan.inkaso.mobile', ['tab' => 'profil']) }}" 
               class="w-11 h-11 rounded-full bg-white/15 hover:bg-white/25 active:bg-white/30 flex items-center justify-center text-white cursor-pointer transition select-none shrink-0"
               title="Profil">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </a>
        </nav>
    @endif
</x-dynamic-component>
