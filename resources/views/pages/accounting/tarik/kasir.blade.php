<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-kp-blue-50 text-kp-blue-600 border border-kp-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Rekonsiliasi Kasir & Mutasi Bank</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">Kas & Bank</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pencocokan penerimaan kas dari kasir harian dengan mutasi rekening koran bank (BCA, BJB, Kas Fisik).
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('accounting.tarik.kasir.proses') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white shadow-xs transition">
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

        <!-- Filter Tanggal -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.tarik.kasir') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Tanggal Kas</label>
                    <input type="date" name="tanggal" value="{{ $tanggalPilihan }}" class="text-xs rounded-lg border-slate-200 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                </div>
                <button type="submit" class="py-2 px-3 text-xs font-semibold rounded-lg bg-kp-blue-600 text-white hover:bg-kp-blue-700 transition">
                    Filter Kasir
                </button>
            </form>
        </div>

        <!-- Tabel Rekonsiliasi -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-[#0A72AC] text-white text-[11px] uppercase tracking-wider border-b-2 border-[#FFB900]">
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
                    <tbody class="divide-y divide-slate-100">
                        @foreach($kasirList as $k)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3 font-bold text-slate-900 font-mono">
                                {{ $k->no_bukti }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 font-mono text-[11px] whitespace-nowrap">
                                {{ $k->waktu }}
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-800">
                                {{ $k->pembayar }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $k->kategori }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-700">
                                {{ $k->cara_bayar }}
                            </td>
                            <td class="px-4 py-3 text-right font-extrabold text-slate-900 text-sm">
                                Rp {{ number_format($k->nominal, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if(str_contains($k->status_rekonsiliasi, 'Cocok'))
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        {{ $k->status_rekonsiliasi }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-300">
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
