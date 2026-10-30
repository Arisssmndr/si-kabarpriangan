<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman Standar Selaras Foto 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 relative z-20">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Dashboard Iklan
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl font-normal">
                        Ringkasan performa dan pembukuan periklanan multi-kanal Kabar Priangan (Koran Cetak, Online, & TV).
                    </p>
                </div>
            </div>

            <!-- Filter Periode (Standar Industri: Custom Popover, Fleksibel & Simpel) -->
            <div class="relative" 
                 x-data="{ open: false }" 
                 @click.outside="open = false" 
                 @keydown.escape.window="open = false" 
                 id="dashboardFilterContainer">
                <button type="button" 
                        id="btnPeriodTrigger"
                        @click="open = !open"
                        class="h-11 px-4 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold text-slate-700 shadow-xs flex items-center gap-2.5 transition cursor-pointer select-none">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="font-bold text-slate-800">{{ $rangeTitle }}</span>
                    <span class="text-slate-400 font-normal hidden sm:inline">&bull;</span>
                    <span class="text-slate-600 font-semibold hidden sm:inline text-xs">{{ $rangeSub }}</span>
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
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pilih Rentang Waktu</span>
                        @if($range !== 'month')
                            <a href="{{ route('dashboard', ['range' => 'month']) }}" class="text-xs font-semibold text-kp-blue-600 hover:text-kp-blue-700 hover:underline">
                                Reset ke Bulan Ini
                            </a>
                        @endif
                    </div>

                    <!-- Presets Cepat -->
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
                            <a href="{{ route('dashboard', ['range' => $key]) }}" 
                               class="flex items-center justify-between px-2.5 py-2 rounded-lg text-xs font-medium transition {{ $isActive ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-semibold' : 'text-slate-700 hover:bg-slate-50 border border-transparent hover:border-slate-200' }}">
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

                    <!-- Kustom Tanggal (Fleksibel) -->
                    <div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                            Kustom Rentang Tanggal
                        </div>
                        <form method="GET" action="{{ route('dashboard') }}" class="space-y-2">
                            <input type="hidden" name="range" value="custom">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Dari Tanggal</label>
                                    <input type="date" 
                                           name="dari" 
                                           value="{{ $dari }}" 
                                           required
                                           class="w-full h-9 px-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 transition" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Sampai Tanggal</label>
                                    <input type="date" 
                                           name="sampai" 
                                           value="{{ $sampai }}" 
                                           required
                                           class="w-full h-9 px-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 transition" />
                                </div>
                            </div>
                            <button type="submit" 
                                    class="w-full h-9 bg-kp-blue-600 hover:bg-kp-blue-700 text-white rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 transition cursor-pointer shadow-2xs">
                                <span>Terapkan Rentang</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- 5 KPI Utama (Grid Proporsional, Elegan, Bersih Tanpa Stroke Tebal) -->
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-3.5">
            
            <!-- 1. Total Omset (Bruto) -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs overflow-hidden">
                <div class="flex items-center justify-between gap-1.5">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider truncate">Total Omset</span>
                    <span class="w-7 h-7 rounded-lg bg-sky-50 text-kp-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-2.5 flex items-baseline gap-1 whitespace-nowrap overflow-hidden">
                    <span class="text-xs font-medium text-slate-400 shrink-0">Rp</span>
                    <span class="text-lg xl:text-xl font-bold font-tabular text-kp-blue-600 tracking-tight truncate">
                        {{ number_format($totalOmset, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- 2. Kas Masuk (Lunas / Cash In) -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs overflow-hidden">
                <div class="flex items-center justify-between gap-1.5">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider truncate">Kas Masuk (Lunas)</span>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-2.5 flex items-baseline gap-1 whitespace-nowrap overflow-hidden">
                    <span class="text-xs font-medium text-slate-400 shrink-0">Rp</span>
                    <span class="text-lg xl:text-xl font-bold font-tabular text-emerald-600 tracking-tight truncate">
                        {{ number_format($totalBayar, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- 3. Sisa Piutang Berjalan -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs overflow-hidden">
                <div class="flex items-center justify-between gap-1.5">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider truncate">Sisa Piutang</span>
                    <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-2.5 flex items-baseline gap-1 whitespace-nowrap overflow-hidden">
                    <span class="text-xs font-medium text-slate-400 shrink-0">Rp</span>
                    <span class="text-lg xl:text-xl font-bold font-tabular {{ $totalPiutang > 0 ? 'text-rose-600' : 'text-slate-900' }} tracking-tight truncate">
                        {{ number_format($totalPiutang, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- 4. Total Transaksi -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs overflow-hidden">
                <div class="flex items-center justify-between gap-1.5">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider truncate">Volume Transaksi</span>
                    <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-2.5 flex items-baseline gap-1.5 whitespace-nowrap overflow-hidden">
                    <span class="text-lg xl:text-xl font-bold font-tabular text-slate-900 tracking-tight truncate">
                        {{ number_format($totalTransaksi) }}
                    </span>
                    <span class="text-xs font-medium text-slate-500 shrink-0">Faktur</span>
                </div>
            </div>

            <!-- 5. Total Tipe Iklan Aktif -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs overflow-hidden col-span-2 sm:col-span-1 md:col-span-1 xl:col-span-1">
                <div class="flex items-center justify-between gap-1.5">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider truncate">Tipe Iklan Aktif</span>
                    <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-2.5 flex items-baseline gap-1.5 whitespace-nowrap overflow-hidden">
                    <span class="text-lg xl:text-xl font-bold font-tabular text-slate-900 tracking-tight truncate">
                        {{ $totalJenisIklan }}
                    </span>
                    <span class="text-xs font-medium text-slate-500 shrink-0">Paket</span>
                </div>
            </div>

        </div>

        <!-- Section Visual Chart, Saluran & Kalender Nasional (Grid 12: 8 Kolom Utama + 4 Kolom Kalender Samping) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            
            <!-- Kolom Utama (8 Kolom): Visual Chart Tren Pendapatan 7 Hari & Ringkasan 3 Saluran Media -->
            <div class="lg:col-span-8 space-y-5">
                
                <!-- 1. Visual Chart Tren Pendapatan 7 Hari -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-slate-100">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Tren Pendapatan 7 Hari Terakhir</h2>                        </div>
                        <div class="flex items-center gap-3 text-xs">
                            <span class="flex items-center gap-1.5 text-slate-700 font-medium">
                                <span class="w-2.5 h-2.5 rounded-xs bg-sky-500"></span> Koran Cetak
                            </span>
                            <span class="flex items-center gap-1.5 text-slate-700 font-medium">
                                <span class="w-2.5 h-2.5 rounded-xs bg-indigo-500"></span> Media Online
                            </span>
                            <span class="flex items-center gap-1.5 text-slate-700 font-medium">
                                <span class="w-2.5 h-2.5 rounded-xs bg-teal-500"></span> Priangan TV
                            </span>
                        </div>
                    </div>

                    <div class="h-64 w-full relative">
                        <canvas id="revenueTrendChart"></canvas>
                    </div>
                </div>

                <!-- 2. Rincian Saluran Media (3 Kartu Berjejer di Bawah Chart) -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Performa Unit Saluran Media</h3>
                        <a href="{{ route('laporan.index') }}" class="text-xs font-medium text-kp-blue-600 hover:text-kp-blue-700 hover:underline">
                            Lihat Pembukuan Laporan &rarr;
                        </a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                        @foreach($channels as $cKey => $ch)
                            @php
                                $badgeColor = match($cKey) {
                                    'koran' => 'bg-sky-500',
                                    'online' => 'bg-indigo-500',
                                    default => 'bg-teal-500',
                                };
                            @endphp
                            <div class="p-3.5 rounded-xl bg-white border border-slate-200 hover:shadow-xs transition space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full {{ $badgeColor }}"></span>
                                        <span class="text-xs font-bold text-slate-900">{{ $ch['nama'] }}</span>
                                    </div>
                                    <a href="{{ route($ch['route_tx']) }}" class="text-xs font-semibold text-kp-blue-600 hover:text-kp-blue-700 hover:underline">
                                        Buka &rarr;
                                    </a>
                                </div>
                                <div class="pt-0.5">
                                    <div class="text-xs text-slate-500 font-medium">Total Omzet:</div>
                                    <div class="text-base font-bold text-slate-900 font-tabular tracking-tight">
                                        Rp {{ number_format($ch['omzet'], 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-xs text-slate-600 font-tabular pt-2 border-t border-slate-100">
                                    <span>Kas: <strong class="text-emerald-700 font-semibold">Rp {{ number_format($ch['bayar'], 0, ',', '.') }}</strong></span>
                                    <span>Piutang: <strong class="text-rose-600 font-semibold">Rp {{ number_format($ch['piutang'], 0, ',', '.') }}</strong></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
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
                            <h2 class="text-sm font-bold text-slate-900 leading-tight">Kalender Nasional</h2>
                            <p class="text-xs text-slate-500">Agenda edisi & hari besar pers</p>
                        </div>
                    </div>
                    <button type="button" 
                            onclick="resetDashToday()" 
                            class="px-2.5 py-1 text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 hover:bg-kp-blue-100 rounded-md transition cursor-pointer">
                        Hari Ini
                    </button>
                </div>

                <!-- Navigasi Bulan & Tahun -->
                <div class="flex items-center justify-between px-1">
                    <button type="button" 
                            onclick="changeDashMonth(-1)" 
                            class="w-7 h-7 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer"
                            title="Bulan Sebelumnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-1.5">
                        <span id="dashCalMonthTitle" class="text-xs font-bold text-slate-900">
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
                            class="w-7 h-7 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer"
                            title="Bulan Berikutnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- Header Hari (Min, Sen, Sel, Rab, Kam, Jum, Sab) -->
                <div class="grid grid-cols-7 gap-1 text-center">
                    <span class="text-xs font-bold text-rose-500 py-1">Min</span>
                    <span class="text-xs font-semibold text-slate-500 py-1">Sen</span>
                    <span class="text-xs font-semibold text-slate-500 py-1">Sel</span>
                    <span class="text-xs font-semibold text-slate-500 py-1">Rab</span>
                    <span class="text-xs font-semibold text-slate-500 py-1">Kam</span>
                    <span class="text-xs font-semibold text-slate-500 py-1">Jum</span>
                    <span class="text-xs font-semibold text-slate-500 py-1">Sab</span>
                </div>

                <!-- Grid Tanggal Kompak Standar Industri -->
                <div id="dashCalGrid" class="grid grid-cols-7 gap-1 text-center min-h-[190px]">
                    <!-- Dirender via JavaScript -->
                </div>

                <!-- Legenda Simpel Ringkas Persegi (Standar Industri Kabar Priangan) -->
                <div class="flex items-center justify-between text-xs text-slate-500 border-t border-slate-100 pt-2 px-0.5">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-kp-blue-600 inline-block shadow-2xs"></span> Terpilih / Hari Ini</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-rose-50 border border-rose-300 inline-block"></span> Libur / Minggu</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-amber-50 border border-amber-300 inline-block"></span> Peringatan</span>
                </div>

                <!-- Panel Informasi Tanggal Terpilih / Event -->
                <div id="dashCalInfoPanel" class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1.5 transition-all">
                    <!-- Render dinamis: Tanggal & Keterangan Libur / Peringatan / Normal -->
                </div>

            </div>
        </div>

        <!-- Section Aktivitas Transaksi Terbaru -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <!-- Header Tabel Transaksi -->
            <!-- <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Aktivitas Transaksi Terbaru</h2>
                    <p class="text-sm text-slate-500 mt-1">Monitoring transaksi terkini lintas koran cetak, portal online, dan Priangan TV</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Realtime Terupdate
                    </span>
                </div>
            </div> -->


            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/50 text-slate-600 text-xs font-semibold whitespace-nowrap">
                            <th class="py-3 px-4 text-center w-10">No</th>
                            <th class="py-3 px-4">Saluran</th>
                            <th class="py-3 px-4">No. Faktur</th>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Pemasang / Klien</th>
                            <th class="py-3 px-4">Jenis / Paket Iklan</th>
                            <th class="py-3 px-4 text-right">Nilai Tagihan</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($recentStream as $key => $tx)
                            @php
                                $printUrl = match($tx->code) {
                                    'KRN' => route('transaksikoran.cetak', $tx->id),
                                    'ONL' => route('transaksionline.cetak', $tx->id),
                                    'PTV' => route('transaksipriangan.cetak', $tx->id),
                                    default => null,
                                };
                                $reportMedia = match($tx->code) {
                                    'KRN' => 'koran',
                                    'ONL' => 'online',
                                    'PTV' => 'tv',
                                    default => 'all',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-4 text-center text-slate-900 font-normal">
                                    {{ $key + 1 }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-900 border border-slate-200">
                                        {{ $tx->channel }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap font-mono font-semibold text-kp-blue-700">
                                    {{ $tx->faktur }}
                                </td>
                                <td class="py-3 px-4 text-slate-600 whitespace-nowrap font-mono">
                                    {{ \Carbon\Carbon::parse($tx->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-slate-900 font-medium whitespace-nowrap">
                                    {{ $tx->customer }}
                                </td>
                                <td class="py-3 px-4 text-slate-600 max-w-xs truncate font-normal">
                                    {{ $tx->paket }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-xs">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-medium text-slate-900 tabular-nums">{{ number_format($tx->total, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-900 border border-slate-200">
                                        {{ $tx->is_lunas ? 'Lunas' : 'Piutang' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap" 
                                    x-data="{ open: false }" 
                                    :class="{ 'relative z-50': open }">
                                    <div class="relative inline-block text-left" @click.outside="open = false">
                                        <!-- Tombol Titik Tiga (Slim, Tajam & Kontras Tinggi) -->
                                        <button type="button" 
                                                @click="open = !open" 
                                                class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-700 hover:text-slate-950 hover:bg-slate-100 transition focus:outline-none cursor-pointer"
                                                :class="{ 'text-slate-950 bg-slate-100': open }"
                                                title="Pilihan Aksi">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="5" r="1.5" />
                                                <circle cx="12" cy="12" r="1.5" />
                                                <circle cx="12" cy="19" r="1.5" />
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu 3 Tombol Aksi Dashboard -->
                                        <div x-cloak 
                                             x-show="open" 
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-44 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">
                                            
                                            <!-- 1. Buka Transaksi -->
                                            <a href="{{ $tx->route_view }}" 
                                               @click="open = false"
                                               class="group flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:text-kp-blue-700 hover:bg-kp-blue-50 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 group-hover:text-kp-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                                </svg>
                                                <span>Buka Modul</span>
                                            </a>

                                            <!-- 2. Cetak Dokumen Faktur (Jika Ada) -->
                                            @if($printUrl)
                                                <a href="{{ $printUrl }}" 
                                                   target="_blank"
                                                   @click="open = false"
                                                   class="group flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:text-kp-blue-700 hover:bg-kp-blue-50 rounded-lg transition">
                                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-kp-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                    <span>Cetak Faktur</span>
                                                </a>
                                            @endif

                                            <!-- Separator -->
                                            <div class="h-px bg-slate-100 my-1"></div>

                                            <!-- 3. Lihat di Laporan -->
                                            <a href="{{ route('laporan.index', ['media' => $reportMedia]) }}" 
                                               @click="open = false"
                                               class="group flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:text-kp-blue-700 hover:bg-kp-blue-50 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 group-hover:text-kp-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span>Lihat di Laporan</span>
                                            </a>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-10 text-center text-slate-900 text-xs font-normal">
                                    Belum ada transaksi tercatat.
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
        // ==============================================================
        // 1. CHART.JS: TREN PENDAPATAN 7 HARI TERAKHIR
        // ==============================================================
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('revenueTrendChart');
            if (ctx) {
                const labels = @json($dailyLabels ?? []);
                const dataKoran = @json($dailyKoran ?? []);
                const dataOnline = @json($dailyOnline ?? []);
                const dataTv = @json($dailyTv ?? []);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Koran Cetak',
                                data: dataKoran,
                                backgroundColor: 'rgba(14, 165, 233, 0.85)', // sky-500
                                borderRadius: 4,
                                barPercentage: 0.6,
                            },
                            {
                                label: 'Media Online',
                                data: dataOnline,
                                backgroundColor: 'rgba(99, 102, 241, 0.85)', // indigo-500
                                borderRadius: 4,
                                barPercentage: 0.6,
                            },
                            {
                                label: 'Priangan TV',
                                data: dataTv,
                                backgroundColor: 'rgba(20, 184, 166, 0.85)', // teal-500
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
                                    font: { size: 10 },
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

            // Inisialisasi Widget Kalender Dashboard
            initDashCalendar();
        });

        // ==============================================================
        // 2. WIDGET KALENDER NASIONAL & AGENDA PERS MODERN
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

                const btn = document.createElement('button');
                btn.type = 'button';
                let btnClass = 'h-8 rounded-md flex items-center justify-center relative text-xs transition cursor-pointer select-none border ';

                if (isSelected) {
                    btnClass += 'bg-kp-blue-600 text-white font-bold border-kp-blue-700 shadow-2xs ';
                } else if (isToday) {
                    btnClass += 'bg-kp-blue-50 text-kp-blue-700 font-bold border border-kp-blue-300 hover:bg-kp-blue-100 ';
                } else if (liburDesc) {
                    btnClass += 'text-rose-700 font-bold bg-rose-50 border-rose-200 hover:bg-rose-100 ';
                } else if (peringatanDesc) {
                    btnClass += 'text-amber-800 font-bold bg-amber-50 border-amber-200 hover:bg-amber-100 ';
                } else if (isSunday) {
                    btnClass += 'text-rose-600 font-semibold bg-rose-50/70 border-rose-200/80 hover:bg-rose-100 ';
                } else {
                    btnClass += 'text-slate-700 font-medium border-transparent hover:bg-slate-100 ';
                }
                btn.className = btnClass;

                // Sel Kalender: Angka tanggal kotak bersih tanpa titik lingkaran
                btn.innerHTML = `
                    <span class="leading-none text-xs font-semibold ${isSunday && !isSelected && !liburDesc ? 'text-rose-600' : ''}">${day}</span>
                `;

                if (liburDesc || peringatanDesc) {
                    btn.title = [liburDesc ? `Libur: ${liburDesc}` : null, peringatanDesc ? `Peringatan: ${peringatanDesc}` : null].filter(Boolean).join(' • ');
                }

                btn.onclick = () => {
                    dashSelectedDate = dateStr;
                    renderDashCalendar();
                    updateDashInfoPanel(dateStr);
                };

                grid.appendChild(btn);
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

            let html = `
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-slate-900">${formatted}</span>
                    <span class="text-xs font-mono text-slate-500">${dateStr}</span>
                </div>
            `;

            if (liburDesc) {
                html += `
                    <div class="p-2 rounded-md bg-rose-50 border border-rose-200/80 text-rose-800 text-xs flex items-start gap-1.5 mt-1">
                        <svg class="w-3.5 h-3.5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <strong class="font-bold">Libur Nasional:</strong> ${liburDesc}
                        </div>
                    </div>
                `;
            }

            if (peringatanDesc) {
                html += `
                    <div class="p-2 rounded-md bg-amber-50 border border-amber-200/80 text-amber-900 text-xs flex items-start gap-1.5 mt-1">
                        <svg class="w-3.5 h-3.5 text-amber-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <div>
                            <strong class="font-bold">Momentum / Potensi Iklan:</strong> ${peringatanDesc}
                        </div>
                    </div>
                `;
            }

            if (!liburDesc && !peringatanDesc) {
                html += `
                    <div class="text-xs text-slate-500 flex items-center justify-between pt-0.5">
                        <span>${isSunday ? 'Hari Minggu / Edisi Khusus' : 'Hari Kerja / Edisi Terbit Harian'}</span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold px-2 py-0.5 bg-slate-100 text-slate-600">Terbit Normal</span>
                    </div>
                `;
            }

            panel.innerHTML = html;
        }
    </script>
    @endpush
</x-app-layout>
