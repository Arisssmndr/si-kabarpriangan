<x-app-layout>
    <div class="space-y-6">

        <!-- Header Posisi Saldo Kas Fisik (Hidden on Print) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden no-print">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Posisi Saldo Kas Fisik & Bank (Sistem KAS)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded {{ $activeShift->status === 'open' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-700' }}">
                            Shift: {{ strtoupper($activeShift->status) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Monitoring posisi uang tunai brankas kasir, perhitungan teoritis sistem, serta selisih fisik secara realtime
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 print:hidden no-print">
                <a href="{{ route('kasir.utilitas.closing') }}" class="px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Lakukan Tutup Kas (Closing)</span>
                </a>
                <button type="button" onclick="window.print()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Lembar Saldo</span>
                </button>
            </div>
        </div>

        <!-- Kop Cetak Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print:block border-b-2 border-slate-900 pb-3 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black tracking-wider uppercase text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                    <p class="text-[10px] text-slate-600 font-semibold">DIVISI KASIR & KEUANGAN - SISTEM INFORMASI KAS (SIKAPRI)</p>
                    <p class="text-[9px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</p>
                </div>
                <div class="text-right">
                    <div class="text-xs font-black uppercase text-slate-900">LEMBAR POSISI SALDO KAS & REKONSILIASI</div>
                    <div class="text-[10px] font-semibold text-slate-700">Tanggal: {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
                    <div class="text-[9px] text-slate-400 font-mono">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- 4 Grid Core Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:grid-cols-4 print:gap-2">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Saldo Awal Shift Fisik</span>
                <div class="mt-2 text-lg font-black text-slate-900">
                    Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Modal awal meja kasir</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Kas Masuk (KI Hari Ini)</span>
                <div class="mt-2 text-lg font-black text-teal-700">
                    + Rp {{ number_format($totalKiHariIni, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Iklan, Koran, Eceran & BPR</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Kas Keluar (KO Hari Ini)</span>
                <div class="mt-2 text-lg font-black text-rose-700">
                    - Rp {{ number_format($totalKoHariIni, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Realisasi BP & Komisi</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Saldo Kas Bank (BJB)</span>
                <div class="mt-2 text-lg font-black text-blue-700">
                    Rp {{ number_format($totalBankBulanIni, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Total mutasi bank bulan ini</div>
            </div>
        </div>

        <!-- Detail Perhitungan Rekonsiliasi Saldo Kas Fisik -->
        <div class="report-print-container bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-4">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                Lembar Perhitungan Rekonsiliasi Kas Fisik Laci
            </h3>

            <div class="space-y-3 font-mono text-xs">
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="font-sans font-medium text-slate-600">1. Saldo Modal Kasir Awal Shift</span>
                    <span class="font-bold text-slate-900">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100 text-teal-800">
                    <span class="font-sans font-medium">(+) Total Penerimaan Kas Masuk (KI)</span>
                    <span class="font-bold">Rp {{ number_format($totalKiHariIni, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100 text-rose-800">
                    <span class="font-sans font-medium">(-) Total Pengeluaran Kas Keluar (KO)</span>
                    <span class="font-bold">Rp {{ number_format($totalKoHariIni, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5 bg-slate-50 px-3 rounded-lg border border-slate-200 font-bold">
                    <span class="font-sans text-slate-800 font-bold">2. Saldo Akhir Teoritis Sistem (Awal + KI - KO)</span>
                    <span class="text-sm font-black text-slate-900">Rp {{ number_format($saldoTeoritis, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5 bg-emerald-50 px-3 rounded-lg border border-emerald-200 font-bold">
                    <span class="font-sans text-emerald-900 font-bold">3. Posisi Saldo Kas Fisik Terakhir Dicatat</span>
                    <span class="text-sm font-black text-emerald-800">Rp {{ number_format($saldoFisik, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-3 px-3 rounded-lg border {{ $selisih == 0 ? 'bg-slate-50 border-slate-200' : ($selisih > 0 ? 'bg-blue-50 border-blue-200 text-blue-900' : 'bg-rose-50 border-rose-200 text-rose-900') }}">
                    <div>
                        <span class="font-sans font-bold block">Status Selisih Kas (Fisik - Teoritis)</span>
                        <span class="text-[11px] font-sans text-slate-500">
                            {{ $selisih == 0 ? 'Status Seimbang (Zero Difference). Kas fisik pas dengan catatan sistem.' : ($selisih > 0 ? 'Terdapat kelebihan fisik uang tunai di laci.' : 'Terdapat kekurangan fisik uang tunai di laci!') }}
                        </span>
                    </div>
                    <span class="text-base font-black font-mono">
                        {{ $selisih == 0 ? 'Rp 0 (SEIMBANG)' : ($selisih > 0 ? '+ Rp ' . number_format($selisih, 0, ',', '.') : '- Rp ' . number_format(abs($selisih), 0, ',', '.')) }}
                    </span>
                </div>
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
