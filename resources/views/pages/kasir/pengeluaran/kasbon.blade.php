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

        <!-- Flash Message -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-semibold text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs font-semibold text-rose-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- Header Page -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Kasbon / Uang Muka Operasional Meja Kasir
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-amber-100 text-amber-800 border border-amber-300">
                            Petugas Kasir: {{ Auth::user()->name ?? 'Kasir' }}
                        </span>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-slate-100 text-slate-700 border border-slate-200">
                            Format: KB-YYYYMMXXXX
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Pencatatan uang muka untuk pengeluaran mendesak tanpa nomor BP anggaran. Wajib di-ACC Keuangan dan otomatis memotong saldo kas fisik harian.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if(session('cetak_kasbon'))
                    <button type="button" @click="showCetakModal = true" class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-xs rounded-xl border border-amber-300 transition shadow-2xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Slip Kasbon Terakhir</span>
                    </button>
                @endif
                <a href="{{ route('kasir.laporan.posisi-kas') }}" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl border border-slate-300 transition shadow-2xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Cek Rekonsiliasi Kas Fisik</span>
                </a>
            </div>
        </div>

        <!-- Banner Alur Kerja & Ketentuan Kasbon -->
        <div class="bg-amber-50/70 border border-amber-200/90 rounded-xl p-3.5 text-xs text-amber-900 leading-relaxed shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-start gap-2.5">
                <span class="p-1 rounded-md bg-amber-200 text-amber-800 shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div>
                    <span class="font-bold">SOP Kasbon Operasional:</span>
                    Digunakan untuk pengeluaran mendesak yang belum sempat masuk ke pos penganggaran (misal: belanja kebutuhan operasional mendesak). Uang tunai diserahkan kepada pemohon, dan posisi kasbon <b>otomatis menjadi faktor pengurang pada posisi kas fisik meja kasir harian</b>. Kasbon diselesaikan bila uang dikembalikan tunai (lunas) atau dialihkan menjadi BP Anggaran resmi.
                </div>
            </div>
            <div class="text-[11px] font-bold text-amber-800 bg-white px-2.5 py-1 rounded-lg border border-amber-200 shrink-0">
                Wajib ACC Keuangan
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Kasbon Gantung Aktif -->
            <div class="bg-white border-2 border-rose-300 rounded-xl p-4 shadow-xs bg-rose-50/20">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-rose-700 uppercase tracking-wider">Kasbon Gantung Aktif</span>
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                        {{ $countKasbonGantung }} Slip Aktif
                    </span>
                </div>
                <div class="mt-2 text-2xl font-black text-rose-600 font-mono">
                    Rp {{ number_format($totalKasbonGantung, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-rose-700/90 font-medium">
                    ⚡ Faktor pengurang uang fisik di meja kasir
                </div>
            </div>

            <!-- Menunggu ACC Keuangan -->
            <div class="bg-white border border-amber-300 rounded-xl p-4 shadow-xs bg-amber-50/20">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-amber-700 uppercase tracking-wider">Menunggu ACC Keuangan</span>
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                        {{ $countMenungguAcc }} Pengajuan
                    </span>
                </div>
                <div class="mt-2 text-2xl font-black text-amber-700 font-mono">
                    Rp {{ number_format($totalMenungguAcc, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-amber-700/80">
                    Menunggu otorisasi sebelum uang fisik diserahkan
                </div>
            </div>

            <!-- Saldo Kas Fisik Laci Kasir -->
            <div class="bg-white border border-emerald-300 rounded-xl p-4 shadow-xs bg-emerald-50/20">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Estimasi Kas Fisik Laci</span>
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Meja Kasir
                    </span>
                </div>
                <div class="mt-2 text-2xl font-black text-emerald-700 font-mono">
                    Rp {{ number_format($saldoFisikLaci, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-emerald-800/80">
                    Saldo Sistem (Rp {{ number_format($saldoTeoritisShift, 0, ',', '.') }}) - Kasbon Gantung
                </div>
            </div>

            <!-- Total Kasbon Bulan Ini -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Kasbon Bulan Ini</span>
                <div class="mt-2 text-2xl font-extrabold text-slate-900 font-mono">
                    Rp {{ number_format($totalKasbonBulanIni, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Akumulasi seluruh pengeluaran uang muka periode berjalan</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Input Kasbon -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs p-5">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Form Pengeluaran Kasbon</h3>
                        <p class="text-[11px] text-slate-400">Dicatat oleh Petugas Kasir</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('kasir.pengeluaran.kasbon.store') }}" class="space-y-3.5">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Penerima Uang Muka</label>
                        <input type="text" name="nama_penerima" x-model="form.namaPenerima" required placeholder="Contoh: Bu Sofi / Pak Yana" 
                               class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 text-slate-900 font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Keperluan Pengeluaran Darurat</label>
                        <input type="text" name="keperluan" x-model="form.keperluan" required placeholder="Contoh: Beli air galon kantor / belanja mendesak" 
                               class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 text-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Kasbon Fisik (Rp)</label>
                        <input type="number" name="nominal" x-model="form.nominal" required min="1000" step="500" 
                               class="w-full text-base font-mono font-bold py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 text-slate-900 bg-amber-50/30">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Persetujuan / ACC</label>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <label class="flex items-center gap-2 p-2 rounded-lg border cursor-pointer transition"
                                   :class="form.statusAcc === 'langsung' ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'border-slate-200 text-slate-600'">
                                <input type="radio" name="status_acc" value="langsung" x-model="form.statusAcc" class="text-emerald-600">
                                <span>Sudah Di-ACC (Uang Diserahkan)</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 rounded-lg border cursor-pointer transition"
                                   :class="form.statusAcc === 'menunggu' ? 'bg-amber-50 border-amber-300 text-amber-900 font-bold' : 'border-slate-200 text-slate-600'">
                                <input type="radio" name="status_acc" value="menunggu" x-model="form.statusAcc" class="text-amber-600">
                                <span>Menunggu ACC Keuangan</span>
                            </label>
                        </div>
                    </div>

                    <div x-show="form.statusAcc === 'langsung'">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pejabat Keuangan yang Menyetujui (ACC)</label>
                        <select name="approved_by" x-model="form.approvedBy" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 text-slate-900 font-medium">
                            <option value="Ibu Eli Yulianti (Asisten Manajer Keuangan)">Ibu Eli Yulianti (Asisten Manajer Keuangan)</option>
                            <option value="Moch. Al Nurdin (Pemimpin Perusahaan)">Moch. Al Nurdin (Pemimpin Perusahaan)</option>
                            <option value="Admin Keuangan">Admin Keuangan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan Tambahan (Opsional)</label>
                        <textarea name="keterangan" x-model="form.keterangan" rows="2" placeholder="Catatan bukti kasbon..." 
                                  class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 text-slate-900"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Simpan & Keluarkan Kasbon (KB)</span>
                    </button>
                </form>
            </div>

            <!-- Tabel Daftar Kasbon & Aksi ACC / Penyelesaian -->
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden flex flex-col">
                <div class="px-5 py-3.5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-slate-50/50">
                    <div>
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Daftar Monitoring Kasbon Meja Kasir
                        </h3>
                    </div>

                    <!-- Filter Status Segmented -->
                    <div class="flex items-center gap-1 text-xs flex-wrap">
                        <a href="{{ route('kasir.pengeluaran.kasbon', ['status' => 'all']) }}" 
                           class="px-2.5 py-1 rounded-md font-medium transition {{ ($statusFilter ?? 'all') === 'all' ? 'bg-white text-slate-900 font-bold shadow-2xs border border-slate-200' : 'text-slate-500 hover:text-slate-800' }}">
                            Semua
                        </a>
                        <a href="{{ route('kasir.pengeluaran.kasbon', ['status' => 'menunggu_acc']) }}" 
                           class="px-2.5 py-1 rounded-md font-medium transition {{ ($statusFilter ?? '') === 'menunggu_acc' ? 'bg-amber-100 text-amber-900 font-bold border border-amber-300' : 'text-slate-500 hover:text-slate-800' }}">
                            Menunggu ACC @if($countMenungguAcc > 0) <span class="ml-1 px-1 bg-amber-200 text-amber-900 rounded-full text-[9px]">{{ $countMenungguAcc }}</span> @endif
                        </a>
                        <a href="{{ route('kasir.pengeluaran.kasbon', ['status' => 'pending']) }}" 
                           class="px-2.5 py-1 rounded-md font-medium transition {{ ($statusFilter ?? '') === 'pending' ? 'bg-rose-50 text-rose-700 font-bold border border-rose-200' : 'text-slate-500 hover:text-slate-800' }}">
                            Gantung (Aktif)
                        </a>
                        <a href="{{ route('kasir.pengeluaran.kasbon', ['status' => 'lunas']) }}" 
                           class="px-2.5 py-1 rounded-md font-medium transition {{ ($statusFilter ?? '') === 'lunas' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200' : 'text-slate-500 hover:text-slate-800' }}">
                            Lunas
                        </a>
                        <a href="{{ route('kasir.pengeluaran.kasbon', ['status' => 'masuk_bp']) }}" 
                           class="px-2.5 py-1 rounded-md font-medium transition {{ ($statusFilter ?? '') === 'masuk_bp' ? 'bg-sky-50 text-sky-700 font-bold border border-sky-200' : 'text-slate-500 hover:text-slate-800' }}">
                            Masuk BP
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 font-semibold">No. Kasbon</th>
                                <th class="px-4 py-3 font-semibold">Tanggal</th>
                                <th class="px-4 py-3 font-semibold">Penerima & Keperluan</th>
                                <th class="px-4 py-3 font-semibold text-right">Nominal (Rp)</th>
                                <th class="px-4 py-3 font-semibold text-center">Status</th>
                                <th class="px-4 py-3 font-semibold">Otorisasi ACC</th>
                                <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($kasbonList as $kb)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $kb->no_kasbon }}</td>
                                <td class="px-4 py-3 text-slate-600 whitespace-nowrap">{{ Carbon\Carbon::parse($kb->tanggal)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900">{{ $kb->nama_penerima }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $kb->keperluan }}</div>
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold {{ $kb->status === 'pending' ? 'text-rose-600' : ($kb->status === 'menunggu_acc' ? 'text-amber-700' : 'text-slate-700') }}">
                                    Rp {{ number_format($kb->nominal, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @if($kb->status === 'menunggu_acc')
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-300 flex items-center justify-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Menunggu ACC
                                        </span>
                                    @elseif($kb->status === 'pending')
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                            Kasbon Aktif (Gantung)
                                        </span>
                                    @elseif($kb->status === 'lunas')
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Lunas / Kembali
                                        </span>
                                    @elseif($kb->status === 'masuk_bp')
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-sky-50 text-sky-700 border border-sky-200">
                                            Masuk BP Anggaran
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600 text-[11px]">
                                    @if($kb->approved_by)
                                        <span class="font-semibold text-emerald-700 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            {{ Str::limit($kb->approved_by, 22) }}
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
                                                    class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] rounded-lg transition shadow-2xs flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>ACC</span>
                                            </button>
                                            <button type="button" 
                                                    @click="tolakTarget = { id: {{ $kb->id }}, no: '{{ $kb->no_kasbon }}', nominal: {{ $kb->nominal }}, penerima: '{{ addslashes($kb->nama_penerima) }}' }; showTolakModal = true"
                                                    class="px-2 py-1 bg-white hover:bg-rose-50 text-rose-700 border border-rose-300 font-semibold text-[10px] rounded-lg transition">
                                                Tolak
                                            </button>
                                        </div>
                                    @elseif($kb->status === 'pending')
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" 
                                                    @click="selesaiTarget = { id: {{ $kb->id }}, no: '{{ $kb->no_kasbon }}', nominal: {{ $kb->nominal }}, penerima: '{{ addslashes($kb->nama_penerima) }}' }; showSelesaiModal = true"
                                                    class="px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-[11px] rounded-lg transition shadow-2xs">
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
                                <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                    Tidak ada catatan kasbon pada status ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($kasbonList->hasPages())
                <div class="p-3 border-t border-slate-100 bg-slate-50">
                    {{ $kasbonList->links() }}
                </div>
                @endif
            </div>
        </div>

        </div>

        <!-- Modal Persetujuan (ACC) Kasbon oleh Keuangan -->
        <div x-cloak x-show="showAccModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showAccModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-emerald-50/50">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <h4 class="text-sm font-bold text-slate-900">Otorisasi & ACC Kasbon Keuangan</h4>
                    </div>
                    <button type="button" @click="showAccModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <form :action="'{{ url('/kasir/pengeluaran/kasbon') }}/' + accTarget.id + '/acc'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1.5">
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
                        <div class="flex justify-between border-t border-slate-200 pt-1">
                            <span class="text-slate-600 font-bold">Nominal Kasbon:</span>
                            <span class="font-bold font-mono text-emerald-700 text-sm">Rp <span x-text="new Intl.NumberFormat('id-ID').format(accTarget.nominal)"></span></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pejabat Keuangan yang Menyetujui (ACC)</label>
                        <select name="approved_by" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 text-slate-900 font-medium">
                            <option value="Ibu Eli Yulianti (Asisten Manajer Keuangan)">Ibu Eli Yulianti (Asisten Manajer Keuangan)</option>
                            <option value="Moch. Al Nurdin (Pemimpin Perusahaan)">Moch. Al Nurdin (Pemimpin Perusahaan)</option>
                            <option value="{{ Auth::user()->name }} (Keuangan)">{{ Auth::user()->name }} (Keuangan)</option>
                        </select>
                        <span class="text-[10px] text-slate-400 mt-1 block">Setelah di-ACC, uang fisik resmi dikeluarkan dari meja kasir dan memotong saldo kas fisik.</span>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="showAccModal = false" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition shadow-2xs flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Setujui & ACC Kasbon</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Tolak Kasbon -->
        <div x-cloak x-show="showTolakModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showTolakModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-rose-50/50">
                    <h4 class="text-sm font-bold text-rose-900">Tolak Permohonan Kasbon</h4>
                    <button type="button" @click="showTolakModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <form :action="'{{ url('/kasir/pengeluaran/kasbon') }}/' + tolakTarget.id + '/tolak'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1">
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
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Penolakan</label>
                        <input type="text" name="alasan_tolak" required placeholder="Contoh: Belum ada urgensi / gunakan kas kecil rutin" 
                               class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-rose-500 text-slate-900">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="showTolakModal = false" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-lg transition shadow-2xs">
                            Konfirmasi Tolak Kasbon
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Selesaikan Kasbon -->
        <div x-cloak x-show="showSelesaiModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showSelesaiModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-amber-50/50">
                    <h4 class="text-sm font-bold text-slate-900">Selesaikan Kasbon Meja Kasir</h4>
                    <button type="button" @click="showSelesaiModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <form :action="'{{ url('/kasir/pengeluaran/kasbon') }}/' + selesaiTarget.id + '/selesai'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500">No. Kasbon:</span>
                            <span class="font-mono font-bold text-slate-900" x-text="selesaiTarget.no"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Penerima:</span>
                            <span class="font-bold text-slate-800" x-text="selesaiTarget.penerima"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Nominal:</span>
                            <span class="font-bold font-mono text-rose-600">Rp <span x-text="new Intl.NumberFormat('id-ID').format(selesaiTarget.nominal)"></span></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Metode Penyelesaian</label>
                        <select name="tipe_selesai" required class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 text-slate-900 font-medium">
                            <option value="lunas">1. Uang Fisik Dikembalikan Tunai (Lunas Kembali ke Meja Kasir)</option>
                            <option value="masuk_bp">2. Diterbitkan Nomor BP Anggaran Resmi (Masuk KO Anggaran)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Penyelesaian</label>
                        <input type="text" name="catatan" placeholder="Contoh: Sisa uang belanja dikembalikan / Register BP No. 042" 
                               class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 text-slate-900">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="showSelesaiModal = false" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg transition shadow-2xs">
                            Konfirmasi Selesai
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Cetak Voucher Kasbon -->
        @if(session('cetak_kasbon'))
        @php $kb = session('cetak_kasbon'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-100 overflow-hidden">
                <!-- Header Dokumen -->
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-slate-50/50">
                    <div>
                        <div class="text-base font-black tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500">PT. Priangan Berkah Media - Bagian Perkasiran</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 text-[10px] font-black rounded bg-amber-100 text-amber-900 uppercase tracking-widest border border-amber-300">
                            VOUCHER KASBON OPERASIONAL
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1">{{ $kb['no_kasbon'] }}</div>
                        <div class="text-[10px] text-slate-500">{{ $kb['tanggal'] }}</div>
                    </div>
                </div>

                <!-- Body Dokumen -->
                <div class="print-document-body flex-1 overflow-y-auto p-5 space-y-2.5 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Diberikan Kepada</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $kb['penerima'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Keperluan Uang Muka</span>
                        <span class="col-span-2 text-slate-800">: {{ $kb['keperluan'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Disetujui Oleh (ACC)</span>
                        <span class="col-span-2 font-bold text-emerald-800">: {{ $kb['approved_by'] }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-2 bg-amber-50 px-3 rounded-xl border border-amber-200/80 my-2">
                        <span class="font-bold text-amber-900">Nominal Uang Kasbon</span>
                        <span class="col-span-2 font-mono font-black text-base text-amber-900">: Rp {{ number_format($kb['nominal'], 0, ',', '.') }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-1.5 text-xs bg-slate-50 px-3 rounded-lg border border-slate-200">
                        <span class="text-slate-500 font-semibold">Terbilang Huruf</span>
                        <span class="col-span-2 font-semibold italic text-slate-900">: {{ $kb['terbilang'] }}</span>
                    </div>

                    <div class="p-2 bg-slate-50 rounded text-[10px] text-slate-500 italic">
                        * Lembar bukti uang muka operasional meja kasir. Saldo kasbon ini otomatis memotong uang fisik kasir sampai diselesaikan atau diterbitkan nomor BP anggaran resmi.
                    </div>

                    <!-- Tanda Tangan Cetak -->
                    <div class="pt-5 border-t border-slate-200 grid grid-cols-3 gap-2 text-center text-xs text-slate-600">
                        <div>
                            <div class="font-semibold text-slate-700">Penerima Kasbon</div>
                            <div class="h-12"></div>
                            <div class="font-bold text-slate-900 underline">({{ $kb['penerima'] }})</div>
                        </div>
                        <div>
                            <div class="font-semibold text-slate-700">Menyetujui (Keuangan)</div>
                            <div class="h-12"></div>
                            <div class="font-bold text-slate-900 underline">({{ Str::words($kb['approved_by'], 2, '') }})</div>
                        </div>
                        <div>
                            <div class="font-semibold text-slate-700">Kasir Pembayar</div>
                            <div class="h-12"></div>
                            <div class="font-bold text-slate-900 font-mono underline">({{ $kb['kasir'] }})</div>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Voucher Kasbon</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
