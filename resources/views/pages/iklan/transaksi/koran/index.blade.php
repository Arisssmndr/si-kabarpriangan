<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Konsisten (Standar Laporan dengan Logo Kantor) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Transaksi Iklan Koran (Harian Umum Kabar Priangan)
                    </h1>
                    <p class="text-xs font-normal text-slate-500 mt-0.5">
                        Manajemen faktur periklanan cetak, kontrak SPPI, dan gerbang kasir Cash Before Print (CBP)
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('pembayaran-iklan.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Kwitansi Kasir</span>
                </a>
                <a href="{{ route('transaksikoran.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs font-semibold rounded-lg shadow-xs hover:shadow transition cursor-pointer">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Transaksi Iklan Baru</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- TOP KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Omset Koran</span>
                <p class="text-lg font-extrabold text-slate-900 mt-1 font-tabular">
                    Rp {{ number_format($totalOmset ?? 0, 0, ',', '.') }}
                </p>
                <span class="text-[10px] text-slate-400 mt-0.5 block">Akumulasi seluruh faktur cetak</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Piutang Berjalan</span>
                <p class="text-lg font-extrabold text-rose-600 mt-1 font-tabular">
                    Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}
                </p>
                <span class="text-[10px] text-slate-400 mt-0.5 block">Kredit instansi & belum lunas</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">CBP Gate (Menunggu Kasir)</span>
                <p class="text-lg font-extrabold text-amber-600 mt-1 font-tabular">
                    {{ $antreanCBP ?? 0 }} Order
                </p>
                <span class="text-[10px] text-slate-400 mt-0.5 block">Terkunci belum boleh ke layout</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Faktur Tercatat</span>
                <p class="text-lg font-extrabold text-kp-blue-700 mt-1 font-tabular">
                    {{ $transaksikoran->total() }} Faktur
                </p>
                <span class="text-[10px] text-slate-400 mt-0.5 block">Baris, Kolom, Display & Adv</span>
            </div>
        </div>

        <!-- Toolbar Pencarian & Filter Komprehensif -->
        <div class="bg-white border border-slate-300 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('transaksikoran.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Pencarian Data
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="No faktur, nama pemasang, sales..."
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-black placeholder-gray-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kategori Form
                    </label>
                    <select name="kategori_form" class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-black focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="">Semua Form</option>
                        <option value="BARIS" {{ request('kategori_form') === 'BARIS' ? 'selected' : '' }}>Form 1 — Baris</option>
                        <option value="KOLOM" {{ request('kategori_form') === 'KOLOM' ? 'selected' : '' }}>Form 2 — Kolom</option>
                        <option value="DISPLAY" {{ request('kategori_form') === 'DISPLAY' ? 'selected' : '' }}>Form 2 — Display</option>
                        <option value="ADVERTORIAL" {{ request('kategori_form') === 'ADVERTORIAL' ? 'selected' : '' }}>Form 2 — Advertorial</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kanal Order
                    </label>
                    <select name="kanal_order" class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-black focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="">Semua Kanal</option>
                        <option value="FO" {{ request('kanal_order') === 'FO' ? 'selected' : '' }}>FO (Counter)</option>
                        <option value="AE" {{ request('kanal_order') === 'AE' ? 'selected' : '' }}>AE (Outdoor Sales)</option>
                        <option value="BIRO_IKLAN" {{ request('kanal_order') === 'BIRO_IKLAN' ? 'selected' : '' }}>Biro Iklan (Agensi)</option>
                        <option value="KORP" {{ request('kanal_order') === 'KORP' ? 'selected' : '' }}>KORP (Pemda/BUMN)</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Status Pembayaran
                    </label>
                    <select name="status" class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-black focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="">Semua Status</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="piutang" {{ request('status') === 'piutang' ? 'selected' : '' }}>Belum Lunas (Piutang)</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-semibold text-xs rounded-lg shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'status', 'kategori_form', 'kanal_order']))
                        <a href="{{ route('transaksikoran.index') }}" class="h-10 px-3 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-lg transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Transaksi Koran Terpadu -->
        <div class="bg-white border border-slate-300 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-4 bg-slate-50/90 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Transaksi Periklanan Koran</h2>
                    <p class="text-xs text-slate-500 mt-0.5 font-normal">Total {{ $transaksikoran->total() }} faktur tercatat</p>
                </div>
            </div>

            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-300 bg-slate-100 text-slate-800 uppercase tracking-wider text-[11px] whitespace-nowrap">
                            <th class="py-3.5 px-3.5 font-bold text-center w-12">No</th>
                            <th class="py-3.5 px-3.5 font-bold">Faktur & Tanggal</th>
                            <th class="py-3.5 px-3.5 font-bold">Kategori & Kanal</th>
                            <th class="py-3.5 px-3.5 font-bold">Pemasang / Instansi</th>
                            <th class="py-3.5 px-3.5 font-bold">Spesifikasi & Terbit</th>
                            <th class="py-3.5 px-3.5 font-bold text-right">Total Tagihan</th>
                            <th class="py-3.5 px-3.5 font-bold text-right">Piutang</th>
                            <th class="py-3.5 px-3.5 font-bold text-center">Status Order (CBP)</th>
                            <th class="py-3.5 px-3.5 font-bold text-center">SPPI Legal</th>
                            <th class="py-3.5 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 whitespace-nowrap">
                        @forelse($transaksikoran as $key => $t)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-3.5 text-center text-gray-500 font-normal">
                                    {{ $transaksikoran->firstItem() + $key }}
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-bold text-slate-900 font-mono">{{ $t->nofakturkoran }}</div>
                                    <div class="text-[11px] text-slate-500">{{ \Carbon\Carbon::parse($t->tanggal_transaksikoran)->format('d/m/Y') }}</div>
                                </td>
                                <td class="py-3 px-3.5">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold {{ $t->kategori_form === 'BARIS' ? 'bg-indigo-100 text-indigo-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $t->kategori_form ?: 'KORAN' }}
                                    </span>
                                    <div class="text-[10px] text-slate-500 mt-0.5 font-medium">
                                        Kanal: <strong>{{ $t->kanal_order ?: 'FO' }}</strong>
                                    </div>
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-bold text-slate-900">{{ $t->nama_pemasangkoran }}</div>
                                    @if($t->pelanggan)
                                        <div class="text-[10px] text-slate-500 font-medium mt-0.5">
                                            <span class="px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 border border-slate-200">{{ $t->pelanggan->jenis_klien }}</span>
                                            <span class="ml-1 font-mono">{{ $t->pelanggan->kode_pelanggan }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-medium text-slate-800">
                                        {{ $t->ukuran_iklan ?: ($t->jumlah_baris ? $t->jumlah_baris . ' Baris' : '-') }}
                                        <span class="text-slate-400 font-normal">({{ $t->warna_iklan }})</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5 font-mono">
                                        Terbit: {{ \Carbon\Carbon::parse($t->tanggal_muatkoran)->format('d/m/Y') }} ({{ $t->total_muatkoran }} edisi)
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-tabular">
                                    Rp {{ number_format($t->totaltagihan_transaksikoran, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-bold font-tabular {{ $t->piutang_transaksikoran > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    @if($t->piutang_transaksikoran > 0)
                                        Rp {{ number_format($t->piutang_transaksikoran, 0, ',', '.') }}
                                    @else
                                        <span class="text-emerald-700 font-semibold">LUNAS</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @php
                                        $badge = $t->status_badge;
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badge['bg'] }}">
                                        @if($t->status_order === 'Menunggu_Bayar')
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1 animate-pulse"></span>
                                        @endif
                                        {{ $badge['label'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @if($t->status_sppi === 'Sudah_Ditandatangani')
                                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            ✓ Sudah TTD
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                            Belum TTD
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-center" 
                                    x-data="{ open: false }" 
                                    :class="{ 'relative z-50': open }">
                                    <div class="relative inline-block text-left" @click.outside="open = false">
                                        <!-- Tombol Titik Tiga (Slim, Tajam & Kontras Tinggi) -->
                                        <button type="button" 
                                                @click="open = !open" 
                                                class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-700 hover:text-slate-950 hover:bg-slate-100 transition focus:outline-none cursor-pointer"
                                                :class="{ 'text-slate-950 bg-slate-100': open }"
                                                title="Pilihan Aksi">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="5" r="1.5" />
                                                <circle cx="12" cy="12" r="1.5" />
                                                <circle cx="12" cy="19" r="1.5" />
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu Aksi Lengkap -->
                                        <div x-cloak 
                                             x-show="open" 
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-52 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">
                                            
                                            <!-- 1. Cetak Faktur Koran -->
                                            <a href="{{ route('transaksikoran.cetak', $t->id) }}" 
                                               target="_blank"
                                               @click="open = false"
                                               class="group flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:text-kp-blue-700 hover:bg-kp-blue-50 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 group-hover:text-kp-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                                <span>Cetak Faktur Iklan</span>
                                            </a>

                                            <!-- 2. Cetak SPPI (Insertion Order Legal) -->
                                            <a href="{{ route('transaksikoran.sppi', $t->id) }}" 
                                               target="_blank"
                                               @click="open = false"
                                               class="group flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span>Cetak SPPI / Kontrak</span>
                                            </a>

                                            <!-- 3. Catat Pembayaran Kasir / Pelunasan Piutang -->
                                            <button type="button" 
                                                    onclick="openBayarModal('{{ $t->id }}', '{{ $t->nofakturkoran }}', '{{ $t->nama_pemasangkoran }}', '{{ $t->piutang_transaksikoran }}')"
                                                    @click="open = false"
                                                    class="w-full group flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-emerald-500 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>Catat Bayar / Kwitansi</span>
                                            </button>

                                            <!-- 4. Ubah Data Transaksi -->
                                            <button type="button" 
                                                    onclick="openEditModal(this)"
                                                    @click="open = false"
                                                    data-id="{{ $t->id }}"
                                                    data-nofaktur="{{ $t->nofakturkoran }}"
                                                    data-tanggal="{{ $t->tanggal_transaksikoran }}"
                                                    data-nama="{{ $t->nama_pemasangkoran }}"
                                                    data-alamat="{{ $t->alamat_pemasangkoran }}"
                                                    data-id_iklan="{{ $t->id_iklankoran }}"
                                                    data-halaman="{{ $t->halaman_iklan }}"
                                                    data-warna="{{ $t->warna_iklan }}"
                                                    data-ukuran="{{ $t->ukuran_iklan }}"
                                                    data-sales="{{ $t->sales_iklankoran }}"
                                                    data-tgl_muat="{{ $t->tanggal_muatkoran }}"
                                                    data-total_muat="{{ $t->total_muatkoran }}"
                                                    data-harga="{{ $t->harga_transaksikoran }}"
                                                    data-diskon="{{ $t->diskon_transaksikoran }}"
                                                    data-bayar="{{ $t->jumlahbayar_transaksikoran }}"
                                                    data-komisi="{{ $t->komisi_transaksikoran }}"
                                                    data-insentif="{{ $t->insentif_transaksikoran }}"
                                                    class="w-full group flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:text-kp-blue-700 hover:bg-kp-blue-50 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-slate-400 group-hover:text-kp-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Ubah Transaksi</span>
                                            </button>

                                            <!-- Separator -->
                                            <div class="h-px bg-slate-100 my-1"></div>

                                            <!-- 5. Hapus Transaksi -->
                                            <button type="button" 
                                                    onclick="deleteTransaksi('{{ $t->id }}', '{{ $t->nofakturkoran }}')"
                                                    @click="open = false"
                                                    class="w-full group flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-rose-400 group-hover:text-rose-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span>Hapus Transaksi</span>
                                            </button>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-500 font-normal">
                                    Belum ada data transaksi iklan koran. Klik "Transaksi Iklan Baru" untuk menambah data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transaksikoran->hasPages())
                <div class="p-4 border-t border-slate-200 bg-white">
                    {{ $transaksikoran->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Modal Catat Pembayaran Kasir / Pelunasan Piutang -->
    <div id="bayarModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs hidden p-4">
        <div class="bg-white border border-slate-200 rounded-xl shadow-xl w-full max-w-lg">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Catat Pembayaran Kasir & Terbitkan Kwitansi</span>
                </h3>
                <button type="button" onclick="closeBayarModal()" class="text-xs font-semibold text-gray-500 hover:text-black cursor-pointer">
                    ✕
                </button>
            </div>

            <form action="{{ route('pembayaran-iklan.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="transaksi_id" id="b_transaksi_id">
                <input type="hidden" name="media_type" value="koran">

                <div>
                    <span class="text-xs text-slate-500 block">No. Faktur:</span>
                    <strong id="b_label_faktur" class="font-mono text-sm text-slate-900">-</strong>
                </div>

                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex items-center justify-between text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Nama Pemasang</span>
                        <strong id="b_label_pemasang" class="text-slate-900">-</strong>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Sisa Piutang</span>
                        <strong id="b_label_piutang" class="text-rose-600 font-tabular font-bold">-</strong>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Nominal Bayar (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nominal_bayar" id="b_nominal_bayar" required onkeyup="formatRupiahInput(this)"
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Metode Bayar <span class="text-red-500">*</span>
                        </label>
                        <select name="metode_bayar" required
                                class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <option value="Tunai_Kasir">Tunai di Kasir</option>
                            <option value="Transfer_Bank">Transfer Bank</option>
                            <option value="Giro">Giro</option>
                            <option value="Cek">Cek</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                            Potongan PPh 23 (2%)
                        </label>
                        <input type="text" name="potongan_pph23" value="0" onkeyup="formatRupiahInput(this)"
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Nama Penyetor
                    </label>
                    <input type="text" name="nama_penyetor" id="b_nama_penyetor"
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                        Catatan / Referensi Transfer
                    </label>
                    <input type="text" name="catatan" placeholder="Contoh: Bukti transfer BCA an. CV Berkah"
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" onclick="closeBayarModal()" class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-xs transition cursor-pointer">
                        Simpan & Cetak Kwitansi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Transaksi Koran -->
    <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs hidden p-4">
        <div class="bg-white border border-slate-200 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50 sticky top-0 z-10">
                <h3 id="editModalTitle" class="text-sm font-semibold text-black">
                    Ubah Transaksi Iklan Koran
                </h3>
                <button type="button" onclick="closeEditModal()" class="text-xs font-medium text-gray-500 hover:text-black px-2 py-1 cursor-pointer">
                    Tutup
                </button>
            </div>

            <form id="editForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">No Faktur</label>
                        <input type="text" id="e_nofaktur" readonly class="w-full px-3 py-2 bg-gray-100 border border-slate-300 rounded-lg text-xs font-medium text-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Transaksi</label>
                        <input type="date" id="e_tanggal" name="tanggal_transaksikoran" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nama Pemasang</label>
                        <input type="text" id="e_nama" name="nama_pemasangkoran" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Sales Marketing</label>
                        <input type="text" id="e_sales" name="sales_iklankoran" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Alamat Pemasang</label>
                    <input type="text" id="e_alamat" name="alamat_pemasangkoran" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jenis Iklan</label>
                        <select id="e_id_iklan" name="id_iklankoran" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black">
                            @foreach($iklankoran as $ik)
                                <option value="{{ $ik->id }}">{{ $ik->jenis_iklankoran }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Halaman</label>
                        <select id="e_halaman" name="halaman_iklan" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black">
                            <option value="Halaman Dalam">Halaman Dalam</option>
                            <option value="Halaman 1 (Cover)">Halaman 1 (Cover)</option>
                            <option value="Halaman Belakang">Halaman Belakang</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Warna</label>
                        <select id="e_warna" name="warna_iklan" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black">
                            <option value="Hitam Putih (BW)">Hitam Putih (BW)</option>
                            <option value="Full Color (FC)">Full Color (FC)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Ukuran (mmk / Baris)</label>
                        <input type="text" id="e_ukuran" name="ukuran_iklan" placeholder="2 x 100 mmk" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Terbit</label>
                        <input type="date" id="e_tgl_muat" name="tanggal_muatkoran" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah Edisi (Qty)</label>
                        <input type="number" id="e_total_muat" name="total_muatkoran" min="1" required oninput="hitungEdit()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                    </div>
                </div>

                <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-3">
                    <p class="text-xs font-semibold text-black uppercase tracking-wider">Perhitungan Keuangan</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Harga Satuan (Rp)</label>
                            <input type="text" id="e_harga" name="harga_transaksikoran" required onkeyup="formatInput(this)" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-black font-tabular" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Diskon (Rp)</label>
                            <input type="text" id="e_diskon" name="diskon_transaksikoran" onkeyup="formatInput(this)" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-black font-tabular" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah Dibayar (Rp)</label>
                            <input type="text" id="e_bayar" name="jumlahbayar_transaksikoran" required onkeyup="formatInput(this)" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-black font-tabular" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 text-xs border-t border-slate-200">
                        <div>
                            <label class="block text-[11px] font-medium text-gray-600 mb-1">DPP</label>
                            <input type="text" id="e_dpp" readonly class="w-full px-2.5 py-1.5 bg-gray-100 border border-slate-300 rounded-md text-xs font-medium text-gray-700 font-tabular cursor-not-allowed" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-600 mb-1">PPN</label>
                            <input type="text" id="e_ppn" readonly class="w-full px-2.5 py-1.5 bg-gray-100 border border-slate-300 rounded-md text-xs font-medium text-gray-700 font-tabular cursor-not-allowed" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-600 mb-1">Komisi (Rp)</label>
                            <input type="text" id="e_komisi" name="komisi_transaksikoran" onkeyup="formatInput(this)" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-md text-xs font-medium text-black font-tabular" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-600 mb-1">Insentif (Rp)</label>
                            <input type="text" id="e_insentif" name="insentif_transaksikoran" onkeyup="formatInput(this)" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-md text-xs font-medium text-black font-tabular" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-2 text-xs border-t border-slate-200">
                        <div>
                            <span class="text-gray-600">Total Tagihan:</span>
                            <p id="e_label_total" class="font-semibold text-kp-blue-700 text-sm font-tabular">Rp 0</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Sisa Piutang:</span>
                            <p id="e_label_piutang" class="font-semibold text-red-600 text-sm font-tabular">Rp 0</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-xs font-medium text-gray-600 hover:bg-slate-100 rounded-lg transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-medium rounded-lg shadow-xs transition cursor-pointer">
                        Simpan Perubahan
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
        function formatInput(el) {
            const v = cleanNumber(el.value);
            el.value = formatRupiah(v);
            hitungEdit();
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

        function openEditModal(btn) {
            const d = btn.dataset;
            document.getElementById('editModalTitle').innerText = 'Ubah Transaksi ' + d.nofaktur;
            document.getElementById('editForm').action = "/transaksikoran/" + d.id;

            document.getElementById('e_nofaktur').value = d.nofaktur;
            document.getElementById('e_tanggal').value = d.tanggal;
            document.getElementById('e_nama').value = d.nama;
            document.getElementById('e_alamat').value = d.alamat;
            document.getElementById('e_sales').value = d.sales;
            document.getElementById('e_id_iklan').value = d.id_iklan;
            document.getElementById('e_halaman').value = d.halaman;
            document.getElementById('e_warna').value = d.warna;
            document.getElementById('e_ukuran').value = d.ukuran;
            document.getElementById('e_tgl_muat').value = d.tgl_muat;
            document.getElementById('e_total_muat').value = d.total_muat;

            document.getElementById('e_harga').value = formatRupiah(d.harga);
            document.getElementById('e_diskon').value = formatRupiah(d.diskon);
            document.getElementById('e_bayar').value = formatRupiah(d.bayar);
            document.getElementById('e_komisi').value = formatRupiah(d.komisi || 0);
            document.getElementById('e_insentif').value = formatRupiah(d.insentif || 0);

            hitungEdit();
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function hitungEdit() {
            const harga = cleanNumber(document.getElementById('e_harga').value);
            const diskon = cleanNumber(document.getElementById('e_diskon').value);
            const bayar = cleanNumber(document.getElementById('e_bayar').value);
            const qty = parseFloat(document.getElementById('e_total_muat').value) || 1;

            const omset = harga * qty;
            let total = omset - diskon;
            if (total < 0) total = 0;

            const dpp = total / 1.12; // Standar UU HPP PPN 12%
            const ppn = total - dpp;
            const piutang = Math.max(0, total - bayar);

            if (document.getElementById('e_dpp')) {
                document.getElementById('e_dpp').value = formatRupiah(Math.round(dpp));
            }
            if (document.getElementById('e_ppn')) {
                document.getElementById('e_ppn').value = formatRupiah(Math.round(ppn));
            }

            document.getElementById('e_label_total').innerText = 'Rp ' + formatRupiah(Math.round(total));
            const elPiutang = document.getElementById('e_label_piutang');
            elPiutang.innerText = 'Rp ' + formatRupiah(Math.round(piutang));
            if (piutang > 0) {
                elPiutang.className = 'font-semibold text-rose-600 text-sm font-tabular';
            } else {
                elPiutang.className = 'font-semibold text-emerald-600 text-sm font-tabular';
            }
        }

        async function deleteTransaksi(id, nofaktur) {
            if (window.AppAlert && AppAlert.confirmDelete) {
                AppAlert.confirmDelete({
                    title: 'Hapus Transaksi',
                    subtitle: 'Tindakan ini tidak dapat dibatalkan',
                    target: nofaktur,
                    confirmText: 'Ya, Hapus Transaksi'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const token = document.querySelector('meta[name="csrf-token"]').content;
                            await axios.post('/transaksikoran/' + id, {
                                _method: 'DELETE',
                                _token: token
                            });
                            AppAlert.success({
                                title: 'Berhasil Dihapus',
                                subtitle: 'Data transaksi telah dihapus',
                                message: 'Transaksi ' + nofaktur + ' berhasil dihapus.'
                            }).then(() => location.reload());
                        } catch (err) {
                            AppAlert.error({
                                title: 'Gagal Menghapus',
                                subtitle: 'Terjadi kesalahan sistem',
                                message: 'Gagal menghapus data transaksi.',
                                buttonText: 'Tutup'
                            });
                        }
                    }
                });
            } else if (confirm(`Apakah Anda yakin ingin menghapus transaksi ${nofaktur}?`)) {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]').content;
                    await axios.post('/transaksikoran/' + id, {
                        _method: 'DELETE',
                        _token: token
                    });
                    location.reload();
                } catch (err) {
                    alert('Gagal menghapus data transaksi.');
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
