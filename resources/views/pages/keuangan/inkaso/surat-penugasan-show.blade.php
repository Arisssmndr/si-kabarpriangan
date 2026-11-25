<x-mobile-inkaso-layout>
    @php
        $totalTagihanSurat = $tasks->sum(fn($t) => (float) ($t->faktur->total_tagihan ?? 0));
        $totalTerbayarSurat = $tasks->sum(fn($t) => max((float) ($t->faktur->total_terbayar ?? 0), (float) $t->nominal_diterima));
        $totalSisaSurat = $tasks->sum(fn($t) => (float) ($t->faktur->sisa_piutang ?? 0));
        $sudahInputCount = $tasks->filter(fn($t) => isset($setoranPending[$t->faktur_id]))->count();
        $lunasCount = $tasks->filter(fn($t) => ($t->faktur->sisa_piutang ?? 0) <= 0)->count();
        $belumBayarCount = max(0, $tasks->count() - $sudahInputCount - $lunasCount);
        $firstDate = $tasks->first()->tanggal_tugas;
    @endphp

    <div class="space-y-4 pb-12">
        <!-- Bar Navigasi Kembali (Strict 60-30-10) -->
        <div class="flex items-center justify-between gap-2">
            <a href="{{ $backUrl }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-white border border-slate-200 text-xs font-extrabold text-[#0E527B] hover:bg-slate-50 shadow-2xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>{{ $backLabel }}</span>
            </a>

            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $isSelesai ? 'bg-[#0E527B] text-white' : 'bg-[#FFB900] text-[#07334F]' }}">
                {{ $isSelesai ? 'Selesai' : 'Aktif' }}
            </span>
        </div>

        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="p-3.5 rounded-2xl bg-sky-50 border border-sky-200 text-[#0E527B] text-xs font-bold flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-[#0E527B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-bold flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-300 text-[#07334F] text-xs font-bold flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-[#FFB900] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Kartu Ringkasan Header Surat Penugasan (Biru KP 30% + Amber 10%) -->
        <div class="rounded-[1.75rem] p-5 text-white shadow-md border border-white/15 space-y-3"
             style="background: linear-gradient(135deg, #0E527B 0%, #07334F 100%) !important;">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="text-[9px] font-bold text-amber-300 uppercase tracking-widest block">Rincian Surat Penugasan</span>
                    <h1 class="text-base font-black font-mono text-white tracking-tight mt-0.5">{{ $noSuratLabel }}</h1>
                    <span class="text-[11px] text-white/75 block mt-0.5">
                        Tanggal Tugas: {{ $firstDate ? $firstDate->format('d/m/Y') : '-' }}
                    </span>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-[9px] font-bold text-white/70 uppercase block">Total Sisa Tagihan</span>
                    <span class="text-lg font-black text-white font-mono">Rp {{ number_format($totalSisaSurat, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="flex items-center gap-1.5 flex-wrap pt-2 border-t border-white/15">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-white text-[#0E527B]">
                    {{ $tasks->count() }} Faktur
                </span>
                @if($sudahInputCount > 0)
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-[#FFB900] text-[#07334F]">
                        {{ $sudahInputCount }} Pending QR
                    </span>
                @endif
                @if($lunasCount > 0)
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 text-white border border-white/25">
                        {{ $lunasCount }} Lunas
                    </span>
                @endif
                @if($belumBayarCount > 0)
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/15 text-white border border-white/20">
                        {{ $belumBayarCount }} Belum Bayar
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-white/15 text-[11px]">
                <div>
                    <span class="text-white/70 block">Total Piutang Surat</span>
                    <span class="font-mono font-bold text-white">Rp {{ number_format($totalTagihanSurat, 0, ',', '.') }}</span>
                </div>
                <div class="text-right">
                    <span class="text-white/70 block">Terinput / Terkumpul</span>
                    <span class="font-mono font-black text-amber-300">Rp {{ number_format($totalTerbayarSurat, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($waitingTasks->count() > 1 && $isGrupSurat)
                <form action="{{ route('keuangan.inkaso.penugasan.grup.konfirmasi', $noSuratLabel) }}" method="POST" class="pt-1">
                    @csrf
                    <button type="submit"
                            class="w-full h-10 rounded-xl bg-[#FFB900] hover:bg-amber-400 active:scale-98 text-[#07334F] text-xs font-black flex items-center justify-center gap-1.5 shadow-sm transition cursor-pointer">
                        <svg class="w-4 h-4 text-[#0E527B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Terima Semua Surat Ini</span>
                    </button>
                </form>
            @endif
        </div>

        <!-- Daftar Kartu Faktur Terpisah di dalam Surat Ini (Dengan Pita Diagonal Pojok) -->
        <div class="space-y-3">
            @foreach($tasks as $tugas)
                @php
                    $pendingBsk = $setoranPending->get($tugas->faktur_id);
                    $latestBsk = $pendingBsk ?: $tugas->setoranLapangan->sortByDesc('id')->first();
                    $sisaFaktur = (float) ($tugas->faktur->sisa_piutang ?? 0);
                    $isMenunggu = $tugas->status_konfirmasi === 'menunggu' && $tugas->status_tugas !== 'selesai';
                    $isBatal = $tugas->status_tugas === 'batal' || $tugas->status_konfirmasi === 'ditolak';
                    $isLunas = $sisaFaktur <= 0;
                    $isCicilan = !$pendingBsk && !$isLunas && ($tugas->faktur->total_terbayar ?? 0) > 0;
                    $suratLocked = $tugas->status_tugas === 'selesai';
                @endphp

                <div class="relative overflow-hidden bg-white rounded-[1.75rem] p-4 shadow-sm border {{ $pendingBsk ? 'border-[#FFB900]/70' : 'border-slate-200' }} space-y-3">
                    <!-- Pita Pojok Diagonal -->
                    @if($isLunas)
                        <div class="absolute bg-[#0E527B] text-white font-black text-[9px] tracking-widest uppercase py-1 text-center rotate-45 shadow-2xs pointer-events-none"
                             style="top: 14px; right: -34px; width: 132px;">
                            LUNAS
                        </div>
                    @elseif($pendingBsk)
                        <div class="absolute bg-[#FFB900] text-[#07334F] font-black text-[9px] tracking-widest uppercase py-1 text-center rotate-45 shadow-2xs pointer-events-none"
                             style="top: 14px; right: -34px; width: 132px;">
                            PENDING
                        </div>
                    @elseif($isBatal)
                        <div class="absolute bg-slate-300 text-slate-800 font-black text-[9px] tracking-widest uppercase py-1 text-center rotate-45 shadow-2xs pointer-events-none"
                             style="top: 14px; right: -34px; width: 132px;">
                            DITOLAK
                        </div>
                    @elseif($isCicilan)
                        <div class="absolute bg-[#0E527B] text-white font-black text-[9px] tracking-widest uppercase py-1 text-center rotate-45 shadow-2xs pointer-events-none"
                             style="top: 14px; right: -34px; width: 132px;">
                            CICILAN
                        </div>
                    @else
                        <div class="absolute bg-slate-200 text-[#0E527B] font-black text-[9px] tracking-widest uppercase py-1 text-center rotate-45 shadow-2xs pointer-events-none"
                             style="top: 14px; right: -34px; width: 132px;">
                            BELUM
                        </div>
                    @endif

                    <div class="pr-14 space-y-1">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-[#0E527B] text-[10px] font-mono font-bold border border-slate-200">
                                {{ $tugas->faktur->no_faktur ?? '-' }}
                            </span>
                            @if($isLunas)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#0E527B] text-white">Lunas</span>
                            @elseif($pendingBsk)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-100 text-[#07334F] border border-amber-300">
                                    Sudah Input &bull; QR Siap
                                </span>
                            @elseif($isMenunggu)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-100 text-amber-800">Menunggu Terima</span>
                            @elseif($isBatal)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-200 text-slate-700">Ditolak</span>
                            @elseif($isCicilan)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-sky-50 text-[#0E527B] border border-sky-200">Cicilan</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 text-slate-600">Belum Bayar</span>
                            @endif
                        </div>

                        <h4 class="text-sm font-extrabold text-slate-900 leading-snug pt-0.5">
                            {{ $tugas->faktur->customer->nama_customer ?? 'Pelanggan / Agen' }}
                        </h4>
                        <p class="text-[11px] text-slate-500 truncate">
                            {{ $tugas->faktur->customer->kota ?? ($tugas->faktur->customer->alamat ?? 'Tasikmalaya') }}
                        </p>
                    </div>

                    <!-- Rincian Nominal -->
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3 flex items-center justify-between gap-2 text-xs">
                        @if($pendingBsk)
                            <div class="min-w-0">
                                <span class="text-[10px] font-bold text-slate-500 uppercase block">Sudah Diinput ({{ strtoupper($pendingBsk->metode_bayar) }})</span>
                                <span class="text-[11px] font-mono font-bold text-[#0E527B]">{{ $pendingBsk->no_bsk }}</span>
                            </div>
                            <span class="text-base font-black text-[#0E527B] font-mono shrink-0">
                                Rp {{ number_format($pendingBsk->nominal_titipan, 0, ',', '.') }}
                            </span>
                        @elseif($isLunas)
                            <div class="min-w-0">
                                <span class="text-[10px] font-bold text-slate-500 uppercase block">Total Dibayar</span>
                                @if($latestBsk)
                                    <span class="text-[11px] font-mono font-bold text-[#0E527B]">{{ $latestBsk->no_kuitansi_kasir ?? $latestBsk->no_bsk }}</span>
                                @endif
                            </div>
                            <span class="text-base font-black text-[#0E527B] font-mono shrink-0">
                                Rp {{ number_format($tugas->faktur->total_terbayar ?? 0, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="text-[11px] font-bold text-slate-500 uppercase">Sisa Piutang</span>
                            <span class="text-base font-black text-[#0E527B] font-mono">
                                Rp {{ number_format($sisaFaktur, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>

                    <!-- Aksi per Faktur -->
                    @if($isMenunggu)
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button"
                                    onclick="bukaModalTolakDetail('{{ $tugas->id }}', '{{ $tugas->faktur->no_faktur }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '-') }}')"
                                    class="h-10 px-3 rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100 text-[#07334F] text-xs font-bold flex items-center justify-center gap-1 transition cursor-pointer">
                                <span>Tolak Faktur Ini</span>
                            </button>
                            <form action="{{ route('keuangan.inkaso.penugasan.konfirmasi', $tugas->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="aksi" value="terima">
                                <button type="submit"
                                        class="w-full h-10 px-3 rounded-xl bg-[#0E527B] hover:bg-[#07334F] text-white text-xs font-bold flex items-center justify-center gap-1 shadow-xs transition cursor-pointer">
                                    <span>Terima Tugas</span>
                                </button>
                            </form>
                        </div>
                    @elseif($pendingBsk)
                        <div class="flex items-center gap-2">
                            <a href="{{ route('keuangan.inkaso.bsk.show', $pendingBsk->no_bsk) }}"
                               class="flex-1 h-10 px-3 rounded-xl bg-[#0E527B] hover:bg-[#07334F] text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition cursor-pointer">
                                <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                <span>Lihat QR</span>
                            </a>
                            @if(!$suratLocked)
                                <button type="button"
                                        onclick="bukaModalEditDetail('{{ $pendingBsk->id }}', '{{ $pendingBsk->no_bsk }}', '{{ $tugas->faktur->no_faktur }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '-') }}', '{{ $sisaFaktur }}', '{{ (float) $pendingBsk->nominal_titipan }}', '{{ $pendingBsk->metode_bayar }}', '{{ addslashes($pendingBsk->keterangan_angsuran ?? '') }}')"
                                        class="h-10 px-4 rounded-xl bg-[#FFB900] hover:bg-amber-400 text-[#07334F] text-xs font-black flex items-center justify-center gap-1 shadow-xs transition cursor-pointer shrink-0">
                                    <span>Ubah</span>
                                </button>
                            @endif
                        </div>
                    @elseif(!$suratLocked && !$isBatal && !$isLunas)
                        <div class="flex items-center gap-2">
                            @if(!empty($tugas->faktur->customer->no_telepon))
                                <a href="tel:{{ $tugas->faktur->customer->no_telepon }}"
                                   class="h-10 px-3.5 rounded-xl border border-slate-200 text-[#0E527B] hover:bg-slate-50 text-xs font-bold flex items-center justify-center gap-1.5 shrink-0 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span>Hubungi</span>
                                </a>
                            @else
                                <button type="button"
                                        onclick="hubungiKosongDetail('{{ addslashes($tugas->faktur->customer->nama_customer ?? 'Pelanggan') }}')"
                                        class="h-10 px-3.5 rounded-xl border border-slate-200 text-[#0E527B] hover:bg-slate-50 text-xs font-bold flex items-center justify-center gap-1.5 shrink-0 transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span>Hubungi</span>
                                </button>
                            @endif
                            <button type="button"
                                    onclick="bukaModalBayarDetail('{{ $tugas->faktur_id }}', '{{ $tugas->id }}', '{{ $tugas->faktur->no_faktur }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '-') }}', '{{ $sisaFaktur }}')"
                                    class="flex-1 h-10 px-4 rounded-xl bg-[#0E527B] hover:bg-[#07334F] text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition cursor-pointer">
                                <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Input Pembayaran</span>
                            </button>
                        </div>
                    @elseif($latestBsk)
                        <a href="{{ route('keuangan.inkaso.bsk.show', $latestBsk->no_bsk) }}"
                           class="w-full h-10 rounded-xl border border-slate-200 hover:bg-slate-50 text-[#0E527B] text-xs font-bold flex items-center justify-center transition">
                            <span>Lihat Bukti BSK / BKM</span>
                        </a>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Tombol Selesai Penagihan Surat Ini (jika belum selesai) + Tombol Kembali -->
        <div class="space-y-2.5 pt-2">
            @if(!$isSelesai && $isGrupSurat && $activeTasks->count() > 0)
                <form action="{{ route('keuangan.inkaso.penugasan.grup.selesai', $noSuratLabel) }}" method="POST"
                      onsubmit="return confirm('Simpan & selesaikan surat penugasan {{ $noSuratLabel }} ({{ $tasks->count() }} faktur)?\n\nSemua faktur surat ini akan dipindahkan ke Riwayat dan nominal pembayaran terkunci.')">
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

            <a href="{{ $backUrl }}"
               class="w-full h-11 rounded-2xl border border-slate-300 bg-white hover:bg-slate-50 text-[#0E527B] text-xs font-extrabold flex items-center justify-center gap-1.5 shadow-2xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>{{ $backLabel }}</span>
            </a>
        </div>
    </div>

    <!-- Modal Input Bayar -->
    <div id="modalBayarDetail" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-[2px] p-4 hidden">
        <div class="bg-white rounded-[1.75rem] max-w-md w-full p-5 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-extrabold text-[#0E527B] tracking-tight">Input Pembayaran</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Buat bukti setoran pelanggan (BSK)</p>
                </div>
                <button type="button" onclick="document.getElementById('modalBayarDetail').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center cursor-pointer transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('keuangan.inkaso.setoran-lapangan.store') }}" method="POST"
                  onsubmit="return validateDotNominal('dtlInputNominalDisplay', 'dtlInputNominal')" class="space-y-4">
                @csrf
                <input type="hidden" name="faktur_id" id="dtlBayarFakturId">
                <input type="hidden" name="penugasan_id" id="dtlBayarPenugasanId">
                <input type="hidden" name="nominal_bayar" id="dtlInputNominal">

                <div class="bg-slate-50/90 border border-slate-200/80 rounded-2xl p-3.5 space-y-2 text-xs">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-slate-500 shrink-0">No. Faktur</span>
                        <span class="font-mono font-bold text-[#0E527B] text-right" id="dtlBayarFakturNo">-</span>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <span class="text-slate-500 shrink-0">Pelanggan</span>
                        <span class="font-bold text-slate-800 text-right leading-snug" id="dtlBayarCustomer">-</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 pt-2 border-t border-slate-200/80">
                        <span class="font-bold text-slate-700">Total Piutang</span>
                        <span class="font-black text-sm text-[#0E527B] font-mono" id="dtlBayarSisaText">Rp 0</span>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-bold text-slate-700">Jumlah Bayar (Rp) <span class="text-[#FFB900]">*</span></label>
                        <button type="button" onclick="isiLunasDetail()" class="text-[11px] font-bold text-[#0E527B] hover:text-[#07334F] hover:underline cursor-pointer">Bayar Lunas</button>
                    </div>
                    <div class="flex items-center gap-2 w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white focus-within:ring-2 focus-within:ring-[#0E527B] focus-within:border-[#0E527B] transition">
                        <span class="text-xs font-extrabold text-slate-400">Rp</span>
                        <input type="text" inputmode="numeric" id="dtlInputNominalDisplay" required placeholder="0"
                               oninput="syncDotNominal('dtlInputNominalDisplay', 'dtlInputNominal')"
                               class="w-full text-base font-black font-mono text-slate-900 bg-transparent focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-2.5">
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 has-checked:border-[#0E527B] has-checked:bg-sky-50/60 transition">
                            <input type="radio" name="metode_bayar" value="tunai" checked class="text-[#0E527B] focus:ring-[#0E527B]">
                            <span class="text-xs font-bold text-slate-800">Tunai</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 has-checked:border-[#0E527B] has-checked:bg-sky-50/60 transition">
                            <input type="radio" name="metode_bayar" value="transfer" class="text-[#0E527B] focus:ring-[#0E527B]">
                            <span class="text-xs font-bold text-slate-800">Transfer Bank</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan <span class="text-[#FFB900]">*</span></label>
                    <input type="text" name="keterangan_angsuran" id="dtlInputKet" required
                           placeholder="Contoh: Angsuran ke-1 atau Pelunasan faktur"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#0E527B] focus:border-[#0E527B] text-xs text-slate-800 bg-white">
                </div>

                <div class="grid grid-cols-2 gap-2.5 pt-1">
                    <button type="button" onclick="document.getElementById('modalBayarDetail').classList.add('hidden')" class="h-11 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-bold cursor-pointer transition">
                        Batal
                    </button>
                    <button type="submit" class="h-11 rounded-xl bg-[#0E527B] hover:bg-[#07334F] text-white text-xs font-bold shadow-sm transition cursor-pointer">
                        Simpan &amp; Buat QR
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Bayar -->
    <div id="modalEditBayarDetail" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-[2px] p-4 hidden">
        <div class="bg-white rounded-[1.75rem] max-w-md w-full p-5 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-base font-extrabold text-[#0E527B] tracking-tight">Ubah Pembayaran</h3>
                        <span id="dtlEditNoBsk" class="font-mono text-[10px] font-bold px-2 py-0.5 rounded-full bg-sky-50 text-[#0E527B] border border-sky-100">-</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-0.5">Sesuaikan data sebelum surat diselesaikan</p>
                </div>
                <button type="button" onclick="document.getElementById('modalEditBayarDetail').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center cursor-pointer transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="formEditBayarDetail" method="POST"
                  onsubmit="return validateDotNominal('dtlEditNominalDisplay', 'dtlEditNominal')" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="metode_bayar" id="dtlEditMetodeHidden" value="tunai">
                <input type="hidden" name="nominal_bayar" id="dtlEditNominal">

                <div class="bg-slate-50/90 border border-slate-200/80 rounded-2xl p-3.5 space-y-2 text-xs">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-slate-500 shrink-0">No. Faktur</span>
                        <span class="font-mono font-bold text-[#0E527B] text-right" id="dtlEditFakturNo">-</span>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <span class="text-slate-500 shrink-0">Pelanggan</span>
                        <span class="font-bold text-slate-800 text-right leading-snug" id="dtlEditCustomer">-</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 pt-2 border-t border-slate-200/80">
                        <span class="font-bold text-slate-700">Total Piutang</span>
                        <span class="font-black text-sm text-[#0E527B] font-mono" id="dtlEditSisaText">Rp 0</span>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-bold text-slate-700">Jumlah Bayar (Rp) <span class="text-[#FFB900]">*</span></label>
                        <button type="button" onclick="isiEditLunasDetail()" class="text-[11px] font-bold text-[#0E527B] hover:text-[#07334F] hover:underline cursor-pointer">Bayar Lunas</button>
                    </div>
                    <div class="flex items-center gap-2 w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white focus-within:ring-2 focus-within:ring-[#0E527B] focus-within:border-[#0E527B] transition">
                        <span class="text-xs font-extrabold text-slate-400">Rp</span>
                        <input type="text" inputmode="numeric" id="dtlEditNominalDisplay" required placeholder="0"
                               oninput="syncDotNominal('dtlEditNominalDisplay', 'dtlEditNominal')"
                               class="w-full text-base font-black font-mono text-slate-900 bg-transparent focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-2.5">
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 has-checked:border-[#0E527B] has-checked:bg-sky-50/60 transition">
                            <input type="radio" name="metode_bayar_dtl" value="tunai" onchange="document.getElementById('dtlEditMetodeHidden').value=this.value" class="text-[#0E527B] focus:ring-[#0E527B]">
                            <span class="text-xs font-bold text-slate-800">Tunai</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 has-checked:border-[#0E527B] has-checked:bg-sky-50/60 transition">
                            <input type="radio" name="metode_bayar_dtl" value="transfer" onchange="document.getElementById('dtlEditMetodeHidden').value=this.value" class="text-[#0E527B] focus:ring-[#0E527B]">
                            <span class="text-xs font-bold text-slate-800">Transfer Bank</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan <span class="text-[#FFB900]">*</span></label>
                    <input type="text" name="keterangan_angsuran" id="dtlEditKet" required
                           placeholder="Contoh: Revisi nominal bayar"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#0E527B] focus:border-[#0E527B] text-xs text-slate-800 bg-white">
                </div>

                <div class="grid grid-cols-2 gap-2.5 pt-1">
                    <button type="button" onclick="document.getElementById('modalEditBayarDetail').classList.add('hidden')" class="h-11 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-bold cursor-pointer transition">
                        Batal
                    </button>
                    <button type="submit" class="h-11 rounded-xl bg-[#0E527B] hover:bg-[#07334F] text-white text-xs font-bold shadow-sm transition cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tolak -->
    <div id="modalTolakDetail" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-5 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-extrabold text-[#0E527B] uppercase tracking-tight">Tolak Faktur Ini</h3>
                <button type="button" onclick="document.getElementById('modalTolakDetail').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="formTolakDetail" method="POST" class="space-y-3.5">
                @csrf
                <input type="hidden" name="aksi" value="tolak">
                <p class="text-xs text-slate-600">Tolak faktur <strong id="dtlTolakFakturNo" class="font-mono">-</strong> (<strong id="dtlTolakCustomer">-</strong>).</p>
                <textarea name="alasan_penolakan" required rows="3" placeholder="Alasan penolakan..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800"></textarea>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-[#FFB900] hover:bg-amber-400 text-[#07334F] text-xs font-black cursor-pointer">Konfirmasi Tolak Faktur</button>
            </form>
        </div>
    </div>

    <script>
        let dtlSisaMax = 0;
        let dtlEditSisaMax = 0;

        function formatRibuanTitik(val) {
            const digits = String(val ?? '').replace(/\D/g, '').replace(/^0+(?=\d)/, '');
            if (!digits) return '';
            return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function syncDotNominal(displayId, hiddenId) {
            const displayEl = document.getElementById(displayId);
            const hiddenEl = document.getElementById(hiddenId);
            const digits = String(displayEl.value ?? '').replace(/\D/g, '').replace(/^0+(?=\d)/, '');
            displayEl.value = digits ? digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
            hiddenEl.value = digits;
            displayEl.setCustomValidity('');
        }

        function setDotNominalValue(displayId, hiddenId, numericVal) {
            const rawInt = Math.round(parseFloat(numericVal) || 0);
            const strVal = rawInt > 0 ? String(rawInt) : '';
            document.getElementById(hiddenId).value = strVal;
            document.getElementById(displayId).value = formatRibuanTitik(strVal);
            document.getElementById(displayId).setCustomValidity('');
        }

        function validateDotNominal(displayId, hiddenId) {
            syncDotNominal(displayId, hiddenId);
            const val = parseInt(document.getElementById(hiddenId).value || '0', 10);
            const displayEl = document.getElementById(displayId);
            if (!val || val < 1000) {
                displayEl.setCustomValidity('Nominal pembayaran minimal Rp 1.000');
                displayEl.reportValidity();
                return false;
            }
            return true;
        }

        function hubungiKosongDetail(nama) {
            const pesan = 'Nomor telepon untuk ' + nama + ' belum tercatat pada data pelanggan.';
            if (window.Swal) {
                Swal.fire({ icon: 'info', title: 'Nomor Belum Tersedia', text: pesan, confirmButtonColor: '#0E527B' });
            } else {
                alert(pesan);
            }
        }

        function bukaModalBayarDetail(fakturId, penugasanId, fakturNo, customer, sisa) {
            dtlSisaMax = parseFloat(sisa) || 0;
            document.getElementById('dtlBayarFakturId').value = fakturId;
            document.getElementById('dtlBayarPenugasanId').value = penugasanId || '';
            document.getElementById('dtlBayarFakturNo').innerText = fakturNo;
            document.getElementById('dtlBayarCustomer').innerText = customer;
            document.getElementById('dtlBayarSisaText').innerText = 'Rp ' + Number(dtlSisaMax).toLocaleString('id-ID');
            setDotNominalValue('dtlInputNominalDisplay', 'dtlInputNominal', '');
            document.getElementById('dtlInputKet').value = '';
            document.getElementById('modalBayarDetail').classList.remove('hidden');
        }

        function isiLunasDetail() {
            if (dtlSisaMax > 0) {
                setDotNominalValue('dtlInputNominalDisplay', 'dtlInputNominal', dtlSisaMax);
                const ket = document.getElementById('dtlInputKet');
                if (!ket.value.trim()) ket.value = 'Pelunasan faktur';
            }
        }

        function bukaModalEditDetail(setoranId, noBsk, fakturNo, customer, sisa, nominal, metode, ket) {
            dtlEditSisaMax = parseFloat(sisa) || 0;
            const form = document.getElementById('formEditBayarDetail');
            form.action = "{{ url('keuangan/inkaso/setoran-lapangan') }}/" + setoranId;
            document.getElementById('dtlEditNoBsk').innerText = noBsk;
            document.getElementById('dtlEditFakturNo').innerText = fakturNo;
            document.getElementById('dtlEditCustomer').innerText = customer;
            document.getElementById('dtlEditSisaText').innerText = 'Rp ' + Number(dtlEditSisaMax).toLocaleString('id-ID');
            setDotNominalValue('dtlEditNominalDisplay', 'dtlEditNominal', nominal);
            document.getElementById('dtlEditKet').value = ket || '';
            document.querySelectorAll('input[name="metode_bayar_dtl"]').forEach(r => { r.checked = (r.value === (metode || 'tunai')); });
            document.getElementById('dtlEditMetodeHidden').value = metode || 'tunai';
            document.getElementById('modalEditBayarDetail').classList.remove('hidden');
        }

        function isiEditLunasDetail() {
            if (dtlEditSisaMax > 0) {
                setDotNominalValue('dtlEditNominalDisplay', 'dtlEditNominal', dtlEditSisaMax);
                const ket = document.getElementById('dtlEditKet');
                if (!ket.value.trim()) ket.value = 'Pelunasan faktur';
            }
        }

        function bukaModalTolakDetail(tugasId, fakturNo, customer) {
            document.getElementById('dtlTolakFakturNo').innerText = fakturNo;
            document.getElementById('dtlTolakCustomer').innerText = customer;
            document.getElementById('formTolakDetail').action = "{{ url('keuangan/inkaso/penugasan') }}/" + tugasId + "/konfirmasi";
            document.getElementById('modalTolakDetail').classList.remove('hidden');
        }
    </script>
</x-mobile-inkaso-layout>
