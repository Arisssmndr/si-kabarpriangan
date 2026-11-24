<x-app-layout>
    <div class="space-y-6">

        <!-- 1. Header Halaman & Filter Periode Interaktif Konsolidasi 6 Divisi -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 relative z-20">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0" onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Pusat Kendali Administrator
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Konsolidasi operasional eksekutif real-time: Iklan, Sirkulasi, Faktur, Kasir, Inkaso, Anggaran, &amp; Keuangan.
                    </p>
                </div>
            </div>

            <!-- Filter Periode (Popover Standar Interaktif - Sama Seperti Iklan & Sirkulasi) -->
            <div class="relative shrink-0"
                 x-data="{ open: false }"
                 @click.outside="open = false"
                 @keydown.escape.window="open = false"
                 id="dashboardFilterContainer">
                <button type="button"
                        id="btnPeriodTrigger"
                        @click="open = !open"
                        class="h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold text-slate-700 shadow-xs flex items-center gap-2.5 transition cursor-pointer select-none">
                    <svg class="w-4 h-4 text-[#0E527B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="font-bold text-slate-900 text-sm">{{ $rangeTitle }}</span>
                    <span class="text-slate-300 font-normal hidden sm:inline">&bull;</span>
                    <span class="text-slate-600 font-normal hidden sm:inline text-sm">{{ $rangeSub }}</span>
                    <svg id="periodChevron"
                         :class="{ 'rotate-180': open }"
                         class="w-3.5 h-3.5 text-slate-400 transition-transform duration-150 shrink-0"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Popover Panel Filter -->
                <div id="periodMenuPanel"
                     x-cloak
                     x-show="open"
                     x-transition.duration.150ms
                     class="absolute right-0 top-full mt-1.5 w-80 bg-white rounded-xl shadow-xl border border-slate-200 z-50 p-3.5">

                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 mb-2.5">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pilih Periode</span>
                        @if($range !== 'month')
                            <a href="{{ route('admin.dashboard', ['range' => 'month']) }}" class="text-xs font-semibold text-[#0E527B] hover:underline">
                                Bulan Ini
                            </a>
                        @endif
                    </div>

                    <!-- Pilihan Cepat -->
                    <div class="grid grid-cols-2 gap-1.5 mb-3">
                        @php
                            $presets = [
                                'today'      => 'Hari Ini',
                                '7days'      => '7 Hari Terakhir',
                                'month'      => 'Bulan Ini',
                                '30days'     => '30 Hari Terakhir',
                                'last_month' => 'Bulan Lalu',
                                'year'       => 'Tahun Ini',
                            ];
                        @endphp
                        @foreach($presets as $key => $title)
                            @php $isActive = ($range === $key); @endphp
                            <a href="{{ route('admin.dashboard', ['range' => $key]) }}"
                               class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition {{ $isActive ? 'bg-sky-50 text-[#0E527B] border border-sky-200 font-bold' : 'text-slate-700 font-normal hover:bg-slate-50 border border-transparent hover:border-slate-200' }}">
                                <span>{{ $title }}</span>
                                @if($isActive)
                                    <svg class="w-3.5 h-3.5 text-[#0E527B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                @endif
                            </a>
                        @endforeach
                    </div>

                    <div class="border-t border-slate-100 my-2.5"></div>

                    <!-- Pilih Tanggal Sendiri -->
                    <div>
                        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">
                            Pilih Tanggal Sendiri
                        </div>
                        <form method="GET" action="{{ route('admin.dashboard') }}" class="space-y-2.5">
                            <input type="hidden" name="range" value="custom">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Dari Tanggal</label>
                                    <input type="date"
                                           name="dari"
                                           value="{{ $dari }}"
                                           required
                                           class="w-full h-10 px-2.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-[#0E527B] transition" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Sampai Tanggal</label>
                                    <input type="date"
                                           name="sampai"
                                           value="{{ $sampai }}"
                                           required
                                           class="w-full h-10 px-2.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-[#0E527B] transition" />
                                </div>
                            </div>
                            <button type="submit"
                                    class="w-full h-10 bg-[#0E527B] hover:bg-[#07334F] text-white rounded-xl text-sm font-bold flex items-center justify-center gap-1.5 transition cursor-pointer shadow-xs">
                                <span>Tampilkan</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- 2. Baris Utama: 4 Kartu Ringkasan Keuangan & Operasional (Standar Iklan 60-30-10) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Total Penjualan -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Penjualan</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalOmsetKonsolidasi, 0, ',', '.') }}
                </p>
                <div class="text-xs text-slate-500 mt-2 pt-2 border-t border-slate-100 space-y-0.5">
                    <div class="flex justify-between">
                        <span>Iklan:</span>
                        <span class="font-semibold text-slate-700 font-mono">Rp {{ number_format($totalOmsetIklan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Sirkulasi:</span>
                        <span class="font-semibold text-slate-700 font-mono">Rp {{ number_format($totalOmsetSirkulasi, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Kas Masuk -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kas Masuk</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalKasMasukKonsolidasi, 0, ',', '.') }}
                </p>
                <div class="text-xs text-slate-500 mt-2 pt-2 border-t border-slate-100 space-y-0.5">
                    <div class="flex justify-between">
                        <span>Loket Kasir:</span>
                        <span class="font-semibold text-slate-700 font-mono">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Koleksi Inkaso:</span>
                        <span class="font-semibold text-slate-700 font-mono">Rp {{ number_format($totalSetoranInkaso, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- 3. Total Piutang -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Piutang</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalPiutangKonsolidasi, 0, ',', '.') }}
                </p>
                <div class="text-xs text-slate-500 mt-2 pt-2 border-t border-slate-100 space-y-0.5">
                    <div class="flex justify-between">
                        <span>Piutang Iklan:</span>
                        <span class="font-semibold text-slate-700 font-mono">Rp {{ number_format($totalPiutangIklan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Piutang Agen:</span>
                        <span class="font-semibold text-slate-700 font-mono">Rp {{ number_format($totalPiutangSirkulasi, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- 4. Pengeluaran Kas -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pengeluaran Kas</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalRealisasiAnggaran, 0, ',', '.') }}
                </p>
                <div class="text-xs text-slate-500 mt-2 pt-2 border-t border-slate-100 space-y-0.5">
                    <div class="flex justify-between">
                        <span>Kasbon / DP:</span>
                        <span class="font-semibold text-slate-700 font-mono">Rp {{ number_format($kasbonGantung, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Saldo Kas:</span>
                        <span class="font-semibold text-kp-blue-700 font-mono">Rp {{ number_format($saldoKasTersedia, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Kartu Ringkasan Kinerja Divisi (Tata Letak Harmonis 4 Kolom x 2 Baris Sesuai Standar Nasional) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- 1. Divisi Iklan -->
            <a href="{{ route('iklan.dashboard') }}" 
               class="group bg-white border border-slate-200 hover:border-kp-blue-600 rounded-xl p-4 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Divisi Iklan</span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">{{ $totalTxIklan }} Faktur</span>
                    </div>
                    <div class="text-2xl font-bold text-kp-blue-700 font-mono tabular-nums">
                        Rp {{ number_format($totalOmsetIklan, 0, ',', '.') }}
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Cetak: Rp {{ number_format($totKoran ?? 0, 0, ',', '.') }} • Online &amp; TV: Rp {{ number_format(($totOnline ?? 0) + ($totTv ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-xs font-semibold text-kp-blue-700 flex items-center justify-between">
                    <span>Buka Divisi Iklan</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
            </a>

            <!-- 2. Divisi Sirkulasi -->
            <a href="{{ route('sirkulasi.dashboard') }}" 
               class="group bg-white border border-slate-200 hover:border-kp-blue-600 rounded-xl p-4 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Divisi Sirkulasi</span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">{{ $totalAgenAktif }} Agen</span>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 font-mono tabular-nums">
                        {{ number_format($oplahHariIni, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-500">Eksemplar</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Terkirim: {{ $persentaseTerdistribusi }}% • Retur: {{ $returKoranHariIni }} eks</p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-xs font-semibold text-kp-blue-700 flex items-center justify-between">
                    <span>Buka Divisi Sirkulasi</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
            </a>

            <!-- 3. Divisi Faktur -->
            <a href="{{ route('keuangan.faktur.iklan.index') }}" 
               class="group bg-white border border-slate-200 hover:border-kp-blue-600 rounded-xl p-4 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Divisi Faktur</span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">{{ $totalFakturTerbit }} Faktur</span>
                    </div>
                    <div class="text-2xl font-bold text-kp-blue-700 font-mono tabular-nums">
                        Rp {{ number_format($totalTagihanFaktur, 0, ',', '.') }}
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Antrean: {{ $fakturBelumDitugaskan }} tagihan belum ditugaskan</p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-xs font-semibold text-kp-blue-700 flex items-center justify-between">
                    <span>Buka Divisi Faktur</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
            </a>

            <!-- 4. Divisi Petugas Inkaso -->
            <a href="{{ route('keuangan.inkaso.index') }}" 
               class="group bg-white border border-slate-200 hover:border-kp-blue-600 rounded-xl p-4 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Divisi Inkaso</span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200 font-mono">{{ $fakturAktifLapangan }} Lapangan</span>
                    </div>
                    <div class="text-2xl font-bold text-kp-blue-700 font-mono tabular-nums">
                        Rp {{ number_format($totalSetoranInkaso, 0, ',', '.') }}
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Menunggu Kasir: Rp {{ number_format($setoranMenungguKasir, 0, ',', '.') }}</p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-xs font-semibold text-kp-blue-700 flex items-center justify-between">
                    <span>Atur Penugasan Inkaso</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
            </a>

            <!-- 5. Divisi Kasir -->
            <a href="{{ route('kasir.dashboard') }}" 
               class="group bg-white border border-slate-200 hover:border-kp-blue-600 rounded-xl p-4 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Divisi Kasir</span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">{{ $txKasirCount }} Transaksi</span>
                    </div>
                    <div class="text-2xl font-bold text-kp-blue-700 font-mono tabular-nums">
                        Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Status Loket: {{ $shiftKasirOpen > 0 ? 'Buka Normal' : 'Tutup' }} • Hari Ini: Rp {{ number_format($penerimaanHariIni, 0, ',', '.') }}</p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-xs font-semibold text-kp-blue-700 flex items-center justify-between">
                    <span>Buka Divisi Kasir</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
            </a>

            <!-- 6. Divisi Anggaran -->
            <a href="{{ route('keuangan.anggaran.index') }}" 
               class="group bg-white border border-slate-200 hover:border-kp-blue-600 rounded-xl p-4 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Divisi Anggaran</span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200 font-mono">Realisasi RAB</span>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 font-mono tabular-nums">
                        Rp {{ number_format($totalRealisasiAnggaran, 0, ',', '.') }}
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Kasbon / DP: Rp {{ number_format($kasbonGantung, 0, ',', '.') }}</p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-xs font-semibold text-kp-blue-700 flex items-center justify-between">
                    <span>Buka Divisi Anggaran</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
            </a>

            <!-- 7. Divisi Accounting & Keuangan -->
            <a href="{{ route('keuangan.dashboard') }}" 
               class="group bg-white border border-slate-200 hover:border-kp-blue-600 rounded-xl p-4 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Divisi Accounting</span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">Buku Besar</span>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 font-mono tabular-nums">
                        Rp {{ number_format($labaKotorBerjalan, 0, ',', '.') }}
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Status: {{ $statusBukuBesar }} • {{ $totalJurnalPending }} Jurnal Pending</p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-xs font-semibold text-kp-blue-700 flex items-center justify-between">
                    <span>Buka Keuangan &amp; Buku Besar</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
            </a>

            <!-- 8. SDM & Tata Kelola Keamanan -->
            <a href="{{ route('admin.users.index') }}" 
               class="group bg-white border border-slate-200 hover:border-kp-blue-600 rounded-xl p-4 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">SDM &amp; Keamanan</span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200 font-mono">{{ $onlineCount }} Online</span>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 font-mono tabular-nums">
                        {{ $totalUsers }} <span class="text-xs font-sans font-normal text-slate-500">Staf Terdaftar</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Audit Sesi &amp; Log Keamanan Terpantau Live</p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 text-xs font-semibold text-kp-blue-700 flex items-center justify-between">
                    <span>Kelola Pengguna &amp; Sesi</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
            </a>

        </div>

        <!-- 4. Ikhtisar Operasional Lintas Divisi (Harmonis 4 Kolom Sesuai Standar Nasional) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <!-- Panel 1: Operasional Iklan -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">Operasional Iklan</h2>
                        <a href="{{ route('iklan.dashboard') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">Detail &rarr;</a>
                    </div>
                    <div class="mt-4 space-y-2.5">
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Penjualan Iklan</span>
                            <span class="font-bold text-kp-blue-700 font-mono tabular-nums">Rp {{ number_format($totalOmsetIklan, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Koran Cetak</span>
                            <span class="font-semibold text-slate-700 font-mono tabular-nums">Rp {{ number_format($totKoran ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Online &amp; TV</span>
                            <span class="font-semibold text-slate-700 font-mono tabular-nums">Rp {{ number_format(($totOnline ?? 0) + ($totTv ?? 0), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Sisa Piutang</span>
                            <span class="font-semibold text-slate-700 font-mono tabular-nums">Rp {{ number_format($totalPiutangIklan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Status Operasional:</span>
                    <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2 py-0.5 rounded-md">Aktif Normal</span>
                </div>
            </div>

            <!-- Panel 2: Operasional Sirkulasi -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">Operasional Sirkulasi</h2>
                        <a href="{{ route('sirkulasi.dashboard') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">Detail &rarr;</a>
                    </div>
                    <div class="mt-4 space-y-2.5">
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Cetak Koran</span>
                            <span class="font-bold text-slate-900 font-mono tabular-nums">{{ number_format($oplahHariIni, 0, ',', '.') }} Eks</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Distribusi Agen</span>
                            <span class="font-bold text-kp-blue-700 font-mono tabular-nums">{{ $persentaseTerdistribusi }}%</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Retur Koran</span>
                            <span class="font-semibold text-slate-700 font-mono tabular-nums">{{ $returKoranHariIni }} Eks</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Jaringan Agen</span>
                            <span class="font-semibold text-slate-700 font-mono tabular-nums">{{ $totalAgenAktif }} Agen Aktif</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Jaringan Distribusi:</span>
                    <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2 py-0.5 rounded-md">Terkendali</span>
                </div>
            </div>

            <!-- Panel 3: Operasional Kasir & Inkaso -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">Kasir &amp; Inkaso</h2>
                        <a href="{{ route('kasir.dashboard') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">Detail &rarr;</a>
                    </div>
                    <div class="mt-4 space-y-2.5">
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Penerimaan Kasir</span>
                            <span class="font-bold text-kp-blue-700 font-mono tabular-nums">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Koleksi Inkaso</span>
                            <span class="font-bold text-slate-900 font-mono tabular-nums">Rp {{ number_format($totalSetoranInkaso, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Menunggu Kasir</span>
                            <span class="font-semibold text-slate-700 font-mono tabular-nums">Rp {{ number_format($setoranMenungguKasir, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Transaksi Kasir</span>
                            <span class="font-semibold text-slate-700 font-mono">{{ $txHariIni }} Struk Hari Ini</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Status Loket:</span>
                    <span class="text-xs font-semibold text-slate-700 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md">{{ $shiftKasirOpen > 0 ? 'Buka Normal' : 'Tutup' }}</span>
                </div>
            </div>

            <!-- Panel 4: Operasional Anggaran & Akuntansi -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">Anggaran &amp; Keuangan</h2>
                        <a href="{{ route('keuangan.dashboard') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">Detail &rarr;</a>
                    </div>
                    <div class="mt-4 space-y-2.5">
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Realisasi Beban</span>
                            <span class="font-bold text-slate-900 font-mono tabular-nums">Rp {{ number_format($estimasiPengeluaranBulanIni, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Kasbon / DP</span>
                            <span class="font-semibold text-slate-700 font-mono tabular-nums">Rp {{ number_format($kasbonGantung, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Estimasi Laba</span>
                            <span class="font-bold text-kp-blue-700 font-mono tabular-nums">Rp {{ number_format($labaKotorBerjalan, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Buku Besar</span>
                            <span class="font-semibold text-slate-700">{{ $statusBukuBesar }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Jurnal Pending:</span>
                    <span class="text-xs font-semibold text-slate-700 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md">{{ $totalJurnalPending }} Perlu Verifikasi</span>
                </div>
            </div>

        </div>

        <!-- ============================================================== -->
        <!-- WIDGET LIVE MONITORING STATUS SESI ADMIN & PENGGUNA REAL-TIME  -->
        <!-- ============================================================== -->
        <div x-data="adminLiveStatus({
            pollUrl: '{{ route('admin.users.online-status') }}',
            initialUsers: {{ \Illuminate\Support\Js::from($initialUsersData ?? []) }},
            initialOnlineCount: {{ $onlineCount ?? 0 }},
            initialOfflineCount: {{ $offlineCount ?? 0 }},
            initialTotalCount: {{ $totalUsers ?? 0 }},
            initialAdminOnlineCount: {{ $adminOnlineCount ?? 0 }},
            currentUserId: {{ auth()->id() ?? 0 }}
        })" class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            
            <!-- Header Widget -->
            <div class="p-5 border-b border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-slate-900 tracking-tight">Live Status Sesi Admin & Pengguna</h2>
                            <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">
                                Live Polling
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Status aktif langsung saat login dan nonaktif otomatis saat logout di seluruh akun admin & staf.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                    <!-- Live Sync Indicator (Standar Bersih Tanpa Titik Bulat) -->
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-300 text-xs font-bold text-slate-700">
                        <span>Auto-Sync 5 Detik</span>
                        <span class="font-mono text-xs text-slate-500" x-text="lastSynced ? '(' + lastSynced + ')' : ''"></span>
                    </div>

                    <a href="{{ route('admin.users.index') }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl border border-slate-300 transition">
                        <span>Kelola Pengguna</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            </div>

            <!-- Tab Filter Cepat & Ringkasan (Bebas Titik Bulat Kecil) -->
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/70 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <button type="button" 
                            @click="filter = 'all'" 
                            :class="filter === 'all' ? 'bg-white text-slate-900 shadow-xs border-slate-300 font-bold' : 'text-slate-600 hover:bg-slate-200/50 border-transparent font-medium'"
                            class="px-3 py-1.5 rounded-xl border text-xs transition cursor-pointer flex items-center gap-1.5">
                        <span>Semua Staf</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-800 text-[10px] font-extrabold font-mono" x-text="totalCount">{{ $totalUsers }}</span>
                    </button>

                    <button type="button" 
                            @click="filter = 'online'" 
                            :class="filter === 'online' ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-300 font-bold' : 'text-slate-600 hover:bg-slate-200/50 border-transparent font-medium'"
                            class="px-3 py-1.5 rounded-xl border text-xs transition cursor-pointer flex items-center gap-1.5">
                        <span>Aktif Sekarang</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-kp-blue-100 text-kp-blue-800 text-[10px] font-black font-mono" x-text="onlineCount">{{ $onlineCount }}</span>
                    </button>

                    <button type="button" 
                            @click="filter = 'offline'" 
                            :class="filter === 'offline' ? 'bg-white text-slate-900 shadow-xs border-slate-300 font-bold' : 'text-slate-600 hover:bg-slate-200/50 border-transparent font-medium'"
                            class="px-3 py-1.5 rounded-xl border text-xs transition cursor-pointer flex items-center gap-1.5">
                        <span>Nonaktif</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-extrabold font-mono" x-text="offlineCount">{{ $offlineCount }}</span>
                    </button>

                    <button type="button" 
                            @click="filter = 'admin'" 
                            :class="filter === 'admin' ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-300 font-bold' : 'text-slate-600 hover:bg-slate-200/50 border-transparent font-medium'"
                            class="px-3 py-1.5 rounded-xl border text-xs transition cursor-pointer flex items-center gap-1.5">
                        <span>Administrator</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-kp-blue-100 text-kp-blue-800 text-[10px] font-black font-mono" x-text="adminOnlineCount + ' Aktif'"></span>
                    </button>
                </div>

                <div class="text-xs text-slate-500 font-medium">
                    Status diperbarui live tanpa perlu refresh halaman
                </div>
            </div>

            <!-- Grid Kartu Live Status Staf & Administrator -->
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5">
                    <template x-for="u in filteredUsers" :key="u.id">
                        <div class="p-4 rounded-xl border transition-all duration-200 flex flex-col justify-between"
                             :class="u.is_online ? 'bg-white border-kp-blue-300 shadow-xs ring-1 ring-kp-blue-100' : 'bg-white border-slate-200 hover:border-slate-300'">
                            
                            <!-- Bagian Atas: Avatar, Nama, Role & Live Badge -->
                            <div>
                                <div class="flex items-start justify-between gap-2 mb-2.5">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <!-- Avatar Persegi Halus Profesional (Warna Konsisten Korporat) -->
                                        <div class="w-8 h-8 rounded-lg font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs bg-[#0E527B] text-white">
                                            <span x-text="(u.name || '').substring(0, 1).toUpperCase()"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-xs font-bold text-slate-900 truncate flex items-center gap-1">
                                                <span x-text="u.name"></span>
                                                <span x-show="u.id == currentUserId" class="px-1 py-0.2 rounded text-[9px] font-bold bg-slate-200 text-slate-700 shrink-0">Anda</span>
                                            </div>
                                            <span class="inline-block mt-0.5 px-2 py-0.2 rounded text-[10px] font-bold border"
                                                  :class="getRoleBadgeColor(u.role)"
                                                  x-text="u.role_label || u.role">
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Live Status Badge (Clean Text Tanpa Lingkaran Bulat) -->
                                    <div class="shrink-0">
                                        <template x-if="u.is_online">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                Aktif
                                            </span>
                                        </template>
                                        <template x-if="!u.is_online">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                                Nonaktif
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <!-- Email & Keterangan Status -->
                                <div class="space-y-1 pt-1 border-t border-slate-100 text-[11px]">
                                    <div class="text-slate-600 font-mono truncate text-[10px]" x-text="u.email"></div>
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="text-slate-500">Status Kehadiran:</span>
                                        <span class="font-bold" :class="u.is_online ? 'text-kp-blue-700 font-extrabold' : 'text-slate-600'" x-text="u.last_seen_text"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian Bawah: Jejak Login & Tombol Aksi Cepat -->
                            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[10px] gap-2">
                                <div class="text-slate-500 min-w-0 space-y-0.5">
                                    <span class="block truncate">Login: <span class="font-bold text-slate-800" x-text="u.last_login"></span></span>
                                    <span class="block font-mono text-[10px] text-slate-700 font-semibold">IP: <span x-text="u.last_login_ip || '-'"></span></span>
                                    <span x-show="u.last_login_device && u.last_login_device !== '-'" class="block text-[9px] text-slate-600 truncate" x-text="u.last_login_device"></span>
                                    <span x-show="u.last_login_browser && u.last_login_browser !== '-'" class="block text-[9px] text-kp-blue-700 font-bold truncate" x-text="u.last_login_browser"></span>
                                </div>

                                <!-- Putuskan Sesi jika sedang online dan bukan akun sendiri -->
                                <template x-if="u.is_online && u.id != currentUserId">
                                    <form method="POST" :action="'{{ url('admin/users') }}/' + u.id + '/force-logout'" 
                                          onsubmit="return confirmSubmit(this, event, {
                                              title: 'Putuskan Sesi Staf?',
                                              text: 'Putuskan sesi aktif staf ini sekarang? Pengguna akan langsung dikeluarkan ke halaman login.',
                                              theme: 'rose',
                                              icon: 'cancel',
                                              confirmText: 'Ya, Putuskan Sesi'
                                          });">
                                        @csrf
                                        <button type="submit" 
                                                class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold border border-slate-300 transition cursor-pointer">
                                            Putuskan
                                        </button>
                                    </form>
                                </template>
                            </div>

                        </div>
                    </template>
                </div>
            </div>

        </div>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Kolom Utama (8 Kolom): Aktivitas Transaksi Terbaru & Panduan Administrator -->
            <div class="lg:col-span-8 space-y-6">

                <!-- 1. Tabel Aktivitas Transaksi Terbaru Lintas Saluran -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Transaksi Terkini</h2>
                                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Transaksi terbaru lintas divisi iklan, online, dan TV</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono self-start sm:self-auto">
                            Realtime Terupdate
                        </span>
                    </div>

                    <div class="overflow-x-auto min-h-[200px]">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-[#0E527B] text-white border-b-2 border-[#FFB900]">
                                <tr class="text-xs font-bold uppercase tracking-wider text-white whitespace-nowrap">
                                    <th class="py-3 px-3.5 text-center w-12">No</th>
                                    <th class="py-3 px-3.5">Saluran</th>
                                    <th class="py-3 px-3.5">No. Faktur</th>
                                    <th class="py-3 px-3.5">Tanggal</th>
                                    <th class="py-3 px-3.5">Pemasang / Klien</th>
                                    <th class="py-3 px-3.5 text-right">Nilai Tagihan</th>
                                    <th class="py-3 px-3.5 text-center">Status</th>
                                    <th class="py-3 px-3.5 text-center w-12"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700 text-sm">
                                @forelse($recentStream as $key => $tx)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3.5 px-4 text-center text-slate-500 font-normal">
                                            {{ $key + 1 }}
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="inline-block px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 font-mono">
                                                {{ $tx->channel }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="font-mono font-bold text-kp-blue-700 text-base">
                                                <a href="{{ $tx->route_view }}" class="hover:underline">
                                                    {{ $tx->faktur }}
                                                </a>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap font-mono text-sm">
                                            {{ \Carbon\Carbon::parse($tx->tanggal)->format('d/m/Y') }}
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="font-bold text-slate-900 text-sm">{{ $tx->customer }}</div>
                                        </td>
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                            <div class="flex justify-between items-center gap-2 max-w-[140px] ml-auto font-mono text-sm">
                                                <span class="text-slate-400 font-normal select-none">Rp</span>
                                                <span class="font-bold text-slate-900 tabular-nums">{{ number_format($tx->total, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            @if($tx->is_lunas)
                                                <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                    Lunas
                                                </span>
                                            @else
                                                <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                    Piutang
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            <a href="{{ $tx->route_view }}" class="text-xs sm:text-sm text-kp-blue-600 hover:text-kp-blue-800 font-bold hover:underline">
                                                Detail &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-10 text-center text-slate-500 text-sm font-medium">
                                            Belum ada transaksi terkini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Info Administrator -->
                <div class="bg-gradient-to-r from-kp-blue-900 to-kp-blue-700 rounded-xl p-5 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-xs">
                    <div>
                        <h4 class="text-base font-bold">Akses Administrator</h4>
                        <p class="text-xs sm:text-sm text-kp-blue-100 mt-1 max-w-2xl leading-relaxed">
                            Anda dapat mengakses semua divisi: Iklan, Keuangan, Accounting, Sirkulasi, dan Kasir. Termasuk kelola data, validasi laporan, dan pengaturan sistem.
                        </p>
                    </div>
                    <a href="{{ route('kategori-media.index') }}" 
                       class="shrink-0 px-4 py-2 bg-white text-kp-blue-900 font-bold text-xs sm:text-sm rounded-xl shadow-xs hover:bg-kp-blue-50 transition text-center whitespace-nowrap">
                        Buka Master Data &rarr;
                    </a>
                </div>

            </div>

            <!-- Kolom Samping (4 Kolom): Widget Kalender Nasional & Agenda Momentum Pers -->
            <div class="lg:col-span-4 bg-white border border-slate-200 rounded-xl p-5 shadow-xs space-y-4">
                
                <!-- Header Kalender -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-base font-bold text-slate-900 leading-tight">Kalender Nasional</h2>
                            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Agenda edisi &amp; hari besar pers</p>
                        </div>
                    </div>
                    <button type="button" 
                            onclick="resetDashToday()" 
                            class="px-3 py-1.5 text-xs sm:text-sm font-bold text-kp-blue-700 bg-kp-blue-50 hover:bg-kp-blue-100 rounded-lg transition cursor-pointer">
                        Hari Ini
                    </button>
                </div>

                <!-- Navigasi Bulan & Tahun -->
                <div class="flex items-center justify-between px-1">
                    <button type="button" 
                            onclick="changeDashMonth(-1)" 
                            class="w-8 h-8 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer"
                            title="Bulan Sebelumnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-1.5">
                        <span id="dashCalMonthTitle" class="text-sm sm:text-base font-bold text-slate-900">
                            {{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM Y') }}
                        </span>
                        <span id="dashCalLoadingSpinner" class="hidden">
                            <svg class="animate-spin w-3.5 h-3.5 text-kp-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </span>
                    </div>

                    <button type="button" 
                            onclick="changeDashMonth(1)" 
                            class="w-8 h-8 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer"
                            title="Bulan Berikutnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- Header Hari (Min, Sen, Sel, Rab, Kam, Jum, Sab) -->
                <div class="grid grid-cols-7 gap-1 text-center">
                    <span class="text-xs sm:text-sm font-semibold text-slate-500 py-1">Min</span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-600 py-1">Sen</span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-600 py-1">Sel</span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-600 py-1">Rab</span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-600 py-1">Kam</span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-600 py-1">Jum</span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-600 py-1">Sab</span>
                </div>

                <!-- Grid Tanggal Kompak Standar Industri -->
                <div id="dashCalGrid" class="grid grid-cols-7 gap-1 text-center min-h-[190px]">
                    <!-- Dirender via JavaScript -->
                </div>

                <!-- Legenda Simpel Ringkas Persegi (Standar 60-30-10) -->
                <div class="flex items-center justify-between text-xs text-slate-600 font-normal border-t border-slate-100 pt-3 px-1">
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap shrink-0">
                        <span class="w-2.5 h-2.5 rounded-xs bg-kp-blue-600 inline-block shrink-0"></span>
                        <span class="text-slate-700">Terpilih</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap shrink-0">
                        <span class="w-2.5 h-2.5 rounded-xs bg-kp-blue-50 border border-kp-blue-300 inline-block shrink-0"></span>
                        <span class="text-slate-700">Hari Penting</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap shrink-0">
                        <span class="w-2.5 h-2.5 rounded-xs bg-slate-200 border border-slate-300 inline-block shrink-0"></span>
                        <span class="text-slate-700">Libur / Min</span>
                    </span>
                </div>

                <!-- Panel Informasi Tanggal Terpilih / Event -->
                <div id="dashCalInfoPanel" class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2 transition-all">
                    <!-- Render dinamis: Tanggal & Keterangan Libur / Peringatan / Normal -->
                </div>

            </div>

        </div>

    </div>

    @push('scripts')
    <script>
        // ==============================================================
        // WIDGET KALENDER NASIONAL & AGENDA PERS MODERN ADMINISTRATOR
        // ==============================================================
        const dashMonthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        const dashDayNames = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];

        const dashNow = new Date();
        let dashCurrentYear = dashNow.getFullYear();
        let dashCurrentMonth = dashNow.getMonth(); // 0-indexed
        let dashSelectedDate = `${dashCurrentYear}-${String(dashCurrentMonth + 1).padStart(2, '0')}-${String(dashNow.getDate()).padStart(2, '0')}`;

        // Inisialisasi cache dari data server-side awal ($kalenderAwal)
        const dashCalCache = {
            [`${dashCurrentYear}-${dashCurrentMonth + 1}`]: @json($kalenderAwal ?? ['libur' => [], 'peringatan' => []])
        };

        document.addEventListener('DOMContentLoaded', function () {
            initDashCalendar();
        });

        function initDashCalendar() {
            renderDashCalendar();
            updateDashInfoPanel(dashSelectedDate);
        }

        function changeDashMonth(delta) {
            dashCurrentMonth += delta;
            if (dashCurrentMonth > 11) {
                dashCurrentMonth = 0;
                dashCurrentYear++;
            } else if (dashCurrentMonth < 0) {
                dashCurrentMonth = 11;
                dashCurrentYear--;
            }
            fetchDashKalender(dashCurrentYear, dashCurrentMonth + 1, () => {
                renderDashCalendar();
            });
        }

        function resetDashToday() {
            const today = new Date();
            dashCurrentYear = today.getFullYear();
            dashCurrentMonth = today.getMonth();
            dashSelectedDate = `${dashCurrentYear}-${String(dashCurrentMonth + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
            
            fetchDashKalender(dashCurrentYear, dashCurrentMonth + 1, () => {
                renderDashCalendar();
                updateDashInfoPanel(dashSelectedDate);
            });
        }

        function fetchDashKalender(year, month, callback) {
            const key = `${year}-${month}`;
            if (dashCalCache[key]) {
                if (callback) callback();
                return;
            }

            const spinner = document.getElementById('dashCalLoadingSpinner');
            const grid = document.getElementById('dashCalGrid');
            if (spinner) spinner.classList.remove('hidden');
            if (grid) grid.classList.add('opacity-40', 'pointer-events-none');

            fetch(`/api/kalender-nasional?year=${year}&month=${month}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => {
                if (!res.ok) throw new Error('Network error');
                return res.json();
            })
            .then(data => {
                dashCalCache[key] = {
                    libur: data.libur || {},
                    peringatan: data.peringatan || {}
                };
            })
            .catch(err => {
                console.warn('Gagal memuat API Kalender Nasional:', err);
                dashCalCache[key] = { libur: {}, peringatan: {} };
            })
            .finally(() => {
                if (spinner) spinner.classList.add('hidden');
                if (grid) grid.classList.remove('opacity-40', 'pointer-events-none');
                if (callback) callback();
            });
        }

        function extractDashEventText(item) {
            if (!item) return null;
            if (typeof item === 'string') return item;
            if (typeof item === 'object') {
                return item.nama || item.keterangan || item.summary || '';
            }
            return null;
        }

        function renderDashCalendar() {
            const titleEl = document.getElementById('dashCalMonthTitle');
            if (titleEl) {
                titleEl.textContent = `${dashMonthNames[dashCurrentMonth]} ${dashCurrentYear}`;
            }

            const grid = document.getElementById('dashCalGrid');
            if (!grid) return;
            grid.innerHTML = '';

            const key = `${dashCurrentYear}-${dashCurrentMonth + 1}`;
            const monthData = dashCalCache[key] || { libur: {}, peringatan: {} };
            const liburMap = monthData.libur || {};
            const peringatanMap = monthData.peringatan || {};

            const firstDayOfWeek = new Date(dashCurrentYear, dashCurrentMonth, 1).getDay(); // 0 = Minggu
            const daysInMonth = new Date(dashCurrentYear, dashCurrentMonth + 1, 0).getDate();

            // Sel kosong sebelum tanggal 1
            for (let i = 0; i < firstDayOfWeek; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'h-8 pointer-events-none';
                grid.appendChild(emptyCell);
            }

            const today = new Date();
            const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${dashCurrentYear}-${String(dashCurrentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const dateObj = new Date(dashCurrentYear, dashCurrentMonth, day);
                const dayOfWeek = dateObj.getDay();

                const isToday = (dateStr === todayStr);
                const isSelected = (dateStr === dashSelectedDate);
                const isSunday = (dayOfWeek === 0);
                const liburDesc = extractDashEventText(liburMap[dateStr]);
                const peringatanDesc = extractDashEventText(peringatanMap[dateStr]);

                const cell = document.createElement('button');
                cell.type = 'button';
                cell.className = 'h-8 w-full rounded-md text-xs font-semibold flex items-center justify-center transition cursor-pointer relative';

                if (isSelected) {
                    cell.className += ' bg-kp-blue-600 text-white shadow-2xs font-bold';
                } else if (isToday) {
                    cell.className += ' bg-kp-blue-50 text-kp-blue-700 font-bold border border-kp-blue-300';
                } else if (liburDesc || isSunday) {
                    cell.className += ' bg-slate-100 text-slate-700 hover:bg-slate-200 font-medium';
                } else if (peringatanDesc) {
                    cell.className += ' bg-amber-50 text-amber-800 hover:bg-amber-100 font-medium';
                } else {
                    cell.className += ' text-slate-700 hover:bg-slate-100 font-medium';
                }

                cell.textContent = day;

                if (liburDesc || peringatanDesc) {
                    cell.title = [liburDesc ? `Libur: ${liburDesc}` : null, peringatanDesc ? `Peringatan: ${peringatanDesc}` : null].filter(Boolean).join(' • ');
                }

                cell.onclick = () => {
                    dashSelectedDate = dateStr;
                    renderDashCalendar();
                    updateDashInfoPanel(dateStr);
                };

                grid.appendChild(cell);
            }
        }

        function updateDashInfoPanel(dateStr) {
            const panel = document.getElementById('dashCalInfoPanel');
            if (!panel) return;

            const parts = dateStr.split('-');
            if (parts.length !== 3) return;
            const y = parseInt(parts[0]);
            const m = parseInt(parts[1]) - 1;
            const d = parseInt(parts[2]);
            const dateObj = new Date(y, m, d);

            const dayName = dashDayNames[dateObj.getDay()] || '';
            const formatted = `${dayName}, ${d} ${dashMonthNames[m]} ${y}`;

            const key = `${y}-${m + 1}`;
            const monthData = dashCalCache[key] || { libur: {}, peringatan: {} };
            const liburDesc = extractDashEventText(monthData.libur ? monthData.libur[dateStr] : null);
            const peringatanDesc = extractDashEventText(monthData.peringatan ? monthData.peringatan[dateStr] : null);
            const isSunday = (dateObj.getDay() === 0);

            let eventInfo = '';
            if (liburDesc) {
                eventInfo += `<div class="text-slate-700 font-semibold mt-1">Libur Nasional: ${liburDesc}</div>`;
            }
            if (peringatanDesc) {
                eventInfo += `<div class="text-amber-700 font-medium mt-1">Agenda Pers: ${peringatanDesc}</div>`;
            }

            panel.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-slate-900">${formatted}</span>
                    <span class="text-xs font-mono text-slate-500">${dateStr}</span>
                </div>
                ${eventInfo || '<div class="text-xs text-slate-500 mt-1">Hari kerja operasional pers normal.</div>'}
            `;
        }

        // ==============================================================
        // CONTROLLER LIVE STATUS KEHADIRAN & SESI ADMIN / PENGGUNA
        // ==============================================================
        function adminLiveStatus(config) {
            return {
                filter: 'all',
                currentUserId: config.currentUserId || 0,
                usersMap: config.initialUsers || {},
                onlineCount: parseInt(config.initialOnlineCount) || 0,
                offlineCount: parseInt(config.initialOfflineCount) || 0,
                totalCount: parseInt(config.initialTotalCount) || 0,
                adminOnlineCount: parseInt(config.initialAdminOnlineCount) || 0,
                lastSynced: '',
                pollTimer: null,

                init() {
                    this.fetchStatus();
                    // Live polling status setiap 5 detik
                    this.pollTimer = setInterval(() => {
                        this.fetchStatus();
                    }, 5000);
                },

                fetchStatus() {
                    if (!config.pollUrl) return;
                    fetch(config.pollUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.users) {
                            this.usersMap = data.users;
                            if (typeof data.online_count !== 'undefined') this.onlineCount = data.online_count;
                            if (typeof data.offline_count !== 'undefined') this.offlineCount = data.offline_count;
                            if (typeof data.total_count !== 'undefined') this.totalCount = data.total_count;
                            if (typeof data.admin_online_count !== 'undefined') this.adminOnlineCount = data.admin_online_count;
                            if (data.timestamp) this.lastSynced = data.timestamp;
                        }
                    })
                    .catch(() => {});
                },

                get userList() {
                    return Object.values(this.usersMap);
                },

                get filteredUsers() {
                    const list = this.userList;
                    if (this.filter === 'online') {
                        return list.filter(u => u.is_online);
                    }
                    if (this.filter === 'offline') {
                        return list.filter(u => !u.is_online);
                    }
                    if (this.filter === 'admin') {
                        return list.filter(u => ['administrator', 'admin'].includes((u.role || '').toLowerCase()));
                    }
                    return list;
                },

                getRoleAvatarColor(role) {
                    return 'bg-[#0E527B] text-white';
                },

                getRoleBadgeColor(role) {
                    const r = (role || '').toLowerCase();
                    if (['administrator', 'admin'].includes(r)) return 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200 font-bold';
                    return 'bg-slate-100 text-slate-700 border-slate-200 font-semibold';
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
