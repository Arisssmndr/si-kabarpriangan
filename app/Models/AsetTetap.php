<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetTetap extends Model
{
    use HasFactory;

    protected $table = 'aset_tetaps';

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'tgl_perolehan',
        'harga_beli',
        'tarif_susut',
        'susut_tahunan',
        'susut_bulanan',
        'kode_perkiraan_aset',
        'status',
        'tgl_disposal',
        'nilai_disposal',
        'jurnal_perolehan_id',
        'jurnal_disposal_id',
    ];

    protected $casts = [
        'tgl_perolehan'  => 'date',
        'tgl_disposal'   => 'date',
        'harga_beli'     => 'decimal:2',
        'tarif_susut'    => 'decimal:2',
        'susut_tahunan'  => 'decimal:2',
        'susut_bulanan'  => 'decimal:2',
        'nilai_disposal' => 'decimal:2',
    ];
}
