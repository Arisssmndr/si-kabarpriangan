<x-app-layout>
    <div class="space-y-5" x-data="{ batalModal: false }">

        @php
            $sisaPiutang = (int) ($transaksi->piutang_transaksipriangan ?? 0);
            $jumlahBayar = (int) ($transaksi->jumlahbayar_transaksipriangan ?? 0);
            $isBatal = ($transaksi->status_tayang ?? '') === 'batal';

            if ($isBatal) {
                $statusBayarLabel = 'Dibatalkan';
                $statusBayarClass = 'bg-slate-100 text-slate-600 border-slate-300';
            } elseif ($sisaPiutang <= 0) {
                $statusBayarLabel = 'Lunas';
                $statusBayarClass = 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200';
            } elseif ($jumlahBayar > 0) {
                $statusBayarLabel = 'Cicilan / DP';
                $statusBayarClass = 'bg-slate-100 text-slate-700 border-slate-300';
            } else {
                $statusBayarLabel = 'Belum Bayar';
                $statusBayarClass = 'bg-white text-slate-700 border-slate-300';
            }

            $statusTayangLabel = $isBatal ? 'Batal' : ucfirst($transaksi->status_tayang ?: 'Terjadwal');
            $statusTayangClass = ($transaksi->status_tayang ?? '') === 'tayang' && !$isBatal
                ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200'
                : 'bg-slate-100 text-slate-700 border-slate-300';

            $rawDates = $transaksi->tanggal_muat_list
                ? array_values(array_filter(array_map('trim', explode(',', $transaksi->tanggal_muat_list))))
                : ($transaksi->tanggal_muatiklanpriangan ? [\Carbon\Carbon::parse($transaksi->tanggal_muatiklanpriangan)->toDateString()] : []);
        @endphp

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight font-mono">
                            {{ $transaksi->nofakturpriangan }}
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold border {{ $statusBayarClass }} shrink-0">
                            {{ $statusBayarLabel }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold border {{ $statusTayangClass }} shrink-0">
                            {{ $statusTayangLabel }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate max-w-xl">
                        Tanggal Order: {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksipriangan)->format('d/m/Y') }} &bull; Priangan TV
                    </p>
                </div>
            </div>

            <!-- Action Bar Atas (Seragam 60-30-10) -->
            <div class="flex items-center gap-2 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('transaksipriangan.index') }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-3.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl shadow-2xs transition cursor-pointer whitespace-nowrap">
                    &larr; <span>Kembali</span>
                </a>

                <a href="{{ route('transaksipriangan.edit', $transaksi->id) }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-3.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl shadow-2xs transition cursor-pointer whitespace-nowrap">
                    <span>Ubah</span>
                </a>

                <a href="{{ route('transaksipriangan.sppi', $transaksi->id) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-3.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl shadow-2xs transition whitespace-nowrap">
                    <span>Cetak SPPI</span>
                </a>

                <a href="{{ route('transaksipriangan.cetak', $transaksi->id) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs font-bold rounded-xl transition whitespace-nowrap shadow-xs">
                    <span>Cetak Faktur</span>
                </a>

                @if(!$isBatal)
                    <button type="button" 
                            @click="batalModal = true"
                            class="inline-flex items-center gap-1.5 h-10 px-3.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer whitespace-nowrap">
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

        <!-- Tata Letak Utama 2 Kolom (8 Kiri : 4 Kanan) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

            <!-- Kolom Kiri (8 Kolom) -->
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
                            <p class="text-sm font-semibold text-slate-900 mt-0.5">{{ $transaksi->nama_pemasangpriangan }}</p>
                        </div>

                        <div>
                            <span class="block text-xs font-medium text-slate-500">Alamat</span>
                            <p class="text-sm font-semibold text-slate-900 mt-0.5">{{ $transaksi->alamat_pemasangpriangan ?: '-' }}</p>
                        </div>

                        <div>
                            <span class="block text-xs font-medium text-slate-500">Sales / Account Executive</span>
                            <p class="text-sm font-semibold text-slate-900 mt-0.5">
                                {{ $transaksi->salesAe?->nama_ae ?: ($transaksi->sales_iklanpriangan ?: '-') }}
                            </p>
                            @if(($transaksi->insentif_transaksipriangan ?? 0) > 0 || ($transaksi->insentif_persen ?? 0) > 0)
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Insentif {{ $transaksi->insentif_persen ?? 0 }}% &bull; <span class="font-mono font-semibold text-slate-700">Rp {{ number_format($transaksi->insentif_transaksipriangan ?? 0, 0, ',', '.') }}</span>
                                </p>
                            @endif
                        </div>

                        <div>
                            <span class="block text-xs font-medium text-slate-500">Perantara / Komisioner</span>
                            @if($transaksi->komisioner)
                                <p class="text-sm font-semibold text-slate-900 mt-0.5">{{ $transaksi->komisioner }}</p>
                                @if(($transaksi->komisi_transaksipriangan ?? 0) > 0 || ($transaksi->komisi_persen ?? 0) > 0)
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Komisi {{ $transaksi->komisi_persen ?? 0 }}% &bull; <span class="font-mono font-semibold text-slate-700">Rp {{ number_format($transaksi->komisi_transaksipriangan ?? 0, 0, ',', '.') }}</span>
                                    </p>
                                @endif
                            @else
                                <p class="text-sm font-semibold text-slate-400 mt-0.5">-</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 2. Spesifikasi Siaran Priangan TV -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">
                            Spesifikasi Siaran Priangan TV
                        </h2>
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            PRIANGAN TV
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <span class="block text-xs font-medium text-slate-500">Program / Jenis Iklan</span>
                            <p class="text-sm font-semibold text-slate-900 mt-0.5">
                                {{ $transaksi->iklanpriangan?->nama_jenis ?: 'Iklan Priangan TV' }}
                            </p>
                        </div>

                        <div>
                            <span class="block text-xs font-medium text-slate-500">Durasi Siar</span>
                            <p class="text-sm font-semibold text-slate-900 mt-0.5">
                                {{ $transaksi->durasi_detik ?: 30 }} Detik
                            </p>
                        </div>

                        <div>
                            <span class="block text-xs font-medium text-slate-500">Jam Tayang</span>
                            <p class="text-sm font-semibold text-slate-900 mt-0.5">
                                {{ $transaksi->jam_tayang ?: 'Prime Time' }}
                            </p>
                        </div>

                        <div>
                            <span class="block text-xs font-medium text-slate-500">Frekuensi Siar</span>
                            <p class="text-sm font-semibold text-slate-900 mt-0.5">
                                {{ $transaksi->total_muatiklanpriangan ?? 1 }}x Siar
                            </p>
                        </div>
                    </div>

                    @if($transaksi->materi_iklan)
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <span class="block text-xs font-medium text-slate-500 mb-1.5">Materi / Catatan Produksi</span>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-xs text-slate-800 leading-relaxed whitespace-pre-wrap">{{ $transaksi->materi_iklan }}</div>
                        </div>
                    @endif

                    @if($transaksi->file_materi)
                        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-end">
                            <a href="{{ Storage::url($transaksi->file_materi) }}" target="_blank" class="inline-flex items-center gap-1.5 h-9 px-3.5 bg-kp-blue-50 hover:bg-kp-blue-100 border border-kp-blue-200 text-kp-blue-700 text-xs font-semibold rounded-lg transition">
                                <span>Unduh File Materi Siar</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- 3. Jadwal Tayang -->
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900">
                            Jadwal Siar Priangan TV
                        </h2>
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ count($rawDates) }} Jadwal
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-xs whitespace-nowrap">
                                <tr>
                                    <th class="py-2.5 px-4">Siar Ke</th>
                                    <th class="py-2.5 px-4">Tanggal Siar</th>
                                    <th class="py-2.5 px-4">Hari</th>
                                    <th class="py-2.5 px-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 text-sm">
                                @forelse($rawDates as $idx => $tgl)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 px-4 font-semibold text-slate-900">Siar #{{ $idx + 1 }}</td>
                                        <td class="py-3 px-4 font-mono text-slate-800">
                                            {{ \Carbon\Carbon::parse($tgl)->format('d/m/Y') }}
                                        </td>
                                        <td class="py-3 px-4 text-slate-600">
                                            {{ \Carbon\Carbon::parse($tgl)->translatedFormat('l') }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="inline-flex px-2.5 py-0.5 rounded-lg text-xs font-semibold border {{ $statusTayangClass }}">
                                                {{ $statusTayangLabel }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-6 text-center text-xs text-slate-400">
                                            Belum ada jadwal siar yang terdaftar.
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
                        <span class="text-xs font-medium text-slate-500">{{ $transaksi->total_muatiklanpriangan ?? 1 }}x Siar</span>
                    </div>

                    <div class="space-y-2.5 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Tarif Satuan</span>
                            <span class="font-mono font-medium text-slate-800">Rp {{ number_format($transaksi->harga_transaksipriangan ?? 0, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Nilai Brutto</span>
                            <span class="font-mono font-semibold text-slate-900">Rp {{ number_format($transaksi->nilai_brutto, 0, ',', '.') }}</span>
                        </div>

                        @if($transaksi->nilai_diskon > 0)
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Reduksi / Diskon</span>
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
                            <span class="font-mono font-semibold text-slate-900">Rp {{ number_format($jumlahBayar, 0, ',', '.') }}</span>
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                            <span class="font-semibold text-slate-700">Sisa Piutang</span>
                            <span class="font-mono font-bold {{ $sisaPiutang > 0 ? 'text-slate-900' : 'text-kp-blue-700' }}">
                                @if($sisaPiutang > 0)
                                    Rp {{ number_format($sisaPiutang, 0, ',', '.') }}
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

                <form method="POST" action="{{ route('transaksipriangan.batalkan', $transaksi->id) }}" class="p-6 space-y-4">
                    @csrf
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Order <span class="font-mono font-bold text-slate-900">{{ $transaksi->nofakturpriangan }}</span> akan dibatalkan dari antrean siaran.
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

