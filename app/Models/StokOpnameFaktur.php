<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokOpnameFaktur extends Model
{
    use HasFactory;

    protected $table = 'stok_opname_faktur';

    protected $fillable = [
        'no_dokumen',
        'kategori',
        'tanggal_opname',
        'periode_bulan',
        'nomor_seri_awal',
        'nomor_seri_akhir',
        'stok_awal',
        'jumlah_terpakai',
        'jumlah_rusak_batal',
        'sisa_blangko_fisik',
        'selisih',
        'status_opname',
        'keterangan',
        'petugas_id',
    ];

    protected $casts = [
        'tanggal_opname'     => 'date',
        'stok_awal'          => 'integer',
        'jumlah_terpakai'    => 'integer',
        'jumlah_rusak_batal' => 'integer',
        'sisa_blangko_fisik' => 'integer',
        'selisih'            => 'integer',
    ];

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
