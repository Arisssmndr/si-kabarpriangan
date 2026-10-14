<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Penarikan Penjualan Iklan Harian</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-purple-50 text-purple-700 border border-purple-200">Pull Data Iklan</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Tarik dan validasi penjualan iklan harian dari Divisi Iklan untuk mengakui <strong>Piutang Usaha</strong> dan meneruskan data ke <strong>Faktur Penagihan Keuangan</strong>.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('accounting.tarik.iklan.proses') }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-lg bg-purple-600 hover:bg-purple-700 text-white shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                    <span>Tarik & Validasi Semua Data</span>
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-medium flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter Tanggal -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.tarik.iklan') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Tanggal Transaksi Iklan</label>
                    <input type="date" name="tanggal" value="{{ $tanggalPilihan }}" class="text-xs rounded-lg border-slate-200 focus:border-purple-500 focus:ring-purple-500">
                </div>
                <button type="submit" class="py-2 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition">
                    Tampilkan Data
                </button>
            </form>
        </div>

        <!-- 2 KPI Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500">Siap Ditarik & Diakui Piutang</span>
                    <div class="text-xl font-extrabold text-amber-700 mt-1">Rp {{ number_format($totalBelumDitarik, 0, ',', '.') }}</div>
                    <span class="text-[11px] text-slate-400">Menunggu validasi akuntansi</span>
                </div>
                <span class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-bold text-xs">
                    Pending
                </span>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500">Sudah Ditarik & Terposting GL</span>
                    <div class="text-xl font-extrabold text-emerald-600 mt-1">Rp {{ number_format($totalSudahDitarik, 0, ',', '.') }}</div>
                    <span class="text-[11px] text-slate-400">Tersinkron ke faktur penagihan Keuangan</span>
                </div>
                <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center font-bold text-xs">
                    Sync
                </span>
            </div>
        </div>

        <!-- Tabel Transaksi Iklan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                    Daftar Order Iklan Harian dari Admin Iklan
                </h3>
                <span class="text-[11px] text-slate-500 font-medium">Periode: {{ \Carbon\Carbon::parse($tanggalPilihan)->translatedFormat('d F Y') }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3 font-semibold">No. Order & Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Nama Klien / Instansi</th>
                            <th class="px-4 py-3 font-semibold">Media & Spesifikasi</th>
                            <th class="px-4 py-3 font-semibold">Account Executive (AE)</th>
                            <th class="px-4 py-3 font-semibold text-right">Nilai Tagihan</th>
                            <th class="px-4 py-3 font-semibold text-center">Closing Iklan</th>
                            <th class="px-5 py-3 font-semibold text-center">Status Akuntansi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($iklanList as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3">
                                <div class="font-bold text-slate-900 font-mono">{{ $item->no_order }}</div>
                                <div class="text-[11px] text-slate-500">{{ $item->tanggal }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $item->klien }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-slate-800">{{ $item->jenis_media }}</span>
                                <div class="text-[11px] text-slate-500">{{ $item->ukuran }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $item->sales_ae }}
                            </td>
                            <td class="px-4 py-3 text-right font-extrabold text-slate-900 text-sm">
                                Rp {{ number_format($item->omset, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-sky-50 text-sky-700 border border-sky-200">
                                    {{ $item->status_admin_iklan }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($item->status_akuntansi === 'Belum Ditarik')
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                        {{ $item->status_akuntansi }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
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
