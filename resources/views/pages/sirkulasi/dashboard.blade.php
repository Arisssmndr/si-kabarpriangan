<x-app-layout>
    @php
        $rasioReturPeriode = $totalKirimPeriode > 0 ? round(($totalRetur / $totalKirimPeriode) * 100, 1) : 0;
        $rasioSetoran = $totalOmset > 0 ? min(100, round(($totalBayarKasir / $totalOmset) * 100)) : 0;
        $maxWilayahOplah = collect($wilayahLeaderboard ?? [])->max('oplah_periode') ?: 1;
    @endphp

    <div class="space-y-5">

        <!-- Header Halaman Standar Divisi Iklan (Logo KP + Judul + Deskripsi + Filter Periode & Aksi) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 relative z-20">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Dashboard Sirkulasi
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Ringkasan distribusi koran, penjualan agen, setoran kasir, piutang, dan kalender terbit.
                    </p>
                </div>
            </div>

            <!-- Filter Periode (Popover Standar Selaras Dashboard Iklan) & Aksi Cepat -->
            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <div class="relative shrink-0"
                     x-data="{ open: false }"
                     @click.outside="open = false"
                     @keydown.escape.window="open = false">
                    <button type="button"
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
                                <a href="{{ route('sirkulasi.dashboard', ['range' => 'month']) }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
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
                                <a href="{{ route('sirkulasi.dashboard', ['range' => $key]) }}"
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
                            <form method="GET" action="{{ route('sirkulasi.dashboard') }}" class="space-y-2.5">
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

                <a href="{{ route('sirkulasi.jatah.edit') }}"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Input Jatah H+1</span>
                </a>
            </div>
        </div>

        <!-- Baris 1: 4 Kartu Ringkasan Penjualan, Setoran, Piutang & Oplah -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Estimasi Penjualan Koran -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Estimasi Penjualan</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalOmset, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    Hari ini: <span class="font-semibold text-slate-700 font-mono">Rp {{ number_format($penjualanHariIni ?? 0, 0, ',', '.') }}</span>
                </span>
            </div>

            <!-- 2. Setoran Masuk Kasir -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Setoran Kasir</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalBayarKasir, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    <span class="font-semibold text-kp-blue-700 font-mono">{{ number_format($jumlahSetoranKasir) }}</span> setoran ({{ $rasioSetoran }}% periode)
                </span>
            </div>

            <!-- 3. Sisa Piutang Agen -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Piutang Agen</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    <span class="font-semibold text-slate-700 font-mono">{{ number_format($jumlahAgenPiutang ?? 0) }}</span> agen memiliki tunggakan
                </span>
            </div>

            <!-- 4. Volume Terjual & Retur -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Oplah Terjual</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalTerjualPeriode) }} <span class="text-xs font-sans font-normal text-slate-500">Eks</span>
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    Retur: <span class="font-semibold text-slate-700 font-mono">{{ number_format($totalRetur) }}</span> eks ({{ $rasioReturPeriode }}%)
                </span>
            </div>
        </div>

        <!-- Baris 2: 4 Kotak Berjejer Rangkuman Master Data & Operasional -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Data Agen -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Data Agen</span>
                    <a href="{{ route('sirkulasi.tabel.agen') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
                        Lihat &rarr;
                    </a>
                </div>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalAgenAktif ?? 0) }} <span class="text-xs font-sans font-normal text-slate-500">Agen Aktif</span>
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    Dari total {{ number_format($totalAgen) }} mitra terdaftar
                </span>
            </div>

            <!-- 2. Wilayah Pemasaran -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Wilayah Pemasaran</span>
                    <a href="{{ route('sirkulasi.tabel.wilayah') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
                        Lihat &rarr;
                    </a>
                </div>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalWilayah ?? 0) }} <span class="text-xs font-sans font-normal text-slate-500">Zona</span>
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    Oplah hari ini: <span class="font-semibold text-slate-700 font-mono">{{ number_format($oplahHarian, 0, ',', '.') }}</span> eks
                </span>
            </div>

            <!-- 3. Rute Armada Subuh -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jurusan Armada</span>
                    <a href="{{ route('sirkulasi.cetak.nota-distribusi') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
                        Lihat &rarr;
                    </a>
                </div>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalJurusan ?? 0) }} <span class="text-xs font-sans font-normal text-slate-500">Rute</span>
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    Efektivitas serapan <span class="font-semibold text-kp-blue-700 font-mono">{{ $efektivitasDistribusi }}%</span>
                </span>
            </div>

            <!-- 4. Tutup Data & Faktur Kasir -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Closing &amp; Faktur</span>
                    <a href="{{ route('sirkulasi.jatah.tutup') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
                        Lihat &rarr;
                    </a>
                </div>
                <p class="text-2xl font-bold {{ $isTodayClosed ? 'text-kp-blue-700' : 'text-slate-900' }} mt-1 font-mono tabular-nums">
                    {{ $isTodayClosed ? 'Terkunci' : 'Terbuka' }}
                </p>
                <span class="text-xs text-slate-500 mt-1 block">
                    Status edisi {{ $today->translatedFormat('d M Y') }}
                </span>
            </div>
        </div>

        <!-- Baris 3: Distribusi per Kategori Keagenan (Agen Besar 11, Sub Agen 99, Gratis/Insidentil) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($channels as $ch)
                <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <h3 class="text-sm font-bold text-slate-900 truncate">{{ $ch['nama'] }}</h3>
                                <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200 font-mono shrink-0">
                                    {{ $ch['count'] }} {{ $ch['unit'] }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2.5 shrink-0">
                                <a href="{{ route($ch['route_new']) }}"
                                   class="text-xs font-semibold text-slate-600 hover:text-kp-blue-700 transition">
                                    + Kelola
                                </a>
                                <a href="{{ $ch['route_tx'] }}"
                                   class="text-xs font-semibold text-kp-blue-700 hover:underline">
                                    Data &rarr;
                                </a>
                            </div>
                        </div>

                        <div class="py-3.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    {{ $ch['omzet'] > 0 ? 'Nilai Penjualan' : 'Alokasi Distribusi' }}
                                </span>
                                <span class="text-xs font-mono font-bold text-kp-blue-700">{{ $ch['percent'] }}% porsi</span>
                            </div>
                            <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums tracking-tight mt-1 whitespace-nowrap">
                                @if($ch['omzet'] > 0)
                                    Rp {{ number_format($ch['omzet'], 0, ',', '.') }}
                                @else
                                    {{ number_format($ch['kirim_eks'], 0, ',', '.') }} Eks
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-xs font-normal text-slate-500 block">Total Kirim</span>
                                <span class="text-sm font-semibold text-slate-900 font-mono tabular-nums block mt-0.5 whitespace-nowrap">
                                    {{ number_format($ch['kirim_eks'], 0, ',', '.') }} eks
                                </span>
                            </div>
                            <div>
                                <span class="text-xs font-normal text-slate-500 block">Saldo Piutang</span>
                                <span class="text-sm font-normal {{ $ch['piutang'] > 0 ? 'text-slate-700' : 'text-slate-400' }} font-mono tabular-nums block mt-0.5 whitespace-nowrap">
                                    Rp {{ number_format($ch['piutang'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-100 text-xs text-slate-500 flex items-center justify-between">
                            <span>{!! $ch['info_ops'] !!}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Baris 4: Grafik Tren Penjualan Sirkulasi 7 Hari (8 Kolom) & Kalender Terbit (4 Kolom) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">

            <!-- Kolom Utama (8 Kolom): Grafik Penjualan 7 Hari Terakhir -->
            <div class="lg:col-span-8 bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3.5 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Grafik Penjualan Koran 7 Hari Terakhir</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Nilai estimasi penjualan harian dari Agen Besar (11) dan Sub Agen (99)</p>
                    </div>
                    <div class="flex items-center flex-wrap gap-4 text-xs font-medium text-slate-700">
                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap shrink-0">
                            <span class="w-2.5 h-2.5 rounded-xs bg-kp-blue-700 shrink-0"></span> Agen Besar (11)
                        </span>
                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap shrink-0">
                            <span class="w-2.5 h-2.5 rounded-xs bg-kp-blue-400 shrink-0"></span> Sub Agen (99)
                        </span>
                    </div>
                </div>

                <div class="h-80 w-full relative">
                    <canvas id="sirkulasiTrendChart"></canvas>
                </div>
            </div>

            <!-- Kolom Samping (4 Kolom): Kalender Terbit & Hari Penting (Identik Dashboard Iklan) -->
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

        <!-- Baris 5: Performa Wilayah Distribusi (6 Kolom) & Komposisi Porsi Jatah (6 Kolom) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch">

            <!-- Kiri (6 Kolom): Performa Wilayah Pemasaran -->
            <div class="lg:col-span-6 bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4 flex flex-col justify-between">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 gap-2">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Performa Wilayah Pemasaran</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Serapan oplah distribusi periode {{ strtolower($rangeTitle) }}</p>
                    </div>
                    <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'rekap-wilayah']) }}" class="text-xs font-semibold text-kp-blue-700 hover:underline shrink-0">
                        Rekap Wilayah &rarr;
                    </a>
                </div>

                <div class="space-y-3.5">
                    @foreach($wilayahLeaderboard as $wil)
                        @php
                            $wPct = $maxWilayahOplah > 0 ? round(($wil->oplah_periode / $maxWilayahOplah) * 100) : 0;
                        @endphp
                        <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-200/70 hover:bg-slate-50 transition space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-6 h-6 rounded-lg bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ $wil->kode }}
                                    </span>
                                    <span class="font-bold text-slate-900 truncate">{{ $wil->nama }}</span>
                                    <span class="text-slate-400 font-mono text-[11px]">({{ $wil->total_agen }} titik)</span>
                                </div>
                                <span class="font-mono font-bold text-kp-blue-700 shrink-0">
                                    {{ number_format($wil->oplah_periode, 0, ',', '.') }} eks
                                </span>
                            </div>

                            <div class="w-full bg-slate-200/70 rounded-full h-2 overflow-hidden">
                                <div class="bg-kp-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ max(4, $wPct) }}%"></div>
                            </div>

                            <div class="flex items-center justify-between text-[11px] text-slate-500 pt-0.5">
                                <span>Harian: <strong class="text-slate-700 font-mono">{{ number_format($wil->alokasi_harian, 0, ',', '.') }} eks</strong></span>
                                <span>Estimasi Omset: <strong class="text-slate-700 font-mono">Rp {{ number_format($wil->estimasi_omset, 0, ',', '.') }}</strong></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Kanan (6 Kolom): Komposisi Porsi Saluran Jatah Koran (Donut Chart) -->
            <div class="lg:col-span-6 bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4 flex flex-col justify-between">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Komposisi Distribusi Koran</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Porsi alokasi periode {{ strtolower($rangeTitle) }}</p>
                    </div>
                    <span class="text-xs font-mono font-bold text-kp-blue-700">
                        100% Total
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                    <div class="sm:col-span-6 h-48 relative flex items-center justify-center">
                        <canvas id="sirkulasiShareChart"></canvas>
                    </div>

                    <div class="sm:col-span-6 space-y-2.5">
                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-800 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-kp-blue-700 inline-block"></span> Langganan
                                </span>
                                <span class="font-bold text-kp-blue-700 font-mono">{{ $komposisiDistribusi['langganan']['pct'] ?? 0 }}%</span>
                            </div>
                            <div class="text-xs font-mono text-slate-600 mt-0.5">
                                {{ number_format($komposisiDistribusi['langganan']['eks'] ?? 0, 0, ',', '.') }} Eks
                            </div>
                        </div>

                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-800 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-kp-blue-400 inline-block"></span> Konsinyasi / Eceran
                                </span>
                                <span class="font-bold text-kp-blue-600 font-mono">{{ $komposisiDistribusi['konsinyasi']['pct'] ?? 0 }}%</span>
                            </div>
                            <div class="text-xs font-mono text-slate-600 mt-0.5">
                                {{ number_format($komposisiDistribusi['konsinyasi']['eks'] ?? 0, 0, ',', '.') }} Eks
                            </div>
                        </div>

                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-800 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-slate-400 inline-block"></span> Gratis &amp; Insidentil
                                </span>
                                <span class="font-bold text-slate-600 font-mono">{{ $komposisiDistribusi['gratis']['pct'] ?? 0 }}%</span>
                            </div>
                            <div class="text-xs font-mono text-slate-600 mt-0.5">
                                {{ number_format($komposisiDistribusi['gratis']['eks'] ?? 0, 0, ',', '.') }} Eks
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-[11px] text-slate-400 pt-2 border-t border-slate-100 flex items-center justify-between">
                    <span>Total Estimasi Penjualan:</span>
                    <span class="font-bold font-mono text-slate-700">Rp {{ number_format($totalOmset, 0, ',', '.') }}</span>
                </div>
            </div>

        </div>

        <!-- Baris 6: Tabel Ringkas Faktur & Distribusi Agen (Standar Tabel Divisi Iklan 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 py-3.5 border-b border-slate-200 flex items-center justify-between gap-2 bg-white">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Rekap Distribusi &amp; Penjualan Agen</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Ringkasan pengiriman dan penjualan koran agen periode {{ strtolower($rangeTitle) }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">
                        {{ $recentFaktur->count() }} Agen
                    </span>
                    <a href="{{ route('sirkulasi.cetak.penjualan') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
                        Lihat Laporan Penjualan &rarr;
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto min-h-[260px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5">No. Referensi</th>
                            <th class="py-3 px-3.5">Kode Agen</th>
                            <th class="py-3 px-3.5">Nama Agen</th>
                            <th class="py-3 px-3.5">Wilayah</th>
                            <th class="py-3 px-3.5 text-right">Kirim (Eks)</th>
                            <th class="py-3 px-3.5 text-right">Terjual</th>
                            <th class="py-3 px-3.5 text-right w-36">Estimasi Penjualan</th>
                            <th class="py-3 px-3.5 text-right w-32">Saldo Piutang</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($recentFaktur as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    {{ $item->no_faktur }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-slate-700 font-medium">
                                    {{ $item->kode_agen }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    {{ $item->nama_agen }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                    {{ $item->wilayah_nama }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">
                                    {{ number_format($item->kirim, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">
                                    {{ number_format($item->nett, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($item->tagihan, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    @if($item->saldo_piutang > 0)
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-normal text-slate-700 tabular-nums">{{ number_format($item->saldo_piutang, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <div class="text-right font-mono text-sm text-slate-400 font-normal">
                                            Rp 0
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($item->status === 'Lunas')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Lunas
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            Piutang
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

                                            <a href="{{ route('sirkulasi.cetak.nota-distribusi', ['jurusan' => $item->jurusan_kode]) }}"
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span>Surat Jalan Rute</span>
                                            </a>

                                            <a href="{{ route('sirkulasi.tabel.agen', ['search' => $item->kode_agen]) }}"
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>Lihat Data Agen</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-10 text-center text-sm text-slate-500">
                                    Belum ada data agen distribusi tercatat.
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
            // 1. Grafik Tren Penjualan Koran 7 Hari Terakhir
            const ctxTrend = document.getElementById('sirkulasiTrendChart');
            if (ctxTrend) {
                const labels = @json($dailyLabels ?? []);
                const dataBesar = @json($dailyBesar ?? []);
                const dataSub = @json($dailySub ?? []);

                new Chart(ctxTrend, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Agen Besar (11)',
                                data: dataBesar,
                                backgroundColor: '#0A72AC',
                                borderRadius: 4,
                                barPercentage: 0.6,
                            },
                            {
                                label: 'Sub Agen (99)',
                                data: dataSub,
                                backgroundColor: '#64B0E5',
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

            // 2. Grafik Komposisi Porsi Distribusi Koran (Doughnut Chart)
            const ctxShare = document.getElementById('sirkulasiShareChart');
            if (ctxShare) {
                const eksLangganan = {{ (int) ($komposisiDistribusi['langganan']['eks'] ?? 0) }};
                const eksKonsinyasi = {{ (int) ($komposisiDistribusi['konsinyasi']['eks'] ?? 0) }};
                const eksGratis = {{ (int) ($komposisiDistribusi['gratis']['eks'] ?? 0) }};

                new Chart(ctxShare, {
                    type: 'doughnut',
                    data: {
                        labels: ['Langganan', 'Konsinyasi / Eceran', 'Gratis & Insidentil'],
                        datasets: [{
                            data: [eksLangganan, eksKonsinyasi, eksGratis],
                            backgroundColor: ['#0A72AC', '#64B0E5', '#94a3b8'],
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
                                        return c.label + ': ' + new Intl.NumberFormat('id-ID').format(val) + ' eks';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            initDashCalendar();
        });

        // 3. Kalender Terbit & Hari Penting (Selaras Dashboard Divisi Iklan)
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
                    cell.className += ' bg-slate-200 text-slate-700 hover:bg-slate-300 font-medium';
                } else if (isMemorial) {
                    cell.className += ' bg-kp-blue-50/70 text-kp-blue-700 border border-kp-blue-200 hover:bg-kp-blue-100 font-medium';
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
                eventInfo += `<div class="text-slate-900 font-bold mt-1">&bull; ${liburText}</div>`;
            }
            if (peringatanMap[dateStr]) {
                const eventText = typeof peringatanMap[dateStr] === 'string' ? peringatanMap[dateStr] : (peringatanMap[dateStr].nama || 'Hari Peringatan');
                eventInfo += `<div class="text-kp-blue-700 font-medium mt-1">&bull; ${eventText}</div>`;
            }

            panel.innerHTML = `
                <div class="font-bold text-slate-900">${formatted}</div>
                ${eventInfo || '<div class="text-slate-500 mt-0.5">Hari kerja operasional cetak &amp; distribusi normal.</div>'}
            `;
        }
    </script>
    @endpush
</x-app-layout>
