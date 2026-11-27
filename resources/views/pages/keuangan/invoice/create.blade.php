<x-app-layout>
    <div class="space-y-6" x-data="invoiceForm({
        suggestedNoInvoice: '{{ old('no_invoice', $suggestedNoInvoice) }}',
        defaultTransferNote: `{{ old('catatan_transfer', $defaultTransferNote) }}`,
        initialData: @json($orderData ?? null)
    })">

        <!-- Header Navigasi -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('keuangan.invoice.index') }}" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl font-extrabold text-blue-950 tracking-tight">
                        BUAT INVOICE &amp; KWITANSI IKLAN
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Input No. Faktur Order untuk mengisi data secara otomatis &bull; Seluruh kolom tetap dapat diedit/disesuaikan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" 
                        @click="openModalCari = true" 
                        class="h-10 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center gap-2 transition border border-slate-300 cursor-pointer">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span>📋 Buka Tabel Semua Order Iklan</span>
                </button>
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

        <!-- Formulir Input Dokumen Invoice & Kwitansi -->
        <form action="{{ route('keuangan.invoice.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Hidden Fields untuk Referensi Relasi -->
            <input type="hidden" name="transaksi_koran_id" :value="form.transaksi_koran_id">
            <input type="hidden" name="transaksi_online_id" :value="form.transaksi_online_id">

            <!-- Card 1: Input No Faktur Transaksi & Identitas Dokumen -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-5">
                
                <!-- BAGIAN UTAMA: INPUT NO FAKTUR TRANSAKSI UNTUK AUTOFILL OTOMATIS -->
                <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-blue-50/90 via-sky-50/70 to-indigo-50/80 border-2 border-blue-200 shadow-2xs space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-blue-600 text-white shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-xs font-black text-blue-950 uppercase tracking-wide">
                                    Input No. Faktur Transaksi Iklan (Muncul Otomatis &amp; Bisa Diedit)
                                </h3>
                                <p class="text-[11px] text-slate-500">
                                    Ketik atau pilih nomor faktur order transaksi. Seluruh form di bawah akan langsung terisi otomatis dan tetap <strong>bebas diedit</strong>.
                                </p>
                            </div>
                        </div>

                        <span x-show="selectedOrderLabel" 
                              x-text="selectedOrderLabel" 
                              class="text-[11px] font-bold px-3 py-1 rounded-full bg-blue-600 text-white shadow-xs self-start sm:self-auto">
                        </span>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch gap-2.5 pt-1">
                        <div class="relative flex-1">
                            <input type="text" 
                                   name="no_faktur_iklan" 
                                   x-model="form.no_faktur_iklan" 
                                   @input.debounce.300ms="cariByNoFaktur(form.no_faktur_iklan)" 
                                   @change="cariByNoFaktur(form.no_faktur_iklan)"
                                   @keydown.enter.prevent="cariByNoFaktur(form.no_faktur_iklan)"
                                   list="datalist_no_faktur"
                                   placeholder="Ketik atau pilih No. Faktur (contoh: 0143/10/2026, FKKRN001, FKONL001)..." 
                                   class="w-full text-xs font-mono font-bold rounded-xl border-2 border-blue-300 pl-10 pr-10 py-3 bg-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600 shadow-xs">
                            
                            <!-- Search Icon -->
                            <svg class="w-4 h-4 text-blue-600 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>

                            <!-- Loading Spinner -->
                            <span x-show="isFetchingFaktur" class="absolute right-3.5 top-3 text-xs text-blue-600 animate-spin">
                                ⏳
                            </span>

                            <!-- Datalist Autocomplete No Faktur -->
                            <datalist id="datalist_no_faktur">
                                @if(isset($daftarFakturKoran))
                                    @foreach($daftarFakturKoran as $dfk)
                                        <option value="{{ $dfk->nofakturkoran }}">{{ $dfk->nofakturkoran }} - {{ $dfk->nama_pemasangkoran }} (Rp {{ number_format($dfk->totaltagihan_transaksikoran, 0, ',', '.') }})</option>
                                    @endforeach
                                @endif
                                @if(isset($daftarFakturOnline))
                                    @foreach($daftarFakturOnline as $dfo)
                                        <option value="{{ $dfo->nofakturonline }}">{{ $dfo->nofakturonline }} - {{ $dfo->nama_pemasangonline }} (Rp {{ number_format($dfo->totaltagihan_transaksionline, 0, ',', '.') }})</option>
                                    @endforeach
                                @endif
                            </datalist>
                        </div>

                        <button type="button" 
                                @click="cariByNoFaktur(form.no_faktur_iklan)" 
                                class="px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-black shadow-xs transition shrink-0 cursor-pointer flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>Tarik Data Otomatis</span>
                        </button>
                    </div>

                    <!-- Notifikasi Sukses / Error saat Penarikan Data -->
                    <div x-show="msgSuccess" 
                         x-transition 
                         class="p-3 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-950 text-xs font-bold flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span x-text="msgSuccess"></span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-white/70 px-2 py-0.5 rounded-md">Bisa Diedit</span>
                    </div>

                    <div x-show="msgError" 
                         x-transition 
                         class="p-3 rounded-xl bg-rose-100 border border-rose-300 text-rose-950 text-xs font-bold flex items-center gap-2">
                        <span class="text-rose-600 text-base">⚠️</span>
                        <span x-text="msgError"></span>
                    </div>
                </div>

                <!-- Field-field Identitas Dokumen -->
                <div class="border-t border-slate-100 pt-4">
                    <h2 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span>Identitas Dokumen &amp; Penerima (Kepada / To)</span>
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nomor Invoice Resmi <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="no_invoice" 
                                   x-model="form.no_invoice" 
                                   required 
                                   placeholder="Contoh: 006/KEU-HU.KP/XI/2026" 
                                   class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500 font-mono bg-slate-50/50">
                            <p class="text-[10px] text-slate-400 mt-1">Format: {No Urut}/KEU-HU.KP/{Bulan Romawi}/{Tahun}</p>
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

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-3">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Kepada / To (Nama Instansi / Perusahaan) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="kepada" 
                                   x-model="form.kepada" 
                                   required 
                                   placeholder="Contoh: Prokopim Kota Tasikmalaya" 
                                   class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Attn (Ditujukan Ke)
                            </label>
                            <input type="text" 
                                   name="attn" 
                                   x-model="form.attn" 
                                   placeholder="Contoh: Bagian Keuangan" 
                                   class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Alamat Lengkap Instansi / Pemasang
                            </label>
                            <input type="text" 
                                   name="alamat" 
                                   x-model="form.alamat" 
                                   placeholder="Contoh: Jl. Letnan Harun No.01 Bungursari Kota Tasikmalaya" 
                                   class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

            </div>

            <!-- Card 2: Rincian Materi Iklan & Nominal Tagihan -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">
                            2. Rincian Deskripsi &amp; Nilai Tagihan (Sesuai Format Foto)
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
                              placeholder="Advertorial FC dengan tema :&#10;Kota Tasikmalaya Masuk 20 Kota Indonesia dalam Global Cities Index 2026&#10;Tanggal Muat : 12 November 2026 Ukuran : 7 klm x 240 mm" 
                              class="w-full text-xs rounded-xl border border-slate-300 py-2 px-3 focus:ring-2 focus:ring-blue-500 leading-relaxed font-sans"></textarea>
                    <p class="text-[10px] text-slate-400 mt-1">Baris deskripsi ini akan tercetak persis pada tabel Invoice dan kolom 'Untuk Pembayaran' pada Kwitansi.</p>
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

                <!-- Terbilang Box -->
                <div class="pt-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Terbilang (Sum of)
                    </label>
                    <div class="relative">
                        <input type="text" 
                               name="terbilang" 
                               x-model="form.terbilang" 
                               placeholder="Contoh: Dua Puluh Tiga Juta Rupiah" 
                               class="w-full text-xs font-bold italic rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500 bg-amber-50/30 text-amber-900">
                        <button type="button" 
                                @click="generateTerbilang()" 
                                class="absolute right-2 top-2 px-2.5 py-1 text-[10px] font-bold bg-amber-200 hover:bg-amber-300 text-amber-900 rounded-lg transition cursor-pointer">
                            Update Terbilang
                        </button>
                    </div>
                </div>
            </div>

            <!-- Card 3: Instruksi Transfer & Penandatangan Resmi -->
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
                <a href="{{ route('keuangan.invoice.index') }}" 
                   class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold shadow-md transition cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan &amp; Siapkan Cetak</span>
                </button>
            </div>
        </form>

        <!-- ============================================== -->
        <!-- MODAL CARI & PILIH ORDER TRANSAKSI IKLAN       -->
        <!-- ============================================== -->
        <div x-cloak 
             x-show="openModalCari" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            
            <div class="bg-white rounded-2xl max-w-3xl w-full shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]" 
                 @click.away="openModalCari = false">
                
                <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
                    <div class="flex items-center gap-2.5">
                        <span class="p-2 rounded-xl bg-blue-100 text-blue-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900">Pilih Order Transaksi Iklan</h3>
                            <p class="text-[11px] text-slate-500">Pilih salah satu order iklan untuk mengisi seluruh form secara otomatis</p>
                        </div>
                    </div>
                    <button type="button" @click="openModalCari = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1">
                        ✕
                    </button>
                </div>

                <div class="p-4 border-b border-slate-200 bg-white">
                    <div class="relative">
                        <input type="text" 
                               x-model="searchQuery" 
                               @input.debounce.300ms="cariOrder()" 
                               placeholder="Ketik No Faktur, Nama Pemasang, atau Tema Iklan..." 
                               class="w-full text-xs rounded-xl border border-slate-300 pl-9 pr-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <div class="p-4 overflow-y-auto flex-1 space-y-2">
                    <div x-show="isLoading" class="py-8 text-center text-xs text-slate-500">
                        <span class="inline-block animate-spin mr-2">⏳</span> Sedang mencari data transaksi iklan...
                    </div>

                    <template x-for="item in searchResults" :key="item.type + '_' + item.id">
                        <div @click="pilihOrder(item)" 
                             class="p-3.5 rounded-xl border border-slate-200 hover:border-blue-500 hover:bg-blue-50/40 cursor-pointer transition flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full" 
                                          :class="item.type === 'koran' ? 'bg-sky-100 text-sky-800' : 'bg-emerald-100 text-emerald-800'"
                                          x-text="item.type === 'koran' ? 'Koran' : 'Online'"></span>
                                    <span class="text-xs font-extrabold text-blue-900" x-text="item.no_faktur"></span>
                                    <span class="text-[10px] text-slate-400" x-text="'Order: ' + item.no_order"></span>
                                </div>
                                <div class="text-xs font-bold text-slate-900 mt-1" x-text="item.nama_pemasang"></div>
                                <div class="text-[11px] text-slate-600 line-clamp-1" x-text="item.materi"></div>
                                <div class="text-[10px] text-slate-400 mt-0.5" x-text="'Tgl Muat: ' + item.tanggal_muat + ' • Ukuran: ' + item.ukuran"></div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-xs font-black text-slate-900" x-text="'Rp ' + formatRibuan(item.total_tagihan)"></div>
                                <button type="button" class="mt-1 px-3 py-1 rounded-lg bg-blue-600 text-white text-[10px] font-bold">
                                    Pilih
                                </button>
                            </div>
                        </div>
                    </template>

                    <div x-show="!isLoading && searchResults.length === 0" class="py-8 text-center text-xs text-slate-400">
                        Tidak ada data order iklan yang cocok dengan kata kunci.
                    </div>
                </div>

                <div class="p-3 border-t border-slate-200 bg-slate-50 flex justify-end">
                    <button type="button" @click="openModalCari = false" class="px-4 py-2 rounded-xl bg-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-300">
                        Tutup
                    </button>
                </div>

            </div>
        </div>

    </div>

    <!-- Script Alpine.js Logika Autofill & Kalkulasi -->
    <script>
        function invoiceForm(config) {
            return {
                openModalCari: false,
                searchQuery: '',
                searchResults: [],
                isLoading: false,
                selectedOrderLabel: '',
                isFetchingFaktur: false,
                msgSuccess: '',
                msgError: '',

                form: {
                    no_invoice: config.suggestedNoInvoice,
                    no_faktur_iklan: '',
                    transaksi_koran_id: null,
                    transaksi_online_id: null,
                    kepada: '',
                    alamat: '',
                    attn: 'Bagian Keuangan',
                    tanggal_invoice: new Date().toISOString().split('T')[0],
                    deskripsi: '',
                    qty: 1,
                    unit_price: 0,
                    dpp: 0,
                    ppn_nominal: 0,
                    total_amount: 0,
                    terbilang: '',
                    catatan_transfer: config.defaultTransferNote,
                    kota_terbit: 'Tasikmalaya',
                    nama_penandatangan: 'Eli Yulianti',
                    jabatan_penandatangan: 'Manager Keuangan',
                    status_pembayaran: 'belum_lunas'
                },

                init() {
                    if (config.initialData) {
                        this.applyOrderData(config.initialData);
                    }
                    this.cariOrder();
                },

                async cariByNoFaktur(noFaktur) {
                    const val = (noFaktur || this.form.no_faktur_iklan || '').trim();
                    if (!val) {
                        this.msgError = '';
                        this.msgSuccess = '';
                        return;
                    }

                    this.isFetchingFaktur = true;
                    this.msgSuccess = '';
                    this.msgError = '';

                    try {
                        const res = await fetch(`{{ route('keuangan.invoice.cari-order') }}?no_faktur=${encodeURIComponent(val)}`);
                        const json = await res.json();

                        if (json.success && json.found && json.data) {
                            this.applyOrderData(json.data);
                            this.form.no_faktur_iklan = json.data.no_faktur_iklan || val;
                            this.selectedOrderLabel = `${json.source.toUpperCase()}: ${this.form.no_faktur_iklan} - ${json.data.kepada}`;
                            this.msgSuccess = `✓ Data dari Faktur '${this.form.no_faktur_iklan}' berhasil dimuat otomatis! Seluruh rincian di bawah tetap bebas Anda edit.`;
                            this.msgError = '';
                        } else {
                            this.msgError = json.message || `Order iklan dengan No. Faktur '${val}' tidak ditemukan. Silakan cek kembali atau klik 'Buka Tabel Semua Order Iklan'.`;
                            this.msgSuccess = '';
                        }
                    } catch (e) {
                        console.error('Error saat pencarian No. Faktur:', e);
                        this.msgError = 'Gagal memuat data dari server. Silakan coba lagi.';
                    } finally {
                        this.isFetchingFaktur = false;
                    }
                },

                async cariOrder() {
                    this.isLoading = true;
                    try {
                        const res = await fetch(`{{ route('keuangan.invoice.cari-order') }}?q=${encodeURIComponent(this.searchQuery)}`);
                        const data = await res.json();
                        if (data.success) {
                            this.searchResults = data.data;
                        }
                    } catch (e) {
                        console.error('Gagal mengambil data order iklan', e);
                    } finally {
                        this.isLoading = false;
                    }
                },

                pilihOrder(item) {
                    if (item.formatted_data) {
                        this.applyOrderData(item.formatted_data);
                        this.form.no_faktur_iklan = item.formatted_data.no_faktur_iklan || item.no_faktur;
                        this.selectedOrderLabel = `${item.type.toUpperCase()}: ${item.no_faktur} - ${item.nama_pemasang}`;
                        this.msgSuccess = `✓ Data dari Faktur '${item.no_faktur}' berhasil dimuat otomatis! Anda tetap bisa mengedit kolom di bawah.`;
                        this.msgError = '';
                    }
                    this.openModalCari = false;
                },

                applyOrderData(d) {
                    this.form.no_faktur_iklan = d.no_faktur_iklan || this.form.no_faktur_iklan;
                    this.form.transaksi_koran_id = d.transaksi_koran_id;
                    this.form.transaksi_online_id = d.transaksi_online_id;
                    this.form.kepada = d.kepada;
                    this.form.alamat = d.alamat;
                    this.form.attn = d.attn || 'Bagian Keuangan';
                    this.form.deskripsi = d.deskripsi;
                    this.form.qty = d.qty || 1;
                    this.form.unit_price = d.unit_price || 0;
                    this.form.dpp = d.dpp || 0;
                    this.form.ppn_nominal = d.ppn_nominal || 0;
                    this.form.total_amount = d.total_amount || 0;
                    this.form.terbilang = d.terbilang || '';
                    if (d.tanggal_invoice) {
                        this.form.tanggal_invoice = d.tanggal_invoice;
                    }
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
                },

                formatRibuan(val) {
                    return Number(val || 0).toLocaleString('id-ID');
                }
            }
        }
    </script>
</x-app-layout>
