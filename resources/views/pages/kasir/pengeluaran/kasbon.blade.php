<x-app-layout>
    <div class="space-y-6" x-data="{ 
        activeTab: 'bs',
        showCetakModal: {{ session('cetak_kasbon') ? 'true' : 'false' }},
        showSelesaiModal: false,
        showAccModal: false,
        showTolakModal: false,
        selesaiTarget: { id: null, no: '', nominal: 0, realisasi: 0, no_bkkc: '{{ $nextNoBkkc ?? '' }}', tipe: 'selesai_bkkc', penerima: '' },
        accTarget: { id: null, no: '', nominal: 0, penerima: '', keperluan: '' },
        tolakTarget: { id: null, no: '', nominal: 0, penerima: '' },
        bkkcForm: {
            kasbonId: '',
            namaPenerima: '',
            keperluan: '',
            nominalBs: 0,
            nominalRealisasi: 0,
            noBkkcRef: '{{ $nextNoBkkc ?? '' }}',
            kodeBagian: '03'
        },
        form: {
            namaPenerima: '',
            keperluan: '',
            nominal: '',
            statusAcc: 'langsung',
            approvedBy: 'Ibu Eli Yulianti (Asisten Manajer Keuangan)',
            keterangan: ''
        },
        selectBsForBkkc(event) {
            const opt = event.target.selectedOptions[0];
            if (opt && opt.value) {
                this.bkkcForm.namaPenerima = opt.dataset.penerima || '';
                this.bkkcForm.keperluan = opt.dataset.keperluan || '';
                this.bkkcForm.nominalBs = parseFloat(opt.dataset.nominal || 0);
                this.bkkcForm.nominalRealisasi = parseFloat(opt.dataset.nominal || 0);
            } else {
                this.bkkcForm.nominalBs = 0;
            }
        }
    }">

        <!-- Konten Halaman Kasir (Disembunyikan saat modal cetak aktif) -->
        <div class="space-y-6" :class="{ 'print:hidden': showCetakModal }">

            <!-- Breadcrumb & Header OripioFin -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <!-- Breadcrumb minimalis -->
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1.5">
                        <span class="hover:text-slate-600 transition">Kasir</span>
                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="hover:text-slate-600 transition">Pengeluaran</span>
                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="text-blue-600 flex items-center gap-1.5 font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                            Kasbon Operasional
                        </span>
                    </div>

                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                            Kasbon & Uang Muka Meja Kasir
                        </h1>
                        <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200/80 shadow-2xs flex items-center gap-1">
                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Kasir: {{ Auth::user()->name ?? 'Kasir' }}
                        </span>
                        <span class="px-2.5 py-0.5 text-[11px] font-mono font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                            KB-YYYYMMXXXX
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Pencatatan uang muka untuk pengeluaran mendesak tanpa BP anggaran. Wajib otorisasi Keuangan dan memotong posisi kas fisik laci kasir.
                    </p>
                </div>

                <!-- Action Shortcut Buttons -->
                <div class="flex items-center gap-2.5 flex-wrap">
                    @if(session('cetak_kasbon'))
                        <button type="button" @click="showCetakModal = true" class="px-3.5 py-2 bg-blue-50 hover:bg-blue-100 active:scale-95 text-blue-700 font-bold text-xs rounded-xl border border-blue-200 transition-all shadow-xs flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak Slip Terakhir</span>
                        </button>
                    @endif
                    <a href="{{ route('kasir.laporan.posisi-kas') }}" class="px-3.5 py-2 bg-white hover:bg-slate-50 active:scale-95 text-slate-700 font-bold text-xs rounded-xl border border-slate-200/90 hover:border-slate-300 transition-all shadow-xs flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Rekonsiliasi Kas Fisik</span>
                    </a>
                </div>
            </div>

            <!-- Flash Message Modern -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50/90 border border-emerald-200/90 rounded-2xl text-xs font-semibold text-emerald-800 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </span>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-base font-bold p-1 leading-none">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50/90 border border-rose-200/90 rounded-2xl text-xs font-semibold text-rose-800 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </span>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-base font-bold p-1 leading-none">&times;</button>
                </div>
            @endif

            <!-- Banner Panduan SOP Kasbon Gaya OripioFin (Gradasi Lembut Biru Korporat) -->
            <div class="sop-banner-card bg-gradient-to-r from-blue-50/90 via-indigo-50/70 to-slate-50 dark:from-slate-900 dark:via-[#131c33] dark:to-slate-900 border border-blue-200/80 dark:border-blue-900/40 rounded-2xl p-4 sm:p-5 shadow-xs shadow-slate-100 dark:shadow-none flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-xs shadow-blue-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-blue-950 dark:text-blue-300 uppercase tracking-wider">SOP Pengeluaran Kasbon Operasional</span>
                            <span class="px-2 py-0.5 text-[9px] font-black uppercase rounded bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-200 border border-blue-200 dark:border-blue-700">Wajib ACC</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                            Uang muka hanya untuk kebutuhan mendesak yang belum masuk penganggaran. Posisi kasbon aktif <span class="font-bold text-blue-900 dark:text-blue-300">otomatis menjadi faktor pengurang kas fisik di laci</span> kasir. Kasbon selesai bila uang dikembalikan (lunas) atau dialihkan menjadi realisasi BP anggaran resmi.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="sop-status-pill px-3 py-1.5 bg-white text-blue-700 font-bold text-xs rounded-xl border border-blue-200 shadow-2xs flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        Otorisasi Keuangan
                    </span>
                </div>
            </div>

            <!-- Top 4 KPI Metrics (OripioFin Hover Lift) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Kasbon Gantung Aktif (Rose Accent) -->
                <div class="bg-white rounded-2xl border border-rose-200/90 p-5 shadow-sm shadow-slate-100 hover:-translate-y-1 hover:shadow-md transition-all duration-200 ease-in-out">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider">Kasbon Gantung Aktif</span>
                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                            {{ $countKasbonGantung }} Slip Aktif
                        </span>
                    </div>
                    <div class="mt-3 text-2xl font-black text-rose-600 font-mono tracking-tight">
                        Rp {{ number_format($totalKasbonGantung, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 text-[11px] text-rose-600/80 font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                        <span>Faktor pengurang uang fisik di laci kasir</span>
                    </div>
                </div>

                <!-- Menunggu ACC Keuangan (Amber Accent) -->
                <div class="bg-white rounded-2xl border border-amber-200/90 p-5 shadow-sm shadow-slate-100 hover:-translate-y-1 hover:shadow-md transition-all duration-200 ease-in-out">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Menunggu ACC Keuangan</span>
                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                            {{ $countMenungguAcc }} Pengajuan
                        </span>
                    </div>
                    <div class="mt-3 text-2xl font-black text-amber-700 font-mono tracking-tight">
                        Rp {{ number_format($totalMenungguAcc, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 text-[11px] text-amber-700/80 font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Menunggu otorisasi serah terima fisik</span>
                    </div>
                </div>

                <!-- Estimasi Kas Fisik Laci (Emerald Accent) -->
                <div class="bg-white rounded-2xl border border-emerald-200/90 p-5 shadow-sm shadow-slate-100 hover:-translate-y-1 hover:shadow-md transition-all duration-200 ease-in-out">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Estimasi Kas Fisik Laci</span>
                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Meja Kasir
                        </span>
                    </div>
                    <div class="mt-3 text-2xl font-black text-emerald-700 font-mono tracking-tight">
                        Rp {{ number_format($saldoFisikLaci, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 text-[11px] text-emerald-800/80 font-medium truncate" title="Saldo Sistem (Rp {{ number_format($saldoTeoritisShift, 0, ',', '.') }}) - Kasbon">
                        Sistem (Rp {{ number_format($saldoTeoritisShift, 0, ',', '.') }}) - Kasbon
                    </div>
                </div>

                <!-- Total Kasbon Bulan Ini (Corporate Blue Accent) -->
                <div class="bg-white rounded-2xl border border-blue-200/80 p-5 shadow-sm shadow-slate-100 hover:-translate-y-1 hover:shadow-md transition-all duration-200 ease-in-out">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider">Total Kasbon Bulan Ini</span>
                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                            Bulan Ini
                        </span>
                    </div>
                    <div class="mt-3 text-2xl font-black text-slate-900 font-mono tracking-tight">
                        Rp {{ number_format($totalKasbonBulanIni, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 text-[11px] text-slate-500 font-medium">
                        Akumulasi pengeluaran uang muka periode berjalan
                    </div>
                </div>
            </div>

            <!-- Navigasi 3 Tab SOP Kas Kecil: 1. Bon Sementara (BS) | 2. Penyelesaian Kasbon & BKKC | 3. Pengisian Kembali (LKK & KI-09) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-2 shadow-sm shadow-slate-100 flex flex-wrap items-center gap-2">
                <button type="button" @click="activeTab = 'bs'"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer"
                        :class="activeTab === 'bs' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20' : 'text-slate-600 hover:bg-slate-100'">
                    <span class="px-1.5 py-0.5 rounded text-[10px] font-mono" :class="activeTab === 'bs' ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-700'">SOP 5A</span>
                    <span>1. Bon Sementara (BS / Kasbon)</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeTab === 'bs' ? 'bg-white text-blue-700' : 'bg-rose-100 text-rose-700'">{{ $countKasbonGantung }} Aktif</span>
                </button>
                <button type="button" @click="activeTab = 'bkkc'"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer"
                        :class="activeTab === 'bkkc' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/20' : 'text-slate-600 hover:bg-slate-100'">
                    <span class="px-1.5 py-0.5 rounded text-[10px] font-mono" :class="activeTab === 'bkkc' ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-700'">SOP 5B</span>
                    <span>2. Penyelesaian Kasbon & Penggantian Biaya (BKKC)</span>
                </button>
                <button type="button" @click="activeTab = 'lkk'"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer"
                        :class="activeTab === 'lkk' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/20' : 'text-slate-600 hover:bg-slate-100'">
                    <span class="px-1.5 py-0.5 rounded text-[10px] font-mono" :class="activeTab === 'lkk' ? 'bg-white/20 text-white' : 'bg-indigo-50 text-indigo-700'">SOP 5C</span>
                    <span>3. Pengisian Kembali Kas Kecil (LKK & Pencairan Cek KI-09)</span>
                    @if(($bkkcSiapLkk ?? collect())->count() > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeTab === 'lkk' ? 'bg-white text-indigo-700' : 'bg-indigo-100 text-indigo-700'">{{ $bkkcSiapLkk->count() }} Siap LKK</span>
                    @endif
                </button>
            </div>

            <!-- TAB 1: BON SEMENTARA (BS / KASBON) & MONITORING -->
            <div x-show="activeTab === 'bs'" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- SISI KIRI: Form Pengeluaran Kasbon Proporsional (lg:col-span-5) -->
                <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-sm shadow-slate-100 sticky top-4">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center font-bold shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Form Bon Sementara (BS / Kasbon)</h3>
                                <p class="text-[11px] text-slate-400">Nomor urut otomatis per bulan • Potong kas fisik harian</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-mono font-bold text-blue-700 bg-blue-50 rounded border border-blue-200">
                            {{ $nextNoBs ?? 'KB-BARU' }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('kasir.pengeluaran.kasbon.store') }}" class="space-y-4">
                        @csrf

                        <!-- Nama Penerima -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Penerima Uang Muka (TTD User)</label>
                            <div class="relative">
                                <input type="text" name="nama_penerima" x-model="form.namaPenerima" required placeholder="Contoh: Bu Sofi / Pak Yana" 
                                       class="w-full text-xs py-2.5 px-3.5 rounded-xl border border-slate-200/90 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-slate-900 font-semibold transition-all">
                            </div>
                        </div>

                        <!-- Keperluan Pengeluaran Darurat -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Keperluan Pengeluaran Kas Kecil</label>
                            <input type="text" name="keperluan" x-model="form.keperluan" required placeholder="Contoh: Beli konsumsi redaksi / air galon kantor mendesak" 
                                   class="w-full text-xs py-2.5 px-3.5 rounded-xl border border-slate-200/90 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-slate-900 transition-all">
                        </div>

                        <!-- Nominal Kasbon Fisik -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal Bon Sementara (BS)</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                <input type="number" name="nominal" x-model="form.nominal" required min="1000" step="500" placeholder="0" 
                                       class="w-full text-base font-mono font-black py-2.5 pl-10 pr-3.5 rounded-xl border border-slate-200/90 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-slate-900 bg-slate-50/50 transition-all">
                            </div>
                        </div>

                        <!-- Status Persetujuan / ACC (Segmented Pill Toggle) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Persetujuan Manajer / ACC</label>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition-all duration-150"
                                       :class="form.statusAcc === 'langsung' ? 'bg-blue-50/80 border-blue-400 text-blue-900 font-bold shadow-2xs' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                                    <input type="radio" name="status_acc" value="langsung" x-model="form.statusAcc" class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-[11px]">Sudah ACC (Uang Fisik Keluar)</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition-all duration-150"
                                       :class="form.statusAcc === 'menunggu' ? 'bg-amber-50/80 border-amber-400 text-amber-900 font-bold shadow-2xs' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                                    <input type="radio" name="status_acc" value="menunggu" x-model="form.statusAcc" class="text-amber-600 focus:ring-amber-500">
                                    <span class="text-[11px]">Menunggu ACC Manajer</span>
                                </label>
                            </div>
                        </div>

                        <!-- Pejabat Keuangan yang Menyetujui -->
                        <div x-show="form.statusAcc === 'langsung'" x-transition>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Manajer Keuangan yang Menyetujui (TTD Manajer)</label>
                            <select name="approved_by" x-model="form.approvedBy" class="w-full text-xs py-2.5 px-3.5 rounded-xl border border-slate-200/90 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-slate-900 font-medium transition-all">
                                <option value="Ibu Eli Yulianti (Asisten Manajer Keuangan)">Ibu Eli Yulianti (Asisten Manajer Keuangan)</option>
                                <option value="Moch. Al Nurdin (Pemimpin Perusahaan)">Moch. Al Nurdin (Pemimpin Perusahaan)</option>
                                <option value="Admin Keuangan">Admin Keuangan</option>
                            </select>
                        </div>

                        <!-- Keterangan Tambahan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan Tambahan (Opsional)</label>
                            <textarea name="keterangan" x-model="form.keterangan" rows="2" placeholder="Catatan bukti kasbon atau nomor nota/bon sementara..." 
                                      class="w-full text-xs py-2.5 px-3.5 rounded-xl border border-slate-200/90 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-slate-900 transition-all"></textarea>
                        </div>

                        <!-- Submit Button OripioFin Blue -->
                        <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs rounded-xl transition-all shadow-sm shadow-blue-500/20 flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>Simpan & Keluarkan Bon Sementara (BS)</span>
                        </button>
                    </form>
                </div>

                <!-- SISI KANAN: Tabel Monitoring Kasbon Aktif (lg:col-span-7) -->
                <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-sm shadow-slate-100 overflow-hidden flex flex-col">
                    <!-- Header Toolbar with OripioFin Segmented Pill Tabs -->
                    <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                                Monitoring Bon Sementara (BS), BKKC & LKK
                            </h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Kelola status ACC, realisasi BKKC, selisih kembali tunai, dan LKK</p>
                        </div>

                        <!-- Filter Status Segmented Pills -->
                        <div class="flex items-center gap-1.5 text-xs flex-wrap">
                            <a href="{{ route('kasir.pengeluaran.kasbon', ['status' => 'all']) }}" 
                               class="px-3 py-1 rounded-full text-xs font-semibold transition-all active:scale-95 {{ ($statusFilter ?? 'all') === 'all' ? 'bg-blue-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                Semua
                            </a>
                            <a href="{{ route('kasir.pengeluaran.kasbon', ['status' => 'menunggu_acc']) }}" 
                               class="px-3 py-1 rounded-full text-xs font-semibold transition-all active:scale-95 flex items-center gap-1 {{ ($statusFilter ?? '') === 'menunggu_acc' ? 'bg-amber-500 text-white shadow-2xs' : 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200' }}">
                                <span>Menunggu ACC</span>
                                @if($countMenungguAcc > 0)
                                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ ($statusFilter ?? '') === 'menunggu_acc' ? 'bg-amber-600 text-white' : 'bg-amber-200 text-amber-900' }}">{{ $countMenungguAcc }}</span>
                                @endif
                            </a>
                            <a href="{{ route('kasir.pengeluaran.kasbon', ['status' => 'pending']) }}" 
                               class="px-3 py-1 rounded-full text-xs font-semibold transition-all active:scale-95 {{ ($statusFilter ?? '') === 'pending' ? 'bg-rose-600 text-white shadow-2xs' : 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200' }}">
                                BS Aktif
                            </a>
                            <a href="{{ route('kasir.pengeluaran.kasbon', ['status' => 'selesai_bkkc']) }}" 
                               class="px-3 py-1 rounded-full text-xs font-semibold transition-all active:scale-95 {{ ($statusFilter ?? '') === 'selesai_bkkc' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' }}">
                                Selesai BKKC
                            </a>
                            <a href="{{ route('kasir.pengeluaran.kasbon', ['status' => 'lunas']) }}" 
                               class="px-3 py-1 rounded-full text-xs font-semibold transition-all active:scale-95 {{ ($statusFilter ?? '') === 'lunas' ? 'bg-teal-600 text-white shadow-2xs' : 'bg-teal-50 text-teal-700 hover:bg-teal-100 border border-teal-200' }}">
                                Lunas Tunai
                            </a>
                        </div>
                    </div>

                    <!-- Table Responsive Container -->
                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-slate-50/80 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200/80">
                                <tr>
                                    <th class="px-4 py-3 font-bold">No. BS / Ref BKKC</th>
                                    <th class="px-4 py-3 font-bold">Tanggal</th>
                                    <th class="px-4 py-3 font-bold">Penerima & Keperluan</th>
                                    <th class="px-4 py-3 font-bold text-right">Nominal BS / Realisasi</th>
                                    <th class="px-4 py-3 font-bold text-center">Status</th>
                                    <th class="px-4 py-3 font-bold">Otorisasi TTD</th>
                                    <th class="px-4 py-3 font-bold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($kasbonList as $kb)
                                <tr class="hover:bg-slate-50/70 transition-colors duration-150">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="font-mono font-bold text-blue-700">{{ $kb->no_bs ?? $kb->no_kasbon }}</div>
                                        @if($kb->no_bkkc_ref)
                                            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                {{ $kb->no_bkkc_ref }}
                                            </span>
                                        @endif
                                        @if($kb->no_lkk_ref)
                                            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                {{ $kb->no_lkk_ref }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 whitespace-nowrap">{{ Carbon\Carbon::parse($kb->tanggal)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 min-w-[160px]">
                                        <div class="font-bold text-slate-900">{{ $kb->nama_penerima }}</div>
                                        <div class="text-[11px] text-slate-500 line-clamp-1">{{ $kb->keperluan }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono whitespace-nowrap">
                                        <div class="font-bold {{ in_array($kb->status, ['pending', 'aktif']) ? 'text-rose-600' : 'text-slate-800' }}">
                                            BS: Rp {{ number_format($kb->nominal_bs ?: $kb->nominal, 0, ',', '.') }}
                                        </div>
                                        @if($kb->nominal_realisasi !== null)
                                            <div class="text-[10px] text-emerald-700 font-semibold">
                                                Nota: Rp {{ number_format($kb->nominal_realisasi, 0, ',', '.') }}
                                            </div>
                                        @endif
                                        @if(($kb->selisih_kembali_tunai ?? 0) > 0)
                                            <div class="text-[10px] text-blue-700 font-semibold">
                                                Kembali: Rp {{ number_format($kb->selisih_kembali_tunai, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        @if($kb->status === 'menunggu_acc')
                                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-50 text-amber-800 border border-amber-300 inline-flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Menunggu ACC
                                            </span>
                                        @elseif(in_array($kb->status, ['pending', 'aktif']))
                                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200 inline-flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                BS Aktif
                                            </span>
                                        @elseif($kb->status === 'selesai_bkkc')
                                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Selesai BKKC
                                            </span>
                                        @elseif($kb->status === 'lunas')
                                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-teal-50 text-teal-700 border border-teal-200 inline-flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                                                Lunas Kembali
                                            </span>
                                        @elseif($kb->status === 'masuk_bp')
                                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 inline-flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                                Masuk BP/BKKC
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                                Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 text-[11px] whitespace-nowrap">
                                        @if($kb->approved_by)
                                            <span class="font-semibold text-emerald-700 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>{{ Str::limit($kb->approved_by, 18) }}</span>
                                            </span>
                                        @else
                                            <span class="text-amber-600 italic">Belum di-ACC</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        @if($kb->status === 'menunggu_acc')
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button type="button" 
                                                        @click="accTarget = { id: {{ $kb->id }}, no: '{{ $kb->no_kasbon }}', nominal: {{ $kb->nominal }}, penerima: '{{ addslashes($kb->nama_penerima) }}', keperluan: '{{ addslashes($kb->keperluan) }}' }; showAccModal = true"
                                                        class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-[10px] rounded-lg transition-all shadow-2xs flex items-center gap-1 cursor-pointer">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span>ACC</span>
                                                </button>
                                                <button type="button" 
                                                        @click="tolakTarget = { id: {{ $kb->id }}, no: '{{ $kb->no_kasbon }}', nominal: {{ $kb->nominal }}, penerima: '{{ addslashes($kb->nama_penerima) }}' }; showTolakModal = true"
                                                        class="px-2 py-1 bg-white hover:bg-rose-50 active:scale-95 text-rose-700 border border-rose-300 font-semibold text-[10px] rounded-lg transition-all cursor-pointer">
                                                    Tolak
                                                </button>
                                            </div>
                                        @elseif(in_array($kb->status, ['pending', 'aktif']))
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button type="button" 
                                                        @click="selesaiTarget = { id: {{ $kb->id }}, no: '{{ $kb->no_bs ?? $kb->no_kasbon }}', nominal: {{ (float)($kb->nominal_bs ?: $kb->nominal) }}, realisasi: {{ (float)($kb->nominal_bs ?: $kb->nominal) }}, no_bkkc: '{{ $nextNoBkkc ?? '' }}', tipe: 'selesai_bkkc', penerima: '{{ addslashes($kb->nama_penerima) }}' }; showSelesaiModal = true"
                                                        class="px-3 py-1 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-[11px] rounded-lg transition-all shadow-2xs cursor-pointer">
                                                    Selesaikan BKKC
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-[10px] text-slate-400 font-medium">Selesai: {{ $kb->selesai_at ? Carbon\Carbon::parse($kb->selesai_at)->format('d/m/Y') : '-' }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                                        <p class="font-medium text-xs text-slate-500">Tidak ada catatan kasbon pada status ini.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($kasbonList->hasPages())
                    <div class="p-3.5 border-t border-slate-100 bg-slate-50/50">
                        {{ $kasbonList->links() }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- TAB 2: PENYELESAIAN KASBON & PENGGANTIAN BIAYA (BKKC - SOP 5B) -->
            <div x-cloak x-show="activeTab === 'bkkc'" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <div class="lg:col-span-6 bg-white rounded-2xl border border-emerald-200/80 p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Form Penyelesaian BS & Penggantian Biaya (BKKC)</h3>
                            <p class="text-[11px] text-slate-500">Panggil BS menggantung atau input penggantian biaya nota langsung</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-mono font-bold text-xs border border-emerald-200">
                            {{ $nextNoBkkc ?? 'BKKC-AUTO' }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('kasir.pengeluaran.kasbon.bkkc.store') }}" class="space-y-4 text-xs">
                        @csrf
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">1. Pilih Nomor BS / Kasbon Menggantung (Atau Kosongkan untuk BKKC Langsung)</label>
                            <select name="kasbon_id" x-model="bkkcForm.kasbonId" @change="selectBsForBkkc($event)"
                                    class="w-full text-xs py-2.5 px-3 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-slate-900 font-semibold bg-slate-50/60">
                                <option value="">-- Tanpa BS (Penggantian Biaya / BKKC Langsung) --</option>
                                @foreach($kasbonAktifList ?? [] as $bsAktif)
                                    <option value="{{ $bsAktif->id }}"
                                            data-penerima="{{ $bsAktif->nama_penerima }}"
                                            data-keperluan="{{ $bsAktif->keperluan }}"
                                            data-nominal="{{ (float)($bsAktif->nominal_bs ?: $bsAktif->nominal) }}">
                                        {{ $bsAktif->no_bs ?? $bsAktif->no_kasbon }} — {{ $bsAktif->nama_penerima }} (BS: Rp {{ number_format($bsAktif->nominal_bs ?: $bsAktif->nominal, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nomor Referensi BKKC / BP</label>
                                <input type="text" name="no_bkkc_ref" x-model="bkkcForm.noBkkcRef" placeholder="{{ $nextNoBkkc ?? 'BKKC-...' }}"
                                       class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 font-mono font-bold text-slate-900">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Divisi / Bagian Beban</label>
                                <select name="kode_bagian" x-model="bkkcForm.kodeBagian" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 font-semibold text-slate-800">
                                    <option value="03">03 - Umum & SDM</option>
                                    <option value="06">06 - Redaksi</option>
                                    <option value="01">01 - Iklan</option>
                                    <option value="02">02 - Sirkulasi</option>
                                    <option value="05">05 - Keuangan</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nama Penerima / Pemohon</label>
                                <input type="text" name="nama_penerima" x-model="bkkcForm.namaPenerima" required placeholder="Nama karyawan..."
                                       class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 font-semibold text-slate-900">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Uraian Realisasi Nota / Bukti</label>
                                <input type="text" name="keperluan" x-model="bkkcForm.keperluan" required placeholder="Uraian belanja sesuai nota..."
                                       class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 text-slate-900">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 bg-emerald-50/60 border border-emerald-200 rounded-xl">
                            <div>
                                <label class="block font-bold text-emerald-950 mb-1">Nilai Realisasi Belanja Sesuai Nota (Rp)</label>
                                <input type="number" name="nominal_realisasi" x-model="bkkcForm.nominalRealisasi" required min="0"
                                       class="w-full text-sm font-mono font-black py-2 px-3 rounded-lg border border-emerald-300 bg-white text-slate-900">
                            </div>
                            <div class="flex flex-col justify-center">
                                <span class="text-[11px] text-emerald-800 font-semibold">Selisih Uang Tunai Kembali ke Laci Kasir:</span>
                                <span class="text-base font-mono font-black text-emerald-700 mt-0.5">
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(Math.max(0, (bkkcForm.nominalBs || 0) - (bkkcForm.nominalRealisasi || 0)))"></span>
                                </span>
                                <span class="text-[10px] text-emerald-700">Otomatis menambah kembali kas fisik laci (balance)</span>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm transition cursor-pointer">
                            Simpan Penyelesaian BKKC & Catat Pengembalian Tunai
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-6 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-3">
                    <h3 class="text-sm font-bold text-slate-900">Daftar BKKC Selesai (Siap Direkap ke LKK)</h3>
                    <p class="text-xs text-slate-500">Daftar bukti kas kecil yang telah memiliki nilai realisasi nota dan siap diajukan pengisian kembali (LKK).</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase border-b border-slate-200">
                                <tr>
                                    <th class="px-3 py-2">No. BKKC / BS</th>
                                    <th class="px-3 py-2">Penerima & Nota</th>
                                    <th class="px-3 py-2 text-right">Nominal BS</th>
                                    <th class="px-3 py-2 text-right">Realisasi BKKC</th>
                                    <th class="px-3 py-2 text-right">Kembali Tunai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($bkkcSiapLkk ?? [] as $item)
                                <tr>
                                    <td class="px-3 py-2 font-mono font-bold text-emerald-700">
                                        {{ $item->no_bkkc_ref ?? $item->no_kasbon }}
                                        <div class="text-[10px] text-slate-400">{{ $item->no_bs ?? $item->no_kasbon }}</div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="font-bold text-slate-800">{{ $item->nama_penerima }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $item->keperluan }}</div>
                                    </td>
                                    <td class="px-3 py-2 text-right font-mono">Rp {{ number_format($item->nominal_bs ?: $item->nominal, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right font-mono font-bold text-emerald-700">Rp {{ number_format($item->nominal_realisasi ?? $item->nominal, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right font-mono text-blue-700">Rp {{ number_format($item->selisih_kembali_tunai ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-6 text-center text-slate-400">Belum ada BKKC baru yang menunggu rekap LKK.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 3: PENGISIAN KEMBALI KAS KECIL (LKK & PENCAIRAN CEK KI-09 - SOP 5C) -->
            <div x-cloak x-show="activeTab === 'lkk'" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <div class="lg:col-span-6 bg-white rounded-2xl border border-indigo-200/80 p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Rekap LKK & Pencairan Cek Pengisian Kembali (KI-09)</h3>
                            <p class="text-[11px] text-slate-500">Himpun BKKC ke LKK & catat pencairan Cek BBK Keuangan sebagai KI-09</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-mono font-bold text-xs border border-indigo-200">
                            {{ $nextNoLkk ?? 'LKK-AUTO' }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('kasir.pengeluaran.kasbon.lkk.store') }}" class="space-y-4 text-xs">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nomor LKK (Laporan Kas Kecil)</label>
                                <input type="text" name="no_lkk_ref" value="{{ $nextNoLkk ?? '' }}" required
                                       class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 font-mono font-bold text-indigo-700 bg-indigo-50/40">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Total Realisasi BKKC Terpilih</label>
                                <input type="number" name="nominal_pengisian" value="{{ (float)($totalSiapLkk ?? 0) }}" min="0"
                                       class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 font-mono font-bold text-slate-900">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Pilih Bukti BKKC yang Dihimpun ke LKK Ini:</label>
                            <div class="max-h-40 overflow-y-auto border border-slate-200 rounded-xl p-2.5 space-y-1.5 bg-slate-50/60">
                                @forelse($bkkcSiapLkk ?? [] as $bkkc)
                                    <label class="flex items-center justify-between p-2 bg-white rounded-lg border border-slate-200 cursor-pointer">
                                        <span class="flex items-center gap-2">
                                            <input type="checkbox" name="kasbon_ids[]" value="{{ $bkkc->id }}" checked class="rounded text-indigo-600">
                                            <span class="font-mono font-bold text-slate-800">{{ $bkkc->no_bkkc_ref ?? $bkkc->no_kasbon }}</span>
                                            <span class="text-slate-600">({{ $bkkc->nama_penerima }} - {{ Str::limit($bkkc->keperluan, 28) }})</span>
                                        </span>
                                        <span class="font-mono font-bold text-emerald-700">Rp {{ number_format($bkkc->nominal_realisasi ?? $bkkc->nominal, 0, ',', '.') }}</span>
                                    </label>
                                @empty
                                    <div class="text-slate-400 text-center py-2">Tidak ada BKKC gantung. Anda tetap dapat mencatat pencairan Cek KI-09 secara langsung di bawah.</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="p-3.5 bg-indigo-50/70 border border-indigo-200 rounded-xl space-y-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="cairkan_cek_ki09" value="1" checked class="rounded text-indigo-600 w-4 h-4">
                                <span class="font-bold text-indigo-950">Cairkan Cek Pengisian Kembali & Terbitkan KI-09 (Debet Kas Fisik / Kredit Bank)</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-[11px] font-bold text-indigo-900 mb-0.5">Nomor Cek / BBK Keuangan</label>
                                    <input type="text" name="no_cek_bbk" placeholder="Contoh: CEK-BJB-LKK-001"
                                           class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-indigo-300 font-mono bg-white text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-indigo-900 mb-0.5">Rekening Bank Sumber Dana</label>
                                    <select name="bank_sumber" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-indigo-300 bg-white text-slate-900">
                                        <option value="Bank BJB Operasional">Bank BJB Operasional</option>
                                        <option value="Bank BCA Tasikmalaya">Bank BCA Tasikmalaya</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm transition cursor-pointer">
                            Terbitkan Rekap LKK & Catat Penarikan Tunai Bank (KI-09)
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-6 bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Riwayat Rekap LKK & Pencairan Cek KI-09</h3>
                        <p class="text-xs text-slate-500">Jejak pengisian kembali kas kecil yang menambah uang fisik laci kasir (Debet Kas) dan mengurangi buku bank (Kredit Bank).</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase border-b border-slate-200">
                                <tr>
                                    <th class="px-3 py-2">No. KI-09</th>
                                    <th class="px-3 py-2">Tanggal</th>
                                    <th class="px-3 py-2">Bank & Keterangan LKK</th>
                                    <th class="px-3 py-2 text-right">Debet Kas / Kredit Bank</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($penarikanBankLkk ?? [] as $ki09)
                                <tr>
                                    <td class="px-3 py-2 font-mono font-bold text-indigo-700">{{ $ki09->no_kuitansi }}</td>
                                    <td class="px-3 py-2 text-slate-600">{{ $ki09->tanggal ? $ki09->tanggal->format('d/m/Y') : '-' }}</td>
                                    <td class="px-3 py-2">
                                        <div class="font-semibold text-slate-800">{{ $ki09->bank_tujuan ?? 'Bank BJB' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $ki09->keterangan }}</div>
                                    </td>
                                    <td class="px-3 py-2 text-right font-mono font-bold text-emerald-700">Rp {{ number_format($ki09->nominal_bayar, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-6 text-center text-slate-400">Belum ada riwayat penarikan cek LKK (KI-09).</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Persetujuan (ACC) Kasbon oleh Keuangan -->
        <div x-cloak x-show="showAccModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showAccModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200/80 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-emerald-50/50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Otorisasi & ACC Kasbon Keuangan</h4>
                            <p class="text-[11px] text-slate-500">Persetujuan pengeluaran uang muka kasir</p>
                        </div>
                    </div>
                    <button type="button" @click="showAccModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none p-1">&times;</button>
                </div>

                <form :action="'{{ url('/kasir/pengeluaran/kasbon') }}/' + accTarget.id + '/acc'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="p-4 bg-slate-50/80 rounded-xl border border-slate-200/90 text-xs space-y-2">
                        <div class="flex justify-between">
                            <span class="text-slate-500">No. Kasbon:</span>
                            <span class="font-mono font-bold text-slate-900" x-text="accTarget.no"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Pemohon / Penerima:</span>
                            <span class="font-bold text-slate-800" x-text="accTarget.penerima"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Keperluan:</span>
                            <span class="text-slate-700 font-medium" x-text="accTarget.keperluan"></span>
                        </div>
                        <div class="flex justify-between border-t border-slate-200 pt-2">
                            <span class="text-slate-600 font-bold">Nominal Kasbon:</span>
                            <span class="font-bold font-mono text-emerald-700 text-base">Rp <span x-text="new Intl.NumberFormat('id-ID').format(accTarget.nominal)"></span></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Pejabat Keuangan yang Menyetujui (ACC)</label>
                        <select name="approved_by" class="w-full text-xs py-2.5 px-3.5 rounded-xl border border-slate-200/90 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 text-slate-900 font-medium transition-all">
                            <option value="Ibu Eli Yulianti (Asisten Manajer Keuangan)">Ibu Eli Yulianti (Asisten Manajer Keuangan)</option>
                            <option value="Moch. Al Nurdin (Pemimpin Perusahaan)">Moch. Al Nurdin (Pemimpin Perusahaan)</option>
                            <option value="{{ Auth::user()->name }} (Keuangan)">{{ Auth::user()->name }} (Keuangan)</option>
                        </select>
                        <span class="text-[10px] text-slate-400 mt-1 block">Setelah di-ACC, uang fisik resmi diserahkan ke penerima dan memotong saldo kas laci.</span>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2">
                        <button type="button" @click="showAccModal = false" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 active:scale-95 text-slate-700 font-semibold text-xs rounded-xl transition-all">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-xs rounded-xl transition-all shadow-xs flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Setujui & ACC Kasbon</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Tolak Kasbon -->
        <div x-cloak x-show="showTolakModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showTolakModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200/80 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-rose-50/50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-rose-900">Tolak Permohonan Kasbon</h4>
                            <p class="text-[11px] text-slate-500">Konfirmasi pembatalan uang muka</p>
                        </div>
                    </div>
                    <button type="button" @click="showTolakModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none p-1">&times;</button>
                </div>

                <form :action="'{{ url('/kasir/pengeluaran/kasbon') }}/' + tolakTarget.id + '/tolak'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="p-4 bg-slate-50/80 rounded-xl border border-slate-200/90 text-xs space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-slate-500">No. Kasbon:</span>
                            <span class="font-mono font-bold text-slate-900" x-text="tolakTarget.no"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Penerima:</span>
                            <span class="font-bold text-slate-800" x-text="tolakTarget.penerima"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Alasan Penolakan</label>
                        <input type="text" name="alasan_tolak" required placeholder="Contoh: Belum ada urgensi / gunakan kas kecil rutin" 
                               class="w-full text-xs py-2.5 px-3.5 rounded-xl border border-slate-200/90 focus:border-rose-500 focus:ring-2 focus:ring-rose-100 text-slate-900 transition-all">
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2">
                        <button type="button" @click="showTolakModal = false" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 active:scale-95 text-slate-700 font-semibold text-xs rounded-xl transition-all">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-bold text-xs rounded-xl transition-all shadow-xs">
                            Konfirmasi Tolak Kasbon
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Selesaikan Kasbon -->
        <div x-cloak x-show="showSelesaiModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showSelesaiModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200/80 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-blue-50/50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Selesaikan Kasbon Meja Kasir</h4>
                            <p class="text-[11px] text-slate-500">Pilih opsi pengembalian dana atau alih BP</p>
                        </div>
                    </div>
                    <button type="button" @click="showSelesaiModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none p-1">&times;</button>
                </div>

                <form :action="'{{ url('/kasir/pengeluaran/kasbon') }}/' + selesaiTarget.id + '/selesai'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="p-4 bg-slate-50/80 rounded-xl border border-slate-200/90 text-xs space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-slate-500">No. Kasbon:</span>
                            <span class="font-mono font-bold text-slate-900" x-text="selesaiTarget.no"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Penerima:</span>
                            <span class="font-bold text-slate-800" x-text="selesaiTarget.penerima"></span>
                        </div>
                        <div class="flex justify-between border-t border-slate-200 pt-1.5">
                            <span class="text-slate-600 font-bold">Nominal Kasbon:</span>
                            <span class="font-bold font-mono text-rose-600 text-base">Rp <span x-text="new Intl.NumberFormat('id-ID').format(selesaiTarget.nominal)"></span></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Penyelesaian BS / Kasbon</label>
                        <select name="tipe_selesai" x-model="selesaiTarget.tipe" required class="w-full text-xs py-2.5 px-3.5 rounded-xl border border-slate-200/90 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-slate-900 font-semibold transition-all">
                            <option value="selesai_bkkc">1. Realisasi Belanja Nota / Bukti Kas Kecil (BKKC) + Kembalikan Sisa Tunai</option>
                            <option value="lunas">2. Uang Fisik Dikembalikan Utuh 100% Tunai (Batal Belanja)</option>
                            <option value="masuk_bp">3. Diterbitkan Nomor BP Anggaran Resmi (Masuk KO Anggaran)</option>
                        </select>
                    </div>

                    <div x-show="selesaiTarget.tipe !== 'lunas'" class="p-3.5 bg-emerald-50/70 border border-emerald-200 rounded-xl space-y-2.5 text-xs">
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block font-bold text-emerald-950 mb-1">Nilai Realisasi Nota (Rp)</label>
                                <input type="number" name="nominal_realisasi" x-model="selesaiTarget.realisasi" min="0"
                                       class="w-full text-xs font-mono font-bold py-2 px-3 rounded-lg border border-emerald-300 bg-white text-slate-900">
                            </div>
                            <div>
                                <label class="block font-bold text-emerald-950 mb-1">Nomor BKKC / BP Ref</label>
                                <input type="text" name="no_bkkc_ref" x-model="selesaiTarget.no_bkkc" placeholder="BKKC-..."
                                       class="w-full text-xs font-mono font-bold py-2 px-3 rounded-lg border border-emerald-300 bg-white text-slate-900">
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-1.5 border-t border-emerald-200">
                            <span class="text-emerald-900 font-semibold">Selisih Uang Tunai Kembali ke Laci:</span>
                            <span class="font-mono font-black text-emerald-700 text-sm">
                                Rp <span x-text="new Intl.NumberFormat('id-ID').format(Math.max(0, (selesaiTarget.nominal || 0) - (selesaiTarget.realisasi || 0)))"></span>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Penyelesaian</label>
                        <input type="text" name="catatan" placeholder="Contoh: Nota konsumsi rapat Rp450.000, sisa Rp50.000 kembali tunai..." 
                               class="w-full text-xs py-2.5 px-3.5 rounded-xl border border-slate-200/90 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-slate-900 transition-all">
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2">
                        <button type="button" @click="showSelesaiModal = false" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 active:scale-95 text-slate-700 font-semibold text-xs rounded-xl transition-all">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs rounded-xl transition-all shadow-xs">
                            Konfirmasi Selesai
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Cetak Voucher Kasbon (OripioFin Clean Printable Slip) -->
        @if(session('cetak_kasbon'))
        @php $kb = session('cetak_kasbon'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200/80 overflow-hidden">
                <!-- Header Dokumen Cetak -->
                <div class="border-b-2 border-slate-900 px-6 py-4 flex items-start justify-between shrink-0 bg-slate-50/50">
                    <div>
                        <div class="text-base font-black tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500">PT. Priangan Berkah Media - Bagian Perkasiran</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2.5 py-0.5 text-[10px] font-black rounded-full bg-blue-100 text-blue-900 uppercase tracking-widest border border-blue-300">
                            VOUCHER KASBON
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1.5">{{ $kb['no_kasbon'] }}</div>
                        <div class="text-[10px] text-slate-500">{{ $kb['tanggal'] }}</div>
                    </div>
                </div>

                <!-- Body Dokumen -->
                <div class="print-document-body flex-1 overflow-y-auto p-6 space-y-3 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Diberikan Kepada</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $kb['penerima'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Keperluan Uang Muka</span>
                        <span class="col-span-2 text-slate-800 font-medium">: {{ $kb['keperluan'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Disetujui Oleh (ACC)</span>
                        <span class="col-span-2 font-bold text-emerald-800">: {{ $kb['approved_by'] }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-2.5 bg-blue-50/70 px-4 rounded-xl border border-blue-200/80 my-2.5">
                        <span class="font-bold text-blue-900 self-center">Nominal Uang Kasbon</span>
                        <span class="col-span-2 font-mono font-black text-lg text-blue-900">: Rp {{ number_format($kb['nominal'], 0, ',', '.') }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-2 text-xs bg-slate-50 px-4 rounded-xl border border-slate-200">
                        <span class="text-slate-500 font-semibold">Terbilang Huruf</span>
                        <span class="col-span-2 font-semibold italic text-slate-900">: {{ $kb['terbilang'] }}</span>
                    </div>

                    <div class="p-2.5 bg-slate-50 rounded-xl text-[10px] text-slate-500 italic border border-slate-100">
                        * Lembar bukti uang muka operasional meja kasir. Saldo kasbon ini otomatis memotong uang fisik kasir sampai diselesaikan atau diterbitkan nomor BP anggaran resmi.
                    </div>

                    <!-- Tanda Tangan Cetak 3 Kolom -->
                    <div class="pt-6 border-t border-slate-200 grid grid-cols-3 gap-2 text-center text-xs text-slate-600">
                        <div>
                            <div class="font-semibold text-slate-700">Penerima Kasbon</div>
                            <div class="h-14"></div>
                            <div class="font-bold text-slate-900 underline">({{ $kb['penerima'] }})</div>
                        </div>
                        <div>
                            <div class="font-semibold text-slate-700">Menyetujui (Keuangan)</div>
                            <div class="h-14"></div>
                            <div class="font-bold text-slate-900 underline">({{ Str::words($kb['approved_by'], 2, '') }})</div>
                        </div>
                        <div>
                            <div class="font-semibold text-slate-700">Kasir Pembayar</div>
                            <div class="h-14"></div>
                            <div class="font-bold text-slate-900 font-mono underline">({{ $kb['kasir'] }})</div>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="px-6 py-3.5 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2.5 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 active:scale-95 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition-all">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs rounded-xl transition-all shadow-sm shadow-blue-500/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
