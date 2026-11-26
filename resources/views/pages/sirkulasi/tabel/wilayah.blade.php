<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Standar Divisi Iklan, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Wilayah Pemasaran (01 s.d. 08)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Klasifikasi 8 zona pemasaran sirkulasi dan pemetaan rute distribusi Kabar Priangan.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('sirkulasi.cetak.pemasaran', ['tab' => 'rekap-wilayah']) }}"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Laporan Wilayah</span>
                </a>
            </div>
        </div>

        <!-- Ringkasan KPI Wilayah (60-30-10) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Zona Distribusi</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ $wilayahList->count() }} <span class="text-xs font-sans font-normal text-slate-500">Wilayah</span>
                </p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Titik Agen</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    {{ number_format($totalAgen, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-500">Titik</span>
                </p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Alokasi Oplah Rutin</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalAlokasi, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-500">Eks / Hari</span>
                </p>
            </div>
        </div>

        <!-- Tabel Wilayah Pemasaran (Standar Divisi Iklan, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-16">Kode</th>
                            <th class="py-3 px-3.5">Nama Wilayah</th>
                            <th class="py-3 px-3.5">Cakupan Distribusi</th>
                            <th class="py-3 px-3.5 text-center">Tipe Alokasi</th>
                            <th class="py-3 px-3.5 text-center">Jumlah Agen</th>
                            <th class="py-3 px-3.5 text-right">Alokasi Default</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($wilayahList as $w)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center font-mono font-bold text-kp-blue-700 text-sm whitespace-nowrap">
                                    {{ $w->kode }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $w->nama }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 text-sm leading-relaxed min-w-[240px]">
                                    {{ $w->keterangan }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                    @if($w->tipe === 'Komersil')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Komersil
                                        </span>
                                    @elseif($w->tipe === 'Non-Komersil (Gratis)')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            Non-Komersil
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                            Insidentil
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap font-mono font-semibold text-slate-900 tabular-nums">
                                    {{ $w->total_agen }} titik
                                </td>
                                <td class="py-3.5 px-3.5 text-right whitespace-nowrap font-mono font-semibold text-slate-900 tabular-nums">
                                    {{ number_format($w->alokasi_default, 0, ',', '.') }} eks
                                </td>
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap"
                                    x-data="{ open: false }"
                                    :class="{ 'relative z-50': open }">
                                    <div class="relative inline-block text-left" @click.outside="open = false">
                                        <button type="button"
                                                @click="open = !open"
                                                class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none cursor-pointer"
                                                :class="{ 'text-slate-900 bg-slate-100': open }"
                                                title="Pilihan Aksi">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="5" r="1.5" />
                                                <circle cx="12" cy="12" r="1.5" />
                                                <circle cx="12" cy="19" r="1.5" />
                                            </svg>
                                        </button>

                                        <div x-cloak
                                             x-show="open"
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="absolute right-0 {{ $loop->remaining < 2 && $loop->count > 2 ? 'bottom-full mb-1.5 origin-bottom-right' : 'top-full mt-1.5 origin-top-right' }} w-48 bg-white rounded-xl shadow-xl border border-slate-200 p-1.5 z-50 focus:outline-none text-left">

                                            <a href="{{ route('sirkulasi.tabel.agen', ['wilayah' => $w->kode]) }}"
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Lihat Daftar Agen</span>
                                            </a>

                                            <a href="{{ route('sirkulasi.cetak.alamat-agen', ['wilayah' => $w->kode]) }}"
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                <span>Cetak Direktori</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-semibold text-slate-900 text-sm whitespace-nowrap">
                        <tr>
                            <td colspan="4" class="py-3.5 px-3.5 uppercase text-xs font-bold text-slate-700">
                                Total {{ $wilayahList->count() }} Wilayah Pemasaran
                            </td>
                            <td class="py-3.5 px-3.5 text-center font-mono tabular-nums">
                                {{ number_format($totalAgen, 0, ',', '.') }} titik
                            </td>
                            <td class="py-3.5 px-3.5 text-right font-mono font-bold text-kp-blue-700 tabular-nums">
                                {{ number_format($totalAlokasi, 0, ',', '.') }} eks
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
