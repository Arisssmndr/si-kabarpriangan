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
            html5QrScanner: null,
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

    <div class="space-y-6" 
         @keydown.window.f2.prevent="openScanModal()"
         x-data="kasirIklanApp()">

        <!-- Konten Halaman Kasir (Disembunyikan saat modal cetak aktif) -->
        <div class="space-y-6" :class="{ 'print:hidden': showCetakModal }">

        <!-- Flash Message Alert -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-semibold text-emerald-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs font-semibold text-rose-800 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- Header Page Setoran Iklan (OripioFin Style) -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 shadow-sm shadow-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 border border-blue-200/80 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <div>
                    <!-- Breadcrumb minimalis dengan indikator biru aktif -->
                    <div class="flex items-center gap-2 text-xs font-medium text-slate-400 mb-1">
                        <span>Kasir</span>
                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span>Penerimaan</span>
                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="text-blue-600 font-semibold flex items-center gap-1.5 bg-blue-50/80 px-2 py-0.5 rounded-md border border-blue-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                            Setoran Iklan
                        </span>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Setoran & Pelunasan Faktur Iklan
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-blue-50 text-blue-700 border border-blue-200">
                            Faktur Iklan
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Penerimaan setoran kas & penerbitan Bukti Kas Masuk (BKM) dari piutang iklan tempo dengan integrasi komisi AE
                    </p>
                </div>
            </div>

            @if(session('cetak_bkm'))
                <button type="button" @click="showCetakModal = true" class="px-3.5 py-2 bg-blue-50 hover:bg-blue-100 active:scale-95 text-blue-700 font-bold text-xs rounded-xl border border-blue-200 transition shadow-2xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Ulang BKM Terakhir</span>
                </button>
            @endif
        </div>

        <!-- KPI Ringkasan Piutang & Setoran Iklan (OripioFin Hover Lift) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm shadow-slate-100 hover:-translate-y-1 hover:shadow-md transition-all duration-200 group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Faktur</span>
                    <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-xs group-hover:scale-105 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                </div>
                <p class="text-2xl font-bold text-slate-900 mt-2 tracking-tight">{{ number_format($stats['total_faktur'] ?? 0) }}</p>
                <p class="text-[11px] text-slate-500 mt-1">
                    <span class="font-semibold text-rose-600">{{ $stats['faktur_piutang'] ?? 0 }} Belum Lunas</span> • <span class="font-semibold text-emerald-600">{{ $stats['faktur_lunas'] ?? 0 }} Lunas</span>
                </p>
            </div>

            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm shadow-slate-100 hover:-translate-y-1 hover:shadow-md transition-all duration-200 group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Nilai Tagihan</span>
                    <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center text-xs group-hover:scale-105 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <p class="text-2xl font-bold text-slate-900 mt-2 tracking-tight">Rp {{ number_format($stats['total_tagihan'] ?? 0, 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Akumulasi nilai transaksi</p>
            </div>

            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm shadow-slate-100 hover:-translate-y-1 hover:shadow-md transition-all duration-200 group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Sudah Dibayar</span>
                    <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-xs group-hover:scale-105 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <p class="text-2xl font-bold text-emerald-600 mt-2 tracking-tight">Rp {{ number_format($stats['total_dibayar'] ?? 0, 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Kas masuk terverifikasi</p>
            </div>

            <div class="bg-white border border-rose-200/80 rounded-2xl p-5 shadow-sm shadow-slate-100 hover:-translate-y-1 hover:shadow-md transition-all duration-200 group bg-rose-50/10">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-rose-700 uppercase tracking-wider">Sisa Piutang Berjalan</span>
                    <span class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center text-xs group-hover:scale-105 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                </div>
                <p class="text-2xl font-extrabold text-rose-600 mt-2 tracking-tight">Rp {{ number_format($stats['total_piutang'] ?? 0, 0, ',', '.') }}</p>
                <p class="text-[11px] text-rose-600/80 mt-1">Menunggu pelunasan BKM kasir</p>
            </div>
        </div>

        <!-- Panel Dua Pilihan: Input Manual vs Scan Barcode -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-blue-950 border border-slate-700/80 rounded-2xl p-5 shadow-md text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wider rounded bg-blue-500/20 text-blue-300 border border-blue-400/30">
                            Mode Input Setoran
                        </span>
                        <span class="text-xs text-slate-400">&bull; Pilih metode pemrosesan setoran BKM</span>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                        <span>Pilihan 1: Proses Manual</span>
                        <span class="text-slate-400 font-normal">atau</span>
                        <span class="text-blue-300 font-extrabold">Pilihan 2: Scan Barcode Cepat</span>
                    </h3>
                    <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                        Anda dapat memilih faktur dari tabel secara manual, atau menggunakan aksi <b>Scan Barcode</b> untuk pengisian otomatis nomor faktur, data klien, dan sisa piutang secara instan.
                    </p>
                </div>

                <!-- Action Buttons: 2 Pilihan -->
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <!-- Pilihan 1: Manual -->
                    <button type="button" 
                            @click="document.getElementById('tabel-faktur-iklan')?.scrollIntoView({ behavior: 'smooth' })"
                            class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 active:scale-95 text-slate-200 hover:text-white text-xs font-bold rounded-xl border border-slate-600 transition flex items-center gap-2 shadow-xs cursor-pointer">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        <span>Pilihan 1: Pilih Manual di Tabel</span>
                    </button>

                    <!-- Pilihan 2: Barcode -->
                    <button type="button" 
                            @click="openScanModal()" 
                            class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 active:scale-95 text-white text-xs font-extrabold rounded-xl shadow-lg shadow-blue-900/40 hover:shadow-blue-900/60 transition flex items-center gap-2.5 cursor-pointer ring-2 ring-blue-400/30">
                        <svg class="w-4 h-4 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        <span>Pilihan 2: Aksi Scan Barcode</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] bg-white/20 font-mono font-bold tracking-wider">F2</span>
                    </button>
                </div>
            </div>

            <!-- Quick Barcode Input Strip -->
            <div class="mt-3.5 pt-3 border-t border-slate-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs text-slate-300">
                    <span class="w-2 h-2 rounded-full bg-blue-400 animate-ping"></span>
                    <span class="font-medium">Scanner Siap:</span>
                    <span class="text-slate-400">Tembak laser barcode scanner atau ketik no faktur langsung di bawah:</span>
                </div>
                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                    <label class="inline-flex items-center gap-1.5 cursor-pointer text-[11px] text-blue-300 font-semibold bg-blue-900/60 hover:bg-blue-900/90 px-2.5 py-1.5 rounded-lg border border-blue-500/40 select-none transition">
                        <input type="checkbox" x-model="autoProcess" class="rounded border-blue-400 text-blue-500 focus:ring-0 w-3.5 h-3.5">
                        <span>⚡ Auto-Proses Langsung</span>
                    </label>
                    <div class="relative flex-1 sm:w-64">
                        <input type="text" 
                               x-model="scanInput" 
                               @keydown.enter.prevent="processBarcode(scanInput)" 
                               placeholder="Tembak barcode faktur (Enter)..." 
                               class="w-full text-xs font-mono py-1.5 pl-8 pr-3 rounded-lg bg-slate-950/70 border border-slate-600 focus:border-blue-400 focus:ring-1 focus:ring-blue-400 text-white placeholder-slate-500">
                        <svg class="w-3.5 h-3.5 text-blue-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </div>
                    <button type="button" 
                            @click="processBarcode(scanInput)" 
                            :disabled="isScanning"
                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 active:scale-95 text-white text-xs font-bold rounded-lg transition shrink-0 cursor-pointer disabled:opacity-50">
                        <span x-show="!isScanning">Scan</span>
                        <span x-show="isScanning">Mencari...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar Komprehensif (OripioFin Style) -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm shadow-slate-100 space-y-4" x-data="{ 
            periode: '{{ $periodeTipe ?? 'semua' }}'
        }">
            <form method="GET" action="{{ route('kasir.penerimaan.iklan') }}" class="space-y-4">
                <!-- Baris 1: Filter Status Piutang (Pill Filter OripioFin) -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3.5 border-b border-slate-100">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5 mr-1">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            Status Tagihan:
                        </span>
                        <div class="inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200/80 text-xs">
                            <label class="cursor-pointer px-3.5 py-1.5 rounded-lg font-medium transition-all {{ ($statusPiutang ?? 'belum_lunas') === 'belum_lunas' ? 'bg-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="status_piutang" value="belum_lunas" {{ ($statusPiutang ?? 'belum_lunas') === 'belum_lunas' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                                <span>Hanya yang Punya Piutang (Belum Lunas)</span>
                            </label>
                            <label class="cursor-pointer px-3.5 py-1.5 rounded-lg font-medium transition-all {{ ($statusPiutang ?? '') === 'lunas' ? 'bg-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="status_piutang" value="lunas" {{ ($statusPiutang ?? '') === 'lunas' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                                <span>Sudah Lunas</span>
                            </label>
                            <label class="cursor-pointer px-3.5 py-1.5 rounded-lg font-medium transition-all {{ ($statusPiutang ?? '') === 'semua' ? 'bg-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="status_piutang" value="semua" {{ ($statusPiutang ?? '') === 'semua' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                                <span>Semua Status</span>
                            </label>
                        </div>
                    </div>

                    <!-- Indikator Filter Aktif & Reset -->
                    @if(($statusPiutang ?? 'belum_lunas') !== 'belum_lunas' || ($periodeTipe ?? 'semua') !== 'semua' || (($saluranMedia ?? 'semua') !== 'semua' && ($saluranMedia ?? 'semua') !== 'all') || !empty($search))
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-lg font-medium flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                Filter Aktif
                            </span>
                            <a href="{{ route('kasir.penerimaan.iklan') }}" class="text-xs text-rose-600 hover:text-rose-700 font-semibold hover:underline flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reset Filter
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Baris 2: Kontrol Filter Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <!-- Quick Search Field -->
                    <div class="sm:col-span-4">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Pencarian Cepat</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search ?? '' }}" 
                                   placeholder="No. Faktur, Klien, atau Tanggal Muat..." 
                                   class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50/70 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 placeholder-slate-400 transition-all">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    <!-- Saluran Media -->
                    <div class="sm:col-span-3">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Saluran Media</label>
                        <select name="saluran_media" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 bg-white">
                            <option value="semua" {{ in_array(($saluranMedia ?? 'semua'), ['semua', 'all', '']) ? 'selected' : '' }}>Semua Saluran Media</option>
                            <option value="koran" {{ ($saluranMedia ?? '') === 'koran' ? 'selected' : '' }}>Koran Cetak</option>
                            <option value="online" {{ ($saluranMedia ?? '') === 'online' ? 'selected' : '' }}>Media Online</option>
                            <option value="tv" {{ ($saluranMedia ?? '') === 'tv' ? 'selected' : '' }}>Priangan TV</option>
                        </select>
                    </div>

                    <!-- Tipe Periode Waktu -->
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Periode Waktu</label>
                        <select name="periode_tipe" x-model="periode" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 bg-white">
                            <option value="semua">Semua Waktu</option>
                            <option value="hari">Per Hari (Tanggal)</option>
                            <option value="bulan">Per Bulan</option>
                            <option value="tahun">Per Tahun</option>
                        </select>
                    </div>

                    <!-- Dynamic Input Tanggal / Bulan / Tahun + Tombol Terapkan -->
                    <div class="sm:col-span-3 flex items-center gap-2">
                        <!-- Per Hari: Input Tanggal Spesifik -->
                        <div x-show="periode === 'hari'" class="w-full">
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Pilih Tanggal</label>
                            <input type="date" name="tanggal" value="{{ $tanggalFilter ?? date('Y-m-d') }}" :disabled="periode !== 'hari'" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800">
                        </div>

                        <!-- Per Bulan: Pilih Bulan & Tahun -->
                        <div x-show="periode === 'bulan'" class="w-full grid grid-cols-2 gap-1.5">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Bulan</label>
                                <select name="bulan" :disabled="periode !== 'bulan'" class="w-full py-2 px-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 bg-white">
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
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tahun</label>
                                <select name="tahun" :disabled="periode !== 'bulan'" class="w-full py-2 px-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 bg-white">
                                    @for($y = (int)date('Y') + 1; $y >= 2020; $y--)
                                        <option value="{{ $y }}" {{ (int)($tahunFilter ?? date('Y')) === $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- Per Tahun: Pilih Tahun Saja -->
                        <div x-show="periode === 'tahun'" class="w-full">
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Pilih Tahun</label>
                            <select name="tahun" :disabled="periode !== 'tahun'" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 bg-white">
                                @for($y = (int)date('Y') + 1; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ (int)($tahunFilter ?? date('Y')) === $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Tombol Terapkan Filter -->
                        <div :class="periode === 'semua' ? 'w-full' : 'shrink-0'">
                            <label class="block text-[11px] font-semibold text-transparent mb-1 select-none">Aksi</label>
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold text-xs py-2 px-4 rounded-xl transition shadow-sm shadow-blue-500/20 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                <span>Terapkan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Faktur & Piutang Iklan -->
        <div id="tabel-faktur-iklan" class="bg-white border border-slate-200/80 rounded-2xl shadow-sm shadow-slate-100 overflow-hidden scroll-mt-6">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                        Daftar Faktur Iklan & Status Piutang
                    </h3>
                </div>
                <span class="text-[11px] text-slate-500 font-medium">Total {{ $fakturList->count() }} Faktur Ditemukan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">No. Faktur</th>
                            <th class="px-4 py-3 font-semibold">Saluran Media</th>
                            <th class="px-4 py-3 font-semibold">Nama Pemasang / Klien</th>
                            <th class="px-4 py-3 font-semibold">Sales / AE</th>
                            <th class="px-4 py-3 font-semibold text-right">Total Tagihan</th>
                            <th class="px-4 py-3 font-semibold text-right">Sudah Dibayar</th>
                            <th class="px-4 py-3 font-semibold text-right">Sisa Piutang</th>
                            <th class="px-4 py-3 font-semibold text-center">Status Komisi</th>
                            <th class="px-4 py-3 font-semibold text-center">Aksi / Bayar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($fakturList as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Kolom 1: No. Faktur & Sub-teks Tanggal Muat -->
                            <td class="px-4 py-3 align-top">
                                <span class="font-bold text-slate-800 font-mono">{{ $item->no_faktur }}</span>
                                <span class="block text-[11px] text-slate-400 mt-0.5 font-medium">
                                    Muat: {{ \Carbon\Carbon::parse($item->tanggal_muat)->format('d/m/Y') }}
                                </span>
                            </td>

                            <!-- Kolom 2: Saluran Media -->
                            <td class="px-4 py-3 align-top whitespace-nowrap">
                                @if($item->type === 'koran')
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-sky-50 text-sky-700 border border-sky-200">
                                        Koran Cetak
                                    </span>
                                @elseif($item->type === 'online')
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Media Online
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-purple-50 text-purple-700 border border-purple-200">
                                        Priangan TV
                                    </span>
                                @endif
                            </td>

                            <!-- Kolom 3: Nama Pemasang / Klien & Kode Akun & Periode Tayang -->
                            <td class="px-4 py-3 align-top">
                                <span class="font-bold text-slate-900 block">{{ $item->nama_klien }}</span>
                                <span class="text-[11px] font-semibold text-slate-500 font-mono tracking-wide">{{ $item->kode_rekening ?? 'AG-01-xxx' }}</span>
                                @if(!empty($item->deskripsi_tayang))
                                    <span class="block text-[11px] text-slate-400 mt-0.5 font-medium">{{ $item->deskripsi_tayang }}</span>
                                @endif
                            </td>

                            <!-- Kolom 4: Sales / AE -->
                            <td class="px-4 py-3 align-top text-slate-600 whitespace-nowrap">
                                <span class="font-medium">{{ $item->sales }}</span>
                            </td>

                            <!-- Kolom 5: Total Tagihan -->
                            <td class="px-4 py-3 align-top text-right font-medium text-slate-700 whitespace-nowrap">
                                Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                            </td>

                            <!-- Kolom 6: Sudah Dibayar -->
                            <td class="px-4 py-3 align-top text-right font-bold text-emerald-600 whitespace-nowrap">
                                Rp {{ number_format($item->sudah_dibayar, 0, ',', '.') }}
                            </td>

                            <!-- Kolom 7: Sisa Piutang -->
                            <td class="px-4 py-3 align-top text-right whitespace-nowrap">
                                @if($item->sisa_piutang > 0)
                                    <span class="text-rose-600 font-bold">
                                        Rp {{ number_format($item->sisa_piutang, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-slate-400 font-medium">
                                        Rp 0
                                    </span>
                                @endif
                            </td>

                            <!-- Kolom 8: Status Komisi -->
                            <td class="px-4 py-3 align-top text-center whitespace-nowrap">
                                @if($item->siap_cair)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Siap Cair KH
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-medium rounded-full bg-slate-100 text-slate-500 border border-slate-200">
                                        Terkunci (Belum Lunas)
                                    </span>
                                @endif
                            </td>

                            <!-- Kolom 9: Aksi / Bayar -->
                            <td class="px-4 py-3 align-top text-center whitespace-nowrap">
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
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold text-[11px] rounded-xl transition shadow-xs shadow-blue-500/20 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <span>Proses BKM</span>
                                    </button>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 cursor-not-allowed opacity-90">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Lunas</span>
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-5 py-8 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-1">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="font-medium text-slate-500">Tidak ada data faktur iklan yang sesuai dengan filter pencarian.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form Input Setoran Kas / Terbitkan BKM -->
        <div x-cloak x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showModal = false" class="bg-white rounded-2xl max-w-xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200/80 overflow-hidden">
                <!-- Header (shrink-0) -->
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 shrink-0 bg-slate-50/50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Terbitkan Bukti Kas Masuk (BKM)</h4>
                            <p class="text-[11px] text-slate-500">Pencatatan kas masuk & pelunasan faktur piutang iklan</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="showModal = false; openScanModal()" class="px-2.5 py-1 text-[11px] bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-700 font-semibold rounded-lg border border-slate-200 transition flex items-center gap-1 cursor-pointer active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <span>Ganti via Scan</span>
                        </button>
                        <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
                    </div>
                </div>

                <form id="form-bayar-iklan" method="POST" action="{{ route('kasir.penerimaan.iklan.store') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="type" :value="bayarForm.type">
                    <input type="hidden" name="id" :value="bayarForm.id">

                    <!-- Scrollable Body (flex-1 overflow-y-auto) -->
                    <div class="flex-1 overflow-y-auto px-5 py-3 space-y-3">
                        <!-- Info Tagihan -->
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-slate-500">No. Faktur:</span>
                                <span class="font-mono font-bold text-slate-900" x-text="bayarForm.faktur"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Pemasang / Klien:</span>
                                <span class="font-bold text-slate-800" x-text="bayarForm.customer"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Sisa Piutang Saat Ini:</span>
                                <span class="font-bold text-rose-600">Rp <span x-text="new Intl.NumberFormat('id-ID').format(bayarForm.sisa)"></span></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Tanggal Muat:</span>
                                <span class="font-bold text-slate-800" x-text="bayarForm.tanggalMuat"></span>
                            </div>
                        </div>

                        <!-- Identitas Form Divisi Iklan -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 p-3 bg-slate-50/80 rounded-xl border border-slate-200 text-xs">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-0.5">Divisi</label>
                                <div class="py-1.5 px-3 bg-white rounded-lg border border-slate-200 font-bold text-slate-800 flex items-center justify-between text-xs">
                                    <span>Divisi Iklan</span>
                                    <span class="text-[9px] px-1.5 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded font-sans font-bold">KAS</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-0.5">Kode Perkiraan (COA)</label>
                                <select name="kode_perkiraan" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-slate-200 font-mono font-semibold text-slate-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 bg-white">
                                    <option value="1.00.04.01.01.01">1.00.04.01.01.01 (Piutang Iklan)</option>
                                    <option value="2.00.01.01.01.01">2.00.01.01.01.01 (Uang Muka/Titipan)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-0.5">No. Blanko Fisik (Opsional)</label>
                                <input type="text" name="no_blanko" placeholder="Contoh: BLK-01982" 
                                       class="w-full text-xs py-1.5 px-3 rounded-lg border border-slate-200 font-mono text-slate-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 bg-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <!-- Petugas Inkaso Dropdown -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Petugas Inkaso / Penerima</label>
                                <select name="petugas_inkaso" x-model="bayarForm.petugasInkaso" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 bg-white">
                                    @foreach($petugasInkasoList as $petugas)
                                        <option value="{{ $petugas->name }}">{{ $petugas->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Metode Pembayaran -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pembayaran</label>
                                <select name="metode_pembayaran" x-model="bayarForm.metode" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 bg-white">
                                    <option value="tunai">Tunai Kasir (Fisik Meja)</option>
                                    <option value="transfer">Transfer Bank BJB / Lainnya</option>
                                    <option value="qris">QRIS / E-Wallet Resmi</option>
                                </select>
                            </div>
                        </div>

                        <!-- Input Khusus Transfer -->
                        <div x-show="bayarForm.metode === 'transfer'" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 p-3 bg-blue-50/50 rounded-xl border border-blue-100">
                            <div>
                                <label class="block text-[11px] font-bold text-blue-900 mb-0.5">Bank Tujuan Rekening</label>
                                <select name="bank_tujuan" x-model="bayarForm.bankTujuan" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-blue-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 bg-white">
                                    <option value="Bank BJB Cabang Tasikmalaya">Bank BJB (Giro: 0012-0034-5678)</option>
                                    <option value="Bank Mandiri Tasikmalaya">Bank Mandiri (Giro: 131-00-9876543-2)</option>
                                    <option value="Bank BCA Tasikmalaya">Bank BCA (Giro: 054-8899-771)</option>
                                    <option value="Bank BRI Tasikmalaya">Bank BRI (Giro: 0100-01-002345-30-1)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-blue-900 mb-0.5">No. Bukti / Referensi Transfer</label>
                                <input type="text" name="no_referensi_transfer" x-model="bayarForm.noRef" placeholder="Contoh: TRF-BJB-984128" 
                                       class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-blue-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 font-mono bg-white">
                            </div>
                        </div>

                        <!-- Nominal Tagihan / Bruto Faktur -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Tagihan / Faktur yang Dibayar (Rp)</label>
                            <input type="number" name="nominal_bayar" x-model="bayarForm.nominal" @input="recalculateTax()" required min="1" 
                                   class="w-full text-base font-mono font-bold py-2.5 px-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-900 bg-slate-50/50">
                            <span class="text-[10px] text-slate-500 mt-0.5 block">Diinput bulat sesuai nilai faktur (KI mencatat nilai bruto faktur secara utuh)</span>
                        </div>

                        <!-- Checkbox Toggle: Potongan Pajak Instansi Pemerintah (PPN 11% & PPh 23 2%) -->
                        <div class="p-3 bg-amber-50/70 border border-amber-200 rounded-xl space-y-2.5">
                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input type="checkbox" name="is_instansi_tax" value="1" x-model="bayarForm.isInstansiTax" @change="recalculateTax()" 
                                       class="mt-0.5 rounded border-amber-300 text-amber-600 focus:ring-amber-500 w-4 h-4">
                                <div class="text-xs">
                                    <span class="font-bold text-amber-950 block">Potong Pajak Dinas / Instansi Pemerintah (WAPU)</span>
                                    <span class="text-[11px] text-amber-800">DPRD, Pemkot, Setda, Bapenda, Diskominfo (Kewajiban potong PPN 11% & PPh 23 2% dari DPP)</span>
                                </div>
                            </label>

                            <!-- Breakdown Pajak Otomatis jika dicentang -->
                            <div x-show="bayarForm.isInstansiTax" x-transition class="pt-2 border-t border-amber-200/80 space-y-2 text-xs">
                                <div class="grid grid-cols-3 gap-2 bg-white p-2.5 rounded-lg border border-amber-200/60 font-mono">
                                    <div>
                                        <span class="text-[10px] text-slate-500 block">DPP (Dasar Pajak)</span>
                                        <span class="font-bold text-slate-800">Rp <span x-text="new Intl.NumberFormat('id-ID').format(bayarForm.dpp)"></span></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-amber-700 block">PPN 11%</span>
                                        <span class="font-bold text-amber-700">Rp <span x-text="new Intl.NumberFormat('id-ID').format(bayarForm.ppn11)"></span></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-rose-700 block">PPh 23 (2%)</span>
                                        <span class="font-bold text-rose-700">Rp <span x-text="new Intl.NumberFormat('id-ID').format(bayarForm.pph23)"></span></span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between px-1 text-xs">
                                    <span class="text-amber-900 font-semibold">Total Potongan Beban Pajak:</span>
                                    <span class="font-bold font-mono text-rose-700">Rp <span x-text="new Intl.NumberFormat('id-ID').format(bayarForm.potonganPajak)"></span></span>
                                </div>
                                <div class="p-2.5 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center justify-between text-xs">
                                    <span class="font-bold text-emerald-900 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        Uang Fisik Diterima Kasir:
                                    </span>
                                    <span class="font-mono font-black text-sm text-emerald-700">Rp <span x-text="new Intl.NumberFormat('id-ID').format(uangFisikBersih)"></span></span>
                                </div>
                                <p class="text-[10px] text-amber-700 italic flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                    <span>Sistem otomatis menerbitkan <b>Kas Keluar (KO) Penyeimbang</b> akun Beban Pajak agar kas fisik kasir tetap balance dengan pembukuan.</span>
                                </p>
                            </div>
                        </div>

                        <!-- Potongan Pajak Manual jika Non-Instansi -->
                        <div x-show="!bayarForm.isInstansiTax">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Potongan Pajak Lainnya / Manual (Rp)</label>
                            <input type="number" name="potongan_pajak" x-model="bayarForm.potonganPajak" min="0" placeholder="0" 
                                   class="w-full text-sm font-mono font-bold py-2 px-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-900">
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Diisi jika terdapat potongan pajak non-standar instansi</span>
                        </div>

                        <!-- Live Calculation Preview -->
                        <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl text-xs space-y-1">
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Sisa Piutang Setelah Transaksi Ini:</span>
                                <span class="font-bold font-mono text-sm" :class="sisaSetelahBayar === 0 ? 'text-emerald-600' : 'text-amber-600'">
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(sisaSetelahBayar)"></span>
                                    <span class="text-[10px] ml-1 font-semibold" x-text="sisaSetelahBayar === 0 ? '(LUNAS - Siap Cair Komisi)' : '(Sisa Piutang)'"></span>
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan Tambahan (Opsional)</label>
                            <input type="text" name="keterangan" x-model="bayarForm.keterangan" placeholder="Catatan bukti kas masuk..." 
                                   class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-900">
                        </div>
                    </div>

                    <!-- Footer Action Buttons (shrink-0) -->
                    <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl transition active:scale-95">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold text-xs rounded-xl transition shadow-xs shadow-blue-500/20">
                            Simpan & Terbitkan BKM
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <!-- Modal Cetak Kuitansi / Bukti Kas Masuk (BKM) Otomatis -->
        @if(session('cetak_bkm'))
        @php $bkm = session('cetak_bkm'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200/80 overflow-hidden">
                <!-- Header Kuitansi -->
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-slate-50/50">
                    <div>
                        <div class="text-base font-black tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500">PT. Priangan Berkah Media - Bagian Perkasiran</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 text-[10px] font-black rounded-md bg-blue-50 text-blue-700 uppercase tracking-widest border border-blue-200">
                            BUKTI KAS MASUK (BKM)
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1">{{ $bkm['no_bkm'] }}</div>
                        <div class="text-[10px] text-slate-500">{{ $bkm['tanggal'] }}</div>
                    </div>
                </div>

                <!-- Body Kuitansi (Scrollable) -->
                <div class="print-document-body flex-1 overflow-y-auto p-5 space-y-2.5 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Telah Diterima Dari</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $bkm['customer'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Nomor Faktur / Ref</span>
                        <span class="col-span-2 font-mono font-bold text-slate-900">: {{ $bkm['faktur'] }}</span>
                    </div>
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
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Petugas Inkaso / Kolektor</span>
                        <span class="col-span-2 font-semibold text-slate-800">: {{ $bkm['penagih'] }}</span>
                    </div>
                    @if(($bkm['is_instansi_tax'] ?? false) || ($bkm['pajak'] ?? 0) > 0)
                    <div class="py-2 px-3 bg-amber-50 rounded-lg border border-amber-200 text-xs space-y-1 my-1">
                        <div class="font-bold text-amber-900 flex items-center justify-between">
                            <span>Perlakuan Potongan Pajak Dinas (WAPU):</span>
                            @if(!empty($bkm['no_bkk_pajak']))
                                <span class="font-mono text-[10px] text-amber-800 bg-amber-100 px-1.5 py-0.5 rounded">KO Penyeimbang: {{ $bkm['no_bkk_pajak'] }}</span>
                            @endif
                        </div>
                        @if(!empty($bkm['dpp']))
                        <div class="grid grid-cols-3 gap-1 text-[11px] text-amber-900 font-mono pt-0.5">
                            <div>DPP: Rp {{ number_format($bkm['dpp'], 0, ',', '.') }}</div>
                            <div>PPN 11%: Rp {{ number_format($bkm['ppn'], 0, ',', '.') }}</div>
                            <div>PPh 23: Rp {{ number_format($bkm['pph23'], 0, ',', '.') }}</div>
                        </div>
                        @endif
                        <div class="text-[10px] text-slate-600 pt-0.5">
                            Total Faktur (KI Bruto): Rp {{ number_format($bkm['nominal'], 0, ',', '.') }} • Potongan Pajak: Rp {{ number_format($bkm['pajak'], 0, ',', '.') }}
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-3 py-1.5 bg-blue-50/70 px-2.5 rounded-lg border border-blue-200/60">
                        <span class="font-bold text-blue-900">Uang Fisik Diterima</span>
                        <span class="col-span-2 font-mono font-black text-sm text-blue-800">: Rp {{ number_format($bkm['nominal_fisik'] ?? $bkm['nominal'], 0, ',', '.') }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-1 text-xs">
                        <span class="text-slate-500">Terbilang Huruf</span>
                        <span class="col-span-2 font-semibold italic text-slate-900">: {{ $bkm['terbilang'] }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-1">
                        <span class="text-slate-500">Status Pembayaran</span>
                        <span class="col-span-2 font-bold {{ $bkm['status_bayar'] === 'LUNAS' ? 'text-emerald-600' : 'text-amber-600' }}">
                            : {{ $bkm['status_bayar'] }} 
                            @if($bkm['sisa_piutang'] > 0)
                                (Sisa Piutang: Rp {{ number_format($bkm['sisa_piutang'], 0, ',', '.') }})
                            @endif
                        </span>
                    </div>

                    <!-- Tanda Tangan Cetak -->
                    <div class="grid grid-cols-2 pt-4 border-t border-slate-200 text-center text-[11px] text-slate-600">
                        <div>
                            <div>Penyetor / Klien</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900">({{ $bkm['customer'] }})</div>
                        </div>
                        <div>
                            <div>Kasir Penerima</div>
                            <div class="h-10"></div>
                            <div class="font-bold text-slate-900 font-mono">({{ $bkm['kasir'] }})</div>
                        </div>
                    </div>
                </div>

                <!-- Action Button Print & Close (Pinned Bottom) -->
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition active:scale-95">
                        Tutup
                    </button>
                    @if(!empty($bkm['no_bkk_pajak']))
                    <a href="{{ route('kasir.pengeluaran.realisasi-bp') }}?search={{ $bkm['no_bkk_pajak'] }}" 
                       class="px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-1.5 active:scale-95">
                        <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Lihat & Cetak KO Pajak ({{ $bkm['no_bkk_pajak'] }})</span>
                    </a>
                    @endif
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs rounded-xl transition shadow-xs shadow-blue-500/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Modal Aksi Scan Barcode Faktur Iklan -->
        <div x-cloak x-show="showScanModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="closeScanModal()" class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
                <!-- Header Modal Scanner -->
                <div class="flex items-center justify-between px-5 py-3.5 bg-gradient-to-r from-slate-900 via-slate-800 to-blue-950 text-white">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-blue-500/20 text-blue-300 border border-blue-400/30 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-white">Aksi Scan Barcode Faktur Iklan</h4>
                            <p class="text-[11px] text-blue-200">Arahkan scanner ke barcode faktur / SPPI</p>
                        </div>
                    </div>
                    <button type="button" @click="closeScanModal()" class="text-slate-400 hover:text-white font-bold text-lg leading-none cursor-pointer">&times;</button>
                </div>

                <!-- Body Modal Scanner -->
                <div class="p-5 space-y-4">
                    <!-- Toggle Mode Auto-Submit -->
                    <div class="p-3 bg-gradient-to-r from-blue-50 via-indigo-50 to-slate-50 dark:from-slate-900 dark:via-blue-950/40 dark:to-slate-900 border border-blue-100 dark:border-blue-900/40 rounded-xl flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                                    <span>Input & Proses Otomatis Saat Scan</span>
                                    <span class="px-1.5 py-0.5 bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-200 text-[9px] font-bold rounded">Auto-Submit</span>
                                </div>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">Scan barcode faktur langsung menyimpan setoran & membuka cetak BKM</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="autoProcess" class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Scanner Laser Input Area -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">Kode Barcode / Nomor Faktur</label>
                        <div class="relative">
                            <input type="text" 
                                   x-ref="barcodeInput"
                                   x-model="scanInput" 
                                   @keydown.enter.prevent="processBarcode(scanInput)" 
                                   placeholder="Tembak scanner di sini atau ketik nomor faktur..." 
                                   class="w-full text-base font-mono font-bold py-2.5 pl-10 pr-20 rounded-xl border-2 border-blue-500 focus:ring-2 focus:ring-blue-200 focus:border-blue-600 text-slate-900 bg-blue-50/20">
                            <svg class="w-5 h-5 text-blue-600 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <button type="button" 
                                    @click="processBarcode(scanInput)" 
                                    :disabled="isScanning"
                                    class="absolute right-1.5 top-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs rounded-lg transition disabled:opacity-50 cursor-pointer">
                                <span x-show="!isScanning">Proses</span>
                                <span x-show="isScanning">Mencari...</span>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                            <span>Mendukung alat scanner laser USB/Bluetooth otomatis (tekan Enter otomatis).</span>
                        </p>
                    </div>

                    <!-- Error Alert -->
                    <div x-show="scanError" x-transition class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs font-semibold text-rose-700 flex items-center justify-between">
                        <span x-text="scanError"></span>
                        <button type="button" @click="scanError = ''" class="text-rose-500 font-bold">&times;</button>
                    </div>

                    <!-- Pilihan Scan Kamera & Unggah Gambar -->
                    <div class="border border-slate-200 rounded-xl p-3 bg-slate-50/70 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Kamera Web / HP (Live Scan)</span>
                            </span>
                            <button type="button" 
                                    @click="cameraActive ? stopCamera() : startCamera()" 
                                    class="px-3 py-1.5 text-xs font-bold rounded-lg border transition cursor-pointer flex items-center gap-1.5 active:scale-95"
                                    :class="cameraActive ? 'bg-rose-100 text-rose-700 border-rose-300' : 'bg-blue-600 text-white border-blue-600 hover:bg-blue-700 shadow-2xs'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span x-text="cameraActive ? 'Matikan Kamera' : 'Nyalakan Kamera'"></span>
                            </button>
                        </div>

                        <!-- Live Camera Viewfinder (Html5Qrcode) -->
                        <div x-show="cameraActive" class="rounded-xl overflow-hidden bg-slate-900 p-2 shadow-inner">
                            <div id="reader-camera-iklan" class="w-full overflow-hidden rounded-lg"></div>
                            <p class="text-[11px] text-blue-300 py-1.5 text-center font-medium animate-pulse">
                                Arahkan barcode atau QR Code ke dalam kotak scanner di atas
                            </p>
                        </div>

                        <!-- Opsi Scan Gambar File / Screenshot / Paste Ctrl+V -->
                        <div class="pt-2 border-t border-slate-200/80 flex items-center justify-between text-xs">
                            <span class="text-slate-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Punya file foto / screenshot?</span>
                            </span>
                            <label class="px-2.5 py-1 font-semibold rounded-lg bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 cursor-pointer transition shadow-2xs flex items-center gap-1 active:scale-95">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <span>Pilih Gambar Barcode</span>
                                <input type="file" accept="image/*" @change="scanUploadedImage($event)" class="hidden">
                            </label>
                        </div>
                        <div id="temp-reader-iklan" style="display:none;"></div>
                    </div>

                    <!-- Quick Click Simulation Barcodes -->
                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">
                            Simulasi / Contoh Barcode Faktur (Klik Langsung untuk Uji):
                        </span>
                        <div class="flex flex-wrap gap-1.5">
                            <!-- Barcode Faktur Gambar Pengguna (FKPTV002) -->
                            <button type="button" 
                                    @click="processBarcode('FKPTV002')" 
                                    class="px-2.5 py-1 text-[11px] font-mono font-bold rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 transition cursor-pointer flex items-center gap-1 shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                <span>FKPTV002</span>
                                <span class="text-[9px] text-amber-700 font-sans font-medium">(PT Surya Madistrindo - Priangan TV)</span>
                            </button>

                            @forelse($fakturList->take(12) as $f)
                            @if($f->no_faktur !== 'FKPTV002')
                            <button type="button" 
                                    @click="processBarcode('{{ $f->no_faktur }}')" 
                                    class="px-2.5 py-1 text-[11px] font-mono font-semibold rounded-lg bg-slate-100 hover:bg-teal-100 hover:text-teal-900 border border-slate-200 transition cursor-pointer flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full {{ $f->sisa_piutang > 0 ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                <span>{{ $f->no_faktur }}</span>
                                <span class="text-[9px] text-slate-400 font-sans">({{ $f->nama_klien }})</span>
                            </button>
                            @endif
                            @empty
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Tekan <b>Esc</b> untuk membatalkan</span>
                    <button type="button" @click="closeScanModal()" class="px-3.5 py-1.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold rounded-lg transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- Overlay Loading Saat Auto-Submit -->
        <div x-cloak x-show="isAutoSubmitting" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl border border-slate-100 flex flex-col items-center text-center space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-teal-50 border-2 border-teal-500 text-teal-600 flex items-center justify-center animate-bounce shadow-md shadow-teal-500/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Barcode Berhasil Dipindai!</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Memproses input setoran secara otomatis ke Buku Kas...</p>
                </div>
                <div class="w-full bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs space-y-1.5 font-mono text-left">
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-sans">No. Faktur:</span>
                        <span class="font-bold text-slate-900" x-text="autoSubmitInfo.faktur"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-sans">Klien/Pemasang:</span>
                        <span class="font-bold text-slate-800 truncate max-w-[180px]" x-text="autoSubmitInfo.customer"></span>
                    </div>
                    <div class="flex justify-between border-t border-slate-200 pt-1">
                        <span class="text-slate-500 font-sans">Nominal Setoran:</span>
                        <span class="font-bold text-teal-600">Rp <span x-text="new Intl.NumberFormat('id-ID').format(autoSubmitInfo.nominal)"></span></span>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs text-teal-700 font-semibold">
                    <svg class="w-4 h-4 animate-spin text-teal-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <span>Menerbitkan Bukti Kas Masuk (BKM)...</span>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
