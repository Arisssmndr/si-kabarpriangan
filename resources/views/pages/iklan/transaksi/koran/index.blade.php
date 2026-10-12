<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Konsisten (Standar Laporan dengan Logo Kantor) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Transaksi Iklan Koran
                    </h1>
                    <p class="text-xs font-normal text-slate-500 mt-0.5">
                        Pencatatan dan pengelolaan faktur iklan cetak Harian Umum Kabar Priangan
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('transaksikoran.create') }}" 
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
            <form method="GET" action="{{ route('transaksikoran.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-6">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Pencarian Data
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik no faktur, nama pemasang, atau nama sales..."
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
                        <a href="{{ route('transaksikoran.index') }}" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-lg transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Transaksi Koran -->
        <div class="bg-white border border-slate-300 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-4 bg-slate-50/90 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Transaksi Iklan Koran</h2>
                    <p class="text-xs text-slate-500 mt-0.5 font-normal">Total {{ $transaksikoran->total() }} faktur tercatat</p>
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
                            <th class="py-3.5 px-3.5 font-bold">Jenis Iklan</th>
                            <th class="py-3.5 px-3.5 font-bold">Penempatan & Warna</th>
                            <th class="py-3.5 px-3.5 font-bold">Tgl Terbit</th>
                            <th class="py-3.5 px-3.5 font-bold text-center">Edisi</th>
                            <th class="py-3.5 px-3.5 font-bold text-right">Total Tagihan</th>
                            <th class="py-3.5 px-3.5 font-bold text-right">Dibayar</th>
                            <th class="py-3.5 px-3.5 font-bold text-right">Piutang</th>
                            <th class="py-3.5 px-3.5 font-bold text-center">Status</th>
                            <th class="py-3.5 px-3.5 font-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 whitespace-nowrap">
                        @forelse($transaksikoran as $key => $t)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-3.5 text-center text-gray-500 font-normal">
                                    {{ $transaksikoran->firstItem() + $key }}
                                </td>
                                <td class="py-3 px-3.5 font-medium text-black">
                                    {{ $t->nofakturkoran }}
                                </td>
                                <td class="py-3 px-3.5 text-gray-600 font-normal">
                                    {{ $t->tanggal_transaksikoran }}
                                </td>
                                <td class="py-3 px-3.5 font-medium text-black">
                                    {{ $t->nama_pemasangkoran }}
                                </td>
                                <td class="py-3 px-3.5 text-gray-700 font-normal">
                                    {{ $t->iklankoran->jenis_iklankoran ?? '-' }}
                                </td>
                                <td class="py-3 px-3.5 text-gray-600 font-normal">
                                    {{ $t->halaman_iklan }} · {{ $t->warna_iklan }}
                                </td>
                                <td class="py-3 px-3.5 text-gray-600 font-normal">
                                    {{ $t->tanggal_muatkoran }}
                                </td>
                                <td class="py-3 px-3.5 text-center font-normal text-gray-700">
                                    {{ $t->total_muatkoran }}x
                                </td>
                                <td class="py-3 px-3.5 text-right font-semibold text-black font-tabular">
                                    Rp {{ number_format($t->totaltagihan_transaksikoran, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-right text-gray-800 font-normal font-tabular">
                                    Rp {{ number_format($t->jumlahbayar_transaksikoran, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-semibold font-tabular {{ $t->piutang_transaksikoran > 0 ? 'text-red-600' : 'text-gray-500' }}">
                                    Rp {{ number_format($t->piutang_transaksikoran, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @if($t->piutang_transaksikoran <= 0)
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
                                        <a href="{{ route('transaksikoran.cetak', $t->id) }}" 
                                           target="_blank"
                                           class="px-2.5 py-1 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 rounded-md border border-slate-200 transition">
                                            Cetak
                                        </a>
                                        <button type="button" 
                                                onclick="openEditModal(this)"
                                                data-id="{{ $t->id }}"
                                                data-nofaktur="{{ $t->nofakturkoran }}"
                                                data-tanggal="{{ $t->tanggal_transaksikoran }}"
                                                data-nama="{{ $t->nama_pemasangkoran }}"
                                                data-alamat="{{ $t->alamat_pemasangkoran }}"
                                                data-id_iklan="{{ $t->id_iklankoran }}"
                                                data-halaman="{{ $t->halaman_iklan }}"
                                                data-warna="{{ $t->warna_iklan }}"
                                                data-ukuran="{{ $t->ukuran_iklan }}"
                                                data-sales="{{ $t->sales_iklankoran }}"
                                                data-tgl_muat="{{ $t->tanggal_muatkoran }}"
                                                data-total_muat="{{ $t->total_muatkoran }}"
                                                data-harga="{{ $t->harga_transaksikoran }}"
                                                data-diskon="{{ $t->diskon_transaksikoran }}"
                                                data-bayar="{{ $t->jumlahbayar_transaksikoran }}"
                                                data-komisi="{{ $t->komisi_transaksikoran }}"
                                                data-insentif="{{ $t->insentif_transaksikoran }}"
                                                class="px-2.5 py-1 text-xs font-medium text-kp-blue-700 bg-kp-blue-50 hover:bg-kp-blue-100 rounded-md border border-kp-blue-200 transition cursor-pointer">
                                            Ubah
                                        </button>
                                        <button type="button" 
                                                onclick="deleteTransaksi('{{ $t->id }}', '{{ $t->nofakturkoran }}')"
                                                class="px-2.5 py-1 text-xs font-medium text-red-600 bg-white hover:bg-red-50 rounded-md border border-red-200 transition cursor-pointer">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="py-12 text-center text-slate-500 font-normal">
                                    Belum ada data transaksi. Klik "Transaksi Baru" untuk menambah data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transaksikoran->hasPages())
                <div class="p-4 border-t border-slate-200 bg-white">
                    {{ $transaksikoran->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Modal Edit Transaksi Koran -->
    <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs hidden p-4">
        <div class="bg-white border border-slate-200 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50 sticky top-0 z-10">
                <h3 id="editModalTitle" class="text-sm font-semibold text-black">
                    Ubah Transaksi Iklan Koran
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
                        <input type="date" id="e_tanggal" name="tanggal_transaksikoran" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nama Pemasang</label>
                        <input type="text" id="e_nama" name="nama_pemasangkoran" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Sales Marketing</label>
                        <input type="text" id="e_sales" name="sales_iklankoran" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Alamat Pemasang</label>
                    <input type="text" id="e_alamat" name="alamat_pemasangkoran" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jenis Iklan</label>
                        <select id="e_id_iklan" name="id_iklankoran" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black">
                            @foreach($iklankoran as $ik)
                                <option value="{{ $ik->id }}">{{ $ik->jenis_iklankoran }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Halaman</label>
                        <select id="e_halaman" name="halaman_iklan" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black">
                            <option value="Halaman Dalam">Halaman Dalam</option>
                            <option value="Halaman 1 (Cover)">Halaman 1 (Cover)</option>
                            <option value="Halaman Belakang">Halaman Belakang</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Warna</label>
                        <select id="e_warna" name="warna_iklan" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black">
                            <option value="Hitam Putih (BW)">Hitam Putih (BW)</option>
                            <option value="Full Color (FC)">Full Color (FC)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Ukuran (mmk / Baris)</label>
                        <input type="text" id="e_ukuran" name="ukuran_iklan" placeholder="2 x 100 mmk" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Terbit</label>
                        <input type="date" id="e_tgl_muat" name="tanggal_muatkoran" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah Edisi (Qty)</label>
                        <input type="number" id="e_total_muat" name="total_muatkoran" min="1" required oninput="hitungEdit()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-black" />
                    </div>
                </div>

                <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-3">
                    <p class="text-xs font-semibold text-black uppercase tracking-wider">Perhitungan Keuangan</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Harga Satuan (Rp)</label>
                            <input type="text" id="e_harga" name="harga_transaksikoran" required onkeyup="formatInput(this)" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-black font-tabular" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Diskon (Rp)</label>
                            <input type="text" id="e_diskon" name="diskon_transaksikoran" onkeyup="formatInput(this)" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-black font-tabular" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah Dibayar (Rp)</label>
                            <input type="text" id="e_bayar" name="jumlahbayar_transaksikoran" required onkeyup="formatInput(this)" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-black font-tabular" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 text-xs border-t border-slate-200">
                        <div>
                            <label class="block text-[11px] font-medium text-gray-600 mb-1">DPP (Nilai / 1.11)</label>
                            <input type="text" id="e_dpp" readonly class="w-full px-2.5 py-1.5 bg-gray-100 border border-slate-300 rounded-md text-xs font-medium text-gray-700 font-tabular cursor-not-allowed" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-600 mb-1">PPN (11%)</label>
                            <input type="text" id="e_ppn" readonly class="w-full px-2.5 py-1.5 bg-gray-100 border border-slate-300 rounded-md text-xs font-medium text-gray-700 font-tabular cursor-not-allowed" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-600 mb-1">Komisi (Rp)</label>
                            <input type="text" id="e_komisi" name="komisi_transaksikoran" onkeyup="formatInput(this)" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-md text-xs font-medium text-black font-tabular" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-600 mb-1">Insentif (Rp)</label>
                            <input type="text" id="e_insentif" name="insentif_transaksikoran" onkeyup="formatInput(this)" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-md text-xs font-medium text-black font-tabular" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-2 text-xs border-t border-slate-200">
                        <div>
                            <span class="text-gray-600">Total Tagihan:</span>
                            <p id="e_label_total" class="font-semibold text-kp-blue-700 text-sm font-tabular">Rp 0</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Sisa Piutang:</span>
                            <p id="e_label_piutang" class="font-semibold text-red-600 text-sm font-tabular">Rp 0</p>
                        </div>
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
            document.getElementById('editForm').action = "/transaksikoran/" + d.id;

            document.getElementById('e_nofaktur').value = d.nofaktur;
            document.getElementById('e_tanggal').value = d.tanggal;
            document.getElementById('e_nama').value = d.nama;
            document.getElementById('e_alamat').value = d.alamat;
            document.getElementById('e_sales').value = d.sales;
            document.getElementById('e_id_iklan').value = d.id_iklan;
            document.getElementById('e_halaman').value = d.halaman;
            document.getElementById('e_warna').value = d.warna;
            document.getElementById('e_ukuran').value = d.ukuran;
            document.getElementById('e_tgl_muat').value = d.tgl_muat;
            document.getElementById('e_total_muat').value = d.total_muat;

            document.getElementById('e_harga').value = formatRupiah(d.harga);
            document.getElementById('e_diskon').value = formatRupiah(d.diskon);
            document.getElementById('e_bayar').value = formatRupiah(d.bayar);
            document.getElementById('e_komisi').value = formatRupiah(d.komisi || 0);
            document.getElementById('e_insentif').value = formatRupiah(d.insentif || 0);

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
            const qty = parseFloat(document.getElementById('e_total_muat').value) || 1;

            const omset = harga * qty;
            let total = omset - diskon;
            if (total < 0) total = 0;

            const dpp = total / 1.11;
            const ppn = total - dpp;
            const piutang = Math.max(0, total - bayar);

            if (document.getElementById('e_dpp')) {
                document.getElementById('e_dpp').value = formatRupiah(Math.round(dpp));
            }
            if (document.getElementById('e_ppn')) {
                document.getElementById('e_ppn').value = formatRupiah(Math.round(ppn));
            }

            document.getElementById('e_label_total').innerText = 'Rp ' + formatRupiah(Math.round(total));
            const elPiutang = document.getElementById('e_label_piutang');
            elPiutang.innerText = 'Rp ' + formatRupiah(Math.round(piutang));
            if (piutang > 0) {
                elPiutang.className = 'font-semibold text-red-600 text-sm font-tabular';
            } else {
                elPiutang.className = 'font-semibold text-emerald-600 text-sm font-tabular';
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
                        await axios.post('/transaksikoran/' + id, {
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
