<x-app-layout>
    <div class="space-y-5">
        <!-- 1. Header Halaman (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Buku Besar Pembantu (Subsidiary Ledger)</h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">Rincian Pos Akun</span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Rincian mutasi transaksi debit, kredit, dan saldo berjalan per nomor rekening buku besar.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('accounting.gl.buku-besar.cetak', ['kode_perkiraan' => request('kode_perkiraan', $daftarAkun->first()->kode_perkiraan ?? '')]) }}" target="_blank" 
                   class="inline-flex items-center gap-2 h-10 px-4 text-xs font-semibold rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 text-white shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak Dokumen Resmi</span>
                </a>
            </div>
        </div>

        <!-- 2. Filter Akun Mandiri -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <form method="GET" action="{{ route('accounting.laporan.buku-besar') }}" class="flex flex-col sm:flex-row sm:items-end gap-3 flex-1">
                <div class="flex-1 min-w-[280px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Rekening Akun</label>
                    <select name="akun" class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                        @foreach($daftarPilihanAkun as $kode => $nama)
                            <option value="{{ $kode }}" {{ $akunPilihan == $kode ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                        Tampilkan Buku Besar
                    </button>
                    <a href="{{ route('accounting.laporan.buku-besar') }}" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- 3. Pure Tabel Mutasi Akun -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[350px]">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">No. Ref</th>
                            <th class="px-5 py-3 font-semibold">Keterangan Transaksi</th>
                            <th class="px-4 py-3 font-semibold text-right">Debet (Rp)</th>
                            <th class="px-4 py-3 font-semibold text-right">Kredit (Rp)</th>
                            <th class="px-5 py-3 font-semibold text-right">Saldo Berjalan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-sm whitespace-nowrap">
                        @foreach($mutasiBuku as $m)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3 text-slate-600">{{ $m->tanggal }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-kp-blue-700">{{ $m->ref }}</td>
                            <td class="px-5 py-3 text-slate-800">{{ $m->keterangan }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900">
                                {{ $m->debet > 0 ? 'Rp ' . number_format($m->debet, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900">
                                {{ $m->kredit > 0 ? 'Rp ' . number_format($m->kredit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-3 text-right font-mono font-bold text-kp-blue-700">
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