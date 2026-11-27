<x-app-layout>
    <div class="space-y-5" x-data="{
        showModal: false,
        doc: {
            no_dokumen: '',
            tipe: 'KI',
            jenis_nama: '',
            kode_bagian: '',
            kode_coa: '',
            tanggal: '',
            pihak: '',
            uraian: '',
            nominal: 0,
            metode: 'KAS FISIK',
            kasir: ''
        },
        openPreview(d) {
            this.doc = d;
            this.showModal = true;
        },
        terbilang(n) {
            n = Math.abs(parseInt(n) || 0);
            const satuan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
            function bilang(x) {
                if (x < 12) return ' ' + satuan[x];
                if (x < 20) return bilang(x - 10) + ' Belas';
                if (x < 100) return bilang(Math.floor(x / 10)) + ' Puluh' + bilang(x % 10);
                if (x < 200) return ' Seratus' + bilang(x - 100);
                if (x < 1000) return bilang(Math.floor(x / 100)) + ' Ratus' + bilang(x % 100);
                if (x < 2000) return ' Seribu' + bilang(x - 1000);
                if (x < 1000000) return bilang(Math.floor(x / 1000)) + ' Ribu' + bilang(x % 1000);
                if (x < 1000000000) return bilang(Math.floor(x / 1000000)) + ' Juta' + bilang(x % 1000000);
                return bilang(Math.floor(x / 1000000000)) + ' Miliar' + bilang(x % 1000000000);
            }
            let res = bilang(n).trim().replace(/\s+/g, ' ');
            return res ? res + ' Rupiah' : 'Nol Rupiah';
        }
    }">

        <!-- Konten Halaman Kasir (Disembunyikan saat modal cetak aktif) -->
        <div class="space-y-5" :class="{ 'print:hidden': showModal }">

            <!-- ============================================================== -->
            <!-- 1. HEADER HALAMAN (PALET 60-30-10 & ACTION BAR)                -->
            <!-- ============================================================== -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5 min-w-0">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                                Cetak Ulang Kuitansi (BKM / BKK)
                            </h1>
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-sky-50 text-kp-blue-700 border border-kp-blue-200">
                                Utilitas Kas
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                            Pencarian dan cetak ulang tanda bukti kuitansi Kas Masuk (KI) atau Bukti Kas Keluar (KO) resmi Kabar Priangan.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-xs font-semibold text-slate-500 bg-slate-50 border border-slate-200 px-3 py-2 rounded-xl">
                        Total Ditemukan: <span class="text-slate-900 font-bold font-mono">{{ $dokumenList->count() }}</span> Dokumen
                    </span>
                </div>
            </div>

            <!-- ============================================================== -->
            <!-- 2. FILTER BAR TERPISAH (H-11 STANDAR IKLAN)                    -->
            <!-- ============================================================== -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs" x-data="{ periode: '{{ $periodeTipe ?? 'semua' }}' }">
                <form method="GET" action="{{ route('kasir.utilitas.cetak-ulang') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Pencarian Dokumen</label>
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search }}" 
                                   placeholder="No. kuitansi, nama, uraian..."
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
                                Semua Riwayat Kuitansi
                            </div>
                        </div>
                    </div>

                    <!-- Jenis Dokumen -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Dokumen</label>
                        <select name="jenis" 
                                class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition cursor-pointer">
                            <option value="all" {{ ($jenisFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua (KI & KO)</option>
                            <option value="KI" {{ ($jenisFilter ?? '') === 'KI' ? 'selected' : '' }}>Hanya KI</option>
                            <option value="KO" {{ ($jenisFilter ?? '') === 'KO' ? 'selected' : '' }}>Hanya KO</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2 flex items-center gap-2">
                        <button type="submit" 
                                class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98] cursor-pointer flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <span>Cari</span>
                        </button>
                        @if(!empty($search) || ($periodeTipe ?? 'semua') !== 'semua' || (($jenisFilter ?? 'all') !== 'all'))
                            <a href="{{ route('kasir.utilitas.cetak-ulang') }}" 
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
            <!-- 3. PURE DATA TABLE (ARSIP DOKUMEN TRANSAKSI)                  -->
            <!-- ============================================================== -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-[#0A72AC] border-b-2 border-[#FFB900] text-white font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="px-4 py-3">Tipe</th>
                                <th class="px-4 py-3">No. Dokumen</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Pihak Terkait</th>
                                <th class="px-4 py-3">Uraian / Keterangan</th>
                                <th class="px-4 py-3 text-right">Nominal (Rp)</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($dokumenList as $doc)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $doc->tipe === 'KI' ? 'bg-sky-50 text-kp-blue-700 border border-sky-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                        {{ $doc->tipe }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-slate-900">
                                    {{ $doc->no_dokumen }}
                                    @if($doc->is_void)
                                        <span class="text-[10px] text-rose-600 font-bold block">[VOID]</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-500 font-mono text-xs">{{ $doc->tanggal }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $doc->pihak }}</td>
                                <td class="px-4 py-3 text-slate-600 text-xs max-w-xs truncate">{{ $doc->uraian }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold {{ $doc->tipe === 'KI' ? 'text-kp-blue-700' : 'text-rose-600' }}">
                                    Rp {{ number_format($doc->nominal, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button"
                                            @click="openPreview({{ json_encode($doc) }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-900 active:bg-slate-950 text-white font-semibold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Cetak Ulang</span>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-slate-400 text-sm">
                                    Tidak ada arsip dokumen yang cocok dengan pencarian filter.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 4. MODAL CETAK ULANG DOKUMEN (PREVIEW KUITANSI DOT MATRIX)     -->
        <!-- ============================================================== -->
        <div x-cloak x-show="showModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showModal = false" class="max-w-4xl w-full max-h-[92vh] flex flex-col">
                <div class="mb-3 flex items-center justify-between print:hidden no-print">
                    <button type="button" @click="showModal = false" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs font-sans cursor-pointer">
                        ← Kembali ke Daftar Cetak Ulang Kuitansi
                    </button>
                    <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition cursor-pointer font-sans">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span x-text="doc.tipe === 'KI' ? 'Cetak Lembar Bukti Kas Masuk (Print / PDF)' : 'Cetak Lembar Bukti Kas Keluar (Print / PDF)'"></span>
                    </button>
                </div>

                <div class="print-document-sheet dot-matrix-container bg-white rounded-lg shadow-2xl overflow-y-auto border border-slate-300 flex font-dot-matrix text-black">
                    <div class="w-8 bg-slate-50 border-r border-dashed border-slate-300 py-6 flex flex-col items-center justify-between gap-4 select-none shrink-0 tractor-feed print:hidden">
                        @for ($i = 0; $i < 14; $i++)
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-200 border border-slate-300"></div>
                        @endfor
                    </div>

                    <div class="print-document-body flex-1 p-6 sm:p-8 text-[12px] leading-relaxed text-black">
                        <div class="text-left mb-3">
                            <h1 class="text-base sm:text-lg font-black tracking-wider uppercase font-dot-matrix text-black"
                                x-text="doc.tipe === 'KI' ? 'BUKTI KAS MASUK (BKM / KI) HU. PRIANGAN' : 'BUKTI KAS KELUAR (BKK / KO) HU. PRIANGAN'">
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
                                    <span class="font-bold" x-text="doc.kode_coa || '1.00.01.01.00.01'"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap" x-text="doc.tipe === 'KI' ? 'Terima Dari' : 'Kepada'"></span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold uppercase" x-text="doc.pihak"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Divisi</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold" x-text="({'01':'DIVISI IKLAN','02':'DIVISI SIRKULASI','03':'DIVISI UMUM','04':'DIVISI AKUNTANSI','05':'BIDANG KEUANGAN','06':'DIVISI REDAKSI','07':'DIVISI PIMPINAN','09':'BIDANG KEUANGAN','14':'DIVISI SIRKULASI'}[doc.kode_bagian] || (doc.kode_bagian && isNaN(doc.kode_bagian) ? ('DIVISI ' + doc.kode_bagian) : 'BIDANG KEUANGAN'))"></span>
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
                                    <span class="font-bold font-mono text-blue-900" x-text="doc.no_dokumen"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">Metode Bayar</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold" x-text="doc.metode"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">Tanggal</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold" x-text="doc.tanggal"></span>
                                </div>
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-3"></div>

                        <div class="grid grid-cols-12 gap-x-3 gap-y-1 mb-3">
                            <div class="col-span-4 flex">
                                <span class="w-20 shrink-0 font-bold whitespace-nowrap">Kategori</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase" x-text="doc.tipe === 'KI' ? 'KAS MASUK (BKM)' : 'KAS KELUAR (BKK)'"></span>
                            </div>
                            <div class="col-span-3 flex">
                                <span class="w-12 shrink-0 font-bold whitespace-nowrap">Tipe</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase" x-text="doc.tipe"></span>
                            </div>
                            <div class="col-span-5 flex">
                                <span class="w-24 shrink-0 font-bold whitespace-nowrap">Keterangan</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase text-[11px] leading-tight" x-text="doc.uraian"></span>
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-3"></div>

                        <div class="space-y-1 max-w-lg mb-4 font-bold">
                            <div class="flex justify-between">
                                <span>Brutto Transaksi</span>
                                <span>Rp. <span x-text="new Intl.NumberFormat('id-ID', {minimumFractionDigits: 2}).format(doc.nominal || 0)"></span></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Diskon / Potongan 0,00 %</span>
                                <span>Rp. 0,00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Netto</span>
                                <span>Rp. <span x-text="new Intl.NumberFormat('id-ID', {minimumFractionDigits: 2}).format(doc.nominal || 0)"></span></span>
                            </div>
                            <div class="border-t border-dashed border-black pt-1 flex justify-between text-sm font-black">
                                <span x-text="doc.tipe === 'KI' ? 'Jumlah Total Diterima' : 'Jumlah Total Dibayarkan'"></span>
                                <span>Rp. <span x-text="new Intl.NumberFormat('id-ID', {minimumFractionDigits: 2}).format(doc.nominal || 0)"></span></span>
                            </div>
                            <div class="pt-1 text-[11px] italic font-bold">
                                <span class="not-italic">Terbilang :</span> # <span x-text="terbilang(doc.nominal)"></span> #
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-6"></div>

                        <div class="grid grid-cols-2 gap-4 items-end pt-2">
                            <div class="pl-4">
                                <div class="inline-block border-2 border-emerald-600 text-emerald-700 px-5 py-2 rounded-full font-black text-center rotate-[-4deg] opacity-90">
                                    <div class="text-[9px] uppercase tracking-widest">KABAR PRIANGAN</div>
                                    <div class="text-sm tracking-wider border-t border-b border-emerald-600 my-0.5 py-0.5" x-text="doc.tipe === 'KI' ? 'L U N A S' : 'T E R B A Y A R'"></div>
                                    <div class="text-[8px] font-mono">DIVISI KEUANGAN</div>
                                </div>
                            </div>

                            <div class="text-right flex flex-col gap-5">
                                <div>
                                    <p class="text-[11px] font-bold">Tasikmalaya, <span x-text="doc.tanggal"></span></p>
                                    <p class="text-[11px] font-semibold mt-0.5" x-text="doc.tipe === 'KI' ? 'Kasir Penerima,' : 'Kasir Pembayar,'"></p>
                                    <div class="h-10"></div>
                                    <p class="font-bold underline text-[11px] uppercase" x-text="doc.kasir || 'KASIR PRIANGAN'"></p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold" x-text="doc.tipe === 'KI' ? 'Penyetor / Pelanggan,' : 'Penerima Dana,'"></p>
                                    <div class="h-10"></div>
                                    <p class="font-bold underline text-[11px] uppercase" x-text="doc.pihak"></p>
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