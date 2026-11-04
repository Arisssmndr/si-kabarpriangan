<x-app-layout>
    <div class="space-y-5" x-data="{ tambahModal: false, editModal: false, editData: {} }">

        <!-- Header Halaman Konsisten (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Rubrikasi Iklan Baris (Kop Iklan)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Pengelompokan rubrik iklan baris harian (Kehilangan, Otomotif, Properti, dll).
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <button type="button" 
                        @click="tambahModal = true"
                        class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Rubrik Baru</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 font-semibold rounded-xl text-sm flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter Pencarian (Selaras /transaksipriangan) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('kop-iklan.index') }}" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-end">
                <div class="flex-1 w-full">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Pencarian Data
                    </label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik nama rubrik atau kode..."
                           class="w-full h-11 px-3.5 text-sm font-normal text-slate-900 bg-white border border-slate-300 rounded-xl placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>
                <div class="flex gap-2 shrink-0 sm:w-48">
                    <button type="submit" class="flex-1 h-11 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-sm font-bold rounded-xl transition shadow-xs flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->filled('q'))
                        <a href="{{ route('kop-iklan.index') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-sm font-semibold rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Master Data Kop Iklan (Selaras /transaksipriangan, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">Kode</th>
                            <th class="py-3 px-3.5 text-center w-20">Urutan</th>
                            <th class="py-3 px-3.5">Nama Rubrik</th>
                            <th class="py-3 px-3.5">Deskripsi / Keterangan</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($kopIklans as $idx => $rubrik)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                    {{ $kopIklans->firstItem() + $idx }}
                                </td>
                                <td class="py-3.5 px-3.5 font-bold font-mono text-kp-blue-700 text-sm">
                                    {{ $rubrik->kode }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center font-mono text-slate-600 font-normal">
                                    #{{ $rubrik->urutan }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    {{ $rubrik->nama_kop }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                    {{ $rubrik->deskripsi ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border {{ $rubrik->is_active ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                        {{ $rubrik->is_active ? 'Aktif' : 'Nonaktif' }}
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
                                            
                                            <!-- 1. Ubah Rubrik -->
                                            <button type="button"
                                                    @click="editData = {{ json_encode($rubrik) }}; editModal = true; open = false"
                                                    class="w-full group flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:text-kp-blue-700 hover:bg-kp-blue-50 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-slate-400 group-hover:text-kp-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Ubah Rubrik</span>
                                            </button>

                                            <div class="h-px bg-slate-100 my-1"></div>

                                            <!-- 2. Toggle Status Aktif / Nonaktif -->
                                            <form method="POST" action="{{ route('kop-iklan.toggle-status', $rubrik->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="w-full group flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition text-left cursor-pointer">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                                                    </svg>
                                                    <span>{{ $rubrik->is_active ? 'Nonaktifkan Rubrik' : 'Aktifkan Rubrik' }}</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500 font-normal">
                                    Belum ada data rubrik iklan baris. Klik "Tambah Rubrik Baru" untuk menambah data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($kopIklans->hasPages())
                <div class="p-4 border-t border-slate-200 bg-white">
                    {{ $kopIklans->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Tambah Rubrik Baru -->
        <div x-cloak x-show="tambahModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="tambahModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <h3 class="text-base font-bold text-slate-900 mb-3">
                    Tambah Rubrik Iklan Baris Baru
                </h3>

                <form method="POST" action="{{ route('kop-iklan.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Rubrik (Unik):</label>
                        <input type="text" name="kode" required placeholder="Contoh: HILANG, OTO, LOKER"
                               class="w-full px-3 py-2 text-sm font-semibold text-slate-900 uppercase bg-white border border-slate-300 rounded-xl font-mono focus:outline-none focus:border-kp-blue-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Urut Tampil:</label>
                        <input type="number" name="urutan" required min="1" value="1"
                               class="w-full px-3 py-2 text-sm font-semibold text-slate-900 bg-white border border-slate-300 rounded-xl font-mono focus:outline-none focus:border-kp-blue-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Rubrik:</label>
                        <input type="text" name="nama_kop" required placeholder="Contoh: Kehilangan (STNK/BPKB/Sertifikat)"
                               class="w-full px-3 py-2 text-sm font-normal text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi / Keterangan:</label>
                        <textarea name="deskripsi" rows="2" placeholder="Keterangan isi rubrik..."
                                  class="w-full p-2.5 text-sm font-normal text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3">
                        <button type="button" @click="tambahModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl transition cursor-pointer">
                            Simpan Rubrik
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Rubrik -->
        <div x-cloak x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="editModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <h3 class="text-base font-bold text-slate-900 mb-3">
                    Edit Rubrik Iklan [<span class="font-mono text-kp-blue-700" x-text="editData.kode"></span>]
                </h3>

                <form method="POST" :action="'{{ url('master/kop-iklan') }}/' + editData.id" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Urut Tampil:</label>
                        <input type="number" name="urutan" required min="1" :value="editData.urutan"
                               class="w-full px-3 py-2 text-sm font-semibold text-slate-900 bg-white border border-slate-300 rounded-xl font-mono focus:outline-none focus:border-kp-blue-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Rubrik:</label>
                        <input type="text" name="nama_kop" required :value="editData.nama_kop"
                               class="w-full px-3 py-2 text-sm font-normal text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi / Keterangan:</label>
                        <textarea name="deskripsi" rows="2" :value="editData.deskripsi"
                                  class="w-full p-2.5 text-sm font-normal text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3">
                        <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl transition cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
