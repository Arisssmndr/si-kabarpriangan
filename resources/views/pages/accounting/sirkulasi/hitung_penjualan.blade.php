<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Bersih (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Hitung Penjualan Sirkulasi
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Kartu Sirkulasi
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5 truncate">
                        Perhitungan omset koran bulanan dan sinkronisasi piutang agen ke GL.
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

        <!-- Metric Cards (Report Informasi Jatah & Omset Sirkulasi) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Jumlah Agen Aktif</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">{{ number_format($daftarAgen->count()) }} Agen</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Jatah Harian Total</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">{{ number_format($totalJatah) }} eks/hari</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Hari Kalender / Harga</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">{{ $estimasiHari ?? 30 }} Hari &bull; Rp {{ number_format($hargaKoran, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Omset Bulanan</span>
                <div class="text-lg font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($totalOmset, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- 3. Toolbar Pencarian & Filter Mandiri (Standar Divisi Iklan) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.sirkulasi.hitung-penjualan') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Agen</label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q', $q ?? '') }}" 
                           placeholder="Ketik kode agen atau nama agen..." 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Wilayah Distribusi</label>
                    <select name="wilayah" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Wilayah</option>
                        @foreach($daftarWilayah ?? [] as $w)
                            <option value="{{ $w }}" {{ request('wilayah', $wilayah ?? 'all') === $w ? 'selected' : '' }}>{{ $w }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bulan Penjualan</label>
                    <input type="month" 
                           name="bulan" 
                           value="{{ $bulanPilihan }}" 
                           class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'wilayah']) || request('bulan') != date('Y-m'))
                        <a href="{{ route('accounting.sirkulasi.hitung-penjualan') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tombol Aksi Posting GL Sirkulasi (Terpisah Mandiri) -->
        <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
            <div class="text-xs text-slate-500">
                Omset koran sirkulasi periode bulan <span class="font-mono font-bold text-slate-800">{{ \Carbon\Carbon::parse($bulanPilihan . '-01')->isoFormat('MMMM Y') }}</span> ({{ $estimasiHari ?? 30 }} Hari Kalender)
            </div>
            <div>
                @if(!$sudahDihitung)
                    <form method="POST" action="{{ route('accounting.sirkulasi.hitung-penjualan.proses') }}">
                        @csrf
                        <input type="hidden" name="bulan" value="{{ $bulanPilihan }}">
                        <button type="submit" 
                                onclick="return confirmSubmit(this, event, {
                                    title: 'Hitung &amp; Posting Omset Sirkulasi?',
                                    text: 'Proses hitung penjualan sirkulasi bulan {{ $bulanPilihan }}? Jurnal piutang agen akan dibentuk ke GL.',
                                    theme: 'blue',
                                    icon: 'journal',
                                    confirmText: 'Ya, Hitung &amp; Posting'
                                })"
                                class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            <span>Hitung &amp; Posting GL</span>
                        </button>
                    </form>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 border border-slate-200 text-slate-600 font-semibold text-xs rounded-xl">
                        <svg class="w-4 h-4 text-kp-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Penjualan Bulan Ini Sudah Diposting ke GL</span>
                    </span>
                @endif
            </div>
        </div>

        <!-- Pure Tabel Data Jatah Penjualan Agen -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">Kode Agen</th>
                            <th class="py-3 px-3.5">Nama Agen</th>
                            <th class="py-3 px-3.5">Wilayah</th>
                            <th class="py-3 px-3.5 text-center">Jatah/Hari</th>
                            <th class="py-3 px-3.5 text-center">Total Eks ({{ $estimasiHari ?? 30 }} Hari)</th>
                            <th class="py-3 px-3.5 text-right">Nilai Tagihan Bulanan</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700 text-sm">
                        @forelse($daftarAgen as $key => $agen)
                            @php
                                $hari = $estimasiHari ?? 30;
                                $rowCalc = $perAgenCalc[$agen->id] ?? null;
                                $totalEks = $rowCalc ? $rowCalc['eks_total'] : ($agen->jatah_default * $hari);
                                $omsetAgen = $rowCalc ? $rowCalc['nominal'] : ($totalEks * $hargaKoran);
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-slate-400 font-mono text-xs">{{ $key + 1 }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $agen->kode_agen }}</td>
                                <td class="py-3 px-3.5 text-slate-800 font-medium">{{ $agen->nama_agen }}</td>
                                <td class="py-3 px-3.5 text-slate-600">{{ $agen->wilayah ?: '-' }}</td>
                                <td class="py-3 px-3.5 text-center font-mono text-slate-700">{{ number_format($agen->jatah_default) }} eks</td>
                                <td class="py-3 px-3.5 text-center font-mono text-slate-700">{{ number_format($totalEks) }} eks</td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($omsetAgen, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <x-action-dropdown align="right">
                                        <a href="{{ route('accounting.sirkulasi.saldo-awal', ['q' => $agen->kode_agen]) }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Saldo Piutang Agen</span>
                                        </a>
                                        <a href="{{ route('accounting.cetak.sirkulasi', ['tab' => 'buku_piutang', 'tgl_mulai' => $bulanPilihan . '-01', 'tgl_selesai' => \Carbon\Carbon::parse($bulanPilihan . '-01')->endOfMonth()->toDateString()]) }}" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            <span>Buku Piutang Sirkulasi</span>
                                        </a>
                                    </x-action-dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400 text-sm">
                                    Tidak ada data agen yang sesuai filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
