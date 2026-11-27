<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPembayaran extends Model
{
    use HasFactory;

    protected $table = 'vendor_pembayarans';

    protected $fillable = [
        'vendor_tagihan_id',
        'no_bukti_bayar',
        'tanggal_bayar',
        'metode_bayar',
        'kode_perkiraan_kas_bank',
        'nominal_bayar',
        'potongan_pph',
        'keterangan',
        'jurnal_bayar_id',
        'user_id',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'nominal_bayar' => 'decimal:2',
        'potongan_pph'  => 'decimal:2',
    ];

    public function tagihan()
    {
        return $this->belongsTo(VendorTagihan::class, 'vendor_tagihan_id');
    }

    public function jurnalBayar()
    {
        return $this->belongsTo(JurnalUmum::class, 'jurnal_bayar_id');
    }

    public static function generateNoBukti(?string $tanggal = null): string
    {
        $d = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
        $prefix = 'PAY-AP-' . $d->format('Ym') . '-';
        $last = self::where('no_bukti_bayar', 'like', $prefix . '%')->orderByDesc('id')->first();
        $seq = 1;
        if ($last && preg_match('/(\d{4})$/', $last->no_bukti_bayar, $m)) {
            $seq = ((int) $m[1]) + 1;
        }
        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
