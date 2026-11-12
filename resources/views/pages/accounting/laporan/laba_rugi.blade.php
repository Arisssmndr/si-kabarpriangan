<x-app-layout>
    <div class="space-y-6">

        <!-- Top Action & Filter Bar (Print Hidden) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Laporan Laba Rugi (Income Statement)
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Multi-Step
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Laporan kinerja operasional pers dan hasil usaha bersih periode berjalan.
                    </p>
                </div>
            </div>

            <!-- Filter Periode & Cetak Button -->
            <div class="flex items-center gap-2.5 shrink-0">
                <form method="GET" action="{{ route('accounting.laporan.laba-rugi') }}" class="flex items-center gap-2">
                    <input type="month" name="periode" value="{{ $periode }}" class="h-9 px-3 bg-white border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 transition" />
                    <button type="submit" class="h-9 px-3.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition cursor-pointer">
                        Filter
                    </button>
                </form>

                <a href="{{ route('accounting.laporan.laba-rugi.cetak', ['periode' => $periode]) }}" target="_blank" class="inline-flex items-center gap-2 h-9 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Dokumen Resmi</span>
                </a>
            </div>
        </div>

        <!-- Dokumen Resmi Laporan Laba Rugi Multi-Step -->
        <div class="report-print-container bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-xs text-slate-800 print:border-none print:shadow-none print:p-0 max-w-4xl mx-auto">
            
            <!-- Kop Dokumen Resmi Kabar Priangan -->
            <div class="border-b-2 border-slate-900 pb-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-black tracking-wider uppercase text-slate-900">PT Berkah Pikiran Rakyat</h2>
                        <h3 class="text-base font-bold text-[#0A72AC] tracking-wide uppercase">Harian Umum Kabar Priangan</h3>
                        <p class="text-[11px] text-slate-500">Jl. Dr. Sukardjo No. 47, Tawangsari, Kec. Tawang, Kota Tasikmalaya, Jawa Barat 46112</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-black uppercase text-slate-900">
                            LAPORAN LABA / RUGI KOMPREHENSIF
                        </div>
                        <div class="text-xs font-semibold text-[#0A72AC] mt-0.5">
                            Periode: {{ \Carbon\Carbon::parse($periode . '-01')->translatedFormat('F Y') }}
                        </div>
                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                            Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rincian Laba Rugi Multi-Step -->
            <div class="space-y-6 text-xs">
                
                <!-- I. PENDAPATAN USAHA -->
                <div>
                    <h4 class="font-bold uppercase tracking-wider text-slate-800 border-b border-slate-300 pb-1 mb-2">
                        I. Pendapatan Usaha
                    </h4>
                    <table class="w-full">
                        <tbody>
                            <tr>
                                <td class="py-1.5 text-slate-700 pl-4">Pendapatan Penjualan Iklan (Koran &amp; Digital)</td>
                                <td class="py-1.5 text-right font-mono font-medium text-slate-800 w-44">Rp {{ number_format($pendapatanIklan, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-1.5 text-slate-700 pl-4">Pendapatan Sirkulasi Koran Harian</td>
                                <td class="py-1.5 text-right font-mono font-medium text-slate-800">Rp {{ number_format($pendapatanSirkulasi, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="font-semibold text-slate-900 border-t border-slate-100">
                                <td class="py-1.5 pl-4">Total Pendapatan Usaha Kotor</td>
                                <td class="py-1.5 text-right font-mono">Rp {{ number_format($totalPendapatanKotor, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-1.5 pl-4 text-slate-700">Dikurangi: Retur Koran Sirkulasi &amp; Nota Kredit Iklan</td>
                                <td class="py-1.5 text-right font-mono font-medium text-slate-900">(Rp {{ number_format($totalRetur, 0, ',', '.') }})</td>
                            </tr>
                            <tr class="bg-kp-blue-50/60 font-bold border-t border-kp-blue-200 text-slate-900">
                                <td class="py-2 pl-4 uppercase">Pendapatan Usaha Bersih</td>
                                <td class="py-2 text-right font-mono text-sm">Rp {{ number_format($pendapatanBersih, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- II. BEBAN POKOK PENDAPATAN (HPP CETAK) -->
                <div>
                    <h4 class="font-bold uppercase tracking-wider text-slate-800 border-b border-slate-300 pb-1 mb-2">
                        II. Beban Pokok Pendapatan (HPP Percetakan)
                    </h4>
                    <table class="w-full">
                        <tbody>
                            <tr>
                                <td class="py-1.5 text-slate-700 pl-4">Biaya Kertas Koran, Tinta, Plat &amp; Listrik Mesin Web Offset</td>
                                <td class="py-1.5 text-right font-mono font-medium text-slate-800 w-44">Rp {{ number_format($hppCetak, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="bg-amber-50/60 font-bold border-t border-amber-200 text-amber-950">
                                <td class="py-2 pl-4 uppercase">Laba Kotor Usaha (Gross Profit)</td>
                                <td class="py-2 text-right font-mono text-sm">Rp {{ number_format($labaKotor, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- III. BEBAN OPERASIONAL & UMUM -->
                <div>
                    <h4 class="font-bold uppercase tracking-wider text-slate-800 border-b border-slate-300 pb-1 mb-2">
                        III. Beban Operasional, Pemasaran &amp; Administrasi
                    </h4>
                    <table class="w-full">
                        <tbody>
                            <tr>
                                <td class="py-1.5 text-slate-700 pl-4">Realisasi Beban Kas Keluar (Redaksi, Distribusi, Kantor)</td>
                                <td class="py-1.5 text-right font-mono font-medium text-slate-800 w-44">Rp {{ number_format($bebanKasKeluar, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-1.5 text-slate-700 pl-4">Beban Penyusutan Aset Tetap (Mesin, Gedung, Armada)</td>
                                <td class="py-1.5 text-right font-mono font-medium text-slate-800">Rp {{ number_format($bebanPenyusutan, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="font-semibold text-slate-900 border-t border-slate-100">
                                <td class="py-1.5 pl-4">Total Beban Operasional</td>
                                <td class="py-1.5 text-right font-mono">Rp {{ number_format($totalBebanOperasional, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- IV. LABA BERSIH OPERASIONAL -->
                <div class="p-3.5 bg-kp-blue-50/80 border border-kp-blue-200 rounded-xl">
                    <div class="flex items-center justify-between">
                        <span class="font-black uppercase tracking-wider text-slate-900 text-sm">
                            Laba Operasional Bersih (Net Operating Income)
                        </span>
                        <span class="font-mono font-black text-kp-blue-700 text-base">
                            Rp {{ number_format($labaOperasional, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

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

