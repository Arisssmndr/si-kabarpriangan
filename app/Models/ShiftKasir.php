<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShiftKasir extends Model
{
    use HasFactory;

    protected $table = 'shift_kasirs';

    protected $fillable = [
        'kasir_id',
        'tanggal_shift',
        'tgl_shift',
        'saldo_awal_fisik',
        'total_ki',
        'total_ko',
        'saldo_akhir_teoritis',
        'total_kasbon_gantung',
        'total_masuk',
        'total_keluar',
        'saldo_akhir_fisik',
        'rincian_pecahan',
        'selisih',
        'selisih_kas',
        'status',
        'closed_at',
        'catatan_serah_terima',
        'verified_by',
        'verified_at',
        'catatan_supervisor',
        'reopen_requested',
        'reopen_status',
        'reopen_reason',
        'berita_acara_reopen',
        'reopen_requested_by',
        'reopen_requested_at',
        'reopen_approved_by',
        'reopen_approved_at',
        'reopen_notes',
    ];

    protected $casts = [
        'tanggal_shift'       => 'date',
        'tgl_shift'           => 'date',
        'closed_at'           => 'datetime',
        'verified_at'         => 'datetime',
        'reopen_requested'    => 'boolean',
        'reopen_requested_at' => 'datetime',
        'reopen_approved_at'  => 'datetime',
        'saldo_awal_fisik'    => 'decimal:2',
        'total_ki'            => 'decimal:2',
        'total_ko'            => 'decimal:2',
        'saldo_akhir_teoritis'=> 'decimal:2',
        'total_kasbon_gantung'=> 'decimal:2',
        'total_masuk'         => 'decimal:2',
        'total_keluar'        => 'decimal:2',
        'saldo_akhir_fisik'   => 'decimal:2',
        'rincian_pecahan'     => 'array',
        'selisih'             => 'decimal:2',
        'selisih_kas'         => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->reopen_status === 'requested' && !$model->reopen_requested) {
                $model->reopen_requested = true;
            }
            if (empty($model->berita_acara_reopen) && !empty($model->reopen_reason)) {
                $model->berita_acara_reopen = $model->reopen_reason;
            }
            if (empty($model->reopen_reason) && !empty($model->berita_acara_reopen)) {
                $model->reopen_reason = $model->berita_acara_reopen;
            }
        });
    }

    /**
     * User kasir pemegang shift
     */
    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    /**
     * Transaksi Kas Masuk selama shift ini
     */
    public function kasMasuks(): HasMany
    {
        return $this->hasMany(KasMasuk::class, 'shift_id');
    }

    /**
     * Transaksi Kas Keluar selama shift ini
     */
    public function kasKeluars(): HasMany
    {
        return $this->hasMany(KasKeluar::class, 'shift_id');
    }

    /**
     * Transaksi Kasbon / Uang Muka Meja Kasir selama shift ini
     */
    public function kasbons(): HasMany
    {
        return $this->hasMany(Kasbon::class, 'shift_id');
    }

    /**
     * User yang mengajukan permohonan buka kembali kas
     */
    public function reopenRequester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reopen_requested_by');
    }

    /**
     * Manager Keuangan / Pimpinan yang menyetujui buka kembali kas
     */
    public function reopenApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reopen_approved_by');
    }

    /**
     * Daftar definisi pecahan uang resmi untuk form closing dan rincian riwayat.
     */
    public static function denominationMeta(): array
    {
        return [
            ['key' => '100k', 'label' => 'Rp 100.000', 'nominal' => 100000, 'unit' => 'lembar'],
            ['key' => '50k',  'label' => 'Rp 50.000',  'nominal' => 50000,  'unit' => 'lembar'],
            ['key' => '20k',  'label' => 'Rp 20.000',  'nominal' => 20000,  'unit' => 'lembar'],
            ['key' => '10k',  'label' => 'Rp 10.000',  'nominal' => 10000,  'unit' => 'lembar'],
            ['key' => '5k',   'label' => 'Rp 5.000',   'nominal' => 5000,   'unit' => 'lembar'],
            ['key' => '2k',   'label' => 'Rp 2.000',   'nominal' => 2000,   'unit' => 'lembar'],
            ['key' => '1k',   'label' => 'Rp 1.000',   'nominal' => 1000,   'unit' => 'lembar'],
            ['key' => 'koin', 'label' => 'Koin Logam', 'nominal' => 1,      'unit' => 'nominal'],
        ];
    }

    /**
     * Susun payload terstruktur riwayat closing (ringkasan kas & rincian lembar pecahan).
     */
    public function toClosingDetailArray(): array
    {
        $rawDate = $this->tanggal_shift ?? $this->tgl_shift ?? $this->created_at;
        $tanggalIso = $rawDate ? \Carbon\Carbon::parse($rawDate)->toDateString() : '-';
        $tanggalFormatted = $rawDate ? \Carbon\Carbon::parse($rawDate)->translatedFormat('d F Y') : '-';
        $tanggalShort = $rawDate ? \Carbon\Carbon::parse($rawDate)->format('d/m/Y') : '-';

        $stored = is_array($this->rincian_pecahan) ? $this->rincian_pecahan : null;
        $hasRincian = $stored !== null;

        $pecahanRows = [];
        $totalLembar = 0;

        foreach (self::denominationMeta() as $meta) {
            $key = $meta['key'];
            $qty = $stored ? (float) ($stored[$key] ?? 0) : 0;
            $subtotal = $key === 'koin' ? $qty : ($qty * $meta['nominal']);

            if ($key !== 'koin') {
                $totalLembar += (int) $qty;
            }

            $pecahanRows[] = [
                'key'      => $key,
                'label'    => $meta['label'],
                'nominal'  => $meta['nominal'],
                'unit'     => $meta['unit'],
                'qty'      => $key === 'koin' ? $qty : (int) $qty,
                'subtotal' => (float) $subtotal,
            ];
        }

        $totalKi = (float) ($this->total_ki ?: $this->total_masuk);
        $totalKo = (float) ($this->total_ko ?: $this->total_keluar);
        $saldoAwal = (float) $this->saldo_awal_fisik;
        $saldoTeoritis = (float) ($this->saldo_akhir_teoritis != 0 ? $this->saldo_akhir_teoritis : ($saldoAwal + $totalKi - $totalKo));
        $saldoFisik = (float) $this->saldo_akhir_fisik;
        $selisih = (float) ($this->selisih ?? $this->selisih_kas ?? ($saldoFisik - $saldoTeoritis));

        return [
            'id'                  => $this->id,
            'tanggal'             => $tanggalFormatted,
            'tanggal_short'       => $tanggalShort,
            'tanggal_iso'         => $tanggalIso,
            'kasir_name'          => $this->kasir->name ?? 'Kasir',
            'saldo_awal'          => $saldoAwal,
            'total_ki'            => $totalKi,
            'total_ko'            => $totalKo,
            'saldo_teoritis'      => $saldoTeoritis,
            'saldo_fisik'         => $saldoFisik,
            'selisih'             => $selisih,
            'waktu_tutup'         => $this->closed_at ? \Carbon\Carbon::parse($this->closed_at)->format('d/m/Y H:i') : ($this->updated_at ? $this->updated_at->format('d/m/Y H:i') : '-'),
            'catatan'             => $this->catatan_serah_terima ?: '-',
            'has_rincian_pecahan' => $hasRincian,
            'total_lembar'        => $totalLembar,
            'pecahan'             => $pecahanRows,
        ];
    }

    /**
     * Hitung saldo akhir kas menurut sistem
     */
    public function getSaldoSistemAttribute(): float
    {
        return (float) ($this->saldo_awal_fisik + $this->total_masuk - $this->total_keluar);
    }
}
