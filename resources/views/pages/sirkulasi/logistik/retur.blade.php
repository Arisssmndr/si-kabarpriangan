<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-kp-blue-50 text-kp-blue-600 border border-kp-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-kp-blue-600 uppercase tracking-wider">Divisi Sirkulasi</span>
                        <span class="text-slate-300">&bull;</span>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Berita Acara Penerimaan Retur (BAPR)</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pencatatan fisik koran sisa/retur dari agen sebagai dasar pemotongan piutang di Divisi Kasir & Akuntansi.
                    </p>
                </div>
            </div>

            <button type="button" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg bg-kp-blue-600 hover:bg-kp-blue-700 text-white shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>+ Input BAPR Retur</span>
            </button>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 rounded-xl text-kp-blue-900 text-xs font-medium flex items-center gap-2">
                <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Summary KPI Retur (60-30-10) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500">Total Fisik Koran Retur Hari Ini</span>
                    <div class="text-xl font-bold text-slate-900 font-mono mt-1">{{ number_format($totalReturEks, 0, ',', '.') }} Eksemplar</div>
                </div>
                <span class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center font-bold text-xs">
                    Eks
                </span>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500">Total Nilai Pengurang Piutang Resmi</span>
                    <div class="text-xl font-bold text-slate-900 font-mono mt-1">Rp {{ number_format($totalNilaiRetur, 0, ',', '.') }}</div>
                </div>
                <span class="w-10 h-10 rounded-xl bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 flex items-center justify-center font-bold text-xs">
                    Rp
                </span>
            </div>
        </div>

        <!-- Tabel BAPR (Kolom Terpisah Lengkap) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left min-w-[1200px]">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] uppercase text-[10px] tracking-wider font-bold">
                        <tr>
                            <th class="px-4 py-3 whitespace-nowrap w-36">No. BAPR</th>
                            <th class="px-3.5 py-3 whitespace-nowrap w-28">Tanggal</th>
                            <th class="px-3.5 py-3 whitespace-nowrap w-28">Edisi Koran</th>
                            <th class="px-3.5 py-3 whitespace-nowrap w-28">Kode Agen</th>
                            <th class="px-4 py-3 min-w-[170px]">Nama Agen</th>
                            <th class="px-3.5 py-3 whitespace-nowrap min-w-[140px]">Jalur Ekspedisi</th>
                            <th class="px-3.5 py-3 text-right whitespace-nowrap w-24">Kirim (Eks)</th>
                            <th class="px-3.5 py-3 text-right whitespace-nowrap w-28">Retur Fisik</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap w-32">Nilai Pengurang</th>
                            <th class="px-3.5 py-3 text-center whitespace-nowrap w-32">Verifikasi Fisik</th>
                            <th class="px-4 py-3 text-center whitespace-nowrap w-32">Status Akuntansi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($baprList as $b)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- No. BAPR (Kolom Terpisah) -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-bold text-slate-900 font-mono text-xs">{{ $b->no_bapr }}</span>
                            </td>

                            <!-- Tanggal (Kolom Terpisah) -->
                            <td class="px-3.5 py-3 whitespace-nowrap font-mono text-slate-600 text-xs">
                                {{ $b->tanggal }}
                            </td>

                            <!-- Edisi Koran (Kolom Terpisah) -->
                            <td class="px-3.5 py-3 whitespace-nowrap font-mono text-slate-600 text-xs">
                                {{ $b->edisi_retur }}
                            </td>

                            <!-- Kode Agen (Kolom Terpisah) -->
                            <td class="px-3.5 py-3 whitespace-nowrap">
                                <span class="inline-block px-2 py-0.5 rounded font-mono font-bold text-slate-900 bg-slate-100 text-xs border border-slate-200">
                                    {{ $b->kode_agen }}
                                </span>
                            </td>

                            <!-- Nama Agen (Kolom Terpisah) -->
                            <td class="px-4 py-3 font-bold text-slate-900 text-xs">
                                {{ $b->nama_agen }}
                            </td>

                            <!-- Jalur Ekspedisi (Kolom Terpisah) -->
                            <td class="px-3.5 py-3 whitespace-nowrap text-slate-600 text-xs">
                                {{ $b->rute }}
                            </td>

                            <!-- Kirim -->
                            <td class="px-3.5 py-3 text-right font-mono text-slate-700 text-xs whitespace-nowrap">
                                {{ number_format($b->jumlah_kirim, 0, ',', '.') }}
                            </td>

                            <!-- Retur Fisik -->
                            <td class="px-3.5 py-3 text-right whitespace-nowrap font-mono font-bold text-slate-900 text-xs">
                                {{ $b->retur_eks }} eks <span class="text-[10px] text-slate-400 font-normal">({{ $b->persentase }}%)</span>
                            </td>

                            <!-- Nilai Pengurang -->
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900 text-xs whitespace-nowrap">
                                Rp {{ number_format($b->nilai_rupiah, 0, ',', '.') }}
                            </td>

                            <!-- Verifikasi Fisik -->
                            <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                    {{ $b->status_verifikasi }}
                                </span>
                            </td>

                            <!-- Status Akuntansi -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $b->status_akuntansi }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
