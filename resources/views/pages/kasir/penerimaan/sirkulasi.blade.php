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
            showCetakModal: {{ session('cetak_sirkulasi') ? 'true' : 'false' }},
            init() {
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
                keterangan: ''
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
                        const nominal = piutang > 0 ? piutang : 0;

                        if (nominal <= 0) {
                            this.scanError = `Agen ${d.nama_agen} (${d.kode_agen}) tidak memiliki tunggakan/piutang koran (Saldo: Rp 0).`;
                            this.isScanning = false;
                            return;
                        }

                        this.activeAgen = {
                            id: d.id,
                            kode: d.kode_agen,
                            nama: d.nama_agen,
                            wilayah: d.wilayah || '-',
                            piutang: piutang,
                            bulan: '{{ $periodeLabel }}',
                            nominal: nominal,
                            metode: 'tunai',
                            bankTujuan: 'Bank BJB Sirkulasi',
                            noRef: '',
                            penagih: '{{ Auth::user()->name }}',
                            keterangan: `Setoran koran ${d.nama_agen} Periode {{ $periodeLabel }}`
                        };
                        this.closeScanModal();

                        if (this.autoProcess) {
                            this.isAutoSubmitting = true;
                            this.autoSubmitInfo = {
                                agen: d.nama_agen,
                                kode: d.kode_agen,
                                nominal: nominal
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
                                    if (nominalInput) nominalInput.value = nominal;
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
                        this.scanError = json.message || 'Data agen tidak ditemukan!';
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
                            this.scanError = 'Pustaka pemindai barcode belum selesai dimuat. Silakan refresh halaman browser.';
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
                                return {
                                    width: Math.floor(viewfinderWidth * 0.88),
                                    height: Math.floor(viewfinderHeight * 0.65)
                                };
                            },
                            formatsToSupport: [
                                Html5QrcodeSupportedFormats.CODE_128,
                                Html5QrcodeSupportedFormats.CODE_39,
                                Html5QrcodeSupportedFormats.EAN_13,
                                Html5QrcodeSupportedFormats.QR_CODE,
                                Html5QrcodeSupportedFormats.UPC_A
                            ]
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
            async stopCamera() {
                if (this.html5QrScanner) {
                    try {
                        if (this.html5QrScanner.isScanning) {
                            await this.html5QrScanner.stop();
                        }
                        this.html5QrScanner.clear();
                    } catch (e) {}
                    this.html5QrScanner = null;
                }
                this.cameraActive = false;
            },
            async scanUploadedImage(event) {
                const file = event.target.files ? event.target.files[0] : null;
                if (!file) return;
                this.isScanning = true;
                this.scanError = '';
                try {
                    if (typeof Html5Qrcode === 'undefined') {
                        this.scanError = 'Pustaka scanner belum dimuat.';
                        return;
                    }
                    const scanner = new Html5Qrcode('temp-reader-sirkulasi');
                    const decodedText = await scanner.scanFile(file, true);
                    scanner.clear();
                    this.playBeep();
                    this.processBarcode(decodedText);
                } catch (err) {
                    this.scanError = 'Barcode tidak dapat terbaca dari gambar ini. Pastikan gambar tidak buram dan barcode terlihat jelas.';
                } finally {
                    this.isScanning = false;
                    if (event.target) event.target.value = '';
                }
            },
            get sisaPiutangBaru() {
                let sisa = this.activeAgen.piutang - Number(this.activeAgen.nominal);
                return sisa < 0 ? 0 : sisa;
            }
        };
    }
    </script>

    <div class="space-y-5" 
         @keydown.window.f2.prevent="openScanModal()"
         x-data="kasirSirkulasiApp()">

        <!-- Konten Halaman Kasir (Disembunyikan saat modal cetak aktif) -->
        <div class="space-y-5" :class="{ 'print-hide-when-modal-open print:hidden': showCetakModal }">

            <!-- Header Halaman (Palet 60-30-10) -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 no-print print:hidden">
                <div class="flex items-center gap-3.5 min-w-0">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                                Setoran Sirkulasi Koran Harian
                            </h1>
                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">
                                KI-02
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                            Penerimaan setoran kas piutang agen koran dan rekonsiliasi potongan retur BAPR.
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <!-- Pager Navigasi Periode Bulan -->
                    <div class="flex items-center gap-1 h-10 bg-slate-100 px-1.5 rounded-xl border border-slate-200 text-xs">
                        <a href="{{ route('kasir.penerimaan.sirkulasi', array_merge(request()->query(), ['bulan' => $prevMonth])) }}" 
                           title="Bulan Sebelumnya" 
                           class="p-1.5 text-slate-600 hover:text-slate-900 hover:bg-white rounded-lg transition">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                        <span class="px-2 font-semibold text-slate-800 flex items-center gap-1.5 whitespace-nowrap">
                            <span>{{ $periodeLabel }}</span>
                            @if(!$isCurrentMonth)
                                <span class="px-1.5 py-0.5 text-[10px] font-semibold uppercase rounded bg-white text-slate-600 border border-slate-300">Arsip</span>
                            @endif
                        </span>
                        <a href="{{ route('kasir.penerimaan.sirkulasi', array_merge(request()->query(), ['bulan' => $nextMonth])) }}" 
                           title="Bulan Selanjutnya" 
                           class="p-1.5 text-slate-600 hover:text-slate-900 hover:bg-white rounded-lg transition">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    @if(session('cetak_sirkulasi'))
                        <button type="button" 
                                @click="showCetakModal = true" 
                                class="inline-flex items-center gap-2 h-10 px-4 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer whitespace-nowrap">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Cetak Ulang BKM</span>
                        </button>
                    @endif

                    <button type="button" 
                            @click="openScanModal()" 
                            class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        <span>Scan Barcode (F2)</span>
                    </button>
                </div>
            </div>

            <!-- Alert Flash Message -->
            @if(session('success'))
                <div class="p-3.5 bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                    <span>{{ session('success') }}</span>
                    <button type="button" @click="$el.parentElement.remove()" class="text-kp-blue-700 hover:text-kp-blue-900 font-bold ml-3 cursor-pointer">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-3.5 bg-slate-100 border border-slate-300 text-slate-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs">
                    <span>{{ session('error') }}</span>
                    <button type="button" @click="$el.parentElement.remove()" class="text-slate-600 hover:text-slate-900 font-bold ml-3 cursor-pointer">&times;</button>
                </div>
            @endif

            <!-- 3 KPI Ringkasan Tagihan Agen -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Saldo Piutang Beredar</span>
                    <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($totalPiutangBulan, 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">
                        Tagihan koran periode {{ $periodeLabel }}
                    </span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Plafon Kredit Agen</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($totalPlafon, 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">
                        Batas toleransi plafon sirkulasi
                    </span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jatah Oplah Sirkulasi</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                        {{ number_format($totalJatah, 0, ',', '.') }} <span class="text-xs font-sans font-normal text-slate-500">Eksemplar</span>
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">
                        Kapasitas oplah harian agen
                    </span>
                </div>
            </div>

            <!-- Toolbar Barcode Cepat & Filter Pencarian -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs space-y-4">
                <!-- Baris Input Cepat Barcode Scanner -->
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 pb-3.5 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-block px-2.5 py-1 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Scanner Siap
                        </span>
                        <span class="text-xs sm:text-sm text-slate-600">
                            Tembak barcode kartu agen atau ketik kode agen untuk setoran cepat.
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700 bg-slate-100 px-3 h-10 rounded-xl border border-slate-200 select-none">
                            <input type="checkbox" x-model="autoProcess" class="rounded border-slate-300 text-kp-blue-600 focus:ring-kp-blue-600 w-4 h-4">
                            <span>Auto-Proses</span>
                        </label>
                        <div class="flex-1 sm:w-64">
                            <input type="text" 
                                   x-model="scanInput" 
                                   @keydown.enter.prevent="processBarcode(scanInput)" 
                                   placeholder="Tembak barcode / kode agen..." 
                                   class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>
                        <button type="button" 
                                @click="processBarcode(scanInput)" 
                                :disabled="isScanning"
                                class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-sm font-bold rounded-xl shadow-xs transition shrink-0 cursor-pointer disabled:opacity-50">
                            <span x-show="!isScanning">Scan</span>
                            <span x-show="isScanning">Mencari...</span>
                        </button>
                    </div>
                </div>

                <!-- Form Filter Pencarian -->
                <form method="GET" action="{{ route('kasir.penerimaan.sirkulasi') }}" class="space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mr-1">Status Tagihan:</span>
                            <div class="inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200 text-xs">
                                <label class="cursor-pointer px-3 py-1.5 rounded-lg transition {{ ($statusPiutang ?? 'belum_lunas') === 'belum_lunas' ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-700 font-medium hover:text-slate-900' }}">
                                    <input type="radio" name="status_piutang" value="belum_lunas" {{ ($statusPiutang ?? 'belum_lunas') === 'belum_lunas' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                                    <span>Belum Lunas</span>
                                </label>
                                <label class="cursor-pointer px-3 py-1.5 rounded-lg transition {{ ($statusPiutang ?? '') === 'lunas' ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-700 font-medium hover:text-slate-900' }}">
                                    <input type="radio" name="status_piutang" value="lunas" {{ ($statusPiutang ?? '') === 'lunas' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                                    <span>Lunas</span>
                                </label>
                                <label class="cursor-pointer px-3 py-1.5 rounded-lg transition {{ ($statusPiutang ?? '') === 'semua' ? 'bg-kp-blue-600 text-white font-bold shadow-xs' : 'text-slate-700 font-medium hover:text-slate-900' }}">
                                    <input type="radio" name="status_piutang" value="semua" {{ ($statusPiutang ?? '') === 'semua' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                                    <span>Semua</span>
                                </label>
                            </div>
                        </div>

                        @if(($statusPiutang ?? 'belum_lunas') !== 'belum_lunas' || ($bulanPilihan ?? Carbon\Carbon::now()->format('Y-m')) !== Carbon\Carbon::now()->format('Y-m') || request('search') || request('kode_agen'))
                            <a href="{{ route('kasir.penerimaan.sirkulasi') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
                                Reset Filter
                            </a>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                        <div class="sm:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Periode Bulan</label>
                            <input type="month" name="bulan" value="{{ $bulanPilihan }}" onchange="this.form.submit()" 
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kode Agen</label>
                            <input type="text" name="kode_agen" value="{{ request('kode_agen') }}" placeholder="Contoh: 001, 005..." 
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>

                        <div class="sm:col-span-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Agen / Wilayah</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama agen atau wilayah..." 
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>

                        <div class="sm:col-span-2">
                            <button type="submit" class="w-full h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                                Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabel Tagihan Agen & Form Setoran -->
            <div id="tabel-agen-sirkulasi" class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden scroll-mt-6">
                <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Daftar Agen Sirkulasi ({{ $periodeLabel }})</h3>
                        <p class="text-xs text-slate-500">Status piutang koran dan input setoran kas masuk</p>
                    </div>
                    <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                        {{ $agenList->count() }} Agen
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-3.5">Kode Agen</th>
                                <th class="py-3 px-3.5">Nama Agen</th>
                                <th class="py-3 px-3.5">Kategori &amp; Rute</th>
                                <th class="py-3 px-3.5">Wilayah</th>
                                <th class="py-3 px-3.5 text-right">Jatah</th>
                                <th class="py-3 px-3.5 text-right">Plafon</th>
                                <th class="py-3 px-3.5 text-right">Saldo Piutang</th>
                                <th class="py-3 px-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                            @forelse($agenList as $agen)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    {{ $agen->kode_agen }}
                                </td>
                                <td class="py-3.5 px-3.5 font-semibold text-slate-900">
                                    {{ $agen->nama_agen }}
                                </td>
                                <td class="py-3.5 px-3.5">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $agen->kategori === '11' ? 'Agen Besar' : 'Sub-Agen' }}
                                    </span>
                                    <span class="text-xs font-mono text-slate-500 ml-1">{{ $agen->rute_kode ?? 'R-01' }}</span>
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600">
                                    {{ $agen->wilayah ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums text-slate-700">
                                    {{ number_format($agen->jatah_default) }} eks
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums text-slate-600">
                                    Rp {{ number_format($agen->plafon_piutang, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">
                                    @if($agen->saldo_piutang_bulan > 0)
                                        <div class="font-bold text-kp-blue-700">
                                            Rp {{ number_format($agen->saldo_piutang_bulan, 0, ',', '.') }}
                                        </div>
                                    @else
                                        <div class="text-slate-400">Rp 0</div>
                                    @endif
                                    @if($agen->total_setoran_bulan > 0)
                                        <div class="text-xs font-normal text-slate-500">
                                            Setor: Rp {{ number_format($agen->total_setoran_bulan, 0, ',', '.') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
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
                                                    keterangan: 'Setoran koran {{ addslashes($agen->nama_agen) }} Periode {{ $periodeLabel }}'
                                                }; showSetorModal = true"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                                            <span>Setor BKM</span>
                                        </button>
                                    @else
                                        <div class="inline-flex flex-col items-center">
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                                Lunas
                                            </span>
                                            @if($agen->transaksi_terakhir)
                                                <span class="text-[11px] font-mono text-slate-400 mt-0.5">
                                                    {{ $agen->transaksi_terakhir->no_kuitansi }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="py-10 text-center text-slate-500 text-sm">
                                    Tidak ada data agen sirkulasi yang memenuhi kriteria filter.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Form Setoran Sirkulasi -->
            <div x-cloak x-show="showSetorModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
                <div @click.away="showSetorModal = false" class="bg-white rounded-2xl max-w-xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-3.5 bg-white border-b border-slate-200 shrink-0">
                        <div>
                            <h4 class="text-base font-bold text-slate-900">Input Setoran Kas Sirkulasi</h4>
                            <p class="text-xs text-slate-500">Pencatatan kas masuk setoran agen koran</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="showSetorModal = false; openScanModal()" class="px-3 py-1.5 text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl border border-slate-300 transition cursor-pointer">
                                Ganti via Scan
                            </button>
                            <button type="button" @click="showSetorModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-xl leading-none cursor-pointer">&times;</button>
                        </div>
                    </div>

                    <form id="form-setor-sirkulasi" method="POST" action="{{ route('kasir.penerimaan.sirkulasi.store') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                        @csrf
                        <input type="hidden" name="agen_id" :value="activeAgen.id">
                        <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                        <input type="hidden" name="status_piutang" value="{{ $statusPiutang }}">

                        <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3.5 text-xs">
                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 text-xs space-y-1.5">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Nama &amp; Kode Agen</span>
                                    <span class="font-semibold text-slate-900"><span x-text="activeAgen.nama"></span> (<span class="font-mono text-kp-blue-700 font-bold" x-text="activeAgen.kode"></span>)</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Wilayah Sirkulasi</span>
                                    <span class="font-semibold text-slate-800" x-text="activeAgen.wilayah"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Saldo Piutang Koran</span>
                                    <span class="font-mono font-bold text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeAgen.piutang)"></span></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Periode Tagihan</span>
                                    <span class="font-semibold text-slate-800" x-text="activeAgen.bulan"></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Divisi &amp; Dokumen</label>
                                    <div class="h-10 px-3 bg-white rounded-xl border border-slate-300 font-semibold text-slate-800 flex items-center justify-between text-xs">
                                        <span>Sirkulasi (KI-02)</span>
                                        <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200"
                                              x-text="(activeAgen.metode === 'transfer' || activeAgen.metode === 'qris') ? 'BBM' : 'BKM'"></span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kode COA</label>
                                    <input type="hidden" name="kode_perkiraan" value="1.00.04.01.01.00">
                                    <input type="text" value="1.00.04.01.01.00" readonly
                                           class="w-full h-10 px-3 rounded-xl border border-slate-200 font-mono text-xs font-semibold text-slate-600 bg-slate-100 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">No. Blanko Fisik</label>
                                    <input type="text" name="no_blanko" placeholder="Opsional" 
                                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Petugas Inkaso</label>
                                    <select name="penagih_inkaso" x-model="activeAgen.penagih" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                        @foreach($petugasInkasoList as $petugas)
                                            <option value="{{ $petugas->name }}">{{ $petugas->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Setoran</label>
                                    <select name="metode_pembayaran" x-model="activeAgen.metode" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                        <option value="tunai">Tunai Meja Kasir (BKM)</option>
                                        <option value="transfer">Transfer Bank (BBM + KO-09)</option>
                                        <option value="qris">QRIS Resmi (BBM + KO-09)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Otorisasi Manajer</label>
                                    <input type="text" name="manager_otorisasi" value="Manajer Keuangan" required
                                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                </div>
                            </div>

                            <!-- Input Khusus Transfer -->
                            <div x-show="activeAgen.metode === 'transfer' || activeAgen.metode === 'qris'" class="p-3 bg-kp-blue-50 rounded-xl border border-kp-blue-200 space-y-2.5">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Bank Tujuan</label>
                                        <select name="bank_tujuan" x-model="activeAgen.bankTujuan" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                            <option value="Bank BJB Sirkulasi">Bank BJB Sirkulasi (Giro: 0012-0034-5678)</option>
                                            <option value="Bank BCA Tasikmalaya">Bank BCA (Giro: 054-8899-771)</option>
                                            <option value="Bank Mandiri Sirkulasi">Bank Mandiri (Giro: 131-00-9876543-2)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">No. Referensi Transfer</label>
                                        <input type="text" name="no_referensi_transfer" x-model="activeAgen.noRef" placeholder="Contoh: TRF-BJB-984128" 
                                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                    </div>
                                </div>
                                <div class="text-xs text-kp-blue-800">
                                    Otomatis mencatat <b>BBM (KI-02)</b> &amp; penyeimbang setor bank <b>KO-09</b>.
                                </div>
                            </div>

                            <!-- Nominal Uang Yang Disetor -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-sm font-semibold text-slate-700">Nominal Setoran (Rp)</label>
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border"
                                          :class="sisaPiutangBaru > 0 ? 'bg-slate-100 text-slate-700 border-slate-200' : 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200'"
                                          x-text="sisaPiutangBaru > 0 ? 'Cicilan Sebagian' : 'Pelunasan Penuh'"></span>
                                </div>
                                <input type="number" name="nominal_setoran" x-model="activeAgen.nominal" required min="1" 
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-base font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            </div>

                            <!-- Input Nomor Kuitansi Sementara jika Cicilan -->
                            <div x-show="sisaPiutangBaru > 0" x-transition class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1.5">
                                <label class="block text-xs font-semibold text-slate-700">No. Kuitansi Sementara (Cicilan)</label>
                                <input type="text" name="no_kuitansi_sementara" placeholder="Kosongkan untuk auto-generate (KS-SRK-...)"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                            </div>

                            <!-- Rincian Rekonsiliasi Hasil Setoran -->
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs space-y-1.5">
                                <div class="flex justify-between items-center text-slate-600">
                                    <span>Jumlah Setoran Kas Masuk:</span>
                                    <span class="font-bold font-mono text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeAgen.nominal || 0)"></span></span>
                                </div>
                                <div class="flex justify-between items-center text-slate-600 border-t border-slate-200 pt-1.5">
                                    <span>Sisa Saldo Piutang:</span>
                                    <span class="font-bold font-mono text-sm" :class="sisaPiutangBaru === 0 ? 'text-kp-blue-700' : 'text-slate-900'">
                                        Rp <span x-text="new Intl.NumberFormat('id-ID').format(sisaPiutangBaru)"></span>
                                        <span class="text-xs ml-1 font-sans font-medium" x-text="sisaPiutangBaru === 0 ? '(Lunas)' : '(Cicilan)'"></span>
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                                <input type="text" name="keterangan" x-model="activeAgen.keterangan" placeholder="Keterangan setoran agen..." 
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                            </div>
                        </div>

                        <div class="px-5 py-3.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0">
                            <button type="button" @click="showSetorModal = false" class="h-10 px-4 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition cursor-pointer">
                                Simpan &amp; Terbitkan BKM
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- Modal Cetak Tanda Terima Setoran Koran Otomatis -->
        @if(session('cetak_sirkulasi'))
        @php $srk = session('cetak_sirkulasi'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-white">
                    <div>
                        <div class="text-base font-bold tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[11px] text-slate-500">Divisi Sirkulasi &amp; Perkasiran Harian</div>
                        <div class="text-[11px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</div>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold rounded-md uppercase tracking-wider bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            {{ ($srk['jenis_dokumen'] ?? 'BKM') === 'BBM' ? 'BUKTI BANK MASUK (BBM)' : 'BUKTI KAS MASUK (BKM)' }}
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1">{{ $srk['no_bkm'] }}</div>
                        <div class="text-[11px] text-slate-500 font-mono">{{ $srk['tanggal'] }}</div>
                    </div>
                </div>

                <div class="print-document-body flex-1 overflow-y-auto p-5 space-y-2.5 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Nama Agen</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $srk['nama_agen'] }} ({{ $srk['kode_agen'] }})</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Wilayah Sirkulasi</span>
                        <span class="col-span-2 text-slate-800">: {{ $srk['wilayah'] }}</span>
                    </div>
                    @if(!empty($srk['is_cicilan']) && !empty($srk['no_kuitansi_sementara']))
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 bg-slate-50 px-2 rounded-lg">
                        <span class="text-slate-700 font-semibold">Ref. Kuitansi Sementara</span>
                        <span class="col-span-2 font-mono font-bold text-slate-900">: {{ $srk['no_kuitansi_sementara'] }} (Cicilan)</span>
                    </div>
                    @endif
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Petugas Inkaso</span>
                        <span class="col-span-2 font-semibold text-slate-800">: {{ $srk['penagih'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Metode Bayar</span>
                        <span class="col-span-2 font-semibold text-slate-800">: {{ $srk['metode'] }}</span>
                    </div>
                    @if(!empty($srk['no_ko_bank']))
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 bg-slate-50 px-2 rounded-lg">
                        <span class="text-slate-700 font-semibold">Double-Entry KO-09</span>
                        <span class="col-span-2 font-mono font-bold text-slate-900">: {{ $srk['no_ko_bank'] }} (Penyeimbang Setor Bank)</span>
                    </div>
                    @endif
                    @if($srk['no_bapr'] !== '-')
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 bg-slate-50 px-2 rounded-lg">
                        <span class="text-slate-700 font-semibold">Bukti Retur Sah (BAPR)</span>
                        <span class="col-span-2 font-mono text-slate-900">: {{ $srk['no_bapr'] }} ({{ $srk['retur_eks'] }} eks / Rp {{ number_format($srk['nilai_retur'], 0, ',', '.') }})</span>
                    </div>
                    @endif
                    <div class="grid grid-cols-3 py-2 bg-kp-blue-50 px-3 rounded-xl border border-kp-blue-200 my-2">
                        <span class="font-bold text-kp-blue-800">Jumlah Disetor</span>
                        <span class="col-span-2 font-mono font-bold text-base text-kp-blue-700">: Rp {{ number_format($srk['nominal'], 0, ',', '.') }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-1.5 text-xs bg-slate-50 px-3 rounded-lg border border-slate-200">
                        <span class="text-slate-500 font-semibold">Terbilang</span>
                        <span class="col-span-2 font-semibold italic text-slate-900">: {{ $srk['terbilang'] }}</span>
                    </div>

                    <div class="grid grid-cols-2 pt-6 border-t border-slate-200 text-center text-xs text-slate-600">
                        <div>
                            <div class="text-[11px] text-slate-500">&nbsp;</div>
                            <div class="font-semibold text-slate-700 mt-1">Otorisasi Manajer</div>
                            <div class="h-14"></div>
                            <div class="font-bold text-slate-900 underline">({{ $srk['manager_otorisasi'] ?? 'Manajer Keuangan' }})</div>
                            <div class="text-[10px] text-slate-400">Manajer Keuangan &amp; Kas</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-500">Tasikmalaya, {{ Carbon\Carbon::now()->format('d/m/Y') }}</div>
                            <div class="font-semibold text-slate-700 mt-1">Kasir Penerima</div>
                            <div class="h-14"></div>
                            <div class="font-bold text-slate-900 font-mono underline">({{ $srk['kasir'] }})</div>
                            <div class="text-[10px] text-slate-400">Bagian Perkasiran HU Priangan</div>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="h-10 px-4 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl border border-slate-300 transition cursor-pointer">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Modal Scan Barcode Agen Sirkulasi -->
        <div x-cloak x-show="showScanModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="closeScanModal()" class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
                <div class="flex items-center justify-between px-5 py-3.5 bg-white border-b border-slate-200">
                    <div>
                        <h4 class="text-base font-bold text-slate-900">Scan Barcode Agen Koran</h4>
                        <p class="text-xs text-slate-500">Pindai kartu agen / struk loper atau ketik kode agen</p>
                    </div>
                    <button type="button" @click="closeScanModal()" class="text-slate-400 hover:text-slate-600 font-bold text-xl leading-none cursor-pointer">&times;</button>
                </div>

                <div class="p-5 space-y-4">
                    <!-- Toggle Mode Auto-Submit -->
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-bold text-slate-900">Proses Otomatis Saat Scan</div>
                            <p class="text-xs text-slate-500">Langsung simpan BKM tunai dan buka struk cetak</p>
                        </div>
                        <input type="checkbox" x-model="autoProcess" class="rounded border-slate-300 text-kp-blue-600 focus:ring-kp-blue-600 w-4 h-4 cursor-pointer">
                    </div>

                    <!-- Scanner Laser Input Area -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kode Barcode / Kode Agen</label>
                        <div class="flex gap-2">
                            <input type="text" 
                                   x-ref="barcodeInput"
                                   x-model="scanInput" 
                                   @keydown.enter.prevent="processBarcode(scanInput)" 
                                   placeholder="Tembak barcode agen atau ketik kode agen..." 
                                   class="flex-1 h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            <button type="button" 
                                    @click="processBarcode(scanInput)" 
                                    :disabled="isScanning"
                                    class="h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition disabled:opacity-50 cursor-pointer shrink-0">
                                <span x-show="!isScanning">Proses</span>
                                <span x-show="isScanning">Mencari...</span>
                            </button>
                        </div>
                    </div>

                    <!-- Error Alert -->
                    <div x-show="scanError" x-transition class="p-3 bg-slate-100 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 flex items-center justify-between">
                        <span x-text="scanError"></span>
                        <button type="button" @click="scanError = ''" class="text-slate-500 hover:text-slate-800 font-bold ml-2 cursor-pointer">&times;</button>
                    </div>

                    <!-- Pilihan Scan Kamera & Unggah Gambar -->
                    <div class="border border-slate-200 rounded-xl p-3.5 bg-slate-50 space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold text-slate-700">Kamera Web / HP (Live Scan)</span>
                            <button type="button" 
                                    @click="cameraActive ? stopCamera() : startCamera()" 
                                    class="px-3 py-1.5 text-xs font-bold rounded-xl border transition cursor-pointer"
                                    :class="cameraActive ? 'bg-slate-200 text-slate-800 border-slate-300' : 'bg-kp-blue-600 text-white border-kp-blue-600 hover:bg-kp-blue-700'">
                                <span x-text="cameraActive ? 'Matikan Kamera' : 'Nyalakan Kamera'"></span>
                            </button>
                        </div>

                        <div x-show="cameraActive" class="rounded-xl overflow-hidden bg-slate-900 p-2">
                            <div id="reader-camera-sirkulasi" class="w-full overflow-hidden rounded-lg"></div>
                            <p class="text-xs text-slate-200 py-1.5 text-center">
                                Arahkan barcode kartu agen atau QR Code ke area kamera
                            </p>
                        </div>

                        <div class="pt-2.5 border-t border-slate-200 flex items-center justify-between text-xs">
                            <span class="text-slate-600">Unggah gambar / screenshot barcode:</span>
                            <label class="px-3 py-1.5 font-semibold rounded-xl bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 cursor-pointer transition">
                                <span>Pilih Gambar</span>
                                <input type="file" accept="image/*" @change="scanUploadedImage($event)" class="hidden">
                            </label>
                        </div>
                        <div id="temp-reader-sirkulasi" style="display:none;"></div>
                    </div>

                    <!-- Quick Click Simulation Barcodes -->
                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">
                            Pilih Cepat Agen:
                        </span>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse($agenList->take(6) as $a)
                                <button type="button" 
                                        @click="processBarcode('{{ $a->kode_agen }}')" 
                                        class="px-2.5 py-1 text-xs font-mono font-semibold rounded-lg bg-slate-100 hover:bg-kp-blue-50 hover:text-kp-blue-700 border border-slate-200 transition cursor-pointer">
                                    {{ $a->kode_agen }} ({{ $a->nama_agen }})
                                </button>
                            @empty
                                <span class="text-xs text-slate-400">Tidak ada agen.</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-end">
                    <button type="button" @click="closeScanModal()" class="h-10 px-4 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- Overlay Loading Saat Auto-Submit -->
        <div x-cloak x-show="isAutoSubmitting" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl border border-slate-200 flex flex-col items-center text-center space-y-4">
                <div class="w-12 h-12 rounded-xl bg-kp-blue-50 border border-kp-blue-200 text-kp-blue-700 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Barcode Terbaca</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Memproses setoran agen otomatis...</p>
                </div>
                <div class="w-full bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs space-y-1.5 font-mono text-left">
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-sans">Kode Agen:</span>
                        <span class="font-bold text-kp-blue-700" x-text="autoSubmitInfo.kode"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-sans">Nama Agen:</span>
                        <span class="font-semibold text-slate-900 truncate max-w-[180px]" x-text="autoSubmitInfo.agen"></span>
                    </div>
                    <div class="flex justify-between border-t border-slate-200 pt-1">
                        <span class="text-slate-500 font-sans">Nominal:</span>
                        <span class="font-bold text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(autoSubmitInfo.nominal)"></span></span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
    @endpush
</x-app-layout>
