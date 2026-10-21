<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasMasuk extends Model
{
    use HasFactory;

    protected $table = 'kas_masuks';

    protected $fillable = [
        'no_kuitansi',
        'no_bkm',
        'shift_id',
        'tanggal_terima',
        'tgl_transaksi',
        'no_blanko',
        'kode_bagian',
        'kode_perkiraan',
        'nama_perkiraan',
        'terima_dari',
        'nama_pembayar',
        'penagih_inkaso',
        'no_faktur_ref',
        'kode_agen_ref',
        'kode_agen_input',
        'no_bapr_ref',
        'tipe_transaksi',
        'ref_id',
        'ref_type',
        'metode_bayar',
        'bank_tujuan',
        'no_referensi_transfer',
        'bukti_transfer_path',
        'jumlah_tagihan',
        'total_tagihan',
        'nilai_dpp',
        'potongan_ppn',
        'potongan_pph23',
        'is_instansi_tax',
        'potongan_retur',
        'potongan_pajak',
        'no_bapr',
        'jumlah_retur_eks',
        'jumlah_diterima',
        'jumlah_dibayar',
        'sisa_piutang',
        'status_bayar',
        'status_pembayaran',
        'keterangan',
        'kasir_id',
        'user_id',
        'is_closed',
        'is_pulled_accounting',
        'status_tarik_akunting',
        'status_void',
        'void_by',
        'void_at',
        'alasan_void',
    ];

    protected $casts = [
        'tanggal_terima'        => 'date',
        'tgl_transaksi'         => 'date',
        'total_tagihan'         => 'decimal:2',
        'jumlah_tagihan'        => 'decimal:2',
        'nilai_dpp'             => 'decimal:2',
        'potongan_ppn'          => 'decimal:2',
        'potongan_pph23'        => 'decimal:2',
        'is_instansi_tax'       => 'boolean',
        'potongan_pajak'        => 'decimal:2',
        'potongan_retur'        => 'decimal:2',
        'jumlah_retur_eks'      => 'integer',
        'jumlah_dibayar'        => 'decimal:2',
        'jumlah_diterima'       => 'decimal:2',
        'sisa_piutang'          => 'decimal:2',
        'is_closed'             => 'boolean',
        'is_pulled_accounting'  => 'boolean',
        'status_tarik_akunting' => 'boolean',
        'void_at'               => 'datetime',
    ];

    /**
     * User kasir pencatat transaksi
     */
    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id')->withDefault(function ($user, $kasMasuk) {
            return User::find($kasMasuk->user_id);
        });
    }

    /**
     * Shift kasir saat transaksi terjadi
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(ShiftKasir::class, 'shift_id');
    }

    /**
     * Relasi ke Faktur (jika tipe iklan atau tempo)
     */
    public function faktur(): BelongsTo
    {
        return $this->belongsTo(Faktur::class, 'ref_id');
    }

    /**
     * Relasi ke TransaksiKoran (jika ref_type koran)
     */
    public function transaksiKoran(): BelongsTo
    {
        return $this->belongsTo(TransaksiKoran::class, 'ref_id');
    }

    /**
     * Relasi ke Agen Sirkulasi (jika tipe sirkulasi)
     */
    public function agen(): BelongsTo
    {
        return $this->belongsTo(Agen::class, 'ref_id');
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            if (empty($model->nama_pembayar) && !empty($model->terima_dari)) {
                $model->nama_pembayar = $model->terima_dari;
            }
            if (empty($model->terima_dari) && !empty($model->nama_pembayar)) {
                $model->terima_dari = $model->nama_pembayar;
            }
            if (empty($model->no_bkm) && !empty($model->no_kuitansi)) {
                $model->no_bkm = $model->no_kuitansi;
            }
            if (empty($model->no_kuitansi) && !empty($model->no_bkm)) {
                $model->no_kuitansi = $model->no_bkm;
            }
            if (empty($model->tgl_transaksi) && !empty($model->tanggal_terima)) {
                $model->tgl_transaksi = $model->tanggal_terima;
            }
            if (empty($model->tanggal_terima) && !empty($model->tgl_transaksi)) {
                $model->tanggal_terima = $model->tgl_transaksi;
            }
            if (empty($model->jumlah_dibayar) && !empty($model->jumlah_diterima)) {
                $model->jumlah_dibayar = $model->jumlah_diterima;
            }
            if (empty($model->jumlah_diterima) && !empty($model->jumlah_dibayar)) {
                $model->jumlah_diterima = $model->jumlah_dibayar;
            }
            if (empty($model->user_id) && !empty($model->kasir_id)) {
                $model->user_id = $model->kasir_id;
            }
            if (empty($model->kasir_id) && !empty($model->user_id)) {
                $model->kasir_id = $model->user_id;
            }
        });
    }

    /**
     * Generator Nomor Kuitansi Kas Masuk (KI) Format DOS KAS.EXE
     * Format: KI-[KodeBagian]-[TahunBulan][NoUrut]
     * Contoh: KI-01-2026100001 / KI-02-2026100001 / KI-14-2026100001
     */
    public static function generateNoKuitansi(string $kodeBagian = '01'): string
    {
        $ym = Carbon::today()->format('Ym');
        $prefix = "KI-{$kodeBagian}-{$ym}";

        $latest = self::where(function ($q) use ($prefix) {
                $q->where('no_kuitansi', 'like', "{$prefix}%")
                  ->orWhere('no_bkm', 'like', "{$prefix}%");
            })
            ->orderBy('id', 'desc')
            ->value('no_kuitansi');

        $nextSeq = 1;
        if ($latest && str_starts_with($latest, $prefix)) {
            $lastSeq = substr($latest, strlen($prefix));
            $nextSeq = intval($lastSeq) + 1;
        }

        return $prefix . sprintf('%04d', $nextSeq);
    }

    /**
     * Generator Nomor Bukti Kas Masuk (BKM) - Fallback
     */
    public static function generateNoBkm(string $kodeBagian = '01'): string
    {
        return self::generateNoKuitansi($kodeBagian);
    }

    /**
     * Get nama divisi resmi (tanpa format angka / kode bagian)
     */
    public function getNamaDivisiAttribute(): string
    {
        return match ($this->kode_bagian) {
            '01' => 'Divisi Iklan',
            '02' => 'Divisi Sirkulasi',
            '03' => 'Divisi Umum',
            '04' => 'Divisi Akuntansi',
            '05' => 'Divisi Keuangan',
            '06' => 'Divisi Redaksi',
            '07' => 'Divisi Pimpinan',
            '09' => 'Divisi Kasir',
            '14' => 'Divisi Sirkulasi',
            default => (!empty($this->kode_bagian) && !is_numeric($this->kode_bagian)) ? 'Divisi ' . $this->kode_bagian : 'Divisi Iklan',
        };
    }
}

