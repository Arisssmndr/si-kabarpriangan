<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (60-30-10 Palette) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Tutup Data Bulanan (Closing Sirkulasi)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Penguncian data jatah dan retur akhir bulan untuk integrasi pembukuan akuntansi.
                    </p>
                </div>
            </div>

            @if(!$periodeSaatIni->is_locked)
                <div class="flex items-center gap-2.5 shrink-0">
                    <form method="POST" action="{{ route('sirkulasi.laporan.closing.lock') }}" onsubmit="return confirm('Kunci data sirkulasi periode ini untuk integrasi ke akuntansi?');">
                        @csrf
                        <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                        <button type="submit" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                            <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>Tutup Data &amp; Kunci Periode</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Toolbar Pilih Periode -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <form method="GET" action="{{ route('sirkulasi.laporan.closing') }}" class="flex flex-col sm:flex-row sm:items-end gap-3 w-full sm:w-auto">
                <div class="w-full sm:w-64">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Periode Operasional</label>
                    <input type="month"
                           name="bulan"
                           value="{{ $bulanPilihan }}"
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>
                <button type="submit" class="h-11 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    Pilih Periode
                </button>
            </form>

            <div class="flex items-center gap-2.5">
                <span class="text-xs text-slate-500">Periode <strong class="text-slate-900">{{ $periodeSaatIni->periode }}</strong>:</span>
                @if($periodeSaatIni->is_locked)
                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                        {{ $periodeSaatIni->status_label }}
                    </span>
                @else
                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $periodeSaatIni->status_label }}
                    </span>
                @endif
            </div>
        </div>

        <!-- 4 KPI Ringkasan Periode Aktif -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Omset Bersih Sirkulasi</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($periodeSaatIni->omset_bersih, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Periode {{ $periodeSaatIni->periode }}</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Oplah Cetak</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($periodeSaatIni->total_oplah_cetak, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Akumulasi produksi cetak</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Koran Terjual</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($periodeSaatIni->total_terjual, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Penjualan bersih efektif</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Retur Fisik (BAPR)</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($periodeSaatIni->total_retur_eks, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block font-mono">Rasio retur: {{ $periodeSaatIni->rasio_retur }}%</span>
            </div>
        </div>

        <!-- Tabel Riwayat Closing -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Riwayat Closing Periode Sebelumnya</h2>
                    <p class="text-xs text-slate-500">Data periode terkunci tidak dapat diubah kembali oleh staf operasional</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ count($riwayatClosing) }} Periode
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">Periode</th>
                            <th class="py-3 px-3.5 text-right">Oplah Cetak</th>
                            <th class="py-3 px-3.5 text-right">Retur Fisik</th>
                            <th class="py-3 px-3.5 text-right">Omset Bersih</th>
                            <th class="py-3 px-3.5 text-center">Status Integrasi</th>
                            <th class="py-3 px-3.5">Waktu Closing</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                        @forelse($riwayatClosing as $idx => $rc)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $rc->periode }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">
                                    {{ number_format($rc->total_oplah_cetak, 0, ',', '.') }} eks
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-600 tabular-nums">
                                    {{ number_format($rc->total_retur_eks, 0, ',', '.') }} eks ({{ $rc->rasio_retur }}%)
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">
                                    Rp {{ number_format($rc->total_omset_bersih, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        {{ $rc->status_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-xs text-slate-600">
                                    {{ $rc->closed_at }} &bull; <span class="font-sans text-slate-700">{{ $rc->closed_by }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500 font-normal">
                                    Belum ada riwayat penutupan periode sirkulasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
