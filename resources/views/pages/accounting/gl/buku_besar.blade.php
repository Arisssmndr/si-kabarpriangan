<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Buku Besar Per Akun (General Ledger)
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-sky-50 text-kp-blue-700 border border-kp-blue-200">
                            Kartu Perkiraan
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Mutasi debet, kredit, dan saldo berjalan per pos akun buku besar.
                    </p>
                </div>
            </div>

            <!-- Pilih Akun -->
            <form method="GET" action="{{ route('accounting.gl.buku-besar') }}" class="flex items-center gap-2">
                <select name="kode_perkiraan" onchange="this.form.submit()" class="h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                    @foreach($daftarAkun as $item)
                        <option value="{{ $item->kode_perkiraan }}" {{ $akunPilihan === $item->kode_perkiraan ? 'selected' : '' }}>
                            {{ $item->kode_perkiraan }} - {{ $item->nama_perkiraan }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- 3 KPI Cards Buku Besar -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Akun Terpilih</span>
                <div class="text-base font-bold text-slate-900 mt-1 truncate">{{ $akun ? $akun->nama_perkiraan : 'Kas Operasional' }}</div>
                <div class="text-xs text-slate-400 mt-0.5 font-mono">{{ $akunPilihan }} (Normal: {{ strtoupper($akun?->posisi_normal ?? 'Debet') }})</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Mutasi Debet</span>
                <div class="text-xl font-bold text-emerald-600 mt-1 font-mono">Rp {{ number_format($totalDebet, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Mutasi Kredit</span>
                <div class="text-xl font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalKredit, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Tabel Mutasi Kartu Perkiraan -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Mutasi Rekening Akun</h2>
                    <p class="text-xs text-slate-500">Rincian jurnal transaksi yang mempengaruhi pos akun ini.</p>
                </div>
            </div>

            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">No. Jurnal</th>
                            <th class="py-3 px-3.5">Uraian Transaksi</th>
                            <th class="py-3 px-3.5 text-right w-36">Debet</th>
                            <th class="py-3 px-3.5 text-right w-36">Kredit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($mutasiBuku as $key => $m)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-xs text-slate-400">{{ $key + 1 }}</td>
                                <td class="py-3 px-3.5">{{ \Carbon\Carbon::parse($m->jurnalUmum?->tanggal ?? date('Y-m-d'))->format('d/m/Y') }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $m->jurnalUmum?->no_jurnal ?? '-' }}</td>
                                <td class="py-3 px-3.5 max-w-sm truncate">{{ $m->jurnalUmum?->keterangan ?? '-' }}</td>
                                <td class="py-3 px-3.5 text-right font-mono {{ $m->debet > 0 ? 'font-bold text-slate-900' : 'text-slate-400' }}">
                                    {{ $m->debet > 0 ? 'Rp ' . number_format($m->debet, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3 px-3.5 text-right font-mono {{ $m->kredit > 0 ? 'font-bold text-slate-900' : 'text-slate-400' }}">
                                    {{ $m->kredit > 0 ? 'Rp ' . number_format($m->kredit, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                    Belum ada mutasi transaksi untuk akun ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 border-t-2 border-slate-300 font-bold">
                            <td colspan="4" class="py-3 px-3.5 text-right uppercase text-xs">Total Mutasi</td>
                            <td class="py-3 px-3.5 text-right font-mono font-bold text-emerald-700">
                                Rp {{ number_format($totalDebet, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($totalKredit, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>

