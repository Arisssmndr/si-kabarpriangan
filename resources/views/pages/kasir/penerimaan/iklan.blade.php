<x-app-layout>
    <div class="space-y-6" x-data="{ 
        showModal: false, 
        showCetakModal: {{ session('cetak_bkm') ? 'true' : 'false' }},
        bayarForm: { 
            type: '', 
            id: '', 
            faktur: '', 
            customer: '', 
            totalTagihan: 0,
            sisa: 0, 
            nominal: 0, 
            potonganPajak: 0,
            metode: 'tunai',
            bankTujuan: 'Bank BJB',
            noRef: '',
            petugasInkaso: '{{ Auth::user()->name }}',
            keterangan: ''
        },
        get sisaSetelahBayar() {
            let sisa = this.bayarForm.sisa - (Number(this.bayarForm.nominal) + Number(this.bayarForm.potonganPajak));
            return sisa < 0 ? 0 : sisa;
        }
    }">

        <!-- Konten Halaman Kasir (Disembunyikan saat modal cetak aktif) -->
        <div class="space-y-6" :class="{ 'print:hidden': showCetakModal }">

        <!-- Flash Message Alert -->
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

        <!-- Header Page Setoran Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Setoran & Pelunasan Faktur Iklan
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-teal-50 text-teal-700 border border-teal-200">
                            Badge: Faktur
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Penerimaan setoran kas & penerbitan Bukti Kas Masuk (BKM) dari piutang iklan tempo dengan integrasi komisi AE
                    </p>
                </div>
            </div>

            @if(session('cetak_bkm'))
                <button type="button" @click="showCetakModal = true" class="px-3.5 py-2 bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold text-xs rounded-xl border border-teal-200 transition shadow-2xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Ulang BKM Terakhir</span>
                </button>
            @endif
        </div>

        <!-- KPI Ringkasan Piutang & Setoran Iklan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Faktur</span>
                    <span class="w-7 h-7 rounded-lg bg-slate-50 text-slate-600 border border-slate-200 flex items-center justify-center text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                </div>
                <p class="text-xl font-bold text-slate-900 mt-2">{{ number_format($stats['total_faktur'] ?? 0) }}</p>
                <p class="text-[11px] text-slate-500 mt-0.5">
                    <span class="font-semibold text-rose-600">{{ $stats['faktur_piutang'] ?? 0 }} Belum Lunas</span> • <span class="font-semibold text-emerald-600">{{ $stats['faktur_lunas'] ?? 0 }} Lunas</span>
                </p>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Nilai Tagihan</span>
                    <span class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 border border-sky-200 flex items-center justify-center text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <p class="text-xl font-bold text-slate-900 mt-2">Rp {{ number_format($stats['total_tagihan'] ?? 0, 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Akumulasi nilai transaksi</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Sudah Dibayar</span>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <p class="text-xl font-bold text-emerald-600 mt-2">Rp {{ number_format($stats['total_dibayar'] ?? 0, 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Kas masuk terverifikasi</p>
            </div>

            <div class="bg-white border border-rose-200/80 rounded-xl p-4 shadow-xs bg-rose-50/20">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-rose-700 uppercase tracking-wider">Sisa Piutang Berjalan</span>
                    <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                </div>
                <p class="text-xl font-extrabold text-rose-600 mt-2">Rp {{ number_format($stats['total_piutang'] ?? 0, 0, ',', '.') }}</p>
                <p class="text-[11px] text-rose-600/80 mt-0.5">Menunggu pelunasan BKM kasir</p>
            </div>
        </div>

        <!-- Filter & Search Toolbar Komprehensif -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4" x-data="{ 
            periode: '{{ $periodeTipe ?? 'semua' }}'
        }">
            <form method="GET" action="{{ route('kasir.penerimaan.iklan') }}" class="space-y-4">
                <!-- Baris 1: Filter Status Piutang (Segmented Control / Radio) -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5 mr-1">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            Status Tagihan:
                        </span>
                        <div class="inline-flex rounded-lg p-1 bg-slate-100 border border-slate-200 text-xs">
                            <label class="cursor-pointer px-3 py-1.5 rounded-md font-medium transition {{ ($statusPiutang ?? 'belum_lunas') === 'belum_lunas' ? 'bg-white text-teal-700 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="status_piutang" value="belum_lunas" {{ ($statusPiutang ?? 'belum_lunas') === 'belum_lunas' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                                <span>Hanya yang Punya Piutang (Belum Lunas)</span>
                            </label>
                            <label class="cursor-pointer px-3 py-1.5 rounded-md font-medium transition {{ ($statusPiutang ?? '') === 'lunas' ? 'bg-white text-teal-700 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="status_piutang" value="lunas" {{ ($statusPiutang ?? '') === 'lunas' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                                <span>Sudah Lunas</span>
                            </label>
                            <label class="cursor-pointer px-3 py-1.5 rounded-md font-medium transition {{ ($statusPiutang ?? '') === 'semua' ? 'bg-white text-teal-700 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="status_piutang" value="semua" {{ ($statusPiutang ?? '') === 'semua' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                                <span>Semua Status</span>
                            </label>
                        </div>
                    </div>

                    <!-- Indikator Filter Aktif & Reset -->
                    @if(($statusPiutang ?? 'belum_lunas') !== 'belum_lunas' || ($periodeTipe ?? 'semua') !== 'semua' || (($saluranMedia ?? 'semua') !== 'semua' && ($saluranMedia ?? 'semua') !== 'all') || !empty($search))
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-lg font-medium flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Filter Aktif
                            </span>
                            <a href="{{ route('kasir.penerimaan.iklan') }}" class="text-xs text-rose-600 hover:text-rose-700 font-semibold hover:underline flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reset Filter
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Baris 2: Kontrol Filter Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <!-- Quick Search Field -->
                    <div class="sm:col-span-4">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Pencarian Cepat</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search ?? '' }}" 
                                   placeholder="No. Faktur, Klien, atau Tanggal Muat..." 
                                   class="w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-800 placeholder-slate-400">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    <!-- Saluran Media -->
                    <div class="sm:col-span-3">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Saluran Media</label>
                        <select name="saluran_media" class="w-full py-2 px-3 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-800 bg-white">
                            <option value="semua" {{ in_array(($saluranMedia ?? 'semua'), ['semua', 'all', '']) ? 'selected' : '' }}>Semua Saluran Media</option>
                            <option value="koran" {{ ($saluranMedia ?? '') === 'koran' ? 'selected' : '' }}>Koran Cetak</option>
                            <option value="online" {{ ($saluranMedia ?? '') === 'online' ? 'selected' : '' }}>Media Online</option>
                            <option value="tv" {{ ($saluranMedia ?? '') === 'tv' ? 'selected' : '' }}>Priangan TV</option>
                        </select>
                    </div>

                    <!-- Tipe Periode Waktu -->
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Periode Waktu</label>
                        <select name="periode_tipe" x-model="periode" class="w-full py-2 px-3 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-800 bg-white">
                            <option value="semua">Semua Waktu</option>
                            <option value="hari">Per Hari (Tanggal)</option>
                            <option value="bulan">Per Bulan</option>
                            <option value="tahun">Per Tahun</option>
                        </select>
                    </div>

                    <!-- Dynamic Input Tanggal / Bulan / Tahun + Tombol Terapkan -->
                    <div class="sm:col-span-3 flex items-center gap-2">
                        <!-- Per Hari: Input Tanggal Spesifik -->
                        <div x-show="periode === 'hari'" class="w-full">
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Pilih Tanggal</label>
                            <input type="date" name="tanggal" value="{{ $tanggalFilter ?? date('Y-m-d') }}" :disabled="periode !== 'hari'" class="w-full py-2 px-3 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-800">
                        </div>

                        <!-- Per Bulan: Pilih Bulan & Tahun -->
                        <div x-show="periode === 'bulan'" class="w-full grid grid-cols-2 gap-1.5">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Bulan</label>
                                <select name="bulan" :disabled="periode !== 'bulan'" class="w-full py-2 px-2 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-800 bg-white">
                                    @foreach([
                                        '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
                                        '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu',
                                        '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'
                                    ] as $blnNum => $blnLabel)
                                        <option value="{{ $blnNum }}" {{ (string)($bulanFilter ?? '') === (string)$blnNum || (int)($bulanFilter ?? 0) === (int)$blnNum ? 'selected' : '' }}>
                                            {{ $blnLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tahun</label>
                                <select name="tahun" :disabled="periode !== 'bulan'" class="w-full py-2 px-2 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-800 bg-white">
                                    @for($y = (int)date('Y') + 1; $y >= 2020; $y--)
                                        <option value="{{ $y }}" {{ (int)($tahunFilter ?? date('Y')) === $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- Per Tahun: Pilih Tahun Saja -->
                        <div x-show="periode === 'tahun'" class="w-full">
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Pilih Tahun</label>
                            <select name="tahun" :disabled="periode !== 'tahun'" class="w-full py-2 px-3 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-800 bg-white">
                                @for($y = (int)date('Y') + 1; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ (int)($tahunFilter ?? date('Y')) === $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Tombol Terapkan Filter -->
                        <div :class="periode === 'semua' ? 'w-full' : 'shrink-0'">
                            <label class="block text-[11px] font-semibold text-transparent mb-1 select-none">Aksi</label>
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-semibold text-xs py-2 px-4 rounded-lg transition shadow-2xs cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                <span>Terapkan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Faktur & Piutang Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                        Daftar Faktur Iklan & Status Piutang
                    </h3>
                </div>
                <span class="text-[11px] text-slate-500 font-medium">Total {{ $fakturList->count() }} Faktur Ditemukan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">No. Faktur</th>
                            <th class="px-4 py-3 font-semibold">Saluran Media</th>
                            <th class="px-4 py-3 font-semibold">Nama Pemasang / Klien</th>
                            <th class="px-4 py-3 font-semibold">Sales / AE</th>
                            <th class="px-4 py-3 font-semibold text-right">Total Tagihan</th>
                            <th class="px-4 py-3 font-semibold text-right">Sudah Dibayar</th>
                            <th class="px-4 py-3 font-semibold text-right">Sisa Piutang</th>
                            <th class="px-4 py-3 font-semibold text-center">Status Komisi</th>
                            <th class="px-4 py-3 font-semibold text-center">Aksi / Bayar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($fakturList as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Kolom 1: No. Faktur & Sub-teks Tanggal Muat -->
                            <td class="px-4 py-3 align-top">
                                <span class="font-bold text-slate-800 font-mono">{{ $item->no_faktur }}</span>
                                <span class="block text-[11px] text-slate-400 mt-0.5 font-medium">
                                    Muat: {{ \Carbon\Carbon::parse($item->tanggal_muat)->format('d/m/Y') }}
                                </span>
                            </td>

                            <!-- Kolom 2: Saluran Media -->
                            <td class="px-4 py-3 align-top whitespace-nowrap">
                                @if($item->type === 'koran')
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-sky-50 text-sky-700 border border-sky-200">
                                        Koran Cetak
                                    </span>
                                @elseif($item->type === 'online')
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Media Online
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-purple-50 text-purple-700 border border-purple-200">
                                        Priangan TV
                                    </span>
                                @endif
                            </td>

                            <!-- Kolom 3: Nama Pemasang / Klien & Kode Akun & Periode Tayang -->
                            <td class="px-4 py-3 align-top">
                                <span class="font-bold text-slate-900 block">{{ $item->nama_klien }}</span>
                                <span class="text-[11px] font-semibold text-slate-500 font-mono tracking-wide">{{ $item->kode_rekening ?? 'AG-01-xxx' }}</span>
                                @if(!empty($item->deskripsi_tayang))
                                    <span class="block text-[11px] text-slate-400 mt-0.5 font-medium">{{ $item->deskripsi_tayang }}</span>
                                @endif
                            </td>

                            <!-- Kolom 4: Sales / AE -->
                            <td class="px-4 py-3 align-top text-slate-600 whitespace-nowrap">
                                <span class="font-medium">{{ $item->sales }}</span>
                            </td>

                            <!-- Kolom 5: Total Tagihan -->
                            <td class="px-4 py-3 align-top text-right font-medium text-slate-700 whitespace-nowrap">
                                Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                            </td>

                            <!-- Kolom 6: Sudah Dibayar -->
                            <td class="px-4 py-3 align-top text-right font-bold text-emerald-600 whitespace-nowrap">
                                Rp {{ number_format($item->sudah_dibayar, 0, ',', '.') }}
                            </td>

                            <!-- Kolom 7: Sisa Piutang -->
                            <td class="px-4 py-3 align-top text-right whitespace-nowrap">
                                @if($item->sisa_piutang > 0)
                                    <span class="text-rose-600 font-bold">
                                        Rp {{ number_format($item->sisa_piutang, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-slate-400 font-medium">
                                        Rp 0
                                    </span>
                                @endif
                            </td>

                            <!-- Kolom 8: Status Komisi -->
                            <td class="px-4 py-3 align-top text-center whitespace-nowrap">
                                @if($item->siap_cair)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Siap Cair KH
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-medium rounded-full bg-slate-100 text-slate-500 border border-slate-200">
                                        Terkunci (Belum Lunas)
                                    </span>
                                @endif
                            </td>

                            <!-- Kolom 9: Aksi / Bayar -->
                            <td class="px-4 py-3 align-top text-center whitespace-nowrap">
                                @if($item->sisa_piutang > 0)
                                    <button type="button" 
                                            @click="bayarForm = { 
                                                type: '{{ $item->type }}', 
                                                id: {{ $item->id }}, 
                                                faktur: '{{ $item->no_faktur }}', 
                                                customer: '{{ addslashes($item->nama_klien) }}', 
                                                totalTagihan: {{ $item->total_tagihan }},
                                                sisa: {{ $item->sisa_piutang }}, 
                                                nominal: {{ $item->sisa_piutang }}, 
                                                potonganPajak: 0,
                                                metode: 'tunai',
                                                bankTujuan: 'Bank BJB',
                                                noRef: '',
                                                petugasInkaso: '{{ Auth::user()->name }}',
                                                keterangan: 'Pelunasan Iklan {{ $item->no_faktur }}'
                                            }; showModal = true"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white font-semibold text-[11px] rounded-lg transition shadow-2xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <span>Proses BKM</span>
                                    </button>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 cursor-not-allowed opacity-90">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Lunas</span>
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-5 py-8 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-1">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="font-medium text-slate-500">Tidak ada data faktur iklan yang sesuai dengan filter pencarian.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form Input Setoran Kas / Terbitkan BKM -->
        <div x-cloak x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showModal = false" class="bg-white rounded-2xl max-w-xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-100 overflow-hidden">
                <!-- Header (shrink-0) -->
                <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Terbitkan Bukti Kas Masuk (BKM)</h4>
                            <p class="text-[11px] text-slate-500">Pencatatan kas masuk & pelunasan faktur piutang iklan</p>
                        </div>
                    </div>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <form method="POST" action="{{ route('kasir.penerimaan.iklan.store') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="type" :value="bayarForm.type">
                    <input type="hidden" name="id" :value="bayarForm.id">

                    <!-- Scrollable Body (flex-1 overflow-y-auto) -->
                    <div class="flex-1 overflow-y-auto px-5 py-3 space-y-3">
                        <!-- Info Tagihan -->
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-slate-500">No. Faktur:</span>
                                <span class="font-mono font-bold text-slate-900" x-text="bayarForm.faktur"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Pemasang / Klien:</span>
                                <span class="font-bold text-slate-800" x-text="bayarForm.customer"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Sisa Piutang Saat Ini:</span>
                                <span class="font-bold text-rose-600">Rp <span x-text="new Intl.NumberFormat('id-ID').format(bayarForm.sisa)"></span></span>
                            </div>
                        </div>

                        <!-- Identitas Form Divisi Iklan -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 p-2.5 bg-slate-100/70 rounded-xl border border-slate-200 text-xs">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-0.5">Divisi</label>
                                <div class="py-1 px-2.5 bg-white rounded-lg border border-slate-300 font-bold text-slate-800 flex items-center justify-between text-xs">
                                    <span>Divisi Iklan</span>
                                    <span class="text-[9px] px-1 py-0.2 bg-teal-100 text-teal-800 rounded font-sans font-bold">KAS</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-0.5">Kode Perkiraan (COA)</label>
                                <select name="kode_perkiraan" class="w-full text-xs py-1 px-2 rounded-lg border border-slate-300 font-mono font-semibold text-slate-800 focus:ring-1 focus:ring-teal-500">
                                    <option value="1.00.04.01.01.01">1.00.04.01.01.01 (Piutang Iklan)</option>
                                    <option value="2.00.01.01.01.01">2.00.01.01.01.01 (Uang Muka/Titipan)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-0.5">No. Blanko Fisik (Opsional)</label>
                                <input type="text" name="no_blanko" placeholder="Contoh: BLK-01982" 
                                       class="w-full text-xs py-1 px-2.5 rounded-lg border border-slate-300 font-mono text-slate-800 focus:ring-1 focus:ring-teal-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <!-- Petugas Inkaso Dropdown -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Petugas Inkaso / Penerima</label>
                                <select name="petugas_inkaso" x-model="bayarForm.petugasInkaso" class="w-full text-xs py-1.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-800">
                                    @foreach($petugasInkasoList as $petugas)
                                        <option value="{{ $petugas->name }}">{{ $petugas->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Metode Pembayaran -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pembayaran</label>
                                <select name="metode_pembayaran" x-model="bayarForm.metode" class="w-full text-xs py-1.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-800">
                                    <option value="tunai">Tunai Kasir (Fisik Meja)</option>
                                    <option value="transfer">Transfer Bank BJB / Lainnya</option>
                                    <option value="qris">QRIS / E-Wallet Resmi</option>
                                </select>
                            </div>
                        </div>

                        <!-- Input Khusus Transfer -->
                        <div x-show="bayarForm.metode === 'transfer'" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 p-2.5 bg-teal-50/50 rounded-xl border border-teal-100">
                            <div>
                                <label class="block text-[11px] font-bold text-teal-900 mb-0.5">Bank Tujuan Rekening</label>
                                <select name="bank_tujuan" x-model="bayarForm.bankTujuan" class="w-full text-xs py-1 px-2.5 rounded-lg border border-teal-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-800">
                                    <option value="Bank BJB Cabang Tasikmalaya">Bank BJB (Giro: 0012-0034-5678)</option>
                                    <option value="Bank Mandiri Tasikmalaya">Bank Mandiri (Giro: 131-00-9876543-2)</option>
                                    <option value="Bank BCA Tasikmalaya">Bank BCA (Giro: 054-8899-771)</option>
                                    <option value="Bank BRI Tasikmalaya">Bank BRI (Giro: 0100-01-002345-30-1)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-teal-900 mb-0.5">No. Bukti / Referensi Transfer</label>
                                <input type="text" name="no_referensi_transfer" x-model="bayarForm.noRef" placeholder="Contoh: TRF-BJB-984128" 
                                       class="w-full text-xs py-1 px-2.5 rounded-lg border border-teal-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-800 font-mono">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <!-- Nominal Dibayar -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Setoran Tunai/Transfer (Rp)</label>
                                <input type="number" name="nominal_bayar" x-model="bayarForm.nominal" required min="1" 
                                       class="w-full text-sm font-mono font-bold py-1.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-900">
                            </div>

                            <!-- Potongan Pajak Dinas PPN/PPh -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Potongan Pajak Dinas PPN/PPh (Rp)</label>
                                <input type="number" name="potongan_pajak" x-model="bayarForm.potonganPajak" min="0" placeholder="0" 
                                       class="w-full text-sm font-mono font-bold py-1.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-900">
                                <span class="text-[10px] text-slate-400 mt-0.5 block">Sebagai pengurang sah piutang dinas (PPh 23 / PPN WAPU)</span>
                            </div>
                        </div>

                        <!-- Live Calculation Preview -->
                        <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs space-y-1">
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Sisa Piutang Setelah Transaksi Ini:</span>
                                <span class="font-bold font-mono text-sm" :class="sisaSetelahBayar === 0 ? 'text-emerald-600' : 'text-amber-600'">
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(sisaSetelahBayar)"></span>
                                    <span class="text-[10px] ml-1 font-semibold" x-text="sisaSetelahBayar === 0 ? '(LUNAS - Siap Cair Komisi)' : '(Sisa Piutang)'"></span>
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan Tambahan (Opsional)</label>
                            <input type="text" name="keterangan" x-model="bayarForm.keterangan" placeholder="Catatan bukti kas masuk..." 
                                   class="w-full text-xs py-1.5 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-900">
                        </div>
                    </div>

                    <!-- Footer Action Buttons (shrink-0) -->
                    <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-xs rounded-lg transition shadow-2xs">
                            Simpan & Terbitkan BKM
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <!-- Modal Cetak Kuitansi / Bukti Kas Masuk (BKM) Otomatis -->
        @if(session('cetak_bkm'))
        @php $bkm = session('cetak_bkm'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-100 overflow-hidden">
                <!-- Header Kuitansi -->
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-slate-50/50">
                    <div>
                        <div class="text-base font-black tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500">PT. Priangan Berkah Media - Bagian Perkasiran</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 text-[10px] font-black rounded bg-teal-100 text-teal-800 uppercase tracking-widest border border-teal-300">
                            BUKTI KAS MASUK (BKM)
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1">{{ $bkm['no_bkm'] }}</div>
                        <div class="text-[10px] text-slate-500">{{ $bkm['tanggal'] }}</div>
                    </div>
                </div>

                <!-- Body Kuitansi (Scrollable) -->
                <div class="print-document-body flex-1 overflow-y-auto p-5 space-y-2.5 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Telah Diterima Dari</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $bkm['customer'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Nomor Faktur / Ref</span>
                        <span class="col-span-2 font-mono font-bold text-slate-900">: {{ $bkm['faktur'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Metode Bayar</span>
                        <span class="col-span-2 font-semibold text-slate-800">: {{ $bkm['metode'] }} {{ $bkm['bank'] !== '-' ? '(' . $bkm['bank'] . ')' : '' }}</span>
                    </div>
                    @if($bkm['no_ref'] !== '-')
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">No. Bukti Transfer</span>
                        <span class="col-span-2 font-mono text-slate-800">: {{ $bkm['no_ref'] }}</span>
                    </div>
                    @endif
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Petugas Inkaso / Kolektor</span>
                        <span class="col-span-2 font-semibold text-slate-800">: {{ $bkm['penagih'] }}</span>
                    </div>
                    @if($bkm['pajak'] > 0)
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Potongan Pajak Dinas</span>
                        <span class="col-span-2 font-mono text-slate-800">: Rp {{ number_format($bkm['pajak'], 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="grid grid-cols-3 py-1.5 bg-teal-50/60 px-2 rounded-lg">
                        <span class="font-bold text-teal-900">Jumlah Diterima (Rp)</span>
                        <span class="col-span-2 font-mono font-black text-sm text-teal-800">: Rp {{ $bkm['terbilang'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1">
                        <span class="text-slate-500">Status Pembayaran</span>
                        <span class="col-span-2 font-bold {{ $bkm['status_bayar'] === 'LUNAS' ? 'text-emerald-600' : 'text-amber-600' }}">
                            : {{ $bkm['status_bayar'] }} 
                            @if($bkm['sisa_piutang'] > 0)
                                (Sisa: Rp {{ number_format($bkm['sisa_piutang'], 0, ',', '.') }})
                            @endif
                        </span>
                    </div>

                    <!-- Tanda Tangan Cetak -->
                    <div class="grid grid-cols-2 pt-4 border-t border-slate-200 text-center text-[11px] text-slate-600">
                        <div>
                            <div>Penyetor / Klien</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900">({{ $bkm['customer'] }})</div>
                        </div>
                        <div>
                            <div>Kasir Penerima</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900 font-mono">({{ $bkm['kasir'] }})</div>
                        </div>
                    </div>
                </div>

                <!-- Action Button Print & Close (Pinned Bottom) -->
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0 print:hidden">
                    <button type="button" @click="showCetakModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Dokumen BKM</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
