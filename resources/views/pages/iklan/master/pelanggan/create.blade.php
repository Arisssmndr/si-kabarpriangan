<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman Standar Selaras Menu Lainnya -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Tambah Pelanggan Baru
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl">
                        Master Data Pemasang Iklan &amp; Instansi (CRM)
                    </p>
                </div>
            </div>
            <a href="{{ route('pelanggan.index') }}" 
               class="inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer shrink-0 whitespace-nowrap">
                &larr; <span>Kembali</span>
            </a>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 space-y-1">
                <strong class="font-semibold block mb-0.5">Terdapat kesalahan input:</strong>
                <ul class="list-disc list-inside space-y-0.5 font-normal">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulir Data Pelanggan Bersih & Proporsional -->
        <form method="POST" action="{{ route('pelanggan.store') }}" class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 shadow-xs space-y-5">
            @csrf

            <!-- Baris 1: Kode Pelanggan & Jenis Klien -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Kode Pelanggan</label>
                    <input type="text" value="{{ $nextCode }}" readonly 
                           class="w-full h-11 px-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono font-medium text-slate-600 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Jenis Klien <span class="text-rose-500">*</span></label>
                    <select name="jenis_klien" required 
                            class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                        <option value="Perorangan" {{ old('jenis_klien') == 'Perorangan' ? 'selected' : '' }}>Perorangan (Walk-in Customer)</option>
                        <option value="Swasta" {{ old('jenis_klien') == 'Swasta' ? 'selected' : '' }}>Swasta / Korporat</option>
                        <option value="Pemda" {{ old('jenis_klien') == 'Pemda' ? 'selected' : '' }}>Instansi Pemda (APBD)</option>
                        <option value="BUMN" {{ old('jenis_klien') == 'BUMN' ? 'selected' : '' }}>BUMN / BUMD</option>
                        <option value="Agensi" {{ old('jenis_klien') == 'Agensi' ? 'selected' : '' }}>Biro Iklan (Agensi Periklanan)</option>
                    </select>
                </div>
            </div>

            <!-- Baris 2: Nama Instansi / Perusahaan -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Instansi / Perusahaan / Perorangan <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_instansi" value="{{ old('nama_instansi') }}" required 
                       placeholder="Nama instansi, perusahaan, atau perorangan"
                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
            </div>

            <!-- Baris 3: NPWP / NIK & Credit Limit -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">NPWP / NIK</label>
                    <input type="text" name="npwp_nik" value="{{ old('npwp_nik') }}" 
                           placeholder="Nomor NPWP atau NIK 16 digit"
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Plafon Kredit (Credit Limit)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-3 text-sm text-slate-400 font-normal pointer-events-none">Rp</span>
                        <input type="text" name="credit_limit" id="credit_limit" value="{{ old('credit_limit', '0') }}" onkeyup="formatRupiahInput(this)"
                               class="w-full h-11 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                    </div>
                </div>
            </div>

            <!-- Baris 4: No Telp & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">No. Telp / WhatsApp</label>
                    <input type="text" name="no_telp" value="{{ old('no_telp') }}" 
                           placeholder="08xxxxxxxxxx"
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                           placeholder="alamat@email.com"
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                </div>
            </div>

            <!-- Baris 5: Alamat Lengkap -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Alamat Lengkap</label>
                <textarea name="alamat" rows="3" 
                          placeholder="Alamat lengkap instansi atau domisili..."
                          class="w-full px-3.5 py-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">{{ old('alamat') }}</textarea>
            </div>

            <!-- Tombol Aksi Simpan / Batal -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('pelanggan.index') }}" 
                   class="px-4 py-2.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-sm font-semibold rounded-xl shadow-xs transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-5 py-2.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer">
                    Simpan Pelanggan
                </button>
            </div>
        </form>

    </div>

    @push('scripts')
    <script>
        function formatRupiahInput(el) {
            if (typeof window.formatRupiahInput === 'function') {
                window.formatRupiahInput(el);
            } else {
                let val = el.value.replace(/[^0-9]/g, '');
                el.value = val ? new Intl.NumberFormat('id-ID').format(val) : '0';
            }
        }
    </script>
    @endpush
</x-app-layout>
