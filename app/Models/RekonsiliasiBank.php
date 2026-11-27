<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekonsiliasiBank extends Model
{
    use HasFactory;

    protected $table = 'rekonsiliasi_banks';

    protected $fillable = [
        'periode',
        'rekening_bank',
        'kode_perkiraan_bank',
        'tanggal_transaksi',
        'uraian_bank',
        'referensi_bank',
        'tipe_mutasi',
        'nominal',
        'status_match',
        'matched_model',
        'matched_id',
        'jurnal_umum_id',
        'user_id',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'nominal'           => 'decimal:2',
    ];

    public function jurnal()
    {
        return $this->belongsTo(JurnalUmum::class, 'jurnal_umum_id');
    }
}
