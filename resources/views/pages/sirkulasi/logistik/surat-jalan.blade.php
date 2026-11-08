<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (60-30-10 Palette) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Surat Jalan &amp; Packing Armada
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Daftar pengepakan bendel koran (50 eks/bendel) dan manifes muatan ekspedisi subuh.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <button type="button"
                        onclick="window.print()"
                        class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Manifes Surat Jalan</span>
                </button>
            </div>
        </div>

        <!-- Toolbar Filter Rute & Tanggal -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.logistik.surat-jalan') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Pengiriman</label>
                    <input type="date"
                           name="tanggal"
                           value="{{ $tanggalDistribusi }}"
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>

                <div class="sm:col-span-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jalur Rute Armada</label>
                    <select name="rute" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        @foreach($ruteList as $r)
                            <option value="{{ $r->kode }}" {{ ($activeRute->kode ?? '') == $r->kode ? 'selected' : '' }}>
                                {{ $r->kode }} - {{ $r->nama_rute }} (Sopir: {{ $r->driver }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Tampilkan
                    </button>
                </div>
            </form>
        </div>

        <!-- 4 KPI Detail Armada & Muatan Aktif -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Muatan Rute</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    {{ number_format($totalEksemplarRute, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block font-mono">{{ $activeRute->kode ?? '-' }} &bull; {{ $activeRute->nama_rute ?? 'Rute Armada' }}</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kemasan Packing</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ $totalBendelRute }} <span class="text-xs font-sans font-normal text-slate-400">bndl + {{ $totalEceranRute }} ecer</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Standar 50 eksemplar / bendel</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sopir &amp; Kendaraan</span>
                <p class="text-base font-bold text-slate-900 mt-1.5 truncate">
                    {{ $activeRute->driver ?? '-' }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block truncate">{{ $activeRute->kendaraan ?? '-' }}</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jam Berangkat</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ $activeRute->jam_berangkat ?? '-' }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Jadwal keberangkatan subuh</span>
            </div>
        </div>

        <!-- Tabel Pengepakan Koran (Packing List) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Manifes Pengepakan &amp; Serah Terima Agen</h2>
                    <p class="text-xs text-slate-500">{{ $activeRute->nama_rute ?? 'Rute Armada' }} &bull; Tanggal {{ \Carbon\Carbon::parse($tanggalDistribusi)->format('d/m/Y') }}</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ count($packingItems) }} Drop Point
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">Kode Agen</th>
                            <th class="py-3 px-3.5">Drop Point / Nama Agen</th>
                            <th class="py-3 px-3.5">Wilayah</th>
                            <th class="py-3 px-3.5">Kontak</th>
                            <th class="py-3 px-3.5 text-center">Packing Bendel (50 eks)</th>
                            <th class="py-3 px-3.5 text-right">Sisa Lepas</th>
                            <th class="py-3 px-3.5 text-right">Total Muat</th>
                            <th class="py-3 px-3.5 text-center">Paraf Penerima</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                        @forelse($packingItems as $idx => $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">{{ $item->kode_agen }}</td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">{{ $item->nama_agen }}</td>
                                <td class="py-3.5 px-3.5 text-slate-700">{{ $item->wilayah }}</td>
                                <td class="py-3.5 px-3.5 font-mono text-slate-600 tabular-nums">{{ $item->telepon }}</td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">
                                        {{ $item->bendel_50 }} Bendel
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">
                                    {{ $item->sisa_ecer }} eks
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">
                                    {{ number_format($item->total_eks, 0, ',', '.') }} eks
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <div class="w-24 h-7 border border-dashed border-slate-300 rounded mx-auto flex items-center justify-center text-[10px] text-slate-400">
                                        Paraf
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-500 font-normal">
                                    Tidak ada alokasi jatah pada rute ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($packingItems) > 0)
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                            <tr>
                                <td colspan="5" class="py-3 px-3.5 text-right font-sans text-xs uppercase tracking-wider text-slate-600">Total Muatan Rute:</td>
                                <td class="py-3 px-3.5 text-center">{{ $totalBendelRute }} Bendel</td>
                                <td class="py-3 px-3.5 text-right tabular-nums">{{ $totalEceranRute }} eks</td>
                                <td class="py-3 px-3.5 text-right font-bold text-kp-blue-700 tabular-nums">{{ number_format($totalEksemplarRute, 0, ',', '.') }} eks</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
