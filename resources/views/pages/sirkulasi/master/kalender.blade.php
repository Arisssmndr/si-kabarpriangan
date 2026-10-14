<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Master Kalender Terbit Koran</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-purple-50 text-purple-700 border border-purple-200">Perencanaan</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Jadwal terbit harian Harian Umum Kabar Priangan periode {{ $bulanIni->translatedFormat('F Y') }}.
                    </p>
                </div>
            </div>

            <div class="text-xs text-slate-600 bg-slate-50 px-3.5 py-2 rounded-lg border border-slate-200">
                <span>Edisi Terbit Bulanan: <strong class="text-slate-900">{{ $kalender->count() }} Hari Terbit</strong></span>
            </div>
        </div>

        <!-- Tabel Kalender -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Tanggal Terbit</th>
                            <th class="px-5 py-3 font-semibold">Hari</th>
                            <th class="px-5 py-3 font-semibold">Jenis Edisi</th>
                            <th class="px-5 py-3 font-semibold text-right">Target Oplah Cetak</th>
                            <th class="px-5 py-3 font-semibold text-center">Status Terbit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($kalender as $item)
                        <tr class="hover:bg-slate-50/80 transition {{ str_contains($item->edisi, 'Minggu') ? 'bg-amber-50/30' : '' }}">
                            <td class="px-5 py-3 font-bold text-slate-900">{{ $item->tgl_format }}</td>
                            <td class="px-5 py-3 font-semibold text-slate-700">{{ $item->hari }}</td>
                            <td class="px-5 py-3">
                                @if(str_contains($item->edisi, 'Minggu'))
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">Edisi Minggu (Khusus)</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-slate-100 text-slate-700 border border-slate-200">Edisi Reguler</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right font-extrabold text-slate-900">
                                {{ number_format($item->target_oplah, 0, ',', '.') }} eks
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $item->status }}
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
