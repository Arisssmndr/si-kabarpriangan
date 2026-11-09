<?php

namespace App\Http\Middleware;

use App\Models\ShiftKasir;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnsurePreviousShiftClosed
{
    /**
     * Cegah bypass kunci shift kasir (Shift Lock Enforcement via URL).
     * Jika shift kasir hari sebelumnya masih berstatus open/aktif (belum ditutup),
     * paksa redirect ke halaman Tutup Kas Harian (kasir.utilitas.closing) dengan pesan error.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Schema::hasTable('shift_kasirs')) {
            return $next($request);
        }

        // Izinkan akses ke halaman tutup kasir (closing) agar kasir dapat menyelesaikan penutupan shift gantung
        if ($request->routeIs('kasir.utilitas.closing*', 'kasir.tutup-kasir')) {
            return $next($request);
        }

        $unclosedShift = ShiftKasir::where('kasir_id', Auth::id())
            ->whereIn('status', ['open', 'aktif'])
            ->where(function ($q) {
                $q->whereDate('tanggal_shift', '<', Carbon::today()->toDateString())
                  ->orWhereDate('tgl_shift', '<', Carbon::today()->toDateString());
            })
            ->orderBy('id', 'asc')
            ->first();

        if ($unclosedShift) {
            $tgl = Carbon::parse($unclosedShift->tanggal_shift ?? $unclosedShift->tgl_shift)->format('d/m/Y');
            $pesan = "Penguncian Shift Aktif: Shift kasir tanggal {$tgl} belum ditutup (status masih OPEN). Silakan lakukan Tutup Kas Harian terlebih dahulu sebelum membuka atau menginput transaksi baru.";

            return redirect()->route('kasir.utilitas.closing')->with('error', $pesan);
        }

        return $next($request);
    }
}
