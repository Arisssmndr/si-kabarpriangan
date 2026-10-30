<x-app-layout>
    <div class="space-y-6">

        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Pusat Kendali Administrator
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pantau seluruh divisi operasional Kabar Priangan.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 text-xs text-slate-600 bg-slate-50 px-3.5 py-2 rounded-lg border border-slate-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
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
                        <h3 class="text-sm font-semibold text-slate-900 group-hover:text-indigo-700 transition">Laporan</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Ringkasan kinerja seluruh divisi</p>
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
                        <h3 class="text-sm font-semibold text-slate-900 group-hover:text-kp-blue-700 transition">Pengaturan</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Data perusahaan, rekening, dan pajak</p>
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
                        <h3 class="text-sm font-semibold text-slate-900 group-hover:text-emerald-700 transition">Status Sistem</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Kondisi server, database, dan log terkini</p>
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
                        <span class="text-[10px] font-semibold text-slate-600 bg-slate-50 border border-slate-200 px-1.5 py-0.5 rounded">
                            Aktif
                        </span>
                    </div>
                    <h3 class="text-xs font-bold text-slate-800 group-hover:text-kp-blue-700 transition">Divisi Iklan</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Koran, Online, Priangan TV</p>
                    <div class="mt-3 text-sm font-extrabold text-slate-900">
                        Rp {{ number_format($totalOmsetIklan, 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-kp-blue-600 font-semibold">
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
                        <span class="text-[10px] font-semibold text-slate-600 bg-slate-50 border border-slate-200 px-1.5 py-0.5 rounded">
                            Aktif
                        </span>
                    </div>
                    <h3 class="text-xs font-bold text-slate-800 group-hover:text-emerald-700 transition">Divisi Keuangan</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Arus Kas & Piutang</p>
                    <div class="mt-3 text-sm font-extrabold text-slate-900">
                        Rp {{ number_format($arusKasMasuk, 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-emerald-600 font-semibold">
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
                        <span class="text-[10px] font-semibold text-slate-600 bg-slate-50 border border-slate-200 px-1.5 py-0.5 rounded">
                            Aktif
                        </span>
                    </div>
                    <h3 class="text-xs font-bold text-slate-800 group-hover:text-purple-700 transition">Divisi Accounting</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Laba/Rugi & Buku Besar</p>
                    <div class="mt-3 text-sm font-extrabold text-slate-900">
                        Rp {{ number_format($labaKotorBerjalan, 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-purple-600 font-semibold">
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
                        <span class="text-[10px] font-semibold text-slate-600 bg-slate-50 border border-slate-200 px-1.5 py-0.5 rounded">
                            Aktif
                        </span>
                    </div>
                    <h3 class="text-xs font-bold text-slate-800 group-hover:text-amber-700 transition">Divisi Sirkulasi</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Oplah & Distribusi Agen</p>
                    <div class="mt-3 text-sm font-extrabold text-slate-900">
                        {{ number_format($oplahHariIni, 0, ',', '.') }} Eks
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-amber-600 font-semibold">
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
                        <span class="text-[10px] font-semibold text-slate-600 bg-slate-50 border border-slate-200 px-1.5 py-0.5 rounded">
                            Aktif
                        </span>
                    </div>
                    <h3 class="text-xs font-bold text-slate-800 group-hover:text-teal-700 transition">Divisi Kasir</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Penerimaan Kas Harian</p>
                    <div class="mt-3 text-sm font-extrabold text-slate-900">
                        Rp {{ number_format($penerimaanHariIni, 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-teal-600 font-semibold">
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
                        <h2 class="text-xs font-bold text-slate-800 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-kp-blue-600"></span>
                            Ringkasan Iklan
                        </h2>
                        <a href="{{ route('iklan.dashboard') }}" class="text-[11px] text-kp-blue-600 font-semibold hover:underline">Detail &rarr;</a>
                    </div>
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Total Omset Iklan</span>
                            <span class="font-extrabold text-slate-900">Rp {{ number_format($totalOmsetIklan, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Total Pembayaran Diterima</span>
                            <span class="font-bold text-emerald-600">Rp {{ number_format($totalBayarIklan, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Total Sisa Piutang</span>
                            <span class="font-bold text-rose-600">Rp {{ number_format($totalPiutangIklan, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Total Transaksi Iklan</span>
                            <span class="font-semibold text-slate-800">{{ $totalTxIklan }} transaksi</span>
                        </div>
                    </div>
                </div>
                <div class="mt-5 pt-3 border-t border-slate-100 bg-slate-50 -mx-5 -mb-5 p-4 rounded-b-xl flex items-center justify-between text-[11px]">
                    <span class="text-slate-500">Status Modul Iklan:</span>
                    <span class="text-emerald-700 font-bold bg-emerald-100/70 px-2 py-0.5 rounded">Operasional & Aktif</span>
                </div>
            </div>

            <!-- Panel 2: Keuangan & Pembukuan -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-xs font-bold text-slate-800 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            Ringkasan Keuangan
                        </h2>
                        <a href="{{ route('keuangan.dashboard') }}" class="text-[11px] text-emerald-600 font-semibold hover:underline">Detail &rarr;</a>
                    </div>
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Total Arus Kas Masuk</span>
                            <span class="font-extrabold text-slate-900">Rp {{ number_format($arusKasMasuk, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Estimasi Beban Operasional</span>
                            <span class="font-bold text-slate-700">Rp {{ number_format($estimasiPengeluaranBulanIni, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Estimasi Laba Kotor Berjalan</span>
                            <span class="font-bold text-emerald-600">Rp {{ number_format($labaKotorBerjalan, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Keseimbangan Buku Besar</span>
                            <span class="font-semibold text-slate-800">{{ $statusBukuBesar }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-5 pt-3 border-t border-slate-100 bg-slate-50 -mx-5 -mb-5 p-4 rounded-b-xl flex items-center justify-between text-[11px]">
                    <span class="text-slate-500">Jurnal Pending:</span>
                    <span class="text-amber-700 font-bold bg-amber-100/70 px-2 py-0.5 rounded">{{ $totalJurnalPending }} Perlu Verifikasi</span>
                </div>
            </div>

            <!-- Panel 3: Sirkulasi & Kasir -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-xs font-bold text-slate-800 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                            Sirkulasi & Kasir
                        </h2>
                        <a href="{{ route('sirkulasi.dashboard') }}" class="text-[11px] text-amber-600 font-semibold hover:underline">Detail &rarr;</a>
                    </div>
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Oplah Cetak Harian</span>
                            <span class="font-extrabold text-slate-900">{{ number_format($oplahHariIni, 0, ',', '.') }} Eks</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Efektivitas Distribusi Agen</span>
                            <span class="font-bold text-emerald-600">{{ $persentaseTerdistribusi }}%</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Penerimaan Kasir Hari Ini</span>
                            <span class="font-bold text-teal-600">Rp {{ number_format($penerimaanHariIni, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Transaksi Kasir Hari Ini</span>
                            <span class="font-semibold text-slate-800">{{ $txHariIni }} struk</span>
                        </div>
                    </div>
                </div>
                <div class="mt-5 pt-3 border-t border-slate-100 bg-slate-50 -mx-5 -mb-5 p-4 rounded-b-xl flex items-center justify-between text-[11px]">
                    <span class="text-slate-500">Jaringan Distribusi:</span>
                    <span class="text-slate-700 font-bold bg-slate-200/70 px-2 py-0.5 rounded">{{ $totalAgenAktif }} Agen Aktif</span>
                </div>
            </div>

        </div>

        <!-- Section Operasional Terpadu & Kalender Nasional (Grid 12: 8 Kolom Utama + 4 Kolom Kalender Samping) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Kolom Utama (8 Kolom): Aktivitas Transaksi Terbaru & Panduan Administrator -->
            <div class="lg:col-span-8 space-y-6">

                <!-- 1. Tabel Aktivitas Transaksi Terbaru Lintas Saluran -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-kp-blue-50 text-kp-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Transaksi Terkini</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Transaksi terbaru lintas divisi iklan, online, dan TV</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60 self-start sm:self-auto">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Realtime Terupdate
                        </span>
                    </div>

                    <div class="overflow-x-auto min-h-[200px]">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50/60 text-slate-600 font-semibold whitespace-nowrap">
                                    <th class="py-3 px-4 text-center w-10">No</th>
                                    <th class="py-3 px-4">Saluran</th>
                                    <th class="py-3 px-4">No. Faktur</th>
                                    <th class="py-3 px-4">Tanggal</th>
                                    <th class="py-3 px-4">Pemasang / Klien</th>
                                    <th class="py-3 px-4 text-right">Nilai Tagihan</th>
                                    <th class="py-3 px-4 text-center">Status</th>
                                    <th class="py-3 px-4 text-center w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($recentStream as $key => $tx)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-4 text-center text-slate-500 font-normal">
                                            {{ $key + 1 }}
                                        </td>
                                        <td class="py-3 px-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold 
                                                {{ $tx->code === 'KRN' ? 'bg-sky-50 text-sky-700 border border-sky-200' : ($tx->code === 'ONL' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-teal-50 text-teal-700 border border-teal-200') }}">
                                                {{ $tx->channel }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 whitespace-nowrap font-mono font-bold text-kp-blue-700">
                                            {{ $tx->faktur }}
                                        </td>
                                        <td class="py-3 px-4 text-slate-600 whitespace-nowrap font-mono">
                                            {{ \Carbon\Carbon::parse($tx->tanggal)->format('d/m/Y') }}
                                        </td>
                                        <td class="py-3 px-4 text-slate-900 font-medium whitespace-nowrap">
                                            {{ $tx->customer }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-extrabold text-slate-900 font-tabular whitespace-nowrap">
                                            Rp {{ number_format($tx->total, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            @if($tx->is_lunas)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Lunas
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    Belum Lunas
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <a href="{{ $tx->route_view }}" class="text-xs text-kp-blue-600 hover:text-kp-blue-800 font-bold hover:underline">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-8 text-center text-slate-400 italic">
                                            Belum ada transaksi terkini
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
                        <h4 class="text-sm font-bold">Akses Administrator</h4>
                        <p class="text-xs text-kp-blue-100 mt-1 max-w-2xl leading-relaxed">
                            Anda dapat mengakses semua divisi: Iklan, Keuangan, Accounting, Sirkulasi, dan Kasir. Termasuk kelola data, validasi laporan, dan pengaturan sistem.
                        </p>
                    </div>
                    <a href="{{ route('kategori-media.index') }}" 
                       class="shrink-0 px-4 py-2 bg-white text-kp-blue-900 font-bold text-xs rounded-lg shadow-sm hover:bg-kp-blue-50 transition text-center">
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
                            <h2 class="text-sm font-bold text-slate-900 leading-tight">Kalender Nasional</h2>
                            <p class="text-[11px] text-slate-500">Agenda edisi & hari besar pers</p>
                        </div>
                    </div>
                    <button type="button" 
                            onclick="resetDashToday()" 
                            class="px-2.5 py-1 text-[11px] font-semibold text-kp-blue-700 bg-kp-blue-50 hover:bg-kp-blue-100 rounded-md transition cursor-pointer">
                        Hari Ini
                    </button>
                </div>

                <!-- Navigasi Bulan & Tahun -->
                <div class="flex items-center justify-between px-1">
                    <button type="button" 
                            onclick="changeDashMonth(-1)" 
                            class="w-7 h-7 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer"
                            title="Bulan Sebelumnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-1.5">
                        <span id="dashCalMonthTitle" class="text-xs font-bold text-slate-900">
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
                            class="w-7 h-7 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer"
                            title="Bulan Berikutnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- Header Hari (Min, Sen, Sel, Rab, Kam, Jum, Sab) -->
                <div class="grid grid-cols-7 gap-1 text-center">
                    <span class="text-[10px] font-bold text-rose-500 py-1">Min</span>
                    <span class="text-[10px] font-semibold text-slate-500 py-1">Sen</span>
                    <span class="text-[10px] font-semibold text-slate-500 py-1">Sel</span>
                    <span class="text-[10px] font-semibold text-slate-500 py-1">Rab</span>
                    <span class="text-[10px] font-semibold text-slate-500 py-1">Kam</span>
                    <span class="text-[10px] font-semibold text-slate-500 py-1">Jum</span>
                    <span class="text-[10px] font-semibold text-slate-500 py-1">Sab</span>
                </div>

                <!-- Grid Tanggal Kompak Standar Industri -->
                <div id="dashCalGrid" class="grid grid-cols-7 gap-1 text-center min-h-[190px]">
                    <!-- Dirender via JavaScript -->
                </div>

                <!-- Legenda Simpel Ringkas Persegi (Standar Industri Kabar Priangan) -->
                <div class="flex items-center justify-between text-[10px] text-slate-500 border-t border-slate-100 pt-2 px-0.5">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-kp-blue-600 inline-block shadow-2xs"></span> Terpilih / Hari Ini</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-rose-50 border border-rose-300 inline-block"></span> Libur / Minggu</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-xs bg-amber-50 border border-amber-300 inline-block"></span> Peringatan</span>
                </div>

                <!-- Panel Informasi Tanggal Terpilih / Event -->
                <div id="dashCalInfoPanel" class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1.5 transition-all">
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
                let btnClass = 'h-8 rounded-md flex items-center justify-center relative text-xs transition cursor-pointer select-none border ';

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
                    <span class="leading-none text-xs font-semibold ${isSunday && !isSelected && !liburDesc ? 'text-rose-600' : ''}">${day}</span>
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
                    <span class="text-xs font-bold text-slate-900">${formatted}</span>
                    <span class="text-[10px] font-mono text-slate-400">${dateStr}</span>
                </div>
            `;

            if (liburDesc) {
                html += `
                    <div class="p-2 rounded-md bg-rose-50 border border-rose-200/80 text-rose-800 text-[11px] flex items-start gap-1.5 mt-1">
                        <svg class="w-3.5 h-3.5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="p-2 rounded-md bg-amber-50 border border-amber-200/80 text-amber-900 text-[11px] flex items-start gap-1.5 mt-1">
                        <svg class="w-3.5 h-3.5 text-amber-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
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
                    <div class="text-[11px] text-slate-500 flex items-center justify-between pt-0.5">
                        <span>${isSunday ? 'Hari Minggu / Edisi Khusus' : 'Hari Kerja / Edisi Terbit Harian'}</span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">Terbit Normal</span>
                    </div>
                `;
            }

            panel.innerHTML = html;
        }
    </script>
    @endpush
</x-app-layout>
