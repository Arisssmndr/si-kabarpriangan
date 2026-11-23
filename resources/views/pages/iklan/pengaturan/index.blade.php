<x-app-layout>
    @php
        $totalPaketAktif = ($paketSummary['koran'] ?? 0) + ($paketSummary['online'] ?? 0) + ($paketSummary['tv'] ?? 0);
    @endphp

    <div class="space-y-5 max-w-7xl mx-auto">

        <!-- Form Pengaturan Terpadu -->
        <form method="POST" action="{{ route('iklan.pengaturan.update') }}" id="formPengaturanIklan" class="space-y-5">
            @csrf

            <!-- Header Halaman Modern 60-30-10 -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-4 min-w-0">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-10 w-auto object-contain shrink-0">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                                Pengaturan Iklan
                            </h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $settings['is_past_cutoff'] ? 'bg-slate-100 text-slate-700 border border-slate-200' : 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $settings['is_past_cutoff'] ? 'bg-slate-400' : 'bg-kp-blue-600' }} mr-1.5"></span>
                                {{ $settings['is_past_cutoff'] ? 'Cetak Tutup' : 'Cetak Buka' }}
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1 truncate">
                            Parameter pajak PPN, jam cut-off operasional, dan batas cetak iklan.
                        </p>
                    </div>
                </div>

                <div class="flex items-center flex-wrap gap-2.5 shrink-0">
                    <a href="{{ route('jenis-iklan.index') }}"
                       class="inline-flex items-center gap-2 h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <span>Master Jenis Iklan</span>
                        <span class="px-2 py-0.5 rounded-md text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $totalPaketAktif }}
                        </span>
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </div>

            <!-- Pesan Notifikasi Sukses / Gagal -->
            @if(session('success'))
                <div class="p-4 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-900 rounded-xl text-xs sm:text-sm font-medium flex items-center gap-2.5 shadow-2xs">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (($errors ?? null)?->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs sm:text-sm font-medium shadow-2xs space-y-1">
                    <strong class="font-semibold block">Terdapat kesalahan pengisian data:</strong>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 3 Kolom Kartu Pengaturan Bersih & Terstruktur -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-stretch">

                <!-- 1. Pajak & Operasional -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col justify-between space-y-5">
                    <div>
                        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                            <h2 class="text-sm font-bold text-slate-900">Pajak &amp; Operasional</h2>
                            <span class="text-xs font-semibold text-slate-600">Sistem</span>
                        </div>

                        <div class="space-y-4 pt-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Pajak PPN <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative flex items-center">
                                    <input type="number" step="0.1" min="0" max="100" name="ppn_persen"
                                           value="{{ old('ppn_persen', $settings['ppn_persen']) }}" required
                                           class="w-full h-10 px-3.5 pr-9 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                    <span class="absolute right-3.5 text-xs text-slate-400 font-semibold pointer-events-none">%</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Batas Waktu Cetak <span class="text-rose-500">*</span>
                                </label>
                                <input type="time" name="jam_cutoff_iklan"
                                       value="{{ old('jam_cutoff_iklan', $settings['jam_cutoff_iklan']) }}" required
                                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Diskon Default Agensi <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative flex items-center">
                                    <input type="number" step="0.5" min="0" max="100" name="diskon_agensi_default"
                                           value="{{ old('diskon_agensi_default', $settings['diskon_agensi_default']) }}" required
                                           class="w-full h-10 px-3.5 pr-9 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                    <span class="absolute right-3.5 text-xs text-slate-400 font-semibold pointer-events-none">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Ketentuan Iklan Baris -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col justify-between space-y-5">
                    <div>
                        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                            <h2 class="text-sm font-bold text-slate-900">Ketentuan Iklan Baris</h2>
                            <span class="text-xs font-semibold text-slate-600">Iklan Cetak</span>
                        </div>

                        <div class="space-y-4 pt-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Minimal Baris <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative flex items-center">
                                    <input type="number" min="1" max="50" name="minimal_baris"
                                           value="{{ old('minimal_baris', $settings['minimal_baris']) }}" required
                                           class="w-full h-10 px-3.5 pr-14 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                    <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">baris</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Batas Karakter per Baris <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative flex items-center">
                                    <input type="number" min="10" max="200" name="maksimal_karakter_per_baris"
                                           value="{{ old('maksimal_karakter_per_baris', $settings['maksimal_karakter_per_baris']) }}" required
                                           class="w-full h-10 px-3.5 pr-16 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                    <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">karakter</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shortcut Kelola Tarif Baris di Master -->
                    <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-800 block">Tarif &amp; Paket Baris</span>
                            <span class="text-[11px] text-slate-500 block truncate">Dikelola di Master Jenis Iklan</span>
                        </div>
                        <a href="{{ route('jenis-iklan.index') }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition shrink-0">
                            <span>Kelola</span>
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- 3. Tarif Acuan Display & Portal -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col justify-between space-y-5">
                    <div>
                        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                            <h2 class="text-sm font-bold text-slate-900">Tarif Acuan Display &amp; Portal</h2>
                            <span class="text-xs font-semibold text-slate-600">Lintas Media</span>
                        </div>

                        <div class="space-y-4 pt-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Display Hitam Putih (BW)
                                </label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3.5 text-xs text-slate-400 font-mono pointer-events-none">Rp</span>
                                    <input type="text" name="tarif_mmk_bw"
                                           value="{{ number_format((int) old('tarif_mmk_bw', $settings['tarif_mmk_bw']), 0, ',', '.') }}"
                                           onkeyup="formatRupiahPengaturan(this)"
                                           class="w-full h-10 pl-9 pr-14 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                    <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">/ mmk</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Display Berwarna (FC)
                                </label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3.5 text-xs text-slate-400 font-mono pointer-events-none">Rp</span>
                                    <input type="text" name="tarif_mmk_fc"
                                           value="{{ number_format((int) old('tarif_mmk_fc', $settings['tarif_mmk_fc']), 0, ',', '.') }}"
                                           onkeyup="formatRupiahPengaturan(this)"
                                           class="w-full h-10 pl-9 pr-14 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                    <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">/ mmk</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Publikasi Portal Berita
                                </label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3.5 text-xs text-slate-400 font-mono pointer-events-none">Rp</span>
                                    <input type="text" name="tarif_portal_berita"
                                           value="{{ number_format((int) old('tarif_portal_berita', $settings['tarif_portal_berita']), 0, ',', '.') }}"
                                           onkeyup="formatRupiahPengaturan(this)"
                                           class="w-full h-10 pl-9 pr-16 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                    <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">/ portal</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer Simpan Pengaturan -->
            <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 h-10 px-6 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Pengaturan</span>
                </button>
            </div>

        </form>

    </div>

    @push('scripts')
    <script>
        function formatRupiahPengaturan(el) {
            const raw = (el.value || '').replace(/[^0-9]/g, '');
            el.value = raw ? new Intl.NumberFormat('id-ID').format(parseInt(raw, 10)) : '0';
        }
    </script>
    @endpush
</x-app-layout>