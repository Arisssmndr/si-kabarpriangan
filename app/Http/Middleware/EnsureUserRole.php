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

        // Jika tidak memiliki akses, arahkan kembali ke dashboard divisinya sendiri
        return redirect()->route('dashboard')->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk mengakses halaman divisi tersebut.');
    }
}
