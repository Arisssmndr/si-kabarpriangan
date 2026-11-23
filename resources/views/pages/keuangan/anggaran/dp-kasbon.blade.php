<x-app-layout>
    <div x-data="{ createModal: false }" class="space-y-6">

        <!-- Tier 1: Header Halaman Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-9 sm:h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            DP Kasbon (Uang Muka &amp; Panjar Operasional)
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Menu Entry DOS
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Pencatatan dan pengawasan uang muka/panjar kas sementara divisi sebelum dipertanggungjawabkan menjadi BP definitif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <button type="button" @click="createModal = true" 
                        class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Daftarkan Kasbon Baru</span>
                </button>
            </div>
        </div>

        <!-- Tier 2: 4 KPI Summary Cards Standar Divisi Iklan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kasbon Aktif (Belum LPJ)</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalKasbonAktif, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Menanti Surat Pertanggungjawaban
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jumlah Kasbon Aktif</span>
                <div class="text-2xl font-bold text-kp-blue-700 font-mono mt-1">
                    {{ $jumlahAktif }} <span class="text-sm font-normal text-slate-500">Transaksi</span>
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Belum Di-LPJ-kan
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kasbon Telah Selesai (LPJ)</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums mt-1">
                    Rp {{ number_format($totalKasbonSelesai, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Telah Dipertanggungjawabkan
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Format Nomor Kasbon</span>
                <div class="text-xl sm:text-2xl font-bold text-slate-900 font-mono mt-1">
                    {{ $suggestedNoKb }}
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Standar DOS: KB-YYYYMM-XXXX
                </div>
            </div>
        </div>

        <!-- Tier 3: Toolbar Filter & Search Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-1.5 overflow-x-auto">
                <a href="{{ route('keuangan.anggaran.entry.dp-kasbon', ['status' => 'semua']) }}" 
                   class="h-10 px-4 rounded-xl text-xs font-semibold inline-flex items-center justify-center transition {{ $filterStatus === 'semua' ? 'bg-kp-blue-600 text-white shadow-xs' : 'border border-slate-300 text-slate-700 hover:bg-slate-50' }}">
                    Semua
                </a>
                <a href="{{ route('keuangan.anggaran.entry.dp-kasbon', ['status' => 'aktif']) }}" 
                   class="h-10 px-4 rounded-xl text-xs font-semibold inline-flex items-center justify-center transition {{ $filterStatus === 'aktif' ? 'bg-kp-blue-600 text-white shadow-xs' : 'border border-slate-300 text-slate-700 hover:bg-slate-50' }}">
                    Kasbon Aktif
                </a>
                <a href="{{ route('keuangan.anggaran.entry.dp-kasbon', ['status' => 'selesai']) }}" 
                   class="h-10 px-4 rounded-xl text-xs font-semibold inline-flex items-center justify-center transition {{ $filterStatus === 'selesai' ? 'bg-kp-blue-600 text-white shadow-xs' : 'border border-slate-300 text-slate-700 hover:bg-slate-50' }}">
                    Selesai
                </a>
            </div>

            <form method="GET" action="{{ route('keuangan.anggaran.entry.dp-kasbon') }}" class="flex items-center gap-2">
                <input type="hidden" name="status" value="{{ $filterStatus }}">
                <div class="relative w-64 sm:w-72">
                    <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Cari nomor kasbon, penerima, keperluan..." 
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                </div>
                <button type="submit" class="h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl transition shadow-xs cursor-pointer">
                    Cari
                </button>
                @if(!empty($search))
                    <a href="{{ route('keuangan.anggaran.entry.dp-kasbon', ['status' => $filterStatus]) }}" class="h-11 px-4 inline-flex items-center justify-center border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Tier 4: Pure Data Table Standar Divisi Iklan (Kolom Terpisah) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3.5 px-3.5 text-center w-12">No</th>
                            <th class="py-3.5 px-3.5 min-w-[140px]">No. Kasbon</th>
                            <th class="py-3.5 px-3.5 min-w-[110px]">Tanggal</th>
                            <th class="py-3.5 px-3.5 min-w-[110px]">Jatuh Tempo</th>
                            <th class="py-3.5 px-3.5 min-w-[160px]">Nama Penerima</th>
                            <th class="py-3.5 px-3.5 text-center w-24">Kode Bagian</th>
                            <th class="py-3.5 px-3.5 min-w-[140px]">Bidang / Divisi</th>
                            <th class="py-3.5 px-3.5 min-w-[260px]">Keperluan Uang Muka</th>
                            <th class="py-3.5 px-3.5 text-right min-w-[140px]">Nominal Panjar</th>
                            <th class="py-3.5 px-3.5 text-center w-24">Status</th>
                            <th class="py-3.5 px-3.5 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($kasbonList as $key => $kb)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $key + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700">
                                    {{ $kb->no_kasbon }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-700">
                                    {{ $kb->tanggal_kasbon ? $kb->tanggal_kasbon->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-700">
                                    {{ $kb->tgl_jatuh_tempo ? $kb->tgl_jatuh_tempo->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 font-bold text-slate-900">
                                    {{ $kb->penerima }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded font-mono text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        {{ $kb->kode_bagian }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 font-medium text-slate-800 uppercase">
                                    {{ $kb->divisi }}
                                </td>
                                <td class="py-3.5 px-3.5 text-xs text-slate-600 whitespace-normal min-w-[260px] max-w-sm leading-relaxed">
                                    {{ $kb->keperluan }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right">
                                    <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($kb->nominal, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($kb->status === 'aktif')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Selesai
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($kb->status === 'aktif')
                                        <form action="{{ route('keuangan.anggaran.entry.dp-kasbon.selesai', $kb->id) }}" method="POST" onsubmit="return confirmSubmit(this, event, {
                                            title: 'Selesaikan Kasbon?',
                                            text: 'Tandai kasbon nomor <strong>{{ $kb->nomor_kasbon ?? $kb->id }}</strong> ini telah selesai / di-LPJ-kan?',
                                            theme: 'emerald',
                                            icon: 'check',
                                            confirmText: 'Ya, Tandai Selesai'
                                        });">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs shadow-2xs transition cursor-pointer">
                                                Selesaikan
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-kp-blue-700 font-semibold text-xs">Lunas LPJ</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-12 text-center text-slate-400 italic">
                                    Tidak ada data kasbon yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL FORM REGISTRASI KASBON BARU (Alpine.js) -->
        <div x-cloak x-show="createModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200" @click.outside="createModal = false">
                <form action="{{ route('keuangan.anggaran.entry.dp-kasbon.store') }}" method="POST">
                    @csrf
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-tight">
                                Registrasi DP Kasbon Operasional Baru
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Sistem DOS ANGGARAN.EXE</p>
                        </div>
                        <button type="button" @click="createModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                    </div>

                    <div class="mt-4 space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Kasbon <span class="text-rose-500">*</span></label>
                                <input type="text" name="no_kasbon" value="{{ $suggestedNoKb }}" required
                                       class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 px-3 py-2.5 bg-slate-50 text-slate-800">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Kasbon <span class="text-rose-500">*</span></label>
                                <input type="date" name="tanggal_kasbon" value="{{ date('Y-m-d') }}" required
                                       class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Bidang / Divisi <span class="text-rose-500">*</span></label>
                                <select name="divisi" required class="w-full text-xs font-medium rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
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
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Penerima <span class="text-rose-500">*</span></label>
                                <input type="text" name="penerima" required placeholder="Nama staf pemegang uang muka..." 
                                       class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Panjar (Rp) <span class="text-rose-500">*</span></label>
                                <input type="number" name="nominal" min="1000" step="50000" required placeholder="Contoh: 1500000"
                                       class="w-full text-sm font-mono font-bold rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Batas Waktu LPJ</label>
                                <input type="date" name="tgl_jatuh_tempo" value="{{ date('Y-m-d', strtotime('+7 days')) }}" 
                                       class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 text-slate-900 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Rincian Keperluan <span class="text-rose-500">*</span></label>
                            <textarea name="keperluan" rows="3" required placeholder="Jelaskan penggunaan panjar kasbon sementara ini..." 
                                      class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 leading-relaxed text-slate-900 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4 mt-4 border-t border-slate-100">
                        <button type="button" @click="createModal = false" class="h-10 px-4 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="h-10 px-5 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition cursor-pointer">
                            Daftarkan Kasbon
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
