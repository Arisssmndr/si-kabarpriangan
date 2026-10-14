<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class KasTransaksi extends Model
{
    use HasFactory;

    protected $table = 'kas_transaksi';

    protected $fillable = [
        'no_transaksi',
        'tanggal',
        'jenis',
        'kategori_kas',
        'nominal',
        'referensi_id',
        'referensi_tipe',
        'keterangan',
        'saldo_akhir',
        'created_by',
    ];

    protected $casts = [
        'tanggal'     => 'date',
        'nominal'     => 'decimal:2',
        'saldo_akhir' => 'decimal:2',
    ];

    /**
     * Boot model observer untuk otomatis generate no_transaksi dan menghitung saldo_akhir.
     */
    protected static function booted()
    {
        static::creating(function (KasTransaksi $kas) {
            if (empty($kas->no_transaksi)) {
                $prefix = $kas->jenis === 'masuk' ? 'KM-' : 'KK-';
                $dateCode = date('Ymd');
                $lastId = static::max('id') ?? 0;
                $kas->no_transaksi = $prefix . $dateCode . '-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
            }

            if ($kas->saldo_akhir === null || $kas->saldo_akhir == 0) {
                $lastSaldo = (float) (static::latest('id')->value('saldo_akhir') ?? 0);
                if ($kas->jenis === 'masuk') {
                    $kas->saldo_akhir = $lastSaldo + (float) $kas->nominal;
                } else {
                    $kas->saldo_akhir = $lastSaldo - (float) $kas->nominal;
                }
            }
        });
    }

    /**
     * User pembuat/kasir.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Objek transaksi sumber (misal PembayaranFaktur atau PengajuanAnggaran).
     */
    public function referensi(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'referensi_tipe', 'referensi_id');
    }

    /**
     * Helper terpusat untuk mencatat mutasi kas dengan keamanan transaksi.
     */
    public static function catatMutasi(
        string $jenis,
        string $kategori,
        float $nominal,
        string $keterangan,
        $referensi = null,
        ?int $userId = null,
        ?string $tanggal = null
    ): self {
        $lastRecord = static::lockForUpdate()->latest('id')->first();
        $lastSaldo = $lastRecord ? (float) $lastRecord->saldo_akhir : 0.0;

        $newSaldo = $jenis === 'masuk' ? ($lastSaldo + $nominal) : ($lastSaldo - $nominal);

        return static::create([
            'tanggal'        => $tanggal ?? date('Y-m-d'),
            'jenis'          => $jenis,
            'kategori_kas'   => $kategori,
            'nominal'        => $nominal,
            'referensi_id'   => $referensi ? $referensi->id : null,
            'referensi_tipe' => $referensi ? get_class($referensi) : null,
            'keterangan'     => $keterangan,
            'saldo_akhir'    => $newSaldo,
            'created_by'     => $userId ?? auth()->id(),
        ]);
    }

    /**
     * Dapatkan saldo kas terakhir saat ini.
     */
    public static function saldoSaatIni(): float
    {
        return (float) (static::latest('id')->value('saldo_akhir') ?? 0);
    }
}
