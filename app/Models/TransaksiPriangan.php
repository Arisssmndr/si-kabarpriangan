<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPriangan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nofakturpriangan',
        'tanggal_transaksipriangan',
        'nama_pemasangpriangan',
        'alamat_pemasangpriangan',
        'id_iklanpriangan',
        'sales_iklanpriangan',
        'tanggal_muatiklanpriangan',
        'total_muatiklanpriangan',
        'harga_transaksipriangan',
        'diskon_transaksipriangan',
        'insentif_transaksipriangan',
        'komisi_transaksipriangan',
        'ppn_transaksipriangan',
        'totaltagihan_transaksipriangan',
        'jumlahbayar_transaksipriangan',
        'piutang_transaksipriangan',
    ];

    protected $table = 'transaksipriangan';

    public function iklanpriangan()
    {
        return $this->belongsTo(JenisIklan::class, 'id_iklanpriangan');
    }

    public static function createCode(){
        $latestCode = self::orderBy('nofakturpriangan','desc')->value('nofakturpriangan');
        $latestCodeNumber = intval(substr($latestCode,5));
        $nextCodeNumber = $latestCodeNumber ? $latestCodeNumber + 1 : 1;
        $formattedCodeNumber = sprintf("%03d", $nextCodeNumber);
        return 'FKPTV' . $formattedCodeNumber;
    }
}
