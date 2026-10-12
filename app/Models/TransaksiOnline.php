<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiOnline extends Model
{
    use HasFactory;

    protected $fillable = [
        'nofakturonline',
        'tanggal_transaksionline',
        'nama_pemasangonline',
        'alamat_pemasangonline',
        'id_iklanonline',
        'sales_iklanonline',
        'total_muatiklanonline',   // Qty
        'tanggal_muatiklanonline',
        'portal_iklanonline',

        // --- FIELD KEUANGAN ---
        'harga_transaksionline',
        'diskon_transaksionline',
        'insentif_transaksionline',

        // TAMBAHKAN 3 KOLOM INI AGAR BISA DISIMPAN:
        'komisi_transaksionline',       // <--- Wajib Tambah
        'ppn_transaksionline',          // <--- Wajib Tambah
        'totaltagihan_transaksionline', // <--- Wajib Tambah

        'jumlahbayar_transaksionline',
        'piutang_transaksionline',
    ];

    protected $table = 'transaksionline';

    public function iklanonline()
    {
        return $this->belongsTo(JenisIklan::class, 'id_iklanonline');
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
