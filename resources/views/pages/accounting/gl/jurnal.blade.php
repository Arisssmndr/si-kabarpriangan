<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Jurnal Umum (General Ledger)
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-sky-50 text-kp-blue-700 border border-kp-blue-200">
                            Buku Besar
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Pencatatan berpasangan seluruh transaksi penjualan, kasir, dan penyesuaian.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('accounting.gl.memorial') }}" 
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>Input Jurnal Memorial</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Tabel Jurnal Umum Berpasangan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Transaksi General Ledger</h2>
                    <p class="text-xs text-slate-500">Mutasi debet dan kredit berimbang sesuai standar akuntansi.</p>
                </div>
            </div>

            <div class="overflow-x-auto min-h-[350px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">No. Jurnal</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Sumber</th>
                            <th class="py-3 px-3.5">No. Referensi</th>
                            <th class="py-3 px-3.5">Keterangan</th>
                            <th class="py-3 px-3.5 text-right w-36">Total Debet/Kredit</th>
                            <th class="py-3 px-3.5 text-center w-24">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($jurnalList as $key => $jurnal)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-xs text-slate-400">{{ $jurnalList->firstItem() + $key }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $jurnal->no_jurnal }}</td>
                                <td class="py-3 px-3.5 whitespace-nowrap">{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/Y') }}</td>
                                <td class="py-3 px-3.5">
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md uppercase {{ $jurnal->sumber === 'iklan' ? 'bg-sky-50 text-kp-blue-700 border border-kp-blue-200' : ($jurnal->sumber === 'sirkulasi' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-700') }}">
                                        {{ $jurnal->sumber ?: 'Auto' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 font-mono text-xs">{{ $jurnal->referensi ?: '-' }}</td>
                                <td class="py-3 px-3.5 max-w-xs truncate">{{ $jurnal->keterangan }}</td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($jurnal->total_debet, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ ucfirst($jurnal->status ?: 'Posted') }}
                                    </span>
                                </td>
                            </tr>
                            @if($jurnal->details && $jurnal->details->count() > 0)
                                <tr class="bg-slate-50/40">
                                    <td colspan="8" class="p-3 pl-12">
                                        <table class="w-full text-xs">
                                            <thead>
                                                <tr class="text-slate-500 border-b border-slate-200">
                                                    <th class="py-1 text-left font-semibold">Kode Perkiraan</th>
                                                    <th class="py-1 text-left font-semibold">Nama Akun</th>
                                                    <th class="py-1 text-right font-semibold w-28">Debet (Rp)</th>
                                                    <th class="py-1 text-right font-semibold w-28">Kredit (Rp)</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                @foreach($jurnal->details as $d)
                                                    <tr>
                                                        <td class="py-1 font-mono {{ $d->kredit > 0 ? 'pl-4 text-slate-500' : 'text-slate-800 font-medium' }}">{{ $d->kode_perkiraan }}</td>
                                                        <td class="py-1 {{ $d->kredit > 0 ? 'pl-4 text-slate-500' : 'text-slate-800' }}">{{ $d->nama_perkiraan }}</td>
                                                        <td class="py-1 text-right font-mono text-slate-800">{{ $d->debet > 0 ? number_format($d->debet, 0, ',', '.') : '-' }}</td>
                                                        <td class="py-1 text-right font-mono text-slate-800">{{ $d->kredit > 0 ? number_format($d->kredit, 0, ',', '.') : '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400 text-sm">
                                    Belum ada jurnal umum yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-t border-slate-200">
                {{ $jurnalList->links() }}
            </div>
        </div>

    </div>
</x-app-layout>

