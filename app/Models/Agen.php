<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agen extends Model
{
    use HasFactory;

    protected $table = 'agens';

    protected $fillable = [
        'kode_agen',
        'nama_agen',
        'kategori',
        'induk_agen',
        'rute_kode',
        'wilayah',
        'telepon',
        'jatah_default',
        'plafon_piutang',
        'saldo_piutang',
        'status',
    ];

    protected $casts = [
        'jatah_default'  => 'integer',
        'plafon_piutang' => 'decimal:2',
        'saldo_piutang'  => 'decimal:2',
    ];

    /**
     * Seluruh setoran kasir dari agen ini
     */
    public function kasMasuks(): HasMany
    {
        return $this->hasMany(KasMasuk::class, 'ref_id')->where('tipe_transaksi', 'sirkulasi');
    }
}
