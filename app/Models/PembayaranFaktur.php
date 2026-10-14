<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranFaktur extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_faktur';

    protected $fillable = [
        'no_kwitansi',
        'faktur_id',
        'penugasan_inkaso_id',
        'tanggal_bayar',
        'nominal_bayar',
        'metode_bayar',
        'tipe_bayar',
        'diterima_oleh',
        'catatan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'nominal_bayar' => 'decimal:2',
    ];

    /**
     * Faktur terkait pembayaran ini.
     */
    public function faktur(): BelongsTo
    {
        return $this->belongsTo(Faktur::class, 'faktur_id');
    }

    /**
     * Penugasan inkaso (jika merupakan setoran inkaso).
     */
    public function penugasanInkaso(): BelongsTo
    {
        return $this->belongsTo(PenugasanInkaso::class, 'penugasan_inkaso_id');
    }

    /**
     * User kasir/admin yang menerima dan memverifikasi setoran.
     */
    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }
}
