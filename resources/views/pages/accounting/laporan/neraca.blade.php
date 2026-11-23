<x-app-layout>
    <div class="space-y-5">

        <!-- 1. Header Halaman (Palet 60-30-10 & Action Cetak) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Neraca Keuangan (Balance Sheet)
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Balanced
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Posisi aset, liabilitas, dan ekuitas HU. Kabar Priangan per akhir periode.
                    </p>
                </div>
            </div>

            <!-- Tombol Cetak Dokumen Resmi -->
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('accounting.laporan.neraca.cetak', ['periode' => $periode]) }}" target="_blank" 
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Dokumen Resmi</span>
                </a>
            </div>
        </div>

        <!-- 2. KPI Summary Cards (Report Info) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Aset Lancar</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalAsetLancar, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Kas &amp; piutang usaha</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Nilai Buku Aset Tetap</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($nilaiBukuAset, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Setelah akumulasi susut</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kewajiban</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalKewajiban, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Utang usaha &amp; pajak</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Ekuitas Bersih</span>
                <div class="text-xl font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($totalEkuitas, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Modal &amp; laba ditahan</span>
            </div>
        </div>

        <!-- 3. Filter Periode Mandiri -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <form method="GET" action="{{ route('accounting.laporan.neraca') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Periode Laporan</label>
                    <input type="month" 
                           name="periode" 
                           value="{{ $periode }}" 
                           class="h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Terapkan
                    </button>
                    <a href="{{ route('accounting.laporan.neraca') }}" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- 4. Pure Tabel Neraca Keuangan (Skontro: Aktiva vs Pasiva) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            
            <!-- KOLOM KIRI: AKTIVA (ASET) -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs space-y-5">
                
                <!-- Aset Lancar -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <div class="p-3 bg-slate-50 border-b border-slate-200">
                        <h4 class="font-bold uppercase tracking-wider text-slate-800 text-xs">
                            Aset Lancar (Current Assets)
                        </h4>
                    </div>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2.5 px-3 text-slate-700">Kas &amp; Bank Operasional</td>
                                <td class="py-2.5 px-3 text-right font-mono font-medium text-slate-800 w-44">Rp {{ number_format($kasOperasional, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2.5 px-3 text-slate-700">Piutang Usaha Iklan</td>
                                <td class="py-2.5 px-3 text-right font-mono font-medium text-slate-800">Rp {{ number_format($piutangIklan, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2.5 px-3 text-slate-700">Piutang Usaha Koran Sirkulasi</td>
                                <td class="py-2.5 px-3 text-right font-mono font-medium text-slate-800">Rp {{ number_format($piutangSirkulasi, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="bg-slate-50 font-semibold border-t border-slate-200 text-slate-900">
                                <td class="py-2.5 px-3">Total Aset Lancar</td>
                                <td class="py-2.5 px-3 text-right font-mono text-slate-900 font-bold">Rp {{ number_format($totalAsetLancar, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Aset Tetap -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <div class="p-3 bg-slate-50 border-b border-slate-200">
                        <h4 class="font-bold uppercase tracking-wider text-slate-800 text-xs">
                            Aset Tetap (Fixed Assets)
                        </h4>
                    </div>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2.5 px-3 text-slate-700">Harga Perolehan Mesin, Gedung &amp; Armada</td>
                                <td class="py-2.5 px-3 text-right font-mono font-medium text-slate-800 w-44">Rp {{ number_format($hargaPerolehanAset, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2.5 px-3 text-slate-700">Akumulasi Penyusutan Aset</td>
                                <td class="py-2.5 px-3 text-right font-mono font-medium text-slate-900">(Rp {{ number_format($akumulasiPenyusutan, 0, ',', '.') }})</td>
                            </tr>
                            <tr class="bg-slate-50 font-semibold border-t border-slate-200 text-slate-900">
                                <td class="py-2.5 px-3">Nilai Buku Aset Tetap</td>
                                <td class="py-2.5 px-3 text-right font-mono text-slate-900 font-bold">Rp {{ number_format($nilaiBukuAset, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Total Aktiva -->
                <div class="p-3.5 bg-kp-blue-50/70 border border-kp-blue-200 rounded-xl flex items-center justify-between">
                    <span class="font-bold uppercase tracking-wider text-slate-900 text-xs">Total Aktiva (Aset)</span>
                    <span class="font-mono font-bold text-kp-blue-700 text-base">Rp {{ number_format($totalAset, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- KOLOM KANAN: PASIVA (KEWAJIBAN & EKUITAS) -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs space-y-5">
                
                <!-- Kewajiban -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <div class="p-3 bg-slate-50 border-b border-slate-200">
                        <h4 class="font-bold uppercase tracking-wider text-slate-800 text-xs">
                            Kewajiban (Liabilities)
                        </h4>
                    </div>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2.5 px-3 text-slate-700">Utang Usaha &amp; Pengadaan Kertas</td>
                                <td class="py-2.5 px-3 text-right font-mono font-medium text-slate-800 w-44">Rp {{ number_format($utangUsaha, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2.5 px-3 text-slate-700">Utang Pajak PPN &amp; PPh</td>
                                <td class="py-2.5 px-3 text-right font-mono font-medium text-slate-800">Rp {{ number_format($utangPajak, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="bg-slate-50 font-semibold border-t border-slate-200 text-slate-900">
                                <td class="py-2.5 px-3">Total Kewajiban</td>
                                <td class="py-2.5 px-3 text-right font-mono text-slate-900 font-bold">Rp {{ number_format($totalKewajiban, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Ekuitas -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <div class="p-3 bg-slate-50 border-b border-slate-200">
                        <h4 class="font-bold uppercase tracking-wider text-slate-800 text-xs">
                            Ekuitas (Equity)
                        </h4>
                    </div>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2.5 px-3 text-slate-700">Modal Saham Disetor</td>
                                <td class="py-2.5 px-3 text-right font-mono font-medium text-slate-800 w-44">Rp {{ number_format($modalSaham, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2.5 px-3 text-slate-700">Laba Ditahan &amp; Berjalan</td>
                                <td class="py-2.5 px-3 text-right font-mono font-medium text-slate-800">Rp {{ number_format($labaDitahan, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="bg-slate-50 font-semibold border-t border-slate-200 text-slate-900">
                                <td class="py-2.5 px-3">Total Ekuitas</td>
                                <td class="py-2.5 px-3 text-right font-mono text-slate-900 font-bold">Rp {{ number_format($totalEkuitas, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Total Pasiva -->
                <div class="p-3.5 bg-kp-blue-50/70 border border-kp-blue-200 rounded-xl flex items-center justify-between">
                    <span class="font-bold uppercase tracking-wider text-slate-900 text-xs">Total Pasiva (Kewajiban &amp; Ekuitas)</span>
                    <span class="font-mono font-bold text-kp-blue-700 text-base">Rp {{ number_format($totalKewajiban + $totalEkuitas, 0, ',', '.') }}</span>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>