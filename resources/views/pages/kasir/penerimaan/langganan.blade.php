<x-app-layout>
    <div class="space-y-6" x-data="{ showModal: false, activeLgn: { no: '', instansi: '', eks: 0, tagihan: 0, periode: '' } }">

        <!-- Flash Message Alert -->
        @if(session('success'))
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-slate-400 hover:text-slate-700 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- Tier 1: Header Standar Divisi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Logo Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                            Koran Langganan Instansi / Dinas
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-sky-50 text-[#0E527B] border border-sky-200">
                            Langganan Instansi
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Penerimaan setoran koran langganan bulanan dari kantor dinas Pemda, Bapenda, Setwan DPRD, Polres, dan instansi vertikal.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <span class="text-xs font-semibold text-slate-700 bg-slate-50 px-3.5 py-2 rounded-xl border border-slate-200">
                    Periode: {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
                </span>
            </div>
        </div>

        <!-- Tier 2: KPI Ribbon (Palet 60-30-10) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Instansi Berlangganan</span>
                <p class="mt-2 text-2xl font-bold font-mono text-slate-900">
                    {{ count($langgananList) }} <span class="text-xs font-normal text-slate-400">Instansi</span>
                </p>
                <span class="mt-1 text-[11px] text-slate-400 block">Daftar pelanggan instansi dinas aktif</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Tagihan Berjalan</span>
                <p class="mt-2 text-2xl font-bold font-mono text-kp-blue-700">
                    Rp {{ number_format($langgananList->sum('total_tagihan'), 0, ',', '.') }}
                </p>
                <span class="mt-1 text-[11px] text-slate-400 block">Akumulasi tagihan bulan {{ \Carbon\Carbon::now()->translatedFormat('F') }}</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Eksemplar Harian</span>
                <p class="mt-2 text-2xl font-bold font-mono text-slate-900">
                    {{ number_format($langgananList->sum('jumlah_eks'), 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">eks/hari</span>
                </p>
                <span class="mt-1 text-[11px] text-slate-400 block">Distribusi koran dinas harian</span>
            </div>
        </div>

        <!-- Tier 3: Filter Bar Terpisah -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('kasir.penerimaan.langganan') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-6">
                    <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Pencarian Data</label>
                    <input type="text" name="search" value="{{ $search ?? '' }}" 
                           placeholder="Cari Nama Dinas / Instansi / No. Pelanggan..." 
                           class="h-11 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600">
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Status Pembayaran</label>
                    <select name="status" onchange="this.form.submit()" class="h-11 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600">
                        <option value="all" {{ ($statusFilter ?? '') === 'all' ? 'selected' : '' }}>Semua Status Pembayaran</option>
                        <option value="Belum Lunas" {{ ($statusFilter ?? '') === 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas / Jatuh Tempo</option>
                        <option value="Lunas" {{ ($statusFilter ?? '') === 'Lunas' ? 'selected' : '' }}>Sudah Lunas</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="h-11 w-full bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-xs cursor-pointer">
                        Filter
                    </button>
                    @if($search || ($statusFilter && $statusFilter !== 'all'))
                        <a href="{{ route('kasir.penerimaan.langganan') }}" class="h-11 px-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition flex items-center justify-center" title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tier 4: Pure Data Table (Tanpa Banner Strip) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-emerald-700 border-b border-emerald-800 text-white font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-4">No. Pelanggan</th>
                            <th class="py-3 px-4">Nama Dinas / Instansi</th>
                            <th class="py-3 px-4">Kontak & Alamat</th>
                            <th class="py-3 px-4 text-center">Jumlah Eks.</th>
                            <th class="py-3 px-4 text-right">Total Tagihan (Rp)</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @forelse($langgananList as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900 whitespace-nowrap">{{ $item->no_pelanggan }}</td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-900">{{ $item->nama_instansi }}</div>
                                <span class="text-xs text-kp-blue-700 font-medium">{{ $item->periode }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                <div>{{ $item->kontak }}</div>
                                <div class="text-xs text-slate-400 truncate max-w-xs">{{ $item->alamat }}</div>
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-800 whitespace-nowrap">{{ $item->jumlah_eks }} eks/hari</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($item->status_pembayaran === 'Lunas')
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full bg-sky-50 text-[#0E527B] border border-sky-200">
                                        Lunas
                                    </span>
                                @else
                                    @php
                                        $tempoFormatted = '-';
                                        try {
                                            $tempoFormatted = \Carbon\Carbon::parse($item->jatuh_tempo)->format('d/m/Y');
                                        } catch (\Exception $e) {
                                            $tempoFormatted = $item->jatuh_tempo;
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-700 border border-slate-300" title="Jatuh tempo: {{ $item->jatuh_tempo }}">
                                        Tempo: {{ $tempoFormatted }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($item->status_pembayaran !== 'Lunas')
                                    <button type="button" 
                                            @click="activeLgn = { no: '{{ $item->no_pelanggan }}', instansi: '{{ addslashes($item->nama_instansi) }}', eks: {{ $item->jumlah_eks }}, tagihan: {{ $item->total_tagihan }}, periode: '{{ $item->periode }}' }; showModal = true"
                                            class="h-9 px-3.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs rounded-xl transition shadow-xs cursor-pointer">
                                        Terbitkan Kwitansi
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">Sudah Lunas</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">
                                Tidak ada data langganan instansi yang cocok.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form Pembayaran Langganan Dinas -->
        <div x-cloak x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showModal = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-xl border border-slate-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between shrink-0 bg-slate-50/50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-sky-50 text-[#0E527B] border border-sky-200 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Kwitansi Penerimaan Koran Langganan</h4>
                            <p class="text-[11px] text-slate-500">Penerbitan kwitansi pembayaran langganan instansi pemerintah</p>
                        </div>
                    </div>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <form method="POST" action="{{ route('kasir.penerimaan.langganan.store') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="no_pelanggan" :value="activeLgn.no">
                    <input type="hidden" name="nama_instansi" :value="activeLgn.instansi">

                    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 text-xs space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Instansi / Dinas:</span>
                                <span class="font-bold text-slate-900" x-text="activeLgn.instansi"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Periode Tagihan:</span>
                                <span class="font-semibold text-kp-blue-700" x-text="activeLgn.periode"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Total Tagihan Bulanan:</span>
                                <span class="font-extrabold text-slate-900 font-mono">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeLgn.tagihan)"></span></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Yang Diterima Kasir (Rp)</label>
                            <input type="number" name="nominal_bayar" :value="activeLgn.tagihan" required min="1" 
                                   class="h-10 w-full px-3.5 bg-white border border-slate-300 rounded-xl font-mono font-bold text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor SPK / Surat Pesanan Dinas (Opsional)</label>
                            <input type="text" name="no_spk_po" placeholder="Contoh: 027/SPK-KORAN/SETDA/X/2026" 
                                   class="h-10 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Pembayaran</label>
                            <select name="metode_pembayaran" class="h-10 w-full px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                                <option value="Transfer Bank BJB Kasda">Transfer Bank BJB (Kasda Pemda)</option>
                                <option value="Tunai Meja Kasir">Tunai Meja Kasir (Bendahara Dinas)</option>
                                <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                            </select>
                        </div>
                    </div>

                    <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-semibold text-xs rounded-xl transition shadow-xs cursor-pointer">
                            Cetak Kwitansi Langganan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>