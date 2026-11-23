<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Bersih (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Hitung Pembayaran Iklan
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Kartu Iklan
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5 truncate">
                        Penarikan kwitansi pelunasan kasir dan pembentukan jurnal kas/bank.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if($sudahDihitung)
                    <div class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 px-3 py-2 rounded-xl border border-kp-blue-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Sudah Dihitung ({{ $logHitung?->nomor_jurnal ?? 'Terekam GL' }})</span>
                    </div>
                @else
                    <div class="text-xs font-semibold text-slate-600 bg-slate-100 px-3 py-2 rounded-xl border border-slate-200 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        <span>Belum Dihitung</span>
                    </div>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Metric Cards (Report Informasi Pembayaran & Kas Masuk) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Jumlah Bukti</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">{{ number_format($totalCount) }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Pelunasan</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalBayar, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">PPh 23 Dipotong</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalPPh, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Kas Masuk Netto</span>
                <div class="text-lg font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($totalNettoKas, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Toolbar Pencarian & Filter Mandiri (Format Standar Divisi Iklan) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.iklan.hitung-pembayaran') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Pencarian Data
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q', $q ?? '') }}" 
                           placeholder="Ketik no kwitansi, faktur, penyetor, atau keterangan..."
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Metode Pembayaran
                    </label>
                    <select name="metode" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all" {{ request('metode', $metode ?? 'all') === 'all' ? 'selected' : '' }}>Semua Metode</option>
                        <option value="tunai" {{ request('metode', $metode ?? '') === 'tunai' ? 'selected' : '' }}>Kasir Tunai</option>
                        <option value="bca" {{ request('metode', $metode ?? '') === 'bca' ? 'selected' : '' }}>Transfer BCA</option>
                        <option value="bjb" {{ request('metode', $metode ?? '') === 'bjb' ? 'selected' : '' }}>Transfer BJB</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Tanggal Penerimaan
                    </label>
                    <input type="date" 
                           name="tanggal" 
                           value="{{ $tanggalPilihan }}" 
                           class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'metode']) || request('tanggal') != date('Y-m-d'))
                        <a href="{{ route('accounting.iklan.hitung-pembayaran') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tombol Aksi Posting Kas/Bank (Terpisah Mandiri) -->
        <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
            <div class="text-xs text-slate-500">
                Penerimaan kasir tanggal <span class="font-mono font-bold text-slate-800">{{ \Carbon\Carbon::parse($tanggalPilihan)->isoFormat('D MMMM Y') }}</span>
            </div>
            <div>
                @if(!$sudahDihitung)
                    <form method="POST" action="{{ route('accounting.iklan.proses-hitung-pembayaran') }}">
                        @csrf
                        <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                        <button type="submit" 
                                onclick="return confirmSubmit(this, event, {
                                    title: 'Posting Kas/Bank Pelunasan?',
                                    text: 'Proses hitung pelunasan piutang iklan tanggal {{ $tanggalPilihan }}? Data jurnal kas/bank akan dibentuk ke GL.',
                                    theme: 'blue',
                                    icon: 'journal',
                                    confirmText: 'Ya, Hitung &amp; Posting'
                                })"
                                class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            <span>Hitung &amp; Posting Kas/Bank</span>
                        </button>
                    </form>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 border border-slate-200 text-slate-600 font-semibold text-xs rounded-xl">
                        <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Data Pembayaran Tanggal Ini Sudah Diposting ke GL</span>
                    </span>
                @endif
            </div>
        </div>

        <!-- Tabel Pembayaran -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">No. Kwitansi / BKM</th>
                            <th class="py-3 px-3.5">No. Faktur Ref</th>
                            <th class="py-3 px-3.5">Pemasang / Pembayar</th>
                            <th class="py-3 px-3.5">Metode Bayar</th>
                            <th class="py-3 px-3.5 text-right">Potongan PPh 23</th>
                            <th class="py-3 px-3.5 text-right">Nominal Bayar</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700 text-sm">
                        @forelse($daftarBayar as $key => $b)
                            @php
                                $noKwitansi = $b->no_kuitansi ?? $b->no_bkm ?? ('BKM-' . $b->id);
                                $noFakturRef = $b->no_faktur_ref ?? $b->transaksiKoran?->nofakturkoran ?? '-';
                                $pembayar = $b->nama_pemasangkoran ?? $b->nama_pembayar ?? $b->terima_dari ?? $b->transaksiKoran?->nama_pemasangkoran ?? '-';
                                $metode = $b->metode_bayar ?? 'tunai';
                                $pph = (float) ($b->potongan_pajak ?? 0);
                                $nominal = (float) ($b->jumlah_dibayar ?? $b->jumlah_diterima ?? 0);
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-slate-400 font-mono text-xs">{{ $key + 1 }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $noKwitansi }}</td>
                                <td class="py-3 px-3.5 text-slate-600 font-mono">{{ $noFakturRef }}</td>
                                <td class="py-3 px-3.5 max-w-xs truncate text-slate-800">{{ $pembayar }}</td>
                                <td class="py-3 px-3.5">
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold uppercase {{ $metode === 'transfer' ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $metode }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono text-slate-700">Rp {{ number_format($pph, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($nominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <x-action-dropdown align="right">
                                        <a href="{{ route('accounting.iklan.belanja', ['q' => $pembayar]) }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <span>Riwayat Belanja</span>
                                        </a>
                                        <a href="{{ route('accounting.cetak.iklan', ['tab' => 'pembayaran', 'tgl_mulai' => $tanggalPilihan, 'tgl_selesai' => $tanggalPilihan]) }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            <span>Cetak Bukti Kasir</span>
                                        </a>
                                    </x-action-dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400 text-sm">
                                    Tidak ada data pembayaran iklan pada filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
