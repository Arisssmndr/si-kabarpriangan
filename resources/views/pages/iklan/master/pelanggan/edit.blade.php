<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">

        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-black tracking-tight">
                        Edit Pelanggan — {{ $pelanggan->kode_pelanggan }}
                    </h1>
                    <p class="text-xs text-black mt-0.5">
                        Perbarui data instansi, kontak PIC, dan batas kredit klien
                    </p>
                </div>
            </div>
            <a href="{{ route('pelanggan.index') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-black text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
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

        <form method="POST" action="{{ route('pelanggan.update', $pelanggan->id) }}" class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kode Pelanggan</label>
                    <input type="text" value="{{ $pelanggan->kode_pelanggan }}" readonly 
                           class="w-full h-10 px-3.5 bg-slate-100 border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-600 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Klien <span class="text-red-500">*</span></label>
                    <select name="jenis_klien" required 
                            class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="Perorangan" {{ old('jenis_klien', $pelanggan->jenis_klien) == 'Perorangan' ? 'selected' : '' }}>Perorangan (Walk-in Customer)</option>
                        <option value="Swasta" {{ old('jenis_klien', $pelanggan->jenis_klien) == 'Swasta' ? 'selected' : '' }}>Swasta / Korporat</option>
                        <option value="Pemda" {{ old('jenis_klien', $pelanggan->jenis_klien) == 'Pemda' ? 'selected' : '' }}>Instansi Pemda (APBD)</option>
                        <option value="BUMN" {{ old('jenis_klien', $pelanggan->jenis_klien) == 'BUMN' ? 'selected' : '' }}>BUMN / BUMD</option>
                        <option value="Agensi" {{ old('jenis_klien', $pelanggan->jenis_klien) == 'Agensi' ? 'selected' : '' }}>Biro Iklan (Agensi Periklanan)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Instansi / Perusahaan / Perorangan <span class="text-red-500">*</span></label>
                <input type="text" name="nama_instansi" value="{{ old('nama_instansi', $pelanggan->nama_instansi) }}" required 
                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">NPWP / NIK (Untuk e-Faktur Pajak)</label>
                    <input type="text" name="npwp_nik" value="{{ old('npwp_nik', $pelanggan->npwp_nik) }}" 
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Credit Limit (Plafon Kredit)</label>
                    <input type="text" name="credit_limit" id="credit_limit" value="{{ number_format(old('credit_limit', $pelanggan->credit_limit), 0, ',', '.') }}" onkeyup="formatRupiahInput(this)"
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Kontak PIC</label>
                    <input type="text" name="kontak_pic" value="{{ old('kontak_pic', $pelanggan->kontak_pic) }}" 
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">No. Telp / WhatsApp</label>
                    <input type="text" name="no_telp" value="{{ old('no_telp', $pelanggan->no_telp) }}" 
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $pelanggan->email) }}" 
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Lengkap</label>
                <textarea name="alamat" rows="3" 
                          class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">{{ old('alamat', $pelanggan->alamat) }}</textarea>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $pelanggan->is_active) ? 'checked' : '' }}
                       class="rounded border-slate-300 text-kp-blue-600 focus:ring-kp-blue-500">
                <label for="is_active" class="text-xs font-medium text-slate-700">Pelanggan Aktif (Bisa Memasang Iklan)</label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('pelanggan.index') }}" 
                   class="px-4 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-5 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    Perbarui Data
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
