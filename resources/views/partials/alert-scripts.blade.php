<style>
    /* ============================================================
       Alert & Konfirmasi - gaya ringkas dan modern
       ============================================================ */
    .swal2-container.kp-alert-backdrop {
        background: rgba(15, 23, 42, 0.40) !important;
        backdrop-filter: blur(2px) !important;
        -webkit-backdrop-filter: blur(2px) !important;
        z-index: 99999 !important;
    }

    /* Dialog */
    .swal2-popup.kp-alert-popup {
        width: 380px !important;
        max-width: calc(100vw - 32px) !important;
        padding: 24px 24px 20px !important;
        border-radius: 16px !important;
        background: #ffffff !important;
        border: none !important;
        box-shadow: 0 24px 48px -12px rgba(15, 23, 42, 0.22) !important;
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
        box-sizing: border-box !important;
        display: block !important;
        text-align: left !important;
    }

    .swal2-popup.kp-alert-popup .swal2-html-container {
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
        text-align: left !important;
    }

    .swal2-popup.kp-alert-popup .swal2-icon,
    .swal2-popup.kp-alert-popup .swal2-title {
        display: none !important;
    }

    .swal2-popup.kp-alert-popup .swal2-actions {
        margin: 20px 0 0 0 !important;
        padding: 0 !important;
        width: 100% !important;
        display: flex !important;
        gap: 8px !important;
    }

    .swal2-popup.kp-alert-popup .swal2-actions > button {
        flex: 1 1 0 !important;
    }

    .kp-alert-icon {
        width: 40px;
        height: 40px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
    }

    .kp-alert-icon svg {
        width: 20px;
        height: 20px;
    }

    .kp-alert-icon.theme-rose    { background: #fff1f2; color: #e11d48; }
    .kp-alert-icon.theme-amber   { background: #fffbeb; color: #d97706; }
    .kp-alert-icon.theme-blue    { background: #eff6ff; color: #2563eb; }
    .kp-alert-icon.theme-emerald { background: #ecfdf5; color: #059669; }

    .kp-alert-title {
        font-size: 16px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 1.35 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .kp-alert-body {
        font-size: 13.5px !important;
        line-height: 1.55 !important;
        color: #475569 !important;
        margin: 6px 0 0 0 !important;
        padding: 0 !important;
    }

    .kp-alert-body strong { color: #0f172a; font-weight: 600; }

    .kp-alert-note {
        font-size: 12.5px !important;
        color: #94a3b8 !important;
        margin: 6px 0 0 0 !important;
    }

    /* Tombol */
    .kp-alert-btn-cancel,
    .kp-alert-btn-confirm {
        margin: 0 !important;
        height: 38px !important;
        padding: 0 16px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        border-radius: 10px !important;
        cursor: pointer !important;
        outline: none !important;
        transition: background-color .15s ease, border-color .15s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-sizing: border-box !important;
        box-shadow: none !important;
    }

    .kp-alert-btn-cancel {
        color: #334155 !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
    }

    .kp-alert-btn-cancel:hover { background: #f8fafc !important; border-color: #cbd5e1 !important; }

    .kp-alert-btn-confirm { color: #ffffff !important; border: none !important; }
    .kp-alert-btn-confirm.theme-rose    { background: #e11d48 !important; }
    .kp-alert-btn-confirm.theme-rose:hover    { background: #be123c !important; }
    .kp-alert-btn-confirm.theme-amber   { background: #d97706 !important; }
    .kp-alert-btn-confirm.theme-amber:hover   { background: #b45309 !important; }
    .kp-alert-btn-confirm.theme-blue    { background: #2563eb !important; }
    .kp-alert-btn-confirm.theme-blue:hover    { background: #1d4ed8 !important; }
    .kp-alert-btn-confirm.theme-emerald { background: #059669 !important; }
    .kp-alert-btn-confirm.theme-emerald:hover { background: #047857 !important; }

    .kp-alert-btn-confirm:focus-visible,
    .kp-alert-btn-cancel:focus-visible {
        outline: 2px solid #93c5fd !important;
        outline-offset: 2px !important;
    }

    /* Toast (notifikasi sukses) */
    .swal2-popup.kp-toast {
        width: auto !important;
        min-width: 260px !important;
        max-width: 360px !important;
        padding: 12px 16px !important;
        border-radius: 12px !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 10px 25px -8px rgba(15, 23, 42, 0.18) !important;
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
    }

    .swal2-popup.kp-toast .swal2-html-container { margin: 0 !important; padding: 0 !important; overflow: visible !important; }
    .swal2-popup.kp-toast .swal2-timer-progress-bar { background: #10b981 !important; height: 2px !important; }

    .kp-toast-inner { display: flex; align-items: flex-start; gap: 10px; text-align: left; }
    .kp-toast-icon {
        width: 22px; height: 22px; border-radius: 999px; flex-shrink: 0; margin-top: 1px;
        display: flex; align-items: center; justify-content: center;
        background: #d1fae5; color: #059669;
    }
    .kp-toast-icon svg { width: 13px; height: 13px; }
    .kp-toast-title { font-size: 13.5px; font-weight: 600; color: #0f172a; line-height: 1.3; }
    .kp-toast-msg   { font-size: 12.5px; color: #64748b; line-height: 1.45; margin-top: 2px; }

    /* Mode gelap */
    html.dark .swal2-popup.kp-alert-popup,
    html.dark .swal2-popup.kp-toast {
        background: #1e293b !important;
        border: 1px solid #334155 !important;
        box-shadow: 0 24px 48px -12px rgba(0, 0, 0, 0.5) !important;
    }
    html.dark .kp-alert-title,
    html.dark .kp-toast-title { color: #f1f5f9 !important; }
    html.dark .kp-alert-body  { color: #cbd5e1 !important; }
    html.dark .kp-alert-body strong { color: #ffffff; }
    html.dark .kp-alert-note,
    html.dark .kp-toast-msg   { color: #94a3b8 !important; }
    html.dark .kp-alert-icon.theme-rose    { background: rgba(244, 63, 94, .15);  color: #fda4af; }
    html.dark .kp-alert-icon.theme-amber   { background: rgba(245, 158, 11, .15); color: #fcd34d; }
    html.dark .kp-alert-icon.theme-blue    { background: rgba(59, 130, 246, .15); color: #93c5fd; }
    html.dark .kp-alert-icon.theme-emerald { background: rgba(16, 185, 129, .15); color: #6ee7b7; }
    html.dark .kp-toast-icon { background: rgba(16, 185, 129, .18); color: #6ee7b7; }
    html.dark .kp-alert-btn-cancel { background: #334155 !important; border-color: #475569 !important; color: #f1f5f9 !important; }
    html.dark .kp-alert-btn-cancel:hover { background: #3b4a61 !important; }
</style>

<script>
    (function () {
        const ICONS = {
            trash:   'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
            warning: 'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z',
            logout:  'M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9',
            check:   'M5 13l4 4L19 7',
            info:    'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            lock:    'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        };

        const svg = (path, w = 1.8) =>
            `<svg fill="none" stroke="currentColor" stroke-width="${w}" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="${path}"/></svg>`;

        const dialog = ({ theme, icon, title, text, note, confirmText, cancelText, showCancel = true }) =>
            Swal.fire({
                showConfirmButton: true,
                showCancelButton: showCancel,
                reverseButtons: true,
                buttonsStyling: false,
                focusCancel: showCancel,
                confirmButtonText: confirmText,
                cancelButtonText: cancelText || 'Batal',
                customClass: {
                    container: 'kp-alert-backdrop',
                    popup: 'kp-alert-popup',
                    cancelButton: 'kp-alert-btn-cancel',
                    confirmButton: 'kp-alert-btn-confirm theme-' + theme,
                },
                html: `
                    <div class="kp-alert-icon theme-${theme}">${svg(ICONS[icon])}</div>
                    <h4 class="kp-alert-title">${title}</h4>
                    <p class="kp-alert-body">${text}</p>
                    ${note ? `<p class="kp-alert-note">${note}</p>` : ''}
                `,
            });

        window.AppAlert = {
            /** Konfirmasi umum. theme: rose | amber | blue | emerald */
            confirm: function (o = {}) {
                return dialog({
                    theme: o.theme || 'blue',
                    icon: o.icon || 'info',
                    title: o.title || 'Konfirmasi',
                    text: o.text || 'Lanjutkan tindakan ini?',
                    note: o.note || '',
                    confirmText: o.confirmText || 'Ya, Lanjutkan',
                    cancelText: o.cancelText,
                });
            },

            /** Konfirmasi hapus */
            confirmDelete: function (o = {}) {
                return dialog({
                    theme: 'rose',
                    icon: 'trash',
                    title: o.title || 'Hapus data?',
                    text: o.text || (o.target
                        ? `Data <strong>${o.target}</strong> akan dihapus dari sistem.`
                        : 'Data ini akan dihapus dari sistem.'),
                    note: 'Tindakan ini tidak dapat dibatalkan.',
                    confirmText: o.confirmText || 'Hapus',
                });
            },

            /** Konfirmasi keluar akun */
            confirmLogout: function (o = {}) {
                return dialog({
                    theme: 'rose',
                    icon: 'logout',
                    title: 'Keluar dari akun?',
                    text: 'Sesi Anda akan diakhiri.',
                    confirmText: 'Keluar',
                }).then((result) => {
                    if (result.isConfirmed && typeof o.onConfirm === 'function') o.onConfirm();
                    return result;
                });
            },

            /** Konfirmasi simpan */
            confirmSave: function (o = {}) {
                return dialog({
                    theme: 'blue',
                    icon: 'check',
                    title: o.title || 'Simpan perubahan?',
                    text: o.text || (o.target
                        ? `Perubahan pada <strong>${o.target}</strong> akan disimpan.`
                        : 'Perubahan data akan disimpan.'),
                    confirmText: o.confirmText || 'Simpan',
                });
            },

            /** Pesan gagal / peringatan (satu tombol) */
            error: function (o = {}) {
                return dialog({
                    theme: 'rose',
                    icon: 'warning',
                    title: o.title || 'Terjadi kesalahan',
                    text: o.message || 'Operasi tidak dapat diproses.',
                    confirmText: o.buttonText || 'Tutup',
                    showCancel: false,
                });
            },

            /** Notifikasi sukses: toast kecil di pojok kanan atas, hilang otomatis */
            success: function (o = {}) {
                return Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: o.timer !== undefined ? o.timer : 2000,
                    timerProgressBar: true,
                    didOpen: (t) => {
                        t.addEventListener('mouseenter', Swal.stopTimer);
                        t.addEventListener('mouseleave', Swal.resumeTimer);
                    },
                    customClass: { popup: 'kp-toast' },
                    html: `
                        <div class="kp-toast-inner">
                            <div class="kp-toast-icon">${svg(ICONS.check, 2.4)}</div>
                            <div>
                                <div class="kp-toast-title">${o.title || 'Berhasil'}</div>
                                ${o.message ? `<div class="kp-toast-msg">${o.message}</div>` : ''}
                            </div>
                        </div>
                    `,
                });
            },
        };

        /**
         * Pengganti confirm() bawaan browser.
         * Tambahkan data-confirm pada <form> atau <button>/<a>:
         *   data-confirm="Teks pesan"
         *   data-confirm-title, data-confirm-ok, data-confirm-theme (rose|amber|blue|emerald)
         */
        function readOpts(el) {
            return {
                title: el.dataset.confirmTitle,
                text: el.dataset.confirm,
                confirmText: el.dataset.confirmOk,
                theme: el.dataset.confirmTheme,
                icon: el.dataset.confirmIcon,
            };
        }

        document.addEventListener('click', function (e) {
            const el = e.target.closest('button[data-confirm], a[data-confirm], input[type=submit][data-confirm]');
            if (!el || el.dataset.confirmed === '1') return;
            e.preventDefault();
            e.stopImmediatePropagation();

            AppAlert.confirm(readOpts(el)).then((r) => {
                if (!r.isConfirmed) return;
                el.dataset.confirmed = '1';
                if (el.tagName === 'A') {
                    window.location.href = el.href;
                } else {
                    // requestSubmit menjaga validasi HTML5 dan nilai tombol submit
                    const form = el.form;
                    if (form && form.requestSubmit) form.requestSubmit(el);
                    else el.click();
                }
                setTimeout(() => { delete el.dataset.confirmed; }, 0);
            });
        }, true);

        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!form.matches || !form.matches('form[data-confirm]') || form.dataset.confirmed === '1') return;
            e.preventDefault();

            AppAlert.confirm(readOpts(form)).then((r) => {
                if (!r.isConfirmed) return;
                form.dataset.confirmed = '1';
                HTMLFormElement.prototype.submit.call(form);
            });
        }, true);

        // Helper global logout
        window.confirmLogout = function () {
            AppAlert.confirmLogout({
                onConfirm: function () {
                    const f = document.getElementById('logoutForm');
                    if (f) f.submit();
                },
            });
        };
    })();
</script>
