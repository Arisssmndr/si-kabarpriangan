<x-app-layout>
    <div class="space-y-5">

        <form method="POST" action="{{ route('sirkulasi.pengaturan.update') }}" class="space-y-5">
            @csrf

            <!-- Header Halaman -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-3.5 min-w-0">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                    <div class="min-w-0">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Tarif &amp; Pengaturan Sirkulasi
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl" title="Pengaturan tarif harga koran, operasional distribusi, dan jam cut-off harian.">
                            Pengaturan tarif harga koran, operasional distribusi, dan jam cut-off harian.
                        </p>
                    </div>
                </div>

                <div class="flex items-center flex-wrap gap-2.5 shrink-0">
                    <a href="{{ route('sirkulasi.jatah.edit') }}"
                       class="inline-flex items-center gap-1.5 h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition whitespace-nowrap">
                        Input Oplah Harian
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
                <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs sm:text-sm font-semibold flex items-center gap-2 shadow-2xs">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(($errors ?? null)?->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs sm:text-sm font-medium shadow-2xs space-y-1">
                    <strong class="font-semibold block">Terdapat data yang belum sesuai:</strong>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 3 Kartu Pengaturan Bersih & Simpel (60-30-10) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

                <!-- 1. Tarif Koran -->
                <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">Tarif Koran</h2>
                        <span class="inline-block px-2 py-0.5 rounded-md text-[11px] font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Aktif
                        </span>
                    </div>

                    <div class="space-y-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Harga Eceran <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-xs text-slate-400 font-mono pointer-events-none">Rp</span>
                                <input type="text" name="sirkulasi_harga_eceran"
                                       value="{{ number_format((int) old('sirkulasi_harga_eceran', $settings['harga_eceran']), 0, ',', '.') }}"
                                       onkeyup="formatRupiahSirkulasi(this)" required
                                       class="w-full h-10 pl-9 pr-14 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-kp-blue-700 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">/ eks</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Harga Agen Besar <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-xs text-slate-400 font-mono pointer-events-none">Rp</span>
                                <input type="text" name="sirkulasi_harga_agen_besar"
                                       value="{{ number_format((int) old('sirkulasi_harga_agen_besar', $settings['harga_agen_besar']), 0, ',', '.') }}"
                                       onkeyup="formatRupiahSirkulasi(this)" required
                                       class="w-full h-10 pl-9 pr-14 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">/ eks</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Harga Langganan <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-xs text-slate-400 font-mono pointer-events-none">Rp</span>
                                <input type="text" name="sirkulasi_harga_langganan"
                                       value="{{ number_format((int) old('sirkulasi_harga_langganan', $settings['harga_langganan']), 0, ',', '.') }}"
                                       onkeyup="formatRupiahSirkulasi(this)" required
                                       class="w-full h-10 pl-9 pr-14 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">/ eks</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                HET (Cover) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-xs text-slate-400 font-mono pointer-events-none">Rp</span>
                                <input type="text" name="sirkulasi_harga_cover_het"
                                       value="{{ number_format((int) old('sirkulasi_harga_cover_het', $settings['harga_cover_het']), 0, ',', '.') }}"
                                       onkeyup="formatRupiahSirkulasi(this)" required
                                       class="w-full h-10 pl-9 pr-14 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">/ eks</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Operasional & Cut-Off -->
                <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">Operasional &amp; Cut-Off</h2>
                        <span class="inline-block px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $isPastCutoff ? 'bg-slate-100 text-slate-600 border border-slate-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                            {{ $isPastCutoff ? 'Cut-Off Selesai' : 'Bisa Input' }}
                        </span>
                    </div>

                    <div class="space-y-3.5">
                        <div x-data="{ cutoffVal: '{{ old('sirkulasi_jam_cutoff', $settings['jam_cutoff']) }}' }">
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Jam Cut-Off Harian <span class="text-rose-500">*</span>
                            </label>
                            <input type="time"
                                   name="sirkulasi_jam_cutoff"
                                   x-model="cutoffVal"
                                   value="{{ old('sirkulasi_jam_cutoff', $settings['jam_cutoff']) }}" required
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                            
                            <!-- Preset Cepat -->
                            <div class="grid grid-cols-5 gap-1.5 mt-2">
                                <button type="button" @click="cutoffVal = '14:00'" class="py-1 rounded-lg border text-xs font-mono font-bold transition text-center cursor-pointer" :class="cutoffVal === '14:00' ? 'bg-kp-blue-600 text-white border-kp-blue-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">14:00</button>
                                <button type="button" @click="cutoffVal = '16:00'" class="py-1 rounded-lg border text-xs font-mono font-bold transition text-center cursor-pointer" :class="cutoffVal === '16:00' ? 'bg-kp-blue-600 text-white border-kp-blue-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">16:00</button>
                                <button type="button" @click="cutoffVal = '17:00'" class="py-1 rounded-lg border text-xs font-mono font-bold transition text-center cursor-pointer" :class="cutoffVal === '17:00' ? 'bg-kp-blue-600 text-white border-kp-blue-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">17:00</button>
                                <button type="button" @click="cutoffVal = '18:00'" class="py-1 rounded-lg border text-xs font-mono font-bold transition text-center cursor-pointer" :class="cutoffVal === '18:00' ? 'bg-kp-blue-600 text-white border-kp-blue-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">18:00</button>
                                <button type="button" @click="cutoffVal = '20:00'" class="py-1 rounded-lg border text-xs font-mono font-bold transition text-center cursor-pointer" :class="cutoffVal === '20:00' ? 'bg-kp-blue-600 text-white border-kp-blue-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'">20:00</button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Isi per Ikat (Bendel) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <input type="number" min="10" max="500" name="sirkulasi_isi_bendel"
                                       value="{{ old('sirkulasi_isi_bendel', $settings['isi_bendel']) }}" required
                                       class="w-full h-10 px-3.5 pr-16 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">eks / ikat</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Batas Retur <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <input type="number" step="0.5" min="0" max="100" name="sirkulasi_batas_retur_persen"
                                       value="{{ old('sirkulasi_batas_retur_persen', $settings['batas_retur_persen']) }}" required
                                       class="w-full h-10 px-3.5 pr-9 bg-white border border-slate-300 rounded-xl text-sm font-bold font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-500/20 transition shadow-2xs">
                                <span class="absolute right-3.5 text-xs text-slate-400 font-normal pointer-events-none">%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Rasio Default Oplah -->
                <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">Rasio Default Oplah</h2>
                        <span class="inline-block px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            Total 100%
                        </span>
                    </div>

                    <div class="space-y-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Langganan <span class="text-rose-500">*</span>
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
                                Konsinyasi <span class="text-rose-500">*</span>
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
                                Gratis / Terbit <span class="text-rose-500">*</span>
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

            <!-- Footer Simpan -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex items-center justify-between gap-3">
                <span class="text-xs text-slate-500">
                    Perubahan langsung berlaku untuk seluruh operasional sirkulasi.
                </span>
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
