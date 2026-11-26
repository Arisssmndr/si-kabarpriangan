<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyCutoff extends Model
{
    use HasFactory;

    protected $table = 'daily_cutoffs';

    protected $fillable = [
        'tanggal_edisi',
        'jam_cutoff',
        'is_closed',
        'closed_by_user_id',
        'closed_at',
        'catatan',
    ];

    protected $casts = [
        'tanggal_edisi' => 'date',
        'is_closed'     => 'boolean',
        'closed_at'     => 'datetime',
    ];

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    /**
     * Cek apakah transaksi untuk tanggal terbit/tayang tertentu sedang terkunci oleh cut-off.
     * Aturan Standar Industri (Berlaku Seragam untuk Iklan Cetak, Online & TV):
     * 1. Jika ada Memo Digital (MemoUnlock) berstatus OPEN untuk tanggal tersebut -> TERBUKA (false).
     * 2. Jika tanggal tersebut sudah ditutup eksplisit di DailyCutoff -> TERKUNCI (true).
     * 3. Jika tanggal terbit adalah Hari Ini (H) atau tanggal lampau (< H) -> TERKUNCI OTOMATIS (true),
     *    karena pemasangan iklan hari ini minimal untuk terbit Besok (H+1).
     * 4. Tanggal Besok (H+1) dan seterusnya -> TERBUKA (false) selama belum ditutup eksplisit.
     */
    public static function isDateLocked(string $date): bool
    {
        $cleanDate = \Carbon\Carbon::parse($date)->toDateString();

        // 1. Cek apakah ada Memo Unlock Digital yang sedang OPEN untuk tanggal ini
        $hasActiveUnlock = MemoUnlock::whereDate('tanggal_target', $cleanDate)
            ->where('status', 'OPEN')
            ->exists();

        if ($hasActiveUnlock) {
            return false;
        }

        // 2. Cek kunci eksplisit di tabel daily_cutoffs
        $cutoff = self::whereDate('tanggal_edisi', $cleanDate)->first();
        if ($cutoff && $cutoff->is_closed) {
            return true;
        }

        // 3. Tanggal Hari Ini (H) atau sebelumnya (< H) terkunci otomatis untuk terbit hari yang sama
        $todayStr = now('Asia/Jakarta')->toDateString();
        if ($cleanDate <= $todayStr) {
            return true;
        }

        return false;
    }
}
