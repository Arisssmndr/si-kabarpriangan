<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Verifikasi Beban & Kas Kecil
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-amber-50 text-amber-700 border border-amber-200">
                            BKK Kasir
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Pemeriksaan bukti kas keluar dari kasir dan pembebanan ke pos bagan akun GL.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <div class="text-xs font-medium text-slate-600 bg-slate-50 px-3 py-2 rounded-xl border border-slate-200">
                    Belum Posting: <strong class="text-slate-900 font-mono">Rp {{ number_format($totalBelumPosting, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Beban Belum Diposting ke GL</span>
                <div class="text-xl font-bold text-slate-900 mt-1">Rp {{ number_format($totalBelumPosting, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Beban Terverifikasi & Posted</span>
                <div class="text-xl font-bold text-kp-blue-700 mt-1">Rp {{ number_format($totalSudahPosting, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Tabel Bukti Kas Keluar (BKK) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Bukti Kas Keluar</h2>
                    <p class="text-xs text-slate-500">Realisasi pengeluaran kas kecil kasir harian.</p>
                </div>
            </div>

            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] font-bold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">No. BKK</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">Diberikan Kepada</th>
                            <th class="py-3 px-3.5">Keterangan</th>
                            <th class="py-3 px-3.5 text-right">Nominal</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-48">Alokasi Beban (GL)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($daftarBeban as $key => $b)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-xs text-slate-400">{{ $key + 1 }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $b->no_bkk ?: ('BKK-' . $b->id) }}</td>
                                <td class="py-3 px-3.5">{{ \Carbon\Carbon::parse($b->tanggal_keluar)->format('d/m/Y') }}</td>
                                <td class="py-3 px-3.5">{{ $b->diberikan_kepada ?: ($b->nama_penerima ?: '-') }}</td>
                                <td class="py-3 px-3.5 max-w-xs truncate">{{ $b->keterangan }}</td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($b->nominal_keluar ?: $b->jumlah_keluar ?: 0, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md {{ $b->is_pulled_accounting ? 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ $b->is_pulled_accounting ? 'Posted' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    @if(!$b->is_pulled_accounting)
                                        <form method="POST" action="{{ route('accounting.beban.verifikasi.post', $b->id) }}" class="flex items-center gap-1.5">
                                            @csrf
                                            <select name="kode_perkiraan" required class="h-8 px-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 transition">
                                                <option value="">Pilih Akun...</option>
                                                @foreach($daftarAkunBeban as $akun)
                                                    <option value="{{ $akun->kode_perkiraan }}">
                                                        {{ $akun->kode_perkiraan }} - {{ $akun->nama_perkiraan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" 
                                                    title="Post ke Buku Besar"
                                                    class="h-8 px-2.5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-bold rounded-lg shadow-2xs transition cursor-pointer">
                                                Post
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs font-mono text-slate-500">{{ $b->kode_perkiraan ?: '6.00.01' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400 text-sm">
                                    Belum ada data pengeluaran kasir.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-t border-slate-200">
                {{ $daftarBeban->links() }}
            </div>
        </div>

    </div>
</x-app-layout>

