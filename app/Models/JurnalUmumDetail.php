<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalUmumDetail extends Model
{
    use HasFactory;

    protected $table = 'jurnal_umum_detail';

    protected $fillable = [
        'jurnal_umum_id',
        'akun_perkiraan_id',
        'kode_perkiraan',
        'nama_perkiraan',
        'debet',
        'kredit',
        'keterangan_baris',
        'urutan',
    ];

    protected $casts = [
        'debet'  => 'decimal:2',
        'kredit' => 'decimal:2',
        'urutan' => 'integer',
    ];

    public function jurnal()
    {
        return $this->belongsTo(JurnalUmum::class, 'jurnal_umum_id');
    }

    public function akun()
    {
        return $this->belongsTo(AkunPerkiraan::class, 'akun_perkiraan_id');
    }
}

