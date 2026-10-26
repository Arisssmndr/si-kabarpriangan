<x-app-layout>
    <div class="space-y-6" x-data="{ 
        showCairModal: false, 
        showCetakModal: {{ session('cetak_komisi') ? 'true' : 'false' }},
        cairForm: {
            faktur_ref: '',
            pemasang: '',
            jenis_komisi: 'komisi_kh',
            penerima: '',
            nominal: 0,
            keterangan: ''
        },
        openCair(faktur, pemasang, jenis, penerimaDefault, nominalCalc) {
            this.cairForm.faktur_ref = faktur;
            this.cairForm.pemasang = pemasang;
            this.cairForm.jenis_komisi = jenis;
            this.cairForm.penerima = penerimaDefault;
            this.cairForm.nominal = nominalCalc;
            this.cairForm.keterangan = (jenis === 'komisi_kh' ? 'Pencairan Komisi Humas (KH 20%)' : 'Pencairan Insentif Sales/AE (10%)') + ' atas Faktur ' + faktur + ' (' + pemasang + ')';
            this.showCairModal = true;
        }
    }">

        <!-- Konten Halaman Kasir (Disembunyikan saat modal cetak aktif) -->
        <div class="space-y-6" :class="{ 'print:hidden': showCetakModal }">

        <!-- Top Breadcrumbs & Header Bar (OripioFin Style) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5 text-slate-400">
                    <button class="w-8 h-8 rounded-full bg-white border border-slate-200/80 flex items-center justify-center hover:bg-slate-50 transition shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button class="w-8 h-8 rounded-full bg-white border border-slate-200/80 flex items-center justify-center hover:bg-slate-50 transition shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
                    <span class="hover:text-slate-600 cursor-pointer">OripioFin</span>
                    <span>&rsaquo;</span>
                    <span class="hover:text-slate-600 cursor-pointer">Pengeluaran</span>
                    <span>&rsaquo;</span>
                    <span class="text-slate-800 font-semibold">Pencairan Komisi KH & AE</span>
                </div>
            </div>

            @if(session('cetak_komisi'))
                <button type="button" @click="showCetakModal = true" class="px-4 py-2.5 bg-[#0B6B44] hover:bg-[#095737] text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Voucher BKK Komisi</span>
                </button>
            @endif
        </div>

        <!-- Flash Message Alert -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200/80 rounded-2xl text-xs font-semibold text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200/80 rounded-2xl text-xs font-semibold text-rose-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs shadow-slate-100">
                <span class="text-xs font-semibold text-slate-400">Faktur Siap Cairkan Komisi</span>
                <div class="mt-2 text-lg font-black text-slate-800">
                    {{ $fakturLunas->count() }} Faktur Lunas
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Status siap pencairan KH & Insentif</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs shadow-slate-100">
                <span class="text-xs font-semibold text-slate-400">Alokasi Komisi Humas (KH ~20%)</span>
                <div class="mt-2 text-lg font-black text-emerald-600 font-mono">
                    Rp {{ number_format($fakturLunas->sum(fn($f) => $f->subtotal * 0.20), 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-emerald-600/80 font-medium">Total estimasi hak mitra humas</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs shadow-slate-100">
                <span class="text-xs font-semibold text-slate-400">Alokasi Insentif AE / Sales (~10%)</span>
                <div class="mt-2 text-lg font-black text-amber-600 font-mono">
                    Rp {{ number_format($fakturLunas->sum(fn($f) => $f->subtotal * 0.10), 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Total estimasi hak marketing internal</div>
            </div>
        </div>

        <!-- Filter & Search Toolbar (OripioFin Style) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-3 shadow-xs shadow-slate-100">
            <form method="GET" action="{{ route('kasir.pengeluaran.komisi') }}" class="flex items-center gap-3">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari no faktur, pemasang, sales AE... ⌘K"
                           class="w-full text-xs py-2 pl-9 pr-3 rounded-xl border border-slate-200/80 focus:ring-2 focus:ring-[#0B6B44]/20 focus:border-[#0B6B44] text-slate-800 bg-slate-50/50">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition shadow-2xs">
                    Cari Faktur
                </button>
            </form>
        </div>

        <!-- Tabel Faktur Lunas Siap Dicairkan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs shadow-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0B6B44]"></span>
                    Daftar Faktur Iklan Berstatus Lunas (Siap Cairkan KH / Insentif)
                </h3>
                <span class="text-[11px] text-slate-400 font-medium">Berdasarkan Pelunasan Kasir (KI-01)</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50/70 text-slate-400 text-[11px] uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3.5 font-semibold">No. Faktur</th>
                            <th class="px-5 py-3.5 font-semibold">Klien / Pemasang</th>
                            <th class="px-5 py-3.5 font-semibold">Sales / AE</th>
                            <th class="px-5 py-3.5 font-semibold text-right">Nilai Faktur (Rp)</th>
                            <th class="px-5 py-3.5 font-semibold text-right">Komisi KH (20%)</th>
                            <th class="px-5 py-3.5 font-semibold text-right">Insentif AE (10%)</th>
                            <th class="px-5 py-3.5 font-semibold text-center">Aksi Pencairan KO</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($fakturLunas as $faktur)
                        @php
                            $subtotal = (float) ($faktur->subtotal ?? $faktur->totalbayar_transaksikoran ?? 0);
                            $khNominal = round($subtotal * 0.20);
                            $aeNominal = round($subtotal * 0.10);
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-5 py-3.5 font-mono font-bold text-slate-800">{{ $faktur->nofakturkoran }}</td>
                            <td class="px-5 py-3.5 font-bold text-slate-800">{{ $faktur->nama_pemasangkoran }}</td>
                            <td class="px-5 py-3.5 text-slate-600">
                                <span class="px-2.5 py-0.5 bg-slate-100 rounded-full text-[10px] font-semibold text-slate-700">
                                    {{ $faktur->sales_iklankoran ?? 'Direct Sales' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-black text-slate-900 font-mono">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold text-emerald-600">
                                Rp {{ number_format($khNominal, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold text-amber-600">
                                Rp {{ number_format($aeNominal, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button"
                                            @click="openCair('{{ $faktur->nofakturkoran }}', '{{ addslashes($faktur->nama_pemasangkoran) }}', 'komisi_kh', 'Humas Mitra Terkait', {{ $khNominal }})"
                                            class="px-2.5 py-1.5 bg-[#0B6B44] hover:bg-[#095737] text-white font-bold text-[10px] rounded-lg transition shadow-2xs">
                                        Cairkan KH (20%)
                                    </button>
                                    <button type="button"
                                            @click="openCair('{{ $faktur->nofakturkoran }}', '{{ addslashes($faktur->nama_pemasangkoran) }}', 'insentif_ae', '{{ addslashes($faktur->sales_iklankoran ?? 'Sales AE') }}', {{ $aeNominal }})"
                                            class="px-2.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-[10px] rounded-lg transition shadow-2xs">
                                        Cairkan AE (10%)
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                Tidak ada data faktur lunas yang memerlukan pencairan komisi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Riwayat Pencairan Komisi (KO) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs shadow-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    Riwayat Pencairan Komisi & Insentif Terakhir (BKK / KO)
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50/70 text-slate-400 text-[11px] uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3.5 font-semibold">No. BKK (KO)</th>
                            <th class="px-5 py-3.5 font-semibold">Tanggal</th>
                            <th class="px-5 py-3.5 font-semibold">Faktur Ref</th>
                            <th class="px-5 py-3.5 font-semibold">Jenis Beban</th>
                            <th class="px-5 py-3.5 font-semibold">Penerima Dana</th>
                            <th class="px-5 py-3.5 font-semibold text-right">Jumlah Dicairkan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($riwayatKomisi as $ko)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-5 py-3.5 font-mono font-bold text-slate-800">{{ $ko->no_bkk }}</td>
                            <td class="px-5 py-3.5 text-slate-400">{{ $ko->tanggal_keluar ? \Carbon\Carbon::parse($ko->tanggal_keluar)->format('d/m/Y') : '-' }}</td>
                            <td class="px-5 py-3.5 font-mono text-emerald-800 font-semibold">{{ $ko->no_bp_ref }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full {{ $ko->kategori_beban === 'komisi_kh' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/60' : 'bg-amber-50 text-amber-800 border border-amber-200/60' }}">
                                    {{ $ko->kategori_beban === 'komisi_kh' ? 'Komisi Humas (KH)' : 'Insentif Sales / AE' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-bold text-slate-800">{{ $ko->diberikan_kepada ?? $ko->nama_penerima }}</td>
                            <td class="px-5 py-3.5 text-right font-black text-rose-600 font-mono">Rp {{ number_format($ko->jumlah_keluar, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">Belum ada riwayat pencairan komisi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form Pencairan Komisi / Terbitkan KO -->
        <div x-cloak x-show="showCairModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCairModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200/80 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Pencairan Hak Komisi / Insentif (KO)</h4>
                            <p class="text-[11px] text-slate-400">Divisi Iklan - COA 6.00.01.01.01.02</p>
                        </div>
                    </div>
                    <button type="button" @click="showCairModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <form method="POST" action="{{ route('kasir.pengeluaran.komisi.store') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="faktur_ref" :value="cairForm.faktur_ref">
                    <input type="hidden" name="jenis_komisi" :value="cairForm.jenis_komisi">

                    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
                        <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80 text-xs space-y-2">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Dasar Faktur:</span>
                                <span class="font-mono font-bold text-slate-900" x-text="cairForm.faktur_ref"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Pemasang:</span>
                                <span class="font-bold text-slate-800" x-text="cairForm.pemasang"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Jenis Pencairan:</span>
                                <span class="font-bold text-[#0B6B44]" x-text="cairForm.jenis_komisi === 'komisi_kh' ? 'Komisi Humas (KH ~20%)' : 'Insentif Sales/AE (~10%)'"></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Penerima Uang</label>
                            <input type="text" name="penerima" x-model="cairForm.penerima" required 
                                   class="w-full text-xs py-2 px-3.5 rounded-xl border border-slate-200/80 focus:ring-2 focus:ring-[#0B6B44]/20 focus:border-[#0B6B44] text-slate-900 font-semibold bg-slate-50/50">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal Pencairan (Rp)</label>
                            <input type="number" name="nominal" x-model="cairForm.nominal" required min="1"
                                   class="w-full text-xs py-2 px-3.5 rounded-xl border border-slate-200/80 focus:ring-2 focus:ring-[#0B6B44]/20 focus:border-[#0B6B44] text-slate-900 font-mono font-bold bg-slate-50/50">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan Tambahan</label>
                            <input type="text" name="keterangan" x-model="cairForm.keterangan"
                                   class="w-full text-xs py-2 px-3.5 rounded-xl border border-slate-200/80 focus:ring-2 focus:ring-[#0B6B44]/20 focus:border-[#0B6B44] text-slate-800 bg-slate-50/50">
                        </div>
                    </div>

                    <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/60 flex items-center justify-end gap-2.5 shrink-0">
                        <button type="button" @click="showCairModal = false" class="px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200/80 transition shadow-2xs">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2.5 bg-[#0B6B44] hover:bg-[#095737] text-white font-bold text-xs rounded-xl transition shadow-xs">
                            Cairkan Uang & Terbitkan KO
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <!-- Modal Cetak BKK Komisi -->
        @if(session('cetak_komisi'))
        @php $ck = session('cetak_komisi'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-100 overflow-hidden">
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-slate-50/50">
                    <div>
                        <div class="text-base font-black tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500 font-semibold">DIVISI KASIR - SISTEM OPERASI KAS</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 text-[10px] font-black rounded bg-teal-100 text-teal-800 uppercase tracking-widest border border-teal-300">
                            VOUCHER KAS KELUAR (KO)
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1">{{ $ck['no_bkk'] }}</div>
                        <div class="text-[10px] text-slate-500">{{ $ck['tanggal'] }}</div>
                    </div>
                </div>

                <div class="print-document-body flex-1 overflow-y-auto p-5 space-y-2 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Jenis Komisi</span>
                        <span class="col-span-2 font-bold text-teal-800">: {{ $ck['jenis_komisi'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Diberikan Kepada</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $ck['penerima'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Referensi Faktur</span>
                        <span class="col-span-2 font-mono font-bold text-slate-900">: {{ $ck['no_bp'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Akun Beban (COA)</span>
                        <span class="col-span-2 font-mono text-slate-700">: Divisi Iklan - 6.00.01.01.01.02</span>
                    </div>
                    <div class="grid grid-cols-3 py-1.5 bg-teal-50/80 px-2 rounded-lg">
                        <span class="font-bold text-teal-900">Jumlah Kas Keluar</span>
                        <span class="col-span-2 font-mono font-black text-sm text-teal-800">: Rp {{ number_format($ck['nominal'], 0, ',', '.') }}</span>
                    </div>
                    <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                        <span class="text-[10px] text-slate-500 block uppercase font-bold">Terbilang:</span>
                        <span class="text-xs font-semibold italic text-slate-800">{{ $ck['terbilang'] }}</span>
                    </div>

                    <div class="grid grid-cols-3 pt-4 border-t border-slate-200 text-center text-[10px] text-slate-600">
                        <div>
                            <div>Penerima Hak</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900">({{ $ck['penerima'] }})</div>
                        </div>
                        <div>
                            <div>Manajer Iklan</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900">(....................)</div>
                        </div>
                        <div>
                            <div>Kasir Pembayar</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900 font-mono">({{ $ck['kasir'] }})</div>
                        </div>
                    </div>
                </div>

                <!-- Action Button Print & Close (Pinned Bottom) -->
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak BKK</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
