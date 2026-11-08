<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (60-30-10 Palette) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Berita Acara Penerimaan Retur (BAPR)
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Pencatatan fisik koran sisa dari agen sebagai dasar pengurang piutang kasir dan akuntansi.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <button type="button"
                        class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Input BAPR Retur Baru</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- KPI Ringkasan Retur (Tanpa Lingkaran Ikon Dekoratif) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Nilai Pengurang Piutang</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalNilaiRetur, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Potongan tagihan resmi BAPR</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Fisik Koran Retur</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($totalReturEks, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-400">eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Akumulasi penerimaan hari ini</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jumlah Dokumen BAPR</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ count($baprList) }} <span class="text-xs font-sans font-normal text-slate-400">Berita Acara</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Tercatat pada sistem sirkulasi</span>
            </div>
        </div>

        <!-- Tabel BAPR -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Berita Acara Penerimaan Retur (BAPR)</h2>
                    <p class="text-xs text-slate-500">Rekap verifikasi fisik gudang dan status sinkronisasi akuntansi</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                    {{ count($baprList) }} Dokumen
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">No. BAPR</th>
                            <th class="py-3 px-3.5">Tanggal / Edisi</th>
                            <th class="py-3 px-3.5">Nama Agen</th>
                            <th class="py-3 px-3.5">Jalur Rute</th>
                            <th class="py-3 px-3.5 text-right">Kirim (Eks)</th>
                            <th class="py-3 px-3.5 text-right">Retur Fisik</th>
                            <th class="py-3 px-3.5 text-right">Nilai Pengurang</th>
                            <th class="py-3 px-3.5 text-center">Verifikasi Fisik</th>
                            <th class="py-3 px-3.5 text-center">Status Akuntansi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                        @forelse($baprList as $idx => $b)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">{{ $b->no_bapr }}</td>
                                <td class="py-3.5 px-3.5">
                                    <div class="font-mono text-slate-600">{{ $b->tanggal }}</div>
                                    <div class="text-xs text-slate-400">Edisi: {{ $b->edisi_retur }}</div>
                                </td>
                                <td class="py-3.5 px-3.5">
                                    <div class="font-semibold text-slate-900">{{ $b->nama_agen }}</div>
                                    <div class="text-xs font-mono text-slate-500">{{ $b->kode_agen }}</div>
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600">{{ $b->rute }}</td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">
                                    {{ number_format($b->jumlah_kirim, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">
                                    <span class="font-semibold text-slate-900">{{ $b->retur_eks }} eks</span>
                                    <span class="text-xs text-slate-400 ml-1">({{ $b->persentase }}%)</span>
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">
                                    Rp {{ number_format($b->nilai_rupiah, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if(str_contains($b->status_verifikasi, 'Terverifikasi'))
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            {{ $b->status_verifikasi }}
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $b->status_verifikasi }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $b->status_akuntansi }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-500 font-normal">
                                    Belum ada data Berita Acara Penerimaan Retur.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
