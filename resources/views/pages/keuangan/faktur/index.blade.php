<x-app-layout>
    <div class="space-y-5">

        @php
            $judulHalaman = match($tab ?? 'keseluruhan') {
                'keseluruhan'   => 'Faktur Iklan Keseluruhan',
                'susulan'       => 'Faktur Iklan Susulan',
                'rekap_tanggal' => 'Rekap Faktur Iklan (Per Tanggal)',
                'rekap_bulan'   => 'Rekap Faktur Iklan (Per Bulan)',
                'tunai'         => 'Rekap Faktur Iklan Tunai',
                'piutang'       => 'Rekap Faktur Iklan Piutang',
                'stopper_bonus' => 'Rekap Faktur Iklan Stopper & Bonus',
                default         => 'Daftar Faktur Iklan',
            };
        @endphp

        <!-- 1. Header Halaman (Standar Divisi Iklan 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-9 sm:h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            {{ $judulHalaman }}
                        </h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 shrink-0">
                            <span>Auto-Sync Front Office</span>
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Monitoring data transaksi iklan otomatis dari Front Office &bull; Rekapitulasi piutang &amp; cetak surat penugasan penagihan
                    </p>
                </div>
            </div>

            <!-- Tombol Aksi Sejajar 1 Baris -->
            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <button type="button"
                        id="btnBulkSuratTugas"
                        onclick="bukaModalSuratTugasGabungan()"
                        style="display: none;"
                        class="inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-amber-400 hover:bg-amber-500 text-slate-900 text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Cetak Surat Tugas (<span id="countSelectedInkaso">0</span>/10)</span>
                </button>

                <a href="{{ route('keuangan.faktur.cetak-rekap', [
                    'mode' => $tab === 'rekap_bulan' ? 'bulan' : 'tanggal',
                    'status' => $statusSubmenu ?? 'keseluruhan',
                    'tahun' => $tahun ?? now()->year,
                    'bulan' => $bulan ?? now()->month,
                    'dari_tanggal' => $dariTanggal ?? now()->startOfMonth()->toDateString(),
                    'sampai_tanggal' => $sampaiTanggal ?? now()->toDateString()
                ]) }}" target="_blank" 
                   class="inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Rekap</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 font-semibold rounded-xl text-sm flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-slate-100 border border-slate-300 text-slate-800 font-semibold rounded-xl text-sm flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- 2. KPI Summary Cards (Standar Divisi Iklan) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Total Faktur Iklan</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalFakturCount) }} <span class="text-xs font-normal text-slate-400 font-sans">Dokumen</span>
                </p>
                <div class="mt-2 text-xs text-slate-500 flex items-center justify-between">
                    <span>Omset:</span>
                    <span class="font-bold text-slate-800 font-mono">Rp {{ number_format($totalTagihanSum, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Faktur Lunas</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($fakturLunasCount) }} <span class="text-xs font-normal text-slate-400 font-sans">Faktur</span>
                </p>
                <div class="mt-2 text-xs text-slate-500 flex items-center justify-between">
                    <span>Terbayar:</span>
                    <span class="font-bold text-kp-blue-700 font-mono">Rp {{ number_format($totalTerbayarSum, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Piutang Beredar</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($fakturPiutangCount) }} <span class="text-xs font-normal text-slate-400 font-sans">Faktur</span>
                </p>
                <div class="mt-2 text-xs text-slate-500 flex items-center justify-between">
                    <span>Penagihan:</span>
                    <span class="font-semibold text-kp-blue-700">Wajib Inkaso</span>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Sisa Saldo Piutang</span>
                <p class="text-xl sm:text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalPiutangSum, 0, ',', '.') }}
                </p>
                <div class="mt-2 text-xs text-slate-500 flex items-center justify-between">
                    <span>Status:</span>
                    <span class="font-bold text-kp-blue-700 bg-kp-blue-50 px-2 py-0.5 rounded-md border border-kp-blue-200">Siap Ditugaskan</span>
                </div>
            </div>
        </div>

        <!-- 3. Toolbar Pencarian & Filter (Standar Divisi Iklan h-11) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('keuangan.faktur.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <!-- Pencarian Global -->
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Data</label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik no. faktur, nama pemasang, sales..." 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>

                <!-- Submenu Status -->
                @if(in_array($tab, ['keseluruhan', 'susulan', 'rekap_tanggal', 'rekap_bulan']))
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status Pembayaran</label>
                        <select name="status" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            <option value="keseluruhan" {{ $statusSubmenu === 'keseluruhan' ? 'selected' : '' }}>Semua Status</option>
                            <option value="lunas" {{ $statusSubmenu === 'lunas' ? 'selected' : '' }}>Lunas</option>
                            <option value="belum_lunas" {{ $statusSubmenu === 'belum_lunas' ? 'selected' : '' }}>Belum Lunas (Piutang)</option>
                        </select>
                    </div>
                @endif

                <!-- Filter Tanggal jika Tab Rekap Tanggal -->
                @if($tab === 'rekap_tanggal')
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Dari Tanggal</label>
                        <input type="date" name="dari_tanggal" value="{{ $dariTanggal }}" 
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sampai Tanggal</label>
                        <input type="date" name="sampai_tanggal" value="{{ $sampaiTanggal }}" 
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                @endif

                <!-- Filter Bulan & Tahun jika Tab Rekap Bulan / Stock Opname -->
                @if(in_array($tab, ['rekap_bulan', 'stock_opname', 'tunai']))
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bulan</label>
                        <select name="bulan" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tahun</label>
                        <select name="tahun" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            @for ($y = 2024; $y <= 2028; $y++)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                @endif

                <!-- Filter Khusus Piutang -->
                @if($tab === 'piutang')
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Penugasan Inkaso</label>
                        <select name="status_inkaso" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            <option value="">Semua Penugasan</option>
                            <option value="belum" {{ request('status_inkaso') === 'belum' ? 'selected' : '' }}>Belum Ditugaskan (Antrean)</option>
                            <option value="sudah" {{ request('status_inkaso') === 'sudah' ? 'selected' : '' }}>Sudah Ditugaskan ke Petugas</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Umur Piutang</label>
                        <select name="aging" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            <option value="">Semua Umur</option>
                            <option value="0-30" {{ request('aging') === '0-30' ? 'selected' : '' }}>0 - 30 Hari</option>
                            <option value="31-60" {{ request('aging') === '31-60' ? 'selected' : '' }}>31 - 60 Hari</option>
                            <option value="61-90" {{ request('aging') === '61-90' ? 'selected' : '' }}>61 - 90 Hari</option>
                            <option value=">90" {{ request('aging') === '>90' ? 'selected' : '' }}>&gt; 90 Hari</option>
                        </select>
                    </div>
                @endif

                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'status', 'dari_tanggal', 'sampai_tanggal', 'status_inkaso', 'aging']))
                        <a href="{{ route('keuangan.faktur.index', ['tab' => $tab]) }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center" title="Reset Filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 4. Pure Data Table (Kolom Terpisah & Bersih Selaras Standar Divisi Iklan) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3 text-center w-12">No</th>
                            <th class="py-3 px-3 text-center w-12" title="Pilih Maksimal 10 Faktur untuk 1 Surat Tugas Inkaso">Inkaso</th>
                            <th class="py-3 px-3.5">No. Faktur</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Jatuh Tempo</th>
                            <th class="py-3 px-3.5">Pemasang</th>
                            <th class="py-3 px-3.5">Alamat</th>
                            <th class="py-3 px-3.5">Sales / AE</th>
                            <th class="py-3 px-3.5 text-center">Tipe</th>
                            <th class="py-3 px-3.5 text-right w-32">Tagihan</th>
                            <th class="py-3 px-3.5 text-right w-32">Dibayar</th>
                            <th class="py-3 px-3.5 text-right w-32">Piutang</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center">Surat Tugas</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($fakturs as $key => $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3 text-center text-slate-500 font-normal">
                                    {{ method_exists($fakturs, 'firstItem') && $fakturs->firstItem() ? ($fakturs->firstItem() + $key) : ($key + 1) }}
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    @if($item->sisa_piutang > 0)
                                        <input type="checkbox"
                                               class="cb-inkaso-faktur rounded border-slate-300 text-kp-blue-600 focus:ring-kp-blue-500 cursor-pointer"
                                               value="{{ $item->id }}"
                                               data-nofaktur="{{ $item->no_faktur }}"
                                               data-customer="{{ $item->customer->nama_customer ?? 'UMUM' }}"
                                               data-piutang="{{ (float) $item->sisa_piutang }}"
                                               onchange="onCheckInkasoRow(this)"
                                               title="Centang untuk gabung ke Surat Tugas Inkaso (Maks. 10 Faktur)">
                                    @else
                                        <span class="text-slate-300 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    <a href="{{ route('keuangan.faktur.show', $item->id) }}" class="hover:underline">
                                        {{ $item->no_faktur }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-mono font-normal">
                                    {{ optional($item->tanggal_faktur)->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-mono font-normal">
                                    {{ optional($item->jatuh_tempo)->format('d/m/Y') ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900 uppercase">
                                    {{ $item->customer->nama_customer ?? 'UMUM' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal truncate max-w-xs" title="{{ $item->customer->alamat ?? '-' }}">
                                    {{ $item->customer->alamat ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                    {{ $item->sales_nama ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700 uppercase border border-slate-200">
                                        {{ $item->tipe_faktur ?? 'reguler' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($item->total_tagihan, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($item->total_terbayar, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    @if($item->sisa_piutang > 0)
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-normal text-slate-700 tabular-nums">{{ number_format($item->sisa_piutang, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <div class="text-right font-mono text-sm text-slate-400 font-normal">
                                            Rp 0
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($item->status === 'lunas')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200">
                                            Lunas
                                        </span>
                                    @elseif($item->status === 'cicilan')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border bg-amber-50 text-amber-800 border-amber-300">
                                            Cicilan
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border bg-slate-100 text-slate-700 border-slate-200">
                                            Piutang
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($item->sisa_piutang > 0)
                                        <button type="button" 
                                                onclick="bukaModalSuratTugas('{{ $item->id }}', '{{ $item->no_faktur }}', '{{ number_format($item->sisa_piutang, 0, ',', '.') }}', '{{ addslashes($item->customer->nama_customer ?? 'UMUM') }}')"
                                                class="inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-400 hover:bg-amber-500 text-amber-950 text-xs font-bold shadow-2xs transition cursor-pointer"
                                                title="Cetak Surat Penugasan Penagihan Lapangan">
                                            <span>Surat Tugas</span>
                                        </button>
                                    @else
                                        <span class="text-slate-300 font-mono text-xs">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center" x-data="{ open: false }" :class="{ 'relative z-50': open }">
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
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             class="absolute right-0 z-50 mt-1 w-44 origin-top-right rounded-xl bg-white border border-slate-200 shadow-lg py-1 focus:outline-none text-left">
                                            <a href="{{ route('keuangan.faktur.show', $item->id) }}" class="flex items-center gap-2 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail Faktur</span>
                                            </a>
                                            <a href="{{ route('keuangan.faktur.cetak', $item->id) }}" target="_blank" class="flex items-center gap-2 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                <span>Cetak Faktur</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="py-12 text-center text-slate-400">
                                    <p class="text-sm font-semibold text-slate-600">Tidak ada faktur pemuatan iklan pada kriteria ini.</p>
                                    <p class="text-xs text-slate-400 mt-1">Data faktur iklan otomatis tersinkron dari transaksi Front Office Iklan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($fakturs, 'hasPages') && $fakturs->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $fakturs->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Modal Input Nama Petugas Penagih (Inkaso) & Pilih s/d 10 Faktur -->
    <div id="modalSuratTugas" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in duration-150">
            <div class="flex items-start justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Cetak Surat Penugasan Penagihan (Inkaso)</h3>
                    <p class="text-xs text-slate-500">1 Surat Tugas Inkaso dapat memuat hingga <strong>10 Faktur Piutang</strong> sekaligus</p>
                </div>
                <button type="button" onclick="tutupModalSuratTugas()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">✕</button>
            </div>

            <div class="mt-4 space-y-3">
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 space-y-1">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 font-medium">Faktur Terpilih:</span>
                        <strong class="font-mono text-slate-900" id="modalNoFaktur">-</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 font-medium">Pelanggan:</span>
                        <span class="font-bold truncate max-w-[240px]" id="modalCustomer">-</span>
                    </div>
                    <div class="flex justify-between items-center pt-1 border-t border-slate-200">
                        <span class="text-slate-700 font-semibold">Total Sisa Piutang Ditagih:</span>
                        <strong class="text-kp-blue-700 font-black font-mono text-sm" id="modalSisaPiutang">Rp 0</strong>
                    </div>
                </div>

                <!-- Pilihan Tambah Faktur Piutang Lainnya (Maks 10 Faktur) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-bold text-slate-700">
                            Daftar Faktur Piutang yang Dibawa (Maks. 10 Faktur):
                        </label>
                        <span class="text-xs font-mono font-bold text-kp-blue-700 bg-kp-blue-50 px-2 py-0.5 rounded border border-kp-blue-200">
                            <span id="modalCountFaktur">1</span> / 10 Faktur
                        </span>
                    </div>
                    <div class="border border-slate-200 rounded-xl bg-slate-50/70 p-2 space-y-1.5 overflow-y-auto" style="max-height: 150px;">
                        @foreach($fakturs as $fOpt)
                            @if($fOpt->sisa_piutang > 0)
                                <label class="flex items-center justify-between gap-2 p-2 rounded-lg bg-white border border-slate-200 hover:border-kp-blue-300 cursor-pointer text-xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <input type="checkbox"
                                               class="cb-modal-faktur rounded border-slate-300 text-kp-blue-600 focus:ring-kp-blue-500"
                                               value="{{ $fOpt->id }}"
                                               data-nofaktur="{{ $fOpt->no_faktur }}"
                                               data-customer="{{ $fOpt->customer->nama_customer ?? 'UMUM' }}"
                                               data-piutang="{{ (float) $fOpt->sisa_piutang }}"
                                               onchange="onCheckModalFaktur(this)">
                                        <span class="font-mono font-bold text-kp-blue-700">{{ $fOpt->no_faktur }}</span>
                                        <span class="text-slate-700 font-semibold truncate">{{ $fOpt->customer->nama_customer ?? 'UMUM' }}</span>
                                    </div>
                                    <span class="font-mono font-bold text-slate-900 shrink-0">Rp {{ number_format($fOpt->sisa_piutang, 0, ',', '.') }}</span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                    <p id="modalMaxWarning" style="display: none;" class="text-xs text-rose-600 font-bold mt-1">
                        Maksimal 10 faktur untuk 1 Surat Penugasan Inkaso!
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Pilih Petugas Penagih Lapangan (Inkaso):
                    </label>
                    <select id="selectModalPetugas" 
                            onchange="handleSelectModalPetugas(this.value)" 
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-sm text-slate-900 font-semibold bg-white focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 shadow-2xs cursor-pointer">
                        <option value="">-- Pilih Petugas Penagihan --</option>
                        <option value="Ahmad Supardi">Ahmad Supardi (Wilayah Kota Tasikmalaya)</option>
                        <option value="Rudi Hermawan">Rudi Hermawan (Wilayah Ciamis &amp; Singaparna)</option>
                        <option value="Dadan Ramdani">Dadan Ramdani (Wilayah Banjar &amp; Pangandaran)</option>
                        <option value="Yudi Permana">Yudi Permana (Wilayah Garut &amp; Priangan)</option>
                        <option value="__manual__">Ketik Nama Petugas Lainnya...</option>
                        <option value="__blank__">Tanpa Nama (Format Garis Titik-titik / Tulis Tangan)</option>
                    </select>

                    <!-- Pilihan Cepat (Pill Buttons) -->
                    <div class="flex flex-wrap gap-1.5 mt-2.5">
                        <button type="button" onclick="setPetugasModal('Ahmad Supardi')" class="px-2.5 py-1 rounded-lg bg-kp-blue-50 hover:bg-kp-blue-100 text-kp-blue-900 text-xs font-semibold border border-kp-blue-200 transition cursor-pointer">
                            Ahmad Supardi
                        </button>
                        <button type="button" onclick="setPetugasModal('Rudi Hermawan')" class="px-2.5 py-1 rounded-lg bg-kp-blue-50 hover:bg-kp-blue-100 text-kp-blue-900 text-xs font-semibold border border-kp-blue-200 transition cursor-pointer">
                            Rudi Hermawan
                        </button>
                        <button type="button" onclick="setPetugasModal('Dadan Ramdani')" class="px-2.5 py-1 rounded-lg bg-kp-blue-50 hover:bg-kp-blue-100 text-kp-blue-900 text-xs font-semibold border border-kp-blue-200 transition cursor-pointer">
                            Dadan Ramdani
                        </button>
                        <button type="button" onclick="setPetugasModal('Yudi Permana')" class="px-2.5 py-1 rounded-lg bg-kp-blue-50 hover:bg-kp-blue-100 text-kp-blue-900 text-xs font-semibold border border-kp-blue-200 transition cursor-pointer">
                            Yudi Permana
                        </button>
                    </div>

                    <!-- Field Input jika memilih opsi Ketik Manual -->
                    <div id="containerInputManual" class="mt-2.5 hidden">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Petugas Lainnya:</label>
                        <input type="text" 
                               id="inputModalPetugas" 
                               placeholder="Ketik nama petugas..." 
                               onkeydown="if(event.key === 'Enter') prosesCetakSuratTugas();"
                               class="w-full h-11 px-3.5 rounded-xl border border-kp-blue-300 text-sm text-slate-900 font-bold focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 shadow-2xs">
                    </div>

                    <p class="text-xs text-slate-400 mt-2 italic">
                        *Nama hanya dicetak pada lembar fisik penugasan (tanpa membuat akun user di database).
                    </p>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2">
                <button type="button" onclick="tutupModalSuratTugas()" class="h-10 px-4 rounded-xl border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold hover:bg-slate-50 transition cursor-pointer">
                    Batal
                </button>
                <button type="button" onclick="prosesCetakSuratTugas()" class="h-10 px-4 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-900 font-bold text-xs sm:text-sm shadow-xs transition flex items-center cursor-pointer">
                    <span>Buka &amp; Cetak Surat Tugas</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentSuratTugasFakturId = null;
        let selectedPetugasNama = '';

        function formatIDR(num) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(num || 0));
        }

        function onCheckInkasoRow(cb) {
            const checked = document.querySelectorAll('.cb-inkaso-faktur:checked');
            if (checked.length > 10) {
                cb.checked = false;
                if (window.AppAlert) {
                    AppAlert.warning({
                        title: 'Batas Maksimal Faktur',
                        message: 'Maksimal 10 faktur yang dapat dipilih untuk 1 Surat Penugasan Inkaso.'
                    });
                }
                return;
            }
            const count = document.querySelectorAll('.cb-inkaso-faktur:checked').length;
            const btnBulk = document.getElementById('btnBulkSuratTugas');
            const lblCount = document.getElementById('countSelectedInkaso');
            if (lblCount) lblCount.textContent = count;
            if (btnBulk) btnBulk.style.display = count > 0 ? 'inline-flex' : 'none';
        }

        function updateModalFakturSummary() {
            const checked = Array.from(document.querySelectorAll('.cb-modal-faktur:checked'));
            const countEl = document.getElementById('modalCountFaktur');
            if (countEl) countEl.textContent = checked.length;

            if (checked.length === 0) {
                document.getElementById('modalNoFaktur').textContent = '-';
                document.getElementById('modalCustomer').textContent = '-';
                document.getElementById('modalSisaPiutang').textContent = 'Rp 0';
                return;
            }

            currentSuratTugasFakturId = checked[0].value;
            const totalPiutang = checked.reduce((acc, el) => acc + (parseFloat(el.getAttribute('data-piutang')) || 0), 0);

            if (checked.length === 1) {
                document.getElementById('modalNoFaktur').textContent = checked[0].getAttribute('data-nofaktur');
                document.getElementById('modalCustomer').textContent = checked[0].getAttribute('data-customer');
            } else {
                document.getElementById('modalNoFaktur').textContent = checked.length + ' Faktur (' + checked[0].getAttribute('data-nofaktur') + ', dst.)';
                document.getElementById('modalCustomer').textContent = checked.length + ' Pelanggan / Pemasang';
            }
            document.getElementById('modalSisaPiutang').textContent = formatIDR(totalPiutang);
        }

        function onCheckModalFaktur(cb) {
            const checked = document.querySelectorAll('.cb-modal-faktur:checked');
            const warn = document.getElementById('modalMaxWarning');
            if (checked.length > 10) {
                cb.checked = false;
                if (warn) warn.style.display = 'block';
                return;
            }
            if (warn) warn.style.display = 'none';
            updateModalFakturSummary();
        }

        function bukaModalSuratTugas(id, noFaktur, sisaPiutang, customer) {
            currentSuratTugasFakturId = id;
            const tableChecked = Array.from(document.querySelectorAll('.cb-inkaso-faktur:checked')).map(el => String(el.value));
            const targetIds = tableChecked.length > 0 && tableChecked.includes(String(id))
                ? tableChecked.slice(0, 10)
                : [String(id)];

            document.querySelectorAll('.cb-modal-faktur').forEach(cb => {
                cb.checked = targetIds.includes(String(cb.value));
            });

            updateModalFakturSummary();

            const selectEl = document.getElementById('selectModalPetugas');
            selectEl.value = 'Ahmad Supardi';
            selectedPetugasNama = 'Ahmad Supardi';
            document.getElementById('containerInputManual').classList.add('hidden');
            document.getElementById('inputModalPetugas').value = '';

            const modal = document.getElementById('modalSuratTugas');
            modal.classList.remove('hidden');
        }

        function bukaModalSuratTugasGabungan() {
            const tableChecked = Array.from(document.querySelectorAll('.cb-inkaso-faktur:checked')).map(el => String(el.value)).slice(0, 10);
            if (tableChecked.length === 0) return;

            currentSuratTugasFakturId = tableChecked[0];
            document.querySelectorAll('.cb-modal-faktur').forEach(cb => {
                cb.checked = tableChecked.includes(String(cb.value));
            });

            updateModalFakturSummary();

            const selectEl = document.getElementById('selectModalPetugas');
            selectEl.value = 'Ahmad Supardi';
            selectedPetugasNama = 'Ahmad Supardi';
            document.getElementById('containerInputManual').classList.add('hidden');
            document.getElementById('inputModalPetugas').value = '';

            const modal = document.getElementById('modalSuratTugas');
            modal.classList.remove('hidden');
        }

        function handleSelectModalPetugas(val) {
            const containerManual = document.getElementById('containerInputManual');
            const inputManual = document.getElementById('inputModalPetugas');
            
            if (val === '__manual__') {
                containerManual.classList.remove('hidden');
                inputManual.focus();
                selectedPetugasNama = inputManual.value.trim();
            } else if (val === '__blank__') {
                containerManual.classList.add('hidden');
                selectedPetugasNama = '';
            } else {
                containerManual.classList.add('hidden');
                selectedPetugasNama = val;
            }
        }

        function setPetugasModal(nama) {
            const selectEl = document.getElementById('selectModalPetugas');
            selectEl.value = nama;
            selectedPetugasNama = nama;
            document.getElementById('containerInputManual').classList.add('hidden');
        }

        function tutupModalSuratTugas() {
            const modal = document.getElementById('modalSuratTugas');
            modal.classList.add('hidden');
            currentSuratTugasFakturId = null;
        }

        function prosesCetakSuratTugas() {
            const selectedIds = Array.from(document.querySelectorAll('.cb-modal-faktur:checked')).map(el => el.value).slice(0, 10);
            const firstId = selectedIds.length > 0 ? selectedIds[0] : currentSuratTugasFakturId;
            if (!firstId) return;
            
            const selectEl = document.getElementById('selectModalPetugas');
            let finalNama = selectedPetugasNama;
            
            if (selectEl.value === '__manual__') {
                finalNama = document.getElementById('inputModalPetugas').value.trim();
            } else if (selectEl.value === '__blank__') {
                finalNama = '';
            }

            const url = new URL("{{ url('keuangan/faktur') }}/" + firstId + "/surat-tugas", window.location.origin);
            if (finalNama) url.searchParams.set('petugas', finalNama);
            if (selectedIds.length > 0) url.searchParams.set('faktur_ids', selectedIds.join(','));
            window.open(url.toString(), '_blank');
            tutupModalSuratTugas();
        }
    </script>
</x-app-layout>
