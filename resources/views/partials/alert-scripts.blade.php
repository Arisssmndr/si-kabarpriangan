<style>
    /* ==========================================================================
       SIKAPRI MODERN ELEGANT SWEETALERT2 SYSTEM (PALET 60-30-10)
       - Minimalist, High-Contrast, Professional, Zero Clutter
       - Layout Header Side-by-Side: Icon & Judul sejajar rapi tanpa ruang kosong samping
       - 60% Dominan: Putih Bersih (#ffffff) & Backdrop Slate Halus Kontras
       - 30% Struktural: Tipografi Slate-900 / Slate-600 tegas berbobot
       - 10% Aksen: KP Blue (#005792) / Rose (#e11d48) / Emerald (#059669)
       ========================================================================== */

    /* Backdrop Modal — High Contrast Focus */
    .swal2-container.kp-alert-backdrop,
    .swal2-container.swal2-backdrop-show {
        background: rgba(15, 23, 42, 0.6) !important;
        backdrop-filter: blur(5px) !important;
        -webkit-backdrop-filter: blur(5px) !important;
        z-index: 99999 !important;
        padding: 16px !important;
    }

    /* Popup Dialog Utama — Superellipse Elegan, Border Tegas Berkontras Tinggi */
    .swal2-popup.kp-alert-popup {
        width: 460px !important;
        max-width: calc(100vw - 32px) !important;
        padding: 24px !important;
        border-radius: 20px !important;
        background: #ffffff !important;
        border: 1.5px solid #cbd5e1 !important;
        box-shadow:
            0 25px 50px -12px rgba(15, 23, 42, 0.25),
            0 0 0 1px rgba(15, 23, 42, 0.05) !important;
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
        text-align: left !important;
    }

    .swal2-popup.kp-alert-popup .swal2-html-container {
        margin: 0 !important;
        padding: 0 !important;
        text-align: left !important;
        color: #1e293b !important;
    }

    /* Action Buttons Container — Clean & High Contrast */
    .swal2-popup.kp-alert-popup .swal2-actions {
        margin: 20px 0 0 0 !important;
        padding: 0 !important;
        width: 100% !important;
        background: transparent !important;
        border-top: none !important;
        display: flex !important;
        justify-content: flex-end !important;
        align-items: center !important;
        gap: 10px !important;
        box-sizing: border-box !important;
    }

    .swal2-popup.kp-alert-popup .swal2-actions:empty {
        display: none !important;
    }

    .swal2-popup.kp-alert-popup .swal2-icon,
    .swal2-popup.kp-alert-popup .swal2-title {
        display: none !important;
    }

    .swal2-popup.kp-alert-popup .swal2-timer-progress-bar,
    .swal2-timer-progress-bar {
        display: none !important;
        height: 0 !important;
        opacity: 0 !important;
        visibility: hidden !important;
    }

    /* Header Side-by-Side: Icon & Judul Bersebelahan Rapi (Tidak Ada Samping Kosong) */
    .kp-alert-header-row {
        display: flex !important;
        align-items: flex-start !important;
        gap: 14px !important;
        position: relative !important;
        margin-bottom: 12px !important;
    }

    .kp-alert-icon-circle {
        width: 44px !important;
        height: 44px !important;
        border-radius: 12px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        margin-top: 1px !important;
    }

    .kp-alert-icon-circle svg {
        width: 22px !important;
        height: 22px !important;
    }

    .kp-alert-icon-circle.theme-rose {
        background-color: #ffe4e6 !important;
        color: #e11d48 !important;
        border: 1px solid #fecdd3 !important;
    }

    .kp-alert-icon-circle.theme-blue {
        background-color: #e0f2fe !important;
        color: #005792 !important;
        border: 1px solid #bae6fd !important;
    }

    .kp-alert-icon-circle.theme-emerald {
        background-color: #dcfce7 !important;
        color: #059669 !important;
        border: 1px solid #bbf7d0 !important;
    }

    .kp-alert-icon-circle.theme-amber {
        background-color: #fef3c7 !important;
        color: #d97706 !important;
        border: 1px solid #fde68a !important;
    }

    .kp-alert-header-text {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        padding-right: 28px !important;
    }

    .kp-alert-heading {
        font-size: 17px !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        line-height: 1.35 !important;
        margin: 0 !important;
        letter-spacing: -0.015em !important;
    }

    .kp-alert-desc {
        font-size: 13.5px !important;
        color: #475569 !important;
        line-height: 1.5 !important;
        margin-top: 4px !important;
        font-weight: 500 !important;
    }

    .kp-alert-close-btn {
        position: absolute !important;
        top: 0 !important;
        right: 0 !important;
        width: 30px !important;
        height: 30px !important;
        border-radius: 8px !important;
        border: none !important;
        background: transparent !important;
        color: #94a3b8 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
        padding: 0 !important;
    }

    .kp-alert-close-btn:hover {
        background-color: #f1f5f9 !important;
        color: #1e293b !important;
    }

    .kp-alert-close-btn svg {
        width: 17px !important;
        height: 17px !important;
    }

    /* Structured Summary Card (Untuk Simpan Transaksi & Detail Ekstra) */
    .kp-save-summary-card {
        background: #f8fafc !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 14px !important;
        padding: 12px 14px !important;
        margin-top: 14px !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 7px !important;
        font-size: 13px !important;
    }

    .kp-save-summary-row {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
    }

    .kp-save-summary-row .label {
        color: #64748b !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.03em !important;
    }

    .kp-save-summary-row .value {
        color: #0f172a !important;
        font-weight: 700 !important;
        text-align: right !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        max-width: 250px !important;
    }

    .kp-save-summary-divider {
        height: 1px !important;
        background-color: #e2e8f0 !important;
        margin: 3px 0 !important;
    }

    .kp-save-summary-row.highlight .label {
        color: #0f172a !important;
        font-weight: 700 !important;
    }

    .kp-save-summary-row.highlight .value {
        color: #005792 !important;
        font-size: 15px !important;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        font-weight: 800 !important;
    }

    .kp-badge-pill {
        display: inline-flex !important;
        align-items: center !important;
        padding: 2px 8px !important;
        border-radius: 6px !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        letter-spacing: 0.02em !important;
    }
    .kp-badge-pill.tunai {
        background-color: #ecfdf5 !important;
        color: #047857 !important;
        border: 1px solid #a7f3d0 !important;
    }
    .kp-badge-pill.kredit {
        background-color: #eff6ff !important;
        color: #1d4ed8 !important;
        border: 1px solid #bfdbfe !important;
    }
    .kp-badge-pill.lunas {
        background-color: #ecfdf5 !important;
        color: #047857 !important;
        border: 1px solid #a7f3d0 !important;
    }
    .kp-badge-pill.piutang {
        background-color: #eff6ff !important;
        color: #1d4ed8 !important;
        border: 1px solid #bfdbfe !important;
    }
    .kp-badge-pill.dp {
        background-color: #fffbeb !important;
        color: #b45309 !important;
        border: 1px solid #fde68a !important;
    }

    /* Action Buttons (High Contrast & Tactile) */
    .kp-alert-btn-cancel {
        min-height: 40px !important;
        padding: 0 16px !important;
        border-radius: 10px !important;
        border: 1.5px solid #cbd5e1 !important;
        background-color: #ffffff !important;
        color: #334155 !important;
        font-size: 13.5px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.15s ease !important;
        font-family: inherit !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05) !important;
    }

    .kp-alert-btn-cancel:hover {
        background-color: #f8fafc !important;
        border-color: #94a3b8 !important;
        color: #0f172a !important;
    }

    .kp-alert-btn-confirm {
        min-height: 40px !important;
        padding: 0 18px !important;
        border-radius: 10px !important;
        border: none !important;
        color: #ffffff !important;
        font-size: 13.5px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        transition: all 0.15s ease !important;
        font-family: inherit !important;
    }

    .kp-alert-btn-confirm.theme-rose {
        background-color: #e11d48 !important;
        box-shadow: 0 2px 8px rgba(225, 29, 72, 0.3) !important;
    }
    .kp-alert-btn-confirm.theme-rose:hover {
        background-color: #be123c !important;
    }

    .kp-alert-btn-confirm.theme-blue {
        background-color: #005792 !important;
        box-shadow: 0 2px 8px rgba(0, 87, 146, 0.3) !important;
    }
    .kp-alert-btn-confirm.theme-blue:hover {
        background-color: #004270 !important;
    }

    .kp-alert-btn-confirm.theme-emerald {
        background-color: #059669 !important;
        box-shadow: 0 2px 8px rgba(5, 150, 105, 0.3) !important;
    }
    .kp-alert-btn-confirm.theme-emerald:hover {
        background-color: #047857 !important;
    }

    .kp-alert-btn-confirm.theme-amber {
        background-color: #d97706 !important;
        box-shadow: 0 2px 8px rgba(217, 119, 6, 0.3) !important;
    }
    .kp-alert-btn-confirm.theme-amber:hover {
        background-color: #b45309 !important;
    }
</style>

<script>
    window.AppAlert = {
        _escapeHtml: function(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        },

        /**
         * Template Side-by-Side: Icon & Judul sejajar tanpa ruang samping kosong
         */
        _buildTemplate: function({ theme, title, iconSvg, descHtml, extraHtml }) {
            const safeTitle = this._escapeHtml(title);

            return `
                <div class="kp-alert-header-row">
                    <div class="kp-alert-icon-circle theme-${theme}">
                        ${iconSvg}
                    </div>
                    <div class="kp-alert-header-text">
                        <h4 class="kp-alert-heading">${safeTitle}</h4>
                        ${descHtml ? `<div class="kp-alert-desc">${descHtml}</div>` : ''}
                    </div>
                    <button type="button" class="kp-alert-close-btn" onclick="Swal.close()" title="Tutup">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                ${extraHtml ? extraHtml : ''}
            `;
        },

        /**
         * 1. Alert Sukses
         */
        success: function(options = {}) {
            const title = options.title || 'Berhasil';
            const message = options.message || options.text || 'Operasi berhasil disimpan.';
            const hasCetak = !!options.hasCetak;
            const timer = hasCetak ? false : (options.timer !== undefined ? options.timer : 2500);
            const showButton = hasCetak || (options.showConfirmButton !== undefined ? options.showConfirmButton : false);
            const buttonText = options.confirmText || options.buttonText || (hasCetak ? 'Lihat Bukti Kas & Cetak' : 'Selesai');

            return Swal.fire({
                showConfirmButton: showButton,
                showCancelButton: false,
                buttonsStyling: false,
                confirmButtonText: buttonText,
                timer: timer,
                timerProgressBar: false,
                customClass: {
                    container: 'kp-alert-backdrop',
                    popup: 'kp-alert-popup',
                    confirmButton: 'kp-alert-btn-confirm theme-emerald'
                },
                html: this._buildTemplate({
                    theme: 'emerald',
                    title: title,
                    iconSvg: `
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    `,
                    descHtml: typeof message === 'string' ? this._escapeHtml(message) : message
                })
            });
        },

        /**
         * Closing Success Modal Dialog Khusus Kasir
         */
        closingSuccess: function(options = {}) {
            const title = options.title || 'Kas Berhasil Ditutup!';
            const message = options.message || options.text || 'Shift kasir telah berhasil ditutup.';
            const confirmText = options.confirmText || 'Selesai & Lanjutkan';
            const summary = options.summary;

            let extraHtml = '';
            if (summary && typeof summary === 'object') {
                extraHtml = `
                    <div class="kp-save-summary-box" style="margin-top: 14px;">
                        ${summary.tanggal ? `<div class="kp-save-summary-row"><span class="label">Tanggal Shift</span><span class="value">${this._escapeHtml(String(summary.tanggal))}</span></div>` : ''}
                        ${summary.kasir ? `<div class="kp-save-summary-row"><span class="label">Kasir</span><span class="value">${this._escapeHtml(String(summary.kasir))}</span></div>` : ''}
                        ${summary.saldo_awal !== undefined ? `<div class="kp-save-summary-row"><span class="label">Saldo Awal</span><span class="value font-mono">Rp ${new Intl.NumberFormat('id-ID').format(summary.saldo_awal)}</span></div>` : ''}
                        ${summary.total_masuk !== undefined ? `<div class="kp-save-summary-row"><span class="label">Total Masuk</span><span class="value font-mono text-emerald-600">+Rp ${new Intl.NumberFormat('id-ID').format(summary.total_masuk)}</span></div>` : ''}
                        ${summary.total_keluar !== undefined ? `<div class="kp-save-summary-row"><span class="label">Total Keluar</span><span class="value font-mono text-rose-600">-Rp ${new Intl.NumberFormat('id-ID').format(summary.total_keluar)}</span></div>` : ''}
                        <div class="kp-save-summary-divider"></div>
                        ${summary.saldo_akhir !== undefined ? `<div class="kp-save-summary-row highlight"><span class="label">Saldo Akhir</span><span class="value font-mono">Rp ${new Intl.NumberFormat('id-ID').format(summary.saldo_akhir)}</span></div>` : ''}
                    </div>
                `;
            }

            return Swal.fire({
                showConfirmButton: true,
                showCancelButton: false,
                buttonsStyling: false,
                confirmButtonText: confirmText,
                customClass: {
                    container: 'kp-alert-backdrop',
                    popup: 'kp-alert-popup',
                    confirmButton: 'kp-alert-btn-confirm theme-emerald'
                },
                html: this._buildTemplate({
                    theme: 'emerald',
                    title: title,
                    iconSvg: `
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    `,
                    descHtml: typeof message === 'string' ? this._escapeHtml(message) : message,
                    extraHtml: extraHtml
                })
            });
        },

        /**
         * 2. Alert Peringatan / Warning
         */
        warning: function(options = {}) {
            const title = options.title || 'Perhatian';
            const message = options.message || options.text || 'Periksa kembali data Anda.';
            const buttonText = options.buttonText || 'Mengerti';

            return Swal.fire({
                showConfirmButton: true,
                showCancelButton: false,
                buttonsStyling: false,
                confirmButtonText: buttonText,
                customClass: {
                    container: 'kp-alert-backdrop',
                    popup: 'kp-alert-popup',
                    confirmButton: 'kp-alert-btn-confirm theme-amber'
                },
                html: this._buildTemplate({
                    theme: 'amber',
                    title: title,
                    iconSvg: `
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    `,
                    descHtml: typeof message === 'string' ? this._escapeHtml(message) : message
                })
            });
        },

        /**
         * 3. Alert Error / Kesalahan
         */
        error: function(options = {}) {
            const title = options.title || 'Terjadi Kesalahan';
            const message = options.message || options.text || 'Silakan coba kembali beberapa saat lagi.';
            const buttonText = options.buttonText || 'Tutup';

            return Swal.fire({
                showConfirmButton: true,
                showCancelButton: false,
                buttonsStyling: false,
                confirmButtonText: buttonText,
                customClass: {
                    container: 'kp-alert-backdrop',
                    popup: 'kp-alert-popup',
                    confirmButton: 'kp-alert-btn-confirm theme-rose'
                },
                html: this._buildTemplate({
                    theme: 'rose',
                    title: title,
                    iconSvg: `
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    `,
                    descHtml: typeof message === 'string' ? this._escapeHtml(message) : message
                })
            });
        },

        /**
         * 4. Konfirmasi Hapus Data
         */
        confirmDelete: function(options = {}) {
            const title = options.title || 'Hapus Data?';
            const target = options.target ? this._escapeHtml(options.target) : '';
            const confirmText = options.confirmText || 'Ya, Hapus Data';
            const cancelText = options.cancelText || 'Batal';
            const onConfirm = options.onConfirm;
            const desc = options.text || (target
                ? `Data <strong>${target}</strong> akan dihapus permanen dari sistem.`
                : 'Data yang telah dihapus tidak dapat dipulihkan kembali.');

            return Swal.fire({
                showConfirmButton: true,
                showCancelButton: true,
                reverseButtons: true,
                buttonsStyling: false,
                confirmButtonText: confirmText,
                cancelButtonText: cancelText,
                customClass: {
                    container: 'kp-alert-backdrop',
                    popup: 'kp-alert-popup',
                    cancelButton: 'kp-alert-btn-cancel',
                    confirmButton: 'kp-alert-btn-confirm theme-rose'
                },
                html: this._buildTemplate({
                    theme: 'rose',
                    title: title,
                    iconSvg: `
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    `,
                    descHtml: desc
                })
            }).then((result) => {
                if (result.isConfirmed && typeof onConfirm === 'function') {
                    onConfirm();
                }
                return result;
            });
        },

        /**
         * 5. Konfirmasi Logout
         */
        confirmLogout: function(options = {}) {
            const onConfirm = options.onConfirm;

            return Swal.fire({
                showConfirmButton: true,
                showCancelButton: true,
                reverseButtons: true,
                buttonsStyling: false,
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                customClass: {
                    container: 'kp-alert-backdrop',
                    popup: 'kp-alert-popup',
                    cancelButton: 'kp-alert-btn-cancel',
                    confirmButton: 'kp-alert-btn-confirm theme-rose'
                },
                html: this._buildTemplate({
                    theme: 'rose',
                    title: 'Keluar dari Sistem?',
                    iconSvg: `
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                    `,
                    descHtml: 'Sesi kerja Anda saat ini akan diakhiri. Pastikan data yang sedang dikerjakan telah tersimpan.'
                })
            }).then((result) => {
                if (result.isConfirmed && typeof onConfirm === 'function') {
                    onConfirm();
                }
                return result;
            });
        },

        /**
         * 6. Konfirmasi Batal / Tinggalkan Halaman (Unsaved Changes)
         */
        confirmLeave: function(options = {}) {
            const title = options.title || 'Tinggalkan Halaman?';
            const message = options.message || options.text || 'Data transaksi yang sudah Anda isi belum disimpan dan akan hilang jika Anda keluar.';
            const confirmText = options.confirmText || 'Tinggalkan';
            const cancelText = options.cancelText || 'Lanjut Mengisi';
            const onConfirm = options.onConfirm;

            return Swal.fire({
                showConfirmButton: true,
                showCancelButton: true,
                reverseButtons: true,
                buttonsStyling: false,
                confirmButtonText: confirmText,
                cancelButtonText: cancelText,
                customClass: {
                    container: 'kp-alert-backdrop',
                    popup: 'kp-alert-popup',
                    cancelButton: 'kp-alert-btn-cancel',
                    confirmButton: 'kp-alert-btn-confirm theme-rose'
                },
                html: this._buildTemplate({
                    theme: 'rose',
                    title: title,
                    iconSvg: `
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zm8.303 3.376c.866 1.5-.217 3.374-1.948 3.374H5.645c-1.73 0-2.813-1.874-1.948-3.374L10.051 4.878c.866-1.5 3.032-1.5 3.898 0l6.354 11.248z" />
                        </svg>
                    `,
                    descHtml: typeof message === 'string' ? this._escapeHtml(message) : message
                })
            }).then((result) => {
                if (result.isConfirmed && typeof onConfirm === 'function') {
                    onConfirm();
                }
                return result;
            });
        },

        /**
         * 7. Konfirmasi Simpan Transaksi Baru (Dengan Kartu Ringkasan Terstruktur & Bebas Bug Tag HTML)
         */
        confirmSaveTransaction: function(options = {}) {
            const title = options.title || 'Simpan Transaksi?';
            const confirmText = options.confirmText || 'Ya, Simpan Transaksi';
            const cancelText = options.cancelText || 'Periksa Kembali';
            const onConfirm = options.onConfirm;

            let extraHtml = '';

            // Jika options menyediakan object summary terstruktur
            if (options.summary && typeof options.summary === 'object') {
                const s = options.summary;
                const safeNama = this._escapeHtml(s.nama || s.pemasang || '-');
                const safeProduk = this._escapeHtml(s.produk || s.jenisIklan || '-');
                const safeSkema = this._escapeHtml(s.skema || s.jenis_pembayaran || 'Tunai');
                const isTunai = safeSkema.toLowerCase().includes('tunai');
                const safeTotal = this._escapeHtml(s.total || 'Rp 0');
                const safeBayar = this._escapeHtml(s.bayar || 'Rp 0');
                const safeStatus = this._escapeHtml(s.status || (isTunai ? 'Lunas' : 'Piutang'));
                const statusClass = safeStatus.toLowerCase().includes('lunas') ? 'lunas' : (safeStatus.toLowerCase().includes('dp') ? 'dp' : 'piutang');

                extraHtml = `
                    <div class="kp-save-summary-card">
                        <div class="kp-save-summary-row">
                            <span class="label">Pemesan</span>
                            <span class="value" title="${safeNama}">${safeNama}</span>
                        </div>
                        <div class="kp-save-summary-row">
                            <span class="label">Jenis Iklan</span>
                            <span class="value" title="${safeProduk}">${safeProduk}</span>
                        </div>
                        <div class="kp-save-summary-row">
                            <span class="label">Pembayaran</span>
                            <span class="value">
                                <span class="kp-badge-pill ${isTunai ? 'tunai' : 'kredit'}">${safeSkema}</span>
                            </span>
                        </div>
                        <div class="kp-save-summary-divider"></div>
                        <div class="kp-save-summary-row highlight">
                            <span class="label">Total Tagihan</span>
                            <span class="value">${safeTotal}</span>
                        </div>
                        <div class="kp-save-summary-row">
                            <span class="label">Jumlah Bayar</span>
                            <span class="value font-mono">
                                ${safeBayar} <span class="kp-badge-pill ${statusClass}" style="margin-left: 4px;">${safeStatus}</span>
                            </span>
                        </div>
                    </div>
                `;
            } else if (options.text || options.message) {
                // Fallback teks biasa (membersihkan jika ada tag strong lama agar tidak diekspos mentah)
                const rawText = options.text || options.message || '';
                extraHtml = `<div class="kp-alert-desc" style="margin-top: 10px;">${rawText}</div>`;
            }

            return Swal.fire({
                showConfirmButton: true,
                showCancelButton: true,
                reverseButtons: true,
                buttonsStyling: false,
                confirmButtonText: confirmText,
                cancelButtonText: cancelText,
                customClass: {
                    container: 'kp-alert-backdrop',
                    popup: 'kp-alert-popup',
                    cancelButton: 'kp-alert-btn-cancel',
                    confirmButton: 'kp-alert-btn-confirm theme-blue'
                },
                html: this._buildTemplate({
                    theme: 'blue',
                    title: title,
                    iconSvg: `
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    `,
                    descHtml: (options.summary && (options.message || options.desc)) 
                        ? this._escapeHtml(options.message || options.desc) 
                        : (options.summary ? 'Pastikan seluruh data pesanan dan rincian transaksi sudah sesuai sebelum disimpan.' : ''),
                    extraHtml: extraHtml
                })
            }).then((result) => {
                if (result.isConfirmed && typeof onConfirm === 'function') {
                    onConfirm();
                }
                return result;
            });
        },

        /**
         * 8. Konfirmasi Simpan Perubahan Standar
         */
        confirmSave: function(options = {}) {
            const title = options.title || 'Simpan Perubahan?';
            const target = options.target ? this._escapeHtml(options.target) : '';
            const confirmText = options.confirmText || 'Ya, Simpan';
            const onConfirm = options.onConfirm;
            const desc = options.text || (target
                ? `Apakah Anda yakin ingin menyimpan perubahan pada data <strong>${target}</strong>?`
                : 'Apakah Anda yakin ingin menyimpan perubahan data ini ke dalam sistem?');

            return Swal.fire({
                showConfirmButton: true,
                showCancelButton: true,
                reverseButtons: true,
                buttonsStyling: false,
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                customClass: {
                    container: 'kp-alert-backdrop',
                    popup: 'kp-alert-popup',
                    cancelButton: 'kp-alert-btn-cancel',
                    confirmButton: 'kp-alert-btn-confirm theme-blue'
                },
                html: this._buildTemplate({
                    theme: 'blue',
                    title: title,
                    iconSvg: `
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    `,
                    descHtml: desc
                })
            }).then((result) => {
                if (result.isConfirmed && typeof onConfirm === 'function') {
                    onConfirm();
                }
                return result;
            });
        },

        confirmAction: function(options = {}) {
            return this.confirmSave(options);
        }
    };

    // Helper Global Logout
    function confirmLogout() {
        AppAlert.confirmLogout({
            onConfirm: function() {
                const logoutForm = document.getElementById('logoutForm');
                if (logoutForm) {
                    logoutForm.submit();
                }
            }
        });
    }
</script>
