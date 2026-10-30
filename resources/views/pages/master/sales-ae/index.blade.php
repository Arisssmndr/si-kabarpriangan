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

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- TOP KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Sales / AE</span>
                <p class="text-xl font-bold text-slate-900 mt-1 font-tabular">
                    {{ $stats['total'] }} Personil
                </p>
                <span class="text-xs text-slate-400 mt-0.5 block">Seluruh personil tercatat</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">AE Struktural</span>
                <p class="text-xl font-bold text-kp-blue-700 mt-1 font-tabular">
                    {{ $stats['struktural'] }} Personil
                </p>
                <span class="text-xs text-slate-400 mt-0.5 block">Kantor pusat (Kode S)</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">AE Koresponden</span>
                <p class="text-xl font-bold text-amber-600 mt-1 font-tabular">
                    {{ $stats['koresponden'] }} Personil
                </p>
                <span class="text-xs text-slate-400 mt-0.5 block">Biro wilayah (Kode K)</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Status Aktif</span>
                <p class="text-xl font-bold text-emerald-600 mt-1 font-tabular">
                    {{ $stats['aktif'] }} Personil
                </p>
                <span class="text-xs text-slate-400 mt-0.5 block">Siap menerima order iklan</span>
            </div>
        </div>

        <!-- Toolbar Pencarian & Filter Komprehensif -->
        <div class="bg-white border border-slate-300 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sales-ae.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-6">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Pencarian Data
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik kode AE atau nama lengkap sales..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-gray-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Klasifikasi Jenis
                    </label>
                    <select name="jenis" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="">Semua Jenis Sales/AE</option>
                        <option value="STRUKTURAL" {{ request('jenis') == 'STRUKTURAL' ? 'selected' : '' }}>Struktural (Kode S)</option>
                        <option value="KORESPONDEN" {{ request('jenis') == 'KORESPONDEN' ? 'selected' : '' }}>Koresponden (Kode K)</option>
                        <option value="FO" {{ request('jenis') == 'FO' ? 'selected' : '' }}>Front Office Counter</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-semibold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'jenis']))
                        <a href="{{ route('sales-ae.index') }}" class="h-11 px-3.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Data Master Sales/AE Terpadu -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="whitespace-nowrap">
                            <th class="py-3 px-3.5 font-bold text-center w-12">No</th>
                            <th class="py-3 px-3.5 font-bold">Kode AE</th>
                            <th class="py-3 px-3.5 font-bold">Nama Sales / AE</th>
                            <th class="py-3 px-3.5 font-bold">Klasifikasi</th>
                            <th class="py-3 px-3.5 font-bold text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap">
                        @forelse($salesAes as $key => $ae)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-3.5 text-center text-slate-500 font-normal">
                                    {{ $salesAes->firstItem() + $key }}
                                </td>
                                <td class="py-3 px-3.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-slate-100 text-kp-blue-700 border border-slate-200">
                                        {{ $ae->kode_ae }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-bold text-slate-900">{{ $ae->nama_ae }}</div>
                                </td>
                                <td class="py-3 px-3.5">
                                    @php
                                        $jenisColor = match($ae->jenis_kode) {
                                             'STRUKTURAL'  => 'bg-violet-100 text-violet-800 border-violet-200',
                                             'KORESPONDEN' => 'bg-amber-100 text-amber-800 border-amber-200',
                                             default       => 'bg-blue-100 text-blue-800 border-blue-200',
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold border {{ $jenisColor }}">
                                        {{ $ae->jenis_kode }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <form method="POST" action="{{ route('sales-ae.toggle-status', $ae->id) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" 
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border transition cursor-pointer shadow-2xs {{ $ae->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' }}">
                                            @if($ae->is_active)
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                <span>Aktif</span>
                                            @else
                                                <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                <span>Nonaktif</span>
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button"
                                                onclick="openModalEdit({{ $ae->id }}, '{{ $ae->kode_ae }}', '{{ addslashes($ae->nama_ae) }}', '{{ $ae->jenis_kode }}')"
                                                class="px-2.5 py-1.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-2xs transition cursor-pointer">
                                            Edit
                                        </button>
                                        @if(!$ae->transaksis()->exists())
                                            <form method="POST" action="{{ route('sales-ae.destroy', $ae->id) }}"
                                                  onsubmit="return confirm('Hapus Sales/AE [{{ $ae->kode_ae }}]? Pastikan tidak ada transaksi.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1.5 bg-white hover:bg-rose-50 border border-rose-200 text-rose-600 text-xs font-semibold rounded-lg shadow-2xs transition cursor-pointer">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-600">Belum ada data Sales/AE yang cocok</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Klik tombol "Tambah Sales/AE" di atas untuk menambahkan data baru</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($salesAes->hasPages())
                <div class="px-5 py-3 border-t border-slate-200 bg-slate-50/80">
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
