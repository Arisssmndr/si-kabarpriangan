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
                            // LANGSUNG PROSES OTOMATIS:
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
                        this.html5QrScanner = new Html5Qrcode('reader-camera-iklan');
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
                    const html5QrCode = new Html5Qrcode('temp-reader-iklan');
                    const decodedText = await html5QrCode.scanFile(file, true);
                    this.playBeep();
                    this.processBarcode(decodedText);
                } catch (err) {
                    this.scanError = 'QR Code tidak terdeteksi pada gambar yang diunggah. Pastikan gambar jelas dan tidak buram.';
                } finally {
                    this.isScanning = false;
                    event.target.value = '';
                }
            },
            recalculateTax() {
                const nominal = Number(this.bayarForm.nominal) || 0;
                if (this.bayarForm.isInstansiTax) {
                    const dpp = Math.round(nominal / 1.11);
                    const ppn = Math.round(dpp * 0.11);
                    const pph = Math.round(dpp * 0.02);
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
                const nominal = Number(this.bayarForm.nominal) || 0;
                const pajak = Number(this.bayarForm.potonganPajak) || 0;
                return Math.max(0, nominal - pajak);
            },
            get sisaSetelahBayar() {
                const sisa = Number(this.bayarForm.sisa) || 0;
                const nominal = Number(this.bayarForm.nominal) || 0;
                return Math.max(0, sisa - nominal);
            }
        };
    }
    </script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <div class="space-y-5 pb-10" x-data="kasirIklanApp()">

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

        <!-- 1. Header Halaman Setoran Iklan (60-30-10 Palette) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="{{ asset('kabarpriangan.png') }}" alt="Kabar Priangan" class="h-9 sm:h-10 w-auto object-contain shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight truncate">
                            Setoran & Pelunasan Faktur Iklan
                        </h1>
                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                            Faktur Iklan
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm font-normal text-slate-500 mt-0.5 truncate max-w-2xl">
                        Penerimaan setoran kas & penerbitan Bukti Kas Masuk (BKM) dari piutang iklan tempo dengan integrasi komisi AE
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap sm:flex-nowrap">
                @if(session('cetak_bkm'))
                    <button type="button" @click="showCetakModal = true" class="inline-flex items-center gap-2 h-10 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Ulang BKM Terakhir</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- 2. KPI Finansial & Operasional (60-30-10 Palette) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Faktur</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    {{ number_format($stats['total_faktur'] ?? 0) }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">
                    {{ $stats['faktur_piutang'] ?? 0 }} Belum Lunas &bull; {{ $stats['faktur_lunas'] ?? 0 }} Lunas
                </span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Nilai Tagihan</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($stats['total_tagihan'] ?? 0, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">
                    Akumulasi nilai transaksi iklan
                </span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Sudah Dibayar</span>
                <p class="text-2xl font-bold text-kp-blue-700 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($stats['total_dibayar'] ?? 0, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">
                    Kas masuk terverifikasi BKM
                </span>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Sisa Piutang Berjalan</span>
                <p class="text-2xl font-bold text-slate-900 mt-1 font-mono tabular-nums">
                    Rp {{ number_format($stats['total_piutang'] ?? 0, 0, ',', '.') }}
                </p>
                <span class="text-xs text-slate-400 mt-1 block">
                    Menunggu pelunasan kasir
                </span>
            </div>
        </div>

        <!-- 3. Barcode / QR Code Scanner Bar (Terpisah, 60-30-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
                <div>
                    <span class="text-sm font-semibold text-slate-900 block">Scan Barcode / QR Code Faktur Iklan</span>
                    <span class="text-xs text-slate-500 block">Tembak scanner barcode laser atau ketik no faktur (Enter)</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <div class="relative flex-1 sm:w-72">
                    <input type="text" 
                           x-model="scanInput" 
                           @keydown.enter.prevent="processBarcode(scanInput)" 
                           placeholder="Scan barcode / no faktur..." 
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
        <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 shadow-xs space-y-4" x-data="{ 
            periode: '{{ $periodeTipe ?? 'semua' }}'
        }">
            <form method="GET" action="{{ route('kasir.penerimaan.iklan') }}" class="space-y-4">
                <!-- Baris 1: Filter Status Piutang -->
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
                    @if(($statusPiutang ?? 'belum_lunas') !== 'belum_lunas' || ($periodeTipe ?? 'semua') !== 'semua' || (($saluranMedia ?? 'semua') !== 'semua' && ($saluranMedia ?? 'semua') !== 'all') || !empty($search))
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-kp-blue-700 bg-kp-blue-50 border border-kp-blue-200 px-2.5 py-1 rounded-md font-medium flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-kp-blue-600 animate-pulse"></span>
                                Filter Aktif
                            </span>
                            <a href="{{ route('kasir.penerimaan.iklan') }}" class="text-xs text-slate-500 hover:text-slate-800 font-semibold flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reset Filter
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Baris 2: Kontrol Filter Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <div class="sm:col-span-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pencarian Data</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search ?? '' }}" 
                                   placeholder="No. Faktur, Klien, atau Tanggal..." 
                                   class="w-full h-11 pl-9 pr-3 text-sm rounded-xl border border-slate-300 bg-white focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 text-slate-900 placeholder-slate-400 transition-all shadow-2xs">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Saluran Media</label>
                        <select name="saluran_media" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            <option value="semua" {{ in_array(($saluranMedia ?? 'semua'), ['semua', 'all', '']) ? 'selected' : '' }}>Semua Saluran Media</option>
                            <option value="koran" {{ ($saluranMedia ?? '') === 'koran' ? 'selected' : '' }}>Koran Cetak</option>
                            <option value="online" {{ ($saluranMedia ?? '') === 'online' ? 'selected' : '' }}>Media Online</option>
                            <option value="tv" {{ ($saluranMedia ?? '') === 'tv' ? 'selected' : '' }}>Priangan TV</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Periode Waktu</label>
                        <select name="periode_tipe" x-model="periode" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                            <option value="semua">Semua Waktu</option>
                            <option value="hari">Per Hari (Tanggal)</option>
                            <option value="bulan">Per Bulan</option>
                            <option value="tahun">Per Tahun</option>
                        </select>
                    </div>

                    <div class="sm:col-span-3 flex items-center gap-2">
                        <div x-show="periode === 'hari'" class="w-full">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pilih Tanggal</label>
                            <input type="date" name="tanggal" value="{{ $tanggalFilter ?? date('Y-m-d') }}" :disabled="periode !== 'hari'" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                        </div>

                        <div x-show="periode === 'bulan'" class="w-full grid grid-cols-2 gap-1.5">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bulan</label>
                                <select name="bulan" :disabled="periode !== 'bulan'" class="w-full h-11 px-2.5 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
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
                                <select name="tahun" :disabled="periode !== 'bulan'" class="w-full h-11 px-2.5 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                    @for($y = (int)date('Y') + 1; $y >= 2020; $y--)
                                        <option value="{{ $y }}" {{ (int)($tahunFilter ?? date('Y')) === $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div x-show="periode === 'tahun'" class="w-full">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pilih Tahun</label>
                            <select name="tahun" :disabled="periode !== 'tahun'" class="w-full h-11 px-3.5 bg-white border border-slate-300 rounded-xl text-sm font-normal text-slate-900 focus:outline-none focus:border-kp-blue-600 focus:ring-1 focus:ring-kp-blue-600 transition shadow-2xs">
                                @for($y = (int)date('Y') + 1; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ (int)($tahunFilter ?? date('Y')) === $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <div :class="periode === 'semua' ? 'w-full' : 'shrink-0'">
                            <label class="block text-sm font-semibold text-transparent mb-1.5 select-none">Aksi</label>
                            <button type="submit" class="w-full h-11 px-5 inline-flex items-center justify-center gap-1.5 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-semibold text-sm rounded-xl transition shadow-xs cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                <span>Terapkan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- 5. Pure Data Table: Faktur & Piutang Iklan (Tanpa Banner Tempel di Atas Tabel) -->
        <div id="tabel-faktur-iklan" class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden scroll-mt-6">
            <div class="overflow-x-auto min-h-[300px]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-semibold text-xs whitespace-nowrap">
                        <tr>
                            <th class="py-3 px-3">No. Faktur</th>
                            <th class="py-3 px-3">Saluran Media</th>
                            <th class="py-3 px-3">Nama Pemasang / Klien</th>
                            <th class="py-3 px-3">Sales / AE</th>
                            <th class="py-3 px-3 text-right">Total Tagihan</th>
                            <th class="py-3 px-3 text-right">Sudah Dibayar</th>
                            <th class="py-3 px-3 text-right">Sisa Piutang</th>
                            <th class="py-3 px-3 text-center">Status Komisi</th>
                            <th class="py-3 px-3 text-center">Aksi / Bayar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 whitespace-nowrap">
                        @forelse($fakturList as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3 align-top">
                                <div class="font-bold text-kp-blue-700 font-mono">{{ $item->no_faktur }}</div>
                                <span class="block text-xs text-slate-400 mt-0.5">
                                    Muat: {{ \Carbon\Carbon::parse($item->tanggal_muat)->format('d/m/Y') }}
                                </span>
                            </td>

                            <td class="py-3 px-3 align-top whitespace-nowrap">
                                @if($item->type === 'koran')
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        Koran Cetak
                                    </span>
                                @elseif($item->type === 'online')
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200">
                                        Media Online
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        Priangan TV
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-3 align-top">
                                <span class="font-semibold text-slate-900 block">{{ $item->nama_klien }}</span>
                                <span class="text-xs text-slate-500 font-mono">{{ $item->kode_rekening ?? 'AG-01-xxx' }}</span>
                                @if(!empty($item->deskripsi_tayang))
                                    <span class="block text-xs text-slate-400 mt-0.5 max-w-[200px] truncate" title="{{ $item->deskripsi_tayang }}">{{ $item->deskripsi_tayang }}</span>
                                @endif
                            </td>

                            <td class="py-3 px-3 align-top text-slate-600 whitespace-nowrap">
                                <span>{{ $item->sales }}</span>
                            </td>

                            <td class="py-3 px-3 align-top text-right font-mono font-medium text-slate-700 whitespace-nowrap">
                                Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                            </td>

                            <td class="py-3 px-3 align-top text-right font-mono font-semibold text-kp-blue-700 whitespace-nowrap">
                                Rp {{ number_format($item->sudah_dibayar, 0, ',', '.') }}
                            </td>

                            <td class="py-3 px-3 align-top text-right whitespace-nowrap">
                                @if($item->sisa_piutang > 0)
                                    <span class="font-mono font-bold text-slate-900">
                                        Rp {{ number_format($item->sisa_piutang, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="font-mono text-slate-400">
                                        Rp 0
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-3 align-top text-center whitespace-nowrap">
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

                            <td class="py-3 px-3 align-top text-center whitespace-nowrap">
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
                                            class="inline-flex items-center gap-1.5 h-8 px-3 bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 text-white font-semibold text-xs rounded-lg transition shadow-2xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <span>Proses BKM</span>
                                    </button>
                                @else
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        Lunas
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400 text-xs">
                                Tidak ada data faktur iklan yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form Input Setoran Kas / Terbitkan BKM -->
        <div x-cloak x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showModal = false" class="bg-white rounded-2xl max-w-xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200 shrink-0 bg-slate-50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-kp-blue-600 text-white flex items-center justify-center shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Terbitkan Bukti Kas Masuk (BKM)</h4>
                            <p class="text-xs text-slate-500">Pencatatan kas masuk & pelunasan faktur piutang iklan</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="showModal = false; openScanModal()" class="px-2.5 py-1 text-xs bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-lg border border-slate-300 transition flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <span>Scan QR</span>
                        </button>
                        <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
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
                                <span class="text-slate-500">No. Faktur:</span>
                                <span class="font-mono font-bold text-kp-blue-700" x-text="bayarForm.faktur"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Pemasang / Klien:</span>
                                <span class="font-semibold text-slate-900" x-text="bayarForm.customer"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Sisa Piutang Saat Ini:</span>
                                <span class="font-mono font-bold text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(bayarForm.sisa)"></span></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Tanggal Muat:</span>
                                <span class="font-medium text-slate-800" x-text="bayarForm.tanggalMuat"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Divisi & Dokumen</label>
                                <div class="h-9 px-3 bg-white rounded-lg border border-slate-200 font-semibold text-slate-800 flex items-center justify-between text-xs">
                                    <span>Divisi Iklan (KI-01)</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded font-mono font-bold bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200"
                                          x-text="(bayarForm.metode === 'transfer' || bayarForm.metode === 'qris') ? 'BBM' : 'BKM'"></span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Perkiraan (COA)</label>
                                <input type="hidden" name="kode_perkiraan" value="1.00.04.01.01.01">
                                <input type="text" value="1.00.04.01.01.01 (Piutang Iklan)" readonly
                                       class="w-full h-9 px-2.5 rounded-lg border border-slate-200 font-mono text-xs text-slate-600 bg-slate-100 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">No. Blanko (Opsional)</label>
                                <input type="text" name="no_blanko" placeholder="BLK-01982" 
                                       class="w-full h-9 px-3 rounded-lg border border-slate-300 font-mono text-xs text-slate-800 focus:outline-none focus:border-kp-blue-600 bg-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Petugas Inkaso</label>
                                <select name="petugas_inkaso" x-model="bayarForm.petugasInkaso" class="w-full h-10 px-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-xs text-slate-800 bg-white">
                                    @foreach($petugasInkasoList as $petugas)
                                        <option value="{{ $petugas->name }}">{{ $petugas->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Pembayaran</label>
                                <select name="metode_pembayaran" x-model="bayarForm.metode" class="w-full h-10 px-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-xs text-slate-800 bg-white">
                                    <option value="tunai">Tunai Kasir (Fisik BKM)</option>
                                    <option value="transfer">Transfer Bank (BBM + KO-09)</option>
                                    <option value="qris">QRIS / E-Wallet Resmi (BBM + KO-09)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Otorisasi Manajer</label>
                                <input type="text" name="manager_otorisasi" value="Manajer Keuangan" required
                                       class="w-full h-10 px-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-xs text-slate-800 bg-white font-medium">
                            </div>
                        </div>

                        <!-- Input Khusus Transfer -->
                        <div x-show="bayarForm.metode === 'transfer' || bayarForm.metode === 'qris'" class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Bank Tujuan Rekening</label>
                                    <select name="bank_tujuan" x-model="bayarForm.bankTujuan" class="w-full h-9 px-2.5 rounded-lg border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-xs text-slate-800 bg-white">
                                        <option value="Bank BJB Cabang Tasikmalaya">Bank BJB (Giro: 0012-0034-5678)</option>
                                        <option value="Bank BCA Tasikmalaya">Bank BCA (Giro: 054-8899-771)</option>
                                        <option value="Bank Mandiri Tasikmalaya">Bank Mandiri (Giro: 131-00-9876543-2)</option>
                                        <option value="Bank BRI Tasikmalaya">Bank BRI (Giro: 0100-01-002345-30-1)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">No. Bukti Transfer</label>
                                    <input type="text" name="no_referensi_transfer" x-model="bayarForm.noRef" placeholder="TRF-BJB-984128" 
                                           class="w-full h-9 px-2.5 rounded-lg border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-xs text-slate-800 font-mono bg-white">
                                </div>
                            </div>
                            <div class="text-xs text-slate-600 bg-white p-2.5 rounded-lg border border-slate-200 flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded bg-slate-200 text-slate-800 font-mono font-bold text-[10px] shrink-0">DOUBLE-ENTRY KO-09</span>
                                <span>Penerimaan KI-01 (BBM) otomatis mencatat Pengeluaran Penyeimbang Setor Bank (KO-09) agar kas fisik tetap seimbang.</span>
                            </div>
                        </div>

                        <!-- Nominal Tagihan Dibayar -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-slate-700">Nilai Tagihan / Faktur yang Dibayar (Rp)</label>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md"
                                      :class="sisaSetelahBayar > 0 ? 'bg-slate-100 text-slate-700 border border-slate-200' : 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200'"
                                      x-text="sisaSetelahBayar > 0 ? 'CICILAN SEBAGIAN' : 'PELUNASAN PENUH'"></span>
                            </div>
                            <input type="number" name="nominal_bayar" x-model="bayarForm.nominal" @input="recalculateTax()" required min="1" 
                                   class="w-full h-11 px-3.5 text-base font-mono font-bold rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-slate-900 bg-white">
                            <span class="text-xs text-slate-500 mt-1 block">Otomatis menampilkan Sisa Piutang terbaru. Ubah nominal jika membayar cicilan sebagian.</span>
                        </div>

                        <!-- Kuitansi Sementara jika Cicilan Sebagian -->
                        <div x-show="sisaSetelahBayar > 0" x-transition class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700">Nomor Kuitansi Sementara (Cicilan)</label>
                            <input type="text" name="no_kuitansi_sementara" placeholder="Kosongkan untuk auto-generate (Contoh: KS-202611-0001)"
                                   class="w-full h-9 px-3 rounded-lg border border-slate-300 font-mono text-xs text-slate-900 bg-white focus:outline-none focus:border-kp-blue-600">
                            <p class="text-[11px] text-slate-500">SOP Penerimaan: Pembayaran cicilan menggunakan referensi Kuitansi Sementara (Faktur asli masih ditahan).</p>
                        </div>

                        <!-- Checkbox Toggle WAPU -->
                        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2.5">
                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input type="checkbox" name="is_instansi_tax" value="1" x-model="bayarForm.isInstansiTax" @change="recalculateTax()" 
                                       class="mt-0.5 rounded border-slate-300 text-kp-blue-600 focus:ring-kp-blue-600 w-4 h-4">
                                <div class="text-xs">
                                    <span class="font-semibold text-slate-900 block">Potong Pajak Dinas / Instansi Pemerintah (WAPU)</span>
                                    <span class="text-slate-500">DPRD, Pemkot, Setda, Diskominfo (Kewajiban potong PPN 11% & PPh 23 2% dari DPP)</span>
                                </div>
                            </label>

                            <div x-show="bayarForm.isInstansiTax" x-transition class="pt-2 border-t border-slate-200 space-y-2 text-xs">
                                <div class="grid grid-cols-3 gap-2 bg-white p-2.5 rounded-lg border border-slate-200 font-mono">
                                    <div>
                                        <span class="text-[10px] text-slate-500 block">DPP (Dasar Pajak)</span>
                                        <span class="font-bold text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(bayarForm.dpp)"></span></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-500 block">PPN 11%</span>
                                        <span class="font-bold text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(bayarForm.ppn11)"></span></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-500 block">PPh 23 (2%)</span>
                                        <span class="font-bold text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(bayarForm.pph23)"></span></span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between px-1 text-xs">
                                    <span class="text-slate-600 font-medium">Total Potongan Beban Pajak:</span>
                                    <span class="font-bold font-mono text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(bayarForm.potonganPajak)"></span></span>
                                </div>
                                <div class="p-2.5 bg-kp-blue-50 rounded-xl border border-kp-blue-200 flex items-center justify-between text-xs">
                                    <span class="font-semibold text-kp-blue-900">Uang Fisik Diterima Kasir:</span>
                                    <span class="font-mono font-bold text-sm text-kp-blue-700">Rp <span x-text="new Intl.NumberFormat('id-ID').format(uangFisikBersih)"></span></span>
                                </div>
                            </div>
                        </div>

                        <!-- Potongan Pajak Manual jika Non-Instansi -->
                        <div x-show="!bayarForm.isInstansiTax">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Potongan Pajak Lainnya / Manual (Rp)</label>
                            <input type="number" name="potongan_pajak" x-model="bayarForm.potonganPajak" min="0" placeholder="0" 
                                   class="w-full h-10 px-3.5 text-sm font-mono font-bold rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-slate-900">
                        </div>

                        <!-- Live Calculation Preview -->
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Sisa Piutang Setelah Transaksi Ini:</span>
                                <span class="font-bold font-mono text-sm text-slate-900">
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(sisaSetelahBayar)"></span>
                                    <span class="text-xs ml-1 font-semibold text-kp-blue-700" x-text="sisaSetelahBayar === 0 ? '(Lunas - Siap Cair Komisi)' : '(Sisa Piutang)'"></span>
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan Tambahan (Opsional)</label>
                            <input type="text" name="keterangan" x-model="bayarForm.keterangan" placeholder="Catatan bukti kas masuk..." 
                                   class="w-full h-10 px-3 rounded-xl border border-slate-300 focus:outline-none focus:border-kp-blue-600 text-xs text-slate-900">
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="px-5 py-3.5 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showModal = false" class="h-10 px-4 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="h-10 px-5 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl transition shadow-2xs">
                            Simpan & Terbitkan BKM
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Cetak Kuitansi / Bukti Kas Masuk (BKM) -->
        @if(session('cetak_bkm'))
        @php $bkm = session('cetak_bkm'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-slate-50">
                    <div>
                        <div class="text-base font-bold tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500">PT. Priangan Berkah Media - Bagian Perkasiran</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md uppercase tracking-wider border {{ ($bkm['jenis_dokumen'] ?? 'BKM') === 'BBM' ? 'bg-slate-100 text-slate-700 border-slate-200' : 'bg-kp-blue-50 text-kp-blue-700 border border-kp-blue-200' }}">
                            {{ ($bkm['jenis_dokumen'] ?? 'BKM') === 'BBM' ? 'BUKTI BANK MASUK (BBM)' : 'BUKTI KAS MASUK (BKM)' }}
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1">{{ $bkm['no_bkm'] }}</div>
                        <div class="text-[10px] text-slate-500">{{ $bkm['tanggal'] }}</div>
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
                        <span class="col-span-2 font-mono font-bold text-slate-900">: {{ $bkm['no_kuitansi_sementara'] }} (Cicilan Sebagian)</span>
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

                    <div class="grid grid-cols-3 py-1.5 bg-kp-blue-50 px-2.5 rounded-lg border border-kp-blue-200">
                        <span class="font-bold text-kp-blue-900">Uang Diterima</span>
                        <span class="col-span-2 font-mono font-bold text-sm text-kp-blue-700">: Rp {{ number_format($bkm['nominal_fisik'] ?? $bkm['nominal'], 0, ',', '.') }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-1 text-xs">
                        <span class="text-slate-500">Terbilang Huruf</span>
                        <span class="col-span-2 font-semibold italic text-slate-900">: {{ $bkm['terbilang'] }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-1">
                        <span class="text-slate-500">Status Pembayaran</span>
                        <span class="col-span-2 font-bold text-slate-900">
                            : {{ $bkm['status_bayar'] }} 
                            @if($bkm['sisa_piutang'] > 0)
                                (Sisa Piutang: Rp {{ number_format($bkm['sisa_piutang'], 0, ',', '.') }})
                            @endif
                        </span>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-200 text-center text-xs text-slate-600">
                        <div class="w-48">
                            <div>Tasikmalaya, {{ date('d/m/Y') }}</div>
                            <div class="font-semibold text-slate-700 mt-0.5">Kasir Penerima</div>
                            <div class="h-12"></div>
                            <div class="font-bold text-slate-900 font-mono border-t border-slate-400 pt-1">({{ $bkm['kasir'] }})</div>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="h-9 px-4 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-300 transition">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="h-9 px-4 bg-kp-blue-600 hover:bg-kp-blue-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Modal Aksi Scan QR Code Faktur Iklan -->
        <div x-cloak x-show="showScanModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="closeScanModal()" class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
                <div class="flex items-center justify-between px-5 py-3.5 bg-slate-900 text-white">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-kp-blue-600 text-white flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-white">Scan QR Code Faktur Iklan</h4>
                            <p class="text-xs text-slate-300">Arahkan scanner kamera ke QR Code faktur / SPPI</p>
                        </div>
                    </div>
                    <button type="button" @click="closeScanModal()" class="text-slate-400 hover:text-white font-bold text-lg leading-none cursor-pointer">&times;</button>
                </div>

                <div class="p-5 space-y-4">
                    <!-- Scanner Laser / Keyboard Input Area -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Kode QR / Nomor Faktur</label>
                        <div class="relative">
                            <input type="text" 
                                   x-ref="barcodeInput"
                                   x-model="scanInput" 
                                   @keydown.enter.prevent="processBarcode(scanInput)" 
                                   placeholder="Arahkan scanner QR atau ketik nomor faktur..." 
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

                    <!-- Error Alert -->
                    <div x-show="scanError" x-transition class="p-3 bg-slate-100 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 flex items-center justify-between">
                        <span x-text="scanError"></span>
                        <button type="button" @click="scanError = ''" class="text-slate-500 font-bold">&times;</button>
                    </div>

                    <!-- Pilihan Scan Kamera & Unggah Gambar -->
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
                            <div id="reader-camera-iklan" class="w-full overflow-hidden rounded-lg aspect-square max-w-[340px] mx-auto"></div>
                        </div>

                        <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
                            <span class="text-slate-500">Unggah berkas screenshot QR?</span>
                            <label class="h-8 px-3 font-semibold rounded-lg bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 cursor-pointer transition flex items-center gap-1">
                                <span>Pilih Gambar QR</span>
                                <input type="file" accept="image/*" @change="scanUploadedImage($event)" class="hidden">
                            </label>
                        </div>
                        <div id="temp-reader-iklan" style="display:none;"></div>
                    </div>
                </div>

                <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-end text-xs">
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
                    <p class="text-xs text-slate-500 mt-0.5">Memproses input setoran secara otomatis ke Buku Kas...</p>
                </div>
                <div class="w-full bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs space-y-1 font-mono text-left">
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-sans">No. Faktur:</span>
                        <span class="font-bold text-kp-blue-700" x-text="autoSubmitInfo.faktur"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-sans">Klien:</span>
                        <span class="font-bold text-slate-800 truncate max-w-[180px]" x-text="autoSubmitInfo.customer"></span>
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