<x-app-layout>
    <div class="space-y-6">

        <!-- Header Dashboard & Filter Periode Terpadu (Standar Enterprise) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 relative z-20">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Dashboard Keuangan
                    </h1>
                    <p class="text-xs font-normal text-slate-500 mt-0.5">
                        Ringkasan arus kas masuk, pengelolaan piutang, dan pelunasan multi-kanal Kabar Priangan (Koran Cetak, Online, & TV)
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
                        class="h-9 px-3.5 bg-white hover:bg-slate-50 border border-slate-200 hover:border-slate-300 rounded-lg text-xs font-semibold text-slate-800 shadow-2xs hover:shadow-xs flex items-center gap-2.5 transition cursor-pointer select-none">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="font-bold text-slate-900">{{ $rangeTitle }}</span>
                    <span class="text-slate-300 font-normal hidden sm:inline">&bull;</span>
                    <span class="text-slate-500 font-normal hidden sm:inline text-[11px]">{{ $rangeSub }}</span>
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
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pilih Rentang Waktu</span>
                        @if($range !== 'month')
                            <a href="{{ route('keuangan.dashboard', ['range' => 'month']) }}" class="text-[11px] font-medium text-kp-blue-600 hover:text-kp-blue-700 hover:underline">
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
                            <a href="{{ route('keuangan.dashboard', ['range' => $key]) }}" 
                               class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-medium transition {{ $isActive ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-semibold' : 'text-slate-700 hover:bg-slate-50 border border-transparent hover:border-slate-200' }}">
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
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                            Kustom Rentang Tanggal
                        </div>
                        <form method="GET" action="{{ route('keuangan.dashboard') }}" class="space-y-2">
                            <input type="hidden" name="range" value="custom">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[10px] font-medium text-slate-600 mb-1">Dari Tanggal</label>
                                    <input type="date" 
                                           name="dari" 
                                           value="{{ $dari }}" 
                                           required
                                           class="w-full h-8 px-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 transition" />
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-slate-600 mb-1">Sampai Tanggal</label>
                                    <input type="date" 
                                           name="sampai" 
                                           value="{{ $sampai }}" 
                                           required
                                           class="w-full h-8 px-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 transition" />
                                </div>
                            </div>
                            <button type="submit" 
                                    class="w-full h-8 bg-kp-blue-600 hover:bg-kp-blue-700 text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 transition cursor-pointer shadow-2xs">
                                <span>Terapkan Rentang</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- 5 KPI Utama (Grid Proporsional, Anti-Numpuk, Angka Hitam Tegas) -->
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-3.5">
            
            <!-- 1. Total Tagihan (Bruto) -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs overflow-hidden">
                <div class="flex items-center justify-between gap-1.5">
                    <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider truncate">Total Tagihan</span>
                    <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-2.5 flex items-baseline gap-0.5 whitespace-nowrap overflow-hidden">
                    <span class="text-xs font-semibold text-slate-600 shrink-0">Rp</span>
                    <span class="text-lg xl:text-xl font-bold font-tabular text-slate-900 tracking-tight truncate">
                        {{ number_format($totalTagihan, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- 2. Kas Masuk (Lunas / Cash In) -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs overflow-hidden">
                <div class="flex items-center justify-between gap-1.5">
                    <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider truncate">Kas Masuk (Lunas)</span>
                    <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-2.5 flex items-baseline gap-0.5 whitespace-nowrap overflow-hidden">
                    <span class="text-xs font-semibold text-slate-600 shrink-0">Rp</span>
                    <span class="text-lg xl:text-xl font-bold font-tabular text-slate-900 tracking-tight truncate">
                        {{ number_format($totalPenerimaan, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- 3. Sisa Piutang Berjalan -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs overflow-hidden">
                <div class="flex items-center justify-between gap-1.5">
                    <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider truncate">Sisa Piutang</span>
                    <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-2.5 flex items-baseline gap-0.5 whitespace-nowrap overflow-hidden">
                    <span class="text-xs font-semibold text-slate-600 shrink-0">Rp</span>
                    <span class="text-lg xl:text-xl font-bold font-tabular text-slate-900 tracking-tight truncate">
                        {{ number_format($totalPiutangPeriode, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- 4. Total Transaksi -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs overflow-hidden">
                <div class="flex items-center justify-between gap-1.5">
                    <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider truncate">Volume Transaksi</span>
                    <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-2.5 flex items-baseline gap-1 whitespace-nowrap overflow-hidden">
                    <span class="text-lg xl:text-xl font-bold font-tabular text-slate-900 tracking-tight truncate">
                        {{ number_format($totalTransaksi) }}
                    </span>
                    <span class="text-xs font-normal text-slate-500 shrink-0">Faktur</span>
                </div>
            </div>

            <!-- 5. Faktur Pending -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs overflow-hidden col-span-2 sm:col-span-1 md:col-span-1 xl:col-span-1">
                <div class="flex items-center justify-between gap-1.5">
                    <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider truncate">Faktur Pending</span>
                    <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-2.5 flex items-baseline gap-1 whitespace-nowrap overflow-hidden">
                    <span class="text-lg xl:text-xl font-bold font-tabular text-slate-900 tracking-tight truncate">
                        {{ $invoicePendingPeriode }}
                    </span>
                    <span class="text-xs font-normal text-slate-500 shrink-0">Faktur</span>
                </div>
            </div>

        </div>

        <!-- Section Visual Chart & Komparasi Saluran (Grid 12: 8 Kolom Chart + 4 Kolom Rincian Divisi) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            
            <!-- Kolom Kiri (8 Kolom): Visual Chart Tren Penerimaan Kas 7 Hari -->
            <div class="lg:col-span-8 bg-white border border-slate-200 rounded-xl p-5 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Tren Penerimaan Kas 7 Hari Terakhir</h2>
                    </div>
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

            <!-- Kolom Kanan (4 Kolom): Rincian 3 Saluran Media -->
            <div class="lg:col-span-4 bg-white border border-slate-200 rounded-xl p-5 shadow-xs space-y-4 flex flex-col justify-between">
                <div>
                    <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Rincian Saluran Media</h2>
                            <p class="text-xs font-normal text-slate-500 mt-0.5">Ringkasan transaksi kas & piutang per saluran</p>
                        </div>
                        <a href="{{ route('laporan.index') }}" class="text-xs font-medium text-kp-blue-600 hover:text-kp-blue-700 hover:underline">
                            Laporan &rarr;
                        </a>
                    </div>

                    <div class="space-y-3.5 mt-3.5">
                        @foreach($channels as $cKey => $ch)
                            <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200/80 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full {{ $cKey === 'koran' ? 'bg-sky-500' : ($cKey === 'online' ? 'bg-indigo-500' : 'bg-teal-500') }}"></span>
                                        <span class="text-xs font-bold text-slate-900">{{ $ch['nama'] }}</span>
                                    </div>
                                    <a href="{{ route($ch['route_tx']) }}" class="text-xs font-medium text-kp-blue-600 hover:text-kp-blue-700 hover:underline">
                                        Buka &rarr;
                                    </a>
                                </div>
                                <div class="flex items-baseline justify-between text-xs font-tabular pt-0.5">
                                    <span class="text-slate-600 font-normal">Tagihan:</span>
                                    <span class="font-bold text-slate-900">Rp&nbsp;{{ number_format($ch['tagihan'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-700 font-tabular pt-1.5 border-t border-slate-200">
                                    <span>Kas: <strong class="font-semibold text-slate-900">Rp&nbsp;{{ number_format($ch['bayar'], 0, ',', '.') }}</strong></span>
                                    <span>Piutang: <strong class="font-semibold text-slate-900">Rp&nbsp;{{ number_format($ch['piutang'], 0, ',', '.') }}</strong></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Analisis Umur Piutang (Aging Schedule) -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3.5 border-b border-slate-100">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Analisis Umur Piutang (Aging Schedule)</h2>
                    <p class="text-xs font-normal text-slate-500 mt-0.5">Segmentasi resiko penagihan berdasarkan tanggal penerbitan invoice</p>
                </div>
                <div class="text-xs font-semibold text-slate-700 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200 self-start sm:self-auto font-tabular">
                    Total Piutang Aktif: <strong class="font-bold text-slate-900">Rp {{ number_format($totalPiutangKumulatif, 0, ',', '.') }}</strong>
                </div>
            </div>

            <!-- 3 Kolom Aging Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 mt-3.5">
                
                <!-- 1. Lancar (<= 30 Hari) -->
                <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-bold text-slate-900">Lancar (&le; 30 Hari)</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            {{ $agingLancarCount }} Faktur
                        </span>
                    </div>
                    <div class="text-base font-bold font-tabular text-slate-900 pt-1">
                        Rp&nbsp;{{ number_format($agingLancar, 0, ',', '.') }}
                    </div>
                    <p class="text-[11px] text-slate-500">Penagihan reguler berjalan tanpa kendala</p>
                </div>

                <!-- 2. Perhatian (31 - 60 Hari) -->
                <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span class="text-xs font-bold text-slate-900">Perhatian (31 &ndash; 60 Hari)</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                            {{ $agingPerhatianCount }} Faktur
                        </span>
                    </div>
                    <div class="text-base font-bold font-tabular text-slate-900 pt-1">
                        Rp&nbsp;{{ number_format($agingPerhatian, 0, ',', '.') }}
                    </div>
                    <p class="text-[11px] text-slate-500">Perlu pengiriman surat pengingat / reminder</p>
                </div>

                <!-- 3. Jatuh Tempo (> 60 Hari) -->
                <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            <span class="text-xs font-bold text-slate-900">Jatuh Tempo (&gt; 60 Hari)</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                            {{ $agingKritisCount }} Faktur
                        </span>
                    </div>
                    <div class="text-base font-bold font-tabular text-slate-900 pt-1">
                        Rp&nbsp;{{ number_format($agingKritis, 0, ',', '.') }}
                    </div>
                    <p class="text-[11px] text-slate-500">Prioritas penagihan langsung / konfirmasi intensif</p>
                </div>

            </div>
        </div>

        <!-- Section Aktivitas Transaksi Terbaru (Standar Perbankan: Font Hitam Tajam, Hierarki Bersih) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Aktivitas Transaksi & Pembayaran Terbaru</h2>
                    <p class="text-xs font-normal text-slate-500 mt-0.5">Aliran faktur multi-saluran terdaftar di sistem</p>
                </div>
                <a href="{{ route('laporan.index') }}" class="text-xs font-medium text-kp-blue-600 hover:text-kp-blue-700 hover:underline">
                    Lihat Semua di Laporan &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-700 text-xs whitespace-nowrap">
                            <th class="py-3 px-4 font-semibold text-center w-10">No</th>
                            <th class="py-3 px-4 font-semibold">Saluran</th>
                            <th class="py-3 px-4 font-semibold">No. Faktur</th>
                            <th class="py-3 px-4 font-semibold">Tanggal</th>
                            <th class="py-3 px-4 font-semibold">Pemasang / Klien</th>
                            <th class="py-3 px-4 font-semibold">Jenis / Paket Iklan</th>
                            <th class="py-3 px-4 font-semibold text-right">Nilai Tagihan</th>
                            <th class="py-3 px-4 font-semibold text-right">Kas Masuk</th>
                            <th class="py-3 px-4 font-semibold text-right">Sisa Piutang</th>
                            <th class="py-3 px-4 font-semibold text-center">Status</th>
                            <th class="py-3 px-4 font-semibold text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($recentStream as $key => $tx)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-4 text-center text-slate-900 font-normal">
                                    {{ $key + 1 }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-900 border border-slate-200">
                                        {{ $tx->channel }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap font-mono font-medium text-slate-900">
                                    {{ $tx->faktur }}
                                </td>
                                <td class="py-3 px-4 text-slate-900 whitespace-nowrap font-normal">
                                    {{ \Carbon\Carbon::parse($tx->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-slate-900 font-semibold whitespace-nowrap">
                                    {{ $tx->customer }}
                                </td>
                                <td class="py-3 px-4 text-slate-900 max-w-xs truncate font-normal">
                                    {{ $tx->paket }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold font-tabular text-slate-900 whitespace-nowrap">
                                    Rp&nbsp;{{ number_format($tx->total, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-semibold font-tabular text-emerald-600 whitespace-nowrap">
                                    Rp&nbsp;{{ number_format($tx->bayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold font-tabular {{ $tx->piutang > 0 ? 'text-rose-600' : 'text-slate-400' }} whitespace-nowrap">
                                    Rp&nbsp;{{ number_format($tx->piutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold {{ $tx->is_lunas ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                                        {{ $tx->is_lunas ? 'Lunas' : 'Piutang' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <a href="{{ $tx->route_cetak }}" target="_blank" class="text-xs font-semibold text-kp-blue-600 hover:text-kp-blue-700 hover:underline">
                                        Cetak Faktur
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-10 text-center text-slate-900 text-xs font-normal">
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
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('revenueTrendChart');
            if (!ctx) return;

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
                        legend: {
                            display: false,
                        },
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
                            ticks: {
                                font: { size: 11, weight: '500' },
                                color: '#334155'
                            }
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
        });
    </script>
    @endpush
</x-app-layout>
