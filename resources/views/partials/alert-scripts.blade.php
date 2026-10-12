<style>
    /* Backdrop Modal dengan Blur Halus */
    .swal2-container.kp-alert-backdrop {
        background: rgba(15, 23, 42, 0.45) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
        z-index: 99999 !important;
    }

    /* Popup Dialog Utama - Proporsional Ideal (Gak Kebesaran, Gak Kekecilan) */
    .swal2-popup.kp-alert-popup {
        width: 430px !important;
        max-width: calc(100vw - 32px) !important;
        padding: 24px !important;
        border-radius: 24px !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 20px 50px -12px rgba(15, 23, 42, 0.15), 0 8px 20px -4px rgba(15, 23, 42, 0.06) !important;
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
        box-sizing: border-box !important;
    }

    .swal2-popup.kp-alert-popup .swal2-html-container {
        margin: 0 !important;
        padding: 0 !important;
        text-align: left !important;
        overflow: visible !important;
    }

    .swal2-popup.kp-alert-popup .swal2-actions {
        margin: 20px 0 0 0 !important;
        padding: 0 !important;
        width: 100% !important;
        display: flex !important;
        justify-content: flex-end !important;
        align-items: center !important;
        gap: 10px !important;
    }

    .swal2-popup.kp-alert-popup .swal2-icon,
    .swal2-popup.kp-alert-popup .swal2-title {
        display: none !important;
    }

    /* Header Bar */
    .kp-alert-header {
        display: flex !important;
        align-items: center !important;
        gap: 14px !important;
        margin-bottom: 18px !important;
    }

    /* Squircle Icon Container - Proporsional 50x50 */
    .kp-alert-icon-box {
        width: 50px !important;
        height: 50px !important;
        min-width: 50px !important;
        min-height: 50px !important;
        border-radius: 16px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        box-sizing: border-box !important;
    }

    .kp-alert-icon-box svg {
        width: 24px !important;
        height: 24px !important;
        min-width: 24px !important;
        min-height: 24px !important;
        display: block !important;
    }

    /* Varian Warna Icon Box */
    .kp-alert-icon-box.theme-rose {
        background-color: #fff1f2 !important;
        border: 1.5px solid #ffe4e6 !important;
        color: #e11435 !important;
    }

    .kp-alert-icon-box.theme-blue {
        background-color: #f0f7fd !important;
        border: 1.5px solid #ddeefa !important;
        color: #0a72ac !important;
    }

    .kp-alert-icon-box.theme-emerald {
        background-color: #ecfdf5 !important;
        border: 1.5px solid #d1fae5 !important;
        color: #059669 !important;
    }

    /* Area Teks Judul & Subjudul */
    .kp-alert-title-wrap {
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        min-width: 0 !important;
        flex: 1 !important;
    }

    .kp-alert-title {
        font-size: 16.5px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 1.3 !important;
        margin: 0 !important;
        padding: 0 !important;
        letter-spacing: -0.01em !important;
    }

    .kp-alert-subtitle {
        font-size: 13px !important;
        color: #64748b !important;
        font-weight: 400 !important;
        line-height: 1.35 !important;
        margin: 3px 0 0 0 !important;
        padding: 0 !important;
    }

    /* Card Inset Pesan - Proporsional & Pas */
    .kp-alert-card {
        background-color: #f8fafc !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 15px !important;
        padding: 15px 18px !important;
        font-size: 13px !important;
        color: #334155 !important;
        line-height: 1.55 !important;
        text-align: left !important;
        margin: 0 !important;
        box-sizing: border-box !important;
    }

    /* Tombol Batal */
    .kp-alert-btn-cancel {
        margin: 0 !important;
        height: 40px !important;
        padding: 0 20px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #334155 !important;
        background-color: #ffffff !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 13px !important;
        transition: all 0.15s ease !important;
        cursor: pointer !important;
        outline: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-sizing: border-box !important;
    }

    .kp-alert-btn-cancel:hover {
        background-color: #f8fafc !important;
        color: #0f172a !important;
        border-color: #cbd5e1 !important;
    }

    /* Tombol Konfirmasi Aksi dengan Icon Sesuai Foto 2 */
    .kp-alert-btn-confirm {
        margin: 0 !important;
        height: 40px !important;
        padding: 0 20px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 13px !important;
        transition: all 0.15s ease !important;
        cursor: pointer !important;
        outline: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 7px !important;
        box-sizing: border-box !important;
    }

    .kp-alert-btn-confirm svg {
        width: 15px !important;
        height: 15px !important;
        flex-shrink: 0 !important;
    }

    .kp-alert-btn-confirm.theme-rose {
        background-color: #e11435 !important;
        box-shadow: 0 3px 10px rgba(225, 20, 53, 0.22) !important;
    }

    .kp-alert-btn-confirm.theme-rose:hover {
        background-color: #be123c !important;
        box-shadow: 0 5px 14px rgba(225, 20, 53, 0.32) !important;
    }

    .kp-alert-btn-confirm.theme-blue {
        background-color: #0a72ac !important;
        box-shadow: 0 3px 10px rgba(10, 114, 172, 0.22) !important;
    }

    .kp-alert-btn-confirm.theme-blue:hover {
        background-color: #085c8d !important;
        box-shadow: 0 5px 14px rgba(10, 114, 172, 0.32) !important;
    }

    .kp-alert-btn-confirm.theme-emerald {
        background-color: #059669 !important;
        box-shadow: 0 3px 10px rgba(5, 150, 105, 0.22) !important;
    }

    .kp-alert-btn-confirm.theme-emerald:hover {
        background-color: #047857 !important;
        box-shadow: 0 5px 14px rgba(5, 150, 105, 0.32) !important;
    }
</style>

<script>
    window.AppAlert = {
        /**
         * 1. Validasi Hapus Data (Merah / Rose - Proporsional Ideal Sesuai Foto 2)
         */
        confirmDelete: function(options = {}) {
            const title = options.title || 'Hapus Transaksi';
            const subtitle = options.subtitle || 'Tindakan ini tidak dapat dibatalkan';
            const target = options.target || '';
            const confirmText = options.confirmText || 'Ya, Hapus';
            const contentText = options.text || (target 
                ? `Apakah Anda yakin ingin menghapus data <strong style="color: #e11435; font-weight: 700;">${target}</strong>?`
                : 'Apakah Anda yakin ingin menghapus data ini dari sistem?');

            return Swal.fire({
                showConfirmButton: true,
                showCancelButton: true,
                reverseButtons: true,
                buttonsStyling: false,
                confirmButtonText: `
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>${confirmText}</span>
                `,
                cancelButtonText: 'Batal',
                customClass: {
                    container: 'kp-alert-backdrop',
                    popup: 'kp-alert-popup',
                    cancelButton: 'kp-alert-btn-cancel',
                    confirmButton: 'kp-alert-btn-confirm theme-rose'
                },
                html: `
                    <div class="kp-alert-header">
                        <div class="kp-alert-icon-box theme-rose">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <div class="kp-alert-title-wrap">
                            <h4 class="kp-alert-title">${title}</h4>
                            <p class="kp-alert-subtitle">${subtitle}</p>
                        </div>
                    </div>
                    <div class="kp-alert-card">
                        ${contentText}
                    </div>
                `
            });
        },

        /**
         * 2. Validasi Logout (Merah / Rose - Proporsional Ideal Sesuai Foto 2)
         */
        confirmLogout: function(options = {}) {
            const onConfirm = options.onConfirm;

            return Swal.fire({
                showConfirmButton: true,
                showCancelButton: true,
                reverseButtons: true,
                buttonsStyling: false,
                confirmButtonText: `
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                    <span>Ya, Keluar</span>
                `,
                cancelButtonText: 'Batal',
                customClass: {
                    container: 'kp-alert-backdrop',
                    popup: 'kp-alert-popup',
                    cancelButton: 'kp-alert-btn-cancel',
                    confirmButton: 'kp-alert-btn-confirm theme-rose'
                },
                html: `
                    <div class="kp-alert-header">
                        <div class="kp-alert-icon-box theme-rose">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                        </div>
                        <div class="kp-alert-title-wrap">
                            <h4 class="kp-alert-title">Keluar dari Sistem</h4>
                            <p class="kp-alert-subtitle">Sesi Anda saat ini akan diakhiri</p>
                        </div>
                    </div>
                    <div class="kp-alert-card">
                        Apakah Anda yakin ingin keluar dari akun ini?
                    </div>
                `
            }).then((result) => {
                if (result.isConfirmed && typeof onConfirm === 'function') {
                    onConfirm();
                }
                return result;
            });
        },

        /**
         * 3. Konfirmasi Simpan Pas Update Data (Biru Kabar Priangan - Proporsional Ideal)
         */
        confirmSave: function(options = {}) {
            const title = options.title || 'Simpan Perubahan';
            const subtitle = options.subtitle || 'Data akan diperbarui ke sistem';
            const target = options.target || '';
            const confirmText = options.confirmText || 'Ya, Simpan';
            const contentText = options.text || (target 
                ? `Apakah Anda yakin ingin menyimpan perubahan pada data <strong style="color: #0a72ac; font-weight: 700;">${target}</strong>?`
                : 'Apakah Anda yakin ingin menyimpan perubahan data ini?');

            return Swal.fire({
                showConfirmButton: true,
                showCancelButton: true,
                reverseButtons: true,
                buttonsStyling: false,
                confirmButtonText: `
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>${confirmText}</span>
                `,
                cancelButtonText: 'Batal',
                customClass: {
                    container: 'kp-alert-backdrop',
                    popup: 'kp-alert-popup',
                    cancelButton: 'kp-alert-btn-cancel',
                    confirmButton: 'kp-alert-btn-confirm theme-blue'
                },
                html: `
                    <div class="kp-alert-header">
                        <div class="kp-alert-icon-box theme-blue">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div class="kp-alert-title-wrap">
                            <h4 class="kp-alert-title">${title}</h4>
                            <p class="kp-alert-subtitle">${subtitle}</p>
                        </div>
                    </div>
                    <div class="kp-alert-card">
                        ${contentText}
                    </div>
                `
            });
        },

        /**
         * 4. Login Gagal / Alert Error (Merah / Rose - Proporsional Ideal)
         */
        error: function(options = {}) {
            const title = options.title || 'Gagal Masuk';
            const subtitle = options.subtitle || 'Autentikasi tidak berhasil';
            const message = options.message || 'Email atau kata sandi yang Anda masukkan salah.';
            const buttonText = options.buttonText || 'Coba Lagi';

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
                html: `
                    <div class="kp-alert-header">
                        <div class="kp-alert-icon-box theme-rose">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="kp-alert-title-wrap">
                            <h4 class="kp-alert-title">${title}</h4>
                            <p class="kp-alert-subtitle">${subtitle}</p>
                        </div>
                    </div>
                    <div class="kp-alert-card">
                        ${message}
                    </div>
                `
            });
        },

        /**
         * 5. Alert Sukses (Hijau / Emerald - Proporsional Ideal)
         */
        success: function(options = {}) {
            const title = options.title || 'Berhasil';
            const subtitle = options.subtitle || 'Data telah diperbarui';
            const message = options.message || 'Operasi berhasil disimpan.';
            const timer = options.timer !== undefined ? options.timer : 2000;

            return Swal.fire({
                showConfirmButton: false,
                timer: timer,
                timerProgressBar: true,
                customClass: {
                    container: 'kp-alert-backdrop',
                    popup: 'kp-alert-popup'
                },
                html: `
                    <div class="kp-alert-header">
                        <div class="kp-alert-icon-box theme-emerald">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="kp-alert-title-wrap">
                            <h4 class="kp-alert-title">${title}</h4>
                            <p class="kp-alert-subtitle">${subtitle}</p>
                        </div>
                    </div>
                    <div class="kp-alert-card">
                        ${message}
                    </div>
                `
            });
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
