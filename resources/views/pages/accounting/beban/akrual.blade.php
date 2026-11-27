<x-app-layout>
    <div class="space-y-5">

        <!-- 1. Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Jurnal Penyesuaian & Akrual
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Closing Bulanan
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Pencatatan penyesuaian beban akrual dan pendapatan dibayar dimuka akhir bulan.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <span class="px-3 py-1.5 text-xs font-semibold rounded-xl bg-slate-50 text-slate-700 border border-slate-200">
                    Periode: <strong class="text-slate-900 font-mono">{{ \Carbon\Carbon::parse($bulanPilihan . '-01')->translatedFormat('F Y') }}</strong>
                </span>
            </div>
        </div>

        <!-- 2. KPI / Metrics Ribbon (Palet 60-30-10) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Total Entri Akrual</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($stats['total_jurnal'] ?? 0, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">Jurnal penyesuaian periode</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Total Nilai Penyesuaian</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($stats['total_nominal'] ?? 0, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">Akumulasi nominal debet/kredit</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Bulan Pelaporan</span>
                <span class="text-lg font-bold text-slate-900 tracking-tight block truncate">{{ \Carbon\Carbon::parse($bulanPilihan . '-01')->translatedFormat('F Y') }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">Cut-off tutup buku</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Status Integrasi GL</span>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="px-2 py-0.5 text-xs font-bold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                        POSTED REALTIME
                    </span>
                </div>
                <span class="text-xs text-slate-400 block mt-1">Otomatis masuk buku besar</span>
            </div>
        </div>

        <!-- 3. Toolbar Pencarian & Filter Mandiri (12-Col Grid) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('accounting.beban.akrual') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4 items-end">
                <div class="sm:col-span-6">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pencarian Jurnal Penyesuaian</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari nomor jurnal, referensi, keterangan..." 
                               class="w-full h-11 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>
                </div>
                <div class="sm:col-span-4">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Bulan</label>
                    <input type="month" name="bulan" value="{{ $bulanPilihan }}" 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="w-full h-11 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer" title="Cari Data">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Cari</span>
                    </button>
                    <a href="{{ route('accounting.beban.akrual') }}" class="h-11 px-3.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center" title="Reset Filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.033 8.033 0 01-15.357-2m15.357 2H15"/></svg>
                    </a>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Form Input Jurnal Akrual -->
            <div class="lg:col-span-4 bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
                <h2 class="text-sm font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">
                    Input Jurnal Penyesuaian
                </h2>

                <form method="POST" action="{{ route('accounting.gl.memorial.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="referensi" value="AKRUAL-{{ $bulanPilihan }}">

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Penyesuaian</label>
                        <input type="date" 
                               name="tanggal" 
                               value="{{ date('Y-m-d') }}" 
                               required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Akun Debet</label>
                        <select name="akun_debet" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                            <option value="">-- Pilih Akun Debet --</option>
                            @foreach($daftarAkun as $akun)
                                <option value="{{ $akun->kode_perkiraan }}">
                                    {{ $akun->kode_perkiraan }} - {{ $akun->nama_perkiraan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Akun Kredit</label>
                        <select name="akun_kredit" required class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                            <option value="">-- Pilih Akun Kredit --</option>
                            @foreach($daftarAkun as $akun)
                                <option value="{{ $akun->kode_perkiraan }}">
                                    {{ $akun->kode_perkiraan }} - {{ $akun->nama_perkiraan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nominal Penyesuaian (Rp)</label>
                        <input type="number" 
                               name="nominal" 
                               placeholder="Contoh: 5000000" 
                               min="1" 
                               required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Keterangan Akrual</label>
                        <textarea name="keterangan" 
                                  rows="2" 
                                  required 
                                  placeholder="Akrual gaji belum dibayar / amortisasi asuransi..."
                                  class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition"></textarea>
                    </div>

                    <input type="hidden" name="sumber" value="akrual">

                    <div class="pt-2 grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <button type="submit" name="action_mode" value="draft"
                                class="h-10 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center cursor-pointer">
                            Simpan Draft
                        </button>
                        <button type="submit" name="action_mode" value="posted"
                                class="h-10 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                            Posting Penyesuaian
                        </button>
                    </div>
                </form>
            </div>

            <!-- Pure Tabel Daftar Jurnal Akrual Periode Ini -->
            <div class="lg:col-span-8 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 w-12 text-center">No</th>
                                <th class="py-3 px-3.5">No. Jurnal</th>
                                <th class="py-3 px-3.5">Tanggal</th>
                                <th class="py-3 px-3.5">Keterangan</th>
                                <th class="py-3 px-3.5 text-right">Nilai Debet/Kredit</th>
                                <th class="py-3 px-3.5 text-center w-24">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @forelse($jurnalAkrual as $key => $j)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-3.5 text-center text-xs text-slate-400">{{ $key + 1 }}</td>
                                    <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $j->no_jurnal }}</td>
                                    <td class="py-3 px-3.5">{{ \Carbon\Carbon::parse($j->tanggal)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-3.5 max-w-xs truncate">{{ $j->keterangan }}</td>
                                    <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($j->total_debet, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-3.5 text-center">
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Posted
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                        Belum ada jurnal penyesuaian pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>

