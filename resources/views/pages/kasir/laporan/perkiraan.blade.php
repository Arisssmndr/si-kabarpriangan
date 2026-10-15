<x-app-layout>
    <div class="space-y-6">

        <!-- Header Laporan Mutasi Perkiraan (COA) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 border border-purple-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Mutasi Kas per Bagan Akun Perkiraan (COA)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-purple-100 text-purple-800 border border-purple-300">
                            Format DOS KAS.EXE
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekapitulasi mutasi kas harian/bulanan yang siap ditarik (ready_to_pull) ke Buku Besar Modul Akuntansi
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 print:hidden">
                <button type="button" onclick="window.print()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Laporan COA</span>
                </button>
            </div>
        </div>

        <!-- Filter Periode -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:hidden">
            <form method="GET" action="{{ route('kasir.laporan.perkiraan') }}" class="flex items-center gap-3">
                <div class="w-44">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Bulan</label>
                    <select name="bulan" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-purple-500 text-slate-800">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="w-32">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tahun</label>
                    <select name="tahun" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-purple-500 text-slate-800">
                        @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="pt-5">
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-lg transition shadow-2xs">
                        Tampilkan Rekap COA
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Rekap Per Akun COA -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-purple-50/40">
                <h3 class="text-xs font-bold text-purple-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                    Daftar Mutasi Per Nomor Perkiraan (Bagan Akun Standar Kabar Priangan)
                </h3>
                <span class="text-xs font-bold text-slate-500">
                    Periode: {{ \Carbon\Carbon::create(null, (int)$bulan, 1)->translatedFormat('F') }} {{ $tahun }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Kode Perkiraan (COA)</th>
                            <th class="px-4 py-3 font-semibold">Nama Akun Perkiraan</th>
                            <th class="px-4 py-3 font-semibold text-right">Debet / Kas Masuk (Rp)</th>
                            <th class="px-4 py-3 font-semibold text-right">Kredit / Kas Keluar (Rp)</th>
                            <th class="px-4 py-3 font-semibold text-right">Saldo Mutasi (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono">
                        @php
                            $sumMasuk = 0;
                            $sumKeluar = 0;
                        @endphp
                        @foreach($rekapPerkiraan as $row)
                        @php
                            $sumMasuk += $row->masuk;
                            $sumKeluar += $row->keluar;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $row->kode }}</td>
                            <td class="px-4 py-3 font-sans font-semibold text-slate-800">{{ $row->nama }}</td>
                            <td class="px-4 py-3 text-right font-bold text-teal-700">
                                {{ $row->masuk > 0 ? number_format($row->masuk, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-rose-700">
                                {{ $row->keluar > 0 ? number_format($row->keluar, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-black {{ $row->saldo >= 0 ? 'text-slate-900' : 'text-amber-700' }}">
                                {{ number_format($row->saldo, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-100 font-mono font-black text-xs text-slate-900 border-t-2 border-slate-300">
                        <tr>
                            <td colspan="2" class="px-4 py-3 font-sans font-bold">TOTAL MUTASI KONSOLIDASI COA</td>
                            <td class="px-4 py-3 text-right text-teal-800">Rp {{ number_format($sumMasuk, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-rose-800">Rp {{ number_format($sumKeluar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-purple-900">Rp {{ number_format($sumMasuk - $sumKeluar, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
