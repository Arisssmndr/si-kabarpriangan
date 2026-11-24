<x-app-layout>
    <script>
    function kasirSirkulasiApp() {
        return {
            showSetorModal: false, 
            showScanModal: false,
            scanInput: '',
            isScanning: false,
            scanError: '',
            cameraActive: false,
            html5QrScanner: null,
            autoProcess: true,
            isAutoSubmitting: false,
            autoSubmitInfo: { agen: '', kode: '', nominal: 0 },
            showCetakModal: false,
            init() {
                window.addEventListener('open-cetak-modal', () => {
                    this.showCetakModal = true;
                });

                // Global physical barcode scanner listener (laser USB/Bluetooth)
                let barcodeBuffer = '';
                let lastKeyTime = 0;
                window.addEventListener('keydown', (e) => {
                    const tag = (e.target.tagName || '').toLowerCase();
                    const isBarcodeField = e.target.getAttribute('x-ref') === 'barcodeInput' || e.target.name === 'scanInput';
                    if (!isBarcodeField && (tag === 'input' || tag === 'textarea' || tag === 'select')) {
                        return;
                    }
                    const now = Date.now();
                    if (now - lastKeyTime > 150) {
                        barcodeBuffer = '';
                    }
                    lastKeyTime = now;

                    if (e.key === 'Enter') {
                        if (barcodeBuffer.trim().length >= 3) {
                            e.preventDefault();
                            const code = barcodeBuffer.trim();
                            barcodeBuffer = '';
                            this.processBarcode(code);
                        }
                    } else if (e.key.length === 1) {
                        barcodeBuffer += e.key;
                    }
                });
            },
            activeAgen: { 
                id: null,
                kode: '',
                nama: '', 
                wilayah: '', 
                piutang: 0,
                bulan: '{{ $periodeLabel }}',
                nominal: 0,
                metode: 'tunai',
                bankTujuan: 'Bank BJB Sirkulasi',
                noRef: '',
                penagih: '{{ Auth::user()->name }}',
                keterangan: '',
                no_faktur: ''
            },
            openScanModal() {
                this.showScanModal = true;
                this.scanInput = '';
                this.scanError = '';
                this.$nextTick(() => {
                    if (this.$refs.barcodeInput) {
                        this.$refs.barcodeInput.focus();
                    }
                });
                if (!window._pasteBarcodeHandlerSirkulasi) {
                    window._pasteBarcodeHandlerSirkulasi = (e) => {
                        if (!this.showScanModal) return;
                        const items = (e.clipboardData || e.originalEvent.clipboardData).items;
                        for (let index in items) {
                            const item = items[index];
                            if (item.kind === 'file') {
                                const blob = item.getAsFile();
                                this.scanUploadedImage({ target: { files: [blob], value: '' } });
                                break;
                            }
                        }
                    };
                    window.addEventListener('paste', window._pasteBarcodeHandlerSirkulasi);
                }
            },
            closeScanModal() {
                this.showScanModal = false;
                this.stopCamera();
            },
            playBeep() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(1400, ctx.currentTime);
                    gain.gain.setValueAtTime(0.2, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.15);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.15);
                } catch(e) {}
            },
            async processBarcode(code) {
                code = (code || this.scanInput || '').trim();
                if (!code) {
                    this.scanError = 'Masukkan atau tembak barcode / kode agen!';
                    return;
                }
                this.isScanning = true;
                this.scanError = '';
                try {
                    const res = await fetch(`{{ route('kasir.api.scan-sirkulasi') }}?barcode=${encodeURIComponent(code)}`);
                    const json = await res.json();
                    if (json.success && json.data) {
                        this.playBeep();
                        const d = json.data;
                        const piutang = Number(d.saldo_piutang) || 0;

                        if (piutang <= 0) {
                            this.scanError = `Agen ${d.nama_agen} (${d.kode_agen}) sudah LUNAS BERSIH pada periode ini.`;
                            this.isScanning = false;
                            return;
                        }

                        this.activeAgen = {
                            id: d.id,
                            kode: d.kode_agen,
                            nama: d.nama_agen,
                            wilayah: d.wilayah || '-',
                            piutang: piutang,
                            bulan: d.bulan || '{{ $periodeLabel }}',
                            nominal: piutang,
                            metode: 'tunai',
                            bankTujuan: 'Bank BJB Sirkulasi',
                            noRef: '',
                            penagih: '{{ Auth::user()->name }}',
                            keterangan: `Setoran koran ${d.nama_agen} Periode ${d.bulan || '{{ $periodeLabel }}'}` + (d.no_faktur ? ` [Faktur: ${d.no_faktur}]` : ''),
                            no_faktur: d.no_faktur || ''
                        };
                        this.closeScanModal();

                        if (this.autoProcess) {
                            this.isAutoSubmitting = true;
                            this.autoSubmitInfo = {
                                kode: d.kode_agen,
                                agen: d.nama_agen,
                                nominal: piutang
                            };

                            this.$nextTick(() => {
                                const form = document.getElementById('form-setor-sirkulasi');
                                if (form) {
                                    const agenIdInput = form.querySelector('[name="agen_id"]');
                                    const nominalInput = form.querySelector('[name="nominal_setoran"]');
                                    const metodeInput = form.querySelector('[name="metode_pembayaran"]');
                                    const penagihInput = form.querySelector('[name="penagih_inkaso"]');
                                    const keteranganInput = form.querySelector('[name="keterangan"]');

                                    if (agenIdInput) agenIdInput.value = d.id;
                                    if (nominalInput) nominalInput.value = piutang;
                                    if (metodeInput) metodeInput.value = 'tunai';
                                    if (penagihInput) penagihInput.value = '{{ Auth::user()->name }}';
                                    if (keteranganInput) keteranganInput.value = `Setoran koran ${d.nama_agen} Periode {{ $periodeLabel }}`;

                                    form.submit();
                                }
                            });
                        } else {
                            this.showSetorModal = true;
                            this.$nextTick(() => {
                                const nomInput = document.querySelector('input[name="nominal_setoran"]');
                                if (nomInput) { nomInput.focus(); nomInput.select(); }
                            });
                        }
                    } else {
                        this.scanError = json.message || 'Agen sirkulasi tidak ditemukan!';
                    }
                } catch (err) {
                    this.scanError = 'Terjadi kesalahan jaringan saat memproses barcode: ' + (err.message || err);
                } finally {
                    this.isScanning = false;
                }
            },
            async startCamera() {
                this.cameraActive = true;
                this.scanError = '';
                this.$nextTick(async () => {
                    try {
                        if (typeof Html5Qrcode === 'undefined') {
                            this.scanError = 'Pustaka pemindai barcode belum selesai dimuat. Silakan refresh browser.';
                            this.cameraActive = false;
                            return;
                        }
                        if (this.html5QrScanner) {
                            try {
                                if (this.html5QrScanner.isScanning) {
                                    await this.html5QrScanner.stop();
                                }
                                this.html5QrScanner.clear();
                            } catch(e){}
                        }
                        this.html5QrScanner = new Html5Qrcode('reader-camera-sirkulasi');
                        const config = {
                            fps: 15,
                            qrbox: (viewfinderWidth, viewfinderHeight) => {
                                const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                                const edge = Math.floor(minEdge * 0.75);
                                return { width: edge, height: edge };
                            },
                            formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE]
                        };
                        await this.html5QrScanner.start(
                            { facingMode: 'environment' },
                            config,
                            (decodedText) => {
                                this.playBeep();
                                this.stopCamera();
                                this.processBarcode(decodedText);
                            },
                            (err) => {}
                        );
                    } catch (e) {
                        this.scanError = 'Gagal mengakses kamera: ' + (e.message || e) + '. Pastikan izin kamera aktif pada browser.';
                        this.cameraActive = false;
                    }
                });
            },
            stopCamera() {
                if (this.html5QrScanner) {
                    try {
                        if (this.html5QrScanner.isScanning) {
                            this.html5QrScanner.stop();
                        }
                        this.html5QrScanner.clear();
                    } catch(e) {}
                    this.html5QrScanner = null;
                }
                this.cameraActive = false;
            },
            async scanUploadedImage(event) {
                const file = event.target.files[0];
                if (!file) return;
                this.isScanning = true;
                this.scanError = '';
                try {
                    const html5QrCode = new Html5Qrcode('temp-reader-sirkulasi');
                    const decodedText = await html5QrCode.scanFile(file, true);
                    this.playBeep();
                    this.processBarcode(decodedText);
                } catch (err) {
                    this.scanError = 'QR Code tidak terdeteksi pada gambar yang diunggah.';
                } finally {
                    this.isScanning = false;
                    event.target.value = '';
                }
            },
            get sisaPiutangBaru() {
                const piutang = Number(this.activeAgen.piutang) || 0;
                const setoran = Number(this.activeAgen.nominal) || 0;
                return Math.max(0, piutang - setoran);
            }
        };
    }
    </script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <div class="space-y-5 pb-10" x-data="kasirSirkulasiApp()">

        <!-- Konten Halaman Kasir (Disembunyikan saat modal cetak aktif) -->
        <div class="space-y-5" :class="{ 'print:hidden no-print': showCetakModal }">

        <!-- Alert Flash Message -->
        @if(session('success'))
            <div class="p-4 bg-kp-blue-50 border border-kp-blue-200 rounded-xl text-xs font-semibold text-kp-blue-800 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-kp-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-kp-blue-500 hover:text-kp-blue-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-slate-100 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-slate-500 hover:text-slate-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- 1. Header Page Setoran Sirkulasi (60-30-10 Palette) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Setoran Sirkulasi Koran Harian
                        </h1>
                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Sirkulasi Koran
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Rekonsiliasi tagihan agen koran, perhitungan sah potongan retur BAPR, dan pencatatan kas setoran harian
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                @if(session('cetak_sirkulasi'))
                    <button type="button" @click="showCetakModal = true" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Bukti Setoran Terakhir</span>
                    </button>
                @endif

                <!-- Pager Navigasi Periode Bulan -->
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs">
                    <a href="{{ route('kasir.penerimaan.sirkulasi', array_merge(request()->query(), ['bulan' => $prevMonth])) }}" 
                       title="Bulan Sebelumnya ({{ Carbon\Carbon::parse($prevMonth . '-01')->translatedFormat('M Y') }})" 
                       class="p-2 text-slate-600 hover:text-slate-900 hover:bg-white rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <span class="px-2.5 py-1 font-bold text-slate-800 flex items-center gap-1.5">
                        <span>Periode: {{ $periodeLabel }}</span>
                        @if(!$isCurrentMonth)
                            <span class="px-1.5 py-0.5 text-[10px] font-bold uppercase rounded bg-slate-200 text-slate-700">Arsip</span>
                        @endif
                    </span>
                    <a href="{{ route('kasir.penerimaan.sirkulasi', array_merge(request()->query(), ['bulan' => $nextMonth])) }}" 
                       title="Bulan Selanjutnya ({{ Carbon\Carbon::parse($nextMonth . '-01')->translatedFormat('M Y') }})" 
                       class="p-2 text-slate-600 hover:text-slate-900 hover:bg-white rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. KPI Finansial & Operasional (60-30-10 Palette) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Plafon Kredit Agen</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalPlafon, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Batas toleransi jatah sirkulasi</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Piutang Beredar</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($totalPiutangBulan, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Tagihan koran periode {{ $periodeLabel }}</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jatah Oplah Sirkulasi</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    {{ number_format($totalJatah, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500 font-sans">Eks</span>
                </p>
                <span class="text-xs text-slate-400 mt-1 block">Kapasitas oplah terlayani kasir & agen</span>
            </div>
        </div>

        <!-- 3. Barcode / QR Code Scanner Bar (Terpisah, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
                <div>
                    <span class="text-sm font-semibold text-slate-900 block">Scan Barcode / QR Code Agen Sirkulasi</span>
                    <span class="text-xs text-slate-500 block">Tembak scanner laser atau ketik kode agen (Enter)</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <div class="relative flex-1 sm:w-72">
                    <input type="text" 
                           x-model="scanInput" 
                           @keydown.enter.prevent="processBarcode(scanInput)" 
                           placeholder="Scan barcode / kode agen..." 
                           class="w-full h-11 px-3.5 pl-9 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>

                <label class="inline-flex items-center gap-2 h-11 px-3.5 bg-slate-50 hover:bg-slate-100 rounded-xl border border-slate-300 text-xs text-slate-700 font-medium cursor-pointer transition select-none">
                    <input type="checkbox" x-model="autoProcess" class="rounded border-slate-300 text-kp-blue-600 focus:ring-kp-blue-600 w-4 h-4">
                    <span>Auto-Proses</span>
                </label>

                <button type="button" 
                        @click="processBarcode(scanInput)" 
                        :disabled="isScanning"
                        class="h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl transition cursor-pointer shadow-xs disabled:opacity-50">
                    <span x-show="!isScanning">Scan</span>
                    <span x-show="isScanning">Mencari...</span>
                </button>

                <button type="button" 
                        @click="openScanModal()" 
                        class="h-11 px-4 bg-white hover:bg-slate-50 active:bg-slate-100 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl border border-slate-300 transition flex items-center gap-1.5 cursor-pointer shadow-2xs"
                        title="Buka Kamera Scanner (F2)">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Kamera (F2)</span>
                </button>
            </div>
        </div>

        <!-- 4. Filter Toolbar Mandiri (60-30-10 Palette) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4">
            <form method="GET" action="{{ route('kasir.penerimaan.sirkulasi') }}" class="space-y-4">
                <!-- Baris 1: Filter Status Tagihan -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3.5 border-b border-slate-100">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider mr-1">
                            Status Tagihan:
                        </span>
                        <div class="inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200 text-xs">
                            <label class="cursor-pointer px-3.5 py-1.5 rounded-lg font-medium transition-all {{ ($statusPiutang ?? 'belum_lunas') === 'belum_lunas' ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="status_piutang" value="belum_lunas" {{ ($statusPiutang ?? 'belum_lunas') === 'belum_lunas' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                                <span>Belum Lunas (Piutang)</span>
                            </label>
                            <label class="cursor-pointer px-3.5 py-1.5 rounded-lg font-medium transition-all {{ ($statusPiutang ?? '') === 'lunas' ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="status_piutang" value="lunas" {{ ($statusPiutang ?? '') === 'lunas' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                                <span>Sudah Lunas</span>
                            </label>
                            <label class="cursor-pointer px-3.5 py-1.5 rounded-lg font-medium transition-all {{ ($statusPiutang ?? '') === 'semua' ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="status_piutang" value="semua" {{ ($statusPiutang ?? '') === 'semua' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                                <span>Semua Status</span>
                            </label>
                        </div>
                    </div>

                    <!-- Indikator Filter Aktif & Reset -->
                    @if(($statusPiutang ?? 'belum_lunas') !== 'belum_lunas' || ($bulanPilihan ?? Carbon\Carbon::now()->format('Y-m')) !== Carbon\Carbon::now()->format('Y-m') || request('search') || request('kode_agen'))
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-medium flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-kp-blue-600 animate-pulse"></span>
                                Filter Aktif ({{ $periodeLabel }})
                            </span>
                            <a href="{{ route('kasir.penerimaan.sirkulasi') }}" class="text-xs text-slate-500 hover:text-slate-800 font-semibold flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reset Filter
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Baris 2: Kontrol Filter Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pilih Bulan</label>
                        <input type="month" name="bulan" value="{{ $bulanPilihan }}" onchange="this.form.submit()" 
                               class="w-full h-11 px-3.5 text-sm rounded-xl border border-slate-300 bg-white focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 text-slate-900 shadow-2xs">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kode Agen</label>
                        <input type="text" name="kode_agen" value="{{ request('kode_agen') }}" placeholder="Contoh: 001, 005..." 
                               class="w-full h-11 px-3.5 text-sm font-mono rounded-xl border border-slate-300 bg-white focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 text-slate-900 placeholder-slate-400 shadow-2xs">
                    </div>

                    <div class="sm:col-span-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Cari Nama / Wilayah</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama agen atau wilayah..." 
                                   class="w-full h-11 pl-9 pr-3 text-sm rounded-xl border border-slate-300 bg-white focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 text-slate-900 placeholder-slate-400 shadow-2xs">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <button type="submit" class="w-full h-11 px-5 inline-flex items-center justify-center gap-1.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-semibold text-sm rounded-xl transition shadow-xs cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            <span>Filter</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- 5. Pure Data Table: Tagihan Agen & Form Setoran (Tanpa Banner Tempel di Atas Tabel) -->
        <div id="tabel-agen-sirkulasi" class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden scroll-mt-6">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-emerald-700 border-b border-emerald-800 text-white font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3">Kode & Nama Agen</th>
                            <th class="py-3 px-3">Kategori & Rute</th>
                            <th class="py-3 px-3">Wilayah</th>
                            <th class="py-3 px-3 text-right">Jatah Eks</th>
                            <th class="py-3 px-3 text-right">Plafon Piutang</th>
                            <th class="py-3 px-3 text-right">Saldo Piutang Koran</th>
                            <th class="py-3 px-3 text-center">Aksi Setor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                        @forelse($agenList as $agen)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3">
                                <div class="font-bold text-slate-900">{{ $agen->nama_agen }}</div>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="font-mono text-xs text-kp-blue-700 font-semibold">{{ $agen->kode_agen }}</span>
                                    @if($agen->no_faktur_bulanan)
                                        <span class="inline-flex items-center gap-1 font-mono text-[10px] text-kp-blue-700 font-bold bg-kp-blue-50 px-1.5 py-0.5 rounded border border-kp-blue-200" title="No. Faktur Bulanan Terkonsolidasi">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            {{ $agen->no_faktur_bulanan }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $agen->kategori === '11' ? 'Agen Besar' : 'Sub-Agen' }}
                                </span>
                                <div class="text-xs text-slate-400 mt-0.5 font-mono">{{ $agen->rute_kode ?? 'R-01' }}</div>
                            </td>
                            <td class="py-3 px-3 text-slate-600">{{ $agen->wilayah ?? '-' }}</td>
                            <td class="py-3 px-3 text-right font-mono text-slate-700">{{ number_format($agen->jatah_default) }} eks</td>
                            <td class="py-3 px-3 text-right font-mono text-slate-600">Rp {{ number_format($agen->plafon_piutang, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono font-semibold {{ $agen->saldo_piutang_bulan > 0 ? 'text-slate-900' : 'text-slate-400' }}">
                                Rp {{ number_format($agen->saldo_piutang_bulan, 0, ',', '.') }}
                                @if($agen->total_setoran_bulan > 0)
                                    <div class="text-xs font-normal text-kp-blue-700 mt-0.5">
                                        Setoran: Rp {{ number_format($agen->total_setoran_bulan, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if($agen->saldo_piutang_bulan > 0)
                                    <button type="button" 
                                            @click="activeAgen = { 
                                                id: {{ $agen->id }},
                                                kode: '{{ $agen->kode_agen }}',
                                                nama: '{{ addslashes($agen->nama_agen) }}', 
                                                wilayah: '{{ $agen->wilayah ?? '-' }}', 
                                                piutang: {{ $agen->saldo_piutang_bulan }}, 
                                                bulan: '{{ $periodeLabel }}',
                                                nominal: {{ $agen->saldo_piutang_bulan }},
                                                metode: 'tunai',
                                                bankTujuan: 'Bank BJB Sirkulasi',
                                                noRef: '',
                                                penagih: '{{ Auth::user()->name }}',
                                                keterangan: 'Setoran koran {{ addslashes($agen->nama_agen) }} Periode {{ $periodeLabel }}' + ('{{ $agen->no_faktur_bulanan }}' ? ' [Faktur: {{ $agen->no_faktur_bulanan }}]' : ''),
                                                no_faktur: '{{ $agen->no_faktur_bulanan ?? '' }}'
                                            }; showSetorModal = true"
                                            class="inline-flex items-center gap-1.5 h-8 px-3 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-semibold text-xs rounded-lg transition shadow-2xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <span>Input Setoran</span>
                                    </button>
                                @else
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        Lunas
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                                Tidak ada data agen yang memenuhi kriteria filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form Setoran Sirkulasi -->
        <div x-cloak x-show="showSetorModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showSetorModal = false" class="bg-white rounded-2xl max-w-xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200 shrink-0 bg-slate-50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-kp-blue-600 text-white flex items-center justify-center shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Form Setoran Kas Sirkulasi</h4>
                            <p class="text-xs text-slate-500">Pencatatan kas masuk setoran agen koran</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="showSetorModal = false; openScanModal()" class="px-2.5 py-1 text-xs bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-lg border border-slate-300 transition flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <span>Scan QR</span>
                        </button>
                        <button type="button" @click="showSetorModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
                    </div>
                </div>

                <form id="form-setor-sirkulasi" method="POST" action="{{ route('kasir.penerimaan.sirkulasi.store') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="agen_id" :value="activeAgen.id">
                    <input type="hidden" name="no_faktur" :value="activeAgen.no_faktur || ''">
                    <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                    <input type="hidden" name="status_piutang" value="{{ $statusPiutang }}">

                    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3.5 text-xs">
                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 text-xs space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Nama Agen:</span>
                                <span class="font-bold text-slate-900" x-text="activeAgen.nama + ' (' + activeAgen.kode + ')'"></span>
                            </div>
                            <template x-if="activeAgen.no_faktur">
                                <div class="flex justify-between items-center bg-kp-blue-50/80 px-2 py-1 rounded-md border border-kp-blue-200">
                                    <span class="text-kp-blue-700 font-semibold">No. Faktur Bulanan:</span>
                                    <span class="font-bold font-mono text-kp-blue-700" x-text="activeAgen.no_faktur"></span>
                                </div>
                            </template>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Wilayah:</span>
                                <span class="font-medium text-slate-800" x-text="activeAgen.wilayah"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Saldo Piutang Koran Saat Ini:</span>
                                <span class="font-bold font-mono text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeAgen.piutang)"></span></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Bulan Periode:</span>
                                <span class="font-semibold text-slate-800" x-text="activeAgen.bulan"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Divisi & Dokumen</label>
                                <div class="h-9 px-3 bg-white rounded-lg border border-slate-200 font-semibold text-slate-800 flex items-center justify-between text-xs">
                                    <span>Divisi Sirkulasi (KI-02)</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded font-mono font-bold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200"
                                          x-text="(activeAgen.metode === 'transfer' || activeAgen.metode === 'qris') ? 'BBM' : 'BKM'"></span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Perkiraan (COA)</label>
                                <input type="hidden" name="kode_perkiraan" value="1.00.04.01.01.00">
                                <input type="text" value="1.00.04.01.01.00 (Piutang Sirkulasi)" readonly
                                       class="w-full h-9 px-2.5 rounded-lg border border-slate-200 font-mono text-xs text-slate-600 bg-slate-100 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">No. Blanko (Opsional)</label>
                                <input type="text" name="no_blanko" placeholder="BLK-02881" 
                                       class="w-full h-9 px-3 rounded-lg border border-slate-300 font-mono text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 bg-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Petugas Inkaso Sirkulasi</label>
                                <select name="penagih_inkaso" x-model="activeAgen.penagih" class="w-full h-10 px-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-xs text-slate-800 bg-white">
                                    @foreach($petugasInkasoList as $petugas)
                                        <option value="{{ $petugas->name }}">{{ $petugas->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Setoran</label>
                                <select name="metode_pembayaran" x-model="activeAgen.metode" class="w-full h-10 px-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-xs text-slate-800 bg-white">
                                    <option value="tunai">Tunai Meja Kasir (BKM)</option>
                                    <option value="transfer">Transfer Bank (BBM + KO-09)</option>
                                    <option value="qris">QRIS Resmi (BBM + KO-09)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Otorisasi Manajer</label>
                                <input type="text" name="manager_otorisasi" value="Manajer Keuangan" required
                                       class="w-full h-10 px-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-xs text-slate-800 bg-white font-medium">
                            </div>
                        </div>

                        <!-- Input Khusus Transfer -->
                        <div x-show="activeAgen.metode === 'transfer' || activeAgen.metode === 'qris'" class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Bank Tujuan Rekening</label>
                                    <select name="bank_tujuan" x-model="activeAgen.bankTujuan" class="w-full h-9 px-2.5 rounded-lg border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-xs text-slate-800 bg-white">
                                        <option value="Bank BJB Sirkulasi">Bank BJB Sirkulasi (Giro: 0012-0034-5678)</option>
                                        <option value="Bank BCA Tasikmalaya">Bank BCA (Giro: 054-8899-771)</option>
                                        <option value="Bank Mandiri Sirkulasi">Bank Mandiri (Giro: 131-00-9876543-2)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">No. Bukti Transfer</label>
                                    <input type="text" name="no_referensi_transfer" x-model="activeAgen.noRef" placeholder="TRF-BJB-984128" 
                                           class="w-full h-9 px-2.5 rounded-lg border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-xs text-slate-800 font-mono bg-white">
                                </div>
                            </div>
                            <div class="text-xs text-slate-600 bg-white p-2.5 rounded-lg border border-slate-200 flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded bg-slate-200 text-slate-800 font-mono font-bold text-[10px] shrink-0">DOUBLE-ENTRY KO-09</span>
                                <span>Penerimaan KI-02 (BBM) otomatis mencatat Pengeluaran Penyeimbang Setor Bank (KO-09) agar kas fisik tetap seimbang.</span>
                            </div>
                        </div>

                        <!-- Nominal Uang Disetor -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-slate-700">Nominal Uang Kas Masuk / Ditransfer (Rp)</label>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md"
                                      :class="sisaPiutangBaru > 0 ? 'bg-slate-100 text-slate-700 border border-slate-200' : 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200'"
                                      x-text="sisaPiutangBaru > 0 ? 'CICILAN SEBAGIAN' : 'PELUNASAN PENUH'"></span>
                            </div>
                            <input type="number" name="nominal_setoran" x-model="activeAgen.nominal" required min="1" 
                                   class="w-full h-11 px-3.5 text-base font-mono font-bold rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-slate-900 bg-white">
                            <span class="text-xs text-slate-500 mt-1 block">Otomatis menampilkan Sisa Piutang terbaru. Ubah nominal jika agen mencicil sebagian.</span>
                        </div>

                        <!-- Kuitansi Sementara jika Cicilan Sebagian -->
                        <div x-show="sisaPiutangBaru > 0" x-transition class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700">Nomor Kuitansi Sementara (Cicilan)</label>
                            <input type="text" name="no_kuitansi_sementara" placeholder="Kosongkan untuk auto-generate (Contoh: KS-SRK-202611-0001)"
                                   class="w-full h-9 px-3 rounded-lg border border-slate-300 font-mono text-xs text-slate-900 bg-white focus:outline-none focus:border-kp-blue-600">
                            <p class="text-[11px] text-slate-500">SOP Penerimaan: Pembayaran cicilan menggunakan referensi Kuitansi Sementara.</p>
                        </div>

                        <!-- Live Calculation Preview -->
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs space-y-1">
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Jumlah Setoran Kas Masuk:</span>
                                <span class="font-bold font-mono text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeAgen.nominal || 0)"></span></span>
                            </div>
                            <div class="flex justify-between items-center text-slate-600 border-t border-slate-200 pt-1">
                                <span>Sisa Saldo Piutang Setelah Setoran:</span>
                                <span class="font-bold font-mono text-sm text-slate-900">
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(sisaPiutangBaru)"></span>
                                    <span class="text-xs ml-1 font-semibold text-kp-blue-700" x-text="sisaPiutangBaru === 0 ? '(Lunas Bersih)' : '(Sisa Piutang)'"></span>
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                            <input type="text" name="keterangan" x-model="activeAgen.keterangan" placeholder="Keterangan setoran agen..." 
                                   class="w-full h-10 px-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-xs text-slate-900">
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="px-5 py-3.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showSetorModal = false" class="h-10 px-4 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl transition shadow-2xs">
                            Simpan & Terbitkan BKM
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <!-- Modal Cetak Tanda Terima Setoran Koran - Format Continuous Form Dot Matrix -->
        @if(session('cetak_sirkulasi'))
        @php $srk = session('cetak_sirkulasi'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="max-w-4xl w-full max-h-[92vh] flex flex-col">
                <div class="mb-3 flex items-center justify-between print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs font-sans cursor-pointer">
                        ← Kembali ke Daftar Setoran Sirkulasi
                    </button>
                    <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition cursor-pointer font-sans">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Lembar Faktur / Kuitansi Sirkulasi (Print / PDF)</span>
                    </button>
                </div>

                <div class="print-document-sheet dot-matrix-container bg-white rounded-lg shadow-2xl overflow-y-auto border border-slate-300 flex font-dot-matrix text-black">
                    <div class="w-8 bg-slate-50 border-r border-dashed border-slate-300 py-6 flex flex-col items-center justify-between gap-4 select-none shrink-0 tractor-feed print:hidden">
                        @for ($i = 0; $i < 14; $i++)
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-200 border border-slate-300"></div>
                        @endfor
                    </div>

                    <div class="print-document-body flex-1 p-6 sm:p-8 text-[12px] leading-relaxed text-black">
                        <div class="text-left mb-3">
                            <h1 class="text-base sm:text-lg font-black tracking-wider uppercase font-dot-matrix text-black">
                                FAKTUR & KUITANSI SIRKULASI KORAN HU. PRIANGAN
                            </h1>
                            <p class="text-[11px] font-bold text-neutral-700 tracking-tight leading-tight">
                                JL. DINDINGARI RAYA NO.12 TLP. (0265)-335300, FAX. 335677 PERUM BUMI RESIK PANGLAYUNGAN TASIKMALAYA 46134
                            </p>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-3"></div>

                        <div class="grid grid-cols-12 gap-x-4 gap-y-1 mb-3">
                            <div class="col-span-7 space-y-0.5">
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Identitas</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold">1.00.04.01.01.01.{{ $srk['kode_agen'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Nama</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold uppercase">{{ $srk['nama_agen'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Wilayah</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold">{{ $srk['wilayah'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Kota</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold">TASIKMALAYA</span>
                                </div>
                            </div>

                            <div class="col-span-5 space-y-0.5">
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">No. Kuitansi</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold font-mono text-blue-900">{{ $srk['no_bkm'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">Petugas</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold uppercase">{{ $srk['penagih'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">Metode Bayar</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold">SETORAN ({{ $srk['metode'] }})</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">Tanggal</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold">{{ $srk['tanggal'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-3"></div>

                        <div class="grid grid-cols-12 gap-x-3 gap-y-1 mb-3">
                            <div class="col-span-4 flex">
                                <span class="w-20 shrink-0 font-bold whitespace-nowrap">Kategori</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase">SIRKULASI KORAN</span>
                            </div>
                            <div class="col-span-3 flex">
                                <span class="w-12 shrink-0 font-bold whitespace-nowrap">Tipe</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase">{{ ($srk['jenis_dokumen'] ?? 'BKM') }} (KI-11)</span>
                            </div>
                            <div class="col-span-5 flex">
                                <span class="w-24 shrink-0 font-bold whitespace-nowrap">Keterangan</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase text-[11px] leading-tight">
                                    SETORAN KORAN AGEN {{ $srk['nama_agen'] }}
                                    @if(!empty($srk['no_bapr']) && $srk['no_bapr'] !== '-') (RETUR BAPR: {{ $srk['no_bapr'] }}) @endif
                                </span>
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-3"></div>

                        <div class="space-y-1 max-w-lg mb-4 font-bold">
                            <div class="flex justify-between">
                                <span>Brutto Setoran</span>
                                <span>Rp. {{ number_format(($srk['nominal'] ?? 0) + ($srk['nilai_retur'] ?? 0), 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Retur Koran ({{ $srk['retur_eks'] ?? 0 }} Eks)</span>
                                <span>Rp. {{ number_format($srk['nilai_retur'] ?? 0, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Netto Setoran</span>
                                <span>Rp. {{ number_format($srk['nominal'] ?? 0, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>PPn 11 %</span>
                                <span>Rp. 0,00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Materai</span>
                                <span>Rp. 0,00</span>
                            </div>
                            <div class="border-t border-dashed border-black pt-1 flex justify-between text-sm font-black">
                                <span>Jumlah Total Disetor</span>
                                <span>Rp. {{ number_format($srk['nominal'] ?? 0, 2, ',', '.') }}</span>
                            </div>
                            <div class="pt-1 text-[11px] italic font-bold">
                                <span class="not-italic">Terbilang :</span> # {{ $srk['terbilang'] }} #
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-6"></div>

                        <div class="grid grid-cols-2 gap-4 items-end pt-2">
                            <div class="pl-4">
                                <div class="inline-block border-2 border-emerald-600 text-emerald-700 px-5 py-2 rounded-full font-black text-center rotate-[-4deg] opacity-90">
                                    <div class="text-[9px] uppercase tracking-widest">SETORAN SIRKULASI</div>
                                    <div class="text-sm tracking-wider border-t border-b border-emerald-600 my-0.5 py-0.5">L U N A S</div>
                                    <div class="text-[8px] font-mono">DIVISI KEUANGAN</div>
                                </div>
                            </div>

                            <div class="text-right flex flex-col gap-5">
                                <div>
                                    <p class="text-[11px] font-bold">Tasikmalaya, {{ date('d-m-Y') }}</p>
                                    <p class="text-[11px] font-semibold mt-0.5">Bagian Sirkulasi / Kasir,</p>
                                    <div class="h-10"></div>
                                    <p class="font-bold underline text-[11px] uppercase">{{ $srk['kasir'] }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold">Agen / Pelanggan Sirkulasi,</p>
                                    <div class="h-10"></div>
                                    <p class="font-bold underline text-[11px] uppercase">{{ $srk['nama_agen'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-8 bg-slate-50 border-l border-dashed border-slate-300 py-6 flex flex-col items-center justify-between gap-4 select-none shrink-0 tractor-feed print:hidden">
                        @for ($i = 0; $i < 14; $i++)
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-200 border border-slate-300"></div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Modal Aksi Scan QR Code Agen Sirkulasi -->
        <div x-cloak x-show="showScanModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="closeScanModal()" class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
                <div class="flex items-center justify-between px-5 py-3.5 bg-slate-900 text-white">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-kp-blue-600 text-white flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-white">Scan QR Code Agen Koran</h4>
                            <p class="text-xs text-slate-300">Arahkan scanner kamera ke QR Code kartu agen / struk loper</p>
                        </div>
                    </div>
                    <button type="button" @click="closeScanModal()" class="text-slate-400 hover:text-white font-bold text-lg leading-none cursor-pointer">&times;</button>
                </div>

                <div class="p-5 space-y-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Kode QR / Kode Agen / Nama Agen</label>
                        <div class="relative">
                            <input type="text" 
                                   x-ref="barcodeInput"
                                   x-model="scanInput" 
                                   @keydown.enter.prevent="processBarcode(scanInput)" 
                                   placeholder="Arahkan scanner QR Code atau ketik kode agen..." 
                                   class="w-full h-11 text-base font-mono font-bold pl-10 pr-24 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-slate-900 bg-white">
                            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <button type="button" 
                                     @click="processBarcode(scanInput)" 
                                     :disabled="isScanning"
                                     class="absolute right-1.5 top-1.5 h-8 px-3 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-lg transition disabled:opacity-50 cursor-pointer">
                                <span x-show="!isScanning">Proses</span>
                                <span x-show="isScanning">Mencari...</span>
                            </button>
                        </div>
                    </div>

                    <div x-show="scanError" x-transition class="p-3 bg-slate-100 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 flex items-center justify-between">
                        <span x-text="scanError"></span>
                        <button type="button" @click="scanError = ''" class="text-slate-500 font-bold cursor-pointer">&times;</button>
                    </div>

                    <div class="border border-slate-200 rounded-xl p-3.5 bg-slate-50 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Kamera (Live Scan)</span>
                            </span>
                            <button type="button" 
                                    @click="cameraActive ? stopCamera() : startCamera()" 
                                    class="h-8 px-3 text-xs font-semibold rounded-lg border transition cursor-pointer flex items-center gap-1.5"
                                    :class="cameraActive ? 'bg-white text-slate-700 border-slate-300' : 'bg-kp-blue-600 text-white border-kp-blue-600 hover:bg-kp-blue-700'">
                                <span x-text="cameraActive ? 'Matikan Kamera' : 'Nyalakan Kamera'"></span>
                            </button>
                        </div>

                        <div x-show="cameraActive" class="rounded-xl overflow-hidden bg-slate-900 p-2 shadow-inner">
                            <div id="reader-camera-sirkulasi" class="w-full overflow-hidden rounded-lg aspect-square max-w-[340px] mx-auto"></div>
                        </div>

                        <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
                            <span class="text-slate-500">Unggah berkas screenshot QR?</span>
                            <label class="h-8 px-3 font-semibold rounded-lg bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 cursor-pointer transition flex items-center gap-1">
                                <span>Pilih Gambar QR</span>
                                <input type="file" accept="image/*" @change="scanUploadedImage($event)" class="hidden">
                            </label>
                        </div>
                        <div id="temp-reader-sirkulasi" style="display:none;"></div>
                    </div>
                </div>

                <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-end text-xs">
                    <button type="button" @click="closeScanModal()" class="h-9 px-4 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold rounded-xl transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- Overlay Loading Saat Auto-Submit -->
        <div x-cloak x-show="isAutoSubmitting" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl border border-slate-200 flex flex-col items-center text-center space-y-4">
                <div class="w-12 h-12 rounded-xl bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-600 flex items-center justify-center shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">QR Code Berhasil Dipindai!</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Memproses setoran agen koran otomatis ke Kas Masuk...</p>
                </div>
                <div class="w-full bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs space-y-1 font-mono text-left">
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-sans">Kode Agen:</span>
                        <span class="font-bold text-kp-blue-700" x-text="autoSubmitInfo.kode"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-sans">Nama Agen:</span>
                        <span class="font-bold text-slate-800 truncate max-w-[180px]" x-text="autoSubmitInfo.agen"></span>
                    </div>
                    <div class="flex justify-between border-t border-slate-200 pt-1">
                        <span class="text-slate-500 font-sans">Nominal:</span>
                        <span class="font-bold text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(autoSubmitInfo.nominal)"></span></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>