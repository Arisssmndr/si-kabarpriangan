<x-mobile-inkaso-layout>
    <div x-data="{ 
            mobileTab: '{{ in_array(request('tab'), ['tagihan', 'surat_tugas', 'setoran_bsk']) ? 'penagihan' : request('tab', 'beranda') }}', 
            subFilter: '{{ request('tab') === 'surat_tugas' ? 'tugas_baru' : (request('tab') === 'setoran_bsk' ? 'setor_kasir' : 'semua') }}',
            photoPreview: null, 
            editProfileModal: false 
         }" 
         class="space-y-4">

        <!-- Notifikasi Pesan Sistem (Tanpa Emoji, Menggunakan SVG Heroicons) -->
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
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-bold flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- TAB 1: BERANDA / DASHBOARD UTAMA (SESUAI DESAIN REFERENSI UI/UX)       -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div x-show="mobileTab === 'beranda'" x-cloak class="space-y-4">
            
            <!-- 1. HERO FEATURE CARD: TARGET KOLEKSI PIUTANG (GRADIENT MESH ALA REFERENSI GAMBAR) -->
            <div class="relative overflow-hidden rounded-[2rem] p-6 text-white shadow-xl border border-white/20"
                 style="background: linear-gradient(135deg, #0E527B 0%, #0A72AC 45%, #D97706 125%) !important;">
                <!-- Ambient overlay glow -->
                <div class="absolute -right-8 -bottom-8 w-48 h-48 rounded-full bg-amber-400/20 blur-2xl pointer-events-none"></div>

                <!-- Top Row: Glass Icon Badge + Circular White Action Button -->
                <div class="flex items-center justify-between relative z-10">
                    <div class="w-11 h-11 rounded-full bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    
                    <button type="button" 
                            @click="mobileTab = 'penagihan'; subFilter = 'semua'; window.scrollTo({top: 0, behavior: 'smooth'})"
                            class="w-10 h-10 rounded-full bg-white text-slate-900 shadow-md flex items-center justify-center hover:scale-105 active:scale-95 transition cursor-pointer"
                            title="Buka Daftar Penagihan">
                        <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H7M17 7V17"/>
                        </svg>
                    </button>
                </div>

                <!-- Center: Label & Primary Metric -->
                <div class="mt-5 relative z-10">
                    <span class="text-xs font-semibold text-white/85 tracking-wide uppercase">
                        Target Piutang Hari Ini
                    </span>
                    <div class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-1">
                        Rp {{ number_format($totalTargetPiutang, 0, ',', '.') }}
                    </div>
                </div>

                <!-- Bottom Row: Status Text & Sleek Indicator Dots Ala Gambar -->
                <div class="mt-5 flex items-center justify-between relative z-10 pt-3 border-t border-white/15">
                    <span class="text-xs font-semibold text-white/90">
                        {{ $tagihanAktif->count() }} Faktur Ditugaskan
                    </span>
                    <!-- Progress Dots Chain -->
                    <div class="flex items-center gap-1.5" title="Progres Penugasan">
                        <span class="w-2 h-2 rounded-full bg-white/40"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-white/60"></span>
                        <span class="w-3 h-3 rounded-full bg-white shadow-xs"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-white shadow-xs"></span>
                        <span class="w-4 h-4 rounded-full bg-white shadow-xs flex items-center justify-center">
                            <span class="w-2 h-2 rounded-full bg-[#0E527B]"></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- 3. 2x2 GRID METRIC CARDS (PERSIS DENGAN REFERENSI GAMBAR) -->
            <div class="grid grid-cols-2 gap-3.5">
                
                <!-- CARD 1: SURAT TUGAS (DENGAN MINI BAR CHART VISUAL) -->
                <div @click="mobileTab = 'penagihan'; subFilter = 'tugas_baru'; window.scrollTo({top: 0, behavior: 'smooth'})"
                     class="bg-white rounded-[1.75rem] p-4 shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition cursor-pointer active:scale-98">
                    <div class="flex items-center justify-between">
                        <div class="w-9 h-9 rounded-full bg-sky-50 border border-sky-100 flex items-center justify-center text-[#0E527B] shrink-0">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex items-center gap-0.5 text-slate-400">
                            <span class="text-[11px] font-bold text-slate-700">Surat Tugas</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-end justify-between">
                        <div>
                            <span class="text-2xl font-black text-slate-900 leading-none">{{ $suratTugasMenunggu->count() }}</span>
                            <span class="text-[10px] text-slate-500 font-semibold ml-1">Tugas</span>
                        </div>
                        <!-- Mini bar chart icon matching reference image Card 1 -->
                        <div class="flex items-end gap-1 h-5 pb-0.5">
                            <span class="w-1 h-2 bg-[#0E527B]/30 rounded-full"></span>
                            <span class="w-1 h-3.5 bg-[#0E527B]/60 rounded-full"></span>
                            <span class="w-1 h-5 bg-[#0E527B] rounded-full"></span>
                            <span class="w-1 h-3 bg-[#0E527B] rounded-full"></span>
                            <span class="w-1 h-4 bg-[#0E527B] rounded-full"></span>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: TAGIHAN AKTIF (DENGAN MINI SPARKLINE WAVE VISUAL) -->
                <div @click="mobileTab = 'penagihan'; subFilter = 'siap_ditagih'; window.scrollTo({top: 0, behavior: 'smooth'})"
                     class="bg-white rounded-[1.75rem] p-4 shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition cursor-pointer active:scale-98">
                    <div class="flex items-center justify-between">
                        <div class="w-9 h-9 rounded-full bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex items-center gap-0.5 text-slate-400">
                            <span class="text-[11px] font-bold text-slate-700">Tagihan Aktif</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-end justify-between">
                        <div>
                            <span class="text-2xl font-black text-slate-900 leading-none">{{ $tagihanAktif->count() }}</span>
                            <span class="text-[10px] text-slate-500 font-semibold ml-1">Faktur</span>
                        </div>
                        <!-- Mini sparkline wave matching reference image Card 2 -->
                        <svg class="w-12 h-6 text-amber-500" viewBox="0 0 50 20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                            <path d="M2 14 Q 10 4, 18 10 T 34 5 T 48 12"/>
                        </svg>
                    </div>
                </div>

                <!-- CARD 3: SETORAN BSK (DENGAN MINI STATUS CURVE VISUAL) -->
                <div @click="mobileTab = 'penagihan'; subFilter = 'setor_kasir'; window.scrollTo({top: 0, behavior: 'smooth'})"
                     class="bg-white rounded-[1.75rem] p-4 shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition cursor-pointer active:scale-98">
                    <div class="flex items-center justify-between">
                        <div class="w-9 h-9 rounded-full bg-sky-50 border border-sky-100 flex items-center justify-center text-[#0E527B] shrink-0">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <div class="flex items-center gap-0.5 text-slate-400">
                            <span class="text-[11px] font-bold text-slate-700">Setoran BSK</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-end justify-between">
                        <div>
                            <span class="text-2xl font-black text-slate-900 leading-none">{{ $setoranPending->count() }}</span>
                            <span class="text-[10px] text-slate-500 font-semibold ml-1">BSK</span>
                        </div>
                        <!-- Mini wave status line matching reference image Card 3 -->
                        <svg class="w-12 h-5 text-[#0E527B]" viewBox="0 0 50 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                            <path d="M2 10 C 8 10, 10 3, 16 3 C 22 3, 24 10, 30 10 C 36 10, 38 3, 44 3"/>
                        </svg>
                    </div>
                </div>

                <!-- CARD 4: SETORAN KASIR (DENGAN MINI DOT MATRIX VISUAL) -->
                <div @click="mobileTab = 'riwayat'; window.scrollTo({top: 0, behavior: 'smooth'})"
                     class="bg-white rounded-[1.75rem] p-4 shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition cursor-pointer active:scale-98">
                    <div class="flex items-center justify-between">
                        <div class="w-9 h-9 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex items-center gap-0.5 text-slate-400">
                            <span class="text-[11px] font-bold text-slate-700">Setoran Kasir</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-end justify-between">
                        <div>
                            <span class="text-base sm:text-lg font-black text-slate-900 leading-none">Rp {{ number_format($totalTerkumpulHariIni, 0, ',', '.') }}</span>
                            <span class="text-[10px] text-emerald-600 font-bold block mt-0.5">Lunas Kasir</span>
                        </div>
                        <!-- Mini dot matrix matching reference image Card 4 -->
                        <div class="grid grid-cols-4 gap-1 pb-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. WIDE HORIZONTAL PILL CARD (PERSIS DENGAN KARTU DEROGATORY MARKS DI GAMBAR) -->
            <div class="bg-white rounded-3xl p-4 shadow-sm border border-slate-100 flex items-center justify-between gap-3">
                <div class="w-11 h-11 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-[#0E527B] shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-xs font-bold text-slate-900 tracking-tight">SOP Serah Terima Kasir</h4>
                    <p class="text-[11px] text-slate-500 truncate">Tunjukkan barcode BSK & uang fisik ke kasir loket</p>
                </div>
                <button type="button" 
                        @click="mobileTab = 'penagihan'; subFilter = 'setor_kasir'; window.scrollTo({top: 0, behavior: 'smooth'})"
                        class="w-11 h-11 rounded-full bg-slate-900 hover:bg-[#0E527B] active:scale-95 text-white flex items-center justify-center shadow-md transition cursor-pointer shrink-0"
                        title="Buka Barcode Kasir">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H7M17 7V17"/>
                    </svg>
                </button>
            </div>

            <!-- Spacer Bawah agar konten tidak tertutup floating dock -->
            <div class="h-44 w-full shrink-0 pointer-events-none" style="height: 180px; min-height: 180px;" aria-hidden="true"></div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- TAB 2: SATU ALUR BISNIS PENAGIHAN TERPADU (TUGAS -> TAGIHAN -> BSK)   -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div x-show="mobileTab === 'penagihan'" x-cloak class="space-y-4 pb-12">
            
            <!-- Pencarian Cepat (Sleek Modern Pill Style) -->
            <div class="bg-white rounded-full p-2 shadow-xs border border-slate-200/80">
                <form method="GET" action="{{ route('keuangan.inkaso.mobile') }}" class="relative flex items-center">
                    <input type="hidden" name="tab" value="penagihan">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" 
                           name="q" 
                           value="{{ $q }}" 
                           placeholder="Cari nomor faktur atau nama pelanggan..." 
                           class="w-full pl-10 pr-9 py-2 rounded-full text-xs bg-slate-50 border-none focus:ring-2 focus:ring-[#0E527B] text-slate-800 placeholder-slate-400">
                    @if($q)
                        <a href="{{ route('keuangan.inkaso.mobile', ['tab' => 'penagihan']) }}" class="absolute right-3 text-slate-400 hover:text-slate-600 p-1" title="Hapus Pencarian">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Sub-Filter Alur Penagihan (Sleek Rounded-Full Pills 60-30-10) -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 no-scrollbar">
                <button type="button" 
                        @click="subFilter = 'semua'"
                        :class="subFilter === 'semua' ? 'bg-[#0E527B] text-white font-extrabold shadow-sm' : 'bg-white text-slate-600 border border-slate-200 font-semibold hover:bg-slate-50'"
                        class="px-4 py-2 rounded-full text-xs shrink-0 transition cursor-pointer">
                    Semua ({{ $suratTugasMenunggu->count() + $tagihanAktif->count() }})
                </button>

                <button type="button" 
                        @click="subFilter = 'tugas_baru'"
                        :class="subFilter === 'tugas_baru' ? 'bg-[#0E527B] text-white font-extrabold shadow-sm' : 'bg-white text-slate-600 border border-slate-200 font-semibold hover:bg-slate-50'"
                        class="px-4 py-2 rounded-full text-xs shrink-0 transition cursor-pointer flex items-center gap-1.5">
                    <span>Surat Tugas</span>
                    @if($suratTugasMenunggu->count() > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-400 text-amber-950 font-black">
                            {{ $suratTugasMenunggu->count() }}
                        </span>
                    @endif
                </button>

                <button type="button" 
                        @click="subFilter = 'siap_ditagih'"
                        :class="subFilter === 'siap_ditagih' ? 'bg-[#0E527B] text-white font-extrabold shadow-sm' : 'bg-white text-slate-600 border border-slate-200 font-semibold hover:bg-slate-50'"
                        class="px-4 py-2 rounded-full text-xs shrink-0 transition cursor-pointer">
                    <span>Tagihan Aktif ({{ $tagihanAktif->count() }})</span>
                </button>

                <button type="button" 
                        @click="subFilter = 'setor_kasir'"
                        :class="subFilter === 'setor_kasir' ? 'bg-[#0E527B] text-white font-extrabold shadow-sm' : 'bg-white text-slate-600 border border-slate-200 font-semibold hover:bg-slate-50'"
                        class="px-4 py-2 rounded-full text-xs shrink-0 transition cursor-pointer flex items-center gap-1.5">
                    <span>Setoran BSK</span>
                    @if($setoranPending->count() > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-[#0E527B] text-white font-black">
                            {{ $setoranPending->count() }}
                        </span>
                    @endif
                </button>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- SECTION A: SURAT TUGAS BARU MENUNGGU KONFIRMASI                     -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div x-show="subFilter === 'semua' || subFilter === 'tugas_baru'" class="space-y-3.5">
                @if($suratTugasMenunggu->count() > 0)
                    <div class="flex items-center justify-between px-1 pt-1">
                        <span class="text-xs font-extrabold text-[#0E527B] uppercase tracking-wider">
                            Surat Tugas Baru ({{ $suratTugasMenunggu->count() }})
                        </span>
                        <span class="text-[11px] text-slate-400 font-medium">Pilih terima atau tolak tugas</span>
                    </div>

                    @foreach($suratTugasMenunggu as $tugas)
                        <div class="bg-white rounded-[1.75rem] p-4.5 shadow-sm border border-slate-100 hover:shadow-md transition space-y-3.5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-full bg-sky-50 border border-sky-100 flex items-center justify-center text-[#0E527B] shrink-0 mt-0.5">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-[#0E527B] text-[10px] font-mono font-bold border border-slate-200">
                                                {{ $tugas->no_tugas }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-900">
                                                Tugas Baru
                                            </span>
                                        </div>
                                        <h3 class="text-sm font-extrabold text-slate-900 mt-1 truncate">
                                            {{ $tugas->faktur->customer->nama_customer ?? 'Pelanggan / Agen' }}
                                        </h3>
                                        <p class="text-xs text-slate-500 truncate">
                                            {{ $tugas->faktur->customer->kota ?? ($tugas->faktur->customer->alamat ?? 'Tasikmalaya') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Sisa Piutang</span>
                                    <span class="text-base font-black text-rose-600 block">
                                        Rp {{ number_format($tugas->faktur->sisa_piutang ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div class="bg-slate-50/80 rounded-2xl p-3 text-xs text-slate-600 space-y-1.5 border border-slate-100">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">No. Faktur:</span>
                                    <span class="font-mono font-bold text-slate-800">{{ $tugas->faktur->no_faktur }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">Divisi:</span>
                                    <span class="font-bold uppercase text-[#0E527B]">{{ $tugas->faktur->kategori ?? 'sirkulasi' }}</span>
                                </div>
                                @if($tugas->catatan)
                                    <div class="pt-1 border-t border-slate-200/60 text-[11px] text-slate-500 italic">
                                        Catatan: "{{ $tugas->catatan }}"
                                    </div>
                                @endif
                            </div>

                            <!-- Tombol Aksi Konfirmasi Fleksibel Per-Faktur -->
                            <div class="grid grid-cols-2 gap-2.5 pt-1">
                                <button type="button" 
                                        onclick="bukaModalTolak('{{ $tugas->id }}', '{{ $tugas->faktur->no_faktur }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '-') }}')"
                                        class="w-full h-11 py-2 px-3 rounded-2xl border border-rose-200 bg-rose-50 hover:bg-rose-100 active:scale-98 text-rose-700 text-xs font-bold flex items-center justify-center gap-1.5 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Tolak Faktur Ini</span>
                                </button>

                                <form action="{{ route('keuangan.inkaso.penugasan.konfirmasi', $tugas->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="aksi" value="terima">
                                    <button type="submit" 
                                            class="w-full h-11 py-2 px-3 rounded-2xl bg-[#0E527B] hover:bg-[#0A3D5C] active:scale-98 text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-sm transition cursor-pointer">
                                        <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Terima Tugas</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div x-show="subFilter === 'tugas_baru'" class="bg-white rounded-3xl p-8 text-center shadow-xs border border-slate-100">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800">Tidak Ada Surat Tugas Baru</h4>
                        <p class="text-xs text-slate-500 mt-1">Semua surat tugas sudah Anda konfirmasi.</p>
                    </div>
                @endif
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- SECTION B: DAFTAR TAGIHAN AKTIF LAPANGAN & KARTU STATUS SETORAN     -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div x-show="subFilter === 'semua' || subFilter === 'siap_ditagih'" class="space-y-3.5">
                <div class="flex items-center justify-between px-1 pt-1">
                    <span class="text-xs font-extrabold text-[#0E527B] uppercase tracking-wider">
                        Daftar Tagihan ({{ $tagihanAktif->count() }})
                    </span>
                    <span class="text-[11px] text-slate-400 font-medium">Pilih faktur untuk bayar</span>
                </div>

                @forelse($tagihanAktif as $tugas)
                    @php
                        $pendingBsk = $pendingBskByFaktur->get($tugas->faktur_id);
                    @endphp
                    <div class="bg-white rounded-[1.75rem] p-4.5 shadow-sm border border-slate-100 hover:shadow-md transition space-y-3.5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-[#0E527B] text-[10px] font-mono font-bold border border-slate-200">
                                        {{ $tugas->faktur->no_faktur }}
                                    </span>
                                    
                                    @if($pendingBsk)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-900 border border-amber-300">
                                            <svg class="w-3 h-3 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Menunggu Kasir</span>
                                        </span>
                                    @elseif($tugas->faktur->total_terbayar > 0)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-sky-100 text-[#0E527B]">
                                            Cicilan
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-slate-100 text-slate-700">
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
                                <span class="text-[10px] text-slate-400 block mt-0.5 font-medium">
                                    Total: Rp {{ number_format($tugas->faktur->total_tagihan ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- KONDISI JIKA SUDAH ADA BSK PENDING (CEGAH DOUBLE INPUT SECARA VISUAL) -->
                        @if($pendingBsk)
                            <div class="p-3.5 rounded-2xl bg-amber-50/80 border border-amber-200/80 text-xs space-y-1">
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
                                   class="w-full h-11 px-4 rounded-2xl bg-[#0E527B] hover:bg-[#07334F] text-white text-xs sm:text-sm font-bold flex items-center justify-center gap-2 shadow-sm transition cursor-pointer active:scale-98">
                                    <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    <span>Tampilkan Barcode BSK ke Kasir</span>
                                </a>
                            </div>
                        @else
                            <!-- Tombol Cepat Hubungi & Tombol Input Pembayaran -->
                            <div class="flex items-center gap-2 pt-1 border-t border-slate-100">
                                @if(!empty($tugas->faktur->customer->no_telepon))
                                    <a href="tel:{{ $tugas->faktur->customer->no_telepon }}" 
                                       class="h-11 px-4 rounded-2xl border border-slate-200 text-slate-700 hover:text-[#0E527B] hover:bg-slate-50 text-xs font-bold flex items-center justify-center gap-1.5 shrink-0 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span>Hubungi</span>
                                    </a>
                                @endif

                                <button type="button" 
                                        onclick="bukaModalBayar('{{ $tugas->faktur_id }}', '{{ $tugas->id }}', '{{ $tugas->faktur->no_faktur }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '-') }}', '{{ (float) $tugas->faktur->sisa_piutang }}')"
                                        class="flex-1 h-11 px-4 rounded-2xl bg-[#0E527B] hover:bg-[#07334F] active:scale-98 text-white text-xs sm:text-sm font-bold flex items-center justify-center gap-2 shadow-sm transition cursor-pointer">
                                    <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span>Input Pembayaran</span>
                                </button>
                            </div>
                        @endif
                    </div>
                @empty
                    <div x-show="subFilter === 'siap_ditagih' || subFilter === 'semua'" class="bg-white rounded-3xl p-8 text-center shadow-xs border border-slate-100">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800">Belum Ada Tagihan Aktif</h4>
                        <p class="text-xs text-slate-500 mt-1">Cek Surat Tugas untuk menerima penugasan baru.</p>
                    </div>
                @endforelse
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- SECTION C: REKAP SETORAN BSK SIAP KASIR (LOKET SORE HARI)           -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div x-show="subFilter === 'semua' || subFilter === 'setor_kasir'" class="space-y-3.5 pt-2">
                @if($setoranPending->count() > 0)
                    <div class="flex items-center justify-between px-1">
                        <div>
                            <span class="text-xs font-extrabold text-[#0E527B] uppercase tracking-wider block">
                                Setoran Menunggu Kasir ({{ $setoranPending->count() }})
                            </span>
                            <span class="text-[11px] text-slate-500 font-medium">Tunjukkan barcode ke loket kasir</span>
                        </div>
                    </div>

                    <!-- Petunjuk Setor Kasir (Bahasa Sederhana & Ramah Lapangan) -->
                    <div class="p-3.5 rounded-2xl bg-sky-50/80 border border-sky-200/80 text-xs text-[#0E527B] flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-[#0E527B] shrink-0 shadow-2xs">
                            <svg class="w-4 h-4 text-[#0E527B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-[11px] text-slate-700 leading-snug">
                            Tunjukkan barcode bukti setoran di bawah ini beserta uang tunai ke Kasir untuk diverifikasi.
                        </p>
                    </div>

                    @foreach($setoranPending as $setoran)
                        <div class="bg-white rounded-[1.75rem] p-4.5 shadow-sm border border-slate-100 hover:shadow-md transition space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 border border-slate-200 font-mono text-[10px] font-bold text-slate-800">
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
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300 mt-1">
                                        <svg class="w-3 h-3 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Menunggu Kasir</span>
                                    </span>
                                </div>
                            </div>

                            <div class="text-xs text-slate-600 bg-slate-50/80 p-3 rounded-2xl border border-slate-100">
                                <strong>Keterangan:</strong> {{ $setoran->keterangan_angsuran }}
                            </div>

                            <!-- Tombol Buka Barcode -->
                            <div class="pt-1">
                                <a href="{{ route('keuangan.inkaso.bsk.show', $setoran->no_bsk) }}" 
                                   class="w-full h-11 px-4 rounded-2xl bg-[#0E527B] hover:bg-[#07334F] active:scale-98 text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-sm transition cursor-pointer">
                                    <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    <span>Tampilkan Barcode ke Kasir</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div x-show="subFilter === 'setor_kasir'" class="bg-white rounded-3xl p-8 text-center shadow-xs border border-slate-100">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800">Tidak Ada Setoran Menunggu</h4>
                        <p class="text-xs text-slate-500 mt-1">Semua titipan penagihan sudah disahkan kasir.</p>
                    </div>
                @endif
            </div>

            <!-- Spacer Ekstra Bawah -->
            <div class="h-44 w-full shrink-0 pointer-events-none" style="height: 180px; min-height: 180px;" aria-hidden="true"></div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- TAB 3: RIWAYAT PEMBAYARAN SAH (DISAHKAN KASIR BKM)                     -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div x-show="mobileTab === 'riwayat'" x-cloak class="space-y-4 pb-12">
            
            <!-- Summary Banner (Mini Hero Card) -->
            <div class="rounded-[2rem] p-5 text-white shadow-md relative overflow-hidden"
                 style="background: linear-gradient(135deg, #0E527B 0%, #0A72AC 100%) !important;">
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white border border-white/25">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-white/80 uppercase tracking-wider block">Total Koleksi Sah Hari Ini</span>
                            <div class="text-xl sm:text-2xl font-black text-white leading-tight">
                                Rp {{ number_format($totalTerkumpulHariIni, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-white/20 text-white border border-white/30 backdrop-blur-md">
                        {{ $riwayatSetoranSah->count() }} Lunas
                    </span>
                </div>
            </div>

            <div class="flex items-center justify-between px-1">
                <div>
                    <span class="text-xs font-extrabold text-[#0E527B] uppercase tracking-wider block">
                        Riwayat Pembayaran Sah ({{ $riwayatSetoranSah->count() }})
                    </span>
                    <span class="text-[11px] text-slate-500 font-medium">Arsip kuitansi BKM kasir resmi</span>
                </div>
            </div>

            @forelse($riwayatSetoranSah as $setoran)
                <div class="bg-white rounded-[1.75rem] p-4.5 shadow-sm border border-slate-100 hover:shadow-md transition space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-[#0E527B] font-mono text-[10px] font-bold border border-slate-200">
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
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 mt-1">
                                <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span>Disetujui Kasir</span>
                            </span>
                        </div>
                    </div>

                    <div class="text-xs text-slate-700 bg-slate-50/80 p-3 rounded-2xl border border-slate-100 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-semibold">No. Kuitansi BKM:</span>
                            <span class="font-mono font-bold text-[#0E527B]">{{ $setoran->no_kuitansi_kasir ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Disahkan oleh:</span>
                            <span class="font-medium text-slate-800">{{ $setoran->kasir->name ?? 'Kasir Redaksi' }} ({{ $setoran->approved_at ? $setoran->approved_at->format('H:i') : '' }} WIB)</span>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-slate-200/60">
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
                           class="w-full h-11 rounded-2xl border border-slate-200 hover:bg-slate-50 active:scale-98 text-slate-700 text-xs font-bold flex items-center justify-center gap-1.5 transition cursor-pointer shadow-2xs">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Lihat Bukti Setoran</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl p-8 text-center shadow-xs border border-slate-100">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800">Belum Ada Riwayat Pembayaran Sah</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">
                        Setelah Kasir menyetujui setoran Anda di loket, tanda terima resmi akan tersimpan di sini.
                    </p>
                </div>
            @endforelse

            <!-- Spacer Ekstra Bawah -->
            <div class="h-44 w-full shrink-0 pointer-events-none" style="height: 180px; min-height: 180px;" aria-hidden="true"></div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- TAB 4: PROFIL PETUGAS INKASO (VIEW-ONLY ELEGAN & MODAL EDIT)           -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div x-show="mobileTab === 'profil'" x-cloak class="space-y-4 pb-12">
            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 text-center space-y-4">
                
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
                    <div class="flex items-center justify-center gap-1.5 mt-2 flex-wrap">
                        <span class="px-3.5 py-1 rounded-full text-[10px] font-black bg-[#FFB900] text-[#07334F] uppercase tracking-wider shadow-2xs">
                            Petugas Inkaso Resmi
                        </span>
                        <span class="px-3 py-1 rounded-full text-[10px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                            ID: INK-PR-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                </div>

                <!-- Informasi Detail Profil Staf -->
                <div class="text-left bg-slate-50/80 border border-slate-200/70 rounded-2xl p-4 space-y-3 text-xs">
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
                        <span class="font-bold text-[#0E527B] text-xs flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-[#0E527B]"></span>
                            <span>Kolektor Lapangan</span>
                        </span>
                    </div>
                </div>

                <!-- Kebijakan Keamanan Akun Terpadu Korporat (Dilarang Ganti Password Mandiri) -->
                <div class="p-4 rounded-2xl bg-sky-50/80 border border-sky-200/80 text-left text-xs space-y-1.5">
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
                            class="w-full h-12 rounded-2xl bg-[#0E527B] hover:bg-[#07334F] text-white text-sm font-bold flex items-center justify-center gap-2 shadow-sm transition cursor-pointer active:scale-98">
                        <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        <span>Ubah Profil &amp; Foto</span>
                    </button>

                    <button type="button" 
                            @click="showLogoutModal = true" 
                            class="w-full h-11 rounded-2xl border border-slate-300 hover:border-rose-300 hover:bg-rose-50 text-slate-700 hover:text-rose-700 text-xs font-extrabold flex items-center justify-center gap-2 transition cursor-pointer active:scale-98">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Keluar dari Akun Inkaso</span>
                    </button>
                </div>
            </div>

            <!-- Spacer Ekstra Bawah -->
            <div class="h-44 w-full shrink-0 pointer-events-none" style="height: 180px; min-height: 180px;" aria-hidden="true"></div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- FLOATING BOTTOM DOCK NAVIGATION (FOTO 2 STYLE - DOCK BIRU #0E527B)       -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <nav class="fixed bottom-5 left-4 right-4 max-w-sm mx-auto z-40 bg-[#0E527B] shadow-[0_12px_40px_rgba(14,82,123,0.38)] rounded-full p-1.5 border border-white/20 flex items-center justify-between select-none">
            
            {{-- 1. Beranda (Home Icon + Expanded Pill Label saat aktif) --}}
            <button type="button" 
                    @click="mobileTab = 'beranda'; window.scrollTo({top: 0, behavior: 'smooth'})" 
                    class="cursor-pointer transition-all duration-300 flex items-center select-none"
                    :class="mobileTab === 'beranda' ? 'bg-white text-[#0E527B] px-4 py-2 rounded-full font-extrabold text-xs shadow-md gap-2' : 'w-10 h-10 rounded-full flex items-center justify-center text-white/75 hover:text-white'">
                <svg class="w-4.5 h-4.5 shrink-0" 
                     :class="mobileTab === 'beranda' ? 'text-[#0E527B]' : 'text-white/80'"
                     fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                <span x-show="mobileTab === 'beranda'" x-transition class="tracking-tight whitespace-nowrap font-bold">Beranda</span>
            </button>

            {{-- 2. Penagihan (Analytics Icon + Notification Counter + Expanded Pill Label saat aktif) --}}
            <button type="button" 
                    @click="mobileTab = 'penagihan'; subFilter = 'semua'; window.scrollTo({top: 0, behavior: 'smooth'})" 
                    class="cursor-pointer transition-all duration-300 flex items-center select-none relative"
                    :class="mobileTab === 'penagihan' ? 'bg-white text-[#0E527B] px-4 py-2 rounded-full font-extrabold text-xs shadow-md gap-2' : 'w-10 h-10 rounded-full flex items-center justify-center text-white/75 hover:text-white'">
                <div class="relative shrink-0 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5" 
                         :class="mobileTab === 'penagihan' ? 'text-[#0E527B]' : 'text-white/80'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    @if(($suratTugasMenunggu->count() + $tagihanAktif->count()) > 0)
                        <span x-show="mobileTab !== 'penagihan'" class="absolute -top-1.5 -right-2 min-w-[15px] h-3.5 px-0.5 bg-amber-400 text-amber-950 rounded-full text-[9px] font-black flex items-center justify-center shadow-xs ring-1 ring-[#0E527B]">
                            {{ $suratTugasMenunggu->count() + $tagihanAktif->count() }}
                        </span>
                    @endif
                </div>
                <span x-show="mobileTab === 'penagihan'" x-transition class="tracking-tight whitespace-nowrap font-bold">Penagihan</span>
                @if(($suratTugasMenunggu->count() + $tagihanAktif->count()) > 0)
                    <span x-show="mobileTab === 'penagihan'" class="ml-0.5 px-1.5 py-0.2 rounded-full text-[9px] font-black bg-[#0E527B] text-white">
                        {{ $suratTugasMenunggu->count() + $tagihanAktif->count() }}
                    </span>
                @endif
            </button>

            {{-- 3. Riwayat (Receipt / Card Icon + Expanded Pill Label saat aktif) --}}
            <button type="button" 
                    @click="mobileTab = 'riwayat'; window.scrollTo({top: 0, behavior: 'smooth'})" 
                    class="cursor-pointer transition-all duration-300 flex items-center select-none"
                    :class="mobileTab === 'riwayat' ? 'bg-white text-[#0E527B] px-4 py-2 rounded-full font-extrabold text-xs shadow-md gap-2' : 'w-10 h-10 rounded-full flex items-center justify-center text-white/75 hover:text-white'">
                <svg class="w-4.5 h-4.5 shrink-0" 
                     :class="mobileTab === 'riwayat' ? 'text-[#0E527B]' : 'text-white/80'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span x-show="mobileTab === 'riwayat'" x-transition class="tracking-tight whitespace-nowrap font-bold">Riwayat</span>
            </button>

            {{-- 4. Profil (User Silhouette + Expanded Pill Label saat aktif) --}}
            <button type="button" 
                    @click="mobileTab = 'profil'; window.scrollTo({top: 0, behavior: 'smooth'})" 
                    class="cursor-pointer transition-all duration-300 flex items-center select-none"
                    :class="mobileTab === 'profil' ? 'bg-white text-[#0E527B] px-4 py-2 rounded-full font-extrabold text-xs shadow-md gap-2' : 'w-10 h-10 rounded-full flex items-center justify-center text-white/75 hover:text-white'">
                <svg class="w-4.5 h-4.5 shrink-0" 
                     :class="mobileTab === 'profil' ? 'text-[#0E527B]' : 'text-white/80'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span x-show="mobileTab === 'profil'" x-transition class="tracking-tight whitespace-nowrap font-bold">Profil</span>
            </button>
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
                    <button type="button" onclick="tutupModalBayar()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer" title="Tutup"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
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
                    <button type="button" onclick="tutupModalTolak()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer" title="Tutup"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
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
                    <button type="button" @click="editProfileModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer" title="Tutup"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
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
