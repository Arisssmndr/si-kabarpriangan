<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Hidden on Print) -->
        <div class="print:hidden no-print bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Laporan Harian Kasir (LHK)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Rekapitulasi saldo, penerimaan, dan pengeluaran kasir ({{ $formatTanggal }}).
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <form method="GET" action="{{ route('kasir.laporan-harian') }}" class="flex items-center gap-2">
                    <input type="date" name="tanggal" value="{{ $tanggalPilihan }}" onchange="this.form.submit()" 
                           class="h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </form>

                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak LHK</span>
                </button>
            </div>
        </div>

        <!-- 4 Kartu KPI (Screen Only) -->
        <div class="print:hidden no-print grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Awal Kas</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Modal kerja kas kecil</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Penerimaan</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Iklan, Sirkulasi, Langganan & Tunai</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Pengeluaran</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Realisasi BP & Setoran Bank</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Akhir Kasir</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Sisa kas fisik brankas</span>
            </div>
        </div>

        <!-- Dokumen Resmi LHK (Print & Screen View) -->
        <div class="report-print-container bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-xs text-slate-800 print:border-none print:shadow-none print:p-0">
            
            <!-- Kop Dokumen -->
            <div class="border-b-2 border-slate-900 pb-4 mb-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold tracking-wider uppercase text-slate-900">PT Berkah Pikiran Rakyat</h2>
                        <h3 class="text-base font-bold text-kp-blue-700 tracking-wide uppercase">Harian Umum Kabar Priangan</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Jl. Dr. Sukardjo No. 47, Tawangsari, Kec. Tawang, Kota Tasikmalaya, Jawa Barat 46112</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold uppercase text-slate-900">Laporan Harian Kasir (LHK)</div>
                        <div class="text-xs font-semibold text-kp-blue-700 mt-0.5">{{ $formatTanggal }}</div>
                        <div class="text-[11px] text-slate-400 font-mono mt-0.5">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB</div>
                    </div>
                </div>
            </div>

            <!-- Bagian I: Rekonsiliasi Saldo -->
            <div class="mb-6">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 border-b border-slate-200 pb-1.5 mb-3">
                    I. Rekonsiliasi Saldo Kas
                </h4>
                <table class="w-full text-sm border-collapse">
                    <tbody>
                        <tr class="border-b border-slate-100">
                            <td class="py-2.5 text-slate-700 font-medium">A. Saldo Awal Kas (Opening Balance)</td>
                            <td class="py-2.5 text-right font-mono font-semibold text-slate-900 tabular-nums">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-2.5 text-slate-700 font-medium">B. Total Penerimaan Kas Hari Ini</td>
                            <td class="py-2.5 text-right font-mono font-semibold text-kp-blue-700 tabular-nums">+ Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-2.5 text-slate-700 font-medium">C. Total Pengeluaran Kas Hari Ini</td>
                            <td class="py-2.5 text-right font-mono font-semibold text-slate-900 tabular-nums">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-slate-50 border-t-2 border-slate-200 font-bold">
                            <td class="py-3 px-3 text-slate-900 uppercase text-xs">D. Saldo Akhir Kas Fisik (A + B - C)</td>
                            <td class="py-3 px-3 text-right font-mono text-sm text-kp-blue-700 tabular-nums">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Bagian II: Rincian Penerimaan Kas -->
            <div class="mb-6">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 border-b border-slate-200 pb-1.5 mb-3">
                    II. Rincian Penerimaan Kas
                </h4>
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-2.5 px-3 text-left">Sumber Penerimaan</th>
                            <th class="py-2.5 px-3 text-left">Keterangan / Pos</th>
                            <th class="py-2.5 px-3 text-right">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <tr>
                            <td class="py-2.5 px-3 font-semibold text-slate-900">Penerimaan Iklan Koran & Online</td>
                            <td class="py-2.5 px-3 text-slate-600 text-xs">Pelunasan faktur iklan koran harian, advertorial & digital</td>
                            <td class="py-2.5 px-3 text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($totalPenerimaanIklan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 font-semibold text-slate-900">Setoran Sirkulasi Koran</td>
                            <td class="py-2.5 px-3 text-slate-600 text-xs">Setoran agen & loper koran wilayah distribusi</td>
                            <td class="py-2.5 px-3 text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($totalSirkulasi, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 font-semibold text-slate-900">Koran Langganan Instansi/Dinas</td>
                            <td class="py-2.5 px-3 text-slate-600 text-xs">Pembayaran koran kantor SKPD, Setda & instansi</td>
                            <td class="py-2.5 px-3 text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($totalLangganan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 font-semibold text-slate-900">Penjualan Langsung & Iklan Baris</td>
                            <td class="py-2.5 px-3 text-slate-600 text-xs">Pembelian koran eceran & iklan baris tunai</td>
                            <td class="py-2.5 px-3 text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($totalTunaiLangsung, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900">
                        <tr>
                            <td colspan="2" class="py-2.5 px-3 font-sans text-xs uppercase font-bold">Total Penerimaan Kas</td>
                            <td class="py-2.5 px-3 text-right text-kp-blue-700 font-bold tabular-nums">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Bagian III: Rincian Pengeluaran Kas -->
            <div class="mb-8">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 border-b border-slate-200 pb-1.5 mb-3">
                    III. Rincian Pengeluaran Kas
                </h4>
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-2.5 px-3 text-left">Tujuan Pengeluaran</th>
                            <th class="py-2.5 px-3 text-left">Keterangan</th>
                            <th class="py-2.5 px-3 text-right">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <tr>
                            <td class="py-2.5 px-3 font-semibold text-slate-900">Realisasi Bukti Kas Keluar (BKK / BP)</td>
                            <td class="py-2.5 px-3 text-slate-600 text-xs">Pencairan BP operasional, komisi KH & insentif AE</td>
                            <td class="py-2.5 px-3 text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($pengeluaranBpBkk, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 font-semibold text-slate-900">Setor Kas Fisik ke Bank (Banking In)</td>
                            <td class="py-2.5 px-3 text-slate-600 text-xs">Penyetoran kas ke rekening giro bank operasional</td>
                            <td class="py-2.5 px-3 text-right font-mono font-semibold text-slate-900 tabular-nums">{{ number_format($setorBank, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900">
                        <tr>
                            <td colspan="2" class="py-2.5 px-3 font-sans text-xs uppercase font-bold">Total Pengeluaran Kas</td>
                            <td class="py-2.5 px-3 text-right font-bold tabular-nums">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Bagian IV: Lembar Tanda Tangan Resmi -->
            <div class="pt-6 border-t-2 border-slate-800">
                <div class="grid grid-cols-3 gap-6 text-center text-xs">
                    <div>
                        <div class="text-slate-500 font-medium">Dibuat Oleh:</div>
                        <div class="text-slate-700 font-semibold mt-1">Kasir Kabar Priangan</div>
                        <div class="h-20"></div>
                        <div class="font-bold text-slate-900 underline">{{ Auth::user()->name ?? 'Kasir' }}</div>
                        <div class="text-[11px] text-slate-400">Petugas Kasir</div>
                    </div>

                    <div>
                        <div class="text-slate-500 font-medium">Diperiksa Oleh:</div>
                        <div class="text-slate-700 font-semibold mt-1">Bagian Keuangan & Akuntansi</div>
                        <div class="h-20"></div>
                        <div class="font-bold text-slate-900 underline">( .................................................. )</div>
                        <div class="text-[11px] text-slate-400">Kabag Keuangan</div>
                    </div>

                    <div>
                        <div class="text-slate-500 font-medium">Disetujui Oleh:</div>
                        <div class="text-slate-700 font-semibold mt-1">Pemimpin Perusahaan</div>
                        <div class="h-20"></div>
                        <div class="font-bold text-slate-900 underline">( .................................................. )</div>
                        <div class="text-[11px] text-slate-400">Direktur / General Manager</div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
