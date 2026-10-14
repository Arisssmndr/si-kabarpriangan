<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Buku Besar Pembantu (Subsidiary Ledger)</h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-purple-50 text-purple-700 border border-purple-200">Rincian Pos Akun</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rincian mutasi transaksi debit, kredit, dan saldo berjalan per nomor rekening buku besar.
                    </p>
                </div>
            </div>

            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-900 text-white shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                <span>Cetak Buku Besar</span>
            </button>
        </div>

        <!-- Filter Akun -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('accounting.laporan.buku-besar') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[280px]">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Pilih Rekening Akun</label>
                    <select name="akun" class="w-full text-xs rounded-lg border-slate-200 focus:border-purple-500 focus:ring-purple-500">
                        @foreach($daftarPilihanAkun as $kode => $nama)
                            <option value="{{ $kode }}" {{ $akunPilihan == $kode ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="py-2 px-3 text-xs font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900 transition">
                    Tampilkan Buku Besar
                </button>
            </form>
        </div>

        <!-- Tabel Mutasi Akun -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">No. Ref</th>
                            <th class="px-5 py-3 font-semibold">Keterangan Transaksi</th>
                            <th class="px-4 py-3 font-semibold text-right">Debet (Rp)</th>
                            <th class="px-4 py-3 font-semibold text-right">Kredit (Rp)</th>
                            <th class="px-5 py-3 font-semibold text-right">Saldo Berjalan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($mutasiBuku as $m)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3 font-bold text-slate-800">{{ $m->tanggal }}</td>
                            <td class="px-4 py-3 font-mono font-semibold text-purple-700">{{ $m->ref }}</td>
                            <td class="px-5 py-3 text-slate-700">{{ $m->keterangan }}</td>
                            <td class="px-4 py-3 text-right font-extrabold text-slate-900">
                                {{ $m->debet > 0 ? 'Rp ' . number_format($m->debet, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-extrabold text-slate-900">
                                {{ $m->kredit > 0 ? 'Rp ' . number_format($m->kredit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-3 text-right font-black text-purple-900 text-sm">
                                Rp {{ number_format($m->saldo, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
