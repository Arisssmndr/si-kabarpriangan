<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-kp-blue-50 text-kp-blue-600 border border-kp-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-kp-blue-600 uppercase tracking-wider">Divisi Sirkulasi</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Surat Jalan & Packing Armada</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Daftar pengepakan bendel koran (50 eks per bendel) dan manifes muatan ekspedisi per rute tujuan.
                    </p>
                </div>
            </div>

            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                <span>Cetak Manifes Surat Jalan</span>
            </button>
        </div>

        <!-- Filter Rute & Tanggal -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.logistik.surat-jalan') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tanggal Pengiriman</label>
                    <input type="date" name="tanggal" value="{{ $tanggalDistribusi }}" class="text-xs rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jalur Rute Armada</label>
                    <select name="rute" class="text-xs rounded-lg border-slate-300 focus:border-kp-blue-600 focus:ring-kp-blue-600">
                        @foreach($ruteList as $r)
                            <option value="{{ $r->kode }}" {{ ($activeRute->kode ?? '') == $r->kode ? 'selected' : '' }}>
                                {{ $r->kode }} - {{ $r->nama_rute }} (Sopir: {{ $r->driver }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="py-2 px-4 text-xs font-semibold rounded-lg bg-kp-blue-600 text-white hover:bg-kp-blue-700 transition shadow-2xs">
                    Tampilkan
                </button>
            </form>
        </div>

        <!-- Detail Armada Aktif (60-30-10) -->
        <div class="bg-kp-blue-50 border border-kp-blue-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
            <div>
                <span class="font-bold text-kp-blue-950 text-sm">{{ $activeRute->nama_rute ?? 'Rute Armada' }}</span>
                <span class="ml-2 font-mono text-[11px] px-2 py-0.5 rounded bg-white text-kp-blue-900 font-bold border border-kp-blue-200">{{ $activeRute->kode ?? '' }}</span>
                <div class="text-kp-blue-900 mt-1">
                    Sopir / Driver: <strong>{{ $activeRute->driver ?? '-' }}</strong> &bull; Kendaraan: <strong>{{ $activeRute->kendaraan ?? '-' }}</strong> &bull; Jam Berangkat: <strong>{{ $activeRute->jam_berangkat ?? '-' }}</strong>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-white px-3 py-1.5 rounded-lg border border-kp-blue-200 text-right">
                    <div class="text-[10px] text-slate-500 uppercase font-semibold">Total Muatan</div>
                    <div class="text-sm font-extrabold text-slate-900 font-mono">{{ number_format($totalEksemplarRute, 0, ',', '.') }} eks</div>
                </div>
                <div class="bg-white px-3 py-1.5 rounded-lg border border-kp-blue-200 text-right">
                    <div class="text-[10px] text-slate-500 uppercase font-semibold">Kemasan Packing</div>
                    <div class="text-sm font-extrabold text-slate-900 font-mono">{{ $totalBendelRute }} bndl + {{ $totalEceranRute }} ecer</div>
                </div>
            </div>
        </div>

        <!-- Tabel Pengepakan Koran (Kolom Terpisah) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left min-w-[1050px]">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] uppercase text-[10px] tracking-wider font-bold">
                        <tr>
                            <th class="px-3.5 py-3 whitespace-nowrap w-28">Kode Agen</th>
                            <th class="px-4 py-3 min-w-[160px]">Nama Agen</th>
                            <th class="px-3.5 py-3 whitespace-nowrap min-w-[130px]">Wilayah</th>
                            <th class="px-3.5 py-3 whitespace-nowrap min-w-[130px]">No. Kontak</th>
                            <th class="px-4 py-3 text-center whitespace-nowrap w-36">Packing Bendel (50 eks)</th>
                            <th class="px-4 py-3 text-center whitespace-nowrap w-24">Sisa Lepas</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap w-28">Total Muat</th>
                            <th class="px-4 py-3 text-center whitespace-nowrap w-32">Paraf Penerima</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($packingItems as $item)
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

                            <!-- Wilayah (Kolom Terpisah) -->
                            <td class="px-3.5 py-3 whitespace-nowrap text-slate-700 text-xs">
                                {{ $item->wilayah }}
                            </td>

                            <!-- Kontak (Kolom Terpisah) -->
                            <td class="px-3.5 py-3 whitespace-nowrap font-mono text-slate-600 text-xs">
                                {{ $item->telepon ?: '-' }}
                            </td>

                            <!-- Packing Bendel -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-md bg-kp-blue-50 text-kp-blue-800 border border-kp-blue-200 font-mono">
                                    {{ $item->bendel_50 }} Bendel
                                </span>
                            </td>

                            <!-- Sisa Lepas -->
                            <td class="px-4 py-3 text-center font-mono font-semibold text-slate-700 whitespace-nowrap">
                                {{ $item->sisa_ecer }} eks
                            </td>

                            <!-- Total Muat -->
                            <td class="px-4 py-3 text-right font-bold text-slate-900 text-xs font-mono whitespace-nowrap">
                                {{ number_format($item->total_eks, 0, ',', '.') }} eks
                            </td>

                            <!-- Paraf -->
                            <td class="px-4 py-3 text-center">
                                <div class="w-24 h-7 border border-dashed border-slate-300 rounded mx-auto flex items-center justify-center text-[10px] text-slate-300">
                                    Tanda Tangan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                Tidak ada data muatan untuk rute dan tanggal ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
