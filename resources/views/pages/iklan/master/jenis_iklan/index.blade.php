<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Konsisten (Standar Laporan dengan Logo Kantor) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Master Tipe & Jenis Iklan
                    </h1>
                    <p class="text-xs font-normal text-slate-500 mt-0.5">
                        Pengelolaan paket dan spesifikasi jenis iklan per kategori media
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <button type="button" 
                        onclick="openAddModal()" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs font-semibold rounded-lg shadow-xs hover:shadow transition cursor-pointer">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah Jenis Iklan</span>
                </button>
            </div>
        </div>

        <!-- Toolbar Pencarian & Filter Kategori -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('jenis-iklan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Pencarian Nama / Kode
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" 
                               name="q" 
                               value="{{ request('q') }}" 
                               placeholder="Ketik nama paket atau kode..."
                               class="w-full h-10 pl-10 pr-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Filter Kategori Media
                    </label>
                    <select name="kategori_id" class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="">Semua Kategori Media</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama }} ({{ $kat->kode }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-3 flex gap-2">
                    <button type="submit" class="flex-1 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs rounded-lg transition cursor-pointer flex items-center justify-center">
                        Filter
                    </button>
                    @if(request()->hasAny(['q', 'kategori_id']))
                        <a href="{{ route('jenis-iklan.index') }}" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-lg transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Data Jenis Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Daftar Paket & Jenis Iklan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Total {{ $jenisList->total() }} jenis iklan terdaftar</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-600 text-xs whitespace-nowrap">
                            <th class="py-3 px-4 font-semibold text-center w-12">No</th>
                            <th class="py-3 px-4 font-semibold">Kode Paket</th>
                            <th class="py-3 px-4 font-semibold">Kategori Media</th>
                            <th class="py-3 px-4 font-semibold">Nama Jenis / Paket</th>
                            <th class="py-3 px-4 font-semibold">Keterangan</th>
                            <th class="py-3 px-4 font-semibold text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($jenisList as $key => $j)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-4 text-center text-slate-900 font-normal">
                                    {{ $jenisList->firstItem() + $key }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-slate-100 text-kp-blue-700 border border-slate-200">
                                        {{ $j->kode_jenis }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $j->kategoriMedia->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-900 font-semibold">
                                    {{ $j->nama_jenis }}
                                </td>
                                <td class="py-3 px-4 text-slate-700 font-normal max-w-sm truncate">
                                    {{ $j->keterangan ?: '-' }}
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" 
                                                onclick="openEditModal({{ json_encode($j) }})" 
                                                class="px-2.5 py-1 bg-white hover:bg-slate-100 text-slate-800 font-medium text-xs rounded-md transition border border-slate-200 cursor-pointer">
                                            Ubah
                                        </button>
                                        <button type="button" 
                                                onclick="deleteJenis({{ $j->id }}, '{{ $j->nama_jenis }}', '{{ $j->kode_jenis }}')" 
                                                class="px-2.5 py-1 bg-white hover:bg-rose-50 text-rose-600 font-medium text-xs rounded-md transition border border-slate-200 hover:border-rose-200 cursor-pointer">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500 text-xs">
                                    Belum ada jenis iklan yang sesuai kriteria. Klik "Tambah Jenis Iklan" untuk menambah baru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($jenisList->hasPages())
                <div class="px-5 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $jenisList->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Form (Tambah / Edit Jenis Iklan) -->
    <div id="jenisModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
                <h3 id="modalTitle" class="text-sm font-semibold text-slate-900">Tambah Jenis Iklan</h3>
                <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600 text-lg leading-none cursor-pointer">&times;</button>
            </div>

            <form id="jenisForm" method="POST" action="{{ route('jenis-iklan.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <!-- 1. PILIH KATEGORI MEDIA -->
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">
                        Pilih Kategori Media <span class="text-rose-500">*</span>
                    </label>
                    <select name="kategori_media_id" 
                            id="modal_kategori_media_id" 
                            required 
                            onchange="onKategoriChanged(this.value)"
                            class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="" disabled selected>Pilih Kategori Media...</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat->id }}" data-prefix="{{ $kat->kode }}">
                                {{ $kat->nama }} ({{ $kat->kode }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. KODE PAKET (OTOMATIS DARI DATABASE) & NAMA PAKET -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <div class="sm:col-span-5">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-medium text-slate-700">
                                Kode Paket <span class="text-rose-500">*</span>
                            </label>
                            <!-- <span id="badge_kode_status" class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Otomatis DB
                            </span> -->
                        </div>
                        <div class="relative">
                            <input type="text" 
                                   name="kode_jenis" 
                                   id="modal_kode_jenis" 
                                   readonly 
                                   required 
                                   placeholder="Pilih kategori..." 
                                   class="w-full h-10 px-3 bg-slate-100 border border-slate-300 rounded-lg text-xs text-slate-900 font-mono font-bold uppercase tracking-wider cursor-not-allowed select-none focus:outline-none transition" />
                            <div id="kode_loading" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="animate-spin h-4 w-4 text-kp-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Kode otomatis dari database.</p>
                    </div>

                    <div class="sm:col-span-7">
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Nama Jenis / Paket <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="nama_jenis" 
                               id="modal_nama_jenis" 
                               required 
                               placeholder="Contoh: Banner Header 30 Hari" 
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                        <p class="text-[11px] text-slate-400 mt-1">Nama spesifikasi jenis paket iklan.</p>
                    </div>
                </div>

                <!-- 3. KETERANGAN -->
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1.5">
                        Keterangan / Spesifikasi Khusus
                    </label>
                    <textarea name="keterangan" 
                              id="modal_keterangan" 
                              rows="3" 
                              placeholder="Keterangan dimensi, durasi, atau posisi penempatan..."
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
        async function onKategoriChanged(kategoriId) {
            if (!kategoriId) {
                document.getElementById('modal_kode_jenis').value = '';
                return;
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
                    const select = document.getElementById('modal_kategori_media_id');
                    const selectedOption = select.options[select.selectedIndex];
                    const prefix = selectedOption.getAttribute('data-prefix') || 'IKL';
                    kodeInput.value = prefix + '001';
                } finally {
                    if (loading) loading.classList.add('hidden');
                }
            }
        }

        async function openAddModal() {
            document.getElementById('modalTitle').innerText = 'Tambah Jenis Iklan';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('jenisForm').action = "{{ route('jenis-iklan.store') }}";
            
            const badge = document.getElementById('badge_kode_status');
            if (badge) {
                badge.className = 'inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200';
                badge.innerHTML = `<svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Otomatis DB`;
            }

            const kategoriSelect = document.getElementById('modal_kategori_media_id');
            kategoriSelect.disabled = false;

            // Jika ada filter kategori aktif di URL, gunakan itu; jika tidak pilih kategori pertama
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
            document.getElementById('modal_keterangan').value = '';
            document.getElementById('jenisModal').classList.remove('hidden');

            if (kategoriSelect.value) {
                await onKategoriChanged(kategoriSelect.value);
            }
            document.getElementById('modal_nama_jenis').focus();
        }

        function openEditModal(item) {
            document.getElementById('modalTitle').innerText = 'Ubah Jenis Iklan';
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('jenisForm').action = "/jenis-iklan/" + item.id;

            const badge = document.getElementById('badge_kode_status');
            if (badge) {
                badge.className = 'inline-flex items-center text-[10px] font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200';
                badge.innerText = 'Kode Terdaftar';
            }

            const kategoriSelect = document.getElementById('modal_kategori_media_id');
            kategoriSelect.value = item.kategori_media_id;
            document.getElementById('modal_kode_jenis').value = item.kode_jenis;
            document.getElementById('modal_nama_jenis').value = item.nama_jenis;
            document.getElementById('modal_keterangan').value = item.keterangan || '';
            document.getElementById('jenisModal').classList.remove('hidden');
            document.getElementById('modal_nama_jenis').focus();
        }

        function closeModal() {
            document.getElementById('jenisModal').classList.add('hidden');
        }

        async function deleteJenis(id, nama, kode) {
            AppAlert.confirmDelete({
                title: 'Hapus Jenis Iklan',
                subtitle: 'Tindakan ini tidak dapat dibatalkan',
                target: `${nama} (${kode})`,
                confirmText: 'Ya, Hapus Paket'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').content;
                        const res = await axios.post('/jenis-iklan/' + id, {
                            _method: 'DELETE',
                            _token: token
                        });
                        AppAlert.success({
                            title: 'Berhasil Dihapus',
                            message: res.data.message || 'Jenis iklan berhasil dihapus.'
                        }).then(() => location.reload());
                    } catch (err) {
                        AppAlert.error({
                            title: 'Gagal Menghapus',
                            subtitle: 'Operasi Gagal',
                            message: 'Terjadi kesalahan sistem saat menghapus data.',
                            buttonText: 'Tutup'
                        });
                    }
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
