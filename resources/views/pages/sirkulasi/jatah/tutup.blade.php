<x-app-layout>
    <div class="space-y-5" x-data="{ openModalRequestHarian: false, openModalRequestBulanan: false, kategoriHarian: 'Penjualan Baru / Susulan Masuk', kategoriBulanan: 'Penjualan Baru / Susulan Masuk' }">
        @php
            $canDirectUnlock = $canDirectUnlock ?? (auth()->user() && (auth()->user()->isAdmin() || auth()->user()->hasRole('akuntansi', 'accounting', 'keuangan')));
        @endphp

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 no-print print:hidden">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Tutup Data Penjualan (Cut-Off &amp; Memo)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Penguncian jatah koran harian/bulanan dan pengajuan memo buka kunci ke Akuntansi.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Berita Acara</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="no-print print:hidden p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between gap-2 shadow-2xs">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="window.print()" class="h-9 px-3.5 text-xs font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white shadow-xs transition inline-flex items-center cursor-pointer">
                    <span>Cetak Berita Acara</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="no-print print:hidden p-3.5 bg-slate-100 border border-slate-300 text-slate-800 rounded-xl text-xs sm:text-sm font-semibold shadow-2xs">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Toolbar Mode & Filter -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs no-print print:hidden space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'semua', 'tanggal' => $tanggalPilihan, 'bulan' => $bulanPilihan]) }}"
                       class="inline-flex items-center h-9 px-3.5 text-xs font-bold rounded-xl transition cursor-pointer {{ $mode === 'semua' ? 'bg-kp-blue-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold' }}">
                        <span>Harian &amp; Bulanan</span>
                    </a>
                    <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $tanggalPilihan]) }}"
                       class="inline-flex items-center h-9 px-3.5 text-xs font-bold rounded-xl transition cursor-pointer {{ $mode === 'harian' ? 'bg-kp-blue-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold' }}">
                        <span>Tutup Harian</span>
                    </a>
                    <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'bulanan', 'bulan' => $bulanPilihan]) }}"
                       class="inline-flex items-center h-9 px-3.5 text-xs font-bold rounded-xl transition cursor-pointer {{ $mode === 'bulanan' ? 'bg-kp-blue-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold' }}">
                        <span>Tutup Bulanan</span>
                    </a>
                </div>

                @if($mode === 'harian')
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => \Carbon\Carbon::today()->toDateString()]) }}"
                           class="px-3 py-1.5 text-xs font-semibold rounded-xl border transition cursor-pointer {{ $tanggalPilihan === \Carbon\Carbon::today()->toDateString() ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50' }}">
                            Hari Ini
                        </a>
                        <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => \Carbon\Carbon::yesterday()->toDateString()]) }}"
                           class="px-3 py-1.5 text-xs font-semibold rounded-xl border transition cursor-pointer {{ $tanggalPilihan === \Carbon\Carbon::yesterday()->toDateString() ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50' }}">
                            Kemarin
                        </a>
                    </div>
                @endif
            </div>

            @if($mode === 'bulanan')
                <form method="GET" action="{{ route('sirkulasi.jatah.tutup') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <input type="hidden" name="mode" value="bulanan">
                    <div class="sm:col-span-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Periode Bulan</label>
                        <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                    <div class="sm:col-span-3">
                        <button type="submit" class="w-full h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer">
                            Filter Bulan
                        </button>
                    </div>
                </form>
            @elseif($mode === 'harian')
                <form method="GET" action="{{ route('sirkulasi.jatah.tutup') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <input type="hidden" name="mode" value="harian">
                    <div class="sm:col-span-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Edisi Harian</label>
                        <input type="date" name="tanggal" value="{{ $tanggalPilihan }}" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                    <div class="sm:col-span-3">
                        <button type="submit" class="w-full h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer">
                            Filter Tanggal
                        </button>
                    </div>
                </form>
            @else
                <form method="GET" action="{{ route('sirkulasi.jatah.tutup') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <input type="hidden" name="mode" value="semua">
                    <div class="sm:col-span-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Edisi Harian</label>
                        <input type="date" name="tanggal" value="{{ $tanggalPilihan }}" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                    <div class="sm:col-span-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Periode Bulan</label>
                        <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit" class="w-full h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer">
                            Terapkan
                        </button>
                    </div>
                </form>
            @endif
        </div>

        <!-- KOP DOKUMEN CETAK RESMI -->
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

        {{-- BAGIAN 1: TUTUP DATA PENJUALAN HARIAN --}}
        @if($mode === 'harian' || $mode === 'semua')
            <div class="space-y-5">
                <!-- 4 KPI Harian -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Oplah Cetak Harian</span>
                        <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">{{ number_format($harianSaatIni->total_oplah_cetak, 0, ',', '.') }}</p>
                        <span class="text-xs text-slate-400 mt-1 block">Edisi {{ $harianSaatIni->tanggal_singkat }} (Eks)</span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Koran Terjual (Nett)</span>
                        <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($harianSaatIni->total_terjual, 0, ',', '.') }}</p>
                        <span class="text-xs text-slate-400 mt-1 block">Konsinyasi &amp; langganan (Eks)</span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Retur Fisik</span>
                        <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($harianSaatIni->total_retur_eks, 0, ',', '.') }}</p>
                        <span class="text-xs text-slate-400 mt-1 block">Rasio {{ $harianSaatIni->rasio_retur }}% &bull; Rp {{ number_format($harianSaatIni->potongan_retur, 0, ',', '.') }}</span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Omset Bersih Harian</span>
                        <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">Rp {{ number_format($harianSaatIni->omset_bersih, 0, ',', '.') }}</p>
                        <span class="text-xs text-slate-400 mt-1 block">{{ $harianSaatIni->status_label }}</span>
                    </div>
                </div>

                <!-- Panel Status & Aksi Cut-Off Harian -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Tutup Penjualan Harian &mdash; {{ $harianSaatIni->tanggal }}</h2>
                            <p class="text-xs text-slate-500 mt-0.5 font-mono">{{ $harianSaatIni->tanggal_key }}</p>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($harianSaatIni->is_locked)
                                <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                    {{ $harianSaatIni->status_label }}
                                </span>
                                <span class="text-xs text-slate-400 font-mono">{{ $harianSaatIni->closed_at }} ({{ $harianSaatIni->closed_by }})</span>
                            @else
                                <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $harianSaatIni->status_label }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 no-print print:hidden">
                        @if($harianSaatIni->is_locked && $permintaanBukaHarian)
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Permintaan Buka Kunci Diajukan</span>
                                            <span class="px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">{{ $permintaanBukaHarian->id }}</span>
                                        </div>
                                        <p class="text-xs text-slate-600 mt-1">
                                            Menunggu persetujuan Akuntansi untuk edisi {{ $harianSaatIni->tanggal }}.
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <form method="POST" action="{{ route('sirkulasi.jatah.tutup.cancel-request-unlock') }}" onsubmit="return confirm('Batalkan permohonan buka kunci transaksi harian ini?')">
                                            @csrf
                                            <input type="hidden" name="mode" value="harian">
                                            <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                                            <button type="submit" class="h-9 px-3.5 text-xs font-semibold rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 transition cursor-pointer">
                                                Batalkan
                                            </button>
                                        </form>
                                        @if($canDirectUnlock)
                                            <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                                                @csrf
                                                <input type="hidden" name="mode" value="harian">
                                                <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                                                <input type="hidden" name="action" value="unlock">
                                                <button type="submit" onclick="return confirm('Buka kunci penjualan harian tanggal {{ $tanggalPilihan }} sekarang?')" class="h-9 px-3.5 text-xs font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white shadow-xs transition cursor-pointer">
                                                    Setujui &amp; Buka
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-3 bg-white rounded-xl border border-slate-200 text-xs text-slate-700 grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <span class="text-xs font-semibold text-slate-500 block">Kategori &amp; Alasan</span>
                                        <div class="font-semibold text-slate-900 mt-0.5">{{ $permintaanBukaHarian->kategori_alasan }}</div>
                                        <div class="text-slate-600 mt-0.5">"{{ $permintaanBukaHarian->alasan }}"</div>
                                    </div>
                                    <div class="space-y-1">
                                        @if(!empty($permintaanBukaHarian->estimasi_eksemplar) || !empty($permintaanBukaHarian->estimasi_nominal))
                                            <div class="flex items-center justify-between py-0.5 border-b border-slate-100">
                                                <span class="text-slate-500">Estimasi Susulan</span>
                                                <span class="font-bold text-kp-blue-700 font-mono tabular-nums">
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
                                            <span class="text-slate-500">Pemohon</span>
                                            <span class="font-semibold text-slate-900">{{ $permintaanBukaHarian->pemohon }}</span>
                                        </div>
                                        <div class="flex items-center justify-between py-0.5">
                                            <span class="text-slate-500">Waktu Pengajuan</span>
                                            <span class="font-mono text-slate-600">{{ $permintaanBukaHarian->created_at }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs sm:text-sm text-slate-600">
                                <div>
                                    @if($harianSaatIni->is_locked)
                                        <span class="font-semibold text-slate-900">Edisi {{ $harianSaatIni->tanggal }} terkunci.</span> Pembukaan kunci memerlukan persetujuan Akuntansi.
                                    @else
                                        <span class="font-semibold text-slate-900">Edisi {{ $harianSaatIni->tanggal_singkat }} terbuka.</span> Kunci data setelah jatah dan retur tervalidasi.
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" onclick="window.print()" class="h-10 px-3.5 text-xs font-semibold rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 transition inline-flex items-center cursor-pointer">
                                        <span>Cetak Slip</span>
                                    </button>

                                    @if($harianSaatIni->is_locked)
                                        @if($canDirectUnlock)
                                            <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                                                @csrf
                                                <input type="hidden" name="mode" value="harian">
                                                <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                                                <input type="hidden" name="action" value="unlock">
                                                <button type="submit"
                                                        onclick="return confirm('Buka kembali kunci penjualan tanggal {{ $tanggalPilihan }}?')"
                                                        class="h-10 px-4 text-xs font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white shadow-xs transition shrink-0 cursor-pointer">
                                                    Buka Kunci Harian
                                                </button>
                                            </form>
                                        @else
                                            <button type="button"
                                                    @click="openModalRequestHarian = true"
                                                    class="h-10 px-4 text-xs font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white shadow-xs transition shrink-0 inline-flex items-center cursor-pointer">
                                                <span>Ajukan Buka Kunci</span>
                                            </button>
                                        @endif
                                    @else
                                        <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                                            @csrf
                                            <input type="hidden" name="mode" value="harian">
                                            <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                                            <input type="hidden" name="action" value="lock">
                                            <button type="submit"
                                                    onclick="return confirm('Tutup & kunci data penjualan harian tanggal {{ $tanggalPilihan }}?')"
                                                    class="h-10 px-4 text-xs font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white shadow-xs transition shrink-0 cursor-pointer">
                                                Kunci Penjualan Harian
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Rincian Rekapitulasi Jatah & Retur Harian Per Jurusan -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Rekapitulasi Edisi Harian per Jurusan</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Edisi {{ $harianSaatIni->tanggal }}</p>
                        </div>
                        <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono w-fit">
                            {{ $rekapJurusanHarian->count() }} Jurusan Armada
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                <tr>
                                    <th class="py-3 px-3.5 text-center w-12">No</th>
                                    <th class="py-3 px-3.5 w-20">Kode</th>
                                    <th class="py-3 px-3.5">Nama Jurusan</th>
                                    <th class="py-3 px-3.5">Titik Tujuan / Rute</th>
                                    <th class="py-3 px-3.5 text-right">Oplah Cetak</th>
                                    <th class="py-3 px-3.5 text-right">Terjual (Nett)</th>
                                    <th class="py-3 px-3.5 text-right">Retur (Eks)</th>
                                    <th class="py-3 px-3.5 text-right">Rasio Retur</th>
                                    <th class="py-3 px-3.5 text-right w-36">Omset Bersih</th>
                                    <th class="py-3 px-3.5 text-center">Status Data</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                                @foreach($rekapJurusanHarian as $idx => $rj)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal w-12">
                                            {{ $idx + 1 }}
                                        </td>
                                        <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                            {{ $rj->kode }}
                                        </td>
                                        <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                            {{ $rj->nama }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-slate-600 font-normal max-w-xs truncate" title="{{ $rj->wilayah }}">
                                            {{ $rj->wilayah }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right font-mono text-slate-800 tabular-nums">
                                            {{ number_format($rj->oplah, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-kp-blue-700 tabular-nums">
                                            {{ number_format($rj->terjual, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums">
                                            {{ number_format($rj->retur, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">
                                            {{ $rj->rasio }}%
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right whitespace-nowrap">
                                            <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                                <span class="text-slate-400 font-normal select-none">Rp</span>
                                                <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($rj->omset, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-3.5 text-center">
                                            @if($rj->status === 'TERKUNCI')
                                                <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                    Terkunci
                                                </span>
                                            @else
                                                <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                    Valid
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                                <tr>
                                    <td colspan="4" class="py-3 px-3.5 text-right font-sans text-xs font-bold uppercase tracking-wider text-slate-700">Grand Total Edisi Harian</td>
                                    <td class="py-3 px-3.5 text-right tabular-nums">{{ number_format($rekapJurusanHarian->sum('oplah'), 0, ',', '.') }}</td>
                                    <td class="py-3 px-3.5 text-right text-kp-blue-700 font-bold tabular-nums">{{ number_format($rekapJurusanHarian->sum('terjual'), 0, ',', '.') }}</td>
                                    <td class="py-3 px-3.5 text-right tabular-nums">{{ number_format($rekapJurusanHarian->sum('retur'), 0, ',', '.') }}</td>
                                    <td class="py-3 px-3.5 text-right tabular-nums">
                                        {{ number_format(($rekapJurusanHarian->sum('retur') / ($rekapJurusanHarian->sum('oplah') ?: 1)) * 100, 2) }}%
                                    </td>
                                    <td class="py-3 px-3.5 text-right whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-bold text-kp-blue-700 tabular-nums">{{ number_format($rekapJurusanHarian->sum('omset'), 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3.5 text-center text-slate-400">&mdash;</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Riwayat Tutup Data Penjualan Harian -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Riwayat Penutupan Penjualan Harian</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Rekap penguncian edisi harian terakhir</p>
                        </div>
                        <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono w-fit">
                            7 Hari Terakhir
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                <tr>
                                    <th class="py-3 px-3.5 text-center w-12">No</th>
                                    <th class="py-3 px-3.5">Tanggal Edisi</th>
                                    <th class="py-3 px-3.5 text-right">Oplah Cetak</th>
                                    <th class="py-3 px-3.5 text-right">Koran Terjual</th>
                                    <th class="py-3 px-3.5 text-right">Retur (Eks)</th>
                                    <th class="py-3 px-3.5 text-right">Rasio Retur</th>
                                    <th class="py-3 px-3.5 text-right w-36">Omset Bersih</th>
                                    <th class="py-3 px-3.5 text-center">Status Akuntansi</th>
                                    <th class="py-3 px-3.5">Waktu Closing</th>
                                    <th class="py-3 px-3.5">Otorisator</th>
                                    <th class="print:hidden py-3 px-3.5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                                @foreach($riwayatClosingHarian as $idx => $rc)
                                    <tr class="hover:bg-slate-50/80 transition {{ $rc->tanggal_key === $tanggalPilihan ? 'bg-kp-blue-50/30' : '' }}">
                                        <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal w-12">
                                            {{ $idx + 1 }}
                                        </td>
                                        <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                            {{ $rc->tanggal }}
                                            <span class="block text-xs font-mono text-slate-500 font-normal">{{ $rc->tanggal_key }}</span>
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">{{ number_format($rc->total_oplah_cetak, 0, ',', '.') }}</td>
                                        <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-kp-blue-700 tabular-nums">{{ number_format($rc->total_terjual, 0, ',', '.') }}</td>
                                        <td class="py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums">{{ number_format($rc->total_retur_eks, 0, ',', '.') }}</td>
                                        <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">{{ $rc->rasio_retur }}%</td>
                                        <td class="py-3.5 px-3.5 text-right whitespace-nowrap">
                                            <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                                <span class="text-slate-400 font-normal select-none">Rp</span>
                                                <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($rc->total_omset_bersih, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-3.5 text-center">
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                {{ $rc->status_label }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3.5 font-mono text-slate-600">{{ $rc->closed_at }}</td>
                                        <td class="py-3.5 px-3.5 text-slate-700">{{ $rc->closed_by }}</td>
                                        <td class="print:hidden py-3.5 px-3.5 text-center">
                                            <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'harian', 'tanggal' => $rc->tanggal_key]) }}"
                                               class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 hover:bg-kp-blue-100 border border-kp-blue-200 rounded-md transition">
                                                Buka Edisi
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

        {{-- BAGIAN 2: TUTUP DATA PENJUALAN BULANAN --}}
        @if($mode === 'bulanan' || $mode === 'semua')
            <div class="space-y-5">
                <!-- 4 KPI Bulanan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Oplah Cetak Bulanan</span>
                        <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">{{ number_format($periodeSaatIni->total_oplah_cetak, 0, ',', '.') }}</p>
                        <span class="text-xs text-slate-400 mt-1 block">Periode {{ $periodeSaatIni->periode }} (Eks)</span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Koran Terjual (Nett)</span>
                        <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($periodeSaatIni->total_terjual, 0, ',', '.') }}</p>
                        <span class="text-xs text-slate-400 mt-1 block">Terdistribusi ke agen (Eks)</span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Retur (BAPR)</span>
                        <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($periodeSaatIni->total_retur_eks, 0, ',', '.') }}</p>
                        <span class="text-xs text-slate-400 mt-1 block">Rasio {{ $periodeSaatIni->rasio_retur }}% &bull; Rp {{ number_format($periodeSaatIni->potongan_retur, 0, ',', '.') }}</span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Omset Bersih Bulanan</span>
                        <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">Rp {{ number_format($periodeSaatIni->omset_bersih, 0, ',', '.') }}</p>
                        <span class="text-xs text-slate-400 mt-1 block">{{ $periodeSaatIni->status_label }}</span>
                    </div>
                </div>

                <!-- Panel Status Periode Bulan Saat Ini -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Tutup Penjualan Bulanan &mdash; {{ $periodeSaatIni->periode }}</h2>
                            <p class="text-xs text-slate-500 mt-0.5 font-mono">{{ $periodeSaatIni->periode_key }}</p>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($periodeSaatIni->is_locked)
                                <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                    {{ $periodeSaatIni->status_label }}
                                </span>
                                @if($periodeSaatIni->closed_at)
                                    <span class="text-xs text-slate-400 font-mono">{{ $periodeSaatIni->closed_at }} ({{ $periodeSaatIni->closed_by }})</span>
                                @endif
                            @else
                                <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $periodeSaatIni->status_label }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 no-print print:hidden">
                        @if($periodeSaatIni->is_locked && $permintaanBukaBulanan)
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Permintaan Buka Kunci Diajukan</span>
                                            <span class="px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">{{ $permintaanBukaBulanan->id }}</span>
                                        </div>
                                        <p class="text-xs text-slate-600 mt-1">
                                            Menunggu persetujuan Akuntansi untuk periode {{ $periodeSaatIni->periode }}.
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <form method="POST" action="{{ route('sirkulasi.jatah.tutup.cancel-request-unlock') }}" onsubmit="return confirm('Batalkan permohonan buka kunci transaksi bulanan ini?')">
                                            @csrf
                                            <input type="hidden" name="mode" value="bulanan">
                                            <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                                            <button type="submit" class="h-9 px-3.5 text-xs font-semibold rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 transition cursor-pointer">
                                                Batalkan
                                            </button>
                                        </form>
                                        @if($canDirectUnlock)
                                            <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                                                @csrf
                                                <input type="hidden" name="mode" value="bulanan">
                                                <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                                                <input type="hidden" name="action" value="unlock">
                                                <button type="submit" onclick="return confirm('Buka kunci penjualan periode {{ $bulanPilihan }} sekarang?')" class="h-9 px-3.5 text-xs font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white shadow-xs transition cursor-pointer">
                                                    Setujui &amp; Buka
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-3 bg-white rounded-xl border border-slate-200 text-xs text-slate-700 grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <span class="text-xs font-semibold text-slate-500 block">Kategori &amp; Alasan</span>
                                        <div class="font-semibold text-slate-900 mt-0.5">{{ $permintaanBukaBulanan->kategori_alasan }}</div>
                                        <div class="text-slate-600 mt-0.5">"{{ $permintaanBukaBulanan->alasan }}"</div>
                                    </div>
                                    <div class="space-y-1">
                                        @if(!empty($permintaanBukaBulanan->estimasi_eksemplar) || !empty($permintaanBukaBulanan->estimasi_nominal))
                                            <div class="flex items-center justify-between py-0.5 border-b border-slate-100">
                                                <span class="text-slate-500">Estimasi Susulan</span>
                                                <span class="font-bold text-kp-blue-700 font-mono tabular-nums">
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
                                            <span class="text-slate-500">Pemohon</span>
                                            <span class="font-semibold text-slate-900">{{ $permintaanBukaBulanan->pemohon }}</span>
                                        </div>
                                        <div class="flex items-center justify-between py-0.5">
                                            <span class="text-slate-500">Waktu Pengajuan</span>
                                            <span class="font-mono text-slate-600">{{ $permintaanBukaBulanan->created_at }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs sm:text-sm text-slate-600">
                                <div>
                                    @if($periodeSaatIni->is_locked)
                                        <span class="font-semibold text-slate-900">Periode {{ $periodeSaatIni->periode }} terkunci.</span> Pembukaan kunci memerlukan persetujuan Akuntansi.
                                    @else
                                        <span class="font-semibold text-slate-900">Periode {{ $periodeSaatIni->periode }} terbuka.</span> Kunci buku bulanan setelah rekonsiliasi BAPR selesai.
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" onclick="window.print()" class="h-10 px-3.5 text-xs font-semibold rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 transition inline-flex items-center cursor-pointer">
                                        <span>Cetak Rekap</span>
                                    </button>

                                    @if($periodeSaatIni->is_locked)
                                        @if($canDirectUnlock)
                                            <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                                                @csrf
                                                <input type="hidden" name="mode" value="bulanan">
                                                <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                                                <input type="hidden" name="action" value="unlock">
                                                <button type="submit"
                                                        onclick="return confirm('Buka kembali kunci penjualan periode {{ $bulanPilihan }}?')"
                                                        class="h-10 px-4 text-xs font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white shadow-xs transition shrink-0 cursor-pointer">
                                                    Buka Kunci Bulanan
                                                </button>
                                            </form>
                                        @else
                                            <button type="button"
                                                    @click="openModalRequestBulanan = true"
                                                    class="h-10 px-4 text-xs font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white shadow-xs transition shrink-0 inline-flex items-center cursor-pointer">
                                                <span>Ajukan Buka Kunci</span>
                                            </button>
                                        @endif
                                    @else
                                        <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                                            @csrf
                                            <input type="hidden" name="mode" value="bulanan">
                                            <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                                            <input type="hidden" name="action" value="lock">
                                            <button type="submit"
                                                    onclick="return confirm('Tutup & kunci data penjualan periode {{ $bulanPilihan }}?')"
                                                    class="h-10 px-4 text-xs font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white shadow-xs transition shrink-0 cursor-pointer">
                                                Kunci Penjualan Bulanan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Riwayat Tutup Data Bulan Sebelumnya -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Riwayat Penutupan Buku Bulanan</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Histori penguncian penjualan bulanan Sirkulasi</p>
                        </div>
                        <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono w-fit">
                            Histori Bulanan
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                <tr>
                                    <th class="py-3 px-3.5 text-center w-12">No</th>
                                    <th class="py-3 px-3.5">Periode Bulan</th>
                                    <th class="py-3 px-3.5 text-right">Oplah Cetak</th>
                                    <th class="py-3 px-3.5 text-right">Retur (Eks)</th>
                                    <th class="py-3 px-3.5 text-right">Rasio Retur</th>
                                    <th class="py-3 px-3.5 text-right w-36">Omset Bersih</th>
                                    <th class="py-3 px-3.5 text-center">Status Akuntansi</th>
                                    <th class="py-3 px-3.5">Waktu Closing</th>
                                    <th class="py-3 px-3.5">Otorisator</th>
                                    <th class="print:hidden py-3 px-3.5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                                @foreach($riwayatClosingBulanan as $idx => $rc)
                                    <tr class="hover:bg-slate-50/80 transition {{ $rc->periode_key === $bulanPilihan ? 'bg-kp-blue-50/30' : '' }}">
                                        <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal w-12">
                                            {{ $idx + 1 }}
                                        </td>
                                        <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                            {{ $rc->periode }}
                                            <span class="block text-xs font-mono text-slate-500 font-normal">{{ $rc->periode_key }}</span>
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">{{ number_format($rc->total_oplah_cetak, 0, ',', '.') }}</td>
                                        <td class="py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums">{{ number_format($rc->total_retur_eks, 0, ',', '.') }}</td>
                                        <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">{{ $rc->rasio_retur }}%</td>
                                        <td class="py-3.5 px-3.5 text-right whitespace-nowrap">
                                            <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                                <span class="text-slate-400 font-normal select-none">Rp</span>
                                                <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($rc->total_omset_bersih, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-3.5 text-center">
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                {{ $rc->status_label }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3.5 font-mono text-slate-600">{{ $rc->closed_at }}</td>
                                        <td class="py-3.5 px-3.5 text-slate-700">{{ $rc->closed_by }}</td>
                                        <td class="print:hidden py-3.5 px-3.5 text-center">
                                            <a href="{{ route('sirkulasi.jatah.tutup', ['mode' => 'bulanan', 'bulan' => $rc->periode_key]) }}"
                                               class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 hover:bg-kp-blue-100 border border-kp-blue-200 rounded-md transition">
                                                Buka Periode
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
            <div class="no-print print:hidden bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Daftar Pengajuan Buka Kunci Transaksi</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Status persetujuan memo buka kunci oleh Divisi Akuntansi</p>
                    </div>
                    <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono w-fit">
                        {{ $semuaPermintaanBuka->count() }} Pengajuan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-3.5">No. Tiket</th>
                                <th class="py-3 px-3.5">Target Transaksi</th>
                                <th class="py-3 px-3.5">Kategori &amp; Alasan</th>
                                <th class="py-3 px-3.5 text-right">Estimasi Susulan</th>
                                <th class="py-3 px-3.5">Pemohon</th>
                                <th class="py-3 px-3.5 text-center">Status Akuntansi</th>
                                <th class="py-3 px-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @foreach($semuaPermintaanBuka as $idx => $req)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal w-12">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                        {{ $req->id }}
                                    </td>
                                    <td class="py-3.5 px-3.5">
                                        <div class="font-semibold text-slate-900">{{ $req->target_label }}</div>
                                        <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200 uppercase mt-0.5">
                                            {{ $req->mode }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3.5 max-w-xs truncate" title="{{ $req->alasan }}">
                                        <div class="font-semibold text-slate-800 text-xs">{{ $req->kategori_alasan }}</div>
                                        <div class="text-slate-500 text-xs truncate">"{{ $req->alasan }}"</div>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-xs tabular-nums">
                                        @if(!empty($req->estimasi_eksemplar))
                                            <span class="font-bold text-slate-900">{{ number_format($req->estimasi_eksemplar) }} eks</span><br>
                                        @endif
                                        @if(!empty($req->estimasi_nominal))
                                            <span class="text-slate-500">Rp {{ number_format($req->estimasi_nominal, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-slate-400">&mdash;</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3.5">
                                        <div class="font-semibold text-slate-900">{{ $req->pemohon }}</div>
                                        <div class="text-xs text-slate-500 font-mono">{{ $req->created_at }}</div>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        @if($req->status === 'APPROVED')
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                Disetujui &amp; Dibuka
                                            </span>
                                        @else
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                Menunggu Akuntansi
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        @if($req->status === 'PENDING')
                                            <div class="inline-flex items-center gap-2">
                                                @if($canDirectUnlock)
                                                    <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}" onsubmit="return confirm('Setujui memo {{ $req->id }} dan buka kunci transaksi ini?')">
                                                        @csrf
                                                        <input type="hidden" name="mode" value="{{ $req->mode }}">
                                                        <input type="hidden" name="{{ $req->mode === 'harian' ? 'tanggal' : 'periode' }}" value="{{ $req->target }}">
                                                        <input type="hidden" name="action" value="unlock">
                                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold transition cursor-pointer">
                                                            Setujui &amp; Buka
                                                        </button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('sirkulasi.jatah.tutup.cancel-request-unlock') }}" onsubmit="return confirm('Batalkan pengajuan {{ $req->id }}?')">
                                                    @csrf
                                                    <input type="hidden" name="mode" value="{{ $req->mode }}">
                                                    <input type="hidden" name="{{ $req->mode === 'harian' ? 'tanggal' : 'periode' }}" value="{{ $req->target }}">
                                                    <button type="submit" class="text-xs font-semibold text-slate-600 hover:text-slate-900 hover:underline cursor-pointer">
                                                        Batalkan
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400">Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- MODAL PENGAJUAN BUKA KUNCI HARIAN -->
        <div x-cloak
             x-show="openModalRequestHarian"
             @keydown.escape.window="openModalRequestHarian = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs no-print print:hidden"
             role="dialog"
             aria-modal="true"
             aria-labelledby="modal-title-harian">
            <div @click.outside="openModalRequestHarian = false"
                 class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full max-h-[92vh] overflow-y-auto">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900" id="modal-title-harian">
                            Ajukan Buka Kunci Harian
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Edisi {{ $harianSaatIni->tanggal }}
                        </p>
                    </div>
                    <button type="button" @click="openModalRequestHarian = false" class="text-slate-400 hover:text-slate-600 rounded-lg p-1 transition cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('sirkulasi.jatah.tutup.request-unlock') }}" class="p-5 space-y-4">
                    @csrf
                    <input type="hidden" name="mode" value="harian">
                    <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Target Edisi</label>
                        <div class="h-11 px-3.5 bg-slate-50 rounded-xl border border-slate-200 text-sm font-mono font-semibold text-slate-800 flex items-center justify-between">
                            <span>{{ $harianSaatIni->tanggal }}</span>
                            <span class="text-xs text-kp-blue-700 font-bold">{{ $tanggalPilihan }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kategori Pengajuan</label>
                        <select name="kategori_alasan" x-model="kategoriHarian" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            <option value="Penjualan Baru / Susulan Masuk">Penjualan Baru / Susulan Agen</option>
                            <option value="Koreksi Retur Fisik Agen Terlambat">Koreksi Retur Fisik Terlambat</option>
                            <option value="Penyesuaian Jatah Ekspedisi Luar Kota">Penyesuaian Jatah Ekspedisi / Armada</option>
                            <option value="Koreksi Salah Input Data Jatah">Koreksi Salah Input Data Jatah</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alasan Pembukaan Kunci</label>
                        <textarea name="alasan"
                                  rows="3"
                                  required
                                  minlength="5"
                                  placeholder="Nama agen, perkiraan eksemplar, atau keterangan koreksi..."
                                  class="w-full p-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Estimasi Susulan (Eks)</label>
                            <input type="number" name="estimasi_eksemplar" placeholder="0" min="0" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono text-right tabular-nums placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Estimasi Nilai (Rp)</label>
                            <input type="number" name="estimasi_nominal" placeholder="0" min="0" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono text-right tabular-nums placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200">
                        <button type="button" @click="openModalRequestHarian = false" class="h-10 px-4 text-xs sm:text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="h-10 px-4 text-xs sm:text-sm font-bold text-white bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 rounded-xl shadow-xs transition cursor-pointer">
                            Kirim ke Akuntansi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL PENGAJUAN BUKA KUNCI BULANAN -->
        <div x-cloak
             x-show="openModalRequestBulanan"
             @keydown.escape.window="openModalRequestBulanan = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs no-print print:hidden"
             role="dialog"
             aria-modal="true"
             aria-labelledby="modal-title-bulanan">
            <div @click.outside="openModalRequestBulanan = false"
                 class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full max-h-[92vh] overflow-y-auto">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900" id="modal-title-bulanan">
                            Ajukan Buka Kunci Bulanan
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Periode {{ $periodeSaatIni->periode }}
                        </p>
                    </div>
                    <button type="button" @click="openModalRequestBulanan = false" class="text-slate-400 hover:text-slate-600 rounded-lg p-1 transition cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('sirkulasi.jatah.tutup.request-unlock') }}" class="p-5 space-y-4">
                    @csrf
                    <input type="hidden" name="mode" value="bulanan">
                    <input type="hidden" name="periode" value="{{ $bulanPilihan }}">

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Target Periode</label>
                        <div class="h-11 px-3.5 bg-slate-50 rounded-xl border border-slate-200 text-sm font-mono font-semibold text-slate-800 flex items-center justify-between">
                            <span>{{ $periodeSaatIni->periode }}</span>
                            <span class="text-xs text-kp-blue-700 font-bold">{{ $bulanPilihan }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kategori Pengajuan</label>
                        <select name="kategori_alasan" x-model="kategoriBulanan" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            <option value="Penjualan Baru / Susulan Masuk">Penjualan Susulan Akhir Bulan</option>
                            <option value="Rekonsiliasi Retur Fisik Agen BAPR">Rekonsiliasi Retur Fisik BAPR</option>
                            <option value="Koreksi Selisih Oplah & Ekspedisi">Koreksi Selisih Oplah &amp; Ekspedisi</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alasan Pembukaan Kunci</label>
                        <textarea name="alasan"
                                  rows="3"
                                  required
                                  minlength="5"
                                  placeholder="Keterangan koreksi periode untuk verifikasi Akuntansi..."
                                  class="w-full p-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Estimasi Susulan (Eks)</label>
                            <input type="number" name="estimasi_eksemplar" placeholder="0" min="0" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono text-right tabular-nums placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Estimasi Nilai (Rp)</label>
                            <input type="number" name="estimasi_nominal" placeholder="0" min="0" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono text-right tabular-nums placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200">
                        <button type="button" @click="openModalRequestBulanan = false" class="h-10 px-4 text-xs sm:text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="h-10 px-4 text-xs sm:text-sm font-bold text-white bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 rounded-xl shadow-xs transition cursor-pointer">
                            Kirim ke Akuntansi
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
