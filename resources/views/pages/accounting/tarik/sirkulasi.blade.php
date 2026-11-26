<x-app-layout>
    <div class="space-y-5">
        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Penarikan Penjualan Sirkulasi Bulanan
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Siklus Bulanan
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Ditarik secara akumulatif 1 bulan sekali setelah Divisi Sirkulasi melakukan penutupan data jatah koran (status: Ready to Pull Akunting).
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('accounting.tarik.sirkulasi.proses') }}" class="shrink-0">
                @csrf
                <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                    <span>Tarik Penjualan Sirkulasi (Ready to Pull)</span>
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Card Daftar Batch Sirkulasi Bulanan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($batchSirkulasi as $batch)
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-slate-900">{{ $batch->periode }}</h2>
                            <span class="text-xs text-slate-500 font-mono">({{ $batch->periode_key }})</span>
                        </div>
                        @if(str_contains($batch->status_pull_akunting, 'SIAP'))
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                {{ $batch->status_pull_akunting }}
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $batch->status_pull_akunting }}
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-4 text-sm">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <span class="text-xs text-slate-500 block">Total Oplah Cetak:</span>
                            <div class="font-bold font-mono text-slate-900 mt-0.5">{{ number_format($batch->total_oplah_cetak, 0, ',', '.') }} eks</div>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <span class="text-xs text-slate-500 block">Total Retur BAPR:</span>
                            <div class="font-bold font-mono text-slate-900 mt-0.5">{{ number_format($batch->total_retur_bapr, 0, ',', '.') }} eks</div>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <span class="text-xs text-slate-500 block">Omset Tagihan Kotor:</span>
                            <div class="font-bold font-mono text-slate-800 mt-0.5">Rp {{ number_format($batch->omset_kotor, 0, ',', '.') }}</div>
                        </div>
                        <div class="p-3 bg-kp-blue-50 rounded-xl border border-kp-blue-200">
                            <span class="text-xs text-kp-blue-800 font-bold block">Omset Bersih Diakui:</span>
                            <div class="font-bold font-mono text-kp-blue-700 mt-0.5">Rp {{ number_format($batch->omset_bersih, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-400">Closing Sirkulasi: {{ $batch->status_closing_sirkulasi }}</span>
                    @if(str_contains($batch->status_pull_akunting, 'SIAP'))
                        <form method="POST" action="{{ route('accounting.tarik.sirkulasi.proses') }}">
                            @csrf
                            <input type="hidden" name="periode" value="{{ $batch->periode }}">
                            <button type="submit" class="text-xs font-bold text-kp-blue-700 hover:underline cursor-pointer">
                                Proses Tarik Periode Ini &rarr;
                            </button>
                        </form>
                    @else
                        <span class="text-slate-400">{{ $batch->pulled_at }}</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full bg-white border border-slate-200 rounded-xl p-8 text-center text-sm text-slate-500 shadow-xs">
                Tidak ada data batch penarikan sirkulasi yang tersedia.
            </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
