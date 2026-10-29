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

    /**
     * Payload JSON terstruktur untuk Kode QR Kasir (Faktur Iklan Priangan TV).
     */
    public function getQrKasirPayloadAttribute(): string
    {
        $itemNama = $this->iklanpriangan->jenis_iklanpriangan ?? 'Iklan Priangan TV';

        $payload = [
            'app'             => 'SI-KABARPRIANGAN',
            'tipe'            => 'FAKTUR_PEMESANAN',
            'sumber'          => 'iklan_priangan',
            'no_faktur'       => $this->nofakturpriangan,
            'kategori'        => 'iklan_priangan',
            'status'          => $this->piutang_transaksipriangan <= 0 ? 'lunas' : 'belum_bayar',
            'customer'        => $this->nama_pemasangpriangan ?? '-',
            'alamat'          => $this->alamat_pemasangpriangan ?? '-',
            'telepon'         => '-',
            'tanggal_faktur'  => $this->tanggal_transaksipriangan,
            'jatuh_tempo'     => $this->tanggal_transaksipriangan,
            'total_tagihan'   => (float) $this->totaltagihan_transaksipriangan,
            'total_terbayar'  => (float) $this->jumlahbayar_transaksipriangan,
            'sisa_piutang'    => (float) $this->piutang_transaksipriangan,
            'rincian_pesanan' => [
                [
                    'item'     => $itemNama,
                    'qty'      => (int) ($this->total_muatiklanpriangan ?: 1),
                    'harga'    => (float) ($this->harga_transaksipriangan ?: 0),
                    'subtotal' => (float) ($this->harga_transaksipriangan * ($this->total_muatiklanpriangan ?: 1)),
                ]
            ],
            'api_url'         => url('/api/faktur/' . rawurlencode($this->nofakturpriangan)),
        ];

        return json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}

