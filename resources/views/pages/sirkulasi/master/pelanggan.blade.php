<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pelanggan Koran Dinas & Loper</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-indigo-50 text-indigo-700 border border-indigo-200">Langganan</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Daftar langganan koran instansi pemerintah (SKPD Pemda), perbankan, BUMN, swasta, dan loper pengantar.
                    </p>
                </div>
            </div>

            <button type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>+ Daftarkan Pelanggan Baru</span>
            </button>
        </div>

        <!-- Tabel Pelanggan Dinas -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Kode & Nama Instansi / Pelanggan</th>
                            <th class="px-5 py-3 font-semibold">Kategori</th>
                            <th class="px-5 py-3 font-semibold">Loper Pengantar</th>
                            <th class="px-5 py-3 font-semibold text-right">Jumlah Koran</th>
                            <th class="px-5 py-3 font-semibold text-right">Tarif / Bulan</th>
                            <th class="px-5 py-3 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pelangganList as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3">
                                <div class="font-bold text-slate-900">{{ $p->nama }}</div>
                                <div class="text-[11px] text-slate-500">{{ $p->alamat }}</div>
                                <div class="text-[10px] font-mono text-indigo-700 mt-0.5">{{ $p->kode }}</div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $p->kategori }}
                                </span>
                            </td>
                            <td class="px-5 py-3 font-medium text-slate-800">
                                {{ $p->loper }}
                            </td>
                            <td class="px-5 py-3 text-right font-extrabold text-slate-900">
                                {{ $p->jumlah_eks }} eks
                            </td>
                            <td class="px-5 py-3 text-right font-bold text-slate-900">
                                Rp {{ number_format($p->tarif_bulanan, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $p->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
