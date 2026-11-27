<x-app-layout>
    <div class="space-y-6" id="invoice_create_container" x-data="{
        no_faktur_iklan: '{{ old('no_faktur_iklan', $orderData['no_faktur_iklan'] ?? '') }}',
        transaksi_koran_id: '{{ old('transaksi_koran_id', $orderData['transaksi_koran_id'] ?? '') }}',
        transaksi_online_id: '{{ old('transaksi_online_id', $orderData['transaksi_online_id'] ?? '') }}',
        no_invoice: '{{ old('no_invoice', $suggestedNoInvoice) }}',
        tanggal_invoice: '{{ old('tanggal_invoice', $orderData['tanggal_invoice'] ?? date('Y-m-d')) }}',
        status_pembayaran: '{{ old('status_pembayaran', 'belum_lunas') }}',
        kepada: '{{ old('kepada', $orderData['kepada'] ?? '') }}',
        attn: '{{ old('attn', $orderData['attn'] ?? 'Bagian Keuangan') }}',
        alamat: '{{ old('alamat', $orderData['alamat'] ?? '') }}',
        deskripsi: `{{ old('deskripsi', $orderData['deskripsi'] ?? '') }}`,
        qty: {{ old('qty', $orderData['qty'] ?? 1) }},
        unit_price: {{ old('unit_price', $orderData['unit_price'] ?? 0) }},
        dpp: {{ old('dpp', $orderData['dpp'] ?? 0) }},
        ppn_nominal: {{ old('ppn_nominal', $orderData['ppn_nominal'] ?? 0) }},
        total_amount: {{ old('total_amount', $orderData['total_amount'] ?? 0) }},
        terbilang: '{{ old('terbilang', $orderData['terbilang'] ?? '') }}',
        catatan_transfer: `{{ old('catatan_transfer', $defaultTransferNote) }}`,
        kota_terbit: '{{ old('kota_terbit', 'Tasikmalaya') }}',
        nama_penandatangan: '{{ old('nama_penandatangan', 'Eli Yulianti') }}',
        jabatan_penandatangan: '{{ old('jabatan_penandatangan', 'Manager Keuangan') }}',

        // UI states
        openModalCari: false,
        searchQuery: '',
        searchResults: [],
        isLoadingTable: false,
        isFetchingFaktur: false,
        selectedOrderLabel: '{{ $orderData['no_faktur_iklan'] ?? '' }}',
        msgSuccess: '{{ !empty($orderData) ? "✓ Data order berhasil dimuat otomatis." : "" }}',
        msgError: '',

        async cariByNoFaktur(query) {
            const val = (query || this.no_faktur_iklan || '').trim();
            if (!val) {
                this.msgError = 'Ketik atau pilih No. Faktur transaksi terlebih dahulu.';
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
                    this.applyData(json.data);
                    this.selectedOrderLabel = `${json.source.toUpperCase()}: ${json.data.no_faktur_iklan || val} - ${json.data.kepada}`;
                    this.msgSuccess = `✓ Data order '${json.data.no_faktur_iklan || val}' berhasil dimuat otomatis! Seluruh kolom di bawah tetap bebas Anda edit.`;
                    this.msgError = '';
                } else {
                    this.msgError = json.message || `No. Faktur '${val}' tidak ditemukan. Cek kembali atau gunakan tombol 'Pilih dari Tabel Order'.`;
                    this.msgSuccess = '';
                }
            } catch (err) {
                console.error(err);
                this.msgError = 'Gagal memuat data dari server. Silakan coba lagi.';
            } finally {
                this.isFetchingFaktur = false;
            }
        },

        async cariOrderTable() {
            this.isLoadingTable = true;
            try {
                const res = await fetch(`{{ route('keuangan.invoice.cari-order') }}?q=${encodeURIComponent(this.searchQuery)}`);
                const json = await res.json();
                if (json.success) {
                    this.searchResults = json.data;
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.isLoadingTable = false;
            }
        },

        pilihOrderDariModal(item) {
            if (item.formatted_data) {
                this.applyData(item.formatted_data);
                this.no_faktur_iklan = item.formatted_data.no_faktur_iklan || item.no_faktur;
                this.selectedOrderLabel = `${item.type.toUpperCase()}: ${this.no_faktur_iklan} - ${item.nama_pemasang}`;
                this.msgSuccess = `✓ Data order '${this.no_faktur_iklan}' berhasil dimuat otomatis! Anda tetap bisa mengedit kolom di bawah.`;
                this.msgError = '';
            }
            this.openModalCari = false;
        },

        applyData(d) {
            this.no_faktur_iklan = d.no_faktur_iklan || this.no_faktur_iklan;
            this.transaksi_koran_id = d.transaksi_koran_id || '';
            this.transaksi_online_id = d.transaksi_online_id || '';
            this.kepada = d.kepada || '';
            this.alamat = d.alamat || '';
            this.attn = d.attn || 'Bagian Keuangan';
            this.deskripsi = d.deskripsi || '';
            this.qty = d.qty || 1;
            this.unit_price = d.unit_price || 0;
            this.dpp = d.dpp || 0;
            this.ppn_nominal = d.ppn_nominal || 0;
            this.total_amount = d.total_amount || 0;
            this.terbilang = d.terbilang || '';
            if (d.tanggal_invoice) {
                this.tanggal_invoice = d.tanggal_invoice;
            }

            // Fallback direct DOM updates for maximum reliability
            const setVal = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.value = val;
            };
            setVal('input_no_faktur_iklan', this.no_faktur_iklan);
            setVal('input_kepada', this.kepada);
            setVal('input_alamat', this.alamat);
            setVal('input_attn', this.attn);
            setVal('input_deskripsi', this.deskripsi);
            setVal('input_qty', this.qty);
            setVal('input_unit_price', this.unit_price);
            setVal('input_dpp', this.dpp);
            setVal('input_ppn_nominal', this.ppn_nominal);
            setVal('input_total_amount', this.total_amount);
            setVal('input_terbilang', this.terbilang);
            setVal('input_tanggal_invoice', this.tanggal_invoice);
        },

        hitungDariTotal() {
            const total = parseFloat(this.total_amount) || 0;
            const dpp = Math.round(total / 1.11);
            const ppn = Math.round(total - dpp);
            this.dpp = dpp;
            this.ppn_nominal = ppn;
            const q = parseInt(this.qty) || 1;
            this.unit_price = q > 0 ? Math.round(dpp / q) : dpp;
            this.generateTerbilang();
        },

        hitungDariDpp() {
            const dpp = parseFloat(this.dpp) || 0;
            const ppn = Math.round(dpp * 0.11);
            this.ppn_nominal = ppn;
            this.total_amount = dpp + ppn;
            const q = parseInt(this.qty) || 1;
            this.unit_price = q > 0 ? Math.round(dpp / q) : dpp;
            this.generateTerbilang();
        },

        onQtyPriceChange() {
            const q = parseInt(this.qty) || 1;
            const u = parseFloat(this.unit_price) || 0;
            this.dpp = Math.round(q * u);
            this.hitungDariDpp();
        },

        generateTerbilang() {
            const angka = Math.round(parseFloat(this.total_amount) || 0);
            this.terbilang = this.konversiTerbilang(angka);
            const el = document.getElementById('input_terbilang');
            if (el) el.value = this.terbilang;
        },

        konversiTerbilang(nilai) {
            nilai = Math.abs(nilai);
            const huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
            let hasil = '';

            if (nilai < 12) {
                hasil = ' ' + huruf[nilai];
            } else if (nilai < 20) {
                hasil = this.konversiTerbilang(nilai - 10) + ' Belas';
            } else if (nilai < 100) {
                hasil = this.konversiTerbilang(Math.floor(nilai / 10)) + ' Puluh' + this.konversiTerbilang(nilai % 10);
            } else if (nilai < 200) {
                hasil = ' Seratus' + this.konversiTerbilang(nilai - 100);
            } else if (nilai < 1000) {
                hasil = this.konversiTerbilang(Math.floor(nilai / 100)) + ' Ratus' + this.konversiTerbilang(nilai % 100);
            } else if (nilai < 2000) {
                hasil = ' Seribu' + this.konversiTerbilang(nilai - 1000);
            } else if (nilai < 1000000) {
                hasil = this.konversiTerbilang(Math.floor(nilai / 1000)) + ' Ribu' + this.konversiTerbilang(nilai % 1000);
            } else if (nilai < 1000000000) {
                hasil = this.konversiTerbilang(Math.floor(nilai / 1000000)) + ' Juta' + this.konversiTerbilang(nilai % 1000000);
            } else if (nilai < 1000000000000) {
                hasil = this.konversiTerbilang(Math.floor(nilai / 1000000000)) + ' Milyar' + this.konversiTerbilang(nilai % 1000000000);
            } else {
                hasil = this.konversiTerbilang(Math.floor(nilai / 1000000000000)) + ' Triliun' + this.konversiTerbilang(nilai % 1000000000000);
            }

            return hasil.replace(/\s+/g, ' ').trim() + ' Rupiah';
        }
    }">

        <!-- HEADER ATAS (NAVIGASI & AKSI) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('keuangan.invoice.index') }}" 
                   class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition" 
                   title="Kembali ke Daftar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">
                        BUAT INVOICE &amp; KWITANSI IKLAN
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Tarik data otomatis dari order transaksi &bull; Seluruh isian tetap bebas diedit / disesuaikan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" 
                        @click="openModalCari = true; cariOrderTable();" 
                        class="h-9 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center gap-2 transition border border-slate-300 cursor-pointer">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span>Pilih dari Tabel Order</span>
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

        <!-- FORMULIR INPUT -->
        <form action="{{ route('keuangan.invoice.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Hidden Fields ID Relasi -->
            <input type="hidden" name="transaksi_koran_id" :value="transaksi_koran_id">
            <input type="hidden" name="transaksi_online_id" :value="transaksi_online_id">

            <!-- ============================================== -->
            <!-- CARD 0: TARIK DATA ORDER TRANSAKSI IKLAN      -->
            <!-- ============================================== -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wide flex items-center gap-2">
                                <span>Tarik Data dari No. Faktur Transaksi Iklan</span>
                                <span class="text-[10px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-md normal-case">Otomatis &amp; Bisa Diedit</span>
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Pilih atau ketik No. Faktur order untuk mengisi data di bawah otomatis. Seluruh isian tetap dapat diedit bebas.
                            </p>
                        </div>
                    </div>

                    <!-- Badge Status Order Terpilih -->
                    <div x-show="selectedOrderLabel" 
                         x-text="selectedOrderLabel" 
                         class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-blue-50 text-blue-800 border border-blue-200/80 self-start sm:self-auto flex items-center gap-1.5">
                    </div>
                </div>

                <!-- Input Group Pencarian -->
                <div class="flex items-center gap-2.5 pt-1">
                    <div class="relative flex-1 min-w-0">
                        <!-- Icon Search yang SELALU sejajar di tengah vertikal -->
                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3.5 flex items-center">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        <input type="text" 
                               name="no_faktur_iklan" 
                               id="input_no_faktur_iklan"
                               value="{{ old('no_faktur_iklan', $orderData['no_faktur_iklan'] ?? '') }}"
                               x-model="no_faktur_iklan" 
                               @input.debounce.300ms="cariByNoFaktur(no_faktur_iklan)" 
                               @change="cariByNoFaktur(no_faktur_iklan)"
                               @keydown.enter.prevent="cariByNoFaktur(no_faktur_iklan)"
                               list="datalist_no_faktur"
                               placeholder="Ketik / pilih No. Faktur (contoh: FKKRN001, 0143/10/2026, FKONL001)..." 
                               class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 pl-10 pr-10 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs">

                        <!-- Spinner Loading -->
                        <div x-show="isFetchingFaktur" class="absolute inset-y-0 right-0 pr-3.5 flex items-center">
                            <span class="text-xs text-blue-600 animate-spin">⏳</span>
                        </div>

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
                            @click="cariByNoFaktur(no_faktur_iklan)" 
                            class="h-10 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs transition flex items-center justify-center gap-2 shrink-0 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span>Tarik Data Faktur</span>
                    </button>

                    <button type="button" 
                            @click="openModalCari = true; cariOrderTable();" 
                            class="h-10 px-3.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center justify-center gap-1.5 shrink-0 cursor-pointer shadow-2xs">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <span>Pilih Tabel</span>
                    </button>
                </div>

                <!-- Notifikasi Status Berhasil -->
                <div x-show="msgSuccess" 
                     x-transition 
                     class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span x-text="msgSuccess" class="font-medium"></span>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-md shrink-0">Bisa Diedit</span>
                </div>

                <!-- Notifikasi Error -->
                <div x-show="msgError" 
                     x-transition 
                     class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span x-text="msgError" class="font-medium"></span>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- CARD 1: IDENTITAS DOKUMEN & PENERIMA           -->
            <!-- ============================================== -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <h2 class="text-xs sm:text-sm font-extrabold text-slate-800 uppercase tracking-wider">
                            1. Identitas Dokumen &amp; Penerima (Kepada / To)
                        </h2>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-400">Kolom bertanda <span class="text-rose-500">*</span> wajib diisi</span>
                </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Nomor Invoice Resmi <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="no_invoice" 
                                   id="input_no_invoice"
                                   value="{{ old('no_invoice', $suggestedNoInvoice) }}"
                                   x-model="no_invoice" 
                                   required 
                                   placeholder="Contoh: 006/KEU-HU.KP/XI/2026" 
                                   class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500 bg-slate-50/50">
                            <p class="text-[10px] text-slate-400 mt-1">Format: {No Urut}/KEU-HU.KP/{Bulan Romawi}/{Tahun}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Tanggal Invoice <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" 
                                   name="tanggal_invoice" 
                                   id="input_tanggal_invoice"
                                   value="{{ old('tanggal_invoice', date('Y-m-d')) }}"
                                   x-model="tanggal_invoice" 
                                   required 
                                   class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Status Pembayaran <span class="text-rose-500">*</span>
                            </label>
                            <select name="status_pembayaran" 
                                    id="input_status_pembayaran"
                                    x-model="status_pembayaran" 
                                    required 
                                    class="w-full text-xs font-semibold rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500">
                                <option value="belum_lunas">Belum Lunas (Piutang)</option>
                                <option value="lunas">Sudah Lunas</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-3.5">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Kepada / To (Nama Instansi / Perusahaan) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="kepada" 
                                   id="input_kepada"
                                   value="{{ old('kepada', '') }}"
                                   x-model="kepada" 
                                   required 
                                   placeholder="Contoh: Prokopim Kota Tasikmalaya" 
                                   class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Attn (Ditujukan Ke)
                            </label>
                            <input type="text" 
                                   name="attn" 
                                   id="input_attn"
                                   value="{{ old('attn', 'Bagian Keuangan') }}"
                                   x-model="attn" 
                                   placeholder="Contoh: Bagian Keuangan" 
                                   class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Alamat Lengkap Instansi / Pemasang
                            </label>
                            <input type="text" 
                                   name="alamat" 
                                   id="input_alamat"
                                   value="{{ old('alamat', '') }}"
                                   x-model="alamat" 
                                   placeholder="Contoh: Jl. Letnan Harun No.01 Bungursari Kota Tasikmalaya" 
                                   class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

            <!-- ============================================== -->
            <!-- CARD 2: RINCIAN DESKRIPSI & NILAI TAGIHAN      -->
            <!-- ============================================== -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">
                <div class="border-b border-slate-100 pb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <h2 class="text-xs sm:text-sm font-extrabold text-slate-800 uppercase tracking-wider">
                            2. Rincian Deskripsi &amp; Nilai Tagihan (Sesuai Format Foto)
                        </h2>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" 
                                @click="hitungDariTotal()" 
                                class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-amber-50 text-amber-800 hover:bg-amber-100 transition border border-amber-200 cursor-pointer shadow-2xs">
                            ⚡ Hitung DPP &amp; PPN 11% dari Total
                        </button>
                        <button type="button" 
                                @click="hitungDariDpp()" 
                                class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-blue-50 text-blue-800 hover:bg-blue-100 transition border border-blue-200 cursor-pointer shadow-2xs">
                            ⚡ Hitung PPN &amp; Total dari DPP
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Deskripsi / Rincian Iklan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="deskripsi" 
                              id="input_deskripsi"
                              rows="3" 
                              x-model="deskripsi" 
                              required 
                              placeholder="Advertorial FC dengan tema :&#10;Kota Tasikmalaya Masuk 20 Kota Indonesia dalam Global Cities Index 2026&#10;Tanggal Muat : 12 November 2026 Ukuran : 7 klm x 240 mm" 
                              class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500 leading-relaxed font-sans">{{ old('deskripsi', '') }}</textarea>
                    <p class="text-[10px] text-slate-400 mt-1">Tercetak persis pada tabel lembar Invoice dan kolom 'Untuk Pembayaran' pada Kwitansi.</p>
                </div>

                <!-- 5 KOLOM ANGKA DENGAN DESAIN BERSIH -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 pt-2">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Qty</label>
                        <input type="number" 
                               name="qty" 
                               id="input_qty"
                               min="1" 
                               value="{{ old('qty', 1) }}"
                               x-model.number="qty" 
                               @input="onQtyPriceChange()" 
                               required 
                               class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Unit Price (DPP) Rp</label>
                        <input type="number" 
                               step="0.01" 
                               name="unit_price" 
                               id="input_unit_price"
                               value="{{ old('unit_price', 0) }}"
                               x-model.number="unit_price" 
                               @input="onQtyPriceChange()" 
                               required 
                               class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Total DPP (Rp)</label>
                        <input type="number" 
                               step="0.01" 
                               name="dpp" 
                               id="input_dpp"
                               value="{{ old('dpp', 0) }}"
                               x-model.number="dpp" 
                               @input="hitungDariDpp()" 
                               required 
                               class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500 bg-slate-50/70">
                        <input type="hidden" name="subtotal" :value="dpp">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">PPN 11% (Rp)</label>
                        <input type="number" 
                               step="0.01" 
                               name="ppn_nominal" 
                               id="input_ppn_nominal"
                               value="{{ old('ppn_nominal', 0) }}"
                               x-model.number="ppn_nominal" 
                               required 
                               class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2.5 px-3 focus:ring-2 focus:ring-blue-500 bg-slate-50/70">
                        <input type="hidden" name="ppn_persen" value="11.00">
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-[11px] font-bold text-blue-950 mb-1">Grand Total (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" 
                               step="0.01" 
                               name="total_amount" 
                               id="input_total_amount"
                               value="{{ old('total_amount', 0) }}"
                               x-model.number="total_amount" 
                               @input="hitungDariTotal()" 
                               required 
                               class="w-full text-sm font-black text-blue-900 rounded-xl border-2 border-blue-500 py-2 px-3 focus:ring-2 focus:ring-blue-500 bg-blue-50/50 shadow-2xs">
                    </div>
                </div>

                <!-- Terbilang Box -->
                <div class="pt-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Terbilang (Sum of)
                    </label>
                    <div class="relative">
                        <input type="text" 
                               name="terbilang" 
                               id="input_terbilang"
                               value="{{ old('terbilang', '') }}"
                               x-model="terbilang" 
                               placeholder="Contoh: Dua Puluh Tiga Juta Rupiah" 
                               class="w-full text-xs font-bold italic rounded-xl border border-slate-300 py-2.5 pl-3 pr-32 focus:ring-2 focus:ring-blue-500 bg-amber-50/30 text-amber-950">
                        <button type="button" 
                                @click="generateTerbilang()" 
                                class="absolute right-2 top-2 px-3 py-1 text-[11px] font-bold bg-amber-200 hover:bg-amber-300 text-amber-900 rounded-lg transition cursor-pointer">
                            Update Terbilang
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- CARD 3: INSTRUKSI TRANSFER & PENANDATANGAN     -->
            <!-- ============================================== -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <h2 class="text-xs sm:text-sm font-extrabold text-slate-800 uppercase tracking-wider">
                            3. Info Rekening Bank &amp; Penandatangan
                        </h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Instruksi Pembayaran Transfer Bank (Note Invoice)
                        </label>
                        <textarea name="catatan_transfer" 
                                  id="input_catatan_transfer"
                                  rows="6" 
                                  x-model="catatan_transfer" 
                                  class="w-full text-xs rounded-xl border border-slate-300 py-2 px-3 focus:ring-2 focus:ring-blue-500 font-mono leading-relaxed">{{ old('catatan_transfer', $defaultTransferNote) }}</textarea>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kota Penerbitan</label>
                            <input type="text" 
                                   name="kota_terbit" 
                                   id="input_kota_terbit"
                                   value="{{ old('kota_terbit', 'Tasikmalaya') }}"
                                   x-model="kota_terbit" 
                                   required 
                                   class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2 px-3 focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Penandatangan</label>
                            <input type="text" 
                                   name="nama_penandatangan" 
                                   id="input_nama_penandatangan"
                                   value="{{ old('nama_penandatangan', 'Eli Yulianti') }}"
                                   x-model="nama_penandatangan" 
                                   required 
                                   class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2 px-3 focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan Penandatangan</label>
                            <input type="text" 
                                   name="jabatan_penandatangan" 
                                   id="input_jabatan_penandatangan"
                                   value="{{ old('jabatan_penandatangan', 'Manager Keuangan') }}"
                                   x-model="jabatan_penandatangan" 
                                   required 
                                   class="w-full text-xs font-bold rounded-xl border border-slate-300 py-2 px-3 focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOMBOL SIMPAN -->
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
        <!-- MODAL PILIH DARI TABEL ORDER IKLAN             -->
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900">Pilih Order Transaksi Iklan</h3>
                            <p class="text-[11px] text-slate-500">Klik salah satu baris order untuk mengisi seluruh kolom formulir</p>
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
                               @input.debounce.300ms="cariOrderTable()" 
                               placeholder="Ketik No Faktur, Nama Pemasang, atau Tema Iklan..." 
                               class="w-full text-xs rounded-xl border border-slate-300 pl-9 pr-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <div class="p-4 overflow-y-auto flex-1 space-y-2">
                    <div x-show="isLoadingTable" class="py-8 text-center text-xs text-slate-500">
                        <span class="inline-block animate-spin mr-2">⏳</span> Sedang mencari data transaksi iklan...
                    </div>

                    <template x-for="item in searchResults" :key="item.type + '_' + item.id">
                        <div @click="pilihOrderDariModal(item)" 
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
                                <div class="text-xs font-black text-slate-900" x-text="'Rp ' + Number(item.total_tagihan || 0).toLocaleString('id-ID')"></div>
                                <button type="button" class="mt-1 px-3 py-1 rounded-lg bg-blue-600 text-white text-[10px] font-bold shadow-2xs">
                                    Pilih
                                </button>
                            </div>
                        </div>
                    </template>

                    <div x-show="!isLoadingTable && searchResults.length === 0" class="py-8 text-center text-xs text-slate-400">
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
</x-app-layout>
