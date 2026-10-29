<x-app-layout>
    <div class="space-y-6" x-data="{ 
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
        <div class="space-y-6" :class="{ 'print:hidden': showCetakModal }">

        <!-- Flash Message Alert -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-semibold text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- Header Page Penjualan Langsung / Tunai -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5a1.5 1.5 0 011.5 1.5v9a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5v-9a1.5 1.5 0 011.5-1.5z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Penjualan Langsung / Kas Masuk Tunai
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Penerimaan Tunai
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pencatatan kas masuk transaksi langsung tanpa tempo (iklan baris lepas, koran eceran, penjualan afval kertas)
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if(session('cetak_tunai'))
                    <button type="button" @click="showCetakModal = true" class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs rounded-xl border border-emerald-200 transition shadow-2xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Ulang Nota Terakhir</span>
                    </button>
                @endif
                <button type="button" @click="showFormModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Input Transaksi Tunai</span>
                </button>
            </div>
        </div>

        <!-- Cards Kategori Penerimaan Tunai -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Iklan Baris & Kolom Lepas</span>
                <div class="mt-1 text-sm font-extrabold text-slate-900">Bayar Langsung Meja Kasir</div>
                <p class="mt-1 text-[11px] text-slate-400">Pemasangan iklan pengumuman duka cita, kehilangan STNK/BPKB, lowongan singkat.</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Penjualan Koran Eceran Kantor</span>
                <div class="mt-1 text-sm font-extrabold text-slate-900">Penjualan Off-the-shelf</div>
                <p class="mt-1 text-[11px] text-slate-400">Pembelian koran eceran langsung oleh tamu, masyarakat umum, & dinas pemerintah.</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Limbah Percetakan (Afval)</span>
                <div class="mt-1 text-sm font-extrabold text-slate-900">Pendapatan Lain-Lain Sah</div>
                <p class="mt-1 text-[11px] text-slate-400">Penjualan sisa kertas potongan percetakan, koran retur kedaluwarsa, & plat aluminium.</p>
            </div>
        </div>

        <!-- Tabel Riwayat Penjualan Langsung -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Riwayat Transaksi Penjualan Langsung (Tersimpan di Database)
                </h3>
                <span class="text-[11px] text-slate-500 font-medium">Kwitansi Kas Masuk Tunai</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">No. BKM / Kwitansi</th>
                            <th class="px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Pembeli / Pembayar</th>
                            <th class="px-4 py-3 font-semibold">Kategori & Uraian</th>
                            <th class="px-4 py-3 font-semibold">Metode</th>
                            <th class="px-4 py-3 font-semibold">Kasir</th>
                            <th class="px-4 py-3 font-semibold text-right">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentTunai as $t)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $t->no_bkm }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $t->tgl_transaksi->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $t->nama_pembayar }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                <span class="font-medium text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded text-[10px] mr-1">Direct</span>
                                {{ $t->keterangan }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 uppercase font-mono text-[10px]">{{ $t->metode_bayar }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $t->kasir->name ?? 'Kasir' }}</td>
                            <td class="px-4 py-3 text-right font-extrabold text-emerald-600">Rp {{ number_format($t->jumlah_dibayar, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-6 text-center text-slate-400">Belum ada transaksi penjualan langsung hari ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form Input Penjualan Langsung -->
        <div x-cloak x-show="showFormModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showFormModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between shrink-0 bg-slate-50/50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Input Transaksi Penjualan Langsung</h4>
                            <p class="text-[11px] text-slate-500">Penerimaan kas masuk langsung & cetak nota kwitansi</p>
                        </div>
                    </div>
                    <button type="button" @click="showFormModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <form method="POST" action="{{ route('kasir.penerimaan.tunai.store') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf

                    <div class="flex-1 overflow-y-auto px-5 py-3.5 space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pembeli / Pembayar</label>
                            <input type="text" name="pembeli" x-model="tunaiForm.pembeli" required placeholder="Contoh: Bpk. Hendra / Dinas Pendidikan" 
                                   class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-slate-900 font-semibold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Transaksi</label>
                            <select name="kategori" x-model="tunaiForm.kategori" required class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-slate-800">
                                <option value="Iklan Baris / Kolom Kecil">Iklan Baris / Kolom Kecil (Kehilangan STNK/Duka Cita)</option>
                                <option value="Pembelian Koran Eceran Sisa Stock">Pembelian Koran Eceran Kantor</option>
                                <option value="Penjualan Kertas Afval / Koran Bekas">Penjualan Kertas Afval / Limbah Cetak</option>
                                <option value="Pendapatan Kasir Lainnya">Pendapatan Kasir Lain-Lain</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Uraian / Keterangan Transaksi</label>
                            <textarea name="keterangan" x-model="tunaiForm.keterangan" required rows="2" placeholder="Contoh: Pemasangan iklan baris kehilangan STNK motor Yamaha Nopol Z 1234 AB a.n Hendra" 
                                      class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-slate-900"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Diterima (Rp)</label>
                                <input type="number" name="jumlah" x-model="tunaiForm.jumlah" required min="1" placeholder="75000" 
                                       class="w-full text-xs font-mono font-bold py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-slate-900">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pembayaran</label>
                                <select name="metode" x-model="tunaiForm.metode" required class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-slate-800">
                                    <option value="Tunai Kasir">Tunai Meja Kasir</option>
                                    <option value="Transfer QRIS">Transfer QRIS Resmi</option>
                                    <option value="Transfer Bank BJB">Transfer Bank BJB</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showFormModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-2xs">
                            Simpan & Cetak Nota Kasir
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <!-- Modal Cetak Nota Penjualan Langsung Otomatis -->
        @if(session('cetak_tunai'))
        @php $nota = session('cetak_tunai'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-md w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-100 overflow-hidden">
                <!-- Header Nota -->
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-slate-50/50">
                    <div>
                        <div class="text-sm font-black tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500">Bagian Loket Kasir Kantor Pusat</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 text-[9px] font-black rounded bg-emerald-100 text-emerald-800 uppercase tracking-widest border border-emerald-300">
                            NOTA TUNAI
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1">{{ $nota['no_bkm'] }}</div>
                        <div class="text-[10px] text-slate-500">{{ $nota['tanggal'] }}</div>
                    </div>
                </div>

                <!-- Body Nota (Scrollable) -->
                <div class="print-document-body flex-1 overflow-y-auto p-5 space-y-2 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Pembeli / Pemohon</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $nota['pembeli'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Kategori Transaksi</span>
                        <span class="col-span-2 font-semibold text-slate-800">: {{ $nota['kategori'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Uraian / Keterangan</span>
                        <span class="col-span-2 text-slate-700">: {{ $nota['rincian'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Metode Bayar</span>
                        <span class="col-span-2 font-semibold text-slate-800">: {{ $nota['metode'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1.5 bg-emerald-50 px-2 rounded-lg">
                        <span class="font-bold text-emerald-900">Total Diterima</span>
                        <span class="col-span-2 font-mono font-black text-sm text-emerald-800">: Rp {{ $nota['terbilang'] }}</span>
                    </div>

                    <!-- Tanda Tangan Cetak -->
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

                <!-- Action Button Print & Close (Pinned Bottom) -->
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Nota Tunai</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
