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
        'shift_id',
        'tanggal',
        'nama_penerima',
        'keperluan',
        'nominal',
        'status', // 'menunggu_acc', 'pending' (gantung / kasbon aktif), 'lunas', 'masuk_bp', 'ditolak'
        'approved_by',
        'approved_at',
        'kasir_id',
        'keterangan',
        'selesai_at',
        'alasan_tolak',
    ];

    protected $casts = [
        'tanggal'     => 'date',
        'nominal'     => 'decimal:2',
        'approved_at' => 'datetime',
        'selesai_at'  => 'datetime',
    ];

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
}
