<x-app-layout>
    <div class="space-y-6" x-data="{
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
        <div class="space-y-6" :class="{ 'print:hidden': showModal }">

        <!-- Header Cetak Ulang Kuitansi -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-800 border border-slate-300 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Cetak Ulang Kuitansi (BKM / BKK)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-slate-100 text-slate-800 border border-slate-300">
                            Utilitas KAS
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pencarian dan cetak ulang tanda bukti kuitansi Kas Masuk (KI) atau Bukti Kas Keluar (KO) resmi Kabar Priangan
                    </p>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar (Per Hari, Per Bulan, Per Tahun, Semua Waktu) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs" x-data="{ periode: '{{ $periodeTipe ?? 'semua' }}' }">
            <form method="GET" action="{{ route('kasir.utilitas.cetak-ulang') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-3">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Cari Dokumen</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="No. Kuitansi (KI-...), BKK (KO-...), nama, uraian..."
                               class="w-full text-xs py-2 pl-9 pr-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-slate-500 text-slate-800">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                <!-- Tipe Periode Filter -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Periode Waktu</label>
                    <select name="periode_tipe" x-model="periode" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-slate-500 text-slate-800 font-semibold bg-white cursor-pointer">
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
                        <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Pilih Tanggal</span>
                        </label>
                        <input type="date" name="tanggal" value="{{ $tanggal ?? date('Y-m-d') }}" :disabled="periode !== 'hari'"
                               class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-slate-500 text-slate-800 font-semibold bg-white cursor-pointer">
                    </div>

                    <!-- Per Bulan -->
                    <div x-show="periode === 'bulan'" class="grid grid-cols-2 gap-1.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Bulan</label>
                            <select name="bulan" :disabled="periode !== 'bulan'" class="w-full text-xs py-2 px-2 rounded-lg border border-slate-300 focus:ring-1 focus:ring-slate-500 text-slate-800 font-semibold bg-white cursor-pointer">
                                @foreach(['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun','07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'] as $blnNum => $blnLabel)
                                    <option value="{{ $blnNum }}" {{ (string)($bulan ?? date('m')) === (string)$blnNum ? 'selected' : '' }}>{{ $blnLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tahun</label>
                            <select name="tahun" :disabled="periode !== 'bulan'" class="w-full text-xs py-2 px-2 rounded-lg border border-slate-300 focus:ring-1 focus:ring-slate-500 text-slate-800 font-semibold bg-white cursor-pointer">
                                @for($y = (int)date('Y') + 1; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ (int)($tahun ?? date('Y')) === $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Per Tahun -->
                    <div x-show="periode === 'tahun'">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Tahun</label>
                        <select name="tahun" :disabled="periode !== 'tahun'" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-slate-500 text-slate-800 font-semibold bg-white cursor-pointer">
                            @for($y = (int)date('Y') + 1; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ (int)($tahun ?? date('Y')) === $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Semua Waktu -->
                    <div x-show="periode === 'semua'">
                        <label class="block text-xs font-bold text-slate-400 mb-1">Rentang Tanggal</label>
                        <div class="text-xs py-2 px-3 rounded-lg bg-slate-50 border border-slate-200 text-slate-500 italic">
                            Semua Riwayat Kuitansi
                        </div>
                    </div>
                </div>

                <!-- Jenis Dokumen -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Dokumen</label>
                    <select name="jenis" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-slate-500 text-slate-800 bg-white cursor-pointer">
                        <option value="all" {{ ($jenisFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua (KI & KO)</option>
                        <option value="KI" {{ ($jenisFilter ?? '') === 'KI' ? 'selected' : '' }}>Hanya KI</option>
                        <option value="KO" {{ ($jenisFilter ?? '') === 'KO' ? 'selected' : '' }}>Hanya KO</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex items-center gap-1.5">
                    <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white font-bold text-xs rounded-lg transition shadow-2xs flex items-center justify-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Cari</span>
                    </button>
                    @if(!empty($search) || ($periodeTipe ?? 'semua') !== 'semua' || (($jenisFilter ?? 'all') !== 'all'))
                        <a href="{{ route('kasir.utilitas.cetak-ulang') }}" title="Reset Filter" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Arsip Dokumen -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-slate-600"></span>
                    Arsip Kuitansi Transaksi Kasir
                </h3>
                <span class="text-xs font-bold text-slate-500">Ditemukan: {{ $dokumenList->count() }} Dokumen</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-100/90 text-slate-800 font-bold text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-bold text-slate-800">Tipe</th>
                            <th class="px-4 py-3 font-bold text-slate-800">No. Dokumen</th>
                            <th class="px-4 py-3 font-bold text-slate-800">Tanggal</th>
                            <th class="px-4 py-3 font-bold text-slate-800">Pihak Terkait</th>
                            <th class="px-4 py-3 font-bold text-slate-800">Uraian / Keterangan</th>
                            <th class="px-4 py-3 font-bold text-slate-800 text-right">Nominal (Rp)</th>
                            <th class="px-4 py-3 font-bold text-slate-800 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dokumenList as $doc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $doc->tipe === 'KI' ? 'bg-teal-50 text-teal-800 border border-teal-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                                    {{ $doc->tipe }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-mono font-bold text-slate-900">
                                {{ $doc->no_dokumen }}
                                @if($doc->is_void)
                                    <span class="text-[9px] text-rose-600 font-black block">[VOID]</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $doc->tanggal }}</td>
                            <td class="px-4 py-3 font-bold text-slate-800">{{ $doc->pihak }}</td>
                            <td class="px-4 py-3 text-slate-600 max-w-xs truncate">{{ $doc->uraian }}</td>
                            <td class="px-4 py-3 text-right font-mono font-black {{ $doc->tipe === 'KI' ? 'text-teal-700' : 'text-rose-700' }}">
                                Rp {{ number_format($doc->nominal, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button"
                                        @click="openPreview({{ json_encode($doc) }})"
                                        class="px-2.5 py-1 bg-slate-800 hover:bg-slate-900 text-white font-bold text-[10px] rounded-lg transition shadow-2xs flex items-center gap-1 mx-auto">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Cetak Ulang</span>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-6 text-center text-slate-400">Tidak ada arsip dokumen yang cocok dengan pencarian.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        </div>

        <!-- Modal Cetak Ulang Dokumen (KI / KO) Sesuai Layout Resmi DOS -->
        <div x-cloak x-show="showModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-100 overflow-hidden">
                <!-- Header Kuitansi -->
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-slate-50/50">
                    <div>
                        <div class="text-base font-black tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500 font-semibold">DIVISI KASIR - ARSIP RESMI KAS</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 text-[10px] font-black rounded uppercase tracking-widest border"
                              :class="doc.tipe === 'KI' ? 'bg-teal-100 text-teal-800 border-teal-300' : 'bg-rose-100 text-rose-800 border-rose-300'"
                              x-text="doc.tipe === 'KI' ? 'BUKTI KAS MASUK (KI)' : 'BUKTI KAS KELUAR (KO)'">
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1" x-text="doc.no_dokumen"></div>
                        <div class="text-[10px] text-slate-500" x-text="doc.tanggal"></div>
                    </div>
                </div>

                <!-- Body Kuitansi (Scrollable) -->
                <div class="print-document-body p-5 overflow-y-auto flex-1 space-y-2 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500" x-text="doc.tipe === 'KI' ? 'Sudah Terima Dari' : 'Diberikan Kepada'"></span>
                        <span class="col-span-2 font-bold text-slate-900">: <span x-text="doc.pihak"></span></span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Divisi & COA</span>
                        <span class="col-span-2 font-mono text-slate-700">: <span x-text="({'01':'Divisi Iklan','02':'Divisi Sirkulasi','03':'Divisi Umum','04':'Divisi Akuntansi','05':'Divisi Keuangan','06':'Divisi Redaksi','07':'Divisi Pimpinan','09':'Divisi Kasir','14':'Divisi Sirkulasi'}[doc.kode_bagian] || (doc.kode_bagian && isNaN(doc.kode_bagian) ? ('Divisi ' + doc.kode_bagian) : 'Divisi Keuangan'))"></span> - <span x-text="doc.kode_coa || '-'"></span></span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Uraian / Keperluan</span>
                        <span class="col-span-2 text-slate-800">: <span x-text="doc.uraian"></span></span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Metode Transaksi</span>
                        <span class="col-span-2 font-semibold text-slate-800">: <span x-text="doc.metode"></span></span>
                    </div>
                    <div class="grid grid-cols-3 py-2 px-3 rounded-lg"
                         :class="doc.tipe === 'KI' ? 'bg-teal-50/80 text-teal-900' : 'bg-rose-50/80 text-rose-900'">
                        <span class="font-bold">Total Nominal</span>
                        <span class="col-span-2 font-mono font-black text-sm">: Rp <span x-text="new Intl.NumberFormat('id-ID').format(doc.nominal)"></span></span>
                    </div>

                    <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                        <span class="text-[10px] text-slate-500 block uppercase font-bold">Terbilang:</span>
                        <span class="text-xs font-semibold italic text-slate-800" x-text="terbilang(doc.nominal)"></span>
                    </div>

                    <!-- Tanda Tangan -->
                    <div class="grid grid-cols-2 pt-4 border-t border-slate-200 text-center text-[10px] text-slate-600">
                        <div>
                            <div x-text="doc.tipe === 'KI' ? 'Penyetor / Klien' : 'Penerima Uang'"></div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900">(<span x-text="doc.pihak"></span>)</div>
                        </div>
                        <div>
                            <div>Petugas Kasir</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900 font-mono">(<span x-text="doc.kasir || 'Kasir Priangan'"></span>)</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons (Pinned Bottom) -->
                <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2 bg-slate-50/80 shrink-0 print:hidden no-print">
                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
