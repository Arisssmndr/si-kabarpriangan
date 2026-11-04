<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Standar Selaras Foto 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Data Master Sales &amp; Account Executive
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl">
                        Kelola personil AE struktural, koresponden daerah, dan front office loket.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <button type="button" onclick="openModalTambah()"
                        class="inline-flex items-center gap-2 h-10 px-3.5 sm:px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-xs hover:shadow transition cursor-pointer shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah Sales / AE</span>
                </button>
            </div>
        </div>

        <!-- Flash Messages (60-30-10) -->
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
        @if(session('error'))
            <div class="p-3.5 bg-slate-100 border border-slate-300 text-slate-800 rounded-xl text-sm font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- TOP KPI Summary Cards (60-30-10) -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Sales / AE</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-tabular">
                    {{ $stats['total'] }} <span class="text-xs font-normal text-slate-400">Personil</span>
                </p>
                <span class="text-xs text-slate-400 mt-0.5 block">Seluruh personil tercatat</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">AE Struktural</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-tabular">
                    {{ $stats['struktural'] }} <span class="text-xs font-normal text-slate-400">Personil</span>
                </p>
                <span class="text-xs text-slate-400 mt-0.5 block">Kantor pusat (Kode S)</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">AE Koresponden</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-tabular">
                    {{ $stats['koresponden'] }} <span class="text-xs font-normal text-slate-400">Personil</span>
                </p>
                <span class="text-xs text-slate-400 mt-0.5 block">Biro wilayah (Kode K)</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Status Aktif</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-tabular">
                    {{ $stats['aktif'] }} <span class="text-xs font-normal text-slate-400">Personil</span>
                </p>
                <span class="text-xs text-slate-400 mt-0.5 block">Siap menerima order iklan</span>
            </div>
        </div>

        <!-- Toolbar Pencarian & Filter (Selaras /transaksipriangan) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sales-ae.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Pencarian Data
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik kode AE atau nama lengkap sales..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Klasifikasi Jenis
                    </label>
                    <select name="jenis" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="">Semua Jenis Sales/AE</option>
                        <option value="STRUKTURAL" {{ request('jenis') == 'STRUKTURAL' ? 'selected' : '' }}>Struktural (Kode S)</option>
                        <option value="KORESPONDEN" {{ request('jenis') == 'KORESPONDEN' ? 'selected' : '' }}>Koresponden (Kode K)</option>
                        <option value="FO" {{ request('jenis') == 'FO' ? 'selected' : '' }}>Front Office Counter</option>
                    </select>
                </div>

                <div class="sm:col-span-3 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'jenis']))
                        <a href="{{ route('sales-ae.index') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Data Master Sales/AE (Selaras /transaksipriangan, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">Kode AE</th>
                            <th class="py-3 px-3.5">Nama Sales / AE</th>
                            <th class="py-3 px-3.5">Klasifikasi</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($salesAes as $key => $ae)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                    {{ $salesAes->firstItem() + $key }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    {{ $ae->kode_ae }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    {{ $ae->nama_ae }}
                                </td>
                                <td class="py-3.5 px-3.5">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $ae->jenis_kode }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border {{ $ae->is_active ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                        {{ $ae->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
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
                                             class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">
                                            
                                            <!-- 1. Ubah Sales / AE -->
                                            <button type="button"
                                                    onclick="openModalEdit({{ $ae->id }}, '{{ $ae->kode_ae }}', '{{ addslashes($ae->nama_ae) }}', '{{ $ae->jenis_kode }}')"
                                                    @click="open = false"
                                                    class="w-full group flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:text-kp-blue-700 hover:bg-kp-blue-50 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-slate-400 group-hover:text-kp-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Ubah Sales / AE</span>
                                            </button>

                                            <!-- 2. Toggle Status Aktif / Nonaktif -->
                                            <form method="POST" action="{{ route('sales-ae.toggle-status', $ae->id) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                        class="w-full group flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition text-left cursor-pointer">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                                                    </svg>
                                                    <span>{{ $ae->is_active ? 'Nonaktifkan Sales' : 'Aktifkan Sales' }}</span>
                                                </button>
                                            </form>

                                            @if(!$ae->transaksis()->exists())
                                                <div class="h-px bg-slate-100 my-1"></div>

                                                <!-- 3. Hapus Sales / AE -->
                                                <form method="POST" action="{{ route('sales-ae.destroy', $ae->id) }}"
                                                      onsubmit="return confirm('Hapus Sales/AE [{{ $ae->kode_ae }}]? Pastikan tidak ada transaksi.')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                            class="w-full group flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition text-left cursor-pointer">
                                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        <span>Hapus Sales / AE</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500 font-normal">
                                    Belum ada data Sales/AE yang cocok. Klik "Tambah Sales / AE" untuk menambah data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($salesAes->hasPages())
                <div class="p-4 border-t border-slate-200 bg-white">
                    {{ $salesAes->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Modal Tambah -->
    <div id="modalTambahAe" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Tambah Sales / Account Executive Baru</h3>
                <button type="button" onclick="closeModal('modalTambahAe')" class="text-slate-400 hover:text-slate-600 text-xl cursor-pointer leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('sales-ae.store') }}" class="space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-700 mb-1.5 block">Klasifikasi <span class="text-rose-500">*</span></label>
                        <select name="jenis_kode" id="tambah_jenis_kode" onchange="autoGenerateKode()"
                                class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                            <option value="STRUKTURAL">Struktural</option>
                            <option value="KORESPONDEN">Koresponden</option>
                            <option value="FO">Front Office</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 mb-1.5 block">Kode AE <span class="text-rose-500">*</span></label>
                        <div class="flex gap-1.5">
                            <input type="text" name="kode_ae" id="tambah_kode_ae" required maxlength="20"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600"
                                   placeholder="Contoh: S15">
                            <button type="button" onclick="autoGenerateKode()" title="Generate otomatis"
                                    class="h-10 px-3 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded-lg text-slate-600 transition cursor-pointer flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="font-bold text-slate-700 mb-1.5 block">Nama Lengkap Sales / AE <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_ae" required maxlength="150"
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600"
                           placeholder="Nama lengkap personil">
                </div>
                <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal('modalTambahAe')"
                            class="h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-lg text-xs font-semibold cursor-pointer">Batal</button>
                    <button type="submit"
                            class="h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-lg text-xs font-semibold cursor-pointer shadow-xs transition">
                        Simpan Sales/AE
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="modalEditAe" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Edit Sales / Account Executive</h3>
                <button type="button" onclick="closeModal('modalEditAe')" class="text-slate-400 hover:text-slate-600 text-xl cursor-pointer leading-none">&times;</button>
            </div>
            <form id="formEditAe" method="POST" action="" class="space-y-3.5 text-xs">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-700 mb-1.5 block">Klasifikasi <span class="text-rose-500">*</span></label>
                        <select name="jenis_kode" id="edit_jenis_kode"
                                class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                            <option value="STRUKTURAL">Struktural</option>
                            <option value="KORESPONDEN">Koresponden</option>
                            <option value="FO">Front Office</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 mb-1.5 block">Kode AE <span class="text-rose-500">*</span></label>
                        <input type="text" name="kode_ae" id="edit_kode_ae" required maxlength="20"
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                    </div>
                </div>
                <div>
                    <label class="font-bold text-slate-700 mb-1.5 block">Nama Lengkap Sales / AE <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_ae" id="edit_nama_ae" required maxlength="150"
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                </div>
                <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal('modalEditAe')"
                            class="h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-lg text-xs font-semibold cursor-pointer">Batal</button>
                    <button type="submit"
                            class="h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-lg text-xs font-semibold cursor-pointer shadow-xs transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModalTambah() {
            document.getElementById('modalTambahAe').classList.remove('hidden');
            autoGenerateKode();
        }

        function openModalEdit(id, kode, nama, jenis) {
            document.getElementById('formEditAe').action = `/master/sales-ae/${id}`;
            document.getElementById('edit_kode_ae').value = kode;
            document.getElementById('edit_nama_ae').value = nama;
            document.getElementById('edit_jenis_kode').value = jenis;
            document.getElementById('modalEditAe').classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function autoGenerateKode() {
            const jenis = document.getElementById('tambah_jenis_kode').value;
            fetch(`/master/sales-ae/generate-kode?jenis=${jenis}`)
                .then(r => r.json())
                .then(data => { document.getElementById('tambah_kode_ae').value = data.kode; });
        }
    </script>
</x-app-layout>
