<x-app-layout>
    <div class="space-y-5">

        <!-- 1. Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Neraca Lajur (10 Kolom)
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Balanced
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Kertas kerja penyusunan laporan keuangan dan verifikasi keseimbangan saldo.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('accounting.gl.neraca-lajur.cetak', ['periode' => $periode]) }}" target="_blank" 
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Lembar Kerja</span>
                </a>
            </div>
        </div>

        <!-- 2. KPI / Metrics Ribbon (Palet 60-30-10) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Total Akun Tampil</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format(count($neracaLajur), 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">Akun perkiraan aktif</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Total Saldo Debet</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($totalDebet, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">Keseimbangan debit neraca</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Total Saldo Kredit</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($totalKredit, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">Keseimbangan kredit neraca</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Status Keseimbangan</span>
                <div class="flex items-center gap-2 mt-1">
                    @if(abs($totalDebet - $totalKredit) < 0.01)
                        <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            BALANCED (SEIMBANG)
                        </span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-slate-100 text-slate-700 border border-slate-300">
                            SELISIH: Rp {{ number_format(abs($totalDebet - $totalKredit), 0, ',', '.') }}
                        </span>
                    @endif
                </div>
                <span class="text-xs text-slate-400 block mt-1">Verifikasi matematis GL</span>
            </div>
        </div>

        <!-- 3. Toolbar Pencarian & Filter Mandiri (12-Col Grid) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('accounting.gl.neraca-lajur') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pencarian Akun</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Kode atau nama akun perkiraan..." 
                               class="w-full h-11 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                    </div>
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kelompok Akun</label>
                    <select name="kepala" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="all" {{ ($kepala ?? 'all') === 'all' ? 'selected' : '' }}>Semua Kelompok (1-8)</option>
                        <option value="1" {{ ($kepala ?? '') == '1' ? 'selected' : '' }}>1 - Aktiva / Aset</option>
                        <option value="2" {{ ($kepala ?? '') == '2' ? 'selected' : '' }}>2 - Kewajiban / Hutang</option>
                        <option value="3" {{ ($kepala ?? '') == '3' ? 'selected' : '' }}>3 - Modal / Ekuitas</option>
                        <option value="4" {{ ($kepala ?? '') == '4' ? 'selected' : '' }}>4 - Pendapatan Usaha</option>
                        <option value="5" {{ ($kepala ?? '') == '5' ? 'selected' : '' }}>5 - HPP / Beban Pokok</option>
                        <option value="6" {{ ($kepala ?? '') == '6' ? 'selected' : '' }}>6 - Beban Operasional</option>
                        <option value="7" {{ ($kepala ?? '') == '7' ? 'selected' : '' }}>7 - Pendapatan Lain-lain</option>
                        <option value="8" {{ ($kepala ?? '') == '8' ? 'selected' : '' }}>8 - Beban Lain-lain</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Periode Laporan</label>
                    <input type="month" name="periode" value="{{ $periode }}" 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Filter Saldo</label>
                    <label class="flex items-center gap-2 h-11 px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-700 cursor-pointer hover:bg-slate-100 transition">
                        <input type="checkbox" name="hide_zero" value="1" {{ !empty($sembunyikanNol) ? 'checked' : '' }} class="rounded border-slate-300 text-kp-blue-600 focus:ring-kp-blue-500">
                        <span>Sembunyikan Nol</span>
                    </label>
                </div>
                <div class="sm:col-span-1 flex items-center gap-2">
                    <button type="submit" class="w-full h-11 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer" title="Cari Data">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                    <a href="{{ route('accounting.gl.neraca-lajur') }}" class="h-11 px-3 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center" title="Reset Filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.033 8.033 0 01-15.357-2m15.357 2H15"/></svg>
                    </a>
                </div>
            </form>
        </div>

        <!-- 3. Pure Tabel Kertas Kerja Neraca Lajur -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[350px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 uppercase text-xs font-semibold whitespace-nowrap">
                        <tr>
                            <th rowspan="2" class="py-3 px-3.5 text-center border-r border-slate-200">Kode</th>
                            <th rowspan="2" class="py-3 px-3.5 border-r border-slate-200">Nama Akun Perkiraan</th>
                            <th colspan="2" class="py-2 px-3 text-center border-r border-slate-200">Neraca Saldo</th>
                            <th colspan="2" class="py-2 px-3 text-center border-r border-slate-200">Laba Rugi</th>
                            <th colspan="2" class="py-2 px-3 text-center">Neraca Akhir</th>
                        </tr>
                        <tr class="border-t border-slate-200 bg-slate-100/70 text-slate-600 text-xs">
                            <th class="py-2 px-3 text-right border-r border-slate-200 w-28">Debet</th>
                            <th class="py-2 px-3 text-right border-r border-slate-200 w-28">Kredit</th>
                            <th class="py-2 px-3 text-right border-r border-slate-200 w-28">Beban</th>
                            <th class="py-2 px-3 text-right border-r border-slate-200 w-28">Pendapatan</th>
                            <th class="py-2 px-3 text-right border-r border-slate-200 w-28">Aktiva</th>
                            <th class="py-2 px-3 text-right w-28">Pasiva</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700 text-sm">
                        @forelse($neracaLajur as $row)
                            @php
                                $isLabaRugi = str_starts_with($row['kode'], '4.') || str_starts_with($row['kode'], '5.') || str_starts_with($row['kode'], '6.');
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center font-bold text-slate-900 font-mono border-r border-slate-100 text-xs">{{ $row['kode'] }}</td>
                                <td class="py-3 px-3.5 border-r border-slate-100">{{ $row['nama'] }}</td>
                                <td class="py-3 px-3.5 text-right font-mono border-r border-slate-100">{{ $row['debet'] > 0 ? number_format($row['debet'], 0, ',', '.') : '-' }}</td>
                                <td class="py-3 px-3.5 text-right font-mono border-r border-slate-100">{{ $row['kredit'] > 0 ? number_format($row['kredit'], 0, ',', '.') : '-' }}</td>
                                <td class="py-3 px-3.5 text-right font-mono border-r border-slate-100">{{ $isLabaRugi && $row['debet'] > 0 ? number_format($row['debet'], 0, ',', '.') : '-' }}</td>
                                <td class="py-3 px-3.5 text-right font-mono border-r border-slate-100">{{ $isLabaRugi && $row['kredit'] > 0 ? number_format($row['kredit'], 0, ',', '.') : '-' }}</td>
                                <td class="py-3 px-3.5 text-right font-mono border-r border-slate-100">{{ !$isLabaRugi && $row['debet'] > 0 ? number_format($row['debet'], 0, ',', '.') : '-' }}</td>
                                <td class="py-3 px-3.5 text-right font-mono">{{ !$isLabaRugi && $row['kredit'] > 0 ? number_format($row['kredit'], 0, ',', '.') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-sm font-semibold text-slate-600">Tidak ada data neraca lajur</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Tidak ditemukan akun yang sesuai dengan parameter filter pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 border-t-2 border-slate-300 font-bold text-sm">
                            <td colspan="2" class="py-3 px-3.5 text-right uppercase text-xs">TOTAL SALDO</td>
                            <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">{{ number_format($totalDebet, 0, ',', '.') }}</td>
                            <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">{{ number_format($totalKredit, 0, ',', '.') }}</td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>