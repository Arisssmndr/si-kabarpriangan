<x-mobile-inkaso-layout>
    <div x-data="{ 
            mobileTab: '{{ in_array(request('tab'), ['tagihan', 'surat_tugas', 'setoran_bsk']) ? 'penagihan' : request('tab', 'beranda') }}', 
            subFilter: '{{ request('tab') === 'surat_tugas' ? 'tugas_baru' : (request('tab') === 'setoran_bsk' ? 'setor_kasir' : 'semua') }}',
            photoPreview: null, 
            editProfileModal: false 
         }" 
         class="space-y-4">

        <!-- Notifikasi Pesan Flash Sistem -->
        @if(session('success'))
            <div class="p-3.5 rounded-2xl bg-sky-50 border border-sky-200 text-[#0E527B] text-xs font-bold flex items-center justify-between shadow-xs">
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
        <!-- TAB 1: BERANDA / DASHBOARD UTAMA (FOTO 3 & RINGKASAN INFORMASI)        -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div x-show="mobileTab === 'beranda'" x-cloak class="space-y-4">
            
            <!-- 1. IDENTITAS PETUGAS INKASO LAPANGAN (FOTO 3 BAGIAN ATAS) -->
            <div class="p-4 sm:p-5 text-white rounded-3xl shadow-md border border-[#FFB900]/30" 
                 style="background: linear-gradient(135deg, #0E527B 0%, #07334F 100%) !important;">
                <div class="flex items-center gap-4">
                    <!-- Foto Profil Bulat Gaya WhatsApp -->
                    <button type="button" 
                            @click="mobileTab = 'profil'" 
                            class="relative shrink-0 cursor-pointer active:scale-95 transition" 
                            title="Buka Profil Petugas">
                        @if($user->foto_url)
                            <img src="{{ $user->foto_url }}" 
                                 alt="{{ $user->name }}" 
                                 class="w-16 h-16 rounded-full object-cover border-2 border-[#FFB900] shadow-md">
                        @else
                            <!-- WhatsApp Style Default Avatar: Bulat Abu-abu dengan siluet kepala-bahu putih/abu -->
                            <div class="w-16 h-16 rounded-full bg-[#CFD8DC] flex items-center justify-center border-2 border-[#FFB900] shadow-md overflow-hidden">
                                <svg class="w-14 h-14 text-[#90A4AE] translate-y-1.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            </div>
                        @endif
                    </button>

                    <!-- Identitas Petugas Ringkas & Elegan -->
                    <div class="min-w-0 flex-1">
                        <span class="text-xs font-bold text-[#FFB900] uppercase tracking-wider block">
                            Petugas Inkaso Lapangan
                        </span>
                        <h2 class="font-extrabold text-lg sm:text-xl text-white truncate mt-0.5 leading-snug">
                            {{ $user->name ?? 'Petugas Inkaso' }}
                        </h2>
                        <p class="text-xs text-sky-200 truncate mt-0.5 font-medium">
                            Priangan Timur &bull; Kolektor Lapangan
                        </p>
                    </div>
                </div>
            </div>

            <!-- 2. KARTU TARGET PIUTANG UTAMA (FOTO 3 BAGIAN TENGAH) -->
            <div class="bg-white border-2 border-[#0E527B]/15 rounded-2xl p-4 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider block">
                        Target Piutang Hari Ini
                    </span>
                    <span class="text-2xl sm:text-3xl font-black text-[#0E527B] mt-0.5 block leading-tight">
                        Rp {{ number_format($totalTargetPiutang, 0, ',', '.') }}
                    </span>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-black bg-sky-50 text-[#0E527B] border border-sky-200">
                        {{ $tagihanAktif->count() }} Faktur Aktif
                    </span>
                </div>
            </div>

            <!-- 3. DUA STATUS KASIR (FOTO 3 BAGIAN BAWAH) -->
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white border border-amber-200 rounded-2xl p-3.5 shadow-xs">
                    <span class="text-[11px] font-bold text-amber-800 uppercase tracking-tight block">
                        Menunggu Kasir
                    </span>
                    <span class="text-base sm:text-lg font-black text-amber-950 mt-1 block truncate">
                        Rp {{ number_format($totalPendingKasirHariIni, 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] text-slate-500 font-semibold mt-1 block">
                        {{ $setoranPending->count() }} BSK Siap Setor
                    </span>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl p-3.5 shadow-xs">
                    <span class="text-[11px] font-bold text-[#0E527B] uppercase tracking-tight block">
                        Disetujui Kasir
                    </span>
                    <span class="text-base sm:text-lg font-black text-[#0E527B] mt-1 block truncate">
                        Rp {{ number_format($totalTerkumpulHariIni, 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] text-emerald-700 font-bold mt-1 block">
                        ✓ Kas Sah Hari Ini
                    </span>
                </div>
            </div>

            <!-- 4. PUSAT AKSI & STATUS OPERASIONAL LAPANGAN -->
            <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-xs space-y-3.5">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Status Tugas Operasional</h3>
                    <span class="text-[11px] text-[#0E527B] font-bold font-mono">{{ now()->translatedFormat('d M Y') }}</span>
                </div>

                <!-- Alert jika ada Surat Tugas baru menunggu konfirmasi -->
                @if($suratTugasMenunggu->count() > 0)
                    <div class="p-3.5 rounded-2xl bg-amber-50/90 border border-amber-200 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-8 h-8 rounded-xl bg-amber-400 text-amber-950 font-black text-sm flex items-center justify-center shrink-0">
                                {{ $suratTugasMenunggu->count() }}
                            </span>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-amber-950 truncate">Surat Tugas Baru Masuk</h4>
                                <p class="text-[11px] text-amber-800">Perlu konfirmasi terima/tolak rute</p>
                            </div>
                        </div>
                        <button type="button" 
                                @click="mobileTab = 'penagihan'; subFilter = 'tugas_baru'; window.scrollTo({top: 0, behavior: 'smooth'})"
                                class="px-3 py-1.5 rounded-xl bg-[#0E527B] text-white text-xs font-bold shrink-0 hover:bg-[#07334F] transition cursor-pointer">
                            Review
                        </button>
                    </div>
                @endif

                <!-- Alert jika ada Setoran BSK menunggu disetor ke kasir -->
                @if($setoranPending->count() > 0)
                    <div class="p-3.5 rounded-2xl bg-sky-50 border border-sky-200 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-8 h-8 rounded-xl bg-[#0E527B] text-white font-black text-sm flex items-center justify-center shrink-0">
                                {{ $setoranPending->count() }}
                            </span>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-slate-900 truncate">BSK Menunggu Kasir</h4>
                                <p class="text-[11px] text-slate-600">Total Rp {{ number_format($totalPendingKasirHariIni, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <button type="button" 
                                @click="mobileTab = 'penagihan'; subFilter = 'setor_kasir'; window.scrollTo({top: 0, behavior: 'smooth'})"
                                class="px-3 py-1.5 rounded-xl bg-[#0E527B] text-white text-xs font-bold shrink-0 hover:bg-[#07334F] transition cursor-pointer">
                            Barcode Kasir
                        </button>
                    </div>
                @endif

                <!-- Tombol Utama: Mulai / Buka Penagihan Lapangan -->
                <button type="button" 
                        @click="mobileTab = 'penagihan'; subFilter = 'semua'; window.scrollTo({top: 0, behavior: 'smooth'})"
                        class="w-full h-12 rounded-2xl bg-[#0E527B] hover:bg-[#07334F] active:scale-98 text-white font-bold text-sm shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-5 h-5 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <span>Buka Alur Penagihan Lapangan</span>
                </button>
            </div>

        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- TAB 2: SATU ALUR BISNIS PENAGIHAN TERPADU (TUGAS -> TAGIHAN -> BSK)   -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div x-show="mobileTab === 'penagihan'" x-cloak class="space-y-3.5 pb-12">
            
            <!-- Pencarian Cepat -->
            <div class="bg-white border border-slate-200 rounded-2xl p-2.5 shadow-xs">
                <form method="GET" action="{{ route('keuangan.inkaso.mobile') }}" class="relative flex items-center">
                    <input type="hidden" name="tab" value="penagihan">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" 
                           name="q" 
                           value="{{ $q }}" 
                           placeholder="Cari No. Faktur atau Nama Pelanggan/Agen..." 
                           class="w-full pl-9 pr-8 py-2 rounded-xl text-xs bg-slate-50 border-none focus:ring-2 focus:ring-[#0E527B] text-slate-800 placeholder-slate-400">
                    @if($q)
                        <a href="{{ route('keuangan.inkaso.mobile', ['tab' => 'penagihan']) }}" class="absolute right-2.5 text-slate-400 hover:text-slate-600 text-xs font-bold">✕</a>
                    @endif
                </form>
            </div>

            <!-- Sub-Filter Alur Penagihan (1 Alur Terpadu, Tanpa Pecah-Pecah Menu) -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 no-scrollbar">
                <button type="button" 
                        @click="subFilter = 'semua'"
                        :class="subFilter === 'semua' ? 'bg-[#0E527B] text-white font-bold shadow-xs' : 'bg-white text-slate-600 border border-slate-200 font-semibold'"
                        class="px-3.5 py-2 rounded-xl text-xs shrink-0 transition cursor-pointer">
                    Semua Penagihan ({{ $suratTugasMenunggu->count() + $tagihanAktif->count() }})
                </button>

                <button type="button" 
                        @click="subFilter = 'tugas_baru'"
                        :class="subFilter === 'tugas_baru' ? 'bg-[#0E527B] text-white font-bold shadow-xs' : 'bg-white text-slate-600 border border-slate-200 font-semibold'"
                        class="px-3.5 py-2 rounded-xl text-xs shrink-0 transition cursor-pointer flex items-center gap-1.5">
                    <span>Surat Tugas</span>
                    @if($suratTugasMenunggu->count() > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-400 text-amber-950 font-black">
                            {{ $suratTugasMenunggu->count() }}
                        </span>
                    @endif
                </button>

                <button type="button" 
                        @click="subFilter = 'siap_ditagih'"
                        :class="subFilter === 'siap_ditagih' ? 'bg-[#0E527B] text-white font-bold shadow-xs' : 'bg-white text-slate-600 border border-slate-200 font-semibold'"
                        class="px-3.5 py-2 rounded-xl text-xs shrink-0 transition cursor-pointer">
                    <span>Tagihan Aktif ({{ $tagihanAktif->count() }})</span>
                </button>

                <button type="button" 
                        @click="subFilter = 'setor_kasir'"
                        :class="subFilter === 'setor_kasir' ? 'bg-[#0E527B] text-white font-bold shadow-xs' : 'bg-white text-slate-600 border border-slate-200 font-semibold'"
                        class="px-3.5 py-2 rounded-xl text-xs shrink-0 transition cursor-pointer flex items-center gap-1.5">
                    <span>Setoran BSK</span>
                    @if($setoranPending->count() > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-[#0E527B] text-white font-black">
                            {{ $setoranPending->count() }}
                        </span>
                    @endif
                </button>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- SECTION A: SURAT TUGAS BARU MENUNGGU KONFIRMASI                     -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div x-show="subFilter === 'semua' || subFilter === 'tugas_baru'" class="space-y-3">
                @if($suratTugasMenunggu->count() > 0)
                    <div class="flex items-center justify-between px-1 pt-1">
                        <span class="text-xs font-extrabold text-[#0E527B] uppercase tracking-wider">
                            Surat Tugas Baru ({{ $suratTugasMenunggu->count() }})
                        </span>
                        <span class="text-[11px] text-slate-500">Konfirmasi terima atau tolak</span>
                    </div>

                    @foreach($suratTugasMenunggu as $tugas)
                        <div class="bg-white border-2 border-[#0E527B]/30 rounded-2xl p-4 shadow-xs space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-[#0E527B] text-[11px] font-mono font-bold border border-slate-200">
                                            {{ $tugas->no_tugas }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-amber-100 text-amber-900">
                                            Tugas Baru Masuk
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
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Sisa Piutang</span>
                                    <span class="text-base font-black text-rose-600 block">
                                        Rp {{ number_format($tugas->faktur->sisa_piutang ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div class="bg-slate-50 rounded-xl p-2.5 text-xs text-slate-600 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">No. Faktur:</span>
                                    <span class="font-mono font-bold text-slate-800">{{ $tugas->faktur->no_faktur }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">Divisi:</span>
                                    <span class="font-semibold uppercase text-[#0E527B]">{{ $tugas->faktur->kategori ?? 'sirkulasi' }}</span>
                                </div>
                                @if($tugas->catatan)
                                    <div class="pt-1 border-t border-slate-200/70 text-[11px] text-slate-500 italic">
                                        Catatan: "{{ $tugas->catatan }}"
                                    </div>
                                @endif
                            </div>

                            <!-- Tombol Aksi Konfirmasi Fleksibel Per-Faktur -->
                            <div class="grid grid-cols-2 gap-2 pt-1">
                                <button type="button" 
                                        onclick="bukaModalTolak('{{ $tugas->id }}', '{{ $tugas->faktur->no_faktur }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '-') }}')"
                                        class="w-full h-11 py-2 px-3 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold flex items-center justify-center gap-1.5 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Tolak Faktur Ini</span>
                                </button>

                                <form action="{{ route('keuangan.inkaso.penugasan.konfirmasi', $tugas->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="aksi" value="terima">
                                    <button type="submit" 
                                            class="w-full h-11 py-2 px-3 rounded-xl bg-[#0E527B] hover:bg-[#0A3D5C] text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition cursor-pointer">
                                        <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Terima Tugas</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div x-show="subFilter === 'tugas_baru'" class="bg-white border border-slate-200 rounded-2xl p-8 text-center shadow-xs">
                        <h4 class="text-sm font-bold text-slate-800">Tidak Ada Surat Tugas Baru</h4>
                        <p class="text-xs text-slate-500 mt-1">Semua surat tugas sudah Anda konfirmasi.</p>
                    </div>
                @endif
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- SECTION B: DAFTAR TAGIHAN AKTIF LAPANGAN & KARTU STATUS SETORAN     -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div x-show="subFilter === 'semua' || subFilter === 'siap_ditagih'" class="space-y-3">
                <div class="flex items-center justify-between px-1 pt-1">
                    <span class="text-xs font-extrabold text-[#0E527B] uppercase tracking-wider">
                        Daftar Tagihan Berjalan ({{ $tagihanAktif->count() }})
                    </span>
                    <span class="text-[11px] text-slate-400">Pilih faktur untuk penagihan</span>
                </div>

                @forelse($tagihanAktif as $tugas)
                    @php
                        $pendingBsk = $pendingBskByFaktur->get($tugas->faktur_id);
                    @endphp
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs space-y-3 transition hover:border-[#0E527B]/40">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-[#0E527B] text-[11px] font-mono font-bold border border-slate-200">
                                        {{ $tugas->faktur->no_faktur }}
                                    </span>
                                    
                                    @if($pendingBsk)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-amber-100 text-amber-900 border border-amber-300">
                                            ⌛ Menunggu Kasir
                                        </span>
                                    @elseif($tugas->faktur->total_terbayar > 0)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-sky-100 text-[#0E527B]">
                                            Cicilan
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                            Belum Bayar
                                        </span>
                                    @endif
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

                        <!-- KONDISI JIKA SUDAH ADA BSK PENDING (CEGAH DOUBLE INPUT SECARA VISUAL) -->
                        @if($pendingBsk)
                            <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200 text-xs space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-amber-900 font-semibold">Telah Ditagih:</span>
                                    <span class="font-mono font-black text-amber-950 text-sm">Rp {{ number_format($pendingBsk->nominal_titipan, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-600">
                                    <span>No. BSK: <strong class="font-mono">{{ $pendingBsk->no_bsk }}</strong></span>
                                    <span>{{ $pendingBsk->waktu_terima_lapangan ? $pendingBsk->waktu_terima_lapangan->format('H:i') : '' }} WIB</span>
                                </div>
                            </div>

                            <!-- Tombol Langsung Tampilkan Barcode ke Kasir -->
                            <div class="pt-1">
                                <a href="{{ route('keuangan.inkaso.bsk.show', $pendingBsk->no_bsk) }}" 
                                   class="w-full h-11 px-4 rounded-xl bg-[#0E527B] hover:bg-[#07334F] text-white text-xs sm:text-sm font-bold flex items-center justify-center gap-2 shadow-xs transition cursor-pointer">
                                    <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    <span>Tampilkan Barcode BSK ke Kasir</span>
                                </a>
                            </div>
                        @else
                            <!-- Tombol Cepat Hubungi & Tombol Input Pembayaran -->
                            <div class="flex items-center gap-2 pt-1 border-t border-slate-100">
                                @if(!empty($tugas->faktur->customer->no_telepon))
                                    <a href="tel:{{ $tugas->faktur->customer->no_telepon }}" 
                                       class="h-11 px-3.5 rounded-xl border border-slate-200 text-slate-700 hover:text-[#0E527B] hover:bg-slate-50 text-xs font-bold flex items-center justify-center gap-1.5 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span>Hubungi</span>
                                    </a>
                                @endif

                                <button type="button" 
                                        onclick="bukaModalBayar('{{ $tugas->faktur_id }}', '{{ $tugas->id }}', '{{ $tugas->faktur->no_faktur }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '-') }}', '{{ (float) $tugas->faktur->sisa_piutang }}')"
                                        class="flex-1 h-11 px-4 rounded-xl bg-[#0E527B] hover:bg-[#07334F] text-white text-xs sm:text-sm font-bold flex items-center justify-center gap-2 shadow-xs transition cursor-pointer">
                                    <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span>Input Pembayaran</span>
                                </button>
                            </div>
                        @endif
                    </div>
                @empty
                    <div x-show="subFilter === 'siap_ditagih' || subFilter === 'semua'" class="bg-white border border-slate-200 rounded-2xl p-8 text-center shadow-xs">
                        <h4 class="text-sm font-bold text-slate-800">Belum Ada Tagihan Aktif</h4>
                        <p class="text-xs text-slate-500 mt-1">Cek Surat Tugas untuk menerima penugasan baru.</p>
                    </div>
                @endforelse
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- SECTION C: REKAP SETORAN BSK SIAP KASIR (LOKET SORE HARI)           -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div x-show="subFilter === 'semua' || subFilter === 'setor_kasir'" class="space-y-3 pt-2">
                @if($setoranPending->count() > 0)
                    <div class="flex items-center justify-between px-1">
                        <div>
                            <span class="text-xs font-extrabold text-[#0E527B] uppercase tracking-wider block">
                                Setoran BSK Menunggu Kasir ({{ $setoranPending->count() }})
                            </span>
                            <span class="text-[11px] text-slate-500">Tunjukkan barcode di Loket Kasir Redaksi</span>
                        </div>
                    </div>

                    <!-- Banner SOP Kasir Bersih 60-30-10 -->
                    <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-950 space-y-1">
                        <span class="font-bold flex items-center gap-1.5 text-amber-900">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            SOP Serah Terima Kasir:
                        </span>
                        <p class="text-[11px] text-slate-700 leading-relaxed">
                            Saldo piutang penjualan di sistem utama <strong>belum dipotong</strong>. Tunjukkan Barcode BSK ini beserta fisik uang ke Kasir. Setelah Kasir meng-approve, saldo piutang resmi berkurang.
                        </p>
                    </div>

                    @foreach($setoranPending as $setoran)
                        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 font-mono text-[11px] font-bold text-slate-800">
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
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300 mt-1">
                                        ⌛ Menunggu Kasir
                                    </span>
                                </div>
                            </div>

                            <div class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                <strong>Keterangan:</strong> {{ $setoran->keterangan_angsuran }}
                            </div>

                            <!-- Tombol Buka Barcode -->
                            <div class="pt-1">
                                <a href="{{ route('keuangan.inkaso.bsk.show', $setoran->no_bsk) }}" 
                                   class="w-full h-11 px-4 rounded-xl bg-[#0E527B] hover:bg-[#07334F] text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition cursor-pointer">
                                    <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    <span>Tampilkan Barcode ke Kasir</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div x-show="subFilter === 'setor_kasir'" class="bg-white border border-slate-200 rounded-2xl p-8 text-center shadow-xs">
                        <h4 class="text-sm font-bold text-slate-800">Tidak Ada Setoran Menunggu</h4>
                        <p class="text-xs text-slate-500 mt-1">Semua titipan penagihan sudah disahkan kasir.</p>
                    </div>
                @endif
            </div>

        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- TAB 3: RIWAYAT PEMBAYARAN SAH (DISAHKAN KASIR BKM)                     -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div x-show="mobileTab === 'riwayat'" x-cloak class="space-y-3 pb-12">
            <div class="flex items-center justify-between px-1">
                <div>
                    <span class="text-xs font-extrabold text-[#0E527B] uppercase tracking-wider block">
                        Riwayat Pembayaran Sah ({{ $riwayatSetoranSah->count() }})
                    </span>
                    <span class="text-[11px] text-slate-500">Arsip kuitansi BKM kasir resmi</span>
                </div>
            </div>

            @forelse($riwayatSetoranSah as $setoran)
                <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-[#0E527B] font-mono text-[11px] font-bold border border-slate-200">
                                {{ $setoran->no_bsk }}
                            </span>
                            <h3 class="text-sm font-extrabold text-slate-900 mt-1">
                                {{ $setoran->nama_pembayar }}
                            </h3>
                            <p class="text-[11px] text-slate-500">
                                Faktur: <span class="font-mono font-semibold">{{ $setoran->faktur->no_faktur ?? '-' }}</span> &bull; {{ $setoran->waktu_terima_lapangan ? $setoran->waktu_terima_lapangan->format('d/m/Y') : '' }}
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="text-sm font-black text-[#0E527B] block">
                                Rp {{ number_format($setoran->nominal_titipan, 0, ',', '.') }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#0E527B] text-white mt-1">
                                ✓ Disetujui Kasir
                            </span>
                        </div>
                    </div>

                    <div class="text-xs text-slate-700 bg-slate-50 p-2.5 rounded-xl border border-slate-200 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-semibold">No. Kuitansi BKM:</span>
                            <span class="font-mono font-bold text-[#0E527B]">{{ $setoran->no_kuitansi_kasir ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Disahkan oleh:</span>
                            <span class="font-medium text-slate-800">{{ $setoran->kasir->name ?? 'Kasir Redaksi' }} ({{ $setoran->approved_at ? $setoran->approved_at->format('H:i') : '' }} WIB)</span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-slate-200/80">
                            <span class="text-slate-500">Sisa Piutang Sekarang:</span>
                            <span class="font-mono font-bold text-slate-900">
                                Rp {{ number_format($setoran->faktur->sisa_piutang ?? 0, 0, ',', '.') }}
                                <span class="text-[10px] uppercase font-bold {{ ($setoran->faktur->sisa_piutang ?? 0) <= 0 ? 'text-[#0E527B]' : 'text-amber-800' }}">
                                    ({{ ($setoran->faktur->sisa_piutang ?? 0) <= 0 ? 'Lunas' : 'Cicilan' }})
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="pt-0.5">
                        <a href="{{ route('keuangan.inkaso.bsk.show', $setoran->no_bsk) }}" 
                           class="w-full py-2.5 px-3 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold flex items-center justify-center gap-1.5 transition cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Lihat Bukti Tanda Terima BSK</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center shadow-xs">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800">Belum Ada Riwayat Pembayaran Sah</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">
                        Setelah kasir meng-approve setoran lapangan Anda di loket sore hari, tanda terima sah akan tersimpan di sini.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- TAB 4: PROFIL PETUGAS INKASO (VIEW-ONLY ELEGAN & MODAL EDIT)           -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div x-show="mobileTab === 'profil'" x-cloak class="space-y-4 pb-12">
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs text-center space-y-4">
                
                <!-- Avatar Bulat WhatsApp Style -->
                <div class="relative w-24 h-24 mx-auto">
                    @if($user->foto_url)
                        <img src="{{ $user->foto_url }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover border-4 border-[#0E527B] shadow-md">
                    @else
                        <!-- WhatsApp Style Default Avatar: Bulat Abu-abu dengan siluet kepala-bahu putih/abu -->
                        <div class="w-24 h-24 rounded-full bg-[#CFD8DC] flex items-center justify-center border-4 border-[#0E527B] shadow-md overflow-hidden">
                            <svg class="w-20 h-20 text-[#90A4AE] translate-y-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                            </svg>
                        </div>
                    @endif
                </div>

                <div>
                    <h3 class="text-xl font-extrabold text-slate-900 leading-tight">{{ $user->name }}</h3>
                    <div class="flex items-center justify-center gap-1.5 mt-1.5 flex-wrap">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black bg-[#FFB900] text-[#07334F] uppercase tracking-wider shadow-2xs">
                            Petugas Inkaso Resmi
                        </span>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                            ID: INK-PR-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                </div>

                <!-- Informasi Detail Profil Staf -->
                <div class="text-left bg-slate-50 border border-slate-200/80 rounded-2xl p-4 space-y-3 text-xs">
                    <div class="flex items-center justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500 font-medium">Nama Lengkap Petugas</span>
                        <span class="font-extrabold text-slate-900 text-sm">{{ $user->name }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500 font-medium">Nomor HP / WhatsApp</span>
                        <span class="font-extrabold text-slate-900 text-sm font-mono">{{ $user->telepon ?? 'Belum Diatur' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500 font-medium">Email Akun Staf</span>
                        <span class="font-bold text-slate-800 text-xs font-mono">{{ $user->email }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500 font-medium">Wilayah Operasional</span>
                        <span class="font-bold text-slate-800 text-xs">Priangan Timur</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500 font-medium">Status Tugas</span>
                        <span class="font-bold text-[#0E527B] text-xs flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-[#0E527B]"></span>
                            <span>Kolektor Lapangan</span>
                        </span>
                    </div>
                </div>

                <!-- Kebijakan Keamanan Akun Terpadu Korporat (Dilarang Ganti Password Mandiri) -->
                <div class="p-3.5 rounded-2xl bg-sky-50/70 border border-sky-200 text-left text-xs space-y-1">
                    <div class="flex items-center gap-1.5 font-extrabold text-[#0E527B]">
                        <svg class="w-4 h-4 text-[#0E527B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Kebijakan Keamanan Akun Korporat</span>
                    </div>
                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        Sesuai SOP keamanan &amp; audit penagihan, kata sandi dikelola terpusat oleh <strong>IT Administrator Sistem</strong>. Hubungi administrator jika memerlukan reset kata sandi akun.
                    </p>
                </div>

                <!-- Tombol Aksi Utama -->
                <div class="space-y-2.5 pt-2">
                    <button type="button" 
                            @click="editProfileModal = true"
                            class="w-full h-12 rounded-2xl bg-[#0E527B] hover:bg-[#07334F] text-white text-sm font-bold flex items-center justify-center gap-2 shadow-md transition cursor-pointer active:scale-98">
                        <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        <span>Ubah Profil &amp; Foto</span>
                    </button>

                    <button type="button" 
                            @click="showLogoutModal = true" 
                            class="w-full h-11 rounded-2xl border border-slate-300 hover:border-rose-300 hover:bg-rose-50 text-slate-700 hover:text-rose-700 text-xs font-extrabold flex items-center justify-center gap-2 transition cursor-pointer">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Keluar dari Akun Inkaso</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- BOTTOM NAVIGATION BAR (ELEGAN, SIMPEL, MEWAH 60-30-10)                 -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <nav class="bg-white/95 backdrop-blur-md border-t border-slate-200 fixed bottom-0 left-0 right-0 z-40 shadow-[0_-8px_30px_rgba(14,82,123,0.08)] py-2.5 px-3 sm:px-6">
            <div class="max-w-lg mx-auto flex items-center justify-between">
                
                {{-- 1. Beranda / Dashboard --}}
                <button type="button" 
                        @click="mobileTab = 'beranda'; window.scrollTo({top: 0, behavior: 'smooth'})" 
                        class="flex flex-col items-center group transition flex-1 py-1 cursor-pointer">
                    <div :class="mobileTab === 'beranda' ? 'w-12 h-8 sm:w-14 sm:h-9 rounded-xl bg-[#0E527B] text-[#FFB900] shadow-sm' : 'w-12 h-8 sm:w-14 sm:h-9 rounded-xl text-slate-400 group-hover:text-[#0E527B] group-hover:bg-slate-100'"
                         class="flex items-center justify-center transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <span :class="mobileTab === 'beranda' ? 'text-[11px] font-black text-[#0E527B]' : 'text-[11px] font-semibold text-slate-500 group-hover:text-slate-700'" 
                          class="mt-1 tracking-tight truncate">Beranda</span>
                </button>

                {{-- 2. Penagihan (1 Alur Terpadu) --}}
                <button type="button" 
                        @click="mobileTab = 'penagihan'; subFilter = 'semua'; window.scrollTo({top: 0, behavior: 'smooth'})" 
                        class="flex flex-col items-center group transition flex-1 py-1 relative cursor-pointer">
                    @if(($suratTugasMenunggu->count() + $tagihanAktif->count()) > 0)
                        <span class="absolute top-0 right-3 min-w-[18px] h-4.5 px-1 bg-[#0E527B] rounded-full text-[10px] text-white font-black flex items-center justify-center shadow-xs ring-2 ring-white z-10">
                            {{ $suratTugasMenunggu->count() + $tagihanAktif->count() }}
                        </span>
                    @endif
                    <div :class="mobileTab === 'penagihan' ? 'w-12 h-8 sm:w-14 sm:h-9 rounded-xl bg-[#0E527B] text-[#FFB900] shadow-sm' : 'w-12 h-8 sm:w-14 sm:h-9 rounded-xl text-slate-400 group-hover:text-[#0E527B] group-hover:bg-slate-100'"
                         class="flex items-center justify-center transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <span :class="mobileTab === 'penagihan' ? 'text-[11px] font-black text-[#0E527B]' : 'text-[11px] font-semibold text-slate-500 group-hover:text-slate-700'" 
                          class="mt-1 tracking-tight truncate">Penagihan</span>
                </button>

                {{-- 3. Riwayat Pembayaran Sah --}}
                <button type="button" 
                        @click="mobileTab = 'riwayat'; window.scrollTo({top: 0, behavior: 'smooth'})" 
                        class="flex flex-col items-center group transition flex-1 py-1 relative cursor-pointer">
                    <div :class="mobileTab === 'riwayat' ? 'w-12 h-8 sm:w-14 sm:h-9 rounded-xl bg-[#0E527B] text-[#FFB900] shadow-sm' : 'w-12 h-8 sm:w-14 sm:h-9 rounded-xl text-slate-400 group-hover:text-[#0E527B] group-hover:bg-slate-100'"
                         class="flex items-center justify-center transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span :class="mobileTab === 'riwayat' ? 'text-[11px] font-black text-[#0E527B]' : 'text-[11px] font-semibold text-slate-500 group-hover:text-slate-700'" 
                          class="mt-1 tracking-tight truncate">Riwayat</span>
                </button>

                {{-- 4. Profil Petugas --}}
                <button type="button" 
                        @click="mobileTab = 'profil'; window.scrollTo({top: 0, behavior: 'smooth'})" 
                        class="flex flex-col items-center group transition flex-1 py-1 relative cursor-pointer">
                    <div :class="mobileTab === 'profil' ? 'w-12 h-8 sm:w-14 sm:h-9 rounded-xl bg-[#0E527B] text-[#FFB900] shadow-sm' : 'w-12 h-8 sm:w-14 sm:h-9 rounded-xl text-slate-400 group-hover:text-[#0E527B] group-hover:bg-slate-100'"
                         class="flex items-center justify-center transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span :class="mobileTab === 'profil' ? 'text-[11px] font-black text-[#0E527B]' : 'text-[11px] font-semibold text-slate-500 group-hover:text-slate-700'" 
                          class="mt-1 tracking-tight truncate">Profil</span>
                </button>
            </div>
        </nav>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- MODAL 1: INPUT PEMBAYARAN (PROTEKSI DOUBLE-CLICK & CEGAH MULTI BSK)     -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div id="modalBayar" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 hidden">
            <div class="bg-white rounded-3xl max-w-md w-full p-5 shadow-2xl border border-slate-200 space-y-4 animate-in fade-in zoom-in duration-150">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-extrabold text-[#0E527B] uppercase tracking-tight">
                            Input Pembayaran Lapangan
                        </h3>
                        <p class="text-[11px] text-slate-500">Terbitkan Barcode Setoran Koleksi (BSK) Sementara</p>
                    </div>
                    <button type="button" onclick="tutupModalBayar()" class="text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('keuangan.inkaso.setoran-lapangan.store') }}" 
                      method="POST" 
                      onsubmit="const btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.classList.add('opacity-75'); btn.innerHTML = '<span>Menyimpan BSK...</span>';"
                      class="space-y-3.5">
                    @csrf
                    <input type="hidden" name="faktur_id" id="bayarFakturId">
                    <input type="hidden" name="penugasan_id" id="bayarPenugasanId">

                    <!-- Info Ringkas Faktur -->
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3 space-y-1 text-xs">
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
                            <button type="button" onclick="isiBayarLunas()" class="text-[11px] font-bold text-[#0E527B] hover:underline cursor-pointer">
                                Bayar Lunas Penuh
                            </button>
                        </div>
                        <input type="number" 
                               name="nominal_bayar" 
                               id="inputNominalBayar" 
                               required 
                               min="1000" 
                               step="500" 
                               placeholder="Contoh: 1000000" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#0E527B] text-sm font-black text-slate-900 bg-white shadow-2xs">
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
                                class="w-full h-12 rounded-xl bg-[#0E527B] hover:bg-[#07334F] text-white text-xs sm:text-sm font-bold flex items-center justify-center gap-2 shadow-md transition cursor-pointer active:scale-98">
                            <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <span>Simpan &amp; Terbitkan Barcode Setoran</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- MODAL 2: TOLAK FAKTUR SPESIFIK (JIKA BEDA ARAH / RUTE)                  -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div id="modalTolak" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 hidden">
            <div class="bg-white rounded-3xl max-w-md w-full p-5 shadow-2xl border border-slate-200 space-y-4 animate-in fade-in zoom-in duration-150">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-extrabold text-rose-950 uppercase tracking-tight">
                            Tolak Penagihan Faktur Ini
                        </h3>
                        <p class="text-[11px] text-slate-500">Faktur ini akan dikembalikan ke meja Admin Faktur</p>
                    </div>
                    <button type="button" onclick="tutupModalTolak()" class="text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">✕</button>
                </div>

                <form id="formTolakTugas" method="POST" class="space-y-3.5">
                    @csrf
                    <input type="hidden" name="aksi" value="tolak">

                    <p class="text-xs text-slate-600">
                        Anda menolak menagih faktur <strong id="tolakFakturNo" class="text-slate-900 font-mono">-</strong> untuk pelanggan <strong id="tolakCustomer" class="text-slate-900">-</strong>. Faktur lainnya di surat tugas ini tetap berjalan.
                    </p>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Alasan Penolakan <span class="text-rose-500">*</span>
                        </label>
                        <select onchange="pilihPresetAlasan(this.value)" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 mb-2">
                            <option value="">-- Pilih Alasan Cepat atau Ketik di Bawah --</option>
                            <option value="Beda arah rute penagihan hari ini">Beda arah rute penagihan hari ini</option>
                            <option value="Lokasi toko/agen tutup permanen">Lokasi toko/agen tutup permanen</option>
                            <option value="Pelanggan meminta jadwal ulang minggu depan">Pelanggan meminta jadwal ulang minggu depan</option>
                            <option value="Luar jangkauan area penagihan petugas">Luar jangkauan area penagihan petugas</option>
                        </select>
                        <textarea name="alasan_penolakan" 
                                  id="inputAlasanTolak"
                                  required 
                                  rows="3" 
                                  placeholder="Contoh: Beda arah rute (hari ini ke Singaparna, toko berada di Ciawi)..." 
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 text-xs text-slate-800"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <button type="button" onclick="tutupModalTolak()" class="py-2.5 px-3 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="py-2.5 px-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                            Konfirmasi Tolak Faktur
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- MODAL 3: UBAH PROFIL & UPLOAD FOTO (HANYA TERBUKA SAAT DIKLIK)          -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div x-cloak x-show="editProfileModal" 
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl p-5 sm:p-6 border border-slate-200 space-y-4 max-h-[90vh] overflow-y-auto"
                 @click.away="editProfileModal = false">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-extrabold text-[#0E527B]">Ubah Profil Petugas</h3>
                        <p class="text-xs text-slate-500">Perbarui nama, nomor kontak, dan foto resmi</p>
                    </div>
                    <button type="button" @click="editProfileModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold p-1 cursor-pointer">✕</button>
                </div>

                <form action="{{ route('keuangan.inkaso.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="text-center">
                        <div class="relative w-24 h-24 mx-auto">
                            <template x-if="photoPreview">
                                <img :src="photoPreview" class="w-24 h-24 rounded-full object-cover border-4 border-[#0E527B] shadow-md">
                            </template>
                            <template x-if="!photoPreview">
                                <div>
                                    @if($user->foto_url)
                                        <img src="{{ $user->foto_url }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover border-4 border-[#0E527B] shadow-md">
                                    @else
                                        <div class="w-24 h-24 rounded-full bg-[#CFD8DC] flex items-center justify-center border-4 border-[#0E527B] shadow-md overflow-hidden relative">
                                            <svg class="w-20 h-20 text-[#90A4AE] translate-y-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </template>

                            <label for="inputFotoProfil" 
                                   class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-[#FFB900] hover:bg-amber-400 text-[#07334F] flex items-center justify-center shadow-md border-2 border-white cursor-pointer transition transform active:scale-95" 
                                   title="Pilih Foto Baru">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </label>
                            <input type="file" 
                                   name="foto" 
                                   id="inputFotoProfil" 
                                   accept="image/png,image/jpeg,image/jpg,image/webp" 
                                   class="hidden" 
                                   @change="previewFoto($event)">
                        </div>

                        <p class="text-[11px] text-slate-500 mt-2">Klik tombol kamera untuk memilih foto galeri atau kamera HP</p>

                        @if($user->foto)
                            <div class="mt-2">
                                <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 hover:text-rose-700 cursor-pointer font-bold">
                                    <input type="checkbox" name="hapus_foto" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                    <span>Hapus Foto Saat Ini (Kembali ke Default WA)</span>
                                </label>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Nama Lengkap Petugas <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name', $user->name) }}" 
                               required 
                               placeholder="Nama lengkap petugas..." 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#0E527B] text-xs font-bold text-slate-900 bg-white shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Nomor HP / WhatsApp Resmi
                        </label>
                        <input type="text" 
                               name="telepon" 
                               value="{{ old('telepon', $user->telepon) }}" 
                               placeholder="Contoh: 081234567890" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#0E527B] text-xs font-bold text-slate-900 bg-white shadow-2xs">
                        <p class="text-[10px] text-slate-400 mt-1">Digunakan untuk koordinasi operasional bersama Admin Faktur &amp; Kasir.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                        <button type="button" 
                                @click="editProfileModal = false" 
                                class="h-11 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="h-11 rounded-xl bg-[#0E527B] hover:bg-[#07334F] text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-md transition cursor-pointer active:scale-98">
                            <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan</span>
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
            document.getElementById('inputAlasanTolak').value = '';
            const form = document.getElementById('formTolakTugas');
            form.action = "{{ url('keuangan/inkaso/penugasan') }}/" + tugasId + "/konfirmasi";
            document.getElementById('modalTolak').classList.remove('hidden');
        }

        function pilihPresetAlasan(val) {
            if (val) {
                document.getElementById('inputAlasanTolak').value = val;
            }
        }

        function tutupModalTolak() {
            document.getElementById('modalTolak').classList.add('hidden');
        }

        function previewFoto(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const el = document.querySelector('[x-data]');
                    if (el && window.Alpine) {
                        window.Alpine.$data(el).photoPreview = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-mobile-inkaso-layout>
