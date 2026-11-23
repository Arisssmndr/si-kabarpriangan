<x-app-layout>
    <div class="space-y-6" 
         @open-cetak-modal.window="showCetakModal = true"
         x-data="{ 
        showCairModal: false, 
        showCetakModal: false,
        cairForm: {
            faktur_ref: '',
            pemasang: '',
            jenis_komisi: 'komisi_kh',
            penerima: '',
            penerima_kh: '',
            penerima_ae: '',
            nominal: 0,
            nominal_kh: 0,
            nominal_ae: 0,
            keterangan: ''
        },
        openCair(faktur, pemasang, penerimaKh, penerimaAe, nominalKh, nominalAe) {
            this.cairForm.faktur_ref = faktur;
            this.cairForm.pemasang = pemasang;
            this.cairForm.penerima_kh = penerimaKh;
            this.cairForm.penerima_ae = penerimaAe;
            this.cairForm.nominal_kh = nominalKh;
            this.cairForm.nominal_ae = nominalAe;
            this.cairForm.jenis_komisi = 'komisi_kh';
            this.syncJenisKomisi();
            this.showCairModal = true;
        },
        syncJenisKomisi() {
            if (this.cairForm.jenis_komisi === 'komisi_kh') {
                this.cairForm.penerima = this.cairForm.penerima_kh;
                this.cairForm.nominal = this.cairForm.nominal_kh;
                this.cairForm.keterangan = 'Pencairan Komisi Humas (KH) atas Faktur ' + this.cairForm.faktur_ref + ' (' + this.cairForm.pemasang + ')';
            } else {
                this.cairForm.penerima = this.cairForm.penerima_ae;
                this.cairForm.nominal = this.cairForm.nominal_ae;
                this.cairForm.keterangan = 'Pencairan Insentif AE / Sales atas Faktur ' + this.cairForm.faktur_ref + ' (' + this.cairForm.pemasang + ')';
            }
        }
    }">

        <!-- Konten Halaman Kasir (Disembunyikan saat modal cetak aktif) -->
        <div class="space-y-6" :class="{ 'print:hidden': showCetakModal }">

            <!-- Flash Message Alert -->
            @if(session('success'))
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-slate-400 hover:text-slate-700 text-sm font-bold">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-rose-700 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-slate-400 hover:text-slate-700 text-sm font-bold">&times;</button>
                </div>
            @endif

            <!-- 1. Header Standar Divisi Iklan -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Logo Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                                Pencairan Komisi KH & Insentif AE (Divisi Iklan)
                            </h1>
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-sky-50 text-[#0E527B] border border-sky-200">
                                COA 6.00.01.01.01.02
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Nominal komisi & insentif ditarik langsung dari data penjualan iklan yang fakturnya telah berstatus LUNAS.
                        </p>
                    </div>
                </div>

                @if(session('cetak_komisi'))
                    <button type="button" @click="showCetakModal = true" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs rounded-xl transition shadow-xs flex items-center gap-2 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Bukti Pencairan Komisi</span>
                    </button>
                @endif
            </div>

            <!-- 2. KPI Cards Standar Divisi Iklan -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Faktur Lunas Siap Cair</span>
                    <p class="mt-2 text-2xl font-bold font-mono text-slate-900">
                        {{ $fakturLunas->count() }} <span class="text-xs font-normal text-slate-400">Faktur</span>
                    </p>
                    <span class="mt-1 text-[11px] text-slate-400 block">Status siap pencairan KH & Insentif AE</span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Hak Komisi Humas (KH)</span>
                    <p class="mt-2 text-2xl font-bold font-mono text-kp-blue-700">
                        Rp {{ number_format($fakturLunas->sum(fn($f) => $f->komisi_transaksikoran > 0 ? $f->komisi_transaksikoran : round(($f->subtotal ?? $f->totaltagihan_transaksikoran ?? 0) * 0.20)), 0, ',', '.') }}
                    </p>
                    <span class="mt-1 text-[11px] text-slate-400 block">Akumulasi nilai komisi KH dari faktur lunas</span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Hak Insentif AE / Sales</span>
                    <p class="mt-2 text-2xl font-bold font-mono text-slate-900">
                        Rp {{ number_format($fakturLunas->sum(fn($f) => $f->insentif_transaksikoran > 0 ? $f->insentif_transaksikoran : round(($f->subtotal ?? $f->totaltagihan_transaksikoran ?? 0) * 0.10)), 0, ',', '.') }}
                    </p>
                    <span class="mt-1 text-[11px] text-slate-400 block">Akumulasi nilai insentif AE dari faktur lunas</span>
                </div>
            </div>

            <!-- 3. Filter Bar Terpisah -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <form method="GET" action="{{ route('kasir.pengeluaran.komisi') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <div class="sm:col-span-7">
                        <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Pencarian Data</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari No Faktur Koran, nama pemasang, atau nama sales/AE..."
                               class="h-11 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600">
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ $tanggal ?? request('tanggal') }}"
                               class="h-11 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600">
                    </div>
                    <div class="sm:col-span-2 flex items-center gap-2">
                        <button type="submit" class="h-11 w-full bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-xs cursor-pointer flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>Cari</span>
                        </button>
                        @if(!empty($search) || !empty($tanggal))
                            <a href="{{ route('kasir.pengeluaran.komisi') }}" title="Reset Filter" class="h-11 px-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- 4. Pure Data Table: Faktur Lunas Siap Cair (Tanpa Banner Strip) -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-emerald-700 border-b border-emerald-800 text-white font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-4">No. Faktur</th>
                                <th class="py-3 px-4">Klien / Pemasang</th>
                                <th class="py-3 px-4">Sales / AE</th>
                                <th class="py-3 px-4 text-right">Nilai Faktur Lunas (Rp)</th>
                                <th class="py-3 px-4 text-right">Komisi KH (Rp)</th>
                                <th class="py-3 px-4 text-right">Insentif AE (Rp)</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800">
                            @forelse($fakturLunas as $faktur)
                            @php
                                $subtotal = (float) ($faktur->subtotal ?? $faktur->totaltagihan_transaksikoran ?? $faktur->jumlahbayar_transaksikoran ?? 0);
                                $khNominal = (float) ($faktur->komisi_transaksikoran > 0 ? $faktur->komisi_transaksikoran : round($subtotal * 0.20));
                                $aeNominal = (float) ($faktur->insentif_transaksikoran > 0 ? $faktur->insentif_transaksikoran : round($subtotal * 0.10));
                                $penerimaKh = $faktur->komisioner ?: ($faktur->nama_pic ?: 'Mitra Humas Terkait');
                                $penerimaAe = $faktur->sales_iklankoran ?: 'Sales AE';
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 font-mono font-bold text-slate-900 whitespace-nowrap">{{ $faktur->nofakturkoran }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-900">{{ $faktur->nama_pemasangkoran }}</td>
                                <td class="py-3 px-4 text-slate-600">
                                    <span class="px-2 py-0.5 bg-slate-100 rounded text-xs font-semibold text-slate-700">
                                        {{ $faktur->sales_iklankoran ?? 'Direct Sales' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-kp-blue-700 whitespace-nowrap">
                                    Rp {{ number_format($khNominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-700 whitespace-nowrap">
                                    Rp {{ number_format($aeNominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <button type="button"
                                            @click="openCair('{{ $faktur->nofakturkoran }}', '{{ addslashes($faktur->nama_pemasangkoran) }}', '{{ addslashes($penerimaKh) }}', '{{ addslashes($penerimaAe) }}', {{ $khNominal }}, {{ $aeNominal }})"
                                            class="h-8 px-3.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs rounded-xl transition shadow-xs inline-flex items-center gap-1.5 cursor-pointer">
                                        <span>Cairkan</span>
                                    </button>
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

            <!-- 5. Pure Data Table: Riwayat Pencairan Komisi (Tanpa Banner Strip) -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-emerald-700 border-b border-emerald-800 text-white font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-4">No. BKK (KO)</th>
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">Faktur Ref</th>
                                <th class="py-3 px-4">Jenis Beban</th>
                                <th class="py-3 px-4">Penerima Dana</th>
                                <th class="py-3 px-4 text-right">Jumlah Dicairkan (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800">
                            @forelse($riwayatKomisi as $ko)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 font-mono font-bold text-slate-900 whitespace-nowrap">{{ $ko->no_bkk }}</td>
                                <td class="py-3 px-4 text-slate-600 font-mono text-xs whitespace-nowrap">
                                    {{ $ko->tanggal_keluar ? \Carbon\Carbon::parse($ko->tanggal_keluar)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-3 px-4 font-mono text-xs text-kp-blue-700 font-semibold">{{ $ko->no_bp_ref }}</td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $ko->kategori_beban === 'komisi_kh' ? 'bg-sky-50 text-[#0E527B] border border-sky-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                        {{ $ko->kategori_beban === 'komisi_kh' ? 'Komisi Humas (KH)' : 'Insentif Sales / AE' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-semibold text-slate-800">{{ $ko->diberikan_kepada ?? $ko->nama_penerima }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                    Rp {{ number_format($ko->jumlah_keluar, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                    Belum ada riwayat pencairan komisi.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Form Pencairan Komisi / Terbitkan KO -->
            <div x-cloak x-show="showCairModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
                <div @click.away="showCairModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-xl border border-slate-100 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between shrink-0 bg-slate-50/50">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-sky-50 text-[#0E527B] border border-sky-200 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Pencairan Hak Komisi / Insentif (KO)</h4>
                                <p class="text-[11px] text-slate-500">Divisi Iklan - COA 6.00.01.01.01.02</p>
                            </div>
                        </div>
                        <button type="button" @click="showCairModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('kasir.pengeluaran.komisi.store') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                        @csrf
                        <input type="hidden" name="faktur_ref" :value="cairForm.faktur_ref">

                        <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 text-xs space-y-1.5">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Dasar Faktur Lunas:</span>
                                    <span class="font-mono font-bold text-slate-900" x-text="cairForm.faktur_ref"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Pemasang:</span>
                                    <span class="font-semibold text-slate-800" x-text="cairForm.pemasang"></span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Pencairan Komisi / Insentif</label>
                                <select name="jenis_komisi" x-model="cairForm.jenis_komisi" @change="syncJenisKomisi()" class="h-10 w-full px-3 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                    <option value="komisi_kh">Komisi Humas (KH)</option>
                                    <option value="insentif_ae">Insentif AE / Sales</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Penerima Uang (AE / Sales / Mitra)</label>
                                <input type="text" name="penerima" x-model="cairForm.penerima" required 
                                       class="h-10 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Pencairan (Rp)</label>
                                <input type="number" name="nominal" x-model="cairForm.nominal" required min="1"
                                       class="h-10 w-full px-3.5 bg-white border border-slate-300 rounded-xl font-mono font-bold text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan Tambahan</label>
                                <input type="text" name="keterangan" x-model="cairForm.keterangan"
                                       class="h-10 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600">
                            </div>
                        </div>

                        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0">
                            <button type="button" @click="showCairModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs rounded-xl transition shadow-xs cursor-pointer">
                                Cairkan Uang & Terbitkan KO
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- Modal Cetak BKK Komisi - Format Continuous Form Dot Matrix -->
        @if(session('cetak_komisi'))
        @php $ck = session('cetak_komisi'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="max-w-4xl w-full max-h-[92vh] flex flex-col">
                <div class="mb-3 flex items-center justify-between print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs font-sans cursor-pointer">
                        ← Kembali ke Daftar Pencairan Komisi
                    </button>
                    <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition cursor-pointer font-sans">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Lembar BKK Komisi (Print / PDF)</span>
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
                            <h1 class="text-base sm:text-lg font-black tracking-wider uppercase font-dot-matrix text-black">
                                BUKTI PENCAIRAN KOMISI & INSENTIF (KO-01) HU. PRIANGAN
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
                                    <span class="font-bold">6.00.01.01.01.02</span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Penerima</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold uppercase">{{ $ck['penerima'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Divisi</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold">DIVISI IKLAN (KOMISI & INSENTIF)</span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Kota</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold">TASIKMALAYA</span>
                                </div>
                            </div>

                            <div class="col-span-5 space-y-0.5">
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">No. BKK / KO</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold font-mono text-blue-900">{{ $ck['no_bkk'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">Ref. Faktur</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold font-mono">{{ $ck['no_bp'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">Jenis Komisi</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold">{{ $ck['jenis_komisi'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">Tanggal</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold">{{ $ck['tanggal'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-3"></div>

                        <div class="grid grid-cols-12 gap-x-3 gap-y-1 mb-3">
                            <div class="col-span-4 flex">
                                <span class="w-20 shrink-0 font-bold whitespace-nowrap">Kategori</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase">KOMISI & INSENTIF IKLAN</span>
                            </div>
                            <div class="col-span-3 flex">
                                <span class="w-12 shrink-0 font-bold whitespace-nowrap">Tipe</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase">KO-01</span>
                            </div>
                            <div class="col-span-5 flex">
                                <span class="w-24 shrink-0 font-bold whitespace-nowrap">Keterangan</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase text-[11px] leading-tight">PENCAIRAN {{ $ck['jenis_komisi'] }} ATAS FAKTUR LUNAS {{ $ck['no_bp'] }}</span>
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-3"></div>

                        <div class="space-y-1 max-w-lg mb-4 font-bold">
                            <div class="flex justify-between">
                                <span>Brutto Komisi / Insentif</span>
                                <span>Rp. {{ number_format($ck['nominal'], 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Potongan Pajak</span>
                                <span>Rp. 0,00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Netto Diterima</span>
                                <span>Rp. {{ number_format($ck['nominal'], 2, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-dashed border-black pt-1 flex justify-between text-sm font-black">
                                <span>Jumlah Total Kas Keluar</span>
                                <span>Rp. {{ number_format($ck['nominal'], 2, ',', '.') }}</span>
                            </div>
                            <div class="pt-1 text-[11px] italic font-bold">
                                <span class="not-italic">Terbilang :</span> # {{ $ck['terbilang'] }} #
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-6"></div>

                        <div class="grid grid-cols-2 gap-4 items-end pt-2">
                            <div class="pl-4">
                                <div class="inline-block border-2 border-emerald-600 text-emerald-700 px-5 py-2 rounded-full font-black text-center rotate-[-4deg] opacity-90">
                                    <div class="text-[9px] uppercase tracking-widest">KOMISI & INSENTIF</div>
                                    <div class="text-sm tracking-wider border-t border-b border-emerald-600 my-0.5 py-0.5">T E R B A Y A R</div>
                                    <div class="text-[8px] font-mono">DIVISI KEUANGAN</div>
                                </div>
                            </div>

                            <div class="text-right flex flex-col gap-5">
                                <div>
                                    <p class="text-[11px] font-bold">Tasikmalaya, {{ date('d-m-Y') }}</p>
                                    <p class="text-[11px] font-semibold mt-0.5">Kasir Pembayar,</p>
                                    <div class="h-10"></div>
                                    <p class="font-bold underline text-[11px] uppercase">{{ $ck['kasir'] }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold">Penerima (AE / Sales),</p>
                                    <div class="h-10"></div>
                                    <p class="font-bold underline text-[11px] uppercase">{{ $ck['penerima'] }}</p>
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
        @endif

    </div>
</x-app-layout>