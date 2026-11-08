<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman Bersih (Palet 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Hitung Penjualan Iklan
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-sky-50 text-kp-blue-700 border border-kp-blue-200">
                            Kartu Iklan
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5 truncate">
                        Perhitungan omset harian dan pembentukan jurnal piutang multi-kanal.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if($sudahDihitung)
                    <div class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-2 rounded-xl border border-emerald-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Sudah Dihitung ({{ $logHitung?->nomor_jurnal ?? 'Terekam GL' }})</span>
                    </div>
                @else
                    <div class="text-xs font-semibold text-amber-700 bg-amber-50 px-3 py-2 rounded-xl border border-amber-200 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-[#FFB900]"></span>
                        <span>Belum Dihitung</span>
                    </div>
                @endif
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

        <!-- Filter Tanggal & Action Bar (Terpisah Bersih, Bebas Nested Form) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <form method="GET" action="{{ route('accounting.iklan.hitung-penjualan') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Transaksi</label>
                    <input type="date" 
                           name="tanggal" 
                           value="{{ $tanggalPilihan }}" 
                           class="h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition" />
                </div>
                <button type="submit" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center cursor-pointer">
                    Filter Data
                </button>
            </form>

            <div>
                @if(!$sudahDihitung)
                    <form method="POST" action="{{ route('accounting.iklan.proses-hitung-penjualan') }}">
                        @csrf
                        <input type="hidden" name="tanggal" value="{{ $tanggalPilihan }}">
                        <button type="submit" 
                                onclick="return confirm('Jalankan hitung penjualan iklan tanggal {{ $tanggalPilihan }}? Data jurnal otomatis akan dibentuk.')"
                                class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            <span>Hitung & Posting GL</span>
                        </button>
                    </form>
                @else
                    <button type="button" disabled class="h-10 px-4 bg-slate-100 border border-slate-200 text-slate-400 font-semibold text-xs rounded-xl cursor-not-allowed">
                        Data Sudah Dihitung
                    </button>
                @endif
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Jumlah Transaksi</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">{{ number_format($totalCount) }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Omset Bruto</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalBruto, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">PPN 11%</span>
                <div class="text-lg font-bold text-slate-900 mt-1 font-mono">Rp {{ number_format($totalPpn, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Piutang Netto</span>
                <div class="text-lg font-bold text-kp-blue-700 mt-1 font-mono">Rp {{ number_format($totalNetto, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Tabel Data Faktur -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">No. Faktur</th>
                            <th class="py-3 px-3.5">Tanggal</th>
                            <th class="py-3 px-3.5">No. Perkiraan</th>
                            <th class="py-3 px-3.5">Pemasang</th>
                            <th class="py-3 px-3.5">Jenis Iklan</th>
                            <th class="py-3 px-3.5 text-right">Tarif Bruto</th>
                            <th class="py-3 px-3.5 text-right">PPN</th>
                            <th class="py-3 px-3.5 text-right">Total Netto</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                        @forelse($daftarFaktur as $key => $f)
                            @php
                                $noFaktur = $f->nofakturkoran ?? $f->no_faktur ?? '-';
                                $tgl = $f->tanggal_muatkoran ?? $f->tanggal_transaksikoran ?? $f->tanggal;
                                $pemasang = $f->nama_pemasangkoran ?? $f->nama_pemasang ?? $f->nama_pemohon ?? '-';
                                $kategori = $f->kategori_form ?? 'Koran';
                                $bruto = (float) ($f->harga_transaksikoran ?? $f->subtotal ?? 0);
                                $ppn = (float) ($f->ppn_transaksikoran ?? $f->ppn ?? 0);
                                $netto = (float) ($f->totaltagihan_transaksikoran ?? $f->total_akhir ?? $bruto);
                                $piutang = (float) ($f->piutang_transaksikoran ?? 0);
                                $isLunas = ($piutang <= 0);
                                $tipeAkun = $f->tipe_akun_display ?? ($isLunas ? 'Uang Muka' : 'Piutang');
                                $kodePerkiraan = $f->kode_perkiraan_display ?? ($isLunas ? '2.00.01.01.01.00001' : '1.00.04.01.01.00001');
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3.5 text-center text-slate-400 font-mono">{{ $key + 1 }}</td>
                                <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $noFaktur }}</td>
                                <td class="py-3 px-3.5 text-slate-600">{{ $tgl ? \Carbon\Carbon::parse($tgl)->format('d/m/Y') : '-' }}</td>
                                <td class="py-3 px-3.5 font-mono text-[11px]">
                                    @if($tipeAkun === 'Uang Muka')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold" title="Akun Uang Muka Iklan">
                                            {{ $kodePerkiraan }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-sky-50 text-kp-blue-700 border border-kp-blue-200 font-semibold" title="Akun Piutang Iklan">
                                            {{ $kodePerkiraan }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 max-w-xs truncate text-slate-800">{{ $pemasang }}</td>
                                <td class="py-3 px-3.5 text-slate-600">{{ $kategori }}</td>
                                <td class="py-3 px-3.5 text-right font-mono text-slate-700">Rp {{ number_format($bruto, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right font-mono text-slate-700">Rp {{ number_format($ppn, 0, ',', '.') }}</td>
                                <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($netto, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md {{ $isLunas ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ $isLunas ? 'Lunas (UM)' : 'Piutang' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-8 text-center text-slate-400 text-xs">
                                    Tidak ada data transaksi iklan pada tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
