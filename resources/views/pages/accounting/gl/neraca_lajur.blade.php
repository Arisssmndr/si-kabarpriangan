<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
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

            <!-- Filter Periode & Cetak -->
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('accounting.gl.neraca-lajur') }}" class="flex items-center gap-2">
                    <input type="month" name="periode" value="{{ $periode }}" class="h-9 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 transition" />
                    <button type="submit" class="h-9 px-3.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                        Filter
                    </button>
                </form>
                <a href="{{ route('accounting.gl.neraca-lajur.cetak', ['periode' => $periode]) }}" target="_blank" 
                   class="inline-flex items-center gap-2 h-9 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Lembar Kerja</span>
                </a>
            </div>
        </div>

        <!-- Tabel Kertas Kerja Neraca Lajur -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Kertas Kerja Akuntansi Periode {{ $periode }}</h2>
                    <p class="text-xs text-slate-500">Neraca Saldo, Laba Rugi, dan Neraca Akhir.</p>
                </div>
            </div>

            <div class="overflow-x-auto min-h-[350px]">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] uppercase text-[10px] font-bold whitespace-nowrap">
                        <tr>
                            <th rowspan="2" class="py-2.5 px-3 text-center border-r border-white/20">Kode</th>
                            <th rowspan="2" class="py-2.5 px-3 border-r border-white/20">Nama Akun Perkiraan</th>
                            <th colspan="2" class="py-1.5 px-3 text-center border-r border-white/20">Neraca Saldo</th>
                            <th colspan="2" class="py-1.5 px-3 text-center border-r border-white/20">Laba Rugi</th>
                            <th colspan="2" class="py-1.5 px-3 text-center">Neraca Akhir</th>
                        </tr>
                        <tr class="border-t border-white/20 bg-[#085a88]">
                            <th class="py-1.5 px-3 text-right border-r border-white/20 w-24">Debet</th>
                            <th class="py-1.5 px-3 text-right border-r border-white/20 w-24">Kredit</th>
                            <th class="py-1.5 px-3 text-right border-r border-white/20 w-24">Beban</th>
                            <th class="py-1.5 px-3 text-right border-r border-white/20 w-24">Pendapatan</th>
                            <th class="py-1.5 px-3 text-right border-r border-white/20 w-24">Aktiva</th>
                            <th class="py-1.5 px-3 text-right w-24">Pasiva</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @foreach($neracaLajur as $row)
                            @php
                                $isLabaRugi = str_starts_with($row['kode'], '4.') || str_starts_with($row['kode'], '5.') || str_starts_with($row['kode'], '6.');
                            @endphp
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-2 px-3 text-center font-bold text-slate-900 font-mono border-r border-slate-100">{{ $row['kode'] }}</td>
                                <td class="py-2 px-3 border-r border-slate-100">{{ $row['nama'] }}</td>
                                <td class="py-2 px-3 text-right font-mono border-r border-slate-100">{{ $row['debet'] > 0 ? number_format($row['debet'], 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-3 text-right font-mono border-r border-slate-100">{{ $row['kredit'] > 0 ? number_format($row['kredit'], 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-3 text-right font-mono border-r border-slate-100">{{ $isLabaRugi && $row['debet'] > 0 ? number_format($row['debet'], 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-3 text-right font-mono border-r border-slate-100">{{ $isLabaRugi && $row['kredit'] > 0 ? number_format($row['kredit'], 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-3 text-right font-mono border-r border-slate-100">{{ !$isLabaRugi && $row['debet'] > 0 ? number_format($row['debet'], 0, ',', '.') : '-' }}</td>
                                <td class="py-2 px-3 text-right font-mono">{{ !$isLabaRugi && $row['kredit'] > 0 ? number_format($row['kredit'], 0, ',', '.') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 border-t-2 border-slate-300 font-bold">
                            <td colspan="2" class="py-2.5 px-3 text-right uppercase">TOTAL SALDO</td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">{{ number_format($totalDebet, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">{{ number_format($totalKredit, 0, ',', '.') }}</td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>

