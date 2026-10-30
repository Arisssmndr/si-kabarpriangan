<x-app-layout>
    <div class="space-y-6" x-data="{ tambahModal: false, editModal: false, editData: {} }">

        <!-- Header Halaman Standar Selaras Foto 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Rubrikasi Iklan Baris (Kop Iklan)
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl">
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
            <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 font-semibold rounded-xl text-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter Pencarian -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('kop-iklan.index') }}" class="flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama rubrik atau kode..."
                       class="flex-1 h-11 px-3.5 text-sm font-medium text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600 shadow-2xs">
                <button type="submit" class="h-11 px-5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold rounded-xl transition shadow-xs cursor-pointer">
                    Cari
                </button>
                @if(request()->filled('q'))
                    <a href="{{ route('kop-iklan.index') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-sm font-semibold rounded-xl transition flex items-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabel Master Data Kop Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-4">Urutan</th>
                            <th class="py-3 px-4">Kode</th>
                            <th class="py-3 px-4">Nama Rubrik</th>
                            <th class="py-3 px-4">Deskripsi / Keterangan</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($kopIklans as $rubrik)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 font-semibold font-mono text-slate-900">#{{ $rubrik->urutan }}</td>
                                <td class="py-3 px-4 font-bold font-mono text-kp-blue-700">{{ $rubrik->kode }}</td>
                                <td class="py-3 px-4 font-medium text-slate-900">{{ $rubrik->nama_kop }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $rubrik->deskripsi ?: '-' }}</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold border {{ $rubrik->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                        {{ $rubrik->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" 
                                                @click="editData = {{ json_encode($rubrik) }}; editModal = true"
                                                class="px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-bold rounded-lg shadow-2xs transition cursor-pointer">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('kop-iklan.toggle-status', $rubrik->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1.5 {{ $rubrik->is_active ? 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' }} text-xs font-semibold rounded-lg transition cursor-pointer shadow-2xs">
                                                {{ $rubrik->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-sm font-medium text-slate-400">
                                    Belum ada data rubrik iklan baris.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($kopIklans->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $kopIklans->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Tambah Rubrik Baru -->
        <div x-cloak x-show="tambahModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="tambahModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <h3 class="text-base font-bold text-black mb-3">
                    Tambah Rubrik Iklan Baris Baru
                </h3>

                <form method="POST" action="{{ route('kop-iklan.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-black mb-1">Kode Rubrik (Unik):</label>
                        <input type="text" name="kode" required placeholder="Contoh: HILANG, OTO, LOKER"
                               class="w-full px-3 py-2 text-xs font-bold text-black uppercase bg-white border border-slate-300 rounded-xl font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-black mb-1">Nomor Urut Tampil:</label>
                        <input type="number" name="urutan" required min="1" value="1"
                               class="w-full px-3 py-2 text-xs font-bold text-black bg-white border border-slate-300 rounded-xl font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-black mb-1">Nama Rubrik:</label>
                        <input type="text" name="nama_kop" required placeholder="Contoh: Kehilangan (STNK/BPKB/Sertifikat)"
                               class="w-full px-3 py-2 text-xs font-bold text-black bg-white border border-slate-300 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-black mb-1">Deskripsi / Keterangan:</label>
                        <textarea name="deskripsi" rows="2" placeholder="Keterangan isi rubrik..."
                                  class="w-full p-2.5 text-xs font-semibold text-black bg-white border border-slate-300 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3">
                        <button type="button" @click="tambahModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-black text-xs font-bold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold rounded-xl transition">
                            Simpan Rubrik
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Rubrik -->
        <div x-cloak x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="editModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <h3 class="text-base font-bold text-black mb-3">
                    Edit Rubrik Iklan [<span class="font-mono text-kp-blue-700" x-text="editData.kode"></span>]
                </h3>

                <form method="POST" :action="'{{ url('master/kop-iklan') }}/' + editData.id" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-black mb-1">Nomor Urut Tampil:</label>
                        <input type="number" name="urutan" required min="1" :value="editData.urutan"
                               class="w-full px-3 py-2 text-xs font-bold text-black bg-white border border-slate-300 rounded-xl font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-black mb-1">Nama Rubrik:</label>
                        <input type="text" name="nama_kop" required :value="editData.nama_kop"
                               class="w-full px-3 py-2 text-xs font-bold text-black bg-white border border-slate-300 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-black mb-1">Deskripsi / Keterangan:</label>
                        <textarea name="deskripsi" rows="2" :value="editData.deskripsi"
                                  class="w-full p-2.5 text-xs font-semibold text-black bg-white border border-slate-300 rounded-xl"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3">
                        <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-black text-xs font-bold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold rounded-xl transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
