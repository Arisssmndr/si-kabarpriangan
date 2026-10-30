<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Konsisten (Standar Laporan dengan Logo Kantor) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Transaksi Iklan Priangan TV
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Pencatatan dan faktur penyiaran spot iklan Priangan TV.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('transaksipriangan.create') }}" 
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Transaksi TV Baru</span>
                </a>
            </div>
        </div>

        <!-- Toolbar Pencarian & Filter -->
        <div class="bg-white border border-slate-300 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('transaksipriangan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Pencarian Data
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik no faktur, nama pemasang, atau sales..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Status Pembayaran
                    </label>
                    <select name="status" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="">Semua Status</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="piutang" {{ request('status') === 'piutang' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>
                </div>

                <div class="sm:col-span-3 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'status']))
                        <a href="{{ route('transaksipriangan.index') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Transaksi Priangan TV (Clean Standard Kwitansi) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. Faktur</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Pemasang</th>
                            <th class="py-3 px-3.5">Sales</th>
                            <th class="py-3 px-3.5">Jenis Iklan TV</th>
                            <th class="py-3 px-3.5">Tgl Tayang</th>
                            <th class="py-3 px-3.5 text-right w-32">Harga Iklan</th>
                            <th class="py-3 px-3.5 text-right w-32">Dibayar</th>
                            <th class="py-3 px-3.5 text-right w-32">Piutang</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($transaksipriangan as $key => $t)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-3.5 text-center text-slate-500 font-normal">
                                    {{ $transaksipriangan->firstItem() + $key }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-base">
                                    {{ $t->nofakturpriangan }}
                                </td>
                                <td class="py-3 px-3.5 text-slate-600 font-mono">
                                    {{ \Carbon\Carbon::parse($t->tanggal_transaksipriangan)->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    {{ $t->nama_pemasangpriangan }}
                                </td>
                                <td class="py-3 px-3.5 text-slate-600">
                                    {{ $t->sales_iklanpriangan }}
                                </td>
                                <td class="py-3 px-3.5 text-slate-700">
                                    {{ $t->iklanpriangan->jenis_iklanpriangan ?? '-' }}
                                </td>
                                <td class="py-3 px-3.5 text-slate-600 font-mono">
                                    {{ \Carbon\Carbon::parse($t->tanggal_muatiklanpriangan)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[120px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-medium text-slate-900 tabular-nums">{{ number_format($t->harga_transaksipriangan, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[120px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-medium text-slate-900 tabular-nums">{{ number_format($t->jumlahbayar_transaksipriangan, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    @if($t->piutang_transaksipriangan > 0)
                                        <div class="flex justify-between items-center gap-2 max-w-[120px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-medium text-slate-900 tabular-nums">{{ number_format($t->piutang_transaksipriangan, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <div class="text-right">
                                            <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 text-slate-600 border border-slate-200">LUNAS</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @if($t->piutang_transaksipriangan <= 0)
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 text-slate-600 border border-slate-200">
                                            Lunas
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-0.5 border border-slate-200">
                                            Piutang
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-center" 
                                    x-data="{ open: false }" 
                                    :class="{ 'relative z-50': open }">
                                    <div class="relative inline-block text-left" @click.outside="open = false">
                                        <!-- Tombol Titik Tiga (Slim, Tajam & Kontras Tinggi) -->
                                        <button type="button" 
                                                @click="open = !open" 
                                                class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-700 hover:text-slate-950 hover:bg-slate-100 transition focus:outline-none cursor-pointer"
                                                :class="{ 'text-slate-950 bg-slate-100': open }"
                                                title="Pilihan Aksi">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="5" r="1.5" />
                                                <circle cx="12" cy="12" r="1.5" />
                                                <circle cx="12" cy="19" r="1.5" />
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu 3 Tombol Aksi -->
                                        <div x-cloak 
                                             x-show="open" 
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-44 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">
                                            
                                            <!-- 1. Cetak Faktur -->
                                            <a href="{{ route('transaksipriangan.cetak', $t->id) }}" 
                                               target="_blank"
                                               @click="open = false"
                                               class="group flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:text-kp-blue-700 hover:bg-kp-blue-50 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 group-hover:text-kp-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                                <span>Cetak Faktur</span>
                                            </a>

                                            <!-- 2. Ubah Data Transaksi -->
                                            <a href="{{ route('transaksipriangan.edit', $t->id) }}" 
                                               @click="open = false"
                                               class="w-full group flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:text-kp-blue-700 hover:bg-kp-blue-50 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-slate-400 group-hover:text-kp-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Ubah Data Order</span>
                                            </a>

                                            <!-- Separator -->
                                            <div class="h-px bg-slate-100 my-1"></div>

                                            <!-- 3. Hapus Transaksi -->
                                            <button type="button" 
                                                    onclick="deleteTransaksi('{{ $t->id }}', '{{ $t->nofakturpriangan }}')"
                                                    @click="open = false"
                                                    class="w-full group flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-rose-400 group-hover:text-rose-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span>Hapus Transaksi</span>
                                            </button>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="py-12 text-center text-slate-500 font-normal">
                                    Belum ada data transaksi. Klik "Transaksi Baru" untuk menambah data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transaksipriangan->hasPages())
                <div class="p-4 border-t border-slate-200 bg-white">
                    {{ $transaksipriangan->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Modal Edit Transaksi Priangan TV -->
    <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs hidden p-4">
        <div class="bg-white border border-slate-200 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50 sticky top-0 z-10">
                <h3 id="editModalTitle" class="text-sm font-semibold text-black">
                    Ubah Transaksi Priangan TV
                </h3>
                <button type="button" onclick="closeEditModal()" class="text-xs font-medium text-gray-500 hover:text-black px-2 py-1 cursor-pointer">
                    Tutup
                </button>
            </div>

            <form id="editForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">No Faktur</label>
                        <input type="text" id="e_nofaktur" readonly class="w-full px-3 py-2 bg-gray-100 border border-slate-300 rounded-lg text-xs font-medium text-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Transaksi</label>
                        <input type="date" id="e_tanggal" name="tanggal_transaksipriangan" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nama Pemasang</label>
                        <input type="text" id="e_nama" name="nama_pemasangpriangan" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Sales Marketing</label>
                        <input type="text" id="e_sales" name="sales_iklanpriangan" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Alamat Pemasang</label>
                    <input type="text" id="e_alamat" name="alamat_pemasangpriangan" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jenis Iklan TV</label>
                        <select id="e_id_iklan" name="id_iklanpriangan" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black">
                            @foreach($iklanpriangan as $ip)
                                <option value="{{ $ip->id }}">{{ $ip->jenis_iklanpriangan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Tayang</label>
                        <input type="date" id="e_tgl_muat" name="tanggal_muatiklanpriangan" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                    </div>
                </div>

                <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-3">
                    <p class="text-xs font-semibold text-black uppercase tracking-wider">Perhitungan Biaya</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Harga Iklan (Rp)</label>
                            <input type="text" id="e_harga" name="harga_transaksipriangan" required onkeyup="formatInput(this)" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-black font-tabular" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Diskon (Rp)</label>
                            <input type="text" id="e_diskon" name="diskon_transaksipriangan" value="0" onkeyup="formatInput(this)" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-black font-tabular" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah Dibayar (Rp)</label>
                            <input type="text" id="e_bayar" name="jumlahbayar_transaksipriangan" required onkeyup="formatInput(this)" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-black font-tabular" />
                        </div>
                    </div>
                    <div class="pt-2 text-xs border-t border-slate-200 flex justify-between items-center">
                        <span class="text-gray-600">Total Tagihan:</span>
                        <p id="e_label_total" class="font-bold text-kp-blue-700 text-sm font-tabular">Rp 0</p>
                    </div>
                    <div class="text-xs flex justify-between items-center">
                        <span class="text-gray-600">Sisa Piutang:</span>
                        <p id="e_label_piutang" class="font-bold text-red-600 text-sm font-tabular">Rp 0</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-xs font-medium text-gray-600 hover:bg-slate-100 rounded-lg transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-medium rounded-lg shadow-xs transition cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function formatRupiah(num) {
            if (!num) return '0';
            return new Intl.NumberFormat('id-ID').format(num);
        }
        function cleanNumber(str) {
            if (!str) return 0;
            return parseFloat(str.toString().replace(/[^0-9]/g, '')) || 0;
        }
        function formatInput(el) {
            const v = cleanNumber(el.value);
            el.value = formatRupiah(v);
            hitungEdit();
        }

        function openEditModal(btn) {
            const d = btn.dataset;
            document.getElementById('editModalTitle').innerText = 'Ubah Transaksi ' + d.nofaktur;
            document.getElementById('editForm').action = "/transaksipriangan/" + d.id;

            document.getElementById('e_nofaktur').value = d.nofaktur;
            document.getElementById('e_tanggal').value = d.tanggal;
            document.getElementById('e_nama').value = d.nama;
            document.getElementById('e_alamat').value = d.alamat;
            document.getElementById('e_sales').value = d.sales;
            document.getElementById('e_id_iklan').value = d.id_iklan;
            document.getElementById('e_tgl_muat').value = d.tgl_muat;

            document.getElementById('e_harga').value = formatRupiah(d.harga);
            document.getElementById('e_diskon').value = formatRupiah(d.diskon || 0);
            document.getElementById('e_bayar').value = formatRupiah(d.bayar);

            hitungEdit();
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function hitungEdit() {
            const harga = cleanNumber(document.getElementById('e_harga').value);
            const diskon = cleanNumber(document.getElementById('e_diskon').value);
            const bayar = cleanNumber(document.getElementById('e_bayar').value);
            const total = Math.max(0, harga - diskon);
            const piutang = Math.max(0, total - bayar);

            document.getElementById('e_label_total').innerText = 'Rp ' + formatRupiah(total);
            document.getElementById('e_label_piutang').innerText = 'Rp ' + formatRupiah(piutang);
            if (piutang > 0) {
                document.getElementById('e_label_piutang').className = 'font-bold text-red-600 text-sm font-tabular';
            } else {
                document.getElementById('e_label_piutang').className = 'font-bold text-emerald-600 text-sm font-tabular';
            }
        }

        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const nofaktur = document.getElementById('e_nofaktur').value;
            AppAlert.confirmSave({
                title: 'Simpan Perubahan',
                subtitle: 'Data transaksi akan diperbarui',
                target: nofaktur,
                confirmText: 'Ya, Simpan'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        async function deleteTransaksi(id, nofaktur) {
            AppAlert.confirmDelete({
                title: 'Hapus Transaksi',
                subtitle: 'Tindakan ini tidak dapat dibatalkan',
                target: nofaktur,
                confirmText: 'Ya, Hapus Transaksi'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').content;
                        await axios.post('/transaksipriangan/' + id, {
                            _method: 'DELETE',
                            _token: token
                        });
                        AppAlert.success({
                            title: 'Berhasil Dihapus',
                            subtitle: 'Data telah dihapus dari sistem',
                            message: 'Transaksi ' + nofaktur + ' berhasil dihapus.'
                        }).then(() => location.reload());
                    } catch (err) {
                        AppAlert.error({
                            title: 'Gagal Menghapus',
                            subtitle: 'Terjadi kesalahan sistem',
                            message: 'Terjadi kesalahan saat menghapus data transaksi.',
                            buttonText: 'Tutup'
                        });
                    }
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
