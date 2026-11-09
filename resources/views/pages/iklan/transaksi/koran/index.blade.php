<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Transaksi Iklan Koran
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Pencatatan faktur iklan cetak harian, jadwal edisi muat, dan status pembayaran.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('transaksikoran.create') }}" 
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Transaksi Baru</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 bordier border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 bg-slate-100 border border-slate-300 text-slate-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Toolbar Pencarian & Filter -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('transaksikoran.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Pencarian Data
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik no faktur, nama pemasang, atau sales..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Jenis Iklan Koran
                    </label>
                    <select name="kategori_form" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="">Semua Kategori</option>
                        <option value="BARIS" {{ request('kategori_form') === 'BARIS' ? 'selected' : '' }}>Iklan Baris</option>
                        <option value="KOLOM" {{ request('kategori_form') === 'KOLOM' ? 'selected' : '' }}>Iklan Kolom</option>
                        <option value="DISPLAY" {{ request('kategori_form') === 'DISPLAY' ? 'selected' : '' }}>Iklan Display</option>
                        <option value="ADVERTORIAL" {{ request('kategori_form') === 'ADVERTORIAL' ? 'selected' : '' }}>Advertorial</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Status Pembayaran
                    </label>
                    <select name="status" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="">Semua Status</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="piutang" {{ request('status') === 'piutang' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'status', 'kategori_form']))
                        <a href="{{ route('transaksikoran.index') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Transaksi Koran (Kolom Terpisah & Bersih Selaras Priangan TV, Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. Faktur</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Pemasang</th>
                            <th class="py-3 px-3.5">Sales</th>
                            <th class="py-3 px-3.5">Jenis Iklan</th>
                            <th class="py-3 px-3.5">Ukuran</th>
                            <th class="py-3 px-3.5">Tgl Muat</th>
                            <th class="py-3 px-3.5 text-right w-32">Tagihan</th>
                            <th class="py-3 px-3.5 text-right w-32">Piutang</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center">Pracetak</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($transaksikoran as $key => $t)
                            @php
                                $labelJenis = match($t->kategori_form) {
                                    'BARIS'       => 'Iklan Baris',
                                    'KOLOM'       => 'Iklan Kolom',
                                    'DISPLAY'     => 'Iklan Display',
                                    'ADVERTORIAL' => 'Advertorial',
                                    default       => ($t->iklankoran?->nama_jenis ?: ($t->kategori_form ?: 'Iklan Koran'))
                                };

                                if ($t->status_order === 'Batal') {
                                    $statusBayarLabel = 'Batal';
                                    $statusBayarClass = 'bg-slate-100 text-slate-400 border-slate-200';
                                } elseif ($t->piutang_transaksikoran <= 0) {
                                    $statusBayarLabel = 'Lunas';
                                    $statusBayarClass = 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200';
                                } else {
                                    $statusBayarLabel = 'Piutang';
                                    $statusBayarClass = 'bg-slate-100 text-slate-700 border-slate-200';
                                }
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition {{ $t->status_order === 'Batal' ? 'bg-slate-50/60 opacity-70' : '' }}">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                    {{ $transaksikoran->firstItem() + $key }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    <a href="{{ route('transaksikoran.show', $t->id) }}" class="hover:underline">
                                        {{ $t->nofakturkoran }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-mono font-normal">
                                    {{ \Carbon\Carbon::parse($t->tanggal_transaksikoran)->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    {{ $t->nama_pemasangkoran }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                    {{ $t->salesAe?->nama_ae ?: ($t->sales_iklankoran ?: '-') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-700 font-normal">
                                    {{ $labelJenis }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                    {{ $t->ukuran_iklan ?: ($t->jumlah_baris ? $t->jumlah_baris . ' Baris' : '-') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-mono font-normal">
                                    {{ \Carbon\Carbon::parse($t->tanggal_muatkoran)->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($t->totaltagihan_transaksikoran, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    @if($t->piutang_transaksikoran > 0)
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-normal text-slate-700 tabular-nums">{{ number_format($t->piutang_transaksikoran, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <div class="text-right font-mono text-sm text-slate-400 font-normal">
                                            Rp 0
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border {{ $statusBayarClass }}">
                                        {{ $statusBayarLabel }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($t->status_order === 'Batal')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-400 border border-slate-200">
                                            Batal
                                        </span>
                                    @elseif($t->cbp_approved)
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Siap Terbit
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            Tahan CBP
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center" 
                                    x-data="{ open: false }" 
                                    :class="{ 'relative z-50': open }">
                                    <div class="relative inline-block text-left" @click.outside="open = false">
                                        <button type="button" 
                                                @click="open = !open" 
                                                class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none cursor-pointer"
                                                :class="{ 'text-slate-900 bg-slate-100': open }"
                                                title="Pilihan Aksi">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="5" r="1.5" />
                                                <circle cx="12" cy="12" r="1.5" />
                                                <circle cx="12" cy="19" r="1.5" />
                                            </svg>
                                        </button>

                                        <div x-cloak 
                                             x-show="open" 
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-52 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">
                                            
                                            <!-- 1. Detail Transaksi -->
                                            <a href="{{ route('transaksikoran.show', $t->id) }}" 
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail Transaksi</span>
                                            </a>

                                            <!-- 2. Ubah Data Order -->
                                            @if($t->canEdit())
                                                <a href="{{ route('transaksikoran.edit', $t->id) }}" 
                                                   @click="open = false"
                                                   class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    <span>Ubah Data Order</span>
                                                </a>
                                            @else
                                                <div class="flex items-center justify-between px-3 py-2 text-sm font-medium text-slate-400 bg-slate-50 rounded-lg cursor-not-allowed select-none"
                                                     title="{{ $t->getCutoffLockReason() }}">
                                                    <div class="flex items-center gap-2.5">
                                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                        <span>Terkunci Cutoff</span>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="h-px bg-slate-100 my-1"></div>

                                            <!-- 3. Cetak Faktur -->
                                            <a href="{{ route('transaksikoran.cetak', $t->id) }}" 
                                               target="_blank"
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                                <span>Cetak Faktur</span>
                                            </a>

                                            <!-- 4. Cetak SPPI -->
                                            <a href="{{ route('transaksikoran.sppi', $t->id) }}" 
                                               target="_blank"
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span>Cetak SPPI</span>
                                            </a>

                                            <div class="h-px bg-slate-100 my-1"></div>

                                            <!-- 5. Hapus Transaksi -->
                                            <button type="button" 
                                                    onclick="deleteTransaksi('{{ $t->id }}', '{{ $t->nofakturkoran }}')"
                                                    @click="open = false"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span>Hapus Transaksi</span>
                                            </button>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="py-12 text-center text-slate-500 font-normal">
                                    Belum ada data transaksi iklan koran. Klik "Transaksi Baru" untuk menambah data.
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
    <div id="bayarModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs hidden p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider block">No. Faktur</span>
                    <strong id="b_label_faktur" class="font-mono text-base font-bold text-kp-blue-700">-</strong>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between text-xs">
                    <div>
                        <span class="text-slate-500 block text-xs font-medium">Nama Pemasang</span>
                        <strong id="b_label_pemasang" class="text-slate-900 font-bold text-sm mt-0.5 block">-</strong>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-500 block text-xs font-medium">Sisa Piutang</span>
                        <strong id="b_label_piutang" class="text-slate-900 font-tabular font-bold text-base mt-0.5 block">-</strong>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Nominal Bayar (Rp) <span class="text-kp-blue-600">*</span>
                    </label>
                    <input type="text" name="nominal_bayar" id="b_nominal_bayar" required onkeyup="formatRupiahInput(this)"
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Metode Bayar <span class="text-kp-blue-600">*</span>
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
                    <button type="submit" class="px-5 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                        Simpan &amp; Cetak Kwitansi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Pembatalan Transaksi -->
    <div id="batalModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs hidden p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Batalkan Transaksi Iklan</span>
                </h3>
                <button type="button" onclick="closeBatalModal()" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">
                    &times;
                </button>
            </div>

            <form id="formBatal" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                <p class="text-xs text-slate-600 font-normal leading-relaxed">
                    Anda akan membatalkan transaksi faktur <span id="labelFakturBatal" class="font-mono font-bold text-slate-900">-</span>.
                    Pemuatan iklan pada edisi terkait akan dihentikan dan dicatat dalam laporan pembatalan.
                </p>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Alasan Pembatalan <span class="text-kp-blue-600">*</span>
                    </label>
                    <textarea name="alasan_batal" rows="3" required placeholder="Contoh: Klien membatalkan event mendadak / Salah input materi"
                              class="w-full p-2.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeBatalModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 border border-slate-300 rounded-lg transition cursor-pointer">
                        Tutup
                    </button>
                    <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
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
