<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KoreksiPiutang extends Model
{
    use HasFactory;

    protected $table = 'koreksi_piutang';

    protected $fillable = [
        'no_bukti',
        'jenis',
        'tanggal_koreksi',
        'faktur_id',
        'no_faktur',
        'no_order',
        'kode_perkiraan',
        'nama_entitas',
        'alamat',
        'nilai_penjualan',
        'jumlah_koreksi',
        'saldo_setelah_koreksi',
        'eksemplar_langganan',
        'eksemplar_eceran',
        'harga_koran',
        'keterangan',
        'jurnal_umum_id',
        'user_id',
    ];

    protected $casts = [
        'tanggal_koreksi'       => 'date',
        'nilai_penjualan'       => 'decimal:2',
        'jumlah_koreksi'        => 'decimal:2',
        'saldo_setelah_koreksi' => 'decimal:2',
        'eksemplar_langganan'   => 'integer',
        'eksemplar_eceran'      => 'integer',
        'harga_koran'           => 'decimal:2',
    ];

    public function jurnal()
    {
        return $this->belongsTo(JurnalUmum::class, 'jurnal_umum_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Compatibility accessors
    public function getNomorBuktiAttribute(): ?string
    {
        return $this->no_bukti;
    }

    public function getNoReferensiAttribute(): ?string
    {
        return $this->no_faktur;
    }

    public function getTanggalAttribute(): ?string
    {
        return $this->tanggal_koreksi?->format('Y-m-d');
    }

    public function getNominalKoreksiAttribute(): float
    {
        return (float) ($this->jumlah_koreksi ?? 0);
    }

    public function getAlasanAttribute(): ?string
    {
        return $this->keterangan;
    }

    public function getKodeAkunDebetAttribute(): string
    {
        return ($this->jenis === 'sirkulasi') ? '4.00.05.01.02.00001' : '4.00.05.01.01.00001';
    }

    public function getKodeAkunKreditAttribute(): string
    {
        return $this->kode_perkiraan ?: (($this->jenis === 'sirkulasi') ? '1.00.04.01.01.00.00002' : '1.00.04.01.01.01.00001');
    }

    public function fakturIklan()
    {
        return $this->belongsTo(TransaksiKoran::class, 'faktur_id');
    }

    public function fakturKoran()
    {
        return $this->belongsTo(TransaksiKoran::class, 'faktur_id');
    }

    /**
     * Generate No Bukti Urut: CN-IKL-YYYYXXXX atau CN-SIR-YYYYXXXX
     */
    public static function generateNoBukti(string $jenis, ?string $date = null): string
    {
        $d = $date ? \Carbon\Carbon::parse($date) : now();
        $year = $d->format('Y');
        $tag = ($jenis === 'sirkulasi') ? 'SIR' : 'IKL';
        $prefix = "CN-{$tag}-{$year}";

        $last = self::where('no_bukti', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $seq = 1;
        if ($last && preg_match('/(\d{4})$/', $last->no_bukti, $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}

