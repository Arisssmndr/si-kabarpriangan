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
                            // LANGSUNG PROSES OTOMATIS:
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

    <div class="space-y-6" 
         @keydown.window.f2.prevent="openScanModal()"
         x-data="kasirSirkulasiApp()">

        <!-- Konten Halaman Kasir (Disembunyikan saat modal cetak aktif) -->
        <div class="space-y-6" :class="{ 'print:hidden': showCetakModal }">

        <!-- Alert Flash Message -->
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

        <!-- Header Page Setoran Sirkulasi -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm shadow-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5" />
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
                            Koran Sirkulasi Agen
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            Setoran Sirkulasi Koran Harian
                        </h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-blue-50 text-blue-700 border border-blue-200">
                            Sirkulasi Koran
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Rekonsiliasi tagihan agen koran, perhitungan sah potongan retur BAPR, dan pencatatan kas setoran harian
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if(session('cetak_sirkulasi'))
                    <button type="button" @click="showCetakModal = true" class="px-3.5 py-2 bg-blue-50 hover:bg-blue-100 active:scale-95 text-blue-700 font-bold text-xs rounded-xl border border-blue-200 transition shadow-2xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Bukti Setoran Terakhir</span>
                    </button>
                @endif

                <!-- Pager Navigasi Periode Bulan Cepat -->
                <div class="flex items-center gap-1 bg-slate-100/80 p-1 rounded-xl border border-slate-200/80 text-xs shadow-2xs">
                    <a href="{{ route('kasir.penerimaan.sirkulasi', array_merge(request()->query(), ['bulan' => $prevMonth])) }}" 
                       title="Bulan Sebelumnya ({{ Carbon\Carbon::parse($prevMonth . '-01')->translatedFormat('M Y') }})" 
                       class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-white rounded-lg transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <span class="px-2.5 py-1 font-bold text-slate-700 flex items-center gap-1.5">
                        <span>Periode: {{ $periodeLabel }}</span>
                        @if(!$isCurrentMonth)
                            <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase rounded bg-amber-100 text-amber-800 border border-amber-300">Arsip</span>
                        @endif
                    </span>
                    <a href="{{ route('kasir.penerimaan.sirkulasi', array_merge(request()->query(), ['bulan' => $nextMonth])) }}" 
                       title="Bulan Selanjutnya ({{ Carbon\Carbon::parse($nextMonth . '-01')->translatedFormat('M Y') }})" 
                       class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-white rounded-lg transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Perhitungan Ringkas Rekapitulasi Tagihan Agen (OripioFin Hover Lift) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm shadow-slate-100 hover:-translate-y-1 hover:shadow-md transition-all duration-200 group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Plafon Kredit Agen</span>
                    <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center text-xs group-hover:scale-105 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-slate-900 tracking-tight">
                    Rp {{ number_format($totalPlafon, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Batas toleransi jatah sirkulasi</div>
            </div>

            <div class="bg-white border border-rose-200/80 rounded-2xl p-5 shadow-sm shadow-slate-100 hover:-translate-y-1 hover:shadow-md transition-all duration-200 group bg-rose-50/10">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-rose-700 uppercase tracking-wider">Saldo Piutang Beredar (Wajib Setor)</span>
                    <span class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center text-xs group-hover:scale-105 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-extrabold text-rose-600 tracking-tight">
                    Rp {{ number_format($totalPiutangBulan, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-[11px] text-rose-600/80">Tagihan koran periode {{ $periodeLabel }}</div>
            </div>

            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm shadow-slate-100 hover:-translate-y-1 hover:shadow-md transition-all duration-200 group">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Jatah Oplah & Potongan Retur</span>
                    <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-xs group-hover:scale-105 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-blue-600 tracking-tight">
                    {{ number_format($totalJatah, 0, ',', '.') }} Eksemplar
                </div>
                <div class="mt-1 text-[11px] text-slate-400">Kapasitas oplah terlayani kasir & agen</div>
            </div>
        </div>

        <!-- Panel Dua Pilihan: Input Manual vs Scan Barcode Agen -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-blue-950 border border-slate-700/80 rounded-2xl p-5 shadow-md text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wider rounded bg-blue-500/20 text-blue-300 border border-blue-400/30">
                            Mode Setoran Sirkulasi (2 Pilihan)
                        </span>
                        <span class="text-xs text-slate-400">• Pilih metode pemrosesan setoran agen</span>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                        <span>Pilihan 1: Proses Manual</span>
                        <span class="text-slate-400 font-normal">atau</span>
                        <span class="text-blue-300 font-extrabold">Pilihan 2: Scan Barcode Agen</span>
                    </h3>
                    <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                        Anda dapat memilih agen dari tabel secara manual, atau menggunakan aksi <b>Scan Barcode</b> untuk pengisian otomatis nama agen, wilayah, dan saldo piutang koran secara instan.
                    </p>
                </div>

                <!-- Action Buttons: 2 Pilihan -->
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <!-- Pilihan 1: Manual -->
                    <button type="button" 
                            @click="document.getElementById('tabel-agen-sirkulasi')?.scrollIntoView({ behavior: 'smooth' })"
                            class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 active:scale-95 text-slate-200 hover:text-white text-xs font-bold rounded-xl border border-slate-600 transition flex items-center gap-2 shadow-xs cursor-pointer">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        <span>Pilihan 1: Pilih Manual di Tabel</span>
                    </button>

                    <!-- Pilihan 2: Barcode -->
                    <button type="button" 
                            @click="openScanModal()" 
                            class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/30 transition flex items-center gap-2.5 cursor-pointer">
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
                    <span class="text-slate-400">Tembak laser barcode kartu agen / surat jalan atau ketik kode agen di bawah:</span>
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
                               placeholder="Tembak barcode agen (Enter)..." 
                               class="w-full text-xs font-mono py-2 pl-8 pr-3 rounded-xl bg-slate-950/70 border border-slate-600 focus:border-blue-400 focus:ring-1 focus:ring-blue-400 text-white placeholder-slate-500">
                        <svg class="w-3.5 h-3.5 text-blue-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </div>
                    <button type="button" 
                            @click="processBarcode(scanInput)" 
                            :disabled="isScanning"
                            class="px-3 py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-xs font-bold rounded-xl transition shrink-0 cursor-pointer disabled:opacity-50 shadow-xs">
                        <span x-show="!isScanning">Scan</span>
                        <span x-show="isScanning">Mencari...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar: Status Tagihan, Pilihan Bulan, Kode & Nama Agen -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm shadow-slate-100 space-y-4">
            <form method="GET" action="{{ route('kasir.penerimaan.sirkulasi') }}" class="space-y-4">
                <!-- Baris 1: Filter Status Tagihan (Segmented Control / Radio) -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5 mr-1">
                            <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            STATUS TAGIHAN:
                        </span>
                        <div class="inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200/80 text-xs shadow-2xs">
                            <label class="cursor-pointer px-3.5 py-1.5 rounded-lg font-medium transition {{ ($statusPiutang ?? 'belum_lunas') === 'belum_lunas' ? 'bg-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="status_piutang" value="belum_lunas" {{ ($statusPiutang ?? 'belum_lunas') === 'belum_lunas' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                                <span>Hanya yang Punya Piutang (Belum Lunas)</span>
                            </label>
                            <label class="cursor-pointer px-3.5 py-1.5 rounded-lg font-medium transition {{ ($statusPiutang ?? '') === 'lunas' ? 'bg-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="status_piutang" value="lunas" {{ ($statusPiutang ?? '') === 'lunas' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                                <span>Sudah Lunas</span>
                            </label>
                            <label class="cursor-pointer px-3.5 py-1.5 rounded-lg font-medium transition {{ ($statusPiutang ?? '') === 'semua' ? 'bg-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                                <input type="radio" name="status_piutang" value="semua" {{ ($statusPiutang ?? '') === 'semua' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                                <span>Semua Status</span>
                            </label>
                        </div>
                    </div>

                    <!-- Indikator Filter Aktif & Reset -->
                    @if(($statusPiutang ?? 'belum_lunas') !== 'belum_lunas' || ($bulanPilihan ?? Carbon\Carbon::now()->format('Y-m')) !== Carbon\Carbon::now()->format('Y-m') || request('search') || request('kode_agen'))
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-lg font-medium flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                Filter Aktif ({{ $periodeLabel }})
                            </span>
                            <a href="{{ route('kasir.penerimaan.sirkulasi') }}" class="text-xs text-rose-600 hover:text-rose-700 font-semibold hover:underline flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reset Filter
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Baris 2: Pilih Bulan & Pencarian Cepat Kode Agen Pill OripioFin -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <!-- Pilih Periode Bulan -->
                    <div class="sm:col-span-3">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            PILIH BULAN:
                        </label>
                        <div class="relative">
                            <input type="month" name="bulan" value="{{ $bulanPilihan }}" onchange="this.form.submit()" 
                                   class="w-full text-xs font-semibold py-2 px-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-900 bg-slate-50/50">
                        </div>
                    </div>

                    <!-- Input Cepat Kode Agen Berformat Pill OripioFin -->
                    <div class="sm:col-span-4">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                            KODE AGEN (PILL CEPAT):
                        </label>
                        <div class="relative">
                            <input type="text" name="kode_agen" value="{{ request('kode_agen') }}" placeholder="Pill Kode Agen (misal: 001, 005)..." 
                                   class="w-full text-xs font-mono font-bold py-2 pl-9 pr-3 rounded-full border border-slate-200 bg-slate-50/70 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-900 placeholder-slate-400 transition-all">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        </div>
                    </div>

                    <!-- General Search Input -->
                    <div class="sm:col-span-3">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Nama / Wilayah:</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama agen atau wilayah..." 
                                   class="w-full text-xs py-2 pl-9 pr-3 rounded-xl border border-slate-200 bg-slate-50/70 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-900 transition-all">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    <!-- Tombol Submit Filter -->
                    <div class="sm:col-span-2 flex items-center gap-2">
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs rounded-xl transition shadow-xs shadow-blue-500/20 flex items-center justify-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            <span>Filter Agen</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Tagihan Agen & Form Setoran -->
        <div id="tabel-agen-sirkulasi" class="bg-white border border-slate-200/80 rounded-2xl shadow-sm shadow-slate-100 overflow-hidden scroll-mt-6">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    Daftar Agen Sirkulasi & Status Piutang ({{ $periodeLabel }})
                </h3>
                <span class="text-[11px] text-slate-500 font-medium">Terintegrasi dengan Master Agen & Kartu Piutang Akunting</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Kode & Nama Agen</th>
                            <th class="px-4 py-3 font-semibold">Kategori & Rute</th>
                            <th class="px-4 py-3 font-semibold">Wilayah</th>
                            <th class="px-4 py-3 font-semibold text-right">Jatah Eks</th>
                            <th class="px-4 py-3 font-semibold text-right">Plafon Piutang</th>
                            <th class="px-4 py-3 font-semibold text-right">Saldo Piutang Koran</th>
                            <th class="px-4 py-3 font-semibold text-center">Aksi Setor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($agenList as $agen)
                        <tr class="hover:bg-slate-50/70 transition-colors duration-150">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $agen->nama_agen }}</div>
                                <span class="font-mono text-[10px] text-slate-400">{{ $agen->kode_agen }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $agen->kategori === '11' ? 'Agen Besar' : 'Sub-Agen' }}
                                </span>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $agen->rute_kode ?? 'R-01' }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-600 font-medium">{{ $agen->wilayah ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-medium text-slate-700">{{ number_format($agen->jatah_default) }} eks</td>
                            <td class="px-4 py-3 text-right text-slate-600">Rp {{ number_format($agen->plafon_piutang, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-extrabold {{ $agen->saldo_piutang_bulan > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                Rp {{ number_format($agen->saldo_piutang_bulan, 0, ',', '.') }}
                                @if($agen->total_setoran_bulan > 0)
                                    <div class="text-[10px] font-normal text-slate-500 mt-0.5">
                                        Setoran: Rp {{ number_format($agen->total_setoran_bulan, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
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
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold text-[11px] rounded-xl transition shadow-xs shadow-blue-500/20 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <span>Input Setoran BKM</span>
                                    </button>
                                @else
                                    <div class="inline-flex flex-col items-center">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Lunas Bersih
                                        </span>
                                        @if($agen->transaksi_terakhir)
                                            <span class="text-[9px] font-mono text-slate-400 mt-0.5" title="Nomor Kuitansi Setoran">
                                                {{ $agen->transaksi_terakhir->no_kuitansi }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="font-semibold text-slate-700 text-xs">Tidak ada data agen yang memenuhi kriteria filter</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        Status: <span class="font-bold">{{ $statusPiutang === 'belum_lunas' ? 'Hanya yang Punya Piutang (Belum Lunas)' : ($statusPiutang === 'lunas' ? 'Sudah Lunas' : 'Semua Status') }}</span> 
                                        • Periode: <span class="font-bold">{{ $periodeLabel }}</span>
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form Setoran Sirkulasi -->
        <div x-cloak x-show="showSetorModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showSetorModal = false" class="bg-white rounded-2xl max-w-xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200/80 overflow-hidden">
                <!-- Header (shrink-0) -->
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 shrink-0 bg-slate-50/50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Form Setoran Kas Sirkulasi</h4>
                            <p class="text-[11px] text-slate-500">Pencatatan kas masuk setoran agen koran</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="showSetorModal = false; openScanModal()" class="px-2.5 py-1 text-[11px] bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-700 font-semibold rounded-lg border border-slate-200 transition flex items-center gap-1 cursor-pointer active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <span>Ganti via Scan</span>
                        </button>
                        <button type="button" @click="showSetorModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
                    </div>
                </div>

                <form id="form-setor-sirkulasi" method="POST" action="{{ route('kasir.penerimaan.sirkulasi.store') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="agen_id" :value="activeAgen.id">
                    <input type="hidden" name="periode" value="{{ $bulanPilihan }}">
                    <input type="hidden" name="status_piutang" value="{{ $statusPiutang }}">

                    <!-- Scrollable Body (flex-1 overflow-y-auto) -->
                    <div class="flex-1 overflow-y-auto px-5 py-3.5 space-y-3.5 text-xs">
                        <div class="bg-slate-50/80 p-3.5 rounded-xl border border-slate-200 text-xs space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Nama Agen:</span>
                                <span class="font-bold text-slate-900" x-text="activeAgen.nama + ' (' + activeAgen.kode + ')'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Wilayah Sirkulasi:</span>
                                <span class="font-bold text-slate-800" x-text="activeAgen.wilayah"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Saldo Piutang Koran Saat Ini:</span>
                                <span class="font-bold text-rose-600">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeAgen.piutang)"></span></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Bulan Periode:</span>
                                <span class="font-bold text-slate-800" x-text="activeAgen.bulan"></span>
                            </div>
                        </div>

                        <!-- Identitas Form Divisi Sirkulasi -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 p-3 bg-slate-50/80 rounded-xl border border-slate-200 text-xs">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-0.5">Divisi</label>
                                <div class="py-1.5 px-3 bg-white rounded-lg border border-slate-200 font-bold text-slate-800 flex items-center justify-between text-xs">
                                    <span>Divisi Sirkulasi</span>
                                    <span class="text-[9px] px-1.5 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded font-sans font-bold">KAS</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-0.5">Kode Perkiraan (COA)</label>
                                <select name="kode_perkiraan" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-slate-200 font-mono font-semibold text-slate-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 bg-white">
                                    <option value="1.00.04.01.01.00">1.00.04.01.01.00 (Piutang Sirkulasi)</option>
                                    <option value="2.00.01.01.01.00">2.00.01.01.01.00 (Uang Muka Sirkulasi)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-0.5">No. Blanko Fisik (Opsional)</label>
                                <input type="text" name="no_blanko" placeholder="Contoh: BLK-02881" 
                                       class="w-full text-xs py-1.5 px-3 rounded-lg border border-slate-200 font-mono text-slate-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 bg-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <!-- Petugas Inkaso Dropdown -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Petugas Inkaso Sirkulasi</label>
                                <select name="penagih_inkaso" x-model="activeAgen.penagih" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 bg-white">
                                    @foreach($petugasInkasoList as $petugas)
                                        <option value="{{ $petugas->name }}">{{ $petugas->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Metode Setoran -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Metode Setoran</label>
                                <select name="metode_pembayaran" x-model="activeAgen.metode" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 bg-white">
                                    <option value="tunai">Tunai Meja Kasir</option>
                                    <option value="transfer">Transfer Bank BJB Sirkulasi</option>
                                    <option value="qris">QRIS Resmi Kabar Priangan</option>
                                </select>
                            </div>
                        </div>

                        <!-- Input Khusus Transfer -->
                        <div x-show="activeAgen.metode === 'transfer'" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 p-3 bg-blue-50/50 rounded-xl border border-blue-100">
                            <div>
                                <label class="block text-[11px] font-bold text-blue-900 mb-0.5">Bank Tujuan</label>
                                <select name="bank_tujuan" x-model="activeAgen.bankTujuan" class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-blue-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 bg-white">
                                    <option value="Bank BJB Sirkulasi">Bank BJB Sirkulasi (Giro: 0012-0034-5678)</option>
                                    <option value="Bank Mandiri Sirkulasi">Bank Mandiri (Giro: 131-00-9876543-2)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-blue-900 mb-0.5">No. Referensi / Bukti Transfer</label>
                                <input type="text" name="no_referensi_transfer" x-model="activeAgen.noRef" placeholder="Contoh: TRF-BJB-984128" 
                                       class="w-full text-xs py-1.5 px-2.5 rounded-lg border border-blue-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-800 font-mono bg-white">
                            </div>
                        </div>

                        <!-- Nominal Uang Yang Disetor -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Uang Kas Masuk / Ditransfer (Rp)</label>
                            <input type="number" name="nominal_setoran" x-model="activeAgen.nominal" required min="1" 
                                   class="w-full text-base font-mono font-bold py-2.5 px-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-900 bg-slate-50/50">
                        </div>

                        <!-- Rincian Rekonsiliasi Hasil Setoran -->
                        <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl text-xs space-y-1">
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Jumlah Setoran Kas Masuk:</span>
                                <span class="font-bold font-mono text-slate-900">Rp <span x-text="new Intl.NumberFormat('id-ID').format(activeAgen.nominal || 0)"></span></span>
                            </div>
                            <div class="flex justify-between items-center text-slate-600 border-t border-slate-200/60 pt-1">
                                <span>Sisa Saldo Piutang Setelah Setoran:</span>
                                <span class="font-bold font-mono text-sm" :class="sisaPiutangBaru === 0 ? 'text-emerald-600' : 'text-amber-600'">
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(sisaPiutangBaru)"></span>
                                    <span class="text-[10px] ml-1 font-semibold" x-text="sisaPiutangBaru === 0 ? '(LUNAS BERSIH)' : '(SISA PIUTANG)'"></span>
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                            <input type="text" name="keterangan" x-model="activeAgen.keterangan" placeholder="Keterangan setoran agen..." 
                                   class="w-full text-xs py-2 px-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 text-slate-900">
                        </div>
                    </div>

                    <!-- Footer Action Buttons (shrink-0) -->
                    <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0">
                        <button type="button" @click="showSetorModal = false" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl transition active:scale-95">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold text-xs rounded-xl transition shadow-xs shadow-blue-500/20">
                            Simpan & Terbitkan BKM Sirkulasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <!-- Modal Cetak Tanda Terima Setoran Koran Otomatis (Format Bersih Tanpa Saldo Masa Lalu) -->
        @if(session('cetak_sirkulasi'))
        @php $srk = session('cetak_sirkulasi'); @endphp
        <div x-cloak x-show="showCetakModal" class="modal-print-overlay fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="showCetakModal = false" class="print-document-sheet bg-white rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200/80 overflow-hidden">
                <!-- Header Dokumen -->
                <div class="border-b-2 border-slate-900 px-5 py-3.5 flex items-start justify-between shrink-0 bg-slate-50/50">
                    <div>
                        <div class="text-base font-black tracking-tight text-slate-900 uppercase">HARIAN UMUM KABAR PRIANGAN</div>
                        <div class="text-[10px] text-slate-500">Divisi Sirkulasi & Perkasiran Harian</div>
                        <div class="text-[10px] text-slate-500">Jl. Siliwangi No. 129, Tasikmalaya - Telp: (0265) 331200</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 text-[10px] font-black rounded-md bg-blue-50 text-blue-700 uppercase tracking-widest border border-blue-200">
                            TANDA TERIMA SETORAN KORAN
                        </span>
                        <div class="font-mono font-bold text-xs text-slate-900 mt-1">{{ $srk['no_bkm'] }}</div>
                        <div class="text-[10px] text-slate-500">{{ $srk['tanggal'] }}</div>
                    </div>
                </div>

                <!-- Body Dokumen (Scrollable - Bersih tanpa saldo piutang masa lalu) -->
                <div class="print-document-body flex-1 overflow-y-auto p-5 space-y-2.5 text-xs text-slate-800">
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Nama Agen</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $srk['nama_agen'] }} ({{ $srk['kode_agen'] }})</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Wilayah Sirkulasi</span>
                        <span class="col-span-2 text-slate-800">: {{ $srk['wilayah'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Petugas Inkaso</span>
                        <span class="col-span-2 font-semibold text-slate-800">: {{ $srk['penagih'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100">
                        <span class="text-slate-500">Metode Bayar</span>
                        <span class="col-span-2 font-semibold text-slate-800">: {{ $srk['metode'] }}</span>
                    </div>
                    @if($srk['no_bapr'] !== '-')
                    <div class="grid grid-cols-3 py-1 border-b border-slate-100 bg-amber-50/60 px-2 rounded-lg">
                        <span class="text-amber-900 font-semibold">Bukti Retur Sah (BAPR)</span>
                        <span class="col-span-2 font-mono text-amber-900">: {{ $srk['no_bapr'] }} ({{ $srk['retur_eks'] }} eks / Rp {{ number_format($srk['nilai_retur'], 0, ',', '.') }})</span>
                    </div>
                    @endif
                    <div class="grid grid-cols-3 py-2 bg-blue-50 px-3 rounded-xl border border-blue-200/80 my-2">
                        <span class="font-bold text-blue-900">Jumlah Disetor</span>
                        <span class="col-span-2 font-mono font-black text-base text-blue-800">: Rp {{ number_format($srk['nominal'], 0, ',', '.') }}</span>
                    </div>

                    <div class="grid grid-cols-3 py-1.5 text-xs bg-slate-50 px-3 rounded-lg border border-slate-200">
                        <span class="text-slate-500 font-semibold">Terbilang Huruf</span>
                        <span class="col-span-2 font-semibold italic text-slate-900">: {{ $srk['terbilang'] }}</span>
                    </div>

                    <!-- Tanda Tangan Cetak: Hanya Kasir Penerima Sah Sesuai SOP Operasional -->
                    <div class="pt-6 border-t border-slate-200 flex justify-end text-center text-xs text-slate-600">
                        <div class="w-48">
                            <div class="text-[11px] text-slate-500">Tasikmalaya, {{ Carbon\Carbon::now()->format('d/m/Y') }}</div>
                            <div class="font-semibold text-slate-700 mt-1">Kasir Penerima</div>
                            <div class="h-14"></div>
                            <div class="font-bold text-slate-900 font-mono underline">({{ $srk['kasir'] }})</div>
                            <div class="text-[10px] text-slate-400">Bagian Perkasiran HU Priangan</div>
                        </div>
                    </div>
                </div>

                <!-- Action Button Print & Close (Pinned Bottom) -->
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-end gap-2 shrink-0 print:hidden no-print">
                    <button type="button" @click="showCetakModal = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition active:scale-95">
                        Tutup
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs rounded-xl transition shadow-xs shadow-blue-500/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Bukti Setoran Koran</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Modal Aksi Scan Barcode Agen Sirkulasi -->
        <div x-cloak x-show="showScanModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            <div @click.away="closeScanModal()" class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
                <!-- Header Modal Scanner -->
                <div class="flex items-center justify-between px-5 py-3.5 bg-gradient-to-r from-slate-900 via-slate-800 to-blue-950 text-white">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-blue-500/20 text-blue-300 border border-blue-400/30 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-white">Aksi Scan Barcode Agen Koran</h4>
                            <p class="text-[11px] text-blue-200">Arahkan scanner ke kartu agen / struk loper</p>
                        </div>
                    </div>
                    <button type="button" @click="closeScanModal()" class="text-slate-400 hover:text-white font-bold text-lg leading-none cursor-pointer">&times;</button>
                </div>

                <!-- Body Modal Scanner -->
                <div class="p-5 space-y-4">
                    <!-- Toggle Mode Auto-Submit -->
                    <div class="p-3 bg-gradient-to-r from-blue-50 via-indigo-50 to-slate-50 border border-blue-100 rounded-xl flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                    <span>Input & Proses Otomatis Saat Scan</span>
                                    <span class="px-1.5 py-0.5 bg-blue-100 text-blue-800 text-[9px] font-bold rounded">Auto-Submit</span>
                                </div>
                                <p class="text-[10px] text-slate-500">Scan barcode agen langsung mencatat setoran & membuka cetak BKM</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="autoProcess" class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Scanner Laser Input Area -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">Kode Barcode / Kode Agen / Nama Agen</label>
                        <div class="relative">
                            <input type="text" 
                                   x-ref="barcodeInput"
                                   x-model="scanInput" 
                                   @keydown.enter.prevent="processBarcode(scanInput)" 
                                   placeholder="Tembak barcode agen atau ketik kode agen (misal: 001)..." 
                                   class="w-full text-base font-mono font-bold py-2.5 pl-10 pr-20 rounded-xl border-2 border-blue-500 focus:ring-2 focus:ring-blue-100 focus:border-blue-600 text-slate-900 bg-blue-50/20">
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
                        <button type="button" @click="scanError = ''" class="text-rose-500 font-bold cursor-pointer">&times;</button>
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
                            <div id="reader-camera-sirkulasi" class="w-full overflow-hidden rounded-lg"></div>
                            <p class="text-[11px] text-blue-300 py-1.5 text-center font-medium animate-pulse">
                                Arahkan barcode kartu agen atau QR Code ke dalam kotak scanner di atas
                            </p>
                        </div>

                        <!-- Opsi Scan Gambar File / Screenshot -->
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
                        <div id="temp-reader-sirkulasi" style="display:none;"></div>
                    </div>

                    <!-- Quick Click Simulation Barcodes -->
                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">
                            Simulasi / Contoh Barcode Agen (Klik untuk Uji Cepat):
                        </span>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse($agenList->take(6) as $a)
                            <button type="button" 
                                    @click="processBarcode('{{ $a->kode_agen }}')" 
                                    class="px-2.5 py-1 text-[11px] font-mono font-semibold rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-900 border border-slate-200 transition cursor-pointer flex items-center gap-1 active:scale-95">
                                <span class="w-1.5 h-1.5 rounded-full {{ ($a->saldo_piutang_bulan_ini ?? 0) > 0 ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                <span>{{ $a->kode_agen }}</span>
                                <span class="text-[9px] text-slate-400 font-sans">({{ $a->nama_agen }})</span>
                            </button>
                            @empty
                            <span class="text-xs text-slate-400">Tidak ada agen contoh.</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="px-5 py-3.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Tekan <b>Esc</b> untuk membatalkan</span>
                    <button type="button" @click="closeScanModal()" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold rounded-xl transition cursor-pointer active:scale-95">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- Overlay Loading Saat Auto-Submit -->
        <div x-cloak x-show="isAutoSubmitting" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl border border-slate-100 flex flex-col items-center text-center space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 border-2 border-blue-500 text-blue-600 flex items-center justify-center animate-bounce shadow-md shadow-blue-500/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Barcode Berhasil Dipindai!</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Memproses setoran agen koran otomatis ke Kas Masuk...</p>
                </div>
                <div class="w-full bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs space-y-1.5 font-mono text-left">
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-sans">Kode Agen:</span>
                        <span class="font-bold text-slate-900" x-text="autoSubmitInfo.kode"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-sans">Nama Agen:</span>
                        <span class="font-bold text-slate-800 truncate max-w-[180px]" x-text="autoSubmitInfo.agen"></span>
                    </div>
                    <div class="flex justify-between border-t border-slate-200 pt-1">
                        <span class="text-slate-500 font-sans">Nominal Setoran:</span>
                        <span class="font-bold text-blue-600">Rp <span x-text="new Intl.NumberFormat('id-ID').format(autoSubmitInfo.nominal)"></span></span>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs text-blue-700 font-semibold">
                    <svg class="w-4 h-4 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <span>Menerbitkan Bukti Kas Masuk Sirkulasi...</span>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
