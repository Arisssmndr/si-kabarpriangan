<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SetoranInkasoLapangan extends Model
{
    use HasFactory;

    protected $table = 'setoran_inkaso_lapangans';

    protected $fillable = [
        'no_bsk',
        'penugasan_id',
        'faktur_id',
        'petugas_id',
        'nama_pembayar',
        'nominal_titipan',
        'metode_bayar',
        'keterangan_angsuran',
        'barcode_token',
        'waktu_terima_lapangan',
        'status_approval',
        'catatan_kasir',
        'approved_by_kasir_id',
        'approved_at',
        'no_kuitansi_kasir',
        'kas_masuk_id',
    ];

    protected $casts = [
        'nominal_titipan'       => 'decimal:2',
        'waktu_terima_lapangan' => 'datetime',
        'approved_at'           => 'datetime',
    ];

    /**
     * Penugasan Inkaso yang menaungi penagihan faktur ini.
     */
    public function penugasan(): BelongsTo
    {
        return $this->belongsTo(PenugasanInkaso::class, 'penugasan_id');
    }

    /**
     * Faktur yang dibayar.
     */
    public function faktur(): BelongsTo
    {
        return $this->belongsTo(Faktur::class, 'faktur_id');
    }

    /**
     * Petugas Inkaso yang menerima pembayaran di lapangan.
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    /**
     * Kasir yang memvalidasi uang fisik dan menyetujui setoran.
     */
    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_kasir_id');
    }

    /**
     * Transaksi Kas Masuk resmi BKM yang diterbitkan.
     */
    public function kasMasuk(): BelongsTo
    {
        return $this->belongsTo(KasMasuk::class, 'kas_masuk_id');
    }

    /**
     * Generate Nomor Bukti Setoran Koleksi (BSK) Urut Harian: BSK-INK-YYYYMMDD-XXXX
     */
    public static function generateNoBsk(): string
    {
        $dateStr = Carbon::today()->format('Ymd');
        $prefix = "BSK-INK-{$dateStr}-";

        $maxSeq = (int) self::where('no_bsk', 'like', "{$prefix}%")
            ->pluck('no_bsk')
            ->map(function ($val) {
                if (preg_match('/-(\d{4,})$/', (string) $val, $matches)) {
                    return (int) $matches[1];
                }
                return 0;
            })
            ->max();

        $nextNum = $maxSeq + 1;
        do {
            $candidate = $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
            $nextNum++;
        } while (self::where('no_bsk', $candidate)->exists());

        return $candidate;
    }
}
