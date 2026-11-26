<x-app-layout>
    <div class="space-y-5">

        <!-- 1. Header Halaman (Palet 60-30-10 & Action Cetak) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Laporan Sub-Ledger Iklan
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Buku Pembantu
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Rekapitulasi mutasi kartu piutang iklan resmi per periode pembukuan.
                    </p>
                </div>
            </div>

            <!-- Tombol Aksi Utama: Cetak Dokumen Resmi (Membuka Tampilan Cetak Continuous Form / PDF) -->
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('accounting.cetak.iklan.cetak', request()->all()) }}" target="_blank" 
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
                <span class="text-xs text-slate-400 mt-0.5 block">Sub-Ledger {{ ucwords(str_replace('_', ' ', $tab)) }}</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Rentang Periode</span>
                <div class="text-sm font-bold text-slate-800 mt-2 font-mono">{{ \Carbon\Carbon::parse($tglMulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tglSelesai)->format('d/m/Y') }}</div>
                <span class="text-xs text-slate-400 mt-1 block">Mode: {{ ucfirst($periodeType ?? 'bulanan') }}</span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Akumulasi Nilai</span>
                <div class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
                <span class="text-xs text-slate-400 mt-0.5 block">Total rekap mutasi</span>
            </div>
        </div>

        <!-- 3. Filter Bar Mandiri (Selaras Divisi Iklan: Jenis Sub-Ledger, Preset & Tanggal) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('accounting.cetak.iklan') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-end">
                <div class="sm:col-span-4 lg:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Sub-Ledger</label>
                    <select name="tab" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="penjualan" {{ $tab === 'penjualan' ? 'selected' : '' }}>1. Penjualan Iklan</option>
                        <option value="pembayaran" {{ $tab === 'pembayaran' ? 'selected' : '' }}>2. Penerimaan Kasir</option>
                        <option value="piutang" {{ $tab === 'piutang' ? 'selected' : '' }}>3. Buku Piutang</option>
                        <option value="saldo_piutang" {{ $tab === 'saldo_piutang' ? 'selected' : '' }}>4. Saldo Piutang</option>
                        <option value="piutang_ae" {{ $tab === 'piutang_ae' ? 'selected' : '' }}>5. Piutang AE</option>
                        <option value="insentif_ae" {{ $tab === 'insentif_ae' ? 'selected' : '' }}>6. Insentif AE</option>
                        <option value="space_iklan" {{ $tab === 'space_iklan' ? 'selected' : '' }}>7. Space Iklan</option>
                        <option value="stock_opname" {{ $tab === 'stock_opname' ? 'selected' : '' }}>8. Stock Opname</option>
                        <option value="koreksi" {{ $tab === 'koreksi' ? 'selected' : '' }}>9. Koreksi CN</option>
                    </select>
                </div>

                <div class="sm:col-span-3 lg:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Preset Periode</label>
                    <select name="periode_type" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        <option value="harian" {{ ($periodeType ?? '') === 'harian' ? 'selected' : '' }}>Harian</option>
                        <option value="bulanan" {{ ($periodeType ?? 'bulanan') === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                        <option value="tahunan" {{ ($periodeType ?? '') === 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                    </select>
                </div>

                <div class="sm:col-span-5 lg:col-span-4 grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Dari Tanggal</label>
                        <input type="date" name="tgl_mulai" value="{{ $tglMulai }}" class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sampai Tanggal</label>
                        <input type="date" name="tgl_selesai" value="{{ $tglSelesai }}" class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    </div>
                </div>

                <div class="sm:col-span-12 lg:col-span-3 flex items-center gap-2">
                    <button type="submit" class="flex-1 h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-sm font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <span>Terapkan</span>
                    </button>
                    <a href="{{ route('accounting.cetak.iklan', ['tab' => $tab]) }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-sm font-semibold rounded-xl transition inline-flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- 4. Tabel Data Murni (Tanpa Header / Info yang Menyatu di Atas Tabel) -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                        @if($tab === 'penjualan')
                            <tr>
                                <th class="py-3 px-3 text-center w-10">No</th>
                                <th class="py-3 px-3 text-left">No. Faktur</th>
                                <th class="py-3 px-3 text-left">Tanggal</th>
                                <th class="py-3 px-3 text-left">No. Perki</th>
                                <th class="py-3 px-3 text-left">Pemasang / Klien</th>
                                <th class="py-3 px-3 text-center">Akun</th>
                                <th class="py-3 px-3 text-right">Tagihan Bruto</th>
                                <th class="py-3 px-3 text-right">PPN</th>
                                <th class="py-3 px-3 text-right">Total Netto</th>
                            </tr>
                        @elseif($tab === 'pembayaran')
                            <tr>
                                <th class="py-3 px-3 text-center w-10">No</th>
                                <th class="py-3 px-3 text-left">No. Kwitansi</th>
                                <th class="py-3 px-3 text-left">Tgl Bayar</th>
                                <th class="py-3 px-3 text-left">No. Faktur</th>
                                <th class="py-3 px-3 text-left">Tgl Faktur</th>
                                <th class="py-3 px-3 text-left">Pemasang</th>
                                <th class="py-3 px-3 text-left">Metode</th>
                                <th class="py-3 px-3 text-right">PPh 23</th>
                                <th class="py-3 px-3 text-right">Jumlah Bayar</th>
                            </tr>
                        @elseif($tab === 'piutang')
                            <tr>
                                <th class="py-3 px-3 text-center w-10">No</th>
                                <th class="py-3 px-3 text-left">No. Faktur</th>
                                <th class="py-3 px-3 text-left">Tgl Muat</th>
                                <th class="py-3 px-3 text-left">No. Perki</th>
                                <th class="py-3 px-3 text-left">Nama Pemasang</th>
                                <th class="py-3 px-3 text-center">Akun</th>
                                <th class="py-3 px-3 text-right">Total Faktur</th>
                                <th class="py-3 px-3 text-right">Sisa Piutang</th>
                            </tr>
                        @elseif($tab === 'saldo_piutang')
                            <tr>
                                <th class="py-3 px-3 text-center w-10">No</th>
                                <th class="py-3 px-3 text-left">No. Faktur</th>
                                <th class="py-3 px-3 text-left">Tgl Muat</th>
                                <th class="py-3 px-3 text-left">No. Perki</th>
                                <th class="py-3 px-3 text-left">Pemasang</th>
                                <th class="py-3 px-3 text-right">Tagihan</th>
                                <th class="py-3 px-3 text-right">Pembayaran</th>
                                <th class="py-3 px-3 text-right">Saldo Akhir</th>
                            </tr>
                        @elseif($tab === 'piutang_ae')
                            <tr>
                                <th class="py-3 px-3 text-center w-10">No</th>
                                <th class="py-3 px-3 text-left">Nama Account Executive</th>
                                <th class="py-3 px-3 text-center">Jml Faktur</th>
                                <th class="py-3 px-3 text-right">Total Tagihan</th>
                                <th class="py-3 px-3 text-right">Total Bayar</th>
                                <th class="py-3 px-3 text-right">Sisa Piutang</th>
                            </tr>
                        @elseif($tab === 'insentif_ae')
                            <tr>
                                <th class="py-3 px-3 text-center w-10">No</th>
                                <th class="py-3 px-3 text-left">No. Faktur</th>
                                <th class="py-3 px-3 text-left">Tanggal</th>
                                <th class="py-3 px-3 text-left">Nama AE</th>
                                <th class="py-3 px-3 text-left">Pemasang</th>
                                <th class="py-3 px-3 text-right">Nilai Iklan</th>
                                <th class="py-3 px-3 text-right">Insentif AE</th>
                            </tr>
                        @elseif($tab === 'space_iklan')
                            <tr>
                                <th class="py-3 px-3 text-center w-10">No</th>
                                <th class="py-3 px-3 text-left">No. Faktur</th>
                                <th class="py-3 px-3 text-left">Tanggal</th>
                                <th class="py-3 px-3 text-left">Halaman</th>
                                <th class="py-3 px-3 text-center">Kolom x MM</th>
                                <th class="py-3 px-3 text-right">Total MMK</th>
                                <th class="py-3 px-3 text-right">Total Nilai</th>
                            </tr>
                        @elseif($tab === 'stock_opname')
                            <tr>
                                <th class="py-3 px-3 text-center w-10">No</th>
                                <th class="py-3 px-3 text-left">Tanggal Opname</th>
                                <th class="py-3 px-3 text-left">Jenis Dokumen</th>
                                <th class="py-3 px-3 text-center">No Seri Awal</th>
                                <th class="py-3 px-3 text-center">No Seri Akhir</th>
                                <th class="py-3 px-3 text-right">Terpakai</th>
                                <th class="py-3 px-3 text-right">Sisa Stok</th>
                            </tr>
                        @elseif($tab === 'koreksi')
                            <tr>
                                <th class="py-3 px-3 text-center w-10">No</th>
                                <th class="py-3 px-3 text-left">No. Bukti CN</th>
                                <th class="py-3 px-3 text-left">Tanggal</th>
                                <th class="py-3 px-3 text-left">No. Faktur</th>
                                <th class="py-3 px-3 text-left">Alasan</th>
                                <th class="py-3 px-3 text-right">Nilai Koreksi</th>
                            </tr>
                        @endif
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($data as $key => $row)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-3 text-center text-slate-400">{{ $key + 1 }}</td>
                                @if($tab === 'penjualan')
                                    <td class="py-3 px-3 font-bold text-slate-900 font-mono">{{ $row->no_faktur }}</td>
                                    <td class="py-3 px-3">{{ \Carbon\Carbon::parse($row->tanggal_transaksikoran)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-3 font-mono text-slate-600">{{ $row->kode_perkiraan_display ?? '-' }}</td>
                                    <td class="py-3 px-3 font-medium text-slate-900">{{ $row->pelanggan?->nama_pelanggan ?? $row->nama_pemohon }}</td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md {{ ($row->tipe_akun_display ?? '') === 'Uang Muka' ? 'bg-slate-100 text-slate-800 border border-slate-200' : 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' }}">
                                            {{ $row->tipe_akun_display ?? 'Piutang' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono">Rp {{ number_format($row->nilai_transaksikoran ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-3 px-3 text-right font-mono">Rp {{ number_format($row->ppn_transaksikoran ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-3 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->totaltagihan_transaksikoran ?? 0, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'pembayaran')
                                    <td class="py-3 px-3 font-bold text-slate-900 font-mono">{{ $row->no_kwitansi ?? ('BKM-' . $row->id) }}</td>
                                    <td class="py-3 px-3">{{ \Carbon\Carbon::parse($row->tanggal_bayar)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-3 font-mono">{{ $row->transaksiKoran?->no_faktur ?? '-' }}</td>
                                    <td class="py-3 px-3 text-slate-500">{{ $row->transaksiKoran ? \Carbon\Carbon::parse($row->transaksiKoran->tanggal_transaksikoran)->format('d/m/Y') : '-' }}</td>
                                    <td class="py-3 px-3 font-medium text-slate-900">{{ $row->transaksiKoran?->nama_pemohon ?? '-' }}</td>
                                    <td class="py-3 px-3 uppercase text-slate-600">{{ $row->metode_pembayaran ?: 'Tunai' }}</td>
                                    <td class="py-3 px-3 text-right font-mono">Rp {{ number_format($row->pph23 ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-3 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->nominal_bayar ?? 0, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'piutang')
                                    <td class="py-3 px-3 font-bold text-slate-900 font-mono">{{ $row->no_faktur }}</td>
                                    <td class="py-3 px-3">{{ \Carbon\Carbon::parse($row->tanggal_transaksikoran)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-3 font-mono text-slate-600">{{ $row->kode_perkiraan_display ?? '-' }}</td>
                                    <td class="py-3 px-3 font-medium text-slate-900">{{ $row->pelanggan?->nama_pelanggan ?? $row->nama_pemohon }}</td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md {{ ($row->tipe_akun_display ?? '') === 'Uang Muka' ? 'bg-slate-100 text-slate-800 border border-slate-200' : 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' }}">
                                            {{ $row->tipe_akun_display ?? 'Piutang' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono">Rp {{ number_format($row->totaltagihan_transaksikoran ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-3 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->piutang_transaksikoran ?? 0, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'saldo_piutang')
                                    <td class="py-3 px-3 font-bold text-slate-900 font-mono">{{ $row->no_faktur }}</td>
                                    <td class="py-3 px-3">{{ \Carbon\Carbon::parse($row->tanggal_transaksikoran)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-3 font-mono text-slate-600">{{ $row->kode_perkiraan_display ?? '-' }}</td>
                                    <td class="py-3 px-3 font-medium text-slate-900">{{ $row->pelanggan?->nama_pelanggan ?? $row->nama_pemohon }}</td>
                                    <td class="py-3 px-3 text-right font-mono">Rp {{ number_format($row->totaltagihan_transaksikoran ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-3 px-3 text-right font-mono text-slate-900">Rp {{ number_format($row->jumlahbayar_transaksikoran ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-3 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->piutang_transaksikoran ?? 0, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'piutang_ae')
                                    <td class="py-3 px-3 font-bold text-slate-900">{{ $row->nama_ae }}</td>
                                    <td class="py-3 px-3 text-center">{{ number_format($row->jumlah_faktur) }}</td>
                                    <td class="py-3 px-3 text-right font-mono">Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}</td>
                                    <td class="py-3 px-3 text-right font-mono text-slate-900">Rp {{ number_format($row->total_bayar, 0, ',', '.') }}</td>
                                    <td class="py-3 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->sisa_piutang, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'insentif_ae')
                                    <td class="py-3 px-3 font-bold text-slate-900 font-mono">{{ $row->no_faktur }}</td>
                                    <td class="py-3 px-3">{{ \Carbon\Carbon::parse($row->tanggal_transaksikoran)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-3 font-semibold text-[#0A72AC]">{{ $row->sales_iklankoran ?: 'Kantor Pusat' }}</td>
                                    <td class="py-3 px-3 font-medium text-slate-900">{{ $row->nama_pemohon }}</td>
                                    <td class="py-3 px-3 text-right font-mono">Rp {{ number_format($row->totaltagihan_transaksikoran, 0, ',', '.') }}</td>
                                    <td class="py-3 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->insentif_transaksikoran, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'space_iklan')
                                    @php
                                        $mmk = ($row->lebar_kolom ?? 1) * ($row->tinggi_mm ?? 1) * ($row->total_muatkoran ?? 1);
                                    @endphp
                                    <td class="py-3 px-3 font-bold text-slate-900 font-mono">{{ $row->no_faktur }}</td>
                                    <td class="py-3 px-3">{{ \Carbon\Carbon::parse($row->tanggal_transaksikoran)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-3">{{ $row->halaman_iklan ?: 'Hal. Dalam' }}</td>
                                    <td class="py-3 px-3 text-center font-mono">{{ $row->lebar_kolom ?? 1 }} Kol x {{ $row->tinggi_mm ?? 1 }} mm</td>
                                    <td class="py-3 px-3 text-right font-bold font-mono">{{ number_format($mmk) }} mmk</td>
                                    <td class="py-3 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->totaltagihan_transaksikoran, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'stock_opname')
                                    <td class="py-3 px-3">{{ \Carbon\Carbon::parse($row->tanggal_opname)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-3 font-semibold text-slate-900">{{ $row->jenis_dokumen }}</td>
                                    <td class="py-3 px-3 text-center font-mono">{{ $row->nomor_seri_awal }}</td>
                                    <td class="py-3 px-3 text-center font-mono">{{ $row->nomor_seri_akhir }}</td>
                                    <td class="py-3 px-3 text-right font-mono text-slate-900">{{ number_format($row->jumlah_terpakai) }}</td>
                                    <td class="py-3 px-3 text-right font-bold font-mono text-slate-900">{{ number_format($row->jumlah_sisa) }}</td>
                                @elseif($tab === 'koreksi')
                                    <td class="py-3 px-3 font-bold text-slate-900 font-mono">{{ $row->nomor_bukti }}</td>
                                    <td class="py-3 px-3">{{ \Carbon\Carbon::parse($row->tanggal_koreksi)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-3 font-mono">{{ $row->nomor_faktur }}</td>
                                    <td class="py-3 px-3">{{ $row->alasan }}</td>
                                    <td class="py-3 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->jumlah_koreksi, 0, ',', '.') }}
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400">
                                    Tidak ada data untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-kp-blue-50/70 border-t-2 border-slate-300 font-bold">
                            <td colspan="{{ $tab === 'penjualan' ? '8' : ($tab === 'pembayaran' ? '8' : ($tab === 'piutang' ? '7' : ($tab === 'saldo_piutang' ? '7' : ($tab === 'piutang_ae' ? '5' : ($tab === 'insentif_ae' ? '6' : ($tab === 'space_iklan' ? '6' : ($tab === 'stock_opname' ? '6' : '5'))))))) }}" class="py-2.5 px-3 text-right uppercase">
                                TOTAL AKUMULASI
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-slate-900 text-xs font-bold">
                                Rp {{ number_format($totalNominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Rekap Pembayaran Berdasarkan Asal Bulan Penjualan (Sesuai Foto DOS 12.22.42) -->
            @if($tab === 'pembayaran' && !empty($rekapAsalBulan))
                <div class="p-5 border-t border-slate-200 bg-slate-50/50">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 mb-1">
                        Rekap Pembayaran Iklan HU. Kabar Priangan (Berdasarkan Asal Bulan Penjualan)
                    </h4>
                    <p class="text-xs text-slate-500 mb-3">Distribusi setoran kasir bulan berjalan untuk melunasi faktur iklan berjalan dan piutang bulan-bulan sebelumnya.</p>
                    <div class="max-w-md border border-slate-200 rounded-xl overflow-hidden bg-white shadow-xs">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs uppercase tracking-wider whitespace-nowrap">
                                <tr>
                                    <th class="py-3 px-3 text-left">Asal Bulan Faktur</th>
                                    <th class="py-3 px-3 text-center">Jml Transaksi</th>
                                    <th class="py-3 px-3 text-right">Jumlah Penerimaan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @php $sumRekap = 0; $countRekap = 0; @endphp
                                @foreach($rekapAsalBulan as $m)
                                    @php 
                                        $sumRekap += $m['total']; 
                                        $countRekap += $m['count'];
                                    @endphp
                                    <tr class="hover:bg-slate-50/60">
                                        <td class="py-3 px-3 font-semibold text-slate-800">{{ $m['label'] }}</td>
                                        <td class="py-3 px-3 text-center text-slate-500">{{ $m['count'] }}</td>
                                        <td class="py-3 px-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($m['total'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-kp-blue-50/80 font-bold border-t border-slate-200">
                                <tr>
                                    <td class="py-3 px-3 text-slate-900 uppercase">TOTAL KESELURUHAN</td>
                                    <td class="py-3 px-3 text-center font-mono">{{ $countRekap }}</td>
                                    <td class="py-3 px-3 text-right font-mono text-kp-blue-700">Rp {{ number_format($sumRekap, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
