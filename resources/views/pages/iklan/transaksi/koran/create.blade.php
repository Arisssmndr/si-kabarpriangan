<x-app-layout>
    <div class="space-y-6" x-data="{
        // Identitas & Dokumen
        noOrder: '{{ old('no_order', $noOrder) }}',
        noFaktur: '{{ old('nofakturkoran', $nofakturkoran) }}',
        identitas: '{{ old('identitas', $identitas) }}',
        namaPemasang: '{{ old('nama_pemasangkoran', '') }}',
        alamatPemasang: '{{ old('alamat_pemasangkoran', '') }}',
        kota: '{{ old('kota', 'SUMEDANG') }}',
        tglTransaksi: '{{ old('tanggal_transaksikoran', date('Y-m-d')) }}',
        tglMuat: '{{ old('tanggal_muatkoran', date('Y-m-d', strtotime('+1 day'))) }}',
        sales: '{{ old('sales_iklankoran', 'TIA') }}',

        // Spesifikasi Iklan
        jenisIklan: '{{ old('id_iklankoran', $iklankoran->first()->id ?? '') }}',
        jenisIklanNama: 'DISPLAY ADV DINAS 2009',
        kopIklan: '{{ old('kop_iklan', '') }}',
        merk: '{{ old('merk', '') }}',
        materiIklan: '{{ old('materi_iklan', 'APBD PERUBAHAN 2026 SUDAH BERJALAN') }}',
        ukuranIklan: '{{ old('ukuran_iklan', '4 x 200 mm') }}',
        kolom: 4,
        tinggiMm: 200,
        halamanIklan: '{{ old('halaman_iklan', 'Halaman Dalam') }}',
        warnaIklan: '{{ old('warna_iklan', 'Full Color (FC)') }}',
        qtyEdisi: {{ old('total_muatkoran', 1) }},

        // Keuangan & Kalkulasi
        hargaSatuan: {{ old('harga_transaksikoran', 5000000) }},
        diskonPersen: {{ old('diskon_persen', 36.95) }},
        diskonNominal: {{ old('diskon_transaksikoran', 1847500) }},
        kenaPpn: true,
        materai: {{ old('materai_transaksikoran', 0) }},
        jumlahBayar: {{ old('jumlahbayar_transaksikoran', 0) }},
        komisi: {{ old('komisi_transaksikoran', 0) }},
        insentif: {{ old('insentif_transaksikoran', 0) }},

        // Mode input diskon aktif
        diskonMode: 'persen',

        updateUkuranFromGrid() {
            if (this.kolom && this.tinggiMm) {
                this.ukuranIklan = `${this.kolom} x ${this.tinggiMm} mm`;
            }
        },

        get brutto() {
            return (Number(this.hargaSatuan) || 0) * (Number(this.qtyEdisi) || 1);
        },

        onDiskonPersenChange() {
            const b = this.brutto;
            const p = parseFloat(this.diskonPersen) || 0;
            this.diskonNominal = Math.round(b * (p / 100));
        },

        onDiskonNominalChange() {
            const b = this.brutto;
            const n = parseFloat(this.diskonNominal) || 0;
            if (b > 0) {
                this.diskonPersen = Math.round((n / b) * 10000) / 100;
            } else {
                this.diskonPersen = 0;
            }
        },

        get netto() {
            return Math.max(0, this.brutto - (Number(this.diskonNominal) || 0));
        },

        get ppnNominal() {
            if (!this.kenaPpn) return 0;
            return Math.round(this.netto * 0.11);
        },

        get totalYangDibayar() {
            return this.netto + this.ppnNominal + (Number(this.materai) || 0);
        },

        get sisaPiutang() {
            return Math.max(0, this.totalYangDibayar - (Number(this.jumlahBayar) || 0));
        },

        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num || 0);
        },

        formatRupiahBulat(num) {
            return new Intl.NumberFormat('id-ID').format(Math.round(num || 0));
        },

        formatTglIndo(tglStr) {
            if (!tglStr) return '-';
            const parts = tglStr.split('-');
            if (parts.length === 3) {
                return `${parts[2]}-${parts[1]}-${parts[0]}`;
            }
            return tglStr;
        },

        pilihCustomer(el) {
            const opt = el.options[el.selectedIndex];
            if (opt && opt.value) {
                this.namaPemasang = opt.text;
                if (opt.dataset.alamat) this.alamatPemasang = opt.dataset.alamat;
                if (opt.dataset.kota) this.kota = opt.dataset.kota;
                if (opt.dataset.identitas) this.identitas = opt.dataset.identitas;
            }
        },

        updateJenisNama(el) {
            const opt = el.options[el.selectedIndex];
            if (opt) {
                this.jenisIklanNama = opt.text;
            }
        }
    }">

        <!-- Top Header & Breadcrumb -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-10 w-auto object-contain shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-800 border border-blue-200">
                            HU. Priangan
                        </span>
                        <span class="text-xs text-slate-400">/</span>
                        <span class="text-xs font-semibold text-slate-600">Formulir Faktur Asli Continuous Form</span>
                    </div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight mt-0.5">
                        Order Transaksi Iklan HU. Priangan
                    </h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('transaksikoran.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl shadow-xs transition">
                    &larr; <span>Kembali ke Daftar</span>
                </a>
            </div>
        </div>

        @if (isset($errors) && $errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-2xl text-xs text-red-800 space-y-1">
                <strong class="font-bold flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Mohon periksa data input berikut:
                </strong>
                <ul class="list-disc list-inside space-y-0.5 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Layout Grid: 7 Kolom Form Input + 5 Kolom Live Preview Slip Faktur -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- FORMULIR INPUT PENGISIAN (Kiri - 7 Kolom) -->
            <div class="lg:col-span-7 space-y-5">
                <form method="POST" action="{{ route('transaksikoran.store') }}" class="space-y-5" id="formTransaksiKoran">
                    @csrf

                    <!-- CARD 1: IDENTITAS & DATA ORDER (Sesuai Slip Faktur) -->
                    <div class="bg-white border border-slate-300 rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">
                        <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-black">1</span>
                                <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider">
                                    Identitas Pelanggan &amp; Nomor Order
                                </h2>
                            </div>
                            <span class="text-[11px] font-mono text-slate-500 font-bold">Dot Matrix Format</span>
                        </div>

                        <!-- Baris 1: No. Order, No. Faktur & Identitas COA -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    No. Order <span class="text-blue-600 font-mono">*</span>
                                </label>
                                <input type="text" name="no_order" x-model="noOrder" required
                                       class="w-full h-9 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition" />
                                <span class="text-[10px] text-slate-400">Contoh: 2026100143</span>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    No. Faktur <span class="text-blue-600 font-mono">*</span>
                                </label>
                                <input type="text" name="nofakturkoran" x-model="noFaktur" required
                                       class="w-full h-9 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold text-blue-900 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition" />
                                <span class="text-[10px] text-slate-400">Contoh: 0143/10/2026</span>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Identitas Pelanggan <span class="text-blue-600">*</span>
                                </label>
                                <input type="text" name="identitas" x-model="identitas" required
                                       class="w-full h-9 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition" />
                                <span class="text-[10px] text-slate-400">Format COA: 1.00.04.01.01.01.00496</span>
                            </div>
                        </div>

                        <!-- Baris 2: Nama Pemasang + Quick Select Customer -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-[11px] font-bold text-slate-700 uppercase">
                                    Nama Pemasang / Lembaga / Instansi <span class="text-red-500">*</span>
                                </label>
                                @if(isset($existingCustomers) && $existingCustomers->count() > 0)
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] text-slate-500">Pilih dari Master:</span>
                                        <select @change="pilihCustomer($el)" class="text-[10px] py-0.5 px-2 bg-slate-100 border border-slate-300 rounded-lg text-slate-700 focus:outline-none">
                                            <option value="">-- Cari Pelanggan Lama --</option>
                                            @foreach($existingCustomers as $c)
                                                <option value="{{ $c->id }}" data-alamat="{{ $c->alamat }}" data-kota="{{ $c->kota }}" data-identitas="{{ $c->identitas }}">{{ $c->nama_customer }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                            <input type="text" name="nama_pemasangkoran" x-model="namaPemasang" required placeholder="Contoh: DPRD SUMEDANG"
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-900 placeholder:font-normal placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 uppercase transition" />
                        </div>

                        <!-- Baris 3: Alamat & Kota -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Alamat Pemasang <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="alamat_pemasangkoran" x-model="alamatPemasang" required placeholder="Contoh: SUMEDANG"
                                       class="w-full h-9 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 uppercase transition" />
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Kota <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="kota" x-model="kota" required placeholder="Contoh: SUMEDANG"
                                       class="w-full h-9 px-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 uppercase transition" />
                            </div>
                        </div>

                        <!-- Baris 4: Tanggal Transaksi, Tanggal Muat & Front Office (Sales) -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 pt-1">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Tanggal Order / Transaksi <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal_transaksikoran" x-model="tglTransaksi" required
                                       class="w-full h-9 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition" />
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Tanggal Muat (Terbit) <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal_muatkoran" x-model="tglMuat" required
                                       class="w-full h-9 px-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-blue-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition" />
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Front Office / AE (Sales) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="sales_iklankoran" x-model="sales" required placeholder="Contoh: TIA"
                                       class="w-full h-9 px-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 uppercase transition" />
                            </div>
                        </div>
                    </div>

                    <!-- CARD 2: SPESIFIKASI & MATERI IKLAN (Sesuai Slip Faktur) -->
                    <div class="bg-white border border-slate-300 rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">
                        <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs font-black">2</span>
                                <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider">
                                    Spesifikasi, Ukuran &amp; Materi Iklan
                                </h2>
                            </div>
                            <span class="text-[11px] text-slate-500 font-medium">Kop · Merk · Materi</span>
                        </div>

                        <!-- Baris 1: Jenis Iklan & Materi Iklan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Jenis Iklan Koran <span class="text-red-500">*</span>
                                </label>
                                <select name="id_iklankoran" x-model="jenisIklan" @change="updateJenisNama($el)" required
                                        class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition">
                                    @foreach($iklankoran as $ik)
                                        <option value="{{ $ik->id }}" {{ (str_contains(strtoupper($ik->jenis_iklankoran), 'DISPLAY') || $loop->first) ? 'selected' : '' }}>
                                            {{ $ik->jenis_iklankoran }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Materi Iklan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="materi_iklan" x-model="materiIklan" required placeholder="Contoh: APBD PERUBAHAN 2026 SUDAH BERJALAN"
                                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-900 placeholder:font-normal placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 uppercase transition" />
                            </div>
                        </div>

                        <!-- Baris 2: Kop Iklan & Merk (Opsional) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Kop Iklan <span class="text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <input type="text" name="kop_iklan" x-model="kopIklan" placeholder="Bisa dikosongkan jika tidak ada kop"
                                       class="w-full h-9 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 uppercase transition" />
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Merk <span class="text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <input type="text" name="merk" x-model="merk" placeholder="Bisa dikosongkan jika tidak ada merk"
                                       class="w-full h-9 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 uppercase transition" />
                            </div>
                        </div>

                        <!-- Baris 3: Ukuran Iklan + Helper Kolom x mm -->
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <label class="block text-[11px] font-bold text-slate-800 uppercase">
                                    Ukuran Iklan <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] text-slate-500 font-medium">Bantu Hitung:</span>
                                    <div class="inline-flex items-center gap-1.5 bg-white border border-slate-300 px-2 py-1 rounded-lg">
                                        <input type="number" x-model="kolom" @input="updateUkuranFromGrid()" min="1" max="9" class="w-10 text-center text-xs font-bold border-0 p-0 focus:ring-0">
                                        <span class="text-xs text-slate-400 font-bold">kolom ×</span>
                                        <input type="number" x-model="tinggiMm" @input="updateUkuranFromGrid()" min="1" class="w-14 text-center text-xs font-bold border-0 p-0 focus:ring-0">
                                        <span class="text-xs text-slate-400 font-bold">mm</span>
                                    </div>
                                </div>
                            </div>
                            <input type="text" name="ukuran_iklan" x-model="ukuranIklan" required placeholder="Contoh: 4 x 200 mm"
                                   class="w-full h-9 px-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition" />
                        </div>

                        <!-- Baris 4: Halaman, Warna, Edisi (Qty) -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Penempatan Halaman
                                </label>
                                <select name="halaman_iklan" x-model="halamanIklan"
                                        class="w-full h-9 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition">
                                    <option value="Halaman Dalam">Halaman Dalam</option>
                                    <option value="Halaman 1 (Cover Depan)">Halaman 1 (Cover Depan)</option>
                                    <option value="Halaman Belakang">Halaman Belakang</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Warna Cetak
                                </label>
                                <select name="warna_iklan" x-model="warnaIklan"
                                        class="w-full h-9 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition">
                                    <option value="Full Color (FC)">Full Color (FC)</option>
                                    <option value="Hitam Putih (BW)">Hitam Putih (BW)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Jumlah Edisi (Qty) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="total_muatkoran" x-model="qtyEdisi" min="1" required
                                       @input="onDiskonPersenChange()"
                                       class="w-full h-9 px-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition" />
                            </div>
                        </div>
                    </div>

                    <!-- CARD 3: RINCIAN KALKULASI FINANSIAL PERSIS SLIP FAKTUR -->
                    <div class="bg-white border-2 border-blue-500/80 rounded-2xl p-5 sm:p-6 shadow-md space-y-4">
                        <div class="border-b border-blue-100 pb-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-black">3</span>
                                <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider">
                                    Rincian Perhitungan &amp; Pembayaran (Slip Faktur)
                                </h2>
                            </div>
                            <span class="text-[11px] font-black text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-full">
                                Rumus Faktur Fisik
                            </span>
                        </div>

                        <!-- Baris 1: Harga Satuan Rp. & Nilai Brutto -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Harga Tarif Iklan (Rp) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-slate-400">Rp</span>
                                    <input type="number" name="harga_transaksikoran" x-model="hargaSatuan" required
                                           @input="onDiskonPersenChange()"
                                           class="w-full h-10 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-sm font-black text-slate-900 font-mono focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition" />
                                </div>
                                <span class="text-[10px] text-slate-400">Contoh: 5000000</span>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Nilai Brutto (Rp) <span class="text-slate-400 font-normal">[Harga × Qty]</span>
                                </label>
                                <div class="h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-xl flex items-center justify-between font-mono font-bold text-slate-800 text-sm">
                                    <span class="text-xs text-slate-500 font-sans font-normal">Rp</span>
                                    <span x-text="formatRupiah(brutto)"></span>
                                </div>
                                <span class="text-[10px] text-slate-400">Brutto kotor sebelum potongan diskon</span>
                            </div>
                        </div>

                        <!-- Baris 2: Diskon % dan Diskon Rp. (Two-way Live Sync) -->
                        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-[11px] font-black text-slate-800 uppercase tracking-wide">
                                    Potongan Diskon
                                </label>
                                <span class="text-[10px] text-slate-500 font-medium">Bisa isi % atau nominal Rp (Saling sinkron)</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[10px] font-bold text-slate-600 uppercase">Diskon (%)</span>
                                    </div>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="diskon_persen" x-model="diskonPersen"
                                               @input="onDiskonPersenChange()"
                                               placeholder="36.95"
                                               class="w-full h-9 pl-3 pr-8 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition" />
                                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-slate-400">%</span>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[10px] font-bold text-slate-600 uppercase">Diskon (Rp)</span>
                                    </div>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-slate-400">Rp</span>
                                        <input type="number" name="diskon_transaksikoran" x-model="diskonNominal"
                                               @input="onDiskonNominalChange()"
                                               placeholder="1847500"
                                               class="w-full h-9 pl-9 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Baris 3: Netto, PPn 11%, Materai -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Netto (Rp) <span class="text-slate-400 font-normal">[Brutto - Diskon]</span>
                                </label>
                                <div class="h-9 px-3 bg-slate-100 border border-slate-300 rounded-xl flex items-center justify-between font-mono font-bold text-slate-900 text-xs">
                                    <span class="text-[10px] text-slate-500 font-sans">Rp</span>
                                    <span x-text="formatRupiah(netto)"></span>
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-[11px] font-bold text-slate-700 uppercase">
                                        PPn 11% (Rp)
                                    </label>
                                    <label class="inline-flex items-center gap-1 text-[10px] text-blue-700 font-bold cursor-pointer">
                                        <input type="checkbox" name="kena_ppn" value="1" x-model="kenaPpn" class="rounded text-blue-600 focus:ring-blue-500 w-3.5 h-3.5">
                                        <span>Aktifkan</span>
                                    </label>
                                </div>
                                <div class="h-9 px-3 bg-slate-100 border border-slate-300 rounded-xl flex items-center justify-between font-mono font-bold text-slate-900 text-xs">
                                    <span class="text-[10px] text-slate-500 font-sans">Rp</span>
                                    <span x-text="formatRupiah(ppnNominal)"></span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Materai (Rp)
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-xs font-bold text-slate-400">Rp</span>
                                    <input type="number" name="materai_transaksikoran" x-model="materai" placeholder="0"
                                           class="w-full h-9 pl-8 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition" />
                                </div>
                            </div>
                        </div>

                        <!-- Baris 4: TOTAL JUMLAH YANG DIBAYAR (Highlight Besar) -->
                        <div class="p-4 rounded-xl bg-blue-50/80 border border-blue-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-blue-700">
                                    Jumlah Yang Dibayar (Grand Total)
                                </span>
                                <p class="text-xs text-slate-600 mt-0.5">Rumus: Netto + PPn 11% + Materai</p>
                            </div>
                            <div class="text-right">
                                <span class="text-lg sm:text-xl font-black font-mono text-blue-900">
                                    Rp <span x-text="formatRupiah(totalYangDibayar)"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Baris 5: Pembayaran Saat Ini & Sisa Piutang Iklan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Jumlah Dibayar Saat Ini (DP / Tunai)
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-slate-400">Rp</span>
                                    <input type="number" name="jumlahbayar_transaksikoran" x-model="jumlahBayar" placeholder="0"
                                           class="w-full h-10 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-900 font-mono focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition" />
                                </div>
                                <span class="text-[10px] text-slate-500">Biarkan 0 jika belum ada pembayaran (Faktur Terbit sebagai Piutang)</span>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                    Sisa Piutang Iklan (Rp)
                                </label>
                                <div class="h-10 px-3.5 rounded-xl border flex items-center justify-between font-mono font-black text-sm"
                                     :class="sisaPiutang > 0 ? 'bg-red-50 border-red-200 text-red-600' : 'bg-emerald-50 border-emerald-200 text-emerald-700'">
                                    <span class="text-xs font-sans font-normal">Rp</span>
                                    <span x-text="formatRupiah(sisaPiutang)"></span>
                                </div>
                                <div class="mt-1 flex items-center justify-between">
                                    <span class="text-[10px] text-slate-400">Status Pembukuan:</span>
                                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded"
                                          :class="sisaPiutang > 0 ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-800'"
                                          x-text="sisaPiutang > 0 ? '● PIUTANG IKLAN' : '● LUNAS'">
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Baris 6: Komisi & Insentif Internal AE (Accordion Mini) -->
                        <div x-data="{ openInternal: false }" class="border border-slate-200 rounded-xl overflow-hidden">
                            <button type="button" @click="openInternal = !openInternal" class="w-full px-3.5 py-2 bg-slate-50 hover:bg-slate-100 text-left flex items-center justify-between text-xs text-slate-600 font-bold transition">
                                <span>⚙️ Pengaturan Komisi &amp; Insentif AE (Internal Marketing)</span>
                                <span x-text="openInternal ? '▲ Tutup' : '▼ Lihat / Sesuaikan'"></span>
                            </button>
                            <div x-show="openInternal" class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3 bg-white">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Komisi AE (Rp)</label>
                                    <input type="number" name="komisi_transaksikoran" x-model="komisi" class="w-full h-8 px-2.5 border border-slate-300 rounded-lg text-xs font-mono">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Insentif AE (Rp)</label>
                                    <input type="number" name="insentif_transaksikoran" x-model="insentif" class="w-full h-8 px-2.5 border border-slate-300 rounded-lg text-xs font-mono">
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- TOMBOL AKSI SIMPAN -->
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('transaksikoran.index') }}" 
                           class="px-5 py-2.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-bold rounded-xl transition">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-7 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-xs font-extrabold rounded-xl shadow-md transition flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Order Transaksi Iklan</span>
                        </button>
                    </div>

                </form>
            </div>

            <!-- LIVE PREVIEW SLIP DOKUMEN FAKTUR CONTINUOUS FORM (Kanan - 5 Kolom) -->
            <div class="lg:col-span-5 sticky top-6 space-y-3">
                <div class="flex items-center justify-between px-1">
                    <span class="text-xs font-extrabold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live Preview Lembar Faktur Fisik
                    </span>
                    <span class="text-[10px] text-slate-400 font-mono">1:1 Dot Matrix Slip</span>
                </div>

                <!-- Kontainer Continuous Form Dot Matrix Sesuai Foto Asli -->
                <div class="bg-white rounded-xl shadow-lg border border-slate-300 overflow-hidden text-[11px] leading-relaxed font-mono flex select-none">
                    
                    <!-- Lubang Kertas Kiri (Tractor Feed Holes) -->
                    <div class="w-6 bg-slate-100 border-r border-dashed border-slate-300 py-4 flex flex-col items-center justify-between gap-3 shrink-0">
                        @for ($i = 0; $i < 14; $i++)
                            <div class="w-2 h-2 rounded-full bg-slate-300 border border-slate-400"></div>
                        @endfor
                    </div>

                    <!-- Isi Lembar Faktur Dot-Matrix -->
                    <div class="flex-1 p-4 sm:p-5 text-black space-y-2">
                        
                        <!-- Header Kop HU. Priangan -->
                        <div class="border-b-2 border-dashed border-black pb-2 text-left">
                            <h3 class="text-xs sm:text-[13px] font-black tracking-wide uppercase">
                                ORDER TRANSAKSI IKLAN HU. PRIANGAN
                            </h3>
                            <p class="text-[9px] font-bold text-neutral-800 leading-tight">
                                JL.DINDINGARI RAYA NO.12 TLP. (0265)-335300, FAX 335677 PERUM BUMI RESIK PANGLAYUNGAN TASIKMALAYA 46134
                            </p>
                        </div>

                        <!-- Baris Identitas Pelanggan & No Order Faktur (2 Kolom) -->
                        <div class="grid grid-cols-12 gap-1 text-[10px] border-b-2 border-dashed border-black pb-2">
                            <div class="col-span-7 space-y-0.5">
                                <div class="flex">
                                    <span class="w-20 shrink-0 font-bold">Identitas</span>
                                    <span class="mr-1">:</span>
                                    <span class="font-bold truncate" x-text="identitas || '-'"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-20 shrink-0 font-bold">Nama</span>
                                    <span class="mr-1">:</span>
                                    <span class="font-black uppercase truncate" x-text="namaPemasang || 'DPRD SUMEDANG'"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-20 shrink-0 font-bold">Alamat</span>
                                    <span class="mr-1">:</span>
                                    <span class="uppercase truncate" x-text="alamatPemasang || 'SUMEDANG'"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-20 shrink-0 font-bold">Kota</span>
                                    <span class="mr-1">:</span>
                                    <span class="font-bold uppercase truncate" x-text="kota || 'SUMEDANG'"></span>
                                </div>
                            </div>

                            <div class="col-span-5 space-y-0.5 pl-1 border-l border-dotted border-slate-300">
                                <div class="flex">
                                    <span class="w-18 shrink-0 font-bold">No. Order</span>
                                    <span class="mr-1">:</span>
                                    <span class="font-black" x-text="noOrder || '-'"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-18 shrink-0 font-bold">No. Faktur</span>
                                    <span class="mr-1">:</span>
                                    <span class="font-black text-blue-900" x-text="noFaktur || '-'"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-18 shrink-0 font-bold">Jenis Iklan</span>
                                    <span class="mr-1">:</span>
                                    <span class="font-bold uppercase truncate" x-text="jenisIklanNama || 'DISPLAY ADV DINAS 2009'"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-18 shrink-0 font-bold">Tanggal Muat</span>
                                    <span class="mr-1">:</span>
                                    <span class="font-bold" x-text="formatTglIndo(tglMuat)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Baris Kop, Merk & Materi Iklan -->
                        <div class="text-[10px] space-y-0.5 border-b-2 border-dashed border-black pb-2">
                            <div class="grid grid-cols-12 gap-1">
                                <div class="col-span-6 flex">
                                    <span class="w-18 shrink-0 font-bold">Kop Iklan</span>
                                    <span class="mr-1">:</span>
                                    <span class="truncate" x-text="kopIklan || '-'"></span>
                                </div>
                                <div class="col-span-6 flex">
                                    <span class="w-12 shrink-0 font-bold">Merk</span>
                                    <span class="mr-1">:</span>
                                    <span class="truncate" x-text="merk || '-'"></span>
                                </div>
                            </div>
                            <div class="flex">
                                <span class="w-24 shrink-0 font-bold">Materi Iklan</span>
                                <span class="mr-1">:</span>
                                <span class="font-black uppercase text-slate-900 leading-tight" x-text="materiIklan || 'APBD PERUBAHAN 2026 SUDAH BERJALAN'"></span>
                            </div>
                        </div>

                        <!-- Rincian Biaya Finansial Persis Kertas Faktur -->
                        <div class="text-[10px] space-y-1 pt-1">
                            <div class="flex justify-between items-center">
                                <span>Ukuran : <strong x-text="ukuranIklan"></strong></span>
                                <span>Harga Rp. <strong x-text="formatRupiah(hargaSatuan)"></strong></span>
                            </div>
                            <div class="flex justify-between items-center font-bold">
                                <span>Brutto</span>
                                <span>Rp. <span x-text="formatRupiah(brutto)"></span></span>
                            </div>
                            <div class="flex justify-between items-center text-slate-800">
                                <span>Diskon <span x-text="diskonPersen > 0 ? (diskonPersen.toString().replace('.', ',') + ' %') : '0,00 %'"></span></span>
                                <span>Rp. <span x-text="formatRupiah(diskonNominal)"></span></span>
                            </div>
                            <div class="flex justify-between items-center font-black">
                                <span>Netto</span>
                                <span>Rp. <span x-text="formatRupiah(netto)"></span></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>PPn 11 %</span>
                                <span>Rp. <span x-text="formatRupiah(ppnNominal)"></span></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Materai</span>
                                <span>Rp. <span x-text="formatRupiah(materai)"></span></span>
                            </div>
                            <div class="border-t-2 border-dashed border-black pt-1.5 flex justify-between items-center text-[11px] font-black">
                                <span class="uppercase">Jumlah Yang Dibayar</span>
                                <span>Rp. <span x-text="formatRupiah(totalYangDibayar)"></span></span>
                            </div>
                        </div>

                        <!-- Area Tanda Tangan & Cap Piutang Iklan Sesuai Foto 2 -->
                        <div class="pt-3 border-t-2 border-dashed border-black grid grid-cols-2 gap-2 items-center text-[10px]">
                            <!-- Stempel Cap Piutang Iklan -->
                            <div class="text-center">
                                <template x-if="sisaPiutang > 0">
                                    <div class="inline-block border-2 border-cyan-800 text-cyan-900 px-3 py-1.5 rounded-full font-black text-center rotate-[-4deg]">
                                        <div class="text-[8px] uppercase tracking-wider font-extrabold">PIUTANG IKLAN</div>
                                        <div class="text-[10px] tracking-tight border-t border-b border-cyan-800 my-0.5 py-0.5">Kabar Priangan</div>
                                        <div class="text-[7px]">FRONT OFFICE</div>
                                    </div>
                                </template>
                                <template x-if="sisaPiutang <= 0">
                                    <div class="inline-block border-2 border-emerald-700 text-emerald-800 px-3 py-1.5 rounded-full font-black text-center rotate-[-4deg]">
                                        <div class="text-[8px] uppercase tracking-wider font-extrabold">KABAR PRIANGAN</div>
                                        <div class="text-[10px] tracking-tight border-t border-b border-emerald-700 my-0.5 py-0.5">L U N A S</div>
                                        <div class="text-[7px]">DIVISI KEUANGAN</div>
                                    </div>
                                </template>
                            </div>

                            <!-- Front Office & Tgl -->
                            <div class="text-right text-[9px] space-y-1 leading-tight">
                                <p>Tasikmalaya, <span x-text="formatTglIndo(tglTransaksi)"></span></p>
                                <p>Front Office,</p>
                                <div class="h-6"></div>
                                <p class="font-bold underline uppercase" x-text="sales || 'TIA'"></p>
                                <p class="pt-1">Pemasang,</p>
                                <div class="h-5"></div>
                                <p class="font-bold underline uppercase" x-text="namaPemasang || '( .................... )'"></p>
                            </div>
                        </div>

                    </div>

                    <!-- Lubang Kertas Kanan (Tractor Feed Holes) -->
                    <div class="w-6 bg-slate-100 border-l border-dashed border-slate-300 py-4 flex flex-col items-center justify-between gap-3 shrink-0">
                        @for ($i = 0; $i < 14; $i++)
                            <div class="w-2 h-2 rounded-full bg-slate-300 border border-slate-400"></div>
                        @endfor
                    </div>

                </div>

                <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-center text-xs font-semibold text-blue-800">
                    💡 Lembar ini otomatis dapat dicetak ke printer dot-matrix atau PDF setelah disimpan.
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
