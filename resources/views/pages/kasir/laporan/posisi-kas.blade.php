<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Hidden on Print) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 print:hidden no-print">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Posisi Saldo Kas Fisik & Bank
                        </h1>
                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium {{ $activeShift->status === 'open' ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                            Shift: {{ strtoupper($activeShift->status) }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Rekonsiliasi saldo kas fisik laci, perhitungan sistem, dan posisi bank berjalan.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap print:hidden no-print">
                <a href="{{ route('kasir.utilitas.closing') }}" class="inline-flex items-center gap-2 h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer whitespace-nowrap">
                    <span>Tutup Kas (Closing)</span>
                </a>
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Lembar Saldo</span>
                </button>
            </div>
        </div>

        <!-- Kop Cetak Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print:block border-b-2 border-slate-900 pb-3 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold tracking-wider uppercase text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                    <p class="text-[10px] text-slate-600 font-semibold">DIVISI KASIR & KEUANGAN - SISTEM INFORMASI KAS (SIKAPRI)</p>
                    <p class="text-[9px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</p>
                </div>
                <div class="text-right">
                    <div class="text-xs font-bold uppercase text-slate-900">LEMBAR POSISI SALDO KAS & REKONSILIASI</div>
                    <div class="text-[10px] font-semibold text-slate-700">Tanggal: {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
                    <div class="text-[9px] text-slate-400 font-mono">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- 4 Kartu KPI -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:grid-cols-4 print:gap-2">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Awal Shift</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Modal awal meja kasir</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kas Masuk (KI Hari Ini)</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalKiHariIni, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Iklan, Koran, Eceran & BPR</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kas Keluar (KO Hari Ini)</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalKoHariIni, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Realisasi BP & Komisi</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Kas Bank (BJB)</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalBankBulanIni, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Total mutasi bank bulan ini</span>
            </div>
        </div>

        <!-- Detail Perhitungan Rekonsiliasi Saldo Kas Fisik -->
        <div class="report-print-container bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200">
                <h2 class="text-sm font-bold text-slate-900">Lembar Perhitungan Rekonsiliasi Kas Fisik Laci</h2>
                <p class="text-xs text-slate-500">Perbandingan posisi kas fisik laci dan voucher kasbon terhadap saldo sistem</p>
            </div>

            <div class="p-5 sm:p-6 space-y-3 font-mono text-sm">
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="font-sans text-slate-700">1. Saldo Modal Kasir Awal Shift</span>
                    <span class="font-semibold text-slate-900 tabular-nums">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="font-sans text-slate-700">(+) Total Penerimaan Kas Masuk (KI)</span>
                    <span class="font-semibold text-kp-blue-700 tabular-nums">Rp {{ number_format($totalKiHariIni, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="font-sans text-slate-700">(-) Total Pengeluaran Kas Keluar (KO)</span>
                    <span class="font-semibold text-slate-900 tabular-nums">Rp {{ number_format($totalKoHariIni, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5 px-3.5 bg-slate-50 rounded-xl border border-slate-200 font-semibold">
                    <span class="font-sans text-slate-900 text-xs sm:text-sm">2. Saldo Akhir Teoritis Sistem GL (Awal + KI - KO)</span>
                    <span class="text-slate-900 tabular-nums">Rp {{ number_format($saldoTeoritis, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5 px-3.5 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="font-sans font-medium text-slate-700 text-xs sm:text-sm">(-) Kasbon / Uang Muka Gantung Aktif</span>
                    <span class="font-semibold text-slate-900 tabular-nums">- Rp {{ number_format($totalKasbonGantung, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5 px-3.5 bg-kp-blue-50/50 rounded-xl border border-kp-blue-200 font-bold">
                    <span class="font-sans text-kp-blue-700 text-xs sm:text-sm">3. Target Saldo Kas Fisik Bersih di Laci Kasir</span>
                    <span class="text-kp-blue-700 tabular-nums">Rp {{ number_format($saldoFisikDiharapkan, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5 px-3.5 bg-slate-50 rounded-xl border border-slate-200 font-bold">
                    <span class="font-sans text-slate-900 text-xs sm:text-sm">4. Posisi Saldo Kas Fisik Tercatat</span>
                    <span class="text-slate-900 tabular-nums">Rp {{ number_format($saldoFisik, 0, ',', '.') }}</span>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 py-3 px-4 rounded-xl border {{ $selisih == 0 ? 'bg-kp-blue-50 border-kp-blue-200 text-kp-blue-700' : 'bg-slate-100 border-slate-300 text-slate-900' }}">
                    <div>
                        <span class="font-sans font-bold text-sm block">Status Rekonsiliasi Kas</span>
                        <span class="text-xs font-sans text-slate-500">
                            {{ $selisih == 0 ? 'Seimbang (Zero Difference).' : ($selisih > 0 ? 'Terdapat kelebihan fisik uang tunai di laci.' : 'Terdapat kekurangan fisik uang tunai di laci.') }}
                        </span>
                    </div>
                    <span class="text-base font-bold font-mono tabular-nums">
                        {{ $selisih == 0 ? 'Rp 0 (SEIMBANG)' : ($selisih > 0 ? '+ Rp ' . number_format($selisih, 0, ',', '.') : '- Rp ' . number_format(abs($selisih), 0, ',', '.')) }}
                    </span>
                </div>

                @if(isset($daftarKasbonGantung) && $daftarKasbonGantung->count() > 0)
                <div class="pt-4 mt-2 border-t border-slate-200 font-sans">
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Daftar Slip Kasbon Gantung</span>
                        <span class="text-xs font-mono font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2 py-0.5 rounded-md">{{ $daftarKasbonGantung->count() }} Slip</span>
                    </div>
                    <div class="space-y-1.5 text-xs">
                        @foreach($daftarKasbonGantung as $kg)
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-800">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="font-mono font-bold text-kp-blue-700">{{ $kg->no_kasbon }}</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="font-semibold text-slate-900">{{ $kg->nama_penerima }}</span>
                                <span class="text-slate-500 truncate">({{ $kg->keperluan }})</span>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 ml-2">
                                <span class="px-2 py-0.5 rounded bg-white border border-slate-200 text-slate-600 font-medium">ACC: {{ Str::limit($kg->approved_by, 15) }}</span>
                                <span class="font-mono font-semibold text-slate-900 tabular-nums">Rp {{ number_format($kg->nominal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Tanda Tangan Laporan Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print:grid grid-cols-3 gap-6 text-center text-xs pt-6 mt-6 border-t border-slate-300">
            <div>
                <div class="text-slate-500">Kasir Jaga:</div>
                <div class="text-slate-700 font-semibold mt-1">Petugas Kasir</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">{{ Auth::user()->name }}</div>
                <div class="text-[10px] text-slate-500">Kasir Shift {{ strtoupper($activeShift->status) }}</div>
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
