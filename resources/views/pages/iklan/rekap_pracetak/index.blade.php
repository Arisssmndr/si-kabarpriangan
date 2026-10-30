<x-app-layout>
    <div class="space-y-6" x-data="{ tutupModal: false, unlockModal: false }">

        <!-- Header Halaman Standar Selaras Brand Kabar Priangan (60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Rekap Dummy Pracetak Harian
                        </h1>
                        @if($isDateLocked)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 shrink-0">
                                <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span>Cut-Off Terkunci</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <span>Cut-Off Terbuka</span>
                            </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Tata letak pemuatan koran edisi: <span class="font-semibold text-slate-700">{{ $tanggalFormatted }}</span>
                    </p>
                </div>
            </div>

            <!-- Tombol Aksi: Sejajar 1 Baris Tanpa Numpuk -->
            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('transaksikoran.index') }}" 
                   class="inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer shrink-0 whitespace-nowrap">
                    &larr; <span>Transaksi Koran</span>
                </a>

                <a href="{{ route('iklan.rekap-pracetak.cetak', ['tanggal_muat' => $tanggalMuat, 'kop_kota_id' => $halamanFilter]) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak Dummy</span>
                </a>

                @if(!$isDateLocked)
                    <button type="button" 
                            @click="tutupModal = true"
                            class="inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer shrink-0 whitespace-nowrap">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Tutup Cut-Off</span>
                    </button>
                @else
                    <button type="button" 
                            @click="unlockModal = true"
                            class="inline-flex items-center gap-1.5 h-10 px-3.5 sm:px-4 bg-amber-500 hover:bg-amber-600 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer shrink-0 whitespace-nowrap">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>
                        <span>Memo Unlock</span>
                    </button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold rounded-xl text-xs flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($cbpWarnings > 0)
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-xs font-normal flex items-center gap-3 shadow-2xs">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <span class="font-bold text-amber-900">Perhatian Tim Pracetak:</span> Terdapat <span class="font-bold">{{ $cbpWarnings }}</span> order iklan tertahan aturan <em>Cash Before Print</em> (CBP) karena pembayaran tunai belum disetor ke Kasir. Jangan dicetak sebelum divalidasi lunas.
                </div>
            </div>
        @endif

        <!-- Filter Bar: Tanggal Terbit & Penempatan Halaman (Konsisten & Rapi) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('iklan.rekap-pracetak.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal Terbit Edisi Koran
                    </label>
                    <input type="date" name="tanggal_muat" value="{{ $tanggalMuat }}" 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 font-mono focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>

                <div class="sm:col-span-5">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Penempatan Halaman / Wilayah
                    </label>
                    <select name="kop_kota_id" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="all">Semua Halaman (Seluruh Edisi Terbit)</option>
                        @foreach($allHalaman as $hal)
                            <option value="{{ $hal->id }}" {{ $halamanFilter == $hal->id ? 'selected' : '' }}>
                                {{ $hal->nama_penempatan }} (Hal. {{ $hal->nomor_halaman }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-3 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-semibold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Tampilkan Rekap
                    </button>
                    <a href="{{ route('iklan.rekap-pracetak.index', ['tanggal_muat' => now()->addDay()->toDateString()]) }}" 
                       class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                        Besok
                    </a>
                </div>
            </form>
        </div>

        <!-- KPI Ringkasan Produksi Koran Harian (Hierarki Teks Terstandarisasi) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Total Order Terbit</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-tabular">
                    {{ $totalIklan }} <span class="text-xs font-normal text-slate-400">Iklan</span>
                </p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Volume MMK (Display)</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-tabular">
                    {{ number_format($totalMmk, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">mmk</span>
                </p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Total Baris Naskah</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-tabular">
                    {{ $totalBaris }} <span class="text-xs font-normal text-slate-400">Baris</span>
                </p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Estimasi Omset Terbit</span>
                <p class="text-xl sm:text-2xl font-bold text-kp-blue-700 mt-1 font-tabular">
                    Rp {{ number_format($totalNilai, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Pengelompokan Pemuatan per Halaman Koran -->
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
                                <span class="bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 rounded-md px-2.5 py-1 font-bold font-tabular">
                                    Rp {{ number_format($grup['total_nominal'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Tabel Iklan pada Halaman Ini (Global Blue-Gold Header) -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm border-collapse">
                                <thead>
                                    <tr class="whitespace-nowrap">
                                        <th class="py-3 px-3.5 font-bold text-center w-12">No</th>
                                        <th class="py-3 px-3.5 font-bold">Faktur / Pengiklan</th>
                                        <th class="py-3 px-3.5 font-bold">Produk &amp; Warna</th>
                                        <th class="py-3 px-3.5 font-bold">Spesifikasi Ukuran</th>
                                        <th class="py-3 px-3.5 font-bold">Materi / Naskah Kalimat</th>
                                        <th class="py-3 px-3.5 font-bold text-center">Kesiapan Desain</th>
                                        <th class="py-3 px-3.5 font-bold">Sales AE</th>
                                        <th class="py-3 px-3.5 font-bold text-center w-20">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 whitespace-nowrap">
                                    @foreach($grup['items'] as $idx => $item)
                                        <tr class="hover:bg-slate-50/80 transition {{ $item['is_cbp_blocked'] ? 'bg-amber-50/40' : '' }}">
                                            <td class="py-3 px-3.5 font-normal text-slate-500 font-tabular text-center">{{ $idx + 1 }}</td>
                                            <td class="py-3 px-3.5">
                                                <div class="font-mono text-xs font-bold text-kp-blue-700">{{ $item['nofaktur'] }}</div>
                                                <div class="font-semibold text-slate-900 text-sm mt-0.5">{{ $item['pengiklan'] }}</div>
                                                @if($item['is_cbp_blocked'])
                                                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                                        <svg class="w-3 h-3 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span>CBP: Belum Lunas</span>
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-3.5">
                                                <span class="font-medium text-slate-800 block">{{ $item['produk'] }}</span>
                                                <span class="text-xs text-slate-500 font-normal">({{ $item['warna'] }})</span>
                                            </td>
                                            <td class="py-3 px-3.5">
                                                <span class="font-semibold text-slate-900 block font-mono text-xs">{{ $item['ukuran'] }}</span>
                                                <span class="text-xs text-slate-500 font-normal">Edisi ke-{{ $item['edisi_ke'] }}</span>
                                            </td>
                                            <td class="py-3 px-3.5 max-w-xs whitespace-normal">
                                                @if($item['kategori_form'] === 'BARIS')
                                                    <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg font-mono text-xs leading-relaxed whitespace-pre-wrap select-all text-slate-700">
                                                        {{ $item['naskah_baris'] }}
                                                    </div>
                                                @else
                                                    <span class="text-xs text-slate-700 font-normal block">{{ $item['keterangan'] ?: 'Materi visual display' }}</span>
                                                    @if($item['file_materi'])
                                                        <a href="{{ Storage::url($item['file_materi']) }}" target="_blank" class="text-xs font-semibold text-kp-blue-700 hover:underline mt-0.5 inline-block">
                                                            Unduh File CMYK
                                                        </a>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="py-3 px-3.5 text-center">
                                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $item['status_materi'] === 'Siap_Cetak' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                                    {{ $item['status_materi'] === 'Siap_Cetak' ? 'Siap Cetak' : $item['status_materi'] }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-3.5 text-xs font-medium text-slate-700">
                                                {{ $item['sales'] }}
                                            </td>
                                            <td class="py-3 px-3.5 text-center">
                                                <a href="{{ route('transaksikoran.show', $item['transaksi_id']) }}" target="_blank" 
                                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-500 hover:text-kp-blue-700 hover:bg-slate-100 transition" 
                                                   title="Buka Detail">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
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

        <!-- Modal Dialog: Tutup Transaksi Harian (Daily Cut-Off) -->
        <div x-cloak x-show="tutupModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="tutupModal = false" class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">
                            Tutup Transaksi Hari Ini (Cut-Off Cetak)
                        </h3>
                    </div>
                </div>
                <p class="text-xs text-slate-600 mb-4 font-normal">
                    Setelah ditutup, staf FO/AE tidak dapat menambah atau mengubah order untuk edisi <span class="font-semibold text-slate-800">{{ $tanggalFormatted }}</span> tanpa Memo Unlock Supervisor.
                </p>

                <form method="POST" action="{{ route('iklan.cutoff.tutup') }}" class="space-y-3.5">
                    @csrf
                    <input type="hidden" name="tanggal_edisi" value="{{ $tanggalMuat }}">

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Catatan Supervisor / Pracetak
                        </label>
                        <textarea name="catatan" rows="3" placeholder="Contoh: Berkas layout pracetak sudah diserahkan ke percetakan..."
                                  class="w-full p-3 text-sm font-normal text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition"></textarea>
                    </div>

                    <div class="flex justify-end gap-2.5 pt-2">
                        <button type="button" @click="tutupModal = false" 
                                class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="h-10 px-4 bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer shadow-xs">
                            Kunci Transaksi Edisi Ini
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Dialog: Memo Unlock Supervisor -->
        <div x-cloak x-show="unlockModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="unlockModal = false" class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">
                            Memo Unlock Transaksi Supervisor
                        </h3>
                    </div>
                </div>
                <p class="text-xs text-slate-600 mb-4 font-normal">
                    Gunakan untuk membuka kunci edisi <span class="font-semibold text-slate-800">{{ $tanggalFormatted }}</span> apabila terdapat iklan susulan mendesak (VIP/Instansi) yang telah disetujui pimpinan.
                </p>

                <form method="POST" action="{{ route('iklan.cutoff.memo-unlock') }}" class="space-y-3.5">
                    @csrf
                    <input type="hidden" name="tanggal_target" value="{{ $tanggalMuat }}">

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nomor Memo Resmi (Wajib)
                        </label>
                        <input type="text" name="nomor_memo" required placeholder="Contoh: MEMO/IKL/2026/10/001"
                               class="w-full h-11 px-3.5 text-sm font-semibold text-slate-900 uppercase bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 font-mono transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alasan Pembukaan Kunci
                        </label>
                        <textarea name="alasan" required rows="3" placeholder="Contoh: Iklan ucapan bela sungkawa Bupati Tasikmalaya..."
                                  class="w-full p-3 text-sm font-normal text-slate-900 bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"></textarea>
                    </div>

                    <div class="flex justify-end gap-2.5 pt-2">
                        <button type="button" @click="unlockModal = false" 
                                class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="h-10 px-4 bg-amber-500 hover:bg-amber-600 text-white text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer shadow-xs">
                            Buka Kunci Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
