<x-app-layout>
    <div class="space-y-6">

        <!-- Top Header & Breadcrumb -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('keuangan.anggaran.cetak.hub') }}" class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition" title="Kembali ke Menu Cetak">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-md bg-purple-100 text-purple-800 text-[10px] font-black uppercase tracking-wider">
                            CETAK #7 &bull; ANGGARAN.EXE
                        </span>
                        <span class="text-xs text-slate-400 font-medium">HU. KABAR PRIANGAN</span>
                    </div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight mt-0.5">
                        REKAP REALISASI ANGGARAN PER TAHUN (12 BULAN KOMPARASI)
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Matriks realisasi per bidang dari Januari s.d. Desember Tahun {{ $tahun }}
                    </p>
                </div>
            </div>

            <!-- Filter Tahun & Print Action -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <form method="GET" action="{{ route('keuangan.anggaran.cetak.tahunan') }}" class="flex items-center gap-2">
                    <select name="tahun" onchange="this.form.submit()" class="h-9 text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 font-semibold focus:ring-2 focus:ring-purple-500">
                        @for($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ (int)$tahun === $y ? 'selected' : '' }}>
                                Tahun {{ $y }}
                            </option>
                        @endfor
                    </select>
                    <button type="submit" class="h-9 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Filter
                    </button>
                </form>

                <a href="{{ route('keuangan.anggaran.cetak.tahunan', ['tahun' => $tahun, 'cetak' => 1]) }}" 
                   target="_blank"
                   class="h-9 px-4 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold inline-flex items-center gap-2 shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Matriks Tahunan (Print / PDF)</span>
                </a>
            </div>
        </div>

        <!-- Banner Grand Total Tahun -->
        <div class="bg-purple-900 text-white rounded-2xl p-5 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase tracking-wider text-purple-300 font-bold block">
                    GRAND TOTAL REALISASI ANGGARAN TAHUN {{ $tahun }}
                </span>
                <div class="text-2xl sm:text-3xl font-black mt-1 font-mono">
                    Rp {{ number_format($grandTotalTahun, 0, ',', '.') }}
                </div>
                <p class="text-purple-300 text-xs mt-1">
                    Akumulasi realisasi 7 bidang kerja operasional HU. Kabar Priangan selama 12 bulan
                </p>
            </div>
            <div class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/20 text-right">
                <span class="text-[10px] text-purple-200 block uppercase font-bold">Rata-Rata Realisasi / Bulan</span>
                <span class="text-sm sm:text-base font-black font-mono">
                    Rp {{ number_format($grandTotalTahun / 12, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Tabel Matriks 12 Bulan Cross-Tab -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-tight">
                        Matriks Realisasi 12 Bulan (Januari - Desember {{ $tahun }})
                    </h3>
                    <p class="text-xs text-slate-500">
                        Dalam satuan Rupiah (Rp)
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100/80 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-black tracking-wider">
                            <th class="py-3 px-3 w-10 text-center sticky left-0 bg-slate-100 z-10">Kd</th>
                            <th class="py-3 px-3 min-w-[150px] sticky left-10 bg-slate-100 z-10">Nama Bidang</th>
                            @for($b = 1; $b <= 12; $b++)
                                <th class="py-3 px-2 text-right min-w-[85px]">
                                    {{ \Carbon\Carbon::create(2026, $b, 1)->translatedFormat('M') }}
                                </th>
                            @endfor
                            <th class="py-3 px-3 text-right min-w-[110px] bg-purple-50 text-purple-900 font-black">
                                Total Tahun
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono text-[11px]">
                        @foreach($matriks as $row)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-3 text-center font-bold text-slate-700 sticky left-0 bg-white">
                                    {{ $row['kode'] }}
                                </td>
                                <td class="py-3 px-3 font-sans font-bold text-slate-900 sticky left-10 bg-white truncate">
                                    {{ $row['nama'] }}
                                </td>
                                @for($b = 1; $b <= 12; $b++)
                                    <td class="py-3 px-2 text-right {{ $row['bulan'][$b] > 0 ? 'text-slate-900 font-semibold' : 'text-slate-300' }}">
                                        {{ $row['bulan'][$b] > 0 ? number_format($row['bulan'][$b], 0, ',', '.') : '-' }}
                                    </td>
                                @endfor
                                <td class="py-3 px-3 text-right font-black text-purple-700 bg-purple-50/50">
                                    {{ number_format($row['total'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-300 font-mono text-xs">
                        <tr class="font-black text-slate-900">
                            <td colspan="2" class="py-3.5 px-3 uppercase text-center tracking-wider sticky left-0 bg-slate-50 z-10">
                                TOTAL BULANAN
                            </td>
                            @for($b = 1; $b <= 12; $b++)
                                <td class="py-3.5 px-2 text-right text-[11px] {{ $totalPerBulan[$b] > 0 ? 'text-purple-700' : 'text-slate-400' }}">
                                    {{ $totalPerBulan[$b] > 0 ? number_format($totalPerBulan[$b], 0, ',', '.') : '-' }}
                                </td>
                            @endfor
                            <td class="py-3.5 px-3 text-right text-purple-900 bg-purple-100 font-black">
                                Rp {{ number_format($grandTotalTahun, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
