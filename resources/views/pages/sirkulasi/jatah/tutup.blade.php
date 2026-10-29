<x-app-layout>
    <div class="space-y-6" x-data="{ openModalRequestHarian: false, openModalRequestBulanan: false, kategoriHarian: 'Penjualan Baru / Susulan Masuk', kategoriBulanan: 'Penjualan Baru / Susulan Masuk' }">
        @php 
            $canDirectUnlock = $canDirectUnlock ?? (auth()->user() && (auth()->user()->isAdmin() || auth()->user()->hasRole('akuntansi', 'accounting', 'keuangan')));
        @endphp

        <!-- Header Halaman (Hanya Tampil di Layar) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-purple-600 uppercase tracking-wider">Operasional Jatah & Closing</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Tutup Data Penjualan (Harian & Bulanan)</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Kunci data transaksi jatah dan retur koran harian atau bulanan sebelum ditarik oleh Divisi Kasir & Accounting (Ready to Pull Akunting)
                    </p>
                </div>
            </div>

            <!-- Filter Controls Sesuai Mode Aktif -->
            <div class="flex flex-wrap items-center gap-2">
                @if($mode === 'bulanan')
                    <form method="GET" action="{{ route('sirkulasi.jatah.tutup') }}" class="flex items-center gap-1.5">
                        <input type="hidden" name="mode" value="bulanan">
                        <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="text-xs rounded-lg border-slate-200 py-1.5 focus:border-purple-500 focus:ring-purple-500">
                        <button type="submit" class="py-1.5 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition-colors cursor-pointer">
                            Filter Bulan
                        </button>
                    </form>
                @elseif($mode === 'harian')
                    <form method="GET" action="{{ route('sirkulasi.jatah.tutup') }}" class="flex items-center gap-1.5">
                        <input type="hidden" name="mode" value="harian">
                        <input type="date" name="tanggal" value="{{ $tanggalPilihan }}" class="text-xs rounded-lg border-slate-200 py-1.5 focus:border-purple-500 focus:ring-purple-500">
                        <button type="submit" class="py-1.5 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition-colors cursor-pointer">
                            Filter Tanggal
                        </button>
                    </form>
                    <div class="hidden sm:flex items-center gap-1">
                        <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => \Carbon\Carbon::today()->toDateString()]) }}" 
                           class="py-1 px-2 text-[11px] font-semibold rounded-lg border {{ $tanggalPilihan === \Carbon\Carbon::today()->toDateString() ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                            Hari Ini
                        </a>
                        <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => \Carbon\Carbon::yesterday()->toDateString()]) }}" 
                           class="py-1 px-2 text-[11px] font-semibold rounded-lg border {{ $tanggalPilihan === \Carbon\Carbon::yesterday()->toDateString() ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                            Kemarin
                        </a>
                    </div>
                @else
                    <!-- Mode 'semua' (Tampilkan Keduanya) -->
                    <div class="flex flex-wrap items-center gap-2">
                        <form method="GET" action="{{ route('sirkulasi.jatah.tutup') }}" class="flex items-center gap-1">
                            <input type="hidden" name="mode" value="semua">
                            <span class="text-[11px] font-semibold text-slate-500">Harian:</span>
                            <input type="date" name="tanggal" value="{{ $tanggalPilihan }}" class="text-xs rounded-lg border-slate-200 py-1.5 focus:border-purple-500 focus:ring-purple-500">
                            <span class="text-[11px] font-semibold text-slate-500 ml-1">Bulanan:</span>
                            <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="text-xs rounded-lg border-slate-200 py-1.5 focus:border-purple-500 focus:ring-purple-500">
                            <button type="submit" class="py-1.5 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition-colors cursor-pointer">
                                Terapkan
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab Navigasi Mode: Harian, Bulanan, dan Keduanya -->
        <div class="print:hidden bg-slate-100 p-1.5 rounded-xl flex flex-wrap gap-1.5 max-w-fit border border-slate-200/80">
            <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'semua', 'tanggal' => $tanggalPilihan, 'bulan' => $bulanPilihan]) }}" 
               class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all {{ $mode === 'semua' ? 'bg-white text-purple-700 shadow-xs border border-purple-100' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                <svg class="w-4 h-4 {{ $mode === 'semua' ? 'text-purple-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span>Tampilkan Keduanya (Harian & Bulanan)</span>
            </a>

            <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $tanggalPilihan]) }}" 
               class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all {{ $mode === 'harian' ? 'bg-white text-purple-700 shadow-xs border border-purple-100' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                <svg class="w-4 h-4 {{ $mode === 'harian' ? 'text-purple-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Tutup Penjualan Harian</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] {{ $mode === 'harian' ? 'bg-purple-100 text-purple-800 font-bold' : 'bg-slate-200/80 text-slate-600' }}">Harian</span>
            </a>

            <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'bulanan', 'bulan' => $bulanPilihan]) }}" 
               class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all {{ $mode === 'bulanan' ? 'bg-white text-purple-700 shadow-xs border border-purple-100' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                <svg class="w-4 h-4 {{ $mode === 'bulanan' ? 'text-purple-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>Tutup Penjualan Bulanan</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] {{ $mode === 'bulanan' ? 'bg-purple-100 text-purple-800 font-bold' : 'bg-slate-200/80 text-slate-600' }}">Bulanan</span>
            </a>
        </div>

        @if(session('success'))
            <div class="print:hidden p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-medium text-emerald-800 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="window.print()" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white transition-colors inline-flex items-center gap-1 cursor-pointer shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak Berita Acara</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="print:hidden p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs font-medium text-rose-800 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Ringkasan Cepat Dual Overview (Harian & Bulanan) -->
        <div class="print:hidden grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Ringkasan Harian -->
            <div class="bg-gradient-to-br from-white to-purple-50/30 border border-purple-200/70 rounded-xl p-4 shadow-2xs">
                <div class="flex items-center justify-between pb-2.5 border-b border-purple-100">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold">
                            📅
                        </span>
                        <div>
                            <span class="text-[10px] font-bold text-purple-600 uppercase tracking-wider">Tutup Penjualan Harian</span>
                            <h3 class="text-xs font-extrabold text-slate-900">{{ $harianSaatIni->tanggal }}</h3>
                        </div>
                    </div>
                    @if($harianSaatIni->is_locked)
                        @if($permintaanBukaHarian)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300 inline-flex items-center gap-1 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                <span>⏳ Menunggu Akuntansi</span>
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-300">
                                🔒 Terkunci
                            </span>
                        @endif
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                            🟡 Belum Tutup Harian
                        </span>
                    @endif
                </div>
                <div class="grid grid-cols-3 gap-2 mt-3 text-xs">
                    <div class="p-2 bg-white rounded-lg border border-slate-200">
                        <span class="text-slate-400 text-[10px]">Oplah:</span>
                        <div class="font-extrabold text-slate-800">{{ number_format($harianSaatIni->total_oplah_cetak, 0, ',', '.') }} eks</div>
                    </div>
                    <div class="p-2 bg-white rounded-lg border border-slate-200">
                        <span class="text-slate-400 text-[10px]">Retur Fisik:</span>
                        <div class="font-extrabold text-rose-600">{{ number_format($harianSaatIni->total_retur_eks, 0, ',', '.') }} eks ({{ $harianSaatIni->rasio_retur }}%)</div>
                    </div>
                    <div class="p-2 bg-purple-50 rounded-lg border border-purple-200">
                        <span class="text-purple-700 text-[10px] font-bold">Omset Bersih:</span>
                        <div class="font-extrabold text-purple-950 font-mono">Rp {{ number_format($harianSaatIni->omset_bersih, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Bulanan -->
            <div class="bg-gradient-to-br from-white to-blue-50/30 border border-blue-200/70 rounded-xl p-4 shadow-2xs">
                <div class="flex items-center justify-between pb-2.5 border-b border-blue-100">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">
                            🗓️
                        </span>
                        <div>
                            <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Tutup Penjualan Bulanan</span>
                            <h3 class="text-xs font-extrabold text-slate-900">{{ $periodeSaatIni->periode }}</h3>
                        </div>
                    </div>
                    @if($periodeSaatIni->is_locked)
                        @if($permintaanBukaBulanan)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300 inline-flex items-center gap-1 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                <span>⏳ Menunggu Akuntansi</span>
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-300">
                                🔒 Terkunci
                            </span>
                        @endif
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                            🟡 Belum Tutup Bulan
                        </span>
                    @endif
                </div>
                <div class="grid grid-cols-3 gap-2 mt-3 text-xs">
                    <div class="p-2 bg-white rounded-lg border border-slate-200">
                        <span class="text-slate-400 text-[10px]">Oplah Akumulatif:</span>
                        <div class="font-extrabold text-slate-800">{{ number_format($periodeSaatIni->total_oplah_cetak, 0, ',', '.') }} eks</div>
                    </div>
                    <div class="p-2 bg-white rounded-lg border border-slate-200">
                        <span class="text-slate-400 text-[10px]">Retur BAPR:</span>
                        <div class="font-extrabold text-rose-600">{{ number_format($periodeSaatIni->total_retur_eks, 0, ',', '.') }} eks ({{ $periodeSaatIni->rasio_retur }}%)</div>
                    </div>
                    <div class="p-2 bg-blue-50 rounded-lg border border-blue-200">
                        <span class="text-blue-700 text-[10px] font-bold">Omset Bersih:</span>
                        <div class="font-extrabold text-blue-950 font-mono">Rp {{ number_format($periodeSaatIni->omset_bersih, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOP DOKUMEN CETAK RESMI (HANYA TAMPIL SAAT CETAK) -->
        <div class="hidden print:block mb-4 border-b-2 border-slate-900 pb-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-extrabold tracking-wider uppercase text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                    <p class="text-[11px] font-bold text-slate-700">DIVISI SIRKULASI & DISTRIBUSI — BERITA ACARA TUTUP DATA PENJUALAN</p>
                    <p class="text-[10px] text-slate-500">Jl. Sutisna Senjaya No. 120 Tasikmalaya &bull; Telp (0265) 331888 &bull; sirkulasi@kabarpriangan.co.id</p>
                </div>
                <div class="text-right">
                    <span class="inline-block px-2 py-0.5 border border-slate-400 font-mono text-[10px] font-bold text-slate-800 uppercase">
                        KODE DOK: SIRK-CLS-{{ $mode === 'bulanan' ? 'MTH' : 'DAY' }}
                    </span>
                    <p class="text-[10px] text-slate-600 mt-1">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</p>
                    <p class="text-[10px] text-slate-600">Operator: {{ Auth::user()->name ?? 'Manager Sirkulasi' }}</p>
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
        {{-- Tampil jika mode === 'harian' ATAU mode === 'semua'                 --}}
        {{-- =================================================================== --}}
        @if($mode === 'harian' || $mode === 'semua')
            <div class="space-y-4">
                <div class="flex items-center gap-2 pt-2">
                    <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold bg-purple-600 text-white">BAGIAN 1</span>
                    <h2 class="text-base font-extrabold text-slate-900 tracking-tight">Tutup Data Penjualan Harian (Per Hari)</h2>
                    <span class="text-xs text-slate-400 font-mono">Edisi: {{ $harianSaatIni->tanggal_singkat }}</span>
                </div>

                <!-- Panel Status Penjualan Harian -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-semibold text-purple-600 uppercase tracking-wider">Edisi Terbit Harian</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="text-[11px] font-mono text-slate-500">{{ $harianSaatIni->tanggal_key }}</span>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-900 mt-0.5">{{ $harianSaatIni->tanggal }}</h3>
                        </div>
                        <div>
                            @if($harianSaatIni->is_locked)
                                <div class="text-right">
                                    <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-purple-100 text-purple-800 border border-purple-300 inline-flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                        <span>{{ $harianSaatIni->status_label }}</span>
                                    </span>
                                    <p class="text-[10px] text-slate-400 mt-1 font-mono">Ditutup: {{ $harianSaatIni->closed_at }} ({{ $harianSaatIni->closed_by }})</p>
                                </div>
                            @else
                                <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-amber-50 text-amber-800 border border-amber-300 inline-flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span>{{ $harianSaatIni->status_label }}</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Matriks Ringkasan Harian -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 my-4 text-xs">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <span class="text-slate-500 text-[11px]">Total Oplah Cetak Harian:</span>
                            <div class="text-sm font-extrabold text-slate-900 mt-1">{{ number_format($harianSaatIni->total_oplah_cetak, 0, ',', '.') }} eks</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">Produksi Cetak Subuh</div>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <span class="text-slate-500 text-[11px]">Koran Terjual (Nett):</span>
                            <div class="text-sm font-extrabold text-emerald-700 mt-1">{{ number_format($harianSaatIni->total_terjual, 0, ',', '.') }} eks</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">Konsinyasi & Langganan</div>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <span class="text-slate-500 text-[11px]">Total Retur (Fisik):</span>
                            <div class="text-sm font-extrabold text-rose-600 mt-1">{{ number_format($harianSaatIni->total_retur_eks, 0, ',', '.') }} eks ({{ $harianSaatIni->rasio_retur }}%)</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">Potongan Rp {{ number_format($harianSaatIni->potongan_retur, 0, ',', '.') }}</div>
                        </div>
                        <div class="p-3 bg-purple-50/60 rounded-xl border border-purple-200">
                            <span class="text-purple-800 text-[11px] font-semibold">Omset Bersih Sirkulasi:</span>
                            <div class="text-sm font-extrabold text-purple-950 mt-1 font-mono">Rp {{ number_format($harianSaatIni->omset_bersih, 0, ',', '.') }}</div>
                            <div class="text-[10px] text-purple-600 mt-0.5">Siap Tarik ke Kasir / Jurnal</div>
                        </div>
                    </div>

                    @php 
                        $canDirectUnlock = auth()->user() && (auth()->user()->isAdmin() || auth()->user()->hasRole('akuntansi', 'accounting', 'keuangan'));
                    @endphp

                    @if($harianSaatIni->is_locked && $permintaanBukaHarian)
                        <!-- Card Peringatan Pengajuan Buka Kunci Sedang Berjalan (Harian) -->
                        <div class="print:hidden p-4 bg-amber-50/90 border border-amber-300 rounded-xl space-y-3 shadow-2xs">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-amber-100 border border-amber-300 text-amber-800 flex items-center justify-center font-bold text-sm shrink-0">
                                        ⏳
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-extrabold text-amber-950 uppercase tracking-wider">Permintaan Buka Kunci Diajukan ke Akuntansi</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-200 text-amber-900 border border-amber-300">{{ $permintaanBukaHarian->id }}</span>
                                        </div>
                                        <p class="text-[11px] text-amber-800 mt-0.5">
                                            Data penjualan edisi <span class="font-semibold">{{ $harianSaatIni->tanggal }}</span> tetap terkunci. Menunggu persetujuan & otorisasi Bagian Akuntansi.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <form method="POST" action="{{ route('sirkulasi.jatah.tutup.cancel-request-unlock') }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan permohonan buka kunci transaksi harian ini?')">
                                        @csrf
                                        <input type="hidden" name="mode" value="harian">
                                        <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-white border border-amber-300 text-amber-900 hover:bg-amber-100 transition-colors cursor-pointer shadow-2xs">
                                            Batalkan Permintaan
                                        </button>
                                    </form>
                                    @if($canDirectUnlock)
                                        <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                                            @csrf
                                            <input type="hidden" name="mode" value="harian">
                                            <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                                            <input type="hidden" name="action" value="unlock">
                                            <button type="submit" onclick="return confirm('Buka kunci penjualan harian tanggal {{ $tanggalPilihan }} sekarang? (Otoritas Akuntansi)')" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition-colors cursor-pointer">
                                                🔓 Setujui & Buka Kunci
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <div class="p-3 bg-white/90 rounded-lg border border-amber-200/80 text-xs text-slate-700 grid grid-cols-1 md:grid-cols-2 gap-2.5">
                                <div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kategori & Alasan Pengajuan Sirkulasi:</div>
                                    <div class="font-semibold text-slate-900 mt-0.5 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>{{ $permintaanBukaHarian->kategori_alasan }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-600 italic mt-1 bg-amber-50/50 p-2 rounded border border-amber-100">
                                        "{{ $permintaanBukaHarian->alasan }}"
                                    </div>
                                </div>
                                <div class="space-y-1.5 text-[11px]">
                                    @if(!empty($permintaanBukaHarian->estimasi_eksemplar) || !empty($permintaanBukaHarian->estimasi_nominal))
                                        <div class="flex items-center justify-between py-0.5 border-b border-slate-100">
                                            <span class="text-slate-500">Estimasi Penjualan Baru:</span>
                                            <span class="font-bold text-purple-700 font-mono">
                                                @if(!empty($permintaanBukaHarian->estimasi_eksemplar))
                                                    {{ number_format($permintaanBukaHarian->estimasi_eksemplar) }} eks
                                                @endif
                                                @if(!empty($permintaanBukaHarian->estimasi_nominal))
                                                    (Rp {{ number_format($permintaanBukaHarian->estimasi_nominal, 0, ',', '.') }})
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                    <div class="flex items-center justify-between py-0.5 border-b border-slate-100">
                                        <span class="text-slate-500">Pemohon (Sirkulasi):</span>
                                        <span class="font-semibold text-slate-800">{{ $permintaanBukaHarian->pemohon }}</span>
                                    </div>
                                    <div class="flex items-center justify-between py-0.5">
                                        <span class="text-slate-500">Waktu Pengajuan:</span>
                                        <span class="font-mono text-slate-600 text-[10px]">{{ $permintaanBukaHarian->created_at }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Tombol Aksi Kunci Data Harian Standard -->
                        <div class="print:hidden p-4 {{ $harianSaatIni->is_locked ? 'bg-slate-50 border-slate-200' : 'bg-purple-50/70 border-purple-200' }} border rounded-xl flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                            <div class="{{ $harianSaatIni->is_locked ? 'text-slate-700' : 'text-purple-900' }} leading-relaxed">
                                @if($harianSaatIni->is_locked)
                                    <strong class="font-bold text-purple-950">Status Data Harian: TERKUNCI (LOCKED).</strong> Data penjualan edisi <span class="font-semibold">{{ $harianSaatIni->tanggal }}</span> telah ditutup. Sesuai SOP, pembukaan transaksi hanya memiliki otorisasi oleh <span class="font-semibold text-indigo-900">Divisi Akuntansi</span>. Jika ada penjualan baru susulan, silakan ajukan permohonan buka kunci.
                                @else
                                    <strong class="font-bold">Konfirmasi Penguncian Harian:</strong> Setelah status dikunci (<span class="font-mono font-bold">READY TO PULL AKUNTING & KASIR</span>), staf Sirkulasi tidak dapat merubah jatah dan retur koran tanggal <span class="font-semibold">{{ $harianSaatIni->tanggal_singkat }}</span> agar rekonsiliasi kas kasir dan piutang harian konsisten.
                                @endif
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" onclick="window.print()" class="px-3 py-2 text-xs font-bold rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-100 transition-colors inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                    <span>Cetak Slip Closing</span>
                                </button>

                                @if($harianSaatIni->is_locked)
                                    @if($canDirectUnlock)
                                        <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                                            @csrf
                                            <input type="hidden" name="mode" value="harian">
                                            <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                                            <input type="hidden" name="action" value="unlock">
                                            <button type="submit" 
                                                    onclick="return confirm('Apakah Anda yakin ingin MEMBUKA KEMBALI kunci penjualan tanggal {{ $tanggalPilihan }}? (Otoritas Akuntansi)')" 
                                                    class="px-3.5 py-2 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition-colors shrink-0 cursor-pointer">
                                                🔓 Buka Kunci Harian (Akuntansi)
                                            </button>
                                        </form>
                                    @else
                                        <!-- Tombol Permintaan Buka Kunci ke Akuntansi untuk Sirkulasi -->
                                        <button type="button" 
                                                @click="openModalRequestHarian = true" 
                                                class="px-3.5 py-2 text-xs font-bold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs transition-colors shrink-0 inline-flex items-center gap-1.5 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                            <span>Minta Buka Kunci ke Akuntansi</span>
                                        </button>
                                    @endif
                                @else
                                    <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                                        @csrf
                                        <input type="hidden" name="mode" value="harian">
                                        <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                                        <input type="hidden" name="action" value="lock">
                                        <button type="submit" 
                                                onclick="return confirm('Apakah Anda yakin ingin MENUTUP & MENGUNCI data penjualan harian tanggal {{ $tanggalPilihan }}? Data jatah dan retur harian akan dikunci untuk diserahkan ke Divisi Kasir & Akuntansi.')" 
                                                class="px-4 py-2 text-xs font-bold rounded-lg bg-purple-700 hover:bg-purple-800 text-white shadow-xs transition-colors shrink-0 cursor-pointer">
                                            🔒 Kunci & Tutup Penjualan Harian
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Rincian Rekapitulasi Jatah & Retur Harian Per Jurusan -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-200/80 bg-slate-50/60 flex items-center justify-between gap-3 flex-wrap">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-sm font-bold shrink-0">
                                🚚
                            </div>
                            <div>
                                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-800">Rincian Rekapitulasi Edisi Harian per Jurusan</h3>
                                <p class="text-[11px] text-slate-500 mt-0.5">Distribusi armada dan fisik retur koran edisi <span class="font-semibold text-slate-700">{{ $harianSaatIni->tanggal }}</span></p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-xs font-mono font-bold text-slate-700 bg-white border border-slate-200 px-3 py-1 rounded-lg shadow-2xs shrink-0">
                            <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                            {{ $rekapJurusanHarian->count() }} Jurusan Armada
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[1020px] text-left text-xs text-slate-600">
                            <thead class="bg-slate-100/75 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-3 px-4 whitespace-nowrap min-w-[210px]">Kode & Nama Jurusan</th>
                                    <th class="py-3 px-4 min-w-[280px]">Titik Tujuan / Rute</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap min-w-[105px]">Oplah Cetak</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap min-w-[110px]">Terjual (Nett)</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap min-w-[100px]">Retur (Eks)</th>
                                    <th class="py-3 px-4 text-center whitespace-nowrap min-w-[95px]">Rasio Retur</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap min-w-[140px]">Omset Bersih</th>
                                    <th class="py-3 px-4 text-center whitespace-nowrap min-w-[100px]">Status Data</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach($rekapJurusanHarian as $rj)
                                    <tr class="hover:bg-purple-50/20 transition-colors">
                                        <td class="py-2.5 px-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-purple-50 text-purple-700 font-mono font-bold text-[11px] border border-purple-200 shrink-0">
                                                    {{ $rj->kode }}
                                                </span>
                                                <span class="font-bold text-slate-900 text-xs">
                                                    {{ $rj->nama }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="py-2.5 px-4 text-slate-600 text-[11.5px] leading-relaxed">
                                            {{ $rj->wilayah }}
                                        </td>
                                        <td class="py-2.5 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap tabular-nums">
                                            {{ number_format($rj->oplah, 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-4 text-right font-mono font-bold text-emerald-700 whitespace-nowrap tabular-nums">
                                            {{ number_format($rj->terjual, 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-4 text-right font-mono font-bold text-rose-600 whitespace-nowrap tabular-nums">
                                            {{ number_format($rj->retur, 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-4 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-[10.5px] font-bold tabular-nums {{ $rj->rasio > 2.5 ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-700' }}">
                                                {{ $rj->rasio }}%
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-4 text-right font-mono font-extrabold text-slate-900 text-xs whitespace-nowrap tabular-nums">
                                            Rp&nbsp;{{ number_format($rj->omset, 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-4 text-center whitespace-nowrap">
                                            @if($rj->status === 'TERKUNCI')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>
                                                    TERKUNCI
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    VALID
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50/90 font-bold text-slate-900 border-t-2 border-slate-300 text-xs">
                                <tr>
                                    <td colspan="2" class="py-3 px-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                                            <span class="uppercase tracking-wider text-[11px] font-extrabold text-slate-800">Grand Total Edisi Harian</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-bold whitespace-nowrap tabular-nums">
                                        {{ number_format($rekapJurusanHarian->sum('oplah'), 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">eks</span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-emerald-700 whitespace-nowrap tabular-nums">
                                        {{ number_format($rekapJurusanHarian->sum('terjual'), 0, ',', '.') }} <span class="text-[10px] font-normal text-emerald-600/70">eks</span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-rose-600 whitespace-nowrap tabular-nums">
                                        {{ number_format($rekapJurusanHarian->sum('retur'), 0, ',', '.') }} <span class="text-[10px] font-normal text-rose-500/70">eks</span>
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-[10.5px] font-bold bg-slate-200/70 text-slate-800 tabular-nums">
                                            {{ number_format(($rekapJurusanHarian->sum('retur') / ($rekapJurusanHarian->sum('oplah') ?: 1)) * 100, 2) }}%
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-extrabold text-purple-950 whitespace-nowrap tabular-nums">
                                        Rp&nbsp;{{ number_format($rekapJurusanHarian->sum('omset'), 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                                            ✓ Ready
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Riwayat Tutup Data Penjualan Harian -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Riwayat Penutupan Penjualan Harian (Edisi Lalu)</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">Catatan locking harian untuk audit kas kasir dan sinkronisasi piutang akuntansi</p>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-500">7 Hari Terakhir</span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[1080px] text-left text-xs text-slate-600">
                            <thead class="bg-slate-100/75 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-3 px-4 whitespace-nowrap min-w-[160px]">Tanggal Edisi</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap min-w-[110px]">Oplah Cetak</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap min-w-[115px]">Koran Terjual</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap min-w-[105px]">Retur (Eks)</th>
                                    <th class="py-3 px-4 text-center whitespace-nowrap min-w-[95px]">Rasio Retur</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap min-w-[140px]">Omset Bersih</th>
                                    <th class="py-3 px-4 text-center whitespace-nowrap min-w-[160px]">Status Akuntansi / Kasir</th>
                                    <th class="py-3 px-4 whitespace-nowrap min-w-[140px]">Waktu Closing</th>
                                    <th class="py-3 px-4 whitespace-nowrap min-w-[130px]">Otorisator</th>
                                    <th class="print:hidden py-3 px-4 text-center whitespace-nowrap min-w-[100px]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach($riwayatClosingHarian as $rc)
                                    <tr class="hover:bg-slate-50/80 transition-colors {{ $rc->tanggal_key === $tanggalPilihan ? 'bg-purple-50/40' : '' }}">
                                        <td class="py-3 px-4 whitespace-nowrap font-bold text-slate-900">
                                            {{ $rc->tanggal }}
                                            <div class="text-[10px] font-mono text-slate-400 font-normal">{{ $rc->tanggal_key }}</div>
                                        </td>
                                        <td class="py-3 px-4 text-right whitespace-nowrap font-mono tabular-nums">
                                            {{ number_format($rc->total_oplah_cetak, 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-400">eks</span>
                                        </td>
                                        <td class="py-3 px-4 text-right text-emerald-700 whitespace-nowrap font-mono font-bold tabular-nums">
                                            {{ number_format($rc->total_terjual, 0, ',', '.') }} <span class="text-[10px] font-normal text-emerald-600/70">eks</span>
                                        </td>
                                        <td class="py-3 px-4 text-right text-rose-600 whitespace-nowrap font-mono font-bold tabular-nums">
                                            {{ number_format($rc->total_retur_eks, 0, ',', '.') }} <span class="text-[10px] font-normal text-rose-500/70">eks</span>
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-[10.5px] font-bold bg-slate-100 text-slate-700 tabular-nums">
                                                {{ $rc->rasio_retur }}%
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-right font-mono font-extrabold text-slate-900 text-xs whitespace-nowrap tabular-nums">
                                            Rp&nbsp;{{ number_format($rc->total_omset_bersih, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            @if($rc->status === 'PULLED')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                                    {{ $rc->status_label }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>
                                                    {{ $rc->status_label }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 font-mono text-[11px] text-slate-500 whitespace-nowrap">
                                            {{ $rc->closed_at }}
                                        </td>
                                        <td class="py-3 px-4 text-slate-700 whitespace-nowrap">
                                            {{ $rc->closed_by }}
                                        </td>
                                        <td class="print:hidden py-3 px-4 text-center whitespace-nowrap">
                                            <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $rc->tanggal_key]) }}" 
                                               class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold text-purple-700 hover:text-purple-900 hover:bg-purple-50 border border-purple-200 rounded-lg transition-colors shadow-2xs">
                                                <span>Buka Edisi</span>
                                                <span>&rarr;</span>
                                            </a>
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
        {{-- Tampil jika mode === 'bulanan' ATAU mode === 'semua'                --}}
        {{-- =================================================================== --}}
        @if($mode === 'bulanan' || $mode === 'semua')
            <div class="space-y-4 pt-4 border-t-2 border-slate-200/80">
                <div class="flex items-center gap-2 pt-2">
                    <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold bg-blue-600 text-white">BAGIAN 2</span>
                    <h2 class="text-base font-extrabold text-slate-900 tracking-tight">Tutup Data Penjualan Bulanan (Per Bulan)</h2>
                    <span class="text-xs text-slate-400 font-mono">Periode: {{ $periodeSaatIni->periode }}</span>
                </div>

                <!-- Panel Status Periode Bulan Saat Ini -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-semibold text-blue-600 uppercase tracking-wider">Periode Penjualan Bulanan</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="text-[11px] font-mono text-slate-500">{{ $periodeSaatIni->periode_key }}</span>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-900 mt-0.5">{{ $periodeSaatIni->periode }}</h3>
                        </div>
                        <div>
                            @if($periodeSaatIni->is_locked)
                                <div class="text-right">
                                    <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-purple-100 text-purple-800 border border-purple-300 inline-flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                        <span>{{ $periodeSaatIni->status_label }}</span>
                                    </span>
                                    @if($periodeSaatIni->closed_at)
                                        <p class="text-[10px] text-slate-400 mt-1 font-mono">Ditutup: {{ $periodeSaatIni->closed_at }} ({{ $periodeSaatIni->closed_by }})</p>
                                    @endif
                                </div>
                            @else
                                <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-amber-50 text-amber-800 border border-amber-300 inline-flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span>{{ $periodeSaatIni->status_label }}</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Matriks Ringkasan Bulanan -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 my-4 text-xs">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <span class="text-slate-500 text-[11px]">Total Oplah Cetak Bulanan:</span>
                            <div class="text-sm font-extrabold text-slate-900 mt-1">{{ number_format($periodeSaatIni->total_oplah_cetak, 0, ',', '.') }} eks</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">Akumulasi Bulan {{ $periodeSaatIni->periode }}</div>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <span class="text-slate-500 text-[11px]">Koran Terjual (Nett):</span>
                            <div class="text-sm font-extrabold text-emerald-700 mt-1">{{ number_format($periodeSaatIni->total_terjual, 0, ',', '.') }} eks</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">Terdistribusi ke Mitra Agen</div>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <span class="text-slate-500 text-[11px]">Total Retur (Fisik BAPR):</span>
                            <div class="text-sm font-extrabold text-rose-600 mt-1">{{ number_format($periodeSaatIni->total_retur_eks, 0, ',', '.') }} eks ({{ $periodeSaatIni->rasio_retur }}%)</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">Potongan Rp {{ number_format($periodeSaatIni->potongan_retur, 0, ',', '.') }}</div>
                        </div>
                        <div class="p-3 bg-blue-50/60 rounded-xl border border-blue-200">
                            <span class="text-blue-800 text-[11px] font-semibold">Omset Bersih Sirkulasi:</span>
                            <div class="text-sm font-extrabold text-blue-950 mt-1 font-mono">Rp {{ number_format($periodeSaatIni->omset_bersih, 0, ',', '.') }}</div>
                            <div class="text-[10px] text-blue-600 mt-0.5">Siap Ditarik Piutang Akuntansi</div>
                        </div>
                    </div>

                    @if($periodeSaatIni->is_locked && $permintaanBukaBulanan)
                        <!-- Card Peringatan Pengajuan Buka Kunci Sedang Berjalan (Bulanan) -->
                        <div class="print:hidden p-4 bg-amber-50/90 border border-amber-300 rounded-xl space-y-3 shadow-2xs">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-amber-100 border border-amber-300 text-amber-800 flex items-center justify-center font-bold text-sm shrink-0">
                                        ⏳
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-extrabold text-amber-950 uppercase tracking-wider">Permintaan Buka Kunci Periode Diajukan ke Akuntansi</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-200 text-amber-900 border border-amber-300">{{ $permintaanBukaBulanan->id }}</span>
                                        </div>
                                        <p class="text-[11px] text-amber-800 mt-0.5">
                                            Data penjualan bulanan periode <span class="font-semibold">{{ $periodeSaatIni->periode }}</span> tetap terkunci. Menunggu persetujuan & otorisasi Bagian Akuntansi.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <form method="POST" action="{{ route('sirkulasi.jatah.tutup.cancel-request-unlock') }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan permohonan buka kunci transaksi bulanan ini?')">
                                        @csrf
                                        <input type="hidden" name="mode" value="bulanan">
                                        <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-white border border-amber-300 text-amber-900 hover:bg-amber-100 transition-colors cursor-pointer shadow-2xs">
                                            Batalkan Permintaan
                                        </button>
                                    </form>
                                    @if($canDirectUnlock)
                                        <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                                            @csrf
                                            <input type="hidden" name="mode" value="bulanan">
                                            <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                                            <input type="hidden" name="action" value="unlock">
                                            <button type="submit" onclick="return confirm('Buka kunci penjualan periode {{ $bulanPilihan }} sekarang? (Otoritas Akuntansi)')" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition-colors cursor-pointer">
                                                🔓 Setujui & Buka Kunci
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <div class="p-3 bg-white/90 rounded-lg border border-amber-200/80 text-xs text-slate-700 grid grid-cols-1 md:grid-cols-2 gap-2.5">
                                <div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kategori & Alasan Pengajuan Sirkulasi:</div>
                                    <div class="font-semibold text-slate-900 mt-0.5 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>{{ $permintaanBukaBulanan->kategori_alasan }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-600 italic mt-1 bg-amber-50/50 p-2 rounded border border-amber-100">
                                        "{{ $permintaanBukaBulanan->alasan }}"
                                    </div>
                                </div>
                                <div class="space-y-1.5 text-[11px]">
                                    @if(!empty($permintaanBukaBulanan->estimasi_eksemplar) || !empty($permintaanBukaBulanan->estimasi_nominal))
                                        <div class="flex items-center justify-between py-0.5 border-b border-slate-100">
                                            <span class="text-slate-500">Estimasi Penjualan Baru:</span>
                                            <span class="font-bold text-blue-700 font-mono">
                                                @if(!empty($permintaanBukaBulanan->estimasi_eksemplar))
                                                    {{ number_format($permintaanBukaBulanan->estimasi_eksemplar) }} eks
                                                @endif
                                                @if(!empty($permintaanBukaBulanan->estimasi_nominal))
                                                    (Rp {{ number_format($permintaanBukaBulanan->estimasi_nominal, 0, ',', '.') }})
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                    <div class="flex items-center justify-between py-0.5 border-b border-slate-100">
                                        <span class="text-slate-500">Pemohon (Sirkulasi):</span>
                                        <span class="font-semibold text-slate-800">{{ $permintaanBukaBulanan->pemohon }}</span>
                                    </div>
                                    <div class="flex items-center justify-between py-0.5">
                                        <span class="text-slate-500">Waktu Pengajuan:</span>
                                        <span class="font-mono text-slate-600 text-[10px]">{{ $permintaanBukaBulanan->created_at }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Tombol Aksi Kunci Data Bulanan Standard -->
                        <div class="print:hidden p-4 {{ $periodeSaatIni->is_locked ? 'bg-slate-50 border-slate-200' : 'bg-blue-50/70 border-blue-200' }} border rounded-xl flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                            <div class="{{ $periodeSaatIni->is_locked ? 'text-slate-700' : 'text-blue-900' }} leading-relaxed">
                                @if($periodeSaatIni->is_locked)
                                    <strong class="font-bold text-blue-950">Status Data Bulanan: TERKUNCI (LOCKED).</strong> Data penjualan bulanan periode <span class="font-semibold">{{ $periodeSaatIni->periode }}</span> telah ditutup dan dikunci. Sesuai SOP, pembukaan transaksi hanya memiliki otorisasi oleh <span class="font-semibold text-indigo-900">Divisi Akuntansi</span>. Jika ada penjualan baru susulan, silakan ajukan permohonan buka kunci.
                                @else
                                    <strong class="font-bold">Konfirmasi Penguncian Bulanan:</strong> Setelah status dikunci (<span class="font-mono font-bold">READY TO PULL AKUNTING</span>), staf Sirkulasi tidak dapat merubah jatah dan retur koran periode ini agar sinkronisasi kartu piutang di Akuntansi konsisten.
                                @endif
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" onclick="window.print()" class="px-3 py-2 text-xs font-bold rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-100 transition-colors inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                    <span>Cetak Rekap Bulanan</span>
                                </button>

                                @if($periodeSaatIni->is_locked)
                                    @if($canDirectUnlock)
                                        <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                                            @csrf
                                            <input type="hidden" name="mode" value="bulanan">
                                            <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                                            <input type="hidden" name="action" value="unlock">
                                            <button type="submit" 
                                                    onclick="return confirm('Apakah Anda yakin ingin MEMBUKA KEMBALI kunci penjualan periode {{ $bulanPilihan }}? (Otoritas Akuntansi)')" 
                                                    class="px-3.5 py-2 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition-colors shrink-0 cursor-pointer">
                                                🔓 Buka Kunci Bulanan (Akuntansi)
                                            </button>
                                        </form>
                                    @else
                                        <!-- Tombol Permintaan Buka Kunci ke Akuntansi untuk Sirkulasi -->
                                        <button type="button" 
                                                @click="openModalRequestBulanan = true" 
                                                class="px-3.5 py-2 text-xs font-bold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs transition-colors shrink-0 inline-flex items-center gap-1.5 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                            <span>Minta Buka Kunci ke Akuntansi</span>
                                        </button>
                                    @endif
                                @else
                                    <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                                        @csrf
                                        <input type="hidden" name="mode" value="bulanan">
                                        <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                                        <input type="hidden" name="action" value="lock">
                                        <button type="submit" 
                                                onclick="return confirm('Apakah Anda yakin ingin MENUTUP & MENGUNCI data penjualan periode {{ $bulanPilihan }}? Data akan diserahkan ke Divisi Akuntansi.')" 
                                                class="px-4 py-2 text-xs font-bold rounded-lg bg-blue-700 hover:bg-blue-800 text-white shadow-xs transition-colors shrink-0 cursor-pointer">
                                            🔒 Kunci & Tutup Data Penjualan Bulanan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Riwayat Tutup Data Bulan Sebelumnya -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Riwayat Penutupan Buku Periode Lalu (Bulanan)</h3>
                        <span class="text-[11px] font-semibold text-slate-500">Histori Bulanan</span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[1050px] text-left text-xs text-slate-600">
                            <thead class="bg-slate-100/75 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-3 px-4 whitespace-nowrap min-w-[160px]">Periode Bulan</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap min-w-[110px]">Oplah Cetak</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap min-w-[105px]">Retur (Eks)</th>
                                    <th class="py-3 px-4 text-center whitespace-nowrap min-w-[95px]">Rasio Retur</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap min-w-[140px]">Omset Bersih</th>
                                    <th class="py-3 px-4 text-center whitespace-nowrap min-w-[150px]">Status Akuntansi</th>
                                    <th class="py-3 px-4 whitespace-nowrap min-w-[140px]">Waktu Closing</th>
                                    <th class="py-3 px-4 whitespace-nowrap min-w-[130px]">Otorisator</th>
                                    <th class="print:hidden py-3 px-4 text-center whitespace-nowrap min-w-[100px]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach($riwayatClosingBulanan as $rc)
                                    <tr class="hover:bg-slate-50/80 transition-colors {{ $rc->periode_key === $bulanPilihan ? 'bg-blue-50/40' : '' }}">
                                        <td class="py-3 px-4 whitespace-nowrap font-bold text-slate-900">
                                            {{ $rc->periode }}
                                            <div class="text-[10px] font-mono text-slate-400 font-normal">{{ $rc->periode_key }}</div>
                                        </td>
                                        <td class="py-3 px-4 text-right whitespace-nowrap font-mono tabular-nums">
                                            {{ number_format($rc->total_oplah_cetak, 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-400">eks</span>
                                        </td>
                                        <td class="py-3 px-4 text-right text-rose-600 whitespace-nowrap font-mono font-bold tabular-nums">
                                            {{ number_format($rc->total_retur_eks, 0, ',', '.') }} <span class="text-[10px] font-normal text-rose-500/70">eks</span>
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-[10.5px] font-bold bg-slate-100 text-slate-700 tabular-nums">
                                                {{ $rc->rasio_retur }}%
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-right font-mono font-extrabold text-slate-900 text-xs whitespace-nowrap tabular-nums">
                                            Rp&nbsp;{{ number_format($rc->total_omset_bersih, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            @if($rc->status === 'PULLED')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                                    {{ $rc->status_label }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>
                                                    {{ $rc->status_label }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 font-mono text-[11px] text-slate-500 whitespace-nowrap">
                                            {{ $rc->closed_at }}
                                        </td>
                                        <td class="py-3 px-4 text-slate-700 whitespace-nowrap">
                                            {{ $rc->closed_by }}
                                        </td>
                                        <td class="print:hidden py-3 px-4 text-center whitespace-nowrap">
                                            <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'bulanan', 'bulan' => $rc->periode_key]) }}" 
                                               class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold text-blue-700 hover:text-blue-900 hover:bg-blue-50 border border-blue-200 rounded-lg transition-colors shadow-2xs">
                                                <span>Buka Periode</span>
                                                <span>&rarr;</span>
                                            </a>
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
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between gap-3 flex-wrap">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold shrink-0">
                            📨
                        </div>
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Daftar Pengajuan Buka Kunci Transaksi ke Divisi Akuntansi</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">Monitoring status persetujuan dari Bagian Akuntansi untuk transaksi penjualan yang terkunci</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-mono font-bold text-slate-700 bg-white border border-slate-200 px-3 py-1 rounded-lg shadow-2xs shrink-0">
                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                        {{ $semuaPermintaanBuka->count() }} Pengajuan Terdaftar
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[950px] text-left text-xs text-slate-600">
                        <thead class="bg-slate-100/75 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4 whitespace-nowrap min-w-[130px]">No. Tiket</th>
                                <th class="py-3 px-4 whitespace-nowrap min-w-[150px]">Target Transaksi</th>
                                <th class="py-3 px-4 whitespace-nowrap min-w-[220px]">Kategori & Alasan</th>
                                <th class="py-3 px-4 text-right whitespace-nowrap min-w-[120px]">Estimasi Susulan</th>
                                <th class="py-3 px-4 whitespace-nowrap min-w-[140px]">Pemohon (Sirkulasi)</th>
                                <th class="py-3 px-4 text-center whitespace-nowrap min-w-[150px]">Status Akuntansi</th>
                                <th class="py-3 px-4 text-center whitespace-nowrap min-w-[100px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($semuaPermintaanBuka as $req)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3 px-4 font-mono font-bold text-indigo-700 whitespace-nowrap">
                                        {{ $req->id }}
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div class="font-bold text-slate-900">{{ $req->target_label }}</div>
                                        <span class="inline-block text-[10px] px-1.5 py-0.2 rounded font-semibold uppercase {{ $req->mode === 'harian' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $req->mode }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-slate-800 text-[11px]">{{ $req->kategori_alasan }}</div>
                                        <div class="text-slate-500 italic text-[11px] mt-0.5 line-clamp-2">"{{ $req->alasan }}"</div>
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono text-[11px] whitespace-nowrap">
                                        @if(!empty($req->estimasi_eksemplar))
                                            <span class="font-bold text-slate-800">{{ number_format($req->estimasi_eksemplar) }} eks</span><br>
                                        @endif
                                        @if(!empty($req->estimasi_nominal))
                                            <span class="text-slate-500">Rp {{ number_format($req->estimasi_nominal, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div class="font-semibold text-slate-800">{{ $req->pemohon }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $req->created_at }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        @if($req->status === 'APPROVED')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                Disetujui & Dibuka
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Menunggu Otorisasi Akuntansi
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        @if($req->status === 'PENDING')
                                            <form method="POST" action="{{ route('sirkulasi.jatah.tutup.cancel-request-unlock') }}" onsubmit="return confirm('Batalkan pengajuan {{ $req->id }}?')">
                                                @csrf
                                                <input type="hidden" name="mode" value="{{ $req->mode }}">
                                                <input type="hidden" name="{{ $req->mode === 'harian' ? 'tanggal' : 'periode' }}" value="{{ $req->target }}">
                                                <button type="submit" class="text-[11px] font-semibold text-rose-600 hover:text-rose-800 hover:underline cursor-pointer">
                                                    Batalkan
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[11px] text-slate-400">Selesai</span>
                                        @endif
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
                <!-- Backdrop -->
                <div x-show="openModalRequestHarian" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs" 
                     @click="openModalRequestHarian = false"></div>

                <!-- Modal Dialog -->
                <div x-show="openModalRequestHarian" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative inline-block w-full max-w-xl p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl border border-slate-200">
                    
                    <!-- Header Modal -->
                    <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-700 flex items-center justify-center font-bold text-lg shrink-0">
                                📨
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 tracking-tight" id="modal-title-harian">
                                    Pengajuan Buka Kunci Transaksi Harian ke Akuntansi
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Permintaan pembukaan data edisi <span class="font-semibold text-slate-700">{{ $harianSaatIni->tanggal }}</span>
                                </p>
                            </div>
                        </div>
                        <button type="button" @click="openModalRequestHarian = false" class="text-slate-400 hover:text-slate-600 rounded-lg p-1.5 transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- SOP Notice Box -->
                    <div class="mt-4 p-3 bg-indigo-50/70 border border-indigo-200 rounded-xl text-xs text-indigo-900 leading-relaxed flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <strong class="font-bold">Ketentuan Otoritas Akuntansi:</strong> Sesuai SOP, transaksi yang telah ditutup hanya dapat dibuka kembali oleh Bagian Akuntansi. Formulir ini akan dikirimkan ke Akuntansi untuk verifikasi sebelum kunci dibuka.
                        </div>
                    </div>

                    <!-- Form Permintaan -->
                    <form method="POST" action="{{ route('sirkulasi.jatah.tutup.request-unlock') }}" class="mt-4 space-y-4">
                        @csrf
                        <input type="hidden" name="mode" value="harian">
                        <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">

                        <!-- Target Tanggal -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Target Transaksi Ditutup:</label>
                            <div class="px-3 py-2 bg-slate-100 rounded-lg border border-slate-200 text-xs font-mono font-semibold text-slate-800 flex items-center justify-between">
                                <span>📅 {{ $harianSaatIni->tanggal }}</span>
                                <span class="text-[11px] text-slate-500 font-sans">Edisi: {{ $tanggalPilihan }}</span>
                            </div>
                        </div>

                        <!-- Kategori Alasan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Masalah / Kebutuhan:</label>
                            <select name="kategori_alasan" x-model="kategoriHarian" class="w-full text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-2">
                                <option value="Penjualan Baru / Susulan Masuk">Penjualan Baru / Susulan Agen (Terlambat lapor saat closing)</option>
                                <option value="Koreksi Retur Fisik Agen Terlambat">Koreksi Fisik Retur Koran Terlambat Masuk</option>
                                <option value="Penyesuaian Jatah Ekspedisi Luar Kota">Penyesuaian Jatah Ekspedisi / Armada</option>
                                <option value="Koreksi Salah Input Data Jatah">Koreksi Salah Input Data Jatah / Pelanggan</option>
                                <option value="Lainnya">Lain-lain / Keperluan Khusus</option>
                            </select>
                        </div>

                        <!-- Textarea Alasan (Wajib) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Rincian Alasan Pengajuan Pembukaan <span class="text-rose-600">*</span>:
                            </label>
                            <textarea name="alasan" 
                                      rows="3" 
                                      required 
                                      minlength="5"
                                      placeholder="Contoh: Terdapat penjualan baru susulan dari Agen Kios Barokah (Pasar Cikurubuk) sebanyak 120 eksemplar yang baru disetor setelah jam penutupan harian. Mohon izin dibuka transaksi agar faktur dapat diterbitkan."
                                      class="w-full text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 leading-relaxed"></textarea>
                            <span class="text-[11px] text-slate-400 mt-1 block">Tuliskan nama agen, perkiraan eksemplar, atau kendala spesifik agar Bagian Akuntansi segera memverifikasi.</span>
                        </div>

                        <!-- Estimasi Eksemplar & Nominal (Opsional) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Estimasi Eksemplar Susulan (Eks):</label>
                                <input type="number" 
                                       name="estimasi_eksemplar" 
                                       placeholder="Contoh: 120" 
                                       min="0"
                                       class="w-full text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Estimasi Nilai Susulan (Rp):</label>
                                <input type="number" 
                                       name="estimasi_nominal" 
                                       placeholder="Contoh: 360000" 
                                       min="0"
                                       class="w-full text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5">
                            </div>
                        </div>

                        <!-- Info Pemohon -->
                        <div class="flex items-center justify-between text-[11px] text-slate-500 px-1 pt-1">
                            <span>Pemohon: <strong class="text-slate-800">{{ auth()->user()?->name ?? 'Staf Sirkulasi' }}</strong> (Divisi Sirkulasi)</span>
                            <span class="font-mono">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} WIB</span>
                        </div>

                        <!-- Footer Modal Buttons -->
                        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                            <button type="button" 
                                    @click="openModalRequestHarian = false" 
                                    class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                <span>Kirim Permintaan ke Akuntansi</span>
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
                <!-- Backdrop -->
                <div x-show="openModalRequestBulanan" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs" 
                     @click="openModalRequestBulanan = false"></div>

                <!-- Modal Dialog -->
                <div x-show="openModalRequestBulanan" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative inline-block w-full max-w-xl p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl border border-slate-200">
                    
                    <!-- Header Modal -->
                    <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-700 flex items-center justify-center font-bold text-lg shrink-0">
                                🗓️
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 tracking-tight" id="modal-title-bulanan">
                                    Pengajuan Buka Kunci Transaksi Bulanan ke Akuntansi
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Permintaan pembukaan buku periode <span class="font-semibold text-slate-700">{{ $periodeSaatIni->periode }}</span>
                                </p>
                            </div>
                        </div>
                        <button type="button" @click="openModalRequestBulanan = false" class="text-slate-400 hover:text-slate-600 rounded-lg p-1.5 transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- SOP Notice Box -->
                    <div class="mt-4 p-3 bg-indigo-50/70 border border-indigo-200 rounded-xl text-xs text-indigo-900 leading-relaxed flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <strong class="font-bold">Ketentuan Otoritas Akuntansi:</strong> Penutupan data bulanan merupakan dasar sinkronisasi piutang dan buku besar. Pembukaan kunci hanya dapat dilakukan oleh Divisi Akuntansi setelah formulir ini disetujui.
                        </div>
                    </div>

                    <!-- Form Permintaan -->
                    <form method="POST" action="{{ route('sirkulasi.jatah.tutup.request-unlock') }}" class="mt-4 space-y-4">
                        @csrf
                        <input type="hidden" name="mode" value="bulanan">
                        <input type="hidden" name="periode" value="{{ $bulanPilihan }}">

                        <!-- Target Bulan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Target Periode Ditutup:</label>
                            <div class="px-3 py-2 bg-slate-100 rounded-lg border border-slate-200 text-xs font-mono font-semibold text-slate-800 flex items-center justify-between">
                                <span>🗓️ Periode: {{ $periodeSaatIni->periode }}</span>
                                <span class="text-[11px] text-slate-500 font-sans">Kode: {{ $bulanPilihan }}</span>
                            </div>
                        </div>

                        <!-- Kategori Alasan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Masalah / Kebutuhan:</label>
                            <select name="kategori_alasan" x-model="kategoriBulanan" class="w-full text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-2">
                                <option value="Penjualan Baru / Susulan Masuk">Penjualan Susulan Akhir Bulan Masuk Terlambat</option>
                                <option value="Rekonsiliasi Retur Fisik Agen BAPR">Rekonsiliasi Retur Fisik BAPR Terlambat Diterima</option>
                                <option value="Koreksi Selisih Oplah & Ekspedisi">Koreksi Selisih Oplah & Pengiriman Ekspedisi</option>
                                <option value="Lainnya">Lain-lain / Keperluan Audit Khusus</option>
                            </select>
                        </div>

                        <!-- Textarea Alasan (Wajib) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Rincian Alasan Pengajuan Pembukaan <span class="text-rose-600">*</span>:
                            </label>
                            <textarea name="alasan" 
                                      rows="3" 
                                      required 
                                      minlength="5"
                                      placeholder="Contoh: Terdapat rekap penjualan akhir bulan susulan dari perwakilan biro daerah yang belum sempat terinput sebelum penutupan buku bulanan. Mohon izin dibuka transaksi."
                                      class="w-full text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 leading-relaxed"></textarea>
                            <span class="text-[11px] text-slate-400 mt-1 block">Tuliskan keterangan detail untuk verifikasi Divisi Akuntansi.</span>
                        </div>

                        <!-- Estimasi Eksemplar & Nominal (Opsional) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Estimasi Eksemplar Susulan (Eks):</label>
                                <input type="number" 
                                       name="estimasi_eksemplar" 
                                       placeholder="Contoh: 500" 
                                       min="0"
                                       class="w-full text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Estimasi Nilai Susulan (Rp):</label>
                                <input type="number" 
                                       name="estimasi_nominal" 
                                       placeholder="Contoh: 1500000" 
                                       min="0"
                                       class="w-full text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5">
                            </div>
                        </div>

                        <!-- Info Pemohon -->
                        <div class="flex items-center justify-between text-[11px] text-slate-500 px-1 pt-1">
                            <span>Pemohon: <strong class="text-slate-800">{{ auth()->user()?->name ?? 'Staf Sirkulasi' }}</strong> (Divisi Sirkulasi)</span>
                            <span class="font-mono">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} WIB</span>
                        </div>

                        <!-- Footer Modal Buttons -->
                        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                            <button type="button" 
                                    @click="openModalRequestBulanan = false" 
                                    class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                <span>Kirim Permintaan ke Akuntansi</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
