const STORAGE_CAMERA_KEY = 'kasir_qr_selected_camera';
const STORAGE_FLIP_PREFIX = 'kasir_qr_anti_mirror_';

/**
 * Determine whether a camera label or facingMode represents a rear-facing camera.
 */
function isBackCamera(label = '', facingMode = '') {
    if (String(facingMode).toLowerCase() === 'environment') {
        return true;
    }

    return /(back|rear|belakang|environment)/i.test(String(label));
}

/**
 * Create a shared Alpine state mixin for QR/barcode live camera scanning with horizontal un-mirror controls.
 *
 * @param {Object} options
 * @param {string} options.readerId DOM ID of the live camera container
 * @param {string} options.tempReaderId DOM ID of the hidden file-scan container
 * @param {string} [options.manualInputLabel='Nomor Referensi'] Label used in camera permission fallback tips
 */
export function createQrCameraScannerMixin({
    readerId,
    tempReaderId,
    manualInputLabel = 'Nomor Referensi',
}) {
    return {
        cameraActive: false,
        isStartingCamera: false,
        html5QrScanner: null,
        availableCameras: [],
        selectedCameraId: '',
        _runningCameraId: '',
        isCameraFlipped: true,

        _resolveFlipDefault(cameraId = '', label = '', facingMode = '') {
            const storageKey = `${STORAGE_FLIP_PREFIX}${cameraId || 'default'}`;
            try {
                const saved = localStorage.getItem(storageKey);
                if (saved !== null) {
                    return saved === 'true';
                }
            } catch (e) {
                // Abaikan jika storage browser dibatasi
            }

            // Kamera belakang HP umumnya sudah normal; webcam laptop/kamera depan sering mirror
            return !isBackCamera(label, facingMode);
        },

        toggleCameraFlip() {
            this.isCameraFlipped = !this.isCameraFlipped;
            const storageKey = `${STORAGE_FLIP_PREFIX}${this.selectedCameraId || 'default'}`;
            try {
                localStorage.setItem(storageKey, String(this.isCameraFlipped));
            } catch (e) {
                // Abaikan jika storage browser dibatasi
            }
        },

        async _clearScannerInstance() {
            const scanner = this.html5QrScanner;
            this.html5QrScanner = null;
            this._runningCameraId = '';

            if (!scanner) {
                return;
            }

            try {
                if (scanner.isScanning) {
                    await scanner.stop();
                }
                scanner.clear();
            } catch (e) {
                // Abaikan error pembersihan instance kamera lama
            }
        },

        _buildCameraPermissionErrorHtml(host) {
            return `Browser memblokir akses webcam/kamera untuk alamat <strong>${host}</strong>.<br><br>`
                + '<strong>Cara Mengaktifkan Kembali:</strong><br>'
                + '<ol class="list-decimal pl-4 space-y-1 mt-1 text-slate-700">'
                + '<li>Lihat <strong>Address Bar</strong> (kolom alamat website di bagian paling atas browser).</li>'
                + `<li>Klik ikon <strong>Setelan Situs / Tune</strong> di sebelah kiri alamat <code>${host}</code>.</li>`
                + '<li>Cari opsi <strong>Kamera (Camera)</strong> lalu ubah dari <strong>Blokir (Block)</strong> menjadi <strong>Izinkan (Allow)</strong>.</li>'
                + '<li>Muat ulang halaman browser (tekan <strong>F5</strong> atau tombol Reload).</li>'
                + '</ol><br>'
                + '<div class="p-2 bg-sky-50 border border-sky-200 rounded-lg text-sky-900 font-medium">'
                + `💡 <strong>Alternatif Cepat:</strong> Anda tetap bisa memproses setoran dengan mengetik ${manualInputLabel} di atas lalu tekan <strong>Enter/Proses</strong>, menembak dengan <strong>Scanner Barcode USB</strong>, atau klik <strong>Pilih Gambar QR</strong>.`
                + '</div>';
        },

        async startCamera(preferredCameraId = null) {
            if (this.isStartingCamera) {
                return;
            }

            this.isStartingCamera = true;
            this.cameraActive = true;
            this.scanError = '';
            this.scanErrorTitle = '';

            await new Promise((resolve) => this.$nextTick(resolve));

            try {
                if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                    this.scanErrorTitle = 'Konteks Keamanan Kamera Diperlukan';
                    this.scanError = 'Akses kamera membutuhkan HTTPS atau http://127.0.0.1 / http://localhost.';
                    this.cameraActive = false;
                    return;
                }

                if (typeof Html5Qrcode === 'undefined') {
                    this.scanErrorTitle = 'Pustaka Kamera Belum Siap';
                    this.scanError = 'Pustaka pemindai barcode belum selesai dimuat. Silakan muat ulang (refresh) browser.';
                    this.cameraActive = false;
                    return;
                }

                await this._clearScannerInstance();

                this.html5QrScanner = new Html5Qrcode(readerId);
                const config = {
                    fps: 15,
                    // Tetap izinkan pembacaan frame normal maupun terbalik agar semua QR tetap terbaca
                    disableFlip: false,
                    qrbox: (viewfinderWidth, viewfinderHeight) => {
                        const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                        const edge = Math.min(minEdge, Math.max(50, Math.floor(minEdge * 0.75)));
                        return { width: edge, height: edge };
                    },
                    formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
                };

                const onScanSuccess = (decodedText) => {
                    this.playBeep();
                    this.stopCamera();
                    this.processBarcode(decodedText);
                };

                let savedCameraId = '';
                try {
                    savedCameraId = localStorage.getItem(STORAGE_CAMERA_KEY) || '';
                } catch (e) {
                    savedCameraId = '';
                }

                let cameraIdOrConfig = { facingMode: 'user' };
                let chosenLabel = '';
                let chosenId = '';

                try {
                    const cameras = await Html5Qrcode.getCameras();
                    if (cameras && cameras.length > 0) {
                        this.availableCameras = cameras.map((cam, idx) => ({
                            id: cam.id,
                            label: cam.label || `Kamera ${idx + 1}`,
                        }));

                        const explicitMatch = preferredCameraId
                            ? this.availableCameras.find((c) => c.id === preferredCameraId)
                            : null;
                        const savedMatch = !explicitMatch && savedCameraId
                            ? this.availableCameras.find((c) => c.id === savedCameraId)
                            : null;
                        const backMatch = !explicitMatch && !savedMatch
                            ? this.availableCameras.find((c) => isBackCamera(c.label))
                            : null;

                        const selected = explicitMatch || savedMatch || backMatch || this.availableCameras[0];
                        chosenId = selected.id;
                        chosenLabel = selected.label;
                        cameraIdOrConfig = preferredCameraId
                            ? { deviceId: { exact: chosenId } }
                            : chosenId;
                    }
                } catch (camErr) {
                    console.warn('Enumerasi kamera via getCameras():', camErr);
                    cameraIdOrConfig = { facingMode: 'environment' };
                }

                this.isCameraFlipped = this._resolveFlipDefault(
                    chosenId,
                    chosenLabel,
                    typeof cameraIdOrConfig === 'object' ? cameraIdOrConfig.facingMode || '' : ''
                );

                try {
                    await this.html5QrScanner.start(
                        cameraIdOrConfig,
                        config,
                        onScanSuccess,
                        () => {}
                    );
                } catch (startErr) {
                    const msg = String(startErr?.message || startErr?.name || startErr).toLowerCase();
                    const isPermissionErr = msg.includes('notallowed') || msg.includes('permission') || msg.includes('denied');
                    const isDeviceSpec = typeof cameraIdOrConfig === 'string'
                        || (typeof cameraIdOrConfig === 'object' && Boolean(cameraIdOrConfig?.deviceId));

                    if (!isPermissionErr && isDeviceSpec) {
                        await this.html5QrScanner.start(
                            { facingMode: 'user' },
                            config,
                            onScanSuccess,
                            () => {}
                        );
                    } else {
                        throw startErr;
                    }
                }

                // Segarkan daftar kamera setelah izin diberikan agar label perangkat terisi lengkap
                try {
                    const refreshedCameras = await Html5Qrcode.getCameras();
                    if (refreshedCameras && refreshedCameras.length > 0) {
                        this.availableCameras = refreshedCameras.map((cam, idx) => ({
                            id: cam.id,
                            label: cam.label || `Kamera ${idx + 1}`,
                        }));
                    }
                } catch (e) {
                    // Tetap gunakan daftar kamera sebelumnya
                }

                let runningSettings = {};
                try {
                    if (typeof this.html5QrScanner?.getRunningTrackSettings === 'function') {
                        runningSettings = this.html5QrScanner.getRunningTrackSettings() || {};
                    }
                } catch (e) {
                    runningSettings = {};
                }

                const resolvedId = runningSettings.deviceId || chosenId || this.availableCameras[0]?.id || 'default';
                const matchedCamera = this.availableCameras.find((cam) => cam.id === resolvedId);

                this.selectedCameraId = matchedCamera ? matchedCamera.id : (this.availableCameras[0]?.id || resolvedId);
                this._runningCameraId = this.selectedCameraId;
                this.isCameraFlipped = this._resolveFlipDefault(
                    this.selectedCameraId,
                    matchedCamera?.label || chosenLabel,
                    runningSettings.facingMode || ''
                );
            } catch (e) {
                const errStr = String(e?.message || e?.name || e).toLowerCase();
                if (errStr.includes('notallowed') || errStr.includes('permission') || errStr.includes('denied')) {
                    this.scanErrorTitle = 'Izin Akses Kamera Ditolak / Diblokir Browser';
                    this.scanError = this._buildCameraPermissionErrorHtml(window.location.host);
                } else if (errStr.includes('notfound') || errStr.includes('device not found') || errStr.includes('requested device not found')) {
                    this.scanErrorTitle = 'Perangkat Kamera Tidak Ditemukan';
                    this.scanError = `Tidak ada perangkat webcam atau kamera yang terhubung pada komputer ini. Silakan sambungkan webcam atau gunakan input ${manualInputLabel} / scanner laser USB.`;
                } else {
                    this.scanErrorTitle = 'Gagal Mengakses Kamera';
                    this.scanError = `Kamera tidak dapat dinyalakan: ${e?.message || e}. Pastikan kamera tidak sedang digunakan oleh aplikasi lain (seperti Zoom, Teams, Meet).`;
                }
                this.cameraActive = false;
            } finally {
                this.isStartingCamera = false;
            }
        },

        async switchCamera(cameraId) {
            if (!cameraId || this.isStartingCamera) {
                return;
            }
            if (cameraId === this._runningCameraId && this.cameraActive && this.html5QrScanner?.isScanning) {
                return;
            }

            try {
                localStorage.setItem(STORAGE_CAMERA_KEY, cameraId);
            } catch (e) {
                // Abaikan jika storage browser dibatasi
            }

            this.selectedCameraId = cameraId;
            await this._clearScannerInstance();
            await this.startCamera(cameraId);
        },

        async cycleCamera() {
            if (this.availableCameras.length < 2 || this.isStartingCamera) {
                return;
            }

            const currentIdx = this.availableCameras.findIndex((cam) => cam.id === this.selectedCameraId);
            const nextIdx = currentIdx >= 0 ? (currentIdx + 1) % this.availableCameras.length : 0;
            await this.switchCamera(this.availableCameras[nextIdx].id);
        },

        async stopCamera() {
            this.cameraActive = false;
            await this._clearScannerInstance();
        },

        async scanUploadedImage(event) {
            const file = event.target.files?.[0];
            if (!file) {
                return;
            }

            this.isScanning = true;
            this.scanError = '';
            this.scanErrorTitle = '';

            try {
                const html5QrCode = new Html5Qrcode(tempReaderId);
                const decodedText = await html5QrCode.scanFile(file, true);
                this.playBeep();
                this.processBarcode(decodedText);
            } catch (err) {
                this.scanError = 'QR Code tidak terdeteksi pada gambar yang diunggah. Pastikan gambar jelas dan tidak buram.';
            } finally {
                this.isScanning = false;
                if (event.target) {
                    event.target.value = '';
                }
            }
        },
    };
}
