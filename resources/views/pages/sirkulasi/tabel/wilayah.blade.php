<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Wilayah Pemasaran (01 s.d. 08)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Daftar zona wilayah pemasaran sirkulasi dan alokasi distribusi harian.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('sirkulasi.tabel.agen') }}"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <span>Direktori Agen</span>
                </a>
            </div>
        </div>

        <!-- KPI Ringkasan Wilayah -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Zona Wilayah</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">{{ $wilayahList->count() }}</p>
                <span class="text-xs text-slate-400 mt-1 block">Kode wilayah 01 s.d. 08</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Titik Agen</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ $totalAgen }}</p>
                <span class="text-xs text-slate-400 mt-1 block">Titik agen seluruh wilayah</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Oplah Rutin Default</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">{{ number_format($totalAlokasi, 0, ',', '.') }}</p>
                <span class="text-xs text-slate-400 mt-1 block">Eksemplar alokasi harian</span>
            </div>
        </div>

        <!-- Tabel 8 Wilayah Pemasaran -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Kode &amp; Cakupan Wilayah Distribusi</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pemetaan pangkalan agen, rute armada, dan rekap laporan bulanan</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono w-fit">
                    {{ $wilayahList->count() }} Zona Wilayah
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5 w-20">Kode</th>
                            <th class="py-3 px-3.5">Nama Wilayah Pemasaran</th>
                            <th class="py-3 px-3.5">Cakupan Distribusi</th>
                            <th class="py-3 px-3.5 text-center">Tipe Alokasi</th>
                            <th class="py-3 px-3.5 text-right">Jumlah Agen</th>
                            <th class="py-3 px-3.5 text-right">Alokasi Default</th>
                            <th class="py-3 px-3.5 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @foreach($wilayahList as $idx => $w)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal w-12">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    {{ $w->kode }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    {{ $w->nama }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal whitespace-normal min-w-[260px]">
                                    {{ $w->keterangan }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($w->tipe === 'Komersil')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Komersil
                                        </span>
                                    @elseif($w->tipe === 'Non-Komersil (Gratis)')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            Non-Komersil
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            Insidentil
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">
                                    <span class="font-semibold text-slate-900">{{ $w->total_agen }}</span>
                                    <span class="text-xs font-sans font-normal text-slate-400">titik</span>
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">
                                    <span class="font-semibold text-slate-900">{{ number_format($w->alokasi_default, 0, ',', '.') }}</span>
                                    <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <a href="{{ route('sirkulasi.tabel.agen', ['wilayah' => $w->kode]) }}"
                                       class="text-xs font-semibold text-kp-blue-700 hover:underline">
                                        Lihat Agen &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                        <tr>
                            <td colspan="5" class="py-3 px-3.5 text-right font-sans text-xs font-bold uppercase tracking-wider text-slate-700">
                                Total 8 Wilayah Pemasaran
                            </td>
                            <td class="py-3 px-3.5 text-right tabular-nums">
                                {{ $totalAgen }} <span class="text-xs font-sans font-normal text-slate-500">titik</span>
                            </td>
                            <td class="py-3 px-3.5 text-right font-bold text-kp-blue-700 tabular-nums">
                                {{ number_format($totalAlokasi, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-500">eks</span>
                            </td>
                            <td class="py-3 px-3.5 text-center text-slate-400 font-normal">&mdash;</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
