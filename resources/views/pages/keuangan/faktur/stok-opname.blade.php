<x-app-layout>
    <div class="space-y-6">

        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-200 flex items-center justify-center text-xl shrink-0">
                    📦
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                            STOK OPNAME &amp; REKAP SERI FAKTUR IKLAN
                        </h1>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 uppercase">
                            Tahun {{ $tahun }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pemantauan pemakaian nomor seri faktur, performa Account Executive (AE), rekapitulasi berkala, dan saldo piutang pemasang iklan (DOS faktur1.exe)
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('keuangan.faktur.stok-opname.cetak', request()->query()) }}" target="_blank"
                   class="h-9 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                    <svg class="w-4 h-4 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>🖨 Cetak Laporan Dot-Matrix</span>
                </a>
            </div>
        </div>

        <!-- Tabs Navigasi Submodul Faktur (Iklan vs Sirkulasi) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
            @include('pages.keuangan.faktur.partials.tabs', ['activeTab' => 'stock_opname', 'activeCategory' => 'iklan'])
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
                <span>✕</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- SUB-NAVIGASI 7 DIMENSI LAPORAN STOK OPNAME FAKTUR IKLAN -->
        <div class="bg-white border border-slate-200 rounded-2xl p-2.5 shadow-xs">
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <!-- 1. Per AE / Bulan -->
                <a href="{{ route('keuangan.faktur.stok-opname', array_merge(request()->query(), ['tab' => 'per_ae_bulan'])) }}" 
                   class="px-3.5 py-2 rounded-xl font-bold flex items-center gap-1.5 shrink-0 transition {{ $tab === 'per_ae_bulan' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <span>👤</span>
                    <span>Per AE / Bulan</span>
                </a>

                <!-- 2. Per AE / Tahun -->
                <a href="{{ route('keuangan.faktur.stok-opname', array_merge(request()->query(), ['tab' => 'per_ae_tahun'])) }}" 
                   class="px-3.5 py-2 rounded-xl font-bold flex items-center gap-1.5 shrink-0 transition {{ $tab === 'per_ae_tahun' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <span>👤📅</span>
                    <span>Per AE / Tahun</span>
                </a>

                <!-- 3. Rekap AE / Bulan -->
                <a href="{{ route('keuangan.faktur.stok-opname', array_merge(request()->query(), ['tab' => 'rekap_ae_bulan'])) }}" 
                   class="px-3.5 py-2 rounded-xl font-bold flex items-center gap-1.5 shrink-0 transition {{ $tab === 'rekap_ae_bulan' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <span>👥📊</span>
                    <span>Rekap AE / Bulan</span>
                </a>

                <!-- 4. Rekap AE / Tahun -->
                <a href="{{ route('keuangan.faktur.stok-opname', array_merge(request()->query(), ['tab' => 'rekap_ae_tahun'])) }}" 
                   class="px-3.5 py-2 rounded-xl font-bold flex items-center gap-1.5 shrink-0 transition {{ $tab === 'rekap_ae_tahun' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <span>👥📈</span>
                    <span>Rekap AE / Tahun</span>
                </a>

                <!-- 5. Per Bulan -->
                <a href="{{ route('keuangan.faktur.stok-opname', array_merge(request()->query(), ['tab' => 'per_bulan'])) }}" 
                   class="px-3.5 py-2 rounded-xl font-bold flex items-center gap-1.5 shrink-0 transition {{ $tab === 'per_bulan' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <span>🗓️</span>
                    <span>Per Bulan</span>
                </a>

                <!-- 6. Per Tahun -->
                <a href="{{ route('keuangan.faktur.stok-opname', array_merge(request()->query(), ['tab' => 'per_tahun'])) }}" 
                   class="px-3.5 py-2 rounded-xl font-bold flex items-center gap-1.5 shrink-0 transition {{ $tab === 'per_tahun' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <span>📅</span>
                    <span>Per Tahun</span>
                </a>

                <!-- 7. Per Pemasang -->
                <a href="{{ route('keuangan.faktur.stok-opname', array_merge(request()->query(), ['tab' => 'per_pemasang'])) }}" 
                   class="px-3.5 py-2 rounded-xl font-bold flex items-center gap-1.5 shrink-0 transition {{ $tab === 'per_pemasang' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <span>🏢</span>
                    <span>Per Pemasang</span>
                </a>
            </div>
        </div>

        <!-- Filter Bar Interaktif Sesuai Tab Terpilih -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('keuangan.faktur.stok-opname') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Tahun</label>
                    <select name="tahun" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:outline-hidden font-bold">
                        @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                            <option value="{{ $y }}" {{ ($tahun ?? date('Y')) == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </div>

                @if(in_array($tab, ['per_ae_bulan', 'rekap_ae_bulan', 'per_pemasang']))
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">
                            Bulan {{ $tab === 'per_pemasang' ? '(Opsional)' : '' }}
                        </label>
                        <select name="bulan" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:outline-hidden font-bold">
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
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Account Executive (AE)</label>
                        <select name="ae" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:outline-hidden font-bold">
                            @if($tab === 'per_ae_bulan')
                                <option value="semua" {{ ($selectedAE ?? '') === 'semua' ? 'selected' : '' }}>-- Semua Account Executive --</option>
                            @endif
                            @foreach($listAE as $ae)
                                <option value="{{ $ae }}" {{ ($selectedAE ?? '') === $ae ? 'selected' : '' }}>
                                    👤 {{ $ae }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if(in_array($tab, ['per_ae_bulan', 'per_pemasang']))
                    <div class="sm:col-span-3">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">
                            {{ $tab === 'per_pemasang' ? 'Cari Pemasang / Alamat' : 'Cari No Seri / Pemasang' }}
                        </label>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik kata kunci pencarian..."
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:outline-hidden">
                    </div>
                @endif

                <div class="sm:col-span-2 flex items-center gap-1.5">
                    <button type="submit"
                            class="h-9 px-4 w-full bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Terapkan</span>
                    </button>
                    @if(request()->anyFilled(['q', 'ae', 'bulan', 'tahun']))
                        <a href="{{ route('keuangan.faktur.stok-opname', ['tab' => $tab]) }}" class="h-9 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center justify-center transition" title="Reset Filter">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ========================================================================================= --}}
        {{-- 1. TAB: PER AE / BULAN --}}
        {{-- ========================================================================================= --}}
        @if($tab === 'per_ae_bulan')
            <!-- KPI Cards AE Bulan Terpilih -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white border border-purple-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-700 font-mono">Faktur Terbit ({{ $kpiAE['total_faktur'] ?? 0 }} Lembar)</span>
                    <div class="mt-2 text-2xl font-black text-purple-700 tracking-tight font-mono">
                        Rp {{ number_format($kpiAE['total_tagihan'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 text-xs text-slate-500">
                        AE: <strong class="text-slate-800">{{ $selectedAE }}</strong> | Bulan {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 font-mono">Rentang Nomor Seri Terbit</span>
                    <div class="mt-2 text-xs font-mono font-bold text-slate-800 bg-slate-50 p-2 rounded-xl border border-slate-200 truncate">
                        {{ $kpiAE['seri_awal'] ?? '-' }} &bull; {{ $kpiAE['seri_akhir'] ?? '-' }}
                    </div>
                    <div class="mt-2 text-xs text-slate-500">
                        Blangko seri terdaftar di pembukuan
                    </div>
                </div>

                <div class="bg-white border border-emerald-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 font-mono">Realisasi Penerimaan Terbayar</span>
                    <div class="mt-2 text-2xl font-black text-emerald-600 tracking-tight font-mono">
                        Rp {{ number_format($kpiAE['total_terbayar'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 text-xs text-emerald-700 font-semibold">
                        Kas masuk / Pelunasan
                    </div>
                </div>

                <div class="bg-white border border-rose-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-700 font-mono">Sisa Piutang Berjalan</span>
                    <div class="mt-2 text-2xl font-black text-rose-600 tracking-tight font-mono">
                        Rp {{ number_format($kpiAE['total_piutang'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 text-xs text-rose-700 font-semibold">
                        {{ ($kpiAE['total_tagihan'] ?? 0) > 0 ? round((($kpiAE['total_piutang'] ?? 0) / $kpiAE['total_tagihan']) * 100, 1) : 0 }}% belum terlunasi
                    </div>
                </div>
            </div>

            <!-- Tabel Dokumen Faktur Per AE / Bulan -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden space-y-3 p-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span>📄</span>
                            <span>RINCIAN FAKTUR IKLAN: {{ strtoupper($selectedAE) }} &mdash; BULAN {{ strtoupper(\Carbon\Carbon::create()->month($bulan)->translatedFormat('F')) }} {{ $tahun }}</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Menampilkan seluruh faktur iklan terbit pada periode terpilih
                        </p>
                    </div>
                    <div class="text-xs font-bold text-slate-600">
                        Total: <span class="text-purple-600">{{ $fakturs->total() }} Faktur</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-purple-50/70 text-[10px] uppercase tracking-wider text-purple-900 font-bold border-b border-purple-200">
                            <tr>
                                <th class="py-3 px-4 text-center" style="width: 30px;">No</th>
                                <th class="py-3 px-4">No. Seri Faktur</th>
                                <th class="py-3 px-4">Tgl Terbit</th>
                                <th class="py-3 px-4">Customer / Pemasang</th>
                                <th class="py-3 px-4 text-center">Tipe</th>
                                <th class="py-3 px-4 text-right">Nilai Tagihan</th>
                                <th class="py-3 px-4 text-right text-emerald-700">Terbayar</th>
                                <th class="py-3 px-4 text-right text-rose-700">Sisa Piutang</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($fakturs as $idx => $faktur)
                                <tr class="hover:bg-purple-50/20 transition">
                                    <td class="py-3.5 px-4 text-center font-bold">{{ $fakturs->firstItem() + $idx }}</td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-purple-900">
                                        {{ $faktur->no_faktur }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500">
                                        {{ $faktur->tanggal_faktur ? $faktur->tanggal_faktur->translatedFormat('d M Y') : '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900">
                                        {{ $faktur->customer->nama_customer ?? '-' }}
                                        <div class="text-[10px] font-normal text-slate-400">{{ Str::limit($faktur->customer->alamat ?? '', 30) }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($faktur->tipe_faktur === 'reguler')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Reguler</span>
                                        @elseif($faktur->tipe_faktur === 'stopper')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">Stopper</span>
                                        @elseif($faktur->tipe_faktur === 'bonus')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-800 border border-teal-200">Bonus</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">{{ $faktur->tipe_faktur }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                        Rp {{ number_format($faktur->total_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-emerald-600 font-bold">
                                        Rp {{ number_format($faktur->total_terbayar, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-rose-600 font-bold">
                                        Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($faktur->status === 'lunas')
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Lunas</span>
                                        @elseif($faktur->status === 'cicilan')
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Cicilan</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Belum Bayar</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <a href="{{ route('keuangan.faktur.show', $faktur->id) }}" 
                                           class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="py-10 text-center text-slate-400">
                                        <div class="text-3xl mb-1">🔍</div>
                                        Tidak ada dokumen faktur pada filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($fakturs->hasPages())
                    <div class="pt-4 border-t border-slate-100">
                        {{ $fakturs->links() }}
                    </div>
                @endif
            </div>

        {{-- ========================================================================================= --}}
        {{-- 2. TAB: PER AE / TAHUN --}}
        {{-- ========================================================================================= --}}
        @elseif($tab === 'per_ae_tahun')
            <!-- KPI Cards AE Setahun -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white border border-purple-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-700 font-mono">Total Omset 1 Tahun</span>
                    <div class="mt-2 text-2xl font-black text-purple-700 tracking-tight font-mono">
                        Rp {{ number_format($kpiAE['total_tagihan'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 text-xs text-slate-500">
                        AE: <strong class="text-slate-800">{{ $selectedAE }}</strong> | Tahun {{ $tahun }}
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 font-mono">Volume Faktur Diterbitkan</span>
                    <div class="mt-2 text-2xl font-black text-slate-900 tracking-tight">
                        {{ $kpiAE['total_faktur'] ?? 0 }} <span class="text-sm font-medium text-slate-500">Faktur</span>
                    </div>
                    <div class="mt-2 text-xs font-mono text-slate-500 truncate">
                        {{ $kpiAE['seri_awal'] ?? '-' }} s/d {{ $kpiAE['seri_akhir'] ?? '-' }}
                    </div>
                </div>

                <div class="bg-white border border-emerald-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 font-mono">Total Kas Terbayar</span>
                    <div class="mt-2 text-2xl font-black text-emerald-600 tracking-tight font-mono">
                        Rp {{ number_format($kpiAE['total_terbayar'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 text-xs text-emerald-700 font-semibold">
                        Realisasi tunai &amp; setoran
                    </div>
                </div>

                <div class="bg-white border border-rose-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-700 font-mono">Sisa Piutang Berjalan</span>
                    <div class="mt-2 text-2xl font-black text-rose-600 tracking-tight font-mono">
                        Rp {{ number_format($kpiAE['total_piutang'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 text-xs text-rose-700 font-semibold">
                        Tagihan belum selesai
                    </div>
                </div>
            </div>

            <!-- Matriks Bulanan AE -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden space-y-3 p-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span>🗓️</span>
                            <span>REKAPITULASI BULANAN: {{ strtoupper($selectedAE) }} &mdash; TAHUN {{ $tahun }}</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Distribusi perolehan omset dan status pelunasan faktur per bulan (Januari - Desember)
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-purple-50/70 text-[10px] uppercase tracking-wider text-purple-900 font-bold border-b border-purple-200">
                            <tr>
                                <th class="py-3 px-4">Bulan</th>
                                <th class="py-3 px-4 text-center">Faktur Terbit</th>
                                <th class="py-3 px-4">Rentang Seri Faktur</th>
                                <th class="py-3 px-4 text-center">Reguler</th>
                                <th class="py-3 px-4 text-center text-amber-800">Stopper</th>
                                <th class="py-3 px-4 text-center text-teal-800">Bonus</th>
                                <th class="py-3 px-4 text-right">Total Tagihan</th>
                                <th class="py-3 px-4 text-right text-emerald-700">Terbayar</th>
                                <th class="py-3 px-4 text-right text-rose-700">Sisa Piutang</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
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
                                <tr class="hover:bg-purple-50/20 transition">
                                    <td class="py-3.5 px-4 font-bold text-slate-900">
                                        📅 {{ $namaBulan }} {{ $tahun }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                                        {{ $row->total_faktur }} Lembar
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-[11px] text-purple-900 font-bold">
                                        {{ $row->seri_awal }} &bull; {{ $row->seri_akhir }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-blue-700">{{ $row->total_reguler }}</td>
                                    <td class="py-3.5 px-4 text-center font-bold text-amber-700">{{ $row->total_stopper }}</td>
                                    <td class="py-3.5 px-4 text-center font-bold text-teal-700">{{ $row->total_bonus }}</td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                        Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-emerald-600 font-bold">
                                        Rp {{ number_format($row->total_terbayar, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-rose-600 font-bold">
                                        Rp {{ number_format($row->total_piutang, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <a href="{{ route('keuangan.faktur.stok-opname', ['tab' => 'per_ae_bulan', 'tahun' => $tahun, 'bulan' => $row->bulan, 'ae' => $selectedAE]) }}" 
                                           class="px-2.5 py-1 rounded-lg bg-purple-100 hover:bg-purple-200 text-purple-800 text-[10px] font-bold transition">
                                            Buka Rincian →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="py-10 text-center text-slate-400">
                                        <div class="text-3xl mb-1">📭</div>
                                        Tidak ada transaksi faktur untuk AE ini pada tahun {{ $tahun }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($rekapBulananAE->isNotEmpty())
                            <tfoot>
                                <tr class="bg-slate-50 font-bold border-t-2 border-slate-300 text-slate-900">
                                    <td class="py-3 px-4">TOTAL TAHUN {{ $tahun }}</td>
                                    <td class="py-3 px-4 text-center">{{ $totF }} Lembar</td>
                                    <td class="py-3 px-4 text-center">-</td>
                                    <td colspan="3" class="py-3 px-4 text-center">-</td>
                                    <td class="py-3 px-4 text-right font-mono">Rp {{ number_format($totT, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-right font-mono text-emerald-700">Rp {{ number_format($totB, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-right font-mono text-rose-700">Rp {{ number_format($totP, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        {{-- ========================================================================================= --}}
        {{-- 3. TAB: REKAP AE / BULAN --}}
        {{-- ========================================================================================= --}}
        @elseif($tab === 'rekap_ae_bulan')
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden space-y-3 p-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span>👥</span>
                            <span>REKAPITULASI KINERJA SELURUH AE &mdash; BULAN {{ strtoupper(\Carbon\Carbon::create()->month($bulan)->translatedFormat('F')) }} {{ $tahun }}</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Perbandingan pemakaian faktur, perolehan omset, dan tingkat penagihan antar Account Executive
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-purple-50/70 text-[10px] uppercase tracking-wider text-purple-900 font-bold border-b border-purple-200">
                            <tr>
                                <th class="py-3 px-4 text-center" style="width: 30px;">No</th>
                                <th class="py-3 px-4">Account Executive (AE)</th>
                                <th class="py-3 px-4 text-center">Total Faktur</th>
                                <th class="py-3 px-4">Rentang Seri Faktur</th>
                                <th class="py-3 px-4 text-center">Reguler</th>
                                <th class="py-3 px-4 text-center text-amber-800">Stopper</th>
                                <th class="py-3 px-4 text-center text-teal-800">Bonus</th>
                                <th class="py-3 px-4 text-right">Total Tagihan (Omset)</th>
                                <th class="py-3 px-4 text-right text-emerald-700">Terbayar</th>
                                <th class="py-3 px-4 text-right text-rose-700">Sisa Piutang</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
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
                                <tr class="hover:bg-purple-50/20 transition">
                                    <td class="py-3.5 px-4 text-center font-bold">{{ $idx + 1 }}</td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900">
                                        👤 {{ $row->sales_nama }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                                        {{ $row->total_faktur }} Lembar
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-[11px] text-purple-900 font-bold">
                                        {{ $row->seri_awal }} &bull; {{ $row->seri_akhir }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center text-blue-700 font-bold">{{ $row->total_reguler }}</td>
                                    <td class="py-3.5 px-4 text-center text-amber-700 font-bold">{{ $row->total_stopper }}</td>
                                    <td class="py-3.5 px-4 text-center text-teal-700 font-bold">{{ $row->total_bonus }}</td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                        Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-emerald-600 font-bold">
                                        Rp {{ number_format($row->total_terbayar, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-rose-600 font-bold">
                                        Rp {{ number_format($row->total_piutang, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <a href="{{ route('keuangan.faktur.stok-opname', ['tab' => 'per_ae_bulan', 'tahun' => $tahun, 'bulan' => $bulan, 'ae' => $row->sales_nama]) }}" 
                                           class="px-2.5 py-1 rounded-lg bg-purple-100 hover:bg-purple-200 text-purple-800 text-[10px] font-bold transition">
                                            Rincian →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="py-10 text-center text-slate-400">
                                        <div class="text-3xl mb-1">📭</div>
                                        Tidak ada data rekapitulasi AE untuk bulan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($rekapAEBulan->isNotEmpty())
                            <tfoot>
                                <tr class="bg-purple-50 font-bold border-t-2 border-purple-200 text-purple-950">
                                    <td colspan="2" class="py-3 px-4 text-center">TOTAL KESELURUHAN AE</td>
                                    <td class="py-3 px-4 text-center">{{ $totFaktur }} Lembar</td>
                                    <td class="py-3 px-4 text-center">-</td>
                                    <td colspan="3" class="py-3 px-4 text-center">-</td>
                                    <td class="py-3 px-4 text-right font-mono">Rp {{ number_format($totTagihan, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-right font-mono text-emerald-700">Rp {{ number_format($totTerbayar, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-right font-mono text-rose-700">Rp {{ number_format($totPiutang, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        {{-- ========================================================================================= --}}
        {{-- 4. TAB: REKAP AE / TAHUN --}}
        {{-- ========================================================================================= --}}
        @elseif($tab === 'rekap_ae_tahun')
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden space-y-3 p-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span>👥📈</span>
                            <span>REKAPITULASI TAHUNAN SELURUH ACCOUNT EXECUTIVE (TAHUN {{ $tahun }})</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Total volume perolehan iklan, perbandingan omset tahunan, dan sisa piutang per AE
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-purple-50/70 text-[10px] uppercase tracking-wider text-purple-900 font-bold border-b border-purple-200">
                            <tr>
                                <th class="py-3 px-4 text-center" style="width: 30px;">No</th>
                                <th class="py-3 px-4">Account Executive (AE)</th>
                                <th class="py-3 px-4 text-center">Faktur Setahun</th>
                                <th class="py-3 px-4">Rentang Seri Terdaftar</th>
                                <th class="py-3 px-4 text-center">Reguler</th>
                                <th class="py-3 px-4 text-center text-amber-800">Stopper</th>
                                <th class="py-3 px-4 text-center text-teal-800">Bonus</th>
                                <th class="py-3 px-4 text-right">Omset Setahun (Rp)</th>
                                <th class="py-3 px-4 text-right text-emerald-700">Terbayar (Rp)</th>
                                <th class="py-3 px-4 text-right text-rose-700">Piutang (Rp)</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
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
                                <tr class="hover:bg-purple-50/20 transition">
                                    <td class="py-3.5 px-4 text-center font-bold">{{ $idx + 1 }}</td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900">
                                        👤 {{ $row->sales_nama }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                                        {{ $row->total_faktur }} Lembar
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-[11px] text-purple-900 font-bold">
                                        {{ $row->seri_awal }} &bull; {{ $row->seri_akhir }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center text-blue-700 font-bold">{{ $row->total_reguler }}</td>
                                    <td class="py-3.5 px-4 text-center text-amber-700 font-bold">{{ $row->total_stopper }}</td>
                                    <td class="py-3.5 px-4 text-center text-teal-700 font-bold">{{ $row->total_bonus }}</td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                        Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-emerald-600 font-bold">
                                        Rp {{ number_format($row->total_terbayar, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-rose-600 font-bold">
                                        Rp {{ number_format($row->total_piutang, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <a href="{{ route('keuangan.faktur.stok-opname', ['tab' => 'per_ae_tahun', 'tahun' => $tahun, 'ae' => $row->sales_nama]) }}" 
                                           class="px-2.5 py-1 rounded-lg bg-purple-100 hover:bg-purple-200 text-purple-800 text-[10px] font-bold transition">
                                            Rincian AE →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="py-10 text-center text-slate-400">
                                        <div class="text-3xl mb-1">📭</div>
                                        Tidak ada data perolehan AE pada tahun {{ $tahun }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($rekapAETahun->isNotEmpty())
                            <tfoot>
                                <tr class="bg-purple-50 font-bold border-t-2 border-purple-200 text-purple-950">
                                    <td colspan="2" class="py-3 px-4 text-center">TOTAL KESELURUHAN SETAHUN</td>
                                    <td class="py-3 px-4 text-center">{{ $totFakturT }} Lembar</td>
                                    <td class="py-3 px-4 text-center">-</td>
                                    <td colspan="3" class="py-3 px-4 text-center">-</td>
                                    <td class="py-3 px-4 text-right font-mono">Rp {{ number_format($totTagihanT, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-right font-mono text-emerald-700">Rp {{ number_format($totTerbayarT, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-right font-mono text-rose-700">Rp {{ number_format($totPiutangT, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        {{-- ========================================================================================= --}}
        {{-- 5. TAB: PER BULAN (STOK OPNAME BULANAN) --}}
        {{-- ========================================================================================= --}}
        @elseif($tab === 'per_bulan')
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden space-y-3 p-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span>🗓️</span>
                            <span>STOK OPNAME SERI FAKTUR IKLAN PER BULAN (TAHUN {{ $tahun }})</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Audit pemakaian buku seri blangko faktur iklan dari Januari hingga Desember
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-purple-50/70 text-[10px] uppercase tracking-wider text-purple-900 font-bold border-b border-purple-200">
                            <tr>
                                <th class="py-3 px-4">Bulan</th>
                                <th class="py-3 px-4 text-center">Faktur Terpakai</th>
                                <th class="py-3 px-4">Rentang Seri Faktur Terbit</th>
                                <th class="py-3 px-4 text-center">Reguler</th>
                                <th class="py-3 px-4 text-center text-amber-800">Stopper</th>
                                <th class="py-3 px-4 text-center text-teal-800">Bonus</th>
                                <th class="py-3 px-4 text-right">Nilai Tagihan</th>
                                <th class="py-3 px-4 text-right text-emerald-700">Terbayar</th>
                                <th class="py-3 px-4 text-right text-rose-700">Sisa Piutang</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
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
                                <tr class="hover:bg-purple-50/20 transition">
                                    <td class="py-3.5 px-4 font-bold text-slate-900">
                                        📅 {{ $namaBulan }} {{ $tahun }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                                        {{ $row->total_faktur }} Lembar
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-[11px] text-purple-900 font-bold">
                                        {{ $row->seri_awal }} &bull; {{ $row->seri_akhir }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-blue-700">{{ $row->total_reguler }}</td>
                                    <td class="py-3.5 px-4 text-center font-bold text-amber-700">{{ $row->total_stopper }}</td>
                                    <td class="py-3.5 px-4 text-center font-bold text-teal-700">{{ $row->total_bonus }}</td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                        Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-emerald-600 font-bold">
                                        Rp {{ number_format($row->total_terbayar, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-rose-600 font-bold">
                                        Rp {{ number_format($row->total_piutang, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <a href="{{ route('keuangan.faktur.stok-opname', ['tab' => 'rekap_ae_bulan', 'tahun' => $tahun, 'bulan' => $row->bulan]) }}" 
                                           class="px-2.5 py-1 rounded-lg bg-purple-100 hover:bg-purple-200 text-purple-800 text-[10px] font-bold transition">
                                            Rincian AE →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="py-10 text-center text-slate-400">
                                        <div class="text-3xl mb-1">📭</div>
                                        Tidak ada data faktur untuk tahun {{ $tahun }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($rekapPerBulan->isNotEmpty())
                            <tfoot>
                                <tr class="bg-slate-50 font-bold border-t-2 border-slate-300 text-slate-900">
                                    <td class="py-3 px-4">TOTAL TAHUN {{ $tahun }}</td>
                                    <td class="py-3 px-4 text-center">{{ $totFakturB }} Lembar</td>
                                    <td class="py-3 px-4 text-center">-</td>
                                    <td colspan="3" class="py-3 px-4 text-center">-</td>
                                    <td class="py-3 px-4 text-right font-mono">Rp {{ number_format($totTagihanB, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-right font-mono text-emerald-700">Rp {{ number_format($totTerbayarB, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-right font-mono text-rose-700">Rp {{ number_format($totPiutangB, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        {{-- ========================================================================================= --}}
        {{-- 6. TAB: PER TAHUN (MULTI-TAHUN) --}}
        {{-- ========================================================================================= --}}
        @elseif($tab === 'per_tahun')
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden space-y-3 p-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span>📅</span>
                            <span>STOK OPNAME &amp; REKAPITULASI FAKTUR IKLAN MULTI-TAHUN</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Komparasi historis pemakaian seri blangko faktur iklan dan pencapaian per tahun
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-purple-50/70 text-[10px] uppercase tracking-wider text-purple-900 font-bold border-b border-purple-200">
                            <tr>
                                <th class="py-3 px-4 font-bold">Tahun Buku</th>
                                <th class="py-3 px-4 text-center">Total Faktur</th>
                                <th class="py-3 px-4">Rentang Seri Terbit</th>
                                <th class="py-3 px-4 text-center">AE Aktif</th>
                                <th class="py-3 px-4 text-center">Jml Pemasang</th>
                                <th class="py-3 px-4 text-right">Total Tagihan (Omset)</th>
                                <th class="py-3 px-4 text-right text-emerald-700">Terbayar</th>
                                <th class="py-3 px-4 text-right text-rose-700">Piutang</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($rekapPerTahun as $row)
                                <tr class="hover:bg-purple-50/20 transition">
                                    <td class="py-3.5 px-4 font-black text-slate-900 text-sm">
                                        📅 Tahun {{ $row->tahun }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                                        {{ $row->total_faktur }} Lembar
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-[11px] text-purple-900">
                                        {{ $row->seri_awal }} &bull; {{ $row->seri_akhir }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-purple-700">{{ $row->total_ae }} AE</td>
                                    <td class="py-3.5 px-4 text-center">{{ $row->total_pemasang }} Client</td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                        Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-emerald-600 font-bold">
                                        Rp {{ number_format($row->total_terbayar, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-rose-600 font-bold">
                                        Rp {{ number_format($row->total_piutang, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <a href="{{ route('keuangan.faktur.stok-opname', ['tab' => 'per_bulan', 'tahun' => $row->tahun]) }}" 
                                           class="px-2.5 py-1 rounded-lg bg-purple-100 hover:bg-purple-200 text-purple-800 text-[10px] font-bold transition">
                                            Rincian Bulan →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-10 text-center text-slate-400">
                                        <div class="text-3xl mb-1">📭</div>
                                        Tidak ada data faktur historis.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        {{-- ========================================================================================= --}}
        {{-- 7. TAB: PER PEMASANG (CUSTOMER) --}}
        {{-- ========================================================================================= --}}
        @elseif($tab === 'per_pemasang')
            <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden space-y-3 p-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span>🏢</span>
                            <span>REKAPITULASI FAKTUR PER PEMASANG / CLIENT IKLAN (TAHUN {{ $tahun }})</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Analisis akumulasi transaksi faktur per instansi/klien, AE terkait, dan status kredit piutang
                        </p>
                    </div>
                    <div class="text-xs font-bold text-slate-600">
                        Total Pemasang: <span class="text-purple-600">{{ $rekapPemasang->total() }} Client</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-purple-50/70 text-[10px] uppercase tracking-wider text-purple-900 font-bold border-b border-purple-200">
                            <tr>
                                <th class="py-3 px-4">Nama Customer / Pemasang</th>
                                <th class="py-3 px-4">Perusahaan / Alamat</th>
                                <th class="py-3 px-4">Account Executive (AE)</th>
                                <th class="py-3 px-4 text-center">Faktur</th>
                                <th class="py-3 px-4">Rentang Seri Faktur</th>
                                <th class="py-3 px-4 text-right">Total Tagihan</th>
                                <th class="py-3 px-4 text-right text-emerald-700">Terbayar</th>
                                <th class="py-3 px-4 text-right text-rose-700">Sisa Piutang</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($rekapPemasang as $row)
                                <tr class="hover:bg-purple-50/20 transition">
                                    <td class="py-3.5 px-4 font-bold text-slate-900">
                                        {{ $row->customer->nama_customer ?? 'Umum' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600">
                                        {{ $row->customer->alamat ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            👤 {{ $row->daftar_ae ?: 'Umum/Kasir' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                                        {{ $row->total_faktur }}
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-[10px] text-slate-600">
                                        {{ $row->seri_awal }} &bull; {{ $row->seri_akhir }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                        Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-emerald-600 font-bold">
                                        Rp {{ number_format($row->total_terbayar, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-rose-600 font-bold">
                                        Rp {{ number_format($row->total_piutang, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($row->total_piutang <= 0)
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800">Lunas</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-100 text-rose-800">Berpiutang</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-10 text-center text-slate-400">
                                        <div class="text-3xl mb-1">🔍</div>
                                        Tidak ada data pemasang yang sesuai filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($rekapPemasang->hasPages())
                    <div class="pt-4 border-t border-slate-100">
                        {{ $rekapPemasang->links() }}
                    </div>
                @endif
            </div>
        @endif

    </div>
</x-app-layout>

