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
     * Cek apakah transaksi untuk tanggal terbit tertentu sedang terkunci oleh cut-off
     */
    public static function isDateLocked(string $date): bool
    {
        // Cek apakah ada Memo Unlock yang sedang OPEN untuk tanggal ini
        $hasActiveUnlock = MemoUnlock::where('tanggal_target', $date)
            ->where('status', 'OPEN')
            ->exists();

        if ($hasActiveUnlock) {
            return false;
        }

        // Cek kunci eksplisit di tabel daily_cutoffs
        $cutoff = self::where('tanggal_edisi', $date)->first();
        if ($cutoff && $cutoff->is_closed) {
            return true;
        }

        // Cek jam cutoff harian: Jika tanggal target adalah besok dan hari ini sudah melewati jam cutoff
        $besokStr = now()->addDay()->toDateString();
        if ($date === $besokStr && SistemConfig::isPastCutoff()) {
            return true;
        }

        return false;
    }
}
