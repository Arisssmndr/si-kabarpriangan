<x-app-layout>
    <div class="space-y-6">

        <!-- Top Action & Filter Bar (Print Hidden) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Laporan Sub-Ledger Iklan
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-[#0A72AC] border border-kp-blue-200">
                            Cetak Kartu
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Rekapitulasi mutasi kartu piutang iklan resmi per periode pembukuan.
                    </p>
                </div>
            </div>

            <!-- Periode Quick Presets & Cetak Button -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <div class="flex items-center bg-slate-100 p-0.5 rounded-lg border border-slate-200">
                    <a href="{{ route('accounting.cetak.iklan', ['tab' => $tab, 'periode_type' => 'harian']) }}"
                       class="px-2.5 py-1 text-xs font-semibold rounded-md transition {{ ($periodeType ?? '') === 'harian' ? 'bg-white text-[#0A72AC] shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Harian
                    </a>
                    <a href="{{ route('accounting.cetak.iklan', ['tab' => $tab, 'periode_type' => 'bulanan']) }}"
                       class="px-2.5 py-1 text-xs font-semibold rounded-md transition {{ ($periodeType ?? 'bulanan') === 'bulanan' ? 'bg-white text-[#0A72AC] shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Bulanan
                    </a>
                    <a href="{{ route('accounting.cetak.iklan', ['tab' => $tab, 'periode_type' => 'tahunan']) }}"
                       class="px-2.5 py-1 text-xs font-semibold rounded-md transition {{ ($periodeType ?? '') === 'tahunan' ? 'bg-white text-[#0A72AC] shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Tahunan
                    </a>
                </div>

                <form method="GET" action="{{ route('accounting.cetak.iklan') }}" class="flex flex-wrap items-center gap-1.5">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <input type="date" name="tgl_mulai" value="{{ $tglMulai }}" class="h-9 px-2.5 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-[#0A72AC] focus:border-[#0A72AC] text-slate-800">
                    <span class="text-xs text-slate-400">s/d</span>
                    <input type="date" name="tgl_selesai" value="{{ $tglSelesai }}" class="h-9 px-2.5 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-[#0A72AC] focus:border-[#0A72AC] text-slate-800">
                    <button type="submit" class="h-9 px-3 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition cursor-pointer">
                        Filter
                    </button>
                </form>

                <a href="{{ route('accounting.cetak.iklan.cetak', request()->all()) }}" target="_blank" class="inline-flex items-center gap-1.5 h-9 px-3.5 bg-[#0A72AC] hover:bg-[#085d8d] text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Dokumen Resmi</span>
                </a>
            </div>
        </div>

        <!-- 9 Sub-Ledger Tabs (Standar Menu DOS KARTU.EXE Foto 12.22.24) -->
        <div class="print:hidden border-b border-slate-200 overflow-x-auto">
            <nav class="flex items-center gap-1 whitespace-nowrap min-w-max pb-1">
                @php
                    $tabs = [
                        'penjualan'     => '1. Penjualan',
                        'pembayaran'    => '2. Penerimaan Kasir',
                        'piutang'       => '3. Buku Piutang',
                        'saldo_piutang' => '4. Saldo Piutang',
                        'piutang_ae'    => '5. Piutang AE',
                        'insentif_ae'   => '6. Insentif AE',
                        'space_iklan'   => '7. Space Iklan',
                        'stock_opname'  => '8. Stock Opname',
                        'koreksi'       => '9. Koreksi CN',
                    ];
                @endphp
                @foreach($tabs as $k => $label)
                    <a href="{{ route('accounting.cetak.iklan', ['tab' => $k, 'tgl_mulai' => $tglMulai, 'tgl_selesai' => $tglSelesai, 'periode_type' => $periodeType ?? 'bulanan']) }}"
                       class="px-3.5 py-2 text-xs font-semibold border-b-2 transition {{ $tab === $k ? 'border-[#0A72AC] text-[#0A72AC] font-bold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>

        <!-- Metric Summary Card (Print Hidden) -->
        <div class="print:hidden grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Jumlah Baris</span>
                <div class="text-lg font-bold text-slate-900 mt-1">{{ number_format($data->count()) }} Data</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Periode</span>
                <div class="text-sm font-bold text-slate-800 mt-1">{{ \Carbon\Carbon::parse($tglMulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tglSelesai)->format('d/m/Y') }}</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Akumulasi Nilai</span>
                <div class="text-lg font-bold text-[#0A72AC] mt-1">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Dokumen Cetak Resmi (Standar Kabar Priangan) -->
        <div class="report-print-container bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-xs text-slate-800 print:border-none print:shadow-none print:p-0">
            
            <!-- Kop Dokumen Resmi -->
            <div class="border-b-2 border-slate-900 pb-3 mb-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black tracking-wider uppercase text-slate-900">PT Berkah Pikiran Rakyat</h2>
                        <h3 class="text-sm font-bold text-[#0A72AC] tracking-wide uppercase">Harian Umum Kabar Priangan</h3>
                        <p class="text-[11px] text-slate-500">Jl. Dr. Sukardjo No. 47, Tawangsari, Kec. Tawang, Kota Tasikmalaya 46112</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-black uppercase text-slate-900">
                            LAPORAN {{ strtoupper(str_replace('_', ' ', $tab)) }} IKLAN
                        </div>
                        <div class="text-xs font-semibold text-[#0A72AC] mt-0.5">
                            Periode: {{ \Carbon\Carbon::parse($tglMulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tglSelesai)->format('d/m/Y') }}
                        </div>
                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                            Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Data Laporan Resmi -->
            <div class="mb-6 overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] uppercase font-bold text-[10px] tracking-wider">
                        @if($tab === 'penjualan')
                            <tr>
                                <th class="py-2.5 px-3 text-center w-10">No</th>
                                <th class="py-2.5 px-3 text-left">No. Faktur</th>
                                <th class="py-2.5 px-3 text-left">Tanggal</th>
                                <th class="py-2.5 px-3 text-left">No. Perki</th>
                                <th class="py-2.5 px-3 text-left">Pemasang / Klien</th>
                                <th class="py-2.5 px-3 text-center">Akun</th>
                                <th class="py-2.5 px-3 text-right">Tagihan Bruto</th>
                                <th class="py-2.5 px-3 text-right">PPN</th>
                                <th class="py-2.5 px-3 text-right">Total Netto</th>
                            </tr>
                        @elseif($tab === 'pembayaran')
                            <tr>
                                <th class="py-2.5 px-3 text-center w-10">No</th>
                                <th class="py-2.5 px-3 text-left">No. Kwitansi</th>
                                <th class="py-2.5 px-3 text-left">Tgl Bayar</th>
                                <th class="py-2.5 px-3 text-left">No. Faktur</th>
                                <th class="py-2.5 px-3 text-left">Tgl Faktur</th>
                                <th class="py-2.5 px-3 text-left">Pemasang</th>
                                <th class="py-2.5 px-3 text-left">Metode</th>
                                <th class="py-2.5 px-3 text-right">PPh 23</th>
                                <th class="py-2.5 px-3 text-right">Jumlah Bayar</th>
                            </tr>
                        @elseif($tab === 'piutang')
                            <tr>
                                <th class="py-2.5 px-3 text-center w-10">No</th>
                                <th class="py-2.5 px-3 text-left">No. Faktur</th>
                                <th class="py-2.5 px-3 text-left">Tgl Muat</th>
                                <th class="py-2.5 px-3 text-left">No. Perki</th>
                                <th class="py-2.5 px-3 text-left">Nama Pemasang</th>
                                <th class="py-2.5 px-3 text-center">Akun</th>
                                <th class="py-2.5 px-3 text-right">Total Faktur</th>
                                <th class="py-2.5 px-3 text-right">Sisa Piutang</th>
                            </tr>
                        @elseif($tab === 'saldo_piutang')
                            <tr>
                                <th class="py-2.5 px-3 text-center w-10">No</th>
                                <th class="py-2.5 px-3 text-left">No. Faktur</th>
                                <th class="py-2.5 px-3 text-left">Tgl Muat</th>
                                <th class="py-2.5 px-3 text-left">No. Perki</th>
                                <th class="py-2.5 px-3 text-left">Pemasang</th>
                                <th class="py-2.5 px-3 text-right">Tagihan</th>
                                <th class="py-2.5 px-3 text-right">Pembayaran</th>
                                <th class="py-2.5 px-3 text-right">Saldo Akhir</th>
                            </tr>
                        @elseif($tab === 'piutang_ae')
                            <tr>
                                <th class="py-2.5 px-3 text-center w-10">No</th>
                                <th class="py-2.5 px-3 text-left">Nama Account Executive</th>
                                <th class="py-2.5 px-3 text-center">Jml Faktur</th>
                                <th class="py-2.5 px-3 text-right">Total Tagihan</th>
                                <th class="py-2.5 px-3 text-right">Total Bayar</th>
                                <th class="py-2.5 px-3 text-right">Sisa Piutang</th>
                            </tr>
                        @elseif($tab === 'insentif_ae')
                            <tr>
                                <th class="py-2.5 px-3 text-center w-10">No</th>
                                <th class="py-2.5 px-3 text-left">No. Faktur</th>
                                <th class="py-2.5 px-3 text-left">Tanggal</th>
                                <th class="py-2.5 px-3 text-left">Nama AE</th>
                                <th class="py-2.5 px-3 text-left">Pemasang</th>
                                <th class="py-2.5 px-3 text-right">Nilai Iklan</th>
                                <th class="py-2.5 px-3 text-right">Insentif AE</th>
                            </tr>
                        @elseif($tab === 'space_iklan')
                            <tr>
                                <th class="py-2.5 px-3 text-center w-10">No</th>
                                <th class="py-2.5 px-3 text-left">No. Faktur</th>
                                <th class="py-2.5 px-3 text-left">Tanggal</th>
                                <th class="py-2.5 px-3 text-left">Halaman</th>
                                <th class="py-2.5 px-3 text-center">Kolom x MM</th>
                                <th class="py-2.5 px-3 text-right">Total MMK</th>
                                <th class="py-2.5 px-3 text-right">Total Nilai</th>
                            </tr>
                        @elseif($tab === 'stock_opname')
                            <tr>
                                <th class="py-2.5 px-3 text-center w-10">No</th>
                                <th class="py-2.5 px-3 text-left">Tanggal Opname</th>
                                <th class="py-2.5 px-3 text-left">Jenis Dokumen</th>
                                <th class="py-2.5 px-3 text-center">No Seri Awal</th>
                                <th class="py-2.5 px-3 text-center">No Seri Akhir</th>
                                <th class="py-2.5 px-3 text-right">Terpakai</th>
                                <th class="py-2.5 px-3 text-right">Sisa Stok</th>
                            </tr>
                        @elseif($tab === 'koreksi')
                            <tr>
                                <th class="py-2.5 px-3 text-center w-10">No</th>
                                <th class="py-2.5 px-3 text-left">No. Bukti CN</th>
                                <th class="py-2.5 px-3 text-left">Tanggal</th>
                                <th class="py-2.5 px-3 text-left">No. Faktur</th>
                                <th class="py-2.5 px-3 text-left">Alasan</th>
                                <th class="py-2.5 px-3 text-right">Nilai Koreksi</th>
                            </tr>
                        @endif
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($data as $key => $row)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2 px-3 text-center text-slate-400">{{ $key + 1 }}</td>
                                @if($tab === 'penjualan')
                                    <td class="py-2 px-3 font-bold text-slate-900 font-mono">{{ $row->no_faktur }}</td>
                                    <td class="py-2 px-3">{{ \Carbon\Carbon::parse($row->tanggal_transaksikoran)->format('d/m/Y') }}</td>
                                    <td class="py-2 px-3 font-mono text-slate-600">{{ $row->kode_perkiraan_display ?? '-' }}</td>
                                    <td class="py-2 px-3 font-medium text-slate-900">{{ $row->pelanggan?->nama_pelanggan ?? $row->nama_pemohon }}</td>
                                    <td class="py-2 px-3 text-center">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ ($row->tipe_akun_display ?? '') === 'Uang Muka' ? 'bg-amber-100 text-amber-800' : 'bg-kp-blue-100 text-kp-blue-800' }}">
                                            {{ $row->tipe_akun_display ?? 'Piutang' }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3 text-right font-mono">Rp {{ number_format($row->nilai_transaksikoran ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-2 px-3 text-right font-mono">Rp {{ number_format($row->ppn_transaksikoran ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-2 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->totaltagihan_transaksikoran ?? 0, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'pembayaran')
                                    <td class="py-2 px-3 font-bold text-slate-900 font-mono">{{ $row->no_kwitansi ?? ('BKM-' . $row->id) }}</td>
                                    <td class="py-2 px-3">{{ \Carbon\Carbon::parse($row->tanggal_bayar)->format('d/m/Y') }}</td>
                                    <td class="py-2 px-3 font-mono">{{ $row->transaksiKoran?->no_faktur ?? '-' }}</td>
                                    <td class="py-2 px-3 text-slate-500">{{ $row->transaksiKoran ? \Carbon\Carbon::parse($row->transaksiKoran->tanggal_transaksikoran)->format('d/m/Y') : '-' }}</td>
                                    <td class="py-2 px-3 font-medium text-slate-900">{{ $row->transaksiKoran?->nama_pemohon ?? '-' }}</td>
                                    <td class="py-2 px-3 uppercase text-slate-600">{{ $row->metode_pembayaran ?: 'Tunai' }}</td>
                                    <td class="py-2 px-3 text-right font-mono">Rp {{ number_format($row->pph23 ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-2 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->nominal_bayar ?? 0, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'piutang')
                                    <td class="py-2 px-3 font-bold text-slate-900 font-mono">{{ $row->no_faktur }}</td>
                                    <td class="py-2 px-3">{{ \Carbon\Carbon::parse($row->tanggal_transaksikoran)->format('d/m/Y') }}</td>
                                    <td class="py-2 px-3 font-mono text-slate-600">{{ $row->kode_perkiraan_display ?? '-' }}</td>
                                    <td class="py-2 px-3 font-medium text-slate-900">{{ $row->pelanggan?->nama_pelanggan ?? $row->nama_pemohon }}</td>
                                    <td class="py-2 px-3 text-center">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ ($row->tipe_akun_display ?? '') === 'Uang Muka' ? 'bg-amber-100 text-amber-800' : 'bg-kp-blue-100 text-kp-blue-800' }}">
                                            {{ $row->tipe_akun_display ?? 'Piutang' }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3 text-right font-mono">Rp {{ number_format($row->totaltagihan_transaksikoran ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-2 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->piutang_transaksikoran ?? 0, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'saldo_piutang')
                                    <td class="py-2 px-3 font-bold text-slate-900 font-mono">{{ $row->no_faktur }}</td>
                                    <td class="py-2 px-3">{{ \Carbon\Carbon::parse($row->tanggal_transaksikoran)->format('d/m/Y') }}</td>
                                    <td class="py-2 px-3 font-mono text-slate-600">{{ $row->kode_perkiraan_display ?? '-' }}</td>
                                    <td class="py-2 px-3 font-medium text-slate-900">{{ $row->pelanggan?->nama_pelanggan ?? $row->nama_pemohon }}</td>
                                    <td class="py-2 px-3 text-right font-mono">Rp {{ number_format($row->totaltagihan_transaksikoran ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-2 px-3 text-right font-mono text-slate-900">Rp {{ number_format($row->jumlahbayar_transaksikoran ?? 0, 0, ',', '.') }}</td>
                                    <td class="py-2 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->piutang_transaksikoran ?? 0, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'piutang_ae')
                                    <td class="py-2 px-3 font-bold text-slate-900">{{ $row->nama_ae }}</td>
                                    <td class="py-2 px-3 text-center">{{ number_format($row->jumlah_faktur) }}</td>
                                    <td class="py-2 px-3 text-right font-mono">Rp {{ number_format($row->total_tagihan, 0, ',', '.') }}</td>
                                    <td class="py-2 px-3 text-right font-mono text-slate-900">Rp {{ number_format($row->total_bayar, 0, ',', '.') }}</td>
                                    <td class="py-2 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->sisa_piutang, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'insentif_ae')
                                    <td class="py-2 px-3 font-bold text-slate-900 font-mono">{{ $row->no_faktur }}</td>
                                    <td class="py-2 px-3">{{ \Carbon\Carbon::parse($row->tanggal_transaksikoran)->format('d/m/Y') }}</td>
                                    <td class="py-2 px-3 font-semibold text-[#0A72AC]">{{ $row->sales_iklankoran ?: 'Kantor Pusat' }}</td>
                                    <td class="py-2 px-3 font-medium text-slate-900">{{ $row->nama_pemohon }}</td>
                                    <td class="py-2 px-3 text-right font-mono">Rp {{ number_format($row->totaltagihan_transaksikoran, 0, ',', '.') }}</td>
                                    <td class="py-2 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->insentif_transaksikoran, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'space_iklan')
                                    @php
                                        $mmk = ($row->lebar_kolom ?? 1) * ($row->tinggi_mm ?? 1) * ($row->total_muatkoran ?? 1);
                                    @endphp
                                    <td class="py-2 px-3 font-bold text-slate-900 font-mono">{{ $row->no_faktur }}</td>
                                    <td class="py-2 px-3">{{ \Carbon\Carbon::parse($row->tanggal_transaksikoran)->format('d/m/Y') }}</td>
                                    <td class="py-2 px-3">{{ $row->halaman_iklan ?: 'Hal. Dalam' }}</td>
                                    <td class="py-2 px-3 text-center font-mono">{{ $row->lebar_kolom ?? 1 }} Kol x {{ $row->tinggi_mm ?? 1 }} mm</td>
                                    <td class="py-2 px-3 text-right font-bold font-mono">{{ number_format($mmk) }} mmk</td>
                                    <td class="py-2 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->totaltagihan_transaksikoran, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'stock_opname')
                                    <td class="py-2 px-3">{{ \Carbon\Carbon::parse($row->tanggal_opname)->format('d/m/Y') }}</td>
                                    <td class="py-2 px-3 font-semibold text-slate-900">{{ $row->jenis_dokumen }}</td>
                                    <td class="py-2 px-3 text-center font-mono">{{ $row->nomor_seri_awal }}</td>
                                    <td class="py-2 px-3 text-center font-mono">{{ $row->nomor_seri_akhir }}</td>
                                    <td class="py-2 px-3 text-right font-mono text-slate-900">{{ number_format($row->jumlah_terpakai) }}</td>
                                    <td class="py-2 px-3 text-right font-bold font-mono text-slate-900">{{ number_format($row->jumlah_sisa) }}</td>
                                @elseif($tab === 'koreksi')
                                    <td class="py-2 px-3 font-bold text-slate-900 font-mono">{{ $row->nomor_bukti }}</td>
                                    <td class="py-2 px-3">{{ \Carbon\Carbon::parse($row->tanggal_koreksi)->format('d/m/Y') }}</td>
                                    <td class="py-2 px-3 font-mono">{{ $row->nomor_faktur }}</td>
                                    <td class="py-2 px-3">{{ $row->alasan }}</td>
                                    <td class="py-2 px-3 text-right font-bold text-slate-900 font-mono">
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
                <div class="mt-8 mb-6 border-t-2 border-slate-300 pt-5">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 mb-2">
                        REKAP PEMBAYARAN IKLAN HU. KABAR PRIANGAN (BERDASARKAN ASAL BULAN PENJUALAN)
                    </h4>
                    <p class="text-[11px] text-slate-500 mb-3">Distribusi setoran kasir bulan berjalan untuk melunasi faktur iklan bulan berjalan dan piutang bulan-bulan sebelumnya.</p>
                    <div class="max-w-md border border-slate-300 rounded-lg overflow-hidden">
                        <table class="w-full text-xs">
                            <thead class="bg-[#0A72AC] text-white font-bold text-[11px] border-b-2 border-[#FFB900] uppercase">
                                <tr>
                                    <th class="py-2 px-3 text-left">Asal Bulan Faktur</th>
                                    <th class="py-2 px-3 text-center">Jml Transaksi</th>
                                    <th class="py-2 px-3 text-right">Jumlah Penerimaan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @php $sumRekap = 0; $countRekap = 0; @endphp
                                @foreach($rekapAsalBulan as $m)
                                    @php 
                                        $sumRekap += $m['total']; 
                                        $countRekap += $m['count'];
                                    @endphp
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-1.5 px-3 font-semibold text-slate-800">{{ $m['label'] }}</td>
                                        <td class="py-1.5 px-3 text-center text-slate-500">{{ $m['count'] }}</td>
                                        <td class="py-1.5 px-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($m['total'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-kp-blue-50/80 font-bold border-t-2 border-slate-300">
                                <tr>
                                    <td class="py-2 px-3 text-slate-900 uppercase">TOTAL KESELURUHAN</td>
                                    <td class="py-2 px-3 text-center font-mono">{{ $countRekap }}</td>
                                    <td class="py-2 px-3 text-right font-mono text-[#0A72AC]">Rp {{ number_format($sumRekap, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Lembar Tanda Tangan Resmi Sesuai Standar Kabar Priangan (Foto 12.22.47 (1)) -->
            <div class="pt-6 border-t-2 border-slate-800">
                <div class="grid grid-cols-2 gap-8 text-center text-xs">
                    <div>
                        <div class="text-slate-500 font-medium">Dibuat Oleh:</div>
                        <div class="text-slate-800 font-bold mt-1">Ass. Manager Akuntansi</div>
                        <div class="h-20"></div>
                        <div class="font-bold text-slate-900 underline">{{ auth()->user()->name ?? 'Yadi Setiadi' }}</div>
                        <div class="text-[10px] text-slate-400">Penyusun Sub-Ledger</div>
                    </div>

                    <div>
                        <div class="text-slate-500 font-medium">Mengetahui:</div>
                        <div class="text-slate-800 font-bold mt-1">Manager Akuntansi & Keuangan</div>
                        <div class="h-20"></div>
                        <div class="font-bold text-slate-900 underline">( .................................................. )</div>
                        <div class="text-[10px] text-slate-400">H. Asep Ridwan, S.E.</div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
