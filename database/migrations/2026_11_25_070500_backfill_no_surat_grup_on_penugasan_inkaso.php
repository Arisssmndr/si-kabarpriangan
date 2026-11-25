<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill kolom no_surat_grup untuk data penugasan_inkaso yang masih NULL.
     * Penugasan dengan petugas_id dan tanggal_tugas yang sama digabungkan dalam 1 nomor surat grup (ST-YYYYMM-XXX).
     */
    public function up(): void
    {
        $rows = DB::table('penugasan_inkaso')
            ->whereNull('no_surat_grup')
            ->orderBy('tanggal_tugas')
            ->orderBy('id')
            ->get();

        if ($rows->isEmpty()) {
            return;
        }

        // Ambil sequence tertinggi yang sudah ada per periode YYYYMM
        $existingSurats = DB::table('penugasan_inkaso')
            ->whereNotNull('no_surat_grup')
            ->pluck('no_surat_grup');

        $seqByPeriod = [];
        foreach ($existingSurats as $surat) {
            $parts = explode('-', (string) $surat);
            if (count($parts) >= 3) {
                $period = $parts[1];
                $num = (int) end($parts);
                $seqByPeriod[$period] = max($seqByPeriod[$period] ?? 0, $num);
            }
        }

        $grouped = $rows->groupBy(function ($row) {
            $tgl = $row->tanggal_tugas ? Carbon::parse($row->tanggal_tugas)->toDateString() : date('Y-m-d');
            return $row->petugas_id . '|' . $tgl . '|' . ($row->status_tugas === 'batal' ? 'batal' : 'aktif');
        });

        foreach ($grouped as $groupRows) {
            $first = $groupRows->first();
            $period = $first->tanggal_tugas ? Carbon::parse($first->tanggal_tugas)->format('Ym') : date('Ym');
            $next = ($seqByPeriod[$period] ?? 0) + 1;

            do {
                $noSurat = "ST-{$period}-" . str_pad($next, 3, '0', STR_PAD_LEFT);
                $next++;
            } while (DB::table('penugasan_inkaso')->where('no_surat_grup', $noSurat)->exists());

            $seqByPeriod[$period] = $next - 1;

            DB::table('penugasan_inkaso')
                ->whereIn('id', $groupRows->pluck('id')->all())
                ->update(['no_surat_grup' => $noSurat]);
        }
    }

    public function down(): void
    {
        // Non-destructive backfill
    }
};
