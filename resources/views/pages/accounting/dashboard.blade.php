<x-app-layout>
    @php
        $rasioKasMasuk = $omsetUsaha > 0 ? round(($penerimaanKas / $omsetUsaha) * 100) : 0;
        $marginLaba = $omsetUsaha > 0 ? round(($estimasiLabaBersih / $omsetUsaha) * 100, 1) : 0;
        $totalBebanKategori = collect($bebanChartValues ?? [])->sum();
    @endphp

    <div class="space-y-5">

        <!-- Header Halaman Standar (Palet 60-30-10, Selaras Dashboard Iklan) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 relative z-20">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Dashboard Akuntansi
                        </h1>
                        @if($periodeAktif && $periodeAktif->status === 'terkunci')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                Periode {{ $periodeAktif->periode_key }} Terkunci
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                Pembukuan Aktif
                            </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Ringkasan keuangan terintegrasi lintas divisi Iklan (Koran, Online, TV), Sirkulasi, Kas/Beban, dan General Ledger.
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
                    <svg :class="{ 'rotate-180': open }"
                         class="w-3.5 h-3.5 text-slate-400 transition-transform duration-150 shrink-0"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Popover Panel -->
                <div x-cloak
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
                                <span>Tampilkan</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Baris 1: 4 Kartu Kinerja Keuangan Utama -->
        @if(($unpostedSummary['total_pending_items'] ?? 0) > 0)
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 shadow-2xs">
                <div class="text-xs text-amber-900">
                    <span class="font-bold text-sm block">Sub-Ledger Belum Masuk General Ledger ({{ $unpostedSummary['periode'] ?? date('Y-m') }})</span>
                    Ada <strong>{{ $unpostedSummary['iklan_unpulled'] }}</strong> transaksi iklan,
                    <strong>{{ $unpostedSummary['kas_masuk_iklan_unpulled'] + $unpostedSummary['kas_masuk_sirkulasi_unpulled'] }}</strong> kas masuk,
                    <strong>{{ $unpostedSummary['beban_unpulled_count'] }}</strong> BKK kasir, dan
                    <strong>{{ $unpostedSummary['draft_jurnal_count'] }}</strong> jurnal draft menunggu finalisasi ke GL.
                </div>
                <div class="flex items-center gap-2 shrink-0 flex-wrap">
                    <a href="{{ route('accounting.iklan.hitung-penjualan') }}" class="px-3 py-1.5 bg-white border border-amber-300 text-amber-900 rounded-lg text-xs font-semibold hover:bg-amber-100">Hitung Iklan</a>
                    <a href="{{ route('accounting.beban.verifikasi') }}" class="px-3 py-1.5 bg-white border border-amber-300 text-amber-900 rounded-lg text-xs font-semibold hover:bg-amber-100">Verifikasi Beban</a>
                    <a href="{{ route('accounting.gl.jurnal', ['status' => 'draft']) }}" class="px-3 py-1.5 bg-white border border-amber-300 text-amber-900 rounded-lg text-xs font-semibold hover:bg-amber-100">Review Draft</a>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Total Pendapatan Usaha -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pendapatan Usaha</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($omsetUsaha, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    Retur/Koreksi: <span class="font-semibold text-slate-700 font-mono">Rp {{ number_format($totalRetur, 0, ',', '.') }}</span>
                </span>
            </div>

            <!-- 2. Penerimaan Kas Masuk -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Penerimaan Kas Masuk</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($penerimaanKas, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    Realisasi kas: <span class="font-semibold text-kp-blue-700 font-mono">{{ $rasioKasMasuk }}%</span> terhadap omset
                </span>
            </div>

            <!-- 3. Piutang Usaha Aktif -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Piutang Usaha</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($piutangUsaha, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    <span class="font-semibold text-slate-700 font-mono">{{ number_format($fakturBelumLunasCount) }}</span> faktur &amp; agen aktif
                </span>
            </div>

            <!-- 4. Laba Operasional Periode -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Laba Operasional Riil</span>
                <p class="text-2xl font-bold {{ $estimasiLabaBersih >= 0 ? 'text-kp-blue-700' : 'text-rose-700' }} mt-1 font-mono tabular-nums">
                    Rp {{ number_format($estimasiLabaBersih, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    Beban riil: <span class="font-semibold text-slate-700 font-mono">Rp {{ number_format($totalBebanRiil, 0, ',', '.') }}</span> &bull; Margin {{ $marginLaba }}%
                </span>
            </div>
        </div>

        <!-- Baris 2: 4 Kartu Kontrol & Kesiapan Tutup Buku -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Kalkulasi Iklan -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kalkulasi Iklan</span>
                    <a href="{{ route('accounting.iklan.hitung-penjualan') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
                        Buka &rarr;
                    </a>
                </div>
                <p class="text-lg font-bold mt-1.5">
                    @if($statusHitungIklanHariIni)
                        <span class="text-kp-blue-700">Sudah Dihitung</span>
                    @else
                        <span class="text-slate-700">Belum Dihitung</span>
                    @endif
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    Edisi hari ini ({{ \Carbon\Carbon::parse($today)->translatedFormat('d M Y') }})
                </span>
            </div>

            <!-- 2. Kalkulasi Sirkulasi -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kalkulasi Sirkulasi</span>
                    <a href="{{ route('accounting.sirkulasi.hitung-penjualan') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
                        Buka &rarr;
                    </a>
                </div>
                <p class="text-lg font-bold mt-1.5">
                    @if($statusHitungSirkulasiBulanIni)
                        <span class="text-kp-blue-700">Sudah Dihitung</span>
                    @else
                        <span class="text-slate-700">Menunggu Rekonsiliasi</span>
                    @endif
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    @if($memoPendingCount > 0)
                        <span class="font-semibold text-slate-800">{{ $memoPendingCount }}</span> permintaan buka kunci tertunda
                    @else
                        Tidak ada antrean buka kunci jatah
                    @endif
                </span>
            </div>

            <!-- 3. Verifikasi Beban Kasir -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Verifikasi Beban</span>
                    <a href="{{ route('accounting.beban.verifikasi') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
                        Posting &rarr;
                    </a>
                </div>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($bebanBelumPostingCount) }} <span class="text-xs font-sans font-normal text-slate-500">BKK Pending</span>
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    Nominal belum GL: <span class="font-semibold text-slate-700 font-mono">Rp {{ number_format($bebanBelumPostingNominal, 0, ',', '.') }}</span>
                </span>
            </div>

            <!-- 4. Aktivitas Jurnal GL -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">General Ledger (GL)</span>
                    <a href="{{ route('accounting.gl.jurnal') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
                        Lihat &rarr;
                    </a>
                </div>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    {{ number_format($jurnalPeriodeCount) }} <span class="text-xs font-sans font-normal text-slate-500">Jurnal</span>
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    Total mutasi: <span class="font-semibold text-slate-700 font-mono">Rp {{ number_format($jurnalPeriodeNominal, 0, ',', '.') }}</span>
                </span>
            </div>
        </div>

        <!-- Baris 3: Ringkasan Keuangan per Unit Usaha (4 Saluran: Koran, Online, TV, Sirkulasi) -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            @foreach($channels as $ch)
                <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <h3 class="text-sm font-bold text-slate-900 truncate">{{ $ch['nama'] }}</h3>
                                <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200 font-mono shrink-0">
                                    {{ $ch['count'] }}
                                </span>
                            </div>
                            <a href="{{ $ch['route_go'] }}" class="text-xs font-semibold text-kp-blue-700 hover:underline shrink-0">
                                Proses &rarr;
                            </a>
                        </div>

                        <div class="py-3.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pendapatan</span>
                                <span class="text-xs font-mono font-bold text-kp-blue-700">{{ $ch['percent'] }}% porsi</span>
                            </div>
                            <div class="text-xl font-bold text-kp-blue-700 font-mono tabular-nums tracking-tight mt-1 whitespace-nowrap">
                                Rp {{ number_format($ch['omzet'], 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-3">
                        <div>
                            <span class="text-xs font-normal text-slate-500 block">Terbayar</span>
                            <span class="text-sm font-semibold text-slate-900 font-mono tabular-nums block mt-0.5 whitespace-nowrap">
                                Rp {{ number_format($ch['bayar'], 0, ',', '.') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-xs font-normal text-slate-500 block">Piutang</span>
                            <span class="text-sm font-normal {{ $ch['piutang'] > 0 ? 'text-slate-700 font-semibold' : 'text-slate-400' }} font-mono tabular-nums block mt-0.5 whitespace-nowrap">
                                Rp {{ number_format($ch['piutang'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Baris 4: Grafik Arus Keuangan 7 Hari Terakhir (8 Kolom) & Kalender Operasional (4 Kolom) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">

            <!-- Kolom Utama (8 Kolom): Grafik Batang Arus Keuangan 7 Hari -->
            <div class="lg:col-span-8 bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3.5 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Grafik Arus Keuangan 7 Hari Terakhir</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Perbandingan harian omset Iklan, Sirkulasi, Kas Masuk, dan Beban Keluar</p>
                    </div>
                    <div class="flex items-center flex-wrap gap-3 text-xs font-medium text-slate-700">
                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap shrink-0">
                            <span class="w-2.5 h-2.5 rounded-xs bg-kp-blue-700 shrink-0"></span> Iklan
                        </span>
                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap shrink-0">
                            <span class="w-2.5 h-2.5 rounded-xs bg-kp-blue-300 shrink-0"></span> Sirkulasi
                        </span>
                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap shrink-0">
                            <span class="w-2.5 h-2.5 rounded-xs bg-kp-blue-600 shrink-0"></span> Kas Masuk
                        </span>
                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap shrink-0">
                            <span class="w-2.5 h-2.5 rounded-xs bg-slate-500 shrink-0"></span> Beban
                        </span>
                    </div>
                </div>

                <div class="h-80 w-full relative">
                    <canvas id="accountingFlowChart"></canvas>
                </div>
            </div>

            <!-- Kolom Samping (4 Kolom): Kalender Operasional Akuntansi -->
            <div class="lg:col-span-4 bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-3.5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Kalender Pembukuan</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Jadwal operasional &amp; hari besar nasional</p>
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

        <!-- Baris 5: Komposisi Pendapatan Divisi (Doughnut Chart Bersih Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Komposisi Pendapatan Perusahaan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Porsi Iklan Koran, Online, Priangan TV, dan Sirkulasi</p>
                </div>
                <a href="{{ route('accounting.laporan.laba-rugi') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline shrink-0">
                    Laba Rugi &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center py-2">
                <div class="md:col-span-5 h-52 relative flex items-center justify-center">
                    <canvas id="revenueShareChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-xs font-medium text-slate-500">Total Omset</span>
                        <span class="text-sm font-bold text-slate-900 font-mono">
                            {{ number_format($omsetUsaha / 1000000, 1, ',', '.') }} Jt
                        </span>
                    </div>
                </div>

                <div class="md:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    @php
                        $dotColors = [
                            'koran'     => 'bg-kp-blue-700',
                            'online'    => 'bg-kp-blue-300',
                            'tv'        => 'bg-slate-400',
                            'sirkulasi' => 'bg-kp-blue-600',
                        ];
                    @endphp
                    @foreach($channels as $key => $ch)
                        <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-200/80 flex flex-col justify-between space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-2.5 h-2.5 rounded-xs {{ $dotColors[$key] ?? 'bg-slate-400' }} shrink-0"></span>
                                    <span class="font-semibold text-slate-800 truncate">{{ $ch['nama'] }}</span>
                                </div>
                                <span class="font-bold text-slate-900 font-mono">{{ $ch['percent'] }}%</span>
                            </div>
                            <div class="text-xs font-mono text-kp-blue-700 font-bold">
                                Rp {{ number_format($ch['omzet'], 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Laba Kotor (Gross Margin)</span>
                <span class="font-bold text-slate-900 font-mono tabular-nums">
                    Rp {{ number_format($labaKotor, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Baris 6: Tabel Jurnal Umum Terbaru (10 Baris) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Jurnal Umum Terbaru</h2>
                    <p class="text-xs text-slate-500 mt-0.5">10 entri posting General Ledger terakhir dari seluruh modul</p>
                </div>
                <a href="{{ route('accounting.gl.jurnal') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline shrink-0">
                    Semua Jurnal &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">No. Jurnal</th>
                            <th class="py-3 px-4">Sumber</th>
                            <th class="py-3 px-4">Referensi &amp; Keterangan</th>
                            <th class="py-3 px-4 text-right">Debet (Rp)</th>
                            <th class="py-3 px-4 text-right">Kredit (Rp)</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($jurnalTerbaru as $jur)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-4 text-slate-600 font-mono text-xs whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($jur->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 font-semibold text-kp-blue-700 font-mono text-xs whitespace-nowrap">
                                    {{ $jur->no_jurnal }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200 uppercase">
                                        {{ $jur->sumber }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-slate-900 line-clamp-1">{{ $jur->keterangan }}</div>
                                    <div class="text-xs text-slate-400 font-mono">{{ $jur->referensi }}</div>
                                </td>
                                <td class="py-3 px-4 text-right font-mono tabular-nums text-slate-900 font-semibold whitespace-nowrap">
                                    {{ number_format($jur->total_debet, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono tabular-nums text-slate-900 font-semibold whitespace-nowrap">
                                    {{ number_format($jur->total_kredit, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium {{ $jur->status === 'posted' ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }} uppercase">
                                        {{ $jur->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-xs text-slate-500">
                                    Belum ada jurnal umum yang diposting ke General Ledger.
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
            // 1. GRAFIK ARUS KEUANGAN 7 HARI TERAKHIR (GROUPED BAR CHART)
            // ==============================================================
            const ctxFlow = document.getElementById('accountingFlowChart');
            if (ctxFlow) {
                new Chart(ctxFlow, {
                    type: 'bar',
                    data: {
                        labels: @json($dailyLabels ?? []),
                        datasets: [
                            {
                                label: 'Pendapatan Iklan',
                                data: @json($dailyIklan ?? []),
                                backgroundColor: '#005792',
                                borderRadius: 4,
                                barPercentage: 0.7,
                            },
                            {
                                label: 'Pendapatan Sirkulasi',
                                data: @json($dailySirkulasi ?? []),
                                backgroundColor: '#38bdf8',
                                borderRadius: 4,
                                barPercentage: 0.7,
                            },
                            {
                                label: 'Kas Masuk',
                                data: @json($dailyKasMasuk ?? []),
                                backgroundColor: '#0A72AC',
                                borderRadius: 4,
                                barPercentage: 0.7,
                            },
                            {
                                label: 'Beban Keluar',
                                data: @json($dailyBeban ?? []),
                                backgroundColor: '#64748b',
                                borderRadius: 4,
                                barPercentage: 0.7,
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
                                        if (value >= 1000000) return (value / 1000000) + ' Jt';
                                        if (value >= 1000) return (value / 1000) + ' Rb';
                                        return value;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // ==============================================================
            // 2. GRAFIK KOMPOSISI PENDAPATAN LINTAS DIVISI (DOUGHNUT CHART)
            // ==============================================================
            const ctxShare = document.getElementById('revenueShareChart');
            if (ctxShare) {
                const omzKoran = {{ (int) ($channels['koran']['omzet'] ?? 0) }};
                const omzOnline = {{ (int) ($channels['online']['omzet'] ?? 0) }};
                const omzTv = {{ (int) ($channels['tv']['omzet'] ?? 0) }};
                const omzSirkulasi = {{ (int) ($channels['sirkulasi']['omzet'] ?? 0) }};

                new Chart(ctxShare, {
                    type: 'doughnut',
                    data: {
                        labels: ['Iklan Koran', 'Iklan Online', 'Priangan TV', 'Sirkulasi Koran'],
                        datasets: [{
                            data: [omzKoran, omzOnline, omzTv, omzSirkulasi],
                            backgroundColor: ['#005792', '#38bdf8', '#94a3b8', '#0A72AC'],
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
        // 4. KALENDER OPERASIONAL & HARI PENTING
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
            .catch(() => {
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
                    cell.className += ' bg-kp-blue-50 text-kp-blue-800 hover:bg-kp-blue-100 font-medium';
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
                eventInfo += `<div class="text-kp-blue-700 font-medium mt-1">&bull; ${eventText}</div>`;
            }

            panel.innerHTML = `
                <div class="font-bold text-slate-900">${formatted}</div>
                ${eventInfo || '<div class="text-slate-500 mt-0.5">Hari kerja operasional &amp; pembukuan normal.</div>'}
            `;
        }
    </script>
    @endpush
</x-app-layout>
