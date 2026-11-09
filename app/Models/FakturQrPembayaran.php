<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FakturQrPembayaran extends Model
{
    use HasFactory;

    public const KATEGORI_KORAN  = 'KORAN';
    public const KATEGORI_ONLINE = 'ONLINE';
    public const KATEGORI_TV     = 'TV';

    public const STATUS_AKTIF   = 'AKTIF';
    public const STATUS_DISETOR = 'DISETOR';
    public const STATUS_BATAL   = 'BATAL';

    protected $table = 'faktur_qr_pembayaran';

    protected $fillable = [
        'kategori',
        'transaksi_id',
        'no_faktur',
        'kode_pengaman',
        'signature',
        'nama_pemasang',
        'total_tagihan',
        'nominal_bayar',
        'sisa_piutang',
        'status',
        'alasan_batal',
        'dibuat_oleh',
        'dibuat_pada',
        'discan_pada',
        'discan_oleh',
        'penerimaan_kasir_id',
    ];

    protected $casts = [
        'transaksi_id'        => 'integer',
        'total_tagihan'       => 'decimal:2',
        'nominal_bayar'       => 'decimal:2',
        'sisa_piutang'        => 'decimal:2',
        'dibuat_oleh'         => 'integer',
        'dibuat_pada'         => 'datetime',
        'discan_pada'         => 'datetime',
        'discan_oleh'         => 'integer',
        'penerimaan_kasir_id' => 'integer',
    ];

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function kasirScanner()
    {
        return $this->belongsTo(User::class, 'discan_oleh');
    }

    /**
     * Singkatan kode media pada payload QR (KRN / ONL / TV).
     */
    public function getKodeMediaAttribute(): string
    {
        return match ($this->kategori) {
            self::KATEGORI_KORAN  => 'KRN',
            self::KATEGORI_ONLINE => 'ONL',
            self::KATEGORI_TV     => 'TV',
            default               => 'KRN',
        };
    }

    /**
     * Isi payload QR standar Kabar Priangan:
     * KP1|{KRN|ONL|TV}|{NO_FAKTUR}|{KODE_PENGAMAN}
     */
    public function getPayloadQrAttribute(): string
    {
        return sprintf(
            'KP1|%s|%s|%s',
            $this->kode_media,
            $this->no_faktur,
            $this->kode_pengaman
        );
    }

    /**
     * Kode cadangan ketik manual di Kasir bila scanner bermasalah:
     * {NO_FAKTUR}-{6_KARAKTER_AWAL_KODE_PENGAMAN}
     */
    public function getKodeManualAttribute(): string
    {
        return $this->no_faktur . '-' . substr($this->kode_pengaman, 0, 6);
    }

    public function getIsLunasAttribute(): bool
    {
        return (float) $this->sisa_piutang <= 0;
    }

    /**
     * Ambil objek transaksi asli berdasarkan kategori & transaksi_id / no_faktur.
     */
    public function resolveTransaksi(): TransaksiKoran|TransaksiOnline|TransaksiPriangan|null
    {
        return match ($this->kategori) {
            self::KATEGORI_KORAN => TransaksiKoran::find($this->transaksi_id)
                ?? TransaksiKoran::where('nofakturkoran', $this->no_faktur)->first(),
            self::KATEGORI_ONLINE => TransaksiOnline::find($this->transaksi_id)
                ?? TransaksiOnline::where('nofakturonline', $this->no_faktur)->first(),
            self::KATEGORI_TV => TransaksiPriangan::find($this->transaksi_id)
                ?? TransaksiPriangan::where('nofakturpriangan', $this->no_faktur)->first(),
            default => null,
        };
    }
}

