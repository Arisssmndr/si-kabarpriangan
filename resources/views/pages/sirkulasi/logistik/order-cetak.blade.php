<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 border border-orange-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">SPK Order Cetak Percetakan</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-orange-50 text-orange-700 border border-orange-200">Pabrik Cetak</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Surat Perintah Kerja (SPK) kuota cetak oplah harian kepada pabrik percetakan (PT Priangan Intermedia Grafika).
                    </p>
                </div>
            </div>

            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-orange-600 hover:bg-orange-700 text-white shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                <span>Cetak SPK Percetakan</span>
            </button>
        </div>

        <!-- Detail SPK Cetak -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs max-w-3xl space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200">
                <div>
                    <div class="text-xs text-slate-500 font-semibold uppercase">Nomor SPK Resmi</div>
                    <div class="text-base font-extrabold text-slate-900 font-mono">{{ $orderCetak->no_spk }}</div>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ $orderCetak->status }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="text-slate-500">Pabrik Percetakan:</span>
                    <div class="font-bold text-slate-900 mt-0.5">{{ $orderCetak->pabrik_percetakan }}</div>
                </div>

                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="text-slate-500">Edisi Koran Terbit:</span>
                    <div class="font-bold text-slate-900 mt-0.5">{{ $orderCetak->edisi_terbit }}</div>
                </div>

                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="text-slate-500">Jadwal Jam Cetak:</span>
                    <div class="font-bold text-slate-900 mt-0.5">{{ $orderCetak->jam_cetak_mulai }} s/d {{ $orderCetak->jam_cetak_selesai }}</div>
                </div>

                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="text-slate-500">Spesifikasi Kertas:</span>
                    <div class="font-bold text-slate-900 mt-0.5">{{ $orderCetak->tipe_kertas }} &bull; {{ $orderCetak->jumlah_halaman }} Halaman</div>
                </div>
            </div>

            <!-- Breakdown Rincian Oplah -->
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <div class="bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-700 uppercase border-b border-slate-200">
                    Rincian Alokasi Oplah Naik Cetak
                </div>
                <div class="p-4 space-y-3 text-xs">
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Oplah Reguler Agen & Sub-Agen:</span>
                        <strong class="text-slate-900">{{ number_format($orderCetak->oplah_reguler, 0, ',', '.') }} eks</strong>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Jatah Gratis (Bukti Iklan & Legal Redaksi):</span>
                        <strong class="text-purple-700">{{ number_format($orderCetak->oplah_gratis, 0, ',', '.') }} eks</strong>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Cadangan Sirkulasi & Kerusakan:</span>
                        <strong class="text-amber-700">{{ number_format($orderCetak->oplah_cadangan, 0, ',', '.') }} eks</strong>
                    </div>
                    <div class="pt-3 border-t border-slate-200 flex items-center justify-between text-sm">
                        <span class="font-bold text-slate-900">Total Oplah Wajib Cetak:</span>
                        <span class="text-lg font-black text-orange-600">{{ number_format($orderCetak->total_oplah_cetak, 0, ',', '.') }} Eksemplar</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
