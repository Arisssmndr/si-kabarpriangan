<x-guest-layout>
    <style>
        /* Standar Industri: Pastikan tombol submit disabled tetap putih bersih di semua browser (Chrome, Edge, Safari) */
        #loginForm button[type="submit"]:disabled,
        #loginForm button[type="submit"]:disabled * {
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
        }
    </style>

    <!-- Tombol Navigasi Kembali ke Landing Page (Simpel, Bersih & Standar Modern) -->
    <div class="mb-3">
        <a href="{{ url('/') }}" 
           class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-[#0A72AC] transition duration-150 group">
            <svg class="w-4 h-4 text-slate-400 group-hover:text-[#0A72AC] transition-transform duration-150 group-hover:-translate-x-1" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Beranda</span>
        </a>
    </div>

    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xl p-7 sm:p-9" 
         x-data="loginState({{ (int) ($initialLockoutSeconds ?? (session('lockout_seconds') ?? 0)) }})">
        
        <!-- Logo Perusahaan di Dalam Kartu -->
        <div class="flex justify-center mb-4">
            <img src="{{ asset('images/kabarpriangan.png') }}" 
                 alt="Logo Kabar Priangan" 
                 class="h-14 w-auto object-contain" 
                 onerror="this.src='{{ asset('logo.png') }}'">
        </div>

        <!-- =====================================================================
             STATE 1: FORM LOGIN NORMAL (!isLocked)
             ===================================================================== -->
        <div x-show="!isLocked" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0 translate-y-1" 
             x-transition:enter-end="opacity-100 translate-y-0">
            <!-- Teks Judul & Deskripsi dengan Warna Kontras Tinggi -->
            <div class="text-center mb-5">
                <h2 class="text-[18px] font-bold text-slate-900 tracking-tight">
                    Masuk ke Akun Anda
                </h2>
                <p class="text-xs text-slate-600 font-medium mt-1">
                    Silakan masukkan email dan password untuk mengakses portal.
                </p>
            </div>

            <!-- Status Sesi Pesan -->
            <x-auth-session-status class="mb-4 text-xs text-emerald-800 bg-emerald-50 p-2.5 rounded-lg border border-emerald-300 font-semibold" :status="session('status')" />

            <!-- FORM LOGIN UTAMA: BERSIH, RAPI, TANPA TAMPILAN BERANTAKAN -->
            <form method="POST" action="{{ route('login') }}" id="loginForm" @submit="handleSubmit($event)" class="space-y-4">
                @csrf

                <!-- Email -->
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-bold text-slate-900">
                        Email
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username"
                           placeholder="Masukkan Email Anda"
                           class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs font-semibold text-red-600" />
                </div>

                <!-- Password dengan Tombol Lihat Password (Icon Mata) -->
                <div class="space-y-1">
                    <label for="password" class="block text-xs font-bold text-slate-900">
                        Password
                    </label>
                    <div class="relative">
                        <input id="password" 
                               :type="showPass ? 'text' : 'password'" 
                               name="password" 
                               required 
                               autocomplete="current-password"
                               placeholder="Masukkan Password"
                               class="w-full px-3.5 py-2.5 pr-11 border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-kp-blue-600 focus:ring-2 focus:ring-kp-blue-100 transition" />
                        <button type="button" 
                                @click="showPass = !showPass" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-800 focus:outline-none transition cursor-pointer"
                                :title="showPass ? 'Sembunyikan password' : 'Lihat password'"
                                tabindex="-1">
                            <!-- Eye Open (Lihat) -->
                            <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <!-- Eye Slash (Sembunyikan) -->
                            <svg x-show="showPass" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs font-semibold text-red-600" />
                </div>

                <!-- Remember Me (Ingat Saya) -->
                <div class="flex items-center pt-0.5">
                    <input id="remember_me" 
                           type="checkbox" 
                           name="remember" 
                           class="w-4 h-4 text-kp-blue-600 border-slate-300 rounded focus:ring-kp-blue-500 cursor-pointer" />
                    <label for="remember_me" class="ms-2 text-xs font-semibold text-slate-800 select-none cursor-pointer">
                        Ingat saya
                    </label>
                </div>

                <!-- Google reCAPTCHA v2 (Standar Industri: Terpasang Normal & Presisi) -->
                <div class="pt-2">
                    <div class="flex justify-center">
                        <div class="g-recaptcha inline-block" 
                             data-sitekey="{{ $recaptchaSiteKey ?? config('services.recaptcha.site_key') }}"
                             data-callback="onCaptchaVerified"
                             data-expired-callback="onCaptchaExpired"></div>
                    </div>
                    <!-- Inline Error Bersih Tanpa Alert Mengagetkan -->
                    <p x-show="captchaError" 
                       x-cloak 
                       x-transition 
                       class="text-xs text-rose-600 font-semibold text-center mt-2 flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Silakan centang verifikasi "I'm not a robot" terlebih dahulu.</span>
                    </p>
                    <x-input-error :messages="$errors->get('g-recaptcha-response')" class="mt-1 text-xs font-semibold text-red-600 text-center" />
                </div>

                <!-- Tombol Masuk -->
                <div class="pt-2">
                    <button type="submit" 
                            :disabled="isSubmitting"
                            class="w-full py-2.5 px-4 font-bold text-sm rounded-lg shadow-sm transition duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 flex items-center justify-center gap-2 text-white !text-white"
                            :class="isSubmitting ? 'bg-kp-blue-700 opacity-95 cursor-wait' : 'bg-kp-blue-600 hover:bg-kp-blue-700 active:bg-kp-blue-800 focus:ring-kp-blue-400 focus:ring-offset-2 cursor-pointer'"
                            style="color: #ffffff !important; -webkit-text-fill-color: #ffffff !important;">
                        <!-- Normal State -->
                        <span x-show="!isSubmitting" class="flex items-center gap-2 text-white" style="color: #ffffff !important; -webkit-text-fill-color: #ffffff !important;">
                            <span>Masuk</span>
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" style="color: #ffffff !important;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </span>

                        <!-- Loading / Sedang Memproses -->
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2 text-white" style="color: #ffffff !important; -webkit-text-fill-color: #ffffff !important;">
                            <svg class="w-4 h-4 animate-spin text-white shrink-0" fill="none" viewBox="0 0 24 24" style="color: #ffffff !important;">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-white font-bold" style="color: #ffffff !important; -webkit-text-fill-color: #ffffff !important;">Memproses...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- =====================================================================
             STATE 2: AKUN TERKUNCI / LOCKOUT (Standar Perbankan & Aplikasi Besar)
             - Form disembunyikan sepenuhnya (tidak ada form numpuk ke bawah)
             - Tanpa icon gembok merah kuno, menggunakan visual timer keamanan tenang
             - Kartu tetap compact, rapi, dan proporsional
             ===================================================================== -->
        <div x-show="isLocked" 
             x-cloak 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100"
             class="text-center py-2">
            
            <!-- Ikon Waktu Keamanan Elegan & Tenang (Standar Perbankan) -->
            <div class="mx-auto w-14 h-14 rounded-2xl bg-amber-50 border border-amber-200/80 flex items-center justify-center text-amber-600 mb-3 shadow-xs">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <!-- Judul & Keterangan -->
            <h2 class="text-lg font-bold text-slate-900 tracking-tight">
                Akses Masuk Dibatasi
            </h2>
            <p class="text-xs text-slate-600 font-medium mt-1 leading-relaxed max-w-xs mx-auto">
                Terlalu banyak percobaan masuk yang tidak sesuai. Demi keamanan akun, sistem membatasi percobaan masuk untuk sementara.
            </p>

            <!-- Kotak Countdown Mewah & Presisi -->
            <div class="my-5 p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">
                    Dapat Mencoba Kembali Dalam
                </span>
                <div class="font-mono text-3xl font-black text-slate-900 tracking-wider">
                    <span x-text="minutes">15</span>:<span x-text="seconds">00</span>
                </div>
                <!-- Progress Bar Halus -->
                <div class="w-full bg-slate-200 rounded-full h-1.5 mt-3 overflow-hidden">
                    <div class="bg-amber-500 h-1.5 rounded-full transition-all duration-1000 ease-linear"
                         :style="`width: ${progressPercent}%`"></div>
                </div>
            </div>

            <!-- Tombol Navigasi Alternatif -->
            <a href="{{ url('/') }}" 
               class="w-full py-2.5 px-4 font-bold text-xs rounded-lg border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 hover:text-slate-900 transition flex items-center justify-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Halaman Utama</span>
            </a>
        </div>
    </div>

    <!-- Script Logika Alpine.js: Standar Industri Tanpa Modal / Alert Mengagetkan -->
    <script>
        function loginState(initialLockout) {
            return {
                showPass: false,
                isSubmitting: false,
                isLocked: initialLockout > 0,
                lockoutSeconds: parseInt(initialLockout) || 0,
                captchaError: false,
                minutes: '00',
                seconds: '00',
                progressPercent: 100,
                timerInterval: null,

                init() {
                    window.loginComponent = this;

                    // Reset submitting state saat navigasi browser kembali (bfcache)
                    window.addEventListener('pageshow', () => {
                        this.isSubmitting = false;
                    });

                    // 1. Fitur Ingat Saya: Otomatis isi email dari cache lokal jika pernah dicentang
                    const savedEmail = localStorage.getItem('kp_saved_email');
                    const emailInput = document.getElementById('email');
                    const rememberCheckbox = document.getElementById('remember_me');

                    if (savedEmail && emailInput && !emailInput.value) {
                        emailInput.value = savedEmail;
                        if (rememberCheckbox) {
                            rememberCheckbox.checked = true;
                        }
                    }

                    // 2. Jika ada status jeda (Lockout 15 menit), mulai hitung mundur real-time
                    if (this.lockoutSeconds > 0) {
                        this.isLocked = true;
                        this.updateDisplay();
                        this.startTimer();
                    }
                },

                updateDisplay() {
                    const m = Math.floor(this.lockoutSeconds / 60);
                    const s = this.lockoutSeconds % 60;
                    this.minutes = String(m).padStart(2, '0');
                    this.seconds = String(s).padStart(2, '0');
                    this.progressPercent = Math.max(0, Math.min(100, (this.lockoutSeconds / 900) * 100));
                },

                startTimer() {
                    if (this.timerInterval) clearInterval(this.timerInterval);

                    this.timerInterval = setInterval(() => {
                        if (this.lockoutSeconds > 1) {
                            this.lockoutSeconds--;
                            this.updateDisplay();
                        } else {
                            this.lockoutSeconds = 0;
                            this.updateDisplay();
                            this.isLocked = false;
                            clearInterval(this.timerInterval);

                            if (typeof AppAlert !== 'undefined' && AppAlert.success) {
                                AppAlert.success({
                                    title: 'Akses Dibuka',
                                    subtitle: 'Proteksi Selesai',
                                    message: 'Waktu jeda telah selesai. Anda dapat mencoba login kembali.',
                                    timer: 3000
                                });
                            }
                        }
                    }, 1000);
                },

                handleSubmit(e) {
                    if (this.isLocked || this.isSubmitting) {
                        e.preventDefault();
                        return;
                    }

                    // Cek kelengkapan input email dan password secara bawaan browser
                    const emailInput = document.getElementById('email');
                    const passwordInput = document.getElementById('password');

                    if (!emailInput.value || !passwordInput.value) {
                        return; // Biarkan HTML5 browser validation bekerja
                    }

                    // Cek apakah reCAPTCHA sudah diverifikasi
                    let captchaResponse = '';
                    if (typeof grecaptcha !== 'undefined') {
                        try {
                            captchaResponse = grecaptcha.getResponse();
                        } catch (err) {
                            captchaResponse = '';
                        }
                    }

                    // Jika reCAPTCHA belum dicentang: tampilkan inline error halus (TANPA SweetAlert)
                    if (!captchaResponse) {
                        e.preventDefault();
                        this.captchaError = true;
                        return;
                    }

                    this.captchaError = false;

                    // Simpan email ke LocalStorage jika checkbox Ingat Saya dicentang
                    const rememberCheckbox = document.getElementById('remember_me');
                    if (rememberCheckbox && rememberCheckbox.checked && emailInput) {
                        localStorage.setItem('kp_saved_email', emailInput.value.trim());
                    } else {
                        localStorage.removeItem('kp_saved_email');
                    }

                    // Aktifkan loading state pada tombol
                    this.isSubmitting = true;
                }
            };
        }

        // Callback resmi saat pengguna mencentang reCAPTCHA
        window.onCaptchaVerified = function(token) {
            if (window.loginComponent) {
                window.loginComponent.captchaError = false;
            }
        };

        window.onCaptchaExpired = function() {
            if (window.loginComponent) {
                window.loginComponent.captchaError = true;
            }
        };
    </script>

    <!-- Feedback Notifikasi: Bebas dari Alert Mengagetkan untuk reCAPTCHA -->
    @if ($errors->any() || session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Jika error HANYA karena reCAPTCHA, jangan tampilkan SweetAlert!
                // Cukup aktifkan inline error halus agar bersih dan tidak mengagetkan
                @if ($errors->has('g-recaptcha-response') && ! $errors->has('email') && ! $errors->has('password') && ! session('error'))
                    if (window.loginComponent) {
                        window.loginComponent.captchaError = true;
                    }
                @else
                    const isLockout = {{ (session('lockout_seconds') || ($initialLockoutSeconds ?? 0) > 0) ? 'true' : 'false' }};
                    
                    if (isLockout) {
                        // Tidak memunculkan modal popup SweetAlert yang menghalangi layar dan menumpuk teks
                        // Tampilan hitung mundur mewah Kabar Priangan di dalam kartu sudah menyajikan informasi secara elegan
                    } else {
                        // Tampilkan error hanya jika email/password memang salah
                        AppAlert.error({
                            title: 'Login Gagal',
                            subtitle: 'Autentikasi Tidak Berhasil',
                            message: '{{ $errors->first('email') ?: ($errors->first('password') ?: session('error')) }}',
                            buttonText: 'Coba Lagi'
                        });
                    }
                @endif
            });
        </script>
    @endif
</x-guest-layout>


