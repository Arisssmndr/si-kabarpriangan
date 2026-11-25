<x-mobile-inkaso-layout>
    <div x-data="{ 
            mobileTab: '{{ in_array(request('tab'), ['tagihan', 'surat_tugas', 'setoran_bsk', 'penagihan']) ? 'penagihan' : request('tab', 'beranda') }}', 
            subFilter: '{{ request('sub') ?: (request('tab') === 'surat_tugas' ? 'surat_penugasan' : (request('tab') === 'setoran_bsk' ? 'setor_kasir' : 'semua_faktur')) }}',
            riwayatTab: 'surat',
            riwayatQuery: '',
            riwayatPeriod: 'semua',
            riwayatDateFrom: '',
            riwayatDateTo: '',
            showRiwayatFilter: false,
            openSurat: null,
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
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-300 text-[#07334F] text-xs font-bold flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-[#FFB900] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M6 18L18 6M6 6l12 12"/></svg>
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
                            @click="mobileTab = 'penagihan'; subFilter = 'semua_faktur'; window.scrollTo({top: 0, behavior: 'smooth'})"
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
                <div @click="mobileTab = 'penagihan'; subFilter = 'surat_penugasan'; window.scrollTo({top: 0, behavior: 'smooth'})"
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
                <div @click="mobileTab = 'penagihan'; subFilter = 'semua_faktur'; window.scrollTo({top: 0, behavior: 'smooth'})"
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

                <!-- CARD 4: SETORAN KASIR (DENGAN MINI DOT MATRIX VISUAL - 60-30-10) -->
                <div @click="mobileTab = 'riwayat'; window.scrollTo({top: 0, behavior: 'smooth'})"
                     class="bg-white rounded-[1.75rem] p-4 shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition cursor-pointer active:scale-98">
                    <div class="flex items-center justify-between">
                        <div class="w-9 h-9 rounded-full bg-sky-50 border border-sky-100 flex items-center justify-center text-[#0E527B] shrink-0">
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
                            <span class="text-[10px] text-[#0E527B] font-bold block mt-0.5">Lunas Kasir</span>
                        </div>
                        <!-- Mini dot matrix matching reference image Card 4 -->
                        <div class="grid grid-cols-4 gap-1 pb-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#0E527B]"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-[#0E527B]"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-[#FFB900]"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-[#0E527B]"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-[#FFB900]"></span>
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
        <!-- TAB 2: PENAGIHAN (3 MENU: SEMUA FAKTUR | SURAT PENUGASAN | SETORAN BSK)-->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div x-show="mobileTab === 'penagihan'" x-cloak class="space-y-3.5 pb-12">

            <!-- Search Bar Ringkas -->
            <form method="GET" action="{{ route('keuangan.inkaso.mobile') }}" class="relative">
                <input type="hidden" name="tab" value="penagihan">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari No. Surat, No. Faktur, atau Nama Agen..."
                       class="w-full pl-10 pr-9 py-2.5 rounded-2xl text-xs bg-white border border-slate-200 text-slate-800 placeholder-slate-400 shadow-xs focus:outline-none focus:ring-2 focus:ring-[#0E527B]">
                @if($q)
                    <a href="{{ route('keuangan.inkaso.mobile', ['tab' => 'penagihan']) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </form>

            <!-- 3 Pill Menu Penagihan (Gaya Dock Navigasi Bawah: Dock Biru #0E527B, Aktif Putih Tanpa Kuning) -->
            @php
                $totalSuratPenugasanMenu = ($suratTugasMenunggu->count() > 0 ? $suratTugasMenunggu->groupBy(fn($t) => $t->no_surat_grup ?: ('IND-' . $t->no_tugas))->count() : 0)
                    + ($tagihanAktif->count() > 0 ? $tagihanAktif->groupBy(fn($t) => $t->no_surat_grup ?: ('IND-' . $t->no_tugas))->count() : 0);
            @endphp
            <div class="flex items-center gap-1 p-1.5 rounded-full bg-[#0E527B] shadow-sm border border-[#07334F]/20">
                <button type="button"
                        @click="subFilter = 'semua_faktur'"
                        :class="subFilter === 'semua_faktur' ? 'bg-white text-[#0E527B] font-extrabold shadow-sm' : 'bg-white/15 hover:bg-white/25 text-white font-semibold'"
                        class="flex-1 px-2 py-2 rounded-full text-[10.5px] transition cursor-pointer flex items-center justify-center gap-1 whitespace-nowrap">
                    <span>Semua Faktur</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-black"
                          :class="subFilter === 'semua_faktur' ? 'bg-[#0E527B] text-white' : 'bg-white/20 text-white'">
                        {{ $tagihanAktif->count() }}
                    </span>
                </button>

                <button type="button"
                        @click="subFilter = 'surat_penugasan'"
                        :class="subFilter === 'surat_penugasan' ? 'bg-white text-[#0E527B] font-extrabold shadow-sm' : 'bg-white/15 hover:bg-white/25 text-white font-semibold'"
                        class="flex-1 px-2 py-2 rounded-full text-[10.5px] transition cursor-pointer flex items-center justify-center gap-1 whitespace-nowrap">
                    <span>Surat Penugasan</span>
                    @if($totalSuratPenugasanMenu > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-black"
                              :class="subFilter === 'surat_penugasan' ? 'bg-[#0E527B] text-white' : 'bg-white/20 text-white'">
                            {{ $totalSuratPenugasanMenu }}
                        </span>
                    @endif
                </button>

                <button type="button"
                        @click="subFilter = 'setor_kasir'"
                        :class="subFilter === 'setor_kasir' ? 'bg-white text-[#0E527B] font-extrabold shadow-sm' : 'bg-white/15 hover:bg-white/25 text-white font-semibold'"
                        class="flex-1 px-2 py-2 rounded-full text-[10.5px] transition cursor-pointer flex items-center justify-center gap-1 whitespace-nowrap">
                    <span>Setoran BSK</span>
                    @if($setoranPending->count() > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-black"
                              :class="subFilter === 'setor_kasir' ? 'bg-[#0E527B] text-white' : 'bg-white/20 text-white'">
                            {{ $setoranPending->count() }}
                        </span>
                    @endif
                </button>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- MENU 1: SEMUA FAKTUR (BELUM BAYAR & SUDAH INPUT QR, KARTU TERPISAH)  -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div x-show="subFilter === 'semua_faktur'" class="space-y-3">
                @if($tagihanAktif->count() > 0)
                    @foreach($tagihanAktif as $tugas)
                        @php
                            $pendingBsk = $pendingBskByFaktur->get($tugas->faktur_id);
                            $sisaFaktur = (float) ($tugas->faktur->sisa_piutang ?? 0);
                            $isCicilan = !$pendingBsk && ($tugas->faktur->total_terbayar ?? 0) > 0;
                            $suratCode = $tugas->no_surat_grup ?: $tugas->no_tugas;
                        @endphp

                        <div class="relative overflow-hidden bg-white rounded-[1.75rem] p-4 shadow-sm border {{ $pendingBsk ? 'border-[#FFB900]/70' : 'border-slate-200' }} space-y-3">
                            <!-- Pita Pojok Diagonal (Gaya Foto 2) -->
                            @if($pendingBsk)
                                <div class="absolute bg-[#FFB900] text-[#07334F] font-black text-[9px] tracking-widest uppercase py-1 text-center rotate-45 shadow-2xs pointer-events-none"
                                     style="top: 14px; right: -34px; width: 132px;">
                                    PENDING
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
                                        {{ $tugas->faktur->no_faktur }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-mono font-bold bg-sky-50 text-[#0E527B] border border-sky-100">
                                        {{ $suratCode }}
                                    </span>
                                    @if($pendingBsk)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-100 text-[#07334F] border border-amber-300">
                                            Sudah Input &bull; QR Siap
                                        </span>
                                    @elseif($isCicilan)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-sky-50 text-[#0E527B]">
                                            Cicilan
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 text-slate-600">
                                            Belum Bayar
                                        </span>
                                    @endif
                                </div>

                                <h4 class="text-sm font-extrabold text-slate-900 leading-snug pt-0.5">
                                    {{ $tugas->faktur->customer->nama_customer ?? 'Pelanggan / Agen' }}
                                </h4>
                                @if(!$pendingBsk)
                                    <p class="text-[11px] text-slate-500 truncate">
                                        {{ $tugas->faktur->customer->kota ?? ($tugas->faktur->customer->alamat ?? 'Tasikmalaya') }}
                                    </p>
                                @endif
                            </div>

                            @if($pendingBsk)
                                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3 flex items-center justify-between gap-2">
                                    <div class="min-w-0">
                                        <span class="text-[10px] font-bold text-slate-500 uppercase block">Sudah Diinput ({{ strtoupper($pendingBsk->metode_bayar) }})</span>
                                        <span class="text-[11px] font-mono font-bold text-[#0E527B]">{{ $pendingBsk->no_bsk }}</span>
                                    </div>
                                    <span class="text-base font-black text-[#0E527B] font-mono shrink-0">
                                        Rp {{ number_format($pendingBsk->nominal_titipan, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('keuangan.inkaso.bsk.show', $pendingBsk->no_bsk) }}"
                                       class="flex-1 h-10 px-3 rounded-xl bg-[#0E527B] hover:bg-[#07334F] active:scale-98 text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition cursor-pointer">
                                        <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                        <span>Lihat QR</span>
                                    </a>

                                    @if($tugas->status_tugas !== 'selesai')
                                        <button type="button"
                                                onclick="bukaModalEditBayar('{{ $pendingBsk->id }}', '{{ $pendingBsk->no_bsk }}', '{{ $tugas->faktur->no_faktur }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '-') }}', '{{ $sisaFaktur }}', '{{ (float) $pendingBsk->nominal_titipan }}', '{{ $pendingBsk->metode_bayar }}', '{{ addslashes($pendingBsk->keterangan_angsuran ?? '') }}')"
                                                class="h-10 px-4 rounded-xl bg-[#FFB900] hover:bg-amber-400 active:scale-98 text-[#07334F] text-xs font-black flex items-center justify-center gap-1 shadow-xs transition cursor-pointer shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            <span>Ubah</span>
                                        </button>
                                    @endif
                                </div>
                            @else
                                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3 flex items-center justify-between gap-2">
                                    <span class="text-[11px] font-bold text-slate-500 uppercase">Sisa Piutang</span>
                                    <span class="text-base font-black text-[#0E527B] font-mono">
                                        Rp {{ number_format($sisaFaktur, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if(!empty($tugas->faktur->customer->no_telepon))
                                        <a href="tel:{{ $tugas->faktur->customer->no_telepon }}"
                                           class="h-10 px-3.5 rounded-xl border border-slate-200 text-[#0E527B] hover:bg-slate-50 text-xs font-bold flex items-center justify-center gap-1 shrink-0 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            <span>Hubungi</span>
                                        </a>
                                    @else
                                        <button type="button"
                                                onclick="hubungiKosong('{{ addslashes($tugas->faktur->customer->nama_customer ?? 'Pelanggan') }}')"
                                                class="h-10 px-3.5 rounded-xl border border-slate-200 text-[#0E527B] hover:bg-slate-50 text-xs font-bold flex items-center justify-center gap-1 shrink-0 transition cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            <span>Hubungi</span>
                                        </button>
                                    @endif
                                    <button type="button"
                                            onclick="bukaModalBayar('{{ $tugas->faktur_id }}', '{{ $tugas->id }}', '{{ $tugas->faktur->no_faktur }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '-') }}', '{{ $sisaFaktur }}')"
                                            class="flex-1 h-10 px-4 rounded-xl bg-[#0E527B] hover:bg-[#07334F] active:scale-98 text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition cursor-pointer">
                                        <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <span>Input Pembayaran</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="bg-white rounded-3xl p-8 text-center shadow-xs border border-slate-100">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800">Belum Ada Faktur Aktif</h4>
                        <p class="text-xs text-slate-500 mt-1">Buka tab Surat Tugas untuk melihat atau menerima penugasan baru.</p>
                    </div>
                @endif
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- MENU 2: SURAT PENUGASAN (KLIK PINDAH HALAMAN KHUSUS + BUTTON SELESAI)-->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div x-show="subFilter === 'surat_penugasan'" class="space-y-4">
                @if($suratTugasMenunggu->count() > 0)
                    @php
                        $suratGrupTugas = $suratTugasMenunggu->groupBy(function($t) {
                            return $t->no_surat_grup ?: ('IND-' . $t->no_tugas);
                        });
                    @endphp

                    @foreach($suratGrupTugas as $noGrupKey => $tugasList)
                        @php
                            $isGrup = !str_starts_with($noGrupKey, 'IND-');
                            $noSuratLabel = $isGrup ? $noGrupKey : ($tugasList->first()->no_tugas ?? '-');
                            $totalPiutangGrup = $tugasList->sum(fn($t) => $t->faktur->sisa_piutang ?? 0);
                        @endphp

                        <!-- Kartu Surat Penugasan Baru Menunggu Konfirmasi -->
                        <div class="bg-white rounded-[1.75rem] shadow-sm border border-slate-100 overflow-hidden">
                            <div class="bg-gradient-to-r from-[#0E527B]/6 to-transparent px-4 pt-3.5 pb-3 border-b border-slate-100">
                                <div class="flex items-center justify-between gap-2">
                                    <a href="{{ route('keuangan.inkaso.surat-penugasan.show', $isGrup ? $noSuratLabel : ('IND-' . $noSuratLabel)) }}"
                                       class="flex items-center gap-2 min-w-0 group">
                                        <div class="w-8 h-8 rounded-full bg-[#0E527B]/10 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-[#0E527B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="text-xs font-black text-[#0E527B] font-mono group-hover:underline">{{ $noSuratLabel }}</span>
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-100 text-amber-800">Surat Tugas Baru</span>
                                            </div>
                                            <span class="text-[10px] text-[#0E527B] font-bold inline-flex items-center gap-0.5">
                                                <span>{{ $tugasList->count() }} Faktur &bull; Lihat Surat Resmi &rsaquo;</span>
                                            </span>
                                        </div>
                                    </a>
                                    <span class="text-sm font-black text-[#0E527B] shrink-0">Rp {{ number_format($totalPiutangGrup, 0, ',', '.') }}</span>
                                </div>
                                @if($isGrup && $tugasList->count() > 1)
                                    <div class="mt-2.5">
                                        <form action="{{ route('keuangan.inkaso.penugasan.grup.konfirmasi', $noSuratLabel) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full h-9 rounded-xl bg-[#0E527B] hover:bg-[#07334F] active:scale-98 text-white text-xs font-extrabold flex items-center justify-center gap-1.5 shadow-xs transition cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                <span>Terima Semua Surat Ini</span>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <div class="divide-y divide-slate-100">
                                @foreach($tugasList as $tugas)
                                    <div class="p-3.5 space-y-2.5">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-[#0E527B] text-[9px] font-mono font-bold">
                                                        {{ $tugas->faktur->no_faktur }}
                                                    </span>
                                                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-amber-50 text-amber-800">
                                                        {{ strtoupper($tugas->faktur->kategori ?? 'iklan') }}
                                                    </span>
                                                </div>
                                                <h3 class="text-xs font-extrabold text-slate-900 mt-1 truncate">
                                                    {{ $tugas->faktur->customer->nama_customer ?? 'Pelanggan / Agen' }}
                                                </h3>
                                            </div>
                                            <span class="text-xs font-black text-[#0E527B] shrink-0 mt-0.5">
                                                Rp {{ number_format($tugas->faktur->sisa_piutang ?? 0, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <button type="button"
                                                    onclick="bukaModalTolak('{{ $tugas->id }}', '{{ $tugas->faktur->no_faktur }}', '{{ addslashes($tugas->faktur->customer->nama_customer ?? '-') }}')"
                                                    class="h-9 px-3 rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100 active:scale-98 text-[#07334F] text-xs font-bold flex items-center justify-center gap-1 transition cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-[#0E527B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                <span>Tolak Faktur Ini</span>
                                            </button>

                                            <form action="{{ route('keuangan.inkaso.penugasan.konfirmasi', $tugas->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="aksi" value="terima">
                                                <button type="submit"
                                                        class="w-full h-9 px-3 rounded-xl bg-[#0E527B] hover:bg-[#0A3D5C] active:scale-98 text-white text-xs font-bold flex items-center justify-center gap-1 shadow-xs transition cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Terima Tugas</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- Daftar Surat Penugasan Aktif (Klik menuju halaman khusus Rincian Surat + Tombol Selesai) --}}
                @if($tagihanAktif->count() > 0)
                    @php
                        $suratAktifList = $tagihanAktif->groupBy(function($t) {
                            return $t->no_surat_grup ?: ('IND-' . $t->no_tugas);
                        });
                    @endphp
                    @foreach($suratAktifList as $noGrupKey => $tugasList)
                        @php
                            $isGrup = !str_starts_with($noGrupKey, 'IND-');
                            $noSuratLabel = $isGrup ? $noGrupKey : ($tugasList->first()->no_tugas ?? '-');
                            $routeKey = $isGrup ? $noSuratLabel : $noGrupKey;
                            $allInGrup = $isGrup && isset($allTasksByGrup) ? ($allTasksByGrup->get($noGrupKey) ?? $tugasList) : $tugasList;
                            $totalPiutangAktifGrup = $tugasList->sum(fn($t) => $t->faktur->sisa_piutang ?? 0);
                            $totalTerkumpulGrup = $allInGrup->sum(fn($t) => max((float) ($t->faktur->total_terbayar ?? 0), (float) $t->nominal_diterima));
                            $sudahInputCount = $tugasList->filter(fn($t) => isset($pendingBskByFaktur[$t->faktur_id]))->count();
                            $belumBayarCount = $tugasList->count() - $sudahInputCount;
                        @endphp

                        <!-- Kartu Biru Surat Penugasan (Button Selesai berada DI DALAM Kartu Biru agar tidak tertukar) -->
                        <div class="rounded-[1.75rem] p-4 text-white shadow-sm border border-white/15 space-y-3"
                             style="background: linear-gradient(135deg, #0E527B 0%, #07334F 100%) !important;">
                            <a href="{{ route('keuangan.inkaso.surat-penugasan.show', $routeKey) }}"
                               class="block transition active:scale-99">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <span class="text-[9px] font-bold text-amber-300 uppercase tracking-widest block">No. Surat Penugasan</span>
                                        <h3 class="text-sm font-black font-mono text-white tracking-tight">{{ $noSuratLabel }}</h3>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-[9px] font-bold text-white/70 uppercase block">Total Sisa Tagihan</span>
                                        <span class="text-base font-black text-white font-mono">Rp {{ number_format($totalPiutangAktifGrup, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <div class="mt-3 pt-2.5 border-t border-white/15 flex items-center justify-between gap-2 flex-wrap">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-white text-[#0E527B]">
                                            {{ $tugasList->count() }} Faktur
                                        </span>
                                        @if($sudahInputCount > 0)
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-[#FFB900] text-[#07334F]">
                                                {{ $sudahInputCount }} Pending QR
                                            </span>
                                        @endif
                                        @if($belumBayarCount > 0)
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/15 text-white border border-white/20">
                                                {{ $belumBayarCount }} Belum Bayar
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1 text-[10px] font-bold text-amber-300">
                                        <span>Lihat Rincian</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                    </div>
                                </div>

                                @if($totalTerkumpulGrup > 0)
                                    <div class="mt-1.5">
                                        <span class="text-[10px] font-bold text-amber-300 font-mono">
                                            Terinput: Rp {{ number_format($totalTerkumpulGrup, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif
                            </a>

                            <!-- Tombol Selesai Penagihan Surat Ini di DALAM Element Kartu Biru -->
                            @if($isGrup)
                                <form action="{{ route('keuangan.inkaso.penugasan.grup.selesai', $noSuratLabel) }}" method="POST"
                                      class="pt-2.5 border-t border-white/15"
                                      onsubmit="return confirm('Simpan & selesaikan surat penugasan {{ $noSuratLabel }} ({{ $tugasList->count() }} faktur)?\n\nSemua faktur dalam surat ini akan dipindahkan ke Riwayat dan nominal pembayaran terkunci.')">
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
                        </div>
                    @endforeach
                @elseif($suratTugasMenunggu->count() === 0)
                    <div class="bg-white rounded-3xl p-8 text-center shadow-xs border border-slate-100">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800">Tidak Ada Surat Penugasan Aktif</h4>
                        <p class="text-xs text-slate-500 mt-1">Semua surat penugasan sudah selesai ditagih atau belum ada tugas baru.</p>
                    </div>
                @endif
            </div>

            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- MENU 3: SETORAN BSK (KHUSUS FAKTUR YANG SUDAH BAYAR / INPUT QR)      -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div x-show="subFilter === 'setor_kasir'" class="space-y-3 pt-1">
                @if($setoranPending->count() > 0)
                    <div class="p-3 rounded-2xl bg-[#0E527B]/5 border border-[#0E527B]/15 text-[11px] text-slate-700 flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-[#0E527B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Tunjukkan QR/Barcode BSK beserta fisik uang ke Kasir untuk mengesahkan setoran.</span>
                    </div>

                    @foreach($setoranPending as $setoran)
                        @php $tugasLocked = $setoran->penugasan && $setoran->penugasan->status_tugas === 'selesai'; @endphp
                        <div class="bg-white rounded-[1.75rem] p-4 shadow-sm border border-slate-100 space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 font-mono text-[9px] font-bold text-[#0E527B]">
                                        {{ $setoran->no_bsk }}
                                    </span>
                                    <h3 class="text-xs font-extrabold text-slate-900 mt-1 truncate">{{ $setoran->nama_pembayar }}</h3>
                                    <p class="text-[11px] text-slate-500">
                                        {{ $setoran->faktur->no_faktur ?? '-' }} &bull; {{ $setoran->waktu_terima_lapangan ? $setoran->waktu_terima_lapangan->format('H:i') : '' }} WIB
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-sm font-black text-[#0E527B] block">
                                        Rp {{ number_format($setoran->nominal_titipan, 0, ',', '.') }}
                                    </span>
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-100 text-amber-900 mt-0.5">
                                        Menunggu Kasir
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('keuangan.inkaso.bsk.show', $setoran->no_bsk) }}"
                                   class="flex-1 h-9 px-3 rounded-xl bg-[#0E527B] hover:bg-[#07334F] text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-xs transition cursor-pointer">
                                    <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    <span>Tampilkan Barcode ke Kasir</span>
                                </a>
                                @if(!$tugasLocked)
                                    <button type="button"
                                            onclick="bukaModalEditBayar('{{ $setoran->id }}', '{{ $setoran->no_bsk }}', '{{ $setoran->faktur->no_faktur ?? '-' }}', '{{ addslashes($setoran->nama_pembayar) }}', '{{ (float) ($setoran->faktur->sisa_piutang ?? 0) }}', '{{ (float) $setoran->nominal_titipan }}', '{{ $setoran->metode_bayar }}', '{{ addslashes($setoran->keterangan_angsuran ?? '') }}')"
                                            class="h-9 px-3.5 rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-900 text-xs font-bold transition cursor-pointer shrink-0">
                                        Ubah
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="bg-white rounded-3xl p-8 text-center shadow-xs border border-slate-100">
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
        <!-- TAB 3: RIWAYAT (KLIK SURAT PENUGASAN -> ISI FAKTUR & LIHAT SEMUA FAKTUR)-->
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

            <!-- Search Pill + Circular Filter Button (60-30-10) -->
            <div class="space-y-2.5">
                <div class="flex items-center gap-2.5">
                    <div class="flex-1 flex items-center gap-2.5 bg-[#07334F] text-white rounded-full px-4 h-11 shadow-sm border border-[#0E527B]">
                        <svg class="w-4 h-4 text-[#FFB900] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text"
                               x-model="riwayatQuery"
                               placeholder="Cari No. Surat, Faktur, Pelanggan..."
                               class="w-full bg-transparent text-xs font-medium text-white placeholder-white/70 focus:outline-none">
                        <button type="button" x-show="riwayatQuery.length > 0" @click="riwayatQuery = ''" class="text-white/70 hover:text-white cursor-pointer" title="Hapus pencarian">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <button type="button"
                            @click="showRiwayatFilter = !showRiwayatFilter"
                            :class="(showRiwayatFilter || riwayatPeriod !== 'semua') ? 'bg-[#FFB900] text-[#07334F] ring-2 ring-[#FFB900]/30' : 'bg-[#0E527B] text-white hover:bg-[#07334F]'"
                            class="w-11 h-11 rounded-full flex items-center justify-center shrink-0 shadow-sm transition cursor-pointer relative"
                            title="Filter Riwayat">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                        <span x-show="riwayatPeriod !== 'semua'" class="w-2.5 h-2.5 rounded-full bg-[#07334F] border-2 border-[#FFB900] absolute -top-0.5 -right-0.5"></span>
                    </button>
                </div>

                <!-- Panel Filter Fleksibel -->
                <div x-show="showRiwayatFilter" x-cloak
                     class="bg-white rounded-2xl p-3.5 border border-slate-200 shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-extrabold text-[#0E527B] uppercase tracking-wider">Filter Periode</span>
                        <button type="button"
                                @click="riwayatPeriod = 'semua'; riwayatDateFrom = ''; riwayatDateTo = ''; riwayatQuery = ''"
                                class="text-[11px] font-bold text-[#0E527B] hover:underline cursor-pointer">
                            Reset
                        </button>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach([
                            'semua' => 'Semua',
                            '1h'    => '1 Hari',
                            '3h'    => '3 Hari',
                            '1b'    => '1 Bulan',
                            '1t'    => '1 Tahun',
                            'custom'=> 'Pilih Tanggal',
                        ] as $pKey => $pLabel)
                            <button type="button"
                                    @click="riwayatPeriod = '{{ $pKey }}'"
                                    :class="riwayatPeriod === '{{ $pKey }}' ? 'bg-[#0E527B] text-white font-extrabold shadow-2xs' : 'bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200'"
                                    class="px-3 py-1.5 rounded-full text-[11px] transition cursor-pointer">
                                {{ $pLabel }}
                            </button>
                        @endforeach
                    </div>
                    <div x-show="riwayatPeriod === 'custom'" class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-100">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">Dari Tanggal</label>
                            <input type="date" x-model="riwayatDateFrom" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs text-slate-800 bg-white">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">Sampai Tanggal</label>
                            <input type="date" x-model="riwayatDateTo" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs text-slate-800 bg-white">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Switcher Tab Riwayat: Surat Penugasan vs Semua Faktur -->
            <div class="grid grid-cols-2 gap-1.5 bg-slate-100 p-1 rounded-2xl">
                <button type="button"
                        @click="riwayatTab = 'surat'"
                        :class="riwayatTab === 'surat' ? 'bg-white text-[#0E527B] font-extrabold shadow-xs' : 'text-slate-600 font-semibold'"
                        class="py-2 px-3 rounded-xl text-xs transition cursor-pointer">
                    Surat Penugasan ({{ isset($riwayatSuratSelesai) ? $riwayatSuratSelesai->count() : 0 }})
                </button>
                <button type="button"
                        @click="riwayatTab = 'faktur'"
                        :class="riwayatTab === 'faktur' ? 'bg-white text-[#0E527B] font-extrabold shadow-xs' : 'text-slate-600 font-semibold'"
                        class="py-2 px-3 rounded-xl text-xs transition cursor-pointer">
                    Semua Faktur ({{ (isset($riwayatSemuaFaktur) ? $riwayatSemuaFaktur->count() : 0) + $riwayatSetoranSah->count() }})
                </button>
            </div>

            <!-- VIEW 1: ARSIP SURAT PENUGASAN (KLIK UNTUK MUNCULKAN SEMUA ISI FAKTURNYA) -->
            <div x-show="riwayatTab === 'surat'" class="space-y-3">
                @if(isset($riwayatSuratSelesai) && $riwayatSuratSelesai->count() > 0)
                    <span class="text-[11px] font-extrabold text-[#0E527B] uppercase tracking-wider px-1 block">
                        Arsip Surat Penugasan Selesai
                    </span>

                    @foreach($riwayatSuratSelesai as $noSurat => $suratItems)
                        @php
                            $rTotalTagihan = $suratItems->sum(fn($t) => $t->faktur->total_tagihan ?? 0);
                            $rTotalTerbayar = $suratItems->sum(fn($t) => max((float) ($t->faktur->total_terbayar ?? 0), (float) $t->nominal_diterima));
                            $rTotalSisa = $suratItems->sum(fn($t) => $t->faktur->sisa_piutang ?? 0);
                            $rTanggal = optional($suratItems->first()->updated_at ?? $suratItems->first()->created_at)->format('Y-m-d') ?? date('Y-m-d');
                            $rTanggalLabel = optional($suratItems->first()->updated_at ?? $suratItems->first()->created_at)->format('d M Y') ?? '-';
                            $rSearchText = trim($noSurat . ' ' . $suratItems->map(fn($t) => ($t->faktur->no_faktur ?? '') . ' ' . ($t->faktur->customer->nama_customer ?? ''))->implode(' '));
                        @endphp
                        <a href="{{ route('keuangan.inkaso.surat-penugasan.show', [$noSurat, 'from' => 'riwayat']) }}"
                           x-show="window.matchesRiwayatItem('{{ $rTanggal }}', '{{ addslashes($rSearchText) }}', riwayatQuery, riwayatPeriod, riwayatDateFrom, riwayatDateTo)"
                           class="block bg-white rounded-[1.75rem] p-4 shadow-sm border border-slate-100 hover:border-[#0E527B]/30 transition">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-9 h-9 rounded-full bg-sky-50 border border-sky-100 flex items-center justify-center text-[#0E527B] shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="text-xs font-black font-mono text-[#0E527B]">{{ $noSurat }}</span>
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#0E527B] text-white">
                                                {{ $suratItems->count() }} Faktur
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-medium">{{ $rTanggalLabel }}</span>
                                        </div>
                                        <span class="text-[11px] text-slate-500">
                                            Terkumpul: <strong class="text-[#0E527B] font-mono">Rp {{ number_format($rTotalTerbayar, 0, ',', '.') }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <span class="text-[10px] font-bold text-[#0E527B]">Rincian</span>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @elseif($riwayatSetoranSah->count() === 0)
                    <div class="bg-white rounded-3xl p-8 text-center shadow-xs border border-slate-100">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800">Belum Ada Riwayat Surat Selesai</h4>
                        <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">
                            Klik "Selesai Penagihan Surat Ini" pada penagihan atau lihat tab Semua Faktur untuk riwayat pembayaran sah.
                        </p>
                    </div>
                @endif
            </div>

            <!-- VIEW 2: SEMUA FAKTUR (GABUNGAN BSK SAH + SEMUA FAKTUR DARI SURAT SELESAI) -->
            <div x-show="riwayatTab === 'faktur'" class="space-y-3">
                @if(isset($riwayatSemuaFaktur) && $riwayatSemuaFaktur->count() > 0)
                    @foreach($riwayatSemuaFaktur as $fTask)
                        @php
                            $fSisa = (float) ($fTask->faktur->sisa_piutang ?? 0);
                            $fBayar = (float) ($fTask->faktur->total_terbayar ?? 0);
                            $bskLatest = $fTask->setoranLapangan->sortByDesc('id')->first();
                            $fTanggal = optional($fTask->updated_at ?? $fTask->created_at)->format('Y-m-d') ?? date('Y-m-d');
                            $fTanggalLabel = optional($fTask->updated_at ?? $fTask->created_at)->format('d M Y') ?? '-';
                            $fSearchText = trim(($fTask->faktur->no_faktur ?? '') . ' ' . ($fTask->no_surat_grup ?? '') . ' ' . ($fTask->faktur->customer->nama_customer ?? '') . ' ' . ($bskLatest->no_bsk ?? ''));
                        @endphp
                        <div x-show="window.matchesRiwayatItem('{{ $fTanggal }}', '{{ addslashes($fSearchText) }}', riwayatQuery, riwayatPeriod, riwayatDateFrom, riwayatDateTo)"
                             class="bg-white rounded-[1.5rem] p-4 shadow-sm border border-slate-100 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-[#0E527B] font-mono text-[9px] font-bold">
                                        {{ $fTask->faktur->no_faktur ?? '-' }}
                                    </span>
                                    @if($fTask->no_surat_grup)
                                        <span class="text-[9px] font-mono text-slate-400">{{ $fTask->no_surat_grup }}</span>
                                    @endif
                                    @if($fSisa <= 0)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#0E527B] text-white">Lunas</span>
                                    @elseif($bskLatest && $bskLatest->status_approval === 'pending')
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#FFB900] text-[#07334F]">Menunggu Kasir</span>
                                    @elseif($fBayar > 0)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-sky-50 text-[#0E527B]">Cicilan</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 text-slate-600">Belum Bayar</span>
                                    @endif
                                </div>
                                <h4 class="text-xs font-extrabold text-slate-900 mt-1 truncate">{{ $fTask->faktur->customer->nama_customer ?? '-' }}</h4>
                                <p class="text-[10px] text-slate-500">
                                    {{ $fTanggalLabel }} &bull; Tagihan: Rp {{ number_format($fTask->faktur->total_tagihan ?? 0, 0, ',', '.') }}
                                    @if($fSisa > 0) &bull; Sisa: Rp {{ number_format($fSisa, 0, ',', '.') }} @endif
                                </p>
                            </div>
                            <div class="text-right shrink-0 space-y-1">
                                <span class="text-xs font-black font-mono block {{ $fBayar > 0 || $bskLatest ? 'text-[#0E527B]' : 'text-slate-400' }}">
                                    Rp {{ number_format($bskLatest && $bskLatest->status_approval === 'pending' ? $bskLatest->nominal_titipan : $fBayar, 0, ',', '.') }}
                                </span>
                                @if($bskLatest)
                                    <a href="{{ route('keuangan.inkaso.bsk.show', $bskLatest->no_bsk) }}" class="inline-block px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-[#0E527B] text-[10px] font-bold">
                                        Lihat Bukti
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif

                @foreach($riwayatSetoranSah as $setoran)
                    @if(!isset($riwayatSemuaFaktur) || !$riwayatSemuaFaktur->contains('faktur_id', $setoran->faktur_id))
                        @php
                            $sTanggal = optional($setoran->approved_at ?? $setoran->created_at)->format('Y-m-d') ?? date('Y-m-d');
                            $sTanggalLabel = optional($setoran->approved_at ?? $setoran->created_at)->format('d M Y') ?? '-';
                            $sSearchText = trim(($setoran->faktur->no_faktur ?? '') . ' ' . $setoran->nama_pembayar . ' ' . ($setoran->no_bsk ?? '') . ' ' . ($setoran->no_kuitansi_kasir ?? ''));
                        @endphp
                        <div x-show="window.matchesRiwayatItem('{{ $sTanggal }}', '{{ addslashes($sSearchText) }}', riwayatQuery, riwayatPeriod, riwayatDateFrom, riwayatDateTo)"
                             class="bg-white rounded-[1.5rem] p-4 shadow-sm border border-slate-100 space-y-2.5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-[#0E527B] font-mono text-[9px] font-bold">
                                        {{ $setoran->faktur->no_faktur ?? '-' }}
                                    </span>
                                    <h3 class="text-xs font-extrabold text-slate-900 mt-1 truncate">{{ $setoran->nama_pembayar }}</h3>
                                    <p class="text-[10px] text-slate-500">{{ $sTanggalLabel }} &bull; BKM: {{ $setoran->no_kuitansi_kasir ?? '-' }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-xs font-black text-[#0E527B] block font-mono">Rp {{ number_format($setoran->nominal_titipan, 0, ',', '.') }}</span>
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#0E527B] text-white mt-0.5">Disetujui Kasir</span>
                                </div>
                            </div>
                            <a href="{{ route('keuangan.inkaso.bsk.show', $setoran->no_bsk) }}"
                               class="w-full h-9 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold flex items-center justify-center transition">
                                Lihat Bukti Setoran
                            </a>
                        </div>
                    @endif
                @endforeach

                @if((!isset($riwayatSemuaFaktur) || $riwayatSemuaFaktur->count() === 0) && $riwayatSetoranSah->count() === 0)
                    <div class="bg-white rounded-3xl p-8 text-center shadow-xs border border-slate-100">
                        <h4 class="text-sm font-bold text-slate-800">Belum Ada Riwayat Pembayaran Sah</h4>
                        <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">
                            Faktur yang sudah selesai ditagih atau disetujui Kasir akan muncul di sini.
                        </p>
                    </div>
                @endif
            </div>

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
                            class="w-full h-11 rounded-2xl border border-slate-300 hover:border-amber-300 hover:bg-amber-50 text-slate-700 hover:text-[#07334F] text-xs font-extrabold flex items-center justify-center gap-2 transition cursor-pointer active:scale-98">
                        <svg class="w-4 h-4 text-[#FFB900]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Keluar dari Akun Inkaso</span>
                    </button>
                </div>
            </div>

            <!-- Spacer Ekstra Bawah -->
            <div class="h-44 w-full shrink-0 pointer-events-none" style="height: 180px; min-height: 180px;" aria-hidden="true"></div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- FLOATING BOTTOM DOCK NAVIGATION (PERSIS FOTO 2 - DOCK BIRU #0E527B)     -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <nav class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 inline-flex items-center gap-2 sm:gap-2.5 p-2 px-2.5 rounded-full bg-[#0E527B] shadow-[0_16px_40px_rgba(14,82,123,0.50)] border border-white/20 select-none backdrop-blur-xl transition-all duration-300">
            
            {{-- 1. Beranda --}}
            <button type="button" 
                    @click="mobileTab = 'beranda'; window.scrollTo({top: 0, behavior: 'smooth'})" 
                    class="cursor-pointer transition-all duration-300 select-none shrink-0"
                    :class="mobileTab === 'beranda' ? 'bg-white text-[#0E527B] px-5 py-2.5 rounded-full font-black text-sm shadow-md flex items-center gap-2.5' : 'w-11 h-11 rounded-full bg-white/15 hover:bg-white/25 active:bg-white/30 flex items-center justify-center text-white'">
                <svg class="w-5 h-5 shrink-0" :class="mobileTab === 'beranda' ? 'text-[#0E527B]' : 'text-white'" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                <span x-show="mobileTab === 'beranda'" class="whitespace-nowrap font-extrabold text-xs sm:text-sm tracking-tight">Beranda</span>
            </button>

            {{-- 2. Penagihan --}}
            <button type="button" 
                    @click="mobileTab = 'penagihan'; subFilter = 'semua_faktur'; window.scrollTo({top: 0, behavior: 'smooth'})" 
                    class="cursor-pointer transition-all duration-300 select-none shrink-0 relative"
                    :class="mobileTab === 'penagihan' ? 'bg-white text-[#0E527B] px-5 py-2.5 rounded-full font-black text-sm shadow-md flex items-center gap-2.5' : 'w-11 h-11 rounded-full bg-white/15 hover:bg-white/25 active:bg-white/30 flex items-center justify-center text-white'">
                <div class="relative flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 shrink-0" :class="mobileTab === 'penagihan' ? 'text-[#0E527B]' : 'text-white'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    @if(($suratTugasMenunggu->count() + $tagihanAktif->count()) > 0)
                        <span x-show="mobileTab !== 'penagihan'" class="absolute -top-1.5 -right-1.5 min-w-[16px] h-4 px-1 bg-amber-400 text-amber-950 rounded-full text-[9px] font-black flex items-center justify-center ring-2 ring-[#0E527B]">
                            {{ $suratTugasMenunggu->count() + $tagihanAktif->count() }}
                        </span>
                    @endif
                </div>
                <span x-show="mobileTab === 'penagihan'" class="whitespace-nowrap font-extrabold text-xs sm:text-sm tracking-tight">Penagihan</span>
                @if(($suratTugasMenunggu->count() + $tagihanAktif->count()) > 0)
                    <span x-show="mobileTab === 'penagihan'" class="px-1.5 py-0.5 rounded-full text-[10px] font-black bg-[#0E527B] text-white">
                        {{ $suratTugasMenunggu->count() + $tagihanAktif->count() }}
                    </span>
                @endif
            </button>

            {{-- 3. Riwayat --}}
            <button type="button" 
                    @click="mobileTab = 'riwayat'; window.scrollTo({top: 0, behavior: 'smooth'})" 
                    class="cursor-pointer transition-all duration-300 select-none shrink-0"
                    :class="mobileTab === 'riwayat' ? 'bg-white text-[#0E527B] px-5 py-2.5 rounded-full font-black text-sm shadow-md flex items-center gap-2.5' : 'w-11 h-11 rounded-full bg-white/15 hover:bg-white/25 active:bg-white/30 flex items-center justify-center text-white'">
                <svg class="w-5 h-5 shrink-0" :class="mobileTab === 'riwayat' ? 'text-[#0E527B]' : 'text-white'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span x-show="mobileTab === 'riwayat'" class="whitespace-nowrap font-extrabold text-xs sm:text-sm tracking-tight">Riwayat</span>
            </button>

            {{-- 4. Profil --}}
            <button type="button" 
                    @click="mobileTab = 'profil'; window.scrollTo({top: 0, behavior: 'smooth'})" 
                    class="cursor-pointer transition-all duration-300 select-none shrink-0"
                    :class="mobileTab === 'profil' ? 'bg-white text-[#0E527B] px-5 py-2.5 rounded-full font-black text-sm shadow-md flex items-center gap-2.5' : 'w-11 h-11 rounded-full bg-white/15 hover:bg-white/25 active:bg-white/30 flex items-center justify-center text-white'">
                <svg class="w-5 h-5 shrink-0" :class="mobileTab === 'profil' ? 'text-[#0E527B]' : 'text-white'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span x-show="mobileTab === 'profil'" class="whitespace-nowrap font-extrabold text-xs sm:text-sm tracking-tight">Profil</span>
            </button>
        </nav>

        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <!-- MODAL 1: INPUT PEMBAYARAN (PROTEKSI DOUBLE-CLICK & CEGAH MULTI BSK)     -->
        <!-- ═══════════════════════════════════════════════════════════════════════ -->
        <div id="modalBayar" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-[2px] p-4 hidden">
            <div class="bg-white rounded-[1.75rem] max-w-md w-full p-5 shadow-2xl border border-slate-200 space-y-4 animate-in fade-in zoom-in duration-150">
                <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-extrabold text-[#0E527B] tracking-tight">
                            Input Pembayaran
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Buat bukti setoran pelanggan (BSK)</p>
                    </div>
                    <button type="button" onclick="tutupModalBayar()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center cursor-pointer transition" title="Tutup">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('keuangan.inkaso.setoran-lapangan.store') }}" 
                      method="POST" 
                      onsubmit="if (!validateDotNominal('inputNominalBayarDisplay', 'inputNominalBayar')) return false; const btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.classList.add('opacity-75'); btn.innerHTML = '<span>Menyimpan...</span>';"
                      class="space-y-4">
                    @csrf
                    <input type="hidden" name="faktur_id" id="bayarFakturId">
                    <input type="hidden" name="penugasan_id" id="bayarPenugasanId">
                    <input type="hidden" name="nominal_bayar" id="inputNominalBayar">

                    <!-- Info Ringkas Faktur -->
                    <div class="bg-slate-50/90 border border-slate-200/80 rounded-2xl p-3.5 space-y-2 text-xs">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-slate-500 shrink-0">No. Faktur</span>
                            <span class="font-mono font-bold text-[#0E527B] text-right" id="bayarFakturNo">-</span>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <span class="text-slate-500 shrink-0">Pelanggan</span>
                            <span class="font-bold text-slate-800 text-right leading-snug" id="bayarCustomer">-</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 pt-2 border-t border-slate-200/80">
                            <span class="font-bold text-slate-700">Total Piutang</span>
                            <span class="font-black text-sm text-[#0E527B] font-mono" id="bayarSisaPiutangText">Rp 0</span>
                        </div>
                    </div>

                    <!-- Input Nominal -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-bold text-slate-700">Jumlah Bayar (Rp) <span class="text-[#FFB900]">*</span></label>
                            <button type="button" onclick="isiBayarLunas()" class="text-[11px] font-bold text-[#0E527B] hover:text-[#07334F] hover:underline cursor-pointer">
                                Bayar Lunas
                            </button>
                        </div>
                        <div class="flex items-center gap-2 w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white focus-within:ring-2 focus-within:ring-[#0E527B] focus-within:border-[#0E527B] transition">
                            <span class="text-xs font-extrabold text-slate-400">Rp</span>
                            <input type="text"
                                   inputmode="numeric"
                                   id="inputNominalBayarDisplay"
                                   required
                                   placeholder="0"
                                   oninput="syncDotNominal('inputNominalBayarDisplay', 'inputNominalBayar')"
                                   class="w-full text-base font-black font-mono text-slate-900 bg-transparent focus:outline-none">
                        </div>
                    </div>

                    <!-- Metode Bayar -->
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

                    <!-- Keterangan Wajib -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan <span class="text-[#FFB900]">*</span></label>
                        <input type="text" 
                               name="keterangan_angsuran" 
                               id="inputKetAngsuran"
                               required
                               placeholder="Contoh: Angsuran ke-1 atau Pelunasan faktur" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#0E527B] focus:border-[#0E527B] text-xs text-slate-800 bg-white">
                    </div>

                    <div class="grid grid-cols-2 gap-2.5 pt-1">
                        <button type="button" onclick="tutupModalBayar()" class="h-11 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-bold cursor-pointer transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="h-11 rounded-xl bg-[#0E527B] hover:bg-[#07334F] text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-sm transition cursor-pointer active:scale-98">
                            <span>Simpan &amp; Buat QR</span>
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
                        <h3 class="text-sm font-extrabold text-[#0E527B] uppercase tracking-tight">
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
                            Alasan Penolakan <span class="text-[#FFB900]">*</span>
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
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#0E527B] text-xs text-slate-800"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <button type="button" onclick="tutupModalTolak()" class="py-2.5 px-3 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="py-2.5 px-3 rounded-xl bg-[#FFB900] hover:bg-amber-400 text-[#07334F] text-xs font-black shadow-xs transition cursor-pointer">
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
                                <label class="inline-flex items-center gap-1.5 text-xs text-[#0E527B] hover:text-[#07334F] cursor-pointer font-bold">
                                    <input type="checkbox" name="hapus_foto" value="1" class="rounded border-slate-300 text-[#0E527B] focus:ring-[#0E527B]">
                                    <span>Hapus Foto Saat Ini (Kembali ke Default WA)</span>
                                </label>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Nama Lengkap Petugas <span class="text-[#FFB900]">*</span>
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

    <!-- Script Interaksi Modal Mobile & Filter Riwayat -->
    <script>
        let currentSisaPiutang = 0;
        let editMaxPiutang = 0;

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

        function hubungiKosong(nama) {
            const pesan = 'Nomor telepon untuk ' + nama + ' belum tercatat pada data pelanggan.';
            if (window.Swal) {
                Swal.fire({ icon: 'info', title: 'Nomor Belum Tersedia', text: pesan, confirmButtonColor: '#0E527B' });
            } else {
                alert(pesan);
            }
        }

        window.matchesRiwayatItem = function(dateStr, searchText, query, period, dateFrom, dateTo) {
            const q = String(query || '').toLowerCase().trim();
            if (q && !String(searchText || '').toLowerCase().includes(q)) {
                return false;
            }
            if (!period || period === 'semua') {
                return true;
            }
            if (!dateStr) {
                return true;
            }
            if (period === 'custom') {
                if (dateFrom && dateStr < dateFrom) return false;
                if (dateTo && dateStr > dateTo) return false;
                return true;
            }
            const itemDate = new Date(dateStr + 'T00:00:00');
            if (isNaN(itemDate.getTime())) return true;
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const cutoff = new Date(today);
            if (period === '1h') cutoff.setDate(today.getDate());
            else if (period === '3h') cutoff.setDate(today.getDate() - 2);
            else if (period === '1b') cutoff.setMonth(today.getMonth() - 1);
            else if (period === '1t') cutoff.setFullYear(today.getFullYear() - 1);
            return itemDate >= cutoff;
        };

        function bukaModalBayar(fakturId, penugasanId, fakturNo, customer, sisaPiutang) {
            currentSisaPiutang = parseFloat(sisaPiutang) || 0;
            document.getElementById('bayarFakturId').value = fakturId;
            document.getElementById('bayarPenugasanId').value = penugasanId || '';
            document.getElementById('bayarFakturNo').innerText = fakturNo;
            document.getElementById('bayarCustomer').innerText = customer;
            document.getElementById('bayarSisaPiutangText').innerText = 'Rp ' + Number(currentSisaPiutang).toLocaleString('id-ID');
            setDotNominalValue('inputNominalBayarDisplay', 'inputNominalBayar', '');
            document.getElementById('inputKetAngsuran').value = '';
            document.getElementById('modalBayar').classList.remove('hidden');
        }

        function isiBayarLunas() {
            if (currentSisaPiutang > 0) {
                setDotNominalValue('inputNominalBayarDisplay', 'inputNominalBayar', currentSisaPiutang);
                const ket = document.getElementById('inputKetAngsuran');
                if (!ket.value.trim()) ket.value = 'Pelunasan faktur';
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

        function bukaModalEditBayar(setoranId, noBsk, fakturNo, customer, sisaPiutang, nominalSaatIni, metodeBayar, keterangan) {
            editMaxPiutang = parseFloat(sisaPiutang) || 0;
            const form = document.getElementById('formEditBayar');
            form.action = "{{ url('keuangan/inkaso/setoran-lapangan') }}/" + setoranId;
            document.getElementById('editNoBsk').innerText = noBsk;
            document.getElementById('editFakturNo').innerText = fakturNo;
            document.getElementById('editCustomer').innerText = customer;
            document.getElementById('editSisaPiutangText').innerText = 'Rp ' + Number(editMaxPiutang).toLocaleString('id-ID');
            setDotNominalValue('editNominalBayarDisplay', 'editNominalBayar', nominalSaatIni);
            document.getElementById('editKetAngsuran').value = keterangan || '';

            const metodeRadios = document.querySelectorAll('input[name="metode_bayar_edit"]');
            metodeRadios.forEach(r => {
                r.checked = (r.value === (metodeBayar || 'tunai'));
            });
            document.getElementById('editMetodeHidden').value = metodeBayar || 'tunai';

            document.getElementById('modalEditBayar').classList.remove('hidden');
        }

        function isiEditLunas() {
            if (editMaxPiutang > 0) {
                setDotNominalValue('editNominalBayarDisplay', 'editNominalBayar', editMaxPiutang);
                const ket = document.getElementById('editKetAngsuran');
                if (!ket.value.trim()) ket.value = 'Pelunasan faktur';
            }
        }

        function tutupModalEditBayar() {
            document.getElementById('modalEditBayar').classList.add('hidden');
        }
    </script>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- MODAL EDIT PEMBAYARAN BSK (SELAGI SURAT BELUM DISIMPAN/SELESAI)         -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div id="modalEditBayar" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-[2px] p-4 hidden">
        <div class="bg-white rounded-[1.75rem] max-w-md w-full p-5 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-base font-extrabold text-[#0E527B] tracking-tight">
                            Ubah Pembayaran
                        </h3>
                        <span id="editNoBsk" class="font-mono text-[10px] font-bold px-2 py-0.5 rounded-full bg-sky-50 text-[#0E527B] border border-sky-100">-</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-0.5">Sesuaikan data sebelum surat diselesaikan</p>
                </div>
                <button type="button" onclick="tutupModalEditBayar()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center cursor-pointer transition" title="Tutup">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="formEditBayar" method="POST"
                  onsubmit="return validateDotNominal('editNominalBayarDisplay', 'editNominalBayar')" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="metode_bayar" id="editMetodeHidden" value="tunai">
                <input type="hidden" name="nominal_bayar" id="editNominalBayar">

                <div class="bg-slate-50/90 border border-slate-200/80 rounded-2xl p-3.5 space-y-2 text-xs">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-slate-500 shrink-0">No. Faktur</span>
                        <span class="font-mono font-bold text-[#0E527B] text-right" id="editFakturNo">-</span>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <span class="text-slate-500 shrink-0">Pelanggan</span>
                        <span class="font-bold text-slate-800 text-right leading-snug" id="editCustomer">-</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 pt-2 border-t border-slate-200/80">
                        <span class="font-bold text-slate-700">Total Piutang</span>
                        <span class="font-black text-sm text-[#0E527B] font-mono" id="editSisaPiutangText">Rp 0</span>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-bold text-slate-700">Jumlah Bayar (Rp) <span class="text-[#FFB900]">*</span></label>
                        <button type="button" onclick="isiEditLunas()" class="text-[11px] font-bold text-[#0E527B] hover:text-[#07334F] hover:underline cursor-pointer">
                            Bayar Lunas
                        </button>
                    </div>
                    <div class="flex items-center gap-2 w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white focus-within:ring-2 focus-within:ring-[#0E527B] focus-within:border-[#0E527B] transition">
                        <span class="text-xs font-extrabold text-slate-400">Rp</span>
                        <input type="text" inputmode="numeric" id="editNominalBayarDisplay" required placeholder="0"
                               oninput="syncDotNominal('editNominalBayarDisplay', 'editNominalBayar')"
                               class="w-full text-base font-black font-mono text-slate-900 bg-transparent focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-2.5">
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 has-checked:border-[#0E527B] has-checked:bg-sky-50/60 transition">
                            <input type="radio" name="metode_bayar_edit" value="tunai" onchange="document.getElementById('editMetodeHidden').value=this.value" class="text-[#0E527B] focus:ring-[#0E527B]">
                            <span class="text-xs font-bold text-slate-800">Tunai</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 has-checked:border-[#0E527B] has-checked:bg-sky-50/60 transition">
                            <input type="radio" name="metode_bayar_edit" value="transfer" onchange="document.getElementById('editMetodeHidden').value=this.value" class="text-[#0E527B] focus:ring-[#0E527B]">
                            <span class="text-xs font-bold text-slate-800">Transfer Bank</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan <span class="text-[#FFB900]">*</span></label>
                    <input type="text" name="keterangan_angsuran" id="editKetAngsuran" required
                           placeholder="Contoh: Revisi nominal angsuran"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#0E527B] focus:border-[#0E527B] text-xs text-slate-800 bg-white">
                </div>

                <div class="grid grid-cols-2 gap-2.5 pt-1">
                    <button type="button" onclick="tutupModalEditBayar()" class="h-11 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-bold cursor-pointer transition">
                        Batal
                    </button>
                    <button type="submit" class="h-11 rounded-xl bg-[#0E527B] hover:bg-[#07334F] text-white text-xs font-bold shadow-sm transition cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-mobile-inkaso-layout>
