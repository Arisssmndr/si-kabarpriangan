<x-app-layout>
    @php
        \Carbon\Carbon::setLocale('id');
        $tot = (float) $paket->total_tagihan;
        $byr = (float) ($paket->jumlah_bayar ?? 0);
        $isTunai = ($paket->jenis_pembayaran === 'Tunai' || ($tot > 0 && $byr >= $tot));
        $statusBayarLabel = $isTunai ? 'Tunai' : 'Kredit';
        $statusBayarClass = $isTunai
            ? 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200'
            : 'bg-slate-100 text-slate-700 border-slate-200';
        $noPerkiraan = \App\Http\Controllers\LaporanController::resolveNoPerkiraan('koran', $tot, $byr, (int) ($paket->pelanggan_id ?? $paket->id), $paket->jenis_pembayaran ?? null);
    @endphp

    <div class="space-y-5">

        <!-- 1. Header Detail Paket (60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-10 w-auto object-contain shrink-0">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 font-mono tracking-tight">
                            {{ $paket->nofaktur_paket }}
                        </h1>
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold border {{ $statusBayarClass }}">
                            {{ $statusBayarLabel }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Tanggal Order: {{ \Carbon\Carbon::parse($paket->tanggal_transaksi)->format('d/m/Y') }} &bull;
                        {{ $paket->nama_paket ?: 'Paket Bundling Lintas Media' }} &bull;
                        Petugas: {{ $paket->inputOleh->name ?? 'Front Office' }}
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('transaksipaket.index') }}"
                   class="inline-flex items-center gap-1.5 h-9 px-3.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl transition">
                    &larr; Kembali
                </a>
                <a href="{{ route('transaksipaket.cetak', $paket->id) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 h-9 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak Faktur Paket
                </a>
            </div>
        </div>

        <!-- 2. Grid Utama: Kiri (Pemesan & Komponen Media), Kanan (Rincian Tagihan Paket) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

            <!-- Kolom Kiri (8 Kolom) -->
            <div class="lg:col-span-8 space-y-5">

                <!-- Informasi Pemesan & Sales -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">Informasi Pemesan &amp; Sales</h2>
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            {{ implode(' + ', is_array($paket->media_terpilih) ? $paket->media_terpilih : []) }}
                        </span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-xs text-slate-500 block">Nama Pemasang / Instansi</span>
                            <p class="font-semibold text-slate-900 mt-0.5">{{ $paket->nama_pemasang }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 block">Penanggung Jawab (PIC) &amp; Kontak</span>
                            <p class="font-semibold text-slate-900 mt-0.5">{{ $paket->nama_pic ?: '-' }} {{ $paket->telepon_pemasang ? '• ' . $paket->telepon_pemasang : '' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 block">Alamat Pemasang</span>
                            <p class="font-semibold text-slate-900 mt-0.5">{{ $paket->alamat_pemasang ?: '-' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 block">Sales / Account Executive</span>
                            <p class="font-semibold text-slate-900 mt-0.5">{{ $paket->salesAe?->nama_ae ?: ($paket->sales_nama ?: '-') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Rincian Komponen Media dalam Paket -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">Komponen Saluran Media dalam Paket</h2>
                        <span class="text-xs text-slate-500">Alokasi Proporsional per Unit Media</span>
                    </div>

                    <div class="space-y-3">
                        @if($paket->transaksiCetak)
                            @php $ctk = $paket->transaksiCetak; @endphp
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-kp-blue-600 text-white font-mono">CTK</span>
                                        <span class="text-sm font-bold text-slate-900">Iklan Cetak — {{ $ctk->kategori_form }} ({{ $ctk->iklankoran?->nama_jenis ?: 'Reguler' }})</span>
                                        <a href="{{ route('transaksikoran.show', $ctk->id) }}" class="text-xs font-mono font-semibold text-kp-blue-700 hover:underline">
                                            {{ $ctk->nofakturkoran }} &rarr;
                                        </a>
                                    </div>
                                    <p class="text-xs text-slate-600">
                                        Ukuran: <strong>{{ $ctk->ukuran_iklan }}</strong> ({{ $ctk->warna_iklan }}) &bull;
                                        Penempatan: {{ $ctk->halaman_iklan }} &bull;
                                        Frekuensi: {{ $ctk->total_muatkoran }}x Terbit
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-[11px] text-slate-500">Alokasi Tagihan Cetak</div>
                                    <div class="font-mono text-sm font-bold text-slate-900">Rp {{ number_format($ctk->totaltagihan_transaksikoran, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        @endif

                        @if($paket->transaksiOnline)
                            @php $onl = $paket->transaksiOnline; @endphp
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-kp-blue-600 text-white font-mono">ONL</span>
                                        <span class="text-sm font-bold text-slate-900">Iklan Online — {{ $onl->iklanonline?->nama_jenis ?: 'Publikasi Online' }}</span>
                                        <a href="{{ route('transaksionline.show', $onl->id) }}" class="text-xs font-mono font-semibold text-kp-blue-700 hover:underline">
                                            {{ $onl->nofakturonline }} &rarr;
                                        </a>
                                    </div>
                                    <p class="text-xs text-slate-600">
                                        Judul: <strong>{{ $onl->materi_iklan }}</strong> &bull;
                                        Portal: {{ $onl->portal_iklanonline ?: '-' }} &bull;
                                        Frekuensi: {{ $onl->total_muatiklanonline }}x Tayang
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-[11px] text-slate-500">Alokasi Tagihan Online</div>
                                    <div class="font-mono text-sm font-bold text-slate-900">Rp {{ number_format($onl->totaltagihan_transaksionline, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        @endif

                        @if($paket->transaksiTv)
                            @php $ptv = $paket->transaksiTv; @endphp
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-kp-blue-600 text-white font-mono">PTV</span>
                                        <span class="text-sm font-bold text-slate-900">Iklan TV — {{ $ptv->iklanpriangan?->nama_jenis ?: 'Program TV' }}</span>
                                        <a href="{{ route('transaksipriangan.show', $ptv->id) }}" class="text-xs font-mono font-semibold text-kp-blue-700 hover:underline">
                                            {{ $ptv->nofakturpriangan }} &rarr;
                                        </a>
                                    </div>
                                    <p class="text-xs text-slate-600">
                                        Judul: <strong>{{ $ptv->materi_iklan }}</strong> &bull;
                                        Durasi: {{ $ptv->durasi_detik }} dtk ({{ $ptv->jam_tayang }}) &bull;
                                        Frekuensi: {{ $ptv->total_muatiklanpriangan }}x Siar
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-[11px] text-slate-500">Alokasi Tagihan TV</div>
                                    <div class="font-mono text-sm font-bold text-slate-900">Rp {{ number_format($ptv->totaltagihan_transaksipriangan, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Kolom Kanan (4 Kolom): Rincian Tagihan Paket -->
            <div class="lg:col-span-4 space-y-5">
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">Rincian Tagihan Paket</h2>
                        <span class="text-xs font-medium text-slate-500">{{ count(is_array($paket->media_terpilih) ? $paket->media_terpilih : []) }} Media</span>
                    </div>

                    <div class="space-y-2.5 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Total Brutto Gabungan</span>
                            <span class="font-mono font-semibold text-slate-900">Rp {{ number_format($paket->nilai_brutto, 0, ',', '.') }}</span>
                        </div>

                        @if($paket->nilai_diskon > 0)
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Diskon Paket ({{ $paket->diskon_persen }}%)</span>
                                <span class="font-mono font-medium text-slate-700">- Rp {{ number_format($paket->nilai_diskon, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between">
                            <span class="font-medium text-slate-700">Nilai Netto (DPP)</span>
                            <span class="font-mono font-semibold text-slate-900">Rp {{ number_format($paket->nilai_netto, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">PPN ({{ $paket->ppn_persen }}%)</span>
                            <span class="font-mono font-medium text-slate-800">Rp {{ number_format($paket->nilai_ppn, 0, ',', '.') }}</span>
                        </div>

                        <div class="my-3 p-3.5 bg-kp-blue-50/70 border border-kp-blue-200 rounded-xl flex items-center justify-between">
                            <span class="text-xs font-bold text-kp-blue-700 uppercase tracking-wider">Total Tagihan</span>
                            <span class="font-mono text-base font-bold text-kp-blue-700">
                                Rp {{ number_format($paket->total_tagihan, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                            <span class="font-medium text-slate-700">Jenis Pembayaran</span>
                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-semibold border {{ $statusBayarClass }}">
                                {{ $statusBayarLabel }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">No. Perkiraan</span>
                            <span class="font-mono text-xs font-semibold text-slate-700">{{ $noPerkiraan }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Nominal Uang Muka / Bayar</span>
                            <span class="font-mono font-semibold text-slate-900">Rp {{ number_format($paket->jumlah_bayar, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- 3. Tabel Full-Width Jadwal Terbit & Tayang Lintas Media -->
        @php
            $allSchedules = [];
            if ($paket->transaksiCetak) {
                foreach ($paket->transaksiCetak->jadwalMuat as $j) {
                    $isBonus = ($j->is_bonus || $j->jenis_muat === 'BONUS' || $j->edisi_ke > 1);
                    $allSchedules[] = [
                        'media'      => 'Iklan Cetak',
                        'kode'       => 'CTK',
                        'faktur'     => $paket->transaksiCetak->nofakturkoran,
                        'edisi'      => $j->edisi_ke,
                        'tanggal'    => \Carbon\Carbon::parse($j->tanggal_muat)->format('Y-m-d'),
                        'is_bonus'   => $isBonus,
                        'nominal'    => $isBonus ? 0 : (float) ($j->nominal_muat ?: $paket->transaksiCetak->totaltagihan_transaksikoran),
                        'keterangan' => $j->catatan ?: ($isBonus ? 'Terbayar di Edisi #1' : 'Induk Tagihan Cetak'),
                    ];
                }
            }
            if ($paket->transaksiOnline) {
                $datesOnl = array_values(array_filter(array_map('trim', explode(',', (string) ($paket->transaksiOnline->tanggal_muat_list ?: $paket->transaksiOnline->tanggal_muatiklanonline?->format('Y-m-d'))))));
                foreach ($datesOnl as $idx => $dStr) {
                    $isBonus = ($idx > 0);
                    $allSchedules[] = [
                        'media'      => 'Iklan Online',
                        'kode'       => 'ONL',
                        'faktur'     => $paket->transaksiOnline->nofakturonline,
                        'edisi'      => $idx + 1,
                        'tanggal'    => \Carbon\Carbon::parse($dStr)->format('Y-m-d'),
                        'is_bonus'   => $isBonus,
                        'nominal'    => $isBonus ? 0 : (float) $paket->transaksiOnline->totaltagihan_transaksionline,
                        'keterangan' => $isBonus ? 'Tayang Bonus (Terbayar di Tayang #1)' : 'Induk Tagihan Online',
                    ];
                }
            }
            if ($paket->transaksiTv) {
                $datesPtv = array_values(array_filter(array_map('trim', explode(',', (string) ($paket->transaksiTv->tanggal_muat_list ?: $paket->transaksiTv->tanggal_muatiklanpriangan?->format('Y-m-d'))))));
                foreach ($datesPtv as $idx => $dStr) {
                    $isBonus = ($idx > 0);
                    $allSchedules[] = [
                        'media'      => 'Iklan TV',
                        'kode'       => 'PTV',
                        'faktur'     => $paket->transaksiTv->nofakturpriangan,
                        'edisi'      => $idx + 1,
                        'tanggal'    => \Carbon\Carbon::parse($dStr)->format('Y-m-d'),
                        'is_bonus'   => $isBonus,
                        'nominal'    => $isBonus ? 0 : (float) $paket->transaksiTv->totaltagihan_transaksipriangan,
                        'keterangan' => $isBonus ? 'Siar Bonus (Terbayar di Spot #1)' : 'Induk Tagihan TV',
                    ];
                }
            }
        @endphp

        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Jadwal Terbit &amp; Tayang Lintas Media</h2>
                <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    {{ count($allSchedules) }} Jadwal
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-2.5 px-4">Saluran Media</th>
                            <th class="py-2.5 px-4">Faktur Media</th>
                            <th class="py-2.5 px-4">Edisi / Ke</th>
                            <th class="py-2.5 px-4">Tanggal Terbit / Tayang</th>
                            <th class="py-2.5 px-4">Hari</th>
                            <th class="py-2.5 px-4 text-center">Jenis Pemuatan</th>
                            <th class="py-2.5 px-4 text-right">Nilai Pemuatan</th>
                            <th class="py-2.5 px-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-sm whitespace-nowrap">
                        @forelse($allSchedules as $sch)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 font-semibold text-slate-900">
                                    {{ $sch['media'] }}
                                </td>
                                <td class="py-3 px-4 font-mono text-xs font-bold text-kp-blue-700">
                                    {{ $sch['faktur'] }}
                                </td>
                                <td class="py-3 px-4 font-semibold text-slate-900">
                                    Edisi #{{ $sch['edisi'] }}
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-800">
                                    {{ \Carbon\Carbon::parse($sch['tanggal'])->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ \Carbon\Carbon::parse($sch['tanggal'])->locale('id')->translatedFormat('l') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($sch['is_bonus'])
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            Muat Bonus
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Muat Bernilai
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-mono {{ $sch['is_bonus'] ? 'text-slate-400 font-normal' : 'text-kp-blue-700 font-bold' }}">
                                    Rp {{ number_format($sch['nominal'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-slate-500 text-xs">
                                    {{ $sch['keterangan'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-6 text-center text-xs text-slate-400">
                                    Belum ada jadwal terbit/tayang.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>

