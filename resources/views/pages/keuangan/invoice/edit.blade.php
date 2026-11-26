<x-app-layout>
    <div class="space-y-6" x-data="invoiceEditForm({
        invoice: @json($invoice)
    })">

        <!-- Header Navigasi -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('keuangan.invoice.show', $invoice->id) }}" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl font-extrabold text-blue-950 tracking-tight">
                        EDIT INVOICE &amp; KWITANSI
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        No. Invoice: <span class="font-bold text-blue-800">{{ $invoice->no_invoice }}</span> &bull; Ditujukan kepada {{ $invoice->kepada }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('keuangan.invoice.show', $invoice->id) }}" 
                   class="h-10 px-4 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-bold flex items-center gap-2 transition">
                    <span>Batal &amp; Kembali</span>
                </a>
            </div>
        </div>

        @if(isset($errors) && $errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold">
                <div class="font-bold mb-1">Perhatian! Ada beberapa data yang belum lengkap:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('keuangan.invoice.update', $invoice->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <input type="hidden" name="transaksi_koran_id" :value="form.transaksi_koran_id">
            <input type="hidden" name="transaksi_online_id" :value="form.transaksi_online_id">

            <!-- Card 1: Identitas Dokumen -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">
                            1. Identitas Dokumen &amp; Penerima (Kepada / To)
                        </h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Nomor Invoice <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="no_invoice" 
                               x-model="form.no_invoice" 
                               required 
                               class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Tanggal Invoice <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" 
                               name="tanggal_invoice" 
                               x-model="form.tanggal_invoice" 
                               required 
                               class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Status Pembayaran <span class="text-rose-500">*</span>
                        </label>
                        <select name="status_pembayaran" 
                                x-model="form.status_pembayaran" 
                                required 
                                class="w-full text-xs font-semibold rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500">
                            <option value="belum_lunas">Belum Lunas (Piutang)</option>
                            <option value="lunas">Sudah Lunas</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Kepada / To (Nama Instansi / Perusahaan) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="kepada" 
                               x-model="form.kepada" 
                               required 
                               class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Attn (Ditujukan Ke)
                        </label>
                        <input type="text" 
                               name="attn" 
                               x-model="form.attn" 
                               class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Alamat Lengkap Instansi / Pemasang
                        </label>
                        <input type="text" 
                               name="alamat" 
                               x-model="form.alamat" 
                               class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Card 2: Rincian Materi Iklan -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">
                            2. Rincian Deskripsi &amp; Nilai Tagihan
                        </h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="hitungDariTotal()" 
                                class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 hover:bg-amber-100 transition border border-amber-200 cursor-pointer">
                            ⚡ Hitung DPP &amp; PPN 11% dari Total
                        </button>
                        <button type="button" 
                                @click="hitungDariDpp()" 
                                class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-blue-50 text-blue-800 hover:bg-blue-100 transition border border-blue-200 cursor-pointer">
                            ⚡ Hitung PPN &amp; Total dari DPP
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Deskripsi / Rincian Iklan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="deskripsi" 
                              rows="4" 
                              x-model="form.deskripsi" 
                              required 
                              class="w-full text-xs rounded-xl border border-slate-300 py-2 px-3 focus:ring-2 focus:ring-blue-500 leading-relaxed font-sans"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Qty</label>
                        <input type="number" 
                               name="qty" 
                               min="1" 
                               x-model.number="form.qty" 
                               @input="onQtyPriceChange()" 
                               required 
                               class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Unit Price (DPP) Rp</label>
                        <input type="number" 
                               step="0.01" 
                               name="unit_price" 
                               x-model.number="form.unit_price" 
                               @input="onUnitPriceChange()" 
                               required 
                               class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Total DPP (Rp)</label>
                        <input type="number" 
                               step="0.01" 
                               name="dpp" 
                               x-model.number="form.dpp" 
                               @input="hitungDariDpp()" 
                               required 
                               class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500 bg-slate-50">
                        <input type="hidden" name="subtotal" :value="form.dpp">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">PPN 11% (Rp)</label>
                        <input type="number" 
                               step="0.01" 
                               name="ppn_nominal" 
                               x-model.number="form.ppn_nominal" 
                               @input="hitungTotalManual()" 
                               required 
                               class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500 bg-slate-50">
                        <input type="hidden" name="ppn_persen" :value="11.00">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-900 mb-1">Grand Total (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" 
                               step="0.01" 
                               name="total_amount" 
                               x-model.number="form.total_amount" 
                               @input="hitungDariTotal()" 
                               required 
                               class="w-full text-sm font-black text-blue-900 rounded-xl border-2 border-blue-500 py-2 px-3 focus:ring-2 focus:ring-blue-500 bg-blue-50/40">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Terbilang (Sum of)
                    </label>
                    <div class="relative">
                        <input type="text" 
                               name="terbilang" 
                               x-model="form.terbilang" 
                               class="w-full text-xs font-bold italic rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500 bg-amber-50/30 text-amber-900">
                        <button type="button" 
                                @click="generateTerbilang()" 
                                class="absolute right-2 top-2 px-2.5 py-1 text-[10px] font-bold bg-amber-200 hover:bg-amber-300 text-amber-900 rounded-lg transition cursor-pointer">
                            Update Terbilang
                        </button>
                    </div>
                </div>
            </div>

            <!-- Card 3: Info Rekening & Penandatangan -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">
                            3. Info Rekening Bank &amp; Penandatangan
                        </h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Instruksi Pembayaran Transfer Bank (Note Invoice)
                        </label>
                        <textarea name="catatan_transfer" 
                                  rows="6" 
                                  x-model="form.catatan_transfer" 
                                  class="w-full text-xs rounded-xl border border-slate-300 py-2 px-3 focus:ring-2 focus:ring-blue-500 font-mono leading-relaxed"></textarea>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kota Penerbitan</label>
                            <input type="text" 
                                   name="kota_terbit" 
                                   x-model="form.kota_terbit" 
                                   required 
                                   class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2 px-3 focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Penandatangan</label>
                            <input type="text" 
                                   name="nama_penandatangan" 
                                   x-model="form.nama_penandatangan" 
                                   required 
                                   class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2 px-3 focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan Penandatangan</label>
                            <input type="text" 
                                   name="jabatan_penandatangan" 
                                   x-model="form.jabatan_penandatangan" 
                                   required 
                                   class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2 px-3 focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi Simpan -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('keuangan.invoice.show', $invoice->id) }}" 
                   class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold shadow-md transition cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>

    </div>

    <script>
        function invoiceEditForm(config) {
            const inv = config.invoice;
            return {
                form: {
                    no_invoice: inv.no_invoice,
                    transaksi_koran_id: inv.transaksi_koran_id,
                    transaksi_online_id: inv.transaksi_online_id,
                    kepada: inv.kepada,
                    alamat: inv.alamat || '',
                    attn: inv.attn || 'Bagian Keuangan',
                    tanggal_invoice: inv.tanggal_invoice ? inv.tanggal_invoice.split('T')[0] : '',
                    deskripsi: inv.deskripsi,
                    qty: inv.qty,
                    unit_price: parseFloat(inv.unit_price) || 0,
                    dpp: parseFloat(inv.dpp) || 0,
                    ppn_nominal: parseFloat(inv.ppn_nominal) || 0,
                    total_amount: parseFloat(inv.total_amount) || 0,
                    terbilang: inv.terbilang || '',
                    catatan_transfer: inv.catatan_transfer || '',
                    kota_terbit: inv.kota_terbit || 'Tasikmalaya',
                    nama_penandatangan: inv.nama_penandatangan || 'Eli Yulianti',
                    jabatan_penandatangan: inv.jabatan_penandatangan || 'Manager Keuangan',
                    status_pembayaran: inv.status_pembayaran || 'belum_lunas'
                },

                hitungDariTotal() {
                    const total = parseFloat(this.form.total_amount) || 0;
                    const dpp = Math.round(total / 1.11);
                    const ppn = Math.round(total - dpp);
                    this.form.dpp = dpp;
                    this.form.ppn_nominal = ppn;
                    const qty = parseInt(this.form.qty) || 1;
                    this.form.unit_price = qty > 0 ? Math.round(dpp / qty) : dpp;
                    this.generateTerbilang();
                },

                hitungDariDpp() {
                    const dpp = parseFloat(this.form.dpp) || 0;
                    const ppn = Math.round(dpp * 0.11);
                    const total = dpp + ppn;
                    this.form.ppn_nominal = ppn;
                    this.form.total_amount = total;
                    const qty = parseInt(this.form.qty) || 1;
                    this.form.unit_price = qty > 0 ? Math.round(dpp / qty) : dpp;
                    this.generateTerbilang();
                },

                onQtyPriceChange() {
                    const qty = parseInt(this.form.qty) || 1;
                    const unitPrice = parseFloat(this.form.unit_price) || 0;
                    this.form.dpp = Math.round(qty * unitPrice);
                    this.hitungDariDpp();
                },

                onUnitPriceChange() {
                    this.onQtyPriceChange();
                },

                hitungTotalManual() {
                    const dpp = parseFloat(this.form.dpp) || 0;
                    const ppn = parseFloat(this.form.ppn_nominal) || 0;
                    this.form.total_amount = dpp + ppn;
                    this.generateTerbilang();
                },

                generateTerbilang() {
                    const angka = Math.round(parseFloat(this.form.total_amount) || 0);
                    this.form.terbilang = this.konversiTerbilang(angka);
                },

                konversiTerbilang(nilai) {
                    nilai = Math.abs(nilai);
                    const huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
                    let hasil = "";

                    if (nilai < 12) {
                        hasil = " " + huruf[nilai];
                    } else if (nilai < 20) {
                        hasil = this.konversiTerbilang(nilai - 10) + " Belas";
                    } else if (nilai < 100) {
                        hasil = this.konversiTerbilang(Math.floor(nilai / 10)) + " Puluh" + this.konversiTerbilang(nilai % 10);
                    } else if (nilai < 200) {
                        hasil = " Seratus" + this.konversiTerbilang(nilai - 100);
                    } else if (nilai < 1000) {
                        hasil = this.konversiTerbilang(Math.floor(nilai / 100)) + " Ratus" + this.konversiTerbilang(nilai % 100);
                    } else if (nilai < 2000) {
                        hasil = " Seribu" + this.konversiTerbilang(nilai - 1000);
                    } else if (nilai < 1000000) {
                        hasil = this.konversiTerbilang(Math.floor(nilai / 1000)) + " Ribu" + this.konversiTerbilang(nilai % 1000);
                    } else if (nilai < 1000000000) {
                        hasil = this.konversiTerbilang(Math.floor(nilai / 1000000)) + " Juta" + this.konversiTerbilang(nilai % 1000000);
                    } else if (nilai < 1000000000000) {
                        hasil = this.konversiTerbilang(Math.floor(nilai / 1000000000)) + " Milyar" + this.konversiTerbilang(nilai % 1000000000);
                    } else {
                        hasil = this.konversiTerbilang(Math.floor(nilai / 1000000000000)) + " Triliun" + this.konversiTerbilang(nilai % 1000000000000);
                    }

                    return hasil.replace(/\s+/g, ' ').trim() + " Rupiah";
                }
            }
        }
    </script>
</x-app-layout>
