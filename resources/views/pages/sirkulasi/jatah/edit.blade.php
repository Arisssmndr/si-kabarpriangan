<x-app-layout>
    <div class="space-y-6">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Operasional Jatah (H+1)</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Edit Data Penjualan Koran</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Input matriks alokasi jatah koran H+1: Langganan, Konsinyasi/Eceran (Rp3.000), Jatah Gratis, dan Insidentil
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('sirkulasi.jatah.copy') }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition-colors inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
                    <span>Salin Pola Jatah</span>
                </a>
                <a href="{{ route('sirkulasi.jatah.hitung') }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 transition-colors inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                    <span>Kalkulasi Akumulasi</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-medium text-emerald-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter Tanggal & Jurusan -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.jatah.edit') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Tanggal Distribusi (Edisi H+1)</label>
                    <input type="date" name="tanggal" value="{{ $targetTanggal }}" class="text-xs rounded-lg border-slate-200 py-1.5 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Filter Rute Distribusi</label>
                    <select name="jurusan" class="text-xs rounded-lg border-slate-200 py-1.5 focus:border-amber-500 focus:ring-amber-500 min-w-56">
                        <option value="all">Semua Rute Distribusi</option>
                        @foreach($jurusanList as $jur)
                            <option value="{{ $jur->kode }}" {{ ($jurusanPilihan ?? '') === $jur->kode ? 'selected' : '' }}>
                                {{ $jur->kode }} - {{ $jur->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="py-1.5 px-3.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-semibold transition-colors cursor-pointer">
                    Tampilkan Matriks
                </button>
            </form>
        </div>

        <!-- KPI Ringkasan Matriks H+1 -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <div class="text-[11px] text-slate-400 font-semibold uppercase">Langganan</div>
                <div class="text-lg font-bold text-slate-900 mt-1">{{ number_format($totalLangganan, 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">eks</span></div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <div class="text-[11px] text-emerald-600 font-semibold uppercase">Konsinyasi (Rp3.000)</div>
                <div class="text-lg font-bold text-emerald-700 mt-1">{{ number_format($totalKonsinyasi, 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">eks</span></div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <div class="text-[11px] text-sky-600 font-semibold uppercase">Jatah Gratis</div>
                <div class="text-lg font-bold text-sky-700 mt-1">{{ number_format($totalGratis, 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">eks</span></div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <div class="text-[11px] text-purple-600 font-semibold uppercase">Insidentil</div>
                <div class="text-lg font-bold text-purple-700 mt-1">{{ number_format($totalInsidentil, 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">eks</span></div>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3.5 shadow-xs col-span-2 sm:col-span-1">
                <div class="text-[11px] text-amber-800 font-semibold uppercase">Total Jatah H+1</div>
                <div class="text-lg font-extrabold text-amber-900 mt-1">{{ number_format($grandTotalJatah, 0, ',', '.') }} <span class="text-[10px] text-amber-700 font-normal">eks</span></div>
            </div>
        </div>

        <!-- Form Matriks Input Jatah H+1 -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <form method="POST" action="{{ route('sirkulasi.jatah.edit.store') }}">
                @csrf
                <input type="hidden" name="tanggal_target" value="{{ $targetTanggal }}">

                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Matriks Alokasi Jatah Penjualan</h3>
                        <span class="text-[11px] text-slate-500">Edisi Distribusi: <strong class="text-slate-800">{{ \Carbon\Carbon::parse($targetTanggal)->translatedFormat('l, d F Y') }}</strong></span>
                    </div>
                    <button type="submit" class="px-4 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-semibold shadow-2xs transition-colors cursor-pointer inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Simpan Perubahan Matriks</span>
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 min-w-[1050px]">
                        <thead class="bg-slate-100/75 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-3 w-12 text-center whitespace-nowrap">No</th>
                                <th class="py-3 px-3.5 whitespace-nowrap w-32">Kode</th>
                                <th class="py-3 px-4 min-w-[170px]">Nama Agen & Kios</th>
                                <th class="py-3 px-3 whitespace-nowrap min-w-[140px]">Rute Distribusi</th>
                                <th class="py-3 px-3 text-center whitespace-nowrap">Langganan (Eks)</th>
                                <th class="py-3 px-3 text-center bg-emerald-50/50 text-emerald-900 whitespace-nowrap">Konsinyasi Rp3.000 (Eks)</th>
                                <th class="py-3 px-3 text-center whitespace-nowrap">Gratis (Eks)</th>
                                <th class="py-3 px-3 text-center whitespace-nowrap">Insidentil (Eks)</th>
                                <th class="py-3 px-3.5 text-right font-extrabold text-slate-900 whitespace-nowrap w-28">Total Jatah</th>
                                <th class="py-3 px-4 text-right whitespace-nowrap w-32">Estimasi Omset</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($matrixJatah as $idx => $item)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-2.5 px-3 text-center text-slate-400 whitespace-nowrap">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-2.5 px-3.5 whitespace-nowrap">
                                        <span class="inline-block px-2.5 py-1 rounded-md font-mono font-bold text-slate-900 bg-slate-100 text-xs border border-slate-200/80">
                                            {{ $item->kode_agen }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-4">
                                        <span class="font-bold text-slate-900 text-xs">{{ $item->nama_agen }}</span>
                                    </td>
                                    <td class="py-2.5 px-3 text-xs text-slate-600 whitespace-nowrap">
                                        <span class="font-semibold text-slate-800">Rute {{ $item->jurusan_kode }}</span>
                                        <span class="text-[11px] text-slate-400">({{ Str::limit($item->jurusan_nama, 18) }})</span>
                                    </td>
                                    <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                        <input type="number" name="langganan[{{ $item->agen_id }}]" value="{{ $item->langganan }}" min="0" class="w-18 text-center text-xs py-1 rounded border-slate-200 focus:border-amber-500 focus:ring-amber-500 font-semibold text-slate-800">
                                    </td>
                                    <td class="py-2.5 px-3 text-center bg-emerald-50/30 whitespace-nowrap">
                                        <input type="number" name="konsinyasi[{{ $item->agen_id }}]" value="{{ $item->konsinyasi }}" min="0" class="w-18 text-center text-xs py-1 rounded border-emerald-300 focus:border-emerald-500 focus:ring-emerald-500 font-bold text-emerald-800">
                                    </td>
                                    <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                        <input type="number" name="gratis[{{ $item->agen_id }}]" value="{{ $item->gratis }}" min="0" class="w-16 text-center text-xs py-1 rounded border-slate-200 focus:border-amber-500 focus:ring-amber-500 text-slate-700">
                                    </td>
                                    <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                        <input type="number" name="insidentil[{{ $item->agen_id }}]" value="{{ $item->insidentil }}" min="0" class="w-16 text-center text-xs py-1 rounded border-slate-200 focus:border-amber-500 focus:ring-amber-500 text-slate-700">
                                    </td>
                                    <td class="py-2.5 px-3.5 text-right font-black text-slate-900 text-xs whitespace-nowrap">
                                        {{ number_format($item->total_jatah, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-bold text-emerald-700 whitespace-nowrap">
                                        Rp {{ number_format($item->estimasi_omset, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-100 font-bold text-slate-800 border-t-2 border-slate-200">
                            <tr>
                                <td colspan="4" class="py-3 px-4 text-right uppercase text-[11px] whitespace-nowrap">Akumulasi Total:</td>
                                <td class="py-3 px-3 text-center whitespace-nowrap">{{ number_format($totalLangganan, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-center text-emerald-800 whitespace-nowrap">{{ number_format($totalKonsinyasi, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-center text-sky-800 whitespace-nowrap">{{ number_format($totalGratis, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-center text-purple-800 whitespace-nowrap">{{ number_format($totalInsidentil, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right text-amber-900 font-black text-sm whitespace-nowrap">{{ number_format($grandTotalJatah, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right text-emerald-800 font-bold text-sm whitespace-nowrap">Rp {{ number_format($grandTotalOmset, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="p-4 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Konsinyasi / Eceran dihitung dengan harga patokan agen Rp3.000 per eksemplar.</span>
                    <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-semibold shadow-2xs transition-colors cursor-pointer">
                        Simpan Matriks H+1
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
