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

                // Throttle pembaruan database setiap 60 detik agar performa tetap maksimal
                $lastSeen = $user->last_seen_at;
                if (! $lastSeen || now()->diffInSeconds($lastSeen) > 60) {
                    $user->forceFill(['last_seen_at' => now()])->saveQuietly();
                }
            }
        }

        return $next($request);
    }
}
