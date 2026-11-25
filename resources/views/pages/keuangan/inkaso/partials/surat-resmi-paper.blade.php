@php
    $paperTotalTagihan = $tasks->sum(fn ($t) => (float) ($t->faktur->total_tagihan ?? 0));
    $paperTotalTerbayar = $tasks->sum(fn ($t) => $t->nominal_terkumpul);
    $paperTotalSisa = $tasks->sum(function ($t) {
        $pBsk = isset($setoranPending) ? $setoranPending->get($t->faktur_id) : $t->setoranLapangan->where('status_approval', 'pending')->first();
        return max(0, (float) ($t->faktur->sisa_piutang ?? 0) - ($pBsk ? (float) $pBsk->nominal_titipan : 0));
    });
    $paperFirstTask = $tasks->first();
    $paperNamaPetugas = $namaPetugas ?? ($paperFirstTask->petugas->name ?? ($paperFirstTask->nama_petugas ?? '-'));
    $paperTanggal = $paperFirstTask?->tanggal_tugas ? \Carbon\Carbon::parse($paperFirstTask->tanggal_tugas)->translatedFormat('d F Y') : now()->translatedFormat('d F Y');
    $paperCatatan = $tasks->pluck('catatan')->filter()->unique()->implode(' | ');
@endphp

<div class="bg-white border border-slate-300 rounded-2xl shadow-sm p-5 sm:p-8 print-shadow text-slate-900">
    <!-- Kop Surat Resmi HU Kabar Priangan -->
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 border-b-2 border-slate-900 pb-4 mb-5">
        <div class="flex items-center gap-3.5">
            <img src="{{ asset('kabarpriangan.png') }}"
                 alt="Kabar Priangan"
                 class="h-12 w-auto object-contain shrink-0"
                 onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
            <div>
                <h1 class="text-sm sm:text-base font-black text-slate-900 uppercase tracking-tight">
                    HARIAN UMUM KABAR PRIANGAN
                </h1>
                <p class="text-[11px] font-bold text-[#0E527B]">
                    DIVISI KEUANGAN &bull; BAGIAN FAKTUR &amp; PENAGIHAN PIUTANG (INKASO)
                </p>
                <p class="text-[10px] text-slate-500">
                    Jl. Dr. Sukardjo No. 51 Kota Tasikmalaya | Telp: (0265) 331234
                </p>
            </div>
        </div>

        <div class="sm:text-right shrink-0">
            <span class="inline-block text-[10px] font-mono font-extrabold uppercase tracking-wider text-[#0E527B] bg-sky-50 px-2.5 py-1 rounded border border-sky-200 mb-1">
                SURAT PENUGASAN RESMI
            </span>
            <div class="text-xs sm:text-sm font-mono font-black text-slate-900">
                {{ $noSuratLabel }}
            </div>
        </div>
    </div>

    <!-- Judul Dokumen Resmi -->
    <div class="text-center mb-5">
        <h2 class="text-sm sm:text-base font-black text-slate-900 uppercase tracking-wide underline underline-offset-4">
            SURAT PENUGASAN PENAGIHAN LAPANGAN (INKASO)
        </h2>
        <p class="text-[11px] text-slate-500 font-mono mt-1">
            Nomor: {{ $noSuratLabel }}
        </p>
    </div>

    <!-- Pengantar & Identitas Petugas -->
    <div class="text-xs text-slate-700 leading-relaxed space-y-3 mb-5">
        <p>
            Bagian Faktur &amp; Keuangan Harian Umum Kabar Priangan memberikan penugasan resmi penagihan lapangan kepada:
        </p>

        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 sm:p-4 space-y-1.5 text-xs">
            <div class="flex items-baseline justify-between gap-4">
                <span class="text-slate-500 shrink-0 w-32">Nama Petugas</span>
                <span class="font-extrabold text-slate-900 uppercase text-right">{{ $paperNamaPetugas }}</span>
            </div>
            <div class="flex items-baseline justify-between gap-4">
                <span class="text-slate-500 shrink-0 w-32">Jabatan</span>
                <span class="font-semibold text-slate-800 text-right">Petugas Penagihan Lapangan (Inkaso)</span>
            </div>
            <div class="flex items-baseline justify-between gap-4">
                <span class="text-slate-500 shrink-0 w-32">Tanggal Tugas</span>
                <span class="font-mono font-semibold text-slate-800 text-right">{{ $paperTanggal }}</span>
            </div>
            <div class="flex items-baseline justify-between gap-4">
                <span class="text-slate-500 shrink-0 w-32">Jumlah Faktur</span>
                <span class="font-bold text-slate-900 text-right">{{ $tasks->count() }} Dokumen Faktur</span>
            </div>
            <div class="flex items-baseline justify-between gap-4 pt-1.5 border-t border-slate-200">
                <span class="font-bold text-slate-700 shrink-0">Total Piutang</span>
                <span class="font-black font-mono text-[#0E527B] text-right">Rp {{ number_format($paperTotalTagihan, 0, ',', '.') }}</span>
            </div>
            @if(!empty($paperCatatan))
                <div class="flex items-baseline justify-between gap-4 pt-1 border-t border-slate-200">
                    <span class="text-slate-500 shrink-0 w-32">Catatan</span>
                    <span class="italic text-slate-700 text-right">{{ $paperCatatan }}</span>
                </div>
            @endif
        </div>

        <p>
            Daftar rincian faktur piutang yang wajib ditagihkan pada penugasan ini adalah sebagai berikut:
        </p>
    </div>

    <!-- Tabel Resmi Rincian Faktur dalam Surat -->
    <div class="overflow-x-auto border border-slate-300 rounded-xl mb-5">
        <table class="w-full text-left text-[11px] sm:text-xs text-slate-700">
            <thead class="bg-slate-100 uppercase text-[10px] font-bold text-slate-700 border-b border-slate-300">
                <tr>
                    <th class="py-2.5 px-2 text-center w-8">No</th>
                    <th class="py-2.5 px-2.5">No. Faktur</th>
                    <th class="py-2.5 px-2.5">Pelanggan / Agen</th>
                    <th class="py-2.5 px-2.5 text-right">Tagihan</th>
                    <th class="py-2.5 px-2.5 text-right">Terbayar</th>
                    <th class="py-2.5 px-2.5 text-right">Sisa Piutang</th>
                    <th class="py-2.5 px-2.5 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($tasks as $idx => $tugas)
                    @php
                        $pendingBsk = isset($setoranPending) ? $setoranPending->get($tugas->faktur_id) : null;
                        $latestBsk = $pendingBsk ?: $tugas->setoranLapangan->sortByDesc('id')->first();
                        $fTotal = (float) ($tugas->faktur->total_tagihan ?? 0);
                        $fTerbayar = $tugas->nominal_terkumpul;
                        $fSisa = max(0, (float) ($tugas->faktur->sisa_piutang ?? 0) - ($pendingBsk ? (float) $pendingBsk->nominal_titipan : 0));
                        $isMenunggu = $tugas->status_konfirmasi === 'menunggu' && $tugas->status_tugas !== 'selesai';
                        $isBatal = $tugas->status_tugas === 'batal' || $tugas->status_konfirmasi === 'ditolak';
                        $isLunas = (float) ($tugas->faktur->sisa_piutang ?? 0) <= 0 || $fSisa <= 0;
                    @endphp
                    <tr class="hover:bg-slate-50/70">
                        <td class="py-2.5 px-2 text-center font-mono font-bold text-slate-600">{{ $idx + 1 }}</td>
                        <td class="py-2.5 px-2.5 whitespace-nowrap">
                            <div class="font-mono font-bold text-[#0E527B]">{{ $tugas->faktur->no_faktur ?? '-' }}</div>
                            @if($tugas->faktur?->tanggal_faktur)
                                <div class="text-[10px] text-slate-400">{{ $tugas->faktur->tanggal_faktur->format('d/m/Y') }}</div>
                            @endif
                        </td>
                        <td class="py-2.5 px-2.5">
                            <div class="font-bold text-slate-900">{{ $tugas->faktur->customer->nama_customer ?? '-' }}</div>
                            <div class="text-[10px] text-slate-500">
                                {{ $tugas->faktur->customer->alamat ?? ($tugas->faktur->customer->kota ?? 'Tasikmalaya') }}
                                @if(!empty($tugas->faktur->customer->no_telepon))
                                    &bull; {{ $tugas->faktur->customer->no_telepon }}
                                @endif
                            </div>
                        </td>
                        <td class="py-2.5 px-2.5 text-right font-mono font-semibold text-slate-800 whitespace-nowrap">
                            Rp {{ number_format($fTotal, 0, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-2.5 text-right font-mono font-semibold text-[#0E527B] whitespace-nowrap">
                            Rp {{ number_format($pendingBsk ? $pendingBsk->nominal_titipan : $fTerbayar, 0, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-2.5 text-right font-mono font-black text-slate-900 whitespace-nowrap">
                            Rp {{ number_format($fSisa, 0, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-2.5 text-center whitespace-nowrap">
                            @if($isLunas)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#0E527B] text-white">Lunas</span>
                            @elseif($pendingBsk)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#FFB900] text-[#07334F]">Pending QR</span>
                            @elseif($isBatal)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-200 text-slate-700">Ditolak</span>
                            @elseif($isMenunggu)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-100 text-amber-800">Menunggu</span>
                            @elseif($fTerbayar > 0)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-sky-50 text-[#0E527B] border border-sky-200">Cicilan</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 text-slate-600">Belum Bayar</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-50 border-t-2 border-slate-300 font-bold text-xs">
                <tr>
                    <td colspan="3" class="py-2.5 px-2.5 text-right uppercase text-[10px] text-slate-700">
                        Total Keseluruhan ({{ $tasks->count() }} Faktur):
                    </td>
                    <td class="py-2.5 px-2.5 text-right font-mono text-slate-900 whitespace-nowrap">
                        Rp {{ number_format($paperTotalTagihan, 0, ',', '.') }}
                    </td>
                    <td class="py-2.5 px-2.5 text-right font-mono text-[#0E527B] whitespace-nowrap">
                        Rp {{ number_format($paperTotalTerbayar, 0, ',', '.') }}
                    </td>
                    <td class="py-2.5 px-2.5 text-right font-mono font-black text-slate-900 whitespace-nowrap">
                        Rp {{ number_format($paperTotalSisa, 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <p class="text-[11px] text-slate-600 leading-relaxed">
        Demikian surat penugasan penagihan resmi ini diterbitkan. Hasil penagihan wajib disetorkan ke Bagian Kasir / Keuangan melalui bukti setoran (BSK) sesuai ketentuan perusahaan.
    </p>

    <!-- Pengesahan / Tanda Tangan Resmi -->
    <div class="grid grid-cols-2 gap-6 text-center text-[11px] pt-6 border-t border-slate-200 mt-6">
        <div>
            <span class="text-slate-500 block mb-12">Petugas Penagih (Inkaso),</span>
            <div class="border-t border-slate-400 pt-1.5 font-extrabold text-slate-900 uppercase">
                {{ $paperNamaPetugas }}
            </div>
        </div>
        <div>
            <span class="text-slate-500 block mb-12">Tasikmalaya, {{ $paperTanggal }}<br>Bagian Faktur &amp; Keuangan,</span>
            <div class="border-t border-slate-400 pt-1.5 font-extrabold text-slate-900">
                Kepala Divisi Keuangan
            </div>
        </div>
    </div>
</div>
