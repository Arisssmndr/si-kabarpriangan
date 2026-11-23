<x-app-layout>
    <div class="space-y-5" x-data="{
        showTambahModal: false,
        showEditModal: false,
        showDetailModal: false,
        activeAgen: {},
        editData: {
            id: '',
            kode_agen: '',
            nama_agen: '',
            kategori: '11',
            induk_agen: '',
            wilayah_kode: '01',
            jurusan_kode: '01',
            alamat: '',
            telepon: '',
            jatah_default: 100,
            plafon_piutang: 5000000
        }
    }">

        <!-- Header Halaman (Standar Divisi Iklan, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Kelola Mitra Agen Sirkulasi
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl" title="Pengaturan master data keagenan koran, penentuan klasifikasi tarif, rute distribusi, plafon kredit piutang, dan jatah default.">
                        Otoritas Akuntansi: Master mitra agen koran, tarif (00, 11, 99), rute, plafon piutang &amp; jatah.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('accounting.cetak.sirkulasi') }}"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Kartu Piutang Agen</span>
                </a>

                <button type="button"
                        @click="showTambahModal = true"
                        class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah Agen Baru</span>
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

        <!-- Ringkasan KPI Keagenan (60-30-10) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Mitra Agen</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ $stats['total_agen'] }}</p>
                <span class="text-xs text-slate-400 mt-1 block">Terdaftar di sistem</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Agen Besar (11)</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">{{ $stats['agen_besar'] }}</p>
                <span class="text-xs text-slate-400 mt-1 block">Tarif Rp 2.800</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sub Agen / Kios (99)</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ $stats['sub_agen'] }}</p>
                <span class="text-xs text-slate-400 mt-1 block">Tarif Konsinyasi Rp 3.000</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Non-Aktif (00)</span>
                <p class="text-2xl font-bold text-slate-500 mt-1 font-mono tabular-nums">{{ $stats['nonaktif'] }}</p>
                <span class="text-xs text-slate-400 mt-1 block">Terkunci jatah</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Piutang</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">Rp {{ number_format($stats['total_piutang'], 0, ',', '.') }}</p>
                <span class="text-xs text-slate-400 mt-1 block">Piutang berjalan koran</span>
            </div>
        </div>

        <!-- Toolbar Pencarian & Filter -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.sirkulasi.agen') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Data</label>
                    <input type="text"
                           name="search"
                           value="{{ $search ?? '' }}"
                           placeholder="Ketik kode agen, nama agen, telepon, atau alamat..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Agen</label>
                    <select name="kategori" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Jenis</option>
                        @foreach($jenisList as $j)
                            <option value="{{ $j->kode }}" {{ ($kategoriFilter ?? '') === $j->kode ? 'selected' : '' }}>
                                {{ $j->label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Wilayah Pemasaran</label>
                    <select name="wilayah" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Wilayah</option>
                        @foreach($wilayahList as $w)
                            <option value="{{ $w->nama }}" {{ ($wilayahFilter ?? '') === $w->nama ? 'selected' : '' }}>
                                {{ $w->kode }} - {{ $w->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Rute Armada</label>
                    <select name="jurusan" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Rute</option>
                        @foreach($jurusanList as $jur)
                            <option value="{{ $jur->kode }}" {{ ($jurusanFilter ?? '') === $jur->kode ? 'selected' : '' }}>
                                {{ $jur->kode }} - {{ $jur->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Filter
                    </button>
                    @if(!empty($search) || ($kategoriFilter ?? 'all') !== 'all' || ($wilayahFilter ?? 'all') !== 'all' || ($jurusanFilter ?? 'all') !== 'all')
                        <a href="{{ route('accounting.sirkulasi.agen') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
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
                            <th class="py-3 px-3.5 text-center w-16">Kode</th>
                            <th class="py-3 px-3.5">Nama Agen &amp; Penanggung Jawab</th>
                            <th class="py-3 px-3.5">Jenis Klasifikasi</th>
                            <th class="py-3 px-3.5">Wilayah Pemasaran</th>
                            <th class="py-3 px-3.5">Rute Armada</th>
                            <th class="py-3 px-3.5 text-right">Default Jatah</th>
                            <th class="py-3 px-3.5 text-right">Plafon Kredit</th>
                            <th class="py-3 px-3.5 text-right">Saldo Piutang</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($agenList as $agen)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center font-mono font-bold text-kp-blue-700 text-sm">
                                    {{ $agen->kode_agen }}
                                </td>
                                <td class="py-3.5 px-3.5">
                                    <div class="font-medium text-slate-900">{{ $agen->nama_agen }}</div>
                                    <div class="text-xs text-slate-400">PJ: {{ $agen->induk_agen ?: '-' }} &bull; Telp: {{ $agen->telepon ?: '-' }}</div>
                                </td>
                                <td class="py-3.5 px-3.5">
                                    @if($agen->kategori === '11')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            11 - Agen Besar
                                        </span>
                                    @elseif($agen->kategori === '99')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            99 - Sub Agen
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                            00 - Non Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600">
                                    {{ $agen->wilayah }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-600">
                                    {{ $agen->rute_kode }} - {{ optional($jurusanList->firstWhere('kode', $agen->rute_kode))->nama_jurusan ?? 'Rute Standar' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">
                                    {{ number_format($agen->jatah_default, 0, ',', '.') }} eks
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums">
                                    Rp {{ number_format($agen->plafon_piutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">
                                    Rp {{ number_format($agen->saldo_piutang, 0, ',', '.') }}
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
                                                    @click="
                                                        open = false;
                                                        activeAgen = {{ json_encode($agen) }};
                                                        showDetailModal = true;
                                                    "
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail Agen</span>
                                            </button>

                                            <div class="h-px bg-slate-100 my-1"></div>

                                            <button type="button"
                                                    @click="
                                                        open = false;
                                                        editData = {
                                                            id: {{ $agen->id }},
                                                            kode_agen: '{{ $agen->kode_agen }}',
                                                            nama_agen: '{{ addslashes($agen->nama_agen) }}',
                                                            kategori: '{{ $agen->kategori }}',
                                                            induk_agen: '{{ addslashes($agen->induk_agen) }}',
                                                            wilayah_kode: '{{ optional($wilayahList->firstWhere('nama', $agen->wilayah))->kode ?? '01' }}',
                                                            jurusan_kode: '{{ $agen->rute_kode ?? '01' }}',
                                                            alamat: '{{ addslashes($agen->alamat ?? '') }}',
                                                            telepon: '{{ addslashes($agen->telepon ?? '') }}',
                                                            jatah_default: {{ (int) $agen->jatah_default }},
                                                            plafon_piutang: {{ (float) $agen->plafon_piutang }}
                                                        };
                                                        showEditModal = true;
                                                    "
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                <span>Edit Data Agen</span>
                                            </button>

                                            <form method="POST" action="{{ route('accounting.sirkulasi.agen.toggle', $agen->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition text-left cursor-pointer">
                                                    @if($agen->status === 'aktif')
                                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                                        <span>Set Non-Aktif (00)</span>
                                                    @else
                                                        <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                        <span>Aktifkan Agen</span>
                                                    @endif
                                                </button>
                                            </form>

                                            <div class="h-px bg-slate-100 my-1"></div>

                                            <form method="POST" action="{{ route('accounting.sirkulasi.agen.destroy', $agen->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        onclick="return confirmSubmit(this, event, {
                                                            title: 'Hapus Agen?',
                                                            text: 'Apakah Anda yakin ingin menghapus agen <strong>{{ addslashes($agen->nama_agen) }}</strong>? Agen dengan saldo piutang tidak dapat dihapus.',
                                                            theme: 'rose',
                                                            icon: 'trash',
                                                            confirmText: 'Ya, Hapus'
                                                        })"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50 rounded-lg transition text-left cursor-pointer">
                                                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    <span>Hapus Agen</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-400">
                                    Tidak ada data agen yang sesuai dengan kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL TAMBAH AGEN BARU -->
        <div x-cloak
             x-show="showTambahModal"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="bg-white w-full max-w-2xl rounded-2xl border border-slate-200 shadow-2xl overflow-hidden p-6 space-y-4 max-h-[90vh] overflow-y-auto"
                 @click.outside="showTambahModal = false">

                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Registrasi Mitra Agen Baru</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Penetapan klasifikasi tarif, rute distribusi, dan limit plafon kredit.</p>
                    </div>
                    <button type="button" @click="showTambahModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('accounting.sirkulasi.agen.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Agen (Opsional)</label>
                            <input type="text" name="kode_agen" placeholder="Otomatis digenerate jika kosong"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Agen / Kios / Toko <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_agen" required placeholder="Contoh: Agen Priangan Jaya"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Penanggung Jawab / Induk Agen</label>
                            <input type="text" name="induk_agen" placeholder="Nama pemilik atau agen induk"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                            <input type="text" name="telepon" placeholder="08xxxxxxxxxx"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Agen <span class="text-rose-500">*</span></label>
                            <select name="kategori" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                @foreach($jenisList as $j)
                                    <option value="{{ $j->kode }}">{{ $j->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Wilayah Pemasaran <span class="text-rose-500">*</span></label>
                            <select name="wilayah_kode" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                @foreach($wilayahList as $w)
                                    <option value="{{ $w->kode }}">{{ $w->kode }} - {{ $w->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Rute Armada Distribusi <span class="text-rose-500">*</span></label>
                            <select name="jurusan_kode" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                @foreach($jurusanList as $jur)
                                    <option value="{{ $jur->kode }}">{{ $jur->kode }} - {{ $jur->nama_jurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Default Jatah (Eksemplar) <span class="text-rose-500">*</span></label>
                            <input type="number" name="jatah_default" min="0" required value="100"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Plafon Kredit Piutang (Rp)</label>
                            <input type="number" name="plafon_piutang" min="0" value="5000000"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Lengkap Drop Point</label>
                        <textarea name="alamat" rows="2.5" placeholder="Alamat drop koran subuh dan patokan lokasi..."
                                  class="w-full p-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="showTambahModal = false"
                                class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-xs">
                            Simpan Data Agen
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL EDIT AGEN -->
        <div x-cloak
             x-show="showEditModal"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="bg-white w-full max-w-2xl rounded-2xl border border-slate-200 shadow-2xl overflow-hidden p-6 space-y-4 max-h-[90vh] overflow-y-auto"
                 @click.outside="showEditModal = false">

                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Edit Data Agen: <span class="font-mono text-kp-blue-700" x-text="editData.kode_agen"></span></h2>
                        <p class="text-xs text-slate-500 mt-0.5">Perubahan klasifikasi tarif, rute distribusi, atau limit kredit.</p>
                    </div>
                    <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" :action="'{{ url('/accounting/sirkulasi/agen') }}/' + editData.id" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Agen</label>
                            <input type="text" disabled x-model="editData.kode_agen"
                                   class="w-full h-10 px-3 bg-slate-100 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-500 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Agen / Kios / Toko <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_agen" required x-model="editData.nama_agen"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Penanggung Jawab / Induk Agen</label>
                            <input type="text" name="induk_agen" x-model="editData.induk_agen"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                            <input type="text" name="telepon" x-model="editData.telepon"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Agen <span class="text-rose-500">*</span></label>
                            <select name="kategori" required x-model="editData.kategori" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                @foreach($jenisList as $j)
                                    <option value="{{ $j->kode }}">{{ $j->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Wilayah Pemasaran <span class="text-rose-500">*</span></label>
                            <select name="wilayah_kode" required x-model="editData.wilayah_kode" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                @foreach($wilayahList as $w)
                                    <option value="{{ $w->kode }}">{{ $w->kode }} - {{ $w->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Rute Armada Distribusi <span class="text-rose-500">*</span></label>
                            <select name="jurusan_kode" required x-model="editData.jurusan_kode" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                @foreach($jurusanList as $jur)
                                    <option value="{{ $jur->kode }}">{{ $jur->kode }} - {{ $jur->nama_jurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Default Jatah (Eksemplar) <span class="text-rose-500">*</span></label>
                            <input type="number" name="jatah_default" min="0" required x-model="editData.jatah_default"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Plafon Kredit Piutang (Rp)</label>
                            <input type="number" name="plafon_piutang" min="0" x-model="editData.plafon_piutang"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Lengkap Drop Point</label>
                        <textarea name="alamat" rows="2.5" x-model="editData.alamat"
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

        <!-- MODAL DETAIL AGEN -->
        <div x-cloak
             x-show="showDetailModal"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="bg-white w-full max-w-lg rounded-2xl border border-slate-200 shadow-2xl overflow-hidden p-6 space-y-4"
                 @click.outside="showDetailModal = false">

                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900" x-text="activeAgen.nama_agen"></h2>
                        <span class="text-xs font-mono font-bold text-kp-blue-700" x-text="activeAgen.kode_agen"></span>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div>
                            <span class="text-slate-400 block uppercase font-mono text-[10px]">Klasifikasi</span>
                            <strong class="text-slate-900" x-text="activeAgen.kategori === '11' ? '11 - Agen Besar' : (activeAgen.kategori === '99' ? '99 - Sub Agen' : '00 - Non Aktif')"></strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block uppercase font-mono text-[10px]">Wilayah Pemasaran</span>
                            <strong class="text-slate-900" x-text="activeAgen.wilayah"></strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block uppercase font-mono text-[10px]">Rute Distribusi</span>
                            <strong class="text-slate-900 font-mono" x-text="activeAgen.rute_kode"></strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block uppercase font-mono text-[10px]">Jatah Default</span>
                            <strong class="text-slate-900 font-mono" x-text="activeAgen.jatah_default + ' eks'"></strong>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div>
                            <span class="text-slate-400 block uppercase font-mono text-[10px]">Plafon Kredit</span>
                            <strong class="text-slate-900 font-mono" x-text="'Rp ' + Number(activeAgen.plafon_piutang || 0).toLocaleString('id-ID')"></strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block uppercase font-mono text-[10px]">Saldo Piutang</span>
                            <strong class="text-kp-blue-700 font-mono font-bold" x-text="'Rp ' + Number(activeAgen.saldo_piutang || 0).toLocaleString('id-ID')"></strong>
                        </div>
                    </div>

                    <div>
                        <span class="text-slate-400 block uppercase font-mono text-[10px] mb-1">Alamat Drop Point</span>
                        <p class="text-slate-800 leading-relaxed p-3 bg-slate-50 rounded-xl border border-slate-200" x-text="activeAgen.alamat || 'Belum diisi'"></p>
                    </div>

                    <div>
                        <span class="text-slate-400 block uppercase font-mono text-[10px] mb-1">Kontak Penanggung Jawab</span>
                        <p class="text-slate-800 p-3 bg-slate-50 rounded-xl border border-slate-200">
                            Nama: <strong x-text="activeAgen.induk_agen || '-'"></strong> &bull; Telepon: <strong x-text="activeAgen.telepon || '-'"></strong>
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end pt-2">
                    <button type="button" @click="showDetailModal = false"
                            class="h-10 px-5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
