<x-app-layout>
    <div class="space-y-6">

        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Pusat Kendali Administrator
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        Pantau seluruh divisi operasional Kabar Priangan.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 text-xs sm:text-sm font-medium text-slate-700 bg-slate-50 px-3.5 py-2 rounded-xl border border-slate-200">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Sistem Aktif &bull; {{ $totalUsers }} Pengguna</span>
            </div>
        </div>

        <!-- Pusat Kendali Administrator Standar Industri (3 Pilar Utama) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- 1. Laporan Eksekutif -->
            <a href="{{ route('admin.laporan.index') }}" 
               class="group bg-gradient-to-br from-indigo-50/60 to-white border border-indigo-200/80 hover:border-indigo-500 rounded-xl p-4 shadow-xs hover:shadow-md transition-all duration-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-indigo-700 transition">Laporan</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Ringkasan kinerja seluruh divisi</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>

            <!-- 2. Pengaturan Sistem Terpusat -->
            <a href="{{ route('admin.pengaturan.index') }}" 
               class="group bg-gradient-to-br from-sky-50/60 to-white border border-sky-200/80 hover:border-kp-blue-500 rounded-xl p-4 shadow-xs hover:shadow-md transition-all duration-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-sky-100 text-kp-blue-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-kp-blue-700 transition">Pengaturan</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Data perusahaan, rekening, dan pajak</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-kp-blue-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>

            <!-- 3. Kesehatan Sistem & Live Log -->
            <a href="{{ route('admin.sistem.index') }}" 
               class="group bg-gradient-to-br from-emerald-50/60 to-white border border-emerald-200/80 hover:border-emerald-500 rounded-xl p-4 shadow-xs hover:shadow-md transition-all duration-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-emerald-700 transition">Status Sistem</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Kondisi server, database, dan log terkini</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>

        <!-- Kartu Akses Cepat Ke 5 Divisi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <!-- 1. Divisi Iklan -->
            <a href="{{ route('iklan.dashboard') }}" 
               class="group bg-white border border-slate-200 hover:border-kp-blue-500 rounded-xl p-4 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-sky-50 text-kp-blue-700 border border-sky-200 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                            </svg>
                        </span>
                        <span class="text-xs font-semibold text-slate-600 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-md">
                            Aktif
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-kp-blue-700 transition">Divisi Iklan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Koran, Online, Priangan TV</p>
                    <div class="mt-3 text-lg font-extrabold text-slate-900 font-tabular">
                        Rp {{ number_format($totalOmsetIklan, 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs sm:text-sm text-kp-blue-600 font-semibold">
                    <span>Buka Divisi</span>
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </div>
            </a>

            <!-- 2. Divisi Keuangan -->
            <a href="{{ route('keuangan.dashboard') }}" 
               class="group bg-white border border-slate-200 hover:border-emerald-500 rounded-xl p-4 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm6 3.75a3 3 0 11-6 0 3 3 0 016 0zM6 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        <span class="text-xs font-semibold text-slate-600 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-md">
                            Aktif
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition">Divisi Keuangan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Arus Kas &amp; Piutang</p>
                    <div class="mt-3 text-lg font-extrabold text-slate-900 font-tabular">
                        Rp {{ number_format($arusKasMasuk, 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs sm:text-sm text-emerald-600 font-semibold">
                    <span>Buka Divisi</span>
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </div>
            </a>

            <!-- 3. Divisi Accounting -->
            <a href="{{ route('accounting.dashboard') }}" 
               class="group bg-white border border-slate-200 hover:border-purple-500 rounded-xl p-4 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-purple-50 text-purple-700 border border-purple-200 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                        </span>
                        <span class="text-xs font-semibold text-slate-600 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-md">
                            Aktif
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-purple-700 transition">Divisi Accounting</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Laba/Rugi &amp; Buku Besar</p>
                    <div class="mt-3 text-lg font-extrabold text-slate-900 font-tabular">
                        Rp {{ number_format($labaKotorBerjalan, 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs sm:text-sm text-purple-600 font-semibold">
                    <span>Buka Divisi</span>
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </div>
            </a>

            <!-- 4. Divisi Sirkulasi -->
            <a href="{{ route('sirkulasi.dashboard') }}" 
               class="group bg-white border border-slate-200 hover:border-amber-500 rounded-xl p-4 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V3.75m0 3.75h-3.75m3.75 0V15m-3.75-7.5H4.875c-.621 0-1.125.504-1.125 1.125v4.5c0 .621.504 1.125 1.125 1.125h3.75" />
                            </svg>
                        </span>
                        <span class="text-xs font-semibold text-slate-600 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-md">
                            Aktif
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-amber-700 transition">Divisi Sirkulasi</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Oplah &amp; Distribusi Agen</p>
                    <div class="mt-3 text-lg font-extrabold text-slate-900 font-tabular">
                        {{ number_format($oplahHariIni, 0, ',', '.') }} Eks
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs sm:text-sm text-amber-600 font-semibold">
                    <span>Buka Divisi</span>
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </div>
            </a>

            <!-- 5. Divisi Kasir -->
            <a href="{{ route('kasir.dashboard') }}" 
               class="group bg-white border border-slate-200 hover:border-teal-500 rounded-xl p-4 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="w-8 h-8 rounded-lg bg-teal-50 text-teal-700 border border-teal-200 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 21z" />
                            </svg>
                        </span>
                        <span class="text-xs font-semibold text-slate-600 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-md">
                            Aktif
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-teal-700 transition">Divisi Kasir</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Penerimaan Kas Harian</p>
                    <div class="mt-3 text-lg font-extrabold text-slate-900 font-tabular">
                        Rp {{ number_format($penerimaanHariIni, 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs sm:text-sm text-teal-600 font-semibold">
                    <span>Buka Divisi</span>
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </div>
            </a>

        </div>

        <!-- Ikhtisar Komprehensif Lintas Divisi -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Panel 1: Iklan & Pendapatan -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-kp-blue-600"></span>
                            Ringkasan Iklan
                        </h2>
                        <a href="{{ route('iklan.dashboard') }}" class="text-xs sm:text-sm text-kp-blue-600 font-semibold hover:underline">Detail &rarr;</a>
                    </div>
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Total Omset Iklan</span>
                            <span class="font-extrabold text-slate-900 font-tabular">Rp {{ number_format($totalOmsetIklan, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Total Pembayaran Diterima</span>
                            <span class="font-bold text-emerald-600 font-tabular">Rp {{ number_format($totalBayarIklan, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Total Sisa Piutang</span>
                            <span class="font-bold text-rose-600 font-tabular">Rp {{ number_format($totalPiutangIklan, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Total Transaksi Iklan</span>
                            <span class="font-semibold text-slate-800">{{ $totalTxIklan }} transaksi</span>
                        </div>
                    </div>
                </div>
                <div class="mt-5 pt-3 border-t border-slate-100 bg-slate-50 -mx-5 -mb-5 p-4 rounded-b-xl flex items-center justify-between text-xs">
                    <span class="text-slate-500">Status Modul Iklan:</span>
                    <span class="text-emerald-700 font-bold bg-emerald-100/70 px-2 py-0.5 rounded">Operasional &amp; Aktif</span>
                </div>
            </div>

            <!-- Panel 2: Keuangan & Pembukuan -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            Ringkasan Keuangan
                        </h2>
                        <a href="{{ route('keuangan.dashboard') }}" class="text-xs sm:text-sm text-emerald-600 font-semibold hover:underline">Detail &rarr;</a>
                    </div>
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Total Arus Kas Masuk</span>
                            <span class="font-extrabold text-slate-900 font-tabular">Rp {{ number_format($arusKasMasuk, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Estimasi Beban Operasional</span>
                            <span class="font-bold text-slate-700 font-tabular">Rp {{ number_format($estimasiPengeluaranBulanIni, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Estimasi Laba Kotor Berjalan</span>
                            <span class="font-bold text-emerald-600 font-tabular">Rp {{ number_format($labaKotorBerjalan, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Keseimbangan Buku Besar</span>
                            <span class="font-semibold text-slate-800">{{ $statusBukuBesar }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-5 pt-3 border-t border-slate-100 bg-slate-50 -mx-5 -mb-5 p-4 rounded-b-xl flex items-center justify-between text-xs">
                    <span class="text-slate-500">Jurnal Pending:</span>
                    <span class="text-amber-700 font-bold bg-amber-100/70 px-2 py-0.5 rounded">{{ $totalJurnalPending }} Perlu Verifikasi</span>
                </div>
            </div>

            <!-- Panel 3: Sirkulasi & Kasir -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-600"></span>
                            Sirkulasi &amp; Kasir
                        </h2>
                        <a href="{{ route('sirkulasi.dashboard') }}" class="text-xs sm:text-sm text-amber-600 font-semibold hover:underline">Detail &rarr;</a>
                    </div>
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Oplah Cetak Harian</span>
                            <span class="font-extrabold text-slate-900 font-tabular">{{ number_format($oplahHariIni, 0, ',', '.') }} Eks</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Efektivitas Distribusi Agen</span>
                            <span class="font-bold text-emerald-600">{{ $persentaseTerdistribusi }}%</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Penerimaan Kasir Hari Ini</span>
                            <span class="font-bold text-teal-600 font-tabular">Rp {{ number_format($penerimaanHariIni, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-slate-500">Transaksi Kasir Hari Ini</span>
                            <span class="font-semibold text-slate-800">{{ $txHariIni }} struk</span>
                        </div>
                    </div>
                </div>
                <div class="mt-5 pt-3 border-t border-slate-100 bg-slate-50 -mx-5 -mb-5 p-4 rounded-b-xl flex items-center justify-between text-xs">
                    <span class="text-slate-500">Jaringan Distribusi:</span>
                    <span class="text-slate-700 font-bold bg-slate-200/70 px-2 py-0.5 rounded">{{ $totalAgenAktif }} Agen Aktif</span>
                </div>
            </div>

        </div>

        <!-- ============================================================== -->
        <!-- WIDGET LIVE MONITORING STATUS SESI ADMIN & PENGGUNA REAL-TIME  -->
        <!-- ============================================================== -->
        <div x-data="adminLiveStatus({
            pollUrl: '{{ route('admin.users.online-status') }}',
            initialUsers: {{ \Illuminate\Support\Js::from($initialUsersData ?? []) }},
            initialOnlineCount: {{ $onlineCount ?? 0 }},
            initialOfflineCount: {{ $offlineCount ?? 0 }},
            initialTotalCount: {{ $totalUsers ?? 0 }},
            initialAdminOnlineCount: {{ $adminOnlineCount ?? 0 }},
            currentUserId: {{ auth()->id() ?? 0 }}
        })" class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            
            <!-- Header Widget -->
            <div class="p-5 border-b border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="relative w-11 h-11 rounded-2xl bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        <span class="absolute -top-1 -right-1 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                        </span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-slate-900 tracking-tight">Live Status Sesi Admin & Pengguna</h2>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300">
                                Live Polling
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Status aktif langsung saat login dan nonaktif otomatis saat logout di seluruh akun admin & staf.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                    <!-- Live Sync Indicator -->
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-bold text-emerald-800">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>Auto-Sync 5 Detik</span>
                        <span class="font-mono text-[10px] text-emerald-600 font-bold" x-text="lastSynced ? '• ' + lastSynced : ''"></span>
                    </div>

                    <a href="{{ route('admin.users.index') }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl border border-slate-300 transition">
                        <span>Kelola Pengguna</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            </div>

            <!-- Tab Filter Cepat & Ringkasan -->
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/70 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <button type="button" 
                            @click="filter = 'all'" 
                            :class="filter === 'all' ? 'bg-white text-slate-900 shadow-xs border-slate-300 font-bold' : 'text-slate-600 hover:bg-slate-200/50 border-transparent font-medium'"
                            class="px-3 py-1.5 rounded-xl border text-xs transition cursor-pointer flex items-center gap-1.5">
                        <span>Semua Staf</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-800 text-[10px] font-extrabold" x-text="totalCount">{{ $totalUsers }}</span>
                    </button>

                    <button type="button" 
                            @click="filter = 'online'" 
                            :class="filter === 'online' ? 'bg-emerald-50 text-emerald-900 border-emerald-300 font-bold' : 'text-slate-600 hover:bg-slate-200/50 border-transparent font-medium'"
                            class="px-3 py-1.5 rounded-xl border text-xs transition cursor-pointer flex items-center gap-1.5">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>Aktif Sekarang</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-emerald-100 text-emerald-900 text-[10px] font-black" x-text="onlineCount">{{ $onlineCount }}</span>
                    </button>

                    <button type="button" 
                            @click="filter = 'offline'" 
                            :class="filter === 'offline' ? 'bg-white text-slate-900 shadow-xs border-slate-300 font-bold' : 'text-slate-600 hover:bg-slate-200/50 border-transparent font-medium'"
                            class="px-3 py-1.5 rounded-xl border text-xs transition cursor-pointer flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        <span>Nonaktif</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-extrabold" x-text="offlineCount">{{ $offlineCount }}</span>
                    </button>

                    <button type="button" 
                            @click="filter = 'admin'" 
                            :class="filter === 'admin' ? 'bg-rose-50 text-rose-900 border-rose-300 font-bold' : 'text-slate-600 hover:bg-slate-200/50 border-transparent font-medium'"
                            class="px-3 py-1.5 rounded-xl border text-xs transition cursor-pointer flex items-center gap-1.5">
                        <span>Administrator</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-rose-100 text-rose-800 text-[10px] font-black" x-text="adminOnlineCount + ' Aktif'"></span>
                    </button>
                </div>

                <div class="text-xs text-slate-500 font-medium">
                    Status diperbarui live tanpa perlu refresh halaman
                </div>
            </div>

            <!-- Grid Kartu Live Status Staf & Administrator -->
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5">
                    <template x-for="u in filteredUsers" :key="u.id">
                        <div class="p-4 rounded-xl border transition-all duration-200 flex flex-col justify-between"
                             :class="u.is_online ? 'bg-emerald-50/30 border-emerald-300 shadow-xs ring-1 ring-emerald-100' : 'bg-white border-slate-200 hover:border-slate-300'">
                            
                            <!-- Bagian Atas: Avatar, Nama, Role & Live Badge -->
                            <div>
                                <div class="flex items-start justify-between gap-2 mb-2.5">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <!-- Avatar Bulat -->
                                        <div class="w-8 h-8 rounded-xl font-black text-xs flex items-center justify-center shrink-0 shadow-2xs"
                                             :class="getRoleAvatarColor(u.role)">
                                            <span x-text="(u.name || '').substring(0, 1).toUpperCase()"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-xs font-bold text-slate-900 truncate flex items-center gap-1">
                                                <span x-text="u.name"></span>
                                                <span x-show="u.id == currentUserId" class="px-1 py-0.2 rounded text-[9px] font-bold bg-slate-200 text-slate-700 shrink-0">Anda</span>
                                            </div>
                                            <span class="inline-block mt-0.5 px-2 py-0.2 rounded text-[10px] font-bold border"
                                                  :class="getRoleBadgeColor(u.role)"
                                                  x-text="u.role_label || u.role">
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Live Status Pill -->
                                    <div class="shrink-0">
                                        <template x-if="u.is_online">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                                <span>Aktif</span>
                                            </span>
                                        </template>
                                        <template x-if="!u.is_online">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                <span>Nonaktif</span>
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <!-- Email & Keterangan Status -->
                                <div class="space-y-1 pt-1 border-t border-slate-100 text-[11px]">
                                    <div class="text-slate-600 font-mono truncate text-[10px]" x-text="u.email"></div>
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="text-slate-500">Status Kehadiran:</span>
                                        <span class="font-bold" :class="u.is_online ? 'text-emerald-700 font-extrabold' : 'text-slate-600'" x-text="u.last_seen_text"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian Bawah: Jejak Login & Tombol Aksi Cepat -->
                            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[10px]">
                                <div class="text-slate-500">
                                    <span class="block">Login: <span class="font-semibold text-slate-700" x-text="u.last_login"></span></span>
                                    <span class="block font-mono text-[9px] text-slate-400">IP: <span x-text="u.last_login_ip || '-'"></span></span>
                                </div>

                                <!-- Putuskan Sesi jika sedang online dan bukan akun sendiri -->
                                <template x-if="u.is_online && u.id != currentUserId">
                                    <form method="POST" :action="'{{ url('admin/users') }}/' + u.id + '/force-logout'" 
                                          onsubmit="return confirm('Putuskan sesi aktif staf ini sekarang?');">
                                        @csrf
                                        <button type="submit" 
                                                class="px-2 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-[10px] font-bold border border-rose-200 transition cursor-pointer">
                                            Putuskan
                                        </button>
                                    </form>
                                </template>
                            </div>

                        </div>
                    </template>
                </div>
            </div>

        </div>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Kolom Utama (8 Kolom): Aktivitas Transaksi Terbaru & Panduan Administrator -->
            <div class="lg:col-span-8 space-y-6">

                <!-- 1. Tabel Aktivitas Transaksi Terbaru Lintas Saluran -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Transaksi Terkini</h2>
                                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Transaksi terbaru lintas divisi iklan, online, dan TV</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 self-start sm:self-auto">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Realtime Terupdate
                        </span>
                    </div>

                    <div class="overflow-x-auto min-h-[200px]">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50/60 text-slate-600 text-xs font-semibold whitespace-nowrap">
                                    <th class="py-3.5 px-4 text-center w-12">No</th>
                                    <th class="py-3.5 px-4">Saluran</th>
                                    <th class="py-3.5 px-4">No. Faktur</th>
                                    <th class="py-3.5 px-4">Tanggal</th>
                                    <th class="py-3.5 px-4">Pemasang / Klien</th>
                                    <th class="py-3.5 px-4 text-right">Nilai Tagihan</th>
                                    <th class="py-3.5 px-4 text-center">Status</th>
                                    <th class="py-3.5 px-4 text-center w-12"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700 text-sm">
                                @forelse($recentStream as $key => $tx)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3.5 px-4 text-center text-slate-500 font-normal">
                                            {{ $key + 1 }}
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold border 
                                                {{ $tx->code === 'KRN' ? 'bg-sky-50 text-sky-700 border-sky-200' : ($tx->code === 'ONL' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-teal-50 text-teal-700 border-teal-200') }}">
                                                {{ $tx->channel }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="font-mono font-bold text-kp-blue-700 text-base">
                                                <a href="{{ $tx->route_view }}" class="hover:underline">
                                                    {{ $tx->faktur }}
                                                </a>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap font-mono text-sm">
                                            {{ \Carbon\Carbon::parse($tx->tanggal)->format('d/m/Y') }}
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="font-bold text-slate-900 text-sm">{{ $tx->customer }}</div>
                                        </td>
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                            <div class="flex justify-between items-center gap-2 max-w-[140px] ml-auto font-mono text-sm">
                                                <span class="text-slate-400 font-normal select-none">Rp</span>
                                                <span class="font-bold text-slate-900 tabular-nums">{{ number_format($tx->total, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            @if($tx->is_lunas)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold border bg-emerald-50 text-emerald-800 border-emerald-200">
                                                    Lunas
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold border bg-rose-50 text-rose-800 border-rose-200">
                                                    Piutang
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            <a href="{{ $tx->route_view }}" class="text-xs sm:text-sm text-kp-blue-600 hover:text-kp-blue-800 font-bold hover:underline">
                                                Detail &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-10 text-center text-slate-500 text-sm font-medium">
                                            Belum ada transaksi terkini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Info Administrator -->
                <div class="bg-gradient-to-r from-kp-blue-900 to-kp-blue-700 rounded-xl p-5 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-xs">
                    <div>
                        <h4 class="text-base font-bold">Akses Administrator</h4>
                        <p class="text-xs sm:text-sm text-kp-blue-100 mt-1 max-w-2xl leading-relaxed">
                            Anda dapat mengakses semua divisi: Iklan, Keuangan, Accounting, Sirkulasi, dan Kasir. Termasuk kelola data, validasi laporan, dan pengaturan sistem.
                        </p>
                    </div>
                    <a href="{{ route('kategori-media.index') }}" 
                       class="shrink-0 px-4 py-2 bg-white text-kp-blue-900 font-bold text-xs sm:text-sm rounded-xl shadow-xs hover:bg-kp-blue-50 transition text-center whitespace-nowrap">
                        Buka Master Data &rarr;
                    </a>
                </div>

            </div>

            <!-- Kolom Samping (4 Kolom): Widget Kalender Nasional & Agenda Momentum Pers -->
            <div class="lg:col-span-4 bg-white border border-slate-200 rounded-xl p-5 shadow-xs space-y-4">
                
                <!-- Header Kalender -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-base font-bold text-slate-900 leading-tight">Kalender Nasional</h2>
                            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Agenda edisi &amp; hari besar pers</p>
                        </div>
                    </div>
                    <button type="button" 
                            onclick="resetDashToday()" 
                            class="px-3 py-1.5 text-xs sm:text-sm font-bold text-kp-blue-700 bg-kp-blue-50 hover:bg-kp-blue-100 rounded-lg transition cursor-pointer">
                        Hari Ini
                    </button>
                </div>

                <!-- Navigasi Bulan & Tahun -->
                <div class="flex items-center justify-between px-1">
                    <button type="button" 
                            onclick="changeDashMonth(-1)" 
                            class="w-8 h-8 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer"
                            title="Bulan Sebelumnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-1.5">
                        <span id="dashCalMonthTitle" class="text-sm sm:text-base font-bold text-slate-900">
                            {{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM Y') }}
                        </span>
                        <span id="dashCalLoadingSpinner" class="hidden">
                            <svg class="animate-spin w-3.5 h-3.5 text-kp-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </span>
                    </div>

                    <button type="button" 
                            onclick="changeDashMonth(1)" 
                            class="w-8 h-8 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer"
                            title="Bulan Berikutnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- Header Hari (Min, Sen, Sel, Rab, Kam, Jum, Sab) -->
                <div class="grid grid-cols-7 gap-1 text-center">
                    <span class="text-xs sm:text-sm font-bold text-rose-500 py-1">Min</span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-600 py-1">Sen</span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-600 py-1">Sel</span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-600 py-1">Rab</span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-600 py-1">Kam</span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-600 py-1">Jum</span>
                    <span class="text-xs sm:text-sm font-semibold text-slate-600 py-1">Sab</span>
                </div>

                <!-- Grid Tanggal Kompak Standar Industri -->
                <div id="dashCalGrid" class="grid grid-cols-7 gap-1 text-center min-h-[190px]">
                    <!-- Dirender via JavaScript -->
                </div>

                <!-- Legenda Simpel Ringkas Persegi (Standar Industri Kabar Priangan) -->
                <div class="flex items-center justify-between text-xs text-slate-600 font-medium border-t border-slate-100 pt-3 px-1">
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap shrink-0" title="Hari Ini &amp; Tanggal Terpilih">
                        <span class="w-2.5 h-2.5 rounded-xs bg-kp-blue-600 inline-block shadow-2xs shrink-0"></span>
                        <span class="font-semibold text-slate-700">Hari Ini</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap shrink-0" title="Hari Libur Nasional &amp; Hari Minggu">
                        <span class="w-2.5 h-2.5 rounded-xs bg-rose-50 border border-rose-300 inline-block shrink-0"></span>
                        <span class="font-semibold text-rose-700">Libur</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap shrink-0" title="Hari Peringatan Nasional &amp; Agenda Pers">
                        <span class="w-2.5 h-2.5 rounded-xs bg-amber-50 border border-amber-300 inline-block shrink-0"></span>
                        <span class="font-semibold text-amber-700">Peringatan</span>
                    </span>
                </div>

                <!-- Panel Informasi Tanggal Terpilih / Event -->
                <div id="dashCalInfoPanel" class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2 transition-all">
                    <!-- Render dinamis: Tanggal & Keterangan Libur / Peringatan / Normal -->
                </div>

            </div>

        </div>

    </div>

    @push('scripts')
    <script>
        // ==============================================================
        // WIDGET KALENDER NASIONAL & AGENDA PERS MODERN ADMINISTRATOR
        // ==============================================================
        const dashMonthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        const dashDayNames = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];

        const dashNow = new Date();
        let dashCurrentYear = dashNow.getFullYear();
        let dashCurrentMonth = dashNow.getMonth(); // 0-indexed
        let dashSelectedDate = `${dashCurrentYear}-${String(dashCurrentMonth + 1).padStart(2, '0')}-${String(dashNow.getDate()).padStart(2, '0')}`;

        // Inisialisasi cache dari data server-side awal ($kalenderAwal)
        const dashCalCache = {
            [`${dashCurrentYear}-${dashCurrentMonth + 1}`]: @json($kalenderAwal ?? ['libur' => [], 'peringatan' => []])
        };

        document.addEventListener('DOMContentLoaded', function () {
            initDashCalendar();
        });

        function initDashCalendar() {
            renderDashCalendar();
            updateDashInfoPanel(dashSelectedDate);
        }

        function changeDashMonth(delta) {
            dashCurrentMonth += delta;
            if (dashCurrentMonth > 11) {
                dashCurrentMonth = 0;
                dashCurrentYear++;
            } else if (dashCurrentMonth < 0) {
                dashCurrentMonth = 11;
                dashCurrentYear--;
            }
            fetchDashKalender(dashCurrentYear, dashCurrentMonth + 1, () => {
                renderDashCalendar();
            });
        }

        function resetDashToday() {
            const today = new Date();
            dashCurrentYear = today.getFullYear();
            dashCurrentMonth = today.getMonth();
            dashSelectedDate = `${dashCurrentYear}-${String(dashCurrentMonth + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
            
            fetchDashKalender(dashCurrentYear, dashCurrentMonth + 1, () => {
                renderDashCalendar();
                updateDashInfoPanel(dashSelectedDate);
            });
        }

        function fetchDashKalender(year, month, callback) {
            const key = `${year}-${month}`;
            if (dashCalCache[key]) {
                if (callback) callback();
                return;
            }

            const spinner = document.getElementById('dashCalLoadingSpinner');
            const grid = document.getElementById('dashCalGrid');
            if (spinner) spinner.classList.remove('hidden');
            if (grid) grid.classList.add('opacity-40', 'pointer-events-none');

            fetch(`/api/kalender-nasional?year=${year}&month=${month}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => {
                if (!res.ok) throw new Error('Network error');
                return res.json();
            })
            .then(data => {
                dashCalCache[key] = {
                    libur: data.libur || {},
                    peringatan: data.peringatan || {}
                };
            })
            .catch(err => {
                console.warn('Gagal memuat API Kalender Nasional:', err);
                dashCalCache[key] = { libur: {}, peringatan: {} };
            })
            .finally(() => {
                if (spinner) spinner.classList.add('hidden');
                if (grid) grid.classList.remove('opacity-40', 'pointer-events-none');
                if (callback) callback();
            });
        }

        function extractDashEventText(item) {
            if (!item) return null;
            if (typeof item === 'string') return item;
            if (typeof item === 'object') {
                return item.nama || item.keterangan || item.summary || '';
            }
            return null;
        }

        function renderDashCalendar() {
            const titleEl = document.getElementById('dashCalMonthTitle');
            if (titleEl) {
                titleEl.textContent = `${dashMonthNames[dashCurrentMonth]} ${dashCurrentYear}`;
            }

            const grid = document.getElementById('dashCalGrid');
            if (!grid) return;
            grid.innerHTML = '';

            const key = `${dashCurrentYear}-${dashCurrentMonth + 1}`;
            const monthData = dashCalCache[key] || { libur: {}, peringatan: {} };
            const liburMap = monthData.libur || {};
            const peringatanMap = monthData.peringatan || {};

            const firstDayOfWeek = new Date(dashCurrentYear, dashCurrentMonth, 1).getDay(); // 0 = Minggu
            const daysInMonth = new Date(dashCurrentYear, dashCurrentMonth + 1, 0).getDate();

            // Sel kosong sebelum tanggal 1
            for (let i = 0; i < firstDayOfWeek; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'h-8 pointer-events-none';
                grid.appendChild(emptyCell);
            }

            const today = new Date();
            const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${dashCurrentYear}-${String(dashCurrentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const dateObj = new Date(dashCurrentYear, dashCurrentMonth, day);
                const dayOfWeek = dateObj.getDay();

                const isToday = (dateStr === todayStr);
                const isSelected = (dateStr === dashSelectedDate);
                const isSunday = (dayOfWeek === 0);
                const liburDesc = extractDashEventText(liburMap[dateStr]);
                const peringatanDesc = extractDashEventText(peringatanMap[dateStr]);

                const btn = document.createElement('button');
                btn.type = 'button';
                let btnClass = 'h-9 rounded-lg flex items-center justify-center relative text-xs sm:text-sm transition cursor-pointer select-none border ';

                if (isSelected) {
                    btnClass += 'bg-kp-blue-600 text-white font-bold border-kp-blue-700 shadow-2xs ';
                } else if (isToday) {
                    btnClass += 'bg-kp-blue-50 text-kp-blue-700 font-bold border border-kp-blue-300 hover:bg-kp-blue-100 ';
                } else if (liburDesc) {
                    btnClass += 'text-rose-700 font-bold bg-rose-50 border-rose-200 hover:bg-rose-100 ';
                } else if (peringatanDesc) {
                    btnClass += 'text-amber-800 font-bold bg-amber-50 border-amber-200 hover:bg-amber-100 ';
                } else if (isSunday) {
                    btnClass += 'text-rose-600 font-semibold bg-rose-50/70 border-rose-200/80 hover:bg-rose-100 ';
                } else {
                    btnClass += 'text-slate-700 font-medium border-transparent hover:bg-slate-100 ';
                }
                btn.className = btnClass;

                // Sel Kalender: Angka tanggal kotak bersih tanpa titik lingkaran
                btn.innerHTML = `
                    <span class="leading-none text-xs sm:text-sm font-semibold ${isSunday && !isSelected && !liburDesc ? 'text-rose-600' : ''}">${day}</span>
                `;

                if (liburDesc || peringatanDesc) {
                    btn.title = [liburDesc ? `Libur: ${liburDesc}` : null, peringatanDesc ? `Peringatan: ${peringatanDesc}` : null].filter(Boolean).join(' • ');
                }

                btn.onclick = () => {
                    dashSelectedDate = dateStr;
                    renderDashCalendar();
                    updateDashInfoPanel(dateStr);
                };

                grid.appendChild(btn);
            }
        }

        function updateDashInfoPanel(dateStr) {
            const panel = document.getElementById('dashCalInfoPanel');
            if (!panel) return;

            const parts = dateStr.split('-');
            if (parts.length !== 3) return;
            const y = parseInt(parts[0]);
            const m = parseInt(parts[1]) - 1;
            const d = parseInt(parts[2]);
            const dateObj = new Date(y, m, d);

            const dayName = dashDayNames[dateObj.getDay()] || '';
            const formatted = `${dayName}, ${d} ${dashMonthNames[m]} ${y}`;

            const key = `${y}-${m + 1}`;
            const monthData = dashCalCache[key] || { libur: {}, peringatan: {} };
            const liburDesc = extractDashEventText(monthData.libur ? monthData.libur[dateStr] : null);
            const peringatanDesc = extractDashEventText(monthData.peringatan ? monthData.peringatan[dateStr] : null);
            const isSunday = (dateObj.getDay() === 0);

            let html = `
                <div class="flex items-center justify-between">
                    <span class="text-sm sm:text-base font-bold text-slate-900">${formatted}</span>
                    <span class="text-xs font-mono text-slate-500">${dateStr}</span>
                </div>
            `;

            if (liburDesc) {
                html += `
                    <div class="p-2.5 rounded-lg bg-rose-50 border border-rose-200/80 text-rose-800 text-xs sm:text-sm flex items-start gap-2 mt-1">
                        <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <strong class="font-bold">Libur Nasional:</strong> ${liburDesc}
                        </div>
                    </div>
                `;
            }

            if (peringatanDesc) {
                html += `
                    <div class="p-2.5 rounded-lg bg-amber-50 border border-amber-200/80 text-amber-900 text-xs sm:text-sm flex items-start gap-2 mt-1">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <div>
                            <strong class="font-bold">Momentum / Potensi Iklan:</strong> ${peringatanDesc}
                        </div>
                    </div>
                `;
            }

            if (!liburDesc && !peringatanDesc) {
                html += `
                    <div class="text-xs sm:text-sm text-slate-500 flex items-center justify-between pt-0.5">
                        <span>${isSunday ? 'Hari Minggu / Edisi Khusus' : 'Hari Kerja / Edisi Terbit Harian'}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-600">Terbit Normal</span>
                    </div>
                `;
            }

            panel.innerHTML = html;
        }

        // ==============================================================
        // CONTROLLER LIVE STATUS KEHADIRAN & SESI ADMIN / PENGGUNA
        // ==============================================================
        function adminLiveStatus(config) {
            return {
                filter: 'all',
                currentUserId: config.currentUserId || 0,
                usersMap: config.initialUsers || {},
                onlineCount: parseInt(config.initialOnlineCount) || 0,
                offlineCount: parseInt(config.initialOfflineCount) || 0,
                totalCount: parseInt(config.initialTotalCount) || 0,
                adminOnlineCount: parseInt(config.initialAdminOnlineCount) || 0,
                lastSynced: '',
                pollTimer: null,

                init() {
                    this.fetchStatus();
                    // Live polling status setiap 5 detik
                    this.pollTimer = setInterval(() => {
                        this.fetchStatus();
                    }, 5000);
                },

                fetchStatus() {
                    if (!config.pollUrl) return;
                    fetch(config.pollUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.users) {
                            this.usersMap = data.users;
                            if (typeof data.online_count !== 'undefined') this.onlineCount = data.online_count;
                            if (typeof data.offline_count !== 'undefined') this.offlineCount = data.offline_count;
                            if (typeof data.total_count !== 'undefined') this.totalCount = data.total_count;
                            if (typeof data.admin_online_count !== 'undefined') this.adminOnlineCount = data.admin_online_count;
                            if (data.timestamp) this.lastSynced = data.timestamp;
                        }
                    })
                    .catch(() => {});
                },

                get userList() {
                    return Object.values(this.usersMap);
                },

                get filteredUsers() {
                    const list = this.userList;
                    if (this.filter === 'online') {
                        return list.filter(u => u.is_online);
                    }
                    if (this.filter === 'offline') {
                        return list.filter(u => !u.is_online);
                    }
                    if (this.filter === 'admin') {
                        return list.filter(u => ['administrator', 'admin'].includes((u.role || '').toLowerCase()));
                    }
                    return list;
                },

                getRoleAvatarColor(role) {
                    const r = (role || '').toLowerCase();
                    if (['administrator', 'admin'].includes(r)) return 'bg-rose-600 text-white';
                    if (['keuangan', 'admin_keuangan', 'petugas_inkaso', 'kepala_divisi'].includes(r)) return 'bg-emerald-600 text-white';
                    if (['kasir'].includes(r)) return 'bg-teal-600 text-white';
                    if (['sirkulasi'].includes(r)) return 'bg-amber-600 text-white';
                    if (['iklan'].includes(r)) return 'bg-sky-600 text-white';
                    if (['accounting'].includes(r)) return 'bg-purple-600 text-white';
                    if (['billing'].includes(r)) return 'bg-indigo-600 text-white';
                    return 'bg-slate-700 text-white';
                },

                getRoleBadgeColor(role) {
                    const r = (role || '').toLowerCase();
                    if (['administrator', 'admin'].includes(r)) return 'bg-rose-50 text-rose-800 border-rose-300';
                    if (['keuangan', 'admin_keuangan', 'petugas_inkaso', 'kepala_divisi'].includes(r)) return 'bg-emerald-50 text-emerald-800 border-emerald-300';
                    if (['kasir'].includes(r)) return 'bg-teal-50 text-teal-800 border-teal-300';
                    if (['sirkulasi'].includes(r)) return 'bg-amber-50 text-amber-900 border-amber-300';
                    if (['iklan'].includes(r)) return 'bg-sky-50 text-sky-800 border-sky-300';
                    if (['accounting'].includes(r)) return 'bg-purple-50 text-purple-800 border-purple-300';
                    if (['billing'].includes(r)) return 'bg-indigo-50 text-indigo-800 border-indigo-300';
                    return 'bg-slate-100 text-slate-700 border-slate-300';
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
