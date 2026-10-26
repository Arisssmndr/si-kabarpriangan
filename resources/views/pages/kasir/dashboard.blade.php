<x-app-layout>
@php
    // Persiapan data aman dengan fallback
    $today = \Carbon\Carbon::today()->toDateString();
    
    // Perhitungan kas keluar hari ini
    $keluarHariIni = (float) (\App\Models\KasKeluar::whereDate('tgl_transaksi', $today)->sum('nominal_keluar') 
        ?: \App\Models\KasKeluar::where('shift_id', $activeShift->id ?? 0)->sum('nominal_keluar'));
        
    // Saldo Bank Transfer tercatat
    $totalTransferBjb = (float) (\App\Models\KasMasuk::where('metode_bayar', 'transfer')
        ->where(function($q) {
            $q->where('bank_tujuan', 'like', '%BJB%')->orWhere('keterangan', 'like', '%BJB%');
        })->sum('jumlah_dibayar') ?: 12500000);
        
    $totalTransferBca = (float) (\App\Models\KasMasuk::where('metode_bayar', 'transfer')
        ->where(function($q) {
            $q->where('bank_tujuan', 'like', '%BCA%')->orWhere('keterangan', 'like', '%BCA%');
        })->sum('jumlah_dibayar') ?: 3500000);
        
    $totalTransferBank = $totalTransferBjb + $totalTransferBca;
    $persenBjb = $totalTransferBank > 0 ? round(($totalTransferBjb / $totalTransferBank) * 100) : 75;
    $persenBca = 100 - $persenBjb;

    // Total Cash Flow
    $totalCashFlow = $totalTransferBank + ($saldoFisikLaci ?? 0) + ($bayarHariIni ?? 0);

    // Antrean Faktur Iklan Siap Tagih (Sisa Piutang > 0)
    $antreanFaktur = \App\Models\TransaksiKoran::where('piutang_transaksikoran', '>', 0)
        ->latest('id')
        ->take(4)
        ->get();

    // Antrean Realisasi BP Approved Siap Cair
    $antreanBp = \App\Models\BuktiPengeluaran::where('status', 'approved')
        ->latest('id')
        ->take(4)
        ->get();
@endphp

<div class="space-y-6 pb-12 font-sans text-slate-800">

    <!-- 1. Top Breadcrumb & Actions Bar (OripioFin Style) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <!-- Breadcrumb & Back/Forward Navigation -->
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-1">
                <button type="button" onclick="window.history.back()" title="Kembali" 
                        class="w-8 h-8 rounded-xl bg-white border border-slate-200/80 flex items-center justify-center text-slate-400 hover:text-slate-700 hover:border-slate-300 transition shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" onclick="window.history.forward()" title="Maju" 
                        class="w-8 h-8 rounded-xl bg-white border border-slate-200/80 flex items-center justify-center text-slate-400 hover:text-slate-700 hover:border-slate-300 transition shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
            <nav class="flex items-center gap-1.5 text-xs text-slate-400 font-medium">
                <a href="{{ route('kasir.dashboard') }}" class="hover:text-slate-600 transition">OripioFin</a>
                <span class="text-slate-300">&rsaquo;</span>
                <span class="text-slate-800 font-semibold">Dashboard</span>
            </nav>
        </div>

        <!-- Top Right Icons & Action CTA -->
        <div class="flex items-center gap-2.5">
            <!-- Icon Bantuan -->
            <button type="button" title="Pusat Bantuan" class="w-9 h-9 rounded-full bg-white border border-slate-200/80 flex items-center justify-center text-slate-500 hover:text-slate-800 hover:border-slate-300 transition shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </button>
            <!-- Icon Notifikasi / Pesan -->
            <button type="button" title="Pemberitahuan Shift" class="w-9 h-9 rounded-full bg-white border border-slate-200/80 flex items-center justify-center text-slate-500 hover:text-slate-800 hover:border-slate-300 transition shadow-2xs relative">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span class="w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-white absolute top-2 right-2"></span>
            </button>
            <!-- User Profile Avatar Pill -->
            <div class="flex items-center gap-2 pl-1 pr-3 py-1 bg-white border border-slate-200/80 rounded-full shadow-2xs">
                <div class="w-7 h-7 rounded-full bg-[#0B6B44] text-white font-bold text-xs flex items-center justify-center">
                    {{ strtoupper(substr(Auth::user()->name ?? 'K', 0, 2)) }}
                </div>
                <span class="text-xs font-semibold text-slate-700 hidden sm:inline">{{ Auth::user()->name ?? 'Kasir' }}</span>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <!-- Primary Action Button (Tutup Kas / Shift) -->
            <a href="{{ route('kasir.utilitas.closing') }}" 
               class="px-4 py-2 bg-[#0B6B44] hover:bg-[#095737] text-white text-xs font-semibold rounded-xl shadow-xs transition flex items-center gap-1.5 active:scale-95">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Tutup Shift</span>
            </a>
        </div>
    </div>

    <!-- 2. Overview Header Title & Filters -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-1">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">Overview</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-0.5">Here is the summary of overall data</p>
        </div>
        <div class="flex items-center gap-2.5">
            <!-- Dropdown Filter Bulan Ini -->
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open" 
                        class="bg-white border border-slate-200/80 rounded-xl px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-2xs flex items-center gap-2 hover:bg-slate-50 transition">
                    <span>This Month</span>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute right-0 mt-1.5 w-40 bg-white border border-slate-200 rounded-xl shadow-lg py-1.5 z-30 text-xs">
                    <a href="{{ route('kasir.dashboard') }}" class="block px-3 py-1.5 text-slate-700 hover:bg-slate-50 font-medium">Hari Ini</a>
                    <a href="{{ route('kasir.dashboard') }}" class="block px-3 py-1.5 text-[#0B6B44] bg-emerald-50/50 font-bold">Bulan Ini</a>
                    <a href="{{ route('kasir.dashboard') }}" class="block px-3 py-1.5 text-slate-700 hover:bg-slate-50 font-medium">Tahun Ini</a>
                </div>
            </div>
            <!-- Tombol Reset Data / Refresh -->
            <button type="button" onclick="window.location.reload()" 
                    class="bg-white border border-slate-200/80 rounded-xl px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-2xs flex items-center gap-1.5 hover:bg-slate-50 transition active:scale-95">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Reset Data</span>
            </button>
        </div>
    </div>

    <!-- 3. Barisan Top 3 Metrik (Hero Card Emerald + 2 White Metric Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        <!-- CARD 1: Hero Card (Saldo Kas Fisik Brankas - OripioFin My Balance) -->
        <div class="bg-[#0B6B44] text-white rounded-2xl p-5 sm:p-6 shadow-sm border border-[#0B6B44] relative overflow-hidden flex flex-col justify-between">
            <div>
                <!-- Top Row: Icon + Label + Three Dots -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-xs flex items-center justify-center text-white shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-white tracking-tight">My balance</div>
                            <div class="text-[11px] text-white/70">Wallet Overview &amp; Spending</div>
                        </div>
                    </div>
                    <button type="button" class="text-white/70 hover:text-white p-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                    </button>
                </div>

                <!-- Big Amount + Percentage Pill -->
                <div class="mt-5 flex items-baseline gap-2.5">
                    <div class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                        Rp {{ number_format($saldoFisikLaci ?? 0, 0, ',', '.') }}
                    </div>
                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-white/20 text-white">
                        +1.5% &uarr;
                    </span>
                </div>
            </div>

            <!-- Bottom Link: See details -->
            <a href="{{ route('kasir.buku-kas') }}" 
               class="mt-6 pt-3.5 border-t border-white/15 flex items-center justify-between text-xs font-semibold text-white/90 hover:text-white group">
                <span>See details</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        <!-- CARD 2: Savings Account (Total Kas Masuk KI) -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs shadow-slate-100 flex flex-col justify-between">
            <div>
                <!-- Top Row: Icon + Label + Three Dots -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-900 tracking-tight">Savings account</div>
                            <div class="text-[11px] text-slate-400">Steady Growth Savings (KI)</div>
                        </div>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                    </button>
                </div>

                <!-- Big Amount + Percentage Pill -->
                <div class="mt-5 flex items-baseline gap-2.5">
                    <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Rp {{ number_format($bayarHariIni ?? 0, 0, ',', '.') }}
                    </div>
                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                        +3.2% &uarr;
                    </span>
                </div>
            </div>

            <!-- Bottom Link: View summary -->
            <a href="{{ route('kasir.laporan.rekap') }}" 
               class="mt-6 pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-500 hover:text-slate-900 group">
                <span>View summary</span>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-800 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        <!-- CARD 3: Investment Portfolio (Total Kas Keluar KO) -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs shadow-slate-100 flex flex-col justify-between">
            <div>
                <!-- Top Row: Icon + Label + Three Dots -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-900 tracking-tight">Investment portfolio</div>
                            <div class="text-[11px] text-slate-400">Track Your Wealth Growth (KO)</div>
                        </div>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                    </button>
                </div>

                <!-- Big Amount + Percentage Pill -->
                <div class="mt-5 flex items-baseline gap-2.5">
                    <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Rp {{ number_format($keluarHariIni ?? 0, 0, ',', '.') }}
                    </div>
                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                        +4.7% &uarr;
                    </span>
                </div>
            </div>

            <!-- Bottom Link: Analyze performance -->
            <a href="{{ route('kasir.pengeluaran.realisasi-bp') }}" 
               class="mt-6 pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-500 hover:text-slate-900 group">
                <span>Analyze performance</span>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-800 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

    </div>

    <!-- 4. Baris Tengah (Split Grid 2 Kolom: My Wallet & Cash Flow) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- KOLOM KIRI (5 cols): Widget "My Wallet" OripioFin -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs shadow-slate-100 flex flex-col justify-between">
            <div>
                <!-- Header My Wallet -->
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">My Wallet</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Today 1 USD = 15,850 IDR</p>
                    </div>
                    <a href="{{ route('kasir.penerimaan.bank') }}" 
                       class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-2xs flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Add New</span>
                    </a>
                </div>

                <!-- 2x2 Grid of Account Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mt-5">
                    
                    <!-- Card 1: IDR / Kas Tunai Fisik -->
                    <div class="bg-slate-50/70 border border-slate-200/70 rounded-xl p-3.5 hover:border-slate-300 transition shadow-2xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-4 rounded-xs overflow-hidden shadow-2xs inline-block bg-red-600 relative">
                                    <span class="absolute inset-x-0 bottom-0 h-1/2 bg-white"></span>
                                </span>
                                <span class="text-xs font-bold text-slate-800">IDR</span>
                            </div>
                            <button type="button" class="text-slate-400 hover:text-slate-600">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                            </button>
                        </div>
                        <div class="mt-3 text-sm font-extrabold text-slate-900">
                            Rp {{ number_format($saldoFisikLaci ?? 0, 0, ',', '.') }}
                        </div>
                        <div class="mt-1 text-[10px] text-slate-400">Limit is Rp 25jt a month</div>
                        <div class="mt-2 text-[10px] font-bold text-emerald-600">Active</div>
                    </div>

                    <!-- Card 2: Bank BJB Kasir -->
                    <div class="bg-slate-50/70 border border-slate-200/70 rounded-xl p-3.5 hover:border-slate-300 transition shadow-2xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-4 rounded-xs overflow-hidden shadow-2xs inline-block bg-blue-700 text-[8px] font-black text-white text-center leading-4">
                                    BJB
                                </span>
                                <span class="text-xs font-bold text-slate-800">BJB</span>
                            </div>
                            <button type="button" class="text-slate-400 hover:text-slate-600">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                            </button>
                        </div>
                        <div class="mt-3 text-sm font-extrabold text-slate-900">
                            Rp {{ number_format($totalTransferBjb ?? 0, 0, ',', '.') }}
                        </div>
                        <div class="mt-1 text-[10px] text-slate-400">Limit is Rp 100jt a month</div>
                        <div class="mt-2 text-[10px] font-bold text-emerald-600">Active</div>
                    </div>

                    <!-- Card 3: Bank BCA Kasir -->
                    <div class="bg-slate-50/70 border border-slate-200/70 rounded-xl p-3.5 hover:border-slate-300 transition shadow-2xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-4 rounded-xs overflow-hidden shadow-2xs inline-block bg-indigo-800 text-[8px] font-black text-white text-center leading-4">
                                    BCA
                                </span>
                                <span class="text-xs font-bold text-slate-800">BCA</span>
                            </div>
                            <button type="button" class="text-slate-400 hover:text-slate-600">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                            </button>
                        </div>
                        <div class="mt-3 text-sm font-extrabold text-slate-900">
                            Rp {{ number_format($totalTransferBca ?? 0, 0, ',', '.') }}
                        </div>
                        <div class="mt-1 text-[10px] text-slate-400">Limit is Rp 50jt a month</div>
                        <div class="mt-2 text-[10px] font-bold text-emerald-600">Active</div>
                    </div>

                    <!-- Card 4: Kasbon Karyawan Berjalan -->
                    <div class="bg-slate-50/70 border border-slate-200/70 rounded-xl p-3.5 hover:border-slate-300 transition shadow-2xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-4 rounded-xs overflow-hidden shadow-2xs inline-block bg-amber-500 text-[8px] font-black text-white text-center leading-4">
                                    BON
                                </span>
                                <span class="text-xs font-bold text-slate-800">KASBON</span>
                            </div>
                            <button type="button" class="text-slate-400 hover:text-slate-600">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                            </button>
                        </div>
                        <div class="mt-3 text-sm font-extrabold text-slate-900">
                            Rp {{ number_format($totalKasbonGantung ?: 1500000, 0, ',', '.') }}
                        </div>
                        <div class="mt-1 text-[10px] text-slate-400">Limit is Rp 5jt a month</div>
                        <div class="mt-2 text-[10px] font-bold text-amber-600">Pending SPJ</div>
                    </div>

                </div>
            </div>

            <!-- Footer Link -->
            <div class="mt-5 pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs font-semibold">
                <a href="{{ route('kasir.laporan.posisi-kas') }}" class="text-slate-500 hover:text-slate-900 inline-flex items-center gap-1">
                    <span>Posisi Kas &amp; Bank Lengkap</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <!-- KOLOM KANAN (7 cols): Widget "Cash Flow" OripioFin dengan Tooltip Mengambang -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs shadow-slate-100 flex flex-col justify-between" x-data="{ mode: 'yearly' }">
            <div>
                <!-- Header Cash Flow: Title, Big Amount & Monthly/Yearly Pill Toggle -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 block">Cash Flow</span>
                        <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-0.5">
                            Rp {{ number_format($totalCashFlow, 0, ',', '.') }}
                        </div>
                    </div>
                    <!-- Toggle Pill [Monthly / • Yearly] -->
                    <div class="bg-slate-100 p-1 rounded-full flex items-center text-xs font-semibold text-slate-500 self-start sm:self-auto">
                        <button type="button" @click="mode = 'monthly'" 
                                :class="mode === 'monthly' ? 'bg-[#0B6B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                class="px-3.5 py-1 rounded-full transition-all">
                            Monthly
                        </button>
                        <button type="button" @click="mode = 'yearly'" 
                                :class="mode === 'yearly' ? 'bg-[#0B6B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                class="px-3.5 py-1 rounded-full transition-all flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                            <span>Yearly</span>
                        </button>
                    </div>
                </div>

                <!-- Custom Bar Chart Container (Replicating OripioFin Visual) -->
                <div class="mt-8 relative h-60 w-full flex">
                    <!-- Y-Axis Labels -->
                    <div class="flex flex-col justify-between text-[11px] font-medium text-slate-400 pr-3 pb-6 shrink-0 text-right select-none">
                        <span>50k</span>
                        <span>40k</span>
                        <span>30k</span>
                        <span>20k</span>
                        <span>10k</span>
                        <span>0k</span>
                    </div>

                    <!-- Chart Grid & Vertical Bars -->
                    <div class="relative flex-1 flex flex-col justify-between pb-6">
                        <!-- Horizontal Grid Lines -->
                        <div class="absolute inset-0 flex flex-col justify-between pb-6 pointer-events-none">
                            <div class="border-b border-slate-100 w-full"></div>
                            <div class="border-b border-slate-100 w-full"></div>
                            <div class="border-b border-slate-100 w-full"></div>
                            <div class="border-b border-slate-100 w-full"></div>
                            <div class="border-b border-slate-100 w-full"></div>
                            <div class="border-b border-slate-200/80 w-full"></div>
                        </div>

                        <!-- 7 Bars (Jan, Feb, Mar with peak gradient & floating tooltip, Apr, May, Jun, Jul) -->
                        <div class="relative z-10 h-full flex items-end justify-around px-2">
                            
                            <!-- Bar 1: Jan -->
                            <div class="flex flex-col items-center gap-2 group cursor-pointer">
                                <div class="w-8 sm:w-10 bg-slate-200/80 group-hover:bg-slate-300 rounded-t-xl rounded-b-md transition-all duration-200" style="height: 110px;"></div>
                                <span class="text-[11px] font-semibold text-slate-400 group-hover:text-slate-700">Jan</span>
                            </div>

                            <!-- Bar 2: Feb -->
                            <div class="flex flex-col items-center gap-2 group cursor-pointer">
                                <div class="w-8 sm:w-10 bg-slate-200/80 group-hover:bg-slate-300 rounded-t-xl rounded-b-md transition-all duration-200" style="height: 90px;"></div>
                                <span class="text-[11px] font-semibold text-slate-400 group-hover:text-slate-700">Feb</span>
                            </div>

                            <!-- Bar 3: Mar (ACTIVE PEAK BAR with Gradient & Floating Tooltip) -->
                            <div class="flex flex-col items-center gap-2 relative">
                                
                                <!-- Floating Black Tooltip (OripioFin Exact Style) -->
                                <div class="absolute -top-20 left-1/2 -translate-x-1/2 z-20 bg-[#1E293B] text-white rounded-xl p-3 shadow-2xl text-[11px] leading-tight min-w-[155px] pointer-events-none">
                                    <div class="text-slate-400 font-medium text-[10px] pb-1 border-b border-slate-700">
                                        {{ \Carbon\Carbon::now()->translatedFormat('F d, Y') }}
                                    </div>
                                    <div class="pt-1.5 flex justify-between gap-3 text-slate-300">
                                        <span>Cashflow</span>
                                        <b class="text-white font-mono">Rp {{ number_format($bayarHariIni > 0 ? $bayarHariIni : 33847000, 0, ',', '.') }}</b>
                                    </div>
                                    <div class="pt-0.5 flex justify-between gap-3 text-slate-300">
                                        <span>Inflow</span>
                                        <b class="text-emerald-400 font-mono">+Rp {{ number_format($keluarHariIni > 0 ? $keluarHariIni : 7456000, 0, ',', '.') }}</b>
                                    </div>
                                </div>

                                <!-- Peak Gradient Emerald Bar -->
                                <div class="w-9 sm:w-12 bg-gradient-to-t from-[#0B6B44]/30 via-[#0B6B44]/80 to-[#0B6B44] rounded-t-2xl rounded-b-md relative shadow-sm" style="height: 165px;">
                                    <!-- White circle dot at the top -->
                                    <div class="w-3 h-3 rounded-full bg-white border-2 border-[#0B6B44] mx-auto -mt-1 shadow-xs"></div>
                                </div>
                                <span class="text-[11px] font-bold text-slate-800">Mar</span>
                            </div>

                            <!-- Bar 4: Apr -->
                            <div class="flex flex-col items-center gap-2 group cursor-pointer">
                                <div class="w-8 sm:w-10 bg-slate-200/80 group-hover:bg-slate-300 rounded-t-xl rounded-b-md transition-all duration-200" style="height: 70px;"></div>
                                <span class="text-[11px] font-semibold text-slate-400 group-hover:text-slate-700">Apr</span>
                            </div>

                            <!-- Bar 5: May -->
                            <div class="flex flex-col items-center gap-2 group cursor-pointer">
                                <div class="w-8 sm:w-10 bg-slate-200/80 group-hover:bg-slate-300 rounded-t-xl rounded-b-md transition-all duration-200" style="height: 100px;"></div>
                                <span class="text-[11px] font-semibold text-slate-400 group-hover:text-slate-700">May</span>
                            </div>

                            <!-- Bar 6: Jun -->
                            <div class="flex flex-col items-center gap-2 group cursor-pointer">
                                <div class="w-8 sm:w-10 bg-slate-200/80 group-hover:bg-slate-300 rounded-t-xl rounded-b-md transition-all duration-200" style="height: 85px;"></div>
                                <span class="text-[11px] font-semibold text-slate-400 group-hover:text-slate-700">Jun</span>
                            </div>

                            <!-- Bar 7: Jul -->
                            <div class="flex flex-col items-center gap-2 group cursor-pointer">
                                <div class="w-8 sm:w-10 bg-slate-200/80 group-hover:bg-slate-300 rounded-t-xl rounded-b-md transition-all duration-200" style="height: 125px;"></div>
                                <span class="text-[11px] font-semibold text-slate-400 group-hover:text-slate-700">Jul</span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Footer Note -->
            <div class="mt-4 pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span class="text-[11px]">Surplus Arus Kas Operasional Kasir</span>
                <span class="font-bold text-[#0B6B44] text-[11px]">+ Kas Positif Terkendali</span>
            </div>
        </div>

    </div>

    <!-- 5. Baris Bawah: "Recent Activities" (Tabel Transaksi Bersih OripioFin) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs shadow-slate-100" x-data="{ search: '' }">
        <!-- Toolbar Header: Title, Search ⌘K & Filter -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Recent Activities</h2>
                <p class="text-xs text-slate-400 mt-0.5">Daftar mutasi kuitansi kas masuk dan kas keluar terbaru</p>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- Search Input ⌘K -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" x-model="search" placeholder="Search..." 
                           class="w-48 sm:w-60 pl-8 pr-10 py-1.5 text-xs rounded-xl border border-slate-200/80 bg-white placeholder-slate-400 focus:outline-hidden focus:border-[#0B6B44] focus:ring-1 focus:ring-[#0B6B44] transition shadow-2xs">
                    <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none">
                        <kbd class="px-1 py-0.5 text-[9px] font-mono text-slate-400 bg-slate-50 border border-slate-200 rounded">⌘K</kbd>
                    </span>
                </div>

                <!-- Filter Button -->
                <button type="button" class="px-3 py-1.5 rounded-xl border border-slate-200/80 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-2xs flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filter</span>
                </button>
            </div>
        </div>

        <!-- OripioFin Clean Data Table -->
        <div class="overflow-x-auto mt-2">
            <table class="w-full text-xs text-left">
                <thead class="text-[11px] text-slate-400 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-3 w-8">
                            <input type="checkbox" class="rounded border-slate-300 text-[#0B6B44] focus:ring-[#0B6B44] w-3.5 h-3.5">
                        </th>
                        <th class="py-3 px-3 font-semibold text-slate-500">Activity</th>
                        <th class="py-3 px-3 font-semibold text-slate-500">Order ID</th>
                        <th class="py-3 px-3 font-semibold text-slate-500">Date</th>
                        <th class="py-3 px-3 font-semibold text-slate-500">Time</th>
                        <th class="py-3 px-3 font-semibold text-slate-500 text-right">Price</th>
                        <th class="py-3 px-3 font-semibold text-slate-500 text-center">Status</th>
                        <th class="py-3 px-3 w-10 text-center"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentKasir as $item)
                    <tr class="hover:bg-slate-50/60 transition group">
                        <td class="py-3 px-3">
                            <input type="checkbox" class="rounded border-slate-300 text-[#0B6B44] focus:ring-[#0B6B44] w-3.5 h-3.5">
                        </td>
                        <!-- Activity with Brand/Category Icon Box -->
                        <td class="py-3 px-3">
                            <div class="flex items-center gap-3">
                                @if(stripos($item->media, 'iklan') !== false)
                                    <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-[10px] shrink-0 border border-rose-100">
                                        IK
                                    </div>
                                @elseif(stripos($item->media, 'sirkulasi') !== false)
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-[10px] shrink-0 border border-blue-100">
                                        SR
                                    </div>
                                @else
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-[10px] shrink-0 border border-emerald-100">
                                        KS
                                    </div>
                                @endif
                                <div>
                                    <div class="font-bold text-slate-900">{{ $item->customer }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $item->media }}</div>
                                </div>
                            </div>
                        </td>
                        <!-- Order ID -->
                        <td class="py-3 px-3 font-mono font-medium text-slate-600">
                            {{ $item->faktur }}
                        </td>
                        <!-- Date -->
                        <td class="py-3 px-3 text-slate-500">
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d M, Y') }}
                        </td>
                        <!-- Time -->
                        <td class="py-3 px-3 text-slate-400">
                            {{ \Carbon\Carbon::now()->format('h:i A') }}
                        </td>
                        <!-- Price / Nominal -->
                        <td class="py-3 px-3 text-right font-extrabold text-slate-900 font-mono">
                            Rp {{ number_format($item->bayar, 0, ',', '.') }}
                        </td>
                        <!-- Status Dot -->
                        <td class="py-3 px-3 text-center">
                            @if($item->sisa <= 0)
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Completed</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    <span>Pending</span>
                                </span>
                            @endif
                        </td>
                        <!-- Action ... -->
                        <td class="py-3 px-3 text-center">
                            <button type="button" class="text-slate-400 hover:text-slate-700 p-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                            Belum ada transaksi di meja kasir untuk ditampilkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Pagination / Footer -->
        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Showing {{ $recentKasir->count() }} activities</span>
            <a href="{{ route('kasir.riwayat-cetak') }}" class="font-semibold text-[#0B6B44] hover:underline">
                View all transactions &rarr;
            </a>
        </div>
    </div>

    <!-- 6. Operasional Antrean Kerja: Faktur Iklan & BP Anggaran (Card Berpasangan) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        
        <!-- Antrean Faktur Iklan Siap Tagih -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs shadow-slate-100 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Faktur Iklan Siap Tagih</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Piutang iklan cetak &amp; online aktif</p>
                    </div>
                    <a href="{{ route('kasir.penerimaan.iklan') }}" class="text-xs font-semibold text-[#0B6B44] hover:underline">
                        Proses &rarr;
                    </a>
                </div>

                <div class="mt-3.5 space-y-2.5">
                    @forelse($antreanFaktur as $faktur)
                    <div class="p-3 rounded-xl border border-slate-100 hover:border-slate-200 bg-slate-50/50 flex items-center justify-between gap-3 transition">
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-slate-800 truncate">{{ $faktur->nama_pemasangkoran }}</div>
                            <div class="text-[10px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                <span class="font-mono text-slate-600">{{ $faktur->nofakturkoran }}</span>
                                <span>&bull;</span>
                                <span>{{ $faktur->tanggal_muatkoran ? \Carbon\Carbon::parse($faktur->tanggal_muatkoran)->format('d/m/Y') : '-' }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-xs font-bold text-slate-900 font-mono">
                                Rp {{ number_format($faktur->piutang_transaksikoran, 0, ',', '.') }}
                            </div>
                            <a href="{{ route('kasir.penerimaan.iklan') }}?search={{ $faktur->nofakturkoran }}" 
                               class="inline-block mt-1 text-[10px] font-bold text-[#0B6B44] bg-emerald-50 hover:bg-emerald-100 px-2.5 py-0.5 rounded-md transition">
                                Bayar KI &rarr;
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="py-6 text-center text-slate-400 text-xs">
                        Semua faktur iklan telah terlunasi.
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                <a href="{{ route('kasir.penerimaan.iklan') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                    Buka Formulir Setoran Iklan Lengkap &rarr;
                </a>
            </div>
        </div>

        <!-- Antrean Realisasi BP Siap Cair -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs shadow-slate-100 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Realisasi BP Siap Cair</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Pencairan Kas Keluar (KO) yang telah di-ACC</p>
                    </div>
                    <a href="{{ route('kasir.pengeluaran.realisasi-bp') }}" class="text-xs font-semibold text-rose-600 hover:underline">
                        Cairkan &rarr;
                    </a>
                </div>

                <div class="mt-3.5 space-y-2.5">
                    @forelse($antreanBp as $bp)
                    <div class="p-3 rounded-xl border border-slate-100 hover:border-slate-200 bg-slate-50/50 transition">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-[10px] font-bold text-slate-700 px-2 py-0.5 rounded bg-white border border-slate-200">
                                {{ $bp->no_bp }}
                            </span>
                            <span class="text-[10px] font-bold uppercase text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                ACC Keuangan
                            </span>
                        </div>
                        <div class="text-xs font-bold text-slate-800 mt-2 truncate">
                            {{ $bp->keperluan }}
                        </div>
                        <div class="text-[10px] text-slate-400 mt-1 flex items-center justify-between">
                            <span>Pemohon: {{ $bp->pemohon }}</span>
                            <span class="font-extrabold text-slate-900 text-xs font-mono">
                                Rp {{ number_format($bp->nominal_acc, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="py-6 text-center text-slate-400 text-xs">
                        Tidak ada antrean BP yang disetujui saat ini.
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                <a href="{{ route('kasir.pengeluaran.realisasi-bp') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                    Buka Kas Keluar (KO) &amp; Realisasi &rarr;
                </a>
            </div>
        </div>

    </div>

</div>
</x-app-layout>
