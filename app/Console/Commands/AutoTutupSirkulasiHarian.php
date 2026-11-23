<?php

namespace App\Console\Commands;

use App\Http\Controllers\SirkulasiController;
use App\Models\DailyCutoff;
use App\Models\MemoUnlock;
use App\Models\SistemConfig;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AutoTutupSirkulasiHarian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sirkulasi:auto-tutup-harian 
                            {--date= : Tanggal edisi tertentu yang ingin ditutup (Format: YYYY-MM-DD, default hari ini)}
                            {--force : Paksa tutup tanpa mengecek batas jam cutoff}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis menutup data penjualan harian sirkulasi sesuai jam cut-off operasional dinamis (Daily Cut-Off Lock)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $targetDate = $this->option('date') ?: Carbon::today()->toDateString();
        $force = (bool) $this->option('force');
        $jamCutoff = class_exists(SistemConfig::class) ? SistemConfig::getJamCutoffSirkulasi() : '17:00';
        $currentTime = Carbon::now()->format('H:i');

        $this->info("Menjalankan tugas auto-cutoff penjualan sirkulasi harian untuk edisi {$targetDate}...");
        $this->info("Batas jam cutoff sirkulasi: {$jamCutoff} WIB (Waktu server saat ini: {$currentTime} WIB)");

        // Jika hari ini dan tidak ada flag force, cek apakah sudah mencapai jam cutoff
        if (!$force && $targetDate === Carbon::today()->toDateString() && $currentTime < $jamCutoff) {
            $this->warn("Belum mencapai jam cutoff sirkulasi ({$jamCutoff} WIB). Proses dibatalkan. Gunakan --force jika ingin menutup paksa.");
            return Command::SUCCESS;
        }

        // Cek apakah ada Memo Unlock yang sedang OPEN dan APPROVED oleh Akuntansi
        if (Schema::hasTable('memo_unlocks')) {
            $hasApprovedMemo = MemoUnlock::whereDate('tanggal_target', $targetDate)
                ->where('tujuan_divisi', 'sirkulasi')
                ->where('status', 'OPEN')
                ->whereIn('status_akuntansi', ['APPROVED', 'DISETUJUI', 'TERKIRIM'])
                ->exists();

            if ($hasApprovedMemo) {
                $this->warn("Edisi {$targetDate} memiliki Memo Unlock resmi yang disetujui Akuntansi. Auto-cutoff dilewati.");
                Log::info("Auto-cutoff sirkulasi harian dilewati untuk tanggal {$targetDate} karena ada Memo Unlock OPEN.");
                return Command::SUCCESS;
            }
        }

        // Cek apakah sudah ditutup sebelumnya
        $isAlreadyClosed = session('locked_harian_' . $targetDate, Cache::get('locked_harian_' . $targetDate, false));
        if (!$isAlreadyClosed && Schema::hasTable('daily_cutoffs')) {
            $existing = DailyCutoff::whereDate('tanggal_edisi', $targetDate)->first();
            if ($existing && $existing->is_closed) {
                $isAlreadyClosed = true;
            }
        }

        if ($isAlreadyClosed) {
            $this->info("Data penjualan harian sirkulasi tanggal {$targetDate} sudah dalam status tertutup.");
            return Command::SUCCESS;
        }

        // Eksekusi penutupan otomatis
        $operator = "Sistem Otomatis (Cut-Off {$jamCutoff} WIB)";
        SirkulasiController::executeTutupHarian($targetDate, $operator);

        $this->info("Berhasil! Penjualan harian sirkulasi tanggal {$targetDate} telah DITUTUP & DIKUNCI secara otomatis oleh {$operator}.");
        Log::info("Penjualan harian sirkulasi tanggal {$targetDate} ditutup otomatis oleh {$operator}.");

        return Command::SUCCESS;
    }
}
