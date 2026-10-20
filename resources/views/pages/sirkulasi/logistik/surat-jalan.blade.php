<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="print:hidden no-print bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 border border-sky-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Surat Jalan & Packing Armada</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-sky-50 text-sky-700 border border-sky-200">Pengiriman Subuh</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Daftar pengepakan bendel koran (50 eks per bendel) dan manifes muatan mobil ekspedisi per rute tujuan.
                    </p>
                </div>
            </div>

            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-900 text-white shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                <span>Cetak Manifes Surat Jalan</span>
            </button>
        </div>

        <!-- Filter Rute & Tanggal -->
        <div class="print:hidden no-print bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.logistik.surat-jalan') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Tanggal Pengiriman</label>
                    <input type="date" name="tanggal" value="{{ $tanggalDistribusi }}" class="text-xs rounded-lg border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Pilih Jalur Rute Armada</label>
                    <select name="rute" class="text-xs rounded-lg border-slate-200 focus:border-amber-500 focus:ring-amber-500">
                        @foreach($ruteList as $r)
                            <option value="{{ $r->kode }}" {{ ($activeRute->kode ?? '') == $r->kode ? 'selected' : '' }}>
                                {{ $r->kode }} - {{ $r->nama_rute }} (Sopir: {{ $r->driver }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="py-2 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition">
                    Tampilkan Surat Jalan
                </button>
            </form>
        </div>

        <!-- Kop Cetak Resmi (Hanya Muncul Saat Print) -->
        <div class="hidden print:block border-b-2 border-slate-900 pb-3 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black tracking-wider uppercase text-slate-900">HARIAN UMUM KABAR PRIANGAN</h2>
                    <p class="text-[10px] text-slate-600 font-semibold">DIVISI SIRKULASI & LOGISTIK DISTRIBUSI</p>
                    <p class="text-[9px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</p>
                </div>
                <div class="text-right">
                    <div class="text-xs font-black uppercase text-slate-900">SURAT JALAN & PACKING ARMADA</div>
                    <div class="text-[10px] font-semibold text-slate-700">Tanggal Pengiriman: {{ \Carbon\Carbon::parse($tanggalDistribusi)->translatedFormat('d F Y') }}</div>
                    <div class="text-[9px] text-slate-400 font-mono">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- Detail Armada Aktif -->
        <div class="report-print-container bg-sky-50 border border-sky-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
            <div>
                <span class="font-bold text-sky-950 text-sm">{{ $activeRute->nama_rute ?? 'Rute Armada' }}</span>
                <span class="ml-2 font-mono text-[11px] px-2 py-0.5 rounded bg-sky-200 text-sky-900 font-bold">{{ $activeRute->kode ?? '' }}</span>
                <div class="text-sky-800 mt-1">
                    Sopir / Driver: <strong>{{ $activeRute->driver ?? '-' }}</strong> &bull; Kendaraan: <strong>{{ $activeRute->kendaraan ?? '-' }}</strong> &bull; Jam Berangkat: <strong>{{ $activeRute->jam_berangkat ?? '-' }}</strong>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-white px-3 py-1.5 rounded-lg border border-sky-200 text-right">
                    <div class="text-[10px] text-slate-500 uppercase font-semibold">Total Muatan</div>
                    <div class="text-sm font-extrabold text-sky-950">{{ number_format($totalEksemplarRute, 0, ',', '.') }} eks</div>
                </div>
                <div class="bg-white px-3 py-1.5 rounded-lg border border-sky-200 text-right">
                    <div class="text-[10px] text-slate-500 uppercase font-semibold">Kemasan Packing</div>
                    <div class="text-sm font-extrabold text-sky-950">{{ $totalBendelRute }} bndl + {{ $totalEceranRute }} ecer</div>
                </div>
            </div>
        </div>

        <!-- Tabel Pengepakan Koran (Packing List) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Drop Point / Nama Agen</th>
                            <th class="px-4 py-3 font-semibold">Wilayah / Kontak</th>
                            <th class="px-4 py-3 font-semibold text-center">Packing Bendel (50 eks)</th>
                            <th class="px-4 py-3 font-semibold text-center">Sisa Lepas</th>
                            <th class="px-5 py-3 font-semibold text-right">Total Muat</th>
                            <th class="px-5 py-3 font-semibold text-center">Paraf Penerima</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($packingItems as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3">
                                <div class="font-bold text-slate-900">{{ $item->nama_agen }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $item->kode_agen }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-slate-800">{{ $item->wilayah }}</div>
                                <div class="text-[11px] text-slate-400">{{ $item->telepon }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-1 text-xs font-bold rounded bg-amber-50 text-amber-800 border border-amber-200">
                                    {{ $item->bendel_50 }} Bendel
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-semibold text-slate-700">
                                {{ $item->sisa_ecer }} eks
                            </td>
                            <td class="px-5 py-3 text-right font-extrabold text-slate-900 text-sm">
                                {{ number_format($item->total_eks, 0, ',', '.') }} eks
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div class="w-24 h-8 border border-dashed border-slate-300 rounded mx-auto flex items-center justify-center text-[10px] text-slate-300">
                                    Tanda Tangan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">Tidak ada alokasi jatah pada rute ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tanda Tangan Surat Jalan (Hanya Muncul Saat Print) -->
        <div class="hidden print:grid grid-cols-3 gap-6 text-center text-xs pt-6 mt-6 border-t border-slate-300">
            <div>
                <div class="text-slate-500">Sopir / Driver Armada:</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">{{ $activeRute->driver ?? '( ................................ )' }}</div>
                <div class="text-[10px] text-slate-500">Mobil / Ekspedisi</div>
            </div>
            <div>
                <div class="text-slate-500">Petugas Dispatcher:</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                <div class="text-[10px] text-slate-500">Gudang & Ekspedisi</div>
            </div>
            <div>
                <div class="text-slate-500">Mengetahui:</div>
                <div class="h-16"></div>
                <div class="font-bold text-slate-900 underline">( ........................................ )</div>
                <div class="text-[10px] text-slate-500">Kabag Sirkulasi & Distribusi</div>
            </div>
        </div>
    </div>
</x-app-layout>
