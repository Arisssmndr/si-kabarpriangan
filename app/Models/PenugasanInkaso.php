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
        'tanggal_lapor',
        'status_tugas',
        'hasil_kunjungan',
        'tanggal_janji_bayar',
        'nominal_diterima',
        'catatan',
        'keterangan_hasil',
        'dilaporkan_oleh',
    ];

    protected $casts = [
        'tanggal_tugas'       => 'date',
        'tanggal_lapor'       => 'date',
        'tanggal_janji_bayar' => 'date',
        'nominal_diterima'    => 'decimal:2',
    ];

    /**
     * Staf Keuangan yang mencatat laporan hasil penugasan (Bu Ulfa / Admin).
     */
    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dilaporkan_oleh');
    }

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
}
