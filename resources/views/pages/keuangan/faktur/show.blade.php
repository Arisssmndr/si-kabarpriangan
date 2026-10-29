<x-app-layout>
    <div class="space-y-6">

        <!-- Header -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('keuangan.faktur.index') }}" class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition">
                    ←
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">
                            FAKTUR {{ strtoupper($faktur->kategori ?? 'PENJUALAN') }}: {{ $faktur->no_faktur }}
                        </h1>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $faktur->kategori === 'sirkulasi' ? 'bg-indigo-100 text-indigo-800 border border-indigo-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                            {{ $faktur->kategori === 'sirkulasi' ? 'Sirkulasi' : 'Iklan' }}
                        </span>
                        @if($faktur->status === 'lunas')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                ✓ LUNAS (100%)
                            </span>
                        @elseif($faktur->status === 'cicilan')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                CICILAN / DP
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                BELUM BAYAR (TEMPO)
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Diterbitkan pada {{ $faktur->tanggal_faktur->translatedFormat('d F Y') }} &bull; Jatuh tempo {{ $faktur->jatuh_tempo->translatedFormat('d F Y') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                @if($faktur->sisa_piutang > 0)
                    <button type="button" 
                            onclick="bukaModalPembayaran()"
                            class="h-9 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        <span>Catat Pembayaran / Lunaskan</span>
                    </button>

                    <button type="button" 
                            onclick="bukaModalSuratTugas()"
                            class="h-9 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        <span>Cetak Surat Penugasan</span>
                    </button>
                @endif
                <a href="{{ route('keuangan.faktur.cetak', $faktur->id) }}" target="_blank"
                   class="h-9 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>{{ $faktur->kategori === 'sirkulasi' ? 'Cetak Faktur Sirkulasi' : 'Cetak Order Transaksi Iklan' }}</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <!-- Grid Ringkasan Keuangan Faktur -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 font-mono">Total Nilai Tagihan</span>
                <div class="mt-2 text-2xl font-black text-slate-900 font-mono">
                    Rp {{ number_format($faktur->total_tagihan, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-slate-500">
                    Kategori: <strong class="text-slate-800 uppercase">{{ $faktur->kategori }}</strong>
                </div>
            </div>

            <div class="bg-white border border-emerald-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 font-mono">Total Masuk Kas (Terbayar)</span>
                <div class="mt-2 text-2xl font-black text-emerald-600 font-mono">
                    Rp {{ number_format($faktur->total_terbayar, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-emerald-700 font-semibold">
                    {{ $faktur->total_tagihan > 0 ? round(($faktur->total_terbayar / $faktur->total_tagihan) * 100, 1) : 0 }}% Dari total tagihan
                </div>
            </div>

            <div class="bg-white border border-rose-200 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-700 font-mono">Sisa Piutang Berjalan</span>
                <div class="mt-2 text-2xl font-black {{ $faktur->sisa_piutang > 0 ? 'text-rose-600' : 'text-slate-400' }} font-mono">
                    Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-slate-500">
                    @if($faktur->sisa_piutang <= 0)
                        <span class="text-emerald-600 font-bold">✓ Tagihan selesai (Nol Piutang)</span>
                    @else
                        <span class="text-amber-700 font-bold">Wajib penagihan Inkaso</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Kolom Kiri: Rincian Customer & Item Faktur -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Data Customer -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 font-mono mb-4 pb-2 border-b border-slate-100">
                        Informasi Pelanggan (Customer)
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 block">Nama Customer:</span>
                            <span class="font-extrabold text-slate-900 text-sm block mt-0.5">
                                {{ $faktur->customer->nama_customer ?? '-' }}
                            </span>
                            <span class="text-slate-400 block mt-2">Nomor Telepon:</span>
                            <span class="font-bold text-slate-800 block mt-0.5">
                                {{ $faktur->customer->no_telepon ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Alamat / Domisili:</span>
                            <span class="text-slate-700 block mt-0.5 leading-relaxed">
                                {{ $faktur->customer->alamat ?? '-' }}
                            </span>
                            <span class="text-slate-400 block mt-2">Diterbitkan Oleh Petugas:</span>
                            <span class="font-bold text-slate-800 block mt-0.5">
                                {{ $faktur->creator->name ?? 'Sistem Keuangan' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Rincian Biaya Item -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 font-mono mb-4 pb-2 border-b border-slate-100">
                        Rincian Biaya Transaksi
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-slate-50 text-[11px] uppercase text-slate-500 font-bold border-b border-slate-200">
                                <tr>
                                    <th class="py-2.5 px-3">No</th>
                                    <th class="py-2.5 px-3">Uraian Item Tagihan</th>
                                    <th class="py-2.5 px-3 text-center">Qty</th>
                                    <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                                    <th class="py-2.5 px-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @if(is_array($faktur->rincian_biaya))
                                    @foreach($faktur->rincian_biaya as $idx => $item)
                                        <tr>
                                            <td class="py-2.5 px-3 text-slate-400">{{ $idx + 1 }}</td>
                                            <td class="py-2.5 px-3 font-bold text-slate-800">{{ $item['item'] ?? '-' }}</td>
                                            <td class="py-2.5 px-3 text-center">{{ $item['qty'] ?? 1 }}</td>
                                            <td class="py-2.5 px-3 text-right font-mono">Rp {{ number_format($item['harga'] ?? 0, 0, ',', '.') }}</td>
                                            <td class="py-2.5 px-3 text-right font-bold text-slate-900 font-mono">Rp {{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot class="border-t-2 border-slate-200 bg-slate-50 font-bold">
                                <tr>
                                    <td colspan="4" class="py-3 px-3 text-right uppercase text-slate-700">Total Tagihan:</td>
                                    <td class="py-3 px-3 text-right font-black text-slate-900 text-sm font-mono">
                                        Rp {{ number_format($faktur->total_tagihan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Kolom Kanan: Riwayat Pembayaran & Status Inkaso -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Riwayat Kwitansi & Pembayaran Masuk -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 font-mono mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                        <span>Riwayat Pembayaran Kas</span>
                        <span class="text-emerald-600 font-bold">{{ $faktur->pembayaran->count() }} Setoran</span>
                    </h2>

                    <div class="space-y-3">
                        @forelse($faktur->pembayaran as $bayar)
                            <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/70 text-xs space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono font-bold text-blue-700">{{ $bayar->no_kwitansi }}</span>
                                    <span class="text-[10px] uppercase font-bold px-1.5 py-0.2 rounded {{ $bayar->tipe_bayar === 'lunas_langsung' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $bayar->tipe_bayar === 'lunas_langsung' ? 'Lunas Langsung' : 'Setoran Inkaso / DP' }}
                                    </span>
                                </div>
                                <div class="text-base font-black text-emerald-700 font-mono">
                                    Rp {{ number_format($bayar->nominal_bayar, 0, ',', '.') }}
                                </div>
                                <div class="text-[11px] text-slate-500 flex items-center justify-between pt-1 border-t border-slate-200/60">
                                    <span>Tgl: {{ $bayar->tanggal_bayar->format('d/m/Y') }} ({{ ucfirst($bayar->metode_bayar) }})</span>
                                    <span class="text-slate-400">Kasir: {{ $bayar->penerima->name ?? 'Kasir' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-xs">
                                Belum ada pembayaran yang masuk.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Surat Penugasan Penagihan Lapangan -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 font-mono mb-4 pb-2 border-b border-slate-100">
                        Surat Penugasan Penagihan
                    </h2>

                    @if($faktur->status === 'lunas')
                        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-center">
                            <span class="text-xl">✓</span>
                            <div class="font-bold text-emerald-900 text-xs mt-1">Faktur Telah Lunas</div>
                            <p class="text-[11px] text-emerald-700 mt-0.5">
                                Faktur ini telah lunas 100% dan tidak memiliki sisa piutang.
                            </p>
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-center">
                            <span class="text-xl">📄</span>
                            <div class="font-bold text-amber-900 text-xs mt-1">Surat Penugasan Penagihan (Inkaso)</div>
                            <p class="text-[11px] text-amber-800 mt-1 mb-3">
                                Faktur memiliki sisa piutang <strong>Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}</strong>. Anda dapat langsung mencetak Surat Penugasan fisik untuk diberikan kepada petugas penagih lapangan.
                            </p>
                            <button type="button" 
                                    onclick="bukaModalSuratTugas()"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                <span>Cetak Surat Tugas Penagihan ➔</span>
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
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Cetak Surat Penugasan Penagihan</h3>
                        <p class="text-[11px] text-slate-500">Penugasan Penagihan Lapangan (Inkaso)</p>
                    </div>
                </div>
                <button type="button" onclick="tutupModalSuratTugas()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">✕</button>
            </div>

            <div class="mt-4 space-y-3">
                <div class="p-3 rounded-xl bg-amber-50/60 border border-amber-200 text-xs text-amber-900 space-y-1">
                    <div class="flex justify-between">
                        <span class="text-amber-700">No Faktur:</span>
                        <strong class="font-mono text-slate-900">{{ $faktur->no_faktur }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-amber-700">Pelanggan:</span>
                        <span class="font-bold truncate max-w-[200px]">{{ $faktur->customer->nama_customer ?? 'UMUM' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-amber-700">Sisa Piutang:</span>
                        <strong class="text-rose-600 font-mono">Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Pilih Petugas Penagih Lapangan (Inkaso):
                    </label>
                    <select id="selectModalPetugas" 
                            onchange="handleSelectModalPetugas(this.value)" 
                            class="w-full h-10 px-3 rounded-xl border border-slate-300 text-xs text-slate-900 font-bold bg-white focus:ring-2 focus:ring-amber-500 focus:outline-hidden shadow-2xs cursor-pointer">
                        <option value="">-- Pilih Petugas Penagihan --</option>
                        <option value="Ahmad Supardi">Ahmad Supardi (Wilayah Kota Tasikmalaya)</option>
                        <option value="Rudi Hermawan">Rudi Hermawan (Wilayah Ciamis &amp; Singaparna)</option>
                        <option value="Dadan Ramdani">Dadan Ramdani (Wilayah Banjar &amp; Pangandaran)</option>
                        <option value="Yudi Permana">Yudi Permana (Wilayah Garut &amp; Priangan)</option>
                        <option value="__manual__">✏️ Ketik Nama Petugas Lainnya...</option>
                        <option value="__blank__">📄 Tanpa Nama (Format Garis Titik-titik / Tulis Tangan)</option>
                    </select>

                    <!-- Pilihan Cepat (Pill Buttons) -->
                    <div class="flex flex-wrap gap-1.5 mt-2.5">
                        <button type="button" onclick="setPetugasModal('Ahmad Supardi')" class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 text-[11px] font-semibold border border-amber-200 transition cursor-pointer">
                            Ahmad Supardi
                        </button>
                        <button type="button" onclick="setPetugasModal('Rudi Hermawan')" class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 text-[11px] font-semibold border border-amber-200 transition cursor-pointer">
                            Rudi Hermawan
                        </button>
                        <button type="button" onclick="setPetugasModal('Dadan Ramdani')" class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 text-[11px] font-semibold border border-amber-200 transition cursor-pointer">
                            Dadan Ramdani
                        </button>
                        <button type="button" onclick="setPetugasModal('Yudi Permana')" class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 text-[11px] font-semibold border border-amber-200 transition cursor-pointer">
                            Yudi Permana
                        </button>
                    </div>

                    <!-- Field Input jika memilih opsi Ketik Manual -->
                    <div id="containerInputManual" class="mt-2.5 hidden">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Petugas Lainnya:</label>
                        <input type="text" 
                               id="inputModalPetugas" 
                               placeholder="Ketik nama petugas..." 
                               onkeydown="if(event.key === 'Enter') prosesCetakSuratTugas();"
                               class="w-full h-9 px-3 rounded-xl border border-amber-300 text-xs text-slate-900 font-bold focus:ring-2 focus:ring-amber-500 focus:outline-hidden shadow-2xs">
                    </div>

                    <p class="text-[11px] text-slate-400 mt-2 italic">
                        *Nama hanya dicetak pada lembar fisik penugasan (tanpa membuat akun user di database).
                    </p>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2">
                <button type="button" onclick="tutupModalSuratTugas()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition cursor-pointer">
                    Batal
                </button>
                <button type="button" onclick="prosesCetakSuratTugas()" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Buka &amp; Cetak Surat Tugas</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Catat Pembayaran / Pelunasan Faktur -->
    <div id="modalPembayaran" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Catat Pembayaran / Pelunasan</h3>
                        <p class="text-[11px] text-slate-500 font-mono">{{ $faktur->no_faktur }} &bull; {{ $faktur->kategori === 'sirkulasi' ? 'Sirkulasi Koran' : 'Iklan' }}</p>
                    </div>
                </div>
                <button type="button" onclick="tutupModalPembayaran()" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
            </div>

            <form action="{{ route('keuangan.faktur.bayar', $faktur->id) }}" method="POST" class="mt-4 space-y-4">
                @csrf
                
                <!-- Ringkasan Nilai Tagihan -->
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
                        <span class="font-mono font-bold text-emerald-600">Rp {{ number_format($faktur->total_terbayar, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-900 pt-1.5 border-t border-slate-200 font-bold">
                        <span class="text-rose-600">Sisa Piutang Saat Ini:</span>
                        <span class="font-mono text-rose-600 text-sm">Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Input Nominal Bayar -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700">Nominal Setoran / Bayar (Rp):</label>
                        <button type="button" onclick="isiLunasPenuh({{ (int)$faktur->sisa_piutang }})" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-800 underline cursor-pointer">
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
                           class="w-full h-10 px-3 rounded-xl border border-slate-300 text-sm text-slate-900 font-mono font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-hidden shadow-2xs">
                    <p class="text-[11px] text-slate-400 mt-1">Masukkan nominal pembayaran tunai/transfer yang diterima dari agen.</p>
                </div>

                <!-- Metode & Tanggal Bayar -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Bayar:</label>
                        <select name="metode_bayar" class="w-full h-10 px-3 rounded-xl border border-slate-300 text-xs text-slate-900 font-bold bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-hidden shadow-2xs cursor-pointer">
                            <option value="tunai">💵 Tunai (Kas Kantor)</option>
                            <option value="transfer">🏦 Transfer (Bank BJB Priangan)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Bayar:</label>
                        <input type="date" name="tanggal_bayar" value="{{ date('Y-m-d') }}" required
                               class="w-full h-10 px-3 rounded-xl border border-slate-300 text-xs text-slate-900 font-bold bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-hidden shadow-2xs">
                    </div>
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan / Keterangan (Opsional):</label>
                    <input type="text" name="catatan" placeholder="Contoh: Setoran lunas titip loper / transfer via m-banking..." 
                           class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:outline-hidden shadow-2xs">
                </div>

                <!-- Footer Tombol -->
                <div class="mt-6 flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="tutupModalPembayaran()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
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
            // Default ke Ahmad Supardi
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
