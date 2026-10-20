<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Operasional Jatah</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Hitung Data Penjualan (Kalkulasi Akumulasi)</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Kalkulasi akumulasi eksemplar cetak, rincian langganan, konsinyasi agen Rp3.000, jatah gratis, dan estimasi penerimaan omset
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('sirkulasi.jatah.hitung') }}" class="flex items-center gap-2">
                <input type="date" name="tanggal" value="{{ $tanggalPilihan }}" class="text-xs rounded-lg border-slate-200 py-1.5 focus:border-emerald-500 focus:ring-emerald-500">
                <button type="submit" class="py-1.5 px-3 text-xs font-semibold rounded-lg bg-emerald-700 text-white hover:bg-emerald-800 transition-colors cursor-pointer">
                    Hitung Edisi
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-medium text-emerald-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Ringkasan Akumulasi Card Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Oplah Cetak</span>
                <div class="text-xl font-extrabold text-slate-900 mt-1">{{ number_format($grandTotalEks, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">eks</span></div>
                <div class="text-[11px] text-slate-400 mt-0.5">Target Produksi Subuh</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider">Konsinyasi Agen</span>
                <div class="text-xl font-extrabold text-emerald-700 mt-1">{{ number_format($totalKonsinyasi, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">eks</span></div>
                <div class="text-[11px] text-slate-400 mt-0.5">Tarif Komersil @ Rp3.000</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-blue-600 uppercase tracking-wider">Pelanggan Langganan</span>
                <div class="text-xl font-extrabold text-blue-700 mt-1">{{ number_format($totalLangganan, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">eks</span></div>
                <div class="text-[11px] text-slate-400 mt-0.5">Instansi, Pemda & Rumah Tangga</div>
            </div>

            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 shadow-xs">
                <span class="text-[11px] font-semibold text-emerald-800 uppercase tracking-wider">Estimasi Nilai Omset</span>
                <div class="text-xl font-extrabold text-emerald-900 mt-1">Rp {{ number_format($grandTotalOmset, 0, ',', '.') }}</div>
                <div class="text-[11px] text-emerald-700 mt-0.5">Tagihan Berjalan Agen</div>
            </div>
        </div>

        <!-- Tabel Akumulasi per 15 Jurusan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Rincian Kalkulasi per Jurusan Armada</h3>
                    <span class="text-[11px] text-slate-500">Edisi: {{ \Carbon\Carbon::parse($tanggalPilihan)->translatedFormat('l, d F Y') }}</span>
                </div>
                <form method="POST" action="{{ route('sirkulasi.jatah.hitung.process') }}" class="inline">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                    <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        <span>Kalkulasi Ulang</span>
                    </button>
                </form>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-100/75 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3 w-16 text-center">Rute</th>
                            <th class="py-3 px-4">Nama Jurusan Armada</th>
                            <th class="py-3 px-4">Armada Mobil</th>
                            <th class="py-3 px-3 text-right">Langganan</th>
                            <th class="py-3 px-3 text-right text-emerald-800">Konsinyasi</th>
                            <th class="py-3 px-3 text-right">Gratis</th>
                            <th class="py-3 px-3 text-right">Insidentil</th>
                            <th class="py-3 px-4 text-right font-extrabold text-slate-900">Total Eksemplar</th>
                            <th class="py-3 px-4 text-right">Omset Konsinyasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($rekapKalkulasi as $rk)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-3 text-center">
                                    <span class="font-mono font-bold text-xs px-2 py-0.5 rounded bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $rk->kode }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-bold text-slate-900 text-xs">{{ $rk->nama_jurusan }}</span>
                                </td>
                                <td class="py-3 px-4 text-slate-600 text-[11px]">
                                    {{ $rk->armada }}
                                </td>
                                <td class="py-3 px-3 text-right font-semibold text-slate-800">
                                    {{ number_format($rk->langganan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right font-bold text-emerald-700">
                                    {{ number_format($rk->konsinyasi, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right text-slate-600">
                                    {{ number_format($rk->gratis, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right text-slate-600">
                                    {{ number_format($rk->insidentil, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-extrabold text-slate-900 text-xs">
                                    {{ number_format($rk->total_eks, 0, ',', '.') }} eks
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-emerald-700">
                                    Rp {{ number_format($rk->omset_konsinyasi, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-100 font-bold text-slate-800 border-t-2 border-slate-200">
                        <tr>
                            <td colspan="3" class="py-3 px-4 text-right uppercase text-[11px]">Total Akumulasi:</td>
                            <td class="py-3 px-3 text-right">{{ number_format($totalLangganan, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right text-emerald-800">{{ number_format($totalKonsinyasi, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right">{{ number_format($totalGratis, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right">{{ number_format($totalInsidentil, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-slate-900 text-sm font-extrabold">{{ number_format($grandTotalEks, 0, ',', '.') }} eks</td>
                            <td class="py-3 px-4 text-right text-emerald-800 text-sm font-extrabold">Rp {{ number_format($grandTotalOmset, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
