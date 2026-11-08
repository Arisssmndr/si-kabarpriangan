<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisIklan extends Model
{
    use HasFactory;

    protected $table = 'jenis_iklan';

    protected $fillable = [
        'kategori_media_id',
        'kode_jenis',
        'nama_jenis',
        'tipe_dasar',
        'mode_warna',
        'cara_hitung',
        'tarif_dasar',
        'is_fixed_size',
        'default_kolom',
        'default_tinggi_mm',
        'max_kolom',
        'max_tinggi_mm',
        'min_order',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'tarif_dasar'       => 'integer',
        'is_fixed_size'     => 'boolean',
        'default_kolom'     => 'integer',
        'default_tinggi_mm' => 'integer',
        'max_kolom'         => 'integer',
        'max_tinggi_mm'     => 'integer',
        'min_order'         => 'integer',
        'is_active'         => 'boolean',
    ];

    /**
     * Relasi ke kategori media induknya
     */
    public function kategoriMedia()
    {
        return $this->belongsTo(KategoriMedia::class, 'kategori_media_id');
    }

    /**
     * Format Rupiah untuk tampilan tarif
     */
    public function getTarifRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->tarif_dasar ?? 0, 0, ',', '.');
    }

    /**
     * Accessor Kompatibilitas untuk relasi transaksi lama
     */
    public function getJenisIklankoranAttribute(): string
    {
        return $this->nama_jenis ?? '-';
    }

    public function getJenisIklanonlineAttribute(): string
    {
        return $this->nama_jenis ?? '-';
    }

    public function getJenisIklanprianganAttribute(): string
    {
        return $this->nama_jenis ?? '-';
    }
}
