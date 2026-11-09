<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Standar Pengepakan Sirkulasi
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Standar ikatan bendel 50 eksemplar, jadwal sortir subuh, dan label agen.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('sirkulasi.cetak.nota-distribusi') }}"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <span>Cetak Nota &amp; Distribusi</span>
                </a>
            </div>
        </div>

        <!-- 3 KPI Standar Pengepakan -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Standar Satuan Bendel</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">50</p>
                <span class="text-xs text-slate-400 mt-1 block">Eksemplar per ikat rafia silang ganda</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jadwal Sortir &amp; Loading</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">01:30 &ndash; 02:30</p>
                <span class="text-xs text-slate-400 mt-1 block">WIB &bull; Pemuatan armada subuh</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Label Drop Point Agen</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">100%</p>
                <span class="text-xs text-slate-400 mt-1 block">Terlabel kode agen, bendel, &amp; koran lepas</span>
            </div>
        </div>

        <!-- Tabel Detail Unit Pengepakan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Standar Pengepakan Operasional</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Klasifikasi kemasan koran gudang distribusi fisik</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono w-fit">
                    {{ $pengepakanList->count() }} Standar Kemasan
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5 w-20">Kode</th>
                            <th class="py-3 px-3.5">Klasifikasi Kemasan &amp; Unit</th>
                            <th class="py-3 px-3.5 text-center">Standar Bendel</th>
                            <th class="py-3 px-3.5">Spesifikasi Pengepakan</th>
                            <th class="py-3 px-3.5 text-center">Jadwal Sortir</th>
                            <th class="py-3 px-3.5">Penanggung Jawab</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @foreach($pengepakanList as $idx => $p)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal w-12">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    {{ $p->kode }}
                                </td>
                                <td class="py-3.5 px-3.5">
                                    <div class="font-semibold text-slate-900">{{ $p->unit }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $p->kategori ?? 'Unit Operasional' }}</div>
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200 font-mono">
                                        {{ $p->standar_bendel }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-normal min-w-[260px]">
                                    <div class="font-medium text-slate-900 text-sm">{{ $p->toleransi_ikat }}</div>
                                    @if(isset($p->keterangan))
                                        <div class="text-xs text-slate-500 mt-0.5">{{ $p->keterangan }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center font-mono text-slate-600 text-xs">
                                    {{ $p->waktu_muat }}
                                </td>
                                <td class="py-3.5 px-3.5 font-normal text-slate-700">
                                    {{ $p->pic_checker }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($p->kode === '01')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            {{ $p->status }}
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $p->status }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
