<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 border border-sky-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-sky-600 uppercase tracking-wider">Operasional Jatah</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Copy Data Penjualan (SIRK.EXE)</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Salin pola alokasi antar-tanggal kerja (hari kemarin / hari sama minggu lalu) ke target edisi kerja berikutnya secara instan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('sirkulasi.jatah.edit') }}" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition-colors">
                    &larr; Kembali ke Edit Data Penjualan
                </a>
            </div>
        </div>

        <div class="max-w-2xl bg-white border border-slate-200 rounded-xl p-6 shadow-xs">
            <form method="POST" action="{{ route('sirkulasi.jatah.copy.process') }}" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Sumber (Pola Asal)</label>
                        <input type="date" name="sumber_tanggal" value="{{ $today->toDateString() }}" 
                               class="w-full text-xs rounded-lg border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                        <p class="text-[11px] text-slate-500 mt-1">Default: Hari ini ({{ $today->translatedFormat('d F Y') }})</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Target Distribusi</label>
                        <input type="date" name="target_tanggal" value="{{ $tomorrow->toDateString() }}" 
                               class="w-full text-xs rounded-lg border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                        <p class="text-[11px] text-slate-500 mt-1">Default: Edisi Besok ({{ $tomorrow->translatedFormat('d F Y') }})</p>
                    </div>
                </div>

                <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-xs">
                    <div class="font-bold mb-1">Ketentuan Salin Pola Jatah:</div>
                    Pola jatah Langganan, Konsinyasi/Eceran (Rp3.000), Jatah Gratis, dan Insidentil seluruh agen akan disalin secara utuh. Setelah disalin, Anda tetap dapat melakukan penyesuaian pada menu <strong>Edit Data Penjualan</strong> sebelum pengiriman armada subuh.
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                    <a href="{{ route('sirkulasi.jatah.edit') }}" class="px-4 py-2 text-xs font-medium rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 text-xs font-bold rounded-lg bg-amber-600 hover:bg-amber-700 text-white shadow-xs transition cursor-pointer">
                        Proses Salin Jatah Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
