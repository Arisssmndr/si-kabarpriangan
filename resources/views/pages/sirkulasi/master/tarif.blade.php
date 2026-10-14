<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Tarif Harga Koran & Skema Jatah Gratis</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-emerald-50 text-emerald-700 border border-emerald-200">Komersial</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Struktur harga agen, eceran, batasan maksimal retur koran, dan alokasi jatah cuma-cuma (gratis).
                    </p>
                </div>
            </div>
        </div>

        <!-- 2 Kolom: Tarif vs Skema Gratis -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Tarif Koran -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Struktur Tarif Koran Cetak</h3>
                    <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">SK Direksi</span>
                </div>
                <div class="p-5 space-y-4 text-xs">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <div>
                            <div class="font-bold text-slate-800">Harga Eceran Resmi (HET)</div>
                            <div class="text-[11px] text-slate-500">Harga jual ke pembaca eceran</div>
                        </div>
                        <div class="text-base font-extrabold text-slate-900">
                            Rp {{ number_format($skemaTarif['harga_eceran_resmi'], 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">/ eks</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-lg bg-sky-50/50 border border-sky-200">
                        <div>
                            <div class="font-bold text-sky-900">Tarif Agen Besar (11)</div>
                            <div class="text-[11px] text-sky-700">Diskon 30% dari harga eceran resmi</div>
                        </div>
                        <div class="text-right">
                            <div class="text-base font-extrabold text-sky-900">
                                Rp {{ number_format($skemaTarif['harga_agen_besar'], 0, ',', '.') }}
                            </div>
                            <div class="text-[10px] text-sky-600">Maks Retur: {{ $skemaTarif['maks_retur_agen_besar'] }}%</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-lg bg-amber-50/50 border border-amber-200">
                        <div>
                            <div class="font-bold text-amber-900">Tarif Sub-Agen (11-9)</div>
                            <div class="text-[11px] text-amber-700">Diskon 20% dari harga eceran resmi</div>
                        </div>
                        <div class="text-right">
                            <div class="text-base font-extrabold text-amber-900">
                                Rp {{ number_format($skemaTarif['harga_sub_agen'], 0, ',', '.') }}
                            </div>
                            <div class="text-[10px] text-amber-600">Maks Retur: {{ $skemaTarif['maks_retur_sub_agen'] }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Skema Gratis -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Alokasi Jatah Gratis (Cuma-Cuma)</h3>
                    <span class="text-[11px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200">Legal & Kompensasi</span>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($skemaGratis as $g)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/80 transition">
                        <div>
                            <div class="font-bold text-slate-900">{{ $g['kategori'] }}</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $g['keterangan'] }}</div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-extrabold text-purple-700">{{ $g['kuota'] }}</span>
                            <span class="text-[11px] text-slate-500">eks/hari</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
