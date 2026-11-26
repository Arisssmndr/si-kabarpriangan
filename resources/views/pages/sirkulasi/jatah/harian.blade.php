<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-kp-blue-50 text-kp-blue-600 border border-kp-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-kp-blue-600 uppercase tracking-wider">Divisi Sirkulasi</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Entri Jatah Harian H+1</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Alokasi kuota koran per agen untuk edisi terbit: <strong>{{ \Carbon\Carbon::parse($targetTanggal)->translatedFormat('l, d F Y') }}</strong>.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('sirkulasi.jatah.copy') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 transition shadow-2xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
                    <span>Salin Pola Kemarin</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 rounded-xl text-kp-blue-900 text-xs font-medium flex items-center gap-2">
                <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.jatah.harian') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Target Tanggal Terbit</label>
                    <input type="date" name="tanggal" value="{{ $targetTanggal }}" class="text-xs rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Filter Rute / Armada</label>
                    <select name="rute" class="text-xs rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                        <option value="all">Semua Rute</option>
                        @foreach($ruteList as $r)
                            <option value="{{ $r->kode }}" {{ $rutePilihan == $r->kode ? 'selected' : '' }}>{{ $r->kode }} - {{ $r->nama_rute }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="py-2 px-4 text-xs font-semibold rounded-lg bg-kp-blue-600 text-white hover:bg-kp-blue-700 transition shadow-2xs">
                    Tampilkan Jatah
                </button>
            </form>
        </div>

        <!-- 6 Summary KPI Jatah (60-30-10) -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
            <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs">
                <div class="text-[11px] text-slate-500 font-medium">Langganan Tetap</div>
                <div class="text-base font-extrabold text-slate-900 font-mono mt-1">{{ number_format($totalLangganan, 0, ',', '.') }} eks</div>
            </div>
            <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs">
                <div class="text-[11px] text-slate-500 font-medium">Eceran & Lapak</div>
                <div class="text-base font-extrabold text-slate-900 font-mono mt-1">{{ number_format($totalEceran, 0, ',', '.') }} eks</div>
            </div>
            <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs">
                <div class="text-[11px] text-slate-500 font-medium">Jatah Gratis</div>
                <div class="text-base font-extrabold text-slate-900 font-mono mt-1">{{ number_format($totalGratis, 0, ',', '.') }} eks</div>
            </div>
            <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs">
                <div class="text-[11px] text-slate-500 font-medium">Pengenalan</div>
                <div class="text-base font-extrabold text-slate-900 font-mono mt-1">{{ number_format($totalPengenalan ?? 0, 0, ',', '.') }} eks</div>
            </div>
            <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs">
                <div class="text-[11px] text-slate-500 font-medium">Insidentil</div>
                <div class="text-base font-extrabold text-slate-900 font-mono mt-1">{{ number_format($totalInsidentil, 0, ',', '.') }} eks</div>
            </div>
            <div class="bg-kp-blue-50 p-3.5 rounded-xl border border-kp-blue-200 shadow-xs col-span-2 md:col-span-1">
                <div class="text-[11px] text-kp-blue-800 font-bold">Total Jatah H+1</div>
                <div class="text-base font-extrabold text-kp-blue-900 font-mono mt-1">{{ number_format($grandTotalJatah, 0, ',', '.') }} eks</div>
            </div>
        </div>

        <!-- Form Tabel Entri Jatah (Kolom Terpisah) -->
        <form method="POST" action="{{ route('sirkulasi.jatah.harian.store') }}">
            @csrf
            <input type="hidden" name="tanggal_jatah" value="{{ $targetTanggal }}">
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Matriks Alokasi Koran per Agen</span>
                    <button type="submit" class="px-4 py-2 text-xs font-bold rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white shadow-xs transition cursor-pointer">
                        Simpan Jatah Harian
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left min-w-[1050px]">
                        <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] uppercase text-[10px] tracking-wider font-bold">
                            <tr>
                                <th class="px-3.5 py-3 whitespace-nowrap w-28">Kode Agen</th>
                                <th class="px-4 py-3 min-w-[160px]">Nama Agen</th>
                                <th class="px-3.5 py-3 whitespace-nowrap min-w-[140px]">Jalur Rute</th>
                                <th class="px-3 py-3 text-center whitespace-nowrap w-24">Langganan</th>
                                <th class="px-3 py-3 text-center whitespace-nowrap w-24">Eceran</th>
                                <th class="px-3 py-3 text-center whitespace-nowrap w-20">Gratis</th>
                                <th class="px-3 py-3 text-center whitespace-nowrap w-24">Pengenalan</th>
                                <th class="px-3 py-3 text-center whitespace-nowrap w-24">Insidentil</th>
                                <th class="px-4 py-3 text-right whitespace-nowrap w-28">Total Jatah</th>
                                <th class="px-4 py-3 min-w-[160px]">Catatan Distribusi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($matrixJatah as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <!-- Kode Agen (Kolom Terpisah) -->
                                <td class="px-3.5 py-3 whitespace-nowrap">
                                    <span class="inline-block px-2 py-0.5 rounded font-mono font-bold text-slate-900 bg-slate-100 text-xs border border-slate-200">
                                        {{ $item->kode_agen }}
                                    </span>
                                </td>

                                <!-- Nama Agen (Kolom Terpisah) -->
                                <td class="px-4 py-3 font-bold text-slate-900 text-xs">
                                    {{ $item->nama_agen }}
                                </td>

                                <!-- Jalur Rute (Kolom Terpisah) -->
                                <td class="px-3.5 py-3 whitespace-nowrap text-slate-600 text-xs">
                                    {{ $item->rute_nama }}
                                </td>

                                <!-- Langganan -->
                                <td class="px-3 py-2 text-center whitespace-nowrap">
                                    <input type="number" name="jatah[{{ $item->agen_id }}][langganan]" value="{{ $item->langganan }}" min="0" 
                                           class="w-20 text-center text-xs py-1.5 rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600 font-mono">
                                </td>

                                <!-- Eceran -->
                                <td class="px-3 py-2 text-center whitespace-nowrap">
                                    <input type="number" name="jatah[{{ $item->agen_id }}][eceran]" value="{{ $item->eceran }}" min="0" 
                                           class="w-20 text-center text-xs py-1.5 rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600 font-mono">
                                </td>

                                <!-- Gratis -->
                                <td class="px-3 py-2 text-center whitespace-nowrap">
                                    <input type="number" name="jatah[{{ $item->agen_id }}][gratis]" value="{{ $item->gratis }}" min="0" 
                                           class="w-16 text-center text-xs py-1.5 rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600 font-mono">
                                </td>

                                <!-- Pengenalan -->
                                <td class="px-3 py-2 text-center whitespace-nowrap">
                                    <input type="number" name="jatah[{{ $item->agen_id }}][pengenalan]" value="{{ $item->pengenalan ?? 0 }}" min="0" 
                                           class="w-16 text-center text-xs py-1.5 rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600 font-mono">
                                </td>

                                <!-- Insidentil -->
                                <td class="px-3 py-2 text-center whitespace-nowrap">
                                    <input type="number" name="jatah[{{ $item->agen_id }}][insidentil]" value="{{ $item->insidentil }}" min="0" 
                                           class="w-20 text-center text-xs py-1.5 rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600 font-mono">
                                </td>

                                <!-- Total Jatah -->
                                <td class="px-4 py-3 text-right font-bold text-slate-900 text-xs whitespace-nowrap font-mono">
                                    {{ number_format($item->total_jatah, 0, ',', '.') }}
                                </td>

                                <!-- Catatan Distribusi -->
                                <td class="px-4 py-2">
                                    <input type="text" name="jatah[{{ $item->agen_id }}][keterangan]" value="{{ $item->keterangan ?? '' }}" placeholder="Catatan drop point..."
                                           class="w-full text-xs py-1.5 px-2.5 rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
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
