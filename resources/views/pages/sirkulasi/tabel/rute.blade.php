<x-app-layout>
    <div class="space-y-5" x-data="{
        showTambahModal: false,
        showEditModal: false,
        editData: { id: '', kode: '', nama_rute: '', armada: '', sopir: '', jam_berangkat: '02:30 WIB', titik_tujuan: '', alokasi_oplah: 0 }
    }">

        <!-- Header Halaman (Standar Divisi Iklan, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Master Rute &amp; Jurusan Distribusi
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl" title="Pengaturan rute armada mobil ekspedisi subuh, penugasan sopir, dan drop point agen koran.">
                        Pengaturan rute armada mobil ekspedisi subuh, penugasan sopir, dan drop point agen koran.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('sirkulasi.cetak.nota-distribusi') }}"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Nota Kirim Armada</span>
                </a>

                <button type="button"
                        @click="showTambahModal = true"
                        class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah Rute Baru</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-slate-100 border border-slate-300 text-slate-800 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Ringkasan KPI Rute (60-30-10) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Rute Armada</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ $totalRute }} <span class="text-xs font-sans font-normal text-slate-500">Jalur Distribusi</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Tersimpan di Master Rute</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Alokasi Oplah Armada</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    {{ number_format($totalOplah, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-500">Eks / Hari</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Kapasitas muat gabungan</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jadwal Keberangkatan</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    01:45 - 03:30 <span class="text-xs font-sans font-normal text-slate-500">WIB</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Ekspedisi Subuh Priangan Timur</span>
            </div>
        </div>

        <!-- Filter & Pencarian Rute -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.tabel.rute') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-8">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Rute</label>
                    <input type="text"
                           name="search"
                           value="{{ $search ?? '' }}"
                           placeholder="Ketik kode rute, nama jurusan, nama sopir, armada mobil, atau titik tujuan..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status Rute</label>
                    <select name="status" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Status</option>
                        <option value="aktif" {{ ($statusFilter ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ ($statusFilter ?? '') === 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Filter
                    </button>
                    @if(!empty($search) || ($statusFilter ?? 'all') !== 'all')
                        <a href="{{ route('sirkulasi.tabel.rute') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Master Rute (Standar Divisi Iklan, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-16">Kode</th>
                            <th class="py-3 px-3.5">Nama Rute Jurusan</th>
                            <th class="py-3 px-3.5">Kendaraan</th>
                            <th class="py-3 px-3.5">Sopir Pengantar</th>
                            <th class="py-3 px-3.5 text-center">Jam Berangkat</th>
                            <th class="py-3 px-3.5">Lokasi Tujuan</th>
                            <th class="py-3 px-3.5 text-right">Total Kirim</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($ruteList as $rute)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center font-mono font-bold text-kp-blue-700 text-sm whitespace-nowrap">
                                    {{ $rute->kode }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $rute->nama_rute }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 text-xs font-mono whitespace-nowrap">
                                    {{ $rute->armada }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-700 whitespace-nowrap">
                                    {{ $rute->sopir }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center font-mono text-xs text-slate-600 whitespace-nowrap">
                                    {{ $rute->jam_berangkat }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 text-xs leading-relaxed min-w-[220px]">
                                    {{ $rute->titik_tujuan }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right whitespace-nowrap font-mono font-semibold text-slate-900 tabular-nums">
                                    {{ number_format($rute->alokasi_oplah, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                                </td>
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                    @if($rute->status === 'aktif')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                            Non-Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap"
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

                                            <button type="button"
                                                    @click="
                                                        open = false;
                                                        editData = {
                                                            id: {{ $rute->id }},
                                                            kode: '{{ $rute->kode }}',
                                                            nama_rute: '{{ addslashes($rute->nama_rute) }}',
                                                            armada: '{{ addslashes($rute->armada) }}',
                                                            sopir: '{{ addslashes($rute->sopir) }}',
                                                            jam_berangkat: '{{ addslashes($rute->jam_berangkat) }}',
                                                            titik_tujuan: '{{ addslashes($rute->titik_tujuan) }}',
                                                            alokasi_oplah: {{ (int) $rute->alokasi_oplah }}
                                                        };
                                                        showEditModal = true;
                                                    "
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                <span>Edit Data Rute</span>
                                            </button>

                                            <form method="POST" action="{{ route('sirkulasi.tabel.rute.toggle', $rute->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition text-left cursor-pointer">
                                                    @if($rute->status === 'aktif')
                                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                                        <span>Set Non-Aktif</span>
                                                    @else
                                                        <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                        <span>Aktifkan Rute</span>
                                                    @endif
                                                </button>
                                            </form>

                                            <div class="h-px bg-slate-100 my-1"></div>

                                            <form method="POST" action="{{ route('sirkulasi.tabel.rute.destroy', $rute->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        onclick="return confirmSubmit(this, event, {
                                                            title: 'Hapus Rute?',
                                                            text: 'Apakah Anda yakin ingin menghapus rute <strong>{{ addslashes($rute->nama_rute) }}</strong>?',
                                                            theme: 'rose',
                                                            icon: 'trash',
                                                            confirmText: 'Ya, Hapus'
                                                        })"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50 rounded-lg transition text-left cursor-pointer">
                                                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    <span>Hapus Rute</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400">
                                    Tidak ada data rute yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL TAMBAH RUTE BARU -->
        <div x-cloak
             x-show="showTambahModal"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="bg-white w-full max-w-lg rounded-2xl border border-slate-200 shadow-2xl overflow-hidden p-6 space-y-4"
                 @click.outside="showTambahModal = false">

                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Tambah Rute Distribusi Baru</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Definisikan jalur armada ekspedisi dan sopir pengantar.</p>
                    </div>
                    <button type="button" @click="showTambahModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('sirkulasi.tabel.rute.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Rute <span class="text-rose-500">*</span></label>
                            <input type="text" name="kode" required placeholder="Contoh: 16"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Rute / Jurusan <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_rute" required placeholder="Contoh: Singaparna - Cikatomas"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Armada Mobil</label>
                            <input type="text" name="armada" placeholder="Contoh: Mobil L300 Box (Z 1234 XX)"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Sopir / Kru</label>
                            <input type="text" name="sopir" placeholder="Contoh: Pak Herman"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Berangkat</label>
                            <input type="text" name="jam_berangkat" value="02:30 WIB" placeholder="02:30 WIB"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Alokasi Oplah (Eks)</label>
                            <input type="number" name="alokasi_oplah" min="0" value="1000"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Titik Tujuan Drop-Point</label>
                        <textarea name="titik_tujuan" rows="2" placeholder="Sebutkan titik-titik persinggahan drop koran agen..."
                                  class="w-full p-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="showTambahModal = false"
                                class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-xs">
                            Simpan Rute
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL EDIT RUTE -->
        <div x-cloak
             x-show="showEditModal"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="bg-white w-full max-w-lg rounded-2xl border border-slate-200 shadow-2xl overflow-hidden p-6 space-y-4"
                 @click.outside="showEditModal = false">

                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Edit Rute: <span class="font-mono text-kp-blue-700" x-text="editData.kode"></span></h2>
                        <p class="text-xs text-slate-500 mt-0.5">Perbarui data armada, penugasan sopir, dan titik tujuan.</p>
                    </div>
                    <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" :action="'{{ url('/sirkulasi/tabel/rute') }}/' + editData.id" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Rute / Jurusan <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_rute" required x-model="editData.nama_rute"
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Armada Mobil</label>
                            <input type="text" name="armada" x-model="editData.armada"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Sopir / Kru</label>
                            <input type="text" name="sopir" x-model="editData.sopir"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Berangkat</label>
                            <input type="text" name="jam_berangkat" x-model="editData.jam_berangkat"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Alokasi Oplah (Eks)</label>
                            <input type="number" name="alokasi_oplah" min="0" x-model="editData.alokasi_oplah"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Titik Tujuan Drop-Point</label>
                        <textarea name="titik_tujuan" rows="2" x-model="editData.titik_tujuan"
                                  class="w-full p-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="showEditModal = false"
                                class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-xs">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
