<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Standar Selaras Foto 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-bold text-black tracking-tight">
                        Daftar Iklan &amp; Publikasi Online Multimedia
                    </h1>
                    <p class="text-xs text-black mt-0.5">
                        Pengelolaan faktur iklan portal, media sosial, video podcast, dan alur redaksi Kabar Priangan Grup.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('transaksionline.create') }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs hover:shadow transition cursor-pointer">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Entri Order Publikasi Baru</span>
                </a>
            </div>
        </div>

        <!-- TAB STATUS ALUR PRODUKSI REDAKSI & MEDSOS (STANDAR PERS BESAR) -->
        @php
            $currentStatusProd = request('status_produksi', '');
            $tabs = [
                '' => ['label' => 'Semua Order', 'count' => $statusCounts['semua'] ?? 0, 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
                'menunggu_bahan' => ['label' => '1. Menunggu Bahan', 'count' => $statusCounts['menunggu_bahan'] ?? 0, 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                'antrean_redaksi' => ['label' => '2. Antrean Redaksi', 'count' => $statusCounts['antrean_redaksi'] ?? 0, 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                'tayang' => ['label' => '3. Tayang (Live)', 'count' => $statusCounts['tayang'] ?? 0, 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                'selesai' => ['label' => '4. Selesai (Archived)', 'count' => $statusCounts['selesai'] ?? 0, 'icon' => 'M5 13l4 4L19 7'],
            ];
        @endphp
        <div class="bg-white border border-slate-200 rounded-xl p-1.5 shadow-xs overflow-x-auto">
            <div class="flex items-center gap-1.5 min-w-max">
                @foreach($tabs as $val => $tab)
                    @php
                        $isActive = ($currentStatusProd === $val);
                        $queryParams = request()->except('page');
                        if ($val === '') {
                            unset($queryParams['status_produksi']);
                        } else {
                            $queryParams['status_produksi'] = $val;
                        }
                        $tabUrl = route('transaksionline.index', $queryParams);
                    @endphp
                    <a href="{{ $tabUrl }}" 
                       class="flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold transition cursor-pointer {{ $isActive ? 'bg-kp-blue-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <svg class="w-3.5 h-3.5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tab['icon'] }}" />
                        </svg>
                        <span>{{ $tab['label'] }}</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                            {{ $tab['count'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Toolbar Pencarian & Filter -->
        <div class="bg-white border border-slate-300 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('transaksionline.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                @if(request('status_produksi'))
                    <input type="hidden" name="status_produksi" value="{{ request('status_produksi') }}">
                @endif

                <div class="sm:col-span-6">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Pencarian Data Order
                    </label>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="Ketik no faktur, no SPK, nama instansi, atau sales..."
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-black placeholder-gray-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Status Pembayaran
                    </label>
                    <select name="status" class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-lg text-xs text-black focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        <option value="">Semua Pembayaran</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="piutang" {{ request('status') === 'piutang' ? 'selected' : '' }}>Belum Lunas / Piutang</option>
                    </select>
                </div>

                <div class="sm:col-span-3 flex gap-2">
                    <button type="submit" class="flex-1 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-semibold text-xs rounded-lg shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if(request()->hasAny(['q', 'status', 'status_produksi']))
                        <a href="{{ route('transaksionline.index') }}" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-lg transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Transaksi Online & Multimedia (Standar Pers Profesional) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3 text-center w-10">No</th>
                            <th class="py-3 px-3.5">Faktur / SPK</th>
                            <th class="py-3 px-3.5">Tgl Order</th>
                            <th class="py-3 px-3.5">Instansi / Pemasang</th>
                            <th class="py-3 px-3.5">Format &amp; Saluran</th>
                            <th class="py-3 px-3.5">Tgl Muat</th>
                            <th class="py-3 px-3.5 text-center">Alur Redaksi</th>
                            <th class="py-3 px-3.5 text-center">Bukti Tayang</th>
                            <th class="py-3 px-3.5 text-right w-28">Tagihan</th>
                            <th class="py-3 px-3.5 text-right w-28">Piutang</th>
                            <th class="py-3 px-3.5 text-center w-20">Kasir</th>
                            <th class="py-3 px-3.5 text-center w-12">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($transaksionline as $key => $t)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-3 text-center text-slate-500 font-normal">
                                    {{ $transaksionline->firstItem() + $key }}
                                </td>
                                <td class="py-3 px-3.5 font-mono text-xs">
                                    <div class="font-bold text-kp-blue-700">{{ $t->nofakturonline }}</div>
                                    @if($t->no_spk)
                                        <div class="text-[10px] text-slate-500 font-normal truncate max-w-[140px]" title="No. SPK: {{ $t->no_spk }}">
                                            SPK: {{ $t->no_spk }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-slate-600 font-mono">
                                    {{ \Carbon\Carbon::parse($t->tanggal_transaksionline)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-bold text-slate-900 max-w-[180px] truncate" title="{{ $t->nama_pemasangonline }}">
                                        {{ $t->nama_pemasangonline }}
                                    </div>
                                    @if($t->produk_jasa)
                                        <div class="text-[10px] text-slate-500 truncate max-w-[180px]" title="{{ $t->produk_jasa }}">
                                            {{ $t->produk_jasa }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-slate-600">
                                    <!-- Format Konten Pills -->
                                    <div class="flex flex-wrap gap-1 max-w-[200px] mb-1">
                                        @if(!empty($t->kategori_produk_publikasi) && is_array($t->kategori_produk_publikasi))
                                            @foreach($t->kategori_produk_publikasi as $kat)
                                                <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-50 text-amber-800 border border-amber-200 truncate">{{ $kat }}</span>
                                            @endforeach
                                        @else
                                            <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-50 text-amber-800 border border-amber-200">Advertorial Berita</span>
                                        @endif
                                    </div>
                                    <!-- Saluran Media Pills -->
                                    <div class="flex flex-wrap gap-1 max-w-[200px]">
                                        @if(!empty($t->platforms_publikasi) && is_array($t->platforms_publikasi))
                                            @foreach($t->platforms_publikasi as $pf)
                                                <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-semibold bg-sky-50 text-sky-800 border border-sky-200">{{ $pf }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-[10px] text-slate-600">{{ $t->portal_iklanonline ?: 'Portal Berita' }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 text-slate-600 font-mono text-[11px]">
                                    <div>{{ \Carbon\Carbon::parse($t->tanggal_muatiklanonline)->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $t->total_muatiklanonline }}x tayang</div>
                                </td>
                                
                                <!-- Status Alur Redaksi (Quick Dropdown Changer) -->
                                <td class="py-3 px-3.5 text-center">
                                    <select onchange="updateStatusProduksiAjax('{{ $t->id }}', this.value)"
                                            class="text-[10px] font-bold rounded-lg px-2 py-1 border transition cursor-pointer {{ $t->status_produksi_badge_class }}">
                                        <option value="menunggu_bahan" {{ $t->status_produksi === 'menunggu_bahan' ? 'selected' : '' }}>1. Menunggu Bahan</option>
                                        <option value="antrean_redaksi" {{ $t->status_produksi === 'antrean_redaksi' ? 'selected' : '' }}>2. Antrean Redaksi</option>
                                        <option value="tayang" {{ $t->status_produksi === 'tayang' ? 'selected' : '' }}>3. Tayang (Live)</option>
                                        <option value="selesai" {{ $t->status_produksi === 'selesai' ? 'selected' : '' }}>4. Selesai (Archived)</option>
                                    </select>
                                </td>

                                <!-- Bukti Tayang (Digital Tearsheet) -->
                                <td class="py-3 px-3.5 text-center">
                                    @php
                                        $hasBukti = !empty($t->url_berita_tayang) || !empty($t->url_medsos_tayang) || !empty($t->file_bukti_tayang);
                                    @endphp
                                    <button type="button" 
                                            onclick="openModalBuktiTayang({{ json_encode([
                                                'id' => $t->id,
                                                'nofaktur' => $t->nofakturonline,
                                                'instansi' => $t->nama_pemasangonline,
                                                'url_berita' => $t->url_berita_tayang,
                                                'url_medsos' => $t->url_medsos_tayang,
                                                'file_bukti' => $t->file_bukti_tayang ? asset('storage/' . $t->file_bukti_tayang) : null,
                                                'tgl_realisasi' => $t->tanggal_realisasi_tayang?->format('Y-m-d') ?: date('Y-m-d'),
                                                'status_produksi' => $t->status_produksi,
                                                'catatan_redaksi' => $t->catatan_redaksi,
                                            ]) }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-semibold transition cursor-pointer {{ $hasBukti ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-300' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200' }}">
                                        @if($hasBukti)
                                            <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            <span>Lihat/Edit Bukti</span>
                                        @else
                                            <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                            <span>Input Bukti</span>
                                        @endif
                                    </button>
                                </td>

                                <td class="py-3 px-3.5 text-right font-mono text-xs font-semibold text-slate-900">
                                    Rp {{ number_format($t->totaltagihan_transaksionline, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono text-xs font-medium text-slate-700">
                                    @if($t->piutang_transaksionline > 0)
                                        <span class="text-rose-600 font-bold">Rp {{ number_format($t->piutang_transaksionline, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-400 font-normal">Rp 0</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @if($t->piutang_transaksionline <= 0)
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Lunas
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            Tempo
                                        </span>
                                    @endif
                                </td>
                                
                                <!-- Dropdown Aksi Titik Tiga -->
                                <td class="py-3 px-3.5 text-center" 
                                    x-data="{ open: false }" 
                                    :class="{ 'relative z-50': open }">
                                    <div class="relative inline-block text-left" @click.outside="open = false">
                                        <button type="button" 
                                                @click="open = !open" 
                                                class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-700 hover:text-slate-950 hover:bg-slate-100 transition focus:outline-none cursor-pointer"
                                                :class="{ 'text-slate-950 bg-slate-100': open }"
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
                                            
                                            <!-- 1. Input Bukti Tayang (Digital Tearsheet) -->
                                            <button type="button"
                                                    onclick="openModalBuktiTayang({{ json_encode([
                                                        'id' => $t->id,
                                                        'nofaktur' => $t->nofakturonline,
                                                        'instansi' => $t->nama_pemasangonline,
                                                        'url_berita' => $t->url_berita_tayang,
                                                        'url_medsos' => $t->url_medsos_tayang,
                                                        'file_bukti' => $t->file_bukti_tayang ? asset('storage/' . $t->file_bukti_tayang) : null,
                                                        'tgl_realisasi' => $t->tanggal_realisasi_tayang?->format('Y-m-d') ?: date('Y-m-d'),
                                                        'status_produksi' => $t->status_produksi,
                                                        'catatan_redaksi' => $t->catatan_redaksi,
                                                    ]) }})"
                                                    @click="open = false"
                                                    class="w-full group flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-emerald-700 hover:text-emerald-800 hover:bg-emerald-50 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span>Input Bukti Tayang</span>
                                            </button>

                                            <!-- 2. Cetak Surat Order & Faktur -->
                                            <a href="{{ route('transaksionline.cetak', $t->id) }}" 
                                               target="_blank"
                                               @click="open = false"
                                               class="group flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:text-kp-blue-700 hover:bg-kp-blue-50 rounded-lg transition">
                                                <svg class="w-4 h-4 text-slate-400 group-hover:text-kp-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                                <span>Cetak Order Publikasi</span>
                                            </a>

                                            <!-- 3. Ubah Data Transaksi -->
                                            <a href="{{ route('transaksionline.edit', $t->id) }}" 
                                               @click="open = false"
                                               class="w-full group flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:text-kp-blue-700 hover:bg-kp-blue-50 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-slate-400 group-hover:text-kp-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Ubah Data Order</span>
                                            </a>

                                            <div class="h-px bg-slate-100 my-1"></div>

                                            <!-- 4. Hapus Transaksi -->
                                            <button type="button" 
                                                    onclick="deleteTransaksi('{{ $t->id }}', '{{ $t->nofakturonline }}')"
                                                    @click="open = false"
                                                    class="w-full group flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-rose-400 group-hover:text-rose-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span>Hapus Transaksi</span>
                                            </button>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="py-12 text-center text-slate-500 font-normal">
                                    Belum ada data transaksi order publikasi online. Klik "Entri Order Publikasi Baru" untuk memulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transaksionline->hasPages())
                <div class="p-4 border-t border-slate-200 bg-white">
                    {{ $transaksionline->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- MODAL POST-PUBLISH: INPUT BUKTI TAYANG & DIGITAL TEARSHEET -->
    <div id="modalBuktiTayang" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs hidden p-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto transform transition-all">
            
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 via-white to-slate-50 border-b border-emerald-100 flex items-center justify-between sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-xs shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 tracking-tight" id="bt_modal_title">
                            Input Bukti Tayang (Digital Tearsheet)
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5" id="bt_modal_sub">
                            Laporan bukti publikasi live untuk lampiran penagihan &amp; SPJ klien.
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeModalBuktiTayang()" 
                        class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer text-lg leading-none">&times;</button>
            </div>

            <form id="formBuktiTayang" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 text-xs">
                @csrf
                <input type="hidden" id="bt_transaksi_id" name="transaksi_id">

                <!-- Info Instansi & Faktur -->
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between text-xs">
                    <div>
                        <span class="text-[10px] text-slate-400 block font-medium">Instansi / Pemesan:</span>
                        <strong class="text-slate-900 text-xs" id="bt_nama_instansi">-</strong>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 block font-medium">No. Faktur:</span>
                        <span class="font-mono font-bold text-kp-blue-700" id="bt_nofaktur">-</span>
                    </div>
                </div>

                <!-- URL Berita Tayang -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        URL Link Berita / Artikel Portal Tayang
                    </label>
                    <input type="url" name="url_berita_tayang" id="bt_url_berita" 
                           placeholder="https://kabarpriangan.com/berita/..."
                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                </div>

                <!-- URL Medsos Tayang -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        URL Link Postingan Medsos (Instagram / TikTok / YouTube)
                    </label>
                    <input type="url" name="url_medsos_tayang" id="bt_url_medsos" 
                           placeholder="https://instagram.com/p/... atau link YouTube"
                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                </div>

                <!-- File Bukti Screenshot & Tanggal Realisasi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-start">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Upload File Screenshot Bukti Tayang (Image/PDF)
                        </label>
                        <input type="file" name="file_bukti_tayang" id="bt_file_bukti" accept="image/*,.pdf"
                               class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer border border-slate-300 rounded-xl bg-white">
                        <div id="bt_preview_file_link" class="mt-1 hidden text-[11px]">
                            <a href="#" target="_blank" class="text-kp-blue-600 underline font-medium">Lihat File Bukti Tayang Tersimpan</a>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Tanggal Realisasi Siar / Tayang
                        </label>
                        <input type="date" name="tanggal_realisasi_tayang" id="bt_tgl_realisasi"
                               class="w-full h-9 px-3 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                    </div>
                </div>

                <!-- Status Alur Redaksi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Perbarui Status Alur Produksi Menjadi:
                    </label>
                    <select name="status_produksi" id="bt_status_produksi"
                            class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                        <option value="tayang">3. Tayang (Live) — Konten sudah mengudara di portal/medsos</option>
                        <option value="selesai">4. Selesai (Archived) — Masa tayang selesai &amp; SPJ lengkap</option>
                        <option value="antrean_redaksi">2. Antrean Redaksi — Masih proses liputan/editing</option>
                        <option value="menunggu_bahan">1. Menunggu Bahan — Belum ada materi dari pemesan</option>
                    </select>
                </div>

                <!-- Catatan Redaksi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Catatan Khusus Redaksi / Keterangan Bukti Tayang
                    </label>
                    <textarea name="catatan_redaksi" id="bt_catatan_redaksi" rows="2"
                              placeholder="Catatan nomor arsip SPJ, jumlah tayangan/viewer, atau keterangan lainnya..."
                              class="w-full p-2.5 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs resize-none"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeModalBuktiTayang()" 
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="btnSimpanBuktiTayang" 
                            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-xs hover:shadow transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>Simpan Bukti Tayang</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Modal Bukti Tayang
        function openModalBuktiTayang(data) {
            document.getElementById('bt_transaksi_id').value = data.id;
            document.getElementById('formBuktiTayang').action = "/transaksionline/" + data.id + "/bukti-tayang";
            document.getElementById('bt_nama_instansi').innerText = data.instansi || '-';
            document.getElementById('bt_nofaktur').innerText = data.nofaktur || '-';
            document.getElementById('bt_url_berita').value = data.url_berita || '';
            document.getElementById('bt_url_medsos').value = data.url_medsos || '';
            document.getElementById('bt_tgl_realisasi').value = data.tgl_realisasi || '';
            document.getElementById('bt_catatan_redaksi').value = data.catatan_redaksi || '';
            document.getElementById('bt_file_bukti').value = '';

            const previewLink = document.getElementById('bt_preview_file_link');
            if (data.file_bukti) {
                previewLink.classList.remove('hidden');
                previewLink.querySelector('a').href = data.file_bukti;
            } else {
                previewLink.classList.add('hidden');
            }

            // Default status: if already tayang/selesai keep it, else default to 'tayang'
            const stSelect = document.getElementById('bt_status_produksi');
            if (data.status_produksi === 'selesai') {
                stSelect.value = 'selesai';
            } else {
                stSelect.value = 'tayang';
            }

            document.getElementById('modalBuktiTayang').classList.remove('hidden');
        }

        function closeModalBuktiTayang() {
            document.getElementById('modalBuktiTayang').classList.add('hidden');
        }

        // Quick update status alur produksi via AJAX
        async function updateStatusProduksiAjax(id, newStatus) {
            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await axios.post('/transaksionline/' + id + '/status-produksi', {
                    status_produksi: newStatus
                }, {
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                });

                if (res.data && res.data.status === 'success') {
                    if (window.AppAlert) {
                        AppAlert.toast({
                            title: 'Status Diperbarui',
                            message: res.data.message,
                            type: 'success'
                        });
                    }
                }
            } catch (err) {
                alert('Gagal memperbarui status produksi.');
                location.reload();
            }
        }

        async function deleteTransaksi(id, nofaktur) {
            AppAlert.confirmDelete({
                title: 'Hapus Transaksi',
                subtitle: 'Tindakan ini tidak dapat dibatalkan',
                target: nofaktur,
                confirmText: 'Ya, Hapus Transaksi'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').content;
                        await axios.post('/transaksionline/' + id, {
                            _method: 'DELETE',
                            _token: token
                        });
                        AppAlert.success({
                            title: 'Berhasil Dihapus',
                            subtitle: 'Data telah dihapus dari sistem',
                            message: 'Transaksi ' + nofaktur + ' berhasil dihapus.'
                        }).then(() => location.reload());
                    } catch (err) {
                        AppAlert.error({
                            title: 'Gagal Menghapus',
                            subtitle: 'Terjadi kesalahan sistem',
                            message: 'Terjadi kesalahan saat menghapus data transaksi.',
                            buttonText: 'Tutup'
                        });
                    }
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
