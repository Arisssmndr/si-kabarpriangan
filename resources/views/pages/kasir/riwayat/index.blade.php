<x-app-layout>
    <div class="space-y-6" x-data="{ 
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
        <div class="space-y-6" :class="{ 'print:hidden': showPreview }">

        <!-- Header Page Riwayat Cetak Dokumen -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.076-.672-2.12-1.272-3.086m0 0A12.016 12.016 0 0112 4.5c4.478 0 8.268 2.943 9.543 7M4.887 10.743L3.18 12.45a.75.75 0 001.06 1.06l1.242-1.241m0 0a12.074 12.074 0 007.618 6.231M19.5 12a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Riwayat & Cetak Kwitansi / BKM / BKK
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-amber-50 text-amber-700 border border-amber-200">
                            Badge: Kwitansi
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Arsip digital pencetakan ulang bukti kas masuk, bukti kas keluar, kwitansi iklan langsung, dan kwitansi koran langganan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                    Total Tercatat: {{ $dokumenList->count() }} Dokumen
                </span>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('kasir.riwayat-cetak') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-8 relative">
                    <input type="text" name="search" value="{{ $search ?? '' }}" 
                           placeholder="Cari Nomor Dokumen, Nama Pembayar/Penerima, atau Uraian..." 
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-slate-800 placeholder-slate-400">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <div class="sm:col-span-4">
                    <select name="jenis" onchange="this.form.submit()" class="w-full py-2 px-3 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-slate-800">
                        <option value="all" {{ ($jenisFilter ?? '') === 'all' ? 'selected' : '' }}>Semua Jenis Dokumen</option>
                        <option value="BKM" {{ ($jenisFilter ?? '') === 'BKM' ? 'selected' : '' }}>Bukti Kas Masuk (BKM)</option>
                        <option value="Kwitansi" {{ ($jenisFilter ?? '') === 'Kwitansi' ? 'selected' : '' }}>Kwitansi Kasir</option>
                        <option value="BKK" {{ ($jenisFilter ?? '') === 'BKK' ? 'selected' : '' }}>Bukti Kas Keluar (BKK)</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Tabel Arsip Dokumen -->
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold text-[10px]">
                        <tr>
                            <th class="px-4 py-3">No. Dokumen & Jenis</th>
                            <th class="px-4 py-3">Tanggal Terbit</th>
                            <th class="px-4 py-3">Penyetor / Penerima</th>
                            <th class="px-4 py-3">Uraian Transaksi</th>
                            <th class="px-4 py-3">Metode Bayar</th>
                            <th class="px-4 py-3">Nominal</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dokumenList as $doc)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-mono font-bold text-slate-900">{{ $doc->no_dokumen }}</div>
                                    @if(str_contains($doc->jenis, 'BKM'))
                                        <span class="inline-block px-1.5 py-0.5 text-[9px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200 mt-0.5">
                                            {{ $doc->jenis }}
                                        </span>
                                    @elseif(str_contains($doc->jenis, 'BKK'))
                                        <span class="inline-block px-1.5 py-0.5 text-[9px] font-bold rounded bg-rose-50 text-rose-700 border border-rose-200 mt-0.5">
                                            {{ $doc->jenis }}
                                        </span>
                                    @else
                                        <span class="inline-block px-1.5 py-0.5 text-[9px] font-bold rounded bg-amber-50 text-amber-700 border border-amber-200 mt-0.5">
                                            {{ $doc->jenis }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-700 font-medium">
                                    {{ \Carbon\Carbon::parse($doc->tanggal)->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-800">{{ $doc->penerima_pembayar }}</div>
                                    <div class="text-[10px] text-slate-400">Kasir: {{ $doc->kasir }}</div>
                                </td>
                                <td class="px-4 py-3 max-w-xs text-slate-600 truncate" title="{{ $doc->uraian }}">
                                    {{ $doc->uraian }}
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-700">
                                    {{ $doc->metode }}
                                </td>
                                <td class="px-4 py-3 font-bold {{ str_contains($doc->jenis, 'BKK') ? 'text-rose-600' : 'text-emerald-700' }}">
                                    Rp {{ number_format($doc->nominal, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
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
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-lg border border-amber-200 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Lihat & Cetak</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-400">
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
             class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showPreview = false" class="print-document-sheet bg-white rounded-2xl max-w-2xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 text-slate-800 overflow-hidden">
                <div class="px-6 py-3 border-b border-slate-100 flex items-center justify-between shrink-0 bg-slate-50/50 print:hidden">
                    <span class="text-xs font-bold text-slate-700">Pratinjau Kuitansi Cetak Resmi</span>
                    <button type="button" @click="showPreview = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <!-- Print Preview Area (Scrollable) -->
                <div class="print-document-body flex-1 overflow-y-auto p-5 sm:p-6">
                    <div id="printable-voucher" class="border border-slate-300 p-6 rounded-xl bg-slate-50/40 relative">
                        <!-- Header Kop Kabar Priangan -->
                        <div class="flex items-start justify-between border-b-2 border-slate-800 pb-4 mb-4">
                            <div>
                                <h2 class="text-lg font-black tracking-wider text-slate-900 uppercase">Harian Umum Kabar Priangan</h2>
                                <p class="text-[10px] text-slate-500">PT Berkah Pikiran Rakyat • Jl. Dr. Sukardjo No. 47, Kota Tasikmalaya</p>
                                <p class="text-[10px] text-slate-500">Telp. (0265) 331888 • Email: redaksi@kabarpriangan.co.id</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-3 py-1 font-bold text-xs uppercase rounded bg-slate-900 text-white tracking-widest" x-text="selectedDoc.jenis"></span>
                                <div class="text-xs font-mono font-bold mt-1 text-slate-800" x-text="selectedDoc.no"></div>
                            </div>
                        </div>

                        <!-- Voucher Details Body -->
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

                            <!-- Highlight Nominal -->
                            <div class="bg-amber-50/80 border border-amber-200 p-3 rounded-lg flex items-center justify-between mt-4">
                                <span class="text-xs font-bold text-slate-700 uppercase">Jumlah Total:</span>
                                <span class="text-base font-black text-amber-900 font-mono">
                                    Rp <span x-text="formatRp(selectedDoc.nominal)"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Signatures Section -->
                        <div class="grid grid-cols-2 gap-8 text-center text-xs mt-6 pt-4 border-t border-slate-200">
                            <div>
                                <div class="text-slate-500">Penyetor / Penerima,</div>
                                <div class="h-12"></div>
                                <div class="font-bold text-slate-800 underline" x-text="selectedDoc.pihak"></div>
                            </div>
                            <div>
                                <div class="text-slate-500">Kasir Kabar Priangan,</div>
                                <div class="h-12"></div>
                                <div class="font-bold text-slate-800 underline" x-text="selectedDoc.kasir"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons (Pinned Bottom) -->
                <div class="px-6 py-3.5 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-3 shrink-0 print:hidden">
                    <button type="button" @click="showPreview = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-white border border-slate-200 hover:bg-slate-100 rounded-lg transition-colors">
                        Tutup Pratinjau
                    </button>
                    <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-xs transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak ke Printer / PDF</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
