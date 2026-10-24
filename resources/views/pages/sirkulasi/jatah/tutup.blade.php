<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-purple-600 uppercase tracking-wider">Operasional Jatah</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Tutup Data Penjualan (Locking Akuntansi)</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Kunci data transaksi jatah dan retur koran harian/bulanan sebelum ditarik oleh Divisi Accounting (Ready to Pull Akunting)
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('sirkulasi.jatah.tutup') }}" class="flex items-center gap-2">
                <input type="month" name="bulan" value="{{ $bulanPilihan }}" class="text-xs rounded-lg border-slate-200 py-1.5 focus:border-amber-500 focus:ring-amber-500">
                <button type="submit" class="py-1.5 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition-colors cursor-pointer">
                    Pilih Periode
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-medium text-emerald-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Panel Status Periode Saat Ini -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 gap-3">
                <div>
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Periode Penjualan Aktif</span>
                    <h2 class="text-base font-extrabold text-slate-900 mt-0.5">{{ $periodeSaatIni->periode }}</h2>
                </div>
                <div>
                    @if($periodeSaatIni->is_locked)
                        <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-purple-100 text-purple-800 border border-purple-300 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            <span>{{ $periodeSaatIni->status_label }}</span>
                        </span>
                    @else
                        <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-amber-50 text-amber-800 border border-amber-300 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>{{ $periodeSaatIni->status_label }}</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Matriks Ringkasan -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 my-4 text-xs">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="text-slate-500 text-[11px]">Total Oplah Cetak:</span>
                    <div class="text-sm font-extrabold text-slate-900 mt-1">{{ number_format($periodeSaatIni->total_oplah_cetak, 0, ',', '.') }} eks</div>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="text-slate-500 text-[11px]">Koran Terjual (Nett):</span>
                    <div class="text-sm font-extrabold text-emerald-700 mt-1">{{ number_format($periodeSaatIni->total_terjual, 0, ',', '.') }} eks</div>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="text-slate-500 text-[11px]">Total Retur (Fisik):</span>
                    <div class="text-sm font-extrabold text-rose-600 mt-1">{{ number_format($periodeSaatIni->total_retur_eks, 0, ',', '.') }} eks ({{ $periodeSaatIni->rasio_retur }}%)</div>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="text-slate-500 text-[11px]">Omset Bersih Sirkulasi:</span>
                    <div class="text-sm font-extrabold text-slate-900 mt-1">Rp {{ number_format($periodeSaatIni->omset_bersih, 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Tombol Aksi Kunci Data -->
            <div class="p-4 bg-purple-50/70 border border-purple-200 rounded-xl flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                <div class="text-purple-900 leading-relaxed">
                    <strong class="font-bold">Konfirmasi Penguncian:</strong> Setelah status dikunci (<span class="font-mono font-bold">READY TO PULL AKUNTING</span>), staf Sirkulasi tidak dapat merubah jatah dan retur koran periode ini agar sinkronisasi kartu piutang di Akuntansi konsisten.
                </div>
                <form method="POST" action="{{ route('sirkulasi.jatah.tutup.lock') }}">
                    @csrf
                    <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                    <button type="submit" 
                            onclick="return confirm('Apakah Anda yakin ingin MENUTUP & MENGUNCI data penjualan periode {{ $bulanPilihan }}? Data akan diserahkan ke Divisi Akuntansi.')" 
                            class="px-4 py-2 text-xs font-bold rounded-lg bg-purple-700 hover:bg-purple-800 text-white shadow-xs transition-colors shrink-0 cursor-pointer">
                        🔒 Kunci & Tutup Data Penjualan
                    </button>
                </form>
            </div>
        </div>

        <!-- Riwayat Tutup Data Bulan Sebelumnya -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/60">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Riwayat Penutupan Buku Periode Lalu</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-100/75 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Periode</th>
                            <th class="py-3 px-4 text-right">Oplah Cetak</th>
                            <th class="py-3 px-4 text-right">Retur (Eks)</th>
                            <th class="py-3 px-4 text-center">Rasio Retur</th>
                            <th class="py-3 px-4 text-right">Omset Bersih</th>
                            <th class="py-3 px-4 text-center">Status Akuntansi</th>
                            <th class="py-3 px-4">Waktu Closing</th>
                            <th class="py-3 px-4">Otorisator</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($riwayatClosing as $rc)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-900">
                                    {{ $rc->periode }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    {{ number_format($rc->total_oplah_cetak, 0, ',', '.') }} eks
                                </td>
                                <td class="py-3 px-4 text-right text-rose-600">
                                    {{ number_format($rc->total_retur_eks, 0, ',', '.') }} eks
                                </td>
                                <td class="py-3 px-4 text-center">
                                    {{ $rc->rasio_retur }}%
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-slate-900">
                                    Rp {{ number_format($rc->total_omset_bersih, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($rc->status === 'PULLED')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                            {{ $rc->status_label }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">
                                            {{ $rc->status_label }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-mono text-[11px] text-slate-500">
                                    {{ $rc->closed_at }}
                                </td>
                                <td class="py-3 px-4 text-slate-700">
                                    {{ $rc->closed_by }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
