<x-app-layout>
    <script>
    function kasirIklanApp() {
        return {
            showModal: false, 
            showScanModal: false,
            scanInput: '',
            isScanning: false,
            scanError: '',
            scanErrorTitle: '',
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
                this.scanErrorTitle = '';
                this.$nextTick(async () => {
                    try {
                        if (typeof Html5Qrcode === 'undefined') {
                            this.scanErrorTitle = 'Pustaka Kamera Belum Siap';
                            this.scanError = 'Pustaka pemindai barcode belum selesai dimuat. Silakan muat ulang (refresh) browser.';
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
                        
                        // 1. Ambil daftar kamera perangkat (Kompatibel dengan Webcam Laptop & HP)
                        let cameraIdOrConfig = { facingMode: 'user' };
                        try {
                            const cameras = await Html5Qrcode.getCameras();
                            if (cameras && cameras.length > 0) {
                                const backCamera = cameras.find(c => {
                                    const lbl = (c.label || '').toLowerCase();
                                    return lbl.includes('back') || lbl.includes('rear') || lbl.includes('belakang');
                                });
                                cameraIdOrConfig = backCamera ? backCamera.id : cameras[0].id;
                            }
                        } catch (camErr) {
                            console.warn('Enumerasi kamera via getCameras():', camErr);
                            cameraIdOrConfig = { facingMode: 'environment' };
                        }

                        // 2. Jalankan scanner kamera
                        try {
                            await this.html5QrScanner.start(
                                cameraIdOrConfig,
                                config,
                                (decodedText) => {
                                    this.playBeep();
                                    this.stopCamera();
                                    this.processBarcode(decodedText);
                                },
                                (err) => {}
                            );
                        } catch (startErr) {
                            if (typeof cameraIdOrConfig === 'string') {
                                await this.html5QrScanner.start(
                                    { facingMode: 'user' },
                                    config,
                                    (decodedText) => {
                                        this.playBeep();
                                        this.stopCamera();
                                        this.processBarcode(decodedText);
                                    },
                                    (err) => {}
                                );
                            } else {
                                throw startErr;
                            }
                        }
                    } catch (e) {
                        const errStr = (e.message || e.name || String(e)).toLowerCase();
                        if (errStr.includes('notallowed') || errStr.includes('permission') || errStr.includes('denied')) {
                            this.scanErrorTitle = 'Izin Akses Kamera Ditolak / Diblokir Browser';
                            this.scanError = 'Browser Google Chrome memblokir akses webcam/kamera untuk alamat <strong>' + window.location.host + '</strong>.<br><br>' +
                                '<strong>Cara Mengaktifkan Kembali:</strong><br>' +
                                '<ol class="list-decimal pl-4 space-y-1 mt-1 text-slate-700">' +
                                '<li>Lihat <strong>Address Bar</strong> (kolom alamat website di bagian paling atas browser).</li>' +
                                '<li>Klik ikon <strong>Setelan Situs / Tune</strong> (ikon slider/sakelar di sebelah kiri alamat <code>' + window.location.host + '</code>).</li>' +
                                '<li>Cari opsi <strong>Kamera (Camera)</strong> lalu ubah dari <strong>Blokir (Block)</strong> menjadi <strong>Izinkan (Allow)</strong>.</li>' +
                                '<li>Muat ulang halaman browser (tekan <strong>F5</strong> atau tombol Reload).</li>' +
                                '</ol><br>' +
                                '<div class="p-2 bg-sky-50 border border-sky-200 rounded-lg text-sky-900 font-medium">' +
                                '💡 <strong>Alternatif Cepat:</strong> Anda tetap bisa memproses setoran dengan mengetik Nomor Faktur di atas lalu tekan <strong>Enter/Proses</strong>, menembak dengan <strong>Scanner Barcode USB</strong>, atau klik <strong>Pilih Gambar QR</strong>.' +
                                '</div>';
                        } else if (errStr.includes('notfound') || errStr.includes('device not found') || errStr.includes('requested device not found')) {
                            this.scanErrorTitle = 'Perangkat Kamera Tidak Ditemukan';
                            this.scanError = 'Tidak ada perangkat webcam atau kamera yang terhubung pada komputer ini. Silakan sambungkan webcam atau gunakan input nomor faktur / scanner laser USB.';
                        } else {
                            this.scanErrorTitle = 'Gagal Mengakses Kamera';
                            this.scanError = 'Kamera tidak dapat dinyalakan: ' + (e.message || e) + '. Pastikan kamera tidak sedang digunakan oleh aplikasi lain (seperti Zoom, Teams, Meet).';
                        }
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
    <script>
        if (typeof Html5Qrcode === 'undefined') {
            document.write('<script src="{{ asset('js/html5-qrcode.min.js') }}"><\/script>');
        }
    </script>

    <div class="space-y-5 pb-10" x-data="kasirIklanApp()">

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
                    <thead class="bg-emerald-700 border-b border-emerald-800 text-white font-semibold text-xs whitespace-nowrap">
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
        </div>

        <!-- Modal Cetak Kuitansi / Bukti Kas Masuk (BKM) - Format Continuous Form Dot Matrix -->
        @if(session('cetak_bkm'))
        @php
            $bkm = session('cetak_bkm');
            $metodeBkm = strtoupper((string) ($bkm['metode'] ?? 'TUNAI'));
            $bankBkm = trim((string) ($bkm['bank'] ?? '-'));
            $tampilBankBkm = ($metodeBkm !== 'TUNAI' && $bankBkm !== '' && $bankBkm !== '-');
            $refKsBkm = trim((string) ($bkm['no_kuitansi_sementara'] ?? ''));
            $tampilRefKs = ($refKsBkm !== '' && $refKsBkm !== '-');
        @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="max-w-4xl w-full max-h-[92vh] flex flex-col">
                <!-- Action Bar Atas (Persis Foto ke-1) -->
                <div class="mb-3 flex items-center justify-between print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-2xs font-sans cursor-pointer">
                        ← Kembali ke Daftar Setoran Iklan
                    </button>
                    <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition cursor-pointer font-sans">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Lembar Kuitansi Iklan (Print / PDF)</span>
                    </button>
                </div>

                <!-- Lembar Dokumen Slip Dot Matrix Continuous Form -->
                <div class="print-document-sheet dot-matrix-container bg-white rounded-lg shadow-2xl overflow-y-auto border border-slate-300 flex font-dot-matrix text-black">
                    <!-- Lubang Kertas Continuous Form (Tractor Feed Kiri) -->
                    <div class="w-8 bg-slate-50 border-r border-dashed border-slate-300 py-6 flex flex-col items-center justify-between gap-4 select-none shrink-0 tractor-feed print:hidden">
                        @for ($i = 0; $i < 14; $i++)
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-200 border border-slate-300"></div>
                        @endfor
                    </div>

                    <!-- Isi Slip Kuitansi -->
                    <div class="print-document-body flex-1 p-6 sm:p-8 text-[12px] leading-relaxed text-black">
                        <div class="text-left mb-3">
                            <h1 class="text-base sm:text-lg font-black tracking-wider uppercase font-dot-matrix text-black">
                                {{ ($bkm['jenis_dokumen'] ?? 'BKM') === 'BBM' ? 'BUKTI BANK MASUK (BBM) IKLAN HU. PRIANGAN' : 'KUITANSI SETORAN IKLAN (BKM) HU. PRIANGAN' }}
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
                                    <span class="font-bold">{{ $bkm['kode_perkiraan'] ?? '1.00.04.01.01.01' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Terima Dari</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold uppercase">{{ $bkm['customer'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-28 shrink-0 font-bold whitespace-nowrap">Petugas</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold">{{ $bkm['penagih'] ?? 'KASIR LANGSUNG' }}</span>
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
                                    <span class="font-bold font-mono">{{ $bkm['no_bkm'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">No. Faktur</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold font-mono text-blue-900">{{ $bkm['faktur'] }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">Metode Bayar</span>
                                    <span class="mr-2">:</span>
                                    <span class="uppercase font-bold">{{ $metodeBkm }}{{ $tampilBankBkm ? ' (' . $bankBkm . ')' : '' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 shrink-0 font-bold whitespace-nowrap">Tanggal</span>
                                    <span class="mr-2">:</span>
                                    <span class="font-bold">{{ $bkm['tanggal'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-3"></div>

                        <div class="grid grid-cols-12 gap-x-3 gap-y-1 mb-3">
                            <div class="col-span-4 flex">
                                <span class="w-20 shrink-0 font-bold whitespace-nowrap">Kategori</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase">SETORAN IKLAN</span>
                            </div>
                            <div class="col-span-3 flex">
                                <span class="w-12 shrink-0 font-bold whitespace-nowrap">Tipe</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase">{{ $bkm['status_bayar'] ?? 'LUNAS' }}</span>
                            </div>
                            <div class="col-span-5 flex">
                                <span class="w-24 shrink-0 font-bold whitespace-nowrap">Keterangan</span>
                                <span class="mr-2">:</span>
                                <span class="font-bold uppercase text-[11px] leading-tight">
                                    PEMBAYARAN FAKTUR IKLAN {{ $bkm['faktur'] }}{{ $tampilRefKs ? ' (REF: ' . $refKsBkm . ')' : '' }}
                                </span>
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-3"></div>

                        <div class="space-y-1 max-w-lg mb-4 font-bold">
                            <div class="flex justify-between">
                                <span>Brutto / Nilai Tagihan</span>
                                <span>Rp. {{ number_format($bkm['nominal'] ?? 0, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Potongan Pajak (PPN / PPh 23)</span>
                                <span>Rp. {{ number_format($bkm['pajak'] ?? 0, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Netto Diterima Kasir</span>
                                <span>Rp. {{ number_format($bkm['nominal_fisik'] ?? $bkm['nominal'] ?? 0, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Sisa Piutang Faktur</span>
                                <span>Rp. {{ number_format($bkm['sisa_piutang'] ?? 0, 2, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-dashed border-black pt-1 flex justify-between text-sm font-black">
                                <span>Jumlah Total Diterima</span>
                                <span>Rp. {{ number_format($bkm['nominal_fisik'] ?? $bkm['nominal'] ?? 0, 2, ',', '.') }}</span>
                            </div>
                            <div class="pt-1 text-[11px] italic font-bold">
                                <span class="not-italic">Terbilang :</span> # {{ $bkm['terbilang'] }} #
                            </div>
                        </div>

                        <div class="border-b-2 border-dashed border-black mb-6"></div>

                        <div class="grid grid-cols-2 gap-4 items-end pt-2">
                            <div class="pl-4">
                                @if(($bkm['sisa_piutang'] ?? 0) <= 0)
                                    <div class="inline-block border-2 border-emerald-600 text-emerald-700 px-5 py-2 rounded-full font-black text-center rotate-[-4deg] opacity-90">
                                        <div class="text-[9px] uppercase tracking-widest">KABAR PRIANGAN</div>
                                        <div class="text-sm tracking-wider border-t border-b border-emerald-600 my-0.5 py-0.5">L U N A S</div>
                                        <div class="text-[8px] font-mono">DIVISI KEUANGAN - KASIR</div>
                                    </div>
                                @else
                                    <div class="inline-block border-2 border-cyan-700 text-cyan-800 px-5 py-2.5 rounded-full font-black text-center rotate-[-6deg] opacity-90">
                                        <div class="text-[9px] uppercase tracking-widest text-cyan-900">ANGSURAN IKLAN</div>
                                        <div class="text-xs tracking-wider border-t border-b border-cyan-700 my-0.5 py-0.5">Kabar Priangan</div>
                                        <div class="text-[8px] font-mono">DIVISI KEUANGAN</div>
                                    </div>
                                @endif
                            </div>

                            <div class="text-right flex flex-col gap-5">
                                <div>
                                    <p class="text-[11px] font-bold">Tasikmalaya, {{ date('d-m-Y') }}</p>
                                    <p class="text-[11px] font-semibold mt-0.5">Bagian Kasir,</p>
                                    <div class="h-10"></div>
                                    <p class="font-bold underline text-[11px] uppercase">{{ $bkm['kasir'] }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold">Penyetor / Pelanggan,</p>
                                    <div class="h-10"></div>
                                    <p class="font-bold underline text-[11px] uppercase">{{ $bkm['customer'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lubang Kertas Continuous Form (Tractor Feed Kanan) -->
                    <div class="w-8 bg-slate-50 border-l border-dashed border-slate-300 py-6 flex flex-col items-center justify-between gap-4 select-none shrink-0 tractor-feed print:hidden">
                        @for ($i = 0; $i < 14; $i++)
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-200 border border-slate-300"></div>
                        @endfor
                    </div>
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
                    <div x-show="scanError" x-transition class="p-4 bg-amber-50 border border-amber-300 rounded-xl text-xs text-amber-950 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2 font-bold text-amber-900 text-sm">
                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span x-text="scanErrorTitle || 'Perhatian Pemindai'"></span>
                            </div>
                            <button type="button" @click="scanError = ''" class="text-amber-800 hover:text-amber-950 font-bold text-base leading-none cursor-pointer">&times;</button>
                        </div>
                        <div class="text-xs leading-relaxed text-slate-800" x-html="scanError"></div>
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