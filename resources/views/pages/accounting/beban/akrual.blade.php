<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
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

            <!-- Filter Bulan -->
            <form method="GET" action="{{ route('accounting.beban.akrual') }}" class="flex items-center gap-2">
                <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="h-9 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 transition" />
                <button type="submit" class="h-9 px-3.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                    Filter
                </button>
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

                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full h-10 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                            Posting Penyesuaian
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Daftar Jurnal Akrual Periode Ini -->
            <div class="lg:col-span-8 bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Daftar Jurnal Penyesuaian ({{ $bulanPilihan }})</h2>
                        <p class="text-xs text-slate-500">Mutasi penyesuaian akhir periode yang telah dibukukan.</p>
                    </div>
                </div>

                <div class="overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] font-bold text-xs uppercase tracking-wider whitespace-nowrap">
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

