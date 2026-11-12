<x-app-layout>
    <div class="space-y-6">

        <!-- Top Action & Filter Bar (Print Hidden) -->
        <div class="print:hidden bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Laporan Sub-Ledger Sirkulasi
                        </h1>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-kp-blue-50 text-[#0A72AC] border border-kp-blue-200">
                            Cetak Kartu
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate">
                        Rekapitulasi mutasi kartu piutang agen dan distribusi koran resmi.
                    </p>
                </div>
            </div>

            <!-- Periode Quick Presets & Cetak Button -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <form method="GET" action="{{ route('accounting.cetak.sirkulasi') }}" class="flex flex-wrap items-center gap-2">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="flex items-center gap-1.5">
                        <input type="date" name="tgl_mulai" value="{{ $tglMulai }}" class="h-9 px-2.5 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-[#0A72AC] focus:border-[#0A72AC] text-slate-800">
                        <span class="text-xs text-slate-400">s/d</span>
                        <input type="date" name="tgl_selesai" value="{{ $tglSelesai }}" class="h-9 px-2.5 text-xs rounded-lg border border-slate-300 focus:ring-1 focus:ring-[#0A72AC] focus:border-[#0A72AC] text-slate-800">
                    </div>
                    <button type="submit" class="h-9 px-3 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition cursor-pointer">
                        Filter
                    </button>
                </form>

                <a href="{{ route('accounting.cetak.sirkulasi.cetak', request()->all()) }}" target="_blank" class="inline-flex items-center gap-1.5 h-9 px-3.5 bg-[#0A72AC] hover:bg-[#085d8d] text-white text-xs font-semibold rounded-lg shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Dokumen Resmi</span>
                </a>
            </div>
        </div>

        <!-- Tab Navigasi Sub-Laporan (Print Hidden) -->
        <div class="print:hidden flex items-center gap-1 border-b border-slate-200">
            <a href="{{ route('accounting.cetak.sirkulasi', ['tab' => 'penjualan', 'tgl_mulai' => $tglMulai, 'tgl_selesai' => $tglSelesai]) }}"
               class="px-4 py-2.5 text-xs font-semibold border-b-2 transition {{ $tab === 'penjualan' ? 'border-[#0A72AC] text-[#0A72AC] font-bold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                1. Penjualan & Oplah Agen
            </a>
            <a href="{{ route('accounting.cetak.sirkulasi', ['tab' => 'pembayaran', 'tgl_mulai' => $tglMulai, 'tgl_selesai' => $tglSelesai]) }}"
               class="px-4 py-2.5 text-xs font-semibold border-b-2 transition {{ $tab === 'pembayaran' ? 'border-[#0A72AC] text-[#0A72AC] font-bold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                2. Setoran Kas Masuk
            </a>
            <a href="{{ route('accounting.cetak.sirkulasi', ['tab' => 'piutang', 'tgl_mulai' => $tglMulai, 'tgl_selesai' => $tglSelesai]) }}"
               class="px-4 py-2.5 text-xs font-semibold border-b-2 transition {{ $tab === 'piutang' ? 'border-[#0A72AC] text-[#0A72AC] font-bold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                3. Rekap Piutang Seluruh Agen
            </a>
            <a href="{{ route('accounting.cetak.sirkulasi', ['tab' => 'retur', 'tgl_mulai' => $tglMulai, 'tgl_selesai' => $tglSelesai]) }}"
               class="px-4 py-2.5 text-xs font-semibold border-b-2 transition {{ $tab === 'retur' ? 'border-[#0A72AC] text-[#0A72AC] font-bold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                4. Register Retur CN-SIR
            </a>
        </div>

        <!-- Metric Summary Card (Print Hidden) -->
        <div class="print:hidden grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Jumlah Baris Data</span>
                <div class="text-lg font-bold text-slate-900 mt-1">{{ number_format($data->count()) }} Data</div>
            </div>
            @if($tab === 'penjualan')
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500">Total Oplah Eksemplar</span>
                    <div class="text-lg font-bold text-slate-900 mt-1">{{ number_format($totalEksemplar) }} Eks</div>
                </div>
            @endif
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500">Total Nominal</span>
                <div class="text-lg font-bold text-[#0A72AC] mt-1">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Dokumen Resmi Laporan (Sesuai Standar Cetak Divisi Kasir Kabar Priangan) -->
        <div class="report-print-container bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-xs text-slate-800 print:border-none print:shadow-none print:p-0">
            
            <!-- Kop Dokumen Resmi Kabar Priangan -->
            <div class="border-b-2 border-slate-900 pb-3 mb-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black tracking-wider uppercase text-slate-900">PT Berkah Pikiran Rakyat</h2>
                        <h3 class="text-sm font-bold text-[#0A72AC] tracking-wide uppercase">Harian Umum Kabar Priangan</h3>
                        <p class="text-[11px] text-slate-500">Jl. Dr. Sukardjo No. 47, Tawangsari, Kec. Tawang, Kota Tasikmalaya 46112</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-black uppercase text-slate-900">
                            LAPORAN SUB-LEDGER {{ strtoupper($tab) }} SIRKULASI
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
            <div class="mb-8 overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead class="bg-[#0A72AC] text-white border-b-2 border-[#FFB900] uppercase font-bold text-[10px] tracking-wider">
                        @if($tab === 'penjualan')
                            <tr>
                                <th class="py-2.5 px-3 text-center w-10">No</th>
                                <th class="py-2.5 px-3 text-left">Kode Agen</th>
                                <th class="py-2.5 px-3 text-left">Nama Agen</th>
                                <th class="py-2.5 px-3 text-left">Wilayah</th>
                                <th class="py-2.5 px-3 text-center">Jatah/Hari</th>
                                <th class="py-2.5 px-3 text-right">Oplah Bln Ini</th>
                                <th class="py-2.5 px-3 text-right">Nilai Bruto</th>
                            </tr>
                        @elseif($tab === 'pembayaran')
                            <tr>
                                <th class="py-2.5 px-3 text-center w-10">No</th>
                                <th class="py-2.5 px-3 text-left">No. BKM</th>
                                <th class="py-2.5 px-3 text-left">Tanggal</th>
                                <th class="py-2.5 px-3 text-left">Kode Agen</th>
                                <th class="py-2.5 px-3 text-left">Nama Agen</th>
                                <th class="py-2.5 px-3 text-left">Metode</th>
                                <th class="py-2.5 px-3 text-right">Jumlah Setor</th>
                            </tr>
                        @elseif($tab === 'piutang')
                            <tr>
                                <th class="py-2.5 px-3 text-center w-10">No</th>
                                <th class="py-2.5 px-3 text-left">Kode Agen</th>
                                <th class="py-2.5 px-3 text-left">Nama Agen</th>
                                <th class="py-2.5 px-3 text-left">Wilayah</th>
                                <th class="py-2.5 px-3 text-left">No. Telepon</th>
                                <th class="py-2.5 px-3 text-right">Saldo Piutang Berjalan</th>
                            </tr>
                        @elseif($tab === 'retur')
                            <tr>
                                <th class="py-2.5 px-3 text-center w-10">No</th>
                                <th class="py-2.5 px-3 text-left">No. Bukti CN-SIR</th>
                                <th class="py-2.5 px-3 text-left">Tanggal</th>
                                <th class="py-2.5 px-3 text-left">Nama Agen</th>
                                <th class="py-2.5 px-3 text-left">Alasan / BAPR</th>
                                <th class="py-2.5 px-3 text-right">Nilai Koreksi</th>
                            </tr>
                        @endif
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($data as $key => $row)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2 px-3 text-center text-slate-400">{{ $key + 1 }}</td>
                                @if($tab === 'penjualan')
                                    <td class="py-2 px-3 font-mono font-bold text-slate-900">{{ $row->kode_agen }}</td>
                                    <td class="py-2 px-3 font-medium text-slate-900">{{ $row->nama_agen }}</td>
                                    <td class="py-2 px-3">{{ $row->wilayah ?: 'Tasikmalaya' }}</td>
                                    <td class="py-2 px-3 text-center font-mono">{{ number_format($row->jatah_default ?? 0) }} eks</td>
                                    <td class="py-2 px-3 text-right font-mono">{{ number_format(($row->jatah_default ?? 0) * 30) }} eks</td>
                                    <td class="py-2 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format(($row->jatah_default ?? 0) * 30 * 3500, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'pembayaran')
                                    <td class="py-2 px-3 font-bold text-slate-900 font-mono">{{ $row->nomor_kas ?? $row->no_transaksi }}</td>
                                    <td class="py-2 px-3">{{ \Carbon\Carbon::parse($row->tgl_transaksi ?? $row->tanggal_terima)->format('d/m/Y') }}</td>
                                    <td class="py-2 px-3 font-mono text-slate-600">{{ $row->agen?->kode_agen ?? '-' }}</td>
                                    <td class="py-2 px-3 font-medium text-slate-900">{{ $row->agen?->nama_agen ?? $row->terima_dari }}</td>
                                    <td class="py-2 px-3 uppercase text-slate-600">{{ $row->metode_pembayaran ?: 'Kas' }}</td>
                                    <td class="py-2 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->jumlah_dibayar ?? 0, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'piutang')
                                    <td class="py-2 px-3 font-mono font-bold text-slate-900">{{ $row->kode_agen }}</td>
                                    <td class="py-2 px-3 font-medium text-slate-900">{{ $row->nama_agen }}</td>
                                    <td class="py-2 px-3">{{ $row->wilayah ?: 'Tasikmalaya' }}</td>
                                    <td class="py-2 px-3 text-slate-600">{{ $row->no_telepon ?: '-' }}</td>
                                    <td class="py-2 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->saldo_piutang ?? 0, 0, ',', '.') }}
                                    </td>
                                @elseif($tab === 'retur')
                                    <td class="py-2 px-3 font-bold text-slate-900 font-mono">{{ $row->nomor_bukti }}</td>
                                    <td class="py-2 px-3">{{ \Carbon\Carbon::parse($row->tanggal_koreksi)->format('d/m/Y') }}</td>
                                    <td class="py-2 px-3 font-medium text-slate-900">{{ $row->nama_entitas }}</td>
                                    <td class="py-2 px-3">{{ $row->alasan }}</td>
                                    <td class="py-2 px-3 text-right font-bold text-slate-900 font-mono">
                                        Rp {{ number_format($row->jumlah_koreksi, 0, ',', '.') }}
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    Tidak ada data untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-kp-blue-50/70 border-t-2 border-slate-300 font-bold">
                            <td colspan="{{ $tab === 'penjualan' ? '6' : ($tab === 'pembayaran' ? '6' : ($tab === 'piutang' ? '5' : '5')) }}" class="py-2.5 px-3 text-right uppercase">
                                TOTAL AKUMULASI
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-slate-900 text-xs font-bold">
                                Rp {{ number_format($totalNominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

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
