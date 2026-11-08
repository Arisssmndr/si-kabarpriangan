<x-app-layout>
    <div class="space-y-5" x-data="{ tutupModal: false, unlockModal: false }">

        <!-- Header Halaman Standar Selaras Brand Kabar Priangan (60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Dummy Pemuatan Iklan (Pracetak)
                        </h1>
                        @if($isDateLocked)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-300 shrink-0">
                                <span>Cut-Off Terkunci</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 shrink-0">
                                <span>Cut-Off Terbuka</span>
                            </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Tata letak pemuatan koran edisi: <span class="font-semibold text-slate-700">{{ $tanggalFormatted }}</span>
                    </p>
                </div>
            </div>

            <!-- Tombol Aksi: Sejajar 1 Baris (60-30-10) -->
            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('iklan.rekap-pracetak.cetak', ['tanggal_muat' => $tanggalMuat, 'kop_kota_id' => $halamanFilter]) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak Dummy</span>
                </a>

                @if(!$isDateLocked)
                    <button type="button" 
                            @click="tutupModal = true"
                            class="inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-slate-800 hover:bg-slate-900 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer shrink-0 whitespace-nowrap">
                        <span>Tutup Cut-Off</span>
                    </button>
                @else
                    <button type="button" 
                            @click="unlockModal = true"
                            class="inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer shrink-0 whitespace-nowrap">
                        <span>Memo Cut-Off</span>
                    </button>
                @endif
            </div>
        </div>

        @if(isset($activeMemo) && $activeMemo)
            <div class="px-4 py-3 bg-kp-blue-50/70 border border-kp-blue-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                <div class="text-xs sm:text-sm text-slate-700 flex items-center gap-2 flex-wrap">
                    <span class="font-bold text-slate-900">Memo Aktif:</span>
                    <span class="font-mono font-bold text-kp-blue-700">{{ $activeMemo->nomor_memo }}</span>
                    @if($activeMemo->nama_pemasang)
                        <span class="text-slate-300">|</span>
                        <span class="font-semibold text-slate-900">{{ $activeMemo->nama_pemasang }}</span>
                    @endif
                    <span class="text-slate-300">|</span>
                    <span class="font-medium text-slate-700">{{ $activeMemo->kategori_urgensi }}</span>
                    <span class="text-slate-300">|</span>
                    <span class="text-slate-600">{{ $activeMemo->alasan }}</span>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('iklan.cutoff.memo-unlock.cetak', $activeMemo->id) }}" target="_blank"
                       class="h-8 px-3 rounded-lg bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold inline-flex items-center transition">
                        Cetak Memo
                    </a>
                    <form method="POST" action="{{ route('iklan.cutoff.memo-unlock.tutup', $activeMemo->id) }}">
                        @csrf
                        <button type="submit" class="h-8 px-3 rounded-lg bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold inline-flex items-center transition cursor-pointer">
                            Kunci Kembali
                        </button>
                    </form>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="p-4 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 font-semibold rounded-xl text-sm flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($cbpWarnings > 0)
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-sm font-normal flex items-center gap-3 shadow-2xs">
                <svg class="w-5 h-5 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <span class="font-semibold text-slate-900">Perhatian Tim Pracetak:</span> Terdapat <span class="font-semibold text-slate-900">{{ $cbpWarnings }}</span> order iklan tertahan aturan <em>Cash Before Print</em> (CBP) karena pembayaran tunai belum disetor ke Kasir.
                </div>
            </div>
        @endif

        <!-- Filter Bar: Tanggal Terbit & Penempatan Halaman (Selaras /transaksipriangan) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('iklan.rekap-pracetak.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Tanggal Terbit Edisi Koran
                    </label>
                    <input type="date" name="tanggal_muat" value="{{ $tanggalMuat }}" 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>

                <div class="sm:col-span-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Penempatan Halaman / Wilayah
                    </label>
                    <select name="kop_kota_id" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Halaman (Seluruh Edisi Terbit)</option>
                        @foreach($allHalaman as $hal)
                            <option value="{{ $hal->id }}" {{ $halamanFilter == $hal->id ? 'selected' : '' }}>
                                {{ $hal->nama_penempatan }} (Hal. {{ $hal->nomor_halaman }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-3 flex gap-2">
                    <button type="submit" class="w-full h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Tampilkan
                    </button>
                </div>
            </form>
        </div>

        <!-- KPI Ringkasan Produksi Koran Harian (60-30-10) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Total Order Terbit</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-tabular">
                    {{ $totalIklan }} <span class="text-xs font-normal text-slate-400">Iklan</span>
                </p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Volume MMK (Display)</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-tabular">
                    {{ number_format($totalMmk, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">mmk</span>
                </p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Total Baris Naskah</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-tabular">
                    {{ $totalBaris }} <span class="text-xs font-normal text-slate-400">Baris</span>
                </p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Estimasi Omzet Terbit</span>
                <p class="text-xl sm:text-2xl font-bold text-kp-blue-700 mt-1 font-tabular">
                    Rp {{ number_format($totalNilai, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Pengelompokan Pemuatan per Halaman Koran (Kolom Terpisah & Bersih) -->
        <div class="space-y-6">
            @forelse($groupedByHalaman as $key => $grup)
                @if(count($grup['items']) > 0)
                    <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                        
                        <!-- Header Halaman Koran: Kontras Bersih 60-30-10 -->
                        <div class="bg-slate-50 px-5 py-3.5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-kp-blue-600 text-white font-bold flex items-center justify-center text-xs font-mono shadow-xs shrink-0">
                                    {{ $grup['nomor_halaman'] < 99 ? 'H' . $grup['nomor_halaman'] : 'REG' }}
                                </span>
                                <div>
                                    <h2 class="text-sm font-bold text-slate-900">{{ $grup['nama'] }}</h2>
                                    <span class="text-xs text-slate-500 font-normal">Wilayah Biro: {{ $grup['wilayah'] }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2.5 text-xs text-slate-600">
                                <span class="bg-white border border-slate-200 rounded-md px-2.5 py-1 font-normal">
                                    <strong class="font-bold text-slate-900">{{ count($grup['items']) }}</strong> Iklan
                                </span>
                                @if($grup['total_mmk'] > 0)
                                    <span class="bg-white border border-slate-200 rounded-md px-2.5 py-1 font-normal">
                                        <strong class="font-bold text-slate-900">{{ number_format($grup['total_mmk'], 0, ',', '.') }}</strong> mmk
                                    </span>
                                @endif
                                @if($grup['total_baris'] > 0)
                                    <span class="bg-white border border-slate-200 rounded-md px-2.5 py-1 font-normal">
                                        <strong class="font-bold text-slate-900">{{ $grup['total_baris'] }}</strong> baris
                                    </span>
                                @endif
                                <span class="bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-md px-2.5 py-1 font-bold font-tabular">
                                    Rp {{ number_format($grup['total_nominal'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Tabel Iklan pada Halaman Ini (Selaras /transaksipriangan: Kolom Terpisah, 60-30-10) -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm border-collapse">
                                <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                    <tr>
                                        <th class="py-3 px-3.5 text-center w-12">No</th>
                                        <th class="py-3 px-3.5">No. Faktur</th>
                                        <th class="py-3 px-3.5">Pemasang</th>
                                        <th class="py-3 px-3.5">Sales</th>
                                        <th class="py-3 px-3.5">Jenis Iklan</th>
                                        <th class="py-3 px-3.5">Warna</th>
                                        <th class="py-3 px-3.5">Ukuran</th>
                                        <th class="py-3 px-3.5 text-center">Edisi</th>
                                        <th class="py-3 px-3.5">Materi / Naskah</th>
                                        <th class="py-3 px-3.5 text-center">Status Pracetak</th>
                                        <th class="py-3 px-3.5 text-center w-10"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                                    @foreach($grup['items'] as $idx => $item)
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                                {{ $idx + 1 }}
                                            </td>
                                            <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                                {{ $item['nofaktur'] }}
                                            </td>
                                            <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                                {{ $item['pengiklan'] }}
                                            </td>
                                            <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                                {{ $item['sales'] ?: '-' }}
                                            </td>
                                            <td class="py-3.5 px-3.5 text-slate-700 font-normal">
                                                {{ $item['produk'] }}
                                            </td>
                                            <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                                {{ $item['warna'] }}
                                            </td>
                                            <td class="py-3.5 px-3.5 font-mono text-slate-700 font-normal">
                                                {{ $item['ukuran'] }}
                                            </td>
                                            <td class="py-3.5 px-3.5 text-center text-slate-600 font-mono font-normal">
                                                Ke-{{ $item['edisi_ke'] }}
                                            </td>
                                            <td class="py-3.5 px-3.5 max-w-xs truncate text-slate-600 font-normal">
                                                @if($item['kategori_form'] === 'BARIS')
                                                    <span class="font-mono text-xs text-slate-700">{{ $item['naskah_baris'] }}</span>
                                                @else
                                                    <span>{{ $item['keterangan'] ?: 'Materi visual display' }}</span>
                                                    @if($item['file_materi'])
                                                        <a href="{{ Storage::url($item['file_materi']) }}" target="_blank" class="ml-1.5 text-xs font-semibold text-kp-blue-700 hover:underline">
                                                            [Unduh]
                                                        </a>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="py-3.5 px-3.5 text-center">
                                                @if($item['is_cbp_blocked'])
                                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-300">
                                                        Tertahan CBP
                                                    </span>
                                                @elseif($item['status_materi'] === 'Siap_Cetak')
                                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                        Siap Cetak
                                                    </span>
                                                @else
                                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                        {{ str_replace('_', ' ', $item['status_materi']) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3.5 px-3.5 text-center relative" x-data="{ open: false }">
                                                <button type="button" @click="open = !open" @click.outside="open = false"
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer"
                                                        title="Menu Aksi">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                                    </svg>
                                                </button>
                                                <div x-cloak x-show="open" x-transition.origin.top.right
                                                     class="absolute right-4 mt-1 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-1.5 z-50 text-left">
                                                    <a href="{{ route('transaksikoran.show', $item['transaksi_id']) }}" target="_blank"
                                                       class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-kp-blue-700 transition">
                                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        Detail Transaksi
                                                    </a>
                                                    <a href="{{ route('transaksikoran.cetak', $item['transaksi_id']) }}" target="_blank"
                                                       class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-kp-blue-700 transition">
                                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                                        </svg>
                                                        Cetak Faktur
                                                    </a>
                                                    <a href="{{ route('transaksikoran.sppi', $item['transaksi_id']) }}" target="_blank"
                                                       class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-kp-blue-700 transition">
                                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        Cetak SPPI
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                @endif
            @empty
                <div class="bg-white border border-slate-200 rounded-xl p-12 text-center shadow-xs">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">
                        Tidak ada jadwal pemuatan iklan untuk tanggal {{ $tanggalFormatted }}
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-sm mx-auto font-normal">
                        Silakan pilih tanggal edisi terbit lain atau ubah filter penempatan wilayah di atas.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Modal Dialog: Tutup Cut-Off -->
        <div x-cloak x-show="tutupModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="tutupModal = false" class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-4">
                    <h3 class="text-base font-bold text-slate-900">Tutup Cut-Off</h3>
                    <span class="text-xs font-medium text-slate-500">{{ $tanggalFormatted }}</span>
                </div>

                <form method="POST" action="{{ route('iklan.cutoff.tutup') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="tanggal_edisi" value="{{ $tanggalMuat }}">

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan</label>
                        <textarea name="catatan" rows="2" placeholder="Tulis catatan penutupan edisi..."
                                  class="w-full p-3 text-sm font-normal text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="tutupModal = false" 
                                class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-xs">
                            Tutup Edisi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Dialog: Memo Buka Cut-Off -->
        <div x-cloak x-show="unlockModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="unlockModal = false" class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <!-- Header Tanpa Icon -->
                <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-4">
                    <h3 class="text-base font-bold text-slate-900">Memo Buka Cut-Off</h3>
                    <span class="px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-slate-100 text-kp-blue-700 border border-slate-200">
                        {{ $nextNomorMemo ?? 'MEMO/IKL/' . date('Y/m') . '/0001' }}
                    </span>
                </div>

                <form method="POST" action="{{ route('iklan.cutoff.memo-unlock') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="tanggal_target" value="{{ $tanggalMuat }}">
                    <input type="hidden" name="nomor_memo" value="{{ $nextNomorMemo ?? '' }}">

                    <!-- Identitas Nota Memo Ringkas -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Kepada</span>
                            <span class="font-semibold text-slate-900">Divisi Akuntansi</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Dari</span>
                            <span class="font-semibold text-slate-900">Divisi Iklan</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Tanggal Terbit</span>
                            <span class="font-semibold text-kp-blue-700">{{ $tanggalFormatted }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Pemasang <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_pemasang" required placeholder="Nama klien atau instansi"
                                   class="w-full h-10 px-3 text-xs font-normal text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Diajukan Oleh <span class="text-rose-500">*</span></label>
                            <input type="text" name="pengaju" required value="{{ auth()->user()->name ?? 'Front Office' }}" placeholder="Nama Sales / Petugas FO"
                                   class="w-full h-10 px-3 text-xs font-normal text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kategori Urgensi <span class="text-rose-500">*</span></label>
                            <select name="kategori_urgensi" required
                                    class="w-full h-10 pl-3 pr-9 text-xs font-medium text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                <option value="Iklan Dukacita">Iklan Dukacita</option>
                                <option value="Pengumuman Instansi">Pengumuman Instansi</option>
                                <option value="Klien Prioritas">Klien Prioritas</option>
                                <option value="Ralat Iklan">Ralat Iklan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Penyetuju <span class="text-rose-500">*</span></label>
                            <input type="text" name="diketahui_oleh" required value="Shinta Dewi, SH., M.H." placeholder="Nama penyetuju"
                                   class="w-full h-10 px-3 text-xs font-normal text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alasan Urgensi <span class="text-rose-500">*</span></label>
                        <textarea name="alasan" required rows="2" placeholder="Jelaskan alasan pemuatan melewati jam cut-off..."
                                  class="w-full p-3 text-xs font-normal text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="unlockModal = false" 
                                class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-xs">
                            Kirim Memo
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
