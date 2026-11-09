<x-app-layout>
    @php
        $canManage = $canManageAgen ?? (auth()->user() && (auth()->user()->isAdmin() || auth()->user()->hasRole('accounting', 'akuntansi')));
    @endphp
    <div class="space-y-5">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Data Agen &amp; Sub-Agen
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Direktori agen koran, kuota jatah default, rute armada, dan saldo piutang.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('sirkulasi.cetak.alamat-agen') }}"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Direktori</span>
                </a>

                @if($canManage)
                    <button type="button"
                            onclick="document.getElementById('modalTambahAgen').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Tambah Agen</span>
                    </button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-slate-100 border border-slate-300 text-slate-800 rounded-xl text-xs sm:text-sm font-semibold shadow-2xs">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Toolbar Pencarian & Filter -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.tabel.agen') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Pencarian Data
                    </label>
                    <input type="text"
                           name="search"
                           value="{{ $search ?? '' }}"
                           placeholder="Kode agen, nama, atau alamat..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Jenis Agen
                    </label>
                    <select name="kategori" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Jenis</option>
                        <option value="11" {{ ($kategoriFilter ?? '') === '11' ? 'selected' : '' }}>11 - Agen Besar</option>
                        <option value="99" {{ ($kategoriFilter ?? '') === '99' ? 'selected' : '' }}>99 - Sub Agen</option>
                        <option value="00" {{ ($kategoriFilter ?? '') === '00' ? 'selected' : '' }}>00 - Agen Non Aktif</option>
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Wilayah Pemasaran
                    </label>
                    <select name="wilayah" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Wilayah</option>
                        @foreach($wilayahList as $w)
                            <option value="{{ $w->kode }}" {{ ($wilayahFilter ?? '') === $w->kode ? 'selected' : '' }}>
                                {{ $w->kode }} - {{ $w->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    <a href="{{ route('sirkulasi.tabel.agen') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center cursor-pointer">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabel Data Agen -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Agen Koran</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $canManage ? 'Kelola penuh (Akuntansi)' : 'Mode baca (Sirkulasi)' }}
                    </p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono w-fit">
                    {{ $agenList->count() }} Agen
                </span>
            </div>

            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-24">Tgl Daftar</th>
                            <th class="py-3 px-3.5">Kode Agen</th>
                            <th class="py-3 px-3.5">Nama Agen &amp; Kios</th>
                            <th class="py-3 px-3.5">Alamat</th>
                            <th class="py-3 px-3.5 text-center">Jenis</th>
                            <th class="py-3 px-3.5">Wilayah &amp; Rute</th>
                            <th class="py-3 px-3.5 text-right">Jatah Default</th>
                            <th class="py-3 px-3.5 text-right w-36">Saldo Piutang</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($agenList as $agen)
                            <tr class="hover:bg-slate-50/80 transition {{ $agen->status === 'non-aktif' ? 'bg-slate-50/60 opacity-80' : '' }}">
                                <td class="py-3.5 px-3.5 text-center text-slate-600 font-mono font-normal">
                                    {{ $agen->tanggal_daftar }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    <button type="button" onclick='openDetailModal(@json($agen))' class="hover:underline cursor-pointer text-left">
                                        {{ $agen->kode_agen }}
                                    </button>
                                </td>
                                <td class="py-3.5 px-3.5">
                                    <div class="font-semibold text-slate-900">{{ $agen->nama_agen }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5 font-mono">{{ $agen->telepon }}</div>
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal max-w-xs truncate" title="{{ $agen->alamat }}">
                                    {{ $agen->alamat }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($agen->kategori === '11')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            11 Besar
                                        </span>
                                    @elseif($agen->kategori === '99')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            99 Sub
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-400 border border-slate-200">
                                            00 Non-Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5">
                                    <div class="font-semibold text-slate-900">{{ $agen->wilayah_nama }}</div>
                                    <div class="text-xs text-slate-500">Rute #{{ $agen->jurusan_kode }}</div>
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">
                                    <span class="font-semibold text-slate-900">{{ number_format($agen->jatah_default, 0, ',', '.') }}</span>
                                    <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                                </td>
                                <td class="py-3.5 px-3.5 text-right whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($agen->saldo_piutang, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($agen->status === 'aktif')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            Non-Aktif
                                        </span>
                                    @endif
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
                                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
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
                                             class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-52 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">

                                            <button type="button"
                                                    onclick='openDetailModal(@json($agen))'
                                                    @click="open = false"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail Agen</span>
                                            </button>

                                            @if($canManage)
                                                <button type="button"
                                                        onclick='openEditModal(@json($agen))'
                                                        @click="open = false"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition text-left cursor-pointer">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    <span>Ubah Data Agen</span>
                                                </button>

                                                <form method="POST" action="{{ route('sirkulasi.tabel.agen.toggle', $agen->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            onclick="return confirm('Ubah status aktif agen {{ $agen->nama_agen }}?')"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition text-left cursor-pointer">
                                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                                        <span>{{ $agen->status === 'aktif' ? 'Nonaktifkan Agen' : 'Aktifkan Agen' }}</span>
                                                    </button>
                                                </form>

                                                <div class="h-px bg-slate-100 my-1"></div>

                                                <form method="POST" action="{{ route('sirkulasi.tabel.agen.destroy', $agen->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            onclick="return confirm('Hapus data agen {{ $agen->nama_agen }}? Jika masih memiliki piutang, agen akan dialihkan ke 00 Non-Aktif.')"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50 rounded-lg transition text-left cursor-pointer">
                                                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                        <span>Hapus Agen</span>
                                                    </button>
                                                </form>
                                            @else
                                                <div class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-400 bg-slate-50 rounded-lg cursor-not-allowed select-none">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                    <span>Dikelola Akuntansi</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-500 font-normal">
                                    Tidak ada data agen yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Modal Detail Agen -->
    <div id="modalDetailAgen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900" id="detailNamaAgen">Rincian Data Agen</h3>
                    <p class="text-xs text-kp-blue-700 font-mono font-bold mt-0.5" id="detailKodeAgen">AG-XX-XXX</p>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg cursor-pointer">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="p-5 space-y-4 text-xs text-slate-700">
                <div class="grid grid-cols-3 gap-3 p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                    <div>
                        <span class="block text-xs font-semibold text-slate-500">Jenis</span>
                        <div class="mt-0.5 font-bold text-slate-900" id="detailJenis">-</div>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-500">Status</span>
                        <div class="mt-0.5 font-bold text-slate-900" id="detailStatus">-</div>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-500">Tgl Daftar</span>
                        <div class="mt-0.5 font-mono text-slate-700" id="detailTglDaftar">-</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 border border-slate-200 rounded-xl p-4">
                    <div>
                        <span class="text-slate-500 text-xs">Wilayah Pemasaran</span>
                        <div class="font-semibold text-slate-900 mt-0.5" id="detailWilayah">-</div>
                    </div>
                    <div>
                        <span class="text-slate-500 text-xs">Jurusan Armada</span>
                        <div class="font-semibold text-slate-900 mt-0.5" id="detailJurusan">-</div>
                    </div>
                    <div>
                        <span class="text-slate-500 text-xs">Jatah Default Harian</span>
                        <div class="font-bold text-kp-blue-700 font-mono mt-0.5" id="detailJatah">0 eks</div>
                    </div>
                    <div>
                        <span class="text-slate-500 text-xs">Telepon / WhatsApp</span>
                        <div class="font-mono text-slate-800 mt-0.5" id="detailTelepon">-</div>
                    </div>
                    <div class="col-span-2">
                        <span class="text-slate-500 text-xs">Alamat Pengiriman</span>
                        <div class="font-medium text-slate-800 mt-0.5" id="detailAlamat">-</div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                    <span class="font-semibold text-slate-700">Saldo Piutang Berjalan</span>
                    <span class="text-sm font-bold text-kp-blue-700 font-mono tabular-nums" id="detailPiutang">Rp 0</span>
                </div>
            </div>

            <div class="px-5 py-3.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end">
                <button type="button" onclick="closeDetailModal()" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    @if($canManage)
        <!-- Modal Tambah Agen Baru -->
        <div id="modalTambahAgen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs hidden">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full max-h-[92vh] overflow-y-auto">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Tambah Agen Baru</h3>
                    <button type="button" onclick="document.getElementById('modalTambahAgen').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('sirkulasi.tabel.agen.store') }}" class="p-5 space-y-3.5">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kode Agen (Opsional)</label>
                            <input type="text" name="kode_agen" placeholder="AG-11-XXX" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Agen</label>
                            <select name="kategori" required class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                <option value="11">11 - Agen Besar</option>
                                <option value="99">99 - Sub Agen</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Agen / Kios</label>
                        <input type="text" name="nama_agen" placeholder="Contoh: Kios Koran Mitra Priangan" required class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Pengiriman</label>
                        <textarea name="alamat" rows="2" placeholder="Nama jalan, pasar/kios, kota..." required class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Wilayah Pemasaran</label>
                            <select name="wilayah_kode" required class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                @foreach($wilayahList as $w)
                                    <option value="{{ $w->kode }}">{{ $w->kode }} - {{ $w->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jurusan Armada</label>
                            <select name="jurusan_kode" required class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                @foreach($jurusanList as $jur)
                                    <option value="{{ $jur->kode }}">{{ $jur->kode }} - {{ $jur->nama_jurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jatah Default</label>
                            <input type="number" name="jatah_default" value="100" min="0" required class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono text-right tabular-nums focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Plafon Piutang</label>
                            <input type="number" name="plafon_piutang" value="5000000" min="0" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono text-right tabular-nums focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">No. Telepon</label>
                            <input type="text" name="telepon" placeholder="08..." class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                        <button type="button" onclick="document.getElementById('modalTambahAgen').classList.add('hidden')" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer">
                            Simpan Agen
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Agen -->
        <div id="modalEditAgen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs hidden">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full max-h-[92vh] overflow-y-auto">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Ubah Data Agen</h3>
                    <button type="button" onclick="document.getElementById('modalEditAgen').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form id="formEditAgen" method="POST" action="" class="p-5 space-y-3.5">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kode Agen</label>
                            <input type="text" id="editKodeAgen" disabled class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-100 text-sm text-slate-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Agen</label>
                            <select name="kategori" id="editKategori" required class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                <option value="11">11 - Agen Besar</option>
                                <option value="99">99 - Sub Agen</option>
                                <option value="00">00 - Agen Non Aktif</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Agen / Kios</label>
                        <input type="text" name="nama_agen" id="editNamaAgen" required class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Pengiriman</label>
                        <textarea name="alamat" id="editAlamat" rows="2" required class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Wilayah Pemasaran</label>
                            <select name="wilayah_kode" id="editWilayahKode" required class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                @foreach($wilayahList as $w)
                                    <option value="{{ $w->kode }}">{{ $w->kode }} - {{ $w->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jurusan Armada</label>
                            <select name="jurusan_kode" id="editJurusanKode" required class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                @foreach($jurusanList as $jur)
                                    <option value="{{ $jur->kode }}">{{ $jur->kode }} - {{ $jur->nama_jurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jatah Default</label>
                            <input type="number" name="jatah_default" id="editJatahDefault" min="0" required class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono text-right tabular-nums focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Plafon Piutang</label>
                            <input type="number" name="plafon_piutang" id="editPlafonPiutang" min="0" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono text-right tabular-nums focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">No. Telepon</label>
                            <input type="text" name="telepon" id="editTelepon" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                        <button type="button" onclick="document.getElementById('modalEditAgen').classList.add('hidden')" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <script>
        function openDetailModal(agen) {
            document.getElementById('detailNamaAgen').innerText = agen.nama_agen || '-';
            document.getElementById('detailKodeAgen').innerText = (agen.kode_agen || '-') + ' • ' + (agen.induk_agen && agen.induk_agen !== '-' ? 'Induk: ' + agen.induk_agen : 'Mitra Mandiri');

            let jenisLabel = '11 - Agen Besar';
            if (agen.kategori === '99') jenisLabel = '99 - Sub Agen';
            else if (agen.kategori === '00') jenisLabel = '00 - Non Aktif';
            document.getElementById('detailJenis').innerText = jenisLabel;

            let statusEl = document.getElementById('detailStatus');
            if (agen.status === 'aktif') {
                statusEl.className = 'mt-0.5 font-bold text-kp-blue-700';
                statusEl.innerText = 'Aktif';
            } else {
                statusEl.className = 'mt-0.5 font-bold text-slate-500';
                statusEl.innerText = 'Non-Aktif';
            }

            document.getElementById('detailTglDaftar').innerText = agen.tanggal_daftar || '-';
            document.getElementById('detailWilayah').innerText = (agen.wilayah_kode ? agen.wilayah_kode + ' - ' : '') + (agen.wilayah_nama || '-');
            document.getElementById('detailJurusan').innerText = (agen.jurusan_kode ? 'Rute #' + agen.jurusan_kode + ' - ' : '') + (agen.jurusan_nama || '-');
            document.getElementById('detailJatah').innerText = new Intl.NumberFormat('id-ID').format(agen.jatah_default || 0) + ' eks / hari';
            document.getElementById('detailTelepon').innerText = agen.telepon || '-';
            document.getElementById('detailAlamat').innerText = agen.alamat || '-';
            document.getElementById('detailPiutang').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(agen.saldo_piutang || 0);

            document.getElementById('modalDetailAgen').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('modalDetailAgen').classList.add('hidden');
        }

        function openEditModal(agen) {
            const form = document.getElementById('formEditAgen');
            if (!form) return;
            form.action = "{{ url('/sirkulasi/tabel/agen') }}/" + agen.id;
            document.getElementById('editKodeAgen').value = agen.kode_agen || '';
            document.getElementById('editKategori').value = agen.kategori || '11';
            document.getElementById('editNamaAgen').value = agen.nama_agen || '';
            document.getElementById('editAlamat').value = agen.alamat || '';
            document.getElementById('editWilayahKode').value = agen.wilayah_kode || '01';
            document.getElementById('editJurusanKode').value = agen.jurusan_kode || '06';
            document.getElementById('editJatahDefault').value = agen.jatah_default ?? 0;
            document.getElementById('editPlafonPiutang').value = agen.plafon_piutang ?? 0;
            document.getElementById('editTelepon').value = agen.telepon || '';
            document.getElementById('modalEditAgen').classList.remove('hidden');
        }
    </script>
</x-app-layout>
