<x-app-layout>
    <div class="space-y-6">

        <!-- Header Pemantauan Faktur Iklan (60% Putih, 30% Biru, 10% Kuning) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <h1 class="text-xl font-extrabold text-blue-950 tracking-tight">
                            PEMANTAUAN FAKTUR IKLAN &amp; PIUTANG
                        </h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Monitoring data transaksi iklan otomatis dari Front Office &bull; Rekapitulasi piutang &amp; cetak surat penugasan penagihan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50/70 border border-blue-200 text-blue-900 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>Faktur Otomatis dari Iklan &amp; Sirkulasi</span>
                </span>
                <a href="{{ route('keuangan.faktur.cetak-rekap', [
                    'mode' => $tab === 'rekap_bulan' ? 'bulan' : 'tanggal',
                    'status' => $statusSubmenu ?? 'keseluruhan',
                    'tahun' => $tahun ?? now()->year,
                    'bulan' => $bulan ?? now()->month,
                    'dari_tanggal' => $dariTanggal ?? now()->startOfMonth()->toDateString(),
                    'sampai_tanggal' => $sampaiTanggal ?? now()->toDateString()
                ]) }}" target="_blank" class="h-9 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold flex items-center shadow-xs transition cursor-pointer">
                    <span>Cetak Rekap Dot-Matrix (15 Kolom)</span>
                </a>
            </div>
        </div>

        <!-- Tabs Navigasi Submodul Faktur (Iklan vs Sirkulasi) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
            @include('pages.keuangan.faktur.partials.tabs', ['activeTab' => $tab, 'activeCategory' => 'iklan'])
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-xs font-bold flex items-center justify-between">
                <span>✓ {{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold">
                ✕ {{ session('error') }}
            </div>
        @endif

        <!-- Indikator Submenu Aktif dari Sidebar -->
        <div class="bg-white border border-slate-200 rounded-2xl px-5 py-3 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider font-mono">
                    Halaman Aktif: <strong class="text-blue-700 font-black">{{ strtoupper(str_replace('_', ' ', $tab)) }}</strong>
                </span>
            </div>
            <span class="text-[11px] text-slate-400 font-medium">
                Gunakan menu di sidebar (sebelah kiri) untuk berpindah antar kategori faktur &amp; rekap.
            </span>
        </div>

        <!-- KPI Ringkas Modul Iklan (Putih 60%, Biru 30%, Kuning 10%) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Total Faktur (Putih & Biru) -->
            <div class="bg-white border border-blue-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 font-mono">Total Faktur Iklan</span>
                <div class="mt-2 text-2xl font-black text-slate-900 tracking-tight">
                    {{ $totalFakturCount }} <span class="text-xs font-medium text-slate-500">Dokumen</span>
                </div>
                <div class="mt-2 text-xs text-slate-500 flex items-center justify-between">
                    <span>Omset Kumulatif:</span>
                    <span class="font-bold text-blue-950 font-mono">Rp {{ number_format($totalTagihanSum, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Card 2: Faktur Lunas (Putih & Biru) -->
            <div class="bg-white border border-blue-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 font-mono">Faktur Lunas (100%)</span>
                <div class="mt-2 text-2xl font-black text-blue-600 tracking-tight">
                    {{ $fakturLunasCount }} <span class="text-xs font-medium text-slate-500">Faktur</span>
                </div>
                <div class="mt-2 text-xs text-blue-900 font-semibold flex items-center justify-between">
                    <span>Terbayar:</span>
                    <span class="font-mono">Rp {{ number_format($totalTerbayarSum, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Card 3: Piutang Beredar (10% Aksen Kuning / Amber) -->
            <div class="bg-amber-50/50 border border-amber-300 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-800 font-mono">Piutang Beredar</span>
                <div class="mt-2 text-2xl font-black text-amber-600 tracking-tight">
                    {{ $fakturPiutangCount }} <span class="text-xs font-medium text-amber-800">Faktur</span>
                </div>
                <div class="mt-2 text-xs text-amber-900 font-bold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    <span>Wajib ditagih oleh Inkaso</span>
                </div>
            </div>

            <!-- Card 4: Sisa Saldo Piutang (Kuning Aksen & Biru) -->
            <div class="bg-white border border-blue-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-900 font-mono">Sisa Saldo Piutang Iklan</span>
                <div class="mt-2 text-2xl font-black text-amber-600 tracking-tight font-mono">
                    Rp {{ number_format($totalPiutangSum, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-slate-500 flex items-center justify-between">
                    <span>Status Inkaso:</span>
                    <span class="font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md">Siap Ditugaskan</span>
                </div>
            </div>
        </div>

        <!-- Filter & Submenu Bar (Putih & Biru) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('keuangan.faktur.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <!-- Pencarian Global -->
                <div class="sm:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Cari No. Faktur / Pemasang</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik kata kunci..." 
                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 bg-white text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-hidden">
                </div>

                <!-- Submenu Status: LUNAS, BELUM LUNAS, KESELURUHAN -->
                @if(in_array($tab, ['keseluruhan', 'susulan', 'rekap_tanggal', 'rekap_bulan']))
                    <div class="sm:col-span-3">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Sub-Kategori Status</label>
                        <select name="status" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 bg-white text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-hidden font-bold">
                            <option value="keseluruhan" {{ $statusSubmenu === 'keseluruhan' ? 'selected' : '' }}>KESELURUHAN (SEMUA)</option>
                            <option value="lunas" {{ $statusSubmenu === 'lunas' ? 'selected' : '' }}>LUNAS</option>
                            <option value="belum_lunas" {{ $statusSubmenu === 'belum_lunas' ? 'selected' : '' }}>BELUM LUNAS (PIUTANG)</option>
                        </select>
                    </div>
                @endif

                <!-- Filter Tanggal jika Tab Rekap Tanggal -->
                @if($tab === 'rekap_tanggal')
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Dari Tanggal</label>
                        <input type="date" name="dari_tanggal" value="{{ $dariTanggal }}" 
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 bg-white text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-hidden">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Sampai Tanggal</label>
                        <input type="date" name="sampai_tanggal" value="{{ $sampaiTanggal }}" 
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 bg-white text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-hidden">
                    </div>
                @endif

                <!-- Filter Bulan & Tahun jika Tab Rekap Bulan / Stock Opname -->
                @if(in_array($tab, ['rekap_bulan', 'stock_opname', 'tunai']))
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Bulan</label>
                        <select name="bulan" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 bg-white text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-hidden">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Tahun</label>
                        <select name="tahun" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 bg-white text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-hidden">
                            @for ($y = 2024; $y <= 2028; $y++)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                @endif

                <!-- Filter Khusus Piutang (Status Inkaso & Umur Piutang) -->
                @if($tab === 'piutang')
                    <div class="sm:col-span-3">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Penugasan Inkaso</label>
                        <select name="status_inkaso" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 bg-white text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-hidden">
                            <option value="">Semua Penugasan</option>
                            <option value="belum" {{ request('status_inkaso') === 'belum' ? 'selected' : '' }}>Belum Ditugaskan (Antrean)</option>
                            <option value="sudah" {{ request('status_inkaso') === 'sudah' ? 'selected' : '' }}>Sudah Ditugaskan ke Petugas</option>
                        </select>
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Umur Piutang (Aging)</label>
                        <select name="aging" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 bg-white text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-hidden">
                            <option value="">Semua Umur Piutang</option>
                            <option value="0-30" {{ request('aging') === '0-30' ? 'selected' : '' }}>0 - 30 Hari (Lancar)</option>
                            <option value="31-60" {{ request('aging') === '31-60' ? 'selected' : '' }}>31 - 60 Hari (Perhatian)</option>
                            <option value="61-90" {{ request('aging') === '61-90' ? 'selected' : '' }}>61 - 90 Hari (Kritis)</option>
                            <option value=">90" {{ request('aging') === '>90' ? 'selected' : '' }}>&gt; 90 Hari (Macet)</option>
                        </select>
                    </div>
                @endif

                <div class="sm:col-span-2 flex items-center gap-1.5">
                    <button type="submit" class="w-full h-9 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                        Tampilkan
                    </button>
                    @if(request()->hasAny(['q', 'status', 'dari_tanggal', 'sampai_tanggal', 'status_inkaso', 'aging']))
                        <a href="{{ route('keuangan.faktur.index', ['tab' => $tab]) }}" class="h-9 px-3 bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 rounded-xl text-xs font-bold flex items-center justify-center transition" title="Reset Filter">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Data Faktur Iklan (60% Putih, 30% Biru, 10% Kuning) -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 bg-blue-50/50 border-b border-blue-100 flex items-center justify-between">
                <div class="text-xs font-black text-blue-950 uppercase tracking-wider font-mono">
                    DAFTAR PEMANTAUAN FAKTUR IKLAN &bull; {{ strtoupper(str_replace('_', ' ', $tab)) }}
                </div>
                <div class="text-xs text-slate-500 font-medium">
                    Total: <strong class="text-blue-950 font-bold">{{ $fakturs->total() }}</strong> faktur ditemukan
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-blue-50/90 text-[11px] uppercase tracking-wider text-blue-950 font-black border-b border-blue-200">
                        <tr>
                            <th class="py-3 px-4">No. Faktur</th>
                            <th class="py-3 px-4">Pemasang / Customer</th>
                            <th class="py-3 px-4">Tgl Muat / Tgl Faktur</th>
                            <th class="py-3 px-4">Sales / AE</th>
                            <th class="py-3 px-4 text-right">Tagihan (Rp)</th>
                            <th class="py-3 px-4 text-right">Terbayar</th>
                            <th class="py-3 px-4 text-right">Sisa Piutang</th>
                            <th class="py-3 px-4 text-center">Status Bayar</th>
                            <th class="py-3 px-4 text-center">Surat Penugasan</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($fakturs as $item)
                            <tr class="hover:bg-blue-50/30 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                    <a href="{{ route('keuangan.faktur.show', $item->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                        {{ $item->no_faktur }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 uppercase">{{ $item->customer->nama_customer ?? 'UMUM' }}</div>
                                    <div class="text-[10px] text-slate-400 truncate max-w-xs">{{ $item->customer->alamat ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-[11px] font-mono">
                                    {{ $item->tanggal_faktur->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-700">
                                    {{ $item->sales_nama ?: '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-slate-900 font-mono">
                                    {{ number_format($item->total_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-blue-700 font-mono">
                                    {{ number_format($item->total_terbayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold font-mono {{ $item->sisa_piutang > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                    {{ number_format($item->sisa_piutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($item->status === 'lunas')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                            ✓ LUNAS
                                        </span>
                                    @elseif($item->status === 'cicilan')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-300">
                                            CICILAN
                                        </span>
                                    @else
                                        <!-- 10% Kuning Aksen untuk Piutang Belum Bayar -->
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-950 border border-amber-300">
                                            PIUTANG
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($item->sisa_piutang > 0)
                                        <!-- Tombol Aksen Kuning 10% -->
                                        <button type="button" 
                                                onclick="bukaModalSuratTugas('{{ $item->id }}', '{{ $item->no_faktur }}', '{{ number_format($item->sisa_piutang, 0, ',', '.') }}', '{{ addslashes($item->customer->nama_customer ?? 'UMUM') }}')"
                                                class="inline-flex items-center px-3 py-1.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-amber-950 border border-amber-500/30 text-[10px] font-black shadow-2xs transition cursor-pointer"
                                                title="Cetak Surat Penugasan Penagihan Lapangan">
                                            <span>Cetak Surat Tugas</span>
                                        </button>
                                    @else
                                        <span class="text-slate-300 font-mono text-[10px]">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Tombol Cetak Slip Order -->
                                        <a href="{{ route('keuangan.faktur.cetak', $item->id) }}" target="_blank" 
                                           class="px-2.5 py-1 text-[11px] font-bold text-slate-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg border border-slate-200 transition" 
                                           title="Cetak Order Transaksi Iklan (Slip Foto)">
                                            Cetak
                                        </a>

                                        <!-- Tombol Detail -->
                                        <a href="{{ route('keuangan.faktur.show', $item->id) }}" 
                                           class="px-2.5 py-1 text-[11px] font-bold text-blue-700 hover:text-blue-800 hover:bg-blue-50 rounded-lg border border-blue-200 transition" 
                                           title="Rincian Faktur">
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-400">
                                    <p class="text-sm font-semibold text-slate-600">Tidak ada faktur pemuatan iklan pada kriteria ini.</p>
                                    <p class="text-xs text-slate-400 mt-1">Data faktur iklan otomatis tersinkron dari transaksi Front Office Iklan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($fakturs->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $fakturs->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Modal Input Nama Petugas Penagih (Inkaso) -->
    <div id="modalSuratTugas" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in duration-150">
            <div class="flex items-start justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Cetak Surat Penugasan Penagihan</h3>
                    <p class="text-[11px] text-slate-500">Penugasan Penagihan Lapangan (Inkaso)</p>
                </div>
                <button type="button" onclick="tutupModalSuratTugas()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">✕</button>
            </div>

            <div class="mt-4 space-y-3">
                <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1">
                    <div class="flex justify-between">
                        <span class="text-amber-800 font-medium">No Faktur:</span>
                        <strong class="font-mono text-slate-900" id="modalNoFaktur">-</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-amber-800 font-medium">Pelanggan:</span>
                        <span class="font-bold truncate max-w-[200px]" id="modalCustomer">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-amber-800 font-medium">Sisa Piutang:</span>
                        <strong class="text-amber-700 font-black font-mono text-sm" id="modalSisaPiutang">Rp 0</strong>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Pilih Petugas Penagih Lapangan (Inkaso):
                    </label>
                    <select id="selectModalPetugas" 
                            onchange="handleSelectModalPetugas(this.value)" 
                            class="w-full h-10 px-3 rounded-xl border border-slate-300 text-xs text-slate-900 font-bold bg-white focus:ring-2 focus:ring-blue-500 focus:outline-hidden shadow-2xs cursor-pointer">
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
                        <button type="button" onclick="setPetugasModal('Ahmad Supardi')" class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-900 text-[11px] font-semibold border border-blue-200 transition cursor-pointer">
                            Ahmad Supardi
                        </button>
                        <button type="button" onclick="setPetugasModal('Rudi Hermawan')" class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-900 text-[11px] font-semibold border border-blue-200 transition cursor-pointer">
                            Rudi Hermawan
                        </button>
                        <button type="button" onclick="setPetugasModal('Dadan Ramdani')" class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-900 text-[11px] font-semibold border border-blue-200 transition cursor-pointer">
                            Dadan Ramdani
                        </button>
                        <button type="button" onclick="setPetugasModal('Yudi Permana')" class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-900 text-[11px] font-semibold border border-blue-200 transition cursor-pointer">
                            Yudi Permana
                        </button>
                    </div>

                    <!-- Field Input jika memilih opsi Ketik Manual -->
                    <div id="containerInputManual" class="mt-2.5 hidden">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Petugas Lainnya:</label>
                        <input type="text" 
                               id="inputModalPetugas" 
                               placeholder="Ketik nama petugas..." 
                               onkeydown="if(event.key === 'Enter') prosesCetakSuratTugas();"
                               class="w-full h-9 px-3 rounded-xl border border-blue-300 text-xs text-slate-900 font-bold focus:ring-2 focus:ring-blue-500 focus:outline-hidden shadow-2xs">
                    </div>

                    <p class="text-[11px] text-slate-400 mt-2 italic">
                        *Nama hanya dicetak pada lembar fisik penugasan (tanpa membuat akun user di database).
                    </p>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2">
                <button type="button" onclick="tutupModalSuratTugas()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition cursor-pointer">
                    Batal
                </button>
                <button type="button" onclick="prosesCetakSuratTugas()" class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-amber-950 font-black text-xs shadow-xs transition flex items-center cursor-pointer">
                    <span>Buka &amp; Cetak Surat Tugas</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentSuratTugasFakturId = null;
        let selectedPetugasNama = '';

        function bukaModalSuratTugas(id, noFaktur, sisaPiutang, customer) {
            currentSuratTugasFakturId = id;
            document.getElementById('modalNoFaktur').textContent = noFaktur;
            document.getElementById('modalCustomer').textContent = customer || 'UMUM';
            document.getElementById('modalSisaPiutang').textContent = 'Rp ' + sisaPiutang;
            
            // Reset pilihan ke default pertama (Ahmad Supardi)
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
            if (!currentSuratTugasFakturId) return;
            
            const selectEl = document.getElementById('selectModalPetugas');
            let finalNama = selectedPetugasNama;
            
            if (selectEl.value === '__manual__') {
                finalNama = document.getElementById('inputModalPetugas').value.trim();
            } else if (selectEl.value === '__blank__') {
                finalNama = '';
            }

            const queryParam = finalNama ? "?petugas=" + encodeURIComponent(finalNama) : "";
            const baseUrl = "{{ url('keuangan/faktur') }}/" + currentSuratTugasFakturId + "/surat-tugas";
            window.open(baseUrl + queryParam, '_blank');
            tutupModalSuratTugas();
        }
    </script>
</x-app-layout>
