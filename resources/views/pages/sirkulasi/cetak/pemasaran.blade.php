<x-app-layout>
    <style>
        @media print {
            header,
            footer,
            nav,
            aside,
            .print\:hidden,
            .no-print {
                display: none !important;
            }

            body, html, main {
                background: #ffffff !important;
                background-color: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .printable-area {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            th, td {
                border: 1px solid #cbd5e1 !important;
            }

            tr {
                page-break-inside: avoid;
            }

            @page {
                size: A4 landscape;
                margin: 8mm 10mm 10mm 10mm;
            }
        }
    </style>

    @php
        $tabsPemasaran = [
            'agen-besar'         => ['label' => 'Agen Besar', 'full' => 'Laporan Agen Besar'],
            'sub-agen'           => ['label' => 'Sub Agen', 'full' => 'Laporan Sub Agen & Kios'],
            'rekap-terbit-bulan' => ['label' => 'Rekap Harian', 'full' => 'Rekap Penjualan Harian'],
            'rekap-wilayah'      => ['label' => 'Per Wilayah', 'full' => 'Laporan Penjualan per Wilayah'],
            'rekap-jurusan'      => ['label' => 'Per Jurusan', 'full' => 'Laporan per Rute Jurusan'],
            'penerimaan-koran'   => ['label' => 'Penerimaan Koran', 'full' => 'Laporan Penerimaan Koran'],
            'oplah-tahun'        => ['label' => 'Oplah Tahunan', 'full' => 'Laporan Penjualan Koran Tahunan'],
            'rekap-bln-wilayah'  => ['label' => 'Bulanan Wilayah', 'full' => 'Laporan Bulanan per Wilayah'],
        ];
        $activeMeta = $tabsPemasaran[$tab] ?? ['label' => 'Laporan Pemasaran', 'full' => 'Laporan Pemasaran Sirkulasi'];
        $activeQuery = request()->except(['tab']);
    @endphp

    <div class="space-y-4">

        <!-- 1. Unified Command Header + Minimalist Underline Tabs (1 Baris Penuh Tanpa Clipping) -->
        <div class="print:hidden bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
            <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5 min-w-0">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 w-auto object-contain shrink-0">
                    <div class="min-w-0">
                        <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight truncate">
                            {{ $activeMeta['full'] }}
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate">
                            {{ $periodeLabel }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('sirkulasi.cetak.pemasaran.print', request()->query()) }}"
                   target="_blank"
                   class="inline-flex items-center justify-center gap-2 h-10 px-4 bg-[#0E527B] hover:bg-[#07334F] text-white text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Laporan Pemasaran</span>
                </a>
            </div>

            <!-- Baris Navigasi Laporan: Compact Pill Buttons (Jelas Terlihat Bisa Diklik & Tanpa Terpotong) -->
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/70 flex flex-wrap items-center gap-2">
                @foreach($tabsPemasaran as $tabKey => $tabMeta)
                    @php
                        $isActiveTab = ($tab === $tabKey);
                        $tabUrl = route('sirkulasi.cetak.pemasaran', array_merge($activeQuery, ['tab' => $tabKey]));
                    @endphp
                    <a href="{{ $tabUrl }}"
                       class="inline-flex items-center h-9 px-3.5 rounded-lg text-xs sm:text-[13px] border whitespace-nowrap transition-all duration-150 {{ $isActiveTab ? 'bg-[#0E527B] border-[#0E527B] text-white font-semibold shadow-xs' : 'bg-white border-slate-200 text-slate-700 hover:border-[#0E527B]/50 hover:text-[#0E527B] hover:bg-sky-50/50 font-medium shadow-2xs' }}">
                        {{ $tabMeta['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- 2. Filter Bar Simpel & Segmented Control Modern -->
        <div class="print:hidden bg-white border border-slate-200/90 rounded-2xl p-4 shadow-xs"
             x-data="{ mode: '{{ $modePeriode }}' }">
            <form method="GET" action="{{ route('sirkulasi.cetak.pemasaran') }}" class="space-y-3.5">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="hidden" name="mode_periode" value="{{ $modePeriode }}" :value="mode">

                <!-- Segmented Control Mode Periode -->
                <div class="flex flex-wrap items-center justify-between gap-2.5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Mode Periode:</span>
                        <div class="inline-flex flex-wrap items-center bg-slate-100/90 p-1 rounded-xl gap-0.5">
                            <button type="button" @click="mode = 'harian'"
                                    :class="mode === 'harian' ? 'bg-[#0E527B] text-white font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                    class="h-8 px-3 rounded-lg text-xs sm:text-sm transition cursor-pointer">
                                Harian
                            </button>
                            <button type="button" @click="mode = 'mingguan'"
                                    :class="mode === 'mingguan' ? 'bg-[#0E527B] text-white font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                    class="h-8 px-3 rounded-lg text-xs sm:text-sm transition cursor-pointer">
                                Mingguan
                            </button>
                            <button type="button" @click="mode = 'bulanan'"
                                    :class="mode === 'bulanan' ? 'bg-[#0E527B] text-white font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                    class="h-8 px-3 rounded-lg text-xs sm:text-sm transition cursor-pointer">
                                Bulanan
                            </button>
                            <button type="button" @click="mode = 'tahunan'"
                                    :class="mode === 'tahunan' ? 'bg-[#0E527B] text-white font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                    class="h-8 px-3 rounded-lg text-xs sm:text-sm transition cursor-pointer">
                                Tahunan
                            </button>
                            <button type="button" @click="mode = 'custom'"
                                    :class="mode === 'custom' ? 'bg-[#0E527B] text-white font-semibold shadow-xs' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                    class="h-8 px-3 rounded-lg text-xs sm:text-sm transition cursor-pointer">
                                Rentang
                            </button>
                        </div>
                    </div>

                    <span class="text-xs font-semibold text-[#0E527B] bg-sky-50 border border-sky-100 px-3 py-1 rounded-full">
                        {{ $periodeLabel }}
                    </span>
                </div>

                <!-- Baris 2: Input Periode + Filter Wilayah + Jurusan + Pencarian -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">

                    <!-- Input Harian / Mingguan -->
                    <div class="sm:col-span-3" x-show="mode === 'harian' || mode === 'mingguan'" x-cloak>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" x-text="mode === 'harian' ? 'Tanggal Terbit' : 'Tanggal Akhir Pekan (7 Hari)'"></label>
                        <input type="date" name="tanggal" value="{{ $tanggal }}"
                               :disabled="mode !== 'harian' && mode !== 'mingguan'"
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    <!-- Input Bulanan -->
                    <div class="sm:col-span-2" x-show="mode === 'bulanan'" x-cloak>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bulan</label>
                        <select name="bulan" :disabled="mode !== 'bulanan'"
                                class="w-full h-11 pl-3.5 pr-8 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ (int) $bulan === $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Input Tahun (Bulanan & Tahunan) -->
                    <div class="sm:col-span-2" x-show="mode === 'bulanan' || mode === 'tahunan'" x-cloak>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tahun</label>
                        <input type="number" name="tahun" value="{{ $tahun }}" min="2020" max="2035"
                               :disabled="mode !== 'bulanan' && mode !== 'tahunan'"
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    <!-- Input Rentang Kustom -->
                    <div class="sm:col-span-2" x-show="mode === 'custom'" x-cloak>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Dari Tanggal</label>
                        <input type="date" name="dari" value="{{ $dari }}"
                               :disabled="mode !== 'custom'"
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    <div class="sm:col-span-2" x-show="mode === 'custom'" x-cloak>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sampai Tanggal</label>
                        <input type="date" name="sampai" value="{{ $sampai }}"
                               :disabled="mode !== 'custom'"
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    <!-- Filter Wilayah -->
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Wilayah</label>
                        <select name="wilayah"
                                class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            <option value="all" {{ $wilayahFilter === 'all' ? 'selected' : '' }}>Semua Wilayah</option>
                            @foreach($wilayahList as $wil)
                                <option value="{{ $wil->kode }}" {{ $wilayahFilter === $wil->kode ? 'selected' : '' }}>
                                    {{ $wil->kode }} - {{ $wil->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Jurusan / Armada -->
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jurusan Armada</label>
                        <select name="jurusan"
                                class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            <option value="all" {{ $jurusanFilter === 'all' ? 'selected' : '' }}>Semua Jurusan</option>
                            @foreach($jurusanList as $jur)
                                <option value="{{ $jur->kode }}" {{ $jurusanFilter === $jur->kode ? 'selected' : '' }}>
                                    Rute {{ $jur->kode }} - {{ $jur->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($tab === 'penerimaan-koran')
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status Bayar</label>
                            <select name="status_bayar"
                                    class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                <option value="all" {{ $statusBayarFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="lunas" {{ $statusBayarFilter === 'lunas' ? 'selected' : '' }}>Lunas</option>
                                <option value="sebagian" {{ $statusBayarFilter === 'sebagian' ? 'selected' : '' }}>Sebagian</option>
                                <option value="belum_bayar" {{ $statusBayarFilter === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                            </select>
                        </div>
                    @else
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Cari Agen</label>
                            <input type="text" name="q" value="{{ $searchQ }}" placeholder="Kode / nama agen..."
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    @endif

                    <!-- Tombol Tampilkan & Reset -->
                    <div class="sm:col-span-2 flex items-center gap-2">
                        <button type="submit"
                                class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer whitespace-nowrap">
                            Tampilkan
                        </button>
                        <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => $tab]) }}"
                           class="h-11 px-3.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl flex items-center justify-center transition">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        @if(!empty($isDateTruncated))
            <div class="print:hidden bg-amber-50 border border-amber-200 text-amber-900 rounded-xl p-3.5 flex items-center gap-3 text-xs sm:text-sm">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <span class="font-bold">Rentang tanggal terlalu panjang ({{ $totalDaysSelected ?? '370+' }} hari).</span>
                    <span>Sistem secara otomatis membatasi kalkulasi jatah harian ke 365 hari terakhir untuk menjaga performa real-time database.</span>
                </div>
            </div>
        @endif

        <!-- 4. Kop Cetak Langsung (Bila Ctrl+P dari layar menu) -->
        <div class="hidden print:block border-b-2 border-slate-900 pb-3 mb-4 text-center">
            <h2 class="text-base font-extrabold uppercase tracking-tight text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
            <h3 class="text-xs font-bold uppercase text-slate-700 mt-0.5">DIVISI SIRKULASI &amp; DISTRIBUSI</h3>
            <div class="mt-2 inline-block px-3 py-1 rounded bg-slate-100 font-bold text-xs uppercase text-slate-800">
                {{ strtoupper($activeMeta['full']) }} &bull; {{ $periodeCetakHeader }}
            </div>
        </div>

        <!-- 5. Konten KPI + Tabel Laporan Sesuai Tab & Filter Aktif -->
        @include('pages.sirkulasi.cetak.partials.pemasaran_content', ['isPrintMode' => false])

        <!-- Tanda Tangan Pengesahan (Bila Ctrl+P dari layar menu) -->
        <div class="hidden print:grid grid-cols-2 text-center text-xs text-slate-800 mt-8 pt-4 border-t border-slate-300 break-inside-avoid">
            <div>
                <p class="text-slate-500">Mengetahui,</p>
                <p class="font-bold text-slate-900 mt-1">Pimpinan Perusahaan / General Manager</p>
                <div class="h-16"></div>
                <p class="font-bold underline text-slate-900">( .................................................... )</p>
            </div>
            <div>
                <p class="text-slate-500">Tasikmalaya, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p class="font-bold text-slate-900 mt-1">Manajer Divisi Sirkulasi</p>
                <div class="h-16"></div>
                <p class="font-bold underline text-slate-900">( .................................................... )</p>
            </div>
        </div>

    </div>
</x-app-layout>
