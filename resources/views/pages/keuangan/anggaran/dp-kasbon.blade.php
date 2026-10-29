<x-app-layout>
    <div x-data="{ createModal: false }" class="space-y-6">

        <!-- Header DP Kasbon Sesuai DOS ANGGARAN.EXE -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shadow-xs shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-600 animate-pulse"></span>
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">
                            DP KASBON (UANG MUKA &amp; PANJAR OPERASIONAL)
                        </h1>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                            Menu Entry DOS
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pencatatan dan pengawasan uang muka/panjar kas sementara divisi sebelum dipertanggungjawabkan menjadi BP definitif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" @click="createModal = true" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-xs hover:shadow-md transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span>+ Daftarkan Kasbon Baru</span>
                </button>
            </div>
        </div>

        <!-- 3 Stat Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Kasbon Aktif (Belum LPJ)</span>
                <div class="mt-1 text-xl font-black font-mono text-amber-600">
                    Rp {{ number_format($totalKasbonAktif, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-amber-700 font-semibold">
                    {{ $jumlahAktif }} transaksi belum diselesaikan
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Kasbon Telah Selesai (Di-LPJ-kan)</span>
                <div class="mt-1 text-xl font-black font-mono text-emerald-600">
                    Rp {{ number_format($totalKasbonSelesai, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-500">Telah dipertanggungjawabkan resmi</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Format Penomoran Kasbon DOS</span>
                <div class="mt-1 text-base font-black font-mono text-slate-800">
                    {{ $suggestedNoKb }}
                </div>
                <div class="mt-1 text-[11px] text-slate-500">Format standar KB-YYYYMM-XXXX</div>
            </div>
        </div>

        <!-- FILTER & SEARCH -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-1.5">
                <a href="{{ route('keuangan.anggaran.entry.dp-kasbon', ['status' => 'semua']) }}" 
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $filterStatus === 'semua' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    Semua
                </a>
                <a href="{{ route('keuangan.anggaran.entry.dp-kasbon', ['status' => 'aktif']) }}" 
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $filterStatus === 'aktif' ? 'bg-amber-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    Kasbon Aktif
                </a>
                <a href="{{ route('keuangan.anggaran.entry.dp-kasbon', ['status' => 'selesai']) }}" 
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $filterStatus === 'selesai' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    Selesai
                </a>
            </div>

            <form method="GET" action="{{ route('keuangan.anggaran.entry.dp-kasbon') }}" class="flex items-center gap-2">
                <input type="hidden" name="status" value="{{ $filterStatus }}">
                <div class="relative w-64">
                    <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Cari nama pemohon, keperluan..." 
                           class="w-full text-xs pl-8 pr-3 py-1.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-amber-500 text-slate-800">
                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </span>
                </div>
                <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition">
                    Cari
                </button>
            </form>
        </div>

        <!-- TABEL DATA KASBON -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100/80 border-b border-slate-200 text-slate-700 text-[11px] font-extrabold uppercase tracking-wider">
                            <th class="py-3 px-4 w-36">No. Kasbon</th>
                            <th class="py-3 px-4 w-28">Tanggal</th>
                            <th class="py-3 px-4 w-48">Penerima &amp; Divisi</th>
                            <th class="py-3 px-4 min-w-[240px]">Keperluan Uang Muka</th>
                            <th class="py-3 px-4 w-36 text-right">Nominal (Rp)</th>
                            <th class="py-3 px-4 w-28 text-center">Jatuh Tempo</th>
                            <th class="py-3 px-4 w-24 text-center">Status</th>
                            <th class="py-3 px-4 w-28 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/80 text-slate-700">
                        @forelse($kasbonList as $kb)
                            <tr class="hover:bg-amber-50/20 transition align-middle">
                                <td class="py-4 px-4 font-mono font-bold text-slate-900">
                                    {{ $kb->no_kasbon }}
                                </td>
                                <td class="py-4 px-4 font-mono text-[11px] text-slate-600">
                                    {{ $kb->tanggal_kasbon->format('d/m/Y') }}
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900">{{ $kb->penerima }}</div>
                                    <div class="text-[10px] text-slate-500 uppercase font-semibold">Divisi {{ $kb->divisi }} (Bag. {{ $kb->kode_bagian }})</div>
                                </td>
                                <td class="py-4 px-4">
                                    <p class="text-xs text-slate-700 leading-relaxed font-normal">
                                        {{ $kb->keperluan }}
                                    </p>
                                </td>
                                <td class="py-4 px-4 text-right font-mono font-black text-amber-700 text-xs">
                                    Rp {{ number_format($kb->nominal, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 text-center font-mono text-[11px] text-slate-600">
                                    {{ $kb->tgl_jatuh_tempo ? $kb->tgl_jatuh_tempo->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @if($kb->status === 'aktif')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Selesai
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @if($kb->status === 'aktif')
                                        <form action="{{ route('keuangan.anggaran.entry.dp-kasbon.selesai', $kb->id) }}" method="POST" onsubmit="return confirm('Tandai kasbon ini telah selesai / di-LPJ-kan?');">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-2xs transition">
                                                Selesaikan
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-400 text-xs">✓ Lunas</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400 italic">
                                    Tidak ada data kasbon yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL FORM REGISTRASI KASBON BARU (Alpine.js) -->
        <div x-cloak x-show="createModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="createModal" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" 
                     @click="createModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="createModal" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    
                    <form action="{{ route('keuangan.anggaran.entry.dp-kasbon.store') }}" method="POST">
                        @csrf
                        <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                            <div>
                                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">
                                    Registrasi DP Kasbon Operasional Baru
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">Sistem DOS ANGGARAN.EXE</p>
                            </div>
                            <button type="button" @click="createModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">✕</button>
                        </div>

                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Kasbon <span class="text-rose-500">*</span></label>
                                    <input type="text" name="no_kasbon" value="{{ $suggestedNoKb }}" required
                                           class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 px-3 py-2 bg-slate-50">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Kasbon <span class="text-rose-500">*</span></label>
                                    <input type="date" name="tanggal_kasbon" value="{{ date('Y-m-d') }}" required
                                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Bidang / Divisi <span class="text-rose-500">*</span></label>
                                    <select name="divisi" required class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2">
                                        <option value="iklan">01 - Iklan</option>
                                        <option value="sirkulasi">02 - Sirkulasi</option>
                                        <option value="sekum" selected>03 - Sekum &amp; SDM</option>
                                        <option value="akuntansi">04 - Akuntansi</option>
                                        <option value="keuangan">05 - Keuangan</option>
                                        <option value="redaksi">06 - Redaksi</option>
                                        <option value="management_fee">07 - Pimpinan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Penerima <span class="text-rose-500">*</span></label>
                                    <input type="text" name="penerima" required placeholder="Nama staf pemegang uang muka..." 
                                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Panjar (Rp) <span class="text-rose-500">*</span></label>
                                    <input type="number" name="nominal" min="1000" step="50000" required placeholder="Contoh: 1500000"
                                           class="w-full text-sm font-mono font-black rounded-xl border border-slate-300 px-3 py-2 text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Batas Waktu LPJ</label>
                                    <input type="date" name="tgl_jatuh_tempo" value="{{ date('Y-m-d', strtotime('+7 days')) }}" 
                                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Rincian Keperluan <span class="text-rose-500">*</span></label>
                                <textarea name="keperluan" rows="3" required placeholder="Jelaskan penggunaan panjar kasbon sementara ini..." 
                                          class="w-full text-xs rounded-xl border border-slate-300 p-3 leading-relaxed"></textarea>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2">
                            <button type="button" @click="createModal = false" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                Daftarkan Kasbon
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
