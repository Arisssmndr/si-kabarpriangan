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
        'produk_jasa',
        'no_spk',
        'nama_pic',
        'jabatan_pic',
        'no_kontak_pemasang',
        'pelanggan_id',
        'alamat_pemasangonline',
        'id_iklanonline',
        'sales_ae_id',
        'sales_iklanonline',
        'komisioner',
        'komisi_persen',
        'insentif_persen',
        'total_muatiklanonline',   // Qty
        'tanggal_muatiklanonline',
        'tanggal_selesai_tayang',
        'portal_iklanonline',
        'platforms_publikasi',
        'skema_pembayaran',
        'status_produksi',
        'kategori_produk_publikasi',
        'bentuk_publikasi',
        'bentuk_publikasi_lainnya',
        'materi_iklan',
        'file_materi',
        'url_materi_iklan',
        'url_berita_tayang',
        'url_medsos_tayang',
        'file_bukti_tayang',
        'tanggal_muat_list',
        'tanggal_realisasi_tayang',
        'catatan_redaksi',

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
        'tanggal_transaksionline'   => 'date',
        'tanggal_muatiklanonline'   => 'date',
        'tanggal_selesai_tayang'    => 'date',
        'tanggal_realisasi_tayang'  => 'date',
        'platforms_publikasi'       => 'array',
        'kategori_produk_publikasi' => 'array',
        'ppn_persen'                => 'float',
        'insentif_persen'           => 'float',
        'komisi_persen'             => 'float',
    ];

    public function getStatusProduksiLabelAttribute(): string
    {
        return match ($this->status_produksi) {
            'antrean_redaksi' => 'Dalam Antrean Redaksi / Medsos',
            'tayang'          => 'Tayang (Live)',
            'selesai'         => 'Selesai (Archived)',
            default           => 'Menunggu Bahan',
        };
    }

    public function getStatusProduksiBadgeClassAttribute(): string
    {
        return match ($this->status_produksi) {
            'antrean_redaksi' => 'bg-amber-100 text-amber-800 border-amber-300',
            'tayang'          => 'bg-emerald-100 text-emerald-800 border-emerald-300 animate-pulse',
            'selesai'         => 'bg-slate-100 text-slate-800 border-slate-300',
            default           => 'bg-rose-100 text-rose-800 border-rose-300',
        };
    }

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

    public function sppi()
    {
        return $this->hasOne(Sppi::class, 'transaksi_id')->where('media_type', 'online');
    }

    public function pembayaranList()
    {
        return $this->hasMany(PembayaranIklan::class, 'transaksi_id')->where('media_type', 'online');
    }

    public function qrPembayaranList()
    {
        return $this->hasMany(FakturQrPembayaran::class, 'transaksi_id')
            ->where('kategori', FakturQrPembayaran::KATEGORI_ONLINE);
    }

    public function qrPembayaranAktif()
    {
        return $this->hasOne(FakturQrPembayaran::class, 'transaksi_id')
            ->where('kategori', FakturQrPembayaran::KATEGORI_ONLINE)
            ->where('status', FakturQrPembayaran::STATUS_AKTIF)
            ->latestOfMany();
    }

    /**
     * Financial OOP Accessors: Standar Akuntansi & Dokumen Order Fisik
     * Brutto -> Diskon -> Netto (DPP) -> PPN -> Jumlah Yang Dibayar
     */
    public function getNilaiBruttoAttribute(): int
    {
        $qty = max(1, (int) $this->total_muatiklanonline);
        return (int) ($this->harga_transaksionline * $qty);
    }

    public function getNilaiDiskonAttribute(): int
    {
        return (int) ($this->diskon_transaksionline ?? 0);
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
        return (int) ($this->ppn_transaksionline ?? 0);
    }

    public function getJumlahYangDibayarAttribute(): int
    {
        return (int) ($this->totaltagihan_transaksionline ?? ($this->nilai_netto + $this->nilai_ppn));
    }

    public function getSisaPiutangAttribute(): int
    {
        return (int) ($this->piutang_transaksionline ?? max(0, $this->jumlah_yang_dibayar - ($this->jumlahbayar_transaksionline ?? 0)));
    }

    /**
     * Generate Nomor Faktur Iklan Standar Industri (FK/YYYY/MM/0001)
     * Terintegrasi dengan counter sekuensial atomik bebas race condition.
     */
    public static function createCode(): string
    {
        return \App\Services\NomorDokumenService::generateFaktur();
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
