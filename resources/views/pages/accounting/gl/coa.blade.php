<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-kp-blue-50 text-kp-blue-600 border border-kp-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Bagan Akun (Chart of Accounts / COA)</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">SAK EMKM</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Daftar master kode rekening pembukuan resmi Harian Umum Kabar Priangan (Aset, Kewajiban, Ekuitas, Pendapatan, dan Beban).
                    </p>
                </div>
            </div>

            <div class="text-xs text-slate-600 bg-slate-50 px-3.5 py-2 rounded-lg border border-slate-200">
                <span>Total Pos Rekening: <strong class="text-slate-900">{{ $coaList->count() }} Akun</strong></span>
            </div>
        </div>

        <!-- Tabel Bagan Akun -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-[#0A72AC] text-white text-[11px] uppercase tracking-wider border-b-2 border-[#FFB900]">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Kode Akun</th>
                            <th class="px-5 py-3 font-semibold">Nama Rekening Buku Besar</th>
                            <th class="px-4 py-3 font-semibold">Kategori Klasifikasi</th>
                            <th class="px-4 py-3 font-semibold text-center">Posisi Normal</th>
                            <th class="px-5 py-3 font-semibold text-right">Saldo Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($coaList as $c)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3 font-mono font-bold text-kp-blue-700">{{ $c->kode }}</td>
                            <td class="px-5 py-3 font-bold text-slate-900">{{ $c->nama }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded 
                                    {{ str_contains($c->kategori, 'Aset') ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : '' }}
                                    {{ str_contains($c->kategori, 'Kewajiban') ? 'bg-slate-100 text-slate-700 border border-slate-200' : '' }}
                                    {{ str_contains($c->kategori, 'Ekuitas') ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : '' }}
                                    {{ str_contains($c->kategori, 'Pendapatan') ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : '' }}
                                    {{ str_contains($c->kategori, 'Beban') ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                ">
                                    {{ $c->kategori }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-semibold text-slate-600">
                                {{ $c->saldo_normal }}
                            </td>
                            <td class="px-5 py-3 text-right font-extrabold text-slate-900">
                                Rp {{ number_format($c->saldo, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
