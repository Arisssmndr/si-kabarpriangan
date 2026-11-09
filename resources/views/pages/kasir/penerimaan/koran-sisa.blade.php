<x-app-layout>
    <div class="space-y-5" x-data="{ 
        showCetakModal: {{ session('cetak_sisa') ? 'true' : 'false' }},
        form: {
            pembeli: '',
            jenisSatuan: 'kg',
            jumlahSatuan: 10,
            hargaSatuan: 3000,
            metodeBayar: 'tunai',
            bankTujuan: 'Bank BJB Cabang Tasikmalaya',
            noBlanko: '',
            keterangan: ''
        },
        get totalBayar() {
            let jml = Number(this.form.jumlahSatuan) || 0;
            let hrg = Number(this.form.hargaSatuan) || 0;
            return Math.round(jml * hrg);
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
                                Penjualan Koran Sisa Cetak
                            </h1>
                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">
                                KI-14
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                            Pencatatan kas masuk penjualan sisa oplah cetak koran ke pengepul (COA 7.00.02.01.02).
                        </p>
                    </div>
                </div>

                @if(session('cetak_sisa'))
                    <div class="shrink-0">
                        <button type="button" 
                                @click="showCetakModal = true" 
                                class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Cetak Ulang BKM</span>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Flash Message -->
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

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Penjualan Hari Ini</span>
                    <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($totalSisaHariIni, 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">Kas masuk diterima hari ini</span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Akumulasi Bulan Ini</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($totalSisaBulanIni, 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">Periode {{ Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Akun Rekening (COA)</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                        7.00.02.01.02
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">Penjualan koran sisa pabrik</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
                <!-- Form Input Setoran Penjualan Koran Sisa -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-900">Input Kas Masuk (KI-14)</h3>
                        <p class="text-xs text-slate-500">Catat penjualan koran sisa cetak</p>
                    </div>

                    <form method="POST" action="{{ route('kasir.penerimaan.koran-sisa.store') }}" class="p-4 sm:p-5 space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Pembeli / Pengepul</label>
                            <input type="text" name="pembeli" x-model="form.pembeli" required placeholder="Contoh: Pak Yana (Pengepul Tasik)" 
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Satuan Ukur</label>
                                <select name="jenis_satuan" x-model="form.jenisSatuan" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                    <option value="kg">Kilogram (Kg)</option>
                                    <option value="eksemplar">Eksemplar (Eks)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jumlah</label>
                                <input type="number" step="0.1" name="jumlah_satuan" x-model="form.jumlahSatuan" required min="0.1" 
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Harga Satuan (Rp / <span x-text="form.jenisSatuan === 'kg' ? 'Kg' : 'Eks'"></span>)
                            </label>
                            <input type="number" name="harga_per_satuan" x-model="form.hargaSatuan" required min="1" 
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>

                        <!-- Live Total Kalkulasi -->
                        <div class="p-3.5 bg-kp-blue-50 rounded-xl border border-kp-blue-200 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold text-kp-blue-800 block">Total Kas Masuk</span>
                                <span class="text-xs text-slate-600 font-mono">
                                    <span x-text="form.jumlahSatuan"></span> <span x-text="form.jenisSatuan"></span> &times; Rp <span x-text="new Intl.NumberFormat('id-ID').format(form.hargaSatuan)"></span>
                                </span>
                            </div>
                            <div class="text-right font-mono font-bold text-base text-kp-blue-700 tabular-nums">
                                Rp <span x-text="new Intl.NumberFormat('id-ID').format(totalBayar)"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Metode Bayar</label>
                                <select name="metode_bayar" x-model="form.metodeBayar" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                    <option value="tunai">Kas Tunai</option>
                                    <option value="transfer">Transfer BJB</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">No. Blanko</label>
                                <input type="text" name="no_blanko" placeholder="Opsional" 
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Keterangan</label>
                            <textarea name="keterangan" rows="2" placeholder="Catatan edisi atau rincian koran sisa..." 
                                      class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"></textarea>
                        </div>

                        <button type="submit" class="w-full h-11 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                            <span>Simpan Kas Masuk (KI-14)</span>
                        </button>
                    </form>
                </div>

                <!-- Tabel Riwayat Penjualan Koran Sisa -->
                <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden flex flex-col">
                    <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Riwayat Penjualan Koran Sisa</h3>
                            <p class="text-xs text-slate-500">Daftar bukti kas masuk penjualan sisa cetak</p>
                        </div>
                        <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                            {{ $riwayatSisa->total() }} Data
                        </span>
                    </div>

                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                <tr>
                                    <th class="py-3 px-3.5 text-center w-12">No</th>
                                    <th class="py-3 px-3.5">No. Kuitansi</th>
                                    <th class="py-3 px-3.5">Tanggal</th>
                                    <th class="py-3 px-3.5">Pembeli / Pengepul</th>
                                    <th class="py-3 px-3.5">Keterangan</th>
                                    <th class="py-3 px-3.5 text-right">Nominal</th>
                                    <th class="py-3 px-3.5 text-center">Metode</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                                @forelse($riwayatSisa as $key => $item)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                        {{ $riwayatSisa->firstItem() + $key }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                        {{ $item->no_kuitansi }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono text-slate-600">
                                        {{ $item->tanggal_terima ? Carbon\Carbon::parse($item->tanggal_terima)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                        {{ $item->terima_dari }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-slate-600 max-w-xs truncate" title="{{ $item->keterangan }}">
                                        {{ $item->keterangan }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono tabular-nums font-semibold text-slate-900">
                                        Rp {{ number_format($item->jumlah_dibayar, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3.5 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium uppercase {{ $item->metode_bayar === 'transfer' ? 'bg-slate-100 text-slate-700 border border-slate-200' : 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' }}">
                                            {{ $item->metode_bayar }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-10 text-center text-slate-500 text-sm">
                                        Belum ada transaksi penjualan koran sisa cetak.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($riwayatSisa->hasPages())
                    <div class="p-3.5 border-t border-slate-200 bg-slate-50">
                        {{ $riwayatSisa->links() }}
                    </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Modal Cetak BKM Koran Sisa -->
        @if(session('cetak_sisa'))
        @php $sisa = session('cetak_sisa'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-white">
                    <div>
                        <div class="text-base font-bold tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[11px] text-slate-500">PT. Priangan Berkah Media - Bagian Perkasiran</div>
                        <div class="text-[11px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</div>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold rounded-md bg-kp-blue-50 text-kp-blue-700 uppercase tracking-wider border border-kp-blue-200">
                            BUKTI KAS MASUK (KI-14)
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1">{{ $sisa['no_kuitansi'] }}</div>
                        <div class="text-[11px] text-slate-500 font-mono">{{ $sisa['tanggal'] }}</div>
                    </div>
                </div>

                <div class="print-document-body flex-1 overflow-y-auto p-5 space-y-2.5 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Kode Perkiraan</span>
                        <span class="col-span-2 font-mono font-bold text-slate-900">: {{ $sisa['kode_perkiraan'] }} (Penjualan Koran Sisa Cetak)</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Diterima Dari</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $sisa['pembeli'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Rincian Barang</span>
                        <span class="col-span-2 text-slate-800">: {{ $sisa['rincian'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Metode Bayar</span>
                        <span class="col-span-2 font-semibold text-slate-800">: {{ $sisa['metode'] }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-2 bg-kp-blue-50 px-3 rounded-xl border border-kp-blue-200 my-2">
                        <span class="font-bold text-kp-blue-800">Jumlah Diterima</span>
                        <span class="col-span-2 font-mono font-bold text-base text-kp-blue-700">: Rp {{ number_format($sisa['nominal'], 0, ',', '.') }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-1.5 text-xs bg-slate-50 px-3 rounded-lg border border-slate-200">
                        <span class="text-slate-500 font-semibold">Terbilang</span>
                        <span class="col-span-2 font-semibold italic text-slate-900">: {{ $sisa['terbilang'] }}</span>
                    </div>

                    <div class="pt-6 border-t border-slate-200 flex justify-between text-center text-xs text-slate-600">
                        <div class="w-40">
                            <div class="font-semibold text-slate-700">Penyetor / Pembeli</div>
                            <div class="h-12"></div>
                            <div class="font-bold text-slate-900 underline">({{ $sisa['pembeli'] }})</div>
                        </div>
                        <div class="w-40">
                            <div class="text-[11px] text-slate-500">Tasikmalaya, {{ Carbon\Carbon::now()->format('d/m/Y') }}</div>
                            <div class="font-semibold text-slate-700 mt-0.5">Kasir Penerima</div>
                            <div class="h-12"></div>
                            <div class="font-bold text-slate-900 font-mono underline">({{ $sisa['kasir'] }})</div>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="h-10 px-4 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl border border-slate-300 transition cursor-pointer">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Bukti KI-14</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
