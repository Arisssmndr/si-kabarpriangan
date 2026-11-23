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
                            Rekap Faktur Sirkulasi
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 uppercase shrink-0">
                            {{ $mode === 'bulan' ? 'Bulanan (' . $tahun . ')' : 'Harian / Per Tanggal' }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Rekapitulasi distribusi eksemplar koran, oplah harian, susulan, dan penjualan insidentil
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('keuangan.faktur.sirkulasi.rekap.cetak', request()->query()) }}" target="_blank"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Rekapitulasi</span>
                </a>
            </div>
        </div>

        <!-- 2. KPI Summary Cards (Standar Divisi Iklan 60-30-10) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Faktur Terbit Periode</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($grandFaktur) }} <span class="text-xs font-normal text-slate-400 font-sans">Dokumen</span>
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    Berdasarkan filter periode aktif
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Total Nilai Tagihan</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($grandTagihan, 0, ',', '.') }}
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    Akumulasi omset sirkulasi
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Total Tertagih (Kas)</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($grandTerbayar, 0, ',', '.') }}
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    {{ $grandTagihan > 0 ? round(($grandTerbayar / $grandTagihan) * 100, 1) : 0 }}% dari target tagihan
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Sisa Piutang Berjalan</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($grandPiutang, 0, ',', '.') }}
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    {{ $grandTagihan > 0 ? round(($grandPiutang / $grandTagihan) * 100, 1) : 0 }}% belum terbayar
                </div>
            </div>
        </div>

        <!-- 3. Toolbar Pencarian & Filter Mode Rekapitulasi (h-11 rounded-xl) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs space-y-3">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Mode Rekap:</span>
                    <a href="{{ route('keuangan.faktur.sirkulasi.rekap', ['mode' => 'tanggal']) }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $mode !== 'bulan' ? 'bg-kp-blue-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        Harian (per Tanggal)
                    </a>
                    <a href="{{ route('keuangan.faktur.sirkulasi.rekap', ['mode' => 'bulan']) }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $mode === 'bulan' ? 'bg-kp-blue-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        Bulanan (per Bulan)
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('keuangan.faktur.sirkulasi.rekap') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <input type="hidden" name="mode" value="{{ $mode }}">

                @if($mode === 'bulan')
                    <div class="sm:col-span-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tahun</label>
                        <select name="tahun" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs font-semibold">
                            @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                                <option value="{{ $y }}" {{ ($tahun ?? date('Y')) == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                @else
                    <div class="sm:col-span-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Dari Tanggal</label>
                        <input type="date" name="dari_tanggal" value="{{ $dariTanggal }}" 
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                    <div class="sm:col-span-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sampai Tanggal</label>
                        <input type="date" name="sampai_tanggal" value="{{ $sampaiTanggal }}" 
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                @endif

                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Filter
                    </button>
                    <a href="{{ route('keuangan.faktur.sirkulasi.rekap') }}" 
                       class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- 4. Pure Data Table (Kolom Terpisah Standar Divisi Iklan) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">{{ $mode === 'bulan' ? 'Bulan Distribusi' : 'Tanggal Faktur' }}</th>
                            <th class="py-3 px-3.5 text-center">Total Faktur</th>
                            <th class="py-3 px-3.5 text-center">Susulan</th>
                            <th class="py-3 px-3.5 text-center">Insidentil</th>
                            <th class="py-3 px-3.5 text-center">Lunas</th>
                            <th class="py-3 px-3.5 text-center">Piutang</th>
                            <th class="py-3 px-3.5 text-right w-32">Tagihan</th>
                            <th class="py-3 px-3.5 text-right w-32">Dibayar</th>
                            <th class="py-3 px-3.5 text-right w-32">Sisa Piutang</th>
                            <th class="py-3 px-3.5 text-center">% Tertagih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($rekapData as $key => $row)
                            @php
                                $pct = $row->total_tagihan > 0 ? round(($row->total_terbayar / $row->total_tagihan) * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                    {{ $key + 1 }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    @if($mode === 'bulan')
                                        {{ \Carbon\Carbon::create()->month((int)$row->bulan)->translatedFormat('F') }} {{ $tahun }}
                                    @else
                                        {{ \Carbon\Carbon::parse($row->tanggal_faktur)->translatedFormat('d F Y') }}
                                        <span class="text-xs text-slate-400 font-normal">({{ \Carbon\Carbon::parse($row->tanggal_faktur)->translatedFormat('l') }})</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center font-bold text-slate-800">
                                    {{ $row->total_faktur }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    {{ $row->total_susulan ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    {{ $row->total_insidentil ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center font-semibold text-kp-blue-700">
                                    {{ $row->jumlah_lunas }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center font-semibold text-slate-700">
                                    {{ $row->jumlah_piutang }}
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
                                    @if($row->total_piutang > 0)
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-normal text-slate-700 tabular-nums">{{ number_format($row->total_piutang, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <div class="text-right font-mono text-sm text-slate-400 font-normal">Rp 0</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border {{ $pct >= 100 ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                        {{ $pct }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-12 text-center text-slate-400">
                                    <p class="text-sm font-semibold text-slate-600">Tidak ada data rekapitulasi pada periode ini.</p>
                                    <p class="text-xs text-slate-400 mt-1">Silakan sesuaikan rentang tanggal atau tahun filter di atas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($rekapData->count() > 0)
                        <tfoot class="bg-slate-50 font-bold text-slate-900 border-t border-slate-200">
                            <tr>
                                <td colspan="2" class="py-3.5 px-3.5 uppercase tracking-wider text-slate-900">
                                    GRAND TOTAL
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    {{ $grandFaktur }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    {{ $rekapData->sum('total_susulan') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    {{ $rekapData->sum('total_insidentil') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center text-kp-blue-700">
                                    {{ $rekapData->sum('jumlah_lunas') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center text-slate-700">
                                    {{ $rekapData->sum('jumlah_piutang') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">
                                    Rp {{ number_format($grandTagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-kp-blue-700 tabular-nums">
                                    Rp {{ number_format($grandTerbayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">
                                    Rp {{ number_format($grandPiutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    {{ $grandTagihan > 0 ? round(($grandTerbayar / $grandTagihan) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
