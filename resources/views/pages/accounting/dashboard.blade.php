<x-app-layout>
    <div class="space-y-5">

        <!-- ============================================================== -->
        <!-- 1. HEADER HALAMAN STANDAR & FILTER PERIODE (PALET 60-30-10)    -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 relative z-20">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Dashboard Akuntansi
                        </h1>
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                            Pembukuan & Finansial
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Ringkasan eksekutif pendapatan, penerimaan kas, piutang usaha, dan jurnal akuntansi.
                    </p>
                </div>
            </div>

            <!-- Filter Periode (Popover Standar Selaras Divisi Iklan) -->
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
                            <a href="{{ route('accounting.dashboard', ['range' => 'month']) }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
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
                            <a href="{{ route('accounting.dashboard', ['range' => $key]) }}"
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
                        <form method="GET" action="{{ route('accounting.dashboard') }}" class="space-y-2.5">
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
                                Terapkan Periode
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- ============================================================== -->
        <!-- 2. KPI UTAMA FINANSIAL (4 KARTU PALET 60-30-10)                -->
        <!-- ============================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Pendapatan Usaha -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pendapatan Usaha</span>
                    <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-600 border border-kp-blue-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-xl font-bold text-slate-900">
                    Rp {{ number_format($omsetUsaha, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-slate-400">Total omset diakui periode ini</div>
            </div>

            <!-- Penerimaan Kas & Bank -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Penerimaan Kas</span>
                    <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-600 border border-kp-blue-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-xl font-bold text-slate-900">
                    Rp {{ number_format($penerimaanKas, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-slate-400">Total kas & bank diterima</div>
            </div>

            <!-- Saldo Piutang Usaha -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Piutang Usaha</span>
                    <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-600 border border-kp-blue-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-xl font-bold text-slate-900">
                    Rp {{ number_format($piutangUsaha, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-slate-400">Saldo piutang berjalan aktif</div>
            </div>

            <!-- Estimasi Laba Operasional -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Estimasi Laba</span>
                    <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-600 border border-kp-blue-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-xl font-bold text-slate-900">
                    Rp {{ number_format($estimasiLabaBersih, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-slate-400">Margin laba operasional berjalan</div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 3. STATUS PROSES AKUNTANSI & KOMPOSISI PENDAPATAN               -->
        <!-- ============================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Komposisi Pendapatan Usaha (Revenue Channels) -->
            <div class="lg:col-span-7 bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Komposisi Pendapatan Usaha</h2>
                        <p class="text-xs text-slate-500">Porsi penjualan per saluran media pada {{ strtolower($rangeLabel) }}.</p>
                    </div>
                </div>

                <div class="mt-4 space-y-4">
                    @foreach($channels as $channel)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-bold text-slate-800">{{ $channel['name'] }} ({{ $channel['count'] }} Transaksi)</span>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-slate-900">Rp {{ number_format($channel['omzet'], 0, ',', '.') }}</span>
                                    <span class="text-slate-400 font-semibold">({{ $channel['persen'] }}%)</span>
                                </div>
                            </div>
                            <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full {{ $channel['color'] }} rounded-full transition-all duration-300" style="width: {{ $channel['persen'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Status Proses Harian & Bulanan (Anti-Dobel) -->
            <div class="lg:col-span-5 bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3.5">
                        <h2 class="text-sm font-bold text-slate-900">Status Proses Akuntansi</h2>
                        <span class="text-xs font-mono font-bold text-slate-600 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">
                            {{ date('d M Y') }}
                        </span>
                    </div>

                    <div class="space-y-3">
                        <!-- Hitung Penjualan Iklan Hari Ini -->
                        <div class="p-3 rounded-xl border {{ $statusHitungIklanHariIni ? 'bg-kp-blue-50/70 border-kp-blue-200' : 'bg-slate-50 border-slate-200' }} flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">Penjualan Iklan Hari Ini</span>
                                <span class="text-[11px] {{ $statusHitungIklanHariIni ? 'text-kp-blue-700' : 'text-slate-500' }}">
                                    {{ $statusHitungIklanHariIni ? 'Sudah dihitung ke jurnal umum' : 'Belum diproses hitung' }}
                                </span>
                            </div>
                            <a href="{{ route('accounting.iklan.hitung-penjualan') }}" 
                               class="h-8 px-3 rounded-lg text-xs font-bold flex items-center transition bg-kp-blue-600 text-white hover:bg-kp-blue-700">
                                {{ $statusHitungIklanHariIni ? 'Lihat' : 'Hitung' }}
                            </a>
                        </div>

                        <!-- Hitung Pembayaran Iklan Hari Ini -->
                        <div class="p-3 rounded-xl border {{ $isBayarDihitung ? 'bg-kp-blue-50/70 border-kp-blue-200' : 'bg-slate-50 border-slate-200' }} flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">Pembayaran Iklan Hari Ini</span>
                                <span class="text-[11px] {{ $isBayarDihitung ? 'text-kp-blue-700' : 'text-slate-500' }}">
                                    {{ $isBayarDihitung ? 'Sudah dibukukan ke penerimaan kas' : 'Belum diproses hitung' }}
                                </span>
                            </div>
                            <a href="{{ route('accounting.iklan.hitung-pembayaran') }}" 
                               class="h-8 px-3 rounded-lg text-xs font-bold flex items-center transition bg-kp-blue-600 text-white hover:bg-kp-blue-700">
                                {{ $isBayarDihitung ? 'Lihat' : 'Hitung' }}
                            </a>
                        </div>

                        <!-- Status Penjualan Sirkulasi -->
                        <div class="p-3 rounded-xl border {{ $statusHitungSirkulasiBulanIni ? 'bg-kp-blue-50/70 border-kp-blue-200' : 'bg-slate-50 border-slate-200' }} flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">Sirkulasi Bulan Ini</span>
                                <span class="text-[11px] {{ $statusHitungSirkulasiBulanIni ? 'text-kp-blue-700' : 'text-slate-500' }}">
                                    {{ $statusHitungSirkulasiBulanIni ? 'Telah ditutup & dibukukan' : 'Belum diproses hitung' }}
                                </span>
                            </div>
                            <a href="{{ route('accounting.sirkulasi.hitung-penjualan') }}" 
                               class="h-8 px-3 rounded-lg text-xs font-bold flex items-center transition bg-kp-blue-600 text-white hover:bg-kp-blue-700">
                                {{ $statusHitungSirkulasiBulanIni ? 'Lihat' : 'Hitung' }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 mt-2">
                    <span>Periode Buku: <strong class="text-slate-800">{{ $periodeAktif?->bulan_tahun ?? date('Y-m') }}</strong></span>
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-kp-blue-600"></span> Aktif
                    </span>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 4. TABEL JURNAL UMUM TERAKHIR (FULL-WIDTH PALET 60-30-10)       -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Jurnal Umum Terkini</h2>
                    <p class="text-xs text-slate-500">Daftar posting transaksi akuntansi terverifikasi terakhir.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('accounting.gl.jurnal') }}" 
                       class="h-9 px-3.5 bg-slate-50 hover:bg-slate-100 border border-slate-300 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <span>Lihat Semua Jurnal</span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] font-bold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. Jurnal</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Keterangan</th>
                            <th class="py-3 px-3.5">Sumber / Referensi</th>
                            <th class="py-3 px-3.5 text-right w-36">Total Nilai</th>
                            <th class="py-3 px-3.5 text-center w-28">Status</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($jurnalTerbaru as $key => $jurnal)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-400 font-normal">
                                    {{ $key + 1 }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    {{ $jurnal->no_jurnal }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                    {{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-3.5 max-w-sm truncate">
                                    <span class="font-bold text-slate-900 block truncate">{{ $jurnal->keterangan }}</span>
                                    <span class="text-xs text-slate-400 font-normal block">{{ count($jurnal->details ?? []) }} Rincian Akun</span>
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $jurnal->sumber ?? 'Memorial' }}
                                    </span>
                                    @if($jurnal->referensi)
                                        <span class="text-xs text-slate-400 ml-1">({{ $jurnal->referensi }})</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($jurnal->total_debet, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($jurnal->is_balanced)
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Seimbang
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            Tidak Seimbang
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
                                            
                                            <!-- Detail Jurnal -->
                                            <a href="{{ route('accounting.gl.jurnal', ['q' => $jurnal->no_jurnal]) }}" 
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail Jurnal</span>
                                            </a>

                                            <!-- Buku Besar Terkait -->
                                            <a href="{{ route('accounting.gl.buku-besar') }}" 
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                                <span>Buku Besar</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-slate-400 text-sm">
                                    Belum ada jurnal umum yang dibukukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
