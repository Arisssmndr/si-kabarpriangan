<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SirkulasiRetur extends Model
{
    use HasFactory;

    protected $table = 'sirkulasi_returs';

    protected $fillable = [
        'no_bapr',
        'periode_bulan',
        'agen_id',
        'kode_agen',
        'nama_agen',
        'tanggal_retur',
        'jumlah_retur_fisik',
        'jumlah_retur_disetujui',
        'catatan_kondisi',
        'status',
        'diverifikasi_oleh',
    ];

    protected $casts = [
        'tanggal_retur'          => 'date',
        'jumlah_retur_fisik'     => 'integer',
        'jumlah_retur_disetujui' => 'integer',
    ];

    public function agen(): BelongsTo
    {
        return $this->belongsTo(Agen::class, 'agen_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    /**
     * Generate nomor BAPR (Berita Acara Pengembalian Retur)
     * Contoh: BAPR-SRK-202611-001
     */
    public static function generateNoBapr(string $periodeBulan): string
    {
        $cleanPeriode = str_replace('-', '', $periodeBulan);
        $count = self::where('periode_bulan', $periodeBulan)->count() + 1;
        return sprintf('BAPR-SRK-%s-%03d', $cleanPeriode, $count);
    }
}
