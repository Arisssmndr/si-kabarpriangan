<x-app-layout>
    <div class="space-y-5" x-data="{ modalOpen: false, selectedFaktur: null, sisaPiutang: 0, nominalKoreksi: 0 }">

        <!-- ============================================================== -->
        <!-- 1. HEADER HALAMAN (PALET 60-30-10 & ENTERPRISE ACTION BAR)    -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Koreksi Piutang Iklan (Credit Note)
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Multi-Media AR Adjustment
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5 truncate">
                        Penerbitan nota kredit resmi (CN) untuk komplain cetak, gagal tayang, diskon susulan pimpinan, atau pemutihan piutang lintas seluruh media (Koran, Online, TV, Paket).
                    </p>
                </div>
            </div>

            <!-- Action Toolbar Kanan -->
            <div class="flex items-center gap-3 shrink-0 flex-wrap">
                <div class="hidden md:flex items-center gap-1.5 text-xs font-medium text-slate-600 bg-slate-50 px-3 py-2 rounded-xl border border-slate-200">
                    <span class="text-slate-400">Bukti Berikutnya:</span>
                    <span class="text-slate-900 font-mono font-bold">{{ $nextNoBukti }}</span>
                </div>

                <!-- Tombol Utama: Terbitkan Nota Kredit -->
                <button type="button" 
                        onclick="openModalKoreksi()"
                        class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Terbitkan Nota Kredit (CN)</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- ============================================================== -->
        <!-- 2. KPI / STATISTIK CEPAT (METRICS RIBBON 60-30-10)             -->
        <!-- ============================================================== -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <!-- Card 1: Total Dokumen CN -->
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Dokumen CN</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">{{ number_format($stats['total_count'] ?? 0) }}</div>
                <p class="text-xs text-slate-400 mt-0.5">Tercatat resmi GL</p>
            </div>

            <!-- Card 2: Total Nilai Koreksi -->
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Akumulasi Koreksi</span>
                <div class="text-lg font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($stats['total_nominal'] ?? 0, 0, ',', '.') }}</div>
                <p class="text-xs text-slate-400 mt-0.5">Pengurang piutang & omset</p>
            </div>

            <!-- Card 3: Faktur Disesuaikan -->
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Faktur Disesuaikan</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">{{ number_format($stats['total_faktur'] ?? 0) }}</div>
                <p class="text-xs text-slate-400 mt-0.5">Faktur dipotong CN</p>
            </div>

            <!-- Card 4: Faktur Siap Koreksi -->
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Faktur Belum Lunas</span>
                <div class="text-lg font-bold text-amber-600 mt-1 font-mono">{{ number_format($stats['faktur_aktif'] ?? 0) }}</div>
                <p class="text-xs text-slate-400 mt-0.5">Koran, Online, TV, Paket</p>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 3. FILTER BAR MANDIRI LINTAS MEDIA & ALASAN                    -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.iklan.koreksi') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pencarian Dokumen</label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}"
                           placeholder="No. Bukti, Faktur, Klien, Keterangan..." 
                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Saluran Media</label>
                    <select name="media" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all" {{ request('media', 'all') === 'all' ? 'selected' : '' }}>Semua Media</option>
                        <option value="koran" {{ request('media') === 'koran' ? 'selected' : '' }}>Koran Cetak</option>
                        <option value="online" {{ request('media') === 'online' ? 'selected' : '' }}>Iklan Online</option>
                        <option value="tv" {{ request('media') === 'tv' ? 'selected' : '' }}>Priangan TV</option>
                        <option value="paket" {{ request('media') === 'paket' ? 'selected' : '' }}>Paket Lintas Media</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kategori Alasan</label>
                    <select name="kategori_alasan" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all" {{ request('kategori_alasan', 'all') === 'all' ? 'selected' : '' }}>Semua Alasan</option>
                        <option value="KOMPLAIN_CETAK" {{ request('kategori_alasan') === 'KOMPLAIN_CETAK' ? 'selected' : '' }}>Komplain Cetak / Naskah</option>
                        <option value="GAGAL_TAYANG" {{ request('kategori_alasan') === 'GAGAL_TAYANG' ? 'selected' : '' }}>Gagal Tayang / Batal Sebagian</option>
                        <option value="DISKON_SUSULAN" {{ request('kategori_alasan') === 'DISKON_SUSULAN' ? 'selected' : '' }}>Diskon Kebijakan Direksi</option>
                        <option value="REVISI_TARIF" {{ request('kategori_alasan') === 'REVISI_TARIF' ? 'selected' : '' }}>Koreksi Hitung Tarif</option>
                        <option value="HAPUS_PIUTANG" {{ request('kategori_alasan') === 'HAPUS_PIUTANG' ? 'selected' : '' }}>Pemutihan Piutang Macet</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Dari Tanggal</label>
                    <input type="date" 
                           name="dari" 
                           value="{{ request('dari') }}"
                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Sampai Tanggal</label>
                    <input type="date" 
                           name="sampai" 
                           value="{{ request('sampai') }}"
                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs" />
                </div>

                <div class="sm:col-span-1 flex gap-1.5">
                    <button type="submit" 
                            title="Terapkan Filter"
                            class="flex-1 h-10 px-3 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'media', 'kategori_alasan', 'status_piutang', 'dari', 'sampai']))
                        <a href="{{ route('accounting.iklan.koreksi') }}" 
                           title="Reset Filter"
                           class="h-10 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl border border-slate-300 transition flex items-center justify-center">
                            ↺
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- ============================================================== -->
        <!-- 4. DATA TABEL NOTA KREDIT RESMI (CREDIT NOTE)                 -->
        <!-- ============================================================== -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[350px]">
                <table class="w-full text-left text-sm border-collapse">
                    <caption class="sr-only">Riwayat Nota Kredit (Credit Note)</caption>
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center text-slate-400">No</th>
                            <th class="py-3 px-3.5">No. Bukti CN</th>
                            <th class="py-3 px-3.5">Faktur & Media</th>
                            <th class="py-3 px-3.5">Pemasang / Klien</th>
                            <th class="py-3 px-3.5">Kategori Alasan</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5 text-right">Potongan CN (DPP / PPN)</th>
                            <th class="py-3 px-3.5 text-right">Sisa Piutang</th>
                            <th class="py-3 px-3.5 text-center w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-xs whitespace-nowrap">
                        @forelse($daftarCn as $key => $cn)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- No -->
                                <td class="py-3 px-3.5 text-center text-slate-400 font-mono">
                                    {{ $daftarCn->firstItem() + $key }}
                                </td>

                                <!-- No Bukti CN -->
                                <td class="py-3 px-3.5">
                                    <span class="font-bold text-slate-900 font-mono block">{{ $cn->no_bukti }}</span>
                                    @if($cn->jurnal)
                                        <span class="text-[10px] text-slate-400 font-mono">GL: {{ $cn->jurnal->no_jurnal }}</span>
                                    @endif
                                </td>

                                <!-- No Faktur & Media Badge -->
                                <td class="py-3 px-3.5">
                                    <span class="font-mono font-semibold text-slate-800 block">{{ $cn->no_faktur }}</span>
                                    @php
                                        $mediaBadge = match(strtolower((string) ($cn->media_type ?? ''))) {
                                            'koran'  => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'label' => 'Koran Cetak'],
                                            'online' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'label' => 'Online'],
                                            'tv'     => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'label' => 'Priangan TV'],
                                            'paket'  => ['bg' => 'bg-amber-50', 'text' => 'text-amber-800', 'border' => 'border-amber-200', 'label' => 'Paket Lintas Media'],
                                            default  => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'label' => 'Iklan']
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $mediaBadge['bg'] }} {{ $mediaBadge['text'] }} border {{ $mediaBadge['border'] }} mt-0.5">
                                        {{ $mediaBadge['label'] }}
                                    </span>
                                </td>

                                <!-- Nama Pemasang / Klien -->
                                <td class="py-3 px-3.5">
                                    <div class="min-w-0 max-w-xs">
                                        <p class="font-semibold text-slate-900 truncate" title="{{ $cn->nama_entitas }}">
                                            {{ $cn->nama_entitas ?: '-' }}
                                        </p>
                                        <p class="text-[11px] text-slate-400 truncate" title="{{ $cn->keterangan }}">
                                            {{ $cn->keterangan }}
                                        </p>
                                    </div>
                                </td>

                                <!-- Kategori Alasan -->
                                <td class="py-3 px-3.5">
                                    @php
                                        $alasanBadge = match((string) ($cn->kategori_alasan ?? '')) {
                                            'KOMPLAIN_CETAK' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'label' => 'Komplain Cetak'],
                                            'GAGAL_TAYANG'   => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'label' => 'Gagal Tayang'],
                                            'DISKON_SUSULAN' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'label' => 'Diskon Direksi'],
                                            'REVISI_TARIF'   => ['bg' => 'bg-cyan-50', 'text' => 'text-cyan-700', 'label' => 'Koreksi Tarif'],
                                            'HAPUS_PIUTANG'  => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'label' => 'Hapus Macet'],
                                            default          => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'label' => $cn->kategori_alasan ?: 'Koreksi']
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $alasanBadge['bg'] }} {{ $alasanBadge['text'] }}">
                                        {{ $alasanBadge['label'] }}
                                    </span>
                                </td>

                                <!-- Tanggal Koreksi -->
                                <td class="py-3 px-3.5 text-slate-600 font-mono">
                                    {{ \Carbon\Carbon::parse($cn->tanggal_koreksi)->translatedFormat('d/m/Y') }}
                                </td>

                                <!-- Nilai Koreksi -->
                                <td class="py-3 px-3.5 text-right font-mono">
                                    <span class="font-bold text-slate-900 block">Rp {{ number_format($cn->jumlah_koreksi, 0, ',', '.') }}</span>
                                    @if($cn->ppn_koreksi > 0)
                                        <span class="text-[10px] text-slate-400 block">PPN: Rp {{ number_format($cn->ppn_koreksi, 0, ',', '.') }}</span>
                                    @endif
                                </td>

                                <!-- Sisa Piutang Setelah Koreksi -->
                                <td class="py-3 px-3.5 text-right font-mono font-semibold">
                                    @if($cn->saldo_setelah_koreksi <= 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Lunas (Rp 0)
                                        </span>
                                    @else
                                        <span class="text-slate-800">
                                            Rp {{ number_format($cn->saldo_setelah_koreksi, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="py-3 px-3.5 text-center">
                                    <x-action-dropdown>
                                        <a href="{{ route('accounting.iklan.cetak-koreksi', $cn->id) }}" 
                                           target="_blank"
                                           class="flex items-center gap-2 px-3.5 py-2 hover:bg-slate-50 text-slate-700 hover:text-kp-blue-700 transition font-medium text-xs">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            <span>Cetak Nota Kredit (CN)</span>
                                        </a>
                                    </x-action-dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center">
                                    <div class="max-w-xs mx-auto flex flex-col items-center justify-center text-center">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-bold text-slate-800">Belum Ada Nota Kredit Iklan</h3>
                                        <p class="text-xs text-slate-500 mt-1">
                                            @if(request('q') || request('media') || request('kategori_alasan'))
                                                Tidak ditemukan nota kredit sesuai kriteria filter pencarian.
                                            @else
                                                Seluruh faktur iklan aktif masih memiliki saldo piutang normal tanpa penyesuaian CN.
                                            @endif
                                        </p>
                                        <button type="button" 
                                                onclick="openModalKoreksi()"
                                                class="mt-4 inline-flex items-center gap-1.5 h-9 px-3.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                            <span>Terbitkan Nota Kredit Sekarang</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            @if($daftarCn->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50/50">
                    {{ $daftarCn->links() }}
                </div>
            @endif
        </div>

        <!-- ============================================================== -->
        <!-- 5. MODAL DIALOG TERBITKAN NOTA KREDIT (ENTERPRISE MULTI-MEDIA) -->
        <!-- ============================================================== -->
        <div id="modalKoreksi" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div class="bg-white rounded-2xl max-w-xl w-full shadow-xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[92vh] overflow-hidden">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0 bg-slate-50/50">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Penerbitan Nota Kredit (Credit Note)</h3>
                        <p class="text-xs text-slate-500">Pemberian potongan resmi atau pembatalan sebagian piutang iklan</p>
                    </div>
                    <button type="button" onclick="closeModalKoreksi()" 
                            class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer" 
                            title="Tutup">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Form Input Koreksi -->
                <form method="POST" action="{{ route('accounting.iklan.koreksi.store') }}" id="formKoreksiCn" class="flex flex-col flex-1 min-h-0">
                    @csrf

                    <!-- Body Content -->
                    <div class="p-6 space-y-4 text-sm overflow-y-auto flex-1">

                        <!-- Baris 1: No Bukti & Tanggal -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1 text-xs">
                                    No. Bukti CN (Otomatis)
                                </label>
                                <input type="text" 
                                       name="nomor_bukti" 
                                       id="modal_nomor_bukti"
                                       value="{{ $nextNoBukti }}" 
                                       readonly
                                       class="w-full h-10 px-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-600 cursor-not-allowed select-none" />
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1 text-xs">
                                    Tanggal Koreksi <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" 
                                       name="tanggal" 
                                       id="modal_tanggal"
                                       value="{{ date('Y-m-d') }}" 
                                       required
                                       class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                            </div>
                        </div>

                        <!-- Baris 2: Pilih / Cari Faktur Iklan Lintas Media -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-semibold text-slate-700 text-xs">
                                    Pilih Faktur Berpiutang <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[11px] text-slate-400">Koran, Online, TV, Paket</span>
                            </div>

                            <!-- Live Search Input -->
                            <div class="relative mb-2">
                                <input type="text" 
                                       id="liveSearchFaktur" 
                                       oninput="handleLiveSearchFaktur(this.value)"
                                       placeholder="Ketik untuk mencari faktur / nama klien..." 
                                       class="w-full h-9 px-3 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:bg-white transition" />
                                <span id="searchSpinner" class="hidden absolute right-3 top-2 text-slate-400 text-xs">⏳</span>
                            </div>

                            <!-- Dropdown List Faktur -->
                            <select name="no_faktur" 
                                    id="modal_select_faktur"
                                    onchange="handleFakturSelection(this)"
                                    required 
                                    class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                <option value="" data-piutang="0" data-penjualan="0" data-nama="-" data-media="-">-- Pilih dari Faktur Aktif --</option>
                                @foreach($daftarFaktur as $f)
                                    <option value="{{ $f['no_faktur'] }}" 
                                            data-piutang="{{ $f['sisa_piutang'] }}"
                                            data-penjualan="{{ $f['total_tagihan'] }}"
                                            data-nama="{{ $f['nama'] }}"
                                            data-media="{{ $f['media'] }}"
                                            data-ppn="{{ $f['ppn_nominal'] }}">
                                        [{{ $f['media'] }}] {{ $f['no_faktur'] }} &bull; {{ $f['nama'] }} (Sisa: Rp {{ number_format($f['sisa_piutang'], 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Ringkasan Faktur Terpilih (Card Preview) -->
                        <div id="cardInfoFaktur" class="hidden p-3.5 rounded-xl bg-kp-blue-50/70 border border-kp-blue-200/80 text-xs space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-white text-kp-blue-700 border border-kp-blue-200" id="infoMediaBadge">
                                    Media
                                </span>
                                <div class="text-right">
                                    <span class="text-slate-500 text-[11px] block">Sisa Piutang Berjalan:</span>
                                    <span class="font-mono font-bold text-slate-900 text-sm" id="infoSisaPiutang">Rp 0</span>
                                </div>
                            </div>
                            <div class="pt-1 border-t border-kp-blue-200/40 flex items-center justify-between">
                                <span class="font-semibold text-slate-900 truncate max-w-[260px]" id="infoNamaPemasang">-</span>
                                <span class="text-slate-600 text-[11px]" id="infoTotalTagihan">Tagihan: Rp 0</span>
                            </div>
                        </div>

                        <!-- Baris 3: Kategori Alasan Koreksi (Reason Code Standar Korporat) -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1 text-xs">
                                Kategori Alasan Koreksi <span class="text-rose-500">*</span>
                            </label>
                            <select name="kategori_alasan" 
                                    id="modal_kategori_alasan"
                                    required 
                                    class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                <option value="KOMPLAIN_CETAK">1. Komplain Cetak / Salah Naskah / Posisi Halaman</option>
                                <option value="GAGAL_TAYANG">2. Gagal Tayang / Slot Siaran Batal Sebagian</option>
                                <option value="DISKON_SUSULAN">3. Diskon Khusus Kebijakan Pimpinan / Direksi</option>
                                <option value="REVISI_TARIF">4. Koreksi Kesalahan Perhitungan Tarif</option>
                                <option value="HAPUS_PIUTANG">5. Pemutihan Piutang Macet (Bad Debt Write-off)</option>
                                <option value="LAINNYA">6. Alasan Operasional Lainnya</option>
                            </select>
                        </div>

                        <!-- Baris 4: Jumlah Koreksi (Rp) & Kalkulasi Real-time -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1 text-xs">
                                Jumlah Nilai Koreksi (Bruto / Pengurang Piutang) <span class="text-rose-500">*</span>
                            </label>
                            
                            <div class="flex rounded-xl border border-slate-300 focus-within:border-kp-blue-600 focus-within:ring-1 focus-within:ring-kp-blue-600 overflow-hidden bg-white shadow-2xs transition h-10">
                                <span class="inline-flex items-center px-3.5 bg-slate-50 border-r border-slate-200 text-xs text-slate-500 font-semibold select-none pointer-events-none">Rp</span>
                                <input type="text" 
                                       name="jumlah_koreksi" 
                                       id="modal_jumlah_koreksi" 
                                       oninput="handleNominalInput(this)" 
                                       placeholder="0" 
                                       required 
                                       class="flex-1 h-10 px-3.5 bg-transparent text-sm font-mono font-bold text-slate-900 border-none focus:outline-none focus:ring-0" />
                            </div>

                            <p id="alertMelebihiPiutang" class="hidden mt-1.5 text-xs text-rose-600 font-medium">
                                ⚠️ Nilai koreksi melebihi sisa piutang faktur!
                            </p>

                            <!-- Live Split Preview: DPP vs PPN -->
                            <div id="splitPpnPreview" class="hidden mt-2 p-2.5 rounded-lg bg-slate-50 border border-slate-200 text-[11px] text-slate-600 flex items-center justify-between font-mono">
                                <span>Alokasi Jurnal:</span>
                                <div>
                                    <span class="mr-3">DPP (Omset): <strong id="lblDppSplit" class="text-slate-800">Rp 0</strong></span>
                                    <span>PPN: <strong id="lblPpnSplit" class="text-slate-800">Rp 0</strong></span>
                                </div>
                            </div>
                        </div>

                        <!-- Baris 5: Uraian Keterangan Lengkap -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1 text-xs">
                                Uraian Keterangan / Kronologi <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="keterangan" 
                                      id="modal_keterangan" 
                                      rows="2" 
                                      required 
                                      placeholder="Penjelasan rinci persetujuan pimpinan / kronologi komplain..." 
                                      class="w-full p-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5 shrink-0">
                        <button type="button" 
                                onclick="closeModalKoreksi()" 
                                class="h-10 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-xl text-xs font-semibold transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                id="btnSubmitKoreksi" 
                                class="h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white rounded-xl text-xs font-bold transition cursor-pointer shadow-xs disabled:opacity-50 disabled:cursor-not-allowed">
                            Simpan & Terbitkan CN
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        let currentSisaPiutang = 0;
        let currentTotalTagihan = 0;
        let currentPpnNominal = 0;
        let searchTimeout = null;

        function openModalKoreksi() {
            const modal = document.getElementById('modalKoreksi');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                setTimeout(() => {
                    document.getElementById('liveSearchFaktur')?.focus();
                }, 100);
            }
        }

        function closeModalKoreksi() {
            const modal = document.getElementById('modalKoreksi');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        function handleFakturSelection(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                document.getElementById('cardInfoFaktur')?.classList.add('hidden');
                document.getElementById('splitPpnPreview')?.classList.add('hidden');
                currentSisaPiutang = 0;
                currentTotalTagihan = 0;
                currentPpnNominal = 0;
                return;
            }

            const sisa = parseFloat(selectedOption.getAttribute('data-piutang') || 0);
            const total = parseFloat(selectedOption.getAttribute('data-penjualan') || 0);
            const ppn = parseFloat(selectedOption.getAttribute('data-ppn') || 0);
            const nama = selectedOption.getAttribute('data-nama') || '-';
            const media = selectedOption.getAttribute('data-media') || 'Iklan';

            currentSisaPiutang = sisa;
            currentTotalTagihan = total;
            currentPpnNominal = ppn;

            const card = document.getElementById('cardInfoFaktur');
            const infoNama = document.getElementById('infoNamaPemasang');
            const infoSisa = document.getElementById('infoSisaPiutang');
            const infoTotal = document.getElementById('infoTotalTagihan');
            const infoMedia = document.getElementById('infoMediaBadge');

            if (infoNama) infoNama.innerText = nama;
            if (infoSisa) infoSisa.innerText = 'Rp ' + sisa.toLocaleString('id-ID');
            if (infoTotal) infoTotal.innerText = 'Total Faktur: Rp ' + total.toLocaleString('id-ID');
            if (infoMedia) infoMedia.innerText = media;

            card?.classList.remove('hidden');

            const inputNominal = document.getElementById('modal_jumlah_koreksi');
            if (inputNominal && inputNominal.value) {
                handleNominalInput(inputNominal);
            }
        }

        function handleNominalInput(inputElement) {
            let cleanVal = inputElement.value.replace(/[^0-9]/g, '');
            let numVal = parseFloat(cleanVal) || 0;

            if (cleanVal) {
                inputElement.value = numVal.toLocaleString('id-ID');
            } else {
                inputElement.value = '';
            }

            const alertEl = document.getElementById('alertMelebihiPiutang');
            const btnSubmit = document.getElementById('btnSubmitKoreksi');
            const previewBox = document.getElementById('splitPpnPreview');
            const lblDpp = document.getElementById('lblDppSplit');
            const lblPpn = document.getElementById('lblPpnSplit');

            if (currentSisaPiutang > 0 && numVal > currentSisaPiutang) {
                alertEl?.classList.remove('hidden');
                if (btnSubmit) btnSubmit.disabled = true;
            } else {
                alertEl?.classList.add('hidden');
                if (btnSubmit) btnSubmit.disabled = (numVal <= 0);
            }

            // Hitung proporsi PPN dinamis preview
            if (numVal > 0 && currentTotalTagihan > 0) {
                let ppnSplit = 0;
                if (currentPpnNominal > 0) {
                    let ratio = currentPpnNominal / currentTotalTagihan;
                    ppnSplit = Math.round(numVal * ratio);
                }
                let dppSplit = numVal - ppnSplit;

                if (lblDpp) lblDpp.innerText = 'Rp ' + dppSplit.toLocaleString('id-ID');
                if (lblPpn) lblPpn.innerText = 'Rp ' + ppnSplit.toLocaleString('id-ID');
                previewBox?.classList.remove('hidden');
            } else {
                previewBox?.classList.add('hidden');
            }
        }

        function handleLiveSearchFaktur(keyword) {
            clearTimeout(searchTimeout);
            const term = (keyword || '').trim();
            const select = document.getElementById('modal_select_faktur');
            if (!select) return;

            if (term.length < 2) {
                return;
            }

            const spinner = document.getElementById('searchSpinner');
            if (spinner) spinner.classList.remove('hidden');

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('accounting.iklan.koreksi.cari-faktur') }}?q=${encodeURIComponent(term)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (spinner) spinner.classList.add('hidden');
                        if (Array.isArray(data) && data.length > 0) {
                            // Hapus opsi kecuali opsi pertama
                            select.innerHTML = '<option value="" data-piutang="0" data-penjualan="0" data-nama="-" data-media="-">-- Pilih dari Hasil Pencarian --</option>';
                            data.forEach(item => {
                                const opt = document.createElement('option');
                                opt.value = item.no_faktur;
                                opt.setAttribute('data-piutang', item.sisa_piutang);
                                opt.setAttribute('data-penjualan', item.total_tagihan);
                                opt.setAttribute('data-nama', item.nama);
                                opt.setAttribute('data-media', item.media);
                                opt.setAttribute('data-ppn', item.ppn_nominal);
                                opt.textContent = `[${item.media}] ${item.no_faktur} • ${item.nama} (Sisa: Rp ${Number(item.sisa_piutang).toLocaleString('id-ID')})`;
                                select.appendChild(opt);
                            });
                            // Auto select yang pertama
                            if (select.options.length > 1) {
                                select.selectedIndex = 1;
                                handleFakturSelection(select);
                            }
                        }
                    })
                    .catch(() => {
                        if (spinner) spinner.classList.add('hidden');
                    });
            }, 300);
        }
    </script>
    @endpush
</x-app-layout>
