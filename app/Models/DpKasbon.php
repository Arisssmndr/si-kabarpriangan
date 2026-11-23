<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DpKasbon extends Model
{
    use HasFactory;

    protected $table = 'dp_kasbons';

    protected $fillable = [
        'no_kasbon',
        'tanggal_kasbon',
        'kode_bagian',
        'divisi',
        'penerima',
        'nominal',
        'keperluan',
        'tgl_jatuh_tempo',
        'status',
        'bp_id',
        'created_by',
    ];

    protected $casts = [
        'tanggal_kasbon'  => 'date',
        'tgl_jatuh_tempo' => 'date',
    ];

    public function buktiPengeluaran(): BelongsTo
    {
        return $this->belongsTo(BuktiPengeluaran::class, 'bp_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate Nomor Kasbon: KB-YYYYMM-XXXX
     */
    public static function generateNoKasbon(): string
    {
        $period = date('Ym');
        $prefix = "KB-{$period}-";
        $latest = self::where('no_kasbon', 'like', "{$prefix}%")
            ->orderByRaw('CAST(SUBSTRING(no_kasbon, -4) AS UNSIGNED) DESC')
            ->first();

        $nextSeq = 1;
        if ($latest) {
            $parts = explode('-', $latest->no_kasbon);
            $nextSeq = ((int) end($parts)) + 1;
        }

        do {
            $suggested = $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
            $nextSeq++;
        } while (self::where('no_kasbon', $suggested)->exists());

        return $suggested;
    }
}
