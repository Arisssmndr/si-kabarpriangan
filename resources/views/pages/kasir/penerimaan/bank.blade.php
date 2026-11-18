<x-app-layout>
    <div class="space-y-6" 
         @open-cetak-modal.window="showCetakModal = true"
         x-data="{
        showCetakModal: false,
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
                                Penarikan Tunai Kas Bank (KI-09)
                            </h1>
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-sky-50 text-[#0E527B] border border-sky-200">
                                Cek / Giro Tunai
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Pencatatan penarikan dana tunai dari rekening giro Bank BJB / BCA via Cek ke brankas kasir (COA 1.02.01.01.01).
                        </p>
                    </div>
                </div>

                @if(session('cetak_bank'))
                    <button type="button" @click="showCetakModal = true" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs rounded-xl transition shadow-xs flex items-center gap-2 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Bukti Penarikan (KI-09)</span>
                    </button>
                @endif
            </div>

            <!-- 2. KPI Cards Standar Divisi Iklan -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Penarikan Cek Bulan Ini</span>
                    <p class="mt-2 text-2xl font-bold font-mono text-kp-blue-700">
                        Rp {{ number_format($totalBankBulanIni, 0, ',', '.') }}
                    </p>
                    <span class="mt-1 text-[11px] text-slate-400 block">Total dana cair dari giro ke brankas fisik kasir</span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pencairan Bank BJB Tasikmalaya</span>
                    <p class="mt-2 text-2xl font-bold font-mono text-slate-900">
                        Rp {{ number_format($totalTarikBjbBulanIni, 0, ',', '.') }}
                    </p>
                    <span class="mt-1 text-[11px] text-slate-400 font-mono block">Giro 05.00.01.006270.9 (PT PBM)</span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Pencairan Bank BCA Tasikmalaya</span>
                    <p class="mt-2 text-2xl font-bold font-mono text-slate-900">
                        Rp {{ number_format($totalTarikBcaBulanIni, 0, ',', '.') }}
                    </p>
                    <span class="mt-1 text-[11px] text-slate-400 font-mono block">Giro 054-0388-991 (PT PBM)</span>
                </div>
            </div>

            <!-- 3. Form Input & Tabel Mutasi -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Kolom 1: Form Input Kas Bank Masuk (KAS) -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs lg:col-span-1 h-fit">
                    <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Form Penarikan Cek Bank (KI-09)
                        </h2>
                        <span class="text-[11px] font-mono text-slate-400">KI-09</span>
                    </div>

                    <form method="POST" action="{{ route('kasir.penerimaan.bank.store') }}" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Bank Sumber Rekening Giro</label>
                            <select name="bank_sumber" x-model="bankSumber" @change="kodeCoa = (bankSumber === 'Bank BCA' ? '1.02.01.01.02' : '1.02.01.01.01')" class="h-10 w-full px-3 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                                <option value="Bank BJB">Bank BJB Tasikmalaya (Giro 05.00.01.006270.9)</option>
                                <option value="Bank BCA">Bank BCA Tasikmalaya (Giro 054-0388-991)</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Bidang</label>
                                <input type="text" value="Keuangan (Bank)" disabled 
                                       class="h-10 w-full px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-600">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Kode COA</label>
                                <input type="text" name="kode_perkiraan" x-model="kodeCoa" readonly
                                       class="h-10 w-full px-3 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs font-semibold text-slate-600">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">No. Cek / Bilyet Giro</label>
                            <input type="text" name="no_cek_giro" required placeholder="Contoh: CK-BJB-892182 / BCA-054991"
                                   class="h-10 w-full px-3 bg-white border border-slate-300 rounded-xl font-mono text-sm font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">No. Blanko (Opsional)</label>
                                <input type="text" name="no_blanko" placeholder="Contoh: B-09-001"
                                       class="h-10 w-full px-3 bg-white border border-slate-300 rounded-xl font-mono text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Cair (Rp)</label>
                                <input type="number" name="nominal" x-model="nominal" required min="1" placeholder="0"
                                       class="h-10 w-full px-3 bg-white border border-slate-300 rounded-xl font-mono font-bold text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                            </div>
                        </div>

                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="text-xs text-slate-600 font-medium flex justify-between items-center">
                                <span>Estimasi Kas Masuk:</span>
                                <span class="font-mono font-bold text-kp-blue-700 text-sm" x-text="formatRupiah(nominal)"></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Keperluan Kas</label>
                            <textarea name="keterangan" rows="2" placeholder="Catatan penarikan cek tunai untuk brankas kasir..."
                                      class="w-full p-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600"></textarea>
                        </div>

                        <button type="submit" class="h-11 w-full bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            <span>Simpan Penarikan Kas (KI-09)</span>
                        </button>
                    </form>
                </div>

                <!-- Kolom 2: Pure Table Mutasi Bank Masuk -->
                <div class="lg:col-span-2 space-y-4">
                    <!-- Filter Bar Terpisah -->
                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                        <form method="GET" action="{{ route('kasir.penerimaan.bank') }}" class="flex flex-wrap items-center gap-3">
                            <div class="min-w-[150px] flex-1">
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Pilih Bank</label>
                                <select name="bank" onchange="this.form.submit()" class="h-11 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600">
                                    <option value="all" {{ ($bankFilter ?? 'all') == 'all' ? 'selected' : '' }}>Semua Bank</option>
                                    <option value="BJB" {{ ($bankFilter ?? '') == 'BJB' ? 'selected' : '' }}>Bank BJB</option>
                                    <option value="BCA" {{ ($bankFilter ?? '') == 'BCA' ? 'selected' : '' }}>Bank BCA</option>
                                </select>
                            </div>
                            <div class="min-w-[150px] flex-1">
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Tanggal</label>
                                <input type="date" name="tanggal" value="{{ $tanggal ?? request('tanggal') }}" 
                                       class="h-11 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600">
                            </div>
                            <div class="min-w-[200px] flex-2">
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Pencarian</label>
                                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kuitansi / no cek..." 
                                       class="h-11 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600">
                            </div>
                            <div class="flex items-end gap-2 pt-5">
                                <button type="submit" class="h-11 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-xs cursor-pointer">
                                    Cari
                                </button>
                                @if(!empty($search) || !empty($tanggal) || (($bankFilter ?? 'all') !== 'all'))
                                    <a href="{{ route('kasir.penerimaan.bank') }}" title="Reset Filter" class="h-11 px-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Pure Data Table (Tanpa Banner Strip) -->
                    <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm border-collapse">
                                <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                    <tr>
                                        <th class="py-3 px-4">No. Kuitansi (KI)</th>
                                        <th class="py-3 px-4">Tanggal</th>
                                        <th class="py-3 px-4">Bank & No. Cek</th>
                                        <th class="py-3 px-4">Uraian / Keterangan</th>
                                        <th class="py-3 px-4 text-right">Nominal Cair (Rp)</th>
                                        <th class="py-3 px-4 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-800">
                                    @forelse($bankList as $bank)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 px-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                                            {{ $bank->no_kuitansi ?? $bank->no_bkm }}
                                            @if($bank->no_blanko)
                                                <span class="block text-[11px] text-slate-400 font-normal">Blanko: {{ $bank->no_blanko }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-slate-600 font-mono text-xs whitespace-nowrap">
                                            {{ $bank->tanggal_terima ? \Carbon\Carbon::parse($bank->tanggal_terima)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="font-semibold text-slate-900">{{ $bank->bank_tujuan ?? $bank->terima_dari }}</div>
                                            <div class="font-mono text-xs text-slate-500">Cek: {{ $bank->no_referensi_transfer ?? '-' }}</div>
                                        </td>
                                        <td class="py-3 px-4 text-slate-700">
                                            <div class="text-sm text-slate-800">{{ $bank->keterangan }}</div>
                                            <div class="font-mono text-xs text-slate-400">COA: {{ $bank->kode_perkiraan }}</div>
                                        </td>
                                        <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                            Rp {{ number_format($bank->jumlah_diterima ?? $bank->jumlah_dibayar, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-[#0E527B] border border-sky-200">
                                                Masuk Kas
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                            Belum ada transaksi penarikan kas bank masuk.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($bankList->hasPages())
                        <div class="p-4 border-t border-slate-100 bg-white">
                            {{ $bankList->links() }}
                        </div>
                        @endif
                    </div>
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
                        <div class="text-[10px] text-slate-500 font-semibold">BIDANG KEUANGAN - BUKTI PENARIKAN KAS BANK (KI-09)</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 text-[10px] font-black rounded bg-sky-50 text-[#0E527B] uppercase tracking-widest border border-sky-200">
                            BUKTI KAS MASUK (KI-09)
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1">{{ $cb['no_kuitansi'] }}</div>
                        <div class="text-[10px] text-slate-500">{{ $cb['tanggal'] }}</div>
                    </div>
                </div>

                <div class="print-document-body flex-1 overflow-y-auto p-5 space-y-2 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Nama Bank & Rekening</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $cb['bank'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">No. Cek / Bilyet Giro</span>
                        <span class="col-span-2 font-mono font-bold text-slate-900">: {{ $cb['no_cek'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Bagan Akun (COA)</span>
                        <span class="col-span-2 font-mono text-slate-700">: {{ $cb['kode_perkiraan'] }} (Bank Penerbitan)</span>
                    </div>
                    <div class="grid grid-cols-3 py-2 bg-slate-50 px-2 rounded-lg border border-slate-200">
                        <span class="font-bold text-slate-900">Nominal Pencairan</span>
                        <span class="col-span-2 font-mono font-black text-sm text-kp-blue-700">: Rp {{ number_format($cb['nominal'], 0, ',', '.') }}</span>
                    </div>
                    <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                        <span class="text-[10px] text-slate-500 block uppercase font-bold">Terbilang:</span>
                        <span class="text-xs font-semibold italic text-slate-800">{{ $cb['terbilang'] }}</span>
                    </div>

                    <!-- Tanda Tangan SOP -->
                    <div class="flex justify-end pt-4 border-t border-slate-200 text-center text-[11px] text-slate-600">
                        <div class="w-48">
                            <div>Tasikmalaya, {{ date('d/m/Y') }}</div>
                            <div class="font-semibold text-slate-700 mt-0.5">Kasir Penerima / Pembayar</div>
                            <div class="h-12"></div>
                            <div class="font-bold text-slate-900 font-mono border-t border-slate-400 pt-1">({{ $cb['kasir'] }})</div>
                        </div>
                    </div>
                </div>

                <!-- Action Button Print & Close (Pinned Bottom) -->
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition cursor-pointer">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs rounded-xl transition shadow-xs flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>