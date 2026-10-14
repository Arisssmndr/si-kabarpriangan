<x-app-layout>
    <div class="space-y-6">

        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('keuangan.anggaran.index') }}" class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition">
                    ←
                </a>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                        FORM PENGAJUAN ANGGARAN BARU
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Usulkan alokasi dana operasional untuk unit kerja divisi Kabar Priangan
                    </p>
                </div>
            </div>

            <div class="text-xs font-mono font-bold text-purple-700 bg-purple-50 px-3 py-1.5 rounded-xl border border-purple-200">
                SOP PENGELOLAAN ANGGARAN
            </div>
        </div>

        <div class="max-w-3xl mx-auto">
            <form action="{{ route('keuangan.anggaran.store') }}" method="POST" class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xs space-y-5">
                @csrf

                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                        Rincian Usulan Anggaran
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nomor Pengajuan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Pengajuan <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_pengajuan" value="{{ old('no_pengajuan', $noPengajuanSuggested) }}" required
                               class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 px-3 py-2.5 bg-slate-50 focus:ring-2 focus:ring-purple-500">
                    </div>

                    <!-- Divisi Pengusul -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Divisi / Unit Kerja <span class="text-rose-500">*</span></label>
                        <select name="divisi" required class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-purple-500 font-semibold text-slate-800">
                            <option value="">-- Pilih Pos Divisi --</option>
                            @foreach($daftarDivisi as $key => $label)
                                <option value="{{ $key }}" {{ old('divisi') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tanggal Pengajuan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pengajuan <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_pengajuan" value="{{ old('tanggal_pengajuan', date('Y-m-d')) }}" required
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-purple-500">
                    </div>

                    <!-- Nominal Diajukan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal yang Diajukan (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="nominal_diajukan" value="{{ old('nominal_diajukan') }}" min="1000" step="1000" required placeholder="Contoh: 2500000"
                               class="w-full text-sm font-mono font-black rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-purple-500 text-slate-900">
                    </div>
                </div>

                <!-- Keterangan Keperluan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Rincian Keperluan Penggunaan Dana <span class="text-rose-500">*</span></label>
                    <textarea name="keterangan" rows="4" required placeholder="Jelaskan kebutuhan pengajuan dana operasional secara spesifik (misal: pengadaan alat, biaya liputan investigasi, servis armada sirkulasi, dll)..."
                              class="w-full text-xs rounded-xl border border-slate-300 p-3 focus:ring-2 focus:ring-purple-500 leading-relaxed">{{ old('keterangan') }}</textarea>
                </div>

                <div class="p-4 rounded-xl bg-purple-50 border border-purple-200 text-xs text-purple-900 leading-relaxed">
                    💡 <strong>Alur Kerja Persetujuan:</strong> Setelah diajukan, pengajuan berstatus <em>Pending</em> dan akan ditinjau oleh Kepala Divisi / Admin Keuangan. Apabila disetujui, kasir dapat mencairkan dana dan sistem otomatis mencatat <strong>Kas Keluar</strong>.
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <a href="{{ route('keuangan.anggaran.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                        Kirim Pengajuan Anggaran ➔
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
