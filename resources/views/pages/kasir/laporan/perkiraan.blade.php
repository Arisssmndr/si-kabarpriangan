<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Hidden on Print) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 print:hidden no-print">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Mutasi Kas per Akun Perkiraan (COA)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Rekapitulasi mutasi kas masuk dan kas keluar per kode perkiraan buku besar ({{ \Carbon\Carbon::create(null, (int)$bulan, 1)->translatedFormat('F') }} {{ $tahun }}).
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 print:hidden no-print">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan COA</span>
                </button>
            </div>
        </div>

        <!-- Filter Periode -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:hidden no-print">
            <form method="GET" action="{{ route('kasir.laporan.perkiraan') }}" class="flex flex-wrap items-end gap-3">
                <div class="w-full sm:w-52">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bulan</label>
                    <select name="bulan" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="w-full sm:w-36">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tahun</label>
                    <select name="tahun" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <button type="submit" class="h-11 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer">
                        Tampilkan Rekap COA
                    </button>
                </div>
            </form>
        </div>

        <!-- Kop Cetak Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print:block border-b-2 border-slate-900 pb-3 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold tracking-wider uppercase text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                    <p class="text-[10px] text-slate-600 font-semibold">DIVISI KASIR & KEUANGAN - SISTEM INFORMASI KAS (SIKAPRI)</p>
                    <p class="text-[9px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</p>
                </div>
                <div class="text-right">
                    <div class="text-xs font-bold uppercase text-slate-900">REKAPITULASI MUTASI PER AKUN (COA)</div>
                    <div class="text-[10px] font-semibold text-slate-700">Periode: {{ \Carbon\Carbon::create(null, (int)$bulan, 1)->translatedFormat('F') }} {{ $tahun }}</div>
                    <div class="text-[9px] text-slate-400 font-mono">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- Tabel Rekap Per Akun COA -->
        <div class="report-print-container bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Mutasi per Nomor Perkiraan</h2>
                    <p class="text-xs text-slate-500">Bagan Akun Standar Kabar Priangan</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ \Carbon\Carbon::create(null, (int)$bulan, 1)->translatedFormat('F') }} {{ $tahun }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">Kode Perkiraan (COA)</th>
                            <th class="py-3 px-3.5">Nama Akun Perkiraan</th>
                            <th class="py-3 px-3.5 text-right">Debet / Kas Masuk</th>
                            <th class="py-3 px-3.5 text-right">Kredit / Kas Keluar</th>
                            <th class="py-3 px-3.5 text-right">Saldo Mutasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @php
                            $sumMasuk = 0;
                            $sumKeluar = 0;
                        @endphp
                        @foreach($rekapPerkiraan as $idx => $row)
                        @php
                            $sumMasuk += $row->masuk;
                            $sumKeluar += $row->keluar;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                            <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm whitespace-nowrap">{{ $row->kode }}</td>
                            <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $row->nama }}</td>
                            <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-kp-blue-700 tabular-nums whitespace-nowrap">
                                {{ $row->masuk > 0 ? number_format($row->masuk, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums whitespace-nowrap">
                                {{ $row->keluar > 0 ? number_format($row->keluar, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums whitespace-nowrap">
                                {{ number_format($row->saldo, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                        <tr>
                            <td colspan="3" class="py-3 px-3.5 font-sans text-xs uppercase font-bold">Total Mutasi Konsolidasi COA</td>
                            <td class="py-3 px-3.5 text-right text-kp-blue-700 tabular-nums">Rp {{ number_format($sumMasuk, 0, ',', '.') }}</td>
                            <td class="py-3 px-3.5 text-right tabular-nums">Rp {{ number_format($sumKeluar, 0, ',', '.') }}</td>
                            <td class="py-3 px-3.5 text-right text-kp-blue-700 font-bold tabular-nums">Rp {{ number_format($sumMasuk - $sumKeluar, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Tanda Tangan Laporan Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print:grid grid-cols-3 gap-6 text-center text-xs pt-6 mt-6 border-t border-slate-300">
            <div>
                <div class="text-slate-500">Dibuat Oleh:</div>
                <div class="text-slate-700 font-semibold mt-1">Petugas Kasir</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">{{ Auth::user()->name }}</div>
                <div class="text-[10px] text-slate-500">Kasir Priangan</div>
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
