<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-sky-600 uppercase tracking-wider">Tabel Master</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Standar Pengepakan (01 Bagian Sirkulasi)</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Ketentuan packing bendel 50 eksemplar, pengikatan tali rafia silang, sortir ekspedisi subuh, dan proteksi cuaca
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                    Total: <strong class="text-slate-900">{{ $pengepakanList->count() }} Standar Kemasan</strong>
                </span>
                <a href="{{ route('sirkulasi.cetak.nota-distribusi') }}" class="px-3.5 py-2 text-xs font-semibold rounded-lg bg-sky-600 text-white hover:bg-sky-700 shadow-2xs transition-colors inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <span>Lihat Surat Jalan Packing</span>
                </a>
            </div>
        </div>

        <!-- Kartu SOP Pengepakan Ekspedisi -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 lg:gap-6">
            <!-- Card 1: Standar Satuan Bendel -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs hover:border-sky-300 hover:shadow-sm transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 border border-sky-200/60 flex items-center justify-center shrink-0">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-slate-800">Standar Satuan Bendel</span>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-sky-50 text-sky-700 border border-sky-200/60">
                            Ikat Utama
                        </span>
                    </div>

                    <div class="my-4">
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-black text-slate-900 tracking-tight">50</span>
                            <span class="text-xs font-semibold text-slate-500">Eksemplar / Ikat</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3.5 border-t border-slate-100">
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Setiap bundel diikat tali rafia silang ganda kencang atau dilapisi plastik anti-air terutama saat musim penghujan guna melindungi fisik koran.
                    </p>
                </div>
            </div>

            <!-- Card 2: Jadwal Sortir & Loading Subuh -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs hover:border-amber-300 hover:shadow-sm transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 border border-amber-200/60 flex items-center justify-center shrink-0">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-slate-800">Jadwal Sortir & Loading</span>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-amber-50 text-amber-700 border border-amber-200/60">
                            Shift Subuh
                        </span>
                    </div>

                    <div class="my-4">
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-black text-slate-900 tracking-tight">01:30 &ndash; 02:30</span>
                            <span class="text-xs font-semibold text-amber-700">WIB Subuh</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3.5 border-t border-slate-100">
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Pengecekan fisik koran dari percetakan, penempelan slip label drop point agen, dan pemuatan ke kendaraan pengangkut ekspedisi distribusi.
                    </p>
                </div>
            </div>

            <!-- Card 3: Label Drop Point Agen -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs hover:border-emerald-300 hover:shadow-sm transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center shrink-0">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-slate-800">Label Drop Point Agen</span>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                            Wajib 100%
                        </span>
                    </div>

                    <div class="my-4">
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-black text-slate-900 tracking-tight">100%</span>
                            <span class="text-xs font-semibold text-emerald-700">Terlabel Jelas</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3.5 border-t border-slate-100">
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Label wajib memuat Kode Agen, Nama Kios/Toko, Jumlah Bendel 50an, dan Jumlah Koran Lepas (Sisa Eceran) untuk petugas pengantaran.
                    </p>
                </div>
            </div>
        </div>

        <!-- Tabel Detail Unit Pengepakan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Detail Master Standar Pengepakan Operasional</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Klasifikasi kemasan koran gudang sesuai SOP distribusi fisik pers</p>
                </div>
                <span class="text-[11px] text-slate-500 font-medium">DOS SIRK.EXE &bull; Unit 01 Bagian Sirkulasi</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-100/75 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4 w-16 text-center">Kode</th>
                            <th class="py-3 px-4">Klasifikasi Kemasan & Unit</th>
                            <th class="py-3 px-4 text-center">Standar Bendel</th>
                            <th class="py-3 px-4">Ketentuan Pengepakan & Spesifikasi</th>
                            <th class="py-3 px-4 text-center whitespace-nowrap">Jadwal Sortir</th>
                            <th class="py-3 px-4">Penanggung Jawab</th>
                            <th class="py-3 px-4 text-center w-36 whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($pengepakanList as $p)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-block px-2.5 py-1 text-xs font-mono font-bold rounded-md bg-sky-100 text-sky-900 border border-sky-200">
                                        {{ $p->kode }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 text-xs">{{ $p->unit }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $p->kategori ?? 'Unit Operasional' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-block px-2.5 py-1 rounded bg-slate-100 text-slate-800 font-bold text-xs border border-slate-200/80">
                                        {{ $p->standar_bendel }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 max-w-sm leading-relaxed">
                                    <div>{{ $p->toleransi_ikat }}</div>
                                    @if(isset($p->keterangan))
                                        <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1">
                                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                            <span>{{ $p->keterangan }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center text-slate-800 font-mono text-[11px] whitespace-nowrap">
                                    {{ $p->waktu_muat }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-700 font-semibold">
                                    {{ $p->pic_checker }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @php
                                        $badgeConfig = match($p->badge_color ?? 'emerald') {
                                            'emerald' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'],
                                            'sky'     => ['bg' => 'bg-sky-50 text-sky-700 border-sky-200', 'dot' => 'bg-sky-500'],
                                            'amber'   => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'dot' => 'bg-amber-500'],
                                            'purple'  => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200', 'dot' => 'bg-purple-500'],
                                            default   => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'dot' => 'bg-slate-400'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold border whitespace-nowrap shadow-2xs {{ $badgeConfig['bg'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $badgeConfig['dot'] }}"></span>
                                        <span>{{ $p->status }}</span>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Seluruh bendel wajib diperiksa ulang sebelum diserahkan ke bagian ekspedisi distribusi.</span>
                </div>
                <div class="font-medium text-slate-400">
                    Standar Operasional Bagian Sirkulasi &bull; Harian Subuh
                </div>
            </div>
        </div>

        <!-- Alur SOP Pengepakan Subuh (Timeline Operasional) -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">Alur Standar Operasional Prosedur (SOP) Pengepakan Subuh</h4>
                        <p class="text-[11px] text-slate-500">Timeline kerja tim ekspedisi & gudang mulai cetak hingga pemberangkatan</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200">Tahapan Kerja</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/40 relative">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-sky-100 text-sky-800">Tahap 1</span>
                        <span class="font-mono text-xs font-bold text-slate-600">01:00 WIB</span>
                    </div>
                    <h5 class="text-xs font-bold text-slate-900 mb-1">Penerimaan Tiras Koran</h5>
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Serah terima fisik koran dari bagian pracetak/percetakan dan pencocokan total oplah dengan daftar tiras harian.
                    </p>
                </div>

                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/40 relative">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-amber-100 text-amber-800">Tahap 2</span>
                        <span class="font-mono text-xs font-bold text-slate-600">01:15 WIB</span>
                    </div>
                    <h5 class="text-xs font-bold text-slate-900 mb-1">Quality Control & Cek Fisik</h5>
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Pemeriksaan kejelasan cetak, kerapian lipatan, dan kekeringan tinta koran agar tidak luntur saat dipegang pembaca.
                    </p>
                </div>

                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/40 relative">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">Tahap 3</span>
                        <span class="font-mono text-xs font-bold text-slate-600">01:30 WIB</span>
                    </div>
                    <h5 class="text-xs font-bold text-slate-900 mb-1">Pembendelan & Pelabelan</h5>
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Pengikatan per 50 eksemplar dengan tali rafia kencang, sortir koran lepas, dan penempelan slip label drop point agen.
                    </p>
                </div>

                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/40 relative">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-indigo-100 text-indigo-800">Tahap 4</span>
                        <span class="font-mono text-xs font-bold text-slate-600">02:30 WIB</span>
                    </div>
                    <h5 class="text-xs font-bold text-slate-900 mb-1">Loading & Surat Jalan</h5>
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Pemuatan bendel ke kendaraan distribusi sesuai urutan rute singgah dan penandatanganan Surat Jalan (SJ) ekspedisi.
                    </p>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
