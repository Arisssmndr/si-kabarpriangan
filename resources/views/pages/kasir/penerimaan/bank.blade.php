<x-app-layout>
    <div class="space-y-6" x-data="{
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
                            Penarikan Tunai Kas Bank (KI-09)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-blue-100 text-blue-800 border border-blue-300">
                            Cek / Giro Tunai
                        </span>
                        <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded bg-slate-100 text-slate-700 border border-slate-200">
                            KI-09-YYYYMMXXXX
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pencatatan penarikan uang fisik dari rekening giro Bank BJB / BCA via Cek Tunai ke brankas kasir (COA 1.02.01.01.01 Bank Penerbitan).
                    </p>
                </div>
            </div>

            @if(session('cetak_bank'))
                <button type="button" @click="showCetakModal = true" class="px-3.5 py-2 bg-blue-50 hover:bg-blue-100 text-blue-800 font-bold text-xs rounded-xl border border-blue-300 transition shadow-2xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Bukti Penarikan (KI-09)</span>
                </button>
            @endif
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Penarikan Cek Bulan Ini</span>
                <div class="mt-2 text-lg font-extrabold text-blue-700 font-mono">
                    Rp {{ number_format($totalBankBulanIni, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Total dana cair dari giro ke brankas kasir</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Pencairan Bank BJB Tasikmalaya</span>
                <div class="mt-2 text-lg font-extrabold text-slate-900 font-mono">
                    Rp {{ number_format($totalTarikBjbBulanIni, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400 font-mono">Giro No. 05.00.01.006270.9 (PT PBM)</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Pencairan Bank BCA Tasikmalaya</span>
                <div class="mt-2 text-lg font-extrabold text-slate-900 font-mono">
                    Rp {{ number_format($totalTarikBcaBulanIni, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400 font-mono">Rekening Operasional Kas BCA</div>
            </div>
        </div>

        <!-- Layout 2 Kolom: Form Penarikan Kas Bank & Tabel Mutasi -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom 1: Form Input Kas Bank Masuk (KAS) -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs lg:col-span-1">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    Form Penarikan Cek Bank (KI-09)
                </h3>

                <form method="POST" action="{{ route('kasir.penerimaan.bank.store') }}" class="mt-4 space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Bank Sumber Rekening Giro</label>
                        <select name="bank_sumber" x-model="bankSumber" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-bold">
                            <option value="Bank BJB">Bank BJB Tasikmalaya (Giro 05.00.01.006270.9)</option>
                            <option value="Bank BCA">Bank BCA Tasikmalaya</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Divisi</label>
                            <input type="text" value="Divisi Kasir (Bank)" disabled 
                                   class="w-full text-xs py-2 px-3 rounded-lg border border-slate-200 bg-slate-50 font-bold text-slate-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kode COA</label>
                            <input type="text" name="kode_perkiraan" x-model="kodeCoa" readonly
                                   class="w-full text-xs py-2 px-3 rounded-lg border border-slate-200 bg-slate-50 font-mono font-bold text-blue-700 text-[11px]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">No. Cek / Bilyet Giro</label>
                        <input type="text" name="no_cek_giro" required placeholder="Contoh: CK-BJB-892182 / Giro 00192"
                               class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 font-mono text-slate-900 font-bold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Petugas Penarik Uang ke Bank</label>
                        <input type="text" name="penarik" required placeholder="Contoh: Eki (Kasir) / Supir Operasional"
                               class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-slate-900 font-semibold">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">No. Blanko Fisik (Opsional)</label>
                            <input type="text" name="no_blanko" placeholder="Contoh: B-09-001"
                                   class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 font-mono text-slate-800">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Cair (Rp)</label>
                            <input type="number" name="nominal" x-model="nominal" required min="1" placeholder="0"
                                   class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-slate-900 font-mono font-bold text-sm">
                        </div>
                    </div>

                    <div class="p-2.5 bg-blue-50/80 rounded-lg border border-blue-200">
                        <div class="text-[11px] text-blue-900 font-semibold flex justify-between">
                            <span>Estimasi Penambahan Kas Fisik:</span>
                            <span class="font-mono font-bold text-blue-800" x-text="formatRupiah(nominal)"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Keperluan Kas</label>
                        <textarea name="keterangan" rows="2" placeholder="Catatan penarikan cek tunai untuk brankas kasir..."
                                  class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-slate-800"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>Simpan Penarikan Kas Bank (KI-09)</span>
                    </button>
                </form>
            </div>

            <!-- Kolom 2: Daftar Mutasi Bank Masuk -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden lg:col-span-2">
                <div class="px-5 py-3.5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        Riwayat Penarikan Cek Bank Masuk (KI-09)
                    </h3>
                    <form method="GET" action="{{ route('kasir.penerimaan.bank') }}" class="flex items-center gap-2">
                        <select name="bank" onchange="this.form.submit()" class="text-xs py-1 px-2.5 rounded-lg border border-slate-300 text-slate-700 font-semibold">
                            <option value="all" {{ ($bankFilter ?? 'all') == 'all' ? 'selected' : '' }}>Semua Bank</option>
                            <option value="BJB" {{ ($bankFilter ?? '') == 'BJB' ? 'selected' : '' }}>Bank BJB</option>
                            <option value="BCA" {{ ($bankFilter ?? '') == 'BCA' ? 'selected' : '' }}>Bank BCA</option>
                        </select>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari kuitansi / cek..." 
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
                                <th class="px-4 py-3 font-semibold">Bank & No. Cek</th>
                                <th class="px-4 py-3 font-semibold">Penarik</th>
                                <th class="px-4 py-3 font-semibold text-right">Nominal Cair (Rp)</th>
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
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900">{{ $bank->bank_tujuan ?? $bank->terima_dari }}</div>
                                    <div class="font-mono text-[10px] text-blue-700">Cek: {{ $bank->no_referensi_transfer ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-slate-700">
                                    {{ $bank->penagih_inkaso ?? '-' }}
                                    <div class="text-[10px] text-slate-400 truncate max-w-[140px]">{{ $bank->keterangan }}</div>
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-extrabold text-blue-800">
                                    Rp {{ number_format($bank->jumlah_diterima ?? $bank->jumlah_dibayar, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Masuk Kas
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-5 py-6 text-center text-slate-400">Belum ada transaksi penarikan kas bank masuk.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($bankList->hasPages())
                <div class="p-3 border-t border-slate-100">
                    {{ $bankList->links() }}
                </div>
                @endif
            </div>
        </div>
        </div>

        <!-- Modal Cetak Dokumen KI-09 -->
        @if(session('cetak_bank'))
        @php $cb = session('cetak_bank'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-100 overflow-hidden">
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-slate-50/50">
                    <div>
                        <div class="text-base font-black tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500 font-semibold">DIVISI KASIR - BUKTI PENARIKAN KAS BANK (KAS)</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 text-[10px] font-black rounded bg-blue-100 text-blue-800 uppercase tracking-widest border border-blue-300">
                            BUKTI KAS MASUK (KI-09)
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1">{{ $cb['no_kuitansi'] }}</div>
                        <div class="text-[10px] text-slate-500">{{ $cb['tanggal'] }}</div>
                    </div>
                </div>

                <div class="print-document-body flex-1 overflow-y-auto p-5 space-y-2 text-xs text-slate-800">
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
                    <div class="grid grid-cols-3 py-1.5 bg-blue-50/80 px-2 rounded-lg">
                        <span class="font-bold text-blue-900">Jumlah Uang Diterima</span>
                        <span class="col-span-2 font-mono font-black text-sm text-blue-800">: Rp {{ number_format($cb['nominal'], 0, ',', '.') }}</span>
                    </div>
                    <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                        <span class="text-[10px] text-slate-500 block uppercase font-bold">Terbilang:</span>
                        <span class="text-xs font-semibold italic text-slate-800">{{ $cb['terbilang'] }}</span>
                    </div>

                    <div class="grid grid-cols-2 pt-4 border-t border-slate-200 text-center text-[10px] text-slate-600">
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

                <!-- Action Button Print & Close (Pinned Bottom) -->
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
