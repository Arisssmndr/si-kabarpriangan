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
                        Monitoring data transaksi iklan otomatis dari Front Office &bull; Rekapitulasi piutang &amp; umur piutang (aging)
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
                    <span>Status Piutang:</span>
                    <span class="font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md">Perlu Ditagih</span>
                </div>
            </div>
        </div>

        <!-- Filter & Submenu Bar (Putih & Biru) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('keuangan.faktur.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <!-- Pencarian Global -->
                <div class="{{ $tab === 'piutang' ? 'sm:col-span-6' : 'sm:col-span-3' }}">
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

                <!-- Filter Khusus Piutang (Umur Piutang / Aging) -->
                @if($tab === 'piutang')
                    <div class="sm:col-span-4">
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
                    @if(request()->hasAny(['q', 'status', 'dari_tanggal', 'sampai_tanggal', 'aging']))
                        <a href="{{ route('keuangan.faktur.index', ['tab' => $tab]) }}" class="h-9 px-3 bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 rounded-xl text-xs font-bold flex items-center justify-center transition" title="Reset Filter">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Data Faktur Iklan (60% Putih, 30% Biru, 10% Kuning) -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 bg-blue-50/50 border-b border-blue-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="text-xs font-black text-blue-950 uppercase tracking-wider font-mono">
                    DAFTAR PEMANTAUAN FAKTUR IKLAN &bull; {{ strtoupper(str_replace('_', ' ', $tab)) }}
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    <div class="text-xs text-slate-500 font-medium">
                        Total: <strong class="text-blue-950 font-bold">{{ $fakturs->total() }}</strong> faktur ditemukan
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-blue-50/90 text-[11px] uppercase tracking-wider text-blue-950 font-black border-b border-blue-200">
                        <tr>
                            <th class="py-3 px-3 text-center w-12">No</th>
                            <th class="py-3 px-4">No. Faktur</th>
                            <th class="py-3 px-4">Pemasang / Customer</th>
                            <th class="py-3 px-4">Tgl Muat / Tgl Faktur</th>
                            <th class="py-3 px-4">Sales / AE</th>
                            <th class="py-3 px-4 text-right">Tagihan (Rp)</th>
                            <th class="py-3 px-4 text-right">Terbayar</th>
                            <th class="py-3 px-4 text-right">Sisa Piutang</th>
                            <th class="py-3 px-4 text-center">Status Bayar</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($fakturs as $item)
                            <tr class="hover:bg-blue-50/30 transition">
                                <td class="py-3.5 px-3 text-center font-mono text-slate-500 text-xs">
                                    {{ $loop->iteration + ($fakturs->firstItem() ? $fakturs->firstItem() - 1 : 0) }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <a href="{{ route('keuangan.faktur.show', $item->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                             {{ $item->no_faktur }}
                                        </a>
                                        @if($item->tipe_faktur === 'bonus' || $item->is_bonus)
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-black bg-purple-100 text-purple-800 border border-purple-200" title="Iklan Bonus / Barter / Tidak Bernilai">
                                                BONUS
                                            </span>
                                        @elseif($item->tipe_faktur === 'stopper')
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-black bg-rose-100 text-rose-800 border border-rose-200">
                                                STOPPER
                                            </span>
                                        @elseif($item->tipe_faktur === 'susulan')
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-black bg-sky-100 text-sky-800 border border-sky-200">
                                                SUSULAN
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 uppercase">{{ $item->customer->nama_customer ?? 'UMUM' }}</div>
                                    <div class="text-[10px] text-slate-400 truncate max-w-xs">{{ $item->customer->alamat ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-[11px] font-mono">
                                    {{ $item->tanggal_faktur->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-700">
                                    <div>{{ $item->sales_nama ?: '-' }}</div>
                                    @if($item->kh_insentif_nominal > 0)
                                        <div class="text-[10px] font-mono text-emerald-700 font-semibold mt-0.5" title="Nilai KH / Insentif">
                                            KH: Rp {{ number_format($item->kh_insentif_nominal, 0, ',', '.') }}
                                        </div>
                                    @endif
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
</x-app-layout>
