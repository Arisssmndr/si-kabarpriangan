<x-app-layout>
    <div class="space-y-5" x-data="{ 
        showFormModal: false,
        showCetakModal: {{ session('cetak_tunai') ? 'true' : 'false' }},
        tunaiForm: {
            pembeli: '',
            kategori: 'Iklan Baris / Kolom Kecil',
            keterangan: '',
            jumlah: '',
            metode: 'Tunai Kasir'
        }
    }">

        <!-- Konten Halaman Kasir (Disembunyikan saat modal cetak aktif) -->
        <div class="space-y-5" :class="{ 'print-hide-when-modal-open print:hidden': showCetakModal }">

            <!-- Header Halaman (Palet 60-30-10) -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 no-print print:hidden">
                <div class="flex items-center gap-3.5 min-w-0">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                    <div class="min-w-0">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Penjualan Langsung / Kas Masuk Tunai
                        </h1>
                        <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                            Pencatatan kas masuk transaksi langsung tanpa tempo (iklan lepas, koran eceran, dan afval).
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    @if(session('cetak_tunai'))
                        <button type="button" 
                                @click="showCetakModal = true" 
                                class="inline-flex items-center gap-2 h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer whitespace-nowrap">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Cetak Ulang Nota</span>
                        </button>
                    @endif
                    <button type="button" 
                            @click="showFormModal = true" 
                            class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Input Transaksi Tunai</span>
                    </button>
                </div>
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

            <!-- KPI Ringkasan Penerimaan Tunai -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kas Tunai Tercatat</span>
                    <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($recentTunai->sum('jumlah_dibayar'), 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">Akumulasi riwayat penjualan langsung</span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Volume Transaksi</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                        {{ number_format($recentTunai->count()) }} <span class="text-xs font-sans font-normal text-slate-500">Nota</span>
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">Kwitansi kas masuk tunai</span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kategori Layanan</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                        4 <span class="text-xs font-sans font-normal text-slate-500">Kategori</span>
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">Iklan lepas, eceran, afval &amp; lainnya</span>
                </div>
            </div>

            <!-- Tabel Riwayat Penjualan Langsung -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Riwayat Transaksi Penjualan Langsung</h3>
                        <p class="text-xs text-slate-500">Daftar nota kas masuk tunai loket kasir</p>
                    </div>
                    <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                        {{ $recentTunai->count() }} Nota
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-3.5">No. BKM / Kwitansi</th>
                                <th class="py-3 px-3.5">Tanggal</th>
                                <th class="py-3 px-3.5">Pembeli / Pembayar</th>
                                <th class="py-3 px-3.5">Uraian Transaksi</th>
                                <th class="py-3 px-3.5">Metode</th>
                                <th class="py-3 px-3.5">Kasir</th>
                                <th class="py-3 px-3.5 text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                            @forelse($recentTunai as $t)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    {{ $t->no_bkm }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-slate-600">
                                    {{ $t->tgl_transaksi->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    {{ $t->nama_pembayar }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-700 max-w-xs truncate" title="{{ $t->keterangan }}">
                                    {{ $t->keterangan }}
                                </td>
                                <td class="py-3.5 px-3.5">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200 uppercase">
                                        {{ $t->metode_bayar }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600">
                                    {{ $t->kasir->name ?? 'Kasir' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums font-semibold text-slate-900">
                                    Rp {{ number_format($t->jumlah_dibayar, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-slate-500 text-sm">
                                    Belum ada transaksi penjualan langsung tercatat.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Form Input Penjualan Langsung -->
            <div x-cloak x-show="showFormModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
                <div @click.away="showFormModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                    <div class="px-5 py-3.5 bg-white border-b border-slate-200 flex items-center justify-between shrink-0">
                        <div>
                            <h4 class="text-base font-bold text-slate-900">Input Penjualan Langsung</h4>
                            <p class="text-xs text-slate-500">Catat kas masuk tunai dan terbitkan nota kasir</p>
                        </div>
                        <button type="button" @click="showFormModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-xl leading-none cursor-pointer">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('kasir.penerimaan.tunai.store') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                        @csrf

                        <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Pembeli / Pembayar</label>
                                <input type="text" name="pembeli" x-model="tunaiForm.pembeli" required placeholder="Contoh: Bpk. Hendra / Dinas Pendidikan" 
                                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kategori Transaksi</label>
                                <select name="kategori" x-model="tunaiForm.kategori" required class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                    <option value="Iklan Baris / Kolom Kecil">Iklan Baris / Kolom Kecil (Kehilangan STNK/Duka Cita)</option>
                                    <option value="Pembelian Koran Eceran Sisa Stock">Pembelian Koran Eceran Kantor</option>
                                    <option value="Penjualan Kertas Afval / Koran Bekas">Penjualan Kertas Afval / Limbah Cetak</option>
                                    <option value="Pendapatan Kasir Lainnya">Pendapatan Kasir Lain-Lain</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Uraian / Keterangan Transaksi</label>
                                <textarea name="keterangan" x-model="tunaiForm.keterangan" required rows="2" placeholder="Uraian singkat transaksi..." 
                                          class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nominal Diterima (Rp)</label>
                                    <input type="number" name="jumlah" x-model="tunaiForm.jumlah" required min="1" placeholder="75000" 
                                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Metode Pembayaran</label>
                                    <select name="metode" x-model="tunaiForm.metode" required class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                        <option value="Tunai Kasir">Tunai Meja Kasir</option>
                                        <option value="Transfer QRIS">Transfer QRIS Resmi</option>
                                        <option value="Transfer Bank BJB">Transfer Bank BJB</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="px-5 py-3.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0">
                            <button type="button" @click="showFormModal = false" class="h-10 px-4 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl border border-slate-300 transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition cursor-pointer">
                                Simpan &amp; Cetak Nota
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- Modal Cetak Nota Penjualan Langsung Otomatis -->
        @if(session('cetak_tunai'))
        @php $nota = session('cetak_tunai'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-md w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-white">
                    <div>
                        <div class="text-sm font-bold tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[11px] text-slate-500">Bagian Loket Kasir Kantor Pusat</div>
                        <div class="text-[11px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya</div>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold rounded-md bg-kp-blue-50 text-kp-blue-700 uppercase tracking-wider border border-kp-blue-200">
                            NOTA TUNAI
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1">{{ $nota['no_bkm'] }}</div>
                        <div class="text-[11px] text-slate-500 font-mono">{{ $nota['tanggal'] }}</div>
                    </div>
                </div>

                <div class="print-document-body flex-1 overflow-y-auto p-5 space-y-2.5 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Pembeli / Pemohon</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $nota['pembeli'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Kategori Transaksi</span>
                        <span class="col-span-2 font-semibold text-slate-800">: {{ $nota['kategori'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Uraian</span>
                        <span class="col-span-2 text-slate-700">: {{ $nota['rincian'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Metode Bayar</span>
                        <span class="col-span-2 font-semibold text-slate-800">: {{ $nota['metode'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-2 bg-kp-blue-50 px-3 rounded-xl border border-kp-blue-200 my-1">
                        <span class="font-bold text-kp-blue-800">Total Diterima</span>
                        <span class="col-span-2 font-mono font-bold text-sm text-kp-blue-700">: Rp {{ $nota['terbilang'] }}</span>
                    </div>

                    <div class="grid grid-cols-2 pt-4 border-t border-slate-200 text-center text-[11px] text-slate-600">
                        <div>
                            <div>Pembayar</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900">({{ $nota['pembeli'] }})</div>
                        </div>
                        <div>
                            <div>Kasir Penerima</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900 font-mono">({{ $nota['kasir'] }})</div>
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
