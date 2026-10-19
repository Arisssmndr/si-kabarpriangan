<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiOnline extends Model
{
    use HasFactory;

    protected $table = 'transaksionline';

    protected $fillable = [
        'nofakturonline',
        'tanggal_transaksionline',
        'nama_pemasangonline',
        'pelanggan_id',
        'alamat_pemasangonline',
        'id_iklanonline',
        'sales_ae_id',
        'sales_iklanonline',
        'total_muatiklanonline',   // Qty
        'tanggal_muatiklanonline',
        'portal_iklanonline',

        // --- FIELD KEUANGAN ---
        'harga_transaksionline',
        'diskon_transaksionline',
        'insentif_transaksionline',
        'komisi_transaksionline',
        'ppn_transaksionline',
        'ppn_persen',
        'totaltagihan_transaksikoran', // fallback
        'totaltagihan_transaksionline',
        'jumlahbayar_transaksionline',
        'piutang_transaksionline',
    ];

    protected $casts = [
        'tanggal_transaksionline' => 'date',
        'tanggal_muatiklanonline' => 'date',
        'ppn_persen'              => 'float',
    ];

    public function iklanonline()
    {
        return $this->belongsTo(JenisIklan::class, 'id_iklanonline');
    }

    public function salesAe()
    {
        return $this->belongsTo(SalesAe::class, 'sales_ae_id');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public static function createCode()
    {
        $latestCode = self::orderBy('nofakturonline', 'desc')->value('nofakturonline');
        $latestCodeNumber = intval(substr($latestCode, 5));
        $nextCodeNumber = $latestCodeNumber ? $latestCodeNumber + 1 : 1;
        $formattedCodeNumber = sprintf("%03d", $nextCodeNumber);
        return 'FKONL' . $formattedCodeNumber;
    }
}
