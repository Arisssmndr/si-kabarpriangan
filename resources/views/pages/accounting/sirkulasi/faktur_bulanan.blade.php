<x-app-layout>
    <div class="space-y-6" x-data="{ openModalRetur: false, openModalGenerate: false, agenDipilih: '' }">
        <!-- Header Halaman (60-30-10) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Faktur Sirkulasi Bulanan
                        </h1>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Konsolidasi Bulanan
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Siklus penagihan terakumulasi per agen (1 Agen = 1 Faktur / Bulan) dengan rekonsiliasi retur fisik.
                    </p>
                </div>
            </div>

            <!-- Filter Periode & Aksi Utama -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <form method="GET" action="{{ route('accounting.sirkulasi.faktur-bulanan') }}" class="flex items-center gap-2">
                    <input type="month" name="bulan" value="{{ $bulanPilihan }}" 
                           class="h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                        Filter
                    </button>
                </form>

                <button type="button" @click="openModalRetur = true"
                        class="inline-flex items-center gap-2 h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>
                    <span>Input Retur (BAPR)</span>
                </button>

                <form method="POST" action="{{ route('accounting.sirkulasi.faktur-bulanan.generate') }}" onsubmit="return confirmSubmit(this, event, {
                    title: 'Generate Faktur Bulanan?',
                    text: 'Tarik akumulasi kirim sebulan dari Sirkulasi dan terbitkan 1 faktur konsolidasi per agen untuk periode {{ $bulanPilihan }}?',
                    theme: 'blue',
                    icon: 'refresh',
                    confirmText: 'Ya, Generate Faktur'
                })">
                    @csrf
                    <input type="hidden" name="bulan" value="{{ $bulanPilihan }}">
                    <button type="submit"
                            class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Generate Faktur Bulanan</span>
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 rounded-xl text-sm font-semibold text-kp-blue-700 flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-sm font-semibold text-rose-800 flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- 4 KPI Metrics Konsolidasi Bulanan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Oplah Kirim Gross</span>
                <p class="text-xl sm:text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalOplahGross, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-500">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Akumulasi seluruh agen sebulan</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Retur Fisik Disetujui</span>
                <p class="text-xl sm:text-2xl font-bold text-rose-600 mt-1 font-mono tabular-nums">
                    {{ number_format($totalReturEks, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-500">eks</span>
                </p>
                <span class="text-xs text-rose-500 mt-1 block font-mono">Potongan: Rp {{ number_format($totalPotonganRetur, 0, ',', '.') }}</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-semibold text-kp-blue-700 uppercase tracking-wider block">Total Tagihan Bersih</span>
                <p class="text-xl sm:text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalTagihanBersih, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-500 mt-1 block font-mono">{{ number_format($totalOplahNetto, 0, ',', '.') }} eks terjual bersih</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sisa Piutang Berjalan</span>
                <p class="text-xl sm:text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalSisaPiutang, 0, ',', '.') }}
                </p>
                <span class="text-xs text-kp-blue-700 mt-1 block font-mono">Terbayar: Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Tabel Faktur Bulanan Terkonsolidasi -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 sm:px-5 sm:py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Daftar Faktur Bulanan Agen (Periode {{ $bulanPilihan }})</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Tepat 1 nomor faktur per agen untuk seluruh distribusi koran selama 1 bulan kalender.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-slate-100 text-slate-700">
                        {{ $fakturList->total() }} Faktur Terbit
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5">No. Faktur</th>
                            <th class="py-3 px-3.5">Kode &amp; Nama Agen</th>
                            <th class="py-3 px-3.5">Wilayah</th>
                            <th class="py-3 px-3.5 text-right">Oplah Kirim</th>
                            <th class="py-3 px-3.5 text-right">Retur BAPR</th>
                            <th class="py-3 px-3.5 text-right">Oplah Bersih</th>
                            <th class="py-3 px-3.5 text-right">Tarif</th>
                            <th class="py-3 px-3.5 text-right w-36">Total Tagihan</th>
                            <th class="py-3 px-3.5 text-right w-32">Sisa Piutang</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($fakturList as $f)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700">
                                    {{ $f->no_faktur }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    {{ $f->nama_agen }}
                                    <span class="block text-xs font-mono font-normal text-slate-500">{{ $f->kode_agen }} &bull; {{ $f->kategori_agen === '11' ? 'Agen Besar' : 'Sub-Agen' }}</span>
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 text-xs">
                                    {{ $f->wilayah ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">
                                    {{ number_format($f->total_oplah_kirim, 0, ',', '.') }} eks
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-rose-600 tabular-nums">
                                    {{ number_format($f->total_retur_eks, 0, ',', '.') }} eks
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-bold text-kp-blue-700 tabular-nums">
                                    {{ number_format($f->oplah_bersih, 0, ',', '.') }} eks
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-xs tabular-nums">
                                    Rp {{ number_format($f->tarif_per_eks, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-bold text-slate-900 tabular-nums">
                                    Rp {{ number_format($f->total_tagihan_bersih, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-bold text-slate-700 tabular-nums">
                                    Rp {{ number_format($f->sisa_piutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($f->status_pembayaran === 'lunas')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            LUNAS
                                        </span>
                                    @elseif($f->status_pembayaran === 'sebagian')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-slate-100 text-slate-700 border border-slate-300">
                                            CICILAN
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            BELUM LUNAS
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <a href="{{ route('accounting.sirkulasi.faktur-bulanan.cetak', $f->id) }}" target="_blank"
                                       class="inline-flex items-center justify-center h-8 px-3 rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold shadow-2xs transition">
                                        Cetak Faktur
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-12 text-center text-slate-500">
                                    <div class="max-w-sm mx-auto">
                                        <p class="font-bold text-slate-900 text-sm">Faktur Bulanan Belum Diterbitkan</p>
                                        <p class="text-xs mt-1 text-slate-500">Klik tombol <strong>Generate Faktur Bulanan</strong> di atas untuk menarik data jatah koran dan menerbitkan faktur periode {{ $bulanPilihan }}.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($fakturList->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $fakturList->links() }}
                </div>
            @endif
        </div>

        <!-- Tabel Riwayat Verifikasi Retur Fisik Koran (BAPR) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 sm:px-5 sm:py-4 border-b border-slate-100 flex items-center justify-between bg-white">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Riwayat Verifikasi Fisik Retur Koran (BAPR)</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Berita Acara Pengembalian Retur fisik yang disetujui Akuntansi sebagai pengurang tagihan.</p>
                </div>
                <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-slate-100 text-slate-700">
                    {{ $returList->count() }} BAPR
                </span>
            </div>

            <div class="overflow-x-auto min-h-[160px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5">No. BAPR</th>
                            <th class="py-3 px-3.5">Tanggal Verifikasi</th>
                            <th class="py-3 px-3.5">Agen</th>
                            <th class="py-3 px-3.5 text-right">Retur Fisik Diserahkan</th>
                            <th class="py-3 px-3.5 text-right">Retur Disetujui</th>
                            <th class="py-3 px-3.5">Catatan Fisik Koran</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($returList as $r)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-3.5 font-mono font-bold text-kp-blue-700">
                                    {{ $r->no_bapr }}
                                </td>
                                <td class="py-3 px-3.5 text-xs text-slate-600">
                                    {{ \Carbon\Carbon::parse($r->tanggal_retur)->translatedFormat('d F Y') }}
                                </td>
                                <td class="py-3 px-3.5 font-semibold text-slate-900">
                                    {{ $r->nama_agen }}
                                    <span class="block text-xs font-mono font-normal text-slate-500">{{ $r->kode_agen }}</span>
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono tabular-nums">
                                    {{ number_format($r->jumlah_retur_fisik, 0, ',', '.') }} eks
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono font-bold text-rose-600 tabular-nums">
                                    {{ number_format($r->jumlah_retur_disetujui, 0, ',', '.') }} eks
                                </td>
                                <td class="py-3 px-3.5 text-xs text-slate-600 max-w-[250px] truncate" title="{{ $r->catatan_kondisi }}">
                                    {{ $r->catatan_kondisi ?? '-' }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        DISETUJUI
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-500 text-xs">
                                    Belum ada catatan fisik retur koran untuk periode {{ $bulanPilihan }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL INPUT & VERIFIKASI RETUR FISIK KORAN OLEH AKUNTANSI -->
        <div x-show="openModalRetur"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog"
             aria-modal="true"
             @keydown.escape.window="openModalRetur = false">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="openModalRetur"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs"
                     @click="openModalRetur = false"></div>

                <div x-show="openModalRetur"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative inline-block w-full max-w-lg p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-xl border border-slate-200">

                    <div class="flex items-start justify-between pb-3.5 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Verifikasi Fisik Retur Koran (BAPR)
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Masukkan jumlah koran sisa/retur yang disetujui sebagai pengurang tagihan resmi.
                            </p>
                        </div>
                        <button type="button" @click="openModalRetur = false" class="text-slate-400 hover:text-slate-600 rounded-lg p-1.5 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('accounting.sirkulasi.retur.store') }}" class="mt-4 space-y-4">
                        @csrf
                        <input type="hidden" name="periode_bulan" value="{{ $bulanPilihan }}">

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Agen Sirkulasi <span class="text-rose-500">*</span></label>
                            <select name="agen_id" required class="w-full h-10 px-3 text-xs rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                                <option value="">-- Pilih Agen --</option>
                                @foreach($daftarAgen as $ag)
                                    <option value="{{ $ag->id }}">{{ $ag->nama_agen }} ({{ $ag->kode_agen }}) - {{ $ag->wilayah ?? 'Rute' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Verifikasi <span class="text-rose-500">*</span></label>
                                <input type="date" name="tanggal_retur" value="{{ date('Y-m-d') }}" required
                                       class="w-full h-10 px-3 text-xs rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Retur Disetujui (Eks) <span class="text-rose-500">*</span></label>
                                <input type="number" name="jumlah_retur_disetujui" min="0" required placeholder="Contoh: 25"
                                       class="w-full h-10 px-3 text-xs rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600 font-mono font-bold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Kondisi Fisik Koran (Opsional)</label>
                            <textarea name="catatan_kondisi" rows="2" placeholder="Catatan keutuhan halaman, nomor edisi tanggal retur..."
                                      class="w-full px-3 py-2 text-xs rounded-xl border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                            <button type="button" @click="openModalRetur = false" class="h-9 px-4 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="h-9 px-4 text-xs font-bold text-white bg-kp-blue-600 hover:bg-kp-blue-700 rounded-xl shadow-xs transition cursor-pointer">
                                Simpan Verifikasi Retur
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
