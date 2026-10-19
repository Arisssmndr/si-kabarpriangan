<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemoUnlock extends Model
{
    use HasFactory;

    protected $table = 'memo_unlocks';

    protected $fillable = [
        'tanggal_target',
        'nomor_memo',
        'alasan',
        'dibuka_oleh_user_id',
        'status',
        'dibuka_pada',
        'ditutup_kembali_pada',
    ];

    protected $casts = [
        'tanggal_target'       => 'date',
        'dibuka_pada'          => 'datetime',
        'ditutup_kembali_pada' => 'datetime',
    ];

    public function dibukaOleh()
    {
        return $this->belongsTo(User::class, 'dibuka_oleh_user_id');
    }
}
