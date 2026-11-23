<x-app-layout>
    @php
        $unitPelaksana = Auth::user()?->kasirReportUnitLabel() ?? 'Divisi Kasir';
        $isKasirUser = Auth::user()?->hasRole('kasir') ?? false;
    @endphp
    <div class="space-y-5" x-data="{ periode: '{{ $periodeTipe ?? 'hari' }}' }">

        <!-- ============================================================== -->
        <!-- 1. HEADER HALAMAN (PALET 60-30-10 & ACTION BAR)                -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden no-print">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Posisi Saldo Kas Fisik & Bank
                        </h1>
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $activeShift->status === 'open' ? 'bg-sky-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-300' }}">
                            Shift: {{ strtoupper($activeShift->status) }}
                        </span>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $unitPelaksana }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Monitoring posisi uang tunai brankas kasir, perhitungan teoritis sistem, serta selisih fisik per tanggal, bulan, atau tahun.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0 flex-wrap print:hidden no-print">
                <a href="{{ route('kasir.utilitas.closing') }}" 
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Tutup Kas (Closing)</span>
                </a>
                <button type="button" 
                        onclick="window.print()" 
                        class="inline-flex items-center gap-2 h-10 px-4 bg-slate-800 hover:bg-slate-900 active:bg-slate-950 text-white font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak Lembar Saldo</span>
                </button>
            </div>
        </div>

        <!-- Kop Cetak Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print:block border-b-2 border-slate-900 pb-3 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black tracking-wider uppercase text-slate-900">HU KABAR PRIANGAN</h2>
                    <p class="text-[10px] text-slate-600 font-semibold">{{ strtoupper($unitPelaksana) }} - SISTEM INFORMASI KAS (SIKAPRI)</p>
                    <p class="text-[9px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</p>
                </div>
                <div class="text-right">
                    <div class="text-xs font-black uppercase text-slate-900">LEMBAR POSISI SALDO KAS & REKONSILIASI</div>
                    <div class="text-[10px] font-semibold text-slate-700">Periode: {{ $labelPeriode ?? \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
                    <div class="text-[9px] text-slate-500 font-semibold">{{ $unitPelaksana }}: {{ Auth::user()->name }}</div>
                    <div class="text-[9px] text-slate-400 font-mono">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 2. KPI / STATISTIK CEPAT (METRICS RIBBON 60-30-10)             -->
        <!-- ============================================================== -->
        <div class="print:hidden no-print grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Awal Shift Fisik</span>
                <span class="text-2xl font-bold font-mono text-slate-900 mt-1 block">
                    Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Modal awal periode terpilih</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kas Masuk (KI)</span>
                <span class="text-2xl font-bold font-mono text-kp-blue-700 mt-1 block">
                    + Rp {{ number_format($totalKiHariIni, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">{{ $labelPeriode ?? 'Hari Ini' }}</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kas Keluar (KO)</span>
                <span class="text-2xl font-bold font-mono text-rose-600 mt-1 block">
                    - Rp {{ number_format($totalKoHariIni, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">{{ $labelPeriode ?? 'Hari Ini' }}</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Setor / Mutasi Bank (BJB)</span>
                <span class="text-2xl font-bold font-mono text-slate-900 mt-1 block">
                    Rp {{ number_format($totalBankBulanIni, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">{{ $labelPeriode ?? 'Periode Terpilih' }}</span>
            </div>
        </div>

        <!-- Filter Periode (Per Tanggal / Per Bulan / Per Tahun) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs print:hidden no-print">
            <form method="GET" action="{{ route('kasir.laporan.posisi-kas') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                @include('pages.kasir.laporan.partials.period-filter-fields', [
                    'periodeTipe'   => $periodeTipe ?? 'hari',
                    'tanggal'       => $tanggal ?? date('Y-m-d'),
                    'bulan'         => $bulan ?? date('m'),
                    'tahun'         => $tahun ?? date('Y'),
                    'tipeColClass'  => 'sm:col-span-3',
                    'inputColClass' => 'sm:col-span-7',
                ])
                <div class="sm:col-span-2">
                    <button type="submit" class="h-11 w-full bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-xs cursor-pointer">
                        Tampilkan
                    </button>
                </div>
            </form>
        </div>

        <!-- ============================================================== -->
        <!-- 3. LEMBAR PERHITUNGAN REKONSILIASI KAS FISIK LACI              -->
        <!-- ============================================================== -->
        <div class="report-print-container bg-white border border-slate-200 rounded-xl p-5 sm:p-6 shadow-xs space-y-4">
            <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-kp-blue-600"></span>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                        Lembar Perhitungan Rekonsiliasi Kas Fisik Laci
                    </h3>
                </div>
                <span class="text-xs font-medium text-slate-500 hidden sm:inline">Metode: Standar Akuntansi Perusahaan</span>
            </div>

            <div class="space-y-3 font-mono text-xs">
                <div class="flex items-center justify-between py-2.5 border-b border-slate-100">
                    <span class="font-sans font-medium text-slate-600 text-sm">1. Saldo Modal Kasir Awal Shift</span>
                    <span class="font-bold text-slate-900 text-sm">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5 border-b border-slate-100 text-kp-blue-700">
                    <span class="font-sans font-medium text-sm">(+) Total Penerimaan Kas Masuk (KI)</span>
                    <span class="font-bold text-sm">Rp {{ number_format($totalKiHariIni, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5 border-b border-slate-100 text-rose-600">
                    <span class="font-sans font-medium text-sm">(-) Total Pengeluaran Kas Keluar (KO)</span>
                    <span class="font-bold text-sm">Rp {{ number_format($totalKoHariIni, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-3 bg-slate-50 px-4 rounded-xl border border-slate-200">
                    <span class="font-sans text-slate-800 font-bold text-sm">2. Saldo Akhir Teoritis Sistem GL (Awal + KI - KO)</span>
                    <span class="text-base font-black text-slate-900">Rp {{ number_format($saldoTeoritis, 0, ',', '.') }}</span>
                </div>

                <div class="flex items-center justify-between py-3 bg-sky-50/80 px-4 rounded-xl border border-sky-200 text-slate-900">
                    <span class="font-sans font-bold text-sm text-[#0E527B]">3. Target Saldo Kas Fisik Bersih di Laci Kasir</span>
                    <span class="text-base font-black text-kp-blue-700">Rp {{ number_format($saldoFisikDiharapkan, 0, ',', '.') }}</span>
                </div>

                <div class="flex items-center justify-between py-3 bg-slate-50 px-4 rounded-xl border border-slate-200">
                    <span class="font-sans text-slate-800 font-bold text-sm">4. Posisi Saldo Kas Fisik Tercatat</span>
                    <span class="text-base font-black text-slate-900">Rp {{ number_format($saldoFisik, 0, ',', '.') }}</span>
                </div>

                <div class="flex items-center justify-between p-4 rounded-xl border {{ $selisih == 0 ? 'bg-sky-50/60 border-sky-200 text-slate-900' : ($selisih > 0 ? 'bg-slate-50 border-slate-300 text-slate-900' : 'bg-rose-50 border-rose-200 text-rose-900') }}">
                    <div>
                        <span class="font-sans font-bold block text-sm">Status Rekonsiliasi Kas (Fisik Laci vs Saldo Sistem)</span>
                        <span class="text-xs font-sans text-slate-500 mt-0.5 block">
                            {{ $selisih == 0 ? 'Status Seimbang (Zero Difference). Total uang fisik laci seimbang dengan saldo sistem.' : ($selisih > 0 ? 'Terdapat kelebihan fisik uang tunai di laci.' : 'Terdapat kekurangan fisik uang tunai di laci!') }}
                        </span>
                    </div>
                    <span class="text-base font-black font-mono shrink-0 ml-4">
                        {{ $selisih == 0 ? 'Rp 0 (SEIMBANG)' : ($selisih > 0 ? '+ Rp ' . number_format($selisih, 0, ',', '.') : '- Rp ' . number_format(abs($selisih), 0, ',', '.')) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Tanda Tangan Laporan Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print:grid grid-cols-3 gap-6 text-center text-xs pt-6 mt-6 border-t border-slate-300">
            <div>
                <div class="text-slate-500">Pelaksana:</div>
                <div class="text-slate-700 font-semibold mt-1">{{ $isKasirUser ? 'Petugas Kasir' : $unitPelaksana }}</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">{{ Auth::user()->name }}</div>
                <div class="text-[10px] text-slate-500">{{ $unitPelaksana }}</div>
            </div>
            <div>
                <div class="text-slate-500">Diperiksa Oleh:</div>
                <div class="text-slate-700 font-semibold mt-1">Supervisor / Akuntansi</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                <div class="text-[10px] text-slate-500">Kabag Keuangan</div>
            </div>
            <div>
                <div class="text-slate-500">Mengetahui:</div>
                <div class="text-slate-700 font-semibold mt-1">Pemimpin Perusahaan</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                <div class="text-[10px] text-slate-500">Direktur / GM</div>
            </div>
        </div>

    </div>
</x-app-layout>