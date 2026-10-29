<x-app-layout>
    <div class="space-y-6" x-data="{
        showVoidModal: false,
        voidAction: '',
        activeDoc: {
            id: null,
            no_dokumen: '',
            tipe: 'KI',
            pihak: '',
            nominal: 0,
            alasan: ''
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
        }
    }">

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

        <!-- Header Utilitas Koreksi -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-700 border border-orange-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Perbaikan & Koreksi Transaksi (Void)
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-orange-100 text-orange-800 border border-orange-300">
                            Audit Control
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pembatalan atau koreksi transaksi yang salah input sebelum Tutup Kas Harian (Closing). Mengembalikan saldo piutang / status BP secara otomatis
                    </p>
                </div>
            </div>

            <div class="bg-orange-50/80 px-3.5 py-2 rounded-xl border border-orange-200 text-xs text-orange-900">
                <span class="font-bold">Shift Aktif #{{ $shift->id }}</span>
                <span class="text-orange-700 block text-[11px]">Hanya transaksi shift terbuka yang dapat dikoreksi</span>
            </div>
        </div>

        <!-- 2 Kolom: Koreksi Kas Masuk & Kas Keluar -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Tabel Kas Masuk Open Shift -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-teal-50/40">
                    <h3 class="text-xs font-bold text-teal-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-teal-600"></span>
                        Kas Masuk Shift Terbuka (KI)
                    </h3>
                    <span class="text-xs font-bold text-teal-800">{{ $openMasuk->count() }} Dokumen</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 font-semibold">No. Kuitansi</th>
                                <th class="px-4 py-3 font-semibold">Terima Dari</th>
                                <th class="px-4 py-3 font-semibold text-right">Jumlah (Rp)</th>
                                <th class="px-4 py-3 font-semibold text-center">Aksi Void</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($openMasuk as $ki)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono font-bold text-teal-900">
                                    {{ $ki->no_kuitansi ?? $ki->no_bkm }}
                                    @php
                                        $divisiKi = match($ki->kode_bagian) {
                                            '01' => 'Divisi Iklan',
                                            '02' => 'Divisi Sirkulasi',
                                            '03' => 'Divisi Umum',
                                            '06' => 'Divisi Redaksi',
                                            '14' => 'Kas Bank BJB',
                                            default => 'Divisi ' . ($ki->kode_bagian ?? 'Umum')
                                        };
                                    @endphp
                                    <span class="text-[10px] text-slate-500 block">{{ $divisiKi }}</span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $ki->terima_dari ?? $ki->nama_pembayar }}</td>
                                <td class="px-4 py-3 text-right font-mono font-extrabold text-teal-800">
                                    Rp {{ number_format($ki->jumlah_diterima ?? $ki->jumlah_dibayar, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button"
                                            @click="openVoid({{ $ki->id }}, '{{ $ki->no_kuitansi ?? $ki->no_bkm }}', 'KI', '{{ addslashes($ki->terima_dari ?? $ki->nama_pembayar) }}', {{ $ki->jumlah_diterima ?? $ki->jumlah_dibayar }})"
                                            class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-[10px] rounded-lg transition shadow-2xs">
                                        Void KI
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-5 py-6 text-center text-slate-400">Tidak ada kas masuk yang belum dikunci.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel Kas Keluar Open Shift -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-rose-50/40">
                    <h3 class="text-xs font-bold text-rose-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        Kas Keluar Shift Terbuka (KO)
                    </h3>
                    <span class="text-xs font-bold text-rose-800">{{ $openKeluar->count() }} Dokumen</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 font-semibold">No. BKK (KO)</th>
                                <th class="px-4 py-3 font-semibold">Penerima Dana</th>
                                <th class="px-4 py-3 font-semibold text-right">Jumlah (Rp)</th>
                                <th class="px-4 py-3 font-semibold text-center">Aksi Void</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($openKeluar as $ko)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono font-bold text-rose-900">
                                    {{ $ko->no_bkk }}
                                    @php
                                        $divisiKo = match($ko->kode_bagian) {
                                            '01' => 'Divisi Iklan',
                                            '02' => 'Divisi Sirkulasi',
                                            '03' => 'Divisi Umum',
                                            '06' => 'Divisi Redaksi',
                                            '14' => 'Kas Bank BJB',
                                            default => 'Divisi ' . ($ko->kode_bagian ?? 'Umum')
                                        };
                                    @endphp
                                    <span class="text-[10px] text-slate-500 block">{{ $divisiKo }}</span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $ko->diberikan_kepada ?? $ko->nama_penerima }}</td>
                                <td class="px-4 py-3 text-right font-mono font-extrabold text-rose-800">
                                    Rp {{ number_format($ko->jumlah_keluar, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button"
                                            @click="openVoid({{ $ko->id }}, '{{ $ko->no_bkk }}', 'KO', '{{ addslashes($ko->diberikan_kepada ?? $ko->nama_penerima) }}', {{ $ko->jumlah_keluar }})"
                                            class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-[10px] rounded-lg transition shadow-2xs">
                                        Void KO
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-5 py-6 text-center text-slate-400">Tidak ada kas keluar yang belum dikunci.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabel Riwayat Pembatalan (Audit Log Void) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                    Audit Trail: Dokumen yang Dibatalkan / Void
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">No. Dokumen</th>
                            <th class="px-4 py-3 font-semibold">Waktu Void</th>
                            <th class="px-4 py-3 font-semibold">Nominal Dibatalkan</th>
                            <th class="px-4 py-3 font-semibold">Alasan Pembatalan / Audit</th>
                            <th class="px-4 py-3 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($voidHistory as $vh)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $vh->no_kuitansi ?? $vh->no_bkm }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $vh->void_at ? \Carbon\Carbon::parse($vh->void_at)->format('d/m/Y H:i') : '-' }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-slate-700">Rp {{ number_format($vh->jumlah_diterima ?? $vh->jumlah_dibayar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $vh->void_reason }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-300">
                                    VOID / BATAL
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-6 text-center text-slate-400">Belum ada transaksi yang dibatalkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Konfirmasi Void Transaksi -->
        <div x-cloak x-show="showVoidModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showVoidModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between shrink-0 bg-slate-50/50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Konfirmasi Void / Batalkan Dokumen</h4>
                            <p class="text-[11px] text-slate-500">Tindakan ini akan mengembalikan saldo piutang & membatalkan mutasi</p>
                        </div>
                    </div>
                    <button type="button" @click="showVoidModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none">&times;</button>
                </div>

                <form method="POST" :action="voidAction" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="tipe_dokumen" :value="activeDoc.tipe">

                    <div class="flex-1 overflow-y-auto px-5 py-3.5 space-y-3">
                        <div class="bg-rose-50/60 p-3.5 rounded-xl border border-rose-200 text-xs space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-slate-600">No. Dokumen:</span>
                                <span class="font-mono font-bold text-slate-900" x-text="activeDoc.no_dokumen"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Pihak Terkait:</span>
                                <span class="font-bold text-slate-800" x-text="activeDoc.pihak"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Nominal:</span>
                                <span class="font-mono font-black text-rose-800">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeDoc.nominal)"></span></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Pembatalan / Void (Wajib Diisi)</label>
                            <textarea name="alasan_void" required minlength="5" rows="3" placeholder="Jelaskan alasan pembatalan (misal: salah input nominal tagihan / faktur keliru)..."
                                      class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-rose-500 text-slate-900"></textarea>
                        </div>
                    </div>

                    <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showVoidModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition shadow-2xs">
                            Proses Void Dokumen
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
