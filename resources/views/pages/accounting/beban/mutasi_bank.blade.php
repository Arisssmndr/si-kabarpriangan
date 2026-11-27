<x-app-layout>
    <div class="space-y-5" x-data="{ modalRekon: false }">

        <!-- 1. Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Mutasi &amp; Rekonsiliasi Bank
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Bank &amp; Kas
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Monitoring arus kas bank dan pencocokan rekening koran (Bank Reconciliation) ke General Ledger.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="button" @click="modalRekon = true"
                        class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Input Rekening Koran</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 px-4 py-3 rounded-xl text-xs sm:text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs sm:text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs sm:text-sm font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- 2. KPI Cards Mutasi Bank (Report Info) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Mutasi Masuk</span>
                <div class="text-xl font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
                <p class="text-xs text-slate-400 mt-0.5">Penerimaan transfer & kasir</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Mutasi Keluar</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</div>
                <p class="text-xs text-slate-400 mt-0.5">Pengeluaran & beban bank</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Net Arus Kas Bank</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($saldoMutasi, 0, ',', '.') }}</div>
                <p class="text-xs text-slate-400 mt-0.5">Surplus / defisit kas bank</p>
            </div>
        </div>

        <!-- 3. Toolbar Pencarian & Filter Mandiri (Format Standar Divisi Iklan) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.beban.mutasi-bank') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Mutasi</label>
                    <input type="text" 
                           name="q" 
                           value="{{ $q ?? '' }}" 
                           placeholder="Ketik no bukti, keterangan, penerima..." 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pos Rekening / Kas</label>
                    <select name="rekening" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all" {{ ($rekening ?? 'all') === 'all' ? 'selected' : '' }}>Semua Rekening Bank &amp; Kas</option>
                        <option value="bca" {{ ($rekening ?? '') === 'bca' ? 'selected' : '' }}>BCA Giro (054-067-6270)</option>
                        <option value="bjb" {{ ($rekening ?? '') === 'bjb' ? 'selected' : '' }}>BJB Giro (001-002-3456)</option>
                        <option value="kasir" {{ ($rekening ?? '') === 'kasir' ? 'selected' : '' }}>Kas Operasional / Kasir</option>
                    </select>
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bulan Mutasi</label>
                    <input type="month" 
                           name="bulan" 
                           value="{{ $bulanPilihan }}" 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'rekening']) || request('bulan') != date('Y-m'))
                        <a href="{{ route('accounting.beban.mutasi-bank') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 4. Pure Tabel Data Mutasi Masuk & Keluar -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Mutasi Masuk -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5">Tanggal</th>
                                <th class="py-3 px-3.5">No. Bukti</th>
                                <th class="py-3 px-3.5">Keterangan</th>
                                <th class="py-3 px-3.5 text-right">Nominal Masuk</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 text-sm whitespace-nowrap">
                            @forelse($mutasiMasuk as $in)
                                @php
                                    $tglMasuk = $in->tgl_transaksi ?? $in->tanggal_terima ?? $in->created_at;
                                    $noBuktiMasuk = $in->no_kuitansi ?: ($in->no_bkm ?: ('BKM-' . $in->id));
                                    $nominalMasuk = (float) ($in->jumlah_dibayar ?? $in->jumlah_diterima ?? 0);
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-3.5 text-slate-600 font-mono text-xs">{{ $tglMasuk ? \Carbon\Carbon::parse($tglMasuk)->format('d/m/Y') : '-' }}</td>
                                    <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $noBuktiMasuk }}</td>
                                    <td class="py-3 px-3.5 max-w-[180px] truncate">{{ $in->terima_dari ?: ($in->nama_pembayar ?: ($in->keterangan ?: '-')) }}</td>
                                    <td class="py-3 px-3.5 text-right font-mono font-bold text-kp-blue-700">
                                        Rp {{ number_format($nominalMasuk, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400 text-sm">Tidak ada mutasi penerimaan transfer.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mutasi Keluar -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5">Tanggal</th>
                                <th class="py-3 px-3.5">No. Bukti</th>
                                <th class="py-3 px-3.5">Keterangan</th>
                                <th class="py-3 px-3.5 text-right">Nominal Keluar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 text-sm whitespace-nowrap">
                            @forelse($mutasiKeluar as $out)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-3.5 text-slate-600">{{ \Carbon\Carbon::parse($out->tanggal_keluar)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $out->no_bkk ?: ('BKK-' . $out->id) }}</td>
                                    <td class="py-3 px-3.5 max-w-[180px] truncate">{{ $out->keterangan }}</td>
                                    <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">
                                        Rp {{ number_format($out->nominal_keluar ?: $out->jumlah_keluar ?: 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400 text-sm">Tidak ada mutasi pengeluaran bank.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 5. Seksi Rekonsiliasi Rekening Koran Bank (Bank Reconciliation) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-sm font-bold text-slate-900">Rekonsiliasi Rekening Koran ({{ $bulanPilihan }})</h2>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-slate-100 text-slate-700 border border-slate-200 font-mono">
                            Saldo Buku BCA: Rp {{ number_format($saldoBukuBca ?? 0, 0, ',', '.') }}
                        </span>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-slate-100 text-slate-700 border border-slate-200 font-mono">
                            Saldo Buku BJB: Rp {{ number_format($saldoBukuBjb ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Total Baris: <span class="font-mono font-bold">{{ $rekonStats['total'] ?? 0 }}</span> &bull;
                        Cocok (Matched): <span class="font-mono font-bold text-kp-blue-700">{{ $rekonStats['matched'] ?? 0 }}</span> &bull;
                        Jurnal Penyesuaian: <span class="font-mono font-bold text-slate-800">{{ $rekonStats['adjusted'] ?? 0 }}</span> &bull;
                        Belum Cocok: <span class="font-mono font-bold text-rose-700">{{ $rekonStats['unmatched'] ?? 0 }}</span>
                    </p>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <form method="POST" action="{{ route('accounting.beban.rekon.auto-match') }}">
                        @csrf
                        <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                        <button type="submit" class="h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-xl text-xs font-bold transition cursor-pointer shadow-xs">
                            Auto-Match Dengan Kas Masuk/Keluar
                        </button>
                    </form>
                    <button type="button" @click="modalRekon = true" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white rounded-xl text-xs font-bold transition cursor-pointer shadow-xs">
                        + Tambah Baris Koran
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3.5">Tanggal &amp; Bank</th>
                            <th class="py-3 px-3.5">Uraian Rekening Koran</th>
                            <th class="py-3 px-3.5">Referensi Bank</th>
                            <th class="py-3 px-3.5 text-center">Tipe</th>
                            <th class="py-3 px-3.5 text-right">Nominal (Rp)</th>
                            <th class="py-3 px-3.5 text-center">Status Rekon</th>
                            <th class="py-3 px-3.5 text-center">Tindak Lanjut GL</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($rekonList ?? [] as $rek)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5">
                                    <span class="font-mono text-xs text-slate-900 font-bold block">{{ \Carbon\Carbon::parse($rek->tanggal_transaksi)->format('d/m/Y') }}</span>
                                    <span class="text-xs font-semibold text-kp-blue-700">{{ $rek->rekening_bank }} ({{ $rek->kode_perkiraan_bank }})</span>
                                </td>
                                <td class="py-3 px-3.5 max-w-xs truncate font-medium text-slate-900">{{ $rek->uraian_bank }}</td>
                                <td class="py-3 px-3.5 font-mono text-xs text-slate-500">{{ $rek->referensi_bank ?: '-' }}</td>
                                <td class="py-3 px-3.5 text-center">
                                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $rek->tipe_mutasi === 'kredit_masuk' ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                        {{ $rek->tipe_mutasi === 'kredit_masuk' ? 'Kredit (Masuk)' : 'Debet (Keluar)' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($rek->nominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @if($rek->status_match === 'matched')
                                        <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">Matched ({{ $rek->matched_model }} #{{ $rek->matched_id }})</span>
                                    @elseif($rek->status_match === 'adjusted')
                                        <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-300">Adjusted ({{ $rek->jurnal?->no_jurnal ?? 'GL' }})</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">Unmatched</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @if($rek->status_match === 'unmatched')
                                        <div class="inline-flex items-center gap-1.5">
                                            <form method="POST" action="{{ route('accounting.beban.rekon.adjust', $rek->id) }}">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 bg-kp-blue-600 hover:bg-kp-blue-700 text-white rounded-lg text-xs font-bold transition cursor-pointer">
                                                    Jurnal Penyesuaian
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('accounting.beban.rekon.destroy', $rek->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-semibold transition cursor-pointer">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">Terselesaikan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-sm text-slate-500">
                                    Belum ada data mutasi rekening koran untuk periode {{ $bulanPilihan }}. Klik <span class="font-semibold text-kp-blue-700">Input Rekening Koran</span> untuk mencocokkan saldo bank.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Input Mutasi Rekening Koran -->
        <div x-show="modalRekon" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="modalRekon = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg bg-white rounded-xl shadow-xl p-6 border border-slate-200">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900">Input Mutasi Rekening Koran Bank</h3>
                        <button type="button" @click="modalRekon = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold cursor-pointer">&times;</button>
                    </div>
                    <form method="POST" action="{{ route('accounting.beban.rekon.store') }}" class="space-y-4 mt-4">
                        @csrf
                        <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Rekening Bank</label>
                                <select name="rekening_bank" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                    <option value="BCA">Bank BCA Giro</option>
                                    <option value="BJB">Bank BJB Giro</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Transaksi Bank</label>
                                <input type="date" name="tanggal_transaksi" required value="{{ date('Y-m-d') }}"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tipe Mutasi Koran</label>
                                <select name="tipe_mutasi" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600">
                                    <option value="kredit_masuk">Kredit (Uang Masuk / Jasa Giro)</option>
                                    <option value="debet_keluar">Debet (Uang Keluar / Biaya Admin)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nominal (Rp)</label>
                                <input type="number" step="1" min="1" name="nominal" required placeholder="25000"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Uraian Rekening Koran</label>
                            <input type="text" name="uraian_bank" required placeholder="Contoh: Biaya Administrasi Bank Bulanan / Setoran Klien"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">No. Referensi Koran</label>
                            <input type="text" name="referensi_bank" placeholder="Opsional"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600" />
                        </div>
                        <div class="pt-2 flex gap-2">
                            <button type="submit" class="flex-1 h-10 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition cursor-pointer">
                                Simpan Baris Rekening Koran
                            </button>
                            <button type="button" @click="modalRekon = false" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl transition cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>