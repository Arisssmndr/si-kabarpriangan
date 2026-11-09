<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (60-30-10 Palette) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Tarif Harga Koran &amp; Skema Jatah Gratis
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Struktur harga eceran, tarif mitra agen, batas retur BAPR, dan kuota koran cuma-cuma.
                    </p>
                </div>
            </div>
        </div>

        <!-- 3 KPI Struktur Harga Resmi -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Harga Eceran Resmi (HET)</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($skemaTarif['harga_eceran_resmi'], 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">/ eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Harga jual pembaca eceran</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Tarif Agen Besar (11)</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($skemaTarif['harga_agen_besar'], 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">/ eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Maksimal retur BAPR: {{ $skemaTarif['maks_retur_agen_besar'] }}%</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Tarif Sub-Agen (11-9)</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($skemaTarif['harga_sub_agen'], 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">/ eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Maksimal retur BAPR: {{ $skemaTarif['maks_retur_sub_agen'] }}%</span>
            </div>
        </div>

        <!-- 2 Kolom: Tabel Struktur Tarif vs Alokasi Jatah Gratis -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            <!-- Struktur Tarif Koran Cetak -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Struktur Tarif Koran Cetak</h2>
                        <p class="text-xs text-slate-500">Ketetapan tarif distribusi Divisi Sirkulasi</p>
                    </div>
                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                        SK Direksi
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5">Kategori Mitra</th>
                                <th class="py-3 px-3.5 text-center">Batas Retur</th>
                                <th class="py-3 px-3.5 text-right">Tarif Satuan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5">
                                    <div class="font-semibold text-slate-900">Harga Eceran Resmi (HET)</div>
                                    <div class="text-xs text-slate-500">Harga jual langsung ke pembaca</div>
                                </td>
                                <td class="py-3.5 px-3.5 text-center font-mono text-slate-400">-</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-bold text-kp-blue-700 tabular-nums">
                                    Rp {{ number_format($skemaTarif['harga_eceran_resmi'], 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5">
                                    <div class="font-semibold text-slate-900">Agen Besar (Kode 11)</div>
                                    <div class="text-xs text-slate-500">Diskon 30% dari harga eceran resmi</div>
                                </td>
                                <td class="py-3.5 px-3.5 text-center font-mono text-slate-700">{{ $skemaTarif['maks_retur_agen_besar'] }}%</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">
                                    Rp {{ number_format($skemaTarif['harga_agen_besar'], 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5">
                                    <div class="font-semibold text-slate-900">Sub-Agen &amp; Kios (Kode 11-9)</div>
                                    <div class="text-xs text-slate-500">Diskon 20% dari harga eceran resmi</div>
                                </td>
                                <td class="py-3.5 px-3.5 text-center font-mono text-slate-700">{{ $skemaTarif['maks_retur_sub_agen'] }}%</td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">
                                    Rp {{ number_format($skemaTarif['harga_sub_agen'], 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Alokasi Jatah Gratis (Cuma-Cuma) -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Alokasi Jatah Gratis (Cuma-Cuma)</h2>
                        <p class="text-xs text-slate-500">Kuota harian bukti iklan, arsip, dan relasi</p>
                    </div>
                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                        Non-Komersial
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-3.5">Kategori Alokasi</th>
                                <th class="py-3 px-3.5 text-right">Kuota / Hari</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach($skemaGratis as $idx => $g)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                    <td class="py-3.5 px-3.5">
                                        <div class="font-semibold text-slate-900">{{ $g['kategori'] }}</div>
                                        <div class="text-xs text-slate-500 mt-0.5">{{ $g['keterangan'] }}</div>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 whitespace-nowrap tabular-nums">
                                        {{ $g['kuota'] }} eks
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
