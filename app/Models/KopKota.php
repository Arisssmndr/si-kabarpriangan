<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KopKota extends Model
{
    use HasFactory;

    protected $table = 'kop_kota';

    protected $fillable = [
        'kode',
        'nomor_halaman',
        'nama_penempatan',
        'wilayah_biro',
        'surcharge_persen',
        'is_active',
    ];

    protected $casts = [
        'nomor_halaman'    => 'integer',
        'surcharge_persen' => 'decimal:2',
        'is_active'        => 'boolean',
    ];

    public function transaksis()
    {
        return $this->hasMany(TransaksiKoran::class, 'kop_kota_id');
    }
}
