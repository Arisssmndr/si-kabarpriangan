<x-app-layout>
    <div class="space-y-6" x-data="{ showFormModal: false }">

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
                        Input penerimaan kas langsung tanpa faktur tempo (iklan baris/kolom kecil, eceran koran, limbah afval cetak)
                    </p>
                </div>
            </div>

            <button type="button" @click="showFormModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Input Kas Masuk Tunai</span>
            </button>
        </div>

        <!-- Cards Kategori Penerimaan Tunai -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Iklan Baris & Kolom Kecil</span>
                <div class="mt-1 text-sm font-extrabold text-slate-900">Bayar Langsung Meja Kasir</div>
                <p class="mt-1 text-[11px] text-slate-400">Pemasangan iklan pengumuman, duka cita, kehilangan STNK/BPKB.</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Penjualan Koran Eceran</span>
                <div class="mt-1 text-sm font-extrabold text-slate-900">Penjualan Off-the-shelf</div>
                <p class="mt-1 text-[11px] text-slate-400">Pembelian koran eceran langsung dari masyarakat & kantor instansi.</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Penjualan Limbah / Afval Kertas</span>
                <div class="mt-1 text-sm font-extrabold text-slate-900">Pendapatan Lain-Lain</div>
                <p class="mt-1 text-[11px] text-slate-400">Penjualan sisa kertas potongan percetakan & plat aluminium bekas.</p>
            </div>
        </div>

        <!-- Tabel Riwayat Penjualan Langsung -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Riwayat Transaksi Penjualan Langsung Terkini
                </h3>
                <span class="text-[11px] text-slate-500 font-medium">Kwitansi Tunai Kasir</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">No. Kwitansi</th>
                            <th class="px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Pembeli / Instansi</th>
                            <th class="px-4 py-3 font-semibold">Kategori Penerimaan</th>
                            <th class="px-4 py-3 font-semibold">Uraian / Keterangan</th>
                            <th class="px-4 py-3 font-semibold">Metode</th>
                            <th class="px-4 py-3 font-semibold text-right">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentTunai as $t)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $t->no_kwitansi }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ \Carbon\Carbon::parse($t->tanggal)->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $t->pembeli }}</td>
                            <td class="px-4 py-3 font-medium text-teal-700 bg-teal-50/60 rounded">{{ $t->kategori }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $t->keterangan }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $t->metode }}</td>
                            <td class="px-4 py-3 text-right font-extrabold text-emerald-600">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form Input Penjualan Langsung -->
        <div x-cloak x-show="showFormModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showFormModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Form Penjualan Langsung / Tunai</h4>
                            <p class="text-[11px] text-slate-500">Terbitkan Kwitansi Kas Masuk Tunai Tanpa Tempo</p>
                        </div>
                    </div>
                    <button type="button" @click="showFormModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <form method="POST" action="{{ route('kasir.penerimaan.tunai.store') }}" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pembeli / Klien / Pemasang</label>
                        <input type="text" name="pembeli" required placeholder="Contoh: Bpk. Ahmad / Dinas Kesehatan" 
                               class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Penerimaan</label>
                        <select name="kategori" required class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-slate-800">
                            <option value="Iklan Baris / Kolom Kecil">Iklan Baris / Kolom Kecil (Kehilangan/Duka Cita)</option>
                            <option value="Pembelian Koran Eceran Sisa Stock">Pembelian Koran Eceran</option>
                            <option value="Penjualan Kertas Afval / Koran Bekas">Penjualan Kertas Afval / Daur Ulang</option>
                            <option value="Pendapatan Operasional Lainnya">Pendapatan Operasional Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Uraian / Keterangan Transaksi</label>
                        <textarea name="keterangan" required rows="2" placeholder="Contoh: Pemasangan iklan baris kehilangan STNK motor Nopol Z 1234 AB" 
                                  class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-slate-900"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Diterima (Rp)</label>
                            <input type="number" name="jumlah" required min="1" placeholder="75000" 
                                   class="w-full text-xs font-mono font-bold py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-slate-900">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pembayaran</label>
                            <select name="metode" required class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-slate-800">
                                <option value="Tunai Kasir">Tunai Meja Kasir</option>
                                <option value="Transfer QRIS">Transfer QRIS / E-Wallet</option>
                                <option value="Transfer Bank BJB">Transfer Bank BJB</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="showFormModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg transition shadow-2xs">
                            Cetak Kwitansi Tunai
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
