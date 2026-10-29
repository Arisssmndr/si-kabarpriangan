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

    <!-- Kontrol Cetak & Input Nama Petugas (Non-Printable Bar) -->
    <div class="max-w-3xl mx-auto mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3 no-print bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <a href="{{ route('keuangan.faktur.index', ['tab' => 'piutang']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                ← Kembali ke Faktur
            </a>
            <div class="flex items-center gap-2">
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

        <button type="button" onclick="window.print()" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-md transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Surat Tugas</span>
        </button>
    </div>

    <!-- Lembar Surat Tugas -->
    <div class="max-w-3xl mx-auto bg-white p-8 sm:p-12 rounded-2xl border border-slate-200 shadow-lg print-shadow">
        
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
            </div>

            <p>
                Untuk melakukan penagihan kewajiban sisa piutang atas transaksi faktur {{ $faktur->kategori === 'sirkulasi' ? 'distribusi oplah dan penjualan sirkulasi koran' : 'penjualan pemuatan iklan' }} sebagai berikut:
            </p>

            <!-- Tabel Rincian Piutang Faktur -->
            <table class="w-full text-xs text-left my-4 border border-slate-300">
                <thead class="bg-slate-100 uppercase text-[11px] font-bold text-slate-700 border-b border-slate-300">
                    <tr>
                        <th class="py-2.5 px-3">Nomor Faktur</th>
                        <th class="py-2.5 px-3">{{ $faktur->kategori === 'sirkulasi' ? 'Agen / Pelanggan Sirkulasi' : 'Pemasang / Pelanggan Iklan' }}</th>
                        <th class="py-2.5 px-3">Alamat Pelanggan</th>
                        <th class="py-2.5 px-3 text-right">Total Tagihan</th>
                        <th class="py-2.5 px-3 text-right">Sudah Bayar</th>
                        <th class="py-2.5 px-3 text-right">Sisa Piutang Wajib Ditagih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <tr>
                        <td class="py-3 px-3 font-mono font-bold text-blue-700">{{ $faktur->no_faktur }}</td>
                        <td class="py-3 px-3 font-bold text-slate-900">
                            {{ $faktur->customer->nama_customer ?? 'UMUM' }}
                            @if(!empty($faktur->customer->no_telepon))
                                <div class="text-[10px] font-normal text-slate-500">Telp: {{ $faktur->customer->no_telepon }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-slate-600">
                            {{ $faktur->customer->alamat ?? '-' }}
                            @if(!empty($faktur->customer->kota))
                                , {{ $faktur->customer->kota }}
                            @endif
                        </td>
                        <td class="py-3 px-3 text-right font-mono font-bold text-slate-900">
                            Rp {{ number_format($faktur->total_tagihan, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-emerald-600 font-semibold">
                            Rp {{ number_format($faktur->total_terbayar, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-right font-black font-mono text-rose-600 text-sm">
                            Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>

            @if($faktur->catatan)
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-xs">
                    <strong>Catatan Faktur:</strong> {{ $faktur->catatan }}
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
