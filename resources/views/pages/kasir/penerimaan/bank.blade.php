<x-app-layout>
    <div class="space-y-5" x-data="{
        showCetakModal: {{ session('cetak_bank') ? 'true' : 'false' }},
        bankSumber: 'Bank BJB',
        kodeCoa: '1.02.01.01.01',
        nominal: '',
        formatRupiah(val) {
            if (!val) return 'Rp 0';
            let num = parseInt(val.toString().replace(/[^0-9]/g, '')) || 0;
            return 'Rp ' + num.toLocaleString('id-ID');
        }
    }">

        <!-- Konten Halaman Kasir (Disembunyikan saat modal cetak aktif) -->
        <div class="space-y-5" :class="{ 'print-hide-when-modal-open print:hidden': showCetakModal }">

            <!-- Header Halaman (Palet 60-30-10) -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 no-print print:hidden">
                <div class="flex items-center gap-3.5 min-w-0">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                                Penarikan Tunai Kas Bank
                            </h1>
                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">
                                KI-09
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                            Pencatatan penarikan dana cek atau bilyet giro bank ke brankas kasir (COA 1.02.01.01.01).
                        </p>
                    </div>
                </div>

                @if(session('cetak_bank'))
                    <div class="shrink-0">
                        <button type="button" 
                                @click="showCetakModal = true" 
                                class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Cetak Bukti KI-09</span>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Flash Message Alert -->
            @if(session('success'))
                <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                    <span>{{ session('success') }}</span>
                    <button type="button" @click="$el.parentElement.remove()" class="text-kp-blue-700 hover:text-kp-blue-900 font-bold ml-3 cursor-pointer">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-3.5 bg-slate-100 border border-slate-300 text-slate-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                    <span>{{ session('error') }}</span>
                    <button type="button" @click="$el.parentElement.remove()" class="text-slate-600 hover:text-slate-900 font-bold ml-3 cursor-pointer">&times;</button>
                </div>
            @endif

            <!-- Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Penarikan Bulan Ini</span>
                    <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($totalBankBulanIni, 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">Dana cair dari giro ke brankas</span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pencairan Bank BJB</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($totalTarikBjbBulanIni, 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block font-mono">Giro 05.00.01.006270.9</span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pencairan Bank BCA</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($totalTarikBcaBulanIni, 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">Rekening operasional kas BCA</span>
                </div>
            </div>

            <!-- Layout 2 Kolom: Form Penarikan Kas Bank & Tabel Mutasi -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
                <!-- Kolom 1: Form Input Kas Bank Masuk -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden lg:col-span-1">
                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-900">Input Penarikan Cek (KI-09)</h3>
                        <p class="text-xs text-slate-500">Catat pencairan cek/giro ke kasir</p>
                    </div>

                    <form method="POST" action="{{ route('kasir.penerimaan.bank.store') }}" class="p-4 sm:p-5 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bank Sumber Giro</label>
                            <select name="bank_sumber" x-model="bankSumber" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                <option value="Bank BJB">Bank BJB Tasikmalaya (05.00.01.006270.9)</option>
                                <option value="Bank BCA">Bank BCA Tasikmalaya</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Divisi</label>
                                <input type="text" value="Kasir (Bank)" disabled 
                                       class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-100 text-sm font-semibold text-slate-600">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kode COA</label>
                                <input type="text" name="kode_perkiraan" x-model="kodeCoa" readonly
                                       class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-slate-100 font-mono font-bold text-kp-blue-700 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">No. Cek / Bilyet Giro</label>
                            <input type="text" name="no_cek_giro" required placeholder="Contoh: CK-BJB-892182"
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Petugas Penarik</label>
                            <input type="text" name="penarik" required placeholder="Nama petugas penarik ke bank"
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">No. Blanko</label>
                                <input type="text" name="no_blanko" placeholder="Opsional"
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nominal Cair (Rp)</label>
                                <input type="number" name="nominal" x-model="nominal" required min="1" placeholder="0"
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            </div>
                        </div>

                        <div class="p-3 bg-kp-blue-50 rounded-xl border border-kp-blue-200 flex items-center justify-between text-xs">
                            <span class="font-semibold text-kp-blue-800">Penambahan Kas Fisik:</span>
                            <span class="font-mono font-bold text-sm text-kp-blue-700 tabular-nums" x-text="formatRupiah(nominal)"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Keterangan</label>
                            <textarea name="keterangan" rows="2" placeholder="Keperluan penarikan kas..."
                                      class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"></textarea>
                        </div>

                        <button type="submit" class="w-full h-11 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                            <span>Simpan Penarikan (KI-09)</span>
                        </button>
                    </form>
                </div>

                <!-- Kolom 2: Daftar Mutasi Bank Masuk -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden lg:col-span-2">
                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Riwayat Penarikan Cek Bank (KI-09)</h3>
                            <p class="text-xs text-slate-500">Mutasi pencairan rekening giro ke kasir</p>
                        </div>
                        <form method="GET" action="{{ route('kasir.penerimaan.bank') }}" class="flex flex-wrap items-center gap-2">
                            <select name="bank" onchange="this.form.submit()" class="h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 focus:outline-none focus:border-kp-blue-600 transition">
                                <option value="all" {{ ($bankFilter ?? 'all') == 'all' ? 'selected' : '' }}>Semua Bank</option>
                                <option value="BJB" {{ ($bankFilter ?? '') == 'BJB' ? 'selected' : '' }}>Bank BJB</option>
                                <option value="BCA" {{ ($bankFilter ?? '') == 'BCA' ? 'selected' : '' }}>Bank BCA</option>
                            </select>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari kuitansi / cek..." 
                                   class="h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 transition">
                            <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm rounded-xl font-bold transition cursor-pointer">Cari</button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                <tr>
                                    <th class="py-3 px-3.5 text-center w-12">No</th>
                                    <th class="py-3 px-3.5">No. Kuitansi</th>
                                    <th class="py-3 px-3.5">Tanggal</th>
                                    <th class="py-3 px-3.5">Bank &amp; No. Cek</th>
                                    <th class="py-3 px-3.5">Penarik</th>
                                    <th class="py-3 px-3.5 text-right">Nominal Cair</th>
                                    <th class="py-3 px-3.5 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                                @forelse($bankList as $key => $bank)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                        {{ $bankList->firstItem() + $key }}
                                    </td>
                                    <td class="py-3.5 px-3.5">
                                        <div class="font-mono font-bold text-kp-blue-700 text-sm">{{ $bank->no_kuitansi ?? $bank->no_bkm }}</div>
                                        @if($bank->no_blanko)
                                            <div class="text-xs text-slate-400 font-mono">Blanko: {{ $bank->no_blanko }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono text-slate-600">
                                        {{ $bank->tanggal_terima ? \Carbon\Carbon::parse($bank->tanggal_terima)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="py-3.5 px-3.5">
                                        <div class="font-semibold text-slate-900">{{ $bank->bank_tujuan ?? $bank->terima_dari }}</div>
                                        <div class="font-mono text-xs text-slate-500">Cek: {{ $bank->no_referensi_transfer ?? '-' }}</div>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-slate-700">
                                        <div class="font-medium">{{ $bank->penagih_inkaso ?? '-' }}</div>
                                        <div class="text-xs text-slate-400 truncate max-w-[150px]">{{ $bank->keterangan }}</div>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono tabular-nums font-semibold text-slate-900">
                                        Rp {{ number_format($bank->jumlah_diterima ?? $bank->jumlah_dibayar, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Masuk Kas
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-10 text-center text-slate-500 text-sm">
                                        Belum ada transaksi penarikan kas bank masuk.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($bankList->hasPages())
                    <div class="p-3.5 border-t border-slate-200 bg-slate-50">
                        {{ $bankList->links() }}
                    </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Modal Cetak Dokumen KI-09 -->
        @if(session('cetak_bank'))
        @php $cb = session('cetak_bank'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-white">
                    <div>
                        <div class="text-base font-bold tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[11px] text-slate-500 font-semibold">DIVISI KASIR - BUKTI PENARIKAN KAS BANK</div>
                        <div class="text-[11px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya</div>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold rounded-md bg-kp-blue-50 text-kp-blue-700 uppercase tracking-wider border border-kp-blue-200">
                            BUKTI KAS MASUK (KI-09)
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1">{{ $cb['no_kuitansi'] }}</div>
                        <div class="text-[11px] text-slate-500 font-mono">{{ $cb['tanggal'] }}</div>
                    </div>
                </div>

                <div class="print-document-body flex-1 overflow-y-auto p-5 space-y-2.5 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Bank Sumber</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $cb['bank'] }} Cabang Tasikmalaya</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">No. Cek / Bilyet Giro</span>
                        <span class="col-span-2 font-mono font-bold text-slate-900">: {{ $cb['no_cek'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Petugas Penarik</span>
                        <span class="col-span-2 font-semibold text-slate-900">: {{ $cb['penarik'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Bagan Akun (COA)</span>
                        <span class="col-span-2 font-mono text-slate-700">: {{ $cb['kode_perkiraan'] }} (Bank Penerbitan)</span>
                    </div>
                    <div class="grid grid-cols-3 py-2 bg-kp-blue-50 px-3 rounded-xl border border-kp-blue-200 my-1">
                        <span class="font-bold text-kp-blue-800">Jumlah Uang Diterima</span>
                        <span class="col-span-2 font-mono font-bold text-base text-kp-blue-700">: Rp {{ number_format($cb['nominal'], 0, ',', '.') }}</span>
                    </div>
                    <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                        <span class="text-[10px] text-slate-500 block uppercase font-bold">Terbilang:</span>
                        <span class="text-xs font-semibold italic text-slate-900">{{ $cb['terbilang'] }}</span>
                    </div>

                    <div class="grid grid-cols-2 pt-4 border-t border-slate-200 text-center text-[11px] text-slate-600">
                        <div>
                            <div>Petugas Penarik Cek</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900">({{ $cb['penarik'] }})</div>
                        </div>
                        <div>
                            <div>Kasir Penerima Uang</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900 font-mono">({{ $cb['kasir'] }})</div>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="h-10 px-4 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl border border-slate-300 transition cursor-pointer">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
