<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 border border-sky-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Wilayah & Rute Jurusan Armada</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-sky-50 text-sky-700 border border-sky-200">Logistik</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Alur trayek distribusi mobil pengangkut koran subuh (02:00 - 03:00 WIB) se-Priangan Timur & sekitarnya.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 text-xs bg-slate-50 px-3.5 py-2 rounded-lg border border-slate-200">
                <div class="text-slate-600">Total Oplah Armada: <strong class="text-slate-900">{{ number_format($totalOplahArmada, 0, ',', '.') }} eks</strong></div>
                <div class="text-slate-300">|</div>
                <div class="text-slate-600">Total Agen: <strong class="text-slate-900">{{ $totalAgenTerlayani }} Titik</strong></div>
            </div>
        </div>

        <!-- Grid Cards Rute -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($ruteList as $rute)
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs hover:border-sky-300 transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 text-xs font-mono font-bold rounded bg-slate-100 text-slate-700 border border-slate-200">{{ $rute->kode }}</span>
                            <span class="text-xs font-bold text-slate-900">{{ $rute->nama_rute }}</span>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                    </div>

                    <div class="mt-3 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Driver / Sopir Armada:</span>
                            <span class="font-bold text-slate-900">{{ $rute->driver }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>No. Kendaraan (Plat):</span>
                            <span class="font-mono font-semibold text-slate-800 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">{{ $rute->kendaraan }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Jam Berangkat Subuh:</span>
                            <span class="font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">{{ $rute->jam_berangkat }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Alokasi Oplah:</span>
                            <span class="font-extrabold text-slate-900">{{ number_format($rute->alokasi_oplah, 0, ',', '.') }} eks</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Agen Terlayani:</span>
                            <span class="font-bold text-sky-700">{{ $rute->total_agen }} Titik Agen</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-400 text-[11px]">Kendaraan Box Operasional</span>
                    <a href="{{ route('sirkulasi.logistik.surat-jalan') }}?rute={{ $rute->kode }}" class="text-sky-600 hover:text-sky-800 font-semibold text-[11px] flex items-center gap-1">
                        Cetak Surat Jalan &rarr;
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
