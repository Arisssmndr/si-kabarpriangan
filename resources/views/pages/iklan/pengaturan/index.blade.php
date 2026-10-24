<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman Standar Selaras Pelanggan & Master Lainnya -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Pengaturan Tarif &amp; Pajak Iklan
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Konfigurasi parameter tarif dasar cetak koran, batas jam cut-off redaksi, PPN, dan bea meterai.
                    </p>
                </div>
            </div>
            <a href="{{ route('transaksikoran.index') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                &larr; <span>Kembali ke Transaksi Koran</span>
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-medium flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 space-y-1 shadow-2xs">
                <strong class="font-semibold block mb-0.5">Terdapat kesalahan input:</strong>
                <ul class="list-disc list-inside space-y-0.5 font-normal">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulir Pengaturan Bersih & Proporsional Selaras Form Master Pelanggan -->
        <form method="POST" action="{{ route('iklan.pengaturan.update') }}" class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 shadow-xs space-y-6">
            @csrf

            <!-- SEKSI 1: Regulasi Pajak & Batas Deadline Redaksi -->
            <div class="space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-5 h-5 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-[10px] font-bold">1</span>
                        <span>Regulasi Pajak &amp; Batas Deadline Redaksi</span>
                    </h2>
                    <p class="text-[11px] text-slate-500 mt-0.5 font-normal">
                        Parameter perpajakan UU HPP, ambang bea meterai, dan cut-off order Front Office
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- PPN Resmi -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Tarif PPN Resmi <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" step="0.1" name="ppn_persen" value="{{ old('ppn_persen', $configs['ppn_persen']->value ?? '12') }}" required
                                   class="w-full h-10 px-3 pr-8 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                            <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-normal pointer-events-none">%</span>
                        </div>
                    </div>

                    <!-- Cut-Off Iklan Harian -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Jam Cut-Off Iklan Harian (WIB) <span class="text-rose-500">*</span>
                        </label>
                        <input type="time" name="jam_cutoff_iklan" value="{{ old('jam_cutoff_iklan', $configs['jam_cutoff_iklan']->value ?? '14:00') }}" required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                    </div>

                    <!-- Ambang Batas Meterai -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Ambang Batas Bea Meterai (Rp)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-normal pointer-events-none">Rp</span>
                            <input type="text" name="materai_threshold" id="materai_threshold" value="{{ number_format($configs['materai_threshold']->value ?? 5000000, 0, ',', '.') }}" onkeyup="formatRupiahInput(this)"
                                   class="w-full h-10 pl-9 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                        </div>
                    </div>

                    <!-- Nominal Meterai -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Nominal Bea Meterai (Rp)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-normal pointer-events-none">Rp</span>
                            <input type="text" name="materai_nominal" id="materai_nominal" value="{{ number_format($configs['materai_nominal']->value ?? 10000, 0, ',', '.') }}" onkeyup="formatRupiahInput(this)"
                                   class="w-full h-10 pl-9 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEKSI 2: Standar Tarif Iklan Cetak Koran -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-5 h-5 rounded-md bg-kp-blue-600 text-white flex items-center justify-center text-[10px] font-bold">2</span>
                        <span>Standar Tarif Iklan Cetak Koran</span>
                    </h2>
                    <p class="text-[11px] text-slate-500 mt-0.5 font-normal">
                        Formula baku kalkulasi tarif iklan baris dan display (mmk) pada sistem transaksi Front Office
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Tarif Baris -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Tarif Baris (per baris / muat)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-normal pointer-events-none">Rp</span>
                            <input type="text" name="tarif_per_baris" id="tarif_per_baris" value="{{ number_format($configs['tarif_per_baris']->value ?? 15000, 0, ',', '.') }}" onkeyup="formatRupiahInput(this)"
                                   class="w-full h-10 pl-9 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                        </div>
                    </div>

                    <!-- Minimal Pemuatan Baris -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Minimal Pemuatan Baris
                        </label>
                        <div class="relative">
                            <input type="number" min="1" name="minimal_baris" value="{{ old('minimal_baris', $configs['minimal_baris']->value ?? '3') }}"
                                   class="w-full h-10 px-3 pr-14 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                            <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-normal pointer-events-none">Baris</span>
                        </div>
                    </div>

                    <!-- Tarif Kolom / Display BW -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Tarif Kolom / Display BW (per mmk)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-normal pointer-events-none">Rp</span>
                            <input type="text" name="tarif_mmk_bw" id="tarif_mmk_bw" value="{{ number_format($configs['tarif_mmk_bw']->value ?? 25000, 0, ',', '.') }}" onkeyup="formatRupiahInput(this)"
                                   class="w-full h-10 pl-9 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                        </div>
                    </div>

                    <!-- Tarif Kolom / Display FC -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Tarif Kolom / Display Full Color (per mmk)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-normal pointer-events-none">Rp</span>
                            <input type="text" name="tarif_mmk_fc" id="tarif_mmk_fc" value="{{ number_format($configs['tarif_mmk_fc']->value ?? 45000, 0, ',', '.') }}" onkeyup="formatRupiahInput(this)"
                                   class="w-full h-10 pl-9 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                        </div>
                    </div>

                    <!-- Batas Karakter per Baris -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Batas Karakter per Baris Cetak
                        </label>
                        <div class="relative">
                            <input type="number" min="10" name="maksimal_karakter_per_baris" value="{{ old('maksimal_karakter_per_baris', $configs['maksimal_karakter_per_baris']->value ?? '32') }}"
                                   class="w-full h-10 px-3 pr-20 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                            <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-normal pointer-events-none">Karakter</span>
                        </div>
                    </div>

                    <!-- Diskon Agensi Default -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">
                            Diskon Agensi / Biro Iklan Default
                        </label>
                        <div class="relative">
                            <input type="number" min="0" max="100" step="0.5" name="diskon_agensi_default" value="{{ old('diskon_agensi_default', $configs['diskon_agensi_default']->value ?? '15') }}"
                                   class="w-full h-10 px-3 pr-8 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                            <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-normal pointer-events-none">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi Simpan / Batal Selaras Master Pelanggan -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('transaksikoran.index') }}" 
                   class="px-4 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl shadow-xs transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-5 py-2.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition cursor-pointer flex items-center gap-1.5">
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
