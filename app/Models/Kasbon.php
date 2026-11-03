<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kasbon extends Model
{
    use HasFactory;

    protected $table = 'kasbons';

    protected $fillable = [
        'no_kasbon',
        'no_bs',
        'shift_id',
        'tanggal',
        'nama_penerima',
        'keperluan',
        'nominal',
        'nominal_bs',
        'nominal_realisasi',
        'selisih_kembali_tunai',
        'no_bkkc_ref',
        'no_lkk_ref',
        'status', // 'menunggu_acc', 'pending' / 'aktif' (gantung), 'lunas' / 'selesai_bkkc', 'masuk_bp', 'ditolak'
        'approved_by',
        'approved_at',
        'ttd_user',
        'ttd_kasir',
        'ttd_manajer',
        'kasir_id',
        'keterangan',
        'selesai_at',
        'alasan_tolak',
    ];

    protected $casts = [
        'tanggal'               => 'date',
        'nominal'               => 'decimal:2',
        'nominal_bs'            => 'decimal:2',
        'nominal_realisasi'     => 'decimal:2',
        'selisih_kembali_tunai' => 'decimal:2',
        'approved_at'           => 'datetime',
        'selesai_at'            => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            if (empty($model->no_bs) && !empty($model->no_kasbon)) {
                $model->no_bs = $model->no_kasbon;
            }
            if (empty($model->no_kasbon) && !empty($model->no_bs)) {
                $model->no_kasbon = $model->no_bs;
            }
            if ((empty($model->nominal_bs) || $model->nominal_bs == 0) && !empty($model->nominal)) {
                $model->nominal_bs = $model->nominal;
            }
            if ((empty($model->nominal) || $model->nominal == 0) && !empty($model->nominal_bs)) {
                $model->nominal = $model->nominal_bs;
            }
            if (empty($model->ttd_user) && !empty($model->nama_penerima)) {
                $model->ttd_user = $model->nama_penerima;
            }
            if (empty($model->ttd_manajer) && !empty($model->approved_by)) {
                $model->ttd_manajer = $model->approved_by;
            }
        });
    }

    /**
     * Shift kasir pemegang kas saat kasbon dicairkan
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(ShiftKasir::class, 'shift_id');
    }

    /**
     * User kasir pencatat kasbon
     */
    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    /**
     * Generator Nomor Kasbon Format KAS: KB-YYYYMMXXXX
     */
    public static function generateNoKasbon(): string
    {
        $ym = Carbon::today()->format('Ym');
        $prefix = "KB-{$ym}";

        $latest = self::where('no_kasbon', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('no_kasbon');

        $nextSeq = 1;
        if ($latest && str_starts_with($latest, $prefix)) {
            $lastSeq = substr($latest, strlen($prefix));
            $nextSeq = intval($lastSeq) + 1;
        }

        return $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generator Nomor Bon Sementara (BS) Otomatis Per Bulan: BS-YYYYMMXXXX
     */
    public static function generateNoBs(): string
    {
        $ym = Carbon::today()->format('Ym');
        $prefix = "BS-{$ym}";

        $latest = self::where('no_bs', 'like', "{$prefix}%")
            ->orWhere('no_kasbon', 'like', "KB-{$ym}%")
            ->orderBy('id', 'desc')
            ->first();

        $nextSeq = 1;
        if ($latest) {
            $ref = $latest->no_bs ?: $latest->no_kasbon;
            if (preg_match('/(\d{4})$/', (string) $ref, $m)) {
                $nextSeq = intval($m[1]) + 1;
            }
        }

        return $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generator Nomor Bukti Kas Kecil (BKKC) Otomatis Per Bulan: BKKC-YYYYMMXXXX
     */
    public static function generateNoBkkc(): string
    {
        $ym = Carbon::today()->format('Ym');
        $prefix = "BKKC-{$ym}";

        $latest = self::where('no_bkkc_ref', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('no_bkkc_ref');

        $nextSeq = 1;
        if ($latest && preg_match('/(\d{4})$/', $latest, $m)) {
            $nextSeq = intval($m[1]) + 1;
        }

        return $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generator Nomor Laporan Kas Kecil (LKK) Otomatis Per Bulan: LKK-YYYYMMXXXX
     */
    public static function generateNoLkk(): string
    {
        $ym = Carbon::today()->format('Ym');
        $prefix = "LKK-{$ym}";

        $latest = self::where('no_lkk_ref', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('no_lkk_ref');

        $nextSeq = 1;
        if ($latest && preg_match('/(\d{4})$/', $latest, $m)) {
            $nextSeq = intval($m[1]) + 1;
        }

        return $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
    }
}
