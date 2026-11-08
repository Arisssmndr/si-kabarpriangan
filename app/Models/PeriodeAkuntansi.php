<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeAkuntansi extends Model
{
    use HasFactory;

    protected $table = 'periode_akuntansi';

    protected $fillable = [
        'tahun',
        'bulan',
        'periode_key',
        'status',
        'ditutup_pada',
        'ditutup_oleh_user_id',
        'catatan',
    ];

    protected $casts = [
        'tahun'        => 'integer',
        'bulan'        => 'integer',
        'ditutup_pada' => 'datetime',
    ];

    public function ditutupOleh()
    {
        return $this->belongsTo(User::class, 'ditutup_oleh_user_id');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'ditutup_oleh_user_id');
    }

    public function getBulanTahunAttribute(): string
    {
        return $this->periode_key ?: sprintf('%04d-%02d', $this->tahun, $this->bulan);
    }

    public function getIsClosedAttribute(): bool
    {
        return $this->status === 'terkunci';
    }

    public function getClosedAtAttribute()
    {
        return $this->ditutup_pada;
    }

    /**
     * Cek apakah periode akuntansi (YYYY-MM) berstatus terkunci.
     */
    public static function isPeriodeLocked(string $periodeKey): bool
    {
        return self::where('periode_key', $periodeKey)
            ->where('status', 'terkunci')
            ->exists();
    }
}

