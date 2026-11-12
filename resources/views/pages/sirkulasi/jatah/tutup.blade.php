<x-app-layout>
    <div class="space-y-5" x-data="{ openModalRequestHarian: false, openModalRequestBulanan: false, kategoriHarian: 'Penjualan Baru / Susulan Masuk', kategoriBulanan: 'Penjualan Baru / Susulan Masuk' }">
        @php
            $canDirectUnlock = $canDirectUnlock ?? (auth()->user() && (auth()->user()->isAdmin() || auth()->user()->hasRole('akuntansi', 'accounting', 'keuangan')));
        @endphp

        <!-- Header Halaman (Hanya Tampil di Layar, Standar Divisi Iklan 60-30-10) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Tutup Data Penjualan
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Penguncian data penjualan koran harian dan bulanan untuk sinkronisasi Kasir &amp; Akuntansi.
                    </p>
                </div>
            </div>

            <!-- Filter Controls Sesuai Mode Aktif -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                @if($mode === 'bulanan')
                    <form method="GET" action="{{ route('sirkulasi.jatah.tutup') }}" class="flex items-center gap-2">
                        <input type="hidden" name="mode" value="bulanan">
                        <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                            Filter Bulan
                        </button>
                    </form>
                @elseif($mode === 'harian')
                    <form method="GET" action="{{ route('sirkulasi.jatah.tutup') }}" class="flex items-center gap-2">
                        <input type="hidden" name="mode" value="harian">
                        <input type="date" name="tanggal" value="{{ $tanggalPilihan }}" class="h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                            Filter Tanggal
                        </button>
                    </form>
                @else
                    <form method="GET" action="{{ route('sirkulasi.jatah.tutup') }}" class="flex flex-wrap items-center gap-2">
                        <input type="hidden" name="mode" value="semua">
                        <input type="date" name="tanggal" value="{{ $tanggalPilihan }}" class="h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" title="Tanggal Harian">
                        <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" title="Periode Bulanan">
                        <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                            Terapkan
                        </button>
                    </form>
                @endif

                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak Berita Acara</span>
                </button>
            </div>
        </div>

        <!-- Tab Navigasi Mode -->
        <div class="print:hidden bg-white border border-slate-200 p-1.5 rounded-xl flex flex-wrap gap-1.5 max-w-fit shadow-xs">
            <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'semua', 'tanggal' => $tanggalPilihan, 'bulan' => $bulanPilihan]) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-semibold rounded-lg transition {{ $mode === 'semua' ? 'bg-kp-blue-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span>Semua (Harian &amp; Bulanan)</span>
            </a>

            <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $tanggalPilihan]) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-semibold rounded-lg transition {{ $mode === 'harian' ? 'bg-kp-blue-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Penjualan Harian</span>
            </a>

            <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'bulanan', 'bulan' => $bulanPilihan]) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-semibold rounded-lg transition {{ $mode === 'bulanan' ? 'bg-kp-blue-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>Penjualan Bulanan</span>
            </a>
        </div>

        @if(session('success'))
            <div class="print:hidden p-3.5 bg-kp-blue-50 border border-kp-blue-200 rounded-xl text-sm font-semibold text-kp-blue-700 flex items-center justify-between gap-2 shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="window.print()" class="h-8 px-3 text-xs font-bold rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white transition inline-flex items-center gap-1 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak Berita Acara</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="print:hidden p-3.5 bg-slate-100 border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Ringkasan Cepat Dual Overview (Harian & Bulanan, 60-30-10) -->
        <div class="print:hidden grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Ringkasan Harian -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-3.5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </span>
                        <div>
                            <span class="text-xs font-semibold text-kp-blue-700 uppercase tracking-wider block">Tutup Penjualan Harian</span>
                            <h3 class="text-sm font-bold text-slate-900">{{ $harianSaatIni->tanggal }}</h3>
                        </div>
                    </div>
                    @if($harianSaatIni->is_locked)
                        @if($permintaanBukaHarian)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Menunggu Akuntansi</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Terkunci</span>
                            </span>
                        @endif
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                            <span>Belum Tutup Harian</span>
                        </span>
                    @endif
                </div>
                <div class="grid grid-cols-3 gap-2.5">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                        <span class="text-slate-500 text-xs block">Oplah</span>
                        <div class="font-bold text-slate-900 font-mono text-sm mt-0.5">{{ number_format($harianSaatIni->total_oplah_cetak, 0, ',', '.') }} eks</div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                        <span class="text-slate-500 text-xs block">Retur Fisik</span>
                        <div class="font-bold text-slate-900 font-mono text-sm mt-0.5">{{ number_format($harianSaatIni->total_retur_eks, 0, ',', '.') }} eks</div>
                    </div>
                    <div class="p-3 bg-kp-blue-50 rounded-xl border border-kp-blue-200">
                        <span class="text-kp-blue-700 text-xs font-semibold block">Omset Bersih</span>
                        <div class="font-bold text-kp-blue-700 font-mono text-sm mt-0.5">Rp {{ number_format($harianSaatIni->omset_bersih, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Bulanan -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-3.5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        </span>
                        <div>
                            <span class="text-xs font-semibold text-kp-blue-700 uppercase tracking-wider block">Tutup Penjualan Bulanan</span>
                            <h3 class="text-sm font-bold text-slate-900">{{ $periodeSaatIni->periode }}</h3>
                        </div>
                    </div>
                    @if($periodeSaatIni->is_locked)
                        @if($permintaanBukaBulanan)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Menunggu Akuntansi</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Terkunci</span>
                            </span>
                        @endif
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                            <span>Belum Tutup Bulan</span>
                        </span>
                    @endif
                </div>
                <div class="grid grid-cols-3 gap-2.5">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                        <span class="text-slate-500 text-xs block">Oplah Akumulatif</span>
                        <div class="font-bold text-slate-900 font-mono text-sm mt-0.5">{{ number_format($periodeSaatIni->total_oplah_cetak, 0, ',', '.') }} eks</div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                        <span class="text-slate-500 text-xs block">Retur BAPR</span>
                        <div class="font-bold text-slate-900 font-mono text-sm mt-0.5">{{ number_format($periodeSaatIni->total_retur_eks, 0, ',', '.') }} eks</div>
                    </div>
                    <div class="p-3 bg-kp-blue-50 rounded-xl border border-kp-blue-200">
                        <span class="text-kp-blue-700 text-xs font-semibold block">Omset Bersih</span>
                        <div class="font-bold text-kp-blue-700 font-mono text-sm mt-0.5">Rp {{ number_format($periodeSaatIni->omset_bersih, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOP DOKUMEN CETAK RESMI (HANYA TAMPIL SAAT CETAK) -->
        <div class="hidden print:block mb-4 border-b-2 border-slate-900 pb-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-extrabold tracking-wider uppercase text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                    <p class="text-[11px] font-bold text-slate-700">DIVISI SIRKULASI &amp; DISTRIBUSI &mdash; BERITA ACARA TUTUP DATA PENJUALAN</p>
                    <p class="text-[10px] text-slate-500">Jl. Sutisna Senjaya No. 120 Tasikmalaya &bull; Telp (0265) 331888 &bull; sirkulasi@kabarpriangan.co.id</p>
                </div>
                <div class="text-right">
                    <span class="inline-block px-2 py-0.5 border border-slate-400 font-mono text-[10px] font-bold text-slate-800 uppercase">
                        KODE DOK: SIRK-CLS-{{ $mode === 'bulanan' ? 'MTH' : 'DAY' }}
                    </span>
                    <p class="text-[10px] text-slate-600 mt-1">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</p>
                    <p class="text-[10px] text-slate-600">Operator: {{ Auth::user()->name ?? 'Staf Sirkulasi' }}</p>
                </div>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-300 flex items-center justify-between">
                <div>
                    <h1 class="text-sm font-black text-slate-900 uppercase tracking-tight">
                        BERITA ACARA PENUTUPAN DATA PENJUALAN {{ $mode === 'bulanan' ? 'BULANAN' : 'HARIAN' }}
                    </h1>
                    <p class="text-[11px] text-slate-700">
                        @if($mode === 'bulanan')
                            Periode Bulan: <strong class="text-slate-900">{{ $periodeSaatIni->periode }}</strong>
                        @else
                            Edisi Terbit: <strong class="text-slate-900">{{ $harianSaatIni->tanggal }}</strong>
                        @endif
                        &bull; Status: <strong class="text-slate-900">{{ $mode === 'bulanan' ? $periodeSaatIni->status_label : $harianSaatIni->status_label }}</strong>
                    </p>
                </div>
                <div class="text-right text-[11px] font-semibold text-slate-700">
                    Oplah: <span class="font-black text-slate-900 text-xs">{{ number_format($mode === 'bulanan' ? $periodeSaatIni->total_oplah_cetak : $harianSaatIni->total_oplah_cetak, 0, ',', '.') }} eks</span>
                    &bull; Omset Bersih: <span class="font-bold text-slate-900 text-xs">Rp {{ number_format($mode === 'bulanan' ? $periodeSaatIni->omset_bersih : $harianSaatIni->omset_bersih, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- =================================================================== --}}
        {{-- BAGIAN 1: TUTUP DATA PENJUALAN HARIAN (PER HARI)                    --}}
        {{-- =================================================================== --}}
        @if($mode === 'harian' || $mode === 'semua')
            <div class="space-y-4">
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3.5 border-b border-slate-100 gap-3">
                        <div>
                            <span class="text-xs font-semibold text-kp-blue-700 uppercase tracking-wider block">Tutup Penjualan Harian ({{ $harianSaatIni->tanggal_key }})</span>
                            <h2 class="text-base font-bold text-slate-900 mt-0.5">{{ $harianSaatIni->tanggal }}</h2>
                        </div>
                        <div>
                            @if($harianSaatIni->is_locked)
                                <div class="sm:text-right">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                        <span>{{ $harianSaatIni->status_label }}</span>
                                    </span>
                                    <p class="text-xs text-slate-400 mt-1 font-mono">Ditutup: {{ $harianSaatIni->closed_at }} ({{ $harianSaatIni->closed_by }})</p>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                    <span>{{ $harianSaatIni->status_label }}</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80">
                            <span class="text-xs text-slate-500 block">Oplah Cetak Harian</span>
                            <div class="text-base font-bold text-slate-900 mt-1 font-mono">{{ number_format($harianSaatIni->total_oplah_cetak, 0, ',', '.') }} eks</div>
                        </div>
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80">
                            <span class="text-xs text-slate-500 block">Koran Terjual (Nett)</span>
                            <div class="text-base font-bold text-kp-blue-700 mt-1 font-mono">{{ number_format($harianSaatIni->total_terjual, 0, ',', '.') }} eks</div>
                        </div>
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80">
                            <span class="text-xs text-slate-500 block">Total Retur ({!! $harianSaatIni->rasio_retur !!}%)</span>
                            <div class="text-base font-bold text-slate-900 mt-1 font-mono">{{ number_format($harianSaatIni->total_retur_eks, 0, ',', '.') }} eks</div>
                        </div>
                        <div class="p-3.5 bg-kp-blue-50 rounded-xl border border-kp-blue-200">
                            <span class="text-xs text-kp-blue-700 font-semibold block">Omset Bersih</span>
                            <div class="text-base font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($harianSaatIni->omset_bersih, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    @if($harianSaatIni->is_locked && $permintaanBukaHarian)
                        <div class="print:hidden p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs sm:text-sm font-bold text-slate-900">Permintaan Buka Kunci Diajukan ke Akuntansi</span>
                                            <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">{{ $permintaanBukaHarian->id }}</span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            Edisi {{ $harianSaatIni->tanggal }} tetap terkunci hingga disetujui Bagian Akuntansi.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <form method="POST" action="{{ route('sirkulasi.jatah.tutup.cancel-request-unlock') }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan permohonan buka kunci transaksi harian ini?')">
                                        @csrf
                                        <input type="hidden" name="mode" value="harian">
                                        <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                                        <button type="submit" class="h-9 px-3.5 text-xs font-semibold rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                                            Batalkan Permintaan
                                        </button>
                                    </form>
                                    @if($canDirectUnlock)
                                        <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                                            @csrf
                                            <input type="hidden" name="mode" value="harian">
                                            <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                                            <input type="hidden" name="action" value="unlock">
                                            <button type="submit" onclick="return confirm('Buka kunci penjualan harian tanggal {{ $tanggalPilihan }} sekarang?')" class="h-9 px-3.5 text-xs font-bold rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white transition cursor-pointer inline-flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                <span>Setujui &amp; Buka Kunci</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="print:hidden p-4 {{ $harianSaatIni->is_locked ? 'bg-slate-50 border-slate-200' : 'bg-kp-blue-50/70 border-kp-blue-200' }} border rounded-xl flex flex-col sm:flex-row items-center justify-between gap-3 text-xs sm:text-sm">
                            <div class="text-slate-700 leading-relaxed">
                                @if($harianSaatIni->is_locked)
                                    <strong class="font-bold text-slate-900">Status Harian: Terkunci.</strong> Pembukaan transaksi memerlukan otorisasi Divisi Akuntansi.
                                @else
                                    <strong class="font-bold text-slate-900">Status Harian: Terbuka.</strong> Kunci data tanggal <span class="font-semibold">{{ $harianSaatIni->tanggal_singkat }}</span> setelah verifikasi selesai.
                                @endif
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                @if($harianSaatIni->is_locked)
                                    @if($canDirectUnlock)
                                        <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                                            @csrf
                                            <input type="hidden" name="mode" value="harian">
                                            <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                                            <input type="hidden" name="action" value="unlock">
                                            <button type="submit"
                                                    onclick="return confirm('Apakah Anda yakin ingin MEMBUKA KEMBALI kunci penjualan tanggal {{ $tanggalPilihan }}?')"
                                                    class="h-10 px-4 text-xs sm:text-sm font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white shadow-xs transition cursor-pointer inline-flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                <span>Buka Kunci Harian</span>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button"
                                                @click="openModalRequestHarian = true"
                                                class="h-10 px-4 text-xs sm:text-sm font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                            <span>Minta Buka Kunci</span>
                                        </button>
                                    @endif
                                @else
                                    <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                                        @csrf
                                        <input type="hidden" name="mode" value="harian">
                                        <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                                        <input type="hidden" name="action" value="lock">
                                        <button type="submit"
                                                onclick="return confirm('Apakah Anda yakin ingin MENUTUP & MENGUNCI data penjualan harian tanggal {{ $tanggalPilihan }}?')"
                                                class="h-10 px-4 text-xs sm:text-sm font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white shadow-xs transition cursor-pointer inline-flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            <span>Kunci Penjualan Harian</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Rincian Rekapitulasi Edisi Harian per Jurusan -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-4 py-3.5 border-b border-slate-200 bg-white flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1" />
                            </svg>
                            <h3 class="text-sm font-bold text-slate-900">Rekapitulasi Edisi Harian per Jurusan</h3>
                        </div>
                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">
                            {{ $rekapJurusanHarian->count() }} Jurusan
                        </span>
                    </div>

                    <div class="overflow-x-auto min-h-[260px]">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                <tr>
                                    <th class="py-3 px-3.5 w-16 text-center">Kode</th>
                                    <th class="py-3 px-3.5">Nama Jurusan</th>
                                    <th class="py-3 px-3.5">Rute Tujuan</th>
                                    <th class="py-3 px-3.5 text-right">Oplah Cetak</th>
                                    <th class="py-3 px-3.5 text-right">Terjual</th>
                                    <th class="py-3 px-3.5 text-right">Retur</th>
                                    <th class="py-3 px-3.5 text-center">Rasio</th>
                                    <th class="py-3 px-3.5 text-right w-40">Omset Bersih</th>
                                    <th class="py-3 px-3.5 text-center">Status</th>
                                    <th class="print:hidden py-3 px-3.5 text-center w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                                @foreach($rekapJurusanHarian as $rj)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3.5 px-3.5 text-center font-mono font-bold text-kp-blue-700">
                                            {{ $rj->kode }}
                                        </td>
                                        <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                            {{ $rj->nama }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-slate-600 font-normal max-w-[240px] truncate" title="{{ $rj->wilayah }}">
                                            {{ $rj->wilayah }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">
                                            {{ number_format($rj->oplah, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-kp-blue-700 tabular-nums">
                                            {{ number_format($rj->terjual, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">
                                            {{ number_format($rj->retur, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-center">
                                            <span class="inline-block px-2 py-0.5 rounded-md font-mono text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200 tabular-nums">
                                                {{ $rj->rasio }}%
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3.5">
                                            <div class="flex justify-between items-center gap-2 max-w-[135px] ml-auto font-mono text-sm">
                                                <span class="text-slate-400 font-normal select-none">Rp</span>
                                                <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($rj->omset, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-3.5 text-center">
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                {{ $rj->status === 'TERKUNCI' ? 'Terkunci' : 'Valid' }}
                                            </span>
                                        </td>
                                        <td class="print:hidden py-3.5 px-3.5 text-center"
                                            x-data="{ open: false }"
                                            :class="{ 'relative z-50': open }">
                                            <div class="relative inline-block text-left" @click.outside="open = false">
                                                <button type="button"
                                                        @click="open = !open"
                                                        class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none cursor-pointer"
                                                        :class="{ 'text-slate-900 bg-slate-100': open }"
                                                        title="Pilihan Aksi">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <circle cx="12" cy="5" r="1.5" />
                                                        <circle cx="12" cy="12" r="1.5" />
                                                        <circle cx="12" cy="19" r="1.5" />
                                                    </svg>
                                                </button>

                                                <div x-cloak
                                                     x-show="open"
                                                     x-transition:enter="transition ease-out duration-150"
                                                     x-transition:enter-start="opacity-0 scale-95"
                                                     x-transition:enter-end="opacity-100 scale-100"
                                                     x-transition:leave="transition ease-in duration-100"
                                                     x-transition:leave-start="opacity-100 scale-100"
                                                     x-transition:leave-end="opacity-0 scale-95"
                                                     class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">

                                                    <a href="{{ route('sirkulasi.cetak.nota-distribusi', ['jurusan' => $rj->kode, 'tanggal' => $tanggalPilihan]) }}"
                                                       @click="open = false"
                                                       class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        <span>Surat Jalan Rute</span>
                                                    </a>

                                                    <a href="{{ route('sirkulasi.jatah.edit', ['tanggal' => $tanggalPilihan, 'jurusan' => $rj->kode]) }}"
                                                       @click="open = false"
                                                       class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        <span>Lihat Matriks Rute</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 font-semibold text-slate-900 border-t-2 border-slate-200 text-sm whitespace-nowrap">
                                <tr>
                                    <td colspan="3" class="py-3.5 px-3.5 uppercase text-xs font-bold text-slate-700">
                                        Grand Total Edisi Harian
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono font-bold tabular-nums">
                                        {{ number_format($rekapJurusanHarian->sum('oplah'), 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono font-bold text-kp-blue-700 tabular-nums">
                                        {{ number_format($rekapJurusanHarian->sum('terjual'), 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono font-bold tabular-nums">
                                        {{ number_format($rekapJurusanHarian->sum('retur'), 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center font-mono text-xs font-bold tabular-nums">
                                        {{ number_format(($rekapJurusanHarian->sum('retur') / ($rekapJurusanHarian->sum('oplah') ?: 1)) * 100, 2) }}%
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono font-bold text-kp-blue-700 tabular-nums">
                                        Rp {{ number_format($rekapJurusanHarian->sum('omset'), 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            <span>Ready</span>
                                        </span>
                                    </td>
                                    <td class="print:hidden"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Riwayat Penutupan Penjualan Harian -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-4 py-3.5 border-b border-slate-200 bg-white flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-900">Riwayat Penutupan Penjualan Harian</h3>
                        <span class="text-xs font-medium text-slate-500">7 Hari Terakhir</span>
                    </div>

                    <div class="overflow-x-auto min-h-[260px]">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                <tr>
                                    <th class="py-3 px-3.5">Tanggal Edisi</th>
                                    <th class="py-3 px-3.5 text-right">Oplah Cetak</th>
                                    <th class="py-3 px-3.5 text-right">Terjual</th>
                                    <th class="py-3 px-3.5 text-right">Retur</th>
                                    <th class="py-3 px-3.5 text-center">Rasio</th>
                                    <th class="py-3 px-3.5 text-right w-40">Omset Bersih</th>
                                    <th class="py-3 px-3.5 text-center">Status</th>
                                    <th class="py-3 px-3.5">Waktu Closing</th>
                                    <th class="py-3 px-3.5">Otorisator</th>
                                    <th class="print:hidden py-3 px-3.5 text-center w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                                @foreach($riwayatClosingHarian as $rc)
                                    <tr class="hover:bg-slate-50/80 transition {{ $rc->tanggal_key === $tanggalPilihan ? 'bg-kp-blue-50/40' : '' }}">
                                        <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                            {{ $rc->tanggal }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">
                                            {{ number_format($rc->total_oplah_cetak, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right text-kp-blue-700 font-mono font-semibold tabular-nums">
                                            {{ number_format($rc->total_terjual, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">
                                            {{ number_format($rc->total_retur_eks, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-center font-mono text-xs">
                                            <span class="inline-block px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200">{{ $rc->rasio_retur }}%</span>
                                        </td>
                                        <td class="py-3.5 px-3.5">
                                            <div class="flex justify-between items-center gap-2 max-w-[135px] ml-auto font-mono text-sm">
                                                <span class="text-slate-400 font-normal select-none">Rp</span>
                                                <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($rc->total_omset_bersih, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-3.5 text-center">
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                {{ $rc->status_label }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3.5 font-mono text-xs text-slate-500">
                                            {{ $rc->closed_at }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-slate-700">
                                            {{ $rc->closed_by }}
                                        </td>
                                        <td class="print:hidden py-3.5 px-3.5 text-center"
                                            x-data="{ open: false }"
                                            :class="{ 'relative z-50': open }">
                                            <div class="relative inline-block text-left" @click.outside="open = false">
                                                <button type="button"
                                                        @click="open = !open"
                                                        class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none cursor-pointer"
                                                        :class="{ 'text-slate-900 bg-slate-100': open }"
                                                        title="Pilihan Aksi">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <circle cx="12" cy="5" r="1.5" />
                                                        <circle cx="12" cy="12" r="1.5" />
                                                        <circle cx="12" cy="19" r="1.5" />
                                                    </svg>
                                                </button>

                                                <div x-cloak
                                                     x-show="open"
                                                     x-transition:enter="transition ease-out duration-150"
                                                     x-transition:enter-start="opacity-0 scale-95"
                                                     x-transition:enter-end="opacity-100 scale-100"
                                                     x-transition:leave="transition ease-in duration-100"
                                                     x-transition:leave-start="opacity-100 scale-100"
                                                     x-transition:leave-end="opacity-0 scale-95"
                                                     class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-44 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">

                                                    <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $rc->tanggal_key]) }}"
                                                       @click="open = false"
                                                       class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        <span>Buka Edisi</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- =================================================================== --}}
        {{-- BAGIAN 2: TUTUP DATA PENJUALAN BULANAN (PER BULAN)                  --}}
        {{-- =================================================================== --}}
        @if($mode === 'bulanan' || $mode === 'semua')
            <div class="space-y-4">
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3.5 border-b border-slate-100 gap-3">
                        <div>
                            <span class="text-xs font-semibold text-kp-blue-700 uppercase tracking-wider block">Tutup Penjualan Bulanan ({{ $periodeSaatIni->periode_key }})</span>
                            <h2 class="text-base font-bold text-slate-900 mt-0.5">{{ $periodeSaatIni->periode }}</h2>
                        </div>
                        <div>
                            @if($periodeSaatIni->is_locked)
                                <div class="sm:text-right">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                        <span>{{ $periodeSaatIni->status_label }}</span>
                                    </span>
                                    @if($periodeSaatIni->closed_at)
                                        <p class="text-xs text-slate-400 mt-1 font-mono">Ditutup: {{ $periodeSaatIni->closed_at }} ({{ $periodeSaatIni->closed_by }})</p>
                                    @endif
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                    <span>{{ $periodeSaatIni->status_label }}</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80">
                            <span class="text-xs text-slate-500 block">Oplah Cetak Bulanan</span>
                            <div class="text-base font-bold text-slate-900 mt-1 font-mono">{{ number_format($periodeSaatIni->total_oplah_cetak, 0, ',', '.') }} eks</div>
                        </div>
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80">
                            <span class="text-xs text-slate-500 block">Koran Terjual (Nett)</span>
                            <div class="text-base font-bold text-kp-blue-700 mt-1 font-mono">{{ number_format($periodeSaatIni->total_terjual, 0, ',', '.') }} eks</div>
                        </div>
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80">
                            <span class="text-xs text-slate-500 block">Retur BAPR ({{ $periodeSaatIni->rasio_retur }}%)</span>
                            <div class="text-base font-bold text-slate-900 mt-1 font-mono">{{ number_format($periodeSaatIni->total_retur_eks, 0, ',', '.') }} eks</div>
                        </div>
                        <div class="p-3.5 bg-kp-blue-50 rounded-xl border border-kp-blue-200">
                            <span class="text-xs text-kp-blue-700 font-semibold block">Omset Bersih</span>
                            <div class="text-base font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($periodeSaatIni->omset_bersih, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    @if($periodeSaatIni->is_locked && $permintaanBukaBulanan)
                        <div class="print:hidden p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs sm:text-sm font-bold text-slate-900">Permintaan Buka Kunci Periode Diajukan</span>
                                            <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">{{ $permintaanBukaBulanan->id }}</span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            Periode {{ $periodeSaatIni->periode }} tetap terkunci hingga disetujui Bagian Akuntansi.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <form method="POST" action="{{ route('sirkulasi.jatah.tutup.cancel-request-unlock') }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan permohonan buka kunci transaksi bulanan ini?')">
                                        @csrf
                                        <input type="hidden" name="mode" value="bulanan">
                                        <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                                        <button type="submit" class="h-9 px-3.5 text-xs font-semibold rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                                            Batalkan Permintaan
                                        </button>
                                    </form>
                                    @if($canDirectUnlock)
                                        <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                                            @csrf
                                            <input type="hidden" name="mode" value="bulanan">
                                            <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                                            <input type="hidden" name="action" value="unlock">
                                            <button type="submit" onclick="return confirm('Buka kunci penjualan periode {{ $bulanPilihan }} sekarang?')" class="h-9 px-3.5 text-xs font-bold rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white transition cursor-pointer inline-flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                <span>Setujui &amp; Buka Kunci</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="print:hidden p-4 {{ $periodeSaatIni->is_locked ? 'bg-slate-50 border-slate-200' : 'bg-kp-blue-50/70 border-kp-blue-200' }} border rounded-xl flex flex-col sm:flex-row items-center justify-between gap-3 text-xs sm:text-sm">
                            <div class="text-slate-700 leading-relaxed">
                                @if($periodeSaatIni->is_locked)
                                    <strong class="font-bold text-slate-900">Status Bulanan: Terkunci.</strong> Pembukaan transaksi bulanan memerlukan otorisasi Divisi Akuntansi.
                                @else
                                    <strong class="font-bold text-slate-900">Status Bulanan: Terbuka.</strong> Kunci periode ini sebelum penarikan piutang bulanan Akuntansi.
                                @endif
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                @if($periodeSaatIni->is_locked)
                                    @if($canDirectUnlock)
                                        <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                                            @csrf
                                            <input type="hidden" name="mode" value="bulanan">
                                            <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                                            <input type="hidden" name="action" value="unlock">
                                            <button type="submit"
                                                    onclick="return confirm('Apakah Anda yakin ingin MEMBUKA KEMBALI kunci penjualan periode {{ $bulanPilihan }}?')"
                                                    class="h-10 px-4 text-xs sm:text-sm font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white shadow-xs transition cursor-pointer inline-flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                <span>Buka Kunci Bulanan</span>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button"
                                                @click="openModalRequestBulanan = true"
                                                class="h-10 px-4 text-xs sm:text-sm font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                            <span>Minta Buka Kunci</span>
                                        </button>
                                    @endif
                                @else
                                    <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                                        @csrf
                                        <input type="hidden" name="mode" value="bulanan">
                                        <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                                        <input type="hidden" name="action" value="lock">
                                        <button type="submit"
                                                onclick="return confirm('Apakah Anda yakin ingin MENUTUP & MENGUNCI data penjualan periode {{ $bulanPilihan }}?')"
                                                class="h-10 px-4 text-xs sm:text-sm font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white shadow-xs transition cursor-pointer inline-flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            <span>Kunci Penjualan Bulanan</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Riwayat Tutup Data Bulan Sebelumnya -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-4 py-3.5 border-b border-slate-200 bg-white flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-900">Riwayat Penutupan Buku Bulanan</h3>
                        <span class="text-xs font-medium text-slate-500">Histori Bulanan</span>
                    </div>

                    <div class="overflow-x-auto min-h-[240px]">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                <tr>
                                    <th class="py-3 px-3.5">Periode Bulan</th>
                                    <th class="py-3 px-3.5 text-right">Oplah Cetak</th>
                                    <th class="py-3 px-3.5 text-right">Retur</th>
                                    <th class="py-3 px-3.5 text-center">Rasio</th>
                                    <th class="py-3 px-3.5 text-right w-40">Omset Bersih</th>
                                    <th class="py-3 px-3.5 text-center">Status Akuntansi</th>
                                    <th class="py-3 px-3.5">Waktu Closing</th>
                                    <th class="py-3 px-3.5">Otorisator</th>
                                    <th class="print:hidden py-3 px-3.5 text-center w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                                @foreach($riwayatClosingBulanan as $rc)
                                    <tr class="hover:bg-slate-50/80 transition {{ $rc->periode_key === $bulanPilihan ? 'bg-kp-blue-50/40' : '' }}">
                                        <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                            {{ $rc->periode }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">
                                            {{ number_format($rc->total_oplah_cetak, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">
                                            {{ number_format($rc->total_retur_eks, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-center font-mono text-xs">
                                            <span class="inline-block px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200">{{ $rc->rasio_retur }}%</span>
                                        </td>
                                        <td class="py-3.5 px-3.5">
                                            <div class="flex justify-between items-center gap-2 max-w-[135px] ml-auto font-mono text-sm">
                                                <span class="text-slate-400 font-normal select-none">Rp</span>
                                                <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($rc->total_omset_bersih, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-3.5 text-center">
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                {{ $rc->status_label }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3.5 font-mono text-xs text-slate-500">
                                            {{ $rc->closed_at }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-slate-700">
                                            {{ $rc->closed_by }}
                                        </td>
                                        <td class="print:hidden py-3.5 px-3.5 text-center"
                                            x-data="{ open: false }"
                                            :class="{ 'relative z-50': open }">
                                            <div class="relative inline-block text-left" @click.outside="open = false">
                                                <button type="button"
                                                        @click="open = !open"
                                                        class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none cursor-pointer"
                                                        :class="{ 'text-slate-900 bg-slate-100': open }"
                                                        title="Pilihan Aksi">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <circle cx="12" cy="5" r="1.5" />
                                                        <circle cx="12" cy="12" r="1.5" />
                                                        <circle cx="12" cy="19" r="1.5" />
                                                    </svg>
                                                </button>

                                                <div x-cloak
                                                     x-show="open"
                                                     x-transition:enter="transition ease-out duration-150"
                                                     x-transition:enter-start="opacity-0 scale-95"
                                                     x-transition:enter-end="opacity-100 scale-100"
                                                     x-transition:leave="transition ease-in duration-100"
                                                     x-transition:leave-start="opacity-100 scale-100"
                                                     x-transition:leave-end="opacity-0 scale-95"
                                                     class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-44 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">

                                                    <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'bulanan', 'bulan' => $rc->periode_key]) }}"
                                                       @click="open = false"
                                                       class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        <span>Buka Periode</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($semuaPermintaanBuka) && $semuaPermintaanBuka->isNotEmpty())
            <!-- Riwayat Pengajuan Pembukaan Transaksi ke Divisi Akuntansi -->
            <div class="print:hidden bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-4 py-3.5 border-b border-slate-200 bg-white flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        <h3 class="text-sm font-bold text-slate-900">Daftar Pengajuan Buka Kunci ke Divisi Akuntansi</h3>
                    </div>
                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">
                        {{ $semuaPermintaanBuka->count() }} Pengajuan
                    </span>
                </div>

                <div class="overflow-x-auto min-h-[220px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5">No. Tiket</th>
                                <th class="py-3 px-3.5">Target Transaksi</th>
                                <th class="py-3 px-3.5">Kategori</th>
                                <th class="py-3 px-3.5">Alasan Pembukaan</th>
                                <th class="py-3 px-3.5 text-right">Estimasi Susulan</th>
                                <th class="py-3 px-3.5">Pemohon</th>
                                <th class="py-3 px-3.5 text-center">Status Akuntansi</th>
                                <th class="py-3 px-3.5 text-center w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @foreach($semuaPermintaanBuka as $req)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700">
                                        {{ $req->id }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                        {{ $req->target_label }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-slate-700">
                                        {{ $req->kategori_alasan }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-slate-600 max-w-[240px] truncate" title="{{ $req->alasan }}">
                                        {{ $req->alasan }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-sm">
                                        @if(!empty($req->estimasi_eksemplar))
                                            {{ number_format($req->estimasi_eksemplar) }} eks
                                        @elseif(!empty($req->estimasi_nominal))
                                            Rp {{ number_format($req->estimasi_nominal, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3.5 text-slate-700">
                                        {{ $req->pemohon }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        @if($req->status === 'APPROVED')
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                Disetujui &amp; Dibuka
                                            </span>
                                        @else
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                Menunggu Otorisasi
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center"
                                        x-data="{ open: false }"
                                        :class="{ 'relative z-50': open }">
                                        <div class="relative inline-block text-left" @click.outside="open = false">
                                            <button type="button"
                                                    @click="open = !open"
                                                    class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none cursor-pointer"
                                                    :class="{ 'text-slate-900 bg-slate-100': open }"
                                                    title="Pilihan Aksi">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="5" r="1.5" />
                                                    <circle cx="12" cy="12" r="1.5" />
                                                    <circle cx="12" cy="19" r="1.5" />
                                                </svg>
                                            </button>

                                            <div x-cloak
                                                 x-show="open"
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100 scale-100"
                                                 x-transition:leave-end="opacity-0 scale-95"
                                                 class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">

                                                @if($req->status === 'PENDING')
                                                    <form method="POST" action="{{ route('sirkulasi.jatah.tutup.cancel-request-unlock') }}" onsubmit="return confirm('Batalkan pengajuan {{ $req->id }}?')">
                                                        @csrf
                                                        <input type="hidden" name="mode" value="{{ $req->mode }}">
                                                        <input type="hidden" name="{{ $req->mode === 'harian' ? 'tanggal' : 'periode' }}" value="{{ $req->target }}">
                                                        <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition text-left cursor-pointer">
                                                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/></svg>
                                                            <span>Batalkan Pengajuan</span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => $req->mode, ($req->mode === 'harian' ? 'tanggal' : 'bulan') => $req->target]) }}"
                                                       @click="open = false"
                                                       class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        <span>Lihat Data</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- MODAL PENGAJUAN BUKA KUNCI HARIAN KE AKUNTANSI -->
        <div x-show="openModalRequestHarian"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title-harian"
             role="dialog"
             aria-modal="true"
             @keydown.escape.window="openModalRequestHarian = false">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="openModalRequestHarian"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs"
                     @click="openModalRequestHarian = false"></div>

                <div x-show="openModalRequestHarian"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative inline-block w-full max-w-xl p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl border border-slate-200">

                    <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900" id="modal-title-harian">
                                    Pengajuan Buka Kunci Transaksi Harian
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Permintaan pembukaan data edisi <span class="font-semibold text-slate-700">{{ $harianSaatIni->tanggal }}</span>
                                </p>
                            </div>
                        </div>
                        <button type="button" @click="openModalRequestHarian = false" class="text-slate-400 hover:text-slate-600 rounded-lg p-1.5 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="mt-4 p-3 bg-kp-blue-50/70 border border-kp-blue-200 rounded-xl text-xs text-slate-700 leading-relaxed flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-kp-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <strong class="font-bold text-slate-900">Ketentuan Otoritas Akuntansi:</strong> Transaksi yang telah ditutup hanya dapat dibuka kembali oleh Bagian Akuntansi setelah formulir ini diverifikasi.
                        </div>
                    </div>

                    <form method="POST" action="{{ route('sirkulasi.jatah.tutup.request-unlock') }}" class="mt-4 space-y-4">
                        @csrf
                        <input type="hidden" name="mode" value="harian">
                        <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Target Transaksi Ditutup</label>
                            <div class="px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 text-sm font-mono font-semibold text-slate-800 flex items-center justify-between">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span>{{ $harianSaatIni->tanggal }}</span>
                                </span>
                                <span class="text-xs text-slate-500 font-sans">{{ $tanggalPilihan }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Kategori Masalah / Kebutuhan</label>
                            <select name="kategori_alasan" x-model="kategoriHarian" class="w-full h-11 px-3.5 text-sm rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                                <option value="Penjualan Baru / Susulan Masuk">Penjualan Baru / Susulan Agen (Terlambat lapor saat closing)</option>
                                <option value="Koreksi Retur Fisik Agen Terlambat">Koreksi Fisik Retur Koran Terlambat Masuk</option>
                                <option value="Penyesuaian Jatah Ekspedisi Luar Kota">Penyesuaian Jatah Ekspedisi / Armada</option>
                                <option value="Koreksi Salah Input Data Jatah">Koreksi Salah Input Data Jatah / Pelanggan</option>
                                <option value="Lainnya">Lain-lain / Keperluan Khusus</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">
                                Rincian Alasan Pengajuan Pembukaan <span class="text-slate-500">*</span>
                            </label>
                            <textarea name="alasan"
                                      rows="3"
                                      required
                                      minlength="5"
                                      placeholder="Tuliskan nama agen, perkiraan eksemplar, atau kendala spesifik..."
                                      class="w-full px-3.5 py-2.5 text-sm rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600 leading-relaxed"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Estimasi Eksemplar (Eks)</label>
                                <input type="number" name="estimasi_eksemplar" placeholder="Contoh: 120" min="0"
                                       class="w-full h-10 px-3 text-sm rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Estimasi Nilai (Rp)</label>
                                <input type="number" name="estimasi_nominal" placeholder="Contoh: 360000" min="0"
                                       class="w-full h-10 px-3 text-sm rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                            <button type="button" @click="openModalRequestHarian = false" class="h-10 px-4 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="h-10 px-4 text-sm font-bold text-white bg-kp-blue-600 hover:bg-kp-blue-700 rounded-xl shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                <span>Kirim Permintaan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL PENGAJUAN BUKA KUNCI BULANAN KE AKUNTANSI -->
        <div x-show="openModalRequestBulanan"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title-bulanan"
             role="dialog"
             aria-modal="true"
             @keydown.escape.window="openModalRequestBulanan = false">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="openModalRequestBulanan"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs"
                     @click="openModalRequestBulanan = false"></div>

                <div x-show="openModalRequestBulanan"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative inline-block w-full max-w-xl p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl border border-slate-200">

                    <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900" id="modal-title-bulanan">
                                    Pengajuan Buka Kunci Transaksi Bulanan
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Permintaan pembukaan buku periode <span class="font-semibold text-slate-700">{{ $periodeSaatIni->periode }}</span>
                                </p>
                            </div>
                        </div>
                        <button type="button" @click="openModalRequestBulanan = false" class="text-slate-400 hover:text-slate-600 rounded-lg p-1.5 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="mt-4 p-3 bg-kp-blue-50/70 border border-kp-blue-200 rounded-xl text-xs text-slate-700 leading-relaxed flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-kp-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <strong class="font-bold text-slate-900">Ketentuan Otoritas Akuntansi:</strong> Pembukaan kunci bulanan hanya dapat dilakukan oleh Divisi Akuntansi setelah formulir ini disetujui.
                        </div>
                    </div>

                    <form method="POST" action="{{ route('sirkulasi.jatah.tutup.request-unlock') }}" class="mt-4 space-y-4">
                        @csrf
                        <input type="hidden" name="mode" value="bulanan">
                        <input type="hidden" name="periode" value="{{ $bulanPilihan }}">

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Target Periode Ditutup</label>
                            <div class="px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 text-sm font-mono font-semibold text-slate-800 flex items-center justify-between">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span>{{ $periodeSaatIni->periode }}</span>
                                </span>
                                <span class="text-xs text-slate-500 font-sans">{{ $bulanPilihan }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Kategori Masalah / Kebutuhan</label>
                            <select name="kategori_alasan" x-model="kategoriBulanan" class="w-full h-11 px-3.5 text-sm rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                                <option value="Penjualan Baru / Susulan Masuk">Penjualan Susulan Akhir Bulan Masuk Terlambat</option>
                                <option value="Rekonsiliasi Retur Fisik Agen BAPR">Rekonsiliasi Retur Fisik BAPR Terlambat Diterima</option>
                                <option value="Koreksi Selisih Oplah & Ekspedisi">Koreksi Selisih Oplah &amp; Pengiriman Ekspedisi</option>
                                <option value="Lainnya">Lain-lain / Keperluan Audit Khusus</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">
                                Rincian Alasan Pengajuan Pembukaan <span class="text-slate-500">*</span>
                            </label>
                            <textarea name="alasan"
                                      rows="3"
                                      required
                                      minlength="5"
                                      placeholder="Tuliskan keterangan detail untuk verifikasi Divisi Akuntansi..."
                                      class="w-full px-3.5 py-2.5 text-sm rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600 leading-relaxed"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Estimasi Eksemplar (Eks)</label>
                                <input type="number" name="estimasi_eksemplar" placeholder="Contoh: 500" min="0"
                                       class="w-full h-10 px-3 text-sm rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Estimasi Nilai (Rp)</label>
                                <input type="number" name="estimasi_nominal" placeholder="Contoh: 1500000" min="0"
                                       class="w-full h-10 px-3 text-sm rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                            <button type="button" @click="openModalRequestBulanan = false" class="h-10 px-4 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="h-10 px-4 text-sm font-bold text-white bg-kp-blue-600 hover:bg-kp-blue-700 rounded-xl shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                <span>Kirim Permintaan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
