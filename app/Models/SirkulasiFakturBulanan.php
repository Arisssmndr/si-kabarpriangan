<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SirkulasiFakturBulanan extends Model
{
    use HasFactory;

    protected $table = 'sirkulasi_faktur_bulanans';

    protected $fillable = [
        'no_faktur',
        'periode_bulan',
        'agen_id',
        'kode_agen',
        'nama_agen',
        'kategori_agen',
        'wilayah',
        'total_oplah_kirim',
        'total_retur_eks',
        'oplah_bersih',
        'tarif_per_eks',
        'total_tagihan_kotor',
        'total_potongan_retur',
        'total_tagihan_bersih',
        'total_terbayar',
        'sisa_piutang',
        'status_pembayaran',
        'rincian_harian',
        'no_bapr',
        'catatan_retur',
        'tanggal_faktur',
        'jatuh_tempo',
        'tanggal_lunas',
        'diterbitkan_oleh',
    ];

    protected $casts = [
        'total_oplah_kirim'    => 'integer',
        'total_retur_eks'      => 'integer',
        'oplah_bersih'         => 'integer',
        'tarif_per_eks'        => 'decimal:2',
        'total_tagihan_kotor'  => 'decimal:2',
        'total_potongan_retur' => 'decimal:2',
        'total_tagihan_bersih' => 'decimal:2',
        'total_terbayar'       => 'decimal:2',
        'sisa_piutang'         => 'decimal:2',
        'rincian_harian'       => 'array',
        'tanggal_faktur'       => 'date',
        'jatuh_tempo'          => 'date',
        'tanggal_lunas'        => 'datetime',
    ];

    public function agen(): BelongsTo
    {
        return $this->belongsTo(Agen::class, 'agen_id');
    }

    public function penerbit(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterbitkan_oleh');
    }

    /**
     * Generate format nomor faktur bulanan sirkulasi standar:
     * Contoh: FAK-SRK-202611-001
     */
    public static function generateNoFaktur(string $periodeBulan, int $index): string
    {
        $cleanPeriode = str_replace('-', '', $periodeBulan);
        return sprintf('FAK-SRK-%s-%03d', $cleanPeriode, $index);
    }
}
