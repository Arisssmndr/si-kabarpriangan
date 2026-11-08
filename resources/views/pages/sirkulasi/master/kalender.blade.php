<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (60-30-10 Palette) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Master Kalender Terbit Koran
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Jadwal terbit harian HU. Kabar Priangan periode {{ $bulanIni->translatedFormat('F Y') }}.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-3 py-2 rounded-xl font-mono">
                    {{ $kalender->count() }} Hari Terbit
                </span>
            </div>
        </div>

        <!-- Tabel Kalender Terbit -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Rincian Jadwal Edisi Cetak Bulanan</h2>
                    <p class="text-xs text-slate-500">Periode {{ $bulanIni->translatedFormat('F Y') }}</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    Total Target: {{ number_format($kalender->sum('target_oplah'), 0, ',', '.') }} eks
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">Tanggal Terbit</th>
                            <th class="py-3 px-3.5">Hari</th>
                            <th class="py-3 px-3.5">Jenis Edisi</th>
                            <th class="py-3 px-3.5 text-right">Target Oplah Cetak</th>
                            <th class="py-3 px-3.5 text-center">Status Terbit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                        @forelse($kalender as $idx => $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">{{ $item->tgl_format }}</td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $item->hari }}</td>
                                <td class="py-3.5 px-3.5">
                                    @if(str_contains($item->edisi, 'Minggu'))
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Edisi Minggu (Khusus)
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            Edisi Reguler
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">
                                    {{ number_format($item->target_oplah, 0, ',', '.') }} eks
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        {{ $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500 font-normal">
                                    Belum ada jadwal kalender terbit pada bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
