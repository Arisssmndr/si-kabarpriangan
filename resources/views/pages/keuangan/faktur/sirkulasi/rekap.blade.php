<x-app-layout>
    <div class="space-y-6">

        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-sky-50 border border-sky-200 flex items-center justify-center text-xl shrink-0">
                    📊
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                            REKAP FAKTUR SIRKULASI
                        </h1>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 uppercase">
                            {{ $mode === 'bulan' ? 'Laporan Bulanan (Tahun ' . $tahun . ')' : 'Laporan Harian / per Tanggal' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekapitulasi distribusi eksemplar koran, oplah harian, susulan, dan penjualan insidentil (HU. Kabar Priangan)
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('keuangan.faktur.sirkulasi.rekap.cetak', request()->query()) }}" target="_blank"
                   class="h-9 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold flex items-center gap-1.5 shadow-2xs transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>🖨 Cetak Rekapitulasi</span>
                </a>
            </div>
        </div>


        <!-- KPI Ringkas Periode Terpilih -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 font-mono">Faktur Terbit Periode</span>
                <div class="mt-2 text-2xl font-black text-slate-900 tracking-tight">
                    {{ $grandFaktur }} <span class="text-xs font-medium text-slate-500">Faktur</span>
                </div>
                <div class="mt-2 text-xs text-slate-500">
                    Berdasarkan filter aktif
                </div>
            </div>

            <div class="bg-white border border-blue-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 font-mono">Total Nilai Tagihan</span>
                <div class="mt-2 text-2xl font-black text-blue-700 tracking-tight font-mono">
                    Rp {{ number_format($grandTagihan, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-blue-800 font-semibold">
                    Akumulasi omset sirkulasi
                </div>
            </div>

            <div class="bg-white border border-blue-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 font-mono">Total Tertagih (Kas)</span>
                <div class="mt-2 text-2xl font-black text-blue-600 tracking-tight font-mono">
                    Rp {{ number_format($grandTerbayar, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-blue-700 font-semibold">
                    {{ $grandTagihan > 0 ? round(($grandTerbayar / $grandTagihan) * 100, 1) : 0 }}% dari target tagihan
                </div>
            </div>

            <div class="bg-white border border-amber-300 bg-amber-50/40 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-800 font-mono">Sisa Piutang Berjalan</span>
                <div class="mt-2 text-2xl font-black text-amber-600 tracking-tight font-mono">
                    Rp {{ number_format($grandPiutang, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-amber-800 font-semibold">
                    {{ $grandTagihan > 0 ? round(($grandPiutang / $grandTagihan) * 100, 1) : 0 }}% belum terbayar
                </div>
            </div>
        </div>

        <!-- Filter Mode Rekapitulasi -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4 flex-wrap gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider font-mono">Pilihan Tampilan:</span>
                    <a href="{{ route('keuangan.faktur.sirkulasi.rekap', ['mode' => 'tanggal']) }}" 
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $mode !== 'bulan' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        📅 Harian (per Tanggal)
                    </a>
                    <a href="{{ route('keuangan.faktur.sirkulasi.rekap', ['mode' => 'bulan']) }}" 
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $mode === 'bulan' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        📆 Bulanan (per Bulan)
                    </a>
                </div>

                <div class="text-xs text-slate-400">
                    Sistem Otomatis Akumulasi Piutang Agen
                </div>
            </div>

            <form method="GET" action="{{ route('keuangan.faktur.sirkulasi.rekap') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <input type="hidden" name="mode" value="{{ $mode }}">

                @if($mode === 'bulan')
                    <div class="sm:col-span-4">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Pilih Tahun</label>
                        <select name="tahun" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-hidden font-bold">
                            @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                                <option value="{{ $y }}" {{ ($tahun ?? date('Y')) == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                @else
                    <div class="sm:col-span-4">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Dari Tanggal</label>
                        <input type="date" name="dari_tanggal" value="{{ $dariTanggal }}" 
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-hidden">
                    </div>
                    <div class="sm:col-span-4">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Sampai Tanggal</label>
                        <input type="date" name="sampai_tanggal" value="{{ $sampaiTanggal }}" 
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-hidden">
                    </div>
                @endif

                <div class="sm:col-span-3 flex items-center gap-2">
                    <button type="submit" class="w-full h-9 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition cursor-pointer">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('keuangan.faktur.sirkulasi.rekap') }}" 
                       class="h-9 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center justify-center transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabel Rekapitulasi -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-blue-50/70 text-[10px] uppercase tracking-wider text-blue-950 font-black border-b border-blue-200">
                        <tr>
                            <th class="py-3 px-4">{{ $mode === 'bulan' ? 'Bulan Distribusi' : 'Tanggal Faktur' }}</th>
                            <th class="py-3 px-4 text-center">Total Faktur</th>
                            <th class="py-3 px-4 text-center">⚡ Susulan</th>
                            <th class="py-3 px-4 text-center">🏷️ Insidentil</th>
                            <th class="py-3 px-4 text-right">Total Tagihan</th>
                            <th class="py-3 px-4 text-right text-blue-700">Terbayar (Kas)</th>
                            <th class="py-3 px-4 text-right text-amber-700">Sisa Piutang</th>
                            <th class="py-3 px-4 text-center">% Tertagih</th>
                            <th class="py-3 px-4 text-center">Rincian Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($rekapData as $row)
                            @php
                                $pct = $row->total_tagihan > 0 ? round(($row->total_terbayar / $row->total_tagihan) * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-blue-50/20 transition">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    @if($mode === 'bulan')
                                        {{ \Carbon\Carbon::create()->month((int)$row->bulan)->translatedFormat('F') }} {{ $tahun }}
                                    @else
                                        {{ \Carbon\Carbon::parse($row->tanggal_faktur)->translatedFormat('d F Y') }}
                                        <span class="text-[10px] text-slate-400 font-normal">({{ \Carbon\Carbon::parse($row->tanggal_faktur)->translatedFormat('l') }})</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-blue-700">
                                    {{ $row->total_faktur }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($row->total_susulan > 0)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            {{ $row->total_susulan }}
                                        </span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($row->total_insidentil > 0)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-800">
                                            {{ $row->total_insidentil }}
                                        </span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-blue-600 font-mono">
                                    Rp {{ number_format($row->total_terbayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold {{ $row->total_piutang > 0 ? 'text-amber-600' : 'text-slate-400' }} font-mono">
                                    Rp {{ number_format($row->total_piutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $pct >= 100 ? 'bg-blue-100 text-blue-800' : ($pct > 0 ? 'bg-amber-100 text-amber-800' : 'bg-amber-400 text-amber-950') }}">
                                        {{ $pct }}%
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center text-[10px]">
                                    <span class="text-blue-700 font-bold">{{ $row->jumlah_lunas }} Lunas</span>
                                    @if($row->jumlah_piutang > 0)
                                        <span class="text-amber-600 font-bold ml-1">/ {{ $row->jumlah_piutang }} Belum</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-2">📊</div>
                                    <p class="text-sm font-semibold text-slate-600">Tidak ada data rekapitulasi pada periode ini.</p>
                                    <p class="text-xs text-slate-400 mt-1">Silakan sesuaikan rentang tanggal atau tahun filter di atas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($rekapData->count() > 0)
                        <tfoot class="bg-blue-50/80 font-bold text-slate-900 border-t-2 border-blue-200">
                            <tr>
                                <td class="py-3.5 px-4 uppercase tracking-wider text-blue-900 font-black">
                                    GRAND TOTAL
                                </td>
                                <td class="py-3.5 px-4 text-center text-blue-900 font-black">
                                    {{ $grandFaktur }}
                                </td>
                                <td class="py-3.5 px-4 text-center text-amber-800">
                                    {{ $rekapData->sum('total_susulan') }}
                                </td>
                                <td class="py-3.5 px-4 text-center text-slate-700">
                                    {{ $rekapData->sum('total_insidentil') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-black font-mono text-blue-950">
                                    Rp {{ number_format($grandTagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right text-blue-700 font-black font-mono">
                                    Rp {{ number_format($grandTerbayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right text-amber-700 font-black font-mono">
                                    Rp {{ number_format($grandPiutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-black">
                                    {{ $grandTagihan > 0 ? round(($grandTerbayar / $grandTagihan) * 100, 1) : 0 }}%
                                </td>
                                <td class="py-3.5 px-4 text-center text-[10px]">
                                    <span class="text-emerald-700">{{ $rekapData->sum('jumlah_lunas') }} Lunas</span> / 
                                    <span class="text-rose-700">{{ $rekapData->sum('jumlah_piutang') }} Belum</span>
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
