<x-app-layout>
    <div class="space-y-6">

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
                            Tabel Master Anggaran (DOS ANGGARAN.EXE)
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            HU. Kabar Priangan
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Referensi data master: Kode Bidang Bagian (01-21), Bagan Akun Perkiraan (COA), &amp; Rekening Bank Resmi
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('keuangan.anggaran.create') }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Entry BP Priangan</span>
                </a>
                <a href="{{ route('keuangan.anggaran.index') }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition">
                    <span>Daftar BP</span>
                </a>
            </div>
        </div>

        <!-- Tier 2: 4 KPI Summary Cards Standar Divisi Iklan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Bidang Aktif</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">
                    {{ count($bidangList ?? ($daftarBidang ?? [])) ?: 21 }} <span class="text-sm font-normal text-slate-500">Divisi</span>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Akun COA</span>
                <div class="text-2xl font-bold text-kp-blue-700 font-mono mt-1">
                    {{ count($perkiraanList ?? []) ?: 28 }} <span class="text-sm font-normal text-slate-500">Akun</span>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Rekening Bank Resmi</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">
                    {{ count($bankList ?? []) ?: 4 }} <span class="text-sm font-normal text-slate-500">Rekening</span>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Realisasi Kumulatif</span>
                <div class="text-xl sm:text-2xl font-bold text-kp-blue-700 font-mono tabular-nums mt-1">
                    Rp {{ number_format(collect($bidangList ?? [])->sum('total_nominal'), 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- Sub-Tabs Navigasi (Bidang | Perkiraan Priangan | Rekening Bank) -->
        <div class="bg-white border border-slate-200 rounded-xl p-2 shadow-xs">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('keuangan.anggaran.tabel.bidang') }}" 
                   class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold transition {{ $activeTab === 'bidang' ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50' }}">
                    <span>TABEL BIDANG</span>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded font-bold {{ $activeTab === 'bidang' ? 'bg-kp-blue-800 text-white' : 'bg-slate-200 text-slate-700' }}">01-21</span>
                </a>

                <a href="{{ route('keuangan.anggaran.tabel.perkiraan') }}" 
                   class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold transition {{ $activeTab === 'perkiraan' ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50' }}">
                    <span>PERKIRAAN PRIANGAN</span>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded font-bold {{ $activeTab === 'perkiraan' ? 'bg-kp-blue-800 text-white' : 'bg-slate-200 text-slate-700' }}">COA</span>
                </a>

                <a href="{{ route('keuangan.anggaran.tabel.rekening-bank') }}" 
                   class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold transition {{ $activeTab === 'rekening_bank' ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-kp-blue-700 hover:bg-slate-50' }}">
                    <span>REKENING BANK PRIANGAN</span>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded font-bold {{ $activeTab === 'rekening_bank' ? 'bg-kp-blue-800 text-white' : 'bg-slate-200 text-slate-700' }}">BANK</span>
                </a>
            </div>
        </div>

        <!-- Tier 3: Toolbar Pencarian & Filter Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <form method="GET" action="{{ url()->current() }}" class="flex-1 flex items-center gap-2 max-w-md">
                <div class="relative w-full">
                    <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Cari kode, nama bidang, COA, atau rekening..." 
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-slate-900 text-sm focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                </div>
                <button type="submit" class="h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl transition shadow-xs cursor-pointer">
                    Cari
                </button>
                @if(!empty($search))
                    <a href="{{ url()->current() }}" class="h-11 px-4 inline-flex items-center justify-center border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl transition">
                        Reset
                    </a>
                @endif
            </form>
            <div class="text-xs text-slate-500 font-medium flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-kp-blue-600"></span>
                <span>DOS ANGGARAN.EXE &bull; Terhubung Real-Time ke Modul Kasir &amp; BP</span>
            </div>
        </div>

        <!-- Tier 4: Pure Data Table Standar Divisi Iklan -->

        <!-- TAB CONTENT 1: TABEL BIDANG -->
        @if($activeTab === 'bidang')
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3.5 px-3.5 text-center w-12">No</th>
                                <th class="py-3.5 px-3.5 text-center w-16">Kode</th>
                                <th class="py-3.5 px-3.5 min-w-[180px]">Nama Bidang</th>
                                <th class="py-3.5 px-3.5 min-w-[120px]">Slug</th>
                                <th class="py-3.5 px-3.5 min-w-[280px]">Deskripsi Operasional</th>
                                <th class="py-3.5 px-3.5 min-w-[150px]">Kode COA</th>
                                <th class="py-3.5 px-3.5 min-w-[180px]">Nama Pos Beban</th>
                                <th class="py-3.5 px-3.5 min-w-[160px]">Penanggung Jawab</th>
                                <th class="py-3.5 px-3.5 text-center w-24">Jml BP</th>
                                <th class="py-3.5 px-3.5 text-right min-w-[150px]">Total Realisasi</th>
                                <th class="py-3.5 px-3.5 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @forelse($bidangList as $key => $item)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $key + 1 }}</td>
                                    <td class="py-3.5 px-3.5 text-center font-mono font-bold text-kp-blue-700">
                                        <span class="px-2 py-0.5 rounded bg-kp-blue-50 border border-kp-blue-200">{{ $item['kode'] }}</span>
                                    </td>
                                    <td class="py-3.5 px-3.5 font-bold text-slate-900">{{ $item['nama'] }}</td>
                                    <td class="py-3.5 px-3.5 font-mono text-xs text-slate-500">{{ $item['slug'] }}</td>
                                    <td class="py-3.5 px-3.5 text-xs text-slate-600 whitespace-normal min-w-[280px] max-w-md leading-relaxed">
                                        {{ $item['deskripsi'] }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono text-xs font-semibold text-kp-blue-700">
                                        {{ $item['pos_beban'] }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-xs text-slate-700">{{ $item['nama_pos'] }}</td>
                                    <td class="py-3.5 px-3.5 font-medium text-slate-800">{{ $item['pj'] }}</td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $item['total_bp'] > 0 ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                            {{ $item['total_bp'] }} BP
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right">
                                        <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($item['total_nominal'], 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        <a href="{{ route('keuangan.anggaran.create', ['divisi' => $item['slug']]) }}" 
                                           class="inline-flex items-center px-3 py-1.5 rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs transition shadow-2xs">
                                            + Entry BP
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="py-12 text-center text-slate-400 italic">
                                        Tidak ada data bidang yang cocok dengan pencarian "{{ $search }}".
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- TAB CONTENT 2: TABEL PERKIRAAN PRIANGAN (COA) -->
        @if($activeTab === 'perkiraan')
            <div x-data="{
                showEditModal: false,
                showCreateModal: false,
                showDeleteModal: false,
                deleteTarget: { id: '', kode: '', nama: '', formId: '' },
                editItem: {
                    id: '',
                    kode: '',
                    nama: '',
                    kode_bagian: '',
                    kategori: '',
                    tipe: 'DEBIT',
                    keterangan: ''
                },
                openEdit(item) {
                    this.editItem = {
                        id: item.id || '',
                        kode: item.kode || '',
                        nama: item.nama || '',
                        kode_bagian: item.kode_bagian || '',
                        kategori: item.kategori || '',
                        tipe: item.tipe || 'DEBIT',
                        keterangan: item.keterangan === '-' ? '' : (item.keterangan || '')
                    };
                    this.showEditModal = true;
                },
                openDelete(item, formId) {
                    this.deleteTarget = {
                        id: item.id || '',
                        kode: item.kode || '',
                        nama: item.nama || '',
                        formId: formId
                    };
                    this.showDeleteModal = true;
                },
                confirmDelete() {
                    if (this.deleteTarget.formId) {
                        const f = document.getElementById(this.deleteTarget.formId);
                        if (f) f.submit();
                    }
                }
            }" class="space-y-4">

                <div class="flex items-center justify-between gap-3">
                    <span class="text-xs font-semibold text-slate-500">
                        Total <strong class="text-slate-900">{{ count($perkiraanList) }}</strong> Akun Perkiraan COA
                    </span>
                    <button type="button" @click="showCreateModal = true"
                            class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Akun Perkiraan</span>
                    </button>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="overflow-x-auto min-h-[300px]">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                <tr>
                                    <th class="py-3.5 px-3.5 text-center w-12">No</th>
                                    <th class="py-3.5 px-3.5 min-w-[150px]">Kode Perkiraan</th>
                                    <th class="py-3.5 px-3.5 min-w-[200px]">Nama Akun Perkiraan</th>
                                    <th class="py-3.5 px-3.5 min-w-[150px]">Terkait Bidang</th>
                                    <th class="py-3.5 px-3.5 min-w-[140px]">Klasifikasi</th>
                                    <th class="py-3.5 px-3.5 min-w-[240px]">Keterangan Pos</th>
                                    <th class="py-3.5 px-3.5 text-center w-24">Posisi</th>
                                    <th class="py-3.5 px-3.5 text-center w-28">Jml BP</th>
                                    <th class="py-3.5 px-3.5 text-right min-w-[150px]">Total Realisasi</th>
                                    <th class="py-3.5 px-3.5 text-center w-28">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                                @forelse($perkiraanList as $key => $item)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $key + 1 }}</td>
                                        <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700">
                                            {{ $item['kode'] }}
                                        </td>
                                        <td class="py-3.5 px-3.5 font-bold text-slate-900">{{ $item['nama'] }}</td>
                                        <td class="py-3.5 px-3.5 font-medium text-slate-800">{{ $item['bidang'] }}</td>
                                        <td class="py-3.5 px-3.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $item['kategori'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3.5 text-xs text-slate-600 whitespace-normal min-w-[240px] max-w-sm leading-relaxed">
                                            {{ $item['keterangan'] }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $item['tipe'] === 'DEBIT' ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                                {{ $item['tipe'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3.5 text-center font-mono text-xs font-bold text-slate-900">
                                            {{ $item['total_bp'] }} BP
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right">
                                            <div class="flex justify-between items-center gap-2 max-w-[130px] ml-auto font-mono text-sm">
                                                <span class="text-slate-400 font-normal select-none">Rp</span>
                                                <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($item['total_rupiah'], 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-3.5 text-center">
                                            <div class="inline-flex items-center gap-1.5">
                                                <button type="button" 
                                                        @click="openEdit({{ json_encode($item) }})"
                                                        class="px-2.5 py-1 rounded-lg border border-slate-300 hover:border-kp-blue-500 hover:bg-kp-blue-50 text-kp-blue-700 text-xs font-semibold transition cursor-pointer">
                                                    Edit
                                                </button>
                                                @if(!empty($item['id']))
                                                    <form id="form-del-coa-{{ $item['id'] }}" action="{{ route('keuangan.anggaran.tabel.perkiraan.destroy', $item['id']) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                                @click="openDelete({{ json_encode(['id' => $item['id'], 'kode' => $item['kode'], 'nama' => $item['nama']]) }}, 'form-del-coa-{{ $item['id'] }}')"
                                                                class="px-2 py-1 rounded-lg border border-slate-200 hover:border-slate-400 hover:bg-slate-100 text-slate-600 text-xs font-semibold transition cursor-pointer">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="py-12 text-center text-slate-400 italic">
                                            Tidak ada akun perkiraan yang cocok dengan pencarian.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- MODAL EDIT AKUN PERKIRAAN -->
                <div x-cloak x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
                    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200" @click.outside="showEditModal = false">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-tight">
                                Edit Akun Perkiraan (COA)
                            </h3>
                            <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                        </div>

                        <form :action="'{{ url('keuangan/anggaran/tabel/perkiraan') }}/' + editItem.id" 
                              @submit="if (!$el.action || $el.action.endsWith('/perkiraan')) { $el.action = '{{ url('keuangan/anggaran/tabel/perkiraan') }}/' + editItem.id; }"
                              method="POST" class="mt-4 space-y-4">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Perkiraan (COA) *</label>
                                    <input type="text" name="kode" x-model="editItem.kode" required
                                           class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                                    <p class="text-[10px] text-slate-400 mt-1">Format hierarki titik: cth 6.00.01.00.01.02</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Posisi Akuntansi *</label>
                                    <select name="tipe" x-model="editItem.tipe" required
                                            class="w-full text-xs font-semibold rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                                        <option value="DEBIT">DEBIT (Beban / Aset)</option>
                                        <option value="KREDIT">KREDIT (Kewajiban / Hutang)</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Akun Perkiraan *</label>
                                <input type="text" name="nama" x-model="editItem.nama" required
                                       class="w-full text-xs font-semibold rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Terkait Bidang (01-21)</label>
                                    <select name="kode_bagian" x-model="editItem.kode_bagian"
                                            class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600 font-medium">
                                        <option value="">-- Umum / Tanpa Bidang Spesifik --</option>
                                        @foreach($daftarBidang ?? [] as $k => $b)
                                            <option value="{{ $k }}">{{ $b['nama_lengkap'] ?? ($k . ' - ' . ($b['nama'] ?? '')) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Klasifikasi Beban</label>
                                    <input type="text" name="kategori" x-model="editItem.kategori" placeholder="Cth: Beban SDM, Operasional, ATK"
                                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan Pos Operasional</label>
                                <textarea name="keterangan" x-model="editItem.keterangan" rows="2"
                                          placeholder="Deskripsi peruntukan pengeluaran akun ini..."
                                          class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600"></textarea>
                            </div>

                            <div class="p-3 rounded-xl bg-kp-blue-50 border border-kp-blue-200 text-xs text-kp-blue-900 leading-relaxed">
                                <strong>Sinkronisasi Otomatis:</strong> Jika nomor kode diubah, sistem akan otomatis memperbarui histori data BP dan Kas Keluar yang terkait dengan akun ini agar pembukuan tetap sinkron.
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                <button type="button" @click="showEditModal = false" class="h-10 px-4 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" class="h-10 px-5 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition cursor-pointer">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- MODAL TAMBAH AKUN PERKIRAAN BARU -->
                <div x-cloak x-show="showCreateModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
                    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200" @click.outside="showCreateModal = false">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-tight">
                                Tambah Akun Perkiraan (COA) Baru
                            </h3>
                            <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                        </div>

                        <form action="{{ route('keuangan.anggaran.tabel.perkiraan.store') }}" method="POST" class="mt-4 space-y-4">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Perkiraan (COA) *</label>
                                    <input type="text" name="kode" required placeholder="Cth: 6.00.01.01.01.09"
                                           class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Posisi Akuntansi *</label>
                                    <select name="tipe" required
                                            class="w-full text-xs font-semibold rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                                        <option value="DEBIT">DEBIT (Beban / Aset)</option>
                                        <option value="KREDIT">KREDIT (Kewajiban / Hutang)</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Akun Perkiraan *</label>
                                <input type="text" name="nama" required placeholder="Cth: Honor Narasumber / Kegiatan Khusus"
                                       class="w-full text-xs font-semibold rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Terkait Bidang (01-21)</label>
                                    <select name="kode_bagian"
                                            class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600 font-medium">
                                        <option value="">-- Umum / Tanpa Bidang Spesifik --</option>
                                        @foreach($daftarBidang ?? [] as $k => $b)
                                            <option value="{{ $k }}">{{ $b['nama_lengkap'] ?? ($k . ' - ' . ($b['nama'] ?? '')) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Klasifikasi Beban</label>
                                    <input type="text" name="kategori" placeholder="Cth: Beban Operasional, SDM, Liputan"
                                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan Pos Operasional</label>
                                <textarea name="keterangan" rows="2"
                                          placeholder="Deskripsi singkat peruntukan beban..."
                                          class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-1 focus:ring-kp-blue-600 focus:border-kp-blue-600"></textarea>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                <button type="button" @click="showCreateModal = false" class="h-10 px-4 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" class="h-10 px-5 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold shadow-xs transition cursor-pointer">
                                    Simpan Akun Baru
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- MODAL KONFIRMASI HAPUS AKUN PERKIRAAN -->
                <div x-cloak x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
                    <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-200" @click.outside="showDeleteModal = false">
                        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                                Hapus / Nonaktifkan Akun COA
                            </h3>
                            <button type="button" @click="showDeleteModal = false" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer">✕</button>
                        </div>
                        <div class="p-6 space-y-4">
                            <p class="text-xs text-slate-600 leading-relaxed font-medium">
                                Apakah Anda yakin ingin menghapus atau menonaktifkan akun perkiraan berikut dari daftar pilihan Entry BP?
                            </p>
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-semibold">Kode Perkiraan:</span>
                                    <span class="font-mono font-bold text-slate-900" x-text="deleteTarget.kode"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-semibold">Nama Akun:</span>
                                    <span class="font-bold text-slate-900" x-text="deleteTarget.nama"></span>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2">
                            <button type="button" @click="showDeleteModal = false"
                                    class="h-10 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-xs font-semibold text-slate-700 cursor-pointer">
                                Batal
                            </button>
                            <button type="button" @click="confirmDelete()"
                                    class="h-10 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs transition cursor-pointer">
                                Ya, Hapus Akun
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        @endif

        <!-- TAB CONTENT 3: TABEL REKENING BANK PRIANGAN (Pure Data Table Standar Divisi Iklan) -->
        @if($activeTab === 'rekening_bank')
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3.5 px-3.5 text-center w-12">No</th>
                                <th class="py-3.5 px-3.5 min-w-[140px]">Kode COA</th>
                                <th class="py-3.5 px-3.5 min-w-[160px]">Nama Bank</th>
                                <th class="py-3.5 px-3.5 min-w-[160px]">Cabang</th>
                                <th class="py-3.5 px-3.5 min-w-[160px]">No. Rekening</th>
                                <th class="py-3.5 px-3.5 min-w-[200px]">Atas Nama</th>
                                <th class="py-3.5 px-3.5 min-w-[140px]">Jenis Rekening</th>
                                <th class="py-3.5 px-3.5 min-w-[220px]">Fungsi Utama</th>
                                <th class="py-3.5 px-3.5 text-center w-24">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @forelse($bankList as $key => $bank)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $key + 1 }}</td>
                                    <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700">{{ $bank['kode_coa'] }}</td>
                                    <td class="py-3.5 px-3.5 font-bold text-slate-900">{{ $bank['bank_nama'] }}</td>
                                    <td class="py-3.5 px-3.5 text-slate-600">{{ $bank['cabang'] }}</td>
                                    <td class="py-3.5 px-3.5 font-mono font-bold text-slate-900 tracking-wider">{{ $bank['no_rekening'] }}</td>
                                    <td class="py-3.5 px-3.5 font-medium text-slate-800">{{ $bank['atas_nama'] }}</td>
                                    <td class="py-3.5 px-3.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $bank['tipe_rekening'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-xs text-slate-600 whitespace-normal min-w-[220px] max-w-sm leading-relaxed">
                                        {{ $bank['fungsi_utama'] }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Aktif
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center text-slate-400 italic">
                                        Tidak ada data rekening bank yang cocok.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
