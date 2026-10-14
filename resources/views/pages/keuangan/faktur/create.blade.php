<x-app-layout>
    <div class="space-y-6" x-data="{
        kategori: '{{ old('kategori', $kategoriDefault) }}',
        tipePembayaran: '{{ old('tipe_pembayaran', 'lunas_langsung') }}',
        nominalDp: {{ old('nominal_dp', 0) }},
        items: [
            { nama: '', qty: 1, harga: 0, subtotal: 0 }
        ],
        addItem() {
            this.items.push({ nama: '', qty: 1, harga: 0, subtotal: 0 });
        },
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },
        calculateSubtotal(item) {
            item.subtotal = (Number(item.qty) || 0) * (Number(item.harga) || 0);
        },
        get totalTagihan() {
            return this.items.reduce((sum, item) => sum + (Number(item.subtotal) || 0), 0);
        },
        get sisaPiutang() {
            if (this.tipePembayaran === 'lunas_langsung') return 0;
            return Math.max(0, this.totalTagihan - (Number(this.nominalDp) || 0));
        },
        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
    }">

        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('keuangan.faktur.index') }}" class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition">
                    ←
                </a>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                        PENERBITAN FAKTUR BARU
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Isi rincian transaksi penjualan divisi Iklan atau Sirkulasi dan tentukan skema pembayaran
                    </p>
                </div>
            </div>

            <div class="text-xs font-mono font-bold text-blue-700 bg-blue-50 px-3 py-1.5 rounded-xl border border-blue-200">
                SOP KABAR PRIANGAN
            </div>
        </div>

        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('keuangan.faktur.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Blok 1: Informasi Dasar & Customer -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    1. Data Transaksi &amp; Pelanggan
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                    <!-- Divisi / Kategori -->
                    <div class="sm:col-span-4">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Divisi / Kanal <span class="text-rose-500">*</span></label>
                        <select name="kategori" x-model="kategori" required class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500 font-semibold text-slate-800">
                            <option value="iklan">Divisi Iklan (Koran, Online, TV)</option>
                            <option value="sirkulasi">Divisi Sirkulasi (Agen &amp; Langganan)</option>
                        </select>
                    </div>

                    <!-- Nomor Faktur -->
                    <div class="sm:col-span-4">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Faktur <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_faktur" value="{{ old('no_faktur', $noFakturSuggested) }}" required
                               class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 px-3 py-2.5 bg-slate-50 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Pilihan Customer -->
                    <div class="sm:col-span-4">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Customer / Klien <span class="text-rose-500">*</span></label>
                        <select name="customer_id" required class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->nama_customer }} ({{ ucfirst($c->tipe) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tanggal Faktur -->
                    <div class="sm:col-span-6">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Transaksi Faktur <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_faktur" value="{{ old('tanggal_faktur', date('Y-m-d')) }}" required
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Tanggal Jatuh Tempo -->
                    <div class="sm:col-span-6">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Jatuh Tempo <span class="text-rose-500">*</span></label>
                        <input type="date" name="jatuh_tempo" value="{{ old('jatuh_tempo', date('Y-m-d', strtotime('+14 days'))) }}" required
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Blok 2: Rincian Biaya Transaksi -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        2. Rincian Biaya &amp; Item Tagihan
                    </h2>

                    <button type="button" @click="addItem()" class="h-8 px-3 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold flex items-center gap-1 transition">
                        + Tambah Baris Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 text-[11px] uppercase text-slate-500 font-bold border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-3">Uraian / Deskripsi Item</th>
                                <th class="py-2.5 px-3 w-28 text-center">Qty</th>
                                <th class="py-2.5 px-3 w-44 text-right">Harga Satuan (Rp)</th>
                                <th class="py-2.5 px-3 w-44 text-right">Subtotal (Rp)</th>
                                <th class="py-2.5 px-3 w-16 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td class="py-2.5 px-3">
                                        <input type="text" :name="'item_nama[' + index + ']'" x-model="item.nama" required
                                               placeholder="Contoh: Iklan Display 1 Halaman Berwarna / Oplah Koran 500 eks..."
                                               class="w-full text-xs rounded-lg border border-slate-300 px-2.5 py-1.5 focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <input type="number" :name="'item_qty[' + index + ']'" x-model="item.qty" @input="calculateSubtotal(item)" min="1" required
                                               class="w-full text-xs text-center rounded-lg border border-slate-300 px-2.5 py-1.5 focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <input type="number" :name="'item_harga[' + index + ']'" x-model="item.harga" @input="calculateSubtotal(item)" min="0" step="1000" required
                                               class="w-full text-xs text-right rounded-lg border border-slate-300 px-2.5 py-1.5 focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp <span x-text="formatRupiah(item.subtotal)"></span>
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        <button type="button" @click="removeItem(index)" :disabled="items.length === 1"
                                                class="w-7 h-7 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition disabled:opacity-30">
                                            ✕
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="border-t-2 border-slate-200 bg-slate-50">
                            <tr>
                                <td colspan="3" class="py-3 px-3 text-right font-bold text-slate-700 uppercase">
                                    Total Tagihan Faktur:
                                </td>
                                <td class="py-3 px-3 text-right font-black text-slate-900 text-sm font-mono">
                                    Rp <span x-text="formatRupiah(totalTagihan)"></span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Blok 3: Logika Pembayaran & Integrasi Inkaso/Kas -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    3. Logika Pembayaran saat Faktur Dibuat
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Opsi 1: Lunas Langsung -->
                    <label class="p-4 rounded-2xl border-2 cursor-pointer transition flex items-start gap-3.5"
                           :class="tipePembayaran === 'lunas_langsung' ? 'border-emerald-500 bg-emerald-50/60 ring-2 ring-emerald-200' : 'border-slate-200 bg-slate-50 hover:bg-white'">
                        <input type="radio" name="tipe_pembayaran" value="lunas_langsung" x-model="tipePembayaran" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-black text-emerald-950 uppercase">1. Bayar Lunas Langsung (100%)</span>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-emerald-200 text-emerald-800">Tanpa Inkaso</span>
                            </div>
                            <p class="text-[11px] text-emerald-800 mt-1 leading-relaxed">
                                Transaksi dibayar penuh saat faktur dibuat. <strong>Otomatis mencatat Kas Masuk 100%</strong> dan <strong>TIDAK masuk antrean petugas Inkaso</strong>.
                            </p>
                        </div>
                    </label>

                    <!-- Opsi 2: Cicilan / Tempo -->
                    <label class="p-4 rounded-2xl border-2 cursor-pointer transition flex items-start gap-3.5"
                           :class="tipePembayaran === 'cicilan_tempo' ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-200' : 'border-slate-200 bg-slate-50 hover:bg-white'">
                        <input type="radio" name="tipe_pembayaran" value="cicilan_tempo" x-model="tipePembayaran" class="mt-1 text-amber-600 focus:ring-amber-500">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-black text-amber-950 uppercase">2. Bayar Bertahap / Nyicil / Tempo</span>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-amber-200 text-amber-800">Wajib Inkaso</span>
                            </div>
                            <p class="text-[11px] text-amber-800 mt-1 leading-relaxed">
                                Jika ada uang muka (DP), nominal DP langsung masuk Kas Masuk. <strong>Sisa piutang otomatis masuk ke modul Penugasan Inkaso</strong>.
                            </p>
                        </div>
                    </label>
                </div>

                <!-- Input Khusus jika Cicilan/Tempo -->
                <div x-show="tipePembayaran === 'cicilan_tempo'" x-transition class="p-4 rounded-xl bg-amber-50 border border-amber-200 grid grid-cols-1 sm:grid-cols-12 gap-4">
                    <div class="sm:col-span-6">
                        <label class="block text-xs font-bold text-amber-900 mb-1">Nominal Uang Muka / DP (Rp)</label>
                        <input type="number" name="nominal_dp" x-model="nominalDp" min="0" :max="totalTagihan" step="1000"
                               placeholder="0 jika belum ada pembayaran"
                               class="w-full text-xs font-mono font-bold rounded-lg border border-amber-300 px-3 py-2 bg-white focus:ring-2 focus:ring-amber-500">
                        <span class="text-[10px] text-amber-700 mt-0.5 block">Isi 0 jika tempo murni (belum ada pembayaran sama sekali).</span>
                    </div>

                    <div class="sm:col-span-6 flex flex-col justify-center">
                        <div class="text-xs text-amber-800 font-semibold">Simulasi Sisa Piutang Berjalan:</div>
                        <div class="text-lg font-black text-rose-600 font-mono mt-0.5">
                            Rp <span x-text="formatRupiah(sisaPiutang)"></span>
                        </div>
                        <span class="text-[10px] text-slate-500">Nominal ini yang akan ditagihkan oleh Petugas Inkaso lapangan.</span>
                    </div>
                </div>

                <!-- Metode Pembayaran & Catatan -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 pt-2">
                    <div class="sm:col-span-4">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Metode Penerimaan Dana <span class="text-rose-500">*</span></label>
                        <select name="metode_bayar" required class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                            <option value="tunai">Tunai / Cash Kasir</option>
                            <option value="transfer">Transfer Rekening Bank</option>
                        </select>
                    </div>

                    <div class="sm:col-span-8">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                        <input type="text" name="catatan" placeholder="Catatan kesepakatan tempo, nomor PO customer, dll..."
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('keuangan.faktur.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                    Terbitkan Faktur Sekarang ➔
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
