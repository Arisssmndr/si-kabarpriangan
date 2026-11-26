<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-kp-blue-50 text-kp-blue-600 border border-kp-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-kp-blue-600 uppercase tracking-wider">Divisi Sirkulasi</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Pelanggan Koran Dinas & Loper</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Daftar langganan koran instansi pemerintah, perbankan, BUMN, swasta, dan loper pengantar.
                    </p>
                </div>
            </div>

            <button type="button" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>+ Daftarkan Pelanggan</span>
            </button>
        </div>

        <!-- Tabel Pelanggan Dinas (Kolom Terpisah Lengkap) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left min-w-[1050px]">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] uppercase text-[10px] tracking-wider font-bold">
                        <tr>
                            <th class="px-3.5 py-3 whitespace-nowrap w-24">Kode</th>
                            <th class="px-4 py-3 min-w-[200px]">Nama Instansi / Pelanggan</th>
                            <th class="px-4 py-3 min-w-[220px]">Alamat</th>
                            <th class="px-3.5 py-3 whitespace-nowrap w-32">Kategori</th>
                            <th class="px-4 py-3 whitespace-nowrap min-w-[150px]">Loper Pengantar</th>
                            <th class="px-3.5 py-3 text-right whitespace-nowrap w-28">Jumlah Koran</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap w-32">Tarif / Bulan</th>
                            <th class="px-3.5 py-3 text-center whitespace-nowrap w-24">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($pelangganList as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Kode (Kolom Terpisah) -->
                            <td class="px-3.5 py-3 whitespace-nowrap">
                                <span class="inline-block px-2 py-0.5 rounded font-mono font-bold text-slate-900 bg-slate-100 text-xs border border-slate-200">
                                    {{ $p->kode }}
                                </span>
                            </td>

                            <!-- Nama Instansi / Pelanggan (Kolom Terpisah) -->
                            <td class="px-4 py-3 font-bold text-slate-900 text-xs">
                                {{ $p->nama }}
                            </td>

                            <!-- Alamat (Kolom Terpisah) -->
                            <td class="px-4 py-3 text-slate-600 text-xs leading-relaxed max-w-xs truncate" title="{{ $p->alamat }}">
                                {{ $p->alamat }}
                            </td>

                            <!-- Kategori -->
                            <td class="px-3.5 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $p->kategori }}
                                </span>
                            </td>

                            <!-- Loper Pengantar -->
                            <td class="px-4 py-3 text-slate-800 text-xs whitespace-nowrap">
                                {{ $p->loper }}
                            </td>

                            <!-- Jumlah Koran -->
                            <td class="px-3.5 py-3 text-right font-mono font-bold text-slate-900 text-xs whitespace-nowrap">
                                {{ $p->jumlah_eks }} eks
                            </td>

                            <!-- Tarif / Bulan -->
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900 text-xs whitespace-nowrap">
                                Rp {{ number_format($p->tarif_bulanan, 0, ',', '.') }}
                            </td>

                            <!-- Status -->
                            <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
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
