<x-app-layout>
    <div class="space-y-6">

        <!-- Top Action & Filter Bar (Print Hidden) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Laporan Perubahan Ekuitas
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-sky-50 text-kp-blue-700 border border-kp-blue-200">
                            Modal Pemilik
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Mutasi modal saham, akumulasi laba ditahan, dan ekuitas akhir pers.
                    </p>
                </div>
            </div>

            <!-- Filter Periode & Cetak Button -->
            <div class="flex items-center gap-2.5 shrink-0">
                <form method="GET" action="{{ route('accounting.laporan.ekuitas') }}" class="flex items-center gap-2">
                    <input type="month" name="periode" value="{{ $periode }}" class="h-9 px-3 bg-white border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 transition" />
                    <button type="submit" class="h-9 px-3.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition cursor-pointer">
                        Filter
                    </button>
                </form>

                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 h-9 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Resmi</span>
                </button>
            </div>
        </div>

        <!-- Dokumen Resmi Perubahan Ekuitas -->
        <div class="report-print-container bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-xs text-slate-800 print:border-none print:shadow-none print:p-0 max-w-4xl mx-auto">
            
            <!-- Kop Dokumen Resmi Kabar Priangan -->
            <div class="border-b-2 border-slate-900 pb-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-black tracking-wider uppercase text-slate-900">PT Berkah Pikiran Rakyat</h2>
                        <h3 class="text-base font-bold text-[#0E527B] tracking-wide uppercase">Harian Umum Kabar Priangan</h3>
                        <p class="text-[11px] text-slate-500">Jl. Dr. Sukardjo No. 47, Tawangsari, Kec. Tawang, Kota Tasikmalaya, Jawa Barat 46112</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-black uppercase text-slate-900">
                            LAPORAN PERUBAHAN EKUITAS
                        </div>
                        <div class="text-xs font-semibold text-[#0E527B] mt-0.5">
                            Periode: {{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}
                        </div>
                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                            Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Perubahan Modal -->
            <div class="mb-8">
                <table class="w-full text-xs border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-300 text-slate-700 uppercase font-semibold text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3 text-left">Komponen Ekuitas</th>
                            <th class="py-2.5 px-3 text-right">Modal Saham</th>
                            <th class="py-2.5 px-3 text-right">Laba Ditahan</th>
                            <th class="py-2.5 px-3 text-right">Total Ekuitas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <tr>
                            <td class="py-2.5 px-3 font-semibold text-slate-800">Saldo Awal Periode</td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($modalAwal, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-right font-mono">Rp 0</td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($modalAwal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-slate-700">Laba Bersih Berjalan</td>
                            <td class="py-2.5 px-3 text-right font-mono">-</td>
                            <td class="py-2.5 px-3 text-right font-mono text-emerald-700">+ Rp {{ number_format($labaBersih, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-right font-mono text-emerald-700">+ Rp {{ number_format($labaBersih, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-slate-700">Distribusi Dividen / Penarikan Pemilik</td>
                            <td class="py-2.5 px-3 text-right font-mono">-</td>
                            <td class="py-2.5 px-3 text-right font-mono text-rose-700">- Rp {{ number_format($priveDividen, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-right font-mono text-rose-700">- Rp {{ number_format($priveDividen, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-sky-50/70 border-t-2 border-slate-300 font-bold">
                            <td class="py-3 px-3 uppercase text-xs">Saldo Akhir Ekuitas</td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($modalAwal, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($labaBersih - $priveDividen, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-kp-blue-700 text-sm">Rp {{ number_format($modalAkhir, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Lembar Tanda Tangan Resmi (Standar Kabar Priangan) -->
            <div class="pt-8 border-t-2 border-slate-800 mt-8">
                <div class="grid grid-cols-3 gap-6 text-center text-xs">
                    <div>
                        <div class="text-slate-500 font-medium">Dibuat Oleh:</div>
                        <div class="text-slate-700 font-semibold mt-1">Staf Akuntansi</div>
                        <div class="h-20"></div>
                        <div class="font-bold text-slate-900 underline">{{ auth()->user()->name ?? 'Staf Akuntansi' }}</div>
                        <div class="text-[10px] text-slate-400">Accounting Officer</div>
                    </div>

                    <div>
                        <div class="text-slate-500 font-medium">Diperiksa Oleh:</div>
                        <div class="text-slate-700 font-semibold mt-1">Bagian Keuangan & Pajak</div>
                        <div class="h-20"></div>
                        <div class="font-bold text-slate-900 underline">( .................................................. )</div>
                        <div class="text-[10px] text-slate-400">Kabag Akuntansi & Pajak</div>
                    </div>

                    <div>
                        <div class="text-slate-500 font-medium">Disetujui Oleh:</div>
                        <div class="text-slate-700 font-semibold mt-1">Pemimpin Perusahaan</div>
                        <div class="h-20"></div>
                        <div class="font-bold text-slate-900 underline">( .................................................. )</div>
                        <div class="text-[10px] text-slate-400">Manager Keuangan</div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>

