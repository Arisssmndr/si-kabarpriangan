<x-app-layout>
    <div class="space-y-5" x-init="$nextTick(() => {
            const p = new URLSearchParams(window.location.search);
            if (p.get('edit') && p.get('tipe')) {
                const el = document.querySelector('[data-edit-id=\'' + p.get('tipe') + '-' + p.get('edit') + '\']');
                if (el) { el.scrollIntoView({ block: 'center' }); el.click(); }
            }
        })" x-data="{
        showVoidModal: false,
        showEditModal: false,
        voidAction: '',
        editAction: '',
        activeDoc: {
            id: null,
            no_dokumen: '',
            tipe: 'KI',
            pihak: '',
            nominal: 0,
            alasan: ''
        },
        editDoc: {
            id: null,
            no_dokumen: '',
            tipe: 'KI',
            nama_pihak: '',
            jumlah_nominal: 0,
            metode_bayar: 'tunai',
            bank_tujuan: '',
            no_referensi_transfer: '',
            no_blanko: '',
            penagih_inkaso: '',
            keterangan: '',
            alasan_koreksi: ''
        },
        openVoid(id, noDok, tipe, pihak, nominal) {
            this.activeDoc.id = id;
            this.activeDoc.no_dokumen = noDok;
            this.activeDoc.tipe = tipe;
            this.activeDoc.pihak = pihak;
            this.activeDoc.nominal = nominal;
            this.activeDoc.alasan = '';
            this.voidAction = '/kasir/utilitas/koreksi/' + id + '/void';
            this.showVoidModal = true;
        },
        openEdit(doc) {
            this.editDoc = {
                id: doc.id,
                no_dokumen: doc.no_dokumen || doc.no_kuitansi || doc.no_bkm || doc.no_bkk || '',
                tipe: doc.tipe || 'KI',
                nama_pihak: doc.nama_pihak || doc.terima_dari || doc.nama_pembayar || doc.diberikan_kepada || doc.nama_penerima || '',
                jumlah_nominal: doc.jumlah_nominal || doc.jumlah_dibayar || doc.jumlah_diterima || doc.jumlah_keluar || doc.nominal_keluar || 0,
                metode_bayar: doc.metode_bayar || 'tunai',
                bank_tujuan: doc.bank_tujuan || '',
                no_referensi_transfer: doc.no_referensi_transfer || doc.no_cek || '',
                no_blanko: doc.no_blanko || '',
                penagih_inkaso: doc.penagih_inkaso || '',
                keterangan: doc.keterangan || '',
                alasan_koreksi: ''
            };
            this.editAction = '/kasir/utilitas/koreksi/' + doc.id + '/update';
            this.showEditModal = true;
        }
    }">

        <!-- ============================================================== -->
        <!-- 1. HEADER HALAMAN (PALET 60-30-10 & ACTION BAR)                -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Edit Inputan & Koreksi Kasir
                        </h1>
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-sky-50 text-kp-blue-700 border border-kp-blue-200">
                            Fitur Edit & Void
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Koreksi data input kasir (nama pembayar, nominal, metode, uraian) atau pembatalan dokumen (void) sebelum tutup shift.
                    </p>
                </div>
            </div>

            <div class="bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-200 text-xs shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-kp-blue-600"></span>
                    <span class="font-bold text-slate-900 font-mono">Shift Aktif</span>
                </div>
                <span class="text-slate-500 block text-xs mt-0.5">Hanya transaksi shift terbuka yang dapat diedit</span>
            </div>
        </div>

        <!-- Flash Message Alert -->
        @if(session('success'))
            <div class="p-3.5 bg-sky-50 border border-sky-200 rounded-xl text-xs font-semibold text-[#0E527B] flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-slate-400 hover:text-slate-700 text-sm font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs font-semibold text-rose-800 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- ============================================================== -->
        <!-- 2. DUA TABEL: KOREKSI KAS MASUK (KI) & KAS KELUAR (KO)         -->
        <!-- ============================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            
            <!-- Tabel Kas Masuk Open Shift (KI) -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-kp-blue-600"></span>
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Kas Masuk Shift Terbuka (KI)
                        </h3>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">{{ $openMasuk->count() }} Dokumen</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="px-4 py-3">No. Kuitansi</th>
                                <th class="px-4 py-3">Terima Dari</th>
                                <th class="px-4 py-3 text-right">Jumlah (Rp)</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($openMasuk as $ki)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono font-bold text-kp-blue-700">
                                    {{ $ki->no_kuitansi ?? $ki->no_bkm }}
                                    <span class="text-xs text-slate-400 font-sans block mt-0.5">{{ $ki->nama_divisi }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900">{{ $ki->terima_dari ?? $ki->nama_pembayar }}</div>
                                    @if($ki->metode_bayar)
                                        <span class="text-[11px] text-slate-400 font-mono uppercase">[{{ $ki->metode_bayar }}]</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($ki->jumlah_diterima ?? $ki->jumlah_dibayar, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button"
                                                data-edit-id="KI-{{ $ki->id }}"
                                                @click="openEdit({
                                                    id: {{ $ki->id }},
                                                    no_dokumen: '{{ $ki->no_kuitansi ?? $ki->no_bkm }}',
                                                    tipe: 'KI',
                                                    nama_pihak: '{{ addslashes($ki->terima_dari ?? $ki->nama_pembayar ?? '') }}',
                                                    jumlah_nominal: {{ $ki->jumlah_diterima ?? $ki->jumlah_dibayar ?? 0 }},
                                                    metode_bayar: '{{ $ki->metode_bayar ?? 'tunai' }}',
                                                    bank_tujuan: '{{ addslashes($ki->bank_tujuan ?? '') }}',
                                                    no_referensi_transfer: '{{ addslashes($ki->no_referensi_transfer ?? '') }}',
                                                    no_blanko: '{{ addslashes($ki->no_blanko ?? '') }}',
                                                    penagih_inkaso: '{{ addslashes($ki->penagih_inkaso ?? '') }}',
                                                    keterangan: '{{ addslashes($ki->keterangan ?? '') }}'
                                                })"
                                                class="px-2.5 py-1 bg-sky-50 hover:bg-sky-100 text-kp-blue-700 border border-sky-200 font-semibold text-xs rounded-lg transition cursor-pointer flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </button>
                                        <button type="button"
                                                @click="openVoid({{ $ki->id }}, '{{ $ki->no_kuitansi ?? $ki->no_bkm }}', 'KI', '{{ addslashes($ki->terima_dari ?? $ki->nama_pembayar ?? '') }}', {{ $ki->jumlah_diterima ?? $ki->jumlah_dibayar ?? 0 }})"
                                                class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold text-xs rounded-lg transition cursor-pointer">
                                            Void
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-slate-400 text-sm">Tidak ada kas masuk yang belum dikunci.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel Kas Keluar Open Shift (KO) -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Kas Keluar Shift Terbuka (KO)
                        </h3>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">{{ $openKeluar->count() }} Dokumen</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="px-4 py-3">No. BKK (KO)</th>
                                <th class="px-4 py-3">Penerima Dana</th>
                                <th class="px-4 py-3 text-right">Jumlah (Rp)</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($openKeluar as $ko)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono font-bold text-rose-600">
                                    {{ $ko->no_bkk }}
                                    <span class="text-xs text-slate-400 font-sans block mt-0.5">{{ $ko->nama_divisi }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900">{{ $ko->diberikan_kepada ?? $ko->nama_penerima }}</div>
                                    @if($ko->metode_bayar)
                                        <span class="text-[11px] text-slate-400 font-mono uppercase">[{{ $ko->metode_bayar }}]</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-rose-600">
                                    Rp {{ number_format($ko->jumlah_keluar, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button"
                                                data-edit-id="KO-{{ $ko->id }}"
                                                @click="openEdit({
                                                    id: {{ $ko->id }},
                                                    no_dokumen: '{{ $ko->no_bkk }}',
                                                    tipe: 'KO',
                                                    nama_pihak: '{{ addslashes($ko->diberikan_kepada ?? $ko->nama_penerima ?? '') }}',
                                                    jumlah_nominal: {{ $ko->jumlah_keluar }},
                                                    metode_bayar: '{{ $ko->metode_bayar ?? 'kas_fisik' }}',
                                                    bank_tujuan: '',
                                                    no_referensi_transfer: '{{ addslashes($ko->no_cek ?? '') }}',
                                                    no_blanko: '',
                                                    penagih_inkaso: '',
                                                    keterangan: '{{ addslashes($ko->keterangan ?? '') }}'
                                                })"
                                                class="px-2.5 py-1 bg-sky-50 hover:bg-sky-100 text-kp-blue-700 border border-sky-200 font-semibold text-xs rounded-lg transition cursor-pointer flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </button>
                                        <button type="button"
                                                @click="openVoid({{ $ko->id }}, '{{ $ko->no_bkk }}', 'KO', '{{ addslashes($ko->diberikan_kepada ?? $ko->nama_penerima ?? '') }}', {{ $ko->jumlah_keluar }})"
                                                class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold text-xs rounded-lg transition cursor-pointer">
                                            Void
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-slate-400 text-sm">Tidak ada kas keluar yang belum dikunci.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 3. PURE DATA TABLE (AUDIT TRAIL: DOKUMEN BATAL / VOID)         -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                        Audit Trail: Dokumen yang Dibatalkan / Void
                    </h3>
                </div>
                <span class="text-xs text-slate-500">{{ $voidHistory->count() }} Dokumen Terdaftar</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="px-4 py-3">No. Dokumen</th>
                            <th class="px-4 py-3">Waktu Void</th>
                            <th class="px-4 py-3">Nominal Dibatalkan</th>
                            <th class="px-4 py-3">Alasan Pembatalan / Audit</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($voidHistory as $vh)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $vh->no_kuitansi ?? $vh->no_bkm }}</td>
                            <td class="px-4 py-3 text-slate-500 text-xs font-mono">{{ $vh->void_at ? \Carbon\Carbon::parse($vh->void_at)->format('d/m/Y H:i') : '-' }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-rose-600">Rp {{ number_format($vh->jumlah_diterima ?? $vh->jumlah_dibayar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-slate-600 text-xs">{{ $vh->alasan_void ?? $vh->void_reason }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    VOID / BATAL
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada transaksi yang dibatalkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 4. MODAL EDIT INPUTAN TRANSAKSI (KI / KO)                      -->
        <!-- ============================================================== -->
        <div x-cloak x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showEditModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between shrink-0 bg-slate-50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-sky-50 text-kp-blue-700 border border-sky-200 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                                <span>Edit Inputan Transaksi</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-mono font-bold"
                                      :class="editDoc.tipe === 'KI' ? 'bg-sky-50 text-kp-blue-700 border border-sky-200' : 'bg-rose-50 text-rose-700 border border-rose-200'"
                                      x-text="editDoc.tipe"></span>
                            </h4>
                            <p class="text-xs text-slate-500 font-mono" x-text="'Dokumen: ' + editDoc.no_dokumen"></p>
                        </div>
                    </div>
                    <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
                </div>

                <form method="POST" :action="editAction" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="tipe_dokumen" :value="editDoc.tipe">

                    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3.5">
                        <div class="p-3 bg-sky-50 border border-sky-200 rounded-xl text-xs text-slate-700 leading-relaxed">
                            Koreksi data transaksi kasir. Perubahan nominal akan otomatis menyinkronkan saldo buku kas dan tagihan piutang terkait.
                        </div>

                        <!-- Pihak Pembayar / Penerima -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                <span x-text="editDoc.tipe === 'KI' ? 'Nama Pembeli / Pembayar / Klien' : 'Diberikan Kepada / Penerima Dana'"></span>
                                <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" 
                                   name="nama_pihak" 
                                   x-model="editDoc.nama_pihak" 
                                   required
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                        </div>

                        <!-- Nominal Transaksi -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nominal Transaksi (Rp) <span class="text-rose-600">*</span>
                            </label>
                            <input type="number" 
                                   step="1" 
                                   min="1" 
                                   name="jumlah_nominal" 
                                   x-model="editDoc.jumlah_nominal" 
                                   required
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 font-mono font-bold transition">
                            <span class="text-xs text-slate-400 font-mono mt-1 block">
                                Subtotal: Rp <span x-text="new Intl.NumberFormat('id-ID').format(editDoc.jumlah_nominal || 0)"></span>
                            </span>
                        </div>

                        <!-- Metode Pembayaran & Bank -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Metode Pembayaran</label>
                                <select name="metode_bayar" 
                                        x-model="editDoc.metode_bayar" 
                                        class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                                    <option value="tunai">Tunai / Kas Fisik</option>
                                    <option value="transfer">Transfer Bank</option>
                                    <option value="qris">QRIS / Digital</option>
                                    <option value="cek" x-show="editDoc.tipe === 'KO'">Cek Tunai Bank</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Bank Tujuan</label>
                                <input type="text" 
                                       name="bank_tujuan" 
                                       x-model="editDoc.bank_tujuan" 
                                       placeholder="Contoh: BJB / BCA"
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                            </div>
                        </div>

                        <!-- No Ref Transfer & No Blanko -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                    <span x-text="editDoc.tipe === 'KO' ? 'No. Cek / Ref' : 'No. Ref Transfer'"></span>
                                </label>
                                <input type="text" 
                                       name="no_referensi_transfer" 
                                       x-model="editDoc.no_referensi_transfer" 
                                       placeholder="Opsional"
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 font-mono focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">No. Blanko / Fisik</label>
                                <input type="text" 
                                       name="no_blanko" 
                                       x-model="editDoc.no_blanko" 
                                       placeholder="Opsional"
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 font-mono focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                            </div>
                        </div>

                        <!-- Petugas Inkaso -->
                        <div x-show="editDoc.tipe === 'KI'">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Petugas Inkaso / Kasir</label>
                            <input type="text" 
                                   name="penagih_inkaso" 
                                   x-model="editDoc.penagih_inkaso" 
                                   placeholder="Kasir Langsung"
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                        </div>

                        <!-- Keterangan / Uraian -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Uraian Transaksi / Keterangan</label>
                            <textarea name="keterangan" 
                                      x-model="editDoc.keterangan" 
                                      rows="2" 
                                      placeholder="Keterangan transaksi..."
                                      class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition"></textarea>
                        </div>

                        <!-- Alasan Koreksi (Audit Trail) -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alasan Koreksi / Catatan Pengeditan <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" 
                                   name="alasan_koreksi" 
                                   x-model="editDoc.alasan_koreksi" 
                                   required 
                                   minlength="3"
                                   placeholder="Contoh: Koreksi salah ketik nama pelanggan / koreksi nominal..."
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition">
                        </div>
                    </div>

                    <div class="px-5 py-3.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" 
                                @click="showEditModal = false" 
                                class="h-10 px-4 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl border border-slate-300 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 5. MODAL KONFIRMASI VOID TRANSAKSI                             -->
        <!-- ============================================================== -->
        <div x-cloak x-show="showVoidModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showVoidModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between shrink-0 bg-slate-50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Konfirmasi Void / Batalkan Dokumen</h4>
                            <p class="text-xs text-slate-500">Tindakan ini akan mengembalikan saldo piutang & membatalkan mutasi</p>
                        </div>
                    </div>
                    <button type="button" @click="showVoidModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
                </div>

                <form method="POST" :action="voidAction" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="tipe_dokumen" :value="activeDoc.tipe">

                    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3.5">
                        <div class="bg-rose-50/60 p-4 rounded-xl border border-rose-200 text-xs space-y-2 font-mono">
                            <div class="flex justify-between">
                                <span class="font-sans text-slate-600">No. Dokumen:</span>
                                <span class="font-bold text-slate-900" x-text="activeDoc.no_dokumen"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-sans text-slate-600">Pihak Terkait:</span>
                                <span class="font-bold font-sans text-slate-800" x-text="activeDoc.pihak"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-sans text-slate-600">Nominal:</span>
                                <span class="font-black text-rose-700">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeDoc.nominal)"></span></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alasan Pembatalan / Void <span class="text-rose-600">*</span>
                            </label>
                            <textarea name="alasan_void" 
                                      required 
                                      minlength="5" 
                                      rows="3" 
                                      placeholder="Jelaskan alasan pembatalan (misal: salah input nominal tagihan / faktur keliru)..."
                                      class="w-full p-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition"></textarea>
                        </div>
                    </div>

                    <div class="px-5 py-3.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" 
                                @click="showVoidModal = false" 
                                class="h-10 px-4 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl border border-slate-300 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="h-10 px-4 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] cursor-pointer">
                            Proses Void Dokumen
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>