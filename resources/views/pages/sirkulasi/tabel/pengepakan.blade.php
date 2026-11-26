<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Standar Divisi Iklan, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Standar Pengepakan
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Ketentuan packing bendel 50 eksemplar, sortir ekspedisi subuh, dan proteksi distribusi.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('sirkulasi.cetak.nota-distribusi') }}"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs hover:shadow transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Nota &amp; Surat Jalan</span>
                </a>
            </div>
        </div>

        <!-- Kartu SOP Pengepakan Ekspedisi (60-30-10) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Standar Satuan Bendel</span>
                    <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">Ikat Utama</span>
                </div>
                <p class="text-2xl font-bold text-slate-900 mt-2 font-mono tabular-nums">
                    50 <span class="text-xs font-sans font-normal text-slate-500">Eksemplar / Ikat</span>
                </p>
                <span class="text-xs text-slate-500 mt-1 block">Diikat tali rafia silang dan pelindung cuaca.</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Batas Waktu Muat</span>
                    <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">Deadline Subuh</span>
                </div>
                <p class="text-2xl font-bold text-kp-blue-700 mt-2 font-mono tabular-nums">
                    04.00 <span class="text-xs font-sans font-normal text-slate-500">WIB Subuh</span>
                </p>
                <span class="text-xs text-slate-500 mt-1 block">Seluruh bendel dimuat sebelum armada berangkat.</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pemeriksaan Checker</span>
                    <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-md bg-slate-100 text-slate-700 border border-slate-200">QC Fisik</span>
                </div>
                <p class="text-2xl font-bold text-slate-900 mt-2 font-mono tabular-nums">
                    100% <span class="text-xs font-sans font-normal text-slate-500">Surat Jalan</span>
                </p>
                <span class="text-xs text-slate-500 mt-1 block">Label agen &amp; drop point tertempel jelas.</span>
            </div>
        </div>

        <!-- Tabel Standar Kemasan Pengepakan (Standar Divisi Iklan, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-16">Kode</th>
                            <th class="py-3 px-3.5">Unit Kemasan</th>
                            <th class="py-3 px-3.5">Kategori</th>
                            <th class="py-3 px-3.5 text-center">Standar Bendel</th>
                            <th class="py-3 px-3.5">Spesifikasi Ikatan</th>
                            <th class="py-3 px-3.5">Keterangan</th>
                            <th class="py-3 px-3.5 text-center">Jadwal Sortir</th>
                            <th class="py-3 px-3.5">Penanggung Jawab</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($pengepakanList as $p)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center font-mono font-bold text-kp-blue-700 text-sm whitespace-nowrap">
                                    {{ $p->kode }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $p->unit }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal whitespace-nowrap">
                                    {{ $p->kategori ?? 'Unit Operasional' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center font-mono font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $p->standar_bendel }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-700 text-sm min-w-[180px]">
                                    {{ $p->toleransi_ikat }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 text-sm leading-relaxed min-w-[200px]">
                                    {{ $p->keterangan ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center font-mono text-slate-700 whitespace-nowrap">
                                    {{ $p->waktu_muat }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-900 font-semibold whitespace-nowrap">
                                    {{ $p->pic_checker }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        {{ $p->status }}
                                    </span>
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

                                            <a href="{{ route('sirkulasi.cetak.nota-distribusi') }}"
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                <span>Surat Jalan Armada</span>
                                            </a>

                                            <a href="{{ route('sirkulasi.jatah.hitung') }}"
                                               @click="open = false"
                                               class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-kp-blue-50 hover:text-kp-blue-700 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                                <span>Kalkulasi Jatah</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
