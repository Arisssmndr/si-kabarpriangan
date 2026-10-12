<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-5">

        <!-- Header Halaman Konsisten (Standar Laporan dengan Logo Kantor) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Tambah Transaksi Priangan TV
                    </h1>
                    <p class="text-xs font-normal text-slate-500 mt-0.5">
                        Formulir input faktur periklanan siaran Priangan TV
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('transaksipriangan.index') }}" 
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

        <form class="space-y-5" method="POST" action="{{ route('transaksipriangan.store') }}">
            @csrf

            {{-- BAGIAN 1: INFO UTAMA --}}
            <div class="bg-white border border-slate-300 rounded-xl p-6 shadow-xs">
                <div class="border-b border-slate-200 pb-3 mb-5">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                        Form Input Transaksi Priangan TV
                    </h2>
                    <p class="text-xs text-slate-500 font-normal mt-0.5">Silakan isi data transaksi dengan lengkap dan benar.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">No Faktur</label>
                            <input type="text" name="nofakturpriangan" value="{{ $nofakturpriangan ?? 'TRX-PRG-001' }}" readonly 
                                   class="w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-sm font-medium text-slate-900 cursor-not-allowed" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Pemasang</label>
                            <input type="text" name="nama_pemasangpriangan" required placeholder="Masukkan nama pemasang" 
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Sales</label>
                            <input type="text" name="sales_iklanpriangan" required placeholder="Masukkan nama sales" 
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Transaksi</label>
                            <input type="date" name="tanggal_transaksipriangan" value="{{ date('Y-m-d') }}" required 
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Alamat Pemasang</label>
                            <textarea name="alamat_pemasangpriangan" rows="4" required placeholder="Masukkan alamat lengkap..." 
                                      class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAGIAN 2: JENIS IKLAN --}}
            <div class="bg-white border border-slate-300 rounded-xl p-6 shadow-xs">
                <div class="border-b border-slate-200 pb-3 mb-5">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                        Spesifikasi Iklan TV
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Jenis Iklan</label>
                        <select name="id_iklanpriangan" id="id_iklanpriangan" required 
                                class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <option value="" disabled {{ old('id_iklanpriangan') == '' ? 'selected' : '' }}>Pilih Jenis Iklan...</option>
                            @foreach($iklanpriangan as $ip)
                                <option value="{{ $ip->id }}" {{ old('id_iklanpriangan') == $ip->id ? 'selected' : '' }}>
                                    {{ $ip->jenis_iklanpriangan ?? $ip->nama_iklanpriangan ?? 'Kolom Salah/Kosong' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Muat</label>
                        <input type="date" name="tanggal_muatiklanpriangan" value="{{ date('Y-m-d') }}" required 
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>
                </div>
            </div>

            {{-- BAGIAN 3: INPUT PEMBAYARAN --}}
            <div class="bg-white border border-slate-300 rounded-xl p-6 shadow-xs">
                <div class="border-b border-slate-200 pb-3 mb-5">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                        Rincian Pembayaran
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Harga (Manual)</label>
                        <input type="text" id="harga_transaksipriangan" name="harga_transaksipriangan" required placeholder="Input Harga..." onkeyup="formatInput(this)" 
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 font-tabular focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Diskon (Rp)</label>
                        <input type="text" id="diskon_transaksipriangan" name="diskon_transaksipriangan" value="0" required onkeyup="formatInput(this)" 
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 font-tabular focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Jumlah Dibayar (DP)</label>
                        <input type="text" id="jumlahbayar_transaksipriangan" name="jumlahbayar_transaksipriangan" value="0" required onkeyup="formatInput(this)" 
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
                        <input type="text" id="dpp_transaksipriangan" name="dpp_transaksipriangan" readonly value="0" 
                               class="w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-sm text-slate-700 font-tabular cursor-not-allowed" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">PPN (11%)</label>
                        <input type="text" id="ppn_transaksipriangan" name="ppn_transaksipriangan" readonly value="0" 
                               class="w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-sm text-slate-700 font-tabular cursor-not-allowed" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Komisi (Rp)</label>
                        <input type="text" id="komisi_transaksipriangan" name="komisi_transaksipriangan" value="0" inputmode="numeric" onkeyup="formatInput(this)" 
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 font-tabular focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Insentif (Rp)</label>
                        <input type="text" id="insentif_transaksipriangan" name="insentif_transaksipriangan" value="0" inputmode="numeric" onkeyup="formatInput(this)" 
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-900 font-tabular focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">TOTAL TAGIHAN</label>
                        <input type="text" id="totaltagihan_transaksipriangan" name="totaltagihan_transaksipriangan" readonly value="0" 
                               class="w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-sm font-bold text-kp-blue-700 font-tabular cursor-not-allowed" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">SISA PIUTANG</label>
                        <input type="text" id="piutang_transaksipriangan" name="piutang_transaksipriangan" readonly value="0" 
                               class="w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-sm font-bold text-red-600 font-tabular cursor-not-allowed" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-200 mt-6">
                    <a href="{{ route('transaksipriangan.index') }}" 
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
            const selectJenis = document.getElementById('id_iklanpriangan');
            const inputHarga = document.getElementById('harga_transaksipriangan');
            const inputDiskon = document.getElementById('diskon_transaksipriangan');
            const inputBayar = document.getElementById('jumlahbayar_transaksipriangan');
            const inputKomisi = document.getElementById('komisi_transaksipriangan');
            const inputInsentif = document.getElementById('insentif_transaksipriangan');

            // OUTPUTS
            const inputDPP = document.getElementById('dpp_transaksipriangan');
            const inputPPN = document.getElementById('ppn_transaksipriangan');
            const inputTotal = document.getElementById('totaltagihan_transaksipriangan');
            const inputPiutang = document.getElementById('piutang_transaksipriangan');

            // Autofill Harga dari Jenis Iklan jika dataset harga ada
            if (selectJenis) {
                selectJenis.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.dataset && selectedOption.dataset.harga) {
                        const hargaDb = parseFloat(selectedOption.dataset.harga) || 0;
                        inputHarga.value = formatRupiah(hargaDb);
                        hitungSemua();
                    }
                });
            }

            // =========================================================
            // PERHITUNGAN TRANSAKSI PRIANGAN TV
            // =========================================================
            window.hitungSemua = function() {
                const harga = cleanNumber(inputHarga.value);
                const diskon = cleanNumber(inputDiskon.value);
                const bayar = cleanNumber(inputBayar.value);

                // Total tagihan = harga - diskon
                let totalTagihan = harga - diskon;
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