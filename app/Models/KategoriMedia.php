<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriMedia extends Model
{
    use HasFactory;

    protected $table = 'kategori_media';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke paket-paket jenis iklan dalam kategori ini
     */
    public function jenisIklan()
    {
        return $this->hasMany(JenisIklan::class, 'kategori_media_id');
    }

    /**
     * Otomatis membuat kode jenis iklan baru berdasarkan kode kategori
     * Contoh: KRN -> KRN001, ONL -> ONL001, PTV -> PTV001
     */
    public function generateNextJenisCode(): string
    {
        $prefix = strtoupper(trim($this->kode));

        $latest = JenisIklan::where('kode_jenis', 'like', $prefix . '%')
            ->orderByRaw('LENGTH(kode_jenis) DESC, kode_jenis DESC')
            ->first();

        if ($latest && preg_match('/(\d+)$/', $latest->kode_jenis, $matches)) {
            $nextNum = ((int) $matches[1]) + 1;
        } else {
            $nextNum = $this->jenisIklan()->count() + 1;
        }

        $candidate = $prefix . sprintf('%03d', $nextNum);

        while (JenisIklan::where('kode_jenis', $candidate)->exists()) {
            $nextNum++;
            $candidate = $prefix . sprintf('%03d', $nextNum);
        }

        return $candidate;
    }
}
