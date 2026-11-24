<x-app-layout>
    <div x-data="kasirInkasoApp()" x-init="init()" class="space-y-6">

        <!-- 1. Header Loket Kasir Penerimaan Setoran Inkaso (Standar 60-30-10 Kp-Blue) -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <h1 class="text-xl font-extrabold text-blue-950 tracking-tight">
                            VERIFIKASI &amp; APPROVAL SETORAN INKASO
                        </h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Loket Kasir &bull; Scan Barcode BSK Lapangan &bull; Rekonsiliasi Uang Fisik &amp; Penerbitan BKM
                    </p>
                </div>
            </div>

            <!-- Info Shift Kasir -->
            <div class="flex items-center gap-3 flex-wrap">
                <div class="bg-blue-50/70 border border-blue-200 rounded-xl px-3 py-1.5 text-xs text-blue-950 font-semibold flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full {{ $shift ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                    <span>Shift: <strong>{{ $shift ? $shift->nama_shift : 'Belum Buka' }}</strong></span>
                </div>

                <a href="{{ route('kasir.penerimaan.sirkulasi') }}" class="h-9 px-3.5 rounded-xl border border-slate-200 text-slate-700 hover:text-blue-900 hover:bg-slate-50 text-xs font-bold flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                    <span>Penerimaan Sirkulasi</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center justify-between shadow-xs">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </span>
            </div>
        @endif
        @if(session('warning'))
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-bold flex items-center justify-between shadow-xs">
                <span>⚠ {{ session('warning') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-bold flex items-center justify-between shadow-xs">
                <span>✕ {{ session('error') }}</span>
            </div>
        @endif

        <!-- 2. SCANNER BARCODE BSK OTOMATIS (Mendukung Scanner Laser USB & Input Keyboard) -->
        <div class="bg-gradient-to-r from-blue-900 via-blue-950 to-slate-900 rounded-2xl p-5 text-white shadow-md">
            <div class="max-w-3xl mx-auto space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 rounded-lg bg-amber-400 text-slate-950">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        </span>
                        <h2 class="text-sm font-extrabold tracking-wide uppercase text-amber-300">
                            Pemindai Barcode / QR Bukti Setoran Koleksi (BSK)
                        </h2>
                    </div>
                    <span class="text-[11px] text-slate-300">Scan satu per satu per setoran faktur</span>
                </div>

                <div class="relative">
                    <input type="text" 
                           x-ref="barcodeInput"
                           x-model="scanInput"
                           @keydown.enter.prevent="cariBarcode(scanInput)"
                           autofocus
                           placeholder="Arahkan barcode scanner laser ke HP Inkaso atau ketik nomor BSK (misal BSK-INK-20261124-0001) lalu Enter..."
                           class="w-full pl-4 pr-24 py-3.5 rounded-xl bg-white text-slate-900 text-sm font-mono font-bold focus:ring-4 focus:ring-amber-400/50 border-none shadow-inner placeholder-slate-400">
                    
                    <button type="button" 
                            @click="cariBarcode(scanInput)"
                            :disabled="isSearching"
                            class="absolute right-2 top-2 bottom-2 px-4 rounded-lg bg-amber-400 hover:bg-amber-500 text-slate-950 text-xs font-black transition cursor-pointer flex items-center gap-1.5 shadow-xs">
                        <span x-show="!isSearching">Cari BSK</span>
                        <span x-show="isSearching" x-cloak>Mencari...</span>
                    </button>
                </div>

                <div class="flex items-center justify-between text-[11px] text-slate-300 pt-1">
                    <span>💡 <strong>Tip Loket:</strong> Scanner laser USB akan langsung otomatis mendeteksi kode BSK begitu barcode di layar HP Inkaso terbaca.</span>
                    <span x-show="scanError" x-text="scanError" class="text-amber-300 font-bold" x-cloak></span>
                </div>
            </div>
        </div>

        <!-- 3. KPI RINGKAS SETORAN HARI INI -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white border border-amber-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-[10px] uppercase font-bold text-amber-800 tracking-wider block">Menunggu Approval (Pending)</span>
                <div class="mt-2 text-2xl font-black text-slate-900 tracking-tight">
                    {{ $pendingSetoran->count() }} <span class="text-xs font-medium text-slate-500">Transaksi</span>
                </div>
                <div class="mt-2 text-xs text-amber-900 font-bold">
                    Total: Rp {{ number_format($totalPendingNominal, 0, ',', '.') }}
                </div>
            </div>

            <div class="bg-white border border-emerald-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-[10px] uppercase font-bold text-emerald-800 tracking-wider block">Disetujui Hari Ini (Approved)</span>
                <div class="mt-2 text-2xl font-black text-slate-900 tracking-tight">
                    {{ $approvedHariIni->count() }} <span class="text-xs font-medium text-slate-500">Kuitansi BKM</span>
                </div>
                <div class="mt-2 text-xs text-emerald-900 font-bold">
                    Total Kas Masuk: Rp {{ number_format($totalApprovedNominal, 0, ',', '.') }}
                </div>
            </div>

            <div class="bg-white border border-blue-200 rounded-2xl p-4 sm:p-5 shadow-xs">
                <span class="text-[10px] uppercase font-bold text-blue-800 tracking-wider block">Total Setoran Inkaso Hari Ini</span>
                <div class="mt-2 text-2xl font-black text-slate-900 tracking-tight">
                    {{ $pendingSetoran->count() + $approvedHariIni->count() }} <span class="text-xs font-medium text-slate-500">Berkas BSK</span>
                </div>
                <div class="mt-2 text-xs text-blue-900 font-bold">
                    Akumulasi: Rp {{ number_format($totalPendingNominal + $totalApprovedNominal, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- 4. TABEL ANTREAN SETORAN MENUNGGU VERIFIKASI (PENDING) -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <h3 class="text-sm font-extrabold text-blue-950 uppercase tracking-tight">
                        Antrean Setoran Inkaso Menunggu Verifikasi ({{ $pendingSetoran->count() }})
                    </h3>
                </div>
                <span class="text-xs text-slate-400">Verifikasi fisik uang sebelum klik Approve</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50/80 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. BSK &amp; Waktu</th>
                            <th class="py-3 px-4">Petugas Inkaso</th>
                            <th class="py-3 px-4">No. Faktur</th>
                            <th class="py-3 px-4">Nama Pelanggan/Agen</th>
                            <th class="py-3 px-4">Nominal Disetor</th>
                            <th class="py-3 px-4">Keterangan Angsuran</th>
                            <th class="py-3 px-4 text-center">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pendingSetoran as $setoran)
                            <tr class="hover:bg-blue-50/30 transition">
                                <td class="py-3 px-4 font-mono font-bold text-blue-900">
                                    {{ $setoran->no_bsk }}
                                    <div class="text-[10px] text-slate-400 font-sans font-normal">
                                        {{ $setoran->waktu_terima_lapangan ? $setoran->waktu_terima_lapangan->format('d/m/Y H:i') : '-' }} WIB
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-900">
                                    {{ $setoran->petugas->name ?? 'Petugas Inkaso' }}
                                </td>
                                <td class="py-3 px-4 font-mono font-semibold text-slate-800">
                                    {{ $setoran->faktur->no_faktur ?? '-' }}
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-900">
                                    {{ $setoran->nama_pembayar }}
                                </td>
                                <td class="py-3 px-4 font-black text-sm text-blue-950">
                                    Rp {{ number_format($setoran->nominal_titipan, 0, ',', '.') }}
                                    <span class="block text-[10px] uppercase font-bold text-slate-400">
                                        {{ $setoran->metode_bayar }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-600 max-w-xs truncate">
                                    {{ $setoran->keterangan_angsuran }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" 
                                                @click="bukaApprovalModal({{ $setoran->id }}, '{{ $setoran->no_bsk }}', '{{ addslashes($setoran->petugas->name ?? '-') }}', '{{ $setoran->faktur->no_faktur ?? '-' }}', '{{ addslashes($setoran->nama_pembayar) }}', {{ (float) $setoran->nominal_titipan }}, '{{ addslashes($setoran->keterangan_angsuran) }}', '{{ strtoupper($setoran->metode_bayar) }}', {{ (float) ($setoran->faktur->sisa_piutang ?? 0) }})"
                                                class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition cursor-pointer flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Periksa &amp; Approve</span>
                                        </button>

                                        <button type="button" 
                                                @click="bukaRejectModal({{ $setoran->id }}, '{{ $setoran->no_bsk }}', '{{ addslashes($setoran->nama_pembayar) }}')"
                                                class="px-2.5 py-1.5 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 font-bold text-xs transition cursor-pointer" title="Tolak Setoran">
                                            ✕
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    Tidak ada setoran inkaso yang menunggu verifikasi saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. TABEL SETORAN INKASO DISETUJUI HARI INI (APPROVED) -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <h3 class="text-sm font-extrabold text-blue-950 uppercase tracking-tight">
                        Riwayat Setoran Disetujui Hari Ini ({{ $approvedHariIni->count() }})
                    </h3>
                </div>
                <span class="text-xs text-slate-400">Sudah sah masuk ke Buku Kas &amp; Terbit Kuitansi BKM</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50/80 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. Kuitansi BKM</th>
                            <th class="py-3 px-4">No. BSK Ref</th>
                            <th class="py-3 px-4">Pelanggan/Agen</th>
                            <th class="py-3 px-4">No. Faktur</th>
                            <th class="py-3 px-4">Kolektor Lapangan</th>
                            <th class="py-3 px-4">Nominal Masuk</th>
                            <th class="py-3 px-4 text-center">Cetak BKM</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($approvedHariIni as $appr)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-4 font-mono font-bold text-emerald-800">
                                    {{ $appr->no_kuitansi_kasir }}
                                    <div class="text-[10px] text-slate-400 font-sans font-normal">
                                        Approved: {{ $appr->approved_at ? $appr->approved_at->format('H:i') : '-' }} WIB
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-600">
                                    {{ $appr->no_bsk }}
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-900">
                                    {{ $appr->nama_pembayar }}
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-700">
                                    {{ $appr->faktur->no_faktur ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-slate-700">
                                    {{ $appr->petugas->name ?? '-' }}
                                </td>
                                <td class="py-3 px-4 font-black text-slate-900">
                                    Rp {{ number_format($appr->nominal_titipan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($appr->kas_masuk_id)
                                        <a href="{{ route('kasir.penerimaan.cetak', $appr->kas_masuk_id) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-[11px] font-bold inline-flex items-center gap-1 transition">
                                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            <span>Cetak</span>
                                        </a>
                                    @else
                                        <span class="text-slate-400 text-[11px]">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    Belum ada setoran inkaso yang disetujui hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- MODAL 1: VERIFIKASI FISIK UANG & APPROVAL KASIR -->
        <!-- ==================================================== -->
        <div x-show="showApprovalModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
            <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 space-y-5 animate-in fade-in zoom-in duration-150">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <h3 class="text-sm font-extrabold text-blue-950 uppercase tracking-tight">
                            Verifikasi Fisik Uang &amp; Approval Setoran Inkaso
                        </h3>
                    </div>
                    <button type="button" @click="showApprovalModal = false" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
                </div>

                <form :action="'{{ url('kasir/penerimaan/inkaso') }}/' + modalData.id + '/approve'" method="POST" class="space-y-4">
                    @csrf

                    <!-- Kartu Rekonsiliasi Digital vs Fisik -->
                    <div class="bg-blue-50/70 border border-blue-200 rounded-2xl p-4 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-blue-200/70">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Nomor BSK Koleksi</span>
                                <span class="text-xs font-mono font-black text-blue-950" x-text="modalData.noBsk"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Kolektor Penyetor</span>
                                <span class="text-xs font-bold text-slate-900" x-text="modalData.petugas"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-slate-500 block">Pelanggan/Agen:</span>
                                <span class="font-bold text-slate-900 block" x-text="modalData.customer"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block">No. Faktur:</span>
                                <span class="font-mono font-bold text-blue-900 block" x-text="modalData.noFaktur"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs pt-1 border-t border-blue-100">
                            <div>
                                <span class="text-slate-500 block">Keterangan Angsuran:</span>
                                <span class="font-medium text-slate-800 block" x-text="modalData.keterangan"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block">Metode Pembayaran:</span>
                                <span class="font-bold text-slate-900 block uppercase" x-text="modalData.metode"></span>
                            </div>
                        </div>

                        <!-- Rekonsiliasi Nominal (Besar) -->
                        <div class="bg-white p-3 rounded-xl border border-blue-200 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Nominal Uang Fisik Yang Wajib Dihitung</span>
                                <span class="text-xl font-black text-emerald-600 block">
                                    Rp <span x-text="Number(modalData.nominal).toLocaleString('id-ID')"></span>
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Sisa Piutang Berjalan</span>
                                <span class="text-xs font-bold text-rose-600 block">
                                    Rp <span x-text="Number(modalData.sisaPiutang).toLocaleString('id-ID')"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Input Catatan Kasir -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Verifikasi Kasir (Opsional)</label>
                        <input type="text" 
                               name="catatan_kasir" 
                               value="Fisik uang cocok dan telah diverifikasi oleh Kasir." 
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-600 text-xs text-slate-800">
                    </div>

                    <!-- Checklist Konfirmasi Fisik Kasir -->
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" required class="rounded text-blue-600 focus:ring-blue-600 w-4 h-4">
                            <span class="text-xs font-bold text-slate-800">
                                Saya telah menghitung dan memastikan fisik uang sebesar <strong class="text-blue-950">Rp <span x-text="Number(modalData.nominal).toLocaleString('id-ID')"></span></strong> diterima dengan lengkap.
                            </span>
                        </label>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2">
                        <button type="button" @click="showApprovalModal = false" class="py-2.5 px-4 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-sm transition cursor-pointer">
                            <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Approve &amp; Terbitkan BKM</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- MODAL 2: TOLAK SETORAN INKASO -->
        <!-- ==================================================== -->
        <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4 animate-in fade-in zoom-in duration-150">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                        <h3 class="text-sm font-extrabold text-rose-950 uppercase tracking-tight">
                            Tolak Setoran Inkaso Lapangan
                        </h3>
                    </div>
                    <button type="button" @click="showRejectModal = false" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
                </div>

                <form :action="'{{ url('kasir/penerimaan/inkaso') }}/' + modalReject.id + '/reject'" method="POST" class="space-y-3.5">
                    @csrf
                    <p class="text-xs text-slate-600">
                        Anda akan menolak setoran <strong class="text-slate-900 font-mono" x-text="modalReject.noBsk"></strong> untuk pelanggan <strong class="text-slate-900" x-text="modalReject.customer"></strong>.
                    </p>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Alasan Penolakan Kasir <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="alasan_penolakan" 
                                  required 
                                  rows="3" 
                                  placeholder="Contoh: Fisik uang kurang Rp 50.000 / Ditemukan uang rusak / Kuitansi salah..." 
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 text-xs text-slate-800"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <button type="button" @click="showRejectModal = false" class="py-2.5 px-3 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold">
                            Batal
                        </button>
                        <button type="submit" class="py-2.5 px-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition cursor-pointer">
                            Konfirmasi Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Script Kasir Interaksi Barcode Scanner -->
    <script>
    function kasirInkasoApp() {
        return {
            scanInput: '',
            isSearching: false,
            scanError: '',
            showApprovalModal: false,
            showRejectModal: false,
            modalData: {
                id: null,
                noBsk: '',
                petugas: '',
                noFaktur: '',
                customer: '',
                nominal: 0,
                keterangan: '',
                metode: '',
                sisaPiutang: 0
            },
            modalReject: {
                id: null,
                noBsk: '',
                customer: ''
            },

            init() {
                // Focus scanner input on load
                this.$nextTick(() => {
                    if (this.$refs.barcodeInput) {
                        this.$refs.barcodeInput.focus();
                    }
                });
            },

            cariBarcode(token) {
                const clean = (token || '').trim();
                if (!clean) return;

                this.isSearching = true;
                this.scanError = '';

                fetch('{{ url("kasir/api/scan-bsk") }}/' + encodeURIComponent(clean))
                    .then(res => res.json())
                    .then(json => {
                        this.isSearching = false;
                        if (json.success && json.data) {
                            const d = json.data;
                            if (d.status_approval === 'approved') {
                                this.scanError = `Setoran ${d.no_bsk} sudah disetujui sebelumnya.`;
                                return;
                            }
                            this.bukaApprovalModal(
                                d.id,
                                d.no_bsk,
                                d.petugas_nama,
                                d.no_faktur,
                                d.nama_pembayar,
                                d.nominal_titipan,
                                d.keterangan_angsuran,
                                d.metode_bayar,
                                d.sisa_piutang_faktur
                            );
                            this.scanInput = '';
                        } else {
                            this.scanError = json.message || 'Kode BSK tidak ditemukan.';
                        }
                    })
                    .catch(err => {
                        this.isSearching = false;
                        this.scanError = 'Gagal memindai barcode. Periksa koneksi.';
                    });
            },

            bukaApprovalModal(id, noBsk, petugas, noFaktur, customer, nominal, keterangan, metode, sisaPiutang) {
                this.modalData = {
                    id: id,
                    noBsk: noBsk,
                    petugas: petugas,
                    noFaktur: noFaktur,
                    customer: customer,
                    nominal: parseFloat(nominal) || 0,
                    keterangan: keterangan,
                    metode: metode,
                    sisaPiutang: parseFloat(sisaPiutang) || 0
                };
                this.showApprovalModal = true;
            },

            bukaRejectModal(id, noBsk, customer) {
                this.modalReject = {
                    id: id,
                    noBsk: noBsk,
                    customer: customer
                };
                this.showRejectModal = true;
            }
        };
    }
    </script>
</x-app-layout>
