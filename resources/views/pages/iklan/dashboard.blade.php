<x-app-layout>
    @php
        $rasioBayar = $totalOmset > 0 ? round(($totalBayar / $totalOmset) * 100) : 0;
        $ppnDash = rtrim(rtrim(number_format((float) ($ppnPersen ?? 11), 1, ',', '.'), '0'), ',');
        $maxSalesOmzet = collect($rekapSalesDash ?? [])->max('omzet') ?: 1;
    @endphp

    <div class="space-y-5">

        <!-- Header Halaman Standar (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 relative z-20">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Dashboard Iklan
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Ringkasan penjualan, pembayaran, master data, dan transaksi Iklan Koran, Online, serta TV.
                    </p>
                </div>
            </div>

            <!-- Filter Periode (Popover Standar) -->
            <div class="relative shrink-0"
                 x-data="{ open: false }"
                 @click.outside="open = false"
                 @keydown.escape.window="open = false"
                 id="dashboardFilterContainer">
                <button type="button"
                        id="btnPeriodTrigger"
                        @click="open = !open"
                        class="h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold text-slate-700 shadow-xs flex items-center gap-2.5 transition cursor-pointer select-none">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                <!-- Popover Panel -->
                <div id="periodMenuPanel"
                     x-cloak
                     x-show="open"
                     x-transition.duration.150ms
                     class="absolute right-0 top-full mt-1.5 w-80 bg-white rounded-xl shadow-xl border border-slate-200 z-50 p-3.5">

                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 mb-2.5">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pilih Periode</span>
                        @if($range !== 'month')
                            <a href="{{ route('iklan.dashboard', ['range' => 'month']) }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
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
                            <a href="{{ route('iklan.dashboard', ['range' => $key]) }}"
                               class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition {{ $isActive ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-semibold' : 'text-slate-700 font-normal hover:bg-slate-50 border border-transparent hover:border-slate-200' }}">
                                <span>{{ $title }}</span>
                                @if($isActive)
                                    <svg class="w-3.5 h-3.5 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <form method="GET" action="{{ route('iklan.dashboard') }}" class="space-y-2.5">
                            <input type="hidden" name="range" value="custom">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Dari Tanggal</label>
                                    <input type="date"
                                           name="dari"
                                           value="{{ $dari }}"
                                           required
                                           class="w-full h-10 px-2.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 transition" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Sampai Tanggal</label>
                                    <input type="date"
                                           name="sampai"
                                           value="{{ $sampai }}"
                                           required
                                           class="w-full h-10 px-2.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 transition" />
                                </div>
                            </div>
                            <button type="submit"
                                    class="w-full h-10 bg-kp-blue-600 hover:bg-kp-blue-700 text-white rounded-xl text-sm font-bold flex items-center justify-center gap-1.5 transition cursor-pointer shadow-xs">
                                <span>Tampilkan</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Baris 1: 4 Kartu Ringkasan Penjualan & Pembayaran -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Total Penjualan -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Penjualan</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalOmset, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    Hari ini: <span class="font-semibold text-slate-700 font-mono">Rp {{ number_format($penjualanHariIni ?? 0, 0, ',', '.') }}</span>
                </span>
            </div>

            <!-- 2. Sudah Dibayar -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sudah Dibayar</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalBayar, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    Masuk: <span class="font-semibold text-kp-blue-700 font-mono">{{ $rasioBayar }}%</span> dari total penjualan
                </span>
            </div>

            <!-- 3. Sisa Piutang -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sisa Piutang</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    <span class="font-semibold text-slate-700 font-mono">{{ number_format($totalBelumLunas ?? 0) }}</span> faktur belum lunas
                </span>
            </div>

            <!-- 4. Jumlah Faktur -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jumlah Transaksi</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalTransaksi) }} <span class="text-xs font-sans font-normal text-slate-500">Faktur</span>
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    Hari ini: <span class="font-semibold text-slate-700 font-mono">{{ $transaksiHariIni ?? 0 }}</span> faktur &bull; {{ $totalJenisIklan }} jenis iklan
                </span>
            </div>
        </div>

        <!-- Baris 2: 4 KOTAK BERJEJER RANGKUMAN MASTER DATA & TRANSAKSI -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Data Pemasang -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Data Pemasang</span>
                    <a href="{{ route('pelanggan.index') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
                        Lihat &rarr;
                    </a>
                </div>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalPemasang ?? 0) }} <span class="text-xs font-sans font-normal text-slate-500">Pemasang</span>
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    Pelanggan aktif tercatat di sistem
                </span>
            </div>

            <!-- 2. Data Sales / AE -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Data Sales</span>
                    <a href="{{ route('sales-ae.index') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
                        Lihat &rarr;
                    </a>
                </div>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalSales ?? 0) }} <span class="text-xs font-sans font-normal text-slate-500">Sales / AE</span>
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    Insentif: <span class="font-semibold text-slate-700 font-mono">Rp {{ number_format($totalInsentifSales ?? 0, 0, ',', '.') }}</span>
                </span>
            </div>

            <!-- 3. Jenis Iklan -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jenis Iklan</span>
                    <a href="{{ route('jenis-iklan.index') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
                        Lihat &rarr;
                    </a>
                </div>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalJenisIklan ?? 0) }} <span class="text-xs font-sans font-normal text-slate-500">Paket</span>
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    Tarif aktif Koran, Online &amp; TV
                </span>
            </div>

            <!-- 4. Data Transaksi / Setoran Kasir -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Data Transaksi</span>
                    <a href="{{ route('transaksikoran.index') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
                        Lihat &rarr;
                    </a>
                </div>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($qrSummary['menunggu_nominal'] ?? 0, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    {{ $qrSummary['menunggu_count'] ?? 0 }} Siap Scan Kasir &bull; Hari ini: <span class="font-semibold text-slate-700 font-mono">{{ $transaksiHariIni ?? 0 }}</span>
                </span>
            </div>
        </div>

        <!-- Baris 3: Penjualan per Media (Iklan Koran, Iklan Online, Iklan TV) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($channels as $cKey => $ch)
                <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col justify-between">
                    <div>
                        <!-- Header Kartu -->
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <h3 class="text-sm font-bold text-slate-900 truncate">{{ $ch['nama'] }}</h3>
                                <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200 font-mono shrink-0">
                                    {{ $ch['count'] }} Faktur
                                </span>
                            </div>
                            <div class="flex items-center gap-2.5 shrink-0">
                                <a href="{{ route($ch['route_new']) }}"
                                   class="text-xs font-semibold text-slate-600 hover:text-kp-blue-700 transition">
                                    + Input
                                </a>
                                <a href="{{ route($ch['route_tx']) }}"
                                   class="text-xs font-semibold text-kp-blue-700 hover:underline">
                                    Data &rarr;
                                </a>
                            </div>
                        </div>

                        <!-- Total Penjualan per Media -->
                        <div class="py-3.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Penjualan</span>
                                <span class="text-xs font-mono font-bold text-kp-blue-700">{{ $ch['percent'] }}% porsi</span>
                            </div>
                            <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums tracking-tight mt-1 whitespace-nowrap">
                                Rp {{ number_format($ch['omzet'], 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        <!-- Sudah Dibayar & Sisa Piutang -->
                        <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-xs font-normal text-slate-500 block">Sudah Dibayar</span>
                                <span class="text-sm font-semibold text-slate-900 font-mono tabular-nums block mt-0.5 whitespace-nowrap">
                                    Rp {{ number_format($ch['bayar'], 0, ',', '.') }}
                                </span>
                            </div>
                            <div>
                                <span class="text-xs font-normal text-slate-500 block">Sisa Piutang</span>
                                <span class="text-sm font-normal {{ $ch['piutang'] > 0 ? 'text-slate-700' : 'text-slate-400' }} font-mono tabular-nums block mt-0.5 whitespace-nowrap">
                                    Rp {{ number_format($ch['piutang'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Ringkasan Operasional Media -->
                        <div class="pt-2 border-t border-slate-100 text-xs text-slate-500 flex items-center justify-between">
                            <span>{{ $ch['info_ops'] ?? '' }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Baris 4: Grafik Tren Penjualan 7 Hari & KALENDER TERBIT (BERDIRI SENDIRI SEPERTI SEBELUMNYA) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">

            <!-- Kolom Utama (8 Kolom): Grafik Penjualan 7 Hari Terakhir -->
            <div class="lg:col-span-8 bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3.5 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Grafik Penjualan 7 Hari Terakhir</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Nilai penjualan harian dari Iklan Koran, Online, dan TV</p>
                    </div>
                    <div class="flex items-center flex-wrap gap-4 text-xs font-medium text-slate-700">
                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap shrink-0">
                            <span class="w-2.5 h-2.5 rounded-xs bg-kp-blue-700 shrink-0"></span> Iklan Koran
                        </span>
                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap shrink-0">
                            <span class="w-2.5 h-2.5 rounded-xs bg-kp-blue-400 shrink-0"></span> Iklan Online
                        </span>
                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap shrink-0">
                            <span class="w-2.5 h-2.5 rounded-xs bg-slate-400 shrink-0"></span> Iklan TV
                        </span>
                    </div>
                </div>

                <div class="h-80 w-full relative">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>

            <!-- Kolom Samping (4 Kolom): Kalender Terbit & Hari Penting (BERDIRI SENDIRI) -->
            <div class="lg:col-span-4 bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-3.5">
                <!-- Header Kalender -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Kalender Terbit</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Jadwal edisi &amp; hari besar nasional</p>
                    </div>
                    <button type="button"
                            onclick="resetDashToday()"
                            class="px-2.5 py-1 text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 hover:bg-kp-blue-100 border border-kp-blue-200 rounded-lg transition cursor-pointer">
                        Hari Ini
                    </button>
                </div>

                <!-- Navigasi Bulan & Tahun -->
                <div class="flex items-center justify-between px-1">
                    <button type="button"
                            onclick="changeDashMonth(-1)"
                            class="w-7 h-7 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer"
                            title="Bulan Sebelumnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-1.5">
                        <span id="dashCalMonthTitle" class="text-sm font-bold text-slate-900">
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
                            class="w-7 h-7 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer"
                            title="Bulan Berikutnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- Header Hari -->
                <div class="grid grid-cols-7 gap-1 text-center">
                    <span class="text-xs font-semibold text-slate-500 py-1">Min</span>
                    <span class="text-xs font-semibold text-slate-600 py-1">Sen</span>
                    <span class="text-xs font-semibold text-slate-600 py-1">Sel</span>
                    <span class="text-xs font-semibold text-slate-600 py-1">Rab</span>
                    <span class="text-xs font-semibold text-slate-600 py-1">Kam</span>
                    <span class="text-xs font-semibold text-slate-600 py-1">Jum</span>
                    <span class="text-xs font-semibold text-slate-600 py-1">Sab</span>
                </div>

                <!-- Grid Tanggal -->
                <div id="dashCalGrid" class="grid grid-cols-7 gap-1 text-center min-h-[190px]"></div>

                <!-- Keterangan Warna -->
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

                <!-- Info Tanggal Terpilih -->
                <div id="dashCalInfoPanel" class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5 transition-all text-xs"></div>
            </div>

        </div>

        <!-- Baris 5: 2 KARTU VISUAL RINGKAS (Performa Sales Leaderboard & Komposisi Donut Chart - 6 Kolom & 6 Kolom) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch">

            <!-- Kiri (6 Kolom): Performa Penjualan Sales (Visual Horizontal Bar Chart) -->
            <div class="lg:col-span-6 bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4 flex flex-col justify-between">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 gap-2">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Performa Sales / AE</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Peringkat penjualan &amp; insentif sales periode {{ strtolower($rangeTitle) }}</p>
                    </div>
                    <a href="{{ route('laporan.rekap-sales') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline shrink-0">
                        Laporan Sales &rarr;
                    </a>
                </div>

                <div class="space-y-3.5">
                    @forelse($rekapSalesDash ?? [] as $sp)
                        @php
                            $spPct = $maxSalesOmzet > 0 ? round(($sp['omzet'] / $maxSalesOmzet) * 100) : 0;
                        @endphp
                        <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-200/70 hover:bg-slate-50 transition space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-6 h-6 rounded-lg bg-kp-blue-100 text-kp-blue-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($sp['nama'], 0, 1)) }}
                                    </span>
                                    <span class="font-bold text-slate-900 truncate">{{ $sp['nama'] }}</span>
                                    <span class="text-slate-400 font-mono text-[11px]">({{ $sp['faktur'] }} faktur)</span>
                                </div>
                                <span class="font-mono font-bold text-kp-blue-700 shrink-0">
                                    Rp {{ number_format($sp['omzet'], 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- Progress Bar Visual -->
                            <div class="w-full bg-slate-200/70 rounded-full h-2 overflow-hidden">
                                <div class="bg-kp-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ max(4, $spPct) }}%"></div>
                            </div>

                            <div class="flex items-center justify-between text-[11px] text-slate-500 pt-0.5">
                                <span>Terbayar: <strong class="text-slate-700 font-mono">Rp {{ number_format($sp['bayar'], 0, ',', '.') }}</strong></span>
                                <span>Insentif: <strong class="text-slate-700 font-mono">Rp {{ number_format($sp['insentif'], 0, ',', '.') }}</strong></span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-500">
                            Belum ada rekap penjualan sales pada periode ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Kanan (6 Kolom): Komposisi Porsi Penjualan Media (Donut Chart) -->
            <div class="lg:col-span-6 bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4 flex flex-col justify-between">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Komposisi Porsi Media</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Distribusi penjualan periode {{ strtolower($rangeTitle) }}</p>
                    </div>
                    <span class="text-xs font-mono font-bold text-kp-blue-700">
                        100% Total
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                    <div class="sm:col-span-6 h-48 relative flex items-center justify-center">
                        <canvas id="channelShareChart"></canvas>
                    </div>

                    <div class="sm:col-span-6 space-y-2.5">
                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-800 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-kp-blue-700 inline-block"></span> Koran
                                </span>
                                <span class="font-bold text-kp-blue-700 font-mono">{{ $channels['koran']['percent'] ?? 0 }}%</span>
                            </div>
                            <div class="text-xs font-mono text-slate-600 mt-0.5">
                                Rp {{ number_format($channels['koran']['omzet'] ?? 0, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-800 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-sky-400 inline-block"></span> Online
                                </span>
                                <span class="font-bold text-sky-600 font-mono">{{ $channels['online']['percent'] ?? 0 }}%</span>
                            </div>
                            <div class="text-xs font-mono text-slate-600 mt-0.5">
                                Rp {{ number_format($channels['online']['omzet'] ?? 0, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-800 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-slate-400 inline-block"></span> Priangan TV
                                </span>
                                <span class="font-bold text-slate-600 font-mono">{{ $channels['tv']['percent'] ?? 0 }}%</span>
                            </div>
                            <div class="text-xs font-mono text-slate-600 mt-0.5">
                                Rp {{ number_format($channels['tv']['omzet'] ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-[11px] text-slate-400 pt-2 border-t border-slate-100 flex items-center justify-between">
                    <span>Total Omset Penjualan:</span>
                    <span class="font-bold font-mono text-slate-700">Rp {{ number_format($totalOmset, 0, ',', '.') }}</span>
                </div>
            </div>

        </div>

        <!-- Baris 6: 1 TABEL RINGKAS TRANSAKSI TERBARU (Satu-satunya Tabel di Dashboard) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 py-3.5 border-b border-slate-200 flex items-center justify-between gap-2 bg-white">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Transaksi Iklan Terbaru</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Faktur terbaru dari Iklan Koran, Online, dan TV</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">
                        {{ $recentStream->count() }} Faktur
                    </span>
                    <a href="{{ route('transaksikoran.index') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
                        Lihat Semua Data &rarr;
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5">No. Faktur</th>
                            <th class="py-3 px-3">Tanggal &amp; Media</th>
                            <th class="py-3 px-3.5">Pemasang</th>
                            <th class="py-3 px-3.5 text-right w-28">Tagihan</th>
                            <th class="py-3 px-3.5 text-right w-28">Dibayar</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center">Setoran QR</th>
                            <th class="py-3 px-3 text-center w-12">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($recentStream->take(6) as $tx)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-3.5 font-mono font-bold text-kp-blue-700 text-xs">
                                    <a href="{{ $tx->route_show }}" class="hover:underline">
                                        {{ $tx->faktur }}
                                    </a>
                                </td>
                                <td class="py-3 px-3 text-xs">
                                    <span class="font-mono text-slate-600">{{ \Carbon\Carbon::parse($tx->tanggal)->format('d/m/Y') }}</span>
                                    <span class="ml-1 inline-block px-1.5 py-0.2 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ $tx->channel }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-bold text-slate-900 text-xs truncate max-w-[190px]">{{ $tx->customer }}</div>
                                    <div class="text-[11px] text-slate-500 truncate max-w-[190px]">{{ $tx->paket }}</div>
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900 text-xs tabular-nums">
                                    Rp {{ number_format($tx->total, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono text-xs tabular-nums text-slate-700">
                                    Rp {{ number_format($tx->bayar ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @if($tx->is_lunas)
                                        <span class="inline-block px-2 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Lunas
                                        </span>
                                    @elseif(($tx->bayar ?? 0) > 0)
                                        <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            DP / Cicil
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                            Piutang
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @if(($tx->qr_status ?? null) === 'DISETOR')
                                        <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Disetor
                                        </span>
                                    @elseif(($tx->qr_status ?? null) === 'AKTIF')
                                        <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            Siap Scan
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <a href="{{ $tx->route_show }}"
                                       class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-slate-500 hover:text-kp-blue-700 hover:bg-slate-100 transition"
                                       title="Buka Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-xs text-slate-500">
                                    Belum ada data transaksi tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ==============================================================
            // 1. GRAFIK TREN PENJUALAN 7 HARI TERAKHIR (BAR CHART)
            // ==============================================================
            const ctxTrend = document.getElementById('revenueTrendChart');
            if (ctxTrend) {
                const labels = @json($dailyLabels ?? []);
                const dataKoran = @json($dailyKoran ?? []);
                const dataOnline = @json($dailyOnline ?? []);
                const dataTv = @json($dailyTv ?? []);

                new Chart(ctxTrend, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Iklan Koran',
                                data: dataKoran,
                                backgroundColor: '#005792',
                                borderRadius: 4,
                                barPercentage: 0.6,
                            },
                            {
                                label: 'Iklan Online',
                                data: dataOnline,
                                backgroundColor: '#38bdf8',
                                borderRadius: 4,
                                barPercentage: 0.6,
                            },
                            {
                                label: 'Iklan TV',
                                data: dataTv,
                                backgroundColor: '#94a3b8',
                                borderRadius: 4,
                                barPercentage: 0.6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 11 },
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        const val = context.parsed.y || 0;
                                        return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(val);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11, weight: '500' }, color: '#334155' }
                            },
                            y: {
                                border: { dash: [4, 4] },
                                grid: { color: '#f1f5f9' },
                                ticks: {
                                    font: { size: 11, weight: '500' },
                                    color: '#475569',
                                    callback: function(value) {
                                        if (value >= 1000000) {
                                            return (value / 1000000) + ' Jt';
                                        }
                                        return value;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // ==============================================================
            // 2. GRAFIK KOMPOSISI PORSI MEDIA (DOUGHNUT CHART)
            // ==============================================================
            const ctxShare = document.getElementById('channelShareChart');
            if (ctxShare) {
                const omzKoran = {{ (int) ($channels['koran']['omzet'] ?? 0) }};
                const omzOnline = {{ (int) ($channels['online']['omzet'] ?? 0) }};
                const omzTv = {{ (int) ($channels['tv']['omzet'] ?? 0) }};

                new Chart(ctxShare, {
                    type: 'doughnut',
                    data: {
                        labels: ['Iklan Koran', 'Iklan Online', 'Iklan TV'],
                        datasets: [{
                            data: [omzKoran, omzOnline, omzTv],
                            backgroundColor: ['#005792', '#38bdf8', '#94a3b8'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 11 },
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(c) {
                                        const val = c.raw || 0;
                                        return c.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(val);
                                    }
                                }
                            }
                        }
                    }
                });
            }

            initDashCalendar();
        });

        // ==============================================================
        // 3. KALENDER TERBIT & HARI PENTING (BERDIRI SENDIRI)
        // ==============================================================
        const dashMonthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        const dashDayNames = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];

        const dashNow = new Date();
        let dashCurrentYear = dashNow.getFullYear();
        let dashCurrentMonth = dashNow.getMonth();
        let dashSelectedDate = `${dashCurrentYear}-${String(dashCurrentMonth + 1).padStart(2, '0')}-${String(dashNow.getDate()).padStart(2, '0')}`;

        const dashCalCache = {
            [`${dashCurrentYear}-${dashCurrentMonth + 1}`]: @json($kalenderAwal ?? ['libur' => [], 'peringatan' => []])
        };

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
                dashCalCache[key] = { libur: {}, peringatan: {} };
            })
            .finally(() => {
                if (spinner) spinner.classList.add('hidden');
                if (grid) grid.classList.remove('opacity-40', 'pointer-events-none');
                if (callback) callback();
            });
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

            const firstDayOfWeek = new Date(dashCurrentYear, dashCurrentMonth, 1).getDay();
            const daysInMonth = new Date(dashCurrentYear, dashCurrentMonth + 1, 0).getDate();

            for (let i = 0; i < firstDayOfWeek; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'h-9 pointer-events-none';
                grid.appendChild(emptyCell);
            }

            const today = new Date();
            const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

            for (let d = 1; d <= daysInMonth; d++) {
                const dateStr = `${dashCurrentYear}-${String(dashCurrentMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                const cellDayOfWeek = new Date(dashCurrentYear, dashCurrentMonth, d).getDay();
                const isSunday = (cellDayOfWeek === 0);
                const isHoliday = Boolean(liburMap[dateStr]);
                const isMemorial = Boolean(peringatanMap[dateStr]);
                const isToday = (dateStr === todayStr);
                const isSelected = (dateStr === dashSelectedDate);

                const cell = document.createElement('button');
                cell.type = 'button';
                cell.className = 'h-8 w-full rounded-md text-xs font-semibold flex items-center justify-center transition cursor-pointer relative';

                if (isSelected) {
                    cell.className += ' bg-kp-blue-600 text-white shadow-2xs font-bold';
                } else if (isToday) {
                    cell.className += ' bg-kp-blue-50 text-kp-blue-700 font-bold border border-kp-blue-300';
                } else if (isHoliday || isSunday) {
                    cell.className += ' bg-rose-50 text-rose-700 hover:bg-rose-100 font-medium';
                } else if (isMemorial) {
                    cell.className += ' bg-amber-50 text-amber-800 hover:bg-amber-100 font-medium';
                } else {
                    cell.className += ' text-slate-700 hover:bg-slate-100 font-medium';
                }

                cell.textContent = d;
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

            const key = `${dashCurrentYear}-${dashCurrentMonth + 1}`;
            const monthData = dashCalCache[key] || { libur: {}, peringatan: {} };
            const liburMap = monthData.libur || {};
            const peringatanMap = monthData.peringatan || {};

            const dateObj = new Date(dateStr);
            const dayName = dashDayNames[dateObj.getDay()] || '';
            const formatted = `${dayName}, ${dateObj.getDate()} ${dashMonthNames[dateObj.getMonth()]} ${dateObj.getFullYear()}`;

            let eventInfo = '';
            if (liburMap[dateStr]) {
                const liburText = typeof liburMap[dateStr] === 'string' ? liburMap[dateStr] : (liburMap[dateStr].nama || 'Libur Nasional');
                eventInfo += `<div class="text-rose-700 font-bold mt-1">&bull; ${liburText}</div>`;
            }
            if (peringatanMap[dateStr]) {
                const eventText = typeof peringatanMap[dateStr] === 'string' ? peringatanMap[dateStr] : (peringatanMap[dateStr].nama || 'Hari Peringatan');
                eventInfo += `<div class="text-amber-700 font-medium mt-1">&bull; ${eventText}</div>`;
            }

            panel.innerHTML = `
                <div class="font-bold text-slate-900">${formatted}</div>
                ${eventInfo || '<div class="text-slate-500 mt-0.5">Hari kerja operasional cetak normal.</div>'}
            `;
        }
    </script>
    @endpush
</x-app-layout>
