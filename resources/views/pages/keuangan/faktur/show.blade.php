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
                            FAKTUR: {{ $faktur->no_faktur }}
                        </h1>
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
                <a href="{{ route('keuangan.faktur.cetak', $faktur->id) }}" target="_blank"
                   class="h-9 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Lembar Faktur</span>
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

                <!-- Status Penugasan Inkaso -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 font-mono mb-4 pb-2 border-b border-slate-100">
                        Status Penugasan Inkaso
                    </h2>

                    @if($faktur->status === 'lunas')
                        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-center">
                            <span class="text-xl">✓</span>
                            <div class="font-bold text-emerald-900 text-xs mt-1">Faktur Telah Lunas</div>
                            <p class="text-[11px] text-emerald-700 mt-0.5">
                                Sesuai SOP, faktur yang lunas tidak memerlukan penugasan Inkaso lapangan.
                            </p>
                        </div>
                    @elseif($faktur->penugasanInkaso->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($faktur->penugasanInkaso as $tugas)
                                <div class="p-3.5 rounded-xl border border-amber-200 bg-amber-50/60 text-xs space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-mono font-bold text-amber-900">{{ $tugas->no_tugas }}</span>
                                        <span class="text-[10px] uppercase font-bold px-1.5 py-0.2 rounded {{ $tugas->status_tugas === 'berjalan' ? 'bg-amber-200 text-amber-900' : 'bg-emerald-100 text-emerald-800' }}">
                                            {{ ucfirst($tugas->status_tugas) }}
                                        </span>
                                    </div>
                                    <div class="font-bold text-slate-800 mt-1">
                                        Petugas: {{ $tugas->petugas->name ?? '-' }}
                                    </div>
                                    <div class="text-[11px] text-slate-500">
                                        Tanggal Tugas: {{ $tugas->tanggal_tugas->format('d/m/Y') }}
                                    </div>
                                    @if($tugas->catatan)
                                        <div class="text-[10px] text-slate-600 bg-white p-2 rounded border border-amber-200 mt-1">
                                            {{ $tugas->catatan }}
                                        </div>
                                    @endif
                                    <div class="pt-2">
                                        <a href="{{ route('keuangan.inkaso.surat-tugas.cetak', $tugas->id) }}" target="_blank"
                                           class="text-[11px] font-bold text-blue-700 hover:underline flex items-center gap-1">
                                            🖨 Cetak Surat Tugas Penagihan ➔
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-center">
                            <span class="text-xl">⚠</span>
                            <div class="font-bold text-rose-900 text-xs mt-1">Belum Ditugaskan</div>
                            <p class="text-[11px] text-rose-700 mt-0.5 mb-3">
                                Faktur memiliki sisa piutang Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }} dan belum memiliki petugas penagihan.
                            </p>
                            <a href="{{ route('keuangan.inkaso.index') }}" 
                               class="inline-block px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition">
                                Buka Antrean Inkaso ➔
                            </a>
                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>
</x-app-layout>
