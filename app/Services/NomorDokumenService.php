<?php

namespace App\Services;

use App\Models\Faktur;
use App\Models\PaketIklan;
use App\Models\PembayaranIklan;
use App\Models\PenomoranDokumen;
use App\Models\Sppi;
use App\Models\TransaksiKoran;
use App\Models\TransaksiOnline;
use App\Models\TransaksiPriangan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NomorDokumenService
{
    /**
     * Konversi kategori/media menjadi kode kanal faktur:
     * - CTK : Iklan Cetak (Koran)
     * - ONL : Iklan Online (Web & Portal)
     * - PTV : Iklan TV (Priangan TV)
     * - PKT : Paket Iklan Lintas Media
     */
    public static function normalizeMediaCode(string $kategori = 'CTK'): string
    {
        return match (strtoupper(trim($kategori))) {
            'ONL', 'ONLINE'         => 'ONL',
            'PTV', 'TV', 'PRIANGAN' => 'PTV',
            'PKT', 'PAKET'          => 'PKT',
            default                 => 'CTK',
        };
    }

    /**
     * Generate Nomor Faktur Iklan Standar Industri:
     * - Cetak  : FK/CTK/YYYY/MM/0001
     * - Online : FK/ONL/YYYY/MM/0002
     * - TV     : FK/PTV/YYYY/MM/0003
     * - Paket  : FK/PKT/YYYY/MM/0004
     * 
     * Nomor urut berjalan secara global per bulan (shared counter),
     * sehingga urutan nomor faktur tetap berkesinambungan lintas media.
     * Menggunakan transaksi database atomik dengan lockForUpdate()
     * 100% aman dari race condition ketika banyak staf menyimpan secara bersamaan.
     */
    public static function generateFaktur(?Carbon $date = null, string $kategori = 'CTK'): string
    {
        $mediaTag = self::normalizeMediaCode($kategori);
        $d = $date ? $date->copy() : Carbon::now();
        $tahun = (int) $d->format('Y');
        $bulan = (int) $d->format('n');
        $bulanPadded = sprintf('%02d', $bulan);

        return DB::transaction(function () use ($tahun, $bulan, $bulanPadded, $mediaTag) {
            $seq = PenomoranDokumen::where('tipe_dokumen', 'FAKTUR')
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->lockForUpdate()
                ->first();

            if (!$seq) {
                $seq = PenomoranDokumen::create([
                    'tipe_dokumen'   => 'FAKTUR',
                    'tahun'          => $tahun,
                    'bulan'          => $bulan,
                    'nomor_terakhir' => 0,
                    'prefix'         => 'FK',
                ]);
            }

            $next = $seq->nomor_terakhir + 1;
            $code = "FK/{$mediaTag}/{$tahun}/{$bulanPadded}/" . sprintf('%04d', $next);

            // Proteksi bentrok tambahan terhadap record historis
            while (
                TransaksiKoran::where('nofakturkoran', $code)->exists() ||
                TransaksiOnline::where('nofakturonline', $code)->exists() ||
                TransaksiPriangan::where('nofakturpriangan', $code)->exists() ||
                PaketIklan::where('nofaktur_paket', $code)->exists() ||
                Faktur::where('no_faktur', $code)->exists()
            ) {
                $next++;
                $code = "FK/{$mediaTag}/{$tahun}/{$bulanPadded}/" . sprintf('%04d', $next);
            }

            $seq->nomor_terakhir = $next;
            $seq->save();

            return $code;
        });
    }

    /**
     * Preview nomor faktur untuk tampilan form pembuatan baru (tanpa menghabiskan counter)
     */
    public static function previewFaktur(?Carbon $date = null, string $kategori = 'CTK'): string
    {
        $mediaTag = self::normalizeMediaCode($kategori);
        $d = $date ? $date->copy() : Carbon::now();
        $tahun = (int) $d->format('Y');
        $bulan = (int) $d->format('n');
        $bulanPadded = sprintf('%02d', $bulan);

        $seq = PenomoranDokumen::where('tipe_dokumen', 'FAKTUR')
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->first();

        $prospective = ($seq ? $seq->nomor_terakhir : 0) + 1;
        return "FK/{$mediaTag}/{$tahun}/{$bulanPadded}/" . sprintf('%04d', $prospective);
    }

    /**
     * Generate Nomor Kwitansi Pembayaran (KWT/YYYY/MM/0001)
     */
    public static function generateKwitansi(?Carbon $date = null): string
    {
        $d = $date ? $date->copy() : Carbon::now();
        $tahun = (int) $d->format('Y');
        $bulan = (int) $d->format('n');
        $bulanPadded = sprintf('%02d', $bulan);

        return DB::transaction(function () use ($tahun, $bulan, $bulanPadded) {
            $seq = PenomoranDokumen::where('tipe_dokumen', 'KWITANSI')
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->lockForUpdate()
                ->first();

            if (!$seq) {
                $seq = PenomoranDokumen::create([
                    'tipe_dokumen'   => 'KWITANSI',
                    'tahun'          => $tahun,
                    'bulan'          => $bulan,
                    'nomor_terakhir' => 0,
                    'prefix'         => 'KWT',
                ]);
            }

            $next = $seq->nomor_terakhir + 1;
            $code = "KWT/{$tahun}/{$bulanPadded}/" . sprintf('%04d', $next);

            while (PembayaranIklan::where('nokwitansi', $code)->exists()) {
                $next++;
                $code = "KWT/{$tahun}/{$bulanPadded}/" . sprintf('%04d', $next);
            }

            $seq->nomor_terakhir = $next;
            $seq->save();

            return $code;
        });
    }

    /**
     * Generate Nomor SPPI Pracetak (SPPI/YYYY/MM/0001)
     */
    public static function generateSppi(?Carbon $date = null): string
    {
        $d = $date ? $date->copy() : Carbon::now();
        $tahun = (int) $d->format('Y');
        $bulan = (int) $d->format('n');
        $bulanPadded = sprintf('%02d', $bulan);

        return DB::transaction(function () use ($tahun, $bulan, $bulanPadded) {
            $seq = PenomoranDokumen::where('tipe_dokumen', 'SPPI')
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->lockForUpdate()
                ->first();

            if (!$seq) {
                $seq = PenomoranDokumen::create([
                    'tipe_dokumen'   => 'SPPI',
                    'tahun'          => $tahun,
                    'bulan'          => $bulan,
                    'nomor_terakhir' => 0,
                    'prefix'         => 'SPPI',
                ]);
            }

            $next = $seq->nomor_terakhir + 1;
            $code = "SPPI/{$tahun}/{$bulanPadded}/" . sprintf('%04d', $next);

            while (Sppi::where('nomor_sppi', $code)->exists()) {
                $next++;
                $code = "SPPI/{$tahun}/{$bulanPadded}/" . sprintf('%04d', $next);
            }

            $seq->nomor_terakhir = $next;
            $seq->save();

            return $code;
        });
    }
}
