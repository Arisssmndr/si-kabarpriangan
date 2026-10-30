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
        'platforms_publikasi',
        'materi_iklan',
        'file_materi',
        'durasi_detik',
        'jam_tayang',
        'status_tayang',
        'sales_ae_id',
        'sales_iklanpriangan',
        'komisioner',
        'komisi_persen',
        'insentif_persen',
        'tanggal_muatiklanpriangan',
        'tanggal_muat_list',
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
        'platforms_publikasi'       => 'array',
        'ppn_persen'                => 'float',
        'insentif_persen'           => 'float',
        'komisi_persen'             => 'float',
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
     * Financial OOP Accessors: Standar Akuntansi & Dokumen Order Fisik
     * Brutto -> Diskon -> Netto (DPP) -> PPN -> Jumlah Yang Dibayar
     */
    public function getNilaiBruttoAttribute(): int
    {
        $qty = max(1, (int) $this->total_muatiklanpriangan);
        return (int) ($this->harga_transaksipriangan * $qty);
    }

    public function getNilaiDiskonAttribute(): int
    {
        return (int) ($this->diskon_transaksipriangan ?? 0);
    }

    public function getNilaiNettoAttribute(): int
    {
        return max(0, $this->nilai_brutto - $this->nilai_diskon);
    }

    public function getNilaiDppAttribute(): int
    {
        return $this->nilai_netto;
    }

    public function getNilaiPpnAttribute(): int
    {
        return (int) ($this->ppn_transaksipriangan ?? 0);
    }

    public function getJumlahYangDibayarAttribute(): int
    {
        return (int) ($this->totaltagihan_transaksipriangan ?? ($this->nilai_netto + $this->nilai_ppn));
    }

    public function getSisaPiutangAttribute(): int
    {
        return (int) ($this->piutang_transaksipriangan ?? max(0, $this->jumlah_yang_dibayar - ($this->jumlahbayar_transaksipriangan ?? 0)));
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
