<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Dashboard Sirkulasi
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Ringkasan oplah harian, jaringan agen, dan distribusi koran.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <div class="h-10 px-4 bg-slate-100 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 flex items-center gap-2 select-none">
                    <svg class="w-4 h-4 text-kp-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="font-bold text-slate-900">Edisi Cetak</span>
                    <span class="text-slate-300 font-normal hidden sm:inline">&bull;</span>
                    <span class="text-slate-600 font-mono font-normal hidden sm:inline">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                </div>
            </div>
        </div>

        <!-- 4 Kartu KPI Utama -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Oplah Cetak Harian</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    {{ number_format($oplahHarian, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Eksemplar cetak edisi berjalan</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jaringan Agen Aktif</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ $totalAgen ?? $totalAgenAktif ?? 80 }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Titik agen Priangan Timur</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Estimasi Retur Harian</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ $totalRetur }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Eksemplar (rasio 2,2% &bull; batas &lt; 5%)</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Efektivitas Distribusi</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    {{ $efektivitasDistribusi }}%
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Tingkat serapan pembaca &amp; pelanggan</span>
            </div>
        </div>

        <!-- Akses Cepat Modul Operasional -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('sirkulasi.jatah.edit') }}" class="bg-white border border-slate-200 hover:border-kp-blue-300 rounded-xl p-4 shadow-xs transition flex flex-col justify-between group">
                <div class="flex items-center justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-kp-blue-700 transition">Edit Data Penjualan</h3>
                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                        Jatah Harian
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-2">
                    Alokasi kuota koran per agen untuk edisi terbit harian.
                </p>
            </a>

            <a href="{{ route('sirkulasi.tabel.agen') }}" class="bg-white border border-slate-200 hover:border-kp-blue-300 rounded-xl p-4 shadow-xs transition flex flex-col justify-between group">
                <div class="flex items-center justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-kp-blue-700 transition">Data Agen &amp; Sub-Agen</h3>
                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                        Tabel Master
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-2">
                    Direktori agen koran dan kuota jatah default.
                </p>
            </a>

            <a href="{{ route('sirkulasi.cetak.nota-distribusi') }}" class="bg-white border border-slate-200 hover:border-kp-blue-300 rounded-xl p-4 shadow-xs transition flex flex-col justify-between group">
                <div class="flex items-center justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-kp-blue-700 transition">Nota &amp; Distribusi</h3>
                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                        Surat Jalan
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-2">
                    Cetak surat jalan armada subuh dan rekap bendel.
                </p>
            </a>

            <a href="{{ route('sirkulasi.jatah.tutup') }}" class="bg-white border border-slate-200 hover:border-kp-blue-300 rounded-xl p-4 shadow-xs transition flex flex-col justify-between group">
                <div class="flex items-center justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-kp-blue-700 transition">Tutup Data &amp; Memo</h3>
                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                        Cut-Off
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-2">
                    Kunci jatah harian/bulanan dan ajukan memo ke Akuntansi.
                </p>
            </a>
        </div>

        <!-- Tabel Distribusi Per Wilayah -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Distribusi Oplah Koran per Wilayah</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Alokasi cetak harian, jumlah agen, dan rasio retur per wilayah</p>
                </div>
                <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono w-fit">
                    {{ count($wilayah) }} Wilayah
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 text-center w-12">No</th>
                            <th class="py-3 px-3.5">Wilayah Distribusi</th>
                            <th class="py-3 px-3.5 text-right">Alokasi Oplah</th>
                            <th class="py-3 px-3.5 text-right">Jumlah Agen</th>
                            <th class="py-3 px-3.5 text-right">Rasio Retur</th>
                            <th class="py-3 px-3.5 text-center">Status Distribusi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @foreach($wilayah as $idx => $w)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal w-12">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    {{ $w['nama'] }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono font-semibold text-slate-900 tabular-nums">
                                    {{ number_format($w['oplah'], 0, ',', '.') }} <span class="text-slate-400 font-sans font-normal text-xs">eks</span>
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">
                                    {{ $w['agen'] }} <span class="text-slate-400 font-sans font-normal text-xs">agen</span>
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono text-slate-700 tabular-nums">
                                    {{ $w['retur_pct'] }}%
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        Terkirim Tepat Waktu
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
