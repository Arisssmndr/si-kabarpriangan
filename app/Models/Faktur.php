<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faktur extends Model
{
    use HasFactory;

    protected $table = 'faktur';

    protected $fillable = [
        'no_faktur',
        'no_order',
        'customer_id',
        'sales_nama',
        'kategori',
        'tipe_faktur',
        'tanggal_faktur',
        'jatuh_tempo',
        'materi_iklan',
        'rincian_biaya',
        'brutto',
        'diskon_persen',
        'diskon_nominal',
        'netto',
        'ppn_nominal',
        'materai',
        'total_tagihan',
        'total_terbayar',
        'sisa_piutang',
        'status',
        'status_bayar',
        'siap_cair_kh_insentif',
        'created_by',
        'catatan',
    ];

    protected $casts = [
        'tanggal_faktur'        => 'date',
        'jatuh_tempo'           => 'date',
        'rincian_biaya'         => 'array',
        'total_tagihan'         => 'decimal:2',
        'total_terbayar'        => 'decimal:2',
        'sisa_piutang'          => 'decimal:2',
        'siap_cair_kh_insentif' => 'boolean',
    ];

    /**
     * Customer pemilik faktur.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * User yang menerbitkan faktur.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Seluruh penugasan inkaso yang pernah dibuat untuk faktur ini.
     */
    public function penugasanInkaso(): HasMany
    {
        return $this->hasMany(PenugasanInkaso::class, 'faktur_id');
    }

    /**
     * Penugasan inkaso yang saat ini aktif/berjalan.
     */
    public function penugasanAktif()
    {
        return $this->hasOne(PenugasanInkaso::class, 'faktur_id')
                    ->where('status_tugas', 'berjalan')
                    ->latestOfMany();
    }

    /**
     * Seluruh riwayat pembayaran faktur (DP, pelunasan langsung, maupun setoran inkaso).
     */
    public function pembayaran(): HasMany
    {
        return $this->hasMany(PembayaranFaktur::class, 'faktur_id');
    }

    /**
     * Seluruh penerimaan kas masuk kasir untuk faktur ini
     */
    public function kasMasuks(): HasMany
    {
        return $this->hasMany(KasMasuk::class, 'ref_id');
    }

    /**
     * Sinkronisasi ulang status faktur berdasarkan saldo terbayar dan sisa piutang.
     */
    public function sinkronkanStatus(): void
    {
        $totalBayar = (float) $this->pembayaran()->sum('nominal_bayar');
        $this->total_terbayar = $totalBayar;
        $this->sisa_piutang = max(0, (float) $this->total_tagihan - $totalBayar);

        if ($this->sisa_piutang <= 0) {
            $this->status = 'lunas';
            $this->penugasanInkaso()->where('status_tugas', 'berjalan')->update(['status_tugas' => 'selesai']);
        } elseif ($totalBayar > 0) {
            $this->status = 'cicilan';
        } else {
            $this->status = 'belum_bayar';
        }

        $this->save();
    }

    /**
     * Relasi ke transaksi Iklan Koran jika faktur ini berasal dari divisi Koran.
     */
    public function getTransaksiKoranAttribute()
    {
        return TransaksiKoran::with('iklankoran')->where('nofakturkoran', $this->no_faktur)->first();
    }

    /**
     * Relasi ke transaksi Iklan Online jika faktur ini berasal dari divisi Online.
     */
    public function getTransaksiOnlineAttribute()
    {
        return TransaksiOnline::with('iklanonline')->where('nofakturonline', $this->no_faktur)->first();
    }

    /**
     * Relasi ke transaksi Iklan Priangan TV jika faktur ini berasal dari divisi Priangan TV.
     */
    public function getTransaksiPrianganAttribute()
    {
        return TransaksiPriangan::with('iklanpriangan')->where('nofakturpriangan', $this->no_faktur)->first();
    }
}
