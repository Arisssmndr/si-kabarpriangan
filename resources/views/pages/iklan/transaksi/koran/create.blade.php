<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-5">

        <!-- Header Halaman Konsisten (Standar Laporan dengan Logo Kantor) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Tambah Transaksi Iklan Koran
                    </h1>
                    <p class="text-xs font-normal text-slate-500 mt-0.5">
                        Formulir input faktur periklanan cetak Harian Umum Kabar Priangan
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('transaksikoran.index') }}" 
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

        <form class="space-y-5" method="POST" action="{{ route('transaksikoran.store') }}">
            @csrf

            {{-- BAGIAN 1: DATA PEMASANG --}}
            <div class="bg-white border border-slate-300 rounded-xl p-6 shadow-xs">
                <div class="border-b border-slate-200 pb-3 mb-5">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                        Form Input Transaksi Koran
                    </h2>
                    <p class="text-xs text-slate-500 font-normal mt-0.5">Silakan isi data transaksi dengan lengkap dan benar.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">No Faktur</label>
                            <input type="text" name="nofakturkoran" value="{{ $nofakturkoran ?? 'FKKRN001' }}" readonly 
                                   class="w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-sm font-medium text-slate-900 cursor-not-allowed" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Pemasang</label>
                            <input type="text" name="nama_pemasangkoran" value="{{ old('nama_pemasangkoran') }}" required placeholder="Masukkan nama pemasang" 
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Sales</label>
                            <input type="text" name="sales_iklankoran" value="{{ old('sales_iklankoran') }}" required placeholder="Masukkan nama sales" 
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Transaksi</label>
                            <input type="date" name="tanggal_transaksikoran" value="{{ old('tanggal_transaksikoran', date('Y-m-d')) }}" required 
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Alamat Pemasang</label>
                            <textarea name="alamat_pemasangkoran" rows="4" required placeholder="Masukkan alamat lengkap..." 
                                      class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">{{ old('alamat_pemasangkoran') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAGIAN 2: SPESIFIKASI IKLAN KORAN --}}
            <div class="bg-white border border-slate-300 rounded-xl p-6 shadow-xs">
                <div class="border-b border-slate-200 pb-3 mb-5">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                        Spesifikasi Iklan Koran
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Jenis Iklan Koran</label>
                        <select name="id_iklankoran" id="id_iklankoran" required 
                                class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <option value="" disabled {{ old('id_iklankoran') ? '' : 'selected' }}>Pilih Kategori Iklan...</option>
                            @foreach($iklankoran as $ik)
                                <option value="{{ $ik->id }}" {{ old('id_iklankoran') == $ik->id ? 'selected' : '' }}>
                                    {{ $ik->jenis_iklankoran }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Halaman Penempatan</label>
                        <select name="halaman_iklan" required 
                                class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <option value="Halaman Dalam" {{ old('halaman_iklan') == 'Halaman Dalam' ? 'selected' : '' }}>Halaman Dalam</option>
                            <option value="Halaman 1 (Cover)" {{ old('halaman_iklan') == 'Halaman 1 (Cover)' ? 'selected' : '' }}>Halaman 1 (Cover Depan)</option>
                            <option value="Halaman Belakang" {{ old('halaman_iklan') == 'Halaman Belakang' ? 'selected' : '' }}>Halaman Belakang</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Pilihan Warna</label>
                        <select name="warna_iklan" required 
                                class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <option value="Hitam Putih (BW)" {{ old('warna_iklan') == 'Hitam Putih (BW)' ? 'selected' : '' }}>Hitam Putih (BW)</option>
                            <option value="Full Color (FC)" {{ old('warna_iklan') == 'Full Color (FC)' ? 'selected' : '' }}>Full Color (FC)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Ukuran Iklan</label>
                        <input type="text" name="ukuran_iklan" value="{{ old('ukuran_iklan') }}" placeholder="Contoh: 2 x 100 mmk, 4 Baris" 
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Terbit</label>
                        <input type="date" name="tanggal_muatkoran" value="{{ old('tanggal_muatkoran', date('Y-m-d')) }}" required 
                                class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Jumlah Edisi (Qty)</label>
                        <input type="number" id="in_qty" name="total_muatkoran" value="{{ old('total_muatkoran', 1) }}" min="1" required oninput="hitungSemua()" 
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>
                </div>
            </div>

            {{-- BAGIAN 3: RINCIAN PEMBAYARAN --}}
            <div class="bg-white border border-slate-300 rounded-xl p-6 shadow-xs">
                <div class="border-b border-slate-200 pb-3 mb-5">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                        Rincian Pembayaran
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Harga (Manual)</label>
                        <input type="text" id="harga_transaksikoran" name="harga_transaksikoran" value="{{ old('harga_transaksikoran') }}" required placeholder="Input Harga..." onkeyup="formatInput(this)" 
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 font-tabular focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Diskon (Rp)</label>
                        <input type="text" id="diskon_transaksikoran" name="diskon_transaksikoran" value="{{ old('diskon_transaksikoran', '0') }}" required onkeyup="formatInput(this)" 
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 font-tabular focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Jumlah Dibayar (DP)</label>
                        <input type="text" id="jumlahbayar_transaksikoran" name="jumlahbayar_transaksikoran" value="{{ old('jumlahbayar_transaksikoran', '0') }}" required onkeyup="formatInput(this)" 
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 font-tabular focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>
                </div>
            </div>

            {{-- BAGIAN 4: RINCIAN KALKULASI & TOTAL --}}
            <div class="bg-white border border-slate-300 rounded-xl p-6 shadow-xs">
                <div class="border-b border-slate-200 pb-3 mb-5">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                        Rincian Kalkulasi & Total
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">DPP (Nilai / 1.11)</label>
                        <input type="text" id="dpp_transaksikoran" name="dpp_transaksikoran" readonly value="0" 
                               class="w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-sm text-slate-700 font-tabular cursor-not-allowed" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">PPN (11%)</label>
                        <input type="text" id="ppn_transaksikoran" name="ppn_transaksikoran" readonly value="0" 
                               class="w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-sm text-slate-700 font-tabular cursor-not-allowed" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Komisi (Rp)</label>
                        <input type="text" id="komisi_transaksikoran" name="komisi_transaksikoran" value="{{ old('komisi_transaksikoran', '0') }}" inputmode="numeric" onkeyup="formatInput(this)" 
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 font-tabular focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Insentif (Rp)</label>
                        <input type="text" id="insentif_transaksikoran" name="insentif_transaksikoran" value="{{ old('insentif_transaksikoran', '0') }}" inputmode="numeric" onkeyup="formatInput(this)" 
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 font-tabular focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">TOTAL TAGIHAN</label>
                        <input type="text" id="totaltagihan_transaksikoran" name="totaltagihan_transaksikoran" readonly value="0" 
                               class="w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-sm font-bold text-kp-blue-700 font-tabular cursor-not-allowed" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">SISA PIUTANG</label>
                        <input type="text" id="piutang_transaksikoran" name="piutang_transaksikoran" readonly value="0" 
                               class="w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-sm font-bold text-red-600 font-tabular cursor-not-allowed" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-200 mt-6">
                    <a href="{{ route('transaksikoran.index') }}" 
                       class="px-5 py-2.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                        Simpan Transaksi
                    </button>
                </div>
            </div>

        </form>
    </div>

    @push('scripts')
    <script>
        function formatRupiah(angka) {
            if (!angka) return '0';
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        function cleanNumber(rupiah) {
            if (!rupiah) return 0;
            return parseFloat(rupiah.toString().replace(/[^0-9]/g, '')) || 0;
        }

        function formatInput(input) {
            let val = cleanNumber(input.value);
            input.value = formatRupiah(val);
            hitungSemua();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // INPUTS
            const inputQty = document.getElementById('in_qty');
            const inputHarga = document.getElementById('harga_transaksikoran');
            const inputDiskon = document.getElementById('diskon_transaksikoran');
            const inputBayar = document.getElementById('jumlahbayar_transaksikoran');
            const inputKomisi = document.getElementById('komisi_transaksikoran');
            const inputInsentif = document.getElementById('insentif_transaksikoran');

            // OUTPUTS
            const inputDPP = document.getElementById('dpp_transaksikoran');
            const inputPPN = document.getElementById('ppn_transaksikoran');
            const inputTotal = document.getElementById('totaltagihan_transaksikoran');
            const inputPiutang = document.getElementById('piutang_transaksikoran');

            // =========================================================
            // PERHITUNGAN TRANSAKSI IKLAN KORAN
            // =========================================================
            window.hitungSemua = function() {
                const harga = cleanNumber(inputHarga.value);
                const diskon = cleanNumber(inputDiskon.value);
                const bayar = cleanNumber(inputBayar.value);
                const qty = parseFloat(inputQty ? inputQty.value : 1) || 1;

                // Total Omset kotor = harga x qty
                const totalOmset = harga * qty;

                // Total tagihan = total omset - diskon
                let totalTagihan = totalOmset - diskon;
                if (totalTagihan < 0) totalTagihan = 0;

                // DPP dan PPN (Asumsi harga sudah termasuk PPN 11%)
                const dpp = totalTagihan / 1.11;
                const ppn = totalTagihan - dpp;

                // Sisa piutang = total tagihan - pembayaran
                let piutang = totalTagihan - bayar;
                if (piutang < 0) piutang = 0;

                // Tampilkan hasil kalkulasi
                inputDPP.value = formatRupiah(Math.round(dpp));
                inputPPN.value = formatRupiah(Math.round(ppn));
                inputTotal.value = formatRupiah(Math.round(totalTagihan));
                inputPiutang.value = formatRupiah(Math.round(piutang));

                // Warna indikator piutang
                if (piutang > 0) {
                    inputPiutang.className = 'w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-sm font-bold text-red-600 font-tabular cursor-not-allowed';
                } else {
                    inputPiutang.className = 'w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-sm font-bold text-emerald-600 font-tabular cursor-not-allowed';
                }
            };

            // Hitung saat halaman dimuat
            hitungSemua();
        });
    </script>
    @endpush
</x-app-layout>
