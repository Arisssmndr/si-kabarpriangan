<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Konsisten (Standar Kabar Priangan) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Data Master Jenis &amp; Ukuran Iklan
                    </h1>
                    <p class="text-xs font-normal text-slate-500 mt-0.5">
                        Pengelolaan master paket iklan cetak koran, banner portal online, dan spot tayang Priangan TV
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

        <!-- TOP KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Paket Master</span>
                <p class="text-lg font-extrabold text-slate-900 mt-1 font-tabular">
                    {{ $stats['total'] ?? $jenisList->total() }} Paket
                </p>
                <span class="text-[10px] text-slate-400 mt-0.5 block">Seluruh saluran media</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Koran Cetak</span>
                <p class="text-lg font-extrabold text-sky-600 mt-1 font-tabular">
                    {{ $stats['koran'] ?? 0 }} Paket
                </p>
                <span class="text-[10px] text-slate-400 mt-0.5 block">Baris, Kolom, Display &amp; Adv</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Media Online</span>
                <p class="text-lg font-extrabold text-emerald-600 mt-1 font-tabular">
                    {{ $stats['online'] ?? 0 }} Paket
                </p>
                <span class="text-[10px] text-slate-400 mt-0.5 block">Banner web &amp; advertorial digital</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Priangan TV</span>
                <p class="text-lg font-extrabold text-purple-600 mt-1 font-tabular">
                    {{ $stats['tv'] ?? 0 }} Paket
                </p>
                <span class="text-[10px] text-slate-400 mt-0.5 block">Running text, spot &amp; blocking</span>
            </div>
        </div>

        <!-- Toolbar Pencarian & Filter Komprehensif -->
        <div class="bg-white border border-slate-300 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('jenis-iklan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-6">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Pencarian Nama / Kode
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik kode paket, nama jenis iklan, atau keterangan..."
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-black placeholder-gray-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Saluran Media
                    </label>
                    <select name="kategori_id" class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-black focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="">Semua Saluran Media</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama }} ({{ $kat->kode }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-semibold text-xs rounded-lg shadow-xs transition flex items-center justify-center cursor-pointer">
                        Filter
                    </button>
                    @if(request()->hasAny(['q', 'kategori_id']))
                        <a href="{{ route('jenis-iklan.index') }}" class="h-10 px-3 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-lg transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Data Master Jenis Iklan Terpadu -->
        <div class="bg-white border border-slate-300 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-4 bg-slate-50/90 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Master Jenis &amp; Ukuran Iklan</h2>
                    <p class="text-xs text-slate-500 mt-0.5 font-normal">Total {{ $jenisList->total() }} jenis iklan terdaftar di sistem</p>
                </div>
            </div>

            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-300 bg-slate-100 text-slate-800 uppercase tracking-wider text-[11px] whitespace-nowrap">
                            <th class="py-3.5 px-3.5 font-bold text-center w-12">No</th>
                            <th class="py-3.5 px-3.5 font-bold">Kode Paket</th>
                            <th class="py-3.5 px-3.5 font-bold">Saluran Media</th>
                            <th class="py-3.5 px-3.5 font-bold">Nama Jenis / Paket Iklan</th>
                            <th class="py-3.5 px-3.5 font-bold">Spesifikasi &amp; Dimensi</th>
                            <th class="py-3.5 px-3.5 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 whitespace-nowrap">
                        @forelse($jenisList as $key => $j)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-3.5 text-center text-gray-500 font-normal">
                                    {{ $jenisList->firstItem() + $key }}
                                </td>
                                <td class="py-3 px-3.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-slate-100 text-kp-blue-700 border border-slate-200">
                                        {{ $j->kode_jenis }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5">
                                    @php
                                        $mediaCode = strtoupper($j->kategoriMedia->kode ?? '');
                                        $badgeColor = match(true) {
                                            str_contains($mediaCode, 'KORAN') => 'bg-sky-100 text-sky-800 border-sky-200',
                                            str_contains($mediaCode, 'ONLINE') => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            str_contains($mediaCode, 'TV') => 'bg-purple-100 text-purple-800 border-purple-200',
                                            default => 'bg-slate-100 text-slate-800 border-slate-200'
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold border {{ $badgeColor }}">
                                        {{ $j->kategoriMedia->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-bold text-slate-900">{{ $j->nama_jenis }}</div>
                                </td>
                                <td class="py-3 px-3.5">
                                    <span class="text-slate-600 font-normal truncate max-w-sm block">
                                        {{ $j->keterangan ?: '-' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" 
                                                onclick="openEditModal({{ json_encode($j) }})" 
                                                class="px-2.5 py-1.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-2xs transition cursor-pointer">
                                            Edit
                                        </button>
                                        <button type="button" 
                                                onclick="deleteJenis({{ $j->id }}, '{{ addslashes($j->nama_jenis) }}', '{{ $j->kode_jenis }}')" 
                                                class="px-2.5 py-1.5 bg-white hover:bg-rose-50 border border-rose-200 text-rose-600 text-xs font-semibold rounded-lg shadow-2xs transition cursor-pointer">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <rect x="3" y="3" width="18" height="18" rx="2" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9h18M9 21V9" />
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-600">Belum ada paket jenis iklan yang cocok</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Ubah kata kunci pencarian atau klik "Tambah Jenis Iklan" di atas</p>
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

    <!-- Modal Form (Tambah / Edit Jenis Iklan) -->
    <div id="jenisModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 id="modalTitle" class="text-sm font-bold text-slate-900">Tambah Jenis Iklan</h3>
                <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600 text-xl leading-none cursor-pointer">&times;</button>
            </div>

            <form id="jenisForm" method="POST" action="{{ route('jenis-iklan.store') }}" class="space-y-3.5 text-xs">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <!-- 1. PILIH KATEGORI MEDIA -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">
                        Saluran Media <span class="text-rose-500">*</span>
                    </label>
                    <select name="kategori_media_id" 
                            id="modal_kategori_media_id" 
                            required 
                            onchange="onKategoriChanged(this.value)"
                            class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="" disabled selected>Pilih Saluran Media...</option>
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
                        <label class="block font-bold text-slate-700 mb-1.5">
                            Kode Paket <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   name="kode_jenis" 
                                   id="modal_kode_jenis" 
                                   readonly 
                                   required 
                                   placeholder="Pilih saluran..." 
                                   class="w-full h-10 px-3 bg-slate-100 border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-800 uppercase tracking-wider cursor-not-allowed select-none focus:outline-none transition" />
                            <div id="kode_loading" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="animate-spin h-4 w-4 text-kp-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-7">
                        <label class="block font-bold text-slate-700 mb-1.5">
                            Nama Jenis / Paket <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="nama_jenis" 
                               id="modal_nama_jenis" 
                               required 
                               placeholder="Contoh: Display Kolom Full Color" 
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>
                </div>

                <!-- 3. KETERANGAN -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">
                        Spesifikasi Dimensi &amp; Keterangan
                    </label>
                    <textarea name="keterangan" 
                              id="modal_keterangan" 
                              rows="3" 
                              placeholder="Keterangan ukuran mmk, durasi tayang, atau posisi penempatan..."
                              class="w-full p-3 bg-white border border-slate-300 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal()" class="h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-lg text-xs font-semibold transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-lg text-xs font-semibold transition cursor-pointer shadow-xs">
                        Simpan Jenis Iklan
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
