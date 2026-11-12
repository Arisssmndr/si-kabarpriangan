<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenugasanInkaso extends Model
{
    use HasFactory;

    protected $table = 'penugasan_inkaso';

    protected $fillable = [
        'no_tugas',
        'faktur_id',
        'petugas_id',
        'tanggal_tugas',
        'status_tugas',
        'catatan',
    ];

    protected $casts = [
        'tanggal_tugas' => 'date',
    ];

    /**
     * Faktur yang ditugaskan untuk ditagih.
     */
    public function faktur(): BelongsTo
    {
        return $this->belongsTo(Faktur::class, 'faktur_id');
    }

    /**
     * Petugas lapangan (Inkaso) yang mengemban tugas.
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    /**
     * Pembayaran setoran yang masuk melalui penugasan ini.
     */
    public function pembayaran(): HasMany
    {
        return $this->hasMany(PembayaranFaktur::class, 'penugasan_inkaso_id');
    }

    /**
     * Nama kolektor lapangan (mendukung penagih manual non-akun maupun user sistem).
     */
    public function getNamaKolektorAttribute(): string
    {
        if (!empty($this->catatan) && preg_match('/^\[Kolektor:\s*([^\]]+)\]/i', $this->catatan, $matches)) {
            return trim($matches[1]);
        }

        return $this->petugas->name ?? 'Petugas Inkaso Manual';
    }

    /**
     * Catatan penugasan tanpa prefix metadata kolektor.
     */
    public function getCatatanBersihAttribute(): ?string
    {
        if (empty($this->catatan)) {
            return null;
        }

        $clean = trim(preg_replace('/^\[Kolektor:\s*[^\]]+\]\s*/i', '', $this->catatan));

        return $clean !== '' ? $clean : null;
    }
}
