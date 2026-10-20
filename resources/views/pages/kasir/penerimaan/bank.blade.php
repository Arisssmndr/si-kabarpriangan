<x-app-layout>
    <div class="space-y-6" x-data="{
        showCetakModal: {{ session('cetak_bank') ? 'true' : 'false' }},
        jenisTransaksi: 'jasa_giro',
        kodeCoa: '7.00.01.01.01',
        updateCoa() {
            if (this.jenisTransaksi === 'jasa_giro') {
                this.kodeCoa = '7.00.01.01.01';
            } else {
                this.kodeCoa = '7.00.01.01.02';
            }
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

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs font-semibold text-rose-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- Header Page Bank -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Mutasi Kas Bank Masuk (Jasa Giro / Transfer BJB)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-blue-100 text-blue-800 border border-blue-300">
                            Kas Bank BJB
                        </span>
                        <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded bg-slate-100 text-slate-700 border border-slate-200">
                            KI-14-YYYYMMXXXX
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Penerimaan Kas Bank Masuk via Rekening Koran Bank BJB Tasikmalaya (Sistem KAS COA 7.00.01.01.01)
                    </p>
                </div>
            </div>

            @if(session('cetak_bank'))
                <button type="button" @click="showCetakModal = true" class="px-3.5 py-2 bg-blue-50 hover:bg-blue-100 text-blue-800 font-bold text-xs rounded-xl border border-blue-300 transition shadow-2xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Kuitansi Bank (KI-14)</span>
                </button>
            @endif
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Penerimaan Bank Bulan Ini</span>
                <div class="mt-2 text-lg font-extrabold text-blue-700">
                    Rp {{ number_format($totalBankBulanIni, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Akumulasi giro & transfer bank</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Transaksi Bank Tercatat</span>
                <div class="mt-2 text-lg font-extrabold text-slate-900">
                    {{ $bankList->count() }} Transaksi
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Mutasi rekening koran masuk</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Rekening Resmi Operasional</span>
                <div class="mt-2 text-sm font-bold text-slate-800">
                    Bank BJB Tasikmalaya
                </div>
                <div class="mt-1 text-[11px] font-mono text-slate-500">No. Rek: 001-029-3849-001 (PT PBM)</div>
            </div>
        </div>

        <!-- Layout 2 Kolom: Form Input Bank & Tabel Mutasi -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom 1: Form Input Kas Bank Masuk (KAS) -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs lg:col-span-1">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    Form Penerimaan Kas Bank (KI-14)
                </h3>

                <form method="POST" action="{{ route('kasir.penerimaan.bank.store') }}" class="mt-4 space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Penerimaan Bank</label>
                        <select name="jenis_transaksi" x-model="jenisTransaksi" @change="updateCoa()" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-semibold">
                            <option value="jasa_giro">Jasa Giro / Bunga Bank BJB</option>
                            <option value="transfer_bjb">Penerimaan Transfer Bank BJB</option>
                            <option value="bunga_tabungan">Pendapatan Bunga Simpanan</option>
                            <option value="pendapatan_lain">Pendapatan Lain-lain / BPR</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Divisi / Bagian</label>
                            <input type="text" value="Kas Bank BJB / BPR" disabled 
                                   class="w-full text-xs py-2 px-3 rounded-lg border border-slate-200 bg-slate-50 font-bold text-slate-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kode COA</label>
                            <input type="text" name="kode_perkiraan" x-model="kodeCoa" readonly
                                   class="w-full text-xs py-2 px-3 rounded-lg border border-slate-200 bg-slate-50 font-mono font-bold text-blue-700 text-[11px]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Terima Dari (Nama Lembaga / Klien)</label>
                        <input type="text" name="terima_dari" required placeholder="Contoh: Bank BJB KC Tasikmalaya / Pemda"
                               class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-slate-900 font-semibold">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">No. Blanko Fisik</label>
                            <input type="text" name="no_blanko" placeholder="Contoh: B-14-098"
                                   class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 font-mono text-slate-800">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">No. Ref Transfer</label>
                            <input type="text" name="no_referensi" placeholder="TRX-BJB-xxxx"
                                   class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 font-mono text-slate-800">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Diterima (Rp)</label>
                        <input type="number" name="nominal" required min="1" placeholder="0"
                               class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-slate-900 font-mono font-bold text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan Mutasi</label>
                        <textarea name="keterangan" rows="2" placeholder="Catatan transaksi mutasi kas bank..."
                                  class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-slate-800"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-2xs">
                        Simpan Penerimaan Kas Bank (KI-14)
                    </button>
                </form>
            </div>

            <!-- Kolom 2: Daftar Mutasi Bank Masuk -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden lg:col-span-2">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        Riwayat Penerimaan Kas Bank Masuk (KAS)
                    </h3>
                    <form method="GET" action="{{ route('kasir.penerimaan.bank') }}" class="flex items-center gap-2">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari kuitansi / asal..." 
                               class="text-xs py-1 px-2.5 rounded-lg border border-slate-300">
                        <button type="submit" class="px-2.5 py-1 bg-slate-800 text-white text-xs rounded-lg font-semibold">Cari</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 font-semibold">No. Kuitansi (KI)</th>
                                <th class="px-4 py-3 font-semibold">Tanggal</th>
                                <th class="px-4 py-3 font-semibold">Terima Dari</th>
                                <th class="px-4 py-3 font-semibold">Perkiraan COA</th>
                                <th class="px-4 py-3 font-semibold text-right">Jumlah Diterima (Rp)</th>
                                <th class="px-4 py-3 font-semibold text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($bankList as $bank)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono font-bold text-blue-900">
                                    {{ $bank->no_kuitansi ?? $bank->no_bkm }}
                                    @if($bank->no_blanko)
                                        <div class="text-[10px] text-slate-400 font-normal">Blanko: {{ $bank->no_blanko }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-500">{{ $bank->tanggal_terima ? \Carbon\Carbon::parse($bank->tanggal_terima)->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-3 font-bold text-slate-800">{{ $bank->terima_dari ?? $bank->nama_pembayar }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-mono text-[11px] text-slate-700">{{ $bank->kode_perkiraan }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $bank->nama_perkiraan }}</div>
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-extrabold text-blue-800">
                                    Rp {{ number_format($bank->jumlah_diterima ?? $bank->jumlah_dibayar, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Lunas
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-5 py-6 text-center text-slate-400">Belum ada transaksi kas bank masuk.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>

        <!-- Modal Cetak Dokumen KI-14 -->
        @if(session('cetak_bank'))
        @php $cb = session('cetak_bank'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-100 overflow-hidden">
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-slate-50/50">
                    <div>
                        <div class="text-base font-black tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500 font-semibold">DIVISI KASIR - MUTASI KAS BANK (KAS)</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 text-[10px] font-black rounded bg-blue-100 text-blue-800 uppercase tracking-widest border border-blue-300">
                            BUKTI KAS MASUK BANK (KI)
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1">{{ $cb['no_kuitansi'] }}</div>
                        <div class="text-[10px] text-slate-500">{{ $cb['tanggal'] }}</div>
                    </div>
                </div>

                <div class="print-document-body flex-1 overflow-y-auto p-5 space-y-2 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Sudah Terima Dari</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $cb['terima_dari'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Bagan Akun (COA)</span>
                        <span class="col-span-2 font-mono text-slate-700">: Kas Bank BJB - {{ $cb['kode_perkiraan'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Uraian Penerimaan</span>
                        <span class="col-span-2 font-semibold text-slate-800">: {{ $cb['jenis'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1.5 bg-blue-50/80 px-2 rounded-lg">
                        <span class="font-bold text-blue-900">Jumlah Kas Masuk</span>
                        <span class="col-span-2 font-mono font-black text-sm text-blue-800">: Rp {{ $cb['terbilang'] }}</span>
                    </div>

                    <div class="grid grid-cols-2 pt-4 border-t border-slate-200 text-center text-[10px] text-slate-600">
                        <div>
                            <div>Penyetor / Bank</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900">({{ $cb['terima_dari'] }})</div>
                        </div>
                        <div>
                            <div>Kasir Penerima</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900 font-mono">({{ Auth::user()->name ?? 'Kasir Priangan' }})</div>
                        </div>
                    </div>
                </div>

                <!-- Action Button Print & Close (Pinned Bottom) -->
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Bukti KI-14</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
