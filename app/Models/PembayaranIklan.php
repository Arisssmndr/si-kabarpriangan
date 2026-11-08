<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranIklan extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_iklan';

    protected $fillable = [
        'nokwitansi',
        'media_type',
        'transaksi_id',
        'nofaktur',
        'tanggal_bayar',
        'nominal_bayar',
        'potongan_pph23',
        'metode_bayar',
        'nama_penyetor',
        'bukti_transfer_path',
        'catatan',
        'kasir_user_id',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'nominal_bayar' => 'integer',
        'potongan_pph23' => 'integer',
    ];

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_user_id');
    }

    public function kasirUser()
    {
        return $this->belongsTo(User::class, 'kasir_user_id');
    }

    public function transaksiKoran()
    {
        return $this->belongsTo(TransaksiKoran::class, 'transaksi_id');
    }

    public function transaksiOnline()
    {
        return $this->belongsTo(TransaksiOnline::class, 'transaksi_id');
    }

    public function transaksiPriangan()
    {
        return $this->belongsTo(TransaksiPriangan::class, 'transaksi_id');
    }

    /**
     * Relasi dinamis ke transaksi terkait berdasarkan media_type
     */
    public function getTransaksiAttribute()
    {
        return match ($this->media_type) {
            'koran'    => $this->transaksiKoran,
            'online'   => $this->transaksiOnline,
            'priangan' => $this->transaksiPriangan,
            default    => $this->transaksiKoran,
        };
    }

    /**
     * Generate Nomor Kwitansi Otomatis (Format: KWT/KRN/2026/0001)
     */
    public static function createCode(string $mediaType = 'KRN'): string
    {
        return \App\Services\NomorDokumenService::generateKwitansi();
    }
}
