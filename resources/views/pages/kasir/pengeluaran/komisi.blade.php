<x-app-layout>
    <div class="space-y-5" x-data="{ 
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
        <div class="space-y-5" :class="{ 'print:hidden print-hide-when-modal-open': showCetakModal }">

            <!-- Header Halaman (60-30-10) -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-3.5 min-w-0">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                    <div class="min-w-0">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Pencairan Komisi KH & Insentif AE
                        </h1>
                        <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                            Pencairan komisi humas (20%) dan insentif sales/AE (10%) atas faktur iklan lunas (COA 6.00.01.01.01.02).
                        </p>
                    </div>
                </div>

                @if(session('cetak_komisi'))
                    <div class="flex items-center gap-2.5 shrink-0">
                        <button type="button" @click="showCetakModal = true" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak Voucher Komisi</span>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                    <span>{{ session('success') }}</span>
                    <button type="button" @click="$el.parentElement.remove()" class="text-kp-blue-600 hover:text-kp-blue-800 font-bold px-1 cursor-pointer">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-3.5 bg-slate-100 border border-slate-300 text-slate-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                    <span>{{ session('error') }}</span>
                    <button type="button" @click="$el.parentElement.remove()" class="text-slate-500 hover:text-slate-800 font-bold px-1 cursor-pointer">&times;</button>
                </div>
            @endif

            <!-- 3 Kartu KPI -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Faktur Siap Cairkan Komisi</span>
                    <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                        {{ $fakturLunas->count() }} <span class="text-xs font-sans font-normal text-slate-400">Faktur Lunas</span>
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">
                        Siap pencairan KH & insentif AE
                    </span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Alokasi Komisi Humas (20%)</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($fakturLunas->sum(fn($f) => $f->subtotal * 0.20), 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">
                        Estimasi hak mitra humas
                    </span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Alokasi Insentif AE (10%)</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($fakturLunas->sum(fn($f) => $f->subtotal * 0.10), 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">
                        Estimasi hak marketing internal
                    </span>
                </div>
            </div>

            <!-- Toolbar Pencarian -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <form method="GET" action="{{ route('kasir.pengeluaran.komisi') }}" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Faktur Lunas</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Ketik no faktur koran, nama pemasang, atau sales/AE..."
                               class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="h-11 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer">
                            Cari
                        </button>
                        @if(!empty($search))
                            <a href="{{ route('kasir.pengeluaran.komisi') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabel Faktur Lunas Siap Dicairkan -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Daftar Faktur Iklan Lunas</h2>
                        <p class="text-xs text-slate-500">Berdasarkan pelunasan kasir (KI-01)</p>
                    </div>
                    <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                        {{ $fakturLunas->count() }} Faktur
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-3.5">No. Faktur</th>
                                <th class="py-3 px-3.5">Pemasang</th>
                                <th class="py-3 px-3.5">Sales / AE</th>
                                <th class="py-3 px-3.5 text-right">Nilai Faktur</th>
                                <th class="py-3 px-3.5 text-right">Komisi KH (20%)</th>
                                <th class="py-3 px-3.5 text-right">Insentif AE (10%)</th>
                                <th class="py-3 px-3.5 text-center">Aksi Pencairan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                            @forelse($fakturLunas as $idx => $faktur)
                            @php
                                $subtotal = (float) ($faktur->subtotal ?? $faktur->totalbayar_transaksikoran ?? 0);
                                $khNominal = round($subtotal * 0.20);
                                $aeNominal = round($subtotal * 0.10);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">{{ $faktur->nofakturkoran }}</td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $faktur->nama_pemasangkoran }}</td>
                                <td class="py-3.5 px-3.5 text-slate-600">{{ $faktur->sales_iklankoran ?? 'Direct Sales' }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-kp-blue-700 font-semibold tabular-nums">
                                    Rp {{ number_format($khNominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">
                                    Rp {{ number_format($aeNominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button"
                                                @click="openCair('{{ $faktur->nofakturkoran }}', '{{ addslashes($faktur->nama_pemasangkoran) }}', 'komisi_kh', 'Humas Mitra Terkait', {{ $khNominal }})"
                                                class="px-3 py-1.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-lg transition cursor-pointer">
                                            Cairkan KH
                                        </button>
                                        <button type="button"
                                                @click="openCair('{{ $faktur->nofakturkoran }}', '{{ addslashes($faktur->nama_pemasangkoran) }}', 'insentif_ae', '{{ addslashes($faktur->sales_iklankoran ?? 'Sales AE') }}', {{ $aeNominal }})"
                                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-lg transition cursor-pointer">
                                            Cairkan AE
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-500">
                                    Tidak ada faktur lunas yang memerlukan pencairan komisi.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel Riwayat Pencairan Komisi (KO) -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Riwayat Pencairan Komisi & Insentif</h2>
                        <p class="text-xs text-slate-500">Bukti kas keluar (BKK / KO) komisi terakhir</p>
                    </div>
                    <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                        {{ $riwayatKomisi->count() }} BKK
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-3.5">No. BKK (KO)</th>
                                <th class="py-3 px-3.5">Tanggal</th>
                                <th class="py-3 px-3.5">Faktur Ref</th>
                                <th class="py-3 px-3.5">Jenis Beban</th>
                                <th class="py-3 px-3.5">Penerima Dana</th>
                                <th class="py-3 px-3.5 text-right">Jumlah Dicairkan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                            @forelse($riwayatKomisi as $idx => $ko)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">{{ $ko->no_bkk }}</td>
                                <td class="py-3.5 px-3.5 font-mono text-slate-600">{{ $ko->tanggal_keluar ? \Carbon\Carbon::parse($ko->tanggal_keluar)->format('d/m/Y') : '-' }}</td>
                                <td class="py-3.5 px-3.5 font-mono text-slate-700">{{ $ko->no_bp_ref }}</td>
                                <td class="py-3.5 px-3.5">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium {{ $ko->kategori_beban === 'komisi_kh' ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                        {{ $ko->kategori_beban === 'komisi_kh' ? 'Komisi Humas (KH)' : 'Insentif Sales / AE' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $ko->diberikan_kepada ?? $ko->nama_penerima }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">Rp {{ number_format($ko->jumlah_keluar, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500">Belum ada riwayat pencairan komisi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Form Pencairan Komisi -->
            <div x-cloak x-show="showCairModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
                <div @click.outside="showCairModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Pencairan Komisi / Insentif</h3>
                            <p class="text-xs text-slate-500 font-mono">COA 6.00.01.01.01.02</p>
                        </div>
                        <button type="button" @click="showCairModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('kasir.pengeluaran.komisi.store') }}" class="p-5 space-y-4">
                        @csrf
                        <input type="hidden" name="faktur_ref" :value="cairForm.faktur_ref">
                        <input type="hidden" name="jenis_komisi" :value="cairForm.jenis_komisi">

                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 text-xs space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Dasar Faktur:</span>
                                <span class="font-mono font-bold text-kp-blue-700" x-text="cairForm.faktur_ref"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Pemasang:</span>
                                <span class="font-semibold text-slate-900" x-text="cairForm.pemasang"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Jenis Pencairan:</span>
                                <span class="font-semibold text-kp-blue-700" x-text="cairForm.jenis_komisi === 'komisi_kh' ? 'Komisi Humas (KH 20%)' : 'Insentif Sales/AE (10%)'"></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Penerima Uang</label>
                            <input type="text" name="penerima" x-model="cairForm.penerima" required 
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Pencairan (Rp)</label>
                            <input type="number" name="nominal" x-model="cairForm.nominal" required min="1"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan</label>
                            <input type="text" name="keterangan" x-model="cairForm.keterangan"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600">
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="button" @click="showCairModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                                Cairkan & Terbitkan KO
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Cetak BKK Komisi -->
        @if(session('cetak_komisi'))
        @php $ck = session('cetak_komisi'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-white">
                    <div>
                        <div class="text-base font-bold tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500 font-semibold">DIVISI KASIR - SISTEM OPERASI KAS</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-kp-blue-50 text-kp-blue-700 uppercase tracking-wider border border-kp-blue-200">
                            VOUCHER KAS KELUAR (KO)
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1">{{ $ck['no_bkk'] }}</div>
                        <div class="text-[10px] text-slate-500">{{ $ck['tanggal'] }}</div>
                    </div>
                </div>

                <div class="print-document-body flex-1 overflow-y-auto p-5 space-y-2 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Jenis Komisi</span>
                        <span class="col-span-2 font-bold text-kp-blue-700">: {{ $ck['jenis_komisi'] }}</span>
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
                    <div class="grid grid-cols-3 py-2 bg-slate-50 px-3 rounded-xl border border-slate-200">
                        <span class="font-bold text-slate-900">Jumlah Kas Keluar</span>
                        <span class="col-span-2 font-mono font-bold text-sm text-kp-blue-700">: Rp {{ number_format($ck['nominal'], 0, ',', '.') }}</span>
                    </div>
                    <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200">
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

                <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
