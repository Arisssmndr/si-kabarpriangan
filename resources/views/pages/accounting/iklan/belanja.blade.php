<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Riwayat Belanja Pemasang
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Kartu Iklan
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Pencarian akumulasi transaksi belanja dan status piutang per pemasang.
                    </p>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.iklan.belanja') }}" class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Pemasang / Klien</label>
                    <input type="text" 
                           name="q" 
                           value="{{ $query }}" 
                           placeholder="Ketik nama klien, biro, atau instansi..." 
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Cari Riwayat
                    </button>
                    @if($query)
                        <a href="{{ route('accounting.iklan.belanja') }}" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Riwayat Transaksi -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] font-bold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">No. Faktur</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Pemasang</th>
                            <th class="py-3 px-3.5">Jenis Iklan</th>
                            <th class="py-3 px-3.5 text-right">Nilai Tagihan</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($riwayat as $key => $r)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-xs text-slate-400">{{ $key + 1 }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900">{{ $r->no_faktur }}</td>
                                <td class="py-3 px-3.5">{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}</td>
                                <td class="py-3 px-3.5">{{ $r->pemasang?->nama_pemasang ?? $r->nama_pemohon }}</td>
                                <td class="py-3 px-3.5">{{ $r->kategori_form ?: 'Koran' }}</td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($r->total_akhir ?? $r->subtotal ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md {{ ($r->status_pembayaran === 'lunas') ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ ucfirst($r->status_pembayaran ?: 'piutang') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                    {{ $query ? 'Tidak ditemukan riwayat untuk pencarian tersebut.' : 'Silakan cari nama pemasang untuk menampilkan riwayat belanja.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>

