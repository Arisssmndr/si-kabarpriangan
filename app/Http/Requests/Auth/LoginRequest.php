<?php

namespace App\Http\Requests\Auth;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Sesuai Standar Industri: Wajib isi email, password, dan reCAPTCHA v2.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'g-recaptcha-response' => ['required'],
        ];
    }

    /**
     * Pesan validasi yang ramah, jelas, dan non-teknis.
     */
    public function messages(): array
    {
        return [
            'g-recaptcha-response.required' => "Silakan centang 'Saya bukan robot' terlebih dahulu.",
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format penulisan email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ];
    }

    /**
     * Dapatkan pengenal unik perangkat (Device ID) dari cookie atau fingerprint.
     */
    public function deviceId(): string
    {
        $deviceId = $this->cookie('kp_device_id') ?? $this->header('X-Device-Id');
        if (! $deviceId) {
            $deviceId = md5($this->ip() . '|' . ($this->userAgent() ?? 'agent'));
        }
        return $deviceId;
    }

    /**
     * Kunci throttle untuk perangkat fisik / browser pengguna.
     */
    public function deviceThrottleKey(): string
    {
        return Str::transliterate('device|' . $this->ip() . '|' . $this->deviceId());
    }

    /**
     * Kunci throttle kombinasi Akun Target + Perangkat untuk mencegah DoS dan brute-force.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip() . '|' . $this->deviceId());
    }

    /**
     * Attempt to authenticate the request's credentials.
     * Standar Keamanan Perbankan / Enterprise:
     * - Verifikasi Google reCAPTCHA v2
     * - Batas 3 kali percobaan salah -> Jeda 15 menit (900 detik)
     * - Live countdown timer & pesan non-teknis ramah pengguna
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        // 1. Verifikasi Google reCAPTCHA v2 Checkbox ("Saya bukan robot")
        $this->verifyRecaptcha();

        $email = $this->string('email')->lower()->toString();
        $user = User::where('email', $email)->first();

        // 2. Cek apakah akun dinonaktifkan (Disable) oleh Administrator
        if ($user && $user->isDisable()) {
            ActivityLog::record(
                'LOGIN_REJECTED_DISABLED',
                "Percobaan login ke akun dinonaktifkan ({$user->name}) dari IP {$this->ip()}",
                null,
                $user,
                $email
            );

            throw ValidationException::withMessages([
                'email' => 'Akun ini telah dinonaktifkan oleh Administrator. Silakan hubungi Administrator untuk informasi lebih lanjut.',
            ]);
        }

        // 3. Cek apakah perangkat atau akun sedang dalam periode jeda 15 menit (Lockout)
        $this->ensureIsNotRateLimited($user);

        // 4. Eksekusi verifikasi password
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // Hit rate limiter dengan durasi 15 menit (900 detik)
            RateLimiter::hit($this->throttleKey(), 900);
            RateLimiter::hit($this->deviceThrottleKey(), 900);

            $attempts = max(
                RateLimiter::attempts($this->throttleKey()),
                RateLimiter::attempts($this->deviceThrottleKey())
            );

            if ($user) {
                $user->increment('failed_login_attempts');
            }

            // Aturan 3 Kali Gagal:
            if ($attempts >= 3) {
                if ($user) {
                    $user->update([
                        'locked_until' => now()->addMinutes(15),
                    ]);
                }

                event(new Lockout($this));
                session()->flash('lockout_seconds', 900);

                ActivityLog::record(
                    'ACCOUNT_LOCKED_15M',
                    "Akun dan perangkat dijeda selama 15 menit setelah 3 kali gagal login dari IP {$this->ip()}",
                    ['attempts' => $attempts],
                    $user,
                    $email
                );

                throw ValidationException::withMessages([
                    'email' => 'Akun dikunci sementara. Silakan coba lagi dalam 15 menit.',
                ]);
            }

            if ($attempts === 2) {
                ActivityLog::record(
                    'LOGIN_FAILED_2ND',
                    "Percobaan login ke-2 gagal untuk {$email} dari IP {$this->ip()}",
                    ['attempts' => 2],
                    $user,
                    $email
                );

                throw ValidationException::withMessages([
                    'email' => 'Email atau kata sandi salah. Sisa 1 kali percobaan.',
                ]);
            }

            // Percobaan ke-1 gagal
            ActivityLog::record(
                'LOGIN_FAILED_1ST',
                "Percobaan login ke-1 gagal untuk {$email} dari IP {$this->ip()}",
                ['attempts' => 1],
                $user,
                $email
            );

            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi salah. Sisa 2 kali percobaan.',
            ]);
        }

        // 5. Autentikasi Berhasil: Bersihkan seluruh throttle & kunci
        RateLimiter::clear($this->throttleKey());
        RateLimiter::clear($this->deviceThrottleKey());
        session()->forget('lockout_seconds');

        /** @var User $authenticatedUser */
        $authenticatedUser = Auth::user();
        $authenticatedUser->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => $this->ip(),
        ]);

        ActivityLog::record(
            'LOGIN_SUCCESS',
            "Pengguna {$authenticatedUser->name} ({$authenticatedUser->role_label}) berhasil login ke sistem",
            ['role' => $authenticatedUser->role, 'ip' => $this->ip()],
            $authenticatedUser,
            $authenticatedUser->email
        );
    }

    /**
     * Verifikasi token Google reCAPTCHA v2 Checkbox.
     */
    protected function verifyRecaptcha(): void
    {
        $recaptchaToken = $this->input('g-recaptcha-response');
        $secretKey = config('services.recaptcha.secret_key');

        try {
            $verifyResponse = Http::asForm()->timeout(5)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $recaptchaToken,
                'remoteip' => $this->ip(),
            ]);

            $captchaSuccess = $verifyResponse->successful() && ($verifyResponse->json('success') === true);
        } catch (\Throwable $e) {
            // Fallback development jika sedang offline / menggunakan Test Key Google
            $captchaSuccess = ($secretKey === '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe');
        }

        if (! $captchaSuccess) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => "Verifikasi 'Saya bukan robot' belum selesai atau kadaluarsa. Silakan centang kembali.",
            ]);
        }
    }

    /**
     * Memastikan request tidak sedang terkena jeda penguncian (Lockout 15 Menit).
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(?User $user = null): void
    {
        $isDbLocked = $user && $user->isLocked();
        $isThrottleLocked = RateLimiter::tooManyAttempts($this->throttleKey(), 3)
            || RateLimiter::tooManyAttempts($this->deviceThrottleKey(), 3);

        if (! $isDbLocked && ! $isThrottleLocked) {
            return;
        }

        // Hitung sisa detik penguncian
        $seconds = max(
            RateLimiter::availableIn($this->throttleKey()),
            RateLimiter::availableIn($this->deviceThrottleKey())
        );

        if ($user && $user->isLocked() && $user->locked_until->isFuture()) {
            $dbSeconds = now()->diffInSeconds($user->locked_until, false);
            $seconds = max($seconds, (int) $dbSeconds);
        }

        if ($seconds <= 0) {
            // Masa jeda telah selesai, reset kunci
            if ($user) {
                $user->update([
                    'failed_login_attempts' => 0,
                    'locked_until' => null,
                ]);
            }
            RateLimiter::clear($this->throttleKey());
            RateLimiter::clear($this->deviceThrottleKey());
            session()->forget('lockout_seconds');
            return;
        }

        event(new Lockout($this));
        session()->flash('lockout_seconds', $seconds);

        throw ValidationException::withMessages([
            'email' => 'Akun sedang dikunci sementara. Silakan coba lagi nanti.',
        ]);
    }
}

