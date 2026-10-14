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

    public function transaksiKoran()
    {
        return $this->belongsTo(TransaksiKoran::class, 'transaksi_id');
    }

    /**
     * Generate Nomor Kwitansi Otomatis (Format: KWT/KRN/2026/0001)
     */
    public static function createCode(string $mediaType = 'KRN'): string
    {
        $year = date('Y');
        $prefix = "KWT/{$mediaType}/{$year}/";
        $latest = self::where('nokwitansi', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $num = 1;
        if ($latest) {
            $parts = explode('/', $latest->nokwitansi);
            $num = intval(end($parts)) + 1;
        }

        return $prefix . sprintf('%04d', $num);
    }
}
