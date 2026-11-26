<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceIklan extends Model
{
    use HasFactory;

    protected $table = 'invoice_iklans';

    protected $fillable = [
        'no_invoice',
        'transaksi_koran_id',
        'transaksi_online_id',
        'kepada',
        'alamat',
        'attn',
        'tanggal_invoice',
        'deskripsi',
        'qty',
        'unit_price',
        'subtotal',
        'dpp',
        'ppn_persen',
        'ppn_nominal',
        'total_amount',
        'terbilang',
        'catatan_transfer',
        'kota_terbit',
        'nama_penandatangan',
        'jabatan_penandatangan',
        'status_pembayaran',
        'created_by',
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
        'qty'             => 'integer',
        'unit_price'      => 'decimal:2',
        'subtotal'        => 'decimal:2',
        'dpp'             => 'decimal:2',
        'ppn_persen'      => 'decimal:2',
        'ppn_nominal'     => 'decimal:2',
        'total_amount'    => 'decimal:2',
    ];

    public function transaksiKoran(): BelongsTo
    {
        return $this->belongsTo(TransaksiKoran::class, 'transaksi_koran_id');
    }

    public function transaksiOnline(): BelongsTo
    {
        return $this->belongsTo(TransaksiOnline::class, 'transaksi_online_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Konversi angka bulan ke format Romawi (contoh: 11 -> XI)
     */
    public static function bulanRomawi(int $bulan): string
    {
        $romawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $romawi[$bulan] ?? 'I';
    }

    /**
     * Generate Nomor Invoice otomatis sesuai format resmi:
     * Contoh: 006/KEU-HU.KP/XI/2026
     */
    public static function generateNoInvoice($date = null): string
    {
        $d = $date ? Carbon::parse($date) : Carbon::now();
        $romawi = self::bulanRomawi((int) $d->format('m'));
        $tahun = $d->format('Y');

        $prefixSuffix = "/KEU-HU.KP/{$romawi}/{$tahun}";

        // Hitung nomor urut berikutnya berdasarkan bulan dan tahun berjalan
        $count = self::whereYear('tanggal_invoice', $d->year)
            ->whereMonth('tanggal_invoice', $d->month)
            ->count();

        $seq = $count + 1;
        $noUrut = sprintf("%03d", $seq);

        // Pastikan tidak ada duplikasi nomor
        while (self::where('no_invoice', "{$noUrut}{$prefixSuffix}")->exists()) {
            $seq++;
            $noUrut = sprintf("%03d", $seq);
        }

        return "{$noUrut}{$prefixSuffix}";
    }

    /**
     * Helper Fungsi Terbilang Bahasa Indonesia
     */
    public static function terbilang($nilai): string
    {
        $nilai = abs((float) $nilai);
        $huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        $hasil = "";

        if ($nilai < 12) {
            $hasil = " " . $huruf[(int) $nilai];
        } else if ($nilai < 20) {
            $hasil = self::terbilang($nilai - 10) . " Belas";
        } else if ($nilai < 100) {
            $hasil = self::terbilang($nilai / 10) . " Puluh" . self::terbilang(fmod($nilai, 10));
        } else if ($nilai < 200) {
            $hasil = " Seratus" . self::terbilang($nilai - 100);
        } else if ($nilai < 1000) {
            $hasil = self::terbilang($nilai / 100) . " Ratus" . self::terbilang(fmod($nilai, 100));
        } else if ($nilai < 2000) {
            $hasil = " Seribu" . self::terbilang($nilai - 1000);
        } else if ($nilai < 1000000) {
            $hasil = self::terbilang($nilai / 1000) . " Ribu" . self::terbilang(fmod($nilai, 1000));
        } else if ($nilai < 1000000000) {
            $hasil = self::terbilang($nilai / 1000000) . " Juta" . self::terbilang(fmod($nilai, 1000000));
        } else if ($nilai < 1000000000000) {
            $hasil = self::terbilang($nilai / 1000000000) . " Milyar" . self::terbilang(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $hasil = self::terbilang($nilai / 1000000000000) . " Triliun" . self::terbilang(fmod($nilai, 1000000000000));
        }

        return trim(preg_replace('/\s+/', ' ', $hasil)) . " Rupiah";
    }

    /**
     * Teks default instruksi pembayaran transfer bank
     */
    public static function defaultCatatanTransfer(): string
    {
        return "BANK BJB CABANG TASIKMALAYA\nNorek : 005.001.00.62709 an.PT. BERKAH PIKIRAN RAKYAT\nBANK BCA CABANG TASIKMALAYA\nNorek : 0540844500 an.PT.BERKAH PIKIRAN RAKYAT\n\nContact person: 085321823093\nBukti Transfer Mohon di email: kabarpriangan1999@gmail.com";
    }
}
