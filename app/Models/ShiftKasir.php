<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShiftKasir extends Model
{
    use HasFactory;

    protected $table = 'shift_kasirs';

    protected $fillable = [
        'kasir_id',
        'tanggal_shift',
        'tgl_shift',
        'saldo_awal_fisik',
        'total_ki',
        'total_ko',
        'saldo_akhir_teoritis',
        'total_masuk',
        'total_keluar',
        'saldo_akhir_fisik',
        'selisih',
        'selisih_kas',
        'status',
        'closed_at',
        'catatan_serah_terima',
        'verified_by',
        'verified_at',
        'catatan_supervisor',
    ];

    protected $casts = [
        'tanggal_shift'       => 'date',
        'tgl_shift'           => 'date',
        'closed_at'           => 'datetime',
        'verified_at'         => 'datetime',
        'saldo_awal_fisik'    => 'decimal:2',
        'total_ki'            => 'decimal:2',
        'total_ko'            => 'decimal:2',
        'saldo_akhir_teoritis'=> 'decimal:2',
        'total_masuk'         => 'decimal:2',
        'total_keluar'        => 'decimal:2',
        'saldo_akhir_fisik'   => 'decimal:2',
        'selisih'             => 'decimal:2',
        'selisih_kas'         => 'decimal:2',
    ];

    /**
     * User kasir pemegang shift
     */
    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    /**
     * Transaksi Kas Masuk selama shift ini
     */
    public function kasMasuks(): HasMany
    {
        return $this->hasMany(KasMasuk::class, 'shift_id');
    }

    /**
     * Transaksi Kas Keluar selama shift ini
     */
    public function kasKeluars(): HasMany
    {
        return $this->hasMany(KasKeluar::class, 'shift_id');
    }

    /**
     * Hitung saldo akhir kas menurut sistem
     */
    public function getSaldoSistemAttribute(): float
    {
        return (float) ($this->saldo_awal_fisik + $this->total_masuk - $this->total_keluar);
    }
}
