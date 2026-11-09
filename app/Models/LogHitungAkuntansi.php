<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogHitungAkuntansi extends Model
{
    use HasFactory;

    protected $table = 'log_hitung_akuntansi';

    protected $fillable = [
        'jenis',
        'tanggal_transaksi',
        'bulan_periode',
        'tahun_periode',
        'total_faktur',
        'total_nominal',
        'status',
        'user_id',
        'catatan',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'bulan_periode'     => 'integer',
        'tahun_periode'     => 'integer',
        'total_faktur'      => 'integer',
        'total_nominal'     => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Cek apakah tanggal/periode tertentu sudah dihitung (Proteksi Anti-Dobel).
     */
    public static function sudahDihitung(string $jenis, ?string $tanggal = null, ?int $bulan = null, ?int $tahun = null): bool
    {
        $q = self::where('jenis', $jenis)->where('status', 'sukses');

        if ($tanggal) {
            $q->whereDate('tanggal_transaksi', $tanggal);
        }
        if ($bulan) {
            $q->where('bulan_periode', $bulan);
        }
        if ($tahun) {
            $q->where('tahun_periode', $tahun);
        }

        return $q->exists();
    }
}

