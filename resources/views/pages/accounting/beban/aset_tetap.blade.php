<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Aset Tetap & Penyusutan
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Aktiva Tetap
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Pencatatan perolehan aset dan kalkulasi beban penyusutan metode garis lurus.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <div class="text-xs font-medium text-slate-600 bg-slate-50 px-3 py-2 rounded-xl border border-slate-200">
                    Beban Susut/Bulan: <strong class="text-slate-900 font-mono">Rp {{ number_format($totalSusutBulanan, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <!-- 3 KPI Cards Aset Tetap -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Harga Perolehan</span>
                <div class="text-xl font-bold text-slate-900 mt-1">Rp {{ number_format($totalHargaBeli, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Penyusutan Bulanan</span>
                <div class="text-xl font-bold text-slate-900 mt-1">Rp {{ number_format($totalSusutBulanan, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Estimasi Nilai Buku</span>
                <div class="text-xl font-bold text-kp-blue-700 mt-1">Rp {{ number_format($totalHargaBeli - ($totalSusutBulanan * 24), 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Tabel Register Aset Tetap -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Aktiva Tetap Perusahaan</h2>
                    <p class="text-xs text-slate-500">Mesin cetak, bangunan, kendaraan, dan peralatan kantor.</p>
                </div>
            </div>

            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] font-bold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">Kode Aset</th>
                            <th class="py-3 px-3.5">Nama Aktiva</th>
                            <th class="py-3 px-3.5">Kategori</th>
                            <th class="py-3 px-3.5">Tgl Perolehan</th>
                            <th class="py-3 px-3.5 text-right">Harga Beli</th>
                            <th class="py-3 px-3.5 text-center">Tarif/Tahun</th>
                            <th class="py-3 px-3.5 text-right">Susut/Bulan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @foreach($daftarAset as $key => $aset)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-xs text-slate-400">{{ $key + 1 }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $aset['kode'] }}</td>
                                <td class="py-3 px-3.5">{{ $aset['nama'] }}</td>
                                <td class="py-3 px-3.5">{{ $aset['kategori'] }}</td>
                                <td class="py-3 px-3.5">{{ \Carbon\Carbon::parse($aset['tgl_perolehan'])->format('d/m/Y') }}</td>
                                <td class="py-3 px-3.5 text-right font-mono">Rp {{ number_format($aset['harga_beli'], 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-center font-mono">{{ $aset['tarif_susut'] }}%</td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($aset['susut_bulanan'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 border-t-2 border-slate-300 font-bold">
                            <td colspan="5" class="py-3 px-3.5 text-right uppercase text-xs">Total Aktiva</td>
                            <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($totalHargaBeli, 0, ',', '.') }}
                            </td>
                            <td></td>
                            <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($totalSusutBulanan, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>

