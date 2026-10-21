<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasKeluar extends Model
{
    use HasFactory;

    protected $table = 'kas_keluars';

    protected $fillable = [
        'no_bkk',
        'shift_id',
        'tanggal_keluar',
        'tgl_transaksi',
        'no_bp_ref',
        'no_register_anggaran',
        'kode_bagian',
        'kode_perkiraan',
        'nama_perkiraan',
        'diberikan_kepada',
        'nama_penerima',
        'kategori_beban',
        'kategori',
        'jumlah_keluar',
        'nominal_keluar',
        'metode_bayar',
        'is_tax_offset',
        'is_kasbon',
        'bp_id',
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
        'tanggal_keluar'        => 'date',
        'tgl_transaksi'         => 'date',
        'jumlah_keluar'         => 'decimal:2',
        'nominal_keluar'        => 'decimal:2',
        'is_tax_offset'         => 'boolean',
        'is_kasbon'             => 'boolean',
        'is_closed'             => 'boolean',
        'is_pulled_accounting'  => 'boolean',
        'status_tarik_akunting' => 'boolean',
        'void_at'               => 'datetime',
    ];

    /**
     * User kasir pencatat realisasi pengeluaran
     */
    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id')->withDefault(function ($user, $kasKeluar) {
            return User::find($kasKeluar->user_id);
        });
    }

    /**
     * Shift kasir saat pengeluaran dicairkan
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(ShiftKasir::class, 'shift_id');
    }

    /**
     * Bukti Pengeluaran (BP) yang dicairkan
     */
    public function buktiPengeluaran(): BelongsTo
    {
        return $this->belongsTo(BuktiPengeluaran::class, 'bp_id');
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            if (empty($model->nama_penerima) && !empty($model->diberikan_kepada)) {
                $model->nama_penerima = $model->diberikan_kepada;
            }
            if (empty($model->diberikan_kepada) && !empty($model->nama_penerima)) {
                $model->diberikan_kepada = $model->nama_penerima;
            }
            if (empty($model->nominal_keluar) && !empty($model->jumlah_keluar)) {
                $model->nominal_keluar = $model->jumlah_keluar;
            }
            if (empty($model->jumlah_keluar) && !empty($model->nominal_keluar)) {
                $model->jumlah_keluar = $model->nominal_keluar;
            }
            if (empty($model->tgl_transaksi) && !empty($model->tanggal_keluar)) {
                $model->tgl_transaksi = $model->tanggal_keluar;
            }
            if (empty($model->tanggal_keluar) && !empty($model->tgl_transaksi)) {
                $model->tanggal_keluar = $model->tgl_transaksi;
            }
            if (empty($model->user_id) && !empty($model->kasir_id)) {
                $model->user_id = $model->kasir_id;
            }
            if (empty($model->kasir_id) && !empty($model->user_id)) {
                $model->kasir_id = $model->user_id;
            }
            if (empty($model->kategori_beban) && !empty($model->kategori)) {
                $model->kategori_beban = $model->kategori;
            }
            if (empty($model->kategori) && !empty($model->kategori_beban)) {
                $model->kategori = match ($model->kategori_beban) {
                    'komisi_kh', 'insentif_ae', 'komisi_kh_insentif' => 'komisi_kh_insentif',
                    'bbm_armada', 'operasional_bbm' => 'operasional_bbm',
                    'redaksi' => 'redaksi',
                    default => 'umum',
                };
            }
        });
    }

    /**
     * Generator Nomor Bukti Kas Keluar (KO) Format DOS KAS.EXE
     * Format: KO-[TahunBulan][NoUrut]
     * Contoh: KO-2026100001 / KO-2026091551
     */
    public static function generateNoBkk(): string
    {
        $ym = Carbon::today()->format('Ym');
        $prefix = "KO-{$ym}";
        
        $latest = self::where(function ($q) use ($prefix) {
                $q->where('no_bkk', 'like', "{$prefix}%")
                  ->orWhere('no_bkk', 'like', "BKK-{$prefix}%");
            })
            ->orderBy('id', 'desc')
            ->value('no_bkk');

        $nextSeq = 1;
        if ($latest && str_starts_with($latest, $prefix)) {
            $lastSeq = substr($latest, strlen($prefix));
            $nextSeq = intval($lastSeq) + 1;
        }

        return $prefix . sprintf('%04d', $nextSeq);
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
            default => (!empty($this->kode_bagian) && !is_numeric($this->kode_bagian)) ? 'Divisi ' . $this->kode_bagian : 'Divisi Keuangan',
        };
    }
}

