<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Pengaturan Tarif & Operasional Iklan
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Konfigurasi parameter periklanan pers, deadline redaksi, PPN, dan tarif dasar tanpa edit kode program
                    </p>
                </div>
            </div>
            <a href="{{ route('transaksikoran.index') }}" 
               class="px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                &larr; Ke Transaksi Koran
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('iklan.pengaturan.update') }}" class="space-y-6">
            @csrf

            <!-- Section 1: Regulasi Pajak & Batas Waktu Cetak -->
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-5">
                <div class="border-b border-slate-200 pb-3">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                        1. Regulasi Pajak (PPN & Bea Meterai) & Advertising Deadline
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Standar operasional periklanan Harian Umum Kabar Priangan sesuai UU yang berlaku</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tarif PPN Resmi (%)
                        </label>
                        <div class="relative">
                            <input type="number" step="0.1" name="ppn_persen" value="{{ $configs['ppn_persen']->value ?? '12' }}" required
                                   class="w-full h-10 px-3.5 pr-10 bg-white border border-slate-300 rounded-lg text-sm font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <span class="absolute right-3.5 top-2.5 text-xs font-bold text-slate-400">%</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Berlaku resmi 12% (UU Harmonisasi Peraturan Perpajakan / UU HPP).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Jam Cut-off Iklan Harian (WIB)
                        </label>
                        <input type="time" name="jam_cutoff_iklan" value="{{ $configs['jam_cutoff_iklan']->value ?? '14:00' }}" required
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <p class="text-[11px] text-slate-400 mt-1">Batas order masuk FO agar bisa terbit di edisi cetak esok hari. Lewat jam ini otomatis diarahkan ke lusa.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Ambang Batas Wajib Bea Meterai (Rp)
                        </label>
                        <input type="text" name="materai_threshold" value="{{ number_format($configs['materai_threshold']->value ?? 5000000, 0, ',', '.') }}" onkeyup="formatRupiahInput(this)"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <p class="text-[11px] text-slate-400 mt-1">Sesuai UU Bea Meterai No. 10/2020: Transaksi ≥ Rp 5.000.000 dikenakan meterai.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nominal Bea Meterai (Rp)
                        </label>
                        <input type="text" name="materai_nominal" value="{{ number_format($configs['materai_nominal']->value ?? 10000, 0, ',', '.') }}" onkeyup="formatRupiahInput(this)"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <p class="text-[11px] text-slate-400 mt-1">Meterai tempel / meterai elektronik Rp 10.000.</p>
                    </div>
                </div>
            </div>

            <!-- Section 2: Tarif Iklan Koran -->
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-5">
                <div class="border-b border-slate-200 pb-3">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                        2. Standar Tarif Iklan Cetak Koran (Baris & Kolom / Display)
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Rumus dasar perhitungan otomatis pada formulir input Front Office</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tarif Iklan Baris per Baris (Rp)
                        </label>
                        <input type="text" name="tarif_per_baris" value="{{ number_format($configs['tarif_per_baris']->value ?? 15000, 0, ',', '.') }}" onkeyup="formatRupiahInput(this)"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <p class="text-[11px] text-slate-400 mt-1">Tarif per baris naskah per 1 kali muat.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Minimal Baris Pemuatan
                        </label>
                        <input type="number" min="1" name="minimal_baris" value="{{ $configs['minimal_baris']->value ?? '3' }}"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <p class="text-[11px] text-slate-400 mt-1">Standar koran harian: minimal 3 baris per pemuatan.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tarif Kolom / Display Hitam Putih (BW) per mmk (Rp)
                        </label>
                        <input type="text" name="tarif_mmk_bw" value="{{ number_format($configs['tarif_mmk_bw']->value ?? 25000, 0, ',', '.') }}" onkeyup="formatRupiahInput(this)"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <p class="text-[11px] text-slate-400 mt-1">Tarif milimeter kolom (mmk) untuk iklan cetak BW.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tarif Kolom / Display Berwarna (FC) per mmk (Rp)
                        </label>
                        <input type="text" name="tarif_mmk_fc" value="{{ number_format($configs['tarif_mmk_fc']->value ?? 45000, 0, ',', '.') }}" onkeyup="formatRupiahInput(this)"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <p class="text-[11px] text-slate-400 mt-1">Tarif milimeter kolom (mmk) untuk iklan Full Color.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kapasitas Karakter per Baris Cetak
                        </label>
                        <input type="number" min="10" name="maksimal_karakter_per_baris" value="{{ $configs['maksimal_karakter_per_baris']->value ?? '32' }}"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <p class="text-[11px] text-slate-400 mt-1">Maksimal karakter (termasuk spasi) per 1 baris kolom koran (rata-rata 32 karakter).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Diskon Agensi / Biro Iklan Default (%)
                        </label>
                        <div class="relative">
                            <input type="number" min="0" max="100" name="diskon_agensi_default" value="{{ $configs['diskon_agensi_default']->value ?? '15' }}"
                                   class="w-full h-10 px-3.5 pr-10 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <span class="absolute right-3.5 top-2.5 text-xs font-bold text-slate-400">%</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Komisi potongan baku bagi agensi periklanan terakreditasi.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('transaksikoran.index') }}" 
                   class="px-5 py-2.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition">
                    Kembali
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    Simpan Perubahan Pengaturan
                </button>
            </div>
        </form>

    </div>

    @push('scripts')
    <script>
        function formatRupiahInput(el) {
            let val = el.value.replace(/[^0-9]/g, '');
            el.value = val ? new Intl.NumberFormat('id-ID').format(val) : '0';
        }
    </script>
    @endpush
</x-app-layout>
