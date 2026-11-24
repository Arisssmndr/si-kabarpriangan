<x-mobile-inkaso-layout>
    <div x-data="{ mobileTab: '{{ $suratTugasMenunggu->count() > 0 ? 'surat_tugas' : 'tagihan' }}' }" class="space-y-4">

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- 1. PROFILE HEADER CARD (GAYA N-DESAPRESENCE & 60-30-10 KP-BLUE)       -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div class="p-4 sm:p-5 text-white rounded-3xl shadow-lg border border-[#FFB900]/40 relative overflow-hidden" 
             style="background: linear-gradient(135deg, #0E527B 0%, #07334F 100%) !important;">
            <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-[#FFB900]/15 rounded-full blur-xl pointer-events-none"></div>

            <div class="flex items-center gap-3.5 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-[#07334F] text-[#FFB900] flex items-center justify-center font-black text-xl shrink-0 border-2 border-[#FFB900] shadow-md">
                    {{ strtoupper(substr($user->name ?? 'I', 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-[#FFB900] text-[#0A3D5C] uppercase tracking-wider shadow-2xs">
                        Kolektor Lapangan Inkaso
                    </span>
                    <h2 class="font-extrabold text-base sm:text-lg text-white truncate mt-1 leading-tight">
                        {{ $user->name ?? 'Petugas Inkaso' }}
                    </h2>
                    <p class="text-xs text-sky-100 truncate font-medium mt-0.5">
                        PT Berkah Pikiran Rakyat &bull; {{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}
                    </p>
                </div>
            </div>

            <div class="mt-3.5 pt-2.5 border-t border-sky-800/80 grid grid-cols-2 gap-2 text-xs relative z-10">
                <div>
                    <span class="text-sky-200/80 text-[10px] uppercase font-bold tracking-wider">Wilayah Operasional</span>
                    <p class="text-white font-bold text-xs mt-0.5 truncate">Priangan Timur</p>
                </div>
                <div>
                    <span class="text-sky-200/80 text-[10px] uppercase font-bold tracking-wider">Status Koleksi</span>
                    <p class="text-[#FFB900] font-bold text-xs mt-0.5 truncate">Aktif Lapangan</p>
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- 2. MINI KPI RINGKAS (60-30-10: PUTIH, KP-BLUE, GOLD)                   -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div class="grid grid-cols-3 gap-2">
            <div class="bg-white border border-slate-200 rounded-2xl p-2.5 sm:p-3 text-center shadow-xs">
                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider block">Target Piutang</span>
                <span class="text-xs sm:text-sm font-black text-[#0E527B] mt-0.5 block truncate">
                    Rp {{ number_format($totalTargetPiutang, 0, ',', '.') }}
                </span>
            </div>
            <div class="bg-amber-50/80 border border-amber-200 rounded-2xl p-2.5 sm:p-3 text-center shadow-xs">
                <span class="text-[10px] uppercase font-bold text-amber-800 tracking-wider block">Menunggu Kasir</span>
                <span class="text-xs sm:text-sm font-black text-amber-950 mt-0.5 block truncate">
                    Rp {{ number_format($totalPendingKasirHariIni, 0, ',', '.') }}
                </span>
            </div>
            <div class="bg-[#0E527B]/10 border border-[#0E527B]/30 rounded-2xl p-2.5 sm:p-3 text-center shadow-xs">
                <span class="text-[10px] uppercase font-bold text-[#0E527B] tracking-wider block">Disetujui Kasir</span>
                <span class="text-xs sm:text-sm font-black text-[#0E527B] mt-0.5 block truncate">
                    Rp {{ number_format($totalTerkumpulHariIni, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Notifikasi Pesan Flash Sistem -->
        @if(session('success'))
            <div class="p-3.5 rounded-2xl bg-blue-50 border border-blue-200 text-[#0E527B] text-xs font-bold flex items-center justify-between shadow-xs">
                <span>✓ {{ session('success') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-bold flex items-center justify-between shadow-xs">
                <span>⚠ {{ session('warning') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-bold flex items-center justify-between shadow-xs">
                <span>✕ {{ session('error') }}</span>
            </div>
        @endif

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- 3. NAVIGASI TAB SEGMENTED KONTROL CEPAT                                 -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div class="bg-white border border-slate-200 rounded-2xl p-1.5 shadow-xs flex items-center gap-1">
            <button type="button" 
                    @click="mobileTab = 'surat_tugas'"
                    :class="mobileTab === 'surat_tugas' ? 'bg-[#0E527B] text-white font-bold shadow-xs' : 'text-slate-600 hover:text-[#0E527B] font-semibold'"
                    class="flex-1 py-2 px-2 rounded-xl text-xs flex items-center justify-center gap-1.5 transition cursor-pointer">
                <span>Surat Tugas</span>
                @if($suratTugasMenunggu->count() > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black bg-[#D97706] text-white">
                        {{ $suratTugasMenunggu->count() }}
                    </span>
                @endif
            </button>

            <button type="button" 
                    @click="mobileTab = 'tagihan'"
                    :class="mobileTab === 'tagihan' ? 'bg-[#0E527B] text-white font-bold shadow-xs' : 'text-slate-600 hover:text-[#0E527B] font-semibold'"
                    class="flex-1 py-2 px-2 rounded-xl text-xs flex items-center justify-center gap-1.5 transition cursor-pointer">
                <span>Tagihan Aktif</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                    {{ $tagihanAktif->count() }}
                </span>
            </button>

            <button type="button" 
                    @click="mobileTab = 'riwayat'"
                    :class="mobileTab === 'riwayat' ? 'bg-[#0E527B] text-white font-bold shadow-xs' : 'text-slate-600 hover:text-[#0E527B] font-semibold'"
                    class="flex-1 py-2 px-2 rounded-xl text-xs flex items-center justify-center gap-1.5 transition cursor-pointer">
                <span>Setoran BSK</span>
                @if($setoranHariIni->where('status_approval', 'pending')->count() > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black bg-amber-400 text-slate-900">
                        {{ $setoranHariIni->where('status_approval', 'pending')->count() }}
                    </span>
                @endif
            </button>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- TAB 1: SURAT TUGAS MASUK (REVIEW & TERIMA / TOLAK)                     -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div x-show="mobileTab === 'surat_tugas'" x-cloak class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <span class="text-xs font-extrabold text-[#0E527B] uppercase tracking-wider">
                    Surat Tugas Menunggu ({{ $suratTugasMenunggu->count() }})
                </span>
                <span class="text-[11px] text-slate-400">Review sebelum ke lapangan</span>
            </div>

            @forelse($suratTugasMenunggu as $tugas)
                <div class="bg-white border-2 border-[#0E527B]/20 rounded-2xl p-4 shadow-xs space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-[#0E527B] text-[11px] font-mono font-bold border border-slate-200">
                                {{ $tugas->no_tugas }}
                            </span>
                            <h3 class="text-sm font-extrabold text-slate-900 mt-1">
                                {{ $tugas->faktur->customer->nama_customer ?? 'Pelanggan / Agen' }}
                            </h3>
                            <p class="text-xs text-slate-500">
                                {{ $tugas->faktur->customer->kota ?? ($tugas->faktur->customer->alamat ?? 'Tasikmalaya') }}
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Sisa Piutang</span>
                            <span class="text-sm font-black text-rose-600 block">
                                Rp {{ number_format($tugas->faktur->sisa_piutang ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-2.5 text-xs text-slate-600 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">No. Faktur:</span>
                            <span class="font-mono font-bold text-slate-800">{{ $tugas->faktur->no_faktur }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Divisi:</span>
                            <span class="font-semibold uppercase text-[#0E527B]">{{ $tugas->faktur->kategori ?? 'sirkulasi' }}</span>
                        </div>
                        @if($tugas->catatan)
                            <div class="pt-1 border-t border-slate-200/70 text-[11px] text-slate-500 italic">
                                "{{ $tugas->catatan }}"
                            </div>
                        @endif
                    </div>

                    <!-- Tombol Aksi Konfirmasi (Terima / Tolak) -->
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <button type="button" 
                                onclick="bukaModalTolak('{{ $tugas->id }}', '{{ $tugas->faktur->no_faktur }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '-') }}')"
                                class="w-full py-2.5 px-3 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold flex items-center justify-center gap-1.5 transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Tolak Tugas</span>
                        </button>

                        <form action="{{ route('keuangan.inkaso.penugasan.konfirmasi', $tugas->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="aksi" value="terima">
                            <button type="submit" 
                                    class="w-full py-2.5 px-3 rounded-xl bg-[#0E527B] hover:bg-[#0A3D5C] text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition cursor-pointer">
                                <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Terima Tugas</span>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center shadow-xs">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-[#0E527B] mx-auto flex items-center justify-center mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800">Tidak Ada Surat Tugas Baru</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">
                        Semua surat tugas dari Admin Faktur sudah Anda proses. Buka tab <strong>Tagihan Aktif</strong> untuk penagihan lapangan.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- TAB 2: DAFTAR TAGIHAN AKTIF & INPUT PEMBAYARAN                         -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div x-show="mobileTab === 'tagihan'" x-cloak class="space-y-3">
            <!-- Search Box Cepat -->
            <div class="bg-white border border-slate-200 rounded-2xl p-2.5 shadow-xs">
                <form method="GET" action="{{ route('keuangan.inkaso.mobile') }}" class="relative flex items-center">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" 
                           name="q" 
                           value="{{ $q }}" 
                           placeholder="Cari No. Faktur (misal 02) atau Nama Agen..." 
                           class="w-full pl-9 pr-8 py-2 rounded-xl text-xs bg-slate-50 border-none focus:ring-2 focus:ring-[#0E527B] text-slate-800 placeholder-slate-400">
                    @if($q)
                        <a href="{{ route('keuangan.inkaso.mobile') }}" class="absolute right-2.5 text-slate-400 hover:text-slate-600 text-xs font-bold">✕</a>
                    @endif
                </form>
            </div>

            <div class="flex items-center justify-between px-1">
                <span class="text-xs font-extrabold text-[#0E527B] uppercase tracking-wider">
                    Daftar Tagihan Berjalan ({{ $tagihanAktif->count() }})
                </span>
                <span class="text-[11px] text-slate-400">Klik "Input Pembayaran" bila klien bayar</span>
            </div>

            @forelse($tagihanAktif as $tugas)
                <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs space-y-3 hover:border-[#0E527B]/40 transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-[#0E527B] text-[11px] font-mono font-bold border border-slate-200">
                                    {{ $tugas->faktur->no_faktur }}
                                </span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ ($tugas->faktur->total_terbayar > 0) ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-700' }}">
                                    {{ ($tugas->faktur->total_terbayar > 0) ? 'Cicilan' : 'Belum Bayar' }}
                                </span>
                            </div>
                            <h3 class="text-sm font-extrabold text-slate-900 mt-1.5 truncate">
                                {{ $tugas->faktur->customer->nama_customer ?? 'Pelanggan / Agen' }}
                            </h3>
                            <p class="text-xs text-slate-500 truncate">
                                {{ $tugas->faktur->customer->kota ?? ($tugas->faktur->customer->alamat ?? 'Tasikmalaya') }}
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Sisa Tagihan</span>
                            <span class="text-base font-black text-rose-600 block">
                                Rp {{ number_format($tugas->faktur->sisa_piutang ?? 0, 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">
                                Total: Rp {{ number_format($tugas->faktur->total_tagihan ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Tombol Cepat Hubungi & Tombol Input Pembayaran -->
                    <div class="flex items-center gap-2 pt-1 border-t border-slate-100">
                        @if(!empty($tugas->faktur->customer->no_telepon))
                            <a href="tel:{{ $tugas->faktur->customer->no_telepon }}" 
                               class="h-9 px-3 rounded-xl border border-slate-200 text-slate-700 hover:text-[#0E527B] hover:bg-slate-50 text-xs font-bold flex items-center justify-center gap-1 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span class="hidden sm:inline">Hubungi</span>
                            </a>
                        @endif

                        <button type="button" 
                                onclick="bukaModalBayar('{{ $tugas->faktur_id }}', '{{ $tugas->id }}', '{{ $tugas->faktur->no_faktur }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '-') }}', '{{ (float) $tugas->faktur->sisa_piutang }}')"
                                class="flex-1 h-9 px-3 rounded-xl bg-[#0E527B] hover:bg-[#0A3D5C] text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition cursor-pointer">
                            <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Input Pembayaran</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center shadow-xs">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800">Belum Ada Tagihan Aktif</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">
                        Tidak ada faktur piutang yang sedang berjalan. Cek tab <strong>Surat Tugas</strong> untuk menerima tugas baru.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- TAB 3: RIWAYAT SETORAN LAPANGAN HARI INI (BARCODE BSK)                  -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div x-show="mobileTab === 'riwayat'" x-cloak class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <span class="text-xs font-extrabold text-[#0E527B] uppercase tracking-wider">
                    Setoran Hari Ini ({{ $setoranHariIni->count() }})
                </span>
                <span class="text-[11px] text-slate-400">Scan Barcode di Loket Kasir</span>
            </div>

            @forelse($setoranHariIni as $setoran)
                <div class="bg-white border {{ $setoran->status_approval === 'approved' ? 'border-[#0E527B]/30 bg-slate-50/50' : ($setoran->status_approval === 'rejected' ? 'border-rose-200 bg-rose-50/20' : 'border-amber-200 bg-amber-50/20') }} rounded-2xl p-4 shadow-xs space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-white border border-slate-200 font-mono text-[11px] font-bold text-slate-800">
                                {{ $setoran->no_bsk }}
                            </span>
                            <h3 class="text-sm font-extrabold text-slate-900 mt-1">
                                {{ $setoran->nama_pembayar }}
                            </h3>
                            <p class="text-[11px] text-slate-500">
                                Faktur: <span class="font-mono font-semibold">{{ $setoran->faktur->no_faktur ?? '-' }}</span> &bull; {{ $setoran->waktu_terima_lapangan ? $setoran->waktu_terima_lapangan->format('H:i') : '' }} WIB
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="text-sm font-black text-[#0E527B] block">
                                Rp {{ number_format($setoran->nominal_titipan, 0, ',', '.') }}
                            </span>
                            @if($setoran->status_approval === 'approved')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#0E527B] text-white mt-1">
                                    ✓ Disetujui Kasir
                                </span>
                            @elseif($setoran->status_approval === 'rejected')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-800 mt-1">
                                    ✕ Ditolak Kasir
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-900 mt-1">
                                    ⌛ Menunggu Kasir
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="text-xs text-slate-600 bg-white/80 p-2 rounded-xl border border-slate-100">
                        <strong>Keterangan:</strong> {{ $setoran->keterangan_angsuran }}
                        @if($setoran->status_approval === 'approved' && $setoran->no_kuitansi_kasir)
                            <div class="text-[11px] text-[#0E527B] font-mono font-bold mt-1">
                                No. BKM Kasir: {{ $setoran->no_kuitansi_kasir }}
                            </div>
                        @endif
                    </div>

                    <!-- Tombol Buka Barcode -->
                    <div class="pt-1">
                        <a href="{{ route('keuangan.inkaso.bsk.show', $setoran->no_bsk) }}" 
                           class="w-full py-2.5 px-3 rounded-xl bg-[#0E527B] hover:bg-[#0A3D5C] text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition cursor-pointer">
                            <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <span>Tampilkan Barcode ke Kasir</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center shadow-xs">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800">Belum Ada Setoran Hari Ini</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">
                        Catat penerimaan pembayaran klien di menu <strong>Tagihan Aktif</strong> untuk menerbitkan Barcode BSK.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- 4. BOTTOM NAVIGATION BAR (STICKY MOBILE GAYA N-DESAPRESENCE)           -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <nav class="bg-white/95 backdrop-blur-md border-t border-slate-200 fixed bottom-0 left-0 right-0 z-40 shadow-[0_-8px_30px_rgba(14,82,123,0.08)] py-2 px-3">
            <div class="max-w-lg mx-auto flex items-center justify-between">
                
                {{-- 1. Surat Tugas --}}
                <button type="button" 
                        @click="mobileTab = 'surat_tugas'; window.scrollTo({top: 0, behavior: 'smooth'})" 
                        class="flex flex-col items-center group transition flex-1 py-1 relative cursor-pointer">
                    @if($suratTugasMenunggu->count() > 0)
                        <span class="absolute top-0 right-3 min-w-[16px] h-4 px-1 bg-[#D97706] rounded-full text-[9px] text-white font-bold flex items-center justify-center shadow-xs ring-2 ring-white z-10">
                            {{ $suratTugasMenunggu->count() }}
                        </span>
                    @endif
                    <div :class="mobileTab === 'surat_tugas' ? 'w-12 h-8 rounded-xl bg-[#0E527B] text-[#FFB900] shadow-sm ring-1 ring-[#FFB900]/30' : 'w-12 h-8 rounded-xl text-slate-400 group-hover:text-[#0E527B] group-hover:bg-slate-100'"
                         class="flex items-center justify-center transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span :class="mobileTab === 'surat_tugas' ? 'text-[10.5px] font-bold text-[#0E527B]' : 'text-[10.5px] font-medium text-slate-400 group-hover:text-slate-600'" 
                          class="mt-1 tracking-tight">Surat Tugas</span>
                </button>

                {{-- 2. Tagihan Aktif --}}
                <button type="button" 
                        @click="mobileTab = 'tagihan'; window.scrollTo({top: 0, behavior: 'smooth'})" 
                        class="flex flex-col items-center group transition flex-1 py-1 relative cursor-pointer">
                    @if($tagihanAktif->count() > 0)
                        <span class="absolute top-0 right-3 min-w-[16px] h-4 px-1 bg-[#0E527B] rounded-full text-[9px] text-white font-bold flex items-center justify-center shadow-xs ring-2 ring-white z-10">
                            {{ $tagihanAktif->count() }}
                        </span>
                    @endif
                    <div :class="mobileTab === 'tagihan' ? 'w-12 h-8 rounded-xl bg-[#0E527B] text-[#FFB900] shadow-sm ring-1 ring-[#FFB900]/30' : 'w-12 h-8 rounded-xl text-slate-400 group-hover:text-[#0E527B] group-hover:bg-slate-100'"
                         class="flex items-center justify-center transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <span :class="mobileTab === 'tagihan' ? 'text-[10.5px] font-bold text-[#0E527B]' : 'text-[10.5px] font-medium text-slate-400 group-hover:text-slate-600'" 
                          class="mt-1 tracking-tight">Tagihan Aktif</span>
                </button>

                {{-- 3. Setoran BSK --}}
                <button type="button" 
                        @click="mobileTab = 'riwayat'; window.scrollTo({top: 0, behavior: 'smooth'})" 
                        class="flex flex-col items-center group transition flex-1 py-1 relative cursor-pointer">
                    @if($setoranHariIni->where('status_approval', 'pending')->count() > 0)
                        <span class="absolute top-0 right-3 min-w-[16px] h-4 px-1 bg-[#D97706] rounded-full text-[9px] text-white font-bold flex items-center justify-center shadow-xs ring-2 ring-white z-10">
                            {{ $setoranHariIni->where('status_approval', 'pending')->count() }}
                        </span>
                    @endif
                    <div :class="mobileTab === 'riwayat' ? 'w-12 h-8 rounded-xl bg-[#0E527B] text-[#FFB900] shadow-sm ring-1 ring-[#FFB900]/30' : 'w-12 h-8 rounded-xl text-slate-400 group-hover:text-[#0E527B] group-hover:bg-slate-100'"
                         class="flex items-center justify-center transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <span :class="mobileTab === 'riwayat' ? 'text-[10.5px] font-bold text-[#0E527B]' : 'text-[10.5px] font-medium text-slate-400 group-hover:text-slate-600'" 
                          class="mt-1 tracking-tight">Setoran BSK</span>
                </button>

                {{-- 4. Keluar --}}
                <button type="button" 
                        @click="showLogoutModal = true" 
                        class="flex flex-col items-center group transition flex-1 py-1 cursor-pointer">
                    <div class="w-12 h-8 rounded-xl text-slate-400 group-hover:text-rose-600 group-hover:bg-rose-50 flex items-center justify-center transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </div>
                    <span class="text-[10.5px] font-medium text-slate-400 group-hover:text-rose-600 mt-1 tracking-tight">Keluar</span>
                </button>
            </div>
        </nav>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- MODAL 1: INPUT PEMBAYARAN LAPANGAN (TERBITKAN BSK)                     -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div id="modalBayar" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 hidden">
            <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl border border-slate-200 space-y-4 animate-in fade-in zoom-in duration-150">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-extrabold text-[#0E527B] uppercase tracking-tight">
                            Input Pembayaran Lapangan
                        </h3>
                        <p class="text-[11px] text-slate-500">Terbitkan Barcode Setoran Koleksi (BSK)</p>
                    </div>
                    <button type="button" onclick="tutupModalBayar()" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
                </div>

                <form action="{{ route('keuangan.inkaso.setoran-lapangan.store') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <input type="hidden" name="faktur_id" id="bayarFakturId">
                    <input type="hidden" name="penugasan_id" id="bayarPenugasanId">

                    <!-- Info Ringkas Faktur -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 space-y-1 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500">No. Faktur:</span>
                            <span class="font-mono font-bold text-[#0E527B]" id="bayarFakturNo">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Pelanggan/Agen:</span>
                            <span class="font-bold text-slate-800" id="bayarCustomer">-</span>
                        </div>
                        <div class="flex justify-between pt-1 border-t border-slate-200">
                            <span class="font-bold text-slate-700">Sisa Piutang:</span>
                            <span class="font-black text-rose-600" id="bayarSisaPiutangText">Rp 0</span>
                        </div>
                    </div>

                    <!-- Input Nominal -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-bold text-slate-700">Nominal Disetor (Rp) <span class="text-rose-500">*</span></label>
                            <button type="button" onclick="isiBayarLunas()" class="text-[11px] font-bold text-[#0E527B] hover:underline">
                                Bayar Lunas Penuh
                            </button>
                        </div>
                        <input type="number" 
                               name="nominal_bayar" 
                               id="inputNominalBayar" 
                               required 
                               min="1000" 
                               step="500" 
                               placeholder="Contoh: 10000000" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#0E527B] text-sm font-black text-slate-900">
                    </div>

                    <!-- Metode Bayar -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pembayaran</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 has-checked:border-[#0E527B] has-checked:bg-slate-100">
                                <input type="radio" name="metode_bayar" value="tunai" checked class="text-[#0E527B] focus:ring-[#0E527B]">
                                <span class="text-xs font-bold text-slate-800">Tunai (Cash)</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 has-checked:border-[#0E527B] has-checked:bg-slate-100">
                                <input type="radio" name="metode_bayar" value="transfer" class="text-[#0E527B] focus:ring-[#0E527B]">
                                <span class="text-xs font-bold text-slate-800">Transfer Bank</span>
                            </label>
                        </div>
                    </div>

                    <!-- Keterangan Angsuran -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan Angsuran</label>
                        <input type="text" 
                               name="keterangan_angsuran" 
                               id="inputKetAngsuran" 
                               placeholder="Misal: Pembayaran pertama / Angsuran ke-1" 
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#0E527B] text-xs text-slate-800">
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full py-3 px-4 rounded-xl bg-[#0E527B] hover:bg-[#0A3D5C] text-white text-xs font-bold flex items-center justify-center gap-2 shadow-sm transition cursor-pointer">
                            <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <span>Simpan &amp; Terbitkan Barcode Setoran</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- MODAL 2: TOLAK SURAT TUGAS PENAGIHAN                                   -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div id="modalTolak" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 hidden">
            <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl border border-slate-200 space-y-4 animate-in fade-in zoom-in duration-150">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-extrabold text-rose-950 uppercase tracking-tight">
                            Tolak Surat Tugas Penagihan
                        </h3>
                        <p class="text-[11px] text-slate-500">Kembalikan ke antrean Admin Faktur</p>
                    </div>
                    <button type="button" onclick="tutupModalTolak()" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
                </div>

                <form id="formTolakTugas" method="POST" class="space-y-3.5">
                    @csrf
                    <input type="hidden" name="aksi" value="tolak">

                    <p class="text-xs text-slate-600">
                        Anda menolak menagih faktur <strong id="tolakFakturNo" class="text-slate-900">-</strong> untuk pelanggan <strong id="tolakCustomer" class="text-slate-900">-</strong>. Faktur ini akan dikembalikan ke Admin Faktur.
                    </p>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Alasan Penolakan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="alasan_penolakan" 
                                  required 
                                  rows="3" 
                                  placeholder="Contoh: Toko tutup permanen / Pelanggan minta jadwal ulang / Luar jangkauan rute..." 
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 text-xs text-slate-800"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <button type="button" onclick="tutupModalTolak()" class="py-2.5 px-3 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold">
                            Batal
                        </button>
                        <button type="submit" class="py-2.5 px-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                            Konfirmasi Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Script Interaksi Modal Mobile -->
    <script>
        let currentSisaPiutang = 0;

        function bukaModalBayar(fakturId, penugasanId, fakturNo, customer, sisaPiutang) {
            currentSisaPiutang = parseFloat(sisaPiutang) || 0;
            document.getElementById('bayarFakturId').value = fakturId;
            document.getElementById('bayarPenugasanId').value = penugasanId || '';
            document.getElementById('bayarFakturNo').innerText = fakturNo;
            document.getElementById('bayarCustomer').innerText = customer;
            document.getElementById('bayarSisaPiutangText').innerText = 'Rp ' + Number(currentSisaPiutang).toLocaleString('id-ID');
            document.getElementById('inputNominalBayar').value = '';
            document.getElementById('inputKetAngsuran').value = 'Pembayaran pertama';
            document.getElementById('modalBayar').classList.remove('hidden');
        }

        function isiBayarLunas() {
            if (currentSisaPiutang > 0) {
                document.getElementById('inputNominalBayar').value = currentSisaPiutang;
                document.getElementById('inputKetAngsuran').value = 'Pelunasan faktur';
            }
        }

        function tutupModalBayar() {
            document.getElementById('modalBayar').classList.add('hidden');
        }

        function bukaModalTolak(tugasId, fakturNo, customer) {
            document.getElementById('tolakFakturNo').innerText = fakturNo;
            document.getElementById('tolakCustomer').innerText = customer;
            const form = document.getElementById('formTolakTugas');
            form.action = "{{ url('keuangan/inkaso/penugasan') }}/" + tugasId + "/konfirmasi";
            document.getElementById('modalTolak').classList.remove('hidden');
        }

        function tutupModalTolak() {
            document.getElementById('modalTolak').classList.add('hidden');
        }
    </script>
</x-mobile-inkaso-layout>
