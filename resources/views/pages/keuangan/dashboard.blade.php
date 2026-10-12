<x-app-layout>
    <div class="space-y-6">

        <!-- Header Dashboard Divisi Keuangan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Dashboard Divisi Keuangan
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Keuangan
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Monitoring arus kas masuk, pengelolaan piutang, dan verifikasi pelunasan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 text-xs text-slate-600 bg-slate-50 px-3.5 py-2 rounded-lg border border-slate-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Periode Berjalan &bull; {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
            </div>
        </div>

        <!-- 4 KPI Cards Keuangan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Card 1: Penerimaan Kas -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Total Arus Kas Masuk</span>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-lg font-extrabold text-slate-900">
                    Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    Akumulasi penerimaan terverifikasi
                </div>
            </div>

            <!-- Card 2: Piutang -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Sisa Piutang Berjalan</span>
                    <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-lg font-extrabold text-rose-600">
                    Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    Tagihan menanti pelunasan
                </div>
            </div>

            <!-- Card 3: Invoice Pending -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Faktur Belum Lunas</span>
                    <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-lg font-extrabold text-slate-900">
                    {{ $invoicePending }} <span class="text-xs font-normal text-slate-500">Invoice</span>
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    Perlu penagihan & tindak lanjut
                </div>
            </div>

            <!-- Card 4: Penerimaan Hari Ini -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Penerimaan Hari Ini</span>
                    <span class="w-7 h-7 rounded-lg bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-lg font-extrabold text-teal-700">
                    Rp {{ number_format($masukHariIni, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    Setoran kas & transfer masuk
                </div>
            </div>

        </div>

        <!-- Banner Roadmap Menu Operasional Divisi Keuangan -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-slate-900">Menu Operasional Divisi Keuangan</h3>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-amber-50 text-amber-700 border border-amber-200">
                            Dalam Pengembangan Tahap Berikutnya
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 max-w-3xl leading-relaxed">
                        Saat ini dashboard monitoring keuangan telah aktif dan membaca data penerimaan serta piutang berjalan secara real-time. Modul operasional detail seperti di bawah ini sedang dipersiapkan:
                    </p>

                    <!-- Modul Yang Akan Datang -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4">
                        <div class="p-3 rounded-lg border border-slate-200 bg-slate-50">
                            <span class="text-xs font-bold text-slate-800 block">1. Manajemen Penagihan & Invoice</span>
                            <span class="text-[11px] text-slate-500 mt-0.5 block">Surat tagihan jatuh tempo, kwitansi resmi, dan kartu piutang pelanggan.</span>
                        </div>
                        <div class="p-3 rounded-lg border border-slate-200 bg-slate-50">
                            <span class="text-xs font-bold text-slate-800 block">2. Pengeluaran & Kasbon Operasional</span>
                            <span class="text-[11px] text-slate-500 mt-0.5 block">Voucher pengeluaran kas kecil (petty cash) dan verifikasi bukti bayar.</span>
                        </div>
                        <div class="p-3 rounded-lg border border-slate-200 bg-slate-50">
                            <span class="text-xs font-bold text-slate-800 block">3. Rekonsiliasi Bank & Buku Kas</span>
                            <span class="text-[11px] text-slate-500 mt-0.5 block">Sinkronisasi rekening koran dengan penerimaan kasir harian.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
