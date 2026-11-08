<x-app-layout>
    <div class="space-y-6">

        <!-- Top Action & Filter Bar (Print Hidden) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Neraca Keuangan (Balance Sheet)
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Balanced
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Posisi aset, liabilitas, dan ekuitas HU. Kabar Priangan per akhir periode.
                    </p>
                </div>
            </div>

            <!-- Filter Periode & Cetak Button -->
            <div class="flex items-center gap-2.5 shrink-0">
                <form method="GET" action="{{ route('accounting.laporan.neraca') }}" class="flex items-center gap-2">
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

        <!-- Dokumen Resmi Neraca Keuangan (Skontro / 2 Kolom Seimbang) -->
        <div class="report-print-container bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-xs text-slate-800 print:border-none print:shadow-none print:p-0 max-w-5xl mx-auto">
            
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
                            NERACA KEUANGAN (BALANCE SHEET)
                        </div>
                        <div class="text-xs font-semibold text-[#0E527B] mt-0.5">
                            Per: {{ \Carbon\Carbon::parse($periode . '-01')->endOfMonth()->format('d/m/Y') }}
                        </div>
                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                            Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid Neraca Skontro (Aset di Kiri vs Kewajiban & Ekuitas di Kanan) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                
                <!-- KOLOM KIRI: AKTIVA (ASET) -->
                <div class="space-y-5">
                    <div>
                        <h4 class="font-bold uppercase tracking-wider text-slate-800 border-b border-slate-300 pb-1 mb-2">
                            Aset Lancar (Current Assets)
                        </h4>
                        <table class="w-full">
                            <tbody>
                                <tr>
                                    <td class="py-1.5 text-slate-700 pl-2">Kas &amp; Bank Operasional</td>
                                    <td class="py-1.5 text-right font-mono font-medium text-slate-800 w-36">Rp {{ number_format($kasOperasional, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 text-slate-700 pl-2">Piutang Usaha Iklan</td>
                                    <td class="py-1.5 text-right font-mono font-medium text-slate-800">Rp {{ number_format($piutangIklan, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 text-slate-700 pl-2">Piutang Usaha Koran Sirkulasi</td>
                                    <td class="py-1.5 text-right font-mono font-medium text-slate-800">Rp {{ number_format($piutangSirkulasi, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="bg-sky-50/60 font-semibold border-t border-sky-200">
                                    <td class="py-1.5 pl-2">Total Aset Lancar</td>
                                    <td class="py-1.5 text-right font-mono text-slate-900 font-bold">Rp {{ number_format($totalAsetLancar, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div>
                        <h4 class="font-bold uppercase tracking-wider text-slate-800 border-b border-slate-300 pb-1 mb-2">
                            Aset Tetap (Fixed Assets)
                        </h4>
                        <table class="w-full">
                            <tbody>
                                <tr>
                                    <td class="py-1.5 text-slate-700 pl-2">Harga Perolehan Mesin, Gedung &amp; Armada</td>
                                    <td class="py-1.5 text-right font-mono font-medium text-slate-800 w-36">Rp {{ number_format($hargaPerolehanAset, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="text-rose-700">
                                    <td class="py-1.5 pl-2">Akumulasi Penyusutan Aset</td>
                                    <td class="py-1.5 text-right font-mono font-medium">(Rp {{ number_format($akumulasiPenyusutan, 0, ',', '.') }})</td>
                                </tr>
                                <tr class="bg-sky-50/60 font-semibold border-t border-sky-200">
                                    <td class="py-1.5 pl-2">Nilai Buku Aset Tetap</td>
                                    <td class="py-1.5 text-right font-mono text-slate-900 font-bold">Rp {{ number_format($nilaiBukuAset, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3 bg-sky-100/70 border-2 border-sky-300 rounded-xl">
                        <div class="flex items-center justify-between">
                            <span class="font-black uppercase tracking-wider text-sky-950 text-xs">Total Aktiva (Aset)</span>
                            <span class="font-mono font-black text-sky-950 text-sm">Rp {{ number_format($totalAset, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- KOLOM KANAN: PASIVA (KEWAJIBAN & EKUITAS) -->
                <div class="space-y-5">
                    <div>
                        <h4 class="font-bold uppercase tracking-wider text-slate-800 border-b border-slate-300 pb-1 mb-2">
                            Kewajiban (Liabilities)
                        </h4>
                        <table class="w-full">
                            <tbody>
                                <tr>
                                    <td class="py-1.5 text-slate-700 pl-2">Utang Usaha &amp; Pengadaan Kertas</td>
                                    <td class="py-1.5 text-right font-mono font-medium text-slate-800 w-36">Rp {{ number_format($utangUsaha, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 text-slate-700 pl-2">Utang Pajak PPN &amp; PPh</td>
                                    <td class="py-1.5 text-right font-mono font-medium text-slate-800">Rp {{ number_format($utangPajak, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="bg-amber-50/60 font-semibold border-t border-amber-200">
                                    <td class="py-1.5 pl-2">Total Kewajiban</td>
                                    <td class="py-1.5 text-right font-mono text-slate-900 font-bold">Rp {{ number_format($totalKewajiban, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div>
                        <h4 class="font-bold uppercase tracking-wider text-slate-800 border-b border-slate-300 pb-1 mb-2">
                            Ekuitas (Equity)
                        </h4>
                        <table class="w-full">
                            <tbody>
                                <tr>
                                    <td class="py-1.5 text-slate-700 pl-2">Modal Saham Disetor</td>
                                    <td class="py-1.5 text-right font-mono font-medium text-slate-800 w-36">Rp {{ number_format($modalSaham, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 text-slate-700 pl-2">Laba Ditahan &amp; Berjalan</td>
                                    <td class="py-1.5 text-right font-mono font-medium text-slate-800">Rp {{ number_format($labaDitahan, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="bg-emerald-50/60 font-semibold border-t border-emerald-200">
                                    <td class="py-1.5 pl-2">Total Ekuitas</td>
                                    <td class="py-1.5 text-right font-mono text-slate-900 font-bold">Rp {{ number_format($totalEkuitas, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3 bg-emerald-100/70 border-2 border-emerald-300 rounded-xl">
                        <div class="flex items-center justify-between">
                            <span class="font-black uppercase tracking-wider text-emerald-950 text-xs">Total Pasiva (Kewajiban &amp; Ekuitas)</span>
                            <span class="font-mono font-black text-emerald-950 text-sm">Rp {{ number_format($totalKewajiban + $totalEkuitas, 0, ',', '.') }}</span>
                        </div>
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
