<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman Standar Selaras Foto 3 & Iklan Online -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Ubah Jadwal &amp; Data Iklan Koran
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Faktur: <span class="font-mono text-kp-blue-700 font-bold">{{ $transaksi->nofakturkoran }}</span> • Kategori: {{ $transaksi->kategori_form }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('transaksikoran.show', $transaksi->id) }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    &larr; <span>Kembali ke Detail</span>
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-semibold">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('transaksikoran.update', $transaksi->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Kolom Kiri: Koreksi Informasi & Materi -->
                <div class="lg:col-span-7 space-y-6">

                    <!-- 1. Identitas Klien -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <h2 class="text-xs font-semibold text-slate-900 border-b border-slate-100 pb-2">
                            Data Pemesan &amp; Penempatan
                        </h2>

                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Nama Pemesan / Instansi <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_pemasangkoran" value="{{ old('nama_pemasangkoran', $transaksi->nama_pemasangkoran) }}" required
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 shadow-2xs">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Alamat</label>
                            <textarea name="alamat_pemasangkoran" rows="2"
                                      class="w-full p-3 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 shadow-2xs">{{ old('alamat_pemasangkoran', $transaksi->alamat_pemasangkoran) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Penempatan Halaman</label>
                            <select name="kop_kota_id" class="w-full h-10 px-3 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 shadow-2xs">
                                @foreach($kopKotas as $kop)
                                    <option value="{{ $kop->id }}" {{ $transaksi->kop_kota_id == $kop->id ? 'selected' : '' }}>
                                        {{ $kop->nama_penempatan }} @if($kop->surcharge_persen > 0) (+{{ number_format($kop->surcharge_persen, 2) }}%) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- 2. Sales & Komisioner -->
                    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <h2 class="text-xs font-semibold text-slate-900 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Sales &amp; Komisioner</span>
                            </h2>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <!-- Sales AE Internal -->
                            <div class="p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/90 space-y-2.5">
                                <label class="block text-xs font-medium text-slate-700">Sales / AE <span class="text-rose-500">*</span></label>
                                <select name="sales_ae_id" class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                    @foreach($salesAes as $ae)
                                        @php
                                            $roleLabel = match($ae->jenis_kode) {
                                                'FO' => 'Loket Kantor',
                                                'STRUKTURAL' => 'Account Executive',
                                                'KORESPONDEN' => 'Koresponden',
                                                'AGEN' => 'Keagenan',
                                                default => $ae->jenis_kode
                                            };
                                            $displayNama = ($ae->kode_ae === 'FO') 
                                                ? 'Front Office (Loket Langsung)' 
                                                : $ae->nama_ae . ' — ' . $roleLabel;
                                        @endphp
                                        <option value="{{ $ae->id }}" {{ $transaksi->sales_ae_id == $ae->id ? 'selected' : '' }}>
                                            {{ $displayNama }}
                                        </option>
                                    @endforeach
                                </select>

                                <div>
                                    <label class="block text-[11px] font-medium text-slate-600 mb-1">Insentif Sales</label>
                                    <div class="grid grid-cols-5 gap-2">
                                        <div class="col-span-2 relative">
                                            <input type="number" step="0.5" min="0" max="100" name="insentif_persen" id="edit_insentif_persen"
                                                   value="{{ old('insentif_persen', $transaksi->insentif_persen ?? 0) }}"
                                                   class="w-full h-8 px-2.5 pr-6 bg-white border border-slate-200 rounded-lg text-xs font-mono font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                            <span class="absolute right-2 top-1.5 text-xs text-slate-400 font-medium pointer-events-none">%</span>
                                        </div>
                                        <div class="col-span-3 relative">
                                            <span class="absolute left-2 top-1.5 text-xs text-slate-400 font-medium pointer-events-none">Rp</span>
                                            <input type="text" name="insentif_transaksikoran" id="edit_insentif_nominal"
                                                   value="{{ old('insentif_transaksikoran', number_format($transaksi->insentif_transaksikoran ?? 0, 0, ',', '.')) }}"
                                                   class="w-full h-8 pl-7 pr-2.5 bg-white border border-slate-200 rounded-lg text-xs font-mono font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Komisioner Pihak Ketiga -->
                            <div class="p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/90 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-medium text-slate-700">Perantara / Komisioner</label>
                                    <span class="text-[10px] text-slate-400 font-normal">Opsional</span>
                                </div>
                                <input type="text" name="komisioner" value="{{ old('komisioner', $transaksi->komisioner) }}"
                                       placeholder="Nama perantara (jika ada)"
                                       class="w-full h-9 px-3 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">

                                <div>
                                    <label class="block text-[11px] font-medium text-slate-600 mb-1">Komisi Perantara</label>
                                    <div class="grid grid-cols-5 gap-2">
                                        <div class="col-span-2 relative">
                                            <input type="number" step="0.5" min="0" max="100" name="komisi_persen" id="edit_komisi_persen"
                                                   value="{{ old('komisi_persen', $transaksi->komisi_persen ?? 0) }}"
                                                   class="w-full h-8 px-2.5 pr-6 bg-white border border-slate-200 rounded-lg text-xs font-mono font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                            <span class="absolute right-2 top-1.5 text-xs text-slate-400 font-medium pointer-events-none">%</span>
                                        </div>
                                        <div class="col-span-3 relative">
                                            <span class="absolute left-2 top-1.5 text-xs text-slate-400 font-medium pointer-events-none">Rp</span>
                                            <input type="text" name="komisi_transaksikoran" id="edit_komisi_nominal"
                                                   value="{{ old('komisi_transaksikoran', number_format($transaksi->komisi_transaksikoran ?? 0, 0, ',', '.')) }}"
                                                   class="w-full h-8 pl-7 pr-2.5 bg-white border border-slate-200 rounded-lg text-xs font-mono font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Koreksi Materi Teks & Desain -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <h2 class="text-xs font-semibold text-slate-900 border-b border-slate-100 pb-2">
                            Materi &amp; Desain
                        </h2>

                        @if($transaksi->kategori_form === 'BARIS')
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Rubrik Iklan</label>
                                <select name="kop_iklan_id" class="w-full h-10 px-3 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 shadow-2xs">
                                    @foreach($kopIklans as $kopI)
                                        <option value="{{ $kopI->id }}" {{ $transaksi->kop_iklan_id == $kopI->id ? 'selected' : '' }}>
                                            {{ $kopI->nama_kop }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Naskah Iklan Baris</label>
                                <textarea name="naskah_iklan_baris" rows="4"
                                          class="w-full p-3 font-mono bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 shadow-2xs">{{ old('naskah_iklan_baris', $transaksi->naskah_iklan_baris) }}</textarea>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Status Materi</label>
                                <select name="status_materi" class="w-full h-10 px-3 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 shadow-2xs">
                                    <option value="Siap_Cetak" {{ $transaksi->status_materi === 'Siap_Cetak' ? 'selected' : '' }}>Siap Cetak</option>
                                    <option value="Butuh_Setting" {{ $transaksi->status_materi === 'Butuh_Setting' ? 'selected' : '' }}>Butuh Setting Layout</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Status Order</label>
                                <select name="status_order" class="w-full h-10 px-3 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 shadow-2xs">
                                    <option value="Draft" {{ $transaksi->status_order === 'Draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="Menunggu_Bayar" {{ $transaksi->status_order === 'Menunggu_Bayar' ? 'selected' : '' }}>Menunggu Bayar</option>
                                    <option value="Antrean_Pracetak" {{ $transaksi->status_order === 'Antrean_Pracetak' ? 'selected' : '' }}>Antrean Pracetak</option>
                                    <option value="Terbit" {{ $transaksi->status_order === 'Terbit' ? 'selected' : '' }}>Terbit</option>
                                    <option value="Lunas" {{ $transaksi->status_order === 'Lunas' ? 'selected' : '' }}>Lunas</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">File Desain Revisi (Opsional)</label>
                            <input type="file" name="file_materi" accept=".pdf,.jpg,.jpeg,.png,.webp,.tiff"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 shadow-2xs">
                            @if($transaksi->file_materi)
                                <span class="text-[11px] text-slate-500 mt-1 block">
                                    File saat ini: <a href="{{ Storage::url($transaksi->file_materi) }}" target="_blank" class="text-kp-blue-700 underline font-medium">Lihat File</a>
                                </span>
                            @endif
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Catatan Khusus (Opsional)</label>
                            <textarea name="keterangan_materi" rows="2" placeholder="Catatan posisi atau edisi khusus..."
                                      class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 shadow-2xs">{{ old('keterangan_materi', $transaksi->keterangan_materi) }}</textarea>
                        </div>
                    </div>

                </div>

                <!-- Kolom Kanan: Reschedule Tanggal Muat per Edisi -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="border-b border-slate-100 pb-2">
                            <h2 class="text-xs font-semibold text-slate-900">
                                Jadwal Terbit
                            </h2>
                        </div>

                        <div class="space-y-4">
                            @foreach($transaksi->jadwalMuat as $jadwal)
                                <div class="p-3.5 bg-slate-50/70 border border-slate-200/90 rounded-xl space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="font-semibold text-slate-900 text-xs">Edisi #{{ $jadwal->edisi_ke }}</span>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-medium text-slate-700 mb-0.5">Tanggal Terbit:</label>
                                        <input type="date" name="jadwal[{{ $jadwal->id }}][tanggal_muat]" 
                                               value="{{ $jadwal->tanggal_muat->format('Y-m-d') }}" required
                                               class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-800 font-mono shadow-2xs">
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-medium text-slate-700 mb-0.5">Status Terbit:</label>
                                        <select name="jadwal[{{ $jadwal->id }}][status_tayang]"
                                                class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-800 shadow-2xs">
                                            <option value="TERJADWAL" {{ $jadwal->status_tayang === 'TERJADWAL' ? 'selected' : '' }}>Terjadwal</option>
                                            <option value="RESCHEDULE" {{ $jadwal->status_tayang === 'RESCHEDULE' ? 'selected' : '' }}>Jadwal Ulang</option>
                                            <option value="SUDAH_TAYANG" {{ $jadwal->status_tayang === 'SUDAH_TAYANG' ? 'selected' : '' }}>Sudah Terbit</option>
                                            <option value="BATAL" {{ $jadwal->status_tayang === 'BATAL' ? 'selected' : '' }}>Batal</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-medium text-slate-700 mb-0.5">Catatan:</label>
                                        <input type="text" name="jadwal[{{ $jadwal->id }}][catatan]" 
                                               value="{{ $jadwal->catatan }}" placeholder="Catatan edisi..."
                                               class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 shadow-2xs">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex justify-end gap-2.5">
                            <a href="{{ route('transaksikoran.show', $transaksi->id) }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                                Batal
                            </a>
                            <button type="submit" class="px-5 py-2.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition cursor-pointer">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>

    </div>
</x-app-layout>
