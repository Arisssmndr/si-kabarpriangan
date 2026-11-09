<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Jenis Agen (00, 11, 99)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Klasifikasi jenis agen koran, harga satuan, dan proteksi kartu piutang.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('sirkulasi.tabel.agen') }}"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <span>Lihat Data Agen</span>
                </a>
            </div>
        </div>

        <!-- 3 KPI Klasifikasi Jenis Agen -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kode 11 &bull; Agen Besar</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">11</p>
                <span class="text-xs text-slate-400 mt-1 block">Pengiriman langsung armada subuh</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kode 99 &bull; Sub-Agen</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">99</p>
                <span class="text-xs text-slate-400 mt-1 block">Jaringan kios eceran &amp; loper wilayah</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kode 00 &bull; Agen Non-Aktif</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">00</p>
                <span class="text-xs text-slate-400 mt-1 block">Jatah berhenti, saldo piutang tetap tercatat</span>
            </div>
        </div>

        <!-- Tabel 3 Jenis Agen -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Klasifikasi Jenis &amp; Ketentuan Agen</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Harga satuan eksemplar, distribusi, dan aturan proteksi piutang</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono w-fit">
                    {{ $jenisList->count() }} Klasifikasi
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5 w-20">Kode</th>
                            <th class="py-3 px-3.5">Klasifikasi Jenis Agen</th>
                            <th class="py-3 px-3.5 text-right w-36">Harga / Eks</th>
                            <th class="py-3 px-3.5">Ketentuan Pengiriman</th>
                            <th class="py-3 px-3.5 text-center">Kebijakan</th>
                            <th class="py-3 px-3.5 text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @foreach($jenisList as $idx => $j)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal w-12">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    {{ $j->kode }}
                                </td>
                                <td class="py-3.5 px-3.5">
                                    <div class="font-semibold text-slate-900">{{ $j->nama }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $j->sistem_distribusi }}</div>
                                </td>
                                <td class="py-3.5 px-3.5 text-right whitespace-nowrap">
                                    @if($j->harga_satuan > 0)
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($j->harga_satuan, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <div class="text-right font-mono text-sm text-slate-400 font-normal tabular-nums">
                                            Rp 0
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal whitespace-normal min-w-[260px]">
                                    {{ $j->ketentuan }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($j->kode === '11')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            {{ $j->status_aturan }}
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $j->status_aturan }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <a href="{{ route('sirkulasi.tabel.agen', ['kategori' => $j->kode]) }}"
                                       class="text-xs font-semibold text-kp-blue-700 hover:underline">
                                        Lihat Agen {{ $j->kode }} &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
