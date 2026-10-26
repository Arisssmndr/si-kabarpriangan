<x-app-layout>
    <div class="space-y-6">

        <!-- Top Breadcrumbs & Header Bar (OripioFin Style, Hidden on Print) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden no-print">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5 text-slate-400">
                    <button class="w-8 h-8 rounded-full bg-white border border-slate-200/80 flex items-center justify-center hover:bg-slate-50 transition shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button class="w-8 h-8 rounded-full bg-white border border-slate-200/80 flex items-center justify-center hover:bg-slate-50 transition shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
                    <span class="hover:text-slate-600 cursor-pointer">OripioFin</span>
                    <span>&rsaquo;</span>
                    <span class="hover:text-slate-600 cursor-pointer">Laporan</span>
                    <span>&rsaquo;</span>
                    <span class="text-slate-800 font-semibold">Posisi Saldo Kas Fisik & Bank</span>
                </div>
            </div>

            <div class="flex items-center gap-2 print:hidden no-print">
                <a href="{{ route('kasir.utilitas.closing') }}" class="px-4 py-2.5 bg-[#0B6B44] hover:bg-[#095737] text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Lakukan Tutup Kas (Closing)</span>
                </a>
                <button type="button" onclick="window.print()" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Lembar Saldo</span>
                </button>
            </div>
        </div>

        <!-- 4 Grid Core Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:grid-cols-4 print:gap-2">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs shadow-slate-100">
                <span class="text-xs font-semibold text-slate-400">Saldo Awal Shift Fisik</span>
                <div class="mt-2 text-lg font-black text-slate-800 font-mono tracking-tight">
                    Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Modal awal meja kasir</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs shadow-slate-100">
                <span class="text-xs font-semibold text-slate-400">Total Kas Masuk (KI Hari Ini)</span>
                <div class="mt-2 text-lg font-black text-emerald-600 font-mono tracking-tight">
                    + Rp {{ number_format($totalKiHariIni, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-emerald-600/80 font-medium">Iklan, Koran, Eceran & BPR</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs shadow-slate-100">
                <span class="text-xs font-semibold text-slate-400">Total Kas Keluar (KO Hari Ini)</span>
                <div class="mt-2 text-lg font-black text-rose-600 font-mono tracking-tight">
                    - Rp {{ number_format($totalKoHariIni, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-rose-500/80 font-medium">Realisasi BP & Komisi</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs shadow-slate-100">
                <span class="text-xs font-semibold text-slate-400">Saldo Kas Bank (BJB)</span>
                <div class="mt-2 text-lg font-black text-blue-600 font-mono tracking-tight">
                    Rp {{ number_format($totalBankBulanIni, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Total mutasi bank bulan ini</div>
            </div>
        </div>

        <!-- Detail Perhitungan Rekonsiliasi Saldo Kas Fisik -->
        <div class="report-print-container bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs shadow-slate-100 space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#0B6B44]"></span>
                Lembar Perhitungan Rekonsiliasi Kas Fisik Laci
            </h3>

            <div class="space-y-3 font-mono text-xs">
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="font-sans font-medium text-slate-600">1. Saldo Modal Kasir Awal Shift</span>
                    <span class="font-bold text-slate-900">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100 text-emerald-700">
                    <span class="font-sans font-medium">(+) Total Penerimaan Kas Masuk (KI)</span>
                    <span class="font-bold">Rp {{ number_format($totalKiHariIni, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100 text-rose-700">
                    <span class="font-sans font-medium">(-) Total Pengeluaran Kas Keluar (KO)</span>
                    <span class="font-bold">Rp {{ number_format($totalKoHariIni, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-3 bg-slate-50/70 px-4 rounded-xl border border-slate-200/80 font-bold">
                    <span class="font-sans text-slate-800 font-bold">2. Saldo Akhir Teoritis Sistem GL (Awal + KI - KO)</span>
                    <span class="text-sm font-black text-slate-900">Rp {{ number_format($saldoTeoritis, 0, ',', '.') }}</span>
                </div>
                <!-- Faktor Pengurang Kas Fisik Harian: Kasbon / Uang Muka Operasional -->
                <div class="flex items-center justify-between py-3 bg-amber-50/60 px-4 rounded-xl border border-amber-200/80 font-bold text-amber-900">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span class="font-sans font-bold">(-) Faktor Pengurang: Kasbon / Uang Muka Gantung (ACC Keuangan)</span>
                    </div>
                    <span class="text-sm font-black font-mono text-rose-700">- Rp {{ number_format($totalKasbonGantung, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-3 bg-emerald-50/50 px-4 rounded-xl border border-emerald-200/80 font-bold">
                    <span class="font-sans text-[#0B6B44] font-bold">3. Target Saldo Kas Fisik Bersih di Laci Kasir (Teoritis - Kasbon Gantung)</span>
                    <span class="text-sm font-black text-[#0B6B44]">Rp {{ number_format($saldoFisikDiharapkan, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-3 bg-slate-50/70 px-4 rounded-xl border border-slate-200/80 font-bold">
                    <span class="font-sans text-slate-800 font-bold">4. Posisi Saldo Kas Fisik Tercatat</span>
                    <span class="text-sm font-black text-slate-900">Rp {{ number_format($saldoFisik, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-3.5 px-4 rounded-xl border {{ $selisih == 0 ? 'bg-emerald-50/50 border-emerald-200/80 text-emerald-900' : ($selisih > 0 ? 'bg-sky-50 border-sky-200/80 text-sky-900' : 'bg-rose-50 border-rose-200/80 text-rose-900') }}">
                    <div>
                        <span class="font-sans font-bold block">Status Rekonsiliasi Kas (Fisik Laci + Voucher Kasbon vs Saldo Sistem)</span>
                        <span class="text-[11px] font-sans text-slate-500">
                            {{ $selisih == 0 ? 'Status Seimbang (Zero Difference). Total uang fisik laci ditambah lembar bukti voucher kasbon seimbang dengan saldo sistem.' : ($selisih > 0 ? 'Terdapat kelebihan fisik uang tunai di laci.' : 'Terdapat kekurangan fisik uang tunai di laci!') }}
                        </span>
                    </div>
                    <span class="text-base font-black font-mono">
                        {{ $selisih == 0 ? 'Rp 0 (SEIMBANG)' : ($selisih > 0 ? '+ Rp ' . number_format($selisih, 0, ',', '.') : '- Rp ' . number_format(abs($selisih), 0, ',', '.')) }}
                    </span>
                </div>
            </div>

            @if(isset($daftarKasbonGantung) && $daftarKasbonGantung->count() > 0)
            <!-- Rincian Slip Kasbon Gantung yang Memotong Kas Fisik -->
            <div class="mt-4 pt-3 border-t border-slate-100">
                <div class="text-[11px] font-bold text-amber-900 uppercase tracking-wider mb-2 flex items-center justify-between">
                    <span>Daftar Slip Kasbon Gantung di Meja Kasir:</span>
                    <span class="text-slate-400 font-normal">Total: {{ $daftarKasbonGantung->count() }} slip</span>
                </div>
                <div class="space-y-2 text-xs font-sans">
                    @foreach($daftarKasbonGantung as $kg)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50/40 border border-amber-200/60 text-slate-800">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-amber-900">{{ $kg->no_kasbon }}</span>
                            <span>•</span>
                            <span class="font-semibold text-slate-900">{{ $kg->nama_penerima }}</span>
                            <span class="text-slate-500">({{ $kg->keperluan }})</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60 font-semibold">ACC: {{ Str::limit($kg->approved_by, 15) }}</span>
                            <span class="font-mono font-bold text-rose-700">Rp {{ number_format($kg->nominal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
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
