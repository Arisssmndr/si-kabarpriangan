<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Administrator (Superadmin) selalu memiliki izin akses ke seluruh divisi/modul
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Cek apakah role user cocok dengan salah satu role yang diizinkan
        if (!empty($roles) && $user->hasRole($roles)) {
            return $next($request);
        }

        // Khusus Petugas Inkaso: Selalu arahkan ke Portal Mobile Inkaso Lapangan jika membuka halaman divisi desktop lain
        if ($user->hasRole('petugas_inkaso') || $user->hasRole('inkaso')) {
            return redirect()->route('keuangan.inkaso.mobile');
        }

        // Jika tidak memiliki akses, tolak dengan HTTP 403 Forbidden (Anti-Tembak URL / RBAC Hardening)
        abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk mengakses halaman divisi tersebut.');
    }
}
