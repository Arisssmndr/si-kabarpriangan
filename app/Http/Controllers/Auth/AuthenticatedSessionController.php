<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view with device lockout status & reCAPTCHA credentials.
     */
    public function create(Request $request): View
    {
        // 1. Pastikan token perangkat unik (Device ID) terpasang di cookie browser
        $deviceId = $request->cookie('kp_device_id');
        if (! $deviceId) {
            $deviceId = (string) Str::uuid();
            cookie()->queue(cookie()->forever('kp_device_id', $deviceId));
        }

        // 2. Cek apakah perangkat ini sedang dalam masa jeda 15 menit
        $deviceThrottleKey = Str::transliterate('device|' . $request->ip() . '|' . $deviceId);
        $lockoutSeconds = 0;

        if ($request->session()->has('lockout_seconds')) {
            $lockoutSeconds = (int) $request->session()->get('lockout_seconds');
        } elseif (RateLimiter::tooManyAttempts($deviceThrottleKey, 3)) {
            $lockoutSeconds = RateLimiter::availableIn($deviceThrottleKey);
        } elseif (config('app.debug') && $request->has('preview_lockout')) {
            $lockoutSeconds = (int) $request->get('preview_lockout', 896);
        }

        return view('auth.login', [
            'initialLockoutSeconds' => max(0, $lockoutSeconds),
            'recaptchaSiteKey' => config('services.recaptcha.site_key'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user) {
            \Illuminate\Support\Facades\Cache::forget('user-is-online-' . $user->id);
            $user->forceFill(['last_seen_at' => null])->saveQuietly();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
