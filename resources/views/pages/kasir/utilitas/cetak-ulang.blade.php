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

        {{-- Konten Layar (Disembunyikan saat modal cetak aktif) --}}
        <div class="space-y-5" :class="{ 'print:hidden print-hide-when-modal-open': showModal }">

            {{-- Header Halaman --}}
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-3.5 min-w-0">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                    <div class="min-w-0">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Cetak Ulang Kuitansi (BKM / BKK)
                        </h1>
                        <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                            Pencarian dan cetak ulang arsip bukti Kas Masuk (KI) dan Kas Keluar (KO).
                        </p>
                    </div>
                </div>
            </div>

            {{-- Filter & Pencarian --}}
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
                <form method="GET" action="{{ route('kasir.utilitas.cetak-ulang') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-end">
                    <div class="sm:col-span-6">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Cari Dokumen</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="No. Kuitansi (KI-...), No. BKK (KO-...), pihak terkait, atau uraian..."
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                    <div class="sm:col-span-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Dokumen</label>
                        <select name="jenis" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            <option value="all" {{ $jenisFilter === 'all' ? 'selected' : '' }}>Semua Dokumen (KI & KO)</option>
                            <option value="KI" {{ $jenisFilter === 'KI' ? 'selected' : '' }}>Kas Masuk (KI)</option>
                            <option value="KO" {{ $jenisFilter === 'KO' ? 'selected' : '' }}>Kas Keluar (KO)</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit" class="w-full h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                            <span>Cari</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tabel Arsip Dokumen --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900">Arsip Kuitansi Transaksi Kasir</h2>
                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $dokumenList->count() }} Dokumen
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-3.5 text-center">Tipe</th>
                                <th class="py-3 px-3.5">No. Dokumen</th>
                                <th class="py-3 px-3.5">Tanggal</th>
                                <th class="py-3 px-3.5">Pihak Terkait</th>
                                <th class="py-3 px-3.5">Uraian</th>
                                <th class="py-3 px-3.5 text-right">Nominal (Rp)</th>
                                <th class="py-3 px-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($dokumenList as $doc)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                    @if($doc->tipe === 'KI')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            KI
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            KO
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <span class="font-mono font-bold text-kp-blue-700 text-sm">{{ $doc->no_dokumen }}</span>
                                    @if($doc->is_void)
                                        <span class="inline-block ml-1.5 px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-400 border border-slate-200">VOID</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-600 whitespace-nowrap">{{ $doc->tanggal }}</td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $doc->pihak }}</td>
                                <td class="py-3.5 px-3.5 text-slate-600 max-w-xs truncate">{{ $doc->uraian }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums font-semibold text-slate-900 whitespace-nowrap">
                                    Rp {{ number_format($doc->nominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                    <button type="button"
                                            @click="openPreview({{ json_encode($doc) }})"
                                            class="px-3 py-1.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer inline-flex items-center gap-1.5">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        <span>Cetak Ulang</span>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-8 px-4 text-center text-sm text-slate-400">Tidak ada arsip dokumen yang cocok dengan pencarian.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Modal Cetak Ulang Dokumen (KI / KO) --}}
        <div x-cloak x-show="showModal" class="modal-print-overlay fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.away="showModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                {{-- Header Kuitansi --}}
                <div class="border-b border-slate-200 px-5 py-4 flex items-start justify-between shrink-0 bg-white">
                    <div>
                        <div class="text-sm font-bold tracking-tight text-slate-900 uppercase">Harian Umum Kabar Priangan</div>
                        <div class="text-xs text-slate-500 font-medium">Divisi Kasir • Arsip Resmi Kas</div>
                        <div class="text-[11px] text-slate-400">Jl. Siliwangi No. 129, Tasikmalaya</div>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border"
                              :class="doc.tipe === 'KI' ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200' : 'bg-slate-100 text-slate-700 border-slate-200'"
                              x-text="doc.tipe === 'KI' ? 'BUKTI KAS MASUK (KI)' : 'BUKTI KAS KELUAR (KO)'">
                        </span>
                        <div class="font-mono font-bold text-sm text-kp-blue-700 mt-1" x-text="doc.no_dokumen"></div>
                        <div class="font-mono text-xs text-slate-500" x-text="doc.tanggal"></div>
                    </div>
                </div>

                {{-- Body Kuitansi --}}
                <div class="print-document-body p-5 overflow-y-auto flex-1 space-y-2.5 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1.5 border-b border-slate-100">
                        <span class="text-slate-500" x-text="doc.tipe === 'KI' ? 'Sudah Terima Dari' : 'Diberikan Kepada'"></span>
                        <span class="col-span-2 font-semibold text-slate-900">: <span x-text="doc.pihak"></span></span>
                    </div>
                    <div class="grid grid-cols-3 py-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Divisi & COA</span>
                        <span class="col-span-2 font-mono text-slate-700">: <span x-text="({'01':'Divisi Iklan','02':'Divisi Sirkulasi','03':'Divisi Umum','04':'Divisi Akuntansi','05':'Divisi Keuangan','06':'Divisi Redaksi','07':'Divisi Pimpinan','09':'Divisi Kasir','14':'Divisi Sirkulasi'}[doc.kode_bagian] || (doc.kode_bagian && isNaN(doc.kode_bagian) ? ('Divisi ' + doc.kode_bagian) : 'Divisi Keuangan'))"></span> - <span x-text="doc.kode_coa || '-'"></span></span>
                    </div>
                    <div class="grid grid-cols-3 py-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Uraian / Keperluan</span>
                        <span class="col-span-2 text-slate-800">: <span x-text="doc.uraian"></span></span>
                    </div>
                    <div class="grid grid-cols-3 py-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Metode Transaksi</span>
                        <span class="col-span-2 font-semibold text-slate-800">: <span x-text="doc.metode"></span></span>
                    </div>
                    <div class="grid grid-cols-3 py-2.5 px-3.5 rounded-xl bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800">
                        <span class="font-semibold">Total Nominal</span>
                        <span class="col-span-2 font-mono font-bold text-sm tabular-nums">: Rp <span x-text="new Intl.NumberFormat('id-ID').format(doc.nominal)"></span></span>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="text-[11px] text-slate-500 block uppercase font-semibold">Terbilang:</span>
                        <span class="text-xs font-semibold italic text-slate-800" x-text="terbilang(doc.nominal)"></span>
                    </div>

                    {{-- Tanda Tangan --}}
                    <div class="grid grid-cols-2 pt-4 border-t border-slate-200 text-center text-xs text-slate-600">
                        <div>
                            <div x-text="doc.tipe === 'KI' ? 'Penyetor / Klien' : 'Penerima Uang'"></div>
                            <div class="h-12"></div>
                            <div class="font-semibold text-slate-900">(<span x-text="doc.pihak"></span>)</div>
                        </div>
                        <div>
                            <div>Petugas Kasir</div>
                            <div class="h-12"></div>
                            <div class="font-semibold text-slate-900">(<span x-text="doc.kasir || 'Kasir Priangan'"></span>)</div>
                        </div>
                    </div>
                </div>

                {{-- Footer Aksi --}}
                <div class="px-5 py-3.5 border-t border-slate-200 flex items-center justify-end gap-2.5 bg-white shrink-0 print:hidden no-print">
                    <button type="button" @click="showModal = false" class="px-4 h-10 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl transition cursor-pointer">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 h-10 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition cursor-pointer inline-flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
