<x-app-layout>
    <div class="space-y-6">

        <!-- Header Tabel Master Sesuai DOS ANGGARAN.EXE -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <h1 class="text-xl font-black text-blue-950 tracking-tight">
                            TABEL MASTER ANGGARAN (DOS ANGGARAN.EXE)
                        </h1>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-900 border border-blue-200">
                            HU. Kabar Priangan
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Referensi data master: Kode Bidang Bagian (01-07), Bagan Akun Perkiraan (COA), &amp; Rekening Bank Resmi
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <!-- Tombol Aksen Kuning 10% -->
                <a href="{{ route('keuangan.anggaran.create') }}" 
                   class="inline-flex items-center px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-amber-950 border border-amber-500/30 text-xs font-black shadow-xs transition cursor-pointer">
                    <span>+ Entry BP Priangan</span>
                </a>
                <a href="{{ route('keuangan.anggaran.index') }}" 
                   class="inline-flex items-center px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-bold transition">
                    <span>Daftar BP</span>
                </a>
            </div>
        </div>

        <!-- Navigation Sub-Tabs (BIDANG | PERKIRAAN PRIANGAN | REKENING BANK PRIANGAN) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-2 shadow-xs">
            <div class="flex flex-wrap items-center gap-2">
                <!-- Tab 1: Bidang -->
                <a href="{{ route('keuangan.anggaran.tabel.bidang') }}" 
                   class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all duration-150 {{ $activeTab === 'bidang' ? 'bg-blue-600 text-white shadow-xs font-extrabold' : 'text-slate-600 hover:text-blue-700 hover:bg-blue-50/50' }}">
                    <span>TABEL BIDANG</span>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-md font-bold {{ $activeTab === 'bidang' ? 'bg-blue-800 text-white' : 'bg-slate-200 text-slate-700' }}">01-21</span>
                </a>

                <!-- Tab 2: Perkiraan Priangan -->
                <a href="{{ route('keuangan.anggaran.tabel.perkiraan') }}" 
                   class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all duration-150 {{ $activeTab === 'perkiraan' ? 'bg-blue-600 text-white shadow-xs font-extrabold' : 'text-slate-600 hover:text-blue-700 hover:bg-blue-50/50' }}">
                    <span>PERKIRAAN PRIANGAN</span>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-md font-bold {{ $activeTab === 'perkiraan' ? 'bg-blue-800 text-white' : 'bg-slate-200 text-slate-700' }}">COA</span>
                </a>

                <!-- Tab 3: Rekening Bank Priangan -->
                <a href="{{ route('keuangan.anggaran.tabel.rekening-bank') }}" 
                   class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all duration-150 {{ $activeTab === 'rekening_bank' ? 'bg-blue-600 text-white shadow-xs font-extrabold' : 'text-slate-600 hover:text-blue-700 hover:bg-blue-50/50' }}">
                    <span>REKENING BANK PRIANGAN</span>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-md font-bold {{ $activeTab === 'rekening_bank' ? 'bg-blue-800 text-white' : 'bg-slate-200 text-slate-700' }}">BANK</span>
                </a>
            </div>
        </div>

        <!-- SEARCH BAR & INFO -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <form method="GET" action="{{ url()->current() }}" class="flex-1 flex items-center gap-2 max-w-md">
                <div class="relative w-full">
                    <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Cari kode, nama divisi, atau kata kunci..." 
                           class="w-full text-xs px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 text-slate-800 font-medium">
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer">
                    Cari
                </button>
                @if(!empty($search))
                    <a href="{{ url()->current() }}" class="px-3 py-2 border border-slate-300 hover:bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl">
                        Reset
                    </a>
                @endif
            </form>
            <div class="text-[11px] text-slate-500 font-semibold flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <span>Standar Baku DOS ANGGARAN.EXE &bull; Terhubung Real-Time ke Modul Kasir &amp; BP</span>
            </div>
        </div>

        <!-- TAB CONTENT 1: TABEL BIDANG -->
        @if($activeTab === 'bidang')
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                <!-- Sub-Header Card -->
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-black text-blue-950 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            Daftar 21 Kode Bidang / Bagian Kabar Priangan (01 s/d 21)
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Klasifikasi departemen pemohon pengeluaran anggaran dan pembebanan kas</p>
                    </div>
                    <span class="text-xs font-mono font-bold px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                        Total {{ count($bidangList) }} Bidang
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <!-- Table Header: Jelas & Rapi -->
                        <thead>
                            <tr class="bg-blue-50/70 border-b border-blue-200 text-blue-950 text-[10px] font-black uppercase tracking-wider">
                                <th class="py-3 px-4 w-20 text-center">Kode</th>
                                <th class="py-3 px-4 w-52">Bidang / Divisi</th>
                                <th class="py-3 px-4 min-w-[280px]">Deskripsi Operasional</th>
                                <th class="py-3 px-4 w-56">Bagan Akun (COA Beban)</th>
                                <th class="py-3 px-4 w-48">Penanggung Jawab</th>
                                <th class="py-3 px-4 w-36 text-center">Aktivitas BP</th>
                                <th class="py-3 px-4 w-28 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/80 text-slate-700">
                            @forelse($bidangList as $item)
                                <tr class="hover:bg-blue-50/30 transition-colors duration-150 align-top">
                                    <!-- 1. Kode Bagian -->
                                    <td class="py-4 px-4 text-center">
                                        <div class="inline-flex items-center justify-center w-9 h-9 rounded-xl font-mono text-xs font-black bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
                                            {{ $item['kode'] }}
                                        </div>
                                    </td>

                                    <!-- 2. Nama Bidang -->
                                    <td class="py-4 px-4">
                                        <div class="font-black text-slate-900 text-sm tracking-tight leading-snug">
                                            {{ $item['nama'] }}
                                        </div>
                                        <div class="mt-1">
                                            <span class="inline-block text-[10px] font-mono font-semibold px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                                slug: {{ $item['slug'] }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- 3. Deskripsi -->
                                    <td class="py-4 px-4">
                                        <p class="text-xs text-slate-600 leading-relaxed font-normal">
                                            {{ $item['deskripsi'] }}
                                        </p>
                                    </td>

                                    <!-- 4. Bagan Akun COA -->
                                    <td class="py-4 px-4">
                                        <div class="space-y-1">
                                            <span class="inline-block px-2 py-0.5 rounded font-mono text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                {{ $item['pos_beban'] }}
                                            </span>
                                            <div class="text-[11px] text-slate-500 font-medium leading-tight">
                                                {{ $item['nama_pos'] }}
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 5. Penanggung Jawab -->
                                    <td class="py-4 px-4">
                                        <span class="font-semibold text-xs text-slate-800 leading-snug">
                                            {{ $item['pj'] }}
                                        </span>
                                    </td>

                                    <!-- 6. Aktivitas Real-Time -->
                                    <td class="py-4 px-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $item['total_bp'] > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $item['total_bp'] > 0 ? 'bg-blue-600' : 'bg-slate-400' }}"></span>
                                                {{ $item['total_bp'] }} BP
                                            </span>
                                            <span class="font-mono text-xs font-bold text-slate-800 mt-1">
                                                Rp {{ number_format($item['total_nominal'], 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- 7. Aksi -->
                                    <td class="py-4 px-4 text-center">
                                        <a href="{{ route('keuangan.anggaran.create', ['divisi' => $item['slug']]) }}" 
                                           class="inline-flex items-center px-3 py-1.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-amber-950 border border-amber-500/30 font-black text-xs shadow-xs hover:shadow transition">
                                            + Entry BP
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400 italic">
                                        Tidak ada data bidang yang cocok dengan pencarian "{{ $search }}".
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- TAB CONTENT 2: TABEL PERKIRAAN PRIANGAN -->
        @if($activeTab === 'perkiraan')
            <div x-data="{
                showEditModal: false,
                showCreateModal: false,
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
                }
            }" class="space-y-4">

                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                    <!-- Sub-Header Card -->
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-black text-blue-950 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                Bagan Akun Perkiraan Pengeluaran Beban (Chart of Accounts Priangan)
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Struktur kode rekening akuntansi pengeluaran operasional DOS ANGGARAN.EXE &amp; Kasir</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-bold px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                                Total {{ count($perkiraanList) }} Akun Perkiraan
                            </span>
                            <button type="button" @click="showCreateModal = true"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-2xs cursor-pointer">
                                <span>+ Tambah Akun</span>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-blue-50/70 border-b border-blue-200 text-blue-950 text-[10px] font-black uppercase tracking-wider">
                                    <th class="py-3 px-4 w-44">Kode Perkiraan (COA)</th>
                                    <th class="py-3 px-4 w-56">Nama Akun Perkiraan</th>
                                    <th class="py-3 px-4 w-40">Terkait Bidang</th>
                                    <th class="py-3 px-4 w-40">Klasifikasi Beban</th>
                                    <th class="py-3 px-4 min-w-[240px]">Keterangan Pos Operasional</th>
                                    <th class="py-3 px-4 w-20 text-center">Posisi</th>
                                    <th class="py-3 px-4 w-36 text-right">Realisasi (BP)</th>
                                    <th class="py-3 px-4 w-28 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200/80 text-slate-700">
                                @forelse($perkiraanList as $item)
                                    <tr class="hover:bg-blue-50/30 transition-colors duration-150 align-top">
                                        <td class="py-4 px-4">
                                            <span class="font-mono text-xs font-black px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                                                {{ $item['kode'] }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="font-bold text-slate-900 text-xs leading-snug">
                                                {{ $item['nama'] }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="inline-flex items-center gap-1.5 font-semibold text-xs text-slate-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                {{ $item['bidang'] }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $item['kategori'] }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <p class="text-xs text-slate-600 leading-relaxed font-normal">
                                                {{ $item['keterangan'] }}
                                            </p>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full {{ $item['tipe'] === 'DEBIT' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                                {{ $item['tipe'] }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <div class="font-mono font-bold text-slate-900 text-xs">
                                                {{ $item['total_bp'] }} Transaksi
                                            </div>
                                            <div class="font-mono text-[11px] text-blue-700 font-bold mt-0.5">
                                                Rp {{ number_format($item['total_rupiah'], 0, ',', '.') }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <div class="inline-flex items-center gap-1">
                                                <button type="button" 
                                                        @click="openEdit({{ json_encode($item) }})"
                                                        class="px-2.5 py-1 rounded-lg bg-white border border-slate-300 hover:border-blue-400 hover:bg-blue-50 text-blue-700 text-[11px] font-bold transition cursor-pointer shadow-2xs">
                                                    Edit
                                                </button>
                                                @if(!empty($item['id']))
                                                    <form action="{{ route('keuangan.anggaran.tabel.perkiraan.destroy', $item['id']) }}" method="POST" class="inline"
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus / menonaktifkan akun perkiraan {{ $item['kode'] }} ({{ $item['nama'] }})?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="px-2 py-1 rounded-lg bg-white border border-slate-200 hover:border-rose-300 hover:bg-rose-50 text-rose-600 text-[11px] font-bold transition cursor-pointer">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-12 text-center text-slate-400 italic">
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
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200" @click.outside="showEditModal = false">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="text-sm font-extrabold text-blue-950 uppercase tracking-tight">
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
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Kode Perkiraan (COA) *</label>
                                    <input type="text" name="kode" x-model="editItem.kode" required
                                           class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                    <p class="text-[10px] text-slate-400 mt-1">Format hierarki titik, cth: 6.00.01.00.01.02</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Posisi Akuntansi *</label>
                                    <select name="tipe" x-model="editItem.tipe" required
                                            class="w-full text-xs font-bold rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                        <option value="DEBIT">DEBIT (Beban / Aset)</option>
                                        <option value="KREDIT">KREDIT (Kewajiban / Hutang)</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Akun Perkiraan *</label>
                                <input type="text" name="nama" x-model="editItem.nama" required
                                       class="w-full text-xs font-bold rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Terkait Bidang (01-21)</label>
                                    <select name="kode_bagian" x-model="editItem.kode_bagian"
                                            class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 font-medium">
                                        <option value="">-- Umum / Tanpa Bidang Spesifik --</option>
                                        @foreach($daftarBidang ?? [] as $k => $b)
                                            <option value="{{ $k }}">{{ $b['nama_lengkap'] ?? ($k . ' - ' . ($b['nama'] ?? '')) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Klasifikasi Beban</label>
                                    <input type="text" name="kategori" x-model="editItem.kategori" placeholder="Cth: Beban SDM, Operasional, ATK"
                                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan Pos Operasional</label>
                                <textarea name="keterangan" x-model="editItem.keterangan" rows="2"
                                          placeholder="Deskripsi peruntukan pengeluaran akun ini..."
                                          class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>

                            <div class="p-3 rounded-xl bg-blue-50 border border-blue-200 text-[11px] text-blue-900 leading-relaxed">
                                <strong>Sinkronisasi Otomatis:</strong> Jika nomor kode diubah, sistem akan otomatis memperbarui histori data BP dan Kas Keluar yang terkait dengan akun ini agar laporan pembukuan tetap sinkron.
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-black shadow-xs transition cursor-pointer">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- MODAL TAMBAH AKUN PERKIRAAN BARU -->
                <div x-cloak x-show="showCreateModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
                    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200" @click.outside="showCreateModal = false">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="text-sm font-extrabold text-blue-950 uppercase tracking-tight">
                                Tambah Akun Perkiraan (COA) Baru
                            </h3>
                            <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                        </div>

                        <form action="{{ route('keuangan.anggaran.tabel.perkiraan.store') }}" method="POST" class="mt-4 space-y-4">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Kode Perkiraan (COA) *</label>
                                    <input type="text" name="kode" required placeholder="Cth: 6.00.01.01.01.09"
                                           class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Posisi Akuntansi *</label>
                                    <select name="tipe" required
                                            class="w-full text-xs font-bold rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                        <option value="DEBIT">DEBIT (Beban / Aset)</option>
                                        <option value="KREDIT">KREDIT (Kewajiban / Hutang)</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Akun Perkiraan *</label>
                                <input type="text" name="nama" required placeholder="Cth: Honor Narasumber / Kegiatan Khusus"
                                       class="w-full text-xs font-bold rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Terkait Bidang (01-21)</label>
                                    <select name="kode_bagian"
                                            class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 font-medium">
                                        <option value="">-- Umum / Tanpa Bidang Spesifik --</option>
                                        @foreach($daftarBidang ?? [] as $k => $b)
                                            <option value="{{ $k }}">{{ $b['nama_lengkap'] ?? ($k . ' - ' . ($b['nama'] ?? '')) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Klasifikasi Beban</label>
                                    <input type="text" name="kategori" placeholder="Cth: Beban Operasional, SDM, Liputan"
                                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan Pos Operasional</label>
                                <textarea name="keterangan" rows="2"
                                          placeholder="Deskripsi singkat peruntukan beban..."
                                          class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-black shadow-xs transition cursor-pointer">
                                    Simpan Akun Baru
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        @endif

        <!-- TAB CONTENT 3: TABEL REKENING BANK PRIANGAN -->
        @if($activeTab === 'rekening_bank')
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                <!-- Sub-Header Card -->
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-black text-blue-950 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            Daftar Rekening Bank Operasional Resmi HU. Kabar Priangan
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Rekening penampung dan rekening pencairan transfer Bukti Kas Keluar (BKK) Kasir</p>
                    </div>
                    <span class="text-xs font-mono font-bold px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                        Total {{ count($bankList) }} Rekening Bank
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-5 bg-slate-50/30">
                    @forelse($bankList as $bank)
                        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs hover:border-blue-300 hover:shadow-md transition space-y-3.5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $bank['tipe_rekening'] }}
                                    </span>
                                    <h3 class="text-sm font-black text-slate-900 mt-1.5">{{ $bank['bank_nama'] }}</h3>
                                    <p class="text-[11px] text-slate-500">{{ $bank['cabang'] }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                    Aktif
                                </span>
                            </div>

                            <div class="bg-slate-900 rounded-xl p-4 text-white font-mono flex items-center justify-between shadow-2xs">
                                <div>
                                    <div class="text-[10px] text-slate-400 uppercase tracking-wider">Nomor Rekening</div>
                                    <div class="text-base font-black tracking-wider text-amber-400 mt-0.5">{{ $bank['no_rekening'] }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] text-slate-400 uppercase tracking-wider">Atas Nama</div>
                                    <div class="text-[11px] font-bold text-slate-200 truncate max-w-[160px]">{{ $bank['atas_nama'] }}</div>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-[10px] text-slate-400 font-semibold block uppercase">Fungsi Utama:</span>
                                    <span class="font-medium text-slate-700 text-[11px] leading-snug">{{ $bank['fungsi_utama'] }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 font-semibold block uppercase">COA Buku Besar:</span>
                                    <span class="font-mono font-bold text-blue-700 text-xs">{{ $bank['kode_coa'] }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 py-12 text-center text-slate-400 italic">
                            Tidak ada data rekening bank yang cocok.
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
