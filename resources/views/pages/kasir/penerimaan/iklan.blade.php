<x-app-layout>
    <div class="space-y-6" x-data="{ showModal: false, selectedFaktur: null, bayarForm: { type: '', id: '', faktur: '', customer: '', sisa: 0, nominal: 0, metode: 'Transfer Bank' } }">

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

        <!-- Header Page Setoran Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Setoran & Pelunasan Faktur Iklan
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-teal-50 text-teal-700 border border-teal-200">
                            Badge: Faktur
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Penerimaan setoran kas & penerbitan Bukti Kas Masuk (BKM) dari piutang iklan tempo
                    </p>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('kasir.penerimaan.iklan') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-6 relative">
                    <input type="text" name="search" value="{{ $search ?? '' }}" 
                           placeholder="Cari No. Faktur atau Nama Pemasang / Klien..." 
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-800 placeholder-slate-400">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <div class="sm:col-span-4">
                    <select name="media" onchange="this.form.submit()" class="w-full py-2 px-3 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-800">
                        <option value="all" {{ ($mediaFilter ?? '') === 'all' ? 'selected' : '' }}>Semua Saluran Media</option>
                        <option value="koran" {{ ($mediaFilter ?? '') === 'koran' ? 'selected' : '' }}>Koran Cetak</option>
                        <option value="online" {{ ($mediaFilter ?? '') === 'online' ? 'selected' : '' }}>Media Online</option>
                        <option value="tv" {{ ($mediaFilter ?? '') === 'tv' ? 'selected' : '' }}>Priangan TV</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold text-xs py-2 px-3 rounded-lg transition text-center shadow-2xs">
                        Filter Data
                    </button>
                    @if($search || ($mediaFilter && $mediaFilter !== 'all'))
                        <a href="{{ route('kasir.penerimaan.iklan') }}" class="p-2 text-slate-500 hover:text-slate-700 border border-slate-300 rounded-lg" title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Faktur & Piutang Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                    Daftar Faktur Iklan & Status Piutang
                </h3>
                <span class="text-[11px] text-slate-500 font-medium">Total {{ $fakturList->count() }} Faktur Ditemukan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">No. Faktur</th>
                            <th class="px-4 py-3 font-semibold">Saluran Media</th>
                            <th class="px-4 py-3 font-semibold">Nama Pemasang / Klien</th>
                            <th class="px-4 py-3 font-semibold">Sales / AE</th>
                            <th class="px-4 py-3 font-semibold text-right">Total Tagihan</th>
                            <th class="px-4 py-3 font-semibold text-right">Sudah Dibayar</th>
                            <th class="px-4 py-3 font-semibold text-right">Sisa Piutang</th>
                            <th class="px-4 py-3 font-semibold text-center">Aksi / Bayar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($fakturList as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $item->faktur }}</td>
                            <td class="px-4 py-3">
                                @if($item->type === 'koran')
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-sky-50 text-sky-700 border border-sky-200">Koran</span>
                                @elseif($item->type === 'online')
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Online</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-purple-50 text-purple-700 border border-purple-200">TV</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $item->customer }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $item->sales }}</td>
                            <td class="px-4 py-3 text-right font-medium text-slate-700">Rp {{ number_format($item->totaltagihan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-bold text-emerald-600">Rp {{ number_format($item->jumlahbayar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-extrabold {{ $item->piutang > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                Rp {{ number_format($item->piutang, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($item->piutang > 0)
                                    <button type="button" 
                                            @click="bayarForm = { type: '{{ $item->type }}', id: {{ $item->id }}, faktur: '{{ $item->faktur }}', customer: '{{ addslashes($item->customer) }}', sisa: {{ $item->piutang }}, nominal: {{ $item->piutang }}, metode: 'Transfer Bank' }; showModal = true"
                                            class="px-2.5 py-1.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-[11px] rounded-lg transition shadow-2xs">
                                        Proses BKM
                                    </button>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Lunas (BKM Ready)
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-5 py-6 text-center text-slate-400">Tidak ada data faktur iklan yang ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form Input Setoran Kas / Terbitkan BKM -->
        <div x-cloak x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Terbitkan Bukti Kas Masuk (BKM)</h4>
                            <p class="text-[11px] text-slate-500">Input nominal setoran pembayaran faktur iklan</p>
                        </div>
                    </div>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <form method="POST" action="{{ route('kasir.penerimaan.iklan.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="type" :value="bayarForm.type">
                    <input type="hidden" name="id" :value="bayarForm.id">

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500">No. Faktur:</span>
                            <span class="font-mono font-bold text-slate-900" x-text="bayarForm.faktur"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Pemasang / Klien:</span>
                            <span class="font-bold text-slate-800" x-text="bayarForm.customer"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Sisa Piutang Saat Ini:</span>
                            <span class="font-bold text-rose-600">Rp <span x-text="new Intl.NumberFormat('id-ID').format(bayarForm.sisa)"></span></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Setoran / Dibayar (Rp)</label>
                        <input type="number" name="nominal_bayar" x-model="bayarForm.nominal" required min="1" :max="bayarForm.sisa" 
                               class="w-full text-sm font-mono font-bold py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pembayaran</label>
                        <select name="metode_pembayaran" x-model="bayarForm.metode" class="w-full text-xs py-2 px-3 rounded-lg border border-slate-300 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-slate-800">
                            <option value="Transfer Bank">Transfer Bank (BJB / Mandiri)</option>
                            <option value="Tunai Kasir">Tunai Meja Kasir</option>
                            <option value="QRIS / E-Wallet">QRIS / E-Wallet Resmi</option>
                            <option value="Cek / Giro">Cek / Bilyet Giro</option>
                        </select>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-xs rounded-lg transition shadow-2xs">
                            Cetak & Simpan BKM
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
