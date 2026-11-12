<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Penugasan Penagihan Lapangan (Inkaso) - {{ $faktur->no_faktur }}</title>
    <link rel="icon" href="{{ asset('kabarpriangan.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-shadow { box-shadow: none !important; border: 1px solid #cbd5e1 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans p-4 sm:p-8">

    @php
        $listFakturs = isset($fakturs) && $fakturs->count() > 0 ? $fakturs : collect([$faktur]);
        $totalTagihanInkaso = (float) $listFakturs->sum('total_tagihan');
        $totalTerbayarInkaso = (float) $listFakturs->sum('total_terbayar');
        $totalSisaPiutangInkaso = (float) $listFakturs->sum('sisa_piutang');
        $selectedIdsArr = isset($selectedIds) ? $selectedIds->toArray() : [$faktur->id];
    @endphp

    <!-- Kontrol Cetak, Pilih Petugas & Pilih Maks 10 Faktur (Non-Printable Bar) -->
    <div class="max-w-4xl mx-auto mb-6 no-print bg-white p-4 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-wrap">
                <a href="{{ route('keuangan.faktur.index', ['tab' => 'piutang']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    ← Kembali ke Faktur
                </a>
                <div class="flex items-center gap-2 flex-wrap">
                    <label for="selectPetugasTop" class="text-xs font-bold text-slate-700 whitespace-nowrap flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        <span>Pilih Petugas:</span>
                    </label>
                    <select id="selectPetugasTop" 
                            class="h-9 px-3 rounded-xl border border-amber-300 bg-amber-50/40 text-xs text-slate-900 font-bold focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-hidden shadow-2xs cursor-pointer">
                        <option value="">-- Format Tulis Tangan (Titik-titik) --</option>
                        <option value="Ahmad Supardi" {{ $namaPetugas === 'Ahmad Supardi' ? 'selected' : '' }}>Ahmad Supardi (Kota Tasikmalaya)</option>
                        <option value="Rudi Hermawan" {{ $namaPetugas === 'Rudi Hermawan' ? 'selected' : '' }}>Rudi Hermawan (Ciamis &amp; Singaparna)</option>
                        <option value="Dadan Ramdani" {{ $namaPetugas === 'Dadan Ramdani' ? 'selected' : '' }}>Dadan Ramdani (Banjar &amp; Pangandaran)</option>
                        <option value="Yudi Permana" {{ $namaPetugas === 'Yudi Permana' ? 'selected' : '' }}>Yudi Permana (Garut &amp; Priangan)</option>
                        <option value="__custom__" {{ !in_array($namaPetugas, ['', 'Ahmad Supardi', 'Rudi Hermawan', 'Dadan Ramdani', 'Yudi Permana']) ? 'selected' : '' }}>✏️ Ketik Nama Lainnya...</option>
                    </select>

                    <input type="text" 
                           id="petugasInput" 
                           value="{{ $namaPetugas }}" 
                           placeholder="Ketik nama petugas..." 
                           autocomplete="off"
                           class="h-9 px-3 rounded-xl border border-amber-300 bg-white text-xs text-slate-900 font-bold focus:ring-2 focus:ring-amber-500 focus:outline-hidden w-56 shadow-2xs {{ in_array($namaPetugas, ['Ahmad Supardi', 'Rudi Hermawan', 'Dadan Ramdani', 'Yudi Permana', '']) ? 'hidden' : '' }}">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="togglePanelPilihFaktur()" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 text-xs font-bold transition cursor-pointer">
                    <span>+ Pilih Faktur Inkaso (<span id="badgeCountTop">{{ $listFakturs->count() }}</span>/10)</span>
                </button>
                <button type="button" onclick="window.print()" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-md transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Surat Tugas</span>
                </button>
            </div>
        </div>

        <!-- Panel Pilih s/d 10 Faktur Piutang untuk 1 Petugas Inkaso -->
        <div id="panelPilihFaktur" style="display: none;" class="pt-3 border-t border-slate-200 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="text-xs font-black text-blue-950 uppercase tracking-wider">
                        Pilih Daftar Faktur Piutang yang Dibawa Petugas Inkaso Ini (Maksimal 10 Faktur)
                    </h3>
                    <p class="text-[11px] text-slate-500">Centang faktur piutang di bawah ini untuk digabungkan ke dalam 1 Surat Penugasan Inkaso.</p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="text" id="searchFakturInkaso" oninput="filterFakturInkasoList(this.value)" placeholder="Cari No. Faktur / Pelanggan..."
                           class="h-8 px-3 rounded-lg border border-slate-300 text-xs text-slate-800 focus:border-blue-500">
                    <span class="px-2.5 py-1 rounded-lg bg-amber-100 text-amber-950 border border-amber-300 text-xs font-black whitespace-nowrap">
                        Terpilih: <span id="selectedCountLabel">{{ $listFakturs->count() }}</span> / 10 Faktur
                    </span>
                </div>
            </div>

            <div id="warningMaxFaktur" style="display: none;" class="p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold">
                Maksimal 10 faktur yang dapat diambil dalam 1 Surat Penugasan Inkaso.
            </div>

            <div class="border border-slate-200 rounded-xl overflow-y-auto bg-slate-50/50 p-2 grid grid-cols-1 sm:grid-cols-2 gap-2" style="max-height: 240px;">
                @foreach($daftarFakturPiutang ?? [] as $fp)
                    @php $isChecked = in_array($fp->id, $selectedIdsArr); @endphp
                    <label class="faktur-inkaso-option flex items-start gap-2.5 p-2.5 rounded-xl border {{ $isChecked ? 'bg-blue-50/80 border-blue-300' : 'bg-white border-slate-200' }} hover:border-blue-300 cursor-pointer transition text-xs"
                           data-search="{{ strtolower($fp->no_faktur . ' ' . ($fp->customer->nama_customer ?? '')) }}">
                        <input type="checkbox"
                               name="pilih_faktur_id"
                               value="{{ $fp->id }}"
                               {{ $isChecked ? 'checked' : '' }}
                               onchange="handleCheckboxFakturInkaso(this)"
                               class="mt-0.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono font-bold text-blue-700">{{ $fp->no_faktur }}</span>
                                <span class="font-mono font-black text-rose-600 text-[11px]">Rp {{ number_format($fp->sisa_piutang, 0, ',', '.') }}</span>
                            </div>
                            <div class="font-bold text-slate-900 truncate">{{ $fp->customer->nama_customer ?? 'UMUM' }}</div>
                            <div class="text-[10px] text-slate-400 truncate">{{ $fp->customer->alamat ?? '-' }}</div>
                        </div>
                    </label>
                @endforeach
            </div>

            <div class="flex items-center justify-end gap-2">
                <button type="button" onclick="togglePanelPilihFaktur()" class="px-3.5 py-1.5 rounded-xl border border-slate-300 bg-white text-slate-700 text-xs font-bold cursor-pointer">
                    Tutup
                </button>
                <button type="button" onclick="terapkanPilihanFakturInkaso()" class="px-4 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                    Terapkan ke Surat Tugas
                </button>
            </div>
        </div>
    </div>

    <!-- Lembar Surat Tugas -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-2xl border border-slate-200 shadow-lg print-shadow">
        
        <!-- Kop Surat HU Kabar Priangan -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-5 mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-12 w-auto object-contain shrink-0" onerror="this.src='{{ asset('images/kabarpriangan.png') }}'">
                <div>
                    <h1 class="text-lg font-black text-slate-900 uppercase tracking-tight">HARIAN UMUM KABAR PRIANGAN</h1>
                    <p class="text-xs text-slate-600">DIVISI KEUANGAN &bull; BAGIAN PENAGIHAN PIUTANG {{ $faktur->kategori === 'sirkulasi' ? 'SIRKULASI' : 'IKLAN' }} (INKASO)</p>
                    <p class="text-[11px] text-slate-500">Jl. Dr. Sukardjo No. 51 Kota Tasikmalaya | Telp: (0265) 331234</p>
                </div>
            </div>

            <div class="text-right">
                <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-amber-800 bg-amber-50 px-2.5 py-1 rounded border border-amber-200 block mb-1">
                    SURAT PENUGASAN PENAGIHAN {{ $faktur->kategori === 'sirkulasi' ? 'SIRKULASI' : 'IKLAN' }}
                </span>
                <span class="text-xs font-mono font-black text-slate-900">
                    ST-INK/{{ str_replace('/', '-', $faktur->no_faktur) }}/{{ date('Y') }}
                </span>
            </div>
        </div>

        <div class="text-center mb-6">
            <h2 class="text-base font-black text-slate-900 uppercase tracking-wide underline underline-offset-4">
                SURAT PENUGASAN PENAGIHAN PIUTANG {{ $faktur->kategori === 'sirkulasi' ? 'SIRKULASI' : 'IKLAN' }} (INKASO)
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Nomor: ST-INK/{{ str_replace('/', '-', $faktur->no_faktur) }}/{{ date('m/Y') }}
            </p>
        </div>

        <div class="text-xs text-slate-700 leading-relaxed mb-6 space-y-3">
            <p>
                Yang bertanda tangan di bawah ini, Kepala Divisi Keuangan Harian Umum Kabar Priangan memberikan penugasan resmi kepada:
            </p>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-1.5 font-medium">
                <div class="flex items-baseline">
                    <span class="w-36 text-slate-500 shrink-0">Nama Petugas:</span>
                    <strong id="previewNamaPetugas" 
                            title="Klik input di atas untuk mengganti nama petugas"
                            class="text-slate-900 uppercase tracking-wide font-bold cursor-pointer">
                        {{ !empty($namaPetugas) ? strtoupper($namaPetugas) : '................................................... (PETUGAS PENAGIH LAPANGAN)' }}
                    </strong>
                </div>
                <div class="flex items-baseline">
                    <span class="w-36 text-slate-500 shrink-0">Jabatan:</span>
                    <span>Petugas Penagihan Lapangan (Inkaso {{ $faktur->kategori === 'sirkulasi' ? 'Sirkulasi' : 'Iklan' }})</span>
                </div>
                <div class="flex items-baseline">
                    <span class="w-36 text-slate-500 shrink-0">Tanggal Penugasan:</span>
                    <span class="font-mono">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                </div>
                <div class="flex items-baseline">
                    <span class="w-36 text-slate-500 shrink-0">Jumlah Dokumen:</span>
                    <span class="font-bold text-slate-900">{{ $listFakturs->count() }} Faktur Piutang (Maksimal 10 Faktur / Penugasan)</span>
                </div>
            </div>

            <p>
                Untuk melakukan penagihan kewajiban sisa piutang atas transaksi faktur {{ $faktur->kategori === 'sirkulasi' ? 'distribusi oplah dan penjualan sirkulasi koran' : 'penjualan pemuatan iklan' }} sebagai berikut:
            </p>

            <!-- Tabel Rincian Piutang Faktur (1 s/d 10 Faktur) -->
            <table class="w-full text-xs text-left my-4 border border-slate-300">
                <thead class="bg-slate-100 uppercase text-[10px] font-bold text-slate-700 border-b border-slate-300">
                    <tr>
                        <th class="py-2.5 px-2 text-center w-8">No</th>
                        <th class="py-2.5 px-2.5">Nomor Faktur</th>
                        <th class="py-2.5 px-2.5">{{ $faktur->kategori === 'sirkulasi' ? 'Agen / Pelanggan Sirkulasi' : 'Pemasang / Pelanggan Iklan' }}</th>
                        <th class="py-2.5 px-2.5">Alamat Pelanggan</th>
                        <th class="py-2.5 px-2.5 text-right">Total Tagihan</th>
                        <th class="py-2.5 px-2.5 text-right">Sudah Bayar</th>
                        <th class="py-2.5 px-2.5 text-right">Sisa Piutang Wajib Ditagih</th>
                        <th class="py-2.5 px-3 border-l border-slate-300 w-52">
                            Keterangan / Dibayarkan ({{ $faktur->kategori === 'sirkulasi' ? 'Agen' : 'Pemasang' }})
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($listFakturs as $idx => $itemFaktur)
                        <tr>
                            <td class="py-3 px-2 text-center font-mono font-bold text-slate-600">{{ $idx + 1 }}</td>
                            <td class="py-3 px-2.5 font-mono font-bold text-blue-700 whitespace-nowrap">
                                {{ $itemFaktur->no_faktur }}
                                @if($itemFaktur->tanggal_faktur)
                                    <div class="text-[10px] font-normal text-slate-400">{{ $itemFaktur->tanggal_faktur->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-2.5 font-bold text-slate-900">
                                {{ $itemFaktur->customer->nama_customer ?? 'UMUM' }}
                                @if(!empty($itemFaktur->customer->no_telepon))
                                    <div class="text-[10px] font-normal text-slate-500">Telp: {{ $itemFaktur->customer->no_telepon }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-2.5 text-slate-600">
                                {{ $itemFaktur->customer->alamat ?? '-' }}
                                @if(!empty($itemFaktur->customer->kota))
                                    , {{ $itemFaktur->customer->kota }}
                                @endif
                            </td>
                            <td class="py-3 px-2.5 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                Rp {{ number_format($itemFaktur->total_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-2.5 text-right font-mono text-emerald-600 font-semibold whitespace-nowrap">
                                Rp {{ number_format($itemFaktur->total_terbayar, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-2.5 text-right font-black font-mono text-rose-600 text-xs whitespace-nowrap">
                                Rp {{ number_format($itemFaktur->sisa_piutang, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 border-l border-slate-200 align-top">
                                <div class="space-y-2 text-[11px] text-slate-700">
                                    <div class="flex items-baseline gap-1">
                                        <span class="font-semibold text-slate-600 shrink-0">Bayar: Rp</span>
                                        <span class="flex-1 border-b border-dotted border-slate-400 inline-block min-w-[100px]">&nbsp;</span>
                                    </div>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-[10px] text-slate-500 shrink-0">Ket/Paraf:</span>
                                        <span class="flex-1 border-b border-dotted border-slate-400 inline-block min-w-[90px]">&nbsp;</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-300 font-bold">
                    <tr>
                        <td colspan="4" class="py-2.5 px-2.5 text-right uppercase text-[11px] text-slate-700">
                            Total Kewajiban Penagihan ({{ $listFakturs->count() }} Faktur):
                        </td>
                        <td class="py-2.5 px-2.5 text-right font-mono text-slate-900 whitespace-nowrap">
                            Rp {{ number_format($totalTagihanInkaso, 0, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-2.5 text-right font-mono text-emerald-700 whitespace-nowrap">
                            Rp {{ number_format($totalTerbayarInkaso, 0, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-2.5 text-right font-mono font-black text-rose-700 text-xs whitespace-nowrap">
                            Rp {{ number_format($totalSisaPiutangInkaso, 0, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-3 border-l border-slate-300 text-[11px] text-slate-800">
                            <div class="flex items-baseline gap-1">
                                <span class="font-bold shrink-0">Total Setor: Rp</span>
                                <span class="flex-1 border-b border-dotted border-slate-500 inline-block min-w-[75px]">&nbsp;</span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>

            @php
                $catatanList = $listFakturs->filter(fn ($f) => !empty($f->catatan));
            @endphp
            @if($catatanList->isNotEmpty())
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-xs space-y-1">
                    <strong>Catatan Faktur:</strong>
                    @if($catatanList->count() === 1)
                        <span>{{ $catatanList->first()->catatan }}</span>
                    @else
                        <ul class="list-disc pl-4 space-y-0.5 mt-1">
                            @foreach($catatanList as $cf)
                                <li><span class="font-mono font-bold">{{ $cf->no_faktur }}:</span> {{ $cf->catatan }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            <p class="pt-2">
                Demikian surat tugas penagihan ini diterbitkan untuk dipergunakan sebagaimana mestinya. Hasil setoran penagihan dari klien wajib diserahkan langsung ke Bagian Kasir / Keuangan selambat-lambatnya 1 x 24 jam setelah penagihan dilakukan.
            </p>
        </div>

        <!-- Tanda Tangan -->
        <div class="grid grid-cols-2 gap-8 text-center text-xs pt-8 border-t border-slate-200 mt-8">
            <div>
                <span class="text-slate-500 block mb-16">Petugas Penagih (Inkaso),</span>
                <div class="border-t border-slate-400 pt-1 font-bold text-slate-800" id="previewTandaTangan">
                    ( {{ !empty($namaPetugas) ? strtoupper($namaPetugas) : '...............................................' }} )
                </div>
            </div>
            <div>
                <span class="text-slate-500 block mb-16">Tasikmalaya, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>Kepala Divisi Keuangan,</span>
                <div class="border-t border-slate-400 pt-1 font-bold text-slate-800">
                    ( H. Asep Ridwan, S.E. )
                </div>
            </div>
        </div>

    </div>

    <!-- Script Reaktif Vanilla JS (Instan & Tanpa Dependensi) -->
    <script>
        function togglePanelPilihFaktur() {
            const panel = document.getElementById('panelPilihFaktur');
            if (!panel) return;
            panel.style.display = (panel.style.display === 'none' || !panel.style.display) ? 'block' : 'none';
        }

        function handleCheckboxFakturInkaso(cb) {
            const checked = document.querySelectorAll('input[name="pilih_faktur_id"]:checked');
            const warning = document.getElementById('warningMaxFaktur');
            if (checked.length > 10) {
                cb.checked = false;
                if (warning) warning.style.display = 'block';
                return;
            }
            if (warning) warning.style.display = 'none';
            const count = document.querySelectorAll('input[name="pilih_faktur_id"]:checked').length;
            const lbl = document.getElementById('selectedCountLabel');
            const badgeTop = document.getElementById('badgeCountTop');
            if (lbl) lbl.textContent = count;
            if (badgeTop) badgeTop.textContent = count;
        }

        function filterFakturInkasoList(q) {
            const term = (q || '').toLowerCase().trim();
            document.querySelectorAll('.faktur-inkaso-option').forEach(function(el) {
                const hay = el.getAttribute('data-search') || '';
                el.style.display = (!term || hay.indexOf(term) !== -1) ? 'flex' : 'none';
            });
        }

        function terapkanPilihanFakturInkaso() {
            const checked = Array.from(document.querySelectorAll('input[name="pilih_faktur_id"]:checked')).map(el => el.value);
            if (checked.length === 0) {
                alert('Pilih minimal 1 faktur piutang untuk Surat Tugas Inkaso.');
                return;
            }
            const selectEl = document.getElementById('selectPetugasTop');
            const inputEl = document.getElementById('petugasInput');
            let petugas = '';
            if (selectEl) {
                petugas = selectEl.value === '__custom__' ? (inputEl ? inputEl.value.trim() : '') : selectEl.value;
            }
            const firstId = checked[0];
            const url = new URL("{{ url('keuangan/faktur') }}/" + firstId + "/surat-tugas", window.location.origin);
            if (petugas) url.searchParams.set('petugas', petugas);
            url.searchParams.set('faktur_ids', checked.slice(0, 10).join(','));
            window.location.href = url.toString();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selectEl = document.getElementById('selectPetugasTop');
            const inputEl = document.getElementById('petugasInput');
            const displayNama = document.getElementById('previewNamaPetugas');
            const displayTTD = document.getElementById('previewTandaTangan');

            function syncPetugasName(val) {
                const trimmed = (val || '').trim();
                if (displayNama) {
                    if (trimmed) {
                        displayNama.textContent = trimmed.toUpperCase();
                        displayNama.classList.add('text-slate-900');
                        displayNama.classList.remove('text-slate-400');
                    } else {
                        displayNama.textContent = '................................................... (PETUGAS PENAGIH LAPANGAN)';
                    }
                }
                if (displayTTD) {
                    if (trimmed) {
                        displayTTD.textContent = '( ' + trimmed.toUpperCase() + ' )';
                    } else {
                        displayTTD.textContent = '( ............................................... )';
                    }
                }
            }

            if (selectEl) {
                selectEl.addEventListener('change', function() {
                    const val = this.value;
                    if (val === '__custom__') {
                        inputEl.classList.remove('hidden');
                        inputEl.focus();
                        syncPetugasName(inputEl.value);
                    } else {
                        inputEl.classList.add('hidden');
                        inputEl.value = val;
                        syncPetugasName(val);
                    }
                });
            }

            if (inputEl) {
                inputEl.addEventListener('input', function() {
                    syncPetugasName(this.value);
                });

                // Auto-sync initial value
                if (inputEl.value) {
                    syncPetugasName(inputEl.value);
                }
            }

            // Jika teks di surat diklik, buka dropdown / input
            if (displayNama) {
                displayNama.addEventListener('click', function() {
                    if (selectEl) {
                        selectEl.focus();
                    }
                });
            }
        });
    </script>
</body>
</html>
