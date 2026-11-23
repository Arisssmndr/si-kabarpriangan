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
                        <thead class="bg-emerald-700 border-b border-emerald-800 text-white font-semibold text-xs whitespace-nowrap">
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
        <!-- 4. MODAL PRATINJAU KWITANSI / BUKTI CETAK (DOT MATRIX)         -->
        <!-- ============================================================== -->
        <div x-cloak x-show="showPreview" style="display: none;" 
             class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showPreview = false" class="max-w-4xl w-full max-h-[92vh] flex flex-col">
                <div class="mb-3 flex items-center justify-between print:hidden no-print">
                    <button type="button" @click="showPreview = false" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs font-sans cursor-pointer">
                        ← Kembali ke Riwayat Log Cetak
                    </button>
                    <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition cursor-pointer font-sans">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Lembar Kuitansi (Print / PDF)</span>
                    </button>
                </div>

                <div class="print-document-sheet dot-matrix-container bg-white rounded-lg shadow-2xl overflow-y-auto border border-slate-300 flex font-dot-matrix text-black">
                    <div class="w-8 bg-slate-50 border-r border-dashed border-slate-300 py-6 flex flex-col items-center justify-between gap-4 select-none shrink-0 tractor-feed print:hidden">
                        @for ($i = 0; $i < 14; $i++)
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-200 border border-slate-300"></div>
                        @endfor
                    </div>

                    <div id="printable-voucher" class="print-document-body flex-1 p-6 sm:p-8 text-[12px] leading-relaxed text-black">
                        <div class="text-left mb-3">
                            <h1 class="text-base sm:text-lg font-black tracking-wider uppercase font-dot-matrix text-black">
                                <span x-text="selectedDoc.jenis"></span> HU. PRIANGAN
                            </h1>
                            <p class="text-[11px] font-bold text-neutral-700 tracking-tight leading-tight">
                                JL. DINDINGARI RAYA NO.12 TLP. (0265)-335300, FAX. 335677 PERUM BUMI RESIK PANGLAYUNGAN TASIKMALAYA 46134
                            </p>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-3"></div>

                        <div class="grid grid-cols-12 gap-x-4 gap-y-1 mb-3">
                            <div class="col-span-7 space-y-0.5">
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Identitas</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold">1.00.01.01.00.01</span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Nama Pihak</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold uppercase" x-text="selectedDoc.pihak"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Petugas</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold" x-text="selectedDoc.kasir"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Kota</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold">TASIKMALAYA</span>
                                </div>
                            </div>

                            <div class="col-span-5 space-y-0.5">
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">No. Dokumen</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold font-mono text-blue-900" x-text="selectedDoc.no"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">Metode Bayar</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold" x-text="selectedDoc.metode"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">Tanggal</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold" x-text="selectedDoc.tgl"></span>
                                </div>
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-3"></div>

                        <div class="grid grid-cols-12 gap-x-3 gap-y-1 mb-3">
                            <div class="col-span-4 flex">
                                <span class="w-20 shrink-0 font-bold whitespace-nowrap">Kategori</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase" x-text="selectedDoc.jenis"></span>
                            </div>
                            <div class="col-span-3 flex">
                                <span class="w-12 shrink-0 font-bold whitespace-nowrap">Tipe</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase">RESMI KASIR</span>
                            </div>
                            <div class="col-span-5 flex">
                                <span class="w-24 shrink-0 font-bold whitespace-nowrap">Keterangan</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase text-[11px] leading-tight" x-text="selectedDoc.uraian"></span>
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-3"></div>

                        <div class="space-y-1 max-w-lg mb-4 font-bold">
                            <div class="flex justify-between">
                                <span>Brutto</span>
                                <span>Rp. <span x-text="formatRp(selectedDoc.nominal)"></span>,00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Diskon 0,00 %</span>
                                <span>Rp. 0,00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Netto</span>
                                <span>Rp. <span x-text="formatRp(selectedDoc.nominal)"></span>,00</span>
                            </div>
                            <div class="border-t border-dashed border-black pt-1 flex justify-between text-sm font-black">
                                <span>Jumlah Total Transaksi</span>
                                <span>Rp. <span x-text="formatRp(selectedDoc.nominal)"></span>,00</span>
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-6"></div>

                        <div class="grid grid-cols-2 gap-4 items-end pt-2">
                            <div class="pl-4">
                                <div class="inline-block border-2 border-emerald-600 text-emerald-700 px-5 py-2 rounded-full font-black text-center rotate-[-4deg] opacity-90">
                                    <div class="text-[9px] uppercase tracking-widest">KABAR PRIANGAN</div>
                                    <div class="text-sm tracking-wider border-t border-b border-emerald-600 my-0.5 py-0.5">L U N A S</div>
                                    <div class="text-[8px] font-mono">DIVISI KEUANGAN</div>
                                </div>
                            </div>

                            <div class="text-right flex flex-col gap-5">
                                <div>
                                    <p class="text-[11px] font-bold">Tasikmalaya, <span x-text="selectedDoc.tgl"></span></p>
                                    <p class="text-[11px] font-semibold mt-0.5">Kasir Kabar Priangan,</p>
                                    <div class="h-10"></div>
                                    <p class="font-bold underline text-[11px] uppercase" x-text="selectedDoc.kasir"></p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold">Penyetor / Penerima,</p>
                                    <div class="h-10"></div>
                                    <p class="font-bold underline text-[11px] uppercase" x-text="selectedDoc.pihak"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-8 bg-slate-50 border-l border-dashed border-slate-300 py-6 flex flex-col items-center justify-between gap-4 select-none shrink-0 tractor-feed print:hidden">
                        @for ($i = 0; $i < 14; $i++)
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-200 border border-slate-300"></div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>