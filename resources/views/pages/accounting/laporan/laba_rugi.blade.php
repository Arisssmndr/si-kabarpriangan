<x-app-layout>
    <div class="space-y-5">

        <!-- 1. Header Halaman (Palet 60-30-10 & Action Cetak) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Laporan Laba Rugi (Income Statement)
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Pure GL-Driven
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Laporan kinerja operasional pers bersumber langsung dari General Ledger periode {{ $periode }}.
                    </p>
                </div>
            </div>

            <!-- Tombol Cetak Dokumen Resmi -->
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('accounting.laporan.laba-rugi.cetak', ['periode' => $periode]) }}" target="_blank" 
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Dokumen Resmi</span>
                </a>
            </div>
        </div>

        @if(($unpostedSummary['total_pending_items'] ?? 0) > 0)
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="text-xs text-amber-900">
                    <strong class="block text-sm font-bold">Peringatan Integritas GL (Unposted Sub-Ledger Guard)</strong>
                    Masih terdapat <strong>{{ $unpostedSummary['iklan_unpulled'] }}</strong> omset iklan belum ditarik,
                    <strong>{{ $unpostedSummary['kas_masuk_iklan_unpulled'] + $unpostedSummary['kas_masuk_sirkulasi_unpulled'] }}</strong> pembayaran kas masuk belum dijurnal,
                    <strong>{{ $unpostedSummary['beban_unpulled_count'] }}</strong> BKK kasir belum diposting ke GL, dan
                    <strong>{{ $unpostedSummary['draft_jurnal_count'] }}</strong> jurnal berstatus Draft pada periode ini.
                </div>
                <div class="flex items-center gap-2 shrink-0 flex-wrap">
                    <a href="{{ route('accounting.iklan.hitung-penjualan') }}" class="px-3 py-1.5 bg-white border border-amber-300 text-amber-900 rounded-lg text-xs font-semibold hover:bg-amber-100">Tarik Iklan</a>
                    <a href="{{ route('accounting.beban.verifikasi') }}" class="px-3 py-1.5 bg-white border border-amber-300 text-amber-900 rounded-lg text-xs font-semibold hover:bg-amber-100">Verifikasi Beban</a>
                    <a href="{{ route('accounting.gl.jurnal', ['status' => 'draft']) }}" class="px-3 py-1.5 bg-white border border-amber-300 text-amber-900 rounded-lg text-xs font-semibold hover:bg-amber-100">Cek Jurnal</a>
                </div>
            </div>
        @endif

        <!-- 2. KPI Summary Cards (Report Info) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pendapatan Bersih</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($pendapatanBersih, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Setelah retur &amp; diskon</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Laba Kotor Usaha</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($labaKotor, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Gross profit pers</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Beban Operasional</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalBebanOperasional, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Kas keluar &amp; depresiasi</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Laba Bersih Berjalan</span>
                <div class="text-xl font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($labaBersih, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Net income periode {{ $periode }}</span>
            </div>
        </div>

        <!-- 3. Filter Periode Mandiri -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <form method="GET" action="{{ route('accounting.laporan.laba-rugi') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
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
                    <a href="{{ route('accounting.laporan.laba-rugi') }}" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- 4. Pure Tabel Rincian Laba Rugi Multi-Step -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden p-5 sm:p-6 space-y-6">
            
            <!-- I. PENDAPATAN USAHA -->
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <div class="p-3 bg-slate-50 border-b border-slate-200">
                    <h4 class="font-bold uppercase tracking-wider text-slate-800 text-xs">
                        I. Pendapatan Usaha
                    </h4>
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2.5 px-4 text-slate-700">Pendapatan Penjualan Iklan (Koran &amp; Digital)</td>
                            <td class="py-2.5 px-4 text-right font-mono font-medium text-slate-800 w-48">Rp {{ number_format($pendapatanIklan, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2.5 px-4 text-slate-700">Pendapatan Sirkulasi Koran Harian</td>
                            <td class="py-2.5 px-4 text-right font-mono font-medium text-slate-800">Rp {{ number_format($pendapatanSirkulasi, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="font-semibold text-slate-900 border-t border-slate-200 bg-slate-50/30">
                            <td class="py-2.5 px-4">Total Pendapatan Usaha Kotor</td>
                            <td class="py-2.5 px-4 text-right font-mono">Rp {{ number_format($totalPendapatanKotor, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2.5 px-4 text-slate-700">Dikurangi: Retur Koran Sirkulasi &amp; Nota Kredit Iklan</td>
                            <td class="py-2.5 px-4 text-right font-mono font-medium text-slate-900">(Rp {{ number_format($totalRetur, 0, ',', '.') }})</td>
                        </tr>
                        <tr class="bg-kp-blue-50/70 font-bold border-t border-kp-blue-200 text-slate-900">
                            <td class="py-3 px-4 uppercase text-xs">Pendapatan Usaha Bersih</td>
                            <td class="py-3 px-4 text-right font-mono text-sm text-kp-blue-700">Rp {{ number_format($pendapatanBersih, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- II. BEBAN POKOK PENDAPATAN (HPP CETAK) -->
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <div class="p-3 bg-slate-50 border-b border-slate-200">
                    <h4 class="font-bold uppercase tracking-wider text-slate-800 text-xs">
                        II. Beban Pokok Pendapatan (HPP Percetakan)
                    </h4>
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2.5 px-4 text-slate-700">Biaya Kertas Koran, Tinta, Plat &amp; Listrik Mesin Web Offset</td>
                            <td class="py-2.5 px-4 text-right font-mono font-medium text-slate-800 w-48">Rp {{ number_format($hppCetak, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-slate-100/70 font-bold border-t border-slate-200 text-slate-900">
                            <td class="py-3 px-4 uppercase text-xs">Laba Kotor Usaha (Gross Profit)</td>
                            <td class="py-3 px-4 text-right font-mono text-sm">Rp {{ number_format($labaKotor, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- III. BEBAN OPERASIONAL & UMUM -->
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <div class="p-3 bg-slate-50 border-b border-slate-200">
                    <h4 class="font-bold uppercase tracking-wider text-slate-800 text-xs">
                        III. Beban Operasional, Pemasaran &amp; Administrasi
                    </h4>
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2.5 px-4 text-slate-700">Realisasi Beban Kas Keluar (Redaksi, Distribusi, Kantor)</td>
                            <td class="py-2.5 px-4 text-right font-mono font-medium text-slate-800 w-48">Rp {{ number_format($bebanKasKeluar, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-2.5 px-4 text-slate-700">Beban Penyusutan Aset Tetap (Mesin, Gedung, Armada)</td>
                            <td class="py-2.5 px-4 text-right font-mono font-medium text-slate-800">Rp {{ number_format($bebanPenyusutan, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-slate-100/70 font-bold border-t border-slate-200 text-slate-900">
                            <td class="py-3 px-4 uppercase text-xs">Total Beban Operasional</td>
                            <td class="py-3 px-4 text-right font-mono text-sm">Rp {{ number_format($totalBebanOperasional, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- IV. LABA BERSIH TAHUN BERJALAN -->
            <div class="border-2 border-kp-blue-300 rounded-xl overflow-hidden bg-kp-blue-50/40">
                <table class="w-full text-sm">
                    <tbody>
                        <tr class="font-bold text-slate-900">
                            <td class="py-3.5 px-4 uppercase text-sm">IV. Laba Bersih Tahun Berjalan (Net Income)</td>
                            <td class="py-3.5 px-4 text-right font-mono text-base font-extrabold text-kp-blue-700 w-48">
                                Rp {{ number_format($labaBersih, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</x-app-layout>