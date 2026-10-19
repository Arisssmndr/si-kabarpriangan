<x-app-layout>
    <div class="space-y-6" x-data="{ tutupModal: false, unlockModal: false }">

        <!-- Header Halaman Standar Selaras Foto 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-black tracking-tight">
                            Rekap Dummy Pracetak Harian
                        </h1>
                        @if($isDateLocked)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                🔒 Cut-Off Terkunci
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                🟢 Terbuka
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-black mt-0.5">
                        Panduan operasional tata letak halaman koran edisi terbit: <span class="text-kp-blue-700 underline font-bold">{{ $tanggalFormatted }}</span>
                    </p>
                </div>
            </div>

            <!-- Tombol Aksi Cetak & Kontrol Cut-off -->
            <div class="flex items-center flex-wrap gap-2">
                <a href="{{ route('transaksikoran.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-black text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    &larr; <span>Kembali ke Transaksi Koran</span>
                </a>

                <a href="{{ route('iklan.rekap-pracetak.cetak', ['tanggal_muat' => $tanggalMuat, 'kop_kota_id' => $halamanFilter]) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Lembar Dummy</span>
                </a>

                @if(!$isDateLocked)
                    <button type="button" 
                            @click="tutupModal = true"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Tutup Transaksi Hari Ini</span>
                    </button>
                @else
                    <button type="button" 
                            @click="unlockModal = true"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                        <span>Memo Unlock Supervisor</span>
                    </button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-300 text-black font-bold rounded-xl text-xs flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($cbpWarnings > 0)
            <div class="p-4 bg-amber-50 border-2 border-amber-300 rounded-xl text-black text-xs font-bold flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-800 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span>PERHATIAN TIM PRACETAK: Terdapat {{ $cbpWarnings }} order iklan yang tertahan aturan Cash Before Print (CBP) karena pembayaran tunai belum disetor ke Kasir. Jangan dicetak sebelum divalidasi lunas.</span>
            </div>
        @endif

        <!-- Filter Bar: Pilih Tanggal Terbit & Penempatan Halaman -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
            <form method="GET" action="{{ route('iklan.rekap-pracetak.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-xs font-bold text-black mb-1">Pilih Tanggal Terbit Edisi Koran:</label>
                    <input type="date" name="tanggal_muat" value="{{ $tanggalMuat }}" 
                           class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-black font-mono">
                </div>

                <div class="sm:col-span-5">
                    <label class="block text-xs font-bold text-black mb-1">Filter Penempatan Halaman / Wilayah:</label>
                    <select name="kop_kota_id" class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-black">
                        <option value="all">Semua Halaman (Seluruh Edisi Terbit)</option>
                        @foreach($allHalaman as $hal)
                            <option value="{{ $hal->id }}" {{ $halamanFilter == $hal->id ? 'selected' : '' }}>
                                {{ $hal->nama_penempatan }} (Hal. {{ $hal->nomor_halaman }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-3 flex gap-2">
                    <button type="submit" class="flex-1 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                        Tampilkan Rekap
                    </button>
                    <a href="{{ route('iklan.rekap-pracetak.index', ['tanggal_muat' => now()->addDay()->toDateString()]) }}" 
                       class="h-10 px-3 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-black font-bold text-xs rounded-xl transition flex items-center justify-center">
                        Besok
                    </a>
                </div>
            </form>
        </div>

        <!-- KPI Ringkasan Produksi Koran Harian -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
                <span class="text-xs font-bold text-black block">Total Order Terbit</span>
                <p class="text-2xl font-bold text-black mt-1 font-mono">{{ $totalIklan }} <span class="text-xs font-semibold">Iklan</span></p>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
                <span class="text-xs font-bold text-black block">Total Volume MMK (Display/Kolom)</span>
                <p class="text-2xl font-bold text-black mt-1 font-mono">{{ number_format($totalMmk, 0, ',', '.') }} <span class="text-xs font-semibold">mmk</span></p>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
                <span class="text-xs font-bold text-black block">Total Baris Naskah</span>
                <p class="text-2xl font-bold text-black mt-1 font-mono">{{ $totalBaris }} <span class="text-xs font-semibold">Baris</span></p>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
                <span class="text-xs font-bold text-black block">Total Estimasi Omset Terbit</span>
                <p class="text-xl font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($totalNilai, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Pengelompokan Pemuatan per Halaman Koran -->
        <div class="space-y-6">
            @forelse($groupedByHalaman as $key => $grup)
                @if(count($grup['items']) > 0)
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
                        
                        <!-- Header Halaman Koran -->
                        <div class="bg-slate-100 px-5 py-3.5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-black text-white font-bold flex items-center justify-center text-xs font-mono">
                                    {{ $grup['nomor_halaman'] < 99 ? 'H' . $grup['nomor_halaman'] : 'REG' }}
                                </span>
                                <div>
                                    <h2 class="text-sm font-bold text-black">{{ $grup['nama'] }}</h2>
                                    <span class="text-xs font-bold text-black">Wilayah Biro: {{ $grup['wilayah'] }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 text-xs font-bold text-black">
                                <span>{{ count($grup['items']) }} Iklan</span>
                                @if($grup['total_mmk'] > 0)
                                    <span>• {{ number_format($grup['total_mmk'], 0, ',', '.') }} mmk</span>
                                @endif
                                @if($grup['total_baris'] > 0)
                                    <span>• {{ $grup['total_baris'] }} baris</span>
                                @endif
                                <span class="text-kp-blue-700">• Rp {{ number_format($grup['total_nominal'], 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Tabel Iklan pada Halaman Ini -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-black">
                                <thead class="bg-slate-50 border-b border-slate-200 text-black">
                                    <tr>
                                        <th class="py-2.5 px-3.5 font-bold">No</th>
                                        <th class="py-2.5 px-3.5 font-bold">Faktur / Pengiklan</th>
                                        <th class="py-2.5 px-3.5 font-bold">Produk &amp; Warna</th>
                                        <th class="py-2.5 px-3.5 font-bold">Spesifikasi Ukuran</th>
                                        <th class="py-2.5 px-3.5 font-bold">Materi / Naskah Kalimat</th>
                                        <th class="py-2.5 px-3.5 font-bold">Kesiapan Desain</th>
                                        <th class="py-2.5 px-3.5 font-bold">Sales AE</th>
                                        <th class="py-2.5 px-3.5 text-center font-bold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($grup['items'] as $idx => $item)
                                        <tr class="hover:bg-slate-50 {{ $item['is_cbp_blocked'] ? 'bg-amber-50/60' : '' }}">
                                            <td class="py-3 px-3.5 font-bold font-mono">{{ $idx + 1 }}</td>
                                            <td class="py-3 px-3.5">
                                                <div class="font-bold font-mono text-kp-blue-700">{{ $item['nofaktur'] }}</div>
                                                <div class="font-bold text-black text-sm mt-0.5">{{ $item['pengiklan'] }}</div>
                                                @if($item['is_cbp_blocked'])
                                                    <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-200 text-amber-900 border border-amber-300">
                                                        ⚠️ CBP: Belum Lunas
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-3.5">
                                                <span class="font-bold text-black block">{{ $item['produk'] }}</span>
                                                <span class="text-[11px] font-semibold text-black">({{ $item['warna'] }})</span>
                                            </td>
                                            <td class="py-3 px-3.5">
                                                <span class="font-bold text-black block font-mono">{{ $item['ukuran'] }}</span>
                                                <span class="text-[11px] font-bold text-black">Edisi ke-{{ $item['edisi_ke'] }}</span>
                                            </td>
                                            <td class="py-3 px-3.5 max-w-xs">
                                                @if($item['kategori_form'] === 'BARIS')
                                                    <div class="p-2 bg-slate-50 border border-slate-300 rounded font-mono text-[11px] leading-relaxed whitespace-pre-wrap select-all">
                                                        {{ $item['naskah_baris'] }}
                                                    </div>
                                                @else
                                                    <span class="font-semibold text-black block">{{ $item['keterangan'] ?: 'Materi visual display' }}</span>
                                                    @if($item['file_materi'])
                                                        <a href="{{ Storage::url($item['file_materi']) }}" target="_blank" class="text-[11px] font-bold text-kp-blue-700 underline mt-0.5 inline-block">
                                                            Unduh File CMYK
                                                        </a>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="py-3 px-3.5">
                                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold {{ $item['status_materi'] === 'Siap_Cetak' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                                                    {{ $item['status_materi'] }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-3.5 font-bold text-black">
                                                {{ $item['sales'] }}
                                            </td>
                                            <td class="py-3 px-3.5 text-center">
                                                <a href="{{ route('transaksikoran.show', $item['transaksi_id']) }}" target="_blank" class="p-1.5 inline-flex items-center text-black hover:text-kp-blue-700" title="Buka Detail">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
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
                <div class="bg-white border border-slate-200 rounded-2xl p-10 text-center text-black">
                    <p class="font-bold text-sm">Tidak ada jadwal pemuatan iklan untuk tanggal {{ $tanggalFormatted }}.</p>
                    <span class="text-xs font-semibold mt-1 block">Silakan pilih tanggal lain melalui filter di atas.</span>
                </div>
            @endforelse
        </div>

        <!-- Modal Dialog: Tutup Transaksi Harian (Daily Cut-Off) -->
        <div x-cloak x-show="tutupModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="tutupModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <h3 class="text-base font-bold text-black mb-1">
                    Tutup Transaksi Hari Ini (Cut-Off Cetak)
                </h3>
                <p class="text-xs font-semibold text-black mb-4">
                    Setelah ditutup, staf FO/AE tidak dapat menambah atau mengubah order untuk edisi <span class="font-bold">{{ $tanggalFormatted }}</span> tanpa Memo Unlock Supervisor.
                </p>

                <form method="POST" action="{{ route('iklan.cutoff.tutup') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="tanggal_edisi" value="{{ $tanggalMuat }}">

                    <div>
                        <label class="block text-xs font-bold text-black mb-1">Catatan Supervisor / Pracetak:</label>
                        <textarea name="catatan" rows="3" placeholder="Contoh: Berkas layout pracetak sudah diserahkan ke percetakan..."
                                  class="w-full p-2.5 text-xs font-medium text-black bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-rose-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="tutupModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-black text-xs font-bold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition">
                            Kunci Transaksi Edisi Ini
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Dialog: Memo Unlock Supervisor -->
        <div x-cloak x-show="unlockModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.outside="unlockModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <h3 class="text-base font-bold text-black mb-1">
                    Memo Unlock Transaksi Supervisor
                </h3>
                <p class="text-xs font-semibold text-black mb-4">
                    Gunakan untuk membuka kunci edisi <span class="font-bold">{{ $tanggalFormatted }}</span> apabila terdapat iklan susulan mendesak (VIP/Instansi) yang telah disetujui pimpinan.
                </p>

                <form method="POST" action="{{ route('iklan.cutoff.memo-unlock') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="tanggal_target" value="{{ $tanggalMuat }}">

                    <div>
                        <label class="block text-xs font-bold text-black mb-1">Nomor Memo Resmi (Wajib):</label>
                        <input type="text" name="nomor_memo" required placeholder="Contoh: MEMO/IKL/2026/10/001"
                               class="w-full px-3 py-2 text-xs font-bold text-black uppercase bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-black mb-1">Alasan Pembukaan Kunci:</label>
                        <textarea name="alasan" required rows="3" placeholder="Contoh: Iklan ucapan bela sungkawa Bupati Tasikmalaya..."
                                  class="w-full p-2.5 text-xs font-medium text-black bg-white border border-slate-300 rounded-xl focus:outline-none focus:border-amber-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="unlockModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-black text-xs font-bold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl transition">
                            Buka Kunci Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
