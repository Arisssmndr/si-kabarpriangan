<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AkunPerkiraan extends Model
{
    use HasFactory;

    protected $table = 'akun_perkiraan';

    protected $fillable = [
        'kode_perkiraan',
        'nama_perkiraan',
        'kepala',
        'kategori',
        'saldo_normal',
        'saldo_awal',
        'saldo_awal_tanggal',
        'entitas',
        'status_aktif',
        'deskripsi',
    ];

    protected $casts = [
        'kepala'             => 'integer',
        'saldo_awal'         => 'decimal:2',
        'saldo_awal_tanggal' => 'date',
        'status_aktif'       => 'boolean',
    ];

    public function jurnalDetails()
    {
        return $this->hasMany(JurnalUmumDetail::class, 'akun_perkiraan_id');
    }

    /**
     * Scope akun khusus entitas Kabar Priangan (memfilter Mitra Dialog/Cirebon)
     */
    public function scopeKabarPriangan($query)
    {
        return $query->where('entitas', 'Kabar Priangan');
    }

    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }
}

