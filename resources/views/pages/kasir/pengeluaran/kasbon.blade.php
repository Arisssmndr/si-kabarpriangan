<x-app-layout>
    <div class="space-y-6" x-data="{ 
        showCetakModal: {{ session('cetak_kasbon') ? 'true' : 'false' }},
        showSelesaiModal: false,
        showAccModal: false,
        showTolakModal: false,
        selesaiTarget: { id: null, no: '', nominal: 0, penerima: '' },
        accTarget: { id: null, no: '', nominal: 0, penerima: '', keperluan: '' },
        tolakTarget: { id: null, no: '', nominal: 0, penerima: '' },
        form: {
            namaPenerima: '',
            keperluan: '',
            nominal: '',
            statusAcc: 'langsung',
            approvedBy: 'Ibu Eli Yulianti (Asisten Manajer Keuangan)',
            keterangan: ''
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
            <div class="bg-gradient-to-r from-blue-50/90 via-indigo-50/70 to-slate-50 border border-blue-200/80 rounded-2xl p-4 sm:p-5 shadow-xs shadow-slate-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-xs shadow-blue-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-blue-950 uppercase tracking-wider">SOP Pengeluaran Kasbon Operasional</span>
                            <span class="px-2 py-0.5 text-[9px] font-black uppercase rounded bg-blue-100 text-blue-800 border border-blue-200">Wajib ACC</span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            Uang muka hanya untuk kebutuhan mendesak yang belum masuk penganggaran. Posisi kasbon aktif <span class="font-bold text-blue-900">otomatis menjadi faktor pengurang kas fisik di laci</span> kasir. Kasbon selesai bila uang dikembalikan (lunas) atau dialihkan menjadi realisasi BP anggaran resmi.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="px-3 py-1.5 bg-white text-blue-700 font-bold text-xs rounded-xl border border-blue-200 shadow-2xs flex items-center gap-1.5">
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

            <!-- Split Grid Layout: Sisi Kiri Form Pengeluaran Kasbon, Sisi Kanan Tabel Monitoring Kasbon -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- SISI KIRI: Form Pengeluaran Kasbon Proporsional (lg:col-span-5) -->
                <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-sm shadow-slate-100 sticky top-4">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center font-bold shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Form Pengeluaran Kasbon</h3>
                                <p class="text-[11px] text-slate-400">Dicatat langsung oleh Petugas Kasir</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-mono font-bold text-blue-700 bg-blue-50 rounded border border-blue-200">
                            KB-BARU
                        </span>
                    </div>

                    <form method="POST" action="{{ route('kasir.pengeluaran.kasbon.store') }}" class="space-y-4">
                        @csrf

                        <!-- Nama Penerima -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Penerima Uang Muka</label>
                            <div class="relative">
                                <input type="text" name="nama_penerima" x-model="form.namaPenerima" required placeholder="Contoh: Bu Sofi / Pak Yana" 
                                       class="w-full text-xs py-2.5 px-3.5 rounded-xl border border-slate-200/90 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-slate-900 font-semibold transition-all">
                            </div>
                        </div>

                        <!-- Keperluan Pengeluaran Darurat -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Keperluan Pengeluaran Darurat</label>
                            <input type="text" name="keperluan" x-model="form.keperluan" required placeholder="Contoh: Beli konsumsi redaksi / air galon kantor mendesak" 
                                   class="w-full text-xs py-2.5 px-3.5 rounded-xl border border-slate-200/90 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-slate-900 transition-all">
                        </div>

                        <!-- Nominal Kasbon Fisik -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal Kasbon Fisik</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                <input type="number" name="nominal" x-model="form.nominal" required min="1000" step="500" placeholder="0" 
                                       class="w-full text-base font-mono font-black py-2.5 pl-10 pr-3.5 rounded-xl border border-slate-200/90 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-slate-900 bg-slate-50/50 transition-all">
                            </div>
                        </div>

                        <!-- Status Persetujuan / ACC (Segmented Pill Toggle) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Persetujuan / ACC</label>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition-all duration-150"
                                       :class="form.statusAcc === 'langsung' ? 'bg-blue-50/80 border-blue-400 text-blue-900 font-bold shadow-2xs' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                                    <input type="radio" name="status_acc" value="langsung" x-model="form.statusAcc" class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-[11px]">Sudah ACC (Uang Fisik Keluar)</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition-all duration-150"
                                       :class="form.statusAcc === 'menunggu' ? 'bg-amber-50/80 border-amber-400 text-amber-900 font-bold shadow-2xs' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                                    <input type="radio" name="status_acc" value="menunggu" x-model="form.statusAcc" class="text-amber-600 focus:ring-amber-500">
                                    <span class="text-[11px]">Menunggu ACC Keuangan</span>
                                </label>
                            </div>
                        </div>

                        <!-- Pejabat Keuangan yang Menyetujui -->
                        <div x-show="form.statusAcc === 'langsung'" x-transition>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Pejabat Keuangan yang Menyetujui (ACC)</label>
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
                        <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs rounded-xl transition-all shadow-sm shadow-blue-500/20 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>Simpan & Keluarkan Kasbon (KB)</span>
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
                                Monitoring Kasbon Meja Kasir
                            </h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Kelola status ACC dan penyelesaian uang muka</p>
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
                                Gantung (Aktif)
                            </a>
                            <a href="{{ route('kasir.pengeluaran.kasbon', ['status' => 'lunas']) }}" 
                               class="px-3 py-1 rounded-full text-xs font-semibold transition-all active:scale-95 {{ ($statusFilter ?? '') === 'lunas' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' }}">
                                Lunas
                            </a>
                            <a href="{{ route('kasir.pengeluaran.kasbon', ['status' => 'masuk_bp']) }}" 
                               class="px-3 py-1 rounded-full text-xs font-semibold transition-all active:scale-95 {{ ($statusFilter ?? '') === 'masuk_bp' ? 'bg-indigo-600 text-white shadow-2xs' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200' }}">
                                Masuk BP
                            </a>
                        </div>
                    </div>

                    <!-- Table Responsive Container -->
                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-slate-50/80 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200/80">
                                <tr>
                                    <th class="px-4 py-3 font-bold">No. Kasbon</th>
                                    <th class="px-4 py-3 font-bold">Tanggal</th>
                                    <th class="px-4 py-3 font-bold">Penerima & Keperluan</th>
                                    <th class="px-4 py-3 font-bold text-right">Nominal (Rp)</th>
                                    <th class="px-4 py-3 font-bold text-center">Status</th>
                                    <th class="px-4 py-3 font-bold">Otorisasi ACC</th>
                                    <th class="px-4 py-3 font-bold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($kasbonList as $kb)
                                <tr class="hover:bg-slate-50/70 transition-colors duration-150">
                                    <td class="px-4 py-3 font-mono font-bold text-blue-700 whitespace-nowrap">{{ $kb->no_kasbon }}</td>
                                    <td class="px-4 py-3 text-slate-600 whitespace-nowrap">{{ Carbon\Carbon::parse($kb->tanggal)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 min-w-[160px]">
                                        <div class="font-bold text-slate-900">{{ $kb->nama_penerima }}</div>
                                        <div class="text-[11px] text-slate-500 line-clamp-1">{{ $kb->keperluan }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-bold whitespace-nowrap {{ $kb->status === 'pending' ? 'text-rose-600' : ($kb->status === 'menunggu_acc' ? 'text-amber-700' : 'text-slate-700') }}">
                                        Rp {{ number_format($kb->nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        @if($kb->status === 'menunggu_acc')
                                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-50 text-amber-800 border border-amber-300 inline-flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Menunggu ACC
                                            </span>
                                        @elseif($kb->status === 'pending')
                                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200 inline-flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Kasbon Aktif
                                            </span>
                                        @elseif($kb->status === 'lunas')
                                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Lunas Kembali
                                            </span>
                                        @elseif($kb->status === 'masuk_bp')
                                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 inline-flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                                Masuk BP
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
                                                <span>{{ Str::limit($kb->approved_by, 20) }}</span>
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
                                                        class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-[10px] rounded-lg transition-all shadow-2xs flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span>ACC</span>
                                                </button>
                                                <button type="button" 
                                                        @click="tolakTarget = { id: {{ $kb->id }}, no: '{{ $kb->no_kasbon }}', nominal: {{ $kb->nominal }}, penerima: '{{ addslashes($kb->nama_penerima) }}' }; showTolakModal = true"
                                                        class="px-2 py-1 bg-white hover:bg-rose-50 active:scale-95 text-rose-700 border border-rose-300 font-semibold text-[10px] rounded-lg transition-all">
                                                    Tolak
                                                </button>
                                            </div>
                                        @elseif($kb->status === 'pending')
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button type="button" 
                                                        @click="selesaiTarget = { id: {{ $kb->id }}, no: '{{ $kb->no_kasbon }}', nominal: {{ $kb->nominal }}, penerima: '{{ addslashes($kb->nama_penerima) }}' }; showSelesaiModal = true"
                                                        class="px-3 py-1 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-[11px] rounded-lg transition-all shadow-2xs">
                                                    Selesaikan
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
                                        <div class="max-w-xs mx-auto space-y-2">
                                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <p class="font-medium text-xs text-slate-500">Tidak ada catatan kasbon pada status ini.</p>
                                        </div>
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
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Penyelesaian</label>
                        <select name="tipe_selesai" required class="w-full text-xs py-2.5 px-3.5 rounded-xl border border-slate-200/90 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-slate-900 font-medium transition-all">
                            <option value="lunas">1. Uang Fisik Dikembalikan Tunai (Lunas Kembali ke Meja Kasir)</option>
                            <option value="masuk_bp">2. Diterbitkan Nomor BP Anggaran Resmi (Masuk KO Anggaran)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Penyelesaian</label>
                        <input type="text" name="catatan" placeholder="Contoh: Sisa uang belanja dikembalikan / Register BP No. PR-2026-..." 
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
                        <span>Cetak Voucher Kasbon</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
