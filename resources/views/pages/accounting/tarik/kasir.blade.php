<x-app-layout>
    <div class="space-y-5">
        <!-- 1. Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Rekonsiliasi Kasir &amp; Mutasi Bank</h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">Kas &amp; Bank</span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Pencocokan penerimaan kas dari kasir harian dengan mutasi rekening koran bank.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('accounting.tarik.kasir.proses') }}" class="shrink-0">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 h-10 px-4 text-xs font-bold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>Posting Hasil Rekonsiliasi Kas</span>
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- 2. Filter Tanggal Mandiri -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <form method="GET" action="{{ route('accounting.tarik.kasir') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Kas</label>
                    <input type="date" 
                           name="tanggal" 
                           value="{{ $tanggalPilihan }}" 
                           class="h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Filter Kasir
                    </button>
                    <a href="{{ route('accounting.tarik.kasir') }}" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- 3. Pure Tabel Rekonsiliasi -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[350px]">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="px-5 py-3 font-semibold">No. Bukti Kas Masuk</th>
                            <th class="px-4 py-3 font-semibold">Waktu / Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Pembayar / Klien</th>
                            <th class="px-4 py-3 font-semibold">Kategori Penerimaan</th>
                            <th class="px-4 py-3 font-semibold">Metode Pembayaran</th>
                            <th class="px-4 py-3 font-semibold text-right">Nominal Riil</th>
                            <th class="px-5 py-3 font-semibold text-center">Status Rekonsiliasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-sm whitespace-nowrap">
                        @foreach($kasirList as $k)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3 font-bold text-slate-900 font-mono text-xs">
                                {{ $k->no_bukti }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 font-mono text-xs whitespace-nowrap">
                                {{ $k->waktu }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-900">
                                {{ $k->pembayar }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $k->kategori }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-700">
                                {{ $k->cara_bayar }}
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-slate-900 font-mono">
                                Rp {{ number_format($k->nominal, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if(str_contains($k->status_rekonsiliasi, 'Cocok'))
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        {{ $k->status_rekonsiliasi }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-md bg-slate-100 text-slate-700 border border-slate-300">
                                        {{ $k->status_rekonsiliasi }}
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