<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Konsisten (Standar Laporan dengan Logo Kantor) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Master Kategori Media
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Pengelolaan unit saluran media iklan (Koran Cetak, Digital, Priangan TV).
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
                    <span>Tambah Kategori</span>
                </button>
            </div>
        </div>

        <!-- Toolbar Pencarian & Filter Terpadu (Modern, Rapi & Proporsional) -->
        <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-3.5 shadow-xs">
            <form method="GET" action="{{ route('kategori-media.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2.5">
                
                <!-- Kolom Input Pencarian -->
                <div class="relative flex-1 min-w-[220px]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Cari nama, kode kategori, atau deskripsi..."
                           class="w-full h-11 pl-9 pr-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <!-- Filter Ketersediaan Paket -->
                    <div class="flex items-center gap-1.5">
                        <label class="text-xs font-medium text-slate-500 whitespace-nowrap">Filter:</label>
                        <select name="filter" onchange="this.form.submit()" 
                                class="h-11 px-3.5 pr-8 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 transition cursor-pointer shadow-2xs">
                            <option value="">Semua Saluran</option>
                            <option value="with_paket" {{ request('filter') === 'with_paket' ? 'selected' : '' }}>Ada Paket Iklan</option>
                            <option value="without_paket" {{ request('filter') === 'without_paket' ? 'selected' : '' }}>Belum Ada Paket</option>
                        </select>
                    </div>

                    <!-- Urutan / Sorting -->
                    <div class="flex items-center gap-1.5">
                        <label class="text-xs font-medium text-slate-500 whitespace-nowrap">Urutan:</label>
                        <select name="sort" onchange="this.form.submit()" 
                                class="h-11 px-3.5 pr-8 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 transition cursor-pointer shadow-2xs">
                            <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="nama_asc" {{ request('sort') === 'nama_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
                            <option value="kode_asc" {{ request('sort') === 'kode_asc' ? 'selected' : '' }}>Kode (A - Z)</option>
                            <option value="paket_desc" {{ request('sort') === 'paket_desc' ? 'selected' : '' }}>Paket Terbanyak</option>
                        </select>
                    </div>

                    <!-- Tombol Cari & Reset -->
                    <button type="submit" class="h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl transition cursor-pointer shadow-xs flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Cari</span>
                    </button>

                    @if(request('q') || request('filter') || (request('sort') && request('sort') !== 'newest'))
                        <a href="{{ route('kategori-media.index') }}" 
                           class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Data Kategori Media -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">

            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 text-xs whitespace-nowrap">
                            <th class="py-3 px-4 font-semibold text-center w-12">No</th>
                            <th class="py-3 px-4 font-semibold">Kode</th>
                            <th class="py-3 px-4 font-semibold">Nama Kategori Media</th>
                            <th class="py-3 px-4 font-semibold">Deskripsi</th>
                            <th class="py-3 px-4 font-semibold text-center">Jumlah Paket</th>
                            <th class="py-3 px-4 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($kategoriList as $key => $kat)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-4 text-center text-slate-900 font-normal">
                                    {{ $kategoriList->firstItem() + $key }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono font-medium bg-slate-100 text-slate-900 border border-slate-200">
                                        {{ $kat->kode }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-900 font-semibold">
                                    {{ $kat->nama }}
                                </td>
                                <td class="py-3 px-4 text-slate-900 font-normal max-w-xs truncate">
                                    {{ $kat->deskripsi ?: '-' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a href="{{ route('jenis-iklan.index', ['kategori_id' => $kat->id]) }}" 
                                       class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-900 border border-slate-200 transition"
                                       title="Lihat paket iklan kategori ini">
                                        <span>{{ $kat->jenis_iklan_count }} Paket</span>
                                        &rarr;
                                    </a>
                                </td>
                                <td class="py-3 px-4 text-center" 
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

                                        <!-- Dropdown Menu 3 Tombol Aksi Kategori Media -->
                                        <div x-cloak 
                                             x-show="open" 
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-44 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">
                                            
                                            <!-- 1. Lihat Daftar Paket -->
                                            <a href="{{ route('jenis-iklan.index', ['kategori_id' => $kat->id]) }}" 
                                               @click="open = false"
                                               class="group flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:text-kp-blue-700 hover:bg-kp-blue-50 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 group-hover:text-kp-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                                <span>Lihat Paket ({{ $kat->jenis_iklan_count }})</span>
                                            </a>

                                            <!-- 2. Ubah Data Kategori -->
                                            <button type="button" 
                                                    onclick="openEditModal({{ json_encode($kat) }})" 
                                                    @click="open = false"
                                                    class="w-full group flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:text-kp-blue-700 hover:bg-kp-blue-50 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-slate-400 group-hover:text-kp-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Ubah Kategori</span>
                                            </button>

                                            <!-- Separator -->
                                            <div class="h-px bg-slate-100 my-1"></div>

                                            <!-- 3. Hapus Kategori -->
                                            <button type="button" 
                                                    onclick="deleteKategori({{ $kat->id }}, '{{ $kat->nama }}', {{ $kat->jenis_iklan_count }})" 
                                                    @click="open = false"
                                                    class="w-full group flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-rose-400 group-hover:text-rose-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span>Hapus Kategori</span>
                                            </button>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500 text-xs">
                                    Belum ada data kategori media. Klik "Tambah Kategori" untuk membuat baru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($kategoriList->hasPages())
                <div class="px-5 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $kategoriList->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Form (Tambah / Edit Kategori Media) -->
    <div id="kategoriModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
                <h3 id="modalTitle" class="text-sm font-semibold text-slate-900">Tambah Kategori Media</h3>
                <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600 text-lg leading-none cursor-pointer">&times;</button>
            </div>

            <form id="kategoriForm" method="POST" action="{{ route('kategori-media.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">
                        Kode Kategori (Prefix) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="kode" 
                           id="modal_kode" 
                           required 
                           maxlength="10" 
                           placeholder="Contoh: KRN, ONL, PTV, TTK" 
                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 uppercase font-mono placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">
                        Nama Kategori Media <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="nama" 
                           id="modal_nama" 
                           required 
                           placeholder="Contoh: Media Online, Priangan TV" 
                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">
                        Deskripsi
                    </label>
                    <textarea name="deskripsi" 
                              id="modal_deskripsi" 
                              rows="3" 
                              placeholder="Keterangan singkat saluran media..."
                              class="w-full p-3 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition border border-slate-200 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-medium text-xs rounded-lg transition cursor-pointer shadow-xs">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openAddModal() {
            document.getElementById('modalTitle').innerText = 'Tambah Kategori Media';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('kategoriForm').action = "{{ route('kategori-media.store') }}";
            document.getElementById('modal_kode').value = '';
            document.getElementById('modal_kode').readOnly = false;
            document.getElementById('modal_nama').value = '';
            document.getElementById('modal_deskripsi').value = '';
            document.getElementById('kategoriModal').classList.remove('hidden');
            document.getElementById('modal_kode').focus();
        }

        function openEditModal(item) {
            document.getElementById('modalTitle').innerText = 'Ubah Kategori Media';
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('kategoriForm').action = "/kategori-media/" + item.id;
            document.getElementById('modal_kode').value = item.kode;
            document.getElementById('modal_kode').readOnly = true;
            document.getElementById('modal_nama').value = item.nama;
            document.getElementById('modal_deskripsi').value = item.deskripsi || '';
            document.getElementById('kategoriModal').classList.remove('hidden');
            document.getElementById('modal_nama').focus();
        }

        function closeModal() {
            document.getElementById('kategoriModal').classList.add('hidden');
        }

        async function deleteKategori(id, nama, paketCount) {
            if (paketCount > 0) {
                AppAlert.error({
                    title: 'Tidak Dapat Dihapus',
                    subtitle: 'Kategori Masih Memiliki Paket',
                    message: `Kategori <strong>${nama}</strong> masih memiliki ${paketCount} paket iklan terhubung. Hapus atau pindahkan paket iklan terlebih dahulu.`,
                    buttonText: 'Mengerti'
                });
                return;
            }

            AppAlert.confirmDelete({
                title: 'Hapus Kategori Media',
                subtitle: 'Tindakan ini tidak dapat dibatalkan',
                target: nama,
                confirmText: 'Ya, Hapus Kategori'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').content;
                        const res = await axios.post('/kategori-media/' + id, {
                            _method: 'DELETE',
                            _token: token
                        });
                        AppAlert.success({
                            title: 'Berhasil Dihapus',
                            message: res.data.message || 'Kategori media berhasil dihapus.'
                        }).then(() => location.reload());
                    } catch (err) {
                        const msg = (err.response && err.response.data && err.response.data.message) 
                            ? err.response.data.message 
                            : 'Terjadi kesalahan sistem saat menghapus data.';
                        AppAlert.error({
                            title: 'Gagal Menghapus',
                            subtitle: 'Operasi Ditolak',
                            message: msg,
                            buttonText: 'Tutup'
                        });
                    }
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
