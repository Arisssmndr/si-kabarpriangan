<x-app-layout>
    <script>
    function kasirIklanApp() {
        return {
            showModal: false, 
            showScanModal: false,
            scanInput: '',
            isScanning: false,
            scanError: '',
            cameraActive: false,
            html5QrScanner: null,
            autoProcess: true,
            isAutoSubmitting: false,
            autoSubmitInfo: { faktur: '', customer: '', nominal: 0 },
            showCetakModal: {{ session('cetak_bkm') ? 'true' : 'false' }},
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
            bayarForm: { 
                type: '', 
                id: '', 
                faktur: '', 
                customer: '', 
                tanggalMuat: '',
                totalTagihan: 0,
                sisa: 0, 
                nominal: 0, 
                isInstansiTax: false,
                potonganPajak: 0,
                dpp: 0,
                ppn11: 0,
                pph23: 0,
                metode: 'tunai',
                bankTujuan: 'Bank BJB Cabang Tasikmalaya',
                noRef: '',
                petugasInkaso: '{{ Auth::user()->name }}',
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
                if (!window._pasteBarcodeHandlerIklan) {
                    window._pasteBarcodeHandlerIklan = (e) => {
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
                    window.addEventListener('paste', window._pasteBarcodeHandlerIklan);
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
                    this.scanError = 'Masukkan atau tembak kode barcode / nomor faktur!';
                    return;
                }
                this.isScanning = true;
                this.scanError = '';
                try {
                    const res = await fetch(`{{ route('kasir.api.scan-iklan') }}?barcode=${encodeURIComponent(code)}`);
                    const json = await res.json();
                    if (json.success && json.data) {
                        this.playBeep();
                        const d = json.data;
                        const sisaPiutang = Number(d.sisa_piutang) || 0;
                        const totalTagihan = Number(d.total_tagihan) || 0;
                        const nominal = sisaPiutang > 0 ? sisaPiutang : totalTagihan;

                        if (sisaPiutang <= 0) {
                            this.scanError = `Faktur ${d.no_faktur} (${d.customer}) sudah LUNAS (Sisa Piutang: Rp 0).`;
                            this.isScanning = false;
                            return;
                        }

                        this.bayarForm = {
                            type: d.type,
                            id: d.id,
                            faktur: d.no_faktur,
                            customer: d.customer,
                            tanggalMuat: d.tanggal_muat || 'Hari ini',
                            totalTagihan: totalTagihan,
                            sisa: sisaPiutang,
                            nominal: nominal,
                            isInstansiTax: false,
                            potonganPajak: 0,
                            dpp: 0,
                            ppn11: 0,
                            pph23: 0,
                            metode: 'tunai',
                            bankTujuan: 'Bank BJB Cabang Tasikmalaya',
                            noRef: '',
                            petugasInkaso: '{{ Auth::user()->name }}',
                            keterangan: `Pelunasan Iklan ${d.no_faktur} (${d.customer})`
                        };
                        this.recalculateTax();
                        this.closeScanModal();

                        if (this.autoProcess) {
                            this.isAutoSubmitting = true;
                            this.autoSubmitInfo = {
                                faktur: d.no_faktur,
                                customer: d.customer,
                                nominal: nominal
                            };

                            this.$nextTick(() => {
                                const form = document.getElementById('form-bayar-iklan');
                                if (form) {
                                    const typeInput = form.querySelector('[name="type"]');
                                    const idInput = form.querySelector('[name="id"]');
                                    const nominalInput = form.querySelector('[name="nominal_bayar"]');
                                    const metodeInput = form.querySelector('[name="metode_pembayaran"]');
                                    const petugasInput = form.querySelector('[name="petugas_inkaso"]');
                                    const keteranganInput = form.querySelector('[name="keterangan"]');

                                    if (typeInput) typeInput.value = d.type;
                                    if (idInput) idInput.value = d.id;
                                    if (nominalInput) nominalInput.value = nominal;
                                    if (metodeInput) metodeInput.value = 'tunai';
                                    if (petugasInput) petugasInput.value = '{{ Auth::user()->name }}';
                                    if (keteranganInput) keteranganInput.value = `Pelunasan Iklan ${d.no_faktur} (${d.customer})`;

                                    form.submit();
                                }
                            });
                        } else {
                            this.showModal = true;
                            this.$nextTick(() => {
                                const nomInput = document.querySelector('input[name="nominal_bayar"]');
                                if (nomInput) { nomInput.focus(); nomInput.select(); }
                            });
                        }
                    } else {
                        this.scanError = json.message || 'Faktur iklan tidak ditemukan!';
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
                        this.html5QrScanner = new Html5Qrcode('reader-camera-iklan');
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
                    const scanner = new Html5Qrcode('temp-reader-iklan');
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
            recalculateTax() {
                if (this.bayarForm.isInstansiTax) {
                    let bruto = Number(this.bayarForm.nominal) || 0;
                    let dpp = Math.round((bruto * 100) / 111);
                    let ppn = Math.round(dpp * 0.11);
                    let pph = Math.round(dpp * 0.02);
                    this.bayarForm.dpp = dpp;
                    this.bayarForm.ppn11 = ppn;
                    this.bayarForm.pph23 = pph;
                    this.bayarForm.potonganPajak = ppn + pph;
                } else {
                    this.bayarForm.dpp = 0;
                    this.bayarForm.ppn11 = 0;
                    this.bayarForm.pph23 = 0;
                }
            },
            get uangFisikBersih() {
                let bruto = Number(this.bayarForm.nominal) || 0;
                let pajak = Number(this.bayarForm.potonganPajak) || 0;
                return Math.max(0, bruto - pajak);
            },
            get sisaSetelahBayar() {
                let sisa = this.bayarForm.sisa - Number(this.bayarForm.nominal);
                return sisa < 0 ? 0 : sisa;
            }
        };
    }
    </script>

    <div class="space-y-5" 
         @keydown.window.f2.prevent="openScanModal()"
         x-data="kasirIklanApp()">

        <!-- Konten Halaman Kasir (Disembunyikan saat modal cetak aktif) -->
        <div class="space-y-5" :class="{ 'print-hide-when-modal-open print:hidden': showCetakModal }">

            <!-- Header Halaman (Palet 60-30-10) -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 no-print print:hidden">
                <div class="flex items-center gap-3.5 min-w-0">
                    <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                                Setoran &amp; Pelunasan Faktur Iklan
                            </h1>
                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 font-mono">
                                KI-01
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-xl">
                            Penerimaan kas masuk (BKM/BBM) atas piutang faktur iklan cetak, online, dan TV.
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    @if(session('cetak_bkm'))
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

            <!-- Flash Message Alert -->
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

            <!-- 4 KPI Ringkasan Piutang & Setoran Iklan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sisa Piutang Berjalan</span>
                    <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($stats['total_piutang'] ?? 0, 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">
                        <span class="font-mono font-semibold text-slate-700">{{ $stats['faktur_piutang'] ?? 0 }}</span> faktur menunggu pelunasan
                    </span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Nilai Tagihan</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($stats['total_tagihan'] ?? 0, 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">
                        Akumulasi nilai faktur iklan
                    </span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kas Masuk Terbayar</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                        Rp {{ number_format($stats['total_dibayar'] ?? 0, 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">
                        Setoran BKM/BBM terverifikasi
                    </span>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Volume Faktur</span>
                    <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                        {{ number_format($stats['total_faktur'] ?? 0) }} <span class="text-xs font-sans font-normal text-slate-500">Faktur</span>
                    </p>
                    <span class="text-xs text-slate-400 mt-1 block">
                        {{ $stats['faktur_lunas'] ?? 0 }} lunas &bull; {{ $stats['faktur_piutang'] ?? 0 }} piutang
                    </span>
                </div>
            </div>

            <!-- Toolbar Barcode Cepat & Filter Pencarian -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs space-y-4" x-data="{ periode: '{{ $periodeTipe ?? 'semua' }}' }">
                <!-- Baris Input Cepat Barcode Scanner -->
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 pb-3.5 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-block px-2.5 py-1 rounded-md text-xs font-semibold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Scanner Siap
                        </span>
                        <span class="text-xs sm:text-sm text-slate-600">
                            Tembak barcode faktur atau ketik nomor faktur untuk proses cepat.
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
                                   placeholder="Tembak barcode / no faktur..." 
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
                <form method="GET" action="{{ route('kasir.penerimaan.iklan') }}" class="space-y-4">
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

                        @if(($statusPiutang ?? 'belum_lunas') !== 'belum_lunas' || ($periodeTipe ?? 'semua') !== 'semua' || (($saluranMedia ?? 'semua') !== 'semua' && ($saluranMedia ?? 'semua') !== 'all') || !empty($search))
                            <a href="{{ route('kasir.penerimaan.iklan') }}" class="text-xs font-semibold text-kp-blue-700 hover:underline">
                                Reset Filter
                            </a>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                        <div class="sm:col-span-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Data</label>
                            <input type="text" name="search" value="{{ $search ?? '' }}" 
                                   placeholder="No. faktur, nama pemasang, atau sales..." 
                                   class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Saluran Media</label>
                            <select name="saluran_media" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                <option value="semua" {{ in_array(($saluranMedia ?? 'semua'), ['semua', 'all', '']) ? 'selected' : '' }}>Semua Saluran Media</option>
                                <option value="koran" {{ ($saluranMedia ?? '') === 'koran' ? 'selected' : '' }}>Koran Cetak</option>
                                <option value="online" {{ ($saluranMedia ?? '') === 'online' ? 'selected' : '' }}>Media Online</option>
                                <option value="tv" {{ ($saluranMedia ?? '') === 'tv' ? 'selected' : '' }}>Priangan TV</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Periode Waktu</label>
                            <select name="periode_tipe" x-model="periode" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                <option value="semua">Semua Waktu</option>
                                <option value="hari">Per Tanggal</option>
                                <option value="bulan">Per Bulan</option>
                                <option value="tahun">Per Tahun</option>
                            </select>
                        </div>

                        <div class="sm:col-span-3 flex items-end gap-2">
                            <div x-show="periode === 'hari'" class="w-full">
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal</label>
                                <input type="date" name="tanggal" value="{{ $tanggalFilter ?? date('Y-m-d') }}" :disabled="periode !== 'hari'" class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            </div>

                            <div x-show="periode === 'bulan'" class="w-full grid grid-cols-2 gap-1.5">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bulan</label>
                                    <select name="bulan" :disabled="periode !== 'bulan'" class="w-full h-11 px-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                        @foreach([
                                            '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
                                            '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu',
                                            '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'
                                        ] as $blnNum => $blnLabel)
                                            <option value="{{ $blnNum }}" {{ (string)($bulanFilter ?? '') === (string)$blnNum || (int)($bulanFilter ?? 0) === (int)$blnNum ? 'selected' : '' }}>
                                                {{ $blnLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tahun</label>
                                    <select name="tahun" :disabled="periode !== 'bulan'" class="w-full h-11 px-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                        @for($y = (int)date('Y') + 1; $y >= 2020; $y--)
                                            <option value="{{ $y }}" {{ (int)($tahunFilter ?? date('Y')) === $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div x-show="periode === 'tahun'" class="w-full">
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tahun</label>
                                <select name="tahun" :disabled="periode !== 'tahun'" class="w-full h-11 px-3 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-kp-blue-600 transition shadow-2xs">
                                    @for($y = (int)date('Y') + 1; $y >= 2020; $y--)
                                        <option value="{{ $y }}" {{ (int)($tahunFilter ?? date('Y')) === $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div :class="periode === 'semua' ? 'w-full' : 'shrink-0'">
                                <button type="submit" class="w-full h-11 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center justify-center cursor-pointer">
                                    Cari
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabel Faktur & Piutang Iklan -->
            <div id="tabel-faktur-iklan" class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden scroll-mt-6">
                <div class="px-4 sm:px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Daftar Faktur Iklan</h3>
                        <p class="text-xs text-slate-500">Status piutang dan penerbitan BKM/BBM iklan</p>
                    </div>
                    <span class="text-xs font-semibold text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-mono">
                        {{ $fakturList->count() }} Faktur
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                            <tr>
                                <th class="py-3 px-3.5 text-center w-12">No</th>
                                <th class="py-3 px-3.5">No. Faktur</th>
                                <th class="py-3 px-3.5">Tgl Muat</th>
                                <th class="py-3 px-3.5">Media</th>
                                <th class="py-3 px-3.5">Pemasang</th>
                                <th class="py-3 px-3.5">Sales</th>
                                <th class="py-3 px-3.5 text-right">Tagihan</th>
                                <th class="py-3 px-3.5 text-right">Dibayar</th>
                                <th class="py-3 px-3.5 text-right">Piutang</th>
                                <th class="py-3 px-3.5 text-center">Komisi</th>
                                <th class="py-3 px-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                            @forelse($fakturList as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3.5 text-center text-slate-500 font-normal">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono font-bold text-kp-blue-700 text-sm">
                                    {{ $item->no_faktur }}
                                </td>
                                <td class="py-3.5 px-3.5 font-mono text-slate-600">
                                    {{ \Carbon\Carbon::parse($item->tanggal_muat)->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-3.5">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $item->type === 'koran' ? 'Koran Cetak' : ($item->type === 'online' ? 'Media Online' : 'Priangan TV') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5">
                                    <div class="font-semibold text-slate-900">{{ $item->nama_klien }}</div>
                                    <div class="text-xs font-mono text-slate-500">{{ $item->kode_rekening ?? 'AG-01-xxx' }}</div>
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600">
                                    {{ $item->sales }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums font-semibold text-slate-900">
                                    Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums text-slate-700">
                                    Rp {{ number_format($item->sudah_dibayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3.5 text-right font-mono tabular-nums">
                                    @if($item->sisa_piutang > 0)
                                        <span class="font-bold text-kp-blue-700">
                                            Rp {{ number_format($item->sisa_piutang, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">Rp 0</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($item->siap_cair)
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                            Siap Cair KH
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                            Terkunci
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-center">
                                    @if($item->sisa_piutang > 0)
                                        <button type="button" 
                                                @click="bayarForm = { 
                                                    type: '{{ $item->type }}', 
                                                    id: {{ $item->id }}, 
                                                    faktur: '{{ $item->no_faktur }}', 
                                                    customer: '{{ addslashes($item->nama_klien) }}', 
                                                    tanggalMuat: '{{ \Carbon\Carbon::parse($item->tanggal_muat)->format('d/m/Y') }}',
                                                    totalTagihan: {{ $item->total_tagihan }},
                                                    sisa: {{ $item->sisa_piutang }}, 
                                                    nominal: {{ $item->sisa_piutang }}, 
                                                    isInstansiTax: false,
                                                    potonganPajak: 0,
                                                    dpp: 0,
                                                    ppn11: 0,
                                                    pph23: 0,
                                                    metode: 'tunai',
                                                    bankTujuan: 'Bank BJB Cabang Tasikmalaya',
                                                    noRef: '',
                                                    petugasInkaso: '{{ Auth::user()->name }}',
                                                    keterangan: 'Pelunasan Iklan {{ $item->no_faktur }}'
                                                }; recalculateTax(); showModal = true"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                                            <span>Proses BKM</span>
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
                                <td colspan="11" class="py-10 text-center text-slate-500 text-sm">
                                    Tidak ada data faktur iklan yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Form Input Setoran Kas / Terbitkan BKM -->
            <div x-cloak x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
                <div @click.away="showModal = false" class="bg-white rounded-2xl max-w-xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-3.5 bg-white border-b border-slate-200 shrink-0">
                        <div>
                            <h4 class="text-base font-bold text-slate-900">Terbitkan Bukti Kas Masuk (BKM)</h4>
                            <p class="text-xs text-slate-500">Pelunasan atau cicilan faktur iklan</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="showModal = false; openScanModal()" class="px-3 py-1.5 text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl border border-slate-300 transition cursor-pointer">
                                Ganti via Scan
                            </button>
                            <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-xl leading-none cursor-pointer">&times;</button>
                        </div>
                    </div>

                    <form id="form-bayar-iklan" method="POST" action="{{ route('kasir.penerimaan.iklan.store') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                        @csrf
                        <input type="hidden" name="type" :value="bayarForm.type">
                        <input type="hidden" name="id" :value="bayarForm.id">

                        <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3.5">
                            <!-- Info Tagihan -->
                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 text-xs space-y-1.5">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">No. Faktur</span>
                                    <span class="font-mono font-bold text-kp-blue-700" x-text="bayarForm.faktur"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Pemasang / Klien</span>
                                    <span class="font-semibold text-slate-900" x-text="bayarForm.customer"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Sisa Piutang Saat Ini</span>
                                    <span class="font-mono font-bold text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(bayarForm.sisa)"></span></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Tanggal Muat</span>
                                    <span class="font-mono text-slate-700" x-text="bayarForm.tanggalMuat"></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Divisi &amp; Dokumen</label>
                                    <div class="h-10 px-3 bg-white rounded-xl border border-slate-300 font-semibold text-slate-800 flex items-center justify-between text-xs">
                                        <span>Iklan (KI-01)</span>
                                        <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200"
                                              x-text="(bayarForm.metode === 'transfer' || bayarForm.metode === 'qris') ? 'BBM' : 'BKM'"></span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kode COA</label>
                                    <input type="hidden" name="kode_perkiraan" value="1.00.04.01.01.01">
                                    <input type="text" value="1.00.04.01.01.01" readonly
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
                                    <select name="petugas_inkaso" x-model="bayarForm.petugasInkaso" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                        @foreach($petugasInkasoList as $petugas)
                                            <option value="{{ $petugas->name }}">{{ $petugas->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Pembayaran</label>
                                    <select name="metode_pembayaran" x-model="bayarForm.metode" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                        <option value="tunai">Tunai Kasir (BKM)</option>
                                        <option value="transfer">Transfer Bank (BBM + KO-09)</option>
                                        <option value="qris">QRIS Resmi (BBM + KO-09)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Otorisasi Manajer</label>
                                    <input type="text" name="manager_otorisasi" value="Manajer Keuangan" required
                                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                                </div>
                            </div>

                            <!-- Input Khusus Transfer -->
                            <div x-show="bayarForm.metode === 'transfer' || bayarForm.metode === 'qris'" class="p-3 bg-kp-blue-50 rounded-xl border border-kp-blue-200 space-y-2.5">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Bank Tujuan</label>
                                        <select name="bank_tujuan" x-model="bayarForm.bankTujuan" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                            <option value="Bank BJB Cabang Tasikmalaya">Bank BJB (Giro: 0012-0034-5678)</option>
                                            <option value="Bank BCA Tasikmalaya">Bank BCA (Giro: 054-8899-771)</option>
                                            <option value="Bank Mandiri Tasikmalaya">Bank Mandiri (Giro: 131-00-9876543-2)</option>
                                            <option value="Bank BRI Tasikmalaya">Bank BRI (Giro: 0100-01-002345-30-1)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">No. Referensi Transfer</label>
                                        <input type="text" name="no_referensi_transfer" x-model="bayarForm.noRef" placeholder="Contoh: TRF-BJB-984128" 
                                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                                    </div>
                                </div>
                                <div class="text-xs text-kp-blue-800">
                                    Otomatis mencatat <b>BBM (KI-01)</b> &amp; penyeimbang setor bank <b>KO-09</b>.
                                </div>
                            </div>

                            <!-- Nominal Tagihan -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-sm font-semibold text-slate-700">Nominal Faktur Dibayar (Rp)</label>
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium border"
                                          :class="sisaSetelahBayar > 0 ? 'bg-slate-100 text-slate-700 border-slate-200' : 'bg-kp-blue-50 text-kp-blue-700 border-kp-blue-200'"
                                          x-text="sisaSetelahBayar > 0 ? 'Cicilan Sebagian' : 'Pelunasan Penuh'"></span>
                                </div>
                                <input type="number" name="nominal_bayar" x-model="bayarForm.nominal" @input="recalculateTax()" required min="1" 
                                       class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-base font-mono font-bold text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition">
                            </div>

                            <!-- Input Nomor Kuitansi Sementara jika Cicilan -->
                            <div x-show="sisaSetelahBayar > 0" x-transition class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1.5">
                                <label class="block text-xs font-semibold text-slate-700">No. Kuitansi Sementara (Cicilan)</label>
                                <input type="text" name="no_kuitansi_sementara" placeholder="Kosongkan untuk auto-generate (KS-...)"
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                            </div>

                            <!-- Potongan Pajak WAPU -->
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-2.5">
                                <label class="flex items-start gap-2.5 cursor-pointer">
                                    <input type="checkbox" name="is_instansi_tax" value="1" x-model="bayarForm.isInstansiTax" @change="recalculateTax()" 
                                           class="mt-0.5 rounded border-slate-300 text-kp-blue-600 focus:ring-kp-blue-600 w-4 h-4">
                                    <div class="text-xs">
                                        <span class="font-semibold text-slate-900 block">Potong Pajak Instansi / WAPU (PPN 11% &amp; PPh 23 2%)</span>
                                        <span class="text-slate-500">Hitung otomatis DPP, PPN 11%, PPh 23 2%, dan KO penyeimbang pajak.</span>
                                    </div>
                                </label>

                                <div x-show="bayarForm.isInstansiTax" x-transition class="pt-2 border-t border-slate-200 space-y-2 text-xs">
                                    <div class="grid grid-cols-3 gap-2 bg-white p-2.5 rounded-xl border border-slate-200 font-mono">
                                        <div>
                                            <span class="text-slate-500 block">DPP</span>
                                            <span class="font-bold text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(bayarForm.dpp)"></span></span>
                                        </div>
                                        <div>
                                            <span class="text-slate-500 block">PPN 11%</span>
                                            <span class="font-bold text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(bayarForm.ppn11)"></span></span>
                                        </div>
                                        <div>
                                            <span class="text-slate-500 block">PPh 23 (2%)</span>
                                            <span class="font-bold text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(bayarForm.pph23)"></span></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between px-1">
                                        <span class="text-slate-600">Total Potongan Pajak:</span>
                                        <span class="font-bold font-mono text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(bayarForm.potonganPajak)"></span></span>
                                    </div>
                                    <div class="p-2.5 bg-kp-blue-50 rounded-xl border border-kp-blue-200 flex items-center justify-between">
                                        <span class="font-semibold text-kp-blue-800">Uang Bersih Diterima Kasir:</span>
                                        <span class="font-mono font-bold text-sm text-kp-blue-700">Rp <span x-text="new Intl.NumberFormat('id-ID').format(uangFisikBersih)"></span></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Potongan Pajak Manual jika Non-Instansi -->
                            <div x-show="!bayarForm.isInstansiTax">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Potongan Pajak Lainnya / Manual (Rp)</label>
                                <input type="number" name="potongan_pajak" x-model="bayarForm.potonganPajak" min="0" placeholder="0" 
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-sm font-mono font-semibold text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                            </div>

                            <!-- Live Calculation Preview -->
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs flex justify-between items-center">
                                <span class="text-slate-600">Sisa Piutang Setelah Bayar:</span>
                                <span class="font-bold font-mono text-sm" :class="sisaSetelahBayar === 0 ? 'text-kp-blue-700' : 'text-slate-900'">
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(sisaSetelahBayar)"></span>
                                    <span class="text-xs ml-1 font-sans font-medium" x-text="sisaSetelahBayar === 0 ? '(Lunas)' : '(Cicilan)'"></span>
                                </span>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan Tambahan</label>
                                <input type="text" name="keterangan" x-model="bayarForm.keterangan" placeholder="Catatan bukti kas masuk..." 
                                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-kp-blue-600 transition">
                            </div>
                        </div>

                        <div class="px-5 py-3.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0">
                            <button type="button" @click="showModal = false" class="h-10 px-4 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl transition cursor-pointer">
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

        <!-- Modal Cetak Kuitansi / Bukti Kas Masuk (BKM) Otomatis -->
        @if(session('cetak_bkm'))
        @php $bkm = session('cetak_bkm'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-white">
                    <div>
                        <div class="text-base font-bold tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[11px] text-slate-500">PT. Priangan Berkah Media - Bagian Perkasiran</div>
                        <div class="text-[11px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</div>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold rounded-md uppercase tracking-wider bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            {{ ($bkm['jenis_dokumen'] ?? 'BKM') === 'BBM' ? 'BUKTI BANK MASUK (BBM)' : 'BUKTI KAS MASUK (BKM)' }}
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1">{{ $bkm['no_bkm'] }}</div>
                        <div class="text-[11px] text-slate-500 font-mono">{{ $bkm['tanggal'] }}</div>
                    </div>
                </div>

                <div class="print-document-body flex-1 overflow-y-auto p-5 space-y-2.5 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Telah Diterima Dari</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $bkm['customer'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Nomor Faktur / Ref</span>
                        <span class="col-span-2 font-mono font-bold text-kp-blue-700">: {{ $bkm['faktur'] }}</span>
                    </div>
                    @if(!empty($bkm['is_cicilan']) && !empty($bkm['no_kuitansi_sementara']))
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 bg-slate-50 px-2 rounded-lg">
                        <span class="text-slate-700 font-semibold">Ref. Kuitansi Sementara</span>
                        <span class="col-span-2 font-mono font-bold text-slate-900">: {{ $bkm['no_kuitansi_sementara'] }} (Cicilan)</span>
                    </div>
                    @endif
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Metode Bayar</span>
                        <span class="col-span-2 font-semibold text-slate-800">: {{ $bkm['metode'] }} {{ $bkm['bank'] !== '-' ? '(' . $bkm['bank'] . ')' : '' }}</span>
                    </div>
                    @if($bkm['no_ref'] !== '-')
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">No. Bukti Transfer</span>
                        <span class="col-span-2 font-mono text-slate-800">: {{ $bkm['no_ref'] }}</span>
                    </div>
                    @endif
                    @if(!empty($bkm['no_ko_bank']))
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 bg-slate-50 px-2 rounded-lg">
                        <span class="text-slate-700 font-semibold">Double-Entry KO-09</span>
                        <span class="col-span-2 font-mono font-bold text-slate-900">: {{ $bkm['no_ko_bank'] }} (Penyeimbang Setor Bank)</span>
                    </div>
                    @endif
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Petugas Inkaso</span>
                        <span class="col-span-2 font-semibold text-slate-800">: {{ $bkm['penagih'] }}</span>
                    </div>
                    @if(($bkm['is_instansi_tax'] ?? false) || ($bkm['pajak'] ?? 0) > 0)
                    <div class="py-2 px-3 bg-slate-50 rounded-lg border border-slate-200 text-xs space-y-1 my-1">
                        <div class="font-bold text-slate-900 flex items-center justify-between">
                            <span>Potongan Pajak Dinas (WAPU):</span>
                            @if(!empty($bkm['no_bkk_pajak']))
                                <span class="font-mono text-[10px] text-slate-700 bg-white border border-slate-200 px-1.5 py-0.5 rounded">KO: {{ $bkm['no_bkk_pajak'] }}</span>
                            @endif
                        </div>
                        @if(!empty($bkm['dpp']))
                        <div class="grid grid-cols-3 gap-1 text-[11px] text-slate-700 font-mono pt-0.5">
                            <div>DPP: Rp {{ number_format($bkm['dpp'], 0, ',', '.') }}</div>
                            <div>PPN 11%: Rp {{ number_format($bkm['ppn'], 0, ',', '.') }}</div>
                            <div>PPh 23: Rp {{ number_format($bkm['pph23'], 0, ',', '.') }}</div>
                        </div>
                        @endif
                        <div class="text-[11px] text-slate-600 pt-0.5 font-mono">
                            Bruto: Rp {{ number_format($bkm['nominal'], 0, ',', '.') }} &bull; Pajak: Rp {{ number_format($bkm['pajak'], 0, ',', '.') }}
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-3 py-2 bg-kp-blue-50 px-3 rounded-xl border border-kp-blue-200">
                        <span class="font-bold text-kp-blue-800">Diterima Kas / Bank</span>
                        <span class="col-span-2 font-mono font-bold text-sm text-kp-blue-700">: Rp {{ number_format($bkm['nominal_fisik'] ?? $bkm['nominal'], 0, ',', '.') }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-1 text-xs">
                        <span class="text-slate-500">Terbilang</span>
                        <span class="col-span-2 font-semibold italic text-slate-900">: {{ $bkm['terbilang'] }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-1">
                        <span class="text-slate-500">Status Pembayaran</span>
                        <span class="col-span-2 font-bold {{ $bkm['status_bayar'] === 'LUNAS' ? 'text-kp-blue-700' : 'text-slate-700' }}">
                            : {{ $bkm['status_bayar'] }} 
                            @if($bkm['sisa_piutang'] > 0)
                                (Sisa Piutang: Rp {{ number_format($bkm['sisa_piutang'], 0, ',', '.') }})
                            @endif
                        </span>
                    </div>

                    <div class="grid grid-cols-3 pt-4 border-t border-slate-200 text-center text-[11px] text-slate-600">
                        <div>
                            <div>Penyetor / Klien</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900">({{ $bkm['customer'] }})</div>
                        </div>
                        <div>
                            <div>Otorisasi Manajer</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900">({{ $bkm['manager_otorisasi'] ?? 'Manajer Keuangan' }})</div>
                        </div>
                        <div>
                            <div>Kasir Penerima</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900 font-mono">({{ $bkm['kasir'] }})</div>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="h-10 px-4 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl border border-slate-300 transition cursor-pointer">
                        Tutup
                    </button>
                    @if(!empty($bkm['no_bkk_pajak']))
                    <a href="{{ route('kasir.pengeluaran.realisasi-bp') }}?search={{ $bkm['no_bkk_pajak'] }}" 
                       class="h-10 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 font-semibold text-xs rounded-xl transition flex items-center gap-1.5 cursor-pointer">
                        <span>KO Pajak ({{ $bkm['no_bkk_pajak'] }})</span>
                    </a>
                    @endif
                    <button type="button" onclick="window.print()" class="h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Modal Scan Barcode Faktur Iklan -->
        <div x-cloak x-show="showScanModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="closeScanModal()" class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
                <div class="flex items-center justify-between px-5 py-3.5 bg-white border-b border-slate-200">
                    <div>
                        <h4 class="text-base font-bold text-slate-900">Scan Barcode Faktur Iklan</h4>
                        <p class="text-xs text-slate-500">Pindai barcode faktur/SPPI atau ketik nomor faktur</p>
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
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kode Barcode / Nomor Faktur</label>
                        <div class="flex gap-2">
                            <input type="text" 
                                   x-ref="barcodeInput"
                                   x-model="scanInput" 
                                   @keydown.enter.prevent="processBarcode(scanInput)" 
                                   placeholder="Tembak scanner atau ketik nomor faktur..." 
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
                            <div id="reader-camera-iklan" class="w-full overflow-hidden rounded-lg"></div>
                            <p class="text-xs text-slate-200 py-1.5 text-center">
                                Arahkan barcode atau QR Code ke area kamera
                            </p>
                        </div>

                        <div class="pt-2.5 border-t border-slate-200 flex items-center justify-between text-xs">
                            <span class="text-slate-600">Unggah gambar / screenshot barcode:</span>
                            <label class="px-3 py-1.5 font-semibold rounded-xl bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 cursor-pointer transition">
                                <span>Pilih Gambar</span>
                                <input type="file" accept="image/*" @change="scanUploadedImage($event)" class="hidden">
                            </label>
                        </div>
                        <div id="temp-reader-iklan" style="display:none;"></div>
                    </div>

                    <!-- Quick Click Simulation Barcodes -->
                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">
                            Pilih Cepat Faktur Piutang:
                        </span>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse($fakturList->take(8) as $f)
                                <button type="button" 
                                        @click="processBarcode('{{ $f->no_faktur }}')" 
                                        class="px-2.5 py-1 text-xs font-mono font-semibold rounded-lg bg-slate-100 hover:bg-kp-blue-50 hover:text-kp-blue-700 border border-slate-200 transition cursor-pointer">
                                    {{ $f->no_faktur }}
                                </button>
                            @empty
                                <span class="text-xs text-slate-400">Tidak ada faktur.</span>
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
                    <p class="text-xs text-slate-500 mt-0.5">Memproses setoran faktur otomatis...</p>
                </div>
                <div class="w-full bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs space-y-1.5 font-mono text-left">
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-sans">No. Faktur:</span>
                        <span class="font-bold text-kp-blue-700" x-text="autoSubmitInfo.faktur"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-sans">Pemasang:</span>
                        <span class="font-semibold text-slate-900 truncate max-w-[180px]" x-text="autoSubmitInfo.customer"></span>
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
