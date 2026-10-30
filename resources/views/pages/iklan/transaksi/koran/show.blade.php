<x-app-layout>
    <div class="space-y-6" x-data="{ batalModal: false }">

        <!-- Header Halaman Standar Selaras Foto 3 -->
        <!-- Header Halaman Standar Selaras Foto 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight font-mono">
                            {{ $transaksi->nofakturkoran }}
                        </h1>
                        @php $badge = $transaksi->status_badge; @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badge['border'] ?? 'border-slate-200' }} {{ $badge['bg'] ?? 'bg-slate-50' }} {{ $badge['text'] ?? 'text-slate-700' }} shrink-0">
                            {{ $badge['label'] ?? $transaksi->status_order }}
                        </span>
                        @if($transaksi->status_order === 'Batal')
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 shrink-0">
                                Dibatalkan
                            </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl">
                        Tgl Order: {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksikoran)->format('d/m/Y') }} • Petugas: {{ $transaksi->inputOleh->name ?? 'Front Office' }}
                    </p>
                </div>
            </div>

            <!-- Action Bar Atas (Compact, Anti-Numpuk) -->
            <div class="flex items-center gap-2 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('transaksikoran.index') }}" 
                   class="inline-flex items-center gap-1.5 h-9.5 px-3 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    &larr; <span>Kembali</span>
                </a>

                <a href="{{ route('transaksikoran.edit', $transaksi->id) }}" 
                   class="inline-flex items-center gap-1.5 h-9.5 px-3 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Ubah</span>
                </a>

                <a href="{{ route('transaksikoran.lembar-kerja', $transaksi->id) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-9.5 px-3 bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-900 text-xs font-bold rounded-xl transition whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 text-amber-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Pracetak</span>
                </a>

                <a href="{{ route('transaksikoran.sppi', $transaksi->id) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-9.5 px-3 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-900 text-xs font-bold rounded-xl transition whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 text-indigo-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>SPPI</span>
                </a>

                <a href="{{ route('transaksikoran.cetak', $transaksi->id) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-9.5 px-3.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs font-bold rounded-xl transition whitespace-nowrap shadow-xs">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Faktur</span>
                </a>

                @if($transaksi->status_order !== 'Batal')
                    <button type="button" 
                            @click="batalModal = true"
                            class="inline-flex items-center gap-1.5 h-9.5 px-3 bg-rose-50 hover:bg-rose-100 border border-rose-300 text-rose-800 text-xs font-bold rounded-xl transition cursor-pointer whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Batal</span>
                    </button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-300 text-black font-bold rounded-xl text-xs flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($transaksi->status_order === 'Batal')
            <div class="p-4 bg-rose-50 border-2 border-rose-300 rounded-xl text-rose-900">
                <div class="flex items-center gap-2 text-rose-900 font-bold text-sm">
                    <svg class="w-5 h-5 text-rose-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>TRANSAKSI TELAH DIBATALKAN</span>
                </div>
                <p class="text-xs font-semibold text-rose-800 mt-1">
                    Alasan: {{ $transaksi->alasan_batal ?: 'Tidak ada keterangan alasan.' }}
                </p>
                <p class="text-xs font-semibold text-rose-700 mt-0.5">
                    Dibatalkan pada: {{ $transaksi->dibatalkan_at ? $transaksi->dibatalkan_at->format('d/m/Y H:i') : '-' }} oleh {{ $transaksi->dibatalkanOleh->name ?? 'Petugas' }}
                </p>
            </div>
        @endif

        <!-- Grid 2 Kolom: Detail Pesanan & Ringkasan Finansial -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Kolom Kiri (8 Kolom): Spesifikasi Iklan, Materi, & Jadwal Muat -->
            <div class="lg:col-span-8 space-y-6">

                <!-- 1. Identitas Klien / Pengiklan -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                    <h2 class="text-xs font-semibold text-slate-900 border-b border-slate-100 pb-2 mb-4">
                        Data Pemesan &amp; Sales
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="block font-medium text-slate-500">Nama Pemesan</span>
                            <span class="text-sm font-semibold text-slate-900 block mt-0.5">{{ $transaksi->nama_pemasangkoran }}</span>
                            @if($transaksi->pelanggan)
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $transaksi->pelanggan->kode_pelanggan }} • {{ $transaksi->pelanggan->jenis_klien }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <span class="block font-medium text-slate-500">Alamat</span>
                            <span class="font-medium text-slate-800 block mt-0.5">{{ $transaksi->alamat_pemasangkoran ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="block font-medium text-slate-500">Sales / AE</span>
                            <span class="font-semibold text-slate-900 block mt-0.5">
                                {{ $transaksi->salesAe ? "[{$transaksi->salesAe->kode_ae}] {$transaksi->salesAe->nama_ae}" : $transaksi->sales_iklankoran }}
                            </span>
                            @if(($transaksi->insentif_transaksikoran ?? 0) > 0 || ($transaksi->insentif_persen ?? 0) > 0)
                                <span class="inline-block mt-1 text-xs text-kp-blue-700 font-semibold bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                    Insentif: {{ $transaksi->insentif_persen ?? 0 }}% (<span class="font-mono">Rp {{ number_format($transaksi->insentif_transaksikoran ?? 0, 0, ',', '.') }}</span>)
                                </span>
                            @endif
                        </div>
                        <div>
                            <span class="block font-medium text-slate-500">Perantara / Komisioner</span>
                            @if($transaksi->komisioner)
                                <span class="font-semibold text-slate-900 block mt-0.5">
                                    {{ $transaksi->komisioner }}
                                </span>
                                <span class="inline-block mt-1 text-xs text-amber-800 font-semibold bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                    Komisi: {{ $transaksi->komisi_persen ?? 0 }}% (<span class="font-mono">Rp {{ number_format($transaksi->komisi_transaksikoran ?? 0, 0, ',', '.') }}</span>)
                                </span>
                            @else
                                <span class="text-slate-400 italic block mt-0.5">Tanpa perantara (Pemasangan Langsung)</span>
                            @endif
                        </div>
                        <div class="sm:col-span-2">
                            <span class="block font-medium text-slate-500">Penempatan Halaman</span>
                            <span class="font-semibold text-slate-900 block mt-0.5">
                                {{ $transaksi->kopKota->nama_penempatan ?? $transaksi->halaman_iklan ?? 'Halaman Dalam' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 2. Spesifikasi Teknis Produk & Materi Cetak -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                    <h2 class="text-xs font-semibold text-slate-900 border-b border-slate-100 pb-2 mb-4">
                        Spesifikasi Iklan
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs mb-4">
                        <div>
                            <span class="block font-medium text-slate-500">Kategori Iklan</span>
                            <span class="font-medium text-slate-800 block mt-0.5">{{ $transaksi->kategori_form }}</span>
                        </div>
                        <div>
                            <span class="block font-medium text-slate-500">Warna Cetak</span>
                            <span class="font-medium text-slate-800 block mt-0.5">{{ $transaksi->warna_iklan }}</span>
                        </div>
                        <div>
                            <span class="block font-medium text-slate-500">Dimensi / Ukuran</span>
                            <span class="font-normal text-slate-800 block mt-0.5">
                                @if($transaksi->kategori_form === 'BARIS')
                                    {{ $transaksi->jumlah_baris }} Baris (Rubrik: {{ $transaksi->kopIklan->nama_kop ?? 'Aneka' }})
                                @else
                                    {{ $transaksi->lebar_kolom }} Kolom × {{ $transaksi->tinggi_mm }} mm ({{ ($transaksi->lebar_kolom * $transaksi->tinggi_mm) }} mmk)
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Naskah Iklan Baris jika kategori BARIS -->
                    @if($transaksi->kategori_form === 'BARIS' || !empty($transaksi->naskah_iklan_baris))
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <span class="block font-medium text-slate-700 mb-1.5 text-xs">Naskah Iklan Baris:</span>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 font-mono text-xs text-slate-900 leading-relaxed whitespace-pre-wrap select-all">
                                {{ $transaksi->naskah_iklan_baris ?: '-' }}
                            </div>
                        </div>
                    @endif

                    <!-- File Desain / Materi Cetak jika Display/Kolom -->
                    <div class="mt-4 pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="block font-medium text-slate-700 text-xs">File Desain Pracetak:</span>
                            <span class="text-xs font-medium text-slate-500 mt-0.5 block">
                                Status: <span class="font-semibold {{ $transaksi->status_materi === 'Siap_Cetak' ? 'text-emerald-700' : 'text-amber-700' }}">{{ $transaksi->status_materi }}</span>
                            </span>
                        </div>
                        @if($transaksi->file_materi)
                            <a href="{{ Storage::url($transaksi->file_materi) }}" 
                               target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-kp-blue-50 hover:bg-kp-blue-100 border border-kp-blue-200 text-kp-blue-700 text-xs font-semibold rounded-lg transition shadow-2xs">
                                <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Lihat File Desain</span>
                            </a>
                        @else
                            <span class="text-xs text-slate-400 italic">Belum ada lampiran file materi.</span>
                        @endif
                    </div>
                </div>

                <!-- 3. Jadwal Muat (Multi-Date Scheduling) -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                        <h2 class="text-xs font-semibold text-slate-900">
                            Jadwal Terbit ({{ $transaksi->total_muatkoran }} Edisi)
                        </h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-600 uppercase tracking-wider text-xs">
                                <tr>
                                    <th class="py-2.5 px-3 font-semibold">Edisi</th>
                                    <th class="py-2.5 px-3 font-semibold">Tanggal Terbit</th>
                                    <th class="py-2.5 px-3 font-semibold">Hari</th>
                                    <th class="py-2.5 px-3 font-semibold">Status Terbit</th>
                                    <th class="py-2.5 px-3 font-semibold">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($transaksi->jadwalMuat as $jadwal)
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-2.5 px-3 font-medium text-slate-800">Edisi #{{ $jadwal->edisi_ke }}</td>
                                        <td class="py-2.5 px-3 font-normal text-slate-700">
                                            {{ \Carbon\Carbon::parse($jadwal->tanggal_muat)->format('d/m/Y') }}
                                        </td>
                                        <td class="py-2.5 px-3 font-normal text-slate-600">
                                            {{ \Carbon\Carbon::parse($jadwal->tanggal_muat)->translatedFormat('l') }}
                                        </td>
                                        <td class="py-2.5 px-3">
                                            @php
                                                $statusMap = [
                                                    'TERJADWAL'     => ['label' => 'Terjadwal', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
                                                    'SUDAH_TAYANG'  => ['label' => 'Sudah Terbit', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                                    'RESCHEDULE'    => ['label' => 'Jadwal Ulang', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
                                                    'BATAL'         => ['label' => 'Batal', 'class' => 'bg-rose-50 text-rose-700 border-rose-200'],
                                                ];
                                                $st = $statusMap[$jadwal->status_tayang] ?? ['label' => $jadwal->status_tayang, 'class' => 'bg-slate-100 text-slate-700 border-slate-200'];
                                            @endphp
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold border {{ $st['class'] }}">
                                                {{ $st['label'] }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 text-slate-600">
                                            {{ $jadwal->catatan ?: '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-xs text-slate-400">
                                            Belum ada jadwal terbit yang dipilih.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Kolom Kanan (4 Kolom): Kalkulasi Finansial & Riwayat Setoran -->
            <div class="lg:col-span-4 space-y-6">

                <!-- 1. Rincian Finansial Transparan -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                    <h2 class="text-xs font-semibold text-slate-900 border-b border-slate-100 pb-2 mb-4">
                        Rincian Biaya
                    </h2>

                    <div class="space-y-2.5 text-xs sm:text-sm">
                        <table class="w-full border-collapse" style="table-layout: fixed;">
                            <tbody>
                                <tr>
                                    <td style="width: 110px; padding: 3px 0;" class="text-slate-600 font-medium whitespace-nowrap">Tarif Satuan</td>
                                    <td style="width: 14px; text-align: center; padding: 3px 0;" class="text-slate-400 font-bold">:</td>
                                    <td style="padding: 3px 0;" class="font-normal text-slate-700 font-mono text-right">Rp {{ number_format($transaksi->harga_transaksikoran, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px 0;" class="text-slate-600 font-medium whitespace-nowrap">Ukuran</td>
                                    <td style="text-align: center; padding: 3px 0;" class="text-slate-400 font-bold">:</td>
                                    <td style="padding: 3px 0;" class="font-normal text-slate-700 text-right">{{ $transaksi->ukuran_iklan ?: ($transaksi->jumlah_baris ? $transaksi->jumlah_baris . ' Baris' : '-') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px 0;" class="text-slate-600 font-medium whitespace-nowrap">Frekuensi</td>
                                    <td style="text-align: center; padding: 3px 0;" class="text-slate-400 font-bold">:</td>
                                    <td style="padding: 3px 0;" class="font-normal text-slate-700 text-right">{{ $transaksi->total_muatkoran }} Edisi</td>
                                </tr>
                                <tr class="border-t border-slate-200">
                                    <td style="padding: 6px 0 3px 0;" class="text-slate-800 font-bold whitespace-nowrap">Brutto</td>
                                    <td style="text-align: center; padding: 6px 0 3px 0;" class="text-slate-400 font-bold">:</td>
                                    <td style="padding: 6px 0 3px 0;" class="font-bold text-slate-900 font-mono text-right">Rp {{ number_format($transaksi->nilai_brutto, 0, ',', '.') }}</td>
                                </tr>
                                @if($transaksi->nilai_diskon > 0)
                                    <tr class="text-rose-700">
                                        <td style="padding: 3px 0;" class="font-medium whitespace-nowrap">Diskon{{ ($transaksi->diskon_persen ?? 0) > 0 ? ' (' . $transaksi->diskon_persen . '%)' : '' }}</td>
                                        <td style="text-align: center; padding: 3px 0;" class="text-rose-400 font-bold">:</td>
                                        <td style="padding: 3px 0;" class="font-normal font-mono text-right">- Rp {{ number_format($transaksi->nilai_diskon, 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                        <div class="pt-1.5 border-t border-slate-200">
                            <table class="w-full border-collapse" style="table-layout: fixed;">
                                <tbody>
                                    <tr>
                                        <td style="width: 110px; padding: 3px 0;" class="text-slate-800 font-semibold whitespace-nowrap">Netto (DPP)</td>
                                        <td style="width: 14px; text-align: center; padding: 3px 0;" class="text-slate-400 font-bold">:</td>
                                        <td style="padding: 3px 0;" class="font-semibold text-slate-800 font-mono text-right">Rp {{ number_format($transaksi->nilai_netto, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 3px 0;" class="text-slate-600 font-medium whitespace-nowrap">PPN ({{ $transaksi->ppn_persen ?? \App\Models\SistemConfig::getPpnPersen() }}%)</td>
                                        <td style="text-align: center; padding: 3px 0;" class="text-slate-400 font-bold">:</td>
                                        <td style="padding: 3px 0;" class="font-normal text-slate-700 font-mono text-right">Rp {{ number_format($transaksi->nilai_ppn, 0, ',', '.') }}</td>
                                    </tr>
                                    @if($transaksi->nilai_materai > 0)
                                        <tr>
                                            <td style="padding: 3px 0;" class="text-slate-600 font-medium whitespace-nowrap">Meterai</td>
                                            <td style="text-align: center; padding: 3px 0;" class="text-slate-400 font-bold">:</td>
                                            <td style="padding: 3px 0;" class="font-normal text-slate-700 font-mono text-right">Rp {{ number_format($transaksi->nilai_materai, 0, ',', '.') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="pt-2 border-t-2 border-slate-800">
                            <table class="w-full border-collapse" style="table-layout: fixed;">
                                <tbody>
                                    <tr>
                                        <td style="width: 110px; padding: 4px 0;" class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wide whitespace-nowrap">Total Tagihan</td>
                                        <td style="width: 14px; text-align: center; padding: 4px 0;" class="text-slate-400 font-bold">:</td>
                                        <td style="padding: 4px 0;" class="font-bold text-kp-blue-700 font-mono text-base sm:text-lg text-right">Rp {{ number_format($transaksi->jumlah_yang_dibayar, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="pt-1.5 border-t border-slate-100">
                            <table class="w-full border-collapse" style="table-layout: fixed;">
                                <tbody>
                                    <tr>
                                        <td style="width: 110px; padding: 3px 0;" class="text-slate-600 font-medium whitespace-nowrap">Sudah Dibayar</td>
                                        <td style="width: 14px; text-align: center; padding: 3px 0;" class="text-slate-400 font-bold">:</td>
                                        <td style="padding: 3px 0;" class="font-medium text-emerald-700 font-mono text-right">Rp {{ number_format($transaksi->jumlahbayar_transaksikoran, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 3px 0;" class="text-slate-700 font-semibold whitespace-nowrap">Sisa Piutang</td>
                                        <td style="text-align: center; padding: 3px 0;" class="text-slate-400 font-bold">:</td>
                                        <td style="padding: 3px 0;" class="font-bold font-mono text-right {{ $transaksi->sisa_piutang > 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                                            @if($transaksi->sisa_piutang > 0)
                                                Rp {{ number_format($transaksi->sisa_piutang, 0, ',', '.') }}
                                            @else
                                                LUNAS
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Ketentuan CBP (Bayar Sebelum Cetak) -->
                    <div class="mt-5 p-3 rounded-xl border {{ $transaksi->isCbpLocked() ? 'bg-amber-50/70 border-amber-200' : 'bg-emerald-50/70 border-emerald-200' }}">
                        <span class="text-xs font-bold uppercase tracking-wider block {{ $transaksi->isCbpLocked() ? 'text-amber-800' : 'text-emerald-800' }}">
                            Status Bayar Sebelum Cetak (CBP):
                        </span>
                        <p class="text-xs mt-0.5 {{ $transaksi->isCbpLocked() ? 'text-amber-900' : 'text-emerald-900' }}">
                            @if($transaksi->isCbpLocked())
                                Menunggu Pembayaran — Perlu pelunasan atau persetujuan kredit sebelum masuk cetak.
                            @else
                                Siap Terbit — Syarat pembayaran sudah terpenuhi.
                            @endif
                        </p>
                    </div>
                </div>

                <!-- 2. Riwayat Pembayaran Kwitansi Kasir -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                    <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 mb-3">
                        Riwayat Pembayaran
                    </h2>

                    <div class="space-y-3">
                        @forelse($transaksi->pembayaranList as $bayar)
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-slate-900 font-mono">{{ $bayar->nokwitansi }}</span>
                                    <span class="font-bold text-emerald-700 font-mono">Rp {{ number_format($bayar->nominal_bayar, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs text-slate-500">
                                    <span>{{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d/m/Y') }}</span>
                                    <span class="font-medium text-slate-700">{{ $bayar->metode_bayar }}</span>
                                </div>
                                @if($bayar->kasirUser)
                                    <div class="text-xs text-slate-500 pt-1 border-t border-slate-200">
                                        Kasir: {{ $bayar->kasirUser->name }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-2">
                                Belum ada catatan kwitansi pembayaran.
                            </p>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

        <!-- Modal Dialog Pembatalan Order Transaksi -->
        <div x-cloak x-show="batalModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="batalModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <h3 class="text-base font-bold text-black mb-1">
                    Konfirmasi Pembatalan Order
                </h3>
                <p class="text-xs font-semibold text-black mb-4">
                    Order <span class="font-mono font-bold">{{ $transaksi->nofakturkoran }}</span> akan dibatalkan secara permanen pada sistem. Semua jadwal pemuatan akan otomatis dihentikan.
                </p>

                <form method="POST" action="{{ route('transaksikoran.batalkan', $transaksi->id) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-black mb-1">Alasan Pembatalan (Wajib):</label>
                        <textarea name="alasan_batal" required rows="3" 
                                  placeholder="Contoh: Permintaan klien dibatalkan via WA / Kendala materi desain..."
                                  class="w-full p-2.5 text-xs font-medium text-black bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-rose-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="batalModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-black text-xs font-bold rounded-xl transition">
                            Tutup
                        </button>
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition">
                            Ya, Batalkan Order
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
