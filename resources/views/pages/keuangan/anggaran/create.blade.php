<x-app-layout>
    <div class="space-y-6">

        <!-- Header Entry BP Priangan Sesuai DOS ANGGARAN.EXE -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('keuangan.anggaran.index') }}" class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition cursor-pointer">
                    ←
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                            ENTRY BP PRIANGAN (BUKTI PENGELUARAN ANGGARAN)
                        </h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pendaftaran dokumen BP operasional unit bidang yang terhubung ke alur persetujuan &amp; pencairan kas keluar
                    </p>
                </div>
            </div>
        </div>

        <div class="max-w-3xl mx-auto">
            <form action="{{ route('keuangan.anggaran.store') }}" method="POST" class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xs space-y-5">
                @csrf

                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                        Rincian Formulir BP Priangan
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nomor Pengajuan Dokumen -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Pengajuan Dokumen <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_pengajuan" value="{{ old('no_pengajuan', $noPengajuanSuggested) }}" required
                               class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 px-3 py-2.5 bg-slate-50 focus:ring-2 focus:ring-purple-500">
                    </div>

                    <!-- Nomor BP Priangan (Otomatis & Editable) -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700">Nomor BP Priangan (Sistem DOS) <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] font-semibold text-purple-700 bg-purple-50 px-1.5 py-0.2 rounded border border-purple-200">Auto / Bisa Diedit</span>
                        </div>
                        <input type="text" name="no_bp" value="{{ old('no_bp', $noBpSuggested ?? 'BP-' . date('Ym') . '-0001') }}" required
                               class="w-full text-xs font-mono font-bold rounded-xl border border-purple-300 px-3 py-2.5 bg-white text-purple-950 focus:ring-2 focus:ring-purple-500 shadow-2xs">
                        <span class="text-[10px] text-slate-400 mt-0.5 block">Format resmi DOS BP: BP-YYYYMM-XXXX (Otomatis / Bisa disesuaikan manual)</span>
                    </div>

                    <!-- Bidang Priangan (01 s/d 07) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Bidang Priangan (Kode Bagian) <span class="text-rose-500">*</span></label>
                        <select name="divisi" required class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-purple-500 font-semibold text-slate-800">
                            <option value="">-- Pilih Bidang Priangan --</option>
                            @foreach($daftarDivisi as $key => $label)
                                <option value="{{ $key }}" {{ old('divisi') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pemohon / Penerima -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pemohon / Penerima Dana <span class="text-rose-500">*</span></label>
                        <input type="text" name="pemohon" value="{{ old('pemohon', Auth::user()->name ?? '') }}" required placeholder="Nama staf / penanggungjawab..."
                               class="w-full text-xs font-bold rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-purple-500 text-slate-800">
                    </div>

                    <!-- Tanggal Pengajuan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pengajuan <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_pengajuan" value="{{ old('tanggal_pengajuan', date('Y-m-d')) }}" required
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-purple-500">
                    </div>

                    <!-- Nominal Diajukan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Usulan BP (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="nominal_diajukan" value="{{ old('nominal_diajukan') }}" min="1000" step="1000" required placeholder="Contoh: 1500000"
                               class="w-full text-sm font-mono font-black rounded-xl border border-slate-300 px-3 py-2.5 focus:ring-2 focus:ring-purple-500 text-slate-900">
                    </div>
                </div>

                <!-- Keterangan Keperluan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Rincian Keperluan Penggunaan Dana <span class="text-rose-500">*</span></label>
                    <textarea name="keterangan" rows="4" required placeholder="Jelaskan kebutuhan pengeluaran dana operasional (misal: biaya peliputan investigasi, pembelian perlengkapan cetak, servis kendaraan armada, konsumsi dinas, dll)..."
                              class="w-full text-xs rounded-xl border border-slate-300 p-3 focus:ring-2 focus:ring-purple-500 leading-relaxed">{{ old('keterangan') }}</textarea>
                </div>

                <div class="p-4 rounded-xl bg-purple-50 border border-purple-200 text-xs text-purple-900 leading-relaxed">
                    ⚡ <strong>Alur Integrasi Kas:</strong> Setelah dokumen BP ini disimpan, dokumen berstatus <em>Pending</em>. Saat disetujui oleh Manajer Keuangan, sistem akan otomatis menerbitkan <strong>Nomor Register Anggaran (PR-YYYYMMDD-XXXX)</strong> sehingga langsung dapat dicairkan oleh Kasir menjadi <strong>Bukti Kas Keluar (BKK KO-YYYYMMXXXX)</strong> dan memotong buku kas secara real-time.
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <a href="{{ route('keuangan.anggaran.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                        Simpan &amp; Terbitkan BP Priangan ➔
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
