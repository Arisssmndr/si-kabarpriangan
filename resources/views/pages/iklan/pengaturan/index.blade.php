<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Standar Selaras Foto 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-black tracking-tight">
                        Pengaturan Tarif &amp; Pajak Iklan
                    </h1>
                    <p class="text-xs text-black mt-0.5">
                        Konfigurasi parameter tarif dasar cetak koran, batas jam cut-off redaksi, PPN, dan bea meterai.
                    </p>
                </div>
            </div>
            <a href="{{ route('transaksikoran.index') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-black text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                &larr; <span>Kembali ke Transaksi Koran</span>
            </a>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('iklan.pengaturan.update') }}" class="space-y-5">
            @csrf

            <!-- Section 1: Regulasi Pajak & Batas Waktu Cetak -->
            <div class="bg-white border border-slate-300 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-4 bg-slate-50/90 border-b border-slate-200">
                    <h2 class="text-sm font-bold text-slate-900">
                        1. Regulasi Pajak &amp; Batas Deadline Redaksi
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5 font-normal">
                        Parameter perpajakan UU HPP, ambang bea meterai, dan cut-off order Front Office
                    </p>
                </div>

                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- PPN -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Tarif PPN Resmi
                        </label>
                        <div class="relative">
                            <input type="number" step="0.1" name="ppn_persen" value="{{ $configs['ppn_persen']->value ?? '12' }}" required
                                   class="w-full h-10 px-3.5 pr-10 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <span class="absolute right-3.5 top-2.5 text-xs font-bold text-slate-400 pointer-events-none">%</span>
                        </div>
                    </div>

                    <!-- Cut-Off -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Jam Cut-Off Iklan Harian (WIB)
                        </label>
                        <input type="time" name="jam_cutoff_iklan" value="{{ $configs['jam_cutoff_iklan']->value ?? '14:00' }}" required
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    </div>

                    <!-- Ambang Meterai -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Ambang Batas Bea Meterai (Rp)
                        </label>
                        <div class="flex rounded-lg shadow-2xs">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-xs font-semibold">Rp</span>
                            <input type="text" name="materai_threshold" value="{{ number_format($configs['materai_threshold']->value ?? 5000000, 0, ',', '.') }}" onkeyup="formatRupiahInput(this)"
                                   class="flex-1 h-10 px-3.5 bg-white border border-slate-300 rounded-r-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        </div>
                    </div>

                    <!-- Nominal Meterai -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nominal Bea Meterai (Rp)
                        </label>
                        <div class="flex rounded-lg shadow-2xs">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-xs font-semibold">Rp</span>
                            <input type="text" name="materai_nominal" value="{{ number_format($configs['materai_nominal']->value ?? 10000, 0, ',', '.') }}" onkeyup="formatRupiahInput(this)"
                                   class="flex-1 h-10 px-3.5 bg-white border border-slate-300 rounded-r-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Tarif Iklan Koran -->
            <div class="bg-white border border-slate-300 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-4 bg-slate-50/90 border-b border-slate-200">
                    <h2 class="text-sm font-bold text-slate-900">
                        2. Standar Tarif Iklan Cetak Koran
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5 font-normal">
                        Formula baku kalkulasi tarif iklan baris dan display (mmk) pada sistem transaksi Front Office
                    </p>
                </div>

                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Tarif Baris -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Tarif Baris (per baris / muat)
                        </label>
                        <div class="flex rounded-lg shadow-2xs">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-xs font-semibold">Rp</span>
                            <input type="text" name="tarif_per_baris" value="{{ number_format($configs['tarif_per_baris']->value ?? 15000, 0, ',', '.') }}" onkeyup="formatRupiahInput(this)"
                                   class="flex-1 h-10 px-3.5 bg-white border border-slate-300 rounded-r-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        </div>
                    </div>

                    <!-- Minimal Baris -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Minimal Pemuatan Baris
                        </label>
                        <div class="relative">
                            <input type="number" min="1" name="minimal_baris" value="{{ $configs['minimal_baris']->value ?? '3' }}"
                                   class="w-full h-10 px-3.5 pr-14 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <span class="absolute right-3.5 top-2.5 text-xs font-semibold text-slate-400 pointer-events-none">Baris</span>
                        </div>
                    </div>

                    <!-- Tarif BW -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Tarif Kolom / Display BW (per mmk)
                        </label>
                        <div class="flex rounded-lg shadow-2xs">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-xs font-semibold">Rp</span>
                            <input type="text" name="tarif_mmk_bw" value="{{ number_format($configs['tarif_mmk_bw']->value ?? 25000, 0, ',', '.') }}" onkeyup="formatRupiahInput(this)"
                                   class="flex-1 h-10 px-3.5 bg-white border border-slate-300 rounded-r-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        </div>
                    </div>

                    <!-- Tarif FC -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Tarif Kolom / Display Full Color (per mmk)
                        </label>
                        <div class="flex rounded-lg shadow-2xs">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-xs font-semibold">Rp</span>
                            <input type="text" name="tarif_mmk_fc" value="{{ number_format($configs['tarif_mmk_fc']->value ?? 45000, 0, ',', '.') }}" onkeyup="formatRupiahInput(this)"
                                   class="flex-1 h-10 px-3.5 bg-white border border-slate-300 rounded-r-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        </div>
                    </div>

                    <!-- Kapasitas Karakter -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Batas Karakter per Baris Cetak
                        </label>
                        <div class="relative">
                            <input type="number" min="10" name="maksimal_karakter_per_baris" value="{{ $configs['maksimal_karakter_per_baris']->value ?? '32' }}"
                                   class="w-full h-10 px-3.5 pr-20 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <span class="absolute right-3.5 top-2.5 text-xs font-semibold text-slate-400 pointer-events-none">Karakter</span>
                        </div>
                    </div>

                    <!-- Diskon Agensi -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Diskon Agensi / Biro Iklan Default
                        </label>
                        <div class="relative">
                            <input type="number" min="0" max="100" name="diskon_agensi_default" value="{{ $configs['diskon_agensi_default']->value ?? '15' }}"
                                   class="w-full h-10 px-3.5 pr-10 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <span class="absolute right-3.5 top-2.5 text-xs font-bold text-slate-400 pointer-events-none">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Footer -->
            <div class="flex items-center justify-end gap-3 pt-1">
                <a href="{{ route('transaksikoran.index') }}" 
                   class="h-10 px-5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-xs transition flex items-center justify-center">
                    Batal
                </a>
                <button type="submit" 
                        class="h-10 px-6 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs font-semibold rounded-lg shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    <span>Simpan Perubahan Tarif</span>
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
