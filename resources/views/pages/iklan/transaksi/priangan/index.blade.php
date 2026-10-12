<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Konsisten (Standar Laporan dengan Logo Kantor) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Transaksi Iklan Priangan TV
                    </h1>
                    <p class="text-xs font-normal text-slate-500 mt-0.5">
                        Pencatatan dan pengelolaan faktur iklan siaran Priangan TV
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('transaksipriangan.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs font-semibold rounded-lg shadow-xs hover:shadow transition cursor-pointer">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Transaksi Baru</span>
                </a>
            </div>
        </div>

        <!-- Toolbar Pencarian & Filter -->
        <div class="bg-white border border-slate-300 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('transaksipriangan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-6">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Pencarian Data
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik no faktur, nama pemasang, atau sales..."
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-black placeholder-gray-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Status Pembayaran
                    </label>
                    <select name="status" class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-black focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="">Semua Status</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="piutang" {{ request('status') === 'piutang' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>
                </div>

                <div class="sm:col-span-3 flex gap-2">
                    <button type="submit" class="flex-1 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-semibold text-xs rounded-lg shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'status']))
                        <a href="{{ route('transaksipriangan.index') }}" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-lg transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Transaksi Priangan TV -->
        <div class="bg-white border border-slate-300 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-4 bg-slate-50/90 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Transaksi Priangan TV</h2>
                    <p class="text-xs text-slate-500 mt-0.5 font-normal">Total {{ $transaksipriangan->total() }} faktur tercatat</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-300 bg-slate-100 text-slate-800 uppercase tracking-wider text-[11px] whitespace-nowrap">
                            <th class="py-3.5 px-3.5 font-bold text-center w-12">No</th>
                            <th class="py-3.5 px-3.5 font-bold">No Faktur</th>
                            <th class="py-3.5 px-3.5 font-bold">Tanggal</th>
                            <th class="py-3.5 px-3.5 font-bold">Pemasang</th>
                            <th class="py-3.5 px-3.5 font-bold">Sales</th>
                            <th class="py-3.5 px-3.5 font-bold">Jenis Iklan TV</th>
                            <th class="py-3.5 px-3.5 font-bold">Tgl Tayang</th>
                            <th class="py-3.5 px-3.5 font-bold text-right">Harga Iklan</th>
                            <th class="py-3.5 px-3.5 font-bold text-right">Dibayar</th>
                            <th class="py-3.5 px-3.5 font-bold text-right">Piutang</th>
                            <th class="py-3.5 px-3.5 font-bold text-center">Status</th>
                            <th class="py-3.5 px-3.5 font-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 whitespace-nowrap">
                        @forelse($transaksipriangan as $key => $t)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-3.5 text-center text-gray-500 font-normal">
                                    {{ $transaksipriangan->firstItem() + $key }}
                                </td>
                                <td class="py-3 px-3.5 font-medium text-black">
                                    {{ $t->nofakturpriangan }}
                                </td>
                                <td class="py-3 px-3.5 text-gray-600 font-normal">
                                    {{ $t->tanggal_transaksipriangan }}
                                </td>
                                <td class="py-3 px-3.5 font-medium text-black">
                                    {{ $t->nama_pemasangpriangan }}
                                </td>
                                <td class="py-3 px-3.5 text-gray-600 font-normal">
                                    {{ $t->sales_iklanpriangan }}
                                </td>
                                <td class="py-3 px-3.5 text-gray-700 font-normal">
                                    {{ $t->iklanpriangan->jenis_iklanpriangan ?? '-' }}
                                </td>
                                <td class="py-3 px-3.5 text-gray-600 font-normal">
                                    {{ $t->tanggal_muatiklanpriangan }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-semibold text-black font-tabular">
                                    Rp {{ number_format($t->harga_transaksipriangan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-right text-gray-800 font-normal font-tabular">
                                    Rp {{ number_format($t->jumlahbayar_transaksipriangan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-semibold font-tabular {{ $t->piutang_transaksipriangan > 0 ? 'text-red-600' : 'text-gray-500' }}">
                                    Rp {{ number_format($t->piutang_transaksipriangan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @if($t->piutang_transaksipriangan <= 0)
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Lunas
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                            Belum Lunas
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('transaksipriangan.cetak', $t->id) }}" 
                                           target="_blank"
                                           class="px-2.5 py-1 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 rounded-md border border-slate-200 transition">
                                            Cetak
                                        </a>
                                        <button type="button" 
                                                onclick="openEditModal(this)"
                                                data-id="{{ $t->id }}"
                                                data-nofaktur="{{ $t->nofakturpriangan }}"
                                                data-tanggal="{{ $t->tanggal_transaksipriangan }}"
                                                data-nama="{{ $t->nama_pemasangpriangan }}"
                                                data-alamat="{{ $t->alamat_pemasangpriangan }}"
                                                data-id_iklan="{{ $t->id_iklanpriangan }}"
                                                data-sales="{{ $t->sales_iklanpriangan }}"
                                                data-tgl_muat="{{ $t->tanggal_muatiklanpriangan }}"
                                                data-harga="{{ $t->harga_transaksipriangan }}"
                                                data-diskon="{{ $t->diskon_transaksipriangan ?? 0 }}"
                                                data-bayar="{{ $t->jumlahbayar_transaksipriangan }}"
                                                data-qty="{{ $t->total_muatiklanpriangan ?? 1 }}"
                                                data-komisi="{{ $t->komisi_transaksipriangan ?? 0 }}"
                                                data-insentif="{{ $t->insentif_transaksipriangan ?? 0 }}"
                                                class="px-2.5 py-1 text-xs font-medium text-kp-blue-700 bg-kp-blue-50 hover:bg-kp-blue-100 rounded-md border border-kp-blue-200 transition cursor-pointer">
                                            Ubah
                                        </button>
                                        <button type="button" 
                                                onclick="deleteTransaksi('{{ $t->id }}', '{{ $t->nofakturpriangan }}')"
                                                class="px-2.5 py-1 text-xs font-medium text-red-600 bg-white hover:bg-red-50 rounded-md border border-red-200 transition cursor-pointer">
                                            Hapus
                                        </button>
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
