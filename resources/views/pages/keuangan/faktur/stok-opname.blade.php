<x-app-layout>
    <div class="space-y-5">

        <!-- 1. Header Halaman (Standar Divisi Iklan 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-9 sm:h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Stok Opname &amp; Rekap Seri Faktur Iklan
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 shrink-0">
                            Tahun {{ $tahun }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Pemantauan pemakaian nomor seri faktur, performa Account Executive (AE), rekapitulasi berkala, dan saldo piutang
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('keuangan.faktur.stok-opname.cetak', request()->query()) }}" target="_blank"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 font-semibold rounded-xl text-sm flex items-center gap-2 shadow-2xs">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-slate-100 border border-slate-300 text-slate-800 font-semibold rounded-xl text-sm flex items-center gap-2 shadow-2xs">
                <span>✕</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- 2. KPI Summary Cards (Standar Divisi Iklan 60-30-10) -->
        @php
            $kpiTagihan = $kpiAE['total_tagihan'] ?? 0;
            $kpiTerbayar = $kpiAE['total_terbayar'] ?? 0;
            $kpiPiutang = $kpiAE['total_piutang'] ?? 0;
            $kpiFaktur = $kpiAE['total_faktur'] ?? 0;
            $kpiSeriAwal = $kpiAE['seri_awal'] ?? '-';
            $kpiSeriAkhir = $kpiAE['seri_akhir'] ?? '-';
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Total Tagihan (Omset)</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($kpiTagihan, 0, ',', '.') }}
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    Volume: <span class="font-bold text-slate-800 font-mono">{{ number_format($kpiFaktur) }}</span> Faktur
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Rentang Nomor Seri</span>
                <p class="text-sm font-bold text-kp-blue-700 mt-2 font-mono truncate">
                    {{ $kpiSeriAwal }} &bull; {{ $kpiSeriAkhir }}
                </p>
                <div class="mt-2 text-xs text-slate-400">
                    Blangko seri terdaftar
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Kas Terbayar</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($kpiTerbayar, 0, ',', '.') }}
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    Realisasi tunai &amp; transfer
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Sisa Saldo Piutang</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($kpiPiutang, 0, ',', '.') }}
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    {{ $kpiTagihan > 0 ? round(($kpiPiutang / $kpiTagihan) * 100, 1) : 0 }}% belum terlunasi
                </div>
            </div>
        </div>

        <!-- 3. Sub-Navigasi 7 Dimensi Laporan Stok Opname -->
        <div class="bg-white border border-slate-200 rounded-xl p-2.5 shadow-xs">
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <a href="{{ route('keuangan.faktur.stok-opname', array_merge(request()->query(), ['tab' => 'per_ae_bulan'])) }}" 
                   class="px-3.5 py-2 rounded-xl font-bold shrink-0 transition {{ $tab === 'per_ae_bulan' ? 'bg-kp-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50' }}">
                    <span>Per AE / Bulan</span>
                </a>
                <a href="{{ route('keuangan.faktur.stok-opname', array_merge(request()->query(), ['tab' => 'per_ae_tahun'])) }}" 
                   class="px-3.5 py-2 rounded-xl font-bold shrink-0 transition {{ $tab === 'per_ae_tahun' ? 'bg-kp-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50' }}">
                    <span>Per AE / Tahun</span>
                </a>
                <a href="{{ route('keuangan.faktur.stok-opname', array_merge(request()->query(), ['tab' => 'rekap_ae_bulan'])) }}" 
                   class="px-3.5 py-2 rounded-xl font-bold shrink-0 transition {{ $tab === 'rekap_ae_bulan' ? 'bg-kp-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50' }}">
                    <span>Rekap AE / Bulan</span>
                </a>
                <a href="{{ route('keuangan.faktur.stok-opname', array_merge(request()->query(), ['tab' => 'rekap_ae_tahun'])) }}" 
                   class="px-3.5 py-2 rounded-xl font-bold shrink-0 transition {{ $tab === 'rekap_ae_tahun' ? 'bg-kp-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50' }}">
                    <span>Rekap AE / Tahun</span>
                </a>
                <a href="{{ route('keuangan.faktur.stok-opname', array_merge(request()->query(), ['tab' => 'per_bulan'])) }}" 
                   class="px-3.5 py-2 rounded-xl font-bold shrink-0 transition {{ $tab === 'per_bulan' ? 'bg-kp-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50' }}">
                    <span>Per Bulan</span>
                </a>
                <a href="{{ route('keuangan.faktur.stok-opname', array_merge(request()->query(), ['tab' => 'per_tahun'])) }}" 
                   class="px-3.5 py-2 rounded-xl font-bold shrink-0 transition {{ $tab === 'per_tahun' ? 'bg-kp-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50' }}">
                    <span>Per Tahun</span>
                </a>
                <a href="{{ route('keuangan.faktur.stok-opname', array_merge(request()->query(), ['tab' => 'per_pemasang'])) }}" 
                   class="px-3.5 py-2 rounded-xl font-bold shrink-0 transition {{ $tab === 'per_pemasang' ? 'bg-kp-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50' }}">
                    <span>Per Pemasang</span>
                </a>
            </div>
        </div>

        <!-- Toolbar Pencarian & Filter Sesuai Tab Terpilih (h-11 rounded-xl) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('keuangan.faktur.stok-opname') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tahun</label>
                    <select name="tahun" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs font-semibold">
                        @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                            <option value="{{ $y }}" {{ ($tahun ?? date('Y')) == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </div>

                @if(in_array($tab, ['per_ae_bulan', 'rekap_ae_bulan', 'per_pemasang']))
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Bulan {{ $tab === 'per_pemasang' ? '(Opsional)' : '' }}
                        </label>
                        <select name="bulan" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs font-semibold">
                            @if($tab === 'per_pemasang')
                                <option value="">Semua Bulan (Setahun)</option>
                            @endif
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ ($bulan ?? date('n')) == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                @endif

                @if(in_array($tab, ['per_ae_bulan', 'per_ae_tahun']))
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Account Executive (AE)</label>
                        <select name="ae" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs font-semibold">
                            @if($tab === 'per_ae_bulan')
                                <option value="semua" {{ ($selectedAE ?? '') === 'semua' ? 'selected' : '' }}>-- Semua Account Executive --</option>
                            @endif
                            @foreach($listAE as $ae)
                                <option value="{{ $ae }}" {{ ($selectedAE ?? '') === $ae ? 'selected' : '' }}>
                                    {{ $ae }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if(in_array($tab, ['per_ae_bulan', 'per_pemasang']))
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            {{ $tab === 'per_pemasang' ? 'Cari Pemasang / Alamat' : 'Cari No Seri / Pemasang' }}
                        </label>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik kata kunci pencarian..."
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                @endif

                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit"
                            class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->anyFilled(['q', 'ae', 'bulan', 'tahun']))
                        <a href="{{ route('keuangan.faktur.stok-opname', ['tab' => $tab]) }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center" title="Reset Filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 4. Pure Data Table Sesuai Tab Terpilih (Kolom Terpisah & Palet 60-30-10) -->

        {{-- 1. TAB: PER AE / BULAN --}}
        @if($tab === 'per_ae_bulan')
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-3.5">No. Seri Faktur</th>
                                <th class="py-3 px-3.5">Tanggal</th>
                                <th class="py-3 px-3.5">Pemasang</th>
                                <th class="py-3 px-3.5">Alamat</th>
                                <th class="py-3 px-3.5 text-center">Tipe</th>
                                <th class="py-3 px-3.5 text-right w-32">Tagihan</th>
                                <th class="py-3 px-3.5 text-right w-32">Dibayar</th>
                                <th class="py-3 px-3.5 text-right w-32">Piutang</th>
                                <th class="py-3 px-3.5 text-center">Status</th>
                                <th class="py-3 px-3.5 text-center w-16">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @forelse($fakturs as $idx => $faktur)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $fakturs->firstItem() + $idx }}</td>
                                    <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                        <a href="{{ route('keuangan.faktur.show', $faktur->id) }}" class="hover:underline">
                                            {{ $faktur->no_faktur }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-slate-600 font-mono font-normal">
                                        {{ $faktur->tanggal_faktur ? $faktur->tanggal_faktur->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-semibold text-slate-900 uppercase">
                                        {{ $faktur->customer->nama_customer ?? 'UMUM' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-slate-600 font-normal truncate max-w-xs" title="{{ $faktur->customer->alamat ?? '-' }}">
                                        {{ $faktur->customer->alamat ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700 uppercase border border-slate-200">
                                            {{ $faktur->tipe_faktur ?? 'reguler' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($faktur->total_tagihan, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($faktur->total_terbayar, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        @if($faktur->sisa_piutang > 0)
                                            <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                                <span class="text-slate-400 font-normal select-none">Rp</span>
                                                <span class="font-normal text-slate-700 tabular-nums">{{ number_format($faktur->sisa_piutang, 0, ',', '.') }}</span>
                                            </div>
                                        @else
                                            <div class="text-right font-mono text-sm text-slate-400 font-normal">Rp 0</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        @if($faktur->status === 'lunas')
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200">
                                                Lunas
                                            </span>
                                        @elseif($faktur->status === 'cicilan')
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
                                        <a href="{{ route('keuangan.faktur.show', $faktur->id) }}" 
                                           class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-300 transition">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="py-12 text-center text-slate-400">
                                        Tidak ada dokumen faktur pada filter ini.
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

        {{-- 2. TAB: PER AE / TAHUN --}}
        @elseif($tab === 'per_ae_tahun')
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5">Bulan</th>
                                <th class="py-3 px-3.5 text-center">Faktur Terbit</th>
                                <th class="py-3 px-3.5">Seri Awal</th>
                                <th class="py-3 px-3.5">Seri Akhir</th>
                                <th class="py-3 px-3.5 text-center">Reguler</th>
                                <th class="py-3 px-3.5 text-center">Stopper</th>
                                <th class="py-3 px-3.5 text-center">Bonus</th>
                                <th class="py-3 px-3.5 text-right w-32">Tagihan</th>
                                <th class="py-3 px-3.5 text-right w-32">Dibayar</th>
                                <th class="py-3 px-3.5 text-right w-32">Piutang</th>
                                <th class="py-3 px-3.5 text-center w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @php
                                $totF = 0; $totT = 0; $totB = 0; $totP = 0;
                            @endphp
                            @forelse($rekapBulananAE as $row)
                                @php
                                    $namaBulan = \Carbon\Carbon::create()->month((int)$row->bulan)->translatedFormat('F');
                                    $totF += $row->total_faktur;
                                    $totT += $row->total_tagihan;
                                    $totB += $row->total_terbayar;
                                    $totP += $row->total_piutang;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                        {{ $namaBulan }} {{ $tahun }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center font-bold text-slate-800">
                                        {{ $row->total_faktur }} Lembar
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono text-xs text-kp-blue-700 font-semibold">
                                        {{ $row->seri_awal ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono text-xs text-kp-blue-700 font-semibold">
                                        {{ $row->seri_akhir ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">{{ $row->total_reguler }}</td>
                                    <td class="py-3.5 px-3.5 text-center">{{ $row->total_stopper }}</td>
                                    <td class="py-3.5 px-3.5 text-center">{{ $row->total_bonus }}</td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($row->total_tagihan, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($row->total_terbayar, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-normal text-slate-700 tabular-nums">{{ number_format($row->total_piutang, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        <a href="{{ route('keuangan.faktur.stok-opname', ['tab' => 'per_ae_bulan', 'tahun' => $tahun, 'bulan' => $row->bulan, 'ae' => $selectedAE]) }}" 
                                           class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-300 transition">
                                            Rincian →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="py-12 text-center text-slate-400">
                                        Tidak ada transaksi faktur untuk AE ini pada tahun {{ $tahun }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($rekapBulananAE->isNotEmpty())
                            <tfoot>
                                <tr class="bg-slate-50 font-bold border-t border-slate-200 text-slate-900">
                                    <td class="py-3.5 px-3.5">TOTAL TAHUN {{ $tahun }}</td>
                                    <td class="py-3.5 px-3.5 text-center">{{ $totF }} Lembar</td>
                                    <td class="py-3.5 px-3.5 text-center">-</td>
                                    <td class="py-3.5 px-3.5 text-center">-</td>
                                    <td colspan="3" class="py-3.5 px-3.5 text-center">-</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">Rp {{ number_format($totT, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-kp-blue-700 tabular-nums">Rp {{ number_format($totB, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">Rp {{ number_format($totP, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        {{-- 3. TAB: REKAP AE / BULAN --}}
        @elseif($tab === 'rekap_ae_bulan')
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-3.5">Account Executive (AE)</th>
                                <th class="py-3 px-3.5 text-center">Total Faktur</th>
                                <th class="py-3 px-3.5">Seri Awal</th>
                                <th class="py-3 px-3.5">Seri Akhir</th>
                                <th class="py-3 px-3.5 text-center">Reguler</th>
                                <th class="py-3 px-3.5 text-center">Stopper</th>
                                <th class="py-3 px-3.5 text-center">Bonus</th>
                                <th class="py-3 px-3.5 text-right w-32">Total Tagihan</th>
                                <th class="py-3 px-3.5 text-right w-32">Dibayar</th>
                                <th class="py-3 px-3.5 text-right w-32">Sisa Piutang</th>
                                <th class="py-3 px-3.5 text-center w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @php
                                $totFaktur = 0; $totTagihan = 0; $totTerbayar = 0; $totPiutang = 0;
                            @endphp
                            @forelse($rekapAEBulan as $idx => $row)
                                @php
                                    $totFaktur += $row->total_faktur;
                                    $totTagihan += $row->total_tagihan;
                                    $totTerbayar += $row->total_terbayar;
                                    $totPiutang += $row->total_piutang;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                    <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                        {{ $row->sales_nama }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center font-bold text-slate-800">
                                        {{ $row->total_faktur }} Lembar
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono text-xs text-kp-blue-700 font-semibold">
                                        {{ $row->seri_awal ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono text-xs text-kp-blue-700 font-semibold">
                                        {{ $row->seri_akhir ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">{{ $row->total_reguler }}</td>
                                    <td class="py-3.5 px-3.5 text-center">{{ $row->total_stopper }}</td>
                                    <td class="py-3.5 px-3.5 text-center">{{ $row->total_bonus }}</td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($row->total_tagihan, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($row->total_terbayar, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-normal text-slate-700 tabular-nums">{{ number_format($row->total_piutang, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        <a href="{{ route('keuangan.faktur.stok-opname', ['tab' => 'per_ae_bulan', 'tahun' => $tahun, 'bulan' => $bulan, 'ae' => $row->sales_nama]) }}" 
                                           class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-300 transition">
                                            Rincian →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="py-12 text-center text-slate-400">
                                        Tidak ada data rekapitulasi AE untuk bulan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($rekapAEBulan->isNotEmpty())
                            <tfoot>
                                <tr class="bg-slate-50 font-bold border-t border-slate-200 text-slate-900">
                                    <td colspan="2" class="py-3.5 px-3.5 text-center">TOTAL KESELURUHAN AE</td>
                                    <td class="py-3.5 px-3.5 text-center">{{ $totFaktur }} Lembar</td>
                                    <td class="py-3.5 px-3.5 text-center">-</td>
                                    <td class="py-3.5 px-3.5 text-center">-</td>
                                    <td colspan="3" class="py-3.5 px-3.5 text-center">-</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">Rp {{ number_format($totTagihan, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-kp-blue-700 tabular-nums">Rp {{ number_format($totTerbayar, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">Rp {{ number_format($totPiutang, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        {{-- 4. TAB: REKAP AE / TAHUN --}}
        @elseif($tab === 'rekap_ae_tahun')
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-3.5">Account Executive (AE)</th>
                                <th class="py-3 px-3.5 text-center">Faktur Setahun</th>
                                <th class="py-3 px-3.5">Seri Awal</th>
                                <th class="py-3 px-3.5">Seri Akhir</th>
                                <th class="py-3 px-3.5 text-center">Reguler</th>
                                <th class="py-3 px-3.5 text-center">Stopper</th>
                                <th class="py-3 px-3.5 text-center">Bonus</th>
                                <th class="py-3 px-3.5 text-right w-32">Omset Setahun</th>
                                <th class="py-3 px-3.5 text-right w-32">Dibayar</th>
                                <th class="py-3 px-3.5 text-right w-32">Piutang</th>
                                <th class="py-3 px-3.5 text-center w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @php
                                $totFakturT = 0; $totTagihanT = 0; $totTerbayarT = 0; $totPiutangT = 0;
                            @endphp
                            @forelse($rekapAETahun as $idx => $row)
                                @php
                                    $totFakturT += $row->total_faktur;
                                    $totTagihanT += $row->total_tagihan;
                                    $totTerbayarT += $row->total_terbayar;
                                    $totPiutangT += $row->total_piutang;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                    <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                        {{ $row->sales_nama }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center font-bold text-slate-800">
                                        {{ $row->total_faktur }} Lembar
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono text-xs text-kp-blue-700 font-semibold">
                                        {{ $row->seri_awal ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono text-xs text-kp-blue-700 font-semibold">
                                        {{ $row->seri_akhir ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">{{ $row->total_reguler }}</td>
                                    <td class="py-3.5 px-3.5 text-center">{{ $row->total_stopper }}</td>
                                    <td class="py-3.5 px-3.5 text-center">{{ $row->total_bonus }}</td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($row->total_tagihan, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($row->total_terbayar, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-normal text-slate-700 tabular-nums">{{ number_format($row->total_piutang, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        <a href="{{ route('keuangan.faktur.stok-opname', ['tab' => 'per_ae_tahun', 'tahun' => $tahun, 'ae' => $row->sales_nama]) }}" 
                                           class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-300 transition">
                                            Rincian AE →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="py-12 text-center text-slate-400">
                                        Tidak ada data perolehan AE pada tahun {{ $tahun }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($rekapAETahun->isNotEmpty())
                            <tfoot>
                                <tr class="bg-slate-50 font-bold border-t border-slate-200 text-slate-900">
                                    <td colspan="2" class="py-3.5 px-3.5 text-center">TOTAL KESELURUHAN SETAHUN</td>
                                    <td class="py-3.5 px-3.5 text-center">{{ $totFakturT }} Lembar</td>
                                    <td class="py-3.5 px-3.5 text-center">-</td>
                                    <td class="py-3.5 px-3.5 text-center">-</td>
                                    <td colspan="3" class="py-3.5 px-3.5 text-center">-</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">Rp {{ number_format($totTagihanT, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-kp-blue-700 tabular-nums">Rp {{ number_format($totTerbayarT, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">Rp {{ number_format($totPiutangT, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        {{-- 5. TAB: PER BULAN --}}
        @elseif($tab === 'per_bulan')
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5">Bulan</th>
                                <th class="py-3 px-3.5 text-center">Faktur Terpakai</th>
                                <th class="py-3 px-3.5">Seri Awal</th>
                                <th class="py-3 px-3.5">Seri Akhir</th>
                                <th class="py-3 px-3.5 text-center">Reguler</th>
                                <th class="py-3 px-3.5 text-center">Stopper</th>
                                <th class="py-3 px-3.5 text-center">Bonus</th>
                                <th class="py-3 px-3.5 text-right w-32">Nilai Tagihan</th>
                                <th class="py-3 px-3.5 text-right w-32">Dibayar</th>
                                <th class="py-3 px-3.5 text-right w-32">Sisa Piutang</th>
                                <th class="py-3 px-3.5 text-center w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @php
                                $totFakturB = 0; $totTagihanB = 0; $totTerbayarB = 0; $totPiutangB = 0;
                            @endphp
                            @forelse($rekapPerBulan as $row)
                                @php
                                    $namaBulan = \Carbon\Carbon::create()->month((int)$row->bulan)->translatedFormat('F');
                                    $totFakturB += $row->total_faktur;
                                    $totTagihanB += $row->total_tagihan;
                                    $totTerbayarB += $row->total_terbayar;
                                    $totPiutangB += $row->total_piutang;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                        {{ $namaBulan }} {{ $tahun }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center font-bold text-slate-800">
                                        {{ $row->total_faktur }} Lembar
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono text-xs text-kp-blue-700 font-semibold">
                                        {{ $row->seri_awal ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono text-xs text-kp-blue-700 font-semibold">
                                        {{ $row->seri_akhir ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">{{ $row->total_reguler }}</td>
                                    <td class="py-3.5 px-3.5 text-center">{{ $row->total_stopper }}</td>
                                    <td class="py-3.5 px-3.5 text-center">{{ $row->total_bonus }}</td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($row->total_tagihan, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($row->total_terbayar, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-normal text-slate-700 tabular-nums">{{ number_format($row->total_piutang, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        <a href="{{ route('keuangan.faktur.stok-opname', ['tab' => 'rekap_ae_bulan', 'tahun' => $tahun, 'bulan' => $row->bulan]) }}" 
                                           class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-300 transition">
                                            Rincian AE →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="py-12 text-center text-slate-400">
                                        Tidak ada data faktur untuk tahun {{ $tahun }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($rekapPerBulan->isNotEmpty())
                            <tfoot>
                                <tr class="bg-slate-50 font-bold border-t border-slate-200 text-slate-900">
                                    <td class="py-3.5 px-3.5">TOTAL TAHUN {{ $tahun }}</td>
                                    <td class="py-3.5 px-3.5 text-center">{{ $totFakturB }} Lembar</td>
                                    <td class="py-3.5 px-3.5 text-center">-</td>
                                    <td class="py-3.5 px-3.5 text-center">-</td>
                                    <td colspan="3" class="py-3.5 px-3.5 text-center">-</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">Rp {{ number_format($totTagihanB, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-kp-blue-700 tabular-nums">Rp {{ number_format($totTerbayarB, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">Rp {{ number_format($totPiutangB, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        {{-- 6. TAB: PER TAHUN (MULTI-TAHUN) --}}
        @elseif($tab === 'per_tahun')
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5">Tahun Buku</th>
                                <th class="py-3 px-3.5 text-center">Total Faktur</th>
                                <th class="py-3 px-3.5">Seri Awal</th>
                                <th class="py-3 px-3.5">Seri Akhir</th>
                                <th class="py-3 px-3.5 text-center">AE Aktif</th>
                                <th class="py-3 px-3.5 text-center">Jml Pemasang</th>
                                <th class="py-3 px-3.5 text-right w-32">Total Tagihan</th>
                                <th class="py-3 px-3.5 text-right w-32">Dibayar</th>
                                <th class="py-3 px-3.5 text-right w-32">Piutang</th>
                                <th class="py-3 px-3.5 text-center w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @forelse($rekapPerTahun as $row)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 font-bold text-slate-900">
                                        Tahun {{ $row->tahun }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center font-bold text-slate-800">
                                        {{ $row->total_faktur }} Lembar
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono text-xs text-kp-blue-700 font-semibold">
                                        {{ $row->seri_awal ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono text-xs text-kp-blue-700 font-semibold">
                                        {{ $row->seri_akhir ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">{{ $row->total_ae }} AE</td>
                                    <td class="py-3.5 px-3.5 text-center">{{ $row->total_pemasang }} Client</td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($row->total_tagihan, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($row->total_terbayar, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-normal text-slate-700 tabular-nums">{{ number_format($row->total_piutang, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        <a href="{{ route('keuangan.faktur.stok-opname', ['tab' => 'per_bulan', 'tahun' => $row->tahun]) }}" 
                                           class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-300 transition">
                                            Rincian Bulan →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="py-12 text-center text-slate-400">
                                        Tidak ada data faktur historis.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        {{-- 7. TAB: PER PEMASANG (CUSTOMER) --}}
        @elseif($tab === 'per_pemasang')
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5">Nama Pemasang</th>
                                <th class="py-3 px-3.5">Alamat</th>
                                <th class="py-3 px-3.5">Sales / AE</th>
                                <th class="py-3 px-3.5 text-center">Faktur</th>
                                <th class="py-3 px-3.5">Seri Awal</th>
                                <th class="py-3 px-3.5">Seri Akhir</th>
                                <th class="py-3 px-3.5 text-right w-32">Total Tagihan</th>
                                <th class="py-3 px-3.5 text-right w-32">Dibayar</th>
                                <th class="py-3 px-3.5 text-right w-32">Piutang</th>
                                <th class="py-3 px-3.5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @forelse($rekapPemasang as $row)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 font-semibold text-slate-900 uppercase">
                                        {{ $row->customer->nama_customer ?? 'UMUM' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-slate-600 font-normal truncate max-w-xs" title="{{ $row->customer->alamat ?? '-' }}">
                                        {{ $row->customer->alamat ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                        {{ $row->daftar_ae ?: 'Umum/Kasir' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center font-bold text-slate-800">
                                        {{ $row->total_faktur }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono text-xs text-kp-blue-700 font-semibold">
                                        {{ $row->seri_awal ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono text-xs text-kp-blue-700 font-semibold">
                                        {{ $row->seri_akhir ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($row->total_tagihan, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($row->total_terbayar, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-normal text-slate-700 tabular-nums">{{ number_format($row->total_piutang, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        @if($row->total_piutang <= 0)
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200">
                                                Lunas
                                            </span>
                                        @else
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border bg-slate-100 text-slate-700 border-slate-200">
                                                Piutang
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="py-12 text-center text-slate-400">
                                        Tidak ada data pemasang yang sesuai filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($rekapPemasang->hasPages())
                    <div class="p-4 border-t border-slate-200">
                        {{ $rekapPemasang->links() }}
                    </div>
                @endif
            </div>
        @endif

    </div>
</x-app-layout>
