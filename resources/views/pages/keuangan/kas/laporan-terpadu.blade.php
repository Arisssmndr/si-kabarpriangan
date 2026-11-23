<x-app-layout>
    <div class="space-y-6">

        <!-- Tier 1: Header Modul Laporan Keuangan Terpadu -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-9 sm:h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <h1 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <span>Pusat Laporan Keuangan Terpadu</span>
                        <span class="text-slate-300 font-normal">|</span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 uppercase tracking-wider">Audit & Rekap Kas</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Rekapitulasi arus kas harian, bulanan, serta monitoring efektivitas penagihan piutang inkaso PT Berkah Pikiran Rakyat
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('keuangan.kas.index') }}" 
                   class="h-11 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold flex items-center gap-2 shadow-2xs transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Buku Kas Utama</span>
                </a>
            </div>
        </div>

        <!-- Tab Navigasi 3 Pilar Laporan (Format Iklan 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-1.5 shadow-xs flex flex-wrap gap-1.5">
            <a href="{{ route('keuangan.laporan', ['tab' => 'harian', 'tanggal' => $tanggalHarian]) }}" 
               class="flex-1 min-w-[150px] text-center px-4 py-2.5 rounded-lg text-xs font-bold transition flex items-center justify-center gap-2 {{ $tab === 'harian' ? 'bg-kp-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>1. Laporan Kas Harian</span>
            </a>
            <a href="{{ route('keuangan.laporan', ['tab' => 'bulanan', 'bulan' => $bulanBulanan, 'tahun' => $tahunBulanan]) }}" 
               class="flex-1 min-w-[150px] text-center px-4 py-2.5 rounded-lg text-xs font-bold transition flex items-center justify-center gap-2 {{ $tab === 'bulanan' ? 'bg-kp-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>2. Laporan Kas Bulanan</span>
            </a>
            <a href="{{ route('keuangan.laporan', ['tab' => 'inkaso', 'dari' => $dariInkaso, 'sampai' => $sampaiInkaso, 'petugas_id' => $petugasId]) }}" 
               class="flex-1 min-w-[150px] text-center px-4 py-2.5 rounded-lg text-xs font-bold transition flex items-center justify-center gap-2 {{ $tab === 'inkaso' ? 'bg-kp-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>3. Rekapitulasi Penagihan Inkaso</span>
            </a>
        </div>

        {{-- ======================================================== --}}
        {{-- TAB 1: LAPORAN KAS HARIAN                                --}}
        {{-- ======================================================== --}}
        @if($tab === 'harian')
            <div class="space-y-6">
                <!-- Tier 2: 4 KPI Summary Cards (Format Iklan) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Saldo Awal Hari Ini</span>
                            <span class="p-1.5 rounded-lg bg-slate-50 text-slate-600 border border-slate-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </span>
                        </div>
                        <div class="mt-2 text-lg font-bold text-slate-900 font-mono tabular-nums">
                            Rp {{ number_format($saldoAwalHarian, 0, ',', '.') }}
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Posisi pembukaan buku</p>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Kas Masuk</span>
                            <span class="p-1.5 rounded-lg bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                            </span>
                        </div>
                        <div class="mt-2 text-lg font-bold text-kp-blue-700 font-mono tabular-nums">
                            Rp {{ number_format($totalMasukHarian, 0, ',', '.') }}
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Penerimaan kas masuk hari ini</p>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Kas Keluar</span>
                            <span class="p-1.5 rounded-lg bg-slate-50 text-slate-600 border border-slate-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                            </span>
                        </div>
                        <div class="mt-2 text-lg font-bold text-slate-900 font-mono tabular-nums">
                            Rp {{ number_format($totalKeluarHarian, 0, ',', '.') }}
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Realisasi pengeluaran kas hari ini</p>
                    </div>

                    <div class="bg-white border border-kp-blue-200 bg-kp-blue-50/20 rounded-xl p-4 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-kp-blue-800 uppercase tracking-wider">Saldo Akhir Hari Ini</span>
                            <span class="p-1.5 rounded-lg bg-kp-blue-600 text-white shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        </div>
                        <div class="mt-2 text-lg font-bold text-kp-blue-800 font-mono tabular-nums">
                            Rp {{ number_format($saldoAkhirHarian, 0, ',', '.') }}
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Sisa kas fisik penutupan hari ini</p>
                    </div>
                </div>

                <!-- Tier 3: Toolbar Filter & Aksi Cetak (Tinggi h-11) -->
                <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <form method="GET" action="{{ route('keuangan.laporan') }}" class="flex flex-wrap items-center gap-3">
                        <input type="hidden" name="tab" value="harian">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-700 whitespace-nowrap">Pilih Tanggal:</span>
                            <input type="date" name="tanggal" value="{{ $tanggalHarian }}" 
                                   class="h-11 px-3 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-1 focus:ring-kp-blue-500 focus:border-kp-blue-500 font-medium">
                        </div>
                        <button type="submit" 
                                class="h-11 px-5 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold transition flex items-center gap-2 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>Tampilkan Mutasi</span>
                        </button>
                    </form>

                    <a href="{{ route('keuangan.kas.cetak', ['mode' => 'harian', 'tanggal' => $tanggalHarian]) }}" target="_blank" 
                       class="h-11 px-4 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl shadow-2xs flex items-center gap-2 transition">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Laporan Harian</span>
                    </a>
                </div>

                <!-- Tier 4: Pure Data Table (Format Iklan Tanpa Sub-Banner) -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="overflow-x-auto min-h-[300px]">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                <tr>
                                    <th class="py-3.5 px-3.5 w-12 text-center">No</th>
                                    <th class="py-3.5 px-3.5">No. Transaksi</th>
                                    <th class="py-3.5 px-3.5">Kategori Kas</th>
                                    <th class="py-3.5 px-3.5">Keterangan / Keperluan</th>
                                    <th class="py-3.5 px-3.5 text-right">Kas Masuk (Rp)</th>
                                    <th class="py-3.5 px-3.5 text-right">Kas Keluar (Rp)</th>
                                    <th class="py-3.5 px-3.5 text-right">Saldo Berjalan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700 text-xs font-sans">
                                @forelse($txHarian as $index => $t)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3.5 px-3.5 text-center text-slate-400">{{ $index + 1 }}</td>
                                        <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700">{{ $t->no_transaksi }}</td>
                                        <td class="py-3.5 px-3.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $t->jenis === 'masuk' ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                                {{ $t->kategori_kas }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3.5 text-slate-900 font-medium">{{ $t->keterangan }}</td>
                                        <td class="py-3.5 px-3.5 text-right font-mono">
                                            @if($t->jenis === 'masuk')
                                                <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto">
                                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                                    <span class="font-bold text-kp-blue-700 tabular-nums">{{ number_format($t->nominal, 0, ',', '.') }}</span>
                                                </div>
                                            @else
                                                <span class="text-slate-300">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right font-mono">
                                            @if($t->jenis === 'keluar')
                                                <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto">
                                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                                    <span class="font-bold text-slate-900 tabular-nums">{{ number_format($t->nominal, 0, ',', '.') }}</span>
                                                </div>
                                            @else
                                                <span class="text-slate-300">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right font-mono font-bold text-slate-900">
                                            <div class="flex justify-between items-center gap-2 max-w-[140px] ml-auto">
                                                <span class="text-slate-400 font-normal select-none">Rp</span>
                                                <span class="tabular-nums">{{ number_format($t->saldo_akhir, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-slate-400">
                                            Tidak ada mutasi kas pada tanggal {{ \Carbon\Carbon::parse($tanggalHarian)->translatedFormat('d F Y') }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- ======================================================== --}}
        {{-- TAB 2: LAPORAN KAS BULANAN                               --}}
        {{-- ======================================================== --}}
        @if($tab === 'bulanan')
            <div class="space-y-6">
                <!-- Tier 2: 3 KPI Summary Cards Bulanan -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Kas Masuk (Inflow)</span>
                            <span class="p-1.5 rounded-lg bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                            </span>
                        </div>
                        <div class="mt-2 text-xl font-bold text-kp-blue-700 font-mono tabular-nums">
                            Rp {{ number_format($totalMasukBulanan, 0, ',', '.') }}
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Akumulasi penerimaan bulan terpilih</p>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Kas Keluar (Outflow)</span>
                            <span class="p-1.5 rounded-lg bg-slate-50 text-slate-600 border border-slate-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                            </span>
                        </div>
                        <div class="mt-2 text-xl font-bold text-slate-900 font-mono tabular-nums">
                            Rp {{ number_format($totalKeluarBulanan, 0, ',', '.') }}
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Akumulasi pengeluaran kas bulan terpilih</p>
                    </div>

                    <div class="bg-white border border-kp-blue-200 bg-kp-blue-50/20 rounded-xl p-4 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-kp-blue-800 uppercase tracking-wider">Surplus / (Defisit) Bersih</span>
                            <span class="p-1.5 rounded-lg bg-kp-blue-600 text-white shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        </div>
                        <div class="mt-2 text-xl font-bold font-mono tabular-nums {{ $surplusDefisitBulanan >= 0 ? 'text-kp-blue-800' : 'text-slate-900' }}">
                            Rp {{ number_format($surplusDefisitBulanan, 0, ',', '.') }}
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Selisih mutasi masuk dan keluar periode</p>
                    </div>
                </div>

                <!-- Tier 3: Toolbar Filter Periode & Cetak (Tinggi h-11) -->
                <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <form method="GET" action="{{ route('keuangan.laporan') }}" class="flex flex-wrap items-center gap-3">
                        <input type="hidden" name="tab" value="bulanan">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-700 whitespace-nowrap">Pilih Periode:</span>
                            <select name="bulan" class="h-11 px-3 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-1 focus:ring-kp-blue-500 focus:border-kp-blue-500 font-medium">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $bulanBulanan == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                            <select name="tahun" class="h-11 px-3 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-1 focus:ring-kp-blue-500 focus:border-kp-blue-500 font-medium">
                                @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}" {{ $tahunBulanan == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <button type="submit" class="h-11 px-5 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold transition flex items-center gap-2 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>Tampilkan Bulanan</span>
                        </button>
                    </form>

                    <a href="{{ route('keuangan.kas.cetak', ['mode' => 'bulanan', 'bulan' => $bulanBulanan, 'tahun' => $tahunBulanan]) }}" target="_blank" 
                       class="h-11 px-4 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl shadow-2xs flex items-center gap-2 transition">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Laporan Bulanan</span>
                    </a>
                </div>

                <!-- Tier 4: Rincian Kategori Inflow & Outflow Pure Data Table -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                            <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-kp-blue-600"></span>
                                <span>Rincian Kas Masuk Per Kategori</span>
                            </h3>
                            <span class="text-xs font-bold text-kp-blue-700 font-mono">Total: Rp {{ number_format($totalMasukBulanan, 0, ',', '.') }}</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm border-collapse">
                                <thead class="bg-slate-50/50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                    <tr>
                                        <th class="py-3 px-3.5">Kategori Arus Masuk</th>
                                        <th class="py-3 px-3.5 text-right">Akumulasi (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700 text-xs">
                                    @forelse($rekapKategoriMasuk as $kat => $nom)
                                        <tr class="hover:bg-slate-50/70 transition">
                                            <td class="py-3 px-3.5 font-medium text-slate-800">{{ $kat }}</td>
                                            <td class="py-3 px-3.5 text-right font-mono font-bold text-kp-blue-700">
                                                <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto">
                                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                                    <span class="tabular-nums">{{ number_format($nom, 0, ',', '.') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="py-8 text-center text-slate-400">Belum ada kas masuk pada periode ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                            <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-slate-600"></span>
                                <span>Rincian Kas Keluar Per Kategori</span>
                            </h3>
                            <span class="text-xs font-bold text-slate-900 font-mono">Total: Rp {{ number_format($totalKeluarBulanan, 0, ',', '.') }}</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm border-collapse">
                                <thead class="bg-slate-50/50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                    <tr>
                                        <th class="py-3 px-3.5">Kategori Arus Keluar</th>
                                        <th class="py-3 px-3.5 text-right">Akumulasi (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700 text-xs">
                                    @forelse($rekapKategoriKeluar as $kat => $nom)
                                        <tr class="hover:bg-slate-50/70 transition">
                                            <td class="py-3 px-3.5 font-medium text-slate-800">{{ $kat }}</td>
                                            <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">
                                                <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto">
                                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                                    <span class="tabular-nums">{{ number_format($nom, 0, ',', '.') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="py-8 text-center text-slate-400">Belum ada kas keluar pada periode ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ======================================================== --}}
        {{-- TAB 3: REKAPITULASI PENAGIHAN INKASO                     --}}
        {{-- ======================================================== --}}
        @if($tab === 'inkaso')
            <div class="space-y-6">
                <!-- Tier 2: 4 KPI Summary Cards Inkaso (Format Iklan) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Target Piutang Inkaso</span>
                            <span class="p-1.5 rounded-lg bg-slate-50 text-slate-600 border border-slate-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </span>
                        </div>
                        <div class="mt-2 text-lg font-bold text-slate-900 font-mono tabular-nums">
                            Rp {{ number_format($totalTargetInkaso, 0, ',', '.') }}
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Total tagihan ditugaskan ke lapangan</p>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Berhasil Ditagih</span>
                            <span class="p-1.5 rounded-lg bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                            </span>
                        </div>
                        <div class="mt-2 text-lg font-bold text-kp-blue-700 font-mono tabular-nums">
                            Rp {{ number_format($totalTertagihInkaso, 0, ',', '.') }}
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Masuk ke kas kantor dari inkaso</p>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Sisa Piutang Lapangan</span>
                            <span class="p-1.5 rounded-lg bg-amber-50 text-amber-700 border border-amber-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                        </div>
                        <div class="mt-2 text-lg font-bold text-amber-700 font-mono tabular-nums">
                            Rp {{ number_format($totalSisaPiutangInkaso, 0, ',', '.') }}
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Belum tertagih oleh kolektor</p>
                    </div>

                    <div class="bg-white border border-kp-blue-200 bg-kp-blue-50/20 rounded-xl p-4 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-kp-blue-800 uppercase tracking-wider">Efektivitas Koleksi</span>
                            <span class="p-1.5 rounded-lg bg-kp-blue-600 text-white shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </span>
                        </div>
                        <div class="mt-2 text-lg font-bold text-kp-blue-800 font-mono tabular-nums">
                            {{ $persenTertagih }}%
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Rasio penyelesaian tagihan</p>
                    </div>
                </div>

                <!-- Tier 3: Toolbar Filter Inkaso & Aksi Cetak (Tinggi h-11) -->
                <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                    <form method="GET" action="{{ route('keuangan.laporan') }}" class="flex flex-wrap items-center gap-3">
                        <input type="hidden" name="tab" value="inkaso">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-700 whitespace-nowrap">Periode:</span>
                            <input type="date" name="dari" value="{{ $dariInkaso }}" 
                                   class="h-11 px-3 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-1 focus:ring-kp-blue-500 focus:border-kp-blue-500 font-medium">
                            <span class="text-xs text-slate-400">s/d</span>
                            <input type="date" name="sampai" value="{{ $sampaiInkaso }}" 
                                   class="h-11 px-3 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-1 focus:ring-kp-blue-500 focus:border-kp-blue-500 font-medium">
                        </div>

                        <select name="petugas_id" 
                                class="h-11 px-3 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-1 focus:ring-kp-blue-500 focus:border-kp-blue-500 font-medium">
                            <option value="">-- Semua Petugas Inkaso --</option>
                            @foreach($daftarPetugas as $p)
                                <option value="{{ $p->id }}" {{ $petugasId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="h-11 px-5 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold transition flex items-center gap-2 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>Filter Inkaso</span>
                        </button>
                    </form>

                    <a href="{{ route('keuangan.inkaso.rekap.cetak', ['dari' => $dariInkaso, 'sampai' => $sampaiInkaso, 'petugas_id' => $petugasId]) }}" target="_blank" 
                       class="h-11 px-4 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl shadow-2xs flex items-center gap-2 transition">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Rekapitulasi</span>
                    </a>
                </div>

                <!-- Tier 4: Pure Data Table (Kolom Terpisah 100% Sesuai Iklan) -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="overflow-x-auto min-h-[300px]">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                <tr>
                                    <th class="py-3.5 px-3.5 w-12 text-center">No</th>
                                    <th class="py-3.5 px-3.5">No. Tugas</th>
                                    <th class="py-3.5 px-3.5">Tanggal Tugas</th>
                                    <th class="py-3.5 px-3.5">Petugas Kolektor</th>
                                    <th class="py-3.5 px-3.5">No. Faktur</th>
                                    <th class="py-3.5 px-3.5">Nama Pelanggan</th>
                                    <th class="py-3.5 px-3.5 text-right">Nilai Tagihan (Rp)</th>
                                    <th class="py-3.5 px-3.5 text-right">Setoran Masuk (Rp)</th>
                                    <th class="py-3.5 px-3.5 text-right">Sisa Piutang (Rp)</th>
                                    <th class="py-3.5 px-3.5 text-center">Status Tagihan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700 text-xs">
                                @forelse($penugasanInkasoList as $index => $p)
                                    @php
                                        $tertagih = (float) $p->pembayaran->sum('nominal_bayar');
                                        $sisa = $p->faktur ? (float) $p->faktur->sisa_piutang : 0;
                                        $totalTagihan = $p->faktur ? (float) $p->faktur->total_tagihan : 0;
                                    @endphp
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3.5 px-3.5 text-center text-slate-400">{{ $index + 1 }}</td>
                                        <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700">{{ $p->no_tugas }}</td>
                                        <td class="py-3.5 px-3.5">{{ \Carbon\Carbon::parse($p->tanggal_tugas)->translatedFormat('d M Y') }}</td>
                                        <td class="py-3.5 px-3.5 font-bold text-slate-800">{{ $p->petugas->name ?? '-' }}</td>
                                        <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700">{{ $p->faktur->no_faktur ?? '-' }}</td>
                                        <td class="py-3.5 px-3.5 font-medium text-slate-900">{{ $p->faktur->customer->nama_customer ?? '-' }}</td>
                                        <td class="py-3.5 px-3.5 text-right font-mono">
                                            <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto">
                                                <span class="text-slate-400 font-normal select-none">Rp</span>
                                                <span class="font-medium text-slate-900 tabular-nums">{{ number_format($totalTagihan, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right font-mono">
                                            <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto">
                                                <span class="text-slate-400 font-normal select-none">Rp</span>
                                                <span class="font-bold text-kp-blue-700 tabular-nums">{{ number_format($tertagih, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right font-mono">
                                            <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto">
                                                <span class="text-slate-400 font-normal select-none">Rp</span>
                                                <span class="font-bold text-amber-700 tabular-nums">{{ number_format($sisa, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-3.5 text-center">
                                            @if($p->status_tugas === 'selesai' || $sisa == 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                    Selesai (Lunas)
                                                </span>
                                            @elseif($p->status_tugas === 'batal')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                    Dibatalkan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                    Berjalan
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="py-12 text-center text-slate-400">
                                            Tidak ada data penugasan inkaso pada periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
