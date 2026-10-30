<x-app-layout>
    <div class="space-y-6" x-data="{ tambahModal: false, editModal: false, editData: {} }">

        <!-- Header Halaman Standar Selaras Foto 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Penempatan Halaman Koran &amp; Biro (Kop Kota)
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl">
                        Konfigurasi nomor halaman, wilayah biro, dan surcharge penempatan koran cetak.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <button type="button" 
                        @click="tambahModal = true"
                        class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Halaman Baru</span>
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
            <form method="GET" action="{{ route('kop-kota.index') }}" class="flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama penempatan, nomor halaman, kode, atau biro..."
                       class="flex-1 h-11 px-3.5 text-sm font-medium text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600 shadow-2xs">
                <button type="submit" class="h-11 px-5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold rounded-xl transition shadow-xs cursor-pointer">
                    Cari
                </button>
                @if(request()->filled('q'))
                    <a href="{{ route('kop-kota.index') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-sm font-semibold rounded-xl transition flex items-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabel Master Data Kop Kota -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-4">Kode</th>
                            <th class="py-3 px-4">Halaman</th>
                            <th class="py-3 px-4">Nama Penempatan</th>
                            <th class="py-3 px-4">Wilayah Biro</th>
                            <th class="py-3 px-4">Surcharge</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($kopKotas as $kop)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 font-bold font-mono text-kp-blue-700">{{ $kop->kode }}</td>
                                <td class="py-3 px-4 font-semibold font-mono text-slate-900">Hal. {{ $kop->nomor_halaman }}</td>
                                <td class="py-3 px-4 font-medium text-slate-900">{{ $kop->nama_penempatan }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $kop->wilayah_biro }}</td>
                                <td class="py-3 px-4 font-mono font-semibold text-slate-900">
                                    {{ $kop->surcharge_persen > 0 ? '+' . $kop->surcharge_persen . '%' : '-' }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold border {{ $kop->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                        {{ $kop->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" 
                                                @click="editData = {{ json_encode($kop) }}; editModal = true"
                                                class="px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-bold rounded-lg shadow-2xs transition cursor-pointer">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('kop-kota.toggle-status', $kop->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1.5 {{ $kop->is_active ? 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' }} text-xs font-semibold rounded-lg transition cursor-pointer shadow-2xs">
                                                {{ $kop->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-sm font-medium text-slate-400">
                                    Belum ada data penempatan halaman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($kopKotas->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $kopKotas->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Tambah Halaman Baru -->
        <div x-cloak x-show="tambahModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="tambahModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <h3 class="text-base font-bold text-black mb-3">
                    Tambah Penempatan Halaman Baru
                </h3>

                <form method="POST" action="{{ route('kop-kota.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-black mb-1">Kode Halaman (Unik):</label>
                        <input type="text" name="kode" required placeholder="Contoh: HAL1, HAL5_SMD"
                               class="w-full px-3 py-2 text-xs font-bold text-black uppercase bg-white border border-slate-300 rounded-xl font-mono">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-black mb-1">Nomor Halaman:</label>
                            <input type="number" name="nomor_halaman" required min="1" max="64" value="1"
                                   class="w-full px-3 py-2 text-xs font-bold text-black bg-white border border-slate-300 rounded-xl font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-black mb-1">Surcharge (%):</label>
                            <input type="number" name="surcharge_persen" step="0.01" min="0" value="0.00"
                                   class="w-full px-3 py-2 text-xs font-bold text-black bg-white border border-slate-300 rounded-xl font-mono">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-black mb-1">Nama Penempatan:</label>
                        <input type="text" name="nama_penempatan" required placeholder="Contoh: Halaman 1 - Cover Depan"
                               class="w-full px-3 py-2 text-xs font-bold text-black bg-white border border-slate-300 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-black mb-1">Wilayah Biro Sirkulasi:</label>
                        <input type="text" name="wilayah_biro" required placeholder="Contoh: Tasikmalaya, Sumedang, Umum"
                               class="w-full px-3 py-2 text-xs font-bold text-black bg-white border border-slate-300 rounded-xl">
                    </div>

                    <div class="flex justify-end gap-2 pt-3">
                        <button type="button" @click="tambahModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-black text-xs font-bold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold rounded-xl transition">
                            Simpan Halaman
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Halaman -->
        <div x-cloak x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="editModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <h3 class="text-base font-bold text-black mb-3">
                    Edit Penempatan Halaman [<span class="font-mono text-kp-blue-700" x-text="editData.kode"></span>]
                </h3>

                <form method="POST" :action="'{{ url('master/kop-kota') }}/' + editData.id" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-black mb-1">Nomor Halaman:</label>
                            <input type="number" name="nomor_halaman" required min="1" max="64" :value="editData.nomor_halaman"
                                   class="w-full px-3 py-2 text-xs font-bold text-black bg-white border border-slate-300 rounded-xl font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-black mb-1">Surcharge (%):</label>
                            <input type="number" name="surcharge_persen" step="0.01" min="0" :value="editData.surcharge_persen"
                                   class="w-full px-3 py-2 text-xs font-bold text-black bg-white border border-slate-300 rounded-xl font-mono">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-black mb-1">Nama Penempatan:</label>
                        <input type="text" name="nama_penempatan" required :value="editData.nama_penempatan"
                               class="w-full px-3 py-2 text-xs font-bold text-black bg-white border border-slate-300 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-black mb-1">Wilayah Biro Sirkulasi:</label>
                        <input type="text" name="wilayah_biro" required :value="editData.wilayah_biro"
                               class="w-full px-3 py-2 text-xs font-bold text-black bg-white border border-slate-300 rounded-xl">
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
