<x-app-layout>
    <div class="space-y-6">

        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                            PUSAT LAPORAN KEUANGAN
                        </h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekapitulasi arus kas harian, bulanan, serta kinerja penagihan piutang inkaso terpadu
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('keuangan.kas.index') }}" 
                   class="h-9 px-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold flex items-center gap-1.5 shadow-2xs transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Buku Kas Utama</span>
                </a>
            </div>
        </div>

        <!-- Tab Navigasi 3 Pilar Laporan -->
        <div class="bg-white border border-slate-200 rounded-2xl p-1.5 shadow-xs flex flex-wrap gap-1">
            <a href="{{ route('keuangan.laporan', ['tab' => 'harian', 'tanggal' => $tanggalHarian]) }}" 
               class="flex-1 min-w-[140px] text-center px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 {{ $tab === 'harian' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>1. Laporan Kas Harian</span>
            </a>
            <a href="{{ route('keuangan.laporan', ['tab' => 'bulanan', 'bulan' => $bulanBulanan, 'tahun' => $tahunBulanan]) }}" 
               class="flex-1 min-w-[140px] text-center px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 {{ $tab === 'bulanan' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>2. Laporan Kas Bulanan</span>
            </a>
            <a href="{{ route('keuangan.laporan', ['tab' => 'inkaso', 'dari' => $dariInkaso, 'sampai' => $sampaiInkaso, 'petugas_id' => $petugasId]) }}" 
               class="flex-1 min-w-[140px] text-center px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 {{ $tab === 'inkaso' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>3. Rekapitulasi Penagihan Inkaso</span>
            </a>
        </div>

        {{-- ======================================================== --}}
        {{-- TAB 1: LAPORAN KAS HARIAN                                --}}
        {{-- ======================================================== --}}
        @if($tab === 'harian')
            <div class="space-y-6">
                <!-- Filter Tanggal & Aksi Cetak -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <form method="GET" action="{{ route('keuangan.laporan') }}" class="flex items-center gap-3">
                        <input type="hidden" name="tab" value="harian">
                        <label class="text-xs font-bold text-slate-700 whitespace-nowrap">Pilih Tanggal:</label>
                        <input type="date" name="tanggal" value="{{ $tanggalHarian }}" class="text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-blue-500 py-1.5 px-3">
                        <button type="submit" class="h-8 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">Tampilkan</button>
                    </form>

                    <a href="{{ route('keuangan.kas.cetak', ['mode' => 'harian', 'tanggal' => $tanggalHarian]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Laporan Harian</span>
                    </a>
                </div>

                <!-- 4 KPI Card Harian -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
                        <span class="text-[11px] font-bold text-slate-400 uppercase">Saldo Awal Hari Ini</span>
                        <div class="text-lg font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($saldoAwalHarian, 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-white border border-emerald-200 rounded-2xl p-4 shadow-xs">
                        <span class="text-[11px] font-bold text-emerald-700 uppercase">Total Kas Masuk</span>
                        <div class="text-lg font-bold text-emerald-600 mt-1 font-mono">+ Rp {{ number_format($totalMasukHarian, 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-white border border-rose-200 rounded-2xl p-4 shadow-xs">
                        <span class="text-[11px] font-bold text-rose-700 uppercase">Total Kas Keluar</span>
                        <div class="text-lg font-bold text-rose-600 mt-1 font-mono">- Rp {{ number_format($totalKeluarHarian, 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-blue-900 to-slate-900 text-white rounded-2xl p-4 shadow-xs">
                        <span class="text-[11px] font-bold text-blue-200 uppercase">Saldo Akhir Hari Ini</span>
                        <div class="text-lg font-bold text-emerald-400 mt-1 font-mono">Rp {{ number_format($saldoAkhirHarian, 0, ',', '.') }}</div>
                    </div>
                </div>

                <!-- Tabel Transaksi Harian -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="font-bold text-slate-900 text-sm">Daftar Mutasi Kas: {{ \Carbon\Carbon::parse($tanggalHarian)->translatedFormat('l, d F Y') }}</h2>
                        <span class="text-xs text-slate-500">{{ $txHarian->count() }} Transaksi</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="py-3 px-4">No. Transaksi</th>
                                    <th class="py-3 px-4">Kategori</th>
                                    <th class="py-3 px-4">Keterangan</th>
                                    <th class="py-3 px-4 text-right">Kas Masuk</th>
                                    <th class="py-3 px-4 text-right">Kas Keluar</th>
                                    <th class="py-3 px-4 text-right">Saldo Berjalan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-mono">
                                @forelse($txHarian as $t)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="py-3 px-4 font-bold text-slate-900">{{ $t->no_transaksi }}</td>
                                        <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[11px] font-sans font-semibold {{ $t->jenis === 'masuk' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ $t->kategori_kas }}</span></td>
                                        <td class="py-3 px-4 font-sans text-slate-700">{{ $t->keterangan }}</td>
                                        <td class="py-3 px-4 text-right font-bold text-emerald-600">{{ $t->jenis === 'masuk' ? 'Rp ' . number_format($t->nominal, 0, ',', '.') : '-' }}</td>
                                        <td class="py-3 px-4 text-right font-bold text-rose-600">{{ $t->jenis === 'keluar' ? 'Rp ' . number_format($t->nominal, 0, ',', '.') : '-' }}</td>
                                        <td class="py-3 px-4 text-right font-bold text-slate-900">Rp {{ number_format($t->saldo_akhir, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-slate-400 font-sans">Tidak ada mutasi kas pada tanggal ini.</td>
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
                <!-- Filter Bulan & Aksi Cetak -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <form method="GET" action="{{ route('keuangan.laporan') }}" class="flex flex-wrap items-center gap-3">
                        <input type="hidden" name="tab" value="bulanan">
                        <label class="text-xs font-bold text-slate-700">Pilih Periode:</label>
                        <select name="bulan" class="text-xs rounded-xl border-slate-200 bg-slate-50 py-1.5 px-3">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $bulanBulanan == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                        <select name="tahun" class="text-xs rounded-xl border-slate-200 bg-slate-50 py-1.5 px-3">
                            @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                <option value="{{ $y }}" {{ $tahunBulanan == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="h-8 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">Tampilkan</button>
                    </form>

                    <a href="{{ route('keuangan.kas.cetak', ['mode' => 'bulanan', 'bulan' => $bulanBulanan, 'tahun' => $tahunBulanan]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Laporan Bulanan</span>
                    </a>
                </div>

                <!-- 3 KPI Card Bulanan -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white border border-emerald-200 rounded-2xl p-5 shadow-xs">
                        <span class="text-xs font-bold text-emerald-700 uppercase">Total Kas Masuk (Inflow)</span>
                        <div class="text-2xl font-black text-slate-900 mt-2 font-mono">Rp {{ number_format($totalMasukBulanan, 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-white border border-rose-200 rounded-2xl p-5 shadow-xs">
                        <span class="text-xs font-bold text-rose-700 uppercase">Total Kas Keluar (Outflow)</span>
                        <div class="text-2xl font-black text-rose-600 mt-2 font-mono">Rp {{ number_format($totalKeluarBulanan, 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                        <span class="text-xs font-bold text-slate-700 uppercase">Surplus / (Defisit) Bersih</span>
                        <div class="text-2xl font-black mt-2 font-mono {{ $surplusDefisitBulanan >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            Rp {{ number_format($surplusDefisitBulanan, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- Rincian per Kategori -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                        <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider text-emerald-700 mb-3">Breakdown Kas Masuk Per Kategori</h3>
                        <div class="space-y-2">
                            @forelse($rekapKategoriMasuk as $kat => $nom)
                                <div class="flex justify-between items-center text-xs py-1.5 border-b border-slate-100">
                                    <span class="font-medium text-slate-700">{{ $kat }}</span>
                                    <span class="font-bold font-mono text-emerald-600">Rp {{ number_format($nom, 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 py-2">Belum ada kas masuk periode ini.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                        <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider text-rose-700 mb-3">Breakdown Kas Keluar Per Kategori</h3>
                        <div class="space-y-2">
                            @forelse($rekapKategoriKeluar as $kat => $nom)
                                <div class="flex justify-between items-center text-xs py-1.5 border-b border-slate-100">
                                    <span class="font-medium text-slate-700">{{ $kat }}</span>
                                    <span class="font-bold font-mono text-rose-600">Rp {{ number_format($nom, 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 py-2">Belum ada kas keluar periode ini.</p>
                            @endforelse
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
                <!-- Filter Inkaso & Aksi Cetak -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <form method="GET" action="{{ route('keuangan.laporan') }}" class="flex flex-wrap items-center gap-3">
                        <input type="hidden" name="tab" value="inkaso">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-700">
                            <span>Periode:</span>
                            <input type="date" name="dari" value="{{ $dariInkaso }}" class="text-xs rounded-xl border-slate-200 bg-slate-50 py-1.5 px-3">
                            <span>s/d</span>
                            <input type="date" name="sampai" value="{{ $sampaiInkaso }}" class="text-xs rounded-xl border-slate-200 bg-slate-50 py-1.5 px-3">
                        </div>

                        <select name="petugas_id" class="text-xs rounded-xl border-slate-200 bg-slate-50 py-1.5 px-3">
                            <option value="">-- Semua Petugas Inkaso --</option>
                            @foreach($daftarPetugas as $p)
                                <option value="{{ $p->id }}" {{ $petugasId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="h-8 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">Tampilkan</button>
                    </form>

                    <a href="{{ route('keuangan.inkaso.rekap.cetak', ['dari' => $dariInkaso, 'sampai' => $sampaiInkaso, 'petugas_id' => $petugasId]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Rekapitulasi Inkaso</span>
                    </a>
                </div>

                <!-- 4 KPI Card Inkaso -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
                        <span class="text-[11px] font-bold text-slate-500 uppercase">Target Piutang Ditugaskan</span>
                        <div class="text-lg font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalTargetInkaso, 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-white border border-emerald-200 rounded-2xl p-4 shadow-xs">
                        <span class="text-[11px] font-bold text-emerald-700 uppercase">Total Berhasil Ditagih (Masuk Kas)</span>
                        <div class="text-lg font-bold text-emerald-600 mt-1 font-mono">Rp {{ number_format($totalTertagihInkaso, 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-white border border-amber-200 rounded-2xl p-4 shadow-xs">
                        <span class="text-[11px] font-bold text-amber-700 uppercase">Sisa Piutang Lapangan</span>
                        <div class="text-lg font-bold text-amber-600 mt-1 font-mono">Rp {{ number_format($totalSisaPiutangInkaso, 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-white border border-blue-200 rounded-2xl p-4 shadow-xs">
                        <span class="text-[11px] font-bold text-blue-700 uppercase">Efektivitas Penagihan</span>
                        <div class="text-lg font-bold text-blue-600 mt-1 font-mono">{{ $persenTertagih }}%</div>
                    </div>
                </div>

                <!-- Tabel Rekapitulasi Penugasan Inkaso -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="font-bold text-slate-900 text-sm">Daftar Penugasan &amp; Hasil Penagihan Petugas</h2>
                        <span class="text-xs text-slate-500">{{ $penugasanInkasoList->count() }} Penugasan</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="py-3 px-4">No. Tugas</th>
                                    <th class="py-3 px-4">Tanggal</th>
                                    <th class="py-3 px-4">Petugas Kolektor</th>
                                    <th class="py-3 px-4">No. Faktur &amp; Customer</th>
                                    <th class="py-3 px-4 text-right">Nilai Tagihan</th>
                                    <th class="py-3 px-4 text-right">Setoran Masuk</th>
                                    <th class="py-3 px-4 text-right">Sisa Piutang</th>
                                    <th class="py-3 px-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($penugasanInkasoList as $p)
                                    @php
                                        $tertagih = (float) $p->pembayaran->sum('nominal_bayar');
                                        $sisa = $p->faktur ? (float) $p->faktur->sisa_piutang : 0;
                                        $totalTagihan = $p->faktur ? (float) $p->faktur->total_tagihan : 0;
                                    @endphp
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="py-3 px-4 font-bold text-slate-900 font-mono">{{ $p->no_tugas }}</td>
                                        <td class="py-3 px-4">{{ \Carbon\Carbon::parse($p->tanggal_tugas)->translatedFormat('d M Y') }}</td>
                                        <td class="py-3 px-4 font-semibold text-slate-800">{{ $p->petugas->name ?? '-' }}</td>
                                        <td class="py-3 px-4">
                                            <div class="font-bold text-slate-900">{{ $p->faktur->no_faktur ?? '-' }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $p->faktur->customer->nama_customer ?? '-' }}</div>
                                        </td>
                                        <td class="py-3 px-4 text-right font-mono font-medium">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                                        <td class="py-3 px-4 text-right font-mono font-bold text-emerald-600">Rp {{ number_format($tertagih, 0, ',', '.') }}</td>
                                        <td class="py-3 px-4 text-right font-mono font-bold text-amber-600">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                                        <td class="py-3 px-4 text-center">
                                            @if($p->status_tugas === 'selesai' || $sisa == 0)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Selesai (Lunas)</span>
                                            @elseif($p->status_tugas === 'batal')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Dibatalkan</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Berjalan</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-8 text-center text-slate-400">Tidak ada data penugasan inkaso pada periode ini.</td>
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
