<x-app-layout>
    <div class="space-y-6">

        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-center text-xl shrink-0">
                    ⏳
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                            REKAP FAKTUR IKLAN PIUTANG (PER BULAN)
                        </h1>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 uppercase">
                            Tahun {{ $tahun }} {{ $bulanPilihan ? '- Bulan ' . \Carbon\Carbon::create()->month($bulanPilihan)->translatedFormat('F') : '(Januari - Desember)' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekapitulasi sisa piutang iklan per bulan, pemantauan aging (lancar, perhatian, kritis), dan rincian penagihan inkaso
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('keuangan.faktur.iklan.piutang.cetak', request()->query()) }}" target="_blank"
                   class="h-9 px-3.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold flex items-center gap-1.5 shadow-2xs transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>🖨 Cetak Rekap Piutang Bulanan</span>
                </a>

                <a href="{{ route('keuangan.inkaso.index') }}" 
                   class="h-9 px-4 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                    <span>📋 Buka Modul Inkaso →</span>
                </a>
            </div>
        </div>


        <!-- KPI Ringkas Aging Piutang Iklan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-rose-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-700 font-mono">Total Piutang Berjalan ({{ $tahun }})</span>
                <div class="mt-2 text-2xl font-black text-rose-600 tracking-tight font-mono">
                    Rp {{ number_format($grandTotalPiutang, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-slate-500">
                    {{ $grandTotalPiutangCount }} faktur iklan belum lunas
                </div>
            </div>

            <div class="bg-white border border-emerald-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 font-mono">1. Lancar (Belum Tempo)</span>
                <div class="mt-2 text-2xl font-black text-emerald-600 tracking-tight font-mono">
                    Rp {{ number_format($grandLancarNominal, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-emerald-700 font-semibold">
                    {{ $grandTotalPiutang > 0 ? round(($grandLancarNominal / $grandTotalPiutang) * 100, 1) : 0 }}% dari total piutang
                </div>
            </div>

            <div class="bg-white border border-amber-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-700 font-mono">2. Perhatian (1 - 30 Hari)</span>
                <div class="mt-2 text-2xl font-black text-amber-600 tracking-tight font-mono">
                    Rp {{ number_format($grandPerhatianNominal, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-amber-800 font-semibold">
                    {{ $grandTotalPiutang > 0 ? round(($grandPerhatianNominal / $grandTotalPiutang) * 100, 1) : 0 }}% lewat s/d 1 bulan
                </div>
            </div>

            <div class="bg-white border border-rose-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-700 font-mono">3. Kritis (> 30 Hari)</span>
                <div class="mt-2 text-2xl font-black text-rose-700 tracking-tight font-mono">
                    Rp {{ number_format($grandKritisNominal, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-rose-700 font-semibold">
                    Prioritas tinggi penagihan inkaso
                </div>
            </div>
        </div>

        <!-- Filter Periode Bulan & Tahun -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('keuangan.faktur.iklan.piutang') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Tahun Rekap</label>
                    <select name="tahun" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-rose-500 focus:outline-hidden font-bold">
                        @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                            <option value="{{ $y }}" {{ ($tahun ?? date('Y')) == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Bulan Faktur</label>
                    <select name="bulan" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-rose-500 focus:outline-hidden">
                        <option value="">Semua Bulan (1 Tahun)</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ ($bulanPilihan ?? '') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Cari Faktur / Customer</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="No. faktur atau nama pengiklan..." 
                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-rose-500 focus:outline-hidden">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Status Piutang</label>
                    <select name="status" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-rose-500 focus:outline-hidden">
                        <option value="">Semua Status</option>
                        <option value="belum_bayar" {{ request('status') === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar (Tempo)</option>
                        <option value="cicilan" {{ request('status') === 'cicilan' ? 'selected' : '' }}>Cicilan (Ada DP)</option>
                    </select>
                </div>

                <div class="sm:col-span-3 flex items-center gap-2">
                    <button type="submit" class="w-full h-9 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition">
                        Filter Rekap Bulanan
                    </button>
                    @if(request()->hasAny(['tahun', 'bulan', 'q', 'status', 'status_inkaso']))
                        <a href="{{ route('keuangan.faktur.iklan.piutang') }}" class="h-9 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center justify-center transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 1. TABEL UTAMA: REKAPITULASI PIUTANG PER BULAN (Tahun {{ $tahun }}) -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden space-y-3 p-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                <div>
                    <h3 class="text-sm font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span>🗓️</span>
                        <span>TABEL REKAPITULASI PIUTANG IKLAN PER BULAN (TAHUN {{ $tahun }})</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Akumulasi tagihan piutang, realisasi terbayar, dan klasifikasi usia piutang per bulan
                    </p>
                </div>

                <div class="text-[11px] font-mono text-slate-500">
                    Total Rekap: <span class="font-bold text-rose-600">{{ $rekapBulanan->count() }} Bulan Aktif</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-rose-50/70 text-[10px] uppercase tracking-wider text-rose-900 font-bold border-b border-rose-200">
                        <tr>
                            <th class="py-3 px-4">Bulan Faktur</th>
                            <th class="py-3 px-4 text-center">Faktur Piutang</th>
                            <th class="py-3 px-4 text-right">Nilai Tagihan</th>
                            <th class="py-3 px-4 text-right">Terbayar (Kas/DP)</th>
                            <th class="py-3 px-4 text-right font-black text-rose-900">Sisa Piutang</th>
                            <th class="py-3 px-4 text-right text-emerald-700">Lancar</th>
                            <th class="py-3 px-4 text-right text-amber-700">Perhatian</th>
                            <th class="py-3 px-4 text-right text-rose-700">Kritis</th>
                            <th class="py-3 px-4 text-center">Aksi Filter</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($rekapBulanan as $row)
                            @php
                                $namaBulan = \Carbon\Carbon::create()->month((int)$row->bulan)->translatedFormat('F');
                                $isSelected = ($bulanPilihan == $row->bulan);
                            @endphp
                            <tr class="hover:bg-rose-50/30 transition {{ $isSelected ? 'bg-rose-50/50 font-bold' : '' }}">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    <span class="text-sm mr-1">📅</span>
                                    <span>{{ $namaBulan }} {{ $tahun }}</span>
                                    @if($isSelected)
                                        <span class="ml-1.5 px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-rose-600 text-white">Aktif</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                                    {{ $row->total_faktur }} <span class="text-[10px] text-slate-400 font-normal">Faktur</span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-slate-700">
                                    Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-emerald-600 font-bold">
                                    Rp {{ number_format($row->total_terbayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-rose-600 font-black text-sm">
                                    Rp {{ number_format($row->total_piutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-emerald-700">
                                    Rp {{ number_format($row->nominal_lancar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-amber-700">
                                    Rp {{ number_format($row->nominal_perhatian, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-rose-700">
                                    Rp {{ number_format($row->nominal_kritis, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('keuangan.faktur.iklan.piutang', ['tahun' => $tahun, 'bulan' => $row->bulan]) }}" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold transition {{ $isSelected ? 'bg-rose-700 text-white shadow-2xs' : 'bg-slate-100 hover:bg-rose-100 text-slate-700 hover:text-rose-900' }}">
                                        <span>Rincian Faktur →</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-10 text-center text-slate-400">
                                    <div class="text-3xl mb-1">🎉</div>
                                    <p class="text-sm font-semibold text-slate-600">Tidak ada piutang iklan pada tahun {{ $tahun }}.</p>
                                    <p class="text-xs text-slate-400">Seluruh faktur terbit pada tahun ini telah lunas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($rekapBulanan->count() > 0)
                        <tfoot class="bg-rose-50/80 font-bold text-slate-900 border-t-2 border-rose-200">
                            <tr>
                                <td class="py-3.5 px-4 uppercase tracking-wider text-rose-900 font-black">
                                    TOTAL REKAP TAHUN {{ $tahun }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-black text-rose-900">
                                    {{ $grandTotalPiutangCount }} Faktur
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-slate-900">
                                    Rp {{ number_format($grandTotalTagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-emerald-700 font-black">
                                    Rp {{ number_format($grandTotalTerbayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-rose-700 font-black text-sm">
                                    Rp {{ number_format($grandTotalPiutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-emerald-800">
                                    Rp {{ number_format($grandLancarNominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-amber-800">
                                    Rp {{ number_format($grandPerhatianNominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-rose-800">
                                    Rp {{ number_format($grandKritisNominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('keuangan.faktur.iklan.piutang', ['tahun' => $tahun]) }}" 
                                       class="text-[10px] font-bold text-rose-700 underline hover:text-rose-900">
                                        Tampilkan Semua
                                    </a>
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- 2. TABEL RINCIAN DOKUMEN FAKTUR PIUTANG -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between flex-wrap gap-2">
                <div>
                    <h3 class="text-sm font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span>📋</span>
                        <span>DAFTAR DOKUMEN FAKTUR PIUTANG {{ $bulanPilihan ? '(BULAN ' . strtoupper(\Carbon\Carbon::create()->month($bulanPilihan)->translatedFormat('F')) . ' ' . $tahun . ')' : '(TAHUN ' . $tahun . ')' }}</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Rincian per faktur iklan yang masih memiliki sisa piutang berjalan
                    </p>
                </div>

                @if($bulanPilihan)
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200">
                            Difilter: Bulan {{ \Carbon\Carbon::create()->month($bulanPilihan)->translatedFormat('F') }}
                        </span>
                        <a href="{{ route('keuangan.faktur.iklan.piutang', array_merge(request()->except('bulan'), ['tahun' => $tahun])) }}" 
                           class="text-xs font-bold text-slate-500 hover:text-rose-600 underline">
                            Tampilkan Semua Bulan
                        </a>
                    </div>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. Faktur</th>
                            <th class="py-3 px-4">Pengiklan (Customer)</th>
                            <th class="py-3 px-4">Tgl Terbit / Tempo</th>
                            <th class="py-3 px-4 text-center">Klasifikasi Aging</th>
                            <th class="py-3 px-4 text-right">Tagihan Total</th>
                            <th class="py-3 px-4 text-right">Terbayar</th>
                            <th class="py-3 px-4 text-right font-black text-rose-700">Sisa Piutang</th>
                            <th class="py-3 px-4 text-center">Status Inkaso</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @php
                            $today = \Carbon\Carbon::now();
                        @endphp
                        @forelse($fakturs as $faktur)
                            @php
                                $jt = \Carbon\Carbon::parse($faktur->jatuh_tempo);
                                $diff = (int) $jt->diffInDays($today, false);
                            @endphp
                            <tr class="hover:bg-rose-50/20 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                    <a href="{{ route('keuangan.faktur.show', $faktur->id) }}" class="text-blue-600 hover:underline">
                                        {{ $faktur->no_faktur }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $faktur->customer->nama_customer ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400">Telp: {{ $faktur->customer->no_telepon ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-[11px]">
                                    <div>Tgl: {{ $faktur->tanggal_faktur->format('d/m/Y') }}</div>
                                    <div class="text-slate-500 font-bold mt-0.5">Tempo: {{ $faktur->jatuh_tempo->format('d/m/Y') }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($diff <= 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            ✓ Lancar (H-{{ abs($diff) }})
                                        </span>
                                    @elseif($diff <= 30)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            ⚠ Lewat {{ $diff }} Hari
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 animate-pulse">
                                            🚨 Kritis (+{{ $diff }} Hari)
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-800">
                                    Rp {{ number_format($faktur->total_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-emerald-600 font-bold">
                                    Rp {{ number_format($faktur->total_terbayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-rose-600 font-extrabold text-sm">
                                    Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($faktur->penugasanAktif)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 text-blue-800">
                                            👤 {{ Str::limit($faktur->penugasanAktif->petugas->name ?? 'Petugas', 12) }}
                                        </span>
                                    @else
                                        <a href="{{ route('keuangan.inkaso.index') }}" 
                                           class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800 hover:bg-amber-200 transition">
                                            + Assign Inkaso
                                        </a>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('keuangan.faktur.show', $faktur->id) }}" 
                                           class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition" 
                                           title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('keuangan.faktur.cetak', $faktur->id) }}" 
                                           target="_blank"
                                           class="p-1.5 text-slate-500 hover:text-emerald-600 hover:bg-slate-100 rounded-lg transition" 
                                           title="Cetak Faktur Piutang">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-2">🎉</div>
                                    <p class="text-sm font-semibold text-slate-600">Tidak ada dokumen faktur piutang yang sesuai filter.</p>
                                    <p class="text-xs text-slate-400 mt-1">Silakan pilih bulan lain atau reset pencarian di atas.</p>
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
