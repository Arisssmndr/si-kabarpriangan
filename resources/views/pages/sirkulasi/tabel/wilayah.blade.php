<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Tabel Master</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Wilayah Pemasaran (01 s.d. 08)</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Klasifikasi 8 zona pemasaran sirkulasi sesuai standar sistem DOS asli SIRK.EXE Kabar Priangan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                    Total: <strong class="text-slate-900">{{ $wilayahList->count() }} Wilayah</strong>
                </span>
                <span class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-amber-50 text-amber-800 border border-amber-200">
                    Oplah Rutin: <strong class="text-amber-900">{{ number_format($totalAlokasi, 0, ',', '.') }} Eks</strong>
                </span>
            </div>
        </div>

        <!-- Tabel 8 Wilayah Pemasaran -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Daftar Kode & Cakupan Wilayah Distribusi</h3>
                <span class="text-[11px] text-slate-500 font-medium">Acuan Pemetaan Agen & Laporan Bulanan</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-100/75 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4 w-20 text-center">Kode</th>
                            <th class="py-3 px-4">Nama Wilayah Pemasaran</th>
                            <th class="py-3 px-4">Deskripsi Cakupan Distribusi</th>
                            <th class="py-3 px-4 text-center">Tipe Alokasi</th>
                            <th class="py-3 px-4 text-center">Jumlah Agen</th>
                            <th class="py-3 px-4 text-right">Alokasi Default</th>
                            <th class="py-3 px-4 text-center">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($wilayahList as $w)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-block px-2.5 py-1 text-xs font-mono font-bold rounded-md bg-amber-100 text-amber-900 border border-amber-200">
                                        {{ $w->kode }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-bold text-slate-900 text-xs">{{ $w->nama }}</span>
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $w->keterangan }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($w->tipe === 'Komersil')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Komersil
                                        </span>
                                    @elseif($w->tipe === 'Non-Komersil (Gratis)')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                            Non-Komersil
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            Insidentil
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="font-bold text-slate-800">{{ $w->total_agen }}</span> titik
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <span class="font-bold text-slate-900">{{ number_format($w->alokasi_default, 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-slate-400">eks</span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a href="{{ route('sirkulasi.tabel.agen', ['wilayah' => $w->kode]) }}" 
                                       class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-600 hover:text-amber-800 hover:underline">
                                        <span>Lihat Agen</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2">
                <span>Standar 8 Wilayah Pemasaran (01 Tasikmalaya, 02 Garut, 03 Ciamis, 04 Banjar, 05 Sumedang, 06 Gratis, 07 Bandung, 08 Insidentil)</span>
                <span>Terhubung otomatis dengan Laporan Pemasaran & Rekap Penjualan</span>
            </div>
        </div>

    </div>
</x-app-layout>
