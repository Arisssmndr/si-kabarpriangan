<x-app-layout>
    <div class="space-y-5" x-data="{ showModal: false, activeLgn: { no: '', instansi: '', eks: 0, tagihan: 0, periode: '' } }">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Koran Langganan Instansi / Dinas
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Penerimaan setoran koran langganan bulanan dari instansi pemerintah dan dinas.
                    </p>
                </div>
            </div>

            <div class="shrink-0">
                <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                    Periode: {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
                </span>
            </div>
        </div>

        <!-- Flash Message Alert -->
        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
                <button type="button" @click="$el.parentElement.remove()" class="text-kp-blue-700 hover:text-kp-blue-900 font-bold ml-3 cursor-pointer">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-slate-100 border border-slate-300 text-slate-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('error') }}</span>
                <button type="button" @click="$el.parentElement.remove()" class="text-slate-600 hover:text-slate-900 font-bold ml-3 cursor-pointer">&times;</button>
            </div>
        @endif

        <!-- KPI Ringkasan Langganan -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Tagihan Langganan</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($langgananList->sum('total_tagihan'), 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Tagihan berjalan periode ini</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Instansi Terdaftar</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($langgananList->count()) }} <span class="text-xs font-sans font-normal text-slate-500">Dinas</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Pelanggan instansi aktif</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Oplah Langganan</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($langgananList->sum('jumlah_eks')) }} <span class="text-xs font-sans font-normal text-slate-500">Eks/Hari</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Distribusi harian instansi</span>
            </div>
        </div>

        <!-- Toolbar Pencarian & Filter -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('kasir.penerimaan.langganan') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Instansi</label>
                    <input type="text" name="search" value="{{ $search ?? '' }}" 
                           placeholder="Ketik nama dinas, instansi, atau no. pelanggan..." 
                           class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status Pembayaran</label>
                    <select name="status" onchange="this.form.submit()" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all" {{ ($statusFilter ?? '') === 'all' ? 'selected' : '' }}>Semua Status Pembayaran</option>
                        <option value="Belum Lunas" {{ ($statusFilter ?? '') === 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas / Jatuh Tempo</option>
                        <option value="Lunas" {{ ($statusFilter ?? '') === 'Lunas' ? 'selected' : '' }}>Sudah Lunas</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari
                    </button>
                    @if($search || ($statusFilter && $statusFilter !== 'all'))
                        <a href="{{ route('kasir.penerimaan.langganan') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Tagihan Langganan Koran Instansi -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Daftar Instansi &amp; Kwitansi Langganan</h3>
                    <p class="text-xs text-slate-500">Tagihan langganan koran bulanan dinas dan instansi</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ $langgananList->count() }} Instansi
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. Pelanggan</th>
                            <th class="py-3 px-3.5">Nama Dinas / Instansi</th>
                            <th class="py-3 px-3.5">Kontak &amp; Alamat</th>
                            <th class="py-3 px-3.5 text-center">Jumlah Eks.</th>
                            <th class="py-3 px-3.5 text-right">Total Tagihan</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                        @forelse($langgananList as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                {{ $loop->iteration }}
                            </td>
                            <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                {{ $item->no_pelanggan }}
                            </td>
                            <td class="py-3.5 px-3.5">
                                <div class="font-semibold text-slate-900">{{ $item->nama_instansi }}</div>
                                <span class="text-xs text-slate-500">{{ $item->periode }}</span>
                            </td>
                            <td class="py-3.5 px-3.5 text-slate-600">
                                <div>{{ $item->kontak }}</div>
                                <div class="text-xs text-slate-400 truncate max-w-xs">{{ $item->alamat }}</div>
                            </td>
                            <td class="py-3.5 px-3.5 text-center font-mono tabular-nums font-semibold text-slate-800">
                                {{ $item->jumlah_eks }} eks/hari
                            </td>
                            <td class="py-3.5 px-3.5 text-right font-mono tabular-nums font-semibold text-slate-900">
                                Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-3.5 text-center">
                                @if($item->status_pembayaran === 'Lunas')
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        Lunas
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        Tempo ({{ $item->jatuh_tempo }})
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3.5 text-center">
                                @if($item->status_pembayaran !== 'Lunas')
                                    <button type="button" 
                                            @click="activeLgn = { no: '{{ $item->no_pelanggan }}', instansi: '{{ addslashes($item->nama_instansi) }}', eks: {{ $item->jumlah_eks }}, tagihan: {{ $item->total_tagihan }}, periode: '{{ $item->periode }}' }; showModal = true"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                                        Terbitkan Kwitansi
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">Lunas</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-slate-500 text-sm">
                                Tidak ada data langganan instansi yang cocok.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form Pembayaran Langganan Dinas -->
        <div x-cloak x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-3.5 bg-white border-b border-slate-200 flex items-center justify-between shrink-0">
                    <div>
                        <h4 class="text-base font-bold text-slate-900">Kwitansi Koran Langganan</h4>
                        <p class="text-xs text-slate-500">Penerbitan kwitansi pembayaran langganan instansi</p>
                    </div>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-xl leading-none cursor-pointer">&times;</button>
                </div>

                <form method="POST" action="{{ route('kasir.penerimaan.langganan.store') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="no_pelanggan" :value="activeLgn.no">
                    <input type="hidden" name="nama_instansi" :value="activeLgn.instansi">

                    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 text-xs space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-slate-500">No. Pelanggan</span>
                                <span class="font-mono font-bold text-kp-blue-700" x-text="activeLgn.no"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Instansi / Dinas</span>
                                <span class="font-semibold text-slate-900" x-text="activeLgn.instansi"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Periode Tagihan</span>
                                <span class="font-semibold text-slate-700" x-text="activeLgn.periode"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Total Tagihan Bulanan</span>
                                <span class="font-mono font-bold text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeLgn.tagihan)"></span></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nominal Diterima Kasir (Rp)</label>
                            <input type="number" name="nominal_bayar" :value="activeLgn.tagihan" required min="1" 
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor SPK / Surat Pesanan Dinas</label>
                            <input type="text" name="no_spk_po" placeholder="Opsional (Contoh: 027/SPK-KORAN/SETDA/X/2026)" 
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Metode Pembayaran</label>
                            <select name="metode_pembayaran" class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                <option value="Transfer Bank BJB Kasda">Transfer Bank BJB (Kasda Pemda)</option>
                                <option value="Tunai Meja Kasir">Tunai Meja Kasir (Bendahara Dinas)</option>
                                <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                            </select>
                        </div>
                    </div>

                    <div class="px-5 py-3.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showModal = false" class="h-10 px-4 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl border border-slate-300 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition cursor-pointer">
                            Cetak Kwitansi Langganan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
