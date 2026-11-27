<x-app-layout>
    <div class="space-y-5 pb-10" 
         @open-cetak-modal.window="showCetakModal = true"
         x-data="{ 
        showFormModal: false,
        showEditModal: false,
        showCetakModal: false,
        tunaiForm: {
            pembeli: '',
            kategori: 'Koran Sisa Cetak / Eceran Kiloan (KI-14)',
            jenis_satuan: 'kiloan',
            jumlah_satuan: '',
            harga_per_satuan: 6000,
            keterangan: '',
            jumlah: '',
            metode: 'Tunai Kasir'
        },
        hitungKoranSisa() {
            if (this.tunaiForm.kategori.includes('Koran Sisa')) {
                const vol = parseFloat(this.tunaiForm.jumlah_satuan) || 0;
                const hrg = parseFloat(this.tunaiForm.harga_per_satuan) || 0;
                if (vol > 0 && hrg > 0) {
                    this.tunaiForm.jumlah = Math.round(vol * hrg);
                }
            }
        },
        editForm: {
            id: null,
            pembeli: '',
            keterangan: '',
            jumlah: 0,
            metode: 'tunai',
            alasan_koreksi: ''
        },
        openEditDirect(data) {
            this.editForm = {
                id: data.id,
                pembeli: data.pembeli,
                keterangan: data.keterangan,
                jumlah: data.jumlah,
                metode: data.metode,
                alasan_koreksi: ''
            };
            this.showEditModal = true;
        }
    }">

        <!-- Konten Halaman Kasir (Disembunyikan saat modal cetak aktif) -->
        <div class="space-y-5" :class="{ 'print:hidden': showCetakModal }">

        <!-- Flash Message Alert -->
        @if(session('success'))
            <div class="p-4 bg-kp-blue-50 border border-kp-blue-200 rounded-xl text-xs font-semibold text-kp-blue-800 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-kp-blue-500 hover:text-kp-blue-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- 1. Header Page Penjualan Langsung / Tunai (60-30-10 Palette) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Penerimaan Tunai / Penerimaan Lain-lain (KI-14)
                        </h1>
                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            KI-14
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Pencatatan kas masuk langsung (Penjualan Koran Sisa Cetak / Eceran Kiloan, Iklan Baris Lepas, & Pendapatan Lain-lain)
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                @if(session('cetak_tunai'))
                    <button type="button" @click="showCetakModal = true" class="inline-flex items-center gap-2 h-10 px-4 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl border border-slate-300 transition shadow-2xs">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Ulang Nota Terakhir</span>
                    </button>
                @endif
                <button type="button" @click="showFormModal = true" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Input Transaksi Tunai</span>
                </button>
            </div>
        </div>

        <!-- 2. Cards Kategori Penerimaan Tunai (60-30-10 Palette) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Iklan Baris & Kolom Lepas</span>
                <p class="text-base font-bold text-slate-900 mt-1">Bayar Langsung Meja Kasir</p>
                <span class="text-xs text-slate-400 mt-1 block">Pemasangan iklan pengumuman duka cita, kehilangan STNK/BPKB, lowongan singkat.</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Penjualan Koran Eceran Kantor</span>
                <p class="text-base font-bold text-slate-900 mt-1">Penjualan Off-the-shelf</p>
                <span class="text-xs text-slate-400 mt-1 block">Pembelian koran eceran langsung oleh tamu, masyarakat umum, & dinas pemerintah.</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Limbah Percetakan (Afval)</span>
                <p class="text-base font-bold text-kp-blue-700 mt-1">Pendapatan Lain-Lain Sah</p>
                <span class="text-xs text-slate-400 mt-1 block">Penjualan sisa kertas potongan percetakan, koran retur kedaluwarsa, & plat aluminium.</span>
            </div>
        </div>

        <!-- 3. Pure Table Riwayat Penjualan Langsung (Tanpa Banner Tempel di Atas Tabel) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-[#0A72AC] border-b-2 border-[#FFB900] text-white font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3">No. BKM / Kwitansi</th>
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Pembeli / Pembayar</th>
                            <th class="py-3 px-3">Kategori & Uraian</th>
                            <th class="py-3 px-3">Metode</th>
                            <th class="py-3 px-3">Kasir</th>
                            <th class="py-3 px-3 text-right">Jumlah (Rp)</th>
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                        @forelse($recentTunai as $t)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3 font-mono font-bold text-kp-blue-700">{{ $t->no_bkm }}</td>
                            <td class="py-3 px-3 text-slate-500">{{ $t->tgl_transaksi->translatedFormat('d M Y') }}</td>
                            <td class="py-3 px-3 font-semibold text-slate-900">{{ $t->nama_pembayar }}</td>
                            <td class="py-3 px-3 text-slate-700">
                                <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200 mr-1">Direct</span>
                                {{ $t->keterangan }}
                            </td>
                            <td class="py-3 px-3 text-slate-600 uppercase font-mono text-xs">{{ $t->metode_bayar }}</td>
                            <td class="py-3 px-3 text-slate-500">{{ $t->kasir->name ?? 'Kasir' }}</td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($t->jumlah_dibayar, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                @if(!$t->is_closed && !$t->status_void)
                                    <button type="button"
                                            @click="openEditDirect({
                                                id: {{ $t->id }},
                                                pembeli: '{{ addslashes($t->nama_pembayar ?? $t->terima_dari ?? '') }}',
                                                keterangan: '{{ addslashes($t->keterangan ?? '') }}',
                                                jumlah: {{ $t->jumlah_dibayar }},
                                                metode: '{{ $t->metode_bayar }}'
                                            })"
                                            class="inline-flex items-center gap-1 h-8 px-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 font-semibold text-xs rounded-lg transition shadow-2xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Edit</span>
                                    </button>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">Terkunci</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400 text-xs">Belum ada transaksi penjualan langsung hari ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form Input Penjualan Langsung -->
        <div x-cloak x-show="showFormModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showFormModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between shrink-0 bg-slate-50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-kp-blue-600 text-white flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Input Transaksi Penjualan Langsung</h4>
                            <p class="text-xs text-slate-500">Penerimaan kas masuk langsung & cetak nota kwitansi</p>
                        </div>
                    </div>
                    <button type="button" @click="showFormModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
                </div>

                <form method="POST" action="{{ route('kasir.penerimaan.tunai.store') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf

                    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pembeli / Pembayar</label>
                            <input type="text" name="pembeli" x-model="tunaiForm.pembeli" required placeholder="Contoh: Bpk. Hendra / Dinas Pendidikan" 
                                   class="w-full h-11 px-3.5 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-sm text-slate-900">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Transaksi (KI-14)</label>
                            <select name="kategori" x-model="tunaiForm.kategori" @change="hitungKoranSisa()" required class="w-full h-11 px-3.5 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-sm text-slate-800 bg-white">
                                <option value="Koran Sisa Cetak / Eceran Kiloan (KI-14)">Koran Sisa Cetak / Eceran Kiloan (KI-14)</option>
                                <option value="Pembelian Koran Eceran Sisa Stock">Pembelian Koran Eceran Kantor (Eksemplar)</option>
                                <option value="Iklan Baris / Kolom Kecil">Iklan Baris / Kolom Kecil (Bayar Tunai Loket)</option>
                                <option value="Penjualan Kertas Afval / Koran Bekas">Penjualan Kertas Afval / Plat Aluminium Bekas</option>
                                <option value="Pendapatan Kasir Lainnya">Penerimaan Tunai Lain-Lain (KI-14)</option>
                            </select>
                        </div>

                        <!-- Kalkulator Otomatis Koran Sisa / Eceran Kiloan -->
                        <div x-show="tunaiForm.kategori.includes('Koran Sisa')" class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2.5">
                            <div class="text-xs font-semibold text-slate-800 flex items-center justify-between">
                                <span>Kalkulator Koran Sisa Cetak / Eceran (KI-14)</span>
                                <span class="font-mono text-xs px-2 py-0.5 rounded bg-white border border-slate-200 text-slate-700">COA 7.00.02.01.02</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="block text-xs text-slate-600 mb-1">Satuan</label>
                                    <select name="jenis_satuan" x-model="tunaiForm.jenis_satuan" @change="tunaiForm.harga_per_satuan = (tunaiForm.jenis_satuan === 'kiloan' ? 6000 : 3500); hitungKoranSisa()" class="w-full h-9 px-2 rounded-lg border border-slate-300 text-xs text-slate-800 bg-white">
                                        <option value="kiloan">Kiloan (Kg)</option>
                                        <option value="eksemplar">Eceran (Eks)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-600 mb-1" x-text="tunaiForm.jenis_satuan === 'kiloan' ? 'Berat (Kg)' : 'Jumlah (Eks)'"></label>
                                    <input type="number" step="0.1" name="jumlah_satuan" x-model="tunaiForm.jumlah_satuan" @input="hitungKoranSisa()" placeholder="0"
                                           class="w-full h-9 px-2 font-mono text-xs font-semibold rounded-lg border border-slate-300 text-slate-900 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-600 mb-1">Harga Satuan (Rp)</label>
                                    <input type="number" name="harga_per_satuan" x-model="tunaiForm.harga_per_satuan" @input="hitungKoranSisa()"
                                           class="w-full h-9 px-2 font-mono text-xs font-semibold rounded-lg border border-slate-300 text-slate-900 bg-white">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Uraian / Keterangan Transaksi</label>
                            <textarea name="keterangan" x-model="tunaiForm.keterangan" required rows="2" placeholder="Contoh: Penjualan koran sisa cetak / retur bulan lalu (kiloan)..." 
                                      class="w-full text-sm p-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-slate-900"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Diterima (Rp)</label>
                                <input type="number" name="jumlah" x-model="tunaiForm.jumlah" required min="1" placeholder="75000" 
                                       class="w-full h-11 px-3.5 font-mono text-base font-bold rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-slate-900">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Pembayaran</label>
                                <select name="metode" x-model="tunaiForm.metode" required class="w-full h-11 px-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-sm text-slate-800 bg-white">
                                    <option value="Tunai Kasir">Tunai Meja Kasir</option>
                                    <option value="Transfer QRIS">Transfer QRIS Resmi</option>
                                    <option value="Transfer Bank BJB">Transfer Bank BJB</option>
                                    <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showFormModal = false" class="h-10 px-4 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-300 transition">
                            Batal
                        </button>
                        <button type="submit" class="h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl transition shadow-2xs">
                            Simpan & Cetak Nota Kasir
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Transaksi Tunai -->
        <div x-cloak x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showEditModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between shrink-0 bg-slate-50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-kp-blue-600 text-white flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Edit Input Transaksi Tunai</h4>
                            <p class="text-xs text-slate-500">Perbaikan data penjualan langsung & rekonsiliasi kas</p>
                        </div>
                    </div>
                    <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
                </div>

                <form method="POST" :action="'/kasir/utilitas/koreksi/' + editForm.id + '/update'" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="tipe_dokumen" value="KI">

                    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pembeli / Pembayar <span class="text-slate-500">*</span></label>
                            <input type="text" name="nama_pihak" x-model="editForm.pembeli" required
                                   class="w-full h-11 px-3.5 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-sm text-slate-900 font-semibold">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Uang Diterima (Rp) <span class="text-slate-500">*</span></label>
                            <input type="number" name="jumlah_nominal" x-model="editForm.jumlah" required min="1"
                                   class="w-full h-11 px-3.5 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 font-mono text-base font-bold text-slate-900">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Pembayaran</label>
                            <select name="metode_bayar" x-model="editForm.metode" class="w-full h-11 px-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-sm text-slate-900 bg-white">
                                <option value="tunai">Tunai / Kas Meja</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="qris">QRIS / Digital</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Uraian</label>
                            <textarea name="keterangan" x-model="editForm.keterangan" rows="2"
                                      class="w-full text-sm p-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-slate-900"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Koreksi <span class="text-slate-500">*</span></label>
                            <input type="text" name="alasan_koreksi" x-model="editForm.alasan_koreksi" required minlength="3"
                                   placeholder="Contoh: Koreksi nominal salah ketik..."
                                   class="w-full h-11 px-3.5 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-sm text-slate-900 font-medium">
                        </div>
                    </div>

                    <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showEditModal = false" class="h-10 px-4 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-300 transition">
                            Batal
                        </button>
                        <button type="submit" class="h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <!-- Modal Cetak Nota Penjualan Langsung Otomatis - Format Continuous Form Dot Matrix -->
        @if(session('cetak_tunai'))
        @php $nota = session('cetak_tunai'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="max-w-4xl w-full max-h-[92vh] flex flex-col">
                <div class="mb-3 flex items-center justify-between print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs font-sans cursor-pointer">
                        ← Kembali ke Daftar Penerimaan Tunai
                    </button>
                    <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition cursor-pointer font-sans">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Lembar Nota Tunai (Print / PDF)</span>
                    </button>
                </div>

                <div class="print-document-sheet dot-matrix-container bg-white rounded-lg shadow-2xl overflow-y-auto border border-slate-300 flex font-dot-matrix text-black">
                    <div class="w-8 bg-slate-50 border-r border-dashed border-slate-300 py-6 flex flex-col items-center justify-between gap-4 select-none shrink-0 tractor-feed print:hidden">
                        @for ($i = 0; $i < 14; $i++)
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-200 border border-slate-300"></div>
                        @endfor
                    </div>

                    <div class="print-document-body flex-1 p-6 sm:p-8 text-[12px] leading-relaxed text-black">
                        <div class="text-left mb-3">
                            <h1 class="text-base sm:text-lg font-black tracking-wider uppercase font-dot-matrix text-black">
                                NOTA PENERIMAAN TUNAI (BKM) HU. PRIANGAN
                            </h1>
                            <p class="text-[11px] font-bold text-neutral-700 tracking-tight leading-tight">
                                JL. DINDINGARI RAYA NO.12 TLP. (0265)-335300, FAX. 335677 PERUM BUMI RESIK PANGLAYUNGAN TASIKMALAYA 46134
                            </p>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-3"></div>

                        <div class="grid grid-cols-12 gap-x-4 gap-y-1 mb-3">
                            <div class="col-span-7 space-y-0.5">
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Identitas</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold">{{ $nota['kode_perkiraan'] ?? '1.00.01.01.00.01' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Nama</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold uppercase">{{ $nota['pembeli'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Metode</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold">{{ $nota['metode'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Kota</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold">TASIKMALAYA</span>
                                </div>
                            </div>

                            <div class="col-span-5 space-y-0.5">
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">No. Kuitansi</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold font-mono text-blue-900">{{ $nota['no_bkm'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">Jenis Transaksi</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold">{{ $nota['kategori'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">Tanggal</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold">{{ $nota['tanggal'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-3"></div>

                        <div class="grid grid-cols-12 gap-x-3 gap-y-1 mb-3">
                            <div class="col-span-4 flex">
                                <span class="w-20 shrink-0 font-bold whitespace-nowrap">Kategori</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase">{{ $nota['kategori'] }}</span>
                            </div>
                            <div class="col-span-3 flex">
                                <span class="w-12 shrink-0 font-bold whitespace-nowrap">Tipe</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase">TUNAI (KI-14)</span>
                            </div>
                            <div class="col-span-5 flex">
                                <span class="w-24 shrink-0 font-bold whitespace-nowrap">Keterangan</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase text-[11px] leading-tight">{{ $nota['rincian'] }}</span>
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-3"></div>

                        <div class="space-y-1 max-w-lg mb-4 font-bold">
                            <div class="flex justify-between">
                                <span>Brutto</span>
                                <span>Rp. {{ $nota['terbilang'] }},00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Diskon 0,00 %</span>
                                <span>Rp. 0,00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Netto</span>
                                <span>Rp. {{ $nota['terbilang'] }},00</span>
                            </div>
                            <div class="border-t border-dashed border-black pt-1 flex justify-between text-sm font-black">
                                <span>Jumlah Total Diterima</span>
                                <span>Rp. {{ $nota['terbilang'] }},00</span>
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-6"></div>

                        <div class="grid grid-cols-2 gap-4 items-end pt-2">
                            <div class="pl-4">
                                <div class="inline-block border-2 border-emerald-600 text-emerald-700 px-5 py-2 rounded-full font-black text-center rotate-[-4deg] opacity-90">
                                    <div class="text-[9px] uppercase tracking-widest">KABAR PRIANGAN</div>
                                    <div class="text-sm tracking-wider border-t border-b border-emerald-600 my-0.5 py-0.5">L U N A S</div>
                                    <div class="text-[8px] font-mono">DIVISI KEUANGAN</div>
                                </div>
                            </div>

                            <div class="text-right flex flex-col gap-5">
                                <div>
                                    <p class="text-[11px] font-bold">Tasikmalaya, {{ date('d-m-Y') }}</p>
                                    <p class="text-[11px] font-semibold mt-0.5">Bagian Kasir,</p>
                                    <div class="h-10"></div>
                                    <p class="font-bold underline text-[11px] uppercase">{{ $nota['kasir'] }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold">Pembeli / Penyetor,</p>
                                    <div class="h-10"></div>
                                    <p class="font-bold underline text-[11px] uppercase">{{ $nota['pembeli'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-8 bg-slate-50 border-l border-dashed border-slate-300 py-6 flex flex-col items-center justify-between gap-4 select-none shrink-0 tractor-feed print:hidden">
                        @for ($i = 0; $i < 14; $i++)
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-200 border border-slate-300"></div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>