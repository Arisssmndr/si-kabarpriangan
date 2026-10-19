<x-app-layout>
    <div class="space-y-6">

        <!-- Top Action & Filter Bar (Hidden when printing) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Laporan Harian Kasir (LHK)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-teal-50 text-teal-700 border border-teal-200">
                            Badge: Rekap
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekapitulasi resmi posisi saldo, seluruh penerimaan, dan pengeluaran kasir per tanggal transaksi
                    </p>
                </div>
            </div>

            <!-- Tanggal Filter & Cetak Button -->
            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" action="{{ route('kasir.laporan-harian') }}" class="flex items-center gap-2">
                    <input type="date" name="tanggal" value="{{ $tanggalPilihan }}" onchange="this.form.submit()" 
                           class="py-1.5 px-3 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-800">
                </form>

                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak LHK Resmi</span>
                </button>
            </div>
        </div>

        <!-- Summary Metric Cards (Visible on screen) -->
        <div class="print:hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <div class="text-[11px] font-medium text-slate-500 uppercase">Saldo Awal Kas</div>
                <div class="text-lg font-bold text-slate-800 mt-1">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</div>
                <div class="text-[10px] text-slate-400">Modal kerja kas kecil</div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <div class="text-[11px] font-medium text-emerald-600 uppercase font-semibold">Total Penerimaan (Masuk)</div>
                <div class="text-lg font-bold text-emerald-600 mt-1">+ Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</div>
                <div class="text-[10px] text-slate-400">Iklan, Sirkulasi, Langganan, Tunai</div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <div class="text-[11px] font-medium text-rose-600 uppercase font-semibold">Total Pengeluaran (Keluar)</div>
                <div class="text-lg font-bold text-rose-600 mt-1">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                <div class="text-[10px] text-slate-400">Realisasi BP & Setoran Bank</div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-teal-200 bg-teal-50/30 shadow-xs">
                <div class="text-[11px] font-medium text-teal-800 uppercase font-bold">Saldo Akhir Kasir</div>
                <div class="text-lg font-black text-teal-700 mt-1">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
                <div class="text-[10px] text-teal-600">Sisa fisik brankas</div>
            </div>
        </div>

        <!-- Dokumen Resmi LHK (Print & Screen View) -->
        <div class="report-print-container bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-xs text-slate-800 print:border-none print:shadow-none print:p-0">
            
            <!-- Kop Dokumen -->
            <div class="border-b-2 border-slate-900 pb-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-black tracking-wider uppercase text-slate-900">PT Berkah Pikiran Rakyat</h2>
                        <h3 class="text-base font-bold text-teal-800 tracking-wide uppercase">Harian Umum Kabar Priangan</h3>
                        <p class="text-[11px] text-slate-500">Jl. Dr. Sukardjo No. 47, Tawangsari, Kec. Tawang, Kota Tasikmalaya, Jawa Barat 46112</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-black uppercase text-slate-900">Laporan Harian Kasir (LHK)</div>
                        <div class="text-xs font-semibold text-teal-700 mt-0.5">{{ $formatTanggal }}</div>
                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB</div>
                    </div>
                </div>
            </div>

            <!-- Bagian I: Rekonsiliasi Saldo -->
            <div class="mb-6">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 border-b border-slate-200 pb-1 mb-3">
                    I. Rekonsiliasi Saldo Kas
                </h4>
                <table class="w-full text-xs">
                    <tbody>
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-slate-600 font-medium">A. Saldo Awal Kas (Opening Balance)</td>
                            <td class="py-2 text-right font-mono font-bold text-slate-800">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-emerald-700 font-medium">B. Total Penerimaan Kas Hari Ini</td>
                            <td class="py-2 text-right font-mono font-bold text-emerald-700">+ Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-2 text-rose-700 font-medium">C. Total Pengeluaran Kas Hari Ini</td>
                            <td class="py-2 text-right font-mono font-bold text-rose-700">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-slate-50 font-bold">
                            <td class="py-2.5 px-2 text-slate-900 uppercase">D. Saldo Akhir Kas Fisik (A + B - C)</td>
                            <td class="py-2.5 px-2 text-right font-mono text-sm text-slate-900">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Bagian II: Rincian Penerimaan Kas -->
            <div class="mb-6">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 border-b border-slate-200 pb-1 mb-3">
                    II. Rincian Penerimaan Kas (Inflows)
                </h4>
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[10px]">
                        <tr>
                            <th class="py-2 text-left font-semibold">Sumber Penerimaan</th>
                            <th class="py-2 text-left font-semibold">Keterangan / Pos</th>
                            <th class="py-2 text-right font-semibold">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-2 font-semibold text-slate-800">Penerimaan Iklan Koran & Online</td>
                            <td class="py-2 text-slate-500">Pelunasan faktur iklan koran harian, adv, & banner digital</td>
                            <td class="py-2 text-right font-mono font-bold text-slate-800">{{ number_format($totalPenerimaanIklan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-semibold text-slate-800">Setoran Sirkulasi Koran</td>
                            <td class="py-2 text-slate-500">Setoran agen & loper koran wilayah Tasikmalaya & Ciamis</td>
                            <td class="py-2 text-right font-mono font-bold text-slate-800">{{ number_format($totalSirkulasi, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-semibold text-slate-800">Koran Langganan Instansi/Dinas</td>
                            <td class="py-2 text-slate-500">Pembayaran koran kantor SKPD, Setda, & instansi vertikal</td>
                            <td class="py-2 text-right font-mono font-bold text-slate-800">{{ number_format($totalLangganan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-semibold text-slate-800">Penjualan Langsung & Iklan Baris</td>
                            <td class="py-2 text-slate-500">Pembelian koran eceran front desk & iklan cilik tunai</td>
                            <td class="py-2 text-right font-mono font-bold text-slate-800">{{ number_format($totalTunaiLangsung, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-emerald-50/60 font-bold border-t border-emerald-200">
                            <td colspan="2" class="py-2 px-2 text-emerald-900 uppercase">Total Penerimaan Kas</td>
                            <td class="py-2 px-2 text-right font-mono text-emerald-900">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Bagian III: Rincian Pengeluaran Kas -->
            <div class="mb-8">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 border-b border-slate-200 pb-1 mb-3">
                    III. Rincian Pengeluaran Kas (Outflows)
                </h4>
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[10px]">
                        <tr>
                            <th class="py-2 text-left font-semibold">Tujuan Pengeluaran</th>
                            <th class="py-2 text-left font-semibold">Nomor Bukti & Keterangan</th>
                            <th class="py-2 text-right font-semibold">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-2 font-semibold text-slate-800">Realisasi Bukti Kas Keluar (BKK / BP)</td>
                            <td class="py-2 text-slate-500">Pencairan komisi KH, BBM loper, & konsumsi jamuan dinas</td>
                            <td class="py-2 text-right font-mono font-bold text-slate-800">{{ number_format($pengeluaranBpBkk, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-semibold text-slate-800">Setor Kas Fisik ke Bank (Banking In)</td>
                            <td class="py-2 text-slate-500">Penyetoran ke Giro BJB Cabang Tasikmalaya</td>
                            <td class="py-2 text-right font-mono font-bold text-slate-800">{{ number_format($setorBank, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-rose-50/60 font-bold border-t border-rose-200">
                            <td colspan="2" class="py-2 px-2 text-rose-900 uppercase">Total Pengeluaran Kas</td>
                            <td class="py-2 px-2 text-right font-mono text-rose-900">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Bagian IV: Lembar Tanda Tangan Resmi -->
            <div class="pt-6 border-t-2 border-slate-800">
                <div class="grid grid-cols-3 gap-6 text-center text-xs">
                    <div>
                        <div class="text-slate-500 font-medium">Dibuat Oleh:</div>
                        <div class="text-slate-700 font-semibold mt-1">Kasir Kabar Priangan</div>
                        <div class="h-20"></div>
                        <div class="font-bold text-slate-900 underline">{{ Auth::user()->name ?? 'Siti Nurhaliza' }}</div>
                        <div class="text-[10px] text-slate-400">Petugas Kasir</div>
                    </div>

                    <div>
                        <div class="text-slate-500 font-medium">Diperiksa Oleh:</div>
                        <div class="text-slate-700 font-semibold mt-1">Bagian Keuangan & Akuntansi</div>
                        <div class="h-20"></div>
                        <div class="font-bold text-slate-900 underline">( .................................................. )</div>
                        <div class="text-[10px] text-slate-400">Kabag Keuangan</div>
                    </div>

                    <div>
                        <div class="text-slate-500 font-medium">Disetujui Oleh:</div>
                        <div class="text-slate-700 font-semibold mt-1">Pemimpin Perusahaan</div>
                        <div class="h-20"></div>
                        <div class="font-bold text-slate-900 underline">( .................................................. )</div>
                        <div class="text-[10px] text-slate-400">Direktur / General Manager</div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
