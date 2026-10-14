<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Penarikan Penjualan Sirkulasi Bulanan</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-purple-50 text-purple-700 border border-purple-200">Siklus Bulanan</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Ditarik secara akumulatif 1 bulan sekali setelah Divisi Sirkulasi melakukan penutupan data jatah koran (status: <strong>Ready to Pull Akunting</strong>).
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('accounting.tarik.sirkulasi.proses') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-lg bg-purple-600 hover:bg-purple-700 text-white shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                    <span>Tarik Penjualan Sirkulasi (Ready to Pull)</span>
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-medium flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Card Daftar Batch Sirkulasi Bulanan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($batchSirkulasi as $batch)
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-extrabold text-slate-900">{{ $batch->periode }}</h2>
                            <span class="text-xs text-slate-500 font-mono">({{ $batch->periode_key }})</span>
                        </div>
                        @if(str_contains($batch->status_pull_akunting, 'SIAP'))
                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-300 animate-pulse">
                                {{ $batch->status_pull_akunting }}
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                {{ $batch->status_pull_akunting }}
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-4 text-xs">
                        <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                            <span class="text-slate-500">Total Oplah Cetak:</span>
                            <div class="font-bold text-slate-900 mt-0.5">{{ number_format($batch->total_oplah_cetak, 0, ',', '.') }} eks</div>
                        </div>
                        <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                            <span class="text-slate-500">Total Retur BAPR:</span>
                            <div class="font-bold text-rose-600 mt-0.5">{{ number_format($batch->total_retur_bapr, 0, ',', '.') }} eks</div>
                        </div>
                        <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                            <span class="text-slate-500">Omset Tagihan Kotor:</span>
                            <div class="font-bold text-slate-800 mt-0.5">Rp {{ number_format($batch->omset_kotor, 0, ',', '.') }}</div>
                        </div>
                        <div class="p-2.5 bg-purple-50 rounded-lg border border-purple-200">
                            <span class="text-purple-800 font-bold">Omset Bersih Diakui:</span>
                            <div class="font-extrabold text-purple-900 mt-0.5">Rp {{ number_format($batch->omset_bersih, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-400 text-[11px]">Closing Sirkulasi: {{ $batch->status_closing_sirkulasi }}</span>
                    @if(str_contains($batch->status_pull_akunting, 'SIAP'))
                        <form method="POST" action="{{ route('accounting.tarik.sirkulasi.proses') }}">
                            @csrf
                            <input type="hidden" name="periode" value="{{ $batch->periode }}">
                            <button type="submit" class="text-xs font-bold text-purple-600 hover:text-purple-800">
                                Proses Tarik Periode Ini &rarr;
                            </button>
                        </form>
                    @else
                        <span class="text-slate-400 text-[11px]">{{ $batch->pulled_at }}</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
