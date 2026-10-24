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
        'nama_pic',
        'jabatan_pic',
        'no_telp',
        'produk_jasa',
        'no_spk',
        'alamat_pemasangpriangan',
        'id_iklanpriangan',
        'materi_iklan',
        'durasi_detik',
        'jam_tayang',
        'status_tayang',
        'sales_ae_id',
        'sales_iklanpriangan',
        'komisioner',
        'komisi_persen',
        'insentif_persen',
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

    /**
     * Generate Nomor Faktur Iklan Standar Industri (FK/YYYY/MM/0001)
     * Terintegrasi dengan counter sekuensial atomik bebas race condition.
     */
    public static function createCode(): string
    {
        return \App\Services\NomorDokumenService::generateFaktur();
    }
}
