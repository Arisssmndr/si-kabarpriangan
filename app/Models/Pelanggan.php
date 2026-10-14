<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';

    protected $fillable = [
        'kode_pelanggan',
        'nama_instansi',
        'jenis_klien',
        'npwp_nik',
        'alamat',
        'kontak_pic',
        'no_telp',
        'email',
        'credit_limit',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'integer',
        'is_active' => 'boolean',
    ];

    public function transaksiKoran()
    {
        return $this->hasMany(TransaksiKoran::class, 'pelanggan_id');
    }

    /**
     * Hitung total piutang berjalan klien di seluruh koran.
     */
    public function getTotalPiutangKoranAttribute(): int
    {
        return (int) $this->transaksiKoran()->sum('piutang_transaksikoran');
    }

    /**
     * Cek apakah klien ini melebihi batas kredit (credit limit).
     */
    public function isExceedingCreditLimit(int $tagihanBaru = 0): bool
    {
        if ($this->credit_limit <= 0) {
            return true; // 0 = Harus Cash Before Print (CBP)
        }
        return ($this->total_piutang_koran + $tagihanBaru) > $this->credit_limit;
    }

    /**
     * Generate Kode Pelanggan Otomatis (Format: PLG-0001)
     */
    public static function createCode(): string
    {
        $latest = self::orderBy('id', 'desc')->first();
        if (!$latest) {
            return 'PLG-0001';
        }
        $num = intval(substr($latest->kode_pelanggan, 4)) + 1;
        return 'PLG-' . sprintf('%04d', $num);
    }
}
