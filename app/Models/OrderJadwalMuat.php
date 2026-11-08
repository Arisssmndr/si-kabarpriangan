<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderJadwalMuat extends Model
{
    use HasFactory;

    protected $table = 'order_jadwal_muat';

    protected $fillable = [
        'transaksi_id',
        'tanggal_muat',
        'edisi_ke',
        'status_tayang',
        'bukti_muat_pdf',
        'catatan',
    ];

    protected $casts = [
        'tanggal_muat' => 'date',
        'edisi_ke'     => 'integer',
    ];

    public function transaksi()
    {
        return $this->belongsTo(TransaksiKoran::class, 'transaksi_id');
    }
}
