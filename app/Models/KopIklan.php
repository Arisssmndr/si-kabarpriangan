<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KopIklan extends Model
{
    use HasFactory;

    protected $table = 'kop_iklan';

    protected $fillable = [
        'kode',
        'nama_kop',
        'deskripsi',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'urutan'    => 'integer',
        'is_active' => 'boolean',
    ];

    public function transaksis()
    {
        return $this->hasMany(TransaksiKoran::class, 'kop_iklan_id');
    }
}
