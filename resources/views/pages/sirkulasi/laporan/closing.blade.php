<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Tutup Data Bulanan (Closing Sirkulasi)</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-purple-50 text-purple-700 border border-purple-200">Integrasi Akuntansi</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Kunci data jatah dan retur koran akhir bulan untuk menghasilkan status <strong>Ready to Pull Akunting</strong>.
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('sirkulasi.laporan.closing') }}" class="flex items-center gap-2">
                <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="text-xs rounded-lg border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                <button type="submit" class="py-2 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition">
                    Pilih Periode
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-medium flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Panel Status Periode Saat Ini -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 gap-3">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase">Status Periode Operasional</span>
                    <h2 class="text-lg font-extrabold text-slate-900">{{ $periodeSaatIni->periode }}</h2>
                </div>
                <div>
                    @if($periodeSaatIni->is_locked)
                        <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-purple-100 text-purple-800 border border-purple-300 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            {{ $periodeSaatIni->status_label }}
                        </span>
                    @else
                        <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-amber-50 text-amber-800 border border-amber-300 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            {{ $periodeSaatIni->status_label }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Matriks Ringkasan -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 my-5 text-xs">
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="text-slate-500">Total Oplah Cetak:</span>
                    <div class="text-base font-extrabold text-slate-900 mt-1">{{ number_format($periodeSaatIni->total_oplah_cetak, 0, ',', '.') }} eks</div>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="text-slate-500">Total Koran Terjual:</span>
                    <div class="text-base font-extrabold text-emerald-700 mt-1">{{ number_format($periodeSaatIni->total_terjual, 0, ',', '.') }} eks</div>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="text-slate-500">Retur Fisik (BAPR):</span>
                    <div class="text-base font-extrabold text-rose-600 mt-1">{{ number_format($periodeSaatIni->total_retur_eks, 0, ',', '.') }} eks ({{ $periodeSaatIni->rasio_retur }}%)</div>
                </div>
                <div class="p-3.5 bg-purple-50 rounded-xl border border-purple-200">
                    <span class="text-purple-800 font-bold">Omset Bersih Sirkulasi:</span>
                    <div class="text-base font-extrabold text-purple-900 mt-1">Rp {{ number_format($periodeSaatIni->omset_bersih, 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Tombol Kunci -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <p class="text-xs text-slate-500">
                    Setelah dikunci, data jatah dan retur tidak dapat diedit lagi oleh staf sirkulasi.
                </p>
                @if(!$periodeSaatIni->is_locked)
                    <form method="POST" action="{{ route('sirkulasi.laporan.closing.lock') }}" onsubmit="return confirm('Kunci data sirkulasi periode ini untuk integrasi ke akuntansi?');">
                        @csrf
                        <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                        <button type="submit" class="px-4 py-2 text-xs font-bold rounded-lg bg-purple-600 hover:bg-purple-700 text-white shadow-xs transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            <span>Tutup Data & Kunci Periode</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Riwayat Closing -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Riwayat Closing Periode Sebelumnya</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Periode</th>
                            <th class="px-4 py-3 font-semibold text-right">Oplah Cetak</th>
                            <th class="px-4 py-3 font-semibold text-right">Retur Fisik</th>
                            <th class="px-5 py-3 font-semibold text-right">Omset Bersih</th>
                            <th class="px-4 py-3 font-semibold text-center">Status Integrasi</th>
                            <th class="px-5 py-3 font-semibold">Waktu Closing</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($riwayatClosing as $rc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3 font-bold text-slate-900">{{ $rc->periode }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-slate-700">{{ number_format($rc->total_oplah_cetak, 0, ',', '.') }} eks</td>
                            <td class="px-4 py-3 text-right font-semibold text-rose-600">{{ number_format($rc->total_retur_eks, 0, ',', '.') }} eks ({{ $rc->rasio_retur }}%)</td>
                            <td class="px-5 py-3 text-right font-extrabold text-slate-900">Rp {{ number_format($rc->total_omset_bersih, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-purple-50 text-purple-700 border border-purple-200">
                                    {{ $rc->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-500 text-[11px]">
                                {{ $rc->closed_at }} &bull; {{ $rc->closed_by }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
