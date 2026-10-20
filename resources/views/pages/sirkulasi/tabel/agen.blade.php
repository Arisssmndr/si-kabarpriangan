<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <circle cx="12" cy="7.5" r="3" />
                        <path stroke-linecap="round" d="M5.5 19.5c0-3.3 2.9-6 6.5-6s6.5 2.7 6.5 6" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Tabel Master</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Data Agen & Sub-Agen</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Master direktori mitra distribusi: Pemantauan kode agen, jatah default, jurusan armada, dan status operasional
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>Mode Pemantauan</span>
                </span>
                <a href="{{ route('sirkulasi.cetak.alamat-agen') }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition-colors inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak Direktori Agen</span>
                </a>
                @if(auth()->user()?->isAdmin())
                    <button type="button" 
                            onclick="document.getElementById('modalTambahAgen').classList.remove('hidden')" 
                            class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-amber-600 hover:bg-amber-700 text-white shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        <span>+ Tambah Agen (Admin)</span>
                    </button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-medium text-emerald-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs font-medium text-rose-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Banner Akses Pemantauan Sirkulasi -->
        <div class="p-4 bg-sky-50 border border-sky-200 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-sky-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <div class="text-xs text-sky-900 leading-relaxed">
                <strong class="font-bold">Akses Pemantauan Master Agen (Read-Only):</strong> Admin sirkulasi hanya berwenang untuk <strong>melihat dan memantau</strong> data mitra, alokasi jatah default harian, dan drop point ekspedisi tanpa hak menambah atau mengedit data agen. Pengelolaan master data agen dilakukan terpusat oleh Administrator guna menjamin konsistensi kartu piutang akuntansi.
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.tabel.agen') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                <!-- Search Input -->
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Cari Kode / Nama / Alamat</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Ketik kode (AG-11-...) atau nama..." class="w-full text-xs rounded-lg border-slate-200 pl-8 pr-3 py-1.5 focus:border-amber-500 focus:ring-amber-500">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                </div>

                <!-- Filter Kategori (00, 11, 99) -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Jenis Agen (SIRK.EXE)</label>
                    <select name="kategori" class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-amber-500 focus:ring-amber-500">
                        <option value="all">Semua Jenis</option>
                        <option value="11" {{ ($kategoriFilter ?? '') === '11' ? 'selected' : '' }}>11 - Agen Besar</option>
                        <option value="99" {{ ($kategoriFilter ?? '') === '99' ? 'selected' : '' }}>99 - Sub Agen</option>
                        <option value="00" {{ ($kategoriFilter ?? '') === '00' ? 'selected' : '' }}>00 - Agen Non Aktif (Arsip)</option>
                    </select>
                </div>

                <!-- Filter Wilayah -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Wilayah Pemasaran</label>
                    <select name="wilayah" class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-amber-500 focus:ring-amber-500">
                        <option value="all">Semua Wilayah</option>
                        @foreach($wilayahList as $w)
                            <option value="{{ $w->kode }}" {{ ($wilayahFilter ?? '') === $w->kode ? 'selected' : '' }}>
                                {{ $w->kode }} - {{ $w->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit / Reset -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-1.5 px-3 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-semibold transition-colors cursor-pointer">
                        Terapkan
                    </button>
                    <a href="{{ route('sirkulasi.tabel.agen') }}" class="py-1.5 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-semibold transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabel Data Agen -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Daftar Agen Terdaftar</h3>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 font-bold">
                        {{ $agenList->count() }} Record
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-[11px] text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> 11 Agen Besar
                    </span>
                    <span class="inline-flex items-center gap-1 text-[11px] text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> 99 Sub Agen
                    </span>
                    <span class="inline-flex items-center gap-1 text-[11px] text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> 00 Non-Aktif
                    </span>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 min-w-[1050px]">
                    <thead class="bg-slate-100/75 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3.5 text-center whitespace-nowrap w-28">Tgl Daftar</th>
                            <th class="py-3 px-3.5 whitespace-nowrap w-32">Kode Agen</th>
                            <th class="py-3 px-4 min-w-[180px]">Nama Agen & Kios</th>
                            <th class="py-3 px-4 min-w-[200px]">Alamat Lengkap</th>
                            <th class="py-3 px-3 text-center whitespace-nowrap w-28">Jenis</th>
                            <th class="py-3 px-3.5 whitespace-nowrap min-w-[120px]">Wilayah</th>
                            <th class="py-3 px-3.5 text-right whitespace-nowrap w-28">Jatah Default</th>
                            <th class="py-3 px-3.5 text-right whitespace-nowrap w-32">Saldo Piutang</th>
                            <th class="py-3 px-3.5 text-center whitespace-nowrap w-28">Status</th>
                            <th class="py-3 px-3.5 text-center whitespace-nowrap w-24">Aksi / Info</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($agenList as $agen)
                            <tr class="hover:bg-slate-50/80 transition-colors {{ $agen->status === 'non-aktif' ? 'bg-slate-50/50' : '' }}">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-mono text-[11px] whitespace-nowrap">
                                    {{ $agen->tanggal_daftar }}
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-1 rounded-md font-mono font-bold text-slate-900 bg-slate-100 text-xs border border-slate-200/80">
                                        {{ $agen->kode_agen }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 text-xs">{{ $agen->nama_agen }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1">
                                        <span>Telp:</span>
                                        <span class="font-mono text-slate-500">{{ $agen->telepon }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 text-xs leading-relaxed max-w-xs">
                                    {{ $agen->alamat }}
                                </td>
                                <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                    @if($agen->kategori === '11')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            <span>11 Besar</span>
                                        </span>
                                    @elseif($agen->kategori === '99')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>99 Sub</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <span>00 Pasif</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800 text-xs">{{ $agen->wilayah_nama }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Drop Point #{{ $agen->jurusan_kode }}</div>
                                </td>
                                <td class="py-3.5 px-3.5 text-right whitespace-nowrap">
                                    <span class="font-bold text-slate-900 text-xs">{{ number_format($agen->jatah_default, 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-slate-400 font-medium">eks</span>
                                </td>
                                <td class="py-3.5 px-3.5 text-right whitespace-nowrap">
                                    <span class="font-extrabold text-xs {{ $agen->saldo_piutang > 0 ? 'text-rose-600' : 'text-slate-700' }}">
                                        Rp {{ number_format($agen->saldo_piutang, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                    @if($agen->status === 'aktif')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Aktif</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <span>Non-Aktif</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Tombol Detail Pemantauan (Tersedia untuk Sirkulasi & Semua Pengguna) -->
                                        <button type="button" 
                                                onclick='openDetailModal(@json($agen))' 
                                                class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition-colors cursor-pointer"
                                                title="Lihat Rincian Data Agen (Mode Pemantauan)">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Detail</span>
                                        </button>

                                        <!-- Kontrol Status (Hanya Muncul untuk Administrator) -->
                                        @if(auth()->user()?->isAdmin())
                                            <form method="POST" action="{{ route('sirkulasi.tabel.agen.toggle', $agen->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                @if($agen->status === 'aktif')
                                                    <button type="submit" 
                                                            title="Set Non-Aktif (00) - Khusus Admin"
                                                            onclick="return confirm('Apakah Anda yakin ingin menonaktifkan agen {{ $agen->nama_agen }}? Sesuai SOP, agen akan berubah menjadi jenis 00 (Non-Aktif) tanpa menghapus data historis piutang.')" 
                                                            class="w-7 h-7 inline-flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors border border-transparent hover:border-rose-200 cursor-pointer">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                                    </button>
                                                @else
                                                    <button type="submit" 
                                                            title="Aktifkan Kembali Agen - Khusus Admin"
                                                            onclick="return confirm('Aktifkan kembali agen {{ $agen->nama_agen }} ke dalam daftar operasional jatah harian?')" 
                                                            class="w-7 h-7 inline-flex items-center justify-center text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors border border-emerald-200 cursor-pointer">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    </button>
                                                @endif
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-8 text-center text-slate-400">
                                    Tidak ada data agen yang sesuai dengan kriteria pencarian / filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2">
                <span>Data agen terhubung langsung dengan Matriks Jatah H+1, Surat Jalan Pengepakan, dan Buku Kas Kasir.</span>
                <span class="font-semibold text-slate-600">Hak Akses: Sirkulasi Mode Pemantauan (Read-Only) &bull; Dilarang Mengubah Data Agen</span>
            </div>
        </div>

    </div>

    <!-- Modal Detail Agen (Mode Pemantauan / Read-Only) -->
    <div id="modalDetailAgen" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-xl w-full overflow-hidden animate-in fade-in zoom-in duration-150">
            <!-- Modal Header -->
            <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900" id="detailNamaAgen">Rincian Data Mitra Agen</h3>
                        <p class="text-[11px] text-slate-500 font-mono" id="detailKodeAgen">AG-XX-XXX</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Read-Only</span>
                    </span>
                    <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-md transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="p-5 space-y-4 text-xs text-slate-600 max-h-[75vh] overflow-y-auto">
                <!-- Info Status & Jenis -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <div>
                        <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Jenis Keagenan</span>
                        <div class="mt-1 font-bold text-slate-900" id="detailJenis">11 - Agen Besar</div>
                    </div>
                    <div>
                        <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Status Kemitraan</span>
                        <div class="mt-1 font-bold" id="detailStatus">Aktif</div>
                    </div>
                    <div>
                        <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Tgl Terdaftar</span>
                        <div class="mt-1 font-mono text-slate-700" id="detailTglDaftar">2024-01-10</div>
                    </div>
                </div>

                <!-- Distribusi & Operasional Sirkulasi -->
                <div class="border border-slate-100 rounded-xl p-3.5 space-y-2.5">
                    <h4 class="text-[11px] font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        <span>Data Distribusi & Drop Point</span>
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-slate-400 text-[11px]">Wilayah Pemasaran:</span>
                            <div class="font-semibold text-slate-800" id="detailWilayah">-</div>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[11px]">Jurusan Drop Point:</span>
                            <div class="font-semibold text-slate-800" id="detailJurusan">-</div>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[11px]">Jatah Default Harian:</span>
                            <div class="font-bold text-amber-700 text-sm" id="detailJatah">0 eks</div>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[11px]">Kontak / WhatsApp:</span>
                            <div class="font-mono text-slate-700" id="detailTelepon">-</div>
                        </div>
                    </div>
                </div>

                <!-- Alamat Fisik -->
                <div class="border border-slate-100 rounded-xl p-3.5 space-y-1.5">
                    <h4 class="text-[11px] font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Alamat Lengkap Kios / Pengecer</span>
                    </h4>
                    <p class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-2.5 rounded-lg border border-slate-100" id="detailAlamat">-</p>
                </div>

                <!-- Saldo Piutang Akuntansi -->
                <div class="border border-slate-100 rounded-xl p-3.5 bg-slate-50/50 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Saldo Piutang Akuntansi</span>
                        <span class="text-[10px] text-slate-400 italic">Terhubung ke Buku Piutang</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500">Kewajiban Tagihan:</span>
                        <span class="text-sm font-extrabold text-slate-900 font-mono" id="detailPiutang">Rp 0</span>
                    </div>
                </div>

                <!-- Catatan Proteksi Keamanan -->
                <div class="p-3 bg-amber-50/70 border border-amber-200/80 rounded-xl text-[11px] text-amber-900 leading-relaxed flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <strong class="font-bold">Ketentuan Akses Sirkulasi:</strong> Data mitra agen tidak dapat diubah atau diedit oleh staf sirkulasi. Jika terdapat perubahan alamat drop point, pergantian pemilik kios, atau penambahan mitra agen baru, silakan ajukan ke <strong>Administrator</strong>.
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end">
                <button type="button" onclick="closeDetailModal()" class="px-4 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-xs transition-colors cursor-pointer">
                    Tutup Rincian
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Agen Baru (Hanya untuk Super Administrator) -->
    @if(auth()->user()?->isAdmin())
        <div id="modalTambahAgen" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-lg w-full overflow-hidden">
                <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">Pendaftaran Agen Sirkulasi Baru (Khusus Administrator)</h3>
                    <button type="button" onclick="document.getElementById('modalTambahAgen').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('sirkulasi.tabel.agen.store') }}" class="p-5 space-y-3.5 text-xs">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kode Agen Baru</label>
                            <input type="text" name="kode_agen" placeholder="Misal: AG-11-006" required class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-amber-500 focus:ring-amber-500 font-mono">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Jenis Agen</label>
                            <select name="kategori" required class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-amber-500 focus:ring-amber-500">
                                <option value="11">11 - Agen Besar</option>
                                <option value="99">99 - Sub Agen</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Pemilik / Kios Koran</label>
                        <input type="text" name="nama_agen" placeholder="Nama Kios Koran atau Loper" required class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-amber-500 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Lengkap Kios / Drop Point</label>
                        <textarea name="alamat" rows="2" placeholder="Nama jalan, pasar/kios, kota..." required class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-amber-500 focus:ring-amber-500"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Wilayah Pemasaran</label>
                            <select name="wilayah_kode" required class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-amber-500 focus:ring-amber-500">
                                @foreach($wilayahList as $w)
                                    <option value="{{ $w->kode }}">{{ $w->kode }} - {{ $w->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Jurusan Armada</label>
                            <select name="jurusan_kode" required class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-amber-500 focus:ring-amber-500">
                                @foreach($jurusanList as $jur)
                                    <option value="{{ $jur->kode }}">{{ $jur->kode }} - {{ $jur->nama_jurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Jatah Default (Eks)</label>
                            <input type="number" name="jatah_default" value="100" min="1" required class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">No. Kontak / WhatsApp</label>
                            <input type="text" name="telepon" placeholder="08..." class="w-full text-xs rounded-lg border-slate-200 py-1.5 focus:border-amber-500 focus:ring-amber-500">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" onclick="document.getElementById('modalTambahAgen').classList.add('hidden')" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-semibold cursor-pointer">
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
            document.getElementById('detailKodeAgen').innerText = (agen.kode_agen || '-') + ' • ' + (agen.induk_agen && agen.induk_agen !== '-' ? 'Induk: ' + agen.induk_agen : 'Mitra Mandiri');
            
            let jenisLabel = '11 - Agen Besar';
            if (agen.kategori === '99') jenisLabel = '99 - Sub Agen';
            else if (agen.kategori === '00') jenisLabel = '00 - Non Aktif (Arsip)';
            document.getElementById('detailJenis').innerText = jenisLabel;

            let statusEl = document.getElementById('detailStatus');
            if (agen.status === 'aktif') {
                statusEl.className = 'mt-1 font-bold text-emerald-600';
                statusEl.innerText = 'Aktif (Komersil)';
            } else {
                statusEl.className = 'mt-1 font-bold text-rose-600';
                statusEl.innerText = 'Non-Aktif (Terkunci)';
            }

            document.getElementById('detailTglDaftar').innerText = agen.tanggal_daftar || '-';
            document.getElementById('detailWilayah').innerText = (agen.wilayah_kode ? agen.wilayah_kode + ' - ' : '') + (agen.wilayah_nama || '-');
            document.getElementById('detailJurusan').innerText = (agen.jurusan_kode ? 'Drop Point #' + agen.jurusan_kode + ' - ' : '') + (agen.jurusan_nama || '-');
            document.getElementById('detailJatah').innerText = new Intl.NumberFormat('id-ID').format(agen.jatah_default || 0) + ' eks / hari';
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
