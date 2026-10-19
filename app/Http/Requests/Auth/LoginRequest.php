<?php

namespace App\Http\Requests\Auth;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     * Sesuai Standar Area 2: Aturan 3 Kali Gagal = Lockout 15 Menit + Status Review/Disable.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $email = $this->string('email')->lower()->toString();
        $user = User::where('email', $email)->first();

        // 1. Cek apakah akun dinonaktifkan (Disable) oleh Administrator
        if ($user && $user->isDisable()) {
            ActivityLog::record(
                'LOGIN_REJECTED_DISABLED',
                "Percobaan login ke akun dinonaktifkan ({$user->name}) dari IP {$this->ip()}",
                null,
                $user,
                $email
            );

            throw ValidationException::withMessages([
                'email' => 'Akun ini telah dinonaktifkan (Disable) oleh Administrator. Akses ditolak. Silakan hubungi Administrator untuk informasi lebih lanjut.',
            ]);
        }

        // 2. Cek apakah akun sedang dalam periode penguncian sementara (Lockout)
        $this->ensureIsNotRateLimited($user);

        // 3. Eksekusi verifikasi password
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // Hit rate limiter dengan masa expired 15 menit (900 detik)
            RateLimiter::hit($this->throttleKey(), 900);
            $attempts = RateLimiter::attempts($this->throttleKey());

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

                ActivityLog::record(
                    'ACCOUNT_LOCKED_15M',
                    "Akun dikunci sementara selama 15 menit setelah 3 kali gagal login berturut-turut dari IP {$this->ip()}",
                    ['attempts' => $attempts],
                    $user,
                    $email
                );

                throw ValidationException::withMessages([
                    'email' => 'Batas 3 kali percobaan gagal tercapai. Demi keamanan sistem informasi pers, akun Anda ditangguhkan sementara selama 15 menit. Silakan coba kembali nanti atau hubungi Administrator.',
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
                    'email' => 'Kata sandi salah (Percobaan ke-2). Peringatan keamanan: Akun akan dikunci selama 15 menit jika salah 1 kali lagi.',
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
                'email' => 'Email atau kata sandi tidak cocok. Silakan periksa kembali data Anda.',
            ]);
        }

        // 4. Autentikasi Berhasil
        RateLimiter::clear($this->throttleKey());

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
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(?User $user = null): void
    {
        // Cek lock di level database
        if ($user && $user->isLocked()) {
            $diffInSeconds = now()->diffInSeconds($user->locked_until, false);
            if ($diffInSeconds > 0) {
                $minutes = ceil($diffInSeconds / 60);

                throw ValidationException::withMessages([
                    'email' => "Akun Anda sedang ditangguhkan sementara selama 15 menit karena 3 kali percobaan gagal berturut-turut. Sisa waktu: {$minutes} menit lagi. Silakan coba kembali nanti atau hubungi Administrator.",
                ]);
            } else {
                // Kunci sudah kadaluarsa, buka kunci
                $user->update([
                    'failed_login_attempts' => 0,
                    'locked_until' => null,
                ]);
                RateLimiter::clear($this->throttleKey());
            }
        }

        // Cek lock di level RateLimiter cache
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 3)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());
        $minutes = ceil($seconds / 60);

        throw ValidationException::withMessages([
            'email' => "Terlalu banyak percobaan login yang gagal. Akses ke akun ini ditangguhkan sementara demi keamanan sistem. Sisa waktu jeda: {$minutes} menit lagi (atau hubungi Administrator).",
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     * Kombinasi Email + IP Klien untuk isolasi keamanan per penyerang.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
