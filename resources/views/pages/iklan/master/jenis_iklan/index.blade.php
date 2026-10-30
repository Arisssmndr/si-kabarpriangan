<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Konsisten (Standar Kabar Priangan) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Data Master Jenis &amp; Ukuran Iklan
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Master tarif dan ukuran iklan koran cetak, portal online, dan Priangan TV.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <button type="button" 
                        onclick="openAddModal()" 
                        class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah Jenis Iklan</span>
                </button>
            </div>
        </div>

        <!-- TOP KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Total Paket Master</span>
                <p class="text-2xl font-extrabold text-slate-900 mt-1 font-tabular">
                    {{ $stats['total'] ?? $jenisList->total() }} Paket
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Seluruh saluran media</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Koran Cetak</span>
                <p class="text-2xl font-extrabold text-sky-600 mt-1 font-tabular">
                    {{ $stats['koran'] ?? 0 }} Paket
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Baris, Kolom, Display &amp; Adv</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Media Online</span>
                <p class="text-2xl font-extrabold text-emerald-600 mt-1 font-tabular">
                    {{ $stats['online'] ?? 0 }} Paket
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Banner web &amp; advertorial digital</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Priangan TV</span>
                <p class="text-2xl font-extrabold text-purple-600 mt-1 font-tabular">
                    {{ $stats['tv'] ?? 0 }} Paket
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Running text, spot &amp; blocking</span>
            </div>
        </div>

        <!-- Toolbar Pencarian & Filter Komprehensif -->
        <div class="bg-white border border-slate-300 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('jenis-iklan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Pencarian Nama / Kode
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik kode paket, nama jenis iklan, atau keterangan..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Saluran Media
                    </label>
                    <select name="kategori_id" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="">Semua Saluran Media</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama }} ({{ $kat->kode }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Filter
                    </button>
                    @if(request()->hasAny(['q', 'kategori_id']))
                        <a href="{{ route('jenis-iklan.index') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Data Master Jenis Iklan Terpadu (Clean Standard Kwitansi) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12 font-medium">No</th>
                            <th class="py-3 px-3.5 font-medium">Kode</th>
                            <th class="py-3 px-3.5 font-medium">Saluran</th>
                            <th class="py-3 px-3.5 font-medium">Nama Paket</th>
                            <th class="py-3 px-3.5 font-medium">Format &amp; Warna</th>
                            <th class="py-3 px-3.5 font-medium">Ukuran Standar</th>
                            <th class="py-3 px-3.5 font-medium w-36">Tarif Dasar</th>
                            <th class="py-3 px-3.5 text-center w-20 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($jenisList as $key => $j)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-3.5 text-center text-slate-500 font-normal">
                                    {{ $jenisList->firstItem() + $key }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-base">
                                    {{ $j->kode_jenis }}
                                </td>
                                <td class="py-3 px-3.5">
                                    @php
                                        $mediaCode = strtoupper($j->kategoriMedia->kode ?? '');
                                        $badgeColor = match(true) {
                                            str_contains($mediaCode, 'KORAN') => 'bg-sky-50 text-sky-800 border-sky-200',
                                            str_contains($mediaCode, 'ONLINE') => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                            str_contains($mediaCode, 'TV') => 'bg-purple-50 text-purple-800 border-purple-200',
                                            default => 'bg-slate-100 text-slate-800 border-slate-200'
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold border px-2.5 py-0.5 {{ $badgeColor }}">
                                        {{ $j->kategoriMedia->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-bold text-slate-900 text-sm">{{ $j->nama_jenis }}</div>
                                    @if($j->keterangan)
                                        <div class="text-xs text-slate-500 font-normal truncate max-w-xs mt-0.5">{{ $j->keterangan }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @php
                                            $tipeLabel = match($j->tipe_dasar) {
                                                'BARIS'         => 'Iklan Baris',
                                                'KOLOM'         => 'Iklan Kolom',
                                                'DISPLAY'       => 'Iklan Display',
                                                'ADVERTORIAL'   => 'Advertorial',
                                                'BONUS'         => 'Layanan Masyarakat / Bonus',
                                                'KHUSUS'        => 'Iklan Display (Kuping)',
                                                'PORTAL_BERITA' => 'Portal Berita',
                                                'INSTAGRAM'     => 'Instagram',
                                                'TIKTOK'        => 'TikTok',
                                                'YOUTUBE'       => 'YouTube',
                                                'PODCAST'       => 'Podcast',
                                                'BANNER_WEB'    => 'Banner Web',
                                                'VIDEO_LIPSUS'  => 'Video Lipsus',
                                                default         => $j->tipe_dasar ?? 'UMUM'
                                            };
                                            $isKoran = str_contains(strtoupper($j->kategoriMedia->kode ?? ''), 'KRN') || str_contains(strtoupper($j->kategoriMedia->nama ?? ''), 'KORAN');
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $tipeLabel }}
                                        </span>
                                        @if($isKoran)
                                            @if(($j->mode_warna ?? 'BW') === 'FC')
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                    Full Color
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                                    Hitam Putih
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-3.5">
                                    @if(($j->tipe_dasar ?? '') === 'BARIS')
                                        <span class="text-xs text-slate-700 font-mono">Min. {{ $j->min_order ?? 2 }} Baris</span>
                                    @elseif($j->default_kolom && $j->default_tinggi_mm)
                                        <div class="font-mono text-xs font-medium text-slate-800">
                                            {{ $j->default_kolom }} Kol × {{ $j->default_tinggi_mm }} mm
                                        </div>
                                        <span class="text-xs text-slate-400 font-normal">{{ $j->is_fixed_size ? 'Ukuran Tetap' : 'Bisa Ubah Ukuran' }}</span>
                                    @else
                                        <span class="text-slate-400 font-normal">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[140px] font-mono text-sm font-bold">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-bold text-slate-900 tabular-nums">
                                            {{ number_format($j->tarif_dasar ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-slate-400 block font-normal mt-0.5">
                                        @if(($j->cara_hitung ?? '') === 'PER_BARIS')
                                            Per Baris
                                        @elseif(($j->cara_hitung ?? '') === 'PER_MMK')
                                            Per mmk
                                        @elseif(($j->cara_hitung ?? '') === 'FIXED_ZERO')
                                            Bonus / Free
                                        @else
                                            Paket Tetap
                                        @endif
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-center" 
                                    x-data="{ open: false }" 
                                    :class="{ 'relative z-50': open }">
                                    <div class="relative inline-block text-left" @click.outside="open = false">
                                        <!-- Tombol Titik Tiga (Kebab Menu) -->
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

                                        <!-- Dropdown Menu Pilihan Aksi -->
                                        <div x-cloak 
                                             x-show="open" 
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-40 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">
                                            
                                            <!-- Ubah Paket -->
                                            <button type="button" 
                                                    onclick="openEditModal({{ json_encode($j) }})" 
                                                    @click="open = false"
                                                    class="w-full group flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:text-kp-blue-700 hover:bg-kp-blue-50 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-slate-400 group-hover:text-kp-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Ubah Paket</span>
                                            </button>

                                            <!-- Separator -->
                                            <div class="h-px bg-slate-100 my-1"></div>

                                            <!-- Hapus Paket -->
                                            <button type="button" 
                                                    onclick="deleteJenis({{ $j->id }}, '{{ addslashes($j->nama_jenis) }}', '{{ $j->kode_jenis }}')" 
                                                    @click="open = false"
                                                    class="w-full group flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-rose-400 group-hover:text-rose-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span>Hapus Paket</span>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <rect x="3" y="3" width="18" height="18" rx="2" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9h18M9 21V9" />
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-600">Belum ada paket jenis iklan yang cocok</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Ubah kata kunci pencarian atau klik "Tambah Jenis Iklan" di atas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($jenisList->hasPages())
                <div class="px-5 py-3 border-t border-slate-200 bg-slate-50/80">
                    {{ $jenisList->links() }}
                </div>
            @endif
        </div>

    </div>
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        .no-scrollbar {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }
    </style>

    <!-- Modal Form (Tambah / Edit Paket Iklan) -->
    <div id="jenisModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div class="bg-white rounded-2xl max-w-xl w-full shadow-xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[92vh] overflow-hidden no-scrollbar">
            
            <!-- Modal Header (Bersih & Ramping) -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                <h3 id="modalTitle" class="text-base font-bold text-slate-900">Ubah Paket Iklan</h3>
                <button type="button" onclick="closeModal()" class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer" title="Tutup">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="jenisForm" method="POST" action="{{ route('jenis-iklan.store') }}" class="flex flex-col flex-1 min-h-0">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <!-- Body Content -->
                <div class="p-6 space-y-3.5 text-xs overflow-y-auto no-scrollbar [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden flex-1">

                    <!-- 1. SALURAN MEDIA & KODE PAKET -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
                        <div class="sm:col-span-7">
                            <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                Saluran Media <span class="text-rose-500">*</span>
                            </label>
                            <select name="kategori_media_id" 
                                    id="modal_kategori_media_id" 
                                    required 
                                    onchange="onKategoriChanged(this.value)"
                                    class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                <option value="" disabled selected>Pilih Saluran...</option>
                                @foreach($kategoriList as $kat)
                                    <option value="{{ $kat->id }}" data-prefix="{{ $kat->kode }}">
                                        {{ $kat->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-5">
                            <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                Kode Paket
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       name="kode_jenis" 
                                       id="modal_kode_jenis" 
                                       readonly 
                                       tabindex="-1"
                                       required 
                                       placeholder="Otomatis" 
                                       class="w-full h-11 px-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono font-bold text-slate-600 uppercase tracking-wider select-none pointer-events-none focus:outline-none" />
                                <div id="kode_loading" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="animate-spin h-3.5 w-3.5 text-kp-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. NAMA PRODUK / PAKET -->
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                            Nama Produk / Paket Iklan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="nama_jenis" 
                               id="modal_nama_jenis" 
                               required 
                               placeholder="Contoh: Feed Post / Carousel Instagram, Advertorial 1 Portal..." 
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>

                    <!-- 3. FORMAT IKLAN & WARNA -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div id="box_tipe_dasar" class="sm:col-span-1">
                            <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                Kategori Jenis / Format <span class="text-rose-500">*</span>
                            </label>
                            <select name="tipe_dasar" id="modal_tipe_dasar" required
                                    class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                <!-- Populated dynamically based on Saluran Media -->
                            </select>
                        </div>

                        <div id="box_mode_warna" class="sm:col-span-1">
                            <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                Warna Cetak <span class="text-rose-500">*</span>
                            </label>
                            <select name="mode_warna" id="modal_mode_warna"
                                    class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                <option value="BW">Hitam Putih (BW)</option>
                                <option value="FC">Full Color (FC)</option>
                            </select>
                        </div>
                    </div>

                    <!-- 4. METODE HITUNG & TARIF DASAR -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                Metode Hitung <span class="text-rose-500">*</span>
                            </label>
                            <select name="cara_hitung" id="modal_cara_hitung" required
                                    class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                <option value="PAKET_FIXED">Paket Tetap / Tayang</option>
                                <option value="PER_MMK">Per mmk</option>
                                <option value="PER_BARIS">Per Baris</option>
                                <option value="FIXED_ZERO">Gratis (Rp 0)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                                Tarif Dasar <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex rounded-xl border border-slate-300 focus-within:border-kp-blue-600 focus-within:ring-1 focus-within:ring-kp-blue-600 overflow-hidden bg-white shadow-2xs transition h-11">
                                <span class="inline-flex items-center px-3 bg-slate-50 border-r border-slate-200 text-xs text-slate-500 font-medium select-none pointer-events-none">Rp</span>
                                <input type="text" 
                                       name="tarif_dasar" 
                                       id="modal_tarif_dasar" 
                                       required 
                                       placeholder="0" 
                                       onkeyup="formatCurrencyModal(this)"
                                       class="flex-1 h-11 px-3.5 bg-transparent text-sm font-mono font-bold text-slate-900 border-none focus:outline-none focus:ring-0 format-rupiah" />
                            </div>
                        </div>
                    </div>

                    <!-- 5. UKURAN STANDAR (KORAN) -->
                    <div id="box_ukuran_koran" class="p-3 bg-slate-50/70 border border-slate-200 rounded-xl space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-700">
                                Ukuran Standar (Koran)
                            </span>
                            <span class="text-xs text-slate-400 font-normal">Khusus cetak</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">
                                    Kolom
                                </label>
                                <input type="number" name="default_kolom" id="modal_default_kolom" min="1" max="7" value="2"
                                       class="w-full h-8 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-mono font-normal text-slate-800 focus:outline-none focus:border-kp-blue-600 transition" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">
                                    Tinggi (mm)
                                </label>
                                <input type="number" name="default_tinggi_mm" id="modal_default_tinggi_mm" min="1" max="540" value="100"
                                       class="w-full h-8 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-mono font-normal text-slate-800 focus:outline-none focus:border-kp-blue-600 transition" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">
                                    Fleksibilitas
                                </label>
                                <select name="is_fixed_size" id="modal_is_fixed_size"
                                        class="w-full h-8 px-2 bg-white border border-slate-300 rounded-lg text-xs font-normal text-slate-800 focus:outline-none focus:border-kp-blue-600 transition">
                                    <option value="1">Ukuran Tetap</option>
                                    <option value="0">Bisa Disesuaikan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 6. KETERANGAN -->
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5 text-sm">
                            Keterangan
                        </label>
                        <textarea name="keterangan" 
                                  id="modal_keterangan" 
                                  rows="2" 
                                  placeholder="Catatan penempatan atau ketentuan materi (opsional)..."
                                  class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition resize-none"></textarea>
                    </div>

                </div>

                <!-- Modal Footer (Bersih & Simpel) -->
                <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5 shrink-0">
                    <button type="button" onclick="closeModal()" class="h-11 px-5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-xl text-sm font-semibold transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="h-11 px-6 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-xl text-sm font-bold transition cursor-pointer shadow-xs">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const FORMAT_OPTIONS = {
            'KRN': [
                { value: 'BARIS', label: 'Iklan Baris' },
                { value: 'KOLOM', label: 'Iklan Kolom' },
                { value: 'DISPLAY', label: 'Iklan Display' },
                { value: 'ADVERTORIAL', label: 'Iklan Advertorial' },
                { value: 'BONUS', label: 'Iklan Layanan Masyarakat / Bonus (Rp 0)' }
            ],
            'ONL': [
                { value: 'PORTAL_BERITA', label: 'Portal Berita' },
                { value: 'INSTAGRAM', label: 'Instagram' },
                { value: 'TIKTOK', label: 'TikTok' },
                { value: 'YOUTUBE', label: 'YouTube' },
                { value: 'PODCAST', label: 'Podcast' }
            ],
            'PTV': [
                { value: 'PODCAST', label: 'Podcast TV' },
                { value: 'INSTAGRAM', label: 'Instagram Priangan TV' },
                { value: 'TIKTOK', label: 'TikTok Priangan TV' },
                { value: 'BANNER_WEB', label: 'Banner Web' },
                { value: 'VIDEO_LIPSUS', label: 'Video Lipsus' }
            ]
        };

        async function onKategoriChanged(kategoriId, selectedTipe = null) {
            if (!kategoriId) {
                document.getElementById('modal_kode_jenis').value = '';
                return;
            }

            const select = document.getElementById('modal_kategori_media_id');
            const selectedOption = select.options[select.selectedIndex];
            let prefix = selectedOption ? (selectedOption.getAttribute('data-prefix') || 'IKL') : 'IKL';
            prefix = prefix.toUpperCase().trim();

            // Populate Format / Tipe Dasar dropdown dynamically
            const tipeSelect = document.getElementById('modal_tipe_dasar');
            tipeSelect.innerHTML = '';
            const opts = FORMAT_OPTIONS[prefix] || FORMAT_OPTIONS['KRN'];
            opts.forEach(o => {
                const optEl = document.createElement('option');
                optEl.value = o.value;
                optEl.textContent = o.label;
                if (selectedTipe && selectedTipe === o.value) {
                    optEl.selected = true;
                }
                tipeSelect.appendChild(optEl);
            });

            // Toggle Koran specific inputs
            const isKoran = prefix.includes('KRN') || prefix.includes('KORAN');
            const boxUkuran = document.getElementById('box_ukuran_koran');
            const boxWarna = document.getElementById('box_mode_warna');
            const caraHitungSelect = document.getElementById('modal_cara_hitung');

            if (isKoran) {
                if (boxUkuran) boxUkuran.classList.remove('hidden');
                if (boxWarna) boxWarna.classList.remove('hidden');
                caraHitungSelect.innerHTML = `
                    <option value="PAKET_FIXED">Paket Tetap</option>
                    <option value="PER_MMK">Per mmk</option>
                    <option value="PER_BARIS">Per Baris</option>
                    <option value="FIXED_ZERO">Gratis (Rp 0)</option>
                `;
            } else {
                if (boxUkuran) boxUkuran.classList.add('hidden');
                if (boxWarna) boxWarna.classList.add('hidden');
                document.getElementById('modal_mode_warna').value = 'FC';
                document.getElementById('modal_default_kolom').value = '1';
                document.getElementById('modal_default_tinggi_mm').value = '50';
                caraHitungSelect.innerHTML = `
                    <option value="PAKET_FIXED">Paket Tetap / Tayang</option>
                    <option value="FIXED_ZERO">Gratis (Rp 0)</option>
                `;
            }

            const isAddMode = document.getElementById('formMethod').value === 'POST';
            if (isAddMode) {
                const loading = document.getElementById('kode_loading');
                const kodeInput = document.getElementById('modal_kode_jenis');
                if (loading) loading.classList.remove('hidden');
                kodeInput.placeholder = 'Memuat kode...';

                try {
                    const res = await axios.get('/api/kategori-media/' + kategoriId + '/next-code');
                    if (res.data && res.data.code) {
                        kodeInput.value = res.data.code;
                    }
                } catch (e) {
                    kodeInput.value = prefix + '001';
                } finally {
                    if (loading) loading.classList.add('hidden');
                }
            }
        }

        async function openAddModal() {
            document.getElementById('modalTitle').innerText = 'Tambah Jenis / Produk Iklan';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('jenisForm').action = "{{ route('jenis-iklan.store') }}";

            const kategoriSelect = document.getElementById('modal_kategori_media_id');
            kategoriSelect.disabled = false;

            const activeFilter = "{{ request('kategori_id') }}";
            if (activeFilter && kategoriSelect.querySelector(`option[value="${activeFilter}"]`)) {
                kategoriSelect.value = activeFilter;
            } else {
                const firstOption = kategoriSelect.querySelector('option:not([disabled])');
                if (firstOption) {
                    kategoriSelect.value = firstOption.value;
                } else {
                    kategoriSelect.value = '';
                }
            }

            document.getElementById('modal_kode_jenis').value = '';
            document.getElementById('modal_nama_jenis').value = '';
            document.getElementById('modal_mode_warna').value = 'BW';
            document.getElementById('modal_cara_hitung').value = 'PAKET_FIXED';
            document.getElementById('modal_tarif_dasar').value = '';
            document.getElementById('modal_default_kolom').value = '2';
            document.getElementById('modal_default_tinggi_mm').value = '100';
            document.getElementById('modal_is_fixed_size').value = '1';
            document.getElementById('modal_keterangan').value = '';
            document.getElementById('jenisModal').classList.remove('hidden');

            if (kategoriSelect.value) {
                await onKategoriChanged(kategoriSelect.value);
            }
            document.getElementById('modal_nama_jenis').focus();
        }

        async function openEditModal(item) {
            document.getElementById('modalTitle').innerText = 'Ubah Jenis / Produk Iklan';
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('jenisForm').action = "/jenis-iklan/" + item.id;

            const kategoriSelect = document.getElementById('modal_kategori_media_id');
            kategoriSelect.value = item.kategori_media_id;
            document.getElementById('modal_kode_jenis').value = item.kode_jenis;
            
            await onKategoriChanged(item.kategori_media_id, item.tipe_dasar);

            document.getElementById('modal_nama_jenis').value = item.nama_jenis || '';
            document.getElementById('modal_mode_warna').value = item.mode_warna || 'FC';
            document.getElementById('modal_cara_hitung').value = item.cara_hitung || 'PAKET_FIXED';
            document.getElementById('modal_tarif_dasar').value = (item.tarif_dasar || 0).toLocaleString('id-ID');
            document.getElementById('modal_default_kolom').value = item.default_kolom || 1;
            document.getElementById('modal_default_tinggi_mm').value = item.default_tinggi_mm || 50;
            document.getElementById('modal_is_fixed_size').value = item.is_fixed_size ? '1' : '0';
            document.getElementById('modal_keterangan').value = item.keterangan || '';
            document.getElementById('jenisModal').classList.remove('hidden');
            document.getElementById('modal_nama_jenis').focus();
        }

        function formatCurrencyModal(input) {
            if (typeof window.formatRupiahInput === 'function') {
                window.formatRupiahInput(input);
            } else {
                let val = input.value.replace(/\D/g, '');
                input.value = val ? parseInt(val, 10).toLocaleString('id-ID') : '';
            }
        }

        function closeModal() {
            document.getElementById('jenisModal').classList.add('hidden');
        }

        async function deleteJenis(id, nama, kode) {
            if (typeof AppAlert !== 'undefined' && AppAlert.confirmDelete) {
                AppAlert.confirmDelete({
                    title: 'Hapus Jenis Iklan',
                    subtitle: 'Tindakan ini tidak dapat dibatalkan',
                    target: `${nama} (${kode})`,
                    confirmText: 'Ya, Hapus Paket'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        doDelete(id);
                    }
                });
            } else {
                if (confirm(`Hapus jenis iklan [${kode}] ${nama}?`)) {
                    doDelete(id);
                }
            }
        }

        async function doDelete(id) {
            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                const res = await axios.post('/jenis-iklan/' + id, {
                    _method: 'DELETE',
                    _token: token
                });
                if (typeof AppAlert !== 'undefined') {
                    AppAlert.success({
                        title: 'Berhasil Dihapus',
                        message: res.data.message || 'Jenis iklan berhasil dihapus.'
                    }).then(() => location.reload());
                } else {
                    alert('Jenis iklan berhasil dihapus.');
                    location.reload();
                }
            } catch (err) {
                if (typeof AppAlert !== 'undefined') {
                    AppAlert.error({
                        title: 'Gagal Menghapus',
                        subtitle: 'Operasi Gagal',
                        message: 'Terjadi kesalahan sistem saat menghapus data.',
                        buttonText: 'Tutup'
                    });
                } else {
                    alert('Terjadi kesalahan saat menghapus jenis iklan.');
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
