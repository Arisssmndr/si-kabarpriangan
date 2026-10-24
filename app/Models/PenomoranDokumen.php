<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenomoranDokumen extends Model
{
    use HasFactory;

    protected $table = 'penomoran_dokumen';

    protected $fillable = [
        'tipe_dokumen',
        'tahun',
        'bulan',
        'nomor_terakhir',
        'prefix',
    ];

    protected $casts = [
        'tahun'          => 'integer',
        'bulan'          => 'integer',
        'nomor_terakhir' => 'integer',
    ];
}
