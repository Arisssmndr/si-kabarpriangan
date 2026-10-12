<x-app-layout>
    <div class="space-y-6">

        <!-- Header Laporan & Aksi Cetak Dokumen Resmi (Standar Enterprise Dashboard) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Laporan Iklan
                    </h1>
                    <p class="text-xs font-normal text-slate-500 mt-0.5">
                        Pusat rekapitulasi data dan pembukuan transaksi periklanan multi-kanal Kabar Priangan
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('laporan.cetak', request()->query()) }}" 
                   target="_blank" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs font-semibold rounded-lg shadow-xs hover:shadow transition cursor-pointer">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Dokumen Resmi</span>
                </a>
            </div>
        </div>

        <!-- Filter Toolbar Bersih & Standar Industri (Modern Popover + Saluran + Status) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs relative z-20">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                
                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- 1. Filter Periode (Modern Popover Dropdown) -->
                    <div class="relative" 
                         x-data="{ open: false }" 
                         @click.outside="open = false" 
                         @keydown.escape.window="open = false" 
                         id="laporanFilterContainer">
                        <button type="button" 
                                id="btnLaporanPeriodTrigger"
                                @click="open = !open" 
                                class="h-9 px-3.5 bg-white hover:bg-slate-50 border border-slate-200 hover:border-slate-300 rounded-lg text-xs font-semibold text-slate-800 shadow-2xs hover:shadow-xs flex items-center gap-2.5 transition cursor-pointer select-none">
                            <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="font-bold text-slate-900">{{ $periodTitle }}</span>
                            <span class="text-slate-300 font-normal hidden sm:inline">&bull;</span>
                            <span class="text-slate-500 font-normal hidden sm:inline text-[11px]">{{ $periodSub }}</span>
                            <svg id="laporanPeriodChevron" 
                                 :class="{ 'rotate-180': open }" 
                                 class="w-3.5 h-3.5 text-slate-400 transition-transform duration-150 shrink-0" 
                                 fill="none" 
                                 stroke="currentColor" 
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Popover Panel Laporan -->
                        <div id="laporanPeriodPanel" 
                             x-cloak
                             x-show="open" 
                             x-transition.duration.150ms
                             class="absolute left-0 top-full mt-1.5 w-80 bg-white rounded-xl shadow-xl border border-slate-200 z-50 p-3.5">
                            
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100 mb-2.5">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pilih Rentang Waktu</span>
                                @if($period !== 'month')
                                    <a href="{{ route('laporan.index', array_merge(request()->query(), ['period' => 'month', 'dari' => null, 'sampai' => null])) }}" class="text-[11px] font-medium text-kp-blue-600 hover:text-kp-blue-700 hover:underline">
                                        Reset ke Bulan Ini
                                    </a>
                                @endif
                            </div>

                            <!-- Presets Cepat -->
                            <div class="grid grid-cols-2 gap-1.5 mb-3">
                                @php
                                    $reportPresets = [
                                        'today'      => 'Hari Ini',
                                        '7days'      => '7 Hari Terakhir',
                                        'month'      => 'Bulan Ini',
                                        '30days'     => '30 Hari Terakhir',
                                        'last_month' => 'Bulan Lalu',
                                        'year'       => 'Tahun Ini',
                                    ];
                                @endphp
                                @foreach($reportPresets as $key => $title)
                                    @php $isActive = ($period === $key); @endphp
                                    <a href="{{ route('laporan.index', array_merge(request()->query(), ['period' => $key, 'dari' => null, 'sampai' => null])) }}" 
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
                                <form method="GET" action="{{ route('laporan.index') }}" class="space-y-2">
                                    <input type="hidden" name="period" value="custom">
                                    @if(request()->filled('media'))
                                        <input type="hidden" name="media" value="{{ request('media') }}">
                                    @endif
                                    @if(request()->filled('status'))
                                        <input type="hidden" name="status" value="{{ request('status') }}">
                                    @endif
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

                    <!-- 2. Dropdown Saluran Media -->
                    <form method="GET" action="{{ route('laporan.index') }}" id="laporanMediaForm" class="flex items-center gap-1.5">
                        <input type="hidden" name="period" value="{{ $period }}">
                        @if($period === 'custom')
                            <input type="hidden" name="dari" value="{{ $dari }}">
                            <input type="hidden" name="sampai" value="{{ $sampai }}">
                        @endif
                        <input type="hidden" name="status" value="{{ $status }}">
                        <div class="relative">
                            <select name="media" onchange="document.getElementById('laporanMediaForm').submit()" 
                                    class="h-9 pl-3 pr-8 bg-white border border-slate-200 hover:border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 shadow-2xs transition cursor-pointer">
                                <option value="all" {{ $media === 'all' ? 'selected' : '' }}>Semua Saluran</option>
                                <option value="koran" {{ $media === 'koran' ? 'selected' : '' }}>Koran Cetak</option>
                                <option value="online" {{ $media === 'online' ? 'selected' : '' }}>Media Online</option>
                                <option value="tv" {{ $media === 'tv' ? 'selected' : '' }}>Priangan TV</option>
                            </select>
                        </div>
                    </form>

                    <!-- 3. Dropdown Status Pembayaran -->
                    <form method="GET" action="{{ route('laporan.index') }}" id="laporanStatusForm" class="flex items-center gap-1.5">
                        <input type="hidden" name="period" value="{{ $period }}">
                        @if($period === 'custom')
                            <input type="hidden" name="dari" value="{{ $dari }}">
                            <input type="hidden" name="sampai" value="{{ $sampai }}">
                        @endif
                        <input type="hidden" name="media" value="{{ $media }}">
                        <div class="relative">
                            <select name="status" onchange="document.getElementById('laporanStatusForm').submit()" 
                                    class="h-9 pl-3 pr-8 bg-white border border-slate-200 hover:border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 shadow-2xs transition cursor-pointer">
                                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="lunas" {{ $status === 'lunas' ? 'selected' : '' }}>Lunas Saja</option>
                                <option value="piutang" {{ $status === 'piutang' ? 'selected' : '' }}>Belum Lunas (Piutang)</option>
                            </select>
                        </div>
                    </form>
                </div>

                <!-- Status Filter & Tombol Reset -->
                <div class="flex items-center gap-2 shrink-0">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                        {{ $periodLabel }}
                    </span>
                    @if($period !== 'month' || $media !== 'all' || $status !== 'all')
                        <a href="{{ route('laporan.index') }}" 
                           class="h-9 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-lg transition flex items-center justify-center">
                            Reset Filter
                        </a>
                    @endif
                </div>

            </div>
        </div>

        <!-- 4 KPI Finansial Utama (Clean 60-30-10, Anti-Numpuk, Angka Hitam Tegas) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- 1. Total Omset -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs overflow-hidden">
                <div class="flex items-center justify-between gap-1.5">
                    <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider truncate">Total Omset</span>
                    <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-2.5 flex items-baseline gap-0.5 whitespace-nowrap overflow-hidden">
                    <span class="text-xs font-semibold text-slate-600 shrink-0">Rp</span>
                    <span class="text-lg xl:text-xl font-bold font-tabular text-slate-900 tracking-tight truncate">
                        {{ number_format($totalOmset, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- 2. Kas Masuk (Lunas) -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs overflow-hidden">
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
                        {{ number_format($totalKasMasuk, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- 3. Sisa Piutang -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs overflow-hidden">
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
                        {{ number_format($totalPiutang, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- 4. Volume Transaksi -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs overflow-hidden">
                <div class="flex items-center justify-between gap-1.5">
                    <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider truncate">Volume Transaksi</span>
                    <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-2.5 flex items-baseline gap-1 whitespace-nowrap overflow-hidden">
                    <span class="text-lg xl:text-xl font-bold font-tabular text-slate-900 tracking-tight truncate">{{ number_format($totalTransaksi) }}</span>
                    <span class="text-xs font-normal text-slate-500 shrink-0">Faktur</span>
                </div>
            </div>
        </div>

        <!-- Rincian Kontribusi Saluran Media (3 Unit Media: Koran, Online, TV) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($breakdown as $bKey => $bData)
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs space-y-2.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $bKey === 'koran' ? 'bg-sky-500' : ($bKey === 'online' ? 'bg-indigo-500' : 'bg-teal-500') }}"></span>
                            <span class="text-xs font-bold text-slate-900">{{ $bData['nama'] }}</span>
                        </div>
                        <span class="text-[11px] font-medium text-slate-500">{{ $bData['count'] }} Faktur</span>
                    </div>
                    
                    <div class="flex items-baseline justify-between pt-0.5">
                        <span class="text-xs font-normal text-slate-500">Omset:</span>
                        <span class="text-xs font-bold font-tabular text-slate-900">Rp&nbsp;{{ number_format($bData['total'], 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-slate-700 font-tabular pt-1.5 border-t border-slate-200">
                        <span>Kas: <strong class="font-semibold text-slate-900">Rp&nbsp;{{ number_format($bData['bayar'], 0, ',', '.') }}</strong></span>
                        <span>Piutang: <strong class="font-semibold text-slate-900">Rp&nbsp;{{ number_format($bData['piutang'], 0, ',', '.') }}</strong></span>
                    </div>

                    <div class="pt-1 flex items-center justify-between">
                        @if($media === $bKey)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                Saluran Aktif
                            </span>
                            <a href="{{ route('laporan.index', array_merge(request()->query(), ['media' => 'all'])) }}" 
                               class="text-[11px] font-semibold text-kp-blue-600 hover:text-kp-blue-700 hover:underline">
                                Lihat Semua &rarr;
                            </a>
                        @else
                            <a href="{{ route('laporan.index', array_merge(request()->query(), ['media' => $bKey])) }}" 
                               class="text-[11px] font-semibold text-kp-blue-600 hover:text-kp-blue-700 hover:underline">
                                Filter Saluran Ini &rarr;
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Tabel Rekapitulasi Data Laporan (Font Hitam Tajam, Hierarki Bersih) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Rincian Transaksi</h2>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-700 text-xs whitespace-nowrap">
                            <th class="py-3 px-3.5 font-semibold text-center w-10">No</th>
                            <th class="py-3 px-3.5 font-semibold">No. Faktur</th>
                            <th class="py-3 px-3.5 font-semibold">Saluran</th>
                            <th class="py-3 px-3.5 font-semibold">Tanggal</th>
                            <th class="py-3 px-3.5 font-semibold">Pemasang / Klien</th>
                            <th class="py-3 px-3.5 font-semibold">Jenis / Paket</th>
                            <th class="py-3 px-3.5 font-semibold text-right">Tagihan</th>
                            <th class="py-3 px-3.5 font-semibold text-right">Kas Masuk</th>
                            <th class="py-3 px-3.5 font-semibold text-right">Piutang</th>
                            <th class="py-3 px-3.5 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($items as $idx => $row)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-slate-900 font-normal">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono font-medium bg-slate-100 text-slate-900 border border-slate-200">
                                        {{ $row->faktur }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-900 border border-slate-200">
                                        {{ $row->media_name }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-slate-900 whitespace-nowrap font-normal">
                                    {{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-3.5 text-slate-900 font-semibold whitespace-nowrap">
                                    {{ $row->customer }}
                                </td>
                                <td class="py-3 px-3.5 text-slate-900 max-w-xs truncate font-normal">
                                    {{ $row->paket }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-bold font-tabular text-slate-900 whitespace-nowrap">
                                    Rp&nbsp;{{ number_format($row->total, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-bold font-tabular text-slate-900 whitespace-nowrap">
                                    Rp&nbsp;{{ number_format($row->bayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-bold font-tabular text-slate-900 whitespace-nowrap">
                                    Rp&nbsp;{{ number_format($row->piutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-900 border border-slate-200">
                                        {{ $row->is_lunas ? 'Lunas' : 'Piutang' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-10 text-center text-slate-900 text-xs font-normal">
                                    Tidak ada data transaksi untuk filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($items->isNotEmpty())
                        <tfoot>
                            <tr class="bg-slate-50 border-t-2 border-slate-200 text-slate-900 font-semibold text-xs">
                                <td colspan="6" class="py-3 px-3.5 text-right uppercase tracking-wider text-[11px] text-slate-700 font-semibold">
                                    Total:
                                </td>
                                <td class="py-3 px-3.5 text-right font-bold font-tabular text-slate-900 whitespace-nowrap">
                                    Rp&nbsp;{{ number_format($totalOmset, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-bold font-tabular text-slate-900 whitespace-nowrap">
                                    Rp&nbsp;{{ number_format($totalKasMasuk, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-bold font-tabular text-slate-900 whitespace-nowrap">
                                    Rp&nbsp;{{ number_format($totalPiutang, 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
