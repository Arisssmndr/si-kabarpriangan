<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Entri Jatah Harian H+1</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-amber-50 text-amber-700 border border-amber-200">Siklus H-1</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Input kuota koran per agen untuk edisi terbit: <strong>{{ \Carbon\Carbon::parse($targetTanggal)->translatedFormat('l, d F Y') }}</strong> (dicetak malam ini).
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('sirkulasi.jatah.copy') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
                    <span>Salin Pola Kemarin</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-medium flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.jatah.harian') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Target Tanggal Terbit (H+1)</label>
                    <input type="date" name="tanggal" value="{{ $targetTanggal }}" class="text-xs rounded-lg border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Filter Rute / Armada</label>
                    <select name="rute" class="text-xs rounded-lg border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                        <option value="all">Semua Rute (Priangan Timur)</option>
                        @foreach($ruteList as $r)
                            <option value="{{ $r->kode }}" {{ $rutePilihan == $r->kode ? 'selected' : '' }}>{{ $r->kode }} - {{ $r->nama_rute }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="py-2 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition">
                    Tampilkan Jatah
                </button>
            </form>
        </div>

        <!-- 4 Summary KPI Jatah -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs">
                <div class="text-[11px] text-slate-500 font-medium">Langganan Tetap</div>
                <div class="text-base font-extrabold text-slate-900 mt-1">{{ number_format($totalLangganan, 0, ',', '.') }} eks</div>
            </div>
            <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs">
                <div class="text-[11px] text-slate-500 font-medium">Eceran & Lapak</div>
                <div class="text-base font-extrabold text-sky-700 mt-1">{{ number_format($totalEceran, 0, ',', '.') }} eks</div>
            </div>
            <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs">
                <div class="text-[11px] text-slate-500 font-medium">Insidentil/Khusus</div>
                <div class="text-base font-extrabold text-amber-700 mt-1">{{ number_format($totalInsidentil, 0, ',', '.') }} eks</div>
            </div>
            <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs">
                <div class="text-[11px] text-slate-500 font-medium">Jatah Gratis (Bukti)</div>
                <div class="text-base font-extrabold text-purple-700 mt-1">{{ number_format($totalGratis, 0, ',', '.') }} eks</div>
            </div>
            <div class="bg-amber-50 p-3.5 rounded-xl border border-amber-200 shadow-xs col-span-2 md:col-span-1">
                <div class="text-[11px] text-amber-800 font-bold">Total Jatah H+1</div>
                <div class="text-base font-extrabold text-amber-900 mt-1">{{ number_format($grandTotalJatah, 0, ',', '.') }} eks</div>
            </div>
        </div>

        <!-- Form Tabel Entri Jatah -->
        <form method="POST" action="{{ route('sirkulasi.jatah.harian.store') }}">
            @csrf
            <input type="hidden" name="tanggal_jatah" value="{{ $targetTanggal }}">
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Matriks Alokasi Koran per Agen</span>
                    <button type="submit" class="px-4 py-1.5 text-xs font-bold rounded-lg bg-amber-600 hover:bg-amber-700 text-white shadow-xs transition">
                        Simpan Jatah Harian
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Nama Agen & Jalur</th>
                                <th class="px-3 py-3 font-semibold text-center">Langganan</th>
                                <th class="px-3 py-3 font-semibold text-center">Eceran</th>
                                <th class="px-3 py-3 font-semibold text-center">Insidentil</th>
                                <th class="px-3 py-3 font-semibold text-center">Gratis</th>
                                <th class="px-4 py-3 font-semibold text-right">Total Jatah</th>
                                <th class="px-4 py-3 font-semibold">Catatan Distribusi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($matrixJatah as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900">{{ $item->nama_agen }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">{{ $item->kode_agen }} &bull; {{ $item->rute_nama }}</div>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <input type="number" name="jatah[{{ $item->agen_id }}][langganan]" value="{{ $item->langganan }}" min="0" 
                                           class="w-20 text-center text-xs py-1 rounded border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <input type="number" name="jatah[{{ $item->agen_id }}][eceran]" value="{{ $item->eceran }}" min="0" 
                                           class="w-20 text-center text-xs py-1 rounded border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <input type="number" name="jatah[{{ $item->agen_id }}][insidentil]" value="{{ $item->insidentil }}" min="0" 
                                           class="w-20 text-center text-xs py-1 rounded border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <input type="number" name="jatah[{{ $item->agen_id }}][gratis]" value="{{ $item->gratis }}" min="0" 
                                           class="w-16 text-center text-xs py-1 rounded border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                                </td>
                                <td class="px-4 py-3 text-right font-black text-slate-900 text-sm whitespace-nowrap">
                                    {{ number_format($item->total_jatah, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" name="jatah[{{ $item->agen_id }}][keterangan]" value="{{ $item->keterangan }}" 
                                           class="w-full text-xs py-1 rounded border-slate-200 focus:border-amber-500 focus:ring-amber-500" placeholder="Keterangan drop point...">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
