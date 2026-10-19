<x-app-layout>
    <div class="space-y-6">

        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-teal-50 border border-teal-200 flex items-center justify-center text-xl shrink-0">
                    🎁
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                            REKAP FAKTUR IKLAN STOPPER & BONUS
                        </h1>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 uppercase">
                            Tahun {{ $tahun }} {{ $bulanPilihan ? '- ' . \Carbon\Carbon::create()->month($bulanPilihan)->translatedFormat('F') : '(1 Tahun)' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Monitoring pemanfaatan ruang iklan non-komersial (iklan stopper pengisi kolom redaksi & bonus apresiasi pemasangan)
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('keuangan.faktur.create', ['kategori' => 'iklan', 'tipe_faktur' => 'stopper']) }}" 
                   class="h-9 px-3.5 rounded-xl border border-teal-300 bg-teal-50 hover:bg-teal-100 text-teal-800 text-xs font-bold flex items-center gap-1.5 shadow-2xs transition">
                    <span>🧱 + Faktur Stopper</span>
                </a>
                <a href="{{ route('keuangan.faktur.create', ['kategori' => 'iklan', 'tipe_faktur' => 'bonus']) }}" 
                   class="h-9 px-3.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                    <span>🎁 + Faktur Bonus</span>
                </a>
            </div>
        </div>


        <!-- KPI Ringkas Stopper & Bonus -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-teal-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-teal-700 font-mono">Total Faktur Non-Komersial</span>
                <div class="mt-2 text-2xl font-black text-teal-800 tracking-tight font-mono">
                    {{ $totalCount }} <span class="text-sm font-medium text-slate-500">Faktur</span>
                </div>
                <div class="mt-2 text-xs text-slate-500">
                    Stopper & bonus terdata di sistem
                </div>
            </div>

            <div class="bg-white border border-amber-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-700 font-mono">🧱 Iklan Stopper</span>
                <div class="mt-2 text-2xl font-black text-amber-700 tracking-tight font-mono">
                    {{ $totalStopperCount }} <span class="text-sm font-medium text-slate-500">Pemuatan</span>
                </div>
                <div class="mt-2 text-xs text-amber-700 font-semibold">
                    Pengisi kolom kosong cetak
                </div>
            </div>

            <div class="bg-white border border-indigo-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 font-mono">🎁 Iklan Bonus</span>
                <div class="mt-2 text-2xl font-black text-indigo-700 tracking-tight font-mono">
                    {{ $totalBonusCount }} <span class="text-sm font-medium text-slate-500">Pemuatan</span>
                </div>
                <div class="mt-2 text-xs text-indigo-700 font-semibold">
                    Kompensasi bundling / paket promo
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-600 font-mono">Nilai Komersial (Tarif Normal)</span>
                <div class="mt-2 text-2xl font-black text-slate-900 tracking-tight font-mono">
                    Rp {{ number_format($totalNilaiKomersial, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-slate-400">
                    Kompensasi space tanpa tagihan kas
                </div>
            </div>
        </div>

        <!-- Filter Pencarian & Periode Bulanan -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('keuangan.faktur.iklan.stopper-bonus') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Tahun</label>
                    <select name="tahun" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:outline-hidden font-bold">
                        @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                            <option value="{{ $y }}" {{ ($tahun ?? date('Y')) == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Bulan</label>
                    <select name="bulan" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:outline-hidden">
                        <option value="">Semua Bulan (1 Tahun)</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ ($bulanPilihan ?? '') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Tipe Faktur</label>
                    <select name="tipe_faktur" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:outline-hidden">
                        <option value="">Semua (Stopper & Bonus)</option>
                        <option value="stopper" {{ request('tipe_faktur') === 'stopper' ? 'selected' : '' }}>🧱 Hanya Stopper</option>
                        <option value="bonus" {{ request('tipe_faktur') === 'bonus' ? 'selected' : '' }}>🎁 Hanya Bonus</option>
                    </select>
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Cari No. Faktur / Pengiklan</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor faktur atau customer..." 
                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:outline-hidden">
                </div>

                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="w-full h-9 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['tahun', 'bulan', 'tipe_faktur', 'q', 'dari_tanggal', 'sampai_tanggal']))
                        <a href="{{ route('keuangan.faktur.iklan.stopper-bonus') }}" class="h-9 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center justify-center transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 1. TABEL REKAPITULASI BULANAN -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden space-y-3 p-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                <div>
                    <h3 class="text-sm font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span>🗓️</span>
                        <span>REKAPITULASI STOPPER & BONUS PER BULAN (TAHUN {{ $tahun }})</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Distribusi pemuatan iklan stopper dan bonus sepanjang bulan pada tahun berjalan
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-teal-50/70 text-[10px] uppercase tracking-wider text-teal-900 font-bold border-b border-teal-200">
                        <tr>
                            <th class="py-3 px-4">Bulan</th>
                            <th class="py-3 px-4 text-center">Total Faktur</th>
                            <th class="py-3 px-4 text-center text-amber-800">🧱 Jml Stopper</th>
                            <th class="py-3 px-4 text-center text-indigo-800">🎁 Jml Bonus</th>
                            <th class="py-3 px-4 text-right">Nilai Komersial (Rp)</th>
                            <th class="py-3 px-4 text-center">Aksi Filter</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($rekapBulanan as $row)
                            @php
                                $namaBulan = \Carbon\Carbon::create()->month((int)$row->bulan)->translatedFormat('F');
                                $isSelected = ($bulanPilihan == $row->bulan);
                            @endphp
                            <tr class="hover:bg-teal-50/30 transition {{ $isSelected ? 'bg-teal-50/50 font-bold' : '' }}">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    <span class="mr-1">📅</span> {{ $namaBulan }} {{ $tahun }}
                                    @if($isSelected)
                                        <span class="ml-1 px-1.5 py-0.2 rounded text-[9px] bg-teal-600 text-white">Aktif</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                                    {{ $row->total_faktur }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-amber-700">
                                    {{ $row->total_stopper }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-indigo-700">
                                    {{ $row->total_bonus }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-slate-800">
                                    Rp {{ number_format($row->total_nilai_komersial, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('keuangan.faktur.iklan.stopper-bonus', ['tahun' => $tahun, 'bulan' => $row->bulan]) }}" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold transition {{ $isSelected ? 'bg-teal-700 text-white' : 'bg-slate-100 hover:bg-teal-100 text-slate-700 hover:text-teal-900' }}">
                                        <span>Rincian Faktur →</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    <div class="text-2xl mb-1">📭</div>
                                    Belum ada catatan faktur iklan stopper atau bonus pada tahun {{ $tahun }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. TABEL RINCIAN FAKTUR STOPPER & BONUS -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden space-y-3 p-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                <div>
                    <h3 class="text-sm font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span>📋</span>
                        <span>DAFTAR DOKUMEN FAKTUR STOPPER & BONUS</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Rincian spesifikasi iklan, tanggal tayang, dan pembuat dokumen
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. Faktur</th>
                            <th class="py-3 px-4">Tanggal Faktur</th>
                            <th class="py-3 px-4">Customer / Pengiklan</th>
                            <th class="py-3 px-4 text-center">Tipe Faktur</th>
                            <th class="py-3 px-4">Rincian / Ukuran Space</th>
                            <th class="py-3 px-4 text-right">Nilai Normal</th>
                            <th class="py-3 px-4 text-center">Petugas / Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($fakturs as $faktur)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                    {{ $faktur->no_faktur }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $faktur->tanggal_faktur ? $faktur->tanggal_faktur->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $faktur->customer->nama_customer ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $faktur->customer->alamat ?? 'Pelanggan Iklan' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($faktur->tipe_faktur === 'stopper')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                            🧱 Stopper
                                        </span>
                                    @elseif($faktur->tipe_faktur === 'bonus')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                            🎁 Bonus
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                            {{ ucfirst($faktur->tipe_faktur) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    @if(is_array($faktur->rincian_biaya) && count($faktur->rincian_biaya) > 0)
                                        <div class="space-y-0.5">
                                            @foreach($faktur->rincian_biaya as $item)
                                                <div class="text-[11px] text-slate-700">
                                                    • {{ $item['nama'] ?? 'Item Iklan' }} ({{ $item['qty'] ?? 1 }}x)
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">Iklan Stopper/Bonus Halaman</span>
                                    @endif
                                    @if($faktur->catatan)
                                        <div class="text-[10px] text-slate-400 mt-1 italic">
                                            Ket: {{ Str::limit($faktur->catatan, 40) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-800">
                                    Rp {{ number_format($faktur->total_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="text-[11px] font-medium text-slate-700">{{ $faktur->creator->name ?? 'Admin Faktur' }}</div>
                                    <span class="px-2 py-0.2 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800">
                                        Disetujui
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('keuangan.faktur.show', $faktur->id) }}" 
                                       class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold transition">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-1">🔍</div>
                                    Tidak ada data faktur stopper atau bonus yang sesuai kriteria pencarian.
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

    </div>
</x-app-layout>
