<x-app-layout>
    <div class="space-y-6" x-data="{ showModal: false, activeLgn: { no: '', instansi: '', eks: 0, tagihan: 0, periode: '' } }">

        <!-- Flash Message Alert -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-semibold text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- Header Page Koran Langganan Dinas -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5m-15 10.5V10.5m18 0a48.667 48.667 0 00-10.5-.75A48.667 48.667 0 003 10.5" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Koran Langganan Instansi / Dinas
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-purple-50 text-purple-700 border border-purple-200">
                            Badge: Dinas
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Penerimaan setoran koran langganan bulanan dari kantor dinas Pemda, Bapenda, Setwan DPRD, Polres, dan instansi vertikal
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                    Periode Berjalan: {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
                </span>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('kasir.penerimaan.langganan') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-7 relative">
                    <input type="text" name="search" value="{{ $search ?? '' }}" 
                           placeholder="Cari Nama Dinas / Instansi / No. Pelanggan..." 
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-purple-500 focus:border-purple-500 text-slate-800 placeholder-slate-400">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <div class="sm:col-span-3">
                    <select name="status" onchange="this.form.submit()" class="w-full py-2 px-3 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-purple-500 focus:border-purple-500 text-slate-800">
                        <option value="all" {{ ($statusFilter ?? '') === 'all' ? 'selected' : '' }}>Semua Status Pembayaran</option>
                        <option value="Belum Lunas" {{ ($statusFilter ?? '') === 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas / Jatuh Tempo</option>
                        <option value="Lunas" {{ ($statusFilter ?? '') === 'Lunas' ? 'selected' : '' }}>Sudah Lunas</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs py-2 px-3 rounded-lg transition text-center shadow-2xs">
                        Filter
                    </button>
                    @if($search || ($statusFilter && $statusFilter !== 'all'))
                        <a href="{{ route('kasir.penerimaan.langganan') }}" class="p-2 text-slate-500 hover:text-slate-700 border border-slate-300 rounded-lg" title="Reset">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Tagihan Langganan Koran Instansi -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                    Daftar Instansi & Kwitansi Koran Langganan
                </h3>
                <span class="text-[11px] text-slate-500 font-medium">Total Tagihan: Rp {{ number_format($langgananList->sum('total_tagihan'), 0, ',', '.') }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">No. Pelanggan</th>
                            <th class="px-4 py-3 font-semibold">Nama Dinas / Instansi</th>
                            <th class="px-4 py-3 font-semibold">Kontak & Alamat</th>
                            <th class="px-4 py-3 font-semibold text-center">Jumlah Eks.</th>
                            <th class="px-4 py-3 font-semibold text-right">Total Tagihan (Rp)</th>
                            <th class="px-4 py-3 font-semibold text-center">Status</th>
                            <th class="px-4 py-3 font-semibold text-center">Aksi Bayar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($langgananList as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $item->no_pelanggan }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $item->nama_instansi }}</div>
                                <span class="text-[10px] text-purple-600 font-semibold">{{ $item->periode }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <div>{{ $item->kontak }}</div>
                                <div class="text-[10px] text-slate-400 truncate max-w-xs">{{ $item->alamat }}</div>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-slate-800">{{ $item->jumlah_eks }} eks/hari</td>
                            <td class="px-4 py-3 text-right font-extrabold text-slate-900">Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($item->status_pembayaran === 'Lunas')
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">Lunas</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-rose-50 text-rose-700 border border-rose-200">Tempo ({{ $item->jatuh_tempo }})</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($item->status_pembayaran !== 'Lunas')
                                    <button type="button" 
                                            @click="activeLgn = { no: '{{ $item->no_pelanggan }}', instansi: '{{ addslashes($item->nama_instansi) }}', eks: {{ $item->jumlah_eks }}, tagihan: {{ $item->total_tagihan }}, periode: '{{ $item->periode }}' }; showModal = true"
                                            class="px-2.5 py-1.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-[11px] rounded-lg transition shadow-2xs">
                                        Terbitkan Kwitansi
                                    </button>
                                @else
                                    <span class="text-[11px] text-slate-400 font-medium">Sudah Lunas</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-6 text-center text-slate-400">Tidak ada data langganan instansi yang cocok.</td>
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
                        <span class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center shrink-0">
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

                    <div class="flex-1 overflow-y-auto px-5 py-3.5 space-y-3">
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Instansi / Dinas:</span>
                                <span class="font-bold text-slate-900" x-text="activeLgn.instansi"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Periode Tagihan:</span>
                                <span class="font-semibold text-purple-700" x-text="activeLgn.periode"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Total Tagihan Bulanan:</span>
                                <span class="font-extrabold text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeLgn.tagihan)"></span></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Yang Diterima Kasir (Rp)</label>
                            <input type="number" name="nominal_bayar" :value="activeLgn.tagihan" required min="1" 
                                   class="w-full text-sm font-mono font-bold py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-purple-500 focus:border-purple-500 text-slate-900">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor SPK / Surat Pesanan Dinas (Opsional)</label>
                            <input type="text" name="no_spk_po" placeholder="Contoh: 027/SPK-KORAN/SETDA/X/2026" 
                                   class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-purple-500 focus:border-purple-500 text-slate-900">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pembayaran</label>
                            <select name="metode_pembayaran" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-purple-500 focus:border-purple-500 text-slate-800">
                                <option value="Transfer Bank BJB Kasda">Transfer Bank BJB (Kasda Pemda)</option>
                                <option value="Tunai Meja Kasir">Tunai Meja Kasir (Bendahara Dinas)</option>
                                <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                            </select>
                        </div>
                    </div>

                    <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-xl transition shadow-2xs">
                            Cetak Kwitansi Langganan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
