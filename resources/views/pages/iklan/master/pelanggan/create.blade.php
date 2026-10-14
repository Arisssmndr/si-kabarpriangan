<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">

        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Tambah Pelanggan Baru (CRM)
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Daftarkan instansi, biro iklan, perusahaan, atau pemasang walk-in ke database terpusat
                    </p>
                </div>
            </div>
            <a href="{{ route('pelanggan.index') }}" 
               class="px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                &larr; Kembali
            </a>
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

        <form method="POST" action="{{ route('pelanggan.store') }}" class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kode Pelanggan</label>
                    <input type="text" value="{{ $nextCode }}" readonly 
                           class="w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-600 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Klien <span class="text-red-500">*</span></label>
                    <select name="jenis_klien" required 
                            class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="Perorangan" {{ old('jenis_klien') == 'Perorangan' ? 'selected' : '' }}>Perorangan (Walk-in Customer)</option>
                        <option value="Swasta" {{ old('jenis_klien') == 'Swasta' ? 'selected' : '' }}>Swasta / Korporat</option>
                        <option value="Pemda" {{ old('jenis_klien') == 'Pemda' ? 'selected' : '' }}>Instansi Pemda (APBD)</option>
                        <option value="BUMN" {{ old('jenis_klien') == 'BUMN' ? 'selected' : '' }}>BUMN / BUMD</option>
                        <option value="Agensi" {{ old('jenis_klien') == 'Agensi' ? 'selected' : '' }}>Biro Iklan (Agensi Periklanan)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Instansi / Perusahaan / Perorangan <span class="text-red-500">*</span></label>
                <input type="text" name="nama_instansi" value="{{ old('nama_instansi') }}" required placeholder="Contoh: Dinas Kominfo Kabupaten Ciamis / PT Djarum / Toko Mas ABC"
                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">NPWP / NIK (Untuk e-Faktur Pajak)</label>
                    <input type="text" name="npwp_nik" value="{{ old('npwp_nik') }}" placeholder="Contoh: 01.234.567.8-425.000 atau NIK 16 digit"
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    <p class="text-[11px] text-slate-400 mt-1">Wajib diisi untuk faktur pajak resmi e-Faktur DJP klien korporat/Pemda.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Credit Limit (Plafon Kredit)</label>
                    <input type="text" name="credit_limit" id="credit_limit" value="{{ old('credit_limit', '0') }}" onkeyup="formatRupiahInput(this)"
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                    <p class="text-[11px] text-slate-400 mt-1">Isi 0 jika wajib Cash Before Print (CBP). Isi nominal jika ada batas piutang yang diizinkan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Kontak PIC</label>
                    <input type="text" name="kontak_pic" value="{{ old('kontak_pic') }}" placeholder="Contoh: Bpk. Asep (Kasubag)"
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">No. Telp / WhatsApp</label>
                    <input type="text" name="no_telp" value="{{ old('no_telp') }}" placeholder="08xxxxxxxxxx"
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="kontak@instansi.go.id"
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Lengkap</label>
                <textarea name="alamat" rows="3" placeholder="Masukkan alamat instansi/perusahaan lengkap..."
                          class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">{{ old('alamat') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('pelanggan.index') }}" 
                   class="px-4 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-5 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    Simpan Pelanggan
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
