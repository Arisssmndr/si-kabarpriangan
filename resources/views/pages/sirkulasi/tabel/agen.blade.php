<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Standar Divisi Iklan, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Data Agen &amp; Sub-Agen
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Direktori mitra keagenan koran, jatah harian, wilayah drop point, dan saldo piutang.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('sirkulasi.cetak.alamat-agen') }}"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Direktori</span>
                </a>
                @if(auth()->user()?->isAdmin())
                    <button type="button"
                            onclick="document.getElementById('modalTambahAgen').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow transition cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Tambah Agen</span>
                    </button>
                @endif
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

        <!-- Toolbar Pencarian & Filter (Selaras Divisi Iklan) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.tabel.agen') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Data</label>
                    <input type="text"
                           name="search"
                           value="{{ $search ?? '' }}"
                           placeholder="Ketik kode agen, nama agen, atau alamat..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Agen</label>
                    <select name="kategori" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Jenis</option>
                        <option value="11" {{ ($kategoriFilter ?? '') === '11' ? 'selected' : '' }}>11 - Agen Besar</option>
                        <option value="99" {{ ($kategoriFilter ?? '') === '99' ? 'selected' : '' }}>99 - Sub Agen</option>
                        <option value="00" {{ ($kategoriFilter ?? '') === '00' ? 'selected' : '' }}>00 - Non Aktif</option>
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Wilayah Pemasaran</label>
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
                    @if(!empty($search) || ($kategoriFilter ?? 'all') !== 'all' || ($wilayahFilter ?? 'all') !== 'all')
                        <a href="{{ route('sirkulasi.tabel.agen') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Data Agen (Standar Divisi Iklan, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">Tgl Daftar</th>
                            <th class="py-3 px-3.5">Kode Agen</th>
                            <th class="py-3 px-3.5">Nama Agen</th>
                            <th class="py-3 px-3.5">No. Telepon</th>
                            <th class="py-3 px-3.5">Alamat</th>
                            <th class="py-3 px-3.5 text-center">Jenis</th>
                            <th class="py-3 px-3.5">Wilayah</th>
                            <th class="py-3 px-3.5 text-center">Kode Rute</th>
                            <th class="py-3 px-3.5">Nama Rute</th>
                            <th class="py-3 px-3.5 text-right">Jatah</th>
                            <th class="py-3 px-3.5 text-right w-36">Saldo Piutang</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($agenList->values() as $idx => $agen)
                            <tr class="hover:bg-slate-50/80 transition {{ $agen->status === 'non-aktif' ? 'bg-slate-50/60 opacity-70' : '' }}">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-mono font-normal">
                                    {{ $agen->tanggal_daftar ? \Carbon\Carbon::parse($agen->tanggal_daftar)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    <button type="button" onclick="openDetailModal({{ json_encode($agen) }})" class="hover:underline cursor-pointer">
                                        {{ $agen->kode_agen }}
                                    </button>
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    {{ $agen->nama_agen }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-mono font-normal">
                                    {{ $agen->telepon ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal max-w-[220px] truncate" title="{{ $agen->alamat }}">
                                    {{ $agen->alamat ?: '-' }}
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
                                <td class="py-3.5 px-3.5 text-slate-700 font-normal">
                                    {{ $agen->wilayah_nama ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center font-mono text-slate-600 font-normal">
                                    R-{{ $agen->jurusan_kode }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                    {{ $agen->jurusan_nama ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">
                                    {{ number_format($agen->jatah_default, 0, ',', '.') }} eks
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    @if($agen->saldo_piutang > 0)
                                        <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($agen->saldo_piutang, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <div class="text-right font-mono text-sm text-slate-400 font-normal">
                                            Rp 0
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($agen->status === 'aktif')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">
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
                                             class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-52 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">

                                            <button type="button"
                                                    @click="open = false; openDetailModal({{ json_encode($agen) }})"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail Agen</span>
                                            </button>

                                            @if(auth()->user()?->isAdmin())
                                                <div class="h-px bg-slate-100 my-1"></div>
                                                <form method="POST" action="{{ route('sirkulasi.tabel.agen.toggle', $agen->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    @if($agen->status === 'aktif')
                                                        <button type="submit"
                                                                onclick="return confirm('Nonaktifkan agen {{ $agen->nama_agen }}?')"
                                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition text-left cursor-pointer">
                                                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                                            <span>Set Non-Aktif</span>
                                                        </button>
                                                    @else
                                                        <button type="submit"
                                                                onclick="return confirm('Aktifkan kembali agen {{ $agen->nama_agen }}?')"
                                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-kp-blue-700 hover:bg-kp-blue-50 rounded-lg transition text-left cursor-pointer">
                                                            <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                            <span>Aktifkan Agen</span>
                                                        </button>
                                                    @endif
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="py-12 text-center text-slate-500 font-normal">
                                    Tidak ada data agen yang sesuai dengan kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Modal Detail Agen -->
    <div id="modalDetailAgen" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-xl w-full overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900" id="detailNamaAgen">Rincian Data Mitra Agen</h3>
                        <p class="text-xs text-slate-500 font-mono" id="detailKodeAgen">AG-XX-XXX</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-md transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="p-5 space-y-4 text-sm text-slate-600 max-h-[75vh] overflow-y-auto">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <div>
                        <span class="block text-xs uppercase font-semibold text-slate-500">Jenis Keagenan</span>
                        <div class="mt-1 font-bold text-slate-900 text-sm" id="detailJenis">11 - Agen Besar</div>
                    </div>
                    <div>
                        <span class="block text-xs uppercase font-semibold text-slate-500">Status</span>
                        <div class="mt-1 font-bold text-slate-900 text-sm" id="detailStatus">Aktif</div>
                    </div>
                    <div>
                        <span class="block text-xs uppercase font-semibold text-slate-500">Tgl Terdaftar</span>
                        <div class="mt-1 font-mono text-slate-700 text-sm" id="detailTglDaftar">-</div>
                    </div>
                </div>

                <div class="border border-slate-100 rounded-xl p-3.5 space-y-2.5">
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Data Distribusi &amp; Drop Point</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-slate-500 text-xs">Wilayah Pemasaran:</span>
                            <div class="font-semibold text-slate-800" id="detailWilayah">-</div>
                        </div>
                        <div>
                            <span class="text-slate-500 text-xs">Jurusan Drop Point:</span>
                            <div class="font-semibold text-slate-800" id="detailJurusan">-</div>
                        </div>
                        <div>
                            <span class="text-slate-500 text-xs">Jatah Default Harian:</span>
                            <div class="font-bold text-kp-blue-700 font-mono" id="detailJatah">0 eks</div>
                        </div>
                        <div>
                            <span class="text-slate-500 text-xs">Kontak / WhatsApp:</span>
                            <div class="font-mono text-slate-700" id="detailTelepon">-</div>
                        </div>
                    </div>
                </div>

                <div class="border border-slate-100 rounded-xl p-3.5 space-y-1.5">
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Alamat Kios / Lokasi</h4>
                    <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-2.5 rounded-lg border border-slate-100" id="detailAlamat">-</p>
                </div>

                <div class="border border-slate-100 rounded-xl p-3.5 bg-slate-50/50 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Saldo Piutang Saat Ini</span>
                    <span class="text-base font-bold text-kp-blue-700 font-mono" id="detailPiutang">Rp 0</span>
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end">
                <button type="button" onclick="closeDetailModal()" class="h-10 px-4 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-sm transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Agen Baru -->
    @if(auth()->user()?->isAdmin())
        <div id="modalTambahAgen" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-lg w-full overflow-hidden">
                <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">Pendaftaran Agen Baru</h3>
                    <button type="button" onclick="document.getElementById('modalTambahAgen').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('sirkulasi.tabel.agen.store') }}" class="p-5 space-y-3.5 text-sm">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kode Agen</label>
                            <input type="text" name="kode_agen" placeholder="Misal: AG-11-006" required class="w-full h-10 px-3 text-sm rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600 font-mono">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Jenis Agen</label>
                            <select name="kategori" required class="w-full h-10 px-3 text-sm rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                                <option value="11">11 - Agen Besar</option>
                                <option value="99">99 - Sub Agen</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Agen / Kios</label>
                        <input type="text" name="nama_agen" placeholder="Nama Agen atau Toko" required class="w-full h-10 px-3 text-sm rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                        <textarea name="alamat" rows="2" placeholder="Nama jalan, wilayah/kota..." required class="w-full px-3 py-2 text-sm rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Wilayah Pemasaran</label>
                            <select name="wilayah_kode" required class="w-full h-10 px-3 text-sm rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                                @foreach($wilayahList as $w)
                                    <option value="{{ $w->kode }}">{{ $w->kode }} - {{ $w->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Jurusan Armada</label>
                            <select name="jurusan_kode" required class="w-full h-10 px-3 text-sm rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                                @foreach($jurusanList as $jur)
                                    <option value="{{ $jur->kode }}">{{ $jur->kode }} - {{ $jur->nama_jurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Jatah Default (Eks)</label>
                            <input type="number" name="jatah_default" value="100" min="1" required class="w-full h-10 px-3 text-sm rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">No. Kontak / Telepon</label>
                            <input type="text" name="telepon" placeholder="08..." class="w-full h-10 px-3 text-sm rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" onclick="document.getElementById('modalTambahAgen').classList.add('hidden')" class="h-10 px-4 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold text-sm cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="h-10 px-4 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-sm cursor-pointer shadow-xs">
                            Simpan Agen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <script>
        function openDetailModal(agen) {
            document.getElementById('detailNamaAgen').innerText = agen.nama_agen || '-';
            document.getElementById('detailKodeAgen').innerText = (agen.kode_agen || '-') + (agen.induk_agen && agen.induk_agen !== '-' ? ' • Induk: ' + agen.induk_agen : '');

            let jenisLabel = '11 - Agen Besar';
            if (agen.kategori === '99') jenisLabel = '99 - Sub Agen';
            else if (agen.kategori === '00') jenisLabel = '00 - Non Aktif';
            document.getElementById('detailJenis').innerText = jenisLabel;

            document.getElementById('detailStatus').innerText = agen.status === 'aktif' ? 'Aktif' : 'Non-Aktif';
            document.getElementById('detailTglDaftar').innerText = agen.tanggal_daftar || '-';
            document.getElementById('detailWilayah').innerText = (agen.wilayah_kode ? agen.wilayah_kode + ' - ' : '') + (agen.wilayah_nama || '-');
            document.getElementById('detailJurusan').innerText = (agen.jurusan_kode ? 'Drop Point #' + agen.jurusan_kode + ' - ' : '') + (agen.jurusan_nama || '-');
            document.getElementById('detailJatah').innerText = new Intl.NumberFormat('id-ID').format(agen.jatah_default || 0) + ' eks';
            document.getElementById('detailTelepon').innerText = agen.telepon || '-';
            document.getElementById('detailAlamat').innerText = agen.alamat || '-';
            document.getElementById('detailPiutang').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(agen.saldo_piutang || 0);

            document.getElementById('modalDetailAgen').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('modalDetailAgen').classList.add('hidden');
        }

        window.addEventListener('click', function(e) {
            const detailModal = document.getElementById('modalDetailAgen');
            if (e.target === detailModal) {
                closeDetailModal();
            }
        });
    </script>
</x-app-layout>
