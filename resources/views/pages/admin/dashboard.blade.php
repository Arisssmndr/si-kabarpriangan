<x-app-layout>
    <div class="space-y-6">

        <!-- Header Dashboard Eksekutif Administrator -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Pusat Kendali Eksekutif
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-rose-50 text-rose-700 border border-rose-200">
                            Superadmin
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Monitoring terpadu lintas 5 divisi operasional Harian Umum Kabar Priangan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 text-xs text-slate-600 bg-slate-50 px-3.5 py-2 rounded-lg border border-slate-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Portal Sistem Aktif &bull; {{ $totalUsers }} Pengguna Terdaftar</span>
            </div>
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
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded">
                            Lengkap
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
                        <span class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded">
                            Dashboard
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
                        <span class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded">
                            Dashboard
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
                        <span class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded">
                            Dashboard
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
                        <span class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded">
                            Dashboard
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
                        <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-kp-blue-600"></span>
                            Ikhtisar Periklanan Multi-Kanal
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
                        <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            Posisi Keuangan & Accounting
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
                        <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                            Distribusi Koran & Meja Kasir
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

        <!-- Petunjuk Hak Akses Administrator -->
        <div class="bg-gradient-to-r from-kp-blue-900 to-kp-blue-700 rounded-xl p-5 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h4 class="text-sm font-bold tracking-wide">Hak Akses Superadmin Administrator</h4>
                <p class="text-xs text-kp-blue-100 mt-1 max-w-2xl leading-relaxed">
                    Sebagai Administrator, Anda memiliki akses penuh ke seluruh menu dan modul dari ke-5 divisi (Iklan, Keuangan, Accounting, Sirkulasi, dan Kasir). Anda dapat mengelola data master, memeriksa transaksi, memvalidasi laporan, dan membuka dashboard setiap divisi secara leluasa.
                </p>
            </div>
            <a href="{{ route('kategori-media.index') }}" 
               class="shrink-0 px-4 py-2 bg-white text-kp-blue-900 font-bold text-xs rounded-lg shadow-sm hover:bg-kp-blue-50 transition text-center">
                Buka Master Data &rarr;
            </a>
        </div>

    </div>
</x-app-layout>
