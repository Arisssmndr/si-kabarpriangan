<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorTagihan extends Model
{
    use HasFactory;

    protected $table = 'vendor_tagihans';

    protected $fillable = [
        'no_tagihan',
        'no_invoice_vendor',
        'nama_vendor',
        'kategori_beban',
        'tanggal_tagihan',
        'jatuh_tempo',
        'kode_perkiraan_beban',
        'kode_perkiraan_utang',
        'dpp',
        'ppn',
        'pph23',
        'total_tagihan',
        'total_terbayar',
        'sisa_utang',
        'status',
        'keterangan',
        'jurnal_akui_id',
        'user_id',
    ];

    protected $casts = [
        'tanggal_tagihan' => 'date',
        'jatuh_tempo'     => 'date',
        'dpp'             => 'decimal:2',
        'ppn'             => 'decimal:2',
        'pph23'           => 'decimal:2',
        'total_tagihan'   => 'decimal:2',
        'total_terbayar'  => 'decimal:2',
        'sisa_utang'      => 'decimal:2',
    ];

    public function pembayarans()
    {
        return $this->hasMany(VendorPembayaran::class, 'vendor_tagihan_id')->latest('tanggal_bayar');
    }

    public function jurnalAkui()
    {
        return $this->belongsTo(JurnalUmum::class, 'jurnal_akui_id');
    }

    public static function generateNoTagihan(?string $tanggal = null): string
    {
        $d = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
        $prefix = 'AP-' . $d->format('Ym') . '-';
        $last = self::where('no_tagihan', 'like', $prefix . '%')->orderByDesc('id')->first();
        $seq = 1;
        if ($last && preg_match('/(\d{4})$/', $last->no_tagihan, $m)) {
            $seq = ((int) $m[1]) + 1;
        }
        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
