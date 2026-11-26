<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Buku Besar Per Akun (General Ledger)
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Kartu Perkiraan
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Mutasi debet, kredit, dan saldo berjalan per pos akun buku besar.
                    </p>
                </div>
            </div>

            <!-- Pilih Akun & Cetak -->
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('accounting.gl.buku-besar.cetak', ['kode_perkiraan' => $akunPilihan, 'dari' => $dari, 'sampai' => $sampai]) }}" target="_blank"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Dokumen</span>
                </a>
            </div>
        </div>

        <!-- 2. KPI / Metrics Ribbon (Palet 60-30-10) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Akun Terpilih</span>
                <span class="text-lg font-bold text-slate-900 tracking-tight truncate block">{{ $akun ? $akun->nama_perkiraan : 'Kas Operasional' }}</span>
                <span class="text-xs text-slate-400 font-mono block mt-0.5">{{ $akunPilihan }} (Normal: {{ strtoupper($akun?->saldo_normal ?? 'Debet') }})</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Total Mutasi Debet</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($totalDebet, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">Arus debet periode ini</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Total Mutasi Kredit</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($totalKredit, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">Arus kredit periode ini</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Saldo Akhir Berjalan</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($saldoAkhir ?? 0, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">Akumulasi saldo per tanggal</span>
            </div>
        </div>

        <!-- 3. Toolbar Pencarian & Filter Mandiri (12-Col Grid) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('accounting.gl.buku-besar') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Akun Perkiraan</label>
                    <select name="kode_perkiraan" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        @foreach($daftarAkun as $item)
                            <option value="{{ $item->kode_perkiraan }}" {{ $akunPilihan === $item->kode_perkiraan ? 'selected' : '' }}>
                                {{ $item->kode_perkiraan }} - {{ $item->nama_perkiraan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Dari Tanggal</label>
                    <input type="date" name="dari" value="{{ $dari ?? '' }}" 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="sampai" value="{{ $sampai ?? '' }}" 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
                <div class="sm:col-span-1 flex items-center gap-2">
                    <button type="submit" class="w-full h-11 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer" title="Terapkan Filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                    <a href="{{ route('accounting.gl.buku-besar') }}" class="h-11 px-3 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center" title="Reset Filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.033 8.033 0 01-15.357-2m15.357 2H15"/></svg>
                    </a>
                </div>
            </form>
        </div>

        <!-- Pure Tabel Mutasi Kartu Perkiraan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 uppercase text-xs tracking-wider font-semibold whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">No. Jurnal</th>
                            <th class="py-3 px-3.5">Uraian Transaksi</th>
                            <th class="py-3 px-3.5 text-right w-36">Debet</th>
                            <th class="py-3 px-3.5 text-right w-36">Kredit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($mutasiBuku as $key => $m)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-xs text-slate-400">{{ $key + 1 }}</td>
                                <td class="py-3 px-3.5">{{ $m->jurnal?->tanggal ? \Carbon\Carbon::parse($m->jurnal->tanggal)->format('d/m/Y') : '-' }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $m->jurnal?->no_jurnal ?? '-' }}</td>
                                <td class="py-3 px-3.5 max-w-sm truncate">{{ $m->jurnal?->keterangan ?? '-' }}</td>
                                <td class="py-3 px-3.5 text-right font-mono {{ $m->debet > 0 ? 'font-bold text-slate-900' : 'text-slate-400' }}">
                                    {{ $m->debet > 0 ? 'Rp ' . number_format($m->debet, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono {{ $m->kredit > 0 ? 'font-bold text-slate-900' : 'text-slate-400' }}">
                                    {{ $m->kredit > 0 ? 'Rp ' . number_format($m->kredit, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                    Belum ada mutasi transaksi untuk akun ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 border-t-2 border-slate-300 font-bold">
                            <td colspan="4" class="py-3 px-3.5 text-right uppercase text-xs">Total Mutasi</td>
                            <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($totalDebet, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($totalKredit, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>

