<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountActive
{
    /**
     * Handle an incoming request, verify account status is not Disable,
     * and maintain real-time online status for active sessions.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user && $user->isDisable()) {
                Cache::forget('user-is-online-' . $user->id);
                $user->forceFill(['last_seen_at' => null])->saveQuietly();

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Sesi Anda telah diputus karena akun ini dinonaktifkan oleh Administrator.');
            }

            // Catat status online/aktif di cache & DB saat staf sedang berada di dalam aplikasi
            if ($user) {
                Cache::put('user-is-online-' . $user->id, true, now()->addMinutes(3));

                // Throttle pembaruan database setiap 60 detik atau jika atribut jejak perangkat belum terisi
                $lastSeen = $user->last_seen_at;
                $needsDeviceSync = empty($user->last_login_device)
                    || empty($user->last_login_browser)
                    || empty($user->last_login_ip)
                    || in_array($user->last_login_ip, ['127.0.0.1', '::1'], true);

                if (! $lastSeen || now()->diffInSeconds($lastSeen) > 60 || $needsDeviceSync) {
                    $resolvedIp = \App\Support\ClientDeviceInspector::resolveIp($request, $user->last_login_ip);
                    $deviceName = \App\Support\ClientDeviceInspector::resolveDeviceName($request->userAgent(), $resolvedIp, $request);
                    $browserName = \App\Support\ClientDeviceInspector::resolveBrowser($request->userAgent(), $request);

                    $updates = [
                        'last_seen_at' => now(),
                        'last_login_ip' => $resolvedIp,
                        'last_login_device' => $deviceName !== '-' ? $deviceName : $user->last_login_device,
                        'last_login_browser' => $browserName !== '-' ? $browserName : $user->last_login_browser,
                        'last_login_user_agent' => substr((string) $request->userAgent(), 0, 500),
                    ];

                    if (! $user->last_login_at) {
                        $updates['last_login_at'] = now();
                    }

                    $user->forceFill($updates)->saveQuietly();
                }
            }
        }

        return $next($request);
    }
}
