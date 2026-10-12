<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiKoran extends Model
{
    use HasFactory;

    protected $table = 'transaksikoran';

    protected $fillable = [
        'nofakturkoran',
        'tanggal_transaksikoran',
        'nama_pemasangkoran',
        'alamat_pemasangkoran',
        'id_iklankoran',
        'halaman_iklan',
        'warna_iklan',
        'ukuran_iklan',
        'tanggal_muatkoran',
        'sales_iklankoran',
        'total_muatkoran',
        'harga_transaksikoran',
        'diskon_transaksikoran',
        'insentif_transaksikoran',
        'komisi_transaksikoran',
        'ppn_transaksikoran',
        'totaltagihan_transaksikoran',
        'jumlahbayar_transaksikoran',
        'piutang_transaksikoran',
    ];

    public function iklankoran()
    {
        return $this->belongsTo(JenisIklan::class, 'id_iklankoran');
    }

    public static function createCode()
    {
        $latestCode = self::orderBy('nofakturkoran', 'desc')->value('nofakturkoran');
        $latestCodeNumber = $latestCode ? intval(substr($latestCode, 5)) : 0;
        $nextCodeNumber = $latestCodeNumber + 1;
        $formattedCodeNumber = sprintf("%03d", $nextCodeNumber);
        return 'FKKRN' . $formattedCodeNumber;
    }
}
