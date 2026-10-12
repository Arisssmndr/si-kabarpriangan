<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-5">

        <!-- Header Halaman Konsisten (Standar Laporan dengan Logo Kantor) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Tambah Transaksi Iklan Online
                    </h1>
                    <p class="text-xs font-normal text-slate-500 mt-0.5">
                        Formulir input faktur periklanan portal online Kabar Priangan
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('transaksionline.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    &larr; <span>Kembali</span>
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-xs text-red-800">
                <strong class="font-semibold block mb-1">Terdapat kesalahan input:</strong>
                <ul class="list-disc list-inside space-y-0.5 font-normal">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('transaksionline.store') }}">
            @csrf

            <div class="bg-white border border-slate-300 rounded-xl p-6 sm:p-8 shadow-xs space-y-6">

                <!-- Bagian 1: Data Pemasang -->
                <div class="space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-200">
                        Data Pemasang
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">No Faktur</label>
                            <input type="text" name="nofakturonline" value="{{ $nofakturonline ?? 'FKONL001' }}" readonly 
                                   class="w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-sm font-medium text-slate-900 cursor-not-allowed" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Transaksi</label>
                            <input type="date" name="tanggal_transaksionline" value="{{ date('Y-m-d') }}" required 
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Pemasang</label>
                            <input type="text" name="nama_pemasangonline" required placeholder="Masukkan nama pemasang" 
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Sales / Marketing</label>
                            <input type="text" name="sales_iklanonline" required placeholder="Masukkan nama sales" 
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Alamat Pemasang</label>
                        <textarea name="alamat_pemasangonline" rows="2" required placeholder="Masukkan alamat pemasang..." 
                                  class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition"></textarea>
                    </div>
                </div>

                <!-- Bagian 2: Spesifikasi Iklan Online -->
                <div class="space-y-4 pt-2">
                    <h2 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-200">
                        Spesifikasi Iklan Online
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Jenis Iklan Online</label>
                            <select name="id_iklanonline" required 
                                    class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                <option value="" disabled selected>Pilih Kategori Iklan...</option>
                                @foreach($iklanonline as $io)
                                    <option value="{{ $io->id }}">{{ $io->jenis_iklanonline }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Portal Media Kabar Priangan</label>
                            <select name="portal_iklanonline" required 
                                    class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                <option value="Kabar Tasikmalaya">Kabar Tasikmalaya</option>
                                <option value="Kabar Singaparna">Kabar Singaparna</option>
                                <option value="Kabar Ciamis">Kabar Ciamis</option>
                                <option value="Kabar Banjar">Kabar Banjar</option>
                                <option value="Kabar Pangandaran">Kabar Pangandaran</option>
                                <option value="Kabar Garut">Kabar Garut</option>
                                <option value="Kabar Bandung">Kabar Bandung</option>
                                <option value="Kabar Sumedang">Kabar Sumedang</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Mulai Tayang</label>
                            <input type="date" name="tanggal_muatiklanonline" value="{{ date('Y-m-d') }}" required 
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Jumlah Tayang (Qty)</label>
                            <input type="number" id="in_qty" name="total_muatiklanonline" value="1" min="1" required oninput="hitungSemua()" 
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                        </div>
                    </div>
                </div>

                <!-- Bagian 3: Pembayaran -->
                <div class="space-y-4 pt-2">
                    <h2 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-200">
                        Pembayaran
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Harga Satuan (Rp)</label>
                            <input type="text" id="in_harga" name="harga_transaksionline" required placeholder="0" onkeyup="formatInput(this)" 
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-900 font-tabular focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Potongan Diskon (Rp)</label>
                            <input type="text" id="in_diskon" name="diskon_transaksionline" value="0" onkeyup="formatInput(this)" 
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-900 font-tabular focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Jumlah Dibayar / DP (Rp)</label>
                            <input type="text" id="in_bayar" name="jumlahbayar_transaksionline" value="0" required onkeyup="formatInput(this)" 
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-900 font-tabular focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Total Tagihan (Termasuk PPN 11%)</label>
                            <input type="text" id="lbl_total" readonly value="Rp 0" 
                                   class="w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-sm font-bold text-kp-blue-700 font-tabular cursor-not-allowed" />
                        </div>
                    </div>

                    <div class="p-3.5 bg-slate-50 border border-slate-300 rounded-lg flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-700">Sisa Piutang:</span>
                        <span id="lbl_piutang" class="text-sm font-bold text-slate-900 font-tabular">Rp 0</span>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('transaksionline.index') }}" 
                       class="px-5 py-2.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs font-semibold rounded-lg shadow-sm transition cursor-pointer">
                        Simpan Transaksi
                    </button>
                </div>

            </div>
        </form>
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
            hitungSemua();
        }

        function hitungSemua() {
            const harga = cleanNumber(document.getElementById('in_harga').value);
            const diskon = cleanNumber(document.getElementById('in_diskon').value);
            const bayar = cleanNumber(document.getElementById('in_bayar').value);
            const qty = parseFloat(document.getElementById('in_qty').value) || 1;

            const omset = harga * qty;
            const subtotal = Math.max(0, omset - diskon);
            const ppn = Math.round(subtotal * 0.11);
            const total = subtotal + ppn;
            const piutang = Math.max(0, total - bayar);

            document.getElementById('lbl_total').value = 'Rp ' + formatRupiah(total);
            const elPiutang = document.getElementById('lbl_piutang');
            elPiutang.innerText = 'Rp ' + formatRupiah(piutang);
            if (piutang > 0) {
                elPiutang.className = 'text-sm font-bold text-red-600 font-tabular';
            } else {
                elPiutang.className = 'text-sm font-bold text-emerald-600 font-tabular';
            }
        }
    </script>
    @endpush
</x-app-layout>
