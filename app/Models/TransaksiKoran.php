<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiKoran extends Model
{
    use HasFactory;

    protected $table = 'transaksikoran';

    protected $fillable = [
        'nofakturkoran',
        'identitas',
        'no_order',
        'tanggal_transaksikoran',
        'nama_pemasangkoran',
        'alamat_pemasangkoran',
        'kota',
        'id_iklankoran',
        'halaman_iklan',
        'warna_iklan',
        'kop_iklan',
        'merk',
        'materi_iklan',
        'ukuran_iklan',
        'tanggal_muatkoran',
        'sales_iklankoran',
        'total_muatkoran',
        'harga_transaksikoran',
        'brutto_transaksikoran',
        'diskon_persen',
        'diskon_transaksikoran',
        'netto_transaksikoran',
        'ppn_transaksikoran',
        'materai_transaksikoran',
        'insentif_transaksikoran',
        'komisi_transaksikoran',
        'totaltagihan_transaksikoran',
        'jumlahbayar_transaksikoran',
        'piutang_transaksikoran',
        'siap_cair_kh_insentif',
    ];

    protected $casts = [
        'diskon_persen'         => 'decimal:2',
        'siap_cair_kh_insentif' => 'boolean',
    ];

    public function kasMasuks()
    {
        return $this->hasMany(KasMasuk::class, 'ref_id')->where('tipe_transaksi', 'iklan');
    }

    public function iklankoran()
    {
        return $this->belongsTo(JenisIklan::class, 'id_iklankoran');
    }

    /**
     * Generate No. Order format: YYYYMM + 4-digit sequence (e.g. 2026100143)
     */
    public static function generateNoOrder($date = null)
    {
        $d = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::now();
        $prefix = $d->format('Ym'); // e.g. 202610

        $latestOrder = self::where('no_order', 'like', "{$prefix}%")
            ->orderBy('no_order', 'desc')
            ->value('no_order');

        if ($latestOrder && strlen($latestOrder) >= 10) {
            $seq = intval(substr($latestOrder, 6)) + 1;
        } else {
            $countMonth = self::whereYear('tanggal_transaksikoran', $d->year)
                ->whereMonth('tanggal_transaksikoran', $d->month)
                ->count();
            $seq = $countMonth + 1;
        }

        return $prefix . sprintf("%04d", $seq);
    }

    /**
     * Generate No. Faktur format: 0143/10/2026
     */
    public static function generateNoFaktur($date = null)
    {
        $d = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::now();
        $month = $d->format('m');
        $year = $d->format('Y');

        $countMonth = self::whereYear('tanggal_transaksikoran', $d->year)
            ->whereMonth('tanggal_transaksikoran', $d->month)
            ->count();
        $seq = $countMonth + 1;

        return sprintf("%04d", $seq) . '/' . $month . '/' . $year;
    }

    /**
     * Generate Identitas COA format: 1.00.04.01.01.01.00496
     */
    public static function generateIdentitas()
    {
        $count = self::count() + 1;
        return '1.00.04.01.01.01.' . sprintf("%05d", $count);
    }

    public static function createCode()
    {
        return self::generateNoFaktur();
    }
}
