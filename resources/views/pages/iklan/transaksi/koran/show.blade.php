<x-app-layout>
    <div class="space-y-5" x-data="{ batalModal: false }">

        @php
            if ($transaksi->status_order === 'Batal') {
                $statusBayarLabel = 'Dibatalkan';
                $statusBayarClass = 'bg-slate-100 text-slate-600 border-slate-300';
            } elseif ($transaksi->sisa_piutang <= 0) {
                $statusBayarLabel = 'Lunas';
                $statusBayarClass = 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200';
            } elseif (($transaksi->jumlahbayar_transaksikoran ?? 0) > 0) {
                $statusBayarLabel = 'Cicilan / DP';
                $statusBayarClass = 'bg-slate-100 text-slate-700 border-slate-300';
            } else {
                $statusBayarLabel = 'Belum Bayar';
                $statusBayarClass = 'bg-white text-slate-700 border-slate-300';
            }

            $pracetakLabel = $transaksi->status_order === 'Batal'
                ? 'Batal'
                : ($transaksi->cbp_approved ? 'Siap Terbit' : 'Tahan CBP');
            $pracetakClass = $transaksi->cbp_approved && $transaksi->status_order !== 'Batal'
                ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200'
                : 'bg-slate-100 text-slate-700 border-slate-300';
        @endphp

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight font-mono">
                            {{ $transaksi->nofakturkoran }}
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold border {{ $statusBayarClass }} shrink-0">
                            {{ $statusBayarLabel }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold border {{ $pracetakClass }} shrink-0">
                            {{ $pracetakLabel }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl">
                        Tanggal Order: {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksikoran)->format('d/m/Y') }} &bull; Petugas: {{ $transaksi->inputOleh->name ?? 'Front Office' }}
                    </p>
                </div>
            </div>

            <!-- Action Bar Atas (Seragam 60-30-10) -->
            <div class="flex items-center gap-2 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('transaksikoran.index') }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-3.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl shadow-2xs transition cursor-pointer whitespace-nowrap">
                    &larr; <span>Kembali</span>
                </a>

                @if($transaksi->canEdit())
                    <a href="{{ route('transaksikoran.edit', $transaksi->id) }}" 
                       class="inline-flex items-center gap-1.5 h-10 px-3.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl shadow-2xs transition cursor-pointer whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Ubah</span>
                    </a>
                @endif

                <a href="{{ route('transaksikoran.sppi', $transaksi->id) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-3.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl shadow-2xs transition whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Cetak SPPI</span>
                </a>

                <a href="{{ route('transaksikoran.cetak', $transaksi->id) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs font-bold rounded-xl transition whitespace-nowrap shadow-xs">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Faktur</span>
                </a>

                @if($transaksi->status_order !== 'Batal')
                    <button type="button" 
                            @click="batalModal = true"
                            class="inline-flex items-center gap-1.5 h-10 px-3.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Batalkan</span>
                    </button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 font-semibold rounded-xl text-xs flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($transaksi->status_order === 'Batal')
            <div class="p-4 bg-slate-100 border border-slate-300 rounded-xl text-slate-800">
                <div class="flex items-center gap-2 text-slate-900 font-bold text-sm">
                    <svg class="w-4 h-4 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Transaksi Dibatalkan</span>
                </div>
                <p class="text-xs text-slate-600 mt-1">
                    Alasan: <span class="font-medium text-slate-800">{{ $transaksi->alasan_batal ?: 'Tidak ada keterangan.' }}</span>
                    &bull; Oleh {{ $transaksi->dibatalkanOleh->name ?? 'Petugas' }} ({{ $transaksi->dibatalkan_at ? $transaksi->dibatalkan_at->format('d/m/Y H:i') : '-' }})
                </p>
            </div>
        @endif

        <!-- Tata Letak Utama 2 Kolom (8 Kiri : 4 Kanan) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

            <!-- Kolom Kiri (8 Kolom): Informasi Pemesan, Spesifikasi Iklan, & Jadwal Terbit -->
            <div class="lg:col-span-8 space-y-5">

                <!-- 1. Informasi Pemesan & Sales -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">
                            Informasi Pemesan &amp; Perolehan
                        </h2>
                        @if($transaksi->pelanggan?->jenis_klien)
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $transaksi->pelanggan->jenis_klien }}
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
                        <div>
                            <span class="block text-xs font-medium text-slate-500">Nama Pemesan</span>
                            <p class="text-sm font-semibold text-slate-900 mt-0.5">{{ $transaksi->nama_pemasangkoran }}</p>
                        </div>

                        <div>
                            <span class="block text-xs font-medium text-slate-500">Alamat</span>
                            <p class="text-sm font-semibold text-slate-900 mt-0.5">{{ $transaksi->alamat_pemasangkoran ?: '-' }}</p>
                        </div>

                        <div>
                            <span class="block text-xs font-medium text-slate-500">Sales / Account Executive</span>
                            <p class="text-sm font-semibold text-slate-900 mt-0.5">
                                {{ $transaksi->salesAe?->nama_ae ?: ($transaksi->sales_iklankoran ?: '-') }}
                            </p>
                            @if(($transaksi->insentif_transaksikoran ?? 0) > 0 || ($transaksi->insentif_persen ?? 0) > 0)
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Insentif {{ $transaksi->insentif_persen ?? 0 }}% &bull; <span class="font-mono font-semibold text-slate-700">Rp {{ number_format($transaksi->insentif_transaksikoran ?? 0, 0, ',', '.') }}</span>
                                </p>
                            @endif
                        </div>

                        <div>
                            <span class="block text-xs font-medium text-slate-500">Perantara / Komisioner</span>
                            @if($transaksi->komisioner || ($transaksi->komisi_transaksikoran ?? 0) > 0 || ($transaksi->komisi_persen ?? 0) > 0)
                                <p class="text-sm font-semibold text-slate-900 mt-0.5">{{ $transaksi->komisioner ?: 'Komisioner' }}</p>
                                @if(($transaksi->komisi_transaksikoran ?? 0) > 0 || ($transaksi->komisi_persen ?? 0) > 0)
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Komisi {{ $transaksi->komisi_persen ?? 0 }}% &bull; <span class="font-mono font-semibold text-slate-700">Rp {{ number_format($transaksi->komisi_transaksikoran ?? 0, 0, ',', '.') }}</span>
                                    </p>
                                @endif
                            @else
                                <p class="text-sm font-semibold text-slate-400 mt-0.5">-</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 2. Spesifikasi Teknis & Materi Iklan -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">
                            Spesifikasi &amp; Materi Iklan
                        </h2>
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            {{ $transaksi->kategori_form ?: 'KORAN' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <span class="block text-xs font-medium text-slate-500">Jenis Produk</span>
                            <p class="text-sm font-semibold text-slate-900 mt-0.5">
                                {{ $transaksi->iklankoran?->nama_jenis ?: ($transaksi->kategori_form ?: '-') }}
                            </p>
                        </div>

                        <div>
                            <span class="block text-xs font-medium text-slate-500">Ukuran / Dimensi</span>
                            <p class="text-sm font-semibold text-slate-900 mt-0.5">
                                @if($transaksi->kategori_form === 'BARIS')
                                    {{ $transaksi->jumlah_baris }} Baris ({{ $transaksi->kopIklan->nama_kop ?? 'Umum' }})
                                @else
                                    {{ $transaksi->lebar_kolom }} Kolom &times; {{ $transaksi->tinggi_mm }} mm ({{ $transaksi->lebar_kolom * $transaksi->tinggi_mm }} mmk)
                                @endif
                            </p>
                        </div>

                        <div>
                            <span class="block text-xs font-medium text-slate-500">Warna Cetak</span>
                            <p class="text-sm font-semibold text-slate-900 mt-0.5">{{ $transaksi->warna_iklan ?: '-' }}</p>
                        </div>

                        <div>
                            <span class="block text-xs font-medium text-slate-500">Penempatan Halaman</span>
                            <p class="text-sm font-semibold text-slate-900 mt-0.5">
                                {{ $transaksi->kopKota->nama_penempatan ?? $transaksi->halaman_iklan ?? 'Halaman Dalam' }}
                            </p>
                        </div>
                    </div>

                    @if($transaksi->kategori_form === 'BARIS' || !empty($transaksi->naskah_iklan_baris))
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <span class="block text-xs font-medium text-slate-500 mb-1.5">Naskah Iklan Baris</span>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 font-mono text-sm text-slate-900 leading-relaxed whitespace-pre-wrap select-all">{{ $transaksi->naskah_iklan_baris ?: '-' }}</div>
                        </div>
                    @endif

                    <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="block text-xs font-medium text-slate-500">Status Materi Pracetak</span>
                            <p class="text-sm font-semibold text-slate-900 mt-0.5">
                                {{ str_replace('_', ' ', $transaksi->status_materi ?: 'Menunggu Materi') }}
                            </p>
                        </div>

                        @if($transaksi->file_materi)
                            <a href="{{ Storage::url($transaksi->file_materi) }}" 
                               target="_blank"
                               class="inline-flex items-center gap-1.5 h-9 px-3.5 bg-kp-blue-50 hover:bg-kp-blue-100 border border-kp-blue-200 text-kp-blue-700 text-xs font-semibold rounded-lg transition">
                                <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Unduh File Desain</span>
                            </a>
                        @else
                            <span class="text-xs font-medium text-slate-400">Belum ada file lampiran</span>
                        @endif
                    </div>
                </div>

                <!-- 3. Jadwal Terbit -->
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900">
                            Jadwal Terbit Koran
                        </h2>
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $transaksi->total_muatkoran }} Edisi
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-xs whitespace-nowrap">
                                <tr>
                                    <th class="py-2.5 px-4">Edisi</th>
                                    <th class="py-2.5 px-4">Tanggal Terbit</th>
                                    <th class="py-2.5 px-4">Hari</th>
                                    <th class="py-2.5 px-4 text-center">Status Terbit</th>
                                    <th class="py-2.5 px-4">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 text-sm">
                                @forelse($transaksi->jadwalMuat as $jadwal)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 px-4 font-semibold text-slate-900">Edisi #{{ $jadwal->edisi_ke }}</td>
                                        <td class="py-3 px-4 font-mono text-slate-800">
                                            {{ \Carbon\Carbon::parse($jadwal->tanggal_muat)->format('d/m/Y') }}
                                        </td>
                                        <td class="py-3 px-4 text-slate-600">
                                            {{ \Carbon\Carbon::parse($jadwal->tanggal_muat)->translatedFormat('l') }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @php
                                                $statusMap = [
                                                    'TERJADWAL'    => ['label' => 'Terjadwal',    'class' => 'bg-slate-100 text-slate-700 border-slate-300'],
                                                    'SUDAH_TAYANG' => ['label' => 'Sudah Terbit', 'class' => 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200'],
                                                    'RESCHEDULE'   => ['label' => 'Jadwal Ulang', 'class' => 'bg-slate-100 text-slate-700 border-slate-300'],
                                                    'BATAL'        => ['label' => 'Batal',        'class' => 'bg-slate-100 text-slate-500 border-slate-200'],
                                                ];
                                                $st = $statusMap[$jadwal->status_tayang] ?? ['label' => $jadwal->status_tayang, 'class' => 'bg-slate-100 text-slate-700 border-slate-200'];
                                            @endphp
                                            <span class="inline-flex px-2.5 py-0.5 rounded-lg text-xs font-semibold border {{ $st['class'] }}">
                                                {{ $st['label'] }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-slate-500 text-xs">
                                            {{ $jadwal->catatan ?: '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-xs text-slate-400">
                                            Belum ada jadwal terbit yang terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Kolom Kanan (4 Kolom): Ringkasan Biaya & Riwayat Pembayaran -->
            <div class="lg:col-span-4 space-y-5">

                <!-- 1. Rincian Biaya -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">
                            Rincian Tagihan
                        </h2>
                        <span class="text-xs font-medium text-slate-500">{{ $transaksi->total_muatkoran }}x Terbit</span>
                    </div>

                    <div class="space-y-2.5 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Tarif Satuan</span>
                            <span class="font-mono font-medium text-slate-800">Rp {{ number_format($transaksi->harga_transaksikoran, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Nilai Brutto</span>
                            <span class="font-mono font-semibold text-slate-900">Rp {{ number_format($transaksi->nilai_brutto, 0, ',', '.') }}</span>
                        </div>

                        @if($transaksi->nilai_diskon > 0)
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Reduksi / Diskon{{ ($transaksi->diskon_persen ?? 0) > 0 ? ' (' . $transaksi->diskon_persen . '%)' : '' }}</span>
                                <span class="font-mono font-medium text-slate-700">- Rp {{ number_format($transaksi->nilai_diskon, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between">
                            <span class="font-medium text-slate-700">Nilai Netto (DPP)</span>
                            <span class="font-mono font-semibold text-slate-900">Rp {{ number_format($transaksi->nilai_netto, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">PPN ({{ $transaksi->ppn_persen ?? \App\Models\SistemConfig::getPpnPersen() }}%)</span>
                            <span class="font-mono font-medium text-slate-800">Rp {{ number_format($transaksi->nilai_ppn, 0, ',', '.') }}</span>
                        </div>

                        <!-- Highlight Total Tagihan (10% Accent) -->
                        <div class="my-3 p-3.5 bg-kp-blue-50/70 border border-kp-blue-200 rounded-xl flex items-center justify-between">
                            <span class="text-xs font-bold text-kp-blue-700 uppercase tracking-wider">Total Tagihan</span>
                            <span class="font-mono text-base font-bold text-kp-blue-700">
                                Rp {{ number_format($transaksi->jumlah_yang_dibayar, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Sudah Dibayar</span>
                            <span class="font-mono font-semibold text-slate-900">Rp {{ number_format($transaksi->jumlahbayar_transaksikoran, 0, ',', '.') }}</span>
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                            <span class="font-semibold text-slate-700">Sisa Piutang</span>
                            <span class="font-mono font-bold {{ $transaksi->sisa_piutang > 0 ? 'text-slate-900' : 'text-kp-blue-700' }}">
                                @if($transaksi->sisa_piutang > 0)
                                    Rp {{ number_format($transaksi->sisa_piutang, 0, ',', '.') }}
                                @else
                                    Rp 0 (Lunas)
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 2. Riwayat Pembayaran Kwitansi Kasir -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">
                            Riwayat Pembayaran
                        </h2>
                        <span class="text-xs font-medium text-slate-500">{{ $transaksi->pembayaranList->count() }} Kwitansi</span>
                    </div>

                    <div class="space-y-2.5">
                        @forelse($transaksi->pembayaranList as $bayar)
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-semibold text-kp-blue-700 font-mono">{{ $bayar->nokwitansi }}</span>
                                    <span class="font-bold text-slate-900 font-mono">Rp {{ number_format($bayar->nominal_bayar, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs text-slate-500">
                                    <span>{{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d/m/Y') }}</span>
                                    <span class="font-medium text-slate-700">{{ str_replace('_', ' ', $bayar->metode_bayar) }}</span>
                                </div>
                                @if($bayar->kasirUser)
                                    <div class="text-xs text-slate-500 pt-1 border-t border-slate-200/80">
                                        Kasir: {{ $bayar->kasirUser->name }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="py-4 text-center text-xs text-slate-400">
                                Belum ada catatan kwitansi pembayaran.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

        <!-- Modal Dialog Pembatalan Order Transaksi -->
        <div x-cloak x-show="batalModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div @click.outside="batalModal = false" class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-200">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">
                        Konfirmasi Pembatalan Order
                    </h3>
                    <button type="button" @click="batalModal = false" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">
                        &times;
                    </button>
                </div>

                <form method="POST" action="{{ route('transaksikoran.batalkan', $transaksi->id) }}" class="p-6 space-y-4">
                    @csrf
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Order <span class="font-mono font-bold text-slate-900">{{ $transaksi->nofakturkoran }}</span> akan dibatalkan dan seluruh jadwal pemuatan yang belum tayang akan dihentikan.
                    </p>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alasan Pembatalan <span class="text-kp-blue-600">*</span></label>
                        <textarea name="alasan_batal" required rows="3" 
                                  placeholder="Contoh: Permintaan klien / Revisi jadwal..."
                                  class="w-full p-2.5 text-xs text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition"></textarea>
                    </div>

                    <div class="flex justify-end gap-2.5 pt-2 border-t border-slate-100">
                        <button type="button" @click="batalModal = false" class="px-4 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                            Tutup
                        </button>
                        <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition cursor-pointer">
                            Ya, Batalkan Order
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
