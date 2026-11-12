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
     * Penegakan Kunci Shift Kasir Harian (Shift Lock Enforcement):
     * 1. Jika ada shift yang sedang dibuka kembali atas persetujuan resmi Accounting (reopen_status = approved & status = open), izinkan akses input/koreksi.
     * 2. Jika ada shift hari sebelumnya yang belum ditutup (status = open/aktif tanpa reopen_status = approved), paksa redirect ke halaman Tutup Kas Harian.
     * 3. Jika kas hari ini (tanggal berjalan) SUDAH DITUTUP (status = closed/ditutup), blokir seluruh menu & form input hari ini kecuali mendapat persetujuan (ACC) dari Accounting untuk membuka kembali kas.
     * 4. Saat pergantian ke hari berikutnya (misal besok tanggal 7), belum ada shift closed di tanggal baru sehingga otomatis terbuka untuk tanggal baru tersebut.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Schema::hasTable('shift_kasirs')) {
            return $next($request);
        }

        // Izinkan akses ke halaman tutup kasir (closing) & buka kembali kas (buka-kas)
        if ($request->routeIs('kasir.utilitas.closing*', 'kasir.tutup-kasir', 'kasir.utilitas.buka-kas*')) {
            return $next($request);
        }

        $todayStr = Carbon::today()->toDateString();

        // 1. Jika ada shift yang sedang dibuka kembali secara sah atas ACC Accounting, izinkan input/perbaikan
        $reopenedShift = ShiftKasir::whereIn('status', ['open', 'aktif'])
            ->where('reopen_status', 'approved')
            ->latest('id')
            ->first();

        if ($reopenedShift) {
            return $next($request);
        }

        // 2. Cek apakah ada shift gantung hari sebelumnya yang belum ditutup (tanpa ACC Re-open Accounting)
        $unclosedShift = ShiftKasir::where('kasir_id', Auth::id())
            ->whereIn('status', ['open', 'aktif'])
            ->where(function ($q) {
                $q->whereNull('reopen_status')
                  ->orWhere('reopen_status', '!=', 'approved');
            })
            ->where(function ($q) use ($todayStr) {
                $q->whereDate('tanggal_shift', '<', $todayStr)
                  ->orWhereDate('tgl_shift', '<', $todayStr);
            })
            ->orderBy('id', 'asc')
            ->first();

        if ($unclosedShift) {
            $tgl = Carbon::parse($unclosedShift->tanggal_shift ?? $unclosedShift->tgl_shift)->format('d/m/Y');
            $pesan = "Penguncian Shift Aktif: Shift kasir tanggal {$tgl} belum ditutup (status masih OPEN). Silakan lakukan Tutup Kas Harian terlebih dahulu sebelum membuka atau menginput transaksi baru.";

            return redirect()->route('kasir.utilitas.closing')->with('error', $pesan);
        }

        // 3. Cek apakah kas HARI INI sudah ditutup (Closed).
        //    Jika sudah ditutup pada hari ini, maka sisa jam di hari ini tidak bisa menginput apapun lagi kecuali minta persetujuan Accounting.
        $openTodayShift = ShiftKasir::whereIn('status', ['open', 'aktif'])
            ->where(function ($q) use ($todayStr) {
                $q->whereDate('tanggal_shift', $todayStr)
                  ->orWhereDate('tgl_shift', $todayStr);
            })
            ->latest('id')
            ->first();

        if (!$openTodayShift) {
            $closedTodayShift = ShiftKasir::whereIn('status', ['closed', 'ditutup'])
                ->where(function ($q) use ($todayStr) {
                    $q->whereDate('tanggal_shift', $todayStr)
                      ->orWhereDate('tgl_shift', $todayStr);
                })
                ->latest('id')
                ->first();

            if ($closedTodayShift) {
                $tgl = Carbon::parse($closedTodayShift->tanggal_shift ?? $closedTodayShift->tgl_shift ?? $todayStr)->format('d/m/Y');
                $jamTutup = $closedTodayShift->closed_at ? Carbon::parse($closedTodayShift->closed_at)->format('H:i') : '';
                $infoJam = $jamTutup ? " pada pukul {$jamTutup} WIB" : '';

                if ($closedTodayShift->reopen_status === 'requested' || $closedTodayShift->reopen_requested) {
                    $pesan = "Kas Harian tanggal {$tgl} telah ditutup{$infoJam} dan permohonan Buka Kembali Kas sedang menunggu persetujuan (ACC) dari Accounting. Anda belum dapat menginput transaksi sebelum disetujui Accounting.";
                } else {
                    $pesan = "Kas Harian tanggal {$tgl} telah ditutup{$infoJam}. Anda tidak dapat membuka menu atau menginput transaksi lagi untuk tanggal {$tgl} kecuali meminta persetujuan (ACC) dari Accounting untuk membuka kembali kas.";
                }

                return redirect()->route('kasir.utilitas.buka-kas')->with('error', $pesan);
            }
        }

        return $next($request);
    }
}
