<x-app-layout>
    <div class="space-y-5">

        <!-- Header Halaman (Standar Divisi Iklan 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" 
                     alt="Kabar Priangan" 
                     class="h-9 sm:h-10 w-auto object-contain shrink-0"
                     onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Faktur {{ strtoupper($faktur->kategori ?? 'PENJUALAN') }}: {{ $faktur->no_faktur }}
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 uppercase">
                            {{ $faktur->kategori === 'sirkulasi' ? 'Sirkulasi' : 'Iklan' }}
                        </span>
                        @if($faktur->status === 'lunas')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                Lunas
                            </span>
                        @elseif($faktur->status === 'cicilan')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-300">
                                Cicilan
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                Piutang
                            </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                        Diterbitkan pada {{ $faktur->tanggal_faktur->translatedFormat('d F Y') }} &bull; Jatuh tempo {{ $faktur->jatuh_tempo->translatedFormat('d F Y') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                @if($faktur->sisa_piutang > 0)
                    <button type="button" 
                            onclick="bukaModalPembayaran()"
                            class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        <span>Catat Pembayaran</span>
                    </button>

                    <button type="button" 
                            onclick="bukaModalSuratTugas()"
                            class="inline-flex items-center gap-2 h-10 px-4 bg-amber-400 hover:bg-amber-500 text-slate-900 text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        <span>Surat Tugas</span>
                    </button>
                @endif
                <a href="{{ route('keuangan.faktur.cetak', $faktur->id) }}" target="_blank"
                   class="inline-flex items-center gap-2 h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Faktur</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 font-semibold rounded-xl text-sm flex items-center gap-2 shadow-2xs">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- 4 KPI Summary Cards (Standar Divisi Iklan 60-30-10) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Total Tagihan</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($faktur->total_tagihan, 0, ',', '.') }}
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    Kategori: <strong class="text-slate-800 uppercase">{{ $faktur->kategori }}</strong>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Total Terbayar</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($faktur->total_terbayar, 0, ',', '.') }}
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    {{ $faktur->total_tagihan > 0 ? round(($faktur->total_terbayar / $faktur->total_tagihan) * 100, 1) : 0 }}% terbayar
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Sisa Piutang</span>
                <p class="text-2xl font-bold {{ $faktur->sisa_piutang > 0 ? 'text-amber-600' : 'text-slate-400' }} mt-1 font-mono tabular-nums">
                    Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}
                </p>
                <div class="mt-2 text-xs text-slate-500">
                    {{ $faktur->sisa_piutang > 0 ? 'Wajib ditagih Inkaso' : 'Nol piutang (Selesai)' }}
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Status Penagihan</span>
                <p class="text-lg font-bold text-slate-900 mt-2 font-mono">
                    {{ $faktur->sisa_piutang > 0 ? 'Antrean Inkaso' : 'Lunas Selesai' }}
                </p>
                <div class="mt-2 text-xs text-slate-400">
                    {{ $faktur->pembayaran->count() }} kali pembayaran
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            <!-- Kolom Kiri: Rincian Pelanggan & Item Faktur -->
            <div class="lg:col-span-8 space-y-5">

                <!-- Data Pelanggan -->
                <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 mb-3 pb-2 border-b border-slate-200">
                        Informasi Pelanggan (Customer)
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-xs text-slate-400 block">Nama Customer:</span>
                            <span class="font-bold text-slate-900 block mt-0.5 uppercase">
                                {{ $faktur->customer->nama_customer ?? '-' }}
                            </span>
                            <span class="text-xs text-slate-400 block mt-2">Nomor Telepon:</span>
                            <span class="font-semibold text-slate-700 block mt-0.5">
                                {{ $faktur->customer->no_telepon ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block">Alamat / Domisili:</span>
                            <span class="text-slate-700 block mt-0.5 leading-relaxed">
                                {{ $faktur->customer->alamat ?? '-' }}
                            </span>
                            <span class="text-xs text-slate-400 block mt-2">Diterbitkan Oleh:</span>
                            <span class="font-semibold text-slate-700 block mt-0.5">
                                {{ $faktur->creator->name ?? 'Sistem Keuangan' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Rincian Biaya Item -->
                <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-200">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700">
                            Rincian Biaya Transaksi
                        </h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                                <tr>
                                    <th class="py-3 px-3.5 text-center w-12">No</th>
                                    <th class="py-3 px-3.5">Uraian Item Tagihan</th>
                                    <th class="py-3 px-3.5 text-center w-24">Qty</th>
                                    <th class="py-3 px-3.5 text-right w-36">Harga Satuan</th>
                                    <th class="py-3 px-3.5 text-right w-36">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 whitespace-nowrap text-slate-700">
                                @if(is_array($faktur->rincian_biaya))
                                    @foreach($faktur->rincian_biaya as $idx => $item)
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-3 px-3.5 text-center text-slate-500 font-normal">{{ $idx + 1 }}</td>
                                            <td class="py-3 px-3.5 font-semibold text-slate-900">{{ $item['item'] ?? '-' }}</td>
                                            <td class="py-3 px-3.5 text-center">{{ $item['qty'] ?? 1 }}</td>
                                            <td class="py-3 px-3.5 whitespace-nowrap">
                                                <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                                    <span class="font-normal text-slate-700 tabular-nums">{{ number_format($item['harga'] ?? 0, 0, ',', '.') }}</span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-3.5 whitespace-nowrap">
                                                <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                                    <span class="text-slate-400 font-normal select-none">Rp</span>
                                                    <span class="font-semibold text-slate-900 tabular-nums">{{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot class="border-t border-slate-200 bg-slate-50 font-bold">
                                <tr>
                                    <td colspan="4" class="py-3.5 px-3.5 text-right font-semibold text-slate-700">Total Tagihan:</td>
                                    <td class="py-3.5 px-3.5 text-right">
                                        <div class="flex justify-between items-center gap-2 max-w-[125px] ml-auto font-mono text-sm">
                                            <span class="text-slate-400 font-normal select-none">Rp</span>
                                            <span class="font-bold text-kp-blue-700 tabular-nums">{{ number_format($faktur->total_tagihan, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Kolom Kanan: Riwayat Pembayaran & Cetak Surat Tugas -->
            <div class="lg:col-span-4 space-y-5">

                <!-- Riwayat Pembayaran Masuk -->
                <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 mb-3 pb-2 border-b border-slate-200 flex items-center justify-between">
                        <span>Riwayat Pembayaran Kas</span>
                        <span class="text-kp-blue-700 font-bold font-mono">{{ $faktur->pembayaran->count() }} Setoran</span>
                    </h2>

                    <div class="space-y-2.5">
                        @forelse($faktur->pembayaran as $bayar)
                            <div class="p-3 rounded-xl border border-slate-200 bg-slate-50 text-xs space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono font-bold text-kp-blue-700">{{ $bayar->no_kwitansi }}</span>
                                    <span class="text-[10px] uppercase font-semibold px-2 py-0.5 rounded border border-slate-200 bg-white text-slate-700">
                                        {{ $bayar->tipe_bayar === 'lunas_langsung' ? 'Lunas Langsung' : 'Setoran Inkaso / DP' }}
                                    </span>
                                </div>
                                <div class="text-base font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($bayar->nominal_bayar, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-slate-500 flex items-center justify-between pt-1 border-t border-slate-200">
                                    <span>{{ $bayar->tanggal_bayar->format('d/m/Y') }} ({{ ucfirst($bayar->metode_bayar) }})</span>
                                    <span class="text-slate-400">{{ $bayar->penerima->name ?? 'Kasir' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-xs">
                                Belum ada pembayaran yang tercatat.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Penugasan Penagihan Lapangan -->
                <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 mb-3 pb-2 border-b border-slate-200">
                        Surat Penugasan Penagihan
                    </h2>

                    @if($faktur->status === 'lunas')
                        <div class="p-4 rounded-xl bg-kp-blue-50 border border-kp-blue-200 text-center">
                            <div class="font-bold text-kp-blue-900 text-xs">Faktur Telah Lunas</div>
                            <p class="text-xs text-kp-blue-700 mt-0.5">
                                Faktur ini telah lunas 100% dan tidak memiliki sisa saldo piutang.
                            </p>
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
                            <div class="font-bold text-slate-900 text-xs">Surat Penugasan Penagihan (Inkaso)</div>
                            <p class="text-xs text-slate-600 mt-1 mb-3">
                                Faktur memiliki sisa piutang <strong>Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}</strong>.
                            </p>
                            <button type="button" 
                                    onclick="bukaModalSuratTugas()"
                                    class="inline-flex items-center px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-900 font-bold text-xs shadow-xs transition cursor-pointer">
                                <span>Cetak Surat Tugas</span>
                            </button>
                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>

    <!-- Modal Input Nama Petugas Penagih (Inkaso) -->
    <div id="modalSuratTugas" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 animate-in fade-in zoom-in duration-150">
            <div class="flex items-start justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Cetak Surat Penugasan Penagihan</h3>
                    <p class="text-xs text-slate-500">Penugasan Penagihan Lapangan (Inkaso)</p>
                </div>
                <button type="button" onclick="tutupModalSuratTugas()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">✕</button>
            </div>

            <div class="mt-4 space-y-3">
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 space-y-1">
                    <div class="flex justify-between">
                        <span class="text-slate-500">No Faktur:</span>
                        <strong class="font-mono text-slate-900">{{ $faktur->no_faktur }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Pelanggan:</span>
                        <span class="font-bold truncate max-w-[200px]">{{ $faktur->customer->nama_customer ?? 'UMUM' }}</span>
                    </div>
                    <div class="flex justify-between pt-1 border-t border-slate-200">
                        <span class="text-slate-700 font-semibold">Sisa Piutang:</span>
                        <strong class="text-kp-blue-700 font-bold font-mono">Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Pilih Petugas Penagih Lapangan (Inkaso):
                    </label>
                    <select id="selectModalPetugas" 
                            onchange="handleSelectModalPetugas(this.value)" 
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-sm text-slate-900 font-semibold bg-white focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 shadow-2xs cursor-pointer">
                        <option value="">-- Pilih Petugas Penagihan --</option>
                        <option value="Ahmad Supardi">Ahmad Supardi (Wilayah Kota Tasikmalaya)</option>
                        <option value="Rudi Hermawan">Rudi Hermawan (Wilayah Ciamis &amp; Singaparna)</option>
                        <option value="Dadan Ramdani">Dadan Ramdani (Wilayah Banjar &amp; Pangandaran)</option>
                        <option value="Yudi Permana">Yudi Permana (Wilayah Garut &amp; Priangan)</option>
                        <option value="__manual__">Ketik Nama Petugas Lainnya...</option>
                        <option value="__blank__">Tanpa Nama (Format Garis Titik-titik / Tulis Tangan)</option>
                    </select>

                    <div class="flex flex-wrap gap-1.5 mt-2.5">
                        <button type="button" onclick="setPetugasModal('Ahmad Supardi')" class="px-2.5 py-1 rounded-lg bg-kp-blue-50 hover:bg-kp-blue-100 text-kp-blue-900 text-xs font-semibold border border-kp-blue-200 transition cursor-pointer">
                            Ahmad Supardi
                        </button>
                        <button type="button" onclick="setPetugasModal('Rudi Hermawan')" class="px-2.5 py-1 rounded-lg bg-kp-blue-50 hover:bg-kp-blue-100 text-kp-blue-900 text-xs font-semibold border border-kp-blue-200 transition cursor-pointer">
                            Rudi Hermawan
                        </button>
                        <button type="button" onclick="setPetugasModal('Dadan Ramdani')" class="px-2.5 py-1 rounded-lg bg-kp-blue-50 hover:bg-kp-blue-100 text-kp-blue-900 text-xs font-semibold border border-kp-blue-200 transition cursor-pointer">
                            Dadan Ramdani
                        </button>
                        <button type="button" onclick="setPetugasModal('Yudi Permana')" class="px-2.5 py-1 rounded-lg bg-kp-blue-50 hover:bg-kp-blue-100 text-kp-blue-900 text-xs font-semibold border border-kp-blue-200 transition cursor-pointer">
                            Yudi Permana
                        </button>
                    </div>

                    <div id="containerInputManual" class="mt-2.5 hidden">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Petugas Lainnya:</label>
                        <input type="text" 
                               id="inputModalPetugas" 
                               placeholder="Ketik nama petugas..." 
                               onkeydown="if(event.key === 'Enter') prosesCetakSuratTugas();"
                               class="w-full h-11 px-3.5 rounded-xl border border-kp-blue-300 text-sm text-slate-900 font-bold focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 shadow-2xs">
                    </div>

                    <p class="text-xs text-slate-400 mt-2 italic">
                        *Nama hanya dicetak pada lembar fisik penugasan (tanpa membuat akun user di database).
                    </p>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2">
                <button type="button" onclick="tutupModalSuratTugas()" class="h-10 px-4 rounded-xl border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold hover:bg-slate-50 transition cursor-pointer">
                    Batal
                </button>
                <button type="button" onclick="prosesCetakSuratTugas()" class="h-10 px-4 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-900 font-bold text-xs sm:text-sm shadow-xs transition flex items-center cursor-pointer">
                    <span>Buka &amp; Cetak Surat Tugas</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Catat Pembayaran / Pelunasan Faktur -->
    <div id="modalPembayaran" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Catat Pembayaran / Pelunasan</h3>
                    <p class="text-xs text-slate-500 font-mono">{{ $faktur->no_faktur }} &bull; {{ $faktur->kategori === 'sirkulasi' ? 'Sirkulasi Koran' : 'Iklan' }}</p>
                </div>
                <button type="button" onclick="tutupModalPembayaran()" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
            </div>

            <form action="{{ route('keuangan.faktur.bayar', $faktur->id) }}" method="POST" class="mt-4 space-y-4">
                @csrf
                
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1.5">
                    <div class="flex justify-between text-slate-600">
                        <span>Penerima / Agen:</span>
                        <strong class="text-slate-900">{{ $faktur->customer->nama_customer ?? 'Umum' }}</strong>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Total Tagihan:</span>
                        <span class="font-mono font-bold">Rp {{ number_format($faktur->total_tagihan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Sudah Terbayar:</span>
                        <span class="font-mono font-bold text-kp-blue-700">Rp {{ number_format($faktur->total_terbayar, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-900 pt-1.5 border-t border-slate-200 font-bold">
                        <span class="text-slate-700">Sisa Piutang Saat Ini:</span>
                        <span class="font-mono text-kp-blue-700 text-sm">Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700">Nominal Setoran / Bayar (Rp):</label>
                        <button type="button" onclick="isiLunasPenuh({{ (int)$faktur->sisa_piutang }})" class="text-xs font-bold text-kp-blue-700 hover:underline cursor-pointer">
                            ✓ Bayar Lunas Penuh (Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }})
                        </button>
                    </div>
                    <input type="number" 
                           name="nominal_bayar" 
                           id="inputNominalBayar" 
                           value="{{ (int) $faktur->sisa_piutang }}" 
                           max="{{ (int) $faktur->sisa_piutang }}"
                           min="1"
                           required
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-sm text-slate-900 font-mono font-bold focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 shadow-2xs">
                    <p class="text-xs text-slate-400 mt-1">Masukkan nominal pembayaran tunai/transfer yang diterima dari agen.</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Bayar:</label>
                        <select name="metode_bayar" class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-sm text-slate-900 font-semibold bg-white focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 shadow-2xs cursor-pointer">
                            <option value="tunai">Tunai (Kas Kantor)</option>
                            <option value="transfer">Transfer (Bank BJB Priangan)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Bayar:</label>
                        <input type="date" name="tanggal_bayar" value="{{ date('Y-m-d') }}" required
                               class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-sm text-slate-900 font-semibold bg-white focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 shadow-2xs">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan / Keterangan (Opsional):</label>
                    <input type="text" name="catatan" placeholder="Contoh: Setoran lunas titip loper / transfer via m-banking..." 
                           class="w-full h-11 px-3.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 shadow-2xs">
                </div>

                <div class="mt-6 flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                    <button type="button" onclick="tutupModalPembayaran()" class="h-10 px-4 rounded-xl border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold hover:bg-slate-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="h-10 px-5 rounded-xl bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold shadow-xs transition flex items-center cursor-pointer">
                        <span>Simpan &amp; Lunaskan Faktur</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let selectedPetugasNama = '';

        function bukaModalPembayaran() {
            const modal = document.getElementById('modalPembayaran');
            if (modal) modal.classList.remove('hidden');
        }

        function tutupModalPembayaran() {
            const modal = document.getElementById('modalPembayaran');
            if (modal) modal.classList.add('hidden');
        }

        function isiLunasPenuh(sisa) {
            const input = document.getElementById('inputNominalBayar');
            if (input) input.value = sisa;
        }

        function bukaModalSuratTugas() {
            const selectEl = document.getElementById('selectModalPetugas');
            selectEl.value = 'Ahmad Supardi';
            selectedPetugasNama = 'Ahmad Supardi';
            document.getElementById('containerInputManual').classList.add('hidden');
            document.getElementById('inputModalPetugas').value = '';
            
            const modal = document.getElementById('modalSuratTugas');
            modal.classList.remove('hidden');
        }

        function handleSelectModalPetugas(val) {
            const containerManual = document.getElementById('containerInputManual');
            const inputManual = document.getElementById('inputModalPetugas');
            
            if (val === '__manual__') {
                containerManual.classList.remove('hidden');
                inputManual.focus();
                selectedPetugasNama = inputManual.value.trim();
            } else if (val === '__blank__') {
                containerManual.classList.add('hidden');
                selectedPetugasNama = '';
            } else {
                containerManual.classList.add('hidden');
                selectedPetugasNama = val;
            }
        }

        function setPetugasModal(nama) {
            const selectEl = document.getElementById('selectModalPetugas');
            selectEl.value = nama;
            selectedPetugasNama = nama;
            document.getElementById('containerInputManual').classList.add('hidden');
        }

        function tutupModalSuratTugas() {
            const modal = document.getElementById('modalSuratTugas');
            modal.classList.add('hidden');
        }

        function prosesCetakSuratTugas() {
            const selectEl = document.getElementById('selectModalPetugas');
            let finalNama = selectedPetugasNama;
            
            if (selectEl.value === '__manual__') {
                finalNama = document.getElementById('inputModalPetugas').value.trim();
            } else if (selectEl.value === '__blank__') {
                finalNama = '';
            }

            const queryParam = finalNama ? "?petugas=" + encodeURIComponent(finalNama) : "";
            const baseUrl = "{{ route('keuangan.faktur.surat-tugas', $faktur->id) }}";
            window.open(baseUrl + queryParam, '_blank');
            tutupModalSuratTugas();
        }
    </script>
</x-app-layout>
