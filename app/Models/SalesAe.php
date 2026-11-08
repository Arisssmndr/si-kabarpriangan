<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesAe extends Model
{
    use HasFactory;

    protected $table = 'sales_ae';

    protected $fillable = [
        'kode_ae',
        'nama_ae',
        'jenis_kode',
        'user_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaksis()
    {
        return $this->hasMany(TransaksiKoran::class, 'sales_ae_id');
    }
}
