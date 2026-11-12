<x-app-layout>
    <div class="space-y-5">

        <form method="POST" action="{{ route('sirkulasi.pengaturan.update') }}" class="space-y-5">
            @csrf

            <!-- Header Halaman (Standar Divisi Iklan, 60-30-10) -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-3.5 min-w-0">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                    <div class="min-w-0">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Tarif &amp; Pengaturan Sirkulasi
                        </h1>
                        <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 leading-relaxed">
                            Pengelolaan harga jual koran per eksemplar, standar ikat bendel percetakan, batas retur, dan komposisi oplah default.
                        </p>
                    </div>
                </div>

                <div class="flex items-center flex-wrap gap-2.5 shrink-0">
                    <a href="{{ route('sirkulasi.jatah.edit') }}"
                       class="inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition whitespace-nowrap">
                        <span>Input Oplah Harian</span>
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Pengaturan</span>
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs sm:text-sm font-semibold flex items-center gap-2 shadow-2xs">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(($errors ?? null)?->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs sm:text-sm font-medium shadow-2xs space-y-1">
                    <strong class="font-semibold block">Terdapat kesalahan pengisian data:</strong>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 3 Kartu Pengaturan Standar Industri Media (60-30-10) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

                <!-- 1. Harga Koran per Eksemplar -->
                <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">Tarif Koran per Eksemplar</h2>
                        <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Aktif
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Harga Eceran / Konsinyasi <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-xs text-slate-400 font-mono pointer-events-none">Rp</span>
                                <input type="text" name="sirkulasi_harga_eceran"
                                       value="{{ number_format((int) old('sirkulasi_harga_eceran', $settings['harga_eceran']), 0, ',', '.') }}"
                                       onkeyup="formatRupiahSirkulasi(this)" required
                                       class="w-full h-10 pl-9 pr-14 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-kp-blue-700 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">/ eks</span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Dipakai untuk perhitungan omset harian &amp; sub agen (99)</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Harga Nett Agen Besar (11) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-xs text-slate-400 font-mono pointer-events-none">Rp</span>
                                <input type="text" name="sirkulasi_harga_agen_besar"
                                       value="{{ number_format((int) old('sirkulasi_harga_agen_besar', $settings['harga_agen_besar']), 0, ',', '.') }}"
                                       onkeyup="formatRupiahSirkulasi(this)" required
                                       class="w-full h-10 pl-9 pr-14 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">/ eks</span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Tarif grosir khusus mitra drop point Agen Besar</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Harga Langganan Rutin <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-xs text-slate-400 font-mono pointer-events-none">Rp</span>
                                <input type="text" name="sirkulasi_harga_langganan"
                                       value="{{ number_format((int) old('sirkulasi_harga_langganan', $settings['harga_langganan']), 0, ',', '.') }}"
                                       onkeyup="formatRupiahSirkulasi(this)" required
                                       class="w-full h-10 pl-9 pr-14 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">/ eks</span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Tarif pelanggan tetap instansi &amp; perorangan</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Harga Eceran Tertinggi (HET Cover) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-xs text-slate-400 font-mono pointer-events-none">Rp</span>
                                <input type="text" name="sirkulasi_harga_cover_het"
                                       value="{{ number_format((int) old('sirkulasi_harga_cover_het', $settings['harga_cover_het']), 0, ',', '.') }}"
                                       onkeyup="formatRupiahSirkulasi(this)" required
                                       class="w-full h-10 pl-9 pr-14 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">/ eks</span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Harga jual eceran resmi di tangan pembaca</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Standar Pengepakan & Cut-Off -->
                <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">Pengepakan &amp; Operasional</h2>
                        <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium {{ $isPastCutoff ? 'bg-slate-100 text-slate-700 border border-slate-200' : 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' }}">
                            {{ $isPastCutoff ? 'Cut-Off Terlewati' : 'Distribusi Buka' }}
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Isi Standar 1 Ikat / Bendel <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <input type="number" min="10" max="500" name="sirkulasi_isi_bendel"
                                       value="{{ old('sirkulasi_isi_bendel', $settings['isi_bendel']) }}" required
                                       class="w-full h-10 px-3.5 pr-16 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">eks / ikat</span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Patokan konversi ikat otomatis di Nota Kirim Percetakan</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Jam Cut-Off Oplah H+1 <span class="text-rose-500">*</span>
                            </label>
                            <input type="time" name="sirkulasi_jam_cutoff"
                                   value="{{ old('sirkulasi_jam_cutoff', $settings['jam_cutoff']) }}" required
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                            <span class="text-[11px] text-slate-500 mt-1 block">Batas waktu serah data cetak ke percetakan</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Toleransi Maksimal Retur Agen <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <input type="number" step="0.5" min="0" max="100" name="sirkulasi_batas_retur_persen"
                                       value="{{ old('sirkulasi_batas_retur_persen', $settings['batas_retur_persen']) }}" required
                                       class="w-full h-10 px-3.5 pr-9 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">%</span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Standar batas pengembalian koran tak terjual</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Komposisi Rasio Default Oplah -->
                <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">Komposisi Default Oplah</h2>
                        <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                            Total 100%
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Porsi Langganan Default <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <input type="number" step="1" min="0" max="100" name="sirkulasi_rasio_langganan"
                                       value="{{ old('sirkulasi_rasio_langganan', $settings['rasio_langganan']) }}" required
                                       class="w-full h-10 px-3.5 pr-9 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">%</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Porsi Konsinyasi / Eceran Default <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <input type="number" step="1" min="0" max="100" name="sirkulasi_rasio_konsinyasi"
                                       value="{{ old('sirkulasi_rasio_konsinyasi', $settings['rasio_konsinyasi']) }}" required
                                       class="w-full h-10 px-3.5 pr-9 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-kp-blue-700 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">%</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Porsi Gratis / Bukti Terbit Default <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <input type="number" step="1" min="0" max="100" name="sirkulasi_rasio_gratis"
                                       value="{{ old('sirkulasi_rasio_gratis', $settings['rasio_gratis']) }}" required
                                       class="w-full h-10 px-3.5 pr-9 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">%</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="text-xs text-slate-500">
                    Perubahan tarif dan standar bendel otomatis disinkronkan ke Input Oplah Harian, Kalkulasi Penjualan, Nota Kirim Percetakan, dan seluruh laporan Sirkulasi.
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
        function formatRupiahSirkulasi(el) {
            const raw = (el.value || '').replace(/[^0-9]/g, '');
            el.value = raw ? new Intl.NumberFormat('id-ID').format(parseInt(raw, 10)) : '0';
        }
    </script>
    @endpush
</x-app-layout>

