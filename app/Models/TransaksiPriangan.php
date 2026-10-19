<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPriangan extends Model
{
    use HasFactory;

    protected $table = 'transaksipriangan';

    protected $fillable = [
        'nofakturpriangan',
        'tanggal_transaksipriangan',
        'nama_pemasangpriangan',
        'pelanggan_id',
        'alamat_pemasangpriangan',
        'id_iklanpriangan',
        'sales_ae_id',
        'sales_iklanpriangan',
        'tanggal_muatiklanpriangan',
        'total_muatiklanpriangan',
        'harga_transaksipriangan',
        'diskon_transaksipriangan',
        'insentif_transaksipriangan',
        'komisi_transaksipriangan',
        'ppn_transaksipriangan',
        'ppn_persen',
        'totaltagihan_transaksipriangan',
        'jumlahbayar_transaksipriangan',
        'piutang_transaksipriangan',
    ];

    protected $casts = [
        'tanggal_transaksipriangan' => 'date',
        'tanggal_muatiklanpriangan' => 'date',
        'ppn_persen'                => 'float',
    ];

    public function iklanpriangan()
    {
        return $this->belongsTo(JenisIklan::class, 'id_iklanpriangan');
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
        $latestCode = self::orderBy('nofakturpriangan', 'desc')->value('nofakturpriangan');
        $latestCodeNumber = intval(substr($latestCode, 5));
        $nextCodeNumber = $latestCodeNumber ? $latestCodeNumber + 1 : 1;
        $formattedCodeNumber = sprintf("%03d", $nextCodeNumber);
        return 'FKPTV' . $formattedCodeNumber;
    }
}
