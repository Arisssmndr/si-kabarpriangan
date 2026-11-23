<x-app-layout>
    @php
        $unitPelaksana = Auth::user()?->kasirReportUnitLabel() ?? 'Divisi Kasir';
        $isKasirUser = Auth::user()?->hasRole('kasir') ?? false;
    @endphp
    <div class="space-y-5" x-data="{ periode: '{{ $periodeTipe ?? 'hari' }}' }">

        <!-- ============================================================== -->
        <!-- 1. HEADER HALAMAN (PALET 60-30-10 & ACTION BAR)                -->
        <!-- ============================================================== -->
        <div class="print:hidden no-print bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Laporan Harian Kasir (LHK)
                        </h1>
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-sky-50 text-kp-blue-700 border border-kp-blue-200">
                            Ringkasan Total
                        </span>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $unitPelaksana }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Ringkasan keseluruhan total penerimaan (KI) dan pengeluaran (KO) kas per tanggal, bulan, atau tahun.
                    </p>
                </div>
            </div>

            <!-- Action Toolbar Kanan -->
            <div class="flex items-center gap-2 shrink-0 flex-wrap print:hidden no-print">
                <button type="button" 
                        onclick="window.print()" 
                        class="inline-flex items-center gap-2 h-10 px-4 bg-slate-800 hover:bg-slate-900 active:bg-slate-950 text-white font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak LHK Resmi</span>
                </button>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 2. KPI / STATISTIK CEPAT (METRICS RIBBON 60-30-10)             -->
        <!-- ============================================================== -->
        <div class="print:hidden no-print grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Awal Kas</span>
                <span class="text-2xl font-bold font-mono text-slate-900 mt-1 block">
                    Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Modal kerja periode terpilih</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kas Masuk (KI)</span>
                <span class="text-2xl font-bold font-mono text-kp-blue-700 mt-1 block">
                    + Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">{{ $jumlahTransaksiKi ?? 0 }} Transaksi Kas Masuk (KI)</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kas Keluar (KO)</span>
                <span class="text-2xl font-bold font-mono text-rose-600 mt-1 block">
                    - Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">{{ $jumlahTransaksiKo ?? 0 }} Transaksi Kas Keluar (KO)</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Akhir Kas</span>
                <span class="text-2xl font-bold font-mono text-slate-900 mt-1 block">
                    Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Saldo akhir periode terpilih</span>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 3. FILTER BAR TERPISAH (H-11 STANDAR IKLAN)                    -->
        <!-- ============================================================== -->
        <div class="print:hidden no-print bg-white border border-slate-200 rounded-xl p-4 shadow-xs space-y-3">
            <form method="GET" action="{{ route('kasir.laporan-harian') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                @include('pages.kasir.laporan.partials.period-filter-fields', [
                    'periodeTipe'   => $periodeTipe ?? 'hari',
                    'tanggal'       => $tanggalPilihan,
                    'bulan'         => $bulan ?? date('m'),
                    'tahun'         => $tahun ?? date('Y'),
                    'tipeColClass'  => 'sm:col-span-3',
                    'inputColClass' => 'sm:col-span-6',
                ])
                <div class="sm:col-span-3">
                    <button type="submit" class="h-11 w-full bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-xs cursor-pointer">
                        Tampilkan
                    </button>
                </div>
            </form>
            <div class="text-xs text-slate-500 font-medium">
                Menampilkan ringkasan buku kas untuk periode: <span class="font-bold text-slate-900">{{ $formatTanggal }}</span>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 4. DOKUMEN RESMI LHK (PRINT & SCREEN VIEW)                     -->
        <!-- ============================================================== -->
        <div class="report-print-container bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-xs text-slate-800 print:border-none print:shadow-none print:p-0 space-y-6">
            
            <!-- Kop Dokumen -->
            <div class="border-b-2 border-slate-900 pb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-black tracking-wider uppercase text-slate-900">PT Berkah Pikiran Rakyat</h2>
                        <h3 class="text-base font-bold text-kp-blue-700 tracking-wide uppercase">HU KABAR PRIANGAN</h3>
                        <p class="text-[11px] text-slate-500">Jl. Dr. Sukardjo No. 47, Tawangsari, Kec. Tawang, Kota Tasikmalaya, Jawa Barat 46112</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-black uppercase text-slate-900">Laporan Harian Kasir (LHK)</div>
                        <div class="text-[10px] font-bold uppercase text-slate-600 mt-0.5">SIKAPRI - {{ strtoupper($unitPelaksana) }}</div>
                        <div class="text-xs font-semibold text-kp-blue-700 mt-0.5">{{ $formatTanggal }}</div>
                        <div class="text-[10px] text-slate-500">{{ $unitPelaksana }}: {{ Auth::user()->name ?? $unitPelaksana }}</div>
                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB</div>
                    </div>
                </div>
            </div>

            <!-- Bagian I: Rekonsiliasi Saldo -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 border-b border-slate-200 pb-1 mb-3">
                    I. Rekonsiliasi Saldo Kas
                </h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="py-2.5 text-slate-600 font-medium">A. Saldo Awal Kas (Opening Balance)</td>
                                <td class="py-2.5 text-right font-mono font-bold text-slate-800">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 text-kp-blue-700 font-medium">B. Total Keseluruhan Kas Masuk (KI)</td>
                                <td class="py-2.5 text-right font-mono font-bold text-kp-blue-700">+ Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 text-rose-600 font-medium">C. Total Keseluruhan Kas Keluar (KO)</td>
                                <td class="py-2.5 text-right font-mono font-bold text-rose-600">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="bg-slate-50 font-bold border-t border-slate-200">
                                <td class="py-3 px-3 text-slate-900 uppercase">D. Saldo Akhir Kas Fisik (A + B - C)</td>
                                <td class="py-3 px-3 text-right font-mono text-base text-slate-900">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bagian II: Ringkasan Total Keseluruhan Transaksi (KI & KO) -->
            @php
                $selisihMutasiPeriode = $totalKasMasuk - $totalPengeluaran;
            @endphp
            <div>
                <div class="flex items-center justify-between border-b border-slate-200 pb-1 mb-3 gap-2 flex-wrap">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">
                        II. Ringkasan Total Keseluruhan Transaksi (KI & KO)
                    </h4>
                    <span class="text-[11px] text-slate-400">Rincian per pos tersedia pada menu Rekapitulasi Kas</span>
                </div>
                <div class="overflow-x-auto border border-slate-200 rounded-xl">
                    <table class="w-full text-sm">
                        <thead class="bg-emerald-700 border-b border-emerald-800 text-white font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-2.5 px-4 text-left font-semibold">Jenis Mutasi</th>
                                <th class="py-2.5 px-4 text-left font-semibold">Keterangan</th>
                                <th class="py-2.5 px-4 text-right font-semibold">Jumlah Transaksi</th>
                                <th class="py-2.5 px-4 text-right font-semibold">Total Nominal (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="py-3 px-4 font-bold text-kp-blue-700">Kas Masuk (KI)</td>
                                <td class="py-3 px-4 text-slate-600">Total keseluruhan penerimaan kas periode {{ $formatTanggal }}</td>
                                <td class="py-3 px-4 text-right font-mono font-semibold text-slate-700">{{ number_format($jumlahTransaksiKi ?? 0, 0, ',', '.') }} transaksi</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-kp-blue-700">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="py-3 px-4 font-bold text-rose-600">Kas Keluar (KO)</td>
                                <td class="py-3 px-4 text-slate-600">Total keseluruhan pengeluaran kas periode {{ $formatTanggal }}</td>
                                <td class="py-3 px-4 text-right font-mono font-semibold text-slate-700">{{ number_format($jumlahTransaksiKo ?? 0, 0, ',', '.') }} transaksi</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-rose-600">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-slate-50 font-bold border-t border-slate-200">
                            <tr>
                                <td colspan="2" class="py-3 px-4 text-slate-900 uppercase">Selisih Mutasi Bersih (Total KI - Total KO)</td>
                                <td class="py-3 px-4 text-right font-mono text-xs text-slate-600">{{ number_format(($jumlahTransaksiKi ?? 0) + ($jumlahTransaksiKo ?? 0), 0, ',', '.') }} transaksi</td>
                                <td class="py-3 px-4 text-right font-mono {{ $selisihMutasiPeriode >= 0 ? 'text-slate-900' : 'text-rose-700' }}">
                                    {{ $selisihMutasiPeriode < 0 ? '- ' : '' }}Rp {{ number_format(abs($selisihMutasiPeriode), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Bagian III: Lembar Tanda Tangan Resmi -->
            <div class="pt-6 border-t-2 border-slate-800">
                <div class="grid grid-cols-3 gap-6 text-center text-xs">
                    <div>
                        <div class="text-slate-500 font-medium">Dibuat Oleh:</div>
                        <div class="text-slate-700 font-semibold mt-1">{{ $isKasirUser ? 'Divisi Kasir Kabar Priangan' : $unitPelaksana }}</div>
                        <div class="h-20"></div>
                        <div class="font-bold text-slate-900 underline">{{ Auth::user()->name ?? $unitPelaksana }}</div>
                        <div class="text-[10px] text-slate-400">{{ $unitPelaksana }}</div>
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