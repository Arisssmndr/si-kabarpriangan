<x-app-layout>
    <div class="space-y-5" x-data="{ 
        showPreview: false, 
        selectedDoc: { 
            no: '', 
            jenis: '', 
            tgl: '', 
            pihak: '', 
            uraian: '', 
            nominal: 0, 
            metode: '', 
            kasir: '' 
        },
        openPreview(doc) {
            this.selectedDoc = doc;
            this.showPreview = true;
        },
        formatRp(val) {
            return new Intl.NumberFormat('id-ID').format(val);
        }
    }">

        <!-- Konten Halaman Kasir (Disembunyikan saat modal cetak aktif) -->
        <div class="space-y-5" :class="{ 'print-hide-when-modal-open print:hidden': showPreview }">

            <!-- Header Halaman (Palet 60-30-10) -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 no-print print:hidden">
                <div class="flex items-center gap-3.5 min-w-0">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                    <div class="min-w-0">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Riwayat &amp; Cetak Kwitansi / BKM / BKK
                        </h1>
                        <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                            Arsip digital pencetakan ulang bukti kas masuk, bukti kas keluar, dan kwitansi kasir.
                        </p>
                    </div>
                </div>

                <div class="shrink-0">
                    <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-semibold font-mono bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                        {{ $dokumenList->count() }} Dokumen
                    </span>
                </div>
            </div>

            <!-- Toolbar Pencarian & Filter -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <form method="GET" action="{{ route('kasir.riwayat-cetak') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <div class="sm:col-span-6">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Dokumen</label>
                        <input type="text" name="search" value="{{ $search ?? '' }}" 
                               placeholder="Ketik nomor dokumen, nama pembayar/penerima, atau uraian..." 
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>

                    <div class="sm:col-span-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Dokumen</label>
                        <select name="jenis" onchange="this.form.submit()" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            <option value="all" {{ ($jenisFilter ?? '') === 'all' ? 'selected' : '' }}>Semua Jenis Dokumen</option>
                            <option value="BKM" {{ ($jenisFilter ?? '') === 'BKM' ? 'selected' : '' }}>Bukti Kas Masuk (BKM)</option>
                            <option value="Kwitansi" {{ ($jenisFilter ?? '') === 'Kwitansi' ? 'selected' : '' }}>Kwitansi Kasir</option>
                            <option value="BKK" {{ ($jenisFilter ?? '') === 'BKK' ? 'selected' : '' }}>Bukti Kas Keluar (BKK)</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2 flex gap-2">
                        <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                            Cari
                        </button>
                        @if(!empty($search) || (!empty($jenisFilter) && $jenisFilter !== 'all'))
                            <a href="{{ route('kasir.riwayat-cetak') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabel Arsip Dokumen -->
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
                <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Daftar Arsip Bukti Transaksi Kasir</h3>
                        <p class="text-xs text-slate-500">Klik tombol cetak untuk melihat pratinjau kwitansi resmi</p>
                    </div>
                    <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                        {{ $dokumenList->count() }} Data
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-3.5">No. Dokumen</th>
                                <th class="py-3 px-3.5">Jenis</th>
                                <th class="py-3 px-3.5">Tanggal</th>
                                <th class="py-3 px-3.5">Penyetor / Penerima</th>
                                <th class="py-3 px-3.5">Uraian Transaksi</th>
                                <th class="py-3 px-3.5">Metode</th>
                                <th class="py-3 px-3.5 text-right">Nominal</th>
                                <th class="py-3 px-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                            @forelse($dokumenList as $doc)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                        {{ $doc->no_dokumen }}
                                    </td>
                                    <td class="py-3.5 px-3.5">
                                        @if(str_contains($doc->jenis, 'BKK'))
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $doc->jenis }}
                                            </span>
                                        @else
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                {{ $doc->jenis }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono text-slate-600">
                                        {{ \Carbon\Carbon::parse($doc->tanggal)->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3.5 px-3.5">
                                        <div class="font-semibold text-slate-900">{{ $doc->penerima_pembayar }}</div>
                                        <div class="text-xs text-slate-400">Kasir: {{ $doc->kasir }}</div>
                                    </td>
                                    <td class="py-3.5 px-3.5 max-w-xs text-slate-600 truncate" title="{{ $doc->uraian }}">
                                        {{ $doc->uraian }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-slate-700">
                                        {{ $doc->metode }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono tabular-nums font-semibold text-slate-900">
                                        Rp {{ number_format($doc->nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        <button type="button" 
                                                @click="openPreview({
                                                    no: '{{ $doc->no_dokumen }}',
                                                    jenis: '{{ $doc->jenis }}',
                                                    tgl: '{{ \Carbon\Carbon::parse($doc->tanggal)->translatedFormat('d F Y') }}',
                                                    pihak: '{{ addslashes($doc->penerima_pembayar) }}',
                                                    uraian: '{{ addslashes($doc->uraian) }}',
                                                    nominal: {{ $doc->nominal }},
                                                    metode: '{{ $doc->metode }}',
                                                    kasir: '{{ $doc->kasir }}'
                                                })"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 rounded-xl shadow-xs transition cursor-pointer">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            <span>Lihat &amp; Cetak</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-10 text-center text-slate-500 text-sm">
                                        Tidak ada riwayat dokumen yang cocok dengan filter pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Modal Pratinjau Kwitansi / Bukti Cetak -->
        <div x-cloak x-show="showPreview" style="display: none;" 
             class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showPreview = false" class="print-document-sheet bg-white rounded-2xl max-w-2xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 text-slate-800 overflow-hidden">
                <div class="px-5 py-3.5 bg-white border-b border-slate-200 flex items-center justify-between shrink-0 print:hidden no-print">
                    <span class="text-sm font-bold text-slate-900">Pratinjau Kuitansi Cetak Resmi</span>
                    <button type="button" @click="showPreview = false" class="text-slate-400 hover:text-slate-600 font-bold text-xl leading-none cursor-pointer">&times;</button>
                </div>

                <!-- Print Preview Area (Scrollable) -->
                <div class="print-document-body flex-1 overflow-y-auto p-5 sm:p-6">
                    <div id="printable-voucher" class="border border-slate-300 p-6 rounded-xl bg-white relative">
                        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-4 mb-4">
                            <div>
                                <h2 class="text-lg font-bold tracking-tight text-slate-900 uppercase">Harian Umum Kabar Priangan</h2>
                                <p class="text-[11px] text-slate-500">PT Berkah Pikiran Rakyat &bull; Jl. Dr. Sukardjo No. 47, Kota Tasikmalaya</p>
                                <p class="text-[11px] text-slate-500">Telp. (0265) 331888 &bull; Email: redaksi@kabarpriangan.co.id</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-2.5 py-0.5 font-bold text-xs uppercase rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 tracking-wider" x-text="selectedDoc.jenis"></span>
                                <div class="text-xs font-mono font-bold mt-1 text-slate-900" x-text="selectedDoc.no"></div>
                            </div>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="grid grid-cols-12 gap-2">
                                <div class="col-span-4 font-semibold text-slate-500">Tanggal Terbit</div>
                                <div class="col-span-8 font-medium text-slate-800">: <span x-text="selectedDoc.tgl"></span></div>
                            </div>
                            <div class="grid grid-cols-12 gap-2">
                                <div class="col-span-4 font-semibold text-slate-500">Telah Terima Dari / Kepada</div>
                                <div class="col-span-8 font-bold text-slate-900">: <span x-text="selectedDoc.pihak"></span></div>
                            </div>
                            <div class="grid grid-cols-12 gap-2">
                                <div class="col-span-4 font-semibold text-slate-500">Untuk Pembayaran / Uraian</div>
                                <div class="col-span-8 text-slate-700">: <span x-text="selectedDoc.uraian"></span></div>
                            </div>
                            <div class="grid grid-cols-12 gap-2">
                                <div class="col-span-4 font-semibold text-slate-500">Metode Pembayaran</div>
                                <div class="col-span-8 font-medium text-slate-800">: <span x-text="selectedDoc.metode"></span></div>
                            </div>

                            <div class="bg-kp-blue-50 border border-kp-blue-200 p-3 rounded-xl flex items-center justify-between mt-4">
                                <span class="text-xs font-bold text-kp-blue-800 uppercase">Jumlah Total:</span>
                                <span class="text-base font-bold text-kp-blue-700 font-mono tabular-nums">
                                    Rp <span x-text="formatRp(selectedDoc.nominal)"></span>
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-8 text-center text-xs mt-6 pt-4 border-t border-slate-200">
                            <div>
                                <div class="text-slate-500">Penyetor / Penerima,</div>
                                <div class="h-12"></div>
                                <div class="font-bold text-slate-900 underline" x-text="selectedDoc.pihak"></div>
                            </div>
                            <div>
                                <div class="text-slate-500">Kasir Kabar Priangan,</div>
                                <div class="h-12"></div>
                                <div class="font-bold text-slate-900 underline font-mono" x-text="selectedDoc.kasir"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-3.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2.5 shrink-0 print:hidden no-print">
                    <button type="button" @click="showPreview = false" class="h-10 px-4 text-xs sm:text-sm font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 rounded-xl transition cursor-pointer">
                        Tutup Pratinjau
                    </button>
                    <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 h-10 px-4 text-xs sm:text-sm font-bold text-white bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 rounded-xl shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak ke Printer / PDF</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
