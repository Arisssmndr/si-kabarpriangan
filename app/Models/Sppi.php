<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sppi extends Model
{
    use HasFactory;

    protected $table = 'sppi';

    protected $fillable = [
        'nomor_sppi',
        'media_type',
        'transaksi_id',
        'nofaktur',
        'tanggal_sppi',
        'status_sppi',
        'tanggal_ditandatangani',
        'ditandatangani_oleh_nama',
        'file_pdf_path',
        'catatan',
    ];

    protected $casts = [
        'tanggal_sppi' => 'date',
        'tanggal_ditandatangani' => 'date',
    ];

    public function transaksiKoran()
    {
        return $this->belongsTo(TransaksiKoran::class, 'transaksi_id');
    }

    /**
     * Generate Nomor SPPI Otomatis (Format: SPPI/KRN/2026/0001)
     */
    public static function createCode(string $mediaType = 'KRN'): string
    {
        return \App\Services\NomorDokumenService::generateSppi();
    }
}
