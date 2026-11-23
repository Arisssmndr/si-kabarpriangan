<x-app-layout>
    <div class="space-y-5">
        <!-- 1. Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Penarikan Penjualan Iklan Harian</h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">Pull Data Iklan</span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Tarik dan validasi penjualan iklan harian dari Divisi Iklan untuk pembentukan piutang di GL.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('accounting.tarik.iklan.proses') }}" class="inline shrink-0">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 h-10 px-4 text-xs font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                    <span>Tarik &amp; Validasi Semua Data</span>
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- 2. KPI Summary (Report Info) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Siap Ditarik &amp; Diakui Piutang</span>
                    <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalBelumDitarik, 0, ',', '.') }}</div>
                    <span class="text-xs text-slate-400 mt-0.5 block">Menunggu validasi akuntansi</span>
                </div>
                <span class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 border border-slate-300 flex items-center justify-center font-bold text-xs shrink-0">
                    Pending
                </span>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sudah Ditarik &amp; Terposting GL</span>
                    <div class="text-xl font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($totalSudahDitarik, 0, ',', '.') }}</div>
                    <span class="text-xs text-slate-400 mt-0.5 block">Tersinkron ke faktur penagihan Keuangan</span>
                </div>
                <span class="w-10 h-10 rounded-xl bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 flex items-center justify-center font-bold text-xs shrink-0">
                    Sync
                </span>
            </div>
        </div>

        <!-- 3. Filter Tanggal Mandiri -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <form method="GET" action="{{ route('accounting.tarik.iklan') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Transaksi Iklan</label>
                    <input type="date" 
                           name="tanggal" 
                           value="{{ $tanggalPilihan }}" 
                           class="h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Tampilkan Data
                    </button>
                    <a href="{{ route('accounting.tarik.iklan') }}" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- 4. Pure Tabel Transaksi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[350px]">
                <table class="w-full text-sm text-left border-collapse min-w-[1050px]">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="px-4 py-3 whitespace-nowrap w-36">No. Order</th>
                            <th class="px-3.5 py-3 whitespace-nowrap w-28">Tanggal</th>
                            <th class="px-4 py-3 min-w-[180px]">Nama Klien / Instansi</th>
                            <th class="px-3.5 py-3 whitespace-nowrap w-28">Media</th>
                            <th class="px-4 py-3 min-w-[160px]">Ukuran / Spesifikasi</th>
                            <th class="px-4 py-3 whitespace-nowrap min-w-[140px]">Account Executive</th>
                            <th class="px-4 py-3 font-semibold text-right whitespace-nowrap w-36">Nilai Tagihan</th>
                            <th class="px-4 py-3 font-semibold text-center whitespace-nowrap w-28">Closing Iklan</th>
                            <th class="px-4 py-3 font-semibold text-center whitespace-nowrap w-36">Status Akuntansi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-sm whitespace-nowrap">
                        @foreach($iklanList as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-bold text-slate-900 font-mono whitespace-nowrap text-xs">
                                {{ $item->no_order }}
                            </td>
                            <td class="px-3.5 py-3 font-mono text-slate-600 whitespace-nowrap text-xs">
                                {{ $item->tanggal }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-900">
                                {{ $item->klien }}
                            </td>
                            <td class="px-3.5 py-3 whitespace-nowrap">
                                <span class="font-semibold text-slate-800">{{ $item->jenis_media }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600 text-xs">
                                {{ $item->ukuran ?: '-' }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $item->sales_ae }}
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-slate-900 font-mono">
                                Rp {{ number_format($item->omset, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                    {{ $item->status_admin_iklan }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($item->status_akuntansi === 'Belum Ditarik')
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-md bg-slate-100 text-slate-700 border border-slate-300">
                                        {{ $item->status_akuntansi }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        {{ $item->status_akuntansi }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>