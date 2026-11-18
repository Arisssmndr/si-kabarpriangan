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
        <div class="space-y-5" :class="{ 'print:hidden': showPreview }">

            <!-- ============================================================== -->
            <!-- 1. HEADER HALAMAN (PALET 60-30-10 & ACTION BAR)                -->
            <!-- ============================================================== -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5 min-w-0">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                                Riwayat Log Cetak Kuitansi (BKM / BKK)
                            </h1>
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-sky-50 text-kp-blue-700 border border-kp-blue-200">
                                Arsip Digital
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                            Arsip digital pencetakan ulang bukti kas masuk, bukti kas keluar, kwitansi iklan langsung, dan kwitansi koran langganan.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-xs font-semibold text-slate-500 bg-slate-50 border border-slate-200 px-3 py-2 rounded-xl">
                        Total Tercatat: <span class="text-slate-900 font-bold font-mono">{{ $dokumenList->count() }}</span> Dokumen
                    </span>
                </div>
            </div>

            <!-- ============================================================== -->
            <!-- 2. FILTER BAR TERPISAH (H-11 STANDAR IKLAN)                    -->
            <!-- ============================================================== -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs" x-data="{ periode: '{{ $periodeTipe ?? 'semua' }}' }">
                <form method="GET" action="{{ route('kasir.riwayat-cetak') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Pencarian Dokumen</label>
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search ?? '' }}" 
                                   placeholder="Nomor dokumen, pihak, uraian..."
                                   class="w-full h-11 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Tipe Periode Filter -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Periode Waktu</label>
                        <select name="periode_tipe" 
                                x-model="periode" 
                                class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 font-semibold focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition cursor-pointer">
                            <option value="semua">Semua Waktu</option>
                            <option value="hari">Per Hari (Tanggal)</option>
                            <option value="bulan">Per Bulan</option>
                            <option value="tahun">Per Tahun</option>
                        </select>
                    </div>

                    <!-- Dynamic Input Tanggal / Bulan / Tahun -->
                    <div class="sm:col-span-3">
                        <!-- Per Hari -->
                        <div x-show="periode === 'hari'">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Tanggal</label>
                            <input type="date" 
                                   name="tanggal" 
                                   value="{{ $tanggal ?? date('Y-m-d') }}" 
                                   :disabled="periode !== 'hari'"
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                        </div>

                        <!-- Per Bulan -->
                        <div x-show="periode === 'bulan'" class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Bulan</label>
                                <select name="bulan" 
                                        :disabled="periode !== 'bulan'" 
                                        class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                                    @foreach(['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun','07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'] as $blnNum => $blnLabel)
                                        <option value="{{ $blnNum }}" {{ (string)($bulan ?? date('m')) === (string)$blnNum ? 'selected' : '' }}>{{ $blnLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tahun</label>
                                <select name="tahun" 
                                        :disabled="periode !== 'bulan'" 
                                        class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                                    @for($y = (int)date('Y') + 1; $y >= 2020; $y--)
                                        <option value="{{ $y }}" {{ (int)($tahun ?? date('Y')) === $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- Per Tahun -->
                        <div x-show="periode === 'tahun'">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Tahun</label>
                            <select name="tahun" 
                                    :disabled="periode !== 'tahun'" 
                                    class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                                @for($y = (int)date('Y') + 1; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ (int)($tahun ?? date('Y')) === $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Semua Waktu -->
                        <div x-show="periode === 'semua'">
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Rentang Waktu</label>
                            <div class="h-11 px-3.5 flex items-center rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-500 italic">
                                Semua Riwayat Dokumen
                            </div>
                        </div>
                    </div>

                    <!-- Jenis Dokumen -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Dokumen</label>
                        <select name="jenis" 
                                class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition cursor-pointer">
                            <option value="all" {{ ($jenisFilter ?? '') === 'all' ? 'selected' : '' }}>Semua Jenis</option>
                            <option value="BKM" {{ ($jenisFilter ?? '') === 'BKM' ? 'selected' : '' }}>BKM</option>
                            <option value="Kwitansi" {{ ($jenisFilter ?? '') === 'Kwitansi' ? 'selected' : '' }}>Kwitansi</option>
                            <option value="BKK" {{ ($jenisFilter ?? '') === 'BKK' ? 'selected' : '' }}>BKK</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2 flex items-center gap-2">
                        <button type="submit" 
                                class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98] cursor-pointer flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <span>Filter</span>
                        </button>
                        @if(!empty($search) || ($periodeTipe ?? 'semua') !== 'semua' || (($jenisFilter ?? 'all') !== 'all'))
                            <a href="{{ route('kasir.riwayat-cetak') }}" 
                               title="Reset Filter" 
                               class="h-11 w-11 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- ============================================================== -->
            <!-- 3. PURE DATA TABLE (ARSIP DOKUMEN CETAK)                       -->
            <!-- ============================================================== -->
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="px-4 py-3">No. Dokumen & Jenis</th>
                                <th class="px-4 py-3">Tanggal Terbit</th>
                                <th class="px-4 py-3">Penyetor / Penerima</th>
                                <th class="px-4 py-3">Uraian Transaksi</th>
                                <th class="px-4 py-3">Metode Bayar</th>
                                <th class="px-4 py-3 text-right">Nominal (Rp)</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($dokumenList as $doc)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="font-mono font-bold text-slate-900">{{ $doc->no_dokumen }}</div>
                                        @if(str_contains($doc->jenis, 'BKM'))
                                            <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full bg-sky-50 text-kp-blue-700 border border-sky-200 mt-1">
                                                {{ $doc->jenis }}
                                            </span>
                                        @elseif(str_contains($doc->jenis, 'BKK'))
                                            <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full bg-rose-50 text-rose-700 border border-rose-200 mt-1">
                                                {{ $doc->jenis }}
                                            </span>
                                        @else
                                            <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-700 border border-slate-300 mt-1">
                                                {{ $doc->jenis }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 font-mono text-xs">
                                        {{ \Carbon\Carbon::parse($doc->tanggal)->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-slate-900">{{ $doc->penerima_pembayar }}</div>
                                        <div class="text-xs text-slate-400">Kasir: {{ $doc->kasir }}</div>
                                    </td>
                                    <td class="px-4 py-3 max-w-xs text-slate-600 text-xs truncate" title="{{ $doc->uraian }}">
                                        {{ $doc->uraian }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-slate-700 text-xs">
                                        {{ $doc->metode }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-bold {{ str_contains($doc->jenis, 'BKK') ? 'text-rose-600' : 'text-kp-blue-700' }}">
                                        Rp {{ number_format($doc->nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
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
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-900 active:bg-slate-950 text-white font-semibold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Lihat & Cetak</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-slate-400 text-sm">
                                        Tidak ada riwayat dokumen yang cocok dengan filter pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 4. MODAL PRATINJAU KWITANSI / BUKTI CETAK                      -->
        <!-- ============================================================== -->
        <div x-cloak x-show="showPreview" style="display: none;" 
             class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showPreview = false" class="print-document-sheet bg-white rounded-2xl max-w-2xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 text-slate-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between shrink-0 bg-slate-50 print:hidden">
                    <span class="text-sm font-bold text-slate-900">Pratinjau Kuitansi Cetak Resmi</span>
                    <button type="button" @click="showPreview = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
                </div>

                <!-- Print Preview Area (Scrollable) -->
                <div class="print-document-body flex-1 overflow-y-auto p-5 sm:p-6">
                    <div id="printable-voucher" class="border border-slate-300 p-6 rounded-2xl bg-white relative">
                        <!-- Header Kop Kabar Priangan -->
                        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-4 mb-5">
                            <div>
                                <h2 class="text-lg font-black tracking-wider text-slate-900 uppercase">Harian Umum Kabar Priangan</h2>
                                <p class="text-[11px] text-slate-500 font-semibold">PT Berkah Pikiran Rakyat • Jl. Dr. Sukardjo No. 47, Kota Tasikmalaya</p>
                                <p class="text-[10px] text-slate-400">Telp. (0265) 331888 • Email: redaksi@kabarpriangan.co.id</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-3 py-1 font-bold text-xs uppercase rounded-full bg-slate-900 text-white tracking-widest" x-text="selectedDoc.jenis"></span>
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
                            <div class="bg-sky-50 border border-sky-200 p-4 rounded-xl flex items-center justify-between mt-4">
                                <span class="text-xs font-bold text-[#0E527B] uppercase tracking-wider">Jumlah Total:</span>
                                <span class="text-base font-black text-kp-blue-700 font-mono">
                                    Rp <span x-text="formatRp(selectedDoc.nominal)"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Signatures Section -->
                        <div class="grid grid-cols-2 gap-8 text-center text-xs mt-6 pt-5 border-t border-slate-200">
                            <div>
                                <div class="text-slate-500">Penyetor / Penerima,</div>
                                <div class="h-12"></div>
                                <div class="font-bold text-slate-900 underline" x-text="selectedDoc.pihak"></div>
                            </div>
                            <div>
                                <div class="text-slate-500">Kasir Kabar Priangan,</div>
                                <div class="h-12"></div>
                                <div class="font-bold text-slate-900 underline" x-text="selectedDoc.kasir"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons (Pinned Bottom) -->
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-3 shrink-0 print:hidden no-print">
                    <button type="button" 
                            @click="showPreview = false" 
                            class="h-10 px-4 text-xs font-semibold text-slate-700 hover:bg-slate-100 bg-white border border-slate-300 rounded-xl transition cursor-pointer">
                        Tutup Pratinjau
                    </button>
                    <button type="button" 
                            onclick="window.print()" 
                            class="h-10 px-5 text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 rounded-xl shadow-xs transition hover:scale-[1.02] flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak ke Printer / PDF</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>