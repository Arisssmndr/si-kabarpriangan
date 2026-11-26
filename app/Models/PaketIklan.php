<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketIklan extends Model
{
    use HasFactory;

    protected $table = 'paket_iklan';

    protected $fillable = [
        'nofaktur_paket',
        'tanggal_transaksi',
        'kode_template',
        'nama_paket',
        'pelanggan_id',
        'nama_pemasang',
        'alamat_pemasang',
        'telepon_pemasang',
        'nama_pic',
        'sales_ae_id',
        'sales_nama',
        'komisioner',
        'komisi_persen',
        'komisi_nominal',
        'insentif_persen',
        'insentif_nominal',
        'media_terpilih',
        'nilai_brutto',
        'diskon_persen',
        'nilai_diskon',
        'nilai_netto',
        'ppn_persen',
        'nilai_ppn',
        'total_tagihan',
        'jumlah_bayar',
        'piutang',
        'jenis_pembayaran',
        'jatuh_tempo',
        'status_order',
        'alasan_batal',
        'dibatalkan_oleh',
        'dibatalkan_pada',
        'is_pulled_akuntansi',
        'pulled_at',
        'created_by',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'jatuh_tempo'       => 'date',
        'dibatalkan_pada'   => 'datetime',
        'pulled_at'         => 'datetime',
        'is_pulled_akuntansi' => 'boolean',
        'media_terpilih'    => 'array',
        'nilai_brutto'      => 'integer',
        'nilai_diskon'      => 'integer',
        'nilai_netto'       => 'integer',
        'nilai_ppn'         => 'integer',
        'total_tagihan'     => 'integer',
        'jumlah_bayar'      => 'integer',
        'piutang'           => 'integer',
        'komisi_nominal'    => 'integer',
        'insentif_nominal'  => 'integer',
        'diskon_persen'     => 'float',
        'ppn_persen'        => 'float',
        'komisi_persen'     => 'float',
        'insentif_persen'   => 'float',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function salesAe()
    {
        return $this->belongsTo(SalesAe::class, 'sales_ae_id');
    }

    public function inputOleh()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function dibatalkanOleh()
    {
        return $this->belongsTo(User::class, 'dibatalkan_oleh');
    }

    public function transaksiCetak()
    {
        return $this->hasOne(TransaksiKoran::class, 'paket_id');
    }

    public function transaksiOnline()
    {
        return $this->hasOne(TransaksiOnline::class, 'paket_id');
    }

    public function transaksiTv()
    {
        return $this->hasOne(TransaksiPriangan::class, 'paket_id');
    }

    public static function createCode(): string
    {
        return \App\Services\NomorDokumenService::generateFaktur(null, 'PKT');
    }

    // Accessors & Mutators untuk kompatibilitas lintas modul
    public function getSisaPiutangAttribute()
    {
        return $this->attributes['piutang'] ?? 0;
    }

    public function setSisaPiutangAttribute($value)
    {
        $this->attributes['piutang'] = $value;
    }

    public function getNoFakturPaketAttribute()
    {
        return $this->attributes['nofaktur_paket'] ?? null;
    }

    public function setNoFakturPaketAttribute($value)
    {
        $this->attributes['nofaktur_paket'] = $value;
    }

    public function getNamaPemesanAttribute()
    {
        return $this->attributes['nama_pemasang'] ?? null;
    }

    public function setNamaPemesanAttribute($value)
    {
        $this->attributes['nama_pemasang'] = $value;
    }
}

