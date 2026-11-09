<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                        Entri Jatah Harian H+1
                    </h1>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Alokasi kuota koran per agen edisi {{ \Carbon\Carbon::parse($targetTanggal)->translatedFormat('l, d F Y') }}.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('sirkulasi.jatah.copy') }}" class="inline-flex items-center gap-2 h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                    </svg>
                    <span>Salin Pola Kemarin</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-slate-100 border border-slate-300 text-slate-800 rounded-xl text-xs sm:text-sm font-semibold shadow-2xs">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Toolbar Filter -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('sirkulasi.jatah.harian') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Terbit (H+1)</label>
                    <input type="date" name="tanggal" value="{{ $targetTanggal }}" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 font-mono focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                </div>
                <div class="sm:col-span-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Filter Rute / Armada</label>
                    <select name="rute" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="all">Semua Rute (Priangan Timur)</option>
                        @foreach($ruteList as $r)
                            <option value="{{ $r->kode }}" {{ $rutePilihan == $r->kode ? 'selected' : '' }}>{{ $r->kode }} - {{ $r->nama_rute }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="w-full h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Tampilkan
                    </button>
                </div>
            </form>
        </div>

        <!-- 5 Kartu KPI Jatah -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Langganan Tetap</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($totalLangganan, 0, ',', '.') }}</p>
                <span class="text-xs text-slate-400 mt-1 block">Eksemplar pelanggan tetap</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Eceran &amp; Lapak</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">{{ number_format($totalEceran, 0, ',', '.') }}</p>
                <span class="text-xs text-slate-400 mt-1 block">Eksemplar eceran kios</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Insidentil / Khusus</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($totalInsidentil, 0, ',', '.') }}</p>
                <span class="text-xs text-slate-400 mt-1 block">Eksemplar pesanan khusus</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jatah Gratis</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($totalGratis, 0, ',', '.') }}</p>
                <span class="text-xs text-slate-400 mt-1 block">Eksemplar bukti &amp; promosi</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Jatah H+1</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">{{ number_format($grandTotalJatah, 0, ',', '.') }}</p>
                <span class="text-xs text-slate-400 mt-1 block">Total eksemplar edisi</span>
            </div>
        </div>

        <!-- Form Tabel Entri Jatah -->
        <form method="POST" action="{{ route('sirkulasi.jatah.harian.store') }}">
            @csrf
            <input type="hidden" name="tanggal_jatah" value="{{ $targetTanggal }}">
            <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Matriks Alokasi Koran per Agen</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Edisi {{ \Carbon\Carbon::parse($targetTanggal)->translatedFormat('d F Y') }}</p>
                    </div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                            {{ count($matrixJatah) }} Agen
                        </span>
                        <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer inline-flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Simpan Jatah Harian</span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-3.5 w-28">Kode</th>
                                <th class="py-3 px-3.5">Nama Agen &amp; Rute</th>
                                <th class="py-3 px-2.5 text-right">Langganan</th>
                                <th class="py-3 px-2.5 text-right">Eceran</th>
                                <th class="py-3 px-2.5 text-right">Insidentil</th>
                                <th class="py-3 px-2.5 text-right">Gratis</th>
                                <th class="py-3 px-3.5 text-right w-28">Total Jatah</th>
                                <th class="py-3 px-3.5 min-w-[200px]">Catatan Distribusi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                            @foreach($matrixJatah as $idx => $item)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal w-12">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                        {{ $item->kode_agen }}
                                    </td>
                                    <td class="py-3.5 px-3.5">
                                        <div class="font-semibold text-slate-900">{{ $item->nama_agen }}</div>
                                        <div class="text-xs text-slate-500">{{ $item->rute_nama }}</div>
                                    </td>
                                    <td class="px-2.5 py-2.5 text-right">
                                        <input type="number" name="jatah[{{ $item->agen_id }}][langganan]" value="{{ $item->langganan }}" min="0"
                                               class="w-20 px-2 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-right font-mono font-semibold text-slate-900 tabular-nums focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                                    </td>
                                    <td class="px-2.5 py-2.5 text-right">
                                        <input type="number" name="jatah[{{ $item->agen_id }}][eceran]" value="{{ $item->eceran }}" min="0"
                                               class="w-20 px-2 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-right font-mono font-bold text-kp-blue-700 tabular-nums focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                                    </td>
                                    <td class="px-2.5 py-2.5 text-right">
                                        <input type="number" name="jatah[{{ $item->agen_id }}][insidentil]" value="{{ $item->insidentil }}" min="0"
                                               class="w-20 px-2 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-right font-mono font-semibold text-slate-900 tabular-nums focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                                    </td>
                                    <td class="px-2.5 py-2.5 text-right">
                                        <input type="number" name="jatah[{{ $item->agen_id }}][gratis]" value="{{ $item->gratis }}" min="0"
                                               class="w-20 px-2 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-right font-mono font-semibold text-slate-900 tabular-nums focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                                    </td>
                                    <td class="py-3.5 px-3.5 text-right font-mono font-bold text-kp-blue-700 tabular-nums">
                                        {{ number_format($item->total_jatah, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3.5 py-2.5">
                                        <input type="text" name="jatah[{{ $item->agen_id }}][keterangan]" value="{{ $item->keterangan }}"
                                               placeholder="Catatan drop point..."
                                               class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-mono text-sm font-semibold text-slate-900 whitespace-nowrap">
                            <tr>
                                <td colspan="3" class="py-3 px-3.5 text-right font-sans text-xs font-bold uppercase tracking-wider text-slate-700">Total Akumulasi</td>
                                <td class="py-3 px-2.5 text-right tabular-nums">{{ number_format($totalLangganan, 0, ',', '.') }}</td>
                                <td class="py-3 px-2.5 text-right text-kp-blue-700 font-bold tabular-nums">{{ number_format($totalEceran, 0, ',', '.') }}</td>
                                <td class="py-3 px-2.5 text-right tabular-nums">{{ number_format($totalInsidentil, 0, ',', '.') }}</td>
                                <td class="py-3 px-2.5 text-right tabular-nums">{{ number_format($totalGratis, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right font-bold text-kp-blue-700 tabular-nums">{{ number_format($grandTotalJatah, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-slate-400 font-normal">&mdash;</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </form>

    </div>
</x-app-layout>
