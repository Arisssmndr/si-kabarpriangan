<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Perubahan Mendadak (Quick Edit Jatah)</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-rose-50 text-rose-700 border border-rose-200">Revisi Ekstra</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Koreksi cepat kuota koran per agen jika ada penambahan mendadak sebelum percetakan menyelesaikan naik cetak.
                    </p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-medium flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="max-w-2xl bg-white border border-slate-200 rounded-xl p-6 shadow-xs">
            <form method="POST" action="{{ route('sirkulasi.jatah.quick-edit.update') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Distribusi</label>
                        <input type="date" name="tanggal_distribusi" value="{{ $targetTanggal }}" 
                               class="w-full text-xs rounded-lg border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Agen / Sub-Agen</label>
                        <select name="agen_id" class="w-full text-xs rounded-lg border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                            @foreach($agenList as $agen)
                                <option value="{{ $agen->id }}">{{ $agen->kode_agen }} - {{ $agen->nama_agen }} ({{ $agen->rute_nama }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Perubahan</label>
                        <select name="tipe_perubahan" class="w-full text-xs rounded-lg border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                            <option value="tambah">Penambahan Eksemplar (+)</option>
                            <option value="kurang">Pengurangan Eksemplar (-)</option>
                            <option value="set">Ganti Total Kuota Baru</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Eksemplar</label>
                        <input type="number" name="jumlah_perubahan" placeholder="Contoh: 50" min="1" 
                               class="w-full text-xs rounded-lg border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Revisi Mendadak</label>
                    <input type="text" name="alasan" placeholder="Contoh: Permintaan tambahan dari Kios Pasar Wetan karena liputan khusus Pilkada" 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="submit" class="px-4 py-2 text-xs font-bold rounded-lg bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition">
                        Simpan Revisi Kuota
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
