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
        'total_kasbon_gantung',
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
        'reopen_status',
        'reopen_reason',
        'reopen_requested_by',
        'reopen_requested_at',
        'reopen_approved_by',
        'reopen_approved_at',
        'reopen_notes',
    ];

    protected $casts = [
        'tanggal_shift'       => 'date',
        'tgl_shift'           => 'date',
        'closed_at'           => 'datetime',
        'verified_at'         => 'datetime',
        'reopen_requested_at' => 'datetime',
        'reopen_approved_at'  => 'datetime',
        'saldo_awal_fisik'    => 'decimal:2',
        'total_ki'            => 'decimal:2',
        'total_ko'            => 'decimal:2',
        'saldo_akhir_teoritis'=> 'decimal:2',
        'total_kasbon_gantung'=> 'decimal:2',
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
     * Transaksi Kasbon / Uang Muka Meja Kasir selama shift ini
     */
    public function kasbons(): HasMany
    {
        return $this->hasMany(Kasbon::class, 'shift_id');
    }

    /**
     * User yang mengajukan permohonan buka kembali kas
     */
    public function reopenRequester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reopen_requested_by');
    }

    /**
     * Manager Keuangan / Pimpinan yang menyetujui buka kembali kas
     */
    public function reopenApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reopen_approved_by');
    }

    /**
     * Hitung saldo akhir kas menurut sistem
     */
    public function getSaldoSistemAttribute(): float
    {
        return (float) ($this->saldo_awal_fisik + $this->total_masuk - $this->total_keluar);
    }
}
