<x-app-layout>
    <div class="space-y-5">

        <!-- 1. Header Halaman (Standar Divisi Iklan 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-9 sm:h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Faktur Sirkulasi Insidentil
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 shrink-0">
                            Eceran &amp; Non-Agen
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Penjualan koran eceran langsung di kantor/loket, pesanan borongan acara/event dinas, dan pembelian insidentil
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                <a href="{{ route('keuangan.faktur.index', ['tab' => 'piutang']) }}" 
                   class="inline-flex items-center gap-2 h-10 px-4 bg-amber-400 hover:bg-amber-500 text-slate-900 text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <span>Faktur Piutang &amp; Surat Tugas</span>
                </a>
            </div>
        </div>

        <!-- 2. KPI Summary Cards (Standar Divisi Iklan 60-30-10) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Total Faktur Insidentil</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalInsidentilCount) }} <span class="text-xs font-normal text-slate-400 font-sans">Dokumen</span>
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    Penjualan eceran &amp; acara
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Nilai Tagihan Insidentil</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalInsidentilTagihan, 0, ',', '.') }}
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    Total omset penjualan insidentil
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Diterima Tunai / Kas</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalInsidentilTerbayar, 0, ',', '.') }}
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    {{ $totalInsidentilTagihan > 0 ? round(($totalInsidentilTerbayar / $totalInsidentilTagihan) * 100, 1) : 0 }}% langsung lunas
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Sisa Piutang Insidentil</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalInsidentilPiutang, 0, ',', '.') }}
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    Tempo instansi / perorangan
                </div>
            </div>
        </div>

        <!-- 3. Toolbar Pencarian & Filter (h-11 rounded-xl) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('keuangan.faktur.sirkulasi.insidentil') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Data</label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik no. faktur insidentil atau nama pembeli/instansi..." 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status Pembayaran</label>
                    <select name="status" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="">Semua Status</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="cicilan" {{ request('status') === 'cicilan' ? 'selected' : '' }}>Cicilan</option>
                        <option value="belum_bayar" {{ request('status') === 'belum_bayar' ? 'selected' : '' }}>Piutang (Tempo)</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'status', 'dari_tanggal', 'sampai_tanggal']))
                        <a href="{{ route('keuangan.faktur.sirkulasi.insidentil') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 4. Pure Data Table (Kolom Terpisah Standar Divisi Iklan) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. Faktur</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Jatuh Tempo</th>
                            <th class="py-3 px-3.5">Pembeli / Instansi</th>
                            <th class="py-3 px-3.5">Alamat</th>
                            <th class="py-3 px-3.5">Telepon</th>
                            <th class="py-3 px-3.5">Keperluan Event</th>
                            <th class="py-3 px-3.5 text-right w-32">Tagihan</th>
                            <th class="py-3 px-3.5 text-right w-32">Dibayar</th>
                            <th class="py-3 px-3.5 text-right w-32">Piutang</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($fakturs as $key => $faktur)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                    {{ method_exists($fakturs, 'firstItem') && $fakturs->firstItem() ? ($fakturs->firstItem() + $key) : ($key + 1) }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    <a href="{{ route('keuangan.faktur.show', $faktur->id) }}" class="hover:underline">
                                        {{ $faktur->no_faktur }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-mono font-normal">
                                    {{ optional($faktur->tanggal_faktur)->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-mono font-normal">
                                    {{ optional($faktur->jatuh_tempo)->format('d/m/Y') ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900 uppercase">
                                    {{ $faktur->customer->nama_customer ?? 'UMUM' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal truncate max-w-xs" title="{{ $faktur->customer->alamat ?? '-' }}">
                                    {{ $faktur->customer->alamat ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal">
                                    {{ $faktur->customer->no_telepon ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 font-normal truncate max-w-xs" title="{{ $faktur->catatan ?? '-' }}">
                                    {{ $faktur->catatan ?: '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($faktur->total_tagihan, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                        <span class="text-slate-400 font-normal select-none">Rp</span>
                                        <span class="font-semibold text-kp-blue-700 tabular-nums">{{ number_format($faktur->total_terbayar, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    @if($faktur->sisa_piutang > 0)
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-normal text-slate-700 tabular-nums">{{ number_format($faktur->sisa_piutang, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <div class="text-right font-mono text-sm text-slate-400 font-normal">Rp 0</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($faktur->status === 'lunas')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200">
                                            Lunas
                                        </span>
                                    @elseif($faktur->status === 'cicilan')
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border bg-amber-50 text-amber-800 border-amber-300">
                                            Cicilan
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border bg-slate-100 text-slate-700 border-slate-200">
                                            Piutang
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center" x-data="{ open: false }" :class="{ 'relative z-50': open }">
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
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             class="absolute right-0 z-50 mt-1 w-44 origin-top-right rounded-xl bg-white border border-slate-200 shadow-lg py-1 focus:outline-none text-left">
                                            <a href="{{ route('keuangan.faktur.show', $faktur->id) }}" class="flex items-center gap-2 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail Faktur</span>
                                            </a>
                                            <a href="{{ route('keuangan.faktur.cetak', $faktur->id) }}" target="_blank" class="flex items-center gap-2 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                <span>Cetak Faktur</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="py-12 text-center text-slate-400">
                                    <p class="text-sm font-semibold text-slate-600">Belum ada faktur sirkulasi insidentil pada kriteria ini.</p>
                                    <p class="text-xs text-slate-400 mt-1">Data faktur insidentil otomatis tercatat dari penjualan eceran/event.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($fakturs, 'hasPages') && $fakturs->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $fakturs->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
