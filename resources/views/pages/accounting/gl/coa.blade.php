<x-app-layout>
    <div class="space-y-5">
        <!-- 1. Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Bagan Akun (Chart of Accounts / COA)</h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">SAK EMKM</span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Daftar master kode rekening pembukuan resmi Harian Umum Kabar Priangan.
                    </p>
                </div>
            </div>

            <div class="text-xs text-slate-600 bg-slate-50 px-3.5 py-2 rounded-xl border border-slate-200 shrink-0">
                <span>Total Pos Rekening: <strong class="text-slate-900 font-mono">{{ $coaList->count() }} Akun</strong></span>
            </div>
        </div>

        <!-- 2. Pure Tabel Bagan Akun -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[350px]">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="px-5 py-3 w-40">Kode Akun</th>
                            <th class="px-5 py-3">Nama Rekening Buku Besar</th>
                            <th class="px-4 py-3">Kategori Klasifikasi</th>
                            <th class="px-4 py-3 text-center w-28">Posisi Normal</th>
                            <th class="px-5 py-3 text-right w-44">Saldo Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-sm whitespace-nowrap">
                        @foreach($coaList as $c)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3 font-mono font-bold text-kp-blue-700 text-xs">{{ $c->kode }}</td>
                            <td class="px-5 py-3 font-semibold text-slate-900">{{ $c->nama }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-md 
                                    {{ str_contains($c->kategori, 'Aset') ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : '' }}
                                    {{ str_contains($c->kategori, 'Kewajiban') ? 'bg-slate-100 text-slate-700 border border-slate-200' : '' }}
                                    {{ str_contains($c->kategori, 'Ekuitas') ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : '' }}
                                    {{ str_contains($c->kategori, 'Pendapatan') ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : '' }}
                                    {{ str_contains($c->kategori, 'Beban') ? 'bg-slate-100 text-slate-700 border border-slate-300' : '' }}
                                ">
                                    {{ $c->kategori }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-medium text-slate-600">
                                {{ $c->saldo_normal }}
                            </td>
                            <td class="px-5 py-3 text-right font-bold text-slate-900 font-mono">
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