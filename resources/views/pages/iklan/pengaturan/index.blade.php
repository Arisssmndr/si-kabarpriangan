<x-app-layout>
    @php
        $ppnFormatted = rtrim(rtrim(number_format((float) $settings['ppn_persen'], 1, ',', '.'), '0'), ',');
        $diskonFormatted = rtrim(rtrim(number_format((float) $settings['diskon_agensi_default'], 1, ',', '.'), '0'), ',');
        $totalPaketAktif = ($paketSummary['koran'] ?? 0) + ($paketSummary['online'] ?? 0) + ($paketSummary['tv'] ?? 0);
    @endphp

    <div class="space-y-5">

        <!-- Form Pengaturan Terpadu -->
        <form method="POST" action="{{ route('iklan.pengaturan.update') }}" id="formPengaturanIklan" class="space-y-5">
            @csrf

            <!-- Header Halaman Standar (Selaras Menu Divisi Iklan) -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-3.5 min-w-0">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                    <div class="min-w-0">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Pengaturan Iklan
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-2xl">
                            Tarif dasar iklan, pajak PPN, dan batas waktu operasional cetak.
                        </p>
                    </div>
                </div>

                <div class="flex items-center flex-wrap gap-2.5 shrink-0">
                    <a href="{{ route('jenis-iklan.index') }}"
                       class="inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition whitespace-nowrap">
                        <span>Master Jenis Iklan ({{ $totalPaketAktif }})</span>
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Pengaturan</span>
                    </button>
                </div>
            </div>

            <!-- Pesan Notifikasi Sukses / Gagal -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs sm:text-sm font-medium flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
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

            <!-- 3 Kartu Pengaturan Utama (Langsung Berupa Form Bersih & Ringkas) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

                <!-- 1. Pajak & Operasional -->
                <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">Pajak &amp; Operasional</h2>
                        <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium {{ $settings['is_past_cutoff'] ? 'bg-slate-100 text-slate-700 border border-slate-200' : 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' }}">
                            {{ $settings['is_past_cutoff'] ? 'Cetak Tutup' : 'Cetak Buka' }}
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Pajak PPN <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <input type="number" step="0.1" min="0" max="100" name="ppn_persen"
                                       value="{{ old('ppn_persen', $settings['ppn_persen']) }}" required
                                       class="w-full h-10 px-3.5 pr-9 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">%</span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Dihitung otomatis dari Netto (DPP)</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Batas Waktu Cetak Koran <span class="text-rose-500">*</span>
                            </label>
                            <input type="time" name="jam_cutoff_iklan"
                                   value="{{ old('jam_cutoff_iklan', $settings['jam_cutoff_iklan']) }}" required
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                            <span class="text-[11px] text-slate-500 mt-1 block">Batas penerimaan iklan koran edisi besok</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Diskon Standar Biro Iklan <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <input type="number" step="0.5" min="0" max="100" name="diskon_agensi_default"
                                       value="{{ old('diskon_agensi_default', $settings['diskon_agensi_default']) }}" required
                                       class="w-full h-10 px-3.5 pr-9 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">%</span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Diskon awal untuk kategori biro / agensi</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Tarif Iklan Baris -->
                <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">Tarif Iklan Baris</h2>
                        <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                            Iklan Koran
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Tarif per Baris <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-xs text-slate-400 font-mono pointer-events-none">Rp</span>
                                <input type="text" name="tarif_per_baris"
                                       value="{{ number_format((int) old('tarif_per_baris', $settings['tarif_per_baris']), 0, ',', '.') }}"
                                       onkeyup="formatRupiahPengaturan(this)" required
                                       class="w-full h-10 pl-9 pr-14 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-kp-blue-700 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">/ baris</span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Tarif dasar per baris setiap pemuatan</span>
                        </div>

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
                            <span class="text-[11px] text-slate-500 mt-1 block">Batas minimal order iklan baris</span>
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
                            <span class="text-[11px] text-slate-500 mt-1 block">Patokan otomatis saat input naskah</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Tarif Display & Portal Web -->
                <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">Tarif Display &amp; Portal</h2>
                        <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                            Koran, Online &amp; TV
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Display Hitam Putih (BW) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-xs text-slate-400 font-mono pointer-events-none">Rp</span>
                                <input type="text" name="tarif_mmk_bw"
                                       value="{{ number_format((int) old('tarif_mmk_bw', $settings['tarif_mmk_bw']), 0, ',', '.') }}"
                                       onkeyup="formatRupiahPengaturan(this)" required
                                       class="w-full h-10 pl-9 pr-14 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">/ mmk</span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Tarif per milimeter kolom (Koran)</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Display Berwarna (FC) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-xs text-slate-400 font-mono pointer-events-none">Rp</span>
                                <input type="text" name="tarif_mmk_fc"
                                       value="{{ number_format((int) old('tarif_mmk_fc', $settings['tarif_mmk_fc']), 0, ',', '.') }}"
                                       onkeyup="formatRupiahPengaturan(this)" required
                                       class="w-full h-10 pl-9 pr-14 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">/ mmk</span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Tarif warna penuh per milimeter kolom</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Publikasi Portal Berita <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-xs text-slate-400 font-mono pointer-events-none">Rp</span>
                                <input type="text" name="tarif_portal_berita"
                                       value="{{ number_format((int) old('tarif_portal_berita', $settings['tarif_portal_berita']), 0, ',', '.') }}"
                                       onkeyup="formatRupiahPengaturan(this)" required
                                       class="w-full h-10 pl-9 pr-16 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-kp-blue-700 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">/ portal</span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Berlaku untuk portal Online &amp; Priangan TV</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer Simpan Pengaturan -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="text-xs text-slate-500">
                    Nilai pengaturan langsung tersinkronisasi ke seluruh formulir transaksi iklan baru.
                </div>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer shrink-0">
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
