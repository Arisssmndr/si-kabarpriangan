<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Konsisten (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Data Master Pemasang (Klien Iklan)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Database profil pelanggan, limit piutang, dan direktori instansi.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('pelanggan.create') }}" 
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah Pemasang Baru</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 rounded-xl text-sm font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- TOP KPI Summary Cards (60-30-10) -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Pemasang</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-tabular">
                    {{ number_format($stats['total'] ?? 0, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">Klien</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Terdaftar di master data</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Instansi &amp; Korporat</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-tabular">
                    {{ number_format($stats['instansi'] ?? 0, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">Lembaga</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Pemda, BUMN, Swasta &amp; Agensi</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Akun Kredit / Tempo</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-tabular">
                    {{ number_format($stats['kredit'] ?? 0, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">Klien</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Memiliki plafon batas kredit</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Akun Tunai (CBP)</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-tabular">
                    {{ number_format($stats['cbp'] ?? 0, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">Klien</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Cash Before Print (Wajib lunas)</span>
            </div>
        </div>

        <!-- Toolbar Pencarian & Filter (Selaras /transaksipriangan) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('pelanggan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Pencarian Data
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik nama instansi, kode pemasang, PIC, no telp, atau NPWP..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Klasifikasi Klien
                    </label>
                    <select name="jenis_klien" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="">Semua Jenis Klien</option>
                        <option value="Perorangan" {{ request('jenis_klien') === 'Perorangan' ? 'selected' : '' }}>Perorangan (Walk-In / CBP)</option>
                        <option value="Swasta" {{ request('jenis_klien') === 'Swasta' ? 'selected' : '' }}>Perusahaan Swasta</option>
                        <option value="Pemda" {{ request('jenis_klien') === 'Pemda' ? 'selected' : '' }}>Instansi Pemda / Dinas</option>
                        <option value="BUMN" {{ request('jenis_klien') === 'BUMN' ? 'selected' : '' }}>BUMN / BUMD</option>
                        <option value="Agensi" {{ request('jenis_klien') === 'Agensi' ? 'selected' : '' }}>Biro Iklan (Agensi)</option>
                    </select>
                </div>

                <div class="sm:col-span-3 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'jenis_klien']))
                        <a href="{{ route('pelanggan.index') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Data Master Pemasang (Selaras /transaksipriangan: Kolom Terpisah, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">Kode</th>
                            <th class="py-3 px-3.5">Nama Pemasang</th>
                            <th class="py-3 px-3.5">Alamat</th>
                            <th class="py-3 px-3.5">Klasifikasi</th>
                            <th class="py-3 px-3.5">Kontak PIC</th>
                            <th class="py-3 px-3.5">No. Telepon</th>
                            <th class="py-3 px-3.5">NPWP / NIK</th>
                            <th class="py-3 px-3.5 text-right w-36">Plafon Kredit</th>
                            <th class="py-3 px-3.5 text-center">Order</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($pelanggan as $key => $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                    {{ $pelanggan->firstItem() + $key }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    {{ $item->kode_pelanggan }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    {{ $item->nama_instansi }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal max-w-[220px] truncate" title="{{ $item->alamat }}">
                                    {{ $item->alamat ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $item->jenis_klien }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-700 font-normal">
                                    {{ $item->kontak_pic ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-slate-600 font-normal">
                                    {{ $item->no_telp ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-slate-600 font-normal">
                                    {{ $item->npwp_nik ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    @if($item->credit_limit > 0)
                                        <div class="flex justify-between items-center gap-2 max-w-[135px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($item->credit_limit, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <div class="text-right text-slate-500 font-normal text-xs">
                                            Tunai (CBP)
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center font-mono text-slate-600 font-normal">
                                    {{ $item->transaksi_koran_count ?? 0 }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($item->is_active)
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center relative" x-data="{ open: false }">
                                    <button type="button" @click="open = !open" @click.outside="open = false"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer"
                                            title="Menu Aksi">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                        </svg>
                                    </button>
                                    <div x-cloak x-show="open" x-transition.origin.top.right
                                         class="absolute right-4 mt-1 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-1.5 z-50 text-left">
                                        <a href="{{ route('pelanggan.edit', $item->id) }}"
                                           class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-kp-blue-700 transition">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Ubah Pemasang
                                        </a>
                                        <div class="my-1 border-t border-slate-100"></div>
                                        <a href="{{ route('transaksikoran.create', ['pelanggan_id' => $item->id]) }}"
                                           class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-kp-blue-700 transition">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Order Iklan Koran
                                        </a>
                                        <a href="{{ route('transaksionline.create', ['pelanggan_id' => $item->id]) }}"
                                           class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-kp-blue-700 transition">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Order Iklan Online
                                        </a>
                                        <a href="{{ route('transaksipriangan.create', ['pelanggan_id' => $item->id]) }}"
                                           class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-kp-blue-700 transition">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Order Iklan TV
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="py-12 text-center text-slate-500 font-normal">
                                    Belum ada data pemasang yang cocok. Klik "Tambah Pemasang Baru" untuk menambah data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pelanggan->hasPages())
                <div class="p-4 border-t border-slate-200 bg-white">
                    {{ $pelanggan->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
