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

    /**
     * Payload JSON terstruktur untuk Kode QR Kasir (Faktur Iklan Online).
     */
    public function getQrKasirPayloadAttribute(): string
    {
        $itemNama = $this->iklanonline->jenis_iklanonline ?? 'Iklan Portal Online';
        if ($this->portal_iklanonline) {
            $itemNama .= ' (' . $this->portal_iklanonline . ')';
        }

        $payload = [
            'app'             => 'SI-KABARPRIANGAN',
            'tipe'            => 'FAKTUR_PEMESANAN',
            'sumber'          => 'iklan_online',
            'no_faktur'       => $this->nofakturonline,
            'kategori'        => 'iklan_online',
            'status'          => $this->piutang_transaksionline <= 0 ? 'lunas' : 'belum_bayar',
            'customer'        => $this->nama_pemasangonline ?? '-',
            'alamat'          => $this->alamat_pemasangonline ?? '-',
            'telepon'         => '-',
            'tanggal_faktur'  => $this->tanggal_transaksionline,
            'jatuh_tempo'     => $this->tanggal_transaksionline,
            'total_tagihan'   => (float) $this->totaltagihan_transaksionline,
            'total_terbayar'  => (float) $this->jumlahbayar_transaksionline,
            'sisa_piutang'    => (float) $this->piutang_transaksionline,
            'rincian_pesanan' => [
                [
                    'item'     => $itemNama,
                    'qty'      => (int) ($this->total_muatiklanonline ?: 1),
                    'harga'    => (float) ($this->harga_transaksionline ?: 0),
                    'subtotal' => (float) ($this->harga_transaksionline * ($this->total_muatiklanonline ?: 1)),
                ]
            ],
            'api_url'         => url('/api/faktur/' . rawurlencode($this->nofakturonline)),
        ];

        return json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}

