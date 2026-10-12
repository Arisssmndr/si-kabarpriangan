<x-app-layout>
    <div class="space-y-6">

        <!-- Header Dashboard Divisi Accounting -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Dashboard Divisi Accounting
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-purple-50 text-purple-700 border border-purple-200">
                            Accounting
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Ringkasan pembukuan, pos akun buku besar, dan estimasi laba/rugi berjalan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 text-xs text-slate-600 bg-slate-50 px-3.5 py-2 rounded-lg border border-slate-200">
                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                <span>Standar SAK EMKM &bull; Tahun Fiskal {{ \Carbon\Carbon::now()->year }}</span>
            </div>
        </div>

        <!-- 4 KPI Cards Accounting -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Card 1: Omset Usaha -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Pendapatan Usaha</span>
                    <span class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-lg font-extrabold text-slate-900">
                    Rp {{ number_format($omsetUsaha, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    Total tagihan diakui berjalan
                </div>
            </div>

            <!-- Card 2: Kas Terbukukan -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Penerimaan Kas Riil</span>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-lg font-extrabold text-emerald-600">
                    Rp {{ number_format($penerimaanKas, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    Debet kas & bank terverifikasi
                </div>
            </div>

            <!-- Card 3: Piutang Tercatat -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Piutang Usaha</span>
                    <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-lg font-extrabold text-slate-900">
                    Rp {{ number_format($piutangUsaha, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    Saldo piutang pada neraca
                </div>
            </div>

            <!-- Card 4: Estimasi Laba Bersih -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Estimasi Laba Kotor</span>
                    <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-200 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    </span>
                </div>
                <div class="mt-2 text-lg font-extrabold text-indigo-700">
                    Rp {{ number_format($estimasiLabaBersih, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">
                    Setelah estimasi HPP & beban pokok
                </div>
            </div>

        </div>

        <!-- Banner Roadmap Menu Operasional Divisi Accounting -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-slate-900">Menu Operasional Divisi Accounting</h3>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-amber-50 text-amber-700 border border-amber-200">
                            Dalam Pengembangan Tahap Berikutnya
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 max-w-3xl leading-relaxed">
                        Modul pembukuan otomatis dan pencatatan jurnal berpasangan (double-entry bookkeeping) sedang dalam tahap perancangan sistem:
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4">
                        <div class="p-3 rounded-lg border border-slate-200 bg-slate-50">
                            <span class="text-xs font-bold text-slate-800 block">1. Bagan Akun (Chart of Accounts)</span>
                            <span class="text-[11px] text-slate-500 mt-0.5 block">Struktur kode rekening aset, kewajiban, ekuitas, pendapatan, dan beban.</span>
                        </div>
                        <div class="p-3 rounded-lg border border-slate-200 bg-slate-50">
                            <span class="text-xs font-bold text-slate-800 block">2. Jurnal Umum & Buku Besar</span>
                            <span class="text-[11px] text-slate-500 mt-0.5 block">Posting otomatis dari transaksi iklan dan kasir harian ke pos buku besar.</span>
                        </div>
                        <div class="p-3 rounded-lg border border-slate-200 bg-slate-50">
                            <span class="text-xs font-bold text-slate-800 block">3. Neraca & Laporan Laba/Rugi</span>
                            <span class="text-[11px] text-slate-500 mt-0.5 block">Laporan keuangan bulanan/tahunan terstandarisasi untuk manajemen eksekutif.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
