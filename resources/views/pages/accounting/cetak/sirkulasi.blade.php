<x-app-layout>
    <div class="space-y-5">

        <!-- 1. Header Halaman (Palet 60-30-10 & Action Cetak) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Laporan Sub-Ledger Sirkulasi
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Buku Pembantu
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Rekapitulasi mutasi kartu piutang agen dan distribusi koran resmi.
                    </p>
                </div>
            </div>

            <!-- Tombol Aksi Utama: Cetak Dokumen Resmi -->
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('accounting.cetak.sirkulasi.cetak', request()->all()) }}" target="_blank" 
                   class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Dokumen Resmi</span>
                </a>
            </div>
        </div>

        <!-- 2. Report / Summary / KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jumlah Baris Data</span>
                <div class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($data->count()) }} Data</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Sub-Ledger {{ ucwords($tab) }}</span>
            </div>
            @if($tab === 'penjualan')
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Oplah Eksemplar</span>
                    <div class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">{{ number_format($totalEksemplar) }} Eks</div>
                    <span class="text-xs text-slate-400 mt-0.5 block">Oplah koran periode berjalan</span>
                </div>
            @else
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Rentang Periode</span>
                    <div class="text-sm font-bold text-slate-800 mt-2 font-mono">{{ \Carbon\Carbon::parse($tglMulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tglSelesai)->format('d/m/Y') }}</div>
                    <span class="text-xs text-slate-400 mt-1 block">Data sirkulasi resmi</span>
                </div>
            @endif
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Nominal</span>
                <div class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Akumulasi mutasi sub-ledger</span>
            </div>
        </div>

        <!-- 3. Filter Bar Mandiri (Selaras Divisi Iklan: Jenis Sub-Ledger & Rentang Tanggal) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('accounting.cetak.sirkulasi') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-end">
                <div class="sm:col-span-5 lg:col-span-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Sub-Ledger</label>
                    <select name="tab" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="penjualan" {{ $tab === 'penjualan' ? 'selected' : '' }}>1. Penjualan &amp; Oplah Agen</option>
                        <option value="pembayaran" {{ $tab === 'pembayaran' ? 'selected' : '' }}>2. Setoran Kas Masuk</option>
                        <option value="piutang" {{ $tab === 'piutang' ? 'selected' : '' }}>3. Rekap Piutang Seluruh Agen</option>
                        <option value="retur" {{ $tab === 'retur' ? 'selected' : '' }}>4. Register Retur CN-SIR</option>
                    </select>
                </div>

                <div class="sm:col-span-4 lg:col-span-5 grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Dari Tanggal</label>
                        <input type="date" name="tgl_mulai" value="{{ $tglMulai }}" class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sampai Tanggal</label>
                        <input type="date" name="tgl_selesai" value="{{ $tglSelesai }}" class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                </div>

                <div class="sm:col-span-3 lg:col-span-3 flex items-center gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-sm font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <span>Terapkan</span>
                    </button>
                    <a href="{{ route('accounting.cetak.sirkulasi', ['tab' => $tab]) }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-sm font-semibold rounded-xl transition inline-flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- 4. Tabel Data Murni (Tanpa Header / Info yang Menyatu di Atas Tabel) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 uppercase font-semibold text-xs tracking-wider whitespace-nowrap">
                        @if($tab === 'penjualan')
                            <tr>
                                <th class="py-3 px-3.5 text-center w-10">No</th>
                                <th class="py-3 px-3.5 text-left">Kode Agen</th>
                                <th class="py-3 px-3.5 text-left">Nama Agen</th>
                                <th class="py-3 px-3.5 text-left">Wilayah</th>
                                <th class="py-3 px-3.5 text-center">Jatah/Hari</th>
                                <th class="py-3 px-3.5 text-right">Oplah Bln Ini</th>
                                <th class="py-3 px-3.5 text-right">Nilai Bruto</th>
                            </tr>
                        @elseif($tab === 'pembayaran')
                            <tr>
                                <th class="py-3 px-3.5 text-center w-10">No</th>
                                <th class="py-3 px-3.5 text-left">No. BKM</th>
                                <th class="py-3 px-3.5 text-left">Tanggal</th>
                                <th class="py-3 px-3.5 text-left">Kode Agen</th>
                                <th class="py-3 px-3.5 text-left">Nama Agen</th>
                                <th class="py-3 px-3.5 text-left">Metode</th>
                                <th class="py-3 px-3.5 text-right">Jumlah Setor</th>
                            </tr>
                        @elseif($tab === 'piutang')
                            <tr>
                                <th class="py-3 px-3.5 text-center w-10">No</th>
                                <th class="py-3 px-3.5 text-left">Kode Agen</th>
                                <th class="py-3 px-3.5 text-left">Nama Agen</th>
                                <th class="py-3 px-3.5 text-left">Wilayah</th>
                                <th class="py-3 px-3.5 text-left">No. Telepon</th>
                                <th class="py-3 px-3.5 text-right">Saldo Piutang Berjalan</th>
                            </tr>
                        @elseif($tab === 'retur')
                            <tr>
                                <th class="py-3 px-3.5 text-center w-10">No</th>
                                <th class="py-3 px-3.5 text-left">No. Bukti CN-SIR</th>
                                <th class="py-3 px-3.5 text-left">Tanggal</th>
                                <th class="py-3 px-3.5 text-left">Nama Agen</th>
                                <th class="py-3 px-3.5 text-left">Alasan / BAPR</th>
                                <th class="py-3 px-3.5 text-right">Nilai Koreksi</th>
                            </tr>
                        @endif
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-sm whitespace-nowrap">
                        @forelse($data as $key => $row)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-3.5 text-center text-slate-400 font-mono text-xs">{{ $key + 1 }}</td>
                                @if($tab === 'penjualan')
                                    @php
                                        $rc = $perAgenCalc[$row->id] ?? null;
                                        $eksTotal = $rc ? $rc['eks_total'] : (($row->jatah_default ?? 0) * ($estimasiHari ?? 30));
                                        $nomTotal = $rc ? $rc['nominal'] : ($eksTotal * ($hargaKoran ?? 3000));
                                    @endphp
                                    <td class="py-3 px-3.5 font-mono font-bold text-slate-900">{{ $row->kode_agen }}</td>
                                    <td class="py-3 px-3.5 font-medium text-slate-900">{{ $row->nama_agen }}</td>
                                    <td class="py-3 px-3.5">{{ $row->wilayah ?: 'Tasikmalaya' }}</td>
                                    <td class="py-3 px-3.5 text-center font-mono">{{ number_format($row->jatah_default ?? 0) }} eks</td>
                                    <td class="py-3 px-3.5 text-right font-mono">{{ number_format($eksTotal) }} eks</td>
                                    <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($nomTotal, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'pembayaran')
                                    <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $row->nomor_kas ?? $row->no_transaksi }}</td>
                                    <td class="py-3 px-3.5 text-slate-600">{{ \Carbon\Carbon::parse($row->tgl_transaksi ?? $row->tanggal_terima)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-3.5 font-mono text-slate-600">{{ $row->agen?->kode_agen ?? '-' }}</td>
                                    <td class="py-3 px-3.5 font-medium text-slate-900">{{ $row->agen?->nama_agen ?? $row->terima_dari }}</td>
                                    <td class="py-3 px-3.5 uppercase text-slate-600">{{ $row->metode_pembayaran ?: 'Kas' }}</td>
                                    <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->jumlah_dibayar ?? 0, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'piutang')
                                    <td class="py-3 px-3.5 font-mono font-bold text-slate-900">{{ $row->kode_agen }}</td>
                                    <td class="py-3 px-3.5 font-medium text-slate-900">{{ $row->nama_agen }}</td>
                                    <td class="py-3 px-3.5">{{ $row->wilayah ?: 'Tasikmalaya' }}</td>
                                    <td class="py-3 px-3.5 text-slate-600">{{ $row->no_telepon ?: '-' }}</td>
                                    <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->saldo_piutang ?? 0, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'retur')
                                    <td class="py-3 px-3.5 font-bold text-slate-900 font-mono">{{ $row->nomor_bukti }}</td>
                                    <td class="py-3 px-3.5 text-slate-600">{{ \Carbon\Carbon::parse($row->tanggal_koreksi)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-3.5 font-medium text-slate-900">{{ $row->nama_entitas }}</td>
                                    <td class="py-3 px-3.5 text-slate-600">{{ $row->alasan }}</td>
                                    <td class="py-3 px-3.5 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->jumlah_koreksi, 0, ',', '.') }}
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                    Tidak ada data untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-kp-blue-50/70 border-t-2 border-slate-300 font-bold text-sm">
                            <td colspan="{{ $tab === 'penjualan' ? '6' : ($tab === 'pembayaran' ? '6' : ($tab === 'piutang' ? '5' : '5')) }}" class="py-3 px-3.5 text-right uppercase text-xs">
                                TOTAL AKUMULASI
                            </td>
                            <td class="py-3 px-3.5 text-right font-mono text-slate-900 font-bold">
                                Rp {{ number_format($totalNominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>

    </div>
</x-app-layout>
