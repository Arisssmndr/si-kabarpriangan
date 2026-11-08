<x-app-layout>
    <div class="space-y-6">

        <!-- Header Panel Laporan Eksekutif -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Laporan Konsolidasi
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Ringkasan kinerja seluruh divisi dalam satu tampilan.
                    </p>
                </div>
            </div>

            <!-- Tombol Cetak / Ekspor Dokumen -->
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.laporan.cetak', request()->query()) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.03-.48-2.062-.72-3.093m0 0l-3.328.777m3.328-.777l.777-3.328m10.743 14.152a9 9 0 11-12.728-12.728" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Lembar Direksi</span>
                </a>
            </div>
        </div>

        <!-- Filter Periode Analisa -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold text-slate-600">Periode:</span>

                    <a href="{{ route('admin.laporan.index', ['periode' => 'bulan_ini']) }}" 
                       class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $periode === 'bulan_ini' ? 'bg-kp-blue-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Bulan Ini
                    </a>
                    <a href="{{ route('admin.laporan.index', ['periode' => 'bulan_lalu']) }}" 
                       class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $periode === 'bulan_lalu' ? 'bg-kp-blue-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Bulan Lalu
                    </a>
                    <a href="{{ route('admin.laporan.index', ['periode' => 'tahun_ini', 'tahun' => date('Y')]) }}" 
                       class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $periode === 'tahun_ini' ? 'bg-kp-blue-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Tahun Ini ({{ date('Y') }})
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500">Periode aktif:</span>
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-800 rounded-md text-xs font-bold border border-slate-200">
                        {{ $periodeLabel }}
                    </span>
                </div>
            </form>
        </div>

        <!-- 1. EXECUTIVE SUMMARY KPI CARDS (5 Indikator Kunci) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <!-- Omset Konsolidasi -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Omset Konsolidasi</span>
                    <span class="w-7 h-7 rounded-lg bg-sky-50 text-kp-blue-700 border border-sky-200 flex items-center justify-center text-xs font-bold">
                        Rp
                    </span>
                </div>
                <div class="mt-3 text-lg font-extrabold text-slate-900 tracking-tight">
                    Rp {{ number_format($totalOmset, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-[11px] text-slate-500 flex items-center gap-1">
                    <span>{{ $totalTransaksi }} order iklan masuk</span>
                </div>
            </div>

            <!-- Kas Diterima -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Pembayaran Diterima</span>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center text-xs font-bold">
                        Rp
                    </span>
                </div>
                <div class="mt-3 text-lg font-extrabold text-emerald-600 tracking-tight">
                    Rp {{ number_format($totalBayar, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-[11px] text-emerald-700 font-semibold flex items-center gap-1">
                    <span>Sudah dibayar</span>
                </div>
            </div>

            <!-- Sisa Piutang Usaha -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Piutang Berjalan</span>
                    <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 flex items-center justify-center text-xs font-bold">
                        !
                    </span>
                </div>
                <div class="mt-3 text-lg font-extrabold text-rose-600 tracking-tight">
                    Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-[11px] text-slate-500 flex items-center gap-1">
                    <span>Tagihan belum terbayar</span>
                </div>
            </div>

            <!-- Tingkat Pembayaran -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Tagihan Terbayar</span>
                    <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center justify-center text-xs font-bold">
                        %
                    </span>
                </div>
                <div class="mt-3 text-lg font-extrabold text-indigo-600 tracking-tight">
                    {{ $collectionRate }}%
                </div>
                <div class="mt-2 text-[11px] text-slate-500 flex items-center gap-1">
                    <span>Dari total tagihan</span>
                </div>
            </div>

            <!-- Pertumbuhan -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Pertumbuhan</span>
                    <span class="w-7 h-7 rounded-lg {{ $growthPct >= 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }} border flex items-center justify-center text-xs font-bold">
                        {{ $growthPct >= 0 ? '+' : '-' }}
                    </span>
                </div>
                <div class="mt-3 text-lg font-extrabold {{ $growthPct >= 0 ? 'text-emerald-600' : 'text-rose-600' }} tracking-tight">
                    {{ $growthPct >= 0 ? '+' : '' }}{{ $growthPct }}%
                </div>
                <div class="mt-2 text-[11px] text-slate-500 truncate">
                    vs bulan lalu (Rp {{ number_format($omsetBulanLalu, 0, ',', '.') }})
                </div>
            </div>

        </div>

        <!-- 2. GRAFIK VISUALISASI INTERAKTIF (Chart.js) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Grafik 1: Tren Omset 12 Bulan (Line Chart) -->
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Tren Pertumbuhan Omset 12 Bulan</h2>
                            <p class="text-[11px] text-slate-500 font-normal">Pergerakan total nilai pemesanan iklan Koran, Online, dan Priangan TV tahun {{ $tahun }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">Tahun {{ $tahun }}</span>
                    </div>
                    <div class="mt-4 h-64">
                        <canvas id="chartTrendOmset"></canvas>
                    </div>
                </div>
            </div>

            <!-- Grafik 2: Komposisi Saluran Media (Doughnut Chart) -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Komposisi Kanal Media</h2>
                            <p class="text-[11px] text-slate-500 font-normal">Pangsa kontribusi omset di periode terpilih</p>
                        </div>
                    </div>
                    <div class="mt-4 h-52 relative flex items-center justify-center">
                        <canvas id="chartDoughnutMedia"></canvas>
                    </div>
                    <div class="mt-4 space-y-2 pt-3 border-t border-slate-100">
                        <div class="flex items-center justify-between text-xs">
                            <span class="flex items-center gap-2 text-slate-600">
                                <span class="w-3 h-3 rounded-full bg-sky-500"></span> Koran Cetak ({{ $pctKoran }}%)
                            </span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($omsetKoran, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="flex items-center gap-2 text-slate-600">
                                <span class="w-3 h-3 rounded-full bg-indigo-500"></span> Media Online ({{ $pctOnline }}%)
                            </span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($omsetOnline, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="flex items-center gap-2 text-slate-600">
                                <span class="w-3 h-3 rounded-full bg-teal-500"></span> Priangan TV ({{ $pctTv }}%)
                            </span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($omsetTv, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- 3. TABEL ANALISIS: TOP PEMASANG IKLAN & LEADERBOARD SALES AE -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Tabel 1: Top 10 Pemasang Iklan Terbesar -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Top 10 Pemasang Iklan Terbesar</h2>
                        <p class="text-[11px] text-slate-500 font-normal">Kontributor nilai transaksi tertinggi di periode ini</p>
                    </div>
                    <span class="text-[10px] font-bold text-sky-700 bg-sky-50 border border-sky-200 px-2 py-0.5 rounded">Akumulasi 3 Kanal</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="px-4 py-2.5 w-12 text-center">#</th>
                                <th class="px-4 py-2.5">Nama Pemasang / Klien</th>
                                <th class="px-4 py-2.5 text-center">Order</th>
                                <th class="px-4 py-2.5 text-right">Total Nilai (Rp)</th>
                                <th class="px-4 py-2.5 text-right">Lunas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($topPemasang as $idx => $p)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-2 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-2">
                                        <div class="font-bold text-slate-900 truncate max-w-[200px]">{{ $p['nama'] }}</div>
                                    </td>
                                    <td class="px-4 py-2 text-center font-semibold text-slate-600">{{ $p['order_count'] }}x</td>
                                    <td class="px-4 py-2 text-right font-extrabold text-slate-900">
                                        Rp {{ number_format($p['omset'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        @if($p['piutang'] <= 0)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Lunas</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Rp {{ number_format($p['piutang'], 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-slate-400">Belum ada transaksi di periode terpilih.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel 2: Leaderboard Performa Sales / Account Executive -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Performa Account Executive (Sales)</h2>
                        <p class="text-[11px] text-slate-500 font-normal">Peringkat kinerja penutupan transaksi sales iklan</p>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">Leaderboard</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="px-4 py-2.5 w-12 text-center">Rank</th>
                                <th class="px-4 py-2.5">Nama AE / Sales</th>
                                <th class="px-4 py-2.5 text-center">Order</th>
                                <th class="px-4 py-2.5 text-right">Omset (Rp)</th>
                                <th class="px-4 py-2.5 text-right">Realisasi Kas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($rankingSales as $idx => $s)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-2 text-center">
                                        @if($idx === 0 && $s['omset'] > 0)
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-400 text-white font-extrabold text-[11px]">🥇</span>
                                        @elseif($idx === 1 && $s['omset'] > 0)
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-300 text-slate-800 font-extrabold text-[11px]">🥈</span>
                                        @elseif($idx === 2 && $s['omset'] > 0)
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-600 text-white font-extrabold text-[11px]">🥉</span>
                                        @else
                                            <span class="font-bold text-slate-400">{{ $idx + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="font-bold text-slate-900">{{ $s['nama'] }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $s['kode'] }}</div>
                                    </td>
                                    <td class="px-4 py-2 text-center font-semibold text-slate-600">{{ $s['order_count'] }} order</td>
                                    <td class="px-4 py-2 text-right font-extrabold text-slate-900">
                                        Rp {{ number_format($s['omset'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-2 text-right font-semibold text-emerald-600">
                                        Rp {{ number_format($s['bayar'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-slate-400">Belum ada data Account Executive terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

    <!-- Chart.js Script via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Line Chart: Tren Omset 12 Bulan
            const ctxTrend = document.getElementById('chartTrendOmset');
            if (ctxTrend) {
                new Chart(ctxTrend, {
                    type: 'line',
                    data: {
                        labels: @json($trendLabels),
                        datasets: [
                            {
                                label: 'Total Omset',
                                data: @json($trendTotal),
                                borderColor: '#0A72AC',
                                backgroundColor: 'rgba(10, 114, 172, 0.08)',
                                fill: true,
                                tension: 0.3,
                                borderWidth: 2.5,
                                pointRadius: 3,
                            },
                            {
                                label: 'Kas Masuk',
                                data: @json($trendBayar),
                                borderColor: '#10B981',
                                borderDash: [4, 4],
                                tension: 0.3,
                                borderWidth: 2,
                                pointRadius: 2,
                            },
                            {
                                label: 'Piutang',
                                data: @json($trendPiutang),
                                borderColor: '#F43F5E',
                                borderDash: [2, 2],
                                tension: 0.3,
                                borderWidth: 1.5,
                                pointRadius: 2,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 12,
                                    font: { size: 11, weight: '600' }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        if (value >= 1000000000) return (value / 1000000000).toFixed(1) + ' M';
                                        if (value >= 1000000) return (value / 1000000).toFixed(0) + ' Jt';
                                        return value;
                                    },
                                    font: { size: 10 }
                                },
                                grid: { color: '#f1f5f9' }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 10 } }
                            }
                        }
                    }
                });
            }

            // 2. Doughnut Chart: Komposisi Saluran Media
            const ctxDoughnut = document.getElementById('chartDoughnutMedia');
            if (ctxDoughnut) {
                const totalOmsetVal = {{ $totalOmset }};
                new Chart(ctxDoughnut, {
                    type: 'doughnut',
                    data: {
                        labels: ['Koran Cetak', 'Media Online', 'Priangan TV'],
                        datasets: [{
                            data: totalOmsetVal > 0 ? [{{ $omsetKoran }}, {{ $omsetOnline }}, {{ $omsetTv }}] : [1, 0, 0],
                            backgroundColor: ['#0284C7', '#6366F1', '#14B8A6'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }
        });
    </script>
</x-app-layout>
