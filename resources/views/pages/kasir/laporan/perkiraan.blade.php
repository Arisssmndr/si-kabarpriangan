<x-app-layout>
    @php
        $unitPelaksana = Auth::user()?->kasirReportUnitLabel() ?? 'Divisi Kasir';
        $isKasirUser = Auth::user()?->hasRole('kasir') ?? false;
    @endphp
    <div class="space-y-6" x-data="{ periode: '{{ $periodeTipe ?? 'bulan' }}' }">

        <!-- 1. Header Standar Divisi Iklan (Hidden on Print) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 print:hidden no-print">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Logo Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                            Mutasi Kas per Bagan Akun Perkiraan (COA)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-sky-50 text-[#0E527B] border border-sky-200">
                            Mutasi COA
                        </span>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $unitPelaksana }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Rekapitulasi mutasi kas harian, bulanan, dan tahunan yang siap diteruskan ke Buku Besar Bagian Akuntansi.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 print:hidden no-print shrink-0">
                <button type="button" onclick="window.print()" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs rounded-xl transition shadow-xs flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan COA</span>
                </button>
            </div>
        </div>

        @php
            $rekapCol = collect($rekapPerkiraan);
            $sumMasuk = $rekapCol->sum('masuk');
            $sumKeluar = $rekapCol->sum('keluar');
            $sumSaldo = $sumMasuk - $sumKeluar;
        @endphp

        <!-- 2. KPI Cards Standar Divisi Iklan -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 print:hidden no-print">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Debet (Kas Masuk)</span>
                <p class="mt-2 text-2xl font-bold font-mono text-slate-900">
                    Rp {{ number_format($sumMasuk, 0, ',', '.') }}
                </p>
                <span class="mt-1 text-[11px] text-slate-400 block">Akumulasi seluruh akun kas masuk</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kredit (Kas Keluar)</span>
                <p class="mt-2 text-2xl font-bold font-mono text-slate-900">
                    Rp {{ number_format($sumKeluar, 0, ',', '.') }}
                </p>
                <span class="mt-1 text-[11px] text-slate-400 block">Akumulasi seluruh akun kas keluar</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Mutasi Konsolidasi</span>
                <p class="mt-2 text-2xl font-bold font-mono text-kp-blue-700">
                    Rp {{ number_format($sumSaldo, 0, ',', '.') }}
                </p>
                <span class="mt-1 text-[11px] text-slate-400 block">Selisih mutasi COA periode ini</span>
            </div>
        </div>

        <!-- 3. Filter Bar Terpisah -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:hidden no-print">
            <form method="GET" action="{{ route('kasir.laporan.perkiraan') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                @include('pages.kasir.laporan.partials.period-filter-fields', [
                    'periodeTipe'   => $periodeTipe ?? 'bulan',
                    'tanggal'       => $tanggal ?? date('Y-m-d'),
                    'bulan'         => $bulan,
                    'tahun'         => $tahun,
                    'tipeColClass'  => 'sm:col-span-3',
                    'inputColClass' => 'sm:col-span-6',
                ])
                <div class="sm:col-span-3">
                    <button type="submit" class="h-11 w-full bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-xs cursor-pointer">
                        Tampilkan Rekap
                    </button>
                </div>
            </form>
        </div>

        <!-- Kop Cetak Resmi (Print Only) -->
        <div class="hidden print:block border-b-2 border-slate-900 pb-3 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black tracking-wider uppercase text-slate-900">HU KABAR PRIANGAN</h2>
                    <p class="text-[10px] text-slate-600 font-semibold">{{ strtoupper($unitPelaksana) }} - SISTEM INFORMASI KAS (SIKAPRI)</p>
                    <p class="text-[9px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</p>
                </div>
                <div class="text-right">
                    <div class="text-xs font-black uppercase text-slate-900">REKAPITULASI MUTASI PER AKUN (COA)</div>
                    <div class="text-[10px] font-semibold text-slate-700">Periode: {{ $labelPeriode ?? (\Carbon\Carbon::create(null, (int)$bulan, 1)->translatedFormat('F') . ' ' . $tahun) }}</div>
                    <div class="text-[9px] text-slate-500 font-semibold">{{ $unitPelaksana }}: {{ Auth::user()->name }}</div>
                    <div class="text-[9px] text-slate-400 font-mono">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- 4. Pure Data Table: Rekap Per Akun COA (Tanpa Banner Strip) -->
        <div class="report-print-container bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-4">Kode Perkiraan (COA)</th>
                            <th class="py-3 px-4">Nama Akun Perkiraan</th>
                            <th class="py-3 px-4 text-right">Debet / Kas Masuk (Rp)</th>
                            <th class="py-3 px-4 text-right">Kredit / Kas Keluar (Rp)</th>
                            <th class="py-3 px-4 text-right">Saldo Mutasi (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @foreach($rekapPerkiraan as $row)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900 whitespace-nowrap">{{ $row->kode }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-900">{{ $row->nama }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                {{ $row->masuk > 0 ? number_format($row->masuk, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                {{ $row->keluar > 0 ? number_format($row->keluar, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold whitespace-nowrap {{ $row->saldo >= 0 ? 'text-kp-blue-700' : 'text-slate-900' }}">
                                {{ number_format($row->saldo, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 font-mono font-bold text-xs text-slate-900 border-t-2 border-slate-300">
                        <tr>
                            <td colspan="2" class="py-3.5 px-4 font-sans font-bold uppercase">Total Mutasi Konsolidasi COA</td>
                            <td class="py-3.5 px-4 text-right">Rp {{ number_format($sumMasuk, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right">Rp {{ number_format($sumKeluar, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right text-kp-blue-700">Rp {{ number_format($sumSaldo, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Tanda Tangan Laporan Resmi (Print Only) -->
        <div class="hidden print:grid grid-cols-3 gap-6 text-center text-xs pt-6 mt-6 border-t border-slate-300">
            <div>
                <div class="text-slate-500">Dibuat Oleh:</div>
                <div class="text-slate-700 font-semibold mt-1">{{ $isKasirUser ? 'Petugas Kasir' : $unitPelaksana }}</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">{{ Auth::user()->name }}</div>
                <div class="text-[10px] text-slate-500">{{ $unitPelaksana }}</div>
            </div>
            <div>
                <div class="text-slate-500">Diperiksa Oleh:</div>
                <div class="text-slate-700 font-semibold mt-1">Bagian Akuntansi / Pajak</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                <div class="text-[10px] text-slate-500">Kabag Keuangan</div>
            </div>
            <div>
                <div class="text-slate-500">Disetujui Oleh:</div>
                <div class="text-slate-700 font-semibold mt-1">Pemimpin Perusahaan</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                <div class="text-[10px] text-slate-500">Direktur / GM</div>
            </div>
        </div>

    </div>
</x-app-layout>