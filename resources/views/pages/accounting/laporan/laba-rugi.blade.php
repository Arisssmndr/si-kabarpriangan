<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="print:hidden no-print bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Laporan Laba Rugi (Income Statement)</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-purple-50 text-purple-700 border border-purple-200">Kinerja Keuangan</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekapitulasi pendapatan operasional (Iklan + Sirkulasi) dikurangi beban pokok produksi cetak dan beban operasional.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('accounting.laporan.laba-rugi') }}" class="flex items-center gap-2">
                    <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="text-xs rounded-lg border-slate-200 focus:border-purple-500 focus:ring-purple-500">
                    <button type="submit" class="py-2 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition">
                        Filter
                    </button>
                </form>
                <button type="button" onclick="window.print()" class="py-2 px-3 text-xs font-bold rounded-lg bg-purple-600 hover:bg-purple-700 text-white shadow-xs transition">
                    Cetak
                </button>
            </div>
        </div>

        <!-- Kop Cetak Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print:block border-b-2 border-slate-900 pb-3 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black tracking-wider uppercase text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                    <p class="text-[10px] text-slate-600 font-semibold">DIVISI KEUANGAN & AKUNTANSI (SAK EMKM)</p>
                    <p class="text-[9px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</p>
                </div>
                <div class="text-right">
                    <div class="text-xs font-black uppercase text-slate-900">LAPORAN LABA RUGI KOMPREHENSIF</div>
                    <div class="text-[10px] font-semibold text-slate-700">Periode: {{ \Carbon\Carbon::parse($bulanPilihan . '-01')->translatedFormat('F Y') }}</div>
                    <div class="text-[9px] text-slate-400 font-mono">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- Kartu Laporan Laba Rugi Komprehensif -->
        <div class="report-print-container max-w-4xl mx-auto bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden p-6 sm:p-8 space-y-6">
            <div class="text-center pb-4 border-b border-slate-200">
                <h2 class="text-base font-extrabold text-slate-900 tracking-tight uppercase">PT PRIANGAN INTERMEDIA GRAFIKA / HARIAN UMUM KABAR PRIANGAN</h2>
                <h3 class="text-sm font-bold text-purple-800 mt-0.5">LAPORAN LABA RUGI KOMPREHENSIF</h3>
                <p class="text-xs text-slate-500 mt-1">Periode: {{ \Carbon\Carbon::parse($bulanPilihan . '-01')->translatedFormat('F Y') }}</p>
            </div>

            <!-- Bagian 1: PENDAPATAN USAHA -->
            <div class="space-y-2 text-xs">
                <div class="font-bold text-slate-900 uppercase tracking-wider text-xs border-b border-slate-200 pb-1 flex justify-between">
                    <span>1. PENDAPATAN USAHA</span>
                    <span>(Rp)</span>
                </div>
                <div class="flex justify-between py-1 text-slate-700 pl-4">
                    <span>Pendapatan Iklan Koran Cetak</span>
                    <span class="font-semibold">{{ number_format($pendapatanIklanCetak, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1 text-slate-700 pl-4">
                    <span>Pendapatan Iklan Media Online & Priangan TV</span>
                    <span class="font-semibold">{{ number_format($pendapatanOnlineTv, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1 text-slate-700 pl-4">
                    <span>Pendapatan Sirkulasi Koran (Gross)</span>
                    <span class="font-semibold">{{ number_format($pendapatanSirkulasiKotor, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1 text-rose-600 pl-4">
                    <span>Dikurangi: Koran Retur (BAPR Fisik)</span>
                    <span class="font-semibold">({{ number_format($returSirkulasi, 0, ',', '.') }})</span>
                </div>
                <div class="flex justify-between py-2 font-bold text-slate-900 border-t border-dashed border-slate-200 bg-slate-50/70 px-3 rounded">
                    <span>TOTAL PENDAPATAN USAHA BERSIH</span>
                    <span class="text-sm font-extrabold text-emerald-700">Rp {{ number_format($totalPendapatanBersih, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Bagian 2: BEBAN POKOK PRODUKSI (HPP) -->
            <div class="space-y-2 text-xs">
                <div class="font-bold text-slate-900 uppercase tracking-wider text-xs border-b border-slate-200 pb-1 flex justify-between">
                    <span>2. BEBAN POKOK PRODUKSI (HPP)</span>
                    <span>(Rp)</span>
                </div>
                <div class="flex justify-between py-1 text-slate-700 pl-4">
                    <span>Beban Kertas Koran & Tinta Percetakan</span>
                    <span class="font-semibold">{{ number_format($bebanKertasTinta, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1 text-slate-700 pl-4">
                    <span>Beban Armada Distribusi & Bahan Bakar Subuh</span>
                    <span class="font-semibold">{{ number_format($bebanArmadaDistribusi, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 font-bold text-slate-900 border-t border-dashed border-slate-200 bg-slate-50/70 px-3 rounded">
                    <span>TOTAL BEBAN POKOK PRODUKSI</span>
                    <span class="font-extrabold text-rose-700">Rp {{ number_format($totalHpp, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2.5 font-extrabold text-slate-900 bg-purple-50/50 px-3 rounded border border-purple-100">
                    <span>LABA KOTOR (GROSS PROFIT)</span>
                    <span class="text-sm font-black text-purple-900">Rp {{ number_format($labaKotor, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Bagian 3: BEBAN OPERASIONAL -->
            <div class="space-y-2 text-xs">
                <div class="font-bold text-slate-900 uppercase tracking-wider text-xs border-b border-slate-200 pb-1 flex justify-between">
                    <span>3. BEBAN OPERASIONAL & UMUM</span>
                    <span>(Rp)</span>
                </div>
                <div class="flex justify-between py-1 text-slate-700 pl-4">
                    <span>Beban Gaji Redaksi, Wartawan & Staf</span>
                    <span class="font-semibold">{{ number_format($bebanGajiKaryawan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1 text-slate-700 pl-4">
                    <span>Beban Komisi KH & Insentif Account Executive</span>
                    <span class="font-semibold">{{ number_format($bebanKomisiInsentif, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1 text-slate-700 pl-4">
                    <span>Beban Utilitas (Listrik, Internet, Telepon Kantor)</span>
                    <span class="font-semibold">{{ number_format($bebanListrikTelepon, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1 text-slate-700 pl-4">
                    <span>Beban Penyusutan Mesin & Kendaraan</span>
                    <span class="font-semibold">{{ number_format($bebanPenyusutan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 font-bold text-slate-900 border-t border-dashed border-slate-200 bg-slate-50/70 px-3 rounded">
                    <span>TOTAL BEBAN OPERASIONAL</span>
                    <span class="font-extrabold text-rose-700">Rp {{ number_format($totalBebanOperasional, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- LABA BERSIH USAHA -->
            <div class="p-4 bg-purple-100/70 border-2 border-purple-300 rounded-xl flex items-center justify-between text-slate-900">
                <div>
                    <span class="text-xs uppercase font-extrabold tracking-wider block">LABA BERSIH TAHUN BERJALAN (NET PROFIT)</span>
                    <span class="text-[11px] text-purple-800">Setelah seluruh alokasi pendapatan dan beban diakui</span>
                </div>
                <div class="text-xl sm:text-2xl font-black text-purple-950">
                    Rp {{ number_format($labaBersihUsaha, 0, ',', '.') }}
                </div>
            </div>

            <!-- Tanda Tangan Laba Rugi (Hanya Muncul Saat Print) -->
            <div class="hidden print:grid grid-cols-3 gap-6 text-center text-xs pt-6 mt-6 border-t border-slate-300">
                <div>
                    <div class="text-slate-500">Disusun Oleh:</div>
                    <div class="h-16"></div>
                    <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                    <div class="text-[10px] text-slate-500">Staf Akuntansi</div>
                </div>
                <div>
                    <div class="text-slate-500">Diperiksa Oleh:</div>
                    <div class="h-16"></div>
                    <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                    <div class="text-[10px] text-slate-500">Kabag Keuangan</div>
                </div>
                <div>
                    <div class="text-slate-500">Disetujui Oleh:</div>
                    <div class="h-16"></div>
                    <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                    <div class="text-[10px] text-slate-500">Direktur / General Manager</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
