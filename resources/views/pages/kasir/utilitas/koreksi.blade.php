<x-app-layout>
    <div class="space-y-5" x-data="{
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

        {{-- Flash Message Alert --}}
        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-kp-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-kp-blue-600 hover:text-kp-blue-800 text-base font-bold leading-none cursor-pointer">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-slate-100 border border-slate-300 text-slate-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-slate-500 hover:text-slate-800 text-base font-bold leading-none cursor-pointer">&times;</button>
            </div>
        @endif

        {{-- Header Halaman --}}
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Koreksi & Pembatalan Transaksi (Void)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Pembatalan kuitansi salah input pada shift kasir yang sedang terbuka.
                    </p>
                </div>
            </div>

            <div class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs shrink-0">
                <span class="text-slate-500">Shift Terbuka:</span>
                <span class="font-mono font-bold text-kp-blue-700 ml-1">#{{ $shift->id }}</span>
            </div>
        </div>

        {{-- 2 Kolom: Koreksi Kas Masuk & Kas Keluar --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- Tabel Kas Masuk Open Shift --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900">Kas Masuk Shift Terbuka (KI)</h2>
                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                        {{ $openMasuk->count() }} Dokumen
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5">No. Kuitansi</th>
                                <th class="py-3 px-3.5">Terima Dari</th>
                                <th class="py-3 px-3.5 text-right">Jumlah (Rp)</th>
                                <th class="py-3 px-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($openMasuk as $ki)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <span class="font-mono font-bold text-kp-blue-700 text-sm">{{ $ki->no_kuitansi ?? $ki->no_bkm }}</span>
                                    @if($ki->nama_divisi)
                                        <span class="text-xs text-slate-400 block">{{ $ki->nama_divisi }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $ki->terima_dari ?? $ki->nama_pembayar }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums font-semibold text-slate-900 whitespace-nowrap">
                                    Rp {{ number_format($ki->jumlah_diterima ?? $ki->jumlah_dibayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                    <button type="button"
                                            @click="openVoid({{ $ki->id }}, '{{ $ki->no_kuitansi ?? $ki->no_bkm }}', 'KI', '{{ addslashes($ki->terima_dari ?? $ki->nama_pembayar) }}', {{ $ki->jumlah_diterima ?? $ki->jumlah_dibayar }})"
                                            class="px-3 py-1.5 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 font-semibold text-xs rounded-xl transition cursor-pointer">
                                        Void KI
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-8 px-4 text-center text-sm text-slate-400">Tidak ada kas masuk pada shift terbuka.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tabel Kas Keluar Open Shift --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900">Kas Keluar Shift Terbuka (KO)</h2>
                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $openKeluar->count() }} Dokumen
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5">No. BKK (KO)</th>
                                <th class="py-3 px-3.5">Penerima Dana</th>
                                <th class="py-3 px-3.5 text-right">Jumlah (Rp)</th>
                                <th class="py-3 px-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($openKeluar as $ko)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <span class="font-mono font-bold text-kp-blue-700 text-sm">{{ $ko->no_bkk }}</span>
                                    @if($ko->nama_divisi)
                                        <span class="text-xs text-slate-400 block">{{ $ko->nama_divisi }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $ko->diberikan_kepada ?? $ko->nama_penerima }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums font-semibold text-slate-900 whitespace-nowrap">
                                    Rp {{ number_format($ko->jumlah_keluar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                    <button type="button"
                                            @click="openVoid({{ $ko->id }}, '{{ $ko->no_bkk }}', 'KO', '{{ addslashes($ko->diberikan_kepada ?? $ko->nama_penerima) }}', {{ $ko->jumlah_keluar }})"
                                            class="px-3 py-1.5 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 font-semibold text-xs rounded-xl transition cursor-pointer">
                                        Void KO
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-8 px-4 text-center text-sm text-slate-400">Tidak ada kas keluar pada shift terbuka.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tabel Riwayat Pembatalan (Audit Log Void) --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Riwayat Dokumen Dibatalkan (Audit Void)</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. Dokumen</th>
                            <th class="py-3 px-3.5">Waktu Void</th>
                            <th class="py-3 px-3.5 text-right">Nominal Dibatalkan</th>
                            <th class="py-3 px-3.5">Alasan Pembatalan</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($voidHistory as $vh)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $loop->iteration }}</td>
                            <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm whitespace-nowrap">{{ $vh->no_kuitansi ?? $vh->no_bkm }}</td>
                            <td class="py-3.5 px-3.5 font-mono text-xs text-slate-600 whitespace-nowrap">{{ $vh->void_at ? \Carbon\Carbon::parse($vh->void_at)->format('d/m/Y H:i') : '-' }}</td>
                            <td class="py-3.5 px-3.5 text-right font-mono tabular-nums font-semibold text-slate-700 whitespace-nowrap">Rp {{ number_format($vh->jumlah_diterima ?? $vh->jumlah_dibayar, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3.5 text-slate-700">{{ $vh->void_reason }}</td>
                            <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-400 border border-slate-200">
                                    VOID / BATAL
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 px-4 text-center text-sm text-slate-400">Belum ada transaksi yang dibatalkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Konfirmasi Void Transaksi --}}
        <div x-cloak x-show="showVoidModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.away="showVoidModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between shrink-0">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Konfirmasi Void Dokumen</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Membatalkan mutasi kas dan mengembalikan status tagihan/pengajuan.</p>
                    </div>
                    <button type="button" @click="showVoidModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold leading-none cursor-pointer">&times;</button>
                </div>

                <form method="POST" :action="voidAction" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="tipe_dokumen" :value="activeDoc.tipe">

                    <div class="flex-1 overflow-y-auto p-5 space-y-4">
                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 text-xs space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-slate-500">No. Dokumen</span>
                                <span class="font-mono font-bold text-kp-blue-700" x-text="activeDoc.no_dokumen"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Pihak Terkait</span>
                                <span class="font-semibold text-slate-900" x-text="activeDoc.pihak"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Nominal</span>
                                <span class="font-mono font-bold text-slate-900 tabular-nums">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeDoc.nominal)"></span></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alasan Pembatalan / Void <span class="text-rose-600">*</span></label>
                            <textarea name="alasan_void" required minlength="5" rows="3" placeholder="Tuliskan alasan pembatalan dokumen (minimal 5 karakter)..."
                                      class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs"></textarea>
                        </div>
                    </div>

                    <div class="px-5 py-3.5 border-t border-slate-200 bg-white flex items-center justify-end gap-2.5 shrink-0">
                        <button type="button" @click="showVoidModal = false" class="px-4 h-10 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 h-10 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition cursor-pointer">
                            Proses Void Dokumen
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
