<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Standar Selaras Iklan Online & Foto 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Daftar Booking & Pemuatan Koran
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekapitulasi faktur iklan cetak harian, status penayangan edisi, penagihan, dan komisi perantara.
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('iklan.rekap-pracetak.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>Rekap Pracetak</span>
                </a>
                <a href="{{ route('pembayaran-iklan.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Kwitansi Kasir</span>
                </a>
                <a href="{{ route('transaksikoran.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs hover:shadow transition cursor-pointer">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Transaksi Iklan Baru</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- KPI Ringkasan: Harmonis Palet 60-30-10 -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Pendapatan</span>
                <p class="text-xl font-bold text-kp-blue-700 mt-1 font-tabular">
                    Rp {{ number_format($totalOmset ?? 0, 0, ',', '.') }}
                </p>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Sisa Piutang</span>
                <p class="text-xl font-bold text-rose-600 mt-1 font-tabular">
                    Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}
                </p>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Menunggu Bayar</span>
                <p class="text-xl font-bold text-amber-600 mt-1 font-tabular">
                    {{ $antreanCBP ?? 0 }} Transaksi
                </p>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Transaksi</span>
                <p class="text-xl font-bold text-slate-900 mt-1 font-tabular">
                    {{ $transaksikoran->total() }} Faktur
                </p>
            </div>
        </div>

        <!-- Filter Pencarian Komprehensif -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
            <form method="GET" action="{{ route('transaksikoran.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">
                        Cari Transaksi
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="No. faktur, nama pemesan, sales..."
                           class="w-full h-10 px-3.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-gray-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">
                        Jenis Iklan
                    </label>
                    <select name="kategori_form" class="w-full h-10 px-3.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="">Semua Kategori</option>
                        <option value="BARIS" {{ request('kategori_form') === 'BARIS' ? 'selected' : '' }}>Iklan Baris</option>
                        <option value="KOLOM" {{ request('kategori_form') === 'KOLOM' ? 'selected' : '' }}>Iklan Kolom</option>
                        <option value="DISPLAY" {{ request('kategori_form') === 'DISPLAY' ? 'selected' : '' }}>Iklan Display</option>
                        <option value="ADVERTORIAL" {{ request('kategori_form') === 'ADVERTORIAL' ? 'selected' : '' }}>Advertorial</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">
                        Status Bayar
                    </label>
                    <select name="status" class="w-full h-10 px-3.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="">Semua Status</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="piutang" {{ request('status') === 'piutang' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-semibold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Filter
                    </button>
                    @if(request()->hasAny(['q', 'status', 'kategori_form']))
                        <a href="{{ route('transaksikoran.index') }}" class="h-10 px-3 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Transaksi Koran Terpadu (Clean Standard Kwitansi) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. Faktur</th>
                            <th class="py-3 px-3.5">Jenis &amp; Sales</th>
                            <th class="py-3 px-3.5">Nama Pemesan</th>
                            <th class="py-3 px-3.5">Ukuran &amp; Jadwal</th>
                            <th class="py-3 px-3.5 text-right w-36">Total Tagihan</th>
                            <th class="py-3 px-3.5 text-right w-36">Sisa Piutang</th>
                            <th class="py-3 px-3.5 text-center">Status Bayar</th>
                            <th class="py-3 px-3.5 text-center">Pracetak</th>
                            <th class="py-3 px-3.5 text-center w-12">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($transaksikoran as $key => $t)
                            <tr class="hover:bg-slate-50/80 transition {{ $t->status_order === 'Batal' ? 'bg-rose-50/60 opacity-80' : '' }}">
                                <td class="py-3 px-3.5 text-center text-slate-500 font-normal">
                                    {{ $transaksikoran->firstItem() + $key }}
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="font-mono font-semibold text-kp-blue-700 text-xs">
                                        <a href="{{ route('transaksikoran.show', $t->id) }}" class="hover:underline">
                                            {{ $t->nofakturkoran }}
                                        </a>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ \Carbon\Carbon::parse($t->tanggal_transaksikoran)->format('d/m/Y') }}</div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    @php
                                        $labelJenis = match($t->kategori_form) {
                                            'BARIS'       => 'Baris',
                                            'KOLOM'       => 'Kolom',
                                            'DISPLAY'     => 'Display',
                                            'ADVERTORIAL' => 'Advertorial',
                                            default       => ($t->kategori_form ?: 'Koran')
                                        };
                                        $catBadge = match($t->kategori_form) {
                                            'BARIS'       => 'bg-slate-100 text-slate-700 border-slate-200',
                                            'KOLOM'       => 'bg-sky-50 text-sky-700 border-sky-200',
                                            'DISPLAY'     => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'ADVERTORIAL' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                            default       => 'bg-slate-100 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-medium border {{ $catBadge }}">
                                        {{ $labelJenis }}
                                    </span>
                                    <div class="text-[11px] text-slate-600 mt-1 font-medium">
                                        AE: {{ $t->sales_iklankoran ?: '-' }}
                                    </div>
                                    @if($t->komisioner)
                                        <div class="text-[10px] text-slate-500 font-medium flex items-center gap-1 mt-0.5">
                                            <span class="text-slate-400">Perantara:</span>
                                            <span class="font-medium text-slate-700">{{ $t->komisioner }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="font-medium text-slate-900 text-xs">{{ $t->nama_pemasangkoran }}</div>
                                    @if($t->pelanggan)
                                        <div class="text-[10px] text-slate-500 flex items-center gap-1.5 mt-0.5 whitespace-nowrap">
                                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">{{ $t->pelanggan->jenis_klien }}</span>
                                            <span class="font-mono">{{ $t->pelanggan->kode_pelanggan }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="text-xs font-normal text-slate-800">
                                        <span>{{ $t->ukuran_iklan ?: ($t->jumlah_baris ? $t->jumlah_baris . ' Baris' : '-') }}</span>
                                        <span class="text-slate-400 font-normal">({{ $t->warna_iklan }})</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        Perdana: {{ \Carbon\Carbon::parse($t->tanggal_muatkoran)->format('d/m/Y') }} ({{ $t->total_muatkoran }}x)
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-xs">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-medium text-slate-900 tabular-nums">{{ number_format($t->totaltagihan_transaksikoran, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    @if($t->piutang_transaksikoran > 0)
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-xs">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-medium text-slate-900 tabular-nums">{{ number_format($t->piutang_transaksikoran, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <div class="text-right">
                                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200">LUNAS</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    @php
                                        $badge = $t->status_badge;
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium border {{ $badge['border'] ?? 'border-slate-200' }} {{ $badge['bg'] ?? 'bg-slate-50' }} {{ $badge['text'] ?? 'text-slate-700' }} whitespace-nowrap">
                                        @if($t->status_order === 'Menunggu_Bayar')
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                        @endif
                                        {{ $badge['label'] ?? $t->status_order }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    @if($t->status_order === 'Batal')
                                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                            BATAL
                                        </span>
                                    @elseif($t->cbp_approved)
                                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Siap Terbit
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                            Tahan CBP
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-center whitespace-nowrap" 
                                    x-data="{ open: false }" 
                                    :class="{ 'relative z-50': open }">
                                    <div class="relative inline-block text-left" @click.outside="open = false">
                                        <!-- Tombol Aksi Tiga Titik -->
                                        <button type="button" 
                                                @click="open = !open" 
                                                class="w-8 h-8 rounded-lg inline-flex items-center justify-center border border-slate-300 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition focus:outline-none cursor-pointer"
                                                :class="{ 'bg-slate-200 text-slate-900': open }"
                                                title="Pilihan Aksi">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="5" r="1.5" />
                                                <circle cx="12" cy="12" r="1.5" />
                                                <circle cx="12" cy="19" r="1.5" />
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu Aksi -->
                                        <div x-cloak 
                                             x-show="open" 
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-56 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">
                                            
                                            <!-- Detail Transaksi -->
                                            <a href="{{ route('transaksikoran.show', $t->id) }}" 
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail Transaksi</span>
                                            </a>

                                            <!-- Edit Transaksi (Hanya jika belum cut-off) -->
                                            @if($t->canEdit())
                                                <a href="{{ route('transaksikoran.edit', $t->id) }}" 
                                                   @click="open = false"
                                                   class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-kp-blue-700 rounded-lg transition">
                                                    <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    <span>Edit Transaksi</span>
                                                </a>
                                            @else
                                                <div class="flex items-center justify-between px-3 py-2 text-xs font-semibold text-slate-400 bg-slate-50 rounded-lg cursor-not-allowed select-none"
                                                     title="{{ $t->getCutoffLockReason() }}">
                                                    <div class="flex items-center gap-2.5">
                                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                        <span>Edit (Terkunci)</span>
                                                    </div>
                                                    <span class="text-[9px] font-mono text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded border border-amber-200">Cutoff</span>
                                                </div>
                                            @endif

                                            <!-- Lembar Kerja Pracetak (Job Ticket) -->
                                            <a href="{{ route('transaksikoran.lembar-kerja', $t->id) }}" 
                                               target="_blank"
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                <span>Lembar Kerja Layout</span>
                                            </a>

                                            <div class="h-px bg-slate-100 my-1"></div>

                                            <!-- Cetak Faktur Koran -->
                                            <a href="{{ route('transaksikoran.cetak', $t->id) }}" 
                                               target="_blank"
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                                <span>Cetak Faktur</span>
                                            </a>

                                            <!-- Cetak SPPI -->
                                            <a href="{{ route('transaksikoran.sppi', $t->id) }}" 
                                               target="_blank"
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span>Cetak SPPI</span>
                                            </a>

                                            <!-- Catat Pembayaran -->
                                            @if($t->piutang_transaksikoran > 0 && $t->status_order !== 'Batal')
                                                <button type="button" 
                                                        onclick="openBayarModal('{{ $t->id }}', '{{ $t->nofakturkoran }}', '{{ $t->nama_pemasangkoran }}', '{{ $t->piutang_transaksikoran }}')"
                                                        @click="open = false"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 rounded-lg transition text-left cursor-pointer">
                                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span>Bayar di Kasir</span>
                                                </button>
                                            @endif

                                            <div class="h-px bg-slate-100 my-1"></div>

                                            <!-- Batalkan Order Iklan (Standar Audit Pers) -->
                                            @if($t->status_order !== 'Batal')
                                                <button type="button" 
                                                        onclick="openBatalModal('{{ $t->id }}', '{{ $t->nofakturkoran }}')"
                                                        @click="open = false"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-lg transition text-left cursor-pointer">
                                                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                    <span>Batalkan Transaksi</span>
                                                </button>
                                            @endif

                                            <!-- Hapus Permanen -->
                                            <button type="button" 
                                                    onclick="deleteTransaksi('{{ $t->id }}', '{{ $t->nofakturkoran }}')"
                                                    @click="open = false"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span>Hapus Data</span>
                                            </button>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-600">Belum ada transaksi iklan koran yang cocok</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Klik tombol "Transaksi Iklan Baru" untuk membuat pesanan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transaksikoran->hasPages())
                <div class="px-5 py-3 border-t border-slate-200 bg-slate-50/80">
                    {{ $transaksikoran->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Modal Catat Pembayaran Kasir / Pelunasan Piutang -->
    <div id="bayarModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs hidden p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Catat Pembayaran Iklan</span>
                </h3>
                <button type="button" onclick="closeBayarModal()" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">
                    &times;
                </button>
            </div>

            <form action="{{ route('pembayaran-iklan.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <input type="hidden" name="transaksi_id" id="b_transaksi_id">
                <input type="hidden" name="media_type" value="koran">

                <div>
                    <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider block">No. Faktur:</span>
                    <strong id="b_label_faktur" class="font-mono text-base font-bold text-kp-blue-700">-</strong>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between text-xs">
                    <div>
                        <span class="text-slate-500 block text-[10px] uppercase font-semibold">Nama Pemasang</span>
                        <strong id="b_label_pemasang" class="text-slate-800 font-bold text-sm">-</strong>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-500 block text-[10px] uppercase font-semibold">Sisa Piutang</span>
                        <strong id="b_label_piutang" class="text-rose-600 font-tabular font-bold text-base">-</strong>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Nominal Bayar (Rp) <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" name="nominal_bayar" id="b_nominal_bayar" required onkeyup="formatRupiahInput(this)"
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Metode Bayar <span class="text-rose-600">*</span>
                        </label>
                        <select name="metode_bayar" required
                                class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <option value="Tunai_Kasir">Tunai Kasir</option>
                            <option value="Transfer_Bank">Transfer Bank</option>
                            <option value="Giro">Giro</option>
                            <option value="Cek">Cek</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Potongan PPh 23 (2%)
                        </label>
                        <input type="text" name="potongan_pph23" value="0" onkeyup="formatRupiahInput(this)"
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Nama Penyetor
                    </label>
                    <input type="text" name="nama_penyetor" id="b_nama_penyetor"
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Catatan / Referensi
                    </label>
                    <input type="text" name="catatan" placeholder="Contoh: Bukti transfer BCA an. CV Berkah"
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeBayarModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 border border-slate-300 rounded-lg transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                        Simpan &amp; Cetak Kwitansi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Pembatalan Transaksi (Standar Audit Trail Pers) -->
    <div id="batalModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs hidden p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-rose-100 flex items-center justify-between bg-rose-50/80">
                <h3 class="text-sm font-bold text-rose-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Batalkan Transaksi Iklan</span>
                </h3>
                <button type="button" onclick="closeBatalModal()" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">
                    &times;
                </button>
            </div>

            <form id="formBatal" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <p class="text-xs text-slate-600 font-normal">
                    Anda akan membatalkan transaksi faktur: <span id="labelFakturBatal" class="font-mono font-bold text-slate-900">-</span>.
                    Pemuatan iklan pada edisi terkait akan dihentikan dan dicatat dalam audit trail pembatalan.
                </p>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Alasan Pembatalan <span class="text-rose-600">*</span>
                    </label>
                    <textarea name="alasan_batal" rows="3" required placeholder="Contoh: Klien membatalkan event mendadak / Salah input materi / Order ditarik sales"
                              class="w-full p-2.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeBatalModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 border border-slate-300 rounded-lg transition cursor-pointer">
                        Tutup
                    </button>
                    <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                        Ya, Batalkan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function formatRupiah(num) {
            if (!num) return '0';
            return new Intl.NumberFormat('id-ID').format(num);
        }
        function cleanNumber(str) {
            if (!str) return 0;
            return parseFloat(str.toString().replace(/[^0-9]/g, '')) || 0;
        }
        function formatRupiahInput(el) {
            const v = cleanNumber(el.value);
            el.value = formatRupiah(v);
        }

        function openBayarModal(id, nofaktur, nama, piutang) {
            document.getElementById('b_transaksi_id').value = id;
            document.getElementById('b_label_faktur').innerText = nofaktur;
            document.getElementById('b_label_pemasang').innerText = nama;
            document.getElementById('b_nama_penyetor').value = nama;
            document.getElementById('b_label_piutang').innerText = 'Rp ' + formatRupiah(piutang);
            document.getElementById('b_nominal_bayar').value = formatRupiah(piutang);
            document.getElementById('bayarModal').classList.remove('hidden');
        }

        function closeBayarModal() {
            document.getElementById('bayarModal').classList.add('hidden');
        }

        function openBatalModal(id, nofaktur) {
            document.getElementById('labelFakturBatal').innerText = nofaktur;
            document.getElementById('formBatal').action = "/transaksikoran/" + id + "/batalkan";
            document.getElementById('batalModal').classList.remove('hidden');
        }

        function closeBatalModal() {
            document.getElementById('batalModal').classList.add('hidden');
        }

        async function deleteTransaksi(id, nofaktur) {
            if (confirm(`Apakah Anda yakin ingin menghapus data transaksi ${nofaktur}?`)) {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]').content;
                    const res = await fetch('/transaksikoran/' + id, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        }
                    });
                    if (res.ok) {
                        location.reload();
                    } else {
                        alert('Gagal menghapus data transaksi.');
                    }
                } catch (err) {
                    alert('Terjadi kesalahan jaringan.');
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
